<?php 
    require_once('../../../assets/db/pdo_config.php');
    session_start();

    // Проверка на авторизицию
    if ($_SESSION['role_id'] != 3 or empty($_SESSION['auth'])) {
        Header('Location: ../../../assets/errors/4XX/403');
    }
    
    // Параметры с сессии
    if (isset($_SESSION['user_photo'])){
        $user_photo = $_SESSION['user_photo'];
    }

    if (isset($_GET['search'])){

        // Получаем поисквой текст
        $search_text = htmlspecialchars($_GET['search']);

        // Запрос на выгрузку только нужного хавчика
        $sql = "SELECT * FROM `food` WHERE name LIKE '%$search_text%'";
    } else{
        // Запрос на выгрузку хавчика в каталоге
        $sql = "SELECT * FROM `food`";
    }
    
    $stmt = $pdo -> prepare($sql);

    $stmt -> execute();
    $food_list = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Обработка создания смены
        if (isset($_POST['сreateShift'])) {

            $date = $_POST['dateOfShift'];

            // Проверка на существование создаваемой смены
            $sql = "SELECT shift_id FROM `shifts` WHERE date = '$date'";
            $stmt = $pdo -> prepare($sql);

            $stmt -> execute();
            $existed_shift = $stmt -> fetch(PDO::FETCH_ASSOC);
            
            if ($existed_shift == false){

                // Если смены нету, то выполняем запрос
                $sql = "INSERT INTO `shifts`(`date`, `status_id`) VALUES ('$date','1')";
                $stmt = $pdo -> prepare($sql);
                
                $stmt -> execute();
                
                Header('Location: ./admin_shifts-page');
            } else {            
                
                // Сообщение об ошибке
                $message['Error'] = "Смена на этот день уже создана!";
            }
        }
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

        <link rel="stylesheet" href="../../../plugins/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="../../../assets/css/main.css">
        <link rel="icon" href="../../../assets/img/logo.png">

        <title>Еда Рядом -> Администратор: Панель управления</title>
    </head>

    <body>

        <!-- Header -->
        <?php require('../../../assets/view/checkout/staff_header.inc'); ?>

        <!-- Main content -->
        <main class="content panel-content">

            <h1 class="visually-hidden">Еда Рядом: Панель управления для Администратора</h1>

            <section class="admin-forms">

                <h2 class="visually-hidden">Еда Рядом: Формы для создания смен, сотрудников</h2>

                <div class="create-order-body d-flex flex-column container">
                    <div class="title-medium darken text-center greetengs">
                        Добро пожаловать,&nbsp; 
                        <span class="lime-color-text"><?php echo htmlspecialchars($_SESSION['fname']); ?>!</span>
                    </div>
    
                    <div class="create-order-content admin d-flex justify-content-between column-gap-4 row-gap-4">
                        <div class="panel-card admin-card">
                            <div class="panel-card-heading">
                                <h3 class="title-medium darken">РЕГИСТРАЦИЯ НОВОГО СОТРУДНИКА</h3>
                            </div>
                            <div class="panel-card-content">
                                <form action="../../../assets/php/admin/admin__employee-registration" method="POST" class="panel-form">
                                    <ul class="panel-form-content">
                                        <li class="panel-form-input-element">
                                            <select class="create-order-position" name="role-name" required>
                                                <option value="1">Официант</option>
                                                <option value="2">Повар</option>
                                            </select>
                                        </li>
                                        <li class="panel-form-input-element">
                                            <input type="text" class="panel-form-input" pattern="[A-Za-z0-9_-]*" title="Допускается только латиница, цифры и спец. символы '-_'" maxlength="20" name="login" placeholder="Введите логин" required>
                                        </li>
                                        <li class="panel-form-input-element">
                                            <input type="text" class="panel-form-input" name="fname" maxlength="35" placeholder="Введите имя" required>
                                        </li>
                                        <li class="panel-form-input-element">
                                            <input type="text" class="panel-form-input" name="lname" maxlength="35" placeholder="Введите фамилию" required>
                                        </li>
                                        <li class="panel-form-input-element">
                                            <input type="password" class="panel-form-input" minlength="8" maxlength="20" name="password" placeholder="Введите пароль" required>
                                        </li>
                                    </ul>

                                    <?php if(isset($_GET['message-error-employee'])): ?>
                                        <span class="message error"><?= $_GET['message-error-employee']?></span>
                                    <?php endif; ?>

                                    <button name="employeeRegistration" class="panel-button form-button title-medium" type="submit">Завершить</button>
                                </form>
                            </div>
                        </div>

                        <div class="panel-card admin-card d-none d-xl-flex" id="uploadFileDiv">
                            <div class="panel-card-heading">
                                <h3 class="title-medium darken">Создание позиции</h3>
                            </div>
                            <div class="panel-card-content">
                                <form action="../../../assets/php/admin/admin__position-upload" enctype="multipart/form-data" method="POST" class="panel-form">
                                    <ul class="panel-form-content">
                                        <li class="panel-form-input-element">
                                            <input type="text" class="panel-form-input" name="position_name" placeholder="Введите название позиции: " required>
                                        </li>
                                        <li class="panel-form-input-element">
                                            <input type="number" class="panel-form-input" min="1" max="9999" name="position_price" placeholder="Введите цену (руб.): " required>
                                        </li>
                                        <li class="panel-form-input-element">
                                            <div class="upload-file-wrapper">
                                                <input type="file" accept="image/jpeg, image/jpg, image/png"  class="panel-form-input upload-file-input" id="uploadFileInput" name="position_img" required>

                                                <label class="upload-file-label" for="uploadFileInput">
                                                    <span class="upload-file-label-text" data-labelIndex="1">Выберите файл</span>
                                                    <span class="upload-file-label-button">Выбрать</span>
                                                </label>
                                            </div>
                                        </li>
                                        <li class="panel-form-input-element">
                                            <textarea class="panel-form-input" name="position_description" required></textarea>
                                        </li>
                                    </ul>

                                    <?php if(isset($_GET['message-error'])): ?>
                                        <span class="message error"><?= $_GET['message-error']?></span>
                                    <?php elseif (isset($_GET['message-success'])): ?>
                                        <span class="message success"><?= $_GET['message-success']?></span>
                                    <?php endif; ?>
                                    
                                    <button name="createFoodPosition" class="panel-button form-button title-medium" type="submit">Завершить</button>
                                </form>
                            </div>
                        </div>

                        <div class="d-flex flex-column row-gap-4 admin-side-cards">
                            <div class="panel-card admin-card">
                                <div class="panel-card-heading">
                                    <h3 class="title-medium darken">Создание смены</h3>
                                </div>
                                <div class="panel-card-content">
                                    <form action="" method="POST" class="panel-form">
                                        <ul class="panel-form-content">
                                            <li class="panel-form-input-element">
                                                <input type="date" class="panel-form-input" min="<?php echo date('Y-m-d');?>" name="dateOfShift" placeholder="Выберите дату" required>
                                            </li>
                                        </ul>

                                        <?php if (isset($message['Error'])): ?>
                                            <span class="message error"><?= $message['Error']; ?></span>
                                        <?php endif; ?>
    
                                        <button name="сreateShift" class="panel-button form-button title-medium" type="submit">Завершить</button>
                                    </form>
                                </div>
                            </div>

                            <div class="panel-card admin-card" id="admin-pages">
                                <div class="panel-card-heading" id="admin-heading">
                                    <h3 class="title-medium darken">
                                        <a href="./admin_employees-page">Список сотрудников</a>
                                    </h3>
                                    <svg width="27" height="16" viewBox="0 0 27 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 7C0.447715 7 -4.82823e-08 7.44772 0 8C4.82823e-08 8.55228 0.447715 9 1 9L1 7ZM26.7071 8.7071C27.0976 8.31658 27.0976 7.68342 26.7071 7.29289L20.3431 0.92893C19.9526 0.538406 19.3195 0.538406 18.9289 0.928931C18.5384 1.31946 18.5384 1.95262 18.9289 2.34314L24.5858 8L18.9289 13.6569C18.5384 14.0474 18.5384 14.6805 18.9289 15.0711C19.3195 15.4616 19.9526 15.4616 20.3431 15.0711L26.7071 8.7071ZM1 9L26 9L26 7L1 7L1 9Z" fill="#4D4D4D"/>
                                    </svg>      
                                </div>
                            </div>

                            <div class="panel-card admin-card" id="admin-pages">
                                <div class="panel-card-heading" id="admin-heading">
                                    <h3 class="title-medium darken">
                                        <a href="./admin_shifts-page">Список смен</a>
                                    </h3>
                                    <svg width="27" height="16" viewBox="0 0 27 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 7C0.447715 7 -4.82823e-08 7.44772 0 8C4.82823e-08 8.55228 0.447715 9 1 9L1 7ZM26.7071 8.7071C27.0976 8.31658 27.0976 7.68342 26.7071 7.29289L20.3431 0.92893C19.9526 0.538406 19.3195 0.538406 18.9289 0.928931C18.5384 1.31946 18.5384 1.95262 18.9289 2.34314L24.5858 8L18.9289 13.6569C18.5384 14.0474 18.5384 14.6805 18.9289 15.0711C19.3195 15.4616 19.9526 15.4616 20.3431 15.0711L26.7071 8.7071ZM1 9L26 9L26 7L1 7L1 9Z" fill="#4D4D4D"/>
                                    </svg>      
                                </div>
                            </div>

                            <div class="panel-card admin-card" id="admin-pages">
                                <div class="panel-card-heading" id="admin-heading">
                                    <h3 class="title-medium darken">
                                        <a href="./admin_shift-reports-page">Отчет по сменам</a>
                                    </h3>
                                    <svg width="27" height="16" viewBox="0 0 27 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 7C0.447715 7 -4.82823e-08 7.44772 0 8C4.82823e-08 8.55228 0.447715 9 1 9L1 7ZM26.7071 8.7071C27.0976 8.31658 27.0976 7.68342 26.7071 7.29289L20.3431 0.92893C19.9526 0.538406 19.3195 0.538406 18.9289 0.928931C18.5384 1.31946 18.5384 1.95262 18.9289 2.34314L24.5858 8L18.9289 13.6569C18.5384 14.0474 18.5384 14.6805 18.9289 15.0711C19.3195 15.4616 19.9526 15.4616 20.3431 15.0711L26.7071 8.7071ZM1 9L26 9L26 7L1 7L1 9Z" fill="#4D4D4D"/>
                                    </svg>      
                                </div>
                            </div>

                            <div class="panel-card admin-card" id="admin-pages">
                                <div class="panel-card-heading" id="admin-heading">
                                    <h3 class="title-medium darken">
                                        <a href="./admin_online-orders-page">Онлайн заказы</a>
                                    </h3>
                                    <svg width="27" height="16" viewBox="0 0 27 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 7C0.447715 7 -4.82823e-08 7.44772 0 8C4.82823e-08 8.55228 0.447715 9 1 9L1 7ZM26.7071 8.7071C27.0976 8.31658 27.0976 7.68342 26.7071 7.29289L20.3431 0.92893C19.9526 0.538406 19.3195 0.538406 18.9289 0.928931C18.5384 1.31946 18.5384 1.95262 18.9289 2.34314L24.5858 8L18.9289 13.6569C18.5384 14.0474 18.5384 14.6805 18.9289 15.0711C19.3195 15.4616 19.9526 15.4616 20.3431 15.0711L26.7071 8.7071ZM1 9L26 9L26 7L1 7L1 9Z" fill="#4D4D4D"/>
                                    </svg>      
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-card admin-card d-flex d-xl-none" id="uploadFileDiv">
                        <div class="panel-card-heading">
                            <h3 class="title-medium darken">Создание позиции</h3>
                        </div>
                        <div class="panel-card-content">
                            <form action="../../../assets/php/admin/admin__position-upload" enctype="multipart/form-data" method="POST" class="panel-form">
                                <ul class="panel-form-content">
                                    <li class="panel-form-input-element">
                                        <input type="text" class="panel-form-input" name="position_name" placeholder="Введите название позиции: " required>
                                    </li>
                                    <li class="panel-form-input-element">
                                        <input type="number" class="panel-form-input" min="1" max="9999" name="position_price" placeholder="Введите цену (руб.): " required>
                                    </li>
                                    <li class="panel-form-input-element">
                                        <div class="upload-file-wrapper">
                                            <input type="file" accept="image/jpeg, image/jpg, image/png"  class="panel-form-input upload-file-input" id="uploadFileInput" name="position_img" required>

                                            <label class="upload-file-label" for="uploadFileInput">
                                                <span class="upload-file-label-text" data-labelIndex="2">Выберите файл</span>
                                                <span class="upload-file-label-button">Выбрать</span>
                                            </label>
                                        </div>
                                    </li>
                                    <li class="panel-form-input-element">
                                        <textarea class="panel-form-input" name="position_description" required></textarea>
                                    </li>
                                </ul>

                                <?php if(isset($_GET['message-error'])): ?>
                                    <span class="message error"><?= $_GET['message-error']?></span>
                                <?php elseif (isset($_GET['message-success'])): ?>
                                    <span class="message success"><?= $_GET['message-success']?></span>
                                <?php endif; ?>
                                
                                <button name="createFoodPosition" class="panel-button form-button title-medium" type="submit">Завершить</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <section class="cart mt-4">
                <h2 class="visually-hidden">Список позиций</h2>

                <div class="cart-body container">
                    <h3 class="panel-heading title-huge darken">Список позиций</h3>

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
                        <a href="./admin_panel" type="button" class="panel-button cart-table-button button mb-4">Очистить поиск</a>
                    <?php endif; ?>

                    <?php if (!empty($food_list)): ?>

                        <form action="../assets/php/user/user__order-create.php" method="POST" id="userCreateOrderForm">
                            <div class="cart-body-table-wrap">
                                    
                                <table class="cart-body-table table">
                                    <thead>
                                        <tr class="table-head">
                                            <th class="cart-body-table-heading title-medium">Изображение</th>
                                            <th class="cart-body-table-heading title-medium">Название</th>
                                            <th class="cart-body-table-heading title-medium">Цена</th>
                                            <th class="cart-body-table-heading title-medium w-25">Описание</th>
                                            <th class="cart-body-table-heading title-medium"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        
                                        <?php foreach ($food_list as $position_data): ?>
                                            <tr>
                                                <td class="w-25">
                                                    <img src="../../../assets/img/products/<?php echo htmlspecialchars($position_data['photo']); ?>" class="cart-image" alt="product image" loading="lazy">
                                                </td>
                                                <td>
                                                    <p class="card-body-table-text" id="productName"><?php echo htmlspecialchars($position_data['name']); ?></p>
                                                </td>
                                                <td>
                                                    <p class="card-body-table-text" id="productPrice"><?php echo htmlspecialchars($position_data['price']); ?> ₽</p>
                                                </td>
                                                <td>
                                                    <p class="card-body-table-text" id="productPrice"><?php echo htmlspecialchars($position_data['description']); ?></p>
                                                </td>
                                                <td>
                                                    <a href="./admin_position-edit?position_id=<?php echo $position_data['food_id']; ?>" class="white-color-text panel-button cart-table-button bg-lime-color">Редактировать</a>
                                                    <a href="../../../assets/php/admin/admin__position-delete?position_id=<?php echo $position_data['food_id']; ?>&photo_path=<?= $position_data['photo'] ?>" class="white-color-text panel-button cart-table-button">Удалить</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        
                                    </tbody>
                                </table>                
                            </div>
                        </form>

                    <?php else: ?>
                        <div class="d-flex justify-content-center mb-5">
                            <p class="title-medium darken">По вашему запросу ничего не найдено...</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <?php require_once('../../../assets/view/checkout/footer.inc'); ?>

        <script src="../../../assets/js/main.js"></script>

    </body>
</html>