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
 *               Détail d'un évènement individuel                          *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// en module NPDS, ce fichier est inutile

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

if(isset($_GET['langue']) or isset($_GET['theme']) or isset($_GET['place_-1']))
{
  if(isset($_GET['langue']))
  {
    setcookie('genea4p[langue]',$_GET['langue'],time()+$g4p_config['cookie_time_limit'],'/');
    $_SESSION['langue']=$_GET['langue'];
  }

  if(isset($_GET['theme'])) 
  {
    setcookie('genea4p[theme]',$_GET['theme'],time()+$g4p_config['cookie_time_limit'],'/');    
    $_SESSION['theme']=$_GET['theme'];
  }

//Davy : ajout profil des lieux
  if(isset($_GET['place_0'])) 
  {
    unset($_SESSION['place']);
    for($i=0;$i<count($g4p_config['subdivision'])&&$_GET['place_'.$i]!=-1;$i++)
    {
       setcookie('genea4p[place]['.$i.']',$_GET['place_'.$i],time()+$g4p_config['cookie_time_limit'],'/');
       $_SESSION['place'][$i]=$_GET['place_'.$i];
    }
  }

  if($_SESSION['g4p_id_membre']!=1)
  {
    $sql="UPDATE genea_membres SET ";
    if(isset($_GET['langue']))
      $sql.="langue='".$_GET['langue']."', ";
    if(isset($_GET['theme']))
      $sql.="theme='".$_GET['theme']."', ";
    if(isset($_GET['place_0'])){
      $sql.="place='";
      for($i=0;$i<count($g4p_config['subdivision']);$i++)
        $sql.=$_GET['place_'.$i].",";
      $sql=substr($sql,0,-1)."', ";
    }
    echo $sql=substr($sql,0,-2)." WHERE id=".$_SESSION['g4p_id_membre'];
    g4p_db_query($sql);
  }
  header('location:'.g4p_make_url('','index.php','',0));
  exit;
}

require_once($g4p_chemin.'entete.php');

echo '<h2>',$g4p_langue['profil_titre'],'</h2>';
echo '<div class="cadre">';

if($_SESSION['g4p_id_membre']==1)
{
  echo '<p>',$g4p_langue['profil_annonyme'],'</p>';
  $g4p_result['langue']=$g4p_config['default_lang'];
  $g4p_result['theme']=$g4p_config['default_theme'];
}
else
{
  $sql="SELECT email, langue, theme, place FROM genea_membres WHERE id=".$_SESSION['g4p_id_membre'];
  if($g4p_result=g4p_db_query($sql))
  {
    if($g4p_result=g4p_db_result($g4p_result))
    {
      $g4p_result=$g4p_result[0];
      echo '<p>',sprintf($g4p_langue['profil_membre'],$g4p_result['email']),'</p>';
    }
  }
}
echo '<hr />';
echo '<form class="formulaire" method="get" action="',g4p_make_url('','profil.php','',0),'">';

echo '<p><label for="langue">',$g4p_langue['profil_langue'],'</label><select id="langue" name="langue" size="1">';
foreach($g4p_config['langues'] as $key=>$val)
{
  if($key==$g4p_result['langue'])
    $g4p_select='selected="selected"';
  else
    $g4p_select='';
  echo '<option value="',$key,'" ',$g4p_select,'>',$val,'</option>';
}
echo '</select></p>';

echo '<p><label for="theme">',$g4p_langue['profil_theme'],'</label><select id="theme" name="theme">';
foreach($g4p_config['themes'] as $key=>$val)
{
  if($key==$g4p_result['theme'])
    $g4p_select='selected="selected"';
  else
    $g4p_select='';
  echo '<option value="',$key,'" ',$g4p_select,'>',$val,'</option>';//syl:debug: utiliser $g4p_select
}
echo '</select></p>';

//Davy : ajout profil des lieux
if(isset($g4p_result['place']))
  $place=explode(',',$g4p_result['place']);
else
  $place=$_SESSION['place'];
echo '<p>',$g4p_langue['profil_lieu'],'<br />';
for($i=0; $i<count($g4p_config['subdivision']); $i++)
{
  echo '<select id="place_',$i,'" name="place_',$i,'">';
  echo '<option value="-1"';
  if(!isset($place[$i]))
    echo ' selected="selected"';
  echo '>',$g4p_langue['profil_place_aucun'],'</option>';
  foreach($g4p_config['subdivision'] as $key=>$val)
  {
    if(isset($place[$i])&&$key==$place[$i])
      $g4p_select='selected="selected"';
    else
      $g4p_select='';
    echo '<option value="',$key,'" ',$g4p_select,'>',$g4p_langue[$val],'</option>';
  }
  echo '</select>,';
}
echo '</p><input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form>';

echo '</div>';

include($g4p_chemin.'pied_de_page.php');
?>
