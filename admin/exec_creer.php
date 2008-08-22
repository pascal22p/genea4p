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
 *          Créations des individus et des familles                        *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

If(!isset($_GET['g4p_opt']) or !$_SESSION['permission']->permission[_PERM_EDIT_FILES_])
{
  echo 'bouhh';
}
else
{
  switch($_GET['g4p_opt'])
  {
    case 'ajout_indi':
    $_POST['g4p_nom']=strtoupper($_POST['g4p_nom']);

    $sql="INSERT INTO genea_individuals (indi_nom, indi_prenom, indi_sexe, indi_chan, base, npfx, givn, nick, spfx, surn, nsfx) VALUES (
         '".mysql_escape_string($_POST['g4p_nom'])."', '".mysql_escape_string($_POST['g4p_prenom'])."', '".mysql_escape_string($_POST['g4p_sexe'])."',NOW(),".$_SESSION['genea_db_id'].",
         '".mysql_escape_string($_POST['npfx'])."', '".mysql_escape_string($_POST['givn'])."', '".mysql_escape_string($_POST['nick'])."', '".mysql_escape_string($_POST['spfx'])."', '".mysql_escape_string($_POST['surn'])."', '".mysql_escape_string($_POST['nsfx'])."')";
    if($g4p_last_id=g4p_db_query($sql))
      $_SESSION['message']='Requète éffectuée avec succès';
    else
      $_SESSION['message']='Erreur lors de la requète';

    $g4p_indi=g4p_load_indi_infos($g4p_last_id);
    g4p_update_agregat_noms($_POST['g4p_nom']);
    header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_fich',0));
    break;

    case 'ajout_fam':
    if(!empty($_POST['femme2']))
      $_POST['femme']=$_POST['femme2'];
    if(!empty($_POST['mari2']))
      $_POST['mari']=$_POST['mari2'];

    if(!$_POST['femme'] && !$_POST['mari'])
    {
      $_SESSION['message']='Veuillez remplir au moins un des champs';
      header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_fams&g4p_id='.$g4p_last_id,0,0));
      break;
    }
    
    
    if(!$_POST['femme'])
      $_POST['femme']='NULL';
    if(!$_POST['mari'])
      $_POST['mari']='NULL';

    $sql="INSERT INTO genea_familles (familles_husb , familles_wife, familles_chan, base) VALUES (".mysql_escape_string($_POST['mari']).", ".mysql_escape_string($_POST['femme']).", NOW(), ".$_SESSION['genea_db_id'].")";
    if($g4p_last_id=g4p_db_query($sql))
      $_SESSION['message']='Requète éffectuée avec succès';
    else
      $_SESSION['message']='Erreur lors de la requète';

    g4p_destroy_cache($_POST['femme']);
    g4p_destroy_cache($_POST['mari']);
    
    /* Pourquoi changer d'individu ?
    unset($g4p_indi);
    if(!empty($_POST['mari']))
      $g4p_indi=g4p_load_indi_infos($_POST['mari']);
    else
      $g4p_indi=g4p_load_indi_infos($_POST['femme']);
    */
    header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_fams&g4p_id='.$g4p_last_id,0,0));
    break;

    default:
    echo 'fonction pas encore faite';//TODO
    break;
    
    case 'ajout_place':
    $g4p_chps=implode(',',$g4p_config['subdivision']);
    foreach($_POST['place'] as $key=>$val)
      $_POST['place'][$key]=mysql_escape_string($val);
    $g4p_chps2='"'.implode('","',$_POST['place']).'"';
    $sql="INSERT INTO genea_place (".$g4p_chps.", base) VALUES (".$g4p_chps2.", ".$_SESSION['genea_db_id'].")";
    if($g4p_last_id=g4p_db_query($sql))
      $_SESSION['message']='Requète éffectuée avec succès';
    else
      $_SESSION['message']='Erreur lors de la requète';
    header('location:'.g4p_make_url('','index.php','',0));
    break;
  }
}
?>
