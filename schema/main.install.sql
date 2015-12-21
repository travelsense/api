-- Database: main

-- Automatically change "updated" column
CREATE OR REPLACE FUNCTION process_updated_column()
  RETURNS TRIGGER AS $$
BEGIN
  NEW.updated = now();
  RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';


-- Expirable Storage


CREATE TABLE expirable_storage
(
  id SERIAL NOT NULL PRIMARY KEY ,
  serialized_object TEXT NOT NULL,
  token TEXT NOT NULL ,
  expires TIMESTAMP,
  created TIMESTAMP DEFAULT now()
);


-- Users


CREATE TABLE users
(
  id SERIAL NOT NULL PRIMARY KEY ,
  email TEXT NOT NULL UNIQUE,
  email_confirmed BOOLEAN DEFAULT FALSE,
  password TEXT,
  first_name TEXT,
  last_name TEXT,
  picture TEXT,
  created TIMESTAMP DEFAULT now(),
  updated TIMESTAMP
);

CREATE TRIGGER users_before_update BEFORE UPDATE
ON users FOR EACH ROW EXECUTE PROCEDURE
  process_updated_column();


-- Sessions


CREATE TABLE sessions
(
  id SERIAL NOT NULL PRIMARY KEY ,
  user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
  token TEXT,
  device TEXT,
  created TIMESTAMP DEFAULT now(),
  updated TIMESTAMP
);

CREATE TRIGGER sessions_before_update BEFORE UPDATE
ON sessions FOR EACH ROW EXECUTE PROCEDURE
  process_updated_column();


-- Travels


CREATE TABLE travels
(
  id SERIAL NOT NULL PRIMARY KEY ,
  user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE ON DELETE SET NULL,
  title TEXT NOT NULL,
  description TEXT NOT NULL,
  created TIMESTAMP DEFAULT now(),
  updated TIMESTAMP
);

CREATE TRIGGER travels_before_update BEFORE UPDATE
ON travels FOR EACH ROW EXECUTE PROCEDURE
  process_updated_column();