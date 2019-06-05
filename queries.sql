INSERT INTO categories (name, symbol_code) VALUES 
('Доски и лыжи', 'boards'), ('Крепления', 'attachment'), ('Ботинки', 'boots'), 
('Одежда', 'clothing'), ('Инструменты', 'tools'), ('Разное', 'other');

INSERT INTO users (full_name, email, password, contacts) VALUES 
('John Doe', 'john-doe@mail.com', '$2y$10$.QMOgin932TFXznBn5LK2.wXCCZ7dQQLrVf/iNwe.JYno6pkZ6A1q', '-'),
('Richard Roe', 'richard-roe@yahoo.com', '$2y$10$XKSnirFRN9xoyyTw.RUoZ.xHCqOlmWN8283htMEi5TQNBMf9cMEEq', '-');

INSERT INTO lots (name, about, image, start_cost, rate_step, current_cost, user_id, category_id, end_at)
VALUES
('2014 Rossignol District Snowboard', '-', 'img/lot-1.jpg', 10999, 1000, 10999, 1, 1, '2019-06-10'),
('DC Ply Mens 2016/2017 Snowboard', '-', 'img/lot-2.jpg', 159999, 1000, 159999, 1, 1, '2019-06-05'),
('Крепления Union Contact Pro 2015 года размер L/XL', '-', 'img/lot-3.jpg', 8000, 500, 8000, 2, 2, '2019-06-07'),
('Ботинки для сноуборда DC Mutiny Charocal', '-', 'img/lot-4.jpg', 10999, 1000, 10999, 2, 3, '2019-06-06'),
('Куртка для сноуборда DC Mutiny Charocal', '-', 'img/lot-5.jpg', 7500, 1000, 7500, 1, 4, '2019-06-11'),
('Маска Oakley Canopy', '-', 'img/lot-6.jpg', 5400, 500, 5400, 2, 6, '2019-06-09');

INSERT INTO rates (cost, user_id, lot_id) VALUES
(12000, 2, 4),
(13000, 1, 4);


SELECT name FROM categories;

SELECT lots.name, start_cost, image, current_cost, categories.name 
FROM lots JOIN categories ON categories.id = category_id 
WHERE winner_id IS NULL
ORDER BY lots.id;

SELECT lots.name, start_cost, image, current_cost, categories.name 
FROM lots JOIN categories ON categories.id = category_id
WHERE lots.id = 5;

UPDATE lots SET name = 'Some new name' WHERE id = 3;

SELECT cost FROM rates JOIN lots ON lots.id = lot_id
WHERE lots.id = 4
ORDER BY rates.id;

