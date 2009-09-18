<?php
$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

if(!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    die('Unsufficient right to delete a new genealogy database');

if(!empty($_GET['id']))
{
    $sql="SELECT nom FROM genea_infos WHERE id=".(int)$_GET['id'];
    if($g4p_infos_req=$g4p_mysqli->g4p_query($sql))
    {
        $g4p_base_nom=$g4p_mysqli->g4p_result($g4p_infos_req);
        $g4p_base_nom=$g4p_base_nom[0]['nom'];
        if(deleteDirectory('cache/'.g4p_base_namefolder($g4p_base_nom).(int)$_GET['id']))
        {
            $sql="DELETE FROM genea_infos WHERE id=".(int)$_GET['id'];
            $g4p_mysqli->g4p_query($sql);
        }
    }
}

header('location:'.g4p_make_url('','index.php','',0,0));

?>

