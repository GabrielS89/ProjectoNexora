-- ============================================================
-- MIGRATION: Sistema de Planes Custom
-- Ejecutar sobre la base de datos nexorabd
-- ============================================================

USE nexorabd;

-- ------------------------------------------------------------
-- Asegurar planes base e AUTO_INCREMENT desde 5
-- IDs reservados: 0=NO PLAN | 1=Start-Up | 2=Business | 4=En Revisión
-- IDs 5+ = Planes Custom creados por empleados
-- ------------------------------------------------------------
INSERT IGNORE INTO `plan_oficina` (`ID_Plan`, `Nombre_Plan`, `Descripcion`, `Cant_Estaciones`) VALUES
(0, 'NO PLAN',       'Sin plan contratado.',                                                                       NULL),
(1, 'Plan Start-Up', '4 Estaciones con suministro de equipo básico, red Wi-Fi y configuración de infraestructura.', 4),
(2, 'Plan Business', '8 Estaciones + sala de juntas con soporte avanzado y despliegue profesional.',                8),
(4, 'En Revisión',   'Solicitud de plan personalizado pendiente de evaluación.',                                    0);

ALTER TABLE `plan_oficina` AUTO_INCREMENT = 5;

-- ------------------------------------------------------------
-- Tabla: solicitud_cotizacion
-- Lo que el cliente ingresa en el cotizador:
--   · Descripcion   → "Descripción del Proyecto" (texto libre)
--   · Cant_PC       → Computadoras / Estaciones de Trabajo
--   · Cant_Impresoras → Impresoras Multifunción de Red
--   · Cant_Puntos_Red → Puntos de Red y Cableado Estructurado
--   · Cant_Servidores → Servidor Local / Rack de Comunicaciones
--   · Cant_Wifi     → Sistema Wi-Fi Mesh Empresarial
--   · Notas_Extra   → "Notaciones y requerimientos adicionales"
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `solicitud_cotizacion` (
    `ID_Solicitud`    int(11)      NOT NULL AUTO_INCREMENT,
    `ID_Usuario`      int(11)      NOT NULL,
    `Descripcion`     varchar(500) NOT NULL,
    `Cant_PC`         int(11)      NOT NULL DEFAULT 0,
    `Cant_Impresoras` int(11)      NOT NULL DEFAULT 0,
    `Cant_Puntos_Red` int(11)      NOT NULL DEFAULT 0,
    `Cant_Servidores` int(11)      NOT NULL DEFAULT 0,
    `Cant_Wifi`       int(11)      NOT NULL DEFAULT 0,
    `Notas_Extra`     text         DEFAULT NULL,
    `Estado`          enum('Pendiente','Aprobada','Rechazada') NOT NULL DEFAULT 'Pendiente',
    `Fecha_Solicitud` datetime     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`ID_Solicitud`),
    KEY `idx_sol_usuario` (`ID_Usuario`),
    KEY `idx_sol_estado`  (`Estado`),
    CONSTRAINT `fk_sol_usuario`
        FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- Tabla: plan_custom_detalle
-- Lo que el empleado define al aprobar una solicitud.
-- Referencia al plan creado (ID >= 5) y a la solicitud de origen.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `plan_custom_detalle` (
    `ID_Detalle`      int(11)       NOT NULL AUTO_INCREMENT,
    `ID_Plan`         int(11)       NOT NULL,
    `ID_Solicitud`    int(11)       DEFAULT NULL,
    `Cant_PC`         int(11)       NOT NULL DEFAULT 0,
    `Cant_Impresoras` int(11)       NOT NULL DEFAULT 0,
    `Cant_Puntos_Red` int(11)       NOT NULL DEFAULT 0,
    `Cant_Servidores` int(11)       NOT NULL DEFAULT 0,
    `Cant_Wifi`       int(11)       NOT NULL DEFAULT 0,
    `Precio_PC`       decimal(10,2) NOT NULL DEFAULT 0.00,
    `Precio_Impresoras` decimal(10,2) NOT NULL DEFAULT 0.00,
    `Precio_Puntos_Red` decimal(10,2) NOT NULL DEFAULT 0.00,
    `Precio_Servidores` decimal(10,2) NOT NULL DEFAULT 0.00,
    `Precio_Wifi`     decimal(10,2) NOT NULL DEFAULT 0.00,
    `Total_Final`     decimal(10,2) NOT NULL DEFAULT 0.00,
    `Notas_Empleado`  text          DEFAULT NULL,
    `Creado_Por`      int(11)       DEFAULT NULL,
    `Fecha_Creacion`  datetime      NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`ID_Detalle`),
    UNIQUE KEY `uq_detalle_plan` (`ID_Plan`),
    KEY `fk_det_solicitud`  (`ID_Solicitud`),
    KEY `fk_det_creado_por` (`Creado_Por`),
    CONSTRAINT `fk_det_plan`
        FOREIGN KEY (`ID_Plan`)      REFERENCES `plan_oficina`         (`ID_Plan`)      ON DELETE CASCADE,
    CONSTRAINT `fk_det_solicitud`
        FOREIGN KEY (`ID_Solicitud`) REFERENCES `solicitud_cotizacion` (`ID_Solicitud`) ON DELETE SET NULL,
    CONSTRAINT `fk_det_creado_por`
        FOREIGN KEY (`Creado_Por`)   REFERENCES `usuario`              (`ID_Usuario`)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
