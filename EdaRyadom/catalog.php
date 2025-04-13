<?php
    require_once('./assets/db/pdo_config.php');
    session_start();

    // Если на страницу попал стаф
    if (isset($_SESSION['auth']) AND $_SESSION['role_id'] < 4) {
        Header('Location: ./checkout/profile');
    }
    
    // Поиск Товара
    if (isset($_GET['search'])){

        // Получаем поисквой текст
        $search_text = htmlspecialchars($_GET['search']);

        // Запрос на выгрузку только нужного хавчика
        $sql = "SELECT * FROM `food` WHERE name LIKE '%$search_text%'";
    } else{
        // Запрос на выгрузку хавчика в каталоге
        $sql = "SELECT * FROM `food`";
    }

    $statement = $pdo -> prepare($sql); // Подготовка запроса

    $statement -> execute(); // Отправка запроса
    $products = $statement -> fetchAll(PDO::FETCH_ASSOC); // запись полученных данных

    if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST['product_id'])) {

            // Добавляем товар в корзину
            array_push($_SESSION['cart'], $_POST['product_id']); 

            // Удаление повторяющихся значений в корзине
            $_SESSION['cart'] = array_unique($_SESSION['cart']); 

    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

        <!--Подключение шрифтов с гугла-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="./plugins/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="./assets/css/main.css">
        <link rel="icon" href="./assets/img/logo.png">
        
        <!-- Указываем для поисковых ботов, что эту страницу надо индексировать без GET параметров -->
        <link rel="canonical" href="https://edaryadom.free.nf/catalog">
        <title>Еда Рядом -> Каталог</title>
    </head>
    <body>

        <!-- Header -->
        <?php
            if (isset($_SESSION['auth'])) {
                require('assets/view/header_authorized.inc');
            } else {
                require('assets/view/header_unauthorized.inc');
            }
        ?>
        
        <!-- Main content -->
        <main class="content">

            <h1 class="visually-hidden">Еда Рядом: Каталог</h1>

            <section class="section main-info">

                <h2 class="visually-hidden">Основная информация</h2>

                <div class="main-info-body container">
                    <div class="main-info-card main-card">
                        <div class="title-medium">
                            <p>Только у нас</p>
                        </div>
                        <h3 class="title-huge">СВЕЖИЕ ФЕРМЕРСКИЕ ПРОДУКТЫ</h3>
                    </div>
                    <div class="side-cards">
                        <div class="main-info-card delivery-card">
                            <div class="title-medium darken">
                                <p>Работает по всему Барнаулу и за его пределами</p>
                            </div>
                            <h3 class="title-huge darken"><a href="<?php if (empty($_SESSION['auth'])){ echo './checkout/sign_in'; } else { echo './checkout/profile'; } ?>">ДОСТАВКА</a></h3>
                        </div>
                        <div class="bottom-cards">
                            <div class="main-info-card contacts-card card-rectangle">
                                <div class="title-medium darken d-flex d-none d-lg-flex">
                                    <p>Хотите сделать заказ?</p>
                                </div>
                                <h3 class="title-huge darken"><a href="tel: +7952000724">телефон</a></h3>
                            </div>
                            <div class="main-info-card catalog-card card-rectangle">
                                <div class="title-medium d-flex d-none d-lg-flex">
                                    <p>Ознакомьтесь с ассортиментом</p>
                                </div>
                                <h3 class="title-huge"><a href="./catalog">КАТАЛОГ ТОВАРОВ</a></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section catalog">
                <div class="catalog-body container">
                    <div class="section-title">
                        <h2 class="title-huge">Каталог</h2>
                    </div>

                    <form action="" method="GET" class="search-field-wrapp">
                        <div class="search-field">
                            <input class="search-field-input" type="text" placeholder="Найти желаемое..." name="search">
                            <svg class="d-none d-sm-flex search-field-icon" width="25" height="26" viewBox="0 0 25 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="10" cy="10" r="9" stroke="#96DD1E" stroke-width="2"/>
                                <path d="M22.7929 25.7071C23.1834 26.0976 23.8166 26.0976 24.2071 25.7071C24.5976 25.3166 24.5976 24.6834 24.2071 24.2929L22.7929 25.7071ZM14.7929 17.7071L22.7929 25.7071L24.2071 24.2929L16.2071 16.2929L14.7929 17.7071Z" fill="#96DD1E"/>
                            </svg>
                        </div>
    
                        <button class=" d-none d-sm-flex panel-button search-field-button title-small bg-lime-color" type="submit">Найти</button>
                        <button class=" d-flex d-sm-none search-field-button panel-button" type="submit">
                            <svg width="25" height="26" viewBox="0 0 25 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="10" cy="10" r="9" stroke="#96DD1E" stroke-width="2"/>
                                <path d="M22.7929 25.7071C23.1834 26.0976 23.8166 26.0976 24.2071 25.7071C24.5976 25.3166 24.5976 24.6834 24.2071 24.2929L22.7929 25.7071ZM14.7929 17.7071L22.7929 25.7071L24.2071 24.2929L16.2071 16.2929L14.7929 17.7071Z" fill="#96DD1E"/>
                            </svg>
                        </button>
                    </form>

                    <?php if (isset($_GET['search'])): ?>
                        <a href="./catalog" type="button" class="panel-button cart-table-button button mb-4">Очистить поиск</a>
                    <?php endif; ?>

                    <?php if (!empty($products)): ?>

                        <ul class="catalog-body-list">

                            <?php foreach ($products as $product): ?>

                                <li class="catalog-card">
                                    <div class="catalog-card-description">
                                        <div class="catalog-card-heading">
                                            <h3 class="title-medium"><?php echo htmlspecialchars($product['name']); ?></h3>
                                            <h4 class="title-small"><?php echo htmlspecialchars($product['price']); ?> ₽</h4>
                                        </div>

                                        <div class="card-text">
                                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                                        </div>

                                        <?php if(isset($_SESSION['auth'])): ?>
                                            <form action="" method="post">
                                                <button name="product_id" value="<?php echo htmlspecialchars($product['food_id']); ?>" class="card-button button">В корзину</button>
                                            </form>
                                        <?php else: ?>
                                            <a href="./checkout/sign_in" class="card-button button">В корзину</a>
                                        <?php endif; ?>
                                    </div>

                                    <img src="./assets/img/products/<?php echo htmlspecialchars($product['photo']); ?>" alt="product image" class="catalog-card-image" loading="lazy">
                                </li>

                            <?php endforeach; ?>
                        </ul>
                        
                    <?php else: ?>
                        <div class="d-flex justify-content-center mb-5">
                            <p class="title-medium darken">По вашему запросу ничего не найдено...</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <?php
            if (isset($_SESSION['auth'])) {
                require('assets/view/footer_authorized.inc');
            } else {
                require('assets/view/footer_unauthorized.inc');
            }
        ?>

        <!-- Scripts -->
        <script src="./assets/js/main.js"></script>
        <script src="./plugins/bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>