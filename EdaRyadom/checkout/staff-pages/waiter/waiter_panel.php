<?php
    require_once('../../../assets/db/pdo_config.php');
    session_start();

    // Параметры с сессии
    if (isset($_SESSION['user_photo'])){
        $user_photo = $_SESSION['user_photo'];
    }
    $waiter_id = $_SESSION['user_id'];

    // Проверка на авторизацию
    if ($_SESSION['role_id'] != 1 or empty($_SESSION['auth'])) {
        Header('Location: ../../../assets/errors/4XX/403');
    }

    // Получение даты и времени
    date_default_timezone_set('Asia/Barnaul');
    $date = date("Y-m-d");

    // Узнаем id смены, который исходит из сегодняшнего дня
    $sql = "SELECT shifts.shift_id FROM `shifts`, shift_user WHERE date = '$date' AND user_id = $waiter_id AND shifts.shift_id = shift_user.shift_id AND shifts.status_id = 2";
    $stmt = $pdo -> prepare($sql);

    $stmt -> execute();
    $shift_info = $stmt -> fetch(PDO::FETCH_ASSOC);
    
    if ($shift_info === false){
        
        // Вывод ошибки
        $warning['Error'] = 'На сегодня нет работы или администратор еще не открыл смену... Добби свободен, ступай';
    } else {

        // Id смены
        $shift_id = $shift_info['shift_id'];

        // Запрос на вывод всех позиций, исходя из смены
        $sql = "SELECT * FROM `orders`, `order_status` WHERE orders.status_id = order_status.status_id AND orders.status_id < 4 AND (waiter_id = $waiter_id OR waiter_id is NULL) AND orders.shift_id = $shift_id";
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

        <title>Еда Рядом -> Официант: Панель управления</title>
    </head>

    <body>
        
        <!-- Header -->
        <?php require('../../../assets/view/checkout/staff_header.inc'); ?>

        <!-- Main content -->
        <main class="content panel-content">

            <h1 class="visually-hidden">Еда Рядом: Панель управления для Официаната</h1>

            <section class="create-order">

                <h2 class="visually-hidden">Еда Рядом: Создание заказа Официантом</h2>

                <div class="create-order-body container">
                    <div class="title-medium darken text-center greetengs">
                        Добро пожаловать,&nbsp; 
                        <span class="lime-color-text"><?= htmlspecialchars($_SESSION['fname']); ?>!</span>
                    </div>
    
                    <?php if (isset($warning['Error'])): ?>
                        <div class="title-medium darken text-center greetengs">
                            <p class="lime-color-text"><?= $warning['Error']; ?></p>
                        </div>
                    <?php else: ?>
                        <div class="create-order-content d-flex justify-content-center">
                            <div class="panel-card create-order-card">
                                <div class="panel-card-heading">
                                    <h3 class="title-medium darken">Создание заказа</h3>
                                </div>
                                <div class="panel-card-content">
                                    <form action="../../../assets/php/waiter/waiter__order-create" method="POST" class="create-order-form">
                                        <div class="create-order-heading d-flex justify-content-between">
                                            <span class="panel-card-parametr">Название позиции</span>
                                            <span class="panel-card-parametr">Кол-во</span>
                                        </div>
        
                                        <ul class="create-order-list" id="editOrderList"></ul>
        
                                        <div class="d-flex justify-content-center">
                                            <button type="submit" class="add-product" id="addProduct">
                                                <svg width="12" height="12" viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1.5 4.5H7.5" stroke="white" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M4.5 7.5V1.5" stroke="white" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                            </button>
                                        </div>
        
                                        <button type="submit" class="panel-button create-order-button title-medium">Завершить</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="active-orders">

                <h2 class="visually-hidden">Еда Рядом: Активные заказы</h2>

                <div class="active-orders-body container">
                    <h3 class="panel-heading title-huge darken">Активные заказы</h3>

                    <?php if (!empty($orders)): ?>

                        <ul class="active-orders-list d-flex justify-content-center">

                            <?php foreach ($orders as $order_info): ?>
                                <?php
                                    $total_count = 0;
                                    $total_price = 0;
                                    $order_id = $order_info['order_id'];

                                    $sql = "SELECT `count`, `name`, price from food, food_order WHERE food_order.order_id = $order_id AND food.food_id = food_order.food_id";
                                    $stmt = $pdo -> prepare($sql);

                                    $stmt -> execute();
                                    $food_list = $stmt -> fetchAll(PDO::FETCH_ASSOC);
                                ?>

                                <li class="active-order-item">
                                    <div class="panel-card active-order-card" id="OrdersHistoryCard">

                                        <div class="active-orders-card-head d-flex justify-content-between">
                                            <span class="panel-card-parametr attribute lime-color-text">№ <?= $order_info['order_id'] ?></span>
                                            
                                            <a href="waiter_order-edit?order_id=<?= $order_id ?>&status_id=<?= $order_info['status_id'] ?>">
                                                <svg class="svg-lime" fill="none" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8.661,19.113,3,21l1.887-5.661ZM20.386,7.388a2.1,2.1,0,0,0,0-2.965l-.809-.809a2.1,2.1,0,0,0-2.965,0L6.571,13.655l3.774,3.774Z"/>
                                                </svg>
                                            </a>

                                            <div class="order-date d-flex column-gap-2">
                                                <span class="panel-card-parametr attribute"><?= date("d.m.y", strtotime($order_info['date'])) ?></span>
                                                <span class="panel-card-parametr attribute"><?= date("H:i", strtotime($order_info['date'])) ?></span>
                                            </div>
                                        </div>

                                        <div class="active-orders-card-content d-flex flex-column row-gap-2">
                                            <div class="active-order-element">
                                                <span class="panel-card-parametr attribute">Состав: </span>

                                                <ul class="active-order-products-list">
                                                    <?php foreach ($food_list as $position): ?>
                                                        <?php
                                                            $total_count = $total_count + $position['count'];
                                                            $total_price = $total_price + $position['count'] * $position['price'];
                                                        ?>
                                                        
                                                        <li class="active-order-product-item">
                                                            <span class="panel-card-parametr"><?= $position['name']?></span>
                                                            <span class="panel-card-parametr"><?= $position['count']?></span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                            <div class="active-order-element">
                                                <span class="panel-card-parametr attribute">Общее кол-во: </span>
                                                <span class="panel-card-parametr"><?= $total_count?></span>
                                            </div>
                                            <?php if($order_info['adress_delivery'] != NULL): ?>
                                                <div class="active-order-element">
                                                    <span class="panel-card-parametr attribute">Доставка: </span>
                                                    <p class="panel-card-parametr"><?= $order_info['adress_delivery']?></p>
                                                </div>
                                            <?php endif; ?>
                                            <div class="active-order-element">
                                                <span class="panel-card-parametr attribute">Статус: </span>
                                                <span class="panel-card-parametr"><?= $order_info['status_name']?></span>
                                            </div>
                                            <div class="active-order-element">
                                                <span class="panel-card-parametr attribute">Итого: </span>
                                                <span class="panel-card-parametr"><?= $total_price?> ₽</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                            <?php endforeach;?>

                        </ul>

                    <?php else: ?>
                        <div class="d-flex justify-content-center mb-5">
                            <p class="title-medium darken">Нет активных заказов...</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <?php require_once('../../../assets/view/checkout/footer.inc'); ?>
        
    </body>
    <script src="../../../assets/js/food_list.js"></script>
</html>