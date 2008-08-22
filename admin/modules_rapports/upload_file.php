<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *\
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

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *\
 *                Export la base au format GEDCOM                           *
 *                                                                          *
 * dernière mise à jour : 11/07//2004                                       *
 * En cas de problème : http://www.parois.net                               *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='../../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'admin/modules_rapports/liste_module_rapport.php');

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
if($g4p_infos_req=g4p_db_query($sql))
  $g4p_result=g4p_db_result($g4p_infos_req);

if(isset($_FILES) and empty($_FILES['g4p_file']['error']))
{
  if(!move_uploaded_file($_FILES['g4p_file']['tmp_name'],$g4p_chemin.'cache/'.$g4p_result[0]['nom'].'/fichiers/'.$_FILES['g4p_file']['name']))
    $g4p_error=1;
}
else
  $g4p_error=1;
  
if(!empty($g4p_error))
{
  $_SESSION['message']='Erreur lors de l\'upload du fichier';
  header('location:'.$g4p_chemin.'admin/export_rapport.php?base='.$_POST['base_rapport']);
  exit;
}

$sql="INSERT INTO genea_download (base, fichier, titre, description) VALUES (".$_POST['base_rapport'].",'".$_FILES['g4p_file']['name']."','".mysql_escape_string($_POST['nom_rapport'])."','".mysql_escape_string($_POST['desc_rapport'])."')";
$g4p_result_req=g4p_db_query($sql);

$_SESSION['message']='Fichier ajouté avec succès avec succès';
header('location:'.g4p_make_url('admin','export_rapport.php','base='.$_POST['base_rapport'],0));

?>
