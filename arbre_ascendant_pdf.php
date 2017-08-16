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
 *            Tracage de l'arbre ascendant                                 *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'include_sys/sys_latex_functions.php');

function g4p_write_parents($mon_indi,$generation=0)
{
    global $liste_indi;
    if(!empty($mon_indi->parents))
    {
        foreach($mon_indi->parents as $parents)
        {
            $pere_arbre=$mere_arbre='';
            if(empty($parents->rela_type) or $parents->rela_type=='birth')
            {
                //pere
                if(!empty($parents->pere))
                {
                    $pere=g4p_load_indi_infos($parents->pere->indi_id);
                    $liste_indi[$pere->indi_id]=$pere;
                    $pere->prenom=explode(' ',$pere->prenom);
                    $pere->prenom=$pere->prenom[0];
                    //conserve uniquement le premier prénom
                    $g4p_nom_aff=$pere->prenom.' '.$pere->nom;
                    $date=$pere->date_rapide('array');
                    if(!empty($date['naissance']))
                        $date['naissance']=' \\\\ '.$date['naissance'];
                    if(!empty($date['deces']))
                        $date['deces']=' \\\\ '.$date['deces'];
                        
                    $pere_arbre="\n".'child {';
                    if($generation!=4)
                        $pere_arbre.="\n".'node[leaf, fill=blue!5] {\limitbox{3.9cm}{2cm}{'.g4p_latex_link('I'.$pere->indi_id,$g4p_nom_aff).'}'.$date['naissance'].$date['deces'].'}';
                    else
                        $pere_arbre.="\n".'node[leaf, fill=blue!5] {\limitbox{3.9cm}{2cm}{'.g4p_latex_link('I'.$pere->indi_id,$g4p_nom_aff).'}}';
                    if($generation<4)
                        $pere_arbre.=g4p_write_parents($pere,$generation+1);
                    $pere_arbre.="\nedge from parent\n}";
                }
                //pere
                if(!empty($parents->mere))
                {
                    $mere=g4p_load_indi_infos($parents->mere->indi_id);
                    $liste_indi[$mere->indi_id]=$mere;
                    $mere->prenom=explode(' ',$mere->prenom);
                    $mere->prenom=$mere->prenom[0];
                    //conserve uniquement le premier prénom
                    $g4p_nom_aff=$mere->prenom.' '.$mere->nom;
                    $date=$mere->date_rapide('array');
                    if(!empty($date['naissance']))
                        $date['naissance']=' \\\\ '.$date['naissance'];
                    if(!empty($date['deces']))
                        $date['deces']=' \\\\ '.$date['deces'];
                    
                    $mere_arbre="\n".'child {';
                    if($generation!=4)
                        $mere_arbre.="\n".'node[leaf, fill=red!5] {\limitbox{3.9cm}{2cm}{'.g4p_latex_link('I'.$mere->indi_id,$g4p_nom_aff).'}'.$date['naissance'].$date['deces'].'}';
                    else
                        $mere_arbre.="\n".'node[leaf, fill=red!5] {\limitbox{3.9cm}{2cm}{'.g4p_latex_link('I'.$mere->indi_id,$g4p_nom_aff).'}}';
                    if($generation<4)
                        $mere_arbre.=g4p_write_parents($mere,$generation+1);
                    $mere_arbre.="\nedge from parent\n}";
                }
            }
            return $pere_arbre.$mere_arbre;
        }
    }
}

function g4p_ascendance($mon_indi)
{
    global $latex, $liste_indi;
    $liste_indi[$mon_indi->indi_id]=$mon_indi;
    
    fwrite($latex, '\begin{center}
    \resizebox{\textwidth}{!}{\begin{tikzpicture}[text width=4cm, grow=right]
    \tikzset{my node/.code=\ifpgfmatrix\else\tikzset{matrix of nodes}\fi}');

    $mon_indi->prenom=explode(' ',$mon_indi->prenom);
    $mon_indi->prenom=$mon_indi->prenom[0];
    //conserve uniquement le premier prénom
    $g4p_nom_aff=$mon_indi->prenom.' '.$mon_indi->nom;
    $date=$mon_indi->date_rapide('array');
    
    if($mon_indi->sexe=='M')
        fwrite($latex,'\node[leaf, fill=blue!5] {\limitbox{3.9cm}{2cm}{'.g4p_latex_link('I'.$mon_indi->indi_id,$g4p_nom_aff).'} \\\\ '.$date['naissance'].' \\\\ '.$date['deces'].'}');
    else
        fwrite($latex,'\node[leaf, fill=red!5] {\limitbox{3.9cm}{2cm}{'.g4p_latex_link('I'.$mon_indi->indi_id,$g4p_nom_aff).'} \\\\ '.$date['naissance'].' \\\\ '.$date['deces'].'}');
        

    $ascd=g4p_write_parents($mon_indi);
    fwrite($latex,$ascd);
    //fwrite($latex,"\nedge from parent");
    fwrite($latex, ";\n\end{tikzpicture}}\n\end{center}\n");
}

if(isset($_GET['id_pers']))
    $g4p_indi=g4p_load_indi_infos($_GET['id_pers']);
else
    die('Ausun id défini');

g4p_forbidden_access($g4p_indi);

$file=uniqid();
//$file='test';
$latex=fopen('/tmp/'.$file.'.tex','w');
fwrite($latex,g4p_latex_write_header());

fwrite($latex,'\section*{Ascendance '.$g4p_indi->prenom.' '.$g4p_indi->nom.'}
    % Set the overall layout of the tree
    \tikzstyle{level 1}=[level distance=0.5cm, sibling distance=12cm, anchor=center]
    \tikzstyle{level 2}=[level distance=1cm, sibling distance=6cm]
    \tikzstyle{level 3}=[level distance=4.7cm, sibling distance=3cm, anchor=center,child anchor=west, edge from parent fork right]
    \tikzstyle{level 4}=[level distance=4.7cm, sibling distance=1.5cm]
    \tikzstyle{level 5}=[level distance=4.4cm, sibling distance=0.6cm]
    % Define styles for leafs
    \tikzstyle{leaf} = [fill=white, text centered, inner sep=1mm, outer sep=0mm, draw]
    ');

$liste_indi=array();
g4p_ascendance($g4p_indi);

// on affiche les fiches
fwrite($latex,'\newpage'."\n");

$sql="SELECT nom FROM genea_infos WHERE id=".$g4p_indi->base;
$g4p_infos_req=$g4p_mysqli->g4p_query($sql);
$g4p_base_select=$g4p_mysqli->g4p_result($g4p_infos_req);
$genea_db_nom=$g4p_base_select[0]['nom'];

foreach($liste_indi as $indi)
{
    g4p_latex_write_indi($indi);
}

fwrite($latex,'\end{document}');
fclose($latex);

//header('Content-Type: text/plain');
shell_exec('sed -i \'s/\$//\' /tmp/'.$file.'.tex');
shell_exec('sed -i \'s/\&//\' /tmp/'.$file.'.tex');
//shell_exec('sed -i \'s/\#//\' /tmp/'.$file.'.tex');
//readfile('/tmp/'.$file.'.tex');

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$output=shell_exec('export PATH=/usr/local/texlive/2017/bin/x86_64-linux/:/bin/:/usr/bin/ && nice -n 10 xelatex -interaction=nonstopmode -output-directory=/tmp/ /tmp/'.$file.'.tex');
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
