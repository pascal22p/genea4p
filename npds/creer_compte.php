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
 *          Page à tout faire                                              *
 *                                                                         *
 * dernière mise à jour : novembre 2004                                    *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

if(!isset($_POST['g4p_action']))
{
  require_once($g4p_chemin.'entete.php');
  echo '<h2>Création d\'une base de donnée de données généalogique pour le compte : ',$cookie[1],'</h2>';
  echo '<div class="cadre">';
  
  echo 'Ce site vous offre la possibilité de pouvoir gérer votre base de données généalogique. De nombreuses options sont disponibles : import/export GEDCOM, création/modification de personnes, évènements, notes, sources, médias...<br />
  Plusieurs personnes différentes peuvent utiliser la même généalogie et travailler ensemble.';
  echo '<br /><br /><br />';

  echo '<form class="formulaire" method="post" action="'.g4p_make_url('npds','creer_compte.php','',0).'">';
  echo 'Description de votre base : <br />
    <textarea rows="8" cols="80" name="description" ></textarea><br />';
  echo '<input type="hidden" name="g4p_action" value="creer_compte" />';
  echo '<input type="submit" value=" Activer mon compte " /></form></div>';

  require_once($g4p_chemin.'pied_de_page.php');
}
else
{
  if(empty($cookie[0]))
  {
    require_once($g4p_chemin.'entete.php');
  
    echo '<h2>Création d\'une base de donnée de données généalogique pour le compte : ',$cookie[1],'</h2>';
    echo '<div class="cadre">';
    echo '<b>Vous devez être membre du site pour activer votre base</b>';
    echo '</div>';
    require_once($g4p_chemin.'pied_de_page.php');
    exit;
  }
  
  $sql="SELECT id FROM genea_infos WHERE nom='".mysql_escape_string($cookie[1])."'";
  if($g4p_result=g4p_db_query($sql))
  {
    if($g4p_result=g4p_db_result($g4p_result))
    {
      require_once($g4p_chemin.'entete.php');
    
      echo '<h2>Création d\'une base de donnée de données généalogique pour le compte : ',$cookie[1],'</h2>';
      echo '<div class="cadre">';
      echo '<b>Votre base de donnée est déjà active</b><br />';
      
      echo '<a href="',g4p_make_url('','index.php','genea_db_id='.$g4p_result[0]['id'].'&g4p_action=liste_patronyme','patronymes-'.$g4p_result[0]['id']),'">Voir ma base</a>';
      echo '</div>';
      require_once($g4p_chemin.'pied_de_page.php');
      exit;
    }
  }    
  else
    exit;
    
  
  //creation de la base
  $sql="INSERT INTO genea_infos (nom, descriptif) VALUES ('".mysql_escape_string($cookie[1])."','".mysql_escape_string(htmlspecialchars($_POST['description']))."')";
  if($g4p_new_id=g4p_db_query($sql))
    $_SESSION['message']=$g4p_langue['message_creer_base_succes'];

  if($g4p_langue['entete_charset']=='UTF-8')
      $cookie[1]=utf8_decode($cookie[1]);

  if(!is_dir($g4p_chemin.'cache'))
  {
    if(!mkdir($g4p_chemin.'cache',0755))
      $_SESSION['message'].='impossible de créer le répertoire : cache<br />';
    else
      if($f=fopen($g4p_chemin.'cache/'.$cookie[1].'/.htaccess','w'))
      {
        fwrite($f,"<Files *.txt>\ndeny from all\n</Files>\noptions -Indexes"); 
        fclose($f);
      }
  }

  if(!mkdir($g4p_chemin.'cache/'.$cookie[1],0755))
    $_SESSION['message'].='impossible de créer le répertoire : cache/'.$cookie[1].'<br />';
  if(!mkdir($g4p_chemin.'cache/'.$cookie[1].'/fichiers',0755))
    $_SESSION['message'].='impossible de créer le répertoire : cache/'.$cookie[1].'/fichiers<br />';
  if(!mkdir($g4p_chemin.'cache/'.$cookie[1].'/objets',0755))
    $_SESSION['message'].='impossible de créer le répertoire : cache/'.$cookie[1].'/objets<br />';      

  $_SESSION['genea_db_id']=$g4p_new_id;

  //creation de l'admin
  $_POST['id_membre']=$cookie[0];
  $_POST['type'][0]=_PERM_ADMIN_;
  $_POST['valeur'][0]=1;
  $_POST['nom'][0]=$_SESSION['genea_db_id'];
  
  //verification des dépendances
  $tmp=array();
  foreach($_POST['type'] as $key=>$a_type)
    $tmp[$key]=$_POST['type'][$key].$_POST['nom'][$key];
  
  foreach($_POST['type'] as $key=>$a_type)
  {
    if(isset($g4p_permissions[$a_type]['dependance']))
    {
      foreach($g4p_permissions[$a_type]['dependance'] as $g4p_a_dependance_key=>$g4p_a_dependance_value)
      {
        if(!in_array($g4p_a_dependance_key.$_POST['nom'][$key],$tmp))
        {
          $g4p_new_perm[]=array('type'=>$g4p_a_dependance_key, 'valeur'=>1, 'nom'=>$_POST['nom'][$key]);
          $_SESSION['message'].='<br />Dépendance incomplète, ajout de : '.$g4p_a_dependance_key.'-'.$_POST['nom'][$key];
        }        
      }
    }
  }

  //2ème passage
  foreach($g4p_new_perm as $a_perm)
  {
    if(isset($g4p_permissions[$a_perm['type']]['dependance']))
    {
      foreach($g4p_permissions[$a_perm['type']]['dependance'] as $g4p_a_dependance_key=>$g4p_a_dependance_value)
      {
        if(!in_array($g4p_a_dependance_key.$g4p_permissions[$a_perm['nom']],$tmp))
        {
          $g4p_new_perm[]=array('type'=>$g4p_a_dependance_key, 'valeur'=>1, 'nom'=>$a_perm['nom']);
          $_SESSION['message'].='<br />Dépendance incomplète, ajout de : '.$g4p_a_dependance_key.'-'.$a_perm['nom'];
        }        
      }
    }
  }
    
  //suppression des perms
  $liste_perm=implode(',',array_keys($_POST['type']));
  if(!empty($liste_perm))
  {
    $sql="DELETE FROM genea_permissions WHERE id IN (".$liste_perm.")";
    g4p_db_query($sql);
  }
  
  //insertions des nouvelles perm
  $sql="INSERT IGNORE INTO genea_permissions (id_membre, type, permission, id_base) VALUES ";
  foreach($_POST['type'] as $key=>$a_type)
    $sql.="(".$_POST['id_membre'].", ".$_POST['type'][$key].", ".$_POST['valeur'][$key].", '".$_POST['nom'][$key]."'), ";
  foreach($g4p_new_perm as $a_newperm)
    $sql.="(".$_POST['id_membre'].", ".$a_newperm['type'].", ".$a_newperm['valeur'].", '".$a_newperm['nom']."'), ";
  $sql=substr($sql,0,-2);
  g4p_db_query($sql);
  
  header('location:'.g4p_make_url('npds','creer_compte.php','',0));
  

}
?>
