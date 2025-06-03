<?php
include 'db_connection.php'; // Ensure this file has the correct database connection details

function numeroExists($connection, $numero) {
    $stmt = $connection->prepare("SELECT COUNT(*) FROM impo WHERE Numero = ?");
    $stmt->bind_param('i', $numero);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_array();
    return $row[0] > 0;
}

$existingNumeros = [];

if (isset($_POST['submit'])) {
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        $file = fopen($_FILES['file']['tmp_name'], 'r');

        // Skip the header row if your CSV file has a header
        fgetcsv($file, 0, ";");

        // Prepare SQL query
        $stmt = $connection->prepare("INSERT INTO impo (Numero, Nom, Date, Superviseur, Preparateur, Nr_page, Statut, Date_liv, Revision, Date_rev, commentaire) VALUES (?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?)");

        // Read each line of the CSV
        while (($row = fgetcsv($file, 0, ";")) !== FALSE) {
            $numero = $row[0];

            // Check if Numero already exists
            if (!numeroExists($connection, $numero)) {
                // Merge FirstName and LastName into Nom
                $nom = $row[4] . ' ' . $row[5]; // Merging FirstName and LastName

                // Bind data from CSV file, using CURDATE() for the Date field
                $stmt->bind_param('isssisssss', $numero, $nom, $row[8], $row[9], $row[10], $row[11], $row[12], $row[13], $row[14], $row[15]);

                // Execute the query
                $stmt->execute();
            } else {
                // Add existing Numero to the list
                $existingNumeros[] = $numero;
            }
        }

        fclose($file);

        if (count($existingNumeros) > 0) {
            echo "The following Numero values already exist and were not imported: " . implode(", ", $existingNumeros) . "<br>";
        }
        echo "CSV file successfully imported!";
    } else {
        echo "Error: Please upload a CSV file.";
    }
} else {
    echo "No file submitted.";
}
?>
