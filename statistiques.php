<?php
 /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                                                          *
 *  Copyright (C) 2004  PAROIS Pascal                                       *
 *                                                                          *
 *  This program is free software; you can redistribute it and/or modify    *
 *  it under the terms of the GNU General Public License as published by    *
 *  the Free Software Foundation; either version 2 of the License, or       *
 *  (at your option) any later version.                                     *
 *                                                                          *
 *  This program is distributed in the hope that it will be useful,         *
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
 *                         Quelque chiffres                                *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

include($g4p_chemin.'p_conf/g4p_config.php');
include($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
include($g4p_chemin.'entete.php');

echo '<div class="box_title"><h2>',sprintf($g4p_langue['stats_titre'],$_SESSION['genea_db_nom']),'</h2></div>';
echo '<ul>';
$sql="SELECT COUNT(*) AS cpt FROM genea_individuals WHERE base=".$_SESSION['genea_db_id'];
if($g4p_result=$g4p_mysqli->g4p_query($sql))
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    echo '<li>',$g4p_langue['stats_nb_indi'],$g4p_result[0]['cpt'],'</li>';

$sql="SELECT COUNT(*) AS cpt FROM rel_indi_events 
    LEFT JOIN genea_individuals USING (indi_id) 
    WHERE base=".$_SESSION['genea_db_id'];
if($g4p_result=$g4p_mysqli->g4p_query($sql))
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    echo '<li>',$g4p_langue['stats_nb_ievent'],$g4p_result[0]['cpt'],'</li>';

$sql="SELECT COUNT(*) AS cpt FROM rel_indi_attributes 
        LEFT JOIN genea_individuals USING (indi_id) 
        WHERE base=".$_SESSION['genea_db_id'];
if($g4p_result=$g4p_mysqli->g4p_query($sql))
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    echo '<li>Nombre d\'attributs : ',$g4p_result[0]['cpt'],'<li/>';

$sql="SELECT COUNT(*) AS cpt FROM genea_familles
        WHERE base=".$_SESSION['genea_db_id'];
if($g4p_result=$g4p_mysqli->g4p_query($sql))
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    echo '<li>Nombre de familles : ',$g4p_result[0]['cpt'],'<li/>';

$sql="SELECT COUNT(*) AS cpt FROM rel_familles_events 
    LEFT JOIN genea_familles USING (familles_id) 
    WHERE base=".$_SESSION['genea_db_id'];
if($g4p_result=$g4p_mysqli->g4p_query($sql))
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    echo '<li>',$g4p_langue['stats_nb_fevent'],$g4p_result[0]['cpt'],'<li/>';

$sql="SELECT COUNT(*) as cpt FROM genea_sour_citations WHERE base=".$_SESSION['genea_db_id'];
if($g4p_result=$g4p_mysqli->g4p_query($sql))
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    echo '<li>',$g4p_langue['stats_nb_sour'],$g4p_result[0]['cpt'],'</li>';

$sql="SELECT COUNT(*) as cpt FROM genea_sour_records WHERE base=".$_SESSION['genea_db_id'];
if($g4p_result=$g4p_mysqli->g4p_query($sql))
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    echo '<li>',$g4p_langue['stats_nb_sour'],$g4p_result[0]['cpt'],'</li>';

$sql="SELECT COUNT(*) as cpt FROM genea_notes WHERE base=".$_SESSION['genea_db_id'];
if($g4p_result=$g4p_mysqli->g4p_query($sql))
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    echo '<li>',$g4p_langue['stats_nb_note'],$g4p_result[0]['cpt'],'</li>';

$sql="SELECT COUNT(*) as cpt FROM genea_multimedia WHERE base=".$_SESSION['genea_db_id'];
if($g4p_result=$g4p_mysqli->g4p_query($sql))
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    echo '<li>',$g4p_langue['stats_nb_media'],$g4p_result[0]['cpt'],'</li>';
          
$sql="SELECT familles_id, COUNT(*) as cpt
           FROM rel_familles_indi
           LEFT JOIN genea_individuals USING(indi_id)
           WHERE genea_individuals.base=".$_SESSION['genea_db_id']." GROUP BY familles_id ORDER BY cpt DESC LIMIT 10";
if($g4p_result=$g4p_mysqli->g4p_query($sql))
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    foreach($g4p_result as $tmp)
      $g4p_liste_famille[]=$tmp['familles_id'];

echo '</ul>';

if(!empty($g4p_liste_famille))
{
	$g4p_liste_famille=implode(',',$g4p_liste_famille);
	$sql="SELECT familles_id, husb.indi_id as husb_id, husb.indi_nom as husb_nom, husb.indi_prenom as husb_prenom, wife.indi_nom as wife_nom,
		wife.indi_prenom as wife_prenom
		FROM genea_familles
		LEFT JOIN genea_individuals AS husb ON (husb.indi_id=familles_husb)
		LEFT JOIN genea_individuals AS wife ON (wife.indi_id=familles_wife)
		WHERE familles_id IN (".$g4p_liste_famille.")";        
	if($g4p_result2=$g4p_mysqli->g4p_query($sql))
	{
		if($g4p_result2=$g4p_mysqli->g4p_result($g4p_result2,'familles_id'))
		{
			echo '<h3>',$g4p_langue['stats_familles'],'</h3>';
			echo '<ul>';
			foreach($g4p_result as $g4p_a_result)
				echo '<li><a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&id_pers='.$g4p_result2[$g4p_a_result['familles_id']]['husb_id'],'fiche-'.g4p_prepare_varurl($g4p_result2[$g4p_a_result['familles_id']]['husb_nom']).'-'.g4p_prepare_varurl($g4p_result2[$g4p_a_result['familles_id']]['husb_prenom']).'-'.$g4p_result2[$g4p_a_result['familles_id']]['husb_id']),'">',$g4p_result2[$g4p_a_result['familles_id']]['husb_nom'],' ',$g4p_result2[$g4p_a_result['familles_id']]['husb_prenom'],' - ',$g4p_result2[$g4p_a_result['familles_id']]['wife_nom'],' ',$g4p_result2[$g4p_a_result['familles_id']]['wife_prenom'],'</a> : ',sprintf($g4p_langue['stats_x_enfants'],$g4p_a_result['cpt']),'<li/>';
			echo '</ul>';
		}
	}
}

$sql="SELECT indi_nom, COUNT(*) AS cpt FROM genea_individuals WHERE base=".$_SESSION['genea_db_id']." GROUP BY indi_nom ORDER BY cpt DESC LIMIT 10";
if($g4p_result=$g4p_mysqli->g4p_query($sql))
{
	if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
	{
		echo '<h3>',$g4p_langue['stats_patronymes'],'</h3>';
		echo '<ul>';
		foreach($g4p_result as $g4p_a_result)
			echo '<a href="',g4p_make_url('','index.php','g4p_action=liste_prenom&id_nom='.rawurlencode($g4p_a_result['indi_nom']).'&g4p_nbre='.$g4p_a_result['cpt'],'prenoms-'.rawurlencode($g4p_a_result['indi_nom']).'-'.$g4p_a_result['cpt']),'">',$g4p_a_result['indi_nom'],'</a> : ',sprintf($g4p_langue['stats_x_pers'],$g4p_a_result['cpt']),'<br />';
		echo '</ul>';
	}
}

$sql="SELECT count(  *  )  as cpt
           FROM rel_familles_indi
           LEFT JOIN genea_individuals USING (indi_id)
           WHERE genea_individuals.base =".$_SESSION['genea_db_id']."
           GROUP  BY familles_id ";
if($g4p_result=$g4p_mysqli->g4p_query($sql))
{
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
  {
    $i=0;
    echo '<h3>',$g4p_langue['stats_nb_enfants'],'</h3>';
    foreach($g4p_result as $g4p_a_result)
      $i+=$g4p_a_result['cpt'];
    echo '<ul><li>',round($i/count($g4p_result),2),'</li></ul>';
  }
}

include($g4p_chemin.'pied_de_page.php');
?>
