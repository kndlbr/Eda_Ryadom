<?php 
    require_once('../../db/pdo_config.php');
    session_start();

    // Данная страница устанавливает сессионные данные для администратора, поскольку для входа в админку требуется ввести пароль от нее (не от логина авторизации)
    // Делается это для того, чтобы при попытке отменить аутентификацию на админа человека не перекидывало в панель администратора, поскольку сессионные данные устанавливались там
    // Следовательно мы устанавливаем сессионные данные на другой странице (в защищенной папке) во избежания несанционированного доступа
    
    // Делаем запрос в базу для вытягивания данных об администраторе
    $sql = "SELECT * FROM `users` WHERE role_id = 3";
    $stmt = $pdo -> prepare($sql);

    $stmt -> execute();

    $admin_info = $stmt -> fetch(PDO::FETCH_ASSOC);
    
    // Устанавливаем сессионные параметры
    $_SESSION['auth'] = TRUE;
    $_SESSION['user_id'] = $admin_info['user_id'];
    $_SESSION['role_id'] = $admin_info['role_id'];
    $_SESSION['fname'] = $admin_info['fname'];
    $_SESSION['user_photo'] = $admin_info['photo'];

    // Перенаправляем в админку
    Header('Location: ../../../checkout/staff-pages/admin/admin_panel');
?>