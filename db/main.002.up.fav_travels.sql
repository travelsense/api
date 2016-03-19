-- Favorite travels

-- DROP TABLE favorite_travels;

CREATE TABLE IF NOT EXISTS favorite_travels
(
  user_id integer NOT NULL,
  travel_id integer NOT NULL,
  CONSTRAINT favorite_travels_pkey PRIMARY KEY (user_id, travel_id),
  CONSTRAINT favorite_travels_user_id_fkey FOREIGN KEY (user_id)
      REFERENCES users (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT favorite_travels_travel_id_fkey FOREIGN KEY (travel_id)
      REFERENCES travels (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE SET NULL
)
WITH (
  OIDS=FALSE
);
ALTER TABLE favorite_travels
  OWNER TO vaca_dev;
