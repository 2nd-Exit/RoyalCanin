-- Create database
DROP DATABASE IF EXISTS AnimalShelter;
CREATE DATABASE AnimalShelter;

-- Select database
USE AnimalShelter;

-- PERSON TABLE 
CREATE TABLE PERSON ( 
	personID INT AUTO_INCREMENT PRIMARY KEY, 
    firstName VARCHAR(50) NOT NULL, 
    lastName VARCHAR(50) NOT NULL, 
    gender ENUM('Male', 'Female', 'Other'), 
    address VARCHAR(255), email VARCHAR(100) UNIQUE, 
    date_of_birth DATE, phoneNo VARCHAR(15) 
); 

-- DEPARTMENT TABLE 
CREATE TABLE DEPARTMENT ( 
	deptID INT AUTO_INCREMENT PRIMARY KEY, 
    deptName VARCHAR(100) NOT NULL UNIQUE 
); 

-- CLINIC TABLE 
CREATE TABLE CLINIC ( 
	clinicID INT AUTO_INCREMENT PRIMARY KEY, 
    clinicName VARCHAR(100) NOT NULL, 
    address VARCHAR(255), 
    telephoneNo VARCHAR(15) 
); 

-- VET TABLE 
CREATE TABLE VET ( 
	vetID INT PRIMARY KEY, 
    vet_license_no VARCHAR(50) UNIQUE NOT NULL, 
    specialization VARCHAR(100), 
    years_of_experience INT, 
    
    clinicID INT NOT NULL, 
    
    FOREIGN KEY (vetID) REFERENCES PERSON(personID), 
    FOREIGN KEY (clinicID) REFERENCES CLINIC(clinicID) 
); 

-- EMPLOYEE TABLE 
CREATE TABLE EMPLOYEE ( 
	employeeID INT PRIMARY KEY, 
    hireDate DATE NOT NULL, 
    workShift ENUM('Morning', 'Afternoon', 'Evening', 'Night'), 
    salary DECIMAL(10,2), 
    employmentType ENUM('Full-Time', 'Part-Time', 'Volunteer'), 
    
    deptID INT NOT NULL, 
    supervisorID INT, 
    
    FOREIGN KEY (employeeID) REFERENCES PERSON(personID), 
    FOREIGN KEY (deptID) REFERENCES DEPARTMENT(deptID), 
    FOREIGN KEY (supervisorID) REFERENCES EMPLOYEE(employeeID) 
); 

-- ADOPTER TABLE 
CREATE TABLE ADOPTER ( 
	adopterID INT PRIMARY KEY, 
    occupation VARCHAR(100), 
    monthly_income DECIMAL(10,2) CHECK (monthly_income >= 15000), 
    number_of_pets_owned INT, 
    
    FOREIGN KEY (adopterID) REFERENCES PERSON(personID) 
); 

-- CAGE TABLE 
CREATE TABLE CAGE ( 
	cageID INT AUTO_INCREMENT PRIMARY KEY, 
    locateZone VARCHAR(100), 
    size ENUM('XL', 'L', 'M', 'S'), 
    cageStatus ENUM('Available', 'Occupied') 
); 

-- FOOD TABLE 
CREATE TABLE FOOD ( 
	foodID INT AUTO_INCREMENT PRIMARY KEY, 
    foodName VARCHAR(100) NOT NULL, 
    foodType VARCHAR(100) NOT NULL, 
    quantityAvailable INT, 
    expirationDate DATE, 
    price DECIMAL(10,2) 
); 

-- ANIMAL TABLE 
CREATE TABLE ANIMAL ( 
	animalID INT AUTO_INCREMENT PRIMARY KEY, 
    animalName VARCHAR(100), 
    species VARCHAR(50) NOT NULL, 
    breed VARCHAR(50), age INT, 
    gender ENUM('Male', 'Female'), 
    weight DECIMAL(10,2) CHECK (weight >= 0), 
    arrivalDate DATE NOT NULL, 
    adoptionStatus ENUM('Available', 'Pending', 'Adopted', 'Euthanized') DEFAULT 'Available', 
    healthCondition VARCHAR(255), 
    
    cageID INT NOT NULL, 
    foodID INT NOT NULL, 
    employeeID INT NOT NULL, 
    
    FOREIGN KEY (cageID) REFERENCES CAGE(cageID), 
    FOREIGN KEY (foodID) REFERENCES FOOD(foodID), 
    FOREIGN KEY (employeeID) REFERENCES EMPLOYEE(employeeID) 
); 

-- FOOD_ORDER TABLE 
CREATE TABLE FOOD_ORDER ( 
	orderID INT AUTO_INCREMENT PRIMARY KEY, 
    orderDate DATE NOT NULL, 
    quantity INT NOT NULL CHECK (quantity > 0), 
    
    employeeID INT NOT NULL, 
    foodID INT NOT NULL, 
    
    FOREIGN KEY (employeeID) REFERENCES EMPLOYEE(employeeID), 
    FOREIGN KEY (foodID) REFERENCES FOOD(foodID) 
); 

-- ADOPTION TABLE 
CREATE TABLE ADOPTION ( 
	adoptionID INT AUTO_INCREMENT PRIMARY KEY, 
    adoptionDate DATE NOT NULL, 
    
    adopterID INT NOT NULL, 
    animalID INT NOT NULL, 
    
    FOREIGN KEY (adopterID) REFERENCES ADOPTER(adopterID), 
    FOREIGN KEY (animalID) REFERENCES ANIMAL(animalID)
); 

-- MEDICINE TABLE 
CREATE TABLE MEDICINE ( 
	medID INT AUTO_INCREMENT PRIMARY KEY, 
    medName VARCHAR(100) NOT NULL, 
    medType VARCHAR(50), 
    price DECIMAL(10,2) 
); 

-- VACCINE TABLE 
CREATE TABLE VACCINE ( 
	vacID INT AUTO_INCREMENT PRIMARY KEY, 
    vacName VARCHAR(100) NOT NULL, 
    vacType VARCHAR(50), 
    price DECIMAL(10,2) 
); 

-- TREATMENT TABLE 
CREATE TABLE TREATMENT ( 
	treatmentID INT AUTO_INCREMENT PRIMARY KEY, 
    treatmentDate DATE NOT NULL, 
    appointmentDate DATE, 
    notes TEXT, 
    
    vetID INT NOT NULL, 
    animalID INT NOT NULL, 
    clinicID INT NOT NULL, 
    
    FOREIGN KEY (vetID) REFERENCES VET(vetID), 
    FOREIGN KEY (animalID) REFERENCES ANIMAL(animalID), 
    FOREIGN KEY (clinicID) REFERENCES CLINIC(clinicID) 
); 

-- TREATMENT_MEDICINE 
CREATE TABLE TREATMENT_MEDICINE ( 
	treatmentID INT NOT NULL, 
    medID INT NOT NULL, 
    dosage DECIMAL(5,2), 
    unit ENUM('ml','bottle','tablet','capsule','drops'), 
    
    PRIMARY KEY (treatmentID, medID), 
    FOREIGN KEY (treatmentID) REFERENCES TREATMENT(treatmentID), 
    FOREIGN KEY (medID) REFERENCES MEDICINE(medID) 
); 

-- TREATMENT_VACCINE 
CREATE TABLE TREATMENT_VACCINE ( 
	treatmentID INT NOT NULL, 
    vacID INT NOT NULL, 
    dosage DECIMAL(5,2), 
    unit ENUM('ml','dose'), 
    
    PRIMARY KEY (treatmentID, vacID), 
    FOREIGN KEY (treatmentID) REFERENCES TREATMENT(treatmentID), 
    FOREIGN KEY (vacID) REFERENCES VACCINE(vacID) 
);