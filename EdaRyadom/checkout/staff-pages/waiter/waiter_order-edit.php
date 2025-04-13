<?php
    require_once("../../../assets/db/pdo_config.php");
    session_start();

    // Проверка на авторизацию
    if ($_SESSION['role_id'] != 1 or empty($_SESSION['auth'])) {
        Header('Location: ../../../assets/errors/4XX/403');
    }

    // Параметры с сессии
    if (isset($_SESSION['user_photo'])){
        $user_photo = $_SESSION['user_photo'];
    }

    // Парсим параметры из URL
    $status_id = $_GET['status_id'];
    $order_id = $_GET['order_id'];
    
    if (empty($status_id) and empty($order_id)) {

        // Если в ссылку нет GET параметров, то выбрасываем офика в панельку
        Header('Location: ./waiter_panel');
    } else {

        // Проверка на существующий редактируемый заказ
        $sql = "SELECT order_id FROM `orders` WHERE order_id = $order_id and status_id = $status_id";
        $stmt = $pdo -> prepare($sql);
        
        $stmt -> execute();
        $check = $stmt -> fetchall(PDO::FETCH_ASSOC);
    
        // Если не прошла проверка
        if (empty($check)) {
            Header('Location: ./waiter_panel');
        }
    
        // Если заказ имеет любой статус, кроме "принят", то 
        if ($status_id != 1) {
            $sql = "SELECT `count`, `name` from food, food_order WHERE food_order.order_id = $order_id AND food.food_id = food_order.food_id";
            $stmt = $pdo -> prepare($sql);
            
            $stmt -> execute();
            $food_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }


?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="../../../plugins/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="../../../assets/css/main.css">
        <link rel="icon" href="../../../assets/img/logo.png">

        <title>Еда Рядом -> Официант: Редактирование заказа</title>
    </head>

    <body>
        
        <!-- Header -->
        <?php require('../../../assets/view/checkout/staff_header.inc'); ?>

        <!-- Main content -->
        <main class="content panel-content">

            <h1 class="visually-hidden">Еда Рядом: Редактирование заказа официантом</h1>

            <section class="create-order">

                <h2 class="visually-hidden">Еда Рядом: Форма редактирование заказа</h2>

                <div class="create-order-body container">

                    <h3 class="panel-heading title-huge darken">Редактирование заказа</h3>

                    <div class="back-button">
                        <svg width="26" height="16" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 6.75C21.4142 6.75 21.75 6.41421 21.75 6C21.75 5.58579 21.4142 5.25 21 5.25V6.75ZM0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM21 5.25L1 5.25V6.75L21 6.75V5.25Z" fill="#4D4D4D"/>
                        </svg>
                        <a href="./waiter_panel">Назад</a>
                    </div>

                    <div class="create-order-content d-flex justify-content-center">
                        <div class="panel-card create-order-card">
                            <div class="panel-card-content">
                                <form action="../../../assets/php/waiter/waiter__order-edit.php" method="POST" class="create-order-form">
                                    <div class="create-order-heading d-flex justify-content-between <?php if ($status_id != 1){ echo 'ps-0';}?>">
                                        <span class="panel-card-parametr">Название позиции</span>
                                        <span class="panel-card-parametr">Кол-во</span>
                                    </div>

                                    <?php if($status_id == 1): ?>
                                        <ul class="create-order-list" id="editOrderList"></ul>
                                    <?php else: ?>
                                        <ul class="create-order-list">
                                            <?php foreach ($food_list as $position): ?>
                                                <li class="create-order-item">
                                                    <button class="delete-prodcut visually-hidden" type="button" data-action="deleteProduct">
                                                        <svg width="10" height="4" viewBox="0 0 10 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M1.5 1.5H7.5" stroke="white" stroke-width="3" stroke-linecap="round"/>
                                                        </svg>
                                                    </button>
            
                                                    <select disabled class="create-order-position" name="position[]">
                                                        <option value="1"><?= $position['name'] ?></option>
                                                    </select>
            
                                                    <input class="create-order-position amount" disabled name="amount[]" type="number" min="1" max="20" value="<?= $position['count'] ?>">
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-center">
                                            <button type="button" class="add-product <?php if($status_id != 1){echo 'visually-hidden';} ?>" id="addProduct">
                                            <svg width="12" height="12" viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.5 4.5H7.5" stroke="white" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M4.5 7.5V1.5" stroke="white" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <div class="create-order-buttons d-flex <?php if($status_id != 1){echo 'mb-0';} ?>">
                                        <a href="../../../assets/php/waiter/waiter__order-edit.php?order_id=<?= $order_id?>&status_id=5" class="panel-button white-color-text delete-order-button title-medium">Отменить</a>
                                        <a href="../../../assets/php/waiter/waiter__order-edit.php?order_id=<?= $order_id?>&status_id=4" class="panel-button white-color-text create-order-button title-medium <?php if($status_id != 3){echo 'visually-hidden';} ?>">Оплата</a>
                                    </div>

                                    <button name="order_id" value="<?= $order_id?>" type="submit" class="panel-button d-flex justify-content-center create-order-button white-color-text title-medium <?php if($status_id != 1){echo 'visually-hidden';} ?>">Завершить</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <?php require_once('../../../assets/view/checkout/footer.inc'); ?>
        
    </body>
    <script src="../../../assets/js/food_list.js"></script>
</html>