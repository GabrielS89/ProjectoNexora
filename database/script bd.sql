CREATE DATABASE IF NOT EXISTS `nexorabd` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE nexorabd;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-08-2026 a las 17:57:43
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

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

--
-- Volcado de datos para la tabla `cotizacion`
--

INSERT INTO `cotizacion` (`ID_Cotizacion`, `Fecha`, `Comentarios`, `Estado`, `ID_Usuario`, `ID_Plan`) VALUES
(1, '2026-08-24 13:34:32', '=== COTIZACIÓN PERSONALIZADA (PLAN CUSTOM) ===\nDescripción: ejemplo 1234 12345\n\n--- DESGLOSE DE COMPONENTES SELECCIONADOS ---\n- 5x PC/Estaciones de Trabajo ($800 c/u): $4,000.00 USD\n- 3x Impresoras de Red ($400 c/u): $1,200.00 USD\n- 3x Puntos de Red y Cableado ($150 c/u): $450.00 USD\n- 1x Servidor / Rack ($1,200 c/u): $1,200.00 USD\nTOTAL ESTIMADO: $6,850.00 USD\n\n', 'Pendiente', 29, 0),
(2, '2026-08-24 13:34:50', '=== COTIZACIÓN PERSONALIZADA (PLAN CUSTOM) ===\nDescripción: Ejemplo1 123123\n\n--- DESGLOSE DE COMPONENTES SELECCIONADOS ---\n- 1x PC/Estaciones de Trabajo ($800 c/u): $800.00 USD\n- 2x Impresoras de Red ($400 c/u): $800.00 USD\n- 2x Puntos de Red y Cableado ($150 c/u): $300.00 USD\n- 1x Servidor / Rack ($1,200 c/u): $1,200.00 USD\nTOTAL ESTIMADO: $3,100.00 USD\n\n', 'Pendiente', 29, 0),
(3, '2026-08-24 13:35:24', '=== COTIZACIÓN PERSONALIZADA (PLAN CUSTOM) ===\nDescripción: asdasda\n\n--- DESGLOSE DE COMPONENTES SELECCIONADOS ---\n- 2x PC/Estaciones de Trabajo ($800 c/u): $1,600.00 USD\n- 4x Impresoras de Red ($400 c/u): $1,600.00 USD\n- 1x Servidor / Rack ($1,200 c/u): $1,200.00 USD\n- 1x Sistema Wi-Fi Mesh ($250 c/u): $250.00 USD\nTOTAL ESTIMADO: $4,650.00 USD\n\n', 'Pendiente', 29, 0),
(4, '2026-08-24 13:49:45', '=== COTIZACION PERSONALIZADA (PLAN CUSTOM) ===\nDescripcion: ffffffffffffff\n\n--- DESGLOSE DE COMPONENTES SELECCIONADOS ---\n- 4x PC/Estaciones de Trabajo ($800 c/u): $3,200.00 USD\n- 2x Impresoras de Red ($400 c/u): $800.00 USD\n- 3x Servidor / Rack ($1,200 c/u): $3,600.00 USD\n- 2x Sistema Wi-Fi Mesh ($250 c/u): $500.00 USD\nTOTAL ESTIMADO: $8,100.00 USD\n\n', 'Pendiente', 29, 4),
(5, '2026-08-24 14:26:59', '=== COTIZACION PERSONALIZADA (PLAN CUSTOM) ===\nDescripcion: aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa\n\n--- DESGLOSE DE COMPONENTES SELECCIONADOS ---\n- 1x PC/Estaciones de Trabajo ($800 c/u): $800.00 USD\n- 3x Impresoras de Red ($400 c/u): $1,200.00 USD\n- 2x Puntos de Red y Cableado ($150 c/u): $300.00 USD\n- 2x Servidor / Rack ($1,200 c/u): $2,400.00 USD\n- 1x Sistema Wi-Fi Mesh ($250 c/u): $250.00 USD\nTOTAL ESTIMADO: $4,950.00 USD\n\n--- NOTACIONES EXTRA DE LA EMPRESA ---\naaaaaaaaaaaaaaaaaaaaaaaa\n', 'Pendiente', 29, 4);

-- --------------------------------------------------------

--
CREATE TABLE `representante` (
  `ID_Representante` int(11) NOT NULL,
  `Nombre_Completo` varchar(100) NOT NULL,
  `Cargo` varchar(50) NOT NULL,
  `Email_Contacto` varchar(100) NOT NULL,
  `Telefono_Contacto` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `ID_Empresa` int(11) NOT NULL,
  `ID_Usuario` int(100) NOT NULL,
  `ID_Representante` int(11) DEFAULT NULL,
  `ID_Plan` int(11) DEFAULT NULL,
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
(22, 27, 15, 11, '123456789012', 'ACME', '123345', 'Esquina 123', '452', 'AAAAAAAAAAAAAAAAAAAAAAAAAAa', 'comercio', 'mediana'),
(23, 29, 16, 10, '12345678910', 'Empresa1', 'Gral 123', 'Esquina1', 'Puerta1', 'AAAAAAAAAAAAA', 'AAAAAAAAAAAAA', 'mediana');

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
(28);

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

--
-- Volcado de datos para la tabla `plan_custom_detalle`
--

INSERT INTO `plan_custom_detalle` (`ID_Detalle`, `ID_Plan`, `ID_Solicitud`, `Cant_PC`, `Cant_Impresoras`, `Cant_Puntos_Red`, `Cant_Servidores`, `Cant_Wifi`, `Precio_PC`, `Precio_Impresoras`, `Precio_Puntos_Red`, `Precio_Servidores`, `Precio_Wifi`, `Total_Final`, `Notas_Empleado`, `Creado_Por`, `Fecha_Creacion`) VALUES
(1, 10, 1, 1, 3, 2, 0, 3, 800.00, 1200.00, 300.00, 0.00, 750.00, 3050.00, NULL, 4, '2026-08-24 14:54:37'),
(2, 11, 2, 2, 1, 0, 0, 0, 1600.00, 400.00, 0.00, 0.00, 0.00, 2000.00, 'asdasd', 4, '2026-08-28 14:19:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plan_oficina`
--

CREATE TABLE `plan_oficina` (
  `ID_Plan` int(11) NOT NULL,
  `Nombre_Plan` varchar(50) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Cant_Estaciones` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `plan_oficina`
--

INSERT INTO `plan_oficina` (`ID_Plan`, `Nombre_Plan`, `Descripcion`, `Cant_Estaciones`) VALUES
(0, 'NO PLAN', 'Sin plan contratado.', NULL),
(1, 'Plan Start-Up', '4 Estaciones con suministro de equipo básico, red Wi-Fi y configuración de infraestructura.', 4),
(2, 'Plan Business', '8 Estaciones + sala de juntas con soporte avanzado y despliegue profesional.', 8),
(4, 'En Revisión', 'Esperando Cotización', 0),
(5, 'NO PLAN', 'Sin plan contratado.', NULL),
(9, 'NO PLAN', 'Sin plan contratado.', NULL),
(10, 'Plan Custom — Empresa1', 'asdasd', 1),
(11, 'Plan Custom — ACME', 'asdasd', 2);

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

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`ID_Producto`, `Nombre`, `Descripcion`, `Precio_Base`, `Stock_Actual`, `Categoria`, `ID_Proveedor`) VALUES
(1, 'Ryzen 3', 'Proscesador', 170.00, 20, 'Proscesador', 1),
(2, 'HyperX 16GB RAM 2666Mhz DDR4', 'RAM HyperX 2666Mhz 16GB Alta velocidad DDR4', 140.00, 4, 'RAM', 2);

-- --------------------------------------------------------

--
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

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`ID_Proveedor`, `Nombre_Empresa`, `Email_Contacto`, `Telefono`, `Calle`, `Esquina`, `Numero_Puerta`, `URL_Sitio_web`, `Metodo_Pago`) VALUES
(1, 'Intel', 'intel@gmail.com', '0999099090', '123', 'Bolivia', '12333', 'intel.com', 'Débito'),
(2, 'HyperX', 'hyperx@gmail.com', '099999999', '123', 'Esquina 123', '259', 'hyperx.com', 'Crédito'),
(3, 'AMD', 'amd@gmail.com', '09999999', '123345', 'Esquina 123', '259', 'https://amd.com', 'Débito');

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
(15, 'jefe1', 'Jefe', 'jefe@gmail.com', '099999123123'),
(16, 'Empresa1', 'Empresa1', 'emailpersonalrepresentanteprueba@gmail.com', '099100999');

-- --------------------------------------------------------

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

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`ID_Rol`, `TipoRol`) VALUES
(1, 'Usuario'),
(2, 'Admin'),
(3, 'Super_Admin');

-- --------------------------------------------------------

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

--
-- Volcado de datos para la tabla `solicitud_cotizacion`
--

INSERT INTO `solicitud_cotizacion` (`ID_Solicitud`, `ID_Usuario`, `Descripcion`, `Cant_PC`, `Cant_Impresoras`, `Cant_Puntos_Red`, `Cant_Servidores`, `Cant_Wifi`, `Notas_Extra`, `Estado`, `Fecha_Solicitud`) VALUES
(1, 29, 'asdasdasd', 1, 3, 2, 0, 3, 'asdasda', 'Aprobada', '2026-08-24 14:43:49'),
(2, 27, 'asdasdasad', 2, 1, 0, 0, 0, 'asdads', 'Aprobada', '2026-08-28 14:18:01');

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
(4, 'admin@gmail.com', '$2y$10$Y88JPjuSGTXC4AoFl/q15eKDw0QD4Gc/LXeZB.RYt94kpSUnHkIp2', 'Admin', '0999099090', 'Persona', 2),
(27, 'acme@gmail.com', '$2y$10$s1mR/bjTHXyuPN5cltXnpO5W3KUmZ0zeh.FcLAxrMBwR0QfBgx3/u', 'ACME', '921368956', 'Empresa', 1),
(28, 'gmailprueba@gmail.com', '$2y$10$N8ZSLNJrTOQnwQtz6Z9SLOaK/CUnqart.SXOmEgiPZWVMd/AsgJym', 'Prueba', '0999099090', 'Persona', 1),
(29, 'empresa1@gmail.com', '$2y$10$ePYniwzqkjYt9y.5c6si.uX3diC8jlPZuZ/bper0bny3JjpG/RHWm', 'Empresa1', '099999999', 'Empresa', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_plan`
--

CREATE TABLE `usuario_plan` (
  `ID_Usuario` int(11) NOT NULL,
  `ID_Plan` int(11) NOT NULL,
  `Fecha_Contratacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_plan`
--

INSERT INTO `usuario_plan` (`ID_Usuario`, `ID_Plan`, `Fecha_Contratacion`) VALUES
(27, 11, '2026-08-28 14:19:27'),
(29, 10, '2026-08-24 14:54:37');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cotizacion`
--
ALTER TABLE `cotizacion`
  ADD PRIMARY KEY (`ID_Cotizacion`),
  ADD KEY `ID_Usuario` (`ID_Usuario`),
  ADD KEY `ID_Plan` (`ID_Plan`),
  ADD KEY `idx_cotizacion_estado` (`Estado`),
  ADD KEY `idx_cotizacion_usuario` (`ID_Usuario`);

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
  ADD KEY `idx_sol_estado` (`Estado`);

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
(6, 'admin@nexora.com', '$2y$10$iZ3OOS8CwGSoj8tiNHwPQuTwO0i2Rxo0TWkZ.ByH5VZ5Uq0EXaDb.', 'martino pucella', '099641258', 'Persona', 3),
(7, 'empresa@nexora.com', '$2y$10$xJnUGdTrA8jdZZIZEZ2YW.n19hyRY94KGEyhVn5MEiQdE0OsKJQuW', 'Pedro Juanitez', '098951295', 'Empresa', 1),
(8, 'admin2@nexora.com', '$2y$10$mFVjpbyV90LUgzSaUl5sV.duMPhmU6C5Uoh4E./83JKklHefAOCaG', 'Luciana Perez', '097543103', 'Persona', 2),
(9, 'usuario@nexora.com', '$2y$10$vKXj3Tb8yisDWS9Zr.hij.P5FboAoHuAW9i//dEqRCv/CWXLTNCD.', 'Gonzalo Nircof', '097654123', 'Persona', 1);

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
  ADD CONSTRAINT `usuario_plan_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuario` (`ID_Usuario`),
  ADD CONSTRAINT `usuario_plan_ibfk_2` FOREIGN KEY (`ID_Plan`) REFERENCES `plan_oficina` (`ID_Plan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
