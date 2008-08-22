<?php
header("Content-type: text/html; charset=UTF-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
  <title>Migration de la base de données</title>
  <meta name="Author" content="PAROIS Pascal" />

  <style type="text/css">
  span.ok
  {
    font-weight:bold;
    color:green;
  }
  span.nok
  {
    font-weight:bold;
    color:red;
  }
  </style>

  </head>
<body>
<?php

$g4p_config['g4p_db_host']='localhost';
$g4p_config['g4p_db_login']='root';
$g4p_config['g4p_db_mdp']='';
$g4p_config['g4p_db_base']='pascalp:genealogie';

$db_error=0;

$connec=mysql_connect($g4p_config['g4p_db_host'], $g4p_config['g4p_db_login'], $g4p_config['g4p_db_mdp']) or die('Impossible de se connecter au serveur MySQL');
mysql_query("SET CHARACTER SET 'utf8'");
mysql_query("SET NAMES 'utf8'" );

mysql_selectdb($g4p_config['g4p_db_base'],$connec) or die('Impossible sélectionner la nase de données');

$sql="SET AUTOCOMMIT = 0";
@mysql_query($sql) or die(mysql_error());

echo '<h2>Désactivation des contraintes de clés étrangères</h2>';
$sql="SET FOREIGN_KEY_CHECKS = 0";
@mysql_query($sql)?(print('<span class="ok">[OK]</span>')):die(mysql_error());

$sql = "SHOW TABLES FROM `".$g4p_config['g4p_db_base']."`";
$query = @mysql_query($sql) or die(mysql_error());

while ($row = mysql_fetch_row($query)) {
   $list_tables[]=$row[0];
}

foreach($list_tables as $a_table)
{
	$sql="SHOW CREATE TABLE ".$a_table;
	$query=@mysql_query($sql) or die(mysql_error());

	while($ligne=mysql_fetch_assoc($query))
	{
		$table_structure[$ligne['Table']]=$ligne['Create Table'];
	}
}
foreach($table_structure as $table=>$structure)
{
	preg_match_all("/CONSTRAINT (.*) FOREIGN/",$structure,$match);
	$liste_keys[$table]=$match[1];
}

echo '<h2>Suppression des clés étrangères</h2>';
foreach ($liste_keys as $table=>$keys)
{
	foreach($keys as $key)
	{
		echo '<br /><br />',$sql="ALTER TABLE $table DROP FOREIGN KEY $key";
		if(@mysql_query($sql))
			echo '<span class="ok">[OK]</span>';
		else {
			echo '<span class="nok">[NOK]</span>';
		}
	}
}

$sql="START TRANSACTION";
@mysql_query($sql)?(print('<span class="ok">[OK]</span>')):die(mysql_error());

echo '<h2>Modification/création des tables</h2>';

echo '<br /><br />',$sql="ALTER TABLE `genea_download` CHANGE `fichier` `download_fichier` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_download` CHANGE `titre` `download_titre` VARCHAR( 45 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
CHANGE `description` `download_description` TINYTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_download` DROP `permission` ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_download` ADD `download_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="CREATE TABLE `genea_events_address` (
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
  PRIMARY KEY  (`events_details_id`),
  KEY `base` (`base`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Table complémentaire de genea_events qui apporte des infos ';";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="CREATE TABLE `genea_events_details` (
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
  KEY `place_id` (`place_id`),
  KEY `jd_count` (`jd_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Tables contenant tous les évènements' AUTO_INCREMENT=1 ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="INSERT INTO genea_events_details (events_details_id, place_id, events_details_descriptor, events_details_gedcom_date, events_details_age, events_details_cause, jd_count, jd_precision, jd_calendar, base, events_details_timestamp)
  SELECT genea_events.id, genea_events.place_id, genea_events.type, genea_events.date_event,  genea_events.age, genea_events.cause, genea_events.jd_count, genea_events.jd_precision, genea_events.jd_calendar, genea_events.base, NULL
  FROM genea_events ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_familles` CHANGE `familles_chan` `familles_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="UPDATE `genea_familles` set `familles_timestamp`=NULL WHERE `familles_timestamp`='0000-00-00 00:00:00';";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_familles` ADD UNIQUE (
`familles_husb` ,
`familles_wife`
)";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_familles` DROP INDEX `husb`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_individuals` CHANGE `indi_chan` `indi_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="UPDATE `genea_individuals` set `indi_timestamp`=NULL WHERE `indi_timestamp`='0000-00-00 00:00:00';";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_individuals` CHANGE `npfx` `indi_npfx` CHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
CHANGE `givn` `indi_givn` CHAR( 120 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
CHANGE `nick` `indi_nick` CHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
CHANGE `spfx` `indi_spfx` CHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
CHANGE `surn` `indi_surn` CHAR( 120 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
CHANGE `nsfx` `indi_nsfx` CHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
CHANGE `resn` `indi_resn` ENUM( 'lock', 'privacy' ) NULL DEFAULT NULL ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="UPDATE `genea_individuals` set `indi_resn`=NULL WHERE `indi_resn`='';";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_membres` DROP INDEX `email` ,
ADD UNIQUE `email` ( `email` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_membres` ADD INDEX ( `pass` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_multimedia` CHANGE `id` `media_id` MEDIUMINT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `title` `media_title` CHAR( 200 ) NOT NULL ,
CHANGE `format` `media_format` CHAR( 50 ) NOT NULL ,
CHANGE `file` `media_file` CHAR( 200 ) NOT NULL ,
CHANGE `chan` `media_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_notes` CHANGE `notes_chan` `notes_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_permissions` CHANGE `id` `permission_id` MEDIUMINT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `id_membre` `membre_id` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0',
CHANGE `type` `permission_type` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0',
CHANGE `permission` `permission_value` MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '0',
CHANGE `id_base` `base` CHAR( 4 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_repository` CHANGE `repo_chan` `repo_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_repository` CHANGE `repo_ctry` `repo_stae` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="CREATE TABLE `genea_sour_citations` (
  `sour_citations_id` mediumint(8) unsigned NOT NULL auto_increment,
  `sour_records_id` mediumint(8) unsigned NOT NULL default '0',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="CREATE TABLE `genea_sour_records` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="INSERT INTO genea_sour_records (sour_records_id,sour_records_auth,sour_records_title,
  sour_records_abbr,sour_records_publ,sour_records_data_even,sour_records_data_date,
  sour_records_data_place,sour_records_agnc,repo_id,repo_caln,repo_medi,
  sour_records_timestamp,base)
  SELECT genea_sources.sources_id, genea_sources.sources_auth, genea_sources.sources_title,
  '', genea_sources.sources_publ, '', '', '', '', genea_sources.repo_id, '',
  genea_sources.sources_medi, genea_sources.sources_chan, genea_sources.base FROM genea_sources ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="INSERT INTO genea_sour_citations (sour_citations_id,sour_records_id,sour_citations_page,
  sour_citations_even,sour_citations_even_role,sour_citations_data_dates,
  sour_citations_data_text,sour_citations_quay,sour_citations_subm,sour_citations_timestamp, base)
  SELECT genea_sources.sources_id, genea_sources.sources_id, genea_sources.sources_page,
  '', '', '', genea_sources.sources_text, '', genea_sources.sources_auth, genea_sources.sources_chan,
  genea_sources.base FROM genea_sources ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}


echo '<br /><br />',$sql="ALTER TABLE `genea_submitters` CHANGE `sub_id` `sub_id` MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '0',
CHANGE `sub_nom` `sub_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
CHANGE `sub_chan` `sub_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
CHANGE `sub_addr` `sub_addr` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_submitters` DROP `sub_prenom` ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_submitters` ADD `sub_city` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
ADD `sub_stae` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
ADD `sub_post` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
ADD `sub_ctry` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
ADD `sub_phon1` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
ADD `sub_phon2` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
ADD `sub_phon3` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
ADD `sub_lang` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_alias` DROP `base`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_alias` ADD PRIMARY KEY ( `alias1` , `alias2` ) ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_alias` DROP INDEX `unicite` ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="CREATE TABLE `rel_alias` (
  `alias1` mediumint(8) unsigned NOT NULL default '0',
  `alias2` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`alias1`,`alias2`),
  KEY `alias2` (`alias2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Stockage des alias des individus';";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_asso_events` DROP `base` ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_asso_events` CHANGE `events_id` `events_details_id` MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '0'";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_asso_events` ADD PRIMARY KEY ( `events_details_id` , `indi_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_asso_events` DROP INDEX `events_id`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_asso_familles` DROP `base` ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_asso_familles` ADD PRIMARY KEY ( `indi_id` , `familles_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_asso_familles` DROP INDEX `indi_id` ";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_asso_indi` DROP `base` ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_asso_indi` ADD PRIMARY KEY ( `indi_id1` , `indi_id2` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_asso_indi` DROP INDEX `indi_id1`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_multimedia` DROP `base`;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_multimedia` CHANGE `multimedia_id` `media_id` MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '0'";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_multimedia` CHANGE `events_id` `events_details_id` MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '0';";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_multimedia` ADD PRIMARY KEY ( `media_id` , `events_details_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_multimedia` DROP INDEX `multimedia_id`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_notes` CHANGE `events_id` `events_details_id` MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '0';";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_notes` ADD PRIMARY KEY ( `events_details_id` , `notes_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_notes` DROP INDEX `events_id`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_sources` DROP `base` ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_sources` CHANGE `events_id` `events_details_id` MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '0';";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_sources` CHANGE `sources_id` `sour_citations_id` MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '0'";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_sources` ADD PRIMARY KEY ( `sour_citations_id` , `events_details_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_sources` DROP INDEX `sources_id` ";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="CREATE TABLE `rel_familles_events` (
`events_details_id` MEDIUMINT( 8 ) unsigned NOT NULL ,
`familles_id` MEDIUMINT( 8 ) unsigned NOT NULL ,
`events_tag` VARCHAR( 4 ) NOT NULL ,
`events_attestation` CHAR( 1 ) NULL DEFAULT NULL
) ENGINE = innodb CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="INSERT INTO rel_familles_events( events_details_id, familles_id, events_tag )
SELECT events_id, familles_id,
TYPE FROM `rel_familles_event`
LEFT JOIN genea_events ON ( events_id = id )
WHERE TYPE IN (
'ANUL', 'CENS', 'DIV', 'DIVF', 'ENGA', 'MARR', 'MARB', 'MARC', 'MARL', 'MARS', 'EVEN'
);";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_events` ADD PRIMARY KEY ( `events_details_id` , `familles_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_events` ADD INDEX ( `familles_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_indi` DROP `base`  ;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_indi` DROP `rela_id`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_indi` ADD PRIMARY KEY ( `familles_id` , `indi_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_indi` DROP INDEX `familles_id`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_multimedia` DROP `base`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_multimedia` CHANGE `multimedia_id` `media_id` MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '0'";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_multimedia` ADD PRIMARY KEY ( `familles_id` , `media_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_multimedia` DROP INDEX `familles_id`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_notes` DROP `base`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_notes` ADD PRIMARY KEY ( `familles_id` , `notes_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_notes` DROP INDEX `familles_id` ";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_sources` DROP `base`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_sources` ADD PRIMARY KEY ( `familles_id` , `sources_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_sources` DROP INDEX `familles_id` ";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="CREATE TABLE `rel_indi_attributes` (
`events_details_id` MEDIUMINT( 8 ) unsigned NOT NULL ,
`indi_id` MEDIUMINT( 8 ) unsigned NOT NULL ,
`events_tag` VARCHAR( 4 ) NOT NULL ,
`events_descr` VARCHAR( 255 ) NULL DEFAULT NULL
) ENGINE = innodb CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="INSERT INTO rel_indi_attributes ( events_details_id, indi_id, events_tag, events_descr )
SELECT events_id, indi_id, type, description
TYPE FROM `rel_indi_event`
LEFT JOIN genea_events ON ( events_id = id )
WHERE TYPE IN (
'CAST', 'EDUC', 'NATI', 'OCCU', 'PROP', 'RELI', 'RESI', 'TITL'
);";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_attributes` ADD PRIMARY KEY ( `indi_id` , `events_details_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_attributes` ADD INDEX ( `events_details_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="CREATE TABLE `rel_indi_events` (
`events_details_id` MEDIUMINT( 8 ) unsigned NOT NULL ,
`indi_id` MEDIUMINT( 8 ) unsigned NOT NULL ,
`events_tag` VARCHAR( 4 ) NOT NULL ,
`events_attestation` VARCHAR( 255 ) NULL DEFAULT NULL
) ENGINE = innodb CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="INSERT INTO rel_indi_events ( events_details_id, indi_id, events_tag, events_attestation )
SELECT events_id, indi_id, type, attestation
TYPE FROM `rel_indi_event`
LEFT JOIN genea_events ON ( events_id = id )
WHERE TYPE IN (
'ADOP', 'BIRT', 'BAPM', 'BARM', 'BASM', 'BLES', 'BURI', 'CENS', 'CHR', 'CHRA', 'CONF', 'CREM', 'DEAT', 'EMIG', 'FCOM', 'GRAD', 'IMMI', 'NATU', 'ORDN', 'RETI', 'PROB', 'WILL', 'EVEN'
);";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_events` ADD PRIMARY KEY ( `indi_id` , `events_details_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_events` ADD INDEX ( `events_details_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_multimedia` DROP `base`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_multimedia` CHANGE `multimedia_id` `media_id` MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '0'";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_multimedia` ADD PRIMARY KEY ( `indi_id` , `media_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_multimedia` DROP INDEX `indi_id`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_notes` DROP `base`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_notes` ADD PRIMARY KEY ( `indi_id` , `notes_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_notes` DROP INDEX `indi_id`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_sources` DROP `base`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_sources` ADD PRIMARY KEY ( `indi_id` , `sources_id` )";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_sources` DROP INDEX `indi_id`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="CREATE TABLE rel_multimedia_notes (
  notes_id mediumint(8) unsigned NOT NULL default '0',
  media_id mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (notes_id,media_id),
  KEY media_id (media_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''un objet multimedia à une source';";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}


echo '<br /><br />',$sql="CREATE TABLE rel_repo_notes (
  notes_id mediumint(8) unsigned NOT NULL default '0',
  repo_id mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (notes_id,repo_id),
  KEY media_id (repo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''un objet multimedia à une source';";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="CREATE TABLE rel_sour_citations_multimedia (
  sour_citations_id mediumint(8) unsigned NOT NULL default '0',
  media_id mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (sour_citations_id,media_id),
  KEY media_id (media_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''un objet multimedia à une source';";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="CREATE TABLE rel_sour_citations_notes (
  notes_id mediumint(8) unsigned NOT NULL default '0',
  sour_citations_id mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (notes_id,sour_citations_id),
  KEY media_id (sour_citations_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Association d''un objet multimedia à une source';";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<h2>Suppression des tables inutiles</h2>';
echo '<br /><br />',$sql="DROP TABLE `genea_events`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="DROP TABLE `genea_sources`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="DROP TABLE `rel_sources_multimedia`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="DROP TABLE `rel_familles_event`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql=" DROP TABLE `rel_indi_event`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="DROP TABLE `genea_alias`";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<h2>Création des contraintes de clés étrangères</h2>';

echo '<br /><br />',$sql="ALTER TABLE `agregats_noms`
  ADD CONSTRAINT `agregats_noms_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_download`
  ADD CONSTRAINT `genea_download_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_events_address`
  ADD CONSTRAINT `genea_events_address_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `genea_events_address_ibfk_2` FOREIGN KEY (`events_details_id`) REFERENCES `genea_events_details` (`events_details_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_events_details`
  ADD CONSTRAINT `genea_events_details_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `genea_events_details_ibfk_2` FOREIGN KEY (`place_id`) REFERENCES `genea_place` (`place_id`) ON DELETE SET NULL ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_familles`
  ADD CONSTRAINT `genea_familles_ibfk_1` FOREIGN KEY (`familles_husb`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE SET NULL ON UPDATE CASCADE,  ADD CONSTRAINT `genea_familles_ibfk_2` FOREIGN KEY (`familles_wife`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE SET NULL ON UPDATE CASCADE,  ADD CONSTRAINT `genea_familles_ibfk_3` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_individuals`
  ADD CONSTRAINT `genea_individuals_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_infos`
  ADD CONSTRAINT `genea_infos_ibfk_1` FOREIGN KEY (`subm`) REFERENCES `genea_submitters` (`sub_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_multimedia`
  ADD CONSTRAINT `genea_multimedia_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_notes`
  ADD CONSTRAINT `genea_notes_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_place`
  ADD CONSTRAINT `genea_place_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_repository`
  ADD CONSTRAINT `genea_repository_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_sour_citations`
  ADD CONSTRAINT `genea_sour_citations_ibfk_1` FOREIGN KEY (`sour_records_id`) REFERENCES `genea_sour_records` (`sour_records_id`) ON DELETE NO ACTION ON UPDATE CASCADE,  ADD CONSTRAINT `genea_sour_citations_ibfk_2` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `genea_sour_records`
  ADD CONSTRAINT `genea_sour_records_ibfk_1` FOREIGN KEY (`base`) REFERENCES `genea_infos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_alias`
  ADD CONSTRAINT `rel_alias_ibfk_1` FOREIGN KEY (`alias1`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_alias_ibfk_2` FOREIGN KEY (`alias2`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_asso_events`
  ADD CONSTRAINT `rel_asso_events_ibfk_1` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_asso_events_ibfk_2` FOREIGN KEY (`events_details_id`) REFERENCES `genea_events_details` (`events_details_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_asso_familles`
  ADD CONSTRAINT `rel_asso_familles_ibfk_1` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_asso_familles_ibfk_2` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_asso_indi`
  ADD CONSTRAINT `rel_asso_indi_ibfk_1` FOREIGN KEY (`indi_id1`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_asso_indi_ibfk_2` FOREIGN KEY (`indi_id2`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_multimedia`
  ADD CONSTRAINT `rel_events_multimedia_ibfk_5` FOREIGN KEY (`media_id`) REFERENCES `genea_multimedia` (`media_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_events_multimedia_ibfk_6` FOREIGN KEY (`events_details_id`) REFERENCES `genea_events_details` (`events_details_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_notes`
  ADD CONSTRAINT `rel_events_notes_ibfk_1` FOREIGN KEY (`notes_id`) REFERENCES `genea_notes` (`notes_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_events_notes_ibfk_2` FOREIGN KEY (`events_details_id`) REFERENCES `genea_events_details` (`events_details_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_events_sources`
  ADD CONSTRAINT `rel_events_sources_ibfk_1` FOREIGN KEY (`events_details_id`) REFERENCES `genea_events_details` (`events_details_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_events_sources_ibfk_2` FOREIGN KEY (`sour_citations_id`) REFERENCES `genea_sour_citations` (`sour_citations_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_events`
  ADD CONSTRAINT `rel_familles_events_ibfk_2` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_indi`
  ADD CONSTRAINT `rel_familles_indi_ibfk_1` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_familles_indi_ibfk_2` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_multimedia`
  ADD CONSTRAINT `rel_familles_multimedia_ibfk_2` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_familles_multimedia_ibfk_3` FOREIGN KEY (`media_id`) REFERENCES `genea_multimedia` (`media_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_notes`
  ADD CONSTRAINT `rel_familles_notes_ibfk_1` FOREIGN KEY (`notes_id`) REFERENCES `genea_notes` (`notes_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_familles_notes_ibfk_2` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_familles_sources`
  ADD CONSTRAINT `rel_familles_sources_ibfk_1` FOREIGN KEY (`familles_id`) REFERENCES `genea_familles` (`familles_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_familles_sources_ibfk_2` FOREIGN KEY (`sources_id`) REFERENCES `genea_sour_citations` (`sour_citations_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_attributes`
  ADD CONSTRAINT `rel_indi_attributes_ibfk_1` FOREIGN KEY (`events_details_id`) REFERENCES `genea_events_details` (`events_details_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_indi_attributes_ibfk_2` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_events`
  ADD CONSTRAINT `rel_indi_events_ibfk_1` FOREIGN KEY (`events_details_id`) REFERENCES `genea_events_details` (`events_details_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_indi_events_ibfk_2` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_multimedia`
  ADD CONSTRAINT `rel_indi_multimedia_ibfk_1` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_indi_multimedia_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `genea_multimedia` (`media_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_notes`
  ADD CONSTRAINT `rel_indi_notes_ibfk_1` FOREIGN KEY (`notes_id`) REFERENCES `genea_notes` (`notes_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_indi_notes_ibfk_2` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_indi_sources`
  ADD CONSTRAINT `rel_indi_sources_ibfk_2` FOREIGN KEY (`indi_id`) REFERENCES `genea_individuals` (`indi_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_indi_sources_ibfk_3` FOREIGN KEY (`sources_id`) REFERENCES `genea_sour_citations` (`sour_citations_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_multimedia_notes`
  ADD CONSTRAINT `rel_multimedia_notes_ibfk_1` FOREIGN KEY (`notes_id`) REFERENCES `genea_notes` (`notes_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_multimedia_notes_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `genea_multimedia` (`media_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_repo_notes`
  ADD CONSTRAINT `rel_repo_notes_ibfk_1` FOREIGN KEY (`notes_id`) REFERENCES `genea_notes` (`notes_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_repo_notes_ibfk_2` FOREIGN KEY (`repo_id`) REFERENCES `genea_repository` (`repo_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_sour_citations_multimedia`
  ADD CONSTRAINT `rel_sour_citations_multimedia_ibfk_1` FOREIGN KEY (`sour_citations_id`) REFERENCES `genea_sour_citations` (`sour_citations_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_sour_citations_multimedia_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `genea_multimedia` (`media_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><br />',$sql="ALTER TABLE `rel_sour_citations_notes`
  ADD CONSTRAINT `rel_sour_citations_notes_ibfk_1` FOREIGN KEY (`notes_id`) REFERENCES `genea_notes` (`notes_id`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `rel_sour_citations_notes_ibfk_2` FOREIGN KEY (`sour_citations_id`) REFERENCES `genea_sour_citations` (`sour_citations_id`) ON DELETE CASCADE ON UPDATE CASCADE;";
if(@mysql_query($sql))
	echo '<span class="ok">[OK]</span>';
else {
	echo '<span class="nok">[NOK]</span>';
	$db_error=1;
}

echo '<br /><hr /><br />';
if($db_error!=0)
{
	echo '<center><big><big><big>Erreur lors de la migration, aucune restauration complète possible</big></big></big></center>';
	$sql="ROLLBACK";
	@mysql_query($sql)?'':die(mysql_error());
}
else
{
	echo '<center><big><big><big>Migration effectuée avec succès</big></big></big></center>';
	$sql="COMMIT";
	@mysql_query($sql)?(print('<span class="ok">[OK]</span>')):die(mysql_error());
}

?>