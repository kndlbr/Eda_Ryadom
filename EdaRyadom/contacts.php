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
        <title>Еда Рядом -> Контакты</title>
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

            <h1 class="visually-hidden">Еда Рядом: Контакты</h1>

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