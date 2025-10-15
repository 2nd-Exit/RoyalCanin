<?php
include 'db.php';
include 'header.php';
$result = $conn->query("SELECT * FROM `food`");
?>
<div class='container mx-auto p-4'>
    <h1 class='text-2xl font-bold mb-4'>food List</h1>
    <table class='min-w-full bg-white shadow-md rounded-lg overflow-hidden'>
        <thead class='bg-gray-200'>
<tr>
            <th class='py-2 px-4'>foodID</th>
            <th class='py-2 px-4'>foodName</th>
            <th class='py-2 px-4'>foodType</th>
            <th class='py-2 px-4'>quantityAvailable</th>
            <th class='py-2 px-4'>expirationDate</th>
            <th class='py-2 px-4'>price</th>
        </tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr class='border-b hover:bg-gray-100'>
    <td class='py-2 px-4'><?= $row['foodID'] ?></td>
    <td class='py-2 px-4'><?= $row['foodName'] ?></td>
    <td class='py-2 px-4'><?= $row['foodType'] ?></td>
    <td class='py-2 px-4'><?= $row['quantityAvailable'] ?></td>
    <td class='py-2 px-4'><?= $row['expirationDate'] ?></td>
    <td class='py-2 px-4'><?= $row['price'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php include 'footer.php'; ?>