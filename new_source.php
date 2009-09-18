<?php
$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');

$g4p_javascript='<script src="javascript/jquery/jquery-1.3.1.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.bgiframe.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.dimensions.js"></script>
  <script type="text/javascript" src="javascript/jquery/jquery.autocomplete.min.js"></script>
  <script>
  $(document).ready(function(){
    $("#g4p_id_source").autocomplete(\'ajax/autocomplete_source.php\', {max:40}).result(callback);
  });
  
    function callback(event, data, formatted)
    {
        if (data) {
            $("#g4p_id_source").attr("value", data[6]);
            $("#source").empty();
            $("#source").append("Page : "+data[2]+"<br />");
            $("#source").append(data[3]+"<br />");
            $("#source").append("Titre source : "+data[1]+"<br />");
            $("#source").append("Référence : "+data[5   ]+"<br />");
            $("#source").append("Dépôt : "+data[4]);
            $("#source").show();
        }
    }    
  </script>
';

require_once($g4p_chemin.'entete.php');
echo '<div class="box_title"><h2>Ajouter une source</h2></div>'."\n";

////////////////////////////////////
//////  ajout d'une source
////////////////////////////////////

if(empty($_REQUEST['parent']) or empty($_REQUEST['id_parent']))
    die('A parameter is missing');
    
$id_parent=(int)$_REQUEST['id_parent'];
if(empty($id_parent))
    die('Error on parameter');
$g4p_indi=g4p_load_indi_infos($id_parent);

$tables=array(
    'INDI'=>'rel_indi_sources',
    'EVENT'=>'rel_events_sources',
    'FAMILLE'=>'rel_famille_sources');
$colonnes=array(
    'INDI'=>'indi_id',
    'EVENT'=>'events_details_id',
    'FAMILLE'=>'familles_id');
    
if(!isset($tables[$_REQUEST['parent']]))
    die('Type de note inconnu');
else
{
    $table=$tables[$_REQUEST['parent']];
    $parentcol=$colonnes[$_REQUEST['parent']];
}

if(!empty($_POST['g4p_id_source']))
{
    $sql="INSERT INTO ".$table." (sour_citations_id, ".$parentcol.") VALUES 
        (".(int)$_POST['g4p_id_source'].",".$id_parent.")";
    if($result=$g4p_mysqli->g4p_query($sql))
    {
        if($result===true)
            echo '<div class="success">Source liée avec succès</div>';
        elseif($result=='Error:1062')
            echo '<div class="error">Source déjà liée</div>';
    }
    else
        echo '<div class="error">Erreur lors de la création du lien</div>';
}
elseif(!empty($_POST['g4p_source']))
{
    $g4p_mysqli->autocommit(false);
    $sql="INSERT INTO genea_notes (notes_text, base) VALUES    
        ('".mysql_escape_string($_POST['g4p_note'])."', ".(int)$_POST['g4p_base'].")";
    if($g4p_mysqli->g4p_query($sql))
    {
        
        $sql="INSERT INTO ".$table." (notes_id, ".$parentcol.") VALUES 
            (".$g4p_mysqli->insert_id.",".$id_parent.")";
        if($result=$g4p_mysqli->g4p_query($sql))
        {
            if($result===true)
            {
                echo 'Note crée et liée avec succès';
                $g4p_mysqli->commit();
            }
            elseif($result=='Error:1062')
            {
                echo 'Note déjà liée';
                $g4p_mysqli->rollback();
            }
        }
        else
        {
            echo 'Erreur lors de la création du lien';
            $g4p_mysqli->rollback();
        }
    }
}
   
echo '
    <div class="box">
    <div class="box_title">Lier une source existante</div>
    <form class="formulaire" method="post" action="',$_SERVER['PHP_SELF'],'" name="link_source">
    <ul>
    <li>Titre : <input id="g4p_id_source" name="g4p_id_source" style="width:400px" type="text"  /></li>
    </ul>
    <div class="box" style="display:none" id="source"></div>
    <input name="id_parent" type="hidden" value="',$_REQUEST['id_parent'],'" />
    <input name="parent" type="hidden" value="',$_REQUEST['parent'],'" />
    <input type="submit" value="Lier la source" /></form>
    </div>
    
    <div class="box">
    <div class="box_title">Ajouter une nouvelle source</div>
    <form class="formulaire" method="post" action="',$_SERVER['PHP_SELF'],'" name="ajout_note"><ul>';
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
    echo '</li>';
    echo '<li><textarea rows="8" cols="50" name="g4p_note" ></textarea></li>';
    echo '<input name="id_parent" type="hidden" value="',$_REQUEST['id_parent'],'" />
        <input name="parent" type="hidden" value="',$_REQUEST['parent'],'" />
        <li><input type="submit" value="Ajouter la note" /></li></form>';
echo '</div>';


require_once($g4p_chemin.'pied_de_page.php');
?>