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
	
if(!empty($_GET['q']))
{
    $_GET['q']='%'.str_replace(' ','%',mysql_escape_string($_GET['q'])).'%';
    $g4p_query=$g4p_mysqli->g4p_query("SELECT DISTINCT notes_id, notes_text
        FROM genea_notes
        WHERE notes_text LIKE '".$_GET['q']."' 
        ORDER BY notes_text
        LIMIT 40   ");
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
    {
        $mask   = array("\r\n", "\n", "\r");
        $replace = '<br />';
        foreach($g4p_result as $g4p_a_result)
        {
            echo str_replace($mask, $replace, $g4p_a_result['notes_text']).'|'.$g4p_a_result['notes_id']."\n";
        }
    }
}

?>
