CREATE DATABASE spw;
USE spw;

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

INSERT INTO `staffs` (`name`, `email`, `address`) VALUES ('lovelock', 'lovelock@gmail.com', 'Beijing');
INSERT INTO `staffs` (`name`, `email`, `address`) VALUES ('lovelock1', 'lovelock1@gmail.com', 'Beijing');
INSERT INTO `staffs` (`name`, `email`, `address`) VALUES ('lovelock2', 'lovelock2@gmail.com', 'Beijing');
INSERT INTO `staffs` (`name`, `email`, `address`) VALUES ('lovelock3', 'lovelock3@gmail.com', 'Beijing');
INSERT INTO `staffs` (`name`, `email`, `address`) VALUES ('lovelock4', 'lovelock4@gmail.com', 'Beijing');


INSERT INTO `books` (`name`, `tags`) VALUES ('Code Complete', '["development", "software engineering"]');
INSERT INTO `books` (`name`, `tags`) VALUES ('Core Java', '["java", "development"]');
INSERT INTO `books` (`name`, `tags`) VALUES ('Thinking in Python', '["python", "development"]');
