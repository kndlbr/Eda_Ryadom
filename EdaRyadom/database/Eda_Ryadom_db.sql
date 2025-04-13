-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 13 2025 г., 09:19
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `aa`
--

-- --------------------------------------------------------

--
-- Структура таблицы `food`
--

CREATE TABLE `food` (
  `food_id` int NOT NULL,
  `name` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(4,0) NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `food`
--

INSERT INTO `food` (`food_id`, `name`, `price`, `description`, `photo`) VALUES
(1, 'Окрошка', '249', 'Квас, картофель, огурцы, редиска, куриные яйца, вареная колбаса, укроп, петрушка, зеленый лук ', 'position_01-04-2025_12-30-59.jpg'),
(2, 'Борщ', '249', 'Свекла, капуста, морковь, лук, перец, укроп, петрушка, чеснок, томатная паста, ложка сметаны ', 'position_01-04-2025_12-52-20.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `food_order`
--

CREATE TABLE `food_order` (
  `order_id` int NOT NULL,
  `food_id` int NOT NULL,
  `count` decimal(2,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `date` datetime NOT NULL,
  `status_id` int NOT NULL,
  `shift_id` int NOT NULL,
  `client_id` int DEFAULT NULL,
  `waiter_id` int DEFAULT NULL,
  `cook_id` int DEFAULT NULL,
  `adress_delivery` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `order_status`
--

CREATE TABLE `order_status` (
  `status_id` int NOT NULL,
  `status_name` varchar(20) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `order_status`
--

INSERT INTO `order_status` (`status_id`, `status_name`) VALUES
(1, 'Принят'),
(2, 'Готовится'),
(3, 'Готов'),
(4, 'Оплачен'),
(5, 'Отменен'),
(6, 'На рассмотрении');

-- --------------------------------------------------------

--
-- Структура таблицы `role`
--

CREATE TABLE `role` (
  `role_id` int NOT NULL,
  `role_name` varchar(13) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `role`
--

INSERT INTO `role` (`role_id`, `role_name`) VALUES
(1, 'Официант'),
(2, 'Повар'),
(3, 'Администратор'),
(4, 'Пользователь');

-- --------------------------------------------------------

--
-- Структура таблицы `shifts`
--

CREATE TABLE `shifts` (
  `shift_id` int NOT NULL,
  `date` date NOT NULL,
  `status_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `shift_status`
--

CREATE TABLE `shift_status` (
  `status_id` int NOT NULL,
  `status_name` varchar(7) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `shift_status`
--

INSERT INTO `shift_status` (`status_id`, `status_name`) VALUES
(1, 'Создана'),
(2, 'Открыта'),
(3, 'Закрыта');

-- --------------------------------------------------------

--
-- Структура таблицы `shift_user`
--

CREATE TABLE `shift_user` (
  `shift_id` int NOT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  `login` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `fname` varchar(35) COLLATE utf8mb4_general_ci NOT NULL,
  `lname` varchar(35) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `photo` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `login`, `password`, `fname`, `lname`, `phone`, `photo`) VALUES
(1, 3, 'root', '$2y$10$3vX..BrYEklLJJWomwr/ie7ethP7E2VqBNowgkuzGrAcyZva.cDoe', 'Админ', 'офываолд', NULL, 'root/root_profile-photo_1743487231.jpg'),
(2, 1, 'waiter', '$2y$10$I6/7/KRukor6G4wRR/1v8OGVC0kr7/p/8OjRV7XKmYXPRBAgoD/IW', 'Официант', 'Фамилия', NULL, NULL),
(3, 2, 'cook', '$2y$10$usJT0ol13hGlm5QaiRBpTexU5gLGDnDld.55DQy8TaPH9hLxlORGK', 'Повар', 'Фамилия', NULL, NULL),
(4, 4, 'user', '$2y$10$3SjRopKhVKGJrWuyJZUnzuNp3Y2Etji6YHVK3Vvwf2sxg2kE.XKJ2', 'Клиент', NULL, '+79999999999', NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `food`
--
ALTER TABLE `food`
  ADD PRIMARY KEY (`food_id`);

--
-- Индексы таблицы `food_order`
--
ALTER TABLE `food_order`
  ADD KEY `order_id` (`order_id`,`food_id`),
  ADD KEY `food_id` (`food_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Индексы таблицы `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`status_id`);

--
-- Индексы таблицы `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Индексы таблицы `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`shift_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Индексы таблицы `shift_status`
--
ALTER TABLE `shift_status`
  ADD PRIMARY KEY (`status_id`);

--
-- Индексы таблицы `shift_user`
--
ALTER TABLE `shift_user`
  ADD KEY `shift` (`shift_id`,`user_id`),
  ADD KEY `shift_id` (`shift_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `food`
--
ALTER TABLE `food`
  MODIFY `food_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `shifts`
--
ALTER TABLE `shifts`
  MODIFY `shift_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `food_order`
--
ALTER TABLE `food_order`
  ADD CONSTRAINT `food_order_ibfk_1` FOREIGN KEY (`food_id`) REFERENCES `food` (`food_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `food_order_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `order_status` (`status_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`shift_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `shifts`
--
ALTER TABLE `shifts`
  ADD CONSTRAINT `shifts_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `shift_status` (`status_id`);

--
-- Ограничения внешнего ключа таблицы `shift_user`
--
ALTER TABLE `shift_user`
  ADD CONSTRAINT `shift_user_ibfk_1` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`shift_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `shift_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
