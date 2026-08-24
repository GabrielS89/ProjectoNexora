<?php

session_start();

class User {
    private $pdo;
    private $lastError = '';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getLastError(): string {
        return $this->lastError;
    }

    

    public function registerEmpresa($data) {
    $this->lastError = '';
    try {
        $this->pdo->beginTransaction();

        // 1) Insertar en USUARIO
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $sqlUser = "INSERT INTO USUARIO (Email, Contrasena, Nombre, Telefono, Tipo_Persona, ID_Rol)
                    VALUES (?, ?, ?, ?, 'Empresa', 1)";
        $stmtUser = $this->pdo->prepare($sqlUser);
        $stmtUser->execute([
            $data['email'],
            $hash,
            $data['nombre_empresa'],
            $data['telefono']
        ]);
        $idUsuario = $this->pdo->lastInsertId();

        // 2) Insertar en EMPRESA
        $sqlEmp = "INSERT INTO EMPRESA (ID_Usuario, ID_Representante, ID_Plan, RUT, Nombre_Empresa, Calle, Esquina, Numero_Puerta, Descripcion, Categoria_Empresarial, Tamano)
                    VALUES (?, NULL, 0, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtEmp = $this->pdo->prepare($sqlEmp);
        $stmtEmp->execute([
            $idUsuario,
            $data['rut'],
            $data['nombre_empresa'],
            $data['calle'],
            $data['esquina'],
            $data['num-puerta'],
            $data['descripcion'],
            $data['categoria'],
            $data['tamano']
        ]);
        $idEmpresa = $this->pdo->lastInsertId();

        // 3) Insertar REPRESENTANTE
        $sqlRep = "INSERT INTO REPRESENTANTE (Nombre_Completo, Cargo, Email_Contacto, Telefono_Contacto)
                    VALUES (?, ?, ?, ?)";
        $stmtRep = $this->pdo->prepare($sqlRep);
        $stmtRep->execute([
            $data['rep_nombre'],
            $data['rep_cargo'],
            $data['rep_email'],
            $data['rep_telefono']
        ]);
        $idRepresentante = $this->pdo->lastInsertId();

        // 4) Actualizar EMPRESA con el representante
        $stmtUpdateEmp = $this->pdo->prepare("UPDATE EMPRESA SET ID_Representante = ? WHERE ID_Empresa = ?");
        $stmtUpdateEmp->execute([$idRepresentante, $idEmpresa]);

        // 5) Insertar relación en PLAN_USUARIO (si corresponde)
        $sqlPlan = "INSERT INTO USUARIO_PLAN (ID_Usuario, ID_Plan, Fecha_Contratacion)
                    VALUES (?, ?, NOW())";
        $stmtPlan = $this->pdo->prepare($sqlPlan);
        $stmtPlan->execute([$idUsuario, 0]); // por defecto ID_Plan = 0 (NO PLAN)

        $this->pdo->commit();
        return true;
    } catch (Exception $e) {
        $this->pdo->rollBack();
        $this->lastError = 'ERROR REGISTRO EMPRESA: ' . $e->getMessage();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['error_message'] = $this->lastError;
        return false;
    }

}
    
    // Login

    public function login($email, $password) {
    try {
    
        $sql = "SELECT ID_Usuario, Email, Contrasena, Tipo_Persona, ID_Rol FROM USUARIO WHERE Email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verificar la contraseña
        if ($user && password_verify($password, $user['Contrasena'])) {
            
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            
            $_SESSION['user_id'] = $user['ID_Usuario'];
            $_SESSION['email'] = $user['Email'];
            $_SESSION['tipo_usuario'] = $user['Tipo_Persona'];
            
            // Definir Tipo de Usuario (Admin o User)
            $_SESSION['usuario_rol'] = $user['ID_Rol'];
            
            return true;
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}

    // Insertar en la tabla USUARIO

    public function registerUnipersonal($data) {
        $this->lastError = '';
        try {
            
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $sqlUser = "INSERT INTO USUARIO (Email, Nombre, Telefono, Contrasena, ID_Rol, Tipo_Persona)
                        VALUES (?, ?, ?, ?, 1, 'Persona')";
            $stmt = $this->pdo->prepare($sqlUser);
            $stmt->execute([$data['email'], $data['nombre'], $data['telefono'], $hash]);
            
            $idUsuario = $this->pdo->lastInsertId();

     // Insertar en la tabla Persona

            $sqlUni = "INSERT INTO persona (ID_Usuario) VALUES (?)";
            $stmtUni = $this->pdo->prepare($sqlUni);
            $stmtUni->execute([$idUsuario]);

            return true;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
            }
        }
    }




    
?>



