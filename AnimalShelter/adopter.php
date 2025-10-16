<?php
require 'db.php';

// SEARCH
$search = $_GET['search'] ?? '';
$where = '';
if ($search) {
    $s = $conn->real_escape_string($search);
    $where = "WHERE p.firstName LIKE '%$s%' OR p.lastName LIKE '%$s%' OR a.adopterID LIKE '%$s%'";
}

$result = $conn->query("
    SELECT a.adopterID, CONCAT(p.firstName, ' ', p.lastName) AS name, a.occupation, a.monthly_income, a.number_of_pets_owned
    FROM ADOPTER a
    JOIN PERSON p ON a.adopterID = p.personID
    ORDER BY a.adopterID DESC
");
?>

<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-2">Adopter Records</h1>
    <p class="text-gray-600 mb-4">Adopters can only be added via the <span class="font-semibold text-blue-600">Person</span> page.</p>

    <form method="GET" class="mb-4">
        <input type="text" name="search" placeholder="Search adopter..." value="<?= htmlspecialchars($search) ?>" class="border p-2 rounded w-full">
    </form>

    <table class="w-full border-collapse border border-gray-300">
        <thead class="bg-gray-200 text-center">
            <tr>
                <th class="border p-2">ID</th>
                <th class="border p-2">Name</th>
                <th class="border p-2">Ocupation</th>
                <th class="border p-2">Monthly Income</th>
                <th class="border p-2">Pets Owned</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr class="text-center">
                <td class="border p-2"><?= $row['adopterID'] ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['name']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['occupation']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['monthly_income']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['number_of_pets_owned']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
