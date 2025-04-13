<?php
    require_once('../../assets/db/pdo_config.php');
    session_start();
    
    // Параметры с сессии
    $user_photo = $_SESSION['user_photo'];
    $staff_id = $_SESSION['user_id'];

    date_default_timezone_set('Asia/Barnaul');
    $date = date("Y-m-d");

    // Проверка действий и запросов, исходя из роли пользователя
    if ($_SESSION['role_id'] == 4 or empty($_SESSION['auth'])) {
        Header('Location: ../../assets/errors/4XX/403');
    } elseif ($_SESSION['role_id'] == 3){
        if (isset($_GET['shift_id'])) {
            $shift_id = $_GET['shift_id'];
            $sql = "SELECT orders.order_id, order_status.status_id, order_status.status_name, orders.date, orders.adress_delivery FROM `orders`, `order_status`, shifts WHERE orders.status_id = order_status.status_id AND orders.status_id > 3 and orders.status_id < 6 and shifts.shift_id = orders.shift_id and shifts.shift_id = $shift_id";   
        } else {
            $sql = "SELECT orders.order_id, order_status.status_id, order_status.status_name, orders.date, orders.adress_delivery FROM `orders`, `order_status`, shifts WHERE orders.status_id = order_status.status_id AND orders.status_id > 3 and orders.status_id < 6 and shifts.shift_id = orders.shift_id and shifts.date = '$date'";
        }
    } elseif ($_SESSION['role_id'] == 2){
        $sql = "SELECT orders.order_id, order_status.status_id, order_status.status_name, orders.date, orders.adress_delivery FROM `orders`, `order_status`, shifts WHERE shifts.shift_id = orders.shift_id and shifts.date = '$date' and orders.status_id = order_status.status_id AND orders.status_id > 3 and orders.status_id < 6 AND orders.cook_id = $staff_id";
    } else {
        $sql = "SELECT orders.order_id, order_status.status_id, order_status.status_name, orders.date, orders.adress_delivery FROM `orders`, `order_status`, shifts WHERE shifts.shift_id = orders.shift_id and shifts.date = '$date' and orders.status_id = order_status.status_id AND orders.status_id > 3 and orders.status_id < 6 AND orders.waiter_id = $staff_id";
    }

    $stmt = $pdo -> prepare($sql);

    $stmt -> execute();
    $orders = $stmt -> fetchAll(PDO::FETCH_ASSOC);
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

        <link rel="stylesheet" href="../../plugins/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="../../assets/css/main.css">
        <link rel="icon" href="../../assets/img/logo.png">

        <title>Еда Рядом -> История заказов</title>
    </head>

    <body>

        <!-- Header -->
        <header class="header">
            <div class="header-panel container d-flex justify-content-between align-items-center">
                <div class="logo d-flex align-items-center">
                    <img src="../../assets/img/logo.png" alt="logo image" class="logo__img" draggable="false">
                    <div class="logo__text d-flex flex-column">
                        <span class="logo__text_green">Еда</span> Рядом
                    </div>
                </div>

                <nav class="header-panel-nav">

                    <?php if ($_SESSION['role_id'] == 1): ?>
                        <a href="./waiter/waiter_panel" class="title-medium lime-color-text">Назад</a>
                    <?php elseif ($_SESSION['role_id'] == 2): ?>
                        <a href="./cook/cook_panel" class="title-medium lime-color-text">Назад</a>
                    <?php elseif ($_SESSION['role_id'] == 3): ?>
                        <a href="./admin/admin_panel" class="title-medium lime-color-text">Назад</a>
                    <?php endif; ?>
                    
                </nav>
                
                <div class="header-panel-profile-stats">
                    <div class="header-panel-profile-stats">
                        <span class="title-medium darken d-none d-md-flex"><?= htmlspecialchars($_SESSION['fname']); ?></span>
                        <a href="../profile">
                            <?php if (isset($user_photo)): ?>
                                <img src="../../assets/img/users/<?= $user_photo; ?>" alt="profile image" class="header-profile-image panel-image" loading="lazy">
                            <?php else: ?>
                                <img src="../../assets/img/thumbnail.jpg" alt="profile image" class="header-profile-image panel-image" loading="lazy">
                            <?php endif; ?>   
                        </a> 
                    </div>
                    <div class="header-panel-logout d-none d-lg-flex">
                        <form method="POST" action="../../assets/php/exit">
                            <button type="submit" class="title-medium darken">Выйти</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main class="content panel-content">

            <h1 class="visually-hidden">Еда Рядом: История заказов</h1>

            <section class="active-orders">

                <h2 class="visually-hidden">Еда Рядом: История заказов</h2>

                <div class="active-orders-body container">
                    <h3 class="panel-heading title-huge darken">История заказов</h3>

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
                                    
                                    <div class="panel-card active-order-card <?php if($order_info['status_id'] == 4){echo 'payed';} else{echo 'canceled';} ?>" id="OrdersHistoryCard">

                                        <div class="active-orders-card-head d-flex justify-content-between">
                                            <span class="panel-card-parametr attribute lime-color-text">№ <?php echo htmlspecialchars($order_info['order_id']); ?></span>
                                            <div class="order-date d-flex column-gap-2">
                                                <span class="panel-card-parametr attribute"><?php echo date("d.m.y", strtotime($order_info['date'])); ?></span>
                                                <span class="panel-card-parametr attribute"><?php echo  date("H:i", strtotime($order_info['date'])); ?></span>
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
                                                <span class="panel-card-parametr"><?php echo $total_count; ?></span>
                                            </div>
                                            <?php if($order_info['adress_delivery'] != NULL): ?>
                                                <div class="active-order-element">
                                                    <span class="panel-card-parametr attribute">Доставка: </span>
                                                    <p class="panel-card-parametr"><?= $order_info['adress_delivery']; ?></p>
                                                </div>
                                            <?php endif; ?>
                                            <div class="active-order-element">
                                                <span class="panel-card-parametr attribute">Статус: </span>
                                                <span class="panel-card-parametr"><?php echo $order_info['status_name']; ?></span>
                                            </div>
                                            <div class="active-order-element">
                                                <span class="panel-card-parametr attribute">Итого: </span>
                                                <span class="panel-card-parametr"><?php echo $total_price; ?> ₽</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                
                            <?php endforeach; ?>
                            
                        </ul>
                    
                    <?php else: ?>
                        <div class="d-flex justify-content-center mb-5">
                            <p class="title-medium darken">Нет закрытых или отмененных заказов...</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <?php require_once('../../assets/view/checkout/footer.inc'); ?>
        
    </body>
</html>