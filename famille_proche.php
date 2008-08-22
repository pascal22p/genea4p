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
 *               Détail d'un évènement individuel                          *
 *                                                                         *
 * dernière mise à jour : 06/11/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

//une petite fonction
function affiche_case($un_indi)
{
  global $g4p_langue, $g4p_tag_def, $g4p_parent;
  $g4p_parent_event=$g4p_parent='';
  if(substr_count($un_indi->prenom,' ')>0)
  {
    $g4p_prenom_court=explode(' ',$un_indi->prenom);
    $g4p_prenom_court=$g4p_prenom_court[0];
  }
  else
    $g4p_prenom_court=$un_indi->prenom;
    
  $g4p_parent='<a href="'.g4p_make_url('','famille_proche.php','g4p_id='.$un_indi->indi_id,0).'">'.$un_indi->nom.' '.$g4p_prenom_court.'</a>';
  $g4p_parent_event.=$un_indi->nom.' '.$un_indi->prenom.'<br />';
  
  if(isset($un_indi->evenements))
  {
    foreach($un_indi->evenements as $g4p_a_event)
    {
      $g4p_parent_event.='<em>'.$g4p_tag_def[$g4p_a_event->type].'</em> :'.g4p_date($g4p_a_event->date).' ';
      if(trim($g4p_a_event->description)!='')
        $g4p_parent_event.='('.$g4p_langue['fproche_description'].trim($g4p_a_event->description).') ';
      if(!empty($g4p_a_event->lieu))
        $g4p_parent_event.='('.$g4p_langue['fproche_lieu'].$g4p_a_event->lieu->toCompactString().') ';
      $g4p_parent_event.='<br />';
    }
  }
  $g4p_parent_event=preg_replace("/[\'|\"]/",'&quote;',$g4p_parent_event);
  return $g4p_parent_event='onmouseover="AffBulle(\''.htmlspecialchars( addslashes($g4p_parent_event), ENT_QUOTES ).'\')" onmouseout="HideBulle()"';
}


if(isset($_GET['g4p_id']))
  $g4p_indi=g4p_load_indi_infos($_GET['g4p_id']);

if (!isset($g4p_indi))
  die($g4p_langue['id_inconnu']);

$g4p_titre_page='Famille proche de : '.$g4p_indi->nom.' '.$g4p_indi->prenom;
require_once($g4p_chemin.'entete.php');

//hack pour NPDS
if($g4p_config['g4p_type_install']=='module-npds' or $g4p_config['g4p_type_install']=='module-npds-mod_rewrite')
  echo '<br /><table style="width:800px;position:relative;"><tr><td style="width:800px">';
else
  echo '<div class="famille_proche">';

  // j'ai fumé qd j'ai choisis mes variables, grand_parents, c'est pour les parents, ar_grand_parents c'est pour les grand parents
  if(isset($g4p_indi->parents))
  {
    foreach($g4p_indi->parents as $g4p_a_parent)
    {
      if($g4p_a_parent['rela_type']=='BIRTH' and !empty($g4p_a_parent['pere']))
      {
        $g4p_grand_parents1=g4p_load_indi_infos($g4p_a_parent['pere']->indi_id);
        if(isset($g4p_grand_parents1->parents))
        {
          foreach($g4p_grand_parents1->parents as $g4p_a_gparent1)
          {
            if($g4p_a_gparent1['rela_type']=='BIRTH' and $g4p_a_gparent1['pere']!=0)
              $g4p_ar_grand_parents1=g4p_load_indi_infos($g4p_a_gparent1['pere']->indi_id);
            if($g4p_a_gparent1['rela_type']=='BIRTH' and $g4p_a_gparent1['mere']!=0)
              $g4p_ar_grand_parents2=g4p_load_indi_infos($g4p_a_gparent1['mere']->indi_id);
          }
        }
      }
      if($g4p_a_parent['rela_type']=='BIRTH' and !empty($g4p_a_parent['mere']))
      {
        $g4p_grand_parents2=g4p_load_indi_infos($g4p_a_parent['mere']->indi_id);
        if(isset($g4p_grand_parents2->parents))
        {
          foreach($g4p_grand_parents2->parents as $g4p_a_gparent2)
          {
            if($g4p_a_gparent2['rela_type']=='BIRTH' and $g4p_a_gparent2['pere']!=0)
              $g4p_ar_grand_parents4=g4p_load_indi_infos($g4p_a_gparent2['pere']->indi_id);
            if($g4p_a_gparent2['rela_type']=='BIRTH' and $g4p_a_gparent2['mere']!=0)
              $g4p_ar_grand_parents3=g4p_load_indi_infos($g4p_a_gparent2['mere']->indi_id);
          }
        }
      }
    }
  }

  //bloc gauche
  $g4p_parent_event='';
  echo '<div class="bloc_gauche">';
  if(isset($g4p_ar_grand_parents1))
    $g4p_parent_event=affiche_case($g4p_ar_grand_parents1);
  else
    $g4p_parent=$g4p_langue['fproche_parent_inconnu'];

  echo '<div class="ar_gp_1" ',$g4p_parent_event,'>',$g4p_parent,'</div>';

  $g4p_parent_event='';
  if(isset($g4p_ar_grand_parents2))
    $g4p_parent_event=affiche_case($g4p_ar_grand_parents2);
  else
    $g4p_parent=$g4p_langue['fproche_parent_inconnu'];
  echo '<div class="ar_gp_2" ',$g4p_parent_event,'>',$g4p_parent,'</div>';

  echo '<div class="spacer">&nbsp;</div>';

  $g4p_parent_event='';
  if(isset($g4p_grand_parents1))
  {
    echo '<a href="'.g4p_make_url('','famille_proche.php','g4p_id='.$g4p_grand_parents1->indi_id,0),'"><img src="',$g4p_chemin,'images/fleche.png" alt="^" /></a>';
    $g4p_parent_event=affiche_case($g4p_grand_parents1);
  }
  else
    $g4p_parent=$g4p_langue['fproche_parent_inconnu'];
  echo '<div class="gp_1" ',$g4p_parent_event,'>',$g4p_parent,'</div>';
  echo '</div>';
  //fin bloc gauche

  // bloc de droite
  echo '<div class="bloc_droit">';
  $g4p_parent_event='';
  if(isset($g4p_ar_grand_parents4))
    $g4p_parent_event=affiche_case($g4p_ar_grand_parents4);
  else
    $g4p_parent=$g4p_langue['fproche_parent_inconnu'];
  echo '<div class="ar_gp_1" ',$g4p_parent_event,'>',$g4p_parent,'</div>';

  $g4p_parent_event='';
  if(isset($g4p_ar_grand_parents3))
    $g4p_parent_event=affiche_case($g4p_ar_grand_parents3);
  else
    $g4p_parent=$g4p_langue['fproche_parent_inconnu'];
  echo '<div class="ar_gp_2" ',$g4p_parent_event,'>',$g4p_parent,'</div>';

  echo '<div class="spacer">&nbsp;</div>';

  $g4p_parent_event='';
  if(isset($g4p_grand_parents2))
  {
    echo '<a href="'.g4p_make_url('','famille_proche.php','g4p_id='.$g4p_grand_parents2->indi_id,0),'"><img src="',$g4p_chemin,'images/fleche.png" alt="^" /></a>';
    $g4p_parent_event=affiche_case($g4p_grand_parents2);
  }
  else
    $g4p_parent=$g4p_langue['fproche_parent_inconnu'];
  echo '<div class="gp_1" ',$g4p_parent_event,'>',$g4p_parent,'</div>';
  echo '</div>';

 //fin bloc droit
  echo '<div class="spacer">&nbsp;</div>';

  $g4p_parent_event=$g4p_indi->nom.' '.$g4p_indi->prenom.'</strong><br />';
  if(isset($g4p_indi->evenements))
  {
    foreach($g4p_indi->evenements as $g4p_a_event)
    {
      $g4p_parent_event.='<em>'.$g4p_tag_def[$g4p_a_event->type].'</em> : '.g4p_date($g4p_a_event->date).' ';
      if(trim($g4p_a_event->description)!='')
        $g4p_parent_event.='('.$g4p_langue['fproche_description'].trim($g4p_a_event->description).') ';
      if(!empty($g4p_a_event->lieu))
        $g4p_parent_event.='('.$g4p_langue['fproche_lieu'].$g4p_a_event->lieu->toCompactString().') ';
      $g4p_parent_event.='<br />';
    }
    $g4p_parent_event.='<br /><em>'.$g4p_langue['fproche_freres_soeurs'].'</em><br />';
    if(isset($g4p_grand_parents2->familles) or isset($g4p_grand_parents1->familles))
    {
      if(isset($g4p_grand_parents2->familles))
        $g4p_tmp=$g4p_grand_parents2->familles;
      else
        $g4p_tmp=$g4p_grand_parents1->familles;
      foreach($g4p_tmp as $g4p_a_famille)
        if(isset($g4p_a_famille->enfants) and array_key_exists($g4p_indi->indi_id,$g4p_a_famille->enfants))
          foreach($g4p_a_famille->enfants as $g4p_a_enfant)
            if($g4p_a_enfant->indi_id!=$g4p_indi->indi_id)
              $g4p_parent_event.=$g4p_a_enfant->nom.' '.$g4p_a_enfant->prenom.' '.g4p_date(substr($g4p_a_enfant->date_rapide,20,-7)).'<br />';
    }
  }
  $g4p_parent_event=preg_replace("/['|\"]/","&quote;",$g4p_parent_event);
  $g4p_parent_event='onmouseover="AffBulle(\''.htmlspecialchars(addslashes('<strong>'.$g4p_parent_event), ENT_QUOTES ).'\')" onmouseout="HideBulle()"';

  echo '<div class="fiche_centrale">
  <span style="font-weight:bold;" ',$g4p_parent_event,'>',$g4p_indi->nom,' ',$g4p_indi->prenom,' <span class="petit">',g4p_date($g4p_indi->date_rapide,'date3'),'</span></span>';
  $g4p_nbre_enfant=0;
  if(isset($g4p_indi->familles))
  {
    $i=0;
    foreach($g4p_indi->familles as $g4p_a_famille)
    {
      $g4p_code_couleur_famille[$g4p_a_famille->id]=$g4p_couleur2[$i];
      if(++$i>=count($g4p_couleur2))
        $i=1;
  
      if(!empty($g4p_a_famille->conjoint->nom) or !empty($g4p_a_famille->conjoint->prenom))
        $g4p_tmp=$g4p_a_famille->conjoint->nom.' '.$g4p_a_famille->conjoint->prenom;
      else
        $g4p_tmp=$g4p_langue['fproche_conjoint_inc'];
  
      $g4p_parent_event='<b>'.$g4p_tmp.'</b><br />';
      
      if(!empty($g4p_a_famille->conjoint))
      {
        $g4p_conjoint=g4p_load_indi_infos($g4p_a_famille->conjoint->indi_id);
        if(isset($g4p_conjoint->evenements))
        {
          foreach($g4p_conjoint->evenements as $g4p_a_event)
          {
            $g4p_parent_event.='<em>'.$g4p_tag_def[$g4p_a_event->type].'</em> : '.g4p_date($g4p_a_event->date).' ';
            if(trim($g4p_a_event->description)!='')
              $g4p_parent_event.='('.$g4p_langue['fproche_description'].trim($g4p_a_event->description).') ';
            if(!empty($g4p_a_event->lieu))
              $g4p_parent_event.='('.$g4p_langue['fproche_lieu'].$g4p_a_event->lieu->toCompactString().') ';
            $g4p_parent_event.='<br />';
          }
        }
        $g4p_parent_event='onmouseover="AffBulle(\''.htmlspecialchars(addslashes($g4p_parent_event), ENT_QUOTES).'\')" onmouseout="HideBulle()"';
  
        echo '
         <div ',$g4p_parent_event,' style="background-color:',$g4p_code_couleur_famille[$g4p_a_famille->id],'">'
         ,$g4p_langue['fproche_marie'],'<a href="'.g4p_make_url('','famille_proche.php','g4p_id='.$g4p_a_famille->conjoint->indi_id,0),'">
         ',$g4p_tmp,'</a> <span class="petit">',g4p_date( $g4p_a_famille->conjoint->date_rapide,'date3'),'</span></div>';
      }
      else
      {
        $g4p_parent_event='onmouseover="AffBulle(\''.htmlspecialchars(addslashes($g4p_parent_event), ENT_QUOTES).'\')" onmouseout="HideBulle()"';
         echo '<div ',$g4p_parent_event,' style="background-color:',$g4p_code_couleur_famille[$g4p_a_famille->id],'">'
         ,$g4p_langue['fproche_marie'],$g4p_langue['fproche_conjoint_inc'],'</div>';
      }
      if(isset($g4p_a_famille->enfants))
        $g4p_nbre_enfant+=count($g4p_a_famille->enfants);
    }
  }
  echo '</div>';

// les enfants   $g4p_nbre_enfant;
  $i=$j=0;
  if(isset($g4p_indi->familles))
  {
  	echo '<div style="text-align:center; overflow:auto; padding:10px; height:auto;">';
  	echo '<table class="famille_proche"><tr>';
    foreach($g4p_indi->familles as $g4p_a_famille)
    {
      if(isset($g4p_a_famille->enfants))
      {
        foreach($g4p_a_famille->enfants as $g4p_a_enfant)
        {
          if(substr_count($g4p_a_enfant->prenom,' ')>0)
          {
            $g4p_prenom_court=explode(' ',$g4p_a_enfant->prenom);
            $g4p_prenom_court=$g4p_prenom_court[0];
          }
          else
            $g4p_prenom_court=$g4p_a_enfant->prenom;
  
          $g4p_liste_enfant[$i][$j]=g4p_load_indi_infos($g4p_a_enfant->indi_id);

          $g4p_parent_event='<b>'.$g4p_liste_enfant[$i][$j]->nom.' '.$g4p_liste_enfant[$i][$j]->prenom.'</b><br />';
          if(isset($g4p_liste_enfant[$i][$j]->evenements))
          {
            foreach($g4p_liste_enfant[$i][$j]->evenements as $g4p_a_event)
            {
              $g4p_parent_event.='<em>'.$g4p_tag_def[$g4p_a_event->type].'</em> : '.g4p_date($g4p_a_event->date).' ';
              if(trim($g4p_a_event->description)!='')
                $g4p_parent_event.='('.$g4p_langue['fproche_description'].trim($g4p_a_event->description).') ';
              if(!empty($g4p_a_event->lieu))
                $g4p_parent_event.='('.$g4p_langue['fproche_lieu'].$g4p_a_event->lieu->toCompactString().') ';
              $g4p_parent_event.='<br />';
            }
    
            if(isset($g4p_liste_enfant[$i][$j]->familles))
            {
              foreach($g4p_liste_enfant[$i][$j]->familles as $g4p_a_petite_famille)
              {
                $g4p_parent_event.='<br />';
                if(isset($g4p_a_petite_famille->conjoint))
                  $g4p_parent_event.=$g4p_langue['fproche_marie'].$g4p_a_petite_famille->conjoint->nom.' '.$g4p_a_petite_famille->conjoint->prenom.'<br />';
                if(isset($g4p_a_petite_famille->evenements))
                {
                  foreach($g4p_a_petite_famille->evenements as $g4p_famille_event)
                  {
                    $g4p_parent_event.=' <em>'.$g4p_tag_def[$g4p_famille_event->type].' :</em>';
                    $g4p_parent_event.=g4p_date($g4p_famille_event->date);
                    $g4p_parent_event.=($g4p_famille_event->description!='')?(' ('.$g4p_langue['fproche_description'].$g4p_famille_event->description.') '):('');
                    $g4p_parent_event.=(!empty($g4p_famille_event->lieu))?(' ('.$g4p_langue['fproche_lieu'].$g4p_famille_event->lieu->toCompactString().') '):('');
                    $g4p_parent_event.='<br />';
                  }
                }
              }
            }
          }
          $g4p_parent_event='onmouseover="AffBulle(\''.htmlspecialchars(addslashes($g4p_parent_event), ENT_QUOTES).'\')" onmouseout="HideBulle()"'; 
          echo '<td style="vertical-align:top; width:',round(100/$g4p_nbre_enfant),'%; border:black solid 1px; background-color:',$g4p_code_couleur_famille[$g4p_a_famille->id],'" >
            <a href="'.g4p_make_url('','famille_proche.php','g4p_id='.$g4p_a_enfant->indi_id,0),'"><img src="',$g4p_chemin,'images/fleche.png" alt="^" /></a><br />
            <a ',$g4p_parent_event,' href="',g4p_make_url('','famille_proche.php','g4p_id='.$g4p_a_enfant->indi_id,0),'">',$g4p_a_enfant->nom.' '.$g4p_prenom_court,'</a>';
          
          if(isset($g4p_liste_enfant[$i][$j]->familles))
          {
            echo '<br /><hr />';
            foreach($g4p_liste_enfant[$i][$j]->familles as $g4p_a_petite_famille)
            {
              if(isset($g4p_a_petite_famille->enfants))
              {
                foreach($g4p_a_petite_famille->enfants as $g4p_petit_enfant)
                {
                  if(substr_count($g4p_petit_enfant->prenom,' ')>0)
                  {
                    $g4p_prenom_court=explode(' ',$g4p_petit_enfant->prenom);
                    $g4p_prenom_court=$g4p_prenom_court[0];
                  }
                  else
                   $g4p_prenom_court=$g4p_petit_enfant->prenom;
                   $g4p_parent_event='<b>'.$g4p_petit_enfant->nom.' '.$g4p_petit_enfant->prenom.'</b><br />';
                   $g4p_parent_event.=g4p_date( $g4p_petit_enfant->date_rapide ,'date3').'<br />';
                   $g4p_parent_event='onmouseover="AffBulle(\''.htmlspecialchars(addslashes($g4p_parent_event), ENT_QUOTES).'\')" onmouseout="HideBulle()"';
                   echo '<a ',$g4p_parent_event,' href="',g4p_make_url('','famille_proche.php','g4p_id='.$g4p_petit_enfant->indi_id,0),'">',$g4p_petit_enfant->nom,' ',$g4p_prenom_court,'</a><br />';
                }
              }
            }
          }
  
          echo '</td>';
          $j++;
        }
      }
      $i++;
    }
    echo '</tr></table></div>';
  }

//hack pour NPDS
if($g4p_config['g4p_type_install']=='module-npds' or $g4p_config['g4p_type_install']=='module-npds-mod_rewrite')
  echo '</td></tr></table>';
else
  echo '</div>';

include($g4p_chemin.'pied_de_page.php');
?>
