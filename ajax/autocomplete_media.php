<?php

$g4p_chemin='../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'p_conf/script_start.php');
	
if(!empty($_GET['q']))
{
    $_GET['q']='%'.str_replace(' ','%',mysql_escape_string($_GET['q'])).'%';
    $g4p_query=$g4p_mysqli->g4p_query("SELECT DISTINCT sour_citations_id, sour_citations_page,
        sour_citations_data_text, repo_name, sour_records_title, repo_caln
        FROM genea_sour_records
        LEFT JOIN genea_sour_citations USING (sour_records_id)
        LEFT JOIN genea_repository USING (repo_id)
        WHERE sour_records_title LIKE '".$_GET['q']."' 
        ORDER BY sour_records_title
        LIMIT 40   ");
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
    {
        $mask   = array("\r\n", "\n", "\r");
        $replace = '<br />';
        foreach($g4p_result as $g4p_a_result)
        {
            echo $g4p_a_result['sour_records_title'].', page : '.
                    $g4p_a_result['sour_citations_page'].
                    ' , Rérérence : '.$g4p_a_result['repo_caln'].'|'.
                $g4p_a_result['sour_records_title'].'|'.
                $g4p_a_result['sour_citations_page'].'|'.
                str_replace($mask, $replace, $g4p_a_result['sour_citations_data_text']).'|'.
                str_replace($mask, $replace, $g4p_a_result['repo_name']).'|'.
                $g4p_a_result['repo_caln'].'|'.
                $g4p_a_result['sour_citations_id']."\n";
        }
    }
}

?>
