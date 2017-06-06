/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `letter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sender_phone` varchar(13) DEFAULT NULL,
  `receiver_phone` varchar(13) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `w3w_address` varchar(255) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `opened_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `receiver_phone` (`receiver_phone`),
  KEY `w3w_address` (`w3w_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
