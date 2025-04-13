<?php
    require_once('../../db/pdo_config.php');
    session_start();

    // Проверка, авторизован ли пользователь
    if (!isset($_SESSION['auth'])) {
        Header('Location: ../../../home-page.php');
        exit;
    }

    // Получаем данные из формы
    $position_name = htmlspecialchars($_POST['position_name']);
    $position_price = htmlspecialchars($_POST['position_price']);
    $position_image = $_FILES['position_img'];
    $position_description = htmlspecialchars($_POST['position_description']);
    
    // Получение данных о фото (имя и расширение)
    $file_name = pathinfo($position_image['name']);
    $extension = strtolower($file_name['extension']);

    // Проверяем типы допустимых файлов. Если неверный формат - отправляем ошибку
    $file_extentions = ['jpg', 'jpeg', 'png'];
    
    if (!in_array($extension, $file_extentions)) {

        // Передаем сообщение об ошибке
        $message = 'Неверный формат файла!';
        $url_message = http_build_query(['message-error' => $message]);

        Header('Location: ../../../checkout/staff-pages/admin/admin_panel?' . $url_message);
    } else {
        
        // Определяем дату
        date_default_timezone_set('Asia/Barnaul');
        $date = date("d-m-Y_H-i-s");

        // Строим имя файла
        $position_file_name = "position_{$date}";

        // Определяем директорию файла и прописываем исходный путь сохраннения файла
        $upload_dir = '../../img/products/';
        $image_file_path = $upload_dir . $position_file_name . '.' . $extension;
        
        // Перемещаем загруженный файл
        if (move_uploaded_file($position_image['tmp_name'], $image_file_path)) {
            
            // Функции изменения изображения 
            $position_photo = resizeImage($image_file_path, 300, 300);

            // Делаем запрос в БД на изменение пути до фото
            $sql = "INSERT INTO `food`(`name`, `price`, `description`, `photo`) VALUES (:position_name, :position_price, :position_description, :position_photo)";
            $stmt = $pdo -> prepare($sql);
            
            $stmt -> bindParam(':position_name', $position_name, PDO::PARAM_STR);
            $stmt -> bindParam(':position_price', $position_price, PDO::PARAM_STR);
            $stmt -> bindParam(':position_description', $position_description, PDO::PARAM_STR);
            $stmt -> bindParam(':position_photo', $position_photo, PDO::PARAM_STR);

            $stmt -> execute();
            
            // Передаем сообщение об успещной загрузке
            $message = 'Успешное создание позиции!';
            $url_message = http_build_query(['message-success' => $message]);

            Header('Location: ../../../checkout/staff-pages/admin/admin_panel?' . $url_message);
        } else {

            // Передаем сообщение об ошибке
            $message = 'Ошибка при загрузке позиции!';
            $url_message = http_build_query(['message-error' => $message]);
    
            Header('Location: ../../../checkout/staff-pages/admin/admin_panel?' . $url_message);
        }
    }

    // Функция изменения размера изображения
    function resizeImage($file_path, $width, $height) {

        // Длинна и ширина необработанного фото в виде списка
        list($image_input_width, $image_input_height) = getimagesize($file_path);
        
        // Отрисовываем изображение в цвет, исходя из переданных параметров
        $resized_image = imagecreatetruecolor($width, $height);

        // В зависимоти от формата изображения выбираем функцию, которая нам даст информацию о изображении
        if (pathinfo($file_path, PATHINFO_EXTENSION) == 'jpg' or pathinfo($file_path, PATHINFO_EXTENSION) == 'jpeg') {
            $image = imagecreatefromjpeg($file_path);
        } elseif (pathinfo($file_path, PATHINFO_EXTENSION) == 'png'){
            $image = imagecreatefrompng($file_path);

            // Устанавливаем прозрачность для PNG
            imagealphablending($resized_image, false);
            imagesavealpha($resized_image, true);
            $transparent = imagecolorallocatealpha($resized_image, 255, 255, 255, 127);
            imagefilledrectangle($resized_image, 0, 0, $width, $height, $transparent);
        }

        // Функция сохранения пропорций изображения (на деле работает странно)
        imagecopyresampled($resized_image, $image, 0, 0, 0, 0, $width, $height, $image_input_width, $image_input_height);
        
        // Перезаписываем полученное изображение
        $new_position_file_path = pathinfo($file_path, PATHINFO_DIRNAME) . '/' . pathinfo($file_path, PATHINFO_FILENAME) . "." . pathinfo($file_path, PATHINFO_EXTENSION);

        // В зависимоти от формата изображения выбираем функцию
        if (pathinfo($file_path, PATHINFO_EXTENSION) == 'jpg' or pathinfo($file_path, PATHINFO_EXTENSION) == 'jpeg') {
            imagejpeg($resized_image, $new_position_file_path);
        } elseif (pathinfo($file_path, PATHINFO_EXTENSION) == 'png'){
            imagepng($resized_image, $new_position_file_path);
        }
        
        // Очистка памяти
        imagedestroy($image);
        imagedestroy($resized_image);

        // Получаем название изображения 
        $new_position_photo_name = pathinfo($file_path, PATHINFO_FILENAME) . "." . pathinfo($file_path, PATHINFO_EXTENSION);

        // Возвращаем название, чтобы вставить его в БД
        return $new_position_photo_name;
    }
?>