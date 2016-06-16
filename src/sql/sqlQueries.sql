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