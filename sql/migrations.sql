CREATE TABLE IF NOT EXISTS `relationships` (
  `publisher_id_1` INT NOT NULL,
  `publisher_id_2` INT NOT NULL,
  PRIMARY KEY (publisher_id_1, publisher_id_2),
  FOREIGN KEY (publisher_id_1) REFERENCES `pioneers`(id) ON DELETE CASCADE ,
  FOREIGN KEY (publisher_id_2) REFERENCES `pioneers`(id) ON DELETE CASCADE
);


ALTER TABLE locations ADD COLUMN capacity INT NOT NULL DEFAULT 0;
UPDATE locations SET capacity = 3;


UPDATE locations SET markers = '[{"color":"red","label":"A","coordinates":"-33.848297,151.085918"},{"color":"red","label":"B","coordinates":"-33.848330,151.085164"}]' WHERE id = 1
UPDATE locations SET markers = '[{"color":"red","label":"A","coordinates":"-33.834387,151.056439"}]' WHERE ID = 2;
UPDATE locations SET markers = '[{"color":"red","label":"A","coordinates":"-33.830509,151.086847"},{"color":"red","label":"B","coordinates":"-33.830580,151.087325"},{"color":"red","label":"C","coordinates":"-33.832808,151.085764"},{"color":"red","label":"D","coordinates":"-33.828065,151.083339"}]' WHERE id = 3;
UPDATE locations SET markers = '[{"color":"red","label":"A","coordinates":"-33.859055,151.088773"},{"color":"red","label":"B","coordinates":"-33.859731,151.088041"},{"color":"red","label":"C","coordinates":"-33.859095,151.088923"}]' WHERE id = 4;
UPDATE locations SET markers = '[{"color":"red","label":"A","coordinates":"-33.822681,151.079010"},{"color":"red","label":"B","coordinates":"-33.826619,151.080370"}]' WHERE id = 5;
