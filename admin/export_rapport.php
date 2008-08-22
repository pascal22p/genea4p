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

$g4p_chemin='../';
// $g4p_chemin='modules/genea4p/';

include($g4p_chemin.'p_conf/g4p_config.php');
include($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
include($g4p_chemin.'entete.php');
include($g4p_chemin.'admin/modules_rapports/liste_module_rapport.php');

echo '<h2>',$g4p_langue['export_rapport_titre'],'</h2>';

echo '<a href="',g4p_make_url('admin','index.php','g4p_opt=gerer_download',0),'">[Retour]</a>';
echo '<div class="cadre">';
foreach($g4p_module_export as $a_g4p_module)
{
  if($_SESSION['permission']->permission[$a_g4p_module['permission']])
  {
    echo '<h3>',$a_g4p_module['titre'],'</h3>';
    
    echo '<form class="formulaire" method="post" enctype="multipart/form-data" action="',g4p_make_url('admin/modules_rapports',$a_g4p_module['file'],'',0),'">';
    echo 'Titre : <input type="text" name="nom_rapport" /><br />';
    echo 'Description :<br /><textarea rows="8" cols="80" name="desc_rapport" ></textarea><br />';
    echo $a_g4p_module['formulaire'];
    echo '<input type="hidden" name="base_rapport" value="',$_GET['base'],'" />';
    echo '<input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form><hr />';
  }
}
echo '</div>';

include($g4p_chemin.'pied_de_page.php');
?>
