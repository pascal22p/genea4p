<?php
$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');

$g4p_javascript='<script src="javascript/jquery/jquery-1.3.1.min.js" type="text/javascript"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.bgiframe.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.dimensions.js"></script>
  <script type="text/javascript" src="javascript/jquery/jquery.autocomplete.min.js"></script>
  <script type="text/javascript">
  /* <![CDATA[ */
  $(document).ready(function(){
    $("#g4p_id_source").autocomplete(\'ajax/autocomplete_source.php\', {max:40}).result(callback);
	
	$("#source_recordid").change(jssourcerecord);
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
			$("#newreporecord").show();
        }
    }    
	
  
  
    function jssourcerecord()
    {
		if($("#source_recordid option:selected").val()!="") {
			$.ajax({
				type: "GET",
				url: "ajax/sourcerecorddetail.php",
				dataType: "json",
				data: "value="+$("#source_recordid option:selected").val(),
				success: function(data){
					fillsourrecord(data);
					}
			});	
		} else {
			$("#newsourrecord").show();
			$("#sourceb").hide();
			$("#newreporecord").show();
		}
    }    	
	
	
	function fillsourrecord(data)
	{
        if (data) {
            $("#sourceb").empty();
			$("#sourceb").html("<ul>"+
			"<li>Id : "+data["sour_records_id"]+"</li>"+
			"<li>Auteur : "+data["sour_records_auth"]+"</li>"+
            "<li>Titre : "+data["sour_records_title"]+"</li>"+
			"<li>Abbr : "+data["sour_records_abbr"]+"</li>"+
			"<li>Publ : "+data["sour_records_publ"]+"</li>"+
            "<li>Référence : "+data["repo_caln"]+"</li>"+
			"<li>Agnc : "+data["sour_records_agnc"]+"</li>"+
            "<li>Base : "+data["nom"]+"</li>"+
            "<li>Dépôt : "+data["repo_name"]+"</li>"+
			"<li>Format : "+data["repo_medi"]+"</li></ul>");
            $("#sourceb").show();
			$("#newsourrecord").hide();
			$("#newreporecord").hide();
        }	
	}
  /* ]]> */
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

$tables=array(
    'INDI'=>'rel_indi_sources',
    'EVENT'=>'rel_events_sources',
    'FAMILLE'=>'rel_famille_sources');
// $origin_tables utiliser pour récupérer l'identifiant de la base
$origin_tables=array(
    'INDI'=>'genea_individuals',
    'EVENT'=>'genea_events_details',
    'FAMILLE'=>'genea_familles');
$colonnes=array(
    'INDI'=>'indi_id',
    'EVENT'=>'events_details_id',
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
elseif(!empty($_POST['newsource']))
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
    <form class="formulaire" method="post" action="',$_SERVER['PHP_SELF'],'" name="ajout_source"><ul>';
    echo '<li>Base généalogique : <select name="g4p_base" id="g4p_base">';
    echo '<option value="'.$g4p_base.'"  >'.$g4p_base_nom.'</option>';
    echo '</select></li></ul>';
	
	echo '<div class="box">
		<div class="box_title">Source citation</div>
		<ul>
		<li>Page : <input id="sour_citations_page" name="sour_citations_page" style="width:300px" type="text"  /></li>
		<li>Even : <select name="sour_citations_even" id="sour_citations_even" >
			<option value="" > </option>
			<option value="ADOP"  >ADOP</option>
			<option value="BIRT"  >BIRT</option>
			<option value="BAPM"  >BAPM</option>
			<option value="BARM"  >BARM</option>
			<option value="BASM"  >BASM</option>
			<option value="BLES"  >BLES</option>
			<option value="BURI"  >BURI</option>
			<option value="CENS"  >CENS</option>
			<option value="CHR"  >CHR</option>
			<option value="CHRA"  >CHRA</option>
			<option value="CONF"  >CONF</option>
			<option value="IMMI"  >IMMI</option>
			<option value="NATU"  >NATU</option>
			<option value="ORDN"  >ORDN</option>
			<option value="RETI"  >RETI</option>
			<option value="WILL"  >WILL</option>
			<option value="EVEN"  >EVEN</option>
			<option value="ANUL"  >ANUL</option>
			<option value="CENS"  >CENS</option>
			<option value="DIV"  >DIV</option>
			<option value="DIVF"  >DIVF</option>
			<option value="ENGA"  >ENGA</option>
			<option value="MARR"  >MARR</option>
			<option value="MARB"  >MARB</option>
			<option value="MARC"  >MARC</option>
			<option value="MARL"  >MARL</option>
			<option value="MARS"  >MARS</option>
			<option value="CAST"  >CAST</option>
			<option value="EDUC"  >EDUC</option>
			<option value="NATI"  >NATI</option>
			<option value="OCCU"  >OCCU</option>
			<option value="PROP"  >PROP</option>
			<option value="RELI"  >RELI</option>
			<option value="RESI"  >RESI</option>
			<option value="TITL"  >TITL</option>
			<option value="FACT"  >FACT</option>
			</select>
		</li>
		<li>Even role : <select name="sour_citations_even_role" id="sour_citations_even_role" >
			<option value="" > </option>
			<option value="CHIL"  >CHIL</option>
			<option value="HUSB"  >HUSB</option>
			<option value="WIFE"  >WIFE</option>
			<option value="MOTH"  >MOTH</option>
			<option value="FATH"  >FATH</option>
			<option value="SPOU"  >SPOU</option>
			</select> ou 
			<input id="sour_citations_even_role_text" name="sour_citations_even_role_text" style="width:300px" type="text"  />
		</li>
		<li>Original Date : <input id="sour_citations_data_dates" name="sour_citations_data_dates" style="width:300px" type="text"  /></li>
		<li>Texte : <textarea rows="8" cols="50" name="sour_citations_data_text" ></textarea></li>
		<li>Confidence : <select name="sour_citations_quay" id="sour_citations_quay">
			<option value="" > </option>
			<option value="0"  >Unreliable evidence or estimated data</option>
			<option value="1"  >Questionable reliability of evidence (interviews, oral genealogies...)</option>
			<option value="2"  >Secondary evidence, data officially recorded sometime after event</option>
			<option value="3"  >Direct and primary evidence used, or by dominance of the evidence</option>
			</select>		
		</li>
		<li>Submiter (Not gedcom!) : <input id="sour_citations_subm" name="sour_citations_subm" style="width:300px" type="text"  /></li>
		</ul></div>';

	echo '<div class="box">
		<div class="box_title">Source Record</div>
		<ul>
		<li><select name="source_recordid" id="source_recordid" ><option value="" >Nouvelle source</option>'; 
    $g4p_query=$g4p_mysqli->g4p_query("SELECT sour_records_id, sour_records_title, repo_caln, repo_name FROM genea_sour_records
		LEFT JOIN genea_infos ON id=base
		LEFT JOIN genea_repository USING (repo_id)
		WHERE genea_infos.id=$g4p_base
		ORDER BY repo_name, sour_records_title");
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
    {
        foreach($g4p_result as $g4p_a_result)
        {
            echo '<option value="'.$g4p_a_result['sour_records_id'].'">'.$g4p_a_result['repo_name'].' '.$g4p_a_result['sour_records_title'].' '.$g4p_a_result['repo_caln'].'</option>';
        }
    }		
	echo '</select></li></ul>
	<div class="box" style="display:none" id="sourceb"></div>
		
		<ul id="newsourrecord">
		<li>Auteur originel : <input id="sour_records_auth" name="sour_records_auth" style="width:300px" type="text"  /></li>
		<li>Titre : <input id="sour_records_title" name="sour_records_title" style="width:300px" type="text"  /></li>
		<li>abbr : <input id="sour_records_abbr" name="sour_records_abbr" style="width:300px" type="text"  /></li>
		<li>publ : <textarea rows="8" cols="50" name="sour_records_publ" ></textarea></li>
		<li>agnc : <input id="sour_records_agnc" name="sour_records_agnc" style="width:300px" type="text"  /></li>
		<li>caln : <input id="repo_caln" name="repo_caln" style="width:300px" type="text"  /></li>
		<li>medi : <select name="repo_medi" id="repo_medi">
			<option value="" > </option>
			<option value="audio"  >audio</option>
			<option value="book"  >book</option>
			<option value="card"  >card</option>
			<option value="electronic"  >electronic</option>
			<option value="fiche"  >fiche</option>
			<option value="film"  >film</option>
			<option value="magazine"  >magazine</option>
			<option value="manuscript"  >manuscript</option>
			<option value="map"  >map</option>
			<option value="newspaper"  >newspaper</option>
			<option value="photo"  >photo</option>
			<option value="tombstone"  >tombstone</option>
			<option value="video"  >video</option>
			</select></li>
		</ul>';

	echo '<div class="box" id="newreporecord">
		<div class="box_title">Dépot</div>
		<ul>
		<li><select name="repo_id" id="repo_id" ><option value="" > </option>'; 
    $g4p_query=$g4p_mysqli->g4p_query("SELECT repo_id, repo_name FROM genea_repository
		WHERE base=$g4p_base
		ORDER BY repo_name");
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
    {
        foreach($g4p_result as $g4p_a_result)
        {
            echo '<option value="'.$g4p_a_result['repo_id'].'">'.$g4p_a_result['repo_name'].'</option>';
        }
    }		
	echo '</select></li>
		</ul></div></div>';
		
    echo '<input name="id_parent" type="hidden" value="',$_REQUEST['id_parent'],'" />
		<input name="newsource" type="hidden" value="1" />
        <input name="parent" type="hidden" value="',$_REQUEST['parent'],'" />
        <input type="submit" value="Ajouter source" /></form>';
echo '</div>';


require_once($g4p_chemin.'pied_de_page.php');
?>