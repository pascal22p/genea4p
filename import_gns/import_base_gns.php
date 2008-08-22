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
 *            import gns, GEOnet Names Server                              *
 *                                                                         *
 * dernière mise à jour : 26/08/2005                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


$g4p_chemin='../';
// $g4p_chemin='modules/genea4p/';

@ini_set('auto_detect_line_endings','1');

include($g4p_chemin.'p_conf/g4p_config.php');
include($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
$time_start = g4p_getmicrotime();


if (!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
{
  $_SESSION['message']='Vous n\'avez pas les autorisations requises pour importer un gns';
  header('location:'.g4p_make_url('','index.php','',0));
  exit;
}

//creation des répertoires si necessaire
if(!isset($_SESSION['message']))
	$_SESSION['message']='';
if(!is_dir($g4p_chemin.'gns'))
{
  if(!@mkdir($g4p_chemin.'gns',0755))
    $_SESSION['message'].='impossible de créer le répertoire : gns<br />';
  else
  {
    if($f=fopen($g4p_chemin.'gns/.htaccess','w'))
    {
      fwrite($f,"deny from all"); 
      fclose($f);
    }
  }
}
if(!is_dir($g4p_chemin.'gns/base'))
{
  if(!@mkdir($g4p_chemin.'gns/base',0755))
    $_SESSION['message'].='impossible de créer le répertoire : gns/base<br />';
  else
  {
    if($f=fopen($g4p_chemin.'gns/base/.htaccess','w'))
    {
      fwrite($f,"deny from all"); 
      fclose($f);
    }
  }
}

if(isset($_GET['g4p_referer']))
  $_POST['g4p_referer']=$_GET['g4p_referer'];

include($g4p_chemin.'entete.php');
echo '<h2>',$g4p_langue['import_base_gns_titre'],'</h2><div class="cadre">';

if(file_exists($g4p_chemin.'gns/base/'.$g4p_config['gns']['fichier_base']))
{
  $g4p_ligne_cpt=0;

  //Ouverture du fichier
  $g4p_gns=fopen($g4p_chemin.'gns/base/'.$g4p_config['gns']['fichier_base'], 'r');
  if(!$g4p_gns)
  {
    $_SESSION['message']=$g4p_langue['import_gns_base_fichier_erreur_open'];
    header('location:'.g4p_make_url('import_gns','import_base_gns.php','',0));
    exit;
  }

  $g4p_ligne_cpt=0;
  $g4p_ligne=fgets($g4p_gns, 4096);

  //Verification du format du fichier avec la première ligne
  if(!ereg("FIPS_Country_Code\tFIPS_ADM1_Code\tADM1_Name\tLanguage_Code\tRegion_Code",$g4p_ligne))
  {
    $_SESSION['message']=$g4p_langue['import_gns_fichier_non_valide'];
    header('location:'.g4p_make_url('import_gns','import_base_gns.php','',0));
    exit;
  }

  //Recupération des données
  $old_CC='';
  $sql = "INSERT INTO gns_ADM1 (CC,ADM1,name) VALUES ";
  $sql2= "INSERT INTO gns_CC (RC,CC,import) VALUES ";
  $i=1;
  $j=1;
  while (!feof($g4p_gns))
  {
    $g4p_ligne_cpt++;
    $g4p_ligne=fgets($g4p_gns, 4096);
    $data=explode("\t",$g4p_ligne);
    if($data[0]=='')
      break;
    $sql.= "('".$data[0]."','".$data[1]."','";
    if($data[1]=='00')
      $sql.= addslashes(substr($data[2],0,-10));
    else
      $sql.= addslashes($data[2]);
    $sql.= "'),";
    if($old_CC!=$data[0])
    {
      $sql2.= "('".trim($data[4])."','".$data[0]."','0'),";
      if(++$j%_NBRE_MYSQL_ENR_==0)
      {
        g4p_db_query(substr($sql2,0,-1));
        $sql2 = "INSERT INTO gns_CC (RC,CC,import) VALUES ";
      }
      $old_CC=$data[0];
    }
    if(++$i%_NBRE_MYSQL_ENR_==0)
    {
      g4p_db_query(substr($sql,0,-1));
      $sql="INSERT INTO gns_ADM1 (CC,ADM1,name) VALUES ";
    }
  }
  fclose ($g4p_gns);
  g4p_db_query(substr($sql,0,-1));
  g4p_db_query(substr($sql2,0,-1));
  $time_end = g4p_getmicrotime();
  $time=round(($time_end - $time_start)*1000);
  echo $g4p_langue['import_gns_import_succes'].' '.$time.' ms.';
  echo '<br/>'.$g4p_langue['import_gns_base_aff_nb_fips'].' '.($g4p_ligne_cpt-1);
  echo '<hr />';
  if(isset($_POST['g4p_referer']))
  {
    $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']),3);
    $g4p_referer=explode('=',$_POST['g4p_referer'][2],2);
    echo '<a href="'.g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],'g4p_referer='.rawurlencode(htmlspecialchars($g4p_referer[1]))).'">',$g4p_langue['retour'],'</a>';
  }
  echo '</div>';
  require_once($g4p_chemin.'pied_de_page.php');
  unlink($g4p_chemin.'gns/base/'.$g4p_config['gns']['fichier_base']);
  exit;
}

if(empty($_GET['auto']))
  echo '<i>'.$g4p_langue['import_gns_base_help_manuel'].' <u><a href="ftp://'.$g4p_config['gns']['ftp'].$g4p_config['gns']['ftp_rep'].$g4p_config['gns']['fichier_base'].'">'.$g4p_langue['import_gns_help_manuel'][1].'</a></u> '.$g4p_langue['import_gns_help_manuel'][4].' gns/base/</i><br/><br/>';

$manuel=0;
if(!function_exists('ftp_connect'))
{
  echo $g4p_langue['import_gns_ftp_impossible'].'<br/>';
  $manuel=1;
}
else
{
  $ftp = @ftp_connect($g4p_config['gns']['ftp']);
  $log = @ftp_login($ftp, "anonymous", "me@localhost");
  if ((!$ftp) || (!$log)) {
    echo $g4p_langue['import_base_gns_download_impossible'].'<br/>';
    $manuel=1;
  }
}

if(!$manuel && !empty($_GET['auto']))
{
  //Recupération du fichier par ftp
  $fp = fopen($g4p_chemin.'gns/base/'.$g4p_config['gns']['fichier_base'], 'w');
  if(!ftp_fget ( $ftp, $fp, $g4p_config['gns']['ftp_rep'].$g4p_config['gns']['fichier_base'],FTP_BINARY))
  {
    echo $g4p_langue['import_gns_erreur_ftpget'].'<br/><hr/>';
    if(isset($_POST['g4p_referer']))
    {
      $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']),3);
      $g4p_referer=explode('=',$_POST['g4p_referer'][2],2);
      echo '<a href="'.g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],'g4p_referer='.rawurlencode(htmlspecialchars($g4p_referer[1]))).'">',$g4p_langue['retour'],'</a>';
    }
    echo '</div>';
    require_once($g4p_chemin.'pied_de_page.php');
    exit;
  }
  ftp_close($ftp);
  fclose($fp);
  echo $g4p_langue['import_gns_aff_download'].' '.$g4p_config['gns']['fichier_base'].' '.$g4p_langue['import_gns_aff_effectue'];
  echo '<br/>'.$g4p_langue['import_gns_aff_recup'].' <b>'.$g4p_langue['import_gns_aff_non_effectue'].'</b>.';
  echo '<br /><br /><hr /><a href="'.g4p_make_url('import_gns','import_base_gns.php','g4p_referer='.rawurlencode($_GET['g4p_referer']),0).'">'.$g4p_langue['import_gns_poursuivre'].'</a></div>';
  //echo '</div>';
  require_once($g4p_chemin.'pied_de_page.php');
  exit;
}

if(!$manuel)
{
  $list = ftp_rawlist($ftp,$g4p_config['gns']['ftp_rep']);
  foreach($list as $file_desc)
  {
    if(ereg("[^0-9]*[0-9]*[^0-9]*[0-9]*[^0-9]*[0-9]*[^0-9]*([0-9]*).*".$g4p_config['gns']['fichier_base'].".*",$file_desc,$reg))
    {
      echo $g4p_langue['import_gns_aff_download'].' '.$g4p_config['gns']['fichier_base'].' (Taille:'.(int)($reg[1]/1024).'Ko) <b>'.$g4p_langue['import_gns_aff_non_effectue'].'</b>.';
      $fichier_dispo=1;
    }
  }
  ftp_close($ftp);
  if(empty($fichier_dispo))
  {
    echo $g4p_langue['import_gns_base_fichier_introuvable_serveur'].'<br/><br /><hr /><a href="';
    if($_GET['g4p_referer'])
      {
        $_GET['g4p_referer']=explode('|',rawurldecode($_GET['g4p_referer']),3);
        $g4p_referer=explode('=',$_GET['g4p_referer'][2],2);
        echo g4p_make_url($_GET['g4p_referer'][0],$_GET['g4p_referer'][1],'g4p_referer='.rawurlencode(htmlspecialchars($g4p_referer[1])));
      }
      else
        echo g4p_make_url('admin','panel.php','',0);
    echo '</a></div>';
    require_once($g4p_chemin.'pied_de_page.php');
    exit;
  }
  echo '<br/>'.$g4p_langue['import_gns_aff_recup'].' <b>'.$g4p_langue['import_gns_aff_non_effectue'].'</b>.';
  echo '<br /><br /><hr /><a href="'.g4p_make_url('import_gns','import_base_gns.php','auto=1&amp;g4p_referer='.rawurlencode($_GET['g4p_referer']),0).'">'.$g4p_langue['import_gns_poursuivre'].'</a></div>';
}
else
  echo '<br /><br /><hr /><a href="'.g4p_make_url('import_gns','import_base_gns.php','g4p_referer='.rawurlencode($_GET['g4p_referer']),0).'">'.$g4p_langue['import_gns_reessayer'].'</a></div>'; 
//echo '</div>';
require_once($g4p_chemin.'pied_de_page.php');
exit;


?>    

