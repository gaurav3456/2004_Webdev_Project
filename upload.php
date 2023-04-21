<?php
session_start();

// Check if the form was submitted and a file was chosen
if (isset($_POST['submit']) && empty($_FILES['csv_file']['name'])) {
  $_SESSION['error_message'] = "Please select a file to upload.";
  header("Location: index.php");
  exit();
}

// Connect to the database
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "op_expenses";
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Get uploaded file
$file = $_FILES['csv_file']['tmp_name'];

// Check if the file was uploaded successfully
if (!is_uploaded_file($file)) {
  $_SESSION['error_message'] = "Error uploading file.";
  header("Location: index.php");
  exit();
}

// Open the uploaded file
$handle = fopen($file, "r");

// Read CSV file line by line
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
  // Escape special characters
  $year = mysqli_real_escape_string($conn, $data[0]);
  $month = mysqli_real_escape_string($conn, $data[1]);
  $expense = mysqli_real_escape_string($conn, $data[2]);
  $revenue = mysqli_real_escape_string($conn, $data[3]);
  $department = mysqli_real_escape_string($conn, $data[4]);
  $operationalExpenses = mysqli_real_escape_string($conn, $data[5]);
  $nonOperationalExpenses = mysqli_real_escape_string($conn, $data[6]);

  // Insert the row into the database
  $sql = "INSERT INTO expenses (year, month, expense, revenue, department, operationalExpenses, nonOperationalExpenses) VALUES ('$year', '$month', '$expense', '$revenue', '$department', '$operationalExpenses', '$nonOperationalExpenses')";
  mysqli_query($conn, $sql);
}

fclose($handle);

// Close the database connection
mysqli_close($conn);

// Redirect to the display page
header("Location: dashboard.php");
exit();
?>
