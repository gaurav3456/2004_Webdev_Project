<?php
$host = 'localhost'; 
$dbname = 'op_expenses';
$username = 'root';
$password = '';

// Create a new PDO object and set the error mode to exceptions
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Get a list of distinct years and departments from the expenses table
try {
    $years = $pdo->query("SELECT DISTINCT year FROM expenses ORDER BY year ASC")->fetchAll(PDO::FETCH_COLUMN);
    $departments = $pdo->query("SELECT DISTINCT department FROM expenses ORDER BY department ASC")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("ERROR: Could not able to execute query. " . $e->getMessage()); 
}

// If there is no data in the table, redirect to the index page
if (empty($years) || empty($departments)) {
    echo "No data found. Please add some data to the expenses table.";
    header("Location: ./index.php");
    return false;
}

// Get the year selected by the user, or use the first year by default
$selectedYear = isset($_GET['year']) ? $_GET['year'] : $years[0];
// $selectedDepartment = isset($_GET['department']) ? $_GET['department'] : $departments[0];

// Query the database to get the total revenue and expense for the selected year
try {
    $sqlpie = "SELECT SUM(revenue) as revenue, SUM(expense) as expense FROM expenses WHERE year = '$selectedYear'";
    $resultpie = $pdo->query($sqlpie);
    if ($resultpie->rowCount() > 0) {
        $row = $resultpie->fetch();
        $revenuepie = array($row["revenue"]);
        $expensespie = array($row["expense"]);
    } else {
        echo "No records matching your query were found.";
    }
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sqlpie. " . $e->getMessage());
}

// Query the database to get the total revenue and expense for each month of the selected year
try {
    $sql = "SELECT month, SUM(revenue) as total_revenue, SUM(expense) as total_expense FROM expenses WHERE year = $selectedYear GROUP BY month";
    $result = $pdo->query($sql);

    // If any rows were returned, store the revenue, expense, and month values in separate arrays
    if ($result->rowCount() > 0) {
        $totalRevenue = array();
        $totalExpenses = array();
        $months = array();
        while ($row = $result->fetch()) {
            $totalRevenue[] = $row["total_revenue"];
            $totalExpenses[] = $row["total_expense"];
            $months[] = $row["month"];
        }
        unset($result);

        // Sort the months array in chronological order
        $timestamps = array_map('strtotime', $months);
        array_multisort($timestamps, $months, $totalRevenue, $totalExpenses);
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
            $profit = $row["profit"];
            if ($profit >= 0) { // filter out negative profit values
                $profitsdonut[] = $profit;
                $departmentsdonut[] = $row["department"];
            }
        }
        unset($resultdonut);
    } else {
        echo "No records matching your query were found.";
    }
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sqldonut. " . $e->getMessage());
}
$sql = "SELECT month, SUM(revenue) as total_revenue, SUM(expense) as total_expense FROM expenses WHERE year = $selectedYear GROUP BY month";

try {
    $sqlop = "SELECT month, SUM(operationalExpenses) as total_op_expenses, SUM(nonOperationalExpenses) as total_nonop_expenses FROM expenses WHERE year = $selectedYear GROUP BY month";
    $resultop = $pdo->query($sqlop);
    if ($resultop->rowCount() > 0) {
        $operationalExpenses = array();
        $nonOperationalExpenses = array();
        $monthsop = array();
        while ($row = $resultop->fetch()) {
            $operationalExpenses[] = $row["total_op_expenses"];
            $nonOperationalExpenses[] = $row["total_nonop_expenses"];
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

// convert the $revenuepie PHP variable into a JSON-encoded string.
// Get revenue and expense data from PHP
const revenuepie = <?php echo json_encode($revenuepie); ?>;
const expensespie = <?php echo json_encode($expensespie); ?>;
const selectedYear = '<?php echo $selectedYear; ?>';
// Line chart
const totalRevenueLine = <?php echo json_encode($totalRevenue); ?>;  
const totalExpensesLine = <?php echo json_encode($totalExpenses); ?>;
const months = <?php echo json_encode($months); ?>;
// Donut Chart
const profits = <?php echo json_encode($profitsdonut); ?>;
const departments = <?php echo json_encode($departmentsdonut); ?>;

//operationa/non line graph
const operationalExpenses = <?php echo json_encode($operationalExpenses); ?>;
const nonOperationalExpenses = <?php echo json_encode($nonOperationalExpenses); ?>;
const monthsop = <?php echo json_encode($monthsop); ?>;

// Pie chart data
const chartData = {
            labels: ['Revenue', 'Expense'],
            datasets: [
                {
                    label: `Revenue vs Expense (${selectedYear})`,
                    data: [revenuepie[0], expensespie[0]],
                    backgroundColor: [
                        '#FFCB91',
                        '#B9EDDD',
                    ],
                    borderColor: [
                        '#FFA931',
                        '#35C7AD',
                    ],
                    borderWidth: 1
                }
            ]
        };
        // Pie chart options

        const chartPieOptions = {
    scales: {
        y: {
            grid: {
                z: -1
            },
            ticks: {
                display: false
            }
        }
    }
};

const chartOptions = {
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function(value, index, values) {
                    return '₹' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
            },
            title: {
                display: true,
                text: 'Amount (in Rupees)'
            }
        }
    }
};

        // Create pie chart
        const revenueExpenseChart = new Chart(
            document.getElementById('revenueExpenseChart'),
            {
                type: 'pie',
                data: chartData,
                options: chartPieOptions
            }
        );
// Line chart
const chartDataLine = {
    labels: months,
    datasets: [
        {
            label: 'Total Revenue',
            data: totalRevenueLine,
            fill: false,
            backgroundColor: '#FFCB91',
            borderColor: '#FFCB91',
            tension: 0.1
        },
        {
            label: 'Total Expense',
            data: totalExpensesLine,
            fill: false,
            backgroundColor: '#6FD0C3',
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
            // label: `Revenue vs Expense (${selectedYear})`,
            label: `Profit (${selectedYear})`,
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
        options: chartPieOptions
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