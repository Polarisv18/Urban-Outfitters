# Final Project Group 6

This is a PHP-based web application aimed at streamlining the purchasing process and providing a seamless and enjoyable journey for shoppers. Video demonstration is here: https://www.youtube.com/watch?v=miP9k0Mk0gQ 

## Features
- **Browse the Catalog**: Customers can browse items without logging in.
- **Account Management**: Customers can log in or sign up to manage their accounts and orders.
- **Order Management**: Place, view, and cancel orders.
- **Admin Features**: Manage the catalog, orders, and user accounts.

## Setup Instructions

### Prerequisites
1. Install [MAMP](https://www.mamp.info/) or similar (e.g., XAMPP, WAMP).
2. Ensure Apache and MySQL are running.

### Steps
1. Open phpMyAdmin by visiting:
http://localhost:8888/phpMyAdmin

2. Create a new database named `Final-Project`.

3. Import the SQL file:
- Click the **Import** tab.
- Choose the file `Code_and_SQL/Final-Project.sql` from this project.
- Click **Go** to complete the import.

4. Update the `config.php` file in the project with your database credentials:
```php
$host = 'localhost';
$user = 'root';
$password = ''; // Default password for MAMP
$dbname = 'Final-Project';
5. Start your local server and access the project: http://localhost/Final_Project_Group6/Code_and_SQL/homepage.php

