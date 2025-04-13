<header class="header">
    <div class="header-panel container d-flex justify-content-between align-items-center">
        <div class="logo d-flex align-items-center">
            <img src="../../../assets/img/logo.png" alt="logo image" class="logo__img" draggable="false">
            <div class="logo__text d-flex flex-column">
                <span class="logo__text_green">Еда</span> Рядом
            </div>
        </div>

        <nav class="header-panel-nav">
            <a href="../orders_history" class="title-medium lime-color-text">История</a>
        </nav>
        
        <div class="header-panel-profile-stats">
            <div class="header-panel-profile-stats">
                <span class="title-medium darken d-none d-md-flex"><?php echo htmlspecialchars($_SESSION['fname']); ?></span>
                <a href="../../profile">
                    <?php if (isset($user_photo)): ?>
                        <img src="../../../assets/img/users/<?= $user_photo; ?>" alt="profile image" class="header-profile-image panel-image" loading="lazy">
                    <?php else: ?>
                        <img src="../../../assets/img/thumbnail.jpg" alt="profile image" class="header-profile-image panel-image" loading="lazy">
                    <?php endif; ?>   
                </a>
            </div>
            <div class="header-panel-logout d-none d-lg-flex">
                <form method="POST" action="../../../assets/php/exit.php">
                    <button type="submit" class="title-medium darken">Выйти</button>
                </form>
            </div>
        </div>
    </div>
</header>