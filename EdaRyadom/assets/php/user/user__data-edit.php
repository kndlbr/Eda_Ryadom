<?php
    require_once('../../db/pdo_config.php');
    session_start();
    
    // Функция изменения размера изображения
    function resizeImage($file_path, $width, $height) {

        // Длинна и ширина необработанного фото в виде списка
        list($image_input_width, $image_input_height) = getimagesize($file_path);
        
        // Отрисовываем изображение в цвет, исходя из переданных параметров
        $resized_image = imagecreatetruecolor($width, $height);

        // В зависимоти от формата изображения выбираем функцию, которая нам даст информацию о изображении
        $image = imagecreatefromjpeg($file_path);

        // Функция сохранения пропорций изображения (на деле работает странно)
        imagecopyresampled($resized_image, $image, 0, 0, 0, 0, $width, $height, $image_input_width, $image_input_height);
        
        // Перезаписываем полученное изображение
        $new_position_file_path = pathinfo($file_path, PATHINFO_DIRNAME) . '/' . pathinfo($file_path, PATHINFO_FILENAME) . "." . pathinfo($file_path, PATHINFO_EXTENSION);

        // В зависимоти от формата изображения выбираем функцию
        imagejpeg($resized_image, $new_position_file_path);
        
        // Очистка памяти
        imagedestroy($image);
        imagedestroy($resized_image);

        // Получаем название изображения 
        $new_position_photo_name = pathinfo($file_path, PATHINFO_FILENAME) . "." . pathinfo($file_path, PATHINFO_EXTENSION);

        // Возвращаем название, чтобы вставить его в БД
        return $new_position_photo_name;
    }
    
    // Берем id пользователя и его роль из сессии
    $user_id = $_SESSION['user_id'];
    $role_id = $_SESSION['role_id'];

    // Парсим данные с формы
    $new_user_data = $_POST['new_data'];
    $new_user_img = $_FILES['new_img'];

    // Если ничего не ввели
    if ((empty($new_user_data[0])) && (empty($new_user_data[1])) && ($new_user_img['error'] >= 1 or empty($new_user_img))){

        // Передаем сообщение об ошибке
        $message = 'Вы ничего не ввели!';
        $url_message = http_build_query(['message-error' => $message]);

        Header('Location: ../../../checkout/profile_edit?' . $url_message);
    } else {
        
        // Если ввели имя
        if (!empty($new_user_data[0])) {
            $sql = "UPDATE `users` SET `fname`= :fname WHERE user_id = $user_id";
            $stmt = $pdo -> prepare($sql);
    
            $f_name = htmlspecialchars($new_user_data[0]);
            $stmt -> bindParam(':fname', $f_name, PDO::PARAM_STR);
            
            $stmt -> execute();

            $_SESSION['fname'] = $f_name;
        }
        
        // Если ввели телефон или фамилию
        if (!empty($new_user_data[1])) {
    
            // Если это обычный пользователь, то обновляем номер телефона
            if ($role_id == 4) {
    
                // Экранируем номер телефона и задаем шаблон проверки 
                $phone_number = htmlspecialchars($new_user_data[1]);
                $pattern = '/^\+7\d{10}$/';
                
                // Если номер совпал с шаблоном 
                if (preg_match($pattern, $phone_number)){
                    
                    // Запрос на изменение телефона
                    $sql = "UPDATE `users` SET `phone`= :phone WHERE user_id = $user_id";
                    $stmt = $pdo -> prepare($sql);

                    $stmt -> bindParam(':phone', $phone_number, PDO::PARAM_STR);
                    $stmt -> execute();
                } else {
                    // Если случилось несовпадение
    
                    // Передаем сообщение об ошибке
                    $message = 'Номер телефона введен некорректно! Введите номер в формате +7XXXXXXXXXX';
                    $url_message = http_build_query(['message-error' => $message]);
    
                    Header('Location: ../../../checkout/profile_edit?' . $url_message);
                }
    
            } else {
                
                // Если сотрудник, то меняем фамилию
                $sql = "UPDATE `users` SET `lname`= :lname WHERE user_id = $user_id";
                $stmt = $pdo -> prepare($sql);
    
                $l_name = htmlspecialchars($new_user_data[1]);
                $stmt -> bindParam(':lname', $l_name, PDO::PARAM_STR);
                
                $stmt -> execute();
            }
        }
    
        // Если фото загрузилось нормально
        if ($new_user_img['error'] == 0){
            
            // Получение данных о фото (имя и расширение)
            $file_name = pathinfo($new_user_img['name']);
            $extension = strtolower($file_name['extension']);
    
            $file_extentions = ['jpg', 'jpeg'];
            
            // Если фото не прошло проверку на расширение
            if (!in_array($extension, $file_extentions)) {
    
                // Передаем сообщение об ошибке
                $message = 'Неверный формат файла!';
                $url_message = http_build_query(['message-error' => $message]);
    
                Header('Location: ../../../checkout/profile_edit?' . $url_message);
            } else {
                
                // Определяем Логин пользователя
                $sql = "SELECT login FROM `users` WHERE user_id = $user_id";
                $stmt = $pdo -> prepare($sql);
    
                $stmt -> execute();                                       //                :D                          
                $login = $stmt -> fetch(PDO::FETCH_ASSOC);
                
                $username = $login['login']; // Заебись колеса едут. Ахуительнейший запрос, просто пиздат, вот я смотрб на него, и он так профессионально играет с моей ориентацией
    
                // Строим имя файла
                $user_photo_name = "{$username}_profile-photo_";

                // Unix время для приписки к фото (для моментального обновления)
                $time = time();
    
                // Определяем директорию файла и прописываем исходный путь сохраннения файла
                $upload_dir = "../../img/users/{$username}/";
                $image_file_path = $upload_dir . $user_photo_name . $time . '.' . $extension;

                // Проверка на существование директории для файла. Если нет - создаем
                if (!is_dir($upload_dir)){
                    mkdir($upload_dir, 0777, true);
                } else {

                    // Если директория существует - сканируем на наличие старых фото
                    $files = scandir($upload_dir);

                    // Удаялем каждый файл в директории, чтобы не засорять её
                    foreach ($files as $image){
                        $image_path = $upload_dir . $image;
                        unlink($image_path);
                    }
                }
                
                // Перемещаем загруженный файл
                if (move_uploaded_file($new_user_img['tmp_name'], $image_file_path)) {
                    
                    // Изменяем изображение и сохраняем путь до изображения в БД
                    $user_photo = $username . '/' . resizeImage($image_file_path, 200, 200);
                    
                    // Сохраняем путь в сессию
                    $_SESSION['user_photo'] = $user_photo;
    
                    // Делаем запрос в БД на изменение пути до фото
                    $sql = "UPDATE `users` SET `photo`= :user_photo WHERE user_id = $user_id";
                    $stmt = $pdo -> prepare($sql);
                    
                    $stmt -> bindParam(':user_photo', $user_photo, PDO::PARAM_STR);
                    $stmt -> execute();
                } else {
    
                    // Передаем сообщение об ошибке
                    $message = 'Ошибка при загрузке фото на сервер!';
                    $url_message = http_build_query(['message-error' => $message]);
            
                    Header('Location: ../../../checkout/profile_edit?' . $url_message);
                }
            }
        }

        // Если ошибок нет
        if (!isset($message)) {
            
            // Перенаправляем пользоваетля на профиль
            Header('Location: ../../../checkout/profile');
        }
    }
?>