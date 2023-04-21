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
    $sqlpie = "SELECT year, SUM(revenue) as revenue, SUM(expense) as expense FROM expenses WHERE department = '$selectedDepartment' GROUP BY year";
    $resultpie = $pdo->query($sqlpie);
    if ($resultpie->rowCount() > 0) {
        $revenuepie = array();
        $expensespie = array();
        $years = array();
        while ($row = $resultpie->fetch()) {
            $revenuepie[] = $row["revenue"];
            $expensespie[] = $row["expense"];
            $years[] = $row["year"];
        }
        unset($resultpie);
    } else {
        echo "No records matching your query were found.";
    }
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sqlpie. " . $e->getMessage());
}

try {
    $sql = "SELECT * FROM expenses WHERE year = $selectedYear AND department = '$selectedDepartment'";
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $revenue = array();
        $expenses = array();
        $months = array();
        while ($row = $result->fetch()) {
            $revenue[] = $row["revenue"];
            $expenses[] = $row["expense"];
            $months[] = $row["month"];
        }
        unset($result);
    } else {
        echo "No records matching your query were found.";
    }
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}

try {
    $sqldonut = "SELECT department, SUM(revenue - expense) as profit FROM expenses WHERE year = '$selectedYear' GROUP BY department";
    $resultdonut = $pdo->query($sqldonut);
    if ($resultdonut->rowCount() > 0) {
        $profitsdonut = array();
        $departmentsdonut = array();
        while ($row = $resultdonut->fetch()) {
            $profitsdonut[] = $row["profit"];
            $departmentsdonut[] = $row["department"];
        }
        unset($resultdonut);
    } else {
        echo "No records matching your query were found.";
    }
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sqldonut. " . $e->getMessage());
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
    <link href="style.css" rel="stylesheet" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
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
        .chartBox {
            /* width: 330px; */
            display: inline-block;
            /* margin-right: 20px; */
            /* margin-left: 80px; */
            align-items: center;
            padding-top: 20px;
            border-radius: 20px;
            border: solid 1px #454545;
        }
        .linechart{
            width: 620px;
            /* margin-right: 80px; */
            margin-bottom: 0px;
            margin-top: 10px;
            margin-right: 20px;
            margin-left: 60px;
            padding-left: 20px;
            padding-right: 20px;
            
        }
        .chartBox.donut, .chartBox.pie {
            padding-left: 60px;
            padding-right: 60px;
            padding-bottom: 10px;
            padding-top: 20px;
            margin-left: 20px;
        }

      
    </style>
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
        <li class="active ">
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
        <div class="chartBox linechart">
            <canvas id="revenueExpenseLineChart"></canvas>
        </div>
        <div class="chartBox pie">
            <canvas id="revenueExpenseChart"></canvas> 
        </div>
        <div class="chartBox linechart">
            <canvas id="opnonopchart"></canvas> 
        </div>

        <div class="chartBox  donut">
            <canvas id="profitChart"></canvas>
        </div>

        </div>
        </div>
    </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Pie chart
const revenuepie = <?php echo json_encode($revenuepie); ?>;  
const expensespie = <?php echo json_encode($expensespie); ?>;
const years = <?php echo json_encode($years); ?>;
// Line chart
const revenueLine = <?php echo json_encode($revenue); ?>;  
const expensesLine = <?php echo json_encode($expenses); ?>;
const months = <?php echo json_encode($months); ?>;
// Donut Chart
const profits = <?php echo json_encode($profitsdonut); ?>;
const departments = <?php echo json_encode($departmentsdonut); ?>;

//operationa/non line graph
const operationalExpenses = <?php echo json_encode($operationalExpenses); ?>;
const nonOperationalExpenses = <?php echo json_encode($nonOperationalExpenses); ?>;
const monthsop = <?php echo json_encode($monthsop); ?>;

// Pie chart 
const chartData = {
    labels: ['Revenue', 'Expense'],
    datasets: [
        {
            label: 'Revenue vs Expense',
            data: [revenuepie[0], expensespie[0]],
            backgroundColor: [
                // 'rgba(75, 192, 192, 0.2)',
                // 'rgba(255, 99, 132, 0.2)',
                '#FFCB91',
                '#B9EDDD',
            ],
            borderColor: [
                // 'FFBF9B',
                '#FFA931',
                '#35C7AD',
            ],
            borderWidth: 1
        },
    ]
};
const chartOptions = {
    scales: {
        y: {
            beginAtZero: true
        }
    }
};
const revenueExpenseChart = new Chart(
    document.getElementById('revenueExpenseChart'),
    {
        type: 'pie',
        data: chartData,
        options: chartOptions
    }
);
// Line chart
const chartDataLine = {
    labels: months,
    datasets: [
        {
            label: 'Revenue',
            data: revenueLine,
            fill: false,
            backgroundColor: '#FFCB91',
            borderColor: '#FFCB91',

            tension: 0.1
        },
        {
            label: 'Expense',
            data: expensesLine,
            fill: false,
            backgroundColor: '#B9EDDD',
            borderColor: '#6FD0C3',

            tension: 0.1
        }
    ]
};
const revenueExpenseLineChart = new Chart(
    document.getElementById('revenueExpenseLineChart'),
    {
        type: 'line',
        data: chartDataLine,
        options: chartOptions
    }
);

//Donut Chart
const chartDatadonut = {
        labels: departments,
        datasets: [
        {
            label: 'Profit',
            data: profits,
            backgroundColor: [
            // '#FAD8FF',//pink
            // '#C2BBF0',//purpple
            // '#8FB8ED',//blue
            '#FFCB91',
            '#FFEFA1',
            '#97DECE',
            ],
            borderColor: [
            // '#F6B9FF',
            // '#A091FF',
            // '#5CA2FF',
            '#F4B065',
            '#ECD45E',
            '#74a19f',
            ],
            borderWidth: 1
        },
    ]
};

const profitChart = new Chart(
    document.getElementById('profitChart'),
    {
        type: 'doughnut',
        data: chartDatadonut,
        options: chartOptions
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