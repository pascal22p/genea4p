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


if(!isset($_GET['g4p_page']))
    $_GET['g4p_page']=1;   
    
if(!isset($_GET['nom']))
    $_GET['nom'] = urldecode($_GET['id_nom']);
            

$sql="SELECT indi_id, indi_nom, indi_prenom, indi_sexe, indi_resn FROM `genea_individuals`";
$sql.=" WHERE base =".$_SESSION['genea_db_id']." AND indi_nom='".mysql_escape_string($_GET['nom'])."'";
if($_SESSION['permission']->permission[_PERM_MASK_INDI_])
    $sql.=" AND (indi_resn IS NULL OR indi_resn<>'privacy')";
$sql.=" ORDER BY indi_nom, indi_prenom LIMIT ".($_GET['g4p_page']-1)*_AFF_NBRE_PRENOM_.","._AFF_NBRE_PRENOM_;

$g4p_result=$g4p_mysqli->g4p_query($sql);
if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
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
        $sql="SELECT CONCAT('i',familles_husb) as familles_husb, indi_id, indi_nom, indi_prenom, indi_resn FROM genea_familles LEFT JOIN genea_individuals ON (familles_wife=indi_id) WHERE familles_husb IN (".$homme.") AND indi_id IS NOT NULL";
        $g4p_result_req=$g4p_mysqli->g4p_query($sql);
        $g4p_femme=$g4p_mysqli->g4p_result($g4p_result_req,'familles_husb',false);
    }
    else
        $g4p_femme=array();

    if($femme!='')
    {
        $sql="SELECT CONCAT('i',familles_wife) as familles_wife, indi_id, indi_nom, indi_prenom, indi_resn FROM genea_familles LEFT JOIN genea_individuals ON (familles_husb=indi_id) WHERE familles_wife IN (".$femme.") AND indi_id IS NOT NULL";
        $g4p_result_req=$g4p_mysqli->g4p_query($sql);
        $g4p_mari=$g4p_mysqli->g4p_result($g4p_result_req,'familles_wife',false);
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


    if(!empty($g4p_conjoints))
    {
        foreach($g4p_conjoints as $a_indi)
            $liste_indi2[]=$a_indi[0]['indi_id'];
        $liste_indi2=implode(',',$liste_indi2).','.$liste_indi;
    }
    else
        $liste_indi2=$liste_indi;

// evenement naissance

    $sql="SELECT genea_individuals.indi_id AS indi_id, events_details_gedcom_date AS naissance
        FROM `genea_individuals`
        JOIN rel_indi_events AS a ON ( genea_individuals.indi_id=a.indi_id )
        JOIN genea_events_details AS n ON ( a.events_details_id = n.events_details_id)
        WHERE genea_individuals.indi_id IN (".$liste_indi2.") AND (a.events_tag = 'BIRT' OR a.events_tag = 'BAPM' )";
    $g4p_result_req=$g4p_mysqli->g4p_query($sql);
    $g4p_naissance=$g4p_mysqli->g4p_result($g4p_result_req,'indi_id',false);
    //echo $sql.':nb='.count($g4p_naissance).':t='.intval($time_query*1000).' ms<br/>';

    // evenement deces
    $sql="SELECT genea_individuals.indi_id AS indi_id, events_details_gedcom_date AS deces
        FROM `genea_individuals`
        JOIN rel_indi_events AS a ON ( genea_individuals.indi_id=a.indi_id )
        JOIN genea_events_details AS n ON ( a.events_details_id = n.events_details_id)
        WHERE genea_individuals.indi_id IN (".$liste_indi2.") AND (a.events_tag = 'DEAT' OR a.events_tag = 'BURI' )";
    $g4p_result_req=$g4p_mysqli->g4p_query($sql);
    $g4p_deces=$g4p_mysqli->g4p_result($g4p_result_req,'indi_id',false);


    $g4p_titre_page='Patronyme : '.$g4p_result[0]['indi_nom'];
    require_once($g4p_chemin.'entete.php');

    echo '<div class="box_title"><h2>Liste des prénom - '.$g4p_result[0]['indi_nom'].'</h2></div>';

    $g4p_page=ceil($_GET['g4p_nbre']/_AFF_NBRE_PRENOM_); 
    echo '<div style="text-align:center;border-bottom:2px groove blue">'."<a href='javascript:history.back()' class=\"retour\">",$g4p_langue['retour'],"</a>&nbsp;";
    echo $_GET['g4p_nbre'],'&nbsp;',$g4p_langue['personnes_trouvees'];

    if($_GET['g4p_nbre']>_AFF_NBRE_PRENOM_)	
    {
        echo ', page n°',$_GET['g4p_page'],'&nbsp;';$g4p_langue['index_prenoms_page'];
        echo ' (',_AFF_NBRE_PRENOM_,'&nbsp;',$g4p_langue['personnes_page'],')';
        echo '<br />- ';

        for($i=1;$i<=$g4p_page;$i++)
        {
            if($_GET['g4p_page']==$i)
                echo $i,' - ';
            else
                echo '<a href="',g4p_make_url('','index.php','g4p_action=liste_prenom&genea_db_id='.$_SESSION['genea_db_id'].'&g4p_nbre='.$_GET['g4p_nbre'].'&g4p_page='.$i.'&nom='.$_GET['nom'],'prenoms-'.$_SESSION['genea_db_id'].'-'.$_GET['nom'].'-'.$_GET['g4p_nbre'].'-'.$i),'"> ',$i,' </a>-';
        }
    }
    echo '</div>';

    //echo $sql.':nb='.count($g4p_deces).':t='.intval($time_query*1000).' ms<br/>';
    echo '<ul>';

    $tit_conjoints=$tit_conjoint=' <img src="'.$g4p_chemin.'images/mariage.png" alt="mariage" class="icone_mar" /> '; //$g4p_langue['conjoint'];
    $tit_celibataire=$g4p_langue['celibataire'];

    foreach($g4p_result as $a_g4p_result)
    {
        $tmp_naissance=$tmp_deces='';
        if(!$_SESSION['permission']->permission[_PERM_MASK_INDI_] or $a_g4p_result['indi_resn']!='privacy')
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

            echo '<li><img src="'.$g4p_chemin.'images/'.$a_g4p_result['indi_sexe'].'.png" alt="'.
                $a_g4p_result['indi_sexe'].'" class="icone_sexe" /> <a class="liste" href="'.
                g4p_make_url('','fiche_individuelle.php','id_pers='.$a_g4p_result['indi_id'],'fiche-'.$_SESSION['genea_db_id'].'-'.g4p_prepare_varurl($a_g4p_result['indi_nom']).'-'.g4p_prepare_varurl($a_g4p_result['indi_prenom']).'-'.$a_g4p_result['indi_id']),'">',$a_g4p_result['indi_nom'],' ',$a_g4p_result['indi_prenom'],'</a>',$g4p_texte;
            //echo ' t=',intval($time_query*1000),' tt=',intval($t_q*1000);
        
            if(!empty($g4p_conjoints['i'.$a_g4p_result['indi_id']]))
            {
                $nb=count($g4p_conjoints['i'.$a_g4p_result['indi_id']]);
                if($nb>1) 
                    echo $tit_conjoints;
                else
                    echo $tit_conjoint;
                $cnt=1;
                foreach($g4p_conjoints['i'.$a_g4p_result['indi_id']] as $a_conjoint)
                {
                    echo $a_conjoint['indi_nom'].' '.$a_conjoint['indi_prenom'];

                    $tmp_naissance=$tmp_deces='';
                    if(!$_SESSION['permission']->permission[_PERM_MASK_INDI_] or $a_conjoint['indi_resn']!='privacy')
                    {
                        if(!empty($g4p_naissance[$a_conjoint['indi_id']]))
                        {
                            foreach($g4p_naissance[$a_conjoint['indi_id']] as $tmp)
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
          
                        if(!empty($g4p_deces[$a_conjoint['indi_id']]))
                        {
                            foreach($g4p_deces[$a_conjoint['indi_id']] as $tmp)
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

                        echo $g4p_texte;
                    }
                    if($cnt++<$nb)
                        echo ' - ';		
                }
            } 
            echo '</li>';
        }
    }
}
else
{
    require_once($g4p_chemin.'entete.php');
    echo '<h2>Aucune personnes trouvée</h2>';
}
//$t = g4p_getmicrotime()-$t_start;
//echo 'total affichage(foreach)=',intval($t*1000),' ms<br/>';
echo '</ul>';

echo '<div style="text-align:center;border-top:2px groove blue">'."<a href='javascript:history.back()' class=\"retour\">",$g4p_langue['retour'],"</a>&nbsp;";
echo $_GET['g4p_nbre'],'&nbsp;',$g4p_langue['personnes_trouvees'];
if($_GET['g4p_nbre']>_AFF_NBRE_PRENOM_)	
{
    echo ', page n°',$_GET['g4p_page'],'&nbsp;';$g4p_langue['index_prenoms_page'];
    echo ' (',_AFF_NBRE_PRENOM_,'&nbsp;',$g4p_langue['personnes_page'],')';
    echo '<br />- ';

    for($i=1;$i<=$g4p_page;$i++)
    {
        if($_GET['g4p_page']==$i)
            echo $i,' - ';
        else
            echo '<a href="',g4p_make_url('','index.php','g4p_action=liste_prenom&genea_db_id='.$_SESSION['genea_db_id'].'&g4p_nbre='.$_GET['g4p_nbre'].'&g4p_page='.$i.'&nom='.$_GET['nom'],'prenoms-'.$_SESSION['genea_db_id'].'-'.$_GET['nom'].'-'.$_GET['g4p_nbre'].'-'.$i),'"> ',$i,' </a>-';
    }
}
echo '</div>';

require_once($g4p_chemin.'pied_de_page.php');

?>
