<?php

$g4p_chemin='../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'p_conf/script_start.php');
	
if(!empty($_GET['q']))
{
    $_GET['q']='%'.str_replace(' ','%',mysql_escape_string($_GET['q'])).'%';
    $g4p_query=$g4p_mysqli->g4p_query("SELECT media_id, media_title, media_format, media_file
        FROM genea_multimedia
        WHERE media_title LIKE '".$_GET['q']."' 
        ORDER BY media_title
        LIMIT 40   ");
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
    {
        $mask   = array("\r\n", "\n", "\r");
        $replace = '<br />';
        foreach($g4p_result as $g4p_a_result)
        {
            echo $g4p_a_result['media_id'].' - '.$g4p_a_result['media_title'].' - '.$g4p_a_result['media_file'].'|'.
				$g4p_a_result['media_id'].'|'.
                $g4p_a_result['media_title'].'|'.
                $g4p_a_result['media_format'].'|'.
                $g4p_a_result['media_file']."\n";
        }
    }
}

?>
