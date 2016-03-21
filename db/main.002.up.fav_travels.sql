-- Favorite travels

CREATE TABLE IF NOT EXISTS favorite_travels
(
  user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
  travel_id INTEGER REFERENCES travels (id) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT favorite_travels_pkey PRIMARY KEY (user_id, travel_id),
);
