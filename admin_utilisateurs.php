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
 *        Modifications des enregistrements , administration               *
 *                                                                         *
 * dernière mise à jour : 31/12/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

$g4p_javascript='<script src="javascript/jquery/jquery-1.3.1.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.bgiframe.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.dimensions.js"></script>
  <script type="text/javascript" src="javascript/jquery/jquery.autocomplete.min.js"></script>
  <script type="text/javascript">';
  
$sql="SELECT membre_id, permission_id, base, nom as nom_base, permission_type, permission_value FROM genea_permissions LEFT JOIN genea_infos ON base=genea_infos.id ORDER BY membre_id, base, permission_type";
$g4p_infos_req=$g4p_mysqli->g4p_query($sql);
if($g4p_result=$g4p_mysqli->g4p_result($g4p_infos_req,'membre_id', false))
{
    $g4p_javascript.="var listeperms = [];\n";
    foreach($g4p_result as $key=>$g4p_a_result)
    {
        $g4p_javascript.="listeperms[".$key."] = new Array(".count($g4p_result)."); \n";
        foreach($g4p_a_result as $key=>$a_perm)
        {
            if(empty($a_perm['base']))
                $a_perm['base']='Toutes';
            $ouinon='<select name="valeur['.$a_perm['permission_id'].']" style="width:auto">';
            if($a_perm['permission_value']==1)
            {
                $ouinon.='<option value="1" selected="selected">Oui</option>';
                $ouinon.='<option value="0">Non</option>';
            }
            else
            {
                $ouinon.='<option value="1">Oui</option>';
                $ouinon.='<option value="0" selected="selected">Non</option>';
            }            
            $ouinon.='</select>';
            $g4p_javascript.="listeperms[".$a_perm['membre_id']."][".$key."] = new Array(".count($g4p_a_result)."); \n";
            $g4p_javascript.="listeperms[".$a_perm['membre_id']."][".$key."][0]='".$a_perm['base']."';\n";
            $g4p_javascript.="listeperms[".$a_perm['membre_id']."][".$key."][1]='".addslashes($g4p_langue[$a_perm['permission_type']])."';\n";
            $g4p_javascript.="listeperms[".$a_perm['membre_id']."][".$key."][2]='".addslashes($ouinon)."';\n";
            $g4p_javascript.="listeperms[".$a_perm['membre_id']."][".$key."][3]='Delete';\n";
        }
    }
}
  
$g4p_javascript.='$(function(){
      $("select#membre_id_perm").change(function(){
        $("table#permission_table").empty();
        $("table#permission_table").append("<thead><tr><td>Nom de la base</td><td>'.$g4p_langue['a_index_gerer_perm_type_perm'].'</td><td>'.$g4p_langue['a_index_gerer_perm_valeur'].'</td><td></td></tr></thead>");
        updateperms(listeperms[$(this).val()]);
      })
    })
    
    function updateperms(val) {
        var textToInsert = [];
        textToInsert[0] = \'<tbody>\';
        var i = 1;
        var length = val.length;
        for (var a = 0; a <length; a += 1) {
            textToInsert[i++]  = \'<tr>\';
            textToInsert[i++]  = \'<td>\'+val[a][0]+\'</td>\';
            textToInsert[i++]  = \'<td>\'+val[a][1]+\'</td>\';
            textToInsert[i++]  = \'<td>\'+val[a][2]+\'</td>\';
            textToInsert[i++]  = \'<td>\'+val[a][3]+\'</td>\';
            textToInsert[i++] = \'</tr>\' ;
        }
        textToInsert[i++] = \'</tbody>\';
        $("table#permission_table").append(textToInsert.join(""));
    }            
    </script>
';

require_once($g4p_chemin.'entete.php');


if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
{
    echo '<div class="box_title"><h2>'.$g4p_langue['a_index_gerer_perm_titre'].'</h2></div>';


    echo '<div class="box">';
    echo '<div class="box_title">Ajout d\'un nouvel utilisateur</div>';
    
    echo '<form class="formulaire" method="post" action="'.$_SERVER['PHP_SELF'].'" name="new_member">';
    echo '<dl class="horizontal">';
    echo '<dt style="width:7em;line-height:1.5em;vertical-align:text-bottom;">Adresse email</dt>
        <dd style="line-height:1.5em;vertical-align:text-bottom;"><input name="email_add" type="text" value="" size="40"  /></dd>';
    echo '<dt style="width:7em;line-height:1.5em;vertical-align:text-bottom;">Mot de passe</dt>
        <dd style="line-height:1.5em;vertical-align:text-bottom;"><input name="mdp" type="text" value="" size="40" /></dd></dl>';
    echo '<input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form></div>';
    
    
    echo '<div class="box">';
    echo '<div class="box_title">Suppression d\'un utilisateur</div>';
    echo '<form class="formulaire" method="post" action="'.$_SERVER['PHP_SELF'].'" name="del_member">';
    echo '<select name="del_membre_id" style="width:auto">';
    $sql="SELECT id, email FROM genea_membres";
    $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
    $g4p_liste_membre=$g4p_mysqli->g4p_result($g4p_infos_req);
    foreach($g4p_liste_membre as $g4p_a_result)
        if($_SESSION['g4p_id_membre']!=$g4p_a_result['id'])
            echo '<option value="',$g4p_a_result['id'],'">',$g4p_a_result['email'],'</option>';
    echo '</select>';
    echo '<input type="submit" value="',$g4p_langue['Supprimer'],'" /></form></div>';
    
    
    echo '<div class="box">';
    echo '<div class="box_title">Gestion des permissions</div>';
    echo '<form class="formulaire" method="post" action="'.$_SERVER['PHP_SELF'].'" name="load_perm">';
    echo 'Editer les permissions de : <select name="membre_id_perm" id="membre_id_perm" style="width:auto"><option value="0" selected="selected"> </option>';
    // creation de la liste des membres
    $sql="SELECT id, email FROM genea_membres";
    $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
    $g4p_liste_membre=$g4p_mysqli->g4p_result($g4p_infos_req);
    foreach($g4p_liste_membre as $g4p_a_result)
        echo '<option value="',$g4p_a_result['id'],'">',$g4p_a_result['email'],'</option>';
    echo '</select>';

    echo '<table id="permission_table">';
    echo '</table>';
  
        
    echo '</div>';

}

require_once($g4p_chemin.'pied_de_page.php');

?>

