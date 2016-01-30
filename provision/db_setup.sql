DROP FUNCTION IF EXISTS init(rolename NAME, pass TEXT);

CREATE FUNCTION init(rolename NAME, pass TEXT) RETURNS void AS

$$
BEGIN
IF NOT EXISTS(SELECT *
              FROM pg_roles
              WHERE rolname = rolename)
THEN
  EXECUTE format('CREATE ROLE %s', rolename);
END IF;
EXECUTE format('ALTER USER %s WITH PASSWORD ''%s''', rolename, pass);
EXECUTE format('ALTER USER %s WITH LOGIN', rolename);
END
$$
LANGUAGE plpgsql;

SELECT init('vaca_dev', 'vaca_dev');
SELECT init('vaca_test', 'vaca_test');

CREATE DATABASE vaca_dev OWNER=vaca_dev;
CREATE DATABASE vaca_test OWNER=vaca_test;