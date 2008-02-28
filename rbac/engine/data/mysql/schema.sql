
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- PERMISSIONS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `PERMISSIONS`;


CREATE TABLE `PERMISSIONS`
(
	`PER_UID` VARCHAR(32) default '' NOT NULL,
	`PER_CODE` VARCHAR(32) default '' NOT NULL,
	`PER_CREATE_DATE` DATETIME default '0000-00-00 00:00:00' NOT NULL,
	`PER_UPDATE_DATE` DATETIME default '0000-00-00 00:00:00' NOT NULL,
	`PER_STATUS` INTEGER default 1 NOT NULL,
	PRIMARY KEY (`PER_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Permissions';
#-----------------------------------------------------------------------------
#-- ROLES
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ROLES`;


CREATE TABLE `ROLES`
(
	`ROL_UID` VARCHAR(32) default '' NOT NULL,
	`ROL_PARENT` VARCHAR(32) default '' NOT NULL,
	`ROL_SYSTEM` VARCHAR(32) default '' NOT NULL,
	`ROL_CODE` VARCHAR(32) default '' NOT NULL,
	`ROL_CREATE_DATE` DATETIME default '0000-00-00 00:00:00' NOT NULL,
	`ROL_UPDATE_DATE` DATETIME default '0000-00-00 00:00:00' NOT NULL,
	`ROL_STATUS` INTEGER default 1 NOT NULL,
	PRIMARY KEY (`ROL_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Roles';
#-----------------------------------------------------------------------------
#-- ROLES_PERMISSIONS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ROLES_PERMISSIONS`;


CREATE TABLE `ROLES_PERMISSIONS`
(
	`ROL_UID` VARCHAR(32) default '' NOT NULL,
	`PER_UID` VARCHAR(32) default '' NOT NULL,
	PRIMARY KEY (`ROL_UID`,`PER_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Permissions of the roles';
#-----------------------------------------------------------------------------
#-- SYSTEMS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `SYSTEMS`;


CREATE TABLE `SYSTEMS`
(
	`SYS_UID` VARCHAR(32) default '' NOT NULL,
	`SYS_CODE` VARCHAR(32) default '' NOT NULL,
	`SYS_CREATE_DATE` DATETIME default '0000-00-00 00:00:00' NOT NULL,
	`SYS_UPDATE_DATE` DATETIME default '0000-00-00 00:00:00' NOT NULL,
	`SYS_STATUS` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`SYS_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Systems';
#-----------------------------------------------------------------------------
#-- USERS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `USERS`;


CREATE TABLE `USERS`
(
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`USR_USERNAME` VARCHAR(100) default '' NOT NULL,
	`USR_PASSWORD` VARCHAR(32) default '' NOT NULL,
	`USR_FIRSTNAME` VARCHAR(50) default '' NOT NULL,
	`USR_LASTNAME` VARCHAR(50) default '' NOT NULL,
	`USR_EMAIL` VARCHAR(100) default '' NOT NULL,
	`USR_DUE_DATE` DATE default '0000-00-00' NOT NULL,
	`USR_CREATE_DATE` DATETIME default '0000-00-00 00:00:00' NOT NULL,
	`USR_UPDATE_DATE` DATETIME default '0000-00-00 00:00:00' NOT NULL,
	`USR_STATUS` INTEGER default 1 NOT NULL,
	PRIMARY KEY (`USR_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Users';
#-----------------------------------------------------------------------------
#-- USERS_ROLES
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `USERS_ROLES`;


CREATE TABLE `USERS_ROLES`
(
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`ROL_UID` VARCHAR(32) default '' NOT NULL,
	PRIMARY KEY (`USR_UID`,`ROL_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Roles of the users';
# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
