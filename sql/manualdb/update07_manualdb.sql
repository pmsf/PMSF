ALTER TABLE `poi`  ADD `poiimageurl` VARCHAR(200) NULL  AFTER `notes`,  ADD `poiimagedeletehash` VARCHAR(200) NULL  AFTER `poiimageurl`,  ADD `poisurroundingurl` VARCHAR(200) NULL  AFTER `poiimagedeleteurl`,  ADD `poisurroundingdeletehash` VARCHAR(200) NULL  AFTER `poisurroundingurl`;

