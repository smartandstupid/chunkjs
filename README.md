Hello, sorry for many .less files and bad structure.

Introduction in main theme of project
--------------------------------------------
Class, which i realized, was - lib/general/Chunks.js.php. 
Using this class implemented all files in the folder - lib/scripts/classes and views in - lib/views


Explanation php code
--------------------------------------------
Start.php is entry point. It contains constants from fie lib/general/Direction.php: PAGE, DIR, which used in required files lib/scripts/index.php, for instance. 
Still php files:
lib/generalActiveRecord.php - I have no idea who to apologize for the code in this file :)
lib/php/openPage.php - It alredy have more serious construction for adding js and css codes and remove old. It intercts with function openPage from lib/scripts/index.php
