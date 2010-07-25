<?php

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
