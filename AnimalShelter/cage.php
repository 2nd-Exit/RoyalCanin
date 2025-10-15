<?php
include 'db.php';
include 'header.php';
$result = $conn->query("SELECT * FROM `cage`");
?>
<div class='container mx-auto p-4'>
    <h1 class='text-2xl font-bold mb-4'>cage List</h1>
    <table class='min-w-full bg-white shadow-md rounded-lg overflow-hidden'>
        <thead class='bg-gray-200'>
<tr>
            <th class='py-2 px-4'>cageID</th>
            <th class='py-2 px-4'>locateZone</th>
            <th class='py-2 px-4'>size</th>
            <th class='py-2 px-4'>cageStatus</th>
        </tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr class='border-b hover:bg-gray-100'>
    <td class='py-2 px-4'><?= $row['cageID'] ?></td>
    <td class='py-2 px-4'><?= $row['locateZone'] ?></td>
    <td class='py-2 px-4'><?= $row['size'] ?></td>
    <td class='py-2 px-4'><?= $row['cageStatus'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php include 'footer.php'; ?>