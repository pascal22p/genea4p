<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                                                          *
 *  Copyright (C) 2004  PAROIS Pascal                                       *
 *                                                                          *
 *  This program is free software; you can redistribute it and/or modify    *
 *  it under the terms of the GNU General Public License as published by    *
 *  the Free Software Foundation; either version 2 of the License, or       *
 *  (at your option) any later version.                                     *
 *                                                                          *
 *  This program is distributed in the hope that it will be useful,         *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of          *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           *
 *  GNU General Public License for more details.                            *
 *                                                                          *
 *  You should have received a copy of the GNU General Public License       *
 *  along with this program; if not, write to the Free Software             *
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA *
 *                                                                          *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                        Page d'accueil                                   *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'p_conf/script_start.php');

if(empty($_SESSION['genea_db_nom']))
    $g4p_titre_h1='Ma généalogie';

require_once($g4p_chemin.'entete.php');

//echo '<div class="box">';
echo '<div class="box_title"><h2>'.$g4p_langue['accueil_titre'].'</h2></div>';

echo '<dl>';
$g4p_query=$g4p_mysqli->g4p_query("SELECT id, nom, descriptif FROM genea_infos ORDER BY nom");
if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
{
    foreach($g4p_result as $g4p_a_result)
    {
        echo '<dt><a href="'.g4p_make_url('','liste_patronymes.php','genea_db_id='.$g4p_a_result['id'],'patronymes-'.$g4p_a_result['id']).'">'.$g4p_a_result['nom'].'</a></dt>';
        echo '<dd>'.$g4p_a_result['descriptif'].'</dd>';
    }
}
echo '</dl>';
//echo '</div>';

//dernière modification
//echo '<div class="box">';
echo '<div class="box_title"><h2>Dernières modifications</h2></div>';

echo '<h3>Individus</h3>';
echo '<ul>';
$g4p_query=$g4p_mysqli->g4p_query("SELECT indi_id FROM genea_individuals ORDER BY indi_timestamp DESC LIMIT 5");
if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
{
    foreach($g4p_result as $g4p_a_result)
    {
        $indi_tmp=g4p_load_indi_infos($g4p_a_result['indi_id']);
        echo '<li>'.g4p_link_nom($indi_tmp).' '.g4p_birth_death($indi_tmp->timestamp).'</li>';
    }
}
echo '</ul>';

echo '<h3>évènements et attributs individuels</h3>';
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
            echo '<dt>'.g4p_link_nom($indi_tmp).'</dt>';
            echo '<dd>'.$g4p_tag_def[$indi_tmp->events[$g4p_a_result['events_details_id']]->tag].' '.
                '<span class="date">',g4p_date($indi_tmp->events[$g4p_a_result['events_details_id']]->gedcom_date),
                '</span> '.g4p_birth_death($indi_tmp->events[$g4p_a_result['events_details_id']]->timestamp).'</dd>';
        }
        else
        {
            echo '<dt>'.g4p_link_nom($indi_tmp).'</dt>';
            echo '<dd>'.$g4p_tag_def[$indi_tmp->attributes[$g4p_a_result['events_details_id']]->tag].' '.
                '<span class="date">',g4p_date($indi_tmp->attributes[$g4p_a_result['events_details_id']]->gedcom_date),
                '</span> '.g4p_birth_death($indi_tmp->attributes[$g4p_a_result['events_details_id']]->timestamp).'</dd>';
        }        
    }
}
echo '</dl>';

echo '<h3>évènements familiaux</h3>';
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
            '</span> '.g4p_birth_death($indi_tmp->familles[$g4p_a_result['familles_id']]->events[$g4p_a_result['events_details_id']]->timestamp).'</dd>';
    }
}
echo '</dl>';

$g4p_query=$g4p_mysqli->g4p_query("SELECT notes_id, notes_text, notes_timestamp FROM genea_notes 
    ORDER BY notes_timestamp DESC LIMIT 3");
if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
{
    echo '<h3>Notes</h3>';
    echo '<dl>';
    foreach($g4p_result as $g4p_a_result)
    {
        echo '<dt>Note '.$g4p_a_result['notes_id'].g4p_birth_death($g4p_a_result['notes_timestamp']).' [détail]</dt>';
        echo '<dd>'.word_limiter($g4p_a_result['notes_text'],100).'</dd>';
    }
    echo '</dl>';
}

echo '<h3>Sources</h3>';

echo '<h3>Médias</h3>';

require_once($g4p_chemin.'pied_de_page.php');

?>
