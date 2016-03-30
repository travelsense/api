-- Favorite travels

CREATE TABLE IF NOT EXISTS favorite_travels
(
  user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
  travel_id INTEGER REFERENCES travels (id) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT favorite_travels_pkey PRIMARY KEY (user_id, travel_id)
);

-- Categories of travels

CREATE TABLE IF NOT EXISTS categories
(
  id SERIAL NOT NULL PRIMARY KEY ,
  name TEXT NOT NULL
);

-- Category and Travel

CREATE TABLE IF NOT EXISTS travel_category
(
  travel_id INTEGER REFERENCES travels (id) ON UPDATE CASCADE ON DELETE CASCADE,
  category_id INTEGER REFERENCES categories (id) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT travel_category_pkey PRIMARY KEY (travel_id, category_id)
);

-- Hotels

CREATE TABLE IF NOT EXISTS hotels
(
  id SERIAL NOT NULL PRIMARY KEY,
  name TEXT NOT NULL,
  location text NOT NULL,
  address TEXT NOT NULL,
  lat FLOAT4 NOT NULL,
  lon FLOAT4 NOT NULL,
  description TEXT ,
  stars INTEGER
);

-- Self and Wego hotels

CREATE TABLE IF NOT EXISTS self_wego_hotel
(
  hotels_id INTEGER REFERENCES hotels (id) ON UPDATE CASCADE ON DELETE CASCADE,
  wego_hotel_id INTEGER,
  CONSTRAINT self_wego_hotel_pkey PRIMARY KEY (hotels_id, wego_hotel_id)
);