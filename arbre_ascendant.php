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
 *            Tracage de l'arbre ascendant                                 *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
$g4p_mon_indi=g4p_load_indi_infos((int)$_GET['g4p_id']);
$g4p_titre_page='Arbre ascendant : '.$g4p_mon_indi->nom.' '.$g4p_mon_indi->prenom;
require_once($g4p_chemin.'entete.php');


function g4p_ascendance($g4p_generation, $g4p_id, $g4p_index)
{
  global $g4p_resultat, $cw, $g4p_tag_def, $g4p_chemin, $g4p_langue;
  $g4p_mon_indi=g4p_load_indi_infos($g4p_id);

  $g4p_mon_indi->prenom=explode(' ',$g4p_mon_indi->prenom);
  $g4p_mon_indi->prenom=$g4p_mon_indi->prenom[0];
 
  //conserve uniquement le premier prénom
  $g4p_nom_aff=$g4p_nom=$g4p_mon_indi->nom.' '.$g4p_mon_indi->prenom;


  if ($g4p_generation==0)
    $g4p_mon_indi->sexe='M';

//evenements individuels

// les parents
  if(isset($g4p_mon_indi->parents))
  {
    foreach($g4p_mon_indi->parents as $g4p_parents)
    {
      if($g4p_parents['rela_type']=='BIRTH')
      {
        $g4p_generation++;
        if(!empty($g4p_parents['pere']) and $g4p_generation<6)
          g4p_ascendance($g4p_generation, $g4p_parents['pere']->indi_id, $g4p_index.$g4p_mon_indi->sexe);
        if(!empty($g4p_parents['mere']) and $g4p_generation<6)
          g4p_ascendance($g4p_generation, $g4p_parents['mere']->indi_id, $g4p_index.$g4p_mon_indi->sexe);
      }
    }
  }
  $g4p_naissance=$g4p_deces='';
  if(isset($g4p_mon_indi->evenements))
  {
    foreach($g4p_mon_indi->evenements as $g4p_a_event)
    {
      if(($g4p_a_event->type=='BIRT' or $g4p_a_event->type=='BAPM') and $g4p_a_event->date!='')
        $g4p_naissance='°'.g4p_date($g4p_a_event->date,'date4');
      if(($g4p_a_event->type=='DEAT' or $g4p_a_event->type=='BURI') and $g4p_a_event->date!='')
        $g4p_deces='+'.g4p_date($g4p_a_event->date,'date4');
    }
  }
  
  $g4p_resultat[$g4p_index.$g4p_mon_indi->sexe]='<a href="'.g4p_make_url('','index.php','g4p_base='.$g4p_mon_indi->indi_base.'&g4p_action=fiche_indi&id_pers='.$g4p_mon_indi->indi_id,'fiche-'.$g4p_mon_indi->indi_base.'-'.g4p_prepare_varurl($g4p_mon_indi->nom).'-'.g4p_prepare_varurl($g4p_mon_indi->prenom).'-'.$g4p_mon_indi->indi_id).'"><img style="border:0; margin:0; padding:0" src="'.$g4p_chemin.'images/fiche.png" title="'.$g4p_langue['menu_fiche'].'" alt="'.$g4p_langue['menu_fiche'].'" /></a>
    <a onmouseover="AffBulle('.htmlspecialchars('\''.$g4p_mon_indi->nom.' '.$g4p_mon_indi->prenom.'<br />');

  $event='';
  if(isset($g4p_mon_indi->evenements))
  {
    foreach($g4p_mon_indi->evenements as $g4p_a_event)
    {
      $event.='<em>'.$g4p_tag_def[$g4p_a_event->type].'</em>:';
      $event.=g4p_date($g4p_a_event->date);
      if(!empty($g4p_a_event->description))
        $event.=' ('.addslashes(trim($g4p_a_event->description)).')';
      if(!empty($g4p_a_event->lieu->place_id))
      	$event.=' à '.AddSlashes($g4p_a_event->lieu->toCompactString());
     $event.='<br />';
    }
  }
  $g4p_resultat[$g4p_index.$g4p_mon_indi->sexe].=htmlspecialchars(addslashes($event)).'\')"  onmouseout="HideBulle()"
    href="'.g4p_make_url('','arbre_ascendant.php','g4p_base='.$g4p_mon_indi->indi_base.'&g4p_id='.$g4p_mon_indi->indi_id,'arbre_ascendant-'.$g4p_mon_indi->indi_base.'-'.g4p_prepare_varurl($g4p_mon_indi->nom).'-'.g4p_prepare_varurl($g4p_mon_indi->prenom).'-'.$g4p_mon_indi->indi_id).'">'.$g4p_nom_aff.'</a><br />'.$g4p_naissance.' '.$g4p_deces;
}

if(isset($_GET['g4p_id']))
  if($g4p_indi->indi_id!=$_GET['g4p_id'])
    $g4p_indi=g4p_load_indi_infos($_GET['g4p_id']);

if($_SESSION['permission']->permission[_PERM_MASK_INDI_] and $g4p_indi->resn=='privacy')
{
  echo $g4p_langue['acces_non_autorise'];
  require_once($g4p_chemin.'copyright.php');
  require_once($g4p_chemin.'pied_de_page.php');
  exit;
}

if(isset($_GET['g4p_id']))
{
  $g4p_resultat=array();
  g4p_ascendance(0, $_GET['g4p_id'], '');
  //print_r($g4p_resultat);
  if(is_array($g4p_resultat))
    include('include_html/ascendance_compact_6gen.php');
  else
    echo $g4p_resultat;
}

require_once($g4p_chemin.'pied_de_page.php');
?>
