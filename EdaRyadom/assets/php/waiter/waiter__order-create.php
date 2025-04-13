<?php
    require_once('../../db/pdo_config.php');
    session_start();

    $waiter_id = $_SESSION['user_id'];

    // Получение даты и времени
    date_default_timezone_set('Asia/Barnaul');
    $date = date("Y-m-d");

    // Получаем id смены, которая исходит из сегодняшнего дня
    $sql = "SELECT `shift_id` FROM `shifts` WHERE date = '$date'";
    $stmt = $pdo -> prepare($sql);

    $stmt -> execute();
    $shift_id = $stmt -> fetch(PDO::FETCH_ASSOC);
    $shift_id = $shift_id['shift_id'];

    // Создаем "каркас" заказа, содержащий в себе статус "Принят", id заказа и id смены
    $date = date("Y-m-d H:i");
    $sql = "INSERT INTO `orders`(`date`, `status_id`, `waiter_id`, `shift_id`) VALUES ('$date', '1', $waiter_id, $shift_id)";
    
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute();
    
    // Находим только что созданный заказ, чтобы потом наполнить его содержимым позиций и их колчества
    $sql = "SELECT `order_id` FROM `orders` ORDER BY order_id DESC LIMIT 1";
    $stmt = $pdo -> prepare($sql);
    
    $stmt -> execute();
    $created_order_id = $stmt -> fetch(PDO::FETCH_ASSOC);
    $created_order_id = $created_order_id['order_id'];

    // Получение двух массивов с позициями и их количествами
    $position = $_POST['position'];
    $amount = $_POST['amount'];

    // Считаем кол-во позиций
    $j = count($_POST['position']);
    
    // Цикл для перебора массивов и заполнение заказа этитми позициями
    for ($i = 0; $i < $j; $i++) { 
        $product_id = $position[$i];
        $product_amount = $amount[$i];

        $sql = "INSERT INTO `food_order`(`order_id`, `food_id`, `count`) VALUES ($created_order_id, $product_id, $product_amount)";
        $stmt = $pdo -> prepare($sql);
        
        $stmt -> execute();
    }

    Header('Location: ../../../checkout/staff-pages/waiter/waiter_panel');
?>