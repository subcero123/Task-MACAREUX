<?php
// Load environment variables
$dotenv = parse_ini_file(__DIR__ . '/../.env');

// Database connection using PDO
try {
    $pdo = new PDO(
        "mysql:host={$dotenv['DB_HOST']};dbname={$dotenv['DB_NAME']};charset=utf8mb4",
        $dotenv['DB_USER'],
        $dotenv['DB_PASSWORD']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error connecting to the database: " . htmlspecialchars($e->getMessage()));
}

if (isset($_GET['prefecture']) && isset($_GET['year'])) {
    // Sanitize inputs
    $prefecture = filter_var(trim($_GET['prefecture']), FILTER_SANITIZE_STRING);
    $year = filter_var(intval($_GET['year']), FILTER_SANITIZE_NUMBER_INT);

    try {
        // Query the population
        if ($prefecture === 'Total') {
            // Query for the total of all prefectures
            $stmt = $pdo->prepare("SELECT SUM(population) AS total_population FROM population_data WHERE year = ?");
            $stmt->execute([$year]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['total_population'] !== null) {
                echo "The total population in " . htmlspecialchars($year) . " is: " . number_format(htmlspecialchars($result['total_population']));
            } else {
                echo "No data found for the year " . htmlspecialchars($year) . ".";
            }
        } else {
            // Query for a specific prefecture
            $stmt = $pdo->prepare("SELECT population FROM population_data WHERE prefecture = ? AND year = ?");
            $stmt->execute([$prefecture, $year]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                echo "The population of " . htmlspecialchars($prefecture) . " in " . htmlspecialchars($year) . " is: " . number_format(htmlspecialchars($result['population']));
            } else {
                echo "No data found for " . htmlspecialchars($prefecture) . " in " . htmlspecialchars($year) . ".";
            }
        }
    } catch (Exception $e) {
        echo "Error querying data: " . htmlspecialchars($e->getMessage());
    }
} else {
    echo "Please select a valid prefecture and year.";
}
?>