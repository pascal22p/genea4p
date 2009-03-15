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
 * dernière mise à jour : 06/11/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
//echo '<textarea cols=1000 rows=100>';

if(isset($_GET['id_pers']))
  $g4p_indi=g4p_load_indi_infos($_GET['id_pers']);

if (!isset($g4p_indi))
  die($g4p_langue['id_inconnu']);

$g4p_titre_page='Famille proche de : '.$g4p_indi->nom.' '.$g4p_indi->prenom;
require_once($g4p_chemin.'entete.php');

//echo '<object type="image/svg+xml" name="famille" data="famille_proche_svg.php?id_pers='.$_GET['id_pers'].'" width="100%" ></object>';
?>
<Applet code="net.claribole.zgrviewer.ZGRApplet.class" archive="zvtm-0.9.6-SNAPSHOT.jar,zgrviewer-0.8.1.jar" width="100%" height="600">  
    <param name="type" value="application/x-java-Applet;version=1.4" />  
    <param name="scriptable" value="false" />  
    <param name="width" value="800" />  
    <param name="height" value="600" />  
<?php
    echo '<param name="svgURL" value="famille_proche_svg.php?id_pers='.$_GET['id_pers'].'" />  ';
?>
    <param name="title" value="zgrviewer - Applet" />  
    <param name="appletBackgroundColor" value="#DDD" />  
    <param name="graphBackgroundColor" value="#DDD" />  
    <param name="highlightColor" value="red" />  </Applet>
<?php


include($g4p_chemin.'pied_de_page.php');
?>
