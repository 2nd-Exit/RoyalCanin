<?php
require 'db.php';

// CREATE
if (isset($_POST['create'])) {
    $name = trim($_POST['deptName']);
    if ($name !== '') {
        $stmt = $conn->prepare("INSERT INTO DEPARTMENT (deptName) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
    }
}

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM DEPARTMENT WHERE deptID=$id");
    header("Location: admin.php?entity=department");
    exit;
}

// FETCH
$result = $conn->query("SELECT * FROM DEPARTMENT ORDER BY deptID DESC");
?>

<h2 class="text-2xl font-semibold mb-4">Department Manager</h2>

<form method="POST" class="mb-6 bg-gray-50 p-4 rounded space-y-2">
    <label class="block font-semibold">Department Name</label>
    <input type="text" name="deptName" required class="border p-2 rounded w-full">
    <button name="create" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Department</button>
</form>

<table class="min-w-full border border-gray-300 bg-white">
    <thead class="bg-gray-200">
        <tr>
            <th class="border p-2">Dept ID</th>
            <th class="border p-2">Name</th>
            <th class="border p-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr class="hover:bg-gray-50 text-center">
            <td class="border p-2"><?= $row['deptID'] ?></td>
            <td class="border p-2"><?= htmlspecialchars($row['deptName']) ?></td>
            <td class="border p-2">
                <a href="?entity=department&delete=<?= $row['deptID'] ?>" onclick="return confirm('Delete this department?')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
