-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 01 Wrz 2017, 10:03
-- Wersja serwera: 10.1.21-MariaDB
-- Wersja PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `gallery`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `comment`
--

CREATE TABLE `comment` (
  `idComment` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `comment` varchar(158) NOT NULL,
  `dateAdded` date NOT NULL,
  `idPhoto` int NOT NULL,
  `idUser` int NOT NULL,
  `username` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `comment`
--

INSERT INTO `comment` (`idComment`, `comment`, `dateAdded`, `idPhoto`, `idUser`, `username`) VALUES
(3, 'jakis komentarz', '2017-08-16', 53, 4, NULL),
(4, 'apple green tin', '2017-08-16', 53, 5, NULL),
(5, 'O ludzie!', '2017-08-19', 53, 4, NULL),
(6, 'O ludzie!', '2017-08-19', 53, 4, NULL),
(9, 'fajne zdj', '2017-08-19', 55, 4, NULL),
(10, 'srina <3', '2017-08-30', 59, 4, NULL),
(12, 'aaaa', '2017-08-30', 57, 0, 'Admin'),
(14, 'hahah XD', '2017-08-30', 57, 0, 'jaknak23'),
(15, 'klocki XD', '2017-08-30', 62, 0, 'jaknak23'),
(16, 'bin', '2017-08-30', 53, 0, 'jaknak23'),
(17, 'jaka fajna stronka, podziwiam :))', '2017-08-30', 57, 0, 'jaknak23');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `photo`
--

CREATE TABLE `photo` (
  `idPhoto` int(3) NOT NULL,
  `path` varchar(158) NOT NULL,
  `description` varchar(158) NOT NULL,
  `is_public` varchar(2) DEFAULT NULL,
  `date_added` date DEFAULT NULL,
  `date_edited` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `photo`
--

INSERT INTO `photo` (`idPhoto`, `path`, `description`, `is_public`, `date_added`, `date_edited`) VALUES
(47, '6b569a84885df1d8227863ffd236a4f4.jpeg', 'hhh', '0', '2017-08-02', '2017-08-02'),
(48, '7da45d35e54d1472a83c9e4261c866a3.jpeg', 'ffff', '0', '2017-08-03', '2017-08-03'),
(49, 'b81d5deb94c960bbe71da61a1c9127f9.jpeg', 'xxx', '1', '2017-08-03', '2017-08-04'),
(50, 'c736afa8cd819265c77c51d82236eb64.jpeg', 'addf', '1', '2017-08-04', '2017-08-04'),
(51, '671fa2a36202c9bcb60be0355041b63c.jpeg', 'fagghs', '1', '2017-08-04', '2017-08-04'),
(52, '643e20c4c6111a73bd56c550e1ffeed3.jpeg', 'klaks', '1', '2017-08-06', '2017-08-06'),
(53, '6ec559720f6fe2b6196df75a1e9d133c.jpeg', 'dff', '1', '2017-08-06', '2017-08-06'),
(55, 'c437c738d18ef4b45586aec32c508b6d.jpeg', 'new tag', '0', '2017-08-08', '2017-08-13'),
(56, '8118f63c23bc5566568cf50f7d920c66.png', 'kskjd', '1', '2017-08-28', '2017-08-28'),
(57, '6af479b09ec6a44e2f18d8a2d1d2e8f8.png', 'kjjh', '1', '2017-08-28', '2017-08-28'),
(59, '08d2715a0f512d539c63e6dfd30a3d96.jpeg', 'iron', '0', '2017-08-30', '2017-08-30'),
(61, 'ddbcb238e9aef3e2336af0c31a862285.jpeg', 'jak', '1', '2017-08-30', '2017-08-30'),
(62, '4e6ac344b79bfa51c2af0a3905590e28.png', 'sssss', '1', '2017-08-30', '2017-08-30');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `photo_tag`
--

CREATE TABLE `photo_tag` (
  `tagId` int(3) NOT NULL,
  `photoId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `photo_tag`
--

INSERT INTO `photo_tag` (`tagId`, `photoId`) VALUES
(1, 11),
(1, 12),
(31, 46),
(37, 51),
(40, 52),
(51, 55),
(61, 55),
(62, 55),
(63, 55),
(64, 55),
(65, 55),
(66, 55),
(67, 55),
(68, 55),
(69, 55),
(70, 55),
(71, 55),
(72, 55),
(73, 55),
(74, 55),
(75, 55),
(76, 55),
(77, 55),
(78, 55),
(79, 55),
(80, 52),
(81, 52),
(88, 53),
(90, 56),
(92, 57),
(94, 59),
(96, 61),
(97, 62);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `photo_user`
--

CREATE TABLE `photo_user` (
  `photoId` int(10) UNSIGNED NOT NULL,
  `userId` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `photo_user`
--

INSERT INTO `photo_user` (`photoId`, `userId`) VALUES
(57, 19),
(58, 17),
(59, 17),
(60, 17),
(61, 19),
(62, 19);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rating`
--

CREATE TABLE `rating` (
  `ratingId` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `total_rating` int(11) NOT NULL,
  `photoId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `rating`
--

INSERT INTO `rating` (`ratingId`, `number`, `total_rating`, `photoId`) VALUES
(1, 2, 2, 57),
(2, 2, 2, 57),
(3, 2, 2, 57),
(4, 4, 4, 57),
(5, 5, 5, 59);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'ROLE_ADMIN'),
(2, 'ROLE_USER');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `tag`
--

CREATE TABLE `tag` (
  `tagId` int(3) NOT NULL,
  `name` varchar(56) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `tag`
--

INSERT INTO `tag` (`tagId`, `name`) VALUES
(1, 'deszcz'),
(2, 'slonce'),
(3, 'smile'),
(4, 'noc'),
(5, 'wieczor'),
(6, 'wino'),
(7, 'wiatrak'),
(8, 'kwaity'),
(9, 'lalka'),
(10, 'król'),
(11, 'jablko'),
(12, 'ogon'),
(13, 'gun'),
(14, 'arm'),
(15, 'army'),
(16, 'as'),
(17, 'ass'),
(18, 'ddd'),
(19, 'aaa'),
(20, 'motyl2'),
(21, 'sss'),
(29, 'tag8'),
(31, 'hjhh'),
(32, 'jjj'),
(33, 'rare'),
(34, ' noz'),
(35, ' nic'),
(36, 'gargamel'),
(37, ' ogorek'),
(38, ' lilia'),
(40, ' nowego'),
(41, ' haja'),
(43, 'Array'),
(44, 'Array'),
(45, 'Array'),
(46, 'Array'),
(47, 'Array'),
(48, 'XDD'),
(49, 'Array'),
(50, 'krolik'),
(51, 'ajax'),
(52, ' js'),
(53, ' db'),
(54, 'db2'),
(55, 'db2'),
(56, 'db2'),
(57, 'db2'),
(58, 'kropki'),
(60, 'iron man'),
(82, 'baran'),
(84, 'jez'),
(88, 'kulfon'),
(89, 'auto'),
(90, 'kjsj'),
(92, 'win'),
(94, 'srina'),
(96, 'samolot2'),
(97, 'klocki');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(2) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(128) DEFAULT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `email`, `login`, `password`, `role_id`) VALUES
(4, 'Ignacy', 'Nowakowski', 'nowak@gmail.com', 'Nowak', 'Nowak', 0),
(5, 'Kasia', 'Kowal', 'KASIA@ONET.PL', 'kaska', 'kaska', 0),
(6, 'Jan', 'Nowak', 'jnowak@wp.pl', 'janNowak', 'start', 0),
(7, 'ssas', 'ddss', 'dssd@wp.pl', 'jolka', 'jolka', 0),
(8, 'ssas', 'ddss', 'dssd@wp.pl', 'jolka', 'jolka', 0),
(9, 'ssas', 'ddss', 'dssd@wp.pl', 'jolka', 'jolka', 0),
(10, 'ssas', 'ddss', 'dssd@wp.pl', 'jolka', 'jolka', 0),
(12, 'ssas', 'ddss', 'dssd@wp.pl', 'jolka', 'jolka', 0),
(15, 'hjjj', 'hhggg', 'jankow@onet.pl', 'gfff', '$2y$13$prTGDv6rvMZY29Z3rZQvueMcYRbwcHb9wRgf.sfKhmZP3V5FtD3xi', 0),
(16, 'Kazimierz', 'Nowak', 'kazik@wp.pl', 'kazik', '$2y$13$0f8BIECXNdOPtjt.7zVeQejz7H6DHkRuXGl3Wqes2PAY8XztmhYgu', 2),
(17, 'Adam', 'Nowak', 'anowak@wp.pl', 'Admin', '$2y$13$0TNeJ9QEw3dc24DPYrPwmOjxbi8pmG8pPdeB1DohrPrz5mIE5oO36', 1),
(18, 'anna', 'kowal', 'anka@wp.pl', 'anka', '$2y$13$/J6T6.BN5Qb5lE6SKrn3luvnfdx/Nw1YFfwRCdBWjvVKERFIX8D96', 2),
(19, 'Janina', 'Kowal', 'janka@exampl.ecom', 'jaknak23', '$2y$13$iD5EbuPrLVsH3OlV59MPVOXcS0oSc2nNwsFnkMYsquQGrbawB9Vce', 2);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`idComment`);

--
-- Indexes for table `photo`
--
ALTER TABLE `photo`
  ADD PRIMARY KEY (`idPhoto`);

--
-- Indexes for table `rating`
--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`ratingId`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`tagId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `comment`
--
ALTER TABLE `comment`
  MODIFY `idComment` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT dla tabeli `photo`
--
ALTER TABLE `photo`
  MODIFY `idPhoto` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
--
-- AUTO_INCREMENT dla tabeli `rating`
--
ALTER TABLE `rating`
  MODIFY `ratingId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT dla tabeli `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT dla tabeli `tag`
--
ALTER TABLE `tag`
  MODIFY `tagId` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;
--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
