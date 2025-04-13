<?php
    require_once('../assets/db/pdo_config.php');
    session_start();

    // Проверка пользователя на авторизацию
    if (empty($_SESSION['auth'])) {
        header('Location: ../home-page');
    }

    // Путь до фото пользователя
    $user_photo = $_SESSION['user_photo'];
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

        <link rel="stylesheet" href="../plugins/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/main.css">
        <link rel="icon" href="../assets/img/logo.png">
        <title>Еда Рядом -> Личный кабинет: Редактирование профиля</title>
    </head>
    <body>
        
        <!-- Header -->
        <header class="header">
            <div class="header-panel container d-flex justify-content-between align-items-center">
                <div class="logo d-flex align-items-center">
                    <img src="../assets/img/logo.png" alt="logo image" class="logo__img" draggable="false">
                    <div class="logo__text d-flex flex-column">
                        <span class="logo__text_green">Еда</span> Рядом
                    </div>
                </div>

                <?php if ($_SESSION['role_id'] == 4): ?>
                    <nav class="header-panel-nav visually-hidden">
                        <a href="#" class="title-medium lime-color-text">История</a>
                    </nav>
                <?php else: ?>
                    <nav class="header-panel-nav">
                        <a href="./staff-pages/orders_history" class="title-medium lime-color-text">История</a>
                    </nav>
                <?php endif; ?>
                
                <div class="header-panel-profile-stats">
                    <div class="header-panel-profile-stats">
                        <span class="title-medium darken d-none d-md-flex"><?php echo htmlspecialchars($_SESSION['fname']); ?></span>
                        <a href="./profile">
                            <?php if (isset($user_photo)): ?>
                                <img src="../assets/img/users/<?= $user_photo; ?>" alt="profile image" class="header-profile-image panel-image" loading="lazy">
                            <?php else: ?>
                                <img src="../assets/img/thumbnail.jpg" alt="profile image" class="header-profile-image panel-image" loading="lazy">
                            <?php endif; ?>    
                        </a>
                    </div>
                    <div class="header-panel-logout d-none d-lg-flex">
                        <form method="POST" action="../assets/php/exit">
                            <button type="submit" class="title-medium darken">Выйти</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main class="content panel-content">

            <h1 class="visually-hidden">Еда Рядом: Личный кабинет</h1>
            
            <section class="profile">
                <h2 class="visually-hidden">Информация о пользователе</h2>

                <div class="profile-body container">

                    <div class="back-button">
                        <svg width="26" height="16" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 6.75C21.4142 6.75 21.75 6.41421 21.75 6C21.75 5.58579 21.4142 5.25 21 5.25V6.75ZM0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM21 5.25L1 5.25V6.75L21 6.75V5.25Z" fill="#4D4D4D"/>
                        </svg>

                        <a href="./profile">Назад</a>
                    </div>

                    <div class="profile-body-wrap">
                        <div class="profile-body-section">
                            <h3 class="panel-heading title-huge darken">Редактирование профиля</h3>

                            <div class="profile-data d-flex">
                                <div class="profile-image d-flex align-items-center flex-column">
                                    
                                    <?php if (isset($user_photo)): ?>
                                        <img src="../assets/img/users/<?= $user_photo; ?>" alt="profile image" class="profile-data-image panel-image" loading="lazy">
                                    <?php else: ?>
                                        <img src="../assets/img/thumbnail.jpg" alt="profile image" class="profile-data-image panel-image" loading="lazy">
                                    <?php endif; ?>   

                                    <span class="title-medium darken profile-name"><?php echo htmlspecialchars($_SESSION['fname']); ?></span>
                                </div>
                               
                                <div class="panel-card profile-info" id="uploadFileDiv">
                                    <div class="panel-card-heading">
                                        <h4 class="title-medium darken">Смена данных</h4>
                                    </div>
                                    <div class="panel-card-content">
                                        <form action="../assets/php/user/user__data-edit" method="POST" enctype="multipart/form-data" class="panel-form">
                                            <ul class="panel-form-content">
                                                <li class="panel-form-input-element">
                                                    <input type="text" class="panel-form-input" name="new_data[]" placeholder="Введите новое имя:">
                                                </li>
                                                <?php if ($_SESSION['role_id'] == 4): ?>
                                                    <li class="panel-form-input-element">
                                                        <input type="tel" class="panel-form-input" maxlength="12" name="new_data[]" placeholder="Ваш телефон">
                                                    </li>
                                                <?php else: ?>
                                                    <li class="panel-form-input-element">
                                                        <input type="text" class="panel-form-input" name="new_data[]" placeholder="Введите новую фамилию:">
                                                    </li>
                                                <?php endif; ?>

                                                <li class="panel-form-input-element">
                                                    <div class="upload-file-wrapper">
                                                        <input type="file" accept="image/jpeg, image/jpg, image/png"  class="panel-form-input upload-file-input" id="uploadFileInput" name="new_img">
        
                                                        <label class="upload-file-label" for="uploadFileInput">
                                                            <span class="upload-file-label-text">Смена фото:</span>
                                                            <span class="upload-file-label-button">Выбрать</span>
                                                        </label>
                                                    </div>
                                                </li>
                                            </ul>

                                            <?php if(isset($_GET['message-error'])): ?>
                                                <span class="message error"><?= $_GET['message-error']?></span>
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
        <?php require_once('../assets/view/checkout/footer.inc'); ?>

    </body>
    <script src="../assets/js/main.js"></script>
</html>