<?php
// Load environment variables
$env = parse_ini_file('.env');
foreach ($env as $key => $value) {
    $_ENV[$key] = $value;
}

require_once 'config/Database.php';
use Config\Database;

// Get database connection
$pdo = Database::getInstance()->getConnection();

// Check if status column exists
$stmt = $pdo->query("
    SELECT column_name 
    FROM information_schema.columns 
    WHERE table_name = 'service' AND column_name = 'status'
");

$hasStatusColumn = ($stmt->rowCount() > 0);
echo "Status column exists: " . ($hasStatusColumn ? 'Yes' : 'No') . "\n";

// Check if service_img column exists
$stmt = $pdo->query("
    SELECT column_name 
    FROM information_schema.columns 
    WHERE table_name = 'service' AND column_name = 'service_img'
");

$hasImageColumn = ($stmt->rowCount() > 0);
echo "Service_img column exists: " . ($hasImageColumn ? 'Yes' : 'No') . "\n";

// Check if updated_at column exists
$stmt = $pdo->query("
    SELECT column_name 
    FROM information_schema.columns 
    WHERE table_name = 'service' AND column_name = 'updated_at'
");

$hasUpdatedAtColumn = ($stmt->rowCount() > 0);
echo "Updated_at column exists: " . ($hasUpdatedAtColumn ? 'Yes' : 'No') . "\n";

// Add missing columns if needed
if (!$hasStatusColumn) {
    echo "Adding status column...\n";
    try {
        $pdo->exec("ALTER TABLE service ADD COLUMN status VARCHAR(20) DEFAULT 'ACTIVE'");
        echo "Status column added successfully!\n";
    } catch (PDOException $e) {
        echo "Error adding status column: " . $e->getMessage() . "\n";
    }
}

if (!$hasImageColumn) {
    echo "Adding service_img column...\n";
    try {
        $pdo->exec("ALTER TABLE service ADD COLUMN service_img VARCHAR(255) DEFAULT '/assets/images/services/default.png'");
        echo "Service_img column added successfully!\n";
    } catch (PDOException $e) {
        echo "Error adding service_img column: " . $e->getMessage() . "\n";
    }
}

if (!$hasUpdatedAtColumn) {
    echo "Adding updated_at column...\n";
    try {
        $pdo->exec("ALTER TABLE service ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        echo "Updated_at column added successfully!\n";
    } catch (PDOException $e) {
        echo "Error adding updated_at column: " . $e->getMessage() . "\n";
    }
}

// Create default.png if it doesn't exist
$defaultImagePath = __DIR__ . '/public/assets/images/services/default.png';
if (!file_exists($defaultImagePath)) {
    echo "Creating default image...\n";
    
    // Check if directory exists
    $imageDir = __DIR__ . '/public/assets/images/services';
    if (!is_dir($imageDir)) {
        mkdir($imageDir, 0755, true);
        echo "Created services directory\n";
    }
    
    // Copy paw.png as default.png
    $pawImagePath = __DIR__ . '/public/assets/images/paw.png';
    if (file_exists($pawImagePath)) {
        copy($pawImagePath, $defaultImagePath);
        echo "Created default.png from paw.png\n";
    } else {
        echo "Warning: paw.png not found, cannot create default image\n";
    }
}

echo "Script completed.\n"; 