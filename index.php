<?php
session_start();

// Connect to the database
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "op_expenses";
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Check if there are any records in the database
$sql = "SELECT COUNT(*) as count FROM expenses";
$result = mysqli_query($conn, $sql);
$count = mysqli_fetch_assoc($result)['count'];

// If there are records, redirect to the display page
if ($count > 0) {
  header("Location: dashboard.php");
  exit();
}

// Check if the form was submitted and a file was chosen
if (isset($_POST['submit']) && empty($_FILES['csv_file']['name'])) {
  $_SESSION['error_message'] = "Please select a file to upload.";
  header("Location: index.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link href="style.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://kit.fontawesome.com/04112681b9.js" crossorigin="anonymous"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <title>Revenue and Expense Charts</title>
    <style>
  /* button */
  input[type="submit"] {
    margin-left: -20px;
    padding: 10px;
    background-color: #4CAF50;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  /* Center button */
  .center {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
  }
</style>
    
</head>
<body class="">
  <div class="wrapper ">
    <div class="sidebar" data-color="white" data-active-color="danger">
      <div class="logo">
        <a href=".index.php" class="simple-text logo-mini">
          <div class="logo-image-small">
            <img src="logo-small2.png">
          </div>
        </a>
        <a href="#" class="simple-text logo-normal">
          Gaurav Sawant
        </a>
      </div>
      <div class="sidebar-wrapper">
        <ul class="nav">
          <li>
            <a href="dashboard.php">
            <i class="fa-solid fa-chart-column"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li>
            <a href="dept.php">
            <i class="fa-solid fa-building-user"></i>
              <p>Department</p>
            </a>
          </li>

          <li>
            <a href="data.php">
            <i class="fa-solid fa-table"></i>
              <p>Data</p>
            </a>
          </li>

          <li>
            <a href="#">
            <ion-icon class="logo" name="person-circle-outline" size="large"></ion-icon>
              <p>About</p>
            </a>
          </li>
        </ul>
      </div>
    </div>
    <div class="main-panel">
      <div class="content" style="margin-top: 10px;" > 
        <!-- <h1>Hiii</h1> -->
        <!-- <h2>Revenue and Expense Charts</h2> -->
<?php
  // Check if there is an error message in the session variable
  if (isset($_SESSION['error_message'])) {
    echo "<p>" . $_SESSION['error_message'] . "</p>";
    unset($_SESSION['error_message']);
  }
  ?>

<div class="center">
  <form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="csv_file">
    <input class="upload" type="submit" name="submit" value="Upload CSV">
  </form>
</div>
</body>
</html>