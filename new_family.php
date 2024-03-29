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

$g4p_javascript='<script src="javascript/jquery/jquery-3.6.0.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.bgiframe.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.dimensions.js"></script>
  <script type="text/javascript" src="javascript/jquery/jquery.autocomplete.min.js"></script>
  <script>
  $(document).ready(function(){
    $("#mari").autocomplete(\'ajax/autocomplete.php\', {max:40});
  });
  $(document).ready(function(){
    $("#femme").autocomplete(\'ajax/autocomplete.php\', {max:40});
  });
  </script>
';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'p_conf/script_start.php');
	
If(!$_SESSION['permission']->permission[_PERM_EDIT_FILES_])
	g4p_error($g4p_langue['acces_non_autorise']);

//var_dump($g4p_indi);

if(empty($_POST))
{
    require_once($g4p_chemin.'entete.php');
    echo '<div class="box_title"><h2>',$g4p_langue['creer_fam_titre'],'</h2></div>'."\n";

    echo '<div class="box">';
    //echo '<div class="box_title">État civil</div>';  
    echo '<form class="formulaire" method="post" action="'.$_SERVER['PHP_SELF'].'" name="fam">';
    echo '<h3>'.$g4p_langue['creer_fam_conjoint1'].'</h3>';
    echo $g4p_langue['creer_fam_id_conjoint'];  
    if(!empty($_SESSION['historic']['indi']))
    {
        echo '<select name="mari2" style="width:auto"><option value=""></option>';
        foreach($_SESSION['historic']['indi'] as $tmp)
        {
            echo '<option value="'.$tmp['id'].'">'.htmlentities($tmp['text'],ENT_NOQUOTES,'UTF-8').'</option>';
        }
        echo '</select>',$g4p_langue['a_index_ajout_alias_ou'];
    }    
    echo '<input type="text" id="mari" name="mari" style="width:300px" />';    

    echo '<h3>'.$g4p_langue['creer_fam_conjoint2'].'</h3>';
    echo $g4p_langue['creer_fam_id_conjoint'];  
    if(!empty($_SESSION['historic']['indi']))
    {
        echo '<select name="femme2" style="width:auto"><option value=""></option>';
        foreach($_SESSION['historic']['indi'] as $tmp)
        {
            echo '<option value="'.$tmp['id'].'">'.htmlentities($tmp['text'],ENT_NOQUOTES,'UTF-8').'</option>';
        }
        echo '</select>',$g4p_langue['a_index_ajout_alias_ou'];
    }    
    echo '<input type="text" id="femme" name="femme" style="width:300px" />';    

    echo '<br /><br />';
    echo '<input type="submit" value="'.$g4p_langue['submit_ajouter'].'" /></form>';

    echo '</div>';

    //  echo '</div>';
    
    require_once($g4p_chemin.'pied_de_page.php');    
}
else
{
    if(!empty($_POST['femme2']))
        $_POST['femme']=$_POST['femme2'];
    if(!empty($_POST['mari2']))
        $_POST['mari']=$_POST['mari2'];

    if(!$_POST['femme'] && !$_POST['mari'])
    {
      //$_SESSION['message']='Veuillez remplir au moins un des champs';
      //header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_fams&g4p_id='.$g4p_last_id,0,0));
      //break;
      echo 'Veuillez remplir au moins un des champs';
      exit;
    }
    
    $_POST['femme']=explode(' ',$_POST['femme']);
    $_POST['femme']=(int)$_POST['femme'][0];
    $_POST['mari']=explode(' ',$_POST['mari']);
    $_POST['mari']=(int)$_POST['mari'][0];
    
    
    if(!$_POST['femme'])
    {
        $_POST['femme']='NULL';
        $base_femme=-1;
    }
    else
    {
        $sql="SELECT base FROM genea_individuals WHERE indi_id=".$_POST['femme'];
        $g4p_result_req=$g4p_mysqli->g4p_query($sql);
        $g4p_result=$g4p_mysqli->g4p_result($g4p_result_req);
        $base_femme=$g4p_result[0]['base'];
    }
        
    if(!$_POST['mari'])
    {
        $_POST['mari']='NULL';
        $base_mari==-1;
    }
    else
    {
        $sql="SELECT base FROM genea_individuals WHERE indi_id=".$_POST['mari'];
        $g4p_result_req=$g4p_mysqli->g4p_query($sql);
        $g4p_result=$g4p_mysqli->g4p_result($g4p_result_req);
        $base_mari=$g4p_result[0]['base'];
    }

    if($_POST['mari']=='NULL' and $_POST['femme']=='NULL')
    {
        echo 'Veuillez remplir au moins un des champs';
        exit;
    }
    
    if($base_mari==-1)
        $base=$base_femme;
    else if($base_feme==-1)
        $base=$base_mari;
    else
        $base=$base_mari;

    $sql="INSERT INTO genea_familles (base, familles_husb , familles_wife) VALUES (".$base.",".$_POST['mari'].", ".$_POST['femme'].")";
    if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']='Requète éffectuée avec succès';
    else
        $_SESSION['message']='Erreur lors de la requète';

    if($_POST['femme'])
    {
        g4p_destroy_cache($_POST['femme']);
        $id_pers=$_POST['femme'];
    }
    if($_POST['mari'])
    {
        g4p_destroy_cache($_POST['mari']);
        $id_pers=$_POST['mari'];
    }
    
    header('location:'.g4p_make_url('','fiche_individuelle.php','id_pers='.$id_pers,0,0));
}




?>
