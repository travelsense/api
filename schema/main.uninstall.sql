DROP TABLE IF EXISTS "travels" CASCADE;
DROP TABLE IF EXISTS "sessions" CASCADE;
DROP TABLE IF EXISTS "users" CASCADE;
DROP TABLE IF EXISTS "expirable_storage" CASCADE;

DROP FUNCTION IF EXISTS process_updated_column() CASCADE;