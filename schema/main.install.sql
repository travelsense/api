-- Database: main

-- Automatically change "updated" column
CREATE OR REPLACE FUNCTION process_updated_column()
  RETURNS TRIGGER AS $$
BEGIN
  NEW.updated = now();
  RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

-- Users

CREATE TABLE users
(
  id SERIAL NOT NULL PRIMARY KEY ,
  email TEXT NOT NULL UNIQUE,
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
  user_id SERIAL REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
  salt TEXT,
  device TEXT,
  created TIMESTAMP DEFAULT now(),
  updated TIMESTAMP
);

CREATE TRIGGER sessions_before_update BEFORE UPDATE
ON sessions FOR EACH ROW EXECUTE PROCEDURE
  process_updated_column();