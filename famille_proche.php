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
//echo '<textarea cols=1000 rows=100>';

if(isset($_GET['id_pers']))
  $g4p_indi=g4p_load_indi_infos($_GET['id_pers']);

if (!isset($g4p_indi))
  die($g4p_langue['id_inconnu']);

$g4p_titre_page='Famille proche de : '.$g4p_indi->nom.' '.$g4p_indi->prenom;
require_once($g4p_chemin.'entete.php');

$filedot=uniqid();
$dot=fopen('/tmp/'.$filedot.'.dot','w');
fwrite($dot, 'digraph family {
    size="8,6";
    ranksep="0.3";
    node [shape = record,height=.1, fontsize=18];'."\n");

function g4p_print_label($indi, $option='')
{
    return 'i'.$indi->indi_id.' '.$option.' [URL="'.g4p_make_url('','famille_proche.php','id_pers='.$indi->indi_id,0).'"] [label="'.$indi->prenom.' '.$indi->nom.'\n'.$indi->date_rapide().'"]'."\n";
}

function g4p_load_gp($g4p_indi, $prefixe='')
{
    global $dot;
    
    if(!empty($g4p_indi->parents))
    {
        foreach($g4p_indi->parents as $a_parent)
        {
            if($a_parent->rela_type=='birth' or $a_parent->rela_type=='')
            {
                if(!empty($a_parent->pere->indi_id))
                {
                    $pere=g4p_load_indi_infos($a_parent->pere->indi_id);
                    if(!empty($pere->parents))
                    {
                        foreach($pere->parents as $a_gparent)
                        {
                            if($a_gparent->rela_type=='birth' or $a_gparent->rela_type=='')
                            {
                                if(!empty($a_gparent->pere->indi_id))
                                    $grand_pere_p=g4p_load_indi_infos($a_gparent->pere->indi_id);
                                if(!empty($a_gparent->mere->indi_id))
                                    $grand_mere_p=g4p_load_indi_infos($a_gparent->mere->indi_id);
                                $grand_parent_p_fam=$a_gparent->famille_id;
                                break;
                            }
                        }
                    }
                }
                if(!empty($a_parent->mere->indi_id))
                {
                    $mere=g4p_load_indi_infos($a_parent->mere->indi_id);
                    if(!empty($mere->parents))
                    {
                        foreach($mere->parents as $a_gparent)
                        {
                            if($a_gparent->rela_type=='birth' or $a_gparent->rela_type=='')
                            {
                                if(!empty($a_gparent->pere->indi_id))
                                    $grand_pere_m=g4p_load_indi_infos($a_gparent->pere->indi_id);
                                if(!empty($a_gparent->mere->indi_id))
                                    $grand_mere_m=g4p_load_indi_infos($a_gparent->mere->indi_id);
                                $grand_parent_m_fam=$a_gparent->famille_id;
                                break;
                            }
                        }
                    }
                }
                $parent_fam=$a_parent->famille_id;                
                break;
            }
        }

        //on redescend toute la descendance.
        if(!empty($grand_pere_p) and !empty($grand_mere_p))
        {
            fwrite($dot, 'subgraph {'.
//                'rank="same";'.
//                'margin="0,0";'.
//                'ranksep="0.1";'."\n".
                g4p_print_label($grand_pere_p).
                g4p_print_label($grand_mere_p).
                'f'.$grand_parent_p_fam.' [label="mariés le : "];'."\n".
                'i'.$grand_pere_p->indi_id.' -> f'.$grand_parent_p_fam.";\n".
                'i'.$grand_mere_p->indi_id.' -> f'.$grand_parent_p_fam.";\n".
                "}\n");
        }
        elseif(!empty($grand_pere_p) or !empty($grand_mere_p))
        {
            if(!empty($grand_pere_p))
                fwrite($dot, $prefixe.'fgpp [label="'.$grand_pere_p->prenom.' '.$grand_pere_p->nom.'\n'.$grand_pere_p->date_rapide().'"]'."\n");
            else
                fwrite($dot, $prefixe.'fgpp [label="'.$grand_mere_p->prenom.' '.$grand_mere_p->nom.'\n'.$grand_mere_p->date_rapide().'"]'."\n");    
        }

        if(!empty($grand_pere_m) and !empty($grand_mere_m))
        {
            fwrite($dot, 'subgraph {'.
//                'rank="same";'.
//                'margin="0,0";'.
//                'ranksep="0.1";'."\n".
                g4p_print_label($grand_pere_m).
                g4p_print_label($grand_mere_m).
                'f'.$grand_parent_m_fam.' [label="mariés le : "];'."\n".
                'i'.$grand_pere_m->indi_id.' -> f'.$grand_parent_m_fam.";\n".
                'i'.$grand_mere_m->indi_id.' -> f'.$grand_parent_m_fam.";\n".
                "}\n");
            //fwrite($dot, $prefixe.'fgpm [label="<i'.$grand_pere_m->indi_id.'>'.$grand_pere_m->prenom.' '.$grand_pere_m->nom.'\n'.$grand_pere_m->date_rapide().'|'.
            //    '<f'.$grand_mere_m->indi_id.'>'.$grand_mere_m->prenom.' '.$grand_mere_m->nom.'\n'.$grand_mere_m->date_rapide().'"]'."\n");
        }
        elseif(!empty($grand_pere_m) or !empty($grand_mere_m))
        {
            if(!empty($grand_pere_m))
                fwrite($dot, $prefixe.'fgpm [label="<i'.$grand_pere_m->indi_id.'>'.$grand_pere_m->prenom.' '.$grand_pere_m->nom.'\n'.$grand_pere_m->date_rapide().'"]'."\n");
            else
                fwrite($dot, $prefixe.'fgpm [label="<i'.$grand_mere_m->indi_id.'>'.$grand_mere_m->prenom.' '.$grand_mere_m->nom.'\n'.$grand_mere_m->date_rapide().'"]'."\n");    
        }

        if(!empty($grand_parent_p_fam))
            fwrite($dot,'f'.$grand_parent_p_fam.' -> i'.$pere->indi_id.";\n");
        if(!empty($grand_parent_m_fam))
            fwrite($dot,'f'.$grand_parent_m_fam.' -> i'.$mere->indi_id.";\n");

        if(!empty($pere) and !empty($mere))
        {
            fwrite($dot, 'subgraph {'.
//                'rank="same";'.
//                'margin="0,0";'.
//                'ranksep="0.1";'."\n".
                g4p_print_label($pere).
                g4p_print_label($mere).
                'f'.$parent_fam.' [label="mariés le : "];'."\n".
                'i'.$pere->indi_id.' -> f'.$parent_fam.";\n".
                'i'.$mere->indi_id.' -> f'.$parent_fam.";\n".
                "}\n");        
            //fwrite($dot, $prefixe.'fp [label="<i'.$pere->indi_id.'>'.$pere->prenom.' '.$pere->nom.'\n'.$pere->date_rapide().'|'.
             //   '<i'.$mere->indi_id.'>'.$mere->prenom.' '.$mere->nom.'\n'.$mere->date_rapide().'"]'."\n");
        }
        elseif(!empty($pere) or !empty($mere))
        {
            if(!empty($pere))
                fwrite($dot, 'subgraph {'.
    //                'rank="same";'.
    //                'margin="0,0";'.
//                    'ranksep="0.1";'."\n".
                    g4p_print_label($pere).
                    'f'.$parent_fam.' [label="mariés le : "];'."\n".
                    'i'.$pere->indi_id.' -> f'.$parent_fam.";\n".
                    "}\n");        
//                fwrite($dot, $prefixe.'fp [label="<i'.$pere->indi_id.'>'.$pere->prenom.' '.$pere->nom.'\n'.$pere->date_rapide().'"]'."\n");
            else
             fwrite($dot, 'subgraph {'.
//                'rank="same";'.
//                'margin="0,0";'.
//                'ranksep="0.1";'."\n".
                g4p_print_label($mere).
                'f'.$parent_fam.' [label="mariés le : "];'."\n".
                'i'.$mere->indi_id.' -> f'.$parent_fam.";\n".
                "}\n");        
//               fwrite($dot, $prefixe.'fp [label="<i'.$mere->indi_id.'>'.$mere->prenom.' '.$mere->nom.'\n'.$mere->date_rapide().'"]'."\n");    
        }
        
//        if(!empty($grand_pere_p) or !empty($grand_mere_p))
//            fwrite($dot, $prefixe.'f'.$grand_parent_p_fam.' -> '.$prefixe.'fp:i'.$pere->indi_id.' ;'."\n");
//        if(!empty($grand_pere_m) or !empty($grand_mere_m))
//            fwrite($dot, $prefixe.'f'.$grand_parent_m_fam.' -> '.$prefixe.'fp:i'.$mere->indi_id.' ;'."\n");
            
        return $parent_fam;
    }
    else
        return false;
}

//mes grands parents + parents
if($parent_fam=g4p_load_gp($g4p_indi))
    fwrite($dot, 'f'.$parent_fam.' -> i'.$g4p_indi->indi_id.' ;'."\n");

             fwrite($dot, 'subgraph {'.
//                'rank="same";'.
//                'margin="0,0";'.
//                'ranksep="0.1";'."\n".
                g4p_print_label($g4p_indi,' [style="filled", fillcolor="#ffffaa"] '));
foreach($g4p_indi->familles as $key=>$a_famille)
{
    if(!empty($a_famille->husb->indi_id) and $a_famille->husb->indi_id!=$g4p_indi->indi_id)
    {
        fwrite($dot,g4p_print_label($a_famille->husb).
                'f'.$key.' [label="mariés le : "];'."\n".
                'i'.$g4p_indi->indi_id.' -> f'.$key.";\n".
                'i'.$a_famille->husb->indi_id.' -> f'.$key.";\n");
                //"}\n");
        $conjoint=g4p_load_indi_infos($a_famille->husb->indi_id);
        if($conjoint_parent=g4p_load_gp($conjoint,'c'))
            fwrite($dot, 'f'.$conjoint_parent.' -> i'.$a_famille->husb->indi_id.' ;'."\n");
        $conjoint=$a_famille->husb->indi_id;
    }
    elseif(!empty($a_famille->wife->indi_id) and $a_famille->wife->indi_id!=$g4p_indi->indi_id)
    {
        fwrite($dot,g4p_print_label($a_famille->wife).
                'f'.$key.' [label="mariés le : "];'."\n".
                'i'.$g4p_indi->indi_id.' -> f'.$key.";\n\n".
                'i'.$a_famille->wife->indi_id.' -> f'.$key.";\n");
                //"}\n");
        $conjoint=g4p_load_indi_infos($a_famille->wife->indi_id);
        if($conjoint_parent=g4p_load_gp($conjoint,'c'))
            fwrite($dot, 'f'.$conjoint_parent.' -> i'.$a_famille->wife->indi_id.' ;'."\n");
        $conjoint=$a_famille->wife->indi_id;
    }
        
    if(!empty($a_famille->enfants))
    {
        foreach($a_famille->enfants as $a_enfant)
        {
            $enfants[$key][]=array('label'=>g4p_print_label($a_enfant['indi']),
                'link'=>$a_enfant['indi']->indi_id);
        }
    }
}
fwrite($dot,"}\n");

//if($familles)
//    fwrite($dot, 'f'.$g4p_indi->indi_id.' [label="'.implode('|',$familles).'"]'."\n");

if(!empty($enfants))
{
    foreach($enfants as $key=>$val)
    {
        foreach($val as $a_enfant)
        {
            fwrite($dot,$a_enfant['label']);
            fwrite($dot, ' f'.$key.' -> i'.$a_enfant['link'].';'."\n");
        }
    }
}

fwrite($dot,"}\n");
fclose($dot);

$output=shell_exec('dot -Tsvg /tmp/'.$filedot.'.dot -o test.svg 2> /tmp/error');

//echo '<pre>'.readfile('/tmp/error').'</pre>';
//echo "<pre>$output</pre>";

//echo '-------------------';
//echo '<object type="image/svg+xml">';
$bouh=file('test.svg');
unset($bouh[0]);
unset($bouh[1]);
unset($bouh[2]);
unset($bouh[3]);
unset($bouh[4]);
unset($bouh[5]);
unset($bouh[6]);
unset($bouh[7]);
echo "\n";
echo implode("\n",$bouh);
//echo '</object>';

echo '/tmp/'.$filedot.'.dot';
include($g4p_chemin.'pied_de_page.php');
?>
