<?php

    // Params
    $host = 'localhost';
    $db = 'eda_ryadom';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $username, $password);
        $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Ошибка подключения: " . $e->getMessage());
    }
?>