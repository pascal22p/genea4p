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
 *                    Export de la base en PDF                             *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='../../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'admin/modules_rapports/export_pdf_db_include.php');
require_once($g4p_chemin.'admin/modules_rapports/liste_module_rapport.php');
require_once($g4p_chemin.'tcpdf/config/lang/eng.php');
require_once($g4p_chemin.'tcpdf/tcpdf.php');
require_once($g4p_chemin.'tcpdf/table/fpdf_table.php');

$time_start = g4p_getmicrotime();

//on verifie que c'est bien la base de l'admin, si ce n'est pas un super-admin
$sql="SELECT id FROM genea_permissions WHERE id_membre=".$_SESSION['g4p_id_membre']." AND type=".$g4p_module_export['export_pdf_db.php']['permission']." AND permission=1 and (id_base='".$_POST['base_rapport']."' OR id_base='*')";
if($g4p_infos_req=g4p_db_query($sql))
{
  $g4p_result=g4p_db_result($g4p_infos_req);
  if(!$g4p_result)
  {
    echo $g4p_langue['acces_admin'];
    exit;
  }
}
else
{
  echo $g4p_langue['acces_admin'];
  exit;
}

$sql="SELECT nom FROM genea_infos WHERE id=".$_POST['base_rapport'];
$g4p_infos_req=g4p_db_query($sql);
$g4p_base_select=g4p_db_result($g4p_infos_req);
$genea_db_nom=$g4p_base_select[0]['nom'];

  
$pdf=new TCPDF('P', 'mm', 'A4', true);
$pdf->AddFont('times','','vera.php');
$pdf->AddFont('times','B','verab.php');
$pdf->AddFont('times','BI','verabi.php');
$pdf->AddFont('times','I','verai.php'); 
$pdf->SetFont('times','',10);

$pdf->SetCreator('http://www.parois.net');
$pdf->SetAuthor('PAROIS Pascal');
$pdf->SetTitle('Base de donnée de la famille'.$genea_db_nom);

$pdf->SetHeaderData('','','','');
$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(15);

$pdf->AliasNbPages();

$pdf->SetAutoPageBreak(TRUE,15);
$pdf->setLanguageArray($l); //set language items

$pdf->open();
$pdf->AddPage();

// Les requètes SQL
$sql="SELECT indi_nom , indi_prenom , indi_sexe, indi_id, indi_chan, npfx, givn, nick, spfx, surn, nsfx
         FROM genea_individuals WHERE base=".$_POST['base_rapport']." ORDER BY indi_nom, indi_prenom";
$g4p_infos_req=g4p_db_query($sql);
$g4p_individuals_table=g4p_db_result($g4p_infos_req, 'indi_id');
foreach($g4p_individuals_table as $tmp)
  $g4p_pdflink['I'.$tmp['indi_id']]=$pdf->AddLink();


$sql="SELECT id, type, date_event, age, description, cause, place_id, attestation
    FROM genea_events WHERE base=".$_POST['base_rapport'];
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

  $sql="SELECT repo_id, repo_name, repo_addr, repo_city, repo_post, repo_ctry, repo_phon1, repo_phon2, repo_phon3, repo_chan
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

$g4p_chps=implode($g4p_config['lieu_separateur'],$g4p_config['subdivision']);
$sql="SELECT place_id, $g4p_chps FROM genea_place WHERE base=".$_POST['base_rapport'];
$g4p_result_req=g4p_db_query($sql);
$g4p_place=g4p_db_result($g4p_result_req,'place_id');

// le serveur est toujours en vie ? Les requètes sont chargés en mémoire


//Construction du sommaire

$pdf->SetFont('times','',10);
/*
foreach($g4p_individuals_table as $g4p_a_individu)
{
  if(isset($cache_count) AND $cache_count>_NBRE_MAX_CACHE_CREES_)
  {
    echo 'Trop de fichiers caches à générer!';
    exit;
  }
  $individu_infos=g4p_load_indi_infos($g4p_a_individu['indi_id']);
  $pdf->writehtml($individu_infos->nom.' '.$individu_infos->prenom.' '.$individu_infos->date_rapide.'<br />',0,1);
}
$pdf->AddPage();
*/

// coeur du pdf
foreach($g4p_individuals_table as $g4p_a_individu)
{
  $pdf->SetFont('times','',8);
  if($g4p_a_individu['indi_chan']!='0000-00-00 00:00:00')
    $g4p_chan=g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_a_individu['indi_chan']));
  $pdf->ln();
  if(isset($g4p_privacy) and in_array($g4p_a_individu['indi_id'],$g4p_privacy))
  {
  	$pdf->write(5,'Privé');
  	$pdf->ln();
  }
  $pdf->SetFont('times','B',16);
  $pdf->SetLink($g4p_pdflink['I'.$g4p_a_individu['indi_id']]);
  $pdf->cell(0,5,$g4p_a_individu['indi_nom'].' '.$g4p_a_individu['indi_prenom'],0,1);
  $pdf->ln(5);

  $pdf->SetFont('times','',10);
  $pdf->writehtml($g4p_langue['index_sexe'].$g4p_langue['index_sexe_valeur'][$g4p_a_individu['indi_sexe']]);
  if($g4p_a_individu['npfx'])
    $pdf->writehtml($g4p_langue['index_npfx'].$g4p_a_individu['npfx']);
  if($g4p_a_individu['givn'])
    $pdf->writehtml($g4p_langue['index_givn'].$g4p_a_individu['givn']);
  if($g4p_a_individu['nick'])
    $pdf->writehtml($g4p_langue['index_nick'].$g4p_a_individu['npfx']);
  if($g4p_a_individu['spfx'])
    $pdf->writehtml($g4p_langue['index_spfx'].$g4p_a_individu['spfx']);
  if($g4p_a_individu['nsfx'])
    $pdf->writehtml($g4p_langue['index_nsfx'].$g4p_a_individu['nsfx']);
  $pdf->ln();

//evenements individuels
  if(isset($g4p_rel_indi_events[$g4p_a_individu['indi_id']]))
  {
    foreach($g4p_rel_indi_events[$g4p_a_individu['indi_id']] as $g4p_a_ievents)
    {
      g4p_pdf_write_event($g4p_a_ievents); 
    }
  }

// Les conjoints
  if(isset($g4p_familles_table2[$g4p_a_individu['indi_id']]))
  {
    $pdf->ln(5);
    foreach($g4p_familles_table2[$g4p_a_individu['indi_id']] as $g4p_a_famille)//affiche tous les mariages
    {
      g4p_pdf_write_famille($g4p_a_famille);
    }
  }
  else
  {
    $pdf->ln(10);
    $g4p_x=$pdf->getx();
    $g4p_y=$pdf->gety();
    $pdf->write(5,"Aucun mariage\n");
    $pdf->rect($g4p_x,$g4p_y,180,$pdf->gety()-$g4p_y,'D');
  }

// les parents
  if (isset($g4p_child_table1[$g4p_a_individu['indi_id']]))
  {
    $pdf->ln(10);
    foreach($g4p_child_table1[$g4p_a_individu['indi_id']] as $g4p_a_parent)
    {
      $pdf->SetFont('times','UI',10);
      $pdf->write(5,'Parents '.str_replace(array_keys($g4p_lien_def), array_values($g4p_lien_def),$g4p_a_parent['rela_type']));
      $pdf->SetFont('times','',10);
      $pdf->ln();
      if(isset($g4p_individuals_table[$g4p_familles_table[$g4p_a_parent['familles_id']]['familles_husb']]))
        $pdf->write(5,$g4p_individuals_table[$g4p_familles_table[$g4p_a_parent['familles_id']]['familles_husb']]['indi_nom'].' '.$g4p_individuals_table[$g4p_familles_table[$g4p_a_parent['familles_id']]['familles_husb']]['indi_prenom']."\r\n", $g4p_pdflink['I'.$g4p_individuals_table[$g4p_familles_table[$g4p_a_parent['familles_id']]['familles_husb']]['indi_id']]);
      else
        $pdf->write(5,"Père inconnu\r\n");

      if(isset($g4p_individuals_table[$g4p_familles_table[$g4p_a_parent['familles_id']]['familles_wife']]))
          $pdf->write(5,$g4p_individuals_table[$g4p_familles_table[$g4p_a_parent['familles_id']]['familles_wife']]['indi_nom'].' '.$g4p_individuals_table[$g4p_familles_table[$g4p_a_parent['familles_id']]['familles_wife']]['indi_prenom']."\r\n", $g4p_pdflink['I'.$g4p_individuals_table[$g4p_familles_table[$g4p_a_parent['familles_id']]['familles_wife']]['indi_id']]);
      else
        $pdf->write(5,"mère inconnue\r\n");
    }
  }

  $pdf->ln();
  $g4p_text='';
  if($_POST['g4p_sources']=='oui' and isset($g4p_rel_indi_sources[$g4p_a_individu['indi_id']]))
    $g4p_text.='Sources : '.implode(',',$g4p_rel_indi_sources[$g4p_a_individu['indi_id']]).'<br />';
  if($_POST['g4p_notes']=='oui' and isset($g4p_rel_indi_notes[$g4p_a_individu['indi_id']]))
    $g4p_text.='Notes : '.implode(',',$g4p_rel_indi_notes[$g4p_a_individu['indi_id']]).'<br />';
  if($_POST['g4p_medias']=='oui' and isset($g4p_rel_indi_multimedia[$g4p_a_individu['indi_id']]))
    $g4p_text.='Médias : '.implode(',',$g4p_rel_indi_multimedia[$g4p_a_individu['indi_id']]).'<br />';
  $pdf->writehtml($g4p_text);
  $pdf->AddPage();
}

//Les notes
if($_POST['g4p_notes']=='oui' and is_array($g4p_notes_liste))
{
  $pdf->AddPage();
  $pdf->SetFont('times','B',16);
  $pdf->cell(0,5,'Les Notes',0,1);
  $pdf->ln(5);  
  $pdf->SetFont('times','',10);
  foreach($g4p_notes_liste as $g4p_a_note)
  {
    if(isset($g4p_a_note['notes_chan']) and $g4p_a_note['notes_chan']!='0000-00-00 00:00:00')
      $chan=' - '.g4p_strftime($g4p_langue['date_complete'],strtotime($g4p_a_note['notes_chan']));
    else
      $chan='';
    $pdf->writehtml('<i>'.$g4p_a_note['notes_id'].$chan.'</i>');
    $pdf->writehtml(nl2br(trim($g4p_a_note['notes_text'])));
    $pdf->ln(5);
  }
}
//les sources
if($_POST['g4p_sources']=='oui'and $g4p_sources_liste)
{
  $pdf->AddPage();
  $pdf->SetFont('times','B',16);
  $pdf->cell(0,5,'Les Sources',0,1);
  $pdf->ln(5);  
  $pdf->SetFont('times','',10);
  foreach($g4p_sources_liste as $g4p_a_source)
  {
    if(isset($g4p_a_source['sources_chan']) and $g4p_a_source['sources_chan']!='0000-00-00 00:00:00')
      $chan=' - '.g4p_strftime($g4p_langue['date_complete'],strtotime($g4p_a_source['sources_chan']));
    else
      $chan='';
    $pdf->writehtml('<i>'.$g4p_a_source['sources_id'].$chan.'</i>');
    $pdf->writehtml('Titre : '.$g4p_a_source['sources_title']);
    $pdf->writehtml('Auteur : '.$g4p_a_source['sources_auth']);
    $pdf->writehtml('Page : '.$g4p_a_source['sources_page']);
    if(!empty($g4p_a_source['repo_id']))
    {
      $pdf->writehtml('Dépot : '.$g4p_a_source['repo_id']);
      $pdf->writehtml('Caln : '.$g4p_a_source['sources_caln']);
      $pdf->writehtml('Medi : '.$g4p_a_source['sources_medi']);
    }
    $pdf->writehtml('Texte : '.nl2br(trim($g4p_a_source['sources_text'])));
    $pdf->writehtml('Publ : '.nl2br(trim($g4p_a_source['sources_publ'])));

    //les médias
    if($_POST['g4p_medias']=='oui' and isset($g4p_rel_sources_medias[$g4p_a_source['sources_id']]))
      $pdf->writehtml('Médias : '.implode(', ',$g4p_rel_sources_medias[$g4p_a_source['sources_id']]));
    $pdf->ln(5);  
  }
}

//les dépots
if($_POST['g4p_sources']=='oui' and $g4p_repo_liste)
{
  $pdf->AddPage();
  $pdf->SetFont('times','B',16);
  $pdf->cell(0,5,'Les dépôts',0,1);
  $pdf->ln(5);  
  $pdf->SetFont('times','',10);
  foreach($g4p_repo_liste as $g4p_a_repo)
  {
    if(isset($g4p_a_repo['repo_chan']) and $g4p_a_repo['repo_chan']!='0000-00-00 00:00:00')
      $chan=' - '.g4p_strftime($g4p_langue['date_complete'],strtotime($g4p_a_repo['repo_chan']));
    else
      $chan=''; 
    $pdf->writehtml('<i>'.$g4p_a_repo['repo_id'].$chan.'</i>');
    $pdf->writehtml('Nom : '.$g4p_a_repo['repo_name']);
    $pdf->writehtml('Adresse : '.$g4p_a_repo['repo_addr']);
    $pdf->writehtml('Ville : '.$g4p_a_repo['repo_city']);
    $pdf->writehtml('Code postal : '.$g4p_a_repo['repo_post']);
    $pdf->writehtml('Pays : '.$g4p_a_repo['repo_ctry']);
    $pdf->writehtml('Téléphone 1 : '.$g4p_a_repo['repo_phon1']);
    $pdf->writehtml('Téléphone 2 : '.$g4p_a_repo['repo_phon2']);
    $pdf->writehtml('Téléphone 3 : '.$g4p_a_repo['repo_phon3']);
    $pdf->ln(5);      
  }
}

$nom_fichier=uniqid('pdf_');
$pdf->Output($g4p_chemin.'cache/'.$genea_db_nom.'/fichiers/'.$nom_fichier.'.pdf',false);

//sauvegarde du fichier en bd
if($_POST['g4p_parm_desc']=='oui')
{
  $_POST['desc_rapport'].="\n\n";
  $_POST['desc_rapport'].='encodage : '.strtoupper($_POST['g4p_encodage'])."\n";
  $_POST['desc_rapport'].='notes : '.$_POST['g4p_notes']."\n";
  $_POST['desc_rapport'].='sources : '.$_POST['g4p_sources']."\n";
  $_POST['desc_rapport'].='medias : '.$_POST['g4p_medias']."\n";
}

$sql="INSERT INTO genea_download (base, fichier, titre, description) VALUES (".$_POST['base_rapport'].",'".$nom_fichier.".pdf','".mysql_escape_string($_POST['nom_rapport'])."','".mysql_escape_string($_POST['desc_rapport'])."')";
$g4p_result_req=g4p_db_query($sql);

$time_end = g4p_getmicrotime();
$_SESSION['message'] = sprintf($g4p_langue['message_gen_pdf'],intval( ($time_end - $time_start)));

//header('location:'.g4p_make_url('admin','export_rapport.php','base='.$_POST['base_rapport'],0));
?>
