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

    // Поиск по сменам
    if (isset($_GET['search'])){
        
        // Получаем поисквой текст
        $search_text = htmlspecialchars($_GET['search']);

        // Если не выбрали дату
        if (empty($search_text)){

            // Сообщение об ошибке
            $message['Error'] = "Выберите дату для поиска!"; 

            // Запрос на выгрузку всех отчетов по сменам
            $sql = "SELECT `shift_id`, `date`, shifts.status_id, `status_name` FROM `shifts`, `shift_status` WHERE shifts.status_id = shift_status.status_id AND shifts.status_id > 1";
        } else {

            // Форматируем дату в вид, который принимает БД
            $search_date = date("Y-m-d", strtotime($search_text));
            
            // Запрос на выгрузку только нужного отчета по смене
            $sql = "SELECT `shift_id`, `date`, shifts.status_id, `status_name` FROM `shifts`, `shift_status` WHERE shifts.status_id = shift_status.status_id AND shifts.status_id > 1 AND date LIKE '%$search_date%'";
        }

    } else{
        // Запрос на выгрузку всех отчетов по сменам
        $sql = "SELECT `shift_id`, `date`, shifts.status_id, `status_name` FROM `shifts`, `shift_status` WHERE shifts.status_id = shift_status.status_id AND shifts.status_id > 1";
    }
    
    $stmt = $pdo -> prepare($sql);
    
    $stmt -> execute();
    $shifts = $stmt -> fetchAll(PDO::FETCH_ASSOC);
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

        <title>Еда Рядом -> Администратор: Отчет по сменам</title>
    </head>

    <body>
        
        <!-- Header -->
        <?php require('../../../assets/view/checkout/staff_header.inc'); ?>

        <!-- Main content -->
        <main class="content panel-content">

            <h1 class="visually-hidden">Еда Рядом: Отчет по сменам</h1>

            <section class="active-orders">

                <h2 class="visually-hidden">Еда Рядом: Список отчетов по сменам</h2>

                <div class="active-orders-body container">
                    
                    <h3 class="panel-heading title-huge darken">Итоги смен</h3>

                    <div class="back-button">
                        <svg width="26" height="16" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 6.75C21.4142 6.75 21.75 6.41421 21.75 6C21.75 5.58579 21.4142 5.25 21 5.25V6.75ZM0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM21 5.25L1 5.25V6.75L21 6.75V5.25Z" fill="#4D4D4D"/>
                        </svg>
                        <a href="./admin_panel">Назад</a>
                    </div>

                    <form action="" method="GET" class="search-field-wrapp">
                        <div class="search-field">
                            <input class="search-field-input" type="date" name="search">
                        </div>
    
                        <button class=" d-none d-sm-flex panel-button search-field-button title-small bg-lime-color" type="submit">Найти</button>
                        <button class=" d-flex d-sm-none search-field-button panel-button" type="submit">
                            <svg width="25" height="26" viewBox="0 0 25 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="10" cy="10" r="9" stroke="#96DD1E" stroke-width="2"/>
                                <path d="M22.7929 25.7071C23.1834 26.0976 23.8166 26.0976 24.2071 25.7071C24.5976 25.3166 24.5976 24.6834 24.2071 24.2929L22.7929 25.7071ZM14.7929 17.7071L22.7929 25.7071L24.2071 24.2929L16.2071 16.2929L14.7929 17.7071Z" fill="#96DD1E"/>
                            </svg>
                        </button>
                    </form>

                    <?php if (isset($message['Error'])): ?>
                        <span class="message error"><?= $message['Error']; ?></span>
                    <?php endif; ?>

                    <?php if (!empty($shifts)): ?>

                        <ul class="active-orders-list d-flex flex-column justify-content-center">

                            <?php foreach ($shifts as $shift_info): ?>
                                <?php

                                    $shift_id = $shift_info['shift_id'];

                                    // Запрос на выдачу стафа, исходя из id смены
                                    $sql = "SELECT `role_name`, `lname` from users, shift_user, role WHERE shift_user.shift_id = $shift_id and shift_user.user_id = users.user_id AND role.role_id = users.role_id; ";
                                    $stmt = $pdo -> prepare($sql);

                                    $stmt -> execute();
                                    $staff_list = $stmt -> fetchAll(PDO::FETCH_ASSOC);

                                    // Запрос на выдачу принятых заказов, привязанных к конкретной смене (для обновления в реальном времени для не закрытых смен)
                                    $sql = "SELECT status_id FROM `orders` WHERE shift_id = $shift_id";
                                    $stmt = $pdo -> prepare($sql);

                                    $stmt -> execute();
                                    $accept_orders = $stmt -> fetchAll(PDO::FETCH_ASSOC);

                                    // Объявляем переменные для подсчета количества на отчет
                                    $total_orders = 0;
                                    $payed_orders = 0;
                                    $cancelled_orders = 0;

                                    // Считаем по статусам заказов
                                    foreach ($accept_orders as $order){
                                        if ($order['status_id'] <= 5){
                                            $total_orders ++;
                                        } 
                                        
                                        if ($order['status_id'] == 4) {
                                            $payed_orders ++;
                                        } elseif ($order['status_id'] == 5) {
                                            $cancelled_orders ++;
                                        }
                                    }

                                    // Запрос на выдачу общей выручки смены (в расчет идут только те, что были оплачены)
                                    $sql = "SELECT orders.status_id, SUM(food.price * food_order.count) AS total_sum FROM orders JOIN food_order ON orders.order_id = food_order.order_id JOIN food ON food_order.food_id = food.food_id WHERE orders.status_id = 4 AND orders.shift_id = $shift_id GROUP BY orders.status_id; ";
                                    $stmt = $pdo -> prepare($sql);

                                    $stmt -> execute();
                                    $total_money = $stmt -> fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <li class="active-order-item">
                                    <div class="panel-card active-order-card w-100 <?php if($shift_info['status_id'] == 3){echo 'payed';} ?>">

                                        <div class="active-orders-card-head d-flex justify-content-between">
                                            <span class="panel-card-parametr attribute lime-color-text">№ <?=$shift_id?></span>

                                            <div class="order-date d-flex column-gap-2">
                                                <span class="panel-card-parametr attribute"><?= date('d.m.y', strtotime($shift_info['date'])); ?></span>
                                            </div>
                                        </div>

                                        <div class="active-orders-card-content d-flex flex-column row-gap-2">
                                            <div class="active-order-element">
                                                <span class="panel-card-parametr attribute">Сотрудники: </span>

                                                <ul class="active-order-products-list">
                                                    <?php foreach ($staff_list as $staff_info):?>
                                                        
                                                        <li class="active-order-product-item">
                                                            <span class="panel-card-parametr"><?= $staff_info['lname']; ?></span>
                                                            <span class="panel-card-parametr"><?= $staff_info['role_name']; ?></span>
                                                        </li>
                                                        
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                            <div class="active-order-element">
                                                <span class="panel-card-parametr attribute">Принято заказов: </span>
                                                <span class="panel-card-parametr"><?= $total_orders; ?> шт.</span>
                                            </div>
                                            <div class="active-order-element">
                                                <span class="panel-card-parametr attribute">Отменено заказов: </span>
                                                <span class="panel-card-parametr red-color-text"><?= $cancelled_orders; ?> шт.</span>
                                            </div>
                                            <div class="active-order-element">
                                                <span class="panel-card-parametr attribute">Оплачено заказов: </span>
                                                <span class="panel-card-parametr lime-color-text"><?= $payed_orders; ?> шт.</span>
                                            </div>
                                            <div class="active-order-element">
                                                <span class="panel-card-parametr attribute">Статус: </span>
                                                <span class="panel-card-parametr"><?= $shift_info['status_name']; ?></span>
                                            </div>

                                            <?php foreach ($total_money as $total_sum):?>
                                                        
                                                <div class="active-order-element">
                                                    <span class="panel-card-parametr attribute">Общая выручка: </span>
                                                    <span class="panel-card-parametr"><?= $total_sum['total_sum']; ?> ₽</span>
                                                </div>
                                                        
                                            <?php endforeach; ?>
                                            
                                            <div class="d-flex justify-content-center">
                                                <a href="../orders_history?shift_id=<?= $shift_id; ?>" class="sign-form-link">Посмотреть заказы в смене</a>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach;?>

                        </ul>

                    <?php else: ?>
                        <div class="d-flex justify-content-center mb-5">
                            <p class="title-medium darken">По вашему запросу ничего не найдено...</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <?php require_once('../../../assets/view/checkout/footer.inc'); ?>

    </body>
</html>