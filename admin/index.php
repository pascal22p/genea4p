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
 *        Modifications des enregistrements , administration               *
 *                                                                         *
 * dernière mise à jour : 31/12/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'entete.php');


If(!isset($_GET['g4p_opt']))
  $_GET['g4p_opt']='';

switch($_GET['g4p_opt'])
{
/////////////////////////////////////////////////////////////:
/////////////////////////////////////////////////////////////:
    case 'mod_fich':
    $g4p_indi=g4p_load_indi_infos($_GET['id_pers']);
    if(empty($g4p_indi))
        g4p_error('Erreur lors du chargement des données de l\'individu');

    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_] and isset($g4p_indi) and $g4p_indi->indi_id!=0)
    {

        sprintf($g4p_langue['a_index_titre'],$g4p_indi->nom,' ',$g4p_indi->prenom);
        echo '<a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&amp;id_pers='.$g4p_indi->indi_id,'fiche-'.g4p_prepare_varurl($g4p_indi->nom).'-'.g4p_prepare_varurl($g4p_indi->prenom).'-'.$g4p_indi->indi_id),'">[Retour]</a>';
        echo '<div class="cadre">';
        echo '<form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=modif_fich',0),'" name="modif_fich"><div class="etat_civil">';
        echo '<em>',$g4p_langue['a_index_nom'],'</em> <input type="text" name="nom" size="20" value="',$g4p_indi->nom,'" /><br />';
        echo '<em>',$g4p_langue['a_index_prenom'],'</em> <input type="text" name="prenom" size="20" value="',$g4p_indi->prenom,'" /><br />';
        echo '<em>',$g4p_langue['a_index_sexe'],'</em> <select name="sexe" size="1">';
        foreach($g4p_langue['index_sexe_valeur'] as $val=>$desc)
            echo '<option value="',$val,'" ',($g4p_indi->sexe==$val)?'selected="selected"':'','>',$desc,'</option>';
        echo '</select><br />';
        echo '<br />';
        echo '<em>',$g4p_langue['a_index_npfx'],'</em> <input type="text" name="npfx" size="20" value="',$g4p_indi->npfx,'" /><br />';
        echo '<em>',$g4p_langue['a_index_givn'],'</em> <input type="text" name="givn" size="20" value="',$g4p_indi->givn,'" /><br />';
        echo '<em>',$g4p_langue['a_index_nick'],'</em> <input type="text" name="nick" size="20" value="',$g4p_indi->nick,'" /><br />';
        echo '<em>',$g4p_langue['a_index_spfx'],'</em> <input type="text" name="spfx" size="20" value="',$g4p_indi->npfx,'" /><br />';
        echo '<em>',$g4p_langue['a_index_nsfx'],'</em> <input type="text" name="nsfx" size="20" value="',$g4p_indi->nsfx,'" /></div>';
        echo '<input type="hidden" name="g4p_id" value="',$g4p_indi->indi_id,'" />
            <input type="submit" value="',$g4p_langue['submit_modifier'],'" />
            </form>';

        echo '<hr />';

//ALIAS
        if(!empty($g4p_indi->alias))
            g4p_show_alias($g4p_indi->alias,$admin=1);

//nouvel ALIAS
        echo '<form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=add_alia',0),'" name="modif_fich">';
        echo $g4p_langue['a_index_ajout_alias'];
        if(!empty($_SESSION['historic']['indi']))
        {
            echo '<select name="alia2" style="width:auto"><option value=""></option>';
            foreach($_SESSION['historic']['indi'] as $tmp)
            {
                $tmp=explode('||',$tmp);
                echo '<option value="'.$tmp[0].'">'.htmlentities($tmp[1],ENT_NOQUOTES,'UTF-8').'</option>';
            }
            echo '</select>',$g4p_langue['a_index_ajout_alias_ou'];
        }
        if(empty($_GET['alia']))
            $_GET['alia']='';
        echo'<input type="text" value="',$_GET['alia'],'" id="alia" name="alia" />
            <a href="',g4p_make_url('','recherche.php','type=indi_0&amp;g4p_referer='.rawurlencode('admin|index.php|'.$_SERVER['QUERY_STRING']).'&amp;g4p_champ=alia',0).'">',$g4p_langue['submit_rechercher'],'</a><br />';
        echo '<input type="hidden" name="g4p_id" value="',$g4p_indi->indi_id,'" />
            <input type="submit" value="',$g4p_langue['submit_ajouter'],'" />
            </form>';
            
        echo '<hr />';
            
    //evenements individuels
        echo '<div class="evenements">';
        if(isset($g4p_indi->evenements))
        {
            $g4p_indi->evenements=array_column_sort($g4p_indi->evenements,'type');
            foreach($g4p_indi->evenements as $g4p_a_ievents)
            {
                if($g4p_a_ievents->description)
                    $g4p_tmp=' ('.$g4p_a_ievents->description.')';
                else
                    $g4p_tmp='';
                echo '<em>', $g4p_tag_def[$g4p_a_ievents->type],$g4p_tmp,' : </em>',g4p_date($g4p_a_ievents->date), (!empty($g4p_a_ievents->lieu))?(' ('.$g4p_langue['index_lieu'].$g4p_a_ievents->lieu->toCompactString().') '):('');
                echo (isset($g4p_a_ievents->sources))?(' <span style="color:blue; font-size:x-small;">-S-</span> '):('');
                echo (isset($g4p_a_ievents->notes))?(' <span style="color:blue; font-size:x-small;">-N-</span> '):('');
                echo (isset($g4p_a_ievents->medias))?(' <span style="color:blue; font-size:x-small;">-M-</span> '):('');
                echo (isset($g4p_a_ievents->asso))?(' <span style="color:blue; font-size:x-small;">-T-</span> '):('');
                echo (isset($g4p_a_ievents->id))?('<a href="'.g4p_make_url('','detail_event.php','g4p_id=i|'.$g4p_indi->indi_id.'|'.$g4p_a_ievents->id,0).'" class="noprint">'.$g4p_langue['detail'].'</a><br />'):('<br />');
            }
        }
        echo '<a class="admin" href="',g4p_make_url('admin','index.php','g4p_opt=ajout_event&amp;g4p_id=i|'.$g4p_indi->indi_id),'">',$g4p_langue['a_index_ajout_ievent'],'</a>';
        echo '</div>';

        g4p_affiche_mariage();

    // les parents
        if(isset($g4p_indi->parents))
        {
            echo '<div class="parents">';
            foreach($g4p_indi->parents as $g4p_a_parent)
            {
                echo '<em>',$g4p_langue['index_ype_parent'],'</em>',str_replace(array_keys($g4p_lien_def),array_values($g4p_lien_def),$g4p_a_parent['rela_type']);
                if(isset($g4p_a_parent['pere']))
                {
                    echo '<br /><a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&amp;g4p_base='.$g4p_a_parent['pere']->indi_base.'&amp;id_pers='.$g4p_a_parent['pere']->indi_id,'fiche-'.$g4p_a_parent['pere']->indi_base.'-'.g4p_prepare_varurl($g4p_a_parent['pere']->nom).'-'.g4p_prepare_varurl($g4p_a_parent['pere']->prenom).'-'.$g4p_a_parent['pere']->indi_id),'">',
                    $g4p_a_parent['pere']->nom,' ',$g4p_a_parent['pere']->prenom,'</a>',g4p_date($g4p_a_parent['pere']->date_rapide);
                }
                else
                    echo '<br />',$g4p_langue['index_parent_inconnu'];

                if(isset($g4p_a_parent['mere']))
                {
                    echo '<br /><a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&amp;g4p_base='.$g4p_a_parent['mere']->indi_base.'&amp;id_pers='.$g4p_a_parent['mere']->indi_id,'fiche-'.$g4p_a_parent['mere']->indi_base.'-'.g4p_prepare_varurl($g4p_a_parent['mere']->nom).'-'.g4p_prepare_varurl($g4p_a_parent['mere']->prenom).'-'.$g4p_a_parent['mere']->indi_id),'">',
                    $g4p_a_parent['mere']->nom,' ',$g4p_a_parent['mere']->prenom,'</a>',g4p_date($g4p_a_parent['mere']->date_rapide);
                }
                else
                    echo '<br />',$g4p_langue['index_parent_inconnu'];
            }
            echo '</div>';
        }

        g4p_affiche_asso(@$g4p_indi->asso, $g4p_indi->indi_id,'indi');

        if ($_SESSION['permission']->permission[_PERM_NOTE_])
            g4p_affiche_notes(@$g4p_indi->notes,$g4p_indi->indi_id, 'indi');
        
        if ($_SESSION['permission']->permission[_PERM_SOURCE_])
            g4p_affiche_sources(@$g4p_indi->sources,$g4p_indi->indi_id, 'indi');

        if ($_SESSION['permission']->permission[_PERM_MULTIMEDIA_])
            g4p_affiche_multimedia(@$g4p_indi->multimedia, $g4p_indi->indi_id, 'indi');

        echo '</div>';
    }
    break;

////////////////////////////////////
//////  modification de la famille
////////////////////////////////////
  case 'mod_fams':

// Le mariage
  if ($_SESSION['permission']->permission[_PERM_EDIT_FILES_] and isset($g4p_indi->familles[$_GET['g4p_id']]))
  {
    echo '<h2>',sprintf(
$g4p_langue['a_index_modfams_titre'],$g4p_indi->nom,@$g4p_indi->familles[$_GET['g4p_id']]->conjoint->nom),'</h2>';

    echo '<a href="',g4p_make_url('','index.php','g4p_action=fiche_indi','fiche-'.g4p_prepare_varurl($g4p_indi->nom).'-'.g4p_prepare_varurl($g4p_indi->prenom).'-'.$g4p_indi->indi_id),'" class="retour">',$g4p_langue['retour'],'</a><div class="cadre">
      <form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=modif_fams',0),'" id="modif_fams" name="modif_fams">';

    if(!isset($_GET['mari']))
      $_GET['mari']='';
    if(!isset($_GET['femme']))
      $_GET['femme']='';

    if($g4p_indi->sexe=='M')
    {
      echo '<em>',$g4p_langue['a_index_modfams_conjoint1'],'</em><a href="',g4p_make_url('','index.php','g4p_action=fiche_indi','fiche-'.g4p_prepare_varurl($g4p_indi->nom).'-'.g4p_prepare_varurl($g4p_indi->prenom).'-'.$g4p_indi->indi_id),'">',
      $g4p_indi->nom,' ', $g4p_indi->prenom,'</a><br />';
      echo $g4p_langue['a_index_modfams_conjointid'];
      if(!empty($_SESSION['historic']['indi']))
      {
        echo '<select name="mari2" style="width:auto"><option value=""></option>';
        foreach($_SESSION['historic']['indi'] as $tmp)
        {
          $tmp=explode('||',$tmp);
          echo '<option value="'.$tmp[0].'">'.htmlentities($tmp[1],ENT_NOQUOTES,'UTF-8').'</option>';
        }
        echo '</select>',$g4p_langue['a_index_modfams_ou'];
      }
      echo'<input type="text" value="',$_GET['mari'],'" id="mari" name="mari" />
      <a href="',g4p_make_url('','recherche.php','type=indi_0&amp;g4p_referer='.rawurlencode('admin|index.php|'.$_SERVER['QUERY_STRING']).'&amp;g4p_champ=mari',0).'">',$g4p_langue['a_index_modfams_conjoint_cherche'],'</a><br />
      
      <em>',$g4p_langue['a_index_modfams_conjoint2'],'</em><a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&amp;id_pers='.$g4p_indi->familles[$_GET['g4p_id']]->conjoint->indi_id,0),'">',
      @$g4p_indi->familles[$_GET['g4p_id']]->conjoint->nom,' ',@$g4p_indi->familles[$_GET['g4p_id']]->conjoint->prenom,'</a><br />';
      echo $g4p_langue['a_index_modfams_conjointid'];
      if(!empty($_SESSION['historic']['indi']))
      {
        echo '<select name="femme2" style="width:auto"><option value=""></option>';
        foreach($_SESSION['historic']['indi'] as $tmp)
        {
          $tmp=explode('||',$tmp);
          echo '<option value="'.$tmp[0].'">'.htmlentities($tmp[1],ENT_NOQUOTES,'UTF-8').'</option>';
        }
        echo '</select>',$g4p_langue['a_index_modfams_ou'];
      }      
      echo '<input type="text" value="',$_GET['femme'],'" id="femme" name="femme" />
      <a href="',g4p_make_url('','recherche.php','type=indi_0&amp;g4p_referer='.rawurlencode('admin|index.php|'.$_SERVER['QUERY_STRING']).'&amp;g4p_champ=femme',0).'">',$g4p_langue['a_index_modfams_conjoint_cherche'],'</a><br />';
      
      echo '<input name="g4p_id" type="hidden" value="',$_GET['g4p_id'],'" /><input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form><br />';
    }
    else
    {
      echo '<em>',$g4p_langue['a_index_modfams_conjoint1'],'</em><a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&amp;id_pers='.$g4p_indi->familles[$_GET['g4p_id']]->conjoint->indi_id,0),'">',
      @$g4p_indi->familles[$_GET['g4p_id']]->conjoint->nom,' ',@$g4p_indi->familles[$_GET['g4p_id']]->conjoint->prenom,'</a><br />';
      echo $g4p_langue['a_index_modfams_conjointid'];
      if(!empty($_SESSION['historic']['indi']))
      {
        echo '<select name="mari2" style="width:auto"><option value=""></option>';
        foreach($_SESSION['historic']['indi'] as $tmp)
        {
          $tmp=explode('||',$tmp);
          echo '<option value="'.$tmp[0].'">'.htmlentities($tmp[1],ENT_NOQUOTES,'UTF-8').'</option>';
        }
        echo '</select>',$g4p_langue['a_index_modfams_ou'];
      }      
      echo '<input type="text" value="',$_GET['mari'],'" id="mari" name="mari" />
      <a href="',g4p_make_url('','recherche.php','type=indi_0&amp;g4p_referer='.rawurlencode('admin|index.php|'.$_SERVER['QUERY_STRING']).'&amp;g4p_champ=mari',0).'">',$g4p_langue['a_index_modfams_conjoint_cherche'],'</a><br />';
      echo '<em>',$g4p_langue['a_index_modfams_conjoint2'],'</em><a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&amp;id_pers='.$g4p_indi->indi_id,0),'">',
      $g4p_indi->nom,' ', $g4p_indi->prenom,'</a><br />';
      echo $g4p_langue['a_index_modfams_conjointid'];
      if(!empty($_SESSION['historic']['indi']))
      {
        echo '<select name="femme2" style="width:auto"><option value=""></option>';
        foreach($_SESSION['historic']['indi'] as $tmp)
        {
          $tmp=explode('||',$tmp);
          echo '<option value="'.$tmp[0].'">'.htmlentities($tmp[1],ENT_NOQUOTES,'UTF-8').'</option>';
        }
        echo '</select>',$g4p_langue['a_index_modfams_ou'];
      }      
      echo '<input type="text" value="',$_GET['femme'],'" id="femme" name="femme" />
      <a href="',g4p_make_url('','recherche.php','type=indi_0&amp;g4p_referer='.rawurlencode('admin|index.php|'.$_SERVER['QUERY_STRING']).'&amp;g4p_champ=femme',0).'">',$g4p_langue['a_index_modfams_conjoint_cherche'],'</a><br />';
      echo '<input name="g4p_id" type="hidden" value="',$_GET['g4p_id'],'" /><input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form><br />';
    }

// les evènements familiaux
    if(isset($g4p_indi->familles[$_GET['g4p_id']]->evenements))
    {
      echo '<div id="evenements">';
      foreach($g4p_indi->familles[$_GET['g4p_id']]->evenements as $g4p_a_event)
      {
        if($g4p_a_event->description)
          $g4p_tmp=' ('.$g4p_a_event->description.')';
        else
          $g4p_tmp='';
        echo '<em>',$g4p_tag_def[$g4p_a_event->type],$g4p_tmp,' :</em> ';
        echo g4p_date($g4p_a_event->date);
        echo (!empty($g4p_a_event->lieu))?(' (lieu : '.$g4p_a_event->lieu->toCompactString().') '):('');
        echo (isset($g4p_a_event->sources))?('  <span style="color:blue; font-size:x-small;">-S-</span>  '):('');
        echo (isset($g4p_a_event->notes))?('  <span style="color:blue; font-size:x-small;">-N-</span>  '):('');
        echo (isset($g4p_a_event->medias))?('  <span style="color:blue; font-size:x-small;">-M-</span>  '):('');
        echo (isset($g4p_a_event->assos))?('  <span style="color:blue; font-size:x-small;">-T-</span>  '):('');
        echo '<a href="',g4p_make_url('','detail_event.php','g4p_id=f|'.$_GET['g4p_id'].'|'.$g4p_a_event->id,0),'" class="noprint">',$g4p_langue['detail'],'</a><br />';
      }
      echo '</div>';
    }
    echo '<a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_event&amp;g4p_id=f|'.$_GET['g4p_id'],0).'" class="admin">',$g4p_langue['a_index_ajout_fevent'],'</a><br /><br />';


//les enfants des mariages
    // Extrait les enfants des familles
    if(isset($g4p_indi->familles[$_GET['g4p_id']]->enfants))
    {
      echo '<em>',$g4p_langue['a_index_modfams_enfants'],'</em><br />';
      foreach($g4p_indi->familles[$_GET['g4p_id']]->enfants as $g4p_a_enfants)
        echo '&nbsp;&nbsp;&nbsp;<a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&amp;id_pers='.$g4p_a_enfants->indi_id,0),'">',$g4p_a_enfants->nom,' ',$g4p_a_enfants->prenom,
        '</a>',$g4p_a_enfants->date_rapide,
        ' <a href="',g4p_make_url('admin','exec.php','g4p_opt=suppr_child&amp;fam_id='.$_GET['g4p_id'].'&amp;g4p_id='.$g4p_a_enfants->indi_id,0),'" class="admin"
            onclick=" return confirme(this, \'',$g4p_langue['a_index_modfams_confirme_suppr_js'],'\')">',$g4p_langue['Supprimer'],'</a>
          <br />';
    }

    if(!isset($_GET['enfant']))
      $_GET['enfant']='';

    echo '<br /><form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=new_child',0),'" name="new_child">',$g4p_langue['a_index_modfams_newchild'];
    if(!empty($_SESSION['historic']['indi']))
    {
        echo '<select name="enfant2" style="width:auto"><option value=""></option>';
        foreach($_SESSION['historic']['indi'] as $tmp)
        {
          $tmp=explode('||',$tmp);
          echo '<option value="'.$tmp[0].'">'.htmlentities($tmp[1],ENT_NOQUOTES,'UTF-8').'</option>';
        }
        echo '</select> ou ';
    }    
    echo '<input type="text" value="',$_GET['enfant'],'" id="enfant" name="enfant" />
    <a href="',g4p_make_url('','recherche.php','type=indi_0&amp;g4p_referer='.rawurlencode('admin|index.php|'.$_SERVER['QUERY_STRING']).'&amp;g4p_champ=enfant',0).'">',$g4p_langue['a_index_modfams_enfant_recherche'],'</a><br />
    <br />',$g4p_langue['a_index_modfams_enfant_relation'],'<select name="rela_type">';
    foreach($g4p_lien_def as $key=>$value)
      echo '<option value="',$key,'">',$value,'</option>';
    echo '</select><br /><br />
    <input type="hidden" value="',$_GET['g4p_id'],'" name="fam_id" /><input type="submit" value="',$g4p_langue['submit_ajouter'],'" /></form><br /></div>';
  }
  else
    echo '<br /><div id="mariage"><em>',$g4p_langue['a_index_modfams_aucune_famille'],'</em></div>';

  if(isset($g4p_indi->familles))
  {
    if ($_SESSION['permission']->permission[_PERM_NOTE_])
	  g4p_affiche_notes(@$g4p_indi->familles[$_GET['g4p_id']]->notes, $g4p_indi->familles[$_GET['g4p_id']]->id,'familles');
	
	if ($_SESSION['permission']->permission[_PERM_SOURCE_])
	  g4p_affiche_sources(@$g4p_indi->familles[$_GET['g4p_id']]->sources, $g4p_indi->familles[$_GET['g4p_id']]->id, 'familles');
	
	if ($_SESSION['permission']->permission[_PERM_MULTIMEDIA_])
	  g4p_affiche_multimedia(@$g4p_indi->familles[$_GET['g4p_id']]->multimedia, $g4p_indi->familles[$_GET['g4p_id']]->id, 'familles');
  }
  break;

////////////////////////////////////
//////  ajout d'une notes
////////////////////////////////////
  case 'ajout_note':
  if(!isset($_GET['g4p_id_note']))
    $_SESSION['formulaire']['g4p_id_note']='';
  else
    $_SESSION['formulaire']['g4p_id_note']=$_GET['g4p_id_note'];
  echo '<h2>',$g4p_langue['a_index_ajout_note_titre'],'</h2><div class="cadre">
      <em>',$g4p_langue['a_index_ajout_note_lier'],'</em><br />
    <form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=link_note',0),'" name="link_note">',$g4p_langue['a_index_ajout_note_id'],'<input name="g4p_id_note" type="text" value="',$_SESSION['formulaire']['g4p_id_note'],'" />
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
  break;

////////////////////////////////////
//////  modification des notes
////////////////////////////////////
  case 'mod_note':
    $sql="SELECT notes_text FROM genea_notes WHERE notes_id=".$_GET['g4p_id'];
    $g4p_result_req=$g4p_mysqli->g4p_query($sql);
    if($g4p_notes=$g4p_mysqli->g4p_result($g4p_result_req))
    {
      echo '<h2>',$g4p_langue['a_index_mod_note_titre'],'</h2>';
      echo '<div class="cadre"><em>',$g4p_langue['a_index_mod_note_inclu'],'</em><br />';
      $sql="SELECT genea_individuals.indi_id, indi_nom, indi_prenom FROM rel_indi_notes
        LEFT JOIN genea_individuals USING(indi_id)
        WHERE notes_id=".$_GET['g4p_id'];
      if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
      {
        if($g4p_ref=$g4p_mysqli->g4p_result($g4p_result_req))
        {
          echo $g4p_langue['a_index_mod_note_liste_indi'],'<ul>';
          foreach($g4p_ref as $g4p_a_ref)
            echo '<li>',$g4p_a_ref['indi_nom'],' ',$g4p_a_ref['indi_prenom'],'</li>';
          echo '</ul>';
        }
      }

      $sql="SELECT husb.indi_nom as husb_nom, husb.indi_prenom as husb_prenom, wife.indi_nom as wife_nom, wife.indi_prenom as wife_prenom FROM rel_familles_notes
        LEFT JOIN genea_familles USING (familles_id)
        LEFT JOIN genea_individuals AS husb ON(genea_familles.familles_husb=husb.indi_id)
        LEFT JOIN genea_individuals AS wife ON(genea_familles.familles_wife=wife.indi_id)
        WHERE notes_id=".$_GET['g4p_id'];
      if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
      {
        if($g4p_ref=$g4p_mysqli->g4p_result($g4p_result_req))
        {
          echo $g4p_langue['a_index_mod_note_liste_famille'],'<ul>';
          foreach($g4p_ref as $g4p_a_ref)
            echo '<li>',$g4p_a_ref['husb_nom'],' ',$g4p_a_ref['husb_prenom'],' - ',$g4p_a_ref['wife_nom'],' ',$g4p_a_ref['wife_prenom'],'</li>';
          echo '</ul>';
        }
      }

      $sql="SELECT type, date_event, place_ville, ";
      $sql.="genea_place.place_id as place_id
        FROM rel_events_notes
        LEFT JOIN genea_events ON (events_id=id)
        LEFT JOIN genea_place ON ( genea_place.place_id=genea_events.place_id )
        WHERE notes_id=".$_GET['g4p_id'];
      if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
      {
        if($g4p_ref=$g4p_mysqli->g4p_result($g4p_result_req))
        {
          echo $g4p_langue['a_index_mod_note_liste_event'],'<ul>';
          foreach($g4p_ref as $g4p_a_ref)
          {
            echo '<li>',$g4p_tag_def[$g4p_a_ref['type']],' ',g4p_date($g4p_a_ref['date_event']),' - ',$g4p_a_ref['place_ville'],'</li>';
          }
          echo '</ul>';
        }
      }
      echo '</div>';

      echo '<div class="cadre"><form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=modif_note',0),'" name="modif_note">';
      echo '<textarea rows="8" cols="80" name="g4p_note" >'.$g4p_notes[0]['notes_text'].'</textarea><br />';
      echo '<input name="g4p_id" type="hidden" value="',$_GET['g4p_id'],'" />
        <input name="g4p_lien" type="hidden" value="',$_GET['g4p_lien'],'" />
        <input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form></div>';
    }
    else
    {
     $_SESSION['message']=$g4p_langue['a_index_mod_note_erreur'];
     $g4p_indi=g4p_destroy_cache();
      header('location:'.g4p_make_url('','index.php','id_pers='.$g4p_indi->indi_id,'fiche-'.g4p_prepare_var_url($g4p_indi->indi_nom).'-'.g4p_prepare_var_url($g4p_indi->indi_prenom).'-'.$g4p_indi->indi_id));
    }
  break;

////////////////////////////////////
//////  Ajout d'une source
////////////////////////////////////
  case 'ajout_source':
    if(!isset($_GET['g4p_id_source']))
      $_SESSION['formulaire']['g4p_id_source']='';
    else
      $_SESSION['formulaire']['g4p_id_source']=$_GET['g4p_id_source'];
    echo '<h2>',$g4p_langue['a_index_ajout_source_titre'],'</h2>';
    echo '<div class="cadre">
      <em>',$g4p_langue['a_index_ajout_source_lier'],'<br /></em>
      <form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=link_source',0),'" name="link_source">'.$g4p_langue['a_index_ajout_source_id'].'<input name="g4p_id_source" type="text" value="',$_SESSION['formulaire']['g4p_id_source'],'" />
      <input name="g4p_id" type="hidden" value="',$_GET['g4p_id'],'" />
      <input name="g4p_type" type="hidden" value="',$_GET['g4p_type'],'" />
      <input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form>
      <a href="',g4p_make_url('','recherche.php','type=sour_0&amp;g4p_referer='.
        rawurlencode('admin|index.php|'.$_SERVER['QUERY_STRING']).'&amp;g4p_champ=g4p_id_source',0).'">'.
        $g4p_langue['a_index_ajout_source_recherche'].'</a>
      <hr /><br />
      <em>',$g4p_langue['a_index_ajout_source_ajout'],'</em>';
      echo '<form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=ajout_source',0),'" name="ajout_source">
      <em>',$g4p_langue['a_index_ajout_source_titre2'],'</em><input name="g4p_titre" type="text" size="40" /><br />
      <em>',$g4p_langue['a_index_ajout_source_auteur'],'</em><input name="g4p_auteur" type="text" size="40" /><br />
      <em>',$g4p_langue['a_index_ajout_source_page'],'</em><input name="g4p_page" type="text" size="40" /><br />
      <em>',$g4p_langue['a_index_ajout_source_ref'],'</em><input name="g4p_caln" type="text" size="40" /><br />
      <em>',$g4p_langue['a_index_ajout_source_type'],'</em><select name="g4p_medi" size="1">';
      foreach($g4p_source_media_type as $g4p_medi_cle=>$g4p_medi_value)
        echo '<option value="',$g4p_medi_cle,'">',$g4p_medi_value,'</option>';
      echo '</select>
      <br /><em>',$g4p_langue['a_index_ajout_source_texte'],'</em><br /><textarea rows="8" cols="80" name="g4p_text" ></textarea><br />
      <em>',$g4p_langue['a_index_ajout_source_publ'],'</em><br /><textarea rows="8" cols="80" name="g4p_publ" ></textarea><br />
      <em>',$g4p_langue['a_index_ajout_source_depot'],'</em><select name="g4p_repo" size="1"><option value="NULL" >&nbsp;</option>';
      $sql="SELECT repo_name, repo_id FROM genea_repository WHERE base=".$_SESSION['genea_db_id']." ORDER BY repo_name";
      $g4p_result_req=$g4p_mysqli->g4p_query($sql);
      $g4p_repo=$g4p_mysqli->g4p_result($g4p_result_req);
      foreach($g4p_repo as $g4p_repo)
        echo '<option value="',$g4p_repo['repo_id'],'">',$g4p_repo['repo_name'],'</option>';
      echo '</select>
      <a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_repo&amp;g4p_referer='.rawurlencode('admin|index.php|'.$_SERVER['QUERY_STRING']),0).'">',$g4p_langue['a_index_ajout_source_nouveau_depor'],'</a><br />
      <input name="g4p_id" type="hidden" value="',$_GET['g4p_id'],'" />
      <input name="g4p_type" type="hidden" value="',$_GET['g4p_type'],'" />
      <input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form></div>';
  break;

////////////////////////////////////
//////  modification d'une source
////////////////////////////////////
  case 'mod_source':
    $sql="SELECT sources_id, sources_title, sources_publ, sources_text, sources_auth, sources_caln, sources_page,
                 sources_medi, sources_chan, repo_id
          FROM genea_sources
          WHERE sources_id='".$_GET['g4p_id']."'";
    $g4p_result_req=$g4p_mysqli->g4p_query($sql);
    $g4p_source=$g4p_mysqli->g4p_result($g4p_result_req);
    $g4p_source=$g4p_source[0];

    if(!isset($_GET['g4p_id_media']))
      $_SESSION['formulaire']['g4p_id_media']='';
    else
      $_SESSION['formulaire']['g4p_id_media']=$_GET['g4p_id_media'];

    echo '<h2>',$g4p_langue['a_index_mod_source_titre'],'</h2>';

    echo '<div class="cadre"><em>',$g4p_langue['a_index_mod_source_inclu'],'</em><br />';
    $sql="SELECT genea_individuals.indi_id, indi_nom, indi_prenom FROM rel_indi_sources
      LEFT JOIN genea_individuals USING(indi_id)
      WHERE sources_id=".$_GET['g4p_id'];
    if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
    {
      if($g4p_ref=$g4p_mysqli->g4p_result($g4p_result_req))
      {
        echo $g4p_langue['a_index_mod_note_liste_indi'],'<ul>';
        foreach($g4p_ref as $g4p_a_ref)
          echo '<li>',$g4p_a_ref['indi_nom'],' ',$g4p_a_ref['indi_prenom'],'</li>';
        echo '</ul>';
      }
    }

    $sql="SELECT husb.indi_nom as husb_nom, husb.indi_prenom as husb_prenom, wife.indi_nom as wife_nom, wife.indi_prenom as wife_prenom FROM rel_familles_sources
      LEFT JOIN genea_familles USING (familles_id)
      LEFT JOIN genea_individuals AS husb ON(genea_familles.familles_husb=husb.indi_id)
      LEFT JOIN genea_individuals AS wife ON(genea_familles.familles_wife=wife.indi_id)
      WHERE sources_id=".$_GET['g4p_id'];
    if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
    {
      if($g4p_ref=$g4p_mysqli->g4p_result($g4p_result_req))
      {
        echo $g4p_langue['a_index_mod_note_liste_famille'],'<ul>';
        foreach($g4p_ref as $g4p_a_ref)
          echo '<li>',$g4p_a_ref['husb_nom'],' ',$g4p_a_ref['husb_prenom'],' - ',$g4p_a_ref['wife_nom'],' ',$g4p_a_ref['wife_prenom'],'</li>';
        echo '</ul>';
      }
    }

    $sql="SELECT type, date_event, place_ville, ";
    $sql.="genea_place.place_id as place_id
      FROM rel_events_sources
      LEFT JOIN genea_events ON (events_id=id)
      LEFT JOIN genea_place ON ( genea_place.place_id=genea_events.place_id )
      WHERE sources_id=".$_GET['g4p_id'];
    if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
    {
      if($g4p_ref=$g4p_mysqli->g4p_result($g4p_result_req))
      {
        echo $g4p_langue['a_index_mod_note_liste_event'],'<ul>';
        foreach($g4p_ref as $g4p_a_ref)
        {
          echo '<li>',$g4p_tag_def[$g4p_a_ref['type']],' ',g4p_date($g4p_a_ref['date_event']),' - ',$g4p_a_ref['place_ville'],'</li>';
        }
        echo '</ul>';
      }
    }
    echo '</div>';


      echo '<div class="cadre">';
      echo '<form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=mod_source',0),'" name="ajout_source">
      <em>',$g4p_langue['a_index_ajout_source_titre2'],'</em><input name="g4p_titre" type="text" size="40" value="',$g4p_source['sources_title'],'" /><br />
      <em>',$g4p_langue['a_index_ajout_source_auteur'],'</em><input name="g4p_auteur" type="text" size="40" value="',$g4p_source['sources_auth'],'" /><br />
      <em>',$g4p_langue['a_index_ajout_source_page'],'</em><input name="g4p_page" type="text" size="40" value="',$g4p_source['sources_page'],'" /><br />
      <em>',$g4p_langue['a_index_ajout_source_ref'],'</em><input name="g4p_caln" type="text" size="40" value="',$g4p_source['sources_caln'],'" /><br />
      <em>',$g4p_langue['a_index_ajout_source_type'],'</em><select name="g4p_medi" size="1">';
      foreach($g4p_source_media_type as $g4p_medi_cle=>$g4p_medi_value)
      {
        if($g4p_medi_cle==$g4p_source['sources_medi'])
          $g4p_selected='selected="selected"';
        else
          $g4p_selected='';
        echo '<option value="',$g4p_medi_cle,'" ',$g4p_selected,'>',$g4p_medi_value,'</option>';
      }
      echo '</select>
      <br /><em>',$g4p_langue['a_index_ajout_source_texte'],'</em><br /><textarea rows="8" cols="80" name="g4p_text" >',$g4p_source['sources_text'],'</textarea><br />
      <em>',$g4p_langue['a_index_ajout_source_publ'],'</em><br /><textarea rows="8" cols="80" name="g4p_publ" >',$g4p_source['sources_publ'],'</textarea><br />
      <em>',$g4p_langue['a_index_ajout_source_depot'],'</em><select name="g4p_repo_id" size="1"><option value="NULL" >&nbsp;</option>';
      $sql="SELECT repo_name, repo_id FROM genea_repository WHERE base=".$_SESSION['genea_db_id']." ORDER BY repo_name";
      $g4p_result_req=$g4p_mysqli->g4p_query($sql);
      $g4p_repo=$g4p_mysqli->g4p_result($g4p_result_req);
      foreach($g4p_repo as $g4p_repo)
      {
        if($g4p_repo['repo_id']==$g4p_source['repo_id'])
          $g4p_selected='selected="selected"';
        else
          $g4p_selected='';
        echo '<option value="',$g4p_repo['repo_id'],'" ',$g4p_selected,'>',$g4p_repo['repo_name'],'</option>';
      }
      echo '</select>
      <input name="g4p_id" type="hidden" value="',$_GET['g4p_id'],'" />
      <input name="g4p_type" type="hidden" value="',$_GET['g4p_type'],'" /><br />
      <input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form><hr /><br />';

    if(!isset($_GET['g4p_id_media']))
      $_SESSION['formulaire']['g4p_id_media']='';
    else
      $_SESSION['formulaire']['g4p_id_media']=$_GET['g4p_id_media'];
      echo '<h2>',$g4p_langue['a_index_ajout_media_titre'],'</h2>
        <em>',$g4p_langue['a_index_ajout_media_lier'],'<br /></em>
      <form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=link_media',0),'" name="link_media">',$g4p_langue['a_index_ajout_media_id'],'<input name="g4p_id_media" type="text" value="',$_SESSION['formulaire']['g4p_id_media'],'" />
      <input name="g4p_id" type="hidden" value="',$_GET['g4p_id'],'" />
      <input name="g4p_type" type="hidden" value="sources" />
      <input type="submit" value="',$g4p_langue['submit_ajouter'],'" /></form>
      <a href="',g4p_make_url('','recherche.php','type=obje_0&amp;g4p_referer='.rawurlencode('admin|index.php|'.$_SERVER['QUERY_STRING']).'&amp;g4p_champ=g4p_id_media',0).'">',$g4p_langue['a_index_ajout_media_recherche'],'</a>
      <br /><br /><em>',$g4p_langue['a_index_ajout_media_nouveau_media'],'</em>
       <form class="formulaire" method="post" enctype="multipart/form-data" action="',g4p_make_url('admin','exec.php','g4p_opt=ajout_media',0),'" name="ajout_media">
       ',$g4p_langue['a_index_ajout_media_titre2'],'<input type="text" name="titre" value="" size="40" /><br />
       ',$g4p_langue['a_index_ajout_media_file'],'<input type="file" name="fichier" /><br />
       ',$g4p_langue['a_index_ajout_media_url'],'<input type="text" name="url" value="http://" size="40" /><br />
        <input name="g4p_id" type="hidden" value="',$_GET['g4p_id'],'" />
        <input name="g4p_type" type="hidden" value="sources" />
        <input type="submit" value="',$g4p_langue['submit_ajouter'],'" /></form></div>';
 break;


////////////////////////////////////
//////  modification d'un évènement individuel ou familial ou un attribut
////////////////////////////////////
  case 'mod_event':
  if(strstr($_SERVER['HTTP_REFERER'],'detail_event'))
    $_SESSION['historic']['url'][0]=$_SERVER['HTTP_REFERER'];

  $sql="SELECT type, description, attestation, date_event, place_id, age, cause
        FROM genea_events WHERE id=".$_GET['g4p_id'];
  $g4p_result_req=$g4p_mysqli->g4p_query($sql);
  $g4p_events=$g4p_mysqli->g4p_result($g4p_result_req);
  $g4p_events=$g4p_events[0];
  
  $g4p_date=new g4p_date_read($g4p_events['date_event']);
  $g4p_date->date_value();

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
  break;

////////////////////////////////////
//////  ajout d'un évènement 
////////////////////////////////////
  case 'ajout_event':

  $g4p_id=explode('|',$_GET['g4p_id']);


  if(!isset($_GET['g4p_type_event']))
  {
	echo '<div class="menu_interne"><a href="'.g4p_make_url('admin','index.php','',0).'" class="retour">'.$g4p_langue['retour'].'</a></div>';
	echo '<div class="cadre"><h2>',$g4p_langue['a_index_ajout_event_titre'],'</h2>';
    echo '<form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=ajout_event',0),'" name="mod_event">';
    if($g4p_id[0]=='i')
    {
      $g4p_select='ievent';
    }
    else
    {
      $g4p_select='fevent';
    }
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
    echo '<li ><a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_event'.$g4p_type_date_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_event=ievent',0),'" >Evènement</a></li>';
    echo '<li class="active"><a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_event'.$g4p_type_date_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_event=iattribut',0),'" >Attribut</a></li>';
    echo '</ul>';
      
    echo '<div class="boite_tabulation">';
  
    echo $g4p_langue['a_index_mod_event_type'],'<select name="g4p_type">';
    echo '<option value="">Choisissez</option>';
    reset($g4p_tag_iattributs);
    while(list($g4p_key, $g4p_value)=each($g4p_tag_iattributs))
    {
      $g4p_selected=($g4p_events['type']==$g4p_key)?('selected="SELECTED"'):('');
      echo '<option value="',$g4p_key,'" '.$g4p_selected.'>',$g4p_value,'</option>';
    }
    echo '</select>';
    echo ' Description <input type="text" name="g4p_description" value="',@$g4p_events['description'],'" /><br />';
    
    g4p_formulaire_date_event('','ajout_event');
    echo '</div>';
  }
  elseif($g4p_select=='ievent')//évènement individuel
  {
    echo '<ul class="tabnav">';
    echo '<li class="active"><a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_event'.$g4p_type_date_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_event=ievent',0),'" >Evènement</a></li>';
    echo '<li ><a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_event'.$g4p_type_date_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_event=iattribut',0),'" >Attribut</a></li>';
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
    echo ' Attestation de l\'évènement <input type="checkbox" name="g4p_attestation" style="vertical-align:middle" /><br />';
    
    g4p_formulaire_date_event('','ajout_event');
    echo '</div>';
  }
  elseif($g4p_select=='fevent')//évènement familiale
  {
    echo '<ul class="tabnav">';
    echo '<li class="active"><a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_event'.$g4p_type_date_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_event=fevent',0),'" >Evènement</a></li>';
    echo '</ul>';
      
    //date gedcom
    echo '<div class="boite_tabulation">';
  
    echo $g4p_langue['a_index_mod_event_type'],'<select name="g4p_tag">';
    echo '<option value="">Choisissez</option>';
    reset($g4p_tag_fevents);
    while(list($g4p_key, $g4p_value)=each($g4p_tag_fevents))
    {
      $g4p_selected=($g4p_events['type']==$g4p_key)?('selected="SELECTED"'):('');
      echo '<option value="',$g4p_key,'" '.$g4p_selected.'>',$g4p_value,'</option>';
    }
    echo '</select>';
    echo ' Attestation de l\'évènement <input type="checkbox" name="g4p_attestation" style="vertical-align:middle" /><br />';
    echo ' EVENT_OR_FACT_CLASSIFICATION <input type="checkbox" name="g4p_type" style="vertical-align:middle" /><br />';
    
    g4p_formulaire_date_event('','ajout_event');
    echo '</div>';
  }
  else
    echo 'Erreur lors de la modif d\un évènement';
  
  echo '<br />';
  echo $g4p_langue['a_index_mod_event_lieu'];
  echo '<select name="g4p_lieu" style="width:auto"><option value=""></option>';
  $sql="SELECT place_id, place_ville, place_lieudit, place_departement, place_region, place_pays FROM genea_place WHERE base=".$_SESSION['genea_db_id']." ORDER BY place_ville";
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
  echo '<input name="g4p_id" type="hidden" value="',$_GET['g4p_id'],'" />
  <input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form></div>';
  break;

//////////////////////////////////////
//////  Ajout d'une base
////////////////////////////////////
  case 'new_base':
  echo '<h2>',$g4p_langue['a_index_new_base_titre'],'</h2>
    <div class="cadre"><form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=new_base',0),'" name="new_base">';
  echo $g4p_langue['a_index_new_base_nom'],'<input name="nom" type="text" value="" size="80" /><br />';
  echo $g4p_langue['a_index_new_base_descr'],'<br /><textarea rows="8" cols="80" name="description"></textarea><br />';
  echo '<input type="submit" value="',$g4p_langue['submit_ajouter'],'" /></form></div><br />';
  break;

  case 'gerer_download':
  if($_SESSION['permission']->permission[_PERM_ADMIN_] or $_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
  {
    echo '<h2>',$g4p_langue['a_index_gerer_download_titre'],'</h2><div class="cadre">';
    echo $g4p_langue['a_index_gerer_download_message'];

    if(!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
      if($g4p_langue['entete_charset']=='UTF-8')
        $g4p_db_nom=utf8_decode($_SESSION['genea_db_nom']);
      else
        $g4p_db_nom=$_SESSION['genea_db_nom'];

      $sql='SELECT id, nom FROM genea_infos WHERE id='.$_SESSION['genea_db_id'];
      $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
      $g4p_liste_bases=$g4p_mysqli->g4p_result($g4p_infos_req);
 
      $sql='SELECT base, download_id, fichier, titre, description FROM genea_download WHERE base='.$_SESSION['genea_db_id'];
      if($g4p_infos_req=$g4p_mysqli->g4p_query($sql))
      {
        if($tmp=$g4p_mysqli->g4p_result($g4p_infos_req))
          foreach($tmp as $atmp)
            $g4p_liste_fichiers[$atmp['base']][]=$atmp;
      }
    }
    else
    {      
      $sql='SELECT id, nom FROM genea_infos';
      $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
      $g4p_liste_bases=$g4p_mysqli->g4p_result($g4p_infos_req);
  
      $sql='SELECT base, download_id, fichier, titre, description FROM genea_download';
      $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
      $tmp=$g4p_mysqli->g4p_result($g4p_infos_req);
      foreach($tmp as $atmp)
        $g4p_liste_fichiers[$atmp['base']][]=$atmp;
    }

    foreach($g4p_liste_bases as $g4p_une_base)
    {
      if($g4p_langue['entete_charset']=='UTF-8')
        $g4p_db_nom=utf8_decode($g4p_une_base['nom']);
      else
        $g4p_db_nom=$g4p_une_base['nom'];

      echo '<br /><br />';
      echo '<h3 style="display:inline">',sprintf($g4p_langue['a_index_gerer_download_base'],$g4p_une_base['nom']),'</h3>';
      echo ' <a href="',g4p_make_url('admin','export_rapport.php','base='.$g4p_une_base['id'],0),'" class="admin">Créer un nouveau rapport ou un GEDCOM</a>';
      if(isset($g4p_liste_fichiers[$g4p_une_base['id']]))
      {
        echo '<div style="margin-left:10px; border-left:1px solid black;">';
        foreach($g4p_liste_fichiers[$g4p_une_base['id']] as $g4p_a_fichier)
        {
          echo '<h4><a href="',g4p_make_url('cache/'.utf8_decode($g4p_une_base['nom']).'/fichiers',$g4p_a_fichier['fichier'],'',0),'">',$g4p_a_fichier['titre'],'</a> <a href="',g4p_make_url('admin','exec.php','g4p_opt=del_rapport&amp;g4p_id='.$g4p_a_fichier['download_id'],0),'" class="admin" onclick=" return confirme(this, \'',$g4p_langue['a_index_delrapport_confirme_suppr_js'],'\')">Supprimer</a></h4>';
          echo '<p>',nl2br(trim($g4p_a_fichier['description'])),'</p>';
        }
        echo '</div>';
      }
      echo '<hr />';
    }
    echo '</div>';    
  }
  break;

  case 'gerer_permissions':
  if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
  {
    echo '<h2>',$g4p_langue['a_index_gerer_perm_titre'],'</h2>';
    echo '<div class="cadre">';
    if($g4p_config['g4p_type_install']=='seule' or $g4p_config['g4p_type_install']=='seule-mod_rewrite')
    {
       echo '<b>',$g4p_langue['a_index_gerer_perm_ajout_membre'],'</b><br /><form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=new_member',0),'" name="new_member">',$g4p_langue['a_index_gerer_perm_email'],'<input name="email" type="text" value="" size="40"  /><br />',$g4p_langue['a_index_gerer_perm_pass'],'<input name="mdp" type="text" value="" size="40" /><br /><br /><input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form></div><br />';
    }
    
    echo '<div class="cadre">';
    if($g4p_config['g4p_type_install']=='seule' or $g4p_config['g4p_type_install']=='seule-mod_rewrite')
    {
       echo '<b>',$g4p_langue['a_index_gerer_perm_supp_membre'],'</b><br /><form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=del_member',0),'" name="del_member">';
       echo '<select name="membre_id" style="width:auto">';
       $g4p_liste_membre=g4p_liste_membres();
	   foreach($g4p_liste_membre as $g4p_a_result)
      	   if($_SESSION['g4p_id_membre']!=$g4p_a_result['id'])
      	   	 echo '<option value="',$g4p_a_result['id'],'">',$g4p_a_result['email'],'</option>';
       echo '</select>';
       echo '<input type="submit" value="',$g4p_langue['Supprimer'],'" /></form></div><br />';
    }

    echo '<div class="cadre"><b>',$g4p_langue['a_index_gerer_perm_gest_perm'],'</b><br />
      ',$g4p_langue['a_index_gerer_perm_edit_perm'],'
      <form class="formulaire" method="post" action="',g4p_make_url('admin','index.php','g4p_opt=gerer_permissions',0),'" name="load_perm">';
      echo '<select name="nom" style="width:auto"><option value="0" selected="selected"> </option>';
    // creation de la liste des membres
    $g4p_liste_membre=g4p_liste_membres();
    foreach($g4p_liste_membre as $g4p_a_result)
      echo '<option value="',$g4p_a_result['id'],'">',$g4p_a_result['email'],'</option>';
    echo '</select><input type="submit" value="',$g4p_langue['submit_editer'],'" /></form><br /><hr /><br />';

    if(isset($_REQUEST['nom']))
    {
      $sql="SELECT id, nom FROM genea_infos ORDER BY nom";
      $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
      $g4p_liste_base=$g4p_mysqli->g4p_result($g4p_infos_req);

      echo '<form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=mod_permissions',0),'" name="mod_perm">
        <table id="permission"><thead><tr><td>Nom de la base</td><td>',$g4p_langue['a_index_gerer_perm_type_perm'],'</td><td>',$g4p_langue['a_index_gerer_perm_valeur'],'</td><td></td></tr></thead>';
        
      $sql="SELECT genea_permissions.id as id_perm, id_base, nom as nom_base, type, permission FROM genea_permissions LEFT JOIN genea_infos ON id_base=genea_infos.id WHERE id_membre=".$_REQUEST['nom']." ORDER BY id_base, type";
      $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
      if($g4p_result=$g4p_mysqli->g4p_result($g4p_infos_req))
      {
        foreach($g4p_result as $g4p_a_result)
        {
          echo '<tr><td><select name="nom[',$g4p_a_result['id_perm'],']" style="width:auto">';
          if($g4p_a_result['nom_base']=='*')
            echo '<option value="*" selected="selected">-Toutes-</option>';
          else
            echo '<option value="*">-Toutes-</option>';
          
          foreach($g4p_liste_base as $tmp)
          {
            if($g4p_a_result['nom_base']==$tmp['nom'])
              $selected='selected="SELECTED"';
            else
              $selected='';
            echo '<option value="',$tmp['id'],'" ',$selected,'>',$tmp['nom'],'</option>';
          }
          echo '</select></td>
            <td>';
          //type de permissions
          echo '<select name="type[',$g4p_a_result['id_perm'],']" style="width:auto">';  
          foreach($g4p_permissions as $g4p_a_perm)
          {
            //print_r($g4p_a_perm);
            if($g4p_a_result['type']==$g4p_a_perm['id'])
              $selected='selected="SELECTED"';
            else
              $selected='';
            echo '<option value="',$g4p_a_perm['id'],'" ',$selected,'>',$g4p_langue[$g4p_a_perm['id']],'</option>';            
          }  
          echo '</select></td><td>';
            
          //valeur de la perm  
          if($g4p_permissions[$g4p_a_result['type']]['type']=='text')
          {
            echo '<input type="text" name="valeur[',$g4p_a_result['id_perm'],']" value="',$g4p_a_result['permission'],'"  />';
          }
          elseif($g4p_permissions[$g4p_a_result['type']]['type']=='select')
          {
            echo '<select name="valeur[',$g4p_a_result['id_perm'],']" style="width:auto">';
            if($g4p_permissions[$g4p_a_result['type']]['value']=='oui-non')
            {
              if($g4p_a_result['permission']==1)
              {
                echo '<option value="1" selected="selected">Oui</option>';
                echo '<option value="0">Non</option>';
              }
              else
              {
                echo '<option value="1">Oui</option>';
                echo '<option value="0" selected="selected">Non</option>';
              }            
            }
            elseif($g4p_permissions[$g4p_a_result['type']]['value']=='database')
            {
              foreach($g4p_liste_base as $tmp)
              {
                if($g4p_a_result['permission']==$tmp['id'])
                  $selected='selected="SELECTED"';
                else
                  $selected='';
                echo '<option value="',$tmp['id'],'" ',$selected,'>',$tmp['nom'],'</option>';
              }           
            }
            echo '</select>';
          }      
          echo '</td>
            <td><a href="',g4p_make_url('admin','exec.php','g4p_opt=suppr_perm&amp;g4p_id='.$g4p_a_result['id_perm'].'&amp;id_membre='.$_REQUEST['nom'],0),'">Supprimer</a></td></tr>';
        }
      }
      //echo '</table>';
      
      //nouvelle perm
/*      echo 'Ajouter une nouvelle permission';
      echo '<table id="permission" style="margin-left:10%;">';*/
      echo '<tr><td><select name="nom[nouvelle_perm1]" style="width:auto">';
      echo '<option value="" selected="selected">&nbsp;</option>';
      echo '<option value="*">-Toutes-</option>';
      foreach($g4p_liste_base as $tmp)
        echo '<option value="',$tmp['id'],'">',$tmp['nom'],'</option>';
      echo '</select></td><td>';

      //type de permissions
      echo '<select name="type[nouvelle_perm1]" style="width:auto">';  
      echo '<option value="" selected="selected">&nbsp;</option>';
      foreach($g4p_permissions as $g4p_a_perm)
        if($g4p_a_perm['type']=='select' and $g4p_a_perm['value']=='oui-non')    
          echo '<option value="',$g4p_a_perm['id'],'">',$g4p_langue[$g4p_a_perm['id']],'</option>';
      echo '</select></td><td>';
            
      //valeur de la perm  
      echo '<select name="valeur[nouvelle_perm1]" style="width:auto">';
      echo '<option value="" selected="selected">&nbsp;</option>';      
      echo '<option value="1">Oui</option>';
      echo '<option value="0">Non</option>';
      echo '</select></td>
            <td>Ajouter</td></tr>';
            
      //nouvelle perm************************
      echo '<tr><td><select name="nom[nouvelle_perm2]" style="width:auto">';
      echo '<option value="" selected="selected">&nbsp;</option>';
      echo '<option value="*">-Toutes-</option>';
      foreach($g4p_liste_base as $tmp)
        echo '<option value="',$tmp['id'],'">',$tmp['nom'],'</option>';
      echo '</select></td><td>';

      //type de permissions
      echo '<select name="type[nouvelle_perm2]" style="width:auto">';  
      echo '<option value="" selected="selected">&nbsp;</option>';
      foreach($g4p_permissions as $g4p_a_perm)
        if($g4p_a_perm['type']=='select' and $g4p_a_perm['value']=='oui-non')
          echo '<option value="',$g4p_a_perm['id'],'">',$g4p_langue[$g4p_a_perm['id']],'</option>';
      echo '</select></td><td>';
            
      //valeur de la perm  
      echo '<select name="valeur[nouvelle_perm2]" style="width:auto">';
      echo '<option value="" selected="selected">&nbsp;</option>';      
      echo '<option value="1">Oui</option>';
      echo '<option value="0">Non</option>';
      echo '</select></td>
            <td>Ajouter</td></tr>';   

      echo '</table>';
      
      echo '<input type="checkbox" style="vertical-align:middle" name="save_modele" value="1" /> Se souvenir du modèle, nom du modèle : <input type="text" style="vertical-align:middle" name="titre_modele" value="" /> 
      <br /><a href="'.g4p_make_url('admin','exec.php','g4p_opt=suppr_allperm&amp;g4p_id='.$_REQUEST['nom'],0).'">Tout éffacer</a> - Charger le modèle : ';
      if(file_exists($g4p_chemin.'p_conf/modele_permissions.php'))
      {      
        require_once($g4p_chemin.'p_conf/modele_permissions.php');
        if(!empty($g4p_modele_permissions))
        {
          echo '<select name="charge_modele" style="width:auto">';  
          echo '<option value="" selected="selected">&nbsp;</option>';
          foreach($g4p_modele_permissions as $g4p_a_modele=>$value)
            echo '<option value="',$g4p_a_modele,'">',$g4p_a_modele,'</option>';
          echo '</select>';     
        }
        else
          echo '(Aucun modèle en mémoire)';
      }
      else
        echo '(Aucun modèle en mémoire)';
      echo '<br /><input type="hidden" name="id_membre" value="',$_REQUEST['nom'],'" /><input type="submit" value="',$g4p_langue['submit_enregistrer'],'" />';
      echo '</form><br />';
    }
    
    echo '</div><br />';
  }
  break;

  case 'mod_perm':
  if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
  {
    echo '<h2>',$g4p_langue['a_index_mod_perm_titre'],'</h2>';
    echo '<div class="cadre">
     <form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=mod_perm',0),'" name="mod_perm">';
    $sql="SELECT id_base, type, permission FROM genea_permissions WHERE id=".$_GET['g4p_id'];
    $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
    $g4p_permission=$g4p_mysqli->g4p_result($g4p_infos_req);

    $sql="SELECT id, nom FROM genea_infos";
    $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
    $g4p_liste_base=$g4p_mysqli->g4p_result($g4p_infos_req);

    echo $g4p_langue['a_index_mod_perm_nom_base'],'<select name="nom" style="width:auto">';
    foreach($g4p_liste_base as $g4p_une_base)
      echo '<option value="',$g4p_une_base['id'],'">',$g4p_une_base['nom'],'</option>';
    echo '<option value="*">*</option>';
    echo '</select><br />';
    echo $g4p_langue['a_index_mod_perm_type_perm'],'<select name="type" style="width:auto"><option value="0" selected="selected"> </option>';
    foreach($g4p_permissions as $g4p_a_perm)
      echo '<option value="',$g4p_a_perm,'">',$g4p_langue[$g4p_a_perm],'</option>';
    echo '</select><br />
      <input type="hidden" name="g4p_id" value="',$_GET['g4p_id'],'" />',sprintf($g4p_langue['a_index_mod_perm_perm'],'<input type="text" name="permission" value="" size="40" />'),'<br />';
    echo '<input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form></div><br />';
  }
  break;

////////////////////////////////////
//////  Vide une base de données
////////////////////////////////////
  case 'vide_base':
  if($_SESSION['permission']->permission[_PERM_ADMIN_] or $_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
  {
    echo '<h2>',$g4p_langue['a_index_vide_base_titre'],'</h2>';
    echo '<div class="cadre">
      <p>',$g4p_langue['a_index_vide_base_message'],'</p>
     <form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=vide_base',0),'" name="vide_base">';
    $sql="SELECT id, nom FROM genea_infos";
    if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
      $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
      $g4p_liste_base=$g4p_mysqli->g4p_result($g4p_infos_req);
      echo $g4p_langue['a_index_vide_base_nom_base'],'<select name="nom" style="width:auto">';
      foreach($g4p_liste_base as $g4p_une_base)
        echo '<option value="',$g4p_une_base['id'],'">',$g4p_une_base['nom'],'</option>';
      echo '</select><br />';
    }
    else
    {
      echo $g4p_langue['a_index_vide_base_nom_base'],'<select name="nom" style="width:auto">';
        echo '<option value="',$_SESSION['genea_db_nom'],'">',$_SESSION['genea_db_nom'],'</option>';
      echo '</select><br />';    
    }
    echo '<input type="submit" value="',$g4p_langue['submit_vider'],'" /></form></div>';
  }
  break;

////////////////////////////////////
//////  supprimer une base de données
////////////////////////////////////
  case 'suppr_base':
  if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
  {
    echo '<h2>',$g4p_langue['a_index_suppr_base'],'</h2>';
    echo '<div class="cadre">
      <p>',$g4p_langue['a_index_suppr_base_message'],'</p>
     <form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=suppr_base',0),'" name="suppr_base">';
    $sql="SELECT id, nom FROM genea_infos";
    $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
    $g4p_liste_base=$g4p_mysqli->g4p_result($g4p_infos_req);
    echo $g4p_langue['a_index_suppr_base_nom_base'],'<select name="nom" style="width:auto">';
    foreach($g4p_liste_base as $g4p_une_base)
      echo '<option value="',$g4p_une_base['id'],'">',$g4p_une_base['nom'],'</option>';
    echo '</select><br />
      <input type="submit" value="',$g4p_langue['Supprimer'],'" /></form></div>';
  }
  break;

////////////////////////////////////
//////  Recupération de la base lors d'un crash de l'import GEDCOM
////////////////////////////////////
  case 'recup_crash':
  if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
  {
    echo '<h2>',$g4p_langue['a_index_recup_crash_titre'],'</h2>';
    echo '<div class="cadre">
      <p>',$g4p_langue['a_index_recup_crash_message'],'</p>
     <form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=recup_crash',0),'" name="recup_crash">
      <input type="submit" value="',$g4p_langue['submit_restaurer'],'" /></form></div>';
  }
  break;

////////////////////////////////////
//////  Ajout d'un document multimédia
////////////////////////////////////
  case 'ajout_media':
  if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
  {
    if(!isset($_GET['g4p_id_media']))
      $_SESSION['formulaire']['g4p_id_media']='';
    else
      $_SESSION['formulaire']['g4p_id_media']=$_GET['g4p_id_media'];
      echo '<h2>',$g4p_langue['a_index_ajout_media_titre'],'</h2><div class="cadre">
        <em>',$g4p_langue['a_index_ajout_media_lier'],'<br /></em>
      <form class="formulaire" method="post" action="',g4p_make_url('admin','exec.php','g4p_opt=link_media',0),'" name="link_media">
      ',$g4p_langue['a_index_ajout_media_id'],'<input name="g4p_id_media" type="text" value="',$_SESSION['formulaire']['g4p_id_media'],'" />
      <input name="g4p_id" type="hidden" value="',$_GET['g4p_id'],'" />
      <input name="g4p_type" type="hidden" value="',$_GET['g4p_type'],'" />
      <input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form>
      <a href="',g4p_make_url('','recherche.php','type=obje_0&amp;g4p_referer='.rawurlencode('admin|index.php|'.$_SERVER['QUERY_STRING']).'&amp;g4p_champ=g4p_id_media',0).'">',$g4p_langue['a_index_ajout_media_recherche'],'</a>
      <hr /><br />
      <em>',$g4p_langue['a_index_ajout_media_nouveau'],'</em>

       <form class="formulaire" method="post" enctype="multipart/form-data" action="',g4p_make_url('admin','exec.php','g4p_opt=ajout_media',0),'" name="ajout_media">
       ',$g4p_langue['a_index_ajout_media_titre_media'],'<input type="text" name="titre" value="" size="40" /><br />
       ',$g4p_langue['a_index_ajout_media_media'],'<input type="file" name="fichier" /><br />
       ',$g4p_langue['a_index_ajout_media_url'],'<input type="text" name="url" value="http://" size="40" /><br />
        <input name="g4p_id" type="hidden" value="',$_GET['g4p_id'],'" />
        <input name="g4p_type" type="hidden" value="',$_GET['g4p_type'],'" />
        <input type="submit" value="',$g4p_langue['submit_ajouter'],'" /></form></div>';
  }
  break;

////////////////////////////////////
//////  modification d'un média
////////////////////////////////////
  case 'mod_multimedia':
  if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
  {
    $sql="SELECT title, file FROM genea_multimedia WHERE id=".$_GET['g4p_id'];
    $g4p_result_req=$g4p_mysqli->g4p_query($sql);
    if($g4p_media=$g4p_mysqli->g4p_result($g4p_result_req))
    {
      $g4p_media=$g4p_media[0];
      echo '<h2>',$g4p_langue['a_index_mod_multimedia_titre'],'</h2>';
      echo '<div class="cadre"><em>',$g4p_langue['a_index_mod_multimedia_lien'],'</em><br />';
      $sql="SELECT genea_individuals.indi_id, indi_nom, indi_prenom FROM rel_indi_multimedia
        LEFT JOIN genea_individuals USING(indi_id)
        WHERE multimedia_id=".$_GET['g4p_id'];
      if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
      {
        if($g4p_ref=$g4p_mysqli->g4p_result($g4p_result_req))
        {
          echo $g4p_langue['a_index_mod_multimedia_lien_indi'],'<ul>';
          foreach($g4p_ref as $g4p_a_ref)
            echo '<li>',$g4p_a_ref['indi_nom'],' ',$g4p_a_ref['indi_prenom'],'</li>';
        }
      }
      echo '</ul>';

      $sql="SELECT husb.indi_nom as husb_nom, husb.indi_prenom as husb_prenom, wife.indi_nom as wife_nom, wife.indi_prenom as wife_prenom FROM rel_familles_multimedia
        LEFT JOIN genea_familles USING (familles_id)
        LEFT JOIN genea_individuals AS husb ON(genea_familles.familles_husb=husb.indi_id)
        LEFT JOIN genea_individuals AS wife ON(genea_familles.familles_wife=wife.indi_id)
        WHERE multimedia_id=".$_GET['g4p_id'];
      if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
      {
        if($g4p_ref=$g4p_mysqli->g4p_result($g4p_result_req))
        {
          echo $g4p_langue['a_index_mod_multimedia_lien_famille'],'<ul>';
          foreach($g4p_ref as $g4p_a_ref)
            echo '<li>',$g4p_a_ref['husb_nom'],' ',$g4p_a_ref['husb_prenom'],' - ',$g4p_a_ref['wife_nom'],' ',$g4p_a_ref['wife_prenom'],'</li>';
        }
      }
      echo '</ul>';

      $sql="SELECT type, date_event, place_ville, ";
      $sql.="genea_place.place_id as place_id
        FROM rel_events_multimedia
        LEFT JOIN genea_events ON (events_id=id)
        LEFT JOIN genea_place ON ( genea_place.place_id=genea_events.place_id )
        WHERE multimedia_id=".$_GET['g4p_id'];
      if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
      {
        if($g4p_ref=$g4p_mysqli->g4p_result($g4p_result_req))
        {
          echo $g4p_langue['a_index_mod_multimedia_lien_event'],'<ul>';
          foreach($g4p_ref as $g4p_a_ref)
          {
            echo '<li>',$g4p_tag_def[$g4p_a_ref['type']],' ',g4p_date($g4p_a_ref['date_event']),' - ',$g4p_a_ref['place_ville'],'</li>';
          }
        }
      }
      echo '</ul></div>';

      echo '<div class="cadre"><form class="formulaire" method="post" enctype="multipart/form-data" action="',g4p_make_url('admin','exec.php','g4p_opt=modif_multimedia',0),'" name="modif_multimedia">';
      echo $g4p_langue['a_index_ajout_media_titre_media'],'<input name="g4p_title" type="text" size="40" value="',$g4p_media['title'],'" /><br />';
      echo $g4p_langue['a_index_mod_multimedia_file_url'],'<input name="g4p_file" size="40" type="text" value="',$g4p_media['file'],'" /><br />';
      echo $g4p_langue['a_index_mod_multimedia_new_file'],'<input name="fichier" type="file" /><br />';
      echo '<input name="g4p_id" type="hidden" value="',$_GET['g4p_id'],'" />
        <input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form></div>';
    }
    else
    {
      $_SESSION['message']=$g4p_langue['a_index_mod_multimedia_erreur'];
      $g4p_indi=g4p_destroy_cache();
      header('location:'.g4p_make_url('','index.php','id_pers='.$g4p_indi->indi_id,'fiche-'.g4p_prepare_varurl($g4p_indi->nom).'-'.g4p_prepare_varurl($g4p_indi->prenom).'-'.$g4p_indi->indi_id));
    }
  }
  break;

////////////////////////////////////
//////  Ajout d'un depot d'archive
////////////////////////////////////
  case 'ajout_repo':
  if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
  {
    if(!isset($_GET['g4p_referer']))
     $_SESSION['formulaire']['g4p_referer']='';
    else
      $_SESSION['formulaire']['g4p_referer']=$_GET['g4p_referer'];
    echo '<h2>',$g4p_langue['a_index_ajout_depot_titre'],'</h2><div class="cadre">
     <form class="formulaire" method="post" enctype="multipart/form-data" action="',g4p_make_url('admin','exec.php','g4p_opt=ajout_repo',0),'" name="ajout_repo">
     ',$g4p_langue['a_index_ajout_depot_nom'],'<input type="text" name="repo_name" value="" size="40" /><br />
     ',$g4p_langue['a_index_ajout_depot_adresse'],'<input type="text" name="repo_addr" value="" size="40" /><br />
     ',$g4p_langue['a_index_ajout_depot_ville'],'<input type="text" name="repo_city" value="" size="40" /><br />
     ',$g4p_langue['a_index_ajout_depot_cp'],'<input type="text" name="repo_post" value="" size="40" /><br />
     ',$g4p_langue['a_index_ajout_depot_etat'],'<input type="text" name="repo_stae" value="" size="40" /><br />
     ',$g4p_langue['a_index_ajout_depot_pays'],'<input type="text" name="repo_ctry" value="" size="40" /><br />
     ',$g4p_langue['a_index_ajout_depot_tel'],' 1<input type="text" name="repo_phon1" value="" size="40" /><br />
     ',$g4p_langue['a_index_ajout_depot_tel'],' 2<input type="text" name="repo_phon2" value="" size="40" /><br />
     ',$g4p_langue['a_index_ajout_depot_tel'],' 3<input type="text" name="repo_phon3" value="" size="40" /><br />
      <input name="g4p_referer" type="hidden" value="',rawurlencode($_SESSION['formulaire']['g4p_referer']),'" />
      <input type="submit" value="',$g4p_langue['submit_ajouter'],'" /></form>           
      </div>';
  }
  break;

////////////////////////////////////
//////  modification d'un depot d'archive
////////////////////////////////////
  case 'mod_repo':
  if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
  {
    if(!isset($_GET['g4p_referer']))
      $_SESSION['formulaire']['g4p_referer']='';
    else
      $_SESSION['formulaire']['g4p_referer']=$_GET['g4p_referer'];

    echo '<h2>Sélectionner un dépot</h2><div class="cadre">
       <form class="formulaire" method="post" enctype="multipart/form-data" action="',g4p_make_url('admin','index.php','g4p_opt=mod_repo',0),'" name="ajout_repo"><select id="depot" name="depot">';
    $sql="SELECT repo_id, repo_name FROM genea_repository WHERE base=".$_SESSION['genea_db_id'];
    if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
    {
      if($g4p_result=$g4p_mysqli->g4p_result($g4p_result_req))
      {
        foreach($g4p_result as $g4p_a_result)
         echo '<option value="',$g4p_a_result['repo_id'],'">',$g4p_a_result['repo_id'],'-',$g4p_a_result['repo_name'],'</option>';
      }
    }
    echo '</select>';
    echo '<input type="submit" value="Modifier" /></form></div>';
    
    if(isset($_POST['depot']))
    {
      echo '<h2>Modification d\'un dépot</h2><div class="cadre">';
      $sql="SELECT repo_id, repo_name, repo_addr, repo_city, repo_post, repo_stae, repo_ctry, repo_phon1, repo_phon2, repo_phon3 FROM genea_repository WHERE repo_id=".$_POST['depot'];
      if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
      {
        if($g4p_result=$g4p_mysqli->g4p_result($g4p_result_req))
        {
          echo '<form class="formulaire" method="post" enctype="multipart/form-data" action="',g4p_make_url('admin','exec.php','g4p_opt=mod_repo',0),'" name="mod_repo">
         ',$g4p_langue['a_index_ajout_depot_nom'],'<input type="text" name="repo_name" value="',$g4p_result[0]['repo_name'],'" size="40" /><br />
         ',$g4p_langue['a_index_ajout_depot_adresse'],'<input type="text" name="repo_addr" value="',$g4p_result[0]['repo_addr'],'" size="40" /><br />
         ',$g4p_langue['a_index_ajout_depot_ville'],'<input type="text" name="repo_city" value="',$g4p_result[0]['repo_city'],'" size="40" /><br />
         ',$g4p_langue['a_index_ajout_depot_cp'],'<input type="text" name="repo_post" value="',$g4p_result[0]['repo_post'],'" size="40" /><br />
     	 ',$g4p_langue['a_index_ajout_depot_etat'],'<input type="text" name="repo_stae" value="',$g4p_result[0]['repo_stae'],'" size="40" /><br />
         ',$g4p_langue['a_index_ajout_depot_pays'],'<input type="text" name="repo_ctry" value="',$g4p_result[0]['repo_ctry'],'" size="40" /><br />
         ',$g4p_langue['a_index_ajout_depot_tel'],' 1 :<input type="text" name="repo_phon1" value="',$g4p_result[0]['repo_phon1'],'" size="40" /><br />
         ',$g4p_langue['a_index_ajout_depot_tel'],' 2 :<input type="text" name="repo_phon2" value="',$g4p_result[0]['repo_phon2'],'" size="40" /><br />
         ',$g4p_langue['a_index_ajout_depot_tel'],' 3 :<input type="text" name="repo_phon3" value="',$g4p_result[0]['repo_phon3'],'" size="40" /><br />
         <input type="hidden" name="depot" value="',$g4p_result[0]['repo_id'],'" />
         <input name="g4p_referer" type="hidden" value="',rawurlencode('admin|index.php|g4p_opt=mod_repo'),'" />
          <input type="submit" value="Modifier" /></form></div>';
        }
      }
    }
  }
  break;
  
////////////////////////////////////
//////  suppression d'un depot d'archive
////////////////////////////////////
  case 'del_repo':
  if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
  {
    echo '<h2>Sélectionner un dépot</h2><div class="cadre">
       <form class="formulaire" method="post" enctype="multipart/form-data" action="',g4p_make_url('admin','exec.php','g4p_opt=del_repo',0),'" name="del_repo"><select id="depot" name="depot">';
    $sql="SELECT repo_id, repo_name FROM genea_repository WHERE base=".$_SESSION['genea_db_id'];
    if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
    {
      if($g4p_result=$g4p_mysqli->g4p_result($g4p_result_req))
      {
        foreach($g4p_result as $g4p_a_result)
         echo '<option value="',$g4p_a_result['repo_id'],'">',$g4p_a_result['repo_id'],'-',$g4p_a_result['repo_name'],'</option>';
      }
    }
    echo '</select>';
    echo '<input type="submit" value=" Supprimer " /></form></div>';
  }
  break;    
  
  
////////////////////////////////////
//////  modification d'un lieu
////////////////////////////////////
  case 'mod_place':
  if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
  {
    if(!isset($_GET['g4p_referer']))
      $_SESSION['formulaire']['g4p_referer']='';
    else
      $_SESSION['formulaire']['g4p_referer']=$_GET['g4p_referer'];

    echo '<h2>Sélectionner un lieu</h2><div class="cadre">
       <form class="formulaire" method="post" enctype="multipart/form-data" action="',g4p_make_url('admin','index.php','g4p_opt=mod_place',0),'" name="ajout_repo"><select id="place" name="place">';
    $sql="SELECT place_id, place_lieudit, place_ville, place_departement, place_region, place_pays FROM genea_place WHERE base=".$_SESSION['genea_db_id']." ORDER BY place_ville";
    if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
      if($g4p_result=$g4p_mysqli->g4p_result($g4p_result_req))
        foreach($g4p_result as $g4p_a_result)
         echo '<option value="',$g4p_a_result['place_id'],'">',$g4p_a_result['place_id'],'- ',$g4p_a_result['place_lieudit'],' ',$g4p_a_result['place_ville'],' ',$g4p_a_result['place_departement'],' ',$g4p_a_result['place_region'],' ',$g4p_a_result['place_pays'],'</option>';

    echo '</select>';
    echo '<input type="submit" value="Modifier" /></form></div>';
    
    if(!isset($_POST['place']) && isset($_GET['place']))
      $_POST['place']=$_GET['place'];
    if(isset($_POST['place']))
    {
      echo '<h2>Modification d\'un lieu</h2><div class="cadre">';
      $g4p_chps=implode(',',$g4p_config['subdivision']);
      $sql="SELECT place_id, ".$g4p_chps." FROM genea_place WHERE place_id=".$_POST['place'];
      if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
      {
       if($g4p_result=$g4p_mysqli->g4p_result($g4p_result_req))
        {
          echo '<a href="',g4p_make_url('admin','recherche_gns.php','g4p_referer='.rawurlencode('admin|index.php|g4p_opt=mod_place&amp;place='.$_POST['place']),0),'">',$g4p_langue['recherche_gns_titre'],'</a><hr/>';
          echo '<form class="formulaire" method="post" enctype="multipart/form-data" action="',g4p_make_url('admin','exec.php','g4p_opt=mod_place',0),'" name="mod_repo">';
          foreach($g4p_config['subdivision'] as $a_subdiv)
          {
            echo $g4p_langue[$a_subdiv],' <input type="text"
           name="',$a_subdiv,'" value="';
            if(isset($_GET[$a_subdiv]))
              echo $_GET[$a_subdiv];
            else
              echo $g4p_result[0][$a_subdiv];
            echo '" size="40" /><br />';
          }
          echo '<input type="hidden" name="place" value="',$g4p_result[0]['place_id'],'" />
         <input name="g4p_referer" type="hidden" value="',rawurlencode('admin|index.php|g4p_opt=mod_place'),'" />
          <input type="submit" value=" Modifier " /></form>
          <hr />
          <a href="http://www.lion1906.com/Pages/Localisation.html" target="_blank">Recherche des coodonnées géographiques</a><br />
          <a href="http://www.lion1906.com/Pages/CodesPostaux.html" target="_blank">Recherche du code postal ou insee</a><br />
          
          </div>';
      
          echo '<h2>fusion du lieu avec :</h2><div class="cadre">';
          echo 'Le lieu ',implode(',', $g4p_result[0]),' va être remplacé par :<br />';
          echo '<form class="formulaire" method="post" enctype="multipart/form-data" action="',g4p_make_url('admin','exec.php','g4p_opt=fus_place',0),'" name="ajout_repo"><select id="new_place" name="new_place">';
          $sql="SELECT place_id, place_lieudit, place_ville, place_departement, place_region, place_pays FROM genea_place WHERE base=".$_SESSION['genea_db_id']." ORDER BY place_ville";
          if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
            if($g4p_result=$g4p_mysqli->g4p_result($g4p_result_req))
              foreach($g4p_result as $g4p_a_result)
               echo '<option value="',$g4p_a_result['place_id'],'">',$g4p_a_result['place_id'],'- ',$g4p_a_result['place_lieudit'],' ',$g4p_a_result['place_ville'],' ',$g4p_a_result['place_departement'],' ',$g4p_a_result['place_region'],' ',$g4p_a_result['place_pays'],'</option>';
          echo '</select>';
         echo '<input type="hidden" name="place" value="',$_POST['place'],'" />
           <input name="g4p_referer" type="hidden" value="',rawurlencode('admin|index.php|g4p_opt=mod_place'),'" />';
          echo '<input type="submit" value=" Modifier " /></form></div>';
       
        }
      }
    }
  }
  break;

////////////////////////////////////
//////  suppression d'un lieu
////////////////////////////////////
  case 'del_place':
  if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
  {
    echo '<h2>Sélectionner un lieu</h2><div class="cadre">
       <form class="formulaire" method="post" enctype="multipart/form-data" action="',g4p_make_url('admin','exec.php','g4p_opt=del_place',0),'" name="del_place"><select id="place" name="place">';
    $sql="SELECT place_id, place_lieudit, place_ville, place_departement, place_region, place_pays FROM genea_place WHERE base=".$_SESSION['genea_db_id']." ORDER BY place_ville";
    if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
      if($g4p_result=$g4p_mysqli->g4p_result($g4p_result_req))
        foreach($g4p_result as $g4p_a_result)
         echo '<option value="',$g4p_a_result['place_id'],'">',$g4p_a_result['place_id'],'- ',$g4p_a_result['place_lieudit'],' ',$g4p_a_result['place_ville'],' ',$g4p_a_result['place_departement'],' ',$g4p_a_result['place_region'],' ',$g4p_a_result['place_pays'],'</option>';
    echo '</select>';
    echo '<input name="g4p_referer" type="hidden" value="',rawurlencode('admin|panel.php|'),'" />';
    echo '<input type="submit" value=" Supprimer " /></form></div>';
  }
  break;  

////////////////////////////////////
//////  Ajout d'une association
////////////////////////////////////
  case 'ajout_asso':
  if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
  {
    if(!isset($_GET['g4p_id_asso']))
      $_GET['g4p_id_asso']='';
    if(!isset($_GET['g4p_referer']))
      $_SESSION['formulaire']['g4p_referer']='';
    else
      $_SESSION['formulaire']['g4p_referer']=$_GET['g4p_referer'];
    echo '<h2>',$g4p_langue['a_index_ajout_assoc_titre'],'</h2><div class="cadre">
     <form class="formulaire" method="post" enctype="multipart/form-data" action="'.g4p_make_url('admin','exec.php','g4p_opt=ajout_asso',0).'" name="ajout_asso">',$g4p_langue['a_index_ajout_assoc_id'];
     
      if(!empty($_SESSION['historic']['indi']))
      {
        echo '<select name="g4p_id_asso2" style="width:auto"><option value=""></option>';
        foreach($_SESSION['historic']['indi'] as $tmp)
        {
          $tmp=explode('||',$tmp);
          echo '<option value="'.$tmp[0].'">'.htmlentities($tmp[1],ENT_NOQUOTES,'UTF-8').'</option>';
        }
        echo '</select>',$g4p_langue['a_index_modfams_ou'];
      }
     
      echo '<input name="g4p_id_asso" type="text" value="',$_GET['g4p_id_asso'],'" /><br />
      ',$g4p_langue['a_index_ajout_assoc_descr'],'<input name="description" type="text" size="40" value="" /><br />
      <input name="g4p_id" type="hidden" value="',$_GET['g4p_lien'],'" />
      <input name="g4p_type" type="hidden" value="',$_GET['g4p_type'],'" />
      <input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form>
      <a href="'.g4p_make_url('','recherche.php','type=indi_0&amp;g4p_referer='.rawurlencode('admin|index.php|'.$_SERVER['QUERY_STRING']).'&amp;g4p_champ=g4p_id_asso',0).'">',$g4p_langue['a_index_ajout_assoc_recherche'],'</a>
      </div>';
  }
  break;
}


require_once($g4p_chemin.'pied_de_page.php');

?>

