<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *\
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

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *\
 *          créations et modifications des enregistrements                  *
 *                                                                          *
 * dernière mise à jour : 07/2004                                           *
 * En cas de problème : http://www.parois.net                               *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


$g4p_chemin='../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

if(!isset($_SESSION['message']))
  $_SESSION['message']='';

If(!isset($_GET['g4p_opt']))
{
  exit('erreur critique');
}
else
{
  switch($_GET['g4p_opt'])
  {
    case 'modif_fich':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      $sql="UPDATE genea_individuals SET indi_nom='".mysql_escape_string($_POST['nom'])."', indi_prenom='".
            mysql_escape_string($_POST['prenom'])."', indi_sexe='".mysql_escape_string($_POST['sexe'])."', 
            indi_timestamp=NOW(), indi_npfx='".mysql_escape_string($_POST['npfx'])."', indi_givn='".
            mysql_escape_string($_POST['givn'])."', indi_nick='".
            mysql_escape_string($_POST['nick'])."', indi_spfx='".mysql_escape_string($_POST['spfx'])."', indi_nsfx='".
            mysql_escape_string($_POST['nsfx'])."' WHERE indi_id='".$_POST['g4p_id']."'";
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message'].=$g4p_langue['message_req_succes'];
      else
        $_SESSION['message'].=$g4p_langue['message_req_echec'];

      $g4p_indi=g4p_destroy_cache($_POST['g4p_id']);
      g4p_update_agregat_noms($_POST['nom']);
      header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_fich&id_pers='.$_POST['g4p_id'],0,0));
    }
    break;

    case 'modif_fams':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      $g4p_modif=0;
      $sql="UPDATE genea_familles SET ";
      if(!empty($_POST['mari2']))
      {
        $g4p_modif=1;
        $sql.="familles_husb=".mysql_escape_string($_POST['mari2'])." ";
      }
      elseif($_POST['mari'])
      {
        $g4p_modif=1;
        $sql.="familles_husb=".mysql_escape_string($_POST['mari'])." ";
      }
      if(($_POST['mari'] or $_POST['mari2']) and ($_POST['femme'] or $_POST['femme2']))
        $sql.=", ";
      if(!empty($_POST['femme2']))
      {
        $g4p_modif=1;
        $sql.="familles_wife=".mysql_escape_string($_POST['femme2'])." ";
      }
      elseif($_POST['femme'])
      {
        $g4p_modif=1;
        $sql.="familles_wife=".mysql_escape_string($_POST['femme'])." ";
      }
      if($g4p_modif)
      {
        $sql.=", familles_chan=NOW() WHERE familles_id=".$_POST['g4p_id'];
        if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message'].=$g4p_langue['message_req_succes'];
      else
        $_SESSION['message'].=$g4p_langue['message_req_echec'];

        $g4p_save_id=$g4p_indi->indi_id;
        $g4p_save_id2=$g4p_indi->familles[$_POST['g4p_id']]->conjoint->indi_id;
        unset($g4p_indi);
        g4p_destroy_cache($g4p_save_id2);
        g4p_destroy_cache($g4p_save_id);
        $g4p_indi=g4p_load_indi_infos($g4p_save_id);
      }
      header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_fams&g4p_id='.$g4p_indi->indi_id,0,0));
    }
    break;

    case 'suppr_child':
    if($_SESSION['permission']->permission[_PERM_SUPPR_FILES_])
    {
      if(isset($_GET['fam_id']) and isset($_GET['g4p_id']))
      {
        $sql="DELETE FROM rel_familles_indi WHERE familles_id=".$_GET['fam_id']." AND indi_id=".$_GET['g4p_id'];
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message'].=$g4p_langue['message_req_succes'];
        else
          $_SESSION['message'].=$g4p_langue['message_req_echec'];
        $sql="UPDATE genea_familles SET familles_chan=NOW() WHERE familles_id=".$_GET['fam_id'];
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message'].=$g4p_langue['message_req_succes'];
        else
          $_SESSION['message'].=$g4p_langue['message_req_echec'];

        g4p_destroy_cache($g4p_indi->familles[$_GET['fam_id']]->conjoint->indi_id);
        g4p_destroy_cache($_GET['g4p_id']);
        $g4p_indi=g4p_destroy_cache();
        header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_fams&g4p_id='.$_GET['fam_id'],0,0));
      }
    }
    break;

//nouvel enfant
    case 'new_child':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      if(!empty($_POST['enfant']) or !empty($_POST['enfant2']))
      {
        if(!empty($_POST['enfant2']))
          $_POST['enfant']=$_POST['enfant2'];
          
        $sql="INSERT INTO rel_familles_indi (familles_id, indi_id, rela_type, base) VALUES
          (".$_POST['fam_id'].", ".$_POST['enfant'].", '".$_POST['rela_type']."',".$_SESSION['genea_db_id'].")";
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message'].=$g4p_langue['message_req_succes'];
        else
          $_SESSION['message'].=$g4p_langue['message_req_echec'];

        $sql="UPDATE genea_familles SET familles_chan=NOW() WHERE familles_id=".$_POST['fam_id'];
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message'].=$g4p_langue['message_req_succes'];
        else
          $_SESSION['message'].=$g4p_langue['message_req_echec'];
      }
      g4p_destroy_cache($g4p_indi->familles[$_POST['fam_id']]->conjoint->indi_id);
      g4p_destroy_cache($_POST['enfant']);
      $g4p_indi=g4p_destroy_cache();
      header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_fams&g4p_id='.$_POST['fam_id'],0,0));
    }
    break;

// modification d'une note
    case 'modif_note':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      $sql="UPDATE genea_notes SET notes_text='".mysql_escape_string($_POST['g4p_note'])."', notes_chan=NOW() WHERE notes_id=".$_POST['g4p_id'];
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message'].=$g4p_langue['message_req_succes'];
      else
        $_SESSION['message'].=$g4p_langue['message_req_echec'];

      $g4p_liste_indi=g4p_cherche_dependances($_POST['g4p_id'],'notes');
      foreach($g4p_liste_indi as $g4p_tmp)
        g4p_destroy_cache($g4p_tmp);
      $g4p_indi=g4p_load_indi_infos($g4p_indi->indi_id);
      header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
    }
    break;

// ajout d'une note
    case 'ajout_note':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_] and isset($_POST['g4p_id']) AND isset($_POST['g4p_type']))
    {
      $sql="INSERT INTO genea_notes (notes_text, notes_chan, base) VALUES ('".mysql_escape_string(trim($_POST['g4p_note']))."' , NOW(), ".$_SESSION['genea_db_id'].")";
      if($g4p_mysqli->g4p_query($sql))
      {
        $_SESSION['message'].=$g4p_langue['message_note_enr'];
        switch($_POST['g4p_type'])
        {
          case 'familles':
          $sql="INSERT INTO rel_familles_notes (familles_id, notes_id, base) VALUES (".$_POST['g4p_id'].", LAST_INSERT_ID(),  ".$_SESSION['genea_db_id'].")";
          if($g4p_mysqli->g4p_query($sql))
            $_SESSION['message'].=$g4p_langue['message_lien_note_succes'];
          break;

          case 'events':
          $sql="INSERT INTO rel_events_notes (events_id, notes_id, base) VALUES (".$_POST['g4p_id'].", LAST_INSERT_ID(),  ".$_SESSION['genea_db_id'].")";
          if($g4p_mysqli->g4p_query($sql))
            $_SESSION['message'].=$g4p_langue['message_lien_note_succes'];
          break;

          case 'indi':
          $sql="INSERT INTO rel_indi_notes (indi_id, notes_id, base) VALUES (".$_POST['g4p_id'].", LAST_INSERT_ID(),  ".$_SESSION['genea_db_id'].")";
          if($g4p_mysqli->g4p_query($sql))
            $_SESSION['message'].=$g4p_langue['message_lien_note_succes'];
          break;
        }
      }
      else
        $_SESSION['message'].=$g4p_langue['message_req_echec'];
      if(isset($g4p_indi->familles[$_POST['g4p_id']]->conjoint->indi_id))
        g4p_destroy_cache($g4p_indi->familles[$_POST['g4p_id']]->conjoint->indi_id);
      $g4p_indi=g4p_destroy_cache();
      header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
    }
    break;

////////////////////////////////////
//////  Lier une note
////////////////////////////////////
  case 'link_note':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_] and isset($_POST['g4p_id']) AND isset($_POST['g4p_type']))
    {
      switch($_POST['g4p_type'])
      {
        case 'indi':
        $sql="INSERT INTO rel_indi_notes (indi_id, notes_id, base) VALUES (".$_POST['g4p_id'].", ".$_POST['g4p_id_note'].", ".$_SESSION['genea_db_id'].")";
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message'].=$g4p_langue['message_lien_note_succes'];
        break;

        case 'events':
        $sql="INSERT INTO rel_events_notes (events_id, notes_id, base) VALUES (".$_POST['g4p_id'].", ".$_POST['g4p_id_note'].", ".$_SESSION['genea_db_id'].")";
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message'].=$g4p_langue['message_lien_note_succes'];
        break;

        case 'familles':
        $sql="INSERT INTO rel_familles_notes (familles_id, notes_id, base) VALUES (".$_POST['g4p_id'].", ".$_POST['g4p_id_note'].", ".$_SESSION['genea_db_id'].")";
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message'].=$g4p_langue['message_lien_note_succes'];
        break;
      }
    }
    if(isset($g4p_indi->familles[$_POST['g4p_id']]->conjoint->indi_id))
      g4p_destroy_cache($g4p_indi->familles[$_POST['g4p_id']]->conjoint->indi_id);
    $g4p_indi=g4p_destroy_cache();
    header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
  break;

////////////////////////////////////
//////  Lier une source
////////////////////////////////////
  case 'link_source':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_] and isset($_POST['g4p_id']) AND isset($_POST['g4p_type']))
    {
      switch($_POST['g4p_type'])
      {
        case 'indi':
        $sql="INSERT INTO rel_indi_sources (indi_id, sources_id, base) VALUES (".$_POST['g4p_id'].", ".$_POST['g4p_id_source'].", ".$_SESSION['genea_db_id'].")";
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message'].=$g4p_langue['message_lien_source_succes'];
        break;

        case 'events':
        $sql="INSERT INTO rel_events_sources (events_id, sources_id, base) VALUES (".$_POST['g4p_id'].", ".$_POST['g4p_id_source'].", ".$_SESSION['genea_db_id'].")";
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message'].=$g4p_langue['message_lien_source_succes'];
        break;

        case 'familles':
        $sql="INSERT INTO rel_familles_sources (familles_id, sources_id, base) VALUES (".$_POST['g4p_id'].", ".$_POST['g4p_id_source'].", ".$_SESSION['genea_db_id'].")";
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message'].=$g4p_langue['message_lien_source_succes'];
        break;
      }
    }
    if(isset($g4p_indi->familles[$_POST['g4p_id']]->conjoint->indi_id))
      g4p_destroy_cache($g4p_indi->familles[$_POST['g4p_id']]->conjoint->indi_id);
    $g4p_indi=g4p_destroy_cache();
    header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
  break;


// Ajout d'une source
    case 'ajout_source':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_] and isset($_POST['g4p_id']) AND isset($_POST['g4p_type']))
    {
      $sql="INSERT INTO genea_sources (sources_title, sources_publ, sources_text, sources_auth, sources_page, sources_caln, sources_medi,
        sources_chan, base, repo_id)
      VALUES ('".mysql_escape_string(trim($_POST['g4p_titre']))."',' ".mysql_escape_string(trim($_POST['g4p_publ']))."','".mysql_escape_string(trim($_POST['g4p_text']))."','".
      mysql_escape_string(trim($_POST['g4p_auteur']))."','".mysql_escape_string(trim($_POST['g4p_page']))."','".mysql_escape_string(trim($_POST['g4p_caln']))."','".mysql_escape_string(trim($_POST['g4p_medi']))."'
      , NOW(),".$_SESSION['genea_db_id'].",".$_POST['g4p_repo']." )";
      if($g4p_mysqli->g4p_query($sql))
      {
        $_SESSION['message'].=$g4p_langue['message_source_enr'];
        switch($_POST['g4p_type'])
        {
          case 'familles':
          $sql="INSERT INTO rel_familles_sources (familles_id, sources_id, base) VALUES (".$_POST['g4p_id'].", LAST_INSERT_ID(),".$_SESSION['genea_db_id']." )";
          if($g4p_mysqli->g4p_query($sql))
            $_SESSION['message'].=$g4p_langue['message_lien_source_succes'];
          break;

          case 'events':
          $sql="INSERT INTO rel_events_sources (events_id, sources_id, base) VALUES (".$_POST['g4p_id'].", LAST_INSERT_ID() ,".$_SESSION['genea_db_id'].")";
          if($g4p_mysqli->g4p_query($sql))
            $_SESSION['message'].=$g4p_langue['message_lien_source_succes'];
          break;

          case 'indi':
          $sql="INSERT INTO rel_indi_sources (indi_id, sources_id, base) VALUES (".$_POST['g4p_id'].", LAST_INSERT_ID(),".$_SESSION['genea_db_id']." )";
          if($g4p_mysqli->g4p_query($sql))
            $_SESSION['message'].=$g4p_langue['message_lien_source_succes'];
          break;
        }
      }
      else
        $_SESSION['message'].='Erreur lors de la requète';
      if(isset($g4p_indi->familles[$_POST['g4p_id']]->conjoint->indi_id))
        g4p_destroy_cache($g4p_indi->familles[$_POST['g4p_id']]->conjoint->indi_id);
      $g4p_indi=g4p_destroy_cache();
      header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
    }
    break;

//modification d'une source
    case 'mod_source':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      $sql="UPDATE genea_sources SET sources_title='".mysql_escape_string(trim($_POST['g4p_titre']))
          ."', sources_publ='".mysql_escape_string(trim($_POST['g4p_publ']))."', sources_text='".mysql_escape_string(trim($_POST['g4p_text']))
          ."', sources_auth='".mysql_escape_string(trim($_POST['g4p_auteur']))."', sources_page='".mysql_escape_string(trim($_POST['g4p_page']))
          ."', sources_caln='".mysql_escape_string(trim($_POST['g4p_caln']))."', sources_medi='".mysql_escape_string(trim($_POST['g4p_medi']))
          ."', sources_chan=NOW(), repo_id=".$_POST['g4p_repo_id'].", base=".$_SESSION['genea_db_id']." WHERE sources_id=".$_POST['g4p_id'];
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message'].=$g4p_langue['message_req_succes'];
      else
        $_SESSION['message'].=$g4p_langue['message_req_echec'];

      $g4p_liste_indi=g4p_cherche_dependances($_POST['g4p_id'],'sources');
      foreach($g4p_liste_indi as $g4p_tmp)
        g4p_destroy_cache($g4p_tmp);
      $g4p_indi=g4p_load_indi_infos($g4p_indi->indi_id);
      header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
    }
    break;

//suppression d'une source
    case 'suppr_source':
    if($_SESSION['permission']->permission[_PERM_SUPPR_FILES_] and isset($_GET['g4p_id']) and isset($_GET['g4p_lien']) and isset($_GET['g4p_type']))
    {
      switch($_GET['g4p_type'])
      {
        case 'indi':
        $sql="DELETE FROM rel_indi_sources WHERE indi_id=".$_GET['g4p_lien']." AND sources_id=".$_GET['g4p_id'];
        break;

        case 'familles':
        $sql="DELETE FROM rel_familles_sources WHERE familles_id=".$_GET['g4p_lien']." AND sources_id=".$_GET['g4p_id'];
        break;

        case 'events':
        $sql="DELETE FROM rel_events_sources WHERE events_id=".$_GET['g4p_lien']." AND sources_id=".$_GET['g4p_id'];
        break;
      }
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_suppr_lien_source_succes'];
      else
        $_SESSION['message']=$g4p_langue['message_suppr_lien_source_echec'];
      if(isset($g4p_indi->familles[$_GET['g4p_lien']]->conjoint->indi_id))
        g4p_destroy_cache($g4p_indi->familles[$_GET['g4p_lien']]->conjoint->indi_id);
      $g4p_indi=g4p_destroy_cache();
      header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
    }
    break;

    case 'suppr_source2':
    if($_SESSION['permission']->permission[_PERM_SUPPR_FILES_] and isset($_GET['g4p_id']))
    {
      $sql="DELETE FROM genea_sources WHERE sources_id=".$_GET['g4p_id'];
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_suppr_source_succes'];
      else
        $_SESSION['message']=$g4p_langue['message_suppr_source_echec'];
       header('location:'.g4p_make_url('','recherche.php','type=sour_0',0));
    }
    break;

// ajout d'un évènement individuel
    case 'ajout_ievent':
    $g4p_date_gedcom='';

    if(empty($_POST['g4p_type']))
    {
        $_SESSION['message']='Veuillez définir le type d\'évènement';
        header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_event&g4p_id='.$_POST['g4p_event_id'],0,0));
        exit;
    }

    if($_POST['g4p_date_type']=='range')
    {
        //construction de la chaine gedcom
        if(!empty($_POST['date_period_deb_modif']) and !empty($_POST['date_periode_deb_annee']))
        {
            $g4p_date_gedcom.=$_POST['date_period_deb_modif'].' '.$_POST['date_periode_deb_calendrier'].' '.$_POST['date_periode_deb_jour'].' '.$_POST['date_periode_deb_mois'].' '.$_POST['date_periode_deb_annee'];
        }
        if(!empty($_POST['date_period_fin_modif']) and !empty($_POST['date_periode_fin_annee']))
        {
            $g4p_date_gedcom.=' '.$_POST['date_period_fin_modif'].' '.$_POST['date_periode_fin_calendrier'].' '.$_POST['date_periode_fin_jour'].' '.$_POST['date_periode_fin_mois'].' '.$_POST['date_periode_fin_annee'];
        }
    }
    elseif($_POST['g4p_date_type']=='date_exacte')
    {
        //construction de la chaine gedcom
        if(!empty($_POST['date_exacte_modif']) and !empty($_POST['date_exacte_annee']))
        {
            if($_POST['date_exacte_modif']=='date_exacte')
                $_POST['date_exacte_modif']='';
            $g4p_date_gedcom.=$_POST['date_exacte_modif'].' '.$_POST['date_exacte_calendrier'].' '.$_POST['date_exacte_jour'].' '.$_POST['date_exacte_mois'].' '.$_POST['date_exacte_annee'];
        }
    }
    elseif($_POST['g4p_date_type']=='gedcom')
        $g4p_date_gedcom=$_POST['date_gedcom'];
    elseif(!array_key_exists($_POST['g4p_type'],$g4p_tag_iattributs))
    {
        $_SESSION['message']='Date invalide';
        header('location:'.g4p_make_url('admin','index.php','g4p_opt=ajout_event&g4p_id='.$_POST['g4p_id'],'',0,0));
        exit;
    }

    if(empty($g4p_date_gedcom) && !array_key_exists($_POST['g4p_type'],$g4p_tag_iattributs))// J'ai un doute de l'utilité de ce petit bout
    {
        $_SESSION['message']='Date invalide';
        header('location:'.g4p_make_url('admin','index.php','g4p_opt=ajout_event&g4p_id='.$_POST['g4p_id'],'',0,0));
        exit;
    }

	//faux
    //if(!empty($_POST['g4p_date_description']))
      //$g4p_date_gedcom='INT '.$g4p_date_gedcom.'('.$_POST['g4p_date_description'].')';
      

    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
        if(!$g4p_jd=g4p_jd_extract($g4p_date_gedcom))
        {
            $g4p_jd['calendrier']='null';
            $g4p_jd['jd_count']='null';
            $g4p_jd['precision']='null';
        }
        if(empty($_POST['g4p_lieu']))
            $_POST['g4p_lieu']='null';

        if(isset($_POST['g4p_attestation']) and $_POST['g4p_attestation']=='on')
            $g4p_attestation="'Y'";
        else
            $g4p_attestation='NULL';

        if(!empty($_POST['g4p_description']))
            $_POST['g4p_description']=$_POST['g4p_description'];
        else
            $_POST['g4p_description']='';

        $sql="SET AUTOCOMMIT=0";
        $g4p_mysqli->g4p_query($sql);
        $sql="START TRANSACTION";
        $g4p_mysqli->g4p_query($sql);        
        $sql="INSERT INTO genea_events_details
            (`place_id`, `addr_id`, `events_details_descriptor`, `events_details_gedcom_date`, `events_details_age`, 
            `events_details_cause`, `events_age`, `jd_count`, `jd_precision`, `jd_calendar`, `base`, `events_details_timestamp`) 
            VALUES ('".(int)$_POST['g4p_lieu'].",".(int)$_POST['g4p_addr'].",".mysql_escape_string(trim($_POST['g4p_type']))."','".$g4p_attestation."','".$g4p_date_gedcom."',".mysql_escape_string(trim($_POST['g4p_lieu'])).",'".$_POST['g4p_age']."','".mysql_escape_string(trim($_POST['g4p_cause']))."',".$_SESSION['genea_db_id'].",".$g4p_jd['jd_count'].",'".$g4p_jd['calendrier']."',".$g4p_jd['precision'].")";
        if($g4p_tmp=$g4p_mysqli->g4p_query($sql))
        {
            $_POST['g4p_id']=explode('|',$_POST['g4p_id']);
            if($_POST['g4p_id'][0]=='i')
                $sql="INSERT INTO rel_indi_event (events_details_id, indi_id, events_tag, events_attestation) VALUES 
                    (".$g4p_tmp.", ".(int)$_POST['g4p_id'][1].",'".mysql_escape_string(trim($_POST['g4p_tag']))."',".
                    $g4p_attestation.",".$_SESSION['genea_db_id'].")";

            if($ds=$g4p_mysqli->g4p_query($sql))
            {
                $sql="COMMIT";
                $g4p_mysqli->g4p_query($sql);        
                $_SESSION['message'].=$g4p_langue['message_req_succes'];
            }
            else
                $_SESSION['message'].=$g4p_langue['message_req_echec'];
        }
        else
        {
            $sql="ROLLBACK";
            $g4p_mysqli->g4p_query($sql);                    
            $_SESSION['message'].=$g4p_langue['message_req_echec'];
        }

        $g4p_indi=g4p_destroy_cache();
        header('location:'.g4p_make_url('','detail_event.php','g4p_id='.$_POST['g4p_id'][0].'|'.$_POST['g4p_id'][1].'|'.$g4p_tmp,0,0));
    }
    break;

// modification d'un évènement
    case 'mod_event':
    $g4p_date_gedcom='';

    if(empty($_POST['g4p_type']))
    {
      $_SESSION['message']='Veuillez définir le type d\'évènement';
      header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_event&g4p_id='.$_POST['g4p_event_id'],0,0));
      exit;
    }

    if($_POST['g4p_date_type']=='range')
    {
      //construction de la chaine gedcom
      if(!empty($_POST['date_period_deb_modif']) and !empty($_POST['date_periode_deb_annee']))
      {
        $g4p_date_gedcom.=$_POST['date_period_deb_modif'].' '.$_POST['date_periode_deb_calendrier'].' '.$_POST['date_periode_deb_jour'].' '.$_POST['date_periode_deb_mois'].' '.$_POST['date_periode_deb_annee'];
      }
      if(!empty($_POST['date_period_fin_modif']) and !empty($_POST['date_periode_fin_annee']))
      {
        $g4p_date_gedcom.=' '.$_POST['date_period_fin_modif'].' '.$_POST['date_periode_fin_calendrier'].' '.$_POST['date_periode_fin_jour'].' '.$_POST['date_periode_fin_mois'].' '.$_POST['date_periode_fin_annee'];
      }
    }
    elseif($_POST['g4p_date_type']=='date_exacte')
    {
      //construction de la chaine gedcom
      if(!empty($_POST['date_exacte_modif']) and !empty($_POST['date_exacte_annee']))
      {
        if($_POST['date_exacte_modif']=='date_exacte')
          $_POST['date_exacte_modif']='';
        $g4p_date_gedcom.=$_POST['date_exacte_modif'].' '.$_POST['date_exacte_calendrier'].' '.$_POST['date_exacte_jour'].' '.$_POST['date_exacte_mois'].' '.$_POST['date_exacte_annee'];
      }
    }
    elseif($_POST['g4p_date_type']=='gedcom')
      $g4p_date_gedcom=$_POST['date_gedcom'];
    else
    {
      $_SESSION['message']='Date invalide';
      header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_event&g4p_id='.$_POST['g4p_event_id'],0,0));
      exit;
    }
/*
    if(empty($g4p_date_gedcom))
    {
      $_SESSION['message']='Date invalide';
      header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_event&g4p_id='.$_POST['g4p_event_id'],0,0));
      exit;
    }
*/
    //faux
    //if(!empty($_POST['g4p_date_description']))
      //$g4p_date_gedcom='INT '.$g4p_date_gedcom.'('.$_POST['g4p_date_description'].')';


/*echo '<pre>';
print_r($_POST);
exit;*/

    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      if(!$g4p_jd=g4p_jd_extract($g4p_date_gedcom))
      {
        $g4p_jd['calendrier']='null';
        $g4p_jd['jd_count']='null';
        $g4p_jd['precision']='null';
      }

      if(empty($_POST['g4p_lieu']))
        $_POST['g4p_lieu']='NULL';
      
      if(isset($_POST['g4p_attestation']) and $_POST['g4p_attestation']=='on')
        $g4p_attestation='Y';
      else
        $g4p_attestation='';

      if(!empty($_POST['g4p_description']))
        $_POST['g4p_description']=$_POST['g4p_description'];
      else
        $_POST['g4p_description']='';

      $sql="UPDATE genea_events SET type='".mysql_escape_string($_POST['g4p_type'])."', description='".mysql_escape_string(trim($_POST['g4p_description']))."', attestation='".$g4p_attestation."', date_event='".$g4p_date_gedcom."', place_id=".mysql_escape_string(trim($_POST['g4p_lieu']))
          .", age='".mysql_escape_string($_POST['g4p_age'])."', cause='".mysql_escape_string(trim($_POST['g4p_cause']))."', jd_count=".$g4p_jd['jd_count'].", jd_precision=".$g4p_jd['precision'].", jd_calendar='".$g4p_jd['calendrier']."'
          WHERE id=".$_POST['g4p_event_id'];
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message'].=$g4p_langue['message_req_succes'];
      else
        $_SESSION['message'].=$g4p_langue['message_req_echec'];

      $g4p_indi=g4p_destroy_cache();
      header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
	  g4p_destroy_cache($g4p_indi->familles[$_GET['g4p_id'][0]]->conjoint->indi_id);      
    }
    break;

// suppression d'un évènement
    case 'del_event':
    if($_SESSION['permission']->permission[_PERM_SUPPR_FILES_])
    {
      $g4p_error=0;

      $sql="DELETE FROM genea_events WHERE id=".$_GET['g4p_id'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=1;
      
      if($g4p_error)
        $_SESSION['message'].=$g4p_langue['message_suppr_event_echec'];

      $g4p_indi=g4p_destroy_cache();
      header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
      foreach($g4p_indi->familles as $g4p_a_famille)
        g4p_destroy_cache($g4p_a_famille->conjoint->indi_id);
    }
    break;


/*************************
* suppression d'un individu
**************************/
    case 'suppr_fiche':

// Supprimer tous les liens (évènements, sources, notes...) mais pas les sources, notes et medias
    if($_SESSION['permission']->permission[_PERM_SUPPR_FILES_] and isset($g4p_indi))
    {
      //suppression du cache des parents
      $sql="SELECT familles_husb, familles_wife
        FROM rel_familles_indi
        LEFT JOIN genea_familles
        USING (familles_id)
        WHERE indi_id=".$g4p_indi->indi_id;
      $g4p_result_req=$g4p_mysqli->g4p_query($sql);
      if($g4p_result2=$g4p_mysqli->g4p_result($g4p_result_req))
      {
        foreach($g4p_result2 as $g4p_a_result2)
        {
          g4p_destroy_cache($g4p_a_result2['familles_wife']);
          g4p_destroy_cache($g4p_a_result2['familles_husb']);
        }
      }

      $sql="DELETE FROM genea_individuals WHERE indi_id=".$g4p_indi->indi_id;
      $g4p_mysqli->g4p_query($sql);
      
      //supression des évènements
      $g4p_liste_event='';
      $sql="SELECT id FROM genea_events LEFT JOIN rel_indi_event ON id=rel_indi_event.events_id LEFT JOIN rel_familles_event ON id=rel_familles_event.events_id WHERE rel_indi_event.events_id IS NULL AND rel_familles_event.events_id IS NULL";
      $g4p_result_req=$g4p_mysqli->g4p_query($sql);      
      if($g4p_result=$g4p_mysqli->g4p_result($g4p_result_req))
        foreach($g4p_result as $g4p_a_result)
          $g4p_liste_event.=$g4p_a_result['id'].',';
      if($g4p_liste_event)
      {
        $sql="DELETE FROM genea_events WHERE id IN (".substr($g4p_liste_event,0,-1).")";
        $g4p_mysqli->g4p_query($sql);      
      }        

	  unset($g4p_indi->indi_id);
      $g4p_indi=false;
      header('location:'.g4p_make_url('','index.php','g4p_action=liste_patronyme',0));
    }
    break;

    // suppression une famille
    case 'suppr_fams':
    if($_SESSION['permission']->permission[_PERM_SUPPR_FILES_] and isset($_GET['g4p_id']))
    {
      $sql="DELETE FROM genea_familles WHERE familles_id=".$_GET['g4p_id'];
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_suppr_famille_succes'];
      else
        $_SESSION['message']=$g4p_langue['message_suppr_famille_echec'];

      if(isset($g4p_indi->familles[$_GET['g4p_id']]->conjoint->indi_id))
        g4p_destroy_cache($g4p_indi->familles[$_GET['g4p_id']]->conjoint->indi_id);

      if(isset($g4p_indi->familles[$_GET['g4p_id']]->enfants))
        foreach($g4p_indi->familles[$_GET['g4p_id']]->enfants as $un_enfant)
          g4p_destroy_cache($un_enfant->indi_id);

      $g4p_indi=g4p_destroy_cache();
      header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
    }
    break;

/**********************************
*  CREATION D'UNE NOUVELLE BASE
**********************************/
    case 'new_base':
    if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
      $sql="INSERT INTO genea_infos (nom, descriptif) VALUES ('".mysql_escape_string($_POST['nom'])."','".mysql_escape_string($_POST['description'])."')";
      if($g4p_new_id=$g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_creer_base_succes'];

	  if($g4p_langue['entete_charset']=='UTF-8')
        $_POST['nom']=utf8_decode($_POST['nom']);

      if(!is_dir($g4p_chemin.'cache'))
      {
        if(!mkdir($g4p_chemin.'cache',0755))
          $_SESSION['message'].='impossible de créer le répertoire : cache<br />';
        else
          if($f=fopen($g4p_chemin.'cache/'.$_POST['nom'].'/.htaccess','w'))
          {
            fwrite($f,"<Files *.txt>\ndeny from all\n</Files>\noptions -Indexes"); 
            fclose($f);
          }
      }

      if(!mkdir($g4p_chemin.'cache/'.$_POST['nom'],0755))
        $_SESSION['message'].='impossible de créer le répertoire : cache/'.$_POST['nom'].'<br />';
      if(!mkdir($g4p_chemin.'cache/'.$_POST['nom'].'/fichiers',0755))
        $_SESSION['message'].='impossible de créer le répertoire : cache/'.$_POST['nom'].'/fichiers<br />';
      if(!mkdir($g4p_chemin.'cache/'.$_POST['nom'].'/objets',0755))
        $_SESSION['message'].='impossible de créer le répertoire : cache/'.$_POST['nom'].'/objets<br />';      

      $_SESSION['genea_db_id']=$g4p_new_id;
      $_SESSION['genea_db_nom']='';
      header('location:'.g4p_make_url('','index.php','base',0));
    }
    else
    {
      $_SESSION['message']=$g4p_langue['message_perm_admin_requis'];
      header('location:'.g4p_make_url('','index.php','base',0));
    }
    break;

    case 'new_member':
    if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
      $sql="INSERT INTO genea_membres (email, pass) VALUES ('".mysql_escape_string($_POST['email'])."', '".md5($_POST['mdp'])."')";
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=sprintf($g4p_langue['message_email_ajout_succes'],$_POST['email']);
      else
        $_SESSION['message']=$g4p_langue['message_email_ajout_erreur'];
    }
    header('location:'.g4p_make_url('admin','index.php','g4p_opt=gerer_permissions',0));
    break;

    case 'del_member':
    if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
      if($_SESSION['g4p_id_membre']==$_POST['membre_id']) {
      	$_SESSION['message']=$g4p_langue['message_membre_supp_erreur_courant'];
      }
      else
      {
      	$sql="DELETE FROM genea_membres WHERE id=".$_POST['membre_id'];
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message']=$g4p_langue['message_membre_supp_succes'];
        else
          $_SESSION['message']=$g4p_langue['message_membre_supp_erreur'];
      }
    }
    header('location:'.g4p_make_url('admin','index.php','g4p_opt=gerer_permissions',0));
    break;
/*
    case 'mod_perm':
    if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
      $sql="UPDATE genea_permissions SET id_base='".$_POST['nom']."', type=".$_POST['type'].", permission=".$_POST['permission']." WHERE id=".$_POST['g4p_id'];
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_perm_modif_succes'];
      else
        $_SESSION['message']=$g4p_langue['message_perm_modif_echec'];
    }
    header('location:'.g4p_make_url('admin','index.php','g4p_opt=gerer_permissions',0));
    break;

    case 'new_permission':
    if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
      $sql="INSERT INTO genea_permissions (id_membre, id_base, type, permission) VALUES (".$_POST['nom'].",'".$_POST['id_base']."',".$_POST['type'].",".$_POST['permission'].")";
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_perm_ajout_succes'];
      else
        $_SESSION['message']=$g4p_langue['message_perm_ajout_echec'];
    }
    header('location:'.g4p_make_url('admin','index.php','g4p_opt=gerer_permissions',0));
    break;
*/

    case 'suppr_perm':
    if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
      $sql="DELETE FROM genea_permissions WHERE id=".$_GET['g4p_id'];
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_email_suppr_succes'];
      else
        $_SESSION['message']=$g4p_langue['message_email_suppr_echec'];
    }
    header('location:'.g4p_make_url('admin','index.php','g4p_opt=gerer_permissions&nom='.$_GET['id_membre'],0,0));
    break;

    case 'suppr_allperm':
    if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
      $sql="DELETE FROM genea_permissions WHERE id_membre=".$_GET['g4p_id'];
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_email_suppr_succes'];
      else
        $_SESSION['message']=$g4p_langue['message_email_suppr_echec'];
    }
    header('location:'.g4p_make_url('admin','index.php','g4p_opt=gerer_permissions&nom='.$_GET['g4p_id'],0,0));
    break;

    //creations et modifs des permissions
    case 'mod_permissions': 
    if(!empty($_POST['charge_modele']))
    {
      require_once($g4p_chemin.'p_conf/modele_permissions.php');
      $g4p_mysqli->g4p_query($g4p_modele_permissions[$_POST['charge_modele']]);
      header('location:'.g4p_make_url('admin','index.php','g4p_opt=gerer_permissions&nom='.$_POST['id_membre'],0,0));
      exit;
    }  
      
    $g4p_new_perm=array();
    
    //validation des nouvelles permissions
    if($_POST['nom']['nouvelle_perm1']!='' and $_POST['valeur']['nouvelle_perm1']!='' and $_POST['type']['nouvelle_perm1']!='')
      $g4p_new_perm[]=array('type'=>$_POST['type']['nouvelle_perm1'], 'valeur'=>$_POST['valeur']['nouvelle_perm1'], 'nom'=>$_POST['nom']['nouvelle_perm1']);
    unset($_POST['type']['nouvelle_perm1']);
    unset($_POST['nom']['nouvelle_perm1']);
    unset($_POST['valeur']['nouvelle_perm1']);
    
    if($_POST['nom']['nouvelle_perm2']!='' and $_POST['valeur']['nouvelle_perm2']!='' and $_POST['type']['nouvelle_perm2']!='')
      $g4p_new_perm[]=array('type'=>$_POST['type']['nouvelle_perm2'], 'valeur'=>$_POST['valeur']['nouvelle_perm2'], 'nom'=>$_POST['nom']['nouvelle_perm2']);
    unset($_POST['type']['nouvelle_perm2']);
    unset($_POST['nom']['nouvelle_perm2']);
    unset($_POST['valeur']['nouvelle_perm2']);

    //verification des dépendances
    $tmp=array();
    foreach($_POST['type'] as $key=>$a_type)
      $tmp[$key]=$_POST['type'][$key].$_POST['nom'][$key];
    
    foreach($_POST['type'] as $key=>$a_type)
    {
      if(isset($g4p_permissions[$a_type]['dependance']))
      {
        foreach($g4p_permissions[$a_type]['dependance'] as $g4p_a_dependance_key=>$g4p_a_dependance_value)
        {
          if(!in_array($g4p_a_dependance_key.$_POST['nom'][$key],$tmp))
          {
            $g4p_new_perm[]=array('type'=>$g4p_a_dependance_key, 'valeur'=>1, 'nom'=>$_POST['nom'][$key]);
            $_SESSION['message'].='<br />Dépendance incomplète, ajout de : '.$g4p_a_dependance_key.'-'.$_POST['nom'][$key];
          }        
        }
      }
    }

    //2ème passage, c'est un peu gorret comme système mais j'ai la flemme de me la joué récusrsive
    foreach($g4p_new_perm as $a_perm)
    {
      if(isset($g4p_permissions[$a_perm['type']]['dependance']))
      {
        foreach($g4p_permissions[$a_perm['type']]['dependance'] as $g4p_a_dependance_key=>$g4p_a_dependance_value)
        {
          if(!in_array($g4p_a_dependance_key.$g4p_permissions[$a_perm['nom']],$tmp))
          {
            $g4p_new_perm[]=array('type'=>$g4p_a_dependance_key, 'valeur'=>1, 'nom'=>$a_perm['nom']);
            $_SESSION['message'].='<br />Dépendance incomplète, ajout de : '.$g4p_a_dependance_key.'-'.$a_perm['nom'];
          }        
        }
      }
    }
    
    //suppression des perms
    $liste_perm=implode(',',array_keys($_POST['type']));
    if(!empty($liste_perm))
    {
      $sql="DELETE FROM genea_permissions WHERE id IN (".$liste_perm.")";
      $g4p_mysqli->g4p_query($sql);
    }
    
    //insertions des nouvelles perm
    $sql="INSERT IGNORE INTO genea_permissions (id_membre, type, permission, id_base) VALUES ";
    foreach($_POST['type'] as $key=>$a_type)
      $sql.="(".$_POST['id_membre'].", ".$_POST['type'][$key].", ".$_POST['valeur'][$key].", '".$_POST['nom'][$key]."'), ";
    foreach($g4p_new_perm as $a_newperm)
      $sql.="(".$_POST['id_membre'].", ".$a_newperm['type'].", ".$a_newperm['valeur'].", '".$a_newperm['nom']."'), ";
    $sql=substr($sql,0,-2);
    $g4p_mysqli->g4p_query($sql);
    
    //sauvegarde de la requète
    if(isset($_POST['save_modele']))
    {
      if(file_exists($g4p_chemin.'p_conf/modele_permissions.php'))      
        include($g4p_chemin.'p_conf/modele_permissions.php');
      $g4p_modele_permissions[$_POST['titre_modele']]=$sql;
      if($save_perm=fopen($g4p_chemin.'p_conf/modele_permissions.php','w'))
      {
        fwrite($save_perm,"<?php");
        foreach($g4p_modele_permissions as $a_perm_titre=>$a_perm_requete)
          fwrite($save_perm,"\n\$g4p_modele_permissions['".addslashes($a_perm_titre)."']='".addslashes($a_perm_requete)."';");
        fwrite($save_perm,"\n?>");
      }
    }
    
    header('location:'.g4p_make_url('admin','index.php','g4p_opt=gerer_permissions&nom='.$_POST['id_membre'],0,0));
    break;

    case 'agregats':
    g4p_agregat_noms();
    header('location:'.g4p_make_url('','index.php','g4p_action=liste_patronyme&patronyme=all',0,0));
    break;

    case 'vide_base':
    if($_SESSION['permission']->permission[_PERM_ADMIN_] or $_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
      if(!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
        $_POST['nom']=$_SESSION['genea_db_id'];
    
      $g4p_error=1;
      $sql="DELETE FROM  genea_address WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_familles WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM genea_individuals WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  agregats_noms WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_events_details WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_multimedia WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_notes WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_sour_citations WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_sour_records WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_repository WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      //$sql="DELETE FROM  genea_submitters WHERE base=".$_POST['nom'];
      //$g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_place WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      
      $sql="SELECT nom FROM genea_infos WHERE id=".$_POST['nom'];
      if($g4p_result=$g4p_mysqli->g4p_query($sql))
      {
        $g4p_result=$g4p_mysqli->g4p_result($g4p_result);
        $g4p_result=$g4p_result[0]['nom'];
        $g4p_error=vide_repertoire($g4p_result);
      }
      
      if($g4p_error==1)
        $_SESSION['message']=$g4p_langue['message_vide_base_succes'];
      else
        $_SESSION['message']=$g4p_langue['message_vide_base_echec'];
    }
    unset($g4p_indi->indi_id);
    $g4p_indi=false;
    header('location:'.g4p_make_url('admin','index.php','g4p_opt=vide_base',0));
    break;

    case 'suppr_base':
    if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
      $g4p_error=1;
      $sql="DELETE FROM  genea_familles WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM genea_individuals WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  agregats_noms WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_events WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_multimedia WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_notes WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_sources WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_repository WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_permissions WHERE id_base=".$_POST['nom']." and type="._PERM_MASK_INDI_;
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_download WHERE base=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      $sql="DELETE FROM  genea_infos WHERE id=".$_POST['nom'];
      $g4p_mysqli->g4p_query($sql) or $g4p_error=0;
      if($g4p_error==1)
        $_SESSION['message']=$g4p_langue['message_suppr_base_succes'];
      else
        $_SESSION['message']=$g4p_langue['message_suppr_base_echec'];
    }
    unset($g4p_indi->indi_id);
    $g4p_indi=false;
    $_SESSION['genea_db_id']=false;
    header('location:'.g4p_make_url('admin','index.php','g4p_opt=suppr_base',0));
    break;

    case 'suppr_note':
    if($_SESSION['permission']->permission[_PERM_SUPPR_FILES_] and isset($_GET['g4p_id']) and isset($_GET['g4p_lien']) and isset($_GET['g4p_type']))
    {
      switch($_GET['g4p_type'])
      {
        case 'indi':
        $sql="DELETE FROM rel_indi_notes WHERE indi_id=".$_GET['g4p_lien']." AND notes_id=".$_GET['g4p_id'];
        break;

        case 'familles':
        $sql="DELETE FROM rel_familles_notes WHERE familles_id=".$_GET['g4p_lien']." AND notes_id=".$_GET['g4p_id'];
        break;

        case 'events':
        $sql="DELETE FROM rel_events_notes WHERE events_id=".$_GET['g4p_lien']." AND notes_id=".$_GET['g4p_id'];
        break;
      }
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_suppr_lien_note_succes'];
      else
        $_SESSION['message']=$g4p_langue['message_suppr_lien_note_echec'];
      if(isset($g4p_indi->familles[$_GET['g4p_lien']]->conjoint->indi_id))
        g4p_destroy_cache($g4p_indi->familles[$_GET['g4p_lien']]->conjoint->indi_id);
      $g4p_indi=g4p_destroy_cache();
      header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
    }
    break;

    case 'suppr_note2':
    if($_SESSION['permission']->permission[_PERM_SUPPR_FILES_] and isset($_GET['g4p_id']))
    {
      $sql="DELETE FROM genea_notes WHERE notes_id=".$_GET['g4p_id'];
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_suppr_note_succes'];
      else
        $_SESSION['message']=$g4p_langue['message_suppr_note_echec'];
      header('location:'.g4p_make_url('','recherche.php','type=note_0',0));
    }
    break;

    case 'suppr_multimedia':
    if($_SESSION['permission']->permission[_PERM_SUPPR_FILES_] and isset($_GET['g4p_id']) and isset($_GET['g4p_lien']) and isset($_GET['g4p_type']))
    {
      switch($_GET['g4p_type'])
      {
        case 'indi':
        $sql="DELETE FROM rel_indi_multimedia WHERE indi_id=".$_GET['g4p_lien']." AND multimedia_id=".$_GET['g4p_id'];
        break;

        case 'familles':
        $sql="DELETE FROM rel_familles_multimedia WHERE familles_id=".$_GET['g4p_lien']." AND multimedia_id=".$_GET['g4p_id'];
        break;

        case 'events':
        $sql="DELETE FROM rel_events_multimedia WHERE events_id=".$_GET['g4p_lien']." AND multimedia_id=".$_GET['g4p_id'];
        break;

        case 'sources':
        $sql="DELETE FROM rel_sources_multimedia WHERE sources_id=".$_GET['g4p_lien']." AND multimedia_id=".$_GET['g4p_id'];
        break;
      }
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_suppr_lien_media_succes'];
      else
        $_SESSION['message']=$g4p_langue['message_suppr_lien_media_echec'];
      if(isset($g4p_indi->familles[$_GET['g4p_lien']]->conjoint->indi_id))
        g4p_destroy_cache($g4p_indi->familles[$_GET['g4p_lien']]->conjoint->indi_id);
      $g4p_indi=g4p_destroy_cache();
      header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
    }
    break;

    case 'suppr_multimedia2':
    if(isset($_GET['g4p_id']))
    {
      $sql="DELETE FROM genea_multimedia WHERE id=".$_GET['g4p_id'];
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_suppr_media_succes'];
      else
        $_SESSION['message']=$g4p_langue['message_suppr_media_echec'];
      header('location:'.g4p_make_url('','recherche.php','',0));
    }
    break;

// ajout d'un document multimédia
    case 'ajout_media':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_] and isset($_POST['g4p_id']) AND isset($_POST['g4p_type']))
    {
      if(!empty($_FILES) and $_FILES['fichier']['error']==0)
      {
        if(!in_array($_FILES['fichier']['type'],$g4p_mime_type_autorise))
        {
          $_SESSION['message']=$g4p_langue['message_type_fichier_refuse'];
          header('location:'.g4p_make_url('admin','index.php','g4p_opt=ajout_media&g4p_id='.$_POST['g4p_id'].'&g4p_type='.$_POST['g4p_type'],0,0));
          exit;
        }
        if(!move_uploaded_file($_FILES['fichier']['tmp_name'],$g4p_chemin.'cache/'.$_SESSION['genea_db_nom'].'/objets/'.$_FILES['fichier']['name']))
        {
          $_SESSION['message']=$g4p_langue['message_copie_imp'];
          if($fichier_log=fopen($g4p_chemin.'logs/modification_'.$_SESSION['genea_db_nom'].'.txt','ab'))
          {
            $g4p_texte='[AJOUT MEDIA] '.date("F j, Y, g:i a");
            $g4p_texte.="\t".$_SESSION['g4p_id_membre'];
            $g4p_texte.="\tImpossible de copier le média : \n";
            var_dump($_FILES['fichier']);
            $g4p_texte.=ob_get_contents();
            ob_clean();
            fwrite($fichier_log,$g4p_texte."\r\n");
            fclose($fichier_log);
          }
          
          if(!is_dir($g4p_chemin.'cache/'.$_SESSION['genea_db_nom'].'/objets'))
            $_SESSION['message'].='<br />Le répertoire objets n\'existe pas : cache/'.$_SESSION['genea_db_nom'].'/objets';
          header('location:'.g4p_make_url('admin','index.php','g4p_opt=ajout_media&g4p_id='.$_POST['g4p_id'].'&g4p_type='.$_POST['g4p_type'],0,0));
          exit;
        }
        $g4p_ext=substr($_FILES['fichier']['name'],-3);
        $g4p_file=$_FILES['fichier']['name'];
      }
      elseif(empty($_POST['url']) and $_FILES['fichier']['error']==1)
      {
        $_SESSION['message']=$g4p_langue['message_fichier_non_trouve'];
        header('location:'.g4p_make_url('admin','index.php','g4p_opt=ajout_media&g4p_id='.$_POST['g4p_id'].'&g4p_type='.$_POST['g4p_type'],0,0));
        exit;
      }

      if(!isset($g4p_ext))
      {
        $g4p_ext='URL';
        $g4p_file=$_POST['url'];
      }

      $sql="INSERT INTO genea_multimedia (title, format, file, base, chan) VALUES ('".mysql_escape_string(trim($_POST['titre']))."','".mysql_escape_string($g4p_ext)."','".mysql_escape_string($g4p_file)."',".$_SESSION['genea_db_id'].", NOW() )";
      if($g4p_mysqli->g4p_query($sql))
      {
        $_SESSION['message'].=$g4p_langue['message_media_enr'] ;
        switch($_POST['g4p_type'])
        {
          case 'familles':
          $sql="INSERT INTO rel_familles_multimedia (familles_id, multimedia_id, base) VALUES (".$_POST['g4p_id'].", LAST_INSERT_ID(), ".$_SESSION['genea_db_id']."  )";
          if($g4p_mysqli->g4p_query($sql))
            $_SESSION['message'].=$g4p_langue['message_lien_media_succes'];
          break;

          case 'events':
          $sql="INSERT INTO rel_events_multimedia (events_id, multimedia_id, base) VALUES (".$_POST['g4p_id'].", LAST_INSERT_ID(), ".$_SESSION['genea_db_id']."  )";
          if($g4p_mysqli->g4p_query($sql))
            $_SESSION['message'].=$g4p_langue['message_lien_media_succes'];
          break;

          case 'indi':
          $sql="INSERT INTO rel_indi_multimedia (indi_id, multimedia_id, base) VALUES (".$_POST['g4p_id'].", LAST_INSERT_ID(), ".$_SESSION['genea_db_id']." )";
          if($g4p_mysqli->g4p_query($sql))
            $_SESSION['message'].=$g4p_langue['message_lien_media_succes'];
          break;

          case 'sources':
          $sql="INSERT INTO rel_sources_multimedia (sources_id, multimedia_id, base) VALUES (".$_POST['g4p_id'].", LAST_INSERT_ID(), ".$_SESSION['genea_db_id']." )";
          if($g4p_mysqli->g4p_query($sql))
            $_SESSION['message'].=$g4p_langue['message_lien_media_succes'];
          break;
        }
      }
      else
        $_SESSION['message'].=$g4p_langue['message_req_echec'];
      if(isset($g4p_indi->familles[$_POST['g4p_id']]->conjoint->indi_id))
        g4p_destroy_cache($g4p_indi->familles[$_POST['g4p_id']]->conjoint->indi_id);
      $g4p_indi=g4p_destroy_cache();
      header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
    }
    break;

// modification d'un média
    case 'modif_multimedia':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      if(!empty($_FILES) and $_FILES['fichier']['error']==0)
      {
        if(!in_array($_FILES['fichier']['type'],$g4p_mime_type_autorise))
        {
          $_SESSION['message']=$g4p_langue['message_type_fichier_refuse'];
          header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_multimedia&g4p_id='.$_POST['g4p_id'].'&g4p_type='.$_POST['g4p_type'],0,0));
          exit;
        }
        if(!move_uploaded_file($_FILES['fichier']['tmp_name'],$g4p_chemin.'cache/'.$_SESSION['genea_db_nom'].'/objets/'.$_FILES['fichier']['name']))
        {
          $_SESSION['message']=$g4p_langue['message_copie_imp'];
          header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_multimedia&g4p_id='.$_POST['g4p_id'].'&g4p_type='.$_POST['g4p_type'],0,0));
          exit;
        }
        $g4p_ext=substr($_FILES['fichier']['name'],-3);
        $g4p_file=$_FILES['fichier']['name'];
      }
      elseif(empty($_POST['url']) and $_FILES['fichier']['error']==1)
      {
        $_SESSION['message']=$g4p_langue['message_fichier_non_trouve'];
        header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_multimedia&g4p_id='.$_POST['g4p_id'].'&g4p_type='.$_POST['g4p_type'],0,0));
        exit;
      }

      if(!isset($g4p_ext))
      {
        if(ereg('http://|mailto:',$_POST['g4p_file']))
        {
          $g4p_ext='URL';
          $g4p_file=$_POST['g4p_file'];
        }
        else
        {
          $g4p_ext=substr($_POST['g4p_file'],-3);
          $g4p_file=$_POST['g4p_file'];
        }
      }


      $sql="UPDATE genea_multimedia SET title='".mysql_escape_string($_POST['g4p_title'])."', file='".mysql_escape_string($g4p_file)."', format='".mysql_escape_string($g4p_ext)."', chan=NOW() WHERE id=".$_POST['g4p_id'];
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message'].=$g4p_langue['message_req_succes'];
      else
        $_SESSION['message'].=$g4p_langue['message_req_echec'];
      $g4p_liste_indi=g4p_cherche_dependances($_POST['g4p_id'],'medias');
      foreach($g4p_liste_indi as $g4p_tmp)
        g4p_destroy_cache($g4p_tmp);
      $g4p_indi=g4p_load_indi_infos($g4p_indi->indi_id);
      header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
    }
    break;

// suppression du cache pour forcer la mise à jour
    case 'del_cache':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      if($g4p_langue['entete_charset']=='UTF-8')
        $g4p_tmp=utf8_decode($_SESSION['genea_db_nom']);
      else
        $g4p_tmp=$_SESSION['genea_db_nom'];

      $g4p_indi=g4p_destroy_cache($_GET['g4p_id']);
      header('Content-Type: text/html; charset=ISO-8859-1') ;
      header('Date: ' . gmdate("D, d M Y H:i:s") . ' GMT');
      header('Cache-Control: no-cache');
      header('Pragma: public');
      $modif = gmdate('D, d M Y H:i:s', @filemtime($g4p_chemin.'cache/'.$g4p_tmp.'/indi_'.$g4p_indi->indi_id.'.txt')) ;
      header("Last-Modified: $modif GMT");
      header('location:'.g4p_make_url('','fiche_individuelle.php','id_pers='.$g4p_indi->indi_id,0));
      exit;
    }
    break;

    case 'ajout_repo':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      $sql="INSERT INTO genea_repository (repo_name, repo_addr, repo_city, repo_post, repo_stae, repo_ctry, repo_phon1, repo_phon2, repo_phon3, repo_chan, base)
        VALUES ('".mysql_escape_string($_POST['repo_name'])."','".mysql_escape_string($_POST['repo_addr'])."','".mysql_escape_string($_POST['repo_city'])."','".mysql_escape_string($_POST['repo_post'])."',
        '".mysql_escape_string($_POST['repo_stae'])."','".mysql_escape_string($_POST['repo_ctry'])."','".mysql_escape_string($_POST['repo_phon1'])."','".mysql_escape_string($_POST['repo_phon2'])."','".mysql_escape_string($_POST['repo_phon3'])."',NOW(),".$_SESSION['genea_db_id'].")";
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_repo_enr'];
         
      if(empty($_POST['g4p_referer']))
        header('location:'.g4p_make_url('admin','panel.php','',0));
      else
        header('location:'.$g4p_chemin.rawurldecode($_POST['g4p_referer']));
    }
    break;

    case 'mod_repo':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      $sql="REPLACE INTO genea_repository (repo_id, repo_name, repo_addr, repo_city, repo_post, repo_stae, repo_ctry, repo_phon1, repo_phon2, repo_phon3, repo_chan, base)
        VALUES (".mysql_escape_string($_POST['depot']).",'".mysql_escape_string($_POST['repo_name'])."','".mysql_escape_string($_POST['repo_addr'])."','".mysql_escape_string($_POST['repo_city'])."','".mysql_escape_string($_POST['repo_post'])."',
        '".mysql_escape_string($_POST['repo_stae'])."','".mysql_escape_string($_POST['repo_ctry'])."',','".mysql_escape_string($_POST['repo_phon1'])."','".mysql_escape_string($_POST['repo_phon2'])."','".mysql_escape_string($_POST['repo_phon3'])."',NOW(),".$_SESSION['genea_db_id'].")";
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_repo_enr'];
         
      if(empty($_POST['g4p_referer']))
        header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_repo',0));
      else
      {
        $referer=explode('|',rawurldecode($_POST['g4p_referer']));
        header('location:'.g4p_make_url($referer[0],$referer[1],$referer[2],0,0));
      }
    }
    break;

    case 'del_repo':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      $sql="DELETE FROM genea_repository WHERE repo_id=".$_POST['depot'];
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']="Depot supprimé avec succès";
         
      if(empty($_POST['g4p_referer']))
        header('location:'.g4p_make_url('admin','panel.php','',0));
      else
      {
        $referer=explode('|',rawurldecode($_POST['g4p_referer']));
        header('location:'.g4p_make_url($referer[0],$referer[1],$referer[2],0,0));
      }
    }
    break;
    
    case 'mod_place':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      $g4p_chps=implode(',',$g4p_config['subdivision']);
    
      $sql="UPDATE genea_place SET ";
      foreach($g4p_config['subdivision'] as $a_subdiv)
        $sql.=$a_subdiv."='".mysql_escape_string($_POST[$a_subdiv])."', ";   
      $sql=substr($sql,0,-2)." WHERE place_id=".mysql_escape_string($_POST['place']);
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']="Modifiaction du lieu effectué avec succès";
         
      if(empty($_POST['g4p_referer']))
        header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
      else
      {
        $referer=explode('|',rawurldecode($_POST['g4p_referer']));
        header('location:'.g4p_make_url($referer[0],$referer[1],$referer[2],0,0));
      }
    }
    break;
    
    case 'fus_place':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      $sql="UPDATE genea_events SET place_id=".$_POST['new_place']." WHERE place_id=".$_POST['place'];
      if($g4p_mysqli->g4p_query($sql))
      {
        $sql="DELETE FROM genea_place WHERE place_id=".$_POST['place'];
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message']="Modifiaction du lieu effectué avec succès";
      }
         
      if(empty($_POST['g4p_referer']))
        header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
      else
      {
        $referer=explode('|',rawurldecode($_POST['g4p_referer']));
        header('location:'.g4p_make_url($referer[0],$referer[1],$referer[2],0,0));
      }
    }
    break;

    case 'del_place':
    if($_SESSION['permission']->permission[_PERM_SUPPR_FILES_])
    {
      $sql="DELETE FROM genea_place WHERE place_id=".$_POST['place'];
      if($g4p_mysqli->g4p_query($sql))
      {
        $_SESSION['message']="suppression du lieu effectué avec succès";
      }
         
      if(empty($_POST['g4p_referer']))
        header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
      else
      {
        $referer=explode('|',rawurldecode($_POST['g4p_referer']));
        header('location:'.g4p_make_url($referer[0],$referer[1],$referer[2],0,0));
      }
    }
    break;
    
    case 'ajout_asso':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      if(!empty($_POST['g4p_id_asso2']))
        $_POST['g4p_id_asso']=$_POST['g4p_id_asso2'];
        
      switch($_POST['g4p_type'])
      {
        case 'events':
        $sql="INSERT INTO rel_asso_events (events_id, indi_id, base, description)
          VALUES ('".$_POST['g4p_id']."','".$_POST['g4p_id_asso']."','".$_SESSION['genea_db_id']."','".mysql_escape_string($_POST['description'])."')";
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message']=$g4p_langue['message_ajout_rela_succes'];
         
        $g4p_indi=g4p_destroy_cache($g4p_indi->indi_id);
        if(empty($_POST['g4p_referer']))
          header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
        else
          header('location:'.$g4p_chemin.rawurldecode($_POST['g4p_referer']));
        break;

        case 'indi':
        $sql="INSERT INTO rel_asso_indi (indi_id1, indi_id2, base, description)
          VALUES ('".$_POST['g4p_id']."','".$_POST['g4p_id_asso']."','".$_SESSION['genea_db_id']."','".mysql_escape_string($_POST['description'])."')";
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message']=$g4p_langue['message_ajout_rela_succes'];
         
        $g4p_indi=g4p_destroy_cache($g4p_indi->indi_id);
        if(empty($_POST['g4p_referer']))
          header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
        else
          header('location:'.$g4p_chemin.rawurldecode($_POST['g4p_referer']));
        break;
      }
    }
    break;

    case 'suppr_asso':
    if($_SESSION['permission']->permission[_PERM_SUPPR_FILES_])
    {
      switch($_GET['g4p_type'])
      {
        case 'events':
        $sql="DELETE FROM rel_asso_events WHERE indi_id=".$_GET['g4p_id']." and events_id=".$_GET['g4p_lien']." and base=".$_SESSION['genea_db_id'];
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message']=$g4p_langue['message_suppr_rela_succes'];
         
        $g4p_indi=g4p_destroy_cache($g4p_indi->indi_id);
        if(empty($_POST['g4p_referer']))
          header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
        else
          header('location:'.$g4p_chemin.rawurldecode($_POST['g4p_referer']));
        break;

        case 'indi':
        $sql="DELETE FROM rel_asso_indi WHERE indi_id1=".$_GET['g4p_lien']." and indi_id2=".$_GET['g4p_id']." and base=".$_SESSION['genea_db_id'];
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message']=$g4p_langue['message_suppr_rela_succes'];
         
        $g4p_indi=g4p_destroy_cache($g4p_indi->indi_id);
        if(empty($_POST['g4p_referer']))
          header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
        else
          header('location:'.$g4p_chemin.rawurldecode($_POST['g4p_referer']));
        break;
      }
    }
    break;

    case 'mask_fich':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      $sql="UPDATE genea_individuals SET resn='privacy' WHERE indi_id=".$_GET['g4p_id'];
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_masque_indi'];
        
     $g4p_indi=g4p_destroy_cache(); 
         
      header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
    }
    break;

    case 'demask_fich':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      $sql="UPDATE genea_individuals SET resn=NULL WHERE indi_id=".$_GET['g4p_id'];
      if($g4p_mysqli->g4p_query($sql))
        $_SESSION['message']=$g4p_langue['message_demasque_indi'];
        
      $g4p_indi=g4p_destroy_cache();
               
      header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
    }
    break;

////////////////////////////////////
//////  Lier un média
////////////////////////////////////
    case 'link_media':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_] and isset($_POST['g4p_id']) AND isset($_POST['g4p_type']))
    {
      switch($_POST['g4p_type'])
      {
        case 'indi':
        $sql="INSERT INTO rel_indi_multimedia (indi_id, multimedia_id, base) VALUES (".$_POST['g4p_id'].", ".$_POST['g4p_id_media'].", ".$_SESSION['genea_db_id'].")";
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message'].=$g4p_langue['message_lien_media_succes'];
        break;

        case 'events':
        $sql="INSERT INTO rel_events_multimedia (events_id, multimedia_id, base) VALUES (".$_POST['g4p_id'].", ".$_POST['g4p_id_media'].", ".$_SESSION['genea_db_id'].")";
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message'].=$g4p_langue['message_lien_media_succes'];
        break;

        case 'familles':
        $sql="INSERT INTO rel_familles_multimedia (familles_id, multimedia_id, base) VALUES (".$_POST['g4p_id'].", ".$_POST['g4p_id_media'].", ".$_SESSION['genea_db_id'].")";
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message'].=$g4p_langue['message_lien_media_succes'];
        break;

        case 'sources':
        $sql="INSERT INTO rel_sources_multimedia (sources_id, multimedia_id, base) VALUES (".$_POST['g4p_id'].", ".$_POST['g4p_id_media'].", ".$_SESSION['genea_db_id'].")";
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message'].=$g4p_langue['message_lien_media_succes'];
        break;
      }
    }
    if(isset($g4p_indi->familles[$_POST['g4p_id']]->conjoint->indi_id))
      g4p_destroy_cache($g4p_indi->familles[$_POST['g4p_id']]->conjoint->indi_id);
    $g4p_indi=g4p_destroy_cache();
    header('location:'.g4p_make_url('','index.php','g4p_action=fiche_indi',0));
    break;


//////////////////////////////////////
//////  Vider le cache d'une base
////////////////////////////////////
    case 'vide_cache':
    if($_SESSION['permission']->permission[_PERM_ADMIN_] or $_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
      if(!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
        $_GET['g4p_id']=$_SESSION['genea_db_nom'];
        
      if(vide_repertoire($_GET['g4p_id']))
        $_SESSION['message']='Cache vidé';
      else
        $_SESSION['message']='Echec de suppression du cache';
      
      if(isset($g4p_indi))
        $g4p_indi=g4p_load_indi_infos($g4p_indi->indi_id);
    }
    header('location:'.g4p_make_url('','index.php','',0));
    break;
    
    default:
    echo 'fonction pas encore faite';
    break;



//////////////////////////////////////
//////  Supprimer un rapport
////////////////////////////////////
    case 'del_rapport':
    if($_SESSION['permission']->permission[_PERM_ADMIN_] or $_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
      $sql="SELECT fichier, base, genea_infos.nom as nom FROM genea_download LEFT JOIN genea_infos ON genea_infos.id=genea_download.base WHERE download_id=".$_GET['g4p_id'];
      $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
      if($g4p_results=$g4p_mysqli->g4p_result($g4p_infos_req))
      {  
        $g4p_results=$g4p_results[0];
        if(!$_SESSION['permission']->permission[_PERM_ADMIN_] and $g4p_results['base']!=$_SESSION['genea_db_nom'])
          exit;
        else
        {
          if(@unlink($g4p_chemin.'cache/'.utf8_decode($g4p_results['nom']).'/fichiers/'.$g4p_results['fichier']))
          {
            $sql="DELETE FROM genea_download WHERE download_id=".$_GET['g4p_id'];
            if($g4p_mysqli->g4p_query($sql))
              $_SESSION['message']=$g4p_langue['a_index_delrapport_ok'];
            else
              $_SESSION['message']='La suppresion a partiellement échouée, supprimer l\'enregistrement '.$_GET['g4p_id'].' de la base genea_downloads';
          }
          else
            $_SESSION['message']=$g4p_langue['a_index_delrapport_fichier_nonok'];
        }
      }
    }
    header('location:'.g4p_make_url('admin','index.php','g4p_opt=gerer_download',0));
    break;
    
//////////////////////////////////////
//////  ajouter un alias
////////////////////////////////////
    case 'add_alia':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      if(!empty($_POST['alia2']))
        $_POST['alia']=$_POST['alia2'];
        
      if(!empty($_POST['alia']))
      {
        $g4p_alia1=g4p_load_indi_infos($_POST['alia']);
        $g4p_alia2=g4p_load_indi_infos($_POST['g4p_id']);
        if($g4p_alia1->indi_base!=$g4p_alia2->indi_base)
          $g4p_base='NULL';
        else
          $g4p_base=$g4p_alia1->indi_base;
        
        $sql="SELECT alias1 FROM genea_alias WHERE (alias1=".$_POST['alia']." AND alias2=".$_POST['g4p_id'].") OR (alias2=".$_POST['alia']." AND alias1=".$_POST['g4p_id'].")";
        $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
        if(!$g4p_mysqli->g4p_result($g4p_infos_req))
        {  
          $sql="INSERT INTO genea_alias (alias1,alias2,base) VALUES (".$_POST['alia'].",".$_POST['g4p_id'].", ".$g4p_base.")";
          if($g4p_mysqli->g4p_query($sql))
            $_SESSION['message']='L\'alias a été ajouté avec succès';
          else
            $_SESSION['message']='Erreur lors de l\'ajout de l\'alias';
          g4p_destroy_cache($_POST['alia']);
          $g4p_indi=g4p_destroy_cache($_POST['g4p_id']);
        }
        else
          $_SESSION['message']='L\'alias éxiste déjà';
      }
    }
    header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_fich',0));
    break;

//////////////////////////////////////
//////  ajouter un alias
////////////////////////////////////
    case 'suppr_alia':
    if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
    {
      if(!empty($_GET['g4p_id1']) and !empty($_GET['g4p_id2']))
      {
        $sql="DELETE FROM genea_alias WHERE (alias1=".$_GET['g4p_id1']." AND alias2=".$_GET['g4p_id2'].") OR (alias2=".$_GET['g4p_id1']." AND alias1=".$_GET['g4p_id2'].")";
        if($g4p_mysqli->g4p_query($sql))
          $_SESSION['message']='L\'alias a été supprimé avec succès';
        else
          $_SESSION['message']='Erreur lors de la suppression de l\'alias';

        g4p_destroy_cache($_GET['g4p_id2']);
        $g4p_indi=g4p_destroy_cache($_GET['g4p_id1']);
      }
    }
    header('location:'.g4p_make_url('admin','index.php','g4p_opt=mod_fich',0));
    break;

    default:
    echo 'fonction pas encore faite';
    break;


  }
}
?>
