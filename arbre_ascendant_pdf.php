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
                        $pere_arbre.="\n".'node[bag, fill=blue!5] {\limitbox{3.9cm}{2cm}{'.g4p_link('I'.$pere->indi_id,$g4p_nom_aff).'}'.$date['naissance'].$date['deces'].'}';
                    else
                        $pere_arbre.="\n".'node[bag, fill=blue!5] {\limitbox{3.9cm}{2cm}{'.g4p_link('I'.$pere->indi_id,$g4p_nom_aff).'}}';
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
                        $mere_arbre.="\n".'node[bag, fill=red!5] {\limitbox{3.9cm}{2cm}{'.g4p_link('I'.$mere->indi_id,$g4p_nom_aff).'}'.$date['naissance'].$date['deces'].'}';
                    else
                        $mere_arbre.="\n".'node[bag, fill=red!5] {\limitbox{3.9cm}{2cm}{'.g4p_link('I'.$mere->indi_id,$g4p_nom_aff).'}}';
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
    
    fwrite($latex, "\begin{center}\n\\resizebox{\\textwidth}{!}{\begin{tikzpicture}[text width=4cm, grow=right]\n\\tikzset{my node/.code=\ifpgfmatrix\else\\tikzset{matrix of nodes}\\fi}\n");

    $mon_indi->prenom=explode(' ',$mon_indi->prenom);
    $mon_indi->prenom=$mon_indi->prenom[0];
    //conserve uniquement le premier prénom
    $g4p_nom_aff=$mon_indi->prenom.' '.$mon_indi->nom;
    $date=$mon_indi->date_rapide('array');
    
    if($mon_indi->sexe=='M')
        fwrite($latex,'\node[bag, fill=blue!5] {'.$g4p_nom_aff.' \\\\ '.$date['naissance'].' \\\\ '.$date['deces'].'}');
    else
        fwrite($latex,'\node[bag, fill=red!5] {'.$g4p_nom_aff.' \\\\ '.$date['naissance'].' \\\\ '.$date['deces'].'}');
        

    $ascd=g4p_write_parents($mon_indi);
    fwrite($latex,$ascd);
    //fwrite($latex,"\nedge from parent");
    fwrite($latex, ";\n\end{tikzpicture}}\n\end{center}\n");
}

if(isset($_GET['id_pers']))
    $g4p_indi=g4p_load_indi_infos($_GET['id_pers']);
else
    die('Ausun id défini');


if($_SESSION['permission']->permission[_PERM_MASK_INDI_] and $g4p_indi->resn=='privacy')
{
  echo $g4p_langue['acces_non_autorise'];
  require_once($g4p_chemin.'copyright.php');
  require_once($g4p_chemin.'pied_de_page.php');
  exit;
}

$file=uniqid();
//$file='test';
$latex=fopen('/tmp/'.$file.'.tex','w');
fwrite($latex,'\documentclass[a4paper,10pt]{article}
\usepackage{fontspec}
\usepackage{xltxtra} % charge aussi fontspec et xunicode, nécessaires... 
\usepackage{hyperref}
\usepackage{framed}
\usepackage{color}
\usepackage{titlesec}
\usepackage{underscore}
\usepackage{graphicx}
\usepackage{geometry}
\geometry{hmargin=2.5cm, top=2cm, bottom=2cm}
\usepackage{tikz}
\usetikzlibrary{trees}
\hypersetup{ % Modifiez la valeur des champs suivants
    pdfauthor   = {Pascal Parois},%
    pdftitle    = {},%
    pdfsubject  = {},%
    pdfkeywords = {},%
    pdfcreator  = {XeLaTeX},%
    pdfproducer = {XeLaTeX},
    bookmarks         = false,%     % Signets
    bookmarksnumbered = false,%     % Signets numerote
    pdfpagemode       = UseOutlines,%     % Signets/vignettes ferme a l\'ouverture
    bookmarksopen	= false,
    pdfstartview      = FitH,%     % La page prend toute la largeur
    pdfpagelayout     = SinglePage,% Vue par page
    colorlinks        = false,%     % Liens en couleur
    pdfborder         = {0 0 0}%   % Style de bordure : ici, pas de bordure
} 
\usepackage[francais]{babel}


\makeatletter
\newcommand{\\affichedate}[1]{#1}

\newcommand*{\limitbox}[3]{%
  \begingroup
    \setlength{\@tempdima}{#1}%
    \setlength{\@tempdimb}{#2}%
    \resizebox{%
      \ifdim\width>\@tempdima\@tempdima\else\width\fi
    }{!}{%
      \resizebox{!}{%
        \ifdim\height>\@tempdimb\@tempdimb\else\height\fi
      }{#3}%
    }%
  \endgroup
}
\definecolor{LightBlue}{rgb}{0.94,0.94,1}
\definecolor{LightGreen}{rgb}{0.9,1,0.9}

\newenvironment{boite}[1][LightGreen]{%
  \def\FrameCommand{\fboxsep=\FrameSep \colorbox{#1}}%
  \MakeFramed {\hsize0.9\textwidth\FrameRestore}}%
{\endMakeFramed}

\makeatother

\titleformat{\section}
{\vspace{3cm}\titlerule[2pt]
\vspace{.8ex}%
\Huge\bfseries\filleft}
{\thesection.}{1em}{}

\titleformat{\subsection}
{\vspace{0.5cm}%
\LARGE\itshape}
{\thesection.}{1em}{}

\titleformat{\subsubsection}
{%
\Large\itshape}
{\thesection.}{0.5em}{}

\setlength{\parindent}{0pt}

\begin{document}
\section*{Ascendance '.$g4p_indi->prenom.' '.$g4p_indi->nom.'}
% Set the overall layout of the tree
\tikzstyle{level 1}=[level distance=0.5cm, sibling distance=12cm, anchor=center]
\tikzstyle{level 2}=[level distance=1cm, sibling distance=6cm]
\tikzstyle{level 3}=[level distance=4.7cm, sibling distance=3cm, anchor=center,child anchor=west, edge from parent fork right]
\tikzstyle{level 4}=[level distance=4.7cm, sibling distance=1.5cm]
\tikzstyle{level 5}=[level distance=4.4cm, sibling distance=0.6cm]

% Define styles for bags and leafs
\tikzstyle{bag} = [fill=white, text centered, inner sep=1mm, outer sep=0mm, draw]
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
    g4p_write_indi($indi);
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

$output=shell_exec('xelatex -interaction=nonstopmode -output-directory=/tmp /tmp/'.$file.'.tex');
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