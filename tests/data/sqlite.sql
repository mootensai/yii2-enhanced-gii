/**
 * This is the database schema for testing Sqlite support of Gii module.
 * The database setup in config.php is required to perform then relevant tests:
 */

DROP TABLE IF EXISTS "product_language";
DROP TABLE IF EXISTS "product";
DROP TABLE IF EXISTS "supplier";
DROP TABLE IF EXISTS "category_photo";
DROP TABLE IF EXISTS "category";
DROP TABLE IF EXISTS "customer";
DROP TABLE IF EXISTS "profile";

CREATE TABLE "profile" (
  id INTEGER NOT NULL,
  description varchar(128) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE "customer" (
  id INTEGER NOT NULL,
  email varchar(128) NOT NULL,
  name varchar(128),
  address text,
  status INTEGER DEFAULT 0,
  profile_id INTEGER,
  PRIMARY KEY (id)
);

CREATE TABLE "category" (
  id INTEGER NOT NULL,
  language_code varchar(3) NOT NULL,
  name varchar(128) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE (id, language_code)
);

CREATE TABLE "category_photo" (
  id INTEGER NOT NULL,
  category_id INTEGER NOT NULL REFERENCES "category" (id) ON DELETE CASCADE,
  display_number INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE (category_id, display_number)
);

CREATE TABLE "supplier" (
  id INTEGER NOT NULL,
  name varchar(128) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE "attribute" (
  id INTEGER NOT NULL,
  supplier_id INTEGER NOT NULL REFERENCES "supplier" (id) ON DELETE CASCADE,
  name varchar(128) NOT NULL,
  value varchar(128) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE "product" (
  id INTEGER NOT NULL,
  supplier_id INTEGER NOT NULL REFERENCES "supplier" (id) ON DELETE CASCADE,
  category_language_code varchar(3) NOT NULL,
  category_id INTEGER NOT NULL,
  internal_name varchar(128),
  PRIMARY KEY (id, supplier_id),
  UNIQUE (category_id, category_language_code),
  CONSTRAINT product_category_id_category_language_code_fkey FOREIGN KEY (category_id, category_language_code) REFERENCES "category" (id, language_code) ON DELETE CASCADE
);

CREATE TABLE "product_language" (
  id INTEGER NOT NULL,
  supplier_id INTEGER NOT NULL REFERENCES "supplier" (id) ON DELETE CASCADE,
  language_code varchar(3),
  name varchar(128),
  PRIMARY KEY (id, supplier_id),
  UNIQUE (id, supplier_id, language_code),
  UNIQUE (supplier_id),
  CONSTRAINT product_language_id_supplier_id_fkey FOREIGN KEY (supplier_id, id) REFERENCES "product" (supplier_id, id) ON DELETE CASCADE
);

INSERT INTO "profile" (description) VALUES ('profile customer 1');
INSERT INTO "profile" (description) VALUES ('profile customer 3');

INSERT INTO "customer" (email, name, address, status, profile_id) VALUES ('user1@example.com', 'user1', 'address1', 1, 1);
INSERT INTO "customer" (email, name, address, status) VALUES ('user2@example.com', 'user2', 'address2', 1);
INSERT INTO "customer" (email, name, address, status, profile_id) VALUES ('user3@example.com', 'user3', 'address3', 2, 2);
