<?php
    require_once('../../../assets/db/pdo_config.php');
    session_start();

    // Проверка на авторизацию
    if ($_SESSION['role_id'] != 2 or empty($_SESSION['auth'])) {
        Header('Location: ../../../assets/errors/4XX/403');
    }
    
    // Параметры с сессии
    if (isset($_SESSION['user_photo'])){
        $user_photo = $_SESSION['user_photo'];
    }
    $cook_id = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (!empty($_POST['cancelled_order'])) {
            $order_id = $_POST['cancelled_order'];
            $sql = "UPDATE `orders` SET `status_id`='5', `cook_id` = $cook_id WHERE order_id = $order_id";
        }
        elseif (!empty($_POST['accepted_order'])) {
            $order_id = $_POST['accepted_order'];
            $sql = "UPDATE `orders` SET `status_id`='2', `cook_id` = $cook_id WHERE order_id = $order_id";
        }
        elseif (!empty($_POST['ready_order'])){
            $order_id = $_POST['ready_order'];
            $sql = "UPDATE `orders` SET `status_id`='3', `cook_id` = $cook_id WHERE order_id = $order_id";
        }

        $stmt = $pdo -> prepare($sql);
        $stmt -> execute();
    }

    // Получение даты и времени
    date_default_timezone_set('Asia/Barnaul');
    $date = date("Y-m-d");

    // Узнаем id смены, который исходит из сегодняшнего дня и повара, который входит в смену
    $sql = "SELECT shifts.shift_id FROM `shifts`, shift_user WHERE date = '$date' AND user_id = $cook_id AND shifts.shift_id = shift_user.shift_id AND shifts.status_id = 2";
    $stmt = $pdo -> prepare($sql);

    $stmt -> execute();
    $shift_info = $stmt -> fetch(PDO::FETCH_ASSOC);
    
    if ($shift_info === false){
        
        // Выводим ошибку
        $warning['Error'] = 'На сегодня нет работы, или администратор еще не открыл смену... Добби свободен, ступай';

    } else {
        
        // Id смены
        $shift_id = $shift_info['shift_id'];

        $sql = "SELECT * FROM `orders`, `order_status` WHERE orders.status_id < 3 AND orders.status_id = order_status.status_id AND (cook_id = $cook_id OR cook_id is NULL) AND orders.shift_id = $shift_id";
        $stmt = $pdo -> prepare($sql);
    
        $stmt -> execute();
        $orders = $stmt -> fetchAll(PDO::FETCH_ASSOC);
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

        <title>Еда Рядом -> Повар: Панель управления</title>
    </head>

    <body>
        
        <!-- Header -->
        <?php require('../../../assets/view/checkout/staff_header.inc'); ?>

        <!-- Main content -->
        <main class="content panel-content">

            <h1 class="visually-hidden">Еда Рядом: Панель управления для Повара</h1>

            <section>

                <h2 class="visually-hidden">Еда Рядом: Управление заказа Поваром</h2>

                <div class="create-order-body container">
                    <div class="title-medium darken text-center greetengs">
                        Добро пожаловать,&nbsp; 
                        <span class="lime-color-text"><?php echo htmlspecialchars($_SESSION['fname']); ?>!</span>
                    </div>

                    <?php if (isset($warning['Error'])): ?>
                        <div class="title-medium darken text-center greetengs">
                            <p class="lime-color-text"><?= $warning['Error']; ?></p>
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

                            <?php foreach ($orders as $order_info):?>
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
                                            <span class="panel-card-parametr attribute lime-color-text">№ <?= $order_info['order_id']?></span>
                                            
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
                                                            <span class="panel-card-parametr"><?php echo $position['name']; ?></span>
                                                            <span class="panel-card-parametr"><?php echo $position['count']; ?></span>
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
                                                    <p class="panel-card-parametr"><?= $order_info['adress_delivery']; ?></p>
                                                </div>
                                            <?php endif; ?>
                                            <div class="active-order-element">
                                                <span class="panel-card-parametr attribute">Статус: </span>
                                                <span class="panel-card-parametr"><?= $order_info['status_name'] ?></span>
                                            </div>
                                            <div class="active-order-element">
                                                <span class="panel-card-parametr attribute">Итого: </span>
                                                <span class="panel-card-parametr"><?= $total_price ?> ₽</span>
                                            </div>
                                            
                                            <form method="post" action="" class="create-order-buttons d-flex m-0">
                                                <?php if ($order_info['status_id'] == 1):?>
                                                    <button name="cancelled_order" value="<?= $order_id ?>" class="panel-button delete-order-button title-medium">Отменить</button>
                                                    <button name="accepted_order" value="<?= $order_id ?>" class="panel-button create-order-button title-medium">Принять</button>
                                                <?php else: ?>
                                                    <button name="ready_order" value="<?= $order_id ?>" class="panel-button create-order-button title-medium">Готов</button>
                                                <?php endif?>
                                            </form>

                                        </div>
                                    </div>
                                </li>
                            <?php endforeach?>

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
</html>