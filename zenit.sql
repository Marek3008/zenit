-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: 127.0.0.1
-- Čas generovania: Po 07.Okt 2024, 11:26
-- Verzia serveru: 10.4.28-MariaDB
-- Verzia PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `zenitkk40`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `location`
--

CREATE TABLE `location` (
  `id` int(11) NOT NULL,
  `nazov` varchar(32) NOT NULL,
  `obrazok` varchar(255) NOT NULL,
  `cena` int(10) UNSIGNED DEFAULT NULL CHECK (`cena` between 0 and 999),
  `cas` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `location`
--

INSERT INTO `location` (`id`, `nazov`, `obrazok`, `cena`, `cas`) VALUES
(1, 'Štrbské Pleso', 'images/loc01.jpg', 150, '2024-10-02 09:06:24'),
(2, 'Tatranská Lomnica', 'images/loc02.jpg', 120, '2024-10-02 09:06:24'),
(3, 'Donovaly', 'images/loc03.jpg', 100, '2024-10-02 09:06:24'),
(4, 'Vrátna', 'images/loc04.jpg', 90, '2024-10-02 09:06:24'),
(5, 'Jasná', 'images/loc05.jpg', 200, '2024-10-02 09:06:24'),
(6, 'Zázrivá', 'images/loc06.jpg', 125, '2024-10-06 16:28:13');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL,
  `meno_priezvisko` varchar(64) NOT NULL,
  `mail` varchar(256) NOT NULL,
  `cas` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `newsletter`
--

INSERT INTO `newsletter` (`id`, `meno_priezvisko`, `mail`, `cas`) VALUES
(1, 'Ján Novák', 'jan.novak@example.com', '2024-10-02 09:03:18'),
(2, 'Mária Horváthová', 'maria.horvathova@example.com', '2024-10-02 09:03:18'),
(3, 'Matus Budos', 'matusbudos21@gmail.com', '2024-10-06 16:35:50'),
(4, 'Jozko Vajda', 'jozkovajda@gmail.com', '2024-10-06 16:37:03'),
(5, 'Michal Palica', 'jankomrkva@gmail.com', '2024-10-06 16:38:17');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `mail` varchar(256) NOT NULL,
  `telefon` varchar(32) NOT NULL,
  `meno_priezvisko` varchar(64) NOT NULL,
  `pocet_osob` int(5) NOT NULL,
  `typ_skipasu` enum('jednodňový','viacdňový','sezónny') NOT NULL,
  `pocet_dni` int(10) UNSIGNED NOT NULL,
  `termin` date NOT NULL,
  `celkova_suma` int(10) UNSIGNED NOT NULL,
  `cas` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `reservations`
--

INSERT INTO `reservations` (`id`, `location_id`, `mail`, `telefon`, `meno_priezvisko`, `pocet_osob`, `typ_skipasu`, `pocet_dni`, `termin`, `celkova_suma`, `cas`) VALUES
(1, 1, 'jan.novak@example.com', '+421901234567', 'Ján Novák', 0, 'jednodňový', 1, '2024-10-10', 150, '2024-10-02 09:22:24'),
(2, 2, 'petra.slovakova@example.com', '+421902345678', 'Petra Slováková', 0, 'viacdňový', 3, '2024-10-15', 360, '2024-10-02 09:22:24'),
(3, 5, 'matusbudos21@gmail.com', '0958 251 654', 'Matus Budos', 3, '', 1, '2024-10-16', 100, '2024-10-06 17:07:07'),
(4, 4, 'jozkovajda@gmail.com', '0251 958 124', 'Jozef Mrkva', 3, '', 15, '2024-10-17', 100, '2024-10-06 17:10:38'),
(5, 6, 'jankomrkva@gmail.com', '0524 125 982', 'Jozko Vajda', 5, '', 1, '2024-10-12', 100, '2024-10-06 17:11:21');

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pre tabuľku `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pre tabuľku `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_id` (`location_id`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `location`
--
ALTER TABLE `location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pre tabuľku `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pre tabuľku `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
