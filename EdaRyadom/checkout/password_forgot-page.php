<?php
    require_once('../assets/db/pdo_config.php');
    session_start();

    // Проверка на авторизацию
    if (!empty($_SESSION['auth'])) {
        Header('Location: profile');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        // Парсим логин с формы
        $login = $_POST['login'];

        // Делаем запрос на поиск пользователя по логину в БД
        $sql = "SELECT user_id from `users` where login = :login";
        $stmt = $pdo -> prepare($sql);
        
        $stmt -> bindParam(':login', $login, PDO::PARAM_STR);
        $stmt -> execute();

        $user_id = $stmt -> fetch(PDO::FETCH_ASSOC);
        
        // Если нашли, то перенаправляем
        if (isset($user_id['user_id'])) {
            
            $_SESSION['login'] = $login;

            Header('Location: ./password_change');
        } else {

            // Если не нашли, то выводим ошибку
            $answer['Error'] = "Неверный логин";
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

        <link rel="stylesheet" href="../plugins/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/main.css">
        <link rel="icon" href="../assets/img/logo.png">
        <title>Еда Рядом -> Забыли пароль</title>
    </head>
    
    <body>
        
        <!-- Header -->
        <?php require_once('../assets/view/checkout/header.inc');?>

        <!-- Main content -->
        <main class="content panel-content">

            <h1 class="visually-hidden">Еда Рядом: Забыли пароль</h1>

            <section class="sign">
                <div class="sign-body container d-flex flex-column justify-content-center">

                    <h2 class="visually-hidden">Восстановление пароля через логин</h2>

                    <div class="back-button">
                        <svg width="26" height="16" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 6.75C21.4142 6.75 21.75 6.41421 21.75 6C21.75 5.58579 21.4142 5.25 21 5.25V6.75ZM0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM21 5.25L1 5.25V6.75L21 6.75V5.25Z" fill="#4D4D4D"/>
                        </svg>
                        <a href="../home-page" class="">На главную</a>
                    </div>

                    <div class="sign-form-wrap d-flex justify-content-center">
                        <div class="panel-card sign-form">
                            <h3 class="title-medium darken sign-form-heading">Восстановление пароля</h3>
    
                            <form action="" method="POST">
                                <ul class="sign-form-list">
                                    <li class="sign-form-item">
                                        <input type="text" pattern="[A-Za-z0-9_]+" title="Допускается только латиница, цифры и нижнее подчеркивание" class="sign-form-input" maxlength="20" name="login" placeholder="Логин" required>
                                    </li>
                                </ul>
    
                                <button class="sign-form-button title-medium" type="submit">Далее</button>
                            </form>
                            
                            <?php if (isset($answer['Error'])): ?>
                                <span class="message error"><?= $answer['Error']?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <?php require_once('../assets/view/checkout/footer.inc'); ?>

    </body>
</html>