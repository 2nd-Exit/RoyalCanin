<?php
include 'db.php';
include 'header.php';
$result = $conn->query("SELECT * FROM `treatment_vaccine`");
?>
<div class='container mx-auto p-4'>
    <h1 class='text-2xl font-bold mb-4'>treatment_vaccine List</h1>
    <table class='min-w-full bg-white shadow-md rounded-lg overflow-hidden'>
        <thead class='bg-gray-200'>
<tr>
            <th class='py-2 px-4'>treatmentID</th>
            <th class='py-2 px-4'>vacID</th>
            <th class='py-2 px-4'>dosage</th>
            <th class='py-2 px-4'>unit</th>
        </tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr class='border-b hover:bg-gray-100'>
    <td class='py-2 px-4'><?= $row['treatmentID'] ?></td>
    <td class='py-2 px-4'><?= $row['vacID'] ?></td>
    <td class='py-2 px-4'><?= $row['dosage'] ?></td>
    <td class='py-2 px-4'><?= $row['unit'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php include 'footer.php'; ?>