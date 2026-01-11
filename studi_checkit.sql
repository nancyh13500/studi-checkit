-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 05 mars 2025 à 14:49
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;

--
-- Base de données : `studi_checkit`
--

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

DROP TABLE IF EXISTS `category`;

CREATE TABLE IF NOT EXISTS `category` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `icon` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO
    `category` (`id`, `name`, `icon`)
VALUES (1, 'Travail', 'Travail'),
    (2, 'Voyage', 'Voyage');

-- --------------------------------------------------------

--
-- Structure de la table `item`
--

DROP TABLE IF EXISTS `item`;

CREATE TABLE IF NOT EXISTS `item` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `status` tinyint(1) NOT NULL,
    `list_id` int NOT NULL,
    PRIMARY KEY (`id`),
    KEY `list_id` (`list_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Déchargement des données de la table `item`
--

INSERT INTO
    `item` (
        `id`,
        `name`,
        `status`,
        `list_id`
    )
VALUES (
        1,
        'Préparer l\'itinéraire',
        0,
        1
    ),
    (2, 'Préparer le sac', 0, 1);

-- --------------------------------------------------------

--
-- Structure de la table `list`
--

DROP TABLE IF EXISTS `list`;

CREATE TABLE IF NOT EXISTS `list` (
    `id` int NOT NULL AUTO_INCREMENT,
    `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `user_id` int NOT NULL,
    `category_id` int NOT NULL,
    PRIMARY KEY (`id`),
    KEY `category_id` (`category_id`),
    KEY `user_id` (`user_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 2 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Déchargement des données de la table `list`
--

INSERT INTO
    `list` (
        `id`,
        `title`,
        `user_id`,
        `category_id`
    )
VALUES (1, 'Voyage en Italie', 1, 2);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;

CREATE TABLE IF NOT EXISTS `user` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nickname` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO
    `user` (
        `id`,
        `nickname`,
        `email`,
        `password`
    )
VALUES (
        1,
        'test',
        'test@test.com',
        '$2y$10$3qFqaoGOFAhr2ZtBIGpm.uI3GyXcReqlJ5VT3sI427xF7BoG935Yq'
    ),
    (
        2,
        'nancy',
        'nancy@nancy.com',
        '$2y$10$HjxLsKxFbXszmh7mx684tO/c8y2kyEqvRdvkwUB6a7wmCpC7KXaKO'
    );

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `item`
--
ALTER TABLE `item`
ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`list_id`) REFERENCES `list` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `list`
--
ALTER TABLE `list`
ADD CONSTRAINT `list_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
ADD CONSTRAINT `list_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;