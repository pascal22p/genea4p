<?
/************************************************************************/
/************************************************************************/
/*                                                                      */
/* NMIG : NPDS Module Installer Generator                               */
/* --------------------------------------                               */
/*                                                                      */
/* Version Beta 0.11 - 10 Janvier 2005                                  */
/* -----------------------------------                                  */
/*                                                                      */
/* Dvelopp par Boris - http://www.lordi-depanneur.com                 */
/*                                                                      */#	N      N  M      M  IIIII     GGG
/* Installeur inspir du programme d'installation d'origine du module   */#	NN     N  MM    MM    I     GG   GG
/* Hot-Projet dvelopp par Hotfirenet                                  */#	N N    N  M M  M M    I    G       G
/*                                                                      */#	N  N   N  M  MM  M    I    G
/************************************************************************/#	N   N  N  M      M    I    G   GGGGGG
/*                                                                      */#	N    N N  M      M    I    G      GG
/* NPDS : Net Portal Dynamic System                                     */#	N     NN  M      M    I     GG   GG
/* ================================                                     */#	N      N  M      M  IIIII     GGG
/*                                                                      */
/* This version name NPDS Copyright (c) 2001 by Philippe Brunier        */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/*                                                                      */
/************************************************************************/
/************************************************************************/

#autodoc : Dfinition des styles personnaliss

echo "\n\n<style type=\"text/css\">\n";
echo "<!--\n";
echo "div.installer_code { background-color: white; text-align: left; border: groove; }\
";
echo "-->\
";
echo "</style>\n\n";

if((!isset($cookie) or $cookie[0]==1) and $nmig=='e2')
{
  die('Protection contre la slection d\'un annonyme comme administrateur du module');
}

if(!isset($cookie) or $cookie[0]==1)
{
  $txtdeb="Bienvenue  l'installation du module de <i>gnalogie</i>.<br />
<b>Attention :</b> l'administrateur du module n'est pas l'administrateur de NPDS<br />
<div style=\"font-size:large; margin-top:10px;margin-bottom:50px;\">veuillez vous identifi comme membre de NPDS.</div>";
}
else
  $txtdeb="Bienvenue  l'installation de <i>gnalogie</i>.<br />
  <b>Attention :</b> l'administrateur du module n'est pas l'administrateur de NPDS<br />
<div style=\"font-size:large; margin-top:10px;margin-bottom:50px;\">Le membre de NPDS suivant : <b>".$cookie[1]." (uid=".$cookie[0].")</b> sera utilisé pour l'administration du module</div>";

if($_GET['ModInstall']!='genea4p')
  $txtdeb.='<center><hr /><span style="font-size:large;">Le module doit-être plac dans le rpertoire genea4p</span><hr /></center>';

if($_GET['ModInstall']!='genea4p' and $nmig=='e2')
  die('Le module doit-tre plac dans le rpertoire genea4p');

#autodoc $name_module: Nom du module

$name_module = "genea4p";


#autodoc $list_fich : Modifications de fichiers: Dans le premier tableau, tapez le nom du fichier
#autodoc et dans le deuxime, A LA MEME POSITION D'INDEX QUE LE PREMIER, tapez le code  insrer dans le fichier.
#autodoc Si le fichier doit tre cr, n'oubliez pas les < ? php et ? > !!! (sans espace!).
#autodoc Synopsis: $list_fich = array(array("nom_fichier1","nom_fichier2"), array("contenu_fchier1","contenu_fichier2"));


$list_fich = array(array("modules/include/header_head.inc"), array('if(defined(\'_g4p_module_load_\'))
{
  if(file_exists(\'modules/genea4p/styles/default/style-\'.$_SESSION[\'langue\'].\'.css\'))
    echo \'<link href="modules/genea4p/styles/default/style-\'.$_SESSION[\'langue\'].\'.css" type="text/css" media="all" rel="stylesheet"  />\';
  else
    echo \'<link href="modules/genea4p/styles/default/style.css" type="text/css" media="all" rel="stylesheet" />\';

  echo \'<link href="modules/genea4p/styles/style_print.css" type="text/css" media="print" rel="stylesheet" />\';
  
  echo \'<script language="JavaScript" type="text/javascript">
  function confirme(objet, message)
  {
    if (!confirm(message))
    {
      objet.href = "#";
      return 0;
    }
    else
    {
      return 1;
    }
  }
  </script>\';
}
'));


#autodoc $sql = array(""): Si votre module doit excuter une ou plusieurs requtes SQL, tapez vos requtes ici.
#autodoc Attention! UNE requte par lment de tableau!
#autodoc Synopsis: $sql = array("requte_sql_1","requte_sql_2");

$sql = array(
"SET FOREIGN_KEY_CHECKS=0",

"ALTER TABLE `users` TYPE = INNODB;",

"DROP TABLE IF EXISTS `agregats_noms`;",
"CREATE TABLE `agregats_noms` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `lettre` tinyblob NOT NULL,
  `nombre` smallint(3) unsigned NOT NULL default '0',
  `longueur` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `lettre` (`lettre`(5)),
  KEY `longueur` (`longueur`),
  KEY `base` (`base`),
  CONSTRAINT `agregats_noms_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Tbale de prcalul pour l''affichage de la liste des patronym';",

"DROP TABLE IF EXISTS `genea_alias`;",
"CREATE TABLE `genea_alias` (
  `base` tinyint(4) unsigned default NULL,
  `alias1` mediumint(8) unsigned NOT NULL default '0',
  `alias2` mediumint(8) unsigned NOT NULL default '0',
  UNIQUE KEY `unicite` (`alias1`,`alias2`),
  KEY `alias2` (`alias2`),
  KEY `base` (`base`),
  CONSTRAINT `genea_alias_ibfk_1` FOREIGN KEY (`alias1`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `genea_alias_ibfk_2` FOREIGN KEY (`alias2`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `genea_alias_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Stockage des alias des individus';",

"DROP TABLE IF EXISTS `genea_download`;",
"CREATE TABLE `genea_download` (
  `download_id` mediumint(8) unsigned NOT NULL auto_increment,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `fichier` varchar(50) NOT NULL default '',
  `titre` varchar(45) NOT NULL default '',
  `description` tinytext,
  `permission` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`download_id`),
  KEY `base` (`base`),
  CONSTRAINT `genea_download_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Table contenant les fichiers  tlcharger';",

"DROP TABLE IF EXISTS `genea_events`;",
"CREATE TABLE `genea_events` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `place_id` mediumint(8) unsigned default NULL,
  `type` varchar(4) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `date_event` varchar(100) NOT NULL default '',
  `age` varchar(20) NOT NULL default '',
  `cause` mediumtext NOT NULL,
  `jd_count` mediumint(8) unsigned default NULL,
  `jd_precision` tinyint(3) unsigned default NULL,
  `jd_calendar` varchar(45) default NULL,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `attestation` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `base` (`base`),
  KEY `place_id` (`place_id`),
  CONSTRAINT `genea_events_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `genea_events_ibfk_2` FOREIGN KEY (`place_id`) REFERENCES `genea_place` (`place_id`) ON DELETE SET NULL ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Tables contenant tous les vnements et attributs';",

"DROP TABLE IF EXISTS `genea_familles`;",
"CREATE TABLE `genea_familles` (
  `familles_id` mediumint(8) unsigned NOT NULL auto_increment,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `familles_wife` mediumint(8) unsigned default NULL,
  `familles_husb` mediumint(8) unsigned default NULL,
  `familles_chan` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`familles_id`),
  KEY `husb` (`familles_husb`),
  KEY `wife` (`familles_wife`),
  KEY `base` (`base`),
  CONSTRAINT `genea_familles_ibfk_1` FOREIGN KEY (`familles_husb`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `genea_familles_ibfk_2` FOREIGN KEY (`familles_wife`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `genea_familles_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Table contenant les familles';",

"DROP TABLE IF EXISTS `genea_individuals`;",
"CREATE TABLE `genea_individuals` (
  `indi_id` mediumint(8) unsigned NOT NULL auto_increment,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `indi_nom` char(100) NOT NULL default '',
  `indi_prenom` char(100) NOT NULL default '',
  `indi_sexe` char(1) NOT NULL default '',
  `indi_chan` datetime NOT NULL default '0000-00-00 00:00:00',
  `npfx` char(30) NOT NULL default '',
  `givn` char(120) NOT NULL default '',
  `nick` char(30) NOT NULL default '',
  `spfx` char(30) NOT NULL default '',
  `surn` char(120) NOT NULL default '',
  `nsfx` char(30) NOT NULL default '',
  `resn` enum('lock','privacy') default NULL,
  PRIMARY KEY  (`indi_id`),
  KEY `indi_nom` (`indi_nom`),
  KEY `indi_prenom` (`indi_prenom`),
  KEY `base` (`base`),
  CONSTRAINT `genea_individuals_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Table contenant les individus';",

"DROP TABLE IF EXISTS `genea_infos`;",
"CREATE TABLE `genea_infos` (
  `id` tinyint(4) unsigned NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL default '',
  `descriptif` tinytext NOT NULL,
  `entetes` text NOT NULL,
  `ged_corp` varchar(90) NOT NULL default '',
  `subm` mediumint(8) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `subm` (`subm`),
  CONSTRAINT `genea_infos_ibfk_1` FOREIGN KEY (`subm`) REFERENCES `genea_submitters` (`sub_id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Paramtres gnraux des bases de donnes gnalogiques';",


"DROP TABLE IF EXISTS `genea_multimedia`;",
"CREATE TABLE `genea_multimedia` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `title` char(200) NOT NULL default '',
  `format` char(50) NOT NULL default '',
  `file` char(200) NOT NULL default '',
  `chan` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `base` (`base`),
  CONSTRAINT `genea_multimedia_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Liste des documents multimdia';",

"DROP TABLE IF EXISTS `genea_notes`;",
"CREATE TABLE `genea_notes` (
  `notes_id` mediumint(8) unsigned NOT NULL auto_increment,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `notes_text` text NOT NULL,
  `notes_chan` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`notes_id`),
  KEY `base` (`base`),
  CONSTRAINT `genea_notes_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Liste des notes';",

"DROP TABLE IF EXISTS `genea_permissions`;",
"CREATE TABLE `genea_permissions` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `id_membre` int(11) NOT NULL default '0',
  `type` tinyint(3) unsigned NOT NULL default '0',
  `permission` mediumint(8) unsigned NOT NULL default '0',
  `id_base` char(4) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unicite` (`id_membre`,`type`,`permission`,`id_base`),
  KEY `type` (`type`),
  KEY `id_base` (`id_base`),
  CONSTRAINT `genea_permissions_ibfk_1` FOREIGN KEY (`id_membre`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Gestion des permissions des diffrents membres';",

"INSERT INTO `genea_permissions` (id_membre, id_base, type, permission) VALUES (".$cookie[0].", '*', 1, 1);",
"INSERT INTO `genea_permissions` (id_membre, id_base, type, permission) VALUES (".$cookie[0].", '*', 2, 1);",
"INSERT INTO `genea_permissions`  (id_membre, id_base, type, permission) VALUES (".$cookie[0].", '*', 3, 1);",
"INSERT INTO `genea_permissions`  (id_membre, id_base, type, permission) VALUES (".$cookie[0].", '*', 4, 1);",
"INSERT INTO `genea_permissions`  (id_membre, id_base, type, permission) VALUES (".$cookie[0].", '*', 5, 1);",
"INSERT INTO `genea_permissions`  (id_membre, id_base, type, permission) VALUES (".$cookie[0].", '*', 6, 1);",
"INSERT INTO `genea_permissions`  (id_membre, id_base, type, permission) VALUES (".$cookie[0].", '*', 7, 1);",
"INSERT INTO `genea_permissions`  (id_membre, id_base, type, permission) VALUES (".$cookie[0].", '*', 14, 1);",
"INSERT INTO `genea_permissions`  (id_membre, id_base, type, permission) VALUES (".$cookie[0].", '*', 8, 1);",
"INSERT INTO `genea_permissions`  (id_membre, id_base, type, permission) VALUES (".$cookie[0].", '*', 9, 1);",
"INSERT INTO `genea_permissions`  (id_membre, id_base, type, permission) VALUES (".$cookie[0].", '*', 10, 1);",
"INSERT INTO `genea_permissions`  (id_membre, id_base, type, permission) VALUES (".$cookie[0].", '*', 11, 1);",
"INSERT INTO `genea_permissions`  (id_membre, id_base, type, permission) VALUES (".$cookie[0].", '*', 13, 0);",

"DROP TABLE IF EXISTS `genea_place`;",
"CREATE TABLE `genea_place` (
  `place_id` mediumint(8) unsigned NOT NULL auto_increment,
  `place_lieudit` varchar(50) default NULL,
  `place_ville` varchar(50) default NULL,
  `place_cp` varchar(50) default NULL,
  `place_insee` mediumint(8) unsigned default NULL,
  `place_departement` varchar(50) default NULL,
  `place_region` varchar(50) default NULL,
  `place_pays` varchar(50) default NULL,
  `place_longitude` float default NULL,
  `place_latitude` float default NULL,
  `base` tinyint(4) unsigned default NULL,
  PRIMARY KEY  (`place_id`),
  KEY `base` (`base`),
  CONSTRAINT `genea_place_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Liste des lieux gographiques de la gnalogie';",

"DROP TABLE IF EXISTS `genea_repository`;",
"CREATE TABLE `genea_repository` (
  `repo_id` mediumint(8) unsigned NOT NULL auto_increment,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `repo_name` varchar(90) NOT NULL default '',
  `repo_addr` varchar(255) NOT NULL default '',
  `repo_city` varchar(60) NOT NULL default '',
  `repo_post` varchar(10) NOT NULL default '',
  `repo_ctry` varchar(60) NOT NULL default '',
  `repo_phon` varchar(25) NOT NULL default '',
  `repo_chan` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`repo_id`),
  KEY `base` (`base`),
  CONSTRAINT `genea_repository_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='liste des dpots d''archives';",

"DROP TABLE IF EXISTS `genea_sources`;",
"CREATE TABLE `genea_sources` (
  `sources_id` mediumint(8) unsigned NOT NULL auto_increment,
  `repo_id` mediumint(8) unsigned default NULL,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `sources_title` varchar(255) NOT NULL default '',
  `sources_publ` text NOT NULL,
  `sources_text` text NOT NULL,
  `sources_auth` varchar(255) NOT NULL default '',
  `sources_caln` varchar(120) NOT NULL default '',
  `sources_page` varchar(255) NOT NULL default '',
  `sources_medi` varchar(15) NOT NULL default '',
  `sources_chan` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`sources_id`),
  KEY `repo_id` (`repo_id`),
  KEY `base` (`base`),
  CONSTRAINT `genea_sources_ibfk_1` FOREIGN KEY (`repo_id`) REFERENCES `genea_repository` (`repo_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `genea_sources_ibfk_2` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Liste des sources';",

"DROP TABLE IF EXISTS `genea_submitters`;",
"CREATE TABLE `genea_submitters` (
  `sub_id` mediumint(8) unsigned NOT NULL auto_increment,
  `sub_nom` varchar(100) NOT NULL default '',
  `sub_prenom` varchar(100) NOT NULL default '',
  `sub_chan` datetime NOT NULL default '0000-00-00 00:00:00',
  `sub_addr` mediumtext,
  PRIMARY KEY  (`sub_id`)
) TYPE=InnoDB COMMENT='Liste des auteurs - NON UTILISE';",

"DROP TABLE IF EXISTS `gns_ADM1`;",
"CREATE TABLE `gns_ADM1` (
  `CC` varchar(2) NOT NULL default '',
  `ADM1` varchar(2) NOT NULL default '0',
  `name` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`CC`,`ADM1`)
) TYPE=InnoDB COMMENT='Liste des rgions des pays charg dans la base de donnes';",

"DROP TABLE IF EXISTS `gns_CC`;",
"CREATE TABLE `gns_CC` (
  `RC` decimal(1,0) NOT NULL default '0',
  `CC` char(2) NOT NULL default '',
  `import` int(1) NOT NULL default '0',
  PRIMARY KEY  (`RC`,`CC`)
) TYPE=InnoDB COMMENT='Liste des pays disponibles pour la base de donnes des lieu';",

"DROP TABLE IF EXISTS `gns_lieux`;",
"CREATE TABLE `gns_lieux` (
  `RC` decimal(1,0) NOT NULL default '0',
  `UNI` decimal(10,0) NOT NULL default '0',
  `LAT` decimal(10,7) NOT NULL default '0.0000000',
  `LONGI` decimal(11,7) NOT NULL default '0.0000000',
  `UTM` varchar(4) NOT NULL default '',
  `JOG` varchar(7) NOT NULL default '',
  `CC1` varchar(2) NOT NULL default '',
  `ADM1` varchar(2) NOT NULL default '',
  `SORT_NAME` varchar(200) NOT NULL default '',
  `FULL_NAME` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`UNI`),
  KEY `RC` (`RC`,`CC1`,`ADM1`),
  KEY `CC1` (`CC1`),
  KEY `FULL_NAME` (`FULL_NAME`)
) TYPE=InnoDB COMMENT='Base de donnes de lieux gographiques';",

"DROP TABLE IF EXISTS `rel_asso_events`;",
"CREATE TABLE `rel_asso_events` (
  `events_id` mediumint(8) unsigned NOT NULL default '0',
  `indi_id` mediumint(8) unsigned NOT NULL default '0',
  `base` tinyint(4) unsigned NOT NULL default '0',
  `description` varchar(255) default NULL,
  KEY `base` (`base`),
  KEY `indi_id` (`indi_id`),
  KEY `events_id` (`events_id`),
  CONSTRAINT `rel_asso_events_ibfk_1` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_asso_events_ibfk_2` FOREIGN KEY (`events_id`) REFERENCES `genea_events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_asso_events_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Association d''un tmoin  un vnement';",

"DROP TABLE IF EXISTS `rel_asso_familles`;",
"CREATE TABLE `rel_asso_familles` (
  `base` tinyint(4) unsigned NOT NULL default '0',
  `indi_id` mediumint(8) unsigned NOT NULL default '0',
  `familles_id` mediumint(8) unsigned NOT NULL default '0',
  `description` varchar(255) default NULL,
  KEY `indi_id` (`indi_id`),
  KEY `familles_id` (`familles_id`),
  KEY `base` (`base`),
  CONSTRAINT `rel_asso_familles_ibfk_1` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_asso_familles_ibfk_2` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_asso_familles_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Association d''une relation non familiale  une famille';",

"DROP TABLE IF EXISTS `rel_asso_indi`;",
"CREATE TABLE `rel_asso_indi` (
  `base` tinyint(4) unsigned NOT NULL default '0',
  `indi_id1` mediumint(8) unsigned NOT NULL default '0',
  `indi_id2` mediumint(8) unsigned NOT NULL default '0',
  `description` varchar(255) NOT NULL default '',
  KEY `indi_id1` (`indi_id1`),
  KEY `indi_id2` (`indi_id2`),
  KEY `base` (`base`),
  CONSTRAINT `rel_asso_indi_ibfk_1` FOREIGN KEY (`indi_id1`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_asso_indi_ibfk_2` FOREIGN KEY (`indi_id2`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_asso_indi_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Association d''une relation non familiale  un individu';",

"DROP TABLE IF EXISTS `rel_events_multimedia`;",
"CREATE TABLE `rel_events_multimedia` (
  `base` tinyint(4) unsigned NOT NULL default '0',
  `multimedia_id` mediumint(8) unsigned NOT NULL default '0',
  `events_id` mediumint(8) unsigned NOT NULL default '0',
  KEY `base` (`base`),
  KEY `multimedia_id` (`multimedia_id`),
  KEY `events_id` (`events_id`),
  CONSTRAINT `rel_events_multimedia_ibfk_1` FOREIGN KEY (`events_id`) REFERENCES `genea_events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_events_multimedia_ibfk_2` FOREIGN KEY (`multimedia_id`) REFERENCES `genea_multimedia` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_events_multimedia_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Association d''un objet multimedia  un vnement';",

"DROP TABLE IF EXISTS `rel_events_notes`;",
"CREATE TABLE `rel_events_notes` (
  `base` tinyint(4) unsigned NOT NULL default '0',
  `events_id` mediumint(8) unsigned NOT NULL default '0',
  `notes_id` mediumint(8) unsigned NOT NULL default '0',
  KEY `notes_id` (`notes_id`),
  KEY `events_id` (`events_id`),
  KEY `base` (`base`),
  CONSTRAINT `rel_events_notes_ibfk_1` FOREIGN KEY (`notes_id`) REFERENCES `genea_notes` (`notes_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_events_notes_ibfk_2` FOREIGN KEY (`events_id`) REFERENCES `genea_events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_events_notes_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Association d''une note  un vnement';",

"DROP TABLE IF EXISTS `rel_events_sources`;",
"CREATE TABLE `rel_events_sources` (
  `events_id` mediumint(8) unsigned NOT NULL default '0',
  `base` tinyint(4) unsigned NOT NULL default '0',
  `sources_id` mediumint(8) unsigned NOT NULL default '0',
  KEY `sources_id` (`sources_id`),
  KEY `base` (`base`),
  KEY `events_id` (`events_id`),
  CONSTRAINT `rel_events_sources_ibfk_1` FOREIGN KEY (`events_id`) REFERENCES `genea_events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_events_sources_ibfk_2` FOREIGN KEY (`sources_id`) REFERENCES `genea_sources` (`sources_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_events_sources_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Association d''une source  un vnement';",

"DROP TABLE IF EXISTS `rel_familles_event`;",
"CREATE TABLE `rel_familles_event` (
  `base` tinyint(4) unsigned NOT NULL default '0',
  `events_id` mediumint(8) unsigned NOT NULL default '0',
  `familles_id` mediumint(8) unsigned NOT NULL default '0',
  KEY `familles_id` (`familles_id`),
  KEY `base` (`base`),
  KEY `events_id` (`events_id`),
  CONSTRAINT `rel_familles_event_ibfk_1` FOREIGN KEY (`events_id`) REFERENCES `genea_events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_familles_event_ibfk_2` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_familles_event_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Association d''un vnement  une famille';",

"DROP TABLE IF EXISTS `rel_familles_indi`;",
"CREATE TABLE `rel_familles_indi` (
  `rela_id` mediumint(8) unsigned NOT NULL auto_increment,
  `base` tinyint(4) unsigned NOT NULL default '0',
  `indi_id` mediumint(8) unsigned NOT NULL default '0',
  `familles_id` mediumint(8) unsigned NOT NULL default '0',
  `rela_type` char(10) NOT NULL default '',
  PRIMARY KEY  (`rela_id`),
  KEY `indi_id` (`indi_id`),
  KEY `familles_id` (`familles_id`),
  KEY `base` (`base`),
  CONSTRAINT `rel_familles_indi_ibfk_1` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_familles_indi_ibfk_2` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_familles_indi_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Association d''un enfant  une famille';",

"DROP TABLE IF EXISTS `rel_familles_multimedia`;",
"CREATE TABLE `rel_familles_multimedia` (
  `multimedia_id` mediumint(8) unsigned NOT NULL default '0',
  `base` tinyint(4) unsigned NOT NULL default '0',
  `familles_id` mediumint(8) unsigned NOT NULL default '0',
  KEY `familles_id` (`familles_id`),
  KEY `base` (`base`),
  KEY `multimedia_id` (`multimedia_id`),
  CONSTRAINT `rel_familles_multimedia_ibfk_1` FOREIGN KEY (`multimedia_id`) REFERENCES `genea_multimedia` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_familles_multimedia_ibfk_2` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_familles_multimedia_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Association d''un objet multimedia  une famille';",

"DROP TABLE IF EXISTS `rel_familles_notes`;",
"CREATE TABLE `rel_familles_notes` (
  `base` tinyint(4) unsigned NOT NULL default '0',
  `notes_id` mediumint(8) unsigned NOT NULL default '0',
  `familles_id` mediumint(8) unsigned NOT NULL default '0',
  KEY `notes_id` (`notes_id`),
  KEY `familles_id` (`familles_id`),
  KEY `base` (`base`),
  CONSTRAINT `rel_familles_notes_ibfk_1` FOREIGN KEY (`notes_id`) REFERENCES `genea_notes` (`notes_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_familles_notes_ibfk_2` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_familles_notes_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Association d''une note  une famille';",

"DROP TABLE IF EXISTS `rel_familles_sources`;",
"CREATE TABLE `rel_familles_sources` (
  `base` tinyint(4) unsigned NOT NULL default '0',
  `sources_id` mediumint(8) unsigned NOT NULL default '0',
  `familles_id` mediumint(8) unsigned NOT NULL default '0',
  KEY `sources_id` (`sources_id`),
  KEY `familles_id` (`familles_id`),
  KEY `base` (`base`),
  CONSTRAINT `rel_familles_sources_ibfk_1` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_familles_sources_ibfk_2` FOREIGN KEY (`sources_id`) REFERENCES `genea_sources` (`sources_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_familles_sources_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Association d''une source  une famille';",

"DROP TABLE IF EXISTS `rel_indi_event`;",
"CREATE TABLE `rel_indi_event` (
  `events_id` mediumint(8) unsigned NOT NULL default '0',
  `indi_id` mediumint(8) unsigned NOT NULL default '0',
  `base` tinyint(4) unsigned NOT NULL default '0',
  KEY `indi_id` (`indi_id`),
  KEY `base` (`base`),
  KEY `events_id` (`events_id`),
  CONSTRAINT `rel_indi_event_ibfk_1` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_indi_event_ibfk_2` FOREIGN KEY (`events_id`) REFERENCES `genea_events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_indi_event_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB;",

"DROP TABLE IF EXISTS `rel_indi_multimedia`;",
"CREATE TABLE `rel_indi_multimedia` (
  `multimedia_id` mediumint(8) unsigned NOT NULL default '0',
  `base` tinyint(4) unsigned NOT NULL default '0',
  `indi_id` mediumint(8) unsigned NOT NULL default '0',
  KEY `indi_id` (`indi_id`),
  KEY `base` (`base`),
  KEY `multimedia_id` (`multimedia_id`),
  CONSTRAINT `rel_indi_multimedia_ibfk_1` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_indi_multimedia_ibfk_2` FOREIGN KEY (`multimedia_id`) REFERENCES `genea_multimedia` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_indi_multimedia_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB;",

"DROP TABLE IF EXISTS `rel_indi_notes`;",
"CREATE TABLE `rel_indi_notes` (
  `base` tinyint(4) unsigned NOT NULL default '0',
  `notes_id` mediumint(8) unsigned NOT NULL default '0',
  `indi_id` mediumint(8) unsigned NOT NULL default '0',
  KEY `indi_id` (`indi_id`),
  KEY `notes_id` (`notes_id`),
  KEY `base` (`base`),
  CONSTRAINT `rel_indi_notes_ibfk_1` FOREIGN KEY (`notes_id`) REFERENCES `genea_notes` (`notes_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_indi_notes_ibfk_2` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_indi_notes_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB;",

"DROP TABLE IF EXISTS `rel_indi_sources`;",
"CREATE TABLE `rel_indi_sources` (
  `base` tinyint(4) unsigned NOT NULL default '0',
  `sources_id` mediumint(8) unsigned NOT NULL default '0',
  `indi_id` mediumint(8) unsigned NOT NULL default '0',
  KEY `indi_id` (`indi_id`),
  KEY `sources_id` (`sources_id`),
  KEY `base` (`base`),
  CONSTRAINT `rel_indi_sources_ibfk_1` FOREIGN KEY (`sources_id`) REFERENCES `genea_sources` (`sources_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_indi_sources_ibfk_2` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_indi_sources_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB;",

"DROP TABLE IF EXISTS `rel_sources_multimedia`;",
"CREATE TABLE `rel_sources_multimedia` (
  `base` tinyint(4) unsigned NOT NULL default '0',
  `sources_id` mediumint(8) unsigned NOT NULL default '0',
  `multimedia_id` mediumint(8) unsigned NOT NULL default '0',
  KEY `base` (`base`),
  KEY `sources_id` (`sources_id`),
  KEY `multimedia_id` (`multimedia_id`),
  CONSTRAINT `rel_sources_multimedia_ibfk_1` FOREIGN KEY (`multimedia_id`) REFERENCES `genea_multimedia` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_sources_multimedia_ibfk_2` FOREIGN KEY (`sources_id`) REFERENCES `genea_sources` (`sources_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rel_sources_multimedia_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB COMMENT='Association d''un objet multimedia  une source';",

);


#autodoc $blocs = array(array(""), array(""), array(""), array(""), array(""), array(""), array(""), array(""), array(""))
#autodoc                titre      contenu    membre     groupe     index      rtention  actif      aide       description
#autodoc Configuration des blocs

$blocs = array(array("Généalogie"), array("include\#modules/genea4p/menu/menu_npds.php"), array("0"), array("7"), array("0"), array("0"), array("1"), array(""));

#autodoc $txtfin : Vous pouvez mettre ici un texte de votre choix avec du html qui s'affichera  la fin de l'install

$txtfin = "Installation terminée avec succès, merci de retourner tous vos problèmes  : <a href=\"mailto:genealogie@parois.net\">genealogie@parois.net</a>";


#autodoc $link: Lien sur lequel sera redirig l'utilisateur  la fin de l'install (si laiss vide, redirig sur index.php)
#autodoc N'oubliez pas les '\' si vous utilisez des guillemets !!!

$end_link = "modules.php?ModPath=genea4p&ModStart=index";
$_SESSION['message']='Bienvenue,<br />la premire chose  faire est de créer une base de donnes généalogique';
?>
