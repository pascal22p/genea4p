<?php


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*                                                                          *
*  Copyright (C) 2006 DEQUIDT Davy, FEDERBE Arnaud                         *
*                                                                          *
*  This program is free software; you can redistribute it and/or modify    *
*  it under the terms of the GNU General Public License as published by    *
*  the Free Software Foundation; either version 2 of the License, or       *
*  (at your option) any later version.                                     *
*                                                                          *
*  This program is distribuited in the hope that it will be useful,        *
*  but WITHOUT ANY WARRANTY; without even the implied warranty of          *
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           *
*  GNU General Public License for more details.                            *
*                                                                          *
*  You should have received a copy of the GNU General Public License       *
*  along with this program; if not, write to the Free Software             *
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA *
*                                                                          *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *            Analyse grammaire gedcom 5.5 / import ds la BD               *
 *                                                                         *
 * dernière mise à jour : 13/04/2006                                       *
 * En cas de problème : http://genea4p.espace.fr.to                        *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin = '../../';
// $g4p_chemin='modules/genea4p/';

include ($g4p_chemin . 'p_conf/g4p_config.php');
require_once ("class_importGedcom.php");
include ($g4p_chemin . 'p_conf/script_start.php');

$time_start = g4p_getmicrotime();

/**
 * Controle des droits 
 */

if (!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_]) {
	if (!$_SESSION['permission']->permission[_PERM_ADMIN_]) {
		$_SESSION['message'] = 'Vous n\'avez pas les autorisations requises pour importer un GEDCOM';
		header('location:' . g4p_make_url('', 'index.php', '', 0));
		exit;
	} else
		$g4p_simple_admin = 1;
} else
	$g4p_simple_admin = 0;

if (empty ($_GET['action'])) {
	if(isset($_SESSION['import_gedcom']))
		$_SESSION['import_gedcom']->annulation();
	$_SESSION['import_gedcom']=null;
}

if (!isset ($_SESSION['import_gedcom'])) {
	if (!isset ($_POST['g4p_gedcom_fichier']) && empty ($_FILES)) {
		include ($g4p_chemin . 'entete.php');
		echo '<h2>', $g4p_langue['import_ged_titre'], '</h2>
						    <div class="cadre">', $g4p_langue['import_ged_explications'], '
						    <form class="formulaire" method="post" enctype="multipart/form-data" action="', g4p_make_url('modules/import_gedcom', 'import_gedcom.php', ''), '">';
		if ($g4p_simple_admin) {
			if (file_exists($g4p_chemin . 'gedcoms/gedcom_' . $_SESSION['g4p_id_membre'] . '.ged')) {
				echo '<label for="upload_gedcom">Votre gedcom&nbsp;: </label><input name="g4p_upload_gedcom" type="file" /><br /><br />';
				echo '<select name="g4p_gedcom_fichier">';
				echo '<option value="gedcom_' . $_SESSION['g4p_id_membre'] . '.ged">Votre gedcom précédent</option>';
				echo '</select>';
			} else {
				echo '<label for="g4p_upload_gedcom">Votre gedcom&nbsp;: </label><input name="g4p_upload_gedcom" type="file" /><br />';
			}
		} else {
			echo '<select name="g4p_gedcom_fichier">';
			$g4p_rep = opendir($g4p_chemin . 'gedcoms');
			while ($g4p_fichier = readdir($g4p_rep)) {
				if (is_file($g4p_chemin . 'gedcoms/' . $g4p_fichier) and eregi('.ged$', $g4p_fichier))
					echo '<option value="' . $g4p_fichier . '">' . $g4p_fichier . '</option>';
			}
			echo '</select>';
		}
		echo '<br/><br/>Type de journalisation de l\'import : <select size="1" name="log">';
		echo '<option value="err"> Erreur</option>';
		echo '<option value="warn"> Avertissement</option>';
		echo '<option value="debug"> Debugage</option>';
		echo '</select><br/><br/><input type="submit" value="', $g4p_langue['import_ged_submit'], '" /></form></div>';
		require_once ($g4p_chemin . 'pied_de_page.php');
		exit;
	}
	//si upload, on traite
	if (!empty ($_FILES) and $_FILES['g4p_upload_gedcom']['error'] != 4) {
		if ($_FILES['g4p_upload_gedcom']['error'] == 0) {
			//windows est trop con et me mets application/octet-stream comme type mime, je me rabat sur l'extension
			if (strtolower(substr($_FILES['g4p_upload_gedcom']['name'], -3)) != 'ged') {
				$_SESSION['message'] = $g4p_langue['message_type_fichier_refuse'];
				header('location:' . g4p_make_url('modules/import_gedcom', 'import_gedcom.php', ''));
				exit;
			}
			if (!@ move_uploaded_file($_FILES['g4p_upload_gedcom']['tmp_name'], $g4p_chemin . 'gedcoms/gedcom_' . $_SESSION['g4p_id_membre'] . '.ged')) {
				$_SESSION['message'] = $g4p_langue['message_copie_imp'] . ' : ' . $_FILES['g4p_upload_gedcom']['tmp_name'] . ' vers ' . $g4p_chemin . 'gedcoms/gedcom_' . $_SESSION['g4p_id_membre'] . '.ged';
				header('location:' . g4p_make_url('modules/import_gedcom', 'import_gedcom.php', ''));
				exit;
			}
			$_POST['g4p_gedcom_fichier'] = 'gedcom_' . $_SESSION['g4p_id_membre'] . '.ged';
		}
		elseif ($_FILES['g4p_upload_gedcom']['error'] == 1 or $_FILES['g4p_upload_gedcom']['size'] > $g4p_config['gedcom_upload_maxsize']) {
			$_SESSION['message'] = 'Fichier trop gros, sa taille est supérieure à ' . $g4p_config['gedcom_upload_maxsize'] . ' octets';
			header('location:' . g4p_make_url('modules/import_gedcom', 'import_gedcom.php', ''));
			exit;
		} else {
			$_SESSION['message'] = 'Erreur lors de l\'upload';
			header('location:' . g4p_make_url('modules/import_gedcom', 'import_gedcom.php', ''));
			exit;
		}
	}
	$_SESSION['import_gedcom'] = new importGedcom($g4p_chemin .'gedcoms/'.$_POST['g4p_gedcom_fichier'], $_SESSION['genea_db_id'],$_POST['log']);
	header('location:' . g4p_make_url('modules/import_gedcom', 'pre_place.php', ''));
	exit;
}


include ($g4p_chemin . 'entete.php');
echo '<h2>', $g4p_langue['import_ged_titre'], '</h2><div class="cadre">';
if ($_GET['action']=='analyse') {
	echo '<h3>Analyse</h3>';
	$res = $_SESSION['import_gedcom']->analyse();

	$_SESSION['import_gedcom_temps']= time() - $_SESSION['import_gedcom']->dateStart;
	
	echo (int) ($_SESSION['import_gedcom']->length * 100 / filesize($_SESSION['import_gedcom']->filename)) . '% analys&eacute; en ';
	echo $_SESSION['import_gedcom_temps']. ' s<br/>';

	if ($res == 'cut')
		echo '<a href="?action=next"><b>Continuer l\'analyse ...</b></a>';
	else
		echo 'Analyse termin&eacute;e.<br/><a href="?action=lien"><b>Inserer les liens ...</b></a>';
	echo '<h3>Journal d\'import</h3>';
	echo '<textarea style="width:99%;overflow:visible" rows="20" readonly="readonly">';
	include($_SESSION['import_gedcom']->log_filename);
	echo '</textarea>';
} else
	if ($_GET['action'] == 'next') {
		echo '<h3>Analyse</h3>';
		$res = $_SESSION['import_gedcom']->restartCut();

		$_SESSION['import_gedcom_temps'] += time() - $_SESSION['import_gedcom']->dateStart;
		
		echo (int) ($_SESSION['import_gedcom']->length * 100 / filesize($_SESSION['import_gedcom']->filename)) . '% analys&eacute; en ';
		echo $_SESSION['import_gedcom_temps']. ' s<br/>';
	
		if ($res == 'cut')
			echo '<a href="?action=next"><b>Continuer l\'analyse ...</b></a>';
		else
			echo 'Analyse termin&eacute;e.<br/><a href="?action=lien"><b>Inserer les liens ...</b></a>';
			echo '<h3>Journal d\'import</h3>';
			echo '<textarea style="width:99%;overflow:visible" rows="20" readonly="readonly">';
			include($_SESSION['import_gedcom']->log_filename);
			echo '</textarea>';
	} else
		if ($_GET['action'] == 'lien') {
			echo '<h3>Création des liens</h3>';

			if($_SESSION['import_gedcom']->insertionLien()=='cut')
			{
				echo 'Analyse termin&eacute;e.<br/><a href="?action=lien"><b>Continuer l\'insertion des liens ...</b></a>';
				echo '<h3>Journal d\'import</h3>';
				echo '<textarea style="width:99%;overflow:visible" rows="20" readonly="readonly">';
				include($_SESSION['import_gedcom']->log_filename);
				echo '</textarea>';
			}
			else
			{
				$_SESSION['import_gedcom_temps']= time() - $_SESSION['import_gedcom']->dateStart;
	
				echo 'Analyse termin&eacute;e.<br/>';
				echo 'Insertion des liens effectu&eacute;e en ';
				echo $_SESSION['import_gedcom_temps']. ' s';
				if($_SESSION['import_gedcom']->nb_err_lien > 0)
					echo ' ('.$_SESSION['import_gedcom']->nb_err_lien.' liens erronés)';
				echo '.<br/><a href="?action=index"><b>Cr&eacute;er l\'index des noms ...</b></a>';
				echo '<h3>Journal d\'import</h3>';
				echo '<textarea style="width:99%;overflow:visible" rows="20" readonly="readonly">';
				include($_SESSION['import_gedcom']->log_filename);
				echo '</textarea>';
			}
	} else
		if ($_GET['action'] == 'index') {
			echo '<h3>Création de l\'index des noms</h3>';
			g4p_agregat_noms();
			echo 'Analyse termin&eacute;e.<br/>';
			echo 'Insertion des liens termin&eacute;e.<br/>';
			echo 'Cr&eacute;ation de l\'index des noms termin&eacute;e.<br/>';
			echo '<b>Import du fichier effectu&eacute; avec succ&egrave;s.</b>';
			
			echo '<h3>Journal d\'import</h3>';
			echo '<textarea style="width:99%;overflow:visible" rows="20" readonly="readonly">';
			include($_SESSION['import_gedcom']->log_filename);
			echo '</textarea>';

			$_SESSION['import_gedcom']=null;
			unset($_SESSION['import_gedcom']);
		}
echo '<br/>';

echo '</div>';
require_once ($g4p_chemin . 'pied_de_page.php');
?>
