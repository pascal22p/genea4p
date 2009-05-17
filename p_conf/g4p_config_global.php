<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                                                          *
 *  Copyright (C) 2004-2005  PAROIS Pascal                                  *
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
 *              FIchier de configuration de genea4p                        *
 *                                                                         *
 * derni�re mise � jour : 09/02/2005                                       *
 * En cas de probl�me : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


// Configuration de la base de donn�es
$g4p_config['g4p_db_host']='localhost';
$g4p_config['g4p_db_login']='root';
$g4p_config['g4p_db_mdp']='';
$g4p_config['g4p_db_base']='genealogie';
$g4p_config['g4p_db_debug']=1; //Affiche les requ�tes SQLs

$g4p_config['g4p_httpcache']=0;

$g4p_config['default_lang']='fr-utf8';

$g4p_config['default_theme']='bleu';

$g4p_config['cookie_time_limit']=30*24*3600; //en secondes

$g4p_config['log']=0;

$g4p_config['historic_count']=10;

$g4p_config['allow_xml_header']=false;

$g4p_config['USE_DOT']=0;
$g4p_config['DOT_EXEC']="e:\\Program Files\\ATT\\Graphviz\\bin\\dot.exe";
$g4p_config['dot_limit_gen']=15;

//activer l'affichage d�taill� des relations (fr�res et soeurs, tantes et oncles, cousin(e)s...
$g4p_config['show_ext_rela']=1;

//activer le module cartographie
$g4p_config['module_carto']=0;

/*----------------------------------*/

//limite de poids pour l'upload des gedcoms 600ko maxi conseill� pour �viter d'avoir une erreur de time limit, en octet
$g4p_config['gedcom_upload_maxsize']=600000;


$g4p_config['g4p_type_install']='seule';
// valeurs possibles : seule, seule-mod_rewrite, module-npds, module-npds-mod_rewrite
// ne pas oublier d'�diter le fichier .htaccess de la racine pour le mod_rewrite
// utiliser l'archive zip seule ou npds pour plus de facilit� de configuration.

/*----------------------------------*/

date_default_timezone_set('Europe/Paris');

//constantes

// nombre de nom afficher par page
define('_AFF_NBRE_PRENOM_LISTPATRO_',200);
// nombre de nom afficher par page
define('_AFF_NBRE_NOM_',100);
// nombre de pr�nom afficher par page
define('_AFF_NBRE_PRENOM_',50);
//nombre de personnes maximum � afficher pour la liste de descendance
define('_NBRE_MAX_PERS_DESCENDANCE_',50000);
//Nre maximum d'appel de cr�ation de cache par page, necessaire pour eviter un gd nbre de requ�tes (150)
define('_NBRE_MAX_CACHE_CREES_',1500);
//nbre de g�n�rations maximum � afficher lors des listes 15
define('_NBRE_MAX_GENERATION_',15);
//nbre de g�n�rations maximum � afficher lors des listes 31
define('_NBRE_MAX_GENERATION_ASCENDANCE_',31);
//Nbre maximum de personnes affich� dans la liste d'ascendance 2500
define('_NBRE_MAX_PERS_ASCENDANCE_',25000);
//nbre de g�n�rations maximum � afficher lors des listes 21
define('_NBRE_MAX_GENERATION_ASCENDANCE_IMPLEXE_',31);
//Nbre maximum de personnes affich� dans la liste d'ascendance 1000
define('_NBRE_MAX_PERS_ASCENDANCE_IMPLEXE_',3000);
//Nbre maximum de villes � afficher dans la recherhce GNS 500
define('_NBRE_MAX_GNS_VILLE_',500);
//Nbres d'enregistrements dans les insertions group�s, les insertions group�es des notes et sources sont divis� par 10
define('_NBRE_MYSQL_ENR_',500);
//nombre maxi de personnes chargés en mémoire
define('_MAX_LOAD_INDI_',5000);

//couleurs utilis�es pour la liste de descendance
$g4p_couleur[]='#000000';
$g4p_couleur[]='#666666';
$g4p_couleur[]='#990000';
$g4p_couleur[]='#FF0000';
$g4p_couleur[]='#FF6600';
$g4p_couleur[]='#CC9933';//jaune
$g4p_couleur[]='#996600';
$g4p_couleur[]='#006600';
$g4p_couleur[]='#339900';//vert
$g4p_couleur[]='#009999';
$g4p_couleur[]='#003399';
$g4p_couleur[]='#660066';
$g4p_couleur[]='#6600FF';//bleu
$g4p_couleur[]='#CC00CC';
$g4p_couleur[]='#CC0066';

//couleurs utilis�es pour la "famille proche"
$g4p_couleur2[]='#cee7ff';
$g4p_couleur2[]='#FFCCFF';
$g4p_couleur2[]='#FFFFCC';
$g4p_couleur2[]='#FFCCCC';
$g4p_couleur2[]='#CCCCFF';

//type de documents autoris�s pour les m�dia, type MIME
$g4p_mime_type_autorise[]='application/msword';
$g4p_mime_type_autorise[]='application/pdf';
$g4p_mime_type_autorise[]='image/jpeg';
$g4p_mime_type_autorise[]='image/pjpeg';
$g4p_mime_type_autorise[]='image/gif';
$g4p_mime_type_autorise[]='video/msvideo';
$g4p_mime_type_autorise[]='image/png';




@ini_set ( "arg_separator", "&amp;");

include_once($g4p_chemin.'languages/liste_lang.php');
include_once($g4p_chemin.'styles/liste_theme.php');

$g4p_config['g4p_version']='0.20';

$g4p_cal_mrev=array(
1=>'VEND',
'BRUM',
'FRIM',
'NIVO',
'PLUV',
'VENT',
'GERM',
'FLOR',
'PRAI',
'MESS',
'THER',
'FRUC',
'COMP'
);

$g4p_cal_mhebreux=array(
1=>'TSH',
'CSH',
'KSL',
'TVT',
'SHV',
'ADR',
'ADS',
'NSN',
'IYR',
'SVN',
'TMZ',
'AAV',
'ELL'
);

$g4p_cal_gregorien=array(
1=>'JAN',
'FEB',
'MAR',
'APR',
'MAY',
'JUN',
'JUL',
'AUG',
'SEP',
'OCT',
'NOV',
'DEC'
);

$g4p_cal_ged_php=array(
'@#DHEBREW@'=>CAL_JEWISH,
'@#DFRENCH R@'=>CAL_FRENCH,
'@#DGREGORIAN@'=>CAL_GREGORIAN,
'@#DJULIAN@'=>CAL_JULIAN
);

//gestion des lieux, liste des subdivisions
//les traductions sont dans les fichiers de langue
$g4p_config['subdivision'][]='place_lieudit';
$g4p_config['subdivision'][]='place_ville';
$g4p_config['subdivision'][]='place_cp';
$g4p_config['subdivision'][]='place_insee';
$g4p_config['subdivision'][]='place_departement';
$g4p_config['subdivision'][]='place_region';
$g4p_config['subdivision'][]='place_pays';
$g4p_config['subdivision'][]='place_latitude';
$g4p_config['subdivision'][]='place_longitude';

//Affichage par default du lieu
//Donner l'ordre les num�ros des subdivisions � afficher
$g4p_config['subdivision_affichage_default'][]=1;
$g4p_config['subdivision_affichage_default'][]=4;
$g4p_config['subdivision_affichage_default'][]=5;
$g4p_config['subdivision_affichage_default'][]=2;

$g4p_config['lieu_separateur']=', ';

//cartographie
$g4p_config['cartographie']['cartes']=array('france','europe','russie');
$g4p_config['cartographie']['taille']=array(480,600,768,1024,1200);

//GNS
//R�gion du monde par d�fault pour la recherche GNS
$g4p_config['gns']['default_RC']='1';
//Pays par d�fault pour la recherche GNS
$g4p_config['gns']['default_CC']='FR';

$g4p_config['gns']['http']='http://earth-info.nga.mil/gns/html/cntyfile/';
$g4p_config['gns']['ftp']='ftp.nga.mil';
$g4p_config['gns']['ftp_rep']='/pub2/gns_data/';
$g4p_config['gns']['fichier_base']='_fips_adm1_code_def.txt';

//Mettre � 1 seulement si les bases correspondant � la recherche
//compl�mentaire INSEE sont charg�es
$g4p_config['insee']['use']=1;

?>
