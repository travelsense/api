CREATE TABLE IF NOT EXISTS flagged_comments
(
  comment_id INTEGER REFERENCES travel_comments (id) ON UPDATE CASCADE ON DELETE CASCADE,
  user_id INTEGER REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
  created TIMESTAMP DEFAULT now(),
  CONSTRAINT flagged_comments_pkey PRIMARY KEY (comment_id, user_id)
);
