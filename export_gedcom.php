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
 *                         Export GEDCOM                                   *
 *                                                                         *
 * dernière mise à jour : février 2005                                     *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

//script basé sur l'algorythme de export_dot.php

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

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

$sql="SELECT nom FROM genea_infos WHERE id=".$_REQUEST['base'];
$g4p_infos_req=$g4p_mysqli->g4p_query($sql);
$g4p_base_select=$g4p_mysqli->g4p_result($g4p_infos_req);
$genea_db_nom=$g4p_base_select[0]['nom'];

// Les requètes SQL
$sql="SELECT indi_id
         FROM genea_individuals WHERE base=".(int)$_REQUEST['base']." AND indi_resn IS NULL 
         ORDER BY indi_nom, indi_prenom";
$g4p_infos_req=$g4p_mysqli->g4p_query($sql);
$g4p_liste_indis=$g4p_mysqli->g4p_result($g4p_infos_req);

// le serveur va souffrir... Si le cache est vide, des miliers de requètes vont être exécutés.

$file=uniqid();
//$file='test';
$latex=fopen('/tmp/'.$file.'.ged','w');
$date=date("d M Y");
$time=date("H:i:s");

fwrite($latex,<<<EOD
0 HEAD 
1 SOUR Genea4p
2 VERS 0.2.99
2 NAME Genea4p
2 DATA 
3 DATE $date
1 DATE $date
2 TIME $time
1 FILE fichier
1 GEDC 
2 VERS 5.5.1
2 FORM LINEAGE-LINKED
1 CHAR UTF-8
1 LANG French
1 PLAC 
2 FORM lieu dit, ville, code postal, numero insee, département, région, pays, longitude, latitude
EOD;
);

foreach($g4p_liste_indis as $indi_id)
{
    $indi_id=$indi_id['indi_id'];
    $g4p_indi=g4p_load_indi_infos($indi_id);
    g4p_latex_write_indi($g4p_indi);
}


















































// ---------------------- MEMORY CACHE

/** sauve les info des personnes chargées */
$dotInfo = array();
$dotFamilyInfo = array();
$link_list=array();

/** retour les infos a propos d'un id */
function getPersonInfoWithoutCache($id) {
    global $dotInfo;
    if (array_key_exists($id, $dotInfo)) {
        return $dotInfo[$id];
    }
    return g4p_load_indi_infos($id);
}

/** retour les infos a propos d'un id */
function getPersonInfo($id) {
    global $dotInfo;
    if (!array_key_exists($id, $dotInfo)) {
        $dotInfo[$id] = g4p_load_indi_infos($id);
    }
    return $dotInfo[$id];
}

function isPersonLoaded($id) {
    global $dotInfo;
    return array_key_exists($id, $dotInfo);
}

/** place la famille en memoire 
 * ### Truc de merde vue qu'on l'a deja :D en param
 */
function getFamily($family, $indi,$sexe) {
    global $dotFamilyInfo;
    $family->indi=$indi;
    $family->sexe=$sexe;
    $dotFamilyInfo[$family->id] = $family;
    return $dotFamilyInfo[$family->id];
}

function isFamilyLoadedById($familyId) {
    global $dotFamilyInfo;
    return array_key_exists($familyId, $dotFamilyInfo);
}

function isFamilyLoaded($family) {
    global $dotFamilyInfo;
    return array_key_exists($family->id, $dotFamilyInfo);
}

function isLinkLoaded($id)
{
  global $link_list;
  return in_array($id,$link_list);
}

define('CIBLE'      ,0);
define('DESCENDANT' ,1);
define('ASCENDANT'  ,2);
define('EXTRA'      ,4);
define('JUSTINFO'   ,8);

// modif pascal : Ca commence ici! J'ai tout réécris, je n'arrivais pas à faire ce que je voulais
function getRecDotText($rootId, $familyId, $depth, $flag) {
    global $dotConfig, $dotMemory, $link_list;

    // si la personne a déjà été traité !
    if (isPersonLoaded($rootId)) return;
    $info = getPersonInfo($rootId);

    //traite le conjoint d'une seule famille pour l'affichage de l'ascendance,
    //1ère condit°=affichage de l'ascendance + descendance ($depth>0 => partie ascendante)
    //2ème condition : ascendance pure
    if(($depth>0 and !$dotConfig['displayExtra'] and $dotConfig['displayAsc'] and $dotConfig['displayDes']) or (!$dotConfig['displayExtra'] and $dotConfig['displayAsc'] and !$dotConfig['displayDes']))
    {
        // traite les mariages et la descendance de la personne
        if (isset($info->familles[$familyId])) {
            $isFamilyLoaded = isFamilyLoadedById($familyId);

            // genere la famille
            if (!$isFamilyLoaded) {
                getFamily($info->familles[$familyId],$info->indi_id,$info->sexe); // désolé

                // genere et lie le conjoint a la famille
                if (isset($info->familles[$familyId]->conjoint)) {

                    $id = $info->familles[$familyId]->conjoint->indi_id;
                    // $hasLoaded = isPersonLoaded($id);
                    //modif pascal, j'ai trop compris les anciennes lignes, je prefere ca:
                    $newflag=$flag;
                    if(!$hasLoaded=isPersonLoaded($id))
                      getRecDotText($id, $familyId, $depth, $newflag);
                    
               }
            }
        }
    }
    else
    {
      // traite les mariages et/ou la descendance de la personne
      if (isset($info->familles)) {
        // modif pascal
        if(!isset($_GET['usedesdepth']) and floatval('-'.($_GET['desdepth']))>=$depth)
           return;

            foreach ($info->familles as $familyId => $family) {

                $isFamilyLoaded = isFamilyLoadedById($familyId);

                // genere la famille
                if (!$isFamilyLoaded) {
                    getFamily($family, $info->indi_id,$info->sexe); // désolé

                    // genere et lie le conjoint a la famille
                    if (isset($family->conjoint)) {

                        $newflag=$flag;

                        $id = $family->conjoint->indi_id;
                        if(!$hasLoaded=isPersonLoaded($id))
                          getRecDotText($id, $familyId, $depth, $newflag);

                    }
                } else {
                    // genere et lie les enfants
                    if (isset($family->enfants) and $dotConfig['displayDes'] and floatval('-'.($_GET['desdepth']))<$depth) {

                        $newflag=$flag;

                        foreach ($family->enfants as $id => $individu) {
                            if(!isPersonLoaded($id))
                                getRecDotText($id, $familyId, $depth - 1, $newflag);
                        }
                    }
                }
            }
        }
    }

    // filtre
    if ($flag == JUSTINFO) return;

    // si il y a des parents (recurssif)
    //($dotConfig['displayAsc'] or ($depth<0 and !$dotConfig['displayExtra'] and $dotConfig['displayAsc'] and $dotConfig['displayDes']) or ($dotConfig['displayExtra'] and $dotConfig['displayAsc'] and $dotConfig['displayDes'])) = $dotConfig['displayAsc'] ;) ou comment se compliqué la vie!
    if (isset($info->parents) and $dotConfig['displayAsc']) {
        foreach($info->parents as $newFamilyId=>$parent) {
            // modif pascal
            if(!isset($_GET['useascdepth']) and ($_GET['ascdepth']-2)<$depth)
              $flag = JUSTINFO;

            if ($parent['rela_type']=='BIRTH') {
                $fatherId = 'unkown';
                $motherId = 'unkown';

                $newflag=$flag;

                if (isset($parent['pere']) and $parent['pere'] != 0) {
                    $fatherId = $parent['pere']->indi_id;
                    getRecDotText($fatherId, $newFamilyId, $depth + 1, $newflag);
                }

                if (isset($parent['mere']) and $parent['mere'] != 0) {
                    $motherId = $parent['mere']->indi_id;
                    getRecDotText($motherId, $newFamilyId, $depth + 1, $newflag);
                }
            }
        }
    }
}

/** Retourne la syntaxe dot de l'arbre ascendant commencant par $rootId
 * @param $rootId l'identifieur de la racine de l'arbre ascendant
 */
function getDotText($rootId) {
    global $dotConfig, $dotMemory, $link_list, $dotInfo, $dotFamilyInfo, $g4p_config;
    if ($rootId == 0) return "/* id error */";
    

    getRecDotText($rootId, 0, 0, CIBLE);

//ecriture du gedcom

header("Content-type: text/plain; charset=UTF-8");

$fichier="0 HEAD
1 SOUR Genea4p
2 VERS ".$g4p_config['g4p_version']."
2 NAME genea4p
2 CORP PAROIS Pascal
1 DATE ".date('d M Y')."
1 CHAR ANSEL
1 GEDC
2 VERS 5.5
2 FORM Lineage-Linked\r\n";

foreach($dotInfo as $g4p_a_individu)
{
  $fichier.='0 @I'.$g4p_a_individu->indi_id."@ INDI\r\n";
  $fichier.=' 1 NAME '.$g4p_a_individu->prenom.'/'.$g4p_a_individu->nom."/\r\n";
  if($g4p_a_individu->npfx)
    $fichier.='  2 NPFX '.$g4p_a_individu->npfx."\r\n";
  if($g4p_a_individu->givn)
    $fichier.='  2 GIVN '.$g4p_a_individu->givn."\r\n";
  if($g4p_a_individu->nick)
    $fichier.='  2 NICK '.$g4p_a_individu->nick."\r\n";
  if($g4p_a_individu->spfx)
    $fichier.='  2 SPFX '.$g4p_a_individu->spfx."\r\n";
  if($g4p_a_individu->surn)
    $fichier.='  2 SURN '.$g4p_a_individu->surn."\r\n";
  if($g4p_a_individu->nsfx)
    $fichier.='  2 NSFX '.$g4p_a_individu->nsfx."\r\n";
  $fichier.=' 1 SEX '.$g4p_a_individu->sexe."\r\n";

//evenements individuels
  if(isset($g4p_a_individu->evenements))
  {
    foreach($g4p_a_individu->evenements as $g4p_a_ievents)
    {
      $fichier.=' 1 '.$g4p_a_ievents->type." ".$g4p_a_ievents->description."\r\n";
      if($g4p_a_ievents->type=='EVEN')
        $fichier.='  2 TYPE '.$g4p_a_ievents->description."\r\n";
      if($g4p_a_ievents->date!='')
        $fichier.='  2 DATE '.$g4p_a_ievents->date."\r\n";
      if(!empty($g4p_a_ievents->lieu))
        $fichier.='  2 PLAC '.$g4p_a_ievents->lieu->toString()."\r\n";
      if($g4p_a_ievents->cause!='')
        $fichier.='  2 CAUS '.$g4p_a_ievents->cause."\r\n";
      if($g4p_a_ievents->age!=0)
        $fichier.='  2 AGE '.$g4p_a_ievents->age."\r\n";

      /*
      if(isset($g4p_rel_events_sources[$g4p_a_ievents]))
        foreach($g4p_rel_events_sources[$g4p_a_ievents] as $g4p_a_source)
          $fichier.='  2 SOUR @S'.$g4p_a_source."@\r\n";
      if(isset($g4p_rel_events_notes[$g4p_a_ievents]))
        foreach($g4p_rel_events_notes[$g4p_a_ievents] as $g4p_a_note)
          $fichier.='  2 NOTE @N'.$g4p_a_note."@\r\n";
      if(isset($g4p_rel_events_multimedia[$g4p_a_ievents]))
        foreach($g4p_rel_events_multimedia[$g4p_a_ievents] as $g4p_a_media)
          $fichier.='  2 OBJE @O'.$g4p_a_media."@\r\n";
      
      //relations
      if(isset($g4p_rel_asso_events[$g4p_a_ievents]))
      {
        foreach($g4p_rel_asso_events[$g4p_a_ievents] as $g4p_a_asso)
        {
          $fichier.='  2 ASSO @I'.$g4p_a_asso['indi_id']."@\r\n";
          if($g4p_a_asso['description'])
            $fichier.='   3 RELA '.$g4p_a_asso['description']."\r\n";
        }
      }
      */
    }
  }

// CHAN
  if(!empty($g4p_a_individu->indi_chan) and $g4p_a_individu->indi_chan!='0000-00-00 00:00:00')
  {
    $g4p_a_individu->indi_chan=explode(' ',$g4p_a_individu->indi_chan);
    $fichier.=" 1 CHAN\r\n";
    setlocale(LC_TIME, 'en');
    $fichier.="  2 DATE ".g4p_strftime('%d %b %Y',strtotime($g4p_a_individu->indi_chan[0]))."\r\n";
    setlocale(LC_TIME, 'fr');
    $fichier.="   3 TIME ".$g4p_a_individu->indi_chan[1]."\r\n";
  }

// Le conjoint
  if(isset($g4p_a_individu->familles))
  {
    foreach($g4p_a_individu->familles as $g4p_a_famille)//affiche tous les mariages
    {
      if(array_key_exists($g4p_a_famille->id, $dotFamilyInfo))
        $fichier.=' 1 FAMS @F'.$g4p_a_famille->id."@\r\n";
    }
  }

// les parents
  if (isset($g4p_a_individu->parents))
  {
    foreach($g4p_a_individu->parents as $g4p_famille_id=>$g4p_un_parent)
    {
      if(array_key_exists($g4p_famille_id, $dotFamilyInfo))
      {
        $fichier.=' 1 FAMC @F'.$g4p_famille_id."@\r\n";
        if($g4p_un_parent->rela_type=='adopted')
          $fichier.="  2 PEDI adopted\r\n";
      }
    }
  }
/*
//Les notes et sources
  if(isset($g4p_rel_indi_sources[$g4p_a_individu->indi_id']]))
    foreach($g4p_rel_indi_sources[$g4p_a_individu->indi_id']] as $g4p_a_source)
      $fichier.=' 1 SOUR @S'.$g4p_a_source."@\r\n";
  if(isset($g4p_rel_indi_notes[$g4p_a_individu->indi_id']]))
    foreach($g4p_rel_indi_notes[$g4p_a_individu->indi_id']] as $g4p_a_note)
      $fichier.=' 1 NOTE @N'.$g4p_a_note."@\r\n";
  if(isset($g4p_rel_indi_multimedia[$g4p_a_individu->indi_id']]))
    foreach($g4p_rel_indi_multimedia[$g4p_a_individu->indi_id']] as $g4p_a_media)
      $fichier.=' 1 OBJE @O'.$g4p_a_media."@\r\n";
*/
  //relations
  if(isset($g4p_a_individu->asso))
  {
    foreach($g4p_a_individu->asso as $g4p_a_asso)
    {
      if(array_key_exists($g4p_a_asso->indi_id, $dotInfo))
      {
        $fichier.=' 1 ASSO @I'.$g4p_a_asso->indi_id."@\r\n";
        if($g4p_a_asso->description)
          $fichier.=' 2 RELA '.$g4p_a_asso->description."\r\n";
      }
    }
  }
}

// la famille
foreach($dotFamilyInfo as $g4p_a_famille)
{
  $fichier.='0 @F'.$g4p_a_famille->id."@ FAM\r\n";
  if(isset($g4p_a_famille->evenements))
  {
    //evenements familiaux
    foreach($g4p_a_famille->evenements as $g4p_a_fevent)
    {
      $fichier.=' 1 '.$g4p_a_fevent->type." ".$g4p_a_fevent->description."\r\n";
      if($g4p_a_fevent->type=='EVEN')
        $fichier.='  2 TYPE '.$g4p_a_fevent->description."\r\n";
      if($g4p_a_fevent->date!='')
        $fichier.='  2 DATE '.$g4p_a_fevent->date."\r\n";
      if(!empty($g4p_a_fevent->lieu))
        $fichier.='  2 PLAC '.$g4p_a_fevent->lieu->toString()."\r\n";
      if($g4p_a_fevent->age!='')
        $fichier.='  2 AGE '.$g4p_a_fevent->age."\r\n";
      if($g4p_a_fevent->cause!='')
        $fichier.='  2 CAUS '.$g4p_a_fevent->cause."\r\n";

/*
      if(isset($g4p_rel_events_sources[$g4p_a_fevent]))
        foreach($g4p_rel_events_sources[$g4p_a_fevent] as $g4p_a_source)
          $fichier.='  2 SOUR @S'.$g4p_a_source."@\r\n";
      if(isset($g4p_rel_events_notes[$g4p_a_fevent]))
        foreach($g4p_rel_events_notes[$g4p_a_fevent] as $g4p_a_note)
          $fichier.='  2 NOTE @N'.$g4p_a_note."@\r\n";
      if(isset($g4p_rel_events_multimedia[$g4p_a_fevent]))
        foreach($g4p_rel_events_multimedia[$g4p_a_fevent] as $g4p_a_media)
          $fichier.='  2 OBJE @O'.$g4p_a_media."@\r\n";
*/

      //relations
      if(isset($g4p_a_fevent->asso))
      {
        foreach($g4p_a_fevent->asso as $g4p_a_asso)
        {
          if(array_key_exists($g4p_a_asso->indi_id, $dotInfo))
          {
            $fichier.='  2 ASSO @I'.$g4p_a_asso->indi_id."@\r\n";
            if($g4p_a_asso->description)
              $fichier.='   3 RELA '.$g4p_a_asso->description."\r\n";
          }
        }
      }
    }
  }
  
  if(isset($g4p_a_famille->conjoint) and $g4p_a_famille->conjoint->sexe=='F')
  {
    if(isset($g4p_a_famille->indi))
      $fichier.=' 1 HUSB @I'.$g4p_a_famille->indi."@\r\n";
    if(isset($g4p_a_famille->conjoint))
      $fichier.=' 1 WIFE @I'.$g4p_a_famille->conjoint->indi_id."@\r\n";
  }
  elseif(isset($g4p_a_famille->sexe) and $g4p_a_famille->sexe=='F')
  {
    if(isset($g4p_a_famille->conjoint))
      $fichier.=' 1 HUSB @I'.$g4p_a_famille->conjoint->indi_id."@\r\n";
    if(isset($g4p_a_famille->indi_id))
      $fichier.=' 1 WIFE @I'.$g4p_a_famille->indi_id."@\r\n";
  }
  elseif(isset($g4p_a_famille->conjoint) and $g4p_a_famille->conjoint->sexe=='M')
  {
    if(isset($g4p_a_famille->indi))
      $fichier.=' 1 WIFE @I'.$g4p_a_famille->indi."@\r\n";
    if(isset($g4p_a_famille->conjoint))
      $fichier.=' 1 HUSB @I'.$g4p_a_famille->conjoint->indi_id."@\r\n";
  }
  elseif(isset($g4p_a_famille->sexe) and $g4p_a_famille->sexe=='M')
  {
    if(isset($g4p_a_famille->conjoint))
      $fichier.=' 1 WIFE @I'.$g4p_a_famille->conjoint->indi_id."@\r\n";
    if(isset($g4p_a_famille->indi_id))
      $fichier.=' 1 HUSB @I'.$g4p_a_famille->indi_id."@\r\n";
  }
  

  if(isset($g4p_a_famille->enfants))
    foreach($g4p_a_famille->enfants as $g4p_a_enfant)
      $fichier.=' 1 CHIL @I'.$g4p_a_enfant->indi_id."@\r\n";

// CHAN
  if(!empty($g4p_a_famille->familles_chan) and $g4p_a_famille->familles_chan!='0000-00-00 00:00:00')
  {
    $g4p_a_famille->familles_chan=explode(' ',$g4p_a_famille->familles_chan);
    $fichier.=" 1 CHAN\r\n";
    setlocale(LC_TIME, 'en');
    $fichier.="  2 DATE ".g4p_strftime('%d %b %Y',strtotime($g4p_a_famille->familles_chan[0]))."\r\n";
    setlocale(LC_TIME, 'fr');
    $fichier.="   3 TIME ".$g4p_a_famille->familles_chan[1]."\r\n";
  }

/*
  if(isset($g4p_rel_familles_sources[$g4p_a_famille['familles_id']]))
    foreach($g4p_rel_familles_sources[$g4p_a_famille['familles_id']] as $g4p_a_source)
      $fichier.=' 1 SOUR @S'.$g4p_a_source."@\r\n";
  if(isset($g4p_rel_familles_notes[$g4p_a_famille['familles_id']]))
    foreach($g4p_rel_familles_notes[$g4p_a_famille['familles_id']] as $g4p_a_note)
      $fichier.=' 1 NOTE @N'.$g4p_a_note."@\r\n";
  if(isset($g4p_rel_familles_multimedia[$g4p_a_famille['familles_id']]))
    foreach($g4p_rel_familles_multimedia[$g4p_a_famille['familles_id']] as $g4p_a_media)
      $fichier.=' 1 OBJE @O'.$g4p_a_media."@\r\n";
*/
}
/*
//les médias
if(is_array($g4p_media_liste))
{
  foreach($g4p_media_liste as $g4p_a_media)
  {
    $fichier.='0 @O'.$g4p_a_media['id']."@ OBJE \r\n";
    $fichier.=' 1 FORM '.$g4p_a_media['format']."\r\n";
    $fichier.=' 1 TITL '.$g4p_a_media['title']."\r\n";
    $fichier.=' 1 FILE '.$g4p_a_media['file']."\r\n";

// CHAN
    if($g4p_a_media['chan']!='0000-00-00 00:00:00')
    {
      $g4p_a_media['chan']=explode(' ',$g4p_a_media['chan']);
      $fichier.=" 1 CHAN\r\n";
      setlocale(LC_TIME, 'en');
      $fichier.="  2 DATE ".g4p_strftime('%d %b %Y',strtotime($g4p_a_media['chan'][0]))."\r\n";
      setlocale(LC_TIME, 'fr');
      $fichier.="   3 TIME ".$g4p_a_media['chan'][1]."\r\n";
    }
  }
}


//les notes
if(is_array($g4p_notes_liste))
{
  foreach($g4p_notes_liste as $g4p_a_note)
  {
    $fichier.='0 @N'.$g4p_a_note['notes_id']."@ NOTE ";
    $g4p_a_note['notes_text']=wordwrap($g4p_a_note['notes_text'],200,"&&rc&& 1 CONC ",1);
    $g4p_a_note['notes_text']=str_replace("\r\n", "\r\n1 CONT ", trim($g4p_a_note['notes_text']));
    $fichier.=str_replace("&&rc&&", "\r\n", trim($g4p_a_note['notes_text']))."\r\n";
// CHAN
    if($g4p_a_note['notes_chan']!='0000-00-00 00:00:00')
    {
      $g4p_a_note['notes_chan']=explode(' ',$g4p_a_note['notes_chan']);
      $fichier.=" 1 CHAN\r\n";
      setlocale(LC_TIME, 'en');
      $fichier.="  2 DATE ".g4p_strftime('%d %b %Y',strtotime($g4p_a_note['notes_chan'][0]))."\r\n";
      setlocale(LC_TIME, 'fr');
      $fichier.="   3 TIME ".$g4p_a_note['notes_chan'][1]."\r\n";
    }
  }
}
//les sources
if($g4p_sources_liste)
{
  foreach($g4p_sources_liste as $g4p_a_source)
  {
    $fichier.='0 @S'.$g4p_a_source['sources_id']."@ SOUR\r\n";
    $fichier.=' 1 TITL '.$g4p_a_source['sources_title']."\r\n";
    $fichier.=' 1 AUTH '.$g4p_a_source['sources_auth']."\r\n";
    $fichier.=' 1 PAGE '.$g4p_a_source['sources_page']."\r\n";
    if(!empty($g4p_a_source['repo_id']))
    {
      $fichier.=' 1 REPO @R'.$g4p_a_source['repo_id']."@\r\n";
      $fichier.='  2 CALN '.$g4p_a_source['sources_caln']."\r\n";
      $fichier.='   3 MEDI '.$g4p_a_source['sources_medi']."\r\n";
    }
    $fichier.=' 1 TEXT ';
    $g4p_a_source['sources_text']=wordwrap($g4p_a_source['sources_text'],200,"&&rc&& 1 CONC ",1);
    $g4p_a_source['sources_text']=str_replace("\r\n", "\r\n1 CONT ", trim($g4p_a_source['sources_text']));
    $fichier.=str_replace("&&rc&&", "\r\n", trim($g4p_a_source['sources_text']))."\r\n";
    $fichier.=' 1 PUBL ';
    $g4p_a_source['sources_publ']=wordwrap($g4p_a_source['sources_publ'],200,"&&rc&& 1 CONC ",1);
    $g4p_a_source['sources_publ']=str_replace("\r\n", "\r\n1 CONT ", trim($g4p_a_source['sources_publ']));
    $fichier.=str_replace("&&rc&&", "\r\n", trim($g4p_a_source['sources_publ']))."\r\n";

// CHAN
    if($g4p_a_source['sources_chan']!='0000-00-00 00:00:00')
    {
      $g4p_a_source['sources_chan']=explode(' ',$g4p_a_source['sources_chan']);
      $fichier.=" 1 CHAN\r\n";
      setlocale(LC_TIME, 'en');
      $fichier.="  2 DATE ".g4p_strftime('%d %b %Y',strtotime($g4p_a_source['sources_chan'][0]))."\r\n";
      setlocale(LC_TIME, 'fr');
      $fichier.="   3 TIME ".$g4p_a_source['sources_chan'][1]."\r\n";
    }

    //les médias
    if(isset($g4p_rel_sources_medias[$g4p_a_source['sources_id']]))
      foreach($g4p_rel_sources_medias[$g4p_a_source['sources_id']] as $g4p_a_media)
        $fichier.=' 1 OBJE @O'.$g4p_a_media."@\r\n");

  }
}

//les dépots
if($g4p_repo_liste)
{
  foreach($g4p_repo_liste as $g4p_a_repo)
  {
    $fichier.="0 @R".$g4p_a_repo['repo_id']."@ REPO\r\n";
    $fichier.=" 1 NAME ".$g4p_a_repo['repo_name']."\r\n";
    $fichier.=" 1 ADDR ".$g4p_a_repo['repo_addr']."\r\n";
    $fichier.="  2 CITY ".$g4p_a_repo['repo_city']."\r\n";
    $fichier.="  2 POST ".$g4p_a_repo['repo_post']."\r\n";
    $fichier.="  2 CTRY ".$g4p_a_repo['repo_ctry']."\r\n";
    $fichier.=" 1 PHON ".$g4p_a_repo['repo_phon']."\r\n";

// CHAN
    if($g4p_a_repo['repo_chan']!='0000-00-00 00:00:00')
    {
      $g4p_a_repo['repo_chan']=explode(' ',$g4p_a_repo['repo_chan']);
      $fichier.=" 1 CHAN\r\n";
      setlocale(LC_TIME, 'en');
      $fichier.="  2 DATE ".g4p_strftime('%d %b %Y',strtotime($g4p_a_repo['repo_chan'][0]))."\r\n";
      setlocale(LC_TIME, 'fr');
      $fichier.="   3 TIME ".$g4p_a_repo['repo_chan'][1]."\r\n";
    }
  }
}
*/

$fichier.='0 TRLR';

//fin de l'ecriture du gedcom

    return $fichier;
}

// ---------------------- INTERFACE

/** Genere un fichier de sortie au format DOT */
function generateDotFile($rootId) {
    global $dotConfig;

    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    
    $dot = getDotText($rootId);
    header("Content-type: text/plain; charset=ISO-8859-1");
    header("Content-Disposition: attachment; filename=gedcom.ged");
    echo utf8_decode($dot);
/*
    if (EXPORT_DOT_DEBUG) {
        $fp = fopen('dot/family.dot', 'wt');
        fwrite($fp, $dot);
        fclose($fp);
    }*/
}

function updateDotConfig() {
    global $dotConfig, $g4p_config, $g4p_langue;
    
    if (isset($_GET['type'])) {
        $type = $_GET['type'];
        switch($type) {
        case 'asc':
            $dotConfig['displayAsc']   = true;
            $dotConfig['displayDes']   = false;
            $dotConfig['displayExtra'] = false;
            break;
        case 'des':
            $dotConfig['displayAsc']   = false;
            $dotConfig['displayDes']   = true;
            $dotConfig['displayExtra'] = false;
            break;
        case 'both':
            $dotConfig['displayAsc']   = true;
            $dotConfig['displayDes']   = true;
            $dotConfig['displayExtra'] = false;
            break;
        case 'nolimit':
            $dotConfig['displayAsc']   = true;
            $dotConfig['displayDes']   = true;
            $dotConfig['displayExtra'] = true;
            break;
        }
    }

    if(!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
      if($g4p_langue['dot_max_asc']+$g4p_langue['dot_max_desc']>$g4p_config['dot_limit_gen'])
        $g4p_langue['dot_max_asc']=$g4p_langue['dot_max_desc']=ceil($g4p_config['dot_limit_gen']/2);
      if(isset($_GET['useascdepth']))
        unset($_GET['useascdepth']);
      if(isset($_GET['usedesdepth']))
        unset($_GET['usedesdepth']);
    }    
    
}

function displayIntoGenetiqueUI($rootId) {
    global $g4p_chemin, $time_start, $sql_count, $g4p_config, $g4p_langue;
    require_once($g4p_chemin . 'entete.php');

    echo '<div class="cadre" style="text-align:left;">';
    echo '<h2>',$g4p_langue['expot_gedcom_titre'],'</h2>';
    echo "<h3>",$g4p_langue['expot_gedcom_sstitre_info'],"</h3>";

    $info = getPersonInfoWithoutCache($rootId);
    
    echo '<form action="',g4p_make_url('','export_gedcom.php','',0),'" method="GET">';
    echo '<label for="g4p_id">';
    if ($info->indi_id != 0) {
        echo '<a href="'.g4p_make_url('','index.php','g4p_action=fiche_indi&id_pers='
        . $rootId,'fiche-'.g4p_prepare_varurl($info->nom).'-'.g4p_prepare_varurl($info->prenom).'-'.$rootId) . '">' . $info->nom . ' ' . $info->prenom . '</a> ';
    } else {
        echo $g4p_langue['id_inconnu'];
    }
    echo '</label>';
    echo '<input type="text" name="g4p_id" value="' . $rootId . '" size="4" />';
    echo '<input type="submit" value="',$g4p_langue['expot_gedcom_submit_id'],'" />';
    echo '</form>';
    echo '<a href="',g4p_make_url('','recherche.php','type=indi_0&g4p_referer='.rawurlencode('|export_gedcom.php|'.$_SERVER['QUERY_STRING']).'&g4p_champ=g4p_id',0).'">Rechercher Identifieur</a>';


    echo "<h3>",$g4p_langue['expot_gedcom_sstitre_config'],"</h3>";
    ?>    
    <form action="<?=$g4p_chemin?>export_gedcom.php" method="GET">
    <input type="hidden" name="g4p_id" value="<?php echo $_GET["g4p_id"]; ?>" />

        <strong>Type d'extraction :</strong>
        <div style="position:relative;float:left;padding-right:5em;">
        <input type="radio" class="coche" name="type" value="asc" checked ><?=$g4p_langue['expot_gedcom_arbre_asc']?></input>
        <br/><input type="radio" class="coche" name="type" value="des"><?=$g4p_langue['expot_gedcom_arbre_desc']?></input>
        <br/><input type="radio" class="coche" name="type" value="both"><?=$g4p_langue['expot_gedcom_arbre_asc_desc']?></input>
        <br/><input type="radio" class="coche" name="type" value="nolimit"><?=$g4p_langue['expot_gedcom_arbre_complet']?></input>
        </div>
        <div style="position:relative;float:left">
        <?php
        if(!$_SESSION['permission']->permission[_PERM_SOURCE_]) 
          $g4p_disble='disabled="disabled"';
        else
          $g4p_disble='';
        echo '<input type="checkbox" class="coche" name="shownote" value="true" ',$g4p_disble,'>',$g4p_langue['expot_gedcom_inserer_source'],'</input><BR />';
        if(!$_SESSION['permission']->permission[_PERM_NOTE_]) 
          $g4p_disble='disabled="disabled"';
        else
          $g4p_disble='';
        echo '<input type="checkbox" class="coche" name="showsource" value="true" ',$g4p_disble,'>',$g4p_langue['expot_gedcom_inserer_note'],'</input><br/>';
        if(!$_SESSION['permission']->permission[_PERM_MULTIMEDIA_]) 
          $g4p_disble='disabled="disabled"';
        else
          $g4p_disble='';
        echo '<input type="checkbox" class="coche" name="showmedia" value="true" ',$g4p_disble,'>',$g4p_langue['expot_gedcom_inserer_media'],'</input>
        </div>
        <hr style="border:none;clear:both;margin:0.5em" />';


        ?>
        <strong>       
<?php 
if(!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
{
  printf($g4p_langue['dot_limit_gen'],$g4p_config['dot_limit_gen']);
  echo '<br />';
}
?>
        </strong>
        <strong><?=$g4p_langue['dot_max_asc']?></strong>
        <br/><input type="text" name="ascdepth" value="7" size="4" />
        &nbsp;&nbsp;
        
<?php 
if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
         echo '<input type="checkbox" class="coche" name="useascdepth" >',$g4p_langue['dot_nolimit'],'</input>';
?>         
        <br/>
        <strong><?=$g4p_langue['dot_max_desc']?></strong>
        <br/><input type="text" name="desdepth" value="7" size="4" />
        &nbsp;&nbsp;
<?php 
if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
         echo '<input type="checkbox" class="coche" name="usedesdepth" >',$g4p_langue['dot_nolimit'],'</input>';
?>         
        <br/>
        <br/>
        
    <br/>
    <input type="hidden" name="gen" value="ged" />
    <input type="submit" value="<?=$g4p_langue['expot_gedcom_generer']?>" />
    </form>
    <?php

    if (EXPORT_DOT_DEBUG) {
        echo '<pre>';
        getDotText($rootId);
        echo '</pre>';
    }

    echo '</div>';
    require_once($g4p_chemin . 'pied_de_page.php');
}

function displayMessage($message) {
    global $g4p_chemin, $time_start, $sql_count, $g4p_config;
    require_once($g4p_chemin . 'entete.php');
    echo '<div class="cadre" style="text-align:left;">';
    echo $message;
    echo '</div>';
    require_once($g4p_chemin . 'copyright.php');
    require_once($g4p_chemin . 'pied_de_page.php');
}

// ---------------------- EXECUTION

if(!$_SESSION['permission']->permission[_PERM_GEDCOM_])
{
    require_once($g4p_chemin . 'entete.php');
    echo '<div class="cadre" style="text-align:left;">';
    echo $g4p_langue['acces_non_autorise'];
    echo '</div>';
    require_once($g4p_chemin . 'copyright.php');
    require_once($g4p_chemin . 'pied_de_page.php');
    exit;
}

//configure les option de dot en fonction de la requette
updateDotConfig();

// filtre les entrees
$id = 0;
$gen = '';
if (isset($_GET['g4p_id'])) {
    $id = $_GET['g4p_id'];
}
if (isset($_GET['gen'])) {
    $gen = $_GET['gen'];
}

// protection
if ($g4p_config['DOT_EXEC'] != true && $gen != 'dot') {
    $gen == '';
}
if ($id == 0) {
    $gen == '';
}

if($id==0)
  $id=$g4p_indi->indi_id;

if (is_numeric($id) && $id >= 0) {
    switch($gen) {
    case '':
        displayIntoGenetiqueUI($id);
        break;
    case 'ged':
        generateDotFile($id);
        break;
    default:
        generateMediaFile($id, $gen);
        break;
    }
} else {
    displayMessage($g4p_langue['dot_erreur_id']);
}

?>
