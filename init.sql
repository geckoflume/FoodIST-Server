DROP DATABASE IF EXISTS foodist;
CREATE DATABASE foodist;

-- DROP TABLE IF EXISTS pictures;
-- DROP TABLE IF EXISTS dishes;
-- DROP TABLE IF EXISTS cafeterias;

CREATE TABLE cafeterias
(
    id int(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO cafeterias (id)
VALUES (1),
       (2),
       (3),
       (4),
       (5),
       (6),
       (7),
       (8),
       (9),
       (10),
       (11),
       (12),
       (13),
       (14),
       (15);

CREATE TABLE dishes
(
    id           int(11)      NOT NULL AUTO_INCREMENT,
    cafeteria_id int(11)      NOT NULL,
    name         varchar(255) NOT NULL,
    price        float        NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (cafeteria_id) REFERENCES cafeterias (id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO dishes (cafeteria_id, name, price)
VALUES (1, 'Soup', 0.6),
       (1, 'Cooked Porkchop', 1.5);

CREATE TABLE pictures
(
    id      int(11) NOT NULL AUTO_INCREMENT,
    dish_id int(11) NOT NULL,
    filename varchar(255) UNIQUE NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (dish_id) REFERENCES dishes (id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO pictures (dish_id, filename)
VALUES (1, 'photo1.jpg'),
       (1, 'photo2.jpg');