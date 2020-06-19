--
-- PostgreSQL database dump
--

-- Dumped from database version 11.2
-- Dumped by pg_dump version 11.2

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: groups_sch; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA groups_sch;


ALTER SCHEMA groups_sch OWNER TO postgres;

--
-- Name: calendar_dynamic_action; Type: TYPE; Schema: groups_sch; Owner: postgres
--

CREATE TYPE groups_sch.calendar_dynamic_action AS ENUM (
    'CHANGE',
    'ERASE'
);


ALTER TYPE groups_sch.calendar_dynamic_action OWNER TO postgres;

--
-- Name: calendar_user_action; Type: TYPE; Schema: groups_sch; Owner: postgres
--

CREATE TYPE groups_sch.calendar_user_action AS ENUM (
    'CHANGE',
    'ERASE'
);


ALTER TYPE groups_sch.calendar_user_action OWNER TO postgres;

--
-- Name: confirm_hash_for; Type: TYPE; Schema: groups_sch; Owner: postgres
--

CREATE TYPE groups_sch.confirm_hash_for AS ENUM (
    'ACCOUNT',
    'PASSWORD',
    'EMAIL'
);


ALTER TYPE groups_sch.confirm_hash_for OWNER TO postgres;

--
-- Name: notifications_refers_to; Type: TYPE; Schema: groups_sch; Owner: postgres
--

CREATE TYPE groups_sch.notifications_refers_to AS ENUM (
    'ALL',
    'GROUP',
    'DAY'
);


ALTER TYPE groups_sch.notifications_refers_to OWNER TO postgres;

--
-- Name: notifications_type; Type: TYPE; Schema: groups_sch; Owner: postgres
--

CREATE TYPE groups_sch.notifications_type AS ENUM (
    'NOTIFICATION',
    'ALERT',
    'WARNING'
);


ALTER TYPE groups_sch.notifications_type OWNER TO postgres;

--
-- Name: notifications_visibility; Type: TYPE; Schema: groups_sch; Owner: postgres
--

CREATE TYPE groups_sch.notifications_visibility AS ENUM (
    'ALL',
    'SELF'
);


ALTER TYPE groups_sch.notifications_visibility OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: calendar; Type: TABLE; Schema: groups_sch; Owner: postgres
--

CREATE TABLE groups_sch.calendar (
    group_id bigint NOT NULL,
    day date NOT NULL,
    weekday integer NOT NULL,
    lesson integer NOT NULL,
    subject character varying(100) NOT NULL,
    type character varying(32) NOT NULL,
    time_start character varying(5) NOT NULL,
    time_end character varying(5) NOT NULL,
    teachers character varying(256) NOT NULL,
    places character varying(256) NOT NULL
);


ALTER TABLE groups_sch.calendar OWNER TO postgres;

--
-- Name: calendar_dynamic; Type: TABLE; Schema: groups_sch; Owner: postgres
--

CREATE TABLE groups_sch.calendar_dynamic (
    id bigint NOT NULL,
    group_id bigint NOT NULL,
    day date NOT NULL,
    weekday integer NOT NULL,
    lesson integer NOT NULL,
    is_odd integer NOT NULL,
    subject character varying(200) DEFAULT NULL::character varying,
    type character varying(32) DEFAULT NULL::character varying,
    time_start character varying(5) DEFAULT NULL::character varying,
    time_end character varying(5) DEFAULT NULL::character varying,
    teachers character varying(1024) DEFAULT NULL::character varying,
    places character varying(1024) DEFAULT NULL::character varying,
    chain bigint NOT NULL,
    action groups_sch.calendar_dynamic_action NOT NULL
);


ALTER TABLE groups_sch.calendar_dynamic OWNER TO postgres;

--
-- Name: calendar_static; Type: TABLE; Schema: groups_sch; Owner: postgres
--

CREATE TABLE groups_sch.calendar_static (
    id bigint NOT NULL,
    group_id bigint NOT NULL,
    weekday integer NOT NULL,
    lesson integer NOT NULL,
    is_odd integer NOT NULL,
    subject character varying(200) NOT NULL,
    type character varying(32) NOT NULL,
    time_start character varying(5) NOT NULL,
    time_end character varying(5) NOT NULL,
    teachers character varying(1024) NOT NULL,
    places character varying(1024) NOT NULL
);


ALTER TABLE groups_sch.calendar_static OWNER TO postgres;

--
-- Name: calendar_stored; Type: TABLE; Schema: groups_sch; Owner: postgres
--

CREATE TABLE groups_sch.calendar_stored (
    group_id bigint NOT NULL,
    day date NOT NULL,
    weekday integer NOT NULL,
    lesson integer NOT NULL,
    is_odd integer NOT NULL,
    subject character varying(200) DEFAULT NULL::character varying,
    type character varying(32) DEFAULT NULL::character varying,
    time_start character varying(5) DEFAULT NULL::character varying,
    time_end character varying(5) DEFAULT NULL::character varying,
    teachers character varying(1024) DEFAULT NULL::character varying,
    places character varying(1024) DEFAULT NULL::character varying
);


ALTER TABLE groups_sch.calendar_stored OWNER TO postgres;

--
-- Name: calendar_user; Type: TABLE; Schema: groups_sch; Owner: postgres
--

CREATE TABLE groups_sch.calendar_user (
    group_id bigint NOT NULL,
    day date,
    weekday integer NOT NULL,
    lesson integer NOT NULL,
    is_odd integer NOT NULL,
    subject character varying(200) DEFAULT NULL::character varying,
    type character varying(32) DEFAULT NULL::character varying,
    time_start character varying(5) DEFAULT NULL::character varying,
    time_end character varying(5) DEFAULT NULL::character varying,
    teachers character varying(1024) DEFAULT NULL::character varying,
    places character varying(1024) DEFAULT NULL::character varying,
    action groups_sch.calendar_user_action NOT NULL
);


ALTER TABLE groups_sch.calendar_user OWNER TO postgres;

--
-- Name: confirm_hash; Type: TABLE; Schema: groups_sch; Owner: postgres
--

CREATE TABLE groups_sch.confirm_hash (
    login character varying(32) DEFAULT NULL::character varying,
    "for" groups_sch.confirm_hash_for,
    value character varying(32) DEFAULT NULL::character varying,
    stored character varying(64) DEFAULT NULL::character varying,
    created timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    lifetime timestamp with time zone
);


ALTER TABLE groups_sch.confirm_hash OWNER TO postgres;

--
-- Name: contributors; Type: TABLE; Schema: groups_sch; Owner: postgres
--

CREATE TABLE groups_sch.contributors (
    group_id bigint,
    user_id bigint
);


ALTER TABLE groups_sch.contributors OWNER TO postgres;

--
-- Name: groups; Type: TABLE; Schema: groups_sch; Owner: postgres
--

CREATE TABLE groups_sch.groups (
    name character varying(16) DEFAULT NULL::character varying,
    id bigint NOT NULL,
    header_login character varying(32) DEFAULT NULL::character varying,
    cache integer DEFAULT 0,
    cache_last timestamp with time zone,
    static_changed timestamp with time zone,
    cache_static integer DEFAULT 0,
    university_id smallint,
    recache_count bigint DEFAULT '5'::bigint,
    year bigint,
    faculty_id bigint,
    faculty_name character varying(128) DEFAULT NULL::character varying,
    faculty_abbr character varying(10) DEFAULT NULL::character varying
);


ALTER TABLE groups_sch.groups OWNER TO postgres;

--
-- Name: head_request; Type: TABLE; Schema: groups_sch; Owner: postgres
--

CREATE TABLE groups_sch.head_request (
    user_id bigint,
    number character varying(17) DEFAULT NULL::character varying,
    vk_link character varying(64) DEFAULT NULL::character varying,
    requested timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE groups_sch.head_request OWNER TO postgres;

--
-- Name: homeworks; Type: TABLE; Schema: groups_sch; Owner: postgres
--

CREATE TABLE groups_sch.homeworks (
    group_id bigint NOT NULL,
    sender_id bigint NOT NULL,
    date date NOT NULL,
    lesson bigint NOT NULL,
    text character varying(1000) DEFAULT NULL::character varying,
    added timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE groups_sch.homeworks OWNER TO postgres;

--
-- Name: move_request; Type: TABLE; Schema: groups_sch; Owner: postgres
--

CREATE TABLE groups_sch.move_request (
    head_id bigint,
    move_from bigint,
    move_to character varying(16) DEFAULT NULL::character varying
);


ALTER TABLE groups_sch.move_request OWNER TO postgres;

--
-- Name: notifications; Type: TABLE; Schema: groups_sch; Owner: postgres
--

CREATE TABLE groups_sch.notifications (
    group_id bigint NOT NULL,
    refers_to groups_sch.notifications_refers_to NOT NULL,
    type groups_sch.notifications_type NOT NULL,
    visibility groups_sch.notifications_visibility NOT NULL,
    date date NOT NULL,
    text character varying(300) NOT NULL,
    sender_id bigint NOT NULL,
    added timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE groups_sch.notifications OWNER TO postgres;

--
-- Name: COLUMN notifications.date; Type: COMMENT; Schema: groups_sch; Owner: postgres
--

COMMENT ON COLUMN groups_sch.notifications.date IS 'Used if refers_to = day or lesson';


--
-- Name: uploads; Type: TABLE; Schema: groups_sch; Owner: postgres
--

CREATE TABLE groups_sch.uploads (
    name character varying(64) NOT NULL,
    original_name character varying(64) NOT NULL,
    showable integer NOT NULL,
    size numeric NOT NULL,
    hash character varying(32) NOT NULL,
    adder_id bigint NOT NULL,
    added timestamp with time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    stored_untill date NOT NULL
);


ALTER TABLE groups_sch.uploads OWNER TO postgres;

--
-- Name: users; Type: TABLE; Schema: groups_sch; Owner: postgres
--

CREATE TABLE groups_sch.users (
    id bigint NOT NULL,
    login character varying(32) NOT NULL,
    email character varying(64) NOT NULL,
    password_hash character varying(60) DEFAULT NULL::character varying,
    password_changed timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    session_hash character varying(64) DEFAULT NULL::character varying,
    last_ip character varying(15) DEFAULT NULL::character varying,
    number character varying(17) DEFAULT NULL::character varying,
    vk_id character varying(64) DEFAULT NULL::character varying,
    is_head integer DEFAULT 0 NOT NULL,
    "group" character varying(16) DEFAULT '0'::character varying NOT NULL,
    rights_group bigint DEFAULT '1'::bigint,
    privileges bigint DEFAULT '16'::bigint NOT NULL,
    verified integer DEFAULT 0,
    active integer DEFAULT 0,
    created timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    year bigint NOT NULL
);


ALTER TABLE groups_sch.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: groups_sch; Owner: postgres
--

CREATE SEQUENCE groups_sch.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE groups_sch.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: groups_sch; Owner: postgres
--

ALTER SEQUENCE groups_sch.users_id_seq OWNED BY groups_sch.users.id;


--
-- Name: users id; Type: DEFAULT; Schema: groups_sch; Owner: postgres
--

ALTER TABLE ONLY groups_sch.users ALTER COLUMN id SET DEFAULT nextval('groups_sch.users_id_seq'::regclass);


--
-- Data for Name: calendar; Type: TABLE DATA; Schema: groups_sch; Owner: postgres
--

COPY groups_sch.calendar (group_id, day, weekday, lesson, subject, type, time_start, time_end, teachers, places) FROM stdin;
\.


--
-- Data for Name: calendar_dynamic; Type: TABLE DATA; Schema: groups_sch; Owner: postgres
--

COPY groups_sch.calendar_dynamic (id, group_id, day, weekday, lesson, is_odd, subject, type, time_start, time_end, teachers, places, chain, action) FROM stdin;
\.


--
-- Data for Name: calendar_static; Type: TABLE DATA; Schema: groups_sch; Owner: postgres
--

COPY groups_sch.calendar_static (id, group_id, weekday, lesson, is_odd, subject, type, time_start, time_end, teachers, places) FROM stdin;
\.


--
-- Data for Name: calendar_stored; Type: TABLE DATA; Schema: groups_sch; Owner: postgres
--

COPY groups_sch.calendar_stored (group_id, day, weekday, lesson, is_odd, subject, type, time_start, time_end, teachers, places) FROM stdin;
\.


--
-- Data for Name: calendar_user; Type: TABLE DATA; Schema: groups_sch; Owner: postgres
--

COPY groups_sch.calendar_user (group_id, day, weekday, lesson, is_odd, subject, type, time_start, time_end, teachers, places, action) FROM stdin;
\.


--
-- Data for Name: confirm_hash; Type: TABLE DATA; Schema: groups_sch; Owner: postgres
--

COPY groups_sch.confirm_hash (login, "for", value, stored, created, lifetime) FROM stdin;
\.


--
-- Data for Name: contributors; Type: TABLE DATA; Schema: groups_sch; Owner: postgres
--

COPY groups_sch.contributors (group_id, user_id) FROM stdin;
\.


--
-- Data for Name: groups; Type: TABLE DATA; Schema: groups_sch; Owner: postgres
--

COPY groups_sch.groups (name, id, header_login, cache, cache_last, static_changed, cache_static, university_id, recache_count, year, faculty_id, faculty_name, faculty_abbr) FROM stdin;
\.


--
-- Data for Name: head_request; Type: TABLE DATA; Schema: groups_sch; Owner: postgres
--

COPY groups_sch.head_request (user_id, number, vk_link, requested) FROM stdin;
\.


--
-- Data for Name: homeworks; Type: TABLE DATA; Schema: groups_sch; Owner: postgres
--

COPY groups_sch.homeworks (group_id, sender_id, date, lesson, text, added) FROM stdin;
\.


--
-- Data for Name: move_request; Type: TABLE DATA; Schema: groups_sch; Owner: postgres
--

COPY groups_sch.move_request (head_id, move_from, move_to) FROM stdin;
\.


--
-- Data for Name: notifications; Type: TABLE DATA; Schema: groups_sch; Owner: postgres
--

COPY groups_sch.notifications (group_id, refers_to, type, visibility, date, text, sender_id, added) FROM stdin;
\.


--
-- Data for Name: uploads; Type: TABLE DATA; Schema: groups_sch; Owner: postgres
--

COPY groups_sch.uploads (name, original_name, showable, size, hash, adder_id, added, stored_untill) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: groups_sch; Owner: postgres
--

COPY groups_sch.users (id, login, email, password_hash, password_changed, session_hash, last_ip, number, vk_id, is_head, "group", rights_group, privileges, verified, active, created, year) FROM stdin;
\.


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: groups_sch; Owner: postgres
--

SELECT pg_catalog.setval('groups_sch.users_id_seq', 1, true);


--
-- Name: calendar idx_16429_primary; Type: CONSTRAINT; Schema: groups_sch; Owner: postgres
--

ALTER TABLE ONLY groups_sch.calendar
    ADD CONSTRAINT idx_16429_primary PRIMARY KEY (group_id, day, lesson);


--
-- Name: calendar_dynamic idx_16435_primary; Type: CONSTRAINT; Schema: groups_sch; Owner: postgres
--

ALTER TABLE ONLY groups_sch.calendar_dynamic
    ADD CONSTRAINT idx_16435_primary PRIMARY KEY (group_id, day, id);


--
-- Name: calendar_static idx_16447_primary; Type: CONSTRAINT; Schema: groups_sch; Owner: postgres
--

ALTER TABLE ONLY groups_sch.calendar_static
    ADD CONSTRAINT idx_16447_primary PRIMARY KEY (group_id, lesson, weekday, is_odd);


--
-- Name: calendar_stored idx_16453_primary; Type: CONSTRAINT; Schema: groups_sch; Owner: postgres
--

ALTER TABLE ONLY groups_sch.calendar_stored
    ADD CONSTRAINT idx_16453_primary PRIMARY KEY (group_id, day, weekday, lesson);


--
-- Name: groups idx_16487_primary; Type: CONSTRAINT; Schema: groups_sch; Owner: postgres
--

ALTER TABLE ONLY groups_sch.groups
    ADD CONSTRAINT idx_16487_primary PRIMARY KEY (id);


--
-- Name: homeworks idx_16503_primary; Type: CONSTRAINT; Schema: groups_sch; Owner: postgres
--

ALTER TABLE ONLY groups_sch.homeworks
    ADD CONSTRAINT idx_16503_primary PRIMARY KEY (group_id, date, lesson);


--
-- Name: uploads idx_16519_primary; Type: CONSTRAINT; Schema: groups_sch; Owner: postgres
--

ALTER TABLE ONLY groups_sch.uploads
    ADD CONSTRAINT idx_16519_primary PRIMARY KEY (name);


--
-- Name: idx_16528_key; Type: INDEX; Schema: groups_sch; Owner: postgres
--

CREATE INDEX idx_16528_key ON groups_sch.users USING btree (id);


--
-- PostgreSQL database dump complete
--

