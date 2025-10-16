<?php
require 'db.php';

// SEARCH
$search = $_GET['search'] ?? '';
$where = '';
if ($search) {
    $s = $conn->real_escape_string($search);
    $where = "WHERE p.firstName LIKE '%$s%' OR p.lastName LIKE '%$s%' OR e.employeeID LIKE '%$s%' OR d.deptName LIKE '%$s%'";
}

$result = $conn->query("
    SELECT e.employeeID, CONCAT(p.firstName, ' ', p.lastName) AS name, e.workShift, e.salary, e.hireDate, e.employmentType, d.deptName, 
           s.firstName AS supFirst, s.lastName AS supLast
    FROM EMPLOYEE e
    JOIN PERSON p ON e.employeeID = p.personID
    LEFT JOIN DEPARTMENT d ON e.deptID = d.deptID
    LEFT JOIN PERSON s ON e.supervisorID = s.personID
    ORDER BY e.employeeID DESC
");
?>

<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-2">Employee Records</h1>
    <p class="text-gray-600 mb-4">Employees can only be added via the <span class="font-semibold text-blue-600">Person</span> page.</p>

    <table class="w-full border-collapse border border-gray-300">
        <thead class="bg-gray-200 text-center">
            <tr>
                <th class="border p-2">ID</th>
                <th class="border p-2">Name</th>
                <th class="border p-2">Work Shift</th>
                <th class="border p-2">Salary</th>
                <th class="border p-2">Hire Date</th>
                <th class="border p-2">Employment Type</th>
                <th class="border p-2">Department</th>
                <th class="border p-2">Supervisor</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr class="text-center">
                <td class="border p-2"><?= $row['employeeID'] ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['name']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['workShift']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['salary']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['hireDate']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['employmentType']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['deptName']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['supFirst'] . ' ' . $row['supLast']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
