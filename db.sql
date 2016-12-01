CREATE DATABASE spw;
USE spw;
CREATE USER 'spw'@'%' IDENTIFIED BY 'spw';
GRANT ALL PRIVILEGES ON spw.* TO `'spw'@'%'`;
FLUSH PRIVILEGES ;

CREATE TABLE staffs (
  `id` INT AUTO_INCREMENT NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `address` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  UNIQUE `idx_email` (`email`)
) ENGINE = InnoDB CHAR SET = 'utf8mb4';

CREATE TABLE books (
  `id` INT AUTO_INCREMENT NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `tags` JSON NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHAR SET = 'utf8mb4';


CREATE TABLE pairs (
  `id` INT AUTO_INCREMENT NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `dogs` JSON NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHAR SET = 'utf8mb4';


INSERT INTO `books` (`name`, `tags`) VALUES ('Code Complete', '["development", "software engineering"]');
INSERT INTO `books` (`name`, `tags`) VALUES ('Core Java', '["java", "development"]');
INSERT INTO `books` (`name`, `tags`) VALUES ('Thinking in Python', '["python", "development"]');

INSERT INTO `pairs` (`name`, `dogs`) VALUE ('wangqingchun', '["holly", "foo", "bar"]');
INSERT INTO `pairs` (`name`, `dogs`) VALUE ('wangqingchun', '{"foo": "bar", "microsoft": "bing"}');
INSERT INTO `pairs` (`name`, `dogs`) VALUE ('wangqingchun', '{"foo": "bar", "microsoft": "bing", "bar": 3}');
INSERT INTO `pairs` (`name`, `dogs`) VALUE ('wangqingchun', '{"foo": "bar", "microsoft": "bing", "bar": 9}');