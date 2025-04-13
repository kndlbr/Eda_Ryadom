<?php
    require_once('../../db/pdo_config.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $shift_id = $_POST['shift_id'];

        // Удаление старого заказа для обновления данных по нему (делается так, потому что UPDATE не может обновить данные по несуществующим строчкам, если количество позиций станет больше)
        $sql = "DELETE FROM `shift_user` WHERE shift_id = $shift_id";
        $stmt = $pdo -> prepare($sql);
        
        $stmt -> execute();
        
        // Получаем массивы с данными по позициям
        $employees_list = $_POST['employees'];

        // Подсчитываем количество сотрудников в массиве
        $j = count($employees_list);
        
        for ($i = 0; $i < $j; $i++) { 
            $employee_id = $employees_list[$i];

            $sql = "INSERT INTO `shift_user`(`shift_id`, `user_id`) VALUES ($shift_id, $employee_id)";
            $stmt = $pdo -> prepare($sql);
            
            $stmt -> execute();
        }

        // Если администратор открывает смену - изменяем ее статус
        if (isset($_POST['openShift'])) {
            $sql = "UPDATE `shifts` SET `status_id`='2' where shift_id = $shift_id";
            $stmt = $pdo -> prepare($sql);

            $stmt -> execute();
        }
    } else {

        $shift_id = $_GET['shift_id'];
        $action = $_GET['action'];

        // Удаление смены администратором. В ином случае смена закрывается
        if ($action == 'deleteShift'){
            $sql = "DELETE FROM `shifts` WHERE shift_id = $shift_id";
            $stmt = $pdo -> prepare($sql);
            
            $stmt -> execute();
        } elseif($action == 'closeShift'){
            $sql = "UPDATE `shifts` SET `status_id`='3' where shift_id = $shift_id";
            $stmt = $pdo -> prepare($sql);
            
            $stmt -> execute();
        }
    }

    Header('Location: ../../../checkout/staff-pages/admin/admin_shifts-page');
?>