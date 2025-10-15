<?php
require 'db.php';

// Handle create
if (isset($_POST['create'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $type = $_POST['type'];

    // Insert into PERSON
    $stmt = $conn->prepare("INSERT INTO PERSON (firstName, lastName, gender, dob, phone, email, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $firstName, $lastName, $gender, $dob, $phone, $email, $address);
    $stmt->execute();
    $personID = $stmt->insert_id;

    // Depending on type, insert into related table
    if ($type === 'employee') {
        $position = $_POST['position'];
        $workShift = $_POST['workShift'];
        $salary = $_POST['salary'];
        $hireDate = $_POST['hireDate'];

        $stmtE = $conn->prepare("INSERT INTO EMPLOYEE (personID, position, workShift, salary, hireDate) VALUES (?, ?, ?, ?, ?)");
        $stmtE->bind_param("issds", $personID, $position, $workShift, $salary, $hireDate);
        $stmtE->execute();
    } elseif ($type === 'adopter') {
        $income = $_POST['monthlyIncome'];
        $housingType = $_POST['housingType'];

        $stmtA = $conn->prepare("INSERT INTO ADOPTER (personID, monthlyIncome, housingType) VALUES (?, ?, ?)");
        $stmtA->bind_param("ids", $personID, $income, $housingType);
        $stmtA->execute();
    } elseif ($type === 'vet') {
        $license = $_POST['licenseNo'];
        $spec = $_POST['specialization'];
        $exp = $_POST['experienceYears'];
        $clinicID = $_POST['clinicID'];

        $stmtV = $conn->prepare("INSERT INTO VET (personID, vet_license_no, specialization, experienceYears, clinicID) VALUES (?, ?, ?, ?, ?)");
        $stmtV->bind_param("issii", $personID, $license, $spec, $exp, $clinicID);
        $stmtV->execute();
    }

    header("Location: person.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $personID = $_GET['delete'];
    $conn->query("DELETE FROM EMPLOYEE WHERE personID=$personID");
    $conn->query("DELETE FROM ADOPTER WHERE personID=$personID");
    $conn->query("DELETE FROM VET WHERE personID=$personID");
    $conn->query("DELETE FROM PERSON WHERE personID=$personID");
    header("Location: person.php");
    exit;
}

// Search
$search = $_GET['search'] ?? '';
$where = '';
if ($search) {
    $s = $conn->real_escape_string($search);
    $where = "WHERE firstName LIKE '%$s%' OR lastName LIKE '%$s%' OR phone LIKE '%$s%' OR email LIKE '%$s%'";
}

$result = $conn->query("SELECT * FROM PERSON $where ORDER BY personID DESC");
$clinics = $conn->query("SELECT * FROM CLINIC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Person Manager</title>
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
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Person Manager</h1>

    <!-- Search -->
    <form method="GET" class="mb-4">
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
                    <select name="workShift" class="border p-2 rounded w-full">
                        <option value="">Select Shift</option>
                        <option value="Morning">Morning</option>
                        <option value="Afternoon">Afternoon</option>
                        <option value="Evening">Evening</option>
                        <option value="Night">Night</option>
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
</body>
</html>
