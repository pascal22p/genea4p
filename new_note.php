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
    $("#g4p_id_note").autocomplete(\'ajax/autocomplete_note.php\', {max:40});
  });
  </script>
';

require_once($g4p_chemin.'entete.php');

////////////////////////////////////
//////  ajout d'une notes
////////////////////////////////////

if(empty($_GET['parent']) or empty($_GET['id_parent']))
    g4p_error('A parameter is missing');
$_GET['id_parent']=(int)$_GET['id_parent'];
if(empty($_GET['id_parent']))
    g4p_error('Error on parameter');

if(empty($g4p_indi))
	g4p_error('Erreur lors du chargement des données de l\'individu');

//verif des infos concernant l'id...
switch($_GET['parent'])
{
    case 'INDI':
    if($_GET['id_parent']!=$g4p_indi->indi_id)
        die('inconsistent data');
    break;
    
    case 'IEVENT':
    if(empty($g4p_indi->events[$_GET['id_parent']]))
        die('inconsistent data');    
    break;
    
    case 'IATTRIBUT':
    if(empty($g4p_indi->attributes[$_GET['id_parent']]))
        die('inconsistent data');    
    break;
    
    case 'FEVENT':
    foreach($g4p_indi->familles as $a_famille)
    {
        if(!empty($a_famille->notes[$_GET['id_parent']]))
        {
            echo $g4p_famille=$a_famille['familles_id'];
            break;
        }
    }
    exit;
    break;

    case 'SOURCE_CIT':
    $sql='SELECT sour_citations_id FROM rel_sour_citations_notes 
        WHERE events_details_id='.$_GET['id_parent'];
    $g4p_result=$g4p_mysqli->g4p_query($sql);
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    {
        $sour_citations_id=$g4p_result[0]['sour_citations_id'];
        $sql="SELECT indi_id FROM rel_indi_sources   
            WHERE sour_citations_id=".$sour_citations_id;
        $g4p_result=$g4p_mysqli->g4p_query($sql);
        if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
        {
            $g4p_indi=g4p_load_indi_infos($g4p_result[0]['indi_id']);
            break;
        }
        
        $sql="SELECT familles_husb, familles_wife FROM rel_familles_sources
            LEFT JOIN genea_familles USING (familles_id)
            WHERE sour_citations_id=".$sour_citations_id;
        $g4p_result=$g4p_mysqli->g4p_query($sql);
        if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
        {
            $g4p_indi=g4p_load_indi_infos($g4p_result[0]['familles_husb']);
            $wife=g4p_load_indi_infos($g4p_result[0]['familles_wife']);
            break;
        }
        
        $sql="SELECT familles_husb, familles_wife FROM rel_events_sources
            LEFT JOIN rel_familles USING (familles_id)
            WHERE sour_citations_id=".$sour_citations_id;
        $g4p_result=$g4p_mysqli->g4p_query($sql);
        if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
        {
            $g4p_indi=g4p_load_indi_infos($g4p_result[0]['familles_husb']);
            $wife=g4p_load_indi_infos($g4p_result[0]['familles_wife']);
            break;
        }
            
        $g4p_indi=g4p_load_indi_infos($g4p_result[0]['husb']);
        $wife=g4p_load_indi_infos($g4p_result[0]['wife']);
    }
    else
        g4p_error('unknown indi_id');
    break;
}

   
echo '<h2>',$g4p_langue['a_index_ajout_note_titre'],'</h2><div class="cadre">
    <em>',$g4p_langue['a_index_ajout_note_lier'],'</em><br />
    <form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=link_note',0),'" name="link_note">',$g4p_langue['a_index_ajout_note_id'],'
    <input id="g4p_id_note" name="g4p_id_note" style="width:400px" type="text" value="',$_SESSION['formulaire']['g4p_id_note'],'" />
    <input name="g4p_id" type="hidden" value="',$_GET['g4p_id'],'" />
    <input name="g4p_type" type="hidden" value="',$_GET['g4p_type'],'" />
    <input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form>
    <a href="',g4p_make_url('','recherche.php','type=note_0&amp;g4p_referer='.rawurlencode('admin|index.php|'.$_SERVER['QUERY_STRING']).'&amp;g4p_champ=g4p_id_note',0).'">',$g4p_langue['a_index_ajout_note_rechercher'],'</a>
    <hr /><br />
    <em>',$g4p_langue['a_index_ajout_note_ajout'],'</em>
    <form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=ajout_note',0),'" name="ajout_note">';
  echo '<textarea rows="8" cols="80" name="g4p_note" ></textarea><br />';
  echo '<input name="g4p_id" type="hidden" value="',$_GET['g4p_id'],'" />
    <input name="g4p_type" type="hidden" value="',$_GET['g4p_type'],'" />
  <input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form>';

  echo '</div>';


require_once($g4p_chemin.'pied_de_page.php');
?>