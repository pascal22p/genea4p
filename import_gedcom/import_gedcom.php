<?php
@ini_set('xdebug.profiler_enable','1');
//xdebug_disable();

error_reporting(E_ALL);

header('Content-Type: text/html; charset=UTF-8');

$g4p_chemin='../';
//include('g4p_ged_grammar.php');
include('../p_conf/g4p_config.php');
include('../include_sys/sys_functions.php');
include('../include_sys/g4p_class.php');
include('import_gedcom_functions.php');
$sql_order=array('lock','addr','places','indis','familles','events','repo','obje',
    'sour_records','sour_cit','note','refn','relations','end');

@ini_set('auto_detect_line_endings','1');
session_start();
$g4p_mysqli=new g4p_mysqli();
ob_start();
$time_start = g4p_getmicrotime();

if(!isset($_POST['g4p_entete_lieux']) and !isset($_POST['savesql']) and !isset($_POST['init']))
{
    echo '<h1>Import Gedcom...</h1>';
    echo '<form class="formulaire" method="post" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'">';
    echo '<select name="g4p_gedcom_fichier">';
    $g4p_rep=opendir($g4p_chemin.'gedcoms');
    $file=array();
    while($g4p_fichier=readdir($g4p_rep))
    {
        if(is_file($g4p_chemin.'gedcoms/'.$g4p_fichier) and (eregi('.ged$',$g4p_fichier) or eregi('.zip$',$g4p_fichier)))
           $file[]='<option value="'.$g4p_fichier.'">'.$g4p_fichier.'</option>';
    }
    natcasesort($file);
    echo implode($file,"\n");
    echo '</select><br />';

    echo '<select name="g4p_base">';
    $sql="SELECT id, nom FROM genea_infos WHERE id<>0";
    $g4p_query=$g4p_mysqli->g4p_query($sql);
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
        foreach($g4p_result as $g4p_a_result)
            echo '<option value="'.$g4p_a_result['id'].'">'.$g4p_a_result['nom'].'</option>';
    echo '</select><br />';

    echo '<input type="hidden" name="init" value="1" /><br /><br />';
    echo '<input type="submit" value="Commencer l\'import" /></form>';
}

if(isset($_POST['init']))
{
    echo '<h1>Import en cours...</h1>';
    
    $g4p_base=$_POST['g4p_base'];
    g4p_gedcom2sql($_POST['g4p_gedcom_fichier']);
    $_SESSION['g4p_db_requetes']=$g4p_requetes;
    $_SESSION['liste_place']=g4p_check_import();
}

if(isset($_POST['g4p_entete_lieux']))
{
    $g4p_base=$_POST['g4p_base'];
    $g4p_requetes=$_SESSION['g4p_db_requetes'];
    $g4p_requetes['lock']=array();
    $g4p_requetes['lock']=array_merge($g4p_requetes['lock'],g4p_getmysql_ids());

    //places
    g4p_gedcom_saveplace();

    $_SESSION['g4p_db_requetes']=$g4p_requetes;

    echo '<h1>Requètes à insérer</h1>';
    echo '<div style="width:100%; height:400px;overflow:auto; font-size:x-small;">';
    foreach($sql_order as $groupe)
        if(isset($g4p_requetes[$groupe]))
            foreach($g4p_requetes[$groupe] as $arequete)
                echo htmlentities($arequete)."; <br />";
    echo '</div><br />';
    echo '<form class="formulaire" method="post" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'">';
    echo '<input type="hidden" name="savesql" value="1" />';
    echo '<input type="submit" value="Poursuivre l\'importation" /></form>';

}

if(isset($_POST['savesql']))
{
    $g4p_mysqli->autocommit(FALSE);
    $g4p_requetes=$_SESSION['g4p_db_requetes'];
    //enregistrement final
    foreach($sql_order as $groupe)
    {
        if(isset($g4p_requetes[$groupe]))
        {
            foreach($g4p_requetes[$groupe] as $arequete)
            {
                if($g4p_mysqli->g4p_query($arequete))
                    echo '<b>OK : </b>',$arequete."<br />";
                else
                {
                    echo '<b>',$arequete."</b><br />";
                    echo '<b>',mysqli_error(),'</b>';
                    $g4p_mysqli->rollback();
                    break 2;
                }
            }
        }
    }
    $g4p_mysqli->commit();
    echo '<h1>Fini!</h1>';
}
$time_end = g4p_getmicrotime();
$time = $time_end - $time_start;
echo 'chargée en : '.intval($time*1000).'ms';
?>