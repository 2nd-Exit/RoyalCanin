<?php
require 'db.php';

// CREATE
if (isset($_POST['create'])) {
    $name = $_POST['foodName'];
    $type = $_POST['foodType'];
    $exp = $_POST['expirationDate'];
    $qty = $_POST['quantityAvailable'];
    $price = $_POST['price'];
    $stmt = $conn->prepare("INSERT INTO FOOD (foodName, foodType, expirationDate, quantityAvailable, price) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssdi", $name, $type, $exp, $qty, $price);
    $stmt->execute();
}

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM FOOD WHERE foodID=$id");
    header("Location: admin.php?entity=food");
    exit;
}

// FETCH
$result = $conn->query("SELECT * FROM FOOD ORDER BY foodID DESC");
?>

<h2 class="text-2xl font-semibold mb-4">Food Inventory</h2>

<form method="POST" class="bg-gray-50 p-4 rounded mb-6 grid grid-cols-2 gap-4">
    <div>
        <label class="block mb-1 font-semibold">Food Name</label>
        <input type="text" name="foodName" required class="border p-2 rounded w-full">
    </div>
    <div>
        <label class="block mb-1 font-semibold">Type</label>
        <input type="text" name="foodType" required class="border p-2 rounded w-full">
    </div>
    <div>
        <label class="block mb-1 font-semibold">Expiration Date</label>
        <input type="date" name="expirationDate" required class="border p-2 rounded w-full">
    </div>
    <div>
        <label class="block mb-1 font-semibold">Quantity Available</label>
        <input type="number" name="quantityAvailable" required class="border p-2 rounded w-full">
    </div>
    <div>
        <label class="block mb-1 font-semibold">Price</label>
        <input type="number" step="0.01" name="price" required class="border p-2 rounded w-full">
    </div>
    <div class="col-span-2">
        <button name="create" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-2">Add Food</button>
    </div>
</form>

<table class="min-w-full border border-gray-300 bg-white">
    <thead class="bg-gray-200 text-center">
        <tr>
            <th class="border p-2">Food ID</th>
            <th class="border p-2">Name</th>
            <th class="border p-2">Type</th>
            <th class="border p-2">Expiration</th>
            <th class="border p-2">Quantity</th>
            <th class="border p-2">Price</th>
            <th class="border p-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr class="text-center hover:bg-gray-50">
            <td class="border p-2"><?= $row['foodID'] ?></td>
            <td class="border p-2"><?= htmlspecialchars($row['foodName']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($row['foodType']) ?></td>
            <td class="border p-2"><?= $row['expirationDate'] ?></td>
            <td class="border p-2"><?= $row['quantityAvailable'] ?></td>
            <td class="border p-2"><?= $row['price'] ?></td>
            <td class="border p-2">
                <a href="?entity=food&delete=<?= $row['foodID'] ?>" onclick="return confirm('Delete this food?')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
