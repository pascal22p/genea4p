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
 *        Modifications des enregistrements , administration               *
 *                                                                         *
 * dernière mise à jour : 31/12/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'entete.php');

echo '<div class="cadre"><h2>Gestion des lieux</h2>';
echo '<center><a href="',g4p_make_url('admin','creer.php','g4p_opt=ajout_place','',0),'">Ajouter un lieu</a> - <a href="',g4p_make_url('admin','index.php','g4p_opt=mod_place','',0),'">Modifier un lieu</a> - <a href="',g4p_make_url('admin','index.php','g4p_opt=del_place','',0),'">Supprimer un lieu</a></center></div>';
echo '<div class="cadre"><h2>Gestion des dépots</h2>';
echo '<center><a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_repo','',0),'">Ajouter un dépot</a> - <a href="',g4p_make_url('admin','index.php','g4p_opt=mod_repo','',0),'">Modifier un dépot</a> - <a href="',g4p_make_url('admin','index.php','g4p_opt=del_repo','',0),'">Supprimer un dépot</a></center>';
echo '</div>';

  
require_once($g4p_chemin.'pied_de_page.php');
?>
