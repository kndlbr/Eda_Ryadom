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

    // Парсим параметры из URL
    $status_id = $_GET['status_id'];
    $shift_id = $_GET['shift_id'];

    $sql = "SELECT shift_id FROM `shifts` WHERE shift_id = $shift_id and status_id = $status_id";
    $stmt = $pdo -> prepare($sql);
    
    $stmt -> execute();
    $check = $stmt -> fetchAll(PDO::FETCH_ASSOC);

    if (empty($check)) {
        Header('Location: admin_shifts-page');
    }

    if ($status_id == 2) {
        $sql = "SELECT `lname`, `role_name` from users, role, shift_user WHERE shift_user.shift_id = $shift_id AND users.user_id = shift_user.user_id and role.role_id = users.role_id";
        $stmt = $pdo -> prepare($sql);
        
        $stmt -> execute();
        $shift_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

        <title>Еда Рядом -> Администратор: Редактирование смены</title>
    </head>

    <body>
        
        <!-- Header -->
        <?php require('../../../assets/view/checkout/staff_header.inc'); ?>

        <!-- Main content -->
        <main class="content panel-content">

            <h1 class="visually-hidden">Еда Рядом: Редактирование смены администратором</h1>

            <section class="create-order">

                <h2 class="visually-hidden">Еда Рядом: Форма редактирования смены</h2>

                <div class="create-order-body container">

                    <h3 class="panel-heading title-huge darken">Редактирование смены</h3>

                    <div class="back-button">
                        <svg width="26" height="16" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 6.75C21.4142 6.75 21.75 6.41421 21.75 6C21.75 5.58579 21.4142 5.25 21 5.25V6.75ZM0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM21 5.25L1 5.25V6.75L21 6.75V5.25Z" fill="#4D4D4D"/>
                        </svg>
                        <a href="./admin_shifts-page">Назад</a>
                    </div>
    
                    <div class="create-order-content d-flex justify-content-center">
                        <div class="panel-card create-order-card">
                            <div class="panel-card-content">
                                <form action="../../../assets/php/admin/admin__shift-edit" method="POST" class="create-order-form">
                                    <div class="create-order-heading d-flex justify-content-between <?php if ($status_id == 2){echo 'ps-0';} ?>">
                                        <span class="panel-card-parametr">Выберите сотрудника</span>
                                    </div>

                                    <?php if($status_id == 1): ?>
                                        <ul class="create-order-list" id="editShiftList"></ul>
                                    <?php else: ?>
                                        <ul class="create-order-list">
                                            <?php foreach($shift_info as $employee): ?>
                                                <li class="create-order-item">
                                                    <select class="create-order-position" disabled>
                                                        <option><?= $employee['lname'] ?>&nbsp;<?= $employee['role_name']?></option>
                                                    </select>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif;?>

                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="add-product<?php if ($status_id == 2){ echo 'visually-hidden';}?>" id="addEmployee">
                                            <svg width="12" height="12" viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.5 4.5H7.5" stroke="white" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M4.5 7.5V1.5" stroke="white" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <input class="visually-hidden" name="shift_id" value="<?= $shift_id?>">

                                    <div class="create-order-buttons d-flex <?php if ($status_id == 2){ echo 'mb-0';} ?>">
                                        <a href="../../../assets/php/admin/admin__shift-edit?shift_id=<?= $shift_id?>&action=deleteShift" class="panel-button delete-order-button title-medium white-color-text <?php if($status_id == 2){echo 'visually-hidden';} ?>">Удалить</a>
                                        <a href="../../../assets/php/admin/admin__shift-edit?shift_id=<?= $shift_id?>&action=closeShift" class="panel-button delete-order-button title-medium white-color-text <?php if($status_id == 1){echo 'visually-hidden';} ?>">Закрыть</a>
                                        <button name="openShift" type="submit" class="panel-button pay-order-button title-medium <?php if($status_id == 2){echo 'visually-hidden';}?>">Открыть</button>
                                    </div>

                                    <button type="submit" class="panel-button create-order-button title-medium <?php if ($status_id == 2){echo 'visually-hidden';} ?>">Завершить</button>
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
    <script src="../../../assets/js/employees_list.js"></script>
</html>