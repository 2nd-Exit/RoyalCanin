<?php
include 'db.php';
include 'header.php';
$result = $conn->query("SELECT * FROM `vaccine`");
?>
<div class='container mx-auto p-4'>
    <h1 class='text-2xl font-bold mb-4'>vaccine List</h1>
    <table class='min-w-full bg-white shadow-md rounded-lg overflow-hidden'>
        <thead class='bg-gray-200'>
<tr>
            <th class='py-2 px-4'>vacID</th>
            <th class='py-2 px-4'>vacName</th>
            <th class='py-2 px-4'>vacType</th>
            <th class='py-2 px-4'>price</th>
        </tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr class='border-b hover:bg-gray-100'>
    <td class='py-2 px-4'><?= $row['vacID'] ?></td>
    <td class='py-2 px-4'><?= $row['vacName'] ?></td>
    <td class='py-2 px-4'><?= $row['vacType'] ?></td>
    <td class='py-2 px-4'><?= $row['price'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php include 'footer.php'; ?>