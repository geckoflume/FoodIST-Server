-- DROP DATABASE IF EXISTS foodist;
-- CREATE DATABASE foodist;
ALTER DATABASE foodist CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin;

DROP TABLE IF EXISTS beacons;
DROP TABLE IF EXISTS pictures;
DROP TABLE IF EXISTS dishes;
DROP TABLE IF EXISTS cafeterias;

CREATE TABLE cafeterias
(
    id int(11) PRIMARY KEY AUTO_INCREMENT
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE utf8mb4_bin;

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

CREATE TABLE beacons
(
    id              int(11) PRIMARY KEY AUTO_INCREMENT,
    cafeteria_id    int(11)     NOT NULL,
    datetime_arrive varchar(30) NOT NULL,
    datetime_leave  varchar(30),
    duration        int(11),
    count_in_queue  int(11)     NOT NULL,
    FOREIGN KEY (cafeteria_id) REFERENCES cafeterias (id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE utf8mb4_bin;

CREATE TABLE dishes
(
    id           int(11) PRIMARY KEY AUTO_INCREMENT,
    cafeteria_id int(11)      NOT NULL,
    name         varchar(255) NOT NULL,
    price        float        NOT NULL,
    FOREIGN KEY (cafeteria_id) REFERENCES cafeterias (id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE utf8mb4_bin;

CREATE TABLE pictures
(
    id       int(11) PRIMARY KEY AUTO_INCREMENT,
    dish_id  int(11)             NOT NULL,
    filename varchar(191) UNIQUE NOT NULL,
    FOREIGN KEY (dish_id) REFERENCES dishes (id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE utf8mb4_bin;