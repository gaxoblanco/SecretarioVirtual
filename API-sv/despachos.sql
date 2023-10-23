-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 24-10-2023 a las 00:56:57
-- Versión del servidor: 8.0.32
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `despachos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `expedientes`
--

CREATE TABLE `expedientes` (
  `id_expediente` int NOT NULL,
  `id_lista_despacho` int DEFAULT NULL,
  `numero_expediente` varchar(50) DEFAULT NULL,
  `anio_expediente` int DEFAULT NULL,
  `caratula` text,
  `reservado` tinyint(1) DEFAULT NULL,
  `dependencia` int DEFAULT NULL,
  `tipo_lista` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16le;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id_movimiento` int NOT NULL,
  `id_expediente` int DEFAULT NULL,
  `fecha_movimiento` date DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL,
  `texto` text,
  `titulo` varchar(255) DEFAULT NULL,
  `despacho` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16le;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `secretaries`
--

CREATE TABLE `secretaries` (
  `secreataryId` int NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `Semail` varchar(255) NOT NULL,
  `Spass` text NOT NULL,
  `token` text,
  `id_users` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16le;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `firstName` varchar(255) DEFAULT NULL,
  `lastName` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16le;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_expedients`
--

CREATE TABLE `user_expedients` (
  `id_exp` int NOT NULL,
  `id_lista_despacho` int DEFAULT NULL,
  `numero_exp` varchar(255) DEFAULT NULL,
  `anio_exp` varchar(255) DEFAULT NULL,
  `caratula` text,
  `reservado` int DEFAULT NULL,
  `dependencia` varchar(255) DEFAULT NULL,
  `tipo_lista` varchar(255) DEFAULT NULL,
  `id_user` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16le;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_exp_move`
--

CREATE TABLE `user_exp_move` (
  `id_move` int NOT NULL,
  `fecha_movimiento` date DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL,
  `texto` text,
  `titulo` varchar(255) DEFAULT NULL,
  `despacho` varchar(255) DEFAULT NULL,
  `id_exp` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16le;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `expedientes`
--
ALTER TABLE `expedientes`
  ADD PRIMARY KEY (`id_expediente`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_expediente` (`id_expediente`);

--
-- Indices de la tabla `secretaries`
--
ALTER TABLE `secretaries`
  ADD PRIMARY KEY (`secreataryId`),
  ADD KEY `id_users` (`id_users`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- Indices de la tabla `user_expedients`
--
ALTER TABLE `user_expedients`
  ADD PRIMARY KEY (`id_exp`),
  ADD KEY `id_user` (`id_user`);

--
-- Indices de la tabla `user_exp_move`
--
ALTER TABLE `user_exp_move`
  ADD PRIMARY KEY (`id_move`),
  ADD KEY `id_exp` (`id_exp`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `expedientes`
--
ALTER TABLE `expedientes`
  MODIFY `id_expediente` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id_movimiento` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `secretaries`
--
ALTER TABLE `secretaries`
  MODIFY `secreataryId` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `user_expedients`
--
ALTER TABLE `user_expedients`
  MODIFY `id_exp` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `user_exp_move`
--
ALTER TABLE `user_exp_move`
  MODIFY `id_move` int NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`id_expediente`) REFERENCES `expedientes` (`id_expediente`);

--
-- Filtros para la tabla `secretaries`
--
ALTER TABLE `secretaries`
  ADD CONSTRAINT `secretaries_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `users` (`id_user`);

--
-- Filtros para la tabla `user_expedients`
--
ALTER TABLE `user_expedients`
  ADD CONSTRAINT `user_expedients_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Filtros para la tabla `user_exp_move`
--
ALTER TABLE `user_exp_move`
  ADD CONSTRAINT `user_exp_move_ibfk_1` FOREIGN KEY (`id_exp`) REFERENCES `user_expedients` (`id_exp`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
