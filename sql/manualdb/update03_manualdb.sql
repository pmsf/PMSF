ALTER TABLE ingress_portals ADD INDEX coordsIndex (lat, lon);
ALTER TABLE ingress_portals ADD INDEX updatedIndex (updated);

ALTER TABLE nests ADD INDEX CoordsIndex (lat, lon);
ALTER TABLE nests ADD INDEX UpdatedIndex (updated);