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

if(!isset($_GET['g4p_referer']))
  $_GET['g4p_referer']='';

include($g4p_chemin.'entete.php');
echo '<h2>',$g4p_langue['import_gns_titre'],'</h2><div class="cadre">';

if(empty($_GET['CC']) || empty($_GET['RC']) )
{
  $_SESSION['message']=$g4p_langue['import_gns_pays_undefined'];
  if($_GET['g4p_referer'])
    {
      $_GET['g4p_referer']=explode('|',rawurldecode($_GET['g4p_referer']),3);
      $g4p_referer=explode('=',$_GET['g4p_referer'][2],2);
      header('location:'.g4p_make_url($_GET['g4p_referer'][0],$_GET['g4p_referer'][1],'g4p_referer='.rawurlencode(htmlspecialchars($g4p_referer[1]))),0);
    }
  else
    header('location:'.g4p_make_url('admin','panel.php','',0));
  exit;
}

if(!file_exists($g4p_chemin.'/gns/'.strtolower($_GET['CC']).'.txt'))
{
  //Ouverture du zip
  if (file_exists($g4p_chemin.'/gns/'.strtolower($_GET['CC']).'.zip') and $zip=fopen($g4p_chemin.'/gns/'.strtolower($_GET['CC']).'.zip',"rb"))
  {
	if(!function_exists('zip_open')){  	
	  	/* dezippe qui marche pas
	    $head = array( );
	    $head['signature']   = reset($tmp=unpack("V",fread( $zip, 4 )));
	    if( (int)$head['signature'] != 0x04034b50 )
	    {
	      echo 'erreur signature';
	      exit;
	    }
	
	    $head['version']     = reset($tmp=unpack("v",fread( $zip, 2 )));
	    $head['bitflag']     = reset($tmp=unpack("v",fread( $zip, 2 )));
	    $head['compmethod']  = reset($tmp=unpack("v",fread( $zip, 2 )));
	    $head['mod_time']    = reset($tmp=unpack("v",fread( $zip, 2 )));
	    $head['mod_date']    = reset($tmp=unpack("v",fread( $zip, 2 )));
	    $head['crc32']       = reset($tmp=unpack("V",fread( $zip, 4 )));
	    $head['compsize']    = reset($tmp=unpack("V",fread( $zip, 4 )));
	    $head['uncompsize']  = reset($tmp=unpack("V",fread( $zip, 4 )));
	    $head['filenamelen'] = reset($tmp=unpack("v",fread( $zip, 2 )));
	    $head['extralen']    = reset($tmp=unpack("v",fread( $zip, 2 )));
	    $head['filename']    = fread( $zip, $head['filenamelen'] );
	    if( $head['extralen'] > 0 ) $head['extra'] = fread( $zip, $head['extralen'] );

	    $dezip=fopen($g4p_chemin.'/gns/'.strtolower($_GET['CC']).'.txt',"wb");
	    $size = $head['compsize'];
  		fwrite($dezip, gzinflate(fread($zip, $head['compsize'])));
  		
	    fclose($dezip);
	    */
	   
	    echo $g4p_langue['import_gns_aff_download'].' '.strtolower($_GET['CC']).'.zip '.$g4p_langue['import_gns_aff_effectue'];
	  	echo '<br/><br/><b>La librairie ZZIPlib n\'est pas disponible vous devez dezipper le fichier manuellement (par ftp dans le répertoire gns)</b>';
	  	
	  	/*echo '<br/>'.$g4p_langue['import_gns_aff_dezip'].' '.$g4p_langue['import_gns_aff_effectue'].'.';
	  	echo '<br/>'.$g4p_langue['import_gns_aff_recup'].' <b>'.$g4p_langue['import_gns_aff_non_effectue'].'</b>.';
	  	echo '<br /><br /><hr /><a href="'.g4p_make_url('import_gns','import_gns.php','auto=1&RC='.$_GET['RC'].'&CC='.$_GET['CC'].'&g4p_referer='.rawurlencode($_GET['g4p_referer']),0).'">'.$g4p_langue['import_gns_poursuivre'].'</a>';
	  	*/
	  	echo '</div>';
	  	require_once($g4p_chemin.'pied_de_page.php');
	  	exit;
	}
	else
	{
		$zip = zip_open($g4p_chemin.'/gns/'.strtolower($_GET['CC']).'.zip');
		if (!$zip)
		{
			$_SESSION['message']=$g4p_langue['import_gns_erreur_unzip'];
			@unlink($g4p_chemin.'/gns/'.strtolower($_GET['CC']).'.zip');
			header('location:'.g4p_make_url('import_gns','import_gns.php','file=0&RC='.$_GET['RC'].'&CC='.$_GET['CC'].'&g4p_referer='.$_GET['g4p_referer'],0));
			exit;
		}
		$zip_entry = zip_read($zip);
			if(!zip_entry_open($zip,$zip_entry, "r")) {
				 $_SESSION['message']=$g4p_langue['import_gns_erreur_unzip'];
			    @unlink($g4p_chemin.'/gns/'.strtolower($_GET['CC']).'.txt');
			    @unlink($g4p_chemin.'/gns/'.strtolower($_GET['CC']).'.zip');
			    header('location:'.g4p_make_url('import_gns','import_gns.php','file=0&RC='.$_GET['RC'].'&CC='.$_GET['CC'].'&g4p_referer='.$_GET['g4p_referer'],0));
			    exit;
			}
        $dezip=fopen($g4p_chemin.'/gns/'.strtolower($_GET['CC']).'.txt','w');
        $lg = zip_entry_filesize($zip_entry);
        while($lg>2048)
        {
         	$lg-=2048;
          	$buf = zip_entry_read($zip_entry,2048);
          	fwrite($dezip,$buf);
        }
        $buf = zip_entry_read($zip_entry,$lg);
        fwrite($dezip,$buf);
    	fclose($dezip);
       
		if(zip_entry_filesize($zip_entry)!=filesize($g4p_chemin.'/gns/'.strtolower($_GET['CC']).'.txt'))
		{
		  	$_SESSION['message']=$g4p_langue['import_gns_erreur_unzip'];
			@unlink($g4p_chemin.'/gns/'.strtolower($_GET['CC']).'.txt');
			@unlink($g4p_chemin.'/gns/'.strtolower($_GET['CC']).'.zip');
			header('location:'.g4p_make_url('import_gns','import_gns.php','file=0&RC='.$_GET['RC'].'&CC='.$_GET['CC'].'&g4p_referer='.$_GET['g4p_referer'],0));
			exit;
		}
		   
		zip_entry_close($zip_entry);
		zip_close($zip);
	}
	
   	echo $g4p_langue['import_gns_aff_download'].' '.strtolower($_GET['CC']).'.zip '.$g4p_langue['import_gns_aff_effectue'].'.';
  	echo '<br/>'.$g4p_langue['import_gns_aff_dezip'].' '.$g4p_langue['import_gns_aff_effectue'].'.';
  	echo '<br/>'.$g4p_langue['import_gns_aff_recup'].' <b>'.$g4p_langue['import_gns_aff_non_effectue'].'</b>.';
  	echo '<br /><br /><hr /><a href="'.g4p_make_url('import_gns','import_gns.php','auto=1&RC='.$_GET['RC'].'&CC='.$_GET['CC'].'&g4p_referer='.rawurlencode($_GET['g4p_referer']),0).'">'.$g4p_langue['import_gns_poursuivre'].'</a></div>';
  	require_once($g4p_chemin.'pied_de_page.php');
  	exit;
  }
}
else
{
  $g4p_ligne_cpt = 1;

  $g4p_fichier=fopen($g4p_chemin.'gns/'.strtolower($_GET['CC']).'.txt',"r");
  $g4p_ligne=fgets($g4p_fichier,4096);

  //Verification du format du fichier avec la première ligne
  if(feof($g4p_fichier) || !ereg("RC\tUFI\tUNI\tLAT\tLONG\tDMS_LAT\tDMS_LONG\tUTM\tJOG\tFC\tDSG\tPC\tCC1\tADM1\tADM2\tDIM\tCC2\tNT\tLC\tSHORT_FORM\tGENERIC\tSORT_NAME\tFULL_NAME\tFULL_NAME_ND\tMODIFY_DATE",$g4p_ligne/*[0]*/))
  {
    echo $_SESSION['message']=$g4p_langue['import_gns_fichier_non_valide'];
    unlink($g4p_chemin.'/gns/'.strtolower($_GET['CC']).'.txt');
    header('location:'.g4p_make_url('import_gns','import_gns.php','file=0&RC='.$_GET['RC'].'&CC='.$_GET['CC'].'&g4p_referer='.$_GET['g4p_referer'],0));
    exit;
  }
 
  //Recupération des données
  $RC_old='';
  $CC_old='';
  $sql = "INSERT INTO gns_lieux (RC,UNI,LAT,LONGI,UTM,JOG,CC1,ADM1,SORT_NAME,FULL_NAME) VALUES ";
  $i=1;
  while (!feof($g4p_fichier))
  {
    $g4p_ligne=fgets($g4p_fichier,4096);
    $data=explode("\t",$g4p_ligne);
    if($data[0]=='')
      break;
    if($RC_old!=$data[0] || $CC_old!=$data[12])
      g4p_db_query("UPDATE gns_CC SET import='1' WHERE RC='".$data[0]."' AND CC='".$data[12]."'");
    $sql.= "('".$data[0]."','".$data[2]."','".$data[3]."','".$data[4]."','".$data[7]."','".$data[8]."','".$data[12]."','".$data[13]."','".addslashes($data[21])."','".addslashes($data[22])."'),";
    $RC_old=$data[0];
    $CC_old=$data[12];
    if(++$g4p_ligne_cpt%_NBRE_MYSQL_ENR_==0)
    {
      g4p_db_query(substr($sql,0,-1));
      $sql="INSERT INTO gns_lieux (RC,UNI,LAT,LONGI,UTM,JOG,CC1,ADM1,SORT_NAME,FULL_NAME) VALUES ";
    }
  }
  g4p_db_query(substr($sql,0,-1));
  fclose($g4p_fichier);
  unlink($g4p_chemin.'gns/'.strtolower($_GET['CC']).'.txt');
  //SUppression du zip si téléchargé
  //if(!empty($_GET['auto']))
    //unlink($g4p_chemin.'gns/'.strtolower($_GET['CC']).'.zip');
  $time_end = g4p_getmicrotime();
  $time=round(($time_end - $time_start)*1000);
  echo $g4p_langue['import_gns_import_succes'].' '.$time.' '.$g4p_langue['import_gns_millisecondes'];
  echo '<br/>'.$g4p_langue['import_gns_import_nb_lieux'].' '.($g4p_ligne_cpt-1);
  echo '<hr />';
  if(!empty($_GET['g4p_referer']))
  {
    $_GET['g4p_referer']=explode('|',rawurldecode($_GET['g4p_referer']),3);
    $g4p_referer=explode('=',$_GET['g4p_referer'][2],2);
    echo '<a href="'.g4p_make_url($_GET['g4p_referer'][0],$_GET['g4p_referer'][1],'RC='.$_GET['RC'].'&CC='.$_GET['CC'].'&g4p_referer='.rawurlencode(htmlspecialchars($g4p_referer[1]))).'">',$g4p_langue['retour'],'</a>';
  }
  echo '</div>';
  require_once($g4p_chemin.'pied_de_page.php');
  exit;
}

if(empty($_GET['auto']))
  echo '<i>'.$g4p_langue['import_gns_help_manuel'][0].' <u><a href="ftp://'.$g4p_config['gns']['ftp'].$g4p_config['gns']['ftp_rep'].strtolower($_GET['CC']).'.zip">'.$g4p_langue['import_gns_help_manuel'][1].'</a></u> '.$g4p_langue['import_gns_help_manuel'][2].' <u><a href="'.$g4p_config['gns']['http'].strtolower($_GET['CC']).'.zip">'.$g4p_langue['import_gns_help_manuel'][3].'</a></u>'.$g4p_langue['import_gns_help_manuel'][4].' gns/</i><br/><br/>';

$manuel=0;
if(!function_exists('ftp_connect'))
{
  echo $g4p_langue['import_gns_ftp_impossible'].'<br/>';
  $manuel=1;
}
else if(!function_exists('zip_open')){
  $manuel=1;
  echo 'La librairie ZZIPlib n\'est pas disponible vous devez dezipper le fichier manuellement (utilisez les liens ci-dessus)';
}
else
{
  $ftp = @ftp_connect($g4p_config['gns']['ftp']);
  $log = @ftp_login($ftp, "anonymous", "me@localhost");
  if ((!$ftp) || (!$log)) {
    echo $g4p_langue['import_gns_download_impossible'].'<br/>';
    $manuel=1;
  }
}

if(!$manuel && !empty($_GET['auto']))
{
  $fp = fopen($g4p_chemin.'gns/'.strtolower($_GET['CC']).'.zip', 'w');
  if(!ftp_fget ( $ftp, $fp, $g4p_config['gns']['ftp_rep'].strtolower($_GET['CC']).'.zip',FTP_BINARY))
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
  echo $g4p_langue['import_gns_aff_download'].' '.strtolower($_GET['CC']).'.zip '.$g4p_langue['import_gns_aff_effectue'].'.';
  echo '<br/>'.$g4p_langue['import_gns_aff_dezip'].' <b>'.$g4p_langue['import_gns_aff_non_effectue'].'</b>.';
  echo '<br/>'.$g4p_langue['import_gns_aff_recup'].' <b>'.$g4p_langue['import_gns_aff_non_effectue'].'</b>.';
  echo '<br /><br /><hr /><a href="'.g4p_make_url('import_gns','import_gns.php','auto=1&RC='.$_GET['RC'].'&CC='.$_GET['CC'].'&g4p_referer='.rawurlencode($_GET['g4p_referer']),0).'">'.$g4p_langue['import_gns_poursuivre'].'</a></div>';
  //echo '</div>';
  require_once($g4p_chemin.'pied_de_page.php');
  exit;
}

if(!$manuel)
{
  $list = @ftp_rawlist($ftp,$g4p_config['gns']['ftp_rep']);
  foreach($list as $file_desc)
  {
    if(ereg("[^0-9]*[0-9]*[^0-9]*[0-9]*[^0-9]*[0-9]*[^0-9]*([0-9]*).*".strtolower($_GET['CC']).".zip.*",$file_desc,$reg))
    {
      echo $g4p_langue['import_gns_aff_download'].' '.strtolower($_GET['CC']).'.zip
            (Taille:'.(int)($reg[1]/1024).'Ko) <b>'.$g4p_langue['import_gns_aff_non_effectue'].'</b>.';
      $fichier_dispo=1;
    }
  }
  if(empty($fichier_dispo))
  {
    echo $g4p_langue['import_gns_cc_unknown'].'<br /><br /><hr /><a href="';
    if($_GET['g4p_referer'])
      {
        $_GET['g4p_referer']=explode('|',rawurldecode($_GET['g4p_referer']),3);
        $g4p_referer=explode('=',$_GET['g4p_referer'][2],2);
        echo g4p_make_url($_GET['g4p_referer'][0],$_GET['g4p_referer'][1],'g4p_referer='.rawurlencode(htmlspecialchars($g4p_referer[1])));
      }
      else
        echo g4p_make_url('admin','panel.php','',0);
    echo '">'.$g4p_langue['retour'].'</a></div>';

    require_once($g4p_chemin.'pied_de_page.php');
    exit;
  }
  echo '<br/>'.$g4p_langue['import_gns_aff_dezip'].' <b>'.$g4p_langue['import_gns_aff_non_effectue'].'</b>.';
  echo '<br/>'.$g4p_langue['import_gns_aff_recup'].' <b>'.$g4p_langue['import_gns_aff_non_effectue'].'</b>.';
  echo '<br /><br /><hr /><a href="'.g4p_make_url('import_gns','import_gns.php','auto=1&RC='.$_GET['RC'].'&CC='.$_GET['CC'].'&g4p_referer='.rawurlencode($_GET['g4p_referer']),0).'">'.$g4p_langue['import_gns_poursuivre'].'</a></div>';
}
else
  echo '<br /><br /><hr /><a href="'.g4p_make_url('import_gns','import_gns.php','RC='.$_GET['RC'].'&CC='.$_GET['CC'].'&g4p_referer='.rawurlencode($_GET['g4p_referer']),0).'">'.$g4p_langue['import_gns_reessayer'].'</a></div>'; 
//echo '</div>';
require_once($g4p_chemin.'pied_de_page.php');

?>    

