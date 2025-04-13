<?php
    require_once('../../db/pdo_config.php');

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

    // Ловим карпа
    $position_id = $_GET['position_id'];

    // Парсим данные из формы
    $new_position_data = $_POST['new_position_data'];
    $new_position_img = $_FILES['new_position_img'];

    if (empty($new_position_data[0]) and empty($new_position_data[1]) and empty($new_position_data[2]) and $new_position_img['error'] >= 1){

        // Передаем сообщение об ошибке
        $message = 'Вы ничего не ввели!';
        $url_message = http_build_query(['message-error' => $message]);

        Header('Location: ../../../checkout/staff-pages/admin/admin_position-edit?position_id='. $position_id .'&' . $url_message);
    } else {
        
        // Если ввели новое название
        if (!empty($new_position_data[0])) {
            $sql = "UPDATE `food` SET `name`= :position_name WHERE food_id = $position_id";
            $stmt = $pdo -> prepare($sql);
    
            $new_position_name = htmlspecialchars($new_position_data[0]);
            $stmt -> bindParam(':position_name', $new_position_name, PDO::PARAM_STR);
            
            $stmt -> execute();
        }
        
        // Если ввели новую цену
        if (!empty($new_position_data[1])) {

            $sql = "UPDATE `food` SET `price`= :position_price WHERE food_id = $position_id";
            $stmt = $pdo -> prepare($sql);

            $new_position_price = htmlspecialchars($new_position_data[1]);
            $stmt -> bindParam(':position_price', $new_position_price, PDO::PARAM_STR);
            
            $stmt -> execute();
        }

        // Если ввели новое описание 
        if (!empty($new_position_data[2])) {

            echo $new_position_data[2];
            
            $sql = "UPDATE `food` SET `description`= :position_description WHERE food_id = $position_id";
            $stmt = $pdo -> prepare($sql);

            $new_position_description = htmlspecialchars($new_position_data[2]);
            $stmt -> bindParam(':position_description', $new_position_description, PDO::PARAM_STR);
            
            $stmt -> execute();
        }
    
        // Если фото загрузилось нормально
        if ($new_position_img['error'] == 0){
            
            // Получение данных о фото (имя и расширение)
            $file_name = pathinfo($new_position_img['name']);
            $extension = strtolower($file_name['extension']);
    
            $file_extentions = ['jpg', 'jpeg', 'png'];
            
            // Если фото не прошло проверку на расширение
            if (!in_array($extension, $file_extentions)) {
    
                // Передаем сообщение об ошибке
                $message = 'Неверный формат файла!';
                $url_message = http_build_query(['message-error' => $message]);
    
                Header('Location: ../../../checkout/staff-pages/admin/admin_position-edit?position_id='. $position_id .'&' . $url_message);
            } else {
                
                // Определяем дату
                date_default_timezone_set('Asia/Barnaul');
                $date = date("d-m-Y_H-i-s");

                // Строим имя файла
                $position_file_name = "position_{$date}";

                // Определяем директорию файла и прописываем исходный путь сохраннения файла
                $upload_dir = '../../img/products/';
                $image_file_path = $upload_dir . $position_file_name . '.' . $extension;

                
                $sql = "SELECT photo FROM food WHERE food_id = $position_id";
                $stmt = $pdo -> prepare($sql);
                
                $stmt -> execute();

                $old_photo_path = $stmt -> fetch(PDO::FETCH_ASSOC);
                $old_photo_path = $upload_dir . $old_photo_path['photo'];

                // Проверка на существование старого фото в директории с продуктами
                if (file_exists($old_photo_path)){
                    
                    // Если есть - удаляем старое фото
                    unlink($old_photo_path);
                }
                
                // Перемещаем загруженный файл
                if (move_uploaded_file($new_position_img['tmp_name'], $image_file_path)) {
                    
                    // Изменяем изображение и сохраняем путь до изображения в БД
                    $new_position_photo = resizeImage($image_file_path, 200, 200);
    
                    // Делаем запрос в БД на изменение пути до фото
                    $sql = "UPDATE `food` SET `photo`= :new_position_photo WHERE food_id = $position_id";
                    $stmt = $pdo -> prepare($sql);
                    
                    $stmt -> bindParam(':new_position_photo', $new_position_photo, PDO::PARAM_STR);
                    $stmt -> execute();
                } else {
    
                    // Передаем сообщение об ошибке
                    $message = 'Ошибка при загрузке фото на сервер!';
                    $url_message = http_build_query(['message-error' => $message]);
            
                    Header('Location: ../../../checkout/staff-pages/admin/admin_position-edit?position_id='. $position_id .'&' . $url_message);
                }
            }
        }

        // Если ошибок нет
        if (!isset($message)) {
            
            // Передаем сообщение об ошибке
            $message = 'Успешное изменение!';
            $url_message = http_build_query(['message-success' => $message]);

            // Перенаправляем пользоваетля на профиль
            Header('Location: ../../../checkout/staff-pages/admin/admin_position-edit?position_id='. $position_id .'&' . $url_message);
        }
    }
?>