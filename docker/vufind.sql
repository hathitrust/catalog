/* 
  As of 2022-03-24, only 'vfsession' appears to be actively used in production.
*/

CREATE DATABASE `vufind`;
GRANT USAGE ON *.* TO 'vufind'@'%' IDENTIFIED BY 'notvillanova';
GRANT SELECT, INSERT, UPDATE, DELETE, LOCK TABLES ON `vufind`.* TO 'vufind'@'%';

USE `vufind`;

CREATE TABLE `vfsession` (
  `id` varchar(32) NOT NULL,
  `cookie` varchar(32) DEFAULT NULL,
  `expires` int(10) DEFAULT NULL,
  `data` mediumblob,
  PRIMARY KEY (`id`)
);
