-- Helper function to ensure consistent date format (adjust based on your RDBMS if needed)
SET DATEFORMAT YMD;

-- 1. PERSON (1-10)
INSERT INTO PERSON (personID, firstName, lastName, gender, age, phoneNo, email, date_of_birth, address) VALUES
(1, 'Alice', 'Johnson', 'F', 30, '555-1001', 'alice.j@shelter.org', '1995-04-12', '101 Main St, Anytown'), -- Employee
(2, 'Bob', 'Smith', 'M', 45, '555-1002', 'bob.s@shelter.org', '1980-11-20', '202 Oak Ave, Anytown'),    -- Employee (Supervisor)
(3, 'Charlie', 'Davis', 'M', 55, '555-1003', 'charlie.d@vetcare.com', '1970-07-05', '303 Pine Ln, Anytown'),  -- Vet
(4, 'Diana', 'Rivera', 'F', 38, '555-2001', 'diana.r@gmail.com', '1987-01-25', '404 Cedar Rd, Othercity'), -- Adopter
(5, 'Ethan', 'Wong', 'M', 25, '555-1005', 'ethan.w@shelter.org', '2000-02-14', '505 Elm Pl, Anytown'),    -- Employee
(6, 'Fiona', 'Chen', 'F', 32, '555-2002', 'fiona.c@hotmail.com', '1993-08-01', '606 Maple Dr, Othercity'), -- Adopter
(7, 'George', 'Bao', 'M', 60, '555-1007', 'george.b@vetcare.com', '1965-03-17', '707 Birch Blvd, Anytown'), -- Vet
(8, 'Hannah', 'Lee', 'F', 22, '555-1008', 'hannah.l@shelter.org', '2003-09-28', '808 Spruce St, Anytown'), -- Employee
(9, 'Ivan', 'Petrov', 'M', 40, '555-2003', 'ivan.p@yahoo.com', '1985-06-03', '909 Willow Way, Othercity'), -- Adopter
(10, 'Jasmine', 'Kaur', 'F', 28, '555-1010', 'jasmine.k@shelter.org', '1997-12-19', '110 Poplar Pt, Anytown'); -- Employee

-- 2. DEPARTMENT (1-3)
INSERT INTO DEPARTMENT (deptID, deptName) VALUES
(1, 'Animal Care'),
(2, 'Adoption Services'),
(3, 'Administration');

-- 3. CLINIC (1-2)
INSERT INTO CLINIC (clinicID, clinicName, location, telephoneNo) VALUES
(1, 'City Vet Clinic', 'Downtown Medical Center', '555-3001'),
(2, 'Suburban Pet Health', 'West End Plaza', '555-3002');

-- 4. EMPLOYEE (1, 2, 5, 8, 10)
INSERT INTO EMPLOYEE (employeeID, hireDate, workShift, salary, employmentType, position, deptID, supervisorID) VALUES
(2, '2015-08-01', 'Day', 65000.00, 'Full-Time', 'Shelter Manager', 3, NULL), -- Bob is the top supervisor
(1, '2020-03-15', 'Day', 45000.00, 'Full-Time', 'Animal Technician', 1, 2), -- Reports to Bob
(5, '2022-06-20', 'Morning', 35000.00, 'Part-Time', 'Cage Cleaner', 1, 1), -- Reports to Alice (who is also an employee)
(8, '2024-01-10', 'Day', 48000.00, 'Full-Time', 'Adoption Counselor', 2, 2), -- Reports to Bob
(10, '2023-09-01', 'Night', 42000.00, 'Full-Time', 'Animal Technician', 1, 1); -- Reports to Alice

-- 5. ADOPTER (4, 6, 9)
INSERT INTO ADOPTER (adopterID, occupation, monthlyIncome, number_of_pets_owned) VALUES
(4, 'Software Engineer', 8500.00, 1),
(6, 'Teacher', 4200.00, 0),
(9, 'Truck Driver', 6000.00, 2);

-- 6. VET (3, 7)
INSERT INTO VET (vetID, vet_license_no, specialization, years_of_experience, clinicID) VALUES
(3, 'VET-L-4589', 'Surgery', 30, 1),
(7, 'VET-L-9012', 'Internal Medicine', 15, 2);

-- 7. CAGE (1-5)
INSERT INTO CAGE (cageID, locationZone, size, cageStatus) VALUES
(1, 'Dog Wing A', 'Large', 'Occupied'),
(2, 'Cat House 1', 'Small', 'Available'),
(3, 'Dog Wing B', 'Medium', 'Occupied'),
(4, 'Exotics', 'Small', 'Cleaning'),
(5, 'Dog Wing A', 'Medium', 'Occupied');

-- 8. FOOD (1-3)
INSERT INTO FOOD (foodID, foodName, foodType, expirationDate, price, quantityAvailable) VALUES
(1, 'Premium Dog Kibble', 'Dry Dog Food', '2026-03-01', 50.00, 15),
(2, 'Salmon Pate', 'Wet Cat Food', '2025-11-15', 2.50, 100),
(3, 'Rabbit Pellets', 'Dry Rabbit Food', '2025-06-01', 15.75, 5);

-- 9. MEDICINE (1-2)
INSERT INTO MEDICINE (medID, medName, medType) VALUES
(1, 'Amoxicillin', 'Antibiotic'),
(2, 'Carprofen', 'Pain Reliever');

-- 10. VACCINE (1-2)
INSERT INTO VACCINE (vacID, vacName, vacType) VALUES
(1, 'DHLPP', 'Canine Vaccine'),
(2, 'FVRCP', 'Feline Vaccine');

-- 11. ANIMAL (1-5)
-- employeeID is the employee responsible for care (Alice=1, Jasmine=10)
INSERT INTO ANIMAL (animalID, animalName, species, breed, age, gender, weight, arrivalDate, adoptionStatus, healthCondition, cageID, foodID, employeeID) VALUES
(101, 'Sparky', 'Dog', 'Beagle', 5, 'M', 15.50, '2024-05-10', 'Available', 'Healthy', 1, 1, 1),
(102, 'Whiskers', 'Cat', 'Siamese', 2, 'F', 3.20, '2024-06-01', 'Pending', 'Mild respiratory infection', 3, 2, 1),
(103, 'Buster', 'Dog', 'Bulldog', 8, 'M', 25.00, '2024-01-20', 'Adopted', 'Hip dysplasia', 5, 1, 10),
(104, 'Thumper', 'Rabbit', 'Dutch', 1, 'F', 1.50, '2024-07-15', 'Available', 'Healthy', NULL, 3, 10),
(105, 'Shadow', 'Cat', 'Maine Coon', 4, 'M', 5.80, '2024-04-05', 'Available', 'Recently Neutered', 3, 2, 1);

-- 12. ORDER (1-2)
-- Employee 1 (Alice) buying food
INSERT INTO "ORDER" (orderID, orderDate, quantity, employeeID, foodID) VALUES
(1, '2024-08-15', 5, 1, 1), -- Alice orders 5 units of Premium Dog Kibble
(2, '2024-09-01', 20, 10, 2); -- Jasmine orders 20 units of Salmon Pate

-- 13. ADOPTION (1-2)
-- Buster (103) adopted by Diana (4)
INSERT INTO ADOPTION (adoptionID, adoptionDate, adopterID, animalID) VALUES
(1, '2024-02-05', 4, 103), -- Buster adopted by Diana
(2, '2024-05-15', 9, 103); -- Buster returned and readopted by Ivan

-- 14. TREATMENT (1-3)
-- Sparky (101) gets a vaccine at Clinic 1 by Vet 3
-- Whiskers (102) gets an antibiotic at Clinic 2 by Vet 7
-- Shadow (105) gets both a vaccine and medicine at Clinic 1 by Vet 3
INSERT INTO TREATMENT (treatmentID, treatmentDate, appointmentDate, notes, vetID, animalID, clinicID, vacID, medID) VALUES
(1, '2024-05-11', '2024-05-11', 'Routine first vaccination (DHLPP). Animal appears healthy.', 3, 101, 1, 1, NULL),
(2, '2024-06-03', '2024-06-02', 'Administered Amoxicillin for mild URI. Follow-up in 7 days.', 7, 102, 2, NULL, 1),
(3, '2024-04-10', '2024-04-10', 'FVRCP vaccine and pain management (Carprofen) post-op.', 3, 105, 1, 2, 2);
