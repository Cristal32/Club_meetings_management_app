<?php 

$host = "localhost";
$username = "root";
$password = "";
$database = "gestion_reunions_clubs";

//creating database connection
$con = mysqli_connect($host, $username, $password, $database);

//check db connection
if(!$con)
{
    die("Connection failed: ". mysqli_connect_error());
}

?>