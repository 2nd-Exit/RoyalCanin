<?php
require 'db.php';

// ENTITY SETUP
$entity = $_GET['entity'] ?? 'person';
$allowed = ['person','employee','adopter','vet','department','cage','food','food_order','adoption','treatment','animal'];
if (!in_array($entity, $allowed)) $entity = 'person';

// SEARCH
$search = $_GET['search'] ?? '';
$where = '';
if ($search) {
    $s = $conn->real_escape_string($search);
    $where = "WHERE firstName LIKE '%$s%' OR lastName LIKE '%$s%' OR phone LIKE '%$s%' OR email LIKE '%$s%'";
}

// ===== HANDLE CREATE / DELETE ACTIONS =====

// Delete person
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM person WHERE personID=$id");
    header("Location: ?entity=person");
    exit;
}

// Delete animal
if (isset($_GET['deleteAnimal'])) {
    $id = intval($_GET['deleteAnimal']);
    $conn->query("DELETE FROM animal WHERE animalID=$id");
    header("Location: ?entity=animal");
    exit;
}

// Delete treatment
if (isset($_GET['deleteTreatment'])) {
    $id = intval($_GET['deleteTreatment']);
    $conn->query("DELETE FROM treatment WHERE treatmentID=$id");
    header("Location: ?entity=treatment");
    exit;
}

// Add Animal
if (isset($_POST['createAnimal'])) {
    $name = $conn->real_escape_string($_POST['animalName']);
    $species = $conn->real_escape_string($_POST['species']);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $cageID = intval($_POST['cageID']);

    $conn->query("INSERT INTO animal (animalName, species, gender, dob, cageID) 
                  VALUES ('$name', '$species', '$gender', '$dob', $cageID)");
    header("Location: ?entity=animal");
    exit;
}

// Add Treatment
if (isset($_POST['createTreatment'])) {
    $animalID = intval($_POST['animalID']);
    $vetID = intval($_POST['vetID']);
    $clinicID = intval($_POST['clinicID']);
    $medicine = $conn->real_escape_string($_POST['medicine']);
    $vaccine = $conn->real_escape_string($_POST['vaccine']);
    $treatmentDate = $_POST['treatmentDate'];

    $conn->query("INSERT INTO treatment (animalID, vetID, clinicID, medicine, vaccine, treatmentDate)
                  VALUES ($animalID, $vetID, $clinicID, '$medicine', '$vaccine', '$treatmentDate')");
    header("Location: ?entity=treatment");
    exit;
}

// ===== FETCH DATA =====
$result = $conn->query("SELECT * FROM person $where ORDER BY personID DESC");
$clinics = $conn->query("SELECT * FROM clinic");
$cages = $conn->query("SELECT * FROM cage");

// For Treatment Table: Correct JOIN
$treatments = $conn->query("
    SELECT t.*, a.animalName, CONCAT(p.firstName,' ',p.lastName) AS vetName, c.clinicName
    FROM treatment t
    LEFT JOIN animal a ON t.animalID = a.animalID
    LEFT JOIN vet v ON t.vetID = v.vetID
    LEFT JOIN person p ON v.vetID = p.personID
    LEFT JOIN clinic c ON t.clinicID = c.clinicID
");

// For Animals Table
$animals = $conn->query("SELECT a.*, c.cageID FROM animal a LEFT JOIN cage c ON a.cageID=c.cageID");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Animal Shelter Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
function showExtraFields() {
    const type = document.getElementById('type').value;
    document.getElementById('employeeFields').classList.add('hidden');
    document.getElementById('adopterFields').classList.add('hidden');
    document.getElementById('vetFields').classList.add('hidden');
    if (type === 'employee') document.getElementById('employeeFields').classList.remove('hidden');
    if (type === 'adopter') document.getElementById('adopterFields').classList.remove('hidden');
    if (type === 'vet') document.getElementById('vetFields').classList.remove('hidden');
}
</script>
</head>
<body class="bg-gray-100 p-6">

<header class="mb-6">
    <h1 class="text-3xl font-bold mb-3">Animal Shelter Admin Panel</h1>

    <!-- Navigation Bar -->
    <nav class="flex flex-wrap gap-2 mb-6">
        <?php
        $menu = [
            'person' => 'Person',
            'department' => 'Department',
            'employee' => 'Employee',
            'adopter' => 'Adopter',
            'cage' => 'Cage',
            'food' => 'Food',
            'animal' => 'Animal',
            'food_order' => 'Food Order',
            'adoption' => 'Adoption',
            'treatment' => 'Treatment'
        ];
        foreach ($menu as $key => $label) {
            $active = ($entity == $key) ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400';
            echo "<a href='?entity=$key' class='px-4 py-2 rounded $active transition'>$label</a>";
        }
        ?>
    </nav>
</header>

<main class="bg-white p-6 rounded shadow">
<?php if($entity === 'person'): ?>
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Person Manager</h1>

    <!-- Search -->
    <form method="GET" class="mb-4">
        <input type="hidden" name="entity" value="person">
        <input type="text" name="search" placeholder="Search person..." value="<?= htmlspecialchars($search) ?>" class="border p-2 rounded w-full">
    </form>

    <!-- Add Person Form -->
    <form method="POST" class="bg-gray-50 p-4 rounded mb-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block">First Name</label>
                <input type="text" name="firstName" required class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block">Last Name</label>
                <input type="text" name="lastName" required class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block">Gender</label>
                <select name="gender" required class="border p-2 rounded w-full">
                    <option value="">Select gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div>
                <label class="block">Date of Birth</label>
                <input type="date" name="dob" required class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block">Phone</label>
                <input type="text" name="phone" required class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block">Email</label>
                <input type="email" name="email" class="border p-2 rounded w-full">
            </div>
            <div class="col-span-2">
                <label class="block">Address</label>
                <textarea name="address" class="border p-2 rounded w-full"></textarea>
            </div>
            <div class="col-span-2">
                <label class="block font-semibold">Add As</label>
                <select name="type" id="type" onchange="showExtraFields()" required class="border p-2 rounded w-full">
                    <option value="">Select Type</option>
                    <option value="employee">Employee</option>
                    <option value="adopter">Adopter</option>
                    <option value="vet">Vet</option>
                </select>
            </div>
        </div>

        <!-- Employee Fields -->
        <div id="employeeFields" class="hidden bg-blue-50 p-4 rounded">
            <h3 class="font-semibold mb-2">Employee Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block">Position</label>
                    <input type="text" name="position" class="border p-2 rounded w-full">
                </div>
                <div>
                    <label class="block">Work Shift</label>
                    <select name="workShift" class="border p-2 rounded">
                        <option value="">Select Shift</option>
                        <?php foreach(['Morning','Afternoon','Evening','Night'] as $shift){
                            $selected = ($editData['workShift']??'')==$shift?'selected':'';
                            echo "<option value='$shift' $selected>$shift</option>";
                        } ?>
                    </select>
                </div>
                <div>
                    <label class="block">Salary</label>
                    <input type="number" step="0.01" name="salary" class="border p-2 rounded w-full">
                </div>
                <div>
                    <label class="block">Hire Date</label>
                    <input type="date" name="hireDate" class="border p-2 rounded w-full">
                </div>
            </div>
        </div>

        <!-- Adopter Fields -->
        <div id="adopterFields" class="hidden bg-green-50 p-4 rounded">
            <h3 class="font-semibold mb-2">Adopter Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block">Monthly Income</label>
                    <input type="number" step="0.01" name="monthlyIncome" class="border p-2 rounded w-full">
                </div>
                <div>
                    <label class="block">Housing Type</label>
                    <input type="text" name="housingType" class="border p-2 rounded w-full">
                </div>
            </div>
        </div>

        <!-- Vet Fields -->
        <div id="vetFields" class="hidden bg-yellow-50 p-4 rounded">
            <h3 class="font-semibold mb-2">Vet Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block">License No</label>
                    <input type="text" name="licenseNo" class="border p-2 rounded w-full">
                </div>
                <div>
                    <label class="block">Specialization</label>
                    <input type="text" name="specialization" class="border p-2 rounded w-full">
                </div>
                <div>
                    <label class="block">Experience (Years)</label>
                    <input type="number" name="experienceYears" class="border p-2 rounded w-full">
                </div>
                <div>
                    <label class="block">Clinic</label>
                    <select name="clinicID" class="border p-2 rounded w-full">
                        <option value="">Select Clinic</option>
                        <?php while($row = $clinics->fetch_assoc()): ?>
                            <option value="<?= $row['clinicID'] ?>"><?= $row['clinicName'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" name="create" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add Person
        </button>
    </form>

    <!-- Person Table -->
    <table class="w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200 text-center">
                <th class="border p-2">ID</th>
                <th class="border p-2">Name</th>
                <th class="border p-2">Gender</th>
                <th class="border p-2">Phone</th>
                <th class="border p-2">Email</th>
                <th class="border p-2">Type</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while($p = $result->fetch_assoc()): 
            $type = '-';
            if ($conn->query("SELECT * FROM EMPLOYEE WHERE personID={$p['personID']}")->num_rows) $type = 'Employee';
            elseif ($conn->query("SELECT * FROM ADOPTER WHERE personID={$p['personID']}")->num_rows) $type = 'Adopter';
            elseif ($conn->query("SELECT * FROM VET WHERE personID={$p['personID']}")->num_rows) $type = 'Vet';
        ?>
        <tr class="text-center">
            <td class="border p-2"><?= $p['personID'] ?></td>
            <td class="border p-2"><?= $p['firstName'].' '.$p['lastName'] ?></td>
            <td class="border p-2"><?= $p['gender'] ?></td>
            <td class="border p-2"><?= $p['phone'] ?></td>
            <td class="border p-2"><?= $p['email'] ?></td>
            <td class="border p-2"><?= $type ?></td>
            <td class="border p-2">
                <a href="?delete=<?= $p['personID'] ?>" onclick="return confirm('Are you sure?')" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if($entity === 'employee'): ?>
<h2 class="text-xl font-semibold mb-4">Employees</h2>
<p class="text-gray-600 mb-4">To add a new employee, go to the <span class="font-semibold">Person</span> page.</p>
<!-- Employee table like your original Tailwind table -->
<?php endif; ?>

<?php if($entity === 'adopter'): ?>
<h2 class="text-xl font-semibold mb-4">Adopters</h2>
<p class="text-gray-600 mb-4">To add a new adopter, go to the <span class="font-semibold">Person</span> page.</p>
<!-- Adopter table like your original Tailwind table -->
<?php endif; ?>

<?php if($entity === 'animal'): ?>
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Animal Manager</h1>

    <!-- Add Animal Form -->
    <form method="POST" class="bg-gray-50 p-4 rounded mb-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block">Name</label>
                <input type="text" name="animalName" required class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block">Species</label>
                <input type="text" name="species" required class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block">Gender</label>
                <select name="gender" required class="border p-2 rounded w-full">
                    <option value="">Select gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div>
                <label class="block">DOB</label>
                <input type="date" name="dob" required class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block">Cage</label>
                <select name="cageID" class="border p-2 rounded w-full">
                    <option value="">Select Cage</option>
                    <?php
                    $cages = $conn->query("SELECT * FROM CAGE");
                    while($c = $cages->fetch_assoc()):
                    ?>
                        <option value="<?= $c['cageID'] ?>"><?= $c['cageName'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <button type="submit" name="createAnimal" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add Animal
        </button>
    </form>

    <!-- Animal Table -->
    <table class="w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200 text-center">
                <th class="border p-2">ID</th>
                <th class="border p-2">Name</th>
                <th class="border p-2">Species</th>
                <th class="border p-2">Gender</th>
                <th class="border p-2">DOB</th>
                <th class="border p-2">Cage</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $animals = $conn->query("SELECT a.*, c.cageID FROM ANIMAL a LEFT JOIN CAGE c ON a.cageID=c.cageID");
        while($a = $animals->fetch_assoc()): ?>
            <tr class="text-center">
                <td class="border p-2"><?= $a['animalID'] ?></td>
                <td class="border p-2"><?= $a['animalName'] ?></td>
                <td class="border p-2"><?= $a['species'] ?></td>
                <td class="border p-2"><?= $a['gender'] ?></td>
                <td class="border p-2"><?= $a['dob'] ?></td>
                <td class="border p-2"><?= $a['cageID'] ?></td>
                <td class="border p-2">
                    <a href="?deleteAnimal=<?= $a['animalID'] ?>" onclick="return confirm('Are you sure?')" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if($entity === 'treatment'): ?>
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Treatment Manager</h1>

    <!-- Add Treatment Form -->
    <form method="POST" class="bg-gray-50 p-4 rounded mb-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block">Animal</label>
                <select name="animalID" class="border p-2 rounded w-full">
                    <option value="">Select Animal</option>
                    <?php
                    $animals = $conn->query("SELECT * FROM ANIMAL");
                    while($a = $animals->fetch_assoc()):
                    ?>
                        <option value="<?= $a['animalID'] ?>"><?= $a['animalName'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label class="block">Vet</label>
                <select name="vetID" class="border p-2 rounded w-full">
                    <option value="">Select Vet</option>
                    <?php
                    $vets = $conn->query("SELECT * FROM VET");
                    while($v = $vets->fetch_assoc()):
                    ?>
                        <option value="<?= $v['vetID'] ?>"><?= $v['firstName'].' '.$v['lastName'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label class="block">Clinic</label>
                <select name="clinicID" class="border p-2 rounded w-full">
                    <option value="">Select Clinic</option>
                    <?php
                    $clinics = $conn->query("SELECT * FROM CLINIC");
                    while($c = $clinics->fetch_assoc()):
                    ?>
                        <option value="<?= $c['clinicID'] ?>"><?= $c['clinicName'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label class="block">Medicine</label>
                <input type="text" name="medicine" class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block">Vaccine</label>
                <input type="text" name="vaccine" class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block">Date</label>
                <input type="date" name="treatmentDate" class="border p-2 rounded w-full">
            </div>
        </div>
        <button type="submit" name="createTreatment" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add Treatment
        </button>
    </form>

    <!-- Treatment Table -->
    <table class="w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200 text-center">
                <th class="border p-2">Treatment ID</th>
                <th class="border p-2">Animal</th>
                <th class="border p-2">Vet</th>
                <th class="border p-2">Clinic</th>
                <th class="border p-2">Medicine</th>
                <th class="border p-2">Vaccine</th>
                <th class="border p-2">Date</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $treatments = $conn->query("
            SELECT t.*, a.animalName, p.firstName, p.lastName, c.clinicName
            FROM treatment t
            LEFT JOIN animal a ON t.animalID = a.animalID
            LEFT JOIN vet v ON t.vetID = v.vetID
            LEFT JOIN person p ON v.vetID = p.personID
            LEFT JOIN clinic c ON t.clinicID = c.clinicID
        ");
        while($t = $treatments->fetch_assoc()):
        ?>
            <tr class="text-center">
                <td class="border p-2"><?= $t['treatmentID'] ?></td>
                <td class="border p-2"><?= $t['animalName'] ?></td>
                <td class="border p-2"><?= $t['vetName'] ?></td>
                <td class="border p-2"><?= $t['clinicName'] ?></td>
                <td class="border p-2"><?= $t['medicine'] ?></td>
                <td class="border p-2"><?= $t['vaccine'] ?></td>
                <td class="border p-2"><?= $t['treatmentDate'] ?></td>
                <td class="border p-2">
                    <a href="?deleteTreatment=<?= $t['treatmentID'] ?>" onclick="return confirm('Are you sure?')" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
</main>
</body>
</html>
