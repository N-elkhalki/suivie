-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : lun. 02 juin 2025 à 09:32
-- Version du serveur : 10.11.11-MariaDB-deb12
-- Version de PHP : 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `osour2189855`
--

-- --------------------------------------------------------

--
-- Structure de la table `additional`
--

CREATE TABLE `additional` (
  `Id` int(11) NOT NULL,
  `Associer` varchar(255) DEFAULT NULL,
  `Personne_RS` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bureau`
--

CREATE TABLE `bureau` (
  `Id_Bureau` int(11) NOT NULL,
  `Name_bureau` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `date_entries`
--

CREATE TABLE `date_entries` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demande`
--

CREATE TABLE `demande` (
  `Demande_id` int(11) NOT NULL,
  `Nom` varchar(100) DEFAULT NULL,
  `Type` varchar(100) DEFAULT NULL,
  `Nature` varchar(100) DEFAULT NULL,
  `today_date` date DEFAULT NULL,
  `Fonction` varchar(100) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `Jours` int(11) DEFAULT NULL,
  `Reprise` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demandes`
--

CREATE TABLE `demandes` (
  `id` int(11) NOT NULL,
  `date_entrevue` date DEFAULT NULL,
  `date_confirmation_embauche` date DEFAULT NULL,
  `contrat` date DEFAULT NULL,
  `demande_acces` date DEFAULT NULL,
  `reception_acces` date DEFAULT NULL,
  `preparation_materiel` date DEFAULT NULL,
  `date_debut_travail` date DEFAULT NULL,
  `etat_avancement` varchar(255) DEFAULT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `folders`
--

CREATE TABLE `folders` (
  `Id_folder` int(11) NOT NULL,
  `Name_folder` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `forms`
--

CREATE TABLE `forms` (
  `id` int(11) NOT NULL,
  `Bureau` varchar(100) DEFAULT NULL,
  `Nom_doss` varchar(100) DEFAULT NULL,
  `Associer` varchar(255) DEFAULT NULL,
  `Personne_RS` varchar(255) DEFAULT NULL,
  `Num_dossier` int(11) DEFAULT NULL,
  `Projet` varchar(255) DEFAULT NULL,
  `Fin_annee` date DEFAULT NULL,
  `Date_recep` date DEFAULT NULL,
  `Priorite` varchar(255) DEFAULT NULL,
  `Budget_heure` int(11) DEFAULT NULL,
  `Preparateur` varchar(255) DEFAULT NULL,
  `Statut` varchar(100) DEFAULT NULL,
  `Date_liv` date DEFAULT NULL,
  `Revision` varchar(255) DEFAULT NULL,
  `Date_rev` date DEFAULT NULL,
  `Type_dossier` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `formulaire`
--

CREATE TABLE `formulaire` (
  `Id_formulaire` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Work_hours` float NOT NULL,
  `Commentaires` varchar(500) NOT NULL,
  `Office` varchar(100) DEFAULT NULL,
  `Folder` varchar(50) DEFAULT NULL,
  `Infos` varchar(200) DEFAULT NULL,
  `Tasks` varchar(50) DEFAULT NULL,
  `Name` varchar(30) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `todays_date` date DEFAULT NULL,
  `Statut` varchar(50) NOT NULL,
  `Livred` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `impo`
--

CREATE TABLE `impo` (
  `id` int(11) NOT NULL,
  `Numero` int(11) NOT NULL,
  `Date` date DEFAULT NULL,
  `Nom` varchar(255) DEFAULT NULL,
  `Superviseur` varchar(255) DEFAULT NULL,
  `Preparateur` varchar(255) DEFAULT NULL,
  `Nr_page` int(11) DEFAULT NULL,
  `Statut` varchar(255) DEFAULT NULL,
  `Date_liv` date NOT NULL,
  `Revision` varchar(255) DEFAULT NULL,
  `Date_rev` date NOT NULL,
  `commentaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `informations`
--

CREATE TABLE `informations` (
  `Id_info` int(11) NOT NULL,
  `Infos` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `request_periode`
--

CREATE TABLE `request_periode` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_type` enum('conge','absence') NOT NULL,
  `leave_type` enum('Annuel','Exceptionnel') NOT NULL,
  `year` year(4) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `return_date` date NOT NULL,
  `leave_days` int(11) NOT NULL,
  `digital_signature` varchar(255) NOT NULL,
  `request_date` datetime DEFAULT current_timestamp(),
  `comments` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `previous_year_credit` varchar(50) DEFAULT NULL,
  `current_year_credit` varchar(50) DEFAULT NULL,
  `used_credit` varchar(50) DEFAULT NULL,
  `remaining_credit` varchar(50) DEFAULT NULL,
  `annual_leave` varchar(50) DEFAULT NULL,
  `exceptional_leave` varchar(50) DEFAULT NULL,
  `balance` varchar(50) DEFAULT NULL,
  `hr_approval` text DEFAULT NULL,
  `management_decision` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tasks`
--

CREATE TABLE `tasks` (
  `Id_Task` int(11) NOT NULL,
  `Name_task` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `Id_user` int(11) NOT NULL,
  `Login` varchar(60) NOT NULL,
  `Password` varchar(256) DEFAULT NULL,
  `Name` varchar(50) NOT NULL,
  `Privilege` varchar(20) NOT NULL,
  `Date` date DEFAULT NULL,
  `Phone_number` varchar(10) DEFAULT NULL,
  `Image` varchar(250) DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `company` varchar(255) DEFAULT 'Simple Sourcing'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `additional`
--
ALTER TABLE `additional`
  ADD PRIMARY KEY (`Id`);

--
-- Index pour la table `bureau`
--
ALTER TABLE `bureau`
  ADD PRIMARY KEY (`Id_Bureau`);

--
-- Index pour la table `date_entries`
--
ALTER TABLE `date_entries`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `demande`
--
ALTER TABLE `demande`
  ADD PRIMARY KEY (`Demande_id`);

--
-- Index pour la table `demandes`
--
ALTER TABLE `demandes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`Id_folder`);

--
-- Index pour la table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `formulaire`
--
ALTER TABLE `formulaire`
  ADD PRIMARY KEY (`Id_formulaire`);

--
-- Index pour la table `impo`
--
ALTER TABLE `impo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `informations`
--
ALTER TABLE `informations`
  ADD PRIMARY KEY (`Id_info`);

--
-- Index pour la table `request_periode`
--
ALTER TABLE `request_periode`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`Id_Task`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Id_user`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `additional`
--
ALTER TABLE `additional`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `bureau`
--
ALTER TABLE `bureau`
  MODIFY `Id_Bureau` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `date_entries`
--
ALTER TABLE `date_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `demande`
--
ALTER TABLE `demande`
  MODIFY `Demande_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `demandes`
--
ALTER TABLE `demandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `folders`
--
ALTER TABLE `folders`
  MODIFY `Id_folder` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `forms`
--
ALTER TABLE `forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formulaire`
--
ALTER TABLE `formulaire`
  MODIFY `Id_formulaire` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `impo`
--
ALTER TABLE `impo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `informations`
--
ALTER TABLE `informations`
  MODIFY `Id_info` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `request_periode`
--
ALTER TABLE `request_periode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `Id_Task` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `Id_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `request_periode`
--
ALTER TABLE `request_periode`
  ADD CONSTRAINT `request_periode_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`Id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
