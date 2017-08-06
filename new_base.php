<?php
$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

if(!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    die('Unsufficient right to add a new genealogy database');

if(!empty($_POST) and !empty($_POST['nom']))
{
    $success=true;
    
    $sql="INSERT INTO genea_infos (nom, descriptif) VALUES ('".$g4p_mysqli->escape_string($_POST['nom'])."','".$g4p_mysqli->escape_string($_POST['description'])."')";
    if(!$error=$g4p_mysqli->g4p_query($sql))
    {
        $success=false;
        $error_m='Erreur lors de la création de la base '.$_POST['nom'];
    }
    $g4p_new_id=$g4p_mysqli->insert_id;
        
    if($error==='Error:1062')
    {
        $success=false;
        $error_m='Erreur la base '.$_POST['nom'].' existe déjà';
    }
        
    if($success and !is_dir($g4p_chemin.'cache'))
    {
        if(!mkdir($g4p_chemin.'cache',0755))
        {
            $success=false;
            $error_m='impossible de créer le répertoire : /cache';
        }
        else
        {
            if($f=fopen($g4p_chemin.'cache/'.$_POST['nom'].'/.htaccess','w'))
            {
                fwrite($f,"<Files *.txt>\ndeny from all\n</Files>\noptions -Indexes"); 
                fclose($f);
            }
            else
            {
                $success=false;
                $error_m='impossible de créer le fichier : /cache/.htaccess';
            } 
        }
    }
      
    if($success)
    {
        if(!mkdir($g4p_chemin.'cache/'.g4p_base_namefolder($_POST['nom']).$g4p_new_id,0755))
        {
            $success=false;
            $error_m='impossible de créer le répertoire : cache/'.g4p_base_namefolder($_POST['nom']).$g4p_new_id;
        } 
        if(!mkdir($g4p_chemin.'cache/'.g4p_base_namefolder($_POST['nom']).$g4p_new_id.'/medias',0755))
        {
            $success=false;
            $error_m='impossible de créer le répertoire : cache/'.g4p_base_namefolder($_POST['nom']).$g4p_new_id.'/medias';
        } 
    }
}

require_once($g4p_chemin.'entete.php');
echo '<div class="box_title"><h2>'.$g4p_langue['a_index_new_base_titre'].'</h2></div>'."\n";

if(isset($success) and $success===true)
    echo '<div class="success">Base '.$_POST['nom'].' créée avec succès</div>';
elseif(isset($success) and $success===false)
    echo'<div class="error">'.$error_m.'</div>';

echo '<form class="formulaire" method="post" action="'.$_SERVER['PHP_SELF'].'" name="new_base">';
  echo $g4p_langue['a_index_new_base_nom'],'<input name="nom" type="text" value="',@$_POST['nom'],'" /><br />';
  echo $g4p_langue['a_index_new_base_descr'],'<br /><textarea style="height:150px; width:95%" name="description">',@$_POST['description'],'</textarea><br />';
  echo '<input type="submit" value="',$g4p_langue['submit_ajouter'],'" /></form>';
echo '</div>';

require_once($g4p_chemin.'pied_de_page.php');

?>

