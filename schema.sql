CREATE DATABASE 951517_yeticave_9
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;

USE 951517_yeticave_9;

CREATE TABLE users (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  full_name  VARCHAR(50) NOT NULL,
  email      VARCHAR(50) NOT NULL UNIQUE,
  password   VARCHAR(60) NOT NULL,
  avatar     VARCHAR(100),
  contacts   TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE users ENGINE = MyISAM;

CREATE TABLE categories (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(30),
  symbol_code VARCHAR(30),
  UNIQUE(name, symbol_code)
);

CREATE TABLE lots (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(70) NOT NULL,
  about        TEXT NOT NULL,
  image        VARCHAR(100) NOT NULL,
  start_cost   INT NOT NULL,
  rate_step    INT NOT NULL,
  current_cost INT NOT NULL,
  user_id      INT REFERENCES users (id),
  winner_id    INT REFERENCES users (id),
  category_id  INT REFERENCES categories (id),
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  end_at       TIMESTAMP
);

ALTER TABLE lots ENGINE = MyISAM;

CREATE TABLE rates (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  cost       INT NOT NULL,
  user_id    INT REFERENCES users (id),
  lot_id     INT REFERENCES lots (id),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE UNIQUE INDEX user_id ON users(id);
CREATE INDEX user_name ON users(full_name);
CREATE UNIQUE INDEX user_email ON users(email);
CREATE INDEX password ON users(password);
CREATE INDEX avatar ON users(avatar);
CREATE FULLTEXT INDEX contacts ON users(contacts);

CREATE UNIQUE INDEX category_id ON categories(id);
CREATE UNIQUE INDEX category_name ON categories(name);
CREATE UNIQUE INDEX category_code ON categories(symbol_code);

CREATE UNIQUE INDEX lot_id ON lots(id);
CREATE INDEX lot_name ON lots(name);
CREATE FULLTEXT INDEX about_lot ON lots(about);
CREATE INDEX lot_image ON lots(image);
CREATE INDEX lot_start_cost ON lots(start_cost);
CREATE INDEX lot_current_cost ON lots(start_cost);
CREATE INDEX rate_step ON lots(rate_step);
CREATE INDEX lot_end ON lots(end_at);

CREATE UNIQUE INDEX rate_id ON rates(id);
CREATE INDEX rate_cost ON rates(cost);
CREATE INDEX rate_date ON rates(created_at);

