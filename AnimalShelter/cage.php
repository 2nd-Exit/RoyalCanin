<?php
require 'db.php';

// CREATE
if (isset($_POST['create'])) {
    $zone = $_POST['locateZone'];
    $size = $_POST['size'];
    $status = $_POST['cageStatus'];
    $stmt = $conn->prepare("INSERT INTO CAGE (locateZone, size, cageStatus) VALUES (?,?,?)");
    $stmt->bind_param("sss", $zone, $size, $status);
    $stmt->execute();
}

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM CAGE WHERE cageID=$id");
    header("Location: admin.php?entity=cage");
    exit;
}

// FETCH
$result = $conn->query("SELECT * FROM CAGE ORDER BY cageID DESC");
?>

<h2 class="text-2xl font-semibold mb-4">Cage Manager</h2>

<form method="POST" class="bg-gray-50 p-4 rounded mb-6 grid grid-cols-3 gap-4">
    <div>
        <label class="block mb-1 font-semibold">Locate Zone</label>
        <input type="text" name="locateZone" required class="border p-2 rounded w-full">
    </div>
    <div>
        <label class="block mb-1 font-semibold">Size</label>
        <select name="size" class="border p-2 rounded w-full">
            <option value="">Select Size</option>
            <option value="XL">XL</option>
            <option value="L">L</option>
            <option value="M">M</option>
            <option value="S">S</option>
        </select>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Status</label>
        <select name="cageStatus" class="border p-2 rounded w-full">
            <option value="">Select Status</option>
            <option value="Available">Available</option>
            <option value="Occupied">Occupied</option>
        </select>
    </div>
    <div class="col-span-3">
        <button name="create" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-2">Add Cage</button>
    </div>
</form>

<table class="min-w-full border border-gray-300 bg-white">
    <thead class="bg-gray-200 text-center">
        <tr>
            <th class="border p-2">Cage ID</th>
            <th class="border p-2">Locate Zone</th>
            <th class="border p-2">Size</th>
            <th class="border p-2">Status</th>
            <th class="border p-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr class="text-center hover:bg-gray-50">
            <td class="border p-2"><?= $row['cageID'] ?></td>
            <td class="border p-2"><?= htmlspecialchars($row['locateZone']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($row['size']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($row['cageStatus']) ?></td>
            <td class="border p-2">
                <a href="?entity=cage&delete=<?= $row['cageID'] ?>" onclick="return confirm('Delete this cage?')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
