<?php $pageTitle = "Dashboard!"; ?>
<?php
include 'db_connection.php';
include 'session.php';
include 'sidebar.php';
$privilege = $_SESSION['privilege'];
$query = "SELECT * FROM user WHERE  DATE(date) = CURDATE()";
$result = mysqli_query($connection, $query);
$birthdays = mysqli_fetch_all($result, MYSQLI_ASSOC);

// $currentDate = date('Y-m-d');

$startOfWeek = date('Y-m-d', strtotime('this week'));
$endOfWeek = date('Y-m-d', strtotime('this week +6 days'));

// Query to fetch the count of form submissions by each user for the current week
$query = "SELECT Name, COUNT(*) AS form_count FROM formulaire WHERE DATE(todays_date) BETWEEN '$startOfWeek' AND '$endOfWeek'";

// If the user is not an admin (privilege = 1) and has privilege level 3, add a WHERE clause to filter data for the current user
if ($privilege == 3) {
    $username = $_SESSION['username'];
    $query .= " AND username = '$username'";
}

$query .= " GROUP BY Name ORDER BY form_count DESC";

$result = mysqli_query($connection, $query);
$formData = mysqli_fetch_all($result, MYSQLI_ASSOC);

$firstDayOfMonth = date('Y-m-01');
$lastDayOfMonth = date('Y-m-t');

if ($privilege == 1 || $privilege == 3) {
    // If the user is not an admin (privilege = 1) and has privilege level 3, get the username
    if ($privilege == 3) {
        $username = $_SESSION['username'];
    }

    // Query to fetch the count of form submissions by each user for the current month
    $queryMonthly = "SELECT Name, COUNT(*) AS form_count FROM formulaire WHERE DATE(todays_date) BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth'";

    // If the user is not an admin (privilege = 1) and has privilege level 3, add a WHERE clause to filter data for the current user
    if ($privilege == 3) {
        $queryMonthly .= " AND username = '$username'";
    }

    $queryMonthly .= " GROUP BY Name ORDER BY form_count DESC";

    $resultMonthly = mysqli_query($connection, $queryMonthly);
    $formDataMonthly = mysqli_fetch_all($resultMonthly, MYSQLI_ASSOC);
}

// Query to fetch the count of dossiers added by each user for the current week
$queryWeekly = "SELECT Name, COUNT(*) AS dossier_count FROM formulaire  WHERE DATE(todays_date) BETWEEN '$startOfWeek' AND '$endOfWeek' GROUP BY Name ORDER BY dossier_count DESC";
$resultWeekly = mysqli_query($connection, $queryWeekly);
$formDataWeekly = mysqli_fetch_all($resultWeekly, MYSQLI_ASSOC);


$sql = "SELECT Name, COUNT(Id_formulaire) AS dossier_count FROM formulaire GROUP BY Work_hours";

$result = $connection->query($sql);

// Create arrays to store the data for the chart
$users = array();
$dossierCounts = array();

// Fetch data from the query result and store it in the arrays
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row["Name"];
        $dossierCounts[] = $row["dossier_count"];
    }
}



// Check if the date range and username are provided from the frontend (e.g., from a form submission)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["startDate"]) && isset($_POST["endDate"])) {
    $startDate = $_POST["startDate"];
    $endDate = $_POST["endDate"];
    $userName = $_POST["userName"]; // Selected username from the dropdown

    // Build the WHERE clause to filter data based on the entered username
    $whereClause = "";
    if ($userName != "All") {
        $whereClause = " AND Name = '$userName' ";
    }

    // SQL query to fetch the data for the first chart and get the number of unique folders and sum of Work_hours
    $query1 = "SELECT DATE(`Date`) AS `date`, Folder, SUM(Work_hours) AS total_work_hours 
               FROM formulaire 
               WHERE DATE(date) BETWEEN '$startDate' AND '$endDate' $whereClause
               GROUP BY Folder, `date`";

    $result1 = mysqli_query($connection, $query1);
    // $result2 = mysqli_query($connection, $query2);

    // Create arrays to store the data for both charts
    $chartData1 = array();
    $chartData2 = array();
	$uniqueFolders = array();
    $uniqueFolders1 = array(); // To store unique folders for the first chart
    $uniqueFolders2 = array(); // To store unique folders for the second chart
	$sumWorkHours = 0;
    $sumWorkHours1 = 0; // To calculate sum of Work_hours for the first chart
    $sumWorkHours2 = 0; // To calculate sum of Work_hours for the second chart

    // Process data for the first chart
    while ($row = mysqli_fetch_assoc($result1)) {
        $chartData1[] = array(
            "Date" => $row["date"],
            "Folder" => $row["Folder"],
            "Work_hours" => floatval($row["total_work_hours"]) // Convert the Work_hours to float for summing.
        );

        if (!in_array($row["Folder"], $uniqueFolders1)) {
            $uniqueFolders1[] = $row["Folder"];
        }

        $sumWorkHours1 += floatval($row["total_work_hours"]);
    }

    // Process data for the second chart
    while ($row = mysqli_fetch_assoc($result1)) {
        $chartData2[] = array(
            "Name" => $row["Name"],
            "folder_count" => $row["folder_count"],
            "total_work_hours" => floatval($row["total_work_hours"]) // Convert the Work_hours to float for summing.
        );

        $uniqueFolders2[] = $row["folder_count"];
        $sumWorkHours2 += floatval($row["total_work_hours"]);
    }
}

// Check if the date range and username are provided from the frontend (e.g., from a form submission)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["startDate"]) && isset($_POST["endDate"])) {
    $startDate = $_POST["startDate"];
    $endDate = $_POST["endDate"];
    $userName = $_POST["userName"]; // Optional username

    // Build the WHERE clause to filter data based on the entered username
    $whereClause = "";
    if ($userName != "All") {
        $whereClause = " AND Name = '$userName' ";
    }

    // SQL query to fetch the data for the second chart and get the number of unique folders and sum of Work_hours
    $query2 = "SELECT Name, COUNT(DISTINCT Folder) AS folder_count, SUM(Work_hours) AS total_work_hours 
               FROM formulaire 
               WHERE DATE(date) BETWEEN '$startDate' AND '$endDate' $whereClause
               GROUP BY Name";

    $result2 = mysqli_query($connection, $query2);
    $formData2 = mysqli_fetch_all($result2, MYSQLI_ASSOC);
} else {
    // Set $formData2 to an empty array if not defined
    $formData2 = array();
}

// Get the current user's name from the session
$currentUser = $_SESSION['username'];

// Get the first day and last day of the current month
$firstDayOfMonth = date('Y-m-01');
$lastDayOfMonth = date('Y-m-t');

// SQL query to fetch the count of folders submitted by the current user for the current month
$queryFoldersCurrentMonth = "SELECT Name, COUNT(DISTINCT Folder) AS folder_count 
                             FROM formulaire 
                             WHERE username = '$currentUser' AND DATE(todays_date) BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth'";

$resultFoldersCurrentMonth = mysqli_query($connection, $queryFoldersCurrentMonth);
$dataFoldersCurrentMonth = mysqli_fetch_assoc($resultFoldersCurrentMonth);
$currentUserFolderCount = (int) $dataFoldersCurrentMonth['folder_count'];

// SQL query to fetch the sum of Work_hours for each username for the current month
if ($privilege == 1) {
    $querySumHours = "SELECT Name, SUM(Work_hours) AS total_work_hours FROM formulaire WHERE DATE(todays_date) BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth' GROUP BY Name";
} else {
    $currentUsername = $_SESSION['username']; // Assuming you've stored the current username in the session.
    $querySumHours = "SELECT Name, SUM(Work_hours) AS total_work_hours FROM formulaire WHERE DATE(todays_date) BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth' AND username = '$currentUsername' GROUP BY Name";
}
$resultSumHours = mysqli_query($connection, $querySumHours);
$dataSumHours = mysqli_fetch_all($resultSumHours, MYSQLI_ASSOC);

$progressBarClasses = ['bg-red', 'bg-orange', 'bg-yellow', 'bg-green', 'bg-teal', 'bg-cyan', 'bg-blue', 'bg-indigo', 'bg-purple', 'bg-pink'];
?>
<div id="layoutSidenav_content">
                <main>
                    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
                        <div class="container-xl px-4">
                            <div class="page-header-content pt-4">
                                <div class="row align-items-center justify-content-between">
									<?php if($privilege == 1 || $privilege == 2 || $privilege == 3 ){ ?>
									<?php if(count($birthdays) > 0): ?>
									<div class="alert alert-info" role="alert"> <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
									<strong>Aujourd'hui c'est l'anniversaire de :</strong>
									<?php foreach($birthdays as $user): ?>
									<span><?php echo $user['Name']; ?></span>
									<?php endforeach; ?>
									</div>
									<?php endif; ?>
									<?php } ?>
                                    <div class="col-auto mt-4">
                                        <h1 class="page-header-title">
                                            <div class="page-header-icon"><i data-feather="activity"></i></div>
                                            Bienvenue sur le Dashboard!
                                        </h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>

            <div class="container-xl px-4 mt-n10">
            <?php if ($_SESSION['privilege'] == 1): ?>
            <div class="row">
                <div class="col-lg-8">
                <div class="card mb-4">
                <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Temps de travail des dossiers</h6>
                    </div>
                    <div class="card-body">
                    <form method="POST" action="" class="user">
                        <div class="input-group input-group-joined border-0" style="display: flex; border: 1px solid; gap: 10px; align-items: center;">
                                <input type="date" class="form-select" id="startDate" name="startDate" style="flex-grow: 1;">
                                <input type="date" class="form-select" id="endDate" name="endDate" style="flex-grow: 1;">
								<select id="userName" name="userName" class="form-select" style="flex-grow: 1;">
									<!-- <option value="" selected disabled>Select User</option> -->
									<option value="All">All</option> <!-- Added "All" option -->
									<?php
									// Fetch all users from the 'user' table
									$queryUsers = "SELECT Name FROM user";
									$resultUsers = mysqli_query($connection, $queryUsers);
									while ($userData = mysqli_fetch_assoc($resultUsers)) {
										$selected = ($_POST["userName"] == $userData['Name']) ? "selected" : "";
										echo "<option value='{$userData['Name']}' {$selected}>{$userData['Name']}</option>";
									}
									?>
								</select>
                                <button type="submit" class="btn btn-rounded btn-success">Entrer</button>
                        </div>
                    </form>
                        <canvas id="timeChart"></canvas>    
                    </div>
                <script>
                    // Get the PHP data from the backend
                    const chartData = <?php echo json_encode($chartData1); ?>;

                    // Process the data and calculate the sum of Work_hours for each Folder for each Date
                        const dataMap = new Map();

                        chartData.forEach((data) => {
                            const key = `${data.Date}-${data.Folder}`;
                            if (dataMap.has(key)) {
                                dataMap.set(key, dataMap.get(key) + data.Work_hours);
                            } else {
                                dataMap.set(key, data.Work_hours);
                            }
                        });

                        // Extract the unique folders and dates from the dataMap
                        const uniqueFolders = [...new Set(chartData.map((data) => data.Folder))];
                        const uniqueDates = [...new Set(chartData.map((data) => data.Date))];

                        // Create the chart dataset for each folder
                        const chartDatasets = uniqueFolders.map((folder) => ({
                            label: folder,
                            data: uniqueDates.map((date) => dataMap.get(`${date}-${folder}`) || 0), // If no data, set to 0
                            backgroundColor: getRandomColor(), // Generate random colors for each folder
                            borderWidth: 1,
                            //borderColor: getRandomColor(),
                        }));

                        // Create a random color generator for chart backgrounds
                        function getRandomColor() {
                            const letters = "0123456789ABCDEF";
                            let color = "#";
                            for (let i = 0; i < 6; i++) {
                                color += letters[Math.floor(Math.random() * 16)];
                            }
                            return color;
                        }

                        // Create the time chart using Chart.js
                        const ctx = document.getElementById("timeChart").getContext("2d");
                        new Chart(ctx, {
                            type: "line",
                            data: {
                                labels: uniqueDates,
                                datasets: chartDatasets,
                            },
                            options: {
                                scales: {
                                    x: {
                                        stacked: true,
                                    },
                                    y: {
                                        stacked: true,
                                        beginAtZero: true,
                                    },
                                },
                            },
                        });
                    </script>
                    
                </div>
                
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header">Total des heures travailler durant le mois</div>
                                <!-- Card Body -->
								<div class="card-body p-0">
                                   
                                    <div class="table-responsive table-billing-history">
										<table class="table mb-0">
											<tbody>
												<?php foreach ($dataSumHours as $entry) : ?>
													<tr>
														<td><?php echo $entry['Name']; ?></td>
														<td><?php echo $entry['total_work_hours']; ?></td>
													</tr>
												<?php endforeach; ?>
											</tbody>
                                        </table>
									</div>
                                </div>

                            </div>
                            <div class="card card-header-actions mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Pourcentage des formulaires soumis durant le mois</h6>
                                   
                                </div>
                                <!-- Card Body -->
                            <div class="card card-header-actions mb-4">
                                    <div id="piechart-monthly" ></div>
                            </div>
							<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var dataMonthly = google.visualization.arrayToDataTable([
        ['User', 'Form Count'],
        <?php
        foreach ($formDataMonthly as $data) {
            echo "['" . $data['Name'] . "', " . $data['form_count'] . "],";
        }
        ?>
    ]);

        var options = {
            title: 'Formulaires ajoutés ce mois-ci',
            is3D: true, 
            sliceVisibilityThreshold: 0.01,
        };
        var chartMonthly = new google.visualization.PieChart(document.getElementById('piechart-monthly'));
        chartMonthly.draw(dataMonthly, options);
    }
</script>
                            </div>
							<div class="card card-header-actions mb-4">
									<!-- Card Header - Dropdown -->
									<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
										<h6 class="m-0 font-weight-bold text-primary">Soumissions de formulaires durant la semaine</h6>
									</div>
									<!-- Card Body -->
									<div class="card card-header-actions mb-4">
											  
										<div class="card-body">
    <?php if (count($formData) > 0): ?>
        <?php foreach ($formData as $index => $data): ?>
            <h4 class="small font-weight-bold">
                <?php echo $data['Name']; ?>
                <span class="float-right"><?php echo $data['form_count']; ?> Formulaire(s)</span>
            </h4>
            <div class="progress mb-4">
                <div class="progress-bar <?php echo $progressBarClasses[$index % count($progressBarClasses)]; ?>" role="progressbar" style="width: <?php echo ($data['form_count'] / array_sum(array_column($formData, 'form_count'))) * 100; ?>%" aria-valuenow="<?php echo ($data['form_count'] / array_sum(array_column($formData, 'form_count'))) * 100; ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun formulaire soumis cette semaine.</p>
    <?php endif; ?>
</div>

									</div>
							</div>
                        
                </div>
                <!-- Pie Chart -->
                 <!-- Soumissions de dossiers -->
                 <div class="col-lg-4">
					<div class="card mb-4">
						<!-- Card Header - Dropdown -->
						<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
							<h6 class="m-0 font-weight-bold text-primary">Soumissions de dossiers</h6>
						</div>
						<!-- Card Body -->
						<div class="card-body">
							<?php if (count($formData2) > 0): ?>
								<?php foreach ($formData2 as $data): ?>
									<h4 class="small font-weight-bold">
										<a class="folder-link" href="#" data-user="<?php echo $data['Name']; ?>">
											<?php echo $data['Name']; ?>
										</a>
										<span class="float-right"><?php echo $data['folder_count']; ?> Dossier(s)</span>
									</h4>
									<ul class="folder-list" style="display:none;">
										<?php
										// Fetch and display the list of folders for the current user in the date range
										$queryFolders = "SELECT DISTINCT Folder FROM formulaire WHERE Name = '{$data['Name']}' AND DATE(date) BETWEEN '$startDate' AND '$endDate'";
										$resultFolders = mysqli_query($connection, $queryFolders);
										while ($folderData = mysqli_fetch_assoc($resultFolders)) {
											echo "<li>{$folderData['Folder']}</li>";
											}
										?>
									</ul>
								<?php endforeach; ?>
							<?php else: ?>
								<p>Aucune information disponible pour la plage de date sélectionnée.</p>
							<?php endif; ?>
						</div>
					</div>
					<div class="card mb-4">
						<!-- Card Header - Dropdown -->
						<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
							<h6 class="m-0 font-weight-bold text-primary">Utilisateurs qui n'ont pas soumis de dossiers</h6>
						</div>
						<!-- Card Body -->
						<div class="card-body">
								<ul id="usersWithoutFolders" class="list-unstyled">
									<?php
									// Query to fetch users who haven't submitted folders during the selected date range
									if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["startDate"]) && isset($_POST["endDate"])) {
										$startDate = $_POST["startDate"];
										$endDate = $_POST["endDate"];
										$queryUsersWithoutFolders = "
										SELECT DISTINCT Name
										FROM user
										WHERE Name NOT IN (
											SELECT DISTINCT Name
											FROM formulaire
											WHERE DATE(date) BETWEEN '$startDate' AND '$endDate'
										)
									";
									$resultUsersWithoutFolders = mysqli_query($connection, $queryUsersWithoutFolders);

									if (mysqli_num_rows($resultUsersWithoutFolders) > 0) {
										while ($userWithoutFolder = mysqli_fetch_assoc($resultUsersWithoutFolders)) {
											echo "<li>{$userWithoutFolder['Name']}</li>";
										}
									}
									 else {
										echo "<p>Tous les utilisateurs ont soumis des dossiers durant la plage de dates sélectionnée.</p>";

									}
								}
								else {
									echo "<p>Aucune information disponible pour la plage de date sélectionnée.</p>";

								}
									?>
								</ul>
						</div>
						</div>


<script>
    // JavaScript to handle link click and toggle folder list visibility
    const folderLinks = document.querySelectorAll('.folder-link');
    folderLinks.forEach(link => {
        link.addEventListener('click', (event) => {
            event.preventDefault(); // Prevent the default link behavior
            const user = link.getAttribute('data-user');
            const folderList = link.parentElement.nextElementSibling;
            folderList.style.display = (folderList.style.display === 'none') ? 'block' : 'none';
        });
    });
</script>


            </div>
			</div>

					
                <?php elseif ($_SESSION['privilege'] == 3): ?>
                    <div class="row">
                    <!-- Progress Bar Chart - Current User's Folders -->
                    <div class="col">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header">Total des heures travailler durant le mois</div>
                                <!-- Card Body -->
								<div class="card-body p-0">
                                   
                                    <div class="table-responsive table-billing-history">
										<table class="table mb-0">
											<tbody>
												<tr>
												<?php foreach ($dataSumHours as $entry) : ?>
													<td><?php echo $entry['Name']; ?></td>
													<td><?php echo $entry['total_work_hours']; ?></td>
												<?php endforeach; ?>
												</tr>
											</tbody>
                                        </table>
									</div>
                                </div>

                            </div>
                        </div>
                        </div>
					<div class="col">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                            <h6 class="m-0 font-weight-bold text-primary">Dossiers soumis durant le mois</h6>
                                        </div>
                                        <div class="card-body">
                                        <h4 class="small font-weight-bold">
                                            <label class="folder-link" for="folderCheckbox"><?php echo $dataFoldersCurrentMonth['Name']; ?></label>
                                            <span class="float-right"><?php echo $currentUserFolderCount; ?> Dossier(s)</span>
                                        </h4>
                                        <input type="checkbox" id="folderCheckbox" style="display: none;">
                                        <ul class="folder-list">
                                            <?php
                                            // Fetch and display the list of folders for the current user in the current month
                                            $queryUserFolders = "SELECT DISTINCT Folder FROM formulaire WHERE username = '$currentUser' AND DATE(todays_date) BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth'";
                                            $resultUserFolders = mysqli_query($connection, $queryUserFolders);
                                            while ($folderData = mysqli_fetch_assoc($resultUserFolders)) {
                                                echo "<li>{$folderData['Folder']}</li>";
                                            }
                                            ?>
                                        </ul>

                                        </div>
                                    </div>
                                </div>
                    <div class="col">
                            <div class="card mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Soumissions de formulaires durant la semaine</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card card-header-actions mb-4">
                                          
									<div class="card-body">
                            <?php if (count($formData) > 0): ?>
                                <?php foreach ($formData as $data): ?>
                                    <h4 class="small font-weight-bold">
                                        <?php echo $data['Name']; ?>
                                        <span class="float-right"><?php echo $data['form_count']; ?></span>
                                    </h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo ($data['form_count'] / count($formData)) * 100; ?>%" aria-valuenow="<?php echo ($data['form_count'] / count($formData)) * 100; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Aucun formulaire soumis cette semaine.</p>
                            <?php endif; ?>
                        </div>
                        </div>
                            </div>
                            </div>
                            </div>
                            <div class="row">
                                
                            </div>
                        <?php endif; ?> 
                    </div>
					</div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function () {
        // Initialize date range picker
        $('#litepickerRangePlugin').litepickerRangePlugin({
            opens: 'left',
            startDate: moment().subtract(6, 'days'),
            endDate: moment(),
            minDate: '01/01/2020',
            maxDate: moment(),
            locale: {
                format: 'MM/DD/YYYY' // Adjust the date format as needed
            }
        });
    });
</script>

<script src="bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<!-- Add this before your closing </body> tag, ensure you've loaded jQuery -->
<script>
    $(document).ready(function(){
        $(".close-btn").click(function(){
            $(".alert-warning").slideUp();
        });

        if ($(".alert-warning").length > 0) {
            $(".alert-warning").slideDown();
        }
    });
</script>
<?php
if (isset($uniqueFolders) && is_array($uniqueFolders)) {
    echo "<script>
            function updateLabels(uniqueFolders, sumWorkHours) {
                document.getElementById('uniqueFoldersLabel').innerHTML = uniqueFolders;
                document.getElementById('sumWorkHoursLabel').innerHTML = sumWorkHours;
            }
            updateLabels(" . count($uniqueFolders) . ", $sumWorkHours);
          </script>";
} else {
    // Handle the case where $uniqueFolders is not set or not an array
    echo "<script>
            function updateLabels(uniqueFolders, sumWorkHours) {
                document.getElementById('uniqueFoldersLabel').innerHTML = 'N/A'; // Or some default value
                document.getElementById('sumWorkHoursLabel').innerHTML = sumWorkHours;
            }
          </script>";
}
?>

<script>
    // JavaScript to handle link click and toggle folder list visibility
    const folderLinks = document.querySelectorAll('.folder-link');
    folderLinks.forEach(link => {
        link.addEventListener('click', (event) => {
            event.preventDefault(); // Prevent the default link behavior
            const user = link.getAttribute('data-user');
            const folderList = link.parentElement.nextElementSibling;
            folderList.style.display = (folderList.style.display === 'none') ? 'block' : 'none';
        });
    });
</script>

</body>
</html>
