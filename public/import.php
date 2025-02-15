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
    die("Error connecting to the database: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $filePath = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($filePath, 'r');
        
        // Ignore the first 14 rows (metadata)
        for ($i = 0; $i < 14; $i++) {
            fgetcsv($handle);
        }

        try {
            // Read the header row (years)
            $headerRow = fgetcsv($handle);
            $years = array_slice($headerRow, 1); // Ignore the first column (prefectures)

            // Disable autocommit for performance improvement
            $pdo->beginTransaction();

            while (($data = fgetcsv($handle, 0, ',')) !== false) {
                // Convert from Shift-JIS to UTF-8
                $data = array_map(function ($value) {
                    return mb_convert_encoding($value, 'UTF-8', 'SJIS-win');
                }, $data);

                // The first column is the prefecture
                $prefecture = trim($data[0]);
                // Validate prefecture
                if (empty($prefecture) || !is_string($prefecture) || !preg_match('/^[0-9\p{Hiragana}\p{Katakana}\p{Han}\s\.,\-_]+$/u', $prefecture)) {
                    throw new Exception('Invalid prefecture.');
                }

                // The following columns are the population values for each year
                $populationValues = array_slice($data, 1);

                foreach ($populationValues as $index => $population) {
                    $year = (int)$years[$index]; // Corresponding year
                    // Validate year
                    if (!is_int($year) || $year < 1900 || $year > date('Y') || !preg_match('/^\d{4}$/', $year)) {
                        throw new Exception('Invalid year.');
                    }
                    $population = (int)$population; // Population
                    // Validate population
                    if ($population < 0 || !preg_match('/^\d+$/', $population)) {
                        throw new Exception('Invalid population value.');
                    }

                    // Sanitize data
                    $prefecture = filter_var($prefecture, FILTER_SANITIZE_STRING);
                    $year = filter_var($year, FILTER_SANITIZE_NUMBER_INT);
                    $population = filter_var($population, FILTER_SANITIZE_NUMBER_INT);

                    // Insert data into the database
                    $stmt = $pdo->prepare("INSERT INTO population_data (prefecture, year, population) VALUES (?, ?, ?)");
                    $stmt->execute([$prefecture, $year, $population]);
                }
            }

            // Commit the transaction
            $pdo->commit();
            echo "Data imported successfully.";
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $pdo->rollBack();
            echo "Error importing data: " . $e->getMessage();
        }

        fclose($handle);
    } else {
        echo "Error uploading the file.";
    }
}
?>