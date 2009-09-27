<?php

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'p_conf/script_start.php');


/******************************************
administration
******************************************/
if(!empty($_GET['del_base_id']))
{
    if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
        $sql="SELECT nom FROM genea_infos WHERE id=".(int)$_GET['del_base_id'];
        if($g4p_infos_req=$g4p_mysqli->g4p_query($sql))
        {
            $g4p_base_nom=$g4p_mysqli->g4p_result($g4p_infos_req);
            $g4p_base_nom=$g4p_base_nom[0]['nom'];
            if(deleteDirectory('cache/'.g4p_base_namefolder($g4p_base_nom).(int)$_GET['del_base_id']))
            {
                $sql="DELETE FROM genea_infos WHERE id=".(int)$_GET['del_base_id'];
                if($g4p_mysqli->g4p_query($sql))
                {
                    $success_del=true;
                    $del_message='La base '.$g4p_base_nom.' a été supprimée avec succès';
                }
                else
                {
                    $success_del=false;
                    $del_message='Erreur lors de la suppression de la base '.$g4p_base_nom;
                }
            }
            else
            {
                $success_del=false;
                $del_message='Impossible de supprimer le cache '.'cache/'.g4p_base_namefolder($g4p_base_nom).(int)$_GET['del_base_id'].' de la base '.$g4p_base_nom;            
            }
        }
        else
        {
            $success_del=false;
            $del_message='Aucune base avec l\'id : '.(int)$_GET['del_base_id'];            
        }
    }
    else
    {
        $success_del=false;
        $del_message='Vous n\'avez pas les droits suffisants pour supprimer la base de données';            
    }
}


if(empty($_SESSION['genea_db_nom']))
    $g4p_titre_h1='Ma généalogie';

require_once($g4p_chemin.'entete.php');

//echo '<div class="box">';
echo '<div class="box_title"><h2>'.$g4p_langue['accueil_titre'].'</h2></div>';

if(isset($success_del))
{
    if($success_del===true)
        echo '<div class="success">'.$del_message.'</div>';
    else
        echo '<div class="error">'.$del_message.'</div>';
}

echo '<dl>';
$g4p_query=$g4p_mysqli->g4p_query("SELECT id, nom, descriptif FROM genea_infos WHERE id<>0 ORDER BY nom");
if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
{
    foreach($g4p_result as $g4p_a_result)
    {
        echo '<dt><a href="'.g4p_make_url('','liste_patronymes.php','genea_db_id='.$g4p_a_result['id'],'patronymes-'.$g4p_a_result['id']).'">'.$g4p_a_result['nom'].'</a>';
        if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
        {
            echo ' <a href="',g4p_make_url('',$_SERVER['PHP_SELF'],'del_base_id='.$g4p_a_result['id'],0),'" onclick=" return confirme(this, \'',$g4p_langue['menu_sppr_confirme'],'\')" class="admin">Supprimer</a>';
        }
        echo '</dt>';
        echo '<dd>'.$g4p_a_result['descriptif'].'</dd>';
    }
}
echo '</dl>';
//echo '</div>';

//dernière modification
//echo '<div class="box">';
echo '<div class="box_title"><h2>Dernières modifications</h2></div>';

echo '<div class="box">';
echo '<div class="box_title"><h3>Individus</h3></div>';
echo '<dl>';
$g4p_query=$g4p_mysqli->g4p_query("SELECT indi_id FROM genea_individuals ORDER BY indi_timestamp DESC LIMIT 5");
if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
{
    foreach($g4p_result as $g4p_a_result)
    {
        $indi_tmp=g4p_load_indi_infos($g4p_a_result['indi_id']);
        echo '<dt>'.g4p_timestamp($indi_tmp->timestamp).'</dt><dd>'.g4p_link_nom($indi_tmp).'</dd>';
    }
}
echo '</dl>';
echo '</div>';

echo '<div class="box">';
echo '<div class="box_title"><h3>évènements et attributs individuels</h3></div>';
echo '<dl>';
$g4p_query=$g4p_mysqli->g4p_query("SELECT IF(events.indi_id IS NULL,attributes.indi_id,events.indi_id) AS indi_id,
    IF(events.indi_id IS NULL,'attributes','events') AS type, 
    events_details_id FROM genea_events_details 
    LEFT JOIN rel_indi_events AS events USING (events_details_id)
    LEFT JOIN rel_indi_attributes AS attributes USING (events_details_id)
    WHERE events.indi_id IS NOT NULL OR attributes.indi_id IS NOT NULL 
    ORDER BY events_details_timestamp DESC LIMIT 3");
if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
{
    foreach($g4p_result as $g4p_a_result)
    {
        $indi_tmp=g4p_load_indi_infos($g4p_a_result['indi_id']);
        if($g4p_a_result['type']=='events')
        {
            if(isset($indi_tmp->events[$g4p_a_result['events_details_id']]))
            {
                echo '<dt>'.g4p_link_nom($indi_tmp).'</dt>';
                echo '<dd>'.$g4p_tag_def[$indi_tmp->events[$g4p_a_result['events_details_id']]->tag].' '.
                    '<span class="date">',g4p_date($indi_tmp->events[$g4p_a_result['events_details_id']]->gedcom_date),
                    '</span> '.g4p_timestamp($indi_tmp->events[$g4p_a_result['events_details_id']]->timestamp).'</dd>';
            }
        }
        else
        {
            if(isset($indi_tmp->attributes[$g4p_a_result['events_details_id']]))
            {
                echo '<dt>'.g4p_link_nom($indi_tmp).'</dt>';
                echo '<dd>'.$g4p_tag_def[$indi_tmp->attributes[$g4p_a_result['events_details_id']]->tag].' '.
                    '<span class="date">',g4p_date($indi_tmp->attributes[$g4p_a_result['events_details_id']]->gedcom_date),
                    '</span> '.g4p_timestamp($indi_tmp->attributes[$g4p_a_result['events_details_id']]->timestamp).'</dd>';
            }
        }        
    }
}
echo '</dl>';
echo '</div>';

echo '<div class="box">';
echo '<div class="box_title"><h3>évènements familiaux</h3></div>';
echo '<dl>';
$g4p_query=$g4p_mysqli->g4p_query("SELECT familles_id, events_details_id, familles_husb, familles_wife FROM genea_events_details 
    LEFT JOIN rel_familles_events USING (events_details_id)
    LEFT JOIN genea_familles USING (familles_id)
    WHERE familles_id IS NOT NULL
    ORDER BY events_details_timestamp DESC LIMIT 3");
if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
{
    foreach($g4p_result as $g4p_a_result)
    {
        $indi_tmp=g4p_load_indi_infos($g4p_a_result['familles_husb']);
        echo '<dt>'.
            g4p_link_nom($indi_tmp).' &mdash;  '.
            g4p_link_nom($indi_tmp->familles[$g4p_a_result['familles_id']]->wife).' '.
            '</dt>';
            
        echo '<dd>'.$g4p_tag_def[$indi_tmp->familles[$g4p_a_result['familles_id']]->events[$g4p_a_result['events_details_id']]->tag].' '.
            '<span class="date">',g4p_date($indi_tmp->familles[$g4p_a_result['familles_id']]->events[$g4p_a_result['events_details_id']]->gedcom_date),
            '</span> '.g4p_timestamp($indi_tmp->familles[$g4p_a_result['familles_id']]->events[$g4p_a_result['events_details_id']]->timestamp).'</dd>';
    }
}
echo '</dl>';
echo '</div>';

$g4p_query=$g4p_mysqli->g4p_query("SELECT notes_id, notes_text, notes_timestamp FROM genea_notes 
    ORDER BY notes_timestamp DESC LIMIT 3");
if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
{
    echo '<div class="box">';
    echo '<div class="box_title"><h3>Notes</h3></div>';
    echo '<dl>';
    foreach($g4p_result as $g4p_a_result)
    {
        echo '<dt>Note '.$g4p_a_result['notes_id'].g4p_timestamp($g4p_a_result['notes_timestamp']).' [détail]</dt>';
        echo '<dd>'.word_limiter($g4p_a_result['notes_text'],100).'</dd>';
    }
    echo '</dl>';
    echo '</div>';
}

echo '<h3>Sources</h3>';

echo '<h3>Médias</h3>';

require_once($g4p_chemin.'pied_de_page.php');

?>
