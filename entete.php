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
 *                 FIchier d'entête                                        *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

//<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
//header("Content-type: text/html; charset=".$g4p_langue['entete_charset']);
//header("Content-type: text/html; charset=ISO-8859-1");
//header('Content-type:application/xhtml+xml charset="'.$g4p_langue['entete_charset']);

ob_start();

//Le navigateur accepte application/xhtml+xml ? (permet de filtrer IE) + prise en compte du fichier de config
if(!empty($g4p_config['allow_xml_header']))
    $accept_xml = 
        !empty($_SERVER['HTTP_ACCEPT']) &&
        strpos($_SERVER['HTTP_ACCEPT'],'application/xhtml+xml') !== false;
else
    $accept_xml = false;

//Si on veut du vrai XML, on envoie un en-tête correct
if ($accept_xml)
    header('Content-type:application/xhtml+xml; charset='.$g4p_langue['entete_charset']);
else
    header('Content-Type: text/html; charset='.$g4p_langue['entete_charset']);

if(isset($g4p_titre_page))
  $g4p_langue['entete_titre']=$g4p_titre_page;

//Si le navigateur supporte application/xml+xhtml, on ajoute le prologue
if ($accept_xml)
    echo '<?xml version="1.0" encoding="',$g4p_langue['entete_charset'],'"?>';
if(!isset($g4p_html_direction))
  	$g4p_html_direction='';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$g4p_langue['entete_content_language']?>" <?=$g4p_html_direction?>>
<head>
<title><?=$g4p_langue['entete_titre']?></title>
<meta name="Description" content="<?=$g4p_langue['entete_description']?>" />
<meta name="Keywords" content="<?=$g4p_langue['entete_mots_cles']?>" />
<meta name="Author" content="PAROIS Pascal" />
<meta http-equiv="Content-language" content="<?=$g4p_langue['entete_content_language']?>" />
<meta http-equiv="Content-Type" content="<?echo $accept_xml?'application/xhtml+xml':'text/html'?> charset=<?=$g4p_langue['entete_charset']?>" />

<?php

// affichage du thème
/*
if(file_exists($g4p_chemin.'styles/'.$_SESSION['theme'].'/style-'.$_SESSION['langue'].'.css'))
    $g4p_style=$g4p_chemin.'styles/'.$_SESSION['theme'].'/style-'.$_SESSION['langue'].'.css';
elseif(file_exists($g4p_chemin.'styles/'.$_SESSION['theme'].'/style.css'))
    $g4p_style=$g4p_chemin.'styles/'.$_SESSION['theme'].'/style.css';
else
    $g4p_style=$g4p_chemin.'styles/'.$g4p_config['default_theme'].'/style.css';

echo '<link href="',$g4p_style,'" type="text/css" media="screen" rel="stylesheet" title="style par defaut" />';
foreach($g4p_config['themes'] as $key=>$val)
{
    if($key!=$_SESSION['theme'])
    {
        if(file_exists($g4p_chemin.'styles/'.$key.'/style-'.$_SESSION['langue'].'.css'))
            echo '<link rel="alternate stylesheet" type="text/css" media="screen" href="',$g4p_chemin.'styles/'.$key.'/style-',$_SESSION['langue'],'.css" title="'.$val.'" />';
        else
            echo '<link rel="alternate stylesheet" type="text/css" media="screen" href="',$g4p_chemin.'styles/'.$key.'/style.css" title="'.$val.'" />';
    }
}
*/

// feuille de style pour l'impression
/*
$g4p_style='';
if(file_exists($g4p_chemin.'styles/'.$_SESSION['theme'].'/style-'.$_SESSION['langue'].'_print.css'))
    $g4p_style=$g4p_chemin.'styles/'.$_SESSION['theme'].'/style-'.$_SESSION['langue'].'_print.css';
elseif(file_exists($g4p_chemin.'styles/'.$_SESSION['theme'].'/style_print.css'))
    $g4p_style=$g4p_chemin.'styles/'.$_SESSION['theme'].'/style_print.css';
elseif(file_exists($g4p_chemin.'styles/'.$g4p_config['default_theme'].'/style_print.css'))
    $g4p_style=$g4p_chemin.'styles/'.$g4p_config['default_theme'].'/style_print.css';
if(!empty($g4p_style))
  echo '<link href="',$g4p_style,'" type="text/css" media="print" rel="stylesheet" title="style pour impression" />';
*/

//nouvelle feuille de style
echo '<link href="',$g4p_chemin,'styles/new.css" type="text/css" media="screen" rel="stylesheet" title="new_style" />';
  
?>  

<script language="JavaScript" type="text/javascript">
function confirme(objet, message)
{
    if (!confirm(message))
    {
        objet.href = "#";
        return 0;
    }
    else
    {
        return 1;
    }
}
</script>

<?php
    
if(!empty($g4p_javascript))
    echo $g4p_javascript;
    
echo '</head><body '.@$body.' >'."\n";

if (!isset($_SESSION['genea_db_nom']))
    $_SESSION['genea_db_nom']='';

//echo '<div style="position:absolute; top:20px; left:-30px;filter:alpha(opacity=10);
//opacity: 0.1;
//-moz-opacity:0.1;z-index:0;"><img src="'.$g4p_chemin.'styles/branche.png"></div>';

echo '<div id="container">';
echo '<div id="header">';

if(isset($g4p_titre_h1))
    echo '<h1>'.$g4p_titre_h1.'</h1>';
else
{
    if(isset($g4p_indi->nom) or isset($g4p_indi->prenom))
        echo '<h1>'.sprintf($g4p_langue['entete_grand_titre'],$_SESSION['genea_db_nom']).' / '.$g4p_indi->nom.' '.$g4p_indi->prenom.'</h1>';
    else
        echo '<h1>'.sprintf($g4p_langue['entete_grand_titre'],$_SESSION['genea_db_nom']).'</h1>';
}

echo '</div>';

echo '<div id="wrapper">';
echo '<div id="content">';
?>
