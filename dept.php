<?php

$host = 'localhost';
$dbname = 'op_expenses';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

try {
    $years = $pdo->query("SELECT DISTINCT year FROM expenses ORDER BY year ASC")->fetchAll(PDO::FETCH_COLUMN);
    $departments = $pdo->query("SELECT DISTINCT department FROM expenses ORDER BY department ASC")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("ERROR: Could not able to execute query. " . $e->getMessage());
}

if (empty($years) || empty($departments)) {
    echo "No data found. Please add some data to the expenses table.";
    header("Location: ./index.php");
    return false;
}



$selectedYear = isset($_GET['year']) ? $_GET['year'] : $years[0];
$selectedDepartment = isset($_GET['department']) ? $_GET['department'] : $departments[0];

try {
    $sql = "SELECT * FROM expenses WHERE year = $selectedYear AND department = '$selectedDepartment'";
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $revenue = array();
        $expenses = array();
        while ($row = $result->fetch()) {
            $revenue[] = $row["revenue"];
            $expenses[] = $row["expense"];
        }
        unset($result);
    } else {
        echo "No records matching your query were found.";
    }
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}



try {
    $sql1 = "SELECT * FROM expenses WHERE year = $selectedYear AND department = '$selectedDepartment'";
    $resultpro = $pdo->query($sql1);
    if ($resultpro->rowCount() > 0) {
        $profitLine = array();
        $months1 = array();
        while ($row = $resultpro->fetch()) {
            $profitLine[] = $row["revenue"] - $row["expense"];
            $months1[] = $row["month"];
        }
        unset($resultpro);
    } else {
        echo "No records matching your query were found.";
    }
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sql1. " . $e->getMessage());
}



try {
    $sqlop = "SELECT * FROM expenses WHERE year = $selectedYear AND department = '$selectedDepartment'";
    $resultop = $pdo->query($sqlop);
    if ($resultop->rowCount() > 0) {
        $operationalExpenses = array();
        $nonOperationalExpenses = array();
        $monthsop = array();
        while ($row = $resultop->fetch()) {
            $operationalExpenses[] = $row["operationalExpenses"];
            $nonOperationalExpenses[] = $row["nonOperationalExpenses"];
            $monthsop[] = $row["month"];
        }
        unset($resultop);
    } else {
        echo "No records matching your query were found.";
    }
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sqlop. " . $e->getMessage());
}

unset($pdo);
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
        .content{
            padding-bottom: 0px;
        }
       
    
        .chartContainer {
            margin-top: 20px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(2, 1fr);
            grid-gap: 20px;
        }

        .chartBox {
            box-sizing: border-box;
            padding: 20px;
            border: solid 1px #454545;
            border-radius: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .barchart1 {
            grid-column: 1 / 2;
            grid-row: 1 / 2;
        }

            .barchart2 {
            grid-column: 2 / 3;
            grid-row: 1 / 2;
        }

            .linechart1 {
            grid-column: 1 / 2;
            grid-row: 2 / 3;
        }

            .linechart2 {
            grid-column: 2 / 3;
            grid-row: 2 / 3;
        }
        
    </style>
</head>
<body >
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
        <li >
            <a href="dashboard.php">
            <i class="fa-solid fa-chart-column"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="active ">
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
        <form method="get">
        <label for="year">Year:</label>
        <select name="year" id="year">
            <?php
            foreach ($years as $year) {
                $selected = $year == $selectedYear ? 'selected' : '';
                echo "<option value=\"$year\" $selected>$year</option>";
            }
            ?>
        </select>
        <label for="department">Department:</label>
        <select name="department" id="department">
            <?php
            foreach ($departments as $department) {
                $selected = $department == $selectedDepartment ? 'selected' : '';
                echo "<option value=\"$department\" $selected>$department</option>";
            }
            ?>
        </select>
        <button type="submit">Update</button>
        </form>
    <div class="chartContainer">

        <div class="chartBox barchart1">
            <canvas  id="revenueChart"></canvas>
        </div>
        <div class="chartBox barchart2">
            <canvas  id="expenseChart"></canvas>
        </div>
        <div class="chartBox linechart1">
            <canvas id="profit"></canvas>
        </div>
        <div class="chartBox linechart2">
            <canvas id="opnonopchart"></canvas> 
        </div>

    </div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

// Line chart profit
const profitLine = <?php echo json_encode($profitLine); ?>;
const months1 = <?php echo json_encode($months1); ?>;



//operationa/non line graph
const operationalExpenses = <?php echo json_encode($operationalExpenses); ?>;
const nonOperationalExpenses = <?php echo json_encode($nonOperationalExpenses); ?>;
const monthsop = <?php echo json_encode($monthsop); ?>;

    const revenue = <?php echo json_encode($revenue); ?>;
    const expenses = <?php echo json_encode($expenses); ?>;
    const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
    const revenueData = {
        labels: labels,
        datasets: [{
            label: 'Revenue By Month',
            data: revenue,
            // backgroundColor: 'rgba(75, 192, 192, 0.2)',
            // borderColor: 'rgba(75, 192, 192, 1)',
            // backgroundColor: '#d4bbff',
            // borderColor: '#a56eff',
            backgroundColor: '#FFCB91',
            borderColor: '#F4B065',
            borderWidth: 1
        }]
    };
    const expenseData = {
        labels: labels,
        datasets: [{
            label: 'Expense By Month',
            data: expenses,
            // backgroundColor: 'rgba(255, 99, 132, 0.2)',
            // borderColor: 'rgba(255, 99, 132, 1)',
            // backgroundColor: '#bae6ff',
            // borderColor: '#33b1ff',
            backgroundColor: '#97DECE',
            borderColor: '#74a19f',
            borderWidth: 1
        }]
    };

    const chartOptions = {
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function(value, index, values) {
                    return '₹' + value;
                }
            }
        }
    }
};

    const revenueChart = new Chart(
        document.getElementById('revenueChart'),
        {
            type: 'bar',
            data: revenueData,
            options: chartOptions
        }
    );

    const expenseChart = new Chart(
        document.getElementById('expenseChart'),
        {
            type: 'bar',
            data: expenseData,
            options: chartOptions
        }
    );




    const chartDataLineprofit = {
    labels: months1,
    datasets: [
        {
            label: 'Profit',
            data: profitLine,
            fill: false,
            backgroundColor: '#FFCB91',
            borderColor: '#FFCB91',
            tension: 0.1,
            pointBackgroundColor: profitLine.map(value => value < 0 ? '#FF0000' : '#FFCB91'),
            pointBorderColor: '#000000',
            pointRadius: 4,
            pointHoverRadius: 6
        }
    ]
};

const chartOptionsprofit = {
    // maintainAspectRatio: false,
    // aspectRatio: 3,

    scales: {
        y: {
            ticks: {
                callback: function(value, index, values) {
                    return '₹' + value;
                }
            }
        }
    }
};

const profitLineChart = new Chart(
    document.getElementById('profit'),
    {
        type: 'line',
        data: chartDataLineprofit,
        options: chartOptionsprofit
    }
);


//Op/nonOp Line graph
const chartDataOpline = {
        labels: monthsop,
        datasets: [
            {
                label: 'OpExpenses',
                data: operationalExpenses,
                fill: false,
                backgroundColor: '#668586',
                borderColor: '#668586',

                tension: 0.1
            },
            {
                label: 'nonOpExpense',
                data: nonOperationalExpenses,
                fill: false,
                backgroundColor: '#00c9ae',
                borderColor: '#00c9ae',

                tension: 0.1
            }
        ]
    };

    const opnonopchart = new Chart(
        document.getElementById('opnonopchart'),
        {
            type: 'line',
            data: chartDataOpline,
            options: chartOptions
        }
    );

</script>
</body>
</html>