--
-- PostgreSQL database dump
--

-- Dumped from database version 17.4
-- Dumped by pg_dump version 17.4

-- Started on 2025-06-09 08:38:55

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
-- TOC entry 228 (class 1259 OID 17233)
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
-- TOC entry 227 (class 1259 OID 17232)
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
-- TOC entry 4981 (class 0 OID 0)
-- Dependencies: 227
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
-- TOC entry 4982 (class 0 OID 0)
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
-- TOC entry 4983 (class 0 OID 0)
-- Dependencies: 221
-- Name: pet_pet_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pet_pet_code_seq OWNED BY public.pet.pet_code;


--
-- TOC entry 226 (class 1259 OID 17221)
-- Name: product; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product (
    prod_code integer NOT NULL,
    prod_name character varying(100),
    prod_category character varying(50),
    prod_stock integer,
    prod_price numeric(10,2),
    supp_code integer,
    prod_image character varying(255),
    prod_status character varying(20) DEFAULT 'ACTIVE'::character varying,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    prod_details text DEFAULT 'No details provided.'::text
);


ALTER TABLE public.product OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 17220)
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
-- TOC entry 4984 (class 0 OID 0)
-- Dependencies: 225
-- Name: product_prod_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.product_prod_code_seq OWNED BY public.product.prod_code;


--
-- TOC entry 232 (class 1259 OID 17304)
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
-- TOC entry 231 (class 1259 OID 17303)
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
-- TOC entry 4985 (class 0 OID 0)
-- Dependencies: 231
-- Name: review_review_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.review_review_code_seq OWNED BY public.review.review_code;


--
-- TOC entry 236 (class 1259 OID 17556)
-- Name: sales; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sales (
    sale_id integer NOT NULL,
    sale_date timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    total_amount numeric(10,2) NOT NULL
);


ALTER TABLE public.sales OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 17564)
-- Name: sales_details; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sales_details (
    sales_detail_id integer NOT NULL,
    sale_id integer,
    prod_code integer,
    quantity integer NOT NULL,
    price numeric(10,2) NOT NULL,
    subtotal numeric(10,2) GENERATED ALWAYS AS (((quantity)::numeric * price)) STORED
);


ALTER TABLE public.sales_details OWNER TO postgres;

--
-- TOC entry 237 (class 1259 OID 17563)
-- Name: sales_details_sales_detail_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sales_details_sales_detail_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sales_details_sales_detail_id_seq OWNER TO postgres;

--
-- TOC entry 4986 (class 0 OID 0)
-- Dependencies: 237
-- Name: sales_details_sales_detail_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sales_details_sales_detail_id_seq OWNED BY public.sales_details.sales_detail_id;


--
-- TOC entry 235 (class 1259 OID 17555)
-- Name: sales_sale_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sales_sale_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sales_sale_id_seq OWNER TO postgres;

--
-- TOC entry 4987 (class 0 OID 0)
-- Dependencies: 235
-- Name: sales_sale_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sales_sale_id_seq OWNED BY public.sales.sale_id;


--
-- TOC entry 224 (class 1259 OID 17203)
-- Name: service; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.service (
    service_code integer NOT NULL,
    service_name character varying(100),
    service_desc text,
    service_fee numeric(10,2),
    service_img character varying(255) DEFAULT '/assets/images/services/default.png'::character varying,
    status character varying(20) DEFAULT 'ACTIVE'::character varying,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
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
-- TOC entry 4988 (class 0 OID 0)
-- Dependencies: 223
-- Name: service_service_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.service_service_code_seq OWNED BY public.service.service_code;


--
-- TOC entry 234 (class 1259 OID 17335)
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
-- TOC entry 233 (class 1259 OID 17334)
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
-- TOC entry 4989 (class 0 OID 0)
-- Dependencies: 233
-- Name: staff_schedule_schedule_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.staff_schedule_schedule_id_seq OWNED BY public.staff_schedule.schedule_id;


--
-- TOC entry 230 (class 1259 OID 17295)
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
-- TOC entry 229 (class 1259 OID 17294)
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
-- TOC entry 4990 (class 0 OID 0)
-- Dependencies: 229
-- Name: sys_user_user_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sys_user_user_code_seq OWNED BY public.sys_user.user_code;


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
    staff_schedule text,
    status character varying(20) DEFAULT 'ACTIVE'::character varying,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
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
-- TOC entry 4991 (class 0 OID 0)
-- Dependencies: 219
-- Name: veterinary_staff_staff_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.veterinary_staff_staff_code_seq OWNED BY public.veterinary_staff.staff_code;


--
-- TOC entry 4758 (class 2604 OID 17236)
-- Name: appointment appt_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appointment ALTER COLUMN appt_code SET DEFAULT nextval('public.appointment_appt_code_seq'::regclass);


--
-- TOC entry 4745 (class 2604 OID 17174)
-- Name: client clt_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.client ALTER COLUMN clt_code SET DEFAULT nextval('public.client_clt_code_seq'::regclass);


--
-- TOC entry 4749 (class 2604 OID 17192)
-- Name: pet pet_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pet ALTER COLUMN pet_code SET DEFAULT nextval('public.pet_pet_code_seq'::regclass);


--
-- TOC entry 4754 (class 2604 OID 17224)
-- Name: product prod_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product ALTER COLUMN prod_code SET DEFAULT nextval('public.product_prod_code_seq'::regclass);


--
-- TOC entry 4762 (class 2604 OID 17307)
-- Name: review review_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.review ALTER COLUMN review_code SET DEFAULT nextval('public.review_review_code_seq'::regclass);


--
-- TOC entry 4765 (class 2604 OID 17559)
-- Name: sales sale_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales ALTER COLUMN sale_id SET DEFAULT nextval('public.sales_sale_id_seq'::regclass);


--
-- TOC entry 4767 (class 2604 OID 17567)
-- Name: sales_details sales_detail_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_details ALTER COLUMN sales_detail_id SET DEFAULT nextval('public.sales_details_sales_detail_id_seq'::regclass);


--
-- TOC entry 4750 (class 2604 OID 17206)
-- Name: service service_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.service ALTER COLUMN service_code SET DEFAULT nextval('public.service_service_code_seq'::regclass);


--
-- TOC entry 4764 (class 2604 OID 17338)
-- Name: staff_schedule schedule_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.staff_schedule ALTER COLUMN schedule_id SET DEFAULT nextval('public.staff_schedule_schedule_id_seq'::regclass);


--
-- TOC entry 4761 (class 2604 OID 17298)
-- Name: sys_user user_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sys_user ALTER COLUMN user_code SET DEFAULT nextval('public.sys_user_user_code_seq'::regclass);


--
-- TOC entry 4746 (class 2604 OID 17183)
-- Name: veterinary_staff staff_code; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.veterinary_staff ALTER COLUMN staff_code SET DEFAULT nextval('public.veterinary_staff_staff_code_seq'::regclass);


--
-- TOC entry 4965 (class 0 OID 17233)
-- Dependencies: 228
-- Data for Name: appointment; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.appointment (appt_code, client_code, pet_code, service_code, appt_datetime, additional_notes, preferred_date, preferred_time, appointment_type, created_at, status) FROM stdin;
37	18	10	7	2025-06-11 10:00:00		2025-06-11	10:00:00	walk-in	2025-06-08 22:00:43.63643	pending
38	17	11	4	2025-06-27 09:00:00		2025-06-27	09:00:00	walk-in	2025-06-08 22:02:49.490445	pending
39	16	12	2	2025-06-28 09:00:00		2025-06-28	09:00:00	walk-in	2025-06-08 22:04:33.385992	pending
\.


--
-- TOC entry 4955 (class 0 OID 17171)
-- Dependencies: 218
-- Data for Name: client; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.client (clt_code, clt_lname, clt_fname, clt_initial, clt_contact, clt_email_address, clt_home_address) FROM stdin;
16	Bayon-on	Kristofer Gerard	B	09924910640	bayononkristofergerard12@gmail.com	165 saint jude street extn Hipodromo Cebu City
17	Recta	Althea Olive	A	09131423123	olive@gmail.com	CEBU CITY
18	Serinio	Stella Marie	A	091234567890	stella@gmail.com	TOLEDO CITY
\.


--
-- TOC entry 4959 (class 0 OID 17189)
-- Dependencies: 222
-- Data for Name: pet; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pet (pet_code, client_code, pet_name, pet_type, pet_breed, pet_age, pet_med_history) FROM stdin;
10	18	Inu	Dog	Bulldog	2	No Medical History
11	17	Hammy	Cat	Ragdoll	1	No Medical History
12	16	Onyx	Dog	Poodle	3	No Medical History
\.


--
-- TOC entry 4963 (class 0 OID 17221)
-- Dependencies: 226
-- Data for Name: product; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product (prod_code, prod_name, prod_category, prod_stock, prod_price, supp_code, prod_image, prod_status, updated_at, prod_details) FROM stdin;
2	Isoflurane	anesthetics	55	56.99	\N	/assets/images/products/anesthetics.png	ACTIVE	2025-06-04 14:18:31.778145	\N
3	Premium Dog Food	food-accessories	20	178.00	1	/assets/images/products/premium dog food.png	ACTIVE	2025-06-04 14:18:31.778145	\N
1	Dog Shampoo	shampoo	249	10.99	\N	/assets/images/products/novapink.png	ACTIVE	2025-06-04 16:30:11.314864	No details provided.
\.


--
-- TOC entry 4969 (class 0 OID 17304)
-- Dependencies: 232
-- Data for Name: review; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.review (review_code, client_code, rating, comment, review_date) FROM stdin;
8	16	5	I think the place is good!	2025-06-08 21:41:01.352805
9	16	5	Staffs are amazing!	2025-06-08 21:41:27.71397
10	18	4	They take care of the pets so well	2025-06-08 21:59:50.951539
11	17	5	Amazing personnel	2025-06-08 22:01:35.190667
\.


--
-- TOC entry 4973 (class 0 OID 17556)
-- Dependencies: 236
-- Data for Name: sales; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sales (sale_id, sale_date, total_amount) FROM stdin;
1	2025-06-04 18:24:39.768088	10.99
2	2025-06-04 19:12:28.059709	601.98
\.


--
-- TOC entry 4975 (class 0 OID 17564)
-- Dependencies: 238
-- Data for Name: sales_details; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sales_details (sales_detail_id, sale_id, prod_code, quantity, price) FROM stdin;
1	1	1	1	10.99
2	2	1	1	10.99
3	2	2	1	56.99
4	2	3	3	178.00
\.


--
-- TOC entry 4961 (class 0 OID 17203)
-- Dependencies: 224
-- Data for Name: service; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.service (service_code, service_name, service_desc, service_fee, service_img, status, updated_at) FROM stdin;
8	General Checkup	Basic pet checkup	200.00	/assets/images/services/service_683ffc08a2ce1_home_vaccinations.png	ACTIVE	2025-06-04 16:22:22.581914
3	anti-parasitic	Anti-parasitic treatments	150.00	/assets/images/services/service_683ffbdf2d026_anti parasitic program.png	ACTIVE	2025-06-04 16:30:35.845257
7	confinement	Pet confinement services	210.00	/assets/images/services/service_683ffbe9690a4_confinement.png	ACTIVE	2025-06-04 16:22:22.581914
2	deworming	Deworming treatments	310.00	/assets/images/services/service_683ffbf6a31ab_deworming.png	ACTIVE	2025-06-04 16:22:22.581914
1	vaccination	Vaccination services for pets	400.00	/assets/images/services/service_683ffc38f0ac2_vaccination.png	ACTIVE	2025-06-04 16:22:22.581914
6	treatment	General treatment	200.00	/assets/images/services/service_683ffc1c30f19_treatment.png	ACTIVE	2025-06-04 16:22:22.581914
4	surgery	Surgical procedures	1000.00	/assets/images/services/service_683ffc2502307_surgery.png	ACTIVE	2025-06-04 16:22:22.581914
5	grooming	Pet grooming services	450.00	/assets/images/services/service_683ffc2d7a824_grooming.png	ACTIVE	2025-06-04 16:22:22.581914
\.


--
-- TOC entry 4971 (class 0 OID 17335)
-- Dependencies: 234
-- Data for Name: staff_schedule; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.staff_schedule (schedule_id, staff_code, day_of_week, start_time, end_time) FROM stdin;
43	1	Monday	09:00:00	17:00:00
44	1	Tuesday	09:00:00	17:00:00
45	1	Wednesday	09:00:00	17:00:00
46	1	Thursday	09:00:00	17:00:00
47	1	Friday	09:00:00	17:00:00
48	1	Saturday	09:00:00	17:00:00
49	2	Monday	09:00:00	17:00:00
50	2	Tuesday	09:00:00	18:00:00
51	2	Wednesday	09:00:00	18:00:00
52	2	Thursday	09:00:00	18:00:00
53	2	Friday	09:00:00	18:00:00
54	2	Saturday	09:00:00	18:00:00
55	4	Monday	09:00:00	17:00:00
56	4	Tuesday	09:00:00	17:00:00
57	4	Wednesday	09:00:00	17:00:00
58	4	Thursday	09:00:00	17:00:00
59	4	Friday	09:00:00	17:00:00
\.


--
-- TOC entry 4967 (class 0 OID 17295)
-- Dependencies: 230
-- Data for Name: sys_user; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sys_user (user_code, username, password, role, client_code) FROM stdin;
16	bayononkristofergerard12@gmail.com	$2y$12$uAZwoWyVzqfwpl7XJzqxHOVk.iQh8/NUX/z1kgvjv0m1tXimishm.	client	16
17	olive@gmail.com	$2y$12$39oUUqm4TgOIo.rGx6r1RO9c82OXQ0DM6ovgV.nBa.Ew1RiN1j7QG	client	17
18	stella@gmail.com	$2y$12$ZprRDjmYL/sTULEF431Eh.ja6K6WQNIqfzhUDdyusYsrwFZXmfWES	client	18
\.


--
-- TOC entry 4957 (class 0 OID 17180)
-- Dependencies: 220
-- Data for Name: veterinary_staff; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.veterinary_staff (staff_code, staff_name, staff_position, staff_contact, staff_email_address, staff_schedule, status, updated_at) FROM stdin;
1	Delia Montanez	Doctor	09567346746	deliamontanez@gmail.com	Mon-Fri: 9 am-6 pm	ACTIVE	2025-06-04 16:29:57.373218
2	Joanah Ferrer	Doctor	09369534784	joanahferrer@gmail.com	Mon-Thu: 9am-4pm	ACTIVE	2025-06-04 16:29:40.374402
4	Jessie Barrola	Doctor	09951042113	jessiebarrola@gmail.com	\N	ACTIVE	2025-06-08 20:40:11.58807
\.


--
-- TOC entry 4992 (class 0 OID 0)
-- Dependencies: 227
-- Name: appointment_appt_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.appointment_appt_code_seq', 39, true);


--
-- TOC entry 4993 (class 0 OID 0)
-- Dependencies: 217
-- Name: client_clt_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.client_clt_code_seq', 18, true);


--
-- TOC entry 4994 (class 0 OID 0)
-- Dependencies: 221
-- Name: pet_pet_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pet_pet_code_seq', 12, true);


--
-- TOC entry 4995 (class 0 OID 0)
-- Dependencies: 225
-- Name: product_prod_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_prod_code_seq', 5, true);


--
-- TOC entry 4996 (class 0 OID 0)
-- Dependencies: 231
-- Name: review_review_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.review_review_code_seq', 11, true);


--
-- TOC entry 4997 (class 0 OID 0)
-- Dependencies: 237
-- Name: sales_details_sales_detail_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sales_details_sales_detail_id_seq', 4, true);


--
-- TOC entry 4998 (class 0 OID 0)
-- Dependencies: 235
-- Name: sales_sale_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sales_sale_id_seq', 2, true);


--
-- TOC entry 4999 (class 0 OID 0)
-- Dependencies: 223
-- Name: service_service_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.service_service_code_seq', 9, true);


--
-- TOC entry 5000 (class 0 OID 0)
-- Dependencies: 233
-- Name: staff_schedule_schedule_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.staff_schedule_schedule_id_seq', 59, true);


--
-- TOC entry 5001 (class 0 OID 0)
-- Dependencies: 229
-- Name: sys_user_user_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sys_user_user_code_seq', 18, true);


--
-- TOC entry 5002 (class 0 OID 0)
-- Dependencies: 219
-- Name: veterinary_staff_staff_code_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.veterinary_staff_staff_code_seq', 4, true);


--
-- TOC entry 4781 (class 2606 OID 17238)
-- Name: appointment appointment_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appointment
    ADD CONSTRAINT appointment_pkey PRIMARY KEY (appt_code);


--
-- TOC entry 4771 (class 2606 OID 17178)
-- Name: client client_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.client
    ADD CONSTRAINT client_pkey PRIMARY KEY (clt_code);


--
-- TOC entry 4775 (class 2606 OID 17196)
-- Name: pet pet_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pet
    ADD CONSTRAINT pet_pkey PRIMARY KEY (pet_code);


--
-- TOC entry 4779 (class 2606 OID 17226)
-- Name: product product_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product
    ADD CONSTRAINT product_pkey PRIMARY KEY (prod_code);


--
-- TOC entry 4790 (class 2606 OID 17313)
-- Name: review review_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.review
    ADD CONSTRAINT review_pkey PRIMARY KEY (review_code);


--
-- TOC entry 4796 (class 2606 OID 17570)
-- Name: sales_details sales_details_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_details
    ADD CONSTRAINT sales_details_pkey PRIMARY KEY (sales_detail_id);


--
-- TOC entry 4794 (class 2606 OID 17562)
-- Name: sales sales_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales
    ADD CONSTRAINT sales_pkey PRIMARY KEY (sale_id);


--
-- TOC entry 4777 (class 2606 OID 17210)
-- Name: service service_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.service
    ADD CONSTRAINT service_pkey PRIMARY KEY (service_code);


--
-- TOC entry 4792 (class 2606 OID 17340)
-- Name: staff_schedule staff_schedule_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.staff_schedule
    ADD CONSTRAINT staff_schedule_pkey PRIMARY KEY (schedule_id);


--
-- TOC entry 4786 (class 2606 OID 17300)
-- Name: sys_user sys_user_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sys_user
    ADD CONSTRAINT sys_user_pkey PRIMARY KEY (user_code);


--
-- TOC entry 4788 (class 2606 OID 17302)
-- Name: sys_user sys_user_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sys_user
    ADD CONSTRAINT sys_user_username_key UNIQUE (username);


--
-- TOC entry 4773 (class 2606 OID 17187)
-- Name: veterinary_staff veterinary_staff_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.veterinary_staff
    ADD CONSTRAINT veterinary_staff_pkey PRIMARY KEY (staff_code);


--
-- TOC entry 4782 (class 1259 OID 17351)
-- Name: idx_appointment_client; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_appointment_client ON public.appointment USING btree (client_code);


--
-- TOC entry 4783 (class 1259 OID 17353)
-- Name: idx_appointment_date_time; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_appointment_date_time ON public.appointment USING btree (preferred_date, preferred_time);


--
-- TOC entry 4784 (class 1259 OID 17352)
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
-- TOC entry 4797 (class 2606 OID 17197)
-- Name: pet pet_client_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pet
    ADD CONSTRAINT pet_client_code_fkey FOREIGN KEY (client_code) REFERENCES public.client(clt_code) ON DELETE CASCADE;


--
-- TOC entry 4805 (class 2606 OID 17314)
-- Name: review review_client_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.review
    ADD CONSTRAINT review_client_code_fkey FOREIGN KEY (client_code) REFERENCES public.client(clt_code) ON DELETE CASCADE;


--
-- TOC entry 4807 (class 2606 OID 17576)
-- Name: sales_details sales_details_prod_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_details
    ADD CONSTRAINT sales_details_prod_code_fkey FOREIGN KEY (prod_code) REFERENCES public.product(prod_code);


--
-- TOC entry 4808 (class 2606 OID 17571)
-- Name: sales_details sales_details_sale_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales_details
    ADD CONSTRAINT sales_details_sale_id_fkey FOREIGN KEY (sale_id) REFERENCES public.sales(sale_id) ON DELETE CASCADE;


--
-- TOC entry 4806 (class 2606 OID 17341)
-- Name: staff_schedule staff_schedule_staff_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.staff_schedule
    ADD CONSTRAINT staff_schedule_staff_code_fkey FOREIGN KEY (staff_code) REFERENCES public.veterinary_staff(staff_code) ON DELETE CASCADE;


--
-- TOC entry 4804 (class 2606 OID 17319)
-- Name: sys_user sys_user_client_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sys_user
    ADD CONSTRAINT sys_user_client_code_fkey FOREIGN KEY (client_code) REFERENCES public.client(clt_code) ON DELETE CASCADE;


-- Completed on 2025-06-09 08:38:56

--
-- PostgreSQL database dump complete
--

