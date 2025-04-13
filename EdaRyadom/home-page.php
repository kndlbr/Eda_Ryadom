<?php
    require_once('./assets/db/pdo_config.php');
    session_start();

    // Если на страницу попал стаф
    if (isset($_SESSION['auth']) AND $_SESSION['role_id'] < 4) {
        Header('Location: ./checkout/profile');
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
        <title>Еда Рядом -> Главная</title>
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

            <h1 class="visually-hidden">Еда Рядом: Главная страница</h1>

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
                                <h3 class="title-huge darken"><a href="tel: +7952000724">Телефон</a></h3>
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

            <section class="section about">
                <div class="about-body container">
                    <div class="section-title">
                        <h2 class="title-huge">О КОМПАНИИ</h2>
                    </div>

                    <ul class="about-body-list">
                        <li class="about-body-item">
                            <div class="about-card">
                                <div class="about-card-title">
                                    <h3 class="title-very-huge">10+ ЛЕТ</h3>
                                    <h4 class="title-huge">РАБОТАЕМ НА РЫНКЕ</h4>
                                </div>

                                <div class="about-card-text">
                                    <p>«ЕдаРядом» — онлайн-магазин, кафе-огород из фермерских продктов, а также выездные банкеты любой сложности из Алтайского края и республики Алтай. Мы работаем, начиная с 2013 года.</p>
                                </div>
                            </div>
                        </li>
                        <li class="about-body-item">
                            <div class="about-card">
                                <div class="about-card-title">
                                    <h3 class="title-very-huge">ТОЛЬКО СВЕЖИЕ ПРОДУКТЫ</h3>
                                    <h4 class="title-huge">ПО ПРИЕМЛИМЫМ ЦЕНАМ</h4>
                                </div>

                                <div class="about-card-text">
                                    <p>Мы предлагаем понятные продукты от понятных производителей. Их могут производить личные подсобные и крестьянские хозяйства, небольшие цеха и маленькие заводы, целые коллективы и отдельно взятые люди.</p> <br>
                                    <p>Производители не скрываются за торговой маркой, а лично своим именем отвечают за качество.</p>
                                </div>
                            </div>
                        </li>
                        <li class="about-body-item">
                            <div class="about-card">
                                <div class="about-card-title">
                                    <h3 class="title-very-huge">ШТАТ ИЗ 10+</h3>
                                    <h4 class="title-huge">ПРОФЕССИОНАЛОВ</h4>
                                </div>

                                <div class="about-card-text">
                                    <p>Сегодня в «Еде Рядом» сформирован штат энтузиастов-професионналов, которые верят в свое дело.</p> <br>
                                    <p>О наших продуктах заботятся менеджеры, курьеры, водители, упаковщики. И, конечно, в нашей команде присутствуют замечательные фермеры и производители продуктов.</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

            <section class="section catalog">
                <div class="catalog-body container">
                    <div class="section-title">
                        <h2 class="title-huge">Каталог</h2>
                    </div>

                    <ul class="catalog-body-list">
                        <?php
                            $sql = "SELECT * FROM `food` ORDER BY RAND() limit 4"; // Запрос
                            $statement = $pdo -> prepare($sql); // Подготовка запроса
                            
                            $statement -> execute(); // Отправка запроса
                            $products = $statement -> fetchAll(PDO::FETCH_ASSOC); // запись полученных данных
                        ?>

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
                                        <a href="./catalog" class="card-button button">Подробнее</a>
                                    <?php else: ?>
                                        <a href="./checkout/sign_in" class="card-button button">Подробнее</a>
                                    <?php endif; ?>
                                </div>

                                <img src="./assets/img/products/<?php echo htmlspecialchars($product['photo']); ?>" alt="product image" class="catalog-card-image" loading="lazy">
                            </li>

                        <?php endforeach; ?>
                    </ul>

                    <a href="./catalog" class="catalog-button button title-medium">Показать еще</a>
                </div>
            </section>

            <section class="section map">
                <div class="map-body container">
                    <div class="section-title">
                        <h2 class="title-huge">Где мы находимся</h2>
                    </div>

                    <div class="map-container">
                        <iframe src="https://yandex.ru/map-widget/v1/?ll=83.757174%2C53.369969&mode=search&oid=1722034825&ol=biz&z=16.79"  frameborder="1" allowfullscreen="true"></iframe>
                    </div>
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