-- phpMyAdmin SQL Dump
-- version 2.10.0-beta1
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Dimanche 04 Février 2007 à 22:27
-- Version du serveur: 5.0.27
-- Version de PHP: 5.2.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Base de données: `genealogie`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `agregats_noms`
-- 

CREATE TABLE `agregats_noms` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `lettre` tinyblob NOT NULL,
  `nombre` smallint(3) unsigned NOT NULL default '0',
  `longueur` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `lettre` (`lettre`(5)),
  KEY `longueur` (`longueur`),
  KEY `base` (`base`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Tbale de précalul pour l''affichage de la liste des patronym' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `agregats_noms`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `genea_download`
-- 

CREATE TABLE `genea_download` (
  `download_id` mediumint(8) unsigned NOT NULL auto_increment,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `download_fichier` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `download_titre` varchar(45) collate utf8_unicode_ci NOT NULL default '',
  `download_description` tinytext collate utf8_unicode_ci,
  `download_timestamp` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`download_id`),
  KEY `base` (`base`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Table contenant les fichiers à télécharger' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `genea_download`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `genea_events_address`
-- 

CREATE TABLE `genea_events_address` (
  `address_id` mediumint(8) unsigned NOT NULL auto_increment,
  `events_details_id` mediumint(8) unsigned NOT NULL default '0',
  `base` tinyint(4) unsigned NOT NULL default '0',
  `address_addr` text collate utf8_unicode_ci NOT NULL,
  `address_city` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `address_stae` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `address_post` varchar(10) collate utf8_unicode_ci NOT NULL default '',
  `address_ctry` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `address_phon1` varchar(25) collate utf8_unicode_ci NOT NULL default '',
  `address_phon2` varchar(25) collate utf8_unicode_ci NOT NULL default '',
  `address_phon3` varchar(25) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`address_id`),
  KEY `base` (`base`),
  KEY `events_details_id` (`events_details_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Table complémentaire de genea_events qui apporte des infos ' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `genea_events_address`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `genea_events_details`
-- 

CREATE TABLE `genea_events_details` (
  `events_details_id` mediumint(8) unsigned NOT NULL auto_increment,
  `place_id` mediumint(8) unsigned default NULL,
  `events_details_descriptor` varchar(4) collate utf8_unicode_ci NOT NULL default '',
  `events_details_gedcom_date` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `events_details_age` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `events_details_cause` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `jd_count` mediumint(8) unsigned default NULL,
  `jd_precision` tinyint(3) unsigned default NULL,
  `jd_calendar` varchar(45) collate utf8_unicode_ci default NULL,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `events_details_timestamp` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`events_details_id`),
  KEY `base` (`base`),
  KEY `place_id` (`place_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Tables contenant tous les évènements' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `genea_events_details`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `genea_familles`
-- 

CREATE TABLE `genea_familles` (
  `familles_id` mediumint(8) unsigned NOT NULL auto_increment,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `familles_wife` mediumint(8) unsigned default NULL,
  `familles_husb` mediumint(8) unsigned default NULL,
  `familles_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`familles_id`),
  KEY `husb` (`familles_husb`),
  KEY `wife` (`familles_wife`),
  KEY `base` (`base`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Table contenant les familles' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `genea_familles`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `genea_individuals`
-- 

CREATE TABLE `genea_individuals` (
  `indi_id` mediumint(8) unsigned NOT NULL auto_increment,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `indi_nom` char(100) collate utf8_unicode_ci NOT NULL default '',
  `indi_prenom` char(100) collate utf8_unicode_ci NOT NULL default '',
  `indi_sexe` char(1) collate utf8_unicode_ci NOT NULL default '',
  `indi_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `indi_npfx` char(30) collate utf8_unicode_ci NOT NULL default '',
  `indi_givn` char(120) collate utf8_unicode_ci NOT NULL default '',
  `indi_nick` char(30) collate utf8_unicode_ci NOT NULL default '',
  `indi_spfx` char(30) collate utf8_unicode_ci NOT NULL default '',
  `indi_surn` char(120) collate utf8_unicode_ci NOT NULL default '',
  `indi_nsfx` char(30) collate utf8_unicode_ci NOT NULL default '',
  `indi_resn` enum('locked','privacy') collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`indi_id`),
  KEY `indi_nom` (`indi_nom`),
  KEY `indi_prenom` (`indi_prenom`),
  KEY `base` (`base`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Table contenant les individus' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `genea_individuals`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `genea_infos`
-- 

CREATE TABLE `genea_infos` (
  `id` tinyint(4) unsigned NOT NULL auto_increment,
  `nom` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `descriptif` tinytext collate utf8_unicode_ci NOT NULL,
  `entetes` text collate utf8_unicode_ci NOT NULL,
  `ged_corp` varchar(90) collate utf8_unicode_ci NOT NULL default '',
  `subm` mediumint(8) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `subm` (`subm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Paramètres généraux des bases de données généalogiques' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `genea_infos`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `genea_membres`
-- 

CREATE TABLE `genea_membres` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `email` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `pass` varchar(32) character set utf8 collate utf8_bin NOT NULL default '',
  `langue` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `theme` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `place` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Liste des utilisateurs du site' AUTO_INCREMENT=3 ;

-- 
-- Contenu de la table `genea_membres`
-- 

INSERT INTO `genea_membres` (`id`, `email`, `pass`, `langue`, `theme`, `place`) VALUES 
(1, 'Annonyme', '', '', '', ''),
(2, 'genealogie@parois.net', 0x3165376139623964316666653732363437646563613231383735643630643761, '', '', '');

-- --------------------------------------------------------

-- 
-- Structure de la table `genea_multimedia`
-- 

CREATE TABLE `genea_multimedia` (
  `media_id` mediumint(8) unsigned NOT NULL auto_increment,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `media_title` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `media_format` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `media_file` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `media_timestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`media_id`),
  KEY `base` (`base`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Liste des documents multimédia' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `genea_multimedia`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `genea_notes`
-- 

CREATE TABLE `genea_notes` (
  `notes_id` mediumint(8) unsigned NOT NULL auto_increment,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `notes_text` text collate utf8_unicode_ci NOT NULL,
  `notes_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`notes_id`),
  KEY `base` (`base`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Liste des notes' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `genea_notes`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `genea_permissions`
-- 

CREATE TABLE `genea_permissions` (
  `permission_id` mediumint(8) unsigned NOT NULL auto_increment,
  `membre_id` tinyint(3) unsigned NOT NULL default '0',
  `permission_type` tinyint(3) unsigned NOT NULL default '0',
  `permission_value` mediumint(8) unsigned NOT NULL default '0',
  `base` char(4) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`permission_id`),
  UNIQUE KEY `unicite` (`membre_id`,`permission_type`,`permission_value`,`base`),
  KEY `type` (`permission_type`),
  KEY `id_base` (`base`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Gestion des permissions des différents membres' AUTO_INCREMENT=14 ;

-- 
-- Contenu de la table `genea_permissions`
-- 

INSERT INTO `genea_permissions` (`permission_id`, `membre_id`, `permission_type`, `permission_value`, `base`) VALUES 
(1, 2, 1, 1, '*'),
(2, 2, 2, 1, '*'),
(3, 2, 3, 1, '*'),
(4, 2, 4, 1, '*'),
(5, 2, 5, 1, '*'),
(6, 2, 6, 1, '*'),
(7, 2, 7, 1, '*'),
(9, 2, 8, 1, '*'),
(10, 2, 9, 1, '*'),
(11, 2, 10, 1, '*'),
(12, 2, 11, 1, '*'),
(13, 2, 13, 0, '*'),
(8, 2, 14, 1, '*');

-- --------------------------------------------------------

-- 
-- Structure de la table `genea_place`
-- 

CREATE TABLE `genea_place` (
  `place_id` mediumint(8) unsigned NOT NULL auto_increment,
  `place_lieudit` varchar(50) collate utf8_unicode_ci default NULL,
  `place_ville` varchar(50) collate utf8_unicode_ci default NULL,
  `place_cp` varchar(50) collate utf8_unicode_ci default NULL,
  `place_insee` mediumint(8) unsigned default NULL,
  `place_departement` varchar(50) collate utf8_unicode_ci default NULL,
  `place_region` varchar(50) collate utf8_unicode_ci default NULL,
  `place_pays` varchar(50) collate utf8_unicode_ci default NULL,
  `place_longitude` float default NULL,
  `place_latitude` float default NULL,
  `base` tinyint(4) unsigned default NULL,
  PRIMARY KEY  (`place_id`),
  KEY `base` (`base`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Liste des lieux géographiques de la généalogie' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `genea_place`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `genea_repository`
-- 

CREATE TABLE `genea_repository` (
  `repo_id` mediumint(8) unsigned NOT NULL auto_increment,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `repo_name` varchar(90) collate utf8_unicode_ci NOT NULL default '',
  `repo_addr` text collate utf8_unicode_ci NOT NULL,
  `repo_city` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `repo_post` varchar(10) collate utf8_unicode_ci NOT NULL default '',
  `repo_stae` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `repo_ctry` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `repo_phon1` varchar(25) collate utf8_unicode_ci NOT NULL default '',
  `repo_phon2` varchar(25) collate utf8_unicode_ci NOT NULL default '',
  `repo_phon3` varchar(25) collate utf8_unicode_ci NOT NULL default '',
  `repo_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`repo_id`),
  KEY `base` (`base`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='liste des dépots d''archives' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `genea_repository`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `genea_sour_citations`
-- 

CREATE TABLE `genea_sour_citations` (
  `sour_citations_id` mediumint(8) unsigned NOT NULL auto_increment,
  `sour_records_id` mediumint(8) unsigned default NULL,
  `sour_citations_page` varchar(248) collate utf8_unicode_ci NOT NULL default '',
  `sour_citations_even` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `sour_citations_even_role` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `sour_citations_data_dates` varchar(90) collate utf8_unicode_ci NOT NULL default '',
  `sour_citations_data_text` mediumtext collate utf8_unicode_ci NOT NULL,
  `sour_citations_quay` tinyint(1) unsigned default NULL,
  `sour_citations_subm` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `sour_citations_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `base` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sour_citations_id`),
  KEY `sour_records_id` (`sour_records_id`),
  KEY `base` (`base`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `genea_sour_citations`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `genea_sour_records`
-- 

CREATE TABLE `genea_sour_records` (
  `sour_records_id` mediumint(8) unsigned NOT NULL auto_increment,
  `sour_records_auth` varchar(248) collate utf8_unicode_ci NOT NULL default '',
  `sour_records_title` mediumtext collate utf8_unicode_ci NOT NULL,
  `sour_records_abbr` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `sour_records_publ` mediumtext collate utf8_unicode_ci NOT NULL,
  `sour_records_data_even` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `sour_records_data_date` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `sour_records_data_place` varchar(120) collate utf8_unicode_ci NOT NULL default '',
  `sour_records_agnc` varchar(120) collate utf8_unicode_ci NOT NULL default '',
  `repo_id` mediumint(8) unsigned default NULL,
  `repo_caln` varchar(120) collate utf8_unicode_ci NOT NULL default '',
  `repo_medi` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `sour_records_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `base` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sour_records_id`),
  KEY `base` (`base`),
  KEY `repo_id` (`repo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `genea_sour_records`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `genea_submitters`
-- 

CREATE TABLE `genea_submitters` (
  `sub_id` mediumint(8) unsigned NOT NULL auto_increment,
  `sub_name` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `sub_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `sub_addr` mediumtext collate utf8_unicode_ci,
  `sub_city` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `sub_stae` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `sub_post` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `sub_ctry` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `sub_phon1` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `sub_phon2` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `sub_phon3` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `sub_lang` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`sub_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Liste des auteurs - NON UTILISE' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `genea_submitters`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `gns_ADM1`
-- 

CREATE TABLE `gns_ADM1` (
  `CC` varchar(2) collate utf8_unicode_ci NOT NULL default '',
  `ADM1` varchar(2) collate utf8_unicode_ci NOT NULL default '0',
  `name` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`CC`,`ADM1`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Liste des régions des pays chargé dans la base de données';

-- 
-- Contenu de la table `gns_ADM1`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `gns_CC`
-- 

CREATE TABLE `gns_CC` (
  `RC` decimal(1,0) NOT NULL default '0',
  `CC` char(2) collate utf8_unicode_ci NOT NULL default '',
  `import` int(1) NOT NULL default '0',
  PRIMARY KEY  (`RC`,`CC`),
  KEY `CC` (`CC`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Liste des pays disponibles pour la base de données des lieu';

-- 
-- Contenu de la table `gns_CC`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `gns_lieux`
-- 

CREATE TABLE `gns_lieux` (
  `RC` decimal(1,0) NOT NULL default '0',
  `UNI` decimal(10,0) NOT NULL default '0',
  `LAT` decimal(10,7) NOT NULL default '0.0000000',
  `LONGI` decimal(11,7) NOT NULL default '0.0000000',
  `UTM` varchar(4) collate utf8_unicode_ci NOT NULL default '',
  `JOG` varchar(7) collate utf8_unicode_ci NOT NULL default '',
  `CC1` varchar(2) collate utf8_unicode_ci NOT NULL default '',
  `ADM1` varchar(2) collate utf8_unicode_ci NOT NULL default '',
  `SORT_NAME` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `FULL_NAME` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`UNI`),
  KEY `RC` (`RC`,`CC1`,`ADM1`),
  KEY `CC1` (`CC1`),
  KEY `FULL_NAME` (`FULL_NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Base de données de lieux géographiques';

-- 
-- Contenu de la table `gns_lieux`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_alias`
-- 

CREATE TABLE `rel_alias` (
  `alias1` mediumint(8) unsigned NOT NULL default '0',
  `alias2` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`alias1`,`alias2`),
  KEY `alias2` (`alias2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Stockage des alias des individus';

-- 
-- Contenu de la table `rel_alias`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_asso_events`
-- 

CREATE TABLE `rel_asso_events` (
  `events_details_id` mediumint(8) unsigned NOT NULL default '0',
  `indi_id` mediumint(8) unsigned NOT NULL default '0',
  `description` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`indi_id`,`events_details_id`),
  KEY `events_id` (`events_details_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''un témoin à un évènement';

-- 
-- Contenu de la table `rel_asso_events`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_asso_familles`
-- 

CREATE TABLE `rel_asso_familles` (
  `indi_id` mediumint(8) unsigned NOT NULL default '0',
  `familles_id` mediumint(8) unsigned NOT NULL default '0',
  `description` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`indi_id`,`familles_id`),
  KEY `familles_id` (`familles_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''une relation non familiale à une famille';

-- 
-- Contenu de la table `rel_asso_familles`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_asso_indi`
-- 

CREATE TABLE `rel_asso_indi` (
  `indi_id1` mediumint(8) unsigned NOT NULL default '0',
  `indi_id2` mediumint(8) unsigned NOT NULL default '0',
  `description` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`indi_id1`,`indi_id2`),
  KEY `indi_id2` (`indi_id2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''une relation non familiale à un individu';

-- 
-- Contenu de la table `rel_asso_indi`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_events_multimedia`
-- 

CREATE TABLE `rel_events_multimedia` (
  `media_id` mediumint(8) unsigned NOT NULL default '0',
  `events_details_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`events_details_id`,`media_id`),
  KEY `media_id` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''un objet multimedia à un évènement';

-- 
-- Contenu de la table `rel_events_multimedia`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_events_notes`
-- 

CREATE TABLE `rel_events_notes` (
  `events_details_id` mediumint(8) unsigned NOT NULL default '0',
  `notes_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`events_details_id`,`notes_id`),
  KEY `notes_id` (`notes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''une note à un évènement';

-- 
-- Contenu de la table `rel_events_notes`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_events_sources`
-- 

CREATE TABLE `rel_events_sources` (
  `events_details_id` mediumint(8) unsigned NOT NULL default '0',
  `sour_citations_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`events_details_id`,`sour_citations_id`),
  KEY `sources_id` (`sour_citations_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''une source à un évènement';

-- 
-- Contenu de la table `rel_events_sources`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_familles_events`
-- 

CREATE TABLE `rel_familles_events` (
  `events_details_id` mediumint(8) unsigned NOT NULL default '0',
  `familles_id` mediumint(8) unsigned NOT NULL default '0',
  `events_tag` varchar(4) collate utf8_unicode_ci NOT NULL default '',
  `events_attestation` char(1) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`familles_id`,`events_details_id`),
  KEY `events_id` (`events_details_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''un évènement ç une famille';

-- 
-- Contenu de la table `rel_familles_events`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_familles_indi`
-- 

CREATE TABLE `rel_familles_indi` (
  `indi_id` mediumint(8) unsigned NOT NULL default '0',
  `familles_id` mediumint(8) unsigned NOT NULL default '0',
  `rela_type` char(10) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`familles_id`,`indi_id`),
  KEY `indi_id` (`indi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''un enfant à une famille';

-- 
-- Contenu de la table `rel_familles_indi`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_familles_multimedia`
-- 

CREATE TABLE `rel_familles_multimedia` (
  `media_id` mediumint(8) unsigned NOT NULL default '0',
  `familles_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`familles_id`,`media_id`),
  KEY `media_id` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''un objet multimedia à une famille';

-- 
-- Contenu de la table `rel_familles_multimedia`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_familles_notes`
-- 

CREATE TABLE `rel_familles_notes` (
  `notes_id` mediumint(8) unsigned NOT NULL default '0',
  `familles_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`familles_id`,`notes_id`),
  KEY `notes_id` (`notes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''une note à une famille';

-- 
-- Contenu de la table `rel_familles_notes`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_familles_sources`
-- 

CREATE TABLE `rel_familles_sources` (
  `sour_citations_id` mediumint(8) unsigned NOT NULL default '0',
  `familles_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`familles_id`,`sour_citations_id`),
  KEY `sources_id` (`sour_citations_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''une source à une famille';

-- 
-- Contenu de la table `rel_familles_sources`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_indi_attributes`
-- 

CREATE TABLE `rel_indi_attributes` (
  `events_details_id` mediumint(8) unsigned NOT NULL default '0',
  `indi_id` mediumint(8) unsigned NOT NULL default '0',
  `events_tag` varchar(4) collate utf8_unicode_ci NOT NULL default '',
  `events_descr` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`indi_id`,`events_details_id`),
  KEY `events_id` (`events_details_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''un évènement ç une famille';

-- 
-- Contenu de la table `rel_indi_attributes`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_indi_events`
-- 

CREATE TABLE `rel_indi_events` (
  `events_details_id` mediumint(8) unsigned NOT NULL default '0',
  `indi_id` mediumint(8) unsigned NOT NULL default '0',
  `events_tag` varchar(4) collate utf8_unicode_ci NOT NULL default '',
  `events_attestation` char(1) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`indi_id`,`events_details_id`),
  KEY `events_id` (`events_details_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''un évènement ç une famille';

-- 
-- Contenu de la table `rel_indi_events`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_indi_multimedia`
-- 

CREATE TABLE `rel_indi_multimedia` (
  `media_id` mediumint(8) unsigned NOT NULL default '0',
  `indi_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`indi_id`,`media_id`),
  KEY `media_id` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Contenu de la table `rel_indi_multimedia`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_indi_notes`
-- 

CREATE TABLE `rel_indi_notes` (
  `notes_id` mediumint(8) unsigned NOT NULL default '0',
  `indi_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`indi_id`,`notes_id`),
  KEY `notes_id` (`notes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Contenu de la table `rel_indi_notes`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_indi_sources`
-- 

CREATE TABLE `rel_indi_sources` (
  `sour_citations_id` mediumint(8) unsigned NOT NULL default '0',
  `indi_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`indi_id`,`sour_citations_id`),
  KEY `sources_id` (`sour_citations_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Contenu de la table `rel_indi_sources`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_multimedia_notes`
-- 

CREATE TABLE `rel_multimedia_notes` (
  `notes_id` mediumint(8) unsigned NOT NULL default '0',
  `media_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`notes_id`,`media_id`),
  KEY `media_id` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''un objet multimedia à une source';

-- 
-- Contenu de la table `rel_multimedia_notes`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_repo_notes`
-- 

CREATE TABLE `rel_repo_notes` (
  `notes_id` mediumint(8) unsigned NOT NULL default '0',
  `repo_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`notes_id`,`repo_id`),
  KEY `media_id` (`repo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''un objet multimedia à une source';

-- 
-- Contenu de la table `rel_repo_notes`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_sour_citations_multimedia`
-- 

CREATE TABLE `rel_sour_citations_multimedia` (
  `sour_citations_id` mediumint(8) unsigned NOT NULL default '0',
  `media_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sour_citations_id`,`media_id`),
  KEY `media_id` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''un objet multimedia à une source';

-- 
-- Contenu de la table `rel_sour_citations_multimedia`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `rel_sour_citations_notes`
-- 

CREATE TABLE `rel_sour_citations_notes` (
  `notes_id` mediumint(8) unsigned NOT NULL default '0',
  `sour_citations_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`notes_id`,`sour_citations_id`),
  KEY `media_id` (`sour_citations_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''un objet multimedia à une source';

-- 
-- Contenu de la table `rel_sour_citations_notes`
-- 


-- 
-- Contraintes pour les tables exportées
-- 

-- 
-- Contraintes pour la table `agregats_noms`
-- 
ALTER TABLE `agregats_noms`
  ADD CONSTRAINT `agregats_noms_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `genea_download`
-- 
ALTER TABLE `genea_download`
  ADD CONSTRAINT `genea_download_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `genea_events_address`
-- 
ALTER TABLE `genea_events_address`
  ADD CONSTRAINT `genea_events_address_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `genea_events_address_ibfk_2` FOREIGN KEY (`events_details_id`) REFERENCES `genea_events_details` (`events_details_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `genea_events_details`
-- 
ALTER TABLE `genea_events_details`
  ADD CONSTRAINT `genea_events_details_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `genea_events_details_ibfk_2` FOREIGN KEY (`place_id`) REFERENCES `genea_place` (`place_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `genea_familles`
-- 
ALTER TABLE `genea_familles`
  ADD CONSTRAINT `genea_familles_ibfk_1` FOREIGN KEY (`familles_husb`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `genea_familles_ibfk_2` FOREIGN KEY (`familles_wife`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `genea_familles_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `genea_individuals`
-- 
ALTER TABLE `genea_individuals`
  ADD CONSTRAINT `genea_individuals_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `genea_infos`
-- 
ALTER TABLE `genea_infos`
  ADD CONSTRAINT `genea_infos_ibfk_1` FOREIGN KEY (`subm`) REFERENCES `genea_submitters` (`sub_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `genea_multimedia`
-- 
ALTER TABLE `genea_multimedia`
  ADD CONSTRAINT `genea_multimedia_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `genea_notes`
-- 
ALTER TABLE `genea_notes`
  ADD CONSTRAINT `genea_notes_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `genea_place`
-- 
ALTER TABLE `genea_place`
  ADD CONSTRAINT `genea_place_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `genea_repository`
-- 
ALTER TABLE `genea_repository`
  ADD CONSTRAINT `genea_repository_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `genea_sour_citations`
-- 
ALTER TABLE `genea_sour_citations`
  ADD CONSTRAINT `genea_sour_citations_ibfk_1` FOREIGN KEY (`sour_records_id`) REFERENCES `genea_sour_records` (`sour_records_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `genea_sour_citations_ibfk_2` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `genea_sour_records`
-- 
ALTER TABLE `genea_sour_records`
  ADD CONSTRAINT `genea_sour_records_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_alias`
-- 
ALTER TABLE `rel_alias`
  ADD CONSTRAINT `rel_alias_ibfk_1` FOREIGN KEY (`alias1`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_alias_ibfk_2` FOREIGN KEY (`alias2`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_asso_events`
-- 
ALTER TABLE `rel_asso_events`
  ADD CONSTRAINT `rel_asso_events_ibfk_1` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_asso_events_ibfk_2` FOREIGN KEY (`events_details_id`) REFERENCES `genea_events_details` (`events_details_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_asso_familles`
-- 
ALTER TABLE `rel_asso_familles`
  ADD CONSTRAINT `rel_asso_familles_ibfk_1` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_asso_familles_ibfk_2` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_asso_indi`
-- 
ALTER TABLE `rel_asso_indi`
  ADD CONSTRAINT `rel_asso_indi_ibfk_1` FOREIGN KEY (`indi_id1`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_asso_indi_ibfk_2` FOREIGN KEY (`indi_id2`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_events_multimedia`
-- 
ALTER TABLE `rel_events_multimedia`
  ADD CONSTRAINT `rel_events_multimedia_ibfk_5` FOREIGN KEY (`media_id`) REFERENCES `genea_multimedia` (`media_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_events_multimedia_ibfk_6` FOREIGN KEY (`events_details_id`) REFERENCES `genea_events_details` (`events_details_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_events_notes`
-- 
ALTER TABLE `rel_events_notes`
  ADD CONSTRAINT `rel_events_notes_ibfk_1` FOREIGN KEY (`notes_id`) REFERENCES `genea_notes` (`notes_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_events_notes_ibfk_2` FOREIGN KEY (`events_details_id`) REFERENCES `genea_events_details` (`events_details_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_events_sources`
-- 
ALTER TABLE `rel_events_sources`
  ADD CONSTRAINT `rel_events_sources_ibfk_1` FOREIGN KEY (`events_details_id`) REFERENCES `genea_events_details` (`events_details_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_events_sources_ibfk_2` FOREIGN KEY (`sour_citations_id`) REFERENCES `genea_sour_citations` (`sour_citations_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_familles_events`
-- 
ALTER TABLE `rel_familles_events`
  ADD CONSTRAINT `rel_familles_events_ibfk_2` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_familles_indi`
-- 
ALTER TABLE `rel_familles_indi`
  ADD CONSTRAINT `rel_familles_indi_ibfk_1` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_familles_indi_ibfk_2` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_familles_multimedia`
-- 
ALTER TABLE `rel_familles_multimedia`
  ADD CONSTRAINT `rel_familles_multimedia_ibfk_2` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_familles_multimedia_ibfk_3` FOREIGN KEY (`media_id`) REFERENCES `genea_multimedia` (`media_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_familles_notes`
-- 
ALTER TABLE `rel_familles_notes`
  ADD CONSTRAINT `rel_familles_notes_ibfk_1` FOREIGN KEY (`notes_id`) REFERENCES `genea_notes` (`notes_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_familles_notes_ibfk_2` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_familles_sources`
-- 
ALTER TABLE `rel_familles_sources`
  ADD CONSTRAINT `rel_familles_sources_ibfk_1` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_familles_sources_ibfk_2` FOREIGN KEY (`sour_citations_id`) REFERENCES `genea_sour_citations` (`sour_citations_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_indi_attributes`
-- 
ALTER TABLE `rel_indi_attributes`
  ADD CONSTRAINT `rel_indi_attributes_ibfk_1` FOREIGN KEY (`events_details_id`) REFERENCES `genea_events_details` (`events_details_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_indi_attributes_ibfk_2` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_indi_events`
-- 
ALTER TABLE `rel_indi_events`
  ADD CONSTRAINT `rel_indi_events_ibfk_1` FOREIGN KEY (`events_details_id`) REFERENCES `genea_events_details` (`events_details_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_indi_events_ibfk_2` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_indi_multimedia`
-- 
ALTER TABLE `rel_indi_multimedia`
  ADD CONSTRAINT `rel_indi_multimedia_ibfk_1` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_indi_multimedia_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `genea_multimedia` (`media_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_indi_notes`
-- 
ALTER TABLE `rel_indi_notes`
  ADD CONSTRAINT `rel_indi_notes_ibfk_1` FOREIGN KEY (`notes_id`) REFERENCES `genea_notes` (`notes_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_indi_notes_ibfk_2` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_indi_sources`
-- 
ALTER TABLE `rel_indi_sources`
  ADD CONSTRAINT `rel_indi_sources_ibfk_2` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_indi_sources_ibfk_3` FOREIGN KEY (`sour_citations_id`) REFERENCES `genea_sour_citations` (`sour_citations_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_multimedia_notes`
-- 
ALTER TABLE `rel_multimedia_notes`
  ADD CONSTRAINT `rel_multimedia_notes_ibfk_1` FOREIGN KEY (`notes_id`) REFERENCES `genea_notes` (`notes_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_multimedia_notes_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `genea_multimedia` (`media_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_repo_notes`
-- 
ALTER TABLE `rel_repo_notes`
  ADD CONSTRAINT `rel_repo_notes_ibfk_1` FOREIGN KEY (`notes_id`) REFERENCES `genea_notes` (`notes_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_repo_notes_ibfk_2` FOREIGN KEY (`repo_id`) REFERENCES `genea_repository` (`repo_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_sour_citations_multimedia`
-- 
ALTER TABLE `rel_sour_citations_multimedia`
  ADD CONSTRAINT `rel_sour_citations_multimedia_ibfk_1` FOREIGN KEY (`sour_citations_id`) REFERENCES `genea_sour_citations` (`sour_citations_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_sour_citations_multimedia_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `genea_multimedia` (`media_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Contraintes pour la table `rel_sour_citations_notes`
-- 
ALTER TABLE `rel_sour_citations_notes`
  ADD CONSTRAINT `rel_sour_citations_notes_ibfk_1` FOREIGN KEY (`notes_id`) REFERENCES `genea_notes` (`notes_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rel_sour_citations_notes_ibfk_2` FOREIGN KEY (`sour_citations_id`) REFERENCES `genea_sour_citations` (`sour_citations_id`) ON DELETE CASCADE ON UPDATE CASCADE;
