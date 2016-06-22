-- SQL to create function to insert new location
CREATE FUNCTION `flow_data`.`insertLocation`(loc varchar(255), reg INT)
RETURNS INT
BEGIN
    INSERT INTO Locations(location, region) VALUES (loc, reg);
    RETURN LAST_INSERT_ID();
END $$


-- SQL to create stored procedure 'getByLocation'
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
    WHERE Location_Data.time_stamp = (SELECT MAX(Location_Data.time_stamp)
                                      FROM Location_Data
                                      WHERE Location_Data.P_Id = id)
    AND Locations.P_Id = id;
END $$


-- SQL to create stored procedure 'getByRegion'
CREATE PROCEDURE `flow_data`.`getByRegion`(reg VARCHAR(20))
LANGUAGE SQL
DETERMINISTIC
SQL SECURITY DEFINER
COMMENT 'Get by region'
BEGIN

    DECLARE @ids INT;
    SET @ids = SELECT @ids = P_Id FROM Locations WHERE region = reg;

    SELECT Locations.P_Id, Locations.location, Location_Data.current_capacity, Location_Data.time_stamp, Locations.region
    FROM Locations
    JOIN Location_Data
    ON Locations.P_Id = Location_Data.P_Id
    WHERE Location_Data.time_stamp = (SELECT MAX(Location_Data.time_stamp)
                                      FROM Location_Data
                                      WHERE Location_Data.P_Id = @ids)
    AND Locations.P_Id = @ids
END $$


-- SQL to create stored procedure 'getByRegionAndLocation'
CREATE PROCEDURE `flow_data`.`getByRegionAndLocation`(id INT, reg VARCHAR(20))
LANGUAGE SQL
DETERMINISTIC
SQL SECURITY DEFINER
COMMENT 'Get by region and location'
BEGIN
    SELECT Locations.P_Id, Locations.location, Location_Data.current_capacity, Location_Data.time_stamp, Locations.region
    FROM Locations
    JOIN Location_Data
    ON Locations.P_Id = Location_Data.P_Id
    WHERE Location_Data.time_stamp = (SELECT MAX(Location_Data.time_stamp)
                                      FROM Location_Data
                                      WHERE Location_Data.P_Id = id)
    AND Locations.P_Id = id
	  AND Locations.region = reg;
END $$