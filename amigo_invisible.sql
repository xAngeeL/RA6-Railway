-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 30-11-2025 a las 23:19:09
-- Versión del servidor: 9.5.0
-- Versión de PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `amigo_invisible`
--
CREATE DATABASE IF NOT EXISTS `amigo_invisible` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `amigo_invisible`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PARTICIPANTE`
--

CREATE TABLE `PARTICIPANTE` (
  `codigo` int NOT NULL,
  `cod_sorteo` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `amigo` int DEFAULT NULL COMMENT 'Participante al que le regala',
  `regalo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Regalo que quiere recibir el participante'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `PARTICIPANTE`
--

INSERT INTO `PARTICIPANTE` (`codigo`, `cod_sorteo`, `nombre`, `amigo`, `regalo`) VALUES
(1, 1, 'Magic', null, 'Balón baloncesto'),
(2, 1, 'Jordan', null, 'Un anillo'),
(3, 1, 'Stephen Curry', null, 'Un arco'),
(4, 1, 'Kareem', null, 'Unas gafas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `SORTEO`
--

CREATE TABLE `SORTEO` (
  `codigo` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci NOT NULL,
  `finalizado` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `SORTEO`
--

INSERT INTO `SORTEO` (`codigo`, `nombre`, `descripcion`, `finalizado`) VALUES
(1, 'Amigo Invisible DWES', 'Amigo invisible para el alumnado del módulo DWES.', 0),
(2, 'Amigo Invisible del Planes', 'Aquí va la descripción', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `USUARIO`
--

CREATE TABLE `USUARIO` (
  `codigo` int NOT NULL,
  `login` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `es_admin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `USUARIO`
--

INSERT INTO `USUARIO` (`codigo`, `login`, `password`, `es_admin`) VALUES
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `PARTICIPANTE`
--
ALTER TABLE `PARTICIPANTE`
  ADD PRIMARY KEY (`codigo`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `fk_participante_sorteo` (`cod_sorteo`),
  ADD KEY `fk_participante_participante` (`amigo`);

--
-- Indices de la tabla `SORTEO`
--
ALTER TABLE `SORTEO`
  ADD PRIMARY KEY (`codigo`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `USUARIO`
--
ALTER TABLE `USUARIO`
  ADD PRIMARY KEY (`codigo`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `PARTICIPANTE`
--
ALTER TABLE `PARTICIPANTE`
  MODIFY `codigo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `SORTEO`
--
ALTER TABLE `SORTEO`
  MODIFY `codigo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `USUARIO`
--
ALTER TABLE `USUARIO`
  MODIFY `codigo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `PARTICIPANTE`
--
ALTER TABLE `PARTICIPANTE`
  ADD CONSTRAINT `fk_participante_participante` FOREIGN KEY (`amigo`) REFERENCES `PARTICIPANTE` (`codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_participante_sorteo` FOREIGN KEY (`cod_sorteo`) REFERENCES `SORTEO` (`codigo`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
