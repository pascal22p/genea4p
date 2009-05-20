<?php
$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'include_sys/sys_latex_functions.php');

$time_start = g4p_getmicrotime();

//on verifie que c'est bien la base de l'admin, si ce n'est pas un super-admin

$sql="SELECT id FROM genea_permissions WHERE id_membre=".$_SESSION['g4p_id_membre']." AND type=".$g4p_module_export['export_pdf_db.php']['permission']." AND permission=1 and (id_base='".$_POST['base_rapport']."' OR id_base='*')";
if($g4p_infos_req=g4p_db_query($sql))
{
  $g4p_result=g4p_db_result($g4p_infos_req);
  if(!$g4p_result)
  {
    echo $g4p_langue['acces_admin'];
    exit;
  }
}
else
{
  echo $g4p_langue['acces_admin'];
  exit;
}


$sql="SELECT nom FROM genea_infos WHERE id=".$_REQUEST['base'];
$g4p_infos_req=$g4p_mysqli->g4p_query($sql);
$g4p_base_select=$g4p_mysqli->g4p_result($g4p_infos_req);
$genea_db_nom=$g4p_base_select[0]['nom'];

// Les requètes SQL
$sql="SELECT indi_id
         FROM genea_individuals WHERE base=".(int)$_REQUEST['base']." AND indi_resn IS NULL 
         ORDER BY indi_nom, indi_prenom";
$g4p_infos_req=$g4p_mysqli->g4p_query($sql);
$g4p_liste_indis=$g4p_mysqli->g4p_result($g4p_infos_req);

// le serveur va souffrir... Si le cache est vide, des miliers de requètes vont être exécutés.

$file=uniqid();
//$file='test';
$latex=fopen('/tmp/'.$file.'.tex','w');
fwrite($latex,g4p_latex_write_header());

foreach($g4p_liste_indis as $indi_id)
{
    $indi_id=$indi_id['indi_id'];
    $g4p_indi=g4p_load_indi_infos($indi_id);
    g4p_latex_write_indi($g4p_indi);
}

fwrite($latex,'\end{document}');
fclose($latex);

shell_exec('sed -i \'s/\$//\' /tmp/'.$file.'.tex');
shell_exec('sed -i \'s/\&//\' /tmp/'.$file.'.tex');

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$output=shell_exec('xelatex -interaction=nonstopmode -output-directory=/tmp /tmp/'.$file.'.tex');
if(file_exists('/tmp/'.$file.'.pdf'))
{
    header('Content-type: application/pdf');
    readfile('/tmp/'.$file.'.pdf');
}
else
{
    header('Content-Type: text/plain');
    echo $output;
}


?>



