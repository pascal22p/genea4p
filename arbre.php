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

$g4p_javascript='<style type="text/css">
#arbre_div {
    overflow:auto;
    width:auto;
    padding:0;
    margin:0;
    border-top:1px solid black;
}
</style>';

$body=' onload="onload();" onresize="onload();" ';

require_once($g4p_chemin.'entete.php');

echo '
<script type="text/javascript">
    function onload() {';
    if(!empty($_GET['pleinecran']))
    {
        echo 'pleinecran();';
        $offset=140;
    }
    else
        $offset=0;
    echo 'document.getElementById(\'arbre_div\').style.height=(window.innerHeight-'.(210-$offset).')+\'px\';';
echo '}

var arbre_width;
function Resizep(ID) {
if(!arbre_width){ 
    arbre_width=document.getElementById(\'arbre_div\').offsetWidth;
}
arbre_width=arbre_width+150;
document.getElementById(ID).width=arbre_width+\'px\';
}
function Resizem(ID) {
if(!arbre_width){ 
    arbre_width=document.getElementById(\'arbre_div\').offsetWidth;
}
arbre_width=arbre_width-150;
document.getElementById(ID).width=arbre_width+\'px\';
}

function pleinecran() {
    document.getElementById(\'header\').style.display=\'none\';
    document.getElementById(\'navigation\').style.display=\'none\';
    document.getElementById(\'wrapper\').style.margin=\'0px\';
    document.getElementById(\'wrapper\').style.padding=\'0px\';
    document.getElementById(\'wrapper\').style.float=\'none\';
    document.getElementById(\'wrapper\').style.width=\'100%\';
    document.getElementById(\'content\').style.margin=\'0px\';
    document.getElementById(\'content\').style.padding=\'0px\';
    document.getElementById(\'content\').style.width=\'100%\';
}
</script>
';

$get=array();
if(!empty($_GET['output']))
    $get['output']='&output='.$_GET['output'];
else
    $get['output']='';
if(!empty($_GET['pleinecran']))
{
    $get['pleinecran']='&pleinecran='.$_GET['pleinecran'];
    echo '<script type="text/javascript">pleinecran()</script>';
}
else
    $get['pleinecran']='';
if(isset($_GET['limite_descendance']) and (int)$_GET['limite_descendance']>=0)
    $get['limite_descendance']='&limite_descendance='.(int)$_GET['limite_descendance'];
else
{
    $get['limite_descendance']='';
    $_GET['limite_descendance']=1;
}
if(isset($_GET['limite_ascendance']) and (int)$_GET['limite_ascendance']>=0)
    $get['limite_ascendance']='&limite_ascendance='.(int)$_GET['limite_ascendance'];
else
{
    $get['limite_ascendance']='';
    $_GET['limite_ascendance']=2;
}
if(!empty($_GET['fulldesc']))
{
    $get['fulldesc']='&fulldesc=1';
}
else
    $get['fulldesc']='';

function return_get($except='')
{
    global $get;
    $tmp=$get;
    if(is_array($except))
        foreach($except as $a_except)  
            unset($tmp[$a_except]);
    elseif(!empty($except))
        unset($tmp[$except]);
    return implode('',$tmp);
}

echo '<a href="#" onClick="Resizep(\'arbre\')">Zoom +</a> 
| <a href="#" onClick="Resizem(\'arbre\')">Zoom -</a> 
| <a href="arbre.php?id_pers='.$_GET['id_pers'].return_get('limite_descendance').'&limite_descendance='.($_GET['limite_descendance']+1).'" >Descendance + 1</a>  
| <a href="arbre.php?id_pers='.$_GET['id_pers'].return_get('limite_descendance').'&limite_descendance='.($_GET['limite_descendance']-1).'" > - 1 </a>  
| <a href="arbre.php?id_pers='.$_GET['id_pers'].return_get('limite_ascendance').'&limite_ascendance='.($_GET['limite_ascendance']+1).'" >Ascendance + 1</a>  
| <a href="arbre.php?id_pers='.$_GET['id_pers'].return_get('limite_ascendance').'&limite_ascendance='.($_GET['limite_ascendance']-1).'" > - 1 </a>  ';
if(empty($get['fulldesc']))
    echo '| <a href="arbre.php?id_pers='.$_GET['id_pers'].return_get('fulldesc').'&fulldesc=1" >Desc. des ascendants</a>  ';
else
    echo '| <a href="arbre.php?id_pers='.$_GET['id_pers'].return_get('fulldesc').'" >Asc. uniquement</a>  ';
echo '| <a href="arbre.php?id_pers='.$_GET['id_pers'].return_get('output').'" >svg</a>  
| <a href="arbre.php?id_pers='.$_GET['id_pers'].return_get('output').'&output=png" >png</a>  
| <a href="arbre.php?id_pers='.$_GET['id_pers'].return_get('output').'&output=java" >java</a>  
| <a href="arbre_svg.php?id_pers='.$_GET['id_pers'].return_get('output').'&output=pdf" >pdf</a>';
if(empty($_GET['pleinecran']))
    echo '| <a href="arbre.php?id_pers='.$_GET['id_pers'].return_get('pleinecran').'&pleinecran=1" >Plein écran</a>';
else
    echo '| <a href="arbre.php?id_pers='.$_GET['id_pers'].return_get('pleinecran').'" >Retour au site</a>';

echo '<div id="arbre_div" >';
if(!empty($_GET['output']) and $_GET['output']=='png')
    echo '<a href="arbre_svg.php?id_pers='.$_GET['id_pers'].'&output=png"><img id="arbre" id="arbre" name="arbre" src="arbre_svg.php?id_pers='.$_GET['id_pers'].return_get('output').'&output=png" width="100%" style="border:0;margin:0" /></a>';
elseif(!empty($_GET['output']) and $_GET['output']=='java')
    echo '<Applet id="arbre" code="net.claribole.zgrviewer.ZGRApplet.class" 
        archive="zvtm-0.10.0-SNAPSHOT.jar,zgrviewer-0.9.0-SNAPSHOT.jar,timingframework-1.0.jar" 
        width="100%" height="100%">  
        <param name="type" value="application/x-java-Applet;version=1.5" />  
        <param name="scriptable" value="false" />  
        <param name="width" value="100%" />  
        <param name="height" value="100%" />  
        <param name="svgURL" value="arbre_svg.php?id_pers='.$_GET['id_pers'].return_get('output').'" />  
        <param name="title" value="zgrviewer - Applet" />  
        <param name="appletBackgroundColor" value="#F1EBDF" />  
        <param name="graphBackgroundColor" value="#F1EBDF" />  
        <param name="highlightColor" value="red" />  
        <param name="displayOverview" value="true" />  
        <param name="focusNodeMagFactor" value="1.5" />  </Applet> ';
        //<param name="svgURL" value="arbre_svg.svg" />
        //<param name="svgURL" value="arbre_svg.php?id_pers='.$_GET['id_pers'].return_get('output').'" />  
else
    echo '<object type="image/svg+xml" id="arbre" name="arbre" data="arbre_svg.php?id_pers='.$_GET['id_pers'].return_get('output').'" width="100%" >
    Votre navigateur ne supporte pas le format svg. Utilisez en un autre (firefox, opera...) ou utilisez le format png.
    </object>';
echo '</div>';
//archive="zvtm-0.10.0-SNAPSHOT.jar,zgrviewer-0.9.0-SNAPSHOT.jar,timingframework-1.0.jar" 
//archive="zvtm-0.9.8.jar,zgrviewer-0.8.2.jar,timingframework-1.0.jar" 
//echo '<iframe src="arbre_svg.php?id_pers='.$_GET['id_pers'].'" width="100%" height="700px" ></iframe>';
/*
echo '
<Applet code="net.claribole.zgrviewer.ZGRApplet.class" archive="zvtm-0.9.6-SNAPSHOT.jar,zgrviewer-0.8.1.jar" width="100%" height="600">  
    <param name="type" value="application/x-java-Applet;version=1.4" />  
    <param name="scriptable" value="false" />  
    <param name="width" value="800" />  
    <param name="height" value="600" />  ';
echo '<param name="svgURL" value="arbre_svg.php?id_pers='.$_GET['id_pers'].'" />  ';
echo '
    <param name="title" value="zgrviewer - Applet" />  
    <param name="appletBackgroundColor" value="#DDD" />  
    <param name="graphBackgroundColor" value="#DDD" />  
    <param name="highlightColor" value="red" />  </Applet>';
*/

include($g4p_chemin.'pied_de_page.php');
?>
