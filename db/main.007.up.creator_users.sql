CREATE TABLE IF NOT EXISTS actions
(
  id SERIAL NOT NULL PRIMARY KEY ,
  travel_id INTEGER REFERENCES travels (id) ON UPDATE CASCADE ON DELETE SET NULL,
  offset_start INTEGER,
  offset_end INTEGER,
  car BOOLEAN,
  airports JSON,
  hotels JSON,
  sightseeings JSON,
  type TEXT
);
