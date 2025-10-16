<?php
require 'db.php';

// === HANDLE CREATE ===
if (isset($_POST['createPerson'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $phone = $_POST['phoneNo'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $AddAs = $_POST['type'];

    // Basic validation
    if (!$firstName || !$lastName || !$gender || !$date_of_birth || !$phone || !$email || !$address || !$AddAs) {
        die("All required fields must be filled.");
    }

    // 1️⃣ Insert into PERSON
    $stmt = $conn->prepare("INSERT INTO PERSON (firstName,lastName,gender,date_of_birth,phoneNo,email,address) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssss", $firstName, $lastName, $gender, $date_of_birth, $phone, $email, $address);
    $stmt->execute();
    $personID = $conn->insert_id;

    // 2️⃣ Insert into Subtable
    if ($AddAs === 'employee') {
        $hireDate = $_POST['hireDate'];
        $workShift = $_POST['workShift'];
        $salary = isset($_POST['salary']) && is_numeric($_POST['salary']) ? floatval($_POST['salary']) : 0;
        $deptID = isset($_POST['deptID']) && is_numeric($_POST['deptID']) ? intval($_POST['deptID']) : NULL;
        $supervisorID = isset($_POST['supervisorID']) && is_numeric($_POST['supervisorID']) ? intval($_POST['supervisorID']) : NULL;

        if ($salary < 0 || !$deptID) die("Salary must be >= 0 and Department ID must be valid.");

        $stmt = $conn->prepare("INSERT INTO EMPLOYEE (employeeID,hireDate,workShift,salary,deptID,supervisorID,position,employmentType)
                                VALUES (?,?,?,?,?,?, 'Staff','Full-Time')");
        $stmt->bind_param("issdii", $personID, $hireDate, $workShift, $salary, $deptID, $supervisorID);
        $stmt->execute();
    }
    elseif ($AddAs === 'adopter') {
        $income = isset($_POST['monthlyIncome']) && is_numeric($_POST['monthlyIncome']) ? floatval($_POST['monthlyIncome']) : 0;
        $pets = isset($_POST['number_of_pets_owned']) && is_numeric($_POST['number_of_pets_owned']) ? intval($_POST['number_of_pets_owned']) : 0;
        $occupation = trim($_POST['occupation']) ?: "N/A";

        // ✅ Strong validation to prevent DB constraint failure
        if ($income < 15000) die("Monthly income must be at least 15,000.");
        if ($pets < 0) die("Number of pets cannot be negative.");

        $stmt = $conn->prepare("INSERT INTO ADOPTER (adopterID,occupation,monthly_income,number_of_pets_owned)
                                VALUES (?,?,?,?)");
        $stmt->bind_param("isdi", $personID, $occupation, $income, $pets);
        $stmt->execute();
    }
    elseif ($AddAs === 'vet') {
        $licenseNo = trim($_POST['vet_license_no']);
        $specialization = trim($_POST['specialization']);
        $experience = isset($_POST['years_of_experience']) && is_numeric($_POST['years_of_experience']) ? intval($_POST['years_of_experience']) : 0;
        $clinicID = isset($_POST['clinicID']) && is_numeric($_POST['clinicID']) ? intval($_POST['clinicID']) : NULL;

        if (!$licenseNo || !$specialization || $experience < 0 || !$clinicID) die("Vet fields must be valid and non-empty.");

        $stmt = $conn->prepare("INSERT INTO VET (vetID,vet_license_no,specialization,years_of_experience,clinicID)
                                VALUES (?,?,?,?,?)");
        $stmt->bind_param("issii", $personID, $licenseNo, $specialization, $experience, $clinicID);
        $stmt->execute();
    }

    header("Location: admin.php?entity=person");
    exit;
}
?>

<h2 class="text-2xl font-semibold mb-4">Person Manager</h2>

<form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium mb-1">First Name</label>
        <input type="text" name="firstName" class="w-full border rounded p-2" placeholder="Enter First Name" required>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Last Name</label>
        <input type="text" name="lastName" class="w-full border rounded p-2" placeholder="Enter Last Name" required>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Gender</label>
        <select name="gender" class="w-full border rounded p-2">
            <option value="">Select Gender</option>
            <option>Male</option>
            <option>Female</option>
            <option>Other</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Phone Number</label>
        <input type="text" name="phoneNo" class="w-full border rounded p-2" placeholder="Enter Phone Number (0XXXXXXXXX)" required> 
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="email" class="w-full border rounded p-2" placeholder="Enter Email" required>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Date of Birth</label>
        <input type="date" name="date_of_birth" class="w-full border rounded p-2" placeholder="Enter Date of Birth" required>
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Address</label>
        <textarea name="address" class="w-full border rounded p-2" placeholder="Enter Address" required></textarea>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Add As</label>
        <select name="type" id="addAs" class="w-full border rounded p-2">
            <option value="">Select Role</option>
            <option value="employee">Employee</option>
            <option value="adopter">Adopter</option>
            <option value="vet">Vet</option>
        </select>
    </div>

    <!-- Role-specific fields -->
    <div id="employeeFields" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium mb-1">Hire Date</label><input type="date" name="hireDate" class="w-full border rounded p-2" required></div>
        <div>
            <label class="block text-sm font-medium mb-1">Work Shift</label>
            <select id="workShift" class="w-full border rounded p-2">
                <option value="">Select Work Shift</option>
                <option value="morning">Morning</option>
                <option value="afternoon">Afternoon</option>
                <option value="evening">Evening</option>
                <option value="night">Night</option>
            </select>
        </div>
        <div><label class="block text-sm font-medium mb-1">Salary</label><input type="number" name="salary" class="w-full border rounded p-2" placeholder="Enter Salary" required></div>
        <div>
            <label class="block text-sm font-medium mb-1">Employment Type</label>
            <select id="employmentType" class="w-full border rounded p-2">
                <option value="">Select Employment Type</option>
                <option value="fullTime">Full-Time</option>
                <option value="partTime">Part-Time</option>
                <option value="volunteer">Volunteer</option>
            </select>
        </div>
        <div><label class="block text-sm font-medium mb-1">Department ID</label><input type="number" name="departmentID" class="w-full border rounded p-2" placeholder="Enter Department ID" required></div>
        <div><label class="block text-sm font-medium mb-1">Supervisor ID</label><input type="number" name="supervisorID" class="w-full border rounded p-2" placeholder="Enter Supervisor ID"></div>
    </div>

    <div id="adopterFields" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium mb-1">Occupation</label><input type="text" name="occupation" class="w-full border rounded p-2" placeholder="Enter Occupation" required></div>
        <div><label class="block text-sm font-medium mb-1">Number of Pets Owned</label><input type="number" name="number_of_pets_owned" class="w-full border rounded p-2" placeholder="Enter Number of Pets Owned" required></div>
        <div><label class="block text-sm font-medium mb-1">Monthly Income</label><input type="number" name="monthlyIncome" class="w-full border rounded p-2"placeholder="Enter Monthly Income" required></div>
    </div>

    <div id="vetFields" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium mb-1">Years of Experience</label><input type="number" name="years_of_experience" class="w-full border rounded p-2" placeholder="Enter Experience (Years)" required></div>
        <div><label class="block text-sm font-medium mb-1">Specialization</label><input type="text" name="specialization" class="w-full border rounded p-2" placeholder="Enter Specialization" required></div>
        <div><label class="block text-sm font-medium mb-1">Vet License No</label><input type="text" name="vet_license_no" class="w-full border rounded p-2" placeholder="Enter License No" required></div>
        <div><label class="block text-sm font-medium mb-1">Clinic ID</label><input type="number" name="clinicID" class="w-full border rounded p-2" placeholder="Enter Clinic ID" required></div>
    </div>

    <div class="md:col-span-2">
        <button type="submit" name="createPerson" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Save</button>
    </div>
</form>

<script>
document.getElementById("addAs").addEventListener("change", function(){
    const val = this.value;
    ["employeeFields","adopterFields","vetFields"].forEach(id => document.getElementById(id).classList.add("hidden"));
    if(val) document.getElementById(val + "Fields").classList.remove("hidden");
});
</script>

<!-- Person Table -->
<table class="min-w-full border border-gray-300 rounded mt-6">
<thead class="bg-gray-200">
<tr>
    <th class="p-2 border">Person ID</th>
    <th class="p-2 border">Name</th>
    <th class="p-2 border">Gender</th>
    <th class="p-2 border">Phone</th>
    <th class="p-2 border">Email</th>
    <th class="p-2 border">DOB</th>
    <th class="p-2 border">Address</th>
</tr>
</thead>
<tbody>
<?php
$result = $conn->query("SELECT * FROM PERSON");
while($row = $result->fetch_assoc()){
    echo "<tr class='text-center border-b hover:bg-gray-50'>
            <td>{$row['personID']}</td>
            <td>{$row['firstName']} {$row['lastName']}</td>
            <td>{$row['gender']}</td>
            <td>{$row['phoneNo']}</td>
            <td>{$row['email']}</td>
            <td>{$row['date_of_birth']}</td>
            <td>{$row['address']}</td>
          </tr>";
}
?>
</tbody>
</table>
