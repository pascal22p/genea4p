<?php
$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

$g4p_javascript='<script src="javascript/jquery/jquery-3.6.0.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.bgiframe.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.dimensions.js"></script>
  <script type="text/javascript" src="javascript/jquery/jquery.autocomplete.min.js"></script>
  <script>
  $(document).ready(function(){
    $("#place_ville").autocomplete(\'ajax/autocomplete_place.php\', {max:40}).result(callback);
  });
  
    function callback(event, data, formatted)
    {
        if (data) {
            $("#place_ville").attr("value", data[0]);
            $("#place_insee").attr("value", data[1]);
            $("#place_dept").attr("value", data[2]);
            $("#place_region").attr("value", data[3]);
            $("#place_pays").attr("value", data[4]);
            $("#place_longitude").attr("value", data[5]);
            $("#place_latitude").attr("value", data[6]);
        }
    }  
  </script>
';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'p_conf/script_start.php');
	
If(!$_SESSION['permission']->permission[_PERM_EDIT_FILES_])
	g4p_error($g4p_langue['acces_non_autorise']);

//var_dump($g4p_indi);

if(empty($_POST))
{
    require_once($g4p_chemin.'entete.php');
    echo '<div class="box_title"><h2>Ajout d\'un nouveau dépôt</h2></div>'."\n";

    echo '<div class="box">';
    echo '<form class="formulaire" method="post" enctype="multipart/form-data" action="',$_SERVER['PHP_SELF'],'" name="ajout_place"><ul>';
    echo '<li>Base généalogique : <select name="g4p_base" id="g4p_base" />';
    $g4p_query=$g4p_mysqli->g4p_query("SELECT id, nom FROM genea_infos ORDER BY nom");
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
    {
        foreach($g4p_result as $g4p_a_result)
        {
            $select=($g4p_a_result['id']==$_SESSION['genea_db_id'])?('selected="selected"'):('');
            echo '<option value="'.$g4p_a_result['id'].'"  '.$select.'>'.$g4p_a_result['nom'].'</option>';
        }
    }
    echo '</select></li>';

    echo '<li>Lieu dit : <input type="text" id="place_lieudit" name="place_lieudit" value="" size="40" /></li>';
    echo '<li>Commune : <input type="text" id="place_ville" name="place_ville" value="" size="40" /></li>';
    echo '<li>Code postal : <input type="text" id="place_cp" name="place_cp" value="" size="40" /></li>';
    echo '<li>Numéro insee : <input type="text" id="place_insee" name="place_insee" value="" size="40" /></li>';
    echo '<li>Département : <input type="text" id="place_dept" name="place_dept" value="" size="40" /></li>';
    echo '<li>Région : <input type="text" id="place_region" name="place_region" value="" size="40" /></li>';
    echo '<li>Pays : <input type="text" id="place_pays" name="place_pays" value="" size="40" /></li>';
    echo '<li>Longitude : <input type="text" id="place_longitude" name="place_longitude" value="" size="40" /></li>';
    echo '<li>Latitude : <input type="text" id="place_latitude" name="place_latitude" value="" size="40" /></li>';

    echo '</ul>';
    echo '<input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form>
      </div>';
    echo '</div>';


    require_once($g4p_chemin.'pied_de_page.php');    
}
else
{
    if(!$_POST['repo_name'])
    {
      //$_SESSION['message']='Veuillez remplir au moins un des champs';
      //header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_fams&g4p_id='.$g4p_last_id,0,0));
      //break;
      echo 'Veuillez remplir Le nom du dépôt';
      exit;
    }
    
    if($_POST['g4p_adress_db']=='' and $_POST['addr_addr'] and $_POST['addr_city'] and $_POST['addr_post'] and $_POST['addr_stae'] 
        and $_POST['addr_ctry'])
    {
        echo 'Faut remplir un mimimum les cases';
        exit;
    }
    
    if(empty($_POST['g4p_adress_db']))
    {
        $g4p_mysqli->autocommit(false);
        //On ajout une nouvelle adresse
        $sql="INSERT INTO genea_address (addr_addr, addr_city, addr_post, addr_stae, addr_ctry, 
            addr_phon1, addr_phon2, addr_phon3, 
            addr_email1, addr_email2, addr_email3, 
            addr_fax1, addr_fax2, addr_fax3, 
            addr_www1, addr_www2, addr_www3,base)
            VALUES ('".mysql_escape_string($_POST['addr_addr'])."','".mysql_escape_string($_POST['addr_city'])."','".
            mysql_escape_string($_POST['addr_post'])."','".mysql_escape_string($_POST['addr_stae'])."','".
            mysql_escape_string($_POST['addr_ctry'])."','".mysql_escape_string($_POST['addr_phon1'])."','".
            mysql_escape_string($_POST['addr_phon2'])."','".mysql_escape_string($_POST['addr_phon3'])."','".
            mysql_escape_string($_POST['addr_email1'])."','".mysql_escape_string($_POST['addr_email2'])."','".
            mysql_escape_string($_POST['addr_email3'])."','".mysql_escape_string($_POST['addr_fax1'])."','".
            mysql_escape_string($_POST['addr_fax2'])."','".mysql_escape_string($_POST['addr_fax3'])."','".
            mysql_escape_string($_POST['addr_www1'])."','".mysql_escape_string($_POST['addr_www2'])."','".
            mysql_escape_string($_POST['addr_www3'])."',".(int)$_POST['g4p_base'].")";
        if($g4p_mysqli->g4p_query($sql))
        {
            $sql="INSERT INTO genea_repository (repo_name, addr_id, base)
                VALUES ('".mysql_escape_string($_POST['repo_name'])."',".$g4p_mysqli->insert_id.",".(int)$_POST['g4p_base'].")";
            if($g4p_mysqli->g4p_query($sql))
            {
                if($g4p_mysqli->commit())
                    $_SESSION['message']=$g4p_langue['message_repo_enr'];
            }
            else
            {
                $g4p_mysqli->rollback();
                $_SESSION['message']='Erreur lors de l\'insertion du dépot, rollback';
            }
        }
        else
            $_SESSION['message']='Erreur lors de l\'insertion du dépot';
    }
    else
    {
        $sql="INSERT INTO genea_repository (repo_name, addr_id, base)
            VALUES ('".mysql_escape_string($_POST['repo_name'])."',".(int)$_POST['g4p_adress_db'].",".(int)$_POST['g4p_base'].")";
        if($g4p_mysqli->g4p_query($sql))
        {
            $_SESSION['message']=$g4p_langue['message_repo_enr'];
        }
        else
        {
            $_SESSION['message']='Erreur lors de l\'insertion du dépot, rollback';
        }
    }
    

    header('location:'.g4p_make_url('','index.php','',0,0));
}




?>
