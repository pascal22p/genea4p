<?php
ini_set('xdebug.var_display_max_data',100) ;
ini_set('xdebug.var_display_max_depth',20);
error_reporting(E_ALL);

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'include_sys/g4p_class.php');

if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
{
    $g4p_mysqli=new g4p_mysqli();
    //$g4p_mon_indi->ignore_cache(true);
    //$g4p_indi=g4p_load_indi_infos($_GET['id']);
    $g4p_mon_indi=new g4p_individu($_GET['id']);
	$g4p_mon_indi->ignore_cache(true);
	$g4p_mon_indi->g4p_load();
    var_dump($g4p_mon_indi);
}

require_once($g4p_chemin.'pied_de_page.php');
?>
