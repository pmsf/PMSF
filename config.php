<?php
namespace Config;

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

$startingLat = 41.771822;                                      // Starting latitude
$startingLng = -87.8549371;                                      // Starting longitude

$maxLatLng = 1;                                                 // Max latitude and longitude size (1 = ~110km, 0 to disable)

/* Map Title + Language */

$title = "MSF Glennmen";                                      // Title to display in title bar
$locale = "en";                                                 // Display language

/* Google Maps Key */

$gmapsKey = "";          // Google Maps API Key


//-----------------------------------------------------
// DATA MANAGEMENT
//-----------------------------------------------------

// Clear pokemon from database this many hours after they disappear (0 to disable)
// This is recommended unless you wish to store a lot of backdata for statistics etc!

$purgeData = 0;


//-----------------------------------------------------
// DATABASE CONFIG
//-----------------------------------------------------

$db = new Medoo([// required
            'database_type' => 'mysql',                                 // mysql/mariadb/pgsql/sybase/oracle/mssql/sqlite
            'database_name' => 'Monocle',
            'server' => '127.0.0.1',
            'username' => 'database_user',
            'password' => 'database_password',
            'charset' => 'utf8',

            // [optional]
            //'port' => 5432,                                             // Comment out if not needed, just add // in front!
        ]);