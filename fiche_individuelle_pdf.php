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

g4p_forbidden_access($g4p_indi);

$sql="SELECT nom FROM genea_infos WHERE id=".$g4p_indi->base;
$g4p_infos_req=$g4p_mysqli->g4p_query($sql);
$_SESSION['genea_db_nom']=$g4p_mysqli->g4p_result($g4p_infos_req);
$_SESSION['genea_db_nom']=$_SESSION['genea_db_nom'][0]['nom'];

$file=uniqid();
//$file='test';
$latex=fopen('/tmp/'.$file.'.tex','w');
fwrite($latex,g4p_latex_write_header());
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
}

?>
