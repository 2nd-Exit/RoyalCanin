<?php
require 'db.php';

// ===== HANDLE CREATE / UPDATE =====
if (isset($_POST['save'])) {
    $vacName = $conn->real_escape_string($_POST['vacName']);
    $vacType = $conn->real_escape_string($_POST['vacType']);
    $price = floatval($_POST['price']);

    if(!empty($_POST['vacID'])) {
        $id = intval($_POST['vacID']);
        $conn->query("UPDATE VACCINE SET vacName='$vacName', vacType='$vacType', price=$price WHERE vacID=$id");
    } else {
        $conn->query("INSERT INTO VACCINE (vacName, vacType, price) VALUES ('$vacName','$vacType',$price)");
    }
    header("Location: vaccine.php");
    exit;
}

// ===== HANDLE DELETE =====
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM VACCINE WHERE vacID=$id");
    header("Location: vaccine.php");
    exit;
}

// ===== FETCH DATA =====
$vaccines = $conn->query("SELECT * FROM VACCINE ORDER BY vacName");
?>
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Vaccine Manager</h1>

    <!-- Add / Edit Form -->
    <form method="POST" class="grid grid-cols-3 gap-4 mb-6">
        <input type="hidden" name="vacID" id="vacID">
        <input type="text" name="vacName" id="vacName" placeholder="Vaccine Name" required class="border p-2 rounded w-full">
        <input type="text" name="vacType" id="vacType" placeholder="Type" class="border p-2 rounded w-full">
        <input type="number" step="0.01" name="price" id="price" placeholder="Price" class="border p-2 rounded w-full">
        <button type="submit" name="save" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 col-span-3">Save</button>
    </form>

    <!-- Vaccine Table -->
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
        <?php while($v = $vaccines->fetch_assoc()): ?>
            <tr class="text-center">
                <td class="border p-2"><?= $v['vacID'] ?></td>
                <td class="border p-2"><?= htmlspecialchars($v['vacName']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($v['vacType']) ?></td>
                <td class="border p-2"><?= $v['price'] ?></td>
                <td class="border p-2 space-x-2">
                    <button onclick="editVaccine(<?= $v['vacID'] ?>,'<?= addslashes($v['vacName']) ?>','<?= addslashes($v['vacType']) ?>',<?= $v['price'] ?>)" class="bg-yellow-500 hover:bg-yellow-700 text-white px-2 py-1 rounded">Edit</button>
                    <a href="?delete=<?= $v['vacID'] ?>" onclick="return confirm('Are you sure?')" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function editVaccine(id, name, type, price){
    document.getElementById('vacID').value = id;
    document.getElementById('vacName').value = name;
    document.getElementById('vacType').value = type;
    document.getElementById('price').value = price;
}
</script>
