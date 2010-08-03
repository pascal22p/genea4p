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
    $("#g4p_id_media").autocomplete(\'ajax/autocomplete_media.php\', {max:40}).result(callback);
  });
  
    function callback(event, data, formatted)
    {
        if (data) {
            $("#g4p_id_media").attr("value", data[0]);
            $("#media").empty();
            $("#media").append("Base : "+data[1]+"<br />");
            $("#media").append("Titre : "+data[2]+"<br />");
            $("#media").append("Format : "+data[3]+"<br />");
            $("#media").append("File : "+data[4]+"<br />");
            $("#media").show();
        }
    }    
  /* ]]> */	
  </script>
';
require_once($g4p_chemin.'entete.php');
echo '<div class="box_title"><h2>Ajouter un média</h2></div>'."\n";

////////////////////////////////////
//////  ajout d'un media
////////////////////////////////////

if(empty($_REQUEST['parent']) or empty($_REQUEST['id_parent']))
    die('A parameter is missing');
    
$id_parent=(int)$_REQUEST['id_parent'];
if(empty($id_parent))
    die('Error on parameter');

$tables=array(
    'INDI'=>'rel_indi_multimedia',
    'EVENT'=>'rel_events_multimedia',
    'SOUR'=>'rel_sour_citations_multimedia',	
    'FAMILLE'=>'rel_familles_multimedia');
// $origin_tables utiliser pour récupérer l'identifiant de la base
$origin_tables=array(
    'INDI'=>'genea_individuals',
    'EVENT'=>'genea_events_details',
    'SOUR'=>'genea_sour_citations',		
    'FAMILLE'=>'genea_familles');
$colonnes=array(
    'INDI'=>'indi_id',
    'EVENT'=>'events_details_id',
	'SOUR'=>'sour_citations_id',
    'FAMILLE'=>'familles_id');
    
if(!isset($tables[$_REQUEST['parent']]))
    die('Type de SOURCE inconnu');
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

if(!empty($_POST['g4p_id_media']))
{
    $sql="INSERT INTO ".$table." (media_id, ".$parentcol.") VALUES 
        (".(int)$_POST['g4p_id_media'].",".$id_parent.")";
    if($result=$g4p_mysqli->g4p_query($sql))
    {
        if($result===true)
            echo '<div class="success">Média lié avec succès</div>';
        elseif($result=='Error:1062')
            echo '<div class="error">Média déjà lié</div>';
    }
    else
        echo '<div class="error">Erreur lors de la création du lien</div>';
}
elseif(!empty($_POST['newmedia']))
{
    $g4p_mysqli->autocommit(false);
	
	if(empty($_POST['source_recordid']))
	{
		$sql="INSERT INTO genea_sour_records (sour_records_auth, sour_records_title,
			sour_records_abbr, sour_records_publ, sour_records_agnc, repo_id, repo_caln, 
			repo_medi, base) VALUES 
			('".mysql_escape_string($_POST['sour_records_auth'])."','".
			mysql_escape_string($_POST['sour_records_title'])."','".
			mysql_escape_string($_POST['sour_records_abbr'])."','".
			mysql_escape_string($_POST['sour_records_publ'])."','".
			mysql_escape_string($_POST['sour_records_agnc'])."','".
			mysql_escape_string($_POST['repo_id'])."','".
			mysql_escape_string($_POST['repo_caln'])."','".
			mysql_escape_string($_POST['repo_medi'])."',".
			(int)$g4p_base.")";
		if($g4p_mysqli->g4p_query($sql))
		{
			$sour_records_id=$g4p_mysqli->insert_id;
			$next=true;
		}
	}
	else
	{
		$sour_records_id=(int)$_POST['source_recordid'];
		$next=true;
	}
	
	if($next)
	{	
		
		
		$sql="INSERT INTO genea_sour_citations (sour_records_id, sour_citations_page, sour_citations_even,
			sour_citations_even_role, sour_citations_data_dates, sour_citations_data_text, sour_citations_quay,
			sour_citations_subm, base) VALUES 
			(".$sour_records_id.",'".mysql_escape_string($_POST['sour_citations_page'])."','".
			mysql_escape_string($_POST['sour_citations_even'])."','".
			mysql_escape_string($_POST['sour_citations_even_role'])."','".
			mysql_escape_string($_POST['sour_citations_data_dates'])."','".
			mysql_escape_string($_POST['sour_citations_data_text'])."','".
			mysql_escape_string($_POST['sour_citations_quay'])."','".
			mysql_escape_string($_POST['sour_citations_subm'])."',".
			(int)$g4p_base.")";
		if($g4p_mysqli->g4p_query($sql))
		{
			$sql="INSERT INTO ".$table." (sour_citations_id, ".$parentcol.") VALUES 
				(".$g4p_mysqli->insert_id.",".$id_parent.")";
			if($g4p_mysqli->g4p_query($sql))
			{
				$g4p_mysqli->commit();
				echo '<div class="success">Source crée avec succès</div>';
			}
			else
			{
				echo '<div class="error">Erreur lors de la création de la source</div>';
				$g4p_mysqli->rollback();
			}
		}
		else
		{
			echo '<div class="error">Erreur lors de la création de la source</div>';
			$g4p_mysqli->rollback();
		}
	}
	else
	{
		echo '<div class="error">Erreur lors de la création de la source</div>';
		$g4p_mysqli->rollback();
	}	
}
   
echo '
    <div class="box">
    <div class="box_title">Lier un média existant</div>
    <form class="formulaire" method="post" action="',$_SERVER['PHP_SELF'],'" name="link_media">
    <ul>
    <li>Titre : <input id="g4p_id_media" name="g4p_id_media" style="width:400px" type="text"  /></li>
    </ul>
    <div class="box" style="display:none" id="media"></div>
    <input name="id_parent" type="hidden" value="',$_REQUEST['id_parent'],'" />
    <input name="parent" type="hidden" value="',$_REQUEST['parent'],'" />
    <input type="submit" value="Lier le média" /></form>
    </div>
    
    <div class="box">
    <div class="box_title">Ajouter un nouveau média</div>
    <form class="formulaire" method="post" action="',$_SERVER['PHP_SELF'],'" name="ajout_media"><ul>';
    echo '<li>Base généalogique : <select name="g4p_base" id="g4p_base">';
    echo '<option value="'.$g4p_base.'"  >'.$g4p_base_nom.'</option>';
    echo '</select></li>';

	echo '<li>Titre : <input id="g4p_title" name="g4p_title" style="width:400px" type="text"  /></li>';
	echo '<li>Fichier : <input id="g4p_file" name="g4p_file" type="file"  /></li>';
			
    echo '<li><input name="id_parent" type="hidden" value="',$_REQUEST['id_parent'],'" />
		<input name="newmedia" type="hidden" value="1" />
        <input name="parent" type="hidden" value="',$_REQUEST['parent'],'" />
        <input type="submit" value="Ajouter média" /></li></ul></form>';
echo '</div>';


require_once($g4p_chemin.'pied_de_page.php');
?>