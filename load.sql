-- load.sql

-- 1. Insert Users (Managers, Servers, Customers)
INSERT INTO User (User_id, name, phone, role) VALUES 
(1, 'Alice Manager', '5551000', 'Manager'),
(2, 'Bob Server', '5552000', 'Server'),
(3, 'Charlie Host', '5553000', 'Host'),
(4, 'Dana Customer', '5554000', 'Customer'),
(5, 'Eve Customer', '5555000', 'Customer');

-- 2. Insert Menu Items
INSERT INTO Menu_Item (item_ID, name, price, category) VALUES
(101, 'Grilled Chicken', 15.99, 'Entree'),
(102, 'Fries', 4.50, 'Appetizer'),
(103, 'Cheesecake', 7.00, 'Dessert'),
(104, 'Soda', 2.50, 'Drink');

-- 3. Insert Tables
INSERT INTO Restaurant_Table (Table_ID, capacity, Server_ID) VALUES
(10, 4, 2),
(11, 6, 2),
(12, 2, NULL);

-- 4. Insert Reservations
INSERT INTO Reservation (Res_ID, party_size, start_time, status, User_ID, Table_ID) VALUES
(1001, 4, '2025-12-01 19:00:00', 'CONFIRMED', 4, 10),
(1002, 2, '2025-12-01 20:30:00', 'SCHEDULED', 5, 12);

-- 5. Insert Orders
INSERT INTO Restaurant_Order (order_ID, User_ID, Server_ID, Table_ID) VALUES
(5001, 4, 2, 10),
(5002, NULL, 2, 11); -- Dine-in order without a logged-in customer

-- 6. Insert Order Items (Contains)
INSERT INTO Contains (order_ID, item_ID, qty, price, notes) VALUES
(5001, 101, 1, 15.99, NULL),
(5001, 104, 2, 2.50, 'Refill when empty'),
(5002, 102, 1, 4.50, 'Extra ketchup'),
(5002, 103, 1, 7.00, NULL);