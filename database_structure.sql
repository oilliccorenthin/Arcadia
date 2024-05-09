CREATE TABLE animal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    state VARCHAR(50),
    views INT DEFAULT 0,
    lastFedAt DATETIME,
    veterinaryReport_id INT,
    habitat_id INT,
    FOREIGN KEY (veterinaryReport_id) REFERENCES rapport_veterinaire(id),
    FOREIGN KEY (habitat_id) REFERENCES habitat(id)
);
CREATE TABLE habitat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    description VARCHAR(50),
    commentaire_habitat VARCHAR(50)
);
CREATE TABLE image (
    id INT AUTO_INCREMENT PRIMARY KEY,
    imageName VARCHAR(255),
    habitat_id INT,
    updatedAt DATETIME,
    FOREIGN KEY (habitat_id) REFERENCES habitat(id)
);
CREATE TABLE opinion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50),
    commentaire VARCHAR(50),
    isVisible BOOLEAN
);
CREATE TABLE race (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50)
);
CREATE TABLE rapport_veterinaire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATETIME,
    detail VARCHAR(50),
    typeFood VARCHAR(50),
    gramFood INT,
    user_id INT,
    animal_id INT,
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (animal_id) REFERENCES animal(id)
);
CREATE TABLE role (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50)
);
CREATE TABLE service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    description VARCHAR(50)
);
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(180),
    password VARCHAR(255)
);

CREATE TABLE user_role (
    user_id INT,
    role_id INT,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (role_id) REFERENCES role(id)
);
