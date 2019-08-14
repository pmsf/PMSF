ALTER TABLE `poi`  ADD `poiimageurl` VARCHAR(200) NULL  AFTER `notes`,  ADD `poiimagedeleteurl` VARCHAR(200) NULL  AFTER `poiimageurl`,  ADD `poisurroundingurl` VARCHAR(200) NULL  AFTER `poiimagedeleteurl`,  ADD `poisurroundingdeleteurl` VARCHAR(200) NULL  AFTER `poisurroundingurl`;

