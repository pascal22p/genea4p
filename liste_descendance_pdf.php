<?php
/*limites imposées pour éviter d'exploser le serveur, cf p_conf/g4p_config:
_NBRE_MAX_PERS_DESCENDANCE_ générations au maximum sont affichées ($generation)
_NBRE_MAX_PERS_DESCENDANCE_ personnes sont affichées au maximum
*/

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'include_sys/sys_latex_functions.php');

//Chargement des données de la personne
$g4p_indi=g4p_load_indi_infos((int)$_REQUEST['id_pers']);

//$g4p_titre_page='Liste de descendance : '.$g4p_indi->nom.' '.$g4p_indi->prenom;

function recursive_descendance($g4p_id, $generation=1, $indent='')
{
    global $g4p_couleur, $g4p_chemin, $g4p_limite_pers, $cache_count, $g4p_langue, $latex;
    $g4p_limite_pers++;
    $g4p_indi_info=g4p_load_indi_infos($g4p_id);
    //$indent=$indent.'\makebox[0]{$\cdot$}';
    $prefix='\prefix{'.$generation.'}';
    if($_SESSION['permission']->permission[_PERM_MASK_INDI_] and $g4p_indi_info->resn=='privacy')
        return;

    if(isset($g4p_indi_info->familles))
    {
        foreach($g4p_indi_info->familles as $g4p_une_famille)
        {
            if(isset($g4p_une_famille->enfants))
            {
                if($generation<$_GET['g4p_generation'] and $generation<_NBRE_MAX_GENERATION_)
                {
                    //echo '-',substr($indent,-46),'-<br>';
                        fwrite($latex,'\item '.$indent.$prefix.g4p_latex_link_nom($g4p_indi_info->indi_id));

                    if(isset($g4p_une_famille->husb->indi_id)  and $g4p_une_famille->husb->indi_id!=$g4p_indi_info->indi_id)
                        fwrite($latex,' x '.g4p_latex_link_nom($g4p_une_famille->husb));
                    elseif(isset($g4p_une_famille->wife->indi_id)  and $g4p_une_famille->wife->indi_id!=$g4p_indi_info->indi_id)
                        fwrite($latex,' x '.g4p_latex_link_nom($g4p_une_famille->wife));
                    fwrite($latex,"\n");
                    $cpt=count($g4p_une_famille->enfants);
                    $i=0;
                    foreach($g4p_une_famille->enfants as $a_enfant)
                    {
                        $i++;
                        if($i<$cpt)
                            recursive_descendance($a_enfant['indi']->indi_id, $generation+1, $indent.'\spacera');
                        else
                            recursive_descendance($a_enfant['indi']->indi_id, $generation+1, $indent.'\spacerb');
                    }                    
                }
                else
                {
                    fwrite($latex,'\item '.$indent.$prefix.g4p_latex_link_nom($g4p_indi_info->indi_id));
                    if(isset($g4p_une_famille->husb->indi_id)  and $g4p_une_famille->husb->indi_id!=$g4p_indi_info->indi_id)
                        fwrite($latex,' x '.g4p_latex_link_nom($g4p_une_famille->husb));
                    elseif(isset($g4p_une_famille->wife->indi_id)  and $g4p_une_famille->wife->indi_id!=$g4p_indi_info->indi_id)
                        fwrite($latex,' x '.g4p_latex_link_nom($g4p_une_famille->wife));
                    fwrite($latex,"\n");
                }
            }
            else
            {
                fwrite($latex,'\item '.$indent.$prefix.g4p_latex_link_nom($g4p_indi_info->indi_id));
                if(isset($g4p_une_famille->husb->indi_id)  and $g4p_une_famille->husb->indi_id!=$g4p_indi_info->indi_id)
                    fwrite($latex,' x '.g4p_latex_link_nom($g4p_une_famille->husb));
                elseif(isset($g4p_une_famille->wife->indi_id)  and $g4p_une_famille->wife->indi_id!=$g4p_indi_info->indi_id)
                    fwrite($latex,' x '.g4p_latex_link_nom($g4p_une_famille->wife));
                fwrite($latex,"\n");
            }
            //echo '</ul>';
        }
    }
    else
    {
        fwrite($latex,'\item '.$indent.$prefix.g4p_latex_link_nom($g4p_indi_info->indi_id));
        fwrite($latex,"\n");
    }
}

if(!isset($_GET['g4p_generation']))
    $_GET['g4p_generation']=5;
$lien='';

$file=uniqid();
//$file='test';
$latex=fopen('/tmp/'.$file.'.tex','w');
fwrite($latex,g4p_latex_write_header());

fwrite($latex, '\newcommand{\prefix}[1]{\raisebox{0.2\baselineskip}[\baselineskip]{\rule[0]{0.25mm}{1.25\baselineskip}\rule[0]{3mm}{0.25mm}}\hspace{-3mm}\makebox[3mm]{\raisebox{0.4\baselineskip}{\tiny{#1}}}}'."\n");
fwrite($latex, '\newcommand{\spacera}{\hspace*{3mm}\raisebox{0.2\baselineskip}[\baselineskip]{\rule[0]{0.25mm}{1.25\baselineskip}}\hspace{-0.25mm}}'."\n");
fwrite($latex, '\newcommand{\spacerb}{\hspace*{3mm}}'."\n");

fwrite($latex,'\section*{Descendance '.$g4p_indi->prenom.' '.$g4p_indi->nom.'}'."\n");
fwrite($latex,'\renewcommand{\labelitemi}{}'."\n");
fwrite($latex,'\begin{itemize}');

recursive_descendance($g4p_indi->indi_id);

fwrite($latex,'\end{itemize}');
fwrite($latex,'\end{document}');
fclose($latex);

//header('Content-Type: text/plain');
shell_exec('sed -i \'s/\$//\' /tmp/'.$file.'.tex');
shell_exec('sed -i \'s/\&//\' /tmp/'.$file.'.tex');
//shell_exec('sed -i \'s/\#//\' /tmp/'.$file.'.tex');
//readfile('/tmp/'.$file.'.tex');

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$output=shell_exec('nice -n 10 xelatex -interaction=nonstopmode -output-directory=/tmp /tmp/'.$file.'.tex');
if(file_exists('/tmp/'.$file.'.pdf'))
{
    header('Content-type: application/pdf');
    readfile('/tmp/'.$file.'.pdf');
}
else
{
    header('Content-Type: text/plain');
    echo $output;
    echo "\n\n\n";
    readfile('/tmp/'.$file.'.tex');
}

?>
