<?php
include('config.php');
//ALTER TABLE
//ALTER TABLE sightings ADD last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
$altertable = $db->pdo->prepare('ALTER TABLE sightings ADD last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
$altertable->execute();

$altertable2 = $db->pdo->prepare('ALTER TABLE fort_sightings ADD last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
$altertable2->execute();

echo "Successfully applied any changes that were needed!";