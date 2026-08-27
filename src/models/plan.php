<?php

class Plan {
    private $pdo;
    private $lastError = '';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getLastError(): string {
        return $this->lastError;
    }

    /**
     * Obtener todos los planes disponibles en la base de datos
     */
    public function getAllPlans(): array {
        try {
            $stmt = $this->pdo->query("SELECT * FROM plan_oficina ORDER BY ID_Plan ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    /**
     * Asegurar que el Plan ID 4 (En Revision) exista en la base de datos
     */
    public function ensurePlanReviewExists(): void {
        try {
            $stmt = $this->pdo->prepare("SELECT ID_Plan FROM plan_oficina WHERE ID_Plan = 4");
            $stmt->execute();
            if (!$stmt->fetch()) {
                $stmtIns = $this->pdo->prepare("
                    INSERT INTO plan_oficina (ID_Plan, Nombre_Plan, Descripcion, Cant_Estaciones)
                    VALUES (4, 'Plan Custom (En Revision)', 'Solicitud de infraestructura personalizada en proceso de evaluacion tecnica.', 0)
                ");
                $stmtIns->execute();
            }
        } catch (Exception $e) {
            // Manejar silenciosamente o asignar a lastError
        }
    }

    /**
     * Obtener planes contratables estandar (excluyendo 'NO PLAN' ID=0 y 'EN REVISION' ID=4)
     */
    public function getContractablePlans(): array {
        try {
            $this->ensurePlanReviewExists();
            $stmt = $this->pdo->query("SELECT * FROM plan_oficina WHERE ID_Plan > 0 AND ID_Plan != 4 ORDER BY ID_Plan ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    /**
     * Obtener un plan específico por ID
     */
    public function getPlanById(int $planId): ?array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM plan_oficina WHERE ID_Plan = ?");
            $stmt->execute([$planId]);
            $plan = $stmt->fetch(PDO::FETCH_ASSOC);
            return $plan ?: null;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return null;
        }
    }

    /**
     * Verificar si un usuario es de tipo Empresa
     */
    public function isUserEmpresa(int $userId): bool {
        try {
            $stmt = $this->pdo->prepare("SELECT Tipo_Persona FROM usuario WHERE ID_Usuario = ?");
            $stmt->execute([$userId]);
            $tipo = $stmt->fetchColumn();
            return strtolower((string)$tipo) === 'empresa';
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * Obtener el plan actual contratado por el usuario
     */
    public function getUserPlan(int $userId): ?array {
        try {
            // 1. Intentar obtener desde usuario_plan
            $stmt = $this->pdo->prepare("
                SELECT p.*, up.Fecha_Contratacion
                FROM plan_oficina p
                JOIN usuario_plan up ON p.ID_Plan = up.ID_Plan
                WHERE up.ID_Usuario = ?
            ");
            $stmt->execute([$userId]);
            $plan = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($plan) {
                return $plan;
            }

            // 2. Si no está en usuario_plan, intentar desde empresa
            $stmtEmp = $this->pdo->prepare("
                SELECT p.*, NULL as Fecha_Contratacion
                FROM plan_oficina p
                JOIN empresa e ON p.ID_Plan = e.ID_Plan
                WHERE e.ID_USUARIO = ?
            ");
            $stmtEmp->execute([$userId]);
            $planEmp = $stmtEmp->fetch(PDO::FETCH_ASSOC);
            if ($planEmp) {
                return $planEmp;
            }

            // 3. Retornar plan por defecto (ID_Plan = 0 - NO PLAN)
            return $this->getDefaultPlan();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return $this->getDefaultPlan();
        }
    }

    /**
     * Obtener el plan por defecto (Sin plan / ID = 0)
     */
    public function getDefaultPlan(): array {
        try {
            $stmtDefault = $this->pdo->prepare("SELECT * FROM plan_oficina WHERE ID_Plan = 0");
            $stmtDefault->execute();
            $default = $stmtDefault->fetch(PDO::FETCH_ASSOC);
            if ($default) {
                return $default;
            }
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
        }
        return [
            'ID_Plan' => 0,
            'Nombre_Plan' => 'Sin Plan',
            'Descripcion' => 'Sin plan contratado.',
            'Cant_Estaciones' => null,
            'Fecha_Contratacion' => null
        ];
    }

    /**
     * Asignar / Cambiar el plan de un usuario empresa
     */
    public function assignPlan(int $userId, int $planId): bool {
        $this->lastError = '';

        if (!$this->isUserEmpresa($userId)) {
            $this->lastError = "Solo los usuarios de tipo Empresa pueden contratar o cambiar planes de infraestructura.";
            return false;
        }

        try {
            $this->ensurePlanReviewExists();
            $this->pdo->beginTransaction();

            // Verificar existencia del plan
            $stmtCheck = $this->pdo->prepare("SELECT ID_Plan, Nombre_Plan FROM plan_oficina WHERE ID_Plan = ?");
            $stmtCheck->execute([$planId]);
            $planExiste = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            if (!$planExiste) {
                throw new Exception("El plan seleccionado no existe.");
            }

            // Actualizar o insertar en USUARIO_PLAN
            $stmtExist = $this->pdo->prepare("SELECT ID_Usuario FROM usuario_plan WHERE ID_Usuario = ?");
            $stmtExist->execute([$userId]);
            if ($stmtExist->fetch()) {
                $stmtUpd = $this->pdo->prepare("UPDATE usuario_plan SET ID_Plan = ?, Fecha_Contratacion = NOW() WHERE ID_Usuario = ?");
                $stmtUpd->execute([$planId, $userId]);
            } else {
                $stmtIns = $this->pdo->prepare("INSERT INTO usuario_plan (ID_Usuario, ID_Plan, Fecha_Contratacion) VALUES (?, ?, NOW())");
                $stmtIns->execute([$userId, $planId]);
            }

            // Actualizar en tabla EMPRESA si existe el registro
            $stmtEmp = $this->pdo->prepare("UPDATE empresa SET ID_Plan = ? WHERE ID_USUARIO = ?");
            $stmtEmp->execute([$planId, $userId]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * Cancelar o dar de baja el plan (asignar plan 0 - NO PLAN)
     */
    public function cancelPlan(int $userId): bool {
        return $this->assignPlan($userId, 0);
    }

    /**
     * Guardar una solicitud de cotización personalizada con datos estructurados.
     * Inserta en la tabla solicitud_cotizacion (una fila por componente/cantidad).
     */
    public function createSolicitudCotizacion(
        int     $userId,
        string  $descripcion,
        int     $cant_pc,
        int     $cant_impresoras,
        int     $cant_puntos_red,
        int     $cant_servidores,
        int     $cant_wifi,
        ?string $notas_extra = null
    ): bool {
        $this->lastError = '';
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO solicitud_cotizacion
                    (ID_Usuario, Descripcion, Cant_PC, Cant_Impresoras, Cant_Puntos_Red,
                     Cant_Servidores, Cant_Wifi, Notas_Extra)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $userId,
                $descripcion,
                $cant_pc,
                $cant_impresoras,
                $cant_puntos_red,
                $cant_servidores,
                $cant_wifi,
                $notas_extra,
            ]);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * Verificar si un plan es custom (ID >= 5)
     */
    public function isPlanCustom(int $planId): bool {
        return $planId >= 5;
    }

    /**
     * Obtener el detalle de un plan custom desde plan_custom_detalle.
     * Solo aplica para planes con ID >= 5.
     */
    public function getCustomPlanDetail(int $planId): ?array {
        if (!$this->isPlanCustom($planId)) {
            return null;
        }
        try {
            $stmt = $this->pdo->prepare("
                SELECT pcd.*,
                       u.Nombre AS Nombre_Empleado,
                       u.Email  AS Email_Empleado,
                       sc.Descripcion      AS Sol_Descripcion,
                       sc.Notas_Extra      AS Sol_Notas_Extra,
                       sc.Fecha_Solicitud  AS Sol_Fecha
                FROM plan_custom_detalle pcd
                LEFT JOIN usuario              u  ON u.ID_Usuario    = pcd.Creado_Por
                LEFT JOIN solicitud_cotizacion sc ON sc.ID_Solicitud = pcd.ID_Solicitud
                WHERE pcd.ID_Plan = ?
            ");
            $stmt->execute([$planId]);
            $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
            return $detalle ?: null;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return null;
        }
    }

    /**
     * Obtener todos los planes custom (ID >= 5) con su detalle y empresa asignada.
     */
    public function getAllCustomPlans(): array {
        try {
            $stmt = $this->pdo->query("
                SELECT p.*,
                       pcd.Total_Final,
                       pcd.Cant_PC, pcd.Cant_Impresoras, pcd.Cant_Puntos_Red,
                       pcd.Cant_Servidores, pcd.Cant_Wifi,
                       pcd.Notas_Empleado, pcd.Fecha_Creacion,
                       e.Nombre_Empresa,
                       u_emp.Email AS Email_Empresa,
                       u_cre.Nombre AS Nombre_Empleado
                FROM plan_oficina p
                LEFT JOIN plan_custom_detalle pcd ON pcd.ID_Plan       = p.ID_Plan
                LEFT JOIN usuario_plan        up  ON up.ID_Plan        = p.ID_Plan
                LEFT JOIN empresa             e   ON e.ID_Usuario      = up.ID_Usuario
                LEFT JOIN usuario             u_emp ON u_emp.ID_Usuario = up.ID_Usuario
                LEFT JOIN usuario             u_cre ON u_cre.ID_Usuario = pcd.Creado_Por
                WHERE p.ID_Plan >= 5
                ORDER BY p.ID_Plan DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    /**
     * Obtener el plan actual del usuario incluyendo detalle custom si aplica.
     */
    public function getUserPlanWithDetail(int $userId): ?array {
        $plan = $this->getUserPlan($userId);
        if ($plan && $this->isPlanCustom((int)($plan['ID_Plan'] ?? 0))) {
            $detalle = $this->getCustomPlanDetail((int)$plan['ID_Plan']);
            if ($detalle) {
                $plan['custom_detalle'] = $detalle;
            }
        }
        return $plan;
    }
}
