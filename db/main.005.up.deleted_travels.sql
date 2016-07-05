--Add a new boolean 'deleted' field to 'travels' table with default values 'false'
ALTER TABLE travels ADD COLUMN deleted BOOLEAN NOT NULL DEFAULT FALSE;
