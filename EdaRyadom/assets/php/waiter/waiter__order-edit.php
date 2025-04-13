<?php
    require_once('../../db/pdo_config.php');
    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $order_id = $_POST['order_id'];

        // Удаление старого заказа для обновления данных по нему (делается так, потому что UPDATE не может обновить данные по несуществующим строчкам, если количество позиций станет больше)
        $sql = "DELETE FROM `food_order` WHERE order_id = '$order_id'";
        $stmt = $pdo -> prepare($sql);
        $stmt -> execute();
        
        // Получаем массивы с данными по позициям
        $position = $_POST['position'];
        $amount = $_POST['amount'];

        // Подсчитываем количество позиций в массиве
        $j = count($_POST['position']);
        
        for ($i = 0; $i < $j; $i++) { 
            $product_id = $position[$i];
            $product_amount = $amount[$i];

            $sql = "INSERT INTO `food_order`(`order_id`, `food_id`, `count`) VALUES ($order_id, $product_id, $product_amount)";
            $stmt = $pdo -> prepare($sql);
            
            $stmt -> execute();
        }

    }
    else {
        $order_id = $_GET['order_id'];
        $status_id = $_GET['status_id'];
        $waiter_id = $_SESSION['user_id'];
        
        $sql = "UPDATE `orders` SET `status_id`= $status_id, `waiter_id`= $waiter_id WHERE order_id = $order_id";
        $stmt = $pdo -> prepare($sql);
        $stmt -> execute();
    }

    Header('Location: ../../../checkout/staff-pages/waiter/waiter_panel');
?>