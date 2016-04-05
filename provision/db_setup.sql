ALTER ROLE postgres PASSWORD 'postgres';

CREATE ROLE vaca_dev LOGIN PASSWORD 'vaca_dev';
CREATE ROLE vaca_test LOGIN CREATEDB SUPERUSER PASSWORD 'vaca_test';

CREATE DATABASE vaca_dev OWNER=vaca_dev;
