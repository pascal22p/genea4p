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
require_once($g4p_chemin.'entete.php');

If(!isset($_GET['g4p_opt']) or !$_SESSION['permission']->permission[_PERM_EDIT_FILES_])
{

}
elseif($_GET['g4p_opt']=='ajout_indi')
{
?>
  <h2><?=$g4p_langue['creer_indi_titre']?></h2>
  <div class="cadre">
  <form class="formulaire" method="post" action="<?=g4p_make_url('admin','exec_creer.php','g4p_opt=ajout_indi',0)?>" name="indi">
  <em><?=$g4p_langue['index_nom']?></em><input type="text" name="g4p_nom" size="20" /><br />
  <em><?=$g4p_langue['index_prenom']?></em><input type="text" name="g4p_prenom" size="20" /><br />
  <em><?=$g4p_langue['index_sexe']?></em><input type="text" name="g4p_sexe" size="1" maxlength="1" /><br />
  <br />
  <em><?=$g4p_langue['index_npfx']?></em> <input type="text" name="npfx" size="20" value="" /><br />
  <em><?=$g4p_langue['index_givn']?></em> <input type="text" name="givn" size="20" value="" /><br />
  <em><?=$g4p_langue['index_nick']?></em> <input type="text" name="nick" size="20" value="" /><br />
  <em><?=$g4p_langue['index_spfx']?></em> <input type="text" name="spfx" size="20" value="" /><br />
  <em><?=$g4p_langue['index_nom']?></em> <input type="text" name="surn" size="20" value="" /><br />
  <em><?=$g4p_langue['index_nsfx']?></em> <input type="text" name="nsfx" size="20" value="" /><br />
  <input type="submit" value="<?=$g4p_langue['submit_ajouter']?>" />
  </form></div>

  <?php
}
elseif($_GET['g4p_opt']=='ajout_fam')
{
  if(!isset($_GET['mari']))
    $_GET['mari']='';
  if(!isset($_GET['femme']))
    $_GET['femme']='';

    echo '<h2>',$g4p_langue['creer_fam_titre'],'</h2>';
    echo '<div class="cadre"><form class="formulaire" method="post" action="',g4p_make_url('admin','exec_creer.php','g4p_opt=ajout_fam',0),'" id="ajout_fam" name="ajout_fam">';
    echo '<em>',$g4p_langue['creer_fam_conjoint1'],'</em><br />'; 
    echo $g4p_langue['creer_fam_id_conjoint'];  
    if(!empty($_SESSION['historic']['indi']))
    {
      echo '<select name="mari2" style="width:auto"><option value=""></option>';
      foreach($_SESSION['historic']['indi'] as $tmp)
      {
        $tmp=explode('||',$tmp);
        echo '<option value="'.$tmp[0].'">'.htmlentities($tmp[1],ENT_NOQUOTES,'UTF-8').'</option>';
      }
      echo '</select>',$g4p_langue['a_index_ajout_alias_ou'];
    }    
    echo '<input type="text" value="',$_GET['mari'],'" id="mari" name="mari" />';    
    
    echo '<a href="',g4p_make_url('','recherche.php','type=indi_0&amp;g4p_referer='.rawurlencode('admin|creer.php|'.$_SERVER['QUERY_STRING']).'&amp;g4p_champ=mari',0).'">',$g4p_langue['creer_fam_rechercher_conjoint'],'</a><br />
    <em>',$g4p_langue['creer_fam_conjoint2'],'</em><br />';
    echo $g4p_langue['creer_fam_id_conjoint'];
    if(!empty($_SESSION['historic']['indi']))
    {
      echo '<select name="femme2" style="width:auto"><option value=""></option>';
      foreach($_SESSION['historic']['indi'] as $tmp)
      {
        $tmp=explode('||',$tmp);
        echo '<option value="'.$tmp[0].'">'.htmlentities($tmp[1],ENT_NOQUOTES,'UTF-8').'</option>';
      }
      echo '</select>',$g4p_langue['a_index_ajout_alias_ou'];
    }    
    echo '<input type="text" value="',$_GET['femme'],'" id="femme" name="femme" />
    <a href="',g4p_make_url('','recherche.php','type=indi_0&amp;g4p_referer='.rawurlencode('admin|creer.php|'.$_SERVER['QUERY_STRING']).'&amp;g4p_champ=femme',0),'">',$g4p_langue['creer_fam_rechercher_conjoint'],'</a><br />';
    //<input name="g4p_id" type="hidden" value="',$_GET['g4p_id'],'" />
    echo '<input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form></div>';
}
elseif($_GET['g4p_opt']=='ajout_place')
{
  echo '<h2>Ajouter un lieu</h2>';
  echo '<div class="cadre">';
  echo '<a href="',g4p_make_url('admin','recherche_gns.php','g4p_referer='.rawurlencode('admin|creer.php|g4p_opt=ajout_place'),0),'">Recherche du lieu dans la base GNS</a><br />';
  echo '<hr />';
  echo '<form class="formulaire" method="post" action="',g4p_make_url('admin','exec_creer.php','g4p_opt=ajout_place',0),'" id="ajout_place" name="ajout_place">';
  foreach($g4p_config['subdivision'] as $a_subdiv)
  {
    echo '<em>'.$g4p_langue[$a_subdiv].' : </em><input type="text" value="';
    if(isset($_GET[$a_subdiv]))
      echo $_GET[$a_subdiv];
    echo '" id="'.$a_subdiv.'" name="place[]" /><br />';
  }
  echo '<input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form>

  <hr />
  <a href="http://www.lion1906.com/Pages/Localisation.html" target="_blank">Recherche des coodonnées géographiques</a><br />
  <a href="http://www.lion1906.com/Pages/CodesPostaux.html" target="_blank">Recherche du code postal ou insee</a><br />
  
  </div>';
}
require_once($g4p_chemin.'pied_de_page.php');
?>
