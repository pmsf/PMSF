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

$startingLat = 50.4005976;                                      // Starting latitude
$startingLng = -4.1355412;                                      // Starting longitude

/* Map Title + Language */

$title = "Pokemon Go Map";                                      // Title to display in title bar
$locale = "en";                                                 // Display language

/* Google Maps Key */

$gmapsKey = "AIzaSyDosJv4qcYZsGNNXMC-4MmZsCR1eSE92VU";          // Google Maps API Key


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
            'database_name' => 'pokeminer',
            'server' => '127.0.0.1',
            'username' => 'database_user',
            'password' => 'database_password',

            // [optional]
            //'charset' => 'utf8',
            //'port' => 5432,                                             // Comment out if not needed, just add // in front!
        ]);