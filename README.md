Lumivote.com
==========
Source code for the Lumivote website at http://lumivote.com/

##Overview

###Code Overview
* Front-end interface with Back-end Web API
* Front-end primarily implemented in HTML, CSS, AngularJS
* Back-end implemented using PHP with MariaSQL Database

###How to Use
* Set up Google Cloud SQL for database storage
* Import included .sql file
* Set up Google App Engine and connect to Google Cloud SQL
* Deploy the entire package using app engine
* Deploy PhpMyAdmin using app engine if administrator database access is desired
* Use Java moderator tool to add, edit, delete questions and add, delete users if desired

###Limitations
* Initial setup is time-consuming (setting up Google Cloud)
* Expensive front-end instance and database hosting
* PHP is antiquated and difficult to use with future desired functionalities like push notifications

##About
Written by Jiawei Zhang

View the corresponding Android application at https://github.com/alexdao/Lumivote

##Libraries
Built with the help of these frameworks:
* [AngularJS](https://angularjs.org/)
* [Slim](http://www.slimframework.com/)
* [Bootstrap](http://getbootstrap.com/)

With the help of these AngularJS directives:
* [Angular-UI](https://angular-ui.github.io/bootstrap/)

With the help of these tools:
* [phpMyAdmin](https://www.phpmyadmin.net/)
* [MySQL Workbench](https://www.mysql.com/products/workbench/)
* [Google Cloud SQL](https://cloud.google.com/sql/)
* [Google App Engine](https://cloud.google.com/appengine/)
* [Font Awesome](https://fortawesome.github.io/Font-Awesome/)
* [Bootstrap Social](http://lipis.github.io/bootstrap-social/)

Data taken from the following APIs:
* [Lumivote API](http://lumivote.com)
* [Sunlight Congress API](https://sunlightlabs.github.io/congress/)
* [United States Images](https://github.com/unitedstates/images)
* [NY Times Congress API](http://developer.nytimes.com/docs/read/congress_api)
* [GovTrack.us](https://www.govtrack.us)
* [congress-legislators Database](https://github.com/unitedstates/congress-legislators)


Special thanks to:
* Alex Dao for corresponding Android application
* Soojee Chung for front-end design

License
--------

	Copyright 2015 Jiawei Zhang.

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
