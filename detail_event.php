<?php
$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');

$_GET['id_event']=(int)$_GET['id_event'];
if(empty($_GET['id_event']))
    g4p_error('Error on parameter');

// Get parent and type from database
$sql="SELECT rel_indi_events.indi_id as indi_id_e, 
	rel_indi_attributes.indi_id as indi_id_a, familles_id,
    genea_familles.familles_wife as wife , genea_familles.familles_husb as husb
	FROM genea_events_details
	LEFT OUTER JOIN rel_indi_events USING (events_details_id)
	LEFT OUTER JOIN rel_indi_attributes USING (events_details_id)
	LEFT OUTER JOIN rel_familles_events USING (events_details_id)
    LEFT OUTER JOIN genea_familles using (familles_id)
	WHERE events_details_id= ".(int)$_REQUEST['id_event'];

$g4p_result_req=$g4p_mysqli->g4p_query($sql);
$g4p_event=$g4p_mysqli->g4p_result($g4p_result_req);
if(empty($g4p_event))
    die('No event with id '.(int)$_REQUEST['id_event']);

// check if result is unique
if(count($g4p_event)>1)
{
    var_dump($g4p_event);
    die('Integrity error, more than results');
}
    
$g4p_event=$g4p_event[0];

// check if event only present in one table
if(!empty($g4p_event['indi_id_e']) and empty($g4p_event['indi_id_a']) and empty($g4p_event['familles_id']))
{
	$type_event='indi_e';
	$indi_id=$g4p_event['indi_id_e'];
}
else if(empty($g4p_event['indi_id_e']) and !empty($g4p_event['indi_id_a']) and empty($g4p_event['familles_id']))
{
	$type_event='indi_a';
	$indi_id=$g4p_event['indi_id_e'];
}
else if(empty($g4p_event['indi_id_e']) and empty($g4p_event['indi_id_a']) and !empty($g4p_event['familles_id']))
{
	$type_event='famille';
	if(empty($g4p_event['husb']) and empty($g4p_event['wife']))
	{
		die("Problem with the family, no husb or wife. Familly_id: ".$g4p_event['familles_id']);
	}
	else if(!empty($g4p_event['husb']))
		$indi_id=$g4p_event['husb'];
	else if(!empty($g4p_event['wife']))
		$indi_id=$g4p_event['wife'];
}
else
	die('event not linked to anything or linked more than once');
	

$g4p_indi=g4p_load_indi_infos($indi_id);
if(empty($g4p_indi))
	g4p_error('Erreur lors du chargement des données de l\'individu');

if($type_event=='famille')
	$g4p_event=$g4p_indi->familles[$g4p_event['familles_id']]->events[(int)$_REQUEST['id_event']];
else
	$g4p_event=$g4p_indi->events[(int)$_REQUEST['id_event']];

require_once($g4p_chemin.'entete.php');

echo '<div class="box_title"><h2>Détail de l\'évènement</h2></div>'."\n";

echo '<div class="menu_interne"><a href="'.g4p_make_url('','fiche_individuelle.php','id_pers='.$g4p_indi->indi_id,'fiche-'.$g4p_indi->base.'-'.g4p_prepare_varurl($g4p_indi->nom).'-'.g4p_prepare_varurl($g4p_indi->prenom).'-'.$g4p_indi->indi_id).'" class="retour">',$g4p_langue['retour'],'</a>';
if ($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    echo ' - <a href="'.g4p_make_url('','modification_event.php','type_event=indi&amp;id_event='.$g4p_event->id,0).'" class="admin">',$g4p_langue['menu_mod_event'],'</a>
           - <a href="'.g4p_make_url('admin','exec.php','g4p_opt=del_event&amp;g4p_id='.(int)$_REQUEST['id_event'],0).'" class="admin">',$g4p_langue['menu_del_event'],'</a>
		   - <a href="',g4p_make_url('','new_note.php','parent=EVENT&amp;id_parent='.(int)$_REQUEST['id_event'],0),'" class="admin">',$g4p_langue['menu_ajout_note'],'</a> 
		   - <a href="',g4p_make_url('','new_source.php','parent=EVENT&amp;id_parent='.(int)$_REQUEST['id_event'],0),'" class="admin">',$g4p_langue['menu_ajout_source'],'</a> 
           - <a href="',g4p_make_url('','new_media.php','parent=EVENT&amp;id_parent='.(int)$_REQUEST['id_event'],0),'" class="admin">',$g4p_langue['menu_ajout_media'],'</a> ';
//           - <a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_asso&amp;g4p_lien='.$_GET['id_parent'].'&amp;g4p_type=events',0).'" class="admin">',$g4p_langue['menu_ajout_relation'],'</a>';
echo '</div>';
 
echo '<div class="box">'."\n";
echo '<div class="box_title">Évènement</div>'."\n";
if(!empty($g4p_event->timestamp))
    echo '<span class="petit">'.sprintf($g4p_langue['sys_function_mariage_chan'],g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_event->timestamp))),'</span><br />';

echo '<dl class="collapsed">'."\n";
echo '<dt>',$g4p_langue['detail_type_event'],'</dt><dd>',$g4p_tag_def[$g4p_event->tag],'</dd>'."\n";
if(!empty($g4p_event->details_descriptor))
    echo '<dt>',$g4p_langue['detail_description'],'</dt><dd>',$g4p_event->details_descriptor,'</dd>'."\n";

if(g4p_date($g4p_event->date->gedcom_date))
    echo '<dt>',$g4p_langue['detail_date_event'],'</dt><dd>',g4p_date($g4p_event->date->gedcom_date),'</dd>'."\n";
 if($g4p_event->date->jd_count and $_SESSION['permission']->permission[_PERM_AFF_DATE_])
 {
    echo '<dt>Détail date : </dt><dd style="margin-left:3em;clear:both"><dl class="collapsed">'."\n";
    $g4p_tmp=cal_from_jd($g4p_event->date->jd_count, CAL_GREGORIAN);
    switch($g4p_event->date->jd_precision)
    {
        case 3:
        echo '<dt>'.$g4p_langue['detail_cal_gregorien'].'</dt><dd>',g4p_strftime($g4p_langue['date'],$g4p_tmp['dow'].'-'.$g4p_tmp['day'].'-'.$g4p_tmp['month'].'-'.$g4p_tmp['year'],'jd_cal_gregorien'),'</dd>';
        break;

        case 2:
        echo '<dt>'.$g4p_langue['detail_cal_gregorien'].'</dt><dd>',g4p_strftime($g4p_langue['date'],'--'.$g4p_tmp['month'].'-'.$g4p_tmp['year'],'jd_cal_gregorien'),'</dd>';
        break;

        case 1:
        echo '<dt>'.$g4p_langue['detail_cal_gregorien'].'</dt><dd>',g4p_strftime($g4p_langue['date'],'---'.$g4p_tmp['year'],'jd_cal_gregorien'),'</dd>';
        break;
    }
    
    $g4p_tmp=cal_from_jd($g4p_event->date->jd_count, CAL_FRENCH);
    if($g4p_tmp['date']!='0/0/0')
    {
        switch($g4p_event->date->jd_precision)
        {
            case 3:
            echo '<dt>'.$g4p_langue['detail_cal_revolutionnaire'].'</dt><dd>',g4p_strftime($g4p_langue['date'],$g4p_tmp['dow'].'-'.$g4p_tmp['day'].'-'.$g4p_tmp['month'].'-'.$g4p_tmp['year'],'jd_cal_revolutionnaire'),'</dd>';
            break;

            case 2:
            echo '<dt>'.$g4p_langue['detail_cal_revolutionnaire'].'</dt><dd>',g4p_strftime($g4p_langue['date'],'--'.$g4p_tmp['month'].'-'.$g4p_tmp['year'],'jd_cal_revolutionnaire'),'</dd>';
            break;

            case 1:
            echo '<dt>'.$g4p_langue['detail_cal_revolutionnaire'].'</dt><dd>',g4p_strftime($g4p_langue['date'],'---'.$g4p_tmp['year'],'jd_cal_revolutionnaire'),'</dd>';
            break;
        }
    }
    $g4p_tmp=cal_from_jd($g4p_event->date->jd_count, CAL_JEWISH);
    switch($g4p_event->date->jd_precision)
    {
        case 3:
        echo '<dt>'.$g4p_langue['detail_cal_juif'].'</dt><dd>',g4p_strftime($g4p_langue['date'],$g4p_tmp['dow'].'-'.$g4p_tmp['day'].'-'.$g4p_tmp['month'].'-'.$g4p_tmp['year'],'jd_cal_juif'),'</dd>';
        break;

        case 2:
        echo '<dt>'.$g4p_langue['detail_cal_juif'].'</dt><dd>',g4p_strftime($g4p_langue['date'],'--'.$g4p_tmp['month'].'-'.$g4p_tmp['year'],'jd_cal_juif'),'</dd>';
        break;

        case 1:
        echo '<dt>'.$g4p_langue['detail_cal_juif'].'</dt><dd>',g4p_strftime($g4p_langue['date'],'---'.$g4p_tmp['year'],'jd_cal_juif'),'</dd>';
        break;
    }
    $g4p_tmp=cal_from_jd($g4p_event->date->jd_count, CAL_JULIAN);
    switch($g4p_event->date->jd_precision)
    {
        case 3:
        echo '<dt>'.$g4p_langue['detail_cal_julien'].'</dt><dd>',g4p_strftime($g4p_langue['date'],$g4p_tmp['dow'].'-'.$g4p_tmp['day'].'-'.$g4p_tmp['month'].'-'.$g4p_tmp['year'],'jd_cal_julien'),'</dd>';
        break;

        case 2:
        echo '<dt>'.$g4p_langue['detail_cal_julien'].'</dt><dd>',g4p_strftime($g4p_langue['date'],'--'.$g4p_tmp['month'].'-'.$g4p_tmp['year'],'jd_cal_julien'),'</dd>';
        break;

        case 1:
        echo '<dt>'.$g4p_langue['detail_cal_julien'].'</dt><dd>',g4p_strftime($g4p_langue['date'],'---'.$g4p_tmp['year'],'jd_cal_julien'),'</dd>';
        break;
    }
    echo '</dl></dd>';
}
if($g4p_event->cause)
	echo '<dt>',$g4p_langue['detail_cause'],' </dt><dd>',$g4p_event->cause,'</dd>';

//print_r($g4p_event->place);
if(!empty($g4p_event->place))
{
    echo '<dt>Lieu : </dt><dd style="margin-left:3em;clear:both"><dl style="collapsed">'."\n";
    foreach($g4p_event->place as $key=>$val)
    {
        if(!empty($val))
        {
            echo '<dt>',$key,' : </dt><dd>',$val,'</dd>'."\n";
            if($key=='latitude')
                $latitude=$val;
            if($key=='longitude')
                $longitude=$val;
            if($key=='ville')
                $ville=$val;
        }
    }
    echo '</dl></dd>'."\n";
}
echo '</dl>';

if(!empty($latitude) and !empty($longitude) )
{
    echo '<iframe width="600px" height="400px" style="border:2px solid black;margin-bottom:1em;margin-left:10em;" src="https://www.google.com/maps/embed/v1/place?q='.$latitude.','.$longitude.'&amp;zoom=10&amp;key=AIzaSyBimqSKc5tF3KpJLFprP5wO-BkxDLr-TH8"></iframe>';
}
echo '</div>';

if(!empty($g4p_event->address))
  g4p_affiche_adresse($g4p_event->address);

if(!empty($g4p_event->asso))
  g4p_affiche_asso($g4p_event->asso, $indi_id,'events');

if ($_SESSION['permission']->permission[_PERM_NOTE_] and !empty($g4p_event->notes))
  g4p_affiche_notes($g4p_event->notes,$indi_id,'events');

if ($_SESSION['permission']->permission[_PERM_SOURCE_] and !empty($g4p_event->sources))
  g4p_affiche_sources($g4p_event->sources, $indi_id,'events');

if ($_SESSION['permission']->permission[_PERM_MULTIMEDIA_] and !empty($g4p_event->medias))
  g4p_affiche_multimedia($g4p_event->medias, $indi_id,'events');


include($g4p_chemin.'pied_de_page.php');
?>
