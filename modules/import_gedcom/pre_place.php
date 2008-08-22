<?

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

if (!isset ($_SESSION['import_gedcom'])) {
	$_SESSION['message'] = 'Aucun import gedcom en cours';
	header('location:' . g4p_make_url('modules/import_gedcom', 'import_gedcom.php', ''));
}

$n = count($g4p_config['subdivision']);

include ($g4p_chemin . 'entete.php');
echo '<h2>', $g4p_langue['import_ged_titre'], '</h2><div class="cadre">';

if(!isset($_GET['action']))
	$_GET['action'] = '';

if (isset ($_POST['continue']) || $_GET['action']=='nextSetPlacePosition') {
	if (isset ($_POST['continue'])) 
		for ($i = 0; $i < $n; $i++) {
			if ($_POST[$i] != '-1')
				$position[$_POST[$i]] = $i;
		}
	if($_GET['action']=='nextSetPlacePosition')
		$res = $_SESSION['import_gedcom']->restartCut('placePosition');
	else
		$res = $_SESSION['import_gedcom']->setPlacePosition($position);
	
	if($res=='cut') {
		echo '<a href="'.g4p_make_url('modules/import_gedcom', 'pre_place.php', 'action=nextSetPlacePosition').'">Continuer l\'enregistrement des lieux</a>';
		echo ' ('.(int) ($_SESSION['import_gedcom']->length * 100 / filesize($_SESSION['import_gedcom']->filename)) . '% effectu&eacute;)';
		echo '</div>';
		require_once ($g4p_chemin . 'pied_de_page.php');
		exit;
	}
	// redirection vers le script d'analyse
	echo '<a href="' . g4p_make_url('modules/import_gedcom', 'import_gedcom.php', 'action=analyse') . '">D&eacute;marer l\'analyse ...</a><br/>';
	echo '</div>';
	require_once ($g4p_chemin . 'pied_de_page.php');
	exit;
}

if($_GET['action']=='next')
	$res = $_SESSION['import_gedcom']->restartCut('nbBlocs');
else
	$res = $_SESSION['import_gedcom']->loadNbBlocs();
	
echo '<h3>Selection de l\'organisation des lieux</h3>';
if($res=='cut') {
	echo '<a href="'.g4p_make_url('modules/import_gedcom', 'pre_place.php', 'action=next').'">Continuer la pré-lecture</a>';
	echo ' ('.(int) ($_SESSION['import_gedcom']->length * 100 / filesize($_SESSION['import_gedcom']->filename)) . '% effectu&eacute;)';
	echo '</div>';
	require_once ($g4p_chemin . 'pied_de_page.php');
	exit;
}
?>
<FORM method="post" name="select_place" action="pre_place.php">
	<TABLE  width="100%">
  <?

	echo '<TR>';
	for ($i = 0; $i < $n; $i++) {
		echo '<TH width="11%"><SELECT name="' . $i . '" size="1" style="align:center;width:100%">';
		echo '<OPTION value="-1">--</OPTION>';
		foreach ($g4p_config['subdivision'] as $sub)
			echo '<OPTION value="' . $sub . '">' . $g4p_langue[$sub] . '</OPTION>';
		echo '</SELECT></TH>';
	}
	echo '</TR>';
	foreach ($_SESSION['import_gedcom']->demoPlace as $place) {
		echo '<TR>';
		$place = explode(',', $place);
		for ($i = 0; $i < $n; $i++)
			echo '<TD style="border-bottom: 1px dotted black;">' . ((trim(isset ($place[$i]))) ? $place[$i] : '') . '</TD>';
			
		echo '</TR>';
	}
	$_SESSION['import_gedcom']->demoPlace = null;
?>
	</TABLE>
  <INPUT type="submit" name="continue" value="Continuer"/>
</FORM>

<?

echo '</div>';
require_once ($g4p_chemin . 'pied_de_page.php');
?>
