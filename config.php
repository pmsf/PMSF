<?php

// Do not touch this!
require 'Medoo.php';
use Medoo\Medoo;

//======================================================================
// MSF - CONFIG FILE
// https://github.com/Nuro/MSF
//======================================================================

//-----------------------------------------------------
// MAP SETTINGS
//-----------------------------------------------------

/* Location Settings */

$startingLat = 50.000000;                                      // Starting latitude
$startingLng = -8.000000;                                      // Starting longitude

/* Map Title + Language */

$title = "Pokemon Go Map";                                      // Title to display in title bar
$locale = "en";                                                 // Display language

/* Google Maps Key */

$gmapsKey = "Your-Google-Maps-API-Key";          // Google Maps API Key


//-----------------------------------------------------
// DATA MANAGEMENT
//-----------------------------------------------------

// Clear pokemon from database this many hours after they disappear (0 to disable)
// This is recommended unless you wish to store a lot of backdata for statistics etc!

// Currently not implemented (Coming Soon)

$purgeData = 0;


//-----------------------------------------------------
// DATABASE CONFIG
//-----------------------------------------------------

global $database;

$database = new Medoo([
    // required
    'database_type' => 'mysql',                                 // mysql/mariadb/pgsql/sybase/oracle/mssql/sqlite
    'database_name' => 'pokeminer',
    'server' => '127.0.0.1',
    'username' => 'dbuser',
    'password' => 'dbpassword',
    'charset' => 'utf8',

    // [optional]
    //'port' => 5432,                                             // Comment out if not needed, just add // in front!
]);