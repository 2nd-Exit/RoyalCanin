<?php
require 'db.php';

// Handle Add/Edit form submission
if (isset($_POST['save'])) {
    $vetID = $_POST['vetID'] ?: null;
    $vet_license_no = $_POST['vet_license_no'];
    $specialization = $_POST['specialization'];
    $years_of_experience = $_POST['years_of_experience'];
    $clinicID = $_POST['clinicID'];

    if ($vetID) {
        // Update existing vet
        $stmt = $conn->prepare("UPDATE VET SET vet_license_no=?, specialization=?, years_of_experience=?, clinicID=? WHERE vetID=?");
        $stmt->bind_param("ssiii", $vet_license_no, $specialization, $years_of_experience, $clinicID, $vetID);
        $stmt->execute();
    } else {
        // Insert new vet
        $stmt = $conn->prepare("INSERT INTO VET (vetID, vet_license_no, specialization, years_of_experience, clinicID) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issii", $vetID, $vet_license_no, $specialization, $years_of_experience, $clinicID);
        $stmt->execute();
    }
    header("Location: vet_crud.php");
}

// Handle Delete
if (isset($_GET['delete'])) {
    $vetID = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM VET WHERE vetID=?");
    $stmt->bind_param("i", $vetID);
    $stmt->execute();
    header("Location: vet_crud.php");
}

// Handle Edit form loading
$editVet = null;
if (isset($_GET['edit'])) {
    $vetID = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM VET WHERE vetID=?");
    $stmt->bind_param("i", $vetID);
    $stmt->execute();
    $result = $stmt->get_result();
    $editVet = $result->fetch_assoc();
}

// Fetch all vets
$vets = $conn->query("SELECT VET.*, PERSON.firstName, PERSON.lastName, CLINIC.clinicName FROM VET 
                      JOIN PERSON ON VET.vetID = PERSON.personID 
                      JOIN CLINIC ON VET.clinicID = CLINIC.clinicID")->fetch_all(MYSQLI_ASSOC);

// Fetch all persons (for dropdown)
$persons = $conn->query("SELECT * FROM PERSON")->fetch_all(MYSQLI_ASSOC);

// Fetch all clinics (for dropdown)
$clinics = $conn->query("SELECT * FROM CLINIC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vet CRUD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Vet Management</h1>

    <!-- Add/Edit Form -->
    <form method="post" class="mb-6 bg-gray-50 p-4 rounded shadow">
        <input type="hidden" name="vetID" value="<?= $editVet['vetID'] ?? '' ?>">

        <label class="block mb-2 font-semibold">Person:</label>
        <select name="vetID" required class="w-full mb-4 p-2 border rounded">
            <option value="">Select Person</option>
            <?php foreach ($persons as $p): ?>
                <option value="<?= $p['personID'] ?>" <?= ($editVet && $editVet['vetID']==$p['personID']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['firstName'] . ' ' . $p['lastName']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label class="block mb-2 font-semibold">License No:</label>
        <input type="text" name="vet_license_no" value="<?= $editVet['vet_license_no'] ?? '' ?>" class="w-full mb-4 p-2 border rounded" required>

        <label class="block mb-2 font-semibold">Specialization:</label>
        <input type="text" name="specialization" value="<?= $editVet['specialization'] ?? '' ?>" class="w-full mb-4 p-2 border rounded">

        <label class="block mb-2 font-semibold">Years of Experience:</label>
        <input type="number" name="years_of_experience" value="<?= $editVet['years_of_experience'] ?? '' ?>" class="w-full mb-4 p-2 border rounded">

        <label class="block mb-2 font-semibold">Clinic:</label>
        <select name="clinicID" required class="w-full mb-4 p-2 border rounded">
            <option value="">Select Clinic</option>
            <?php foreach ($clinics as $c): ?>
                <option value="<?= $c['clinicID'] ?>" <?= ($editVet && $editVet['clinicID']==$c['clinicID']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['clinicName']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="save" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            <?= $editVet ? 'Update Vet' : 'Add Vet' ?>
        </button>
    </form>

    <!-- Vet List Table -->
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">ID</th>
                <th class="border p-2">Name</th>
                <th class="border p-2">License No</th>
                <th class="border p-2">Specialization</th>
                <th class="border p-2">Experience</th>
                <th class="border p-2">Clinic</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vets as $vet): ?>
                <tr>
                    <td class="border p-2"><?= $vet['vetID'] ?></td>
                    <td class="border p-2"><?= htmlspecialchars($vet['firstName'] . ' ' . $vet['lastName']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($vet['vet_license_no']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($vet['specialization']) ?></td>
                    <td class="border p-2"><?= $vet['years_of_experience'] ?></td>
                    <td class="border p-2"><?= htmlspecialchars($vet['clinicName']) ?></td>
                    <td class="border p-2">
                        <a href="?edit=<?= $vet['vetID'] ?>" class="text-blue-500 hover:underline">Edit</a> |
                        <a href="?delete=<?= $vet['vetID'] ?>" onclick="return confirm('Are you sure?')" class="text-red-500 hover:underline">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
