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

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *Page à tout faire*
 * *
 * dernière mise à jour : novembre 2004*
 * En cas de problème : http://www.parois.net*
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'p_conf/script_start.php');

$g4p_javascript='<script src="javascript/jquery/jquery-1.3.1.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.bgiframe.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.dimensions.js"></script>
  <script type="text/javascript" src="javascript/jquery/jquery.autocomplete.min.js"></script>
  <script>
  $(document).ready(function(){
    $("#g4p_nom").autocomplete(\'ajax/autocomplete_surname.php\', {max:40});
  });
  </script>
';

If(!$_SESSION['permission']->permission[_PERM_EDIT_FILES_])
	g4p_error($g4p_langue['acces_non_autorise']);

//var_dump($g4p_indi);

if(empty($_POST))
{
    require_once($g4p_chemin.'entete.php');
    echo '<div class="box_title"><h2>Nouvelle fiche individuelle</h2></div>'."\n";

    echo '<div class="box">';
    echo '<div class="box_title">État civil</div>';  
    echo '<form class="formulaire" method="post" action="'.$_SERVER['PHP_SELF'].'" name="indi">';
    echo '<ul class="etat_civil">';
    echo '<li>Id : Automatique</li>';
    
//liste des bases
    echo '<li>Base généalogique : <select name="g4p_base"  />';
    $g4p_query=$g4p_mysqli->g4p_query("SELECT id, nom FROM genea_infos ORDER BY nom");
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
    {
        foreach($g4p_result as $g4p_a_result)
        {
            $select=($g4p_a_result['id']==$_SESSION['genea_db_id'])?('selected="selected"'):('');
            echo '<option value="'.$g4p_a_result['id'].'"  '.$select.'>'.$g4p_a_result['nom'].'</option>';
        }
    }
    echo '</select></li>';
    
    echo '<li>',$g4p_langue['index_nom'],' <input type="text" id="g4p_nom" name="g4p_nom" size="20" /></li>';
    echo '<li>',$g4p_langue['index_prenom'],' <input type="text" name="g4p_prenom" size="20" /></li>';
    echo '<li>',$g4p_langue['index_sexe'],' <input type="text" name="g4p_sexe" size="1" maxlength="1" /></li>';
    echo '<li>',$g4p_langue['index_npfx'],' <input type="text" name="npfx" size="20" value="" /></li>';
    echo '<li>',$g4p_langue['index_givn'],' <input type="text" name="givn" size="20" value="" /></li>';
    echo '<li>',$g4p_langue['index_nick'],' <input type="text" name="nick" size="20" value="" /></li>';
    echo '<li>',$g4p_langue['index_spfx'],' <input type="text" name="spfx" size="20" value="" /></li>';
    echo '<li>',$g4p_langue['index_nsfx'],' <input type="text" name="nsfx" size="20" value="" /></li>';
    echo '</ul>';
    echo '<input type="submit" value="'.$g4p_langue['submit_ajouter'].'" /></form>';
    echo '</div>';

    //  echo '</div>';
    
    require_once($g4p_chemin.'pied_de_page.php');    
}
else
{

    $_POST['g4p_nom']=strtoupper($_POST['g4p_nom']);

    $sql="INSERT INTO genea_individuals (indi_nom, indi_prenom, indi_sexe, base, indi_npfx, indi_givn, indi_nick, indi_spfx, indi_nsfx) VALUES (
         '".$g4p_mysqli->escape_string($_POST['g4p_nom'])."', '".$g4p_mysqli->escape_string($_POST['g4p_prenom'])."', 
         '".$g4p_mysqli->escape_string($_POST['g4p_sexe'])."',".(int)$_POST['g4p_base'].",
         '".$g4p_mysqli->escape_string($_POST['npfx'])."', '".$g4p_mysqli->escape_string($_POST['givn'])."', 
         '".$g4p_mysqli->escape_string($_POST['nick'])."', '".$g4p_mysqli->escape_string($_POST['spfx'])."', 
         '".$g4p_mysqli->escape_string($_POST['nsfx'])."')";
    if($g4p_query=$g4p_mysqli->g4p_query($sql))
      $_SESSION['message']='Requète éffectuée avec succès';
    else
      $_SESSION['message']='Erreur lors de la requète';
    $g4p_last_id=$g4p_mysqli->insert_id;

    $g4p_indi=g4p_load_indi_infos($g4p_last_id);
    g4p_update_agregat_noms($_POST['g4p_nom']);
    header('location:'.g4p_make_url('','fiche_individuelle.php','id_pers='.$g4p_last_id,0));
}




?>
