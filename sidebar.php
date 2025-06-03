<?php
$userId = $_SESSION['id'];
$privilege = $_SESSION['privilege'];
$queryProfile = "SELECT Image,Name FROM user WHERE Id_user = '$userId'";
$resultProfile = mysqli_query($connection, $queryProfile);
$userProfile = mysqli_fetch_assoc($resultProfile);

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
  
    // Prepare the SQL statement to fetch the name and email (Login) from the 'user' table
    $stmt = $connection->prepare('SELECT name, Login FROM user WHERE login = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
  
    if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $sessionName = $row['name'];
      $emailAddress = $row['Login']; // Fetching the email address
    } else {
      // Handle case when username is not found in the 'user' table
      $sessionName = 'Unknown';
      $emailAddress = 'Unknown';
    }
  
    // Close the statement
    $stmt->close();
} else {
    // Handle case when session username is not set
    $sessionName = 'Unknown';
    $emailAddress = 'Unknown';
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title><?php echo $pageTitle; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
		<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js" crossorigin="anonymous"></script>
        <script src="js/litepicker.js"></script>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<!-- Include Moment.js -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.2/dist/chart.umd.min.js"></script>
		<!-- Include Date Range Picker CSS and JS -->
		<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
		<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script src="js/scripts.js"></script>
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.png" />
        <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="nav-fixed">
        <nav class="topnav navbar navbar-expand shadow justify-content-between justify-content-sm-start navbar-light bg-white" id="sidenavAccordion">
            <!-- Sidenav Toggle Button-->
            <button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 me-2 ms-lg-2 me-lg-0" id="sidebarToggle"><i data-feather="menu"></i></button>
            <!-- Navbar Brand-->
            <!-- * * Tip * * You can use text or an image for your navbar brand.-->
            <!-- * * * * * * When using an image, we recommend the SVG format.-->
            <!-- * * * * * * Dimensions: Maximum height: 32px, maximum width: 240px-->
            <a class="navbar-brand pe-3 ps-4 ps-lg-2" href="#!">Osourcing Sarl</a>
            <!-- Navbar Items-->
            <ul class="navbar-nav align-items-center ms-auto">
                <!-- User Dropdown-->
                <li class="nav-item dropdown no-caret dropdown-user me-3 me-lg-4">
                    <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?php if ($userProfile['Image']): ?>
                            <img class="img-fluid" src="<?php echo $userProfile['Image']; ?>" />
							<?php else: ?>
							<img class="img-fluid" src="img/default_user_image.jpg" />
							<?php endif; ?>
					</a>
                    <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
                        <h6 class="dropdown-header d-flex align-items-center">
							<?php if ($userProfile['Image']): ?>
                            <img class="dropdown-user-img" src="<?php echo $userProfile['Image']; ?>" />
							<?php else: ?>
							<img class="dropdown-user-img" src="img/default_user_image.jpg" />
							<?php endif; ?>
                            <div class="dropdown-user-details">
                                <div class="dropdown-user-details-name"><?php echo $sessionName; ?></div>
                                <div class="dropdown-user-details-email"><?php echo htmlspecialchars($emailAddress); ?></div>
                            </div>
                        </h6>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="profil.php">
                            <div class="dropdown-item-icon"><i data-feather="settings"></i></div>
                            Profil
                        </a>
                        <a class="dropdown-item" href="logout.php">
                            <div class="dropdown-item-icon"><i data-feather="log-out"></i></div>
                            Se déconnecter
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sidenav shadow-right sidenav-light">
                    <div class="sidenav-menu">
                        <div class="nav accordion" id="accordionSidenav">
                            <!-- Sidenav Menu Heading (Account)-->
                            <!-- * * Note: * * Visible only on and above the sm breakpoint-->
                            <div class="sidenav-menu-heading d-sm-none">Account</div>
							<div class="sidenav-menu-heading"></div>
                            <!-- Sidenav Link (Alerts)-->
                            <!-- Sidenav Accordion (Dashboard)-->
							<?php if ($privilege == 1 || $privilege == 3): ?>
                        <a class="nav-link collapsed" href="index.php" >
                                <div class="nav-link-icon"><i data-feather="activity"></i></div>Dashboards</a>
                        <?php endif; ?>
                        <?php if ($privilege == 1): ?>
                            <!-- Sidenav Accordion (Pages)-->
								<a class="nav-link collapsed" href="forms.php">
                                <div class="nav-link-icon"><i data-feather="plus-square"></i></div>Ajouter un formulaire</a>
								<a class="nav-link collapsed" href="leave_request.php">
                                <div class="nav-link-icon"><i data-feather="plus-square"></i></div>Demande de conge/autorisation</a>
								<a class="nav-link collapsed" href="displayform.php" >
                                <div class="nav-link-icon"><i data-feather="list"></i></div>Liste des formulaires</a>
								<a class="nav-link collapsed" href="User.php" >
                                <div class="nav-link-icon"><i data-feather="user-check"></i></div>Ajouter un utilisateur</a>
								<a class="nav-link collapsed" href="displayuser.php" >
                                <div class="nav-link-icon"><i data-feather="users"></i></div>Modifier un utilisateur</a>
								<a class="nav-link collapsed" href="additional.php" >
                                <div class="nav-link-icon"><i data-feather="user"></i></div>Ajouter un associé</a>
								<a class="nav-link collapsed" href="add_folder.php" >
                                <div class="nav-link-icon"><i data-feather="folder"></i></div>Ajouter un dossier</a>
								<a class="nav-link collapsed" href="edit_folder.php" >
                                <div class="nav-link-icon"><i data-feather="edit"></i></div>Modifier un dossier</a>
								<a class="nav-link collapsed" href="suivi.php" >
                                <div class="nav-link-icon"><i data-feather="table"></i></div>Suivi des dossiers</a>
								<a class="nav-link collapsed" href="list_folders.php" >
                                <div class="nav-link-icon"><i data-feather="folder"></i></div>Liste des dossiers</a>
								<a class="nav-link collapsed" href="add_task.php" >
                                <div class="nav-link-icon"><i data-feather="folder-plus"></i></div>Ajouter une tâche</a>
								<a class="nav-link collapsed" href="add_bureau.php" >
                                <div class="nav-link-icon"><i data-feather="plus-circle"></i></div>Ajouter un bureau</a>
								<a class="nav-link collapsed" href="add_infos.php" >
                                <div class="nav-link-icon"><i data-feather="file-text"></i></div>Ajouter information</a>
								<a class="nav-link collapsed" href="edit_bureau.php" >
                                <div class="nav-link-icon"><i data-feather="file"></i></div>Modifier un bureau</a>
								<a class="nav-link collapsed" href="cloture.php" >
                                <div class="nav-link-icon"><i data-feather="wifi-off"></i></div>Clôturer la période</a>
						<?php endif; ?>
                        <?php if ($privilege == 2): ?>
                            	
								<a class="nav-link collapsed" href="forms.php">
                                <div class="nav-link-icon"><i data-feather="plus-square"></i></div>Ajouter un formulaire</a>
								<a class="nav-link collapsed" href="displayform.php" >
								<a class="nav-link collapsed" href="leave_request.php">
                                <div class="nav-link-icon"><i data-feather="plus-square"></i></div>Demande de conge/autorisation</a>
                                <div class="nav-link-icon"><i data-feather="list"></i></div>Liste des formulaires</a>
								<a class="nav-link collapsed" href="add_folder.php" >
                                <div class="nav-link-icon"><i data-feather="folder"></i></div>Ajouter un dossier</a>
								<a class="nav-link collapsed" href="edit_folder.php" >
                                <div class="nav-link-icon"><i data-feather="edit"></i></div>Modifier un dossier</a>
								<a class="nav-link collapsed" href="suivi.php" >
                                <div class="nav-link-icon"><i data-feather="table"></i></div>Suivi des dossiers</a>
								<a class="nav-link collapsed" href="list_folders.php" >
                                <div class="nav-link-icon"><i data-feather="folder"></i></div>Liste des dossiers</a>
                        <?php endif; ?>
						<?php if ($privilege == 3): ?>
								<a class="nav-link collapsed" href="forms.php" >
                                <div class="nav-link-icon"><i data-feather="plus-square"></i></div>Ajouter un formulaire</a>
                                <a class="nav-link collapsed" href="leave_request.php">
                                <div class="nav-link-icon"><i data-feather="plus-square"></i></div>Demande de conge/autorisation</a>
								<a class="nav-link collapsed" href="displayform.php" >
                                <div class="nav-link-icon"><i data-feather="list"></i></div>Liste des formulaires</a>
								<a class="nav-link collapsed" href="list_folders.php" >
                                <div class="nav-link-icon"><i data-feather="folder"></i></div>Liste des dossiers</a>
						<?php endif; ?>
						</div>
                    </div>
                    <!-- Sidenav Footer-->
                    <div class="sidenav-footer">
                        <div class="sidenav-footer-content">
                            <div class="sidenav-footer-subtitle">Connecté en tant que:</div>
                            <div class="sidenav-footer-title"><?php echo $sessionName; ?></div>
                        </div>
                    </div>
                </nav>
            </div>
			
