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
	
$_GET['id']=(int)$_GET['id'];
$json[]='';
    
$g4p_query=$g4p_mysqli->g4p_query("SELECT addr_id, addr_addr, addr_city, addr_ctry, base 
    FROM genea_address WHERE base=".$_GET['id']." ORDER BY addr_ctry, addr_city, addr_addr");
if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
{
    foreach($g4p_result as $g4p_a_result)
        $json[]='{ optionValue: '.$g4p_a_result['addr_id'].', optionDisplay: \''.$g4p_a_result['addr_ctry'].' - '.$g4p_a_result['addr_city'].' - '.$g4p_a_result['addr_addr'].'\'}';
}

    echo '[ { optionValue: \'\', optionDisplay: \'Nouvelle adresse\' } '.implode($json,',').' ]';

?>
