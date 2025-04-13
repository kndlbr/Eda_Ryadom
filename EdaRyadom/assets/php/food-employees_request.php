<?php
    require_once('../db/pdo_config.php');

    // Запрос на получение данных о еде
    $sql = "SELECT `food_id`, `name` FROM `food`";
    $stmt = $pdo -> prepare($sql);

    $stmt -> execute();
    $food_positions = $stmt -> fetchAll(PDO::FETCH_ASSOC);

    // Запрос на получение данных о сотрудниках
    $sql = "SELECT user_id, lname, role_name FROM `users`, `role` WHERE users.role_id = role.role_id and role.role_id < 3";
    $stmt = $pdo -> prepare($sql);

    $stmt -> execute();
    $employees = $stmt -> fetchAll(PDO::FETCH_ASSOC);

    $data = ['food_list' => $food_positions, 'employees_list' => $employees];

    header('Content-Type: application/json');
    echo json_encode($data);
?>