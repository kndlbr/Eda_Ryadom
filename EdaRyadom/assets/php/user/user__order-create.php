<?php 
    require_once('../../db/pdo_config.php');
    session_start();

    // Ловим значение о способе доставки
    $input_value = $_POST['deliveryInput'];

    // Если способ получения доставка
    if ($input_value == 1) {
        
        // Получаем данные о доставке и инициализируем конечный массив с данными
        $delivery_info = $_POST['delivery_info'];
        $delivery_data = [];

        // Проверяем на пустые поля в фолме
        foreach ($delivery_info as $delivery_input_value){
            if (empty($delivery_input_value)){
                $delivery_input_value = 'Нет данных';
            }
            array_push($delivery_data, $delivery_input_value);
        }

        // Готовый строка с адресом
        $delivery_addres = 'Город: '.$delivery_data[0].', Ул. '.$delivery_data[1].', Дом: '.$delivery_data[2].', Квартира/офис: '.$delivery_data[3].', Подъезд: '.$delivery_data[4].', Этаж: '.$delivery_data[5];
    } 
    
    // Если способ получения самовывоз
    else {
        $delivery_addres = 'Самовывоз';
    }

    // Парсим id клиента с сессии
    $client_id = $_SESSION['user_id'];
    
    // Получение даты и времени
    date_default_timezone_set('Asia/Barnaul');
    $order_date = date("Y-m-d H:i");
    $shift_date = date("Y-m-d");

    $sql = "SELECT `shift_id` FROM `shifts` WHERE date = '$shift_date'";
    $stmt = $pdo -> prepare($sql);

    $stmt -> execute();
    $shift_info = $stmt -> fetch(PDO::FETCH_ASSOC);

    // Проверка на открытую смену (Невозможно заказать, когда кафе не работает)
    if ($shift_info === false){
        
        // Передаем сообщение об ошибке
        $message = 'Похоже вы заказываете в день, когда мы не работаем или еще не открылись! Попробуйте заказать позже';
        $url_message = http_build_query(['message-error' => $message]);
        
        Header('Location: ../../../checkout/profile?'. $url_message);
    } else {

        $shift_id = $shift_info['shift_id'];
        
        // Создаем "каркас" заказа, содержащий в себе статус "Принят", id заказа, id клиента(который сделал заказ) и информацию о доставке заказа
        $sql = "INSERT INTO `orders`(`date`, `status_id`, `adress_delivery`, `client_id`, shift_id) VALUES ('$order_date', '6', '$delivery_addres', '$client_id', '$shift_id')";
        $stmt = $pdo -> prepare($sql);
    
        $stmt -> execute();
    
        // Находим только что созданный заказ, чтобы потом наполнить его содержимым позиций и их колчества
        $sql = "SELECT `order_id` FROM `orders` ORDER BY order_id DESC LIMIT 1";
        $stmt = $pdo -> prepare($sql);
    
        $stmt -> execute();
    
        $created_order_id = $stmt -> fetch(PDO::FETCH_ASSOC);
        $created_order_id = $created_order_id['order_id'];
    
        // Получаем данные о продуктах в корзине и их количесте в виде мдвух массивов
        $cart_product_count = $_POST['cartProductCount'];
        $cart = $_SESSION['cart'];
    
        // Считаем кол-во итераций для заполнения базы данных
        $j = count($cart);
            
        // Цикл для перебора массивов и заполнение заказа этитми позициями
        for ($i = 0; $i < $j; $i++) { 
            $product_id = $cart[$i];
            $product_amount = $cart_product_count[$i];
    
            $sql = "INSERT INTO `food_order`(`order_id`, `food_id`, `count`) VALUES ($created_order_id, $product_id, $product_amount)";
            $stmt = $pdo -> prepare($sql);
            
            $stmt -> execute();
        } 
    
        // Чистим корзину
        $_SESSION['cart'] = [];
    }

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!--Подключение шрифтов с гугла-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="../../../plugins/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="../../css/main.css">
        <link rel="icon" href="../../img/logo.png">
        <title>Успех</title>
    </head>
    <body>

        <main class="error-content">

            <div class="trangle-top"></div>

            <h1 class="visually-hidden">Успешная обработка заказа</h1>

            <div class="error-body container">

                <div class="error-title">
                    <h2 class="title-very-huge">Отлично!</h2>
                </div>

                <div class="error-info">
                    <p>Ваш заказ был успешно обработан</p>

                    <?php if($input_value == 1): ?>
                        <p>Ожидайте звонка от оператора <span>в течении 10 минут,</span> для подтверждения вашего заказа</p>
                    <?php else: ?>
                        <p>Ждем вас <span>для выдачи вашего заказа</span></p>
                    <?php endif; ?>
                    
                    <p class="back-to-home">Вернутся к <a href="../../../home-page" class="home-page-href">покупкам</a></p>
                </div>
            </div>

            <div class="trangle-bottom"></div>
        </main>
    </body>
</html>