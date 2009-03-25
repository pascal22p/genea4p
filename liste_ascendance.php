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
15 générations au maximum sont affichées ($generation)
500 personnes sont affichées au maximum
*/

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'p_conf/script_start.php');
$_SESSION['message']='';

function recursive_ascendance($g4p_id, $generation=1, $sosa=1)
{
  global $g4p_liste_personnes2, $g4p_liste_personnes, $g4p_couleur
    , $g4p_chemin, $g4p_limite_pers, $cache_count, $g4p_limite_generation, $g4p_sosa, $g4p_langue
    , $g4p_max_gen, $g4p_max_pers;

    $g4p_indi_info=g4p_load_indi_infos($g4p_id);
    if($_SESSION['permission']->permission[_PERM_MASK_INDI_] and $g4p_indi_info->resn=='privacy')
        return;

  if($_GET['implexe']==1)
  {
    $g4p_sosa[$g4p_indi_info->indi_id][]=$sosa;
    $g4p_max_pers=_NBRE_MAX_PERS_ASCENDANCE_IMPLEXE_;
    $g4p_max_gen=_NBRE_MAX_GENERATION_ASCENDANCE_IMPLEXE_;
  }
  else
  {
    $g4p_max_pers=_NBRE_MAX_PERS_ASCENDANCE_;
    $g4p_max_gen=_NBRE_MAX_GENERATION_ASCENDANCE_;
  }
  if($g4p_limite_pers>$g4p_max_pers)
  {
    if(strpos($_SESSION['message'],'<br /><b>'.sprintf($g4p_langue['liste_asc_max_pers'],$g4p_max_pers).'</b>')===FALSE)
      $_SESSION['message'].='<br /><b>'.sprintf($g4p_langue['liste_asc_max_pers'],$g4p_max_pers).'</b>';
    return;
  }
  if($cache_count>_NBRE_MAX_CACHE_CREES_)
  {
    if(strpos($_SESSION['message'],'<br /><b>'.$g4p_langue['liste_asc_max_cache'].'</b>')===FALSE)
      $_SESSION['message'].='<br /><b>'.$g4p_langue['liste_asc_max_cache'].'</b>';
    return;
  }

  if(!isset($g4p_liste_personnes2[$generation]))
    $g4p_liste_personnes2[$generation]=1;
  else
    $g4p_liste_personnes2[$generation]++;

  if(!isset($g4p_liste_personnes[$generation][$g4p_indi_info->indi_id]))
  {
    $g4p_limite_pers++;
    $g4p_liste_personnes[$generation][$g4p_indi_info->indi_id]['sosa']=$sosa;
    $g4p_liste_personnes[$generation][$g4p_indi_info->indi_id]['indi_id']=$g4p_indi_info->indi_id;
    $g4p_liste_personnes[$generation][$g4p_indi_info->indi_id]['base']=$g4p_indi_info->base;
    $g4p_liste_personnes[$generation][$g4p_indi_info->indi_id]['nom']=$g4p_indi_info->nom;
    $g4p_liste_personnes[$generation][$g4p_indi_info->indi_id]['prenom']=$g4p_indi_info->prenom;
    $g4p_liste_personnes[$generation][$g4p_indi_info->indi_id]['date']=$g4p_indi_info->date_rapide();
  }
  else
  {
     if($_GET['implexe']==0)
       return;
  }
  if(isset($g4p_indi_info->parents))
  {
    foreach($g4p_indi_info->parents as $g4p_a_parent)
    {
      if($g4p_a_parent->rela_type=='BIRTH');
      {
        if($generation>$_GET['g4p_generation'] or $generation>$g4p_max_gen)
        {
          if(strpos($_SESSION['message'],'<br />'.$g4p_langue['liste_asc_max_gen'])===FALSE)
            $_SESSION['message'].='<br />'.$g4p_langue['liste_asc_max_gen'];
          break;
        }

        if(isset($g4p_a_parent->pere))
          recursive_ascendance($g4p_a_parent->pere->indi_id, $generation+1, $sosa*2);

        if(isset($g4p_a_parent->mere))
          recursive_ascendance($g4p_a_parent->mere->indi_id, $generation+1, ($sosa*2)+1);
      }
    }
  }
}

$g4p_titre_page='Liste d\'ascendance de : '.$g4p_indi->nom.' '.$g4p_indi->prenom;

if(!isset($_GET['g4p_generation']))
    $_GET['g4p_generation']=5;

if(!isset($_GET['implexe']))
    $_GET['implexe']=0;

$g4p_limite_pers=0;
recursive_ascendance($g4p_indi->indi_id);
include($g4p_chemin.'entete.php');

echo '<div class="box_title"><h2>',sprintf($g4p_langue['liste_asc_titre'],$g4p_indi->nom,$g4p_indi->prenom),'</h2></div>'."\n";


$lien='';
echo '<ul><li>';
echo $g4p_langue['liste_asc_nb_gen'];
for($i=1;$i<=$g4p_max_gen;$i+=2)
    $lien.='<a href="'.g4p_make_url('','liste_ascendance.php','id_pers='.$g4p_indi->indi_id.'&g4p_generation='.$i.'&implexe='.$_GET['implexe'],'').'">'.$i.'</a> - ';
echo substr($lien,0,-2);
echo '</li><li>',$g4p_langue['liste_asc_recherche_implexe'];
if($_GET['implexe']==0)
    echo '<a href="'.g4p_make_url('','liste_ascendance.php','id_pers='.$g4p_indi->indi_id.'&g4p_generation='.$_GET['g4p_generation'].'&implexe=1','liste_ascendance-'.$_SESSION['genea_db_id'].'-'.g4p_prepare_varurl($g4p_indi->nom).'-'.g4p_prepare_varurl($g4p_indi->prenom).'-'.$g4p_indi->indi_id.'-'.$_GET['g4p_generation'].'-implexe').'">Activer</a>';
else
    echo '<a href="'.g4p_make_url('','liste_ascendance.php','id_pers='.$g4p_indi->indi_id.'&g4p_generation='.$_GET['g4p_generation'].'&implexe=0','liste_ascendance-'.$_SESSION['genea_db_id'].'-'.g4p_prepare_varurl($g4p_indi->nom).'-'.g4p_prepare_varurl($g4p_indi->prenom).'-'.$g4p_indi->indi_id.'-'.$_GET['g4p_generation']).'">Désactiver</a>';
echo '</li><li>',$g4p_langue['liste_asc_info_sosa'];
$a=0;
echo '</li></ul>';

echo '<dl>';
foreach($g4p_liste_personnes as $g4p_generation=>$g4p_liste_generation)
{
    echo '<dt style="margin-bottom:0.3em;">';
    if($_GET['implexe']==1)
        echo sprintf($g4p_langue['liste_asc_nb_pers_implexe'],($g4p_generation-1),$g4p_liste_personnes2[$g4p_generation],count($g4p_liste_generation)),'<br />';
    else
        echo sprintf($g4p_langue['liste_asc_nb_pers'],($g4p_generation-1),count($g4p_liste_generation)),'<br />';
    echo '</dt><dd>';
    echo '<ul>';
    foreach($g4p_liste_generation as $g4p_a_personne)
    {
        $tmp_indi=g4p_load_indi_infos($g4p_a_personne['indi_id']);
        echo '<li><strong>',number_format($g4p_a_personne['sosa'],0,',',' '),'</strong> - ',g4p_link_nom($tmp_indi);
        if($_GET['implexe']==1 and count($g4p_sosa[$g4p_a_personne['indi_id']])>1)
        {
            echo ' <span style="font-size:smaller; color:blue;">',$g4p_langue['liste_asc_implexe'];
            foreach($g4p_sosa[$g4p_a_personne['indi_id']] as $g4p_a_sosa)
                echo number_format($g4p_a_sosa,0,',',' '),', ';
            echo '</span>';
        }
        echo '</li>';
    }
    echo '</ul>';
    echo '</dd>';
}
echo '</dl>';

if($_GET['implexe']==1)
    echo sprintf($g4p_langue['liste_asc_nb_asc_total_implexe'],array_sum($g4p_liste_personnes2),$g4p_limite_pers),'</div>';
else
    echo sprintf($g4p_langue['liste_asc_nb_asc_total'],$g4p_limite_pers),'</div>';


include($g4p_chemin.'pied_de_page.php');
?>
