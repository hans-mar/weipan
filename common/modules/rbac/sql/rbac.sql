/*
Navicat MySQL Data Transfer

Source Server         : ChisWill
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : hsh_ver2

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-02-18 17:07:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `hsh_auth_assignment`
-- ----------------------------
DROP TABLE IF EXISTS `hsh_auth_assignment`;
CREATE TABLE `hsh_auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `hsh_auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `hsh_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `hsh_auth_item`
-- ----------------------------
DROP TABLE IF EXISTS `hsh_auth_item`;
CREATE TABLE `hsh_auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `hsh_auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `hsh_auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `hsh_auth_item_child`
-- ----------------------------
DROP TABLE IF EXISTS `hsh_auth_item_child`;
CREATE TABLE `hsh_auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `hsh_auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `hsh_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `hsh_auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `hsh_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `hsh_auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `hsh_auth_rule`;
CREATE TABLE `hsh_auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;