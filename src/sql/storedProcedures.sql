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

-- SQL to create stored procedure 'insertCurrentData'
CREATE PROCEDURE `flow_data`.`insertCurrentData`( capacity INT, id INT )
LANGUAGE SQL
DETERMINISTIC
SQL SECURITY DEFINER
COMMENT 'Insert current capacity into Current_Data table'
BEGIN
    UPDATE `flow_data`.`Current_Data`
	  SET Current_Data.capacity=capacity, Current_Data.time_stamp=CURRENT_TIMESTAMP
    WHERE Current_Data.P_Id = id;
END $$

----------- THESE ARE GOOD BELOW THIS LINE ---------------

-- SQL to create stored procedure 'getByRegion'
CREATE PROCEDURE `flow_data`.`getByRegion`(region VARCHAR(20))
LANGUAGE SQL
DETERMINISTIC
SQL SECURITY DEFINER
COMMENT 'Get by region'
BEGIN
    SELECT Current_Data.P_Id, Current_Data.capacity, Locations.region, Locations.location
    FROM Current_Data
    INNER JOIN Locations
    ON Current_Data.P_Id = Locations.P_Id
    WHERE Locations.region = region;
END $$