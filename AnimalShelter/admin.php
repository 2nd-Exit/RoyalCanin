<?php
require 'db.php';

// ===== ALLOWED ENTITIES =====
$allowed = ['person','employee','adopter','vet','department','animal','cage','food','food_order','adoption','treatment','medicine','vaccine'];
$entity = $_GET['entity'] ?? 'person';
if(!in_array($entity, $allowed)) $entity = 'person';

// ===== ENTITY FILES =====
$entityFiles = [
    'person' => 'person.php',
    'employee' => 'employee.php',
    'adopter' => 'adopter.php',
    'vet' => 'vet.php',
    'department' => 'department.php',
    'animal' => 'animal.php',
    'cage' => 'cage.php',
    'food' => 'food.php',
    'food_order' => 'food_order.php',
    'adoption' => 'adoption.php',
    'treatment' => 'treatment.php',
    'medicine' => 'medicine.php',
    'vaccine' => 'vaccine.php'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Royal Canin Admin Panel</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<header class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">🐾 Royal Canin Admin Panel</h1>

    <!-- Navigation Bar -->
    <nav class="flex flex-wrap gap-2">
        <?php
        foreach($allowed as $key){
            $label = ucfirst(str_replace('_',' ',$key));
            $active = ($entity === $key) ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400';
            echo "<a href='?entity=$key' class='px-4 py-2 rounded $active transition'>$label</a>";
        }
        ?>
    </nav>
</header>

<main class="bg-white p-6 rounded-xl shadow-lg max-w-6xl mx-auto border border-gray-200">
    <?php
    if(isset($entityFiles[$entity]) && file_exists($entityFiles[$entity])){
        include $entityFiles[$entity];
    } else {
        echo "<p class='text-red-500 font-semibold'>Page not found.</p>";
    }
    ?>
</main>

</body>
</html>
