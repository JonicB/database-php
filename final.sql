-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Июн 22 2021 г., 08:54
-- Версия сервера: 10.5.8-MariaDB
-- Версия PHP: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `final`
--

-- --------------------------------------------------------

--
-- Структура таблицы `catalog_item`
--

CREATE TABLE `catalog_item` (
  `item_id` int(11) NOT NULL,
  `itemname` varchar(50) NOT NULL,
  `description` varchar(500) NOT NULL,
  `price` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `catalog_item`
--

INSERT INTO `catalog_item` (`item_id`, `itemname`, `description`, `price`) VALUES
(1, 'Lipstick', 'Classic corall', 56),
(2, 'Flower', 'Red, blue or yellow flower without thorns.', 50),
(3, 'HourOfSleep', 'The best gift for students. Especially before the session', 1000),
(4, 'Tea', 'Black tea with orange flavor will give you cheerfulness and a good mood for the whole day', 23.4),
(5, 'Phone', 'Nokia. Unbreakable', 40.9),
(6, 'Pillow', 'Very soft and big. For sweet dreams', 10),
(7, 'Book', 'Harry Potter and the goblet of fire', 7.7),
(8, 'Сoffee', 'Drip bags with different flavors', 40),
(10, 'Pencils', 'Watercolour pencils. 12 colors', 100),
(14, 'Succulent', 'Small, 10 cm in height', 76),
(15, 'Anti-stress', 'You can crumple it in your hands and relax', 15),
(16, 'Cola', 'Your favorite drink', 12),
(17, 'Lamp', 'A beautiful lamp. It shines brightly and can change colors', 156),
(18, 'Something', 'Surprise', 53),
(19, 'Rubik\'s Cube', 'The most famous puzzle game in the world', 20);

-- --------------------------------------------------------

--
-- Структура таблицы `client`
--

CREATE TABLE `client` (
  `ID` int(11) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `address` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `client`
--

INSERT INTO `client` (`ID`, `lastname`, `firstname`, `birthdate`, `address`) VALUES
(1, 'Pupkin', 'Petya', '2000-11-22', 'Ulitsa'),
(2, 'Horoshiy', 'Chelovek', '2000-01-01', 'Moscow'),
(3, 'OfMyLife', 'Dream', '2001-06-26', 'BestPlace'),
(4, 'Ivanov', 'Ivan', '2001-10-02', 'Moscow'),
(5, 'Petrov', 'Petr', '2001-02-10', 'Moscow'),
(6, 'Userov1', 'User1', '1999-09-09', 'Gorod'),
(7, 'Userov2', 'User2', '1999-09-09', 'Gorod'),
(11, 'Abra', 'Cadabra', '1010-10-10', 'gde-to'),
(12, 'Bychkova', 'Jonic', '2001-02-10', 'Tam'),
(15, 'Kto', 'To', '1999-01-01', NULL),
(16, 'Cat', 'Barsik', '2015-05-05', 'gde-to'),
(18, 'Azra', 'Fell', '1000-01-01', 'London'),
(19, 'Super', 'Duper', '2002-05-04', NULL),
(20, 'Maya', 'Kulina', '2002-06-05', 'Tvery'),
(21, 'Special', 'Picachu', '1998-08-07', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `order`
--

CREATE TABLE `order` (
  `oid` int(11) NOT NULL,
  `order_num` varchar(10) NOT NULL,
  `order_date` date NOT NULL,
  `client_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `order`
--

INSERT INTO `order` (`oid`, `order_num`, `order_date`, `client_id`) VALUES
(1, '867', '2021-06-02', 2),
(2, '1008', '2019-08-10', 1),
(3, '444', '2020-01-01', 1),
(4, '4', '2021-05-09', 1),
(5, '66666', '2021-06-07', 1),
(6, '565', '2021-06-01', 2),
(7, '155', '2010-08-07', 2),
(11, '88', '2021-05-05', 7),
(12, '789', '2021-06-01', 5),
(13, '356', '2021-06-06', 4),
(14, '752', '2021-06-01', 6),
(15, '752', '2021-06-03', 5),
(16, '741', '2021-06-04', 4),
(17, '987', '2000-04-05', 2),
(24, '888', '2019-01-01', 5),
(25, '757575', '2020-10-08', 4),
(26, '4545454', '2021-03-12', 7),
(27, '89', '2020-12-12', 3),
(28, '6565', '2001-01-01', 4);

-- --------------------------------------------------------

--
-- Структура таблицы `order_item_rel`
--

CREATE TABLE `order_item_rel` (
  `oid` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `order_item_rel`
--

INSERT INTO `order_item_rel` (`oid`, `item_id`, `quantity`) VALUES
(1, 3, 5),
(2, 1, 1),
(2, 2, 3),
(3, 4, 2),
(3, 3, 4),
(4, 2, 117),
(5, 4, 5),
(5, 1, 5),
(6, 2, 4),
(6, 3, 1),
(1, 2, 3),
(11, 8, 14),
(11, 4, 14),
(12, 5, 1),
(13, 6, 2),
(13, 7, 7),
(14, 8, 3),
(15, 1, 1),
(15, 6, 1),
(16, 5, 3),
(16, 1, 2),
(7, 8, 1),
(1, 1, 1),
(1, 1, 1),
(3, 5, 3),
(3, 10, 7),
(3, 10, 7),
(3, 6, 1),
(15, 3, 2),
(15, 8, 7),
(17, 3, 5),
(24, 3, 7),
(26, 17, 5),
(26, 18, 1),
(25, 14, 5);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `catalog_item`
--
ALTER TABLE `catalog_item`
  ADD PRIMARY KEY (`item_id`);

--
-- Индексы таблицы `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`ID`);

--
-- Индексы таблицы `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`oid`),
  ADD KEY `client_id` (`client_id`);

--
-- Индексы таблицы `order_item_rel`
--
ALTER TABLE `order_item_rel`
  ADD KEY `oid` (`oid`),
  ADD KEY `item_id` (`item_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `catalog_item`
--
ALTER TABLE `catalog_item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `client`
--
ALTER TABLE `client`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT для таблицы `order`
--
ALTER TABLE `order`
  MODIFY `oid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`ID`);

--
-- Ограничения внешнего ключа таблицы `order_item_rel`
--
ALTER TABLE `order_item_rel`
  ADD CONSTRAINT `order_item_rel_ibfk_1` FOREIGN KEY (`oid`) REFERENCES `order` (`oid`),
  ADD CONSTRAINT `order_item_rel_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `catalog_item` (`item_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
