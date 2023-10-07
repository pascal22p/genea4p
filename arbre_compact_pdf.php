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
 *                 Export de l'arbre ascendant en PDF                      *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.phpalbum.org                            *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';
require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

function g4p_ascendance($g4p_generation, $g4p_id, $g4p_index)
{
  global $g4p_resultat, $cw, $g4p_tag_def, $g4p_chemin, $pdf, $g4p_sexe;
//  echo '<div id="icone">blab<br />bla</div>';
  unset($g4p_mon_indi);
  $g4p_mon_indi=g4p_load_indi_infos($g4p_id);

  $g4p_nom_aff=$g4p_nom=$g4p_mon_indi->nom.' '.$g4p_mon_indi->prenom;

  //Get width of a string in the current font
  $g4p_nom_aff=$g4p_nom;
  while($pdf->GetStringWidth($g4p_nom_aff)>34)
    $g4p_nom_aff=substr($g4p_nom_aff,0,-1);  

  if ($g4p_generation==0)
    $g4p_sexe=$g4p_mon_indi->sexe='M';

//evenements individuels

// les parents
  if(isset($g4p_mon_indi->parents))
  {
    foreach($g4p_mon_indi->parents as $g4p_parents)
    {
      if(strtolower($g4p_parents->rela_type)=='birth' or $g4p_parents->rela_type=='')
      {
        $g4p_generation++;
        if(isset($g4p_parents->pere) and $g4p_generation<6)
          g4p_ascendance($g4p_generation, $g4p_parents->pere->indi_id, $g4p_index.$g4p_mon_indi->sexe);
        if(isset($g4p_parents->mere) and $g4p_generation<6)
          g4p_ascendance($g4p_generation, $g4p_parents->mere->indi_id, $g4p_index.$g4p_mon_indi->sexe);
      }
    }
  }
  $g4p_naissance=$g4p_deces='';
  if(isset($g4p_mon_indi->evenements))
  {
    foreach($g4p_mon_indi->evenements as $g4p_a_event)
    {
      if($g4p_a_event->type=='BIRT' or $g4p_a_event->type=='BAPM')
      {
        $g4p_naissance=g4p_date($g4p_a_event->date,'date2');
        $g4p_naissance=preg_replace("'([a-z])*'", '', $g4p_naissance);
      }
      if($g4p_a_event->type=='DEAT' or $g4p_a_event->type=='BURI')
      {
        $g4p_deces=g4p_date($g4p_a_event->date,'date2');
        $g4p_deces='+'.preg_replace("'([a-z])*'", '', $g4p_deces);
      }
    }
  }
  $g4p_resultat[$g4p_index.$g4p_mon_indi->sexe]['nom']=$g4p_nom_aff;
  $g4p_resultat[$g4p_index.$g4p_mon_indi->sexe]['date']=$g4p_naissance.' '.$g4p_deces;
  $g4p_resultat[$g4p_index.$g4p_mon_indi->sexe]['nom_complet']=$g4p_mon_indi->nom.' '.$g4p_mon_indi->prenom;
}

if(isset($_GET['id_pers']))
{
  $g4p_indi=g4p_load_indi_infos((int)$_REQUEST['id_pers']);
  echo "DOne";
  g4p_forbidden_access($g4p_indi);
  echo "Pass";
//  require_once($g4p_chemin.'tcpdf/config/tcpdf_config.php');
//  require_once($g4p_chemin.'tcpdf/config/lang/eng.php');
  require_once($g4p_chemin.'tcpdf/tcpdf.php');
  
  $pdf=new TCPDF('L', 'mm', 'A4', true);
//  $pdf->addfont('vera','','vera.php');
//  $pdf->addfont('vera','b','verab.php');
//  $pdf->addfont('vera','i','verai.php');
//  $pdf->addfont('vera','bi','verabi.php');
//  $pdf->SetFont('vera','',10);
  
  $pdf->SetCreator('http://www.parois.net');
  $pdf->SetAuthor('PAROIS Pascal');
  $pdf->SetTitle('Arbre ascendant de : '.$g4p_indi->nom.' '.$g4p_indi->prenom);
  
  $pdf->SetHeaderData('', '', '', '');
  $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
  $pdf->SetAutoPageBreak(TRUE, 5);
  $pdf->SetHeaderMargin(0);
  $pdf->SetFooterMargin(0);
  
  //$pdf->setLanguageArray($l); //set language items
  
  $pdf->open();
  $pdf->AddPage();
  $pdf->SetFont('vera','',7);

  $g4p_resultat=array();
  g4p_ascendance(0, $_GET['id_pers'], '');

$x=5;
$y=15;
$largeur1=18;
$largeur2=5;
$hauteur=5;

$pdf->SetFont('vera','ub',14);
$pdf->multicell(0,6,'Ascendance de '.$g4p_resultat['M']['nom_complet'],0,'C');

//1ligne
$pdf->rect($x,$y,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*4+$largeur2*2,$y,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*8+$largeur2*4,$y,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*12+$largeur2*6,$y,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1,$y+$hauteur*2,$x+$largeur1,$y+$hauteur*3);
$pdf->line($x+$largeur1*5+$largeur2*2,$y+$hauteur*2,$x+$largeur1*5+$largeur2*2,$y+$hauteur*3);
$pdf->line($x+$largeur1*9+$largeur2*4,$y+$hauteur*2,$x+$largeur1*9+$largeur2*4,$y+$hauteur*3);
$pdf->line($x+$largeur1*13+$largeur2*6,$y+$hauteur*2,$x+$largeur1*13+$largeur2*6,$y+$hauteur*3);
//1ligne
$pdf->rect($x,$y+$hauteur*3,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*2,$y+$hauteur*4,$x+$largeur1*2+$largeur2,$y+$hauteur*4);
$pdf->rect($x+$largeur1*2+$largeur2,$y+$hauteur*3,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*4+$largeur2*1,$y+$hauteur*4,$x+$largeur1*4+$largeur2*2,$y+$hauteur*4);
$pdf->rect($x+$largeur1*4+$largeur2*2,$y+$hauteur*3,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*8+$largeur2*4,$y+$hauteur*3,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*10+$largeur2*4,$y+$hauteur*4,$x+$largeur1*10+$largeur2*5,$y+$hauteur*4);
$pdf->rect($x+$largeur1*10+$largeur2*5,$y+$hauteur*3,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*12+$largeur2*5,$y+$hauteur*4,$x+$largeur1*12+$largeur2*6,$y+$hauteur*4);
$pdf->rect($x+$largeur1*12+$largeur2*6,$y+$hauteur*3,$largeur1*2,$hauteur*2);
//1ligne
$pdf->rect($x,$y+$hauteur*6,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*4+$largeur2*2,$y+$hauteur*6,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*8+$largeur2*4,$y+$hauteur*6,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*12+$largeur2*6,$y+$hauteur*6,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1,$y+$hauteur*5,$x+$largeur1,$y+$hauteur*6);
$pdf->line($x+$largeur1*5+$largeur2*2,$y+$hauteur*5,$x+$largeur1*5+$largeur2*2,$y+$hauteur*6);
$pdf->line($x+$largeur1*9+$largeur2*4,$y+$hauteur*5,$x+$largeur1*9+$largeur2*4,$y+$hauteur*6);
$pdf->line($x+$largeur1*13+$largeur2*6,$y+$hauteur*5,$x+$largeur1*13+$largeur2*6,$y+$hauteur*6);
//1ligne
$pdf->rect($x+$largeur1*2+$largeur2,$y+$hauteur*8,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*10+$largeur2*5,$y+$hauteur*8,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*6+$largeur2*3,$y+$hauteur*8,$largeur1*2,$hauteur*2);

$pdf->line($x+$largeur1*4+$largeur2,$y+$hauteur*9,$x+$largeur1*6+$largeur2*3,$y+$hauteur*9);
$pdf->line($x+$largeur1*8+$largeur2*3,$y+$hauteur*9,$x+$largeur1*10+$largeur2*5,$y+$hauteur*9);

$pdf->line($x+$largeur1*3+$largeur2,$y+$hauteur*5,$x+$largeur1*3+$largeur2,$y+$hauteur*8);
$pdf->line($x+$largeur1*11+$largeur2*5,$y+$hauteur*5,$x+$largeur1*11+$largeur2*5,$y+$hauteur*8);
$pdf->line($x+$largeur1*3+$largeur2,$y+$hauteur*10,$x+$largeur1*3+$largeur2,$y+$hauteur*13);
$pdf->line($x+$largeur1*11+$largeur2*5,$y+$hauteur*10,$x+$largeur1*11+$largeur2*5,$y+$hauteur*13);
//1ligne  2譥 motif
$pdf->rect($x,$y+$hauteur*10,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*4+$largeur2*2,$y+$hauteur*10,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*8+$largeur2*4,$y+$hauteur*10,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*12+$largeur2*6,$y+$hauteur*10,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1,$y+$hauteur*12,$x+$largeur1,$y+$hauteur*13);
$pdf->line($x+$largeur1*5+$largeur2*2,$y+$hauteur*12,$x+$largeur1*5+$largeur2*2,$y+$hauteur*13);
$pdf->line($x+$largeur1*9+$largeur2*4,$y+$hauteur*12,$x+$largeur1*9+$largeur2*4,$y+$hauteur*13);
$pdf->line($x+$largeur1*13+$largeur2*6,$y+$hauteur*12,$x+$largeur1*13+$largeur2*6,$y+$hauteur*13);
//1ligne
$pdf->rect($x,$y+$hauteur*13,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*2,$y+$hauteur*14,$x+$largeur1*2+$largeur2,$y+$hauteur*14);
$pdf->rect($x+$largeur1*2+$largeur2,$y+$hauteur*13,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*4+$largeur2*1,$y+$hauteur*14,$x+$largeur1*4+$largeur2*2,$y+$hauteur*14);
$pdf->rect($x+$largeur1*4+$largeur2*2,$y+$hauteur*13,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*8+$largeur2*4,$y+$hauteur*13,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*10+$largeur2*4,$y+$hauteur*14,$x+$largeur1*10+$largeur2*5,$y+$hauteur*14);
$pdf->rect($x+$largeur1*10+$largeur2*5,$y+$hauteur*13,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*12+$largeur2*5,$y+$hauteur*14,$x+$largeur1*12+$largeur2*6,$y+$hauteur*14);
$pdf->rect($x+$largeur1*12+$largeur2*6,$y+$hauteur*13,$largeur1*2,$hauteur*2);
//1ligne
$pdf->rect($x,$y+$hauteur*16,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*4+$largeur2*2,$y+$hauteur*16,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*8+$largeur2*4,$y+$hauteur*16,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*12+$largeur2*6,$y+$hauteur*16,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1,$y+$hauteur*15,$x+$largeur1,$y+$hauteur*16);
$pdf->line($x+$largeur1*5+$largeur2*2,$y+$hauteur*15,$x+$largeur1*5+$largeur2*2,$y+$hauteur*16);
$pdf->line($x+$largeur1*9+$largeur2*4,$y+$hauteur*15,$x+$largeur1*9+$largeur2*4,$y+$hauteur*16);
$pdf->line($x+$largeur1*13+$largeur2*6,$y+$hauteur*15,$x+$largeur1*13+$largeur2*6,$y+$hauteur*16);

//milieu
$pdf->rect($x+$largeur1*6+$largeur2*3,$y+$hauteur*18,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*7+$largeur2*3,$y+$hauteur*10,$x+$largeur1*7+$largeur2*3,$y+$hauteur*18);
$pdf->line($x+$largeur1*7+$largeur2*3,$y+$hauteur*20,$x+$largeur1*7+$largeur2*3,$y+$hauteur*28);
//1ligne
$pdf->rect($x,$y+$hauteur*20,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*4+$largeur2*2,$y+$hauteur*20,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*8+$largeur2*4,$y+$hauteur*20,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*12+$largeur2*6,$y+$hauteur*20,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1,$y+$hauteur*22,$x+$largeur1,$y+$hauteur*23);
$pdf->line($x+$largeur1*5+$largeur2*2,$y+$hauteur*22,$x+$largeur1*5+$largeur2*2,$y+$hauteur*23);
$pdf->line($x+$largeur1*9+$largeur2*4,$y+$hauteur*22,$x+$largeur1*9+$largeur2*4,$y+$hauteur*23);
$pdf->line($x+$largeur1*13+$largeur2*6,$y+$hauteur*22,$x+$largeur1*13+$largeur2*6,$y+$hauteur*23);
//1ligne
$pdf->rect($x,$y+$hauteur*23,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*2,$y+$hauteur*24,$x+$largeur1*2+$largeur2,$y+$hauteur*24);
$pdf->rect($x+$largeur1*2+$largeur2,$y+$hauteur*23,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*4+$largeur2*1,$y+$hauteur*24,$x+$largeur1*4+$largeur2*2,$y+$hauteur*24);
$pdf->rect($x+$largeur1*4+$largeur2*2,$y+$hauteur*23,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*8+$largeur2*4,$y+$hauteur*23,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*10+$largeur2*4,$y+$hauteur*24,$x+$largeur1*10+$largeur2*5,$y+$hauteur*24);
$pdf->rect($x+$largeur1*10+$largeur2*5,$y+$hauteur*23,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*12+$largeur2*5,$y+$hauteur*24,$x+$largeur1*12+$largeur2*6,$y+$hauteur*24);
$pdf->rect($x+$largeur1*12+$largeur2*6,$y+$hauteur*23,$largeur1*2,$hauteur*2);
//1ligne
$pdf->rect($x,$y+$hauteur*26,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*4+$largeur2*2,$y+$hauteur*26,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*8+$largeur2*4,$y+$hauteur*26,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*12+$largeur2*6,$y+$hauteur*26,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1,$y+$hauteur*25,$x+$largeur1,$y+$hauteur*26);
$pdf->line($x+$largeur1*5+$largeur2*2,$y+$hauteur*25,$x+$largeur1*5+$largeur2*2,$y+$hauteur*26);
$pdf->line($x+$largeur1*9+$largeur2*4,$y+$hauteur*25,$x+$largeur1*9+$largeur2*4,$y+$hauteur*26);
$pdf->line($x+$largeur1*13+$largeur2*6,$y+$hauteur*25,$x+$largeur1*13+$largeur2*6,$y+$hauteur*26);
//1ligne
$pdf->rect($x+$largeur1*2+$largeur2,$y+$hauteur*28,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*10+$largeur2*5,$y+$hauteur*28,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*6+$largeur2*3,$y+$hauteur*28,$largeur1*2,$hauteur*2);

$pdf->line($x+$largeur1*4+$largeur2,$y+$hauteur*29,$x+$largeur1*6+$largeur2*3,$y+$hauteur*29);
$pdf->line($x+$largeur1*8+$largeur2*3,$y+$hauteur*29,$x+$largeur1*10+$largeur2*5,$y+$hauteur*29);

$pdf->line($x+$largeur1*3+$largeur2,$y+$hauteur*25,$x+$largeur1*3+$largeur2,$y+$hauteur*28);
$pdf->line($x+$largeur1*11+$largeur2*5,$y+$hauteur*25,$x+$largeur1*11+$largeur2*5,$y+$hauteur*28);
$pdf->line($x+$largeur1*3+$largeur2,$y+$hauteur*30,$x+$largeur1*3+$largeur2,$y+$hauteur*33);
$pdf->line($x+$largeur1*11+$largeur2*5,$y+$hauteur*30,$x+$largeur1*11+$largeur2*5,$y+$hauteur*33);
//1ligne  2譥 motif
$pdf->rect($x,$y+$hauteur*30,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*4+$largeur2*2,$y+$hauteur*30,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*8+$largeur2*4,$y+$hauteur*30,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*12+$largeur2*6,$y+$hauteur*30,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1,$y+$hauteur*32,$x+$largeur1,$y+$hauteur*33);
$pdf->line($x+$largeur1*5+$largeur2*2,$y+$hauteur*32,$x+$largeur1*5+$largeur2*2,$y+$hauteur*33);
$pdf->line($x+$largeur1*9+$largeur2*4,$y+$hauteur*32,$x+$largeur1*9+$largeur2*4,$y+$hauteur*33);
$pdf->line($x+$largeur1*13+$largeur2*6,$y+$hauteur*32,$x+$largeur1*13+$largeur2*6,$y+$hauteur*33);
//1ligne
$pdf->rect($x,$y+$hauteur*33,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*2,$y+$hauteur*34,$x+$largeur1*2+$largeur2,$y+$hauteur*34);
$pdf->rect($x+$largeur1*2+$largeur2,$y+$hauteur*33,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*4+$largeur2*1,$y+$hauteur*34,$x+$largeur1*4+$largeur2*2,$y+$hauteur*34);
$pdf->rect($x+$largeur1*4+$largeur2*2,$y+$hauteur*33,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*8+$largeur2*4,$y+$hauteur*33,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*10+$largeur2*4,$y+$hauteur*34,$x+$largeur1*10+$largeur2*5,$y+$hauteur*34);
$pdf->rect($x+$largeur1*10+$largeur2*5,$y+$hauteur*33,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1*12+$largeur2*5,$y+$hauteur*34,$x+$largeur1*12+$largeur2*6,$y+$hauteur*34);
$pdf->rect($x+$largeur1*12+$largeur2*6,$y+$hauteur*33,$largeur1*2,$hauteur*2);
//1ligne
$pdf->rect($x,$y+$hauteur*36,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*4+$largeur2*2,$y+$hauteur*36,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*8+$largeur2*4,$y+$hauteur*36,$largeur1*2,$hauteur*2);
$pdf->rect($x+$largeur1*12+$largeur2*6,$y+$hauteur*36,$largeur1*2,$hauteur*2);
$pdf->line($x+$largeur1,$y+$hauteur*35,$x+$largeur1,$y+$hauteur*36);
$pdf->line($x+$largeur1*5+$largeur2*2,$y+$hauteur*35,$x+$largeur1*5+$largeur2*2,$y+$hauteur*36);
$pdf->line($x+$largeur1*9+$largeur2*4,$y+$hauteur*35,$x+$largeur1*9+$largeur2*4,$y+$hauteur*36);
$pdf->line($x+$largeur1*13+$largeur2*6,$y+$hauteur*35,$x+$largeur1*13+$largeur2*6,$y+$hauteur*36);

//le texte
$y+=4;
$x+=1;
$pdf->SetFont('vera','',7);

//motif1
$pdf->text($x,$y,(isset($g4p_resultat['MMMMMM']['nom']))?($g4p_resultat['MMMMMM']['nom']):(''));
$pdf->text($x,$y+4,(isset($g4p_resultat['MMMMMM']['date']))?($g4p_resultat['MMMMMM']['date']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y,(isset($g4p_resultat['MMMMFM']['nom']))?($g4p_resultat['MMMMFM']['nom']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+4,(isset($g4p_resultat['MMMMFM']['date']))?($g4p_resultat['MMMMFM']['date']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y,(isset($g4p_resultat['MMFMMM']['nom']))?($g4p_resultat['MMFMMM']['nom']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+4,(isset($g4p_resultat['MMFMMM']['date']))?($g4p_resultat['MMFMMM']['date']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y,(isset($g4p_resultat['MMFMFM']['nom']))?($g4p_resultat['MMFMFM']['nom']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+4,(isset($g4p_resultat['MMFMFM']['date']))?($g4p_resultat['MMFMFM']['date']):(''));

$pdf->text($x,$y+$hauteur*3,(isset($g4p_resultat['MMMMM']['nom']))?($g4p_resultat['MMMMM']['nom']):(''));
$pdf->text($x,$y+$hauteur*3+4,(isset($g4p_resultat['MMMMM']['date']))?($g4p_resultat['MMMMM']['date']):(''));
$pdf->text($x+$largeur1*2+$largeur2,$y+$hauteur*3,(isset($g4p_resultat['MMMM']['nom']))?($g4p_resultat['MMMM']['nom']):(''));
$pdf->text($x+$largeur1*2+$largeur2,$y+$hauteur*3+4,(isset($g4p_resultat['MMMM']['date']))?($g4p_resultat['MMMM']['date']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*3,(isset($g4p_resultat['MMMMF']['nom']))?($g4p_resultat['MMMMF']['nom']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*3+4,(isset($g4p_resultat['MMMMF']['date']))?($g4p_resultat['MMMMF']['date']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*3,(isset($g4p_resultat['MMFMM']['nom']))?($g4p_resultat['MMFMM']['nom']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*3+4,(isset($g4p_resultat['MMFMM']['date']))?($g4p_resultat['MMFMM']['date']):(''));
$pdf->text($x+$largeur1*10+$largeur2*5,$y+$hauteur*3,(isset($g4p_resultat['MMFM']['nom']))?($g4p_resultat['MMFM']['nom']):(''));
$pdf->text($x+$largeur1*10+$largeur2*5,$y+$hauteur*3+4,(isset($g4p_resultat['MMFM']['date']))?($g4p_resultat['MMFM']['date']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*3,(isset($g4p_resultat['MMFMF']['nom']))?($g4p_resultat['MMFMF']['nom']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*3+4,(isset($g4p_resultat['MMFMF']['date']))?($g4p_resultat['MMFMF']['date']):(''));

$pdf->text($x,$y+$hauteur*6,(isset($g4p_resultat['MMMMMF']['nom']))?($g4p_resultat['MMMMMF']['nom']):(''));
$pdf->text($x,$y+$hauteur*6+4,(isset($g4p_resultat['MMMMMF']['date']))?($g4p_resultat['MMMMMF']['date']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*6,(isset($g4p_resultat['MMMMFF']['nom']))?($g4p_resultat['MMMMFF']['nom']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*6+4,(isset($g4p_resultat['MMMMFF']['date']))?($g4p_resultat['MMMMFF']['date']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*6,(isset($g4p_resultat['MMFMMF']['nom']))?($g4p_resultat['MMFMMF']['nom']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*6+4,(isset($g4p_resultat['MMFMMF']['date']))?($g4p_resultat['MMFMMF']['date']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*6,(isset($g4p_resultat['MMFMFF']['nom']))?($g4p_resultat['MMFMFF']['nom']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*6+4,(isset($g4p_resultat['MMFMFF']['date']))?($g4p_resultat['MMFMFF']['date']):(''));
//1er interm餩aire
$pdf->text($x+$largeur1*2+$largeur2,$y+$hauteur*8,(isset($g4p_resultat['MMM']['nom']))?($g4p_resultat['MMM']['nom']):(''));
$pdf->text($x+$largeur1*2+$largeur2,$y+$hauteur*8+4,(isset($g4p_resultat['MMM']['date']))?($g4p_resultat['MMM']['date']):(''));
$pdf->text($x+$largeur1*6+$largeur2*3,$y+$hauteur*8,(isset($g4p_resultat['MM']['nom']))?($g4p_resultat['MM']['nom']):(''));
$pdf->text($x+$largeur1*6+$largeur2*3,$y+$hauteur*8+4,(isset($g4p_resultat['MM']['date']))?($g4p_resultat['MM']['date']):(''));
$pdf->text($x+$largeur1*10+$largeur2*5,$y+$hauteur*8,(isset($g4p_resultat['MMF']['nom']))?($g4p_resultat['MMF']['nom']):(''));
$pdf->text($x+$largeur1*10+$largeur2*5,$y+$hauteur*8+4,(isset($g4p_resultat['MMF']['date']))?($g4p_resultat['MMF']['date']):(''));
//motif2
$pdf->text($x,$y+$hauteur*10,(isset($g4p_resultat['MMMFMM']['nom']))?($g4p_resultat['MMMFMM']['nom']):(''));
$pdf->text($x,$y+$hauteur*10+4,(isset($g4p_resultat['MMMFMM']['date']))?($g4p_resultat['MMMFMM']['date']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*10,(isset($g4p_resultat['MMMFFM']['nom']))?($g4p_resultat['MMMFFM']['nom']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*10+4,(isset($g4p_resultat['MMMFFM']['date']))?($g4p_resultat['MMMFFM']['date']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*10,(isset($g4p_resultat['MMFFMM']['nom']))?($g4p_resultat['MMFFMM']['nom']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*10+4,(isset($g4p_resultat['MMFFMM']['date']))?($g4p_resultat['MMFFMM']['date']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*10,(isset($g4p_resultat['MMFFFM']['nom']))?($g4p_resultat['MMFFFM']['nom']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*10+4,(isset($g4p_resultat['MMFFFM']['date']))?($g4p_resultat['MMFFFM']['date']):(''));

$pdf->text($x,$y+$hauteur*13,(isset($g4p_resultat['MMMFM']['nom']))?($g4p_resultat['MMMFM']['nom']):(''));
$pdf->text($x,$y+$hauteur*13+4,(isset($g4p_resultat['MMMFM']['date']))?($g4p_resultat['MMMFM']['date']):(''));
$pdf->text($x+$largeur1*2+$largeur2,$y+$hauteur*13,(isset($g4p_resultat['MMMF']['nom']))?($g4p_resultat['MMMF']['nom']):(''));
$pdf->text($x+$largeur1*2+$largeur2,$y+$hauteur*13+4,(isset($g4p_resultat['MMMF']['date']))?($g4p_resultat['MMMF']['date']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*13,(isset($g4p_resultat['MMMFF']['nom']))?($g4p_resultat['MMMFF']['nom']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*13+4,(isset($g4p_resultat['MMMFF']['date']))?($g4p_resultat['MMMFF']['date']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*13,(isset($g4p_resultat['MMFFM']['nom']))?($g4p_resultat['MMFFM']['nom']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*13+4,(isset($g4p_resultat['MMFFM']['date']))?($g4p_resultat['MMFFM']['date']):(''));
$pdf->text($x+$largeur1*10+$largeur2*5,$y+$hauteur*13,(isset($g4p_resultat['MMFF']['nom']))?($g4p_resultat['MMFF']['nom']):(''));
$pdf->text($x+$largeur1*10+$largeur2*5,$y+$hauteur*13+4,(isset($g4p_resultat['MMFF']['date']))?($g4p_resultat['MMFF']['date']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*13,(isset($g4p_resultat['MMFFF']['nom']))?($g4p_resultat['MMFFF']['nom']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*13+4,(isset($g4p_resultat['MMFFF']['date']))?($g4p_resultat['MMFFF']['date']):(''));

$pdf->text($x,$y+$hauteur*16,(isset($g4p_resultat['MMMFMF']['nom']))?($g4p_resultat['MMMFMF']['nom']):(''));
$pdf->text($x,$y+$hauteur*16+4,(isset($g4p_resultat['MMMFMF']['date']))?($g4p_resultat['MMMFMF']['date']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*16,(isset($g4p_resultat['MMMFFF']['nom']))?($g4p_resultat['MMMFFF']['nom']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*16+4,(isset($g4p_resultat['MMFMFF']['date']))?($g4p_resultat['MMMFFF']['date']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*16,(isset($g4p_resultat['MMFFMF']['nom']))?($g4p_resultat['MMFFMF']['nom']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*16+4,(isset($g4p_resultat['MMFFMF']['date']))?($g4p_resultat['MMFFMF']['date']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*16,(isset($g4p_resultat['MMFFFF']['nom']))?($g4p_resultat['MMFFFF']['nom']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*16+4,(isset($g4p_resultat['MMFFFF']['date']))?($g4p_resultat['MMFFFF']['date']):(''));
//LE MILIEU
$pdf->text($x+$largeur1*6+$largeur2*3,$y+$hauteur*18,(isset($g4p_resultat['M']['nom']))?($g4p_resultat['M']['nom']):(''));
$pdf->text($x+$largeur1*6+$largeur2*3,$y+$hauteur*18+4,(isset($g4p_resultat['M']['date']))?($g4p_resultat['M']['date']):(''));

//motif1
$pdf->text($x,$y+$hauteur*20,(isset($g4p_resultat['MFMMMM']['nom']))?($g4p_resultat['MFMMMM']['nom']):(''));
$pdf->text($x,$y+$hauteur*20+4,(isset($g4p_resultat['MFMMMM']['date']))?($g4p_resultat['MFMMMM']['date']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*20,(isset($g4p_resultat['MFMMFM']['nom']))?($g4p_resultat['MFMMFM']['nom']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*20+4,(isset($g4p_resultat['MFMMFM']['date']))?($g4p_resultat['MFMMFM']['date']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*20,(isset($g4p_resultat['MFFMMM']['nom']))?($g4p_resultat['MFFMMM']['nom']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*20+4,(isset($g4p_resultat['MFFMMM']['date']))?($g4p_resultat['MFFMMM']['date']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*20,(isset($g4p_resultat['MFFMFM']['nom']))?($g4p_resultat['MFFMFM']['nom']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*20+4,(isset($g4p_resultat['MFFMFM']['date']))?($g4p_resultat['MFFMFM']['date']):(''));

$pdf->text($x,$y+$hauteur*23,(isset($g4p_resultat['MFMMM']['nom']))?($g4p_resultat['MFMMM']['nom']):(''));
$pdf->text($x,$y+$hauteur*23+4,(isset($g4p_resultat['MFMMM']['date']))?($g4p_resultat['MFMMM']['date']):(''));
$pdf->text($x+$largeur1*2+$largeur2,$y+$hauteur*23,(isset($g4p_resultat['MFMM']['nom']))?($g4p_resultat['MFMM']['nom']):(''));
$pdf->text($x+$largeur1*2+$largeur2,$y+$hauteur*23+4,(isset($g4p_resultat['MFMM']['date']))?($g4p_resultat['MFMM']['date']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*23,(isset($g4p_resultat['MFMMF']['nom']))?($g4p_resultat['MFMMF']['nom']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*23+4,(isset($g4p_resultat['MFMMF']['date']))?($g4p_resultat['MFMMF']['date']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*23,(isset($g4p_resultat['MFFMM']['nom']))?($g4p_resultat['MFFMM']['nom']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*23+4,(isset($g4p_resultat['MFFMM']['date']))?($g4p_resultat['MFFMM']['date']):(''));
$pdf->text($x+$largeur1*10+$largeur2*5,$y+$hauteur*23,(isset($g4p_resultat['MFFM']['nom']))?($g4p_resultat['MFFM']['nom']):(''));
$pdf->text($x+$largeur1*10+$largeur2*5,$y+$hauteur*23+4,(isset($g4p_resultat['MFFM']['date']))?($g4p_resultat['MFFM']['date']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*23,(isset($g4p_resultat['MFFMF']['nom']))?($g4p_resultat['MFFMF']['nom']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*23+4,(isset($g4p_resultat['MFFMF']['date']))?($g4p_resultat['MFFMF']['date']):(''));

$pdf->text($x,$y+$hauteur*26,(isset($g4p_resultat['MFMMMF']['nom']))?($g4p_resultat['MFMMMF']['nom']):(''));
$pdf->text($x,$y+$hauteur*26+4,(isset($g4p_resultat['MFMMMF']['date']))?($g4p_resultat['MFMMMF']['date']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*26,(isset($g4p_resultat['MFMMFF']['nom']))?($g4p_resultat['MFMMFF']['nom']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*26+4,(isset($g4p_resultat['MFMMFF']['date']))?($g4p_resultat['MFMMFF']['date']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*26,(isset($g4p_resultat['MFFMMF']['nom']))?($g4p_resultat['MFFMMF']['nom']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*26+4,(isset($g4p_resultat['MFFMMF']['date']))?($g4p_resultat['MFFMMF']['date']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*26,(isset($g4p_resultat['MFFMFF']['nom']))?($g4p_resultat['MFFMFF']['nom']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*26+4,(isset($g4p_resultat['MFFMFF']['date']))?($g4p_resultat['MFFMFF']['date']):(''));
//1er interm餩aire
$pdf->text($x+$largeur1*2+$largeur2,$y+$hauteur*28,(isset($g4p_resultat['MFM']['nom']))?($g4p_resultat['MFM']['nom']):(''));
$pdf->text($x+$largeur1*2+$largeur2,$y+$hauteur*28+4,(isset($g4p_resultat['MFM']['date']))?($g4p_resultat['MFM']['date']):(''));
$pdf->text($x+$largeur1*6+$largeur2*3,$y+$hauteur*28,(isset($g4p_resultat['MF']['nom']))?($g4p_resultat['MF']['nom']):(''));
$pdf->text($x+$largeur1*6+$largeur2*3,$y+$hauteur*28+4,(isset($g4p_resultat['MF']['date']))?($g4p_resultat['MF']['date']):(''));
$pdf->text($x+$largeur1*10+$largeur2*5,$y+$hauteur*28,(isset($g4p_resultat['MFF']['nom']))?($g4p_resultat['MFF']['nom']):(''));
$pdf->text($x+$largeur1*10+$largeur2*5,$y+$hauteur*28+4,(isset($g4p_resultat['MFF']['date']))?($g4p_resultat['MFF']['date']):(''));
//motif2
$pdf->text($x,$y+$hauteur*30,(isset($g4p_resultat['MFMFMM']['nom']))?($g4p_resultat['MFMFMM']['nom']):(''));
$pdf->text($x,$y+$hauteur*30+4,(isset($g4p_resultat['MFMFMM']['date']))?($g4p_resultat['MFMFMM']['date']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*30,(isset($g4p_resultat['MFMFFM']['nom']))?($g4p_resultat['MFMFFM']['nom']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*30+4,(isset($g4p_resultat['MFMFFM']['date']))?($g4p_resultat['MFMFFM']['date']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*30,(isset($g4p_resultat['MFFFMM']['nom']))?($g4p_resultat['MFFFMM']['nom']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*30+4,(isset($g4p_resultat['MFFFMM']['date']))?($g4p_resultat['MFFFMM']['date']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*30,(isset($g4p_resultat['MFFFFM']['nom']))?($g4p_resultat['MFFFFM']['nom']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*30+4,(isset($g4p_resultat['MFFFFM']['date']))?($g4p_resultat['MFFFFM']['date']):(''));

$pdf->text($x,$y+$hauteur*33,(isset($g4p_resultat['MFMFM']['nom']))?($g4p_resultat['MFMFM']['nom']):(''));
$pdf->text($x,$y+$hauteur*33+4,(isset($g4p_resultat['MFMFM']['date']))?($g4p_resultat['MFMFM']['date']):(''));
$pdf->text($x+$largeur1*2+$largeur2,$y+$hauteur*33,(isset($g4p_resultat['MFFM']['nom']))?($g4p_resultat['MFFM']['nom']):(''));
$pdf->text($x+$largeur1*2+$largeur2,$y+$hauteur*33+4,(isset($g4p_resultat['MFFM']['date']))?($g4p_resultat['MFFM']['date']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*33,(isset($g4p_resultat['MFMFF']['nom']))?($g4p_resultat['MFMFF']['nom']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*33+4,(isset($g4p_resultat['MFMFF']['date']))?($g4p_resultat['MFMFF']['date']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*33,(isset($g4p_resultat['MFFFM']['nom']))?($g4p_resultat['MFFFM']['nom']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*33+4,(isset($g4p_resultat['MFFFM']['date']))?($g4p_resultat['MFFFM']['date']):(''));
$pdf->text($x+$largeur1*10+$largeur2*5,$y+$hauteur*33,(isset($g4p_resultat['MFFF']['nom']))?($g4p_resultat['MFFF']['nom']):(''));
$pdf->text($x+$largeur1*10+$largeur2*5,$y+$hauteur*33+4,(isset($g4p_resultat['MFFF']['date']))?($g4p_resultat['MFFF']['date']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*33,(isset($g4p_resultat['MFFFF']['nom']))?($g4p_resultat['MFFFF']['nom']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*33+4,(isset($g4p_resultat['MFFFF']['date']))?($g4p_resultat['MFFFF']['date']):(''));

$pdf->text($x,$y+$hauteur*36,(isset($g4p_resultat['MFMFMF']['nom']))?($g4p_resultat['MFMFMF']['nom']):(''));
$pdf->text($x,$y+$hauteur*36+4,(isset($g4p_resultat['MFMFMF']['date']))?($g4p_resultat['MFMFMF']['date']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*36,(isset($g4p_resultat['MFMFFF']['nom']))?($g4p_resultat['MFMFFF']['nom']):(''));
$pdf->text($x+$largeur1*4+$largeur2*2,$y+$hauteur*36+4,(isset($g4p_resultat['MFMFFF']['date']))?($g4p_resultat['MFMFFF']['date']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*36,(isset($g4p_resultat['MFFFMF']['nom']))?($g4p_resultat['MFFFMF']['nom']):(''));
$pdf->text($x+$largeur1*8+$largeur2*4,$y+$hauteur*36+4,(isset($g4p_resultat['MFFFMF']['date']))?($g4p_resultat['MFFFMF']['date']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*36,(isset($g4p_resultat['MFFFFF']['nom']))?($g4p_resultat['MFFFFF']['nom']):(''));
$pdf->text($x+$largeur1*12+$largeur2*6,$y+$hauteur*36+4,(isset($g4p_resultat['MFFFFF']['date']))?($g4p_resultat['MFFFFF']['date']):(''));

$pdf->Output('arbre '.$g4p_indi->nom.' '.$g4p_indi->prenom.".pdf","I");

}
?>
