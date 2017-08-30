<?php
$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

$g4p_date_modif=array(
  'EST',
  'ABT',
  'BEF',
  'AFT',
  'BET',
  'AND',
  'TO',
  'FROM',
  'CAL',
  'INT'
);

$g4p_months=array(
'VEND',	'BRUM',	'FRIM',	'NIVO',	'PLUV',	'VENT',	'GERM',	'FLOR',
'PRAI',	'MESS',	'THER',	'FRUC',	'COMP',	'TSH',	'CSH',	'KSL',
'TVT',	'SHV',	'ADR',	'ADS',	'NSN',	'IYR',	'SVN',	'TMZ',
'AAV',	'ELL',	'JAN',	'FEB',	'MAR',	'APR',	'MAY',	'JUN',
'JUL',	'AUG',	'SEP',	'OCT',	'NOV',	'DEC'
);

if(empty($_REQUEST['id_event']))
    die('Erreur de paramètre');

    
$sql="SELECT genea_events_details.*, rel_indi_events.indi_id as indi_id_e, 
	rel_indi_events.events_tag as events_tag_e, 
	rel_indi_attributes.indi_id as indi_id_a, familles_id, 
	rel_familles_events.events_tag as events_tag_f, events_details_timestamp, 
	rel_indi_events.timestamp as timestamp_e, rel_indi_attributes.timestamp as timestamp_a,
	rel_familles_events.timestamp as timestamp_f FROM genea_events_details
	LEFT OUTER JOIN rel_indi_events USING (events_details_id)
	LEFT OUTER JOIN rel_indi_attributes USING (events_details_id)
	LEFT OUTER JOIN rel_familles_events USING (events_details_id)
	WHERE events_details_id = ".(int)$_REQUEST['id_event'];

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
	$indi_id=$g4p_event['indi_id_a'];
}
else if(empty($g4p_event['indi_id_e']) and empty($g4p_event['indi_id_a']) and !empty($g4p_event['familles_id']))
{
	$type_event='famille';
	$famille_id=$g4p_event['familles_id'];
}
else
	die('event not linked to anything or linked more than once');

If(!empty($indi_id))
	$g4p_indi=g4p_load_indi_infos($indi_id);
	
if($type_event=='indi_e')//évènement individuel
{
    $select_type_event='<select name="g4p_type">';
    $select_type_event.='<option value="">Choisissez</option>';
    reset($g4p_tag_ievents);
    while(list($g4p_key, $g4p_value)=each($g4p_tag_ievents))
    {
      $g4p_selected=($g4p_event['events_tag_e']==$g4p_key)?('selected="SELECTED"'):('');
      $select_type_event.='<option value="'.$g4p_key.'" '.$g4p_selected.'>'.$g4p_value.'</option>';
    }
    $select_type_event.='</select>';
}
elseif($type_event=='famille')//évènement familiale
{
    $select_type_event='<select name="g4p_type">';
    $select_type_event.='<option value="">Choisissez</option>';
    reset($g4p_tag_fevents);
    while(list($g4p_key, $g4p_value)=each($g4p_tag_fevents))
    {
      $g4p_selected=($g4p_event['events_tag_f']==$g4p_key)?('selected="SELECTED"'):('');
      $select_type_event.='<option value="'.$g4p_key.'" '.$g4p_selected.'>'.$g4p_value.'</option>';
    }
    $select_type_event.='</select>';
}
  
if(!isset($_POST['g4p_exec']))
{
    $g4p_date=new g4p_date('');
    $g4p_date->set_gedcomdate($g4p_event['events_details_gedcom_date']);
    //$g4p_date->set_gedcomdate('FROM @#DFRENCH R@ 12 DEC 1898');
    $g4p_date->g4p_gedom2date();

	$g4p_javascript='<script language="JavaScript" type="text/javascript">
	function openCity(evt, cityName) {
    // Declare all variables
    var i, tabcontent, tablinks;
    
    document.getElementById("date_type").value = cityName;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
	} 

	function setdefault() {
	// Get the element with id="defaultOpen" and click on it
	document.getElementById("defaultOpen").click();
	}
	</script>';
	
	$body='onload="setdefault()"';	

    require_once($g4p_chemin.'entete.php');
    if(!empty($_SESSION['message']))
		echo '<div class="success">'.$_SESSION['message'].'</div>';
	$_SESSION['message']='';
    if(!empty($_SESSION['errormsg']))
		echo '<div class="error">'.$_SESSION['errormsg'].'</div>';
	$_SESSION['message']='';
    echo '<div class="box_title"><h2>Modification de l\'évènement</h2></div>'."\n";
    if(!empty($indi_id))
    {
		echo '<div class="menu_interne">';
		echo '<a href="'.g4p_make_url('','fiche_individuelle.php','id_pers='.$_REQUEST['id_event'],'fiche-'.$g4p_indi->base.'-'.g4p_prepare_varurl($g4p_indi->nom).'-'.g4p_prepare_varurl($g4p_indi->prenom).'-'.$g4p_indi->indi_id).'" class="retour">',$g4p_langue['retour'],'</a>';
	}
	else
	{
		echo '<div class="menu_interne">';
		echo '<a href="'.g4p_make_url('','modification_famille.php','id_famille='.$famille_id,'').'" class="retour">',$g4p_langue['retour'],'</a>';
	}
	echo '<a href="',g4p_make_url('','new_note.php','parent=EVENT&amp;id_parent='.$_REQUEST['id_event'],0),'" class="admin">',$g4p_langue['menu_ajout_note'],'</a> 
	<a href="',g4p_make_url('','new_source.php','parent=EVENT&amp;id_parent='.$_REQUEST['id_event'],0),'" class="admin">',$g4p_langue['menu_ajout_source'],'</a> 
	<a href="',g4p_make_url('','new_media.php','parent=EVENT&amp;id_parent='.$_REQUEST['id_event'],0),'" class="admin">',$g4p_langue['menu_ajout_media'],'</a> ';
	echo '</div>';

	echo '<form class="formulaire" method="post" action="'.$_SERVER['PHP_SELF'].'" id="mod_event">';
    echo '<dl class="mod_event">';
    echo '<dt>Type évènement : </dt><dd>'.$select_type_event.'</dd>';
    echo '<dt>Attestation : </dt><dd><input type="checkbox" name="attestation" style="width:55ex;" value="'.@$g4p_event['events_attestation'].'" /></dd>';
    echo '<dt>Age : </dt><dd><input type="text" name="age" style="width:55ex;" value="'.@$g4p_event['events_details_age'].'" /></dd>';
    echo '<dt>Cause : </dt><dd><input type="text" name="cause" style="width:55ex;" value="'.@$g4p_event['events_details_cause'].'" /></dd>';
    echo '</dl>';


echo '
<div class="tab">
  <button type="button" class="tablinks" onclick="openCity(event, \'Date\')" id="defaultOpen">Date</button>
  <button type="button" class="tablinks" onclick="openCity(event, \'Gedcom\')">Format Gedcom</button>
</div>';

echo '<div id="Date" class="tabcontent">';
    echo '<ul>';
    echo '<li>';
    if(!empty($g4p_date->date1))
        g4p_form_date(0, $g4p_date->date1);
    else
        g4p_form_date(0, '');
    echo "</li>\n";

    if(!empty($g4p_date->date2))
    {
        echo '<li><div id="date2">';
        g4p_form_date(1, $g4p_date->date2);
        echo "</div></li>\n";
    }
    else
    {
        echo '<li><div id="date2">';
        g4p_form_date(1, '');
        echo '</div></li>';
    }

    if(!empty($g4p_date->phrase))
    {
        echo '<li>';
        echo 'Commentaire : <input type="text" name="g4p_phrase" style="width:55ex;" value="'.$g4p_date->phrase.'" />';
        echo "</li>\n";
    }
    else
    {
        echo '<li>';
        echo 'Commentaire : <input type="text" name="g4p_phrase" style="width:55ex;" value="" />';
        echo "</li>\n";
    }

    echo '</ul>';
echo '</div>';

echo '
<div id="Gedcom" class="tabcontent">
        <input type="text" name="g4p_gedcom_date" style="width:70ex;" value="'.$g4p_date->gedcom_date.'" />
</div>';

    echo '<dl class="mod_event">';
    echo '<input type="hidden" name="g4p_exec" value="" />';
    echo '<input type="hidden" name="events_details_timestamp" value="'.$g4p_event['events_details_timestamp'].'" />';
    echo '<input type="hidden" name="timestamp_e" value="'.$g4p_event['timestamp_e'].'" />';
    echo '<input type="hidden" name="timestamp_a" value="'.$g4p_event['timestamp_a'].'" />';
    echo '<input type="hidden" name="timestamp_f" value="'.$g4p_event['timestamp_f'].'" />';    
    echo '<input type="hidden" id="date_type" name="date_type" value="date" />';
    echo '<input type="hidden" name="id_event" value="'.$_REQUEST['id_event'].'" />';
    echo '<input type="submit" value="Valider" />';
    echo '</dl>';

    echo '</div>';
    require_once($g4p_chemin.'pied_de_page.php');
}
else
{
	if((int)$_POST['id_event']===0)
		die("Wrong id event");

	$sql="START TRANSACTION";
	$g4p_result_req=$g4p_mysqli->g4p_query($sql);
	if(empty($g4p_result_req))
	{
		$sql="ROLLBACK";
		$g4p_result_req=$g4p_mysqli->g4p_query($sql);
		$_SESSION['errormsg']="Error while saving modification: ".$sql;
		header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));
	}
		
	$sql="SELECT events_details_timestamp FROM genea_events_details FOR UPDATE";
	$g4p_result_req=$g4p_mysqli->g4p_query($sql);
	if(empty($g4p_result_req))
	{
		$sql="ROLLBACK";
		$g4p_result_req=$g4p_mysqli->g4p_query($sql);
		$_SESSION['errormsg']="Error while saving modification: ".$sql;
		header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));
	}
	
	$sql="SELECT timestamp FROM rel_indi_events FOR UPDATE";
	$g4p_result_req=$g4p_mysqli->g4p_query($sql);
	if(empty($g4p_result_req))
	{
		$sql="ROLLBACK";
		$g4p_result_req=$g4p_mysqli->g4p_query($sql);
		$_SESSION['errormsg']="Error while saving modification: ".$sql;
		header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));
	}

	$sql="SELECT timestamp FROM rel_indi_attributes FOR UPDATE";
	$g4p_result_req=$g4p_mysqli->g4p_query($sql);
	if(empty($g4p_result_req))
	{
		$sql="ROLLBACK";
		$g4p_result_req=$g4p_mysqli->g4p_query($sql);
		$_SESSION['errormsg']="Error while saving modification: ".$sql;
		header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));
	}

	$sql="SELECT timestamp FROM rel_familles_events FOR UPDATE";
	$g4p_result_req=$g4p_mysqli->g4p_query($sql);
	if(empty($g4p_result_req))
	{
		$sql="ROLLBACK";
		$g4p_result_req=$g4p_mysqli->g4p_query($sql);
		$_SESSION['errormsg']="Error while saving modification: ".$sql;
		header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));
	}

	//check timestamp has not changed

	if($type_event=='indi_e')
	{
		$event_type='';
		foreach(array_keys($g4p_tag_ievents) as $value)
		{
			if($value==$_POST['g4p_type'])
				$event_type=$value;
				break;
		}

		if($event_type!='')
		{
			$sql="UPDATE rel_indi_events SET events_tag='".$event_type."' WHERE events_details_id=".(int)$_POST['id_event'];
			$g4p_result_req=$g4p_mysqli->g4p_query($sql);
			if(empty($g4p_result_req))
			{
				$sql="ROLLBACK";
				$g4p_result_req=$g4p_mysqli->g4p_query($sql);
				$_SESSION['errormsg']="Error while saving modification: ".$sql;
				header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));
			}
		}
		
		if(isset($_POST['attestation']))
		{
			$sql="UPDATE rel_indi_events SET events_attestation='Y' WHERE events_details_id=".(int)$_POST['id_event'];
			$g4p_result_req=$g4p_mysqli->g4p_query($sql);
			if(empty($g4p_result_req))
			{
				$sql="ROLLBACK";
				$g4p_result_req=$g4p_mysqli->g4p_query($sql);
				$_SESSION['errormsg']="Error while saving modification: ".$sql;
				header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));
			}
		}
		else
		{
			$sql="UPDATE rel_indi_events SET events_attestation=NULL WHERE events_details_id=".(int)$_POST['id_event'];
			$g4p_result_req=$g4p_mysqli->g4p_query($sql);
			if(empty($g4p_result_req))
			{
				$sql="ROLLBACK";
				$g4p_result_req=$g4p_mysqli->g4p_query($sql);
				$_SESSION['errormsg']="Error while saving modification: ".$sql;
				header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));
			}
		}

		
	}
	else if($type_event=='indi_a')
	{
		$event_type='';
		foreach(array_keys($g4p_tag_iattributs) as $value)
		{
			if($value==$_POST['g4p_type'])
				$event_type=$value;
				break;
		}

		if($event_type!='')
		{
			$sql="UPDATE rel_indi_attributs SET events_tag='".$event_type."' WHERE events_details_id=".(int)$_POST['id_event'];
			$g4p_result_req=$g4p_mysqli->g4p_query($sql);
			if(empty($g4p_result_req))
			{
				$sql="ROLLBACK";
				$g4p_result_req=$g4p_mysqli->g4p_query($sql);
				$_SESSION['errormsg']="Error while saving modification: ".$sql;
				header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));
			}
		}
		
		// to be done
		
	}

	//events_details_descriptor
	if(isset($_POST['events_details_descriptor']))
	{
		$sql="UPDATE genea_events_details SET events_details_descriptor='".$g4p_mysqli->escape_string(trim($_POST['events_details_descriptor']))."' WHERE events_details_id=".(int)$_POST['id_event'];
		$g4p_result_req=$g4p_mysqli->g4p_query($sql);
		if(empty($g4p_result_req))
		{
			$sql="ROLLBACK";
			$g4p_result_req=$g4p_mysqli->g4p_query($sql);
			$_SESSION['errormsg']="Error while saving modification: ".$sql;
			header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));
		}
	}
	
	//age
	$sql="UPDATE genea_events_details SET events_details_age='".$g4p_mysqli->escape_string(trim($_POST['age']))."' WHERE events_details_id=".(int)$_POST['id_event'];
	$g4p_result_req=$g4p_mysqli->g4p_query($sql);
	if(empty($g4p_result_req))
	{
		$sql="ROLLBACK";
		$g4p_result_req=$g4p_mysqli->g4p_query($sql);
		$_SESSION['errormsg']="Error while saving modification: ".$sql;
		header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));
	}

	//cause
	$sql="UPDATE genea_events_details SET events_details_cause='".$g4p_mysqli->escape_string(trim($_POST['cause']))."' WHERE events_details_id=".(int)$_POST['id_event'];
	$g4p_result_req=$g4p_mysqli->g4p_query($sql);
	if(empty($g4p_result_req))
	{
		$sql="ROLLBACK";
		$g4p_result_req=$g4p_mysqli->g4p_query($sql);
		$_SESSION['errormsg']="Error while saving modification: ".$sql;
		header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));
	}

	if($_POST['date_type']=='gedcom')
	{
		$sql="UPDATE genea_events_details SET events_details_gedcom_date='".$g4p_mysqli->escape_string(trim($_POST['events_details_gedcom_date']))."' WHERE events_details_id=".(int)$_POST['id_event'];
		$g4p_result_req=$g4p_mysqli->g4p_query($sql);
		if(empty($g4p_result_req))
		{
			$sql="ROLLBACK";
			$g4p_result_req=$g4p_mysqli->g4p_query($sql);
			$_SESSION['errormsg']="Error while saving modification: ".$sql;
			header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));
		}
	}
	else
	{		
		$date='';
		if(!empty($_POST['g4p_phrase']))
			$date.='INT ';
			
		$mod=array('', '');
		for($i=0; $i<2; $i++)
		{
			if(!empty($_POST['date_mod'][$i]))
			{
				//checking value
				$mod[$i]='';
				foreach($g4p_date_modif as $value)
				{
					if($_POST['date_mod'][$i]==$value)
					{
						$mod[$i]=$value.' ';
						break;
					}
				}	
				$date.=$mod[$i];
			}
			
			if(!empty($_POST['date_calendrier'][$i]))
			{
				//checking value
				$checked='';
				foreach($g4p_liste_calendrier as $value)
				{
					if($_POST['date_calendrier'][$i]==$value and $_POST['date_calendrier'][$i]!='@#DGREGORIAN@' )
					{
						$checked=$value.' ';
						break;
					}
				}
				$date.=$checked;
			}
			
			if(!empty($_POST['date_jour'][$i]))
			{
				//checking value
				if((int)$_POST['date_jour'][$i]!==0)
					$date.=strval((int)$_POST['date_jour'][$i]).' ';
			}

			if(!empty($_POST['date_mois'][$i]))
			{
				//checking value
				$checked='';
				foreach($g4p_months as $value)
				{
					if($_POST['date_mois'][$i]==$value)
					{
						$checked=$value.' ';
						break;
					}
				}
				$date.=$checked;
			}

			if(!empty($_POST['date_annee'][$i]))
			{
				//checking value
				if((int)$_POST['date_annee'][$i]!==0)
					$date.=strval((int)$_POST['date_annee'][$i]).' ';
			}
		}

		//check date modifier
		if(trim($mod[0])=='FROM' and trim($mod[1])!='TO')
			die("wrong combination, FROM must be followed by TO");
		if(trim($mod[0])=='BET' and trim($mod[1])!='AND')
			die("wrong combination, BET must be followed by AND");
		
		if(!empty($_POST['g4p_phrase']))
			$date=$date.'('.$_POST['g4p_phrase'].')';
			
		$sql="UPDATE genea_events_details SET events_details_gedcom_date='".$g4p_mysqli->escape_string($date)."' WHERE events_details_id=".(int)$_POST['id_event'];
		$g4p_result_req=$g4p_mysqli->g4p_query($sql);
		if(empty($g4p_result_req))
		{
			$sql="ROLLBACK";
			$g4p_result_req=$g4p_mysqli->g4p_query($sql);
			$_SESSION['errormsg']="Error while saving modification: ".$sql;
			header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));
		}
		
	}
	$sql="COMMIT";
	$g4p_result_req=$g4p_mysqli->g4p_query($sql);
	if(empty($g4p_result_req))
	{
		$sql="ROLLBACK";
		$g4p_result_req=$g4p_mysqli->g4p_query($sql);
		$_SESSION['errormsg']="Error while saving modification: ".$sql;
		header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));
	}

	if($type_event=='indi_e')
	{
		$g4p_indi=g4p_destroy_cache($g4p_event['indi_id_e']);
		foreach($g4p_indi->familles as $g4p_a_famille)
			g4p_destroy_cache($g4p_a_famille->conjoint->indi_id);
	}
	
    $_SESSION['message']='Modification enregistre';
    header('location:'.g4p_make_url('',$_SERVER['PHP_SELF'],'id_event='.$_POST['id_event'],0,0));

}

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
    'BET'=>'Entre'); //BET
    /*
    'TO'=>'à', //TO
    'AND'=>'et'); //AND
    */
    
    if($i==0)
    {
        echo '<select name="date_mod['.$i.']" id="date_mod'.$i.'" style="width:12ex">'."\n";
        echo '<option value="" ></option>'."\n";
        if(empty($date->mod))
            $date->mod='';
        foreach($liste_modificateur1 as $key=>$val)
        {
            if($key==$date->mod)
                $selected='selected="selected"';
            else
                $selected='';
            echo '<option value="',$key,'" ',$selected,'>',$val,'</option>'."\n";
        }
        echo '</select> '."\n";
    }
    else
    {
        echo '<select name="date_mod['.$i.']" id="date_mod'.$i.'" style="width:12ex">'."\n";
        echo '<option value=""></option>'."\n";
        echo '<option value="TO">à</option>'."\n";
        echo '<option value="AND">et</option>'."\n";
        echo '</select> '."\n";
    }
    
    echo '<select name="date_calendrier['.$i.']" style="width:19ex">'."\n";
    echo '<option value="" ></option>'."\n";
    if(empty($date->calendar))
        $date->calendar='';
    foreach($g4p_liste_calendrier as $a_modif_key=>$a_modif_value)
    {
        if(!empty($date->calendar) and $a_modif_key==$date->calendar)
              $selected='selected="selected"';
            else
              $selected='';
        echo '<option value="',$a_modif_key,'" ',$selected,'>',$a_modif_value,'</option>'."\n";
    }
    echo '</select> '."\n";
    if(empty($date->day))
        $date->day='';   
    echo '<input type="text" name="date_jour['.$i.']" value="'.$date->day.'" style="width:3ex" /> '."\n";
    $tmp=array_merge($liste_mois_gregorien,$liste_mois_francais,$liste_mois_hebreux);
    echo '<select name="date_mois['.$i.']" style="width:26ex">'."\n";
    echo '<option value="" ></option>'."\n";
    if(empty($date->month))
        $date->month='';    
    foreach($tmp as $a_modif_key=>$a_modif_value)
    {
        if(!empty($date->month) and $a_modif_key==$date->month)
            $selected='selected="selected"';
        else
            $selected='';
        echo '<option value="',$a_modif_key,'" ',$selected,'>',$a_modif_value,'</option>'."\n";
    }
    echo '</select>'."\n";
    if(empty($date->year))
        $date->year='';   
    echo ' <input type="text" name="date_annee['.$i.']" value="'.$date->year.'" style="width:6ex" />'."\n";
}

?>

