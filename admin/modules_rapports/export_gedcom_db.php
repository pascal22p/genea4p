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
 *                Export la base au format GEDCOM                           *
 *                                                                          *
 * dernière mise à jour : 11/07//2004                                       *
 * En cas de problème : http://www.parois.net                               *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='../../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'admin/modules_rapports/liste_module_rapport.php');

if(!$_SESSION['permission']->permission[$g4p_module_export['export_gedcom_db.php']['permission']])
{
  echo $g4p_langue['acces_admin'];
  exit;
}

$ansel_to_ansi=array('âe'=>'é','áe'=>'è','ãe'=>'ê','èe'=>'ë','âo'=>'ó','áo'=>'ò','ão'=>'ô','èo'=>'ö','âa'=>'á','áa'=>'à','ãa'=>'â','èa'=>'ä','âu'=>'ú','áu'=>'ù','ãu'=>'û','èu'=>'ü','âi'=>'í','ái'=>'ì','ãi'=>'î','èi'=>'ï','ây'=>'ý','èy'=>'ÿ','ðc'=>'ç','~n'=>'ñ','âE'=>'É','áE'=>'È','ãE'=>'Ê','èE'=>'Ë','âO'=>'Ó','áO'=>'Ò','ãO'=>'Ô','èO'=>'Ö','âA'=>'Á','áA'=>'À','ãA'=>'Â','èA'=>'Ä','âU'=>'Ú','áU'=>'Ù','ãU'=>'Û','èU'=>'Ü','âI'=>'Í','áI'=>'Ì','ãI'=>'Î','èI'=>'Ï','âY'=>'Ý','èY'=>'Ÿ','ðC'=>'Ç','~N'=>'Ñ');
$ansel_map=array_values($ansel_to_ansi);
foreach($ansel_map as $ansel_a_map_key=>$ansel_a_map_value)
  $ansel_map[$ansel_a_map_key]='/'.$ansel_a_map_value.'/';
$ansel_replacement=array_keys($ansel_to_ansi);

function g4p_fwrite($fichier,$texte)
{
  global $ansel_map,$ansel_replacement;
  switch($_POST['g4p_encodage'])
  { 
    case 'utf8':
    fwrite($fichier,$texte);
    break;
    
    case 'ansel':
    $texte=preg_replace($ansel_map,$ansel_replacement,$texte);  
    fwrite($fichier,utf8_decode($texte));
    break;
    
    case 'ansi':
    default:
    fwrite($fichier,utf8_decode($texte));
    break;
  }
}
//on verifie que c'est bien la base de l'admin, si ce n'est pas un super-admin
if(!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
{
  $sql="SELECT id FROM genea_permissions WHERE id_membre=".$_SESSION['g4p_id_membre']." AND type=8 AND permission=1 and (id_base='".$_POST['base_rapport']."' OR id_base='*')";
  if($g4p_infos_req=g4p_db_query($sql))
  {
    $g4p_result=g4p_db_result($g4p_infos_req);
    if(!$g4p_result)
      exit;
  }
}


$sql="SELECT nom FROM genea_infos WHERE id=".$_POST['base_rapport'];
$g4p_infos_req=g4p_db_query($sql);
$g4p_base_select=g4p_db_result($g4p_infos_req);
$g4p_base_select=$g4p_base_select[0]['nom'];
  
if($g4p_langue['entete_charset']=='UTF-8')
  $g4p_base_select=utf8_decode($g4p_base_select);

$sql="SELECT indi_nom , indi_prenom , indi_sexe, indi_id, indi_chan, npfx, givn, nick, spfx, surn, nsfx, resn
         FROM genea_individuals WHERE base=".$_POST['base_rapport']." ORDER BY indi_nom, indi_prenom";
$g4p_infos_req=g4p_db_query($sql);
$g4p_individuals_table=g4p_db_result($g4p_infos_req, 'indi_id');

$sql="SELECT id, type, date_event, age, description, cause,genea_place.place_id,".implode(',',$g4p_config['subdivision'])."
    FROM genea_events 
    LEFT JOIN genea_place USING (place_id)
    WHERE genea_events.base=".$_POST['base_rapport'];
$g4p_result_req=g4p_db_query($sql);
$g4p_events_table=g4p_db_result($g4p_result_req,'id');

//notes
if($_POST['g4p_notes']=='oui')
{
  $sql="SELECT notes_id, notes_text, notes_chan
      FROM genea_notes WHERE base=".$_POST['base_rapport'];
  $g4p_result_req=g4p_db_query($sql);
  $g4p_notes_liste=g4p_db_result($g4p_result_req, 'notes_id');
}

//sources
if($_POST['g4p_sources']=='oui')
{
  $sql="SELECT sources_id, repo_id, sources_title, sources_publ, sources_text, sources_auth,
      sources_caln, sources_page, sources_medi, sources_chan
      FROM genea_sources WHERE base=".$_POST['base_rapport'];
  $g4p_result_req=g4p_db_query($sql);
  $g4p_sources_liste=g4p_db_result($g4p_result_req, 'sources_id');

  $sql="SELECT repo_id, repo_name, repo_addr, repo_city, repo_post, repo_ctry, repo_phon, repo_chan
      FROM genea_repository WHERE base=".$_POST['base_rapport'];
  $g4p_result_req=g4p_db_query($sql);
  $g4p_repo_liste=g4p_db_result($g4p_result_req, 'repo_id');
}

//media
if($_POST['g4p_medias']=='oui')
{
  $sql="SELECT id, title, format, file, chan
       FROM genea_multimedia WHERE base=".$_POST['base_rapport'];
  $g4p_result_req=g4p_db_query($sql);
  $g4p_media_liste=g4p_db_result($g4p_result_req, 'id');
}

$sql="SELECT familles_id, familles_wife, familles_husb, familles_chan
      FROM genea_familles WHERE base=".$_POST['base_rapport'];
$g4p_result_req=g4p_db_query($sql);
$g4p_result=g4p_db_result($g4p_result_req);
foreach($g4p_result as $g4p_a_famille)
{
  $g4p_familles_table[$g4p_a_famille['familles_id']]=$g4p_a_famille;
  $g4p_familles_table2[$g4p_a_famille['familles_husb']][]=$g4p_a_famille;
  $g4p_familles_table2[$g4p_a_famille['familles_wife']][]=$g4p_a_famille;
}

$sql="SELECT familles_id, indi_id, rela_type
  FROM rel_familles_indi WHERE base=".$_POST['base_rapport'];
$g4p_result_req=g4p_db_query($sql);
if($g4p_result=g4p_db_result($g4p_result_req))
{
  foreach($g4p_result as $g4p_a_result)
    $g4p_child_table1[$g4p_a_result['indi_id']][]=$g4p_a_result;
  foreach($g4p_result as $g4p_a_result)
    $g4p_child_table2[$g4p_a_result['familles_id']][]=$g4p_a_result;
}

//medias
if($_POST['g4p_medias']=='oui')
{
  $sql="SELECT events_id, multimedia_id FROM rel_events_multimedia WHERE base=".$_POST['base_rapport'];
  $g4p_result_req=g4p_db_query($sql);
  if($g4p_result=g4p_db_result($g4p_result_req))
    foreach($g4p_result as $g4p_a_rel)
      $g4p_rel_events_multimedia[$g4p_a_rel['events_id']][]=$g4p_a_rel['multimedia_id'];
}

//notes
if($_POST['g4p_notes']=='oui')
{
  $sql="SELECT events_id, notes_id FROM rel_events_notes WHERE base=".$_POST['base_rapport'];
  $g4p_result_req=g4p_db_query($sql);
  if($g4p_result=g4p_db_result($g4p_result_req))
    foreach($g4p_result as $g4p_a_rel)
      $g4p_rel_events_notes[$g4p_a_rel['events_id']][]=$g4p_a_rel['notes_id'];
}

//sources
if($_POST['g4p_sources']=='oui')
{
  $sql="SELECT events_id, sources_id FROM rel_events_sources WHERE base=".$_POST['base_rapport'];
  $g4p_result_req=g4p_db_query($sql);
  if($g4p_result=g4p_db_result($g4p_result_req))
    foreach($g4p_result as $g4p_a_rel)
      $g4p_rel_events_sources[$g4p_a_rel['events_id']][]=$g4p_a_rel['sources_id'];
}

//medias
if($_POST['g4p_medias']=='oui')
{
  $sql="SELECT indi_id, multimedia_id FROM rel_indi_multimedia WHERE base=".$_POST['base_rapport'];
  $g4p_result_req=g4p_db_query($sql);
  if($g4p_result=g4p_db_result($g4p_result_req))
    foreach($g4p_result as $g4p_a_rel)
      $g4p_rel_indi_multimedia[$g4p_a_rel['indi_id']][]=$g4p_a_rel['multimedia_id'];
}

//notes
if($_POST['g4p_notes']=='oui')
{
  $sql="SELECT indi_id, notes_id FROM rel_indi_notes WHERE base=".$_POST['base_rapport'];
  $g4p_result_req=g4p_db_query($sql);
  if($g4p_result=g4p_db_result($g4p_result_req))
    foreach($g4p_result as $g4p_a_rel)
      $g4p_rel_indi_notes[$g4p_a_rel['indi_id']][]=$g4p_a_rel['notes_id'];
}

//sources
if($_POST['g4p_sources']=='oui')
{
  $sql="SELECT indi_id, sources_id FROM rel_indi_sources WHERE base=".$_POST['base_rapport'];
  $g4p_result_req=g4p_db_query($sql);
  if($g4p_result=g4p_db_result($g4p_result_req))
    foreach($g4p_result as $g4p_a_rel)
      $g4p_rel_indi_sources[$g4p_a_rel['indi_id']][]=$g4p_a_rel['sources_id'];
}

//medias
if($_POST['g4p_medias']=='oui')
{
  $sql="SELECT familles_id, multimedia_id FROM rel_familles_multimedia WHERE base=".$_POST['base_rapport'];
  $g4p_result_req=g4p_db_query($sql);
  if($g4p_result=g4p_db_result($g4p_result_req))
    foreach($g4p_result as $g4p_a_rel)
      $g4p_rel_familles_multimedia[$g4p_a_rel['familles_id']][]=$g4p_a_rel['multimedia_id'];
}

//notes
if($_POST['g4p_notes']=='oui')
{
  $sql="SELECT familles_id, notes_id FROM rel_familles_notes WHERE base=".$_POST['base_rapport'];
  $g4p_result_req=g4p_db_query($sql);
  if($g4p_result=g4p_db_result($g4p_result_req))
    foreach($g4p_result as $g4p_a_rel)
      $g4p_rel_familles_notes[$g4p_a_rel['familles_id']][]=$g4p_a_rel['notes_id'];
}

//sources
if($_POST['g4p_sources']=='oui')
{
  $sql="SELECT familles_id, sources_id FROM rel_familles_sources WHERE base=".$_POST['base_rapport'];
  $g4p_result_req=g4p_db_query($sql);
  if($g4p_result=g4p_db_result($g4p_result_req))
    foreach($g4p_result as $g4p_a_rel)
      $g4p_rel_familles_sources[$g4p_a_rel['familles_id']][]=$g4p_a_rel['sources_id'];
}

$sql="SELECT familles_id, events_id FROM rel_familles_event WHERE base=".$_POST['base_rapport'];
$g4p_result_req=g4p_db_query($sql);
if($g4p_result=g4p_db_result($g4p_result_req))
  foreach($g4p_result as $g4p_a_rel)
    $g4p_rel_familles_events[$g4p_a_rel['familles_id']][]=$g4p_a_rel['events_id'];

$sql="SELECT indi_id, events_id FROM rel_indi_event WHERE base=".$_POST['base_rapport'];
$g4p_result_req=g4p_db_query($sql);
if($g4p_result=g4p_db_result($g4p_result_req))
  foreach($g4p_result as $g4p_a_rel)
    $g4p_rel_indi_events[$g4p_a_rel['indi_id']][]=$g4p_a_rel['events_id'];

//medias des sources
if($_POST['g4p_medias']=='oui' and $_POST['g4p_sources']=='oui')
{
  $sql="SELECT sources_id, multimedia_id FROM rel_sources_multimedia WHERE base=".$_POST['base_rapport'];
  $g4p_result_req=g4p_db_query($sql);
  if($g4p_result=g4p_db_result($g4p_result_req))
    foreach($g4p_result as $g4p_a_rel)
      $g4p_rel_sources_medias[$g4p_a_rel['sources_id']][]=$g4p_a_rel['multimedia_id'];
}

$sql="SELECT indi_id, events_id, description FROM rel_asso_events WHERE base=".$_POST['base_rapport'];
$g4p_result_req=g4p_db_query($sql);
if($g4p_result=g4p_db_result($g4p_result_req))
{
  $i=0;
  foreach($g4p_result as $g4p_a_rel)
  {
    $g4p_rel_asso_events[$g4p_a_rel['events_id']][$i]['indi_id']=$g4p_a_rel['indi_id'];
    $g4p_rel_asso_events[$g4p_a_rel['events_id']][$i]['description']=$g4p_a_rel['description'];
    $i++;
  }
}

$sql="SELECT indi_id1, indi_id2, description FROM rel_asso_indi WHERE base=".$_POST['base_rapport'];
$g4p_result_req=g4p_db_query($sql);
if($g4p_result=g4p_db_result($g4p_result_req))
{
  $i=0;
  foreach($g4p_result as $g4p_a_rel)
  {
    $g4p_rel_asso_indi[$g4p_a_rel['indi_id1']][$i]['indi_id']=$g4p_a_rel['indi_id2'];
    $g4p_rel_asso_indi[$g4p_a_rel['indi_id1']][$i]['description']=$g4p_a_rel['description'];
    $i++;
  }
}

$sql="SELECT permission FROM genea_permissions WHERE id_membre=1 AND id_base='".$_POST['base_rapport']."' AND type=11";
$g4p_result_req=g4p_db_query($sql);
if($g4p_result=g4p_db_result($g4p_result_req))
  foreach($g4p_result as $g4p_a_rel)
    $g4p_privacy[]=$g4p_a_rel['permission'];

$fichier=fopen($g4p_chemin.'cache/'.$g4p_base_select.'/fichiers/'.$g4p_base_select.'.ged','wb');
g4p_fwrite($fichier, "0 HEAD
1 SOUR Genea4p
2 VERS ".$g4p_config['g4p_version']."
2 NAME genea4p
2 CORP PAROIS Pascal
1 DATE ".date('d M Y')."
1 CHAR ".strtoupper($_POST['g4p_encodage'])."
1 GEDC
2 VERS 5.5
2 FORM Lineage-Linked\r\n");

foreach($g4p_individuals_table as $g4p_a_individu)
{
  g4p_fwrite($fichier,'0 @I'.$g4p_a_individu['indi_id']."@ INDI\r\n");
  g4p_fwrite($fichier,' 1 NAME '.$g4p_a_individu['indi_prenom'].'/'.$g4p_a_individu['indi_nom']."/\r\n");
  if($g4p_a_individu['npfx'])
    g4p_fwrite($fichier,'  2 NPFX '.$g4p_a_individu['npfx']."\r\n");
  if($g4p_a_individu['givn'])
    g4p_fwrite($fichier,'  2 GIVN '.$g4p_a_individu['givn']."\r\n");
  if($g4p_a_individu['nick'])
    g4p_fwrite($fichier,'  2 NICK '.$g4p_a_individu['nick']."\r\n");
  if($g4p_a_individu['spfx'])
    g4p_fwrite($fichier,'  2 SPFX '.$g4p_a_individu['spfx']."\r\n");
  if($g4p_a_individu['surn'])
    g4p_fwrite($fichier,'  2 SURN '.$g4p_a_individu['surn']."\r\n");
  if($g4p_a_individu['nsfx'])
    g4p_fwrite($fichier,'  2 NSFX '.$g4p_a_individu['nsfx']."\r\n");
  g4p_fwrite($fichier,' 1 SEX '.$g4p_a_individu['indi_sexe']."\r\n");

//evenements individuels
  if(isset($g4p_rel_indi_events[$g4p_a_individu['indi_id']]))
  {
    foreach($g4p_rel_indi_events[$g4p_a_individu['indi_id']] as $g4p_a_ievents)
    {
      if(!empty($g4p_a_ievents['attestation']))
        $g4p_desc1=' '.$g4p_a_ievents['attestation'];
      else
        $g4p_desc1='';
        if(array_key_exists($g4p_events_table[$g4p_a_ievents]['type'],$g4p_tag_iattributs))
        { //Attributs
        	g4p_fwrite($fichier,' 1 '.$g4p_events_table[$g4p_a_ievents]['type'].' '.$g4p_events_table[$g4p_a_ievents]['description']."\r\n");
        	//TODO ecrire le reste (evenements en niveau 2)
        }else{
      g4p_fwrite($fichier,' 1 '.$g4p_events_table[$g4p_a_ievents]['type'].$g4p_desc1."\r\n");
        g4p_fwrite($fichier,'  2 TYPE '.$g4p_events_table[$g4p_a_ievents]['description']."\r\n");
      if($g4p_events_table[$g4p_a_ievents]['date_event']!='')
        g4p_fwrite($fichier,'  2 DATE '.$g4p_events_table[$g4p_a_ievents]['date_event']."\r\n");

      if($g4p_events_table[$g4p_a_ievents]['place_id']!=NULL)
      {
        $tmp='  2 PLAC ';
        foreach($_POST['place'] as $a_place)
          if($a_place!=-1)
            $tmp.=$g4p_events_table[$g4p_a_ievents][$a_place].",";        
        if(strlen($tmp)>9)
          g4p_fwrite($fichier,substr($tmp,0,-1)."\r\n");
      }

      if($g4p_events_table[$g4p_a_ievents]['cause']!='')
        g4p_fwrite($fichier,'  2 CAUS '.$g4p_events_table[$g4p_a_ievents]['cause']."\r\n");
      if(!empty($g4p_events_table[$g4p_a_ievents]['age']))
        g4p_fwrite($fichier,'  2 AGE '.$g4p_events_table[$g4p_a_ievents]['age']."\r\n");

        if($_POST['g4p_sources']=='oui')
        {
            if(isset($g4p_rel_events_sources[$g4p_a_ievents]))
            {
                foreach($g4p_rel_events_sources[$g4p_a_ievents] as $g4p_a_source)
                {
                    g4p_fwrite($fichier,'  2 SOUR @S'.$g4p_a_source."@\r\n");
                    g4p_fwrite($fichier,'   3 PAGE '.$g4p_sources_liste[$g4p_a_source]['source_page']."\r\n");
                    g4p_fwrite($fichier,'   3 TITL '.$g4p_a_source['sources_title']."\r\n");
                    g4p_fwrite($fichier,'   3 AUTH '.$g4p_a_source['sources_auth']."\r\n");
                
                    //les médias
                    if($_POST['g4p_medias']=='oui')
                        if(isset($g4p_rel_sources_medias[$g4p_a_source['sources_id']]))
                            foreach($g4p_rel_sources_medias[$g4p_a_source['sources_id']] as $g4p_a_media)
                                g4p_fwrite($fichier,'   3 OBJE @O'.$g4p_a_media."@\r\n");*/                
                }
            }
        }
      if($_POST['g4p_notes']=='oui')
        if(isset($g4p_rel_events_notes[$g4p_a_ievents]))
          foreach($g4p_rel_events_notes[$g4p_a_ievents] as $g4p_a_note)
            g4p_fwrite($fichier,'  2 NOTE @N'.$g4p_a_note."@\r\n");
      if($_POST['g4p_medias']=='oui')
        if(isset($g4p_rel_events_multimedia[$g4p_a_ievents]))
          foreach($g4p_rel_events_multimedia[$g4p_a_ievents] as $g4p_a_media)
            g4p_fwrite($fichier,'  2 OBJE @O'.$g4p_a_media."@\r\n");
        }
      //relations
      if(isset($g4p_rel_asso_events[$g4p_a_ievents]))
      {
        foreach($g4p_rel_asso_events[$g4p_a_ievents] as $g4p_a_asso)
        {
          g4p_fwrite($fichier,'  2 ASSO @I'.$g4p_a_asso['indi_id']."@\r\n");
          if($g4p_a_asso['description'])
            g4p_fwrite($fichier,'   3 RELA '.$g4p_a_asso['description']."\r\n");
        }
      }
    }
  }

// RESN
  if(!empty($g4p_a_individu['resn']))
    g4p_fwrite($fichier," 1 RESN ".$g4p_a_individu['resn']."\r\n");

// CHAN
  if(!empty($g4p_a_individu['indi_chan']) and $g4p_a_individu['indi_chan']!='0000-00-00 00:00:00')
  {
    $g4p_a_individu['indi_chan']=explode(' ',$g4p_a_individu['indi_chan']);
    g4p_fwrite($fichier," 1 CHAN\r\n");
    g4p_fwrite($fichier,"  2 DATE ".g4p_strftime('%d %b %Y',strtotime($g4p_a_individu['indi_chan'][0]))."\r\n");
    g4p_fwrite($fichier,"   3 TIME ".$g4p_a_individu['indi_chan'][1]."\r\n");
  }

// Le conjoint
  if(isset($g4p_familles_table2[$g4p_a_individu['indi_id']]))
    foreach($g4p_familles_table2[$g4p_a_individu['indi_id']] as $g4p_a_famille)//affiche tous les mariages
      g4p_fwrite($fichier,' 1 FAMS @F'.$g4p_a_famille['familles_id']."@\r\n");

// les parents
  if (isset($g4p_child_table1[$g4p_a_individu['indi_id']]))
  {
    foreach($g4p_child_table1[$g4p_a_individu['indi_id']] as $g4p_un_parent)
    {
      g4p_fwrite($fichier,' 1 FAMC @F'.$g4p_un_parent['familles_id']."@\r\n");
      if($g4p_un_parent['rela_type']=='adopted')
        g4p_fwrite($fichier,"  2 PEDI adopted\r\n");
    }
  }

//Les notes et sources
  if($_POST['g4p_sources']=='oui')
    if(isset($g4p_rel_indi_sources[$g4p_a_individu['indi_id']]))
      foreach($g4p_rel_indi_sources[$g4p_a_individu['indi_id']] as $g4p_a_source)
        g4p_fwrite($fichier,' 1 SOUR @S'.$g4p_a_source."@\r\n");
  if($_POST['g4p_notes']=='oui')
    if(isset($g4p_rel_indi_notes[$g4p_a_individu['indi_id']]))
      foreach($g4p_rel_indi_notes[$g4p_a_individu['indi_id']] as $g4p_a_note)
        g4p_fwrite($fichier,' 1 NOTE @N'.$g4p_a_note."@\r\n");
  if($_POST['g4p_medias']=='oui')
    if(isset($g4p_rel_indi_multimedia[$g4p_a_individu['indi_id']]))
      foreach($g4p_rel_indi_multimedia[$g4p_a_individu['indi_id']] as $g4p_a_media)
        g4p_fwrite($fichier,' 1 OBJE @O'.$g4p_a_media."@\r\n");

  //relations
  if(isset($g4p_rel_asso_indi[$g4p_a_individu['indi_id']]))
  {
    foreach($g4p_rel_asso_indi[$g4p_a_individu['indi_id']] as $g4p_a_asso)
    {
      g4p_fwrite($fichier,' 1 ASSO @I'.$g4p_a_asso['indi_id']."@\r\n");
      if($g4p_a_asso['description'])
        g4p_fwrite($fichier,' 2 RELA '.$g4p_a_asso['description']."\r\n");
    }
  }
}

// la famille
foreach($g4p_familles_table as $g4p_a_famille)
{
  g4p_fwrite($fichier,'0 @F'.$g4p_a_famille['familles_id']."@ FAM\r\n");
  if(isset($g4p_rel_familles_events[$g4p_a_famille['familles_id']]))
  {
    //evenements familiaux
    foreach($g4p_rel_familles_events[$g4p_a_famille['familles_id']] as $g4p_a_fevent)
    {
      g4p_fwrite($fichier,' 1 '.$g4p_events_table[$g4p_a_fevent]['type']." ".$g4p_events_table[$g4p_a_fevent]['desc']."\r\n");
      if($g4p_events_table[$g4p_a_fevent]['type']=='EVEN')
        g4p_fwrite($fichier,'  2 TYPE '.$g4p_events_table[$g4p_a_fevent]['desc']."\r\n");
      if($g4p_events_table[$g4p_a_fevent]['date_event']!='')
        g4p_fwrite($fichier,'  2 DATE '.$g4p_events_table[$g4p_a_fevent]['date_event']."\r\n");

      if($g4p_events_table[$g4p_a_fevent]['place_id']!=NULL)
      {
        $tmp='  2 PLAC ';
        foreach($_POST['place'] as $a_place)
          if($a_place!=-1)
            $tmp.=$g4p_events_table[$g4p_a_fevent][$a_place].",";        
          if(strlen($tmp)>9)
            g4p_fwrite($fichier,substr($tmp,0,-1)."\r\n");
      }

      if($g4p_events_table[$g4p_a_fevent]['age']!='')
        g4p_fwrite($fichier,'  2 AGE '.$g4p_events_table[$g4p_a_fevent]['age']."\r\n");
      if($g4p_events_table[$g4p_a_fevent]['cause']!='')
        g4p_fwrite($fichier,'  2 CAUS '.$g4p_events_table[$g4p_a_fevent]['cause']."\r\n");

      if($_POST['g4p_sources']=='oui')
        if(isset($g4p_rel_events_sources[$g4p_a_fevent]))
          foreach($g4p_rel_events_sources[$g4p_a_fevent] as $g4p_a_source)
            g4p_fwrite($fichier,'  2 SOUR @S'.$g4p_a_source."@\r\n");
      if($_POST['g4p_notes']=='oui')
        if(isset($g4p_rel_events_notes[$g4p_a_fevent]))
          foreach($g4p_rel_events_notes[$g4p_a_fevent] as $g4p_a_note)
            g4p_fwrite($fichier,'  2 NOTE @N'.$g4p_a_note."@\r\n");
      if($_POST['g4p_medias']=='oui')
        if(isset($g4p_rel_events_multimedia[$g4p_a_fevent]))
          foreach($g4p_rel_events_multimedia[$g4p_a_fevent] as $g4p_a_media)
            g4p_fwrite($fichier,'  2 OBJE @O'.$g4p_a_media."@\r\n");

      //relations
      if(isset($g4p_rel_asso_events[$g4p_a_fevent]))
      {
        foreach($g4p_rel_asso_events[$g4p_a_fevent] as $g4p_a_asso)
        {
          g4p_fwrite($fichier,'  2 ASSO @I'.$g4p_a_asso['indi_id']."@\r\n");
          if($g4p_a_asso['description'])
            g4p_fwrite($fichier,'   3 RELA '.$g4p_a_asso['description']."\r\n");
        }
      }
    }
  }
  if($g4p_a_famille['familles_husb'])
    g4p_fwrite($fichier,' 1 HUSB @I'.$g4p_a_famille['familles_husb']."@\r\n");
  if($g4p_a_famille['familles_wife'])
    g4p_fwrite($fichier,' 1 WIFE @I'.$g4p_a_famille['familles_wife']."@\r\n");

  if(isset($g4p_child_table2[$g4p_a_famille['familles_id']]))
    foreach($g4p_child_table2[$g4p_a_famille['familles_id']] as $g4p_a_enfant)
      g4p_fwrite($fichier,' 1 CHIL @I'.$g4p_a_enfant['indi_id']."@\r\n");

// CHAN
  if(!empty($g4p_a_famille['familles_chan']) and $g4p_a_famille['familles_chan']!='0000-00-00 00:00:00')
  {
    $g4p_a_famille['familles_chan']=explode(' ',$g4p_a_famille['familles_chan']);
    g4p_fwrite($fichier," 1 CHAN\r\n");
    g4p_fwrite($fichier,"  2 DATE ".g4p_strftime('%d %b %Y',strtotime($g4p_a_famille['familles_chan'][0]))."\r\n");
    g4p_fwrite($fichier,"   3 TIME ".$g4p_a_famille['familles_chan'][1]."\r\n");
  }

  if($_POST['g4p_sources']=='oui')
    if(isset($g4p_rel_familles_sources[$g4p_a_famille['familles_id']]))
      foreach($g4p_rel_familles_sources[$g4p_a_famille['familles_id']] as $g4p_a_source)
        g4p_fwrite($fichier,' 1 SOUR @S'.$g4p_a_source."@\r\n");
  if($_POST['g4p_notes']=='oui')
    if(isset($g4p_rel_familles_notes[$g4p_a_famille['familles_id']]))
      foreach($g4p_rel_familles_notes[$g4p_a_famille['familles_id']] as $g4p_a_note)
        g4p_fwrite($fichier,' 1 NOTE @N'.$g4p_a_note."@\r\n");
  if($_POST['g4p_medias']=='oui')
    if(isset($g4p_rel_familles_multimedia[$g4p_a_famille['familles_id']]))
      foreach($g4p_rel_familles_multimedia[$g4p_a_famille['familles_id']] as $g4p_a_media)
        g4p_fwrite($fichier,' 1 OBJE @O'.$g4p_a_media."@\r\n");
}
//les médias
if($_POST['g4p_medias']=='oui' and is_array($g4p_media_liste))
{
  foreach($g4p_media_liste as $g4p_a_media)
  {
    g4p_fwrite($fichier,'0 @O'.$g4p_a_media['id']."@ OBJE \r\n");
    g4p_fwrite($fichier,' 1 FORM '.$g4p_a_media['format']."\r\n");
    g4p_fwrite($fichier,' 1 TITL '.$g4p_a_media['title']."\r\n");
    g4p_fwrite($fichier,' 1 FILE '.$g4p_a_media['file']."\r\n");

// CHAN
    if(!empty($g4p_a_media['chan']) and $g4p_a_media['chan']!='0000-00-00 00:00:00')
    {
      $g4p_a_media['chan']=explode(' ',$g4p_a_media['chan']);
      g4p_fwrite($fichier," 1 CHAN\r\n");
      setlocale(LC_TIME, 'en');
      g4p_fwrite($fichier,"  2 DATE ".g4p_strftime('%d %b %Y',strtotime($g4p_a_media['chan'][0]))."\r\n");
      setlocale(LC_TIME, 'fr');
      g4p_fwrite($fichier,"   3 TIME ".$g4p_a_media['chan'][1]."\r\n");
    }
  }
}

//les notes    
if($_POST['g4p_notes']=='oui' and is_array($g4p_notes_liste))
{
  foreach($g4p_notes_liste as $g4p_a_note)
  {
    g4p_fwrite($fichier,'0 @N'.$g4p_a_note['notes_id']."@ NOTE ");
    $g4p_a_note['notes_text']=wordwrap($g4p_a_note['notes_text'],200,"&&rc&& 1 CONC ",1);
    $g4p_a_note['notes_text']=str_replace("\r\n", "\r\n1 CONT ", trim($g4p_a_note['notes_text']));
    g4p_fwrite($fichier,str_replace("&&rc&&", "\r\n", trim($g4p_a_note['notes_text']))."\r\n");
// CHAN
    if(!empty($g4p_a_note['notes_chan']) and $g4p_a_note['notes_chan']!='0000-00-00 00:00:00')
    {
      $g4p_a_note['notes_chan']=explode(' ',$g4p_a_note['notes_chan']);
      g4p_fwrite($fichier," 1 CHAN\r\n");
      setlocale(LC_TIME, 'en');
      g4p_fwrite($fichier,"  2 DATE ".g4p_strftime('%d %b %Y',strtotime($g4p_a_note['notes_chan'][0]))."\r\n");
      setlocale(LC_TIME, 'fr');
      g4p_fwrite($fichier,"   3 TIME ".$g4p_a_note['notes_chan'][1]."\r\n");
    }
  }
}
//les sources
if($_POST['g4p_sources']=='oui'and $g4p_sources_liste)
{
  foreach($g4p_sources_liste as $g4p_a_source)
  {
    g4p_fwrite($fichier,'0 @S'.$g4p_a_source['sources_id']."@ SOUR\r\n");
//    g4p_fwrite($fichier,' 1 TITL '.$g4p_a_source['sources_title']."\r\n");
//    g4p_fwrite($fichier,' 1 AUTH '.$g4p_a_source['sources_auth']."\r\n");
//    g4p_fwrite($fichier,' 1 PAGE '.$g4p_a_source['sources_page']."\r\n");
    if(!empty($g4p_a_source['repo_id']))
    {
      g4p_fwrite($fichier,' 1 REPO @R'.$g4p_a_source['repo_id']."@\r\n");
      g4p_fwrite($fichier,'  2 CALN '.$g4p_a_source['sources_caln']."\r\n");
      g4p_fwrite($fichier,'   3 MEDI '.$g4p_a_source['sources_medi']."\r\n");
    }
    g4p_fwrite($fichier,' 1 TEXT ');
    $g4p_a_source['sources_text']=wordwrap($g4p_a_source['sources_text'],200,"&&rc&& 1 CONC ",1);
    $g4p_a_source['sources_text']=str_replace("\r\n", "\r\n1 CONT ", trim($g4p_a_source['sources_text']));
    g4p_fwrite($fichier,str_replace("&&rc&&", "\r\n", trim($g4p_a_source['sources_text']))."\r\n");
    g4p_fwrite($fichier,' 1 PUBL ');
    $g4p_a_source['sources_publ']=wordwrap($g4p_a_source['sources_publ'],200,"&&rc&& 1 CONC ",1);
    $g4p_a_source['sources_publ']=str_replace("\r\n", "\r\n1 CONT ", trim($g4p_a_source['sources_publ']));
    g4p_fwrite($fichier,str_replace("&&rc&&", "\r\n", trim($g4p_a_source['sources_publ']))."\r\n");

// CHAN
    if(!empty($g4p_a_source['sources_chan']) and $g4p_a_source['sources_chan']!='0000-00-00 00:00:00')
    {
      $g4p_a_source['sources_chan']=explode(' ',$g4p_a_source['sources_chan']);
      g4p_fwrite($fichier," 1 CHAN\r\n");
      setlocale(LC_TIME, 'en');
      g4p_fwrite($fichier,"  2 DATE ".g4p_strftime('%d %b %Y',strtotime($g4p_a_source['sources_chan'][0]))."\r\n");
      setlocale(LC_TIME, 'fr');
      g4p_fwrite($fichier,"   3 TIME ".$g4p_a_source['sources_chan'][1]."\r\n");
    }
/*    //les médias
    if($_POST['g4p_medias']=='oui')
      if(isset($g4p_rel_sources_medias[$g4p_a_source['sources_id']]))
        foreach($g4p_rel_sources_medias[$g4p_a_source['sources_id']] as $g4p_a_media)
          g4p_fwrite($fichier,' 1 OBJE @O'.$g4p_a_media."@\r\n");*/
  }
}

//les dépots
if($_POST['g4p_sources']=='oui' and $g4p_repo_liste)
{
  foreach($g4p_repo_liste as $g4p_a_repo)
  {
    g4p_fwrite($fichier,"0 @R".$g4p_a_repo['repo_id']."@ REPO\r\n");
    g4p_fwrite($fichier," 1 NAME ".$g4p_a_repo['repo_name']."\r\n");
    g4p_fwrite($fichier," 1 ADDR ".$g4p_a_repo['repo_addr']."\r\n");
    g4p_fwrite($fichier,"  2 CITY ".$g4p_a_repo['repo_city']."\r\n");
    g4p_fwrite($fichier,"  2 POST ".$g4p_a_repo['repo_post']."\r\n");
    g4p_fwrite($fichier,"  2 CTRY ".$g4p_a_repo['repo_ctry']."\r\n");
    g4p_fwrite($fichier," 1 PHON ".$g4p_a_repo['repo_phon']."\r\n");

// CHAN
    if(!empty($g4p_a_repo['repo_chan']) and $g4p_a_repo['repo_chan']!='0000-00-00 00:00:00')
    {
      $g4p_a_repo['repo_chan']=explode(' ',$g4p_a_repo['repo_chan']);
      g4p_fwrite($fichier," 1 CHAN\r\n");
      setlocale(LC_TIME, 'en');
      g4p_fwrite($fichier,"  2 DATE ".g4p_strftime('%d %b %Y',strtotime($g4p_a_repo['repo_chan'][0]))."\r\n");
      setlocale(LC_TIME, 'fr');
      g4p_fwrite($fichier,"   3 TIME ".$g4p_a_repo['repo_chan'][1]."\r\n");
    }
  }
}


g4p_fwrite($fichier,'0 TRLR');
fclose($fichier);

require_once($g4p_chemin.'include_sys/zip.lib.php');
$filename = $g4p_base_select.'.ged';
    
// contenu du fichier
$fp = fopen ($g4p_chemin.'cache/'.$g4p_base_select.'/fichiers/'.$g4p_base_select.'.ged', 'rb');
$content = fread($fp, filesize($g4p_chemin.'cache/'.$g4p_base_select.'/fichiers/'.$g4p_base_select.'.ged'));
fclose ($fp);
    
//création d'un objet 'zipfile'
$zip = new zipfile();
// ajout du fichier dans cet objet
$zip->addfile($content, $filename);

if($_POST['g4p_medias']=='oui')
{
  $sql="SELECT format, file
        FROM genea_multimedia WHERE base=".$_POST['base_rapport']." GROUP BY file";
  $g4p_result_req=g4p_db_query($sql);
  if($g4p_media_liste=g4p_db_result($g4p_result_req, 'file'))
  {
    foreach($g4p_media_liste as $g4p_a_media)
    {
      if($g4p_a_media['format']!='URL')
      {
        $filename = $g4p_a_media['file'];
        // contenu du fichier
        $fp = fopen ($g4p_chemin.'cache/'.$g4p_base_select.'/objets/'.$g4p_a_media['file'], 'rb');
        $content = fread($fp, filesize($g4p_chemin.'cache/'.$g4p_base_select.'/objets/'.$g4p_a_media['file']));
        fclose ($fp);
        
        // ajout du fichier dans cet objet
        $zip->addfile($content, $filename);
      }
    }
  }
}
// production de l'archive Zip
$nom_fichier=uniqid('ged_');
$fp = fopen ($g4p_chemin.'cache/'.$g4p_base_select.'/fichiers/'.$nom_fichier.'.zip', 'wb');
fwrite($fp,$zip->file());
fclose ($fp);

//sauvegarde du fichier en bd
if($_POST['g4p_parm_desc']=='oui')
{
  $_POST['desc_rapport'].="\n\n";
  $_POST['desc_rapport'].='encodage : '.strtoupper($_POST['g4p_encodage'])."\n";
  $_POST['desc_rapport'].='notes : '.$_POST['g4p_notes']."\n";
  $_POST['desc_rapport'].='sources : '.$_POST['g4p_sources']."\n";
  $_POST['desc_rapport'].='medias : '.$_POST['g4p_medias']."\n";
}

$sql="INSERT INTO genea_download (base, fichier, titre, description) VALUES (".$_POST['base_rapport'].",'".$nom_fichier.".zip','".mysql_escape_string($_POST['nom_rapport'])."','".mysql_escape_string($_POST['desc_rapport'])."')";
$g4p_result_req=g4p_db_query($sql);

$_SESSION['message']='Le GEDCOM a été crée avec succès';
header('location:'.g4p_make_url('admin','export_rapport.php','base='.$_POST['base_rapport'],0));

?>
