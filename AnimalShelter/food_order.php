<?php
include 'db.php';
include 'header.php';
$result = $conn->query("SELECT * FROM `food_order`");
?>
<div class='container mx-auto p-4'>
    <h1 class='text-2xl font-bold mb-4'>food_order List</h1>
    <table class='min-w-full bg-white shadow-md rounded-lg overflow-hidden'>
        <thead class='bg-gray-200'>
<tr>
            <th class='py-2 px-4'>orderID</th>
            <th class='py-2 px-4'>orderDate</th>
            <th class='py-2 px-4'>quantity</th>
            <th class='py-2 px-4'>employeeID</th>
            <th class='py-2 px-4'>foodID</th>
        </tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr class='border-b hover:bg-gray-100'>
    <td class='py-2 px-4'><?= $row['orderID'] ?></td>
    <td class='py-2 px-4'><?= $row['orderDate'] ?></td>
    <td class='py-2 px-4'><?= $row['quantity'] ?></td>
    <td class='py-2 px-4'><?= $row['employeeID'] ?></td>
    <td class='py-2 px-4'><?= $row['foodID'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php include 'footer.php'; ?>