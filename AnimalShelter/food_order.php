<?php
require 'db.php';

// ===== FETCH DATA =====
$employees = $conn->query("
    SELECT e.employeeID, CONCAT(p.firstName, ' ', p.lastName) AS name
    FROM EMPLOYEE e
    JOIN PERSON p ON e.employeeID = p.personID
    ORDER BY p.firstName, p.lastName
");
$foods = $conn->query("SELECT foodID, foodName FROM FOOD ORDER BY foodName");

// ===== HANDLE CREATE =====
if (isset($_POST['createOrder'])) {
    $orderDate = $_POST['orderDate'];
    $quantity = intval($_POST['quantity']);
    $employeeID = intval($_POST['employeeID']);
    $foodID = intval($_POST['foodID']);

    // Optional: check if employee exists
    $empCheck = $conn->query("SELECT * FROM EMPLOYEE WHERE employeeID=$employeeID");
    if ($empCheck->num_rows === 0) {
        die("Error: Employee ID does not exist.");
    }

    $stmt = $conn->prepare("INSERT INTO FOOD_ORDER (orderDate, quantity, employeeID, foodID) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siii", $orderDate, $quantity, $employeeID, $foodID);
    $stmt->execute();

    header("Location: food_order.php");
    exit;
}

// ===== HANDLE DELETE =====
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM FOOD_ORDER WHERE orderID = $id");
    header("Location: food_order.php");
    exit;
}

// ===== FETCH ORDERS =====
$orders = $conn->query("
    SELECT fo.*, CONCAT(p.firstName,' ',p.lastName) AS employeeName, f.foodName
    FROM FOOD_ORDER fo
    JOIN EMPLOYEE e ON fo.employeeID = e.employeeID
    JOIN PERSON p ON e.employeeID = p.personID
    JOIN FOOD f ON fo.foodID = f.foodID
    ORDER BY fo.orderDate DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Order Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold mb-4">Food Order Manager</h2>

    <!-- Add Food Order Form -->
    <form method="POST" class="bg-gray-50 p-4 rounded mb-6 grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Order Date</label>
            <input type="date" name="orderDate" class="w-full border rounded p-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Employee ID</label>
            <input type="number" name="employeeID" class="w-full border rounded p-2" placeholder="Enter Employee ID" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Food ID</label>
            <input type="number" name="foodID" class="w-full border rounded p-2" placeholder="Enter Food ID" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Quantity</label>
            <input type="number" name="quantity" min="1" class="w-full border rounded p-2" placeholder="Enter Quantity" required>
        </div>
        <div class="md:col-span-2">
            <button type="submit" name="createOrder" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Save</button>
        </div>
    </form>

    <!-- Orders Table -->
    <table class="min-w-full border border-gray-300 bg-white">
    <thead class="bg-gray-200 text-center">
        <tr>
            <th class="border p-2">Order Date</th>
            <th class="border p-2">Employee</th>
            <th class="border p-2">Food</th>
            <th class="border p-2">Quantity</th>
            <th class="border p-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $orders->fetch_assoc()): ?>
            <tr class="text-center hover:bg-gray-50">
                <td class="border p-2"><?= $row['orderDate'] ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['employeeName']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['foodName']) ?></td>
                <td class="border p-2"><?= $row['quantity'] ?></td>
                <td class="border p-2">
                    <a href="?delete=<?= $row['orderID'] ?>" onclick="return confirm('Are you sure?')" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
    </table>
</div>

</body>
</html>
