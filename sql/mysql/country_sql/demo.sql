CREATE DATABASE IF NOT EXISTS weberpdemo;
USE weberpdemo;
SET FOREIGN_KEY_CHECKS = 0;
-- MySQL dump 10.14  Distrib 5.5.40-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: weberpdemo
-- ------------------------------------------------------
-- Server version	5.5.40-MariaDB-0ubuntu0.14.04.1
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accountgroups`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accountgroups` (
  `groupname` char(30) NOT NULL DEFAULT '',
  `sectioninaccounts` int(11) NOT NULL DEFAULT '0',
  `pandl` tinyint(4) NOT NULL DEFAULT '1',
  `sequenceintb` smallint(6) NOT NULL DEFAULT '0',
  `parentgroupname` varchar(30) NOT NULL,
  PRIMARY KEY (`groupname`),
  KEY `SequenceInTB` (`sequenceintb`),
  KEY `sectioninaccounts` (`sectioninaccounts`),
  KEY `parentgroupname` (`parentgroupname`),
  CONSTRAINT `accountgroups_ibfk_1` FOREIGN KEY (`sectioninaccounts`) REFERENCES `accountsection` (`sectionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `accountsection`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accountsection` (
  `sectionid` int(11) NOT NULL DEFAULT '0',
  `sectionname` text NOT NULL,
  PRIMARY KEY (`sectionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `areas`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `areas` (
  `areacode` char(3) NOT NULL,
  `areadescription` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`areacode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assetmanager`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assetmanager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `serialno` varchar(30) NOT NULL DEFAULT '',
  `location` varchar(15) NOT NULL DEFAULT '',
  `cost` double NOT NULL DEFAULT '0',
  `depn` double NOT NULL DEFAULT '0',
  `datepurchased` date NOT NULL DEFAULT '0000-00-00',
  `disposalvalue` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `audittrail`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audittrail` (
  `transactiondate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `userid` varchar(20) NOT NULL DEFAULT '',
  `querystring` text,
  KEY `UserID` (`userid`),
  KEY `transactiondate` (`transactiondate`),
  KEY `transactiondate_2` (`transactiondate`),
  KEY `transactiondate_3` (`transactiondate`),
  CONSTRAINT `audittrail_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `www_users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bankaccounts`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bankaccounts` (
  `accountcode` varchar(20) NOT NULL DEFAULT '0',
  `currcode` char(3) NOT NULL,
  `invoice` smallint(2) NOT NULL DEFAULT '0',
  `bankaccountcode` varchar(50) NOT NULL DEFAULT '',
  `bankaccountname` char(50) NOT NULL DEFAULT '',
  `bankaccountnumber` char(50) NOT NULL DEFAULT '',
  `bankaddress` char(50) DEFAULT NULL,
  `importformat` varchar(10) NOT NULL DEFAULT '''''',
  PRIMARY KEY (`accountcode`),
  KEY `currcode` (`currcode`),
  KEY `BankAccountName` (`bankaccountname`),
  KEY `BankAccountNumber` (`bankaccountnumber`),
  CONSTRAINT `bankaccounts_ibfk_1` FOREIGN KEY (`accountcode`) REFERENCES `chartmaster` (`accountcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bankaccountusers`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bankaccountusers` (
  `accountcode` varchar(20) NOT NULL COMMENT 'Bank account code',
  `userid` varchar(20) NOT NULL COMMENT 'User code'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banktrans`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banktrans` (
  `banktransid` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` smallint(6) NOT NULL DEFAULT '0',
  `transno` bigint(20) NOT NULL DEFAULT '0',
  `bankact` varchar(20) NOT NULL DEFAULT '0',
  `ref` varchar(50) NOT NULL DEFAULT '',
  `amountcleared` double NOT NULL DEFAULT '0',
  `exrate` double NOT NULL DEFAULT '1' COMMENT 'From bank account currency to payment currency',
  `functionalexrate` double NOT NULL DEFAULT '1' COMMENT 'Account currency to functional currency',
  `transdate` date NOT NULL DEFAULT '0000-00-00',
  `banktranstype` varchar(30) NOT NULL DEFAULT '',
  `amount` double NOT NULL DEFAULT '0',
  `currcode` char(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`banktransid`),
  KEY `BankAct` (`bankact`,`ref`),
  KEY `TransDate` (`transdate`),
  KEY `TransType` (`banktranstype`),
  KEY `Type` (`type`,`transno`),
  KEY `CurrCode` (`currcode`),
  KEY `ref` (`ref`),
  CONSTRAINT `banktrans_ibfk_1` FOREIGN KEY (`type`) REFERENCES `systypes` (`typeid`),
  CONSTRAINT `banktrans_ibfk_2` FOREIGN KEY (`bankact`) REFERENCES `bankaccounts` (`accountcode`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bom` (
  `parent` char(20) NOT NULL DEFAULT '',
  `sequence` int(11) NOT NULL DEFAULT '0',
  `component` char(20) NOT NULL DEFAULT '',
  `workcentreadded` char(5) NOT NULL DEFAULT '',
  `loccode` char(5) NOT NULL DEFAULT '',
  `effectiveafter` date NOT NULL DEFAULT '0000-00-00',
  `effectiveto` date NOT NULL DEFAULT '9999-12-31',
  `quantity` double NOT NULL DEFAULT '1',
  `autoissue` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`parent`,`component`,`workcentreadded`,`loccode`),
  KEY `Component` (`component`),
  KEY `EffectiveAfter` (`effectiveafter`),
  KEY `EffectiveTo` (`effectiveto`),
  KEY `LocCode` (`loccode`),
  KEY `Parent` (`parent`,`effectiveafter`,`effectiveto`,`loccode`),
  KEY `Parent_2` (`parent`),
  KEY `WorkCentreAdded` (`workcentreadded`),
  CONSTRAINT `bom_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `bom_ibfk_2` FOREIGN KEY (`component`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `bom_ibfk_3` FOREIGN KEY (`workcentreadded`) REFERENCES `workcentres` (`code`),
  CONSTRAINT `bom_ibfk_4` FOREIGN KEY (`loccode`) REFERENCES `locations` (`loccode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chartdetails`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chartdetails` (
  `accountcode` varchar(20) NOT NULL DEFAULT '0',
  `period` smallint(6) NOT NULL DEFAULT '0',
  `budget` double NOT NULL DEFAULT '0',
  `actual` double NOT NULL DEFAULT '0',
  `bfwd` double NOT NULL DEFAULT '0',
  `bfwdbudget` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`accountcode`,`period`),
  KEY `Period` (`period`),
  CONSTRAINT `chartdetails_ibfk_1` FOREIGN KEY (`accountcode`) REFERENCES `chartmaster` (`accountcode`),
  CONSTRAINT `chartdetails_ibfk_2` FOREIGN KEY (`period`) REFERENCES `periods` (`periodno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chartmaster`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chartmaster` (
  `accountcode` varchar(20) NOT NULL DEFAULT '0',
  `accountname` char(50) NOT NULL DEFAULT '',
  `group_` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`accountcode`),
  KEY `AccountName` (`accountname`),
  KEY `Group_` (`group_`),
  CONSTRAINT `chartmaster_ibfk_1` FOREIGN KEY (`group_`) REFERENCES `accountgroups` (`groupname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cogsglpostings`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cogsglpostings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area` char(3) NOT NULL DEFAULT '',
  `stkcat` varchar(6) NOT NULL DEFAULT '',
  `glcode` varchar(20) NOT NULL DEFAULT '0',
  `salestype` char(2) NOT NULL DEFAULT 'AN',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Area_StkCat` (`area`,`stkcat`,`salestype`),
  KEY `Area` (`area`),
  KEY `StkCat` (`stkcat`),
  KEY `GLCode` (`glcode`),
  KEY `SalesType` (`salestype`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `companies`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `coycode` int(11) NOT NULL DEFAULT '1',
  `coyname` varchar(50) NOT NULL DEFAULT '',
  `gstno` varchar(20) NOT NULL DEFAULT '',
  `companynumber` varchar(20) NOT NULL DEFAULT '0',
  `regoffice1` varchar(40) NOT NULL DEFAULT '',
  `regoffice2` varchar(40) NOT NULL DEFAULT '',
  `regoffice3` varchar(40) NOT NULL DEFAULT '',
  `regoffice4` varchar(40) NOT NULL DEFAULT '',
  `regoffice5` varchar(20) NOT NULL DEFAULT '',
  `regoffice6` varchar(15) NOT NULL DEFAULT '',
  `telephone` varchar(25) NOT NULL DEFAULT '',
  `fax` varchar(25) NOT NULL DEFAULT '',
  `email` varchar(55) NOT NULL DEFAULT '',
  `currencydefault` varchar(4) NOT NULL DEFAULT '',
  `debtorsact` varchar(20) NOT NULL DEFAULT '70000',
  `pytdiscountact` varchar(20) NOT NULL DEFAULT '55000',
  `creditorsact` varchar(20) NOT NULL DEFAULT '80000',
  `payrollact` varchar(20) NOT NULL DEFAULT '84000',
  `grnact` varchar(20) NOT NULL DEFAULT '72000',
  `exchangediffact` varchar(20) NOT NULL DEFAULT '65000',
  `purchasesexchangediffact` varchar(20) NOT NULL DEFAULT '0',
  `retainedearnings` varchar(20) NOT NULL DEFAULT '90000',
  `gllink_debtors` tinyint(1) DEFAULT '1',
  `gllink_creditors` tinyint(1) DEFAULT '1',
  `gllink_stock` tinyint(1) DEFAULT '1',
  `freightact` varchar(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`coycode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `config`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `confname` varchar(35) NOT NULL DEFAULT '',
  `confvalue` text NOT NULL,
  PRIMARY KEY (`confname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contractbom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contractbom` (
  `contractref` varchar(20) NOT NULL DEFAULT '0',
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `workcentreadded` char(5) NOT NULL DEFAULT '',
  `quantity` double NOT NULL DEFAULT '1',
  PRIMARY KEY (`contractref`,`stockid`,`workcentreadded`),
  KEY `Stockid` (`stockid`),
  KEY `ContractRef` (`contractref`),
  KEY `WorkCentreAdded` (`workcentreadded`),
  CONSTRAINT `contractbom_ibfk_1` FOREIGN KEY (`workcentreadded`) REFERENCES `workcentres` (`code`),
  CONSTRAINT `contractbom_ibfk_3` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contractcharges`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contractcharges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contractref` varchar(20) NOT NULL,
  `transtype` smallint(6) NOT NULL DEFAULT '20',
  `transno` int(11) NOT NULL DEFAULT '0',
  `amount` double NOT NULL DEFAULT '0',
  `narrative` text NOT NULL,
  `anticipated` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `contractref` (`contractref`,`transtype`,`transno`),
  KEY `contractcharges_ibfk_2` (`transtype`),
  CONSTRAINT `contractcharges_ibfk_1` FOREIGN KEY (`contractref`) REFERENCES `contracts` (`contractref`),
  CONSTRAINT `contractcharges_ibfk_2` FOREIGN KEY (`transtype`) REFERENCES `systypes` (`typeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contractreqts`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contractreqts` (
  `contractreqid` int(11) NOT NULL AUTO_INCREMENT,
  `contractref` varchar(20) NOT NULL DEFAULT '0',
  `requirement` varchar(40) NOT NULL DEFAULT '',
  `quantity` double NOT NULL DEFAULT '1',
  `costperunit` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`contractreqid`),
  KEY `ContractRef` (`contractref`),
  CONSTRAINT `contractreqts_ibfk_1` FOREIGN KEY (`contractref`) REFERENCES `contracts` (`contractref`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contracts`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contracts` (
  `contractref` varchar(20) NOT NULL DEFAULT '',
  `contractdescription` text NOT NULL,
  `debtorno` varchar(10) NOT NULL DEFAULT '',
  `branchcode` varchar(10) NOT NULL DEFAULT '',
  `loccode` varchar(5) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `categoryid` varchar(6) NOT NULL DEFAULT '',
  `orderno` int(11) NOT NULL DEFAULT '0',
  `customerref` varchar(20) NOT NULL DEFAULT '',
  `margin` double NOT NULL DEFAULT '1',
  `wo` int(11) NOT NULL DEFAULT '0',
  `requireddate` date NOT NULL DEFAULT '0000-00-00',
  `drawing` varchar(50) NOT NULL DEFAULT '',
  `exrate` double NOT NULL DEFAULT '1',
  PRIMARY KEY (`contractref`),
  KEY `OrderNo` (`orderno`),
  KEY `CategoryID` (`categoryid`),
  KEY `Status` (`status`),
  KEY `WO` (`wo`),
  KEY `loccode` (`loccode`),
  KEY `DebtorNo` (`debtorno`,`branchcode`),
  CONSTRAINT `contracts_ibfk_1` FOREIGN KEY (`debtorno`, `branchcode`) REFERENCES `custbranch` (`debtorno`, `branchcode`),
  CONSTRAINT `contracts_ibfk_2` FOREIGN KEY (`categoryid`) REFERENCES `stockcategory` (`categoryid`),
  CONSTRAINT `contracts_ibfk_3` FOREIGN KEY (`loccode`) REFERENCES `locations` (`loccode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `currencies`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currencies` (
  `currency` char(20) NOT NULL DEFAULT '',
  `currabrev` char(3) NOT NULL DEFAULT '',
  `country` char(50) NOT NULL DEFAULT '',
  `hundredsname` char(15) NOT NULL DEFAULT 'Cents',
  `decimalplaces` tinyint(3) NOT NULL DEFAULT '2',
  `rate` double NOT NULL DEFAULT '1',
  `webcart` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'If 1 shown in weberp cart. if 0 no show',
  PRIMARY KEY (`currabrev`),
  KEY `Country` (`country`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custallocns`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custallocns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amt` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `datealloc` date NOT NULL DEFAULT '0000-00-00',
  `transid_allocfrom` int(11) NOT NULL DEFAULT '0',
  `transid_allocto` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `DateAlloc` (`datealloc`),
  KEY `TransID_AllocFrom` (`transid_allocfrom`),
  KEY `TransID_AllocTo` (`transid_allocto`),
  CONSTRAINT `custallocns_ibfk_1` FOREIGN KEY (`transid_allocfrom`) REFERENCES `debtortrans` (`id`),
  CONSTRAINT `custallocns_ibfk_2` FOREIGN KEY (`transid_allocto`) REFERENCES `debtortrans` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custbranch`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custbranch` (
  `branchcode` varchar(10) NOT NULL DEFAULT '',
  `debtorno` varchar(10) NOT NULL DEFAULT '',
  `brname` varchar(40) NOT NULL DEFAULT '',
  `braddress1` varchar(40) NOT NULL DEFAULT '',
  `braddress2` varchar(40) NOT NULL DEFAULT '',
  `braddress3` varchar(40) NOT NULL DEFAULT '',
  `braddress4` varchar(50) NOT NULL DEFAULT '',
  `braddress5` varchar(20) NOT NULL DEFAULT '',
  `braddress6` varchar(40) NOT NULL DEFAULT '',
  `lat` float(10,6) NOT NULL DEFAULT '0.000000',
  `lng` float(10,6) NOT NULL DEFAULT '0.000000',
  `estdeliverydays` smallint(6) NOT NULL DEFAULT '1',
  `area` char(3) NOT NULL,
  `salesman` varchar(4) NOT NULL DEFAULT '',
  `fwddate` smallint(6) NOT NULL DEFAULT '0',
  `phoneno` varchar(20) NOT NULL DEFAULT '',
  `faxno` varchar(20) NOT NULL DEFAULT '',
  `contactname` varchar(30) NOT NULL DEFAULT '',
  `email` varchar(55) NOT NULL DEFAULT '',
  `defaultlocation` varchar(5) NOT NULL DEFAULT '',
  `taxgroupid` tinyint(4) NOT NULL DEFAULT '1',
  `defaultshipvia` int(11) NOT NULL DEFAULT '1',
  `deliverblind` tinyint(1) DEFAULT '1',
  `disabletrans` tinyint(4) NOT NULL DEFAULT '0',
  `brpostaddr1` varchar(40) NOT NULL DEFAULT '',
  `brpostaddr2` varchar(40) NOT NULL DEFAULT '',
  `brpostaddr3` varchar(40) NOT NULL DEFAULT '',
  `brpostaddr4` varchar(50) NOT NULL DEFAULT '',
  `brpostaddr5` varchar(20) NOT NULL DEFAULT '',
  `brpostaddr6` varchar(40) NOT NULL DEFAULT '',
  `specialinstructions` text NOT NULL,
  `custbranchcode` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`branchcode`,`debtorno`),
  KEY `BrName` (`brname`),
  KEY `DebtorNo` (`debtorno`),
  KEY `Salesman` (`salesman`),
  KEY `Area` (`area`),
  KEY `DefaultLocation` (`defaultlocation`),
  KEY `DefaultShipVia` (`defaultshipvia`),
  KEY `taxgroupid` (`taxgroupid`),
  CONSTRAINT `custbranch_ibfk_1` FOREIGN KEY (`debtorno`) REFERENCES `debtorsmaster` (`debtorno`),
  CONSTRAINT `custbranch_ibfk_2` FOREIGN KEY (`area`) REFERENCES `areas` (`areacode`),
  CONSTRAINT `custbranch_ibfk_3` FOREIGN KEY (`salesman`) REFERENCES `salesman` (`salesmancode`),
  CONSTRAINT `custbranch_ibfk_4` FOREIGN KEY (`defaultlocation`) REFERENCES `locations` (`loccode`),
  CONSTRAINT `custbranch_ibfk_6` FOREIGN KEY (`defaultshipvia`) REFERENCES `shippers` (`shipper_id`),
  CONSTRAINT `custbranch_ibfk_7` FOREIGN KEY (`taxgroupid`) REFERENCES `taxgroups` (`taxgroupid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custcontacts`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custcontacts` (
  `contid` int(11) NOT NULL AUTO_INCREMENT,
  `debtorno` varchar(10) NOT NULL,
  `contactname` varchar(40) NOT NULL,
  `role` varchar(40) NOT NULL,
  `phoneno` varchar(20) NOT NULL,
  `notes` varchar(255) NOT NULL,
  `email` varchar(55) NOT NULL,
  PRIMARY KEY (`contid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custitem`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custitem` (
  `debtorno` char(10) NOT NULL DEFAULT '',
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `cust_part` varchar(20) NOT NULL DEFAULT '',
  `cust_description` varchar(30) NOT NULL DEFAULT '',
  `customersuom` char(50) NOT NULL DEFAULT '',
  `conversionfactor` double NOT NULL DEFAULT '1',
  PRIMARY KEY (`debtorno`,`stockid`),
  KEY `StockID` (`stockid`),
  KEY `Debtorno` (`debtorno`),
  CONSTRAINT ` custitem _ibfk_1` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT ` custitem _ibfk_2` FOREIGN KEY (`debtorno`) REFERENCES `debtorsmaster` (`debtorno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custnotes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custnotes` (
  `noteid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `debtorno` varchar(10) NOT NULL DEFAULT '0',
  `href` varchar(100) NOT NULL,
  `note` text NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `priority` varchar(20) NOT NULL,
  PRIMARY KEY (`noteid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `debtorsmaster`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `debtorsmaster` (
  `debtorno` varchar(10) NOT NULL DEFAULT '',
  `name` varchar(40) NOT NULL DEFAULT '',
  `address1` varchar(40) NOT NULL DEFAULT '',
  `address2` varchar(40) NOT NULL DEFAULT '',
  `address3` varchar(40) NOT NULL DEFAULT '',
  `address4` varchar(50) NOT NULL DEFAULT '',
  `address5` varchar(20) NOT NULL DEFAULT '',
  `address6` varchar(40) NOT NULL DEFAULT '',
  `currcode` char(3) NOT NULL DEFAULT '',
  `salestype` char(2) NOT NULL DEFAULT '',
  `clientsince` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `holdreason` smallint(6) NOT NULL DEFAULT '0',
  `paymentterms` char(2) NOT NULL DEFAULT 'f',
  `discount` double NOT NULL DEFAULT '0',
  `pymtdiscount` double NOT NULL DEFAULT '0',
  `lastpaid` double NOT NULL DEFAULT '0',
  `lastpaiddate` datetime DEFAULT NULL,
  `creditlimit` double NOT NULL DEFAULT '1000',
  `invaddrbranch` tinyint(4) NOT NULL DEFAULT '0',
  `discountcode` char(2) NOT NULL DEFAULT '',
  `ediinvoices` tinyint(4) NOT NULL DEFAULT '0',
  `ediorders` tinyint(4) NOT NULL DEFAULT '0',
  `edireference` varchar(20) NOT NULL DEFAULT '',
  `editransport` varchar(5) NOT NULL DEFAULT 'email',
  `ediaddress` varchar(50) NOT NULL DEFAULT '',
  `ediserveruser` varchar(20) NOT NULL DEFAULT '',
  `ediserverpwd` varchar(20) NOT NULL DEFAULT '',
  `taxref` varchar(20) NOT NULL DEFAULT '',
  `customerpoline` tinyint(1) NOT NULL DEFAULT '0',
  `typeid` tinyint(4) NOT NULL DEFAULT '1',
  `language_id` varchar(10) NOT NULL DEFAULT 'en_GB.utf8',
  PRIMARY KEY (`debtorno`),
  KEY `Currency` (`currcode`),
  KEY `HoldReason` (`holdreason`),
  KEY `Name` (`name`),
  KEY `PaymentTerms` (`paymentterms`),
  KEY `SalesType` (`salestype`),
  KEY `EDIInvoices` (`ediinvoices`),
  KEY `EDIOrders` (`ediorders`),
  KEY `debtorsmaster_ibfk_5` (`typeid`),
  CONSTRAINT `debtorsmaster_ibfk_1` FOREIGN KEY (`holdreason`) REFERENCES `holdreasons` (`reasoncode`),
  CONSTRAINT `debtorsmaster_ibfk_2` FOREIGN KEY (`currcode`) REFERENCES `currencies` (`currabrev`),
  CONSTRAINT `debtorsmaster_ibfk_3` FOREIGN KEY (`paymentterms`) REFERENCES `paymentterms` (`termsindicator`),
  CONSTRAINT `debtorsmaster_ibfk_4` FOREIGN KEY (`salestype`) REFERENCES `salestypes` (`typeabbrev`),
  CONSTRAINT `debtorsmaster_ibfk_5` FOREIGN KEY (`typeid`) REFERENCES `debtortype` (`typeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `debtortrans`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `debtortrans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transno` int(11) NOT NULL DEFAULT '0',
  `type` smallint(6) NOT NULL DEFAULT '0',
  `debtorno` varchar(10) NOT NULL DEFAULT '',
  `branchcode` varchar(10) NOT NULL DEFAULT '',
  `trandate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `inputdate` datetime NOT NULL,
  `prd` smallint(6) NOT NULL DEFAULT '0',
  `settled` tinyint(4) NOT NULL DEFAULT '0',
  `reference` varchar(20) NOT NULL DEFAULT '',
  `tpe` char(2) NOT NULL DEFAULT '',
  `order_` int(11) NOT NULL DEFAULT '0',
  `rate` double NOT NULL DEFAULT '0',
  `ovamount` double NOT NULL DEFAULT '0',
  `ovgst` double NOT NULL DEFAULT '0',
  `ovfreight` double NOT NULL DEFAULT '0',
  `ovdiscount` double NOT NULL DEFAULT '0',
  `diffonexch` double NOT NULL DEFAULT '0',
  `alloc` double NOT NULL DEFAULT '0',
  `invtext` text,
  `shipvia` int(11) NOT NULL DEFAULT '0',
  `edisent` tinyint(4) NOT NULL DEFAULT '0',
  `consignment` varchar(20) NOT NULL DEFAULT '',
  `packages` int(11) NOT NULL DEFAULT '1' COMMENT 'number of cartons',
  `salesperson` varchar(4) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `DebtorNo` (`debtorno`,`branchcode`),
  KEY `Order_` (`order_`),
  KEY `Prd` (`prd`),
  KEY `Tpe` (`tpe`),
  KEY `Type` (`type`),
  KEY `Settled` (`settled`),
  KEY `TranDate` (`trandate`),
  KEY `TransNo` (`transno`),
  KEY `Type_2` (`type`,`transno`),
  KEY `EDISent` (`edisent`),
  KEY `salesperson` (`salesperson`),
  CONSTRAINT `debtortrans_ibfk_2` FOREIGN KEY (`type`) REFERENCES `systypes` (`typeid`),
  CONSTRAINT `debtortrans_ibfk_3` FOREIGN KEY (`prd`) REFERENCES `periods` (`periodno`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `debtortranstaxes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `debtortranstaxes` (
  `debtortransid` int(11) NOT NULL DEFAULT '0',
  `taxauthid` tinyint(4) NOT NULL DEFAULT '0',
  `taxamount` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`debtortransid`,`taxauthid`),
  KEY `taxauthid` (`taxauthid`),
  CONSTRAINT `debtortranstaxes_ibfk_1` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthorities` (`taxid`),
  CONSTRAINT `debtortranstaxes_ibfk_2` FOREIGN KEY (`debtortransid`) REFERENCES `debtortrans` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `debtortype`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `debtortype` (
  `typeid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `typename` varchar(100) NOT NULL,
  PRIMARY KEY (`typeid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `debtortypenotes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `debtortypenotes` (
  `noteid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `typeid` tinyint(4) NOT NULL DEFAULT '0',
  `href` varchar(100) NOT NULL,
  `note` varchar(200) NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `priority` varchar(20) NOT NULL,
  PRIMARY KEY (`noteid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deliverynotes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deliverynotes` (
  `deliverynotenumber` int(11) NOT NULL,
  `deliverynotelineno` tinyint(4) NOT NULL,
  `salesorderno` int(11) NOT NULL,
  `salesorderlineno` int(11) NOT NULL,
  `qtydelivered` double NOT NULL DEFAULT '0',
  `printed` tinyint(4) NOT NULL DEFAULT '0',
  `invoiced` tinyint(4) NOT NULL DEFAULT '0',
  `deliverydate` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`deliverynotenumber`,`deliverynotelineno`),
  KEY `deliverynotes_ibfk_2` (`salesorderno`,`salesorderlineno`),
  CONSTRAINT `deliverynotes_ibfk_1` FOREIGN KEY (`salesorderno`) REFERENCES `salesorders` (`orderno`),
  CONSTRAINT `deliverynotes_ibfk_2` FOREIGN KEY (`salesorderno`, `salesorderlineno`) REFERENCES `salesorderdetails` (`orderno`, `orderlineno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `departments`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `departmentid` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL DEFAULT '',
  `authoriser` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`departmentid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `discountmatrix`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discountmatrix` (
  `salestype` char(2) NOT NULL DEFAULT '',
  `discountcategory` char(2) NOT NULL DEFAULT '',
  `quantitybreak` int(11) NOT NULL DEFAULT '1',
  `discountrate` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`salestype`,`discountcategory`,`quantitybreak`),
  KEY `QuantityBreak` (`quantitybreak`),
  KEY `DiscountCategory` (`discountcategory`),
  KEY `SalesType` (`salestype`),
  CONSTRAINT `discountmatrix_ibfk_1` FOREIGN KEY (`salestype`) REFERENCES `salestypes` (`typeabbrev`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `edi_orders_seg_groups`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `edi_orders_seg_groups` (
  `seggroupno` tinyint(4) NOT NULL DEFAULT '0',
  `maxoccur` int(4) NOT NULL DEFAULT '0',
  `parentseggroup` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`seggroupno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `edi_orders_segs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `edi_orders_segs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `segtag` char(3) NOT NULL DEFAULT '',
  `seggroup` tinyint(4) NOT NULL DEFAULT '0',
  `maxoccur` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `SegTag` (`segtag`),
  KEY `SegNo` (`seggroup`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ediitemmapping`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ediitemmapping` (
  `supporcust` varchar(4) NOT NULL DEFAULT '',
  `partnercode` varchar(10) NOT NULL DEFAULT '',
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `partnerstockid` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`supporcust`,`partnercode`,`stockid`),
  KEY `PartnerCode` (`partnercode`),
  KEY `StockID` (`stockid`),
  KEY `PartnerStockID` (`partnerstockid`),
  KEY `SuppOrCust` (`supporcust`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `edimessageformat`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `edimessageformat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partnercode` varchar(10) NOT NULL DEFAULT '',
  `messagetype` varchar(6) NOT NULL DEFAULT '',
  `section` varchar(7) NOT NULL DEFAULT '',
  `sequenceno` int(11) NOT NULL DEFAULT '0',
  `linetext` varchar(70) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `PartnerCode` (`partnercode`,`messagetype`,`sequenceno`),
  KEY `Section` (`section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emailsettings`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emailsettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host` varchar(30) NOT NULL,
  `port` char(5) NOT NULL,
  `heloaddress` varchar(20) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(30) DEFAULT NULL,
  `timeout` int(11) DEFAULT '5',
  `companyname` varchar(50) DEFAULT NULL,
  `auth` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `factorcompanies`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `factorcompanies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coyname` varchar(50) NOT NULL DEFAULT '',
  `address1` varchar(40) NOT NULL DEFAULT '',
  `address2` varchar(40) NOT NULL DEFAULT '',
  `address3` varchar(40) NOT NULL DEFAULT '',
  `address4` varchar(40) NOT NULL DEFAULT '',
  `address5` varchar(20) NOT NULL DEFAULT '',
  `address6` varchar(15) NOT NULL DEFAULT '',
  `contact` varchar(25) NOT NULL DEFAULT '',
  `telephone` varchar(25) NOT NULL DEFAULT '',
  `fax` varchar(25) NOT NULL DEFAULT '',
  `email` varchar(55) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `factor_name` (`coyname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fixedassetcategories`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fixedassetcategories` (
  `categoryid` char(6) NOT NULL DEFAULT '',
  `categorydescription` char(20) NOT NULL DEFAULT '',
  `costact` varchar(20) NOT NULL DEFAULT '0',
  `depnact` varchar(20) NOT NULL DEFAULT '0',
  `disposalact` varchar(20) NOT NULL DEFAULT '80000',
  `accumdepnact` varchar(20) NOT NULL DEFAULT '0',
  `defaultdepnrate` double NOT NULL DEFAULT '0.2',
  `defaultdepntype` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`categoryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fixedassetlocations`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fixedassetlocations` (
  `locationid` char(6) NOT NULL DEFAULT '',
  `locationdescription` char(20) NOT NULL DEFAULT '',
  `parentlocationid` char(6) DEFAULT '',
  PRIMARY KEY (`locationid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fixedassets`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fixedassets` (
  `assetid` int(11) NOT NULL AUTO_INCREMENT,
  `serialno` varchar(30) NOT NULL DEFAULT '',
  `barcode` varchar(20) NOT NULL,
  `assetlocation` varchar(6) NOT NULL DEFAULT '',
  `cost` double NOT NULL DEFAULT '0',
  `accumdepn` double NOT NULL DEFAULT '0',
  `datepurchased` date NOT NULL DEFAULT '0000-00-00',
  `disposalproceeds` double NOT NULL DEFAULT '0',
  `assetcategoryid` varchar(6) NOT NULL DEFAULT '',
  `description` varchar(50) NOT NULL DEFAULT '',
  `longdescription` text NOT NULL,
  `depntype` int(11) NOT NULL DEFAULT '1',
  `depnrate` double NOT NULL,
  `disposaldate` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`assetid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fixedassettasks`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fixedassettasks` (
  `taskid` int(11) NOT NULL AUTO_INCREMENT,
  `assetid` int(11) NOT NULL,
  `taskdescription` text NOT NULL,
  `frequencydays` int(11) NOT NULL DEFAULT '365',
  `lastcompleted` date NOT NULL,
  `userresponsible` varchar(20) NOT NULL,
  `manager` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`taskid`),
  KEY `assetid` (`assetid`),
  KEY `userresponsible` (`userresponsible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fixedassettrans`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fixedassettrans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assetid` int(11) NOT NULL,
  `transtype` tinyint(4) NOT NULL,
  `transdate` date NOT NULL,
  `transno` int(11) NOT NULL,
  `periodno` smallint(6) NOT NULL,
  `inputdate` date NOT NULL,
  `fixedassettranstype` varchar(8) NOT NULL,
  `amount` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `assetid` (`assetid`,`transtype`,`transno`),
  KEY `inputdate` (`inputdate`),
  KEY `transdate` (`transdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `freightcosts`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `freightcosts` (
  `shipcostfromid` int(11) NOT NULL AUTO_INCREMENT,
  `locationfrom` varchar(5) NOT NULL DEFAULT '',
  `destinationcountry` varchar(40) NOT NULL,
  `destination` varchar(40) NOT NULL DEFAULT '',
  `shipperid` int(11) NOT NULL DEFAULT '0',
  `cubrate` double NOT NULL DEFAULT '0',
  `kgrate` double NOT NULL DEFAULT '0',
  `maxkgs` double NOT NULL DEFAULT '999999',
  `maxcub` double NOT NULL DEFAULT '999999',
  `fixedprice` double NOT NULL DEFAULT '0',
  `minimumchg` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`shipcostfromid`),
  KEY `Destination` (`destination`),
  KEY `LocationFrom` (`locationfrom`),
  KEY `ShipperID` (`shipperid`),
  KEY `Destination_2` (`destination`,`locationfrom`,`shipperid`),
  CONSTRAINT `freightcosts_ibfk_1` FOREIGN KEY (`locationfrom`) REFERENCES `locations` (`loccode`),
  CONSTRAINT `freightcosts_ibfk_2` FOREIGN KEY (`shipperid`) REFERENCES `shippers` (`shipper_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geocode_param`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geocode_param` (
  `geocodeid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `geocode_key` varchar(200) NOT NULL DEFAULT '',
  `center_long` varchar(20) NOT NULL DEFAULT '',
  `center_lat` varchar(20) NOT NULL DEFAULT '',
  `map_height` varchar(10) NOT NULL DEFAULT '',
  `map_width` varchar(10) NOT NULL DEFAULT '',
  `map_host` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`geocodeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gltrans`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gltrans` (
  `counterindex` int(11) NOT NULL AUTO_INCREMENT,
  `type` smallint(6) NOT NULL DEFAULT '0',
  `typeno` bigint(16) NOT NULL DEFAULT '1',
  `chequeno` int(11) NOT NULL DEFAULT '0',
  `trandate` date NOT NULL DEFAULT '0000-00-00',
  `periodno` smallint(6) NOT NULL DEFAULT '0',
  `account` varchar(20) NOT NULL DEFAULT '0',
  `narrative` varchar(200) NOT NULL DEFAULT '',
  `amount` double NOT NULL DEFAULT '0',
  `posted` tinyint(4) NOT NULL DEFAULT '0',
  `jobref` varchar(20) NOT NULL DEFAULT '',
  `tag` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`counterindex`),
  KEY `Account` (`account`),
  KEY `ChequeNo` (`chequeno`),
  KEY `PeriodNo` (`periodno`),
  KEY `Posted` (`posted`),
  KEY `TranDate` (`trandate`),
  KEY `TypeNo` (`typeno`),
  KEY `Type_and_Number` (`type`,`typeno`),
  KEY `JobRef` (`jobref`),
  KEY `tag` (`tag`),
  CONSTRAINT `gltrans_ibfk_1` FOREIGN KEY (`account`) REFERENCES `chartmaster` (`accountcode`),
  CONSTRAINT `gltrans_ibfk_2` FOREIGN KEY (`type`) REFERENCES `systypes` (`typeid`),
  CONSTRAINT `gltrans_ibfk_3` FOREIGN KEY (`periodno`) REFERENCES `periods` (`periodno`)
) ENGINE=InnoDB AUTO_INCREMENT=186 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `grns`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grns` (
  `grnbatch` smallint(6) NOT NULL DEFAULT '0',
  `grnno` int(11) NOT NULL AUTO_INCREMENT,
  `podetailitem` int(11) NOT NULL DEFAULT '0',
  `itemcode` varchar(20) NOT NULL DEFAULT '',
  `deliverydate` date NOT NULL DEFAULT '0000-00-00',
  `itemdescription` varchar(100) NOT NULL DEFAULT '',
  `qtyrecd` double NOT NULL DEFAULT '0',
  `quantityinv` double NOT NULL DEFAULT '0',
  `supplierid` varchar(10) NOT NULL DEFAULT '',
  `stdcostunit` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`grnno`),
  KEY `DeliveryDate` (`deliverydate`),
  KEY `ItemCode` (`itemcode`),
  KEY `PODetailItem` (`podetailitem`),
  KEY `SupplierID` (`supplierid`),
  CONSTRAINT `grns_ibfk_1` FOREIGN KEY (`supplierid`) REFERENCES `suppliers` (`supplierid`),
  CONSTRAINT `grns_ibfk_2` FOREIGN KEY (`podetailitem`) REFERENCES `purchorderdetails` (`podetailitem`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `holdreasons`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holdreasons` (
  `reasoncode` smallint(6) NOT NULL DEFAULT '1',
  `reasondescription` char(30) NOT NULL DEFAULT '',
  `dissallowinvoices` tinyint(4) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`reasoncode`),
  KEY `ReasonDescription` (`reasondescription`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `internalstockcatrole`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `internalstockcatrole` (
  `categoryid` varchar(6) NOT NULL,
  `secroleid` int(11) NOT NULL,
  PRIMARY KEY (`categoryid`,`secroleid`),
  KEY `internalstockcatrole_ibfk_1` (`categoryid`),
  KEY `internalstockcatrole_ibfk_2` (`secroleid`),
  CONSTRAINT `internalstockcatrole_ibfk_1` FOREIGN KEY (`categoryid`) REFERENCES `stockcategory` (`categoryid`),
  CONSTRAINT `internalstockcatrole_ibfk_2` FOREIGN KEY (`secroleid`) REFERENCES `securityroles` (`secroleid`),
  CONSTRAINT `internalstockcatrole_ibfk_3` FOREIGN KEY (`categoryid`) REFERENCES `stockcategory` (`categoryid`),
  CONSTRAINT `internalstockcatrole_ibfk_4` FOREIGN KEY (`secroleid`) REFERENCES `securityroles` (`secroleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `labelfields`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `labelfields` (
  `labelfieldid` int(11) NOT NULL AUTO_INCREMENT,
  `labelid` tinyint(4) NOT NULL,
  `fieldvalue` varchar(20) NOT NULL,
  `vpos` double NOT NULL DEFAULT '0',
  `hpos` double NOT NULL DEFAULT '0',
  `fontsize` tinyint(4) NOT NULL,
  `barcode` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`labelfieldid`),
  KEY `labelid` (`labelid`),
  KEY `vpos` (`vpos`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `labels`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `labels` (
  `labelid` tinyint(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL,
  `pagewidth` double NOT NULL DEFAULT '0',
  `pageheight` double NOT NULL DEFAULT '0',
  `height` double NOT NULL DEFAULT '0',
  `width` double NOT NULL DEFAULT '0',
  `topmargin` double NOT NULL DEFAULT '0',
  `leftmargin` double NOT NULL DEFAULT '0',
  `rowheight` double NOT NULL DEFAULT '0',
  `columnwidth` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`labelid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lastcostrollup`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lastcostrollup` (
  `stockid` char(20) NOT NULL DEFAULT '',
  `totalonhand` double NOT NULL DEFAULT '0',
  `matcost` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `labcost` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `oheadcost` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `categoryid` char(6) NOT NULL DEFAULT '',
  `stockact` varchar(20) NOT NULL DEFAULT '0',
  `adjglact` varchar(20) NOT NULL DEFAULT '0',
  `newmatcost` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `newlabcost` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `newoheadcost` decimal(20,4) NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `locations`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locations` (
  `loccode` varchar(5) NOT NULL DEFAULT '',
  `locationname` varchar(50) NOT NULL DEFAULT '',
  `deladd1` varchar(40) NOT NULL DEFAULT '',
  `deladd2` varchar(40) NOT NULL DEFAULT '',
  `deladd3` varchar(40) NOT NULL DEFAULT '',
  `deladd4` varchar(40) NOT NULL DEFAULT '',
  `deladd5` varchar(20) NOT NULL DEFAULT '',
  `deladd6` varchar(15) NOT NULL DEFAULT '',
  `tel` varchar(30) NOT NULL DEFAULT '',
  `fax` varchar(30) NOT NULL DEFAULT '',
  `email` varchar(55) NOT NULL DEFAULT '',
  `contact` varchar(30) NOT NULL DEFAULT '',
  `taxprovinceid` tinyint(4) NOT NULL DEFAULT '1',
  `cashsalecustomer` varchar(10) DEFAULT '',
  `managed` int(11) DEFAULT '0',
  `cashsalebranch` varchar(10) DEFAULT '',
  `internalrequest` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Allow (1) or not (0) internal request from this location',
  `usedforwo` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`loccode`),
  UNIQUE KEY `locationname` (`locationname`),
  KEY `taxprovinceid` (`taxprovinceid`),
  CONSTRAINT `locations_ibfk_1` FOREIGN KEY (`taxprovinceid`) REFERENCES `taxprovinces` (`taxprovinceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `locationusers`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locationusers` (
  `loccode` varchar(5) NOT NULL,
  `userid` varchar(20) NOT NULL,
  `canview` tinyint(4) NOT NULL DEFAULT '0',
  `canupd` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`loccode`,`userid`),
  KEY `UserId` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `locstock`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locstock` (
  `loccode` varchar(5) NOT NULL DEFAULT '',
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `quantity` double NOT NULL DEFAULT '0',
  `reorderlevel` bigint(20) NOT NULL DEFAULT '0',
  `bin` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`loccode`,`stockid`),
  KEY `StockID` (`stockid`),
  KEY `bin` (`bin`),
  CONSTRAINT `locstock_ibfk_1` FOREIGN KEY (`loccode`) REFERENCES `locations` (`loccode`),
  CONSTRAINT `locstock_ibfk_2` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `loctransfers`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loctransfers` (
  `reference` int(11) NOT NULL DEFAULT '0',
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `shipqty` double NOT NULL DEFAULT '0',
  `recqty` double NOT NULL DEFAULT '0',
  `shipdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `recdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `shiploc` varchar(7) NOT NULL DEFAULT '',
  `recloc` varchar(7) NOT NULL DEFAULT '',
  KEY `Reference` (`reference`,`stockid`),
  KEY `ShipLoc` (`shiploc`),
  KEY `RecLoc` (`recloc`),
  KEY `StockID` (`stockid`),
  CONSTRAINT `loctransfers_ibfk_1` FOREIGN KEY (`shiploc`) REFERENCES `locations` (`loccode`),
  CONSTRAINT `loctransfers_ibfk_2` FOREIGN KEY (`recloc`) REFERENCES `locations` (`loccode`),
  CONSTRAINT `loctransfers_ibfk_3` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores Shipments To And From Locations';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mailgroupdetails`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mailgroupdetails` (
  `groupname` varchar(100) NOT NULL,
  `userid` varchar(20) NOT NULL,
  KEY `userid` (`userid`),
  KEY `groupname` (`groupname`),
  CONSTRAINT `mailgroupdetails_ibfk_1` FOREIGN KEY (`groupname`) REFERENCES `mailgroups` (`groupname`),
  CONSTRAINT `mailgroupdetails_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `www_users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mailgroups`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mailgroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupname` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `groupname` (`groupname`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturers`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manufacturers` (
  `manufacturers_id` int(11) NOT NULL AUTO_INCREMENT,
  `manufacturers_name` varchar(32) NOT NULL,
  `manufacturers_url` varchar(50) NOT NULL DEFAULT '',
  `manufacturers_image` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`manufacturers_id`),
  KEY `manufacturers_name` (`manufacturers_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mrpcalendar`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mrpcalendar` (
  `calendardate` date NOT NULL,
  `daynumber` int(6) NOT NULL,
  `manufacturingflag` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`calendardate`),
  KEY `daynumber` (`daynumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mrpdemands`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mrpdemands` (
  `demandid` int(11) NOT NULL AUTO_INCREMENT,
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `mrpdemandtype` varchar(6) NOT NULL DEFAULT '',
  `quantity` double NOT NULL DEFAULT '0',
  `duedate` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`demandid`),
  KEY `StockID` (`stockid`),
  KEY `mrpdemands_ibfk_1` (`mrpdemandtype`),
  CONSTRAINT `mrpdemands_ibfk_1` FOREIGN KEY (`mrpdemandtype`) REFERENCES `mrpdemandtypes` (`mrpdemandtype`),
  CONSTRAINT `mrpdemands_ibfk_2` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mrpdemandtypes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mrpdemandtypes` (
  `mrpdemandtype` varchar(6) NOT NULL DEFAULT '',
  `description` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`mrpdemandtype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mrpplannedorders`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mrpplannedorders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part` char(20) DEFAULT NULL,
  `duedate` date DEFAULT NULL,
  `supplyquantity` double DEFAULT NULL,
  `ordertype` varchar(6) DEFAULT NULL,
  `orderno` int(11) DEFAULT NULL,
  `mrpdate` date DEFAULT NULL,
  `updateflag` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offers`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offers` (
  `offerid` int(11) NOT NULL AUTO_INCREMENT,
  `tenderid` int(11) NOT NULL DEFAULT '0',
  `supplierid` varchar(10) NOT NULL DEFAULT '',
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `quantity` double NOT NULL DEFAULT '0',
  `uom` varchar(15) NOT NULL DEFAULT '',
  `price` double NOT NULL DEFAULT '0',
  `expirydate` date NOT NULL DEFAULT '0000-00-00',
  `currcode` char(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`offerid`),
  KEY `offers_ibfk_1` (`supplierid`),
  KEY `offers_ibfk_2` (`stockid`),
  CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`supplierid`) REFERENCES `suppliers` (`supplierid`),
  CONSTRAINT `offers_ibfk_2` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orderdeliverydifferenceslog`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orderdeliverydifferenceslog` (
  `orderno` int(11) NOT NULL DEFAULT '0',
  `invoiceno` int(11) NOT NULL DEFAULT '0',
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `quantitydiff` double NOT NULL DEFAULT '0',
  `debtorno` varchar(10) NOT NULL DEFAULT '',
  `branch` varchar(10) NOT NULL DEFAULT '',
  `can_or_bo` char(3) NOT NULL DEFAULT 'CAN',
  KEY `StockID` (`stockid`),
  KEY `DebtorNo` (`debtorno`,`branch`),
  KEY `Can_or_BO` (`can_or_bo`),
  KEY `OrderNo` (`orderno`),
  CONSTRAINT `orderdeliverydifferenceslog_ibfk_1` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `orderdeliverydifferenceslog_ibfk_2` FOREIGN KEY (`debtorno`, `branch`) REFERENCES `custbranch` (`debtorno`, `branchcode`),
  CONSTRAINT `orderdeliverydifferenceslog_ibfk_3` FOREIGN KEY (`orderno`) REFERENCES `salesorders` (`orderno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paymentmethods`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paymentmethods` (
  `paymentid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `paymentname` varchar(15) NOT NULL DEFAULT '',
  `paymenttype` int(11) NOT NULL DEFAULT '1',
  `receipttype` int(11) NOT NULL DEFAULT '1',
  `usepreprintedstationery` tinyint(4) NOT NULL DEFAULT '0',
  `opencashdrawer` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`paymentid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paymentterms`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paymentterms` (
  `termsindicator` char(2) NOT NULL DEFAULT '',
  `terms` char(40) NOT NULL DEFAULT '',
  `daysbeforedue` smallint(6) NOT NULL DEFAULT '0',
  `dayinfollowingmonth` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`termsindicator`),
  KEY `DaysBeforeDue` (`daysbeforedue`),
  KEY `DayInFollowingMonth` (`dayinfollowingmonth`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pcashdetails`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pcashdetails` (
  `counterindex` int(20) NOT NULL AUTO_INCREMENT,
  `tabcode` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `codeexpense` varchar(20) NOT NULL,
  `amount` double NOT NULL,
  `authorized` date NOT NULL COMMENT 'date cash assigment was revised and authorized by authorizer from tabs table',
  `posted` tinyint(4) NOT NULL COMMENT 'has (or has not) been posted into gltrans',
  `notes` text NOT NULL,
  `receipt` text COMMENT 'filename or path to scanned receipt or code of receipt to find physical receipt if tax guys or auditors show up',
  PRIMARY KEY (`counterindex`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pcexpenses`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pcexpenses` (
  `codeexpense` varchar(20) NOT NULL COMMENT 'code for the group',
  `description` varchar(50) NOT NULL COMMENT 'text description, e.g. meals, train tickets, fuel, etc',
  `glaccount` varchar(20) NOT NULL DEFAULT '0',
  `tag` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`codeexpense`),
  KEY `glaccount` (`glaccount`),
  CONSTRAINT `pcexpenses_ibfk_1` FOREIGN KEY (`glaccount`) REFERENCES `chartmaster` (`accountcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pctabexpenses`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pctabexpenses` (
  `typetabcode` varchar(20) NOT NULL,
  `codeexpense` varchar(20) NOT NULL,
  KEY `typetabcode` (`typetabcode`),
  KEY `codeexpense` (`codeexpense`),
  CONSTRAINT `pctabexpenses_ibfk_1` FOREIGN KEY (`typetabcode`) REFERENCES `pctypetabs` (`typetabcode`),
  CONSTRAINT `pctabexpenses_ibfk_2` FOREIGN KEY (`codeexpense`) REFERENCES `pcexpenses` (`codeexpense`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pctabs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pctabs` (
  `tabcode` varchar(20) NOT NULL,
  `usercode` varchar(20) NOT NULL COMMENT 'code of user employee from www_users',
  `typetabcode` varchar(20) NOT NULL,
  `currency` char(3) NOT NULL,
  `tablimit` double NOT NULL,
  `assigner` varchar(20) NOT NULL COMMENT 'Cash assigner for the tab',
  `authorizer` varchar(20) NOT NULL COMMENT 'code of user from www_users',
  `glaccountassignment` varchar(20) NOT NULL DEFAULT '0',
  `glaccountpcash` varchar(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tabcode`),
  KEY `usercode` (`usercode`),
  KEY `typetabcode` (`typetabcode`),
  KEY `currency` (`currency`),
  KEY `authorizer` (`authorizer`),
  KEY `glaccountassignment` (`glaccountassignment`),
  CONSTRAINT `pctabs_ibfk_1` FOREIGN KEY (`usercode`) REFERENCES `www_users` (`userid`),
  CONSTRAINT `pctabs_ibfk_2` FOREIGN KEY (`typetabcode`) REFERENCES `pctypetabs` (`typetabcode`),
  CONSTRAINT `pctabs_ibfk_3` FOREIGN KEY (`currency`) REFERENCES `currencies` (`currabrev`),
  CONSTRAINT `pctabs_ibfk_4` FOREIGN KEY (`authorizer`) REFERENCES `www_users` (`userid`),
  CONSTRAINT `pctabs_ibfk_5` FOREIGN KEY (`glaccountassignment`) REFERENCES `chartmaster` (`accountcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pctypetabs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pctypetabs` (
  `typetabcode` varchar(20) NOT NULL COMMENT 'code for the type of petty cash tab',
  `typetabdescription` varchar(50) NOT NULL COMMENT 'text description, e.g. tab for CEO',
  PRIMARY KEY (`typetabcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `periods`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `periods` (
  `periodno` smallint(6) NOT NULL DEFAULT '0',
  `lastdate_in_period` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`periodno`),
  KEY `LastDate_in_Period` (`lastdate_in_period`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pickinglistdetails`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pickinglistdetails` (
  `pickinglistno` int(11) NOT NULL DEFAULT '0',
  `pickinglistlineno` int(11) NOT NULL DEFAULT '0',
  `orderlineno` int(11) NOT NULL DEFAULT '0',
  `qtyexpected` double NOT NULL DEFAULT '0',
  `qtypicked` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`pickinglistno`,`pickinglistlineno`),
  CONSTRAINT `pickinglistdetails_ibfk_1` FOREIGN KEY (`pickinglistno`) REFERENCES `pickinglists` (`pickinglistno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pickinglists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pickinglists` (
  `pickinglistno` int(11) NOT NULL DEFAULT '0',
  `orderno` int(11) NOT NULL DEFAULT '0',
  `pickinglistdate` date NOT NULL DEFAULT '0000-00-00',
  `dateprinted` date NOT NULL DEFAULT '0000-00-00',
  `deliverynotedate` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`pickinglistno`),
  KEY `pickinglists_ibfk_1` (`orderno`),
  CONSTRAINT `pickinglists_ibfk_1` FOREIGN KEY (`orderno`) REFERENCES `salesorders` (`orderno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pricematrix`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pricematrix` (
  `salestype` char(2) NOT NULL DEFAULT '',
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `quantitybreak` int(11) NOT NULL DEFAULT '1',
  `price` double NOT NULL DEFAULT '0',
  `currabrev` char(3) NOT NULL DEFAULT '',
  `startdate` date NOT NULL DEFAULT '0000-00-00',
  `enddate` date NOT NULL DEFAULT '9999-12-31',
  PRIMARY KEY (`salestype`,`stockid`,`currabrev`,`quantitybreak`,`startdate`,`enddate`),
  KEY `SalesType` (`salestype`),
  KEY `currabrev` (`currabrev`),
  KEY `stockid` (`stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prices`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prices` (
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `typeabbrev` char(2) NOT NULL DEFAULT '',
  `currabrev` char(3) NOT NULL DEFAULT '',
  `debtorno` varchar(10) NOT NULL DEFAULT '',
  `price` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `branchcode` varchar(10) NOT NULL DEFAULT '',
  `startdate` date NOT NULL DEFAULT '0000-00-00',
  `enddate` date NOT NULL DEFAULT '9999-12-31',
  PRIMARY KEY (`stockid`,`typeabbrev`,`currabrev`,`debtorno`,`branchcode`,`startdate`,`enddate`),
  KEY `CurrAbrev` (`currabrev`),
  KEY `DebtorNo` (`debtorno`),
  KEY `StockID` (`stockid`),
  KEY `TypeAbbrev` (`typeabbrev`),
  CONSTRAINT `prices_ibfk_1` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `prices_ibfk_2` FOREIGN KEY (`currabrev`) REFERENCES `currencies` (`currabrev`),
  CONSTRAINT `prices_ibfk_3` FOREIGN KEY (`typeabbrev`) REFERENCES `salestypes` (`typeabbrev`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prodspecs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prodspecs` (
  `keyval` varchar(25) NOT NULL,
  `testid` int(11) NOT NULL,
  `defaultvalue` varchar(150) NOT NULL DEFAULT '',
  `targetvalue` varchar(30) NOT NULL DEFAULT '',
  `rangemin` float DEFAULT NULL,
  `rangemax` float DEFAULT NULL,
  `showoncert` tinyint(11) NOT NULL DEFAULT '1',
  `showonspec` tinyint(4) NOT NULL DEFAULT '1',
  `showontestplan` tinyint(4) NOT NULL DEFAULT '1',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`keyval`,`testid`),
  KEY `testid` (`testid`),
  CONSTRAINT `prodspecs_ibfk_1` FOREIGN KEY (`testid`) REFERENCES `qatests` (`testid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchdata`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchdata` (
  `supplierno` char(10) NOT NULL DEFAULT '',
  `stockid` char(20) NOT NULL DEFAULT '',
  `price` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `suppliersuom` char(50) NOT NULL DEFAULT '',
  `conversionfactor` double NOT NULL DEFAULT '1',
  `supplierdescription` char(50) NOT NULL DEFAULT '',
  `leadtime` smallint(6) NOT NULL DEFAULT '1',
  `preferred` tinyint(4) NOT NULL DEFAULT '0',
  `effectivefrom` date NOT NULL,
  `suppliers_partno` varchar(50) NOT NULL DEFAULT '',
  `minorderqty` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`supplierno`,`stockid`,`effectivefrom`),
  KEY `StockID` (`stockid`),
  KEY `SupplierNo` (`supplierno`),
  KEY `Preferred` (`preferred`),
  CONSTRAINT `purchdata_ibfk_1` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `purchdata_ibfk_2` FOREIGN KEY (`supplierno`) REFERENCES `suppliers` (`supplierid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchorderauth`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchorderauth` (
  `userid` varchar(20) NOT NULL DEFAULT '',
  `currabrev` char(3) NOT NULL DEFAULT '',
  `cancreate` smallint(2) NOT NULL DEFAULT '0',
  `authlevel` double NOT NULL DEFAULT '0',
  `offhold` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`,`currabrev`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchorderdetails`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchorderdetails` (
  `podetailitem` int(11) NOT NULL AUTO_INCREMENT,
  `orderno` int(11) NOT NULL DEFAULT '0',
  `itemcode` varchar(20) NOT NULL DEFAULT '',
  `deliverydate` date NOT NULL DEFAULT '0000-00-00',
  `itemdescription` varchar(100) NOT NULL,
  `glcode` varchar(20) NOT NULL DEFAULT '0',
  `qtyinvoiced` double NOT NULL DEFAULT '0',
  `unitprice` double NOT NULL DEFAULT '0',
  `actprice` double NOT NULL DEFAULT '0',
  `stdcostunit` double NOT NULL DEFAULT '0',
  `quantityord` double NOT NULL DEFAULT '0',
  `quantityrecd` double NOT NULL DEFAULT '0',
  `shiptref` int(11) NOT NULL DEFAULT '0',
  `jobref` varchar(20) NOT NULL DEFAULT '',
  `completed` tinyint(4) NOT NULL DEFAULT '0',
  `suppliersunit` varchar(50) DEFAULT NULL,
  `suppliers_partno` varchar(50) NOT NULL DEFAULT '',
  `assetid` int(11) NOT NULL DEFAULT '0',
  `conversionfactor` double NOT NULL DEFAULT '1',
  PRIMARY KEY (`podetailitem`),
  KEY `DeliveryDate` (`deliverydate`),
  KEY `GLCode` (`glcode`),
  KEY `ItemCode` (`itemcode`),
  KEY `JobRef` (`jobref`),
  KEY `OrderNo` (`orderno`),
  KEY `ShiptRef` (`shiptref`),
  KEY `Completed` (`completed`),
  CONSTRAINT `purchorderdetails_ibfk_1` FOREIGN KEY (`orderno`) REFERENCES `purchorders` (`orderno`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchorders`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchorders` (
  `orderno` int(11) NOT NULL AUTO_INCREMENT,
  `supplierno` varchar(10) NOT NULL DEFAULT '',
  `comments` longblob,
  `orddate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `rate` double NOT NULL DEFAULT '1',
  `dateprinted` datetime DEFAULT NULL,
  `allowprint` tinyint(4) NOT NULL DEFAULT '1',
  `initiator` varchar(20) DEFAULT NULL,
  `requisitionno` varchar(15) DEFAULT NULL,
  `intostocklocation` varchar(5) NOT NULL DEFAULT '',
  `deladd1` varchar(40) NOT NULL DEFAULT '',
  `deladd2` varchar(40) NOT NULL DEFAULT '',
  `deladd3` varchar(40) NOT NULL DEFAULT '',
  `deladd4` varchar(40) NOT NULL DEFAULT '',
  `deladd5` varchar(20) NOT NULL DEFAULT '',
  `deladd6` varchar(15) NOT NULL DEFAULT '',
  `tel` varchar(30) NOT NULL DEFAULT '',
  `suppdeladdress1` varchar(40) NOT NULL DEFAULT '',
  `suppdeladdress2` varchar(40) NOT NULL DEFAULT '',
  `suppdeladdress3` varchar(40) NOT NULL DEFAULT '',
  `suppdeladdress4` varchar(40) NOT NULL DEFAULT '',
  `suppdeladdress5` varchar(20) NOT NULL DEFAULT '',
  `suppdeladdress6` varchar(15) NOT NULL DEFAULT '',
  `suppliercontact` varchar(30) NOT NULL DEFAULT '',
  `supptel` varchar(30) NOT NULL DEFAULT '',
  `contact` varchar(30) NOT NULL DEFAULT '',
  `version` decimal(3,2) NOT NULL DEFAULT '1.00',
  `revised` date NOT NULL DEFAULT '0000-00-00',
  `realorderno` varchar(16) NOT NULL DEFAULT '',
  `deliveryby` varchar(100) NOT NULL DEFAULT '',
  `deliverydate` date NOT NULL DEFAULT '0000-00-00',
  `status` varchar(12) NOT NULL DEFAULT '',
  `stat_comment` text NOT NULL,
  `paymentterms` char(2) NOT NULL DEFAULT '',
  `port` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`orderno`),
  KEY `OrdDate` (`orddate`),
  KEY `SupplierNo` (`supplierno`),
  KEY `IntoStockLocation` (`intostocklocation`),
  KEY `AllowPrintPO` (`allowprint`),
  CONSTRAINT `purchorders_ibfk_1` FOREIGN KEY (`supplierno`) REFERENCES `suppliers` (`supplierid`),
  CONSTRAINT `purchorders_ibfk_2` FOREIGN KEY (`intostocklocation`) REFERENCES `locations` (`loccode`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `qasamples`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qasamples` (
  `sampleid` int(11) NOT NULL AUTO_INCREMENT,
  `prodspeckey` varchar(25) NOT NULL DEFAULT '',
  `lotkey` varchar(25) NOT NULL DEFAULT '',
  `identifier` varchar(10) NOT NULL DEFAULT '',
  `createdby` varchar(15) NOT NULL DEFAULT '',
  `sampledate` date NOT NULL DEFAULT '0000-00-00',
  `comments` varchar(255) NOT NULL DEFAULT '',
  `cert` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sampleid`),
  KEY `prodspeckey` (`prodspeckey`,`lotkey`),
  CONSTRAINT `qasamples_ibfk_1` FOREIGN KEY (`prodspeckey`) REFERENCES `prodspecs` (`keyval`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `qatests`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qatests` (
  `testid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `method` varchar(20) DEFAULT NULL,
  `groupby` varchar(20) DEFAULT NULL,
  `units` varchar(20) NOT NULL,
  `type` varchar(15) NOT NULL,
  `defaultvalue` varchar(150) NOT NULL DEFAULT '''''',
  `numericvalue` tinyint(4) NOT NULL DEFAULT '0',
  `showoncert` int(11) NOT NULL DEFAULT '1',
  `showonspec` int(11) NOT NULL DEFAULT '1',
  `showontestplan` tinyint(4) NOT NULL DEFAULT '1',
  `active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`testid`),
  KEY `name` (`name`),
  KEY `groupname` (`groupby`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recurringsalesorders`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recurringsalesorders` (
  `recurrorderno` int(11) NOT NULL AUTO_INCREMENT,
  `debtorno` varchar(10) NOT NULL DEFAULT '',
  `branchcode` varchar(10) NOT NULL DEFAULT '',
  `customerref` varchar(50) NOT NULL DEFAULT '',
  `buyername` varchar(50) DEFAULT NULL,
  `comments` longblob,
  `orddate` date NOT NULL DEFAULT '0000-00-00',
  `ordertype` char(2) NOT NULL DEFAULT '',
  `shipvia` int(11) NOT NULL DEFAULT '0',
  `deladd1` varchar(40) NOT NULL DEFAULT '',
  `deladd2` varchar(40) NOT NULL DEFAULT '',
  `deladd3` varchar(40) NOT NULL DEFAULT '',
  `deladd4` varchar(40) DEFAULT NULL,
  `deladd5` varchar(20) NOT NULL DEFAULT '',
  `deladd6` varchar(15) NOT NULL DEFAULT '',
  `contactphone` varchar(25) DEFAULT NULL,
  `contactemail` varchar(25) DEFAULT NULL,
  `deliverto` varchar(40) NOT NULL DEFAULT '',
  `freightcost` double NOT NULL DEFAULT '0',
  `fromstkloc` varchar(5) NOT NULL DEFAULT '',
  `lastrecurrence` date NOT NULL DEFAULT '0000-00-00',
  `stopdate` date NOT NULL DEFAULT '0000-00-00',
  `frequency` tinyint(4) NOT NULL DEFAULT '1',
  `autoinvoice` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`recurrorderno`),
  KEY `debtorno` (`debtorno`),
  KEY `orddate` (`orddate`),
  KEY `ordertype` (`ordertype`),
  KEY `locationindex` (`fromstkloc`),
  KEY `branchcode` (`branchcode`,`debtorno`),
  CONSTRAINT `recurringsalesorders_ibfk_1` FOREIGN KEY (`branchcode`, `debtorno`) REFERENCES `custbranch` (`branchcode`, `debtorno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recurrsalesorderdetails`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recurrsalesorderdetails` (
  `recurrorderno` int(11) NOT NULL DEFAULT '0',
  `stkcode` varchar(20) NOT NULL DEFAULT '',
  `unitprice` double NOT NULL DEFAULT '0',
  `quantity` double NOT NULL DEFAULT '0',
  `discountpercent` double NOT NULL DEFAULT '0',
  `narrative` text NOT NULL,
  KEY `orderno` (`recurrorderno`),
  KEY `stkcode` (`stkcode`),
  CONSTRAINT `recurrsalesorderdetails_ibfk_1` FOREIGN KEY (`recurrorderno`) REFERENCES `recurringsalesorders` (`recurrorderno`),
  CONSTRAINT `recurrsalesorderdetails_ibfk_2` FOREIGN KEY (`stkcode`) REFERENCES `stockmaster` (`stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relateditems`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relateditems` (
  `stockid` varchar(20) CHARACTER SET utf8 NOT NULL,
  `related` varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`stockid`,`related`),
  UNIQUE KEY `Related` (`related`,`stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reportcolumns`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reportcolumns` (
  `reportid` smallint(6) NOT NULL DEFAULT '0',
  `colno` smallint(6) NOT NULL DEFAULT '0',
  `heading1` varchar(15) NOT NULL DEFAULT '',
  `heading2` varchar(15) DEFAULT NULL,
  `calculation` tinyint(1) NOT NULL DEFAULT '0',
  `periodfrom` smallint(6) DEFAULT NULL,
  `periodto` smallint(6) DEFAULT NULL,
  `datatype` varchar(15) DEFAULT NULL,
  `colnumerator` tinyint(4) DEFAULT NULL,
  `coldenominator` tinyint(4) DEFAULT NULL,
  `calcoperator` char(1) DEFAULT NULL,
  `budgetoractual` tinyint(1) NOT NULL DEFAULT '0',
  `valformat` char(1) NOT NULL DEFAULT 'N',
  `constant` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`reportid`,`colno`),
  CONSTRAINT `reportcolumns_ibfk_1` FOREIGN KEY (`reportid`) REFERENCES `reportheaders` (`reportid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reportfields`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reportfields` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `reportid` int(5) NOT NULL DEFAULT '0',
  `entrytype` varchar(15) NOT NULL DEFAULT '',
  `seqnum` int(3) NOT NULL DEFAULT '0',
  `fieldname` varchar(80) NOT NULL DEFAULT '',
  `displaydesc` varchar(25) NOT NULL DEFAULT '',
  `visible` enum('1','0') NOT NULL DEFAULT '1',
  `columnbreak` enum('1','0') NOT NULL DEFAULT '1',
  `params` text,
  PRIMARY KEY (`id`),
  KEY `reportid` (`reportid`)
) ENGINE=MyISAM AUTO_INCREMENT=1805 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reportheaders`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reportheaders` (
  `reportid` smallint(6) NOT NULL AUTO_INCREMENT,
  `reportheading` varchar(80) NOT NULL DEFAULT '',
  `groupbydata1` varchar(15) NOT NULL DEFAULT '',
  `newpageafter1` tinyint(1) NOT NULL DEFAULT '0',
  `lower1` varchar(10) NOT NULL DEFAULT '',
  `upper1` varchar(10) NOT NULL DEFAULT '',
  `groupbydata2` varchar(15) DEFAULT NULL,
  `newpageafter2` tinyint(1) NOT NULL DEFAULT '0',
  `lower2` varchar(10) DEFAULT NULL,
  `upper2` varchar(10) DEFAULT NULL,
  `groupbydata3` varchar(15) DEFAULT NULL,
  `newpageafter3` tinyint(1) NOT NULL DEFAULT '0',
  `lower3` varchar(10) DEFAULT NULL,
  `upper3` varchar(10) DEFAULT NULL,
  `groupbydata4` varchar(15) NOT NULL DEFAULT '',
  `newpageafter4` tinyint(1) NOT NULL DEFAULT '0',
  `upper4` varchar(10) NOT NULL DEFAULT '',
  `lower4` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`reportid`),
  KEY `ReportHeading` (`reportheading`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reportlinks`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reportlinks` (
  `table1` varchar(25) NOT NULL DEFAULT '',
  `table2` varchar(25) NOT NULL DEFAULT '',
  `equation` varchar(75) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reports`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `reportname` varchar(30) NOT NULL DEFAULT '',
  `reporttype` char(3) NOT NULL DEFAULT 'rpt',
  `groupname` varchar(9) NOT NULL DEFAULT 'misc',
  `defaultreport` enum('1','0') NOT NULL DEFAULT '0',
  `papersize` varchar(15) NOT NULL DEFAULT 'A4,210,297',
  `paperorientation` enum('P','L') NOT NULL DEFAULT 'P',
  `margintop` int(3) NOT NULL DEFAULT '10',
  `marginbottom` int(3) NOT NULL DEFAULT '10',
  `marginleft` int(3) NOT NULL DEFAULT '10',
  `marginright` int(3) NOT NULL DEFAULT '10',
  `coynamefont` varchar(20) NOT NULL DEFAULT 'Helvetica',
  `coynamefontsize` int(3) NOT NULL DEFAULT '12',
  `coynamefontcolor` varchar(11) NOT NULL DEFAULT '0,0,0',
  `coynamealign` enum('L','C','R') NOT NULL DEFAULT 'C',
  `coynameshow` enum('1','0') NOT NULL DEFAULT '1',
  `title1desc` varchar(50) NOT NULL DEFAULT '%reportname%',
  `title1font` varchar(20) NOT NULL DEFAULT 'Helvetica',
  `title1fontsize` int(3) NOT NULL DEFAULT '10',
  `title1fontcolor` varchar(11) NOT NULL DEFAULT '0,0,0',
  `title1fontalign` enum('L','C','R') NOT NULL DEFAULT 'C',
  `title1show` enum('1','0') NOT NULL DEFAULT '1',
  `title2desc` varchar(50) NOT NULL DEFAULT 'Report Generated %date%',
  `title2font` varchar(20) NOT NULL DEFAULT 'Helvetica',
  `title2fontsize` int(3) NOT NULL DEFAULT '10',
  `title2fontcolor` varchar(11) NOT NULL DEFAULT '0,0,0',
  `title2fontalign` enum('L','C','R') NOT NULL DEFAULT 'C',
  `title2show` enum('1','0') NOT NULL DEFAULT '1',
  `filterfont` varchar(10) NOT NULL DEFAULT 'Helvetica',
  `filterfontsize` int(3) NOT NULL DEFAULT '8',
  `filterfontcolor` varchar(11) NOT NULL DEFAULT '0,0,0',
  `filterfontalign` enum('L','C','R') NOT NULL DEFAULT 'L',
  `datafont` varchar(10) NOT NULL DEFAULT 'Helvetica',
  `datafontsize` int(3) NOT NULL DEFAULT '10',
  `datafontcolor` varchar(10) NOT NULL DEFAULT 'black',
  `datafontalign` enum('L','C','R') NOT NULL DEFAULT 'L',
  `totalsfont` varchar(10) NOT NULL DEFAULT 'Helvetica',
  `totalsfontsize` int(3) NOT NULL DEFAULT '10',
  `totalsfontcolor` varchar(11) NOT NULL DEFAULT '0,0,0',
  `totalsfontalign` enum('L','C','R') NOT NULL DEFAULT 'L',
  `col1width` int(3) NOT NULL DEFAULT '25',
  `col2width` int(3) NOT NULL DEFAULT '25',
  `col3width` int(3) NOT NULL DEFAULT '25',
  `col4width` int(3) NOT NULL DEFAULT '25',
  `col5width` int(3) NOT NULL DEFAULT '25',
  `col6width` int(3) NOT NULL DEFAULT '25',
  `col7width` int(3) NOT NULL DEFAULT '25',
  `col8width` int(3) NOT NULL DEFAULT '25',
  `col9width` int(3) NOT NULL DEFAULT '25',
  `col10width` int(3) NOT NULL DEFAULT '25',
  `col11width` int(3) NOT NULL DEFAULT '25',
  `col12width` int(3) NOT NULL DEFAULT '25',
  `col13width` int(3) NOT NULL DEFAULT '25',
  `col14width` int(3) NOT NULL DEFAULT '25',
  `col15width` int(3) NOT NULL DEFAULT '25',
  `col16width` int(3) NOT NULL DEFAULT '25',
  `col17width` int(3) NOT NULL DEFAULT '25',
  `col18width` int(3) NOT NULL DEFAULT '25',
  `col19width` int(3) NOT NULL DEFAULT '25',
  `col20width` int(3) NOT NULL DEFAULT '25',
  `table1` varchar(25) NOT NULL DEFAULT '',
  `table2` varchar(25) DEFAULT NULL,
  `table2criteria` varchar(75) DEFAULT NULL,
  `table3` varchar(25) DEFAULT NULL,
  `table3criteria` varchar(75) DEFAULT NULL,
  `table4` varchar(25) DEFAULT NULL,
  `table4criteria` varchar(75) DEFAULT NULL,
  `table5` varchar(25) DEFAULT NULL,
  `table5criteria` varchar(75) DEFAULT NULL,
  `table6` varchar(25) DEFAULT NULL,
  `table6criteria` varchar(75) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`reportname`,`groupname`)
) ENGINE=MyISAM AUTO_INCREMENT=136 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `salesanalysis`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salesanalysis` (
  `typeabbrev` char(2) NOT NULL DEFAULT '',
  `periodno` smallint(6) NOT NULL DEFAULT '0',
  `amt` double NOT NULL DEFAULT '0',
  `cost` double NOT NULL DEFAULT '0',
  `cust` varchar(10) NOT NULL DEFAULT '',
  `custbranch` varchar(10) NOT NULL DEFAULT '',
  `qty` double NOT NULL DEFAULT '0',
  `disc` double NOT NULL DEFAULT '0',
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `area` varchar(3) NOT NULL,
  `budgetoractual` tinyint(1) NOT NULL DEFAULT '0',
  `salesperson` char(3) NOT NULL DEFAULT '',
  `stkcategory` varchar(6) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `CustBranch` (`custbranch`),
  KEY `Cust` (`cust`),
  KEY `PeriodNo` (`periodno`),
  KEY `StkCategory` (`stkcategory`),
  KEY `StockID` (`stockid`),
  KEY `TypeAbbrev` (`typeabbrev`),
  KEY `Area` (`area`),
  KEY `BudgetOrActual` (`budgetoractual`),
  KEY `Salesperson` (`salesperson`),
  CONSTRAINT `salesanalysis_ibfk_1` FOREIGN KEY (`periodno`) REFERENCES `periods` (`periodno`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `salescat`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salescat` (
  `salescatid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `parentcatid` tinyint(4) DEFAULT NULL,
  `salescatname` varchar(50) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1' COMMENT '1 if active 0 if inactive',
  PRIMARY KEY (`salescatid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `salescatprod`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salescatprod` (
  `salescatid` tinyint(4) NOT NULL DEFAULT '0',
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `manufacturers_id` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`salescatid`,`stockid`),
  KEY `salescatid` (`salescatid`),
  KEY `stockid` (`stockid`),
  KEY `manufacturer_id` (`manufacturers_id`),
  CONSTRAINT `salescatprod_ibfk_1` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `salescatprod_ibfk_2` FOREIGN KEY (`salescatid`) REFERENCES `salescat` (`salescatid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `salescattranslations`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salescattranslations` (
  `salescatid` tinyint(4) NOT NULL DEFAULT '0',
  `language_id` varchar(10) NOT NULL DEFAULT 'en_GB.utf8',
  `salescattranslation` varchar(40) NOT NULL,
  PRIMARY KEY (`salescatid`,`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `salesglpostings`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salesglpostings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area` varchar(3) NOT NULL,
  `stkcat` varchar(6) NOT NULL DEFAULT '',
  `discountglcode` varchar(20) NOT NULL DEFAULT '0',
  `salesglcode` varchar(20) NOT NULL DEFAULT '0',
  `salestype` char(2) NOT NULL DEFAULT 'AN',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Area_StkCat` (`area`,`stkcat`,`salestype`),
  KEY `Area` (`area`),
  KEY `StkCat` (`stkcat`),
  KEY `SalesType` (`salestype`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `salesman`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salesman` (
  `salesmancode` varchar(4) NOT NULL DEFAULT '',
  `salesmanname` char(30) NOT NULL DEFAULT '',
  `smantel` char(20) NOT NULL DEFAULT '',
  `smanfax` char(20) NOT NULL DEFAULT '',
  `commissionrate1` double NOT NULL DEFAULT '0',
  `breakpoint` decimal(10,0) NOT NULL DEFAULT '0',
  `commissionrate2` double NOT NULL DEFAULT '0',
  `current` tinyint(4) NOT NULL COMMENT 'Salesman current (1) or not (0)',
  PRIMARY KEY (`salesmancode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `salesorderdetails`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salesorderdetails` (
  `orderlineno` int(11) NOT NULL DEFAULT '0',
  `orderno` int(11) NOT NULL DEFAULT '0',
  `stkcode` varchar(20) NOT NULL DEFAULT '',
  `qtyinvoiced` double NOT NULL DEFAULT '0',
  `unitprice` double NOT NULL DEFAULT '0',
  `quantity` double NOT NULL DEFAULT '0',
  `estimate` tinyint(4) NOT NULL DEFAULT '0',
  `discountpercent` double NOT NULL DEFAULT '0',
  `actualdispatchdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `narrative` text,
  `itemdue` date DEFAULT NULL COMMENT 'Due date for line item.  Some customers require \r\nacknowledgements with due dates by line item',
  `poline` varchar(10) DEFAULT NULL COMMENT 'Some Customers require acknowledgements with a PO line number for each sales line',
  PRIMARY KEY (`orderlineno`,`orderno`),
  KEY `OrderNo` (`orderno`),
  KEY `StkCode` (`stkcode`),
  KEY `Completed` (`completed`),
  CONSTRAINT `salesorderdetails_ibfk_1` FOREIGN KEY (`orderno`) REFERENCES `salesorders` (`orderno`),
  CONSTRAINT `salesorderdetails_ibfk_2` FOREIGN KEY (`stkcode`) REFERENCES `stockmaster` (`stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `salesorders`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salesorders` (
  `orderno` int(11) NOT NULL,
  `debtorno` varchar(10) NOT NULL DEFAULT '',
  `branchcode` varchar(10) NOT NULL DEFAULT '',
  `customerref` varchar(50) NOT NULL DEFAULT '',
  `buyername` varchar(50) DEFAULT NULL,
  `comments` longblob,
  `orddate` date NOT NULL DEFAULT '0000-00-00',
  `ordertype` char(2) NOT NULL DEFAULT '',
  `shipvia` int(11) NOT NULL DEFAULT '0',
  `deladd1` varchar(40) NOT NULL DEFAULT '',
  `deladd2` varchar(40) NOT NULL DEFAULT '',
  `deladd3` varchar(40) NOT NULL DEFAULT '',
  `deladd4` varchar(40) DEFAULT NULL,
  `deladd5` varchar(20) NOT NULL DEFAULT '',
  `deladd6` varchar(15) NOT NULL DEFAULT '',
  `contactphone` varchar(25) DEFAULT NULL,
  `contactemail` varchar(40) DEFAULT NULL,
  `deliverto` varchar(40) NOT NULL DEFAULT '',
  `deliverblind` tinyint(1) DEFAULT '1',
  `freightcost` double NOT NULL DEFAULT '0',
  `fromstkloc` varchar(5) NOT NULL DEFAULT '',
  `deliverydate` date NOT NULL DEFAULT '0000-00-00',
  `confirmeddate` date NOT NULL DEFAULT '0000-00-00',
  `printedpackingslip` tinyint(4) NOT NULL DEFAULT '0',
  `datepackingslipprinted` date NOT NULL DEFAULT '0000-00-00',
  `quotation` tinyint(4) NOT NULL DEFAULT '0',
  `quotedate` date NOT NULL DEFAULT '0000-00-00',
  `poplaced` tinyint(4) NOT NULL DEFAULT '0',
  `salesperson` varchar(4) NOT NULL,
  PRIMARY KEY (`orderno`),
  KEY `DebtorNo` (`debtorno`),
  KEY `OrdDate` (`orddate`),
  KEY `OrderType` (`ordertype`),
  KEY `LocationIndex` (`fromstkloc`),
  KEY `BranchCode` (`branchcode`,`debtorno`),
  KEY `ShipVia` (`shipvia`),
  KEY `quotation` (`quotation`),
  KEY `poplaced` (`poplaced`),
  KEY `salesperson` (`salesperson`),
  CONSTRAINT `salesorders_ibfk_1` FOREIGN KEY (`branchcode`, `debtorno`) REFERENCES `custbranch` (`branchcode`, `debtorno`),
  CONSTRAINT `salesorders_ibfk_2` FOREIGN KEY (`shipvia`) REFERENCES `shippers` (`shipper_id`),
  CONSTRAINT `salesorders_ibfk_3` FOREIGN KEY (`fromstkloc`) REFERENCES `locations` (`loccode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `salestypes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salestypes` (
  `typeabbrev` char(2) NOT NULL DEFAULT '',
  `sales_type` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`typeabbrev`),
  KEY `Sales_Type` (`sales_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sampleresults`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sampleresults` (
  `resultid` bigint(20) NOT NULL AUTO_INCREMENT,
  `sampleid` int(11) NOT NULL,
  `testid` int(11) NOT NULL,
  `defaultvalue` varchar(150) NOT NULL,
  `targetvalue` varchar(30) NOT NULL,
  `rangemin` float DEFAULT NULL,
  `rangemax` float DEFAULT NULL,
  `testvalue` varchar(30) NOT NULL DEFAULT '',
  `testdate` date NOT NULL DEFAULT '0000-00-00',
  `testedby` varchar(15) NOT NULL DEFAULT '',
  `comments` varchar(255) NOT NULL DEFAULT '',
  `isinspec` tinyint(4) NOT NULL DEFAULT '0',
  `showoncert` tinyint(4) NOT NULL DEFAULT '1',
  `showontestplan` tinyint(4) NOT NULL DEFAULT '1',
  `manuallyadded` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`resultid`),
  KEY `sampleid` (`sampleid`),
  KEY `testid` (`testid`),
  CONSTRAINT `sampleresults_ibfk_1` FOREIGN KEY (`testid`) REFERENCES `qatests` (`testid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scripts`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scripts` (
  `script` varchar(78) NOT NULL DEFAULT '',
  `pagesecurity` int(11) NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  PRIMARY KEY (`script`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `securitygroups`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `securitygroups` (
  `secroleid` int(11) NOT NULL DEFAULT '0',
  `tokenid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`secroleid`,`tokenid`),
  KEY `secroleid` (`secroleid`),
  KEY `tokenid` (`tokenid`),
  CONSTRAINT `securitygroups_secroleid_fk` FOREIGN KEY (`secroleid`) REFERENCES `securityroles` (`secroleid`),
  CONSTRAINT `securitygroups_tokenid_fk` FOREIGN KEY (`tokenid`) REFERENCES `securitytokens` (`tokenid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `securityroles`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `securityroles` (
  `secroleid` int(11) NOT NULL AUTO_INCREMENT,
  `secrolename` text NOT NULL,
  PRIMARY KEY (`secroleid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `securitytokens`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `securitytokens` (
  `tokenid` int(11) NOT NULL DEFAULT '0',
  `tokenname` text NOT NULL,
  PRIMARY KEY (`tokenid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sellthroughsupport`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sellthroughsupport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplierno` varchar(10) NOT NULL,
  `debtorno` varchar(10) NOT NULL DEFAULT '',
  `categoryid` char(6) NOT NULL DEFAULT '',
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `narrative` varchar(20) NOT NULL DEFAULT '',
  `rebatepercent` double NOT NULL DEFAULT '0',
  `rebateamount` double NOT NULL DEFAULT '0',
  `effectivefrom` date NOT NULL,
  `effectiveto` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `supplierno` (`supplierno`),
  KEY `debtorno` (`debtorno`),
  KEY `effectivefrom` (`effectivefrom`),
  KEY `effectiveto` (`effectiveto`),
  KEY `stockid` (`stockid`),
  KEY `categoryid` (`categoryid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shipmentcharges`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipmentcharges` (
  `shiptchgid` int(11) NOT NULL AUTO_INCREMENT,
  `shiptref` int(11) NOT NULL DEFAULT '0',
  `transtype` smallint(6) NOT NULL DEFAULT '0',
  `transno` int(11) NOT NULL DEFAULT '0',
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `value` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`shiptchgid`),
  KEY `TransType` (`transtype`,`transno`),
  KEY `ShiptRef` (`shiptref`),
  KEY `StockID` (`stockid`),
  KEY `TransType_2` (`transtype`),
  CONSTRAINT `shipmentcharges_ibfk_1` FOREIGN KEY (`shiptref`) REFERENCES `shipments` (`shiptref`),
  CONSTRAINT `shipmentcharges_ibfk_2` FOREIGN KEY (`transtype`) REFERENCES `systypes` (`typeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shipments`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipments` (
  `shiptref` int(11) NOT NULL DEFAULT '0',
  `voyageref` varchar(20) NOT NULL DEFAULT '0',
  `vessel` varchar(50) NOT NULL DEFAULT '',
  `eta` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `accumvalue` double NOT NULL DEFAULT '0',
  `supplierid` varchar(10) NOT NULL DEFAULT '',
  `closed` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`shiptref`),
  KEY `ETA` (`eta`),
  KEY `SupplierID` (`supplierid`),
  KEY `ShipperRef` (`voyageref`),
  KEY `Vessel` (`vessel`),
  CONSTRAINT `shipments_ibfk_1` FOREIGN KEY (`supplierid`) REFERENCES `suppliers` (`supplierid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shippers`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shippers` (
  `shipper_id` int(11) NOT NULL AUTO_INCREMENT,
  `shippername` char(40) NOT NULL DEFAULT '',
  `mincharge` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`shipper_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockcategory`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockcategory` (
  `categoryid` char(6) NOT NULL DEFAULT '',
  `categorydescription` char(20) NOT NULL DEFAULT '',
  `stocktype` char(1) NOT NULL DEFAULT 'F',
  `stockact` varchar(20) NOT NULL DEFAULT '0',
  `adjglact` varchar(20) NOT NULL DEFAULT '0',
  `issueglact` varchar(20) NOT NULL DEFAULT '0',
  `purchpricevaract` varchar(20) NOT NULL DEFAULT '80000',
  `materialuseagevarac` varchar(20) NOT NULL DEFAULT '80000',
  `wipact` varchar(20) NOT NULL DEFAULT '0',
  `defaulttaxcatid` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`categoryid`),
  KEY `CategoryDescription` (`categorydescription`),
  KEY `StockType` (`stocktype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockcatproperties`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockcatproperties` (
  `stkcatpropid` int(11) NOT NULL AUTO_INCREMENT,
  `categoryid` char(6) NOT NULL,
  `label` text NOT NULL,
  `controltype` tinyint(4) NOT NULL DEFAULT '0',
  `defaultvalue` varchar(100) NOT NULL DEFAULT '''''',
  `maximumvalue` double NOT NULL DEFAULT '999999999',
  `reqatsalesorder` tinyint(4) NOT NULL DEFAULT '0',
  `minimumvalue` double NOT NULL DEFAULT '-999999999',
  `numericvalue` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`stkcatpropid`),
  KEY `categoryid` (`categoryid`),
  CONSTRAINT `stockcatproperties_ibfk_1` FOREIGN KEY (`categoryid`) REFERENCES `stockcategory` (`categoryid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockcheckfreeze`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockcheckfreeze` (
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `loccode` varchar(5) NOT NULL DEFAULT '',
  `qoh` double NOT NULL DEFAULT '0',
  `stockcheckdate` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`stockid`,`loccode`),
  KEY `LocCode` (`loccode`),
  CONSTRAINT `stockcheckfreeze_ibfk_1` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `stockcheckfreeze_ibfk_2` FOREIGN KEY (`loccode`) REFERENCES `locations` (`loccode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockcounts`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockcounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `loccode` varchar(5) NOT NULL DEFAULT '',
  `qtycounted` double NOT NULL DEFAULT '0',
  `reference` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `StockID` (`stockid`),
  KEY `LocCode` (`loccode`),
  CONSTRAINT `stockcounts_ibfk_1` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `stockcounts_ibfk_2` FOREIGN KEY (`loccode`) REFERENCES `locations` (`loccode`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockdescriptiontranslations`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockdescriptiontranslations` (
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `language_id` varchar(10) NOT NULL DEFAULT 'en_GB.utf8',
  `descriptiontranslation` varchar(50) DEFAULT NULL COMMENT 'Item''s short description',
  `longdescriptiontranslation` text COMMENT 'Item''s long description',
  `needsrevision` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`stockid`,`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockitemproperties`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockitemproperties` (
  `stockid` varchar(20) NOT NULL,
  `stkcatpropid` int(11) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY (`stockid`,`stkcatpropid`),
  KEY `stockid` (`stockid`),
  KEY `value` (`value`),
  KEY `stkcatpropid` (`stkcatpropid`),
  CONSTRAINT `stockitemproperties_ibfk_1` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `stockitemproperties_ibfk_2` FOREIGN KEY (`stkcatpropid`) REFERENCES `stockcatproperties` (`stkcatpropid`),
  CONSTRAINT `stockitemproperties_ibfk_3` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `stockitemproperties_ibfk_4` FOREIGN KEY (`stkcatpropid`) REFERENCES `stockcatproperties` (`stkcatpropid`),
  CONSTRAINT `stockitemproperties_ibfk_5` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `stockitemproperties_ibfk_6` FOREIGN KEY (`stkcatpropid`) REFERENCES `stockcatproperties` (`stkcatpropid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockmaster`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockmaster` (
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `categoryid` varchar(6) NOT NULL DEFAULT '',
  `description` varchar(50) NOT NULL DEFAULT '',
  `longdescription` text NOT NULL,
  `units` varchar(20) NOT NULL DEFAULT 'each',
  `mbflag` char(1) NOT NULL DEFAULT 'B',
  `actualcost` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `lastcost` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `materialcost` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `labourcost` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `overheadcost` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `lowestlevel` smallint(6) NOT NULL DEFAULT '0',
  `discontinued` tinyint(4) NOT NULL DEFAULT '0',
  `controlled` tinyint(4) NOT NULL DEFAULT '0',
  `eoq` double NOT NULL DEFAULT '0',
  `volume` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `grossweight` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `barcode` varchar(50) NOT NULL DEFAULT '',
  `discountcategory` char(2) NOT NULL DEFAULT '',
  `taxcatid` tinyint(4) NOT NULL DEFAULT '1',
  `serialised` tinyint(4) NOT NULL DEFAULT '0',
  `appendfile` varchar(40) NOT NULL DEFAULT 'none',
  `perishable` tinyint(1) NOT NULL DEFAULT '0',
  `decimalplaces` tinyint(4) NOT NULL DEFAULT '0',
  `pansize` double NOT NULL DEFAULT '0',
  `shrinkfactor` double NOT NULL DEFAULT '0',
  `nextserialno` bigint(20) NOT NULL DEFAULT '0',
  `netweight` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `lastcostupdate` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`stockid`),
  KEY `CategoryID` (`categoryid`),
  KEY `Description` (`description`),
  KEY `MBflag` (`mbflag`),
  KEY `StockID` (`stockid`,`categoryid`),
  KEY `Controlled` (`controlled`),
  KEY `DiscountCategory` (`discountcategory`),
  KEY `taxcatid` (`taxcatid`),
  CONSTRAINT `stockmaster_ibfk_1` FOREIGN KEY (`categoryid`) REFERENCES `stockcategory` (`categoryid`),
  CONSTRAINT `stockmaster_ibfk_2` FOREIGN KEY (`taxcatid`) REFERENCES `taxcategories` (`taxcatid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockmoves`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockmoves` (
  `stkmoveno` int(11) NOT NULL AUTO_INCREMENT,
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `type` smallint(6) NOT NULL DEFAULT '0',
  `transno` int(11) NOT NULL DEFAULT '0',
  `loccode` varchar(5) NOT NULL DEFAULT '',
  `trandate` date NOT NULL DEFAULT '0000-00-00',
  `userid` varchar(20) NOT NULL,
  `debtorno` varchar(10) NOT NULL DEFAULT '',
  `branchcode` varchar(10) NOT NULL DEFAULT '',
  `price` decimal(21,5) NOT NULL DEFAULT '0.00000',
  `prd` smallint(6) NOT NULL DEFAULT '0',
  `reference` varchar(100) NOT NULL DEFAULT '',
  `qty` double NOT NULL DEFAULT '1',
  `discountpercent` double NOT NULL DEFAULT '0',
  `standardcost` double NOT NULL DEFAULT '0',
  `show_on_inv_crds` tinyint(4) NOT NULL DEFAULT '1',
  `newqoh` double NOT NULL DEFAULT '0',
  `hidemovt` tinyint(4) NOT NULL DEFAULT '0',
  `narrative` text,
  PRIMARY KEY (`stkmoveno`),
  KEY `DebtorNo` (`debtorno`),
  KEY `LocCode` (`loccode`),
  KEY `Prd` (`prd`),
  KEY `StockID_2` (`stockid`),
  KEY `TranDate` (`trandate`),
  KEY `TransNo` (`transno`),
  KEY `Type` (`type`),
  KEY `Show_On_Inv_Crds` (`show_on_inv_crds`),
  KEY `Hide` (`hidemovt`),
  KEY `reference` (`reference`),
  CONSTRAINT `stockmoves_ibfk_1` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `stockmoves_ibfk_2` FOREIGN KEY (`type`) REFERENCES `systypes` (`typeid`),
  CONSTRAINT `stockmoves_ibfk_3` FOREIGN KEY (`loccode`) REFERENCES `locations` (`loccode`),
  CONSTRAINT `stockmoves_ibfk_4` FOREIGN KEY (`prd`) REFERENCES `periods` (`periodno`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockmovestaxes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockmovestaxes` (
  `stkmoveno` int(11) NOT NULL DEFAULT '0',
  `taxauthid` tinyint(4) NOT NULL DEFAULT '0',
  `taxrate` double NOT NULL DEFAULT '0',
  `taxontax` tinyint(4) NOT NULL DEFAULT '0',
  `taxcalculationorder` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`stkmoveno`,`taxauthid`),
  KEY `taxauthid` (`taxauthid`),
  KEY `calculationorder` (`taxcalculationorder`),
  CONSTRAINT `stockmovestaxes_ibfk_1` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthorities` (`taxid`),
  CONSTRAINT `stockmovestaxes_ibfk_2` FOREIGN KEY (`stkmoveno`) REFERENCES `stockmoves` (`stkmoveno`),
  CONSTRAINT `stockmovestaxes_ibfk_3` FOREIGN KEY (`stkmoveno`) REFERENCES `stockmoves` (`stkmoveno`),
  CONSTRAINT `stockmovestaxes_ibfk_4` FOREIGN KEY (`stkmoveno`) REFERENCES `stockmoves` (`stkmoveno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockrequest`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockrequest` (
  `dispatchid` int(11) NOT NULL AUTO_INCREMENT,
  `loccode` varchar(5) NOT NULL DEFAULT '',
  `departmentid` int(11) NOT NULL DEFAULT '0',
  `despatchdate` date NOT NULL DEFAULT '0000-00-00',
  `authorised` tinyint(4) NOT NULL DEFAULT '0',
  `closed` tinyint(4) NOT NULL DEFAULT '0',
  `narrative` text NOT NULL,
  PRIMARY KEY (`dispatchid`),
  KEY `loccode` (`loccode`),
  KEY `departmentid` (`departmentid`),
  CONSTRAINT `stockrequest_ibfk_1` FOREIGN KEY (`loccode`) REFERENCES `locations` (`loccode`),
  CONSTRAINT `stockrequest_ibfk_2` FOREIGN KEY (`departmentid`) REFERENCES `departments` (`departmentid`),
  CONSTRAINT `stockrequest_ibfk_3` FOREIGN KEY (`loccode`) REFERENCES `locations` (`loccode`),
  CONSTRAINT `stockrequest_ibfk_4` FOREIGN KEY (`departmentid`) REFERENCES `departments` (`departmentid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockrequestitems`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockrequestitems` (
  `dispatchitemsid` int(11) NOT NULL DEFAULT '0',
  `dispatchid` int(11) NOT NULL DEFAULT '0',
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `quantity` double NOT NULL DEFAULT '0',
  `qtydelivered` double NOT NULL DEFAULT '0',
  `decimalplaces` int(11) NOT NULL DEFAULT '0',
  `uom` varchar(20) NOT NULL DEFAULT '',
  `completed` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dispatchitemsid`,`dispatchid`),
  KEY `dispatchid` (`dispatchid`),
  KEY `stockid` (`stockid`),
  CONSTRAINT `stockrequestitems_ibfk_1` FOREIGN KEY (`dispatchid`) REFERENCES `stockrequest` (`dispatchid`),
  CONSTRAINT `stockrequestitems_ibfk_2` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `stockrequestitems_ibfk_3` FOREIGN KEY (`dispatchid`) REFERENCES `stockrequest` (`dispatchid`),
  CONSTRAINT `stockrequestitems_ibfk_4` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockserialitems`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockserialitems` (
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `loccode` varchar(5) NOT NULL DEFAULT '',
  `serialno` varchar(30) NOT NULL DEFAULT '',
  `expirationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `quantity` double NOT NULL DEFAULT '0',
  `qualitytext` text NOT NULL,
  PRIMARY KEY (`stockid`,`serialno`,`loccode`),
  KEY `StockID` (`stockid`),
  KEY `LocCode` (`loccode`),
  KEY `serialno` (`serialno`),
  CONSTRAINT `stockserialitems_ibfk_1` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `stockserialitems_ibfk_2` FOREIGN KEY (`loccode`) REFERENCES `locations` (`loccode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockserialmoves`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockserialmoves` (
  `stkitmmoveno` int(11) NOT NULL AUTO_INCREMENT,
  `stockmoveno` int(11) NOT NULL DEFAULT '0',
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `serialno` varchar(30) NOT NULL DEFAULT '',
  `moveqty` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`stkitmmoveno`),
  KEY `StockMoveNo` (`stockmoveno`),
  KEY `StockID_SN` (`stockid`,`serialno`),
  KEY `serialno` (`serialno`),
  CONSTRAINT `stockserialmoves_ibfk_1` FOREIGN KEY (`stockmoveno`) REFERENCES `stockmoves` (`stkmoveno`),
  CONSTRAINT `stockserialmoves_ibfk_2` FOREIGN KEY (`stockid`, `serialno`) REFERENCES `stockserialitems` (`stockid`, `serialno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suppallocs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppallocs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amt` double NOT NULL DEFAULT '0',
  `datealloc` date NOT NULL DEFAULT '0000-00-00',
  `transid_allocfrom` int(11) NOT NULL DEFAULT '0',
  `transid_allocto` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `TransID_AllocFrom` (`transid_allocfrom`),
  KEY `TransID_AllocTo` (`transid_allocto`),
  KEY `DateAlloc` (`datealloc`),
  CONSTRAINT `suppallocs_ibfk_1` FOREIGN KEY (`transid_allocfrom`) REFERENCES `supptrans` (`id`),
  CONSTRAINT `suppallocs_ibfk_2` FOREIGN KEY (`transid_allocto`) REFERENCES `supptrans` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suppliercontacts`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliercontacts` (
  `supplierid` varchar(10) NOT NULL DEFAULT '',
  `contact` varchar(30) NOT NULL DEFAULT '',
  `position` varchar(30) NOT NULL DEFAULT '',
  `tel` varchar(30) NOT NULL DEFAULT '',
  `fax` varchar(30) NOT NULL DEFAULT '',
  `mobile` varchar(30) NOT NULL DEFAULT '',
  `email` varchar(55) NOT NULL DEFAULT '',
  `ordercontact` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`supplierid`,`contact`),
  KEY `Contact` (`contact`),
  KEY `SupplierID` (`supplierid`),
  CONSTRAINT `suppliercontacts_ibfk_1` FOREIGN KEY (`supplierid`) REFERENCES `suppliers` (`supplierid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplierdiscounts`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplierdiscounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplierno` varchar(10) NOT NULL,
  `stockid` varchar(20) NOT NULL,
  `discountnarrative` varchar(20) NOT NULL,
  `discountpercent` double NOT NULL,
  `discountamount` double NOT NULL,
  `effectivefrom` date NOT NULL,
  `effectiveto` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `supplierno` (`supplierno`),
  KEY `effectivefrom` (`effectivefrom`),
  KEY `effectiveto` (`effectiveto`),
  KEY `stockid` (`stockid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suppliers`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `supplierid` varchar(10) NOT NULL DEFAULT '',
  `suppname` varchar(40) NOT NULL DEFAULT '',
  `address1` varchar(40) NOT NULL DEFAULT '',
  `address2` varchar(40) NOT NULL DEFAULT '',
  `address3` varchar(40) NOT NULL DEFAULT '',
  `address4` varchar(50) NOT NULL DEFAULT '',
  `address5` varchar(20) NOT NULL DEFAULT '',
  `address6` varchar(40) NOT NULL DEFAULT '',
  `supptype` tinyint(4) NOT NULL DEFAULT '1',
  `lat` float(10,6) NOT NULL DEFAULT '0.000000',
  `lng` float(10,6) NOT NULL DEFAULT '0.000000',
  `currcode` char(3) NOT NULL DEFAULT '',
  `suppliersince` date NOT NULL DEFAULT '0000-00-00',
  `paymentterms` char(2) NOT NULL DEFAULT '',
  `lastpaid` double NOT NULL DEFAULT '0',
  `lastpaiddate` datetime DEFAULT NULL,
  `bankact` varchar(30) NOT NULL DEFAULT '',
  `bankref` varchar(12) NOT NULL DEFAULT '',
  `bankpartics` varchar(12) NOT NULL DEFAULT '',
  `remittance` tinyint(4) NOT NULL DEFAULT '1',
  `taxgroupid` tinyint(4) NOT NULL DEFAULT '1',
  `factorcompanyid` int(11) NOT NULL DEFAULT '1',
  `taxref` varchar(20) NOT NULL DEFAULT '',
  `phn` varchar(50) NOT NULL DEFAULT '',
  `port` varchar(200) NOT NULL DEFAULT '',
  `email` varchar(55) DEFAULT NULL,
  `fax` varchar(25) DEFAULT NULL,
  `telephone` varchar(25) DEFAULT NULL,
  `url` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`supplierid`),
  KEY `CurrCode` (`currcode`),
  KEY `PaymentTerms` (`paymentterms`),
  KEY `SuppName` (`suppname`),
  KEY `taxgroupid` (`taxgroupid`),
  CONSTRAINT `suppliers_ibfk_1` FOREIGN KEY (`currcode`) REFERENCES `currencies` (`currabrev`),
  CONSTRAINT `suppliers_ibfk_2` FOREIGN KEY (`paymentterms`) REFERENCES `paymentterms` (`termsindicator`),
  CONSTRAINT `suppliers_ibfk_3` FOREIGN KEY (`taxgroupid`) REFERENCES `taxgroups` (`taxgroupid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suppliertype`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliertype` (
  `typeid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `typename` varchar(100) NOT NULL,
  PRIMARY KEY (`typeid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supptrans`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supptrans` (
  `transno` int(11) NOT NULL DEFAULT '0',
  `type` smallint(6) NOT NULL DEFAULT '0',
  `supplierno` varchar(10) NOT NULL DEFAULT '',
  `suppreference` varchar(20) NOT NULL DEFAULT '',
  `trandate` date NOT NULL DEFAULT '0000-00-00',
  `duedate` date NOT NULL DEFAULT '0000-00-00',
  `inputdate` datetime NOT NULL,
  `settled` tinyint(4) NOT NULL DEFAULT '0',
  `rate` double NOT NULL DEFAULT '1',
  `ovamount` double NOT NULL DEFAULT '0',
  `ovgst` double NOT NULL DEFAULT '0',
  `diffonexch` double NOT NULL DEFAULT '0',
  `alloc` double NOT NULL DEFAULT '0',
  `transtext` text,
  `hold` tinyint(4) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `TypeTransNo` (`transno`,`type`),
  KEY `DueDate` (`duedate`),
  KEY `Hold` (`hold`),
  KEY `SupplierNo` (`supplierno`),
  KEY `Settled` (`settled`),
  KEY `SupplierNo_2` (`supplierno`,`suppreference`),
  KEY `SuppReference` (`suppreference`),
  KEY `TranDate` (`trandate`),
  KEY `TransNo` (`transno`),
  KEY `Type` (`type`),
  CONSTRAINT `supptrans_ibfk_1` FOREIGN KEY (`type`) REFERENCES `systypes` (`typeid`),
  CONSTRAINT `supptrans_ibfk_2` FOREIGN KEY (`supplierno`) REFERENCES `suppliers` (`supplierid`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supptranstaxes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supptranstaxes` (
  `supptransid` int(11) NOT NULL DEFAULT '0',
  `taxauthid` tinyint(4) NOT NULL DEFAULT '0',
  `taxamount` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`supptransid`,`taxauthid`),
  KEY `taxauthid` (`taxauthid`),
  CONSTRAINT `supptranstaxes_ibfk_1` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthorities` (`taxid`),
  CONSTRAINT `supptranstaxes_ibfk_2` FOREIGN KEY (`supptransid`) REFERENCES `supptrans` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `systypes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systypes` (
  `typeid` smallint(6) NOT NULL DEFAULT '0',
  `typename` char(50) NOT NULL DEFAULT '',
  `typeno` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`typeid`),
  KEY `TypeNo` (`typeno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tags`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `tagref` tinyint(4) NOT NULL AUTO_INCREMENT,
  `tagdescription` varchar(50) NOT NULL,
  PRIMARY KEY (`tagref`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxauthorities`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxauthorities` (
  `taxid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `description` varchar(20) NOT NULL DEFAULT '',
  `taxglcode` varchar(20) NOT NULL DEFAULT '0',
  `purchtaxglaccount` varchar(20) NOT NULL DEFAULT '0',
  `bank` varchar(50) NOT NULL DEFAULT '',
  `bankacctype` varchar(20) NOT NULL DEFAULT '',
  `bankacc` varchar(50) NOT NULL DEFAULT '',
  `bankswift` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`taxid`),
  KEY `TaxGLCode` (`taxglcode`),
  KEY `PurchTaxGLAccount` (`purchtaxglaccount`),
  CONSTRAINT `taxauthorities_ibfk_1` FOREIGN KEY (`taxglcode`) REFERENCES `chartmaster` (`accountcode`),
  CONSTRAINT `taxauthorities_ibfk_2` FOREIGN KEY (`purchtaxglaccount`) REFERENCES `chartmaster` (`accountcode`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxauthrates`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxauthrates` (
  `taxauthority` tinyint(4) NOT NULL DEFAULT '1',
  `dispatchtaxprovince` tinyint(4) NOT NULL DEFAULT '1',
  `taxcatid` tinyint(4) NOT NULL DEFAULT '0',
  `taxrate` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`taxauthority`,`dispatchtaxprovince`,`taxcatid`),
  KEY `TaxAuthority` (`taxauthority`),
  KEY `dispatchtaxprovince` (`dispatchtaxprovince`),
  KEY `taxcatid` (`taxcatid`),
  CONSTRAINT `taxauthrates_ibfk_1` FOREIGN KEY (`taxauthority`) REFERENCES `taxauthorities` (`taxid`),
  CONSTRAINT `taxauthrates_ibfk_2` FOREIGN KEY (`taxcatid`) REFERENCES `taxcategories` (`taxcatid`),
  CONSTRAINT `taxauthrates_ibfk_3` FOREIGN KEY (`dispatchtaxprovince`) REFERENCES `taxprovinces` (`taxprovinceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxcategories`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxcategories` (
  `taxcatid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `taxcatname` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`taxcatid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxgroups`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxgroups` (
  `taxgroupid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `taxgroupdescription` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`taxgroupid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxgrouptaxes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxgrouptaxes` (
  `taxgroupid` tinyint(4) NOT NULL DEFAULT '0',
  `taxauthid` tinyint(4) NOT NULL DEFAULT '0',
  `calculationorder` tinyint(4) NOT NULL DEFAULT '0',
  `taxontax` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`taxgroupid`,`taxauthid`),
  KEY `taxgroupid` (`taxgroupid`),
  KEY `taxauthid` (`taxauthid`),
  CONSTRAINT `taxgrouptaxes_ibfk_1` FOREIGN KEY (`taxgroupid`) REFERENCES `taxgroups` (`taxgroupid`),
  CONSTRAINT `taxgrouptaxes_ibfk_2` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthorities` (`taxid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxprovinces`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxprovinces` (
  `taxprovinceid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `taxprovincename` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`taxprovinceid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tenderitems`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tenderitems` (
  `tenderid` int(11) NOT NULL DEFAULT '0',
  `stockid` varchar(20) NOT NULL DEFAULT '',
  `quantity` varchar(40) NOT NULL DEFAULT '',
  `units` varchar(20) NOT NULL DEFAULT 'each',
  PRIMARY KEY (`tenderid`,`stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tenders`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tenders` (
  `tenderid` int(11) NOT NULL DEFAULT '0',
  `location` varchar(5) NOT NULL DEFAULT '',
  `address1` varchar(40) NOT NULL DEFAULT '',
  `address2` varchar(40) NOT NULL DEFAULT '',
  `address3` varchar(40) NOT NULL DEFAULT '',
  `address4` varchar(40) NOT NULL DEFAULT '',
  `address5` varchar(20) NOT NULL DEFAULT '',
  `address6` varchar(15) NOT NULL DEFAULT '',
  `telephone` varchar(25) NOT NULL DEFAULT '',
  `closed` int(2) NOT NULL DEFAULT '0',
  `requiredbydate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`tenderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tendersuppliers`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tendersuppliers` (
  `tenderid` int(11) NOT NULL DEFAULT '0',
  `supplierid` varchar(10) NOT NULL DEFAULT '',
  `email` varchar(40) NOT NULL DEFAULT '',
  `responded` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tenderid`,`supplierid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unitsofmeasure`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unitsofmeasure` (
  `unitid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `unitname` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`unitid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `woitems`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `woitems` (
  `wo` int(11) NOT NULL,
  `stockid` char(20) NOT NULL DEFAULT '',
  `qtyreqd` double NOT NULL DEFAULT '1',
  `qtyrecd` double NOT NULL DEFAULT '0',
  `stdcost` double NOT NULL,
  `nextlotsnref` varchar(20) DEFAULT '',
  `comments` longblob,
  PRIMARY KEY (`wo`,`stockid`),
  KEY `stockid` (`stockid`),
  CONSTRAINT `woitems_ibfk_1` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `woitems_ibfk_2` FOREIGN KEY (`wo`) REFERENCES `workorders` (`wo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `worequirements`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `worequirements` (
  `wo` int(11) NOT NULL,
  `parentstockid` varchar(20) NOT NULL,
  `stockid` varchar(20) NOT NULL,
  `qtypu` double NOT NULL DEFAULT '1',
  `stdcost` double NOT NULL DEFAULT '0',
  `autoissue` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`wo`,`parentstockid`,`stockid`),
  KEY `stockid` (`stockid`),
  KEY `worequirements_ibfk_3` (`parentstockid`),
  CONSTRAINT `worequirements_ibfk_1` FOREIGN KEY (`wo`) REFERENCES `workorders` (`wo`),
  CONSTRAINT `worequirements_ibfk_2` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`),
  CONSTRAINT `worequirements_ibfk_3` FOREIGN KEY (`wo`, `parentstockid`) REFERENCES `woitems` (`wo`, `stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `workcentres`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workcentres` (
  `code` char(5) NOT NULL DEFAULT '',
  `location` char(5) NOT NULL DEFAULT '',
  `description` char(20) NOT NULL DEFAULT '',
  `capacity` double NOT NULL DEFAULT '1',
  `overheadperhour` decimal(10,0) NOT NULL DEFAULT '0',
  `overheadrecoveryact` varchar(20) NOT NULL DEFAULT '0',
  `setuphrs` decimal(10,0) NOT NULL DEFAULT '0',
  PRIMARY KEY (`code`),
  KEY `Description` (`description`),
  KEY `Location` (`location`),
  CONSTRAINT `workcentres_ibfk_1` FOREIGN KEY (`location`) REFERENCES `locations` (`loccode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `workorders`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workorders` (
  `wo` int(11) NOT NULL,
  `loccode` char(5) NOT NULL DEFAULT '',
  `requiredby` date NOT NULL DEFAULT '0000-00-00',
  `startdate` date NOT NULL DEFAULT '0000-00-00',
  `costissued` double NOT NULL DEFAULT '0',
  `closed` tinyint(4) NOT NULL DEFAULT '0',
  `closecomments` longblob,
  PRIMARY KEY (`wo`),
  KEY `LocCode` (`loccode`),
  KEY `StartDate` (`startdate`),
  KEY `RequiredBy` (`requiredby`),
  CONSTRAINT `worksorders_ibfk_1` FOREIGN KEY (`loccode`) REFERENCES `locations` (`loccode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `woserialnos`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `woserialnos` (
  `wo` int(11) NOT NULL,
  `stockid` varchar(20) NOT NULL,
  `serialno` varchar(30) NOT NULL,
  `quantity` double NOT NULL DEFAULT '1',
  `qualitytext` text NOT NULL,
  PRIMARY KEY (`wo`,`stockid`,`serialno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `www_users`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `www_users` (
  `userid` varchar(20) NOT NULL DEFAULT '',
  `password` text NOT NULL,
  `realname` varchar(35) NOT NULL DEFAULT '',
  `customerid` varchar(10) NOT NULL DEFAULT '',
  `supplierid` varchar(10) NOT NULL DEFAULT '',
  `salesman` char(3) NOT NULL,
  `phone` varchar(30) NOT NULL DEFAULT '',
  `email` varchar(55) DEFAULT NULL,
  `defaultlocation` varchar(5) NOT NULL DEFAULT '',
  `fullaccess` int(11) NOT NULL DEFAULT '1',
  `cancreatetender` tinyint(1) NOT NULL DEFAULT '0',
  `lastvisitdate` datetime DEFAULT NULL,
  `branchcode` varchar(10) NOT NULL DEFAULT '',
  `pagesize` varchar(20) NOT NULL DEFAULT 'A4',
  `modulesallowed` varchar(25) NOT NULL,
  `showdashboard` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Display dashboard after login',
  `blocked` tinyint(4) NOT NULL DEFAULT '0',
  `displayrecordsmax` int(11) NOT NULL DEFAULT '0',
  `theme` varchar(30) NOT NULL DEFAULT 'fresh',
  `language` varchar(10) NOT NULL DEFAULT 'en_GB.utf8',
  `pdflanguage` tinyint(1) NOT NULL DEFAULT '0',
  `department` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`),
  KEY `CustomerID` (`customerid`),
  KEY `DefaultLocation` (`defaultlocation`),
  CONSTRAINT `www_users_ibfk_1` FOREIGN KEY (`defaultlocation`) REFERENCES `locations` (`loccode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-02-06 20:01:47
-- MySQL dump 10.14  Distrib 5.5.40-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: weberpdemo
-- ------------------------------------------------------
-- Server version	5.5.40-MariaDB-0ubuntu0.14.04.1
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `accountgroups`
--

INSERT INTO `accountgroups` VALUES ('Cost of Goods Sold',2,1,5000,'');
INSERT INTO `accountgroups` VALUES ('Current Assets',20,0,1000,'');
INSERT INTO `accountgroups` VALUES ('Financed',50,0,3000,'');
INSERT INTO `accountgroups` VALUES ('Fixed Assets',10,0,500,'');
INSERT INTO `accountgroups` VALUES ('Giveaways',5,1,6000,'Promotions');
INSERT INTO `accountgroups` VALUES ('Income Tax',5,1,9000,'');
INSERT INTO `accountgroups` VALUES ('Liabilities',30,0,2000,'');
INSERT INTO `accountgroups` VALUES ('Marketing Expenses',5,1,6000,'');
INSERT INTO `accountgroups` VALUES ('Operating Expenses',5,1,7000,'');
INSERT INTO `accountgroups` VALUES ('Other Revenue and Expenses',5,1,8000,'');
INSERT INTO `accountgroups` VALUES ('Outward Freight',2,1,5000,'Cost of Goods Sold');
INSERT INTO `accountgroups` VALUES ('Promotions',5,1,6000,'Marketing Expenses');
INSERT INTO `accountgroups` VALUES ('Revenue',1,1,4000,'');
INSERT INTO `accountgroups` VALUES ('Sales',1,1,10,'');

--
-- Dumping data for table `accountsection`
--

INSERT INTO `accountsection` VALUES (1,'Income');
INSERT INTO `accountsection` VALUES (2,'Cost Of Sales');
INSERT INTO `accountsection` VALUES (5,'Overheads');
INSERT INTO `accountsection` VALUES (10,'Fixed Assets');
INSERT INTO `accountsection` VALUES (15,'Inventory');
INSERT INTO `accountsection` VALUES (20,'Amounts Receivable');
INSERT INTO `accountsection` VALUES (25,'Cash');
INSERT INTO `accountsection` VALUES (30,'Amounts Payable');
INSERT INTO `accountsection` VALUES (50,'Financed By');

--
-- Dumping data for table `areas`
--

INSERT INTO `areas` VALUES ('123','Test area');
INSERT INTO `areas` VALUES ('DE','Default');
INSERT INTO `areas` VALUES ('FL','Florida');
INSERT INTO `areas` VALUES ('TR','Toronto');

--
-- Dumping data for table `assetmanager`
--


--
-- Dumping data for table `audittrail`
--


--
-- Dumping data for table `bankaccounts`
--

INSERT INTO `bankaccounts` VALUES ('1010','GBP',2,'123','GBP account','123','','');
INSERT INTO `bankaccounts` VALUES ('1030','AUD',2,'12445','Cheque Account','124455667789','123 Straight Street','');
INSERT INTO `bankaccounts` VALUES ('1040','AUD',0,'','Savings Account','','','');
INSERT INTO `bankaccounts` VALUES ('1060','USD',1,'','USD Bank Account','123','','GIFTS');

--
-- Dumping data for table `bankaccountusers`
--

INSERT INTO `bankaccountusers` VALUES ('1030','admin');
INSERT INTO `bankaccountusers` VALUES ('1010','admin');
INSERT INTO `bankaccountusers` VALUES ('1040','admin');
INSERT INTO `bankaccountusers` VALUES ('1060','admin');
INSERT INTO `bankaccountusers` VALUES ('1030','admin');
INSERT INTO `bankaccountusers` VALUES ('1010','admin');
INSERT INTO `bankaccountusers` VALUES ('1040','admin');
INSERT INTO `bankaccountusers` VALUES ('1060','admin');

--
-- Dumping data for table `banktrans`
--

INSERT INTO `banktrans` VALUES (1,12,5,'1030','',0,1,0.9953,'2013-05-10','Cash',50,'AUD');
INSERT INTO `banktrans` VALUES (2,12,12,'1030','web shop receipt 7 3P178942ST690145V',0,1.0378,1.0378,'2013-06-08','PayPalPro web',0,'USD');
INSERT INTO `banktrans` VALUES (3,12,13,'1030','web shop receipt 7 2E5509873Y0129234',0,1.0378,1.0378,'2013-06-08','PayPalPro web',0,'USD');
INSERT INTO `banktrans` VALUES (4,12,14,'1040','web shop receipt 7 6CW03791GP8036526',0,1.0378,1.0378,'2013-06-08','PayPal web',0,'USD');
INSERT INTO `banktrans` VALUES (6,12,17,'1030','web shop receipt 7 ',0,1.0378,1.0378,'2013-06-08','PayPalPro web',0,'USD');
INSERT INTO `banktrans` VALUES (7,12,18,'1030','',0,0.959969,1.0417,'2013-06-16','Cash',85.9,'USD');
INSERT INTO `banktrans` VALUES (8,12,19,'1030','web shop receipt 7 B254331D91384',0,1.0814,1.0814,'2013-06-23','SwipeHQ web',0,'USD');
INSERT INTO `banktrans` VALUES (9,12,20,'1030','web shop receipt 7 B254348421937',0,1.0814,1.0814,'2013-06-23','SwipeHQ web',0,'USD');
INSERT INTO `banktrans` VALUES (10,12,21,'1040','web shop receipt 7 ',0,1.0814,1.0814,'2013-06-23','PayPal web',0,'USD');
INSERT INTO `banktrans` VALUES (11,12,1,'1040','web shop receipt 7 7XT5929577922053V',0,1.0814,1.0814,'2013-06-23','PayPal web',39.5722309059,'USD');
INSERT INTO `banktrans` VALUES (12,12,1,'1030','web shop receipt 16 ',0,1.0814,1.0814,'2013-06-23','PayPalPro web',118.14262747245,'USD');
INSERT INTO `banktrans` VALUES (13,12,2,'1030','web shop receipt 16 ',0,1.091,1.091,'2013-06-24','PayPalPro web',15.450740227125,'USD');
INSERT INTO `banktrans` VALUES (14,12,3,'1030','web shop receipt 16 V78R4D18BB1E',0,1.091,1.091,'2013-06-24','PayFlow web',15.450740227125,'USD');
INSERT INTO `banktrans` VALUES (15,12,4,'1030','web shop receipt 16 V18R4E55E804',0,1.091,1.091,'2013-06-24','PayFlow web',9.447024024585,'USD');
INSERT INTO `banktrans` VALUES (16,22,9,'1030','',0,1,1.0884,'2013-11-20','Cash',-100,'AUD');
INSERT INTO `banktrans` VALUES (17,22,10,'1030','',0,1,1,'2013-11-20','Cash',-100,'AUD');
INSERT INTO `banktrans` VALUES (18,22,11,'1030','',0,1,1.0884,'2013-11-20','Cash',-100,'AUD');
INSERT INTO `banktrans` VALUES (19,1,5,'1030','Test',0,0.91877986,1.0884,'2014-02-03','Cash',-105.5,'USD');
INSERT INTO `banktrans` VALUES (20,1,6,'1060','ffd',0,1,1,'2014-02-03','Cash',-125,'USD');
INSERT INTO `banktrans` VALUES (21,2,1,'1010','Act Transfer From 1060 - ',0,1.5199878400973,0.6579,'2014-07-05','Cash',10,'USD');
INSERT INTO `banktrans` VALUES (22,1,7,'1060','',0,1,1,'2014-07-05','Cash',-10,'USD');

--
-- Dumping data for table `bom`
--

INSERT INTO `bom` VALUES ('BIGEARS12',0,'DVD-CASE','MEL','TOR','2010-08-14','2037-12-31',1,0);
INSERT INTO `bom` VALUES ('BirthdayCakeConstruc',0,'BREAD','MEL','TOR','2010-08-14','2037-12-31',1,0);
INSERT INTO `bom` VALUES ('BirthdayCakeConstruc',0,'DVD-CASE','MEL','TOR','2010-08-14','2037-12-31',1,0);
INSERT INTO `bom` VALUES ('BirthdayCakeConstruc',0,'FLOUR','MEL','TOR','2010-08-14','2037-12-31',1,0);
INSERT INTO `bom` VALUES ('BirthdayCakeConstruc',0,'SALT','MEL','TOR','2010-08-14','2037-12-31',1,0);
INSERT INTO `bom` VALUES ('BirthdayCakeConstruc',0,'YEAST','MEL','TOR','2010-08-14','2037-12-31',1,0);
INSERT INTO `bom` VALUES ('BREAD',0,'SALT','ASS','MEL','2007-06-19','2037-06-20',0.025,1);
INSERT INTO `bom` VALUES ('BREAD',0,'TESTSERIALITEM','ASS','MEL','2013-04-24','2033-04-25',1,0);
INSERT INTO `bom` VALUES ('BREAD',0,'YEAST','ASS','MEL','2007-06-19','2037-06-20',0.1,0);
INSERT INTO `bom` VALUES ('DVD_ACTION',0,'DVD-CASE','ASS','MEL','2007-06-12','2037-06-13',4,0);
INSERT INTO `bom` VALUES ('DVD_ACTION',0,'DVD-DHWV','ASS','MEL','2007-06-12','2037-06-13',1,1);
INSERT INTO `bom` VALUES ('DVD_ACTION',0,'DVD-LTWP','ASS','MEL','2007-06-12','2037-06-13',1,1);
INSERT INTO `bom` VALUES ('DVD_ACTION',0,'DVD-UNSG','ASS','MEL','2007-06-12','2037-06-13',1,1);
INSERT INTO `bom` VALUES ('DVD_ACTION',0,'DVD-UNSG2','ASS','MEL','2007-06-12','2037-06-13',1,1);
INSERT INTO `bom` VALUES ('FUJI9901ASS',0,'FUJI990101','ASS','MEL','2005-06-04','2035-06-05',1,0);
INSERT INTO `bom` VALUES ('FUJI9901ASS',0,'FUJI990102','ASS','MEL','2005-02-12','2037-06-13',1,0);
INSERT INTO `bom` VALUES ('SELLTAPE',0,'TAPE1','ASS','AN','2013-02-08','2033-02-09',0.001,0);
INSERT INTO `bom` VALUES ('SLICE',0,'BREAD','ASS','MEL','2007-06-19','2037-06-20',0.1,1);
INSERT INTO `bom` VALUES ('TAPE2',0,'CUTTING','ASS','AN','2013-02-07','2033-02-08',0.5,0);
INSERT INTO `bom` VALUES ('TAPE2',0,'TAPE1','ASS','AN','2013-02-07','2033-02-08',0.25,1);
INSERT INTO `bom` VALUES ('Test123',0,'SALT','ASS','TOR','2013-06-21','2099-12-31',2,0);
INSERT INTO `bom` VALUES ('Test123',0,'TAPE1','ASS','TOR','2013-06-21','2099-12-31',3,0);
INSERT INTO `bom` VALUES ('Test123',0,'TAPE2','ASS','TOR','2013-06-21','2099-12-31',4,0);

--
-- Dumping data for table `chartdetails`
--

INSERT INTO `chartdetails` VALUES ('1',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',32,0,10,0,0);
INSERT INTO `chartdetails` VALUES ('1010',33,0,0,10,0);
INSERT INTO `chartdetails` VALUES ('1010',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1010',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1020',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',24,0,-283.75597206909,0,0);
INSERT INTO `chartdetails` VALUES ('1030',25,0,0,-283.75597206909,0);
INSERT INTO `chartdetails` VALUES ('1030',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',27,0,-105.50000003967,0,0);
INSERT INTO `chartdetails` VALUES ('1030',28,0,0,-105.50000003967,0);
INSERT INTO `chartdetails` VALUES ('1030',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1030',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1040',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1050',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',27,0,-125,0,0);
INSERT INTO `chartdetails` VALUES ('1060',28,0,0,-125,0);
INSERT INTO `chartdetails` VALUES ('1060',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',32,0,-10,0,0);
INSERT INTO `chartdetails` VALUES ('1060',33,0,0,-10,0);
INSERT INTO `chartdetails` VALUES ('1060',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1060',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1070',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1080',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1090',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1100',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1150',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1200',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1250',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1300',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',27,0,25,0,0);
INSERT INTO `chartdetails` VALUES ('1350',28,0,0,25,0);
INSERT INTO `chartdetails` VALUES ('1350',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1350',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1400',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1420',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1440',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',24,0,52.35,0,0);
INSERT INTO `chartdetails` VALUES ('1460',25,0,0,52.35,0);
INSERT INTO `chartdetails` VALUES ('1460',26,0,263.453,52.35,0);
INSERT INTO `chartdetails` VALUES ('1460',27,0,0,315.803,0);
INSERT INTO `chartdetails` VALUES ('1460',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1460',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1500',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1550',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1600',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1620',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1650',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1670',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1700',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1710',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1720',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1730',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1740',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1750',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1760',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1770',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1780',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1790',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1800',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1850',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('1900',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2010',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2020',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2050',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',24,0,226.17097206908997,0,0);
INSERT INTO `chartdetails` VALUES ('2100',25,0,0,226.17097206908997,0);
INSERT INTO `chartdetails` VALUES ('2100',26,0,-95.7,-57.585,0);
INSERT INTO `chartdetails` VALUES ('2100',27,0,0,-153.285,0);
INSERT INTO `chartdetails` VALUES ('2100',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2100',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',26,0,-217.91,0,0);
INSERT INTO `chartdetails` VALUES ('2150',27,0,0,-217.91,0);
INSERT INTO `chartdetails` VALUES ('2150',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',32,0,-150,0,0);
INSERT INTO `chartdetails` VALUES ('2150',33,0,0,-150,0);
INSERT INTO `chartdetails` VALUES ('2150',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2150',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2200',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2230',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2250',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2300',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',24,0,5.235,0,0);
INSERT INTO `chartdetails` VALUES ('2310',25,0,0,5.235,0);
INSERT INTO `chartdetails` VALUES ('2310',26,0,8.7,5.235,0);
INSERT INTO `chartdetails` VALUES ('2310',27,0,0,13.934999999999999,0);
INSERT INTO `chartdetails` VALUES ('2310',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2310',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2320',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2330',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2340',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2350',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2360',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2400',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2410',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2420',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2450',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2460',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2470',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2480',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2500',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2550',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2560',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2600',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2700',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2720',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2740',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2760',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2800',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('2900',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3100',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3200',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3300',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3400',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('3500',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4100',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4200',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4500',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4600',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4700',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4800',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('4900',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',26,0,4,0,0);
INSERT INTO `chartdetails` VALUES ('5000',27,0,0,4,0);
INSERT INTO `chartdetails` VALUES ('5000',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5000',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5100',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5200',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',32,0,150,0,0);
INSERT INTO `chartdetails` VALUES ('5500',33,0,0,150,0);
INSERT INTO `chartdetails` VALUES ('5500',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5500',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',27,0,100,0,0);
INSERT INTO `chartdetails` VALUES ('5600',28,0,0,100,0);
INSERT INTO `chartdetails` VALUES ('5600',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5600',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',26,0,37.457,0,0);
INSERT INTO `chartdetails` VALUES ('5700',27,0,0,37.457,0);
INSERT INTO `chartdetails` VALUES ('5700',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5700',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5800',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('5900',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6100',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6150',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6200',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6250',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6300',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6400',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6500',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6550',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6590',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',27,0,10.250000003856,0,0);
INSERT INTO `chartdetails` VALUES ('6600',28,0,0,10.250000003856,0);
INSERT INTO `chartdetails` VALUES ('6600',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6600',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6700',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6800',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('6900',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7020',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7030',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7040',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7050',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7060',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7070',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7080',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',27,0,95.250000035814,0,0);
INSERT INTO `chartdetails` VALUES ('7090',28,0,0,95.250000035814,0);
INSERT INTO `chartdetails` VALUES ('7090',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7090',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7100',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7150',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7200',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7210',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7220',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7230',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7240',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7260',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7280',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7300',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7350',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7390',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7400',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7450',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7500',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7550',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7600',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7610',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7620',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7630',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7640',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7650',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7660',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7700',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7750',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7800',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('7900',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8100',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8200',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8300',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8400',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8500',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8600',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('8900',36,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',14,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',15,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',16,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',17,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',18,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',19,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',20,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',21,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',22,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',23,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',24,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',25,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',26,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',27,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',28,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',29,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',30,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',31,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',32,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',33,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',34,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',35,0,0,0,0);
INSERT INTO `chartdetails` VALUES ('9100',36,0,0,0,0);

--
-- Dumping data for table `chartmaster`
--

INSERT INTO `chartmaster` VALUES ('1','Default Sales/Discounts','Sales');
INSERT INTO `chartmaster` VALUES ('1010','Petty Cash','Current Assets');
INSERT INTO `chartmaster` VALUES ('1020','Cash on Hand','Current Assets');
INSERT INTO `chartmaster` VALUES ('1030','Cheque Accounts','Current Assets');
INSERT INTO `chartmaster` VALUES ('1040','Savings Accounts','Current Assets');
INSERT INTO `chartmaster` VALUES ('1050','Payroll Accounts','Current Assets');
INSERT INTO `chartmaster` VALUES ('1060','Special Accounts','Current Assets');
INSERT INTO `chartmaster` VALUES ('1070','Money Market Investments','Current Assets');
INSERT INTO `chartmaster` VALUES ('1080','Short-Term Investments (< 90 days)','Current Assets');
INSERT INTO `chartmaster` VALUES ('1090','Interest Receivable','Current Assets');
INSERT INTO `chartmaster` VALUES ('1100','Accounts Receivable','Current Assets');
INSERT INTO `chartmaster` VALUES ('1150','Allowance for Doubtful Accounts','Current Assets');
INSERT INTO `chartmaster` VALUES ('1200','Notes Receivable','Current Assets');
INSERT INTO `chartmaster` VALUES ('1250','Income Tax Receivable','Current Assets');
INSERT INTO `chartmaster` VALUES ('1300','Prepaid Expenses','Current Assets');
INSERT INTO `chartmaster` VALUES ('1350','Advances','Current Assets');
INSERT INTO `chartmaster` VALUES ('1400','Supplies Inventory','Current Assets');
INSERT INTO `chartmaster` VALUES ('1420','Raw Material Inventory','Current Assets');
INSERT INTO `chartmaster` VALUES ('1440','Work in Progress Inventory','Current Assets');
INSERT INTO `chartmaster` VALUES ('1460','Finished Goods Inventory','Current Assets');
INSERT INTO `chartmaster` VALUES ('1500','Land','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1550','Bonds','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1600','Buildings','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1620','Accumulated Depreciation of Buildings','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1650','Equipment','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1670','Accumulated Depreciation of Equipment','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1700','Furniture & Fixtures','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1710','Accumulated Depreciation of Furniture & Fixtures','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1720','Office Equipment','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1730','Accumulated Depreciation of Office Equipment','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1740','Software','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1750','Accumulated Depreciation of Software','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1760','Vehicles','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1770','Accumulated Depreciation Vehicles','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1780','Other Depreciable Property','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1790','Accumulated Depreciation of Other Depreciable Prop','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1800','Patents','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1850','Goodwill','Fixed Assets');
INSERT INTO `chartmaster` VALUES ('1900','Future Income Tax Receivable','Current Assets');
INSERT INTO `chartmaster` VALUES ('2010','Bank Indedebtedness (overdraft)','Liabilities');
INSERT INTO `chartmaster` VALUES ('2020','Retainers or Advances on Work','Liabilities');
INSERT INTO `chartmaster` VALUES ('2050','Interest Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2100','Accounts Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2150','Goods Received Suspense','Liabilities');
INSERT INTO `chartmaster` VALUES ('2200','Short-Term Loan Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2230','Current Portion of Long-Term Debt Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2250','Income Tax Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2300','GST Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2310','GST Recoverable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2320','PST Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2330','PST Recoverable (commission)','Liabilities');
INSERT INTO `chartmaster` VALUES ('2340','Payroll Tax Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2350','Withholding Income Tax Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2360','Other Taxes Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2400','Employee Salaries Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2410','Management Salaries Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2420','Director / Partner Fees Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2450','Health Benefits Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2460','Pension Benefits Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2470','Canada Pension Plan Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2480','Employment Insurance Premiums Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2500','Land Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2550','Long-Term Bank Loan','Liabilities');
INSERT INTO `chartmaster` VALUES ('2560','Notes Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2600','Building & Equipment Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2700','Furnishing & Fixture Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2720','Office Equipment Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2740','Vehicle Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2760','Other Property Payable','Liabilities');
INSERT INTO `chartmaster` VALUES ('2800','Shareholder Loans','Liabilities');
INSERT INTO `chartmaster` VALUES ('2900','Suspense','Liabilities');
INSERT INTO `chartmaster` VALUES ('3100','Capital Stock','Financed');
INSERT INTO `chartmaster` VALUES ('3200','Capital Surplus / Dividends','Financed');
INSERT INTO `chartmaster` VALUES ('3300','Dividend Taxes Payable','Financed');
INSERT INTO `chartmaster` VALUES ('3400','Dividend Taxes Refundable','Financed');
INSERT INTO `chartmaster` VALUES ('3500','Retained Earnings','Financed');
INSERT INTO `chartmaster` VALUES ('4100','Product / Service Sales','Revenue');
INSERT INTO `chartmaster` VALUES ('4200','Sales Exchange Gains/Losses','Revenue');
INSERT INTO `chartmaster` VALUES ('4500','Consulting Services','Revenue');
INSERT INTO `chartmaster` VALUES ('4600','Rentals','Revenue');
INSERT INTO `chartmaster` VALUES ('4700','Finance Charge Income','Revenue');
INSERT INTO `chartmaster` VALUES ('4800','Sales Returns & Allowances','Revenue');
INSERT INTO `chartmaster` VALUES ('4900','Sales Discounts','Revenue');
INSERT INTO `chartmaster` VALUES ('5000','Cost of Sales','Cost of Goods Sold');
INSERT INTO `chartmaster` VALUES ('5100','Production Expenses','Cost of Goods Sold');
INSERT INTO `chartmaster` VALUES ('5200','Purchases Exchange Gains/Losses','Cost of Goods Sold');
INSERT INTO `chartmaster` VALUES ('5500','Direct Labour Costs','Cost of Goods Sold');
INSERT INTO `chartmaster` VALUES ('5600','Freight Charges','Outward Freight');
INSERT INTO `chartmaster` VALUES ('5700','Inventory Adjustment','Cost of Goods Sold');
INSERT INTO `chartmaster` VALUES ('5800','Purchase Returns & Allowances','Cost of Goods Sold');
INSERT INTO `chartmaster` VALUES ('5900','Purchase Discounts','Cost of Goods Sold');
INSERT INTO `chartmaster` VALUES ('6100','Advertising','Marketing Expenses');
INSERT INTO `chartmaster` VALUES ('6150','Promotion','Promotions');
INSERT INTO `chartmaster` VALUES ('6200','Communications','Marketing Expenses');
INSERT INTO `chartmaster` VALUES ('6250','Meeting Expenses','Marketing Expenses');
INSERT INTO `chartmaster` VALUES ('6300','Travelling Expenses','Marketing Expenses');
INSERT INTO `chartmaster` VALUES ('6400','Delivery Expenses','Marketing Expenses');
INSERT INTO `chartmaster` VALUES ('6500','Sales Salaries & Commission','Marketing Expenses');
INSERT INTO `chartmaster` VALUES ('6550','Sales Salaries & Commission Deductions','Marketing Expenses');
INSERT INTO `chartmaster` VALUES ('6590','Benefits','Marketing Expenses');
INSERT INTO `chartmaster` VALUES ('6600','Other Selling Expenses','Marketing Expenses');
INSERT INTO `chartmaster` VALUES ('6700','Permits, Licenses & License Fees','Marketing Expenses');
INSERT INTO `chartmaster` VALUES ('6800','Research & Development','Marketing Expenses');
INSERT INTO `chartmaster` VALUES ('6900','Professional Services','Marketing Expenses');
INSERT INTO `chartmaster` VALUES ('7020','Support Salaries & Wages','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7030','Support Salary & Wage Deductions','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7040','Management Salaries','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7050','Management Salary deductions','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7060','Director / Partner Fees','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7070','Director / Partner Deductions','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7080','Payroll Tax','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7090','Benefits','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7100','Training & Education Expenses','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7150','Dues & Subscriptions','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7200','Accounting Fees','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7210','Audit Fees','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7220','Banking Fees','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7230','Credit Card Fees','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7240','Consulting Fees','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7260','Legal Fees','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7280','Other Professional Fees','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7300','Business Tax','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7350','Property Tax','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7390','Corporation Capital Tax','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7400','Office Rent','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7450','Equipment Rental','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7500','Office Supplies','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7550','Office Repair & Maintenance','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7600','Automotive Expenses','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7610','Communication Expenses','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7620','Insurance Expenses','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7630','Postage & Courier Expenses','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7640','Miscellaneous Expenses','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7650','Travel Expenses','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7660','Utilities','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7700','Ammortization Expenses','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7750','Depreciation Expenses','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7800','Interest Expense','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('7900','Bad Debt Expense','Operating Expenses');
INSERT INTO `chartmaster` VALUES ('8100','Gain on Sale of Assets','Other Revenue and Expenses');
INSERT INTO `chartmaster` VALUES ('8200','Interest Income','Other Revenue and Expenses');
INSERT INTO `chartmaster` VALUES ('8300','Recovery on Bad Debt','Other Revenue and Expenses');
INSERT INTO `chartmaster` VALUES ('8400','Other Revenue','Other Revenue and Expenses');
INSERT INTO `chartmaster` VALUES ('8500','Loss on Sale of Assets','Other Revenue and Expenses');
INSERT INTO `chartmaster` VALUES ('8600','Charitable Contributions','Other Revenue and Expenses');
INSERT INTO `chartmaster` VALUES ('8900','Other Expenses','Other Revenue and Expenses');
INSERT INTO `chartmaster` VALUES ('9100','Income Tax Provision','Income Tax');

--
-- Dumping data for table `cogsglpostings`
--

INSERT INTO `cogsglpostings` VALUES (5,'AN','ANY','5000','AN');
INSERT INTO `cogsglpostings` VALUES (6,'123','ANY','6100','AN');

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` VALUES (1,'weberpdemo','not entered yet','','123 Web Way','PO Box 123','Queen Street','Melbourne','Victoria 3043','Australia','+61 3 4567 8901','+61 3 4567 8902','weberp@weberpdemo.com','USD','1100','4900','2100','2400','2150','4200','5200','3500',1,1,1,'5600');

--
-- Dumping data for table `config`
--

INSERT INTO `config` VALUES ('AllowOrderLineItemNarrative','1');
INSERT INTO `config` VALUES ('AllowSalesOfZeroCostItems','0');
INSERT INTO `config` VALUES ('AutoAuthorisePO','1');
INSERT INTO `config` VALUES ('AutoCreateWOs','1');
INSERT INTO `config` VALUES ('AutoDebtorNo','0');
INSERT INTO `config` VALUES ('AutoIssue','1');
INSERT INTO `config` VALUES ('AutoSupplierNo','0');
INSERT INTO `config` VALUES ('CheckCreditLimits','1');
INSERT INTO `config` VALUES ('Check_Price_Charged_vs_Order_Price','1');
INSERT INTO `config` VALUES ('Check_Qty_Charged_vs_Del_Qty','1');
INSERT INTO `config` VALUES ('CountryOfOperation','US');
INSERT INTO `config` VALUES ('CreditingControlledItems_MustExist','0');
INSERT INTO `config` VALUES ('DB_Maintenance','1');
INSERT INTO `config` VALUES ('DB_Maintenance_LastRun','2015-02-06');
INSERT INTO `config` VALUES ('DefaultBlindPackNote','1');
INSERT INTO `config` VALUES ('DefaultCreditLimit','1000');
INSERT INTO `config` VALUES ('DefaultCustomerType','1');
INSERT INTO `config` VALUES ('DefaultDateFormat','d/m/Y');
INSERT INTO `config` VALUES ('DefaultDisplayRecordsMax','50');
INSERT INTO `config` VALUES ('DefaultFactoryLocation','MEL');
INSERT INTO `config` VALUES ('DefaultPriceList','DE');
INSERT INTO `config` VALUES ('DefaultSupplierType','1');
INSERT INTO `config` VALUES ('DefaultTaxCategory','1');
INSERT INTO `config` VALUES ('Default_Shipper','1');
INSERT INTO `config` VALUES ('DefineControlledOnWOEntry','1');
INSERT INTO `config` VALUES ('DispatchCutOffTime','14');
INSERT INTO `config` VALUES ('DoFreightCalc','0');
INSERT INTO `config` VALUES ('EDIHeaderMsgId','D:01B:UN:EAN010');
INSERT INTO `config` VALUES ('EDIReference','WEBERP');
INSERT INTO `config` VALUES ('EDI_Incoming_Orders','companies/test/EDI_Incoming_Orders');
INSERT INTO `config` VALUES ('EDI_MsgPending','companies/test/EDI_Pending');
INSERT INTO `config` VALUES ('EDI_MsgSent','companies/test/EDI__Sent');
INSERT INTO `config` VALUES ('ExchangeRateFeed','Google');
INSERT INTO `config` VALUES ('Extended_CustomerInfo','1');
INSERT INTO `config` VALUES ('Extended_SupplierInfo','1');
INSERT INTO `config` VALUES ('FactoryManagerEmail','manager@company.com');
INSERT INTO `config` VALUES ('FreightChargeAppliesIfLessThan','1000');
INSERT INTO `config` VALUES ('FreightTaxCategory','1');
INSERT INTO `config` VALUES ('FrequentlyOrderedItems','0');
INSERT INTO `config` VALUES ('geocode_integration','0');
INSERT INTO `config` VALUES ('GoogleTranslatorAPIKey','');
INSERT INTO `config` VALUES ('HTTPS_Only','0');
INSERT INTO `config` VALUES ('InventoryManagerEmail','test@company.com');
INSERT INTO `config` VALUES ('InvoicePortraitFormat','0');
INSERT INTO `config` VALUES ('InvoiceQuantityDefault','1');
INSERT INTO `config` VALUES ('ItemDescriptionLanguages','fr_FR.utf8,');
INSERT INTO `config` VALUES ('LogPath','');
INSERT INTO `config` VALUES ('LogSeverity','0');
INSERT INTO `config` VALUES ('MaxImageSize','300');
INSERT INTO `config` VALUES ('MonthsAuditTrail','1');
INSERT INTO `config` VALUES ('NumberOfMonthMustBeShown','6');
INSERT INTO `config` VALUES ('NumberOfPeriodsOfStockUsage','12');
INSERT INTO `config` VALUES ('OverChargeProportion','30');
INSERT INTO `config` VALUES ('OverReceiveProportion','20');
INSERT INTO `config` VALUES ('PackNoteFormat','1');
INSERT INTO `config` VALUES ('PageLength','48');
INSERT INTO `config` VALUES ('part_pics_dir','companies/weberpdemo/part_pics');
INSERT INTO `config` VALUES ('PastDueDays1','30');
INSERT INTO `config` VALUES ('PastDueDays2','60');
INSERT INTO `config` VALUES ('PO_AllowSameItemMultipleTimes','1');
INSERT INTO `config` VALUES ('ProhibitJournalsToControlAccounts','1');
INSERT INTO `config` VALUES ('ProhibitNegativeStock','0');
INSERT INTO `config` VALUES ('ProhibitPostingsBefore','2013-12-31');
INSERT INTO `config` VALUES ('PurchasingManagerEmail','test@company.com');
INSERT INTO `config` VALUES ('QualityCOAText','');
INSERT INTO `config` VALUES ('QualityLogSamples','0');
INSERT INTO `config` VALUES ('QualityProdSpecText','');
INSERT INTO `config` VALUES ('QuickEntries','10');
INSERT INTO `config` VALUES ('RadioBeaconFileCounter','/home/RadioBeacon/FileCounter');
INSERT INTO `config` VALUES ('RadioBeaconFTP_user_name','RadioBeacon ftp server user name');
INSERT INTO `config` VALUES ('RadioBeaconHomeDir','/home/RadioBeacon');
INSERT INTO `config` VALUES ('RadioBeaconStockLocation','BL');
INSERT INTO `config` VALUES ('RadioBraconFTP_server','192.168.2.2');
INSERT INTO `config` VALUES ('RadioBreaconFilePrefix','ORDXX');
INSERT INTO `config` VALUES ('RadionBeaconFTP_user_pass','Radio Beacon remote ftp server password');
INSERT INTO `config` VALUES ('reports_dir','companies/weberpdemo/reportwriter');
INSERT INTO `config` VALUES ('RequirePickingNote','0');
INSERT INTO `config` VALUES ('RomalpaClause','Ownership will not pass to the buyer until the goods have been paid for in full.');
INSERT INTO `config` VALUES ('ShopAboutUs','This web-shop software has been developed by Logic Works Ltd for webERP. For support contact Phil Daintree by rn<a href=\\\"mailto:support@logicworks.co.nz\\\">email</a>rn');
INSERT INTO `config` VALUES ('ShopAllowBankTransfer','1');
INSERT INTO `config` VALUES ('ShopAllowCreditCards','1');
INSERT INTO `config` VALUES ('ShopAllowPayPal','1');
INSERT INTO `config` VALUES ('ShopAllowSurcharges','1');
INSERT INTO `config` VALUES ('ShopBankTransferSurcharge','0.0');
INSERT INTO `config` VALUES ('ShopBranchCode','ANGRY');
INSERT INTO `config` VALUES ('ShopContactUs','For support contact Logic Works Ltd by rn<a href=\\\"mailto:support@logicworks.co.nz\\\">email</a>');
INSERT INTO `config` VALUES ('ShopCreditCardBankAccount','1030');
INSERT INTO `config` VALUES ('ShopCreditCardGateway','SwipeHQ');
INSERT INTO `config` VALUES ('ShopCreditCardSurcharge','2.95');
INSERT INTO `config` VALUES ('ShopDebtorNo','ANGRY');
INSERT INTO `config` VALUES ('ShopFreightMethod','NoFreight');
INSERT INTO `config` VALUES ('ShopFreightPolicy','Shipping information');
INSERT INTO `config` VALUES ('ShopManagerEmail','shopmanager@yourdomain.com');
INSERT INTO `config` VALUES ('ShopMode','test');
INSERT INTO `config` VALUES ('ShopName','webERP Demo Store');
INSERT INTO `config` VALUES ('ShopPayFlowMerchant','');
INSERT INTO `config` VALUES ('ShopPayFlowPassword','');
INSERT INTO `config` VALUES ('ShopPayFlowUser','');
INSERT INTO `config` VALUES ('ShopPayFlowVendor','');
INSERT INTO `config` VALUES ('ShopPayPalBankAccount','1040');
INSERT INTO `config` VALUES ('ShopPaypalCommissionAccount','1');
INSERT INTO `config` VALUES ('ShopPayPalPassword','');
INSERT INTO `config` VALUES ('ShopPayPalProPassword','');
INSERT INTO `config` VALUES ('ShopPayPalProSignature','');
INSERT INTO `config` VALUES ('ShopPayPalProUser','');
INSERT INTO `config` VALUES ('ShopPayPalSignature','');
INSERT INTO `config` VALUES ('ShopPayPalSurcharge','3.4');
INSERT INTO `config` VALUES ('ShopPayPalUser','');
INSERT INTO `config` VALUES ('ShopPrivacyStatement','<h2>We are committed to protecting your privacy.</h2><p>We recognise that your personal information is confidential and we understand that it is important for you to know how we treat your personal information. Please read on for more information about our Privacy Policy.</p><ul><li><h2>1. What information do we collect and how do we use it?</h2><br />We use the information it collects from you for the following purposes:<ul><li>To assist us in providing you with a quality service</li><li>To respond to, and process, your request</li><li>To notify competition winners or fulfil promotional obligations</li><li>To inform you of, and provide you with, new and existing products and services offered by us from time to time </li></ul><p>Any information we collect will not be used in ways that you have not consented to.</p><p>If you send us an email, we will store your email address and the contents of the email. This information will only be used for the purpose for which you have provided it. Electronic mail submitted to us is handled and saved according to the provisions of the the relevant statues.</p><p>When we offer contests and promotions, customers who choose to enter are asked to provide personal information. This information may then be used by us to notify winners, or to fulfil promotional obligations.</p><p>We may use the information we collect to occasionally notify you about important functionality changes to our website, new and special offers we think you will find valuable. If at any stage you no longer wish to receive these notifications you may opt out by sending us an email.</p><p>We do monitor this website in order to identify user trends and to improve the site if necessary. Any of this information, such as the type of site browser your computer has, will be used only in aggregate form and your individual details will not be identified.</p></li><li><h2>2. How do we store and protect your personal information and who has access to that information?</h2><p>As required by statute, we follow strict procedures when storing and using the information you have provided.</p><p>We do not sell, trade or rent your personal information to others. We may provide aggregate statistics about our customers and website trends. However, these statistics will not have any personal information which would identify you.</p><p>Only specific employees within our company are able to access your personal data.</p><p>This policy means that we may require proof of identity before we disclose any information to you.</p></li><li><h2>3. What should I do if I want to change my details or if I don??????????????????????????????????????????????????????????????????????????????????t want to be contacted any more?</h2><p>At any stage you have the right to access and amend or update your personal details. If you do not want to receive any communications from us you may opt out by contacting us see <a href=\\\"index.php?Page=ContactUs\\\">the Contact Us Page</a></p></li><li><h2>4. What happens if we decide to change this Privacy Policy?</h2><p>If we change any aspect of our Privacy Policy we will post these changes on this page so that you are always aware of how we are treating your personal information.</p></li><li><h2>5. How can you contact us if you have any questions, comments or concerns about our Privacy Policy?</h2><p>We welcome any questions or comments you may have please email us via the contact details provided on our <a href=\\\"index.php?Page=ContactUs\\\">Contact Us Page</a></p></li></ul><p>Please also refer to our <a href=\\\"index.php?Page=TermsAndConditions\\\">Terms and Conditions</a> for more information.</p>');
INSERT INTO `config` VALUES ('ShopShowOnlyAvailableItems','0');
INSERT INTO `config` VALUES ('ShopShowQOHColumn','1');
INSERT INTO `config` VALUES ('ShopStockLocations','MEL,TOR');
INSERT INTO `config` VALUES ('ShopSurchargeStockID','PAYTSURCHARGE');
INSERT INTO `config` VALUES ('ShopSwipeHQAPIKey','');
INSERT INTO `config` VALUES ('ShopSwipeHQMerchantID','');
INSERT INTO `config` VALUES ('ShopTermsConditions','<p>These terms cover the use of this website. Use includes visits to our sites, purchases on our sites, participation in our database and promotions. These terms of use apply to you when you use our websites. Please read these terms carefully - if you need to refer to them again they can be accessed from the link at the bottom of any page of our websites.</p><br /><ul><li><h2>1. Content</h2><p>While we endeavour to supply accurate information on this site, errors and omissions may occur. We do not accept any liability, direct or indirect, for any loss or damage which may directly or indirectly result from any advice, opinion, information, representation or omission whether negligent or otherwise, contained on this site. You are solely responsible for the actions you take in reliance on the content on, or accessed, through this site.</p><p>We reserve the right to make changes to the content on this site at any time and without notice.</p><p>To the extent permitted by law, we make no warranties in relation to the merchantability, fitness for purpose, freedom from computer virus, accuracy or availability of this web site or any other web site.</p></li><li><h2>2. Making a contract with us</h2><p>When you place an order with us, you are making an offer to buy goods. We will send you an e-mail to confirm that we have received and accepted your order, which indicates that a contract has been made between us. We will take payment from you when we accept your order. In the unlikely event that the goods are no longer available, we will refund your payment to the account it originated from, and advise that the goods are no longer available.</p><p>An order is placed on our website via adding a product to the shopping cart and proceeding through our checkout process. The checkout process includes giving us delivery and any other relevant details for your order, entering payment information and submitting your order. The final step consists of a confirmation page with full details of your order, which you are able to print as a receipt of your order. We will also email you with confirmation of your order.</p><p>We reserve the right to refuse or cancel any orders that we believe, solely by our own judgement, to be placed for commercial purposes, e.g. any kind of reseller. We also reserve the right to refuse or cancel any orders that we believe, solely by our own judgement, to have been placed fraudulently.</p><p>We reserve the right to limit the number of an item customers can purchase in a single transaction.</p></li><li><h2>3. Payment options</h2><p>We currently accept the following credit cards:</p><ul><li>Visa</li><li>MasterCard</li><li>American Express</li></ul>You can also pay using PayPal and internet bank transfer. Surcharges may apply for payment by PayPal or credit cards.</p></li><li><h2>4. Pricing</h2><p>All prices listed are inclusive of relevant taxes.  All prices are correct when published. Please note that we reserve the right to alter prices at any time for any reason. If this should happen after you have ordered a product, we will contact you prior to processing your order. Online and in store pricing may differ.</p></li><li><h2>5. Website and Credit Card Security</h2><p>We want you to have a safe and secure shopping experience online. All payments via our sites are processed using SSL (Secure Socket Layer) protocol, whereby sensitive information is encrypted to protect your privacy.</p><p>You can help to protect your details from unauthorised access by logging out each time you finish using the site, particularly if you are doing so from a public or shared computer.</p><p>For security purposes certain transactions may require proof of identification.</p></li><li><h2>6. Delivery and Delivery Charges</h2><p>We do not deliver to Post Office boxes.</p><p>Please note that a signature is required for all deliveries. The goods become the recipient??????????????????????????????????????????????????????????????????????????????????s property and responsibility once they have been signed for at the time of delivery. If goods are lost or damaged in transit, please contact us within 7 business days <a href=\\\"index.php?Page=ContactUs\\\">see Contact Us page for contact details</a>. We will use this delivery information to make a claim against our courier company. We will offer you the choice of a replacement or a full refund, once we have received confirmation from our courier company that delivery was not successful.</p></li><li><h2>7. Restricted Products</h2><p>Some products on our site carry an age restriction, if a product you have selected is R16 or R18 a message will appear in the cart asking you to confirm you are an appropriate age to purchase the item(s).  Confirming this means that you are of an eligible age to purchase the selected product(s).  You are also agreeing that you are not purchasing the item on behalf of a person who is not the appropriate age.</p></li><li><h2>8. Delivery Period</h2><p>Delivery lead time for products may vary. Deliveries to rural addresses may take longer.  You will receive an email that confirms that your order has been dispatched.</p><p>To ensure successful delivery, please provide a delivery address where someone will be present during business hours to sign for the receipt of your package. You can track your order by entering the tracking number emailed to you in the dispatch email at the Courier\\\'s web-site.</p></li><li><h2>9. Disclaimer</h2><p>Our websites are intended to provide information for people shopping our products and accessing our services, including making purchases via our website and registering on our database to receive e-mails from us.</p><p>While we endeavour to supply accurate information on this site, errors and omissions may occur. We do not accept any liability, direct or indirect, for any loss or damage which may directly or indirectly result from any advice, opinion, information, representation or omission whether negligent or otherwise, contained on this site. You are solely responsible for the actions you take in reliance on the content on, or accessed, through this site.</p><p>We reserve the right to make changes to the content on this site at any time and without notice.</p><p>To the extent permitted by law, we make no warranties in relation to the merchantability, fitness for purpose, freedom from computer virus, accuracy or availability of this web site or any other web site.</p></li><li><h2>10. Links</h2><p>Please note that although this site has some hyperlinks to other third party websites, these sites have not been prepared by us are not under our control. The links are only provided as a convenience, and do not imply that we endorse, check, or approve of the third party site. We are not responsible for the privacy principles or content of these third party sites. We are not responsible for the availability of any of these links.</p></li><li><h2>11. Jurisdiction</h2><p>This website is governed by, and is to be interpreted in accordance with, the laws of  ????.</p></li><li><h2>12. Changes to this Agreement</h2><p>We reserve the right to alter, modify or update these terms of use. These terms apply to your order. We may change our terms and conditions at any time, so please do not assume that the same terms will apply to future orders.</p></li></ul>');
INSERT INTO `config` VALUES ('ShopTitle','Shop Home');
INSERT INTO `config` VALUES ('ShowStockidOnImages','0');
INSERT INTO `config` VALUES ('ShowValueOnGRN','1');
INSERT INTO `config` VALUES ('Show_Settled_LastMonth','1');
INSERT INTO `config` VALUES ('SmtpSetting','0');
INSERT INTO `config` VALUES ('SO_AllowSameItemMultipleTimes','1');
INSERT INTO `config` VALUES ('StandardCostDecimalPlaces','2');
INSERT INTO `config` VALUES ('TaxAuthorityReferenceName','');
INSERT INTO `config` VALUES ('UpdateCurrencyRatesDaily','2015-02-06');
INSERT INTO `config` VALUES ('VersionNumber','4.12.2');
INSERT INTO `config` VALUES ('WeightedAverageCosting','0');
INSERT INTO `config` VALUES ('WikiApp','0');
INSERT INTO `config` VALUES ('WikiPath','wiki');
INSERT INTO `config` VALUES ('WorkingDaysWeek','5');
INSERT INTO `config` VALUES ('YearEnd','3');

--
-- Dumping data for table `contractbom`
--

INSERT INTO `contractbom` VALUES ('Test123','SALT','ASS',2);
INSERT INTO `contractbom` VALUES ('Test123','TAPE1','ASS',3);
INSERT INTO `contractbom` VALUES ('Test123','TAPE2','ASS',4);

--
-- Dumping data for table `contractcharges`
--


--
-- Dumping data for table `contractreqts`
--

INSERT INTO `contractreqts` VALUES (3,'Test123','Other stuff',2,5.95);
INSERT INTO `contractreqts` VALUES (4,'Test123','And that thing too',4,85);

--
-- Dumping data for table `contracts`
--

INSERT INTO `contracts` VALUES ('Test123','Testing manufact tape','14','14','TOR',2,'TAPE',40,'dsssa',50,32,'2013-07-21','',1);

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` VALUES ('Australian Dollars','AUD','Australia','cents',2,1.2824,0);
INSERT INTO `currencies` VALUES ('Swiss Francs','CHF','Swizerland','centimes',2,0.9213,0);
INSERT INTO `currencies` VALUES ('Euro','EUR','Euroland','cents',2,0.8713,1);
INSERT INTO `currencies` VALUES ('Pounds','GBP','England','Pence',2,0.6525,0);
INSERT INTO `currencies` VALUES ('Kenyian Shillings','KES','Kenya','none',0,91.3495,0);
INSERT INTO `currencies` VALUES ('US Dollars','USD','United States','Cents',2,1,1);

--
-- Dumping data for table `custallocns`
--

INSERT INTO `custallocns` VALUES (1,109.2500,'2013-06-25',30,34);

--
-- Dumping data for table `custbranch`
--

INSERT INTO `custbranch` VALUES ('11','11','Angus Routledge &amp; Co','123 Alexander Road','Roundhay','Leeds','3200','','United Kingdom',0.000000,0.000000,1,'TR','ERI',0,'454422111','','Angus McDougall','angus@angry.com','TOR',2,1,1,0,'','','','','','','','');
INSERT INTO `custbranch` VALUES ('12','12','Angus Routledge &amp; Co','123 Alexander Road','Roundhay','Leeds','3211','','United Kingdom',0.000000,0.000000,1,'TR','ERI',0,'212234566','','Angus McDougall','angus@angry.com','TOR',2,1,1,0,'','','','','','','','');
INSERT INTO `custbranch` VALUES ('13','13','Angus Routledge &amp; Co','123 Alexander Road','Roundhay','Leeds','LE189','','United Kingdom',0.000000,0.000000,1,'TR','ERI',0,'0291882001','','Angus McDougall','angus@angry.com','TOR',2,1,1,0,'','','','','','','','');
INSERT INTO `custbranch` VALUES ('14','14','Angus Routledge &amp; Co','123 Alexander Road','Roundhay','Leeds','2113','','United Kingdom',0.000000,0.000000,1,'TR','ERI',0,'12323434355566778899','','Angus McDougall','angus@angry.com','TOR',2,1,1,0,'','','','','','','','');
INSERT INTO `custbranch` VALUES ('16','16','Logic Works Ltd','34 Marram Way','Peka Peka','RD1 Waiakane','Kapiti','5134','New Zealand',0.000000,0.000000,1,'TR','ERI',0,'64275567890','','Phil Daintree','phil@logicworks.co.nz','TOR',2,1,1,0,'34 Marram Way','Peka Peka','RD1 Waiakane','Kapiti','5134','New Zealand','','');
INSERT INTO `custbranch` VALUES ('8','8','Angus Routledge &amp; Co','123 Alexander Road','Roundhay','Leeds','43990','','United Kingdom',0.000000,0.000000,1,'TR','ERI',0,'124544665','','Angus McDougall','angus@angry.com','TOR',2,1,1,0,'','','','','','','','');
INSERT INTO `custbranch` VALUES ('ANGRY','ANGRY','Angus Rouledge - Toronto','P O Box 671','Gowerbridge','Upperton','Toronto ','Canada','United States',0.000000,0.000000,3,'TR','ERI',0,'0422 2245 2213','0422 2245 2215','Granville Thomas','graville@angry.com','TOR',2,1,1,0,'','','','','','','','');
INSERT INTO `custbranch` VALUES ('ANGRYFL','ANGRY','Angus Rouledge - Florida','1821 Sunnyside','Ft Lauderdale','Florida','42554','','United States',0.000000,0.000000,3,'FL','PHO',0,'2445 2232 524','2445 2232 522','Wendy Blowers','wendy@angry.com','TOR',1,1,1,0,'','','','','','','Watch out can bite!','');
INSERT INTO `custbranch` VALUES ('DUMBLE','DUMBLE','Dumbledoor McGonagal & Co','Hogwarts castle','Platform 9.75','','','','',0.000000,0.000000,1,'TR','ERI',0,'Owls only','Owls only','Minerva McGonagal','mmgonagal@hogwarts.edu.uk','TOR',3,10,1,0,'','','','','','','','');
INSERT INTO `custbranch` VALUES ('JOLOMU','JOLOMU','Lorrima Productions Inc','3215 Great Western Highway','Blubberhouses','Yorkshire','England','','',0.000000,0.000000,20,'FL','PHO',0,'+44 812 211456','+44 812 211 554','Jo Lomu','jolomu@lorrima.co.uk','TOR',3,1,1,0,'','','','','','','','');
INSERT INTO `custbranch` VALUES ('KES','KES','Ken Estoban','','','','','','',0.000000,0.000000,0,'DE','DE',0,'','','','','MEL',1,1,1,0,'','','','','','','','');
INSERT INTO `custbranch` VALUES ('NEWTEST','NEWTEST','New Customer','','','','','','United States',0.000000,0.000000,0,'123','DE',0,'','','','','AN',1,1,1,0,'','','','','','','','');
INSERT INTO `custbranch` VALUES ('QUARTER','QUARTER','Quarter Back to Back','1356 Union Drive','Holborn','England','','','',0.000000,0.000000,5,'FL','ERI',0,'123456','1234567','','','TOR',3,1,1,0,'','','','','','','','');
INSERT INTO `custbranch` VALUES ('QUIC','QUICK','Quick Brown PLC','Fox Street','Jumped Over','The Lazy Dog','','','',0.000000,0.000000,1,'FL','ERI',0,'','','','','TOR',1,1,1,0,'','','','','','','','');
INSERT INTO `custbranch` VALUES ('SLOW','QUICK','Slow Dog','Hunstman Road','Woofton','','','','',0.000000,0.000000,1,'TR','ERI',0,'','','Staffordshire Terrier','','TOR',2,1,1,0,'','','','','','','','');
INSERT INTO `custbranch` VALUES ('WEB0000017','WEB0000017','Phil Daintree','8 James Nairn Grove','','','Upper Hutt','5018','New Zealand',0.000000,0.000000,1,'TR','ERI',0,'+64(0)275567890','','Phil Daintree','phil@logicworks.co.nz','TOR',2,1,1,0,'8 James Nairn Grove','','','Upper Hutt','5018','New Zealand','','');
INSERT INTO `custbranch` VALUES ('WEB0000018','WEB0000018','Logic Works Ltd','34 Marram Way','Peka Peka, RD1 Waikanae','','Kapiti','5134','New Zealand',0.000000,0.000000,1,'TR','ERI',0,'04 528 9514','','Phil Daintree','phil@logicworks.co.nz','TOR',2,1,1,0,'34 Marram Way','Peka Peka, RD1 Waikanae','','Kapiti','5134','New Zealand','','');
INSERT INTO `custbranch` VALUES ('WEB0000019','WEB0000019','Logic Works Ltd','8 James Nairn Grove','James Nairn Grove','Riverstone Terraces','Upper Hutt','5018','New Zealand',0.000000,0.000000,1,'TR','ERI',0,'+6445289514','','Phil Daintree','phil@logicworks.co.nz','TOR',2,1,1,0,'8 James Nairn Grove','James Nairn Grove','Riverstone Terraces','Upper Hutt','5018','New Zealand','','');

--
-- Dumping data for table `custcontacts`
--

INSERT INTO `custcontacts` VALUES (2,'ANGRY','Hamish McKay','CEO','12334302','Whisky drinker single malt only','');
INSERT INTO `custcontacts` VALUES (5,'ANGRY','Bob (Robert) Bruce','Chairman','10292811','','');
INSERT INTO `custcontacts` VALUES (6,'ANGRY','Billy Wallace','Mover and Shaker','12455778','English Hater','');

--
-- Dumping data for table `custitem`
--


--
-- Dumping data for table `custnotes`
--


--
-- Dumping data for table `debtorsmaster`
--

INSERT INTO `debtorsmaster` VALUES ('11','Angus Routledge &amp; Co','123 Alexander Road','Roundhay','Leeds','3200','','United Kingdom','USD','DE','2013-06-16 00:00:00',1,'CA',0,0,0,NULL,1000,0,'',0,0,'','email','','','','',0,1,'en_GB.utf8');
INSERT INTO `debtorsmaster` VALUES ('12','Angus Routledge &amp; Co','123 Alexander Road','Roundhay','Leeds','3211','','United Kingdom','USD','DE','2013-06-16 00:00:00',1,'CA',0,0,0,NULL,1000,0,'',0,0,'','email','','','','',0,1,'en_GB.utf8');
INSERT INTO `debtorsmaster` VALUES ('13','Angus Routledge &amp; Co','123 Alexander Road','Roundhay','Leeds','LE189','','United Kingdom','USD','DE','2013-06-16 00:00:00',1,'CA',0,0,0,NULL,1000,0,'',0,0,'','email','','','','',0,1,'en_GB.utf8');
INSERT INTO `debtorsmaster` VALUES ('14','Angus Routledge &amp; Co','123 Alexander Road','Roundhay','Leeds','2113','','United Kingdom','USD','DE','2013-06-16 00:00:00',1,'CA',0,0,0,NULL,1000,0,'',0,0,'','email','','','','',0,1,'en_GB.utf8');
INSERT INTO `debtorsmaster` VALUES ('16','Logic Works Ltd','34 Marram Way','Peka Peka','RD1 Waiakane','Kapiti','5134','New Zealand','USD','DE','2013-06-23 00:00:00',1,'CA',0,0,8.659050435,'2013-06-24 00:00:00',1000,0,'',0,0,'','email','','','','',0,1,'en_GB.utf8');
INSERT INTO `debtorsmaster` VALUES ('8','Angus Routledge &amp; Co','123 Alexander Road','Roundhay','Leeds','43990','','United Kingdom','USD','DE','2013-06-16 00:00:00',1,'CA',0,0,0,NULL,1000,0,'',0,0,'','email','','','','',0,1,'fr_FR.utf8');
INSERT INTO `debtorsmaster` VALUES ('ANGRY','Angus Rouledge Younger &amp; Son','P O Box 67','Gowerbridge','Upperton','Michigan','','United States','USD','DE','2005-04-30 00:00:00',1,'CA',0,0,50,'2013-06-16 00:00:00',2500,0,'',0,0,'','email','','','','1344-654-112',0,1,'en_GB.utf8');
INSERT INTO `debtorsmaster` VALUES ('DUMBLE','Dumbledoor McGonagal & Co','Hogwarts castle','Platform 9.75','','','','','GBP','DE','2005-06-18 00:00:00',1,'30',0,0,10,'2012-12-16 00:00:00',1000,0,'',0,0,'','email','','','','',0,1,'en_GB.utf8');
INSERT INTO `debtorsmaster` VALUES ('JOLOMU','Lorrima Productions Inc','3215 Great Western Highway','Blubberhouses','Yorkshire','England','','','GBP','DE','2005-06-15 00:00:00',1,'30',0,0,0,NULL,1000,0,'',0,0,'','email','','','','',0,1,'en_GB.utf8');
INSERT INTO `debtorsmaster` VALUES ('KES','Ken Estoban','','','','','','','KES','DE','2012-10-25 00:00:00',1,'20',0,0,50094,'2012-11-18 00:00:00',1000,0,'',0,0,'','email','','','','',0,1,'en_GB.utf8');
INSERT INTO `debtorsmaster` VALUES ('NEWTEST','New Customer','','','','','','United States','USD','DE','2013-06-29 00:00:00',1,'20',0,0,0,NULL,1000,0,'',0,0,'','email','','','','',0,1,'en_GB.utf8');
INSERT INTO `debtorsmaster` VALUES ('QUARTER','Quarter Back to Back','1356 Union Drive','Holborn','England','','','','CHF','DE','2005-09-03 00:00:00',1,'20',0,0,0,NULL,1000,0,'',0,0,'','email','','','','',0,1,'en_GB.utf8');
INSERT INTO `debtorsmaster` VALUES ('QUICK','Quick Brown PLC','Fox Street','Jumped Over','The Lazy Dog','','','','USD','DE','2007-01-30 00:00:00',1,'20',0,0,0,NULL,1000,0,'',0,0,'','email','','','','',0,1,'en_GB.utf8');
INSERT INTO `debtorsmaster` VALUES ('WEB0000017','Phil Daintree','8 James Nairn Grove','','','Upper Hutt','5018','New Zealand','USD','DE','2013-10-06 09:09:46',1,'CA',0,0,0,NULL,2500,0,'',0,0,'','email','','','','',0,1,'en_GB.utf8');
INSERT INTO `debtorsmaster` VALUES ('WEB0000018','Logic Works Ltd','34 Marram Way','Peka Peka, RD1 Waikanae','','Kapiti','5134','New Zealand','USD','DE','2014-08-31 12:32:16',1,'CA',0,0,0,NULL,2500,0,'',0,0,'','email','','','','',0,1,'en_GB.utf8');
INSERT INTO `debtorsmaster` VALUES ('WEB0000019','Logic Works Ltd','8 James Nairn Grove','James Nairn Grove','Riverstone Terraces','Upper Hutt','5018','New Zealand','USD','DE','2015-01-31 11:40:36',1,'CA',0,0,0,NULL,2500,0,'',0,0,'','email','','','','',0,1,'en_GB.utf8');

--
-- Dumping data for table `debtortrans`
--

INSERT INTO `debtortrans` VALUES (30,1,12,'16','16','2013-06-23 19:51:00','2013-06-23 19:51:00',19,1,'','',0,1,-109.25,0,0,0,0,-109.25,'web payment',0,0,'',1,'');
INSERT INTO `debtortrans` VALUES (31,2,12,'16','16','2013-06-24 21:53:00','2013-06-24 21:53:00',19,0,'','',0,1,-14.16,0,0,0,0,0,'web payment',0,0,'',1,'');
INSERT INTO `debtortrans` VALUES (32,3,12,'16','16','2013-06-24 22:36:00','2013-06-24 22:36:00',19,0,'V78R4D18BB1E','',4,1,-14.16,0,0,0,0,0,'web payment',0,0,'',1,'ERI');
INSERT INTO `debtortrans` VALUES (33,4,12,'16','16','2013-06-24 22:38:00','2013-06-24 22:38:00',19,0,'V18R4E55E804','',5,1,-8.66,0,0,0,0,0,'web payment',0,0,'',1,'ERI');
INSERT INTO `debtortrans` VALUES (34,1,10,'16','16','2013-06-26 00:00:00','2013-06-25 21:42:59',19,1,'','DE',1,1,97.24,12.01,0,0,0,109.25,'',1,0,'',1,'ERI');
INSERT INTO `debtortrans` VALUES (35,2,10,'12','12','2013-09-07 00:00:00','2013-09-06 21:29:53',22,0,'','DE',7,1,5,0.62,0,0,0,0,'',1,0,'',1,'ERI');

--
-- Dumping data for table `debtortranstaxes`
--

INSERT INTO `debtortranstaxes` VALUES (34,11,7.14717675);
INSERT INTO `debtortranstaxes` VALUES (34,12,4.862025);
INSERT INTO `debtortranstaxes` VALUES (35,11,0.3675);
INSERT INTO `debtortranstaxes` VALUES (35,12,0.25);

--
-- Dumping data for table `debtortype`
--

INSERT INTO `debtortype` VALUES (1,'Default');

--
-- Dumping data for table `debtortypenotes`
--


--
-- Dumping data for table `deliverynotes`
--


--
-- Dumping data for table `departments`
--

INSERT INTO `departments` VALUES (1,'Workshop','admin');
INSERT INTO `departments` VALUES (2,'Customer Services','admin');

--
-- Dumping data for table `discountmatrix`
--

INSERT INTO `discountmatrix` VALUES ('DE','DE',3,0.025);

--
-- Dumping data for table `edi_orders_seg_groups`
--

INSERT INTO `edi_orders_seg_groups` VALUES (0,1,0);
INSERT INTO `edi_orders_seg_groups` VALUES (1,9999,0);
INSERT INTO `edi_orders_seg_groups` VALUES (2,99,0);
INSERT INTO `edi_orders_seg_groups` VALUES (3,99,2);
INSERT INTO `edi_orders_seg_groups` VALUES (5,5,2);
INSERT INTO `edi_orders_seg_groups` VALUES (6,5,0);
INSERT INTO `edi_orders_seg_groups` VALUES (7,5,0);
INSERT INTO `edi_orders_seg_groups` VALUES (8,10,0);
INSERT INTO `edi_orders_seg_groups` VALUES (9,9999,8);
INSERT INTO `edi_orders_seg_groups` VALUES (10,10,0);
INSERT INTO `edi_orders_seg_groups` VALUES (11,10,10);
INSERT INTO `edi_orders_seg_groups` VALUES (12,5,0);
INSERT INTO `edi_orders_seg_groups` VALUES (13,99,0);
INSERT INTO `edi_orders_seg_groups` VALUES (14,5,13);
INSERT INTO `edi_orders_seg_groups` VALUES (15,10,0);
INSERT INTO `edi_orders_seg_groups` VALUES (19,99,0);
INSERT INTO `edi_orders_seg_groups` VALUES (20,1,19);
INSERT INTO `edi_orders_seg_groups` VALUES (21,1,19);
INSERT INTO `edi_orders_seg_groups` VALUES (22,2,19);
INSERT INTO `edi_orders_seg_groups` VALUES (23,1,19);
INSERT INTO `edi_orders_seg_groups` VALUES (24,5,19);
INSERT INTO `edi_orders_seg_groups` VALUES (28,200000,0);
INSERT INTO `edi_orders_seg_groups` VALUES (32,25,28);
INSERT INTO `edi_orders_seg_groups` VALUES (33,9999,28);
INSERT INTO `edi_orders_seg_groups` VALUES (34,99,28);
INSERT INTO `edi_orders_seg_groups` VALUES (36,5,34);
INSERT INTO `edi_orders_seg_groups` VALUES (37,9999,28);
INSERT INTO `edi_orders_seg_groups` VALUES (38,10,28);
INSERT INTO `edi_orders_seg_groups` VALUES (39,999,28);
INSERT INTO `edi_orders_seg_groups` VALUES (42,5,39);
INSERT INTO `edi_orders_seg_groups` VALUES (43,99,28);
INSERT INTO `edi_orders_seg_groups` VALUES (44,1,43);
INSERT INTO `edi_orders_seg_groups` VALUES (45,1,43);
INSERT INTO `edi_orders_seg_groups` VALUES (46,2,43);
INSERT INTO `edi_orders_seg_groups` VALUES (47,1,43);
INSERT INTO `edi_orders_seg_groups` VALUES (48,5,43);
INSERT INTO `edi_orders_seg_groups` VALUES (49,10,28);
INSERT INTO `edi_orders_seg_groups` VALUES (50,1,0);

--
-- Dumping data for table `edi_orders_segs`
--

INSERT INTO `edi_orders_segs` VALUES (1,'UNB',0,1);
INSERT INTO `edi_orders_segs` VALUES (2,'UNH',0,1);
INSERT INTO `edi_orders_segs` VALUES (3,'BGM',0,1);
INSERT INTO `edi_orders_segs` VALUES (4,'DTM',0,35);
INSERT INTO `edi_orders_segs` VALUES (5,'PAI',0,1);
INSERT INTO `edi_orders_segs` VALUES (6,'ALI',0,5);
INSERT INTO `edi_orders_segs` VALUES (7,'FTX',0,99);
INSERT INTO `edi_orders_segs` VALUES (8,'RFF',1,1);
INSERT INTO `edi_orders_segs` VALUES (9,'DTM',1,5);
INSERT INTO `edi_orders_segs` VALUES (10,'NAD',2,1);
INSERT INTO `edi_orders_segs` VALUES (11,'LOC',2,99);
INSERT INTO `edi_orders_segs` VALUES (12,'FII',2,5);
INSERT INTO `edi_orders_segs` VALUES (13,'RFF',3,1);
INSERT INTO `edi_orders_segs` VALUES (14,'CTA',5,1);
INSERT INTO `edi_orders_segs` VALUES (15,'COM',5,5);
INSERT INTO `edi_orders_segs` VALUES (16,'TAX',6,1);
INSERT INTO `edi_orders_segs` VALUES (17,'MOA',6,1);
INSERT INTO `edi_orders_segs` VALUES (18,'CUX',7,1);
INSERT INTO `edi_orders_segs` VALUES (19,'DTM',7,5);
INSERT INTO `edi_orders_segs` VALUES (20,'PAT',8,1);
INSERT INTO `edi_orders_segs` VALUES (21,'DTM',8,5);
INSERT INTO `edi_orders_segs` VALUES (22,'PCD',8,1);
INSERT INTO `edi_orders_segs` VALUES (23,'MOA',9,1);
INSERT INTO `edi_orders_segs` VALUES (24,'TDT',10,1);
INSERT INTO `edi_orders_segs` VALUES (25,'LOC',11,1);
INSERT INTO `edi_orders_segs` VALUES (26,'DTM',11,5);
INSERT INTO `edi_orders_segs` VALUES (27,'TOD',12,1);
INSERT INTO `edi_orders_segs` VALUES (28,'LOC',12,2);
INSERT INTO `edi_orders_segs` VALUES (29,'PAC',13,1);
INSERT INTO `edi_orders_segs` VALUES (30,'PCI',14,1);
INSERT INTO `edi_orders_segs` VALUES (31,'RFF',14,1);
INSERT INTO `edi_orders_segs` VALUES (32,'DTM',14,5);
INSERT INTO `edi_orders_segs` VALUES (33,'GIN',14,10);
INSERT INTO `edi_orders_segs` VALUES (34,'EQD',15,1);
INSERT INTO `edi_orders_segs` VALUES (35,'ALC',19,1);
INSERT INTO `edi_orders_segs` VALUES (36,'ALI',19,5);
INSERT INTO `edi_orders_segs` VALUES (37,'DTM',19,5);
INSERT INTO `edi_orders_segs` VALUES (38,'QTY',20,1);
INSERT INTO `edi_orders_segs` VALUES (39,'RNG',20,1);
INSERT INTO `edi_orders_segs` VALUES (40,'PCD',21,1);
INSERT INTO `edi_orders_segs` VALUES (41,'RNG',21,1);
INSERT INTO `edi_orders_segs` VALUES (42,'MOA',22,1);
INSERT INTO `edi_orders_segs` VALUES (43,'RNG',22,1);
INSERT INTO `edi_orders_segs` VALUES (44,'RTE',23,1);
INSERT INTO `edi_orders_segs` VALUES (45,'RNG',23,1);
INSERT INTO `edi_orders_segs` VALUES (46,'TAX',24,1);
INSERT INTO `edi_orders_segs` VALUES (47,'MOA',24,1);
INSERT INTO `edi_orders_segs` VALUES (48,'LIN',28,1);
INSERT INTO `edi_orders_segs` VALUES (49,'PIA',28,25);
INSERT INTO `edi_orders_segs` VALUES (50,'IMD',28,99);
INSERT INTO `edi_orders_segs` VALUES (51,'MEA',28,99);
INSERT INTO `edi_orders_segs` VALUES (52,'QTY',28,99);
INSERT INTO `edi_orders_segs` VALUES (53,'ALI',28,5);
INSERT INTO `edi_orders_segs` VALUES (54,'DTM',28,35);
INSERT INTO `edi_orders_segs` VALUES (55,'MOA',28,10);
INSERT INTO `edi_orders_segs` VALUES (56,'GIN',28,127);
INSERT INTO `edi_orders_segs` VALUES (57,'QVR',28,1);
INSERT INTO `edi_orders_segs` VALUES (58,'FTX',28,99);
INSERT INTO `edi_orders_segs` VALUES (59,'PRI',32,1);
INSERT INTO `edi_orders_segs` VALUES (60,'CUX',32,1);
INSERT INTO `edi_orders_segs` VALUES (61,'DTM',32,5);
INSERT INTO `edi_orders_segs` VALUES (62,'RFF',33,1);
INSERT INTO `edi_orders_segs` VALUES (63,'DTM',33,5);
INSERT INTO `edi_orders_segs` VALUES (64,'PAC',34,1);
INSERT INTO `edi_orders_segs` VALUES (65,'QTY',34,5);
INSERT INTO `edi_orders_segs` VALUES (66,'PCI',36,1);
INSERT INTO `edi_orders_segs` VALUES (67,'RFF',36,1);
INSERT INTO `edi_orders_segs` VALUES (68,'DTM',36,5);
INSERT INTO `edi_orders_segs` VALUES (69,'GIN',36,10);
INSERT INTO `edi_orders_segs` VALUES (70,'LOC',37,1);
INSERT INTO `edi_orders_segs` VALUES (71,'QTY',37,1);
INSERT INTO `edi_orders_segs` VALUES (72,'DTM',37,5);
INSERT INTO `edi_orders_segs` VALUES (73,'TAX',38,1);
INSERT INTO `edi_orders_segs` VALUES (74,'MOA',38,1);
INSERT INTO `edi_orders_segs` VALUES (75,'NAD',39,1);
INSERT INTO `edi_orders_segs` VALUES (76,'CTA',42,1);
INSERT INTO `edi_orders_segs` VALUES (77,'COM',42,5);
INSERT INTO `edi_orders_segs` VALUES (78,'ALC',43,1);
INSERT INTO `edi_orders_segs` VALUES (79,'ALI',43,5);
INSERT INTO `edi_orders_segs` VALUES (80,'DTM',43,5);
INSERT INTO `edi_orders_segs` VALUES (81,'QTY',44,1);
INSERT INTO `edi_orders_segs` VALUES (82,'RNG',44,1);
INSERT INTO `edi_orders_segs` VALUES (83,'PCD',45,1);
INSERT INTO `edi_orders_segs` VALUES (84,'RNG',45,1);
INSERT INTO `edi_orders_segs` VALUES (85,'MOA',46,1);
INSERT INTO `edi_orders_segs` VALUES (86,'RNG',46,1);
INSERT INTO `edi_orders_segs` VALUES (87,'RTE',47,1);
INSERT INTO `edi_orders_segs` VALUES (88,'RNG',47,1);
INSERT INTO `edi_orders_segs` VALUES (89,'TAX',48,1);
INSERT INTO `edi_orders_segs` VALUES (90,'MOA',48,1);
INSERT INTO `edi_orders_segs` VALUES (91,'TDT',49,1);
INSERT INTO `edi_orders_segs` VALUES (92,'UNS',50,1);
INSERT INTO `edi_orders_segs` VALUES (93,'MOA',50,1);
INSERT INTO `edi_orders_segs` VALUES (94,'CNT',50,1);
INSERT INTO `edi_orders_segs` VALUES (95,'UNT',50,1);

--
-- Dumping data for table `ediitemmapping`
--


--
-- Dumping data for table `edimessageformat`
--


--
-- Dumping data for table `emailsettings`
--


--
-- Dumping data for table `factorcompanies`
--


--
-- Dumping data for table `fixedassetcategories`
--

INSERT INTO `fixedassetcategories` VALUES ('PLANT','Plant and Equipment','1650','7750','8100','1670',0.2,1);

--
-- Dumping data for table `fixedassetlocations`
--

INSERT INTO `fixedassetlocations` VALUES ('HEADOF','Head Office','');
INSERT INTO `fixedassetlocations` VALUES ('TORONT','Toronto Warehouse','');

--
-- Dumping data for table `fixedassets`
--

INSERT INTO `fixedassets` VALUES (1,'','','HEADOF',0,0,'0000-00-00',0,'PLANT','test 1','Test 1',0,5,'0000-00-00');

--
-- Dumping data for table `fixedassettasks`
--


--
-- Dumping data for table `fixedassettrans`
--


--
-- Dumping data for table `freightcosts`
--


--
-- Dumping data for table `geocode_param`
--


--
-- Dumping data for table `gltrans`
--

INSERT INTO `gltrans` VALUES (1,0,7,0,'2013-02-08',15,'5500','CUTTING Change stock category',0,1,'',0);
INSERT INTO `gltrans` VALUES (2,0,7,0,'2013-02-08',15,'1460','CUTTING Change stock category',0,1,'',0);
INSERT INTO `gltrans` VALUES (3,20,37,0,'2013-02-08',15,'4600','CRUISE open item',150.61669829222,1,'',0);
INSERT INTO `gltrans` VALUES (4,20,37,0,'2013-02-08',15,'2100','CRUISE - Inv opninvoice GBP95.25 @ a rate of 0.6324',-150.61669829222,1,'',0);
INSERT INTO `gltrans` VALUES (5,10,4,0,'2013-02-11',15,'5000','ANGRY - TAPE2 x 1 @ 2.5000',2.5,1,'',0);
INSERT INTO `gltrans` VALUES (6,10,4,0,'2013-02-11',15,'1460','ANGRY - TAPE2 x 1 @ 2.5000',-2.5,1,'',0);
INSERT INTO `gltrans` VALUES (7,10,4,0,'2013-02-11',15,'4100','ANGRY - TAPE2 x 1 @ 15',-15,1,'',0);
INSERT INTO `gltrans` VALUES (8,10,4,0,'2013-02-11',15,'1100','ANGRY',16.5,1,'',0);
INSERT INTO `gltrans` VALUES (9,10,4,0,'2013-02-11',15,'2300','ANGRY',-1.5,1,'',0);
INSERT INTO `gltrans` VALUES (12,25,50,0,'2013-01-06',14,'1460','PO: 23 BINGO - DVD_ACTION - Action Series Bundle x 1 @ 16.22',16.22,1,'',0);
INSERT INTO `gltrans` VALUES (13,25,50,0,'2013-01-06',14,'2150','PO1360356432: 23 BINGO - DVD_ACTION - Action Series Bundle x 1 @ 16.22',-16.22,1,'',0);
INSERT INTO `gltrans` VALUES (14,25,51,0,'2013-02-09',15,'1460','PO: 25 CRUISE - TAPE1 - DFR-12  DFR Tape per Keystone spec x 100 @ 10.00',1000,1,'',0);
INSERT INTO `gltrans` VALUES (15,25,51,0,'2013-02-09',15,'2150','PO1360356915: 25 CRUISE - TAPE1 - DFR-12  DFR Tape per Keystone spec x 100 @ 10.00',-1000,1,'',0);
INSERT INTO `gltrans` VALUES (16,25,52,0,'2013-02-09',15,'1460','PO: 27 CRUISE - TAPE1 - DFR-12 - DFR Tape per Keystone spec x 10 @ 10.00',100,1,'',0);
INSERT INTO `gltrans` VALUES (17,25,52,0,'2013-02-09',15,'2150','PO1360357179: 27 CRUISE - TAPE1 - DFR-12 - DFR Tape per Keystone spec x 10 @ 10.00',-100,1,'',0);
INSERT INTO `gltrans` VALUES (18,35,19,0,'2013-02-09',15,'5700','TAPE2 cost was 2.5000 changed to 2.5 x Quantity on hand of -1',0,1,'',0);
INSERT INTO `gltrans` VALUES (19,35,19,0,'2013-02-09',15,'1460','TAPE2 cost was 2.5000 changed to 2.5 x Quantity on hand of -1',0,1,'',0);
INSERT INTO `gltrans` VALUES (20,10,5,0,'2013-02-11',15,'5000','ANGRY - SELLTAPE x 100 @ 0.01',1.0000000000000004,1,'',0);
INSERT INTO `gltrans` VALUES (21,10,5,0,'2013-02-11',15,'1460','ANGRY - SELLTAPE x 100 @ 0.01',-1,1,'',0);
INSERT INTO `gltrans` VALUES (22,10,5,0,'2013-02-11',15,'4100','ANGRY - SELLTAPE x 100 @ 0.5',-50,1,'',0);
INSERT INTO `gltrans` VALUES (23,10,5,0,'2013-02-11',15,'1100','ANGRY',56.18,1,'',0);
INSERT INTO `gltrans` VALUES (24,10,5,0,'2013-02-11',15,'2300','ANGRY',-2.5,1,'',0);
INSERT INTO `gltrans` VALUES (25,10,5,0,'2013-02-11',15,'2300','ANGRY',-3.68,1,'',0);
INSERT INTO `gltrans` VALUES (26,28,13,0,'2013-02-09',15,'1440','31 CUTTING x 2 @ 0.00',0,1,'',0);
INSERT INTO `gltrans` VALUES (27,28,13,0,'2013-02-09',15,'5500','31 CUTTING x 2 @ 0.00',0,1,'',0);
INSERT INTO `gltrans` VALUES (28,28,14,0,'2013-02-09',15,'1440','31 - TAPE2 Component: TAPE1 - 90 x 0.25 @ 10.00',225,1,'',0);
INSERT INTO `gltrans` VALUES (29,28,14,0,'2013-02-09',15,'1460','31 - TAPE2 -> TAPE1 - 90 x 0.25 @ 10.00',-225,1,'',0);
INSERT INTO `gltrans` VALUES (30,26,8,0,'2013-02-09',15,'1460','31 TAPE2 - Tape 2 x 90 @ 2.50',225,1,'',0);
INSERT INTO `gltrans` VALUES (31,26,8,0,'2013-02-09',15,'1440','31 TAPE2 - Tape 2 x 90 @ 2.50',-225,1,'',0);
INSERT INTO `gltrans` VALUES (32,35,20,0,'2013-04-25',17,'5700','BREAD cost was 0.4118 changed to 0.5625 x Quantity on hand of -6',0.9042,1,'',0);
INSERT INTO `gltrans` VALUES (33,35,20,0,'2013-04-25',17,'1460','BREAD cost was 0.4118 changed to 0.5625 x Quantity on hand of -6',-0.9042,1,'',0);
INSERT INTO `gltrans` VALUES (34,35,21,0,'2013-04-25',17,'5700','BREAD cost was 0.5625 changed to 0.5625 x Quantity on hand of -6',0,1,'',0);
INSERT INTO `gltrans` VALUES (35,35,21,0,'2013-04-25',17,'1460','BREAD cost was 0.5625 changed to 0.5625 x Quantity on hand of -6',0,1,'',0);
INSERT INTO `gltrans` VALUES (36,35,22,0,'2013-04-25',17,'5700','BREAD cost was 0.5625 changed to 0.5625 x Quantity on hand of -6',0,1,'',0);
INSERT INTO `gltrans` VALUES (37,35,22,0,'2013-04-25',17,'1460','BREAD cost was 0.5625 changed to 0.5625 x Quantity on hand of -6',0,1,'',0);
INSERT INTO `gltrans` VALUES (38,35,23,0,'2013-04-25',17,'5700','BREAD cost was 0.5625 changed to 0.5625 x Quantity on hand of -6',0,1,'',0);
INSERT INTO `gltrans` VALUES (39,35,23,0,'2013-04-25',17,'1460','BREAD cost was 0.5625 changed to 0.5625 x Quantity on hand of -6',0,1,'',0);
INSERT INTO `gltrans` VALUES (40,35,24,0,'2013-04-25',17,'5700','BREAD cost was 0.5625 changed to 0.5625 x Quantity on hand of -6',0,1,'',0);
INSERT INTO `gltrans` VALUES (41,35,24,0,'2013-04-25',17,'1460','BREAD cost was 0.5625 changed to 0.5625 x Quantity on hand of -6',0,1,'',0);
INSERT INTO `gltrans` VALUES (42,10,6,0,'2013-04-29',17,'5000','DUMBLE - BREAD x 3 @ 0.5625',1.69,1,'',0);
INSERT INTO `gltrans` VALUES (43,10,6,0,'2013-04-29',17,'1460','DUMBLE - BREAD x 3 @ 0.5625',-1.69,1,'',0);
INSERT INTO `gltrans` VALUES (44,10,6,0,'2013-04-29',17,'4100','DUMBLE - BREAD x 3 @ 1001.25',-4651.93,1,'',0);
INSERT INTO `gltrans` VALUES (45,10,6,0,'2013-04-29',17,'1100','DUMBLE',4651.93,1,'',0);
INSERT INTO `gltrans` VALUES (46,10,7,0,'2013-04-29',17,'5000','DUMBLE - BREAD x 1 @ 0.5625',0.56,1,'',0);
INSERT INTO `gltrans` VALUES (47,10,7,0,'2013-04-29',17,'1460','DUMBLE - BREAD x 1 @ 0.5625',-0.56,1,'',0);
INSERT INTO `gltrans` VALUES (48,10,7,0,'2013-04-29',17,'4100','DUMBLE - BREAD x 1 @ 5.9',-9.14,1,'',0);
INSERT INTO `gltrans` VALUES (49,10,7,0,'2013-04-29',17,'4900','DUMBLE - BREAD @ 2.5%',0.23,1,'',0);
INSERT INTO `gltrans` VALUES (50,10,7,0,'2013-04-29',17,'1100','DUMBLE',8.91,1,'',0);
INSERT INTO `gltrans` VALUES (51,11,1,0,'2013-05-02',18,'5000','ANGRY - TAPE2 x 1 @ 2.5000',-2.5,1,'',0);
INSERT INTO `gltrans` VALUES (52,11,1,0,'2013-05-02',18,'1460','ANGRY - TAPE2 x 1 @ 2.5000',2.5,1,'',0);
INSERT INTO `gltrans` VALUES (53,11,1,0,'2013-05-02',18,'4100','ANGRY - TAPE2 x 1 @ 15.00',15,1,'',0);
INSERT INTO `gltrans` VALUES (54,11,1,0,'2013-05-02',18,'1100','ANGRY',-16.5,1,'',0);
INSERT INTO `gltrans` VALUES (55,11,1,0,'2013-05-02',18,'2300','ANGRY',1.5,1,'',0);
INSERT INTO `gltrans` VALUES (56,11,2,0,'2013-05-02',18,'5000','ANGRY - SELLTAPE x 100 @ 10',-999.995,1,'',0);
INSERT INTO `gltrans` VALUES (57,11,2,0,'2013-05-02',18,'1460','ANGRY - SELLTAPE x 100 @ 10',1000,1,'',0);
INSERT INTO `gltrans` VALUES (58,11,2,0,'2013-05-02',18,'4100','ANGRY - SELLTAPE x 100 @ 0.50',50,1,'',0);
INSERT INTO `gltrans` VALUES (59,11,2,0,'2013-05-02',18,'1100','ANGRY',-56.18,1,'',0);
INSERT INTO `gltrans` VALUES (60,11,2,0,'2013-05-02',18,'2300','ANGRY',2.5,1,'',0);
INSERT INTO `gltrans` VALUES (61,11,2,0,'2013-05-02',18,'2300','ANGRY',3.675,1,'',0);
INSERT INTO `gltrans` VALUES (62,11,3,0,'2013-05-02',18,'5000','ANGRY - BREAD x 2 @ 0.5625',-1.1249999999999996,1,'',0);
INSERT INTO `gltrans` VALUES (63,11,3,0,'2013-05-02',18,'1460','ANGRY - BREAD x 2 @ 0.5625',1.13,1,'',0);
INSERT INTO `gltrans` VALUES (64,11,3,0,'2013-05-02',18,'4100','ANGRY - BREAD x 2 @ 5.00',10,1,'',0);
INSERT INTO `gltrans` VALUES (65,11,3,0,'2013-05-02',18,'1100','ANGRY',-11.24,1,'',0);
INSERT INTO `gltrans` VALUES (66,11,3,0,'2013-05-02',18,'2300','ANGRY',0.5,1,'',0);
INSERT INTO `gltrans` VALUES (67,11,3,0,'2013-05-02',18,'2300','ANGRY',0.735,1,'',0);
INSERT INTO `gltrans` VALUES (68,11,4,0,'2013-05-02',18,'5000','ANGRY - SELLTAPE x 100 @ 10',-999.995,1,'',0);
INSERT INTO `gltrans` VALUES (69,11,4,0,'2013-05-02',18,'1460','ANGRY - SELLTAPE x 100 @ 10',1000,1,'',0);
INSERT INTO `gltrans` VALUES (70,11,4,0,'2013-05-02',18,'4100','ANGRY - SELLTAPE x 100 @ 0.5000',50,1,'',0);
INSERT INTO `gltrans` VALUES (71,11,4,0,'2013-05-02',18,'1100','ANGRY',-56.18,1,'',0);
INSERT INTO `gltrans` VALUES (72,11,4,0,'2013-05-02',18,'2300','ANGRY',2.5,1,'',0);
INSERT INTO `gltrans` VALUES (73,11,4,0,'2013-05-02',18,'2300','ANGRY',3.675,1,'',0);
INSERT INTO `gltrans` VALUES (74,12,5,0,'2013-05-10',18,'1090','',-50.236109715664,1,'',0);
INSERT INTO `gltrans` VALUES (75,12,5,0,'2013-05-10',18,'1030','',50.236109715664,1,'',0);
INSERT INTO `gltrans` VALUES (76,12,12,0,'2013-06-08',19,'1030','7 payment for order34',8.659050435,1,'',0);
INSERT INTO `gltrans` VALUES (77,12,12,0,'2013-06-08',19,'1100','7 payment for order34',-8.659050435,1,'',0);
INSERT INTO `gltrans` VALUES (78,12,13,0,'2013-06-08',19,'1030','7 payment for order 35',14.161998375,1,'',0);
INSERT INTO `gltrans` VALUES (79,12,13,0,'2013-06-08',19,'1100','7 payment for order 35',-14.161998375,1,'',0);
INSERT INTO `gltrans` VALUES (80,12,14,0,'2013-06-08',19,'1040','7 payment for order 36',14.23081275,1,'',0);
INSERT INTO `gltrans` VALUES (81,12,14,0,'2013-06-08',19,'1100','7 payment for order 36',-14.23081275,1,'',0);
INSERT INTO `gltrans` VALUES (83,12,16,0,'2013-06-08',19,'1100','7 payment for order ',-14.161998375,1,'',0);
INSERT INTO `gltrans` VALUES (84,12,17,0,'2013-06-08',19,'1030','7 payment for order ',14.161998375,1,'',0);
INSERT INTO `gltrans` VALUES (85,12,17,0,'2013-06-08',19,'1100','7 payment for order ',-14.161998375,1,'',0);
INSERT INTO `gltrans` VALUES (86,17,28,0,'2013-06-13',19,'5700','HIT3043-5 x 6 @ 1235 ',-7410,1,'',0);
INSERT INTO `gltrans` VALUES (87,17,28,0,'2013-06-13',19,'1460','HIT3043-5 x 6 @ 1235 ',7410,1,'',0);
INSERT INTO `gltrans` VALUES (88,12,18,0,'2013-06-16',19,'1030','',85.900025142937,1,'',0);
INSERT INTO `gltrans` VALUES (89,12,18,0,'2013-06-16',19,'1100','',-85.900025142937,1,'',0);
INSERT INTO `gltrans` VALUES (90,12,18,0,'2013-06-16',19,'4200','',-0.0000060910887835774,1,'',0);
INSERT INTO `gltrans` VALUES (91,12,18,0,'2013-06-16',19,'1100','',0.0000060910887835774,1,'',0);
INSERT INTO `gltrans` VALUES (92,28,15,0,'2013-06-21',19,'1440','32 TAPE1 x 2 @ 10.00',20,1,'',0);
INSERT INTO `gltrans` VALUES (93,28,15,0,'2013-06-21',19,'1460','32 TAPE1 x 2 @ 10.00',-20,1,'',0);
INSERT INTO `gltrans` VALUES (94,28,16,0,'2013-06-21',19,'1440','32 SALT x 1.5 @ 2.50',3.75,1,'',0);
INSERT INTO `gltrans` VALUES (95,28,16,0,'2013-06-21',19,'1460','32 SALT x 1.5 @ 2.50',-3.75,1,'',0);
INSERT INTO `gltrans` VALUES (96,28,17,0,'2013-06-21',19,'1440','32 TAPE2 x 3.5 @ 2.50',8.75,1,'',0);
INSERT INTO `gltrans` VALUES (97,28,17,0,'2013-06-21',19,'1460','32 TAPE2 x 3.5 @ 2.50',-8.75,1,'',0);
INSERT INTO `gltrans` VALUES (98,12,19,0,'2013-06-23',19,'1030','7 payment for order 41 Transaction ID: B254331D91384',43.295252175,1,'',0);
INSERT INTO `gltrans` VALUES (99,12,19,0,'2013-06-23',19,'1100','7 payment for order 41 Transaction ID: B254331D91384',-43.295252175,1,'',0);
INSERT INTO `gltrans` VALUES (100,12,20,0,'2013-06-23',19,'1030','7 payment for order 42 Transaction ID: B254348421937',5317.9749,1,'',0);
INSERT INTO `gltrans` VALUES (101,12,20,0,'2013-06-23',19,'1100','7 payment for order 42 Transaction ID: B254348421937',-5317.9749,1,'',0);
INSERT INTO `gltrans` VALUES (102,12,21,0,'2013-06-23',19,'1040','7 payment for order  Transaction ID: ',8.70112551,1,'',0);
INSERT INTO `gltrans` VALUES (103,12,21,0,'2013-06-23',19,'1100','7 payment for order  Transaction ID: ',-8.70112551,1,'',0);
INSERT INTO `gltrans` VALUES (104,0,8,0,'2013-06-23',19,'1010','PAYTSURCHARGE Change stock category',0,1,'',0);
INSERT INTO `gltrans` VALUES (105,0,8,0,'2013-06-23',19,'1460','PAYTSURCHARGE Change stock category',0,1,'',0);
INSERT INTO `gltrans` VALUES (106,12,1,0,'2013-06-23',19,'1040','7 payment for order 1 Transaction ID: 7XT5929577922053V',36.5935185,1,'',0);
INSERT INTO `gltrans` VALUES (107,12,1,0,'2013-06-23',19,'1100','7 payment for order 1 Transaction ID: 7XT5929577922053V',-36.5935185,1,'',0);
INSERT INTO `gltrans` VALUES (108,12,1,0,'2013-06-23',19,'1030','16 payment for order  Transaction ID: ',109.24970175,1,'',0);
INSERT INTO `gltrans` VALUES (109,12,1,0,'2013-06-23',19,'1100','16 payment for order  Transaction ID: ',-109.24970175,1,'',0);
INSERT INTO `gltrans` VALUES (110,12,2,0,'2013-06-24',19,'1030','16 payment for order  Transaction ID: ',14.161998375,1,'',0);
INSERT INTO `gltrans` VALUES (111,12,2,0,'2013-06-24',19,'1100','16 payment for order  Transaction ID: ',-14.161998375,1,'',0);
INSERT INTO `gltrans` VALUES (112,12,3,0,'2013-06-24',19,'1030','16 payment for order 4 Transaction ID: V78R4D18BB1E',14.161998375,1,'',0);
INSERT INTO `gltrans` VALUES (113,12,3,0,'2013-06-24',19,'1100','16 payment for order 4 Transaction ID: V78R4D18BB1E',-14.161998375,1,'',0);
INSERT INTO `gltrans` VALUES (114,12,4,0,'2013-06-24',19,'1030','16 payment for order 5 Transaction ID: V18R4E55E804',8.659050435,1,'',0);
INSERT INTO `gltrans` VALUES (115,12,4,0,'2013-06-24',19,'1100','16 payment for order 5 Transaction ID: V18R4E55E804',-8.659050435,1,'',0);
INSERT INTO `gltrans` VALUES (116,10,1,0,'2013-06-26',19,'5000','16 - DVD-DHWV x 9 @ 2.3200',20.879999999999995,1,'',0);
INSERT INTO `gltrans` VALUES (117,10,1,0,'2013-06-26',19,'1460','16 - DVD-DHWV x 9 @ 2.3200',-20.88,1,'',0);
INSERT INTO `gltrans` VALUES (118,10,1,0,'2013-06-26',19,'4100','16 - DVD-DHWV x 9 @ 10.5',-94.5,1,'',0);
INSERT INTO `gltrans` VALUES (119,10,1,0,'2013-06-26',19,'7230','16 - PAYTSURCHARGE x 1 @ 2.7405',-2.74,1,'',0);
INSERT INTO `gltrans` VALUES (120,10,1,0,'2013-06-26',19,'1100','16',109.25,1,'',0);
INSERT INTO `gltrans` VALUES (121,10,1,0,'2013-06-26',19,'2300','16',-4.86,1,'',0);
INSERT INTO `gltrans` VALUES (122,10,1,0,'2013-06-26',19,'2300','16',-7.15,1,'',0);
INSERT INTO `gltrans` VALUES (123,10,2,0,'2013-09-07',22,'5000','12 - BREAD x 2 @ 0.5625',1.13,1,'',0);
INSERT INTO `gltrans` VALUES (124,10,2,0,'2013-09-07',22,'1460','12 - BREAD x 2 @ 0.5625',-1.13,1,'',0);
INSERT INTO `gltrans` VALUES (125,10,2,0,'2013-09-07',22,'4100','12 - BREAD x 2 @ 2.5',-5,1,'',0);
INSERT INTO `gltrans` VALUES (126,10,2,0,'2013-09-07',22,'1100','12',5.62,1,'',0);
INSERT INTO `gltrans` VALUES (127,10,2,0,'2013-09-07',22,'2300','12',-0.25,1,'',0);
INSERT INTO `gltrans` VALUES (128,10,2,0,'2013-09-07',22,'2300','12',-0.37,1,'',0);
INSERT INTO `gltrans` VALUES (129,25,53,0,'2013-10-05',23,'1','PO: 29 OTHER -  - Some item x 1 @ 45.94',45.938993017273,1,'',0);
INSERT INTO `gltrans` VALUES (130,25,53,0,'2013-10-05',23,'2150','PO1380921154: 29 OTHER -  - Some item x 1 @ 45.94',-45.938993017273,1,'',0);
INSERT INTO `gltrans` VALUES (131,20,38,0,'2013-10-05',23,'2150','OTHER - GRN 4 -  x 1 @  std cost of 45.938993017273',45.938993017273106,1,'',0);
INSERT INTO `gltrans` VALUES (132,20,38,0,'2013-10-05',23,'1','OTHER - GRN 4 - Some item x 1 x  price var 4.59',4.5938993017274,1,'',0);
INSERT INTO `gltrans` VALUES (133,20,38,0,'2013-10-05',23,'2310','OTHER - Inv 145 Ontario PST 5.00% AUD2.75 @ exch rate 1.08840000',2.52664461595,1,'',0);
INSERT INTO `gltrans` VALUES (134,20,38,0,'2013-10-05',23,'2310','OTHER - Inv 145 Canadian GST 7.00% AUD4.0425 @ exch rate 1.08840000',3.7141675854465,1,'',0);
INSERT INTO `gltrans` VALUES (135,20,38,0,'2013-10-05',23,'2100','OTHER - Inv 145 AUD61.79 @ a rate of 1.08840000',-56.773704520397,1,'',0);
INSERT INTO `gltrans` VALUES (136,22,9,0,'2013-11-20',24,'2100','OTHER-',91.877986034546,1,'',0);
INSERT INTO `gltrans` VALUES (137,22,9,0,'2013-11-20',24,'1030','OTHER-',-91.877986034546,1,'',0);
INSERT INTO `gltrans` VALUES (138,22,10,0,'2013-11-20',24,'2100','OTHER-',100,1,'',0);
INSERT INTO `gltrans` VALUES (139,22,10,0,'2013-11-20',24,'1030','OTHER-',-100,1,'',0);
INSERT INTO `gltrans` VALUES (140,22,11,0,'2013-11-20',24,'2100','OTHER-',91.877986034546,1,'',0);
INSERT INTO `gltrans` VALUES (141,22,11,0,'2013-11-20',24,'1030','OTHER-',-91.877986034546,1,'',0);
INSERT INTO `gltrans` VALUES (142,20,39,0,'2013-11-30',24,'1460','BINGO - Average Cost Adj - DVD_ACTION x 1 x 52.35',52.35,1,'',0);
INSERT INTO `gltrans` VALUES (143,20,39,0,'2013-11-30',24,'2310','BINGO - Inv aA112 Australian GST 10.00% USD5.235 @ exch rate 1.00000000',5.235,1,'',0);
INSERT INTO `gltrans` VALUES (144,20,39,0,'2013-11-30',24,'2100','BINGO - Inv aA112 USD57.59 @ a rate of 1.00000000',-57.585,1,'',0);
INSERT INTO `gltrans` VALUES (145,25,54,0,'2014-01-13',26,'1460','PO: 30 BINGO - DVD-CASE - webERP Demo DVD Case x 100 @ 0.30',30,1,'',0);
INSERT INTO `gltrans` VALUES (146,25,54,0,'2014-01-13',26,'2150','PO1389596068: 30 BINGO - DVD-CASE - webERP Demo DVD Case x 100 @ 0.30',-30,1,'',0);
INSERT INTO `gltrans` VALUES (147,20,40,0,'2014-01-13',26,'1460','BINGO - Average Cost Adj - DVD-CASE x 100 x 0.5',50,1,'',0);
INSERT INTO `gltrans` VALUES (148,20,40,0,'2014-01-13',26,'2310','BINGO - Inv 2100 Australian GST 10.00% USD5 @ exch rate 1.00000000',5,1,'',0);
INSERT INTO `gltrans` VALUES (149,20,40,0,'2014-01-13',26,'2100','BINGO - Inv 2100 USD55.00 @ a rate of 1.00000000',-55,1,'',0);
INSERT INTO `gltrans` VALUES (150,25,55,0,'2014-01-13',26,'1460','PO: 31 BINGO - DVD-CASE - webERP Demo DVD Case x 10 @ 0.80',8,1,'',0);
INSERT INTO `gltrans` VALUES (151,25,55,0,'2014-01-13',26,'2150','PO1389596314: 31 BINGO - DVD-CASE - webERP Demo DVD Case x 10 @ 0.80',-8,1,'',0);
INSERT INTO `gltrans` VALUES (152,20,41,0,'2014-01-13',26,'1460','BINGO - Average Cost Adj - DVD-CASE x 110 x 0.1',1,1,'',0);
INSERT INTO `gltrans` VALUES (153,20,41,0,'2014-01-13',26,'2310','BINGO - Inv 1223 Australian GST 10.00% USD0.1 @ exch rate 1.00000000',0.1,1,'',0);
INSERT INTO `gltrans` VALUES (154,20,41,0,'2014-01-13',26,'2100','BINGO - Inv 1223 USD1.10 @ a rate of 1.00000000',-1.1,1,'',0);
INSERT INTO `gltrans` VALUES (155,25,56,0,'2014-01-13',26,'1460','PO: 32 BINGO - DVD-CASE - webERP Demo DVD Case x 100 @ 0.81',80.91,1,'',0);
INSERT INTO `gltrans` VALUES (156,25,56,0,'2014-01-13',26,'2150','PO1389596457: 32 BINGO - DVD-CASE - webERP Demo DVD Case x 100 @ 0.81',-80.91,1,'',0);
INSERT INTO `gltrans` VALUES (157,20,42,0,'2014-01-13',26,'1460','BINGO - Average Cost Adj - DVD-CASE x 210 x 0.31',31,1,'',0);
INSERT INTO `gltrans` VALUES (158,20,42,0,'2014-01-13',26,'2310','BINGO - Inv 23001 Australian GST 10.00% USD3.1 @ exch rate 1.00000000',3.1,1,'',0);
INSERT INTO `gltrans` VALUES (159,20,42,0,'2014-01-13',26,'2100','BINGO - Inv 23001 USD34.10 @ a rate of 1.00000000',-34.1,1,'',0);
INSERT INTO `gltrans` VALUES (160,35,25,0,'2014-01-14',26,'5700','DVD-CASE cost was 0.9567 changed to 1 x Quantity on hand of 210',-9.093,1,'',0);
INSERT INTO `gltrans` VALUES (161,35,25,0,'2014-01-14',26,'1460','DVD-CASE cost was 0.9567 changed to 1 x Quantity on hand of 210',9.093,1,'',0);
INSERT INTO `gltrans` VALUES (162,35,26,0,'2014-01-14',26,'5700','DVD_ACTION cost was 68.5700 changed to 22.02 x Quantity on hand of 1',46.55,1,'',0);
INSERT INTO `gltrans` VALUES (163,35,26,0,'2014-01-14',26,'1460','DVD_ACTION cost was 68.5700 changed to 22.02 x Quantity on hand of 1',-46.55,1,'',0);
INSERT INTO `gltrans` VALUES (164,25,57,0,'2014-01-14',26,'1460','PO: 33 BINGO - DVD-CASE - webERP Demo DVD Case x 100 @ 1.00',100,1,'',0);
INSERT INTO `gltrans` VALUES (165,25,57,0,'2014-01-14',26,'2150','PO1389681160: 33 BINGO - DVD-CASE - webERP Demo DVD Case x 100 @ 1.00',-100,1,'',0);
INSERT INTO `gltrans` VALUES (166,20,43,0,'2014-01-14',26,'5000','BINGO - GRN 8 - DVD-CASE x 1 x  price var of 1.5',1.5,1,'',0);
INSERT INTO `gltrans` VALUES (167,20,43,0,'2014-01-14',26,'2310','BINGO - Inv tedt555 Australian GST 10.00% USD0.15 @ exch rate 1.00000000',0.15,1,'',0);
INSERT INTO `gltrans` VALUES (168,20,43,0,'2014-01-14',26,'2100','BINGO - Inv tedt555 USD1.65 @ a rate of 1.00000000',-1.65,1,'',0);
INSERT INTO `gltrans` VALUES (169,20,44,0,'2014-01-13',26,'5000','BINGO - GRN 8 - DVD-CASE x 1 x  price var of 1.5',1.5,1,'',0);
INSERT INTO `gltrans` VALUES (170,20,44,0,'2014-01-13',26,'2310','BINGO - Inv 2133 Australian GST 10.00% USD0.15 @ exch rate 1.00000000',0.15,1,'',0);
INSERT INTO `gltrans` VALUES (171,20,44,0,'2014-01-13',26,'2100','BINGO - Inv 2133 USD1.65 @ a rate of 1.00000000',-1.65,1,'',0);
INSERT INTO `gltrans` VALUES (172,20,45,0,'2014-01-13',26,'2150','BINGO - GRN 8 - DVD-CASE x 1 @  std cost of 1',1,1,'',0);
INSERT INTO `gltrans` VALUES (173,20,45,0,'2014-01-13',26,'5000','BINGO - GRN 8 - DVD-CASE x 1 x  price var of 1',1,1,'',0);
INSERT INTO `gltrans` VALUES (174,20,45,0,'2014-01-13',26,'2310','BINGO - Inv 211 Australian GST 10.00% USD0.2 @ exch rate 1.00000000',0.2,1,'',0);
INSERT INTO `gltrans` VALUES (175,20,45,0,'2014-01-13',26,'2100','BINGO - Inv 211 USD2.20 @ a rate of 1.00000000',-2.2,1,'',0);
INSERT INTO `gltrans` VALUES (176,1,5,0,'2014-02-03',27,'6600','Some other selling cost',10.250000003856004,1,'',0);
INSERT INTO `gltrans` VALUES (177,1,5,0,'2014-02-03',27,'7090','some benefit',95.250000035814,1,'',0);
INSERT INTO `gltrans` VALUES (178,1,5,0,'2014-02-03',27,'1030','Test narrative',-105.50000003967,1,'',0);
INSERT INTO `gltrans` VALUES (179,1,6,0,'2014-02-03',27,'5600','fregt',100,1,'',0);
INSERT INTO `gltrans` VALUES (180,1,6,0,'2014-02-03',27,'1350','advanv',25,1,'',0);
INSERT INTO `gltrans` VALUES (181,1,6,0,'2014-02-03',27,'1060','213 2221',-125,1,'',0);
INSERT INTO `gltrans` VALUES (182,1,7,0,'2014-07-05',32,'1010','',10,1,'',0);
INSERT INTO `gltrans` VALUES (183,1,7,0,'2014-07-05',32,'1060','',-10,1,'',0);
INSERT INTO `gltrans` VALUES (184,25,58,0,'2014-07-26',32,'5500','PO: 34 WHYNOT - LABOUR - Labour item - Freddie x 2 @ 75.00',150,1,'',0);
INSERT INTO `gltrans` VALUES (185,25,58,0,'2014-07-26',32,'2150','PO1406335331: 34 WHYNOT - LABOUR - Labour item - Freddie x 2 @ 75.00',-150,1,'',0);

--
-- Dumping data for table `grns`
--

INSERT INTO `grns` VALUES (50,1,1,'DVD_ACTION','2013-01-06','Action Series Bundle',1,1,'BINGO',16.22);
INSERT INTO `grns` VALUES (51,2,3,'TAPE1','2013-02-09','DFR-12  DFR Tape per Keystone spec',100,0,'CRUISE',10);
INSERT INTO `grns` VALUES (52,3,5,'TAPE1','2013-02-09','DFR-12 - DFR Tape per Keystone spec',10,0,'CRUISE',10);
INSERT INTO `grns` VALUES (53,4,8,'','2013-10-05','Some item',1,1,'OTHER',45.938993017273);
INSERT INTO `grns` VALUES (54,5,9,'DVD-CASE','2014-01-13','webERP Demo DVD Case',100,100,'BINGO',0.3);
INSERT INTO `grns` VALUES (55,6,10,'DVD-CASE','2014-01-13','webERP Demo DVD Case',10,10,'BINGO',0.8);
INSERT INTO `grns` VALUES (56,7,11,'DVD-CASE','2014-01-13','webERP Demo DVD Case',100,100,'BINGO',0.8091);
INSERT INTO `grns` VALUES (57,8,12,'DVD-CASE','2014-01-14','webERP Demo DVD Case',100,3,'BINGO',1);
INSERT INTO `grns` VALUES (58,9,13,'LABOUR','2014-07-26','Labour item - Freddie',2,0,'WHYNOT',75);

--
-- Dumping data for table `holdreasons`
--

INSERT INTO `holdreasons` VALUES (1,'Good History',0);
INSERT INTO `holdreasons` VALUES (20,'Watch',2);
INSERT INTO `holdreasons` VALUES (51,'In liquidation',1);

--
-- Dumping data for table `internalstockcatrole`
--

INSERT INTO `internalstockcatrole` VALUES ('AIRCON',8);
INSERT INTO `internalstockcatrole` VALUES ('BAKE',8);
INSERT INTO `internalstockcatrole` VALUES ('FOOD',8);

--
-- Dumping data for table `labelfields`
--

INSERT INTO `labelfields` VALUES (1,1,'itemcode',10,10,10,1);
INSERT INTO `labelfields` VALUES (2,1,'itemcode',20,10,10,0);
INSERT INTO `labelfields` VALUES (3,1,'itemdescription',35,10,8,0);

--
-- Dumping data for table `labels`
--

INSERT INTO `labels` VALUES (1,'Test',210,297,0,0,5,10,0,0);

--
-- Dumping data for table `lastcostrollup`
--


--
-- Dumping data for table `locations`
--

INSERT INTO `locations` VALUES ('AN','Anaheim',' ','','','','','United States','','','','Brett',1,'',0,'',0,1);
INSERT INTO `locations` VALUES ('MEL','Melbourne','1234 Collins Street','Melbourne','Victoria 2345','','2345','Australia','+(61) (3) 5678901','+61 3 56789013','jacko@webdemo.com','Jack Roberts',1,'',0,'',1,1);
INSERT INTO `locations` VALUES ('TOR','Toronto','Level 100 ','CN Tower','Toronto','','','','','','','Clive Contrary',1,'',1,'',1,1);

--
-- Dumping data for table `locationusers`
--

INSERT INTO `locationusers` VALUES ('AN','admin',1,1);
INSERT INTO `locationusers` VALUES ('MEL','admin',1,1);
INSERT INTO `locationusers` VALUES ('MEL','WEB0000017',1,1);
INSERT INTO `locationusers` VALUES ('TOR','admin',1,1);
INSERT INTO `locationusers` VALUES ('TOR','WEB0000017',1,1);

--
-- Dumping data for table `locstock`
--

INSERT INTO `locstock` VALUES ('AN','BIGEARS12',0,0,'');
INSERT INTO `locstock` VALUES ('AN','BirthdayCakeConstruc',0,0,'');
INSERT INTO `locstock` VALUES ('AN','BREAD',2,0,'');
INSERT INTO `locstock` VALUES ('AN','CUTTING',0,0,'');
INSERT INTO `locstock` VALUES ('AN','DR_TUMMY',0,0,'');
INSERT INTO `locstock` VALUES ('AN','DVD-CASE',0,0,'');
INSERT INTO `locstock` VALUES ('AN','DVD-DHWV',0,0,'');
INSERT INTO `locstock` VALUES ('AN','DVD-LTWP',0,0,'');
INSERT INTO `locstock` VALUES ('AN','DVD-TOPGUN',0,0,'');
INSERT INTO `locstock` VALUES ('AN','DVD-UNSG',0,0,'');
INSERT INTO `locstock` VALUES ('AN','DVD-UNSG2',0,0,'');
INSERT INTO `locstock` VALUES ('AN','DVD_ACTION',0,0,'');
INSERT INTO `locstock` VALUES ('AN','FLOUR',0,0,'');
INSERT INTO `locstock` VALUES ('AN','FREIGHT',0,0,'');
INSERT INTO `locstock` VALUES ('AN','FROAYLANDO',0,0,'');
INSERT INTO `locstock` VALUES ('AN','FUJI990101',0,0,'');
INSERT INTO `locstock` VALUES ('AN','FUJI990102',0,0,'');
INSERT INTO `locstock` VALUES ('AN','FUJI9901ASS',0,0,'');
INSERT INTO `locstock` VALUES ('AN','HIT3042-4',0,0,'');
INSERT INTO `locstock` VALUES ('AN','HIT3043-5',0,0,'');
INSERT INTO `locstock` VALUES ('AN','LABOUR',0,0,'');
INSERT INTO `locstock` VALUES ('AN','PAYTSURCHARGE',0,0,'');
INSERT INTO `locstock` VALUES ('AN','SALT',-1.5,0,'');
INSERT INTO `locstock` VALUES ('AN','SELLTAPE',0,0,'');
INSERT INTO `locstock` VALUES ('AN','SLICE',0,0,'');
INSERT INTO `locstock` VALUES ('AN','STROD34',0,0,'');
INSERT INTO `locstock` VALUES ('AN','TAPE1',-24.4,0,'');
INSERT INTO `locstock` VALUES ('AN','TAPE2',86.5,0,'');
INSERT INTO `locstock` VALUES ('AN','Test123',0,0,'');
INSERT INTO `locstock` VALUES ('AN','TESTSERIALITEM',0,0,'');
INSERT INTO `locstock` VALUES ('AN','YEAST',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','BIGEARS12',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','BirthdayCakeConstruc',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','BREAD',0,0,'Y63');
INSERT INTO `locstock` VALUES ('MEL','CUTTING',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','DR_TUMMY',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','DVD-CASE',310,0,'4T2');
INSERT INTO `locstock` VALUES ('MEL','DVD-DHWV',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','DVD-LTWP',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','DVD-TOPGUN',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','DVD-UNSG',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','DVD-UNSG2',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','DVD_ACTION',1,0,'');
INSERT INTO `locstock` VALUES ('MEL','FLOUR',0,0,'4D2');
INSERT INTO `locstock` VALUES ('MEL','FREIGHT',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','FROAYLANDO',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','FUJI990101',0,0,'3D2');
INSERT INTO `locstock` VALUES ('MEL','FUJI990102',0,0,'2D2');
INSERT INTO `locstock` VALUES ('MEL','FUJI9901ASS',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','HIT3042-4',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','HIT3043-5',6,0,'');
INSERT INTO `locstock` VALUES ('MEL','LABOUR',2,0,'');
INSERT INTO `locstock` VALUES ('MEL','PAYTSURCHARGE',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','SALT',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','SELLTAPE',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','SLICE',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','STROD34',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','TAPE1',110,0,'');
INSERT INTO `locstock` VALUES ('MEL','TAPE2',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','Test123',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','TESTSERIALITEM',0,0,'');
INSERT INTO `locstock` VALUES ('MEL','YEAST',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','BIGEARS12',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','BirthdayCakeConstruc',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','BREAD',-12,0,'Z41');
INSERT INTO `locstock` VALUES ('TOR','CUTTING',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','DR_TUMMY',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','DVD-CASE',0,0,'3G6');
INSERT INTO `locstock` VALUES ('TOR','DVD-DHWV',-9,0,'');
INSERT INTO `locstock` VALUES ('TOR','DVD-LTWP',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','DVD-TOPGUN',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','DVD-UNSG',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','DVD-UNSG2',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','DVD_ACTION',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','FLOUR',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','FREIGHT',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','FROAYLANDO',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','FUJI990101',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','FUJI990102',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','FUJI9901ASS',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','HIT3042-4',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','HIT3043-5',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','LABOUR',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','PAYTSURCHARGE',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','SALT',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','SELLTAPE',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','SLICE',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','STROD34',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','TAPE1',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','TAPE2',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','Test123',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','TESTSERIALITEM',0,0,'');
INSERT INTO `locstock` VALUES ('TOR','YEAST',0,0,'');

--
-- Dumping data for table `loctransfers`
--


--
-- Dumping data for table `mailgroupdetails`
--


--
-- Dumping data for table `mailgroups`
--

INSERT INTO `mailgroups` VALUES (1,'ChkListingRecipients');
INSERT INTO `mailgroups` VALUES (4,'InventoryValuationRecipients');
INSERT INTO `mailgroups` VALUES (3,'OffersReceivedResultRecipients');
INSERT INTO `mailgroups` VALUES (2,'SalesAnalysisReportRecipients');

--
-- Dumping data for table `manufacturers`
--

INSERT INTO `manufacturers` VALUES (1,'Sony Entertainment','http://www.sony.com','companies/weberpdemo/part_pics/BRAND-3.jpg');
INSERT INTO `manufacturers` VALUES (2,'20th Century Fox','http://www.foxmovies.com/','companies/weberpdemo/part_pics/BRAND-3.jpg');
INSERT INTO `manufacturers` VALUES (3,'Fujitsu','http://www.fujitsu.com','companies/weberpdemo/part_pics/BRAND-3.jpg');
INSERT INTO `manufacturers` VALUES (4,'Hitachi','http://www.hitachi.com','');

--
-- Dumping data for table `mrpcalendar`
--


--
-- Dumping data for table `mrpdemands`
--


--
-- Dumping data for table `mrpdemandtypes`
--

INSERT INTO `mrpdemandtypes` VALUES ('FOR','Forecast');

--
-- Dumping data for table `mrpplannedorders`
--

INSERT INTO `mrpplannedorders` VALUES (1,'CUTTING','2013-02-09',5,'WO',31,'2013-02-09',0);
INSERT INTO `mrpplannedorders` VALUES (2,'CUTTING','2013-02-27',25,'WO',30,'2013-02-27',0);
INSERT INTO `mrpplannedorders` VALUES (3,'CUTTING','2014-08-02',2,'SO',8,'2014-08-02',0);
INSERT INTO `mrpplannedorders` VALUES (4,'DVD-DHWV','2013-06-20',1,'SO',2,'2013-06-20',0);
INSERT INTO `mrpplannedorders` VALUES (5,'DVD-DHWV','2014-09-05',9,'REORD',1,'2014-09-05',0);
INSERT INTO `mrpplannedorders` VALUES (6,'DVD-LTWP','2013-06-24',1,'SO',5,'2013-06-24',0);
INSERT INTO `mrpplannedorders` VALUES (7,'SALT','2012-12-16',0.05,'WO',28,'2012-12-16',0);
INSERT INTO `mrpplannedorders` VALUES (8,'SALT','2013-02-06',0.25,'WO',29,'2013-02-06',0);
INSERT INTO `mrpplannedorders` VALUES (9,'SALT','2013-07-21',2,'WO',32,'2013-07-21',0);
INSERT INTO `mrpplannedorders` VALUES (10,'SALT','2014-09-08',1.5,'REORD',1,'2014-09-08',0);
INSERT INTO `mrpplannedorders` VALUES (11,'YEAST','2012-12-16',0.2,'WO',28,'2012-12-16',0);
INSERT INTO `mrpplannedorders` VALUES (12,'YEAST','2013-02-06',1,'WO',29,'2013-02-06',0);
INSERT INTO `mrpplannedorders` VALUES (13,'DVD-TOPGUN','2013-06-24',1,'SO',3,'2013-06-24',0);
INSERT INTO `mrpplannedorders` VALUES (14,'DVD-TOPGUN','2013-06-24',1,'SO',4,'2013-06-24',0);
INSERT INTO `mrpplannedorders` VALUES (15,'PAYTSURCHARGE','2013-06-24',1,'SO',3,'2013-06-24',0);
INSERT INTO `mrpplannedorders` VALUES (16,'PAYTSURCHARGE','2013-06-24',1,'SO',4,'2013-06-24',0);
INSERT INTO `mrpplannedorders` VALUES (17,'PAYTSURCHARGE','2013-06-24',1,'SO',5,'2013-06-24',0);

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` VALUES (1,0,'BINGO','BREAD',5,'each',0.95,'2011-10-24','USD');

--
-- Dumping data for table `orderdeliverydifferenceslog`
--


--
-- Dumping data for table `paymentmethods`
--

INSERT INTO `paymentmethods` VALUES (1,'Cheque',1,1,1,0);
INSERT INTO `paymentmethods` VALUES (2,'Cash',1,1,0,0);
INSERT INTO `paymentmethods` VALUES (3,'Direct Credit',1,1,0,0);

--
-- Dumping data for table `paymentterms`
--

INSERT INTO `paymentterms` VALUES ('20','Due 20th Of the Following Month',0,22);
INSERT INTO `paymentterms` VALUES ('30','Due By End Of The Following Month',0,30);
INSERT INTO `paymentterms` VALUES ('7','Payment due within 7 days',7,0);
INSERT INTO `paymentterms` VALUES ('CA','Cash Only',1,0);

--
-- Dumping data for table `pcashdetails`
--


--
-- Dumping data for table `pcexpenses`
--


--
-- Dumping data for table `pctabexpenses`
--


--
-- Dumping data for table `pctabs`
--

INSERT INTO `pctabs` VALUES ('test','admin','Default','AUD',85,'admin','admin','1030','7610');

--
-- Dumping data for table `pctypetabs`
--

INSERT INTO `pctypetabs` VALUES ('Default','Default');

--
-- Dumping data for table `periods`
--

INSERT INTO `periods` VALUES (14,'2013-01-31');
INSERT INTO `periods` VALUES (15,'2013-02-28');
INSERT INTO `periods` VALUES (16,'2013-03-31');
INSERT INTO `periods` VALUES (17,'2013-04-30');
INSERT INTO `periods` VALUES (18,'2013-05-31');
INSERT INTO `periods` VALUES (19,'2013-06-30');
INSERT INTO `periods` VALUES (20,'2013-07-31');
INSERT INTO `periods` VALUES (21,'2013-08-31');
INSERT INTO `periods` VALUES (22,'2013-09-30');
INSERT INTO `periods` VALUES (23,'2013-10-31');
INSERT INTO `periods` VALUES (24,'2013-11-30');
INSERT INTO `periods` VALUES (25,'2013-12-31');
INSERT INTO `periods` VALUES (26,'2014-01-31');
INSERT INTO `periods` VALUES (27,'2014-02-28');
INSERT INTO `periods` VALUES (28,'2014-03-31');
INSERT INTO `periods` VALUES (29,'2014-04-30');
INSERT INTO `periods` VALUES (30,'2014-05-31');
INSERT INTO `periods` VALUES (31,'2014-06-30');
INSERT INTO `periods` VALUES (32,'2014-07-31');
INSERT INTO `periods` VALUES (33,'2014-08-31');
INSERT INTO `periods` VALUES (34,'2014-09-30');
INSERT INTO `periods` VALUES (35,'2014-10-31');
INSERT INTO `periods` VALUES (36,'2014-11-30');

--
-- Dumping data for table `pickinglistdetails`
--


--
-- Dumping data for table `pickinglists`
--


--
-- Dumping data for table `pricematrix`
--


--
-- Dumping data for table `prices`
--

INSERT INTO `prices` VALUES ('BIGEARS12','DE','AUD','',4428.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('BirthdayCakeConstruc','DE','AUD','',1476.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('BREAD','DE','AUD','',123.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('CUTTING','DE','USD','',50.0000,'','2013-06-29','0000-00-00');
INSERT INTO `prices` VALUES ('DR_TUMMY','DE','AUD','',246.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('DVD-CASE','DE','AUD','',124.5000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('DVD-CASE','DE','GBP','DUMBLE',52.6500,'DUMBLE','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('DVD-DHWV','DE','AUD','',123.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('DVD-DHWV','DE','USD','',10.5000,'','2013-01-27','0000-00-00');
INSERT INTO `prices` VALUES ('DVD-LTWP','DE','AUD','',123.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('DVD-LTWP','DE','USD','',7.4900,'','2013-04-27','0000-00-00');
INSERT INTO `prices` VALUES ('DVD-TOPGUN','DE','AUD','',123.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('DVD-TOPGUN','DE','USD','',12.2500,'','2013-04-27','0000-00-00');
INSERT INTO `prices` VALUES ('DVD-UNSG','DE','AUD','',123.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('DVD-UNSG2','DE','AUD','',123.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('DVD_ACTION','DE','AUD','',123.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('FLOUR','DE','AUD','',123.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('FUJI990101','DE','AUD','',1353.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('FUJI990102','DE','AUD','',861.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('HIT3042-4','DE','AUD','',1107.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('HIT3043-5','DE','AUD','',1599.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('HIT3043-5','DE','USD','',2300.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('LABOUR','DE','USD','',150.0000,'','2014-07-26','0000-00-00');
INSERT INTO `prices` VALUES ('SALT','DE','AUD','',123.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('SLICE','DE','AUD','',123.0000,'','1999-01-01','0000-00-00');
INSERT INTO `prices` VALUES ('TAPE1','DE','USD','',17.5000,'','2013-02-08','0000-00-00');
INSERT INTO `prices` VALUES ('YEAST','DE','AUD','',123.0000,'','1999-01-01','0000-00-00');

--
-- Dumping data for table `prodspecs`
--


--
-- Dumping data for table `purchdata`
--

INSERT INTO `purchdata` VALUES ('BINGO','DVD-CASE',1000.5900,'10,000',10000,'Mother load of DVD cases',1,1,'2011-03-26','',1);
INSERT INTO `purchdata` VALUES ('BINGO','DVD-DHWV',8.5000,'5 pack',5,'5 Pack Die Hard With Vengence',3,1,'2012-03-01','DHWV-5',1);
INSERT INTO `purchdata` VALUES ('BINGO','HIT3043-5',1235.0000,'',1,'',5,1,'2009-09-18','',1);
INSERT INTO `purchdata` VALUES ('CRUISE','DVD-CASE',1151.2500,'2000 pack',2000,'2000 x DVD covers',50,0,'2011-06-26','coverx2000',1);
INSERT INTO `purchdata` VALUES ('CRUISE','DVD-UNSG2',200.0000,'10 Pack',10,'',5,1,'2009-09-18','',1);
INSERT INTO `purchdata` VALUES ('CRUISE','TAPE1',2.5000,'feet',1,'DFR Tape per Keystone spec',20,1,'2013-02-09','DFR-12',10);
INSERT INTO `purchdata` VALUES ('GOTSTUFF','BREAD',1.5800,'',1,'Loaf of bread',1,1,'2011-10-08','',5);

--
-- Dumping data for table `purchorderauth`
--

INSERT INTO `purchorderauth` VALUES ('admin','AUD',0,50000,0);
INSERT INTO `purchorderauth` VALUES ('admin','EUR',0,999999999,0);
INSERT INTO `purchorderauth` VALUES ('admin','GBP',0,9999999,0);
INSERT INTO `purchorderauth` VALUES ('admin','USD',0,9999999,0);

--
-- Dumping data for table `purchorderdetails`
--

INSERT INTO `purchorderdetails` VALUES (1,23,'DVD_ACTION','2013-01-06','Action Series Bundle','1460',1,50,52.35,16.22,1,1,0,'0',1,'each','',0,1);
INSERT INTO `purchorderdetails` VALUES (2,24,'DVD-CASE','2013-02-10','  Mother load of DVD cases','1460',0,1000.59,0,0,0,0,0,'',1,'10,000','',0,10000);
INSERT INTO `purchorderdetails` VALUES (3,25,'TAPE1','2013-03-01','DFR-12  DFR Tape per Keystone spec','1460',0,2.5,0,10,100,100,0,'',1,'feet','DFR-12',0,1);
INSERT INTO `purchorderdetails` VALUES (4,26,'BREAD','2013-02-10','  Loaf of bread','1460',0,1.58,0,0,10,0,0,'',1,'','',0,1);
INSERT INTO `purchorderdetails` VALUES (5,27,'TAPE1','2013-03-01','DFR-12 - DFR Tape per Keystone spec','1460',0,2.5,0,10,10,10,0,'0',1,'feet','DFR-12',0,1);
INSERT INTO `purchorderdetails` VALUES (6,28,'DVD-CASE','2013-06-20','coverx2000 - 2000 x DVD covers','1460',0,0.575625,0,0,4000,0,0,'0',0,'2000 pack','coverx2000',0,2000);
INSERT INTO `purchorderdetails` VALUES (7,24,'','2013-02-09','Some other thing','7630',0,0.65,0,0,25,0,0,'',0,'each','',0,1);
INSERT INTO `purchorderdetails` VALUES (8,29,'','2013-10-05','Some item','1',1,50,55,45.938993017273,1,1,0,'',0,'each','',0,1);
INSERT INTO `purchorderdetails` VALUES (9,30,'DVD-CASE','2014-01-13','webERP Demo DVD Case','1460',100,0.5,0.5,0.3,100,100,0,'',0,'10,000','',0,1);
INSERT INTO `purchorderdetails` VALUES (10,31,'DVD-CASE','2014-01-13','webERP Demo DVD Case','1460',10,0.1,0.1,0.8,10,10,0,'',0,'10,000','',0,1);
INSERT INTO `purchorderdetails` VALUES (11,32,'DVD-CASE','2014-01-13','webERP Demo DVD Case','1460',100,0.5,0.31,0.8091,100,100,0,'',0,'10,000','',0,1);
INSERT INTO `purchorderdetails` VALUES (12,33,'DVD-CASE','2014-01-14','webERP Demo DVD Case','1460',3,1.5,2,1,100,100,0,'',0,'10,000','',0,1);
INSERT INTO `purchorderdetails` VALUES (13,34,'LABOUR','2014-07-26','Labour item - Freddie','5500',0,5.9,0,75,2,2,0,'0',0,'each','',0,1);

--
-- Dumping data for table `purchorders`
--

INSERT INTO `purchorders` VALUES (23,'BINGO','','2013-01-06 00:00:00',1,NULL,1,'admin','','MEL','1234 Collins Street','Melbourne','Victoria 2345','','','+61 3 56789012','+61 3 56789012','Box 3499','Gardenier','San Fransisco','California 54424','','US','','','Jack Roberts',2.00,'2013-02-09','','1','2013-01-06','Completed','09/02/2013 - Order Completed on entry of GRN<br />09/02/2013 - Order modified by <a href=\"mailto:info@weberp.org\">Demonstration user</a><br />','30','');
INSERT INTO `purchorders` VALUES (24,'BINGO','','2013-02-09 00:00:00',1,NULL,1,'Demonstration user','','MEL','Melbourne','1234 Collins Street','Melbourne','Victoria 2345','','+61 3 56789012','+61 3 56789012','Box 3499','Gardenier','San Fransisco','California 54424','','US','','','',2.00,'2013-06-11','','1','2013-02-09','Authorised','11/06/2013 - Order modified by &lt;a href=&quot;mailto:phil@logicworks.co.nz&quot;&gt;Demonstration user&lt;/a&gt;&lt;br /&gt;','30','');
INSERT INTO `purchorders` VALUES (25,'CRUISE','','2013-02-09 00:00:00',0.6324,NULL,1,'Demonstration user','','MEL','Melbourne','1234 Collins Street','Melbourne','Victoria 2345','','+61 3 56789012','+61 3 56789012','Box 2001','Ft Lauderdale, Florida','','','','','Barry Toad','','',2.00,'2013-02-09','','1','2013-02-09','Completed','09/02/2013 - Order Completed on entry of GRN<br />09/02/2013 - Order modified by <a href=\"mailto:info@weberp.org\">Demonstration user</a><br />','30','');
INSERT INTO `purchorders` VALUES (26,'GOTSTUFF',NULL,'2013-02-09 00:00:00',1,NULL,0,'Demonstration user',NULL,'MEL','Melbourne','1234 Collins Street','Melbourne','Victoria 2345','',' Australia','+61 3 56789012','Test line 1','Test line 2','Test line 3','Test line 4 - editing','','','','','',1.00,'2013-02-09','','1','2013-02-09','Cancelled','28/05/2013 - Cancelled by <a href=\"mailto:phil@logicworks.co.nz\">Demonstration user</a><br />28/05/2013 - Authorised by <a href=\"mailto:phil@logicworks.co.nz\">Demonstration user</a><br />28/05/2013 - Order set to pending status by <a href=\"mailto:phil@logicworks.co.nz\">Demonstration user</a><br />09/02/2013 - Authorised by <a href=\"mailto:info@weberp.org\">Demonstration user</a><br />09/02/2013 - Order Created by  <a href=\"mailto:info@weberp.org\">Demonstration user</a> - Auto created from sales orders<br />','20','');
INSERT INTO `purchorders` VALUES (27,'CRUISE','','2013-02-09 00:00:00',0.6324,'2013-02-09 00:00:00',0,'admin','','MEL','1234 Collins Street','Melbourne','Victoria 2345','','','Australia','+61 3 56789012','Box 2001','Ft Lauderdale, Florida','','','','','Barry Toad','','Jack Roberts',1.00,'2013-02-09','','1','2013-02-09','Printed','09/02/2013 - Printed by &lt;a href=&quot;mailto:info@weberp.org&quot;&gt;Demonstration user&lt;/a&gt;&lt;br /&gt;09/02/2013 - Order Created and Authorised by &lt;a href=&quot;mailto:info@weberp.org&quot;&gt;Demonstration user&lt;/a&gt;&lt;br /&gt;&lt;br /&gt;','30','');
INSERT INTO `purchorders` VALUES (28,'CRUISE','','2013-05-01 00:00:00',0.6432,NULL,1,'admin','','MEL','1234 Collins Street','Melbourne','Victoria 2345','','','Australia','+61 3 56789012','Box 2001','Ft Lauderdale, Florida','','','','','French Froggie','','Jack Roberts',1.00,'2013-05-01','','1','2013-05-01','Authorised','01/05/2013 - Order Created and Authorised by &lt;a href=&quot;mailto:phil@logicworks.co.nz&quot;&gt;Demonstration user&lt;/a&gt;&lt;br /&gt;&lt;br /&gt;','30','');
INSERT INTO `purchorders` VALUES (29,'OTHER','','2013-10-05 00:00:00',1.0884,NULL,1,'admin','','MEL','1234 Collins Street','Melbourne','Victoria 2345','','','Australia','+61 3 56789012','Supplier','','','','','','','','Jack Roberts',1.00,'2013-10-05','','1','2013-10-05','Completed','05/10/2013 - Order Completed on entry of GRN<br />05/10/2013 - Order Created and Authorised by <a href=\"mailto:admin@weberp.org\">Demonstration user</a><br /><br />','20','');
INSERT INTO `purchorders` VALUES (30,'BINGO','','2014-01-13 00:00:00',1,NULL,1,'admin','','MEL','1234 Collins Street','Melbourne','Victoria 2345','','2345','Australia','+(61) (3) 56789','Box 3499','Gardenier','San Fransisco','California 54424','','US','','','Jack Roberts',0.00,'2014-01-13','','','2014-01-13','Completed','13/01/2014 - Order Completed on entry of GRN<br />13/01/2014 - Order Created and Authorised by <a href=\"mailto:admin@weberp.org\">Demonstration user</a><br /><br />','30','');
INSERT INTO `purchorders` VALUES (31,'BINGO','','2014-01-13 00:00:00',1,NULL,1,'admin','','MEL','1234 Collins Street','Melbourne','Victoria 2345','','2345','Australia','+(61) (3) 56789','Box 3499','Gardenier','San Fransisco','California 54424','','US','','','Jack Roberts',0.00,'2014-01-13','','','2014-01-13','Completed','13/01/2014 - Order Completed on entry of GRN<br />13/01/2014 - Order Created and Authorised by <a href=\"mailto:admin@weberp.org\">Demonstration user</a><br /><br />','30','');
INSERT INTO `purchorders` VALUES (32,'BINGO','','2014-01-13 00:00:00',1,NULL,1,'admin','','MEL','1234 Collins Street','Melbourne','Victoria 2345','','2345','Australia','+(61) (3) 56789','Box 3499','Gardenier','San Fransisco','California 54424','','US','','','Jack Roberts',0.00,'2014-01-13','','','2014-01-13','Completed','13/01/2014 - Order Completed on entry of GRN<br />13/01/2014 - Order Created and Authorised by <a href=\"mailto:admin@weberp.org\">Demonstration user</a><br /><br />','30','');
INSERT INTO `purchorders` VALUES (33,'BINGO','','2014-01-14 00:00:00',1,NULL,1,'admin','','MEL','1234 Collins Street','Melbourne','Victoria 2345','','2345','Australia','+(61) (3) 56789','Box 3499','Gardenier','San Fransisco','California 54424','','US','','','Jack Roberts',0.00,'2014-01-14','','','2014-01-14','Completed','14/01/2014 - Order Completed on entry of GRN<br />14/01/2014 - Order Created and Authorised by <a href=\"mailto:admin@weberp.org\">Demonstration user</a><br /><br />','30','');
INSERT INTO `purchorders` VALUES (34,'WHYNOT','','2014-07-26 00:00:00',1.0884,NULL,1,'admin','','MEL','1234 Collins Street','Melbourne','Victoria 2345','','2345','Australia','+(61) (3) 5678901','Well I will ','If I','Want ','To','','','','12323','Jack Roberts',1.00,'2014-07-26','','1','2014-07-26','Completed','26/07/2014 - Order Completed on entry of GRN<br />26/07/2014 - Order Created and Authorised by <a href=\"mailto:admin@weberp.org\">Demonstration user</a><br /><br />','20','');

--
-- Dumping data for table `qasamples`
--


--
-- Dumping data for table `qatests`
--


--
-- Dumping data for table `recurringsalesorders`
--


--
-- Dumping data for table `recurrsalesorderdetails`
--


--
-- Dumping data for table `relateditems`
--


--
-- Dumping data for table `reportcolumns`
--

INSERT INTO `reportcolumns` VALUES (1,1,'Value','',0,0,7,'Net Value',0,0,'',1,'N',0);
INSERT INTO `reportcolumns` VALUES (1,2,'Gross','Profit',0,0,14,'Gross Profit',0,0,'',1,'N',0);
INSERT INTO `reportcolumns` VALUES (2,1,'Cost','Dec',0,1,1,'Cost',0,0,'',1,'N',0);
INSERT INTO `reportcolumns` VALUES (2,2,'Qty','Dec',0,1,1,'Quantity',0,0,'',1,'N',0);

--
-- Dumping data for table `reportfields`
--

INSERT INTO `reportfields` VALUES (1803,135,'critlist',1,'prices.currabrev','Currency','0','0','0');
INSERT INTO `reportfields` VALUES (1802,135,'fieldlist',4,'prices.currabrev','Currency','1','1','0');
INSERT INTO `reportfields` VALUES (1801,135,'fieldlist',3,'prices.typeabbrev','Price List','1','1','0');
INSERT INTO `reportfields` VALUES (1800,135,'fieldlist',2,'prices.price','Price','1','1','0');
INSERT INTO `reportfields` VALUES (1799,135,'fieldlist',1,'stockmaster.stockid','Item','1','1','0');
INSERT INTO `reportfields` VALUES (1797,135,'trunclong',0,'','','1','1','0');
INSERT INTO `reportfields` VALUES (1798,135,'dateselect',0,'','','1','1','a');
INSERT INTO `reportfields` VALUES (1804,135,'sortlist',1,'stockmaster.stockid','Item','0','0','1');

--
-- Dumping data for table `reportheaders`
--

INSERT INTO `reportheaders` VALUES (1,'Test report','Sales Area',0,'0','zzzzz','Customer Code',0,'1','zzzzzzzzzz','Product Code',0,'1','zzzzzzzzz','Not Used',0,'','');
INSERT INTO `reportheaders` VALUES (2,'Sales DVD-UNS','Product Code',0,'DVD-UN','DVD-UNZZZZ','Not Used',0,'','','Not Used',0,'','','Not Used',0,'','');

--
-- Dumping data for table `reportlinks`
--

INSERT INTO `reportlinks` VALUES ('accountgroups','accountsection','accountgroups.sectioninaccounts=accountsection.sectionid');
INSERT INTO `reportlinks` VALUES ('accountsection','accountgroups','accountsection.sectionid=accountgroups.sectioninaccounts');
INSERT INTO `reportlinks` VALUES ('bankaccounts','chartmaster','bankaccounts.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','bankaccounts','chartmaster.accountcode=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('banktrans','systypes','banktrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','banktrans','systypes.typeid=banktrans.type');
INSERT INTO `reportlinks` VALUES ('banktrans','bankaccounts','banktrans.bankact=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('bankaccounts','banktrans','bankaccounts.accountcode=banktrans.bankact');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.parent=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.parent');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.component');
INSERT INTO `reportlinks` VALUES ('bom','workcentres','bom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','bom','workcentres.code=bom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('bom','locations','bom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','bom','locations.loccode=bom.loccode');
INSERT INTO `reportlinks` VALUES ('buckets','workcentres','buckets.workcentre=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','buckets','workcentres.code=buckets.workcentre');
INSERT INTO `reportlinks` VALUES ('chartdetails','chartmaster','chartdetails.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','chartdetails','chartmaster.accountcode=chartdetails.accountcode');
INSERT INTO `reportlinks` VALUES ('chartdetails','periods','chartdetails.period=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','chartdetails','periods.periodno=chartdetails.period');
INSERT INTO `reportlinks` VALUES ('chartmaster','accountgroups','chartmaster.group_=accountgroups.groupname');
INSERT INTO `reportlinks` VALUES ('accountgroups','chartmaster','accountgroups.groupname=chartmaster.group_');
INSERT INTO `reportlinks` VALUES ('contractbom','workcentres','contractbom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','contractbom','workcentres.code=contractbom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('contractbom','locations','contractbom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','contractbom','locations.loccode=contractbom.loccode');
INSERT INTO `reportlinks` VALUES ('contractbom','stockmaster','contractbom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','contractbom','stockmaster.stockid=contractbom.component');
INSERT INTO `reportlinks` VALUES ('contractreqts','contracts','contractreqts.contract=contracts.contractref');
INSERT INTO `reportlinks` VALUES ('contracts','contractreqts','contracts.contractref=contractreqts.contract');
INSERT INTO `reportlinks` VALUES ('contracts','custbranch','contracts.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','contracts','custbranch.debtorno=contracts.debtorno');
INSERT INTO `reportlinks` VALUES ('contracts','stockcategory','contracts.branchcode=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','contracts','stockcategory.categoryid=contracts.branchcode');
INSERT INTO `reportlinks` VALUES ('contracts','salestypes','contracts.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','contracts','salestypes.typeabbrev=contracts.typeabbrev');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocfrom=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocto=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocto');
INSERT INTO `reportlinks` VALUES ('custbranch','debtorsmaster','custbranch.debtorno=debtorsmaster.debtorno');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','custbranch','debtorsmaster.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','areas','custbranch.area=areas.areacode');
INSERT INTO `reportlinks` VALUES ('areas','custbranch','areas.areacode=custbranch.area');
INSERT INTO `reportlinks` VALUES ('custbranch','salesman','custbranch.salesman=salesman.salesmancode');
INSERT INTO `reportlinks` VALUES ('salesman','custbranch','salesman.salesmancode=custbranch.salesman');
INSERT INTO `reportlinks` VALUES ('custbranch','locations','custbranch.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','custbranch','locations.loccode=custbranch.defaultlocation');
INSERT INTO `reportlinks` VALUES ('custbranch','shippers','custbranch.defaultshipvia=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','custbranch','shippers.shipper_id=custbranch.defaultshipvia');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','holdreasons','debtorsmaster.holdreason=holdreasons.reasoncode');
INSERT INTO `reportlinks` VALUES ('holdreasons','debtorsmaster','holdreasons.reasoncode=debtorsmaster.holdreason');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','currencies','debtorsmaster.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','debtorsmaster','currencies.currabrev=debtorsmaster.currcode');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','paymentterms','debtorsmaster.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','debtorsmaster','paymentterms.termsindicator=debtorsmaster.paymentterms');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','salestypes','debtorsmaster.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','debtorsmaster','salestypes.typeabbrev=debtorsmaster.salestype');
INSERT INTO `reportlinks` VALUES ('debtortrans','custbranch','debtortrans.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','debtortrans','custbranch.debtorno=debtortrans.debtorno');
INSERT INTO `reportlinks` VALUES ('debtortrans','systypes','debtortrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','debtortrans','systypes.typeid=debtortrans.type');
INSERT INTO `reportlinks` VALUES ('debtortrans','periods','debtortrans.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','debtortrans','periods.periodno=debtortrans.prd');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','taxauthorities','debtortranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','debtortranstaxes','taxauthorities.taxid=debtortranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','debtortrans','debtortranstaxes.debtortransid=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','debtortranstaxes','debtortrans.id=debtortranstaxes.debtortransid');
INSERT INTO `reportlinks` VALUES ('discountmatrix','salestypes','discountmatrix.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','discountmatrix','salestypes.typeabbrev=discountmatrix.salestype');
INSERT INTO `reportlinks` VALUES ('freightcosts','locations','freightcosts.locationfrom=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','freightcosts','locations.loccode=freightcosts.locationfrom');
INSERT INTO `reportlinks` VALUES ('freightcosts','shippers','freightcosts.shipperid=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','freightcosts','shippers.shipper_id=freightcosts.shipperid');
INSERT INTO `reportlinks` VALUES ('gltrans','chartmaster','gltrans.account=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','gltrans','chartmaster.accountcode=gltrans.account');
INSERT INTO `reportlinks` VALUES ('gltrans','systypes','gltrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','gltrans','systypes.typeid=gltrans.type');
INSERT INTO `reportlinks` VALUES ('gltrans','periods','gltrans.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','gltrans','periods.periodno=gltrans.periodno');
INSERT INTO `reportlinks` VALUES ('grns','suppliers','grns.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','grns','suppliers.supplierid=grns.supplierid');
INSERT INTO `reportlinks` VALUES ('grns','purchorderdetails','grns.podetailitem=purchorderdetails.podetailitem');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','grns','purchorderdetails.podetailitem=grns.podetailitem');
INSERT INTO `reportlinks` VALUES ('locations','taxprovinces','locations.taxprovinceid=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','locations','taxprovinces.taxprovinceid=locations.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('locstock','locations','locstock.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','locstock','locations.loccode=locstock.loccode');
INSERT INTO `reportlinks` VALUES ('locstock','stockmaster','locstock.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','locstock','stockmaster.stockid=locstock.stockid');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.shiploc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.shiploc');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.recloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.recloc');
INSERT INTO `reportlinks` VALUES ('loctransfers','stockmaster','loctransfers.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','loctransfers','stockmaster.stockid=loctransfers.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','stockmaster','orderdeliverydifferenceslog.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','orderdeliverydifferencesl','stockmaster.stockid=orderdeliverydifferenceslog.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','custbranch','orderdeliverydifferenceslog.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','orderdeliverydifferencesl','custbranch.debtorno=orderdeliverydifferenceslog.debtorno');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','salesorders','orderdeliverydifferenceslog.branchcode=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','orderdeliverydifferencesl','salesorders.orderno=orderdeliverydifferenceslog.branchcode');
INSERT INTO `reportlinks` VALUES ('prices','stockmaster','prices.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','prices','stockmaster.stockid=prices.stockid');
INSERT INTO `reportlinks` VALUES ('prices','currencies','prices.currabrev=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','prices','currencies.currabrev=prices.currabrev');
INSERT INTO `reportlinks` VALUES ('prices','salestypes','prices.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','prices','salestypes.typeabbrev=prices.typeabbrev');
INSERT INTO `reportlinks` VALUES ('purchdata','stockmaster','purchdata.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','purchdata','stockmaster.stockid=purchdata.stockid');
INSERT INTO `reportlinks` VALUES ('purchdata','suppliers','purchdata.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchdata','suppliers.supplierid=purchdata.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','purchorders','purchorderdetails.orderno=purchorders.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','purchorderdetails','purchorders.orderno=purchorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','suppliers','purchorders.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchorders','suppliers.supplierid=purchorders.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorders','locations','purchorders.intostocklocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','purchorders','locations.loccode=purchorders.intostocklocation');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','custbranch','recurringsalesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','recurringsalesorders','custbranch.branchcode=recurringsalesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','recurringsalesorders','recurrsalesorderdetails.recurrorderno=recurringsalesorders.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','recurrsalesorderdetails','recurringsalesorders.recurrorderno=recurrsalesorderdetails.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','stockmaster','recurrsalesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','recurrsalesorderdetails','stockmaster.stockid=recurrsalesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('reportcolumns','reportheaders','reportcolumns.reportid=reportheaders.reportid');
INSERT INTO `reportlinks` VALUES ('reportheaders','reportcolumns','reportheaders.reportid=reportcolumns.reportid');
INSERT INTO `reportlinks` VALUES ('salesanalysis','periods','salesanalysis.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','salesanalysis','periods.periodno=salesanalysis.periodno');
INSERT INTO `reportlinks` VALUES ('salescatprod','stockmaster','salescatprod.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salescatprod','stockmaster.stockid=salescatprod.stockid');
INSERT INTO `reportlinks` VALUES ('salescatprod','salescat','salescatprod.salescatid=salescat.salescatid');
INSERT INTO `reportlinks` VALUES ('salescat','salescatprod','salescat.salescatid=salescatprod.salescatid');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','salesorders','salesorderdetails.orderno=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','salesorderdetails','salesorders.orderno=salesorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','stockmaster','salesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salesorderdetails','stockmaster.stockid=salesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('salesorders','custbranch','salesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','salesorders','custbranch.branchcode=salesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('salesorders','shippers','salesorders.debtorno=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','salesorders','shippers.shipper_id=salesorders.debtorno');
INSERT INTO `reportlinks` VALUES ('salesorders','locations','salesorders.fromstkloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','salesorders','locations.loccode=salesorders.fromstkloc');
INSERT INTO `reportlinks` VALUES ('securitygroups','securityroles','securitygroups.secroleid=securityroles.secroleid');
INSERT INTO `reportlinks` VALUES ('securityroles','securitygroups','securityroles.secroleid=securitygroups.secroleid');
INSERT INTO `reportlinks` VALUES ('securitygroups','securitytokens','securitygroups.tokenid=securitytokens.tokenid');
INSERT INTO `reportlinks` VALUES ('securitytokens','securitygroups','securitytokens.tokenid=securitygroups.tokenid');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','shipments','shipmentcharges.shiptref=shipments.shiptref');
INSERT INTO `reportlinks` VALUES ('shipments','shipmentcharges','shipments.shiptref=shipmentcharges.shiptref');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','systypes','shipmentcharges.transtype=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','shipmentcharges','systypes.typeid=shipmentcharges.transtype');
INSERT INTO `reportlinks` VALUES ('shipments','suppliers','shipments.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','shipments','suppliers.supplierid=shipments.supplierid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','stockmaster','stockcheckfreeze.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcheckfreeze','stockmaster.stockid=stockcheckfreeze.stockid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','locations','stockcheckfreeze.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcheckfreeze','locations.loccode=stockcheckfreeze.loccode');
INSERT INTO `reportlinks` VALUES ('stockcounts','stockmaster','stockcounts.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcounts','stockmaster.stockid=stockcounts.stockid');
INSERT INTO `reportlinks` VALUES ('stockcounts','locations','stockcounts.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcounts','locations.loccode=stockcounts.loccode');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcategory','stockmaster.categoryid=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','stockmaster','stockcategory.categoryid=stockmaster.categoryid');
INSERT INTO `reportlinks` VALUES ('stockmaster','taxcategories','stockmaster.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','stockmaster','taxcategories.taxcatid=stockmaster.taxcatid');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockmaster','stockmoves.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockmoves','stockmaster.stockid=stockmoves.stockid');
INSERT INTO `reportlinks` VALUES ('stockmoves','systypes','stockmoves.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','stockmoves','systypes.typeid=stockmoves.type');
INSERT INTO `reportlinks` VALUES ('stockmoves','locations','stockmoves.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockmoves','locations.loccode=stockmoves.loccode');
INSERT INTO `reportlinks` VALUES ('stockmoves','periods','stockmoves.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','stockmoves','periods.periodno=stockmoves.prd');
INSERT INTO `reportlinks` VALUES ('stockmovestaxes','taxauthorities','stockmovestaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','stockmovestaxes','taxauthorities.taxid=stockmovestaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockmaster','stockserialitems.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockserialitems','stockmaster.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','locations','stockserialitems.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockserialitems','locations.loccode=stockserialitems.loccode');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockmoves','stockserialmoves.stockmoveno=stockmoves.stkmoveno');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockserialmoves','stockmoves.stkmoveno=stockserialmoves.stockmoveno');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockserialitems','stockserialmoves.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockserialmoves','stockserialitems.stockid=stockserialmoves.stockid');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocfrom=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocto=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocto');
INSERT INTO `reportlinks` VALUES ('suppliercontacts','suppliers','suppliercontacts.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','suppliercontacts','suppliers.supplierid=suppliercontacts.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','currencies','suppliers.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','suppliers','currencies.currabrev=suppliers.currcode');
INSERT INTO `reportlinks` VALUES ('suppliers','paymentterms','suppliers.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','suppliers','paymentterms.termsindicator=suppliers.paymentterms');
INSERT INTO `reportlinks` VALUES ('suppliers','taxgroups','suppliers.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','suppliers','taxgroups.taxgroupid=suppliers.taxgroupid');
INSERT INTO `reportlinks` VALUES ('supptrans','systypes','supptrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','supptrans','systypes.typeid=supptrans.type');
INSERT INTO `reportlinks` VALUES ('supptrans','suppliers','supptrans.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','supptrans','suppliers.supplierid=supptrans.supplierno');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','taxauthorities','supptranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','supptranstaxes','taxauthorities.taxid=supptranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','supptrans','supptranstaxes.supptransid=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','supptranstaxes','supptrans.id=supptranstaxes.supptransid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.taxglcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.taxglcode');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.purchtaxglaccount=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.purchtaxglaccount');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxauthorities','taxauthrates.taxauthority=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxauthrates','taxauthorities.taxid=taxauthrates.taxauthority');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxcategories','taxauthrates.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','taxauthrates','taxcategories.taxcatid=taxauthrates.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxprovinces','taxauthrates.dispatchtaxprovince=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','taxauthrates','taxprovinces.taxprovinceid=taxauthrates.dispatchtaxprovince');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxgroups','taxgrouptaxes.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','taxgrouptaxes','taxgroups.taxgroupid=taxgrouptaxes.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxauthorities','taxgrouptaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxgrouptaxes','taxauthorities.taxid=taxgrouptaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('workcentres','locations','workcentres.location=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','workcentres','locations.loccode=workcentres.location');
INSERT INTO `reportlinks` VALUES ('worksorders','locations','worksorders.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','worksorders','locations.loccode=worksorders.loccode');
INSERT INTO `reportlinks` VALUES ('worksorders','stockmaster','worksorders.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','worksorders','stockmaster.stockid=worksorders.stockid');
INSERT INTO `reportlinks` VALUES ('www_users','locations','www_users.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','www_users','locations.loccode=www_users.defaultlocation');
INSERT INTO `reportlinks` VALUES ('accountgroups','accountsection','accountgroups.sectioninaccounts=accountsection.sectionid');
INSERT INTO `reportlinks` VALUES ('accountsection','accountgroups','accountsection.sectionid=accountgroups.sectioninaccounts');
INSERT INTO `reportlinks` VALUES ('bankaccounts','chartmaster','bankaccounts.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','bankaccounts','chartmaster.accountcode=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('banktrans','systypes','banktrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','banktrans','systypes.typeid=banktrans.type');
INSERT INTO `reportlinks` VALUES ('banktrans','bankaccounts','banktrans.bankact=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('bankaccounts','banktrans','bankaccounts.accountcode=banktrans.bankact');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.parent=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.parent');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.component');
INSERT INTO `reportlinks` VALUES ('bom','workcentres','bom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','bom','workcentres.code=bom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('bom','locations','bom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','bom','locations.loccode=bom.loccode');
INSERT INTO `reportlinks` VALUES ('buckets','workcentres','buckets.workcentre=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','buckets','workcentres.code=buckets.workcentre');
INSERT INTO `reportlinks` VALUES ('chartdetails','chartmaster','chartdetails.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','chartdetails','chartmaster.accountcode=chartdetails.accountcode');
INSERT INTO `reportlinks` VALUES ('chartdetails','periods','chartdetails.period=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','chartdetails','periods.periodno=chartdetails.period');
INSERT INTO `reportlinks` VALUES ('chartmaster','accountgroups','chartmaster.group_=accountgroups.groupname');
INSERT INTO `reportlinks` VALUES ('accountgroups','chartmaster','accountgroups.groupname=chartmaster.group_');
INSERT INTO `reportlinks` VALUES ('contractbom','workcentres','contractbom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','contractbom','workcentres.code=contractbom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('contractbom','locations','contractbom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','contractbom','locations.loccode=contractbom.loccode');
INSERT INTO `reportlinks` VALUES ('contractbom','stockmaster','contractbom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','contractbom','stockmaster.stockid=contractbom.component');
INSERT INTO `reportlinks` VALUES ('contractreqts','contracts','contractreqts.contract=contracts.contractref');
INSERT INTO `reportlinks` VALUES ('contracts','contractreqts','contracts.contractref=contractreqts.contract');
INSERT INTO `reportlinks` VALUES ('contracts','custbranch','contracts.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','contracts','custbranch.debtorno=contracts.debtorno');
INSERT INTO `reportlinks` VALUES ('contracts','stockcategory','contracts.branchcode=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','contracts','stockcategory.categoryid=contracts.branchcode');
INSERT INTO `reportlinks` VALUES ('contracts','salestypes','contracts.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','contracts','salestypes.typeabbrev=contracts.typeabbrev');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocfrom=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocto=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocto');
INSERT INTO `reportlinks` VALUES ('custbranch','debtorsmaster','custbranch.debtorno=debtorsmaster.debtorno');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','custbranch','debtorsmaster.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','areas','custbranch.area=areas.areacode');
INSERT INTO `reportlinks` VALUES ('areas','custbranch','areas.areacode=custbranch.area');
INSERT INTO `reportlinks` VALUES ('custbranch','salesman','custbranch.salesman=salesman.salesmancode');
INSERT INTO `reportlinks` VALUES ('salesman','custbranch','salesman.salesmancode=custbranch.salesman');
INSERT INTO `reportlinks` VALUES ('custbranch','locations','custbranch.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','custbranch','locations.loccode=custbranch.defaultlocation');
INSERT INTO `reportlinks` VALUES ('custbranch','shippers','custbranch.defaultshipvia=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','custbranch','shippers.shipper_id=custbranch.defaultshipvia');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','holdreasons','debtorsmaster.holdreason=holdreasons.reasoncode');
INSERT INTO `reportlinks` VALUES ('holdreasons','debtorsmaster','holdreasons.reasoncode=debtorsmaster.holdreason');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','currencies','debtorsmaster.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','debtorsmaster','currencies.currabrev=debtorsmaster.currcode');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','paymentterms','debtorsmaster.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','debtorsmaster','paymentterms.termsindicator=debtorsmaster.paymentterms');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','salestypes','debtorsmaster.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','debtorsmaster','salestypes.typeabbrev=debtorsmaster.salestype');
INSERT INTO `reportlinks` VALUES ('debtortrans','custbranch','debtortrans.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','debtortrans','custbranch.debtorno=debtortrans.debtorno');
INSERT INTO `reportlinks` VALUES ('debtortrans','systypes','debtortrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','debtortrans','systypes.typeid=debtortrans.type');
INSERT INTO `reportlinks` VALUES ('debtortrans','periods','debtortrans.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','debtortrans','periods.periodno=debtortrans.prd');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','taxauthorities','debtortranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','debtortranstaxes','taxauthorities.taxid=debtortranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','debtortrans','debtortranstaxes.debtortransid=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','debtortranstaxes','debtortrans.id=debtortranstaxes.debtortransid');
INSERT INTO `reportlinks` VALUES ('discountmatrix','salestypes','discountmatrix.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','discountmatrix','salestypes.typeabbrev=discountmatrix.salestype');
INSERT INTO `reportlinks` VALUES ('freightcosts','locations','freightcosts.locationfrom=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','freightcosts','locations.loccode=freightcosts.locationfrom');
INSERT INTO `reportlinks` VALUES ('freightcosts','shippers','freightcosts.shipperid=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','freightcosts','shippers.shipper_id=freightcosts.shipperid');
INSERT INTO `reportlinks` VALUES ('gltrans','chartmaster','gltrans.account=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','gltrans','chartmaster.accountcode=gltrans.account');
INSERT INTO `reportlinks` VALUES ('gltrans','systypes','gltrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','gltrans','systypes.typeid=gltrans.type');
INSERT INTO `reportlinks` VALUES ('gltrans','periods','gltrans.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','gltrans','periods.periodno=gltrans.periodno');
INSERT INTO `reportlinks` VALUES ('grns','suppliers','grns.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','grns','suppliers.supplierid=grns.supplierid');
INSERT INTO `reportlinks` VALUES ('grns','purchorderdetails','grns.podetailitem=purchorderdetails.podetailitem');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','grns','purchorderdetails.podetailitem=grns.podetailitem');
INSERT INTO `reportlinks` VALUES ('locations','taxprovinces','locations.taxprovinceid=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','locations','taxprovinces.taxprovinceid=locations.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('locstock','locations','locstock.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','locstock','locations.loccode=locstock.loccode');
INSERT INTO `reportlinks` VALUES ('locstock','stockmaster','locstock.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','locstock','stockmaster.stockid=locstock.stockid');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.shiploc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.shiploc');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.recloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.recloc');
INSERT INTO `reportlinks` VALUES ('loctransfers','stockmaster','loctransfers.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','loctransfers','stockmaster.stockid=loctransfers.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','stockmaster','orderdeliverydifferenceslog.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','orderdeliverydifferencesl','stockmaster.stockid=orderdeliverydifferenceslog.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','custbranch','orderdeliverydifferenceslog.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','orderdeliverydifferencesl','custbranch.debtorno=orderdeliverydifferenceslog.debtorno');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','salesorders','orderdeliverydifferenceslog.branchcode=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','orderdeliverydifferencesl','salesorders.orderno=orderdeliverydifferenceslog.branchcode');
INSERT INTO `reportlinks` VALUES ('prices','stockmaster','prices.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','prices','stockmaster.stockid=prices.stockid');
INSERT INTO `reportlinks` VALUES ('prices','currencies','prices.currabrev=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','prices','currencies.currabrev=prices.currabrev');
INSERT INTO `reportlinks` VALUES ('prices','salestypes','prices.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','prices','salestypes.typeabbrev=prices.typeabbrev');
INSERT INTO `reportlinks` VALUES ('purchdata','stockmaster','purchdata.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','purchdata','stockmaster.stockid=purchdata.stockid');
INSERT INTO `reportlinks` VALUES ('purchdata','suppliers','purchdata.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchdata','suppliers.supplierid=purchdata.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','purchorders','purchorderdetails.orderno=purchorders.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','purchorderdetails','purchorders.orderno=purchorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','suppliers','purchorders.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchorders','suppliers.supplierid=purchorders.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorders','locations','purchorders.intostocklocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','purchorders','locations.loccode=purchorders.intostocklocation');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','custbranch','recurringsalesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','recurringsalesorders','custbranch.branchcode=recurringsalesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','recurringsalesorders','recurrsalesorderdetails.recurrorderno=recurringsalesorders.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','recurrsalesorderdetails','recurringsalesorders.recurrorderno=recurrsalesorderdetails.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','stockmaster','recurrsalesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','recurrsalesorderdetails','stockmaster.stockid=recurrsalesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('reportcolumns','reportheaders','reportcolumns.reportid=reportheaders.reportid');
INSERT INTO `reportlinks` VALUES ('reportheaders','reportcolumns','reportheaders.reportid=reportcolumns.reportid');
INSERT INTO `reportlinks` VALUES ('salesanalysis','periods','salesanalysis.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','salesanalysis','periods.periodno=salesanalysis.periodno');
INSERT INTO `reportlinks` VALUES ('salescatprod','stockmaster','salescatprod.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salescatprod','stockmaster.stockid=salescatprod.stockid');
INSERT INTO `reportlinks` VALUES ('salescatprod','salescat','salescatprod.salescatid=salescat.salescatid');
INSERT INTO `reportlinks` VALUES ('salescat','salescatprod','salescat.salescatid=salescatprod.salescatid');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','salesorders','salesorderdetails.orderno=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','salesorderdetails','salesorders.orderno=salesorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','stockmaster','salesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salesorderdetails','stockmaster.stockid=salesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('salesorders','custbranch','salesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','salesorders','custbranch.branchcode=salesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('salesorders','shippers','salesorders.debtorno=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','salesorders','shippers.shipper_id=salesorders.debtorno');
INSERT INTO `reportlinks` VALUES ('salesorders','locations','salesorders.fromstkloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','salesorders','locations.loccode=salesorders.fromstkloc');
INSERT INTO `reportlinks` VALUES ('securitygroups','securityroles','securitygroups.secroleid=securityroles.secroleid');
INSERT INTO `reportlinks` VALUES ('securityroles','securitygroups','securityroles.secroleid=securitygroups.secroleid');
INSERT INTO `reportlinks` VALUES ('securitygroups','securitytokens','securitygroups.tokenid=securitytokens.tokenid');
INSERT INTO `reportlinks` VALUES ('securitytokens','securitygroups','securitytokens.tokenid=securitygroups.tokenid');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','shipments','shipmentcharges.shiptref=shipments.shiptref');
INSERT INTO `reportlinks` VALUES ('shipments','shipmentcharges','shipments.shiptref=shipmentcharges.shiptref');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','systypes','shipmentcharges.transtype=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','shipmentcharges','systypes.typeid=shipmentcharges.transtype');
INSERT INTO `reportlinks` VALUES ('shipments','suppliers','shipments.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','shipments','suppliers.supplierid=shipments.supplierid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','stockmaster','stockcheckfreeze.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcheckfreeze','stockmaster.stockid=stockcheckfreeze.stockid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','locations','stockcheckfreeze.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcheckfreeze','locations.loccode=stockcheckfreeze.loccode');
INSERT INTO `reportlinks` VALUES ('stockcounts','stockmaster','stockcounts.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcounts','stockmaster.stockid=stockcounts.stockid');
INSERT INTO `reportlinks` VALUES ('stockcounts','locations','stockcounts.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcounts','locations.loccode=stockcounts.loccode');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcategory','stockmaster.categoryid=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','stockmaster','stockcategory.categoryid=stockmaster.categoryid');
INSERT INTO `reportlinks` VALUES ('stockmaster','taxcategories','stockmaster.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','stockmaster','taxcategories.taxcatid=stockmaster.taxcatid');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockmaster','stockmoves.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockmoves','stockmaster.stockid=stockmoves.stockid');
INSERT INTO `reportlinks` VALUES ('stockmoves','systypes','stockmoves.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','stockmoves','systypes.typeid=stockmoves.type');
INSERT INTO `reportlinks` VALUES ('stockmoves','locations','stockmoves.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockmoves','locations.loccode=stockmoves.loccode');
INSERT INTO `reportlinks` VALUES ('stockmoves','periods','stockmoves.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','stockmoves','periods.periodno=stockmoves.prd');
INSERT INTO `reportlinks` VALUES ('stockmovestaxes','taxauthorities','stockmovestaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','stockmovestaxes','taxauthorities.taxid=stockmovestaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockmaster','stockserialitems.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockserialitems','stockmaster.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','locations','stockserialitems.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockserialitems','locations.loccode=stockserialitems.loccode');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockmoves','stockserialmoves.stockmoveno=stockmoves.stkmoveno');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockserialmoves','stockmoves.stkmoveno=stockserialmoves.stockmoveno');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockserialitems','stockserialmoves.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockserialmoves','stockserialitems.stockid=stockserialmoves.stockid');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocfrom=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocto=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocto');
INSERT INTO `reportlinks` VALUES ('suppliercontacts','suppliers','suppliercontacts.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','suppliercontacts','suppliers.supplierid=suppliercontacts.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','currencies','suppliers.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','suppliers','currencies.currabrev=suppliers.currcode');
INSERT INTO `reportlinks` VALUES ('suppliers','paymentterms','suppliers.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','suppliers','paymentterms.termsindicator=suppliers.paymentterms');
INSERT INTO `reportlinks` VALUES ('suppliers','taxgroups','suppliers.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','suppliers','taxgroups.taxgroupid=suppliers.taxgroupid');
INSERT INTO `reportlinks` VALUES ('supptrans','systypes','supptrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','supptrans','systypes.typeid=supptrans.type');
INSERT INTO `reportlinks` VALUES ('supptrans','suppliers','supptrans.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','supptrans','suppliers.supplierid=supptrans.supplierno');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','taxauthorities','supptranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','supptranstaxes','taxauthorities.taxid=supptranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','supptrans','supptranstaxes.supptransid=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','supptranstaxes','supptrans.id=supptranstaxes.supptransid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.taxglcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.taxglcode');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.purchtaxglaccount=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.purchtaxglaccount');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxauthorities','taxauthrates.taxauthority=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxauthrates','taxauthorities.taxid=taxauthrates.taxauthority');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxcategories','taxauthrates.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','taxauthrates','taxcategories.taxcatid=taxauthrates.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxprovinces','taxauthrates.dispatchtaxprovince=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','taxauthrates','taxprovinces.taxprovinceid=taxauthrates.dispatchtaxprovince');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxgroups','taxgrouptaxes.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','taxgrouptaxes','taxgroups.taxgroupid=taxgrouptaxes.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxauthorities','taxgrouptaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxgrouptaxes','taxauthorities.taxid=taxgrouptaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('workcentres','locations','workcentres.location=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','workcentres','locations.loccode=workcentres.location');
INSERT INTO `reportlinks` VALUES ('worksorders','locations','worksorders.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','worksorders','locations.loccode=worksorders.loccode');
INSERT INTO `reportlinks` VALUES ('worksorders','stockmaster','worksorders.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','worksorders','stockmaster.stockid=worksorders.stockid');
INSERT INTO `reportlinks` VALUES ('www_users','locations','www_users.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','www_users','locations.loccode=www_users.defaultlocation');
INSERT INTO `reportlinks` VALUES ('accountgroups','accountsection','accountgroups.sectioninaccounts=accountsection.sectionid');
INSERT INTO `reportlinks` VALUES ('accountsection','accountgroups','accountsection.sectionid=accountgroups.sectioninaccounts');
INSERT INTO `reportlinks` VALUES ('bankaccounts','chartmaster','bankaccounts.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','bankaccounts','chartmaster.accountcode=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('banktrans','systypes','banktrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','banktrans','systypes.typeid=banktrans.type');
INSERT INTO `reportlinks` VALUES ('banktrans','bankaccounts','banktrans.bankact=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('bankaccounts','banktrans','bankaccounts.accountcode=banktrans.bankact');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.parent=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.parent');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.component');
INSERT INTO `reportlinks` VALUES ('bom','workcentres','bom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','bom','workcentres.code=bom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('bom','locations','bom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','bom','locations.loccode=bom.loccode');
INSERT INTO `reportlinks` VALUES ('buckets','workcentres','buckets.workcentre=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','buckets','workcentres.code=buckets.workcentre');
INSERT INTO `reportlinks` VALUES ('chartdetails','chartmaster','chartdetails.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','chartdetails','chartmaster.accountcode=chartdetails.accountcode');
INSERT INTO `reportlinks` VALUES ('chartdetails','periods','chartdetails.period=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','chartdetails','periods.periodno=chartdetails.period');
INSERT INTO `reportlinks` VALUES ('chartmaster','accountgroups','chartmaster.group_=accountgroups.groupname');
INSERT INTO `reportlinks` VALUES ('accountgroups','chartmaster','accountgroups.groupname=chartmaster.group_');
INSERT INTO `reportlinks` VALUES ('contractbom','workcentres','contractbom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','contractbom','workcentres.code=contractbom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('contractbom','locations','contractbom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','contractbom','locations.loccode=contractbom.loccode');
INSERT INTO `reportlinks` VALUES ('contractbom','stockmaster','contractbom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','contractbom','stockmaster.stockid=contractbom.component');
INSERT INTO `reportlinks` VALUES ('contractreqts','contracts','contractreqts.contract=contracts.contractref');
INSERT INTO `reportlinks` VALUES ('contracts','contractreqts','contracts.contractref=contractreqts.contract');
INSERT INTO `reportlinks` VALUES ('contracts','custbranch','contracts.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','contracts','custbranch.debtorno=contracts.debtorno');
INSERT INTO `reportlinks` VALUES ('contracts','stockcategory','contracts.branchcode=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','contracts','stockcategory.categoryid=contracts.branchcode');
INSERT INTO `reportlinks` VALUES ('contracts','salestypes','contracts.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','contracts','salestypes.typeabbrev=contracts.typeabbrev');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocfrom=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocto=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocto');
INSERT INTO `reportlinks` VALUES ('custbranch','debtorsmaster','custbranch.debtorno=debtorsmaster.debtorno');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','custbranch','debtorsmaster.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','areas','custbranch.area=areas.areacode');
INSERT INTO `reportlinks` VALUES ('areas','custbranch','areas.areacode=custbranch.area');
INSERT INTO `reportlinks` VALUES ('custbranch','salesman','custbranch.salesman=salesman.salesmancode');
INSERT INTO `reportlinks` VALUES ('salesman','custbranch','salesman.salesmancode=custbranch.salesman');
INSERT INTO `reportlinks` VALUES ('custbranch','locations','custbranch.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','custbranch','locations.loccode=custbranch.defaultlocation');
INSERT INTO `reportlinks` VALUES ('custbranch','shippers','custbranch.defaultshipvia=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','custbranch','shippers.shipper_id=custbranch.defaultshipvia');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','holdreasons','debtorsmaster.holdreason=holdreasons.reasoncode');
INSERT INTO `reportlinks` VALUES ('holdreasons','debtorsmaster','holdreasons.reasoncode=debtorsmaster.holdreason');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','currencies','debtorsmaster.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','debtorsmaster','currencies.currabrev=debtorsmaster.currcode');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','paymentterms','debtorsmaster.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','debtorsmaster','paymentterms.termsindicator=debtorsmaster.paymentterms');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','salestypes','debtorsmaster.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','debtorsmaster','salestypes.typeabbrev=debtorsmaster.salestype');
INSERT INTO `reportlinks` VALUES ('debtortrans','custbranch','debtortrans.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','debtortrans','custbranch.debtorno=debtortrans.debtorno');
INSERT INTO `reportlinks` VALUES ('debtortrans','systypes','debtortrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','debtortrans','systypes.typeid=debtortrans.type');
INSERT INTO `reportlinks` VALUES ('debtortrans','periods','debtortrans.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','debtortrans','periods.periodno=debtortrans.prd');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','taxauthorities','debtortranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','debtortranstaxes','taxauthorities.taxid=debtortranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','debtortrans','debtortranstaxes.debtortransid=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','debtortranstaxes','debtortrans.id=debtortranstaxes.debtortransid');
INSERT INTO `reportlinks` VALUES ('discountmatrix','salestypes','discountmatrix.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','discountmatrix','salestypes.typeabbrev=discountmatrix.salestype');
INSERT INTO `reportlinks` VALUES ('freightcosts','locations','freightcosts.locationfrom=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','freightcosts','locations.loccode=freightcosts.locationfrom');
INSERT INTO `reportlinks` VALUES ('freightcosts','shippers','freightcosts.shipperid=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','freightcosts','shippers.shipper_id=freightcosts.shipperid');
INSERT INTO `reportlinks` VALUES ('gltrans','chartmaster','gltrans.account=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','gltrans','chartmaster.accountcode=gltrans.account');
INSERT INTO `reportlinks` VALUES ('gltrans','systypes','gltrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','gltrans','systypes.typeid=gltrans.type');
INSERT INTO `reportlinks` VALUES ('gltrans','periods','gltrans.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','gltrans','periods.periodno=gltrans.periodno');
INSERT INTO `reportlinks` VALUES ('grns','suppliers','grns.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','grns','suppliers.supplierid=grns.supplierid');
INSERT INTO `reportlinks` VALUES ('grns','purchorderdetails','grns.podetailitem=purchorderdetails.podetailitem');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','grns','purchorderdetails.podetailitem=grns.podetailitem');
INSERT INTO `reportlinks` VALUES ('locations','taxprovinces','locations.taxprovinceid=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','locations','taxprovinces.taxprovinceid=locations.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('locstock','locations','locstock.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','locstock','locations.loccode=locstock.loccode');
INSERT INTO `reportlinks` VALUES ('locstock','stockmaster','locstock.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','locstock','stockmaster.stockid=locstock.stockid');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.shiploc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.shiploc');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.recloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.recloc');
INSERT INTO `reportlinks` VALUES ('loctransfers','stockmaster','loctransfers.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','loctransfers','stockmaster.stockid=loctransfers.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','stockmaster','orderdeliverydifferenceslog.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','orderdeliverydifferencesl','stockmaster.stockid=orderdeliverydifferenceslog.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','custbranch','orderdeliverydifferenceslog.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','orderdeliverydifferencesl','custbranch.debtorno=orderdeliverydifferenceslog.debtorno');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','salesorders','orderdeliverydifferenceslog.branchcode=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','orderdeliverydifferencesl','salesorders.orderno=orderdeliverydifferenceslog.branchcode');
INSERT INTO `reportlinks` VALUES ('prices','stockmaster','prices.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','prices','stockmaster.stockid=prices.stockid');
INSERT INTO `reportlinks` VALUES ('prices','currencies','prices.currabrev=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','prices','currencies.currabrev=prices.currabrev');
INSERT INTO `reportlinks` VALUES ('prices','salestypes','prices.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','prices','salestypes.typeabbrev=prices.typeabbrev');
INSERT INTO `reportlinks` VALUES ('purchdata','stockmaster','purchdata.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','purchdata','stockmaster.stockid=purchdata.stockid');
INSERT INTO `reportlinks` VALUES ('purchdata','suppliers','purchdata.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchdata','suppliers.supplierid=purchdata.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','purchorders','purchorderdetails.orderno=purchorders.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','purchorderdetails','purchorders.orderno=purchorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','suppliers','purchorders.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchorders','suppliers.supplierid=purchorders.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorders','locations','purchorders.intostocklocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','purchorders','locations.loccode=purchorders.intostocklocation');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','custbranch','recurringsalesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','recurringsalesorders','custbranch.branchcode=recurringsalesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','recurringsalesorders','recurrsalesorderdetails.recurrorderno=recurringsalesorders.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','recurrsalesorderdetails','recurringsalesorders.recurrorderno=recurrsalesorderdetails.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','stockmaster','recurrsalesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','recurrsalesorderdetails','stockmaster.stockid=recurrsalesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('reportcolumns','reportheaders','reportcolumns.reportid=reportheaders.reportid');
INSERT INTO `reportlinks` VALUES ('reportheaders','reportcolumns','reportheaders.reportid=reportcolumns.reportid');
INSERT INTO `reportlinks` VALUES ('salesanalysis','periods','salesanalysis.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','salesanalysis','periods.periodno=salesanalysis.periodno');
INSERT INTO `reportlinks` VALUES ('salescatprod','stockmaster','salescatprod.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salescatprod','stockmaster.stockid=salescatprod.stockid');
INSERT INTO `reportlinks` VALUES ('salescatprod','salescat','salescatprod.salescatid=salescat.salescatid');
INSERT INTO `reportlinks` VALUES ('salescat','salescatprod','salescat.salescatid=salescatprod.salescatid');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','salesorders','salesorderdetails.orderno=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','salesorderdetails','salesorders.orderno=salesorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','stockmaster','salesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salesorderdetails','stockmaster.stockid=salesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('salesorders','custbranch','salesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','salesorders','custbranch.branchcode=salesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('salesorders','shippers','salesorders.debtorno=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','salesorders','shippers.shipper_id=salesorders.debtorno');
INSERT INTO `reportlinks` VALUES ('salesorders','locations','salesorders.fromstkloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','salesorders','locations.loccode=salesorders.fromstkloc');
INSERT INTO `reportlinks` VALUES ('securitygroups','securityroles','securitygroups.secroleid=securityroles.secroleid');
INSERT INTO `reportlinks` VALUES ('securityroles','securitygroups','securityroles.secroleid=securitygroups.secroleid');
INSERT INTO `reportlinks` VALUES ('securitygroups','securitytokens','securitygroups.tokenid=securitytokens.tokenid');
INSERT INTO `reportlinks` VALUES ('securitytokens','securitygroups','securitytokens.tokenid=securitygroups.tokenid');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','shipments','shipmentcharges.shiptref=shipments.shiptref');
INSERT INTO `reportlinks` VALUES ('shipments','shipmentcharges','shipments.shiptref=shipmentcharges.shiptref');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','systypes','shipmentcharges.transtype=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','shipmentcharges','systypes.typeid=shipmentcharges.transtype');
INSERT INTO `reportlinks` VALUES ('shipments','suppliers','shipments.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','shipments','suppliers.supplierid=shipments.supplierid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','stockmaster','stockcheckfreeze.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcheckfreeze','stockmaster.stockid=stockcheckfreeze.stockid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','locations','stockcheckfreeze.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcheckfreeze','locations.loccode=stockcheckfreeze.loccode');
INSERT INTO `reportlinks` VALUES ('stockcounts','stockmaster','stockcounts.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcounts','stockmaster.stockid=stockcounts.stockid');
INSERT INTO `reportlinks` VALUES ('stockcounts','locations','stockcounts.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcounts','locations.loccode=stockcounts.loccode');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcategory','stockmaster.categoryid=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','stockmaster','stockcategory.categoryid=stockmaster.categoryid');
INSERT INTO `reportlinks` VALUES ('stockmaster','taxcategories','stockmaster.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','stockmaster','taxcategories.taxcatid=stockmaster.taxcatid');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockmaster','stockmoves.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockmoves','stockmaster.stockid=stockmoves.stockid');
INSERT INTO `reportlinks` VALUES ('stockmoves','systypes','stockmoves.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','stockmoves','systypes.typeid=stockmoves.type');
INSERT INTO `reportlinks` VALUES ('stockmoves','locations','stockmoves.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockmoves','locations.loccode=stockmoves.loccode');
INSERT INTO `reportlinks` VALUES ('stockmoves','periods','stockmoves.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','stockmoves','periods.periodno=stockmoves.prd');
INSERT INTO `reportlinks` VALUES ('stockmovestaxes','taxauthorities','stockmovestaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','stockmovestaxes','taxauthorities.taxid=stockmovestaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockmaster','stockserialitems.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockserialitems','stockmaster.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','locations','stockserialitems.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockserialitems','locations.loccode=stockserialitems.loccode');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockmoves','stockserialmoves.stockmoveno=stockmoves.stkmoveno');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockserialmoves','stockmoves.stkmoveno=stockserialmoves.stockmoveno');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockserialitems','stockserialmoves.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockserialmoves','stockserialitems.stockid=stockserialmoves.stockid');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocfrom=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocto=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocto');
INSERT INTO `reportlinks` VALUES ('suppliercontacts','suppliers','suppliercontacts.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','suppliercontacts','suppliers.supplierid=suppliercontacts.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','currencies','suppliers.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','suppliers','currencies.currabrev=suppliers.currcode');
INSERT INTO `reportlinks` VALUES ('suppliers','paymentterms','suppliers.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','suppliers','paymentterms.termsindicator=suppliers.paymentterms');
INSERT INTO `reportlinks` VALUES ('suppliers','taxgroups','suppliers.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','suppliers','taxgroups.taxgroupid=suppliers.taxgroupid');
INSERT INTO `reportlinks` VALUES ('supptrans','systypes','supptrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','supptrans','systypes.typeid=supptrans.type');
INSERT INTO `reportlinks` VALUES ('supptrans','suppliers','supptrans.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','supptrans','suppliers.supplierid=supptrans.supplierno');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','taxauthorities','supptranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','supptranstaxes','taxauthorities.taxid=supptranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','supptrans','supptranstaxes.supptransid=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','supptranstaxes','supptrans.id=supptranstaxes.supptransid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.taxglcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.taxglcode');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.purchtaxglaccount=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.purchtaxglaccount');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxauthorities','taxauthrates.taxauthority=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxauthrates','taxauthorities.taxid=taxauthrates.taxauthority');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxcategories','taxauthrates.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','taxauthrates','taxcategories.taxcatid=taxauthrates.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxprovinces','taxauthrates.dispatchtaxprovince=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','taxauthrates','taxprovinces.taxprovinceid=taxauthrates.dispatchtaxprovince');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxgroups','taxgrouptaxes.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','taxgrouptaxes','taxgroups.taxgroupid=taxgrouptaxes.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxauthorities','taxgrouptaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxgrouptaxes','taxauthorities.taxid=taxgrouptaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('workcentres','locations','workcentres.location=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','workcentres','locations.loccode=workcentres.location');
INSERT INTO `reportlinks` VALUES ('worksorders','locations','worksorders.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','worksorders','locations.loccode=worksorders.loccode');
INSERT INTO `reportlinks` VALUES ('worksorders','stockmaster','worksorders.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','worksorders','stockmaster.stockid=worksorders.stockid');
INSERT INTO `reportlinks` VALUES ('www_users','locations','www_users.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','www_users','locations.loccode=www_users.defaultlocation');
INSERT INTO `reportlinks` VALUES ('accountgroups','accountsection','accountgroups.sectioninaccounts=accountsection.sectionid');
INSERT INTO `reportlinks` VALUES ('accountsection','accountgroups','accountsection.sectionid=accountgroups.sectioninaccounts');
INSERT INTO `reportlinks` VALUES ('bankaccounts','chartmaster','bankaccounts.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','bankaccounts','chartmaster.accountcode=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('banktrans','systypes','banktrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','banktrans','systypes.typeid=banktrans.type');
INSERT INTO `reportlinks` VALUES ('banktrans','bankaccounts','banktrans.bankact=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('bankaccounts','banktrans','bankaccounts.accountcode=banktrans.bankact');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.parent=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.parent');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.component');
INSERT INTO `reportlinks` VALUES ('bom','workcentres','bom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','bom','workcentres.code=bom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('bom','locations','bom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','bom','locations.loccode=bom.loccode');
INSERT INTO `reportlinks` VALUES ('buckets','workcentres','buckets.workcentre=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','buckets','workcentres.code=buckets.workcentre');
INSERT INTO `reportlinks` VALUES ('chartdetails','chartmaster','chartdetails.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','chartdetails','chartmaster.accountcode=chartdetails.accountcode');
INSERT INTO `reportlinks` VALUES ('chartdetails','periods','chartdetails.period=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','chartdetails','periods.periodno=chartdetails.period');
INSERT INTO `reportlinks` VALUES ('chartmaster','accountgroups','chartmaster.group_=accountgroups.groupname');
INSERT INTO `reportlinks` VALUES ('accountgroups','chartmaster','accountgroups.groupname=chartmaster.group_');
INSERT INTO `reportlinks` VALUES ('contractbom','workcentres','contractbom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','contractbom','workcentres.code=contractbom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('contractbom','locations','contractbom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','contractbom','locations.loccode=contractbom.loccode');
INSERT INTO `reportlinks` VALUES ('contractbom','stockmaster','contractbom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','contractbom','stockmaster.stockid=contractbom.component');
INSERT INTO `reportlinks` VALUES ('contractreqts','contracts','contractreqts.contract=contracts.contractref');
INSERT INTO `reportlinks` VALUES ('contracts','contractreqts','contracts.contractref=contractreqts.contract');
INSERT INTO `reportlinks` VALUES ('contracts','custbranch','contracts.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','contracts','custbranch.debtorno=contracts.debtorno');
INSERT INTO `reportlinks` VALUES ('contracts','stockcategory','contracts.branchcode=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','contracts','stockcategory.categoryid=contracts.branchcode');
INSERT INTO `reportlinks` VALUES ('contracts','salestypes','contracts.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','contracts','salestypes.typeabbrev=contracts.typeabbrev');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocfrom=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocto=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocto');
INSERT INTO `reportlinks` VALUES ('custbranch','debtorsmaster','custbranch.debtorno=debtorsmaster.debtorno');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','custbranch','debtorsmaster.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','areas','custbranch.area=areas.areacode');
INSERT INTO `reportlinks` VALUES ('areas','custbranch','areas.areacode=custbranch.area');
INSERT INTO `reportlinks` VALUES ('custbranch','salesman','custbranch.salesman=salesman.salesmancode');
INSERT INTO `reportlinks` VALUES ('salesman','custbranch','salesman.salesmancode=custbranch.salesman');
INSERT INTO `reportlinks` VALUES ('custbranch','locations','custbranch.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','custbranch','locations.loccode=custbranch.defaultlocation');
INSERT INTO `reportlinks` VALUES ('custbranch','shippers','custbranch.defaultshipvia=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','custbranch','shippers.shipper_id=custbranch.defaultshipvia');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','holdreasons','debtorsmaster.holdreason=holdreasons.reasoncode');
INSERT INTO `reportlinks` VALUES ('holdreasons','debtorsmaster','holdreasons.reasoncode=debtorsmaster.holdreason');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','currencies','debtorsmaster.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','debtorsmaster','currencies.currabrev=debtorsmaster.currcode');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','paymentterms','debtorsmaster.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','debtorsmaster','paymentterms.termsindicator=debtorsmaster.paymentterms');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','salestypes','debtorsmaster.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','debtorsmaster','salestypes.typeabbrev=debtorsmaster.salestype');
INSERT INTO `reportlinks` VALUES ('debtortrans','custbranch','debtortrans.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','debtortrans','custbranch.debtorno=debtortrans.debtorno');
INSERT INTO `reportlinks` VALUES ('debtortrans','systypes','debtortrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','debtortrans','systypes.typeid=debtortrans.type');
INSERT INTO `reportlinks` VALUES ('debtortrans','periods','debtortrans.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','debtortrans','periods.periodno=debtortrans.prd');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','taxauthorities','debtortranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','debtortranstaxes','taxauthorities.taxid=debtortranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','debtortrans','debtortranstaxes.debtortransid=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','debtortranstaxes','debtortrans.id=debtortranstaxes.debtortransid');
INSERT INTO `reportlinks` VALUES ('discountmatrix','salestypes','discountmatrix.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','discountmatrix','salestypes.typeabbrev=discountmatrix.salestype');
INSERT INTO `reportlinks` VALUES ('freightcosts','locations','freightcosts.locationfrom=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','freightcosts','locations.loccode=freightcosts.locationfrom');
INSERT INTO `reportlinks` VALUES ('freightcosts','shippers','freightcosts.shipperid=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','freightcosts','shippers.shipper_id=freightcosts.shipperid');
INSERT INTO `reportlinks` VALUES ('gltrans','chartmaster','gltrans.account=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','gltrans','chartmaster.accountcode=gltrans.account');
INSERT INTO `reportlinks` VALUES ('gltrans','systypes','gltrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','gltrans','systypes.typeid=gltrans.type');
INSERT INTO `reportlinks` VALUES ('gltrans','periods','gltrans.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','gltrans','periods.periodno=gltrans.periodno');
INSERT INTO `reportlinks` VALUES ('grns','suppliers','grns.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','grns','suppliers.supplierid=grns.supplierid');
INSERT INTO `reportlinks` VALUES ('grns','purchorderdetails','grns.podetailitem=purchorderdetails.podetailitem');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','grns','purchorderdetails.podetailitem=grns.podetailitem');
INSERT INTO `reportlinks` VALUES ('locations','taxprovinces','locations.taxprovinceid=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','locations','taxprovinces.taxprovinceid=locations.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('locstock','locations','locstock.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','locstock','locations.loccode=locstock.loccode');
INSERT INTO `reportlinks` VALUES ('locstock','stockmaster','locstock.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','locstock','stockmaster.stockid=locstock.stockid');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.shiploc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.shiploc');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.recloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.recloc');
INSERT INTO `reportlinks` VALUES ('loctransfers','stockmaster','loctransfers.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','loctransfers','stockmaster.stockid=loctransfers.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','stockmaster','orderdeliverydifferenceslog.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','orderdeliverydifferencesl','stockmaster.stockid=orderdeliverydifferenceslog.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','custbranch','orderdeliverydifferenceslog.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','orderdeliverydifferencesl','custbranch.debtorno=orderdeliverydifferenceslog.debtorno');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','salesorders','orderdeliverydifferenceslog.branchcode=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','orderdeliverydifferencesl','salesorders.orderno=orderdeliverydifferenceslog.branchcode');
INSERT INTO `reportlinks` VALUES ('prices','stockmaster','prices.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','prices','stockmaster.stockid=prices.stockid');
INSERT INTO `reportlinks` VALUES ('prices','currencies','prices.currabrev=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','prices','currencies.currabrev=prices.currabrev');
INSERT INTO `reportlinks` VALUES ('prices','salestypes','prices.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','prices','salestypes.typeabbrev=prices.typeabbrev');
INSERT INTO `reportlinks` VALUES ('purchdata','stockmaster','purchdata.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','purchdata','stockmaster.stockid=purchdata.stockid');
INSERT INTO `reportlinks` VALUES ('purchdata','suppliers','purchdata.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchdata','suppliers.supplierid=purchdata.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','purchorders','purchorderdetails.orderno=purchorders.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','purchorderdetails','purchorders.orderno=purchorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','suppliers','purchorders.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchorders','suppliers.supplierid=purchorders.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorders','locations','purchorders.intostocklocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','purchorders','locations.loccode=purchorders.intostocklocation');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','custbranch','recurringsalesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','recurringsalesorders','custbranch.branchcode=recurringsalesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','recurringsalesorders','recurrsalesorderdetails.recurrorderno=recurringsalesorders.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','recurrsalesorderdetails','recurringsalesorders.recurrorderno=recurrsalesorderdetails.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','stockmaster','recurrsalesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','recurrsalesorderdetails','stockmaster.stockid=recurrsalesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('reportcolumns','reportheaders','reportcolumns.reportid=reportheaders.reportid');
INSERT INTO `reportlinks` VALUES ('reportheaders','reportcolumns','reportheaders.reportid=reportcolumns.reportid');
INSERT INTO `reportlinks` VALUES ('salesanalysis','periods','salesanalysis.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','salesanalysis','periods.periodno=salesanalysis.periodno');
INSERT INTO `reportlinks` VALUES ('salescatprod','stockmaster','salescatprod.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salescatprod','stockmaster.stockid=salescatprod.stockid');
INSERT INTO `reportlinks` VALUES ('salescatprod','salescat','salescatprod.salescatid=salescat.salescatid');
INSERT INTO `reportlinks` VALUES ('salescat','salescatprod','salescat.salescatid=salescatprod.salescatid');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','salesorders','salesorderdetails.orderno=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','salesorderdetails','salesorders.orderno=salesorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','stockmaster','salesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salesorderdetails','stockmaster.stockid=salesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('salesorders','custbranch','salesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','salesorders','custbranch.branchcode=salesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('salesorders','shippers','salesorders.debtorno=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','salesorders','shippers.shipper_id=salesorders.debtorno');
INSERT INTO `reportlinks` VALUES ('salesorders','locations','salesorders.fromstkloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','salesorders','locations.loccode=salesorders.fromstkloc');
INSERT INTO `reportlinks` VALUES ('securitygroups','securityroles','securitygroups.secroleid=securityroles.secroleid');
INSERT INTO `reportlinks` VALUES ('securityroles','securitygroups','securityroles.secroleid=securitygroups.secroleid');
INSERT INTO `reportlinks` VALUES ('securitygroups','securitytokens','securitygroups.tokenid=securitytokens.tokenid');
INSERT INTO `reportlinks` VALUES ('securitytokens','securitygroups','securitytokens.tokenid=securitygroups.tokenid');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','shipments','shipmentcharges.shiptref=shipments.shiptref');
INSERT INTO `reportlinks` VALUES ('shipments','shipmentcharges','shipments.shiptref=shipmentcharges.shiptref');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','systypes','shipmentcharges.transtype=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','shipmentcharges','systypes.typeid=shipmentcharges.transtype');
INSERT INTO `reportlinks` VALUES ('shipments','suppliers','shipments.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','shipments','suppliers.supplierid=shipments.supplierid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','stockmaster','stockcheckfreeze.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcheckfreeze','stockmaster.stockid=stockcheckfreeze.stockid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','locations','stockcheckfreeze.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcheckfreeze','locations.loccode=stockcheckfreeze.loccode');
INSERT INTO `reportlinks` VALUES ('stockcounts','stockmaster','stockcounts.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcounts','stockmaster.stockid=stockcounts.stockid');
INSERT INTO `reportlinks` VALUES ('stockcounts','locations','stockcounts.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcounts','locations.loccode=stockcounts.loccode');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcategory','stockmaster.categoryid=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','stockmaster','stockcategory.categoryid=stockmaster.categoryid');
INSERT INTO `reportlinks` VALUES ('stockmaster','taxcategories','stockmaster.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','stockmaster','taxcategories.taxcatid=stockmaster.taxcatid');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockmaster','stockmoves.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockmoves','stockmaster.stockid=stockmoves.stockid');
INSERT INTO `reportlinks` VALUES ('stockmoves','systypes','stockmoves.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','stockmoves','systypes.typeid=stockmoves.type');
INSERT INTO `reportlinks` VALUES ('stockmoves','locations','stockmoves.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockmoves','locations.loccode=stockmoves.loccode');
INSERT INTO `reportlinks` VALUES ('stockmoves','periods','stockmoves.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','stockmoves','periods.periodno=stockmoves.prd');
INSERT INTO `reportlinks` VALUES ('stockmovestaxes','taxauthorities','stockmovestaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','stockmovestaxes','taxauthorities.taxid=stockmovestaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockmaster','stockserialitems.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockserialitems','stockmaster.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','locations','stockserialitems.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockserialitems','locations.loccode=stockserialitems.loccode');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockmoves','stockserialmoves.stockmoveno=stockmoves.stkmoveno');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockserialmoves','stockmoves.stkmoveno=stockserialmoves.stockmoveno');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockserialitems','stockserialmoves.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockserialmoves','stockserialitems.stockid=stockserialmoves.stockid');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocfrom=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocto=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocto');
INSERT INTO `reportlinks` VALUES ('suppliercontacts','suppliers','suppliercontacts.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','suppliercontacts','suppliers.supplierid=suppliercontacts.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','currencies','suppliers.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','suppliers','currencies.currabrev=suppliers.currcode');
INSERT INTO `reportlinks` VALUES ('suppliers','paymentterms','suppliers.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','suppliers','paymentterms.termsindicator=suppliers.paymentterms');
INSERT INTO `reportlinks` VALUES ('suppliers','taxgroups','suppliers.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','suppliers','taxgroups.taxgroupid=suppliers.taxgroupid');
INSERT INTO `reportlinks` VALUES ('supptrans','systypes','supptrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','supptrans','systypes.typeid=supptrans.type');
INSERT INTO `reportlinks` VALUES ('supptrans','suppliers','supptrans.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','supptrans','suppliers.supplierid=supptrans.supplierno');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','taxauthorities','supptranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','supptranstaxes','taxauthorities.taxid=supptranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','supptrans','supptranstaxes.supptransid=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','supptranstaxes','supptrans.id=supptranstaxes.supptransid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.taxglcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.taxglcode');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.purchtaxglaccount=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.purchtaxglaccount');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxauthorities','taxauthrates.taxauthority=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxauthrates','taxauthorities.taxid=taxauthrates.taxauthority');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxcategories','taxauthrates.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','taxauthrates','taxcategories.taxcatid=taxauthrates.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxprovinces','taxauthrates.dispatchtaxprovince=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','taxauthrates','taxprovinces.taxprovinceid=taxauthrates.dispatchtaxprovince');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxgroups','taxgrouptaxes.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','taxgrouptaxes','taxgroups.taxgroupid=taxgrouptaxes.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxauthorities','taxgrouptaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxgrouptaxes','taxauthorities.taxid=taxgrouptaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('workcentres','locations','workcentres.location=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','workcentres','locations.loccode=workcentres.location');
INSERT INTO `reportlinks` VALUES ('worksorders','locations','worksorders.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','worksorders','locations.loccode=worksorders.loccode');
INSERT INTO `reportlinks` VALUES ('worksorders','stockmaster','worksorders.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','worksorders','stockmaster.stockid=worksorders.stockid');
INSERT INTO `reportlinks` VALUES ('www_users','locations','www_users.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','www_users','locations.loccode=www_users.defaultlocation');
INSERT INTO `reportlinks` VALUES ('accountgroups','accountsection','accountgroups.sectioninaccounts=accountsection.sectionid');
INSERT INTO `reportlinks` VALUES ('accountsection','accountgroups','accountsection.sectionid=accountgroups.sectioninaccounts');
INSERT INTO `reportlinks` VALUES ('bankaccounts','chartmaster','bankaccounts.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','bankaccounts','chartmaster.accountcode=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('banktrans','systypes','banktrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','banktrans','systypes.typeid=banktrans.type');
INSERT INTO `reportlinks` VALUES ('banktrans','bankaccounts','banktrans.bankact=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('bankaccounts','banktrans','bankaccounts.accountcode=banktrans.bankact');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.parent=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.parent');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.component');
INSERT INTO `reportlinks` VALUES ('bom','workcentres','bom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','bom','workcentres.code=bom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('bom','locations','bom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','bom','locations.loccode=bom.loccode');
INSERT INTO `reportlinks` VALUES ('buckets','workcentres','buckets.workcentre=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','buckets','workcentres.code=buckets.workcentre');
INSERT INTO `reportlinks` VALUES ('chartdetails','chartmaster','chartdetails.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','chartdetails','chartmaster.accountcode=chartdetails.accountcode');
INSERT INTO `reportlinks` VALUES ('chartdetails','periods','chartdetails.period=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','chartdetails','periods.periodno=chartdetails.period');
INSERT INTO `reportlinks` VALUES ('chartmaster','accountgroups','chartmaster.group_=accountgroups.groupname');
INSERT INTO `reportlinks` VALUES ('accountgroups','chartmaster','accountgroups.groupname=chartmaster.group_');
INSERT INTO `reportlinks` VALUES ('contractbom','workcentres','contractbom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','contractbom','workcentres.code=contractbom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('contractbom','locations','contractbom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','contractbom','locations.loccode=contractbom.loccode');
INSERT INTO `reportlinks` VALUES ('contractbom','stockmaster','contractbom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','contractbom','stockmaster.stockid=contractbom.component');
INSERT INTO `reportlinks` VALUES ('contractreqts','contracts','contractreqts.contract=contracts.contractref');
INSERT INTO `reportlinks` VALUES ('contracts','contractreqts','contracts.contractref=contractreqts.contract');
INSERT INTO `reportlinks` VALUES ('contracts','custbranch','contracts.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','contracts','custbranch.debtorno=contracts.debtorno');
INSERT INTO `reportlinks` VALUES ('contracts','stockcategory','contracts.branchcode=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','contracts','stockcategory.categoryid=contracts.branchcode');
INSERT INTO `reportlinks` VALUES ('contracts','salestypes','contracts.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','contracts','salestypes.typeabbrev=contracts.typeabbrev');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocfrom=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocto=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocto');
INSERT INTO `reportlinks` VALUES ('custbranch','debtorsmaster','custbranch.debtorno=debtorsmaster.debtorno');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','custbranch','debtorsmaster.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','areas','custbranch.area=areas.areacode');
INSERT INTO `reportlinks` VALUES ('areas','custbranch','areas.areacode=custbranch.area');
INSERT INTO `reportlinks` VALUES ('custbranch','salesman','custbranch.salesman=salesman.salesmancode');
INSERT INTO `reportlinks` VALUES ('salesman','custbranch','salesman.salesmancode=custbranch.salesman');
INSERT INTO `reportlinks` VALUES ('custbranch','locations','custbranch.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','custbranch','locations.loccode=custbranch.defaultlocation');
INSERT INTO `reportlinks` VALUES ('custbranch','shippers','custbranch.defaultshipvia=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','custbranch','shippers.shipper_id=custbranch.defaultshipvia');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','holdreasons','debtorsmaster.holdreason=holdreasons.reasoncode');
INSERT INTO `reportlinks` VALUES ('holdreasons','debtorsmaster','holdreasons.reasoncode=debtorsmaster.holdreason');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','currencies','debtorsmaster.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','debtorsmaster','currencies.currabrev=debtorsmaster.currcode');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','paymentterms','debtorsmaster.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','debtorsmaster','paymentterms.termsindicator=debtorsmaster.paymentterms');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','salestypes','debtorsmaster.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','debtorsmaster','salestypes.typeabbrev=debtorsmaster.salestype');
INSERT INTO `reportlinks` VALUES ('debtortrans','custbranch','debtortrans.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','debtortrans','custbranch.debtorno=debtortrans.debtorno');
INSERT INTO `reportlinks` VALUES ('debtortrans','systypes','debtortrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','debtortrans','systypes.typeid=debtortrans.type');
INSERT INTO `reportlinks` VALUES ('debtortrans','periods','debtortrans.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','debtortrans','periods.periodno=debtortrans.prd');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','taxauthorities','debtortranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','debtortranstaxes','taxauthorities.taxid=debtortranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','debtortrans','debtortranstaxes.debtortransid=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','debtortranstaxes','debtortrans.id=debtortranstaxes.debtortransid');
INSERT INTO `reportlinks` VALUES ('discountmatrix','salestypes','discountmatrix.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','discountmatrix','salestypes.typeabbrev=discountmatrix.salestype');
INSERT INTO `reportlinks` VALUES ('freightcosts','locations','freightcosts.locationfrom=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','freightcosts','locations.loccode=freightcosts.locationfrom');
INSERT INTO `reportlinks` VALUES ('freightcosts','shippers','freightcosts.shipperid=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','freightcosts','shippers.shipper_id=freightcosts.shipperid');
INSERT INTO `reportlinks` VALUES ('gltrans','chartmaster','gltrans.account=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','gltrans','chartmaster.accountcode=gltrans.account');
INSERT INTO `reportlinks` VALUES ('gltrans','systypes','gltrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','gltrans','systypes.typeid=gltrans.type');
INSERT INTO `reportlinks` VALUES ('gltrans','periods','gltrans.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','gltrans','periods.periodno=gltrans.periodno');
INSERT INTO `reportlinks` VALUES ('grns','suppliers','grns.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','grns','suppliers.supplierid=grns.supplierid');
INSERT INTO `reportlinks` VALUES ('grns','purchorderdetails','grns.podetailitem=purchorderdetails.podetailitem');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','grns','purchorderdetails.podetailitem=grns.podetailitem');
INSERT INTO `reportlinks` VALUES ('locations','taxprovinces','locations.taxprovinceid=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','locations','taxprovinces.taxprovinceid=locations.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('locstock','locations','locstock.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','locstock','locations.loccode=locstock.loccode');
INSERT INTO `reportlinks` VALUES ('locstock','stockmaster','locstock.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','locstock','stockmaster.stockid=locstock.stockid');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.shiploc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.shiploc');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.recloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.recloc');
INSERT INTO `reportlinks` VALUES ('loctransfers','stockmaster','loctransfers.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','loctransfers','stockmaster.stockid=loctransfers.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','stockmaster','orderdeliverydifferenceslog.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','orderdeliverydifferencesl','stockmaster.stockid=orderdeliverydifferenceslog.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','custbranch','orderdeliverydifferenceslog.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','orderdeliverydifferencesl','custbranch.debtorno=orderdeliverydifferenceslog.debtorno');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','salesorders','orderdeliverydifferenceslog.branchcode=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','orderdeliverydifferencesl','salesorders.orderno=orderdeliverydifferenceslog.branchcode');
INSERT INTO `reportlinks` VALUES ('prices','stockmaster','prices.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','prices','stockmaster.stockid=prices.stockid');
INSERT INTO `reportlinks` VALUES ('prices','currencies','prices.currabrev=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','prices','currencies.currabrev=prices.currabrev');
INSERT INTO `reportlinks` VALUES ('prices','salestypes','prices.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','prices','salestypes.typeabbrev=prices.typeabbrev');
INSERT INTO `reportlinks` VALUES ('purchdata','stockmaster','purchdata.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','purchdata','stockmaster.stockid=purchdata.stockid');
INSERT INTO `reportlinks` VALUES ('purchdata','suppliers','purchdata.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchdata','suppliers.supplierid=purchdata.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','purchorders','purchorderdetails.orderno=purchorders.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','purchorderdetails','purchorders.orderno=purchorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','suppliers','purchorders.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchorders','suppliers.supplierid=purchorders.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorders','locations','purchorders.intostocklocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','purchorders','locations.loccode=purchorders.intostocklocation');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','custbranch','recurringsalesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','recurringsalesorders','custbranch.branchcode=recurringsalesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','recurringsalesorders','recurrsalesorderdetails.recurrorderno=recurringsalesorders.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','recurrsalesorderdetails','recurringsalesorders.recurrorderno=recurrsalesorderdetails.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','stockmaster','recurrsalesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','recurrsalesorderdetails','stockmaster.stockid=recurrsalesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('reportcolumns','reportheaders','reportcolumns.reportid=reportheaders.reportid');
INSERT INTO `reportlinks` VALUES ('reportheaders','reportcolumns','reportheaders.reportid=reportcolumns.reportid');
INSERT INTO `reportlinks` VALUES ('salesanalysis','periods','salesanalysis.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','salesanalysis','periods.periodno=salesanalysis.periodno');
INSERT INTO `reportlinks` VALUES ('salescatprod','stockmaster','salescatprod.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salescatprod','stockmaster.stockid=salescatprod.stockid');
INSERT INTO `reportlinks` VALUES ('salescatprod','salescat','salescatprod.salescatid=salescat.salescatid');
INSERT INTO `reportlinks` VALUES ('salescat','salescatprod','salescat.salescatid=salescatprod.salescatid');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','salesorders','salesorderdetails.orderno=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','salesorderdetails','salesorders.orderno=salesorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','stockmaster','salesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salesorderdetails','stockmaster.stockid=salesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('salesorders','custbranch','salesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','salesorders','custbranch.branchcode=salesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('salesorders','shippers','salesorders.debtorno=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','salesorders','shippers.shipper_id=salesorders.debtorno');
INSERT INTO `reportlinks` VALUES ('salesorders','locations','salesorders.fromstkloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','salesorders','locations.loccode=salesorders.fromstkloc');
INSERT INTO `reportlinks` VALUES ('securitygroups','securityroles','securitygroups.secroleid=securityroles.secroleid');
INSERT INTO `reportlinks` VALUES ('securityroles','securitygroups','securityroles.secroleid=securitygroups.secroleid');
INSERT INTO `reportlinks` VALUES ('securitygroups','securitytokens','securitygroups.tokenid=securitytokens.tokenid');
INSERT INTO `reportlinks` VALUES ('securitytokens','securitygroups','securitytokens.tokenid=securitygroups.tokenid');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','shipments','shipmentcharges.shiptref=shipments.shiptref');
INSERT INTO `reportlinks` VALUES ('shipments','shipmentcharges','shipments.shiptref=shipmentcharges.shiptref');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','systypes','shipmentcharges.transtype=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','shipmentcharges','systypes.typeid=shipmentcharges.transtype');
INSERT INTO `reportlinks` VALUES ('shipments','suppliers','shipments.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','shipments','suppliers.supplierid=shipments.supplierid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','stockmaster','stockcheckfreeze.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcheckfreeze','stockmaster.stockid=stockcheckfreeze.stockid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','locations','stockcheckfreeze.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcheckfreeze','locations.loccode=stockcheckfreeze.loccode');
INSERT INTO `reportlinks` VALUES ('stockcounts','stockmaster','stockcounts.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcounts','stockmaster.stockid=stockcounts.stockid');
INSERT INTO `reportlinks` VALUES ('stockcounts','locations','stockcounts.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcounts','locations.loccode=stockcounts.loccode');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcategory','stockmaster.categoryid=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','stockmaster','stockcategory.categoryid=stockmaster.categoryid');
INSERT INTO `reportlinks` VALUES ('stockmaster','taxcategories','stockmaster.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','stockmaster','taxcategories.taxcatid=stockmaster.taxcatid');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockmaster','stockmoves.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockmoves','stockmaster.stockid=stockmoves.stockid');
INSERT INTO `reportlinks` VALUES ('stockmoves','systypes','stockmoves.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','stockmoves','systypes.typeid=stockmoves.type');
INSERT INTO `reportlinks` VALUES ('stockmoves','locations','stockmoves.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockmoves','locations.loccode=stockmoves.loccode');
INSERT INTO `reportlinks` VALUES ('stockmoves','periods','stockmoves.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','stockmoves','periods.periodno=stockmoves.prd');
INSERT INTO `reportlinks` VALUES ('stockmovestaxes','taxauthorities','stockmovestaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','stockmovestaxes','taxauthorities.taxid=stockmovestaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockmaster','stockserialitems.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockserialitems','stockmaster.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','locations','stockserialitems.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockserialitems','locations.loccode=stockserialitems.loccode');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockmoves','stockserialmoves.stockmoveno=stockmoves.stkmoveno');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockserialmoves','stockmoves.stkmoveno=stockserialmoves.stockmoveno');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockserialitems','stockserialmoves.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockserialmoves','stockserialitems.stockid=stockserialmoves.stockid');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocfrom=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocto=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocto');
INSERT INTO `reportlinks` VALUES ('suppliercontacts','suppliers','suppliercontacts.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','suppliercontacts','suppliers.supplierid=suppliercontacts.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','currencies','suppliers.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','suppliers','currencies.currabrev=suppliers.currcode');
INSERT INTO `reportlinks` VALUES ('suppliers','paymentterms','suppliers.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','suppliers','paymentterms.termsindicator=suppliers.paymentterms');
INSERT INTO `reportlinks` VALUES ('suppliers','taxgroups','suppliers.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','suppliers','taxgroups.taxgroupid=suppliers.taxgroupid');
INSERT INTO `reportlinks` VALUES ('supptrans','systypes','supptrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','supptrans','systypes.typeid=supptrans.type');
INSERT INTO `reportlinks` VALUES ('supptrans','suppliers','supptrans.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','supptrans','suppliers.supplierid=supptrans.supplierno');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','taxauthorities','supptranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','supptranstaxes','taxauthorities.taxid=supptranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','supptrans','supptranstaxes.supptransid=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','supptranstaxes','supptrans.id=supptranstaxes.supptransid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.taxglcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.taxglcode');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.purchtaxglaccount=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.purchtaxglaccount');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxauthorities','taxauthrates.taxauthority=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxauthrates','taxauthorities.taxid=taxauthrates.taxauthority');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxcategories','taxauthrates.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','taxauthrates','taxcategories.taxcatid=taxauthrates.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxprovinces','taxauthrates.dispatchtaxprovince=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','taxauthrates','taxprovinces.taxprovinceid=taxauthrates.dispatchtaxprovince');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxgroups','taxgrouptaxes.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','taxgrouptaxes','taxgroups.taxgroupid=taxgrouptaxes.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxauthorities','taxgrouptaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxgrouptaxes','taxauthorities.taxid=taxgrouptaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('workcentres','locations','workcentres.location=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','workcentres','locations.loccode=workcentres.location');
INSERT INTO `reportlinks` VALUES ('worksorders','locations','worksorders.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','worksorders','locations.loccode=worksorders.loccode');
INSERT INTO `reportlinks` VALUES ('worksorders','stockmaster','worksorders.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','worksorders','stockmaster.stockid=worksorders.stockid');
INSERT INTO `reportlinks` VALUES ('www_users','locations','www_users.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','www_users','locations.loccode=www_users.defaultlocation');
INSERT INTO `reportlinks` VALUES ('accountgroups','accountsection','accountgroups.sectioninaccounts=accountsection.sectionid');
INSERT INTO `reportlinks` VALUES ('accountsection','accountgroups','accountsection.sectionid=accountgroups.sectioninaccounts');
INSERT INTO `reportlinks` VALUES ('bankaccounts','chartmaster','bankaccounts.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','bankaccounts','chartmaster.accountcode=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('banktrans','systypes','banktrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','banktrans','systypes.typeid=banktrans.type');
INSERT INTO `reportlinks` VALUES ('banktrans','bankaccounts','banktrans.bankact=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('bankaccounts','banktrans','bankaccounts.accountcode=banktrans.bankact');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.parent=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.parent');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.component');
INSERT INTO `reportlinks` VALUES ('bom','workcentres','bom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','bom','workcentres.code=bom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('bom','locations','bom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','bom','locations.loccode=bom.loccode');
INSERT INTO `reportlinks` VALUES ('buckets','workcentres','buckets.workcentre=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','buckets','workcentres.code=buckets.workcentre');
INSERT INTO `reportlinks` VALUES ('chartdetails','chartmaster','chartdetails.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','chartdetails','chartmaster.accountcode=chartdetails.accountcode');
INSERT INTO `reportlinks` VALUES ('chartdetails','periods','chartdetails.period=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','chartdetails','periods.periodno=chartdetails.period');
INSERT INTO `reportlinks` VALUES ('chartmaster','accountgroups','chartmaster.group_=accountgroups.groupname');
INSERT INTO `reportlinks` VALUES ('accountgroups','chartmaster','accountgroups.groupname=chartmaster.group_');
INSERT INTO `reportlinks` VALUES ('contractbom','workcentres','contractbom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','contractbom','workcentres.code=contractbom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('contractbom','locations','contractbom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','contractbom','locations.loccode=contractbom.loccode');
INSERT INTO `reportlinks` VALUES ('contractbom','stockmaster','contractbom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','contractbom','stockmaster.stockid=contractbom.component');
INSERT INTO `reportlinks` VALUES ('contractreqts','contracts','contractreqts.contract=contracts.contractref');
INSERT INTO `reportlinks` VALUES ('contracts','contractreqts','contracts.contractref=contractreqts.contract');
INSERT INTO `reportlinks` VALUES ('contracts','custbranch','contracts.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','contracts','custbranch.debtorno=contracts.debtorno');
INSERT INTO `reportlinks` VALUES ('contracts','stockcategory','contracts.branchcode=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','contracts','stockcategory.categoryid=contracts.branchcode');
INSERT INTO `reportlinks` VALUES ('contracts','salestypes','contracts.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','contracts','salestypes.typeabbrev=contracts.typeabbrev');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocfrom=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocto=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocto');
INSERT INTO `reportlinks` VALUES ('custbranch','debtorsmaster','custbranch.debtorno=debtorsmaster.debtorno');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','custbranch','debtorsmaster.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','areas','custbranch.area=areas.areacode');
INSERT INTO `reportlinks` VALUES ('areas','custbranch','areas.areacode=custbranch.area');
INSERT INTO `reportlinks` VALUES ('custbranch','salesman','custbranch.salesman=salesman.salesmancode');
INSERT INTO `reportlinks` VALUES ('salesman','custbranch','salesman.salesmancode=custbranch.salesman');
INSERT INTO `reportlinks` VALUES ('custbranch','locations','custbranch.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','custbranch','locations.loccode=custbranch.defaultlocation');
INSERT INTO `reportlinks` VALUES ('custbranch','shippers','custbranch.defaultshipvia=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','custbranch','shippers.shipper_id=custbranch.defaultshipvia');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','holdreasons','debtorsmaster.holdreason=holdreasons.reasoncode');
INSERT INTO `reportlinks` VALUES ('holdreasons','debtorsmaster','holdreasons.reasoncode=debtorsmaster.holdreason');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','currencies','debtorsmaster.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','debtorsmaster','currencies.currabrev=debtorsmaster.currcode');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','paymentterms','debtorsmaster.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','debtorsmaster','paymentterms.termsindicator=debtorsmaster.paymentterms');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','salestypes','debtorsmaster.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','debtorsmaster','salestypes.typeabbrev=debtorsmaster.salestype');
INSERT INTO `reportlinks` VALUES ('debtortrans','custbranch','debtortrans.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','debtortrans','custbranch.debtorno=debtortrans.debtorno');
INSERT INTO `reportlinks` VALUES ('debtortrans','systypes','debtortrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','debtortrans','systypes.typeid=debtortrans.type');
INSERT INTO `reportlinks` VALUES ('debtortrans','periods','debtortrans.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','debtortrans','periods.periodno=debtortrans.prd');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','taxauthorities','debtortranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','debtortranstaxes','taxauthorities.taxid=debtortranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','debtortrans','debtortranstaxes.debtortransid=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','debtortranstaxes','debtortrans.id=debtortranstaxes.debtortransid');
INSERT INTO `reportlinks` VALUES ('discountmatrix','salestypes','discountmatrix.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','discountmatrix','salestypes.typeabbrev=discountmatrix.salestype');
INSERT INTO `reportlinks` VALUES ('freightcosts','locations','freightcosts.locationfrom=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','freightcosts','locations.loccode=freightcosts.locationfrom');
INSERT INTO `reportlinks` VALUES ('freightcosts','shippers','freightcosts.shipperid=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','freightcosts','shippers.shipper_id=freightcosts.shipperid');
INSERT INTO `reportlinks` VALUES ('gltrans','chartmaster','gltrans.account=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','gltrans','chartmaster.accountcode=gltrans.account');
INSERT INTO `reportlinks` VALUES ('gltrans','systypes','gltrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','gltrans','systypes.typeid=gltrans.type');
INSERT INTO `reportlinks` VALUES ('gltrans','periods','gltrans.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','gltrans','periods.periodno=gltrans.periodno');
INSERT INTO `reportlinks` VALUES ('grns','suppliers','grns.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','grns','suppliers.supplierid=grns.supplierid');
INSERT INTO `reportlinks` VALUES ('grns','purchorderdetails','grns.podetailitem=purchorderdetails.podetailitem');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','grns','purchorderdetails.podetailitem=grns.podetailitem');
INSERT INTO `reportlinks` VALUES ('locations','taxprovinces','locations.taxprovinceid=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','locations','taxprovinces.taxprovinceid=locations.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('locstock','locations','locstock.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','locstock','locations.loccode=locstock.loccode');
INSERT INTO `reportlinks` VALUES ('locstock','stockmaster','locstock.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','locstock','stockmaster.stockid=locstock.stockid');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.shiploc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.shiploc');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.recloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.recloc');
INSERT INTO `reportlinks` VALUES ('loctransfers','stockmaster','loctransfers.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','loctransfers','stockmaster.stockid=loctransfers.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','stockmaster','orderdeliverydifferenceslog.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','orderdeliverydifferencesl','stockmaster.stockid=orderdeliverydifferenceslog.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','custbranch','orderdeliverydifferenceslog.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','orderdeliverydifferencesl','custbranch.debtorno=orderdeliverydifferenceslog.debtorno');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','salesorders','orderdeliverydifferenceslog.branchcode=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','orderdeliverydifferencesl','salesorders.orderno=orderdeliverydifferenceslog.branchcode');
INSERT INTO `reportlinks` VALUES ('prices','stockmaster','prices.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','prices','stockmaster.stockid=prices.stockid');
INSERT INTO `reportlinks` VALUES ('prices','currencies','prices.currabrev=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','prices','currencies.currabrev=prices.currabrev');
INSERT INTO `reportlinks` VALUES ('prices','salestypes','prices.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','prices','salestypes.typeabbrev=prices.typeabbrev');
INSERT INTO `reportlinks` VALUES ('purchdata','stockmaster','purchdata.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','purchdata','stockmaster.stockid=purchdata.stockid');
INSERT INTO `reportlinks` VALUES ('purchdata','suppliers','purchdata.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchdata','suppliers.supplierid=purchdata.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','purchorders','purchorderdetails.orderno=purchorders.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','purchorderdetails','purchorders.orderno=purchorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','suppliers','purchorders.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchorders','suppliers.supplierid=purchorders.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorders','locations','purchorders.intostocklocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','purchorders','locations.loccode=purchorders.intostocklocation');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','custbranch','recurringsalesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','recurringsalesorders','custbranch.branchcode=recurringsalesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','recurringsalesorders','recurrsalesorderdetails.recurrorderno=recurringsalesorders.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','recurrsalesorderdetails','recurringsalesorders.recurrorderno=recurrsalesorderdetails.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','stockmaster','recurrsalesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','recurrsalesorderdetails','stockmaster.stockid=recurrsalesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('reportcolumns','reportheaders','reportcolumns.reportid=reportheaders.reportid');
INSERT INTO `reportlinks` VALUES ('reportheaders','reportcolumns','reportheaders.reportid=reportcolumns.reportid');
INSERT INTO `reportlinks` VALUES ('salesanalysis','periods','salesanalysis.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','salesanalysis','periods.periodno=salesanalysis.periodno');
INSERT INTO `reportlinks` VALUES ('salescatprod','stockmaster','salescatprod.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salescatprod','stockmaster.stockid=salescatprod.stockid');
INSERT INTO `reportlinks` VALUES ('salescatprod','salescat','salescatprod.salescatid=salescat.salescatid');
INSERT INTO `reportlinks` VALUES ('salescat','salescatprod','salescat.salescatid=salescatprod.salescatid');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','salesorders','salesorderdetails.orderno=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','salesorderdetails','salesorders.orderno=salesorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','stockmaster','salesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salesorderdetails','stockmaster.stockid=salesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('salesorders','custbranch','salesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','salesorders','custbranch.branchcode=salesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('salesorders','shippers','salesorders.debtorno=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','salesorders','shippers.shipper_id=salesorders.debtorno');
INSERT INTO `reportlinks` VALUES ('salesorders','locations','salesorders.fromstkloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','salesorders','locations.loccode=salesorders.fromstkloc');
INSERT INTO `reportlinks` VALUES ('securitygroups','securityroles','securitygroups.secroleid=securityroles.secroleid');
INSERT INTO `reportlinks` VALUES ('securityroles','securitygroups','securityroles.secroleid=securitygroups.secroleid');
INSERT INTO `reportlinks` VALUES ('securitygroups','securitytokens','securitygroups.tokenid=securitytokens.tokenid');
INSERT INTO `reportlinks` VALUES ('securitytokens','securitygroups','securitytokens.tokenid=securitygroups.tokenid');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','shipments','shipmentcharges.shiptref=shipments.shiptref');
INSERT INTO `reportlinks` VALUES ('shipments','shipmentcharges','shipments.shiptref=shipmentcharges.shiptref');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','systypes','shipmentcharges.transtype=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','shipmentcharges','systypes.typeid=shipmentcharges.transtype');
INSERT INTO `reportlinks` VALUES ('shipments','suppliers','shipments.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','shipments','suppliers.supplierid=shipments.supplierid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','stockmaster','stockcheckfreeze.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcheckfreeze','stockmaster.stockid=stockcheckfreeze.stockid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','locations','stockcheckfreeze.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcheckfreeze','locations.loccode=stockcheckfreeze.loccode');
INSERT INTO `reportlinks` VALUES ('stockcounts','stockmaster','stockcounts.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcounts','stockmaster.stockid=stockcounts.stockid');
INSERT INTO `reportlinks` VALUES ('stockcounts','locations','stockcounts.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcounts','locations.loccode=stockcounts.loccode');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcategory','stockmaster.categoryid=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','stockmaster','stockcategory.categoryid=stockmaster.categoryid');
INSERT INTO `reportlinks` VALUES ('stockmaster','taxcategories','stockmaster.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','stockmaster','taxcategories.taxcatid=stockmaster.taxcatid');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockmaster','stockmoves.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockmoves','stockmaster.stockid=stockmoves.stockid');
INSERT INTO `reportlinks` VALUES ('stockmoves','systypes','stockmoves.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','stockmoves','systypes.typeid=stockmoves.type');
INSERT INTO `reportlinks` VALUES ('stockmoves','locations','stockmoves.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockmoves','locations.loccode=stockmoves.loccode');
INSERT INTO `reportlinks` VALUES ('stockmoves','periods','stockmoves.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','stockmoves','periods.periodno=stockmoves.prd');
INSERT INTO `reportlinks` VALUES ('stockmovestaxes','taxauthorities','stockmovestaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','stockmovestaxes','taxauthorities.taxid=stockmovestaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockmaster','stockserialitems.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockserialitems','stockmaster.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','locations','stockserialitems.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockserialitems','locations.loccode=stockserialitems.loccode');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockmoves','stockserialmoves.stockmoveno=stockmoves.stkmoveno');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockserialmoves','stockmoves.stkmoveno=stockserialmoves.stockmoveno');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockserialitems','stockserialmoves.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockserialmoves','stockserialitems.stockid=stockserialmoves.stockid');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocfrom=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocto=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocto');
INSERT INTO `reportlinks` VALUES ('suppliercontacts','suppliers','suppliercontacts.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','suppliercontacts','suppliers.supplierid=suppliercontacts.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','currencies','suppliers.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','suppliers','currencies.currabrev=suppliers.currcode');
INSERT INTO `reportlinks` VALUES ('suppliers','paymentterms','suppliers.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','suppliers','paymentterms.termsindicator=suppliers.paymentterms');
INSERT INTO `reportlinks` VALUES ('suppliers','taxgroups','suppliers.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','suppliers','taxgroups.taxgroupid=suppliers.taxgroupid');
INSERT INTO `reportlinks` VALUES ('supptrans','systypes','supptrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','supptrans','systypes.typeid=supptrans.type');
INSERT INTO `reportlinks` VALUES ('supptrans','suppliers','supptrans.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','supptrans','suppliers.supplierid=supptrans.supplierno');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','taxauthorities','supptranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','supptranstaxes','taxauthorities.taxid=supptranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','supptrans','supptranstaxes.supptransid=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','supptranstaxes','supptrans.id=supptranstaxes.supptransid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.taxglcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.taxglcode');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.purchtaxglaccount=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.purchtaxglaccount');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxauthorities','taxauthrates.taxauthority=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxauthrates','taxauthorities.taxid=taxauthrates.taxauthority');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxcategories','taxauthrates.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','taxauthrates','taxcategories.taxcatid=taxauthrates.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxprovinces','taxauthrates.dispatchtaxprovince=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','taxauthrates','taxprovinces.taxprovinceid=taxauthrates.dispatchtaxprovince');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxgroups','taxgrouptaxes.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','taxgrouptaxes','taxgroups.taxgroupid=taxgrouptaxes.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxauthorities','taxgrouptaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxgrouptaxes','taxauthorities.taxid=taxgrouptaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('workcentres','locations','workcentres.location=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','workcentres','locations.loccode=workcentres.location');
INSERT INTO `reportlinks` VALUES ('worksorders','locations','worksorders.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','worksorders','locations.loccode=worksorders.loccode');
INSERT INTO `reportlinks` VALUES ('worksorders','stockmaster','worksorders.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','worksorders','stockmaster.stockid=worksorders.stockid');
INSERT INTO `reportlinks` VALUES ('www_users','locations','www_users.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','www_users','locations.loccode=www_users.defaultlocation');
INSERT INTO `reportlinks` VALUES ('accountgroups','accountsection','accountgroups.sectioninaccounts=accountsection.sectionid');
INSERT INTO `reportlinks` VALUES ('accountsection','accountgroups','accountsection.sectionid=accountgroups.sectioninaccounts');
INSERT INTO `reportlinks` VALUES ('bankaccounts','chartmaster','bankaccounts.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','bankaccounts','chartmaster.accountcode=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('banktrans','systypes','banktrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','banktrans','systypes.typeid=banktrans.type');
INSERT INTO `reportlinks` VALUES ('banktrans','bankaccounts','banktrans.bankact=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('bankaccounts','banktrans','bankaccounts.accountcode=banktrans.bankact');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.parent=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.parent');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.component');
INSERT INTO `reportlinks` VALUES ('bom','workcentres','bom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','bom','workcentres.code=bom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('bom','locations','bom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','bom','locations.loccode=bom.loccode');
INSERT INTO `reportlinks` VALUES ('buckets','workcentres','buckets.workcentre=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','buckets','workcentres.code=buckets.workcentre');
INSERT INTO `reportlinks` VALUES ('chartdetails','chartmaster','chartdetails.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','chartdetails','chartmaster.accountcode=chartdetails.accountcode');
INSERT INTO `reportlinks` VALUES ('chartdetails','periods','chartdetails.period=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','chartdetails','periods.periodno=chartdetails.period');
INSERT INTO `reportlinks` VALUES ('chartmaster','accountgroups','chartmaster.group_=accountgroups.groupname');
INSERT INTO `reportlinks` VALUES ('accountgroups','chartmaster','accountgroups.groupname=chartmaster.group_');
INSERT INTO `reportlinks` VALUES ('contractbom','workcentres','contractbom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','contractbom','workcentres.code=contractbom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('contractbom','locations','contractbom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','contractbom','locations.loccode=contractbom.loccode');
INSERT INTO `reportlinks` VALUES ('contractbom','stockmaster','contractbom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','contractbom','stockmaster.stockid=contractbom.component');
INSERT INTO `reportlinks` VALUES ('contractreqts','contracts','contractreqts.contract=contracts.contractref');
INSERT INTO `reportlinks` VALUES ('contracts','contractreqts','contracts.contractref=contractreqts.contract');
INSERT INTO `reportlinks` VALUES ('contracts','custbranch','contracts.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','contracts','custbranch.debtorno=contracts.debtorno');
INSERT INTO `reportlinks` VALUES ('contracts','stockcategory','contracts.branchcode=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','contracts','stockcategory.categoryid=contracts.branchcode');
INSERT INTO `reportlinks` VALUES ('contracts','salestypes','contracts.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','contracts','salestypes.typeabbrev=contracts.typeabbrev');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocfrom=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocto=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocto');
INSERT INTO `reportlinks` VALUES ('custbranch','debtorsmaster','custbranch.debtorno=debtorsmaster.debtorno');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','custbranch','debtorsmaster.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','areas','custbranch.area=areas.areacode');
INSERT INTO `reportlinks` VALUES ('areas','custbranch','areas.areacode=custbranch.area');
INSERT INTO `reportlinks` VALUES ('custbranch','salesman','custbranch.salesman=salesman.salesmancode');
INSERT INTO `reportlinks` VALUES ('salesman','custbranch','salesman.salesmancode=custbranch.salesman');
INSERT INTO `reportlinks` VALUES ('custbranch','locations','custbranch.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','custbranch','locations.loccode=custbranch.defaultlocation');
INSERT INTO `reportlinks` VALUES ('custbranch','shippers','custbranch.defaultshipvia=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','custbranch','shippers.shipper_id=custbranch.defaultshipvia');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','holdreasons','debtorsmaster.holdreason=holdreasons.reasoncode');
INSERT INTO `reportlinks` VALUES ('holdreasons','debtorsmaster','holdreasons.reasoncode=debtorsmaster.holdreason');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','currencies','debtorsmaster.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','debtorsmaster','currencies.currabrev=debtorsmaster.currcode');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','paymentterms','debtorsmaster.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','debtorsmaster','paymentterms.termsindicator=debtorsmaster.paymentterms');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','salestypes','debtorsmaster.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','debtorsmaster','salestypes.typeabbrev=debtorsmaster.salestype');
INSERT INTO `reportlinks` VALUES ('debtortrans','custbranch','debtortrans.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','debtortrans','custbranch.debtorno=debtortrans.debtorno');
INSERT INTO `reportlinks` VALUES ('debtortrans','systypes','debtortrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','debtortrans','systypes.typeid=debtortrans.type');
INSERT INTO `reportlinks` VALUES ('debtortrans','periods','debtortrans.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','debtortrans','periods.periodno=debtortrans.prd');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','taxauthorities','debtortranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','debtortranstaxes','taxauthorities.taxid=debtortranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','debtortrans','debtortranstaxes.debtortransid=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','debtortranstaxes','debtortrans.id=debtortranstaxes.debtortransid');
INSERT INTO `reportlinks` VALUES ('discountmatrix','salestypes','discountmatrix.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','discountmatrix','salestypes.typeabbrev=discountmatrix.salestype');
INSERT INTO `reportlinks` VALUES ('freightcosts','locations','freightcosts.locationfrom=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','freightcosts','locations.loccode=freightcosts.locationfrom');
INSERT INTO `reportlinks` VALUES ('freightcosts','shippers','freightcosts.shipperid=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','freightcosts','shippers.shipper_id=freightcosts.shipperid');
INSERT INTO `reportlinks` VALUES ('gltrans','chartmaster','gltrans.account=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','gltrans','chartmaster.accountcode=gltrans.account');
INSERT INTO `reportlinks` VALUES ('gltrans','systypes','gltrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','gltrans','systypes.typeid=gltrans.type');
INSERT INTO `reportlinks` VALUES ('gltrans','periods','gltrans.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','gltrans','periods.periodno=gltrans.periodno');
INSERT INTO `reportlinks` VALUES ('grns','suppliers','grns.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','grns','suppliers.supplierid=grns.supplierid');
INSERT INTO `reportlinks` VALUES ('grns','purchorderdetails','grns.podetailitem=purchorderdetails.podetailitem');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','grns','purchorderdetails.podetailitem=grns.podetailitem');
INSERT INTO `reportlinks` VALUES ('locations','taxprovinces','locations.taxprovinceid=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','locations','taxprovinces.taxprovinceid=locations.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('locstock','locations','locstock.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','locstock','locations.loccode=locstock.loccode');
INSERT INTO `reportlinks` VALUES ('locstock','stockmaster','locstock.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','locstock','stockmaster.stockid=locstock.stockid');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.shiploc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.shiploc');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.recloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.recloc');
INSERT INTO `reportlinks` VALUES ('loctransfers','stockmaster','loctransfers.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','loctransfers','stockmaster.stockid=loctransfers.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','stockmaster','orderdeliverydifferenceslog.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','orderdeliverydifferencesl','stockmaster.stockid=orderdeliverydifferenceslog.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','custbranch','orderdeliverydifferenceslog.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','orderdeliverydifferencesl','custbranch.debtorno=orderdeliverydifferenceslog.debtorno');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','salesorders','orderdeliverydifferenceslog.branchcode=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','orderdeliverydifferencesl','salesorders.orderno=orderdeliverydifferenceslog.branchcode');
INSERT INTO `reportlinks` VALUES ('prices','stockmaster','prices.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','prices','stockmaster.stockid=prices.stockid');
INSERT INTO `reportlinks` VALUES ('prices','currencies','prices.currabrev=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','prices','currencies.currabrev=prices.currabrev');
INSERT INTO `reportlinks` VALUES ('prices','salestypes','prices.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','prices','salestypes.typeabbrev=prices.typeabbrev');
INSERT INTO `reportlinks` VALUES ('purchdata','stockmaster','purchdata.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','purchdata','stockmaster.stockid=purchdata.stockid');
INSERT INTO `reportlinks` VALUES ('purchdata','suppliers','purchdata.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchdata','suppliers.supplierid=purchdata.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','purchorders','purchorderdetails.orderno=purchorders.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','purchorderdetails','purchorders.orderno=purchorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','suppliers','purchorders.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchorders','suppliers.supplierid=purchorders.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorders','locations','purchorders.intostocklocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','purchorders','locations.loccode=purchorders.intostocklocation');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','custbranch','recurringsalesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','recurringsalesorders','custbranch.branchcode=recurringsalesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','recurringsalesorders','recurrsalesorderdetails.recurrorderno=recurringsalesorders.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','recurrsalesorderdetails','recurringsalesorders.recurrorderno=recurrsalesorderdetails.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','stockmaster','recurrsalesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','recurrsalesorderdetails','stockmaster.stockid=recurrsalesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('reportcolumns','reportheaders','reportcolumns.reportid=reportheaders.reportid');
INSERT INTO `reportlinks` VALUES ('reportheaders','reportcolumns','reportheaders.reportid=reportcolumns.reportid');
INSERT INTO `reportlinks` VALUES ('salesanalysis','periods','salesanalysis.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','salesanalysis','periods.periodno=salesanalysis.periodno');
INSERT INTO `reportlinks` VALUES ('salescatprod','stockmaster','salescatprod.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salescatprod','stockmaster.stockid=salescatprod.stockid');
INSERT INTO `reportlinks` VALUES ('salescatprod','salescat','salescatprod.salescatid=salescat.salescatid');
INSERT INTO `reportlinks` VALUES ('salescat','salescatprod','salescat.salescatid=salescatprod.salescatid');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','salesorders','salesorderdetails.orderno=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','salesorderdetails','salesorders.orderno=salesorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','stockmaster','salesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salesorderdetails','stockmaster.stockid=salesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('salesorders','custbranch','salesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','salesorders','custbranch.branchcode=salesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('salesorders','shippers','salesorders.debtorno=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','salesorders','shippers.shipper_id=salesorders.debtorno');
INSERT INTO `reportlinks` VALUES ('salesorders','locations','salesorders.fromstkloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','salesorders','locations.loccode=salesorders.fromstkloc');
INSERT INTO `reportlinks` VALUES ('securitygroups','securityroles','securitygroups.secroleid=securityroles.secroleid');
INSERT INTO `reportlinks` VALUES ('securityroles','securitygroups','securityroles.secroleid=securitygroups.secroleid');
INSERT INTO `reportlinks` VALUES ('securitygroups','securitytokens','securitygroups.tokenid=securitytokens.tokenid');
INSERT INTO `reportlinks` VALUES ('securitytokens','securitygroups','securitytokens.tokenid=securitygroups.tokenid');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','shipments','shipmentcharges.shiptref=shipments.shiptref');
INSERT INTO `reportlinks` VALUES ('shipments','shipmentcharges','shipments.shiptref=shipmentcharges.shiptref');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','systypes','shipmentcharges.transtype=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','shipmentcharges','systypes.typeid=shipmentcharges.transtype');
INSERT INTO `reportlinks` VALUES ('shipments','suppliers','shipments.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','shipments','suppliers.supplierid=shipments.supplierid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','stockmaster','stockcheckfreeze.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcheckfreeze','stockmaster.stockid=stockcheckfreeze.stockid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','locations','stockcheckfreeze.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcheckfreeze','locations.loccode=stockcheckfreeze.loccode');
INSERT INTO `reportlinks` VALUES ('stockcounts','stockmaster','stockcounts.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcounts','stockmaster.stockid=stockcounts.stockid');
INSERT INTO `reportlinks` VALUES ('stockcounts','locations','stockcounts.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcounts','locations.loccode=stockcounts.loccode');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcategory','stockmaster.categoryid=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','stockmaster','stockcategory.categoryid=stockmaster.categoryid');
INSERT INTO `reportlinks` VALUES ('stockmaster','taxcategories','stockmaster.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','stockmaster','taxcategories.taxcatid=stockmaster.taxcatid');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockmaster','stockmoves.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockmoves','stockmaster.stockid=stockmoves.stockid');
INSERT INTO `reportlinks` VALUES ('stockmoves','systypes','stockmoves.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','stockmoves','systypes.typeid=stockmoves.type');
INSERT INTO `reportlinks` VALUES ('stockmoves','locations','stockmoves.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockmoves','locations.loccode=stockmoves.loccode');
INSERT INTO `reportlinks` VALUES ('stockmoves','periods','stockmoves.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','stockmoves','periods.periodno=stockmoves.prd');
INSERT INTO `reportlinks` VALUES ('stockmovestaxes','taxauthorities','stockmovestaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','stockmovestaxes','taxauthorities.taxid=stockmovestaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockmaster','stockserialitems.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockserialitems','stockmaster.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','locations','stockserialitems.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockserialitems','locations.loccode=stockserialitems.loccode');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockmoves','stockserialmoves.stockmoveno=stockmoves.stkmoveno');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockserialmoves','stockmoves.stkmoveno=stockserialmoves.stockmoveno');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockserialitems','stockserialmoves.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockserialmoves','stockserialitems.stockid=stockserialmoves.stockid');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocfrom=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocto=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocto');
INSERT INTO `reportlinks` VALUES ('suppliercontacts','suppliers','suppliercontacts.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','suppliercontacts','suppliers.supplierid=suppliercontacts.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','currencies','suppliers.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','suppliers','currencies.currabrev=suppliers.currcode');
INSERT INTO `reportlinks` VALUES ('suppliers','paymentterms','suppliers.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','suppliers','paymentterms.termsindicator=suppliers.paymentterms');
INSERT INTO `reportlinks` VALUES ('suppliers','taxgroups','suppliers.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','suppliers','taxgroups.taxgroupid=suppliers.taxgroupid');
INSERT INTO `reportlinks` VALUES ('supptrans','systypes','supptrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','supptrans','systypes.typeid=supptrans.type');
INSERT INTO `reportlinks` VALUES ('supptrans','suppliers','supptrans.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','supptrans','suppliers.supplierid=supptrans.supplierno');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','taxauthorities','supptranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','supptranstaxes','taxauthorities.taxid=supptranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','supptrans','supptranstaxes.supptransid=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','supptranstaxes','supptrans.id=supptranstaxes.supptransid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.taxglcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.taxglcode');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.purchtaxglaccount=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.purchtaxglaccount');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxauthorities','taxauthrates.taxauthority=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxauthrates','taxauthorities.taxid=taxauthrates.taxauthority');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxcategories','taxauthrates.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','taxauthrates','taxcategories.taxcatid=taxauthrates.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxprovinces','taxauthrates.dispatchtaxprovince=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','taxauthrates','taxprovinces.taxprovinceid=taxauthrates.dispatchtaxprovince');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxgroups','taxgrouptaxes.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','taxgrouptaxes','taxgroups.taxgroupid=taxgrouptaxes.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxauthorities','taxgrouptaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxgrouptaxes','taxauthorities.taxid=taxgrouptaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('workcentres','locations','workcentres.location=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','workcentres','locations.loccode=workcentres.location');
INSERT INTO `reportlinks` VALUES ('worksorders','locations','worksorders.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','worksorders','locations.loccode=worksorders.loccode');
INSERT INTO `reportlinks` VALUES ('worksorders','stockmaster','worksorders.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','worksorders','stockmaster.stockid=worksorders.stockid');
INSERT INTO `reportlinks` VALUES ('www_users','locations','www_users.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','www_users','locations.loccode=www_users.defaultlocation');
INSERT INTO `reportlinks` VALUES ('accountgroups','accountsection','accountgroups.sectioninaccounts=accountsection.sectionid');
INSERT INTO `reportlinks` VALUES ('accountsection','accountgroups','accountsection.sectionid=accountgroups.sectioninaccounts');
INSERT INTO `reportlinks` VALUES ('bankaccounts','chartmaster','bankaccounts.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','bankaccounts','chartmaster.accountcode=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('banktrans','systypes','banktrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','banktrans','systypes.typeid=banktrans.type');
INSERT INTO `reportlinks` VALUES ('banktrans','bankaccounts','banktrans.bankact=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('bankaccounts','banktrans','bankaccounts.accountcode=banktrans.bankact');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.parent=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.parent');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.component');
INSERT INTO `reportlinks` VALUES ('bom','workcentres','bom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','bom','workcentres.code=bom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('bom','locations','bom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','bom','locations.loccode=bom.loccode');
INSERT INTO `reportlinks` VALUES ('buckets','workcentres','buckets.workcentre=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','buckets','workcentres.code=buckets.workcentre');
INSERT INTO `reportlinks` VALUES ('chartdetails','chartmaster','chartdetails.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','chartdetails','chartmaster.accountcode=chartdetails.accountcode');
INSERT INTO `reportlinks` VALUES ('chartdetails','periods','chartdetails.period=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','chartdetails','periods.periodno=chartdetails.period');
INSERT INTO `reportlinks` VALUES ('chartmaster','accountgroups','chartmaster.group_=accountgroups.groupname');
INSERT INTO `reportlinks` VALUES ('accountgroups','chartmaster','accountgroups.groupname=chartmaster.group_');
INSERT INTO `reportlinks` VALUES ('contractbom','workcentres','contractbom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','contractbom','workcentres.code=contractbom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('contractbom','locations','contractbom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','contractbom','locations.loccode=contractbom.loccode');
INSERT INTO `reportlinks` VALUES ('contractbom','stockmaster','contractbom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','contractbom','stockmaster.stockid=contractbom.component');
INSERT INTO `reportlinks` VALUES ('contractreqts','contracts','contractreqts.contract=contracts.contractref');
INSERT INTO `reportlinks` VALUES ('contracts','contractreqts','contracts.contractref=contractreqts.contract');
INSERT INTO `reportlinks` VALUES ('contracts','custbranch','contracts.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','contracts','custbranch.debtorno=contracts.debtorno');
INSERT INTO `reportlinks` VALUES ('contracts','stockcategory','contracts.branchcode=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','contracts','stockcategory.categoryid=contracts.branchcode');
INSERT INTO `reportlinks` VALUES ('contracts','salestypes','contracts.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','contracts','salestypes.typeabbrev=contracts.typeabbrev');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocfrom=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocto=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocto');
INSERT INTO `reportlinks` VALUES ('custbranch','debtorsmaster','custbranch.debtorno=debtorsmaster.debtorno');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','custbranch','debtorsmaster.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','areas','custbranch.area=areas.areacode');
INSERT INTO `reportlinks` VALUES ('areas','custbranch','areas.areacode=custbranch.area');
INSERT INTO `reportlinks` VALUES ('custbranch','salesman','custbranch.salesman=salesman.salesmancode');
INSERT INTO `reportlinks` VALUES ('salesman','custbranch','salesman.salesmancode=custbranch.salesman');
INSERT INTO `reportlinks` VALUES ('custbranch','locations','custbranch.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','custbranch','locations.loccode=custbranch.defaultlocation');
INSERT INTO `reportlinks` VALUES ('custbranch','shippers','custbranch.defaultshipvia=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','custbranch','shippers.shipper_id=custbranch.defaultshipvia');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','holdreasons','debtorsmaster.holdreason=holdreasons.reasoncode');
INSERT INTO `reportlinks` VALUES ('holdreasons','debtorsmaster','holdreasons.reasoncode=debtorsmaster.holdreason');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','currencies','debtorsmaster.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','debtorsmaster','currencies.currabrev=debtorsmaster.currcode');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','paymentterms','debtorsmaster.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','debtorsmaster','paymentterms.termsindicator=debtorsmaster.paymentterms');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','salestypes','debtorsmaster.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','debtorsmaster','salestypes.typeabbrev=debtorsmaster.salestype');
INSERT INTO `reportlinks` VALUES ('debtortrans','custbranch','debtortrans.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','debtortrans','custbranch.debtorno=debtortrans.debtorno');
INSERT INTO `reportlinks` VALUES ('debtortrans','systypes','debtortrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','debtortrans','systypes.typeid=debtortrans.type');
INSERT INTO `reportlinks` VALUES ('debtortrans','periods','debtortrans.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','debtortrans','periods.periodno=debtortrans.prd');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','taxauthorities','debtortranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','debtortranstaxes','taxauthorities.taxid=debtortranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','debtortrans','debtortranstaxes.debtortransid=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','debtortranstaxes','debtortrans.id=debtortranstaxes.debtortransid');
INSERT INTO `reportlinks` VALUES ('discountmatrix','salestypes','discountmatrix.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','discountmatrix','salestypes.typeabbrev=discountmatrix.salestype');
INSERT INTO `reportlinks` VALUES ('freightcosts','locations','freightcosts.locationfrom=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','freightcosts','locations.loccode=freightcosts.locationfrom');
INSERT INTO `reportlinks` VALUES ('freightcosts','shippers','freightcosts.shipperid=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','freightcosts','shippers.shipper_id=freightcosts.shipperid');
INSERT INTO `reportlinks` VALUES ('gltrans','chartmaster','gltrans.account=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','gltrans','chartmaster.accountcode=gltrans.account');
INSERT INTO `reportlinks` VALUES ('gltrans','systypes','gltrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','gltrans','systypes.typeid=gltrans.type');
INSERT INTO `reportlinks` VALUES ('gltrans','periods','gltrans.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','gltrans','periods.periodno=gltrans.periodno');
INSERT INTO `reportlinks` VALUES ('grns','suppliers','grns.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','grns','suppliers.supplierid=grns.supplierid');
INSERT INTO `reportlinks` VALUES ('grns','purchorderdetails','grns.podetailitem=purchorderdetails.podetailitem');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','grns','purchorderdetails.podetailitem=grns.podetailitem');
INSERT INTO `reportlinks` VALUES ('locations','taxprovinces','locations.taxprovinceid=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','locations','taxprovinces.taxprovinceid=locations.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('locstock','locations','locstock.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','locstock','locations.loccode=locstock.loccode');
INSERT INTO `reportlinks` VALUES ('locstock','stockmaster','locstock.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','locstock','stockmaster.stockid=locstock.stockid');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.shiploc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.shiploc');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.recloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.recloc');
INSERT INTO `reportlinks` VALUES ('loctransfers','stockmaster','loctransfers.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','loctransfers','stockmaster.stockid=loctransfers.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','stockmaster','orderdeliverydifferenceslog.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','orderdeliverydifferencesl','stockmaster.stockid=orderdeliverydifferenceslog.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','custbranch','orderdeliverydifferenceslog.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','orderdeliverydifferencesl','custbranch.debtorno=orderdeliverydifferenceslog.debtorno');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','salesorders','orderdeliverydifferenceslog.branchcode=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','orderdeliverydifferencesl','salesorders.orderno=orderdeliverydifferenceslog.branchcode');
INSERT INTO `reportlinks` VALUES ('prices','stockmaster','prices.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','prices','stockmaster.stockid=prices.stockid');
INSERT INTO `reportlinks` VALUES ('prices','currencies','prices.currabrev=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','prices','currencies.currabrev=prices.currabrev');
INSERT INTO `reportlinks` VALUES ('prices','salestypes','prices.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','prices','salestypes.typeabbrev=prices.typeabbrev');
INSERT INTO `reportlinks` VALUES ('purchdata','stockmaster','purchdata.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','purchdata','stockmaster.stockid=purchdata.stockid');
INSERT INTO `reportlinks` VALUES ('purchdata','suppliers','purchdata.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchdata','suppliers.supplierid=purchdata.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','purchorders','purchorderdetails.orderno=purchorders.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','purchorderdetails','purchorders.orderno=purchorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','suppliers','purchorders.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchorders','suppliers.supplierid=purchorders.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorders','locations','purchorders.intostocklocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','purchorders','locations.loccode=purchorders.intostocklocation');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','custbranch','recurringsalesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','recurringsalesorders','custbranch.branchcode=recurringsalesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','recurringsalesorders','recurrsalesorderdetails.recurrorderno=recurringsalesorders.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','recurrsalesorderdetails','recurringsalesorders.recurrorderno=recurrsalesorderdetails.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','stockmaster','recurrsalesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','recurrsalesorderdetails','stockmaster.stockid=recurrsalesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('reportcolumns','reportheaders','reportcolumns.reportid=reportheaders.reportid');
INSERT INTO `reportlinks` VALUES ('reportheaders','reportcolumns','reportheaders.reportid=reportcolumns.reportid');
INSERT INTO `reportlinks` VALUES ('salesanalysis','periods','salesanalysis.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','salesanalysis','periods.periodno=salesanalysis.periodno');
INSERT INTO `reportlinks` VALUES ('salescatprod','stockmaster','salescatprod.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salescatprod','stockmaster.stockid=salescatprod.stockid');
INSERT INTO `reportlinks` VALUES ('salescatprod','salescat','salescatprod.salescatid=salescat.salescatid');
INSERT INTO `reportlinks` VALUES ('salescat','salescatprod','salescat.salescatid=salescatprod.salescatid');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','salesorders','salesorderdetails.orderno=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','salesorderdetails','salesorders.orderno=salesorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','stockmaster','salesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salesorderdetails','stockmaster.stockid=salesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('salesorders','custbranch','salesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','salesorders','custbranch.branchcode=salesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('salesorders','shippers','salesorders.debtorno=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','salesorders','shippers.shipper_id=salesorders.debtorno');
INSERT INTO `reportlinks` VALUES ('salesorders','locations','salesorders.fromstkloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','salesorders','locations.loccode=salesorders.fromstkloc');
INSERT INTO `reportlinks` VALUES ('securitygroups','securityroles','securitygroups.secroleid=securityroles.secroleid');
INSERT INTO `reportlinks` VALUES ('securityroles','securitygroups','securityroles.secroleid=securitygroups.secroleid');
INSERT INTO `reportlinks` VALUES ('securitygroups','securitytokens','securitygroups.tokenid=securitytokens.tokenid');
INSERT INTO `reportlinks` VALUES ('securitytokens','securitygroups','securitytokens.tokenid=securitygroups.tokenid');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','shipments','shipmentcharges.shiptref=shipments.shiptref');
INSERT INTO `reportlinks` VALUES ('shipments','shipmentcharges','shipments.shiptref=shipmentcharges.shiptref');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','systypes','shipmentcharges.transtype=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','shipmentcharges','systypes.typeid=shipmentcharges.transtype');
INSERT INTO `reportlinks` VALUES ('shipments','suppliers','shipments.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','shipments','suppliers.supplierid=shipments.supplierid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','stockmaster','stockcheckfreeze.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcheckfreeze','stockmaster.stockid=stockcheckfreeze.stockid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','locations','stockcheckfreeze.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcheckfreeze','locations.loccode=stockcheckfreeze.loccode');
INSERT INTO `reportlinks` VALUES ('stockcounts','stockmaster','stockcounts.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcounts','stockmaster.stockid=stockcounts.stockid');
INSERT INTO `reportlinks` VALUES ('stockcounts','locations','stockcounts.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcounts','locations.loccode=stockcounts.loccode');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcategory','stockmaster.categoryid=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','stockmaster','stockcategory.categoryid=stockmaster.categoryid');
INSERT INTO `reportlinks` VALUES ('stockmaster','taxcategories','stockmaster.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','stockmaster','taxcategories.taxcatid=stockmaster.taxcatid');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockmaster','stockmoves.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockmoves','stockmaster.stockid=stockmoves.stockid');
INSERT INTO `reportlinks` VALUES ('stockmoves','systypes','stockmoves.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','stockmoves','systypes.typeid=stockmoves.type');
INSERT INTO `reportlinks` VALUES ('stockmoves','locations','stockmoves.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockmoves','locations.loccode=stockmoves.loccode');
INSERT INTO `reportlinks` VALUES ('stockmoves','periods','stockmoves.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','stockmoves','periods.periodno=stockmoves.prd');
INSERT INTO `reportlinks` VALUES ('stockmovestaxes','taxauthorities','stockmovestaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','stockmovestaxes','taxauthorities.taxid=stockmovestaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockmaster','stockserialitems.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockserialitems','stockmaster.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','locations','stockserialitems.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockserialitems','locations.loccode=stockserialitems.loccode');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockmoves','stockserialmoves.stockmoveno=stockmoves.stkmoveno');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockserialmoves','stockmoves.stkmoveno=stockserialmoves.stockmoveno');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockserialitems','stockserialmoves.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockserialmoves','stockserialitems.stockid=stockserialmoves.stockid');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocfrom=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocto=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocto');
INSERT INTO `reportlinks` VALUES ('suppliercontacts','suppliers','suppliercontacts.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','suppliercontacts','suppliers.supplierid=suppliercontacts.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','currencies','suppliers.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','suppliers','currencies.currabrev=suppliers.currcode');
INSERT INTO `reportlinks` VALUES ('suppliers','paymentterms','suppliers.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','suppliers','paymentterms.termsindicator=suppliers.paymentterms');
INSERT INTO `reportlinks` VALUES ('suppliers','taxgroups','suppliers.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','suppliers','taxgroups.taxgroupid=suppliers.taxgroupid');
INSERT INTO `reportlinks` VALUES ('supptrans','systypes','supptrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','supptrans','systypes.typeid=supptrans.type');
INSERT INTO `reportlinks` VALUES ('supptrans','suppliers','supptrans.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','supptrans','suppliers.supplierid=supptrans.supplierno');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','taxauthorities','supptranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','supptranstaxes','taxauthorities.taxid=supptranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','supptrans','supptranstaxes.supptransid=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','supptranstaxes','supptrans.id=supptranstaxes.supptransid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.taxglcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.taxglcode');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.purchtaxglaccount=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.purchtaxglaccount');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxauthorities','taxauthrates.taxauthority=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxauthrates','taxauthorities.taxid=taxauthrates.taxauthority');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxcategories','taxauthrates.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','taxauthrates','taxcategories.taxcatid=taxauthrates.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxprovinces','taxauthrates.dispatchtaxprovince=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','taxauthrates','taxprovinces.taxprovinceid=taxauthrates.dispatchtaxprovince');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxgroups','taxgrouptaxes.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','taxgrouptaxes','taxgroups.taxgroupid=taxgrouptaxes.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxauthorities','taxgrouptaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxgrouptaxes','taxauthorities.taxid=taxgrouptaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('workcentres','locations','workcentres.location=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','workcentres','locations.loccode=workcentres.location');
INSERT INTO `reportlinks` VALUES ('worksorders','locations','worksorders.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','worksorders','locations.loccode=worksorders.loccode');
INSERT INTO `reportlinks` VALUES ('worksorders','stockmaster','worksorders.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','worksorders','stockmaster.stockid=worksorders.stockid');
INSERT INTO `reportlinks` VALUES ('www_users','locations','www_users.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','www_users','locations.loccode=www_users.defaultlocation');
INSERT INTO `reportlinks` VALUES ('accountgroups','accountsection','accountgroups.sectioninaccounts=accountsection.sectionid');
INSERT INTO `reportlinks` VALUES ('accountsection','accountgroups','accountsection.sectionid=accountgroups.sectioninaccounts');
INSERT INTO `reportlinks` VALUES ('bankaccounts','chartmaster','bankaccounts.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','bankaccounts','chartmaster.accountcode=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('banktrans','systypes','banktrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','banktrans','systypes.typeid=banktrans.type');
INSERT INTO `reportlinks` VALUES ('banktrans','bankaccounts','banktrans.bankact=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('bankaccounts','banktrans','bankaccounts.accountcode=banktrans.bankact');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.parent=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.parent');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.component');
INSERT INTO `reportlinks` VALUES ('bom','workcentres','bom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','bom','workcentres.code=bom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('bom','locations','bom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','bom','locations.loccode=bom.loccode');
INSERT INTO `reportlinks` VALUES ('buckets','workcentres','buckets.workcentre=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','buckets','workcentres.code=buckets.workcentre');
INSERT INTO `reportlinks` VALUES ('chartdetails','chartmaster','chartdetails.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','chartdetails','chartmaster.accountcode=chartdetails.accountcode');
INSERT INTO `reportlinks` VALUES ('chartdetails','periods','chartdetails.period=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','chartdetails','periods.periodno=chartdetails.period');
INSERT INTO `reportlinks` VALUES ('chartmaster','accountgroups','chartmaster.group_=accountgroups.groupname');
INSERT INTO `reportlinks` VALUES ('accountgroups','chartmaster','accountgroups.groupname=chartmaster.group_');
INSERT INTO `reportlinks` VALUES ('contractbom','workcentres','contractbom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','contractbom','workcentres.code=contractbom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('contractbom','locations','contractbom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','contractbom','locations.loccode=contractbom.loccode');
INSERT INTO `reportlinks` VALUES ('contractbom','stockmaster','contractbom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','contractbom','stockmaster.stockid=contractbom.component');
INSERT INTO `reportlinks` VALUES ('contractreqts','contracts','contractreqts.contract=contracts.contractref');
INSERT INTO `reportlinks` VALUES ('contracts','contractreqts','contracts.contractref=contractreqts.contract');
INSERT INTO `reportlinks` VALUES ('contracts','custbranch','contracts.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','contracts','custbranch.debtorno=contracts.debtorno');
INSERT INTO `reportlinks` VALUES ('contracts','stockcategory','contracts.branchcode=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','contracts','stockcategory.categoryid=contracts.branchcode');
INSERT INTO `reportlinks` VALUES ('contracts','salestypes','contracts.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','contracts','salestypes.typeabbrev=contracts.typeabbrev');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocfrom=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocto=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocto');
INSERT INTO `reportlinks` VALUES ('custbranch','debtorsmaster','custbranch.debtorno=debtorsmaster.debtorno');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','custbranch','debtorsmaster.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','areas','custbranch.area=areas.areacode');
INSERT INTO `reportlinks` VALUES ('areas','custbranch','areas.areacode=custbranch.area');
INSERT INTO `reportlinks` VALUES ('custbranch','salesman','custbranch.salesman=salesman.salesmancode');
INSERT INTO `reportlinks` VALUES ('salesman','custbranch','salesman.salesmancode=custbranch.salesman');
INSERT INTO `reportlinks` VALUES ('custbranch','locations','custbranch.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','custbranch','locations.loccode=custbranch.defaultlocation');
INSERT INTO `reportlinks` VALUES ('custbranch','shippers','custbranch.defaultshipvia=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','custbranch','shippers.shipper_id=custbranch.defaultshipvia');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','holdreasons','debtorsmaster.holdreason=holdreasons.reasoncode');
INSERT INTO `reportlinks` VALUES ('holdreasons','debtorsmaster','holdreasons.reasoncode=debtorsmaster.holdreason');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','currencies','debtorsmaster.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','debtorsmaster','currencies.currabrev=debtorsmaster.currcode');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','paymentterms','debtorsmaster.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','debtorsmaster','paymentterms.termsindicator=debtorsmaster.paymentterms');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','salestypes','debtorsmaster.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','debtorsmaster','salestypes.typeabbrev=debtorsmaster.salestype');
INSERT INTO `reportlinks` VALUES ('debtortrans','custbranch','debtortrans.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','debtortrans','custbranch.debtorno=debtortrans.debtorno');
INSERT INTO `reportlinks` VALUES ('debtortrans','systypes','debtortrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','debtortrans','systypes.typeid=debtortrans.type');
INSERT INTO `reportlinks` VALUES ('debtortrans','periods','debtortrans.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','debtortrans','periods.periodno=debtortrans.prd');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','taxauthorities','debtortranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','debtortranstaxes','taxauthorities.taxid=debtortranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','debtortrans','debtortranstaxes.debtortransid=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','debtortranstaxes','debtortrans.id=debtortranstaxes.debtortransid');
INSERT INTO `reportlinks` VALUES ('discountmatrix','salestypes','discountmatrix.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','discountmatrix','salestypes.typeabbrev=discountmatrix.salestype');
INSERT INTO `reportlinks` VALUES ('freightcosts','locations','freightcosts.locationfrom=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','freightcosts','locations.loccode=freightcosts.locationfrom');
INSERT INTO `reportlinks` VALUES ('freightcosts','shippers','freightcosts.shipperid=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','freightcosts','shippers.shipper_id=freightcosts.shipperid');
INSERT INTO `reportlinks` VALUES ('gltrans','chartmaster','gltrans.account=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','gltrans','chartmaster.accountcode=gltrans.account');
INSERT INTO `reportlinks` VALUES ('gltrans','systypes','gltrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','gltrans','systypes.typeid=gltrans.type');
INSERT INTO `reportlinks` VALUES ('gltrans','periods','gltrans.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','gltrans','periods.periodno=gltrans.periodno');
INSERT INTO `reportlinks` VALUES ('grns','suppliers','grns.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','grns','suppliers.supplierid=grns.supplierid');
INSERT INTO `reportlinks` VALUES ('grns','purchorderdetails','grns.podetailitem=purchorderdetails.podetailitem');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','grns','purchorderdetails.podetailitem=grns.podetailitem');
INSERT INTO `reportlinks` VALUES ('locations','taxprovinces','locations.taxprovinceid=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','locations','taxprovinces.taxprovinceid=locations.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('locstock','locations','locstock.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','locstock','locations.loccode=locstock.loccode');
INSERT INTO `reportlinks` VALUES ('locstock','stockmaster','locstock.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','locstock','stockmaster.stockid=locstock.stockid');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.shiploc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.shiploc');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.recloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.recloc');
INSERT INTO `reportlinks` VALUES ('loctransfers','stockmaster','loctransfers.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','loctransfers','stockmaster.stockid=loctransfers.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','stockmaster','orderdeliverydifferenceslog.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','orderdeliverydifferencesl','stockmaster.stockid=orderdeliverydifferenceslog.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','custbranch','orderdeliverydifferenceslog.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','orderdeliverydifferencesl','custbranch.debtorno=orderdeliverydifferenceslog.debtorno');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','salesorders','orderdeliverydifferenceslog.branchcode=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','orderdeliverydifferencesl','salesorders.orderno=orderdeliverydifferenceslog.branchcode');
INSERT INTO `reportlinks` VALUES ('prices','stockmaster','prices.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','prices','stockmaster.stockid=prices.stockid');
INSERT INTO `reportlinks` VALUES ('prices','currencies','prices.currabrev=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','prices','currencies.currabrev=prices.currabrev');
INSERT INTO `reportlinks` VALUES ('prices','salestypes','prices.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','prices','salestypes.typeabbrev=prices.typeabbrev');
INSERT INTO `reportlinks` VALUES ('purchdata','stockmaster','purchdata.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','purchdata','stockmaster.stockid=purchdata.stockid');
INSERT INTO `reportlinks` VALUES ('purchdata','suppliers','purchdata.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchdata','suppliers.supplierid=purchdata.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','purchorders','purchorderdetails.orderno=purchorders.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','purchorderdetails','purchorders.orderno=purchorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','suppliers','purchorders.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchorders','suppliers.supplierid=purchorders.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorders','locations','purchorders.intostocklocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','purchorders','locations.loccode=purchorders.intostocklocation');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','custbranch','recurringsalesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','recurringsalesorders','custbranch.branchcode=recurringsalesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','recurringsalesorders','recurrsalesorderdetails.recurrorderno=recurringsalesorders.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','recurrsalesorderdetails','recurringsalesorders.recurrorderno=recurrsalesorderdetails.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','stockmaster','recurrsalesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','recurrsalesorderdetails','stockmaster.stockid=recurrsalesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('reportcolumns','reportheaders','reportcolumns.reportid=reportheaders.reportid');
INSERT INTO `reportlinks` VALUES ('reportheaders','reportcolumns','reportheaders.reportid=reportcolumns.reportid');
INSERT INTO `reportlinks` VALUES ('salesanalysis','periods','salesanalysis.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','salesanalysis','periods.periodno=salesanalysis.periodno');
INSERT INTO `reportlinks` VALUES ('salescatprod','stockmaster','salescatprod.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salescatprod','stockmaster.stockid=salescatprod.stockid');
INSERT INTO `reportlinks` VALUES ('salescatprod','salescat','salescatprod.salescatid=salescat.salescatid');
INSERT INTO `reportlinks` VALUES ('salescat','salescatprod','salescat.salescatid=salescatprod.salescatid');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','salesorders','salesorderdetails.orderno=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','salesorderdetails','salesorders.orderno=salesorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','stockmaster','salesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salesorderdetails','stockmaster.stockid=salesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('salesorders','custbranch','salesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','salesorders','custbranch.branchcode=salesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('salesorders','shippers','salesorders.debtorno=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','salesorders','shippers.shipper_id=salesorders.debtorno');
INSERT INTO `reportlinks` VALUES ('salesorders','locations','salesorders.fromstkloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','salesorders','locations.loccode=salesorders.fromstkloc');
INSERT INTO `reportlinks` VALUES ('securitygroups','securityroles','securitygroups.secroleid=securityroles.secroleid');
INSERT INTO `reportlinks` VALUES ('securityroles','securitygroups','securityroles.secroleid=securitygroups.secroleid');
INSERT INTO `reportlinks` VALUES ('securitygroups','securitytokens','securitygroups.tokenid=securitytokens.tokenid');
INSERT INTO `reportlinks` VALUES ('securitytokens','securitygroups','securitytokens.tokenid=securitygroups.tokenid');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','shipments','shipmentcharges.shiptref=shipments.shiptref');
INSERT INTO `reportlinks` VALUES ('shipments','shipmentcharges','shipments.shiptref=shipmentcharges.shiptref');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','systypes','shipmentcharges.transtype=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','shipmentcharges','systypes.typeid=shipmentcharges.transtype');
INSERT INTO `reportlinks` VALUES ('shipments','suppliers','shipments.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','shipments','suppliers.supplierid=shipments.supplierid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','stockmaster','stockcheckfreeze.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcheckfreeze','stockmaster.stockid=stockcheckfreeze.stockid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','locations','stockcheckfreeze.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcheckfreeze','locations.loccode=stockcheckfreeze.loccode');
INSERT INTO `reportlinks` VALUES ('stockcounts','stockmaster','stockcounts.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcounts','stockmaster.stockid=stockcounts.stockid');
INSERT INTO `reportlinks` VALUES ('stockcounts','locations','stockcounts.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcounts','locations.loccode=stockcounts.loccode');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcategory','stockmaster.categoryid=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','stockmaster','stockcategory.categoryid=stockmaster.categoryid');
INSERT INTO `reportlinks` VALUES ('stockmaster','taxcategories','stockmaster.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','stockmaster','taxcategories.taxcatid=stockmaster.taxcatid');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockmaster','stockmoves.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockmoves','stockmaster.stockid=stockmoves.stockid');
INSERT INTO `reportlinks` VALUES ('stockmoves','systypes','stockmoves.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','stockmoves','systypes.typeid=stockmoves.type');
INSERT INTO `reportlinks` VALUES ('stockmoves','locations','stockmoves.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockmoves','locations.loccode=stockmoves.loccode');
INSERT INTO `reportlinks` VALUES ('stockmoves','periods','stockmoves.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','stockmoves','periods.periodno=stockmoves.prd');
INSERT INTO `reportlinks` VALUES ('stockmovestaxes','taxauthorities','stockmovestaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','stockmovestaxes','taxauthorities.taxid=stockmovestaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockmaster','stockserialitems.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockserialitems','stockmaster.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','locations','stockserialitems.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockserialitems','locations.loccode=stockserialitems.loccode');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockmoves','stockserialmoves.stockmoveno=stockmoves.stkmoveno');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockserialmoves','stockmoves.stkmoveno=stockserialmoves.stockmoveno');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockserialitems','stockserialmoves.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockserialmoves','stockserialitems.stockid=stockserialmoves.stockid');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocfrom=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocto=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocto');
INSERT INTO `reportlinks` VALUES ('suppliercontacts','suppliers','suppliercontacts.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','suppliercontacts','suppliers.supplierid=suppliercontacts.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','currencies','suppliers.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','suppliers','currencies.currabrev=suppliers.currcode');
INSERT INTO `reportlinks` VALUES ('suppliers','paymentterms','suppliers.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','suppliers','paymentterms.termsindicator=suppliers.paymentterms');
INSERT INTO `reportlinks` VALUES ('suppliers','taxgroups','suppliers.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','suppliers','taxgroups.taxgroupid=suppliers.taxgroupid');
INSERT INTO `reportlinks` VALUES ('supptrans','systypes','supptrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','supptrans','systypes.typeid=supptrans.type');
INSERT INTO `reportlinks` VALUES ('supptrans','suppliers','supptrans.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','supptrans','suppliers.supplierid=supptrans.supplierno');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','taxauthorities','supptranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','supptranstaxes','taxauthorities.taxid=supptranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','supptrans','supptranstaxes.supptransid=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','supptranstaxes','supptrans.id=supptranstaxes.supptransid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.taxglcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.taxglcode');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.purchtaxglaccount=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.purchtaxglaccount');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxauthorities','taxauthrates.taxauthority=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxauthrates','taxauthorities.taxid=taxauthrates.taxauthority');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxcategories','taxauthrates.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','taxauthrates','taxcategories.taxcatid=taxauthrates.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxprovinces','taxauthrates.dispatchtaxprovince=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','taxauthrates','taxprovinces.taxprovinceid=taxauthrates.dispatchtaxprovince');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxgroups','taxgrouptaxes.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','taxgrouptaxes','taxgroups.taxgroupid=taxgrouptaxes.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxauthorities','taxgrouptaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxgrouptaxes','taxauthorities.taxid=taxgrouptaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('workcentres','locations','workcentres.location=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','workcentres','locations.loccode=workcentres.location');
INSERT INTO `reportlinks` VALUES ('worksorders','locations','worksorders.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','worksorders','locations.loccode=worksorders.loccode');
INSERT INTO `reportlinks` VALUES ('worksorders','stockmaster','worksorders.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','worksorders','stockmaster.stockid=worksorders.stockid');
INSERT INTO `reportlinks` VALUES ('www_users','locations','www_users.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','www_users','locations.loccode=www_users.defaultlocation');
INSERT INTO `reportlinks` VALUES ('accountgroups','accountsection','accountgroups.sectioninaccounts=accountsection.sectionid');
INSERT INTO `reportlinks` VALUES ('accountsection','accountgroups','accountsection.sectionid=accountgroups.sectioninaccounts');
INSERT INTO `reportlinks` VALUES ('bankaccounts','chartmaster','bankaccounts.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','bankaccounts','chartmaster.accountcode=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('banktrans','systypes','banktrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','banktrans','systypes.typeid=banktrans.type');
INSERT INTO `reportlinks` VALUES ('banktrans','bankaccounts','banktrans.bankact=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('bankaccounts','banktrans','bankaccounts.accountcode=banktrans.bankact');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.parent=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.parent');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.component');
INSERT INTO `reportlinks` VALUES ('bom','workcentres','bom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','bom','workcentres.code=bom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('bom','locations','bom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','bom','locations.loccode=bom.loccode');
INSERT INTO `reportlinks` VALUES ('buckets','workcentres','buckets.workcentre=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','buckets','workcentres.code=buckets.workcentre');
INSERT INTO `reportlinks` VALUES ('chartdetails','chartmaster','chartdetails.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','chartdetails','chartmaster.accountcode=chartdetails.accountcode');
INSERT INTO `reportlinks` VALUES ('chartdetails','periods','chartdetails.period=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','chartdetails','periods.periodno=chartdetails.period');
INSERT INTO `reportlinks` VALUES ('chartmaster','accountgroups','chartmaster.group_=accountgroups.groupname');
INSERT INTO `reportlinks` VALUES ('accountgroups','chartmaster','accountgroups.groupname=chartmaster.group_');
INSERT INTO `reportlinks` VALUES ('contractbom','workcentres','contractbom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','contractbom','workcentres.code=contractbom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('contractbom','locations','contractbom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','contractbom','locations.loccode=contractbom.loccode');
INSERT INTO `reportlinks` VALUES ('contractbom','stockmaster','contractbom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','contractbom','stockmaster.stockid=contractbom.component');
INSERT INTO `reportlinks` VALUES ('contractreqts','contracts','contractreqts.contract=contracts.contractref');
INSERT INTO `reportlinks` VALUES ('contracts','contractreqts','contracts.contractref=contractreqts.contract');
INSERT INTO `reportlinks` VALUES ('contracts','custbranch','contracts.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','contracts','custbranch.debtorno=contracts.debtorno');
INSERT INTO `reportlinks` VALUES ('contracts','stockcategory','contracts.branchcode=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','contracts','stockcategory.categoryid=contracts.branchcode');
INSERT INTO `reportlinks` VALUES ('contracts','salestypes','contracts.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','contracts','salestypes.typeabbrev=contracts.typeabbrev');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocfrom=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocto=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocto');
INSERT INTO `reportlinks` VALUES ('custbranch','debtorsmaster','custbranch.debtorno=debtorsmaster.debtorno');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','custbranch','debtorsmaster.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','areas','custbranch.area=areas.areacode');
INSERT INTO `reportlinks` VALUES ('areas','custbranch','areas.areacode=custbranch.area');
INSERT INTO `reportlinks` VALUES ('custbranch','salesman','custbranch.salesman=salesman.salesmancode');
INSERT INTO `reportlinks` VALUES ('salesman','custbranch','salesman.salesmancode=custbranch.salesman');
INSERT INTO `reportlinks` VALUES ('custbranch','locations','custbranch.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','custbranch','locations.loccode=custbranch.defaultlocation');
INSERT INTO `reportlinks` VALUES ('custbranch','shippers','custbranch.defaultshipvia=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','custbranch','shippers.shipper_id=custbranch.defaultshipvia');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','holdreasons','debtorsmaster.holdreason=holdreasons.reasoncode');
INSERT INTO `reportlinks` VALUES ('holdreasons','debtorsmaster','holdreasons.reasoncode=debtorsmaster.holdreason');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','currencies','debtorsmaster.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','debtorsmaster','currencies.currabrev=debtorsmaster.currcode');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','paymentterms','debtorsmaster.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','debtorsmaster','paymentterms.termsindicator=debtorsmaster.paymentterms');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','salestypes','debtorsmaster.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','debtorsmaster','salestypes.typeabbrev=debtorsmaster.salestype');
INSERT INTO `reportlinks` VALUES ('debtortrans','custbranch','debtortrans.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','debtortrans','custbranch.debtorno=debtortrans.debtorno');
INSERT INTO `reportlinks` VALUES ('debtortrans','systypes','debtortrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','debtortrans','systypes.typeid=debtortrans.type');
INSERT INTO `reportlinks` VALUES ('debtortrans','periods','debtortrans.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','debtortrans','periods.periodno=debtortrans.prd');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','taxauthorities','debtortranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','debtortranstaxes','taxauthorities.taxid=debtortranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','debtortrans','debtortranstaxes.debtortransid=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','debtortranstaxes','debtortrans.id=debtortranstaxes.debtortransid');
INSERT INTO `reportlinks` VALUES ('discountmatrix','salestypes','discountmatrix.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','discountmatrix','salestypes.typeabbrev=discountmatrix.salestype');
INSERT INTO `reportlinks` VALUES ('freightcosts','locations','freightcosts.locationfrom=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','freightcosts','locations.loccode=freightcosts.locationfrom');
INSERT INTO `reportlinks` VALUES ('freightcosts','shippers','freightcosts.shipperid=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','freightcosts','shippers.shipper_id=freightcosts.shipperid');
INSERT INTO `reportlinks` VALUES ('gltrans','chartmaster','gltrans.account=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','gltrans','chartmaster.accountcode=gltrans.account');
INSERT INTO `reportlinks` VALUES ('gltrans','systypes','gltrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','gltrans','systypes.typeid=gltrans.type');
INSERT INTO `reportlinks` VALUES ('gltrans','periods','gltrans.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','gltrans','periods.periodno=gltrans.periodno');
INSERT INTO `reportlinks` VALUES ('grns','suppliers','grns.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','grns','suppliers.supplierid=grns.supplierid');
INSERT INTO `reportlinks` VALUES ('grns','purchorderdetails','grns.podetailitem=purchorderdetails.podetailitem');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','grns','purchorderdetails.podetailitem=grns.podetailitem');
INSERT INTO `reportlinks` VALUES ('locations','taxprovinces','locations.taxprovinceid=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','locations','taxprovinces.taxprovinceid=locations.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('locstock','locations','locstock.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','locstock','locations.loccode=locstock.loccode');
INSERT INTO `reportlinks` VALUES ('locstock','stockmaster','locstock.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','locstock','stockmaster.stockid=locstock.stockid');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.shiploc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.shiploc');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.recloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.recloc');
INSERT INTO `reportlinks` VALUES ('loctransfers','stockmaster','loctransfers.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','loctransfers','stockmaster.stockid=loctransfers.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','stockmaster','orderdeliverydifferenceslog.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','orderdeliverydifferencesl','stockmaster.stockid=orderdeliverydifferenceslog.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','custbranch','orderdeliverydifferenceslog.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','orderdeliverydifferencesl','custbranch.debtorno=orderdeliverydifferenceslog.debtorno');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','salesorders','orderdeliverydifferenceslog.branchcode=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','orderdeliverydifferencesl','salesorders.orderno=orderdeliverydifferenceslog.branchcode');
INSERT INTO `reportlinks` VALUES ('prices','stockmaster','prices.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','prices','stockmaster.stockid=prices.stockid');
INSERT INTO `reportlinks` VALUES ('prices','currencies','prices.currabrev=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','prices','currencies.currabrev=prices.currabrev');
INSERT INTO `reportlinks` VALUES ('prices','salestypes','prices.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','prices','salestypes.typeabbrev=prices.typeabbrev');
INSERT INTO `reportlinks` VALUES ('purchdata','stockmaster','purchdata.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','purchdata','stockmaster.stockid=purchdata.stockid');
INSERT INTO `reportlinks` VALUES ('purchdata','suppliers','purchdata.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchdata','suppliers.supplierid=purchdata.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','purchorders','purchorderdetails.orderno=purchorders.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','purchorderdetails','purchorders.orderno=purchorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','suppliers','purchorders.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchorders','suppliers.supplierid=purchorders.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorders','locations','purchorders.intostocklocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','purchorders','locations.loccode=purchorders.intostocklocation');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','custbranch','recurringsalesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','recurringsalesorders','custbranch.branchcode=recurringsalesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','recurringsalesorders','recurrsalesorderdetails.recurrorderno=recurringsalesorders.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','recurrsalesorderdetails','recurringsalesorders.recurrorderno=recurrsalesorderdetails.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','stockmaster','recurrsalesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','recurrsalesorderdetails','stockmaster.stockid=recurrsalesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('reportcolumns','reportheaders','reportcolumns.reportid=reportheaders.reportid');
INSERT INTO `reportlinks` VALUES ('reportheaders','reportcolumns','reportheaders.reportid=reportcolumns.reportid');
INSERT INTO `reportlinks` VALUES ('salesanalysis','periods','salesanalysis.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','salesanalysis','periods.periodno=salesanalysis.periodno');
INSERT INTO `reportlinks` VALUES ('salescatprod','stockmaster','salescatprod.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salescatprod','stockmaster.stockid=salescatprod.stockid');
INSERT INTO `reportlinks` VALUES ('salescatprod','salescat','salescatprod.salescatid=salescat.salescatid');
INSERT INTO `reportlinks` VALUES ('salescat','salescatprod','salescat.salescatid=salescatprod.salescatid');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','salesorders','salesorderdetails.orderno=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','salesorderdetails','salesorders.orderno=salesorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','stockmaster','salesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salesorderdetails','stockmaster.stockid=salesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('salesorders','custbranch','salesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','salesorders','custbranch.branchcode=salesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('salesorders','shippers','salesorders.debtorno=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','salesorders','shippers.shipper_id=salesorders.debtorno');
INSERT INTO `reportlinks` VALUES ('salesorders','locations','salesorders.fromstkloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','salesorders','locations.loccode=salesorders.fromstkloc');
INSERT INTO `reportlinks` VALUES ('securitygroups','securityroles','securitygroups.secroleid=securityroles.secroleid');
INSERT INTO `reportlinks` VALUES ('securityroles','securitygroups','securityroles.secroleid=securitygroups.secroleid');
INSERT INTO `reportlinks` VALUES ('securitygroups','securitytokens','securitygroups.tokenid=securitytokens.tokenid');
INSERT INTO `reportlinks` VALUES ('securitytokens','securitygroups','securitytokens.tokenid=securitygroups.tokenid');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','shipments','shipmentcharges.shiptref=shipments.shiptref');
INSERT INTO `reportlinks` VALUES ('shipments','shipmentcharges','shipments.shiptref=shipmentcharges.shiptref');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','systypes','shipmentcharges.transtype=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','shipmentcharges','systypes.typeid=shipmentcharges.transtype');
INSERT INTO `reportlinks` VALUES ('shipments','suppliers','shipments.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','shipments','suppliers.supplierid=shipments.supplierid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','stockmaster','stockcheckfreeze.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcheckfreeze','stockmaster.stockid=stockcheckfreeze.stockid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','locations','stockcheckfreeze.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcheckfreeze','locations.loccode=stockcheckfreeze.loccode');
INSERT INTO `reportlinks` VALUES ('stockcounts','stockmaster','stockcounts.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcounts','stockmaster.stockid=stockcounts.stockid');
INSERT INTO `reportlinks` VALUES ('stockcounts','locations','stockcounts.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcounts','locations.loccode=stockcounts.loccode');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcategory','stockmaster.categoryid=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','stockmaster','stockcategory.categoryid=stockmaster.categoryid');
INSERT INTO `reportlinks` VALUES ('stockmaster','taxcategories','stockmaster.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','stockmaster','taxcategories.taxcatid=stockmaster.taxcatid');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockmaster','stockmoves.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockmoves','stockmaster.stockid=stockmoves.stockid');
INSERT INTO `reportlinks` VALUES ('stockmoves','systypes','stockmoves.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','stockmoves','systypes.typeid=stockmoves.type');
INSERT INTO `reportlinks` VALUES ('stockmoves','locations','stockmoves.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockmoves','locations.loccode=stockmoves.loccode');
INSERT INTO `reportlinks` VALUES ('stockmoves','periods','stockmoves.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','stockmoves','periods.periodno=stockmoves.prd');
INSERT INTO `reportlinks` VALUES ('stockmovestaxes','taxauthorities','stockmovestaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','stockmovestaxes','taxauthorities.taxid=stockmovestaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockmaster','stockserialitems.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockserialitems','stockmaster.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','locations','stockserialitems.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockserialitems','locations.loccode=stockserialitems.loccode');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockmoves','stockserialmoves.stockmoveno=stockmoves.stkmoveno');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockserialmoves','stockmoves.stkmoveno=stockserialmoves.stockmoveno');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockserialitems','stockserialmoves.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockserialmoves','stockserialitems.stockid=stockserialmoves.stockid');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocfrom=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocto=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocto');
INSERT INTO `reportlinks` VALUES ('suppliercontacts','suppliers','suppliercontacts.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','suppliercontacts','suppliers.supplierid=suppliercontacts.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','currencies','suppliers.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','suppliers','currencies.currabrev=suppliers.currcode');
INSERT INTO `reportlinks` VALUES ('suppliers','paymentterms','suppliers.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','suppliers','paymentterms.termsindicator=suppliers.paymentterms');
INSERT INTO `reportlinks` VALUES ('suppliers','taxgroups','suppliers.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','suppliers','taxgroups.taxgroupid=suppliers.taxgroupid');
INSERT INTO `reportlinks` VALUES ('supptrans','systypes','supptrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','supptrans','systypes.typeid=supptrans.type');
INSERT INTO `reportlinks` VALUES ('supptrans','suppliers','supptrans.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','supptrans','suppliers.supplierid=supptrans.supplierno');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','taxauthorities','supptranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','supptranstaxes','taxauthorities.taxid=supptranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','supptrans','supptranstaxes.supptransid=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','supptranstaxes','supptrans.id=supptranstaxes.supptransid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.taxglcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.taxglcode');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.purchtaxglaccount=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.purchtaxglaccount');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxauthorities','taxauthrates.taxauthority=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxauthrates','taxauthorities.taxid=taxauthrates.taxauthority');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxcategories','taxauthrates.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','taxauthrates','taxcategories.taxcatid=taxauthrates.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxprovinces','taxauthrates.dispatchtaxprovince=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','taxauthrates','taxprovinces.taxprovinceid=taxauthrates.dispatchtaxprovince');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxgroups','taxgrouptaxes.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','taxgrouptaxes','taxgroups.taxgroupid=taxgrouptaxes.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxauthorities','taxgrouptaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxgrouptaxes','taxauthorities.taxid=taxgrouptaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('workcentres','locations','workcentres.location=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','workcentres','locations.loccode=workcentres.location');
INSERT INTO `reportlinks` VALUES ('worksorders','locations','worksorders.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','worksorders','locations.loccode=worksorders.loccode');
INSERT INTO `reportlinks` VALUES ('worksorders','stockmaster','worksorders.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','worksorders','stockmaster.stockid=worksorders.stockid');
INSERT INTO `reportlinks` VALUES ('www_users','locations','www_users.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','www_users','locations.loccode=www_users.defaultlocation');
INSERT INTO `reportlinks` VALUES ('accountgroups','accountsection','accountgroups.sectioninaccounts=accountsection.sectionid');
INSERT INTO `reportlinks` VALUES ('accountsection','accountgroups','accountsection.sectionid=accountgroups.sectioninaccounts');
INSERT INTO `reportlinks` VALUES ('bankaccounts','chartmaster','bankaccounts.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','bankaccounts','chartmaster.accountcode=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('banktrans','systypes','banktrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','banktrans','systypes.typeid=banktrans.type');
INSERT INTO `reportlinks` VALUES ('banktrans','bankaccounts','banktrans.bankact=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('bankaccounts','banktrans','bankaccounts.accountcode=banktrans.bankact');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.parent=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.parent');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.component');
INSERT INTO `reportlinks` VALUES ('bom','workcentres','bom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','bom','workcentres.code=bom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('bom','locations','bom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','bom','locations.loccode=bom.loccode');
INSERT INTO `reportlinks` VALUES ('buckets','workcentres','buckets.workcentre=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','buckets','workcentres.code=buckets.workcentre');
INSERT INTO `reportlinks` VALUES ('chartdetails','chartmaster','chartdetails.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','chartdetails','chartmaster.accountcode=chartdetails.accountcode');
INSERT INTO `reportlinks` VALUES ('chartdetails','periods','chartdetails.period=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','chartdetails','periods.periodno=chartdetails.period');
INSERT INTO `reportlinks` VALUES ('chartmaster','accountgroups','chartmaster.group_=accountgroups.groupname');
INSERT INTO `reportlinks` VALUES ('accountgroups','chartmaster','accountgroups.groupname=chartmaster.group_');
INSERT INTO `reportlinks` VALUES ('contractbom','workcentres','contractbom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','contractbom','workcentres.code=contractbom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('contractbom','locations','contractbom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','contractbom','locations.loccode=contractbom.loccode');
INSERT INTO `reportlinks` VALUES ('contractbom','stockmaster','contractbom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','contractbom','stockmaster.stockid=contractbom.component');
INSERT INTO `reportlinks` VALUES ('contractreqts','contracts','contractreqts.contract=contracts.contractref');
INSERT INTO `reportlinks` VALUES ('contracts','contractreqts','contracts.contractref=contractreqts.contract');
INSERT INTO `reportlinks` VALUES ('contracts','custbranch','contracts.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','contracts','custbranch.debtorno=contracts.debtorno');
INSERT INTO `reportlinks` VALUES ('contracts','stockcategory','contracts.branchcode=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','contracts','stockcategory.categoryid=contracts.branchcode');
INSERT INTO `reportlinks` VALUES ('contracts','salestypes','contracts.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','contracts','salestypes.typeabbrev=contracts.typeabbrev');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocfrom=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocto=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocto');
INSERT INTO `reportlinks` VALUES ('custbranch','debtorsmaster','custbranch.debtorno=debtorsmaster.debtorno');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','custbranch','debtorsmaster.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','areas','custbranch.area=areas.areacode');
INSERT INTO `reportlinks` VALUES ('areas','custbranch','areas.areacode=custbranch.area');
INSERT INTO `reportlinks` VALUES ('custbranch','salesman','custbranch.salesman=salesman.salesmancode');
INSERT INTO `reportlinks` VALUES ('salesman','custbranch','salesman.salesmancode=custbranch.salesman');
INSERT INTO `reportlinks` VALUES ('custbranch','locations','custbranch.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','custbranch','locations.loccode=custbranch.defaultlocation');
INSERT INTO `reportlinks` VALUES ('custbranch','shippers','custbranch.defaultshipvia=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','custbranch','shippers.shipper_id=custbranch.defaultshipvia');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','holdreasons','debtorsmaster.holdreason=holdreasons.reasoncode');
INSERT INTO `reportlinks` VALUES ('holdreasons','debtorsmaster','holdreasons.reasoncode=debtorsmaster.holdreason');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','currencies','debtorsmaster.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','debtorsmaster','currencies.currabrev=debtorsmaster.currcode');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','paymentterms','debtorsmaster.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','debtorsmaster','paymentterms.termsindicator=debtorsmaster.paymentterms');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','salestypes','debtorsmaster.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','debtorsmaster','salestypes.typeabbrev=debtorsmaster.salestype');
INSERT INTO `reportlinks` VALUES ('debtortrans','custbranch','debtortrans.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','debtortrans','custbranch.debtorno=debtortrans.debtorno');
INSERT INTO `reportlinks` VALUES ('debtortrans','systypes','debtortrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','debtortrans','systypes.typeid=debtortrans.type');
INSERT INTO `reportlinks` VALUES ('debtortrans','periods','debtortrans.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','debtortrans','periods.periodno=debtortrans.prd');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','taxauthorities','debtortranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','debtortranstaxes','taxauthorities.taxid=debtortranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','debtortrans','debtortranstaxes.debtortransid=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','debtortranstaxes','debtortrans.id=debtortranstaxes.debtortransid');
INSERT INTO `reportlinks` VALUES ('discountmatrix','salestypes','discountmatrix.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','discountmatrix','salestypes.typeabbrev=discountmatrix.salestype');
INSERT INTO `reportlinks` VALUES ('freightcosts','locations','freightcosts.locationfrom=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','freightcosts','locations.loccode=freightcosts.locationfrom');
INSERT INTO `reportlinks` VALUES ('freightcosts','shippers','freightcosts.shipperid=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','freightcosts','shippers.shipper_id=freightcosts.shipperid');
INSERT INTO `reportlinks` VALUES ('gltrans','chartmaster','gltrans.account=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','gltrans','chartmaster.accountcode=gltrans.account');
INSERT INTO `reportlinks` VALUES ('gltrans','systypes','gltrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','gltrans','systypes.typeid=gltrans.type');
INSERT INTO `reportlinks` VALUES ('gltrans','periods','gltrans.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','gltrans','periods.periodno=gltrans.periodno');
INSERT INTO `reportlinks` VALUES ('grns','suppliers','grns.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','grns','suppliers.supplierid=grns.supplierid');
INSERT INTO `reportlinks` VALUES ('grns','purchorderdetails','grns.podetailitem=purchorderdetails.podetailitem');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','grns','purchorderdetails.podetailitem=grns.podetailitem');
INSERT INTO `reportlinks` VALUES ('locations','taxprovinces','locations.taxprovinceid=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','locations','taxprovinces.taxprovinceid=locations.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('locstock','locations','locstock.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','locstock','locations.loccode=locstock.loccode');
INSERT INTO `reportlinks` VALUES ('locstock','stockmaster','locstock.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','locstock','stockmaster.stockid=locstock.stockid');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.shiploc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.shiploc');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.recloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.recloc');
INSERT INTO `reportlinks` VALUES ('loctransfers','stockmaster','loctransfers.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','loctransfers','stockmaster.stockid=loctransfers.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','stockmaster','orderdeliverydifferenceslog.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','orderdeliverydifferencesl','stockmaster.stockid=orderdeliverydifferenceslog.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','custbranch','orderdeliverydifferenceslog.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','orderdeliverydifferencesl','custbranch.debtorno=orderdeliverydifferenceslog.debtorno');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','salesorders','orderdeliverydifferenceslog.branchcode=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','orderdeliverydifferencesl','salesorders.orderno=orderdeliverydifferenceslog.branchcode');
INSERT INTO `reportlinks` VALUES ('prices','stockmaster','prices.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','prices','stockmaster.stockid=prices.stockid');
INSERT INTO `reportlinks` VALUES ('prices','currencies','prices.currabrev=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','prices','currencies.currabrev=prices.currabrev');
INSERT INTO `reportlinks` VALUES ('prices','salestypes','prices.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','prices','salestypes.typeabbrev=prices.typeabbrev');
INSERT INTO `reportlinks` VALUES ('purchdata','stockmaster','purchdata.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','purchdata','stockmaster.stockid=purchdata.stockid');
INSERT INTO `reportlinks` VALUES ('purchdata','suppliers','purchdata.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchdata','suppliers.supplierid=purchdata.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','purchorders','purchorderdetails.orderno=purchorders.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','purchorderdetails','purchorders.orderno=purchorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','suppliers','purchorders.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchorders','suppliers.supplierid=purchorders.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorders','locations','purchorders.intostocklocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','purchorders','locations.loccode=purchorders.intostocklocation');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','custbranch','recurringsalesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','recurringsalesorders','custbranch.branchcode=recurringsalesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','recurringsalesorders','recurrsalesorderdetails.recurrorderno=recurringsalesorders.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','recurrsalesorderdetails','recurringsalesorders.recurrorderno=recurrsalesorderdetails.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','stockmaster','recurrsalesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','recurrsalesorderdetails','stockmaster.stockid=recurrsalesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('reportcolumns','reportheaders','reportcolumns.reportid=reportheaders.reportid');
INSERT INTO `reportlinks` VALUES ('reportheaders','reportcolumns','reportheaders.reportid=reportcolumns.reportid');
INSERT INTO `reportlinks` VALUES ('salesanalysis','periods','salesanalysis.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','salesanalysis','periods.periodno=salesanalysis.periodno');
INSERT INTO `reportlinks` VALUES ('salescatprod','stockmaster','salescatprod.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salescatprod','stockmaster.stockid=salescatprod.stockid');
INSERT INTO `reportlinks` VALUES ('salescatprod','salescat','salescatprod.salescatid=salescat.salescatid');
INSERT INTO `reportlinks` VALUES ('salescat','salescatprod','salescat.salescatid=salescatprod.salescatid');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','salesorders','salesorderdetails.orderno=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','salesorderdetails','salesorders.orderno=salesorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','stockmaster','salesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salesorderdetails','stockmaster.stockid=salesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('salesorders','custbranch','salesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','salesorders','custbranch.branchcode=salesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('salesorders','shippers','salesorders.debtorno=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','salesorders','shippers.shipper_id=salesorders.debtorno');
INSERT INTO `reportlinks` VALUES ('salesorders','locations','salesorders.fromstkloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','salesorders','locations.loccode=salesorders.fromstkloc');
INSERT INTO `reportlinks` VALUES ('securitygroups','securityroles','securitygroups.secroleid=securityroles.secroleid');
INSERT INTO `reportlinks` VALUES ('securityroles','securitygroups','securityroles.secroleid=securitygroups.secroleid');
INSERT INTO `reportlinks` VALUES ('securitygroups','securitytokens','securitygroups.tokenid=securitytokens.tokenid');
INSERT INTO `reportlinks` VALUES ('securitytokens','securitygroups','securitytokens.tokenid=securitygroups.tokenid');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','shipments','shipmentcharges.shiptref=shipments.shiptref');
INSERT INTO `reportlinks` VALUES ('shipments','shipmentcharges','shipments.shiptref=shipmentcharges.shiptref');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','systypes','shipmentcharges.transtype=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','shipmentcharges','systypes.typeid=shipmentcharges.transtype');
INSERT INTO `reportlinks` VALUES ('shipments','suppliers','shipments.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','shipments','suppliers.supplierid=shipments.supplierid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','stockmaster','stockcheckfreeze.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcheckfreeze','stockmaster.stockid=stockcheckfreeze.stockid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','locations','stockcheckfreeze.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcheckfreeze','locations.loccode=stockcheckfreeze.loccode');
INSERT INTO `reportlinks` VALUES ('stockcounts','stockmaster','stockcounts.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcounts','stockmaster.stockid=stockcounts.stockid');
INSERT INTO `reportlinks` VALUES ('stockcounts','locations','stockcounts.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcounts','locations.loccode=stockcounts.loccode');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcategory','stockmaster.categoryid=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','stockmaster','stockcategory.categoryid=stockmaster.categoryid');
INSERT INTO `reportlinks` VALUES ('stockmaster','taxcategories','stockmaster.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','stockmaster','taxcategories.taxcatid=stockmaster.taxcatid');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockmaster','stockmoves.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockmoves','stockmaster.stockid=stockmoves.stockid');
INSERT INTO `reportlinks` VALUES ('stockmoves','systypes','stockmoves.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','stockmoves','systypes.typeid=stockmoves.type');
INSERT INTO `reportlinks` VALUES ('stockmoves','locations','stockmoves.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockmoves','locations.loccode=stockmoves.loccode');
INSERT INTO `reportlinks` VALUES ('stockmoves','periods','stockmoves.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','stockmoves','periods.periodno=stockmoves.prd');
INSERT INTO `reportlinks` VALUES ('stockmovestaxes','taxauthorities','stockmovestaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','stockmovestaxes','taxauthorities.taxid=stockmovestaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockmaster','stockserialitems.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockserialitems','stockmaster.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','locations','stockserialitems.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockserialitems','locations.loccode=stockserialitems.loccode');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockmoves','stockserialmoves.stockmoveno=stockmoves.stkmoveno');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockserialmoves','stockmoves.stkmoveno=stockserialmoves.stockmoveno');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockserialitems','stockserialmoves.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockserialmoves','stockserialitems.stockid=stockserialmoves.stockid');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocfrom=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocto=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocto');
INSERT INTO `reportlinks` VALUES ('suppliercontacts','suppliers','suppliercontacts.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','suppliercontacts','suppliers.supplierid=suppliercontacts.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','currencies','suppliers.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','suppliers','currencies.currabrev=suppliers.currcode');
INSERT INTO `reportlinks` VALUES ('suppliers','paymentterms','suppliers.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','suppliers','paymentterms.termsindicator=suppliers.paymentterms');
INSERT INTO `reportlinks` VALUES ('suppliers','taxgroups','suppliers.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','suppliers','taxgroups.taxgroupid=suppliers.taxgroupid');
INSERT INTO `reportlinks` VALUES ('supptrans','systypes','supptrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','supptrans','systypes.typeid=supptrans.type');
INSERT INTO `reportlinks` VALUES ('supptrans','suppliers','supptrans.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','supptrans','suppliers.supplierid=supptrans.supplierno');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','taxauthorities','supptranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','supptranstaxes','taxauthorities.taxid=supptranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','supptrans','supptranstaxes.supptransid=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','supptranstaxes','supptrans.id=supptranstaxes.supptransid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.taxglcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.taxglcode');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.purchtaxglaccount=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.purchtaxglaccount');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxauthorities','taxauthrates.taxauthority=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxauthrates','taxauthorities.taxid=taxauthrates.taxauthority');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxcategories','taxauthrates.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','taxauthrates','taxcategories.taxcatid=taxauthrates.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxprovinces','taxauthrates.dispatchtaxprovince=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','taxauthrates','taxprovinces.taxprovinceid=taxauthrates.dispatchtaxprovince');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxgroups','taxgrouptaxes.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','taxgrouptaxes','taxgroups.taxgroupid=taxgrouptaxes.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxauthorities','taxgrouptaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxgrouptaxes','taxauthorities.taxid=taxgrouptaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('workcentres','locations','workcentres.location=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','workcentres','locations.loccode=workcentres.location');
INSERT INTO `reportlinks` VALUES ('worksorders','locations','worksorders.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','worksorders','locations.loccode=worksorders.loccode');
INSERT INTO `reportlinks` VALUES ('worksorders','stockmaster','worksorders.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','worksorders','stockmaster.stockid=worksorders.stockid');
INSERT INTO `reportlinks` VALUES ('www_users','locations','www_users.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','www_users','locations.loccode=www_users.defaultlocation');
INSERT INTO `reportlinks` VALUES ('accountgroups','accountsection','accountgroups.sectioninaccounts=accountsection.sectionid');
INSERT INTO `reportlinks` VALUES ('accountsection','accountgroups','accountsection.sectionid=accountgroups.sectioninaccounts');
INSERT INTO `reportlinks` VALUES ('bankaccounts','chartmaster','bankaccounts.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','bankaccounts','chartmaster.accountcode=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('banktrans','systypes','banktrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','banktrans','systypes.typeid=banktrans.type');
INSERT INTO `reportlinks` VALUES ('banktrans','bankaccounts','banktrans.bankact=bankaccounts.accountcode');
INSERT INTO `reportlinks` VALUES ('bankaccounts','banktrans','bankaccounts.accountcode=banktrans.bankact');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.parent=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.parent');
INSERT INTO `reportlinks` VALUES ('bom','stockmaster','bom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','bom','stockmaster.stockid=bom.component');
INSERT INTO `reportlinks` VALUES ('bom','workcentres','bom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','bom','workcentres.code=bom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('bom','locations','bom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','bom','locations.loccode=bom.loccode');
INSERT INTO `reportlinks` VALUES ('buckets','workcentres','buckets.workcentre=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','buckets','workcentres.code=buckets.workcentre');
INSERT INTO `reportlinks` VALUES ('chartdetails','chartmaster','chartdetails.accountcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','chartdetails','chartmaster.accountcode=chartdetails.accountcode');
INSERT INTO `reportlinks` VALUES ('chartdetails','periods','chartdetails.period=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','chartdetails','periods.periodno=chartdetails.period');
INSERT INTO `reportlinks` VALUES ('chartmaster','accountgroups','chartmaster.group_=accountgroups.groupname');
INSERT INTO `reportlinks` VALUES ('accountgroups','chartmaster','accountgroups.groupname=chartmaster.group_');
INSERT INTO `reportlinks` VALUES ('contractbom','workcentres','contractbom.workcentreadded=workcentres.code');
INSERT INTO `reportlinks` VALUES ('workcentres','contractbom','workcentres.code=contractbom.workcentreadded');
INSERT INTO `reportlinks` VALUES ('contractbom','locations','contractbom.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','contractbom','locations.loccode=contractbom.loccode');
INSERT INTO `reportlinks` VALUES ('contractbom','stockmaster','contractbom.component=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','contractbom','stockmaster.stockid=contractbom.component');
INSERT INTO `reportlinks` VALUES ('contractreqts','contracts','contractreqts.contract=contracts.contractref');
INSERT INTO `reportlinks` VALUES ('contracts','contractreqts','contracts.contractref=contractreqts.contract');
INSERT INTO `reportlinks` VALUES ('contracts','custbranch','contracts.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','contracts','custbranch.debtorno=contracts.debtorno');
INSERT INTO `reportlinks` VALUES ('contracts','stockcategory','contracts.branchcode=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','contracts','stockcategory.categoryid=contracts.branchcode');
INSERT INTO `reportlinks` VALUES ('contracts','salestypes','contracts.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','contracts','salestypes.typeabbrev=contracts.typeabbrev');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocfrom=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('custallocns','debtortrans','custallocns.transid_allocto=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','custallocns','debtortrans.id=custallocns.transid_allocto');
INSERT INTO `reportlinks` VALUES ('custbranch','debtorsmaster','custbranch.debtorno=debtorsmaster.debtorno');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','custbranch','debtorsmaster.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','areas','custbranch.area=areas.areacode');
INSERT INTO `reportlinks` VALUES ('areas','custbranch','areas.areacode=custbranch.area');
INSERT INTO `reportlinks` VALUES ('custbranch','salesman','custbranch.salesman=salesman.salesmancode');
INSERT INTO `reportlinks` VALUES ('salesman','custbranch','salesman.salesmancode=custbranch.salesman');
INSERT INTO `reportlinks` VALUES ('custbranch','locations','custbranch.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','custbranch','locations.loccode=custbranch.defaultlocation');
INSERT INTO `reportlinks` VALUES ('custbranch','shippers','custbranch.defaultshipvia=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','custbranch','shippers.shipper_id=custbranch.defaultshipvia');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','holdreasons','debtorsmaster.holdreason=holdreasons.reasoncode');
INSERT INTO `reportlinks` VALUES ('holdreasons','debtorsmaster','holdreasons.reasoncode=debtorsmaster.holdreason');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','currencies','debtorsmaster.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','debtorsmaster','currencies.currabrev=debtorsmaster.currcode');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','paymentterms','debtorsmaster.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','debtorsmaster','paymentterms.termsindicator=debtorsmaster.paymentterms');
INSERT INTO `reportlinks` VALUES ('debtorsmaster','salestypes','debtorsmaster.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','debtorsmaster','salestypes.typeabbrev=debtorsmaster.salestype');
INSERT INTO `reportlinks` VALUES ('debtortrans','custbranch','debtortrans.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','debtortrans','custbranch.debtorno=debtortrans.debtorno');
INSERT INTO `reportlinks` VALUES ('debtortrans','systypes','debtortrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','debtortrans','systypes.typeid=debtortrans.type');
INSERT INTO `reportlinks` VALUES ('debtortrans','periods','debtortrans.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','debtortrans','periods.periodno=debtortrans.prd');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','taxauthorities','debtortranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','debtortranstaxes','taxauthorities.taxid=debtortranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('debtortranstaxes','debtortrans','debtortranstaxes.debtortransid=debtortrans.id');
INSERT INTO `reportlinks` VALUES ('debtortrans','debtortranstaxes','debtortrans.id=debtortranstaxes.debtortransid');
INSERT INTO `reportlinks` VALUES ('discountmatrix','salestypes','discountmatrix.salestype=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','discountmatrix','salestypes.typeabbrev=discountmatrix.salestype');
INSERT INTO `reportlinks` VALUES ('freightcosts','locations','freightcosts.locationfrom=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','freightcosts','locations.loccode=freightcosts.locationfrom');
INSERT INTO `reportlinks` VALUES ('freightcosts','shippers','freightcosts.shipperid=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','freightcosts','shippers.shipper_id=freightcosts.shipperid');
INSERT INTO `reportlinks` VALUES ('gltrans','chartmaster','gltrans.account=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','gltrans','chartmaster.accountcode=gltrans.account');
INSERT INTO `reportlinks` VALUES ('gltrans','systypes','gltrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','gltrans','systypes.typeid=gltrans.type');
INSERT INTO `reportlinks` VALUES ('gltrans','periods','gltrans.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','gltrans','periods.periodno=gltrans.periodno');
INSERT INTO `reportlinks` VALUES ('grns','suppliers','grns.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','grns','suppliers.supplierid=grns.supplierid');
INSERT INTO `reportlinks` VALUES ('grns','purchorderdetails','grns.podetailitem=purchorderdetails.podetailitem');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','grns','purchorderdetails.podetailitem=grns.podetailitem');
INSERT INTO `reportlinks` VALUES ('locations','taxprovinces','locations.taxprovinceid=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','locations','taxprovinces.taxprovinceid=locations.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('locstock','locations','locstock.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','locstock','locations.loccode=locstock.loccode');
INSERT INTO `reportlinks` VALUES ('locstock','stockmaster','locstock.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','locstock','stockmaster.stockid=locstock.stockid');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.shiploc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.shiploc');
INSERT INTO `reportlinks` VALUES ('loctransfers','locations','loctransfers.recloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','loctransfers','locations.loccode=loctransfers.recloc');
INSERT INTO `reportlinks` VALUES ('loctransfers','stockmaster','loctransfers.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','loctransfers','stockmaster.stockid=loctransfers.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','stockmaster','orderdeliverydifferenceslog.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','orderdeliverydifferencesl','stockmaster.stockid=orderdeliverydifferenceslog.stockid');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','custbranch','orderdeliverydifferenceslog.debtorno=custbranch.debtorno');
INSERT INTO `reportlinks` VALUES ('custbranch','orderdeliverydifferencesl','custbranch.debtorno=orderdeliverydifferenceslog.debtorno');
INSERT INTO `reportlinks` VALUES ('orderdeliverydifferencesl','salesorders','orderdeliverydifferenceslog.branchcode=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','orderdeliverydifferencesl','salesorders.orderno=orderdeliverydifferenceslog.branchcode');
INSERT INTO `reportlinks` VALUES ('prices','stockmaster','prices.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','prices','stockmaster.stockid=prices.stockid');
INSERT INTO `reportlinks` VALUES ('prices','currencies','prices.currabrev=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','prices','currencies.currabrev=prices.currabrev');
INSERT INTO `reportlinks` VALUES ('prices','salestypes','prices.typeabbrev=salestypes.typeabbrev');
INSERT INTO `reportlinks` VALUES ('salestypes','prices','salestypes.typeabbrev=prices.typeabbrev');
INSERT INTO `reportlinks` VALUES ('purchdata','stockmaster','purchdata.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','purchdata','stockmaster.stockid=purchdata.stockid');
INSERT INTO `reportlinks` VALUES ('purchdata','suppliers','purchdata.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchdata','suppliers.supplierid=purchdata.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorderdetails','purchorders','purchorderdetails.orderno=purchorders.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','purchorderdetails','purchorders.orderno=purchorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('purchorders','suppliers','purchorders.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','purchorders','suppliers.supplierid=purchorders.supplierno');
INSERT INTO `reportlinks` VALUES ('purchorders','locations','purchorders.intostocklocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','purchorders','locations.loccode=purchorders.intostocklocation');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','custbranch','recurringsalesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','recurringsalesorders','custbranch.branchcode=recurringsalesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','recurringsalesorders','recurrsalesorderdetails.recurrorderno=recurringsalesorders.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurringsalesorders','recurrsalesorderdetails','recurringsalesorders.recurrorderno=recurrsalesorderdetails.recurrorderno');
INSERT INTO `reportlinks` VALUES ('recurrsalesorderdetails','stockmaster','recurrsalesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','recurrsalesorderdetails','stockmaster.stockid=recurrsalesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('reportcolumns','reportheaders','reportcolumns.reportid=reportheaders.reportid');
INSERT INTO `reportlinks` VALUES ('reportheaders','reportcolumns','reportheaders.reportid=reportcolumns.reportid');
INSERT INTO `reportlinks` VALUES ('salesanalysis','periods','salesanalysis.periodno=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','salesanalysis','periods.periodno=salesanalysis.periodno');
INSERT INTO `reportlinks` VALUES ('salescatprod','stockmaster','salescatprod.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salescatprod','stockmaster.stockid=salescatprod.stockid');
INSERT INTO `reportlinks` VALUES ('salescatprod','salescat','salescatprod.salescatid=salescat.salescatid');
INSERT INTO `reportlinks` VALUES ('salescat','salescatprod','salescat.salescatid=salescatprod.salescatid');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','salesorders','salesorderdetails.orderno=salesorders.orderno');
INSERT INTO `reportlinks` VALUES ('salesorders','salesorderdetails','salesorders.orderno=salesorderdetails.orderno');
INSERT INTO `reportlinks` VALUES ('salesorderdetails','stockmaster','salesorderdetails.stkcode=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','salesorderdetails','stockmaster.stockid=salesorderdetails.stkcode');
INSERT INTO `reportlinks` VALUES ('salesorders','custbranch','salesorders.branchcode=custbranch.branchcode');
INSERT INTO `reportlinks` VALUES ('custbranch','salesorders','custbranch.branchcode=salesorders.branchcode');
INSERT INTO `reportlinks` VALUES ('salesorders','shippers','salesorders.debtorno=shippers.shipper_id');
INSERT INTO `reportlinks` VALUES ('shippers','salesorders','shippers.shipper_id=salesorders.debtorno');
INSERT INTO `reportlinks` VALUES ('salesorders','locations','salesorders.fromstkloc=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','salesorders','locations.loccode=salesorders.fromstkloc');
INSERT INTO `reportlinks` VALUES ('securitygroups','securityroles','securitygroups.secroleid=securityroles.secroleid');
INSERT INTO `reportlinks` VALUES ('securityroles','securitygroups','securityroles.secroleid=securitygroups.secroleid');
INSERT INTO `reportlinks` VALUES ('securitygroups','securitytokens','securitygroups.tokenid=securitytokens.tokenid');
INSERT INTO `reportlinks` VALUES ('securitytokens','securitygroups','securitytokens.tokenid=securitygroups.tokenid');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','shipments','shipmentcharges.shiptref=shipments.shiptref');
INSERT INTO `reportlinks` VALUES ('shipments','shipmentcharges','shipments.shiptref=shipmentcharges.shiptref');
INSERT INTO `reportlinks` VALUES ('shipmentcharges','systypes','shipmentcharges.transtype=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','shipmentcharges','systypes.typeid=shipmentcharges.transtype');
INSERT INTO `reportlinks` VALUES ('shipments','suppliers','shipments.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','shipments','suppliers.supplierid=shipments.supplierid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','stockmaster','stockcheckfreeze.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcheckfreeze','stockmaster.stockid=stockcheckfreeze.stockid');
INSERT INTO `reportlinks` VALUES ('stockcheckfreeze','locations','stockcheckfreeze.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcheckfreeze','locations.loccode=stockcheckfreeze.loccode');
INSERT INTO `reportlinks` VALUES ('stockcounts','stockmaster','stockcounts.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcounts','stockmaster.stockid=stockcounts.stockid');
INSERT INTO `reportlinks` VALUES ('stockcounts','locations','stockcounts.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockcounts','locations.loccode=stockcounts.loccode');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockcategory','stockmaster.categoryid=stockcategory.categoryid');
INSERT INTO `reportlinks` VALUES ('stockcategory','stockmaster','stockcategory.categoryid=stockmaster.categoryid');
INSERT INTO `reportlinks` VALUES ('stockmaster','taxcategories','stockmaster.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','stockmaster','taxcategories.taxcatid=stockmaster.taxcatid');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockmaster','stockmoves.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockmoves','stockmaster.stockid=stockmoves.stockid');
INSERT INTO `reportlinks` VALUES ('stockmoves','systypes','stockmoves.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','stockmoves','systypes.typeid=stockmoves.type');
INSERT INTO `reportlinks` VALUES ('stockmoves','locations','stockmoves.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockmoves','locations.loccode=stockmoves.loccode');
INSERT INTO `reportlinks` VALUES ('stockmoves','periods','stockmoves.prd=periods.periodno');
INSERT INTO `reportlinks` VALUES ('periods','stockmoves','periods.periodno=stockmoves.prd');
INSERT INTO `reportlinks` VALUES ('stockmovestaxes','taxauthorities','stockmovestaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','stockmovestaxes','taxauthorities.taxid=stockmovestaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockmaster','stockserialitems.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','stockserialitems','stockmaster.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','locations','stockserialitems.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','stockserialitems','locations.loccode=stockserialitems.loccode');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockmoves','stockserialmoves.stockmoveno=stockmoves.stkmoveno');
INSERT INTO `reportlinks` VALUES ('stockmoves','stockserialmoves','stockmoves.stkmoveno=stockserialmoves.stockmoveno');
INSERT INTO `reportlinks` VALUES ('stockserialmoves','stockserialitems','stockserialmoves.stockid=stockserialitems.stockid');
INSERT INTO `reportlinks` VALUES ('stockserialitems','stockserialmoves','stockserialitems.stockid=stockserialmoves.stockid');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocfrom=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocfrom');
INSERT INTO `reportlinks` VALUES ('suppallocs','supptrans','suppallocs.transid_allocto=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','suppallocs','supptrans.id=suppallocs.transid_allocto');
INSERT INTO `reportlinks` VALUES ('suppliercontacts','suppliers','suppliercontacts.supplierid=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','suppliercontacts','suppliers.supplierid=suppliercontacts.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','currencies','suppliers.currcode=currencies.currabrev');
INSERT INTO `reportlinks` VALUES ('currencies','suppliers','currencies.currabrev=suppliers.currcode');
INSERT INTO `reportlinks` VALUES ('suppliers','paymentterms','suppliers.paymentterms=paymentterms.termsindicator');
INSERT INTO `reportlinks` VALUES ('paymentterms','suppliers','paymentterms.termsindicator=suppliers.paymentterms');
INSERT INTO `reportlinks` VALUES ('suppliers','taxgroups','suppliers.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','suppliers','taxgroups.taxgroupid=suppliers.taxgroupid');
INSERT INTO `reportlinks` VALUES ('supptrans','systypes','supptrans.type=systypes.typeid');
INSERT INTO `reportlinks` VALUES ('systypes','supptrans','systypes.typeid=supptrans.type');
INSERT INTO `reportlinks` VALUES ('supptrans','suppliers','supptrans.supplierno=suppliers.supplierid');
INSERT INTO `reportlinks` VALUES ('suppliers','supptrans','suppliers.supplierid=supptrans.supplierno');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','taxauthorities','supptranstaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','supptranstaxes','taxauthorities.taxid=supptranstaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('supptranstaxes','supptrans','supptranstaxes.supptransid=supptrans.id');
INSERT INTO `reportlinks` VALUES ('supptrans','supptranstaxes','supptrans.id=supptranstaxes.supptransid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.taxglcode=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.taxglcode');
INSERT INTO `reportlinks` VALUES ('taxauthorities','chartmaster','taxauthorities.purchtaxglaccount=chartmaster.accountcode');
INSERT INTO `reportlinks` VALUES ('chartmaster','taxauthorities','chartmaster.accountcode=taxauthorities.purchtaxglaccount');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxauthorities','taxauthrates.taxauthority=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxauthrates','taxauthorities.taxid=taxauthrates.taxauthority');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxcategories','taxauthrates.taxcatid=taxcategories.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxcategories','taxauthrates','taxcategories.taxcatid=taxauthrates.taxcatid');
INSERT INTO `reportlinks` VALUES ('taxauthrates','taxprovinces','taxauthrates.dispatchtaxprovince=taxprovinces.taxprovinceid');
INSERT INTO `reportlinks` VALUES ('taxprovinces','taxauthrates','taxprovinces.taxprovinceid=taxauthrates.dispatchtaxprovince');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxgroups','taxgrouptaxes.taxgroupid=taxgroups.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgroups','taxgrouptaxes','taxgroups.taxgroupid=taxgrouptaxes.taxgroupid');
INSERT INTO `reportlinks` VALUES ('taxgrouptaxes','taxauthorities','taxgrouptaxes.taxauthid=taxauthorities.taxid');
INSERT INTO `reportlinks` VALUES ('taxauthorities','taxgrouptaxes','taxauthorities.taxid=taxgrouptaxes.taxauthid');
INSERT INTO `reportlinks` VALUES ('workcentres','locations','workcentres.location=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','workcentres','locations.loccode=workcentres.location');
INSERT INTO `reportlinks` VALUES ('worksorders','locations','worksorders.loccode=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','worksorders','locations.loccode=worksorders.loccode');
INSERT INTO `reportlinks` VALUES ('worksorders','stockmaster','worksorders.stockid=stockmaster.stockid');
INSERT INTO `reportlinks` VALUES ('stockmaster','worksorders','stockmaster.stockid=worksorders.stockid');
INSERT INTO `reportlinks` VALUES ('www_users','locations','www_users.defaultlocation=locations.loccode');
INSERT INTO `reportlinks` VALUES ('locations','www_users','locations.loccode=www_users.defaultlocation');

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` VALUES (135,'Currency Price List','rpt','inv','1','A4:210:297','P',10,10,10,10,'helvetica',12,'0:0:0','C','1','%reportname%','helvetica',10,'0:0:0','C','1','Report Generated %date%','helvetica',10,'0:0:0','C','1','helvetica',8,'0:0:0','L','helvetica',10,'0:0:0','L','helvetica',10,'0:0:0','L',25,25,25,25,25,25,25,25,25,25,25,25,25,25,25,25,25,25,25,25,'stockmaster','prices','stockmaster.stockid=prices.stockid','','','','','','','','');

--
-- Dumping data for table `salesanalysis`
--

INSERT INTO `salesanalysis` VALUES ('DE',19,94.5,20.88,'16','16',9,0,'DVD-DHWV','TR',1,'ERI','DVD',1);
INSERT INTO `salesanalysis` VALUES ('DE',19,2.74,0,'16','16',1,0,'PAYTSURCHARGE','TR',1,'ERI','ZPAYT',2);
INSERT INTO `salesanalysis` VALUES ('DE',22,5,1.13,'12','12',2,0,'BREAD','TR',1,'ERI','FOOD',3);

--
-- Dumping data for table `salescat`
--

INSERT INTO `salescat` VALUES (1,0,'DVD',1);
INSERT INTO `salescat` VALUES (3,1,'Action',1);
INSERT INTO `salescat` VALUES (4,3,'Gibson',1);
INSERT INTO `salescat` VALUES (5,3,'Willis',1);
INSERT INTO `salescat` VALUES (6,3,'Cruise',1);
INSERT INTO `salescat` VALUES (7,0,'Air Conditioning',1);
INSERT INTO `salescat` VALUES (8,0,'Test',1);

--
-- Dumping data for table `salescatprod`
--

INSERT INTO `salescatprod` VALUES (1,'DVD-DHWV',2,1);
INSERT INTO `salescatprod` VALUES (1,'DVD-LTWP',1,0);
INSERT INTO `salescatprod` VALUES (1,'DVD-TOPGUN',1,1);
INSERT INTO `salescatprod` VALUES (1,'DVD-UNSG',1,0);
INSERT INTO `salescatprod` VALUES (4,'DVD-LTWP',1,0);
INSERT INTO `salescatprod` VALUES (5,'DVD-DHWV',2,1);
INSERT INTO `salescatprod` VALUES (6,'DVD-TOPGUN',2,1);
INSERT INTO `salescatprod` VALUES (7,'FUJI990101',3,0);
INSERT INTO `salescatprod` VALUES (7,'FUJI9901ASS',3,0);
INSERT INTO `salescatprod` VALUES (7,'HIT3042-4',4,0);
INSERT INTO `salescatprod` VALUES (7,'HIT3043-5',4,0);
INSERT INTO `salescatprod` VALUES (8,'CUTTING',3,0);

--
-- Dumping data for table `salescattranslations`
--


--
-- Dumping data for table `salesglpostings`
--

INSERT INTO `salesglpostings` VALUES (1,'AN','ANY','4900','4100','AN');
INSERT INTO `salesglpostings` VALUES (2,'AN','AIRCON','5000','4800','DE');
INSERT INTO `salesglpostings` VALUES (3,'AN','ZPAYT','7230','7230','AN');

--
-- Dumping data for table `salesman`
--

INSERT INTO `salesman` VALUES ('DE','Default Sales person','','',0,0,0,1);
INSERT INTO `salesman` VALUES ('ERI','Eric Browlee','','',0,0,0,1);
INSERT INTO `salesman` VALUES ('INT','Internet Shop','','',0,0,0,1);
INSERT INTO `salesman` VALUES ('PHO','Phone Contact','','',5.5,10001,2.95,1);

--
-- Dumping data for table `salesorderdetails`
--

INSERT INTO `salesorderdetails` VALUES (0,1,'DVD-DHWV',9,10.5,9,0,0,'2013-06-26 00:00:00',1,NULL,'2013-06-23','');
INSERT INTO `salesorderdetails` VALUES (0,7,'BREAD',2,2.5,2,0,0,'2013-09-07 00:00:00',1,'','2013-09-06','');
INSERT INTO `salesorderdetails` VALUES (0,8,'CUTTING',0,50,2,0,0,'0000-00-00 00:00:00',0,'','2014-08-02','0');
INSERT INTO `salesorderdetails` VALUES (1,1,'PAYTSURCHARGE',1,2.7405,1,0,0,'2013-06-26 00:00:00',1,NULL,'2013-06-23','');
INSERT INTO `salesorderdetails` VALUES (1,8,'DR_TUMMY',0,0,1,0,0,'0000-00-00 00:00:00',0,'','2014-08-02','0');

--
-- Dumping data for table `salesorders`
--

INSERT INTO `salesorders` VALUES (1,'16','16','',NULL,' Inv 1','2013-06-23','DE',1,'34 Marram Way','Peka Peka','RD1 Waiakane','5134','','New Zealand','64275567890','phil@logicworks.co.nz','Phil Daintree',1,0,'TOR','2013-06-23','2013-06-23',0,'0000-00-00',0,'2013-06-23',0,'ERI');
INSERT INTO `salesorders` VALUES (7,'12','12','',NULL,' Inv 2','2013-09-06','DE',1,'123 Alexander Road','Roundhay','Leeds','3211','','United Kingdom','212234566','angus@angry.com','Angus Routledge &amp; Co',1,0,'TOR','2013-09-07','2013-09-07',0,'0000-00-00',0,'2013-09-07',0,'ERI');
INSERT INTO `salesorders` VALUES (8,'12','12','',NULL,'','2014-08-02','DE',1,'123 Alexander Road','Roundhay','Leeds','3211','','United Kingdom','212234566','angus@angry.com','Angus Routledge & Co',1,0,'TOR','2014-08-04','2014-08-04',0,'0000-00-00',0,'2014-08-04',0,'ERI');
INSERT INTO `salesorders` VALUES (10,'WEB0000018','WEB0000018','',NULL,'','2014-08-31','DE',1,'34 Marram Way','Peka Peka, RD1 Waikanae','','Kapiti','5134','New Zealand','04 528 9514','phil@logicworks.co.nz','Phil Daintree',1,0,'TOR','2014-08-31','2014-08-31',0,'0000-00-00',1,'2014-08-31',0,'ERI');

--
-- Dumping data for table `salestypes`
--

INSERT INTO `salestypes` VALUES ('DE','Default Price List');

--
-- Dumping data for table `sampleresults`
--


--
-- Dumping data for table `scripts`
--

INSERT INTO `scripts` VALUES ('AccountGroups.php',10,'Defines the groupings of general ledger accounts');
INSERT INTO `scripts` VALUES ('AccountSections.php',10,'Defines the sections in the general ledger reports');
INSERT INTO `scripts` VALUES ('AddCustomerContacts.php',3,'Adds customer contacts');
INSERT INTO `scripts` VALUES ('AddCustomerNotes.php',3,'Adds notes about customers');
INSERT INTO `scripts` VALUES ('AddCustomerTypeNotes.php',3,'');
INSERT INTO `scripts` VALUES ('AgedControlledInventory.php',11,'Report of Controlled Items and their age');
INSERT INTO `scripts` VALUES ('AgedDebtors.php',2,'Lists customer account balances in detail or summary in selected currency');
INSERT INTO `scripts` VALUES ('AgedSuppliers.php',2,'Lists supplier account balances in detail or summary in selected currency');
INSERT INTO `scripts` VALUES ('Areas.php',3,'Defines the sales areas - all customers must belong to a sales area for the purposes of sales analysis');
INSERT INTO `scripts` VALUES ('AuditTrail.php',15,'Shows the activity with SQL statements and who performed the changes');
INSERT INTO `scripts` VALUES ('AutomaticTranslationDescriptions.php',15,'Translates via Google Translator all empty translated descriptions');
INSERT INTO `scripts` VALUES ('BankAccounts.php',10,'Defines the general ledger code for bank accounts and specifies that bank transactions be created for these accounts for the purposes of reconciliation');
INSERT INTO `scripts` VALUES ('BankAccountUsers.php',15,'Maintains table bankaccountusers (Authorized users to work with a bank account in webERP)');
INSERT INTO `scripts` VALUES ('BankMatching.php',7,'Allows payments and receipts to be matched off against bank statements');
INSERT INTO `scripts` VALUES ('BankReconciliation.php',7,'Displays the bank reconciliation for a selected bank account');
INSERT INTO `scripts` VALUES ('BOMExtendedQty.php',2,'Shows the component requirements to make an item');
INSERT INTO `scripts` VALUES ('BOMIndented.php',2,'Shows the bill of material indented for each level');
INSERT INTO `scripts` VALUES ('BOMIndentedReverse.php',2,'');
INSERT INTO `scripts` VALUES ('BOMInquiry.php',2,'Displays the bill of material with cost information');
INSERT INTO `scripts` VALUES ('BOMListing.php',2,'Lists the bills of material for a selected range of items');
INSERT INTO `scripts` VALUES ('BOMs.php',9,'Administers the bills of material for a selected item');
INSERT INTO `scripts` VALUES ('COGSGLPostings.php',10,'Defines the general ledger account to be used for cost of sales entries');
INSERT INTO `scripts` VALUES ('CompanyPreferences.php',10,'Defines the settings applicable for the company, including name, address, tax authority reference, whether GL integration used etc.');
INSERT INTO `scripts` VALUES ('ConfirmDispatchControlled_Invoice.php',2,'Specifies the batch references/serial numbers of items dispatched that are being invoiced');
INSERT INTO `scripts` VALUES ('ConfirmDispatch_Invoice.php',2,'Creates sales invoices from entered sales orders based on the quantities dispatched that can be modified');
INSERT INTO `scripts` VALUES ('ContractBOM.php',6,'Creates the item requirements from stock for a contract as part of the contract cost build up');
INSERT INTO `scripts` VALUES ('ContractCosting.php',6,'Shows a contract cost - the components and other non-stock costs issued to the contract');
INSERT INTO `scripts` VALUES ('ContractOtherReqts.php',4,'Creates the other requirements for a contract cost build up');
INSERT INTO `scripts` VALUES ('Contracts.php',6,'Creates or modifies a customer contract costing');
INSERT INTO `scripts` VALUES ('CopyBOM.php',9,'Allows a bill of material to be copied between items');
INSERT INTO `scripts` VALUES ('CostUpdate',10,'NB Not a script but allows users to maintain item costs from withing StockCostUpdate.php');
INSERT INTO `scripts` VALUES ('CounterReturns.php',5,'Allows credits and refunds from the default Counter Sale account for an inventory location');
INSERT INTO `scripts` VALUES ('CounterSales.php',1,'Allows sales to be entered against a cash sale customer account defined in the users location record');
INSERT INTO `scripts` VALUES ('CreditItemsControlled.php',3,'Specifies the batch references/serial numbers of items being credited back into stock');
INSERT INTO `scripts` VALUES ('CreditStatus.php',3,'Defines the credit status records. Each customer account is given a credit status from this table. Some credit status records can prohibit invoicing and new orders being entered.');
INSERT INTO `scripts` VALUES ('Credit_Invoice.php',3,'Creates a credit note based on the details of an existing invoice');
INSERT INTO `scripts` VALUES ('Currencies.php',9,'Defines the currencies available. Each customer and supplier must be defined as transacting in one of the currencies defined here.');
INSERT INTO `scripts` VALUES ('CustEDISetup.php',11,'Allows the set up the customer specified EDI parameters for server, email or ftp.');
INSERT INTO `scripts` VALUES ('CustItem.php',11,'Customer Items');
INSERT INTO `scripts` VALUES ('CustLoginSetup.php',15,'');
INSERT INTO `scripts` VALUES ('CustomerAllocations.php',3,'Allows customer receipts and credit notes to be allocated to sales invoices');
INSERT INTO `scripts` VALUES ('CustomerBalancesMovement.php',3,'Allow customers to be listed in local currency with balances and activity over a date range');
INSERT INTO `scripts` VALUES ('CustomerBranches.php',3,'Defines the details of customer branches such as delivery address and contact details - also sales area, representative etc');
INSERT INTO `scripts` VALUES ('CustomerInquiry.php',1,'Shows the customers account transactions with balances outstanding, links available to drill down to invoice/credit note or email invoices/credit notes');
INSERT INTO `scripts` VALUES ('CustomerPurchases.php',5,'Shows the purchases a customer has made.');
INSERT INTO `scripts` VALUES ('CustomerReceipt.php',3,'Entry of both customer receipts against accounts receivable and also general ledger or nominal receipts');
INSERT INTO `scripts` VALUES ('Customers.php',3,'Defines the setup of a customer account, including payment terms, billing address, credit status, currency etc');
INSERT INTO `scripts` VALUES ('CustomerTransInquiry.php',2,'Lists in html the sequence of customer transactions, invoices, credit notes or receipts by a user entered date range');
INSERT INTO `scripts` VALUES ('CustomerTypes.php',15,'');
INSERT INTO `scripts` VALUES ('CustWhereAlloc.php',2,'Shows to which invoices a receipt was allocated to');
INSERT INTO `scripts` VALUES ('DailyBankTransactions.php',8,'Allows you to view all bank transactions for a selected date range, and the inquiry can be filtered by matched or unmatched transactions, or all transactions can be chosen');
INSERT INTO `scripts` VALUES ('DailySalesInquiry.php',2,'Shows the daily sales with GP in a calendar format');
INSERT INTO `scripts` VALUES ('Dashboard.php',1,'Display outstanding debtors, creditors etc');
INSERT INTO `scripts` VALUES ('DebtorsAtPeriodEnd.php',2,'Shows the debtors control account as at a previous period end - based on system calendar monthly periods');
INSERT INTO `scripts` VALUES ('DeliveryDetails.php',1,'Used during order entry to allow the entry of delivery addresses other than the defaulted branch delivery address and information about carrier/shipping method etc');
INSERT INTO `scripts` VALUES ('Departments.php',1,'Create business departments');
INSERT INTO `scripts` VALUES ('DiscountCategories.php',11,'Defines the items belonging to a discount category. Discount Categories are used to allow discounts based on quantities across a range of producs');
INSERT INTO `scripts` VALUES ('DiscountMatrix.php',11,'Defines the rates of discount applicable to discount categories and the customer groupings to which the rates are to apply');
INSERT INTO `scripts` VALUES ('EDIMessageFormat.php',10,'Specifies the EDI message format used by a customer - administrator use only.');
INSERT INTO `scripts` VALUES ('EDIProcessOrders.php',11,'Processes incoming EDI orders into sales orders');
INSERT INTO `scripts` VALUES ('EDISendInvoices.php',15,'Processes invoiced EDI customer invoices into EDI messages and sends using the customers preferred method either ftp or email attachments.');
INSERT INTO `scripts` VALUES ('EmailConfirmation.php',2,'');
INSERT INTO `scripts` VALUES ('EmailCustTrans.php',2,'Emails selected invoice or credit to the customer');
INSERT INTO `scripts` VALUES ('ExchangeRateTrend.php',2,'Shows the trend in exchange rates as retrieved from ECB');
INSERT INTO `scripts` VALUES ('Factors.php',5,'Defines supplier factor companies');
INSERT INTO `scripts` VALUES ('FixedAssetCategories.php',11,'Defines the various categories of fixed assets');
INSERT INTO `scripts` VALUES ('FixedAssetDepreciation.php',10,'Calculates and creates GL transactions to post depreciation for a period');
INSERT INTO `scripts` VALUES ('FixedAssetItems.php',11,'Allows fixed assets to be defined');
INSERT INTO `scripts` VALUES ('FixedAssetLocations.php',11,'Allows the locations of fixed assets to be defined');
INSERT INTO `scripts` VALUES ('FixedAssetRegister.php',11,'Produces a csv, html or pdf report of the fixed assets over a period showing period depreciation, additions and disposals');
INSERT INTO `scripts` VALUES ('FixedAssetTransfer.php',11,'Allows the fixed asset locations to be changed in bulk');
INSERT INTO `scripts` VALUES ('FormDesigner.php',14,'');
INSERT INTO `scripts` VALUES ('FormMaker.php',1,'Allows running user defined Forms');
INSERT INTO `scripts` VALUES ('FreightCosts.php',11,'Defines the setup of the freight cost using different shipping methods to different destinations. The system can use this information to calculate applicable freight if the items are defined with the correct kgs and cubic volume');
INSERT INTO `scripts` VALUES ('FTP_RadioBeacon.php',2,'FTPs sales orders for dispatch to a radio beacon software enabled warehouse dispatching facility');
INSERT INTO `scripts` VALUES ('geocode.php',3,'');
INSERT INTO `scripts` VALUES ('GeocodeSetup.php',3,'');
INSERT INTO `scripts` VALUES ('geocode_genxml_customers.php',3,'');
INSERT INTO `scripts` VALUES ('geocode_genxml_suppliers.php',3,'');
INSERT INTO `scripts` VALUES ('geo_displaymap_customers.php',3,'');
INSERT INTO `scripts` VALUES ('geo_displaymap_suppliers.php',3,'');
INSERT INTO `scripts` VALUES ('GetStockImage.php',1,'');
INSERT INTO `scripts` VALUES ('GLAccountCSV.php',8,'Produces a CSV of the GL transactions for a particular range of periods and GL account');
INSERT INTO `scripts` VALUES ('GLAccountInquiry.php',8,'Shows the general ledger transactions for a specified account over a specified range of periods');
INSERT INTO `scripts` VALUES ('GLAccountReport.php',8,'Produces a report of the GL transactions for a particular account');
INSERT INTO `scripts` VALUES ('GLAccounts.php',10,'Defines the general ledger accounts');
INSERT INTO `scripts` VALUES ('GLBalanceSheet.php',8,'Shows the balance sheet for the company as at a specified date');
INSERT INTO `scripts` VALUES ('GLBudgets.php',10,'Defines GL Budgets');
INSERT INTO `scripts` VALUES ('GLCodesInquiry.php',8,'Shows the list of general ledger codes defined with account names and groupings');
INSERT INTO `scripts` VALUES ('GLJournal.php',10,'Entry of general ledger journals, periods are calculated based on the date entered here');
INSERT INTO `scripts` VALUES ('GLJournalInquiry.php',15,'General Ledger Journal Inquiry');
INSERT INTO `scripts` VALUES ('GLProfit_Loss.php',8,'Shows the profit and loss of the company for the range of periods entered');
INSERT INTO `scripts` VALUES ('GLTagProfit_Loss.php',8,'');
INSERT INTO `scripts` VALUES ('GLTags.php',10,'Allows GL tags to be defined');
INSERT INTO `scripts` VALUES ('GLTransInquiry.php',8,'Shows the general ledger journal created for the sub ledger transaction specified');
INSERT INTO `scripts` VALUES ('GLTrialBalance.php',8,'Shows the trial balance for the month and the for the period selected together with the budgeted trial balances');
INSERT INTO `scripts` VALUES ('GLTrialBalance_csv.php',8,'Produces a CSV of the Trial Balance for a particular period');
INSERT INTO `scripts` VALUES ('GoodsReceived.php',11,'Entry of items received against purchase orders');
INSERT INTO `scripts` VALUES ('GoodsReceivedControlled.php',11,'Entry of the serial numbers or batch references for controlled items received against purchase orders');
INSERT INTO `scripts` VALUES ('GoodsReceivedNotInvoiced.php',2,'Shows the list of goods received but not yet invoiced, both in supplier currency and home currency. Total in home curency should match the GL Account for Goods received not invoiced. Any discrepancy is due to multicurrency errors.');
INSERT INTO `scripts` VALUES ('HistoricalTestResults.php',16,'Historical Test Results');
INSERT INTO `scripts` VALUES ('ImportBankTrans.php',11,'Imports bank transactions');
INSERT INTO `scripts` VALUES ('ImportBankTransAnalysis.php',11,'Allows analysis of bank transactions being imported');
INSERT INTO `scripts` VALUES ('index.php',1,'The main menu from where all functions available to the user are accessed by clicking on the links');
INSERT INTO `scripts` VALUES ('InternalStockCategoriesByRole.php',15,'Maintains the stock categories to be used as internal for any user security role');
INSERT INTO `scripts` VALUES ('InternalStockRequest.php',1,'Create an internal stock request');
INSERT INTO `scripts` VALUES ('InternalStockRequestAuthorisation.php',1,'Authorise internal stock requests');
INSERT INTO `scripts` VALUES ('InternalStockRequestFulfill.php',1,'Fulfill an internal stock request');
INSERT INTO `scripts` VALUES ('InventoryPlanning.php',2,'Creates a pdf report showing the last 4 months use of items including as a component of assemblies together with stock quantity on hand, current demand for the item and current quantity on sales order.');
INSERT INTO `scripts` VALUES ('InventoryPlanningPrefSupplier.php',2,'Produces a report showing the inventory to be ordered by supplier');
INSERT INTO `scripts` VALUES ('InventoryPlanningPrefSupplier_CSV.php',2,'Inventory planning spreadsheet');
INSERT INTO `scripts` VALUES ('InventoryQuantities.php',2,'');
INSERT INTO `scripts` VALUES ('InventoryValuation.php',2,'Creates a pdf report showing the value of stock at standard cost for a range of product categories selected');
INSERT INTO `scripts` VALUES ('Labels.php',15,'Produces item pricing labels in a pdf from a range of selected criteria');
INSERT INTO `scripts` VALUES ('Locations.php',11,'Defines the inventory stocking locations or warehouses');
INSERT INTO `scripts` VALUES ('LocationUsers.php',15,'Allows users that have permission to access a location to be defined');
INSERT INTO `scripts` VALUES ('Logout.php',1,'Shows when the user logs out of webERP');
INSERT INTO `scripts` VALUES ('MailingGroupMaintenance.php',15,'Mainting mailing lists for items to mail');
INSERT INTO `scripts` VALUES ('MailInventoryValuation.php',1,'Meant to be run as a scheduled process to email the stock valuation off to a specified person. Creates the same stock valuation report as InventoryValuation.php');
INSERT INTO `scripts` VALUES ('MailSalesReport_csv.php',15,'Mailing the sales report');
INSERT INTO `scripts` VALUES ('MaintenanceReminders.php',1,'Sends email reminders for scheduled asset maintenance tasks');
INSERT INTO `scripts` VALUES ('MaintenanceTasks.php',1,'Allows set up and edit of scheduled maintenance tasks');
INSERT INTO `scripts` VALUES ('MaintenanceUserSchedule.php',1,'List users or managers scheduled maintenance tasks and allow to be flagged as completed');
INSERT INTO `scripts` VALUES ('Manufacturers.php',15,'Maintain brands of sales products');
INSERT INTO `scripts` VALUES ('MaterialsNotUsed.php',4,'Lists the items from Raw Material Categories not used in any BOM (thus, not used at all)');
INSERT INTO `scripts` VALUES ('MRP.php',9,'');
INSERT INTO `scripts` VALUES ('MRPCalendar.php',9,'');
INSERT INTO `scripts` VALUES ('MRPCreateDemands.php',9,'');
INSERT INTO `scripts` VALUES ('MRPDemands.php',9,'');
INSERT INTO `scripts` VALUES ('MRPDemandTypes.php',9,'');
INSERT INTO `scripts` VALUES ('MRPPlannedPurchaseOrders.php',2,'');
INSERT INTO `scripts` VALUES ('MRPPlannedWorkOrders.php',2,'');
INSERT INTO `scripts` VALUES ('MRPReport.php',2,'');
INSERT INTO `scripts` VALUES ('MRPReschedules.php',2,'');
INSERT INTO `scripts` VALUES ('MRPShortages.php',2,'');
INSERT INTO `scripts` VALUES ('NoSalesItems.php',2,'Shows the No Selling (worst) items');
INSERT INTO `scripts` VALUES ('OffersReceived.php',4,'');
INSERT INTO `scripts` VALUES ('OrderDetails.php',1,'Shows the detail of a sales order');
INSERT INTO `scripts` VALUES ('OrderEntryDiscountPricing',13,'Not a script but an authority level marker - required if the user is allowed to enter discounts and special pricing against a customer order');
INSERT INTO `scripts` VALUES ('OutstandingGRNs.php',2,'Creates a pdf showing all GRNs for which there has been no purchase invoice matched off against.');
INSERT INTO `scripts` VALUES ('PageSecurity.php',15,'');
INSERT INTO `scripts` VALUES ('PaymentAllocations.php',5,'');
INSERT INTO `scripts` VALUES ('PaymentMethods.php',15,'');
INSERT INTO `scripts` VALUES ('Payments.php',5,'Entry of bank account payments either against an AP account or a general ledger payment - if the AP-GL link in company preferences is set');
INSERT INTO `scripts` VALUES ('PaymentTerms.php',10,'Defines the payment terms records, these can be expressed as either a number of days credit or a day in the following month. All customers and suppliers must have a corresponding payment term recorded against their account');
INSERT INTO `scripts` VALUES ('PcAssignCashToTab.php',6,'');
INSERT INTO `scripts` VALUES ('PcAuthorizeExpenses.php',6,'');
INSERT INTO `scripts` VALUES ('PcClaimExpensesFromTab.php',6,'');
INSERT INTO `scripts` VALUES ('PcExpenses.php',15,'');
INSERT INTO `scripts` VALUES ('PcExpensesTypeTab.php',15,'');
INSERT INTO `scripts` VALUES ('PcReportTab.php',6,'');
INSERT INTO `scripts` VALUES ('PcTabs.php',15,'');
INSERT INTO `scripts` VALUES ('PcTypeTabs.php',15,'');
INSERT INTO `scripts` VALUES ('PDFBankingSummary.php',3,'Creates a pdf showing the amounts entered as receipts on a specified date together with references for the purposes of banking');
INSERT INTO `scripts` VALUES ('PDFChequeListing.php',3,'Creates a pdf showing all payments that have been made from a specified bank account over a specified period. This can be emailed to an email account defined in config.php - ie a financial controller');
INSERT INTO `scripts` VALUES ('PDFCOA.php',0,'PDF of COA');
INSERT INTO `scripts` VALUES ('PDFCustomerList.php',2,'Creates a report of the customer and branch information held. This report has options to print only customer branches in a specified sales area and sales person. Additional option allows to list only those customers with activity either under or over a specified amount, since a specified date.');
INSERT INTO `scripts` VALUES ('PDFCustTransListing.php',3,'');
INSERT INTO `scripts` VALUES ('PDFDeliveryDifferences.php',3,'Creates a pdf report listing the delivery differences from what the customer requested as recorded in the order entry. The report calculates a percentage of order fill based on the number of orders filled in full on time');
INSERT INTO `scripts` VALUES ('PDFDIFOT.php',3,'Produces a pdf showing the delivery in full on time performance');
INSERT INTO `scripts` VALUES ('PDFFGLabel.php',11,'Produces FG Labels');
INSERT INTO `scripts` VALUES ('PDFGLJournal.php',15,'General Ledger Journal Print');
INSERT INTO `scripts` VALUES ('PDFGrn.php',2,'Produces a GRN report on the receipt of stock');
INSERT INTO `scripts` VALUES ('PDFLowGP.php',2,'Creates a pdf report showing the low gross profit sales made in the selected date range. The percentage of gp deemed acceptable can also be entered');
INSERT INTO `scripts` VALUES ('PDFOrdersInvoiced.php',3,'Produces a pdf of orders invoiced based on selected criteria');
INSERT INTO `scripts` VALUES ('PDFOrderStatus.php',3,'Reports on sales order status by date range, by stock location and stock category - producing a pdf showing each line items and any quantites delivered');
INSERT INTO `scripts` VALUES ('PDFPeriodStockTransListing.php',3,'Allows stock transactions of a specific transaction type to be listed over a single day or period range');
INSERT INTO `scripts` VALUES ('PDFPickingList.php',2,'');
INSERT INTO `scripts` VALUES ('PDFPriceList.php',2,'Creates a pdf of the price list applicable to a given sales type and customer. Also allows the listing of prices specific to a customer');
INSERT INTO `scripts` VALUES ('PDFPrintLabel.php',10,'');
INSERT INTO `scripts` VALUES ('PDFProdSpec.php',0,'PDF OF Product Specification');
INSERT INTO `scripts` VALUES ('PDFQALabel.php',2,'Produces a QA label on receipt of stock');
INSERT INTO `scripts` VALUES ('PDFQuotation.php',2,'');
INSERT INTO `scripts` VALUES ('PDFQuotationPortrait.php',2,'Portrait quotation');
INSERT INTO `scripts` VALUES ('PDFReceipt.php',2,'');
INSERT INTO `scripts` VALUES ('PDFRemittanceAdvice.php',2,'');
INSERT INTO `scripts` VALUES ('PDFSellThroughSupportClaim.php',9,'Reports the sell through support claims to be made against all suppliers for a given date range.');
INSERT INTO `scripts` VALUES ('PDFStockCheckComparison.php',2,'Creates a pdf comparing the quantites entered as counted at a given range of locations against the quantity stored as on hand as at the time a stock check was initiated.');
INSERT INTO `scripts` VALUES ('PDFStockLocTransfer.php',1,'Creates a stock location transfer docket for the selected location transfer reference number');
INSERT INTO `scripts` VALUES ('PDFStockNegatives.php',1,'Produces a pdf of the negative stocks by location');
INSERT INTO `scripts` VALUES ('PDFStockTransfer.php',2,'Produces a report for stock transfers');
INSERT INTO `scripts` VALUES ('PDFSuppTransListing.php',3,'');
INSERT INTO `scripts` VALUES ('PDFTestPlan.php',16,'PDF of Test Plan');
INSERT INTO `scripts` VALUES ('PDFTopItems.php',2,'Produces a pdf report of the top items sold');
INSERT INTO `scripts` VALUES ('PDFWOPrint.php',11,'Produces W/O Paperwork');
INSERT INTO `scripts` VALUES ('PeriodsInquiry.php',2,'Shows a list of all the system defined periods');
INSERT INTO `scripts` VALUES ('POReport.php',2,'');
INSERT INTO `scripts` VALUES ('PO_AuthorisationLevels.php',15,'');
INSERT INTO `scripts` VALUES ('PO_AuthoriseMyOrders.php',4,'');
INSERT INTO `scripts` VALUES ('PO_Header.php',4,'Entry of a purchase order header record - date, references buyer etc');
INSERT INTO `scripts` VALUES ('PO_Items.php',4,'Entry of a purchase order items - allows entry of items with lookup of currency cost from Purchasing Data previously entered also allows entry of nominal items against a general ledger code if the AP is integrated to the GL');
INSERT INTO `scripts` VALUES ('PO_OrderDetails.php',2,'Purchase order inquiry shows the quantity received and invoiced of purchase order items as well as the header information');
INSERT INTO `scripts` VALUES ('PO_PDFPurchOrder.php',2,'Creates a pdf of the selected purchase order for printing or email to one of the supplier contacts entered');
INSERT INTO `scripts` VALUES ('PO_SelectOSPurchOrder.php',2,'Shows the outstanding purchase orders for selecting with links to receive or modify the purchase order header and items');
INSERT INTO `scripts` VALUES ('PO_SelectPurchOrder.php',2,'Allows selection of any purchase order with links to the inquiry');
INSERT INTO `scripts` VALUES ('PriceMatrix.php',11,'Mantain stock prices according to quantity break and sales types');
INSERT INTO `scripts` VALUES ('Prices.php',9,'Entry of prices for a selected item also allows selection of sales type and currency for the price');
INSERT INTO `scripts` VALUES ('PricesBasedOnMarkUp.php',11,'');
INSERT INTO `scripts` VALUES ('PricesByCost.php',11,'Allows prices to be updated based on cost');
INSERT INTO `scripts` VALUES ('Prices_Customer.php',11,'Entry of prices for a selected item and selected customer/branch. The currency and sales type is defaulted from the customer\'s record');
INSERT INTO `scripts` VALUES ('PrintCheque.php',5,'');
INSERT INTO `scripts` VALUES ('PrintCustOrder.php',2,'Creates a pdf of the dispatch note - by default this is expected to be on two part pre-printed stationery to allow pickers to note discrepancies for the confirmer to update the dispatch at the time of invoicing');
INSERT INTO `scripts` VALUES ('PrintCustOrder_generic.php',2,'Creates two copies of a laser printed dispatch note - both copies need to be written on by the pickers with any discrepancies to advise customer of any shortfall and on the office copy to ensure the correct quantites are invoiced');
INSERT INTO `scripts` VALUES ('PrintCustStatements.php',2,'Creates a pdf for the customer statements in the selected range');
INSERT INTO `scripts` VALUES ('PrintCustTrans.php',1,'Creates either a html invoice or credit note or a pdf. A range of invoices or credit notes can be selected also.');
INSERT INTO `scripts` VALUES ('PrintCustTransPortrait.php',1,'');
INSERT INTO `scripts` VALUES ('PrintSalesOrder_generic.php',2,'');
INSERT INTO `scripts` VALUES ('PrintWOItemSlip.php',4,'PDF WO Item production Slip ');
INSERT INTO `scripts` VALUES ('ProductSpecs.php',16,'Product Specification Maintenance');
INSERT INTO `scripts` VALUES ('PurchaseByPrefSupplier.php',2,'Purchase ordering by preferred supplier');
INSERT INTO `scripts` VALUES ('PurchData.php',4,'Entry of supplier purchasing data, the suppliers part reference and the suppliers currency cost of the item');
INSERT INTO `scripts` VALUES ('QATests.php',16,'Quality Test Maintenance');
INSERT INTO `scripts` VALUES ('RecurringSalesOrders.php',1,'');
INSERT INTO `scripts` VALUES ('RecurringSalesOrdersProcess.php',1,'Process Recurring Sales Orders');
INSERT INTO `scripts` VALUES ('RelatedItemsUpdate.php',2,'Maintains Related Items');
INSERT INTO `scripts` VALUES ('ReorderLevel.php',2,'Allows reorder levels of inventory to be updated');
INSERT INTO `scripts` VALUES ('ReorderLevelLocation.php',2,'');
INSERT INTO `scripts` VALUES ('ReportCreator.php',13,'Report Writer and Form Creator script that creates templates for user defined reports and forms');
INSERT INTO `scripts` VALUES ('ReportMaker.php',1,'Produces reports from the report writer templates created');
INSERT INTO `scripts` VALUES ('reportwriter/admin/ReportCreator.php',15,'Report Writer');
INSERT INTO `scripts` VALUES ('ReprintGRN.php',11,'Allows selection of a goods received batch for reprinting the goods received note given a purchase order number');
INSERT INTO `scripts` VALUES ('ReverseGRN.php',11,'Reverses the entry of goods received - creating stock movements back out and necessary general ledger journals to effect the reversal');
INSERT INTO `scripts` VALUES ('RevisionTranslations.php',15,'Human revision for automatic descriptions translations');
INSERT INTO `scripts` VALUES ('SalesAnalReptCols.php',2,'Entry of the definition of a sales analysis report\'s columns.');
INSERT INTO `scripts` VALUES ('SalesAnalRepts.php',2,'Entry of the definition of a sales analysis report headers');
INSERT INTO `scripts` VALUES ('SalesAnalysis_UserDefined.php',2,'Creates a pdf of a selected user defined sales analysis report');
INSERT INTO `scripts` VALUES ('SalesByTypePeriodInquiry.php',2,'Shows sales for a selected date range by sales type/price list');
INSERT INTO `scripts` VALUES ('SalesCategories.php',11,'');
INSERT INTO `scripts` VALUES ('SalesCategoryDescriptions.php',15,'Maintain translations for sales categories');
INSERT INTO `scripts` VALUES ('SalesCategoryPeriodInquiry.php',2,'Shows sales for a selected date range by stock category');
INSERT INTO `scripts` VALUES ('SalesGLPostings.php',10,'Defines the general ledger accounts used to post sales to based on product categories and sales areas');
INSERT INTO `scripts` VALUES ('SalesGraph.php',6,'');
INSERT INTO `scripts` VALUES ('SalesInquiry.php',2,'');
INSERT INTO `scripts` VALUES ('SalesPeople.php',3,'Defines the sales people of the business');
INSERT INTO `scripts` VALUES ('SalesTopCustomersInquiry.php',1,'Shows the top customers');
INSERT INTO `scripts` VALUES ('SalesTopItemsInquiry.php',2,'Shows the top item sales for a selected date range');
INSERT INTO `scripts` VALUES ('SalesTypes.php',15,'Defines the sales types - prices are held against sales types they can be considered price lists. Sales analysis records are held by sales type too.');
INSERT INTO `scripts` VALUES ('SecurityTokens.php',15,'Administration of security tokens');
INSERT INTO `scripts` VALUES ('SelectAsset.php',2,'Allows a fixed asset to be selected for modification or viewing');
INSERT INTO `scripts` VALUES ('SelectCompletedOrder.php',1,'Allows the selection of completed sales orders for inquiries - choices to select by item code or customer');
INSERT INTO `scripts` VALUES ('SelectContract.php',6,'Allows a contract costing to be selected for modification or viewing');
INSERT INTO `scripts` VALUES ('SelectCreditItems.php',3,'Entry of credit notes from scratch, selecting the items in either quick entry mode or searching for them manually');
INSERT INTO `scripts` VALUES ('SelectCustomer.php',2,'Selection of customer - from where all customer related maintenance, transactions and inquiries start');
INSERT INTO `scripts` VALUES ('SelectGLAccount.php',8,'Selection of general ledger account from where all general ledger account maintenance, or inquiries are initiated');
INSERT INTO `scripts` VALUES ('SelectOrderItems.php',1,'Entry of sales order items with both quick entry and part search functions');
INSERT INTO `scripts` VALUES ('SelectProduct.php',2,'Selection of items. All item maintenance, transactions and inquiries start with this script');
INSERT INTO `scripts` VALUES ('SelectQASamples.php',16,'Select  QA Samples');
INSERT INTO `scripts` VALUES ('SelectRecurringSalesOrder.php',2,'');
INSERT INTO `scripts` VALUES ('SelectSalesOrder.php',2,'Selects a sales order irrespective of completed or not for inquiries');
INSERT INTO `scripts` VALUES ('SelectSupplier.php',2,'Selects a supplier. A supplier is required to be selected before any AP transactions and before any maintenance or inquiry of the supplier');
INSERT INTO `scripts` VALUES ('SelectWorkOrder.php',2,'');
INSERT INTO `scripts` VALUES ('SellThroughSupport.php',9,'Defines the items, period and quantum of support for which supplier has agreed to provide.');
INSERT INTO `scripts` VALUES ('ShipmentCosting.php',11,'Shows the costing of a shipment with all the items invoice values and any shipment costs apportioned. Updating the shipment has an option to update standard costs of all items on the shipment and create any general ledger variance journals');
INSERT INTO `scripts` VALUES ('Shipments.php',11,'Entry of shipments from outstanding purchase orders for a selected supplier - changes in the delivery date will cascade into the different purchase orders on the shipment');
INSERT INTO `scripts` VALUES ('Shippers.php',15,'Defines the shipping methods available. Each customer branch has a default shipping method associated with it which must match a record from this table');
INSERT INTO `scripts` VALUES ('ShiptsList.php',2,'Shows a list of all the open shipments for a selected supplier. Linked from POItems.php');
INSERT INTO `scripts` VALUES ('Shipt_Select.php',11,'Selection of a shipment for displaying and modification or updating');
INSERT INTO `scripts` VALUES ('ShopParameters.php',15,'Maintain web-store configuration and set up');
INSERT INTO `scripts` VALUES ('SMTPServer.php',15,'');
INSERT INTO `scripts` VALUES ('SpecialOrder.php',4,'Allows for a sales order to be created and an indent order to be created on a supplier for a one off item that may never be purchased again. A dummy part is created based on the description and cost details given.');
INSERT INTO `scripts` VALUES ('StockAdjustments.php',11,'Entry of quantity corrections to stocks in a selected location.');
INSERT INTO `scripts` VALUES ('StockAdjustmentsControlled.php',11,'Entry of batch references or serial numbers on controlled stock items being adjusted');
INSERT INTO `scripts` VALUES ('StockCategories.php',11,'Defines the stock categories. All items must refer to one of these categories. The category record also allows the specification of the general ledger codes where stock items are to be posted - the balance sheet account and the profit and loss effect of any adjustments and the profit and loss effect of any price variances');
INSERT INTO `scripts` VALUES ('StockCheck.php',2,'Allows creation of a stock check file - copying the current quantites in stock for later comparison to the entered counts. Also produces a pdf for the count sheets.');
INSERT INTO `scripts` VALUES ('StockClone.php',11,'Script to copy a stock item and associated properties, image, price, purchase and cost data');
INSERT INTO `scripts` VALUES ('StockCostUpdate.php',9,'Allows update of the standard cost of items producing general ledger journals if the company preferences stock GL interface is active');
INSERT INTO `scripts` VALUES ('StockCounts.php',2,'Allows entry of stock counts');
INSERT INTO `scripts` VALUES ('StockDispatch.php',2,'');
INSERT INTO `scripts` VALUES ('StockLocMovements.php',2,'Inquiry shows the Movements of all stock items for a specified location');
INSERT INTO `scripts` VALUES ('StockLocStatus.php',2,'Shows the stock on hand together with outstanding sales orders and outstanding purchase orders by stock location for all items in the selected stock category');
INSERT INTO `scripts` VALUES ('StockLocTransfer.php',11,'Entry of a bulk stock location transfer for many parts from one location to another.');
INSERT INTO `scripts` VALUES ('StockLocTransferReceive.php',11,'Effects the transfer and creates the stock movements for a bulk stock location transfer initiated from StockLocTransfer.php');
INSERT INTO `scripts` VALUES ('StockMovements.php',2,'Shows a list of all the stock movements for a selected item and stock location including the price at which they were sold in local currency and the price at which they were purchased for in local currency');
INSERT INTO `scripts` VALUES ('StockQties_csv.php',5,'Makes a comma separated values (CSV)file of the stock item codes and quantities');
INSERT INTO `scripts` VALUES ('StockQuantityByDate.php',2,'Shows the stock on hand for each item at a selected location and stock category as at a specified date');
INSERT INTO `scripts` VALUES ('StockReorderLevel.php',4,'Entry and review of the re-order level of items by stocking location');
INSERT INTO `scripts` VALUES ('Stocks.php',11,'Defines an item - maintenance and addition of new parts');
INSERT INTO `scripts` VALUES ('StockSerialItemResearch.php',3,'');
INSERT INTO `scripts` VALUES ('StockSerialItems.php',2,'Shows a list of the serial numbers or the batch references and quantities of controlled items. This inquiry is linked from the stock status inquiry');
INSERT INTO `scripts` VALUES ('StockStatus.php',2,'Shows the stock on hand together with outstanding sales orders and outstanding purchase orders by stock location for a selected part. Has a link to show the serial numbers in stock at the location selected if the item is controlled');
INSERT INTO `scripts` VALUES ('StockTransferControlled.php',11,'Entry of serial numbers/batch references for controlled items being received on a stock transfer. The script is used by both bulk transfers and point to point transfers');
INSERT INTO `scripts` VALUES ('StockTransfers.php',11,'Entry of point to point stock location transfers of a single part');
INSERT INTO `scripts` VALUES ('StockUsage.php',2,'Inquiry showing the quantity of stock used by period calculated from the sum of the stock movements over that period - by item and stock location. Also available over all locations');
INSERT INTO `scripts` VALUES ('StockUsageGraph.php',2,'');
INSERT INTO `scripts` VALUES ('SuppContractChgs.php',5,'');
INSERT INTO `scripts` VALUES ('SuppCreditGRNs.php',5,'Entry of a supplier credit notes (debit notes) against existing GRN which have already been matched in full or in part');
INSERT INTO `scripts` VALUES ('SuppFixedAssetChgs.php',5,'');
INSERT INTO `scripts` VALUES ('SuppInvGRNs.php',5,'Entry of supplier invoices against goods received');
INSERT INTO `scripts` VALUES ('SupplierAllocations.php',5,'Entry of allocations of supplier payments and credit notes to invoices');
INSERT INTO `scripts` VALUES ('SupplierBalsAtPeriodEnd.php',2,'');
INSERT INTO `scripts` VALUES ('SupplierContacts.php',5,'Entry of supplier contacts and contact details including email addresses');
INSERT INTO `scripts` VALUES ('SupplierCredit.php',5,'Entry of supplier credit notes (debit notes)');
INSERT INTO `scripts` VALUES ('SupplierInquiry.php',2,'Inquiry showing invoices, credit notes and payments made to suppliers together with the amounts outstanding');
INSERT INTO `scripts` VALUES ('SupplierInvoice.php',5,'Entry of supplier invoices');
INSERT INTO `scripts` VALUES ('SupplierPriceList.php',4,'Maintain Supplier Price Lists');
INSERT INTO `scripts` VALUES ('Suppliers.php',5,'Entry of new suppliers and maintenance of existing suppliers');
INSERT INTO `scripts` VALUES ('SupplierTenderCreate.php',4,'Create or Edit tenders');
INSERT INTO `scripts` VALUES ('SupplierTenders.php',9,'');
INSERT INTO `scripts` VALUES ('SupplierTransInquiry.php',2,'');
INSERT INTO `scripts` VALUES ('SupplierTypes.php',4,'');
INSERT INTO `scripts` VALUES ('SuppLoginSetup.php',15,'');
INSERT INTO `scripts` VALUES ('SuppPaymentRun.php',5,'Automatic creation of payment records based on calculated amounts due from AP invoices entered');
INSERT INTO `scripts` VALUES ('SuppPriceList.php',2,'');
INSERT INTO `scripts` VALUES ('SuppShiptChgs.php',5,'Entry of supplier invoices against shipments as charges against a shipment');
INSERT INTO `scripts` VALUES ('SuppTransGLAnalysis.php',5,'Entry of supplier invoices against general ledger codes');
INSERT INTO `scripts` VALUES ('SystemParameters.php',15,'');
INSERT INTO `scripts` VALUES ('Tax.php',2,'Creates a report of the ad-valoerm tax - GST/VAT - for the period selected from accounts payable and accounts receivable data');
INSERT INTO `scripts` VALUES ('TaxAuthorities.php',15,'Entry of tax authorities - the state intitutions that charge tax');
INSERT INTO `scripts` VALUES ('TaxAuthorityRates.php',11,'Entry of the rates of tax applicable to the tax authority depending on the item tax level');
INSERT INTO `scripts` VALUES ('TaxCategories.php',15,'Allows for categories of items to be defined that might have different tax rates applied to them');
INSERT INTO `scripts` VALUES ('TaxGroups.php',15,'Allows for taxes to be grouped together where multiple taxes might apply on sale or purchase of items');
INSERT INTO `scripts` VALUES ('TaxProvinces.php',15,'Allows for inventory locations to be defined so that tax applicable from sales in different provinces can be dealt with');
INSERT INTO `scripts` VALUES ('TestPlanResults.php',16,'Test Plan Results Entry');
INSERT INTO `scripts` VALUES ('TopItems.php',2,'Shows the top selling items');
INSERT INTO `scripts` VALUES ('UnitsOfMeasure.php',15,'Allows for units of measure to be defined');
INSERT INTO `scripts` VALUES ('UpgradeDatabase.php',15,'Allows for the database to be automatically upgraded based on currently recorded DBUpgradeNumber config option');
INSERT INTO `scripts` VALUES ('UserLocations.php',15,'Location User Maintenance');
INSERT INTO `scripts` VALUES ('UserSettings.php',1,'Allows the user to change system wide defaults for the theme - appearance, the number of records to show in searches and the language to display messages in');
INSERT INTO `scripts` VALUES ('WhereUsedInquiry.php',2,'Inquiry showing where an item is used ie all the parents where the item is a component of');
INSERT INTO `scripts` VALUES ('WOCanBeProducedNow.php',4,'List of WO items that can be produced with available stock in location');
INSERT INTO `scripts` VALUES ('WorkCentres.php',9,'Defines the various centres of work within a manufacturing company. Also the overhead and labour rates applicable to the work centre and its standard capacity');
INSERT INTO `scripts` VALUES ('WorkOrderCosting.php',11,'');
INSERT INTO `scripts` VALUES ('WorkOrderEntry.php',10,'Entry of new work orders');
INSERT INTO `scripts` VALUES ('WorkOrderIssue.php',11,'Issue of materials to a work order');
INSERT INTO `scripts` VALUES ('WorkOrderReceive.php',11,'Allows for receiving of works orders');
INSERT INTO `scripts` VALUES ('WorkOrderStatus.php',11,'Shows the status of works orders');
INSERT INTO `scripts` VALUES ('WOSerialNos.php',10,'');
INSERT INTO `scripts` VALUES ('WWW_Access.php',15,'');
INSERT INTO `scripts` VALUES ('WWW_Users.php',15,'Entry of users and security settings of users');
INSERT INTO `scripts` VALUES ('Z_BottomUpCosts.php',15,'');
INSERT INTO `scripts` VALUES ('Z_ChangeBranchCode.php',15,'Utility to change the branch code of a customer that cascades the change through all the necessary tables');
INSERT INTO `scripts` VALUES ('Z_ChangeCustomerCode.php',15,'Utility to change a customer code that cascades the change through all the necessary tables');
INSERT INTO `scripts` VALUES ('Z_ChangeGLAccountCode.php',15,'Script to change a GL account code accross all tables necessary');
INSERT INTO `scripts` VALUES ('Z_ChangeLocationCode.php',15,'Change a locations code and in all tables where the old code was used to the new code');
INSERT INTO `scripts` VALUES ('Z_ChangeStockCategory.php',15,'');
INSERT INTO `scripts` VALUES ('Z_ChangeStockCode.php',15,'Utility to change an item code that cascades the change through all the necessary tables');
INSERT INTO `scripts` VALUES ('Z_ChangeSupplierCode.php',15,'Script to change a supplier code accross all tables necessary');
INSERT INTO `scripts` VALUES ('Z_CheckAllocationsFrom.php',15,'');
INSERT INTO `scripts` VALUES ('Z_CheckAllocs.php',2,'');
INSERT INTO `scripts` VALUES ('Z_CheckDebtorsControl.php',15,'Inquiry that shows the total local currency (functional currency) balance of all customer accounts to reconcile with the general ledger debtors account');
INSERT INTO `scripts` VALUES ('Z_CheckGLTransBalance.php',15,'Checks all GL transactions balance and reports problem ones');
INSERT INTO `scripts` VALUES ('Z_CreateChartDetails.php',9,'Utility page to create chart detail records for all general ledger accounts and periods created - needs expert assistance in use');
INSERT INTO `scripts` VALUES ('Z_CreateCompany.php',15,'Utility to insert company number 1 if not already there - actually only company 1 is used - the system is not multi-company');
INSERT INTO `scripts` VALUES ('Z_CreateCompanyTemplateFile.php',15,'');
INSERT INTO `scripts` VALUES ('Z_CurrencyDebtorsBalances.php',15,'Inquiry that shows the total foreign currency together with the total local currency (functional currency) balances of all customer accounts to reconcile with the general ledger debtors account');
INSERT INTO `scripts` VALUES ('Z_CurrencySuppliersBalances.php',15,'Inquiry that shows the total foreign currency amounts and also the local currency (functional currency) balances of all supplier accounts to reconcile with the general ledger creditors account');
INSERT INTO `scripts` VALUES ('Z_DataExport.php',15,'');
INSERT INTO `scripts` VALUES ('Z_DeleteCreditNote.php',15,'Utility to reverse a customer credit note - a desperate measure that should not be used except in extreme circumstances');
INSERT INTO `scripts` VALUES ('Z_DeleteInvoice.php',15,'Utility to reverse a customer invoice - a desperate measure that should not be used except in extreme circumstances');
INSERT INTO `scripts` VALUES ('Z_DeleteOldPrices.php',15,'Deletes all old prices');
INSERT INTO `scripts` VALUES ('Z_DeleteSalesTransActions.php',15,'Utility to delete all sales transactions, sales analysis the lot! Extreme care required!!!');
INSERT INTO `scripts` VALUES ('Z_DescribeTable.php',11,'');
INSERT INTO `scripts` VALUES ('Z_ImportChartOfAccounts.php',11,'');
INSERT INTO `scripts` VALUES ('Z_ImportDebtors.php',15,'Import debtors by csv file');
INSERT INTO `scripts` VALUES ('Z_ImportFixedAssets.php',15,'Allow fixed assets to be imported from a csv');
INSERT INTO `scripts` VALUES ('Z_ImportGLAccountGroups.php',11,'');
INSERT INTO `scripts` VALUES ('Z_ImportGLAccountSections.php',11,'');
INSERT INTO `scripts` VALUES ('Z_ImportGLTransactions.php',15,'Import General Ledger Transactions');
INSERT INTO `scripts` VALUES ('Z_ImportPartCodes.php',11,'Allows inventory items to be imported from a csv');
INSERT INTO `scripts` VALUES ('Z_ImportPriceList.php',15,'Loads a new price list from a csv file');
INSERT INTO `scripts` VALUES ('Z_ImportStocks.php',15,'');
INSERT INTO `scripts` VALUES ('Z_index.php',15,'Utility menu page');
INSERT INTO `scripts` VALUES ('Z_ItemsWithoutPicture.php',15,'Shows the list of curent items without picture in webERP');
INSERT INTO `scripts` VALUES ('Z_MakeLocUsers.php',15,'Create User Location records');
INSERT INTO `scripts` VALUES ('Z_MakeNewCompany.php',15,'');
INSERT INTO `scripts` VALUES ('Z_MakeStockLocns.php',15,'Utility to make LocStock records for all items and locations if not already set up.');
INSERT INTO `scripts` VALUES ('Z_poAddLanguage.php',15,'Allows a new language po file to be created');
INSERT INTO `scripts` VALUES ('Z_poAdmin.php',15,'Allows for a gettext language po file to be administered');
INSERT INTO `scripts` VALUES ('Z_poEditLangHeader.php',15,'');
INSERT INTO `scripts` VALUES ('Z_poEditLangModule.php',15,'');
INSERT INTO `scripts` VALUES ('Z_poEditLangRemaining.php',15,'');
INSERT INTO `scripts` VALUES ('Z_poRebuildDefault.php',15,'');
INSERT INTO `scripts` VALUES ('Z_PriceChanges.php',15,'Utility to make bulk pricing alterations to selected sales type price lists or selected customer prices only');
INSERT INTO `scripts` VALUES ('Z_ReApplyCostToSA.php',15,'Utility to allow the sales analysis table to be updated with the latest cost information - the sales analysis takes the cost at the time the sale was made to reconcile with the enteries made in the gl.');
INSERT INTO `scripts` VALUES ('Z_RePostGLFromPeriod.php',15,'Utility to repost all general ledger transaction commencing from a specified period. This can take some time in busy environments. Normally GL transactions are posted automatically each time a trial balance or profit and loss account is run');
INSERT INTO `scripts` VALUES ('Z_ReverseSuppPaymentRun.php',15,'Utility to reverse an entire Supplier payment run');
INSERT INTO `scripts` VALUES ('Z_SalesIntegrityCheck.php',15,'');
INSERT INTO `scripts` VALUES ('Z_UpdateChartDetailsBFwd.php',15,'Utility to recalculate the ChartDetails table B/Fwd balances - extreme care!!');
INSERT INTO `scripts` VALUES ('Z_UpdateItemCosts.php',15,'Use CSV of item codes and costs to update webERP item costs');
INSERT INTO `scripts` VALUES ('Z_UpdateSalesAnalysisWithLatestCustomerData.php',15,'Updates the salesanalysis table with the latest data from the customer debtorsmaster salestype and custbranch sales area and sales person irrespective of the sales type, area, salesperson at the time when the sale was made');
INSERT INTO `scripts` VALUES ('Z_Upgrade3.10.php',15,'');
INSERT INTO `scripts` VALUES ('Z_Upgrade_3.01-3.02.php',15,'');
INSERT INTO `scripts` VALUES ('Z_Upgrade_3.04-3.05.php',15,'');
INSERT INTO `scripts` VALUES ('Z_Upgrade_3.05-3.06.php',15,'');
INSERT INTO `scripts` VALUES ('Z_Upgrade_3.07-3.08.php',15,'');
INSERT INTO `scripts` VALUES ('Z_Upgrade_3.08-3.09.php',15,'');
INSERT INTO `scripts` VALUES ('Z_Upgrade_3.09-3.10.php',15,'');
INSERT INTO `scripts` VALUES ('Z_Upgrade_3.10-3.11.php',15,'');
INSERT INTO `scripts` VALUES ('Z_Upgrade_3.11-4.00.php',15,'');
INSERT INTO `scripts` VALUES ('Z_UploadForm.php',15,'Utility to upload a file to a remote server');
INSERT INTO `scripts` VALUES ('Z_UploadResult.php',15,'Utility to upload a file to a remote server');

--
-- Dumping data for table `securitygroups`
--

INSERT INTO `securitygroups` VALUES (1,0);
INSERT INTO `securitygroups` VALUES (1,1);
INSERT INTO `securitygroups` VALUES (1,2);
INSERT INTO `securitygroups` VALUES (1,5);
INSERT INTO `securitygroups` VALUES (2,0);
INSERT INTO `securitygroups` VALUES (2,1);
INSERT INTO `securitygroups` VALUES (2,2);
INSERT INTO `securitygroups` VALUES (2,11);
INSERT INTO `securitygroups` VALUES (3,0);
INSERT INTO `securitygroups` VALUES (3,1);
INSERT INTO `securitygroups` VALUES (3,2);
INSERT INTO `securitygroups` VALUES (3,3);
INSERT INTO `securitygroups` VALUES (3,4);
INSERT INTO `securitygroups` VALUES (3,5);
INSERT INTO `securitygroups` VALUES (3,11);
INSERT INTO `securitygroups` VALUES (4,0);
INSERT INTO `securitygroups` VALUES (4,1);
INSERT INTO `securitygroups` VALUES (4,2);
INSERT INTO `securitygroups` VALUES (4,5);
INSERT INTO `securitygroups` VALUES (5,0);
INSERT INTO `securitygroups` VALUES (5,1);
INSERT INTO `securitygroups` VALUES (5,2);
INSERT INTO `securitygroups` VALUES (5,3);
INSERT INTO `securitygroups` VALUES (5,11);
INSERT INTO `securitygroups` VALUES (6,0);
INSERT INTO `securitygroups` VALUES (6,1);
INSERT INTO `securitygroups` VALUES (6,2);
INSERT INTO `securitygroups` VALUES (6,3);
INSERT INTO `securitygroups` VALUES (6,4);
INSERT INTO `securitygroups` VALUES (6,5);
INSERT INTO `securitygroups` VALUES (6,6);
INSERT INTO `securitygroups` VALUES (6,7);
INSERT INTO `securitygroups` VALUES (6,8);
INSERT INTO `securitygroups` VALUES (6,9);
INSERT INTO `securitygroups` VALUES (6,10);
INSERT INTO `securitygroups` VALUES (6,11);
INSERT INTO `securitygroups` VALUES (7,0);
INSERT INTO `securitygroups` VALUES (7,1);
INSERT INTO `securitygroups` VALUES (8,0);
INSERT INTO `securitygroups` VALUES (8,1);
INSERT INTO `securitygroups` VALUES (8,2);
INSERT INTO `securitygroups` VALUES (8,3);
INSERT INTO `securitygroups` VALUES (8,4);
INSERT INTO `securitygroups` VALUES (8,5);
INSERT INTO `securitygroups` VALUES (8,6);
INSERT INTO `securitygroups` VALUES (8,7);
INSERT INTO `securitygroups` VALUES (8,8);
INSERT INTO `securitygroups` VALUES (8,9);
INSERT INTO `securitygroups` VALUES (8,10);
INSERT INTO `securitygroups` VALUES (8,11);
INSERT INTO `securitygroups` VALUES (8,12);
INSERT INTO `securitygroups` VALUES (8,13);
INSERT INTO `securitygroups` VALUES (8,14);
INSERT INTO `securitygroups` VALUES (8,15);
INSERT INTO `securitygroups` VALUES (8,16);
INSERT INTO `securitygroups` VALUES (9,0);
INSERT INTO `securitygroups` VALUES (9,9);

--
-- Dumping data for table `securityroles`
--

INSERT INTO `securityroles` VALUES (1,'Inquiries/Order Entry');
INSERT INTO `securityroles` VALUES (2,'Manufac/Stock Admin');
INSERT INTO `securityroles` VALUES (3,'Purchasing Officer');
INSERT INTO `securityroles` VALUES (4,'AP Clerk');
INSERT INTO `securityroles` VALUES (5,'AR Clerk');
INSERT INTO `securityroles` VALUES (6,'Accountant');
INSERT INTO `securityroles` VALUES (7,'Customer Log On Only');
INSERT INTO `securityroles` VALUES (8,'System Administrator');
INSERT INTO `securityroles` VALUES (9,'Supplier Log On Only');

--
-- Dumping data for table `securitytokens`
--

INSERT INTO `securitytokens` VALUES (0,'Main Index Page');
INSERT INTO `securitytokens` VALUES (1,'Order Entry/Inquiries customer access only');
INSERT INTO `securitytokens` VALUES (2,'Basic Reports and Inquiries with selection options');
INSERT INTO `securitytokens` VALUES (3,'Credit notes and AR management');
INSERT INTO `securitytokens` VALUES (4,'Purchasing data/PO Entry/Reorder Levels');
INSERT INTO `securitytokens` VALUES (5,'Accounts Payable');
INSERT INTO `securitytokens` VALUES (6,'Petty Cash');
INSERT INTO `securitytokens` VALUES (7,'Bank Reconciliations');
INSERT INTO `securitytokens` VALUES (8,'General ledger reports/inquiries');
INSERT INTO `securitytokens` VALUES (9,'Supplier centre - Supplier access only');
INSERT INTO `securitytokens` VALUES (10,'General Ledger Maintenance, stock valuation & Configuration');
INSERT INTO `securitytokens` VALUES (11,'Inventory Management and Pricing');
INSERT INTO `securitytokens` VALUES (12,'Prices Security');
INSERT INTO `securitytokens` VALUES (13,'Customer services Price modifications');
INSERT INTO `securitytokens` VALUES (14,'Unknown');
INSERT INTO `securitytokens` VALUES (15,'User Management and System Administration');
INSERT INTO `securitytokens` VALUES (16,'QA');

--
-- Dumping data for table `sellthroughsupport`
--

INSERT INTO `sellthroughsupport` VALUES (2,'CRUISE','','DVD','','',0,2.55,'2013-01-23','2013-03-31');
INSERT INTO `sellthroughsupport` VALUES (3,'CRUISE','QUARTER','','DVD-UNSG2','',0.1,0,'2013-01-26','2013-01-31');
INSERT INTO `sellthroughsupport` VALUES (4,'GOTSTUFF','','','BREAD','',0.025,0,'2013-03-01','2013-06-01');

--
-- Dumping data for table `shipmentcharges`
--


--
-- Dumping data for table `shipments`
--

INSERT INTO `shipments` VALUES (27,'ass ','asssa ','2012-01-05 00:00:00',0,'WHYNOT',0);
INSERT INTO `shipments` VALUES (28,'test','test','2012-01-05 00:00:00',0,'WHYNOT',0);

--
-- Dumping data for table `shippers`
--

INSERT INTO `shippers` VALUES (1,'DHL',0);
INSERT INTO `shippers` VALUES (8,'UPS',0);
INSERT INTO `shippers` VALUES (10,'Not Specified',0);

--
-- Dumping data for table `stockcategory`
--

INSERT INTO `stockcategory` VALUES ('AIRCON','Air Conditioning','F','1460','5700','5700','5200','5100','1440',1);
INSERT INTO `stockcategory` VALUES ('BAKE','Baking Ingredients','F','1460','5700','5700','5200','5000','1440',1);
INSERT INTO `stockcategory` VALUES ('DVD','DVDs','F','1460','5700','5700','5000','5200','1440',1);
INSERT INTO `stockcategory` VALUES ('FOOD','Food','F','1460','5700','5700','5200','5000','1440',1);
INSERT INTO `stockcategory` VALUES ('TAPE','Tape','F','1460','5700','5700','5800','5800','1440',1);
INSERT INTO `stockcategory` VALUES ('ZFR','Freight','D','1460','5600','5600','5600','5600','1440',1);
INSERT INTO `stockcategory` VALUES ('ZLAB','Labour','L','5500','5700','5700','5900','5500','1440',1);
INSERT INTO `stockcategory` VALUES ('ZPAYT','Payment Surcharge','F','1010','1','1','1','1','1010',1);

--
-- Dumping data for table `stockcatproperties`
--

INSERT INTO `stockcatproperties` VALUES (1,'AIRCON','kw heating',0,'',999999999,0,-999999999,0);
INSERT INTO `stockcatproperties` VALUES (2,'AIRCON','kw cooling',0,'',999999999,0,-999999999,0);
INSERT INTO `stockcatproperties` VALUES (3,'AIRCON','inverter',2,'',999999999,0,-999999999,0);
INSERT INTO `stockcatproperties` VALUES (4,'DVD','Genre',1,'Action,Thriller,Comedy,Romance,Kids,Adult',999999999,0,-999999999,0);

--
-- Dumping data for table `stockcheckfreeze`
--

INSERT INTO `stockcheckfreeze` VALUES ('BIGEARS12','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('BirthdayCakeConstruc','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('BREAD','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('DR_TUMMY','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('DVD-CASE','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('DVD-DHWV','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('DVD-LTWP','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('DVD-TOPGUN','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('DVD-UNSG','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('DVD-UNSG2','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('FLOUR','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('FROAYLANDO','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('FUJI990101','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('FUJI990102','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('HIT3042-4','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('HIT3043-5','MEL',6,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('SALT','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('STROD34','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('TAPE1','MEL',110,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('TAPE2','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('Test123','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('TESTSERIALITEM','MEL',0,'2013-12-03');
INSERT INTO `stockcheckfreeze` VALUES ('YEAST','MEL',0,'2013-12-03');

--
-- Dumping data for table `stockcounts`
--

INSERT INTO `stockcounts` VALUES (1,'TAPE1','MEL',103,'phil 2a');
INSERT INTO `stockcounts` VALUES (2,'TAPE2','MEL',16.3,'pajjs 38891');
INSERT INTO `stockcounts` VALUES (4,'DVD-CASE','AN',2,'');
INSERT INTO `stockcounts` VALUES (5,'DVD-TOPGUN','AN',1,'');

--
-- Dumping data for table `stockdescriptiontranslations`
--

INSERT INTO `stockdescriptiontranslations` VALUES ('DVD-DHWV','fr_FR.utf8','',NULL,0);
INSERT INTO `stockdescriptiontranslations` VALUES ('DVD-LTWP','fr_FR.utf8','',NULL,0);
INSERT INTO `stockdescriptiontranslations` VALUES ('DVD-TOPGUN','fr_FR.utf8','',NULL,0);
INSERT INTO `stockdescriptiontranslations` VALUES ('DVD-UNSG','fr_FR.utf8','',NULL,0);
INSERT INTO `stockdescriptiontranslations` VALUES ('HIT3042-4','fr_FR.utf8','',NULL,0);
INSERT INTO `stockdescriptiontranslations` VALUES ('HIT3043-5','fr_FR.utf8','',NULL,0);
INSERT INTO `stockdescriptiontranslations` VALUES ('PAYTSURCHARGE','de_DE.utf8','',NULL,0);
INSERT INTO `stockdescriptiontranslations` VALUES ('PAYTSURCHARGE','fr_FR.utf8','',NULL,0);
INSERT INTO `stockdescriptiontranslations` VALUES ('PAYTSURCHARGE','it_IT.utf8','',NULL,0);
INSERT INTO `stockdescriptiontranslations` VALUES ('STROD34','de_DE.utf8','',NULL,0);
INSERT INTO `stockdescriptiontranslations` VALUES ('STROD34','fr_FR.utf8','',NULL,0);
INSERT INTO `stockdescriptiontranslations` VALUES ('STROD34','it_IT.utf8','',NULL,0);

--
-- Dumping data for table `stockitemproperties`
--

INSERT INTO `stockitemproperties` VALUES ('HIT3042-4',1,'');
INSERT INTO `stockitemproperties` VALUES ('HIT3042-4',2,'');
INSERT INTO `stockitemproperties` VALUES ('HIT3043-5',1,'');
INSERT INTO `stockitemproperties` VALUES ('HIT3043-5',2,'');
INSERT INTO `stockitemproperties` VALUES ('HIT3042-4',3,'0');
INSERT INTO `stockitemproperties` VALUES ('HIT3043-5',3,'0');
INSERT INTO `stockitemproperties` VALUES ('DVD-CASE',4,'Action');
INSERT INTO `stockitemproperties` VALUES ('DVD-DHWV',4,'Action');
INSERT INTO `stockitemproperties` VALUES ('DVD-LTWP',4,'Action');
INSERT INTO `stockitemproperties` VALUES ('DVD-TOPGUN',4,'Action');
INSERT INTO `stockitemproperties` VALUES ('DVD-UNSG',4,'Action');
INSERT INTO `stockitemproperties` VALUES ('DVD_ACTION',4,'Action');

--
-- Dumping data for table `stockmaster`
--

INSERT INTO `stockmaster` VALUES ('BIGEARS12','DVD','Big Ears and Noddy episodes on DVD','Big Ears and Noddy episodes on DVD','each','M',0.0000,3490.0000,1.0000,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('BirthdayCakeConstruc','BAKE','12 foot birthday cake for wrestling tournament','12 foot birthday cake for wrestling tournament','each','M',0.0000,0.0000,12.9525,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('BREAD','FOOD','Bread','Bread','each','M',0.0000,0.5625,0.5625,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('CUTTING','ZLAB','Cutting Labor','Cutting Labor','hours','D',0.0000,0.0000,0.0000,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('DR_TUMMY','FOOD','Gastric exquisite diarrhea','Gastric exquisite diarrhea','each','M',0.0000,0.0000,116.2250,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('DVD-CASE','DVD','webERP Demo DVD Case','webERP Demo DVD Case','each','B',0.0000,0.9567,1.0000,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','DE',1,0,'0',0,0,0,0,0,0.0000,'2014-01-14');
INSERT INTO `stockmaster` VALUES ('DVD-DHWV','DVD','Die Hard With A Vengeance Linked','Regional Code: 2 (Japan, Europe, Middle East, South Africa). &lt;br /&gt;Languages: English, Deutsch. &lt;br /&gt;Subtitles: English, Deutsch, Spanish. &lt;br /&gt;Audio: Dolby Surround 5.1. &lt;br /&gt;Picture Format: 16:9 Wide-Screen. &lt;br /&gt;Length: (approx) 122 minutes. &lt;br /&gt;Other: Interactive Menus, Chapter Selection, Subtitles (more languages).','each','B',0.0000,5.5000,2.3200,0.0000,0.0000,0,0,0,0,0.0000,7.0000,'','',1,0,'0',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('DVD-LTWP','DVD','Lethal Weapon Linked','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r\n&lt;br /&gt;\r\nLanguages: English, Deutsch.\r\n&lt;br /&gt;\r\nSubtitles: English, Deutsch, Spanish.\r\n&lt;br /&gt;\r\nAudio: Dolby Surround 5.1.\r\n&lt;br /&gt;\r\nPicture Format: 16:9 Wide-Screen.\r\n&lt;br /&gt;\r\nLength: (approx) 100 minutes.\r\n&lt;br /&gt;\r\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','each','B',0.0000,2.6600,2.7000,0.0000,0.0000,0,0,0,0,0.0000,7.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('DVD-TOPGUN','DVD','Top Gun DVD','Top Gun DVD','each','B',0.0000,0.0000,6.5000,0.0000,0.0000,0,0,1,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('DVD-UNSG','DVD','Under Siege Linked','Regional Code: 2 (Japan, Europe, Middle East, South Africa). &lt;br /&gt;Languages: English, Deutsch. &lt;br /&gt;Subtitles: English, Deutsch, Spanish. &lt;br /&gt;Audio: Dolby Surround 5.1. &lt;br /&gt;Picture Format: 16:9 Wide-Screen. &lt;br /&gt;Length: (approx) 98 minutes. &lt;br /&gt;Other: Interactive Menus, Chapter Selection, Subtitles (more languages).','each','B',0.0000,5.0000,8.0000,0.0000,0.0000,0,0,0,0,0.0000,7.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('DVD-UNSG2','DVD','Under Siege 2 - Dark Territory','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br />\nLanguages: English, Deutsch.\r<br />\nSubtitles: English, Deutsch, Spanish.\r<br />\nAudio: Dolby Surround 5.1.\r<br />\nPicture Format: 16:9 Wide-Screen.\r<br />\nLength: (approx) 98 minutes.\r<br />\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','each','B',0.0000,0.0000,5.0000,0.0000,0.0000,0,0,0,0,0.0000,7.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('DVD_ACTION','DVD','Action Series Bundle','Under Seige I and Under Seige II\r\n','each','A',0.0000,16.2200,22.0200,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('FLOUR','AIRCON','High Grade Flour','High Grade Flour','kgs','B',0.0000,0.0000,3.8900,0.0000,0.0000,0,0,1,0,0.0000,0.0000,'','',1,0,'none',0,1,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('FREIGHT','ZFR','Freight','Freight','each','D',0.0000,0.0000,0.0000,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'0',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('FROAYLANDO','FOOD','Fried Orange Yoke Flan D\'Or','Fried Orange Yoke Flan D\'Or','each','M',0.0000,0.0000,34.2618,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('FUJI990101','AIRCON','Fujitsu 990101 Split type Indoor Unit 3.5kw','Fujitsu 990101 Split type Indoor Unit 3.5kw Heat Pump with mounting screws and isolating switch','each','B',0.0000,1015.6105,102564.1026,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,4,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('FUJI990102','AIRCON','Fujitsu 990102 split type A/C Outdoor unit 3.5kw','Fujitsu 990102 split type A/C Outdoor unit 3.5kw with 5m piping & insulation','each','B',0.0000,0.0000,633.0000,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('FUJI9901ASS','AIRCON','Fujitsu 990101 Split type A/C 3.5kw complete','Fujitsu 990101 Split type A/C 3.5kw complete with indoor and outdoor units 5m pipe and insulation isolating switch. 5 year warranty','each','A',0.0000,0.0000,138461.5385,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('HIT3042-4','AIRCON','Hitachi Aircond Rev Cycle Split Type 6.5kw Indoor','Hitachi Aircond Rev Cycle Split Type 6.5kw Indoor Unit - wall hung complete with brackets and screws. 220V-240V AC\r\n5 year guaranttee','each','M',0.0000,0.0000,853.0000,0.0000,0.0000,0,0,1,5,0.4000,7.8000,'','',1,1,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('HIT3043-5','AIRCON','Hitachi Aircond Rev Cycle Split Type 6.5kw Outdoor','Hitachi Aircond Rev Cycle Split Type 6.5kw Outdoor unit - including 5m piping for fitting to HIT3042-4 indoor unit\r\n5 year guaranttee','each','B',0.0000,0.0000,1235.0000,0.0000,0.0000,0,0,1,5,0.8500,16.0000,'','',1,1,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('LABOUR','ZLAB','Labour item - Freddie','Labour item - Freddie','each','D',0.0000,0.0000,75.0000,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'2014-07-26');
INSERT INTO `stockmaster` VALUES ('PAYTSURCHARGE','ZPAYT','Payment Surcharges','Payment Surcharges','each','D',0.0000,0.0000,0.0000,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('SALT','BAKE','Salt','Salt','kgs','B',0.0000,1.2000,2.5000,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,3,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('SELLTAPE','TAPE','Selling units of tape','Selling units of tape','each','A',0.0000,0.0000,0.0100,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('SLICE','FOOD','Slice Of Bread','Slice Of Bread','each','A',0.0000,0.0000,0.0563,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'0',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('STROD34','TAPE','Stainless 3/4&quot; Rod','Stainless 3/4&quot; Rod','each','B',0.0000,0.0000,0.0000,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('TAPE1','TAPE','Log of tape','Log of tape','feet','B',0.0000,0.0000,10.0000,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,2,0,0,0,0.0000,'2013-02-08');
INSERT INTO `stockmaster` VALUES ('TAPE2','TAPE','Tape 2','Tape 2','feet','M',0.0000,0.0000,2.5000,0.0000,0.0000,0,0,0,0,0.0100,0.1000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('Test123','TAPE','Testing manufact tape','Testing manufact tape','each','M',0.0000,0.0000,396.9000,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('TESTSERIALITEM','TAPE','TEst Serial Item','Test Serial Item','each','B',0.0000,0.0000,0.0000,0.0000,0.0000,0,0,1,0,0.0000,0.0000,'','',1,0,'none',0,0,0,0,0,0.0000,'0000-00-00');
INSERT INTO `stockmaster` VALUES ('YEAST','BAKE','Yeast','Yeast','kgs','B',0.0000,3.8500,5.0000,0.0000,0.0000,0,0,0,0,0.0000,0.0000,'','',1,0,'0',0,3,0,0,0,0.0000,'0000-00-00');

--
-- Dumping data for table `stockmoves`
--

INSERT INTO `stockmoves` VALUES (4,'DVD_ACTION',25,49,'MEL','2013-01-06','','','',0.00000,14,'BINGO (Binary Green Ocean Inc) - 23',1,0,16.22,1,1,0,NULL);
INSERT INTO `stockmoves` VALUES (5,'DVD_ACTION',25,1,'MEL','2013-01-06','','','',0.00000,14,'Reversal - BINGO - 23',-1,0,16.22,1,0,0,NULL);
INSERT INTO `stockmoves` VALUES (7,'DVD_ACTION',25,50,'MEL','2013-01-06','','','',52.35000,14,'BINGO (Binary Green Ocean Inc) - 23',1,0,16.22,1,1,0,NULL);
INSERT INTO `stockmoves` VALUES (8,'TAPE1',25,51,'MEL','2013-02-09','','','',3.95320,15,'CRUISE (Cruise Company Inc) - 25',100,0,10,1,100,0,NULL);
INSERT INTO `stockmoves` VALUES (9,'TAPE1',25,52,'MEL','2013-02-09','','','',3.95320,15,'CRUISE (Cruise Company Inc) - 27',10,0,10,1,110,0,NULL);
INSERT INTO `stockmoves` VALUES (12,'CUTTING',28,13,'AN','2013-02-09','','','',0.00000,15,'31',-2,0,0,1,0,0,NULL);
INSERT INTO `stockmoves` VALUES (13,'TAPE1',28,14,'AN','2013-02-09','','','',10.00000,15,'31',-22.5,0,10,1,-22.5,0,NULL);
INSERT INTO `stockmoves` VALUES (14,'TAPE2',26,8,'AN','2013-02-09','','','',2.50000,15,'31',90,0,2.5,1,90,0,NULL);
INSERT INTO `stockmoves` VALUES (23,'HIT3043-5',17,28,'MEL','2013-06-13','','','',0.00000,19,'',6,0,0,1,6,0,NULL);
INSERT INTO `stockmoves` VALUES (24,'TAPE1',28,15,'AN','2013-06-21','','','',10.00000,19,'32',-2,0,10,1,-24.4,0,NULL);
INSERT INTO `stockmoves` VALUES (25,'SALT',28,16,'AN','2013-06-21','','','',2.50000,19,'32',-1.5,0,2.5,1,-1.5,0,NULL);
INSERT INTO `stockmoves` VALUES (26,'TAPE2',28,17,'AN','2013-06-21','','','',2.50000,19,'32',-3.5,0,2.5,1,86.5,0,NULL);
INSERT INTO `stockmoves` VALUES (27,'DVD-DHWV',10,1,'TOR','2013-06-26','','16','16',10.50000,19,'1',-9,0,2.32,1,-9,0,'');
INSERT INTO `stockmoves` VALUES (28,'PAYTSURCHARGE',10,1,'TOR','2013-06-26','','16','16',2.74000,19,'1',-1,0,0,1,0,0,'');
INSERT INTO `stockmoves` VALUES (29,'BREAD',10,2,'TOR','2013-09-07','','12','12',2.50000,22,'7',-2,0,0.5625,1,-12,0,'');
INSERT INTO `stockmoves` VALUES (30,'DVD-CASE',25,54,'MEL','2014-01-13','','','',0.50000,26,'BINGO (Binary Green Ocean Inc) - 30',100,0,0.3,1,100,0,NULL);
INSERT INTO `stockmoves` VALUES (31,'DVD-CASE',25,55,'MEL','2014-01-13','','','',0.10000,26,'BINGO (Binary Green Ocean Inc) - 31',10,0,0.8,1,110,0,NULL);
INSERT INTO `stockmoves` VALUES (32,'DVD-CASE',25,56,'MEL','2014-01-13','','','',0.31000,26,'BINGO (Binary Green Ocean Inc) - 32',100,0,0.8091,1,210,0,NULL);
INSERT INTO `stockmoves` VALUES (33,'DVD-CASE',25,57,'MEL','2014-01-14','','','',2.00000,26,'BINGO (Binary Green Ocean Inc) - 33',100,0,1,1,310,0,NULL);
INSERT INTO `stockmoves` VALUES (34,'LABOUR',25,58,'MEL','2014-07-26','','','',5.42080,32,'WHYNOT (Why not add a new supplier) - 34',2,0,75,1,2,0,NULL);

--
-- Dumping data for table `stockmovestaxes`
--

INSERT INTO `stockmovestaxes` VALUES (27,11,0.07,1,2);
INSERT INTO `stockmovestaxes` VALUES (27,12,0.05,0,1);
INSERT INTO `stockmovestaxes` VALUES (28,11,0.07,1,2);
INSERT INTO `stockmovestaxes` VALUES (28,12,0.05,0,1);
INSERT INTO `stockmovestaxes` VALUES (29,11,0.07,1,2);
INSERT INTO `stockmovestaxes` VALUES (29,12,0.05,0,1);

--
-- Dumping data for table `stockrequest`
--

INSERT INTO `stockrequest` VALUES (1,'MEL',1,'2012-09-19',0,0,'');
INSERT INTO `stockrequest` VALUES (2,'MEL',2,'2014-02-08',1,0,'');

--
-- Dumping data for table `stockrequestitems`
--

INSERT INTO `stockrequestitems` VALUES (0,1,'FLOUR',2,0,1,'kgs',0);
INSERT INTO `stockrequestitems` VALUES (0,2,'BREAD',2,0,0,'each',1);
INSERT INTO `stockrequestitems` VALUES (1,2,'FUJI990101',1,0,4,'each',1);
INSERT INTO `stockrequestitems` VALUES (2,2,'HIT3043-5',1,0,0,'each',1);

--
-- Dumping data for table `stockserialitems`
--

INSERT INTO `stockserialitems` VALUES ('HIT3043-5','MEL','5430','2000-00-00 00:00:00',1,'');
INSERT INTO `stockserialitems` VALUES ('HIT3043-5','MEL','5439','2000-00-00 00:00:00',1,'');
INSERT INTO `stockserialitems` VALUES ('HIT3043-5','MEL','5441','2000-00-00 00:00:00',1,'');
INSERT INTO `stockserialitems` VALUES ('HIT3043-5','MEL','5442','2000-00-00 00:00:00',1,'');
INSERT INTO `stockserialitems` VALUES ('HIT3043-5','MEL','5443','2000-00-00 00:00:00',1,'');
INSERT INTO `stockserialitems` VALUES ('HIT3043-5','MEL','5449','2000-00-00 00:00:00',1,'');

--
-- Dumping data for table `stockserialmoves`
--


--
-- Dumping data for table `suppallocs`
--

INSERT INTO `suppallocs` VALUES (1,10,'2012-12-16',2,1);
INSERT INTO `suppallocs` VALUES (2,100,'2012-12-16',4,3);
INSERT INTO `suppallocs` VALUES (3,100,'2012-12-16',6,5);

--
-- Dumping data for table `suppliercontacts`
--

INSERT INTO `suppliercontacts` VALUES ('CRUISE','Barry Toad','Slips','92827','0204389','','',0);
INSERT INTO `suppliercontacts` VALUES ('CRUISE','French Froggie','Silly mid on','0291991119','1002991','p2038888qp','',0);

--
-- Dumping data for table `supplierdiscounts`
--

INSERT INTO `supplierdiscounts` VALUES (1,'BINGO','DVD-CASE','Marketing support',0.05,0,'2013-01-12','2013-03-31');
INSERT INTO `supplierdiscounts` VALUES (5,'BINGO','DVD-CASE','End of Season Suppor',0,10,'2013-01-01','2013-03-31');
INSERT INTO `supplierdiscounts` VALUES (6,'BINGO','DVD-CASE','Some promo',0.0225,0,'2013-04-01','2013-05-24');

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` VALUES ('BINGO','Binary Green Ocean Inc','Box 3499','Gardenier','San Fransisco','California 54424','','US',1,0.000000,0.000000,'USD','2003-03-01','30',1000.52,'2011-11-19 00:00:00','','0','',0,1,0,'','','','','','','');
INSERT INTO `suppliers` VALUES ('CAMPBELL','Campbell Roberts Inc','Box 9882','Ottowa Rise','','','','',1,0.000000,0.000000,'USD','2005-06-23','30',0,NULL,'','0','',0,2,0,'','','',NULL,NULL,NULL,'');
INSERT INTO `suppliers` VALUES ('CRUISE','Cruise Company Inc','Box 2001','Ft Lauderdale, Florida','','','','',1,0.000000,0.000000,'GBP','2005-06-23','30',100,'2012-12-16 00:00:00','123456789012345678901234567890','0','',0,3,0,'','','',NULL,NULL,NULL,'');
INSERT INTO `suppliers` VALUES ('GOTSTUFF','We Got the Stuff Inc','Test line 1','Test line 2','Test line 3','Test line 4 - editing','','',1,0.000000,0.000000,'USD','2005-10-29','20',0,NULL,'','ok then','tell me abou',0,1,0,'','','',NULL,NULL,NULL,'');
INSERT INTO `suppliers` VALUES ('OTHER','Another ','Supplier','','','','','',2,0.000000,0.000000,'AUD','2011-04-01','20',100,'2013-11-20 00:00:00','','0','',0,2,0,'','','','','','','');
INSERT INTO `suppliers` VALUES ('REGNEW','Reg Newall Inc','P O 5432','Wichita','Wyoming','','','',1,0.000000,0.000000,'USD','2005-04-30','30',0,NULL,'','0','',0,1,0,'','','',NULL,NULL,NULL,'');
INSERT INTO `suppliers` VALUES ('WHYNOT','Why not add a new supplier','Well I will ','If I','Want ','To','','',1,0.000000,0.000000,'AUD','2011-04-01','20',0,NULL,'','0','',0,1,0,'','','','','','12323','');

--
-- Dumping data for table `suppliertype`
--

INSERT INTO `suppliertype` VALUES (1,'Default');
INSERT INTO `suppliertype` VALUES (2,'Others');

--
-- Dumping data for table `supptrans`
--

INSERT INTO `supptrans` VALUES (34,20,'CRUISE','123','2013-01-15','2013-01-31','2012-12-16 00:00:00',1,0.6185,10,0,0,10,'',0,1);
INSERT INTO `supptrans` VALUES (6,22,'CRUISE','Cash','2013-01-01','0000-00-00','2012-12-16 09:17:20',1,0.6185,-10,0,0,-10,'',0,2);
INSERT INTO `supptrans` VALUES (35,20,'CRUISE','234','2013-01-15','2013-01-31','2012-12-16 00:00:00',1,0.6185,100,0,0,100,'',0,3);
INSERT INTO `supptrans` VALUES (7,22,'CRUISE','Cash','2013-01-16','0000-00-00','2012-12-16 09:20:11',1,0.6185,-100,0,0,-100,'',0,4);
INSERT INTO `supptrans` VALUES (36,20,'CRUISE','345','2013-01-16','2013-01-31','2012-12-16 00:00:00',1,0.6185,100,0,9.0667303671351,100,'',0,5);
INSERT INTO `supptrans` VALUES (8,22,'CRUISE','Cash','2013-01-31','0000-00-00','2012-12-16 09:22:34',1,0.58565765,-100,0,-9.0667303671351,-100,'',0,6);
INSERT INTO `supptrans` VALUES (37,20,'CRUISE','opninvoice','2013-02-08','2013-03-31','2013-02-09 00:00:00',0,0.6324,95.25,0,0,0,'',0,7);
INSERT INTO `supptrans` VALUES (38,20,'OTHER','145','2013-10-05','2013-11-22','2013-10-05 00:00:00',0,1.0884,55,6.7925,0,0,'',0,8);
INSERT INTO `supptrans` VALUES (9,22,'OTHER','Cash','2013-11-20','0000-00-00','2013-11-20 19:57:09',0,1.0884,-100,0,0,0,'',0,9);
INSERT INTO `supptrans` VALUES (10,22,'OTHER','Cash','2013-11-20','0000-00-00','2013-11-20 19:59:10',0,1,-100,0,0,0,'',0,10);
INSERT INTO `supptrans` VALUES (11,22,'OTHER','Cash','2013-11-20','0000-00-00','2013-11-20 20:22:30',0,1.0884,-100,0,0,0,'',0,11);
INSERT INTO `supptrans` VALUES (39,20,'BINGO','aA112','2013-11-30','2013-12-31','2013-12-01 00:00:00',0,1,52.35,5.235,0,0,'',0,12);
INSERT INTO `supptrans` VALUES (40,20,'BINGO','2100','2014-01-13','2014-02-28','2014-01-13 00:00:00',0,1,50,5,0,0,'',0,13);
INSERT INTO `supptrans` VALUES (41,20,'BINGO','1223','2014-01-13','2014-02-28','2014-01-13 00:00:00',0,1,1,0.1,0,0,'',0,14);
INSERT INTO `supptrans` VALUES (42,20,'BINGO','23001','2014-01-13','2014-02-28','2014-01-13 00:00:00',0,1,31,3.1,0,0,'',0,15);
INSERT INTO `supptrans` VALUES (43,20,'BINGO','tedt555','2014-01-14','2014-02-28','2014-01-14 00:00:00',0,1,1.5,0.15,0,0,'',0,16);
INSERT INTO `supptrans` VALUES (44,20,'BINGO','2133','2014-01-13','2014-02-28','2014-01-14 00:00:00',0,1,1.5,0.15,0,0,'',0,17);
INSERT INTO `supptrans` VALUES (45,20,'BINGO','211','2014-01-13','2014-02-28','2014-01-14 00:00:00',0,1,2,0.2,0,0,'',0,18);

--
-- Dumping data for table `supptranstaxes`
--

INSERT INTO `supptranstaxes` VALUES (1,13,0);
INSERT INTO `supptranstaxes` VALUES (3,13,0);
INSERT INTO `supptranstaxes` VALUES (5,13,0);
INSERT INTO `supptranstaxes` VALUES (7,13,0);
INSERT INTO `supptranstaxes` VALUES (8,11,4.0425);
INSERT INTO `supptranstaxes` VALUES (8,12,2.75);
INSERT INTO `supptranstaxes` VALUES (12,1,5.235);
INSERT INTO `supptranstaxes` VALUES (13,1,5);
INSERT INTO `supptranstaxes` VALUES (14,1,0.1);
INSERT INTO `supptranstaxes` VALUES (15,1,3.1);
INSERT INTO `supptranstaxes` VALUES (16,1,0.15);
INSERT INTO `supptranstaxes` VALUES (17,1,0.15);
INSERT INTO `supptranstaxes` VALUES (18,1,0.2);

--
-- Dumping data for table `systypes`
--

INSERT INTO `systypes` VALUES (0,'Journal - GL',8);
INSERT INTO `systypes` VALUES (1,'Payment - GL',7);
INSERT INTO `systypes` VALUES (2,'Receipt - GL',1);
INSERT INTO `systypes` VALUES (3,'Standing Journal',0);
INSERT INTO `systypes` VALUES (10,'Sales Invoice',2);
INSERT INTO `systypes` VALUES (11,'Credit Note',0);
INSERT INTO `systypes` VALUES (12,'Receipt',4);
INSERT INTO `systypes` VALUES (15,'Journal - Debtors',0);
INSERT INTO `systypes` VALUES (16,'Location Transfer',27);
INSERT INTO `systypes` VALUES (17,'Stock Adjustment',28);
INSERT INTO `systypes` VALUES (18,'Purchase Order',34);
INSERT INTO `systypes` VALUES (19,'Picking List',0);
INSERT INTO `systypes` VALUES (20,'Purchase Invoice',45);
INSERT INTO `systypes` VALUES (21,'Debit Note',8);
INSERT INTO `systypes` VALUES (22,'Creditors Payment',11);
INSERT INTO `systypes` VALUES (23,'Creditors Journal',0);
INSERT INTO `systypes` VALUES (25,'Purchase Order Delivery',58);
INSERT INTO `systypes` VALUES (26,'Work Order Receipt',8);
INSERT INTO `systypes` VALUES (28,'Work Order Issue',17);
INSERT INTO `systypes` VALUES (29,'Work Order Variance',1);
INSERT INTO `systypes` VALUES (30,'Sales Order',10);
INSERT INTO `systypes` VALUES (31,'Shipment Close',28);
INSERT INTO `systypes` VALUES (32,'Contract Close',6);
INSERT INTO `systypes` VALUES (35,'Cost Update',26);
INSERT INTO `systypes` VALUES (36,'Exchange Difference',1);
INSERT INTO `systypes` VALUES (37,'Tenders',0);
INSERT INTO `systypes` VALUES (38,'Stock Requests',2);
INSERT INTO `systypes` VALUES (40,'Work Order',36);
INSERT INTO `systypes` VALUES (41,'Asset Addition',1);
INSERT INTO `systypes` VALUES (42,'Asset Category Change',1);
INSERT INTO `systypes` VALUES (43,'Delete w/down asset',1);
INSERT INTO `systypes` VALUES (44,'Depreciation',1);
INSERT INTO `systypes` VALUES (49,'Import Fixed Assets',1);
INSERT INTO `systypes` VALUES (50,'Opening Balance',0);
INSERT INTO `systypes` VALUES (500,'Auto Debtor Number',19);
INSERT INTO `systypes` VALUES (600,'Auto Supplier Number',0);

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` VALUES (1,'Sales');
INSERT INTO `tags` VALUES (2,'Marketing');
INSERT INTO `tags` VALUES (3,'Manufacturing');
INSERT INTO `tags` VALUES (4,'Administration');

--
-- Dumping data for table `taxauthorities`
--

INSERT INTO `taxauthorities` VALUES (1,'Australian GST','2300','2310','','','','');
INSERT INTO `taxauthorities` VALUES (5,'Sales Tax','2300','2310','','','','');
INSERT INTO `taxauthorities` VALUES (11,'Canadian GST','2300','2310','','','','');
INSERT INTO `taxauthorities` VALUES (12,'Ontario PST','2300','2310','','','','');
INSERT INTO `taxauthorities` VALUES (13,'UK VAT','2300','2310','','','','');

--
-- Dumping data for table `taxauthrates`
--

INSERT INTO `taxauthrates` VALUES (1,1,1,0.1);
INSERT INTO `taxauthrates` VALUES (1,1,2,0);
INSERT INTO `taxauthrates` VALUES (1,1,5,0);
INSERT INTO `taxauthrates` VALUES (5,1,1,0.2);
INSERT INTO `taxauthrates` VALUES (5,1,2,0.35);
INSERT INTO `taxauthrates` VALUES (5,1,5,0);
INSERT INTO `taxauthrates` VALUES (11,1,1,0.07);
INSERT INTO `taxauthrates` VALUES (11,1,2,0.12);
INSERT INTO `taxauthrates` VALUES (11,1,5,0.07);
INSERT INTO `taxauthrates` VALUES (12,1,1,0.05);
INSERT INTO `taxauthrates` VALUES (12,1,2,0.075);
INSERT INTO `taxauthrates` VALUES (12,1,5,0);
INSERT INTO `taxauthrates` VALUES (13,1,1,0);
INSERT INTO `taxauthrates` VALUES (13,1,2,0);
INSERT INTO `taxauthrates` VALUES (13,1,5,0);

--
-- Dumping data for table `taxcategories`
--

INSERT INTO `taxcategories` VALUES (1,'Taxable supply');
INSERT INTO `taxcategories` VALUES (2,'Luxury Items');
INSERT INTO `taxcategories` VALUES (4,'Exempt');
INSERT INTO `taxcategories` VALUES (5,'Freight');

--
-- Dumping data for table `taxgroups`
--

INSERT INTO `taxgroups` VALUES (1,'Default');
INSERT INTO `taxgroups` VALUES (2,'Ontario');
INSERT INTO `taxgroups` VALUES (3,'UK Inland Revenue');

--
-- Dumping data for table `taxgrouptaxes`
--

INSERT INTO `taxgrouptaxes` VALUES (1,1,1,0);
INSERT INTO `taxgrouptaxes` VALUES (2,11,2,1);
INSERT INTO `taxgrouptaxes` VALUES (2,12,1,0);
INSERT INTO `taxgrouptaxes` VALUES (3,13,0,0);

--
-- Dumping data for table `taxprovinces`
--

INSERT INTO `taxprovinces` VALUES (1,'Default Tax province');

--
-- Dumping data for table `tenderitems`
--


--
-- Dumping data for table `tenders`
--


--
-- Dumping data for table `tendersuppliers`
--


--
-- Dumping data for table `unitsofmeasure`
--

INSERT INTO `unitsofmeasure` VALUES (1,'each');
INSERT INTO `unitsofmeasure` VALUES (2,'meters');
INSERT INTO `unitsofmeasure` VALUES (3,'kgs');
INSERT INTO `unitsofmeasure` VALUES (4,'litres');
INSERT INTO `unitsofmeasure` VALUES (5,'length');
INSERT INTO `unitsofmeasure` VALUES (6,'hours');
INSERT INTO `unitsofmeasure` VALUES (7,'feet');

--
-- Dumping data for table `woitems`
--

INSERT INTO `woitems` VALUES (28,'BREAD',2,0,0.5625,'',NULL);
INSERT INTO `woitems` VALUES (29,'BREAD',10,0,0.5625,'',NULL);
INSERT INTO `woitems` VALUES (30,'TAPE2',50,0,2.5,'',NULL);
INSERT INTO `woitems` VALUES (31,'TAPE2',100,90,2.5,'',NULL);
INSERT INTO `woitems` VALUES (32,'Test123',1,0,396.9,'',NULL);
INSERT INTO `woitems` VALUES (34,'DR_TUMMY',1,0,0,'',NULL);

--
-- Dumping data for table `worequirements`
--

INSERT INTO `worequirements` VALUES (28,'BREAD','SALT',0.025,2.5,1);
INSERT INTO `worequirements` VALUES (28,'BREAD','YEAST',0.1,5,0);
INSERT INTO `worequirements` VALUES (29,'BREAD','SALT',0.025,2.5,1);
INSERT INTO `worequirements` VALUES (29,'BREAD','YEAST',0.1,5,0);
INSERT INTO `worequirements` VALUES (30,'TAPE2','CUTTING',0.5,0,0);
INSERT INTO `worequirements` VALUES (30,'TAPE2','TAPE1',0.25,10,1);
INSERT INTO `worequirements` VALUES (31,'TAPE2','CUTTING',0.5,0,0);
INSERT INTO `worequirements` VALUES (31,'TAPE2','TAPE1',0.25,10,1);
INSERT INTO `worequirements` VALUES (32,'Test123','SALT',2,2.5,0);
INSERT INTO `worequirements` VALUES (32,'Test123','TAPE1',3,10,0);
INSERT INTO `worequirements` VALUES (32,'Test123','TAPE2',4,2.5,0);

--
-- Dumping data for table `workcentres`
--

INSERT INTO `workcentres` VALUES ('ASS','TOR','Assembly',1,50,'4600',0);
INSERT INTO `workcentres` VALUES ('MEL','MEL','Default for MEL',1,0,'1',0);

--
-- Dumping data for table `workorders`
--

INSERT INTO `workorders` VALUES (28,'MEL','2012-12-16','2012-12-16',0,0,NULL);
INSERT INTO `workorders` VALUES (29,'MEL','2013-02-06','2013-02-06',0,0,NULL);
INSERT INTO `workorders` VALUES (30,'AN','2013-02-27','2013-02-08',0,0,NULL);
INSERT INTO `workorders` VALUES (31,'AN','2013-02-09','2013-02-09',225,0,NULL);
INSERT INTO `workorders` VALUES (32,'TOR','2013-07-21','2013-06-21',32.5,0,NULL);
INSERT INTO `workorders` VALUES (33,'MEL','2014-08-02','2014-08-02',0,0,NULL);
INSERT INTO `workorders` VALUES (34,'MEL','2014-08-02','2014-08-02',0,0,NULL);
INSERT INTO `workorders` VALUES (35,'MEL','2014-08-30','2014-08-30',0,0,NULL);
INSERT INTO `workorders` VALUES (36,'MEL','2014-08-30','2014-08-30',0,0,NULL);

--
-- Dumping data for table `woserialnos`
--


--
-- Dumping data for table `www_users`
--

INSERT INTO `www_users` VALUES ('admin','$2y$10$Q8HLC/2rQaB5NcCcK6V6ZOQG3chIsx16mKtZRoSaUsU9okMBDbUwG','Demonstration user','','','','','admin@weberp.org','MEL',8,1,'2015-02-06 20:00:59','','A4','1,1,1,1,1,1,1,1,1,1,1,',0,0,50,'xenos','en_GB.utf8',0,0);
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-02-06 20:01:47
SET FOREIGN_KEY_CHECKS = 1;
