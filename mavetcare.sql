--
-- PostgreSQL database dump
--

-- Dumped from database version 17.4
-- Dumped by pg_dump version 17.4

-- Started on 2025-05-21 11:31:42

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 230 (class 1259 OID 17233)
-- Name: appointment; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.appointment (
    appt_code integer NOT NULL,
    client_code integer,
    pet_code integer,
    service_code integer,
    appt_datetime timestamp without time zone,
    additional_notes text,
    preferred_date date,
    preferred_time time without time zone,
    appointment_type character varying(20),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    status character varying(20) DEFAULT 'pending'::character varying
);


ALTER TABLE public.appointment OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 17232)
-- Name: appointment_appt_code_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.appointment_appt_code_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.appointment_appt_code_seq OWNER TO postgres;

--
-- TOC entry 4985 (class 0 OID 0)
-- Dependencies: 229
-- Name: appointment_appt_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.appointment_appt_code_seq OWNED BY public.appointment.appt_code;


--
-- TOC entry 218 (class 1259 OID 17171)
-- Name: client; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.client (
    clt_code integer NOT NULL,
    clt_lname character varying(50),
    clt_fname character varying(50),
    clt_initial character varying(10),
    clt_contact character varying(20),
    clt_email_address character varying(100),
    clt_home_address text
);


ALTER TABLE public.client OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 17170)
-- Name: client_clt_code_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.client_clt_code_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.client_clt_code_seq OWNER TO postgres;

--
-- TOC entry 4986 (class 0 OID 0)
-- Dependencies: 217
-- Name: client_clt_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.client_clt_code_seq OWNED BY public.client.clt_code;


--
-- TOC entry 222 (class 1259 OID 17189)
-- Name: pet; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pet (
    pet_code integer NOT NULL,
    client_code integer,
    pet_name character varying(100),
    pet_type character varying(50),
    pet_breed character varying(50),
    pet_age integer,
    pet_med_history text
);


ALTER TABLE public.pet OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 17188)
-- Name: pet_pet_code_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pet_pet_code_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.pet_pet_code_seq OWNER TO postgres;

--
-- TOC entry 4987 (class 0 OID 0)
-- Dependencies: 221
-- Name: pet_pet_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pet_pet_code_seq OWNED BY public.pet.pet_code;


--
-- TOC entry 228 (class 1259 OID 17221)
-- Name: product; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product (
    prod_code integer NOT NULL,
    prod_name character varying(100),
    prod_category character varying(50),
    prod_stock integer,
    prod_price numeric(10,2),
    supp_code integer,
    prod_image character varying(255)
);


ALTER TABLE public.product OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 17220)
-- Name: product_prod_code_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.product_prod_code_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_prod_code_seq OWNER TO postgres;

--
-- TOC entry 4988 (class 0 OID 0)
-- Dependencies: 227
-- Name: product_prod_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.product_prod_code_seq OWNED BY public.product.prod_code;


--
-- TOC entry 238 (class 1259 OID 17304)
-- Name: review; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.review (
    review_code integer NOT NULL,
    client_code integer,
    rating integer,
    comment text,
    review_date timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT review_rating_check CHECK (((rating >= 1) AND (rating <= 5)))
);


ALTER TABLE public.review OWNER TO postgres;

--
-- TOC entry 237 (class 1259 OID 17303)
-- Name: review_review_code_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.review_review_code_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.review_review_code_seq OWNER TO postgres;

--
-- TOC entry 4989 (class 0 OID 0)
-- Dependencies: 237
-- Name: review_review_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.review_review_code_seq OWNED BY public.review.review_code;


--
-- TOC entry 232 (class 1259 OID 17260)
-- Name: sales_transaction; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sales_transaction (
    transaction_code integer NOT NULL,
    client_code integer,
    transaction_total_amount numeric(10,2),
    transaction_pay_method character varying(50),
    transaction_datetime timestamp without time zone
);


ALTER TABLE public.sales_transaction OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 17259)
-- Name: sales_transaction_transaction_code_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sales_transaction_transaction_code_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sales_transaction_transaction_code_seq OWNER TO postgres;

--
-- TOC entry 4990 (class 0 OID 0)
-- Dependencies: 231
-- Name: sales_transaction_transaction_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sales_transaction_transaction_code_seq OWNED BY public.sales_transaction.transaction_code;


--
-- TOC entry 224 (class 1259 OID 17203)
-- Name: service; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.service (
    service_code integer NOT NULL,
    service_name character varying(100),
    service_desc text,
    service_fee numeric(10,2)
);


ALTER TABLE public.service OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 17202)
-- Name: service_service_code_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.service_service_code_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.service_service_code_seq OWNER TO postgres;

--
-- TOC entry 4991 (class 0 OID 0)
-- Dependencies: 223
-- Name: service_service_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.service_service_code_seq OWNED BY public.service.service_code;


--
-- TOC entry 240 (class 1259 OID 17335)
-- Name: staff_schedule; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.staff_schedule (
    schedule_id integer NOT NULL,
    staff_code integer,
    day_of_week character varying(10),
    start_time time without time zone,
    end_time time without time zone
);


ALTER TABLE public.staff_schedule OWNER TO postgres;

--
-- TOC entry 239 (class 1259 OID 17334)
-- Name: staff_schedule_schedule_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.staff_schedule_schedule_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.staff_schedule_schedule_id_seq OWNER TO postgres;

--
-- TOC entry 4992 (class 0 OID 0)
-- Dependencies: 239
-- Name: staff_schedule_schedule_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.staff_schedule_schedule_id_seq OWNED BY public.staff_schedule.schedule_id;


--
-- TOC entry 226 (class 1259 OID 17212)
-- Name: supplier; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.supplier (
    supp_code integer NOT NULL,
    supp_name character varying(100),
    supp_contact_person character varying(100),
    supp_contact_number character varying(20),
    supp_email_address character varying(100),
    supp_product_supplied text
);


ALTER TABLE public.supplier OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 17211)
-- Name: supplier_supp_code_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.supplier_supp_code_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.supplier_supp_code_seq OWNER TO postgres;

--
-- TOC entry 4993 (class 0 OID 0)
-- Dependencies: 225
-- Name: supplier_supp_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.supplier_supp_code_seq OWNED BY public.supplier.supp_code;


--
-- TOC entry 236 (class 1259 OID 17295)
-- Name: sys_user; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sys_user (
    user_code integer NOT NULL,
    username character varying(50) NOT NULL,
    password character varying(255) NOT NULL,
    role character varying(20),
    client_code integer
);


ALTER TABLE public.sys_user OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 17294)
-- Name: sys_user_user_code_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sys_user_user_code_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sys_user_user_code_seq OWNER TO postgres;

--
-- TOC entry 4994 (class 0 OID 0)
-- Dependencies: 235
-- Name: sys_user_user_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sys_user_user_code_seq OWNED BY public.sys_user.user_code;


--
-- TOC entry 234 (class 1259 OID 17272)
-- Name: transaction_details; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.transaction_details (
    detail_code integer NOT NULL,
    transaction_code integer,
    prod_code integer,
    service_code integer,
    quantity integer DEFAULT 1,
    price numeric(10,2)
);


ALTER TABLE public.transaction_details OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 17271)
-- Name: transaction_details_detail_code_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.transaction_details_detail_code_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.transaction_details_detail_code_seq OWNER TO postgres;

--
-- TOC entry 4995 (class 0 OID 0)
-- Dependencies: 233
-- Name: transaction_details_detail_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.transaction_details_detail_code_seq OWNED BY public.transaction_details.detail_code;


--
-- TOC entry 220 (class 1259 OID 17180)
-- Name: veterinary_staff; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.veterinary_staff (
    staff_code integer NOT NULL,
    staff_name character varying(100),
    staff_position character varying(50),
    staff_contact character varying(20),
    staff_email_address character varying(100),
    staff_schedule text
);


ALTER TABLE public.veterinary_staff OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 17179)
-- Name: veterinary_staff_staff_code_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.veterinary_staff_staff_code_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.veterinary_staff_staff_code_seq OWNER TO postgres;

--
-- TOC entry 4996 (class 0 OID 0)
-- Dependencies: 219
-- Name: veterinary_staff_staff_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.veterinary_staff_staff_code_seq OWNED BY public.veterinary_staff.staff_code;


--
-- TOC entry 4756 (class 2604 OID 17236)
-- Name: appointment appt_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appointment ALTER COLUMN appt_code SET DEFAULT nextval('public.appointment_appt_code_seq'::regclass);


--
-- TOC entry 4750 (class 2604 OID 17174)
-- Name: client clt_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.client ALTER COLUMN clt_code SET DEFAULT nextval('public.client_clt_code_seq'::regclass);


--
-- TOC entry 4752 (class 2604 OID 17192)
-- Name: pet pet_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pet ALTER COLUMN pet_code SET DEFAULT nextval('public.pet_pet_code_seq'::regclass);


--
-- TOC entry 4755 (class 2604 OID 17224)
-- Name: product prod_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product ALTER COLUMN prod_code SET DEFAULT nextval('public.product_prod_code_seq'::regclass);


--
-- TOC entry 4763 (class 2604 OID 17307)
-- Name: review review_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.review ALTER COLUMN review_code SET DEFAULT nextval('public.review_review_code_seq'::regclass);


--
-- TOC entry 4759 (class 2604 OID 17263)
-- Name: sales_transaction transaction_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_transaction ALTER COLUMN transaction_code SET DEFAULT nextval('public.sales_transaction_transaction_code_seq'::regclass);


--
-- TOC entry 4753 (class 2604 OID 17206)
-- Name: service service_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.service ALTER COLUMN service_code SET DEFAULT nextval('public.service_service_code_seq'::regclass);


--
-- TOC entry 4765 (class 2604 OID 17338)
-- Name: staff_schedule schedule_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.staff_schedule ALTER COLUMN schedule_id SET DEFAULT nextval('public.staff_schedule_schedule_id_seq'::regclass);


--
-- TOC entry 4754 (class 2604 OID 17215)
-- Name: supplier supp_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.supplier ALTER COLUMN supp_code SET DEFAULT nextval('public.supplier_supp_code_seq'::regclass);


--
-- TOC entry 4762 (class 2604 OID 17298)
-- Name: sys_user user_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sys_user ALTER COLUMN user_code SET DEFAULT nextval('public.sys_user_user_code_seq'::regclass);


--
-- TOC entry 4760 (class 2604 OID 17275)
-- Name: transaction_details detail_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaction_details ALTER COLUMN detail_code SET DEFAULT nextval('public.transaction_details_detail_code_seq'::regclass);


--
-- TOC entry 4751 (class 2604 OID 17183)
-- Name: veterinary_staff staff_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.veterinary_staff ALTER COLUMN staff_code SET DEFAULT nextval('public.veterinary_staff_staff_code_seq'::regclass);


--
-- TOC entry 4969 (class 0 OID 17233)
-- Dependencies: 230
-- Data for Name: appointment; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.appointment (appt_code, client_code, pet_code, service_code, appt_datetime, additional_notes, preferred_date, preferred_time, appointment_type, created_at, status) FROM stdin;
11	1	1	\N	\N	\N	2025-05-16	10:00:00	\N	2025-05-14 03:37:51.822212	pending
18	1	1	1	2025-05-14 10:00:00	Test appointment created via test script	2025-05-14	10:00:00	walk-in	2025-05-14 03:55:15.152664	pending
19	1	1	7	2025-06-07 14:00:00		2025-06-07	14:00:00	walk-in	2025-05-14 04:01:04.335193	pending
20	1	1	1	2025-05-14 10:00:00	Test appointment created directly	2025-05-14	10:00:00	walk-in	2025-05-14 04:01:15.524469	pending
22	1	1	1	2025-05-14 10:00:00	Test appointment created directly	2025-05-14	10:00:00	walk-in	2025-05-14 04:03:59.046526	pending
23	1	1	1	2025-05-14 10:00:00	Test appointment created directly	2025-05-14	10:00:00	walk-in	2025-05-14 04:04:38.064787	pending
25	1	1	1	2025-05-29 11:00:00		2025-05-29	11:00:00	walk-in	2025-05-14 04:07:18.007079	pending
26	1	1	6	2025-05-30 14:00:00		2025-05-30	14:00:00	walk-in	2025-05-14 04:09:46.533768	pending
27	1	1	2	2025-07-03 15:00:00	sadadssadas	2025-07-03	15:00:00	walk-in	2025-05-14 04:15:14.730373	pending
28	1	1	1	2025-05-30 09:00:00	sadajnfr	2025-05-30	09:00:00	walk-in	2025-05-14 04:17:44.360019	pending
29	1	1	4	2025-05-14 11:00:00	asdsdsadas	2025-05-14	11:00:00	service-on-call	2025-05-14 04:24:31.715527	pending
30	1	1	4	2025-05-14 11:00:00	asdsdsadas	2025-05-14	11:00:00	service-on-call	2025-05-14 04:24:35.88225	pending
31	1	1	7	2025-07-02 10:00:00	sdfbshfbsf	2025-07-02	10:00:00	walk-in	2025-05-14 04:31:59.96206	pending
32	1	7	1	2025-05-17 10:00:00	krrrrrr	2025-05-17	10:00:00	walk-in	2025-05-14 04:32:54.527587	pending
33	1	7	1	2025-05-14 16:00:00	sadwdawd	2025-05-14	16:00:00	walk-in	2025-05-14 04:55:08.507987	pending
21	1	1	8	2025-05-14 10:00:00		2025-05-14	10:00:00	walk-in	2025-05-14 04:01:58.063307	pending
34	1	8	1	2025-05-31 10:00:00	neoihqeiwqhoeh	2025-05-31	10:00:00	walk-in	2025-05-15 07:46:46.465807	pending
35	2	6	1	2025-05-29 09:00:00	sdjhjisdfdfsnk	2025-05-29	09:00:00	walk-in	2025-05-15 08:43:00.468756	CONFIRMED
\.


--
-- TOC entry 4957 (class 0 OID 17171)
-- Dependencies: 218
-- Data for Name: client; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.client (clt_code, clt_lname, clt_fname, clt_initial, clt_contact, clt_email_address, clt_home_address) FROM stdin;
1	kris	kris	\N	\N	kris@gmail.com	\N
2	recta	olive	\N	\N	olive@gmail.com	\N
4	dayanan	rafael	\N	\N	rafa@gmail.com	\N
5	Serinio	Stella	\N	\N	stella@gmail.com	\N
6	werd	awd	\N	\N	awd@gmail.com	\N
7	Bayon-on	Kraeyon	\N	\N	kristofer@gmail.com	\N
8	1234	1234	\N	\N	1@gmail.com	\N
9	413	143	\N	\N	tof@gmail.com	\N
10	1231231	KRISTOFER GERARD	\N	\N	bayononkristofergerard12@gmail.com	\N
11	Reds	Pert	R	09923412522	pert@gmail.com	Redawrd
13	Bayon-on	KRISTOFER GERARD	B	Red	tofer@gmail.com	165 saint jude street extn Hipodromo Cebu City
14	jffbw	iojd	k	kjdbejkfb	2@gmail.com	bfkebwk
\.


--
-- TOC entry 4961 (class 0 OID 17189)
-- Dependencies: 222
-- Data for Name: pet; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pet (pet_code, client_code, pet_name, pet_type, pet_breed, pet_age, pet_med_history) FROM stdin;
2	2	cutie pie	tatata	red cliff	3	all healthy
3	5	asd	asdas	dasdsda	23	wadadassdasdasda
4	13	radakwdklawndl	kldwankdnawndjkanjd	ndwjiandj	15	ejofnoanofnaffa
5	2	fafafafa	fafafafa	fafafaf	21	3213dadfaffas
1	1	Kreees	tatata	FertLArt	24	wpjsepfns
6	2	cutie pie	Dog	red cliff	3	wadadsaadsd
7	1	cutie pie	Cat	red cliff	5	sadkand
8	1	Eroro	Hamster/Guinea Pig	fdsafsadf	2	mfkmmmfelrmgergs,fsdfm;fs
\.


--
-- TOC entry 4967 (class 0 OID 17221)
-- Dependencies: 228
-- Data for Name: product; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product (prod_code, prod_name, prod_category, prod_stock, prod_price, supp_code, prod_image) FROM stdin;
1	Dog Shampoo	shampoo	25	10.99	\N	/assets/images/products/novapink.png
2	Isoflurane	anesthetics	56	56.99	\N	/assets/images/products/anesthetics.png
3	Premium Dog Food	food-accessories	23	178.00	1	/assets/images/products/premium dog food.png
\.


--
-- TOC entry 4977 (class 0 OID 17304)
-- Dependencies: 238
-- Data for Name: review; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.review (review_code, client_code, rating, comment, review_date) FROM stdin;
1	1	5	Great service!	2025-05-04 01:50:56.750376
2	1	1	mazui	2025-05-04 02:19:15.05611
3	1	4	so good service	2025-05-06 08:12:26.670226
4	1	5	mishlel	2025-05-06 08:14:22.236146
5	4	4	so good	2025-05-06 13:49:39.331258
6	4	1	AUDREY BATIG NAWNG	2025-05-06 13:51:04.993808
7	5	4	nindot siya	2025-05-08 12:44:30.540651
\.


--
-- TOC entry 4971 (class 0 OID 17260)
-- Dependencies: 232
-- Data for Name: sales_transaction; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sales_transaction (transaction_code, client_code, transaction_total_amount, transaction_pay_method, transaction_datetime) FROM stdin;
\.


--
-- TOC entry 4963 (class 0 OID 17203)
-- Dependencies: 224
-- Data for Name: service; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.service (service_code, service_name, service_desc, service_fee) FROM stdin;
1	vaccination	Vaccination services for pets	\N
2	deworming	Deworming treatments	\N
3	anti-parasitic	Anti-parasitic treatments	\N
4	surgery	Surgical procedures	\N
5	grooming	Pet grooming services	\N
6	treatment	General treatment	\N
7	confinement	Pet confinement services	\N
8	General Checkup	Basic pet checkup	200.00
\.


--
-- TOC entry 4979 (class 0 OID 17335)
-- Dependencies: 240
-- Data for Name: staff_schedule; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.staff_schedule (schedule_id, staff_code, day_of_week, start_time, end_time) FROM stdin;
19	2	Monday	09:00:00	18:00:00
20	2	Tuesday	09:00:00	18:00:00
21	2	Wednesday	09:00:00	18:00:00
22	2	Thursday	09:00:00	18:00:00
23	2	Friday	09:00:00	18:00:00
24	2	Saturday	09:00:00	18:00:00
25	1	Monday	09:00:00	17:00:00
26	1	Tuesday	09:00:00	17:00:00
27	1	Wednesday	09:00:00	17:00:00
28	1	Thursday	09:00:00	17:00:00
29	1	Friday	09:00:00	17:00:00
30	1	Saturday	09:00:00	17:00:00
\.


--
-- TOC entry 4965 (class 0 OID 17212)
-- Dependencies: 226
-- Data for Name: supplier; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.supplier (supp_code, supp_name, supp_contact_person, supp_contact_number, supp_email_address, supp_product_supplied) FROM stdin;
1	Dog food	Stella	09154653758	stella@gmail.com	Premium dog food
\.


--
-- TOC entry 4975 (class 0 OID 17295)
-- Dependencies: 236
-- Data for Name: sys_user; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sys_user (user_code, username, password, role, client_code) FROM stdin;
1	kris@gmail.com	$2y$12$wEj4CBoHbh6O9RkyFqkE3ON1WdIBTw3HmdV04ctwjjQ91wo8kLoHq	client	1
2	olive@gmail.com	$2y$12$gWfbMywlKlmmm8DN2tdlWuWQRl1GJcYr1XMnCweukK.ebzbp.5RGK	client	2
4	rafa@gmail.com	$2y$12$CYDebK38gTC4QffnhzSx4.bSgLY1UKR4ovY0evZWfSG8n8n6AoS1.	client	4
5	stella@gmail.com	$2y$12$JbeaXvDxAFhSnAMCMljAm.876Ve.v9cnO6bhYIahTt0JdVGHsQpGe	client	5
6	awd@gmail.com	$2y$12$FCfBScgpau7NHVOG3EZSmufRM/F92hljmQwgW.4qhZjZUTcf.O3dK	client	6
7	kristofer@gmail.com	$2y$12$i3tvUcYlr9kW1JKz0QVvTOna2eLA8be48TooVlTZcB.bfHmz52PHi	client	7
8	1@gmail.com	$2y$12$VXNFQMmyrLzWuVuWZnH.7.PRUXNE/WDC0/gT7VUZm1QoBfsXk8ytm	client	8
9	tof@gmail.com	$2y$12$KpxSfT02xSWPArEDpMiEHuZCQJdXkMLb9HCVPmqkP0UdN6Gb6zUH2	client	9
10	bayononkristofergerard12@gmail.com	$2y$12$9sI52..WcKsw.9j.C1RJ/.Dh/40FSpA1FpSRmF.Qv21wlEuL5QQ36	client	10
11	pert@gmail.com	$2y$12$2QSjNxhIWnCmaOQ9gpdFnOFoHAotAB2ZbwBGbe9goRnCFAcSxEege	client	11
13	tofer@gmail.com	$2y$12$qcXmXeBWfsGNUENjoppQUOw9tHTI8885J4EonzDAw8HmfohRJpyUe	client	13
14	2@gmail.com	$2y$12$y364EFJbexoljMmA8gPWs.ogr49gwBPJb.f2MaNesJralpiyC.iKa	client	14
\.


--
-- TOC entry 4973 (class 0 OID 17272)
-- Dependencies: 234
-- Data for Name: transaction_details; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.transaction_details (detail_code, transaction_code, prod_code, service_code, quantity, price) FROM stdin;
\.


--
-- TOC entry 4959 (class 0 OID 17180)
-- Dependencies: 220
-- Data for Name: veterinary_staff; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.veterinary_staff (staff_code, staff_name, staff_position, staff_contact, staff_email_address, staff_schedule) FROM stdin;
2	Ricardo Moe	Doctor	111-3214	ricardomoe@example.com	Mon-Thu: 9am-4pm
1	John Doe	Doctor	555-1234	johndoe@example.com	Mon-Fri: 9 am-6 pm
\.


--
-- TOC entry 4997 (class 0 OID 0)
-- Dependencies: 229
-- Name: appointment_appt_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.appointment_appt_code_seq', 35, true);


--
-- TOC entry 4998 (class 0 OID 0)
-- Dependencies: 217
-- Name: client_clt_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.client_clt_code_seq', 14, true);


--
-- TOC entry 4999 (class 0 OID 0)
-- Dependencies: 221
-- Name: pet_pet_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pet_pet_code_seq', 8, true);


--
-- TOC entry 5000 (class 0 OID 0)
-- Dependencies: 227
-- Name: product_prod_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_prod_code_seq', 3, true);


--
-- TOC entry 5001 (class 0 OID 0)
-- Dependencies: 237
-- Name: review_review_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.review_review_code_seq', 7, true);


--
-- TOC entry 5002 (class 0 OID 0)
-- Dependencies: 231
-- Name: sales_transaction_transaction_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sales_transaction_transaction_code_seq', 1, false);


--
-- TOC entry 5003 (class 0 OID 0)
-- Dependencies: 223
-- Name: service_service_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.service_service_code_seq', 8, true);


--
-- TOC entry 5004 (class 0 OID 0)
-- Dependencies: 239
-- Name: staff_schedule_schedule_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.staff_schedule_schedule_id_seq', 30, true);


--
-- TOC entry 5005 (class 0 OID 0)
-- Dependencies: 225
-- Name: supplier_supp_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.supplier_supp_code_seq', 1, true);


--
-- TOC entry 5006 (class 0 OID 0)
-- Dependencies: 235
-- Name: sys_user_user_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sys_user_user_code_seq', 14, true);


--
-- TOC entry 5007 (class 0 OID 0)
-- Dependencies: 233
-- Name: transaction_details_detail_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.transaction_details_detail_code_seq', 1, false);


--
-- TOC entry 5008 (class 0 OID 0)
-- Dependencies: 219
-- Name: veterinary_staff_staff_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.veterinary_staff_staff_code_seq', 3, true);


--
-- TOC entry 4780 (class 2606 OID 17238)
-- Name: appointment appointment_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appointment
    ADD CONSTRAINT appointment_pkey PRIMARY KEY (appt_code);


--
-- TOC entry 4768 (class 2606 OID 17178)
-- Name: client client_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.client
    ADD CONSTRAINT client_pkey PRIMARY KEY (clt_code);


--
-- TOC entry 4772 (class 2606 OID 17196)
-- Name: pet pet_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pet
    ADD CONSTRAINT pet_pkey PRIMARY KEY (pet_code);


--
-- TOC entry 4778 (class 2606 OID 17226)
-- Name: product product_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product
    ADD CONSTRAINT product_pkey PRIMARY KEY (prod_code);


--
-- TOC entry 4793 (class 2606 OID 17313)
-- Name: review review_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.review
    ADD CONSTRAINT review_pkey PRIMARY KEY (review_code);


--
-- TOC entry 4785 (class 2606 OID 17265)
-- Name: sales_transaction sales_transaction_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_transaction
    ADD CONSTRAINT sales_transaction_pkey PRIMARY KEY (transaction_code);


--
-- TOC entry 4774 (class 2606 OID 17210)
-- Name: service service_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.service
    ADD CONSTRAINT service_pkey PRIMARY KEY (service_code);


--
-- TOC entry 4795 (class 2606 OID 17340)
-- Name: staff_schedule staff_schedule_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.staff_schedule
    ADD CONSTRAINT staff_schedule_pkey PRIMARY KEY (schedule_id);


--
-- TOC entry 4776 (class 2606 OID 17219)
-- Name: supplier supplier_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.supplier
    ADD CONSTRAINT supplier_pkey PRIMARY KEY (supp_code);


--
-- TOC entry 4789 (class 2606 OID 17300)
-- Name: sys_user sys_user_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sys_user
    ADD CONSTRAINT sys_user_pkey PRIMARY KEY (user_code);


--
-- TOC entry 4791 (class 2606 OID 17302)
-- Name: sys_user sys_user_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sys_user
    ADD CONSTRAINT sys_user_username_key UNIQUE (username);


--
-- TOC entry 4787 (class 2606 OID 17278)
-- Name: transaction_details transaction_details_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaction_details
    ADD CONSTRAINT transaction_details_pkey PRIMARY KEY (detail_code);


--
-- TOC entry 4770 (class 2606 OID 17187)
-- Name: veterinary_staff veterinary_staff_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.veterinary_staff
    ADD CONSTRAINT veterinary_staff_pkey PRIMARY KEY (staff_code);


--
-- TOC entry 4781 (class 1259 OID 17351)
-- Name: idx_appointment_client; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_appointment_client ON public.appointment USING btree (client_code);


--
-- TOC entry 4782 (class 1259 OID 17353)
-- Name: idx_appointment_date_time; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_appointment_date_time ON public.appointment USING btree (preferred_date, preferred_time);


--
-- TOC entry 4783 (class 1259 OID 17352)
-- Name: idx_appointment_pet; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_appointment_pet ON public.appointment USING btree (pet_code);


--
-- TOC entry 4798 (class 2606 OID 17239)
-- Name: appointment appointment_client_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appointment
    ADD CONSTRAINT appointment_client_code_fkey FOREIGN KEY (client_code) REFERENCES public.client(clt_code) ON DELETE CASCADE;


--
-- TOC entry 4799 (class 2606 OID 17244)
-- Name: appointment appointment_pet_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appointment
    ADD CONSTRAINT appointment_pet_code_fkey FOREIGN KEY (pet_code) REFERENCES public.pet(pet_code) ON DELETE CASCADE;


--
-- TOC entry 4800 (class 2606 OID 17249)
-- Name: appointment appointment_service_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appointment
    ADD CONSTRAINT appointment_service_code_fkey FOREIGN KEY (service_code) REFERENCES public.service(service_code) ON DELETE SET NULL;


--
-- TOC entry 4801 (class 2606 OID 17354)
-- Name: appointment fk_client; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appointment
    ADD CONSTRAINT fk_client FOREIGN KEY (client_code) REFERENCES public.client(clt_code) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4802 (class 2606 OID 17359)
-- Name: appointment fk_pet; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appointment
    ADD CONSTRAINT fk_pet FOREIGN KEY (pet_code) REFERENCES public.pet(pet_code) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4803 (class 2606 OID 17364)
-- Name: appointment fk_service; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appointment
    ADD CONSTRAINT fk_service FOREIGN KEY (service_code) REFERENCES public.service(service_code) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 4796 (class 2606 OID 17197)
-- Name: pet pet_client_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pet
    ADD CONSTRAINT pet_client_code_fkey FOREIGN KEY (client_code) REFERENCES public.client(clt_code) ON DELETE CASCADE;


--
-- TOC entry 4797 (class 2606 OID 17227)
-- Name: product product_supp_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product
    ADD CONSTRAINT product_supp_code_fkey FOREIGN KEY (supp_code) REFERENCES public.supplier(supp_code) ON DELETE SET NULL;


--
-- TOC entry 4809 (class 2606 OID 17314)
-- Name: review review_client_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.review
    ADD CONSTRAINT review_client_code_fkey FOREIGN KEY (client_code) REFERENCES public.client(clt_code) ON DELETE CASCADE;


--
-- TOC entry 4804 (class 2606 OID 17266)
-- Name: sales_transaction sales_transaction_client_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_transaction
    ADD CONSTRAINT sales_transaction_client_code_fkey FOREIGN KEY (client_code) REFERENCES public.client(clt_code) ON DELETE SET NULL;


--
-- TOC entry 4810 (class 2606 OID 17341)
-- Name: staff_schedule staff_schedule_staff_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.staff_schedule
    ADD CONSTRAINT staff_schedule_staff_code_fkey FOREIGN KEY (staff_code) REFERENCES public.veterinary_staff(staff_code) ON DELETE CASCADE;


--
-- TOC entry 4808 (class 2606 OID 17319)
-- Name: sys_user sys_user_client_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sys_user
    ADD CONSTRAINT sys_user_client_code_fkey FOREIGN KEY (client_code) REFERENCES public.client(clt_code) ON DELETE CASCADE;


--
-- TOC entry 4805 (class 2606 OID 17284)
-- Name: transaction_details transaction_details_prod_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaction_details
    ADD CONSTRAINT transaction_details_prod_code_fkey FOREIGN KEY (prod_code) REFERENCES public.product(prod_code) ON DELETE SET NULL;


--
-- TOC entry 4806 (class 2606 OID 17289)
-- Name: transaction_details transaction_details_service_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaction_details
    ADD CONSTRAINT transaction_details_service_code_fkey FOREIGN KEY (service_code) REFERENCES public.service(service_code) ON DELETE SET NULL;


--
-- TOC entry 4807 (class 2606 OID 17279)
-- Name: transaction_details transaction_details_transaction_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaction_details
    ADD CONSTRAINT transaction_details_transaction_code_fkey FOREIGN KEY (transaction_code) REFERENCES public.sales_transaction(transaction_code) ON DELETE CASCADE;


-- Completed on 2025-05-21 11:31:42

--
-- PostgreSQL database dump complete
--

