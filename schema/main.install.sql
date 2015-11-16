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


-- Trips


CREATE TABLE trips
(
  id SERIAL NOT NULL PRIMARY KEY ,
  user_id SERIAL REFERENCES users (id) ON UPDATE CASCADE ON DELETE SET NULL,
  name TEXT NOT NULL,
  image TEXT NOT NULL,
  description TEXT NOT NULL,
  path TEXT,
  country TEXT,
  created TIMESTAMP DEFAULT now(),
  updated TIMESTAMP
);

CREATE TABLE trip_stats
(
  id SERIAL NOT NULL PRIMARY KEY ,
  trip_id SERIAL REFERENCES trips (id) ON UPDATE CASCADE ON DELETE CASCADE ,
  rating INT DEFAULT 0,
  views INT DEFAULT 0
);

CREATE TRIGGER trips_before_update BEFORE UPDATE
ON trips FOR EACH ROW EXECUTE PROCEDURE
  process_updated_column();