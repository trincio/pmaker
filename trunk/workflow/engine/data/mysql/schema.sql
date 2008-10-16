
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- APPLICATION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APPLICATION`;


CREATE TABLE `APPLICATION`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`APP_NUMBER` INTEGER default 0 NOT NULL,
	`APP_PARENT` VARCHAR(32) default '0' NOT NULL,
	`APP_STATUS` VARCHAR(100) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`APP_PROC_STATUS` VARCHAR(100) default '' NOT NULL,
	`APP_PROC_CODE` VARCHAR(100) default '' NOT NULL,
	`APP_PARALLEL` VARCHAR(32) default 'NO' NOT NULL,
	`APP_INIT_USER` VARCHAR(32) default '' NOT NULL,
	`APP_CUR_USER` VARCHAR(32) default '' NOT NULL,
	`APP_CREATE_DATE` DATETIME  NOT NULL,
	`APP_INIT_DATE` DATETIME  NOT NULL,
	`APP_FINISH_DATE` DATETIME  NOT NULL,
	`APP_UPDATE_DATE` DATETIME  NOT NULL,
	`APP_DATA` TEXT  NOT NULL,
	`APP_PIN` VARCHAR(32) default '',
	PRIMARY KEY (`APP_UID`),
	KEY `indexApp`(`PRO_UID`, `APP_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The application';
#-----------------------------------------------------------------------------
#-- APP_DELEGATION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_DELEGATION`;


CREATE TABLE `APP_DELEGATION`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`DEL_INDEX` INTEGER default 0 NOT NULL,
	`DEL_PREVIOUS` INTEGER default 0 NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`DEL_TYPE` VARCHAR(32) default 'NORMAL' NOT NULL,
	`DEL_THREAD` INTEGER default 0 NOT NULL,
	`DEL_THREAD_STATUS` VARCHAR(32) default 'OPEN' NOT NULL,
	`DEL_PRIORITY` VARCHAR(32) default '3' NOT NULL,
	`DEL_DELEGATE_DATE` DATETIME  NOT NULL,
	`DEL_INIT_DATE` DATETIME,
	`DEL_TASK_DUE_DATE` DATETIME,
	`DEL_FINISH_DATE` DATETIME,
	`DEL_DURATION` DOUBLE default 0,
	PRIMARY KEY (`APP_UID`,`DEL_INDEX`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Delegation a task to user';
#-----------------------------------------------------------------------------
#-- APP_DOCUMENT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_DOCUMENT`;


CREATE TABLE `APP_DOCUMENT`
(
	`APP_DOC_UID` VARCHAR(32) default '' NOT NULL,
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`DEL_INDEX` INTEGER default 0 NOT NULL,
	`DOC_UID` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`APP_DOC_TYPE` VARCHAR(32) default '' NOT NULL,
	`APP_DOC_CREATE_DATE` DATETIME  NOT NULL,
	`APP_DOC_INDEX` INTEGER  NOT NULL,
	PRIMARY KEY (`APP_DOC_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Documents in an Application';
#-----------------------------------------------------------------------------
#-- APP_MESSAGE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_MESSAGE`;


CREATE TABLE `APP_MESSAGE`
(
	`APP_MSG_UID` VARCHAR(32)  NOT NULL,
	`MSG_UID` VARCHAR(32),
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`DEL_INDEX` INTEGER default 0 NOT NULL,
	`APP_MSG_TYPE` VARCHAR(100) default '' NOT NULL,
	`APP_MSG_SUBJECT` VARCHAR(150) default '' NOT NULL,
	`APP_MSG_FROM` VARCHAR(100) default '' NOT NULL,
	`APP_MSG_TO` TEXT  NOT NULL,
	`APP_MSG_BODY` TEXT  NOT NULL,
	`APP_MSG_DATE` DATETIME  NOT NULL,
	`APP_MSG_CC` TEXT,
	`APP_MSG_BCC` TEXT,
	`APP_MSG_TEMPLATE` TEXT,
	`APP_MSG_STATUS` VARCHAR(20),
	`APP_MSG_ATTACH` TEXT,
	PRIMARY KEY (`APP_MSG_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Messages in an Application';
#-----------------------------------------------------------------------------
#-- APP_OWNER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_OWNER`;


CREATE TABLE `APP_OWNER`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`OWN_UID` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	PRIMARY KEY (`APP_UID`,`OWN_UID`,`USR_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- CONFIGURATION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `CONFIGURATION`;


CREATE TABLE `CONFIGURATION`
(
	`CFG_UID` VARCHAR(32) default '' NOT NULL,
	`OBJ_UID` VARCHAR(128) default '' NOT NULL,
	`CFG_VALUE` TEXT  NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	PRIMARY KEY (`CFG_UID`,`OBJ_UID`,`PRO_UID`,`USR_UID`,`APP_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Stores the users, processes and/or applications configuratio';
#-----------------------------------------------------------------------------
#-- CONTENT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `CONTENT`;


CREATE TABLE `CONTENT`
(
	`CON_CATEGORY` VARCHAR(30) default '' NOT NULL,
	`CON_PARENT` VARCHAR(32) default '' NOT NULL,
	`CON_ID` VARCHAR(100) default '' NOT NULL,
	`CON_LANG` VARCHAR(10) default '' NOT NULL,
	`CON_VALUE` MEDIUMTEXT  NOT NULL,
	PRIMARY KEY (`CON_CATEGORY`,`CON_PARENT`,`CON_ID`,`CON_LANG`),
	KEY `indexUid`(`CON_ID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- DEPARTMENT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `DEPARTMENT`;


CREATE TABLE `DEPARTMENT`
(
	`DEP_UID` VARCHAR(32) default '' NOT NULL,
	`DEP_PARENT` VARCHAR(32) default '' NOT NULL,
	`DEP_MANAGER` VARCHAR(32) default '' NOT NULL,
	`DEP_LOCATION` INTEGER default 0 NOT NULL,
	`DEP_STATUS` CHAR(1) default 'A' NOT NULL,
	`DEP_TYPE` VARCHAR(5) default 'INTER' NOT NULL,
	`DEP_REF_CODE` VARCHAR(10) default '' NOT NULL,
	PRIMARY KEY (`DEP_UID`),
	KEY `DEP_BYPARENT`(`DEP_PARENT`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Departments';
#-----------------------------------------------------------------------------
#-- DYNAFORM
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `DYNAFORM`;


CREATE TABLE `DYNAFORM`
(
	`DYN_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`DYN_TYPE` VARCHAR(20) default 'xmlform' NOT NULL,
	`DYN_FILENAME` VARCHAR(100) default '' NOT NULL,
	PRIMARY KEY (`DYN_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Forms required';
#-----------------------------------------------------------------------------
#-- GROUPWF
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `GROUPWF`;


CREATE TABLE `GROUPWF`
(
	`GRP_UID` VARCHAR(32) default '' NOT NULL,
	`GRP_STATUS` CHAR(8) default 'ACTIVE' NOT NULL,
	PRIMARY KEY (`GRP_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- GROUP_USER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `GROUP_USER`;


CREATE TABLE `GROUP_USER`
(
	`GRP_UID` VARCHAR(32) default '0' NOT NULL,
	`USR_UID` VARCHAR(32) default '0' NOT NULL,
	PRIMARY KEY (`GRP_UID`,`USR_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- HOLIDAY
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `HOLIDAY`;


CREATE TABLE `HOLIDAY`
(
	`HLD_UID` INTEGER  NOT NULL AUTO_INCREMENT,
	`HLD_DATE` VARCHAR(10) default '0000-00-00' NOT NULL,
	`HLD_DESCRIPTION` VARCHAR(200) default '' NOT NULL,
	PRIMARY KEY (`HLD_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- INPUT_DOCUMENT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `INPUT_DOCUMENT`;


CREATE TABLE `INPUT_DOCUMENT`
(
	`INP_DOC_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`INP_DOC_FORM_NEEDED` VARCHAR(20) default 'REAL' NOT NULL,
	`INP_DOC_ORIGINAL` VARCHAR(20) default 'COPY' NOT NULL,
	`INP_DOC_PUBLISHED` VARCHAR(20) default 'PRIVATE' NOT NULL,
	PRIMARY KEY (`INP_DOC_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Documentation required';
#-----------------------------------------------------------------------------
#-- ISO_COUNTRY
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ISO_COUNTRY`;


CREATE TABLE `ISO_COUNTRY`
(
	`IC_UID` VARCHAR(2) default '' NOT NULL,
	`IC_NAME` VARCHAR(255),
	`IC_SORT_ORDER` VARCHAR(255),
	PRIMARY KEY (`IC_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- ISO_LOCATION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ISO_LOCATION`;


CREATE TABLE `ISO_LOCATION`
(
	`IC_UID` VARCHAR(2) default '' NOT NULL,
	`IL_UID` VARCHAR(5) default '' NOT NULL,
	`IL_NAME` VARCHAR(255),
	`IL_NORMAL_NAME` VARCHAR(255),
	`IS_UID` VARCHAR(4),
	PRIMARY KEY (`IC_UID`,`IL_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- ISO_SUBDIVISION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ISO_SUBDIVISION`;


CREATE TABLE `ISO_SUBDIVISION`
(
	`IC_UID` VARCHAR(2) default '' NOT NULL,
	`IS_UID` VARCHAR(4) default '' NOT NULL,
	`IS_NAME` VARCHAR(255) default '' NOT NULL,
	PRIMARY KEY (`IC_UID`,`IS_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- LANGUAGE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `LANGUAGE`;


CREATE TABLE `LANGUAGE`
(
	`LAN_ID` VARCHAR(4) default '' NOT NULL,
	`LAN_NAME` VARCHAR(30) default '' NOT NULL,
	`LAN_NATIVE_NAME` VARCHAR(30) default '' NOT NULL,
	`LAN_DIRECTION` CHAR(1) default 'L' NOT NULL,
	`LAN_WEIGHT` INTEGER default 0 NOT NULL,
	`LAN_ENABLED` CHAR(1) default '1' NOT NULL,
	`LAN_CALENDAR` VARCHAR(30) default 'GREGORIAN' NOT NULL,
	PRIMARY KEY (`LAN_ID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- LEXICO
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `LEXICO`;


CREATE TABLE `LEXICO`
(
	`LEX_TOPIC` VARCHAR(64) default '' NOT NULL,
	`LEX_KEY` VARCHAR(128) default '' NOT NULL,
	`LEX_VALUE` VARCHAR(128) default '' NOT NULL,
	`LEX_CAPTION` VARCHAR(128) default '' NOT NULL,
	PRIMARY KEY (`LEX_TOPIC`,`LEX_KEY`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='LEXICOS, una tabla que contiene tablas';
#-----------------------------------------------------------------------------
#-- OUTPUT_DOCUMENT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `OUTPUT_DOCUMENT`;


CREATE TABLE `OUTPUT_DOCUMENT`
(
	`OUT_DOC_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`OUT_DOC_LANDSCAPE` TINYINT default 0 NOT NULL,
	PRIMARY KEY (`OUT_DOC_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- PROCESS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `PROCESS`;


CREATE TABLE `PROCESS`
(
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_PARENT` VARCHAR(32) default '0' NOT NULL,
	`PRO_TIME` DOUBLE default 1 NOT NULL,
	`PRO_TIMEUNIT` VARCHAR(20) default 'DAYS' NOT NULL,
	`PRO_STATUS` VARCHAR(20) default 'ACTIVE' NOT NULL,
	`PRO_TYPE_DAY` CHAR(1) default '0' NOT NULL,
	`PRO_TYPE` VARCHAR(20) default 'NORMAL' NOT NULL,
	`PRO_ASSIGNMENT` VARCHAR(20) default 'FALSE' NOT NULL,
	`PRO_SHOW_MAP` TINYINT default 1 NOT NULL,
	`PRO_SHOW_MESSAGE` TINYINT default 1 NOT NULL,
	`PRO_SHOW_DELEGATE` TINYINT default 1 NOT NULL,
	`PRO_SHOW_DYNAFORM` TINYINT default 0 NOT NULL,
	`PRO_CATEGORY` VARCHAR(48) default '' NOT NULL,
	`PRO_SUB_CATEGORY` VARCHAR(48) default '' NOT NULL,
	`PRO_INDUSTRY` INTEGER default 1 NOT NULL,
	`PRO_UPDATE_DATE` DATETIME,
	`PRO_CREATE_DATE` DATETIME  NOT NULL,
	`PRO_CREATE_USER` VARCHAR(32) default '' NOT NULL,
	`PRO_HEIGHT` INTEGER default 5000 NOT NULL,
	`PRO_WIDTH` INTEGER default 10000 NOT NULL,
	`PRO_TITLE_X` INTEGER default 0 NOT NULL,
	`PRO_TITLE_Y` INTEGER default 6 NOT NULL,
	`PRO_DEBUG` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`PRO_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Store process Information';
#-----------------------------------------------------------------------------
#-- PROCESS_OWNER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `PROCESS_OWNER`;


CREATE TABLE `PROCESS_OWNER`
(
	`OWN_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	PRIMARY KEY (`OWN_UID`,`PRO_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- REPORT_TABLE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `REPORT_TABLE`;


CREATE TABLE `REPORT_TABLE`
(
	`REP_TAB_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`REP_TAB_NAME` VARCHAR(100) default '' NOT NULL,
	`REP_TAB_TYPE` VARCHAR(6) default '' NOT NULL,
	`REP_TAB_GRID` VARCHAR(150) default '',
	`REP_TAB_CONNECTION` VARCHAR(32) default '' NOT NULL,
	`REP_TAB_CREATE_DATE` DATETIME  NOT NULL,
	`REP_TAB_STATUS` CHAR(8) default 'ACTIVE' NOT NULL,
	PRIMARY KEY (`REP_TAB_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- REPORT_VAR
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `REPORT_VAR`;


CREATE TABLE `REPORT_VAR`
(
	`REP_VAR_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`REP_TAB_UID` VARCHAR(32) default '' NOT NULL,
	`REP_VAR_NAME` VARCHAR(255) default '' NOT NULL,
	`REP_VAR_TYPE` VARCHAR(20) default '' NOT NULL,
	PRIMARY KEY (`REP_VAR_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- ROUTE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ROUTE`;


CREATE TABLE `ROUTE`
(
	`ROU_UID` VARCHAR(32) default '' NOT NULL,
	`ROU_PARENT` VARCHAR(32) default '0' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`ROU_NEXT_TASK` VARCHAR(32) default '0' NOT NULL,
	`ROU_CASE` INTEGER default 0 NOT NULL,
	`ROU_TYPE` VARCHAR(25) default 'SEQUENTIAL' NOT NULL,
	`ROU_CONDITION` VARCHAR(255) default '' NOT NULL,
	`ROU_TO_LAST_USER` VARCHAR(20) default 'FALSE' NOT NULL,
	`ROU_OPTIONAL` VARCHAR(20) default 'FALSE' NOT NULL,
	`ROU_SEND_EMAIL` VARCHAR(20) default 'TRUE' NOT NULL,
	`ROU_SOURCEANCHOR` INTEGER default 1,
	`ROU_TARGETANCHOR` INTEGER default 0,
	PRIMARY KEY (`ROU_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Differents flows for a flow in business process';
#-----------------------------------------------------------------------------
#-- STEP
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `STEP`;


CREATE TABLE `STEP`
(
	`STEP_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`TAS_UID` VARCHAR(32) default '0' NOT NULL,
	`STEP_TYPE_OBJ` VARCHAR(20) default 'DYNAFORM' NOT NULL,
	`STEP_UID_OBJ` VARCHAR(32) default '0' NOT NULL,
	`STEP_CONDITION` TEXT  NOT NULL,
	`STEP_POSITION` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`STEP_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- STEP_TRIGGER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `STEP_TRIGGER`;


CREATE TABLE `STEP_TRIGGER`
(
	`STEP_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`TRI_UID` VARCHAR(32) default '' NOT NULL,
	`ST_TYPE` VARCHAR(20) default '' NOT NULL,
	`ST_CONDITION` VARCHAR(255) default '' NOT NULL,
	`ST_POSITION` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`STEP_UID`,`TAS_UID`,`TRI_UID`,`ST_TYPE`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- SWIMLANES_ELEMENTS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `SWIMLANES_ELEMENTS`;


CREATE TABLE `SWIMLANES_ELEMENTS`
(
	`SWI_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`SWI_TYPE` VARCHAR(20) default 'LINE' NOT NULL,
	`SWI_X` INTEGER default 0 NOT NULL,
	`SWI_Y` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`SWI_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- TASK
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `TASK`;


CREATE TABLE `TASK`
(
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_TYPE` VARCHAR(20) default 'NORMAL' NOT NULL,
	`TAS_DURATION` DOUBLE default 0 NOT NULL,
	`TAS_DELAY_TYPE` VARCHAR(30) default '' NOT NULL,
	`TAS_TEMPORIZER` DOUBLE default 0 NOT NULL,
	`TAS_TYPE_DAY` CHAR(1) default '1' NOT NULL,
	`TAS_TIMEUNIT` VARCHAR(20) default 'DAYS' NOT NULL,
	`TAS_ALERT` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_PRIORITY_VARIABLE` VARCHAR(100) default '' NOT NULL,
	`TAS_ASSIGN_TYPE` VARCHAR(30) default 'BALANCED' NOT NULL,
	`TAS_ASSIGN_VARIABLE` VARCHAR(100) default '@@SYS_NEXT_USER_TO_BE_ASSIGNED' NOT NULL,
	`TAS_ASSIGN_LOCATION` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_ASSIGN_LOCATION_ADHOC` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_TRANSFER_FLY` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_LAST_ASSIGNED` VARCHAR(32) default '0' NOT NULL,
	`TAS_USER` VARCHAR(32) default '0' NOT NULL,
	`TAS_CAN_UPLOAD` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_VIEW_UPLOAD` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_VIEW_ADDITIONAL_DOCUMENTATION` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_CAN_CANCEL` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_OWNER_APP` VARCHAR(32) default '' NOT NULL,
	`STG_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_CAN_PAUSE` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_CAN_SEND_MESSAGE` VARCHAR(20) default 'TRUE' NOT NULL,
	`TAS_CAN_DELETE_DOCS` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_SELF_SERVICE` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_START` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_TO_LAST_USER` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_SEND_LAST_EMAIL` VARCHAR(20) default 'TRUE' NOT NULL,
	`TAS_DERIVATION` VARCHAR(100) default 'NORMAL' NOT NULL,
	`TAS_POSX` INTEGER default 0 NOT NULL,
	`TAS_POSY` INTEGER default 0 NOT NULL,
	`TAS_COLOR` VARCHAR(32) default '' NOT NULL,
	PRIMARY KEY (`TAS_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Task of workflow';
#-----------------------------------------------------------------------------
#-- TASK_USER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `TASK_USER`;


CREATE TABLE `TASK_USER`
(
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`TU_TYPE` INTEGER default 1 NOT NULL,
	`TU_RELATION` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`TAS_UID`,`USR_UID`,`TU_TYPE`,`TU_RELATION`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- TRANSLATION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `TRANSLATION`;


CREATE TABLE `TRANSLATION`
(
	`TRN_CATEGORY` VARCHAR(100) default '' NOT NULL,
	`TRN_ID` VARCHAR(100) default '' NOT NULL,
	`TRN_LANG` VARCHAR(10) default 'en' NOT NULL,
	`TRN_VALUE` VARCHAR(200) default '' NOT NULL,
	PRIMARY KEY (`TRN_CATEGORY`,`TRN_ID`,`TRN_LANG`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- TRIGGERS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `TRIGGERS`;


CREATE TABLE `TRIGGERS`
(
	`TRI_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`TRI_TYPE` VARCHAR(20) default 'SCRIPT' NOT NULL,
	`TRI_WEBBOT` TEXT  NOT NULL,
	PRIMARY KEY (`TRI_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
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
	`USR_DUE_DATE` DATE  NOT NULL,
	`USR_CREATE_DATE` DATETIME  NOT NULL,
	`USR_UPDATE_DATE` DATETIME  NOT NULL,
	`USR_STATUS` VARCHAR(32) default 'ACTIVE' NOT NULL,
	`USR_COUNTRY` VARCHAR(3) default '' NOT NULL,
	`USR_CITY` VARCHAR(3) default '' NOT NULL,
	`USR_LOCATION` VARCHAR(3) default '' NOT NULL,
	`USR_ADDRESS` VARCHAR(255) default '' NOT NULL,
	`USR_PHONE` VARCHAR(24) default '' NOT NULL,
	`USR_FAX` VARCHAR(24) default '' NOT NULL,
	`USR_CELLULAR` VARCHAR(24) default '' NOT NULL,
	`USR_ZIP_CODE` VARCHAR(16) default '' NOT NULL,
	`USR_DEPARTMENT` INTEGER default 0 NOT NULL,
	`USR_POSITION` VARCHAR(100) default '' NOT NULL,
	`USR_RESUME` VARCHAR(100) default '' NOT NULL,
	`USR_BIRTHDAY` DATE  NOT NULL,
	`USR_ROLE` VARCHAR(32) default 'PROCESSMAKER_ADMIN',
	PRIMARY KEY (`USR_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Users';
#-----------------------------------------------------------------------------
#-- APP_THREAD
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_THREAD`;


CREATE TABLE `APP_THREAD`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`APP_THREAD_INDEX` INTEGER default 0 NOT NULL,
	`APP_THREAD_PARENT` INTEGER default 0 NOT NULL,
	`APP_THREAD_STATUS` VARCHAR(32) default 'OPEN' NOT NULL,
	`DEL_INDEX` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`APP_UID`,`APP_THREAD_INDEX`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='APP_THREAD';
#-----------------------------------------------------------------------------
#-- APP_DELAY
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_DELAY`;


CREATE TABLE `APP_DELAY`
(
	`APP_DELAY_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`APP_UID` VARCHAR(32) default '0' NOT NULL,
	`APP_THREAD_INDEX` INTEGER default 0 NOT NULL,
	`APP_DEL_INDEX` INTEGER default 0 NOT NULL,
	`APP_TYPE` VARCHAR(20) default '0' NOT NULL,
	`APP_STATUS` VARCHAR(20) default '0' NOT NULL,
	`APP_NEXT_TASK` VARCHAR(32) default '0',
	`APP_DELEGATION_USER` VARCHAR(32) default '0',
	`APP_ENABLE_ACTION_USER` VARCHAR(32) default '0' NOT NULL,
	`APP_ENABLE_ACTION_DATE` DATETIME  NOT NULL,
	`APP_DISABLE_ACTION_USER` VARCHAR(32) default '0',
	`APP_DISABLE_ACTION_DATE` DATETIME,
	`APP_AUTOMATIC_DISABLED_DATE` DATETIME,
	PRIMARY KEY (`APP_DELAY_UID`),
	KEY `indexAppDelay`(`PRO_UID`, `APP_UID`, `APP_THREAD_INDEX`, `APP_DEL_INDEX`, `APP_NEXT_TASK`, `APP_DELEGATION_USER`, `APP_DISABLE_ACTION_USER`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='APP_DELAY';
#-----------------------------------------------------------------------------
#-- PROCESS_USER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `PROCESS_USER`;


CREATE TABLE `PROCESS_USER`
(
	`PU_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`PU_TYPE` VARCHAR(20) default '' NOT NULL,
	PRIMARY KEY (`PU_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- SESSION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `SESSION`;


CREATE TABLE `SESSION`
(
	`SES_UID` VARCHAR(32) default '' NOT NULL,
	`SES_STATUS` VARCHAR(16) default 'ACTIVE' NOT NULL,
	`USR_UID` VARCHAR(32) default 'ACTIVE' NOT NULL,
	`SES_REMOTE_IP` VARCHAR(32) default '0.0.0.0' NOT NULL,
	`SES_INIT_DATE` VARCHAR(19) default '' NOT NULL,
	`SES_DUE_DATE` VARCHAR(19) default '' NOT NULL,
	`SES_END_DATE` VARCHAR(19) default '' NOT NULL,
	PRIMARY KEY (`SES_UID`),
	KEY `indexSession`(`SES_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='SESSION';
#-----------------------------------------------------------------------------
#-- DB_SOURCE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `DB_SOURCE`;


CREATE TABLE `DB_SOURCE`
(
	`DBS_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`DBS_TYPE` VARCHAR(8) default '0' NOT NULL,
	`DBS_SERVER` VARCHAR(100) default '0' NOT NULL,
	`DBS_DATABASE_NAME` VARCHAR(100) default '0' NOT NULL,
	`DBS_USERNAME` VARCHAR(32) default '0' NOT NULL,
	`DBS_PASSWORD` VARCHAR(32) default '',
	`DBS_PORT` INTEGER default 0,
	PRIMARY KEY (`DBS_UID`),
	KEY `indexDBSource`(`PRO_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='DB_SOURCE';
#-----------------------------------------------------------------------------
#-- STEP_SUPERVISOR
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `STEP_SUPERVISOR`;


CREATE TABLE `STEP_SUPERVISOR`
(
	`STEP_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`STEP_TYPE_OBJ` VARCHAR(20) default 'DYNAFORM' NOT NULL,
	`STEP_UID_OBJ` VARCHAR(32) default '0' NOT NULL,
	`STEP_POSITION` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`STEP_UID`),
	KEY `indexStepSupervisor`(`PRO_UID`, `STEP_TYPE_OBJ`, `STEP_UID_OBJ`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='STEP_SUPERVISOR';
#-----------------------------------------------------------------------------
#-- OBJECT_PERMISSION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `OBJECT_PERMISSION`;


CREATE TABLE `OBJECT_PERMISSION`
(
	`OP_UID` VARCHAR(32) default '0' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`TAS_UID` VARCHAR(32) default '0' NOT NULL,
	`USR_UID` VARCHAR(32) default '0' NOT NULL,
	`OP_USER_RELATION` INTEGER default 0 NOT NULL,
	`OP_TASK_SOURCE` VARCHAR(32) default '0',
	`OP_PARTICIPATE` INTEGER default 0 NOT NULL,
	`OP_OBJ_TYPE` VARCHAR(15) default '0' NOT NULL,
	`OP_OBJ_UID` VARCHAR(32) default '0' NOT NULL,
	`OP_ACTION` VARCHAR(10) default '0' NOT NULL,
	PRIMARY KEY (`OP_UID`),
	KEY `indexObjctPermission`(`PRO_UID`, `TAS_UID`, `USR_UID`, `OP_TASK_SOURCE`, `OP_OBJ_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='OBJECT_PERMISSION';
#-----------------------------------------------------------------------------
#-- CASE_TRACKER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `CASE_TRACKER`;


CREATE TABLE `CASE_TRACKER`
(
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`CT_MAP_TYPE` VARCHAR(10) default '0' NOT NULL,
	`CT_DERIVATION_HISTORY` INTEGER default 0 NOT NULL,
	`CT_MESSAGE_HISTORY` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`PRO_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8' COMMENT='CASE_TRACKER';
#-----------------------------------------------------------------------------
#-- CASE_TRACKER_OBJECT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `CASE_TRACKER_OBJECT`;


CREATE TABLE `CASE_TRACKER_OBJECT`
(
	`CTO_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`CTO_TYPE_OBJ` VARCHAR(20) default 'DYNAFORM' NOT NULL,
	`CTO_UID_OBJ` VARCHAR(32) default '0' NOT NULL,
	`CTO_CONDITION` TEXT  NOT NULL,
	`CTO_POSITION` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`CTO_UID`),
	KEY `indexCaseTrackerObject`(`PRO_UID`, `CTO_UID_OBJ`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- STAGE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `STAGE`;


CREATE TABLE `STAGE`
(
	`STG_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`STG_POSX` INTEGER default 0 NOT NULL,
	`STG_POSY` INTEGER default 0 NOT NULL,
	`STG_INDEX` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`STG_UID`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- SUB_PROCESS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `SUB_PROCESS`;


CREATE TABLE `SUB_PROCESS`
(
	`SP_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_PARENT` VARCHAR(32) default '' NOT NULL,
	`TAS_PARENT` VARCHAR(32) default '' NOT NULL,
	`SP_TYPE` VARCHAR(20) default '' NOT NULL,
	`SP_SYNCHRONOUS` INTEGER default 0 NOT NULL,
	`SP_SYNCHRONOUS_TYPE` VARCHAR(20) default '' NOT NULL,
	`SP_SYNCHRONOUS_WAIT` INTEGER default 0 NOT NULL,
	`SP_VARIABLES_OUT` TEXT  NOT NULL,
	`SP_VARIABLES_IN` TEXT  NOT NULL,
	`SP_GRID_IN` VARCHAR(50) default '' NOT NULL,
	PRIMARY KEY (`SP_UID`),
	KEY `indexSubProcess`(`PRO_UID`, `PRO_PARENT`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- SUB_APPLICATION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `SUB_APPLICATION`;


CREATE TABLE `SUB_APPLICATION`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`APP_PARENT` VARCHAR(32) default '' NOT NULL,
	`DEL_INDEX_PARENT` INTEGER default 0 NOT NULL,
	`DEL_THREAD_PARENT` INTEGER default 0 NOT NULL,
	`SA_STATUS` VARCHAR(32) default '' NOT NULL,
	`SA_VALUES_OUT` TEXT  NOT NULL,
	`SA_VALUES_IN` TEXT  NOT NULL,
	`SA_INIT_DATE` DATETIME  NOT NULL,
	`SA_FINISH_DATE` DATETIME  NOT NULL,
	PRIMARY KEY (`APP_UID`,`APP_PARENT`,`DEL_INDEX_PARENT`,`DEL_THREAD_PARENT`)
)Type=MyISAM  DEFAULT CHARSET='utf8';
# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
