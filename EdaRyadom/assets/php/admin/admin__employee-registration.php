<?php
    require_once('../../db/pdo_config.php');

    // Получение параметров из формы
    $role = htmlspecialchars($_POST['role-name']);
    $login = htmlspecialchars($_POST['login']);
    $fname = htmlspecialchars($_POST['fname']);
    $lname = htmlspecialchars($_POST['lname']);
    $password = htmlspecialchars($_POST['password']);

    // Проверка на существованеи пользователя с таким же логином
    $sql = "SELECT `login` FROM `users` WHERE login = :login";
    $stmt = $pdo -> prepare($sql);
    $stmt -> bindParam(':login', $login, PDO::PARAM_STR);
    
    $stmt -> execute();
    $existed_login = $stmt -> fetch(PDO::FETCH_ASSOC);

    if (!empty($existed_login)){

        // Передаем сообщение об ошибке
        $message = 'Пользователь с таким логином уже есть!';
        $url_message = http_build_query(['message-error-employee' => $message]);

        Header('Location: ../../../checkout/staff-pages/admin/admin_panel?' . $url_message);
    } else {
        
        // Хэшируем пароль
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        // Запрос в базу данных
        $sql = "INSERT INTO `users`(`role_id`, `login`, `password`, `fname`, `lname`) VALUES ($role, :login, :password, :fname, :lname)";
        $stmt = $pdo -> prepare($sql);
    
        // Присваиваем значения к экранируемым параметрам
        $stmt -> bindParam(':login', $login, PDO::PARAM_STR);
        $stmt -> bindParam(':password', $password_hash, PDO::PARAM_STR);
        $stmt -> bindParam(':fname', $fname, PDO::PARAM_STR);
        $stmt -> bindParam(':lname', $lname, PDO::PARAM_STR);
    
        // Выполняем запрос
        $stmt -> execute();
    
        // Перенаправляем
        Header('Location: ../../../checkout/staff-pages/admin/admin_employees-page');
    }
?>