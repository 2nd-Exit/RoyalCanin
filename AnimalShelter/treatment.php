<?php
// Fetch animals, vets, clinics, medicines, vaccines
$animals = $conn->query("SELECT * FROM ANIMAL");
$vets = $conn->query("SELECT * FROM VET v LEFT JOIN PERSON p ON v.personID = p.personID");
$clinics = $conn->query("SELECT * FROM CLINIC");
$medicines = $conn->query("SELECT * FROM MEDICINE")->fetch_all(MYSQLI_ASSOC);
$vaccines = $conn->query("SELECT * FROM VACCINE")->fetch_all(MYSQLI_ASSOC);
?>

<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Treatment Manager</h1>

    <form method="POST" class="bg-gray-50 p-4 rounded mb-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block">Animal</label>
                <select name="animalID" required class="border p-2 rounded w-full">
                    <option value="">Select Animal</option>
                    <?php while($a = $animals->fetch_assoc()): ?>
                        <option value="<?= $a['animalID'] ?>"><?= $a['animalName'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block">Vet</label>
                <select name="vetID" required class="border p-2 rounded w-full">
                    <option value="">Select Vet</option>
                    <?php while($v = $vets->fetch_assoc()): ?>
                        <option value="<?= $v['vetID'] ?>"><?= $v['firstName'].' '.$v['lastName'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block">Clinic</label>
                <select name="clinicID" required class="border p-2 rounded w-full">
                    <option value="">Select Clinic</option>
                    <?php while($c = $clinics->fetch_assoc()): ?>
                        <option value="<?= $c['clinicID'] ?>"><?= $c['clinicName'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block">Date</label>
                <input type="date" name="treatmentDate" required class="border p-2 rounded w-full">
            </div>
        </div>

        <!-- Medicines -->
        <div>
            <h3 class="font-semibold mb-2">Medicines</h3>
            <div id="medContainer"></div>
            <button type="button" onclick="addMedRow()" class="bg-green-500 hover:bg-green-700 text-white px-2 py-1 rounded mt-2">Add Medicine</button>
        </div>

        <!-- Vaccines -->
        <div>
            <h3 class="font-semibold mb-2">Vaccines</h3>
            <div id="vacContainer"></div>
            <button type="button" onclick="addVacRow()" class="bg-yellow-500 hover:bg-yellow-700 text-white px-2 py-1 rounded mt-2">Add Vaccine</button>
        </div>

        <button type="submit" name="createTreatment" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add Treatment
        </button>
    </form>
</div>

<script>
// PHP data to JS
const medicinesData = <?= json_encode($medicines) ?>;
const vaccinesData = <?= json_encode($vaccines) ?>;

function addMedRow() {
    const container = document.getElementById('medContainer');
    const div = document.createElement('div');
    div.classList.add('flex', 'gap-2', 'mb-2');
    div.innerHTML = `
        <select name="medicine[]" class="border p-2 rounded w-full" required>
            <option value="">Select Medicine</option>
            ${medicinesData.map(m => `<option value="${m.medicineID}">${m.medicineName}</option>`).join('')}
        </select>
        <button type="button" onclick="this.parentNode.remove()" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded">Remove</button>
    `;
    container.appendChild(div);
}

function addVacRow() {
    const container = document.getElementById('vacContainer');
    const div = document.createElement('div');
    div.classList.add('flex', 'gap-2', 'mb-2');
    div.innerHTML = `
        <select name="vaccine[]" class="border p-2 rounded w-full" required>
            <option value="">Select Vaccine</option>
            ${vaccinesData.map(v => `<option value="${v.vaccineID}">${v.vaccineName}</option>`).join('')}
        </select>
        <button type="button" onclick="this.parentNode.remove()" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded">Remove</button>
    `;
    container.appendChild(div);
}
</script>
