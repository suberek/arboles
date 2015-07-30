-- phpMyAdmin SQL Dump
-- version 4.2.2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 30-07-2015 a las 16:27:21
-- Versión del servidor: 5.5.20-log
-- Versión de PHP: 5.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `arbolado_db`
--
CREATE DATABASE IF NOT EXISTS `arbolado_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `arbolado_db`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `barrios`
--

DROP TABLE IF EXISTS `barrios`;
CREATE TABLE IF NOT EXISTS `barrios` (
  `id_barrio` int(11) unsigned NOT NULL,
  `barrio_nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especies`
--

DROP TABLE IF EXISTS `especies`;
CREATE TABLE IF NOT EXISTS `especies` (
  `id_especie` int(11) NOT NULL DEFAULT '0',
  `id_familia` int(11) NOT NULL,
  `NOMBRE_FAM` varchar(255) NOT NULL DEFAULT '',
  `NOMBRE_CIE` varchar(255) NOT NULL DEFAULT '',
  `NOMBRE_COM` varchar(255) NOT NULL DEFAULT '',
  `TIPO_FOLLA` varchar(255) NOT NULL DEFAULT '',
  `ORIGEN` varchar(255) NOT NULL DEFAULT '',
  `ICONO` varchar(50) DEFAULT NULL,
  `imagen_completo` varchar(50) DEFAULT NULL,
  `imagen_hoja` varchar(50) DEFAULT NULL,
  `imagen_flor` varchar(50) DEFAULT NULL,
  `descripcion` text,
  `medicinal` text,
  `comestible` text,
  `perfume` tinyint(1) DEFAULT NULL,
  `abejas` tinyint(1) DEFAULT NULL,
  `mariposas` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `familias`
--

DROP TABLE IF EXISTS `familias`;
CREATE TABLE IF NOT EXISTS `familias` (
`id` int(11) NOT NULL,
  `familia` varchar(50) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=73 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `individuos`
--

DROP TABLE IF EXISTS `individuos`;
CREATE TABLE IF NOT EXISTS `individuos` (
`id_individuo` int(11) NOT NULL,
  `ALTURA_TOT` int(11) DEFAULT NULL,
  `DIAMETRO` int(11) DEFAULT NULL,
  `INCLINACIO` int(11) DEFAULT NULL,
  `id_especie` int(11) NOT NULL,
  `calle` varchar(255) DEFAULT NULL,
  `alt_ini` int(11) DEFAULT NULL,
  `espacio_verde` varchar(255) DEFAULT NULL,
  `lat` float(12,10) NOT NULL,
  `lng` float(12,10) NOT NULL,
  `coordenadas` point DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_modificacion` datetime DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=424342 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
`id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `facebook` varchar(50) DEFAULT NULL,
  `twitter` varchar(50) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `profile` text,
  `permisos` int(11) NOT NULL,
  `nombre_completo` varchar(255) DEFAULT NULL,
  `descripcion` text,
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `barrios`
--
ALTER TABLE `barrios`
 ADD PRIMARY KEY (`id_barrio`);

--
-- Indices de la tabla `especies`
--
ALTER TABLE `especies`
 ADD PRIMARY KEY (`id_especie`), ADD KEY `id_especie` (`id_especie`);

--
-- Indices de la tabla `familias`
--
ALTER TABLE `familias`
 ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `individuos`
--
ALTER TABLE `individuos`
 ADD PRIMARY KEY (`id_individuo`), ADD KEY `id_individuo` (`id_individuo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `familias`
--
ALTER TABLE `familias`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=73;
--
-- AUTO_INCREMENT de la tabla `individuos`
--
ALTER TABLE `individuos`
MODIFY `id_individuo` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=424342;
--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
