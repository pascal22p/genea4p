<?php
 /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 **
 *Copyright (C) 2004PAROIS Pascal *
 **
 *This program is free software; you can redistribute it and/or modify*
 *it under the terms of the GNU General Public License as published by*
 *the Free Software Foundation; either version 2 of the License, or *
 *(at your option) any later version. *
 **
 *This program is distributed in the hope that it will be useful, *
 *but WITHOUT ANY WARRANTY; without even the implied warranty of*
 *MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the *
 *GNU General Public License for more details.*
 **
 *You should have received a copy of the GNU General Public License *
 *along with this program; if not, write to the Free Software *
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA02111-1307 USA *
 **
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'p_conf/script_start.php');
	
$_GET['q']='%'.str_replace(' ','%',mysql_escape_string($_GET['q'])).'%';
$g4p_query=$g4p_mysqli->g4p_query("SELECT nccenr, ign_rgc.dep, ign_rgc.com, CONCAT(ign_rgc.dep,ign_rgc.com) as insee,  longi_dms , lati_dms 
    FROM insee_communes
    LEFT JOIN ign_rgc ON insee_communes.dep=ign_rgc.dep AND insee_communes.com=ign_rgc.com
    WHERE ncc LIKE '".$_GET['q']."' OR
    nccenr LIKE '".$_GET['q']."' 
    ORDER BY ncc
    LIMIT 40   ");
if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
{
    foreach($g4p_result as $g4p_a_result)
    {
#        $cpt=count($g4p_a_result['longi_dms']);
#        $seconde=$g4p_a_result['longi_dms'][$cpt-1].$g4p_a_result['longi_dms'][$cpt];
#        $minute=$g4p_a_result['longi_dms'][$cpt-3].$g4p_a_result['longi_dms'][$cpt-2];
#        $degre=$g4p_a_result['longi_dms'][$cpt-5].$g4p_a_result['longi_dms'][$cpt-6];
        $json[]='{ dep: "'.$g4p_a_result['dep'].'", '
            .' com: "'.$g4p_a_result['com'].'",  '
            .'insee: "'.$g4p_a_result['insee'].'",  '
            .'longi_dms: "'.$g4p_a_result['longi_dms'].'",  '
            .'lati_dms: "'.$g4p_a_result['lati_dms'].'"} ';
    }
    echo '[ '.implode($json,',').' ]';
}


?>
