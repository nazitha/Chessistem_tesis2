-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-03-2025 a las 04:11:27
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
-- Base de datos: `chestsystem`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `academias`
--

CREATE TABLE `academias` (
  `nombre_academia` varchar(50) NOT NULL,
  `correo_academia` varchar(40) DEFAULT NULL,
  `telefono_academia` int(11) DEFAULT NULL,
  `representante_academia` varchar(40) NOT NULL,
  `direccion_academia` varchar(150) DEFAULT NULL,
  `estado_academia` bit(1) DEFAULT b'1',
  `ciudad_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `academias`
--

INSERT INTO `academias` (`nombre_academia`, `correo_academia`, `telefono_academia`, `representante_academia`, `direccion_academia`, `estado_academia`, `ciudad_id`) VALUES
('Academia importada completa', 'importmail@gmail.com', 12345678, '028-123001-4258W', 'Por ahi nomas', b'1', NULL),
('Academia importada parcial', '', 0, '028-123001-4258W', '', b'1', NULL),
('Nuevo nombre', 'correo@gmail.com', 0, 'Director nombre prueba', 'esta es una dirección de prueba', b'1', 13);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaciones_permisos`
--

CREATE TABLE `asignaciones_permisos` (
  `rol_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `asignaciones_permisos`
--

INSERT INTO `asignaciones_permisos` (`rol_id`, `permiso_id`) VALUES
(1, 4),
(2, 1),
(2, 2),
(2, 5),
(3, 1),
(3, 5),
(4, 1),
(4, 2),
(4, 3),
(4, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditorias`
--

CREATE TABLE `auditorias` (
  `id` int(11) NOT NULL,
  `correo_id` varchar(40) DEFAULT NULL,
  `tabla_afectada` varchar(30) DEFAULT NULL,
  `valor_previo` varchar(650) DEFAULT NULL,
  `valor_posterior` varchar(650) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `equipo` varchar(50) DEFAULT NULL,
  `accion` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `auditorias`
--

INSERT INTO `auditorias` (`id`, `correo_id`, `tabla_afectada`, `valor_previo`, `valor_posterior`, `fecha`, `hora`, `equipo`, `accion`) VALUES
(1, 'engellargaespadavargas@gmail.com', 'Paises', '[País ingresado: Nuevo, desde el formulario de federaciones]', '-', '2025-01-22', '21:20:18', 'DESKTOP-4QEFDOG', 'Inserción'),
(2, 'engellargaespadavargas@gmail.com', 'Paises', '[País ingresado: Test, desde el formulario de federaciones]', '-', '2025-01-22', '21:22:35', 'DESKTOP-4QEFDOG', 'Inserción'),
(3, 'engellargaespadavargas@gmail.com', 'Paises', '[País ingresado: Test2, desde el formulario de ciudades]', '-', '2025-01-22', '21:32:11', 'DESKTOP-4QEFDOG', 'Inserción'),
(4, 'engellargaespadavargas@gmail.com', 'Paises', '[País ingresado: Test3, desde el formulario de ciudades]', '-', '2025-01-22', '21:33:39', 'DESKTOP-4QEFDOG', 'Inserción'),
(5, 'engellargaespadavargas@gmail.com', 'Paises', '[Departamento ingresado: Depto1 en el país: Costa Rica, desde el formulario de ciudades]', '-', '2025-01-23', '20:24:54', 'DESKTOP-4QEFDOG', 'Inserción'),
(6, 'engellargaespadavargas@gmail.com', 'Departamentos', '[Departamento ingresado: Depto1 en el país: Guatemala, desde el formulario de ciudades]', '-', '2025-01-23', '20:26:31', 'DESKTOP-4QEFDOG', 'Inserción'),
(7, 'engellargaespadavargas@gmail.com', 'Departamentos', '[Departamento ingresado: Depto1 en el país: El Salvador, desde el formulario de ciudades]', '-', '2025-01-23', '20:27:38', 'DESKTOP-4QEFDOG', 'Inserción'),
(8, 'engellargaespadavargas@gmail.com', 'Departamentos', '[Departamento ingresado: Depto1 en el país: Jamaica, desde el formulario de ciudades]', '-', '2025-01-23', '20:27:56', 'DESKTOP-4QEFDOG', 'Inserción'),
(9, 'engellargaespadavargas@gmail.com', 'Ciudades', '[Ciudad ingresada: ciudad1 en el departamento:  del país: Costa Rica, desde el formulario de ciudades]', '-', '2025-01-23', '20:46:26', 'DESKTOP-4QEFDOG', 'Inserción'),
(10, 'engellargaespadavargas@gmail.com', 'Ciudades', '[Ciudad ingresada: Ciudad2 en el departamento: Depto1 del país: Costa Rica, desde el formulario de ciudades]', '-', '2025-01-23', '20:50:41', 'DESKTOP-4QEFDOG', 'Inserción'),
(11, 'engellargaespadavargas@gmail.com', 'Ciudades', '[Ciudad ingresada: Ciudad1 en el departamento: Depto1 del país: Costa Rica, desde el formulario de ciudades]', '-', '2025-01-23', '20:53:00', 'DESKTOP-4QEFDOG', 'Inserción'),
(12, 'engellargaespadavargas@gmail.com', 'Ciudades', '[Ciudad ingresada: Ciudad2 en el departamento: Depto1 del país: Costa Rica, desde el formulario de ciudades]', '-', '2025-01-23', '20:53:10', 'DESKTOP-4QEFDOG', 'Inserción'),
(13, 'engellargaespadavargas@gmail.com', 'Paises', '[País ingresado: Pais1, desde el formulario de ciudades]', '-', '2025-01-23', '20:57:58', 'DESKTOP-4QEFDOG', 'Inserción'),
(14, 'engellargaespadavargas@gmail.com', 'Paises', '[País ingresado: Prueba, desde el formulario de ciudades]', '-', '2025-01-24', '20:20:59', 'DESKTOP-4QEFDOG', 'Inserción'),
(15, 'engellargaespadavargas@gmail.com', 'Paises', '[País ingresado: Prueba rtest, desde el formulario de ciudades]', '-', '2025-01-24', '20:23:20', 'DESKTOP-4QEFDOG', 'Inserción'),
(16, 'engellargaespadavargas@gmail.com', 'Paises', '[País editado: , desde el formulario de ciudades]', '[Nuevo valor: Prueba editado 2.1]', '2025-01-24', '20:27:09', 'DESKTOP-4QEFDOG', 'Inserción'),
(17, 'engellargaespadavargas@gmail.com', 'Paises', '[País editado: , desde el formulario de ciudades]', '[Nuevo valor: Prueba editado 2.2]', '2025-01-24', '20:28:23', 'DESKTOP-4QEFDOG', 'Edición'),
(18, 'engellargaespadavargas@gmail.com', 'Paises', '[País editado: , desde el formulario de ciudades]', '[Nuevo valor: Prueba editado 2.3]', '2025-01-24', '20:30:04', 'DESKTOP-4QEFDOG', 'Edición'),
(19, 'engellargaespadavargas@gmail.com', 'Paises', '[País editado: Prueba editado 2.3, desde el formulario de ciudades]', '[Nuevo valor: Prueba editado 2.4]', '2025-01-24', '20:31:04', 'DESKTOP-4QEFDOG', 'Edición'),
(20, 'engellargaespadavargas@gmail.com', 'Departamentos', '[Departamento ingresado: Prueba en el país: Honduras, desde el formulario de ciudades]', '-', '2025-01-24', '21:30:27', 'DESKTOP-4QEFDOG', 'Inserción'),
(21, 'engellargaespadavargas@gmail.com', 'Departamentos', '[Departamento editado: Prueba, correspondiente al país: , desde el formulario de ciudades]', '[Nuevo valor: Prueba v1, correspondiente al pais: (SELECT nombre_pais FROM paises WHERE id_pais = 5)]', '2025-01-24', '21:39:35', 'DESKTOP-4QEFDOG', 'Edición'),
(22, 'engellargaespadavargas@gmail.com', 'Departamentos', '[Departamento ingresado: Prueba en el país: Panamá, desde el formulario de ciudades]', '-', '2025-01-24', '21:44:47', 'DESKTOP-4QEFDOG', 'Inserción'),
(23, 'engellargaespadavargas@gmail.com', 'Departamentos', '[Departamento editado: Prueba, correspondiente al país: Panamá, desde el formulario de ciudades]', '[Nuevo valor: Prueba v1, correspondiente al pais: Panamá]', '2025-01-24', '21:44:56', 'DESKTOP-4QEFDOG', 'Edición'),
(24, 'engellargaespadavargas@gmail.com', 'Departamentos', '[Departamento editado: Prueba v1, correspondiente al país: Panamá, desde el formulario de ciudades]', '[Nuevo valor: Prueba v1, correspondiente al pais: Jamaica]', '2025-01-24', '21:45:30', 'DESKTOP-4QEFDOG', 'Edición'),
(25, 'engellargaespadavargas@gmail.com', 'Ciudades', '[Ciudad ingresada: Ciudad prueba en el departamento: Prueba v1 del país: Honduras, desde el formulario de ciudades]', '-', '2025-01-24', '22:18:08', 'DESKTOP-4QEFDOG', 'Inserción'),
(26, 'engellargaespadavargas@gmail.com', 'Ciudades', '[Ciudad editada: Ciudad prueba, correspondiente al departamento: Prueba v1, país: Honduras, desde el formulario de ciudades]', '[Nuevo valor: Ciudad prueba v1, correspondiente al departamento: Prueba v1, país: Honduras]', '2025-01-24', '22:18:18', 'DESKTOP-4QEFDOG', 'Edición'),
(27, 'engellargaespadavargas@gmail.com', 'Ciudades', '[Ciudad editada: Ciudad prueba, correspondiente al departamento: Prueba v1, país: Honduras, desde el formulario de ciudades]', '[Nuevo valor: Ciudad prueba v1, correspondiente al departamento: Prueba v1, país: Honduras]', '2025-01-24', '22:29:14', 'DESKTOP-4QEFDOG', 'Edición'),
(28, 'engellargaespadavargas@gmail.com', 'Ciudades', '[Ciudad editada: Ciudad prueba v1, correspondiente al departamento: Prueba v1, país: Honduras, desde el formulario de ciudades]', '[Nuevo valor: Ciudad prueba v1, correspondiente al departamento: Depto1, país: Costa Rica]', '2025-01-24', '22:29:45', 'DESKTOP-4QEFDOG', 'Edición'),
(29, 'engellargaespadavargas@gmail.com', 'Ciudades', '[Ciudad editada: Ciudad prueba v1, correspondiente al departamento: Depto1, país: Costa Rica, desde el formulario de ciudades]', '[Nuevo valor: Ciudad prueba v1, correspondiente al departamento: Cartago, país: Costa Rica]', '2025-01-24', '22:30:08', 'DESKTOP-4QEFDOG', 'Edición'),
(30, 'engellargaespadavargas@gmail.com', 'País', '[País eliminado: Test2 desde el formulario de ciudades]', '[-]', '2025-01-24', '22:54:52', 'DESKTOP-4QEFDOG', 'Eliminación'),
(31, 'engellargaespadavargas@gmail.com', 'Departamentos', '[Departamento eliminado: Depto1 desde el formulario de ciudades]', '[-]', '2025-01-24', '23:03:08', 'DESKTOP-4QEFDOG', 'Eliminación'),
(32, 'engellargaespadavargas@gmail.com', 'Departamentos', '[Departamento eliminado: Depto1, correspondiente al país: El Salvador desde el formulario de ciudades]', '[-]', '2025-01-24', '23:04:22', 'DESKTOP-4QEFDOG', 'Eliminación'),
(33, 'engellargaespadavargas@gmail.com', 'Ciudades', '[Ciudad eliminada: Ciudad prueba v1, correspondiente al departamento: Cartago, país: Costa Rica,  desde el formulario de ciudades]', '[-]', '2025-01-24', '23:18:04', 'DESKTOP-4QEFDOG', 'Eliminación'),
(34, 'engellargaespadavargas@gmail.com', 'Departamentos', '[Departamento eliminado: Depto1, correspondiente al país: Costa Rica desde el formulario de ciudades]', '[-]', '2025-01-24', '23:18:29', 'DESKTOP-4QEFDOG', 'Eliminación'),
(35, 'engellargaespadavargas@gmail.com', 'Ciudades', '[Ciudad ingresada: ciudad en el departamento: Prueba v1 del país: Jamaica, desde el formulario de ciudades]', '-', '2025-01-24', '23:20:14', 'DESKTOP-4QEFDOG', 'Inserción'),
(36, 'engellargaespadavargas@gmail.com', 'Departamentos', '[Departamento eliminado: Prueba v1, correspondiente al país: Jamaica desde el formulario de ciudades]', '[-]', '2025-01-24', '23:20:21', 'DESKTOP-4QEFDOG', 'Eliminación'),
(37, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Permiso asignado: Eliminación al rol: 0]', '[-]', '2025-01-26', '17:39:47', 'DESKTOP-4QEFDOG', 'Asignación'),
(38, 'engellargaespadavargas@gmail.com', 'Asignación de permisos', '[Permiso asignado: Eliminación al rol: 0]', '[-]', '2025-01-26', '17:41:50', 'DESKTOP-4QEFDOG', 'Asignación'),
(39, 'engellargaespadavargas@gmail.com', 'Asignación de permisos', '[Permiso asignado: Eliminación al rol: estudiante]', '[-]', '2025-01-26', '17:46:04', 'DESKTOP-4QEFDOG', 'Asignación'),
(43, 'engellargaespadavargas@gmail.com', 'Asignación de permisos', '[Permiso removido: Eliminación del rol: estudiante]', '[-]', '2025-01-26', '18:13:23', 'DESKTOP-4QEFDOG', 'Remoción'),
(44, 'engellargaespadavargas@gmail.com', 'Asignación de permisos', '[Permiso asignado: Eliminación al rol: estudiante]', '[-]', '2025-01-26', '18:13:40', 'DESKTOP-4QEFDOG', 'Asignación'),
(45, 'engellargaespadavargas@gmail.com', 'Asignación de permisos', '[Permiso removido: Eliminación del rol: estudiante]', '[-]', '2025-01-26', '18:13:53', 'DESKTOP-4QEFDOG', 'Remoción'),
(47, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: Academia Test 2  Correo: correo@gmail.com  Telefono: 78945612  Director: Director nombre prueba  Dirección: esta es una dirección de prueba  Ciudad: Chichigalpa, León (Nicaragua)  Estado: Activo]', '[-]', '2025-01-26', '20:07:24', 'DESKTOP-4QEFDOG', 'Inserción'),
(57, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: Nuevo nombre  Correo: correo@gmail.com  Telefono: 12345678  Director: Director nombre prueba  Dirección: esta es una dirección de prueba  Ciudad: Chichigalpa, Chinandega (Nicaragua)  Estado: Inactivo]', '[Academia: Academia Test 2 edit  Correo: correoedit@gmail.com  Telefono: 2147483647  Director: Director nombre prueba edit  Dirección: esta es una dirección de prueba edit  Ciudad: Catarina, Masaya (Nicaragua)  Estado: Inactivo]', '2025-01-26', '20:52:07', 'DESKTOP-4QEFDOG', 'Modificación'),
(58, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: Academia Test edit  Correo: testedit.academia@gmail.com  Telefono: 2147483647  Director: Director de academia edit  Dirección: Esta es una dirección eduit  Ciudad: Catarina, Masaya (Nicaragua)  Estado: Activo]', '[Academia: Academia Test  Correo: teste.academia@gmail.com  Telefono: 214748364  Director: Director de academia  Dirección: Esta es una dirección  Ciudad: Camoapa, Boaco (Nicaragua)  Estado: Activo]', '2025-01-26', '21:00:12', 'DESKTOP-4QEFDOG', 'Modificación'),
(59, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: Test  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Chichigalpa, León (Nicaragua)  Estado: Activo]', '[-]', '2025-01-26', '21:26:00', 'DESKTOP-4QEFDOG', 'Inserción'),
(60, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: Test  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Chichigalpa, León (Nicaragua)  Estado: Inactivo]', '[Academia: Test  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Chichigalpa, León (Nicaragua)  Estado: Inactivo]', '2025-01-26', '21:26:27', 'DESKTOP-4QEFDOG', 'Modificación'),
(61, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: Test  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Chichigalpa, León (Nicaragua)  Estado: Inactivo]', '[Academia: Test  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Chichigalpa, León (Nicaragua)  Estado: Inactivo]', '2025-01-26', '21:27:03', 'DESKTOP-4QEFDOG', 'Modificación'),
(62, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: Test  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Bonanza, Zelaya Central (Nicaragua)  Estado: Activo]', '[-]', '2025-01-26', '21:30:44', 'DESKTOP-4QEFDOG', 'Inserción'),
(63, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: test  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Acoyapa, Chontales (Nicaragua)  Estado: Activo]', '[-]', '2025-01-26', '21:32:12', 'DESKTOP-4QEFDOG', 'Inserción'),
(64, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: test  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Altagracia, Rivas (Nicaragua)  Estado: Activo]', '[-]', '2025-01-26', '21:35:22', 'DESKTOP-4QEFDOG', 'Inserción'),
(65, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: test  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Altagracia, Rivas (Nicaragua)  Estado: Inactivo]', '[Academia: test  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Altagracia, Rivas (Nicaragua)  Estado: Activo]', '2025-01-26', '21:35:33', 'DESKTOP-4QEFDOG', 'Modificación'),
(66, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: test  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Altagracia, Rivas (Nicaragua)  Estado: Inactivo]', '[Academia: test  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Altagracia, Rivas (Nicaragua)  Estado: Activo]', '2025-01-26', '21:36:18', 'DESKTOP-4QEFDOG', 'Modificación'),
(67, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: test  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Altagracia, Rivas (Nicaragua)  Estado: Inactivo]', '[-]', '2025-01-26', '21:36:41', 'DESKTOP-4QEFDOG', 'Eliminación'),
(68, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: test  Correo:   Telefono: 12345  Director: test  Dirección:   Ciudad: Bejuco, Granada (Nicaragua)  Estado: Activo]', '[-]', '2025-01-26', '21:38:38', 'DESKTOP-4QEFDOG', 'Inserción'),
(69, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: test 12  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Acoyapa, Chontales (Nicaragua)  Estado: Activo]', '[-]', '2025-01-26', '21:40:29', 'DESKTOP-4QEFDOG', 'Inserción'),
(70, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: test 12  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Acoyapa, Chontales (Nicaragua)  Estado: Inactivo]', '[Academia: test 12  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Acoyapa, Chontales (Nicaragua)  Estado: Activo]', '2025-01-26', '21:43:48', 'DESKTOP-4QEFDOG', 'Modificación'),
(71, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: test  Correo:   Telefono: 12345  Director: test  Dirección:   Ciudad: Bejuco, Granada (Nicaragua)  Estado: Inactivo]', '[Academia: test  Correo:   Telefono: 12345  Director: test  Dirección:   Ciudad: Bejuco, Granada (Nicaragua)  Estado: Inactivo]', '2025-01-26', '21:43:55', 'DESKTOP-4QEFDOG', 'Modificación'),
(72, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: test 12  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Acoyapa, Chontales (Nicaragua)  Estado: Activo]', '[Academia: test 12  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Acoyapa, Chontales (Nicaragua)  Estado: Activo]', '2025-01-26', '21:44:06', 'DESKTOP-4QEFDOG', 'Modificación'),
(73, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: test 12  Correo:   Telefono: 0  Director: test  Dirección:   Ciudad: Acoyapa, Chontales (Nicaragua)  Estado: Activo]', '[Academia: test 12  Correo:   Telefono: 1  Director: test  Dirección:   Ciudad: Acoyapa, Chontales (Nicaragua)  Estado: Activo]', '2025-01-26', '21:45:28', 'DESKTOP-4QEFDOG', 'Modificación'),
(74, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: test 12  Correo:   Telefono: 1  Director: test  Dirección:   Ciudad: Acoyapa, Chontales (Nicaragua)  Estado: Activo]', '[Academia: test 12  Correo:   Telefono: NULL  Director: test  Dirección:   Ciudad: Acoyapa, Chontales (Nicaragua)  Estado: Activo]', '2025-01-26', '21:47:35', 'DESKTOP-4QEFDOG', 'Modificación'),
(75, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: testukl  Correo:   Telefono: NULL  Director: testukl  Dirección:   Ciudad: Bejuco, Granada (Nicaragua)  Estado: Activo]', '[-]', '2025-01-26', '21:48:00', 'DESKTOP-4QEFDOG', 'Inserción'),
(76, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: asdfadf  Correo:   Telefono: NULL  Director: fdsf  Dirección:   Ciudad: Bejuco, Granada (Nicaragua)  Estado: Activo]', '[-]', '2025-01-26', '21:48:33', 'DESKTOP-4QEFDOG', 'Inserción'),
(77, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: test  Correo:   Telefono: 12345  Director: test  Dirección:   Ciudad: Bejuco, Granada (Nicaragua)  Estado: Inactivo]', '[Academia: test  Correo:   Telefono: NULL  Director: test  Dirección:   Ciudad: Bejuco, Granada (Nicaragua)  Estado: Activo]', '2025-01-26', '21:48:53', 'DESKTOP-4QEFDOG', 'Modificación'),
(78, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: asdfadf  Correo:   Telefono:   Director: fdsf  Dirección:   Ciudad: Bejuco, Granada (Nicaragua)  Estado: Activo]', '[Academia: asdfadf  Correo:   Telefono: NULL  Director: fdsf  Dirección:   Ciudad: Bejuco, Granada (Nicaragua)  Estado: Inactivo]', '2025-01-26', '21:49:27', 'DESKTOP-4QEFDOG', 'Modificación'),
(79, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: asdfadf  Correo:   Telefono:   Director: fdsf  Dirección:   Ciudad: Bejuco, Granada (Nicaragua)  Estado: Inactivo]', '[-]', '2025-01-26', '21:49:48', 'DESKTOP-4QEFDOG', 'Eliminación'),
(80, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: test  Correo:   Telefono:   Director: test  Dirección:   Ciudad: Bejuco, Granada (Nicaragua)  Estado: Activo]', '[-]', '2025-01-26', '21:50:01', 'DESKTOP-4QEFDOG', 'Eliminación'),
(81, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: test 12  Correo:   Telefono:   Director: test  Dirección:   Ciudad: Acoyapa, Chontales (Nicaragua)  Estado: Activo]', '[-]', '2025-01-26', '21:50:06', 'DESKTOP-4QEFDOG', 'Eliminación'),
(82, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: testukl  Correo:   Telefono: 0  Director: testukl  Dirección:   Ciudad: Bejuco, Granada (Nicaragua)  Estado: Activo]', '[-]', '2025-01-26', '21:50:11', 'DESKTOP-4QEFDOG', 'Eliminación'),
(83, 'engellargaespadavargas@gmail.com', 'Miembros', '[Identificación: Nuevo123  Nombres: Nuevo nombre  Apellidos: Nuevo Apellido  Sexo: Masculino  Fecha de nacimiento: 2002-08-03  Teléfono: 12345678  Fecha de inscripción: 2025-01-27  Club: ejemplo@gmail.com  Correo asignado: ejemplo@gmail.com  Ciudad: Acoyapa, Chontales (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[-]', '2025-01-27', '20:48:45', 'DESKTOP-4QEFDOG', 'Inserción'),
(84, 'engellargaespadavargas@gmail.com', 'Miembros', '[Identificación: Nuevo123  Nombres: Nuevo nombre  Apellidos: Nuevo Apellido  Sexo: Masculino  Fecha de nacimiento: 2002-08-03  Teléfono: 12345678  Fecha de inscripción: 2025-01-27  Club: Porcinos FC  Correo asignado: ejemplo@gmail.com  Ciudad: Acoyapa, Chontales (Nicaragua)  Academia: Nuevo nombre  Estado: Inactivo]', '[Identificación: Nuevo12345  Nombres: Nuevo nombre edit  Apellidos: Nuevo Apellido edit  Sexo: Femenino  Fecha de nacimiento: 2002-08-04  Teléfono: 1234567866  Fecha de inscripción: 2025-01-26  Club: Porcinos FC edit  Correo asignado: usuario@gmail.com  Ciudad: Altagracia, Rivas (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '2025-01-27', '22:08:41', 'DESKTOP-4QEFDOG', 'Modificación'),
(85, 'engellargaespadavargas@gmail.com', 'Miembros', '[Identificación: Nuevo12345  Nombres: Nuevo nombre edit  Apellidos: Nuevo Apellido edit  Sexo: usuario@gmail.com  Fecha de nacimiento: Activo  Teléfono: 04-08-2002  Fecha de inscripción: Altagracia, Rivas (Nicaragua)  Club: 1234567866  Correo asignado: admin  Ciudad: Femenino  Academia: Nuevo nombre  Estado: 26-01-2025]', '[-]', '2025-01-27', '22:18:54', 'DESKTOP-4QEFDOG', 'Eliminación'),
(86, 'engellargaespadavargas@gmail.com', 'Miembros', '[Identificación: 123  Nombres: test  Apellidos: test  Sexo: Masculino  Fecha de nacimiento: 2025-01-27  Teléfono: 123455678  Fecha de inscripción: 2025-01-27  Club: Club porcinos  Correo asignado: ejemplo@gmail.com  Ciudad: Ciudad Sandino, Managua (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[-]', '2025-01-27', '22:23:53', 'DESKTOP-4QEFDOG', 'Inserción'),
(87, 'engellargaespadavargas@gmail.com', 'Miembros', '[Identificación: 123  Nombres: test  Apellidos: test  Sexo: ejemplo@gmail.com  Fecha de nacimiento: Activo  Teléfono: 27-01-2025  Fecha de inscripción: Ciudad Sandino, Managua (Nicaragua)  Club: 123455678  Correo asignado: estudiante  Ciudad: Masculino  Academia: Nuevo nombre  Estado: 27-01-2025]', '[-]', '2025-01-27', '22:23:58', 'DESKTOP-4QEFDOG', 'Eliminación'),
(88, 'engellargaespadavargas@gmail.com', 'Miembros', '[Identificación: 123  Nombres: test  Apellidos: test  Sexo: Masculino  Fecha de nacimiento: 2025-01-27  Teléfono: 12345678  Fecha de inscripción: 2025-01-27  Club: Porcinos club  Correo asignado: ejemplo@gmail.com  Ciudad: Ciudad Sandino, Managua (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[-]', '2025-01-27', '22:26:45', 'DESKTOP-4QEFDOG', 'Inserción'),
(89, 'engellargaespadavargas@gmail.com', 'Miembros', '[Identificación: 123  Nombres: test  Apellidos: test  Sexo: Masculino  Fecha de nacimiento: 27-01-2025  Teléfono: 12345678  Fecha de inscripción: 27-01-2025  Club: Porcinos club  Correo asignado: ejemplo@gmail.com  Ciudad: Ciudad Sandino, Managua (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[-]', '2025-01-27', '22:28:45', 'DESKTOP-4QEFDOG', 'Eliminación'),
(90, 'engellargaespadavargas@gmail.com', 'Miembros', '[Identificación: 1234  Nombres: Jose  Apellidos: Padilla  Sexo: Masculino  Fecha de nacimiento: 2025-01-27  Teléfono: 78945612  Fecha de inscripción: 2025-01-27  Club: Porcinos Club  Correo asignado: ejemplo@gmail.com  Ciudad: Ciudad Sandino, Managua (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[-]', '2025-01-27', '22:29:38', 'DESKTOP-4QEFDOG', 'Inserción'),
(91, 'engellargaespadavargas@gmail.com', 'Miembros', '[Identificación: 1234  Nombres: Jose  Apellidos: Padilla  Sexo: Masculino  Fecha de nacimiento: 2025-01-27  Teléfono: 78945612  Fecha de inscripción: 2025-01-27  Club: Porcinos Club  Correo asignado: ejemplo@gmail.com  Ciudad: Acoyapa, Chontales (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[Identificación: 1234  Nombres: Jose  Apellidos: Padilla  Sexo: Masculino  Fecha de nacimiento: 2025-01-27  Teléfono: 78945612  Fecha de inscripción: 2025-01-27  Club: Porcinos Club  Correo asignado: ejemplo@gmail.com  Ciudad: Acoyapa, Chontales (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '2025-01-27', '22:29:46', 'DESKTOP-4QEFDOG', 'Modificación'),
(92, 'engellargaespadavargas@gmail.com', 'Miembros', '[Identificación: 1234  Nombres: Jose  Apellidos: Padilla  Sexo: Masculino  Fecha de nacimiento: 27-01-2025  Teléfono: 78945612  Fecha de inscripción: 27-01-2025  Club: Porcinos Club  Correo asignado: ejemplo@gmail.com  Ciudad: Acoyapa, Chontales (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[-]', '2025-01-27', '22:31:46', 'DESKTOP-4QEFDOG', 'Eliminación'),
(93, 'engellargaespadavargas@gmail.com', 'Miembros', '[Identificación: 003-042503-2569Q  Nombres: Adriana Sofia  Apellidos: Chamorro Zamora  Sexo: Femenino  Fecha de nacimiento: 2003-06-06  Teléfono: 54123698  Fecha de inscripción: 2025-01-30  Club:   Correo asignado: ejemplo@gmail.com  Ciudad: Ciudad Sandino, Managua (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[-]', '2025-01-30', '20:34:33', 'DESKTOP-4QEFDOG', 'Inserción'),
(94, 'engellargaespadavargas@gmail.com', 'Miembros', '[Identificación: 028-123001-4258W  Nombres: Ernesto José  Apellidos: Martínez Avilés  Sexo: Masculino  Fecha de nacimiento: 2001-12-30  Teléfono: 89756412  Fecha de inscripción: 2025-01-30  Club:   Correo asignado: estudiante.dominical@gmail.com  Ciudad: Chichigalpa, León (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[-]', '2025-01-30', '20:52:40', 'DESKTOP-4QEFDOG', 'Inserción'),
(95, 'engellargaespadavargas@gmail.com', 'FIDES', '[FIDE-ID: 20190014  Ajedrecista: Adriana Sofia Chamorro Zamora (003-042503-2569Q)  Federación: Federación Nacional de Ajedrez de Guatemala (FEMAJUC)  Título: Maestra Internacional Femenina (WIM)  ELO Blitz: 800  ELO Clásico: 1500  ELO rápido: 1800]', '[-]', '2025-01-30', '22:00:46', 'DESKTOP-4QEFDOG', 'Incersión'),
(96, 'engellargaespadavargas@gmail.com', 'FIDES', '[FIDE-ID: 20190328  Ajedrecista: Engel Antonio Largaespada Vargas (001-030802-1004G)  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Título: Candidato a Maestro (CM)  ELO Blitz: 0  ELO Clásico: 800  ELO rápido: 900]', '[FIDE-ID: 201903286  Ajedrecista: Ernesto José Martínez Avilés (028-123001-4258W)  Federación: Federación Nacional de Ajedrez de Honduras (FEDEMA)  Título: Gran Maestro (GM)  ELO Blitz: 100  ELO Clásico: 200  ELO rápido: 300]', '2025-01-31', '22:01:02', 'DESKTOP-4QEFDOG', 'Modificación'),
(97, 'engellargaespadavargas@gmail.com', 'FIDES', '[FIDE-ID: 20190328  Ajedrecista: Ernesto José Martínez Avilés (028-123001-4258W)  Federación: Federación Nacional de Ajedrez de Honduras (FEDEMA)  Título: Gran Maestro (GM)  ELO Blitz: 100  ELO Clásico: 200  ELO rápido: 300]', '[FIDE-ID: 201903286  Ajedrecista: Ernesto José Martínez Avilés (028-123001-4258W)  Federación: Federación Nacional de Ajedrez de Honduras (FEDEMA)  Título: Gran Maestro (GM)  ELO Blitz: 100  ELO Clásico: 200  ELO rápido: 300]', '2025-01-31', '23:02:09', 'DESKTOP-4QEFDOG', 'Modificación'),
(98, 'engellargaespadavargas@gmail.com', 'FIDES', '[FIDE-ID: 20190328U  Ajedrecista: Engel Antonio Largaespada Vargas (001-030802-1004G)  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Título: Candidato a Maestro (CM)  ELO Blitz: 500  ELO Clásico: 1000  ELO rápido: 1800]', '[-]', '2025-01-31', '23:19:36', 'DESKTOP-4QEFDOG', 'Incersión'),
(99, 'engellargaespadavargas@gmail.com', 'FIDES', '[FIDE-ID: 20190328  Ajedrecista: Engel Antonio Largaespada Vargas  Federación: FENAMAC  Título: CM  ELO Blitz: 500  ELO Clásico: 1000  ELO rápido: 0]', '[-]', '2025-01-31', '23:20:09', 'DESKTOP-4QEFDOG', 'Eliminación'),
(100, 'engellargaespadavargas@gmail.com', 'FIDES', '[FIDE-ID: 20190328  Ajedrecista: Engel Antonio Largaespada Vargas (001-030802-1004G)  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Título: Candidato a Maestro (CM)  ELO Blitz: 500  ELO Clásico: 1000  ELO rápido: 1800]', '[-]', '2025-01-31', '23:20:40', 'DESKTOP-4QEFDOG', 'Incersión'),
(101, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: docente.dominical@gmail.com  Rol: 1  Estado: 1 Contraseña: 123]', '-', '2025-02-02', '20:11:08', 'DESKTOP-4QEFDOG', 'Inserción'),
(102, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: Nuevo torneo  Fecha: 2025-02-12  Hora: 15:00  Categoría: Ajedrez Relámpago (Blitz)  Formato: Blitz 3+2 (3+2)  Lugar: Academia Estrellas del Ajedrez  Rondas: 7  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Á', '[-]', '2025-02-02', '20:52:18', 'DESKTOP-4QEFDOG', 'Inserción'),
(103, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: Registro de prueba  Fecha: 2025-02-05  Hora: 15:00  Categoría: Ajedrez Clásico  Formato: Clásico (90+30)  Lugar: Academia Estrallas del Ajedrez  Rondas: 7  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbit', '[-]', '2025-02-02', '21:03:16', 'DESKTOP-4QEFDOG', 'Inserción'),
(104, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: ejemplo@gmail.com  Rol: 3  Estado: 1 Contraseña: Crepair10x@]', '[Correo: ejemplo@gmail.com  Rol: 2  Estado: 1 Contraseña: Crepair10x@]', '2025-02-02', '21:23:40', 'DESKTOP-4QEFDOG', 'Edición'),
(108, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: Registro de prueba  Fecha: 2025-02-05  Hora: 15:00:00  Categoría: Ajedrez Clásico  Formato: Clásico (90+30)  Lugar: Academia Estrallas del Ajedrez  Rondas: 7  Federación: FENAMAC  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: Registro de prueba  Fecha: 2025-02-05  Hora: 15:00:00  Categoría: Ajedrez Clásico  Formato: Clásico (90+30)  Lugar: Academia Estrallas del Ajedrez  Rondas: 7  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Ár', '2025-02-02', '22:53:48', 'DESKTOP-4QEFDOG', 'Modificación'),
(109, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: Registro de prueba  Fecha: 2025-02-05  Hora: 15:00:00  Categoría: Ajedrez Clásico  Formato: Clásico (90+30)  Lugar: Academia Estrallas del Ajedrez  Rondas: 7  Federación: FENAMAC  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: Registro de prueba editado  Fecha: 2025-03-05  Hora: 16:00:00  Categoría: Ajedrez Rápido (Rapid)  Formato: Fischer (5+3)  Lugar: Academia Estrellas del Ajedrez editado  Rondas: 5  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Organizador: Adriana Sofia Chamorro Zamora (003-042503-2569Q)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Adriana Sofia Chamorro Zamora (003-042503-2569Q)  Árbitro principal: Engel Antonio Largaespada Vargas (001-03', '2025-02-02', '22:54:38', 'DESKTOP-4QEFDOG', 'Modificación'),
(110, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: Registro de prueba editado  Fecha: 2025-03-05  Hora: 16:00:00  Categoría: Ajedrez Rápido (Rapid)  Formato: Rápido (15+10)  Lugar: Academia Estrellas del Ajedrez editado  Rondas: 5  Federación: FENAMAC  Organizador: Adriana Sofia Chamorro Zamora  Director: Engel Antonio Largaespada Vargas  Árbitro: Adriana Sofia Chamorro Zamora Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Adriana Sofia Chamorro Zamora  Estado: Finalizado]', '[-]', '2025-02-02', '23:02:33', 'DESKTOP-4QEFDOG', 'Eliminación'),
(112, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: Nuevo torneo  Fecha: 2025-02-03  Hora: 16:30:00  Categoría: Ajedrez Rápido (Rapid)  Formato: Rápido (15+10)  Lugar: Here  Rondas: 6  Federación: FEDEMA  Organizador: Engel Antonio Largaespada Vargas  Director: Adriana Sofia Chamorro Zamora  Árbitro: Adriana Sofia Chamorro Zamora Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Adriana Sofia Chamorro Zamora  Estado: Activo]', '[Torneo: Nuevo torneo editado  Fecha: 2025-02-03  Hora: 16:30:00  Categoría: Ajedrez Rápido (Rapid)  Formato: Rápido (15+10)  Lugar: Here  Rondas: 6  Federación: Federación Nacional de Ajedrez de Honduras (FEDEMA)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Engel', '2025-02-03', '21:04:27', 'DESKTOP-4QEFDOG', 'Modificación'),
(113, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: Nuevo torneo editado  Fecha: 2025-02-03  Hora: 16:30:00  Categoría: Ajedrez Rápido (Rapid)  Formato: Rápido (15+10)  Lugar: Here  Rondas: 6  Federación: FEDEMA  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: Nuevo torneo edit  Fecha: 2025-02-03  Hora: 16:30:00  Categoría: Ajedrez Rápido (Rapid)  Formato: Rápido (15+10)  Lugar: Here  Rondas: 6  Federación: Federación Nacional de Ajedrez de Honduras (FEDEMA)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Engel An', '2025-02-03', '21:05:03', 'DESKTOP-4QEFDOG', 'Modificación'),
(114, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: xtorneo  Fecha: 2025-02-04  Hora: 10:30  Categoría: Ajedrez por Equipos  Formato: Clásico (90+30)  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Adriana Sofia Chamorro Zamora (003-042503-2569Q)  Árbitro: Adriana Sofia Chamorro Zamora (003-042503-2569Q)  Árbitro principal: Adriana Sofia Chamorro Zamora (003-042503-2569Q)  Árbitro adjunto: Engel Antonio Largaespada Vargas ', '[-]', '2025-02-03', '21:11:51', 'DESKTOP-4QEFDOG', 'Inserción'),
(115, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: xtorneo  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Adriana Sofia Chamorro Zamora  Árbitro: Adriana Sofia Chamorro Zamora Árbitro principal: Adriana Sofia Chamorro Zamora  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: xtorneo edit  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Engel A', '2025-02-03', '22:26:05', 'DESKTOP-4QEFDOG', 'Modificación'),
(122, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: xtorneo editado  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: xtorneo editado  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Enge', '2025-02-03', '22:52:04', 'DESKTOP-4QEFDOG', 'Modificación'),
(123, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: xtorneo edit  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: xtorneo editado  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Enge', '2025-02-03', '22:52:26', 'DESKTOP-4QEFDOG', 'Modificación'),
(125, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: xtorneo edit  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: xtorneo edit  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Engel A', '2025-02-03', '23:04:55', 'DESKTOP-4QEFDOG', 'Modificación'),
(126, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: xtorneo edit  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: xtorneo edit  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Engel A', '2025-02-03', '23:09:42', 'DESKTOP-4QEFDOG', 'Modificación'),
(127, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: xtorneo edit  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: xtorneo edit  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Engel A', '2025-02-03', '23:11:17', 'DESKTOP-4QEFDOG', 'Modificación'),
(128, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: xtorneo edit  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: xtorneo editx  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Engel ', '2025-02-03', '23:11:30', 'DESKTOP-4QEFDOG', 'Modificación'),
(129, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: xtorneo editx  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: xtorneo editado  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Enge', '2025-02-03', '23:26:53', 'DESKTOP-4QEFDOG', 'Modificación'),
(130, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: xtorneo editado  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: xtorneo editado  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Enge', '2025-02-03', '23:36:30', 'DESKTOP-4QEFDOG', 'Modificación'),
(144, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: Nuevo torneo edit  Fecha: 2025-02-03  Hora: 16:30:00  Categoría: Ajedrez Rápido (Rapid)  Formato: Rápido (15+10)  Lugar: Here  Rondas: 6  Federación: FEDEMA  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: Nuevo torneo edit  Fecha: 2025-02-03  Hora: 16:30:00  Categoría: Ajedrez Rápido (Rapid)  Formato: Rápido (15+10)  Lugar: Here  Rondas: 6  Federación: Federación Nacional de Ajedrez de Honduras (FEDEMA)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Engel An', '2025-02-04', '21:10:31', 'DESKTOP-4QEFDOG', 'Modificación'),
(145, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Enge', '2025-02-04', '21:10:53', 'DESKTOP-4QEFDOG', 'Modificación'),
(146, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Enge', '2025-02-04', '21:12:17', 'DESKTOP-4QEFDOG', 'Modificación'),
(147, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Enge', '2025-02-04', '21:13:52', 'DESKTOP-4QEFDOG', 'Modificación'),
(148, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: En', '2025-02-04', '21:14:04', 'DESKTOP-4QEFDOG', 'Modificación'),
(150, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-04', '21:14:49', 'DESKTOP-4QEFDOG', 'Modificación');
INSERT INTO `auditorias` (`id`, `correo_id`, `tabla_afectada`, `valor_previo`, `valor_posterior`, `fecha`, `hora`, `equipo`, `accion`) VALUES
(151, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x3  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-04', '21:16:26', 'DESKTOP-4QEFDOG', 'Modificación'),
(154, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x3  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-04', '21:25:55', 'DESKTOP-4QEFDOG', 'Modificación'),
(162, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x3  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-04', '21:33:32', 'DESKTOP-4QEFDOG', 'Modificación'),
(163, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-04', '21:35:19', 'DESKTOP-4QEFDOG', 'Modificación'),
(164, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x1  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-04', '21:45:09', 'DESKTOP-4QEFDOG', 'Modificación'),
(165, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x1  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Enge', '2025-02-04', '21:45:51', 'DESKTOP-4QEFDOG', 'Modificación'),
(166, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: En', '2025-02-04', '21:47:44', 'DESKTOP-4QEFDOG', 'Modificación'),
(167, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x edit  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunt', '2025-02-04', '21:48:03', 'DESKTOP-4QEFDOG', 'Modificación'),
(168, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x edit  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: En', '2025-02-04', '21:48:22', 'DESKTOP-4QEFDOG', 'Modificación'),
(169, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: En', '2025-02-04', '21:50:20', 'DESKTOP-4QEFDOG', 'Modificación'),
(170, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: En', '2025-02-04', '21:51:19', 'DESKTOP-4QEFDOG', 'Modificación'),
(171, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: En', '2025-02-04', '21:52:04', 'DESKTOP-4QEFDOG', 'Modificación'),
(172, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x1  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-04', '21:52:41', 'DESKTOP-4QEFDOG', 'Modificación'),
(173, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x1  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-04', '21:52:48', 'DESKTOP-4QEFDOG', 'Modificación'),
(174, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x1  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-04', '22:00:41', 'DESKTOP-4QEFDOG', 'Modificación'),
(175, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-04', '22:00:55', 'DESKTOP-4QEFDOG', 'Modificación'),
(176, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x1  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x1  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-04', '22:01:08', 'DESKTOP-4QEFDOG', 'Modificación'),
(177, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x1  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x12  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: ', '2025-02-04', '22:04:42', 'DESKTOP-4QEFDOG', 'Modificación'),
(178, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x12  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x1  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-04', '22:04:49', 'DESKTOP-4QEFDOG', 'Modificación'),
(179, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x1  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x12  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: ', '2025-02-04', '22:08:26', 'DESKTOP-4QEFDOG', 'Modificación'),
(180, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x12  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x1  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-04', '22:08:32', 'DESKTOP-4QEFDOG', 'Modificación'),
(181, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x12  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x12  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: ', '2025-02-04', '22:08:43', 'DESKTOP-4QEFDOG', 'Modificación'),
(182, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x1  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: En', '2025-02-04', '22:31:34', 'DESKTOP-4QEFDOG', 'Modificación'),
(183, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x edit  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunt', '2025-02-04', '22:40:50', 'DESKTOP-4QEFDOG', 'Modificación'),
(184, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x edit  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: En', '2025-02-04', '22:43:16', 'DESKTOP-4QEFDOG', 'Modificación'),
(185, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-04', '22:44:26', 'DESKTOP-4QEFDOG', 'Modificación'),
(186, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x3  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-04', '22:46:02', 'DESKTOP-4QEFDOG', 'Modificación'),
(187, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x3  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-06', '20:02:26', 'DESKTOP-4QEFDOG', 'Modificación'),
(188, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x3  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-07', '20:16:52', 'DESKTOP-4QEFDOG', 'Modificación'),
(190, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x2  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x3  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-07', '20:22:07', 'DESKTOP-4QEFDOG', 'Modificación'),
(192, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x3  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x4  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: E', '2025-02-07', '20:22:54', 'DESKTOP-4QEFDOG', 'Modificación'),
(193, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: torneo supremos x3 edit  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Blitz 5+0 (5+0)  Lugar: aqui  Rondas: 6  Federación: FENAPAJ  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: torneo supremos x4 edit  Fecha: 2025-02-04  Hora: 10:30:00  Categoría: Ajedrez por Equipos  Formato: Seleccione un formato...  Lugar: aqui  Rondas: 6  Federación: Federación Nacional de Ajedrez de Panamá (FENAPAJ)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjun', '2025-02-07', '20:23:08', 'DESKTOP-4QEFDOG', 'Modificación'),
(201, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: ejemplo@gmail.com  Rol: 1  Estado: 1 Contraseña: Crepair10x@]', '[Correo: ejemplo@gmail.com  Rol: 3  Estado: 1 Contraseña: Crepair10x@]', '2025-02-07', '20:33:56', 'DESKTOP-4QEFDOG', 'Edición'),
(202, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: ejemplo@gmail.com  Rol: 3  Estado: 1 Contraseña: Crepair10x@]', '[Correo: ejemplo@gmail.com  Rol: 4  Estado: 1 Contraseña: Crepair10x@]', '2025-02-07', '20:34:13', 'DESKTOP-4QEFDOG', 'Edición'),
(203, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: nuevo2@gmail.com  Rol: 0  Estado: 0]', '-', '2025-02-08', '19:31:39', 'DESKTOP-4QEFDOG', 'Eliminación'),
(204, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: ejemplo@gmail.com  Rol: 0  Estado: 0]', '-', '2025-02-08', '19:36:27', 'DESKTOP-4QEFDOG', 'Eliminación'),
(205, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: usuarioprueba@gmail.com  Rol: 0  Estado: 0 Contraseña: Crepair10x@]', '-', '2025-02-08', '19:50:53', 'DESKTOP-4QEFDOG', 'Inserción'),
(206, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: usuarioprueba@gmail.com  rol_text: 0  Estado: Activo]', '-', '2025-02-08', '19:51:41', 'DESKTOP-4QEFDOG', 'Eliminación'),
(207, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: usuarioprueba@gmail.com  Rol:   Estado:  Contraseña: Crepair10x@]', '-', '2025-02-08', '19:52:25', 'DESKTOP-4QEFDOG', 'Inserción'),
(208, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: usuarioprueba@gmail.com  rol_text: 0  Estado: Activo]', '-', '2025-02-08', '19:55:48', 'DESKTOP-4QEFDOG', 'Eliminación'),
(209, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: usuarioprueba@gmail.com  Rol: admin  Estado: Activo Contraseña: Crepair10x@]', '-', '2025-02-08', '19:56:01', 'DESKTOP-4QEFDOG', 'Inserción'),
(210, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: usuarioprueba@gmail.com  rol_text: 0  Estado: Activo]', '-', '2025-02-08', '19:59:41', 'DESKTOP-4QEFDOG', 'Eliminación'),
(211, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: usuario2@gmail.com  Rol: admin  Estado: Activo Contraseña: Crepair10x@]', '-', '2025-02-08', '19:59:54', 'DESKTOP-4QEFDOG', 'Inserción'),
(212, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: usuario2@gmail.com  rol_text: 0  Estado: Activo]', '-', '2025-02-08', '20:02:50', 'DESKTOP-4QEFDOG', 'Eliminación'),
(213, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: usuario.3@gmail.com  Rol: admin  Estado: Activo Contraseña: Crepair10x@]', '-', '2025-02-08', '20:03:05', 'DESKTOP-4QEFDOG', 'Inserción'),
(214, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo:   Rol: 0  Estado: 0 Contraseña: Crepair10x@]', '[Correo: engellargaespadavargas@gmail.com  Rol: 0  Estado: 0 Contraseña: Crepair10x@]', '2025-02-08', '20:04:42', 'DESKTOP-4QEFDOG', 'Edición'),
(215, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: usuario@gmail.com  rol_text: 0  Estado: Activo]', '-', '2025-02-08', '20:05:00', 'DESKTOP-4QEFDOG', 'Eliminación'),
(216, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: usuario.3@gmail.com  rol_text: 0  Estado: Activo]', '-', '2025-02-08', '20:05:03', 'DESKTOP-4QEFDOG', 'Eliminación'),
(217, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: usuario2@gmail.com  Rol: admin  Estado: Activo Contraseña: Crepair102x@]', '-', '2025-02-08', '20:05:13', 'DESKTOP-4QEFDOG', 'Inserción'),
(218, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: usuario2@gmail.com  rol_text: 0  Estado: Activo]', '-', '2025-02-08', '20:06:08', 'DESKTOP-4QEFDOG', 'Eliminación'),
(219, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: usuario3@gmail.com  Rol: admin  Estado: Activo Contraseña: Crepair102x@]', '-', '2025-02-08', '20:06:21', 'DESKTOP-4QEFDOG', 'Inserción'),
(220, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: usuario3@gmail.com  rol_text: 0  Estado: Activo]', '-', '2025-02-08', '20:09:29', 'DESKTOP-4QEFDOG', 'Eliminación'),
(221, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: nuego@gmail.com  Rol: admin  Estado: Activo Contraseña: Crepair10x@]', '-', '2025-02-08', '20:09:45', 'DESKTOP-4QEFDOG', 'Inserción'),
(222, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: espacio123@gmail.com  Rol: admin  Estado: Activo Contraseña: 123]', '-', '2025-02-08', '20:14:28', 'DESKTOP-4QEFDOG', 'Inserción'),
(223, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: nuevo3@gmail.com  Rol: admin  Estado: Activo Contraseña: Crepair10x@]', '-', '2025-02-08', '20:22:12', 'DESKTOP-4QEFDOG', 'Inserción'),
(224, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo:   Rol: 0  Estado: 0 Contraseña: Crepair10x@]', '[Correo: nuevo3@gmail.com  Rol: 0  Estado: 0 Contraseña: Crepair10x@]', '2025-02-08', '20:22:21', 'DESKTOP-4QEFDOG', 'Edición'),
(225, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: nuevo@gmail.com  rol_text: 0  Estado: Inactivo]', '-', '2025-02-08', '20:22:33', 'DESKTOP-4QEFDOG', 'Eliminación'),
(226, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: nuego@gmail.com  rol_text: 0  Estado: Activo]', '-', '2025-02-08', '20:22:36', 'DESKTOP-4QEFDOG', 'Eliminación'),
(227, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: espacio123@gmail.com  rol_text: 0  Estado: Activo]', '-', '2025-02-08', '20:22:41', 'DESKTOP-4QEFDOG', 'Eliminación'),
(228, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo:   Rol: 0  Estado: 0 Contraseña: Crepair10x@]', '[Correo: nuevo_ediot@gmail.com  Rol: 0  Estado: 0 Contraseña: Crepair10x@]', '2025-02-08', '20:23:44', 'DESKTOP-4QEFDOG', 'Edición'),
(229, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo:   Rol: 0  Estado: 0 Contraseña: Crepair10x@]', '[Correo: nuevo_edit@gmail.com  Rol: evaluador  Estado: Activo Contraseña: Crepair10x@]', '2025-02-08', '20:26:41', 'DESKTOP-4QEFDOG', 'Edición'),
(230, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo:   Rol: 0  Estado: 0 Contraseña: Crepair10x@]', '[Correo: nuevo_edit@gmail.com  Rol: admin  Estado: Inactivo Contraseña: Crepair10x@]', '2025-02-08', '20:27:47', 'DESKTOP-4QEFDOG', 'Edición'),
(231, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo:   Rol: 0  Estado: 0 Contraseña: Crepair10x@]', '[Correo: nuevo_edit@gmail.com  Rol: admin  Estado: Inactivo Contraseña: Crepair10x@]', '2025-02-08', '20:29:05', 'DESKTOP-4QEFDOG', 'Edición'),
(232, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo:   Rol:   Estado:  Contraseña: Crepair10x@]', '[Correo: nuevo_edit@gmail.com  Rol: admin  Estado: Inactivo Contraseña: Crepair10x@]', '2025-02-08', '20:30:12', 'DESKTOP-4QEFDOG', 'Edición'),
(233, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo:   Rol:   Estado:  Contraseña: Crepair10x@]', '[Correo: nuevo_edit@gmail.com  Rol: admin  Estado: Inactivo Contraseña: Crepair10x@]', '2025-02-08', '20:32:21', 'DESKTOP-4QEFDOG', 'Edición'),
(234, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo:   Rol:   Estado:  Contraseña: Crepair10x@]', '[Correo: nuevo_edit@gmail.com  Rol: admin  Estado: Inactivo Contraseña: Crepair10x@]', '2025-02-08', '20:35:11', 'DESKTOP-4QEFDOG', 'Edición'),
(235, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: nuevo_edit@gmail.com  Rol: admin  Estado: Inactivo Contraseña: Crepair10x@]', '[Correo: nuevo_edit@gmail.com  Rol: admin  Estado: Inactivo Contraseña: Crepair10x@]', '2025-02-08', '20:36:42', 'DESKTOP-4QEFDOG', 'Edición'),
(236, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: nuevo_edit@gmail.com  Rol: admin  Estado: Inactivo Contraseña: Crepair10x@]', '[Correo: nuevo_editado@gmail.com  Rol: estudiante  Estado: Activo Contraseña: Crepair10x@]', '2025-02-08', '20:37:14', 'DESKTOP-4QEFDOG', 'Edición'),
(237, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: nuevo_editado@gmail.com  rol_text:   Estado: Activo]', '-', '2025-02-08', '20:37:33', 'DESKTOP-4QEFDOG', 'Eliminación'),
(238, 'engellargaespadavargas@gmail.com', 'Asignación de permisos', '[Permiso asignado: Eliminación al rol: estudiante]', '[-]', '2025-02-08', '20:47:02', 'DESKTOP-4QEFDOG', 'Asignación'),
(239, 'engellargaespadavargas@gmail.com', 'Asignación de permisos', '[Permiso removido: Eliminación del rol: estudiante]', '[-]', '2025-02-08', '20:48:46', 'DESKTOP-4QEFDOG', 'Remoción'),
(240, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: otro@gmail.com  Rol:   Estado:  Contraseña: 123]', '-', '2025-02-08', '20:49:23', 'DESKTOP-4QEFDOG', 'Inserción'),
(241, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: Nuevo torneo edit  Fecha: 2025-02-03  Hora: 16:30:00  Categoría: Ajedrez Rápido (Rapid)  Formato: Rápido (15+10)  Lugar: Here  Rondas: 6  Federación: FEDEMA  Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo]', '[Torneo: Nuevo torneo edit1  Fecha: 2025-02-03  Hora: 16:30:00  Categoría: Ajedrez Rápido (Rapid)  Formato: Rápido (15+10)  Lugar: Here  Rondas: 6  Federación: Federación Nacional de Ajedrez de Honduras (FEDEMA)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Engel A', '2025-02-08', '20:50:26', 'DESKTOP-4QEFDOG', 'Modificación'),
(242, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: otro@gmail.com  Rol: admin  Estado: Activo Contraseña: 123]', '[Correo: otro@gmail.com  Rol: admin  Estado: Activo Contraseña: 123]', '2025-02-08', '20:58:04', 'DESKTOP-4QEFDOG', 'Edición'),
(243, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: test@gmail.com  Rol:   Estado:  Contraseña: 123]', '-', '2025-02-08', '21:06:13', 'DESKTOP-4QEFDOG', 'Inserción'),
(244, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: otro@gmail.com  rol_text:   Estado: Activo]', '-', '2025-02-08', '21:07:09', 'DESKTOP-4QEFDOG', 'Eliminación'),
(245, 'engellargaespadavargas@gmail.com', 'Asignación de permisos', '[Permiso asignado: Eliminación al rol: estudiante]', '[-]', '2025-02-08', '21:07:23', 'DESKTOP-4QEFDOG', 'Asignación'),
(246, 'engellargaespadavargas@gmail.com', 'Asignación de permisos', '[Permiso removido: Eliminación del rol: estudiante]', '[-]', '2025-02-08', '21:07:33', 'DESKTOP-4QEFDOG', 'Remoción'),
(247, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: test@gmail.com  rol_text:   Estado: Activo]', '-', '2025-02-11', '19:10:00', 'DESKTOP-4QEFDOG', 'Eliminación'),
(248, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: nuevo@gmail.com  Rol: admin  Estado: Activo Contraseña: Crepair10x@]', '-', '2025-02-11', '19:10:12', 'DESKTOP-4QEFDOG', 'Inserción'),
(249, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: Academia de prueba  Correo:   Telefono: NULL  Director: Director de prueba  Dirección:   Ciudad: Catarina, Masaya (Nicaragua)  Estado: Activo]', '[-]', '2025-02-11', '19:11:39', 'DESKTOP-4QEFDOG', 'Inserción'),
(250, 'engellargaespadavargas@gmail.com', 'Academias', '[Academia: Academia de prueba  Correo:   Telefono: 0  Director: Director de prueba  Dirección:   Ciudad: Seleccione una ciudad...  Estado: Activo]', '[Academia: Academia de prueba  Correo:   Telefono: NULL  Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Catarina, Masaya (Nicaragua)  Estado: Inactivo]', '2025-02-11', '19:12:07', 'DESKTOP-4QEFDOG', 'Modificación'),
(251, 'nuevo@gmail.com', 'Academias', '[Academia: Academia de prueba  Correo:   Telefono: 0  Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Choose...  Estado: Inactivo]', '[Academia: Academia de prueba  Correo:   Telefono: NULL  Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Catarina, Masaya (Nicaragua)  Estado: Inactivo]', '2025-02-11', '19:13:32', 'DESKTOP-4QEFDOG', 'Modificación'),
(252, 'nuevo@gmail.com', 'Academias', '[Academia: Nuevo nombre  Correo: correo@gmail.com  Telefono: 12345678  Director: Director nombre prueba  Dirección: esta es una dirección de prueba  Ciudad: Seleccione una ciudad...  Estado: Activo]', '[Academia: Nuevo nombre  Correo: correo@gmail.com  Telefono: NULL  Director: Director nombre prueba  Dirección: esta es una dirección de prueba  Ciudad: Chichigalpa, Chinandega (Nicaragua)  Estado: Activo]', '2025-02-11', '19:13:46', 'DESKTOP-4QEFDOG', 'Modificación'),
(253, 'nuevo@gmail.com', 'Academias', '[Academia: Academia de prueba  Correo:   Telefono: 0  Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Choose...  Estado: Inactivo]', '[Academia: Academia de prueba  Correo:   Telefono: NULL  Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Catarina, Masaya (Nicaragua)  Estado: Inactivo]', '2025-02-11', '19:14:14', 'DESKTOP-4QEFDOG', 'Modificación'),
(254, 'nuevo@gmail.com', 'Academias', '[Academia: Academia de prueba  Correo:   Telefono: 0  Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Seleccione una ciudad...  Estado: Inactivo]', '[Academia: Academia de prueba  Correo:   Telefono: NULL  Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Catarina, Masaya (Nicaragua)  Estado: Inactivo]', '2025-02-11', '19:14:47', 'DESKTOP-4QEFDOG', 'Modificación'),
(255, 'nuevo@gmail.com', 'Academias', '[Academia: Academia de prueba  Correo:   Telefono: 0  Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Choose...  Estado: Inactivo]', '[Academia: Academia de prueba  Correo:   Telefono: NULL  Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Catarina, Masaya (Nicaragua)  Estado: Inactivo]', '2025-02-11', '19:16:02', 'DESKTOP-4QEFDOG', 'Modificación'),
(256, 'nuevo@gmail.com', 'Academias', '[Academia: Academia de prueba  Correo:   Telefono: 0  Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Choose...  Estado: Inactivo]', '[Academia: Academia de prueba  Correo:   Telefono:   Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Catarina, Masaya (Nicaragua)  Estado: Inactivo]', '2025-02-11', '19:19:09', 'DESKTOP-4QEFDOG', 'Modificación'),
(257, 'nuevo@gmail.com', 'Academias', '[Academia: Academia de prueba  Correo:   Telefono:   Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Seleccione una ciudad...  Estado: Inactivo]', '[Academia: Academia de prueba  Correo:   Telefono:   Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Catarina, Masaya (Nicaragua)  Estado: Inactivo]', '2025-02-11', '19:20:02', 'DESKTOP-4QEFDOG', 'Modificación'),
(258, 'nuevo@gmail.com', 'Academias', '[Academia: Academia de prueba  Correo:   Telefono:   Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Choose...  Estado: Inactivo]', '[Academia: Academia de prueba  Correo:   Telefono: 12345678  Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Catarina, Masaya (Nicaragua)  Estado: Inactivo]', '2025-02-11', '19:22:08', 'DESKTOP-4QEFDOG', 'Modificación'),
(259, 'nuevo@gmail.com', 'Academias', '[Academia: con numero  Correo:   Telefono: 45789632  Director: un pelao  Dirección:   Ciudad: Bejuco, Granada (Nicaragua)  Estado: Activo]', '[-]', '2025-02-11', '19:24:11', 'DESKTOP-4QEFDOG', 'Inserción'),
(260, 'nuevo@gmail.com', 'Academias', '[Academia: con numero  Correo:   Telefono: 45789632  Director: un pelao  Dirección:   Ciudad: Seleccione una ciudad...  Estado: Activo]', '[Academia: con numero  Correo: uncorreo@gmail.com  Telefono: 45789632  Director: un pelao  Dirección:   Ciudad: Bejuco, Granada (Nicaragua)  Estado: Activo]', '2025-02-11', '19:24:23', 'DESKTOP-4QEFDOG', 'Modificación'),
(261, 'nuevo@gmail.com', 'Academias', '[Academia: Academia de prueba  Correo:   Telefono: 12345678  Director: Director de prueba  Dirección: direccion de prueba  Ciudad: Catarina, Masaya (Nicaragua)  Estado: Inactivo]', '[-]', '2025-02-11', '19:24:31', 'DESKTOP-4QEFDOG', 'Eliminación'),
(262, 'nuevo@gmail.com', 'Academias', '[Academia: con numero  Correo: uncorreo@gmail.com  Telefono: 45789632  Director: un pelao  Dirección:   Ciudad: Bejuco, Granada (Nicaragua)  Estado: Activo]', '[-]', '2025-02-11', '19:24:36', 'DESKTOP-4QEFDOG', 'Eliminación'),
(263, 'engellargaespadavargas@gmail.com', 'Paises', '[País ingresado: Como prueba, desde el formulario de ciudades]', '-', '2025-02-11', '20:07:30', 'DESKTOP-4QEFDOG', 'Inserción');
INSERT INTO `auditorias` (`id`, `correo_id`, `tabla_afectada`, `valor_previo`, `valor_posterior`, `fecha`, `hora`, `equipo`, `accion`) VALUES
(264, 'engellargaespadavargas@gmail.com', 'Departamentos', '[Departamento ingresado: Como departamento en el país: Como prueba, desde el formulario de ciudades]', '-', '2025-02-11', '20:07:48', 'DESKTOP-4QEFDOG', 'Inserción'),
(265, 'engellargaespadavargas@gmail.com', 'Ciudades', '[Ciudad ingresada: Como ciudad en el departamento: Como departamento del país: Como prueba, desde el formulario de ciudades]', '-', '2025-02-11', '20:08:03', 'DESKTOP-4QEFDOG', 'Inserción'),
(266, 'nuevo@gmail.com', 'Ciudades', '[Ciudad ingresada: Como ciudad 2 en el departamento: Como departamento del país: Como prueba, desde el formulario de ciudades]', '-', '2025-02-11', '20:09:41', 'DESKTOP-4QEFDOG', 'Inserción'),
(267, 'nuevo@gmail.com', 'Ciudades', '[Ciudad ingresada: Como ciudad 3 en el departamento: Como departamento del país: Como prueba, desde el formulario de ciudades]', '-', '2025-02-11', '20:09:55', 'DESKTOP-4QEFDOG', 'Inserción'),
(268, 'nuevo@gmail.com', 'Ciudades', '[Ciudad eliminada: Como ciudad, correspondiente al departamento: Como departamento, país: Como prueba,  desde el formulario de ciudades]', '[-]', '2025-02-11', '20:10:06', 'DESKTOP-4QEFDOG', 'Eliminación'),
(269, 'nuevo@gmail.com', 'Departamentos', '[Departamento eliminado: Como departamento, correspondiente al país: Como prueba desde el formulario de ciudades]', '[-]', '2025-02-11', '20:10:14', 'DESKTOP-4QEFDOG', 'Eliminación'),
(270, 'nuevo@gmail.com', 'País', '[País eliminado: Como prueba desde el formulario de ciudades]', '[-]', '2025-02-11', '20:12:08', 'DESKTOP-4QEFDOG', 'Eliminación'),
(271, 'engellargaespadavargas@gmail.com', 'Federaciones', '[Acronimo: Nuevo  Federacion: NUEVOOOO  Pais: Honduras Estado: Activo]', '-', '2025-02-11', '20:48:13', 'DESKTOP-4QEFDOG', 'Inserción'),
(272, 'engellargaespadavargas@gmail.com', 'Federaciones', '[Acronimo: FECA  Federacion: Federación Capos del Ajedrez  Pais: Nicaragua Estado: Activo]', '-', '2025-02-11', '20:50:54', 'DESKTOP-4QEFDOG', 'Inserción'),
(273, 'engellargaespadavargas@gmail.com', 'Federaciones', '[Acronimo: FECA  Federacion: Activo  Pais: Nicaragua Estado: Activo]', '[Acronimo: FECA  Federacion: Federación +Capos del Ajedrez  Pais: Nicaragua Estado: Activo]', '2025-02-11', '20:51:11', 'DESKTOP-4QEFDOG', 'Edición'),
(274, 'engellargaespadavargas@gmail.com', 'Federaciones', '[Acronimo: Nuevo  Federacion: Activo  Pais: Honduras Estado: Activo]', '[Acronimo: Nuevo  Federacion: NUEVO  Pais: Honduras Estado: Activo]', '2025-02-11', '20:55:01', 'DESKTOP-4QEFDOG', 'Edición'),
(275, 'engellargaespadavargas@gmail.com', 'Federaciones', '[Acronimo: Nuevo  Federacion: Activo  Pais: Honduras Estado: Activo]', '[Acronimo: Nuevo  Federacion: NUEVO  Pais: Honduras Estado: Activo]', '2025-02-11', '20:55:10', 'DESKTOP-4QEFDOG', 'Edición'),
(276, 'engellargaespadavargas@gmail.com', 'Federaciones', '[Acronimo: Nuevo  Federacion: Activo  Pais: Honduras Estado: Activo]', '[Acronimo: Nuevo  Federacion: NUEV  Pais: Honduras Estado: Activo]', '2025-02-11', '20:57:11', 'DESKTOP-4QEFDOG', 'Edición'),
(277, 'engellargaespadavargas@gmail.com', 'Federaciones', '[Acronimo: Nuevo  Federacion: Activo  Pais: Honduras Estado: Activo]', '[Acronimo: Nuevo  Federacion: NUEVo  Pais: Honduras Estado: Activo]', '2025-02-11', '20:57:21', 'DESKTOP-4QEFDOG', 'Edición'),
(278, 'nuevo@gmail.com', 'FIDES', '[FIDE-ID: 20190328  Ajedrecista: Engel Antonio Largaespada Vargas (001-030802-1004G)  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Título: Candidato a Maestro (CM)  ELO Blitz: 500  ELO Clásico: 1000  ELO rápido: 1800]', '[FIDE-ID: 20190328  Ajedrecista: Engel Antonio Largaespada Vargas (001-030802-1004G)  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Título: Candidato a Maestro (CM)  ELO Blitz: 700  ELO Clásico: 1000  ELO rápido: 1800]', '2025-02-12', '19:36:05', 'DESKTOP-4QEFDOG', 'Modificación'),
(279, 'nuevo@gmail.com', 'FIDES', '[FIDE-ID: 20190328  Ajedrecista: Engel Antonio Largaespada Vargas (001-030802-1004G)  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Título: Candidato a Maestro (CM)  ELO Blitz: 700  ELO Clásico: 1000  ELO rápido: 1800]', '[FIDE-ID: 20190328  Ajedrecista: Engel Antonio Largaespada Vargas (001-030802-1004G)  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Título: Candidato a Maestro (CM)  ELO Blitz: 750  ELO Clásico: 1000  ELO rápido: 1800]', '2025-02-12', '19:37:04', 'DESKTOP-4QEFDOG', 'Modificación'),
(280, 'engellargaespadavargas@gmail.com', 'FIDES', '[FIDE-ID: 20190328  Ajedrecista: Engel Antonio Largaespada Vargas (001-030802-1004G)  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Título: Candidato a Maestro (CM)  ELO Blitz: 700  ELO Clásico: 1000  ELO rápido: 1800]', '[FIDE-ID: 20190328  Ajedrecista: Engel Antonio Largaespada Vargas (001-030802-1004G)  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Título: Candidato a Maestro (CM)  ELO Blitz: 725  ELO Clásico: 1000  ELO rápido: 1800]', '2025-02-12', '19:37:53', 'DESKTOP-4QEFDOG', 'Modificación'),
(281, 'nuevo@gmail.com', 'FIDES', '[FIDE-ID: 20190328  Ajedrecista: Engel Antonio Largaespada Vargas (001-030802-1004G)  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Título: Candidato a Maestro (CM)  ELO Blitz: 725  ELO Clásico: 1000  ELO rápido: 1800]', '[FIDE-ID: 20190328  Ajedrecista: Engel Antonio Largaespada Vargas (001-030802-1004G)  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Título: Candidato a Maestro (CM)  ELO Blitz: 735  ELO Clásico: 1000  ELO rápido: 1800]', '2025-02-12', '19:39:03', 'DESKTOP-4QEFDOG', 'Modificación'),
(282, 'nuevo@gmail.com', 'FIDES', '[FIDE-ID: 20190328  Ajedrecista: Engel Antonio Largaespada Vargas (001-030802-1004G)  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Título: Candidato a Maestro (CM)  ELO Blitz: 735  ELO Clásico: 1000  ELO rápido: 1800]', '[FIDE-ID: 20190328  Ajedrecista: Engel Antonio Largaespada Vargas (001-030802-1004G)  Federación: Federación Nacional de Ajedrez de Nicaragua (FENAMAC)  Título: Candidato a Maestro (CM)  ELO Blitz: 745  ELO Clásico: 1000  ELO rápido: 1800]', '2025-02-12', '19:40:01', 'DESKTOP-4QEFDOG', 'Modificación'),
(283, 'engellargaespadavargas@gmail.com', 'Miembros', '[Identificación: 028-123001-4258W  Nombres: Ernesto José  Apellidos: Martínez Avilés  Sexo: Masculino  Fecha de nacimiento: 2001-12-30  Teléfono: 89756412  Fecha de inscripción: 2025-01-30  Club:   Correo asignado:   Ciudad: Chichigalpa, León (Nicaragua)  Academia: Nuevo nombre  Estado: Inactivo]', '[Identificación: 028-123001-4258W  Nombres: Ernesto José  Apellidos: Martínez Avilés  Sexo: Masculino  Fecha de nacimiento: 2001-12-30  Teléfono: 89756412  Fecha de inscripción: 2025-01-30  Club:   Correo asignado: docente.dominical@gmail.com  Ciudad: Chichigalpa, León (Nicaragua)  Academia: Nuevo nombre  Estado: Inactivo]', '2025-02-12', '20:41:43', 'DESKTOP-4QEFDOG', 'Modificación'),
(284, 'nuevo@gmail.com', 'Miembros', '[Identificación: 123-new  Nombres: Desde  Apellidos: El Celular  Sexo: Masculino  Fecha de nacimiento: 2025-02-12  Teléfono: 12345678  Fecha de inscripción: 2025-02-12  Club:   Correo asignado: recepcion@gmail.com  Ciudad: Comalapa, Chontales (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[-]', '2025-02-12', '20:52:35', 'DESKTOP-4QEFDOG', 'Inserción'),
(285, 'nuevo@gmail.com', 'Miembros', '[Identificación: nmienrbo  Nombres: a  Apellidos: a  Sexo: Masculino  Fecha de nacimiento:   Teléfono: 1234  Fecha de inscripción:   Club:   Correo asignado: nuevo@gmail.com  Ciudad: Camoapa, Boaco (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[-]', '2025-02-12', '20:56:00', 'DESKTOP-4QEFDOG', 'Inserción'),
(286, 'nuevo@gmail.com', 'Miembros', '[Identificación: 133-new  Nombres: a  Apellidos: a  Sexo: Masculino  Fecha de nacimiento:   Teléfono: 123  Fecha de inscripción:   Club:   Correo asignado:   Ciudad: Boaco, Boaco (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[-]', '2025-02-12', '21:01:37', 'DESKTOP-4QEFDOG', 'Inserción'),
(287, 'engellargaespadavargas@gmail.com', 'Miembros', '[Identificación: 133-new  Nombres: a  Apellidos: a  Sexo: Masculino  Fecha de nacimiento:   Teléfono: 123  Fecha de inscripción:   Club:   Correo asignado:   Ciudad: Boaco, Boaco (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[Identificación: 133-new  Nombres: a  Apellidos: a  Sexo: Masculino  Fecha de nacimiento:   Teléfono: 123  Fecha de inscripción:   Club:   Correo asignado: nuevo@gmail.com  Ciudad: Boaco, Boaco (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '2025-02-12', '21:30:26', 'DESKTOP-4QEFDOG', 'Modificación'),
(288, 'engellargaespadavargas@gmail.com', 'Miembros', '[Identificación: 003-042503-2569Q  Nombres: Adriana Sofia  Apellidos: Chamorro Zamora  Sexo: Femenino  Fecha de nacimiento: 2003-06-06  Teléfono: 54123698  Fecha de inscripción: 2025-01-30  Club:   Correo asignado:   Ciudad: Ciudad Sandino, Managua (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[Identificación: 003-042503-2569Q  Nombres: Adriana Sofia  Apellidos: Chamorro Zamora  Sexo: Femenino  Fecha de nacimiento: 2003-06-06  Teléfono: 54123698  Fecha de inscripción: 2025-01-30  Club:   Correo asignado:   Ciudad: Ciudad Sandino, Managua (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '2025-02-12', '21:31:43', 'DESKTOP-4QEFDOG', 'Modificación'),
(289, 'nuevo@gmail.com', 'Miembros', '[Identificación: 133-new  Nombres: a  Apellidos: a  Sexo: Masculino  Fecha de nacimiento:   Teléfono: 123  Fecha de inscripción:   Club:   Correo asignado:   Ciudad: Boaco, Boaco (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[Identificación: 133-new  Nombres: Andrea  Apellidos: Morales  Sexo: Masculino  Fecha de nacimiento:   Teléfono: 123  Fecha de inscripción:   Club:   Correo asignado: nuevo@gmail.com  Ciudad: Boaco, Boaco (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '2025-02-12', '21:32:13', 'DESKTOP-4QEFDOG', 'Modificación'),
(290, 'nuevo@gmail.com', 'Miembros', '[Identificación: 133-new  Nombres: Andrea  Apellidos: Morales  Sexo: Masculino  Fecha de nacimiento: 00-00-0000  Teléfono: 123  Fecha de inscripción: 00-00-0000  Club:   Correo asignado: nuevo@gmail.com  Ciudad: Boaco, Boaco (Nicaragua)  Academia: Nuevo nombre  Estado: Activo]', '[-]', '2025-02-12', '21:32:55', 'DESKTOP-4QEFDOG', 'Eliminación'),
(291, NULL, 'Participantes/Inscripciones', '[Participante: Adriana Sofia Chamorro Zamora (003-042503-2569Q) inscrito en el torneo: Nuevo torneo edit1 (febrero 03, 2025)]', '-', '2025-02-15', '20:53:05', 'DESKTOP-4QEFDOG', 'Inserción'),
(292, 'engellargaespadavargas@gmail.com', 'Participantes/Inscripciones', '[Participante: Adriana Sofia Chamorro Zamora (003-042503-2569Q) inscrito en el torneo: Nuevo torneo edit1 (febrero 03, 2025)]', '-', '2025-02-15', '20:54:06', 'DESKTOP-4QEFDOG', 'Inserción'),
(293, 'engellargaespadavargas@gmail.com', 'Participantes/Inscripciones', '[Participante: Adriana Sofia Chamorro Zamora (003-042503-2569Q) inscrito en el torneo: Nuevo torneo edit1 (febrero 03, 2025)]', '[Participante: Adriana Sofia Chamorro Zamora (003-042503-2569Q) inscrito en el torneo: Nuevo torneo edit1 (febrero 03, 2025)]', '2025-02-15', '21:10:29', 'DESKTOP-4QEFDOG', 'Edición'),
(294, 'engellargaespadavargas@gmail.com', 'Participantes/Inscripciones', '[Participante: Adriana Sofia Chamorro Zamora (003-042503-2569Q) inscrito en el torneo: Nuevo torneo edit1 (febrero 03, 2025)]', '[Participante: Engel Antonio Largaespada Vargas (001-030802-1004G) inscrito en el torneo: Nuevo torneo edit1 (febrero 03, 2025)]', '2025-02-15', '21:11:04', 'DESKTOP-4QEFDOG', 'Edición'),
(295, 'engellargaespadavargas@gmail.com', 'Participantes/Inscripciones', '[Participante: Engel Antonio Largaespada Vargas removido del torneo: Nuevo torneo edit1 (Febrero 03, 2025)]', '-', '2025-02-15', '21:38:50', 'DESKTOP-4QEFDOG', 'Eliminación'),
(296, 'engellargaespadavargas@gmail.com', 'Participantes/Inscripciones', '[Participante: Engel Antonio Largaespada Vargas removido del torneo: Nuevo torneo edit1 (Febrero 03, 2025)]', '-', '2025-02-15', '21:39:09', 'DESKTOP-4QEFDOG', 'Eliminación'),
(297, 'engellargaespadavargas@gmail.com', 'Participantes/Inscripciones', '[Participante: Engel Antonio Largaespada Vargas removido del torneo: Nuevo torneo edit1 (Febrero 03, 2025)]', '-', '2025-02-15', '21:40:03', 'DESKTOP-4QEFDOG', 'Eliminación'),
(298, 'engellargaespadavargas@gmail.com', 'Participantes/Inscripciones', '[Participante: Adriana Sofia Chamorro Zamora removido del torneo: torneo para celular (Febrero 03, 2025)]', '-', '2025-02-15', '21:42:06', 'DESKTOP-4QEFDOG', 'Eliminación'),
(299, 'engellargaespadavargas@gmail.com', 'Participantes/Inscripciones', '[Participante: Adriana Sofia Chamorro Zamora (003-042503-2569Q) inscrito en el torneo: torneo para celular (febrero 03, 2025)]', '-', '2025-02-15', '21:46:48', 'DESKTOP-4QEFDOG', 'Inserción'),
(300, 'engellargaespadavargas@gmail.com', 'Participantes/Inscripciones', '[Participante: Adriana Sofia Chamorro Zamora (003-042503-2569Q) inscrito en el torneo: torneo para celular (febrero 03, 2025)]', '[Participante: Adriana Sofia Chamorro Zamora (003-042503-2569Q) inscrito en el torneo: Nuevo torneo edit1 (febrero 03, 2025)]', '2025-02-15', '21:46:59', 'DESKTOP-4QEFDOG', 'Edición'),
(301, 'engellargaespadavargas@gmail.com', 'Participantes/Inscripciones', '[Participante: Adriana Sofia Chamorro Zamora removido del torneo: Nuevo torneo edit1 (Febrero 03, 2025)]', '-', '2025-02-15', '21:47:11', 'DESKTOP-4QEFDOG', 'Eliminación'),
(302, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: Torneo emparejado  Fecha: 2025-02-16  Hora: 16:45  Categoría: Ajedrez Clásico  Formato: Clásico (90+30)  Lugar: Aqui  Rondas: 7  Federación: Federación Nacional de Ajedrez de Jamaica (FAJUS)  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Ernesto José Martínez Avilés (028-123001-4258W)  Árbitro principal: Ernesto José Martínez Avilés (028-123001-4258W)  Árbitro adjunto: Ernesto José Martínez Avilé', '[-]', '2025-02-16', '16:49:59', 'DESKTOP-4QEFDOG', 'Inserción'),
(303, 'engellargaespadavargas@gmail.com', 'Torneos', NULL, '[-]', '2025-02-16', '16:54:22', 'DESKTOP-4QEFDOG', 'Eliminación'),
(304, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: Torneo emparejado  Fecha: 2025-02-16  Hora: 17:00  Categoría: Ajedrez Clásico  Formato: Seleccione un formato...  Lugar: Aqui  Rondas: 7  Federación: Seleccione una federación...  Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Ernesto José Martínez Avilés (028-123001-4258W)  Árbitro principal: Ernesto José Martínez Avilés (028-123001-4258W)  Árbitro adjunto: Engel Antonio Largaespada Vargas (001-030802-1004G)  Estado: Activo Sistema de emparejamiento: Sistema Suizo]', '[-]', '2025-02-16', '16:54:54', 'DESKTOP-4QEFDOG', 'Inserción'),
(305, 'engellargaespadavargas@gmail.com', 'Torneos', NULL, '[-]', '2025-02-16', '16:59:24', 'DESKTOP-4QEFDOG', 'Eliminación'),
(306, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: Torneo emparejado  Fecha: 2025-02-16  Hora: 17:00  Categoría: Ajedrez Clásico  Formato:   Lugar: Aqui  Rondas: 7  Federación:   Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Ernesto José Martínez Avilés (028-123001-4258W)  Árbitro adjunto: Ernesto José Martínez Avilés (028-123001-4258W)  Estado: Activo Sistema de emparejamiento: Sistema Suizo]', '[-]', '2025-02-16', '17:00:19', 'DESKTOP-4QEFDOG', 'Inserción'),
(307, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: Torneo emparejado  Fecha: 2025-02-16  Hora: 17:00:00  Categoría: Ajedrez Clásico  Formato:   Lugar: Aqui  Rondas: 7  Federación:   Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Ernesto José Martínez Avilés  Árbitro adjunto: Ernesto José Martínez Avilés  Estado: Activo  Sistema de emparejamiento: Sistema Suizo]', '[Torneo: Torneo emparejado  Fecha: 2025-02-16  Hora: 17:00:00  Categoría: Ajedrez Clásico  Formato: -  Lugar: Aqui  Rondas: 7  Federación:   Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Engel Antonio Largaespada Vargas (001-030802-1004G)  Estado: Activo  Sistema de emparejamiento: ]', '2025-02-16', '17:09:21', 'DESKTOP-4QEFDOG', 'Modificación'),
(308, 'engellargaespadavargas@gmail.com', 'Torneos', '[Torneo: Torneo emparejado  Fecha: 2025-02-16  Hora: 17:00:00  Categoría: Ajedrez Clásico  Formato:   Lugar: Aqui  Rondas: 7  Federación:   Organizador: Engel Antonio Largaespada Vargas  Director: Engel Antonio Largaespada Vargas  Árbitro: Engel Antonio Largaespada Vargas Árbitro principal: Engel Antonio Largaespada Vargas  Árbitro adjunto: Engel Antonio Largaespada Vargas  Estado: Activo  Sistema de emparejamiento: Sistema Suizo]', '[Torneo: Torneo emparejado  Fecha: 2025-02-16  Hora: 17:00:00  Categoría: Ajedrez Clásico  Formato: -  Lugar: Aqui  Rondas: 7  Federación:   Organizador: Engel Antonio Largaespada Vargas (001-030802-1004G)  Director: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro principal: Engel Antonio Largaespada Vargas (001-030802-1004G)  Árbitro adjunto: Engel Antonio Largaespada Vargas (001-030802-1004G)  Estado: Activo  Sistema de emparejamiento: Sistema Suizo]', '2025-02-16', '17:11:25', 'DESKTOP-4QEFDOG', 'Modificación'),
(309, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: engellargaespadavargas@gmail.com  Rol: admin  Estado: Activo Contraseña: 8d24627eb9901d67]', '[Correo: engellargaespadavargas@gmail.com  Rol: admin  Estado: Activo Contraseña: 8d24627eb9901d67]', '2025-02-20', '21:28:39', 'DESKTOP-4QEFDOG', 'Edición'),
(310, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: engellargaespadavargas@gmail.com  Rol: admin  Estado: Activo Contraseña: 0ddb18f0b79e6b98]', '[Correo: engellargaespadavargas@gmail.com  Rol: admin  Estado: Activo Contraseña: 0ddb18f0b79e6b98]', '2025-02-22', '21:44:07', 'DESKTOP-4QEFDOG', 'Edición'),
(311, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: engellargaespadavargas@gmail.com  Rol: admin  Estado: Activo Contraseña: ]', '[Correo: engellargaespadavargas@gmail.com  Rol: admin  Estado: Activo Contraseña: ]', '2025-02-23', '17:42:24', 'DESKTOP-4QEFDOG', 'Edición'),
(312, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: testeando@gmail.com  Rol: admin  Estado: Activo Contraseña: Crepair10x@]', '-', '2025-02-23', '17:44:27', 'DESKTOP-4QEFDOG', 'Inserción'),
(313, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: testeando@gmail.com  rol_text:   Estado: Activo]', '-', '2025-02-23', '17:45:33', 'DESKTOP-4QEFDOG', 'Eliminación'),
(314, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: testeando@gmail.com  Rol: admin  Estado: Activo Contraseña: Crepair10x@]', '-', '2025-02-23', '17:46:02', 'DESKTOP-4QEFDOG', 'Inserción'),
(315, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: testeando@gmail.com  Rol: admin  Estado: Activo Contraseña: ]', '[Correo: testeando@gmail.com  Rol: estudiante  Estado: Inactivo Contraseña: ]', '2025-02-23', '17:46:10', 'DESKTOP-4QEFDOG', 'Edición'),
(316, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: testeando@gmail.com  Rol: estudiante  Estado: Inactivo]', '[Correo: testeando@gmail.com  Rol: estudiante  Estado: Inactivo Nueva contraseña: ]', '2025-02-23', '19:40:16', 'DESKTOP-4QEFDOG', 'Edición'),
(317, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: testeando@gmail.com  Rol: estudiante  Estado: Inactivo]', '[Correo: testeando@gmail.com  Rol: estudiante  Estado: Inactivo Nueva contraseña: ]', '2025-02-23', '19:41:09', 'DESKTOP-4QEFDOG', 'Edición'),
(318, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: testeando@gmail.com  Rol: estudiante  Estado: Inactivo]', '[Correo: testeando@gmail.com  Rol: estudiante  Estado: Inactivo Nueva contraseña: ]', '2025-02-23', '19:42:29', 'DESKTOP-4QEFDOG', 'Edición'),
(319, 'engellargaespadavargas@gmail.com', 'Usuarios', '[Correo: testeando@gmail.com  Rol: estudiante  Estado: Inactivo]', '[Correo: testeando@gmail.com  Rol: estudiante  Estado: Inactivo Nueva contraseña: 123]', '2025-02-23', '19:42:55', 'DESKTOP-4QEFDOG', 'Edición'),
(320, 'testeando@gmail.com', 'Usuarios', '[Correo: testeando@gmail.com  Rol: estudiante  Estado: Inactivo]', '[Correo: testeando@gmail.com  Rol: estudiante  Estado: Inactivo Nueva contraseña: 456]', '2025-02-23', '19:43:37', 'DESKTOP-4QEFDOG', 'Edición');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_torneo`
--

CREATE TABLE `categorias_torneo` (
  `id_torneo_categoria` int(11) NOT NULL,
  `categoria_torneo` varchar(50) NOT NULL,
  `descrip_categoria_torneo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `categorias_torneo`
--

INSERT INTO `categorias_torneo` (`id_torneo_categoria`, `categoria_torneo`, `descrip_categoria_torneo`) VALUES
(1, 'Ajedrez Clásico', 'Modalidad tradicional con tiempos de reflexión largos (más de 60 minutos).'),
(2, 'Ajedrez Rápido (Rapid)', 'Partidas con tiempos de reflexión entre 10 y 60 minutos por jugador.'),
(3, 'Ajedrez Relámpago (Blitz)', 'Partidas rápidas con tiempos de reflexión menores a 10 minutos por jugador.'),
(4, 'Ajedrez por Equipos', 'Competiciones donde grupos de jugadores representan a un equipo o país.'),
(5, 'Ajedrez Infantil/Juvenil', 'Categorías dedicadas a jugadores jóvenes o en formación.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudades`
--

CREATE TABLE `ciudades` (
  `id_ciudad` int(11) NOT NULL,
  `nombre_ciudad` varchar(50) NOT NULL,
  `depto_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `ciudades`
--

INSERT INTO `ciudades` (`id_ciudad`, `nombre_ciudad`, `depto_id`) VALUES
(22, 'Acoyapa', 4),
(70, 'Altagracia', 13),
(31, 'Bejuco', 6),
(69, 'Belén', 13),
(1, 'Boaco', 1),
(80, 'Bonanza', 15),
(2, 'Camoapa', 1),
(56, 'Catarina', 11),
(13, 'Chichigalpa', 3),
(40, 'Chichigalpa', 8),
(11, 'Chinandega', 3),
(84, 'ciudad', NULL),
(51, 'Ciudad Sandino', 10),
(81, 'Ciudad1', NULL),
(82, 'Ciudad2', NULL),
(19, 'Comalapa', 4),
(86, 'Como ciudad 2', NULL),
(87, 'Como ciudad 3', NULL),
(24, 'Condega', 5),
(14, 'Corinto', 3),
(30, 'Diriá', 6),
(7, 'Diriamba', 2),
(21, 'El Ayote', 4),
(72, 'El Castillo', 14),
(35, 'El Cuá', 7),
(41, 'El Sauce', 8),
(63, 'El Tuma-La Dalia', 12),
(12, 'El Viejo', 3),
(23, 'Estelí', 5),
(62, 'Estelí', 12),
(28, 'Granada', 6),
(33, 'Jinotega', 7),
(6, 'Jinotepe', 2),
(17, 'Juigalpa', 4),
(58, 'La Concepción', 11),
(34, 'La Dalia', 7),
(60, 'La Dalia', 12),
(20, 'La Libertad', 4),
(73, 'La Libertad', 14),
(8, 'La Paz', 2),
(42, 'La Paz Centro', 8),
(25, 'La Trinidad', 5),
(39, 'León', 8),
(49, 'Managua', 10),
(53, 'Masaya', 11),
(59, 'Matagalpa', 12),
(74, 'Morrito', 14),
(43, 'Nagarote', 8),
(29, 'Nandaime', 6),
(54, 'Nindirí', 11),
(15, 'Posoltega', 3),
(68, 'Potosí', 13),
(26, 'Pueblo Nuevo', 5),
(64, 'Rancho Grande', 12),
(65, 'Rivas', 13),
(78, 'Rosita', 15),
(71, 'San Carlos', 14),
(66, 'San Jorge', 13),
(3, 'San José de los Remates', 1),
(32, 'San Juan de Oriente', 6),
(57, 'San Juan de Oriente', 11),
(46, 'San Juan de Río Coco', 9),
(75, 'San Juan del Norte', 14),
(47, 'San Lucas', 9),
(9, 'San Marcos', 2),
(27, 'San Nicolás', 5),
(37, 'San Rafael del Norte', 7),
(52, 'San Rafael del Sur', 10),
(61, 'San Ramón', 12),
(36, 'San Sebastián de Yalí', 7),
(4, 'Santa Lucia', 1),
(10, 'Santa Teresa', 2),
(79, 'Santo Domingo', 15),
(18, 'Santo Tomás', 4),
(77, 'Siuna', 15),
(45, 'Somoto', 9),
(44, 'Telica', 8),
(5, 'Teustepe', 1),
(55, 'Ticuantepe', 11),
(50, 'Tipitapa', 10),
(67, 'Tola', 13),
(16, 'Villa El Carmen', 3),
(38, 'Wiwilí', 7),
(48, 'Yalagüina', 9),
(76, 'Zelaya Central', 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `controles_tiempo`
--

CREATE TABLE `controles_tiempo` (
  `id_control_tiempo` int(11) NOT NULL,
  `formato` varchar(12) NOT NULL,
  `control_tiempo` varchar(15) NOT NULL,
  `descrip_control_tiempo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `controles_tiempo`
--

INSERT INTO `controles_tiempo` (`id_control_tiempo`, `formato`, `control_tiempo`, `descrip_control_tiempo`) VALUES
(1, '90+30', 'Clásico', '90 minutos iniciales + 30 minutos después del movimiento 40.'),
(2, '15+10', 'Rápido', '15 minutos iniciales + 10 segundos por movimiento.'),
(3, '3+2', 'Blitz 3+2', '3 minutos iniciales + 2 segundos por movimiento.'),
(4, '5+0', 'Blitz 5+0', '5 minutos iniciales sin incremento.'),
(5, '5+3', 'Blitz 5+3', '5 minutos iniciales + 3 segundos por movimiento.'),
(6, '2+1', 'Blitz 2+1', '2 minutos iniciales + 1 segundo por movimiento.'),
(7, '40/90, 20/30', 'Por Movimiento', '90 minutos para 40 movimientos + 30 minutos para 20.'),
(8, '5+3', 'Fischer', '5 minutos iniciales + 3 segundos por movimiento.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `control_tiempo_torneos`
--

CREATE TABLE `control_tiempo_torneos` (
  `control_tiempo_id` int(11) NOT NULL,
  `categorias_torneo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `control_tiempo_torneos`
--

INSERT INTO `control_tiempo_torneos` (`control_tiempo_id`, `categorias_torneo_id`) VALUES
(1, 1),
(1, 4),
(2, 2),
(2, 4),
(2, 5),
(3, 3),
(3, 5),
(4, 3),
(5, 3),
(6, 3),
(7, 1),
(8, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `id_depto` int(11) NOT NULL,
  `nombre_depto` varchar(50) DEFAULT NULL,
  `pais_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`id_depto`, `nombre_depto`, `pais_id`) VALUES
(17, 'Alajuela', 3),
(1, 'Boaco', 4),
(2, 'Carazo', 4),
(18, 'Cartago', 3),
(3, 'Chinandega', 4),
(4, 'Chontales', 4),
(5, 'Estelí', 4),
(6, 'Granada', 4),
(20, 'Guanacaste', 3),
(19, 'Heredia', 3),
(7, 'Jinotega', 4),
(8, 'León', 4),
(22, 'Limón', 3),
(9, 'Madriz', 4),
(10, 'Managua', 4),
(11, 'Masaya', 4),
(12, 'Matagalpa', 4),
(27, 'Prueba v1', 5),
(21, 'Puntarenas', 3),
(14, 'Rio San Juan', 4),
(13, 'Rivas', 4),
(16, 'San José', 3),
(15, 'Zelaya Central', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `desempates_torneo`
--

CREATE TABLE `desempates_torneo` (
  `categoria_torneo_id` int(11) NOT NULL,
  `sistema_desempate_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `elo_categorias`
--

CREATE TABLE `elo_categorias` (
  `no_elo` int(11) NOT NULL,
  `categoria_elo` varchar(10) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `elo_categorias_estado` bit(1) DEFAULT b'1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `elo_categorias`
--

INSERT INTO `elo_categorias` (`no_elo`, `categoria_elo`, `descripcion`, `elo_categorias_estado`) VALUES
(1, 'Clásico', 'Puntaje ELO en torneos Clásicos', b'1'),
(2, 'Rápido', 'Puntaje ELO en torneos Rápidos', b'1'),
(3, 'Blitz', 'Puntaje ELO en torneos Blitz', b'1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `federaciones`
--

CREATE TABLE `federaciones` (
  `acronimo` varchar(10) NOT NULL,
  `nombre_federacion` varchar(50) DEFAULT NULL,
  `pais_id` int(11) DEFAULT NULL,
  `federacion_estado` bit(1) DEFAULT b'1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `federaciones`
--

INSERT INTO `federaciones` (`acronimo`, `nombre_federacion`, `pais_id`, `federacion_estado`) VALUES
('FAJUS', 'Federación Nacional de Ajedrez de Jamaica', 7, b'1'),
('FEDEMA', 'Federación Nacional de Ajedrez de Honduras', 5, b'1'),
('FEMAJUC', 'Federación Nacional de Ajedrez de Guatemala', 6, b'1'),
('FENACOAJ', 'Federación Nacional de Ajedrez de Costa Rica', 3, b'1'),
('FENAMAC', 'Federación Nacional de Ajedrez de Nicaragua', 4, b'1'),
('FENAPAJ', 'Federación Nacional de Ajedrez de Panamá', 2, b'0'),
('FENAZA', 'Federación Nacional de Ajedrez del Salvador', 1, b'1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fides`
--

CREATE TABLE `fides` (
  `fide_id` int(11) NOT NULL,
  `cedula_ajedrecista_id` varchar(20) DEFAULT NULL,
  `fed_id` varchar(10) DEFAULT NULL,
  `titulo` varchar(10) DEFAULT NULL,
  `fide_estado` bit(1) DEFAULT b'1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `fides`
--

INSERT INTO `fides` (`fide_id`, `cedula_ajedrecista_id`, `fed_id`, `titulo`, `fide_estado`) VALUES
(20190328, '001-030802-1004G', 'FENAMAC', 'CM', b'1'),
(201903286, '028-123001-4258W', 'FEDEMA', 'GM', b'1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `miembros`
--

CREATE TABLE `miembros` (
  `cedula` varchar(20) NOT NULL,
  `nombres` varchar(50) DEFAULT NULL,
  `apellidos` varchar(50) DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` int(11) DEFAULT NULL,
  `fecha_inscripcion` date DEFAULT NULL,
  `club` varchar(30) DEFAULT NULL,
  `correo_sistema_id` varchar(40) DEFAULT NULL,
  `ciudad_id` int(11) DEFAULT NULL,
  `academia_id` varchar(50) DEFAULT NULL,
  `estado_miembro` bit(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `miembros`
--

INSERT INTO `miembros` (`cedula`, `nombres`, `apellidos`, `sexo`, `fecha_nacimiento`, `telefono`, `fecha_inscripcion`, `club`, `correo_sistema_id`, `ciudad_id`, `academia_id`, `estado_miembro`) VALUES
('001-030802-1004G', 'Engel Antonio', 'Largaespada Vargas', 'M', '2002-08-03', 81489896, '2025-01-28', 'Porcinos CLUB', 'engellargaespadavargas@gmail.com', 49, 'Nuevo nombre', b'1'),
('003-042503-2569Q', 'Adriana Sofia', 'Chamorro Zamora', 'F', '2003-06-06', 54123698, '2025-01-30', '', NULL, 51, 'Nuevo nombre', b'1'),
('028-123001-4258W', 'Ernesto José', 'Martínez Avilés', 'M', '2001-12-30', 89756412, '2025-01-30', '', 'docente.dominical@gmail.com', 40, 'Nuevo nombre', b'0'),
('1245678', 'Registro importado', 'Completo', 'M', '2025-02-16', 12345678, '2025-02-16', 'Club importado', NULL, NULL, 'Nuevo nombre', b'1'),
('5555', 'Registro importado', 'Parcialmente', 'F', NULL, NULL, '2025-02-16', '', NULL, NULL, NULL, b'1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises`
--

CREATE TABLE `paises` (
  `id_pais` int(11) NOT NULL,
  `nombre_pais` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `paises`
--

INSERT INTO `paises` (`id_pais`, `nombre_pais`) VALUES
(3, 'Costa Rica'),
(1, 'El Salvador'),
(6, 'Guatemala'),
(5, 'Honduras'),
(7, 'Jamaica'),
(4, 'Nicaragua'),
(24, 'Nuevo'),
(37, 'Pais1'),
(2, 'Panamá'),
(38, 'Prueba editado'),
(40, 'Prueba editado 2.4'),
(33, 'Test3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `participantes`
--

CREATE TABLE `participantes` (
  `participante_id` varchar(20) NOT NULL,
  `torneo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `participantes`
--

INSERT INTO `participantes` (`participante_id`, `torneo_id`) VALUES
('001-030802-1004G', 4),
('003-042503-2569Q', 1),
('1245678', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidas`
--

CREATE TABLE `partidas` (
  `no_partida` int(11) NOT NULL,
  `ronda` int(11) DEFAULT NULL,
  `participante_id` varchar(20) DEFAULT NULL,
  `torneo_id` int(11) DEFAULT NULL,
  `mesa` int(11) DEFAULT NULL,
  `color` bit(1) DEFAULT NULL,
  `tiempo` time DEFAULT NULL,
  `desempate_utilizado_id` int(11) DEFAULT NULL,
  `estado_abandono` bit(1) DEFAULT NULL,
  `resultado` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `permiso` varchar(20) NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `permiso`, `descripcion`) VALUES
(1, 'Lectura', 'Acceso limitado a la visualización de la información'),
(2, 'Escritura', 'Acceso limitado a la visualización y edición de la información'),
(3, 'Eliminación', 'Acceso limitado a la visualización y eliminación de la información'),
(4, 'Acceso Total', 'Acceso total a las funciones del sistema '),
(5, 'Exportación', 'Acceso a las funciones para exportación de información');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntajes_elo`
--

CREATE TABLE `puntajes_elo` (
  `fide_id_miembro` int(11) NOT NULL,
  `no_categoria_elo` int(11) NOT NULL,
  `elo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `puntajes_elo`
--

INSERT INTO `puntajes_elo` (`fide_id_miembro`, `no_categoria_elo`, `elo`) VALUES
(20190328, 1, 1000),
(20190328, 2, 1800),
(20190328, 3, 745),
(201903286, 1, 200),
(201903286, 2, 300),
(201903286, 3, 100);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `rol` varchar(15) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `rol`, `descripcion`) VALUES
(1, 'admin', 'Rol administrativo con acceso total a las funciones'),
(2, 'evaluador', 'Rol para evaluador con acceso parcial a las funciones'),
(3, 'estudiante', 'Rol para estudiante con acceso limitado a las funciones'),
(4, 'gestor', 'Rol para gestionar cuentas de otros usuarios');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sistemas_desempate`
--

CREATE TABLE `sistemas_desempate` (
  `id_sistema_desempate` int(11) NOT NULL,
  `nombre_sistema_desempate` varchar(50) NOT NULL,
  `descrip_sistema_desempate` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sistemas_de_emparejamiento`
--

CREATE TABLE `sistemas_de_emparejamiento` (
  `id_emparejamiento` int(11) NOT NULL,
  `sistema` varchar(50) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `sistemas_de_emparejamiento`
--

INSERT INTO `sistemas_de_emparejamiento` (`id_emparejamiento`, `sistema`, `descripcion`) VALUES
(1, 'Sistema Suizo', ''),
(2, 'Round-Robin', NULL),
(3, 'Eliminación Directa ', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `torneos`
--

CREATE TABLE `torneos` (
  `id_torneo` int(11) NOT NULL,
  `categoriaTorneo_id` int(11) NOT NULL,
  `organizador_id` varchar(20) NOT NULL,
  `control_tiempo_id` int(11) DEFAULT NULL,
  `director_torneo_id` varchar(20) NOT NULL,
  `arbitro_principal_id` varchar(20) NOT NULL,
  `arbitro_id` varchar(20) NOT NULL,
  `arbitro_adjunto_id` varchar(20) NOT NULL,
  `federacion_id` varchar(10) DEFAULT NULL,
  `nombre_torneo` varchar(100) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `lugar` varchar(100) DEFAULT NULL,
  `no_rondas` int(11) NOT NULL,
  `estado_torneo` bit(1) DEFAULT b'1',
  `sistema_emparejamiento_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `torneos`
--

INSERT INTO `torneos` (`id_torneo`, `categoriaTorneo_id`, `organizador_id`, `control_tiempo_id`, `director_torneo_id`, `arbitro_principal_id`, `arbitro_id`, `arbitro_adjunto_id`, `federacion_id`, `nombre_torneo`, `fecha_inicio`, `hora_inicio`, `lugar`, `no_rondas`, `estado_torneo`, `sistema_emparejamiento_id`) VALUES
(1, 1, '001-030802-1004G', 1, '001-030802-1004G', '001-030802-1004G', '001-030802-1004G', '001-030802-1004G', 'FENAMAC', 'Torneo de prueba', '2025-02-02', '16:28:13', 'Casa de la cultura', 7, b'0', NULL),
(4, 2, '001-030802-1004G', 2, '001-030802-1004G', '001-030802-1004G', '001-030802-1004G', '001-030802-1004G', 'FEDEMA', 'Nuevo torneo edit1', '2025-02-03', '16:30:00', 'Here', 6, b'1', NULL),
(6, 3, '001-030802-1004G', 3, '001-030802-1004G', '001-030802-1004G', '001-030802-1004G', '001-030802-1004G', 'FENAPAJ', 'torneo para celular', '2025-02-03', '22:30:00', 'En mi casa', 7, b'1', NULL),
(10, 1, '001-030802-1004G', NULL, '001-030802-1004G', '001-030802-1004G', '001-030802-1004G', '001-030802-1004G', NULL, 'Torneo emparejado', '2025-02-16', '17:00:00', 'Aqui', 7, b'1', 1),
(12, 1, '001-030802-1004G', NULL, '001-030802-1004G', '028-123001-4258W', '001-030802-1004G', '028-123001-4258W', NULL, 'Torneo importado', '2025-02-06', '22:30:00', 'Importado', 5, b'1', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `correo` varchar(40) NOT NULL,
  `contrasena` varchar(80) NOT NULL,
  `rol_id` int(11) DEFAULT NULL,
  `usuario_estado` bit(1) DEFAULT b'1',
  `intentos_updatePass` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`correo`, `contrasena`, `rol_id`, `usuario_estado`, `intentos_updatePass`) VALUES
('docente.dominical@gmail.com', '$2y$10$49yLk4PeMQ9KHcsDlPsRzuElemkvK.7iSB/PuocQ7vZnCfTkSsFsC', 1, b'1', NULL),
('docente.sabatino@gmail.com', 'Crepair10x', 2, b'0', NULL),
('engellargaespadavargas@gmail.com', '$2y$10$xgazY.oF7RPmdGJvtDporOncAJY4tvj7k55UCPs1SPlLrIn2Hu0.u', 1, b'1', NULL),
('estudiante.dominical@gmail.com', 'Crepair10x', 3, b'1', NULL),
('laisha.acevedo@gmail.com', 'Crepair10x', 1, b'1', NULL),
('nuevo@gmail.com', '$2y$10$VLbuSpG3dA4h8oUsRxZx8uUM0muoEEy3RCdL1evbJYV.B0zrG2PN.', 1, b'1', NULL),
('recepcion@gmail.com', 'Crepair10x', 4, b'1', NULL),
('testeando@gmail.com', '$2y$10$M81TMgyu65nXydaN4XJyTeJtD/f615a8IK9TvquekB91rtvEEL4IO', 3, b'0', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `academias`
--
ALTER TABLE `academias`
  ADD PRIMARY KEY (`nombre_academia`) USING BTREE,
  ADD KEY `fk_ciudad_escuela` (`ciudad_id`);

--
-- Indices de la tabla `asignaciones_permisos`
--
ALTER TABLE `asignaciones_permisos`
  ADD PRIMARY KEY (`rol_id`,`permiso_id`) USING BTREE,
  ADD KEY `fk_asignperm_perm` (`permiso_id`);

--
-- Indices de la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_auditoria_usuario` (`correo_id`);

--
-- Indices de la tabla `categorias_torneo`
--
ALTER TABLE `categorias_torneo`
  ADD PRIMARY KEY (`id_torneo_categoria`),
  ADD UNIQUE KEY `unique_categoria_torneo` (`categoria_torneo`);

--
-- Indices de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  ADD PRIMARY KEY (`id_ciudad`),
  ADD UNIQUE KEY `unique_ciudad` (`nombre_ciudad`,`depto_id`) USING BTREE,
  ADD KEY `fk_depto_ciudad` (`depto_id`);

--
-- Indices de la tabla `controles_tiempo`
--
ALTER TABLE `controles_tiempo`
  ADD PRIMARY KEY (`id_control_tiempo`),
  ADD UNIQUE KEY `unique_control_tiempo` (`control_tiempo`);

--
-- Indices de la tabla `control_tiempo_torneos`
--
ALTER TABLE `control_tiempo_torneos`
  ADD PRIMARY KEY (`control_tiempo_id`,`categorias_torneo_id`),
  ADD KEY `fk_controltiempoTorneos_categoriaTorneo` (`categorias_torneo_id`);

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`id_depto`),
  ADD UNIQUE KEY `unique_departmanto` (`nombre_depto`,`pais_id`) USING BTREE,
  ADD KEY `fk_depto_pais` (`pais_id`);

--
-- Indices de la tabla `desempates_torneo`
--
ALTER TABLE `desempates_torneo`
  ADD PRIMARY KEY (`categoria_torneo_id`,`sistema_desempate_id`),
  ADD KEY `fk_sistemasDesempate_desempatesTorneo` (`sistema_desempate_id`);

--
-- Indices de la tabla `elo_categorias`
--
ALTER TABLE `elo_categorias`
  ADD PRIMARY KEY (`no_elo`);

--
-- Indices de la tabla `federaciones`
--
ALTER TABLE `federaciones`
  ADD PRIMARY KEY (`acronimo`),
  ADD KEY `fk_pais_fed` (`pais_id`);

--
-- Indices de la tabla `fides`
--
ALTER TABLE `fides`
  ADD PRIMARY KEY (`fide_id`),
  ADD UNIQUE KEY `inex_unique_cedula` (`cedula_ajedrecista_id`) USING BTREE,
  ADD KEY `fk_fed_fide` (`fed_id`);

--
-- Indices de la tabla `miembros`
--
ALTER TABLE `miembros`
  ADD PRIMARY KEY (`cedula`),
  ADD KEY `fk_miembro_usuario` (`correo_sistema_id`),
  ADD KEY `fk_ciudad_procedencia` (`ciudad_id`),
  ADD KEY `fk_academia_miembro` (`academia_id`);

--
-- Indices de la tabla `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`id_pais`),
  ADD UNIQUE KEY `unique_pais` (`nombre_pais`);

--
-- Indices de la tabla `participantes`
--
ALTER TABLE `participantes`
  ADD PRIMARY KEY (`participante_id`,`torneo_id`),
  ADD UNIQUE KEY `unique_participante_torneo` (`participante_id`,`torneo_id`),
  ADD KEY `fk_participantes_torneos` (`torneo_id`),
  ADD KEY `participante_id` (`participante_id`);

--
-- Indices de la tabla `partidas`
--
ALTER TABLE `partidas`
  ADD PRIMARY KEY (`no_partida`) USING BTREE,
  ADD UNIQUE KEY `unique_partida` (`ronda`,`participante_id`,`torneo_id`,`mesa`),
  ADD KEY `fk_desempate_partida` (`desempate_utilizado_id`),
  ADD KEY `fk_participante_partida` (`participante_id`),
  ADD KEY `fk_torneo_partida` (`torneo_id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index_unique_permisos` (`permiso`) USING BTREE;

--
-- Indices de la tabla `puntajes_elo`
--
ALTER TABLE `puntajes_elo`
  ADD PRIMARY KEY (`fide_id_miembro`,`no_categoria_elo`),
  ADD KEY `fk_elo_puntajeselo` (`no_categoria_elo`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index_unique_rol` (`rol`) USING BTREE;

--
-- Indices de la tabla `sistemas_desempate`
--
ALTER TABLE `sistemas_desempate`
  ADD PRIMARY KEY (`id_sistema_desempate`),
  ADD UNIQUE KEY `unique_nombre_sistemaDesempate` (`nombre_sistema_desempate`);

--
-- Indices de la tabla `sistemas_de_emparejamiento`
--
ALTER TABLE `sistemas_de_emparejamiento`
  ADD PRIMARY KEY (`id_emparejamiento`),
  ADD UNIQUE KEY `unique_sistema` (`sistema`);

--
-- Indices de la tabla `torneos`
--
ALTER TABLE `torneos`
  ADD PRIMARY KEY (`id_torneo`),
  ADD UNIQUE KEY `unique_nombreFecha_torneo` (`nombre_torneo`,`fecha_inicio`) USING BTREE,
  ADD KEY `fk_torneo_categoriaTorneo` (`categoriaTorneo_id`),
  ADD KEY `fk_torneo_federacion` (`federacion_id`),
  ADD KEY `fk_torneo_miembroArbitro` (`arbitro_id`),
  ADD KEY `fk_torneo_controltiempo` (`control_tiempo_id`),
  ADD KEY `fk_torneo_emparejamiento` (`sistema_emparejamiento_id`),
  ADD KEY `fk_torneo_miembrosOrganizador` (`organizador_id`),
  ADD KEY `fk_torneo_miembrosDirector` (`director_torneo_id`),
  ADD KEY `fk_torneo_miembrosArbitroPrincipal` (`arbitro_principal_id`),
  ADD KEY `fk_torneo_miembrosArbitroAdjunto` (`arbitro_adjunto_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`correo`),
  ADD KEY `fk_usuario_rol` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditorias`
--
ALTER TABLE `auditorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=321;

--
-- AUTO_INCREMENT de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  MODIFY `id_ciudad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id_depto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `elo_categorias`
--
ALTER TABLE `elo_categorias`
  MODIFY `no_elo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
  MODIFY `id_pais` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `partidas`
--
ALTER TABLE `partidas`
  MODIFY `no_partida` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sistemas_de_emparejamiento`
--
ALTER TABLE `sistemas_de_emparejamiento`
  MODIFY `id_emparejamiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `torneos`
--
ALTER TABLE `torneos`
  MODIFY `id_torneo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `academias`
--
ALTER TABLE `academias`
  ADD CONSTRAINT `fk_ciudad_escuela` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades` (`id_ciudad`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `asignaciones_permisos`
--
ALTER TABLE `asignaciones_permisos`
  ADD CONSTRAINT `fk_asignperm_perm` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_asignperm_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`correo_id`) REFERENCES `usuarios` (`correo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ciudades`
--
ALTER TABLE `ciudades`
  ADD CONSTRAINT `fk_depto_ciudad` FOREIGN KEY (`depto_id`) REFERENCES `departamentos` (`id_depto`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `control_tiempo_torneos`
--
ALTER TABLE `control_tiempo_torneos`
  ADD CONSTRAINT `fk_controltiempoTorneos_categoriaTorneo` FOREIGN KEY (`categorias_torneo_id`) REFERENCES `categorias_torneo` (`id_torneo_categoria`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_controltiempoTorneos_controlTiempo` FOREIGN KEY (`control_tiempo_id`) REFERENCES `controles_tiempo` (`id_control_tiempo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD CONSTRAINT `fk_depto_pais` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id_pais`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `desempates_torneo`
--
ALTER TABLE `desempates_torneo`
  ADD CONSTRAINT `fk_categoriasTorneo_desempatesTorneo` FOREIGN KEY (`categoria_torneo_id`) REFERENCES `categorias_torneo` (`id_torneo_categoria`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sistemasDesempate_desempatesTorneo` FOREIGN KEY (`sistema_desempate_id`) REFERENCES `sistemas_desempate` (`id_sistema_desempate`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `federaciones`
--
ALTER TABLE `federaciones`
  ADD CONSTRAINT `fk_pais_fed` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id_pais`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `fides`
--
ALTER TABLE `fides`
  ADD CONSTRAINT `fk_fed_fide` FOREIGN KEY (`fed_id`) REFERENCES `federaciones` (`acronimo`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_miembro_fide` FOREIGN KEY (`cedula_ajedrecista_id`) REFERENCES `miembros` (`cedula`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `miembros`
--
ALTER TABLE `miembros`
  ADD CONSTRAINT `fk_academia_miembro` FOREIGN KEY (`academia_id`) REFERENCES `academias` (`nombre_academia`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ciudad_procedencia` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades` (`id_ciudad`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_miembro_usuario` FOREIGN KEY (`correo_sistema_id`) REFERENCES `usuarios` (`correo`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `participantes`
--
ALTER TABLE `participantes`
  ADD CONSTRAINT `fk_participantes_miembros` FOREIGN KEY (`participante_id`) REFERENCES `miembros` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_participantes_torneos` FOREIGN KEY (`torneo_id`) REFERENCES `torneos` (`id_torneo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `partidas`
--
ALTER TABLE `partidas`
  ADD CONSTRAINT `fk_desempate_partida` FOREIGN KEY (`desempate_utilizado_id`) REFERENCES `sistemas_desempate` (`id_sistema_desempate`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_participante_partida` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`participante_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_torneo_partida` FOREIGN KEY (`torneo_id`) REFERENCES `torneos` (`id_torneo`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `puntajes_elo`
--
ALTER TABLE `puntajes_elo`
  ADD CONSTRAINT `fk_elo_puntajeselo` FOREIGN KEY (`no_categoria_elo`) REFERENCES `elo_categorias` (`no_elo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fide_puntaje_elo` FOREIGN KEY (`fide_id_miembro`) REFERENCES `fides` (`fide_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `torneos`
--
ALTER TABLE `torneos`
  ADD CONSTRAINT `fk_torneo_categoriaTorneo` FOREIGN KEY (`categoriaTorneo_id`) REFERENCES `categorias_torneo` (`id_torneo_categoria`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_torneo_controltiempo` FOREIGN KEY (`control_tiempo_id`) REFERENCES `controles_tiempo` (`id_control_tiempo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_torneo_emparejamiento` FOREIGN KEY (`sistema_emparejamiento_id`) REFERENCES `sistemas_de_emparejamiento` (`id_emparejamiento`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_torneo_federacion` FOREIGN KEY (`federacion_id`) REFERENCES `federaciones` (`acronimo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_torneo_miembroArbitro` FOREIGN KEY (`arbitro_id`) REFERENCES `miembros` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_torneo_miembrosArbitroAdjunto` FOREIGN KEY (`arbitro_adjunto_id`) REFERENCES `miembros` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_torneo_miembrosArbitroPrincipal` FOREIGN KEY (`arbitro_principal_id`) REFERENCES `miembros` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_torneo_miembrosDirector` FOREIGN KEY (`director_torneo_id`) REFERENCES `miembros` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_torneo_miembrosOrganizador` FOREIGN KEY (`organizador_id`) REFERENCES `miembros` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
