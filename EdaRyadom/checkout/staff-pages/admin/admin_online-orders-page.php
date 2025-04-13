<?php
    require_once('../../../assets/db/pdo_config.php');
    session_start();

    // Проверка на авторизицию
    if ($_SESSION['role_id'] != 3 or empty($_SESSION['auth'])) {
        Header('Location: ../../../assets/errors/4XX/403');
    }

    // Параметры с сессии
    if (isset($_SESSION['user_photo'])){
        $user_photo = $_SESSION['user_photo'];
    }

    // Принятие заказа администратором, либо его отмена
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (!empty($_POST['cancelled_order'])) {
            $order_id = $_POST['cancelled_order'];
            $sql = "UPDATE `orders` SET `status_id`='5' WHERE order_id = $order_id";
        }
        elseif (!empty($_POST['accepted_order'])) {
            $order_id = $_POST['accepted_order'];
            $sql = "UPDATE `orders` SET `status_id`='1' WHERE order_id = $order_id";
        }

        $stmt = $pdo -> prepare($sql);
        $stmt -> execute();
    }

    // Получение даты и времени
    date_default_timezone_set('Asia/Barnaul');
    $date = date("Y-m-d");

    // Узнаем id смены, который исходит из сегодняшнего дня
    $sql = "SELECT shift_id FROM `shifts` WHERE date = '$date' AND status_id <= 2";
    $stmt = $pdo -> prepare($sql);

    $stmt -> execute();
    $shift_info = $stmt -> fetch(PDO::FETCH_ASSOC);
    
    if ($shift_info === false){
        
        // Вывод ошибки
        $warning['Error'] = 'На сегодня нету онлайн заказов из-за отсутствия смены (У вас выходной!)...';
    } else {

        // Id смены
        $shift_id = $shift_info['shift_id'];

        // Запрос на вывод всех позиций, исходя из смены
        $sql = "SELECT orders.order_id, orders.date, orders.adress_delivery, order_status.status_name, users.fname, users.phone FROM `orders`, `order_status`, `users` WHERE orders.status_id = order_status.status_id AND orders.status_id = 6 AND orders.shift_id = $shift_id AND orders.client_id = users.user_id; ";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute();
        $orders = $stmt-> fetchAll(PDO::FETCH_ASSOC);
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

        <title>Еда Рядом -> Администратор: Список онлайн заказов</title>
    </head>

    <body>
        
        <!-- Header -->
        <?php require('../../../assets/view/checkout/staff_header.inc'); ?>

        <!-- Main content -->
        <main class="content panel-content">

            <h1 class="visually-hidden">Еда Рядом: Список онлайн заказов</h1>

            <section class="active-orders">

                <h2 class="visually-hidden">Еда Рядом: Онлайн заказы</h2>

                <div class="active-orders-body container">
                    
                    <h3 class="panel-heading title-huge darken">Онлайн заказы</h3>

                    <div class="back-button">
                        <svg width="26" height="16" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 6.75C21.4142 6.75 21.75 6.41421 21.75 6C21.75 5.58579 21.4142 5.25 21 5.25V6.75ZM0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM21 5.25L1 5.25V6.75L21 6.75V5.25Z" fill="#4D4D4D"/>
                        </svg>
                        <a href="./admin_panel">Назад</a>
                    </div>

                    <!-- Если у нас нашлась созданная смена -->
                    <?php if (empty($warning['Error'])): ?>

                        <!-- Если в смене присутствует хотя бы 1 заказ -->
                        <?php if (!empty($orders)): ?>

                            <ul class="active-orders-list d-flex justify-content-center flex-column">

                                <!-- Выводим кажный закзаз -->
                                <?php foreach ($orders as $order_info): ?>
                                    <?php
                                        $total_count = 0;
                                        $total_price = 0;
                                        $order_id = $order_info['order_id'];

                                        $sql = "SELECT `count`, `name`, price FROM food, food_order WHERE food_order.order_id = $order_id AND food.food_id = food_order.food_id";
                                        $stmt = $pdo -> prepare($sql);

                                        $stmt -> execute();
                                        $food_list = $stmt -> fetchAll(PDO::FETCH_ASSOC);
                                    ?>

                                    <li class="active-order-item">
                                        <div class="panel-card active-order-card w-100" id="OrdersHistoryCard">

                                            <div class="active-orders-card-head d-flex justify-content-between">
                                                <span class="panel-card-parametr attribute lime-color-text">№ <?= $order_info['order_id'] ?></span>

                                                <div class="order-date d-flex column-gap-2">
                                                    <span class="panel-card-parametr attribute"><?= date("d.m.y", strtotime($order_info['date'])) ?></span>
                                                    <span class="panel-card-parametr attribute"><?= date("H:i", strtotime($order_info['date'])) ?></span>
                                                </div>
                                            </div>

                                            <div class="active-orders-card-content d-flex flex-column row-gap-2">

                                                <div class="active-order-element">
                                                    <span class="panel-card-parametr attribute">Заказчик: </span>
                                                    <span class="panel-card-parametr"><?= $order_info['fname']; ?></span>
                                                </div>

                                                <div class="active-order-element">
                                                    <span class="panel-card-parametr attribute">Телефон: </span>
                                                    <span class="panel-card-parametr"><?= $order_info['phone']; ?></span>
                                                </div>

                                                <div class="active-order-element">
                                                    <span class="panel-card-parametr attribute">Состав: </span>

                                                    <ul class="active-order-products-list">

                                                        <!-- Выводим содержание каждой позиции -->
                                                        <?php foreach ($food_list as $position): ?>
                                                            <?php
                                                                $total_count = $total_count + $position['count'];
                                                                $total_price = $total_price + $position['count'] * $position['price'];
                                                            ?>
                                                            
                                                            <li class="active-order-product-item">
                                                                <span class="panel-card-parametr"><?= $position['name']; ?></span>
                                                                <span class="panel-card-parametr"><?= $position['count']; ?></span>
                                                            </li>
                                                        <?php endforeach; ?>

                                                    </ul>
                                                </div>

                                                <div class="active-order-element">
                                                    <span class="panel-card-parametr attribute">Общее кол-во: </span>
                                                    <span class="panel-card-parametr"><?= $total_count; ?></span>
                                                </div>

                                                <div class="active-order-element">
                                                    <span class="panel-card-parametr attribute">Итого: </span>
                                                    <span class="panel-card-parametr"><?= $total_price; ?> ₽</span>
                                                </div>

                                                <?php if($order_info['adress_delivery'] != NULL): ?>
                                                    <div class="active-order-element">
                                                        <span class="panel-card-parametr attribute">Доставка: </span>
                                                        <p class="panel-card-parametr"><?= $order_info['adress_delivery']; ?></p>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="active-order-element">
                                                    <span class="panel-card-parametr attribute">Статус: </span>
                                                    <span class="panel-card-parametr"><?= $order_info['status_name']; ?></span>
                                                </div>

                                                

                                                <form method="post" action="" class="create-order-buttons d-flex m-0">
                                                    <button name="cancelled_order" value="<?= $order_id; ?>" class="panel-button delete-order-button title-medium">Отменить</button>
                                                    <button name="accepted_order" value="<?= $order_id; ?>" class="panel-button create-order-button title-medium">Принять</button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>

                                <?php endforeach; ?>

                            </ul>
                        
                        <!-- Если в смене нет заказов -->
                        <?php else: ?>
                            <div class="d-flex justify-content-center mb-5">
                                <p class="title-medium darken">Пока нет онлайн заказов...</p>
                            </div>
                        <?php endif; ?>
                    
                    <!-- Если смены нету -->
                    <?php else: ?>
                        <div class="d-flex justify-content-center mb-5">
                            <p class="title-medium darken"><?= $warning['Error']; ?></p>
                        </div>
                    <?php endif; ?>
            </div>
            </section>
        </main>

        <!-- Footer -->
        <?php require_once('../../../assets/view/checkout/footer.inc'); ?>

    </body>
</html>