DROP DATABASE IF EXISTS `foodist`;
CREATE DATABASE `foodist`;

DROP TABLE IF EXISTS `dishes`;
DROP TABLE IF EXISTS `cafeterias`;

CREATE TABLE `cafeterias`
(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `cafeterias` (`id`)
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

CREATE TABLE `dishes`
(
    `id`           int(11) NOT NULL AUTO_INCREMENT,
    `cafeteria_id` int(11),
    `name`         varchar(255),
    `price`        float,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`cafeteria_id`) REFERENCES `cafeterias` (id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `dishes` (`id`, `cafeteria_id`, `name`, `price`)
VALUES (NULL, 1, 'Soup', 0.6),
       (NULL, 1, 'Cooked Porkchop', 1.5);