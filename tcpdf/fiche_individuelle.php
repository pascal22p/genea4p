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
 *            Fiche individuelle format PDF                                *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

if($g4p_langue['entete_charset']=='UTF-8')
  $g4p_db_nom=utf8_decode($_SESSION['genea_db_nom']);
else
  $g4p_db_nom=$_SESSION['genea_db_nom'];

  require_once($g4p_chemin.'tcpdf/config/lang/eng.php');
  require_once($g4p_chemin.'tcpdf/tcpdf.php');
  
  $pdf=new TCPDF('P', 'mm', 'A4', true);
  
  $pdf->addfont('vera','','vera.php');
  $pdf->addfont('vera','B','verab.php');
  $pdf->addfont('vera','I','verai.php');
  $pdf->addfont('vera','BI','verabi.php');
  $pdf->SetFont('vera','',10);
  
  $pdf->SetCreator('http://www.parois.net');
  $pdf->SetAuthor('PAROIS Pascal');
  $pdf->SetTitle('Fiche descriptive de : '.$g4p_indi->nom.' '.$g4p_indi->prenom);
  
  $pdf->SetHeaderData('','','','');
  $pdf->SetHeaderMargin(5);
  $pdf->SetFooterMargin(15);
  
  $pdf->AliasNbPages();
  
  $pdf->SetAutoPageBreak(TRUE);
  $pdf->setLanguageArray($l); //set language items
  
  $pdf->open();
  $pdf->AddPage();
  

  if(isset($g4p_indi))
  {
    $pdf->SetFont('vera','',8);
    if($g4p_indi->chan!='0000-00-00 00:00:00')
      $g4p_chan=g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_indi->chan));

    $pdf->ln();
    $pdf->SetFont('vera','b',16);
    $pdf->cell(0,5,$g4p_indi->nom.' '.$g4p_indi->prenom,0,1);
    $pdf->ln(5);

    $pdf->SetFont('vera','',10);
    $pdf->writehtml($g4p_langue['index_sexe'].$g4p_langue['index_sexe_valeur'][$g4p_indi->sexe]);
    if($g4p_indi->npfx)
      $pdf->writehtml($g4p_langue['index_npfx'].$g4p_indi->npfx);
    if($g4p_indi->givn)
      $pdf->writehtml($g4p_langue['index_givn'].$g4p_indi->givn);
    if($g4p_indi->nick)
      $pdf->writehtml($g4p_langue['index_nick'].$g4p_indi->nick);
    if($g4p_indi->spfx)
      $pdf->writehtml($g4p_langue['index_spfx'].$g4p_indi->spfx);
    if($g4p_indi->nsfx)
      $pdf->writehtml($g4p_langue['index_nsfx'].$g4p_indi->nsfx);
    
  //evenements individuels  
    if(isset($g4p_indi->evenements))
    {
      $pdf->ln();
      $pdf->setleftmargin(10);
      foreach($g4p_indi->evenements as $g4p_a_ievents)
      {
        $pdf->SetFont('vera','UI',10);
        $pdf->write(5,$g4p_tag_def[$g4p_a_ievents->type].' :');
        $g4p_tmp=' '.g4p_date($g4p_a_ievents->date);
        $g4p_tmp.=($g4p_a_ievents->description)?(' ('.$g4p_a_ievents->description.')'):('');
        $g4p_tmp.=(!empty($g4p_a_ievents->lieu))?(' ('.$g4p_a_ievents->lieu->toCompactString().') '):('');
        $pdf->SetFont('vera','',10);
        $pdf->write(5,$g4p_tmp."\n");
  
        if(isset($g4p_a_ievents->sources) and $_SESSION['permission']->permission[_PERM_SOURCE_])
        {
          foreach($g4p_a_ievents->sources as $g4p_a_source)
          {
            $pdf->SetFont('vera','IBU',8);
            $pdf->setleftmargin(30);
            $g4p_x=$pdf->getx();
            $g4p_y=$pdf->gety();
            $pdf->write(4,'Source : '.$g4p_a_source->titre."\n");
            $pdf->SetFont('vera','I',8);
            $g4p_a_source_text=($g4p_a_source->text)?(trim($g4p_a_source->text)."\n"):('');
            $pdf->write(4,$g4p_a_source_text);
            $pdf->rect($g4p_x,$g4p_y,120,$pdf->gety()-$g4p_y,'D');
            $pdf->setleftmargin(10);
            $pdf->ln();
          }
        }
        if(isset($g4p_a_ievents->notes) and $_SESSION['permission']->permission[_PERM_NOTE_])
        {
          foreach($g4p_a_ievents->notes as $g4p_a_note)
          {
            $pdf->SetFont('vera','IBU',8);
            $pdf->setleftmargin(30);
            $g4p_x=$pdf->getx();
            $g4p_y=$pdf->gety();
            $pdf->write(4,"Note : \n");
            $pdf->SetFont('vera','I',8);
            $pdf->write(4,trim($g4p_a_note->text)."\n");
            $pdf->rect($g4p_x,$g4p_y,120,$pdf->gety()-$g4p_y,'D');
            $pdf->setleftmargin(10);
            $pdf->ln();
          }
        }
        if(isset($g4p_a_ievents->multimedia) and $_SESSION['permission']->permission[_PERM_MULTIMEDIA_])
        {
          foreach($g4p_a_ievents->multimedia as $g4p_a_multimedia)
          {
            $pdf->SetFont('vera','IBU',8);
            $pdf->setleftmargin(30);
            $g4p_x=$pdf->getx();
            $g4p_y=$pdf->gety();
            $pdf->write(4,"Documents : \n");
            $pdf->SetFont('vera','I',8);
            $pdf->write(4,"Titre : ".trim($g4p_a_multimedia->title)."\n");
            $pdf->write(4,"Format : ".trim($g4p_a_multimedia->format)."\n");
            if($g4p_a_multimedia->format=='URL')
              $pdf->writehtml("Fichier : <a href=\"".$g4p_a_multimedia->file."\">".trim($g4p_a_multimedia->file)."</a><br />");
            else
              $pdf->write(4,"Fichier : ".trim($g4p_a_multimedia->file)."\n");        
            $pdf->rect($g4p_x,$g4p_y,120,$pdf->gety()-$g4p_y,'D');
            $pdf->setleftmargin(10);
            $pdf->ln();
          }
        }
      }
    }
    $pdf->ln();
  // Le mariage
    if(isset($g4p_indi->familles))
    {
      foreach($g4p_indi->familles as $g4p_a_famille)//affiche tous les mariages
      {
        $pdf->SetFont('vera','UI',10);
        $g4p_x=$pdf->getx();
        $g4p_y=$pdf->gety();
        if(isset($g4p_a_famille->conjoint->nom) or isset($g4p_a_famille->conjoint->prenom))
        {
          $pdf->writehtml('Conjoint : '.$g4p_a_famille->conjoint->nom.' '. $g4p_a_famille->conjoint->prenom.' '.$g4p_a_famille->conjoint->date_rapide);
        }
        else
        {
          $pdf->write(5,"Conjoint Inconnu(e)\n");
        }
  
        //evenements familiaux
        if(isset($g4p_a_famille->evenements))
        {
          foreach($g4p_a_famille->evenements as $a_g4p_fevent)
          {
            $pdf->SetFont('vera','',10);
            $g4p_text=$g4p_tag_def[$a_g4p_fevent->type].' : '.g4p_date($a_g4p_fevent->date).
              (($a_g4p_fevent->description!='')?(' (description : '.$a_g4p_fevent->description.') '):('')).
              ((!empty($a_g4p_fevent->lieu))?(' (lieu : '.$a_g4p_fevent->lieu->toCompactString().') '):(''));
            $pdf->write(5,' '.$g4p_text."\n");
  
            if(isset($g4p_a_fevent->sources) AND $_SESSION['permission']->permission[_PERM_SOURCE_])
            {
              foreach($g4p_a_fevent->sources as $g4p_a_source)
              {
                $pdf->SetFont('vera','IBU',8);
                $pdf->setleftmargin(30);
                $g4p_x2=$pdf->getx();
                $g4p_y2=$pdf->gety();
                $pdf->write(4,'Source : '.$g4p_a_source->titre."\n");
                $pdf->SetFont('vera','I',8);
                $g4p_a_source_text=($g4p_a_source->text)?($g4p_a_source->text."\n"):('');
                $pdf->write(4,$g4p_a_source_text);
                $pdf->rect($g4p_x2,$g4p_y2,120,$pdf->gety()-$g4p_y2,'D');
                $pdf->setleftmargin(10);
                $pdf->ln();
              }
            }
            if(isset($g4p_a_fevent->notes) AND $_SESSION['permission']->permission[_PERM_NOTE_])
            {
              foreach($g4p_a_fevent->notes as $g4p_a_source)
              {
                $pdf->SetFont('vera','IBU',8);
                $pdf->setleftmargin(30);
                $g4p_x2=$pdf->getx();
                $g4p_y2=$pdf->gety();
                $pdf->write(4,"Note : \n");
                $pdf->SetFont('vera','I',8);
                $pdf->write(4,trim($g4p_a_source->text)."\n");
                $pdf->rect($g4p_x2,$g4p_y2,120,$pdf->gety()-$g4p_y2,'D');
                $pdf->setleftmargin(10);
                $pdf->ln();
              }
            }
            if(isset($g4p_a_fevent->medias) and $_SESSION['permission']->permission[_PERM_MULTIMEDIA_])
            {
              foreach($g4p_a_fevent->medias as $g4p_a_multimedia)
              {
                $pdf->SetFont('vera','IBU',8);
                $pdf->setleftmargin(30);
                $g4p_x=$pdf->getx();
                $g4p_y=$pdf->gety();
                $pdf->write(4,"Documents : \n");
                $pdf->SetFont('vera','I',8);
                $pdf->write(4,"Titre : ".trim($g4p_a_multimedia->title)."\n");
                $pdf->write(4,"Format : ".trim($g4p_a_multimedia->format)."\n");
                if($g4p_a_multimedia->format=='URL')
                  $pdf->writehtml("Fichier : <a href=\"".$g4p_a_multimedia->file."\">".trim($g4p_a_multimedia->file)."</a><br />");
                else
                  $pdf->write(4,"Fichier : ".trim($g4p_a_multimedia->file)."\n");        
                $pdf->rect($g4p_x,$g4p_y,120,$pdf->gety()-$g4p_y,'D');
                $pdf->setleftmargin(10);
                $pdf->ln();
              }
            }
          }
        }
  
  //les enfants des mariages
        $g4p_texte='';
        if (isset($g4p_a_famille->enfants))
        {
          foreach($g4p_a_famille->enfants as $g4p_a_enfants)
            $g4p_texte.='    '.$g4p_a_enfants->nom.' '.$g4p_a_enfants->prenom.' '.$g4p_a_enfants->date_rapide."<br />";
  
          $pdf->SetFont('vera','UI',10);
          $pdf->write(5,"\n    Enfants issus du mariage :\n");
          $pdf->SetFont('vera','',10);
          $pdf->writehtml($g4p_texte);
          $g4p_texte='';
        }
  
  //Les notes
        if(isset($g4p_a_famille->note) AND $_SESSION['permission']->permission[_PERM_NOTE_])
        {
            $pdf->ln();
            $pdf->SetFont('vera','UI',10);
            $pdf->write(5,"    Note(s) :\n");
            $pdf->SetFont('vera','',10);
            foreach($g4p_a_famille->note as $g4p_a_note)
            {
              $g4p_texte.=$g4p_a_note['notes_text']."\n";
            }
            $pdf->setx(30);
            $pdf->multicell(130,6,$g4p_texte,1,1);
        }
  
  // Les sources
        if(isset($g4p_a_famille->source) AND $_SESSION['permission']->permission[_PERM_SOURCE_])
        {
          foreach($g4p_a_famille->source as $g4p_a_source)
          {
            $pdf->ln();
            $pdf->SetFont('vera','UI',10);
            $pdf->write(5,"    Source(s) :\n");
            $pdf->SetFont('vera','',10);
            $g4p_texte=$g4p_a_source->titre."\nAbbréviation :  ".$g4p_a_source->abbr."\n
            Texte : \n".$g4p_a_source->text."\n";
            $pdf->setx(30);
            $pdf->multicell(130,6,$g4p_texte,1,1);
          }
        }
  // les medias
        if(isset($g4p_a_famille->medias) and $_SESSION['permission']->permission[_PERM_MULTIMEDIA_])
        {
          foreach($g4p_a_famille->medias as $g4p_a_multimedia)
          {
            $pdf->ln();
            $pdf->SetFont('vera','IUB',8);
            $pdf->setleftmargin(30);
            $g4p_x2=$pdf->getx();
            $g4p_y2=$pdf->gety();
            $pdf->write(5,"Documents : \n");
            $pdf->SetFont('vera','',8);
            $pdf->write(5,"Titre : ".trim($g4p_a_multimedia->title)."\n");
            $pdf->write(5,"Format : ".trim($g4p_a_multimedia->format)."\n");
            if($g4p_a_multimedia->format=='URL')
              $pdf->writehtml("Fichier : <a href=\"".$g4p_a_multimedia->file."\">".trim($g4p_a_multimedia->file)."</a><br />");
            else
              $pdf->write(4,"Fichier : ".trim($g4p_a_multimedia->file)."\n");        
            $pdf->rect($g4p_x2,$g4p_y2,130,$pdf->gety()-$g4p_y2,'D');
            $pdf->setleftmargin(10);
            $pdf->ln();
          }
        }
  
        $pdf->rect($g4p_x,$g4p_y,180,$pdf->gety()-$g4p_y,'D');
        $pdf->ln();
      }
    }
    else
    {
      $pdf->write(5,"Aucun mariage\n");
      $pdf->ln();
    }
  
  // les parents
    if (isset($g4p_indi->parents))
    {
      foreach($g4p_indi->parents as $g4p_a_parent)
      {
        $pdf->SetFont('vera','UI',10);
        $pdf->write(5,'Type de parents : ');
        $pdf->SetFont('vera','',10);
        $g4p_parent=str_replace(array_keys($g4p_lien_def), array_values($g4p_lien_def),$g4p_a_parent['rela_type'])."<br />";
        if(isset($g4p_a_parent['pere']))
            $g4p_parent.=$g4p_a_parent['pere']->nom.' '.$g4p_a_parent['pere']->prenom.' '.$g4p_a_parent['pere']->date_rapide."<br />";
        else
          $g4p_parent.="Père inconnu<br />";
  
        if(isset($g4p_a_parent['mere']))
            $g4p_parent.=$g4p_a_parent['mere']->nom.' '.$g4p_a_parent['mere']->prenom.' '.$g4p_a_parent['mere']->date_rapide."<br />";
        else
          $g4p_parent.="mère inconnue<br />";
      }
      $pdf->Writehtml($g4p_parent."<br />");
    }
  
  //Les notes
    $g4p_texte='';
    if(isset($g4p_indi->notes) AND $_SESSION['permission']->permission[_PERM_NOTE_])
    {
      $pdf->ln();
      $pdf->SetFont('vera','UI',10);
      $pdf->write(5,"    Note(s) :\n");
      $pdf->SetFont('vera','',10);
      foreach($g4p_indi->notes as $g4p_a_note)
      {
        $g4p_texte.=$g4p_a_note->text."\n";
      }
      $pdf->setx(20);
      $pdf->multicell(150,6,$g4p_texte,1,1);
    }
  
  // Les sources
    if(isset($g4p_indi->sources) AND $_SESSION['permission']->permission[_PERM_SOURCE_])
    {
      foreach($g4p_indi->sources as $g4p_a_source)
      {
        $pdf->ln();
        $pdf->SetFont('vera','UI',10);
        $pdf->write(5,"    Source(s) :\n");
        $pdf->SetFont('vera','',10);
        $g4p_texte=$g4p_a_source->titre."\n
        Texte : \n".$g4p_a_source->text."\n";
        $pdf->setx(20);
        $pdf->multicell(150,6,$g4p_texte,1,1);
      }
    }
  
    if(isset($g4p_indi->multimedia) and $_SESSION['permission']->permission[_PERM_MULTIMEDIA_])
    {
      foreach($g4p_indi->multimedia as $g4p_a_multimedia)
      {
        $pdf->SetFont('vera','IBU',8);
        $pdf->setleftmargin(30);
        $g4p_x=$pdf->getx();
        $g4p_y=$pdf->gety();
        $pdf->write(4,"Documents : \n");
        $pdf->SetFont('vera','I',8);
        $pdf->write(4,"Titre : ".trim($g4p_a_multimedia->title)."\n");
        $pdf->write(4,"Format : ".trim($g4p_a_multimedia->format)."\n");
        if($g4p_a_multimedia->format=='URL')
          $pdf->writehtml("Fichier : <a href=\"".$g4p_a_multimedia->file."\">".trim($g4p_a_multimedia->file)."</a><br />");
        else
          $pdf->write(4,"Fichier : ".trim($g4p_a_multimedia->file)."\n");        
        $pdf->rect($g4p_x,$g4p_y,120,$pdf->gety()-$g4p_y,'D');
        $pdf->setleftmargin(10);
        $pdf->ln();
      }
    }
  }

//$size=filesize($g4p_chemin.'cache/'.$g4p_db_nom.'/pdf/fiche_indi_'.$_SESSION['permission']->permission['chaine'].'_'.$g4p_indi->indi_id.'.pdf');
//header("Content-Type: application/force-download");
//header("Content-Length: $size");
//if(preg_match("/MSIE 5.5/", $_SERVER['HTTP_USER_AGENT'])) 
//header("Content-Disposition: filename=\"".$g4p_indi->nom.'_'.$g4p_indi->prenom.".pdf\"");
//else 
//header("Content-Disposition: attachment; filename=\"".$g4p_indi->nom.'_'.$g4p_indi->prenom.".pdf\"");
//readfile($g4p_chemin.'cache/'.$g4p_db_nom.'/pdf/fiche_indi_'.$_SESSION['permission']->permission['chaine'].'_'.$g4p_indi->indi_id.'.pdf');

$pdf->Output($g4p_indi->nom.'_'.$g4p_indi->prenom.'.pdf',true);


?>
