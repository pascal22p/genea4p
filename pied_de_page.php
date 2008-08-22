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
 *                 FIchier de pied de page                                 *
 *                                                                         *
 * dernière mise à jour : 30/04/2005                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

//end of content
echo '</div>';
echo '</div>';

//Left side
include($g4p_chemin.'menu/menu.php');

$time_end = g4p_getmicrotime();
$time = $time_end - $time_start;



echo '<div id="footer">
    <a href="mailto:genealogie@parois.net">Ma généalogie - ver : '.$g4p_config['g4p_version'].'</a>
    - crée par : PAROIS Pascal - mars 2006<br />'.$g4p_mysqli->nb_requetes.' requète(s) SQL - chargée en : '.intval($time*1000).'ms';
if(isset($_SESSION['g4p_email_membre']))
    echo '<br />'.$g4p_langue['membre_connecte'],$_SESSION['g4p_email_membre']." / ".$_SESSION['langue'];
echo '</div>';
if (isset($_SESSION['message']))
{
    echo '<div id="message">',$_SESSION['message'],'</div>';
    unset($_SESSION['message']);
}

echo '<div id="bulle_div" style="position: absolute; visibility: hidden; z-index: 8; left: 2px; top: 2px;"></div>';

if($g4p_langue['entete_charset']!='UTF-8')
{
  $tmp=utf8_decode(ob_get_contents());
  ob_clean();  
  echo $tmp;
}    
ob_end_flush();    


/*echo '<div style="width:auto;height:500px;overflow:auto;font-size:xx-small;text-align:left;padding:5px;background-color:white">';
foreach($g4p_mysqli->requetes as $arequete)
{
    echo $arequete,'<br />';
}
echo '</div>';*/

echo '</div>';
echo '</body></html>';
?>
