CREATE TABLE "users"
(
  id SERIAL NOT NULL PRIMARY KEY ,
  email TEXT NOT NULL ,
  password TEXT,
  created TIMESTAMP,
  updated TIMESTAMP
);