DELIMITER $$
CREATE FUNCTION `flow_data`.`insertLocation`(loc varchar(255), reg INT)
RETURNS INT
BEGIN
  INSERT INTO Locations(location, region) VALUES (loc, reg);
  RETURN LAST_INSERT_ID();
END $$
DELIMITER ;

