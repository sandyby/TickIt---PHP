<?php
define("DATABASE_NAME", "webpro_uts_lab");
define("USERS_TABLE_NAME", "users");
define("TODOLIST_TABLE_NAME", "lists");
define("ITEMS_TABLE_NAME", "items");

function createDatabase(): void
{
  $servername = "localhost";
  $username = "root";
  $password = "";
  try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE DATABASE IF NOT EXISTS " . DATABASE_NAME;
    $conn->query($sql);
  } catch (PDOException $e) {
    error_log($sql . "<br>" . $e->getMessage());
  }
  $conn = null;
}

function createTableUsers(): void
{
  $servername = "localhost";
  $username = "root";
  $password = "";
  try {
    $conn = new PDO("mysql:host=$servername;dbname=" . DATABASE_NAME, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS " . USERS_TABLE_NAME . "(
            user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(32) UNIQUE NOT NULL,
            email VARCHAR(128) UNIQUE NOT NULL,
            password VARCHAR(128),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            verification_code VARCHAR(64) UNIQUE NOT NULL,
            isVerified BIT DEFAULT 0,
            verified_at TIMESTAMP NULL,
            reset_password_token VARCHAR(64) UNIQUE DEFAULT NULL,
            reset_password_token_expiry_date TIMESTAMP NULL DEFAULT NULL
        )";

    $conn->exec($sql);
  } catch (PDOException $e) {
    error_log($sql . "<br>" . $e->getMessage());
  }
  $conn = null;
}

function createTableTDL(): void
{
  $servername = "localhost";
  $username = "root";
  $password = "";
  try {
    $conn = new PDO("mysql:host=$servername;dbname=" . DATABASE_NAME, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS " . TODOLIST_TABLE_NAME . "(
            list_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            title VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES " . USERS_TABLE_NAME . "(user_id) ON DELETE CASCADE
        )";

    $conn->exec($sql);
  } catch (PDOException $e) {
    error_log($sql . "<br>" . $e->getMessage());
  }
  $conn = null;
}

function createTableItems(): void
{
  $servername = "localhost";
  $username = "root";
  $password = "";
  try {
    $conn = new PDO("mysql:host=$servername;dbname=" . DATABASE_NAME, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS " . ITEMS_TABLE_NAME . "(
            item_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            list_id INT UNSIGNED NOT NULL,
            description VARCHAR(100) NOT NULL,
            due_date TIMESTAMP,
            status TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (list_id) REFERENCES " . TODOLIST_TABLE_NAME . "(list_id) ON DELETE CASCADE
        )";

    $conn->exec($sql);
  } catch (PDOException $e) {
    error_log($sql . "<br>" . $e->getMessage());
  }
  $conn = null;
}

createDatabase();
createTableUsers();
createTableTDL();
createTableItems();
