-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: wise
-- ------------------------------------------------------
-- Server version	8.0.40

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
-- Table structure for table `user`
--
CREATE DATABASE IF NOT EXISTS wise;
USE wise;

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `User_ID` int NOT NULL AUTO_INCREMENT,
  `User_Number` int NOT NULL,
  `User_Name` varchar(255) NOT NULL,
  `User_FullName` varchar(255) DEFAULT NULL,
  `User_Password` varchar(255) NOT NULL,
  `User_Email` varchar(255) NOT NULL,
  `User_Avatar` varchar(255) NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `failed_login` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `AdminID` int NOT NULL DEFAULT '0',
  `SourceID` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`User_ID`),
  UNIQUE KEY `User_Number` (`User_Number`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1,'Admin','FullName','40bd001563085fc35165329ea1ff5c5ecbdbbeef','wise@wise.edu.jo','style/photo/users/wise.png','2025-01-03 01:09:11',0,'2022-08-18',3,0),(2,2,'employee',NULL,'40bd001563085fc35165329ea1ff5c5ecbdbbeef','wise@wise.edu.jo','style/photo/users/wise.png','2025-01-05 12:00:50',0,NULL,2,0),(3,3,'user','Fullname','40bd001563085fc35165329ea1ff5c5ecbdbbeef','wise@wise.edu.jo','style/photo/users/wise.png','2025-01-05 12:05:41',0,NULL,1,0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-06  4:15:00

ALTER TABLE `user`
ADD COLUMN `User_Phone` varchar(20) DEFAULT NULL,
ADD COLUMN `User_NationalID` varchar(20) DEFAULT NULL;

-- Create properties table if it doesn't exist
CREATE TABLE IF NOT EXISTS properties (
    id INT PRIMARY KEY AUTO_INCREMENT,
    owner_id INT NOT NULL,
    district_name VARCHAR(100) NOT NULL,
    village VARCHAR(100) NOT NULL,
    block_name VARCHAR(100) NOT NULL,
    plot_number VARCHAR(50) NOT NULL,
    block_number VARCHAR(50) NOT NULL,
    apartment_number VARCHAR(50),
    status ENUM('active', 'pending_transfer', 'transferred') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES user(User_ID)
);

-- Create property_transfers table
CREATE TABLE IF NOT EXISTS property_transfers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    property_id INT NOT NULL,
    seller_id INT NOT NULL,
    buyer_name VARCHAR(100) NOT NULL,
    buyer_national_id VARCHAR(50) NOT NULL,
    buyer_phone VARCHAR(20) NOT NULL,
    buyer_address TEXT NOT NULL,
    tracking_number VARCHAR(20) NOT NULL UNIQUE,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id),
    FOREIGN KEY (seller_id) REFERENCES user(User_ID)
);

-- Add indexes for better performance
CREATE INDEX idx_property_transfers_tracking ON property_transfers(tracking_number);
CREATE INDEX idx_property_transfers_status ON property_transfers(status);
CREATE INDEX idx_properties_status ON properties(status);