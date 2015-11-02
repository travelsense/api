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
  created TIMESTAMP,
  updated TIMESTAMP
);

CREATE TRIGGER users_before_update BEFORE UPDATE
ON users FOR EACH ROW EXECUTE PROCEDURE
  process_updated_column();