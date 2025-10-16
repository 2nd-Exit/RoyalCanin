<?php
require 'db.php';

// Handle Create
if (isset($_POST['create'])) {
    $clinicName = $_POST['clinicName'];
    $clinicAddress = $_POST['clinicAddress'];
    $clinicPhone = $_POST['clinicPhone'];

    $stmt = $conn->prepare("INSERT INTO CLINIC (clinicName, clinicAddress, clinicPhone) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $clinicName, $clinicAddress, $clinicPhone);
    $stmt->execute();
    header("Location: clinic.php");
    exit;
}

// Handle Edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editData = $conn->query("SELECT * FROM CLINIC WHERE clinicID=$id")->fetch_assoc();
}

// Handle Update
if (isset($_POST['update'])) {
    $id = $_POST['clinicID'];
    $clinicName = $_POST['clinicName'];
    $clinicAddress = $_POST['clinicAddress'];
    $clinicPhone = $_POST['clinicPhone'];

    $stmt = $conn->prepare("UPDATE CLINIC SET clinicName=?, clinicAddress=?, clinicPhone=? WHERE clinicID=?");
    $stmt->bind_param("sssi", $clinicName, $clinicAddress, $clinicPhone, $id);
    $stmt->execute();
    header("Location: clinic.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM CLINIC WHERE clinicID=$id");
    header("Location: clinic.php");
    exit;
}

// Handle Search
$search = $_GET['search'] ?? '';
$whereClause = '';
if ($search) {
    $s = $conn->real_escape_string($search);
    $whereClause = "WHERE clinicName LIKE '%$s%' OR clinicAddress LIKE '%$s%' OR clinicPhone LIKE '%$s%'";
}

// Fetch all clinics
$result = $conn->query("SELECT * FROM CLINIC $whereClause ORDER BY clinicName ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Clinic Manager</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Clinic Manager</h1>

    <!-- Search -->
    <form method="GET" class="mb-4">
        <input type="text" name="search" placeholder="Search clinics..." value="<?= htmlspecialchars($search) ?>" class="border p-2 rounded w-full">
    </form>

    <!-- Clinic Form -->
    <form method="POST" class="mb-6 space-y-4 bg-gray-50 p-4 rounded">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold">Clinic Name</label>
                <input type="text" name="clinicName" value="<?= $editData['clinicName'] ?? '' ?>" required class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block font-semibold">Phone</label>
                <input type="text" name="clinicPhone" value="<?= $editData['clinicPhone'] ?? '' ?>" required class="border p-2 rounded w-full">
            </div>
            <div class="col-span-2">
                <label class="block font-semibold">Address</label>
                <textarea name="clinicAddress" class="border p-2 rounded w-full"><?= $editData['clinicAddress'] ?? '' ?></textarea>
            </div>
        </div>

        <div class="mt-4">
            <?php if($editData): ?>
                <input type="hidden" name="clinicID" value="<?= $editData['clinicID'] ?>">
                <button type="submit" name="update" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Update Clinic</button>
                <a href="clinic.php" class="ml-2 text-gray-700">Cancel</a>
            <?php else: ?>
                <button type="submit" name="create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Clinic</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Clinic Table -->
    <table class="w-full table-auto border-collapse border border-gray-300 mt-6">
        <thead>
            <tr class="bg-gray-200 text-center">
                <th class="border p-2">Clinic Name</th>
                <th class="border p-2">Phone</th>
                <th class="border p-2">Address</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr class="text-center">
            <td class="border p-2"><?= $row['clinicName'] ?></td>
            <td class="border p-2"><?= $row['clinicPhone'] ?></td>
            <td class="border p-2"><?= $row['clinicAddress'] ?></td>
            <td class="border p-2">
                <a href="?edit=<?= $row['clinicID'] ?>" class="bg-yellow-400 hover:bg-yellow-600 text-white px-2 py-1 rounded">Edit</a>
                <a href="?delete=<?= $row['clinicID'] ?>" onclick="return confirm('Are you sure?')" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
