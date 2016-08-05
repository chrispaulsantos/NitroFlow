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
SELECT Current_Data.P_Id, Current_Data.capacity, Locations.region, Locations.location
FROM Current_Data
INNER JOIN Locations
ON Current_Data.P_Id = Locations.P_Id
WHERE Locations.region = 1;

-- Select by location
SELECT `Current_Data`.P_Id, `Current_Data`.capacity
FROM Current_Data
INNER JOIN Locations
ON Locations.P_Id = Current_Data.P_Id
WHERE Current_Data.P_Id = :id;

---------- Queries ------------

-- Select betweeen times
SELECT *
FROM Location_Data
WHERE timeStamp < 1470169340
      AND timeStamp > 1470169240
      AND P_Id = 2;