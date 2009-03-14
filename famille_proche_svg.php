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

if(isset($_GET['id_pers']))
  $g4p_indi=g4p_load_indi_infos($_GET['id_pers']);
else
    exit;
    
if (!isset($g4p_indi))
  die($g4p_langue['id_inconnu']);

header("Content-Type: image/svg+xml");

$dot_filename=uniqid();
$dot=fopen('/tmp/'.$dot_filename, 'w');
fwrite($dot, 'digraph family {
    ranksep="0.3";
    node [shape = record, fontname="Arial",fontsize="16"];'."\n");

function g4p_print_label($indi, $option='')
{
    return 'i'.$indi->indi_id.' '.$option.' [URL="'.g4p_make_url('','famille_proche_svg.php','id_pers='.$indi->indi_id,0).'", label="'.$indi->prenom.' '.$indi->nom.'\n'.$indi->date_rapide().'"]'."\n";
}

function g4p_load_parent($g4p_indi, $generation=0)
{
    global $dot;
    $generation++;
    if(!empty($g4p_indi->parents))
    {
        foreach($g4p_indi->parents as $a_parent)
        {
            if($a_parent->rela_type=='birth' or $a_parent->rela_type=='')
            {
                fwrite($dot, 'subgraph {');
                    //                'rank="same";'.
                    //                'margin="0,0";'.
                    //                'ranksep="0.1";'."\n".
                    
                if(!empty($a_parent->pere->indi_id))
                {
                    $pere=g4p_load_indi_infos($a_parent->pere->indi_id);
                    fwrite($dot, g4p_print_label($pere));
                    fwrite($dot, 'i'.$pere->indi_id.' -> f'.$a_parent->famille_id.";\n");
                }
                if(!empty($a_parent->mere->indi_id))
                {
                    $mere=g4p_load_indi_infos($a_parent->mere->indi_id);
                   fwrite($dot, g4p_print_label($mere));
                    fwrite($dot, 'i'.$mere->indi_id.' -> f'.$a_parent->famille_id.";\n");
                }
                fwrite($dot, 'f'.$a_parent->famille_id.' [label="mariés le : "];'."\n");
                fwrite($dot, "}\n");
                
                if(!empty($pere) and $generation<35)
                {
                    if($famille_id=g4p_load_parent($pere, $generation))
                        fwrite($dot, 'f'.$famille_id.' -> i'.$pere->indi_id."\n");
                    //fwrite($dot, 'i'.$pere->indi_id.' -> f'.$famille_id."\n");
                }
                if(!empty($mere) and $generation<35)
                {
                    if($famille_id=g4p_load_parent($mere, $generation))
                        fwrite($dot, 'f'.$famille_id.' -> i'.$mere->indi_id."\n");
                }
                
                return $a_parent->famille_id;                
            }
        }
    }
    return false;
}

//mes grands parents + parents
if($famille_id=g4p_load_parent($g4p_indi))
    fwrite($dot, 'f'.$famille_id.' -> i'.$g4p_indi->indi_id.' ;'."\n");

fwrite($dot, 'subgraph {'.
//                'rank="same";'.
//                'margin="0,0";'.
//                'ranksep="0.1";'."\n".
    g4p_print_label($g4p_indi,' [style="filled", fillcolor="#ffffaa"] '));

foreach($g4p_indi->familles as $key=>$a_famille)
{
    if(!empty($a_famille->husb->indi_id) and $a_famille->husb->indi_id!=$g4p_indi->indi_id)
    {
        fwrite($dot, g4p_print_label($a_famille->husb).
                'f'.$key.' [label="mariés le : "];'."\n".
                'i'.$g4p_indi->indi_id.' -> f'.$key.";\n".
                'i'.$a_famille->husb->indi_id.' -> f'.$key.";\n");
                //"}\n");
        $conjoint=g4p_load_indi_infos($a_famille->husb->indi_id);
        if($conjoint_parent=g4p_load_parent($conjoint))
            fwrite($dot, 'f'.$conjoint_parent.' -> i'.$a_famille->husb->indi_id.' ;'."\n");
        $conjoint=$a_famille->husb->indi_id;
    }
    elseif(!empty($a_famille->wife->indi_id) and $a_famille->wife->indi_id!=$g4p_indi->indi_id)
    {
        fwrite($dot, g4p_print_label($a_famille->wife).
                'f'.$key.' [label="mariés le : "];'."\n".
                'i'.$g4p_indi->indi_id.' -> f'.$key.";\n\n".
                'i'.$a_famille->wife->indi_id.' -> f'.$key.";\n");
                //"}\n");
        $conjoint=g4p_load_indi_infos($a_famille->wife->indi_id);
        if($conjoint_parent=g4p_load_parent($conjoint))
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
fwrite($dot, "}\n");

//if($familles)
//    fwrite($dot, 'f'.$g4p_indi->indi_id.' [label="'.implode('|',$familles).'"]'."\n");

if(!empty($enfants))
{
    foreach($enfants as $key=>$val)
    {
        foreach($val as $a_enfant)
        {
            fwrite($dot, $a_enfant['label']);
            fwrite($dot, ' f'.$key.' -> i'.$a_enfant['link'].';'."\n");
        }
    }
}

fwrite($dot, "}\n");
fclose($dot);

$output=shell_exec('dot -Tsvg /tmp/'.$dot_filename.' -o /tmp/'.$dot_filename.'.svg');

readfile('/tmp/'.$dot_filename.'.svg');

?>
