// Search results page

<?php

    session_start();
    require_once('dbfuncs/dbfunctions.php');
    require_once('dbfuncs/dblogin.php');

    $conn = new mysqli($hn, $un, $pw, $db);
        if ($conn->connect_error)
            die($conn->connect_error);
    
echo "Currently under construction.";








?>