<?php
require 'db.php';

// Handle Create
if (isset($_POST['create'])) {
    $animalName = $_POST['animalName'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $weight = $_POST['weight'];
    $arrivalDate = $_POST['arrivalDate'];
    $adoptionStatus = $_POST['adoptionStatus'];
    $healthCondition = $_POST['healthCondition'];
    $cageID = $_POST['cageID'];
    $foodID = $_POST['foodID'];
    $employeeID = $_POST['employeeID'];

    $stmt = $conn->prepare("INSERT INTO ANIMAL (animalName, species, breed, age, gender, weight, arrivalDate, adoptionStatus, healthCondition, cageID, foodID, employeeID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisdssiiii", $animalName, $species, $breed, $age, $gender, $weight, $arrivalDate, $adoptionStatus, $healthCondition, $cageID, $foodID, $employeeID);
    $stmt->execute();
    header("Location: animal.php");
    exit;
}

// Handle Edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editData = $conn->query("SELECT * FROM ANIMAL WHERE animalID = $id")->fetch_assoc();
}

// Handle Update
if (isset($_POST['update'])) {
    $id = $_POST['animalID'];
    $animalName = $_POST['animalName'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $weight = $_POST['weight'];
    $arrivalDate = $_POST['arrivalDate'];
    $adoptionStatus = $_POST['adoptionStatus'];
    $healthCondition = $_POST['healthCondition'];
    $cageID = $_POST['cageID'];
    $foodID = $_POST['foodID'];
    $employeeID = $_POST['employeeID'];

    $stmt = $conn->prepare("UPDATE ANIMAL SET animalName=?, species=?, breed=?, age=?, gender=?, weight=?, arrivalDate=?, adoptionStatus=?, healthCondition=?, cageID=?, foodID=?, employeeID=? WHERE animalID=?");
    $stmt->bind_param("sssisdssiiiiii", $animalName, $species, $breed, $age, $gender, $weight, $arrivalDate, $adoptionStatus, $healthCondition, $cageID, $foodID, $employeeID, $id);
    $stmt->execute();
    header("Location: animal.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM ANIMAL WHERE animalID = $id");
    header("Location: animal.php");
    exit;
}

// Fetch Animals
$result = $conn->query("SELECT a.*, p.firstName AS empFirstName, p.lastName AS empLastName, c.locateZone, f.foodName 
                        FROM ANIMAL a 
                        JOIN EMPLOYEE e ON a.employeeID = e.employeeID 
                        JOIN PERSON p ON e.employeeID = p.personID 
                        LEFT JOIN CAGE c ON a.cageID = c.cageID 
                        LEFT JOIN FOOD f ON a.foodID = f.foodID");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Animal Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Animal Manager</h1>

    <!-- Animal Form -->
    <form method="POST" class="mb-6 space-y-4 bg-gray-50 p-4 rounded">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-semibold">Animal Name</label>
                <input type="text" name="animalName" value="<?php echo $editData['animalName'] ?? ''; ?>" required class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block mb-1 font-semibold">Species</label>
                <input type="text" name="species" value="<?php echo $editData['species'] ?? ''; ?>" required class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block mb-1 font-semibold">Breed</label>
                <input type="text" name="breed" value="<?php echo $editData['breed'] ?? ''; ?>" class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block mb-1 font-semibold">Age</label>
                <input type="number" name="age" value="<?php echo $editData['age'] ?? ''; ?>" class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block mb-1 font-semibold">Gender</label>
                <select name="gender" class="border p-2 rounded w-full">
                    <option value="" disabled <?php if(!isset($editData['gender'])) echo 'selected'; ?>>Select Gender</option>
                    <option value="Male" <?php if(($editData['gender'] ?? '')=='Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if(($editData['gender'] ?? '')=='Female') echo 'selected'; ?>>Female</option>
                </select>
            </div>
            <div>
                <label class="block mb-1 font-semibold">Weight (kg)</label>
                <input type="number" step="0.01" name="weight" value="<?php echo $editData['weight'] ?? ''; ?>" class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block mb-1 font-semibold">Arrival Date</label>
                <input type="date" name="arrivalDate" value="<?php echo $editData['arrivalDate'] ?? ''; ?>" class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block mb-1 font-semibold">Adoption Status</label>
                <select name="adoptionStatus" class="border p-2 rounded w-full">
                    <option value="" disabled <?php if(!isset($editData['adoptionStatus'])) echo 'selected'; ?>>Select Status</option>
                    <option value="Available" <?php if(($editData['adoptionStatus'] ?? '')=='Available') echo 'selected'; ?>>Available</option>
                    <option value="Pending" <?php if(($editData['adoptionStatus'] ?? '')=='Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Adopted" <?php if(($editData['adoptionStatus'] ?? '')=='Adopted') echo 'selected'; ?>>Adopted</option>
                    <option value="Euthanized" <?php if(($editData['adoptionStatus'] ?? '')=='Euthanized') echo 'selected'; ?>>Euthanized</option>
                </select>
            </div>
            <div>
                <label class="block mb-1 font-semibold">Health Condition</label>
                <input type="text" name="healthCondition" value="<?php echo $editData['healthCondition'] ?? ''; ?>" class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block mb-1 font-semibold">Cage</label>
                <select name="cageID" class="border p-2 rounded w-full">
                    <option value="" disabled selected>Select Cage</option>
                    <?php
                    $cages = $conn->query("SELECT cageID, locateZone FROM CAGE");
                    while($cage = $cages->fetch_assoc()) {
                        $selected = ($editData['cageID'] ?? '')==$cage['cageID'] ? 'selected' : '';
                        echo "<option value='{$cage['cageID']}' $selected>{$cage['locateZone']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label class="block mb-1 font-semibold">Food</label>
                <select name="foodID" class="border p-2 rounded w-full">
                    <option value="" disabled selected>Select Food</option>
                    <?php
                    $foods = $conn->query("SELECT foodID, foodName FROM FOOD");
                    while($food = $foods->fetch_assoc()) {
                        $selected = ($editData['foodID'] ?? '')==$food['foodID'] ? 'selected' : '';
                        echo "<option value='{$food['foodID']}' $selected>{$food['foodName']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label class="block mb-1 font-semibold">Assigned Employee</label>
                <select name="employeeID" class="border p-2 rounded w-full" required>
                    <option value="" disabled selected>Select Employee</option>
                    <?php
                    $emps = $conn->query("SELECT e.employeeID, p.firstName, p.lastName FROM EMPLOYEE e JOIN PERSON p ON e.employeeID=p.personID");
                    while($emp = $emps->fetch_assoc()) {
                        $selected = ($editData['employeeID'] ?? '')==$emp['employeeID'] ? 'selected' : '';
                        echo "<option value='{$emp['employeeID']}' $selected>{$emp['firstName']} {$emp['lastName']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="mt-4">
            <?php if ($editData): ?>
                <input type="hidden" name="animalID" value="<?php echo $editData['animalID']; ?>">
                <button type="submit" name="update" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Update Animal</button>
                <a href="animal.php" class="ml-2 text-gray-700">Cancel</a>
            <?php else: ?>
                <button type="submit" name="create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Animal</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Animal Table -->
    <table class="w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Name</th>
                <th class="border p-2">Species</th>
                <th class="border p-2">Breed</th>
                <th class="border p-2">Age</th>
                <th class="border p-2">Gender</th>
                <th class="border p-2">Weight</th>
                <th class="border p-2">Arrival Date</th>
                <th class="border p-2">Status</th>
                <th class="border p-2">Health</th>
                <th class="border p-2">Cage</th>
                <th class="border p-2">Food</th>
                <th class="border p-2">Employee</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr class="text-center">
                    <td class="border p-2"><?php echo $row['animalName']; ?></td>
                    <td class="border p-2"><?php echo $row['species']; ?></td>
                    <td class="border p-2"><?php echo $row['breed']; ?></td>
                    <td class="border p-2"><?php echo $row['age']; ?></td>
                    <td class="border p-2"><?php echo $row['gender']; ?></td>
                    <td class="border p-2"><?php echo $row['weight']; ?></td>
                    <td class="border p-2"><?php echo $row['arrivalDate']; ?></td>
                    <td class="border p-2"><?php echo $row['adoptionStatus']; ?></td>
                    <td class="border p-2"><?php echo $row['healthCondition']; ?></td>
                    <td class="border p-2"><?php echo $row['locateZone']; ?></td>
                    <td class="border p-2"><?php echo $row['foodName']; ?></td>
                    <td class="border p-2"><?php echo $row['empFirstName'].' '.$row['empLastName']; ?></td>
                    <td class="border p-2">
                        <a href="?edit=<?php echo $row['animalID']; ?>" class="bg-yellow-400 hover:bg-yellow-600 text-white px-2 py-1 rounded">Edit</a>
                        <a href="?delete=<?php echo $row['animalID']; ?>" onclick="return confirm('Are you sure?')" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
