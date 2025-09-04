<?php


// $servername = "sql301.epizy.com";
// $username = "epiz_29347615";
// $password = "uOUnn01b47A7leO";
// $dbname = "epiz_29347615_nptcom";

// $servername = "localhost";
// $username = "380718";
// $password = "12345678";
// $dbname = "380718";


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "company";


// Create connection

$conn = mysqli_connect($servername, $username, $password, $dbname);
mysqli_set_charset($conn, "utf8");
// Check connection

if (!$conn) {

    die("Connection failed: " . mysqli_connect_error());
}
