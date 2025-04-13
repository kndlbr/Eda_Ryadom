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

    // Если мы отправляем что-то методом POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){

        // Увольнение сотрудника
        if (isset($_POST['dissmiss'])){

            // Получаем id пользователя
            $user_id = $_POST['dissmiss'];
    
            // Делаем запрос
            $sql = "DELETE FROM `users` WHERE user_id = $user_id";
            $stmt = $pdo -> prepare($sql);
    
            $stmt -> execute();
        }
    }

    // Поиск сотрудника
    if (isset($_GET['search'])){
        
        // Получаем поисквой текст
        $search_text = htmlspecialchars($_GET['search']);
        
        // Вывод только найденных сотруднкиов
        $sql = "SELECT `user_id`, `lname`, `role_name`, `photo` FROM `users`, `role` WHERE (lname LIKE '%$search_text%' OR role.role_name LIKE '%$search_text%') and role.role_id = users.role_id AND users.role_id < 3";
    } else {
        
        // Выводим всех сотрудников
        $sql = "SELECT `user_id`, `lname`, `role_name`, `photo` FROM `users`, `role` WHERE role.role_id = users.role_id and users.role_id < 3;";
    }
    
    $stmt = $pdo -> prepare($sql);
    
    $stmt -> execute();
    $staff_info = $stmt -> fetchAll(PDO::FETCH_ASSOC);

    
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

        <title>Еда Рядом -> Администратор: Список сотрудников</title>
    </head>

    <body>

        <!-- Header -->
        <?php require('../../../assets/view/checkout/staff_header.inc'); ?>

        <!-- Main content -->
        <main class="content panel-content">

            <h1 class="visually-hidden">Еда Рядом: Список сотрудников</h1>

            <section class="active-orders">

                <h2 class="visually-hidden">Еда Рядом: Полный список сотрудников</h2>

                <div class="active-orders-body container">
                    <h3 class="panel-heading title-huge darken">Список сотрудников</h3>
                    <div class="back-button">
                        <svg width="26" height="16" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 6.75C21.4142 6.75 21.75 6.41421 21.75 6C21.75 5.58579 21.4142 5.25 21 5.25V6.75ZM0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM21 5.25L1 5.25V6.75L21 6.75V5.25Z" fill="#4D4D4D"/>
                        </svg>
                        <a href="./admin_panel">Назад</a>
                    </div>

                    <form action="" method="GET" class="search-field-wrapp">
                        <div class="search-field">
                            <input class="search-field-input" type="text" placeholder="Найти сотрудника..." name="search">
                            <svg class="d-none d-sm-flex search-field-icon" width="25" height="26" viewBox="0 0 25 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="10" cy="10" r="9" stroke="#96DD1E" stroke-width="2"/>
                                <path d="M22.7929 25.7071C23.1834 26.0976 23.8166 26.0976 24.2071 25.7071C24.5976 25.3166 24.5976 24.6834 24.2071 24.2929L22.7929 25.7071ZM14.7929 17.7071L22.7929 25.7071L24.2071 24.2929L16.2071 16.2929L14.7929 17.7071Z" fill="#96DD1E"/>
                            </svg>
                        </div>
    
                        <button class=" d-none d-sm-flex panel-button search-field-button title-small bg-lime-color" type="submit">Найти</button>
                        <button class=" d-flex d-sm-none search-field-button panel-button" type="submit">
                            <svg width="25" height="26" viewBox="0 0 25 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="10" cy="10" r="9" stroke="#96DD1E" stroke-width="2"/>
                                <path d="M22.7929 25.7071C23.1834 26.0976 23.8166 26.0976 24.2071 25.7071C24.5976 25.3166 24.5976 24.6834 24.2071 24.2929L22.7929 25.7071ZM14.7929 17.7071L22.7929 25.7071L24.2071 24.2929L16.2071 16.2929L14.7929 17.7071Z" fill="#96DD1E"/>
                            </svg>
                        </button>
                    </form>

                    <?php if (isset($_GET['search'])): ?>
                        <a href="./admin_employees-page" type="button" class="panel-button cart-table-button button mb-4">Очистить поиск</a>
                    <?php endif; ?>

                    <?php if (!empty($staff_info)): ?>

                        <ul class="active-orders-list employees-list d-flex justify-content-center">

                            <?php foreach ($staff_info as $user_info):?>

                                <li class="active-order-item">
                                    <div class="panel-card employee-card">

                                        <?php if (isset($user_info['photo'])): ?>
                                            <img src="../../../assets/img/users/<?= $user_info['photo']; ?>" alt="employee-image" width="100" height="100" class="panel-image employee-card-image" loading="lazy">
                                        <?php else: ?>
                                            <img src="../../../assets/img/thumbnail.jpg" alt="employee-image" width="100" height="100" class="panel-image employee-card-image" loading="lazy">
                                        <?php endif; ?>

                                        <div class="employee-card-info">
                                            <h4 class="title-medium darken"><?= $user_info['lname'] ?></h4>
                                            <span class="panel-card-parametr"><?= $user_info['role_name'] ?></span>
                                            <form action="" method="POST">
                                                <button type="submit" name="dissmiss" value="<?= $user_info['user_id'] ?>" class="panel-button delete-order-button employee-fire title-medium">Уволить</button>
                                            </form>
                                        </div>
                                    </div>
                                </li>

                            <?endforeach?>

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