<?php

namespace Api\DB\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Legacy migrations
 */
class Version20160101000000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql[] = <<<SQL
CREATE OR REPLACE FUNCTION process_updated_column()
  RETURNS TRIGGER AS $$
BEGIN
  NEW.updated = now();
  RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';
SQL;

        $sql[] = <<<SQL
CREATE TABLE expirable_storage
(
  id SERIAL NOT NULL PRIMARY KEY ,
  serialized_object TEXT NOT NULL,
  token TEXT NOT NULL,
  expires TIMESTAMP,
  created TIMESTAMP DEFAULT now()
);
SQL;

        $sql[] = <<<SQL
CREATE TABLE users
(
  id SERIAL NOT NULL PRIMARY KEY ,
  email TEXT NOT NULL UNIQUE,
  email_confirmed BOOLEAN DEFAULT FALSE,
  password TEXT,
  first_name TEXT,
  last_name TEXT,
  picture TEXT,
  creator BOOLEAN NOT NULL DEFAULT FALSE,
  created TIMESTAMP DEFAULT now(),
  updated TIMESTAMP
);
SQL;

        $sql[] = <<<SQL
CREATE TRIGGER users_before_update BEFORE UPDATE
ON users FOR EACH ROW EXECUTE PROCEDURE
  process_updated_column();
SQL;

        $sql[] = <<<SQL
CREATE TABLE sessions
(
  id SERIAL NOT NULL PRIMARY KEY ,
  user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
  token TEXT,
  device TEXT,
  created TIMESTAMP DEFAULT now(),
  updated TIMESTAMP
);
SQL;

        $sql[] = <<<SQL
CREATE TRIGGER sessions_before_update BEFORE UPDATE
ON sessions FOR EACH ROW EXECUTE PROCEDURE
  process_updated_column();
SQL;

        $sql[] = <<<SQL
CREATE TABLE travels
(
  id SERIAL NOT NULL PRIMARY KEY ,
  author_id INTEGER REFERENCES users (id) ON UPDATE CASCADE ON DELETE SET NULL,
  title TEXT NOT NULL,
  description TEXT NOT NULL,
  image TEXT,
  content JSON NOT NULL DEFAULT '[]' :: JSON,
  is_published BOOLEAN DEFAULT FALSE,
  deleted BOOLEAN NOT NULL DEFAULT FALSE,
  creation_mode TEXT,
  transportation INTEGER,
  estimated_price INTEGER,
  created TIMESTAMP DEFAULT now(),
  updated TIMESTAMP
);
SQL;

        $sql[] = <<<SQL
CREATE TRIGGER travels_before_update BEFORE UPDATE
ON travels FOR EACH ROW EXECUTE PROCEDURE
  process_updated_column();
SQL;

        $sql[] = <<<SQL
CREATE TABLE travel_comments
(
  id SERIAL NOT NULL PRIMARY KEY ,
  author_id INTEGER REFERENCES users (id) ON UPDATE CASCADE ON DELETE SET NULL,
  travel_id INTEGER REFERENCES travels (id) ON UPDATE CASCADE ON DELETE CASCADE,
  text TEXT NOT NULL,
  created TIMESTAMP DEFAULT now(),
  updated TIMESTAMP
);
SQL;

        $sql[] = <<<SQL
CREATE TRIGGER travel_comments_before_update BEFORE UPDATE
ON travel_comments FOR EACH ROW EXECUTE PROCEDURE
  process_updated_column();
SQL;

        $sql[] = <<<SQL
CREATE TABLE favorite_travels
(
  user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
  travel_id INTEGER REFERENCES travels (id) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT favorite_travels_pkey PRIMARY KEY (user_id, travel_id)
);
SQL;

        $sql[] = <<<SQL
CREATE TABLE categories
(
  id SERIAL NOT NULL PRIMARY KEY ,
  name TEXT NOT NULL,
  featured BOOLEAN NOT NULL DEFAULT FALSE,
  sort_order INT DEFAULT 0
);
SQL;

        $sql[] = <<<SQL
UPDATE categories SET featured = true WHERE name IN ('Featured', 'Romantic', 'Sports');
SQL;
        $sql[] = <<<SQL
CREATE TABLE travel_categories
(
  travel_id INTEGER REFERENCES travels (id) ON UPDATE CASCADE ON DELETE CASCADE,
  category_id INTEGER REFERENCES categories (id) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT travel_categories_pkey PRIMARY KEY (travel_id, category_id)
);
SQL;

        $sql[] = <<<SQL
CREATE TABLE hotels
(
  id SERIAL NOT NULL PRIMARY KEY,
  name TEXT NOT NULL,
  location text NOT NULL,
  address TEXT NOT NULL,
  lat FLOAT4 NOT NULL,
  lon FLOAT4 NOT NULL,
  description TEXT ,
  stars INTEGER,
  created TIMESTAMP DEFAULT now(),
  updated TIMESTAMP
);
SQL;

        $sql[] = <<<SQL
CREATE TRIGGER hotels_before_update BEFORE UPDATE
ON hotels FOR EACH ROW EXECUTE PROCEDURE
  process_updated_column();
SQL;

        $sql[] = <<<SQL
CREATE TABLE wego_hotels
(
  hotel_id INTEGER REFERENCES hotels (id) ON UPDATE CASCADE ON DELETE CASCADE,
  wego_hotel_id INTEGER,
  CONSTRAINT self_wego_hotel_pkey PRIMARY KEY (hotel_id, wego_hotel_id)
);
SQL;

        $sql[] = <<<SQL
CREATE TABLE flagged_comments
(
  comment_id INTEGER REFERENCES travel_comments (id) ON UPDATE CASCADE ON DELETE CASCADE,
  user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
  created TIMESTAMP DEFAULT now(),
  CONSTRAINT flagged_comments_pkey PRIMARY KEY (comment_id, user_id)
);
SQL;

        $sql[] = <<<SQL
CREATE TABLE bookings
(
  travel_id INTEGER REFERENCES travels (id) ON UPDATE CASCADE ON DELETE CASCADE,
  user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
  created TIMESTAMP DEFAULT now(),
  CONSTRAINT bookings_pkey PRIMARY KEY (travel_id, user_id)
);
SQL;
        $sql[] = <<<SQL
CREATE INDEX bookings_created_idx ON bookings (created);
SQL;
        $sql[] = <<<SQL
CREATE TABLE actions
(
  id SERIAL NOT NULL PRIMARY KEY ,
  travel_id INTEGER REFERENCES travels (id) ON UPDATE CASCADE ON DELETE SET NULL,
  offset_start INTEGER DEFAULT 0,
  offset_end INTEGER DEFAULT 0,
  car BOOLEAN DEFAULT FALSE,
  airports JSON,
  hotels JSON,
  sightseeings JSON,
  type TEXT
);
SQL;
        $sql[] = <<<SQL
CREATE TABLE user_roles (
  user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
  role    TEXT NOT NULL,
  created TIMESTAMP DEFAULT now(),
  CONSTRAINT user_rights_pkey PRIMARY KEY (user_id, role)
);
SQL;
        $sql[] = <<<SQL
CREATE TABLE banners (
  id SERIAL NOT NULL PRIMARY KEY ,
  title TEXT NOT NULL,
  subtitle TEXT NOT NULL,
  image TEXT NOT NULL,
  category TEXT NOT NULL
);
SQL;

        foreach ($sql as $query) {
            $this->addSql($query);
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->throwIrreversibleMigrationException('Just drop the database instead');
    }
}
