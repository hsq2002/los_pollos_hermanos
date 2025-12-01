-- create.sql

-- Drop tables to allow re-running the script
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS Contains;
DROP TABLE IF EXISTS Restaurant_Order;
DROP TABLE IF EXISTS Reservation;
DROP TABLE IF EXISTS Restaurant_Table;
DROP TABLE IF EXISTS Menu_Item;
DROP TABLE IF EXISTS User;
SET FOREIGN_KEY_CHECKS = 1;

-- Table 1: User
CREATE TABLE User (
    User_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    phone VARCHAR(15),
    role ENUM('Customer', 'Server', 'Host', 'Manager') NOT NULL DEFAULT 'Customer'
);

-- Table 2: Menu_Item
CREATE TABLE Menu_Item (
    item_ID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(8, 2) NOT NULL,
    category ENUM('Appetizer', 'Entree', 'Dessert', 'Drink') NOT NULL
);

-- Table 3: Restaurant_Table
CREATE TABLE Restaurant_Table (
    Table_ID INT PRIMARY KEY, -- Assuming Table_ID is manually assigned (e.g., 1, 2, 3...)
    capacity INT NOT NULL, -- Added for practical use
    Server_ID INT,
    FOREIGN KEY (Server_ID) REFERENCES User(User_id) ON DELETE SET NULL
);

-- Table 4: Reservation
CREATE TABLE Reservation (
    Res_ID INT AUTO_INCREMENT PRIMARY KEY,
    party_size INT NOT NULL,
    start_time DATETIME NOT NULL,
    status ENUM('SCHEDULED', 'CONFIRMED', 'COMPLETED', 'CANCELLED') DEFAULT 'SCHEDULED',
    User_ID INT,
    Table_ID INT,
    FOREIGN KEY (User_ID) REFERENCES User(User_id) ON DELETE SET NULL,
    FOREIGN KEY (Table_ID) REFERENCES Restaurant_Table(Table_ID) ON DELETE SET NULL
);

-- Table 5: Restaurant_Order
CREATE TABLE Restaurant_Order (
    order_ID INT AUTO_INCREMENT PRIMARY KEY,
    order_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('OPEN', 'PREPARING', 'READY', 'COMPLETED', 'CANCELLED') DEFAULT 'OPEN',
    User_ID INT,
    Server_ID INT,
    Table_ID INT,
    FOREIGN KEY (User_ID) REFERENCES User(User_id) ON DELETE SET NULL,
    FOREIGN KEY (Server_ID) REFERENCES User(User_id) ON DELETE SET NULL,
    FOREIGN KEY (Table_ID) REFERENCES Restaurant_Table(Table_ID) ON DELETE SET NULL
);

-- Table 6: Contains (Order Items)
CREATE TABLE Contains (
    order_ID INT,
    item_ID INT,
    qty INT NOT NULL DEFAULT 1,
    price DECIMAL(8, 2) NOT NULL,
    notes VARCHAR(200),
    PRIMARY KEY (order_ID, item_ID),
    FOREIGN KEY (order_ID) REFERENCES Restaurant_Order(order_ID) ON DELETE CASCADE,
    FOREIGN KEY (item_ID) REFERENCES Menu_Item(item_ID) ON DELETE RESTRICT
);