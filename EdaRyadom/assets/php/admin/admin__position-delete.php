<?php
    require_once('../../db/pdo_config.php');
    
    // Парсим параметры из URL
    $position_id = $_GET['position_id'];
    $photo_path = $_GET['photo_path'];

    // Определяем директорию файла, в которой находится фото удаляемой позиции
    $upload_dir = '../../img/products/';
    $old_photo_path = $upload_dir . $photo_path;
    
    // Удаляем старое фото
    unlink($old_photo_path);

    // Запрос на удаление
    $sql = "DELETE FROM `food` WHERE food_id = $position_id";
    $stmt = $pdo -> prepare($sql);

    $stmt -> execute();

    // Перенаправляем
    Header('Location: ../../../checkout/staff-pages/admin/admin_panel');
?>