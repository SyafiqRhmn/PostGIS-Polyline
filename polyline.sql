CREATE TABLE polyline (
    id SERIAL PRIMARY KEY,
    geo GEOGRAPHY(LINESTRING)
);

SELECT ST_AsText(geo) FROM polyline;
select * from polyline
ALTER TABLE polyline ADD COLUMN geom geometry(LineString,4326);
ALTER TABLE polyline ADD COLUMN geom geometry(LineString, 4326);
ALTER TABLE polyline DROP COLUMN geom;
DROP TABLE polyline;

DELETE FROM polyline