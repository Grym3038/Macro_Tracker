<?php
// define your connection settings as simple variables
$servername   = "localhost";
$databaseName = "u965274750_macro_Tracker";
$username     = "u965274750_Root";
$password     = "QaLB/31|";

// attempt the connection
$conn = mysqli_connect($servername, $username, $password, $databaseName);

// check for errors
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully";

// close when done
mysqli_close($conn);
?>
