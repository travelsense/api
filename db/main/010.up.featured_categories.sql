ALTER TABLE categories ADD COLUMN featured BOOLEAN NOT NULL DEFAULT FALSE;

UPDATE categories SET featured = true WHERE name = 'Featured';
UPDATE categories SET featured = true WHERE name = 'Romantic';
UPDATE categories SET featured = true WHERE name = 'Sports';
