-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-11-2026 a las 01:19:12
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
-- Base de datos: `coliseo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `detalle_venta`
--

INSERT INTO `detalle_venta` (`id`, `venta_id`, `inventario_id`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 11, 1, 1200.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `fecha` date DEFAULT NULL,
  `hora` varchar(20) DEFAULT NULL,
  `lugar` varchar(120) NOT NULL DEFAULT 'Coliseo Centenario, Torreón',
  `tipo` varchar(40) NOT NULL DEFAULT 'Concierto',
  `imagen` varchar(120) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `nombre`, `fecha`, `hora`, `lugar`, `tipo`, `imagen`, `descripcion`, `activo`) VALUES
(1, 'This Is Lenny Jay\'s Michael', '2026-10-08', 'Por confirmar', 'Coliseo Centenario, Torreón', 'Concierto', 'lenny.jpg', 'Espectáculo tributo en el Coliseo Centenario.', 1),
(2, 'Conjunto Primavera y Firma', '2026-10-10', '9:00 PM', 'Coliseo Centenario, Torreón', 'Concierto', 'primavera.jpg', 'Noche de música regional con Conjunto Primavera y Firma.', 1),
(3, 'Enjambre · Daños Luz Tour 2026', '2026-10-17', 'Por confirmar', 'Coliseo Centenario, Torreón', 'Concierto', 'enjambre.jpg', 'Gira de la banda con un repertorio de sus mayores éxitos.', 1),
(4, 'Panteón Rococó · 30 años', '2026-10-29', 'Por confirmar', 'Coliseo Centenario, Torreón', 'Concierto', 'rococo.jpg', 'Celebración de 30 años de Panteón Rococó.', 1),
(5, 'Los Auténticos Caligaris · La fórmula perfecta', '2026-11-18', 'Por confirmar', 'Coliseo Centenario, Torreón', 'Concierto', 'calagaris.jpg', 'Gira La fórmula perfecta.', 1),
(6, 'Caifanes', '2026-11-19', 'Por confirmar', 'Coliseo Centenario, Torreón', 'Concierto', 'caifanes.jpg', 'Caifanes en concierto.', 1),
(7, 'Manuel Medrano · Superior Tour', '2026-11-27', 'Por confirmar', 'Coliseo Centenario, Torreón', 'Concierto', 'medrano.png', 'Superior Tour de Manuel Medrano.', 1),
(8, 'Natanael Cano · Vol. 1 Tour', '2026-11-28', 'Por confirmar', 'Coliseo Centenario, Torreón', 'Concierto', 'natanael.jpg', 'Vol. 1 Tour de Natanael Cano.', 1),
(9, 'Yuridia', NULL, 'Por confirmar', 'Coliseo Centenario, Torreón', 'Concierto', 'yuridia.jpg', 'Yuridia en el Coliseo Centenario. Fecha por confirmar.', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `evento_id` int(11) NOT NULL,
  `zona` varchar(40) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `vendidos` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id`, `evento_id`, `zona`, `precio`, `stock`, `vendidos`) VALUES
(1, 1, 'VIP', 1200.00, 100, 0),
(2, 1, 'Preferente', 650.00, 200, 0),
(3, 1, 'Palcos', 900.00, 150, 0),
(4, 1, 'General baja', 350.00, 400, 0),
(5, 1, 'General alta', 250.00, 500, 0),
(6, 2, 'VIP', 1200.00, 100, 0),
(7, 2, 'Preferente', 650.00, 200, 0),
(8, 2, 'Palcos', 900.00, 150, 0),
(9, 2, 'General baja', 350.00, 400, 0),
(10, 2, 'General alta', 250.00, 500, 0),
(11, 3, 'VIP', 1200.00, 99, 1),
(12, 3, 'Preferente', 650.00, 200, 0),
(13, 3, 'Palcos', 900.00, 150, 0),
(14, 3, 'General baja', 350.00, 400, 0),
(15, 3, 'General alta', 250.00, 500, 0),
(16, 4, 'VIP', 1200.00, 100, 0),
(17, 4, 'Preferente', 650.00, 200, 0),
(18, 4, 'Palcos', 900.00, 150, 0),
(19, 4, 'General baja', 350.00, 400, 0),
(20, 4, 'General alta', 250.00, 500, 0),
(21, 5, 'VIP', 1200.00, 100, 0),
(22, 5, 'Preferente', 650.00, 200, 0),
(23, 5, 'Palcos', 900.00, 150, 0),
(24, 5, 'General baja', 350.00, 400, 0),
(25, 5, 'General alta', 250.00, 500, 0),
(26, 6, 'VIP', 1200.00, 100, 0),
(27, 6, 'Preferente', 650.00, 200, 0),
(28, 6, 'Palcos', 900.00, 150, 0),
(29, 6, 'General baja', 350.00, 400, 0),
(30, 6, 'General alta', 250.00, 500, 0),
(31, 7, 'VIP', 1200.00, 100, 0),
(32, 7, 'Preferente', 650.00, 200, 0),
(33, 7, 'Palcos', 900.00, 150, 0),
(34, 7, 'General baja', 350.00, 400, 0),
(35, 7, 'General alta', 250.00, 500, 0),
(36, 8, 'VIP', 1200.00, 100, 0),
(37, 8, 'Preferente', 650.00, 200, 0),
(38, 8, 'Palcos', 900.00, 150, 0),
(39, 8, 'General baja', 350.00, 400, 0),
(40, 8, 'General alta', 250.00, 500, 0),
(41, 9, 'VIP', 1200.00, 100, 0),
(42, 9, 'Preferente', 650.00, 200, 0),
(43, 9, 'Palcos', 900.00, 150, 0),
(44, 9, 'General baja', 350.00, 400, 0),
(45, 9, 'General alta', 250.00, 500, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `clave` varchar(255) NOT NULL,
  `rol` enum('asistente','organizador','administrador') NOT NULL DEFAULT 'asistente',
  `datos_extra` text DEFAULT NULL,
  `creado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `telefono`, `clave`, `rol`, `datos_extra`, `creado`) VALUES
(1, 'José Julian Salinas Camacho', 'salinasjulian180@gmail.com', '8721306501', '$2y$10$fN0vyMXkvEC67BG0Q3opDuvFRGVvovPwvCPOMYZ7566fbp.CdCcIy', 'asistente', '{\"institucion\":\"\",\"asistencia\":\"Presencial\",\"ciudad\":\"Torreon, Coahuila\"}', '2026-11-02 21:37:10'),
(2, 'Francisco Isay Licerío Esquivel', 'Francisco12@gmail.com', '8721010101', '$2y$10$b/6mWj6Vy6eNo1nX3S8VqeTTCwVB4SSARhHdkCL1ipAZa4AQzpVZK', 'administrador', '{\"empleado\":\"00001\",\"area\":\"Dirección\"}', '2026-11-02 23:49:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `folio` varchar(20) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `paypal_order_id` varchar(40) NOT NULL,
  `paypal_capture_id` varchar(40) DEFAULT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'PAGADO',
  `creado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `folio`, `usuario_id`, `total`, `paypal_order_id`, `paypal_capture_id`, `estado`, `creado`) VALUES
(1, 'CC-000001', 1, 1200.00, '8LH37089FD976132U', '0LL71836UE993545H', 'PAGADO', '2026-11-02 22:56:38');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `inventario_id` (`inventario_id`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `evento_id` (`evento_id`,`zona`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `folio` (`folio`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`inventario_id`) REFERENCES `inventario` (`id`);

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
