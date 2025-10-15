<?php
require 'db.php';

// Handle Create
if (isset($_POST['create'])) {
    $adoptionDate = $_POST['adoptionDate'];
    $adopterID = $_POST['adopterID'];
    $animalID = $_POST['animalID'];

    $stmt = $conn->prepare("INSERT INTO ADOPTION (adoptionDate, adopterID, animalID) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $adoptionDate, $adopterID, $animalID);
    $stmt->execute();

    // Update animal adoption status to 'Adopted'
    $conn->query("UPDATE ANIMAL SET adoptionStatus='Adopted' WHERE animalID=$animalID");

    header("Location: adoption.php");
    exit;
}

// Handle Edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editData = $conn->query("
        SELECT a.*, p.firstName, p.lastName, an.animalName
        FROM ADOPTION a
        JOIN ADOPTER ad ON a.adopterID = ad.adopterID
        JOIN PERSON p ON ad.adopterID = p.personID
        JOIN ANIMAL an ON a.animalID = an.animalID
        WHERE a.adoptionID = $id
    ")->fetch_assoc();
}

// Handle Update
if (isset($_POST['update'])) {
    $id = $_POST['adoptionID'];
    $adoptionDate = $_POST['adoptionDate'];
    $adopterID = $_POST['adopterID'];
    $animalID = $_POST['animalID'];

    $stmt = $conn->prepare("UPDATE ADOPTION SET adoptionDate=?, adopterID=?, animalID=? WHERE adoptionID=?");
    $stmt->bind_param("siii", $adoptionDate, $adopterID, $animalID, $id);
    $stmt->execute();

    header("Location: adoption.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Reset animal adoption status
    $animalID = $conn->query("SELECT animalID FROM ADOPTION WHERE adoptionID=$id")->fetch_assoc()['animalID'];
    $conn->query("UPDATE ANIMAL SET adoptionStatus='Available' WHERE animalID=$animalID");

    $conn->query("DELETE FROM ADOPTION WHERE adoptionID = $id");
    header("Location: adoption.php");
    exit;
}

// Fetch adoption list
$result = $conn->query("
    SELECT a.adoptionID, a.adoptionDate, p.firstName, p.lastName, an.animalName, an.species
    FROM ADOPTION a
    JOIN ADOPTER ad ON a.adopterID = ad.adopterID
    JOIN PERSON p ON ad.adopterID = p.personID
    JOIN ANIMAL an ON a.animalID = an.animalID
");

// Fetch adopters and animals for dropdown
$adopters = $conn->query("SELECT ad.adopterID, p.firstName, p.lastName FROM ADOPTER ad JOIN PERSON p ON ad.adopterID = p.personID");
$animals = $conn->query("SELECT animalID, animalName, species FROM ANIMAL WHERE adoptionStatus='Available'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adoption Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Adoption Manager</h1>

    <!-- Adoption Form -->
    <form method="POST" class="mb-6 space-y-4 bg-gray-50 p-4 rounded">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="date" name="adoptionDate" placeholder="Adoption Date" value="<?php echo $editData['adoptionDate'] ?? ''; ?>" required class="border p-2 rounded">
            <select name="adopterID" required class="border p-2 rounded">
                <option value="">-- Select Adopter --</option>
                <?php while($row = $adopters->fetch_assoc()): ?>
                    <option value="<?= $row['adopterID'] ?>" <?php if(($editData['adopterID'] ?? '')==$row['adopterID']) echo 'selected'; ?>>
                        <?= $row['firstName'] . ' ' . $row['lastName'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <select name="animalID" required class="border p-2 rounded">
                <option value="">-- Select Animal --</option>
                <?php while($row = $animals->fetch_assoc()): ?>
                    <option value="<?= $row['animalID'] ?>" <?php if(($editData['animalID'] ?? '')==$row['animalID']) echo 'selected'; ?>>
                        <?= $row['animalName'] . ' (' . $row['species'] . ')' ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mt-4">
            <?php if ($editData): ?>
                <input type="hidden" name="adoptionID" value="<?php echo $editData['adoptionID']; ?>">
                <button type="submit" name="update" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Update Adoption</button>
                <a href="adoption.php" class="ml-2 text-gray-700">Cancel</a>
            <?php else: ?>
                <button type="submit" name="create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Adoption</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Adoption Table -->
    <table class="w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Adoption Date</th>
                <th class="border p-2">Adopter</th>
                <th class="border p-2">Animal</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr class="text-center">
                    <td class="border p-2"><?= $row['adoptionDate'] ?></td>
                    <td class="border p-2"><?= $row['firstName'] . ' ' . $row['lastName'] ?></td>
                    <td class="border p-2"><?= $row['animalName'] . ' (' . $row['species'] . ')' ?></td>
                    <td class="border p-2">
                        <a href="?edit=<?= $row['adoptionID']; ?>" class="bg-yellow-400 hover:bg-yellow-600 text-white px-2 py-1 rounded">Edit</a>
                        <a href="?delete=<?= $row['adoptionID']; ?>" onclick="return confirm('Are you sure?')" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
