<?php
$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

if(!isset($_REQUEST['id_famille']))
    die('id_famille is not defined');

if(!isset($_REQUEST['id_pers']))
{
	$sql="SELECT familles_husb, familles_wife FROM genea_familles WHERE familles_id=".(int)$_REQUEST['id_famille'];
    $g4p_result=$g4p_mysqli->g4p_query($sql);
    $g4p_result=$g4p_mysqli->g4p_result($g4p_result);
    if(!empty($g4p_result[0]))
    {
		if(!empty($g4p_result[0]['familles_husb']))
			$_REQUEST['id_pers']=$g4p_result[0]['familles_husb'];
		else if(!empty($g4p_result[0]['familles_wifw']))
			$_REQUEST['id_pers']=$g4p_result[0]['familles_wife'];
		else
			die("Error getting husb or wife id");
	}
}	
	
if(!$_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    die('Unsufficient right to edit the file');

//modif des conjoints
if(!empty($_POST['wife']) or !empty($_POST['wife2']) or !empty($_POST['husb']) or !empty($_POST['husb2']))
{
    if(!empty($_POST['wife']))
    {
        $g4p_conjoint[]=' familles_wife='.(int)$_POST['wife'];
        $id_wife=(int)$_POST['wife'];
    }
    if(!empty($_POST['wife2']))
    {
        $tmp=explode(' ',$_POST['wife2']);
        $g4p_conjoint[]=' familles_wife='.(int)$tmp[0];
        $id_wife=(int)$tmp[0];
    }

    if(!empty($_POST['husb']))
    {
        $g4p_conjoint[]=' familles_husb='.(int)$_POST['husb'];
        $id_husb=(int)$_POST['husb'];
    }
    if(!empty($_POST['husb2']))
    {
        $tmp=explode(' ',$_POST['husb2']);
        $g4p_conjoint[]=' familles_husb='.(int)$tmp[0];
        $id_husb=(int)$tmp[0];        
    }
    
    if(!empty($g4p_conjoint))
    {
        $sql="UPDATE genea_familles SET ".implode(',',$g4p_conjoint)." WHERE familles_id=".(int)$_REQUEST['id_famille'];
        if($g4p_mysqli->g4p_query($sql))       
            $success_modfam=true;
        else
            $success_modfam=false;
        
        if(!empty($id_wife))
            g4p_destroy_cache($id_wife);
        if(!empty($id_husb))
            g4p_destroy_cache($id_husb);
        g4p_destroy_cache($_REQUEST['id_pers']);
        if((empty($id_wife) or $_REQUEST['id_pers']!=$id_wife) and (empty($id_husb) or $_REQUEST['id_pers']!=$id_husb))
        {
            if(!empty($id_husb))
                $_REQUEST['id_pers']=$id_husb;
            elseif(!empty($id_wife))
                $_REQUEST['id_pers']=$id_wife;
        }
    }
}

//Suppression d'un conjoint
if(!empty($_GET['del_wife']))
{
    $g4p_indi=g4p_load_indi_infos((int)$_REQUEST['id_pers']);
    $sql="UPDATE genea_familles SET familles_wife=NULL WHERE familles_id=".(int)$_REQUEST['id_famille'];
    if($g4p_mysqli->g4p_query($sql))       
        $success_delconjoint=true;
    else
        $success_delconjoint=false;
    
    if(isset($g4p_indi->familles[(int)$_REQUEST['id_famille']]->husb->indi_id))
        g4p_destroy_cache($g4p_indi->familles[(int)$_REQUEST['id_famille']]->husb->indi_id);
    if(isset($g4p_indi->familles[(int)$_REQUEST['id_famille']]->wife->indi_id))
        g4p_destroy_cache($g4p_indi->familles[(int)$_REQUEST['id_famille']]->wife->indi_id);
}
if(!empty($_GET['del_husb']))
{
    $g4p_indi=g4p_load_indi_infos((int)$_REQUEST['id_pers']);
    $sql="UPDATE genea_familles SET familles_husb=NULL WHERE familles_id=".(int)$_REQUEST['id_famille'];
    if($g4p_mysqli->g4p_query($sql))       
        $success_delconjoint=true;
    else
        $success_delconjoint=false;
    
    if(isset($g4p_indi->familles[(int)$_REQUEST['id_famille']]->husb->indi_id))
        g4p_destroy_cache($g4p_indi->familles[(int)$_REQUEST['id_famille']]->husb->indi_id);
    if(isset($g4p_indi->familles[(int)$_REQUEST['id_famille']]->wife->indi_id))
        g4p_destroy_cache($g4p_indi->familles[(int)$_REQUEST['id_famille']]->wife->indi_id);
}

//suppression d'un enfant
if(!empty($_GET['del_child']))
{
    $del_indi=g4p_load_indi_infos((int)$_REQUEST['del_child']);
    $sql="DELETE FROM rel_familles_indi WHERE indi_id=".(int)$_REQUEST['del_child']." AND familles_id=".(int)$_REQUEST['id_famille'];
    if($g4p_mysqli->g4p_query($sql))       
        $success_delc=true;
    else
        $success_delc=false;
        
    g4p_destroy_cache($_REQUEST['del_child']);
    g4p_destroy_cache($_REQUEST['id_pers']);
}

//ajout d'un nouvel enfant
if(!empty($_POST['add_child1']) or !empty($_POST['add_child2']))
{
    if(!empty($_POST['add_child1']))
        $add_child=$_POST['add_child1'];
    else
        $add_child=$_POST['add_child2'];

    $add_child=explode(' ',$add_child);
    $add_child=(int)$add_child[0];
    $sql="INSERT INTO rel_familles_indi (indi_id, familles_id) VALUES (".$add_child.",".(int)$_REQUEST['id_famille'].")";
    if($g4p_mysqli->g4p_query($sql))       
    {
        $success_addc=true;
        g4p_destroy_cache($add_child);
        g4p_destroy_cache($_REQUEST['id_pers']);
        $add_indi=g4p_load_indi_infos((int)$add_child);
    }
    else
        $success_addc=false;
}

$g4p_indi=g4p_load_indi_infos((int)$_REQUEST['id_pers']);
if(empty($g4p_indi))
    g4p_error('Erreur lors du chargement des données de l\'individu');
$g4p_famille=$g4p_indi->familles[(int)$_REQUEST['id_famille']];

$g4p_javascript='<script src="javascript/jquery/jquery-1.3.1.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.bgiframe.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.dimensions.js"></script>
  <script type="text/javascript" src="javascript/jquery/jquery.autocomplete.min.js"></script>
  <script>
  $(document).ready(function(){
    $("#husb").autocomplete(\'ajax/autocomplete.php\', {max:40});
  });
  $(document).ready(function(){
    $("#wife").autocomplete(\'ajax/autocomplete.php\', {max:40});
  });
  $(document).ready(function(){
    $("#child").autocomplete(\'ajax/autocomplete.php\', {max:40});
  });
  </script>
';

require_once($g4p_chemin.'entete.php');
if(!empty($g4p_famille->husb->nom) and !empty($g4p_famille->wife->nom))
    echo '<div class="box_title"><h2>Couple '.$g4p_famille->husb->nom.' &mdash; '.$g4p_famille->wife->nom.'</h2></div>'."\n";
elseif(!empty($g4p_famille->husb->nom))
    echo '<div class="box_title"><h2>Couple '.$g4p_famille->husb->nom.' &mdash; Inconnu</h2></div>'."\n";
elseif(!empty($g4p_famille->husb->wife))
    echo '<div class="box_title"><h2>Couple '.$g4p_famille->wife->nom.' &mdash; Inconnu</h2></div>'."\n";
else
    echo '<div class="box_title"><h2>Couple Inconnu &mdash; Inconnu</h2></div>'."\n";

echo '<div class="box">';
echo '<div class="box_title">Couple</div>';        
if($g4p_indi->timestamp!='0000-00-00 00:00:00')
    echo '<span class="petit">',sprintf($g4p_langue['index_chan'],g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_famille->timestamp))),'</span><br />';
if(isset($success_modfam) and $success_modfam===true)
    echo '<div class="success" style="width:80%">Modification des conjoints effectuée avec succès</div>';
if(isset($success_delconjoint) and $success_delconjoint===true)
    echo '<div class="success" style="width:80%">Conjoint supprimé avec succès</div>';
echo '<form class="formulaire" method="post" action="'.$_SERVER['PHP_SELF'].'" id="mod_fam">';
echo '<p>Id : ',number_format($g4p_famille->id, 0, ',', ' '),'</p>';
echo '1er conjoint (homme)  ';
if(!empty($_SESSION['historic']['indi']))
{
    echo '<select name="husb2" style="width:auto"><option value=""></option>';
    foreach($_SESSION['historic']['indi'] as $tmp)
    {
        echo '<option value="'.$tmp['id'].'">'.htmlentities($tmp['text'],ENT_NOQUOTES,'UTF-8').'</option>';
    }
    echo '</select>',$g4p_langue['a_index_ajout_alias_ou'];
}   
echo ' <input type="text" id="husb" name="husb" size="25" value="" /><p>';
if(!empty($g4p_famille->husb->nom))
    echo 'Valeur courante : '.g4p_link_nom($g4p_famille->husb->indi_id).' <a href="',g4p_make_url('',$_SERVER['PHP_SELF'],'id_pers='.$g4p_indi->indi_id.'&id_famille='.$g4p_famille->id.'&del_husb='.$g4p_famille->husb->indi_id,0),'" class="admin" onclick=" return confirme(this, \'',$g4p_langue['menu_sppr_confirme'],'\')">Supprimer le conjoint</a></p></dd>';
echo '2ème conjoint (femme)  ';
if(!empty($_SESSION['historic']['indi']))
{
    echo '<select name="wife2" style="width:auto"><option value=""></option>';
    foreach($_SESSION['historic']['indi'] as $tmp)
    {
        echo '<option value="'.$tmp['id'].'">'.htmlentities($tmp['text'],ENT_NOQUOTES,'UTF-8').'</option>';
    }
    echo '</select>',$g4p_langue['a_index_ajout_alias_ou'];
}    
echo ' <input type="text" id="wife" name="wife" size="25" value="" /><p>';
if(!empty($g4p_famille->wife->nom))
    echo 'Valeur courante : '.g4p_link_nom($g4p_famille->wife->indi_id).' <a href="',g4p_make_url('',$_SERVER['PHP_SELF'],'id_pers='.$g4p_indi->indi_id.'&id_famille='.$g4p_famille->id.'&del_wife='.$g4p_famille->wife->indi_id,0),'" class="admin" onclick=" return confirme(this, \'',$g4p_langue['menu_sppr_confirme'],'\')">Supprimer le conjoint</a></p></dd>';

echo '<input type="hidden" id="id_pers" name="id_pers" value="'.(int)$_REQUEST['id_pers'].'" />
    <input type="hidden" id="id_famille" name="id_famille" value="'.(int)$_REQUEST['id_famille'].'" />
    <input type="submit" value="Valider" />';
    
echo '</form>';
echo '</div>';
      
//evenements familiaux
echo '<div class="box">';
echo '<div class="box_title">Évènements</div>';        
echo '<a class="admin" href="',g4p_make_url('','modification_event.php','id_famille='.$g4p_famille->id),'">',$g4p_langue['a_index_ajout_ievent'],'</a>';
if(!empty($g4p_famille->events))
{               
    echo '<dl class="evenements">';
    //$g4p_indi->events=array_column_sort($g4p_indi->events,'jd_count');
    foreach($g4p_famille->events as $g4p_a_ievents)
    {
        //echo '<pre>'; print_r($g4p_a_ievents);
        if($g4p_a_ievents->details_descriptor)
            $g4p_tmp=' ('.$g4p_a_ievents->details_descriptor.')';
        else
            $g4p_tmp='';
        echo '<dt><em>',$g4p_tag_def[$g4p_a_ievents->tag],'</em> ';
        echo $g4p_tmp,' : ';
        echo '<span class="date">',g4p_date($g4p_a_ievents->gedcom_date),'</span> ';
        echo (isset($g4p_a_ievents->sources))?(' <span style="color:blue; font-size:x-small;">-S-</span> '):('');
        echo (isset($g4p_a_ievents->notes))?(' <span style="color:blue; font-size:x-small;">-N-</span> '):('');
        echo (isset($g4p_a_ievents->medias))?(' <span style="color:blue; font-size:x-small;">-M-</span> '):('');
        echo (isset($g4p_a_ievents->asso))?(' <span style="color:blue; font-size:x-small;">-T-</span> '):('');
        echo (isset($g4p_a_ievents->id))?(' <a href="'.g4p_make_url('','detail_event.php','id_event='.$g4p_a_ievents->id,0).'" class="noprint">'.$g4p_langue['detail'].'</a><br />'):('<br />');
        echo '</dt>';
                
        //age
        echo (!empty($g4p_a_ievents->age))?('<dd><em>Age : </em>'.$g4p_a_ievents->age.'</dd>'):('');

        //place
        if($g4p_a_ievents->place->g4p_formated_place()!='')
            echo '<dd><em>Lieu : </em>'.$g4p_a_ievents->place->g4p_formated_place(),' [détail]</dd>';
        
        //adresse
        if(!empty($g4p_a_ievents->address))
            echo '<dd><em>Adresse : </em>'.$g4p_a_ievents->address->g4p_formated_addr(),' [détail]</dd>';
    }
    echo '</dl>';
}
echo '</div>';

echo '<div class="box">';
echo '<div class="box_title">Enfant(s) issu(s) du mariage </div>'; 
if(isset($success_delc) and $success_delc===true)
    echo '<div class="success" style="width:80%">Lien supprimé avec succès : '.$del_indi->prenom.' '.$del_indi->nom.'</div>';
elseif(isset($success_delc) and $success_delc===false)
    echo'<div class="error">Erreur lors de la suppression du lien</div>';
if(isset($success_addc) and $success_addc===true)
    echo '<div class="success" style="width:80%">Nouvel enfant ajouté : '.$add_indi->prenom.' '.$add_indi->nom.'</div>';

        //les enfants des mariages
        if(isset($g4p_famille->enfants))
        {
            echo '<ul style="list-style-type:none;padding:0;">';
            foreach($g4p_famille->enfants as $g4p_a_enfant)
            {
                echo '<li>'.$g4p_a_enfant['rela_type'].' '.g4p_link_nom($g4p_a_enfant['indi']).' <a href="',g4p_make_url('',$_SERVER['PHP_SELF'],'id_pers='.$g4p_indi->indi_id.'&id_famille='.$g4p_famille->id.'&del_child='.$g4p_a_enfant['indi']->indi_id,0),'" class="admin" onclick=" return confirme(this, \'',$g4p_langue['menu_sppr_confirme'],'\')">Supprimer le lien</a></li>';
            }
            echo '</ul>';
        }

echo '<form class="formulaire" method="post" action="'.$_SERVER['PHP_SELF'].'" id="add_child">
    Nouvel enfant :';
if(!empty($_SESSION['historic']['indi']))
{
    echo '<select name="add_child1" style="width:auto"><option value=""></option>';
    foreach($_SESSION['historic']['indi'] as $tmp)
    {
        echo '<option value="'.$tmp['id'].'">'.htmlentities($tmp['text'],ENT_NOQUOTES,'UTF-8').'</option>';
    }
    echo '</select>',$g4p_langue['a_index_ajout_alias_ou'];
}    
echo '<input type="hidden" id="id_pers" name="id_pers" value="'.(int)$_REQUEST['id_pers'].'" />
    <input type="hidden" id="id_famille" name="id_famille" value="'.(int)$_REQUEST['id_famille'].'" />
    <input type="text" id="child" name="add_child2" size="25" value="" /> <input type="submit" value="Ajouter" /></form>';

echo '</div>';
echo '</div>';

require_once($g4p_chemin.'pied_de_page.php');

?>

