<?php
$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');

$g4p_javascript='<script type="text/javascript" src="javascript/jquery/jquery-1.3.1.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.bgiframe.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.dimensions.js"></script>
  <script type="text/javascript" src="javascript/jquery/jquery.autocomplete.min.js"></script>
  <script type="text/javascript">
  /* <![CDATA[ */  
  $(document).ready(function(){
    $("#g4p_id_note").autocomplete(\'ajax/autocomplete_note.php\', {max:40}).result(callback);
  });
  
    function callback(event, data, formatted)
    {
        if (data) {
            $("#g4p_id_note").attr("value", data[1]);
            $("#note").empty();
            $("#note").append(data[0]);
            $("#note").show();
        }
    }    
  /* ]]> */	
  </script>
';

require_once($g4p_chemin.'entete.php');
echo '<div class="box_title"><h2>Ajouter une note</h2></div>'."\n";

////////////////////////////////////
//////  ajout d'une notes
////////////////////////////////////

if(empty($_REQUEST['parent']) or empty($_REQUEST['id_parent']))
    die('A parameter is missing');
    
$id_parent=(int)$_REQUEST['id_parent'];
if(empty($id_parent))
    die('Error on parameter');

$tables=array(
    'INDI'=>'rel_indi_notes',
    'EVENT'=>'rel_events_notes',
    'FAMILLE'=>'rel_familles_notes',
    'MEDIA'=>'rel_multimedia_notes',
    'PLACE'=>'rel_place_notes',
    'REPO'=>'rel_repo_notes',
    'SCITE'=>'rel_sour_citations_notes',
    'SRECORD'=>'rel_sour_records_notes');
$colonnes=array(
    'INDI'=>'indi_id',
    'EVENT'=>'events_details_id',
    'FAMILLE'=>'familles_id',
    'MEDIA'=>'media_id',
    'PLACE'=>'place_id',
    'REPO'=>'repo_id',
    'SCITE'=>'sour_citations_id',
    'SRECORD'=>'sour_records_id');
$origin_tables=array(
    'INDI'=>'genea_individuals',
    'EVENT'=>'genea_events_details',
    'FAMILLE'=>'genea_familles');

if(!isset($tables[$_REQUEST['parent']]))
    die('Type de note inconnu');
else
{
    $table=$tables[$_REQUEST['parent']];
	$origin_table=$origin_tables[$_REQUEST['parent']];	
    $parentcol=$colonnes[$_REQUEST['parent']];
}
    
// recuperation de la base
//stocké dans $g4p_base et $g4p_base_nom
$g4p_query=$g4p_mysqli->g4p_query("SELECT base, nom FROM  $origin_table
	LEFT JOIN genea_infos on id=base
	WHERE $parentcol=$id_parent");
if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
{
	$g4p_base=$g4p_result[0]['base'];
	$g4p_base_nom=$g4p_result[0]['nom'];

}

if(!empty($_POST['g4p_id_note']))
{
    $sql="INSERT INTO ".$table." (notes_id, ".$parentcol.") VALUES 
        (".(int)$_POST['g4p_id_note'].",".$id_parent.")";
    if($result=$g4p_mysqli->g4p_query($sql))
    {
        if($result===true)
            echo '<div class="success">Note liée avec succès</div>';
        elseif($result=='Error:1062')
            echo '<div class="error">Note déjà liée</div>';
    }
    else
        echo '<div class="error">Erreur lors de la création du lien</div>';
}
elseif(!empty($_POST['g4p_note']))
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
                echo '<div class="success">Note crée et liée avec succès</div>';
                $g4p_mysqli->commit();
            }
            elseif($result=='Error:1062')
            {
                echo '<div class="error">Note déjà liée</div>';
                $g4p_mysqli->rollback();
            }
        }
        else
        {
            echo '<div class="error">Erreur lors de la création du lien</div>';
            $g4p_mysqli->rollback();
        }
    }
}
   
echo '
    <div class="box">
    <div class="box_title">Lier une note existante</div>
    <form class="formulaire" method="post" action="',$_SERVER['PHP_SELF'],'" name="link_note">
    <ul>
    <li>Note : <input id="g4p_id_note" name="g4p_id_note" style="width:400px" type="text"  /></li>
    </ul>
    <div class="box" style="display:none" id="note"></div>
    <input name="id_parent" type="hidden" value="',$_REQUEST['id_parent'],'" />
    <input name="parent" type="hidden" value="',$_REQUEST['parent'],'" />
    <input type="submit" value="Lier la note" /></form>
    </div>
    
    <div class="box">
    <div class="box_title">Ajouter une nouvelle note</div>
    <form class="formulaire" method="post" action="',$_SERVER['PHP_SELF'],'" name="ajout_note"><ul>';
    echo '<li>Base généalogique : <select name="g4p_base" id="g4p_base" >';
	echo '<option value="'.$g4p_base.'">'.$g4p_base_nom.'</option></select>';
/*    $g4p_query=$g4p_mysqli->g4p_query("SELECT id, nom FROM genea_infos ORDER BY nom");
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
    {
        foreach($g4p_result as $g4p_a_result)
        {
            $select=($g4p_a_result['id']==$_SESSION['genea_db_id'])?('selected="selected"'):('');
            echo '<option value="'.$g4p_a_result['id'].'"  '.$select.'>'.$g4p_a_result['nom'].'</option>';
        }
    }*/
    echo '</li>';
    echo '<li><textarea rows="8" cols="50" name="g4p_note" ></textarea></li>';
    echo '<li><input name="id_parent" type="hidden" value="',$_REQUEST['id_parent'],'" />
        <input name="parent" type="hidden" value="',$_REQUEST['parent'],'" />
		<input type="submit" value="Ajouter la note" /></li></ul></form>';
echo '</div>';


require_once($g4p_chemin.'pied_de_page.php');
?>