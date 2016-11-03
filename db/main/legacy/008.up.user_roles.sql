CREATE TABLE user_roles (
  user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
  role    TEXT NOT NULL,
  created TIMESTAMP DEFAULT now(),
  CONSTRAINT user_rights_pkey PRIMARY KEY (user_id, role)
)
