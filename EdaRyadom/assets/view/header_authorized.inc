<header class="header">
    <div class="modalBackground hidden">
        <div class="mobile-modal hidden">
            <div class="container d-flex justify-content-between align-items-center">
                <nav class="mobile-modal__nav">
                    <ul class="nav__list_mobile d-flex flex-column align-items-start">
                        <li class="nav__list-item">
                            <a class="nav__list-link" href="./home-page">Главная</a>
                        </li>
                        <li class="nav__list-item">
                            <a class="nav__list-link" href="./catalog">Каталог</a>
                        </li>
                        <li class="nav__list-item">
                            <a class="nav__list-link" href="./contacts">Контакты</a>
                        </li>
                        <li class="nav__list-item">
                            <a class="nav__list-link" href="./checkout/profile">Профиль</a>
                        </li>
                    </ul>
                </nav>
                <div class="mobile-nav-togler_close" title="Закрыть меню навигации">
                    <span class="visually-hidden">Закрыть меню навигации</span>
                    <svg class="mobile-nav-togler__icon" fill="none" xmlns="https://www.w.org/2000/svg" width="22" height="22" viewBox="0 0 22 22">
                        <path d="M1 1H21" stroke-width="2" stroke-linecap="round"/>
                        <path d="M1 21H21" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <div class="header-container container d-flex justify-content-between align-items-center">
        <div class="logo d-flex align-items-center">
            <img src="./assets/img/logo.png" alt="logo image" class="logo__img" draggable="false">
            <div class="logo__text d-flex flex-column">
                <span class="logo__text_green">Еда</span> Рядом
            </div>
        </div>
        <nav class="nav d-none d-md-flex">  
            <ul class="nav__list d-flex align-items-center">
                <li class="nav__list-item">
                    <a class="nav__list-link" href="./home-page">Главная</a>
                </li>
                <li class="nav__list-item">
                    <a class="nav__list-link" href="./catalog">Каталог</a>
                </li>
                <li class="nav__list-item">
                    <a class="nav__list-link" href="./contacts">Контакты</a>
                </li>
                <li class="nav__list-item">
                    <a class="nav__list-link" href="./checkout/profile">Профиль</a>
                </li>
            </ul>
        </nav>
        <div class="mobile-nav-togler d-flex d-md-none" title="Открыть меню навигации">
            <span class="visually-hidden">Открыть меню навиации</span>
            <svg class="mobile-nav-togler__icon" fill="none" xmlns="https://www.w.org/2000/svg" width="22" height="22" viewBox="0 0 22 22">
                <path d="M1 1H21" stroke-width="2" stroke-linecap="round"/>
                <path d="M1 11H21" stroke-width="2" stroke-linecap="round"/>
                <path d="M1 21H21" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
    </div>
</header>