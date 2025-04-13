<?php
    require_once('../assets/db/pdo_config.php');
    session_start();

    // Проверка на авторизацию
    if (empty($_SESSION['auth'])) {
        header('Location: ../home-page');
    }

    // Параметры с сессии
    if (isset($_SESSION['user_photo'])){
        $user_photo = $_SESSION['user_photo'];
    }
    $user_id = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $new_password = $_POST['new_password'];
        $repeat_password = $_POST['repeat_password'];

        if ($new_password == $repeat_password) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            $sql = "UPDATE `users` SET `password` = :new_password WHERE `user_id` = '$user_id'";
            $stmt = $pdo -> prepare($sql);

            $stmt -> bindParam(':new_password', $hashed_password, PDO::PARAM_STR);
            $stmt -> execute();

            $_SESSION['password'] = $new_password;
        } else {
            $answer['Error'] = 'Пароли не совпадают!';
        }
    }

    $sql = "SELECT login, password, phone, role_name, photo FROM `users`, `role` WHERE user_id = $user_id AND role.role_id = users.role_id";
    $stmt = $pdo -> prepare($sql);
    
    $stmt -> execute();
    $user_info = $stmt -> fetch(PDO::FETCH_ASSOC);

    if ($_SESSION['role_id'] == 4) {
        $sorted_cart = array_count_values($_SESSION['cart']);
        KSort($sorted_cart);
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="../plugins/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/main.css">
        <link rel="icon" href="../assets/img/logo.png">
        <title>Еда Рядом -> Личный кабинет</title>
    </head>
    <body>

        <!-- Header -->
        <header class="header">
            <div class="header-panel container d-flex justify-content-between align-items-center">
                <div class="logo d-flex align-items-center">
                    <img src="../assets/img/logo.png" alt="logo image" class="logo__img" draggable="false">
                    <div class="logo__text d-flex flex-column">
                        <span class="logo__text_green">Еда</span> Рядом
                    </div>
                </div>

                <?php if ($_SESSION['role_id'] == 4): ?>
                    <nav class="header-panel-nav visually-hidden">
                        <a href="#" class="title-medium lime-color-text">История</a>
                    </nav>
                <?php else: ?>
                    <nav class="header-panel-nav">
                        <a href="./staff-pages/orders_history" class="title-medium lime-color-text">История</a>
                    </nav>
                <?php endif; ?>
                
                <div class="header-panel-profile-stats">
                    <div class="header-panel-profile-stats">
                        <span class="title-medium darken d-none d-md-flex"><?php echo htmlspecialchars($_SESSION['fname']); ?></span>
                        <a href="./profile">
                            <?php if (isset($user_photo)): ?>
                                <img src="../assets/img/users/<?= $user_info['photo']; ?>" alt="profile image" class="header-profile-image panel-image" loading="lazy">
                            <?php else: ?>
                                <img src="../assets/img/thumbnail.jpg" alt="profile image" class="header-profile-image panel-image" loading="lazy">
                            <?php endif; ?> 
                        </a>
                    </div>
                    <div class="header-panel-logout d-none d-lg-flex">
                        <form method="POST" action="../assets/php/exit">
                            <button type="submit" class="title-medium darken">Выйти</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main class="content panel-content">

            <h1 class="visually-hidden">Еда Рядом: Личный кабинет</h1>
            
            <section class="profile">
                <h2 class="visually-hidden">Информация о пользователе</h2>

                <div class="profile-body container">

                    <div class="back-button">
                        <svg width="26" height="16" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 6.75C21.4142 6.75 21.75 6.41421 21.75 6C21.75 5.58579 21.4142 5.25 21 5.25V6.75ZM0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM21 5.25L1 5.25V6.75L21 6.75V5.25Z" fill="#4D4D4D"/>
                        </svg>
                        
                        <?php if ($_SESSION['role_id'] == 1): ?>
                            <a href="./staff-pages/waiter/waiter_panel" class="">Назад</a>
                        <?php elseif ($_SESSION['role_id'] == 2): ?>
                            <a href="./staff-pages/cook/cook_panel" class="">Назад</a>
                        <?php elseif ($_SESSION['role_id'] == 3): ?>
                            <a href="./staff-pages/admin/admin_panel" class="">Назад</a>
                        <?php elseif ($_SESSION['role_id'] == 4): ?>
                            <a href="../home-page" class="">Назад</a>
                        <?php endif; ?>
                    </div>

                    <div class="profile-body-wrap">
                        <div class="profile-body-section">
                            <h3 class="panel-heading title-huge darken">Личный кабинет</h3>

                            <div class="profile-data d-flex">
                                <div class="profile-image d-flex align-items-center flex-column">

                                    <?php if (isset($_SESSION['user_photo'])): ?>
                                        <img src="../assets/img/users/<?= $_SESSION['user_photo']; ?>" alt="profile image" class="profile-data-image panel-image" loading="lazy">
                                    <?php else: ?>
                                        <img src="../assets/img/thumbnail.jpg" alt="profile image" class="profile-data-image panel-image" loading="lazy">
                                    <?php endif; ?> 

                                    <span class="title-medium darken profile-name"><?php echo htmlspecialchars($_SESSION['fname']); ?></span>
                                    <div class="profile-edit-button">
                                        <svg class="svg-lime" fill="none" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.661,19.113,3,21l1.887-5.661ZM20.386,7.388a2.1,2.1,0,0,0,0-2.965l-.809-.809a2.1,2.1,0,0,0-2.965,0L6.571,13.655l3.774,3.774Z"/>
                                        </svg>
                                        <a href="./profile_edit" class="title-small lime">Редактировать</a>
                                    </div>
                                </div>
                                <div class="panel-card profile-info">
                                    <div class="panel-card-heading">
                                        <h4 class="title-medium darken">Основная информация</h4>
                                    </div>
                                    <div class="panel-card-content">
                                        <ul class="panel-card-list d-flex flex-column">
                                            <li class="panel-card-list-item">
                                                <span class="panel-card-parametr attribute">Логин:</span>
                                                <span class="panel-card-parametr"><?php echo htmlspecialchars($user_info['login']); ?></span>
                                            </li>
                                            <li class="panel-card-list-item">
                                                <span class="panel-card-parametr attribute">Пароль:</span>
                                                <div class="password-container d-flex align-items-center">
                                                    <p class="panel-card-parametr visually-hidden" id="password-text"><?php echo $_SESSION['password']; ?></p>
                                                    <svg id="eye-button" width="24" height="24" viewBox="0 0 24 24" fill="#FFFFFF" stroke="#96DD1E" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M2.99902 3L20.999 21M9.8433 9.91364C9.32066 10.4536 8.99902 11.1892 8.99902 12C8.99902 13.6569 10.3422 15 11.999 15C12.8215 15 13.5667 14.669 14.1086 14.133M6.49902 6.64715C4.59972 7.90034 3.15305 9.78394 2.45703 12C3.73128 16.0571 7.52159 19 11.9992 19C13.9881 19 15.8414 18.4194 17.3988 17.4184M10.999 5.04939C11.328 5.01673 11.6617 5 11.9992 5C16.4769 5 20.2672 7.94291 21.5414 12C21.2607 12.894 20.8577 13.7338 20.3522 14.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </div>
                                            </li>
                                            
                                            <?php if ($_SESSION['role_id'] == 4):?>
                                                <li class="panel-card-list-item">
                                                    <span class="panel-card-parametr attribute">Телефон:</span>
                                                    <span class="panel-card-parametr"><?php echo htmlspecialchars($user_info['phone']); ?></span>
                                                </li>
                                            <?php else:?>
                                                <li class="panel-card-list-item">
                                                    <span class="panel-card-parametr attribute">Должность:</span>
                                                    <span class="panel-card-parametr"><?php echo htmlspecialchars($user_info['role_name']); ?></span>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <div class="profile-body-section d-none d-lg-flex flex-column">
                            <h3 class="panel-heading title-huge darken">Смена пароля</h3>
    
                            <div class="profile-change-pass-form">
                                <div class="panel-card profile-info">
                                    <div class="panel-card-heading">
                                        <h4 class="title-medium darken">Смена пароля</h4>
                                    </div>
                                    <div class="panel-card-content">
                                        <form action="#" method="POST" class="panel-form">
                                            <ul class="panel-form-content">
                                                <li class="panel-form-input-element">
                                                    <input type="password" class="panel-form-input" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z0-9]+$" title="Пароль должен содержать в себе хотя бы 1 строчную, заглавную латинские буквы и 1 цифру" minlength="8" maxlength="20" name="new_password" placeholder="Введите новый пароль" required>
                                                </li>
                                                <li class="panel-form-input-element">
                                                    <input type="password" class="panel-form-input" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z0-9]+$" title="Пароль должен содержать в себе хотя бы 1 строчную, заглавную латинские буквы и 1 цифру" minlength="8" maxlength="20" name="repeat_password" placeholder="Повторите пароль" required>
                                                </li>
                                            </ul>
                                            <button class="panel-button form-button title-medium" type="submit">Изменить</button>
                                        </form>

                                        <?php if (isset($answer['Error'])): ?>
                                            <span class="error-message"><?= $answer['Error']?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="profile-body container d-flex d-lg-none">

                    <div class="profile-body-section">
                        <h3 class="panel-heading title-huge darken">Смена пароля</h3>

                        <div class="profile-change-pass-form">
                            <div class="panel-card profile-info">
                                <div class="panel-card-heading">
                                    <h4 class="title-medium darken">Смена пароля</h4>
                                </div>
                                <div class="panel-card-content">
                                    <form action="" method="POST" class="panel-form">
                                        <ul class="panel-form-content">
                                            <li class="panel-form-input-element">
                                                <input type="password" class="panel-form-input" minlength="8" maxlength="20" name="new_password" placeholder="Введите новый пароль" required>
                                            </li>
                                            <li class="panel-form-input-element">
                                                <input type="password" class="panel-form-input" minlength="8" maxlength="20" name="repeat_password" placeholder="Повторите пароль" required>
                                            </li>
                                        </ul>
                                        <button class="panel-button form-button title-medium" type="submit">Изменить</button>
                                    </form>

                                    <?php if (isset($answer['Error'])): ?>
                                        <span class="error-message"><?= $answer['Error']?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <?php if ($_SESSION['role_id'] == 4): ?>

                <section class="cart ">
                    <h2 class="visually-hidden">Корзина</h2>

                    <div class="cart-body container">
                        <h3 class="panel-heading title-huge darken">Корзина</h3>

                        <?php if(isset($_GET['message-error'])): ?>
                            <span class="message error"><?= $_GET['message-error']; ?></span>
                        <?php endif; ?>

                        <form action="../assets/php/user/user__order-create.php" method="POST" id="userCreateOrderForm">
                            <div class="cart-body-table-wrap">

                                <?php if(empty($_SESSION['cart'])): ?>

                                    <div class="panel-card text-center">
                                        <h4>Вы еще ничего не добавли в корзину</h4>                            
                                    </div>

                                <?php else:?>
                                    
                                    <table class="cart-body-table table">
                                        <thead>
                                            <tr class="table-head">
                                                <th class="cart-body-table-heading title-medium">Изображение</th>
                                                <th class="cart-body-table-heading title-medium">Название</th>
                                                <th class="cart-body-table-heading title-medium">Цена</th>
                                                <th class="cart-body-table-heading title-medium" colspan="2">Кол-во</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                            
                                            <?php foreach ($sorted_cart as $product_id => $amount): ?>
                                                <?php
                                                    $sql = "SELECT `photo`, `price`, `name` FROM `food` WHERE food_id = $product_id";
                                                    $stmt = $pdo -> prepare($sql);

                                                    $stmt -> execute();
                                                    
                                                    $product_info = $stmt -> fetch(PDO::FETCH_ASSOC);
                                                ?>
                                                <tr>
                                                    <td class="w-25">
                                                        <img src="../assets/img/products/<?php echo htmlspecialchars($product_info['photo']); ?>" class="cart-image" alt="product image" loading="lazy">
                                                    </td>
                                                    <td>
                                                        <p class="card-body-table-text" id="productName"><?php echo htmlspecialchars($product_info['name']); ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="card-body-table-text" id="productPrice"><?php echo htmlspecialchars($product_info['price']); ?> ₽</p>
                                                    </td>
                                                    <td>
                                                        <input class="card-body-table-text" name="cartProductCount[]" type="number" id="productCount" value="<?php echo $amount ?>" min="1" max="20">
                                                    </td>
                                                    <td>
                                                        <a href="../assets/php/user/user__product-delete?product_id=<?php echo $product_id ?>" class="white-color-text panel-button cart-table-button">Убрать</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            
                                        </tbody>
                                    </table>

                                <?php endif; ?>                   
                            </div>

                            <?php if (!empty($_SESSION['cart'])): ?>
                                <div class="panel-card">
                                    <div class="panel-card-content d-flex align-items-center justify-content-between">
                                        <span class="panel-card-parametr">Итого:</span>
                                        <span class="panel-card-parametr attribute lime-color-text" id="totalSum"></span>
                                    </div>
                                </div>

                                <div class="panel-card profile-info mt-3">
                                    <div class="panel-card-heading">
                                        <h4 class="title-medium darken">Оформление заказа</h4>
                                    </div>
                                    <div class="panel-card-content">
                                        <ul class="panel-form-content">
                                            <li class="panel-form-input-element custom-radio">
                                                <input type="radio" class="custom-radio-button" id="delivery" name="deliveryInput" value="1" checked>
                                                <label for="delivery">Доставка</label>
                                                <span></span>
                                            </li>
                                            <li class="panel-form-input-element custom-radio">
                                                <input type="radio" class="custom-radio-button" id="selfPickup" name="deliveryInput" value="2">
                                                <label for="selfPickup">Самовывоз</label>
                                                <span></span>
                                            </li>
                                        </ul>

                                        <ul class="panel-form-content" id="cartDeliveryList">
                                            <li class="panel-form-input-element">
                                                <input type="text" class="panel-form-input" maxlength="23" name="delivery_info[]" placeholder="Город, населенный пункт:" required>
                                            </li>
                                            <li class="panel-form-input-element">
                                                <input type="text" class="panel-form-input" maxlength="23" name="delivery_info[]" placeholder="Название улицы:" required>
                                            </li>
                                            <li class="panel-form-input-element">
                                                <input type="text" class="panel-form-input" maxlength="3" name="delivery_info[]" placeholder="Номер дома:" required>
                                            </li>
                                            <li class="panel-form-input-element">
                                                <input type="text" class="panel-form-input" maxlength="4" name="delivery_info[]" placeholder="Квартира/офис:">
                                            </li>
                                            <li class="panel-form-input-element">
                                                <input type="text" class="panel-form-input" maxlength="4" name="delivery_info[]" placeholder="Подъезд:">
                                            </li>
                                            <li class="panel-form-input-element">
                                                <input type="text" class="panel-form-input" maxlength="3" name="delivery_info[]" placeholder="Этаж:">
                                            </li>
                                        </ul>
                                    </div>

                                    <button class="panel-button form-button title-medium" form="userCreateOrderForm" type="submit">Заказать</button>
                                </div>
                            <?php endif; ?>
                        </form>

                        <a href="../assets/php/exit" class="panel-button exit-button title-huge darken d-flex d-lg-none">Выйти</a>
                    </div>
                </section>

            <?php elseif ($_SESSION['role_id'] < 3): ?>
                <?php
                    $sql = "SELECT shifts.date AS date FROM `shifts`, `shift_user` WHERE shift_user.user_id = $user_id AND shift_user.shift_id = shifts.shift_id AND shifts.status_id < 3"; 
                    $stmt = $pdo -> prepare($sql);

                    $stmt -> execute();

                    $active_shifts_info = $stmt -> fetchAll(PDO::FETCH_ASSOC);
                ?>

                <section class="shifts">
                    <h2 class="visually-hidden">Ближайшие смены</h2>

                    <div class="shifts-body container">
                        <h3 class="panel-heading title-huge darken">Ближайшие смены</h3>

                        <?php if (!empty($active_shifts_info)): ?>

                            <ul class="shifts-body-list d-flex">

                                <?php foreach($active_shifts_info as $active_shift): ?>

                                    <li class="shifts-body-card">
                                        <div class="panel-card shift-card">
                                            <div class="panel-card-heading">
                                                <h4 class="title-medium darken">Основная информация</h4>
                                            </div>
                                            <div class="panel-card-content d-flex align-items-center justify-content-between">
                                                <span class="panel-card-parametr">Дата:</span>
                                                <span class="panel-card-parametr attribute lime-color-text"><?= date("d.m.y", strtotime($active_shift['date'])); ?></span>
                                            </div>
                                        </div>
                                    </li>

                                <?php endforeach; ?>

                            </ul>
                        
                        <?php else: ?>
                            <div class="d-flex justify-content-center mb-5">
                                <p class="title-medium darken">Нет ближайших смен...</p>
                            </div>
                        <?php endif; ?>

                        <a href="../assets/php/exit" class="panel-button exit-button title-huge darken d-flex d-lg-none">Выйти</a>
                    </div>
                </section>

            <?php else: ?>

                <section class="admin">
                    <h2 class="visually-hidden">Выйти</h2>

                    <div class="admin-body container">
                        <a href="../assets/php/exit" class="panel-button exit-button title-huge darken d-flex d-lg-none">Выйти</a>
                    </div>
                </section>

            <?php endif; ?>

        </main>

        <!-- Footer -->
        <?php require_once('../assets/view/checkout/footer.inc'); ?>

    </body>
    <script src="../assets/js/main.js"></script>
</html>