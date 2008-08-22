<?php

$g4p_chemin = "../../../";

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'admin/modules_rapports/liste_module_rapport.php');
require_once($g4p_chemin.'admin/modules_rapports/export_gedcom/class_exportGedcom.php');

if(!$_SESSION['permission']->permission[$g4p_module_export['export_gedcom_db.php']['permission']])
{
  echo $g4p_langue['acces_admin'];
  exit;
}

//on verifie que c'est bien la base de l'admin, si ce n'est pas un super-admin
if(!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
{
  $sql="SELECT id FROM genea_permissions WHERE id_membre=".$_SESSION['g4p_id_membre']." AND type=8 AND permission=1 and (id_base='".$_POST['base_rapport']."' OR id_base='*')";
  if($g4p_infos_req=g4p_db_query($sql))
  {
    $g4p_result=g4p_db_result($g4p_infos_req);
    if(!$g4p_result)
      exit;
  }
}

$sql="SELECT nom FROM genea_infos WHERE id=".$_POST['base_rapport'];
$g4p_infos_req=g4p_db_query($sql);
$g4p_base_select=g4p_db_result($g4p_infos_req);
$g4p_base_select=$g4p_base_select[0]['nom'];
  
if($g4p_langue['entete_charset']=='UTF-8')
  $g4p_base_select=utf8_decode($g4p_base_select);
$chemin = $g4p_chemin.'cache/'.$g4p_base_select.'/fichiers/';
$filename = $g4p_base_select.'.ged';


//On efface les colonnes 'Aucun' des lieux
$placePosition = $_POST['place'];
foreach($placePosition as $key=>$place)
	if($place==-1)
		unset($placePosition[$key]);
		
//Génération du gedcom
$export = new exportGedcom($chemin, $filename, $_POST['base_rapport'], $_POST['g4p_encodage'], $placePosition,
						$_POST['g4p_medias'], $_POST['g4p_notes'], $_POST['g4p_sources']);

if($export->err)
{ //Si erreur
	$_SESSION['message']=$export->err;
	header('location:'.g4p_make_url('admin','export_rapport.php','base='.$_POST['base_rapport'],0));
	exit;
}

$export->genereGedcom( $g4p_config['g4p_version'], $_POST['desc_rapport'] );
fclose($export->fd);

//Création de l'archive zip
require_once($g4p_chemin.'include_sys/zip.lib.php');

$zip = new zipfile();

//Récupération du gedcom en mémoire
$fp = fopen ($chemin.$filename, 'rb');
$content = fread($fp, filesize($chemin.$filename));
fclose ($fp);

//Insertion ds le zip
$zip->addfile($content, $g4p_base_select.'.ged');

if($_POST['g4p_medias']=='oui')
{
  $sql="SELECT format, file
        FROM genea_multimedia WHERE base=".$_POST['base_rapport']." GROUP BY file";
  $g4p_result_req=g4p_db_query($sql);
  if($g4p_media_liste=g4p_db_result($g4p_result_req, 'file'))
  {
    foreach($g4p_media_liste as $g4p_a_media)
    {
      if($g4p_a_media['format']!='URL')
      {
		$filename = $g4p_a_media['file'];
		// contenu du fichier
		if(file_exists($g4p_chemin.'cache/'.$g4p_base_select.'/objets/'.$g4p_a_media['file']))
		{
			$fp = fopen ($g4p_chemin.'cache/'.$g4p_base_select.'/objets/'.$g4p_a_media['file'], 'rb');
			$content = fread($fp, filesize($g4p_chemin.'cache/'.$g4p_base_select.'/objets/'.$g4p_a_media['file']));
			fclose ($fp);
        
			// ajout du fichier dans cet objet
			$zip->addfile($content, $filename);
        }
      }
    }
  }
}

$filename=uniqid('ged_');
$fp = fopen ($chemin.$filename.'.zip', 'wb');
fwrite($fp,$zip->file());
fclose ($fp);

//sauvegarde du fichier en bd
if($_POST['g4p_parm_desc']=='oui')
{
  $_POST['desc_rapport'].='encodage : '.strtoupper($_POST['g4p_encodage'])."\n";
  $_POST['desc_rapport'].='notes : '.$_POST['g4p_notes']."\n";
  $_POST['desc_rapport'].='sources : '.$_POST['g4p_sources']."\n";
  $_POST['desc_rapport'].='medias : '.$_POST['g4p_medias']."\n";
}

$sql="INSERT INTO genea_download (base, fichier, titre, description) VALUES (".$_POST['base_rapport'].",'".$nom_fichier.".zip','".mysql_escape_string($_POST['nom_rapport'])."','".mysql_escape_string($_POST['desc_rapport'])."')";
$g4p_result_req=g4p_db_query($sql);

$_SESSION['message']='Le GEDCOM a été crée avec succès';
header('location:'.g4p_make_url('admin','export_rapport.php','base='.$_POST['base_rapport'],0));


?>
