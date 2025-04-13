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
    $position_id = $_GET['position_id'];

    $sql = "SELECT * FROM `food` WHERE food_id = $position_id";
    $stmt = $pdo -> prepare($sql);

    $stmt -> execute();
    $position_info = $stmt -> fetch(PDO::FETCH_ASSOC);
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

        <title>Еда Рядом -> Администратор: Редактированеи позиции</title>
    </head>
    <body>
        <!-- Header -->
        <?php require('../../../assets/view/checkout/staff_header.inc'); ?>

        <!-- Main content -->
        <main class="content panel-content">

            <h1 class="visually-hidden">Еда Рядом: Редактирование позиции администратором</h1>
            
            <section class="profile">
                <h2 class="visually-hidden">Редактирование позиции администратором</h2>

                <div class="profile-body container">

                    <div class="back-button">
                        <svg width="26" height="16" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 6.75C21.4142 6.75 21.75 6.41421 21.75 6C21.75 5.58579 21.4142 5.25 21 5.25V6.75ZM0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM21 5.25L1 5.25V6.75L21 6.75V5.25Z" fill="#4D4D4D"/>
                        </svg>

                        <a href="./admin_panel">Назад</a>
                    </div>

                    <div class="profile-body-wrap">
                        <div class="profile-body-section">
                            <h3 class="panel-heading title-huge darken">Редактирование профиля</h3>

                            <div class="profile-data d-flex">
                                <div class="profile-image d-flex align-items-center flex-column">
                                    
                                    <img src="../../../assets/img/products/<?= $position_info['photo']; ?>" alt="position image" class="profile-data-image panel-image" loading="lazy">

                                    <span class="title-medium darken profile-name"><?= $position_info['name']; ?></span>
                                </div>
                               
                                <div class="panel-card profile-info" id="uploadFileDiv">
                                    <div class="panel-card-heading">
                                        <h4 class="title-medium darken">Редактирование позиции</h4>
                                    </div>
                                    <div class="panel-card-content">
                                        <form action="../../../assets/php/admin/admin__position-edit?position_id=<?= $position_id ?>" method="POST" enctype="multipart/form-data" class="panel-form">
                                            <ul class="panel-form-content">
                                                <li class="panel-form-input-element">
                                                    <input type="text" class="panel-form-input" name="new_position_data[]" placeholder="Введите новое название позиции (текущ. <?= $position_info['name']?>):">
                                                </li>
    
                                                <li class="panel-form-input-element">
                                                    <input type="number" class="panel-form-input" min="0" max="9999" placeholder="Введите новую цену (текущ. <?= $position_info['price']?> руб.):" name="new_position_data[]">
                                                </li>
                                                
                                                <li class="panel-form-input-element">
                                                    <div class="upload-file-wrapper">
                                                        <input type="file" accept="image/jpeg, image/jpg, image/png"  class="panel-form-input upload-file-input" id="uploadFileInput" name="new_position_img">
        
                                                        <label class="upload-file-label" for="uploadFileInput">
                                                            <span class="upload-file-label-text">Смена фото:</span>
                                                            <span class="upload-file-label-button">Выбрать</span>
                                                        </label>
                                                    </div>
                                                </li>

                                                <li class="panel-form-input-element">
                                                    <textarea type="text" class="panel-form-input" name="new_position_data[]" placeholder="Введите новое описание позиции:"></textarea>
                                                </li>
                                            </ul>

                                            <?php if(isset($_GET['message-error'])): ?>
                                                <span class="message error"><?= $_GET['message-error']?></span>
                                            <?php elseif (isset($_GET['message-success'])): ?>
                                                <span class="message success"><?= $_GET['message-success']?></span>
                                            <?php endif; ?>
                                            
                                            <button class="panel-button form-button title-medium" type="submit">Изменить</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </main>

        <!-- Footer -->
        <?php require_once('../../../assets/view/checkout/footer.inc'); ?>

    </body>
    <script src="../../../assets/js/main.js"></script>
</html>