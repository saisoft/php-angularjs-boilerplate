<?php

//load redbean

require 'readbean/rb.php';

//set up database
//R::setup('mysql:host=127.0.0.1; dbname=projectname_db','db_user','projectname_db_$ecure');
R::setup('mysql:host=127.0.0.1; dbname=projectname_db', 'root', '');
R::freeze(true);
?>
