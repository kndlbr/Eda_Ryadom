window.addEventListener('DOMContentLoaded', () => {

    // Функция для изменения класса элемента в зависимости от страницы
    function headerActiveDesctop() {
        // Получаем список элементов навигации 
        const navList = document.querySelector('.nav__list');
        listItems = navList.querySelectorAll('li');

        const url = window.location.pathname;

        if (url.includes(`home-page`) || url === '/') {
            listItems[0].classList.add('active'); // Этот элемент соответствует странице
        } else if (url.includes(`catalog`)){
            listItems[1].classList.add('active');
        } 
        else {
            listItems[2].classList.add('active'); // Этот элемент не соответствует странице
        }
    };

    function footerActive() {
        // Получаем список элементов навигации 
        const navList = document.querySelector('#footerNav');
        listItems = navList.querySelectorAll('li');

        const url = window.location.pathname;

        if (url.includes(`home-page`) || url === '/') {
            listItems[0].classList.add('active'); // Этот элемент соответствует странице
        } else if (url.includes(`catalog`)){
            listItems[1].classList.add('active');
        } 
        else {
            listItems[2].classList.add('active'); // Этот элемент не соответствует странице
        }
    };

    if (window.innerWidth <= 767) {
        function headerActiveMobile() {
            // Получаем список элементов навигации 
            const navList = document.querySelector('.nav__list_mobile');
            listItems = navList.querySelectorAll('li');
    
            const url = window.location.pathname;
    
            if (url.includes(`home-page`) || url === '/') {
                listItems[0].classList.add('mobile');
                listItems[0].classList.add('active'); // Этот элемент соответствует странице
            } else if (url.includes(`catalog`)){
                listItems[1].classList.add('mobile');
                listItems[1].classList.add('active');
            } 
            else if (url.includes(`contacts`)){
                listItems[2].classList.add('mobile');
                listItems[2].classList.add('active'); // Этот элемент не соответствует странице
            }
        };
    }
    // Функция для изменения класса элемента в зависимости от страницы

    if (window.location.href.includes('home-page') || window.location.pathname === '/') {
        const mobile_modal_bg = document.querySelector(".modalBackground");
        const mobile_modal_header = document.querySelector(".mobile-modal");
        const menu_togler = document.querySelector(".mobile-nav-togler");
        const menu_togler_close = document.querySelector(".mobile-nav-togler_close");

        menu_togler.addEventListener("click", function(){
            mobile_modal_bg.classList.remove("hidden");
            mobile_modal_header.classList.remove("hidden");
        });

        menu_togler_close.addEventListener("click", function(){
            mobile_modal_bg.classList.add("hidden");
            mobile_modal_header.classList.add("hidden");
        });

        headerActiveDesctop();
        footerActive();
        if (window.innerWidth <= 767){
            headerActiveMobile();
        }
    };

    if (window.location.href.includes('contacts')) {
        const mobile_modal_bg = document.querySelector(".modalBackground");
        const mobile_modal_header = document.querySelector(".mobile-modal");
        const menu_togler = document.querySelector(".mobile-nav-togler");
        const menu_togler_close = document.querySelector(".mobile-nav-togler_close");

        menu_togler.addEventListener("click", function(){
            mobile_modal_bg.classList.remove("hidden");
            mobile_modal_header.classList.remove("hidden");
        });

        menu_togler_close.addEventListener("click", function(){
            mobile_modal_bg.classList.add("hidden");
            mobile_modal_header.classList.add("hidden");
        });

        headerActiveDesctop();
        footerActive();
        if (window.innerWidth <= 767){
            headerActiveMobile();
        }
    };

    if (window.location.href.includes('catalog')) {
        const mobile_modal_bg = document.querySelector(".modalBackground");
        const mobile_modal_header = document.querySelector(".mobile-modal");
        const menu_togler = document.querySelector(".mobile-nav-togler");
        const menu_togler_close = document.querySelector(".mobile-nav-togler_close");

        menu_togler.addEventListener("click", function(){
            mobile_modal_bg.classList.remove("hidden");
            mobile_modal_header.classList.remove("hidden");
        });

        menu_togler_close.addEventListener("click", function(){
            mobile_modal_bg.classList.add("hidden");
            mobile_modal_header.classList.add("hidden");
        });

        headerActiveDesctop();
        footerActive();

        if (window.innerWidth <= 767){
            headerActiveMobile();
        }
    };


    if (window.location.href.includes('profile')){

        if (document.querySelector('#eye-button') != null){

            // Ловим svg глаза
            const eyeButton = document.querySelector('#eye-button');
    
            // Пароль пользователя
            const userPassword = document.querySelector('#password-text');
            
            // Действия при клике
            eyeButton.addEventListener('click', function(){
    
                
                // Переключаем классы при нажатии на глаз
                userPassword.classList.toggle('visually-hidden');
            });
        }

        if (document.querySelectorAll('#productCount').length != 0 && document.querySelectorAll('#productPrice').length != 0){

            const inputs = document.querySelectorAll('#productCount');
            const prices = document.querySelectorAll('#productPrice');

            // Функция для вычисления общей суммы
            function calculateTotal() {
                let total = 0;
                i = 0 
    
                inputs.forEach(input => {
                    const price = parseInt(prices[i].textContent);;
                    const amount = input.value;

                    // Проверка на отрицательное значение или 0 в инпуте. Если значение < 1, то в итого напишется текст при else
                    if (amount < 1){
                        total = 'Отказ';
                    }
                    
                    total += price * amount; // суммируем
                    i++
                });

                if (total >= 0){
                    // Обновляем текст с общей суммой
                    document.querySelector('#totalSum').textContent = total + ' ₽';
                } else {
                    total = 'Укажите количество больше 0!'
                    document.querySelector('#totalSum').textContent = total;
                }
            }
    
            // Подключаем обработчики событий к каждому полю ввода
            inputs.forEach(input => {
                input.addEventListener('input', calculateTotal);
            });
    
            // Начальный расчет
            calculateTotal();
        } 

        const selfPickupBtn = document.querySelector('#selfPickup');
        const deliveryBtn = document.querySelector('#delivery');

        if (document.querySelectorAll('#productCount').length != 0){

            // Ловим список, который будем скрывать и его инпуты
            const deliveryInputsList = document.querySelector('#cartDeliveryList');
            let deliveryInputs = deliveryInputsList.querySelectorAll('input');
            
            // Скрываем список и меняем состояние инпутов в зависимости от выбора способа получения заказа

            // Если доставка -> проявляем список и инпуты
            deliveryBtn.addEventListener('click', function(){
                deliveryInputsList.classList.remove('d-none');
                deliveryInputs.forEach((item) => {
                    item.type = "text";
                });
            });

            // Если самовывоз -> скрываем список и инпуты
            selfPickupBtn.addEventListener('click', function(){
                deliveryInputsList.classList.add('d-none');
                deliveryInputs.forEach((item) => {
                    item.type = "hidden";
                });
            });
        }

        if (document.querySelectorAll('#uploadFileDiv').length != 0){
            
            // Определяем контейнеры, где находится инпуты с файлом
            const uploadFileDiv = document.querySelectorAll('#uploadFileDiv');
            
            // Перебираем каждый контейнер
            uploadFileDiv.forEach((div) => {
                 
                // Находим инпут внутри контейнера
                const uploadFileInput = document.querySelector('#uploadFileInput');

                // Проверяем отображен ли элемент на странице
                if (div.style.display !== 'none'){

                    // Ловим изменение загрузки файла
                    uploadFileInput.addEventListener('change', function(){
    
                        // Находим текст, который будем менять 
                        const labelText = div.querySelector('.upload-file-label-text');
                        let uploadFile = uploadFileInput.files[0]; // Загружаемый файл
    
                        // Если файл есть, то вставляем в инпут имя
                        if (uploadFile){
        
                            // Вычленяем имя
                            fileName = uploadFile.name;
            
                            // Вставляем имя в соответствующий инпуту элемент
                            labelText.textContent = `Файл: ${fileName}`;
                        } else {
        
                            // Если файла нету, то вставляем другой текст в соответствующий элемент
                            labelText.textContent = 'Ошибка загрузки!';
                        } 
                    });
                }
            }); 
        }
    }

    if (window.location.href.includes('admin_panel')){

        if (document.querySelectorAll('#uploadFileDiv').length != 0){
            
            // Определяем контейнеры, где находится инпуты с файлом
            const uploadFileDiv = document.querySelectorAll('#uploadFileDiv');
            
            // Перебираем каждый контейнер
            uploadFileDiv.forEach((div) => {
                 
                // Находим инпут внутри контейнера
                const uploadFileInput = document.querySelector('#uploadFileInput');

                // Проверяем отображен ли элемент на странице
                if (div.style.display !== 'none'){

                    // Ловим изменение загрузки файла
                    uploadFileInput.addEventListener('change', function(){
    
                        // Находим текст, который будем менять 
                        const labelText = div.querySelector('.upload-file-label-text');
                        let uploadFile = uploadFileInput.files[0]; // Загружаемый файл
    
                        // Если файл есть, то вставляем в инпут имя
                        if (uploadFile){
        
                            // Вычленяем имя
                            fileName = uploadFile.name;
            
                            // Вставляем имя в соответствующий инпуту элемент
                            labelText.textContent = `Файл: ${fileName}`;
                        } else {
        
                            // Если файла нету, то вставляем другой текст в соответствующий элемент
                            labelText.textContent = 'Ошибка загрузки!';
                        } 
                    });
                }
            }); 
        }
    }

    if (window.location.href.includes('admin_position-edit')){

        if (document.querySelectorAll('#uploadFileDiv').length != 0){
            
            // Определяем контейнеры, где находится инпуты с файлом
            const uploadFileDiv = document.querySelectorAll('#uploadFileDiv');
            
            // Перебираем каждый контейнер
            uploadFileDiv.forEach((div) => {
                 
                // Находим инпут внутри контейнера
                const uploadFileInput = document.querySelector('#uploadFileInput');

                // Проверяем отображен ли элемент на странице
                if (div.style.display !== 'none'){

                    // Ловим изменение загрузки файла
                    uploadFileInput.addEventListener('change', function(){
    
                        // Находим текст, который будем менять 
                        const labelText = div.querySelector('.upload-file-label-text');
                        let uploadFile = uploadFileInput.files[0]; // Загружаемый файл
    
                        // Если файл есть, то вставляем в инпут имя
                        if (uploadFile){
        
                            // Вычленяем имя
                            fileName = uploadFile.name;
            
                            // Вставляем имя в соответствующий инпуту элемент
                            labelText.textContent = `Файл: ${fileName}`;
                        } else {
        
                            // Если файла нету, то вставляем другой текст в соответствующий элемент
                            labelText.textContent = 'Ошибка загрузки!';
                        } 
                    });
                }
            }); 
        }
    }
});