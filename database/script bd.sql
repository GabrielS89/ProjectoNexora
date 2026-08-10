CREATE DATABASE IF NOT EXISTS `nexorabd` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE nexorabd;


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `nexorabd`
--

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `rol`
--
CREATE TABLE `rol` (
  `ID_Rol` int(11) NOT NULL,
  `TipoRol` enum('Usuario','Admin','Super_Admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `rol` (`ID_Rol`, `TipoRol`) VALUES
(1, 'Usuario'),
(2, 'Admin'),
(3, 'Super_Admin');

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `usuario`
--
CREATE TABLE `usuario` (
  `ID_Usuario` int(11) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Contrasena` varchar(255) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Tipo_Persona` enum('Persona','Empresa') NOT NULL,
  `ID_Rol` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
  
-- --------------------------------------------------------
-- Estructura de tabla para la tabla `persona`
--
CREATE TABLE `persona` (
  `ID_Usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `representante`
--
CREATE TABLE `representante` (
  `ID_Representante` int(11) NOT NULL,
  `Nombre_Completo` varchar(100) NOT NULL,
  `Cargo` varchar(50) NOT NULL,
  `Email_Contacto` varchar(100) NOT NULL,
  `Telefono_Contacto` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `empresa`
--
CREATE TABLE `empresa` (
  `ID_Empresa` int(11) NOT NULL,
  `ID_Representante` int(11) DEFAULT NULL,
  `RUT` varchar(20) NOT NULL,
  `Nombre_Empresa` varchar(100) NOT NULL,
  `Calle` varchar(100) DEFAULT NULL,
  `Esquina` varchar(100) DEFAULT NULL,
  `Numero_Puerta` varchar(20) DEFAULT NULL,
  `Descripcion` text DEFAULT NULL,
  `Categoria_Empresarial` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `plan_oficina`
--
CREATE TABLE `plan_oficina` (
  `ID_Plan` int(11) NOT NULL,
  `Nombre_Plan` varchar(50) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Cant_Estaciones` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `cotizacion`
--
CREATE TABLE `cotizacion` (
  `ID_Cotizacion` int(11) NOT NULL,
  `Fecha` datetime DEFAULT current_timestamp(),
  `Comentarios` text DEFAULT NULL,
  `Estado` enum('Pendiente','Aprobada','Rechazada') DEFAULT 'Pendiente',
  `ID_Usuario` int(11) DEFAULT NULL,
  `ID_Plan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `proveedor`
--
CREATE TABLE `proveedor` (
  `ID_Proveedor` int(11) NOT NULL,
  `Nombre_Empresa` varchar(100) NOT NULL,
  `Email_Contacto` varchar(100) NOT NULL,
  `Telefono` varchar(20) NOT NULL,
  `Calle` varchar(100) DEFAULT NULL,
  `Esquina` varchar(100) DEFAULT NULL,
  `Numero_Puerta` varchar(20) DEFAULT NULL,
  `URL_Sitio_web` varchar(255) DEFAULT NULL,
  `Metodo_Pago` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `garantia`
--
CREATE TABLE `garantia` (
  `ID_Garantia` int(11) NOT NULL,
  `ID_Proveedor` int(11) DEFAULT NULL,
  `Tipo_Garantia` enum('Completa','Instalacion') DEFAULT NULL,
  `Fecha_Inicio` date DEFAULT NULL,
  `Fecha_Fin` date DEFAULT NULL,
  `Estado_Mantenimiento` varchar(100) DEFAULT NULL,
  `Es_Extendida` tinyint(1) DEFAULT 0,
  `Terminos_Condiciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `producto`
--
CREATE TABLE `producto` (
  `ID_Producto` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Precio_Base` decimal(10,2) NOT NULL,
  `Stock_Actual` int(11) DEFAULT 0,
  `Categoria` varchar(50) DEFAULT NULL,
  `ID_Proveedor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `pedido`
--
CREATE TABLE `pedido` (
  `ID_Pedido` int(11) NOT NULL,
  `Fecha` datetime DEFAULT current_timestamp(),
  `Estado` varchar(50) DEFAULT NULL,
  `Total` decimal(10,2) DEFAULT NULL,
  `Metodo_Pago` varchar(50) DEFAULT NULL,
  `Tipo_Entrega` varchar(50) DEFAULT NULL,
  `ID_Usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `detalle_pedido`
--
CREATE TABLE `detalle_pedido` (
  `ID_Pedido` int(11) NOT NULL,
  `ID_Producto` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Precio_Unitario` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

ALTER TABLE `rol` ADD PRIMARY KEY (`ID_Rol`);
ALTER TABLE `usuario` ADD PRIMARY KEY (`ID_Usuario`), ADD UNIQUE KEY `Email` (`Email`), ADD KEY `ID_Rol` (`ID_Rol`);
ALTER TABLE `persona` ADD PRIMARY KEY (`ID_Usuario`);
ALTER TABLE `representante` ADD PRIMARY KEY (`ID_Representante`);
ALTER TABLE `empresa` ADD PRIMARY KEY (`ID_Empresa`), ADD UNIQUE KEY `RUT` (`RUT`), ADD KEY `ID_Representante` (`ID_Representante`);
ALTER TABLE `plan_oficina` ADD PRIMARY KEY (`ID_Plan`);
ALTER TABLE `cotizacion` ADD PRIMARY KEY (`ID_Cotizacion`), ADD KEY `ID_Usuario` (`ID_Usuario`), ADD KEY `ID_Plan` (`ID_Plan`);
ALTER TABLE `proveedor` ADD PRIMARY KEY (`ID_Proveedor`);
ALTER TABLE `garantia` ADD PRIMARY KEY (`ID_Garantia`), ADD KEY `ID_Proveedor` (`ID_Proveedor`);
ALTER TABLE `producto` ADD PRIMARY KEY (`ID_Producto`), ADD KEY `ID_Proveedor` (`ID_Proveedor`);
ALTER TABLE `pedido` ADD PRIMARY KEY (`ID_Pedido`), ADD KEY `ID_Usuario` (`ID_Usuario`);
ALTER TABLE `detalle_pedido` ADD PRIMARY KEY (`ID_Pedido`,`ID_Producto`), ADD KEY `ID_Producto` (`ID_Producto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

ALTER TABLE `rol` MODIFY `ID_Rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `usuario` MODIFY `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `representante` MODIFY `ID_Representante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `empresa` MODIFY `ID_Empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `plan_oficina` MODIFY `ID_Plan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `cotizacion` MODIFY `ID_Cotizacion` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `proveedor` MODIFY `ID_Proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `garantia` MODIFY `ID_Garantia` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `producto` MODIFY `ID_Producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `pedido` MODIFY `ID_Pedido` int(11) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------
-- VOLCADO DE DATOS POR DEFECTO / SEMILLAS DE LA APLICACIÓN
-- --------------------------------------------------------

--
-- Cuentas de usuario por defecto (Contraseñas encriptadas con BCRYPT):
-- 1. Super Admin:  admin@nexora.com     /  Admin123!   (ID_Rol = 3)
-- 2. Administrador: admin2@nexora.com    /  Admin123!   (ID_Rol = 2)
-- 3. Persona:       usuario@nexora.com   /  User123!    (ID_Rol = 1)
-- 4. Empresa:       empresa@nexora.com   /  Empresa123! (ID_Rol = 1)
--

INSERT INTO `usuario` (`ID_Usuario`, `Email`, `Contrasena`, `Nombre`, `Telefono`, `Tipo_Persona`, `ID_Rol`) VALUES
(1, 'admin@nexora.com', '$2y$10$wE97711N4j4sU1Nl.lJ6vOqYmE5eL5fB0lKzO4L1gH1W2X3Y4Z5a6', 'Super Administrador Nexora', '099123456', 'Persona', 3),
(2, 'admin2@nexora.com', '$2y$10$wE97711N4j4sU1Nl.lJ6vOqYmE5eL5fB0lKzO4L1gH1W2X3Y4Z5a6', 'Administrador Soporte', '099654321', 'Persona', 2),
(3, 'usuario@nexora.com', '$2y$10$5s3W.J1J1N1N1N1N1N1N1uJ6vOqYmE5eL5fB0lKzO4L1gH1W2X3Y4', 'Juan Pérez', '098765432', 'Persona', 1),
(4, 'empresa@nexora.com', '$2y$10$8k4X.K2K2O2O2O2O2O2O2vK7wPrZnF6fM6gC1mL0P5M2hI2X3Y4Z5', 'Tech Solutions S.A.', '099135086', 'Empresa', 1);

--
-- Volcado de datos para la tabla `persona`
--
INSERT INTO `persona` (`ID_Usuario`) VALUES
(1),
(2),
(3);

--
-- Volcado de datos para la tabla `representante`
--
INSERT INTO `representante` (`ID_Representante`, `Nombre_Completo`, `Cargo`, `Email_Contacto`, `Telefono_Contacto`) VALUES
(1, 'Carlos Rodríguez', 'Director de Operaciones TI', 'crodriguez@techsolutions.com', '099888777');

--
-- Volcado de datos para la tabla `empresa`
--
INSERT INTO `empresa` (`ID_Empresa`, `ID_Representante`, `RUT`, `Nombre_Empresa`, `Calle`, `Esquina`, `Numero_Puerta`, `Descripcion`, `Categoria_Empresarial`) VALUES
(1, 1, '219998880011', 'Tech Solutions S.A.', 'Av. 18 de Julio', 'Plaza Independencia', '1234', 'Empresa enfocada en desarrollo de software y servicios cloud.', 'tecnologia');

--
-- Volcado de datos para la tabla `plan_oficina`
--
INSERT INTO `plan_oficina` (`ID_Plan`, `Nombre_Plan`, `Descripcion`, `Cant_Estaciones`) VALUES
(1, 'Plan Start-Up', 'Suministro de 4 equipos, instalación de cableado estructurado, red Wi-Fi y configuración básica.', 4),
(2, 'Plan Business', 'Suministro de 8 equipos, infraestructura en áreas comunes, soporte y configuración avanzada.', 8),
(3, 'Plan Custom', 'Consultoría personalizada, selección de componentes y software bajo licencia según requerimiento.', NULL);

--
-- Volcado de datos para la tabla `proveedor`
--
INSERT INTO `proveedor` (`ID_Proveedor`, `Nombre_Empresa`, `Email_Contacto`, `Telefono`, `Calle`, `Esquina`, `Numero_Puerta`, `URL_Sitio_web`, `Metodo_Pago`) VALUES
(1, 'TechSupply Uruguay', 'ventas@techsupply.uy', '099012876', 'Canelones', 'Convención', '987', 'https://techsupply.uy', 'Transferencia Bancaria');

--
-- Volcado de datos para la tabla `producto`
--
INSERT INTO `producto` (`ID_Producto`, `Nombre`, `Descripcion`, `Precio_Base`, `Stock_Actual`, `Categoria`, `ID_Proveedor`) VALUES
(1, 'Switch Gigabit 24 Puertos', 'Switch gestionable de alta velocidad para redes corporativas.', 250.00, 15, 'Redes', 1),
(2, 'Access Point Wi-Fi 6 Enterprise', 'Punto de acceso dual-band para alta densidad de conexiones.', 120.00, 30, 'Redes', 1),
(3, 'Servidor Rack 1U Xeon', 'Servidor de rack Intel Xeon 32GB RAM SSD NVMe 1TB.', 1850.00, 5, 'Hardware', 1);

--
-- Filtros (Llaves Foráneas)
--

ALTER TABLE `usuario` ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`ID_Rol`) REFERENCES `rol` (`ID_Rol`);
ALTER TABLE `persona` ADD CONSTRAINT `persona_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`) ON DELETE CASCADE;
ALTER TABLE `empresa` ADD CONSTRAINT `empresa_ibfk_1` FOREIGN KEY (`ID_Representante`) REFERENCES `representante` (`ID_Representante`);
ALTER TABLE `cotizacion` ADD CONSTRAINT `cotizacion_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`), ADD CONSTRAINT `cotizacion_ibfk_2` FOREIGN KEY (`ID_Plan`) REFERENCES `plan_oficina` (`ID_Plan`);
ALTER TABLE `garantia` ADD CONSTRAINT `garantia_ibfk_1` FOREIGN KEY (`ID_Proveedor`) REFERENCES `proveedor` (`ID_Proveedor`);
ALTER TABLE `producto` ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`ID_Proveedor`) REFERENCES `proveedor` (`ID_Proveedor`);
ALTER TABLE `pedido` ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`);
ALTER TABLE `detalle_pedido` ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`ID_Pedido`) REFERENCES `pedido` (`ID_Pedido`), ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`ID_Producto`) REFERENCES `producto` (`ID_Producto`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Usuarios de prueba

INSERT INTO `usuario` (`ID_Usuario`, `Email`, `Contrasena`, `Nombre`, `Telefono`, `Tipo_Persona`, `ID_Rol`) 
VALUES (NULL, 'admin@gmail.com', '$2y$10$Y88JPjuSGTXC4AoFl/q15eKDw0QD4Gc/LXeZB.RYt94kpSUnHkIp2', 'Admin', '0999999999', 'Persona', '2')