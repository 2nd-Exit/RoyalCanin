<?php
require 'db.php';

// ===== FETCH DATA =====
$animals = $conn->query("SELECT * FROM ANIMAL ORDER BY animalName");
$vets = $conn->query("
    SELECT v.vetID, CONCAT(p.firstName,' ',p.lastName) AS name
    FROM VET v
    JOIN PERSON p ON v.vetID = p.personID
    ORDER BY p.firstName, p.lastName
");
$clinics = $conn->query("SELECT * FROM CLINIC ORDER BY clinicName");

$medicinesArr = [];
$medicines = $conn->query("SELECT * FROM MEDICINE ORDER BY medName");
while($m = $medicines->fetch_assoc()) $medicinesArr[] = $m;

$vaccinesArr = [];
$vaccines = $conn->query("SELECT * FROM VACCINE ORDER BY vacName");
while($v = $vaccines->fetch_assoc()) $vaccinesArr[] = $v;

// ===== HANDLE CREATE =====
if (isset($_POST['createTreatment'])) {
    $animalID = intval($_POST['animalID']);
    $vetID = intval($_POST['vetID']);
    $clinicID = intval($_POST['clinicID']);
    $treatmentDate = $_POST['treatmentDate'];
    $appointmentDate = $_POST['appointmentDate'];
    $notes = $conn->real_escape_string($_POST['notes']);

    $conn->query("INSERT INTO TREATMENT (animalID, vetID, clinicID, treatmentDate, appointmentDate, notes) 
                  VALUES ($animalID, $vetID, $clinicID, '$treatmentDate', '$appointmentDate', '$notes')");

    $treatmentID = $conn->insert_id;

    // Insert Medicines
    if(isset($_POST['medicine'])){
        foreach($_POST['medicine'] as $i => $medID){
            $medID = intval($medID);
            $dosage = floatval($_POST['medDosage'][$i]);
            $unit = $_POST['medUnit'][$i];
            if($medID) $conn->query("INSERT INTO TREATMENT_MEDICINE (treatmentID, medID, dosage, unit) VALUES ($treatmentID, $medID, $dosage, '$unit')");
        }
    }

    // Insert Vaccines
    if(isset($_POST['vaccine'])){
        foreach($_POST['vaccine'] as $i => $vacID){
            $vacID = intval($vacID);
            $dosage = floatval($_POST['vacDosage'][$i]);
            $unit = $_POST['vacUnit'][$i];
            if($vacID) $conn->query("INSERT INTO TREATMENT_VACCINE (treatmentID, vacID, dosage, unit) VALUES ($treatmentID, $vacID, $dosage, '$unit')");
        }
    }

    header("Location: treatment.php");
    exit;
}

// ===== HANDLE DELETE =====
if (isset($_GET['deleteTreatment'])) {
    $id = intval($_GET['deleteTreatment']);
    $conn->query("DELETE FROM TREATMENT WHERE treatmentID=$id");
    header("Location: treatment.php");
    exit;
}

// ===== FETCH TREATMENTS =====
$treatments = $conn->query("
    SELECT t.*, a.animalName, CONCAT(p.firstName,' ',p.lastName) AS vetName, c.clinicName
    FROM TREATMENT t
    LEFT JOIN ANIMAL a ON t.animalID = a.animalID
    LEFT JOIN VET v ON t.vetID = v.vetID
    LEFT JOIN PERSON p ON v.vetID = p.personID
    LEFT JOIN CLINIC c ON t.clinicID = c.clinicID
    ORDER BY t.treatmentDate DESC
");
?>

<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Treatment Manager</h1>

    <!-- Add Treatment Form -->
    <form method="POST" class="bg-gray-50 p-4 rounded mb-6 grid grid-cols-2 gap-4">
        <div>
            <label>Treatment Date</label>
            <input type="date" name="treatmentDate" required class="border p-2 rounded w-full">
        </div>
        <div>
            <label>Appointment Date</label>
            <input type="date" name="appointmentDate" required class="border p-2 rounded w-full">
        </div>
        <div>
            <label>Animal</label>
            <select name="animalID" required class="border p-2 rounded w-full">
                <option value="">Select Animal</option>
                <?php foreach($animals as $a): ?>
                <option value="<?= $a['animalID'] ?>"><?= htmlspecialchars($a['animalName']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Vet</label>
            <select name="vetID" required class="border p-2 rounded w-full">
                <option value="">Select Vet</option>
                <?php foreach($vets as $v): ?>
                <option value="<?= $v['vetID'] ?>"><?= htmlspecialchars($v['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Clinic</label>
            <select name="clinicID" required class="border p-2 rounded w-full">
                <option value="">Select Clinic</option>
                <?php foreach($clinics as $c): ?>
                <option value="<?= $c['clinicID'] ?>"><?= htmlspecialchars($c['clinicName']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Notes</label>
            <textarea name="notes" class="border p-2 rounded w-full"></textarea>
        </div>

        <!-- Medicines -->
        <div class="col-span-2">
            <label>Medicines</label>
            <div id="medContainer" class="space-y-2">
                <div class="grid grid-cols-4 gap-2">
                    <select name="medicine[]" class="border p-2 rounded w-full">
                        <option value="">Select Medicine</option>
                        <?php foreach($medicinesArr as $m): ?>
                        <option value="<?= $m['medID'] ?>"><?= htmlspecialchars($m['medName']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" step="0.01" name="medDosage[]" placeholder="Dosage" class="border p-2 rounded w-full">
                    <select name="medUnit[]" class="border p-2 rounded w-full">
                        <option value="ml">ml</option>
                        <option value="bottle">bottle</option>
                        <option value="tablet">tablet</option>
                        <option value="capsule">capsule</option>
                        <option value="drops">drops</option>
                    </select>
                    <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 text-white px-2 rounded">Remove</button>
                </div>
            </div>
            <button type="button" onclick="addMedicine()" class="bg-blue-500 text-white px-4 py-1 rounded mt-2">Add Medicine</button>
        </div>

        <!-- Vaccines -->
        <div class="col-span-2">
            <label>Vaccines</label>
            <div id="vacContainer" class="space-y-2">
                <div class="grid grid-cols-4 gap-2">
                    <select name="vaccine[]" class="border p-2 rounded w-full">
                        <option value="">Select Vaccine</option>
                        <?php foreach($vaccinesArr as $v): ?>
                        <option value="<?= $v['vacID'] ?>"><?= htmlspecialchars($v['vacName']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" step="0.01" name="vacDosage[]" placeholder="Dosage" class="border p-2 rounded w-full">
                    <select name="vacUnit[]" class="border p-2 rounded w-full">
                        <option value="ml">ml</option>
                        <option value="dose">dose</option>
                    </select>
                    <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 text-white px-2 rounded">Remove</button>
                </div>
            </div>
            <button type="button" onclick="addVaccine()" class="bg-blue-500 text-white px-4 py-1 rounded mt-2">Add Vaccine</button>
        </div>

        <div class="col-span-2">
            <button type="submit" name="createTreatment" class="bg-green-600 text-white px-6 py-2 rounded">Save Treatment</button>
        </div>
    </form>

    <!-- Treatment Table -->
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200 text-center">
                <th>ID</th>
                <th>Treatment Date</th>
                <th>Appointment</th>
                <th>Animal</th>
                <th>Vet</th>
                <th>Clinic</th>
                <th>Medicines</th>
                <th>Vaccines</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while($t = $treatments->fetch_assoc()):
            $medList = [];
            $meds = $conn->query("SELECT m.medName, tm.dosage, tm.unit FROM TREATMENT_MEDICINE tm JOIN MEDICINE m ON tm.medID=m.medID WHERE tm.treatmentID={$t['treatmentID']}");
            while($m = $meds->fetch_assoc()) $medList[] = "{$m['medName']} ({$m['dosage']} {$m['unit']})";

            $vacList = [];
            $vacs = $conn->query("SELECT v.vacName, tv.dosage, tv.unit FROM TREATMENT_VACCINE tv JOIN VACCINE v ON tv.vacID=v.vacID WHERE tv.treatmentID={$t['treatmentID']}");
            while($v = $vacs->fetch_assoc()) $vacList[] = "{$v['vacName']} ({$v['dosage']} {$v['unit']})";
        ?>
        <tr class="text-center">
            <td><?= $t['treatmentID'] ?></td>
            <td><?= $t['treatmentDate'] ?></td>
            <td><?= $t['appointmentDate'] ?></td>
            <td><?= htmlspecialchars($t['animalName']) ?></td>
            <td><?= htmlspecialchars($t['vetName']) ?></td>
            <td><?= htmlspecialchars($t['clinicName']) ?></td>
            <td><?= !empty($medList) ? implode('<br>', $medList) : '-' ?></td>
            <td><?= !empty($vacList) ? implode('<br>', $vacList) : '-' ?></td>
            <td><?= htmlspecialchars($t['notes']) ?></td>
            <td>
                <a href="?deleteTreatment=<?= $t['treatmentID'] ?>" onclick="return confirm('Are you sure?')" class="bg-red-500 text-white px-2 rounded">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function addMedicine() {
    const container = document.getElementById('medContainer');
    const div = document.createElement('div');
    div.classList.add('grid','grid-cols-4','gap-2');
    div.innerHTML = `
        <select name="medicine[]" class="border p-2 rounded w-full">
            <option value="">Select Medicine</option>
            <?php foreach($medicinesArr as $m): ?>
            <option value="<?= $m['medID'] ?>"><?= htmlspecialchars($m['medName']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" step="0.01" name="medDosage[]" placeholder="Dosage" class="border p-2 rounded w-full">
        <select name="medUnit[]" class="border p-2 rounded w-full">
            <option value="ml">ml</option>
            <option value="bottle">bottle</option>
            <option value="tablet">tablet</option>
            <option value="capsule">capsule</option>
            <option value="drops">drops</option>
        </select>
        <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 text-white px-2 rounded">Remove</button>
    `;
    container.appendChild(div);
}

function addVaccine() {
    const container = document.getElementById('vacContainer');
    const div = document.createElement('div');
    div.classList.add('grid','grid-cols-4','gap-2');
    div.innerHTML = `
        <select name="vaccine[]" class="border p-2 rounded w-full">
            <option value="">Select Vaccine</option>
            <?php foreach($vaccinesArr as $v): ?>
            <option value="<?= $v['vacID'] ?>"><?= htmlspecialchars($v['vacName']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" step="0.01" name="vacDosage[]" placeholder="Dosage" class="border p-2 rounded w-full">
        <select name="vacUnit[]" class="border p-2 rounded w-full">
            <option value="ml">ml</option>
            <option value="dose">dose</option>
        </select>
        <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 text-white px-2 rounded">Remove</button>
    `;
    container.appendChild(div);
}
</script>
