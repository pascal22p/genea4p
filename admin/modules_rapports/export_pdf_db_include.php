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
 *                Export la base au format PDF (bibliothèque)               *
 *                                                                          *
 * date de cération : 29/10/2005                                            *
 * En cas de problème : http://www.parois.net                               *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


function g4p_pdf_write_event($event)
{
  global $g4p_individuals_table, $g4p_pdflink, $g4p_events_table, $g4p_notes_liste, $g4p_sources_liste, $g4p_repo_liste, $g4p_media_liste, $g4p_familles_table, $g4p_familles_table2, $g4p_child_table1, $g4p_child_table2, $g4p_rel_events_multimedia, $g4p_rel_events_notes, $g4p_rel_events_sources, $g4p_rel_indi_multimedia, $g4p_rel_indi_notes, $g4p_rel_indi_sources, $g4p_rel_familles_multimedia, $g4p_rel_familles_notes, $g4p_rel_familles_sources, $g4p_rel_familles_events, $g4p_rel_indi_events, $g4p_rel_sources_medias, $g4p_rel_asso_events, $g4p_rel_asso_indi, $g4p_place, $g4p_tag_def, $pdf, $g4p_tag_iattributs, $g4p_langue;

  $pdf->SetFont('times','UI',10);
  if(isset($g4p_tag_iattributs[$g4p_events_table[$event]['type']]))
  {
    $g4p_texte=$g4p_tag_def[$g4p_events_table[$event]['type']];
    $pdf->write(5,$g4p_texte);
    $g4p_texte='';
    if(!empty($g4p_events_table[$event]['date_event']))
      $g4p_texte.=' ('.g4p_date($g4p_events_table[$event]['date_event']).')';
    if($g4p_events_table[$event]['description'])
      $g4p_texte.=' : '.$g4p_events_table[$event]['description'];
    $pdf->SetFont('times','',10);
    $pdf->write(5,$g4p_texte);
  }
  else
  {
    $g4p_texte=$g4p_tag_def[$g4p_events_table[$event]['type']];
    $pdf->write(5,$g4p_texte);
    $g4p_texte='';
    if(!empty($g4p_events_table[$event]['date_event']))
      $g4p_texte.=' : '.g4p_date($g4p_events_table[$event]['date_event']);
    $pdf->SetFont('times','',10);
    $pdf->write(5,$g4p_texte);
  }

  $g4p_texte='';
  if($g4p_events_table[$event]['place_id'])
    $g4p_texte.='à '.implode(', ',$g4p_place[$g4p_events_table[$event]['place_id']]);
  $pdf->ln();
  $pdf->SetFont('times','I',9);
  $pdf->write(5,$g4p_texte);
  $g4p_texte='';
  $pdf->ln();

/*
  if($_POST['g4p_sources']=='oui')
    if(isset($g4p_rel_events_sources[$event]))
      $g4p_texte.=' (S : '.implode(',',$g4p_rel_events_sources[$event]).')';
  if($_POST['g4p_notes']=='oui')
    if(isset($g4p_rel_events_notes[$event]))
      $g4p_texte.=' (N : '.implode(',',$g4p_rel_events_notes[$event]).')';
  if($_POST['g4p_medias']=='oui')
    if(isset($g4p_rel_events_multimedia[$event]))
      $g4p_texte.=' (M : '.implode(',',$g4p_rel_events_multimedia[$event]).')';
*/

//relations à faire

  if(isset($g4p_rel_events_sources[$event]))
  {
    foreach($g4p_rel_events_sources[$event] as $a_source)
    {
      g4p_pdf_write_source($g4p_sources_liste[$a_source]);
      $pdf->ln(0.5);
    }
  }
  if(isset($g4p_rel_events_notes[$event]))
  {
    foreach($g4p_rel_events_notes[$event] as $g4p_a_note)
    {
      g4p_pdf_write_note($g4p_notes_liste[$g4p_a_note]);
      $pdf->ln(0.5);
    }
  }
  if(isset($g4p_rel_events_multimedia[$event]))
  {
    foreach($g4p_rel_events_multimedia[$event] as $g4p_a_media)
    {
      g4p_pdf_write_media($g4p_media_liste[$g4p_a_media]);
      $pdf->ln(0.5);
    }
  }

}

function g4p_pdf_write_source($source)
{
  global $g4p_individuals_table, $g4p_pdflink, $g4p_events_table, $g4p_notes_liste, $g4p_sources_liste, $g4p_repo_liste, $g4p_media_liste, $g4p_familles_table, $g4p_familles_table2, $g4p_child_table1, $g4p_child_table2, $g4p_rel_events_multimedia, $g4p_rel_events_notes, $g4p_rel_events_sources, $g4p_rel_indi_multimedia, $g4p_rel_indi_notes, $g4p_rel_indi_sources, $g4p_rel_familles_multimedia, $g4p_rel_familles_notes, $g4p_rel_familles_sources, $g4p_rel_familles_events, $g4p_rel_indi_events, $g4p_rel_sources_medias, $g4p_rel_asso_events, $g4p_rel_asso_indi, $g4p_place, $g4p_tag_def, $pdf, $g4p_tag_iattributs, $g4p_langue;

  $pdf->SetFont('times','IBU',8);
  $pdf->setleftmargin(30);
  $g4p_x=$pdf->getx();
  $g4p_y=$pdf->gety();
  $pdf->write(4,'Source : '.$source['sources_title']);
  $pdf->ln();
  $pdf->SetFont('times','',8);
  if(!empty($source['sources_title']))
  {
    $pdf->write(4,'Auteur : '.$source['sources_title']);
    $pdf->ln();
  }
  if(!empty($source['sources_caln']))
  {
    $pdf->write(4,'Cote : '.$source['sources_caln']);
    $pdf->ln();
  }
  if(!empty($source['sources_page']))
  {
    $pdf->write(4,'Page : '.$source['sources_page']);
    $pdf->ln();
  }
  if(!empty($source['sources_medi']))
  {
    $pdf->write(4,'Support : '.$source['sources_medi']);
    $pdf->ln();
  }
  $pdf->SetFont('times','I',8);
  $g4p_a_source_text=($source['sources_text'])?(trim($source['sources_text'])):('');
  $pdf->write(4,$g4p_a_source_text);
  $pdf->ln();
  $pdf->setleftmargin(PDF_MARGIN_LEFT);
/*
  if($pdf->gety()<$g4p_y)
    $pdf->rect(10,10,120,$pdf->gety()-$g4p_y,'D');
  else
    $pdf->rect($g4p_x,$g4p_y,120,$pdf->gety()-$g4p_y,'D');*/
}

function g4p_pdf_write_note($note)
{
global $g4p_individuals_table, $g4p_pdflink, $g4p_events_table, $g4p_notes_liste, $g4p_sources_liste, $g4p_repo_liste, $g4p_media_liste, $g4p_familles_table, $g4p_familles_table2, $g4p_child_table1, $g4p_child_table2, $g4p_rel_events_multimedia, $g4p_rel_events_notes, $g4p_rel_events_sources, $g4p_rel_indi_multimedia, $g4p_rel_indi_notes, $g4p_rel_indi_sources, $g4p_rel_familles_multimedia, $g4p_rel_familles_notes, $g4p_rel_familles_sources, $g4p_rel_familles_events, $g4p_rel_indi_events, $g4p_rel_sources_medias, $g4p_rel_asso_events, $g4p_rel_asso_indi, $g4p_place, $g4p_tag_def, $pdf, $g4p_tag_iattributs, $g4p_langue;

  $pdf->SetFont('times','IBU',8);
  $pdf->setleftmargin(30);
  $g4p_x=$pdf->getx();
  $g4p_y=$pdf->gety();
  $pdf->write(4,"Note : \n");
  $pdf->SetFont('times','I',8);
  $pdf->write(4,trim($note['notes_text']));
  //$pdf->rect($g4p_x,$g4p_y,120,$pdf->gety()-$g4p_y,'D');
  $pdf->setleftmargin(10);
  $pdf->ln();
}

function g4p_pdf_write_media($media)
{
global $g4p_individuals_table, $g4p_pdflink, $g4p_events_table, $g4p_notes_liste, $g4p_sources_liste, $g4p_repo_liste, $g4p_media_liste, $g4p_familles_table, $g4p_familles_table2, $g4p_child_table1, $g4p_child_table2, $g4p_rel_events_multimedia, $g4p_rel_events_notes, $g4p_rel_events_sources, $g4p_rel_indi_multimedia, $g4p_rel_indi_notes, $g4p_rel_indi_sources, $g4p_rel_familles_multimedia, $g4p_rel_familles_notes, $g4p_rel_familles_sources, $g4p_rel_familles_events, $g4p_rel_indi_events, $g4p_rel_sources_medias, $g4p_rel_asso_events, $g4p_rel_asso_indi, $g4p_place, $g4p_tag_def, $pdf, $g4p_tag_iattributs, $g4p_langue;
//SELECT id, title, format, file, chan
  $pdf->SetFont('times','IBU',8);
  $pdf->setleftmargin(30);
  $g4p_x=$pdf->getx();
  $g4p_y=$pdf->gety();
  $pdf->write(4,"Média : ");
  $pdf->SetFont('times','I',8);
  $pdf->write(4,trim($media['title']));
  $pdf->ln();
  $pdf->write(4,trim($media['format']));
  $pdf->ln();

  if($media['format']!='URL')
    $pdf->write(4,trim($media['file']),trim($media['file']));
  else
  {
    $pdf->write(4,trim($media['file']),trim($media['file']));
  }

  //$pdf->rect($g4p_x,$g4p_y,120,$pdf->gety()-$g4p_y,'D');
  $pdf->setleftmargin(10);
  $pdf->ln();
}

function g4p_pdf_write_famille($famille)
{
  global $g4p_individuals_table, $g4p_pdflink, $g4p_events_table, $g4p_notes_liste, $g4p_sources_liste, $g4p_repo_liste, $g4p_media_liste, $g4p_familles_table, $g4p_familles_table2, $g4p_child_table1, $g4p_child_table2, $g4p_rel_events_multimedia, $g4p_rel_events_notes, $g4p_rel_events_sources, $g4p_rel_indi_multimedia, $g4p_rel_indi_notes, $g4p_rel_indi_sources, $g4p_rel_familles_multimedia, $g4p_rel_familles_notes, $g4p_rel_familles_sources, $g4p_rel_familles_events, $g4p_rel_indi_events, $g4p_rel_sources_medias, $g4p_rel_asso_events, $g4p_rel_asso_indi, $g4p_place, $g4p_tag_def, $pdf, $g4p_tag_iattributs, $g4p_langue;

  $pdf->SetFont('times','UI',10);
  $g4p_x=$pdf->getx();
  $g4p_y=$pdf->gety();      
  if(!empty($g4p_familles_table[$famille['familles_id']]['familles_husb']) and $g4p_familles_table[$famille['familles_id']]['familles_husb']!=$g4p_a_individu['indi_id'])
    $pdf->write(5,'Conjoint : '.$g4p_individuals_table[$g4p_familles_table[$famille['familles_id']]['familles_husb']]['indi_nom'].' '. $g4p_individuals_table[$g4p_familles_table[$famille['familles_id']]['familles_husb']]['indi_prenom'], $g4p_pdflink['I'.$g4p_individuals_table[$g4p_familles_table[$famille['familles_id']]['familles_husb']]['indi_id']]);
  elseif(!empty($g4p_familles_table[$famille['familles_id']]['familles_wife']) and $g4p_familles_table[$famille['familles_id']]['familles_wife']!=$g4p_a_individu['indi_id'])
    $pdf->write(5,'Conjoint : '.$g4p_individuals_table[$g4p_familles_table[$famille['familles_id']]['familles_wife']]['indi_nom'].' '. $g4p_individuals_table[$g4p_familles_table[$famille['familles_id']]['familles_wife']]['indi_prenom'], $g4p_pdflink['I'.$g4p_individuals_table[$g4p_familles_table[$famille['familles_id']]['familles_wife']]['indi_id']]);
  else
    $pdf->write(5,'Conjoint Incconu(e)');
  $pdf->ln(5);
  
  //evenements familiaux
  if(isset($g4p_rel_familles_events[$famille['familles_id']]))
  {
    foreach($g4p_rel_familles_events[$famille['familles_id']] as $g4p_a_fevent)
    {
      g4p_pdf_write_event($g4p_a_fevent);
    }
  }
  
  //les enfants des mariages
  $g4p_texte='';
  if(isset($g4p_child_table2[$famille['familles_id']]))
  {
    $pdf->SetFont('times','UI',10);
    $pdf->write(5,"\n    Enfants issus du mariage :\n");
    $pdf->SetFont('times','',10);     
    foreach($g4p_child_table2[$famille['familles_id']] as $g4p_a_enfant)
      $pdf->write(5,$g4p_individuals_table[$g4p_a_enfant['indi_id']]['indi_nom'].' '. $g4p_individuals_table[$g4p_a_enfant['indi_id']]['indi_prenom']."\r\n", $g4p_pdflink['I'.$g4p_individuals_table[$g4p_a_enfant['indi_id']]['indi_id']]);
  }
  
  $pdf->ln();
  if(isset($g4p_rel_familles_sources[$famille['familles_id']]))
  {
    foreach($g4p_rel_familles_sources[$famille['familles_id']] as $a_source)
    {
      g4p_pdf_write_source($g4p_sources_liste[$a_source]);
      $pdf->ln(0.5);
    }
  }
  if(isset($g4p_rel_familles_notes[$famille['familles_id']]))
  {
    foreach($g4p_rel_familles_notes[$famille['familles_id']] as $g4p_a_note)
    {
      g4p_pdf_write_note($g4p_notes_liste[$g4p_a_note]);
      $pdf->ln(0.5);
    }
  }
  if(isset($g4p_rel_familles_multimedia[$famille['familles_id']]))
  {
    foreach($g4p_rel_familles_multimedia[$famille['familles_id']] as $g4p_a_media)
    {
      g4p_pdf_write_media($g4p_media_liste[$g4p_a_media]);
      $pdf->ln(0.5);
    }
  }
  
  $pdf->ln(5);

}

?>
