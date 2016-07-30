SELECT Locations.P_Id, Locations.location, Location_Data.current_capacity, Location_Data.time_stamp
FROM Locations
JOIN Location_Data
ON Locations.P_Id = Location_Data.P_Id
WHERE Location_Data.time_stamp = (SELECT MAX(Location_Data.time_stamp)
                                  FROM Location_Data
                                  WHERE Location_Data.P_Id = :id)
AND Locations.P_Id = :id;

SELECT Locations.P_Id, Locations.location, Location_Data.current_capacity, Location_Data.time_stamp
FROM Locations
JOIN Location_Data
ON Locations.P_Id = Location_Data.P_Id
WHERE Location_Data.time_stamp = (SELECT MAX(Location_Data.time_stamp)
                                  FROM Location_Data
                                  WHERE Location_Data.P_Id = :id)
AND Locations.P_Id = :id AND region = :region;

-- Select by Region
SELECT Current_Data.P_Id, Current_Data.capacity, Locations.region
FROM Current_Data
INNER JOIN Locations
ON Current_Data.P_Id = Locations.P_Id
WHERE Locations.region = :reg;

-- Select by location
SELECT Current_Data.P_Id, Current_Data.capacity
FROM Current_Data
WHERE Current_Data.P_Id = :id;

BEGIN
    SELECT Current_Data.P_Id, Current_Data.capacity
	  FROM Current_Data
	  WHERE Current_Data.P_Id = :id;
END