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
$output_list=array('svg'=>'svg', 'png'=>'png', 'pdf'=>'pdf', 'java'=>'svg', 'dot'=>'dot');
$output_headers=array('svg'=>'Content-Type: image/svg+xml', 'png'=>'Content-Type: image/png', 
    'pdf'=>"Content-type: application/pdf", 'dot'=>'Content-Type: text/plain', 
    'xdot'=>'Content-Type: text/plain');

//definition format de sortie
if(!empty($_GET['output']) and !empty($output_list[$_GET['output']]))
    $output=$output_list[$_GET['output']];
else
    $output='svg';
    
if($output=='dot')
    $dotmultiplier=2;
else
    $dotmultiplier=1;


if(isset($_GET['limite_ascendance']) and $_GET['limite_ascendance']<$dotmultiplier*25)
    $limite_ascendance=$_GET['limite_ascendance'];
elseif(isset($_GET['limite_ascendance']) and $_GET['limite_ascendance']<0)
    $limite_ascendance=0;
else
    $limite_ascendance=2;
    
if(isset($_GET['limite_descendance']) and $_GET['limite_descendance']<$dotmultiplier*25)
    $limite_descendance=$_GET['limite_descendance'];
elseif(isset($_GET['limite_descendance']) and $_GET['limite_descendance']<$dotmultiplier*0)
    $limite_descendance=0;
else
    $limite_descendance=1;

if(!empty($_GET['fulldesc']))
    define('_fulldesc_',true);
else
    define('_fulldesc_',false);

define('_origine_',$_GET['id_pers']);

if(!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
{
    if($output!='dot')
        define('_MAX_NODES_',800);
    else
        define('_MAX_NODES_',4000);
}
else
{
    if($output!='dot')
        define('_MAX_NODES_',800);
    else
        define('_MAX_NODES_',4000);
}
    
$dot_filename=uniqid();
$dot=fopen('/tmp/'.$dot_filename, 'w');
fwrite($dot, 'digraph arbre {
    bgcolor="transparent";
    ranksep="0.75";
    fontname="LiberationSans";
    overlap="compress";
    size = "150, 50";
    mclimit = "100";
    remincross = "true";
    edge [dir=none];
    node [shape = record, margin="0.45,0.05", width=0.1, height=0.2, tooltip=" "];
    '."\n");
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
        $date=$indi->date_rapide('short');
        if(!empty($date))
/*           return 'i'.$indi->indi_id.' '.$option.' [label=<
                <TABLE BORDER="0" CELLBORDER="0" CELLSPACING="0" 
                    HREF="'.g4p_make_url('','arbre.php','id_pers='.$indi->indi_id.implode('',$tmp),0).'"
                    TARGET="_top" ALIGN="CENTER" TITLE="">
                <TR><TD ALIGN="CENTER" TITLE=""><FONT POINT-SIZE="15.0">'.$indi->prenom.' '.$indi->nom.'</FONT></TD></TR>
                <TR><TD ALIGN="CENTER" TITLE=""><FONT POINT-SIZE="11.0" FACE="LiberationSans">'.$date.'</FONT></TD></TR>
                </TABLE>
                >]'."\n";        */
                return 'i'.$indi->indi_id.' '.$option.' [href="'.g4p_make_url('','arbre_svg.php','id_pers='.$indi->indi_id.implode('',$tmp),0).'", label="'.$indi->prenom.' '.$indi->nom.'\\n'.$date.'"]'."\n";
        else
            /* return 'i'.$indi->indi_id.' '.$option.' [label=<
                <TABLE BORDER="0" CELLBORDER="0" CELLSPACING="0" 
                    HREF="'.g4p_make_url('','arbre.php','id_pers='.$indi->indi_id.implode('',$tmp),0).'"
                    TARGET="_top" ALIGN="CENTER"  TITLE="">
                <TR><TD ALIGN="CENTER" TITLE=""><FONT POINT-SIZE="15.0">'.$indi->prenom.' '.$indi->nom.'</FONT></TD></TR>
                </TABLE>
                >]'."\n";                */
               return 'i'.$indi->indi_id.' '.$option.' [href="'.g4p_make_url('','arbre_svg.php','id_pers='.$indi->indi_id.implode('',$tmp),0).'", label="'.$indi->prenom.' '.$indi->nom.'"]'."\n";
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
        
        if(!empty($famille->enfants))
        {
            if(!empty($date))
            {
                $tmp='f'.$famille->id.' '.$option.' [ shape=ellipse, label=< <FONT POINT-SIZE="13.0" FACE="LiberationSans">'.$date.' </FONT> >];'."\n";
                $tmp.="	{\n node [label=\"\" width=0 height=0 shape=point]
                fa".$famille->id." \n}\n";
                $tmp.="f".$famille->id." -> fa".$famille->id." [dir=none]\n";
            }
            else
            {
                $tmp='f'.$famille->id.' '.$option.' [ shape=point, label="" width=0 height=0];'."\n";
                $tmp.="	{\n node [label=\"\" width=0 height=0 shape=point]
                fa".$famille->id." \n}\n";
                $tmp.="f".$famille->id." -> fa".$famille->id." [dir=none]\n";            
            }
        }
        elseif(!empty($date))
        {
            $tmp='f'.$famille->id.' '.$option.' [ shape=ellipse, label=< <FONT POINT-SIZE="13.0" FACE="LiberationSans">'.$date.' </FONT> >];'."\n";        
        }
        else
        {
            $tmp='f'.$famille->id.' '.$option.' [ shape=point, label="" width=0 height=0];'."\n";        
        }
        return $tmp;
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
                            if($_SESSION['permission']->permission[_PERM_MASK_INDI_] or !($pere->resn=='privacy' or $pere->resn=='confidential'))
                            {
                                fwrite($dot, g4p_print_label($pere));
                                fwrite($dot, g4p_print_link(array('i'.$pere->indi_id,'f'.$a_parent->famille_id)));
                            }
                        }
                        if(!empty($a_parent->mere->indi_id))
                        {
                            $mere=g4p_load_indi_infos($a_parent->mere->indi_id);
                            if($_SESSION['permission']->permission[_PERM_MASK_INDI_] or !($mere->resn=='privacy' or $mere->resn=='confidential'))
                            {
                                fwrite($dot, g4p_print_label($mere));
                                fwrite($dot, g4p_print_link(array('i'.$mere->indi_id,'f'.$a_parent->famille_id)));
                            }
                        }
                        $a_famille=null;
                        if(!empty($pere) and isset($pere->familles[$a_parent->famille_id]))
                            $a_famille=$pere->familles[$a_parent->famille_id];
                        elseif(!empty($mere) and isset($mere->familles[$a_parent->famille_id]))
                            $a_famille=$mere->familles[$a_parent->famille_id];
                        if(!empty($a_famille))
                            fwrite($dot, g4p_print_family($a_famille));
                        fwrite($dot, "}\n");
                    }
                
                    //echo '---',count($liste_nodes),'-';
                    if(!empty($pere) and count($liste_nodes)<_MAX_NODES_)
                        if($famille_id=g4p_load_parent($pere, $generation+1, _fulldesc_))
                            fwrite($dot, g4p_print_link(array('fa'.$famille_id, 'i'.$pere->indi_id)));
                    if(!empty($mere) and count($liste_nodes)<_MAX_NODES_)
                        if($famille_id=g4p_load_parent($mere, $generation+1, _fulldesc_))
                            fwrite($dot, g4p_print_link(array('fa'.$famille_id, 'i'.$mere->indi_id)));
                }
                else
                {
                    ///echo '---',count($liste_nodes),'-';
                    //descendance des parents
                    if(!empty($a_parent->pere))
                    {
                        $pere=g4p_load_indi_infos($a_parent->pere->indi_id);
                        if(($_SESSION['permission']->permission[_PERM_MASK_INDI_] 
                            or !($pere->resn=='privacy' or $pere->resn=='confidential')) 
                            and count($liste_nodes)<_MAX_NODES_)
                        {                        
                            g4p_load_enfants($pere, $generation);
                        }
                    }
                    if(!empty($a_parent->mere))
                    {
                        $mere=g4p_load_indi_infos($a_parent->mere->indi_id);
                        if(($_SESSION['permission']->permission[_PERM_MASK_INDI_] 
                            or !($mere->resn=='privacy' or $mere->resn=='confidential'))
                            and count($liste_nodes)<_MAX_NODES_)
                        {
                            g4p_load_enfants($mere, $generation);
                        }
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
            mclimit = "100";
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
                //echo '---',count($liste_nodes),'-';
                if($generation>=-$limite_descendance and count($liste_nodes)<_MAX_NODES_)
                    g4p_load_enfants($tmp, $generation-1);
                else
                    fwrite($dot, g4p_print_label($tmp));
                    
                fwrite($dot, g4p_print_link(array('fa'.$key, 'i'.$enfant_id)));    
            }
        }
    }
    
    //on remonte l'ascendance du conjoint
    if(!empty($conjoint) and $generation<$limite_ascendance and count($liste_nodes)<_MAX_NODES_)
        foreach($conjoint as $a_conjoint)
            if($famille_id=g4p_load_parent($a_conjoint, $generation+1, _fulldesc_))
                fwrite($dot, g4p_print_link(array('fa'.$famille_id, 'i'.$a_conjoint->indi_id)));
        
    //On remonte l'ascendance la personne en cours
    if($generation<$limite_ascendance and count($liste_nodes)<_MAX_NODES_)
        if($famille_id=g4p_load_parent($g4p_indi, $generation+1, _fulldesc_))
            fwrite($dot, g4p_print_link(array('fa'.$famille_id, 'i'.$g4p_indi->indi_id)));   
}

g4p_load_enfants($g4p_indi,0);
//fwrite($dot, 'sql [label="Nbre requètes sql: '.$g4p_mysqli->nb_requetes."\"]\n");
if(count($liste_nodes)>_MAX_NODES_)
{
    fwrite($dot,'maxnode [style="filled", fillcolor="#ffaaff"] [label=<<TABLE BORDER="0" 
        CELLBORDER="0" CELLSPACING="0" 
        ALIGN="CENTER" TITLE="">
        <TR><TD ALIGN="CENTER" TITLE=""><FONT POINT-SIZE="15.0">Arbre incomplet</FONT></TD></TR>
        <TR><TD ALIGN="CENTER" TITLE=""><FONT POINT-SIZE="15.0">Nombre de noeuds maxi : '._MAX_NODES_.'</FONT></TD></TR></TABLE>>]');
}

fwrite($dot, "}\n");
fclose($dot);
if($output!='dot')
{
    header($output_headers[$output]);
    shell_exec('nice -n 10 dot -T'.$output.' /tmp/'.$dot_filename.' -o /tmp/'.$dot_filename.'.'.$output);
}
else
{
    header('Content-Type: text/plain; charset=utf-8');
    shell_exec('cp /tmp/'.$dot_filename.' /tmp/'.$dot_filename.'.'.$output);
}
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
