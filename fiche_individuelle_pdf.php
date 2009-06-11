<?php
 /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 **
 *Copyright (C) 2004PAROIS Pascal *
 **
 *This program is free software; you can redistribute it and/or modify*
 *it under the terms of the GNU General Public License as published by*
 *the Free Software Foundation; either version 2 of the License, or *
 *(at your option) any later version. *
 **
 *This program is distributed in the hope that it will be useful, *
 *but WITHOUT ANY WARRANTY; without even the implied warranty of*
 *MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the *
 *GNU General Public License for more details.*
 **
 *You should have received a copy of the GNU General Public License *
 *along with this program; if not, write to the Free Software *
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA02111-1307 USA *
 **
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *Page à tout faire*
 * *
 * dernière mise à jour : novembre 2004*
 * En cas de problème : http://www.parois.net*
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_latex_functions.php');

//Chargement des données de la personne
$g4p_indi=g4p_load_indi_infos((int)$_REQUEST['id_pers']);
//var_dump($g4p_indi);

$sql="SELECT nom FROM genea_infos WHERE id=".$g4p_indi->base;
$g4p_infos_req=$g4p_mysqli->g4p_query($sql);
$_SESSION['genea_db_nom']=$g4p_mysqli->g4p_result($g4p_infos_req);
$_SESSION['genea_db_nom']=$_SESSION['genea_db_nom'][0]['nom'];

$file=uniqid();
//$file='test';
$latex=fopen('/tmp/'.$file.'.tex','w');
fwrite($latex,'\documentclass[a4paper,12pt]{article}
%\usepackage[onlymath,mathlf]{MinionPro}
%\usepackage{mathrsfs}
\usepackage{fontspec}
%\setmainfont{Minion Pro}
%\setsansfont[BoldFont={%
%  Myriad Pro Semibold}, BoldItalicFont={%
%  Myriad Pro Semibold Italic}]{Myriad Pro}
%\setmonofont{Luxi Mono}
\usepackage{xltxtra} % charge aussi fontspec et xunicode, nécessaires... 
\usepackage{hyperref}
\usepackage{framed}
\usepackage{color}
%\definecolor{shadecolor}{rgb}{0.94,0.94,0.99} 
\usepackage{titlesec}
\usepackage{underscore}
\usepackage{graphicx}
\usepackage{boites}
\usepackage{pstricks}
\usepackage{geometry}
\geometry{hmargin=2.5cm, vmargin=3cm}

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
    colorlinks        = true,%     % Liens en couleur
    pdfborder         = {0 0 0}%   % Style de bordure : ici, pas de bordure
} 
\usepackage{soul}
\usepackage[francais]{babel}

\makeatletter
\newcommand{\\affichedate}[1]{#1}

\makeatletter
\newrgbcolor{LightBlue}{0.94 0.94 1}
\newrgbcolor{LightGreen}{0.94 1 0.94}
%% Seconde modification
\def\boite#1{%
  %\fboxrule=0.4pt
  \def\bkvz@before@breakbox{\ifhmode\par\fi\vskip\breakboxskip\relax}%
  \def\bkvz@set@linewidth{\advance\linewidth -2\fboxrule 
    \advance\linewidth -2\fboxsep
    \advance\linewidth -1cm}%
  \def\bkvz@left{\hspace{0.5cm}}%
\def\bk@line{\hbox to \linewidth{%
      \ifbkcount\smash{\llap{\the\bk@lcnt\ }}\fi
      \hspace{0.5cm}\psframebox*[framesep=0pt,fillcolor=#1,linewidth=0]{%
        \hskip\fboxsep
        \box\bk@bxa
        \hskip\fboxsep 
        }%
      }}%
  \def\bkvz@right{}%
  \def\bkvz@top{}%
  \def\bkvz@bottom{}%
  \breakbox
}
\def\endboite{\endbreakbox}

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
');

g4p_latex_write_indi($g4p_indi);

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
}

?>
