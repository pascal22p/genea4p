<?php
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

$liste_nodes=array();
$liste_links=array();
$output_list=array('svg'=>'svg', 'png'=>'png', 'pdf'=>'pdf', 'java'=>'svg');
$output_headers=array('svg'=>'Content-Type: image/svg+xml', 'png'=>'Content-Type: image/png', 
    'pdf'=>"Content-type: application/pdf");
        
if(isset($_GET['limite_ascendance']) and $_GET['limite_ascendance']<25)
    $limite_ascendance=$_GET['limite_ascendance'];
else
    $limite_ascendance=2;
if(isset($_GET['limite_descendance']) and $_GET['limite_descendance']<10 and $_GET['limite_descendance']>=0)
    $limite_descendance=$_GET['limite_descendance'];
else
    $limite_descendance=1;
if(!empty($_GET['fulldesc']))
    define('_fulldesc_',true);
else
    define('_fulldesc_',false);
if(!empty($_GET['output']) and !empty($output_list[$_GET['output']]))
    $output=$output_list[$_GET['output']];
else
    $output='svg';
define('_origine_',$_GET['id_pers']);

header($output_headers[$output]);

$dot_filename=uniqid();
$dot=fopen('/tmp/'.$dot_filename, 'w');
fwrite($dot, 'digraph arbre {
    bgcolor="transparent";
    ranksep="1.5";
    fontname="LiberationSans";
    node [shape = record, margin="0.45,0.05"];'."\n");
/*
    dpi="72";
    size="8,100";
*/

function g4p_print_label($indi, $option=' [style="filled", fillcolor="#ffffff"] ')
{
    global $liste_nodes;
    
    $tmp=array();
    foreach($_GET as $key=>$val)
    {
        if($key!='id_pers')
            $tmp[]='&'.$key.'='.$val;
    }
    if(empty($liste_nodes['i'.$indi->indi_id]))
    {
        $liste_nodes['i'.$indi->indi_id]=true;
        //return 'i'.$indi->indi_id.' '.$option.' [URL="'.g4p_make_url('','famille_proche_svg.php','id_pers='.$indi->indi_id,0).'", label="'.$indi->prenom.' '.$indi->nom.'\n'.$indi->date_rapide().'"]'.";\n";
        $date=$indi->date_rapide();
        if(!empty($date))
           return 'i'.$indi->indi_id.' '.$option.' [label=<
                <TABLE BORDER="0" CELLBORDER="0" CELLSPACING="0" 
                    HREF="'.g4p_make_url('','arbre.php','id_pers='.$indi->indi_id.implode('',$tmp),0).'"
                    TARGET="_top" ALIGN="CENTER" TITLE="">
                <TR><TD ALIGN="CENTER" TITLE=""><FONT POINT-SIZE="12.0">'.$indi->prenom.' '.$indi->nom.'</FONT></TD></TR>
                <TR><TD ALIGN="CENTER" TITLE=""><FONT POINT-SIZE="9.0" FACE="LiberationSans">'.$date.'</FONT></TD></TR>
                </TABLE>
                >]'."\n";        
        else
            return 'i'.$indi->indi_id.' '.$option.' [label=<
                <TABLE BORDER="0" CELLBORDER="0" CELLSPACING="0" 
                    HREF="'.g4p_make_url('','arbre.php','id_pers='.$indi->indi_id.implode('',$tmp),0).'"
                    TARGET="_top" ALIGN="CENTER"  TITLE="">
                <TR><TD ALIGN="CENTER" TITLE=""><FONT POINT-SIZE="12.0">'.$indi->prenom.' '.$indi->nom.'</FONT></TD></TR>
                </TABLE>
                >]'."\n";                
    }
    else
        return '';
}

function g4p_print_family($famille, $option=' [style="filled", fillcolor="#ffffff"] ')
{
    global $liste_nodes;
    if(empty($liste_nodes['f'.$famille->id]))
    {
        $liste_nodes['f'.$famille->id]=true;
        $date='';
        if(!empty($famille->events))
        {
            foreach($famille->events as $a_event)
            {
                if($a_event->tag=='MARR')
                {
                    $date='mariés le : '.g4p_date($a_event->gedcom_date);
                    break;
                }
            }   
        }
        return 'f'.$famille->id.' '.$option.' [ shape=ellipse, label=< <FONT POINT-SIZE="10.0" FACE="LiberationSans">'.$date.' </FONT> >];'."\n";
    }
    else
        return '';
}

function g4p_print_link($link, $option='')
{
    static $liste_links=array();
    if(empty($liste_links[$link[0].'->'.$link[1]]))
    {
        $liste_links[$link[0].'->'.$link[1]]=true;
        return '{ '.$option.' '.$link[0].' -> '.$link[1]."};\n";
    }
    else
        return '';
}

function g4p_load_parent($g4p_indi, $generation, $descendance=false)
{
    global $dot;
    global $liste_nodes, $liste_links, $limite_ascendance, $asc_and_desc;
    static $famille_index=0;
    
    if(!empty($g4p_indi->parents))
    {
        foreach($g4p_indi->parents as $a_parent)
        {
            if($a_parent->rela_type=='birth' or $a_parent->rela_type=='')
            {
                if($generation>$limite_ascendance)
                    return false;

                if(!_fulldesc_)
                {
                    if(empty($liste_nodes['f'.$a_parent->famille_id]))
                    {
                        fwrite($dot, 'subgraph cluster_famille'.$famille_index++.'{'."\n");
                        fwrite($dot, 'style="invisible"; 
                            ranksep="1";'."\n");
                        if(!empty($a_parent->pere->indi_id))
                        {
                            $pere=g4p_load_indi_infos($a_parent->pere->indi_id);
                            fwrite($dot, g4p_print_label($pere));
                            fwrite($dot, g4p_print_link(array('i'.$pere->indi_id,'f'.$a_parent->famille_id)));
                        }
                        if(!empty($a_parent->mere->indi_id))
                        {
                            $mere=g4p_load_indi_infos($a_parent->mere->indi_id);
                            fwrite($dot, g4p_print_label($mere));
                            fwrite($dot, g4p_print_link(array('i'.$mere->indi_id,'f'.$a_parent->famille_id)));
                        }
                        if(!empty($pere))
                            $a_famille=$pere->familles[$a_parent->famille_id];
                        else
                            $a_famille=$mere->familles[$a_parent->famille_id];
                        fwrite($dot, g4p_print_family($a_famille));
                        fwrite($dot, "}\n");
                    }
                
                    if(!empty($pere))
                        if($famille_id=g4p_load_parent($pere, $generation+1, _fulldesc_))
                            fwrite($dot, g4p_print_link(array('f'.$famille_id, 'i'.$pere->indi_id)));
                    if(!empty($mere))
                        if($famille_id=g4p_load_parent($mere, $generation+1, _fulldesc_))
                            fwrite($dot, g4p_print_link(array('f'.$famille_id, 'i'.$mere->indi_id)));
                }
                else
                {
                    //descendance des parents
                    if(!empty($a_parent->pere))
                    {
                        $pere=g4p_load_indi_infos($a_parent->pere->indi_id);
                        g4p_load_enfants($pere, $generation);
                    }
                    if(!empty($a_parent->mere))
                    {
                        $mere=g4p_load_indi_infos($a_parent->mere->indi_id);
                        g4p_load_enfants($mere, $generation);
                    }
                }
                return $a_parent->famille_id;                
            }
        }
    }
    return false;
}

function g4p_load_enfants($g4p_indi, $generation)
{
    global $dot;
    global $liste_nodes, $liste_links, $limite_descendance, $limite_ascendance;
    static $famille_index=0;
    
    //fwrite($dot, 'subgraph {'.g4p_print_label($g4p_indi,' [style="filled", fillcolor="#ffffaa"] '));
    if(_origine_==$g4p_indi->indi_id)
        $tmp=g4p_print_label($g4p_indi, ' [style="filled", fillcolor="#ffffaa"] ');
    else
        $tmp=g4p_print_label($g4p_indi);
    if(empty($tmp))
        return  false;
    else 
    {
        fwrite($dot, 'subgraph cluster_familleb'.$famille_index++.'{'."\n");
        fwrite($dot, 'style="invisible"; 
            clusterrank="none";
            ranksep="1"; '."\n");
        fwrite($dot,$tmp);
        
        if($generation>-$limite_descendance and !empty($g4p_indi->familles))
        {
            foreach($g4p_indi->familles as $key=>$a_famille)
            {
                if(!empty($a_famille->husb->indi_id) and $a_famille->husb->indi_id!=$g4p_indi->indi_id)
                {
                    fwrite($dot, g4p_print_label($a_famille->husb));
                    fwrite($dot, g4p_print_family($a_famille));
                    fwrite($dot, g4p_print_link(array('i'.$g4p_indi->indi_id, 'f'.$key)));
                    fwrite($dot, g4p_print_link(array('i'.$a_famille->husb->indi_id, 'f'.$key)));                        
                    $conjoint[]=g4p_load_indi_infos($a_famille->husb->indi_id);
                }
                elseif(!empty($a_famille->wife->indi_id) and $a_famille->wife->indi_id!=$g4p_indi->indi_id)
                {
                    fwrite($dot, g4p_print_label($a_famille->wife));
                    fwrite($dot, g4p_print_family($a_famille));
                    fwrite($dot, g4p_print_link(array('i'.$g4p_indi->indi_id, 'f'.$key)));
                    fwrite($dot, g4p_print_link(array('i'.$a_famille->wife->indi_id, 'f'.$key)));                                                
                    $conjoint[]=g4p_load_indi_infos($a_famille->wife->indi_id);
                }
                //pas de conjoint mais des enfants --> famille monoparentale. Il faut créer une famille
                elseif(!empty($a_famille->enfants))
                {
                    fwrite($dot, g4p_print_family($a_famille));
                    fwrite($dot, g4p_print_link(array('i'.$g4p_indi->indi_id, 'f'.$key)));
                }
                
                if(!empty($a_famille->enfants))
                    foreach($a_famille->enfants as $a_enfant)
                        $enfants[$key][]=$a_enfant['indi']->indi_id;
            }
        }
        fwrite($dot, "}\n");
    }

    if(!empty($enfants))
    {
        foreach($enfants as $key=>$val)
        {
            foreach($val as $enfant_id)
            {
                $tmp=g4p_load_indi_infos($enfant_id);
                if($generation>=-$limite_descendance)
                    g4p_load_enfants($tmp, $generation-1);
                else
                    fwrite($dot, g4p_print_label($tmp));
                    
                fwrite($dot, g4p_print_link(array('f'.$key, 'i'.$enfant_id)));    
            }
        }
    }
    
    //on remonte l'ascendance du conjoint
    if(!empty($conjoint) and $generation<$limite_ascendance)
        foreach($conjoint as $a_conjoint)
            if($famille_id=g4p_load_parent($a_conjoint, $generation+1, _fulldesc_))
                fwrite($dot, g4p_print_link(array('f'.$famille_id, 'i'.$a_conjoint->indi_id)));
        
    //On remonte l'ascendance la personne en cours
    if($generation<$limite_ascendance)
        if($famille_id=g4p_load_parent($g4p_indi, $generation+1, _fulldesc_))
            fwrite($dot, g4p_print_link(array('f'.$famille_id, 'i'.$g4p_indi->indi_id)));   
}

g4p_load_enfants($g4p_indi,0);

//fwrite($dot, 'sql [label="Nbre requètes sql: '.$g4p_mysqli->nb_requetes."\"]\n");

fwrite($dot, "}\n");
fclose($dot);

shell_exec('dot -T'.$output.' /tmp/'.$dot_filename.' -o /tmp/'.$dot_filename.'.'.$output);
if($output=='svg')
{
    //shell_exec('sed -i \'s/font-size:\([0-9.]*\);/font-size:\1px;/g\' /tmp/'.$dot_filename.'.'.$output);
    //shell_exec('sed -i \'s/point-size=\([0-9.]*\);/point-size=\1px;/g\' /tmp/'.$dot_filename.'.'.$output);
    //shell_exec('sed -i \'s/viewBox="\([0-9 .]*\)"//g\' /tmp/'.$dot_filename.'.'.$output);
    //echo '/tmp/'.$dot_filename.'.'.$output;
 }
readfile('/tmp/'.$dot_filename.'.'.$output);

    


//print_r($liste_nodes);
?>
