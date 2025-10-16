<?php
require 'db.php';

// === HANDLE CREATE ===
if (isset($_POST['createAnimal'])) {
    $name = $_POST['animalName'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $weight = $_POST['weight'];
    $arrival = $_POST['arrivalDate'];
    $status = $_POST['adoptionStatus'];
    $health = $_POST['healthCondition'];
    $cage = $_POST['cageID'];
    $food = $_POST['foodID'];
    $employee = $_POST['employeeID'];

    $stmt = $conn->prepare("INSERT INTO ANIMAL (animalName,species,breed,age,gender,weight,arrivalDate,adoptionStatus,healthCondition,cageID,foodID,employeeID)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssisdsssiii", $name, $species, $breed, $age, $gender, $weight, $arrival, $status, $health, $cage, $food, $employee);
    $stmt->execute();

    header("Location: admin.php?entity=animal");
    exit;
}

?>


<h2 class="text-2xl font-semibold mb-4">Animal Manager</h2>

<form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div><label class="block text-sm font-medium mb-1">Animal Name</label><input type="text" name="animalName" class="w-full border rounded p-2" required></div>
    <div><label class="block text-sm font-medium mb-1">Species</label><input type="text" name="species" class="w-full border rounded p-2" required></div>
    <div><label class="block text-sm font-medium mb-1">Breed</label><input type="text" name="breed" class="w-full border rounded p-2"></div>
    <div><label class="block text-sm font-medium mb-1">Age</label><input type="number" name="age" class="w-full border rounded p-2"></div>
    <div><label class="block text-sm font-medium mb-1">Gender</label>
        <select name="gender" class="w-full border rounded p-2">
            <option value="">Select Gender</option>
            <option>Male</option>
            <option>Female</option>
        </select>
    </div>
    <div><label class="block text-sm font-medium mb-1">Weight (kg)</label><input type="number" step="0.1" name="weight" class="w-full border rounded p-2"></div>
    <div><label class="block text-sm font-medium mb-1">Adoption Status</label>
        <select name="adoptionStatus" class="w-full border rounded p-2">
            <option value="">Select Status</option>
            <option>Available</option>
            <option>Pending</option>
            <option>Adopted</option>
            <option>Euthanized</option>
        </select>
    </div>
    <div><label class="block text-sm font-medium mb-1">Arrival Date</label><input type="date" name="arrivalDate" class="w-full border rounded p-2"></div>
    <div><label class="block text-sm font-medium mb-1">Health Condition</label><input type="text" name="healthCondition" class="w-full border rounded p-2"></div>
    <div><label class="block text-sm font-medium mb-1">Cage ID</label><input type="number" name="cageID" class="w-full border rounded p-2"></div>
    <div><label class="block text-sm font-medium mb-1">Food ID</label><input type="number" name="foodID" class="w-full border rounded p-2"></div>
    <div><label class="block text-sm font-medium mb-1">Employee ID</label><input type="number" name="employeeID" class="w-full border rounded p-2"></div>

    <div class="md:col-span-2">
        <button type="submit" name="createAnimal" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Save</button>
    </div>
</form>

<table class="min-w-full border border-gray-300 rounded">
<thead class="bg-gray-200">
<tr>
    <th class="p-2 border">ID</th>
    <th class="p-2 border">Name</th>
    <th class="p-2 border">Species</th>
    <th class="p-2 border">Breed</th>
    <th class="p-2 border">Age</th>
    <th class="p-2 border">Gender</th>
    <th class="p-2 border">Status</th>
    <th class="p-2 border">Health</th>
</tr>
</thead>
<tbody>
<?php
$result = $conn->query("SELECT * FROM animal");
while($row = $result->fetch_assoc()){
    echo "<tr class='text-center border-b hover:bg-gray-50'>
            <td class='p-2'>{$row['animalID']}</td>
            <td class='p-2'>{$row['animalName']}</td>
            <td class='p-2'>{$row['species']}</td>
            <td class='p-2'>{$row['breed']}</td>
            <td class='p-2'>{$row['age']}</td>
            <td class='p-2'>{$row['gender']}</td>
            <td class='p-2'>{$row['adoptionStatus']}</td>
            <td class='p-2'>{$row['healthCondition']}</td>
          </tr>";
}
?>
</tbody>
</table>
