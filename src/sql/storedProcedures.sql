DELIMITER $$
CREATE FUNCTION `flow_data`.`insertLocation`(loc varchar(255))
RETURNS INT
BEGIN
  INSERT INTO Locations(location) VALUES (loc);
  RETURN LAST_INSERT_ID();
END $$
DELIMITER ;