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
 *                    Export de la base en latex                           *
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

$time_start = g4p_getmicrotime();

//on verifie que c'est bien la base de l'admin, si ce n'est pas un super-admin
/*
$sql="SELECT id FROM genea_permissions WHERE id_membre=".$_SESSION['g4p_id_membre']." AND type=".$g4p_module_export['export_pdf_db.php']['permission']." AND permission=1 and (id_base='".$_POST['base_rapport']."' OR id_base='*')";
if($g4p_infos_req=g4p_db_query($sql))
{
  $g4p_result=g4p_db_result($g4p_infos_req);
  if(!$g4p_result)
  {
    echo $g4p_langue['acces_admin'];
    exit;
  }
}
else
{
  echo $g4p_langue['acces_admin'];
  exit;
}
*/

$sql="SELECT nom FROM genea_infos WHERE id=".$_REQUEST['base'];
$g4p_infos_req=$g4p_mysqli->g4p_query($sql);
$g4p_base_select=$g4p_mysqli->g4p_result($g4p_infos_req);
$genea_db_nom=$g4p_base_select[0]['nom'];

// Les requètes SQL
$sql="SELECT indi_id
         FROM genea_individuals WHERE base=".(int)$_REQUEST['base']." AND indi_resn IS NULL ORDER BY indi_nom, indi_prenom";
$g4p_infos_req=$g4p_mysqli->g4p_query($sql);
$g4p_liste_indis=$g4p_mysqli->g4p_result($g4p_infos_req);

// le serveur va souffrir... Si le cache est vide, des miliers de requètes vont être exécutés.

//$file=uniqid();
$file='test';
$latex=fopen('/tmp/'.$file.'.tex','w');
fwrite($latex,'\documentclass[a4paper,12pt]{article}
\usepackage[onlymath,mathlf]{MinionPro}
\usepackage{mathrsfs}
\usepackage[no-math]{fontspec}
\defaultfontfeatures{Scale=MatchLowercase, Mapping=tex-text}
\setmainfont{Minion Pro}
\setsansfont[BoldFont={%
  Myriad Pro Semibold}, BoldItalicFont={%
  Myriad Pro Semibold Italic}]{Myriad Pro}
\setmonofont{Luxi Mono}
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

foreach($g4p_liste_indis as $indi_id)
{
    $indi_id=$indi_id['indi_id'];
    $g4p_indi=g4p_load_indi_infos($indi_id);
    g4p_write_indi($g4p_indi);
}

fwrite($latex,'\end{document}');
fclose($latex);

header('Content-Type: text/plain');
shell_exec('sed -i \'s/\$//\' /tmp/'.$file.'.tex');
shell_exec('sed -i \'s/\&//\' /tmp/'.$file.'.tex');
//shell_exec('sed -i \'s/\#//\' /tmp/'.$file.'.tex');
readfile('/tmp/'.$file.'.tex');

//header('Content-Type: text/plain');
//readfile($file.'.pdf');
//shell_exec('xelatex -interaction=nonstopmode -output-directory=/tmp '.$file.'.tex');

?>



