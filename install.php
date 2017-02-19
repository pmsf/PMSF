<?php

include('config.php');

//ALTER TABLE
//ALTER TABLE sightings ADD last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP

$altertable = $database->pdo->prepare('ALTER TABLE sightings ADD last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
$altertable->execute();