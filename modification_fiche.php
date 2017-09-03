<?php
$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

if(!empty($_POST) and !empty($_POST['id_pers']))
{
    if(!empty($_POST['nom']) and !empty($_POST['prenom']))
    {
          $sql="UPDATE genea_individuals SET indi_nom='".$g4p_mysqli->escape_string($_POST['nom'])."', 
            indi_prenom='".$g4p_mysqli->escape_string($_POST['prenom'])."', 
            indi_sexe='".$g4p_mysqli->escape_string($_POST['sexe'])."', 
            indi_timestamp=NOW(), indi_npfx='".$g4p_mysqli->escape_string($_POST['npfx'])."', 
            indi_givn='".$g4p_mysqli->escape_string($_POST['givn'])."', 
            indi_nick='".$g4p_mysqli->escape_string($_POST['nick'])."', 
            indi_spfx='".$g4p_mysqli->escape_string($_POST['spfx'])."', 
            indi_nsfx='".$g4p_mysqli->escape_string($_POST['nsfx'])."' 
            WHERE indi_id='".$_POST['id_pers']."'";
        if($g4p_mysqli->g4p_query($sql))
            $_SESSION['message']=$g4p_langue['message_req_succes'];
        else
            $_SESSION['message']=$g4p_langue['message_req_echec'];

        $g4p_indi=g4p_destroy_cache($_POST['id_pers']);
        g4p_update_agregat_noms($_POST['nom']);
    }
    elseif(!empty($_POST['alia']))
    {
        $_POST['alia']=explode(' ',$_POST['alia']);
        $_POST['alia']=(int)$_POST['alia'][0];    
        if($_POST['alia']!=$_POST['id_pers'])
        {
            $sql="SELECT alias1 FROM rel_alias WHERE 
                (alias1=".$_POST['alia']." AND alias2=".$_POST['id_pers'].") OR 
                (alias2=".$_POST['alia']." AND alias1=".$_POST['id_pers'].")";
            $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
            if(!$g4p_mysqli->g4p_result($g4p_infos_req))
            {  
                if($_POST['alia']<$_POST['id_pers'])
                {
                    $alia1=$_POST['alia'];
                    $alia2=$_POST['id_pers'];
                }
                else
                {
                    $alia2=$_POST['alia'];
                    $alia1=$_POST['id_pers'];
                }
                $sql="INSERT INTO rel_alias (alias1,alias2) VALUES (".$alia1.",".$alia2.")";
                if($g4p_mysqli->g4p_query($sql))
                    $_SESSION['message']='L\'alias a été ajouté avec succès';
                else
                    $_SESSION['message']='Erreur lors de l\'ajout de l\'alias';
                g4p_destroy_cache($_POST['alia']);
                g4p_destroy_cache($_POST['id_pers']);
            }
            else
                $_SESSION['message']='L\'alias éxiste déjà';
        }
    }
}


if(!isset($_REQUEST['id_pers']))
    die('id_pers is not defined');

$g4p_indi=g4p_load_indi_infos($_REQUEST['id_pers']);
if(empty($g4p_indi))
    g4p_error('Erreur lors du chargement des données de l\'individu');

if(!$_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    die('Unsufficient right to edit the file');

$g4p_javascript='<script src="javascript/jquery/jquery-1.3.1.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.bgiframe.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.dimensions.js"></script>
  <script type="text/javascript" src="javascript/jquery/jquery.autocomplete.min.js"></script>
  <script>
  $(document).ready(function(){
    $("#alia").autocomplete(\'ajax/autocomplete.php\', {max:40});
  });
  </script>
';

require_once($g4p_chemin.'entete.php');
echo '<div class="box_title"><h2>'.$g4p_indi->prenom,' ',$g4p_indi->nom.'</h2></div>'."\n";

echo '<div class="box">';
echo '<div class="box_title">État civil</div>';        
if($g4p_indi->timestamp!='0000-00-00 00:00:00')
    echo '<span class="petit">',sprintf($g4p_langue['index_chan'],g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_indi->timestamp))),'</span><br />';
if($g4p_indi->resn=='privacy')
    echo'<span class="petit">',$g4p_langue['index_masquer'],'</span><br />';
echo '<form class="formulaire" method="post" action="'.$_SERVER['PHP_SELF'].'" id="etat_civil">';
echo '<dl class="etat_civil">';
echo '<dt>Id : </dt><dd>',number_format($g4p_indi->indi_id, 0, ',', ' '),'</dd>';
echo '<dt>',$g4p_langue['index_nom'],'</dt><dd><input type="text" name="nom" size="20" value="',$g4p_indi->nom,'" /></dd>';
echo '<dt>',$g4p_langue['index_prenom'],'</dt><dd><input type="text" name="prenom" size="20" value="',$g4p_indi->prenom,'" /></dd>';
echo '<dt>',$g4p_langue['index_sexe'],'</dt><dd><select name="sexe" size="1">';
foreach($g4p_langue['index_sexe_valeur'] as $val=>$desc)
    echo '<option value="',$val,'" ',($g4p_indi->sexe==$val)?'selected="selected"':'','>',$desc,'</option>';
echo '</select></dd>';
echo '<dt>',$g4p_langue['index_npfx'],'</dt><dd><input type="text" name="npfx" size="20" value="',$g4p_indi->npfx,'" /></dd>';
echo '<dt>',$g4p_langue['index_givn'],'</dt><dd><input type="text" name="givn" size="20" value="',$g4p_indi->givn,'" /></dd>';
echo '<dt>',$g4p_langue['index_nick'],'</dt><dd><input type="text" name="nick" size="20" value="',$g4p_indi->nick,'" /></dd>';
echo '<dt>',$g4p_langue['index_spfx'],'</dt><dd><input type="text" name="spfx" size="20" value="',$g4p_indi->spfx,'" /></dd>';
echo '<dt>',$g4p_langue['index_nsfx'],'</dt><dd><input type="text" name="nsfx" size="20" value="',$g4p_indi->nsfx,'" /></dd>';
echo '<dt><input type="hidden" name="id_pers" value="',$g4p_indi->indi_id,'" /><input type="submit" value="',$g4p_langue['submit_modifier'],'" /><dd>&nbsp;</dd>';
echo '</dl>';    
echo '</form>';
echo '</div>';

//nouvel ALIAS
echo '<form class="formulaire" method="post" action="'.$_SERVER['PHP_SELF'].'" name="modif_fich">';
echo $g4p_langue['a_index_ajout_alias'];
if(!empty($_SESSION['historic']['indi']))
{
    echo '<select name="alia2" style="width:auto"><option value=""></option>';
    foreach($_SESSION['historic']['indi'] as $tmp)
        echo '<option value="'.$tmp['id'].'">'.htmlentities($tmp['text'],ENT_NOQUOTES,'UTF-8').'</option>';
    echo '</select>',$g4p_langue['a_index_ajout_alias_ou'];
}
echo '<input type="text" id="alia" name="alia" style="width:300px" />';   
echo '<input type="hidden" name="id_pers" value="',$g4p_indi->indi_id,'" />
    <input type="submit" value="',$g4p_langue['submit_ajouter'],'" />
    </form>';
//ALIAS
if(!empty($g4p_indi->alias))
    g4p_show_alias($g4p_indi->alias);
        
//evenements individuels
echo '<div class="box">';
echo '<div class="box_title">Évènements</div>';        
echo '<a class="admin" href="',g4p_make_url('admin','index.php','g4p_opt=ajout_event&amp;g4p_id=i|'.$g4p_indi->indi_id),'">',$g4p_langue['a_index_ajout_ievent'],'</a>';
if(!empty($g4p_indi->events))
{               
    echo '<dl class="evenements">';
    //$g4p_indi->events=array_column_sort($g4p_indi->events,'jd_count');
    foreach($g4p_indi->events as $g4p_a_ievents)
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
        echo (isset($g4p_a_ievents->id))?(' <a href="'.g4p_make_url('','detail_event.php','parent=INDI&amp;id_parent='.$g4p_a_ievents->id,0).'" class="noprint">'.$g4p_langue['detail'].'</a><br />'):('<br />');
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

//attributs individuels
echo '<div class="box">';
echo '<div class="box_title">Attributs</div>';        
echo '<a class="admin" href="',g4p_make_url('admin','index.php','g4p_opt=ajout_event&amp;g4p_id=i|'.$g4p_indi->indi_id),'">Ajouter un nouvel attribut</a>';
if(!empty($g4p_indi->attributes))
{
    echo '<dl class="evenements">';
    //$g4p_indi->events=array_column_sort($g4p_indi->events,'jd_count');
    foreach($g4p_indi->attributes as $g4p_a_ievents)
    {
        //echo '<pre>'; print_r($g4p_a_ievents);
        echo '<dt><em>',$g4p_tag_def[$g4p_a_ievents->tag],'</em> : ',$g4p_a_ievents->description;
        echo (isset($g4p_a_ievents->sources))?(' <span style="color:blue; font-size:x-small;">-S-</span> '):('');
        echo (isset($g4p_a_ievents->notes))?(' <span style="color:blue; font-size:x-small;">-N-</span> '):('');
        echo (isset($g4p_a_ievents->medias))?(' <span style="color:blue; font-size:x-small;">-M-</span> '):('');
        echo (isset($g4p_a_ievents->asso))?(' <span style="color:blue; font-size:x-small;">-T-</span> '):('');
        echo (isset($g4p_a_ievents->id))?(' <a href="'.g4p_make_url('','detail_event.php','g4p_id=i|'.$g4p_indi->indi_id.'|'.$g4p_a_ievents->id,0).'" class="noprint">'.$g4p_langue['detail'].'</a><br />'):('<br />');
        echo '</dt>';
        echo '<dd><em>Date : </em><span class="date">',g4p_date($g4p_a_ievents->gedcom_date),'</span> </dd>';
        
        //age
        echo (!empty($g4p_a_ievents->age))?('<dd><em>Age : </em>'.$g4p_a_ievents->age.'</dd>'):('');

        //place
        if(isset($g4p_a_ievents->place))
            echo '<dd><em>Lieu : </em>',$g4p_a_ievents->place->g4p_formated_place(),' [détail]</dd>';
        
        //adresse
        if(isset($g4p_a_ievents->address))
            echo '<dd><em>Adresse : </em>',$g4p_a_ievents->address->g4p_formated_addr(),' [détail]</dd>';
    }
    echo '</dl>';
}
echo '</div>';

g4p_affiche_mariage();

// les parents
if(!empty($g4p_indi->parents))
{
    foreach($g4p_indi->parents as $g4p_a_parent)
    {
        echo '<div class="box">';
        if(empty($g4p_a_parent->rela_type))
            $g4p_a_parent->rela_type='BIRTH';
        echo '<div class="box_title">Parents '.str_replace(array_keys($g4p_lien_def),array_values($g4p_lien_def),$g4p_a_parent->rela_type). '</div>';
        //echo '<em>',$g4p_langue['index_ype_parent'],'</em>',str_replace(array_keys($g4p_lien_def),array_values($g4p_lien_def),$g4p_a_parent->rela_type);
        echo '<ul style="list-style-type:none;padding:0;">';
        if(isset($g4p_a_parent->pere))
            echo '<li>'.g4p_link_nom($g4p_a_parent->pere).'</li>';
        else
            echo '<li>',$g4p_langue['index_parent_inconnu'],'</li>';

        if(isset($g4p_a_parent->mere))
            echo '<li>'.g4p_link_nom($g4p_a_parent->mere).'</li>';
        else
            echo '<li>',$g4p_langue['index_parent_inconnu'].'</li>';
        echo '</ul>';
        echo '</div>';
    }
}

g4p_affiche_asso(@$g4p_indi->asso, $g4p_indi->indi_id,'indi');

if ($_SESSION['permission']->permission[_PERM_NOTE_])
    g4p_affiche_notes(@$g4p_indi->notes,$g4p_indi->indi_id, 'indi');
    
if ($_SESSION['permission']->permission[_PERM_SOURCE_])
    g4p_affiche_sources(@$g4p_indi->sources,$g4p_indi->indi_id, 'indi');

if ($_SESSION['permission']->permission[_PERM_MULTIMEDIA_])
    g4p_affiche_multimedia(@$g4p_indi->multimedia, $g4p_indi->indi_id, 'indi');

echo '</div>';

require_once($g4p_chemin.'pied_de_page.php');

?>

