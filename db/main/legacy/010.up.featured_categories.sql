ALTER TABLE categories ADD COLUMN featured BOOLEAN NOT NULL DEFAULT FALSE;

UPDATE categories SET featured = true WHERE name IN ('Featured', 'Romantic', 'Sports');
