<?php
 /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 **
 *Copyright (C) 2004PAROIS Pascal *
 **
 *This program is free software; you can redistribute it and/or modify*
 *it under the terms of the GNU General Public License as published by*
 *the Free Software Foundation; either version 2 of the License, or *
 *(at your option) any later version. *
 **
 *This program is distributed in the hope that it will be useful, *
 *but WITHOUT ANY WARRANTY; without even the implied warranty of*
 *MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the *
 *GNU General Public License for more details.*
 **
 *You should have received a copy of the GNU General Public License *
 *along with this program; if not, write to the Free Software *
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA02111-1307 USA *
 **
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'p_conf/script_start.php');
	
$_GET['q']='%'.str_replace(' ','%',$_GET['q']).'%';
$g4p_query=$g4p_mysqli->g4p_query("SELECT indi_id, indi_prenom, indi_nom 
    FROM genea_individuals 
    WHERE CONCAT(indi_prenom,' ',indi_nom) LIKE '".$_GET['q']."' OR
    CONCAT(indi_nom,' ',indi_prenom) LIKE '".$_GET['q']."' 
    ORDER BY indi_prenom, indi_nom
    LIMIT 40   ");
if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
{
    foreach($g4p_result as $g4p_a_result)
    {
        echo $g4p_a_result['indi_id'].'  '.$g4p_a_result['indi_prenom'].'  '.$g4p_a_result['indi_nom']."\n";
    }
}

/*   $sql="INSERT INTO genea_individuals (indi_nom, indi_prenom, indi_sexe, base, indi_npfx, indi_givn, indi_nick, indi_spfx, indi_nsfx) VALUES (
         '".mysql_escape_string($_POST['g4p_nom'])."', '".mysql_escape_string($_POST['g4p_prenom'])."', 
         '".mysql_escape_string($_POST['g4p_sexe'])."',".(int)$_POST['g4p_base'].",
         '".mysql_escape_string($_POST['npfx'])."', '".mysql_escape_string($_POST['givn'])."', 
         '".mysql_escape_string($_POST['nick'])."', '".mysql_escape_string($_POST['spfx'])."', 
         '".mysql_escape_string($_POST['nsfx'])."')";
    if($g4p_query=$g4p_mysqli->g4p_query($sql))
      $_SESSION['message']='Requète éffectuée avec succès';
    else
      $_SESSION['message']='Erreur lors de la requète';
    $g4p_last_id=$g4p_mysqli->insert_id;

    $g4p_indi=g4p_load_indi_infos($g4p_last_id);
*/



?>
