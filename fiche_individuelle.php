<?php
 /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 **
 *Copyright (C) 2004PAROIS Pascal *
 **
 *This program is free software; you can redistribute it and/or modify*
 *it under the terms of the GNU General Public License as published by*
 *the Free Software Foundation; either version 2 of the License, or *
 *(at your option) any later version. *
 **
 *This program is distributed in the hope that it will be useful, *
 *but WITHOUT ANY WARRANTY; without even the implied warranty of*
 *MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the *
 *GNU General Public License for more details.*
 **
 *You should have received a copy of the GNU General Public License *
 *along with this program; if not, write to the Free Software *
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA02111-1307 USA *
 **
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *Page à tout faire*
 * *
 * dernière mise à jour : novembre 2004*
 * En cas de problème : http://www.parois.net*
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'p_conf/script_start.php');

//Chargement des données de la personne
$g4p_indi=g4p_load_indi_infos((int)$_REQUEST['id_pers']);
//var_dump($g4p_indi);

g4p_forbidden_access($g4p_indi);

//historique des fiches visitées
g4p_add_intohistoric($g4p_indi->indi_id,'indi');
$g4p_titre_page=$g4p_indi->prenom.' '.$g4p_indi->nom;

require_once($g4p_chemin.'entete.php');
echo '<div class="box_title"><h2>'.$g4p_indi->prenom,' ',$g4p_indi->nom.'</h2></div>'."\n";

//menu edition
if ($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
{
    echo '<div class="menu_interne">
        <a href="',g4p_make_url('','new_note.php','parent=INDI&amp;id_parent='.$g4p_indi->indi_id,0),'" class="admin">',$g4p_langue['menu_ajout_note'],'</a> -
        <a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_source&amp;g4p_id='.$g4p_indi->indi_id.'&amp;g4p_type=indi',0),'" class="admin">',$g4p_langue['menu_ajout_source'],'</a> -
        <a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_media&amp;g4p_id='.$g4p_indi->indi_id.'&amp;g4p_type=indi',0),'" class="admin">',$g4p_langue['menu_ajout_media'],'</a> -
        <a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_asso&amp;g4p_lien='.$g4p_indi->indi_id.'&amp;g4p_type=indi',0),'" class="admin">',$g4p_langue['menu_ajout_relation'],'</a> - ';
}
if($_SESSION['permission']->permission[_PERM_SUPPR_FILES_])
    echo '<a href="',g4p_make_url('admin','exec.php','g4p_opt=suppr_fiche',0),'" class="admin" onclick=" return confirme(this, \'',$g4p_langue['menu_sppr_confirme'],'\')">',$g4p_langue['menu_sppr_indi'],'</a> - ';
if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
{
    echo '<a href="',g4p_make_url('','modification_fiche.php','id_pers='.$g4p_indi->indi_id,0),'" class="admin">',$g4p_langue['menu_modif_fiche'],'</a> -';
    if($g4p_indi->resn!='privacy')
        echo ' <a href="',g4p_make_url('admin','exec.php','g4p_opt=mask_fich&amp;g4p_id='.$g4p_indi->indi_id,0),'" class="admin">',$g4p_langue['menu_masque_fiche'],'</a> -';
    else
        echo ' <a href="',g4p_make_url('admin','exec.php','g4p_opt=demask_fich&amp;g4p_id='.$g4p_indi->indi_id,0),'" class="admin">',$g4p_langue['menu_demasque_fiche'],'</a> -';
    echo ' <a href="',g4p_make_url('admin','exec.php','g4p_opt=del_cache&g4p_id='.$g4p_indi->indi_id,0),'" class="admin">',$g4p_langue['menu_recreer_cache'],'</a> <a href="g4p_object.php?id='.$g4p_indi->indi_id.'" target="_blank">G4p_object</a></div>';
}

        
echo '<div class="box">';
echo '<div class="box_title"><h3>État civil</h3></div>';        
if($g4p_indi->timestamp!='0000-00-00 00:00:00')
    echo '<span class="petit">',sprintf($g4p_langue['index_chan'],g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_indi->timestamp))),'</span><br />';
if($g4p_indi->resn=='privacy')
    echo'<span class="petit">',$g4p_langue['index_masquer'],'</span><br />';
echo '<dl class="etat_civil">';
echo '<dt>Id : </dt><dd>',number_format($g4p_indi->indi_id, 0, ',', ' '),'</dd>';
echo '<dt>',$g4p_langue['index_nom'],'</dt><dd>',$g4p_indi->nom,'</dd>';
echo '<dt>',$g4p_langue['index_prenom'],'</dt><dd>',$g4p_indi->prenom,'</dd>';
echo '<dt>',$g4p_langue['index_sexe'],'</dt><dd>',$g4p_langue['index_sexe_valeur'][$g4p_indi->sexe],'</dd>';
if($g4p_indi->npfx)
    echo '<dt>',$g4p_langue['index_npfx'],'</dt><dd>',$g4p_indi->npfx,'</dd>';
if($g4p_indi->givn)
    echo '<dt>',$g4p_langue['index_givn'],'</dt><dd>',$g4p_indi->givn,'</dd>';
if($g4p_indi->nick)
    echo '<dt>',$g4p_langue['index_nick'],'</dt><dd>',$g4p_indi->nick,'</dd>';
if($g4p_indi->spfx)
    echo '<dt>',$g4p_langue['index_spfx'],'</dt><dd>',$g4p_indi->spfx,'</dd>';
if($g4p_indi->nsfx)
    echo '<dt>',$g4p_langue['index_nsfx'],'</dt><dd>',$g4p_indi->nsfx,'</dd>';
if($g4p_indi->resn)
    echo '<dt>Resn : </dt><dd>',$g4p_indi->resn,'</dd>';
echo '</dl>';
echo '</div>';

if(!empty($g4p_indi->alias))
    g4p_show_alias($g4p_indi->alias);

//evenements individuels
/* 
AGE <AGE_AT_EVENT>
EVENT_DETAIL:=
n TYPE <EVENT_OR_FACT_CLASSIFICATION> {0:1} p.49
n DATE <DATE_VALUE> {0:1} p.47, 46
n <<PLACE_STRUCTURE>> {0:1} p.38
n <<ADDRESS_STRUCTURE>> {0:1} p.31
n AGNC <RESPONSIBLE_AGENCY> {0:1} p.60 [NOT SUPPORTED]
n RELI <RELIGIOUS_AFFILIATION> {0:1} p.60 [NOT SUPPORTED]
n CAUS <CAUSE_OF_EVENT> {0:1} p.43
n RESN <RESTRICTION_NOTICE> {0:1} p.60 [NOT SUPPORTED]
n <<NOTE_STRUCTURE>> {0:M} p.37
n <<SOURCE_CITATION>> {0:M} p.39
n <<MULTIMEDIA_LINK>> {0:M} p.37, 26
*/

if(!empty($g4p_indi->events))
{
    echo '<div class="box">';
    echo '<div class="box_title"><h3>Évènements</h3></div>';
                
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
    echo '</dl></div>';
}

if(!empty($g4p_indi->attributes))
{
    echo '<div class="box">';
    echo '<div class="box_title"><h3>Attributs</h3></div>';

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
    echo '</dl></div>';
}

g4p_affiche_mariage();

// les parents
//echo '<pre>'; print_r($g4p_indi->parents);
if(!empty($g4p_indi->parents))
{
    foreach($g4p_indi->parents as $g4p_a_parent)
    {
        echo '<div class="box">';
        if(empty($g4p_a_parent->rela_type))
            $g4p_a_parent->rela_type='BIRTH';
        echo '<div class="box_title"><h3>Parents '.str_replace(array_keys($g4p_lien_def),array_values($g4p_lien_def),$g4p_a_parent->rela_type). '</h3></div>';
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

if($g4p_config['show_ext_rela'])
    g4p_relations_avancees($g4p_indi->indi_id);

g4p_affiche_asso(@$g4p_indi->asso, $g4p_indi->indi_id,'indi');
g4p_affiche_event_temoins(@$g4p_indi->temoins['events']);

if ($_SESSION['permission']->permission[_PERM_NOTE_])
    g4p_affiche_notes(@$g4p_indi->notes, $g4p_indi->indi_id,'indi');

if ($_SESSION['permission']->permission[_PERM_SOURCE_])
    g4p_affiche_sources(@$g4p_indi->sources, $g4p_indi->indi_id,'indi');

if ($_SESSION['permission']->permission[_PERM_MULTIMEDIA_])
    g4p_affiche_multimedia(@$g4p_indi->multimedia, $g4p_indi->indi_id,'indi');

//  echo '</div>';
require_once($g4p_chemin.'pied_de_page.php');
?>
