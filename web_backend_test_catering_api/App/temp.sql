-- 1. Location Table
CREATE TABLE Location (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    zip_code VARCHAR(10),
    country_code CHAR(2) NOT NULL,
    phone_number VARCHAR(20)
);

-- 2. Tag Table
CREATE TABLE Tag (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
);

-- 3. Facility Table
CREATE TABLE Facility (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    creation_date DATE NOT NULL,
    location_id INT NOT NULL,
    FOREIGN KEY (location_id) REFERENCES Location(id) ON DELETE CASCADE,
);

-- 4. Facility_Tag (Association Table)
CREATE TABLE facility_tag (
    facility_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (facility_id, tag_id),
    FOREIGN KEY (facility_id) REFERENCES Facility(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES Tag(id) ON DELETE CASCADE
);
