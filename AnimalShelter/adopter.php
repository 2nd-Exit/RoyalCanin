<?php
require 'db.php';

// Handle Create
if (isset($_POST['create'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phoneNo = $_POST['phoneNo'];
    $occupation = $_POST['occupation'];
    $monthly_income = $_POST['monthly_income'];
    $number_of_pets_owned = $_POST['number_of_pets_owned'];

    $stmt = $conn->prepare("INSERT INTO PERSON (firstName, lastName, gender, email, phoneNo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $firstName, $lastName, $gender, $email, $phoneNo);
    $stmt->execute();
    $personID = $stmt->insert_id;

    $stmt2 = $conn->prepare("INSERT INTO ADOPTER (adopterID, occupation, monthly_income, number_of_pets_owned) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("isdi", $personID, $occupation, $monthly_income, $number_of_pets_owned);
    $stmt2->execute();
    header("Location: adopter.php");
    exit;
}

// Handle Edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editData = $conn->query("
        SELECT a.*, p.firstName, p.lastName, p.gender, p.email, p.phoneNo
        FROM ADOPTER a
        JOIN PERSON p ON a.adopterID = p.personID
        WHERE a.adopterID = $id
    ")->fetch_assoc();
}

// Handle Update
if (isset($_POST['update'])) {
    $id = $_POST['adopterID'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phoneNo = $_POST['phoneNo'];
    $occupation = $_POST['occupation'];
    $monthly_income = $_POST['monthly_income'];
    $number_of_pets_owned = $_POST['number_of_pets_owned'];

    $stmt = $conn->prepare("UPDATE PERSON SET firstName=?, lastName=?, gender=?, email=?, phoneNo=? WHERE personID=?");
    $stmt->bind_param("sssssi", $firstName, $lastName, $gender, $email, $phoneNo, $id);
    $stmt->execute();

    $stmt2 = $conn->prepare("UPDATE ADOPTER SET occupation=?, monthly_income=?, number_of_pets_owned=? WHERE adopterID=?");
    $stmt2->bind_param("sdii", $occupation, $monthly_income, $number_of_pets_owned, $id);
    $stmt2->execute();
    header("Location: adopter.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM ADOPTER WHERE adopterID = $id");
    $conn->query("DELETE FROM PERSON WHERE personID = $id");
    header("Location: adopter.php");
    exit;
}

// Fetch Adopters
$result = $conn->query("
    SELECT a.adopterID, p.firstName, p.lastName, a.occupation, a.monthly_income, a.number_of_pets_owned
    FROM ADOPTER a
    JOIN PERSON p ON a.adopterID = p.personID
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adopter Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Adopter Manager</h1>

    <!-- Adopter Form -->
    <form method="POST" class="mb-6 space-y-4 bg-gray-50 p-4 rounded">
        <div class="grid grid-cols-2 gap-4">
            <input type="text" name="firstName" placeholder="First Name" value="<?php echo $editData['firstName'] ?? ''; ?>" required class="border p-2 rounded">
            <input type="text" name="lastName" placeholder="Last Name" value="<?php echo $editData['lastName'] ?? ''; ?>" required class="border p-2 rounded">
            <select name="gender" class="border p-2 rounded">
                <option value="Male" <?php if(($editData['gender'] ?? '')=='Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if(($editData['gender'] ?? '')=='Female') echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if(($editData['gender'] ?? '')=='Other') echo 'selected'; ?>>Other</option>
            </select>
            <input type="email" name="email" placeholder="Email" value="<?php echo $editData['email'] ?? ''; ?>" required class="border p-2 rounded">
            <input type="text" name="phoneNo" placeholder="Phone" value="<?php echo $editData['phoneNo'] ?? ''; ?>" class="border p-2 rounded">
            <input type="text" name="occupation" placeholder="Occupation" value="<?php echo $editData['occupation'] ?? ''; ?>" class="border p-2 rounded">
            <input type="number" step="0.01" name="monthly_income" placeholder="Monthly Income" value="<?php echo $editData['monthly_income'] ?? ''; ?>" class="border p-2 rounded">
            <input type="number" name="number_of_pets_owned" placeholder="Number of Pets Owned" value="<?php echo $editData['number_of_pets_owned'] ?? ''; ?>" class="border p-2 rounded">
        </div>
        <div class="mt-4">
            <?php if ($editData): ?>
                <input type="hidden" name="adopterID" value="<?php echo $editData['adopterID']; ?>">
                <button type="submit" name="update" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Update Adopter</button>
                <a href="adopter.php" class="ml-2 text-gray-700">Cancel</a>
            <?php else: ?>
                <button type="submit" name="create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Adopter</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Adopter Table -->
    <table class="w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Name</th>
                <th class="border p-2">Occupation</th>
                <th class="border p-2">Income</th>
                <th class="border p-2">Pets Owned</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr class="text-center">
                    <td class="border p-2"><?php echo $row['firstName'] . ' ' . $row['lastName']; ?></td>
                    <td class="border p-2"><?php echo $row['occupation']; ?></td>
                    <td class="border p-2"><?php echo $row['monthly_income']; ?></td>
                    <td class="border p-2"><?php echo $row['number_of_pets_owned']; ?></td>
                    <td class="border p-2">
                        <a href="?edit=<?php echo $row['adopterID']; ?>" class="bg-yellow-400 hover:bg-yellow-600 text-white px-2 py-1 rounded">Edit</a>
                        <a href="?delete=<?php echo $row['adopterID']; ?>" onclick="return confirm('Are you sure?')" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
