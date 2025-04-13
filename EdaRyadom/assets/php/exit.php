<?php
    session_start();
    session_destroy();
    
    Header('Location: ../../home-page');
    exit;
?>