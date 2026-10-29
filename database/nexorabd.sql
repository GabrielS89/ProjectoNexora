-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-10-2026 a las 17:07:09
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

DROP DATABASE IF EXISTS `nexorabd`;
CREATE DATABASE `nexorabd`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `nexorabd`;

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

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `ID_Pedido` int(11) NOT NULL,
  `ID_Producto` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Precio_Unitario` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `ID_Empresa` int(11) NOT NULL,
  `ID_Usuario` int(11) NOT NULL,
  `ID_Representante` int(11) DEFAULT NULL,
  `ID_Plan` int(11) DEFAULT 0,
  `RUT` varchar(20) NOT NULL,
  `Nombre_Empresa` varchar(100) NOT NULL,
  `Calle` varchar(100) DEFAULT NULL,
  `Esquina` varchar(100) DEFAULT NULL,
  `Numero_Puerta` varchar(20) DEFAULT NULL,
  `Descripcion` text DEFAULT NULL,
  `Categoria_Empresarial` varchar(50) DEFAULT NULL,
  `Tamano` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`ID_Empresa`, `ID_Usuario`, `ID_Representante`, `ID_Plan`, `RUT`, `Nombre_Empresa`, `Calle`, `Esquina`, `Numero_Puerta`, `Descripcion`, `Categoria_Empresarial`, `Tamano`) VALUES
(24, 32, 17, 0, '123456789010', 'User Empresa', 'Gral Flores', 'Lavalleja', '123', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut a', NULL, 'mediana');

-- --------------------------------------------------------

--
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

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `ID_Pedido` int(11) NOT NULL,
  `Fecha` datetime DEFAULT current_timestamp(),
  `Estado` varchar(50) DEFAULT NULL,
  `Total` decimal(10,2) DEFAULT NULL,
  `Metodo_Pago` varchar(50) DEFAULT NULL,
  `Tipo_Entrega` varchar(50) DEFAULT NULL,
  `ID_Usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `ID_Usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`ID_Usuario`) VALUES
(30),
(31),
(33);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plan_custom_detalle`
--

CREATE TABLE `plan_custom_detalle` (
  `ID_Detalle` int(11) NOT NULL,
  `ID_Plan` int(11) NOT NULL,
  `ID_Solicitud` int(11) DEFAULT NULL,
  `Cant_PC` int(11) NOT NULL DEFAULT 0,
  `Cant_Impresoras` int(11) NOT NULL DEFAULT 0,
  `Cant_Puntos_Red` int(11) NOT NULL DEFAULT 0,
  `Cant_Servidores` int(11) NOT NULL DEFAULT 0,
  `Cant_Wifi` int(11) NOT NULL DEFAULT 0,
  `Precio_PC` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Precio_Impresoras` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Precio_Puntos_Red` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Precio_Servidores` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Precio_Wifi` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Total_Final` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Notas_Empleado` text DEFAULT NULL,
  `Creado_Por` int(11) DEFAULT NULL,
  `Fecha_Creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plan_oficina`
--

CREATE TABLE `plan_oficina` (
  `ID_Plan` int(11) NOT NULL,
  `Nombre_Plan` varchar(80) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Cant_Estaciones` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `plan_oficina`
--

INSERT INTO `plan_oficina` (`ID_Plan`, `Nombre_Plan`, `Descripcion`, `Cant_Estaciones`) VALUES
(0, 'NO PLAN', 'Sin plan contratado.', NULL),
(1, 'Oficina Básica', 'Infraestructura para emprendimientos con pocas estaciones de trabajo y configuración mínima.', 4),
(2, 'Oficina Pro', 'Infraestructura para empresas pequeñas con mayor número de estaciones y mejor soporte.', 8),
(3, 'Desarrollo Técnico', 'Infraestructura orientada a equipos de desarrollo con mayor capacidad y conectividad.', 10),
(4, 'En Revisión', 'Solicitudes pendientes de evaluación por parte del equipo administrativo.', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `ID_Producto` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Precio_Base` decimal(10,2) NOT NULL,
  `Stock_Actual` int(11) DEFAULT 0,
  `Categoria` varchar(50) DEFAULT NULL,
  `ID_Proveedor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `ID_Proveedor` int(11) NOT NULL,
  `Nombre_Empresa` varchar(100) NOT NULL,
  `Email_Contacto` varchar(100) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Calle` varchar(100) DEFAULT NULL,
  `Esquina` varchar(100) DEFAULT NULL,
  `Numero_Puerta` varchar(20) DEFAULT NULL,
  `URL_Sitio_web` varchar(255) DEFAULT NULL,
  `Metodo_Pago` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `representante`
--

CREATE TABLE `representante` (
  `ID_Representante` int(11) NOT NULL,
  `Nombre_Completo` varchar(100) DEFAULT NULL,
  `Cargo` varchar(50) DEFAULT NULL,
  `Email_Contacto` varchar(100) DEFAULT NULL,
  `Telefono_Contacto` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `representante`
--

INSERT INTO `representante` (`ID_Representante`, `Nombre_Completo`, `Cargo`, `Email_Contacto`, `Telefono_Contacto`) VALUES
(17, 'Representante Empresa', 'Jefe', 'representante@gmail.com', '098 888 999');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `TipoRol` enum('Usuario','Admin','Super_Admin','Instalacion') NOT NULL,
  `ID_Rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`TipoRol`, `ID_Rol`) VALUES
('Usuario', 1),
('Admin', 2),
('Super_Admin', 3),
('Instalacion', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_cotizacion`
--

CREATE TABLE `solicitud_cotizacion` (
  `ID_Solicitud` int(11) NOT NULL,
  `ID_Usuario` int(11) NOT NULL,
  `Descripcion` varchar(500) NOT NULL,
  `Cant_PC` int(11) NOT NULL DEFAULT 0,
  `Cant_Impresoras` int(11) NOT NULL DEFAULT 0,
  `Cant_Puntos_Red` int(11) NOT NULL DEFAULT 0,
  `Cant_Servidores` int(11) NOT NULL DEFAULT 0,
  `Cant_Wifi` int(11) NOT NULL DEFAULT 0,
  `Tipo_Ordenador` enum('Oficina','Desarrollo') NOT NULL DEFAULT 'Oficina',
  `Gama_Equipo` enum('Baja','Media','Alta') NOT NULL DEFAULT 'Media',
  `Notas_Extra` text DEFAULT NULL,
  `Estado` enum('Pendiente','Aprobada','Rechazada') NOT NULL DEFAULT 'Pendiente',
  `Estado_Instalacion` enum('Pendiente','Aprobada','Rechazada','Instalada') NOT NULL DEFAULT 'Pendiente',
  `Monto_Instalacion` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Fecha_Solicitud` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitud_cotizacion`
--

INSERT INTO `solicitud_cotizacion` (`ID_Solicitud`, `ID_Usuario`, `Descripcion`, `Cant_PC`, `Cant_Impresoras`, `Cant_Puntos_Red`, `Cant_Servidores`, `Cant_Wifi`, `Tipo_Ordenador`, `Gama_Equipo`, `Notas_Extra`, `Estado`, `Estado_Instalacion`, `Monto_Instalacion`, `Fecha_Solicitud`) VALUES
(1, 32, 'Solicitud de paquete 1 - tipo Desarrollo - gama Alta', 0, 0, 0, 0, 0, 'Desarrollo', 'Alta', NULL, 'Aprobada', 'Instalada', 1000.00, '2026-10-26 13:07:56');

-- --------------------------------------------------------

--
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

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`ID_Usuario`, `Email`, `Contrasena`, `Nombre`, `Telefono`, `Tipo_Persona`, `ID_Rol`) VALUES
(30, 'userpersonal@gmail.com', '$2y$10$Ft2rPuxi94kpLx6q2SBgDeokZzxqCuPT9I/7kZDc1LavtbL.V.TTW', 'User Personal', '099999999', 'Persona', 1),
(31, 'useradmin@gmail.com', '$2y$10$gbOSsv7aD9x9VYJtAVLTl.GEudCVHAmxBieHpOJo.0nxMM9HGHftW', 'User Admin', '099 999 999', 'Persona', 2),
(32, 'userempresa@gmail.com', '$2y$10$TTSbRcfYuK//IRAl0QCzye9ZaBTsXBFhYK6da1t2B85ctmHveaFmO', 'User Empresa', '099 999 999', 'Empresa', 1),
(33, 'instalacion@gmail.com', '$2y$10$jCLKA1GgLTF5QUfYswsaruGd2NNQZxER2I2D5Aq6s2fy374D5tSvm', 'Gabriel Rapalin', '096 666 999', 'Persona', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_plan`
--

CREATE TABLE `usuario_plan` (
  `ID_Usuario` int(11) NOT NULL,
  `ID_Plan` int(11) NOT NULL DEFAULT 0,
  `Fecha_Contratacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_plan`
--

INSERT INTO `usuario_plan` (`ID_Usuario`, `ID_Plan`, `Fecha_Contratacion`) VALUES
(32, 0, '2026-10-26 13:07:05');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`ID_Pedido`,`ID_Producto`),
  ADD KEY `ID_Producto` (`ID_Producto`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`ID_Empresa`),
  ADD UNIQUE KEY `RUT` (`RUT`),
  ADD KEY `ID_Representante` (`ID_Representante`),
  ADD KEY `ID_Usuario` (`ID_Usuario`),
  ADD KEY `ID_Plan` (`ID_Plan`);

--
-- Indices de la tabla `garantia`
--
ALTER TABLE `garantia`
  ADD PRIMARY KEY (`ID_Garantia`),
  ADD KEY `ID_Proveedor` (`ID_Proveedor`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`ID_Pedido`),
  ADD KEY `ID_Usuario` (`ID_Usuario`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`ID_Usuario`);

--
-- Indices de la tabla `plan_custom_detalle`
--
ALTER TABLE `plan_custom_detalle`
  ADD PRIMARY KEY (`ID_Detalle`),
  ADD UNIQUE KEY `uq_detalle_plan` (`ID_Plan`),
  ADD KEY `fk_det_solicitud` (`ID_Solicitud`),
  ADD KEY `fk_det_creado_por` (`Creado_Por`);

--
-- Indices de la tabla `plan_oficina`
--
ALTER TABLE `plan_oficina`
  ADD PRIMARY KEY (`ID_Plan`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`ID_Producto`),
  ADD KEY `ID_Proveedor` (`ID_Proveedor`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`ID_Proveedor`);

--
-- Indices de la tabla `representante`
--
ALTER TABLE `representante`
  ADD PRIMARY KEY (`ID_Representante`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`ID_Rol`);

--
-- Indices de la tabla `solicitud_cotizacion`
--
ALTER TABLE `solicitud_cotizacion`
  ADD PRIMARY KEY (`ID_Solicitud`),
  ADD KEY `idx_sol_usuario` (`ID_Usuario`),
  ADD KEY `idx_sol_estado` (`Estado`),
  ADD KEY `idx_sol_instalacion` (`Estado_Instalacion`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`ID_Usuario`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `ID_Rol` (`ID_Rol`);

--
-- Indices de la tabla `usuario_plan`
--
ALTER TABLE `usuario_plan`
  ADD PRIMARY KEY (`ID_Usuario`),
  ADD KEY `ID_Plan` (`ID_Plan`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `ID_Empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `garantia`
--
ALTER TABLE `garantia`
  MODIFY `ID_Garantia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `ID_Pedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plan_custom_detalle`
--
ALTER TABLE `plan_custom_detalle`
  MODIFY `ID_Detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plan_oficina`
--
ALTER TABLE `plan_oficina`
  MODIFY `ID_Plan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `ID_Producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `ID_Proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `representante`
--
ALTER TABLE `representante`
  MODIFY `ID_Representante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `ID_Rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitud_cotizacion`
--
ALTER TABLE `solicitud_cotizacion`
  MODIFY `ID_Solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`ID_Pedido`) REFERENCES `pedido` (`ID_Pedido`),
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`ID_Producto`) REFERENCES `producto` (`ID_Producto`);

--
-- Filtros para la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD CONSTRAINT `empresa_ibfk_1` FOREIGN KEY (`ID_Representante`) REFERENCES `representante` (`ID_Representante`),
  ADD CONSTRAINT `empresa_ibfk_2` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `empresa_ibfk_3` FOREIGN KEY (`ID_Plan`) REFERENCES `plan_oficina` (`ID_Plan`) ON DELETE SET NULL;

--
-- Filtros para la tabla `garantia`
--
ALTER TABLE `garantia`
  ADD CONSTRAINT `garantia_ibfk_1` FOREIGN KEY (`ID_Proveedor`) REFERENCES `proveedor` (`ID_Proveedor`);

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`);

--
-- Filtros para la tabla `persona`
--
ALTER TABLE `persona`
  ADD CONSTRAINT `persona_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `plan_custom_detalle`
--
ALTER TABLE `plan_custom_detalle`
  ADD CONSTRAINT `fk_det_creado_por` FOREIGN KEY (`Creado_Por`) REFERENCES `usuario` (`ID_Usuario`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_det_plan` FOREIGN KEY (`ID_Plan`) REFERENCES `plan_oficina` (`ID_Plan`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_det_solicitud` FOREIGN KEY (`ID_Solicitud`) REFERENCES `solicitud_cotizacion` (`ID_Solicitud`) ON DELETE SET NULL;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`ID_Proveedor`) REFERENCES `proveedor` (`ID_Proveedor`);

--
-- Filtros para la tabla `solicitud_cotizacion`
--
ALTER TABLE `solicitud_cotizacion`
  ADD CONSTRAINT `fk_sol_usuario` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`ID_Rol`) REFERENCES `rol` (`ID_Rol`);

--
-- Filtros para la tabla `usuario_plan`
--
ALTER TABLE `usuario_plan`
  ADD CONSTRAINT `usuario_plan_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_plan_ibfk_2` FOREIGN KEY (`ID_Plan`) REFERENCES `plan_oficina` (`ID_Plan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
