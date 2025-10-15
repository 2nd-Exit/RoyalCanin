<?php
require 'db.php';

// Handle Create
if(isset($_POST['create'])){
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phoneNo = $_POST['phoneNo'];
    $position = $_POST['position'];
    $salary = $_POST['salary'];
    $deptID = $_POST['deptID'];
    $workShift = $_POST['workShift'];
    $employmentType = $_POST['employmentType'];
    $hireDate = $_POST['hireDate'];
    $supervisorID = $_POST['supervisorID'] ?: null;

    $stmt = $conn->prepare("INSERT INTO PERSON (firstName,lastName,gender,email,phoneNo) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss",$firstName,$lastName,$gender,$email,$phoneNo);
    $stmt->execute();
    $personID = $stmt->insert_id;

    $stmt2 = $conn->prepare("INSERT INTO EMPLOYEE (employeeID, hireDate, workShift, position, salary, employmentType, deptID, supervisorID) VALUES (?,?,?,?,?,?,?,?)");
    $stmt2->bind_param("isssdiii",$personID,$hireDate,$workShift,$position,$salary,$employmentType,$deptID,$supervisorID);
    $stmt2->execute();
    header("Location: employee.php");
    exit;
}

// Handle Edit
$editData = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $editData = $conn->query("
        SELECT e.*, p.firstName, p.lastName, p.gender, p.email, p.phoneNo 
        FROM EMPLOYEE e 
        JOIN PERSON p ON e.employeeID = p.personID 
        WHERE e.employeeID = $id
    ")->fetch_assoc();
}

// Handle Update
if(isset($_POST['update'])){
    $id = $_POST['employeeID'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phoneNo = $_POST['phoneNo'];
    $position = $_POST['position'];
    $salary = $_POST['salary'];
    $deptID = $_POST['deptID'];
    $workShift = $_POST['workShift'];
    $employmentType = $_POST['employmentType'];
    $hireDate = $_POST['hireDate'];
    $supervisorID = $_POST['supervisorID'] ?: null;

    $stmt = $conn->prepare("UPDATE PERSON SET firstName=?, lastName=?, gender=?, email=?, phoneNo=? WHERE personID=?");
    $stmt->bind_param("sssssi",$firstName,$lastName,$gender,$email,$phoneNo,$id);
    $stmt->execute();

    $stmt2 = $conn->prepare("UPDATE EMPLOYEE SET hireDate=?, workShift=?, position=?, salary=?, employmentType=?, deptID=?, supervisorID=? WHERE employeeID=?");
    $stmt2->bind_param("sssdiiii",$hireDate,$workShift,$position,$salary,$employmentType,$deptID,$supervisorID,$id);
    $stmt2->execute();
    header("Location: employee.php");
    exit;
}

// Handle Delete
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM EMPLOYEE WHERE employeeID=$id");
    $conn->query("DELETE FROM PERSON WHERE personID=$id");
    header("Location: employee.php");
    exit;
}

// Fetch Employees
$result = $conn->query("
    SELECT e.*, p.firstName, p.lastName, d.deptName 
    FROM EMPLOYEE e
    JOIN PERSON p ON e.employeeID = p.personID
    JOIN DEPARTMENT d ON e.deptID = d.deptID
");
?>

<div class="max-w-6xl mx-auto">
<h2 class="text-xl font-semibold mb-4">Employee Manager</h2>

<!-- Employee Form -->
<form method="POST" class="mb-6 bg-gray-50 p-4 rounded grid grid-cols-2 gap-4">

    <div class="flex flex-col">
        <label class="font-semibold mb-1">First Name</label>
        <input type="text" name="firstName" value="<?= $editData['firstName'] ?? '' ?>" required class="border p-2 rounded">
    </div>

    <div class="flex flex-col">
        <label class="font-semibold mb-1">Last Name</label>
        <input type="text" name="lastName" value="<?= $editData['lastName'] ?? '' ?>" required class="border p-2 rounded">
    </div>

    <div class="flex flex-col">
        <label class="font-semibold mb-1">Gender</label>
        <select name="gender" required class="border p-2 rounded">
            <option value="">Select Gender</option>
            <option value="Male" <?= ($editData['gender']??'')=='Male'?'selected':'' ?>>Male</option>
            <option value="Female" <?= ($editData['gender']??'')=='Female'?'selected':'' ?>>Female</option>
            <option value="Other" <?= ($editData['gender']??'')=='Other'?'selected':'' ?>>Other</option>
        </select>
    </div>

    <div class="flex flex-col">
        <label class="font-semibold mb-1">Email</label>
        <input type="email" name="email" value="<?= $editData['email'] ?? '' ?>" required class="border p-2 rounded">
    </div>

    <div class="flex flex-col">
        <label class="font-semibold mb-1">Phone</label>
        <input type="text" name="phoneNo" value="<?= $editData['phoneNo'] ?? '' ?>" class="border p-2 rounded">
    </div>

    <div class="flex flex-col">
        <label class="font-semibold mb-1">Position</label>
        <input type="text" name="position" value="<?= $editData['position'] ?? '' ?>" class="border p-2 rounded">
    </div>

    <div class="flex flex-col">
        <label class="font-semibold mb-1">Salary</label>
        <input type="number" step="0.01" name="salary" value="<?= $editData['salary'] ?? '' ?>" class="border p-2 rounded">
    </div>

    <div class="flex flex-col">
        <label class="font-semibold mb-1">Department</label>
        <select name="deptID" class="border p-2 rounded">
            <option value="">Select Department</option>
            <?php
            $depts = $conn->query("SELECT * FROM DEPARTMENT");
            while($d = $depts->fetch_assoc()){
                $selected = ($editData['deptID']??0)==$d['deptID']?'selected':'';
                echo "<option value='{$d['deptID']}' $selected>{$d['deptName']}</option>";
            }
            ?>
        </select>
    </div>

    <div class="flex flex-col">
        <label class="font-semibold mb-1">Work Shift</label>
        <select name="workShift" class="border p-2 rounded">
            <option value="">Select Shift</option>
            <?php foreach(['Morning','Afternoon','Evening','Night'] as $shift){
                $selected = ($editData['workShift']??'')==$shift?'selected':'';
                echo "<option value='$shift' $selected>$shift</option>";
            } ?>
        </select>
    </div>

    <div class="flex flex-col">
        <label class="font-semibold mb-1">Employment Type</label>
        <select name="employmentType" class="border p-2 rounded">
            <option value="">Select Type</option>
            <?php foreach(['Full-Time','Part-Time','Volunteer'] as $type){
                $selected = ($editData['employmentType']??'')==$type?'selected':'';
                echo "<option value='$type' $selected>$type</option>";
            } ?>
        </select>
    </div>

    <div class="flex flex-col">
        <label class="font-semibold mb-1">Hire Date</label>
        <input type="date" name="hireDate" value="<?= $editData['hireDate'] ?? '' ?>" class="border p-2 rounded">
    </div>

    <div class="flex flex-col">
        <label class="font-semibold mb-1">Supervisor ID</label>
        <input type="number" name="supervisorID" value="<?= $editData['supervisorID'] ?? '' ?>" class="border p-2 rounded">
    </div>

    <div class="col-span-2 mt-2">
        <?php if($editData): ?>
            <input type="hidden" name="employeeID" value="<?= $editData['employeeID'] ?>">
            <button type="submit" name="update" class="bg-green-500 text-white px-4 py-2 rounded">Update Employee</button>
            <a href="employee.php" class="ml-2 text-gray-700">Cancel</a>
        <?php else: ?>
            <button type="submit" name="create" class="bg-blue-500 text-white px-4 py-2 rounded">Add Employee</button>
        <?php endif; ?>
    </div>
</form>

<!-- Employee Table -->
<table class="w-full table-auto border border-gray-300">
<tr class="bg-gray-200"><th>ID</th><th>Name</th><th>Position</th><th>Department</th><th>Actions</th></tr>
<?php while($row=$result->fetch_assoc()): ?>
<tr class="text-center">
    <td><?= $row['employeeID'] ?></td>
    <td><?= $row['firstName'].' '.$row['lastName'] ?></td>
    <td><?= $row['position'] ?></td>
    <td><?= $row['deptName'] ?></td>
    <td>
        <a href="?edit=<?= $row['employeeID'] ?>" class="text-blue-500">Edit</a>
        <a href="?delete=<?= $row['employeeID'] ?>" onclick="return confirm('Are you sure?')" class="text-red-500 ml-2">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>
