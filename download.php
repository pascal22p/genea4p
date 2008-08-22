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
 *                     Page de téléchargements                             *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

include($g4p_chemin.'p_conf/g4p_config.php');
include($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
include($g4p_chemin.'entete.php');
/*
echo '<pre>';
print_r($g4p_indi);
echo '</pre>';
*/

if($g4p_langue['entete_charset']=='UTF-8')
  $g4p_db_nom=utf8_decode($_SESSION['genea_db_nom']);
else
  $g4p_db_nom=$_SESSION['genea_db_nom'];

echo '<h2>',$g4p_langue['download_titre'],'</h2>';

echo '<div class="cadre">';
if($_SESSION['permission']->permission[_PERM_DOWNLOAD_])
{
  $sql="SELECT fichier, titre, description FROM genea_download WHERE base=".$_SESSION['genea_db_id'];
  $g4p_result_req=g4p_db_query($sql);
  if($g4p_result=g4p_db_result($g4p_result_req))
  {
    foreach($g4p_result as $g4p_a_download)
    {
      //echo '<a href="',g4p_make_url('cache/'.$g4p_db_nom.'/fichiers/',$g4p_a_download['fichier'],'',0),'">',$g4p_a_download['titre'],'</a><br />';
      echo '<a href="',$g4p_chemin.'cache/'.$g4p_db_nom.'/fichiers/',$g4p_a_download['fichier'],'">',$g4p_a_download['titre'],'</a><br />';
      echo '<p>',nl2br(trim($g4p_a_download['description'])),'</p><hr />';
    }
  
  }
}
else
  echo 'Vous n\'êtes pas autorisé à accéder à ces informations';

echo '</div>';

include($g4p_chemin.'pied_de_page.php');
?>
