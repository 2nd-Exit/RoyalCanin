<?php
include 'db.php';
include 'header.php';
$result = $conn->query("SELECT * FROM `department`");
?>
<div class='container mx-auto p-4'>
    <h1 class='text-2xl font-bold mb-4'>department List</h1>
    <table class='min-w-full bg-white shadow-md rounded-lg overflow-hidden'>
        <thead class='bg-gray-200'>
<tr>
            <th class='py-2 px-4'>deptID</th>
            <th class='py-2 px-4'>deptName</th>
        </tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr class='border-b hover:bg-gray-100'>
    <td class='py-2 px-4'><?= $row['deptID'] ?></td>
    <td class='py-2 px-4'><?= $row['deptName'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php include 'footer.php'; ?>