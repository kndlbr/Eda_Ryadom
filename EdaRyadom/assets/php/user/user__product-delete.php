<?php
    require_once('../../db/pdo_config.php');
    session_start();

    // Параметры для удаления товаров из корзины
    $cart = $_SESSION['cart'];
    $delete_product_id = $_GET['product_id'];
    
    $delete_product_key = array_search($delete_product_id, $cart);
    unset($cart[$delete_product_key]); // Удаление товара по его id 
    
    $cart = array_values($cart); // ненужное (возобновляет порядок ключей в массиве)
    $_SESSION['cart'] = $cart; // Перезапись корзины 

    $sorted_cart = array_count_values($_SESSION['cart']);
    KSort($sorted_cart);
    
    Header('Location: ../../../checkout/profile');
?>