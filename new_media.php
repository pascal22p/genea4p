<?php
$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');

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

$g4p_javascript='<script type="text/javascript" src="javascript/jquery/jquery-3.6.0.min.js"></script>
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
            $("#media").append("<ul><li>Id : "+data[1]+"</li><li>Titre : "+data[2]+"</li><li>Format : "+data[3]+"</li><li>Fichier : "+data[4]+"</li><li><img style=\"width:90%\" src=\"cache/'.g4p_base_namefolder($g4p_base_nom).'/objets/"+data[4]+"\" /></ul>");
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
	$error=false;
	
	if(empty($_FILES['g4p_file']))
	{
		$error=true;
		echo '<div class="error">Aucun fichier reçu</div>';	
	}
	
	if(!$error and $_FILES['g4p_file']['error']==UPLOAD_ERR_INI_SIZE)
	{
		$error=true;
		echo '<div class="error">Fichier trop gros</div>';
	}
	elseif(!$error and $_FILES['g4p_file']['error']!=UPLOAD_ERR_OK)
	{
		$error=true;
		echo '<div class="error">Erreur : '.$_FILES['g4p_file']['error'].'</div>';
	}
	
	$allowedmedia=array_flip($g4p_mime_type_autorise);
	if(!$error and !isset($allowedmedia[$_FILES['g4p_file']['type']]))
	{
		$error=true;
		echo '<div class="error">Type de fichier non autorisé</div>';
	}
	
	if(!$error and empty($_GET['g4p_title']))
	{
		$fileinfo=pathinfo($_FILES['g4p_file']['name']);
		$_GET['g4p_title']=$fileinfo['filename'];
	}
	
	if(!$error)
	{
		$uploaddir = 'cache/'.$g4p_base_nom.'/objets/';
		$uploadfile = dirname(__FILE__).'/'.$uploaddir . $fileinfo['basename'];

		if(file_exists($uploadfile))
		{
			$error=true;
			echo '<div class="error">Un fichier existe déjà avec ce nom</div>';		
		}		

		if (!$error and !move_uploaded_file($_FILES['g4p_file']['tmp_name'], $uploadfile)) 
		{
			$error=true;
			echo '<div class="error">Erreur lors de l\'enregistrement du fichier</div>';		
		}		
	
		if(!$error)
		{
			$sql="INSERT INTO genea_multimedia (media_title, media_format, media_file, base) VALUES 
					('".$g4p_mysqli->escape_string($_GET['g4p_title'])."','".
					$g4p_mysqli->escape_string($fileinfo['extension'])."','".
					$g4p_mysqli->escape_string($fileinfo['basename'])."',".
					(int)$g4p_base.")";
			if($g4p_mysqli->g4p_query($sql))
			{
				$sql="INSERT INTO ".$table." (media_id, ".$parentcol.") VALUES 
					(".$g4p_mysqli->insert_id.",".$id_parent.")";
				if($g4p_mysqli->g4p_query($sql))
				{
					$g4p_mysqli->commit();
					echo '<div class="success">Média crée avec succès</div>';
				}
				else
				{
					echo '<div class="error">Erreur lors de la création du média</div>';
					unlink($uploadfile);
					$g4p_mysqli->rollback();
				}
			}
			else
			{
				echo '<div class="error">Erreur lors de la création de la source</div>';
				unlink($uploadfile);
				$g4p_mysqli->rollback();
			}
		}
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
    <form enctype="multipart/form-data" class="formulaire" method="post" action="',$_SERVER['PHP_SELF'],'" name="ajout_media"><ul>';
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
