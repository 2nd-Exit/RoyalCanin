<?php
require 'db.php';

// SEARCH
$search = $_GET['search'] ?? '';
$where = '';
if ($search) {
    $s = $conn->real_escape_string($search);
    $where = "WHERE p.firstName LIKE '%$s%' OR p.lastName LIKE '%$s%' OR v.vet_license_no LIKE '%$s%' OR v.specialization LIKE '%$s%'";
}

$result = $conn->query("
    SELECT v.vetID, CONCAT(p.firstName, ' ', p.lastName) AS name, v.vet_license_no, v.specialization, v.years_of_experience, c.clinicName
    FROM VET v
    JOIN PERSON p ON v.vetID = p.personID
    LEFT JOIN CLINIC c ON v.clinicID = c.clinicID
    ORDER BY v.vetID DESC
");
?>

<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-2">Vet Records</h1>
    <p class="text-gray-600 mb-4">Vets can only be added via the <span class="font-semibold text-blue-600">Person</span> page.</p>

    <form method="GET" class="mb-4">
        <input type="text" name="search" placeholder="Search vet..." value="<?= htmlspecialchars($search) ?>" class="border p-2 rounded w-full">
    </form>

    <table class="w-full border-collapse border border-gray-300">
        <thead class="bg-gray-200 text-center">
            <tr>
                <th class="border p-2">ID</th>
                <th class="border p-2">Name</th>
                <th class="border p-2">License No</th>
                <th class="border p-2">Specialization</th>
                <th class="border p-2">Experience (Years)</th>
                <th class="border p-2">Clinic</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr class="text-center">
                <td class="border p-2"><?= $row['vetID'] ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['name']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['vet_license_no']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['specialization']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['years_of_experience']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['clinicName']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
