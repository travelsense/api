CREATE TABLE IF NOT EXISTS bookings
(
  travel_id INTEGER REFERENCES travels (id) ON UPDATE CASCADE ON DELETE CASCADE,
  user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
  created TIMESTAMP DEFAULT now(),
  CONSTRAINT bookings_pkey PRIMARY KEY (travel_id, user_id)
);

CREATE INDEX bookings_created_idx ON bookings (created);