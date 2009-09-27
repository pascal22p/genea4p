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
            
$sql="SELECT indi_id  FROM `genea_individuals`";
$sql.=" WHERE base =".$_SESSION['genea_db_id']." AND indi_nom='".$g4p_mysqli->escape_string($_GET['nom'])."'";
if(!$_SESSION['permission']->permission[_PERM_MASK_INDI_])
    $sql.=" AND (indi_resn IS NULL OR indi_resn<>'privacy')";
$sql.=" ORDER BY indi_nom, indi_prenom LIMIT ".($_GET['g4p_page']-1)*_AFF_NBRE_PRENOM_.","._AFF_NBRE_PRENOM_;

$g4p_result=$g4p_mysqli->g4p_query($sql);
if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
{
   $g4p_titre_page='Patronyme : '.$_GET['nom'];
    require_once($g4p_chemin.'entete.php');

    echo '<div class="box_title"><h2>Liste des prénom - '.$_GET['nom'].'</h2></div>';

    $g4p_page=ceil($_GET['g4p_nbre']/_AFF_NBRE_PRENOM_); 
    echo '<div style="text-align:center;border-bottom:1px solid black">'."<a href='javascript:history.back()' class=\"retour\">",$g4p_langue['retour'],"</a>&nbsp;";
    echo $_GET['g4p_nbre'],'&nbsp;',$g4p_langue['personnes_trouvees'];

    if($_GET['g4p_nbre']>_AFF_NBRE_PRENOM_)	
    {
        echo ', page n°',$_GET['g4p_page'],'&nbsp;';$g4p_langue['index_prenoms_page'];
        echo ' (',_AFF_NBRE_PRENOM_,'&nbsp;',$g4p_langue['personnes_page'],')';
        echo '<br /><big>   ';

        for($i=1;$i<=$g4p_page;$i++)
        {
            if($_GET['g4p_page']==$i)
                echo $i,'   ';
            else
                echo '<a href="',g4p_make_url('','liste_prenoms.php','genea_db_id='.$_SESSION['genea_db_id'].'&g4p_nbre='.$_GET['g4p_nbre'].'&g4p_page='.$i.'&nom='.$_GET['nom'],''),'"> ',$i,' </a>   ';
        }
        echo '</big>';
    }
    echo '</div>';

    echo '<ul>';
    
    foreach($g4p_result as $g4p_a_result)
    {
        $indi=g4p_load_indi_infos($g4p_a_result['indi_id']);
        echo '<li><img src="'.$g4p_chemin.'images/'.$indi->sexe.'.png" alt="'.
            $indi->sexe.'" class="icone_sexe" /> '.
            g4p_link_nom($indi->indi_id);
        $conjoint=array();
        if(!empty($indi->familles))
        {
            foreach($indi->familles as $a_famille)
            {
                if(!empty($a_famille->husb) and $indi->indi_id!=$a_famille->husb->indi_id)
                    $conjoint[]=g4p_link_nom($a_famille->husb->indi_id);
                elseif(!empty($a_famille->wife) and $indi->indi_id!=$a_famille->wife->indi_id)
                    $conjoint[]=g4p_link_nom($a_famille->wife->indi_id);
            }
            if(!empty($conjoint))
                echo ' <img src="'.$g4p_chemin.'images/mariage.png" alt="mariage" class="icone_mar" /> '.implode(' <img src="'.$g4p_chemin.'images/mariage.png" alt="mariage" class="icone_mar" /> ',$conjoint);
        }
        echo '</li>';
    }
    
    echo '<div style="margin-top:1em;text-align:center;border-top:1px solid black">'."<a href='javascript:history.back()' class=\"retour\">",$g4p_langue['retour'],"</a>&nbsp;";
    echo $_GET['g4p_nbre'],'&nbsp;',$g4p_langue['personnes_trouvees'];
    if($_GET['g4p_nbre']>_AFF_NBRE_PRENOM_)	
    {
        echo ', page n°',$_GET['g4p_page'],'&nbsp;';$g4p_langue['index_prenoms_page'];
        echo ' (',_AFF_NBRE_PRENOM_,'&nbsp;',$g4p_langue['personnes_page'],')';
        echo '<br /><big>   ';

        for($i=1;$i<=$g4p_page;$i++)
        {
            if($_GET['g4p_page']==$i)
                echo $i,'   ';
            else
                echo '<a href="',g4p_make_url('','liste_prenoms.php','genea_db_id='.$_SESSION['genea_db_id'].'&g4p_nbre='.$_GET['g4p_nbre'].'&g4p_page='.$i.'&nom='.$_GET['nom'],''),'"> ',$i,' </a>   ';
        }
        echo '</big></div>';
    }
    echo '</div>';

    require_once($g4p_chemin.'pied_de_page.php');

}
else
{
    require_once($g4p_chemin.'entete.php');
    echo '<h2>Aucune personnes trouvée</h2>';
    require_once($g4p_chemin.'pied_de_page.php');
}


?>
