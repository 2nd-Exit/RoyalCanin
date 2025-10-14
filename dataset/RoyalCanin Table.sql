
-- PERSON TABLE
CREATE TABLE PERSON (
    personID INT PRIMARY KEY,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    gender CHAR(1), -- 'M' or 'F'
    age INT,
    phoneNo VARCHAR(15),
    email VARCHAR(100) UNIQUE,
    date_of_birth DATE,
    address VARCHAR(255)
);

-- DEPARTMENT TABLE
CREATE TABLE DEPARTMENT (
    deptID INT PRIMARY KEY,
    deptName VARCHAR(100) NOT NULL UNIQUE
);

-- CLINIC TABLE
CREATE TABLE CLINIC (
    clinicID INT PRIMARY KEY,
    clinicName VARCHAR(100) NOT NULL,
    location VARCHAR(255),
    telephoneNo VARCHAR(15)
);

-- 2. Derived People/Roles Tables (Child Tables of PERSON)
---------------------------------------------------------------------------------

-- EMPLOYEE TABLE
CREATE TABLE EMPLOYEE (
    employeeID INT PRIMARY KEY,
    hireDate DATE NOT NULL,
    workShift VARCHAR(50), -- e.g., 'Morning', 'Day', 'Night'
    salary DECIMAL(10, 2),
    employmentType VARCHAR(50), -- e.g., 'Full-Time', 'Part-Time'
    position VARCHAR(50) NOT NULL,
    
    -- Foreign Keys
    deptID INT NOT NULL,
    supervisorID INT, -- Self-referencing FK
    
    FOREIGN KEY (employeeID) REFERENCES PERSON(personID),
    FOREIGN KEY (deptID) REFERENCES DEPARTMENT(deptID),
    FOREIGN KEY (supervisorID) REFERENCES EMPLOYEE(employeeID)
);

-- ADOPTER TABLE
CREATE TABLE ADOPTER (
    adopterID INT PRIMARY KEY,
    occupation VARCHAR(100),
    monthlyIncome DECIMAL(10, 2),
    number_of_pets_owned INT DEFAULT 0,
    
    -- Foreign Key
    FOREIGN KEY (adopterID) REFERENCES PERSON(personID)
);

-- VET TABLE
CREATE TABLE VET (
    vetID INT PRIMARY KEY,
    vet_license_no VARCHAR(50) UNIQUE NOT NULL,
    specialization VARCHAR(100),
    years_of_experience INT,
    
    -- Foreign Key
    clinicID INT NOT NULL,
    
    FOREIGN KEY (vetID) REFERENCES PERSON(personID),
    FOREIGN KEY (clinicID) REFERENCES CLINIC(clinicID)
);


-- 3. Inventory & Facility Tables
---------------------------------------------------------------------------------

-- CAGE TABLE
CREATE TABLE CAGE (
    cageID INT PRIMARY KEY,
    locationZone VARCHAR(50) NOT NULL, -- e.g., 'Dog Wing A', 'Cat House 3'
    size VARCHAR(20), -- e.g., 'Large', 'Medium', 'Small'
    cageStatus VARCHAR(20) -- e.g., 'Occupied', 'Available', 'Cleaning'
);

-- FOOD TABLE
CREATE TABLE FOOD (
    foodID INT PRIMARY KEY,
    foodName VARCHAR(100) NOT NULL,
    foodType VARCHAR(50), -- e.g., 'Dog Kibble', 'Cat Wet Food'
    expirationDate DATE,
    price DECIMAL(10, 2) NOT NULL,
    quantityAvailable INT
);

-- MEDICINE TABLE
CREATE TABLE MEDICINE (
    medID INT PRIMARY KEY,
    medName VARCHAR(100) NOT NULL,
    medType VARCHAR(50) -- e.g., 'Antibiotic', 'Pain Reliever'
);

-- VACCINE TABLE
CREATE TABLE VACCINE (
    vacID INT PRIMARY KEY,
    vacName VARCHAR(100) NOT NULL,
    vacType VARCHAR(50) -- e.g., 'Rabies', 'Distemper'
);


-- 4. Animal and Associative Entities
---------------------------------------------------------------------------------

-- ANIMAL TABLE
CREATE TABLE ANIMAL (
    animalID INT PRIMARY KEY,
    animalName VARCHAR(50),
    species VARCHAR(50) NOT NULL, -- 'Dog', 'Cat', 'Rabbit', etc.
    breed VARCHAR(50),
    age INT,
    gender CHAR(1), -- 'M' or 'F'
    weight DECIMAL(10, 2),
    arrivalDate DATE NOT NULL,
    adoptionStatus VARCHAR(20), -- 'Available', 'Pending', 'Adopted', 'Euthanized'
    healthCondition VARCHAR(255),
    
    -- Foreign Keys
    cageID INT,
    foodID INT,
    employeeID INT NOT NULL, -- Employee responsible for the animal
    
    FOREIGN KEY (cageID) REFERENCES CAGE(cageID),
    FOREIGN KEY (foodID) REFERENCES FOOD(foodID),
    FOREIGN KEY (employeeID) REFERENCES EMPLOYEE(employeeID)
);

-- ORDER (Associative Entity for STAFF - FOOD)
CREATE TABLE "ORDER" (
    orderID INT PRIMARY KEY,
    orderDate DATE NOT NULL,
    quantity INT NOT NULL,
    
    -- Foreign Keys
    employeeID INT NOT NULL,
    foodID INT NOT NULL,
    
    FOREIGN KEY (employeeID) REFERENCES EMPLOYEE(employeeID),
    FOREIGN KEY (foodID) REFERENCES FOOD(foodID)
);

-- ADOPTION (Associative Entity for ADOPTER - ANIMAL)
CREATE TABLE ADOPTION (
    adoptionID INT PRIMARY KEY,
    adoptionDate DATE NOT NULL,
    
    -- Foreign Keys
    adopterID INT NOT NULL,
    animalID INT NOT NULL,
    
    FOREIGN KEY (adopterID) REFERENCES ADOPTER(adopterID),
    FOREIGN KEY (animalID) REFERENCES ANIMAL(animalID)
);

-- TREATMENT (Associative Entity for ANIMAL - VET - CLINIC - MEDICINE - VACCINE)
-- Note: vacID and medID are NULLable since a treatment may only include one or the other.
CREATE TABLE TREATMENT (
    treatmentID INT PRIMARY KEY,
    treatmentDate DATE NOT NULL,
    appointmentDate DATE,
    notes TEXT,
    
    -- Foreign Keys
    vetID INT NOT NULL,
    animalID INT NOT NULL,
    clinicID INT NOT NULL,
    vacID INT, -- Optional
    medID INT, -- Optional
    
    FOREIGN KEY (vetID) REFERENCES VET(vetID),
    FOREIGN KEY (animalID) REFERENCES ANIMAL(animalID),
    FOREIGN KEY (clinicID) REFERENCES CLINIC(clinicID),
    FOREIGN KEY (vacID) REFERENCES VACCINE(vacID),
    FOREIGN KEY (medID) REFERENCES MEDICINE(medID)
);
