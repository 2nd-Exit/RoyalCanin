<?php
require 'db.php';

// ===== HANDLE CREATE / UPDATE =====
if (isset($_POST['save'])) {
    $medName = $conn->real_escape_string($_POST['medName']);
    $medType = $conn->real_escape_string($_POST['medType']);
    $price = floatval($_POST['price']);

    if(!empty($_POST['medID'])) {
        $id = intval($_POST['medID']);
        $conn->query("UPDATE MEDICINE SET medName='$medName', medType='$medType', price=$price WHERE medID=$id");
    } else {
        $conn->query("INSERT INTO MEDICINE (medName, medType, price) VALUES ('$medName','$medType',$price)");
    }
    header("Location: admin.php?entity=medicine");
    exit;
}

// ===== HANDLE DELETE =====
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM MEDICINE WHERE medID=$id");
    header("Location: admin.php?entity=medicine");
    exit;
}

// ===== FETCH DATA =====
$medicines = $conn->query("SELECT * FROM MEDICINE ORDER BY medName");
?>
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Medicine Manager</h1>

    <!-- Add / Edit Form -->
    <form method="POST" class="grid grid-cols-3 gap-4 mb-6">
        <input type="hidden" name="medID" id="medID">
        <input type="text" name="medName" id="medName" placeholder="Medicine Name" required class="border p-2 rounded w-full">
        <input type="text" name="medType" id="medType" placeholder="Type" class="border p-2 rounded w-full">
        <input type="number" step="0.01" name="price" id="price" placeholder="Price" class="border p-2 rounded w-full">
        <button type="submit" name="save" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 col-span-3">Save</button>
    </form>

    <!-- Medicine Table -->
    <table class="w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200 text-center">
                <th class="border p-2">ID</th>
                <th class="border p-2">Name</th>
                <th class="border p-2">Type</th>
                <th class="border p-2">Price</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while($m = $medicines->fetch_assoc()): ?>
            <tr class="text-center">
                <td class="border p-2"><?= $m['medID'] ?></td>
                <td class="border p-2"><?= htmlspecialchars($m['medName']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($m['medType']) ?></td>
                <td class="border p-2"><?= $m['price'] ?></td>
                <td class="border p-2 space-x-2">
                    <button onclick="editMedicine(<?= $m['medID'] ?>,'<?= addslashes($m['medName']) ?>','<?= addslashes($m['medType']) ?>',<?= $m['price'] ?>)" class="bg-yellow-500 hover:bg-yellow-700 text-white px-2 py-1 rounded">Edit</button>
                    <a href="?delete=<?= $m['medID'] ?>" onclick="return confirm('Are you sure?')" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function editMedicine(id, name, type, price){
    document.getElementById('medID').value = id;
    document.getElementById('medName').value = name;
    document.getElementById('medType').value = type;
    document.getElementById('price').value = price;
}
</script>
