-- MySQL dump 10.13  Distrib 5.6.22, for Linux (x86_64)
--
-- Host: localhost    Database: bigfooto_relay
-- ------------------------------------------------------
-- Server version	5.6.22

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `entry`
--

DROP TABLE IF EXISTS `entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entry` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ordertime` datetime NOT NULL,
  `name` varchar(60) NOT NULL,
  `sportident` int(10) NOT NULL DEFAULT '0',
  `class` varchar(20) NOT NULL,
  `club` varchar(60) NOT NULL,
  `state` int(2) NOT NULL,
  `sex` varchar(1) NOT NULL DEFAULT 'U',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ordertime` (`ordertime`,`name`),
  UNIQUE KEY `name` (`name`,`club`),
  UNIQUE KEY `name_2` (`name`,`club`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entry`
--

LOCK TABLES `entry` WRITE;
/*!40000 ALTER TABLE `entry` DISABLE KEYS */;
INSERT INTO `entry` (`id`, `ordertime`, `name`, `sportident`, `class`, `club`, `state`, `sex`) VALUES (1,'2014-12-11 18:08:55','Sebastian O\\\'Halloran',8111998,'NOL Relay','Australopers Orienteering Club',8,'M'),(2,'2014-12-14 18:02:20','Ricky Thackray',9200454,'NOL Relay','Bibbulmun Orienteers',10,'M'),(3,'2014-12-16 15:35:49','Natasha Sparg',9005326,'NOL Relay','Bibbulmun Orienteers',10,'F'),(4,'2015-01-03 22:38:26','Gayle Quantock',1931340,'Public Relay','Newcastle Orienteering Club',5,'F'),(5,'2015-01-03 22:38:26','Scott Simson',7200118,'Public Relay','Newcastle Orienteering Club',5,'M'),(6,'2015-01-03 22:38:26','Kaitlin Radstaak',9101018,'NOL Relay','Newcastle Orienteering Club',5,'F'),(7,'2015-01-05 21:40:17','Lisa Lampe',2056440,'Public Relay','Uringa Orienteers',5,'F'),(8,'2015-01-06 00:20:16','Jim Russell',7003014,'NOL Relay','Bendigo Orienteers',9,'M'),(9,'2015-01-09 13:19:09','Andrew Lumsden',2026300,'Public Relay','Big Foot Orienteers',5,'M'),(10,'2015-01-09 13:19:09','Debbie Byers',402403,'Public Relay','Big Foot Orienteers',5,'F'),(11,'2015-01-12 15:08:23','Aislinn Prendergast',1931368,'NOL Relay','Eureka Orienteers',9,'F'),(12,'2015-01-12 15:08:23','Roch Prendergast',1931353,'Public Relay','Eureka Orienteers',9,'M'),(13,'2015-01-13 12:16:48','Clare Brownridge',7200204,'NOL Relay','Bendigo Orienteers',9,'F'),(14,'2015-01-15 10:21:02','Jenny Casanova',1392403,'NOL Relay','Wallaringa Orienteering Club',7,'F'),(15,'2015-01-19 19:48:06','Sue Thomson',213692,'Public Relay','Garingal Orienteers',5,'F'),(31,'2015-01-24 11:21:05','Lachlan Dow',1600566,'NOL Relay','Bushflyers ACT',4,'M'),(32,'2015-01-25 15:05:55','Stephan Wagner',1602125,'NOL Relay','Southern Highlands Occasional Orienteers',5,'M');
/*!40000 ALTER TABLE `entry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment` (
  `ordertime` datetime NOT NULL,
  `reference` int(8) NOT NULL,
  `customer` text NOT NULL,
  PRIMARY KEY (`ordertime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment`
--

LOCK TABLES `payment` WRITE;
/*!40000 ALTER TABLE `payment` DISABLE KEYS */;
INSERT INTO `payment` (`ordertime`, `reference`, `customer`) VALUES ('2014-12-11 18:08:55',12303780,'Sebastian O\'Halloran, 30 Bishop St New Town, Hobart 7008, Tas, Australia, 03-62289660, sebyohalloran@gmail.com'),('2014-12-14 18:02:20',12333886,'Ricky Thackray, 9 Rusthall Way, Huntingdale 6110, WA, Australia, 0459486739, rickythackray@hotmail.com'),('2014-12-16 15:35:49',12356283,'Natasha Sparg, 19/99 Herdsman Parade, Wembley 6014, WA, Australia, 0402172540, natasha.sparg@gmail.com'),('2015-01-03 22:38:26',12431581,'Gayle Quantock, 40 Tenyo Street, Cameron Park 2285, NSW, Australia, 0249588770, gayle.quantock@bigpond.com'),('2015-01-05 21:40:17',12436788,'Lisa Lampe, PO Box 4003, Royal North Shore Hospital St Leonards 2065, NSW, Australia, 0408274766, trickcyclist12@bigpond.com'),('2015-01-06 00:20:16',12437182,'Jim Russell, 14 Lawson St, Bendigo 3550, Vic, Australia, 0411125178, jymbo@dodo.com.au'),('2015-01-09 13:19:09',12442489,'Andrew Lumsden, 3 Dulwich Road, Chatswood 2067, NSW, Australia, 0294123545, lumsden.byers@gmail.com'),('2015-01-12 15:08:23',12455184,'Aislinn Prendergast, 4A Norwood St, Glen Iris 3146, Vic, Australia, 0400959735, aislinn.49@gmail.com'),('2015-01-13 12:16:48',12458782,'Clare Brownridge, 80 Lawson St, Bendigo 3550, Vic, Australia, clare_brownridge@fastmail.fm'),('2015-01-15 10:21:02',12464483,'Jenny Casanova, 7 Perry Ave, Daw Park 5041, SA, Australia, 0427605167, jenny.casanova@health.sa.gov.au'),('2015-01-19 19:48:06',12476586,'Sue Thomson, 1/2 Peel St, Kirribilli 2061, NSW, Australia, 9929-6292, suecomrie@yahoo.com'),('2015-01-24 11:21:05',12485181,'Lachlan Dow, 67 Tyson St, Ainslie 2602, ACT, Australia, cldow@grapevine.com.au'),('2015-01-25 15:05:55',12487682,'Stephan Wagner, 8 Lanark Place, St Andrews 2566, NSW, Australia, 0296036734, stephanw@bigpond.net.au');
/*!40000 ALTER TABLE `payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `selector`
--

DROP TABLE IF EXISTS `selector`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `selector` (
  `email` varchar(46) NOT NULL,
  `password` char(40) NOT NULL,
  `state` int(2) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `selector`
--

LOCK TABLES `selector` WRITE;
/*!40000 ALTER TABLE `selector` DISABLE KEYS */;
INSERT INTO `selector` (`email`, `password`, `state`) VALUES ('nol@bigfootorienteers.com','6a5b17d408bcaefe752bb9d8a78837b6e2684727',-1);
/*!40000 ALTER TABLE `selector` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team` (
  `class` varchar(60) NOT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team`
--

LOCK TABLES `team` WRITE;
/*!40000 ALTER TABLE `team` DISABLE KEYS */;
INSERT INTO `team` (`class`, `id`, `name`) VALUES ('NOL relay',44,'Under rated'),('NOL relay',45,'Over rated'),('NOL relay',46,'under the radar'),('Public relay',53,'1'),('Public relay',54,'2'),('Public relay',55,'3'),('Public relay',56,'4');
/*!40000 ALTER TABLE `team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teammember`
--

DROP TABLE IF EXISTS `teammember`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teammember` (
  `teamid` int(11) NOT NULL,
  `entryid` int(11) NOT NULL,
  `runningorder` int(2) NOT NULL,
  UNIQUE KEY `entryid` (`entryid`),
  KEY `teamindex` (`teamid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teammember`
--

LOCK TABLES `teammember` WRITE;
/*!40000 ALTER TABLE `teammember` DISABLE KEYS */;
INSERT INTO `teammember` (`teamid`, `entryid`, `runningorder`) VALUES (44,1,1),(46,2,4),(46,3,2),(56,4,1),(56,5,2),(44,6,2),(55,7,1),(44,8,4),(54,9,1),(53,10,1),(45,11,1),(53,12,2),(44,13,3),(46,14,3),(55,15,2),(45,31,2),(46,32,1);
/*!40000 ALTER TABLE `teammember` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'bigfooto_relay'
--

--
-- Dumping routines for database 'bigfooto_relay'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-01-26 18:49:47
