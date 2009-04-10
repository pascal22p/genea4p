<?php
################################################################
#       Ce script est éxecuté a chaque début de script         #
################################################################
error_reporting(E_ALL);//& (~E_NOTICE)
setlocale(LC_CTYPE,'UTF8');
ini_set('xdebug.var_display_max_data',100) ;
ini_set('xdebug.var_display_max_depth',20);

require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'include_sys/g4p_class.php');

//démarrage de la session
session_start();

//lancement de la connexion sql
$g4p_mysqli=new g4p_mysqli();

//id de la base de donnée
if(!empty($_GET['genea_db_id']))
{
    unset($_SESSION['indi_id']);
    $_SESSION['genea_db_id']=$_GET['genea_db_id'];
}
if(empty($_SESSION['genea_db_id']))
    $_SESSION['genea_db_id']=1;

/*
if(!empty($_REQUEST['id_pers']))
	$_SESSION['indi_id']=$_REQUEST['id_pers'];
if(!empty($_SESSION['indi_id']))
    $g4p_indi=g4p_load_indi_infos($_SESSION['indi_id']);
   */


// selection du fichier de langue
if(!isset($_SESSION['langue']))
{
  if($g4p_config['g4p_type_install']=='seule' or $g4p_config['g4p_type_install']=='seule-mod_rewrite')
  {
    /* config pour install seule */
    if(!empty($_COOKIE['genea4p']['langue']) and file_exists($g4p_chemin.'languages/lang-'.$_COOKIE['genea4p']['langue'].'.php'))
      $_SESSION['langue']=$_COOKIE['genea4p']['langue'];
    else
      $_SESSION['langue']=$g4p_config['default_lang'];
  }
  elseif($g4p_config['g4p_type_install']=='module-npds' or $g4p_config['g4p_type_install']=='module-npds-mod_rewrite')
  {
    /* config pour npds */
    $g4p_npds_langue=array(
      'french'=>'fr-utf8'
    );
    $_SESSION['langue']=$g4p_npds_langue[$language];
  }
}
if(file_exists($g4p_chemin.'languages/lang-'.$_SESSION['langue'].'.php'))
  require_once($g4p_chemin.'languages/lang-'.$_SESSION['langue'].'.php');
else
  require_once($g4p_chemin.'languages/lang-'.$g4p_config['default_lang'].'.php');

// selection du thème
if(empty($_SESSION['theme']))
{
  if($g4p_config['g4p_type_install']=='seule' or $g4p_config['g4p_type_install']=='seule-mod_rewrite')
  {
    /* config pour install seule */
    if(!empty($_COOKIE['genea4p']['theme']) and file_exists($g4p_chemin.'styles/'.$_COOKIE['genea4p']['theme'].'/style.css'))
      $_SESSION['theme']=$_COOKIE['genea4p']['theme'];
    else
      $_SESSION['theme']=$g4p_config['default_theme'];
  }
  elseif($g4p_config['g4p_type_install']=='module-npds' or $g4p_config['g4p_type_install']=='module-npds-mod_rewrite')
  {
    /* config pour npds */
    /* a faire
      $g4p_npds_theme=array(
      'french'=>'fr-utf8'
    );*/
    $_SESSION['theme']='default';
  }
}

//sélection du profil des lieux
if(empty($_SESSION['place']))
{
  /* config pour install seule */
  if(!empty($_COOKIE['genea4p']['place']))
    $_SESSION['place']=$_COOKIE['genea4p']['place'];
  else
    $_SESSION['place']=$g4p_config['subdivision_affichage_default'];
}


if(!empty($g4p_force_iso))
  $g4p_langue['entete_charset']='ISO 8859-1';

$time_start = g4p_getmicrotime();

$_POST=strip_slashes($_POST); //gère le magic_quote (stripslashes et utf8encode si necessaire)
$_GET=strip_slashes($_GET); //gère le magic_quote (stripslashes et utf8encode si necessaire)
$_COOKIE=strip_slashes($_COOKIE); //gère le magic_quote (stripslashes et utf8encode si necessaire)

$g4p_error=0;
/* config pour une install seule */

if(!isset($_SESSION['g4p_id_membre']))
{
// parsing du cookie    
if(isset($_COOKIE['genea4p']['g4p_id_membre']) and isset($_COOKIE['genea4p']['g4p_membre']))
{
  $_COOKIE['genea4p']['g4p_id_membre']=intval($_COOKIE['genea4p']['g4p_id_membre']);
  $sql="SELECT email FROM genea_membres WHERE id=".$_COOKIE['genea4p']['g4p_id_membre'];
  if($g4p_result=$g4p_mysqli->g4p_query($sql))
  {
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    {
      $g4p_result=$g4p_result[0];
      if(md5($g4p_result['email'])==$_COOKIE['genea4p']['g4p_membre'])
      {
        unset($_SESSION['permission']);
        $_SESSION['g4p_id_membre']=$_COOKIE['genea4p']['g4p_id_membre'];
        $_SESSION['message']=$g4p_langue['connect_bienvenue'];
      }
      else
        $g4p_error=1;
    }
    else
      $g4p_error=1;
  }
  else
    $g4p_error=1;
}
else
  $g4p_error=1;
}

if($g4p_error)
{
  $_SESSION['g4p_id_membre']=1;
  setcookie('genea4p[langue]','',0,'/','',0);
  setcookie('genea4p[g4p_membre]','',0,'/','',0);
  setcookie('genea4p[g4p_id_membre]','',0,'/','',0);
}


if(isset($_GET['genea_db_id']) and (isset($_SESSION['genea_db_id']) and $_SESSION['genea_db_id']!=$_GET['genea_db_id'] or !isset($_SESSION['genea_db_id']))) //changement de base
{
  $_SESSION['genea_db_id']=$_GET['genea_db_id'];
  $sql="SELECT nom FROM genea_infos WHERE id=".$_SESSION['genea_db_id']." LIMIT 1";
  $g4p_query=$g4p_mysqli->g4p_query($sql);
  if ($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
    $_SESSION['genea_db_nom']=$g4p_result[0]['nom'];
  unset($_SESSION['permission']);
  unset($_SESSION['historic']);
}
elseif(empty($_SESSION['genea_db_id']))
{
  if(($g4p_config['g4p_type_install']=='module-npds' or $g4p_config['g4p_type_install']=='module-npds-mod_rewrite') and !empty($cookie[1]))
    $sql="SELECT id, nom FROM genea_infos WHERE nom='".mysql_escape_string($cookie[1])."' LIMIT 1";
  else
    $sql="SELECT id, nom FROM genea_infos ORDER BY nom LIMIT 1";
  $g4p_query=$g4p_mysqli->g4p_query($sql);
  
  if ($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
  {
    $_SESSION['genea_db_id']=$g4p_result[0]['id'];
    $_SESSION['genea_db_nom']=$g4p_result[0]['nom'];
  }
  else
  {
    $sql="SELECT id, nom FROM genea_infos ORDER BY nom LIMIT 1";
    $g4p_query=$g4p_mysqli->g4p_query($sql);
    if ($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
    {
      $_SESSION['genea_db_id']=$g4p_result[0]['id'];
      $_SESSION['genea_db_nom']=$g4p_result[0]['nom'];
    }
    else
    {
      if(!(isset($_GET['g4p_opt']) and $_GET['g4p_opt']=='new_base' or substr($_SERVER['PHP_SELF'],-11)=='connect.php'))
      {
        require_once($g4p_chemin.'p_conf/g4p_config.php');
        require_once($g4p_chemin.'entete.php');
          $_SESSION['permission']=new g4p_permission();
          $_SESSION['permission']->g4p_load_permission($_SESSION['g4p_id_membre']);
        echo '<h2>Aucune base éxistante</h2>';
        echo '<h3>Veuillez créer une base de données pour pouvoir importer votre fichier GEDCOM et/ou créer votre généalogie</h3>';
        require_once($g4p_chemin.'pied_de_page.php');
        exit();
      }
    }
  }
}
elseif(!empty($_SESSION['genea_db_id']) and empty($_SESSION['genea_db_nom']))
{
  $sql="SELECT nom FROM genea_infos WHERE id=".$_SESSION['genea_db_id']." LIMIT 1";
  $g4p_query=$g4p_mysqli->g4p_query($sql);
  if ($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
    $_SESSION['genea_db_nom']=$g4p_result[0]['nom'];
}



//vérif des répertoires de cache:
if(!is_dir($g4p_chemin.'cache'))
{
  if(!mkdir($g4p_chemin.'cache',0755))
    $_SESSION['message'].='impossible de créer le répertoire : cache<br />';
  else
  {
    if($f=fopen($g4p_chemin.'cache/.htaccess','w'))
    {
      fwrite($f,"<Files *.txt>\ndeny from all\n</Files>\noptions -Indexes"); 
      fclose($f);
    }
    else
      $_SESSION['message'].='Impossible de créer le fichier cache/.htaccess';
    mkdir($g4p_chemin.'cache/liste_patronymes',0755);
    mkdir($g4p_chemin.'cache/cartographie',0755);
  }
}
if(!is_dir($g4p_chemin.'cache/liste_patronymes'))
  if(!mkdir($g4p_chemin.'cache/liste_patronymes',0755))
    $_SESSION['message'].='impossible de créer le répertoire : cache/liste_patronymes<br />';

if(!empty($_SESSION['genea_db_nom']))
{
  if($g4p_langue['entete_charset']=='UTF-8')
    $g4p_iso_genea_db_nom=utf8_decode($_SESSION['genea_db_nom']);  
  else
    $g4p_iso_genea_db_nom=$_SESSION['genea_db_nom'];  
    
  if(!is_dir($g4p_chemin.'cache/'.$g4p_iso_genea_db_nom))
  {
    if(!mkdir($g4p_chemin.'cache/'.$g4p_iso_genea_db_nom,0755))
      $_SESSION['message'].='impossible de créer le répertoire : cache/'.$_SESSION['genea_db_nom'].'<br />';
  }
  if(!is_dir($g4p_chemin.'cache/'.$g4p_iso_genea_db_nom.'/fichiers'))
    if(!mkdir($g4p_chemin.'cache/'.$g4p_iso_genea_db_nom.'/fichiers',0755))
      $_SESSION['message'].='impossible de créer le répertoire : cache/'.$_SESSION['genea_db_nom'].'/fichiers<br />';
  if(!is_dir($g4p_chemin.'cache/'.$g4p_iso_genea_db_nom.'/objets'))
    if(!mkdir($g4p_chemin.'cache/'.$g4p_iso_genea_db_nom.'/objets',0755))
      $_SESSION['message'].='impossible de créer le répertoire : cache/'.$_SESSION['genea_db_nom'].'/objets<br />';      
}

  $_SESSION['permission']=new g4p_permission();
  $_SESSION['permission']->g4p_load_permission($_SESSION['g4p_id_membre']);
  
//historique des pages
/*
if(!isset($_SESSION['historic']['url']) or !is_array($_SESSION['historic']['url']))
  $_SESSION['historic']['url']=array();

$g4p_page_actuelle=str_replace(dirname($_SERVER['PHP_SELF']).'/','',$_SERVER['REQUEST_URI']);
if(isset($_SESSION['historic']['url'][0]) and $_SESSION['historic']['url'][0]!=$g4p_page_actuelle)
{
  array_unshift($_SESSION['historic']['url'],$g4p_page_actuelle);       
  if(count($_SESSION['historic']['url'])>5)
    array_pop($_SESSION['historic']['url']);
}
*/
?>
