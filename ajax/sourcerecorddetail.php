<?php

$g4p_chemin='../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'p_conf/script_start.php');
	
if(!empty($_GET['value']))
{
    $g4p_query=$g4p_mysqli->g4p_query("SELECT sour_records_id, sour_records_auth, sour_records_title, 
		sour_records_abbr, sour_records_publ, repo_caln, sour_records_agnc, nom, repo_name, repo_medi 
		FROM genea_sour_records
		LEFT JOIN genea_infos ON id=base
		LEFT JOIN genea_repository USING (repo_id)
		WHERE sour_records_id=".(int)$_GET['value']."
		ORDER BY nom, repo_name, sour_records_title");
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
    {
		foreach($g4p_result[0] as $key=>$val)
			$tmp[]=$key.':"'.addslashes($val).'"';
		echo '{'.implode($tmp,',').'}';
    }		
	
}

?>
