
/* ---------------------------------------------------------------------- */
/* PERMISSIONS											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'PERMISSIONS')
BEGIN
	 DECLARE @reftable_1 nvarchar(60), @constraintname_1 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'PERMISSIONS'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_1, @constraintname_1
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_1+' drop constraint '+@constraintname_1)
	   FETCH NEXT from refcursor into @reftable_1, @constraintname_1
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [PERMISSIONS]
END


CREATE TABLE [PERMISSIONS]
(
	[PER_UID] VARCHAR(32) default '' NOT NULL,
	[PER_CODE] VARCHAR(32) default '' NOT NULL,
	[PER_CREATE_DATE] DATETIME default '0000-00-00 00:00:00' NOT NULL,
	[PER_UPDATE_DATE] DATETIME default '0000-00-00 00:00:00' NOT NULL,
	[PER_STATUS] INT default 1 NOT NULL,
	CONSTRAINT PERMISSIONS_PK PRIMARY KEY ([PER_UID])
);

/* ---------------------------------------------------------------------- */
/* ROLES											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'ROLES')
BEGIN
	 DECLARE @reftable_2 nvarchar(60), @constraintname_2 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'ROLES'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_2, @constraintname_2
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_2+' drop constraint '+@constraintname_2)
	   FETCH NEXT from refcursor into @reftable_2, @constraintname_2
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [ROLES]
END


CREATE TABLE [ROLES]
(
	[ROL_UID] VARCHAR(32) default '' NOT NULL,
	[ROL_PARENT] VARCHAR(32) default '' NOT NULL,
	[ROL_SYSTEM] VARCHAR(32) default '' NOT NULL,
	[ROL_CODE] VARCHAR(32) default '' NOT NULL,
	[ROL_CREATE_DATE] DATETIME default '0000-00-00 00:00:00' NOT NULL,
	[ROL_UPDATE_DATE] DATETIME default '0000-00-00 00:00:00' NOT NULL,
	[ROL_STATUS] INT default 1 NOT NULL,
	CONSTRAINT ROLES_PK PRIMARY KEY ([ROL_UID])
);

/* ---------------------------------------------------------------------- */
/* ROLES_PERMISSIONS											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'ROLES_PERMISSIONS')
BEGIN
	 DECLARE @reftable_3 nvarchar(60), @constraintname_3 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'ROLES_PERMISSIONS'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_3, @constraintname_3
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_3+' drop constraint '+@constraintname_3)
	   FETCH NEXT from refcursor into @reftable_3, @constraintname_3
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [ROLES_PERMISSIONS]
END


CREATE TABLE [ROLES_PERMISSIONS]
(
	[ROL_UID] VARCHAR(32) default '' NOT NULL,
	[PER_UID] VARCHAR(32) default '' NOT NULL,
	CONSTRAINT ROLES_PERMISSIONS_PK PRIMARY KEY ([ROL_UID],[PER_UID])
);

/* ---------------------------------------------------------------------- */
/* SYSTEMS											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'SYSTEMS')
BEGIN
	 DECLARE @reftable_4 nvarchar(60), @constraintname_4 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'SYSTEMS'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_4, @constraintname_4
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_4+' drop constraint '+@constraintname_4)
	   FETCH NEXT from refcursor into @reftable_4, @constraintname_4
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [SYSTEMS]
END


CREATE TABLE [SYSTEMS]
(
	[SYS_UID] VARCHAR(32) default '' NOT NULL,
	[SYS_CODE] VARCHAR(32) default '' NOT NULL,
	[SYS_CREATE_DATE] DATETIME default '0000-00-00 00:00:00' NOT NULL,
	[SYS_UPDATE_DATE] DATETIME default '0000-00-00 00:00:00' NOT NULL,
	[SYS_STATUS] INT default 0 NOT NULL,
	CONSTRAINT SYSTEMS_PK PRIMARY KEY ([SYS_UID])
);

/* ---------------------------------------------------------------------- */
/* USERS											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'USERS')
BEGIN
	 DECLARE @reftable_5 nvarchar(60), @constraintname_5 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'USERS'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_5, @constraintname_5
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_5+' drop constraint '+@constraintname_5)
	   FETCH NEXT from refcursor into @reftable_5, @constraintname_5
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [USERS]
END


CREATE TABLE [USERS]
(
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	[USR_USERNAME] VARCHAR(100) default '' NOT NULL,
	[USR_PASSWORD] VARCHAR(32) default '' NOT NULL,
	[USR_FIRSTNAME] VARCHAR(50) default '' NOT NULL,
	[USR_LASTNAME] VARCHAR(50) default '' NOT NULL,
	[USR_EMAIL] VARCHAR(100) default '' NOT NULL,
	[USR_DUE_DATE] DATETIME default '0000-00-00' NOT NULL,
	[USR_CREATE_DATE] DATETIME default '0000-00-00 00:00:00' NOT NULL,
	[USR_UPDATE_DATE] DATETIME default '0000-00-00 00:00:00' NOT NULL,
	[USR_STATUS] INT default 1 NOT NULL,
	CONSTRAINT USERS_PK PRIMARY KEY ([USR_UID])
);

/* ---------------------------------------------------------------------- */
/* USERS_ROLES											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'USERS_ROLES')
BEGIN
	 DECLARE @reftable_6 nvarchar(60), @constraintname_6 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'USERS_ROLES'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_6, @constraintname_6
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_6+' drop constraint '+@constraintname_6)
	   FETCH NEXT from refcursor into @reftable_6, @constraintname_6
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [USERS_ROLES]
END


CREATE TABLE [USERS_ROLES]
(
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	[ROL_UID] VARCHAR(32) default '' NOT NULL,
	CONSTRAINT USERS_ROLES_PK PRIMARY KEY ([USR_UID],[ROL_UID])
);
