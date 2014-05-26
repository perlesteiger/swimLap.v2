-- =======================================================================================================================================================================================
--				CREATION DE LA BASE DE DONNEES
-- =======================================================================================================================================================================================
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
   constraint CK_RACE_RAC_NAME check (RAC_NAME IN ('25 Nage Libre', '50 Nage Libre', '100 Nage Libre', '200 Nage Libre', '400 Nage Libre', '800 Nage Libre', '1000 Nage Libre', '1500 Nage Libre', '3000 Nage Libre', '5000 Nage Libre', '25 Dos', '50 Dos', '100 Dos', '200 Dos', '25 Brasse', '50 Brasse', '100 Brasse', '200 Brasse', '25 Papillon', '50 Papillon', '100 Papillon', '200 Papillon', '100 4 Nages', '200 4 Nages', '400 4 Nages')),
   constraint CK_RACE_RAC_GENDER check (RAC_GENDER IN ('F','M','Mixed'))
);

create table if not exists T_E_ROUND_ROU (
   ROU_ID integer not null,
   ROU_NAME varchar(50) not null,
   constraint PK_ROUND primary key (ROU_ID)
);

create table if not exists T_J_RECORD_REC (
   REC_ID serial not null,
   REC_SWI_ID integer not null,
   REC_RAC_ID integer not null,
   REC_SWIMTIME_25 numeric(10,4),
   REC_SWIMTIME_50 numeric(10,4),
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
   EVE_TYPE varchar(10) DEFAULT 'RACE',
   EVE_MEE_ID integer not null,
   EVE_RAC_ID integer not null,
   EVE_ROU_ID integer not null,
   constraint PK_EVENT primary key (EVE_ID),
   constraint UQ_EVENT unique (EVE_ID),
   constraint CK_EVENT_EVE_TYPE check (EVE_TYPE IN ('RACE', 'EVENT'))
);

create table if not exists T_J_RESULT_RES (
   RES_ID serial not null,
   RES_SWI_ID integer not null,
   RES_EVE_ID integer not null,
   RES_SWIM_TIME numeric(10,4) not null,
   RES_STEP integer not null,
   RES_SPLITS numeric(10,4)[],
   constraint PK_RESULT primary key (RES_ID),
   constraint UQ_RESULT unique (RES_ID),
   constraint CK_RESULT_RES_STEP check (RES_STEP IN (25, 50))
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

-- =======================================================================================================================================================================================
--				INSERTION DE DONNEES
-- =======================================================================================================================================================================================
-- Insertion Club
INSERT INTO t_e_club_clu(clu_id, clu_code, clu_name)
VALUES (1209, 090064358, 'OLYMPIC NICE NATATION');

-- Insertion Seasons
INSERT INTO t_e_season_sea(sea_id, sea_name, sea_start_date, sea_end_date)
VALUES (DEFAULT, '2012-2013', '16-09-2012', '15-09-2013');
INSERT INTO t_e_season_sea(sea_id, sea_name, sea_start_date, sea_end_date)
VALUES (DEFAULT, '2013-2014', '16-09-2013', '15-09-2014');

-- Insertion Races
/* -------------------------------------------------------------- */
/* --                     EPREUVES DAMES                       -- */
/* -------------------------------------------------------------- */
/* Nage Libre */
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (100, '25 Nage Libre', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (1, '50 Nage Libre', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (2, '100 Nage Libre', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (3, '200 Nage Libre', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (4, '400 Nage Libre', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (5, '800 Nage Libre', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (7, '1000 Nage Libre', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (6, '1500 Nage Libre', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (16, '3000 Nage Libre', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (15, '5000 Nage Libre', false, 'F');
/* Dos */
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (110, '25 Dos', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (11, '50 Dos', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (12, '100 Dos', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (13, '200 Dos', false, 'F');
/* Brasse */
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (120, '25 Brasse', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (21, '50 Brasse', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (22, '100 Brasse', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (23, '200 Brasse', false, 'F');
/* Papillon */
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (130, '25 Papillon', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (31, '50 Papillon', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (32, '100 Papillon', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (33, '200 Papillon', false, 'F');
/* 4 Nages */
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (40, '100 4 Nages', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (41, '200 4 Nages', false, 'F');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (42, '400 4 Nages', false, 'F');

-- Insertion Rounds
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (11, 'Finale A');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (12, 'Finale B');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (13, 'Finale C');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (14, 'Finale D');

INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (31, '1/2 Finale (1)');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (32, '1/2 Finale (2)');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (39, 'Barrage 1/2 Finales');

INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (41, '1/4 Finale (1)');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (42, '1/4 Finale (2)');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (43, '1/4 Finale (3)');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (44, '1/4 Finale (4)');
VALUES (49, 'Barrage 1/4 Finales');

INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (51, '1/8 Finale (1)');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (52, '1/8 Finale (2)');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (53, '1/8 Finale (3)');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (54, '1/8 Finale (4)');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (55, '1/8 Finale (5)');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (56, '1/8 Finale (6)');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (57, '1/8 Finale (7)');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (58, '1/8 Finale (8)');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (59, 'Barrage 1/8 Finales');

INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (60, 'Séries');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (61, 'Série rapide');
INSERT INTO t_e_round_rou(rou_id, rou_name)
VALUES (62, 'Séries lentes');

/* -------------------------------------------------------------- */
/* --                   EPREUVES MESSIEURS                     -- */
/* -------------------------------------------------------------- */
/* Nage Libre */
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (150, '25 Nage Libre', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (51, '50 Nage Libre', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (52, '100 Nage Libre', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (53, '200 Nage Libre', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (54, '400 Nage Libre', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (55, '800 Nage Libre', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (57, '1000 Nage Libre', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (56, '1500 Nage Libre', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (66, '3000 Nage Libre', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (65, '5000 Nage Libre', false, 'M');
/* Dos */
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (160, '25 Dos', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (61, '50 Dos', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (62, '100 Dos', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (63, '200 Dos', false, 'M');
/* Brasse */
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (170, '25 Brasse', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (71, '50 Brasse', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (72, '100 Brasse', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (73, '200 Brasse', false, 'M');
/* Papillon */
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (180, '25 Papillon', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (81, '50 Papillon', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (82, '100 Papillon', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (83, '200 Papillon', false, 'M');
/* 4 Nages */
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (90, '100 4 Nages', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (91, '200 4 Nages', false, 'M');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (92, '400 4 Nages', false, 'M');

/* -------------------------------------------------------------- */
/* --                    EPREUVES MIXTES                       -- */
/* -------------------------------------------------------------- */
/* Nage Libre */
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (200, '25 Nage Libre', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (201, '50 Nage Libre', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (202, '100 Nage Libre', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (203, '200 Nage Libre', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (204, '400 Nage Libre', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (205, '800 Nage Libre', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (207, '1000 Nage Libre', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (206, '1500 Nage Libre', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (216, '3000 Nage Libre', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (215, '5000 Nage Libre', false, 'Mixed');
/* Dos */
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (210, '25 Dos', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (211, '50 Dos', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (212, '100 Dos', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (213, '200 Dos', false, 'Mixed');
/* Brasse */
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (220, '25 Brasse', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (221, '50 Brasse', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (222, '100 Brasse', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (223, '200 Brasse', false, 'Mixed');
/* Papillon */
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (230, '25 Papillon', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (231, '50 Papillon', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (232, '100 Papillon', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (233, '200 Papillon', false, 'Mixed');
/* 4 Nages */
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (240, '100 4 Nages', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (241, '200 4 Nages', false, 'Mixed');
INSERT INTO t_e_race_rac(rac_id, rac_name, rac_is_relay, rac_gender)
VALUES (242, '400 4 Nages', false, 'Mixed');

-- =======================================================================================================================================================================================
-- 				PROCEDURES STOCKEES
-- =======================================================================================================================================================================================
-- Function: ps_insertnewevent(character varying, integer, integer, integer)

-- DROP FUNCTION ps_insertnewevent(character varying, integer, integer, integer);

CREATE OR REPLACE FUNCTION ps_insertnewevent(pevetype character varying, pmeeid integer, praceid integer, proundid integer)
  RETURNS void AS
$BODY$
BEGIN
	IF NOT EXISTS (
		SELECT eve_type, eve_mee_id, eve_rac_id, eve_rou_id
		FROM t_e_event_eve
		WHERE eve_type = pEveType AND eve_mee_id = pMeeId AND eve_rac_id = pRaceId
			AND eve_rou_id = pRoundId)
		THEN
			INSERT INTO t_e_event_eve (eve_id, eve_type, eve_mee_id, eve_rac_id, eve_rou_id)
			VALUES(DEFAULT, pEveType, pMeeId, pRaceId, pRoundId);
	END IF;
EXCEPTION
	WHEN OTHERS THEN
		RAISE EXCEPTION 'Impossible d’inserer l evenement.';
END;
$BODY$
  LANGUAGE 'plpgsql';

-- =======================================================================================================================================================================================
-- Function: ps_insertnewmeeting(integer, character varying, character varying, date, date, integer, integer)

-- DROP FUNCTION ps_insertnewmeeting(integer, character varying, character varying, date, date, integer, integer);

CREATE OR REPLACE FUNCTION ps_insertnewmeeting(pid integer, pname character varying, pcity character varying, pstartdate date, penddate date, ppoolsize integer, pseaid integer)
  RETURNS void AS
$BODY$
BEGIN
	IF NOT EXISTS (SELECT mee_id FROM t_e_meeting_mee WHERE mee_id = pId) THEN
		INSERT INTO t_e_meeting_mee (mee_id, mee_name, mee_city, mee_start_date, mee_end_date, mee_pool_size, mee_sea_id)
		VALUES(pId, pName, pCity, pStartDate, pEndDate, pPoolSize, pSeaId);
	END IF;
EXCEPTION
	WHEN OTHERS THEN
		RAISE EXCEPTION 'Impossible d’inserer le meeting';
END;
$BODY$
  LANGUAGE 'plpgsql';

-- =======================================================================================================================================================================================
-- Function: ps_insertnewresult(integer, integer, numeric, integer, numeric[])

-- DROP FUNCTION ps_insertnewresult(integer, integer, numeric, integer, numeric[]);

CREATE OR REPLACE FUNCTION ps_insertnewresult(pswiid integer, peveid integer, pswimtime numeric, pstep integer, psplits numeric[])
  RETURNS void AS
$BODY$
BEGIN
	INSERT INTO t_j_result_res (res_swi_id, res_eve_id, res_swim_time, res_step, res_splits)
	VALUES(pSwiId, pEveId, pSwimTime, pStep, pSplits);
EXCEPTION
	WHEN OTHERS THEN
		RAISE EXCEPTION 'Impossible d’inserer le resultat';
END;
$BODY$
  LANGUAGE 'plpgsql';

-- =======================================================================================================================================================================================
-- Function: ps_insertnewswimmer(integer, character varying, character varying, date, character, integer)

-- DROP FUNCTION ps_insertnewswimmer(integer, character varying, character varying, date, character, integer);

CREATE OR REPLACE FUNCTION ps_insertnewswimmer(pid integer, pfirstname character varying, plastname character varying, pdateofbirth date, pgender character, pclubid integer)
  RETURNS void AS
$BODY$
BEGIN
	IF NOT EXISTS (SELECT swi_id FROM t_e_swimmer_swi WHERE swi_id = pId) THEN
	    INSERT INTO t_e_swimmer_swi (swi_id, swi_firstname, swi_lastname, swi_dateofbirth, swi_gender, swi_clu_id)
	    VALUES(pId, pFirstName, pLastName, pDateOfBirth, pGender, pClubId);
	END IF;
EXCEPTION
	WHEN OTHERS THEN
		RAISE EXCEPTION 'Impossible d’inserer le nageur';
END;
$BODY$
  LANGUAGE 'plpgsql';

-- =======================================================================================================================================================================================
-- Function: check_records()

-- DROP FUNCTION check_records();

CREATE OR REPLACE FUNCTION check_records()
  RETURNS boolean AS
$BODY$
DECLARE
	result RECORD;
	v_is_new_record boolean := false;
BEGIN
	FOR result IN SELECT res_swi_id, res_swim_time, res_step, eve_rac_id
			FROM t_j_result_res
				JOIN t_e_event_eve ON res_eve_id = eve_id
			ORDER BY res_swi_id ASC
	LOOP
		v_is_new_record := isBestTime(result.res_swim_time, result.res_swi_id, result.eve_rac_id, result.res_step);
	END LOOP;
	RETURN v_is_new_record;
END;
$BODY$
  LANGUAGE 'plpgsql';

-- =======================================================================================================================================================================================
-- Function: check_record_for_meeting(integer)

-- DROP FUNCTION check_record_for_meeting(integer);

CREATE OR REPLACE FUNCTION check_record_for_meeting(v_mee_id integer)
  RETURNS boolean AS
$BODY$
DECLARE
	result RECORD;
	v_is_new_record boolean := false;
BEGIN
	FOR result IN SELECT res_swi_id, res_swim_time, res_step, eve_rac_id
			FROM t_j_result_res
				JOIN t_e_event_eve ON res_eve_id = eve_id
			WHERE eve_mee_id = v_mee_id
			ORDER BY res_swi_id ASC
	LOOP
		v_is_new_record := isBestTime(result.res_swim_time, result.res_swi_id, result.eve_rac_id, result.res_step);
	END LOOP;
	RETURN v_is_new_record;
END;
$BODY$
  LANGUAGE 'plpgsql';

-- =======================================================================================================================================================================================
-- Function: isbesttime(numeric, integer, integer, integer)

-- DROP FUNCTION isbesttime(numeric, integer, integer, integer);

CREATE OR REPLACE FUNCTION isbesttime(swimtime numeric, swimmer integer, race integer, pool_size integer)
  RETURNS boolean AS
$BODY$
DECLARE
	v_is_new_record boolean := false;
	v_current_record numeric(10,4);
BEGIN
	IF pool_size = 25 THEN
		SELECT rec_swimtime_25 INTO v_current_record FROM t_j_record_rec
			WHERE rec_swi_id = swimmer AND rec_rac_id = race;
		IF v_current_record IS NULL THEN
			INSERT INTO t_j_record_rec(rec_swi_id, rec_rac_id, rec_swimtime_25)
				VALUES (swimmer, race, swimtime);
			v_is_new_record := true;
		ELSIF swimtime < v_current_record THEN
			UPDATE t_j_record_rec
			SET rec_swimtime_25 = swimtime
			WHERE rec_swi_id = swimmer AND rec_rac_id = race;
			v_is_new_record := true;
		END IF;
	ELSE
		SELECT rec_swimtime_50 INTO v_current_record FROM t_j_record_rec
			WHERE rec_swi_id = swimmer AND rec_rac_id = race;
		IF v_current_record IS NULL THEN
			INSERT INTO t_j_record_rec(rec_swi_id, rec_rac_id, rec_swimtime_50)
				VALUES (swimmer, race, swimtime);
			v_is_new_record := true;
		ELSIF swimtime < v_current_record THEN
			UPDATE t_j_record_rec
			SET rec_swimtime_50 = swimtime
			WHERE rec_swi_id = swimmer AND rec_rac_id = race;
			v_is_new_record := true;
		END IF;
	END IF;
	RETURN v_is_new_record;
END;
$BODY$
  LANGUAGE 'plpgsql';
