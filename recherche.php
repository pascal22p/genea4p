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
 *          Page à tout faire                                              *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'entete.php');

if(!empty($_REQUEST['g4p_referer']))
    $g4p_referer='&g4p_referer='.$_REQUEST['g4p_referer'];
else
    $g4p_referer='';
if(!empty($_REQUEST['g4p_champ']))
    $g4p_champ='&g4p_champ='.$_REQUEST['g4p_champ'];
else
    $g4p_champ='';

echo '<h2>', sprintf($g4p_langue['recherche_titre'],$_SESSION['genea_db_nom']),'</h2>';
if(!isset($_REQUEST['g4p_referer']) and !isset($_REQUEST['g4p_champ']))
{
    echo '<div style="text-align:center;" class="cadre"><b>',$g4p_langue['recherche_rechercher'],'</b><br />
        <a href="'.g4p_make_url('','recherche.php','type=indi_0',0).'">',$g4p_langue['recherche_indi'],'</a> -
        <a href="'.g4p_make_url('','recherche.php','type=note_0',0).'">',$g4p_langue['recherche_note'],'</a> -
        <a href="'.g4p_make_url('','recherche.php','type=sour_0',0).'">',$g4p_langue['recherche_source'],'</a> -
        <a href="'.g4p_make_url('','recherche.php','type=depot_0',0).'">',$g4p_langue['recherche_depot'],'</a> -
        <a href="'.g4p_make_url('','recherche.php','type=obje_0',0).'">',$g4p_langue['recherche_media'],'</a> -
        <a href="'.g4p_make_url('','recherche.php','type=even_0',0).'">',$g4p_langue['recherche_event'],'</a> -
        </div>';
}

echo '<br /><div class="cadre">';

if(!isset($_GET['type']))
    $_GET['type']='indi_0';

switch($_GET['type'])
{
    case 'indi_0':
    //echo 'type de recherche : exacte approximmative<br />
    echo '<form class="formulaire" method="post" action="',g4p_make_url('','recherche.php','type=indi_1',0),'" name="indi">
        <em>',$g4p_langue['recherche_nom'],'</em><input type="text" name="g4p_nom" size="20" /><br />
        <em>',$g4p_langue['recherche_prenom'],'</em><input type="text" name="g4p_prenom" size="20" /><br />';
    echo '<input type="submit" value="',$g4p_langue['submit_rechercher'],'" /></form>';
    break;

    case 'indi_1':
    $sql="SELECT indi_id
        FROM `genea_individuals` 
        WHERE base =".$_SESSION['genea_db_id']." AND indi_nom LIKE '%".mysql_escape_string($_POST['g4p_nom'])."%' 
            AND indi_prenom LIKE '%".mysql_escape_string($_POST['g4p_prenom'])."%'";
    if($_SESSION['permission']->permission[_PERM_MASK_INDI_])
        $sql.=" AND (indi_resn IS NULL OR indi_resn<>'privacy')";
    $sql.=" ORDER BY indi_nom, indi_prenom
        LIMIT 200";
    $g4p_result=$g4p_mysqli->g4p_query($sql);
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    {
        echo '<ul>';
        foreach($g4p_result as $g4p_a_result)
        {
            $indi=g4p_load_indi_infos($g4p_a_result['indi_id']);
            echo '<li><img src="'.$g4p_chemin.'images/'.$indi->sexe.'.png" alt="'.
                $indi->sexe.'" class="icone_sexe" /> '.
                g4p_link_nom($indi->indi_id);
            $conjoint=array();
            if(!empty($indi->familles))
            {
                foreach($indi->familles as $a_famille)
                {
                    if(!empty($a_famille->husb) and $indi->indi_id!=$a_famille->husb->indi_id)
                        $conjoint[]=g4p_link_nom($a_famille->husb->indi_id);
                    elseif(!empty($a_famille->wife) and $indi->indi_id!=$a_famille->wife->indi_id)
                        $conjoint[]=g4p_link_nom($a_famille->wife->indi_id);
                }
                if(!empty($conjoint))
                    echo ' <img src="'.$g4p_chemin.'images/mariage.png" alt="mariage" class="icone_mar" /> '.implode(' <img src="'.$g4p_chemin.'images/mariage.png" alt="mariage" class="icone_mar" /> ',$conjoint);                
            }
            echo '</li>';
        }
        echo '</ul>';
    }
    break;


// recherche des notes
  case 'note_0':
  //Utiliser . pour un caractère inconnu, et .* pour remplacer 0 ou plusieurs caractères<br />
  If ($_SESSION['permission']->permission[_PERM_NOTE_])
  {
    echo '
      <form class="formulaire" method="post" action="',g4p_make_url('','recherche.php','type=note_1',0),'" name="note">
      <em>',$g4p_langue['recherche_note_text'],'</em><input type="text" name="g4p_text" size="50" /><br />
      ',$g4p_langue['recherche_note_parent'],'<select name="g4p_lien">
        <option value="tous">',$g4p_langue['recherche_note_tous'],'</option>
        <option value="orphelines">',$g4p_langue['recherche_note_orphelines'],'</option>
        <option value="indi">',$g4p_langue['recherche_note_indi'],'</option>
        <option value="events">',$g4p_langue['recherche_note_event'],'</option>
        <option value="familles">',$g4p_langue['recherche_note_familel'],'</option></select><br />';
    if(isset($_GET['g4p_referer']) and isset($_GET['g4p_champ']))
    {
      echo '<input name="g4p_referer" type="hidden" value="',rawurlencode($_GET['g4p_referer']),'" />
      <input name="g4p_champ" type="hidden" value="',$_GET['g4p_champ'],'" />';
    }
    echo '<input type="submit" value="',$g4p_langue['recherche_note_rechercher'],'" /></form>';
  }
  else
    echo $g4p_langue['acces_non_autorise'];
  break;

  case 'note_1':
  If ($_SESSION['permission']->permission[_PERM_NOTE_])
  {
    switch($_POST['g4p_lien'])
    {
      case 'tous':
      $sql="SELECT genea_notes.notes_id as notes_id, notes_text, notes_chan
        FROM genea_notes
        WHERE genea_notes.base =".$_SESSION['genea_db_id']." AND notes_text
        LIKE  '%".$_POST['g4p_text']."%'
        ORDER  BY notes_id LIMIT 200";
      if($g4p_result=g4p_db_query($sql))
      {
        if($g4p_result=g4p_db_result($g4p_result))
        {
          echo '<em>',sprintf($g4p_langue['recherche_indi_result'],count($g4p_result)),'</em><br /><br />';

          if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
            $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']));
          foreach($g4p_result as $g4p_a_note)
          {
            echo '<hr/><div><em>',$g4p_langue['recherche_note_num_note'],'<b>',$g4p_a_note['notes_id'],'</b> <br />',$g4p_langue['recherche_note_chan'],$g4p_a_note['notes_chan'],'
            </em><br />';
            if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
              echo '<b><a href="',g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],$_POST['g4p_referer'][2].'&'.$_POST['g4p_champ'].'='.$g4p_a_note['notes_id'],0),'">',$g4p_langue['recherche_note_select_note'],'</a></b>';
            echo '<p class="notes">',nl2br(htmlspecialchars(stripslashes($g4p_a_note['notes_text']))),'</p></div>';
          }
        }
      }
      break;

      case 'orphelines':
      $sql="SELECT genea_notes.notes_id AS notes_id, notes_text, notes_chan
        FROM genea_notes
        LEFT  JOIN rel_indi_notes
        On ( genea_notes.notes_id=rel_indi_notes.notes_id )
        LEFT  JOIN rel_events_notes
        on ( genea_notes.notes_id=rel_events_notes.notes_id )
        LEFT  JOIN rel_familles_notes
        on ( genea_notes.notes_id=rel_familles_notes.notes_id )
        WHERE genea_notes.base =".$_SESSION['genea_db_id']." AND rel_indi_notes.indi_id IS  NULL  AND rel_events_notes.events_id IS  NULL  AND rel_familles_notes.familles_id IS  NULL
        ORDER  BY notes_id LIMIT 200";
      if($g4p_result=g4p_db_query($sql))
      {
        if($g4p_result=g4p_db_result($g4p_result))
        {
          echo '<em>',sprintf($g4p_langue['recherche_indi_result'],count($g4p_result)),'</em><br /><br />';

          if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
            $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']));
          foreach($g4p_result as $g4p_a_note)
          {
            echo '<hr/><div><em>',$g4p_langue['recherche_note_num_note'],'<b>',$g4p_a_note['notes_id'],'</b> <a href="'.g4p_make_url('admin','exec.php','g4p_opt=suppr_note2&g4p_id='.$g4p_a_note['notes_id'],0).'"  onclick=" return confirme(this, \'',$g4p_langue['menu_sppr_confirme'],'\')" class="admin">',$g4p_langue['Supprimer'],'</a>
             <br />',$g4p_langue['recherche_note_chan'],$g4p_a_note['notes_chan'],'
            </em><br />';
            if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
              echo '<b><a href="',g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],$_POST['g4p_referer'][2].'&'.$_POST['g4p_champ'].'='.$g4p_a_note['notes_id'],0),'">',$g4p_langue['recherche_note_select_note'],'</a></b>';
            echo '<p class="notes">',nl2br($g4p_a_note['notes_text']),'</p></div>';
          }
        }
      }
      break;

      case 'indi':
      $sql="SELECT genea_individuals.indi_id as indi_id, indi_nom, indi_prenom, genea_notes.notes_id as notes_id, notes_text, notes_chan
        FROM genea_notes
        LEFT  JOIN rel_indi_notes
        USING ( notes_id )
        LEFT  JOIN genea_individuals
        USING ( indi_id )
        WHERE genea_notes.base =".$_SESSION['genea_db_id']." AND genea_individuals.indi_id IS NOT NULL AND notes_text
        LIKE  '%".mysql_escape_string($_POST['g4p_text'])."%'
        ORDER  BY notes_id LIMIT 200";
      if($g4p_result=g4p_db_query($sql))
      {
        if($g4p_result=g4p_db_result($g4p_result))
        {
          echo '<em>',sprintf($g4p_langue['recherche_indi_result'],count($g4p_result)),'</em><br /><br />';

          if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
            $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']));
          $g4p_avant=0;
          foreach($g4p_result as $g4p_a_note)
          {
            if($g4p_avant!=$g4p_a_note['notes_id'])
            {
              echo '<hr/><div><em>',$g4p_langue['recherche_note_num_note'],'<b>',$g4p_a_note['notes_id'],'</b> <br />',$g4p_langue['recherche_note_chan'],$g4p_a_note['notes_chan'],'
              </em><br />';
              if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
                echo '<b><a href="',g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],$_POST['g4p_referer'][2].'&'.$_POST['g4p_champ'].'='.$g4p_a_note['notes_id'],0),'">',$g4p_langue['recherche_note_select_note'],'</a></b>';
              echo '<p class="notes">',nl2br($g4p_a_note['notes_text']),'</p></div>';
            }

            echo $g4p_langue['recherche_note_note_de'],'<a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&id_pers='.$g4p_a_note['indi_id'],'fiche-'.g4p_prepare_varurl($g4p_a_note['indi_nom']).'-'.g4p_prepare_varurl($g4p_a_note['indi_prenom']).'-'.$g4p_a_note['indi_id']),'">',$g4p_a_note['indi_nom'],'
            ',$g4p_a_note['indi_prenom'],'</a><br />';
            $g4p_avant=$g4p_a_note['notes_id'];
          }
        }
      }
      break;

      case 'events':
      $sql="SELECT genea_events.id as events_id, genea_events.type as type, genea_events.date_event as date_event,
            genea_notes.notes_id as notes_id, notes_text, notes_chan, place_ville, ";
      $sql.="genea_place.place_id as place_id
        FROM genea_notes
        LEFT  JOIN rel_events_notes USING ( notes_id )
        LEFT  JOIN genea_events ON ( rel_events_notes.events_id=genea_events.id )
        LEFT JOIN genea_place ON ( genea_place.place_id=genea_events.place_id )
        WHERE genea_notes.base =".$_SESSION['genea_db_id']." AND genea_events.id IS NOT NULL AND notes_text
        LIKE  '%".mysql_escape_string($_POST['g4p_text'])."%'
        ORDER  BY notes_id LIMIT 200";
      if($g4p_result=g4p_db_query($sql))
      {
        if($g4p_result=g4p_db_result($g4p_result))
        {
          $g4p_event_chaine='';
          foreach($g4p_result as $val)
            $g4p_event_chaine.=$val['events_id'].',';
          
          //on récupère les ids des familles et des indi
          $sql="SELECT genea_individuals.indi_id, events_id FROM genea_individuals
            LEFT JOIN rel_indi_event USING(indi_id)
            WHERE events_id IN (".substr($g4p_event_chaine,0,-1).")";
          if($g4p_indi=g4p_db_query($sql))
            $g4p_indi=g4p_db_result($g4p_indi,'events_id');
        
          $sql="SELECT genea_familles.familles_id, events_id, familles_husb, familles_wife FROM genea_familles
            LEFT JOIN rel_familles_event USING(familles_id)
            WHERE events_id IN (".substr($g4p_event_chaine,0,-1).")";
          if($g4p_familles=g4p_db_query($sql))
            $g4p_familles=g4p_db_result($g4p_familles,'events_id');
               
         $g4p_avant=0;
          if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
            $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']));
          echo '<em>',sprintf($g4p_langue['recherche_indi_result'],count($g4p_result)),'</em><br /><br />';

          foreach($g4p_result as $g4p_a_note)
          {
            if($g4p_avant!=$g4p_a_note['notes_id'])
            {
              echo '<hr/><div><em>',$g4p_langue['recherche_note_num_note'],'<b>',$g4p_a_note['notes_id'],'</b> <br />',$g4p_langue['recherche_note_chan'],$g4p_a_note['notes_chan'],'
                </em><br />';
              if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
                echo '<b><a href="',g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],$_POST['g4p_referer'][2].'&'.$_POST['g4p_champ'].'='.$g4p_a_note['notes_id'],0),'">',$g4p_langue['recherche_note_select_note'],'</a></b>';
              echo '<p class="notes">',nl2br($g4p_a_note['notes_text']),'</p></div>';
            }

            if(isset($g4p_familles[$g4p_a_note['events_id']]))
            {
              if(!empty($g4p_indi[$g4p_a_note['events_id']]['familles_husb']))
                $g4p_familles[$g4p_a_note['events_id']]['indi_id']=$g4p_familles[$g4p_a_note['events_id']]['familles_husb'];
              else
                $g4p_familles[$g4p_a_note['events_id']]['indi_id']=$g4p_familles[$g4p_a_note['events_id']]['familles_wife'];
              echo sprintf($g4p_langue['recherche_note_noteevent'],$g4p_tag_def[$g4p_a_note['type']],g4p_date($g4p_a_note['date_event']),$g4p_a_note['place_ville']).' (<a href="'.g4p_make_url('','detail_event.php','g4p_id=f|'.$g4p_familles[$g4p_a_note['events_id']]['familles_id'].'|'.$g4p_a_note['events_id'].'&amp;g4p_indi_id='.$g4p_familles[$g4p_a_note['events_id']]['indi_id'],0).'">voir l\'événement</a>)<br />';
            }
            else
              echo sprintf($g4p_langue['recherche_note_noteevent'],$g4p_tag_def[$g4p_a_note['type']],g4p_date($g4p_a_note['date_event']),$g4p_a_note['place_ville']).' (<a href="'.g4p_make_url('','detail_event.php','g4p_id=i|'.$g4p_indi[$g4p_a_note['events_id']]['indi_id'].'|'.$g4p_a_note['events_id'].'&amp;g4p_indi_id='.$g4p_indi[$g4p_a_note['events_id']]['indi_id'],0).'">voir l\'événement</a>)<br />';
            
            $g4p_avant=$g4p_a_note['notes_id'];
          }
        }
      }
      break;

      case 'familles':
      $sql="SELECT genea_familles.familles_id as familles_id, husb.indi_id as husb_id, husb.indi_nom as husb_nom, husb.indi_prenom as husb_prenom,
        wife.indi_nom as wife_nom, wife.indi_prenom as wife_prenom, genea_notes.notes_id as notes_id, notes_text, notes_chan
        FROM genea_notes
        LEFT  JOIN rel_familles_notes
        USING ( notes_id )
        LEFT  JOIN genea_familles
        USING ( familles_id )
        LEFT  JOIN genea_individuals as husb
        ON ( husb.indi_id=genea_familles.familles_husb )
        LEFT  JOIN genea_individuals as wife
        ON ( wife.indi_id=genea_familles.familles_wife )
        WHERE genea_notes.base =".$_SESSION['genea_db_id']." AND genea_familles.familles_id IS NOT NULL AND notes_text
        LIKE  '%".mysql_escape_string($_POST['g4p_text'])."%'
        ORDER  BY notes_id LIMIT 200";
      if($g4p_result=g4p_db_query($sql))
      {
        if($g4p_result=g4p_db_result($g4p_result))
        {
          echo '<em>',sprintf($g4p_langue['recherche_indi_result'],count($g4p_result)),'</em><br /><br />';
        
          if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
            $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']));
          $g4p_avant=0;
          foreach($g4p_result as $g4p_a_note)
          {
            if($g4p_avant!=$g4p_a_note['notes_id'])
            {
              echo '<hr/><div><em>',$g4p_langue['recherche_note_num_note'],'<b>',$g4p_a_note['notes_id'],'</b> <br />',$g4p_langue['recherche_note_chan'],$g4p_a_note['notes_chan'],'
                </em><br />';
              if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
                echo '<b><a href="',g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],$_POST['g4p_referer'][2].'&'.$_POST['g4p_champ'].'='.$g4p_a_note['notes_id'],0),'">',$g4p_langue['recherche_note_select_note'],'</a></b>';
              echo '<p class="notes">',nl2br($g4p_a_note['notes_text']),'</p></div>';
            }
            echo $g4p_langue['recherche_note_notefamille'],'<a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&id_pers='.$g4p_a_note['husb_id'],'fiche-'.g4p_prepare_varurl($g4p_a_note['husb_nom']).'-'.g4p_prepare_varurl($g4p_a_note['husb_prenom']).'-'.$g4p_a_note['husb_id']),'">',$g4p_a_note['husb_nom'],'
            ',$g4p_a_note['husb_prenom'],' - ',$g4p_a_note['wife_nom'],' ',$g4p_a_note['wife_prenom'],'</a><br />';
            $g4p_avant=$g4p_a_note['notes_id'];
          }
        }
      }
      break;
    }
  }
  break;
// fin recherche pour note

// recherche dans les sources
  case 'sour_0':
  //Utiliser . pour un caractère inconnu, et .* pour remplacer 0 ou plusieurs caractères<br />
  if ($_SESSION['permission']->permission[_PERM_NOTE_])
  {
  	$sql="SELECT sources_auth, CONCAT(sources_auth, ' (', count(sources_auth), ')') as authors
        FROM genea_sources
        WHERE genea_sources.base =".$_SESSION['genea_db_id']." AND sources_auth<>'' GROUP BY sources_auth
        ORDER  BY sources_auth";
    if($g4p_result=g4p_db_query($sql))
    {
      if($g4p_liste_sources_auth=g4p_db_result($g4p_result))
        array_unshift($g4p_liste_sources_auth,array('sources_auth'=>'', 'authors'=>''));
    }
    else
      $g4p_liste_sources_auth=false;
    
  	$sql="SELECT sources_medi, CONCAT(sources_medi, ' (', count(sources_medi), ')') as types
        FROM genea_sources
        WHERE genea_sources.base =".$_SESSION['genea_db_id']." AND sources_medi<>'' GROUP BY sources_medi
        ORDER  BY sources_medi";
    if($g4p_result=g4p_db_query($sql))
    {
      if($g4p_liste_sources_types=g4p_db_result($g4p_result))
        array_unshift($g4p_liste_sources_types,array('source_medi'=>'', 'types'=>''));
    }
    else
      $g4p_liste_sources_types=false;
  	
    echo '
      <form class="formulaire" method="post" action="',g4p_make_url('','recherche.php','type=sour_1',0),'" name="source">
      <em>',$g4p_langue['recherche_sour_titre'],'</em><input type="text" name="g4p_titre" size="50" /><br />
      <em>',$g4p_langue['recherche_sour_texte'],'</em><input type="text" name="g4p_text" size="50" /><br />';
      		
    if($g4p_liste_sources_auth)
    {
      echo '<em>',$g4p_langue['recherche_sour_auth'],'</em> <select name="g4p_authors">';
      foreach($g4p_liste_sources_auth as $a_authors)
        echo '<option value="',$a_authors['sources_auth'],'">',$a_authors['authors'],'</option>';
      echo '</select><br />';
    }
    else
      echo '<em>',$g4p_langue['recherche_sour_auth'],'</em> aucune donnée<br />';
      
    if($g4p_liste_sources_types)
    {
      echo '<em>',$g4p_langue['recherche_sour_type'],'</em> <select name="g4p_type">';
      foreach($g4p_liste_sources_types as $a_types)
        echo '<option value="',$a_types['sources_medi'],'">',$a_types['types'],'</option>';
      echo '</select><br />';
    }
    else
      echo '<em>',$g4p_langue['recherche_sour_type'],'</em> aucune donnée<br />';

    echo '
      <em>',$g4p_langue['recherche_sour_ref'],'</em><input type="text" name="g4p_caln" size="50" /><br />
      <em>',$g4p_langue['recherche_sour_page'],'</em><input type="text" name="g4p_page" size="50" /><br />
      ',$g4p_langue['recherche_sour_parent'],'<select name="g4p_lien">
        <option value="orphelines">',$g4p_langue['recherche_sour_orphelines'],'</option>
        <option value="tous">',$g4p_langue['recherche_sour_tous'],'</option>
        <option value="indi">',$g4p_langue['recherche_sour_indi'],'</option>
        <option value="even">',$g4p_langue['recherche_sour_event'],'</option>
        <option value="familles">',$g4p_langue['recherche_sour_famile'],'</option></select><br />';
    if(isset($_GET['g4p_referer']) and isset($_GET['g4p_champ']))
    {
      echo '<input name="g4p_referer" type="hidden" value="',rawurlencode($_GET['g4p_referer']),'" />
      <input name="g4p_champ" type="hidden" value="',$_GET['g4p_champ'],'" />';
    }
    echo '<input type="submit" value="',$g4p_langue['recherche_sour_rechercher'],'" /></form>';
  }
  else
    echo $g4p_langue['acces_non_autorise'];
  break;

  case 'sour_1':
  If ($_SESSION['permission']->permission[_PERM_SOURCE_])
  {
    switch($_POST['g4p_lien'])
    {
      case 'tous':
      $sql="SELECT sources_id, sources_title, sources_auth, sources_caln, sources_page, sources_medi, sources_text, sources_chan
        FROM genea_sources
        WHERE genea_sources.base =".$_SESSION['genea_db_id']." AND
        sources_text LIKE  '%".mysql_escape_string($_POST['g4p_text'])."%' AND
        sources_title LIKE  '%".mysql_escape_string($_POST['g4p_titre'])."%' AND
        sources_auth LIKE  '%".mysql_escape_string($_POST['g4p_authors'])."%' AND
        sources_medi LIKE  '%".mysql_escape_string($_POST['g4p_type'])."%' AND
        sources_caln LIKE  '%".mysql_escape_string($_POST['g4p_caln'])."%' AND
        sources_page LIKE  '%".mysql_escape_string($_POST['g4p_page'])."%'
        ORDER  BY sources_id";
      if($g4p_result=g4p_db_query($sql))
      {
        if($g4p_result=g4p_db_result($g4p_result))
        {
          if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))        
            $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']));
            
          echo '<em>',sprintf($g4p_langue['recherche_indi_result'],count($g4p_result)),'</em><br /><br />';            
            
          foreach($g4p_result as $g4p_a_note)
          {
            echo '<hr/><div><em>',$g4p_langue['recherche_sour_num_sour'],'<b>',$g4p_a_note['sources_id'],'</b> <br />dernière modification : ',g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_a_note['sources_chan']));
            if(!empty($_POST['g4p_referer']) and  !empty($_POST['g4p_champ']))
              echo ' <b><a href="',g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],$_POST['g4p_referer'][2].'&'.$_POST['g4p_champ'].'='.$g4p_a_note['sources_id'],0),'">',$g4p_langue['recherche_sour_select_sour'],'</a></b>';            
            
            echo '</em><br />';
            echo '<em>',$g4p_langue['recherche_sour_titre'],'</em>',$g4p_a_note['sources_title'],'<br />';
            echo '<em>',$g4p_langue['recherche_sour_auth'],'</em>',$g4p_a_note['sources_auth'],'<br />';
            echo '<em>',$g4p_langue['recherche_sour_ref'],'</em>',$g4p_a_note['sources_caln'],'<br />';
            echo '<em>',$g4p_langue['recherche_sour_page'],'</em>',$g4p_a_note['sources_page'],'<br />';
            echo '<em>',$g4p_langue['recherche_sour_type'],'</em>',$g4p_a_note['sources_medi'],'<br />';
            echo '<em>',$g4p_langue['recherche_sour_texte'],'</em><p>',nl2br(trim($g4p_a_note['sources_text'])),'</p></div>';
          }
        }
      }
      break;

      case 'orphelines':
      $sql="SELECT genea_sources.sources_id as sources_id, sources_title, sources_auth, sources_caln, sources_page, sources_medi, sources_text, sources_chan
        FROM genea_sources
        LEFT JOIN rel_indi_sources
        ON ( genea_sources.sources_id=rel_indi_sources.sources_id )
        LEFT  JOIN rel_events_sources
        ON ( genea_sources.sources_id=rel_events_sources.sources_id )
        LEFT  JOIN rel_familles_sources
        ON ( genea_sources.sources_id=rel_familles_sources.sources_id )
        LEFT  JOIN rel_sources_multimedia
        ON ( genea_sources.sources_id=rel_sources_multimedia.sources_id )
        WHERE genea_sources.base =".$_SESSION['genea_db_id']." AND rel_indi_sources.indi_id IS NULL
        AND rel_events_sources.events_id IS NULL AND rel_familles_sources.familles_id IS NULL AND rel_sources_multimedia.multimedia_id IS NULL
        ORDER  BY sources_id LIMIT 200";
      if($g4p_result=g4p_db_query($sql))
      {
        if($g4p_result=g4p_db_result($g4p_result))
        {
          if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
            $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']));
            
          echo '<em>',sprintf($g4p_langue['recherche_indi_result'],count($g4p_result)),'</em><br /><br />';
            
          foreach($g4p_result as $g4p_a_note)
          {
            echo '<hr/><div><em>',$g4p_langue['recherche_sour_num_sour'],'<b>',$g4p_a_note['sources_id'],'</b>
            <a href="'.g4p_make_url('admin','exec.php','g4p_opt=suppr_source2&g4p_id='.$g4p_a_note['sources_id'],0),'"  onclick=" return confirme(this, \'',$g4p_langue['menu_sppr_confirme'],'\')" class="admin">',$g4p_langue['Supprimer'],'</a>
             <br />',$g4p_langue['recherche_sour_chan'],g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_a_note['sources_chan'])),'
            </em><br />';
            echo '<em>',$g4p_langue['recherche_sour_titre'],'</em>',$g4p_a_note['sources_title'],'<br />';
            echo '<em>',$g4p_langue['recherche_sour_auth'],'</em>',$g4p_a_note['sources_auth'],'<br />';
            echo '<em>',$g4p_langue['recherche_sour_ref'],'</em>',$g4p_a_note['sources_caln'],'<br />';
            echo '<em>',$g4p_langue['recherche_sour_page'],'</em>',$g4p_a_note['sources_page'],'<br />';
            echo '<em>',$g4p_langue['recherche_sour_type'],'</em>',$g4p_a_note['sources_medi'],'<br />';
            if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
              echo '<b><a href="',g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],$_POST['g4p_referer'][2].'&'.$_POST['g4p_champ'].'='.$g4p_a_note['sources_id'],0),'">',$g4p_langue['recherche_sour_select_sour'],'</a></b>';
            echo '<em>',$g4p_langue['recherche_sour_texte'],'</em><p>',nl2br(trim($g4p_a_note['sources_text'])),'</p></div>';

          }
        }
      }
      break;

      case 'indi':
      $sql="SELECT genea_individuals.indi_id as indi_id, indi_nom, indi_prenom, genea_sources.sources_id as sources_id, sources_text, sources_chan, sources_title, sources_auth, sources_caln, sources_page, sources_medi 
        FROM genea_sources
        LEFT  JOIN rel_indi_sources
        USING ( sources_id )
        LEFT  JOIN genea_individuals
        USING ( indi_id )
        WHERE genea_sources.base =".$_SESSION['genea_db_id']." AND genea_individuals.indi_id IS NOT NULL AND
        sources_text LIKE  '%".mysql_escape_string($_POST['g4p_text'])."%' AND
        sources_title LIKE  '%".mysql_escape_string($_POST['g4p_titre'])."%' AND
        sources_auth LIKE  '%".mysql_escape_string($_POST['g4p_authors'])."%' AND
        sources_medi LIKE  '%".mysql_escape_string($_POST['g4p_type'])."%' AND
        sources_caln LIKE  '%".mysql_escape_string($_POST['g4p_caln'])."%' AND
        sources_page LIKE  '%".mysql_escape_string($_POST['g4p_page'])."%'
        ORDER  BY sources_id LIMIT 200";
      if($g4p_result=g4p_db_query($sql))
      {
        if($g4p_result=g4p_db_result($g4p_result))
        {
          $g4p_avant=0;
          echo '<em>',sprintf($g4p_langue['recherche_indi_result'],count($g4p_result)),'</em><br /><br />';
          
          foreach($g4p_result as $g4p_a_note)
          {
            if($g4p_avant!=$g4p_a_note['sources_id'])
            {
              echo '<hr/><div><em>',$g4p_langue['recherche_sour_num_sour'],'<b>',$g4p_a_note['sources_id'],'</b>
               <br />',$g4p_langue['recherche_sour_chan'],g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_a_note['sources_chan'])),'
              </em><br />';
              echo '<em>',$g4p_langue['recherche_sour_titre'],'</em>',$g4p_a_note['sources_title'],'<br />';
              echo '<em>',$g4p_langue['recherche_sour_auth'],'</em>',$g4p_a_note['sources_auth'],'<br />';
              echo '<em>',$g4p_langue['recherche_sour_ref'],'</em>',$g4p_a_note['sources_caln'],'<br />';
              echo '<em>',$g4p_langue['recherche_sour_page'],'</em>',$g4p_a_note['sources_page'],'<br />';
              echo '<em>',$g4p_langue['recherche_sour_type'],'</em>',$g4p_a_note['sources_medi'],'<br />';
            if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
              echo '<b><a href="',g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],$_POST['g4p_referer'][2].'&'.$_POST['g4p_champ'].'='.$g4p_a_note['sources_id'],0),'">',$g4p_langue['recherche_sour_select_sour'],'</a></b>';
            echo '<em>',$g4p_langue['recherche_sour_texte'],'</em><p>',nl2br(trim($g4p_a_note['sources_text'])),'</p></div>';
            }
            
            echo $g4p_langue['recherche_sour_sour_de'],'<a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&id_pers='.$g4p_a_note['indi_id'],'fiche-'.g4p_prepare_varurl($g4p_a_note['indi_nom']).'-'.g4p_prepare_varurl($g4p_a_note['indi_prenom']).'-'.$g4p_a_note['indi_id']),'">',$g4p_a_note['indi_nom'],' ',$g4p_a_note['indi_prenom'],'</a><br />';
            $g4p_avant=$g4p_a_note['sources_id'];
          }
        }
      }
      break;

      case 'even':
      $sql="SELECT genea_events.id as events_id, genea_events.type as type, genea_events.date_event as date_event,
            genea_sources.sources_id as sources_id, sources_text, sources_chan, place_ville, sources_title, sources_caln, sources_medi, sources_auth, sources_page, ";
      $sql.="genea_place.place_id as place_id
        FROM genea_sources
        LEFT  JOIN rel_events_sources
        USING ( sources_id )
        LEFT  JOIN genea_events ON ( rel_events_sources.events_id=genea_events.id )
        LEFT JOIN genea_place ON ( genea_place.place_id=genea_events.place_id )
        WHERE genea_sources.base =".$_SESSION['genea_db_id']." AND genea_events.id IS NOT NULL AND
        sources_text LIKE  '%".mysql_escape_string($_POST['g4p_text'])."%' AND
        sources_title LIKE  '%".mysql_escape_string($_POST['g4p_titre'])."%' AND
        sources_auth LIKE  '%".mysql_escape_string($_POST['g4p_authors'])."%' AND
        sources_medi LIKE  '%".mysql_escape_string($_POST['g4p_type'])."%' AND
        sources_caln LIKE  '%".mysql_escape_string($_POST['g4p_caln'])."%' AND
        sources_page LIKE  '%".mysql_escape_string($_POST['g4p_page'])."%'
        ORDER  BY sources_id LIMIT 200";
      if($g4p_result=g4p_db_query($sql))
      {
        if($g4p_result=g4p_db_result($g4p_result))
        {
          $g4p_event_chaine='';
          foreach($g4p_result as $val)
            $g4p_event_chaine.=$val['events_id'].',';
          echo '<em>',sprintf($g4p_langue['recherche_indi_result'],count($g4p_result)),'</em><br /><br />';
          
          //on récupère les ids des familles et des indi
          $sql="SELECT genea_individuals.indi_id, events_id FROM genea_individuals
            LEFT JOIN rel_indi_event USING(indi_id)
            WHERE events_id IN (".substr($g4p_event_chaine,0,-1).")";
          if($g4p_indi=g4p_db_query($sql))
            $g4p_indi=g4p_db_result($g4p_indi,'events_id');
        
          $sql="SELECT genea_familles.familles_id, events_id, familles_husb, familles_wife FROM genea_familles
            LEFT JOIN rel_familles_event USING(familles_id)
            WHERE events_id IN (".substr($g4p_event_chaine,0,-1).")";
          if($g4p_familles=g4p_db_query($sql))
            $g4p_familles=g4p_db_result($g4p_familles,'events_id');

          if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
            $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']));
          $g4p_avant=0;
          foreach($g4p_result as $g4p_a_note)
          {
            if($g4p_avant!=$g4p_a_note['sources_id'])
            {
              echo '<hr/><div><em>',$g4p_langue['recherche_sour_num_sour'],'<b>',$g4p_a_note['sources_id'],'</b>
               <br />',$g4p_langue['recherche_sour_chan'],g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_a_note['sources_chan'])),'
              </em><br />';
              echo '<em>',$g4p_langue['recherche_sour_titre'],'</em>',$g4p_a_note['sources_title'],'<br />';
              echo '<em>',$g4p_langue['recherche_sour_auth'],'</em>',$g4p_a_note['sources_auth'],'<br />';
              echo '<em>',$g4p_langue['recherche_sour_ref'],'</em>',$g4p_a_note['sources_caln'],'<br />';
              echo '<em>',$g4p_langue['recherche_sour_page'],'</em>',$g4p_a_note['sources_page'],'<br />';
              echo '<em>',$g4p_langue['recherche_sour_type'],'</em>',$g4p_a_note['sources_medi'],'<br />';
            if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
              echo '<b><a href="',g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],$_POST['g4p_referer'][2].'&'.$_POST['g4p_champ'].'='.$g4p_a_note['sources_id'],0),'">',$g4p_langue['recherche_sour_select_sour'],'</a></b>';
            echo '<em>',$g4p_langue['recherche_sour_texte'],'</em><p>',nl2br(trim($g4p_a_note['sources_text'])),'</p></div>';
            }
            
            if(isset($g4p_familles[$g4p_a_note['events_id']]))
            {
              if(!empty($g4p_indi[$g4p_a_note['events_id']]['familles_husb']))
                $g4p_familles[$g4p_a_note['events_id']]['indi_id']=$g4p_familles[$g4p_a_note['events_id']]['familles_husb'];
              else
                $g4p_familles[$g4p_a_note['events_id']]['indi_id']=$g4p_familles[$g4p_a_note['events_id']]['familles_wife'];
              echo sprintf($g4p_langue['recherche_sour_soureven'],$g4p_tag_def[$g4p_a_note['type']],g4p_date($g4p_a_note['date_event']),$g4p_a_note['place_ville']).' (<a href="'.g4p_make_url('','detail_event.php','g4p_id=f|'.$g4p_familles[$g4p_a_note['events_id']]['familles_id'].'|'.$g4p_a_note['events_id'].'&amp;g4p_indi_id='.$g4p_familles[$g4p_a_note['events_id']]['indi_id'],0).'">voir l\'événement</a>)<br />';
            }
            else
              echo sprintf($g4p_langue['recherche_sour_soureven'],$g4p_tag_def[$g4p_a_note['type']],g4p_date($g4p_a_note['date_event']),$g4p_a_note['place_ville']).' (<a href="'.g4p_make_url('','detail_event.php','g4p_id=i|'.$g4p_indi[$g4p_a_note['events_id']]['indi_id'].'|'.$g4p_a_note['events_id'].'&amp;g4p_indi_id='.$g4p_indi[$g4p_a_note['events_id']]['indi_id'],0).'">voir l\'événement</a>)<br />';
            $g4p_avant=$g4p_a_note['sources_id'];
          }
        }
      }
      break;

      case 'familles':
      $sql="SELECT genea_familles.familles_id as familles_id, husb.indi_id as husb_id, husb.indi_nom as husb_nom, husb.indi_prenom as husb_prenom,
                 wife.indi_nom as wife_nom, wife.indi_prenom as wife_prenom, genea_sources.sources_id as sources_id, sources_text, sources_chan, sources_title, sources_caln, sources_auth, sources_medi, sources_page
        FROM genea_sources
        LEFT  JOIN rel_familles_sources
        USING ( sources_id )
        LEFT  JOIN genea_familles
        USING ( familles_id )
        LEFT  JOIN genea_individuals as husb
        ON ( husb.indi_id=genea_familles.familles_husb )
        LEFT  JOIN genea_individuals as wife
        ON ( wife.indi_id=genea_familles.familles_wife )
        WHERE genea_sources.base =".$_SESSION['genea_db_id']." AND genea_familles.familles_id IS NOT NULL AND
        sources_text LIKE  '%".mysql_escape_string($_POST['g4p_text'])."%' AND
        sources_title LIKE  '%".mysql_escape_string($_POST['g4p_titre'])."%' AND
        sources_auth LIKE  '%".mysql_escape_string($_POST['g4p_authors'])."%' AND
        sources_medi LIKE  '%".mysql_escape_string($_POST['g4p_type'])."%' AND
        sources_caln LIKE  '%".mysql_escape_string($_POST['g4p_caln'])."%' AND
        sources_page LIKE  '%".mysql_escape_string($_POST['g4p_page'])."%'
        ORDER  BY sources_id LIMIT 200";

      if($g4p_result=g4p_db_query($sql))
      {
        if($g4p_result=g4p_db_result($g4p_result))
        {
          echo '<em>',sprintf($g4p_langue['recherche_indi_result'],count($g4p_result)),'</em><br /><br />';
        
          if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
            $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']));
          $g4p_avant=0;
          foreach($g4p_result as $g4p_a_note)
          {
            if($g4p_avant!=$g4p_a_note['sources_id'])
            {
              echo '<hr/><div><em>',$g4p_langue['recherche_sour_num_sour'],'<b>',$g4p_a_note['sources_id'],'</b>
               <br />',$g4p_langue['recherche_sour_chan'],g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_a_note['sources_chan'])),'
              </em><br />';
              echo '<em>',$g4p_langue['recherche_sour_titre'],'</em>',$g4p_a_note['sources_title'],'<br />';
              echo '<em>',$g4p_langue['recherche_sour_auth'],'</em>',$g4p_a_note['sources_auth'],'<br />';
              echo '<em>',$g4p_langue['recherche_sour_ref'],'</em>',$g4p_a_note['sources_caln'],'<br />';
              echo '<em>',$g4p_langue['recherche_sour_page'],'</em>',$g4p_a_note['sources_page'],'<br />';
              echo '<em>',$g4p_langue['recherche_sour_type'],'</em>',$g4p_a_note['sources_medi'],'<br />';
              if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
                echo '<b><a href="',g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],$_POST['g4p_referer'][2].'&'.$_POST['g4p_champ'].'='.$g4p_a_note['sources_id'],0),'">',$g4p_langue['recherche_sour_select_sour'],'</a></b>';
              echo '<em>',$g4p_langue['recherche_sour_texte'],'</em><p>',nl2br(trim($g4p_a_note['sources_text'])),'</p></div>';
            }

            echo $g4p_langue['recherche_sour_sourfamille'],'<a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&id_pers='.$g4p_a_note['husb_id'],'fiche-'.g4p_prepare_varurl($g4p_a_note['husb_nom']).'-'.g4p_prepare_varurl($g4p_a_note['husb_prenom']).'-'.$g4p_a_note['husb_id']),'">',$g4p_a_note['husb_nom'],'
            ',$g4p_a_note['husb_prenom'],' - ',$g4p_a_note['wife_nom'],' ',$g4p_a_note['wife_prenom'],'</a><br />';
            $g4p_avant=$g4p_a_note['sources_id'];
          }
        }
      }
      break;
    }
  }
  break;

// fin recherche pour source

// recherche dans les objets multimedia
  case 'obje_0':
  //Utiliser . pour un caractère inconnu, et .* pour remplacer 0 ou plusieurs caractères<br />
  If ($_SESSION['permission']->permission[_PERM_MULTIMEDIA_])
  {
    echo '
      <form class="formulaire" method="post" action="',g4p_make_url('','recherche.php','type=obje_1',0),'" name="objet">
      <em>',$g4p_langue['recherche_obje_titre'],'</em><input type="text" name="g4p_titre" size="50" /><br />
      ',$g4p_langue['recherche_obje_parent'],'<select name="g4p_lien">
        <option value="orphelines">',$g4p_langue['recherche_obje_orphelines'],'</option>
        <option value="tous">',$g4p_langue['recherche_obje_tous'],'</option>
        <option value="indi">',$g4p_langue['recherche_obje_indi'],'</option>
        <option value="even">',$g4p_langue['recherche_obje_event'],'</option>
        <option value="familles">',$g4p_langue['recherche_obje_famile'],'</option></select><br />';
    if(isset($_GET['g4p_referer']) and isset($_GET['g4p_champ']))
    {
      echo '<input name="g4p_referer" type="hidden" value="',rawurlencode($_GET['g4p_referer']),'" />
      <input name="g4p_champ" type="hidden" value="',$_GET['g4p_champ'],'" />';
    }
    echo '<input type="submit" value="',$g4p_langue['recherche_obje_rechercher'],'" /></form>';
  }
  else
    echo $g4p_langue['acces_non_autorise'];
  break;

  case 'obje_1':
  If ($_SESSION['permission']->permission[_PERM_MULTIMEDIA_])
  {
    switch($_POST['g4p_lien'])
    {
      case 'tous':
      $sql="SELECT id, title, file, chan
        FROM genea_multimedia
        WHERE genea_multimedia.base =".$_SESSION['genea_db_id']." AND title
        LIKE  '%".mysql_escape_string($_POST['g4p_titre'])."%'
        ORDER  BY id LIMIT 200";
      if($g4p_result=g4p_db_query($sql))
      {
        if($g4p_result=g4p_db_result($g4p_result))
        {
          if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
            $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']));
          echo '<em>',sprintf($g4p_langue['recherche_indi_result'],count($g4p_result)),'</em><br /><br />';
          
          foreach($g4p_result as $g4p_a_note)
          {
            echo '<hr/><div><em>',$g4p_langue['recherche_obje_num_obje'],'<b>',$g4p_a_note['id'],'</b> <br />',$g4p_langue['recherche_obje_chan'],$g4p_a_note['chan'],'
            </em><br />';
            if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
              echo '<b><a href="',g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],$_POST['g4p_referer'][2].'&'.$_POST['g4p_champ'].'='.$g4p_a_note['id'],0),'">',$g4p_langue['recherche_obje_select_obje'],'</a></b>';
            echo '<p class="objets"><a href="',g4p_make_url('cache/'.$_SESSION['genea_db_nom'].'/objets',$g4p_a_note['file'],'',0).'">',nl2br($g4p_a_note['title']),'&nbsp;: ',$g4p_a_note['file'],'</a></p></div>';
          }
        }
      }
      break;

      case 'orphelines':
      $sql="SELECT id, title, file, chan
        FROM genea_multimedia
        LEFT  JOIN rel_indi_multimedia
        ON ( rel_indi_multimedia.multimedia_id=id )
        LEFT  JOIN rel_events_multimedia
        ON ( rel_events_multimedia.multimedia_id=id )
        LEFT  JOIN rel_familles_multimedia
        ON ( rel_familles_multimedia.multimedia_id=id )
        LEFT  JOIN rel_sources_multimedia
        ON ( rel_sources_multimedia.multimedia_id=id )
        WHERE genea_multimedia.base =".$_SESSION['genea_db_id']." AND rel_indi_multimedia.indi_id IS NULL
        AND rel_events_multimedia.events_id IS NULL AND rel_familles_multimedia.familles_id IS NULL AND rel_sources_multimedia.sources_id IS NULL
        ORDER  BY id LIMIT 200";
      if($g4p_result=g4p_db_query($sql))
      {
        if($g4p_result=g4p_db_result($g4p_result))
        {
          if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
            $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']));
          echo '<em>',sprintf($g4p_langue['recherche_indi_result'],count($g4p_result)),'</em><br /><br />';
          
          foreach($g4p_result as $g4p_a_note)
          {
            echo '<hr/><div><em>',$g4p_langue['recherche_obje_num_obje'],'<b>',$g4p_a_note['id'],'</b> <a href="'.g4p_make_url('admin','exec.php','g4p_opt=suppr_multimedia2&g4p_id='.$g4p_a_note['id'],0),'"  onclick=" return confirme(this, \'',$g4p_langue['menu_sppr_confirme'],'\')" class="admin">',$g4p_langue['Supprimer'],'</a>
            <br />',$g4p_langue['recherche_obje_chan'],$g4p_a_note['chan'],'
            </em><br />';
            if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
              echo '<b><a href="',g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],$_POST['g4p_referer'][2].'&'.$_POST['g4p_champ'].'='.$g4p_a_note['id'],0),'">',$g4p_langue['recherche_obje_select_obje'],'</a></b>';
            echo '<p class="objets">',$g4p_a_note['title'],'&nbsp;: ',$g4p_a_note['file'],'</p></div>';
          }
        }
      }
      break;

      case 'indi':
      $sql="SELECT genea_individuals.indi_id as indi_id, indi_nom, indi_prenom, genea_multimedia.id as id, title, file, chan
        FROM genea_multimedia
        LEFT  JOIN rel_indi_multimedia
        ON ( multimedia_id=id )
        LEFT  JOIN genea_individuals
        USING ( indi_id )
        WHERE genea_multimedia.base =".$_SESSION['genea_db_id']." AND genea_individuals.indi_id IS NOT NULL AND title
        LIKE  '%".mysql_escape_string($_POST['g4p_titre'])."%'
        ORDER  BY id LIMIT 200";
      
      if($g4p_result=g4p_db_query($sql))
      {
        if($g4p_result=g4p_db_result($g4p_result))
        {
          if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
            $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']));
          echo '<em>',sprintf($g4p_langue['recherche_indi_result'],count($g4p_result)),'</em><br /><br />';
          $g4p_avant=0;
          
          foreach($g4p_result as $g4p_a_note)
          {
            if($g4p_avant!=$g4p_a_note['id'])
            {          
              echo '<hr><div><em>',$g4p_langue['recherche_obje_num_obje'],'<b>',$g4p_a_note['id'],'</b> <br />dernière modification : ',$g4p_a_note['chan'],'</em><br />';
              if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
                echo '<b><a href="',g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],$_POST['g4p_referer'][2].'&'.$_POST['g4p_champ'].'='.$g4p_a_note['id'],0),'">',$g4p_langue['recherche_obje_select_obje'],'</a></b>';
              echo '<p class="sources">',nl2br($g4p_a_note['title']),'&nbsp;: ',$g4p_a_note['file'],'</p></div>';
            }
            
            echo $g4p_langue['recherche_obje_media_de'],'<a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&id_pers='.$g4p_a_note['indi_id'],'fiche-'.g4p_prepare_varurl($g4p_a_note['indi_nom']).'-'.g4p_prepare_varurl($g4p_a_note['indi_prenom']).'-'.$g4p_a_note['indi_id']),'">',$g4p_a_note['indi_nom'],'',$g4p_a_note['indi_prenom'],'</a><br />';
          }
        }
      }
      break;
      
      case 'even':
      $sql="SELECT genea_events.id as events_id, genea_events.type as type, genea_events.date_event as date_event,
            genea_sources.sources_id as sources_id, sources_text, sources_chan, place_ville";
      $sql.="genea_place.place_id as place_id
        FROM genea_sources
        LEFT JOIN rel_events_sources
        USING ( sources_id )
        LEFT JOIN genea_events ON ( rel_events_sources.events_id=genea_events.id )
        LEFT JOIN genea_place ON ( genea_place.place_id=genea_events.place_id )
        WHERE genea_sources.base =".$_SESSION['genea_db_id']." AND genea_events.id IS NOT NULL AND sources_text
        LIKE  '%".mysql_escape_string($_POST['g4p_titre'])."%'
        ORDER  BY sources_id";
      if($g4p_result=g4p_db_query($sql))
      {
        if($g4p_result=g4p_db_result($g4p_result))
        {
          $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']));
          foreach($g4p_result as $g4p_a_note)
          {
            echo '<hr/><div>',sprintf($g4p_langue['recherche_obje_mediaeven'],$g4p_tag_def[$g4p_a_note['type']],
            g4p_date($g4p_a_note['date_event']),$g4p_a_note['place_ville']),'<br /><em>Source numéro : <b>',$g4p_a_note['sources_id'],
            '</b> <br />dernière modification : ',$g4p_a_note['sources_chan'],'</em><br />';
            if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
              echo '<b><a href="',g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],$_POST['g4p_referer'][2].'&'.$_POST['g4p_champ'].'='.$g4p_a_note['id'],0),'">',$g4p_langue['recherche_obje_select_obje'],'</a></b>';
            echo '<p class="sources">',nl2br($g4p_a_note['sources_text']),'</p></div>';
          }
        }
      }
      break;

      case 'familles':
      $sql="SELECT genea_familles.familles_id as familles_id, husb.indi_id as husb_id, husb.indi_nom as husb_nom, husb.indi_prenom as husb_prenom,
                 wife.indi_nom as wife_nom, wife.indi_prenom as wife_prenom, genea_sources.sources_id as sources_id, sources_text, sources_chan
        FROM genea_sources
        LEFT  JOIN rel_familles_sources
        USING ( sources_id )
        LEFT  JOIN genea_familles
        USING ( familles_id )
        LEFT  JOIN genea_individuals as husb
        ON ( husb.indi_id=genea_familles.familles_husb )
        LEFT  JOIN genea_individuals as wife
        ON ( wife.indi_id=genea_familles.familles_wife )
        WHERE genea_sources.base =".$_SESSION['genea_db_id']." AND genea_familles.familles_id IS NOT NULL AND sources_text
        LIKE  '%".mysql_escape_string($_POST['g4p_text'])."%'
        ORDER  BY sources_id";
      if($g4p_result=g4p_db_query($sql))
      {
        if($g4p_result=g4p_db_result($g4p_result))
        {
          $_POST['g4p_referer']=explode('|',rawurldecode($_POST['g4p_referer']));
          foreach($g4p_result as $g4p_a_note)
          {
            echo '<hr/><div>',$g4p_langue['recherche_obje_mediafamille'],'<a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&id_pers='.$g4p_a_note['husb_id'],'fiche-'.g4p_prepare_var_url($g4p_a_note['husb_nom']).'-'.g4p_prepare_var_url($g4p_a_note['husb_prenom']).'-'.$g4p_a_note['husb_id']),'">
              ',$g4p_a_note['husb_nom'],' ',$g4p_a_note['husb_prenom'],' - ',$g4p_a_note['wife_nom'],' ',$g4p_a_note['wife_prenom'],'</a>
              <br /><em>',$g4p_langue['recherche_obje_num_obje'],'<b>',$g4p_a_note['sources_id'],'</b> <br />',$g4p_langue['recherche_obje_chan'],$g4p_a_note['sources_chan'],'
              </em><br />';
            if(!empty($_POST['g4p_referer']) and !empty($_POST['g4p_champ']))
              echo '<b><a href="',g4p_make_url($_POST['g4p_referer'][0],$_POST['g4p_referer'][1],$_POST['g4p_referer'][2].'&'.$_POST['g4p_champ'].'='.$g4p_a_note['id'],0),'">',$g4p_langue['recherche_obje_select_obje'],'</a></b>';
            echo '<p class="sources">',nl2br($g4p_a_note['sources_text']),'</p></div>';
          }
        }
      }
      break;
    }
  }
  break;

// fin recherche pour les medias


  case 'even_0':
  //Utiliser . pour un caractère inconnu, et .* pour remplacer 0 ou plusieurs caractères<br />
  echo '
    <form class="formulaire" method="post" action="',g4p_make_url('','recherche.php','type=even_1',0),'" name="even">';
  echo $g4p_langue['recherche_even_type'],'<select name="g4p_type"><option value="0"></option>';
  reset($g4p_tag_def);
  while(list($g4p_key, $g4p_value)=each($g4p_tag_def))
  {
    echo '<option value="',$g4p_key,'">',$g4p_value,'</option>';
  }
  echo '</select><br />';
  echo $g4p_langue['recherche_even_descr'],'<input type="text" size="50" name="g4p_desc" value="" /><br />';
  /* TODO à ajouter
   * echo $g4p_langue['recherche_even_year'],'<input type="text" size="4" name="g4p_date" value="" /><br />';
   */

  echo $g4p_langue['recherche_even_lieu'];
  echo '<select name="g4p_lieu" style="width:auto"><option value=""></option>';
  $sql="SELECT place_id, place_ville, place_departement, place_region, place_pays FROM genea_place WHERE base=".$_SESSION['genea_db_id']." ORDER BY place_ville";
  $g4p_result_req=g4p_db_query($sql);      
  if($g4p_result=g4p_db_result($g4p_result_req))
  {
    foreach($g4p_result as $a_result)
      echo '<option value="',$a_result['place_id'],'">',$a_result['place_ville'],' ',$a_result['place_departement'],' ',$a_result['place_region'],' ',$a_result['place_pays'],'</option>';
  }
  echo '</select><br />';

  echo $g4p_langue['recherche_even_cause'],'<input type="text" size="50" name="g4p_cause" /><br />';
  echo '<input type="submit" value="',$g4p_langue['recherche_even_rechercher'],'" /></form>';
  break;

  case 'even_1':
//  if ($_SESSION['permission']->permission[_PERM_MULTIMEDIA_])
//  {
    $sql="SELECT id, type, description, date_event, place_id
      FROM genea_events
      WHERE genea_events.base =".$_SESSION['genea_db_id'];
    if(!empty($_POST['g4p_type'])) $sql.=" AND type LIKE '".$_POST['g4p_type']."'";
    if(!empty($_POST['g4p_desc'])) $sql.=" AND description LIKE '%".$_POST['g4p_desc']."%'";
    if(!empty($_POST['g4p_lieu'])) $sql.=" AND place_id=".$_POST['g4p_lieu'];
    if(!empty($_POST['g4p_cause'])) $sql.=" AND cause LIKE '%".$_POST['g4p_cause']."%'";
    $sql.= " ORDER  BY id LIMIT 200";
    if($g4p_result=g4p_db_query($sql))
    {
      if($g4p_result=g4p_db_result($g4p_result))
      {
        foreach($g4p_result as $g4p_a_note)
        {
          $lieu=new g4p_lieu($g4p_a_note['place_id']);
          echo '<hr/><div><em>Evènement numéro : </em><b>',$g4p_a_note['id'],'</b><br />';
          echo '<p
          class="evenements">',nl2br($g4p_a_note['type']),'&nbsp;:',g4p_date($g4p_a_note['date_event']),' ('.$lieu->toCompactString().')</p></div>';

        }
      }
    }
//  }
  break;
}
echo '</div>';


require_once($g4p_chemin.'pied_de_page.php');
?>
