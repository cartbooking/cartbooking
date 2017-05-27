CREATE TABLE IF NOT EXISTS `relationships` (
  `publisher_id_1` INT NOT NULL,
  `publisher_id_2` INT NOT NULL,
  PRIMARY KEY (publisher_id_1, publisher_id_2),
  FOREIGN KEY (publisher_id_1) REFERENCES `pioneers`(id) ON DELETE CASCADE ,
  FOREIGN KEY (publisher_id_2) REFERENCES `pioneers`(id) ON DELETE CASCADE
);


ALTER TABLE locations ADD COLUMN capacity INT NOT NULL DEFAULT 0;
UPDATE locations SET capacity = 3;
