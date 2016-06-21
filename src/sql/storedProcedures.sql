DELIMITER $$
CREATE FUNCTION `flow_data`.`insertLocation`(loc varchar(255), reg INT)
RETURNS INT
BEGIN
  INSERT INTO Locations(location, region) VALUES (loc, reg);
  RETURN LAST_INSERT_ID();
END $$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `flow_data`.`getByLocation`(IN id INT)
LANGUAGE SQL
DETERMINISTIC
SQL SECURITY DEFINER
COMMENT 'Get by location'
BEGIN
    SELECT Locations.P_Id, Locations.location, Location_Data.current_capacity, Location_Data.time_stamp
    FROM Locations
    JOIN Location_Data
    ON Locations.P_Id = Location_Data.P_Id
    WHERE Location_Data.time_stamp = (SELECT MAX(Location-Data.time_stamp)
                                      FROM Location_Data
                                      WHERE Location_Data.P_Id = id)
    AND Locations.P_Id = id;
END $$