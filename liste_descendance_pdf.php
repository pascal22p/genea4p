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
 *                         Liste de descendance                            *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/*limites imposées pour éviter d'exploser le serveur:
_NBRE_MAX_PERS_DESCENDANCE_ générations au maximum sont affichées ($generation)
_NBRE_MAX_PERS_DESCENDANCE_ personnes sont affichées au maximum
*/

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'tcpdf/config/lang/eng.php');
require_once($g4p_chemin.'tcpdf/tcpdf.php');

class PDF_Tree extends TCPDF {
	function MakeTree($data,$x=0,$nodeFormat='+%k',$childFormat='-%k: %v',$w=20,$h=5,$border=1,$fill=0,$align='',$indent=1,$vspacing=1,$drawlines=true,$level=0,$hcell=array(),$treeHeight=0.00){
		if(is_array($data)){
			$countData = count($data); $c=0; $hcell[$level]=array();
			foreach($data as $key=>$value){
				$this->SetXY($x+$this->lMargin+($indent*$level),$this->GetY()+$vspacing);
				if(is_array($value)){
					$pStr = str_replace('%k',$key,$nodeFormat);
				}else{
					$pStr = str_replace('%k',$key,$childFormat);
					$pStr = str_replace('%v',$value,$pStr);
				}
				$pStr = str_replace("\r",'',$pStr);
				$pStr = str_replace("\t",'',$pStr);
				while(ord(substr($pStr,-1,1))==10)
					$pStr = substr($pStr,0,(strlen($pStr)-1));
				$line = explode("\n",$pStr);
				$rows = 0; $addLines = 0;
				foreach ($line as $l){
					$widthLine = $this->GetStringWidth($l);
					$rows = $widthLine/$w;
					if($rows>1)
						$addLines+=($widthLine%$w==0) ? $rows-1 : $rows;
				}
				$hcell[$level][$c]=intval(count($line)+$addLines)*$h;
				$this->MultiCell($w,$h,$pStr,$border,$align,$fill);
				$x1 = $x+$this->lMargin+($indent*$level);
				$y1 = $this->GetY()-($hcell[$level][$c]/2);
				if($drawlines)
					$this->Line($x1,$y1,$x1-$indent,$y1);
				if($c==$countData-1){
					$x1 = $x+$this->lMargin+($indent*$level)-$indent;
					$halfHeight = 0;
					if(isset($hcell[$level-1])){
						$lastKeys = array_keys($hcell[$level-1]);
						$lastKey = $lastKeys[count($lastKeys)-1];
						$halfHeight = $hcell[$level-1][$lastKey]/2;
					}
					$y2 = $y1-$treeHeight-($hcell[$level][$c]/2)-$halfHeight-$vspacing;
					if($drawlines)
						$this->Line($x1,$this->GetY()-($hcell[$level][$c]/2),$x1,$y2);
				}
				if(is_array($value))
					$treeHeight += $this->MakeTree($value,$x,$nodeFormat,$childFormat,$w,$h,$border,$fill,$align,$indent,$vspacing,$drawlines,$level+1,$hcell);
				$treeHeight += $hcell[$level][$c]+$vspacing;
				$c++;
			}
			return $treeHeight;
		}
	}
}

function recursive_descendance($g4p_id, $generation=1, $couleur=0)
{
  global $g4p_couleur, $g4p_chemin, $g4p_limite_pers, $cache_count, $g4p_langue;
  $g4p_arbre_descendant=array();

  if($_SESSION['permission']->permission[_PERM_MASK_INDI_] and $g4p_indi->resn=='privacy')
    return;

  if($g4p_limite_pers>_NBRE_MAX_PERS_DESCENDANCE_)
  {
    echo '<br /><b>',sprintf($g4p_langue['liste_desc_max_pers'],_NBRE_MAX_PERS_DESCENDANCE_),'</b>';
    return;
  }
  if($cache_count>_NBRE_MAX_CACHE_CREES_)
  {
    echo '<br /><b>',$g4p_langue['liste_desc_max_cache'],'</b>';
    return;
  }

  $g4p_limite_pers++;

  if($couleur>=count($g4p_couleur))
    $couleur=0;
  $g4p_indi_info=g4p_load_indi_infos($g4p_id);

  if(isset($g4p_indi_info->familles))
  {
    foreach($g4p_indi_info->familles as $g4p_une_famille)
    {
      if(isset($g4p_une_famille->conjoint->nom) or isset($g4p_une_famille->conjoint->prenom))
        $g4p_arbre_descendant[$g4p_indi_info->nom.' '.$g4p_indi_info->prenom."\n& ".$g4p_une_famille->conjoint->nom.' '.$g4p_une_famille->conjoint->prenom]=array();


      if(isset($g4p_une_famille->enfants))
      {
        foreach($g4p_une_famille->enfants as $g4p_un_enfant)
        {
          if($generation>$_GET['g4p_generation'] or $generation>_NBRE_MAX_GENERATION_)
          {
            //echo '<b>',sprintf($g4p_langue['liste_desc_max_gen'],$generation-1),'</b>';
            break;
            return;
          }
          $arbre=recursive_descendance($g4p_un_enfant->indi_id, $generation+1, $couleur+1);
          $g4p_arbre_descendant[$g4p_indi_info->nom.' '.$g4p_indi_info->prenom."\n& ".$g4p_une_famille->conjoint->nom.' '.$g4p_une_famille->conjoint->prenom]=$arbre;
        }
      }
    }
  }
  return $g4p_arbre_descendant;
}


if (!isset($g4p_indi) or !isset($_GET['g4p_id']))
{
  include($g4p_chemin.'entete.php');
  echo $g4p_langue['id_inconnu'];
}
else
{
  if($g4p_indi->indi_id!=$_GET['g4p_id'])
    $g4p_indi=g4p_load_indi_infos($_GET['g4p_id']);

  $g4p_titre_page='Liste de descendance : '.$g4p_indi->nom.' '.$g4p_indi->prenom;
  //include($g4p_chemin.'entete.php');

  if(!isset($_GET['g4p_generation']))
    $_GET['g4p_generation']=5;

  /*echo '<h2>',sprintf($g4p_langue['liste_desc_titre'],$g4p_indi->nom,$g4p_indi->prenom),'</h2>
  <span class="noprint">',$g4p_langue['liste_desc_nb_gen'],'<br />';
  $lien='';
  for($i=1;$i<=_NBRE_MAX_GENERATION_;$i+=2)
    $lien.='<a href="'.g4p_make_url('','liste_descendance.php','genea_db_id='.$_SESSION['genea_db_id'].'&g4p_id='.$g4p_indi->indi_id.'&g4p_generation='.$i,'liste_descendance-'.$_SESSION['genea_db_id'].'-'.g4p_prepare_varurl($g4p_indi->nom).'-'.g4p_prepare_varurl($g4p_indi->prenom).'-'.$g4p_indi->indi_id).'-'.$i.'">'.$i.'</a> - ';
  echo substr($lien,0,-2);
  echo '<div class="cadre">';
//<ul class="descendance">';*/
  $g4p_limite_pers=0;
  $arbre=recursive_descendance($g4p_indi->indi_id);
  //echo '</ul><b>',sprintf($g4p_langue['liste_desc_nb_desc_total'],$g4p_limite_pers),'</b></div>';

$pdf=new PDF_Tree();
$pdf->addfont('vera','','vera.php');
$pdf->addfont('vera','b','verab.php');
$pdf->addfont('vera','i','verai.php');
$pdf->addfont('vera','bi','verabi.php');
$pdf->SetFont('vera','',10);

$pdf->SetCreator('http://www.parois.net');
$pdf->SetAuthor('PAROIS Pascal');
$pdf->SetTitle('Arbre ascendant de : '.$g4p_indi->nom.' '.$g4p_indi->prenom);

$pdf->SetHeaderData('', '', '', '');
$pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, 5);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);

$pdf->setLanguageArray($l); //set language items

$pdf->open();
$pdf->AddPage();
$pdf->SetFont('vera','',7);
$pdf->SetFillColor(150,150,150);
$pdf->SetDrawColor(20,20,20);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(0,6,'My Tree Example',0,'','R');
$pdf->Ln(6);


// TREE 1
$pdf->SetY(6);
$pdf->MakeTree($arbre);
$pdf->Output();
exit;
}

include($g4p_chemin.'pied_de_page.php');
?>
