-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: silab
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `disciplina`
--

DROP TABLE IF EXISTS `disciplina`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disciplina` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disciplina`
--

LOCK TABLES `disciplina` WRITE;
/*!40000 ALTER TABLE `disciplina` DISABLE KEYS */;
/*!40000 ALTER TABLE `disciplina` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipamento`
--

DROP TABLE IF EXISTS `equipamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipamento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipamento`
--

LOCK TABLES `equipamento` WRITE;
/*!40000 ALTER TABLE `equipamento` DISABLE KEYS */;
INSERT INTO `equipamento` VALUES (1,'Computador'),(2,'Notebook');
/*!40000 ALTER TABLE `equipamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horario`
--

DROP TABLE IF EXISTS `horario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horario`
--

LOCK TABLES `horario` WRITE;
/*!40000 ALTER TABLE `horario` DISABLE KEYS */;
INSERT INTO `horario` VALUES (1,'08:00 - 08:49'),(2,'08:50 - 09:39'),(3,'10:00 - 10:49'),(4,'10:50 - 11:39'),(5,'14:00 - 14:49'),(6,'14:50 - 15:39'),(7,'15:55 - 16:44'),(8,'16:45 - 17:34'),(9,'17:35 - 18:25'),(10,'18:30 - 19:19'),(11,'19:20 - 20:09'),(12,'20:10 - 20:59'),(13,'21:00 - 21:49'),(14,'21:50 - 22:40');
/*!40000 ALTER TABLE `horario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laboratorio`
--

DROP TABLE IF EXISTS `laboratorio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laboratorio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `capacidade` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laboratorio`
--

LOCK TABLES `laboratorio` WRITE;
/*!40000 ALTER TABLE `laboratorio` DISABLE KEYS */;
INSERT INTO `laboratorio` VALUES (1,'Laboratorio 27',30),(2,'Laboratorio 28',44),(3,'Laboratorio 29',20);
/*!40000 ALTER TABLE `laboratorio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laboratorio_equipamento`
--

DROP TABLE IF EXISTS `laboratorio_equipamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laboratorio_equipamento` (
  `laboratorio_id` int(11) NOT NULL,
  `equipamento_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  PRIMARY KEY (`laboratorio_id`,`equipamento_id`),
  KEY `equipamento_id` (`equipamento_id`),
  CONSTRAINT `laboratorio_equipamento_ibfk_1` FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorio` (`id`) ON DELETE CASCADE,
  CONSTRAINT `laboratorio_equipamento_ibfk_2` FOREIGN KEY (`equipamento_id`) REFERENCES `equipamento` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laboratorio_equipamento`
--

LOCK TABLES `laboratorio_equipamento` WRITE;
/*!40000 ALTER TABLE `laboratorio_equipamento` DISABLE KEYS */;
INSERT INTO `laboratorio_equipamento` VALUES (1,1,1),(2,2,1);
/*!40000 ALTER TABLE `laboratorio_equipamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reserva`
--

DROP TABLE IF EXISTS `reserva`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reserva` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `laboratorio_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_reserva` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `motivo` text DEFAULT NULL,
  `status` enum('pendente','confirmada','cancelada') DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  KEY `laboratorio_id` (`laboratorio_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorio` (`id`),
  CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserva`
--

LOCK TABLES `reserva` WRITE;
/*!40000 ALTER TABLE `reserva` DISABLE KEYS */;
INSERT INTO `reserva` VALUES (1,1,1,'2025-08-11','08:00:00','09:00:00','aaaaa','confirmada'),(2,2,3,'2025-08-12','08:00:00','09:00:00','aaaaaaaaaaa','confirmada'),(3,2,3,'2025-08-11','08:00:00','09:00:00','aaaaaaaaaa','confirmada'),(4,1,3,'2025-08-14','14:00:00','15:00:00','Aula de Engenharia de Software','confirmada');
/*!40000 ALTER TABLE `reserva` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitacaocadastro`
--

DROP TABLE IF EXISTS `solicitacaocadastro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitacaocadastro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_completo` varchar(255) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `data_solicitacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pendente','aprovado','rejeitada') DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricula` (`matricula`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitacaocadastro`
--

LOCK TABLES `solicitacaocadastro` WRITE;
/*!40000 ALTER TABLE `solicitacaocadastro` DISABLE KEYS */;
INSERT INTO `solicitacaocadastro` VALUES (1,'Laertty Lima Bizerra','20231SI0016','limabizerra@acad.ifma.edu.br','$2y$10$drlCqq/kbulKnLlDDa4znOXp7fjqA.Zz2fwh26XmpdOTExr043PC2','2025-08-12 06:08:37','aprovado'),(2,'aaaaa','aaaaaaaa','laertty@gmail.com','$2y$10$n/oaLA39YMJmbJImJGrLJ.vtcTMvpJCQhXfk/CQl5sjiOZQoNf.dW','2025-08-13 19:54:50',''),(3,'Felipe Silva Matos','20231SI0029','felipesilva@gmail.com','$2y$10$kqafLlodasOVUmwKQW4OCOEcJcI8qg/sBw5ATJua9Xga08ANl2Aaq','2025-08-13 22:01:23','aprovado'),(4,'Jamily Grazielle Sousa Maciel','20231SI0022','jamily@gmail.com','$2y$10$VS1LsZBjBFBCSib9/vXC1.XwNvYqc/D7QcgQxdHmvNO8CdjHmkgKq','2025-08-13 22:07:58','aprovado'),(5,'Kaillane Corrêa Martins','20211SI0023','kaillane@gmail.com','$2y$10$q8rVFaKX72FNbz272oAzp.LF4Fg.UuiUvsEt2SUS5jdCHJ3K4hKkm','2025-08-13 22:13:02','aprovado'),(6,'Franciele Alves da Silva','20231SI0012','Franciele@gmail.com','$2y$10$IwWqzKCGAiRlWfjRJC/IQe0Ilss.IJHQPO.eZQ6Ceznlc7g9F5iE2','2025-08-13 22:14:59','aprovado'),(7,'Felipe Moura de Oliveira','20231SI0017','felipemoura@gmail.com','$2y$10$Q6uEWSy9Kalz0GuC0CsgZ.HtUnfMumfVX3GfEqJuG1/vIxB/1Umzy','2025-08-13 22:16:20','aprovado'),(8,'Samuel Chaves De Sá','20231SI0011','Samuel@gmail.com','$2y$10$TSnHcVEYFFp277LL2cVRl.i1piRYpyxI3SXarCKXrD3fLSKgAjEQe','2025-08-13 22:20:07','aprovado'),(9,'Teste Um','20231teste1','teste1@gmail.com','$2y$10$ym1EyWUWlmWW6t3I1iT01uAfFGwBbvjVyxJpEpJoF9KqIRb7gTzDC','2025-08-14 17:39:40','pendente'),(10,'Teste Dois','20231teste2','teste2@gmail.com','$2y$10$Axbqlybx38C9BJ991pBEK.a04FN3iAY5.3ePyxe4wAp/UNwJbfAmG','2025-08-14 17:42:52','pendente'),(11,'Teste Três','20231teste3','teste3@gmail.com','$2y$10$tTNuRL9R0OcvOgMNPDC59.mKwsjLuV.Xc0Khcg.EJjfSbFDrEr4g6','2025-08-14 17:43:14','pendente'),(12,'Teste Quatro','20231teste4','teste4@gmail.com','$2y$10$RdGasFelOMqmZ4QQXxQfiO..kmxnrC40eh8gcSjMWxSUTM/9gQ6mi','2025-08-14 17:43:49','pendente'),(13,'Teste Cinco','20231teste5','teste5@gmail.com','$2y$10$8Ug47hLuaOjA3OKoz.e.ou38t7nYophsX7JlVsNsKEvAafJbMbSI6','2025-08-14 17:44:07','pendente'),(14,'Teste Seis','20231teste6','teste6@gmail.com','$2y$10$7yBNue8WGUniTRK6/dSRhOgzEhZKEyf7gGEAUhDXINyL33l7RSkh2','2025-08-14 17:44:26','pendente'),(15,'Teste Sete','20231teste7','teste7@gmail.com','$2y$10$ShO9mwERpmG5wKB.erz7H..3J2rhJDR1f70kyC.mvtrP5pvC.ySs2','2025-08-14 17:44:58','pendente'),(16,'Teste Oito','20231teste8','teste8@gmail.com','$2y$10$zUs79nQoZe7ttdWEF2kw9.Hj6DfKZbvCdm4CL7TJPNuZDdtvcD6zS','2025-08-14 17:45:15','pendente'),(17,'Teste Nove','20231teste9','teste9@gmail.com','$2y$10$o5AYjOHF66TN3pgWcU2sfu7URAlOydtVjR16eCfPbfItDv.WtlH.W','2025-08-14 17:45:33','pendente'),(18,'Teste Dez','20231teste10','teste10@gmail.com','$2y$10$lyMeIZa7DFsoCWlOatFbn.7pBjWoS6rC3fdzib6IbNkZPc1P/C45m','2025-08-14 17:46:04','pendente'),(19,'Teste Onze','20231teste11','teste11@gmail.com','$2y$10$mCwwjVsC1ts0iu9rlgozQO1030.CVMfIc1PbsyG.VuhYq4IzdPXlK','2025-08-14 17:46:26','pendente');
/*!40000 ALTER TABLE `solicitacaocadastro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matricula` varchar(20) DEFAULT NULL,
  `nome_completo` varchar(70) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `status` enum('pendente','aprovado','rejeitada') NOT NULL DEFAULT 'pendente',
  `perfil` enum('Professor','adm') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricula` (`matricula`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'20251DC0000','root','Dcomp@acad.ifma.edu.br','$2y$10$X8grgP3nG6lhrLTlOdWwculV9hsGQUbS0ArDADLKh35H9smLS8oRe','aprovado','adm'),(3,'20231SI0016','Laertty Lima Bizerra','limabizerra@acad.ifma.edu.br','$2y$10$drlCqq/kbulKnLlDDa4znOXp7fjqA.Zz2fwh26XmpdOTExr043PC2','aprovado','Professor'),(4,'20231SI0029','Felipe Silva Matos','felipesilva@gmail.com','$2y$10$kqafLlodasOVUmwKQW4OCOEcJcI8qg/sBw5ATJua9Xga08ANl2Aaq','aprovado','Professor'),(5,'20231SI0022','Jamily Grazielle Sousa Maciel','jamily@gmail.com','$2y$10$VS1LsZBjBFBCSib9/vXC1.XwNvYqc/D7QcgQxdHmvNO8CdjHmkgKq','aprovado','Professor'),(6,'20211SI0023','Kaillane Corrêa Martins','kaillane@gmail.com','$2y$10$q8rVFaKX72FNbz272oAzp.LF4Fg.UuiUvsEt2SUS5jdCHJ3K4hKkm','aprovado','Professor'),(7,'20231SI0012','Franciele Alves da Silva','Franciele@gmail.com','$2y$10$IwWqzKCGAiRlWfjRJC/IQe0Ilss.IJHQPO.eZQ6Ceznlc7g9F5iE2','aprovado','Professor'),(8,'20231SI0017','Felipe Moura de Oliveira','felipemoura@gmail.com','$2y$10$Q6uEWSy9Kalz0GuC0CsgZ.HtUnfMumfVX3GfEqJuG1/vIxB/1Umzy','aprovado','Professor'),(9,'20231SI0011','Samuel Chaves De Sá','Samuel@gmail.com','$2y$10$TSnHcVEYFFp277LL2cVRl.i1piRYpyxI3SXarCKXrD3fLSKgAjEQe','aprovado','Professor');
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

-- Dump completed on 2025-08-14 14:50:26
