# Los Pollos Hermanos - Local Setup for Teammates

This guide explains how to get a working copy of the Los Pollos Hermanos project with full database access on your local machine.

---

## 1. Clone the GitHub Repo

bash
git clone https://github.com/<your-username>/los_pollos_hermanos.git
cd los_pollos_hermanos

2. Install Requirements

PHP 8+

MySQL 8+

3. Set Up the MySQL User (Shared Credentials)

Use the credentials provided by your teammate:

Username: humza_user

Password: Humza123!

Database: los_pollos_db
If this MySQL user does not exist on your machine yet, run the following as a MySQL root user:

CREATE USER 'humza_user'@'localhost' IDENTIFIED BY 'Humza123!';
GRANT ALL PRIVILEGES ON los_pollos_db.* TO 'humza_user'@'localhost';
FLUSH PRIVILEGES;

4. Import the Database Dump

The .sql file is included in the repo (los_pollos_db.sql). Import it using:

mysql -u humza_user -p los_pollos_db < los_pollos_db.sql


Enter the password: Humza123!

This will recreate all tables and populate the database with the existing data.

5. Configure the Project to Use the Shared Credentials

Open db_connect.php in the project root and make sure it matches these credentials:

(you're using my credentials so dw about this part)


6. Start the PHP Development Server
php -S localhost:8000

