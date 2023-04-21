<?php
// Connect to the database
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "op_expenses";
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Delete all rows from the expenses table
$sql = "DELETE FROM expenses";
mysqli_query($conn, $sql);

// Close the database connection
mysqli_close($conn);

// Redirect back to the index page
header("Location: ./index.php");
exit();
?>