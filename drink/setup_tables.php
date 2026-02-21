<?php
include "../config/db.php";

// 1. Create tbDrink table (if missing)
$sql1 = "CREATE TABLE IF NOT EXISTS tbDrink (
    DrinkID INT AUTO_INCREMENT PRIMARY KEY,
    DrinkName VARCHAR(100) NOT NULL,
    Price DECIMAL(10,2) NOT NULL
);";

if ($conn->query($sql1) === TRUE) {
    echo "<p>✅ Table 'tbDrink' checked/created.</p>";
    
    // --- FIX FOR MISSING COLUMNS ---
    // Check for DrinkName
    $res = $conn->query("SHOW COLUMNS FROM tbDrink LIKE 'DrinkName'");
    if($res->num_rows == 0) {
        // Check if 'Beverage' exists (from potential confusion with tbBeverage)
        $checkBev = $conn->query("SHOW COLUMNS FROM tbDrink LIKE 'Beverage'");
        if($checkBev->num_rows > 0) {
            $conn->query("ALTER TABLE tbDrink CHANGE Beverage DrinkName VARCHAR(100) NOT NULL");
            echo "<p>🔧 Fixed: Renamed column 'Beverage' to 'DrinkName'.</p>";
        } else {
            $conn->query("ALTER TABLE tbDrink ADD DrinkName VARCHAR(100) NOT NULL AFTER DrinkID");
            echo "<p>🔧 Fixed: Added missing column 'DrinkName'.</p>";
        }
    }

    // Check for Price
    $res = $conn->query("SHOW COLUMNS FROM tbDrink LIKE 'Price'");
    if($res->num_rows == 0) {
        // Check if 'SaleUnitPrice' exists
        $checkPrice = $conn->query("SHOW COLUMNS FROM tbDrink LIKE 'SaleUnitPrice'");
        if($checkPrice->num_rows > 0) {
            $conn->query("ALTER TABLE tbDrink CHANGE SaleUnitPrice Price DECIMAL(10,2) NOT NULL");
            echo "<p>🔧 Fixed: Renamed column 'SaleUnitPrice' to 'Price'.</p>";
        } else {
            $conn->query("ALTER TABLE tbDrink ADD Price DECIMAL(10,2) NOT NULL");
            echo "<p>🔧 Fixed: Added missing column 'Price'.</p>";
        }
    }
    // -------------------------------
}

// 2. Create tbDrinkOrder table (The one causing the error)
$sql2 = "CREATE TABLE IF NOT EXISTS tbDrinkOrder (
    OrderID INT AUTO_INCREMENT PRIMARY KEY,
    CheckInID INT NOT NULL,
    DrinkID INT NOT NULL,
    Qty INT NOT NULL,
    Amount DECIMAL(10,2) NOT NULL,
    OrderDate DATETIME NOT NULL
);";

if ($conn->query($sql2) === TRUE) {
    echo "<h3>✅ Table 'tbDrinkOrder' created successfully!</h3>";
    echo "<p>You can now use the Drink module.</p>";
    echo "<a href='report.php'>Go to Drink Report</a>";
} else {
    echo "<h3>❌ Error creating table: " . $conn->error . "</h3>";
}
?>