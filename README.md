# PMSF---Pokemon-Map-Standalone-Frontend

> Current Version 1.0 - First release!

Pokemon Map Standalone Frontend or PMSF for short is a PHP Map Interface for the Monocle and RocketMap Scanner designed to be completely standalone and able to run on any traditional web server

It supports all the common database engines, including MySQL, MariaDB, Postgres, MsSQL, SQLite

## Get Started

### Install via ZIP

* Download the Source Zip and Extract the Files to your Web Host

### Install via Git

* Navigate to your Web Host and 
```
git clone https://github.com/Glennmen/PMSF.git
```

### Edit config.php

* Edit the config.php file making sure to add your database details including database type and port if different to standard.  Google API Key and starting Lat/Lon

### setup.php

####Nginx
* Add nginx.conf to your server nginx.conf file

**Not needed, database columns are not used**
* ~~Browse to your website and run the /install.php for example.... mywebsite.com/install.php.  If you see a white screen with no errors your map should now work, go to your website and enjoy the map!~~


# Common Problems
1) Database wont connect but I know the details are correct.  In this scenario make sure you have the php-database driver installed. For example php7.0-mysql for mysql or php7.0-pgsql for Postgrel

# Feedback
Create an issue if you have any bugs, suggestions, improvements! (Discord channel will follow)

## Thanks

* Thanks MSF for the great basis for this project: [https://github.com/Nuro/MSF](https://github.com/Nuro/MSF)

* Noctem for the awesome Monocle Scanner! [https://github.com/Noctem/Monocle](https://github.com/Noctem/Monocle)

* RocketMap for their scanner: [https://github.com/RocketMap/RocketMap](https://github.com/RocketMap/RocketMap)

* Medoo for the ORM framework: [http://medoo.in](http://medoo.in)