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
 *          Recherche dans la base GNS                                     *
 *                                                                         *
 * dernière mise à jour : 26/08/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

if(empty($_POST['g4p_referer']))
{ 
  if($_GET['g4p_referer'])
    $_POST['g4p_referer']=$_GET['g4p_referer'];
  else
  {
    header("Location:".g4p_make_url('','index.php','',0));
    exit;
  }
}

if(!isset($_GET['type']))
{
  $_POST['g4p_gns_rc']=$_POST['g4p_gns_rc_old']=$g4p_config['gns']['default_RC'];
  $_POST['g4p_gns_cc']=$_POST['g4p_gns_cc_old']=$g4p_config['gns']['default_CC'];
  $_GET['type']='CC';
}

if(!empty($_GET['RC']) && !empty($_GET['CC']))
{
  $_POST['g4p_gns_rc']=$_GET['RC'];
  $_POST['g4p_gns_cc']=$_GET['CC'];
}

$type='';
switch($_GET['type'])
{
  case 'result':
    if(!isset($_POST['g4p_gns_rg_old']) || !isset($_POST['g4p_gns_rg'])
       || $_POST['g4p_gns_rg_old']!=$_POST['g4p_gns_rg'])
      $type='RG';
  case 'RG':
    if(empty($_POST['g4p_gns_cc']))
      $type='CC';
    elseif(!isset($_POST['g4p_gns_cc_old']) || $_POST['g4p_gns_cc_old']!=$_POST['g4p_gns_cc'])
      $type='RG';
    elseif(empty($type))
      $type='result';
  case 'CC':
    if(empty($_POST['g4p_gns_rc']))
      $type='RC';
    elseif(!isset($_POST['g4p_gns_rc_old']) || $_POST['g4p_gns_rc_old']!=$_POST['g4p_gns_rc'])
      $type='CC';
    elseif(empty($type))
      $type='RG';
    break;
  case 'RC':
    if(empty($_POST['g4p_gns_rc']))
      $type='RC';
    else
      $type='CC';
    break;
  default:
    $type='RC';
}

if($type=='result' && isset($_POST['g4p_gns_result']))
{
  $data_lieu='';
  if($_POST['g4p_gns_cc']=='FR' && !empty($g4p_config['insee']['use']))
  {
    $sql = "SELECT D.NCCENR as departement, CONCAT(C.DEP,C.COM) as insee, POSTAL as place_cp
    FROM gns_lieux G, gns_france_dep D, gns_france_insee C
    WHERE G.UNI=C.UNI AND D.DEP=C.DEP AND G.UNI='".$_POST['g4p_gns_result']."'";
    $g4p_result=g4p_db_result_query($sql);
    if($g4p_result[0])
    {
      $data_lieu.="&place_departement=".preg_replace("/\r\n/u","",$g4p_result[0]['departement']);
      $data_lieu.="&place_insee=".$g4p_result[0]['insee'];
      $data_lieu.="&place_cp=".$g4p_result[0]['place_cp'];
    }
  }
  $sql="SELECT FULL_NAME,LAT,LONGI,A1.name as pays,A2.name as region FROM gns_lieux L, gns_ADM1
  A1, gns_ADM1 A2 WHERE A1.ADM1='00' AND L.CC1=A1.CC AND A2.ADM1=L.ADM1 AND UNI='".$_POST['g4p_gns_result']."'";
  if($g4p_result=g4p_db_result_query($sql))
  {
    $data_lieu.= "&place_pays=".$g4p_result[0]['pays'];
    $data_lieu.= "&place_region=".$g4p_result[0]['region'];
    $data_lieu.= "&place_ville=".$g4p_result[0]['FULL_NAME'];
    $data_lieu.= "&place_latitude=".$g4p_result[0]['LAT']."&place_longitude=".$g4p_result[0]['LONGI'];
  }
  $referer=explode('|',rawurldecode(str_replace('&amp;','&',$_POST['g4p_referer'])));
  header("Location:".g4p_make_url($referer[0],$referer[1],$referer[2].$data_lieu,0,0));
  exit;
}
require_once($g4p_chemin.'entete.php');

echo '<h2>',$g4p_langue['recherche_gns_titre'],'</h2>'."\n";
echo '<div class="cadre">';

if(isset($_GET['g4p_referer']))
  $_POST['g4p_referer']=$_GET['g4p_referer'];
elseif(!isset($_POST['g4p_referer']))
  $_POST['g4p_referer']='';

$nb1 = g4p_db_result_query("SELECT count(*) as nb FROM gns_CC");
$nb2 = g4p_db_result_query("SELECT count(*) as nb FROM gns_ADM1");
if(!$nb1[0]['nb'] || !$nb2[0]['nb'])
{
  echo $g4p_langue['recherche_gns_fips_vide'],'<a href="',g4p_make_url('import_gns','import_base_gns.php','g4p_referer='.rawurlencode('admin|recherche_gns.php|g4p_referer='.$_POST['g4p_referer']),0),'">',$g4p_langue['import_base_gns_titre'],'</a></div>';
  require_once($g4p_chemin.'pied_de_page.php');
  exit;
}

echo '<form class="formulaire" method="post" action="',g4p_make_url('admin','recherche_gns.php','type='.$type,0),'" name="gns">'."\n";
echo '<input type="hidden" name="g4p_referer" value="',str_replace('&','&amp;',$_POST['g4p_referer']),'"/>'."\n";
//Region du monde (RC)
echo '<em>',$g4p_langue['recherche_gns_rc'],'</em><select name="g4p_gns_rc" onchange="this.form.submit()">';
echo '<option value="0">--'.$g4p_langue['recherche_gns_choix'].'--</option>'."\n";
while(list($key,$val) = each($g4p_langue['region_code']))
{
  if(isset($_POST['g4p_gns_rc']) && $_POST['g4p_gns_rc']==$key)
    $selected=' selected="selected"';
  else
    $selected='';
  echo '<option value="'.$key.'" '.$selected.'>'.$val.'</option>'."\n";  
}
echo '</select><br/>';
if(isset($_POST['g4p_gns_rc']))
  echo '<input type="hidden" name="g4p_gns_rc_old" value="',$_POST['g4p_gns_rc'],'"/>'; 

//Pays (CC)
if($type!='RC')
{
  echo '<em>',$g4p_langue['recherche_gns_cc'],'</em><select name="g4p_gns_cc" onchange="this.form.submit()">';
  echo '<option value="0">--'.$g4p_langue['recherche_gns_choix'].'--</option>';
  $import='';
  $sql="SELECT c.CC,import,name FROM gns_CC c,gns_ADM1 a WHERE c.CC=a.CC AND RC='".$_POST['g4p_gns_rc']."' AND ADM1='00' ORDER BY name ASC";
  if($g4p_result=g4p_db_result_query($sql))
    foreach($g4p_result as $g4p_a_result)
    {
      if(isset($_POST['g4p_gns_cc']) && $_POST['g4p_gns_cc']==$g4p_a_result['CC'])
      {
        $selected=' selected="selected"';
        $import=$g4p_a_result['import']?0:$g4p_a_result['CC'];
      }
      else
        $selected='';
      echo '<option value="'.$g4p_a_result['CC'].'" '.$selected.'>'.$g4p_a_result['name'].'</option>'."\n";  
    }
  echo '</select><br/>';
  if($import)
  {
    echo $g4p_langue['recherche_gns_lieux_vide'],$import,'.txt&nbsp;: <a href="',g4p_make_url('import_gns','import_gns.php','RC='.$_POST['g4p_gns_rc'].'&CC='.$import.'&g4p_referer='.rawurlencode('admin|recherche_gns.php|g4p_referer='.$_POST['g4p_referer']),0),'">',$g4p_langue['import_gns_titre'],'</a><br/>';
    $type='CC';
  }
  if(isset($_POST['g4p_gns_cc']))
    echo '<input type="hidden" name="g4p_gns_cc_old" value="',$_POST['g4p_gns_cc'],'"/>';
}

//Région (ADM1)
if($type!='RC' && $type!='CC')
{
  $sql="SELECT ADM1,name FROM gns_CC c,gns_ADM1 a WHERE c.CC=a.CC AND RC='".$_POST['g4p_gns_rc']."' 
        AND c.CC='".$_POST['g4p_gns_cc']."' AND ADM1!='00' ORDER BY name ASC";
  echo '<em>',$g4p_langue['recherche_gns_rg'],'</em><select name="g4p_gns_rg">';
  echo '<option value="0">--'.$g4p_langue['recherche_gns_ne_sais_pas'].'--</option>';
  if($g4p_result=g4p_db_result_query($sql))
    foreach($g4p_result as $g4p_a_result)
    {
      if(isset($_POST['g4p_gns_rg']) &&  $_POST['g4p_gns_rg']==$g4p_a_result['ADM1'])
        $selected=' selected="selected"';
      else
        $selected='';
      echo '<option value="'.$g4p_a_result['ADM1'].'" '.$selected.'>'.$g4p_a_result['name'].'</option>';  
    }
  echo '</select><br/>';
  if(isset($_POST['g4p_gns_rg']))
    echo '<input type="hidden" name="g4p_gns_rg_old" value="',$_POST['g4p_gns_rg'],'"/>';

  //Ville
  if($type=='result')
  {
    if($_POST['g4p_gns_rg']!='0')
      $sql= "SELECT G.UNI,G.FULL_NAME FROM gns_lieux G WHERE G.ADM1='".$_POST['g4p_gns_rg']."' AND ";
    else
      $sql="SELECT G.UNI,G.FULL_NAME, name FROM gns_lieux G LEFT JOIN gns_ADM1 on G.ADM1=gns_ADM1.ADM1 AND G.CC1=gns_ADM1.CC WHERE ";
    $sql.=" G.CC1='".$_POST['g4p_gns_cc']."' AND G.RC='".$_POST['g4p_gns_rc']."'";
    $sql.= empty($_POST['g4p_gns_ville'])?'':" AND G.FULL_NAME LIKE '%".addslashes($_POST['g4p_gns_ville'])."%'";
    $sql.= " ORDER BY G.FULL_NAME ASC LIMIT 0,"._NBRE_MAX_GNS_VILLE_;
    $g4p_result=g4p_db_query($sql);
    $nb_result=mysql_num_rows($g4p_result);
    if($g4p_result=g4p_db_result($g4p_result))
    {
      echo '<em>',$g4p_langue['recherche_gns_ville'],'</em><select name="g4p_gns_result">';
      foreach($g4p_result as $g4p_a_result)
      {
        if(isset($g4p_a_result['name']))
          $g4p_a_result['FULL_NAME']=$g4p_a_result['FULL_NAME'].' ('.$g4p_a_result['name'].')';
        echo '<option value="'.$g4p_a_result['UNI'].'">'.$g4p_a_result['FULL_NAME'].'</option>';  
      }
      echo '</select>';
      echo ' ('.$nb_result.' '.$g4p_langue['recherche_gns_results'].')';
      if($nb_result>=_NBRE_MAX_GNS_VILLE_)
          echo
          '<i> '.$g4p_langue['recherche_gns_result_limit'].'</i>';
      echo '<br/>';
    }
    else
      echo '<em>',$g4p_langue['recherche_gns_ville'],'</em><input name="g4p_gns_ville"/> ('.$g4p_langue['recherche_gns_no_result'].')<br/>';
  }else
    echo '<em>',$g4p_langue['recherche_gns_ville'],'</em><input name="g4p_gns_ville"/><br/>';
}
echo '<br/><input type="submit" value="',$g4p_langue['submit_rechercher'],'"/>';
echo '</form></div>';
require_once($g4p_chemin.'pied_de_page.php');
?>
