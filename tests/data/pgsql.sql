/**
 * This is the database schema for testing PostgreSQL support of Gii module.
 * The database setup in config.php is required to perform then relevant tests:
 */

DROP TABLE IF EXISTS "schema1"."table1" CASCADE;
DROP TABLE IF EXISTS "schema1"."table2" CASCADE;
DROP TABLE IF EXISTS "schema2"."table1" CASCADE;
DROP TABLE IF EXISTS "schema2"."table2" CASCADE;
DROP TABLE IF EXISTS "schema1"."multi_pk" CASCADE;
DROP TABLE IF EXISTS "schema1"."junction1" CASCADE;
DROP TABLE IF EXISTS "schema1"."junction2" CASCADE;
DROP SCHEMA IF EXISTS "schema1" CASCADE;
DROP SCHEMA IF EXISTS "schema2" CASCADE;

CREATE SCHEMA "schema1";
CREATE SCHEMA "schema2";

CREATE TABLE "schema1"."table1" (
  id serial primary key,
  a varchar(255) not null,
  fk1 integer not null,
  fk2 integer not null,
  fk3 integer not null,
  fk4 integer not null,
  UNIQUE (fk1, fk2),
  UNIQUE (fk3, fk4)
);

CREATE TABLE "schema1"."table2" (
  id serial primary key,
  b varchar(255) not null,
  fk1 integer not null,
  fk2 integer not null,
  UNIQUE (fk1, fk2)
);

CREATE TABLE "schema1"."multi_pk" (
  id1 integer not null,
  id2 integer not null,
  PRIMARY KEY (id1, id2)
);

CREATE TABLE "schema1"."junction1" (
  multi_pk_id1 integer not null,
  multi_pk_id2 integer not null,
  table1_id integer not null,
  PRIMARY KEY (multi_pk_id1, multi_pk_id2, table1_id),
  CONSTRAINT j1_multi_pk_fkey FOREIGN KEY (multi_pk_id1, multi_pk_id2) REFERENCES "schema1"."multi_pk" (id1, id2),
  CONSTRAINT j1_table1_fkey FOREIGN KEY (table1_id) REFERENCES "schema1"."table1" (id)
);

CREATE TABLE "schema1"."junction2" (
  multi_pk_id1 integer not null,
  multi_pk_id2 integer not null,
  table1_id integer not null,
  data text,
  PRIMARY KEY (multi_pk_id1, multi_pk_id2, table1_id),
  CONSTRAINT j1_multi_pk_fkey FOREIGN KEY (multi_pk_id1, multi_pk_id2) REFERENCES "schema1"."multi_pk" (id1, id2),
  CONSTRAINT j1_table1_fkey FOREIGN KEY (table1_id) REFERENCES "schema1"."table1" (id)
);

CREATE TABLE "schema2"."table1" (
  id serial primary key,
  c varchar(255) not null,
  fk1 integer not null,
  fk2 integer not null,
  fk3 integer not null,
  fk4 integer not null,
  fk5 integer not null,
  fk6 integer not null,
  UNIQUE (fk5, fk6),
  CONSTRAINT t1_f12_fkey FOREIGN KEY (fk1, fk2) REFERENCES "schema1"."table1" (fk2, fk1),
  CONSTRAINT t1_f34_fkey FOREIGN KEY (fk3, fk4) REFERENCES "schema1"."table1" (fk4, fk3)
);

CREATE TABLE "schema2"."table2" (
  id serial primary key,
  d varchar(255) not null,
  fk1 integer not null,
  fk2 integer not null,
  fk5 integer not null,
  fk6 integer not null,
  UNIQUE (fk1, fk2),
  UNIQUE (fk5, fk6),
  CONSTRAINT t2_f12_fkey FOREIGN KEY (fk1, fk2) REFERENCES "schema1"."table1" (fk1, fk2),
  CONSTRAINT t2_f56_fkey FOREIGN KEY (fk5, fk6) REFERENCES "schema2"."table1" (fk5, fk6)
);
