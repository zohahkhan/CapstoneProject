CREATE TABLE Session (
  session_id       CHAR(64)        NOT NULL,
  user_id          INT             NOT NULL,
  created_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_seen_at     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at       TIMESTAMP       NOT NULL,
  revoked_at       TIMESTAMP       NULL,
  PRIMARY KEY (session_id),
  INDEX (user_id),
  INDEX (expires_at),
  FOREIGN KEY (user_id) REFERENCES User(user_id)
);
