drop table if exists T_E_CLUB_CLU cascade;
drop table if exists T_E_SEASON_SEA cascade;
drop table if exists T_E_SWIMMER_SWI cascade;
drop table if exists T_E_RACE_RAC cascade;
drop table if exists T_E_ROUND_ROU cascade;
drop table if exists T_E_EVENT_EVE cascade;
drop table if exists T_E_MEETING_MEE cascade;
drop table if exists T_J_RECORD_REC cascade;
drop table if exists T_J_RESULT_RES cascade;

create table if not exists T_E_CLUB_CLU (
   CLU_ID integer not null,
   CLU_CODE integer not null,
   CLU_NAME varchar(80) not null,
   constraint PK_CLUB primary key (CLU_ID),
   constraint UQ_CLUB unique (CLU_ID, CLU_CODE)
);

create table if not exists T_E_SEASON_SEA (
   SEA_ID serial not null,
   SEA_NAME varchar(50),
   SEA_START_DATE date not null,
   SEA_END_DATE date not null,
   constraint PK_SEASON primary key (SEA_ID)
);

create table if not exists T_E_SWIMMER_SWI (
   SWI_ID integer not null,
   SWI_FIRSTNAME varchar(50) not null,
   SWI_LASTNAME varchar(50) not null,
   SWI_DATEOFBIRTH date,
   SWI_GENDER char(5) not null,
   SWI_CLU_ID integer not null,
   constraint PK_SWIMMER primary key (SWI_ID),
   constraint UQ_SWIMMER unique (SWI_ID),
   constraint CK_SWIMMER_SWI_GENDER check (SWI_GENDER IN ('F','M'))
);

create table if not exists T_E_RACE_RAC (
   RAC_ID integer not null,
   RAC_NAME varchar(50) not null,
   RAC_IS_RELAY boolean not null,
   RAC_GENDER char(5) not null,
   constraint PK_RACE primary key (RAC_ID),
   constraint UQ_RACE unique (RAC_ID),
   constraint CK_RACE_RAC_NAME check (RAC_NAME IN ('25 Nage Libre', '50 Nage Libre', '100 Nage Libre', '200 Nage Libre', '400 Nage Libre', '800 Nage Libre', '1000 Nage Libre', '1500 Nage Libre', '3000 Nage Libre', '5000 Nage Libre', '25 Dos', '50 Dos', '100 Dos', '200  Dos', '25 Brasse', '50 Brasse', '100 Brasse', '200 Brasse', '25 Papillon', '50 Papillon', '100 Papillon', '200 Papillon', '100 4 Nages', '200 4 Nages', '400 4 Nages')),
   constraint CK_RACE_RAC_GENDER check (RAC_GENDER IN ('F','M'))
);

create table if not exists T_E_ROUND_ROU (
   ROU_ID integer not null,
   ROU_NAME varchar(50) not null,
   constraint PK_ROUND primary key (ROU_ID),
   constraint UQ_ROUND unique (ROU_ID)
);

create table if not exists T_J_RECORD_REC (
   REC_ID serial not null,
   REC_SWI_ID integer not null,
   REC_RAC_ID integer not null,
   REC_SWIMTIME_25 numeric(10),
   REC_SWIMTIME_50 numeric(10),
   constraint PK_RECORD primary key (REC_ID),
   constraint UQ_RECORD unique (REC_ID)
);

create table if not exists T_E_MEETING_MEE (
   MEE_ID integer not null,
   MEE_NAME varchar(50) not null,
   MEE_CITY varchar(50) not null,
   MEE_START_DATE date not null,
   MEE_END_DATE date not null,
   MEE_POOL_SIZE integer not null,
   MEE_SEA_ID integer not null,
   constraint PK_MEETING primary key (MEE_ID),
   constraint UQ_MEETING unique (MEE_ID),
   constraint CK_MEETING_MEE_POOL_SIZE check (MEE_POOL_SIZE IN (25, 50))
);

create table if not exists T_E_EVENT_EVE (
   EVE_ID serial not null,
   EVE_TYPE varchar(10) not null,
   EVE_MEE_ID integer not null,
   EVE_RAC_ID integer not null,
   EVE_ROU_ID integer not null,
   EVE_ORDER integer not null,
   EVE_DATETIME timestamp not null,
   constraint PK_EVENT primary key (EVE_ID),
   constraint UQ_EVENT unique (EVE_ID),
   constraint CK_EVENT_EVE_TYPE check (EVE_TYPE IN ('RACE', 'EVENT'))
);

create table if not exists T_J_RESULT_RES (
   RES_ID serial not null,
   RES_SWI_ID integer not null,
   RES_EVE_ID integer not null,
   RES_QUALIF_TIME numeric(10) not null,
   RES_SWIM_TIME numeric(10) not null,
   RES_SPLITS numeric(10)[],
   constraint PK_RESULT primary key (RES_ID),
   constraint UQ_RESULT unique (RES_ID)
);

alter table T_E_SWIMMER_SWI
   add constraint FK_T_E_SWIMMER_SWI_T_E_CLUB_CLU foreign key (SWI_CLU_ID)
      references T_E_CLUB_CLU (CLU_ID) on delete cascade;

alter table T_J_RECORD_REC
   add constraint FK_T_J_RECORD_REC_T_E_SWIMMER_SWI foreign key (REC_SWI_ID)
      references T_E_SWIMMER_SWI (SWI_ID) on delete cascade;

alter table T_J_RECORD_REC
   add constraint FK_T_J_RECORD_REC_T_E_RACE_RAC foreign key (REC_RAC_ID)
      references T_E_RACE_RAC (RAC_ID) on delete cascade;

alter table T_E_MEETING_MEE
   add constraint FK_T_E_MEETING_MEE_T_E_SEASON_SEA foreign key (MEE_SEA_ID)
      references T_E_SEASON_SEA (SEA_ID) on delete cascade;

alter table T_E_EVENT_EVE
   add constraint FK_T_E_EVENT_EVE_T_E_MEETING_MEE foreign key (EVE_MEE_ID)
      references T_E_MEETING_MEE (MEE_ID) on delete cascade;

alter table T_E_EVENT_EVE
   add constraint FK_T_E_EVENT_EVE_T_E_RACE_RAC foreign key (EVE_RAC_ID)
      references T_E_RACE_RAC (RAC_ID) on delete cascade;

alter table T_E_EVENT_EVE
   add constraint FK_T_E_EVENT_EVE_T_E_ROUND_ROU foreign key (EVE_ROU_ID)
      references T_E_ROUND_ROU (ROU_ID) on delete cascade;

alter table T_J_RESULT_RES
   add constraint FK_T_J_RESULT_RES_T_E_EVENT_EVE foreign key (RES_EVE_ID)
      references T_E_EVENT_EVE (EVE_ID) on delete cascade;

alter table T_J_RESULT_RES
   add constraint FK_T_J_RESULT_RES_T_E_SWIMMER_SWI foreign key (RES_SWI_ID)
      references T_E_SWIMMER_SWI (SWI_ID) on delete cascade;