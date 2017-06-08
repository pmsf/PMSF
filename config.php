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

$startingLat = 41.652892;                                      // Starting latitude
$startingLng = -83.541549;                                      // Starting longitude

/* Map Title + Language */

$title = "MSF Glennmen";                                      // Title to display in title bar
$locale = "en";                                                 // Display language

/* Google Maps Key */

$gmapsKey = "AIzaSyBXZSI0gZ6I4-gumIiUd8Dfs-4SAlYY_xM";          // Google Maps API Key


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
            'database_type' => 'pgsql',                                 // mysql/mariadb/pgsql/sybase/oracle/mssql/sqlite
            'database_name' => 'Monocle',
            'server' => '149.56.240.220',
            'username' => 'monocle',
            'password' => 'Password1!',

            // [optional]
            'charset' => 'utf8',
            'port' => 5432,                                             // Comment out if not needed, just add // in front!
        ]);