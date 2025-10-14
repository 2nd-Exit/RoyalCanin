// --- Mock Database Data Structure (Based on SQL Schema) ---
const mockDB = {
    persons: [
        { personID: 1, firstName: 'Alice', lastName: 'Johnson', gender: 'F', age: 35, email: 'alice.j@shelter.org' },
        { personID: 2, firstName: 'Bob', lastName: 'Smith', gender: 'M', age: 45, email: 'bob.s@shelter.org' },
        { personID: 3, firstName: 'Charlie', lastName: 'Brown', gender: 'M', age: 28, email: 'charlie.b@shelter.org' },
        { personID: 4, firstName: 'Diana', lastName: 'Prince', gender: 'F', age: 55, email: 'diana.p@clinic.com' },
        { personID: 5, firstName: 'Ethan', lastName: 'Hunt', gender: 'M', age: 32, email: 'ethan.h@home.com' },
    ],
    departments: [
        { deptID: 1, deptName: 'Animal Care' },
        { deptID: 2, deptName: 'Administration' }
    ],
    employees: [
        { employeeID: 1, hireDate: '2018-06-15', position: 'Animal Handler', deptID: 1 },
        { employeeID: 2, hireDate: '2015-01-01', position: 'Shelter Manager', deptID: 2 },
    ],
    adopters: [
        { adopterID: 5, occupation: 'Software Engineer', monthlyIncome: 9500.00 }
    ],
    cages: [
        { cageID: 101, locateZone: 'Dog Zone A', size: 'Large', cageStatus: 'Occupied' },
        { cageID: 201, locateZone: 'Cat Room B', size: 'Small', cageStatus: 'Occupied' },
    ],
    foods: [
        { foodID: 1, foodName: 'Royal Canin Dog', price: 15.50 },
        { foodID: 3, foodName: 'Purina Wet Cat', price: 1.25 }
    ],
    animals: [
        { animalID: 1, animalName: 'Max', species: 'Dog', breed: 'Labrador', age: 3, adoptionStatus: 'Adopted', healthCondition: 'Healthy', cageID: 101, foodID: 1, employeeID: 1 },
        { animalID: 2, animalName: 'Luna', species: 'Cat', breed: 'Siamese', age: 1, adoptionStatus: 'Available', healthCondition: 'Mild cold', cageID: 201, foodID: 3, employeeID: 1 },
        { animalID: 3, animalName: 'Rocky', species: 'Dog', breed: 'Beagle', age: 5, adoptionStatus: 'Available', healthCondition: 'Needs dental work', cageID: 101, foodID: 1, employeeID: 1 },
    ],
    adoptions: [
        { adoptionID: 5001, adoptionDate: '2024-09-15', adopterID: 5, animalID: 1 }
    ]
};

// Function to simulate joining data for the API response
function getAnimalDetails(animal) {
    const handler = mockDB.employees.find(e => e.employeeID === animal.employeeID);
    const handlerPerson = mockDB.persons.find(p => p.personID === handler?.employeeID);
    const cage = mockDB.cages.find(c => c.cageID === animal.cageID);
    const food = mockDB.foods.find(f => f.foodID === animal.foodID);

    return {
        ...animal,
        cageLocation: cage ? `${cage.locateZone} (Size: ${cage.size})` : 'N/A',
        foodName: food?.foodName || 'N/A',
        assignedHandler: handlerPerson ? `${handlerPerson.firstName} ${handlerPerson.lastName} (${handler.position})` : 'Unassigned'
    };
}

// --- API Router (Mocking Express routes) ---

const apiRouter = {
    '/api/animals': (method, data) => {
        if (method === 'GET') {
            // Return all animals with joined details
            return mockDB.animals.map(getAnimalDetails);
        } else if (method === 'POST') {
            // Simulate adding a new animal
            const newAnimal = { 
                animalID: Math.max(...mockDB.animals.map(a => a.animalID)) + 1, 
                ...data, 
                adoptionStatus: 'Available' 
            };
            mockDB.animals.push(newAnimal);
            return getAnimalDetails(newAnimal);
        }
        return { status: 405, message: 'Method Not Allowed' };
    },

    '/api/dashboard': (method) => {
        if (method === 'GET') {
            const available = mockDB.animals.filter(a => a.adoptionStatus === 'Available').length;
            const adopted = mockDB.animals.filter(a => a.adoptionStatus === 'Adopted').length;
            const total = mockDB.animals.length;
            const employees = mockDB.employees.length;

            return {
                totalAnimals: total,
                availableForAdoption: available,
                recentlyAdopted: adopted,
                totalStaff: employees,
                cagesInUse: mockDB.cages.filter(c => c.cageStatus === 'Occupied').length
            };
        }
        return { status: 405, message: 'Method Not Allowed' };
    }
};

// Global mock function to simulate fetching data from the backend
window.mockFetch = async (url, options = {}) => {
    const method = options.method || 'GET';
    const body = options.body ? JSON.parse(options.body) : {};

    const routeHandler = apiRouter[url];

    if (!routeHandler) {
        return { ok: false, status: 404, json: async () => ({ message: 'Not Found' }) };
    }

    try {
        const result = routeHandler(method, body);
        return {
            ok: true,
            status: 200,
            json: async () => result
        };
    } catch (error) {
        console.error('Mock API Error:', error);
        return { ok: false, status: 500, json: async () => ({ message: 'Internal Server Error' }) };
    }
};

console.log("Mock Backend API is ready. Use window.mockFetch() from the frontend.");
