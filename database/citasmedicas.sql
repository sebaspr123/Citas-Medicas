-- MySQL dump 10.13  Distrib 8.4.8, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: citasmedicas
-- ------------------------------------------------------
-- Server version	8.4.8

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cita`
--

DROP TABLE IF EXISTS `cita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cita` (
  `id_cita` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `id_medico` int NOT NULL,
  `id_consultorio` int NOT NULL,
  `id_empleado` int NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `estado` varchar(20) DEFAULT 'Pendiente',
  PRIMARY KEY (`id_cita`),
  KEY `id_paciente` (`id_paciente`),
  KEY `id_medico` (`id_medico`),
  KEY `id_consultorio` (`id_consultorio`),
  KEY `id_empleado` (`id_empleado`),
  CONSTRAINT `cita_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`id_paciente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cita_ibfk_2` FOREIGN KEY (`id_medico`) REFERENCES `medico` (`id_medico`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cita_ibfk_3` FOREIGN KEY (`id_consultorio`) REFERENCES `consultorio` (`id_consultorio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cita_ibfk_4` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cita`
--

LOCK TABLES `cita` WRITE;
/*!40000 ALTER TABLE `cita` DISABLE KEYS */;
INSERT INTO `cita` VALUES (4,10,7,9,1,'2026-08-20','10:45:00','Pendiente'),(5,7,10,10,2,'2026-07-20','10:30:00','Pendiente');
/*!40000 ALTER TABLE `cita` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consultorio`
--

DROP TABLE IF EXISTS `consultorio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consultorio` (
  `id_consultorio` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(10) NOT NULL,
  `piso` int DEFAULT NULL,
  `ubicacion` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_consultorio`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consultorio`
--

LOCK TABLES `consultorio` WRITE;
/*!40000 ALTER TABLE `consultorio` DISABLE KEYS */;
INSERT INTO `consultorio` VALUES (1,'101',1,'Sector A'),(2,'202',2,'Sector B'),(3,'101',1,'Bloque A'),(4,'102',1,'Bloque A'),(5,'201',2,'Bloque B'),(6,'202',2,'Bloque B'),(7,'203',2,'Bloque B'),(8,'301',3,'Bloque C'),(9,'302',3,'Bloque C'),(10,'401',4,'Bloque D'),(11,'402',4,'Bloque D'),(12,'501',5,'Bloque E');
/*!40000 ALTER TABLE `consultorio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleado`
--

DROP TABLE IF EXISTS `empleado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleado` (
  `id_empleado` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `cargo` varchar(50) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `id_usuario` int DEFAULT NULL,
  PRIMARY KEY (`id_empleado`),
  KEY `fk_empleado_usuario` (`id_usuario`),
  CONSTRAINT `fk_empleado_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleado`
--

LOCK TABLES `empleado` WRITE;
/*!40000 ALTER TABLE `empleado` DISABLE KEYS */;
INSERT INTO `empleado` VALUES (1,'Sebastian',NULL,NULL,NULL,1),(2,'Andres Burgos Agamez',NULL,'3137778902','andresbagamez@gmail.com',3);
/*!40000 ALTER TABLE `empleado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `especialidad`
--

DROP TABLE IF EXISTS `especialidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `especialidad` (
  `id_especialidad` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text,
  PRIMARY KEY (`id_especialidad`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `especialidad`
--

LOCK TABLES `especialidad` WRITE;
/*!40000 ALTER TABLE `especialidad` DISABLE KEYS */;
INSERT INTO `especialidad` VALUES (1,'Cardiología','Diagnóstico y tratamiento de enfermedades del corazón y sistema circulatorio.'),(2,'Pediatría','Atención médica para bebés, niños y adolescentes.'),(3,'Medicina General','Atención primaria y diagnósticos generales'),(4,'Dermatología','Piel, cabello y uñas'),(5,'Oftalmología','Salud visual'),(6,'Ginecología','Salud reproductiva femenina'),(7,'Traumatología','Lesiones óseas y musculares'),(8,'Psiquiatría','Salud mental'),(9,'Endocrinología','Hormonas y metabolismo'),(10,'Otorrinolaringología','Oído, nariz y garganta');
/*!40000 ALTER TABLE `especialidad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historia_clinica`
--

DROP TABLE IF EXISTS `historia_clinica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historia_clinica` (
  `id_historia` int NOT NULL AUTO_INCREMENT,
  `id_cita` int NOT NULL,
  `diagnostico` text,
  `tratamiento` text,
  `observaciones` text,
  PRIMARY KEY (`id_historia`),
  UNIQUE KEY `id_cita` (`id_cita`),
  CONSTRAINT `historia_clinica_ibfk_1` FOREIGN KEY (`id_cita`) REFERENCES `cita` (`id_cita`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historia_clinica`
--

LOCK TABLES `historia_clinica` WRITE;
/*!40000 ALTER TABLE `historia_clinica` DISABLE KEYS */;
/*!40000 ALTER TABLE `historia_clinica` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medico`
--

DROP TABLE IF EXISTS `medico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medico` (
  `id_medico` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `id_especialidad` int DEFAULT NULL,
  PRIMARY KEY (`id_medico`),
  UNIQUE KEY `cedula` (`cedula`),
  KEY `id_especialidad` (`id_especialidad`),
  CONSTRAINT `medico_ibfk_1` FOREIGN KEY (`id_especialidad`) REFERENCES `especialidad` (`id_especialidad`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medico`
--

LOCK TABLES `medico` WRITE;
/*!40000 ALTER TABLE `medico` DISABLE KEYS */;
INSERT INTO `medico` VALUES (1,'Juan','Ramírez','1029456789','3145678901','jramirez@hospital.com',1),(2,'Laura','González','1012547890','3207896543','lgonzalez@hospital.com',2),(4,'Paula','Ríos','90022334','3112345678','paula.rios@clinica.com',2),(5,'Sergio','López','90055667','3123456789','sergio.lopez@clinica.com',3),(7,'Andrés','Gil','90011223','3145678901','andres.gil@clinica.com',4),(8,'Juliana','Pardo','90066778','3156789012','juliana.pardo@clinica.com',5),(9,'Esteban','Suárez','90088990','3167890123','esteban.suarez@clinica.com',6),(10,'Catalina','Peña','90033445','3178901234','catalina.pena@clinica.com',7),(11,'Felipe','Barrera','90055678','3189012345','felipe.barrera@clinica.com',8),(12,'Natalia','Castro','90077889','3190123456','natalia.castro@clinica.com',9);
/*!40000 ALTER TABLE `medico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paciente`
--

DROP TABLE IF EXISTS `paciente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paciente` (
  `id_paciente` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  PRIMARY KEY (`id_paciente`),
  UNIQUE KEY `cedula` (`cedula`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paciente`
--

LOCK TABLES `paciente` WRITE;
/*!40000 ALTER TABLE `paciente` DISABLE KEYS */;
INSERT INTO `paciente` VALUES (2,'Carlos','Gómez','1023456789','3124567890','Cra 10 #15-21','carlos.gomez@mail.com','1990-04-12'),(4,'Luis','Ramírez','1002458796','3204569870','Av 30 #14-33','luis.ramirez@mail.com','1985-12-02'),(5,'María','López','1123456001','3157890011','Calle 70 #56-12','maria.lopez@mail.com','1998-03-27'),(6,'Jorge','Pérez','1054789633','3017892234','Cra 55 #101-30','jorge.perez@mail.com','1975-06-05'),(7,'Diana','Rodríguez','1109845633','3001122334','Calle 33 #18-04','diana.rodriguez@mail.com','1992-02-18'),(8,'Oscar','Medina','1034587699','3164457788','Av 13 #40-25','oscar.medina@mail.com','1980-09-09'),(9,'Sofía','Castro','1145789644','3129988776','Cra 3 #22-10','sofia.castro@mail.com','2000-01-01'),(10,'Camilo','Salinas','1013556889','3186754321','Calle 15 #8-12','camilo.salinas@mail.com','1996-07-14'),(11,'Yulieth','Moreno','1099887766','3156677889','Cra 19 #55-09','yulieth.moreno@mail.com','1994-11-04');
/*!40000 ALTER TABLE `paciente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','empleado') NOT NULL DEFAULT 'empleado',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'Sebastian','Administrador1','$2y$10$uoIlPWG6rE24p6..6unDIOqRbywCVkQwLC.SvomXD/Lcq/bA1aKAK','admin',1),(2,'Test','test123','1234','empleado',1),(3,'Andres Burgos Agamez','AndresAgamez','$2y$10$iAzSYWNBsHGcBeO5E0DBFeMJ/YSLIXOPksbrgOD19jVkunB74EkBC','empleado',1);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-05 11:04:18
