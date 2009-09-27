<?php
$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

if(empty($_REQUEST['id_event']) or empty($_REQUEST['type_event']))
    die('Erreur de paramètre');

$type_events_list=array(
    'indi' => 'rel_indi_events',
    'attr' => 'rel_indi_attributes',
    'fam' => 'rel_familles_events');
    
if(!isset($type_events_list[$_REQUEST['type_event']]))
    die('Erreur de paramètre');

$type_event=$type_events_list[$_REQUEST['type_event']];

$sql="SELECT *
    FROM ".$type_event." 
    LEFT JOIN genea_events_details USING(events_details_id)
    WHERE events_details_id=".(int)$_REQUEST['id_event'];
    
$g4p_result_req=$g4p_mysqli->g4p_query($sql);
$g4p_event=$g4p_mysqli->g4p_result($g4p_result_req);
$g4p_event=$g4p_event[0];
  
$g4p_date=new g4p_date('');
//$g4p_date->set_gedcomdate($g4p_event['events_details_gedcom_date']);
$g4p_date->set_gedcomdate('INT BET @#DFRENCH R@ 12 DEC 1898 AND 2001 (hjdfs ehzor)');
$g4p_date->g4p_gedom2date();

$g4p_javascript='<link type="text/css" href="styles/jquery/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="http://jqueryui.com/latest/jquery-1.3.2.js"></script>
    <script type="text/javascript" src="http://jqueryui.com/latest/ui/ui.core.js"></script>
    <script type="text/javascript" src="http://jqueryui.com/latest/ui/ui.tabs.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#eventstabs").tabs();
        });
    </script>
';
require_once($g4p_chemin.'entete.php');
echo '<div class="box_title"><h2>Modification de l\'évènement</h2></div>'."\n";

echo '<div class="box">';
echo '<div class="box_title">Date</div>'; 

echo '<div id="eventstabs" style="border:none;">
    <ul>
        <li stryle="background-color:#F1EBDF;"><a href="#fragment-1">Date</a></li>
        <li><a href="#fragment-2">Format Gedcom</a></li>
    </ul>
    <div id="fragment-1">';

echo '<ul>';
echo '<li>';
if(!empty($g4p_date->date1))
    g4p_form_date(0, $g4p_date->date1);
else
    g4p_form_date(0, $g4p_date->date1);
echo '</li>';

if(!empty($g4p_date->date2))
{
    echo '<li>';
    g4p_form_date(0, $g4p_date->date2);
    echo '</li>';
}
echo '</ul>';

echo '    </div>
    <div id="fragment-2">
        Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
        Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
    </div>
</div>';

  var_dump($g4p_date);



echo '</div>';
require_once($g4p_chemin.'pied_de_page.php');



  
  
  
  
  
  
  exit;
  echo '<div class="menu_interne"><a href="'.$_SESSION['historic']['url'][0].'" class="retour">'.$g4p_langue['retour'].'</a></div><div class="cadre"><h2>',$g4p_langue['a_index_mod_event_titre'],'</h2><form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=mod_event',0),'" name="mod_event">';

  if(!isset($_GET['g4p_type_event']))
  {
    if(isset($g4p_tag_iattributs[$g4p_events['type']]))
      $g4p_select='iattribut';
    elseif(isset($g4p_tag_ievents[$g4p_events['type']]))
      $g4p_select='ievent';
    elseif(isset($g4p_tag_fevents[$g4p_events['type']]))
      $g4p_select='fevent';
    else
      $g4p_select=false;
  }
  else
    $g4p_select=$_GET['g4p_type_event'];
  
  if(isset($_GET['g4p_type_date']))
    $g4p_type_date_url='&amp;g4p_type_date='.$_GET['g4p_type_date'];
  else
    $g4p_type_date_url='';
    
  if($g4p_select=='iattribut')//attribut individuel
  {
    
    echo '<ul class="tabnav">';
    echo '<li ><a href="',g4p_make_url('admin','index.php','g4p_opt=mod_event'.$g4p_type_date_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_event=ievent',0),'" >Evènement</a></li>';
    echo '<li class="active"><a href="',g4p_make_url('admin','index.php','g4p_opt=mod_event'.$g4p_type_date_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_event=iattribut',0),'" >Attribut</a></li>';
    echo '</ul>';
      
    //date gedcom
    echo '<div class="boite_tabulation">';
  
    echo $g4p_langue['a_index_mod_event_type'],'<select name="g4p_type">';
    echo '<option value="">Choisissez</option>';
    reset($g4p_tag_iattributs);
    while(list($g4p_key, $g4p_value)=each($g4p_tag_iattributs))
    {
      $g4p_selected=($g4p_events['type']==$g4p_key)?('selected="selected"'):('');
      echo '<option value="',$g4p_key,'" '.$g4p_selected.'>',$g4p_value,'</option>';
    }
    echo '</select>';
    echo ' Description <input type="text" name="g4p_description" value="',$g4p_events['description'],'" /><br />';
    
    g4p_formulaire_date_event($g4p_date);
    echo '</div>';
  }
  elseif($g4p_select=='ievent')//évènement individuel
  {
    echo '<ul class="tabnav">';
    echo '<li class="active"><a href="',g4p_make_url('admin','index.php','g4p_opt=mod_event'.$g4p_type_date_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_event=ievent',0),'" >Evènement</a></li>';
    echo '<li ><a href="',g4p_make_url('admin','index.php','g4p_opt=mod_event'.$g4p_type_date_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_event=iattribut',0),'" >Attribut</a></li>';
    echo '</ul>';
      
    //date gedcom
    echo '<div class="boite_tabulation">';
  
    echo $g4p_langue['a_index_mod_event_type'],'<select name="g4p_type">';
    echo '<option value="">Choisissez</option>';
    reset($g4p_tag_ievents);
    while(list($g4p_key, $g4p_value)=each($g4p_tag_ievents))
    {
      $g4p_selected=($g4p_events['type']==$g4p_key)?('selected="SELECTED"'):('');
      echo '<option value="',$g4p_key,'" '.$g4p_selected.'>',$g4p_value,'</option>';
    }
    echo '</select>';
    if($g4p_events['attestation']=='Y')
      echo ' Attestation de l\'évènement <input type="checkbox" name="g4p_attestation" checked="checked" /><br />';
    else
      echo ' Attestation de l\'évènement <input type="checkbox" name="g4p_attestation" style="vertical-align:middle"   /><br />';
    
    g4p_formulaire_date_event($g4p_date);
    echo '</div>';
  }
  elseif($g4p_select=='fevent')//évènement familiale
  {
    echo '<ul class="tabnav">';
    echo '<li class="active"><a href="',g4p_make_url('admin','index.php','g4p_opt=mod_event'.$g4p_type_date_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_event=fevent',0),'" >Evènement</a></li>';
    echo '</ul>';
      
    //date gedcom
    echo '<div class="boite_tabulation">';
  
    echo $g4p_langue['a_index_mod_event_type'],'<select name="g4p_type">';
    echo '<option value="">Choisissez</option>';
    reset($g4p_tag_fevents);
    while(list($g4p_key, $g4p_value)=each($g4p_tag_fevents))
    {
      $g4p_selected=($g4p_events['type']==$g4p_key)?('selected="SELECTED"'):('');
      echo '<option value="',$g4p_key,'" '.$g4p_selected.'>',$g4p_value,'</option>';
    }
    echo '</select>';
    if($g4p_events['attestation']=='Y')
      echo ' Attestation de l\'évènement <input type="checkbox" name="g4p_attestation" checked="checked" /><br />';
    else
      echo ' Attestation de l\'évènement <input type="checkbox" name="g4p_attestation" style="vertical-align:middle"   /><br />';
    
    g4p_formulaire_date_event($g4p_date);
    echo '</div>';
  }
  else
    echo 'Erreur lors de la modif d\'un évènement';
  
  echo '<br />';
  echo $g4p_langue['a_index_mod_event_lieu'];
  echo '<select name="g4p_lieu" style="width:auto"><option value=""></option>';
  $sql="SELECT place_id,place_lieudit, place_ville, place_departement, place_region, place_pays FROM genea_place WHERE base=".$_SESSION['genea_db_id']." ORDER BY place_ville";
  $g4p_result_req=$g4p_mysqli->g4p_query($sql);      
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result_req))
  {
    foreach($g4p_result as $a_result)
    {
      if($g4p_events['place_id']==$a_result['place_id'])
        $selected='selected="selected"';
      else
        $selected='';
      echo '<option value="',$a_result['place_id'],'" ',$selected,'>',$a_result['place_lieudit'],' ',$a_result['place_ville'],' ',$a_result['place_departement'],' ',$a_result['place_region'],' ',$a_result['place_pays'],'</option>';
    }
  }
  echo '</select><br />';
  
  echo $g4p_langue['a_index_mod_event_age'],'<input type="text" name="g4p_age" value="'.$g4p_events['age'].'" /><br />';
  echo $g4p_langue['a_index_mod_event_cause'],'<br /><textarea rows="8" cols="80" name="g4p_cause">'.$g4p_events['cause'].'</textarea><br />';
  echo '<input name="g4p_event_id" type="hidden" value="',$_GET['g4p_id'],'" />
  <input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form></div>';
require_once($g4p_chemin.'pied_de_page.php');



function g4p_form_date($i, $date)
{
    global $liste_mois_gregorien,$liste_mois_francais,$liste_mois_hebreux, $g4p_liste_calendrier;
    
    $liste_modificateur1=array(
    'EST'=>'Estimée', //EST
    'ABT'=>'Environ', //ABT
    'CAL'=>'Calculée', //CAL
    'TO'=>'Avant', //TO
    'AFT'=>'Après', //AFTER
    'FROM'=>'De', //FROM
    'BET'=>'Entre', //BET
    'TO'=>'à', //TO
    'AND'=>'et'); //AND
    
    echo '<select name="date_mod['.$i.']" id="date_mod['.$i.']" style="width:12ex">';
    echo '<option value="" ></option>';
    if(empty($date->mod))
        $date->mod='';
    foreach($liste_modificateur1 as $key=>$val)
    {
        if($key==$date->mod)
            $selected='selected="selected"';
        else
            $selected='';
        echo '<option value="',$key,'" ',$selected,'>',$val,'</option>';
    }
    echo '</select> ';

    echo '<select name="date_calendrier['.$i.']" style="width:19ex">';
    echo '<option value="" ></option>';
    if(empty($date->calendar))
        $date->calendar='';
    foreach($g4p_liste_calendrier as $a_modif_key=>$a_modif_value)
    {
        if(!empty($date->calendar) and $a_modif_key==$date->calendar)
              $selected='selected="selected"';
            else
              $selected='';
        echo '<option value="',$a_modif_key,'" ',$selected,'>',$a_modif_value,'</option>';
    }
    echo '</select> ';

    if(empty($date->day))
        $date->day='';   
    echo '<input type="text" name="date_jour['.$i.']" value="'.$date->day.'" style="width:3ex" /> ';

    $tmp=array_merge($liste_mois_gregorien,$liste_mois_francais,$liste_mois_hebreux);
    echo '<select name="date_mois['.$i.']" style="width:26ex">';
    echo '<option value="" ></option>';
    if(empty($date->month))
        $date->month='';    
    foreach($tmp as $a_modif_key=>$a_modif_value)
    {
        if(!empty($date->month) and $a_modif_key==$date->month)
            $selected='selected="selected"';
        else
            $selected='';
        echo '<option value="',$a_modif_key,'" ',$selected,'>',$a_modif_value,'</option>';
    }
    echo '</select>';

    if(empty($date->year))
        $date->year='';   
    echo ' <input type="text" name="date_annee['.$i.']" value="'.$date->year.'" style="width:6ex" /><br />';
}

?>

