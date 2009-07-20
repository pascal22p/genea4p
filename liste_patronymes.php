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
 *   Liste des patronymes toute bd confondue (utile pour les moteurs       *
 *                    de recherches                                        *
 *                                                                         *
 * création : aout 2005                                                    *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');

require_once($g4p_chemin.'entete.php');

if($_SESSION['permission']->permission[_PERM_MASK_DATABASE_])
{
    echo $g4p_langue['liste_patro_acces_interdis'];
    require_once($g4p_chemin.'pied_de_page.php');
    exit;
}

if(empty($_SESSION['genea_db_id']))
{
    if(empty($_GET['genea_db_id']))
        die('Oups, base non définie');
    else
        $_SESSION['genea_db_id']=(int)$_GET['genea_db_id'];
}

echo '<div class="box_title"><h2>Liste des patronymes</h2></div>';

if(!isset($_GET['patronyme']) or $_GET['patronyme']=='all')
{
    if(isset($_SESSION['patronyme']))
        unset($_SESSION['patronyme']);
    g4p_affiche_liste_nom(0,'');
}
else
{
    $_GET['patronyme']=urldecode($_GET['patronyme']);
    if(!empty($_SESSION['patronyme']))
    {
        $g4p_count_patro=count($_SESSION['patronyme']);
        if(strlen($_GET['patronyme'])<$g4p_count_patro)
        {
            $i=strlen($_GET['patronyme'])+1;
            while(isset($_SESSION['patronyme'][$i]))
            {
                unset($_SESSION['patronyme'][$i]);
                $i++;
            }
        }
    }
    g4p_affiche_liste_nom($_GET['patronyme'],$_GET['g4p_cpt']);
}

//echo '<center><a style="font-size:xx-small;padding-top:0:margin-top:0;" href="'.g4p_make_url('','liste_patronymes.php','',0).'">Liste complète de tous les patronymes, toutes bases confondues</a></center>';



require_once($g4p_chemin.'pied_de_page.php');






/*
if(!isset($_GET['g4p_page']))
  $_GET['g4p_page']=1;

$g4p_titre_h1=$g4p_titre_page='Liste des patronymes, page : '.$_GET['g4p_page'];
require_once($g4p_chemin.'entete.php');

echo '<div class="cadre">Cette page est surtout destinée aux moteurs de recherches. Elle n\'est mise à jour qu\'une fois par semaine</div>';

$sql_page="SELECT indi_id, indi_nom, indi_prenom, indi_sexe, base FROM `genea_individuals`";
if($_SESSION['permission']->permission[_PERM_MASK_INDI_])
  $sql_page.=" WHERE (resn IS NULL OR resn<>'privacy')";
$sql_page.=" LIMIT ".($_GET['g4p_page']-1)*_AFF_NBRE_PRENOM_LISTPATRO_.","._AFF_NBRE_PRENOM_LISTPATRO_;

if(file_exists($g4p_chemin.'cache/liste_patronymes/'.md5($sql_page).'.txt'))
{
  $g4p_ctime=filemtime($g4p_chemin.'cache/liste_patronymes/'.md5($sql_page).'.txt');
  if($g4p_ctime+7*24*3600>time())
    readfile($g4p_chemin.'cache/liste_patronymes/'.md5($sql_page).'.txt');
}
else
{
  $g4p_texte_page='';
  if($g4p_result=g4p_db_result_query($sql_page))
  {
    foreach($g4p_result as $g4p_a_result)
    {
      $liste_indi[]=$g4p_a_result['indi_id'];
      if($g4p_a_result['indi_sexe']=='M')
        $homme[]=$g4p_a_result['indi_id'];
      else
        $femme[]=$g4p_a_result['indi_id'];      
    }
    if(!empty($homme))
      $homme=implode(',',$homme);
    else
      $homme='';
    if(!empty($femme))
      $femme=implode(',',$femme);
    else
      $femme='';
    if(!empty($liste_indi))
      $liste_indi=implode(',',$liste_indi);
    else
      $liste_indi='';
    
    if($homme!='')
    {
      $sql="SELECT CONCAT('i',familles_husb) as familles_husb, indi_id, indi_nom, indi_prenom FROM genea_familles LEFT JOIN genea_individuals ON (familles_wife=indi_id) WHERE familles_husb IN (".$homme.")";
      $g4p_result_req=g4p_db_query($sql);
      $g4p_femme=g4p_db_result($g4p_result_req,'familles_husb',false);
    }
    else
      $g4p_femme=array();
  
    if($femme!='')
    {
      $sql="SELECT CONCAT('i',familles_wife) as familles_wife, indi_id, indi_nom, indi_prenom FROM genea_familles LEFT JOIN genea_individuals ON (familles_husb=indi_id) WHERE familles_wife IN (".$femme.")";
      $g4p_result_req=g4p_db_query($sql);
      $g4p_mari=g4p_db_result($g4p_result_req,'familles_wife',false);
    }
    else
      $g4p_mari=array();
  
    if(is_array($g4p_femme) and is_array($g4p_mari))
      $g4p_conjoints=array_merge($g4p_femme,$g4p_mari);
    elseif(is_array($g4p_femme))
      $g4p_conjoints=$g4p_femme;
    elseif(is_array($g4p_mari))
      $g4p_conjoints=$g4p_mari;
    else
      $g4p_conjoints=array();
    
    //echo '<pre>';
    //print_r($g4p_conjoints);
    
    // evenement naissance
    $sql="SELECT genea_individuals.indi_id AS indi_id, date_event AS naissance
      FROM `genea_individuals`
      JOIN rel_indi_event AS a ON ( genea_individuals.indi_id=a.indi_id )
      JOIN genea_events AS n ON ( a.events_id = n.id)
      WHERE genea_individuals.indi_id IN (".$liste_indi.") AND (n.type = 'BIRT' OR n.type = 'BAPM' )";
    $g4p_result_req=g4p_db_query($sql);
    $g4p_naissance=g4p_db_result($g4p_result_req,'indi_id',false);
    //echo $sql.':nb='.count($g4p_naissance).':t='.intval($time_query*1000).' ms<br/>';
  
    // evenement deces
    $sql="SELECT genea_individuals.indi_id AS indi_id, date_event AS deces
      FROM `genea_individuals`
      JOIN rel_indi_event AS a ON ( genea_individuals.indi_id=a.indi_id )
      JOIN genea_events AS n ON ( a.events_id = n.id)
      WHERE genea_individuals.indi_id IN (".$liste_indi.") AND (n.type = 'DEAT' OR n.type = 'BURI' )";
    $g4p_result_req=g4p_db_query($sql);
    $g4p_deces=g4p_db_result($g4p_result_req,'indi_id',false);
       
    $tit_conjoints=$tit_conjoint=' <img src="'.$g4p_chemin.'images/mariage.png" alt="mariage" class="icone_mar" /> '; //$g4p_langue['conjoint'];
    $tit_celibataire=$g4p_langue['celibataire'];
  
    $g4p_texte_page.='<div class="cadre">';
    $sql="SELECT COUNT(*) AS cpt FROM `genea_individuals`";
    if($_SESSION['permission']->permission[_PERM_MASK_INDI_])
      $sql.=" WHERE (resn IS NULL OR resn<>'privacy')";
    if($g4p_result_page=g4p_db_result_query($sql))
    {
      $max=ceil($g4p_result_page[0]['cpt']/_AFF_NBRE_PRENOM_LISTPATRO_);
      $g4p_pages_liste='<a href="'.g4p_make_url('','liste_patronymes.php','g4p_page=1').'"> &lt;&lt; </a>';
      for($i=1;$i<=$max;$i++)
      {
        if($i!=$_GET['g4p_page'])
          $g4p_pages_liste.='-<a href="'.g4p_make_url('','liste_patronymes.php','g4p_page='.$i).'"> '.$i.' </a>';
        else
          $g4p_pages_liste.='-<b> '.$i.' </b>';
        if($i+100<$_GET['g4p_page'])
          $i+=49;
        elseif($i+20<$_GET['g4p_page'])
          $i+=9;
        if($i>$_GET['g4p_page']+50)
          $i+=49;
        elseif($i>$_GET['g4p_page']+10)
          $i+=9;
      }
      $g4p_pages_liste.='-';
      $g4p_pages_liste.='<a href="'.g4p_make_url('','liste_patronymes.php','g4p_page='.$max).'"> &gt;&gt; </a>'."\r\n";
    }
    $g4p_texte_page.=$g4p_pages_liste.'<hr />';

    foreach($g4p_result as $a_g4p_result)
    {
      $tmp_naissance=$tmp_deces='';
      if(!$_SESSION['permission']->permission[_PERM_MASK_INDI_] or $a_g4p_result['indi_id']['resn']!='privacy')
      {
        if(!empty($g4p_naissance[$a_g4p_result['indi_id']]))
        {
          foreach($g4p_naissance[$a_g4p_result['indi_id']] as $tmp)
          {
            if($tmp['naissance']!='');
            {
              $tmp_naissance=$tmp['naissance'];
              break;
            }
          }            
        }
        if(!empty($tmp_naissance))
          $g4p_naissance_tmp='(°'.g4p_date($tmp_naissance);
        else
          $g4p_naissance_tmp='(';
          
        if(!empty($g4p_deces[$a_g4p_result['indi_id']]))
        {
          foreach($g4p_deces[$a_g4p_result['indi_id']] as $tmp)
          {
            if($tmp['deces']!='');
            {
              $tmp_deces=$tmp['deces'];
              break;
            }
          }            
        }
        if(!empty($tmp_deces))
          $g4p_deces_tmp='+'.g4p_date($tmp_deces).')';
        else
          $g4p_deces_tmp=')';
  
        if($g4p_naissance_tmp=='(' and $g4p_deces_tmp==')')
          $g4p_texte='';
        elseif($g4p_naissance_tmp!='(' and $g4p_deces_tmp!=')')
          $g4p_texte=' <span class="petit">'.$g4p_naissance_tmp.' - '.$g4p_deces_tmp.'</span>';
        else
          $g4p_texte=' <span class="petit">'.$g4p_naissance_tmp.$g4p_deces_tmp.'</span>';
  
        $g4p_texte_page.='<img src="'.$g4p_chemin.'images/'.$a_g4p_result['indi_sexe'].'.png" alt="Homme" class="icone_sexe" /> <a class="liste" href="'.g4p_make_url('','index.php','g4p_action=fiche_indi&genea_db_id='.$a_g4p_result['base'].'&id_pers='.$a_g4p_result['indi_id'],'fiche-'.$a_g4p_result['base'].'-'.g4p_prepare_varurl($a_g4p_result['indi_nom']).'-'.g4p_prepare_varurl($a_g4p_result['indi_prenom']).'-'.$a_g4p_result['indi_id']).'">'.$a_g4p_result['indi_nom'].' '.$a_g4p_result['indi_prenom'].'</a>'.$g4p_texte;
              
        if(!empty($g4p_conjoints['i'.$a_g4p_result['indi_id']]))
        {
          $nb=count($g4p_conjoints['i'.$a_g4p_result['indi_id']]);
          if($nb>1) 
            $g4p_texte_page.=$tit_conjoints;
          else
            $g4p_texte_page.=$tit_conjoint;
          $cnt=1;
          foreach($g4p_conjoints['i'.$a_g4p_result['indi_id']] as $a_conjoint)
          {
            $g4p_texte_page.=$a_conjoint['indi_nom'].' '.$a_conjoint['indi_prenom'];
            if($cnt++<$nb)
              $g4p_texte_page.=' - ';		
          }
        }
        $g4p_texte_page.='<br />'."\r\n";
      }
    }
    $g4p_texte_page.='<hr />';
    $g4p_texte_page.=$g4p_pages_liste.'<hr /></div>';     
    
    if($g4p_ecrit=fopen($g4p_chemin.'cache/liste_patronymes/'.md5($sql_page).'.txt','w'))
    {
      fwrite($g4p_ecrit,$g4p_texte_page);
      fclose($g4p_ecrit);
    }
    echo $g4p_texte_page;
  }
}
require_once($g4p_chemin.'pied_de_page.php');

*/
?>
