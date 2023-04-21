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
    
</head>
<body>

  <div class="wrapper ">
    <div class="sidebar" data-color="white" data-active-color="danger">
      <div class="logo">
        <a href="index.php" class="simple-text logo-mini">
          <div class="logo-image-small">
            <img src="logo-small2.png">
          </div>
        </a>
        <a href="index.php" class="simple-text logo-normal">
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

          <li class="active ">
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
        <a href="#" onclick="if (confirm('Are you sure you want to clear all expenses data?')) { window.location.href = 'clear.php'; }">Clear Data<br><br></a>

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

    // Query the database to get the unique years
    $sql = "SELECT DISTINCT year FROM expenses ORDER BY year DESC";
    $result = mysqli_query($conn, $sql);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Display the year filter dropdown
    echo "<label for='year'>Filter by Year:</label>";
    echo "<select id='year' name='year'>";
    echo "<option value=''>All Years</option>";
    foreach ($rows as $row) {
      echo "<option value='" . $row['year'] . "'>" . $row['year'] . "</option>";
    }
    echo "</select><br><br>";

    // Query the database to get the expenses data
    $sql = "SELECT * FROM expenses ORDER BY year DESC, month DESC";
    $result = mysqli_query($conn, $sql);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Display the expenses data in a HTML table using DataTables
    echo "<table id='expenses_table' class='display'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Year</th>";
    echo "<th>Month</th>";
    echo "<th>Expense</th>";
    echo "<th>Revenue</th>";
    echo "<th>Department</th>";
    echo "<th>OperationalExpenses</th>";
    echo "<th>NonOperationalExpenses</th>";

    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($rows as $row) {
      echo "<tr>";
      echo "<td>" . $row['year'] . "</td>";
      echo "<td>" . $row['month'] . "</td>";
      echo "<td>" . $row['expense'] . "</td>";
      echo "<td>" . $row['revenue'] . "</td>";
      echo "<td>" . $row['department'] . "</td>";
      echo "<td>" . $row['operationalExpenses'] . "</td>";
      echo "<td>" . $row['nonOperationalExpenses'] . "</td>";

      echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";

    // Close the database connection
    mysqli_close($conn);
  ?>

  <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
  <script>
    $(document).ready(function() {
      // Initialize DataTables and set the year filter to all years
      var table = $('#expenses_table').DataTable();
      table.columns(0).search('').draw();

      // Add event listener for the year filter dropdown
      $('#year').on('change', function() {
        var year = $(this).val();
        table.columns(0).search(year).draw();
      });
    });
  </script>
</body>
</html>