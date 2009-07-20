SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `genea4p` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `genea4p`;

-- -----------------------------------------------------
-- Table `genea4p`.`genea_submitters`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_submitters` (
  `sub_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `sub_name` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sub_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
  `sub_addr` MEDIUMTEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `sub_city` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sub_stae` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sub_post` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sub_ctry` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sub_phon1` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sub_phon2` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sub_phon3` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sub_lang` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  PRIMARY KEY (`sub_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Liste des auteurs - NON UTILISE';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_infos`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_infos` (
  `id` MEDIUMINT(8) NOT NULL AUTO_INCREMENT ,
  `nom` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `descriptif` TINYTEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `subm` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `nom` (`nom` ASC) ,
  INDEX `subm` (`subm` ASC) ,
  CONSTRAINT `subm`
    FOREIGN KEY (`subm` )
    REFERENCES `genea4p`.`genea_submitters` (`sub_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `genea4p`.`agregats_noms`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`agregats_noms` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `base` MEDIUMINT(8) NOT NULL ,
  `lettre` TINYBLOB NOT NULL ,
  `nombre` SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0' ,
  `longueur` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `lettre` (`lettre`(5) ASC) ,
  INDEX `longueur` (`longueur` ASC) ,
  INDEX `agregats_base` (`base` ASC) ,
  CONSTRAINT `agregats_base`
    FOREIGN KEY (`base` )
    REFERENCES `genea4p`.`genea_infos` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Table de précalul pour l\'affichage de la liste des patronymes';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_address`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_address` (
  `addr_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `base` MEDIUMINT(8) NOT NULL ,
  `addr_addr` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `addr_city` VARCHAR(60) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_stae` VARCHAR(60) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_post` VARCHAR(10) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_ctry` VARCHAR(60) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_phon1` VARCHAR(25) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_phon2` VARCHAR(25) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_phon3` VARCHAR(25) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_email1` VARCHAR(120) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_email2` VARCHAR(120) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_email3` VARCHAR(120) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_fax1` VARCHAR(60) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_fax2` VARCHAR(60) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_fax3` VARCHAR(60) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_www1` VARCHAR(120) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_www2` VARCHAR(120) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_www3` VARCHAR(120) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  PRIMARY KEY (`addr_id`) ,
  INDEX `addr_base` (`base` ASC) ,
  CONSTRAINT `addr_base`
    FOREIGN KEY (`base` )
    REFERENCES `genea4p`.`genea_infos` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Table de stockage des adresses';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_individuals`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_individuals` (
  `indi_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `base` MEDIUMINT(8) NOT NULL ,
  `indi_nom` CHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `indi_prenom` CHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `indi_sexe` CHAR(1) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `indi_timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
  `indi_npfx` CHAR(30) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `indi_givn` CHAR(120) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `indi_nick` CHAR(30) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `indi_spfx` CHAR(30) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `indi_nsfx` CHAR(30) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `indi_resn` ENUM('locked','privacy','confidential') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  PRIMARY KEY (`indi_id`) ,
  INDEX `indi_nom` (`indi_nom` ASC) ,
  INDEX `indi_prenom` (`indi_prenom` ASC) ,
  INDEX `indi_base` (`base` ASC) ,
  CONSTRAINT `indi_base`
    FOREIGN KEY (`base` )
    REFERENCES `genea4p`.`genea_infos` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Table contenant les individus';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_cache_deps`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_cache_deps` (
  `indi_id` MEDIUMINT(8) UNSIGNED NOT NULL ,
  `indi_dep` MEDIUMINT(8) UNSIGNED NOT NULL ,
  PRIMARY KEY (`indi_id`, `indi_dep`) ,
  INDEX `indi_id` (`indi_id` ASC) ,
  INDEX `deps_indi_id` (`indi_dep` ASC) ,
  CONSTRAINT `indi_id`
    FOREIGN KEY (`indi_id` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `deps_indi_id`
    FOREIGN KEY (`indi_dep` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Liste des dépendances d\'un fichier de cache.';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_download`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_download` (
  `download_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `base` MEDIUMINT(8) NOT NULL ,
  `download_fichier` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `download_titre` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `download_description` TINYTEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `download_timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
  PRIMARY KEY (`download_id`) ,
  INDEX `download_base` (`base` ASC) ,
  CONSTRAINT `download_base`
    FOREIGN KEY (`base` )
    REFERENCES `genea4p`.`genea_infos` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Table contenant les fichiers à télécharger';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_place`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_place` (
  `place_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `place_lieudit` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `place_ville` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `place_cp` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `place_insee` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL ,
  `place_departement` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `place_region` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `place_pays` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `place_longitude` FLOAT NULL DEFAULT NULL ,
  `place_latitude` FLOAT NULL DEFAULT NULL ,
  `base` MEDIUMINT(8) NOT NULL ,
  PRIMARY KEY (`place_id`) ,
  INDEX `place_base` (`base` ASC) ,
  CONSTRAINT `place_base`
    FOREIGN KEY (`base` )
    REFERENCES `genea4p`.`genea_infos` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Liste des lieux géographiques de la généalogie';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_familles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_familles` (
  `familles_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `base` MEDIUMINT(8) NOT NULL COMMENT 'Champ à virer' ,
  `familles_wife` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL ,
  `familles_husb` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL ,
  `familles_timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
  `familles_resn` ENUM('locked','privacy') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  PRIMARY KEY (`familles_id`) ,
  INDEX `familles_base` (`base` ASC) ,
  INDEX `familles_husb` (`familles_husb` ASC) ,
  INDEX `familles_wife` (`familles_wife` ASC) ,
  CONSTRAINT `familles_base`
    FOREIGN KEY (`base` )
    REFERENCES `genea4p`.`genea_infos` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `familles_husb`
    FOREIGN KEY (`familles_husb` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `familles_wife`
    FOREIGN KEY (`familles_wife` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Table contenant les familles';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_events_details`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_events_details` (
  `events_details_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `place_id` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL ,
  `addr_id` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL ,
  `events_details_descriptor` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `events_details_gedcom_date` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `events_details_age` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `events_details_cause` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `jd_count` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL ,
  `jd_precision` TINYINT(3) UNSIGNED NULL DEFAULT NULL ,
  `jd_calendar` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `events_details_famc` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL ,
  `events_details_adop` VARCHAR(4) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `base` MEDIUMINT(8) NOT NULL ,
  `events_details_timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
  PRIMARY KEY (`events_details_id`) ,
  INDEX `events_place` (`place_id` ASC) ,
  INDEX `events_address` (`addr_id` ASC) ,
  INDEX `events_famc` (`events_details_famc` ASC) ,
  INDEX `events_base` (`base` ASC) ,
  CONSTRAINT `events_place`
    FOREIGN KEY (`place_id` )
    REFERENCES `genea4p`.`genea_place` (`place_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `events_address`
    FOREIGN KEY (`addr_id` )
    REFERENCES `genea4p`.`genea_address` (`addr_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `events_famc`
    FOREIGN KEY (`events_details_famc` )
    REFERENCES `genea4p`.`genea_familles` (`familles_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `events_base`
    FOREIGN KEY (`base` )
    REFERENCES `genea4p`.`genea_infos` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Tables contenant tous les évènements';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_membres`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_membres` (
  `id` TINYINT(3) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `email` VARCHAR(128) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `pass` VARCHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL DEFAULT '' ,
  `langue` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `theme` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `place` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `email` (`email` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Liste des utilisateurs du site';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_multimedia`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_multimedia` (
  `media_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `base` MEDIUMINT(8) NOT NULL ,
  `media_title` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `media_format` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `media_file` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `media_timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
  PRIMARY KEY (`media_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Liste des documents multimédia';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_notes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_notes` (
  `notes_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `base` MEDIUMINT(8) NOT NULL ,
  `notes_text` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `notes_timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
  PRIMARY KEY (`notes_id`) ,
  INDEX `notes_base` (`base` ASC) ,
  CONSTRAINT `notes_base`
    FOREIGN KEY (`base` )
    REFERENCES `genea4p`.`genea_infos` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Liste des notes';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_permissions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_permissions` (
  `permission_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `membre_id` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' ,
  `permission_type` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' ,
  `permission_value` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0' ,
  `base` MEDIUMINT(8) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`permission_id`) ,
  UNIQUE INDEX `unicite` (`membre_id` ASC, `permission_type` ASC, `permission_value` ASC, `base` ASC) ,
  INDEX `type` (`permission_type` ASC) ,
  INDEX `perm_base` (`base` ASC) ,
  CONSTRAINT `perm_membre`
    FOREIGN KEY (`membre_id` )
    REFERENCES `genea4p`.`genea_membres` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `perm_base`
    FOREIGN KEY (`base` )
    REFERENCES `genea4p`.`genea_infos` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Gestion des permissions des différents membres';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_refn`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_refn` (
  `refn_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `refn_num` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `refn_type` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  PRIMARY KEY (`refn_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Id de référence gedcom';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_repository`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_repository` (
  `repo_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `base` MEDIUMINT(8) NOT NULL ,
  `repo_name` VARCHAR(90) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `repo_rin` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `addr_id` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL ,
  `repo_timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
  PRIMARY KEY (`repo_id`) ,
  INDEX `repo_base` (`base` ASC) ,
  INDEX `repo_addr` (`addr_id` ASC) ,
  CONSTRAINT `repo_base`
    FOREIGN KEY (`base` )
    REFERENCES `genea4p`.`genea_infos` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `repo_addr`
    FOREIGN KEY (`addr_id` )
    REFERENCES `genea4p`.`genea_address` (`addr_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'liste des dépots d\'archives';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_sour_records`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_sour_records` (
  `sour_records_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `sour_records_auth` VARCHAR(248) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sour_records_title` MEDIUMTEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `sour_records_abbr` VARCHAR(60) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sour_records_publ` MEDIUMTEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `sour_records_agnc` VARCHAR(120) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sour_records_rin` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `repo_id` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL ,
  `repo_caln` VARCHAR(120) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `repo_medi` VARCHAR(15) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sour_records_timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
  `base` MEDIUMINT(8) NOT NULL ,
  PRIMARY KEY (`sour_records_id`) ,
  INDEX `record_base` (`base` ASC) ,
  INDEX `record_repo` (`repo_id` ASC) ,
  CONSTRAINT `record_base`
    FOREIGN KEY (`base` )
    REFERENCES `genea4p`.`genea_infos` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `record_repo`
    FOREIGN KEY (`repo_id` )
    REFERENCES `genea4p`.`genea_repository` (`repo_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Listes des sources ';


-- -----------------------------------------------------
-- Table `genea4p`.`genea_sour_citations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`genea_sour_citations` (
  `sour_citations_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `sour_records_id` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL ,
  `sour_citations_page` VARCHAR(248) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sour_citations_even` VARCHAR(15) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sour_citations_even_role` VARCHAR(15) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sour_citations_data_dates` VARCHAR(90) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sour_citations_data_text` MEDIUMTEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `sour_citations_quay` TINYINT(1) UNSIGNED NULL DEFAULT NULL ,
  `sour_citations_subm` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `sour_citations_timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
  `base` MEDIUMINT(8) NOT NULL ,
  PRIMARY KEY (`sour_citations_id`) ,
  INDEX `citations_record` (`sour_records_id` ASC) ,
  INDEX `base` (`base` ASC) ,
  CONSTRAINT `citations_record`
    FOREIGN KEY (`sour_records_id` )
    REFERENCES `genea4p`.`genea_sour_records` (`sour_records_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `base`
    FOREIGN KEY (`base` )
    REFERENCES `genea4p`.`genea_infos` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Listes des citations des sources';


-- -----------------------------------------------------
-- Table `genea4p`.`gns_ADM1`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`gns_ADM1` (
  `CC` VARCHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `ADM1` VARCHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '0' ,
  `name` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  PRIMARY KEY (`CC`, `ADM1`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Liste des régions des pays chargé dans la base de données';


-- -----------------------------------------------------
-- Table `genea4p`.`gns_CC`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`gns_CC` (
  `RC` DECIMAL(1,0) NOT NULL DEFAULT '0' ,
  `CC` CHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `import` INT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`RC`, `CC`) ,
  INDEX `CC` (`CC` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Liste des pays disponibles pour la base de données des lieu';


-- -----------------------------------------------------
-- Table `genea4p`.`gns_lieux`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`gns_lieux` (
  `RC` DECIMAL(1,0) NOT NULL DEFAULT '0' ,
  `UNI` DECIMAL(10,0) NOT NULL DEFAULT '0' ,
  `LAT` DECIMAL(10,7) NOT NULL DEFAULT '0.0000000' ,
  `LONGI` DECIMAL(11,7) NOT NULL DEFAULT '0.0000000' ,
  `UTM` VARCHAR(4) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `JOG` VARCHAR(7) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `CC1` VARCHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `ADM1` VARCHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `SORT_NAME` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `FULL_NAME` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  PRIMARY KEY (`UNI`) ,
  INDEX `RC` (`RC` ASC, `CC1` ASC, `ADM1` ASC) ,
  INDEX `CC1` (`CC1` ASC) ,
  INDEX `FULL_NAME` (`FULL_NAME` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Base de données de lieux géographiques';


-- -----------------------------------------------------
-- Table `genea4p`.`ign_rgc`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`ign_rgc` (
  `dep` TINYINT(4) UNSIGNED NOT NULL ,
  `com` SMALLINT(5) UNSIGNED NOT NULL ,
  `arrd` TINYINT(4) UNSIGNED NOT NULL ,
  `cant` TINYINT(4) UNSIGNED NOT NULL ,
  `admi` ENUM('1','2','3','4','5','6') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '6' ,
  `popu` MEDIUMINT(9) UNSIGNED NOT NULL ,
  `surface` MEDIUMINT(9) UNSIGNED NOT NULL ,
  `nom` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `xlamb2` MEDIUMINT(9) NOT NULL ,
  `ylamb2` MEDIUMINT(9) NOT NULL ,
  `xlambz` MEDIUMINT(9) NOT NULL ,
  `ylambz` MEDIUMINT(9) NOT NULL ,
  `xlamb93` MEDIUMINT(9) NOT NULL ,
  `ylamb93` MEDIUMINT(9) NOT NULL ,
  `longi_grd` MEDIUMINT(9) NOT NULL ,
  `lati_grd` MEDIUMINT(9) NOT NULL ,
  `longi_dms` MEDIUMINT(9) NOT NULL ,
  `lati_dms` MEDIUMINT(9) NOT NULL ,
  `zmin` SMALLINT(6) NOT NULL ,
  `zmax` SMALLINT(6) NOT NULL ,
  `zchl` SMALLINT(6) NOT NULL ,
  `carte` VARCHAR(6) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  INDEX `dep` (`dep` ASC, `nom` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `genea4p`.`insee_communes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`insee_communes` (
  `actual` SET('1','2','3','4','5','6') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `cheflieu` SET('0','1','2','3','4') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `cdc` SET('0','1','2') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `rang` TINYINT(3) UNSIGNED NOT NULL ,
  `reg` VARCHAR(3) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `dep` VARCHAR(3) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `com` MEDIUMINT(8) UNSIGNED NOT NULL ,
  `ar` TINYINT(3) UNSIGNED NOT NULL ,
  `ct` TINYINT(3) UNSIGNED NOT NULL ,
  `modif` TINYINT(1) NOT NULL ,
  `pole` VARCHAR(5) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `tncc` SET('0','1','2','3','4','5','6','7','8') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `artmaj` VARCHAR(5) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `ncc` VARCHAR(70) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `artmin` VARCHAR(5) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `nccenr` VARCHAR(70) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `articlct` VARCHAR(5) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `nccct` VARCHAR(70) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  INDEX `dep` (`dep` ASC) ,
  INDEX `com` (`com` ASC) ,
  INDEX `ncc` (`ncc` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `genea4p`.`rel_alias`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_alias` (
  `alias1` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `alias2` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`alias1`, `alias2`) ,
  INDEX `alias2` (`alias2` ASC) ,
  CONSTRAINT `alias1`
    FOREIGN KEY (`alias1` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `alias2`
    FOREIGN KEY (`alias2` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Stockage des alias des individus';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_asso_events`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_asso_events` (
  `events_details_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `indi_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `description` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  PRIMARY KEY (`indi_id`, `events_details_id`) ,
  INDEX `asso` (`indi_id` ASC) ,
  INDEX `event` (`events_details_id` ASC) ,
  CONSTRAINT `asso`
    FOREIGN KEY (`indi_id` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `event`
    FOREIGN KEY (`events_details_id` )
    REFERENCES `genea4p`.`genea_events_details` (`events_details_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'un témoin à un évènement';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_asso_familles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_asso_familles` (
  `indi_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `familles_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `description` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  PRIMARY KEY (`indi_id`, `familles_id`) ,
  INDEX `famille` (`familles_id` ASC) ,
  INDEX `indi` (`indi_id` ASC) ,
  CONSTRAINT `famille`
    FOREIGN KEY (`familles_id` )
    REFERENCES `genea4p`.`genea_familles` (`familles_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `indi`
    FOREIGN KEY (`indi_id` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'une relation non familiale à une famille';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_asso_indi`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_asso_indi` (
  `indi_id1` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `indi_id2` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `description` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  PRIMARY KEY (`indi_id1`, `indi_id2`) ,
  INDEX `indi1` (`indi_id1` ASC) ,
  INDEX `indi2` (`indi_id2` ASC) ,
  CONSTRAINT `indi1`
    FOREIGN KEY (`indi_id1` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `indi2`
    FOREIGN KEY (`indi_id2` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'une relation non familiale à un individu';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_events_multimedia`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_events_multimedia` (
  `media_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `events_details_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`events_details_id`, `media_id`) ,
  INDEX `media_id` (`media_id` ASC) ,
  CONSTRAINT `rel_events_multimedia_ibfk_5`
    FOREIGN KEY (`media_id` )
    REFERENCES `genea4p`.`genea_multimedia` (`media_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `rel_events_multimedia_ibfk_6`
    FOREIGN KEY (`events_details_id` )
    REFERENCES `genea4p`.`genea_events_details` (`events_details_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'un objet multimedia à un évènement';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_events_notes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_events_notes` (
  `events_details_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `notes_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`events_details_id`, `notes_id`) ,
  INDEX `note` (`notes_id` ASC) ,
  INDEX `event` (`events_details_id` ASC) ,
  CONSTRAINT `note`
    FOREIGN KEY (`notes_id` )
    REFERENCES `genea4p`.`genea_notes` (`notes_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `event`
    FOREIGN KEY (`events_details_id` )
    REFERENCES `genea4p`.`genea_events_details` (`events_details_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'une note à un évènement';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_events_sources`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_events_sources` (
  `events_details_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `sour_citations_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`events_details_id`, `sour_citations_id`) ,
  INDEX `event` (`events_details_id` ASC) ,
  INDEX `citation` (`sour_citations_id` ASC) ,
  CONSTRAINT `event`
    FOREIGN KEY (`events_details_id` )
    REFERENCES `genea4p`.`genea_events_details` (`events_details_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `citation`
    FOREIGN KEY (`sour_citations_id` )
    REFERENCES `genea4p`.`genea_sour_citations` (`sour_citations_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'une source à un évènement';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_familles_events`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_familles_events` (
  `events_details_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `familles_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `events_tag` VARCHAR(4) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `events_attestation` CHAR(1) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  PRIMARY KEY (`familles_id`, `events_details_id`) ,
  INDEX `famille` (`familles_id` ASC) ,
  INDEX `event` (`events_details_id` ASC) ,
  CONSTRAINT `famille`
    FOREIGN KEY (`familles_id` )
    REFERENCES `genea4p`.`genea_familles` (`familles_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `event`
    FOREIGN KEY (`events_details_id` )
    REFERENCES `genea4p`.`genea_events_details` (`events_details_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'un évènement  à une famille';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_familles_indi`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_familles_indi` (
  `indi_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `familles_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `rela_type` VARCHAR(10) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'birth' ,
  `rela_stat` VARCHAR(10) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  PRIMARY KEY (`familles_id`) ,
  INDEX `indi` (`indi_id` ASC) ,
  INDEX `famille` (`familles_id` ASC) ,
  CONSTRAINT `indi`
    FOREIGN KEY (`indi_id` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `famille`
    FOREIGN KEY (`familles_id` )
    REFERENCES `genea4p`.`genea_familles` (`familles_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'un enfant à une famille';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_familles_multimedia`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_familles_multimedia` (
  `media_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `familles_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`familles_id`, `media_id`) ,
  INDEX `famille` (`familles_id` ASC) ,
  INDEX `media` (`media_id` ASC) ,
  CONSTRAINT `famille`
    FOREIGN KEY (`familles_id` )
    REFERENCES `genea4p`.`genea_familles` (`familles_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `media`
    FOREIGN KEY (`media_id` )
    REFERENCES `genea4p`.`genea_multimedia` (`media_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'un objet multimedia à une famille';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_familles_notes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_familles_notes` (
  `notes_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `familles_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`familles_id`, `notes_id`) ,
  INDEX `note` (`notes_id` ASC) ,
  INDEX `famille` (`familles_id` ASC) ,
  CONSTRAINT `note`
    FOREIGN KEY (`notes_id` )
    REFERENCES `genea4p`.`genea_notes` (`notes_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `famille`
    FOREIGN KEY (`familles_id` )
    REFERENCES `genea4p`.`genea_familles` (`familles_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'une note à une famille';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_familles_sources`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_familles_sources` (
  `sour_citations_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `familles_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`familles_id`, `sour_citations_id`) ,
  INDEX `sources_id` (`sour_citations_id` ASC) ,
  CONSTRAINT `rel_familles_sources_ibfk_1`
    FOREIGN KEY (`familles_id` )
    REFERENCES `genea4p`.`genea_familles` (`familles_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `rel_familles_sources_ibfk_2`
    FOREIGN KEY (`sour_citations_id` )
    REFERENCES `genea4p`.`genea_sour_citations` (`sour_citations_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'une source à une famille';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_indi_attributes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_indi_attributes` (
  `events_details_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `indi_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `events_tag` VARCHAR(4) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `events_descr` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  PRIMARY KEY (`indi_id`, `events_details_id`) ,
  INDEX `event` (`events_details_id` ASC) ,
  INDEX `indi` (`indi_id` ASC) ,
  CONSTRAINT `event`
    FOREIGN KEY (`events_details_id` )
    REFERENCES `genea4p`.`genea_events_details` (`events_details_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `indi`
    FOREIGN KEY (`indi_id` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'un évènement à une famille';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_indi_events`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_indi_events` (
  `events_details_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `indi_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `events_tag` VARCHAR(4) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' ,
  `events_attestation` SET('Y') CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  PRIMARY KEY (`indi_id`, `events_details_id`) ,
  INDEX `event` (`events_details_id` ASC) ,
  INDEX `indi` () ,
  CONSTRAINT `event`
    FOREIGN KEY (`events_details_id` )
    REFERENCES `genea4p`.`genea_events_details` (`events_details_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `indi`
    FOREIGN KEY ()
    REFERENCES `genea4p`.`genea_individuals` ()
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'un évènement à une famille';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_indi_multimedia`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_indi_multimedia` (
  `media_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `indi_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`indi_id`, `media_id`) ,
  INDEX `indi` (`indi_id` ASC) ,
  INDEX `media` (`media_id` ASC) ,
  CONSTRAINT `rel_indi_multimedia_ibfk_1`
    FOREIGN KEY (`indi_id` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `rel_indi_multimedia_ibfk_2`
    FOREIGN KEY (`media_id` )
    REFERENCES `genea4p`.`genea_multimedia` (`media_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `indi`
    FOREIGN KEY (`indi_id` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `media`
    FOREIGN KEY (`media_id` )
    REFERENCES `genea4p`.`genea_multimedia` (`media_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'un objet multimedia à une personne';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_indi_notes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_indi_notes` (
  `notes_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `indi_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`indi_id`, `notes_id`) ,
  INDEX `note` (`notes_id` ASC) ,
  INDEX `indi` (`indi_id` ASC) ,
  CONSTRAINT `note`
    FOREIGN KEY (`notes_id` )
    REFERENCES `genea4p`.`genea_notes` (`notes_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `indi`
    FOREIGN KEY (`indi_id` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'association d\'une note à un individu';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_indi_refn`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_indi_refn` (
  `indi_id` MEDIUMINT(8) UNSIGNED NOT NULL ,
  `refn_id` MEDIUMINT(8) UNSIGNED NOT NULL ,
  PRIMARY KEY (`indi_id`, `refn_id`) ,
  INDEX `indi` (`indi_id` ASC) ,
  INDEX `refn` (`refn_id` ASC) ,
  CONSTRAINT `indi`
    FOREIGN KEY (`indi_id` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `refn`
    FOREIGN KEY (`refn_id` )
    REFERENCES `genea4p`.`genea_refn` (`refn_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_general_ci;


-- -----------------------------------------------------
-- Table `genea4p`.`rel_indi_sources`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_indi_sources` (
  `sour_citations_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `indi_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`indi_id`, `sour_citations_id`) ,
  INDEX `indi` (`indi_id` ASC) ,
  INDEX `citation` (`sour_citations_id` ASC) ,
  CONSTRAINT `indi`
    FOREIGN KEY (`indi_id` )
    REFERENCES `genea4p`.`genea_individuals` (`indi_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `citation`
    FOREIGN KEY (`sour_citations_id` )
    REFERENCES `genea4p`.`genea_sour_citations` (`sour_citations_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'une source à un individu';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_multimedia_notes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_multimedia_notes` (
  `notes_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `media_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`notes_id`, `media_id`) ,
  INDEX `note` (`notes_id` ASC) ,
  INDEX `media` (`media_id` ASC) ,
  CONSTRAINT `note`
    FOREIGN KEY (`notes_id` )
    REFERENCES `genea4p`.`genea_notes` (`notes_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `media`
    FOREIGN KEY (`media_id` )
    REFERENCES `genea4p`.`genea_multimedia` (`media_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'une note à un objet multimedia';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_place_notes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_place_notes` (
  `place_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `notes_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`notes_id`, `place_id`) ,
  INDEX `place` (`place_id` ASC) ,
  INDEX `note` (`notes_id` ASC) ,
  CONSTRAINT `place`
    FOREIGN KEY (`place_id` )
    REFERENCES `genea4p`.`genea_place` (`place_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `note`
    FOREIGN KEY (`notes_id` )
    REFERENCES `genea4p`.`genea_notes` (`notes_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'une note à un lieu';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_repo_notes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_repo_notes` (
  `notes_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `repo_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`notes_id`, `repo_id`) ,
  INDEX `note` (`notes_id` ASC) ,
  INDEX `repo` (`repo_id` ASC) ,
  CONSTRAINT `note`
    FOREIGN KEY (`notes_id` )
    REFERENCES `genea4p`.`genea_notes` (`notes_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `repo`
    FOREIGN KEY (`repo_id` )
    REFERENCES `genea4p`.`genea_repository` (`repo_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'une note à un dépot';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_sour_citations_multimedia`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_sour_citations_multimedia` (
  `sour_citations_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `media_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`sour_citations_id`, `media_id`) ,
  INDEX `citation` (`sour_citations_id` ASC) ,
  INDEX `media` (`media_id` ASC) ,
  CONSTRAINT `citation`
    FOREIGN KEY (`sour_citations_id` )
    REFERENCES `genea4p`.`genea_sour_citations` (`sour_citations_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `media`
    FOREIGN KEY (`media_id` )
    REFERENCES `genea4p`.`genea_multimedia` (`media_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'un objet multimedia à une source';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_sour_citations_notes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_sour_citations_notes` (
  `notes_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  `sour_citations_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`notes_id`, `sour_citations_id`) ,
  INDEX `note` (`notes_id` ASC) ,
  INDEX `citation` (`sour_citations_id` ASC) ,
  CONSTRAINT `note`
    FOREIGN KEY (`notes_id` )
    REFERENCES `genea4p`.`genea_notes` (`notes_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `citation`
    FOREIGN KEY (`sour_citations_id` )
    REFERENCES `genea4p`.`genea_sour_citations` (`sour_citations_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'une note à un objet sour_citation';


-- -----------------------------------------------------
-- Table `genea4p`.`rel_sour_records_notes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `genea4p`.`rel_sour_records_notes` (
  `notes_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0' ,
  `sour_records_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`notes_id`, `sour_records_id`) ,
  INDEX `note` (`notes_id` ASC) ,
  INDEX `record` (`sour_records_id` ASC) ,
  CONSTRAINT `note`
    FOREIGN KEY (`notes_id` )
    REFERENCES `genea4p`.`genea_notes` (`notes_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `record`
    FOREIGN KEY (`sour_records_id` )
    REFERENCES `genea4p`.`genea_sour_records` (`sour_records_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'Association d\'une note à un objet sour_record';



-- -----------------------------------------------------
-- Data for table `genea4p`.`genea_membres`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `genea4p`;
INSERT INTO `genea_membres` (`id`, `email`, `pass`, `langue`, `theme`, `place`) VALUES (1, 'Annonyme', '', '', '', '');
INSERT INTO `genea_membres` (`id`, `email`, `pass`, `langue`, `theme`, `place`) VALUES (2, 'genealogie@parois.net', '1e7a9b9d1ffe72647deca21875d60d7a', '', '', '');

COMMIT;

-- -----------------------------------------------------
-- Data for table `genea4p`.`genea_permissions`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
USE `genea4p`;
INSERT INTO `genea_permissions` (`permission_id`, `membre_id`, `permission_type`, `permission_value`, `base`) VALUES (1, 2, 1, 1, 0);
INSERT INTO `genea_permissions` (`permission_id`, `membre_id`, `permission_type`, `permission_value`, `base`) VALUES (2, 2, 2, 1, 0);
INSERT INTO `genea_permissions` (`permission_id`, `membre_id`, `permission_type`, `permission_value`, `base`) VALUES (3, 2, 3, 1, 0);
INSERT INTO `genea_permissions` (`permission_id`, `membre_id`, `permission_type`, `permission_value`, `base`) VALUES (4, 2, 4, 1, 0);
INSERT INTO `genea_permissions` (`permission_id`, `membre_id`, `permission_type`, `permission_value`, `base`) VALUES (5, 2, 5, 1, 0);
INSERT INTO `genea_permissions` (`permission_id`, `membre_id`, `permission_type`, `permission_value`, `base`) VALUES (6, 2, 6, 1, 0);
INSERT INTO `genea_permissions` (`permission_id`, `membre_id`, `permission_type`, `permission_value`, `base`) VALUES (7, 2, 7, 1, 0);
INSERT INTO `genea_permissions` (`permission_id`, `membre_id`, `permission_type`, `permission_value`, `base`) VALUES (8, 2, 8, 1, 0);
INSERT INTO `genea_permissions` (`permission_id`, `membre_id`, `permission_type`, `permission_value`, `base`) VALUES (9, 2, 9, 1, 0);
INSERT INTO `genea_permissions` (`permission_id`, `membre_id`, `permission_type`, `permission_value`, `base`) VALUES (10, 2, 10, 1, 0);
INSERT INTO `genea_permissions` (`permission_id`, `membre_id`, `permission_type`, `permission_value`, `base`) VALUES (11, 2, 11, 1, 0);
INSERT INTO `genea_permissions` (`permission_id`, `membre_id`, `permission_type`, `permission_value`, `base`) VALUES (12, 2, 12, 1, 0);
INSERT INTO `genea_permissions` (`permission_id`, `membre_id`, `permission_type`, `permission_value`, `base`) VALUES (13, 2, 13, 1, 0);
INSERT INTO `genea_permissions` (`permission_id`, `membre_id`, `permission_type`, `permission_value`, `base`) VALUES (14, 2, 14, 1, 0);

COMMIT;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
