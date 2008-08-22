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
 *                     Page de téléchargements                             *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/**** GEDCOM ****/
$g4p_module_export['export_gedcom_db.php']['titre']='Création d\'un fichier GEDCOM de la base de donnée';
//$g4p_module_export['export_gedcom_db.php']['file']='export_gedcom_db.php';
$g4p_module_export['export_gedcom_db.php']['file']='export_gedcom/exportGedcom.php';
//modifier ici la permission necessaire du module
//_PERM_SUPER_ADMIN_ ou _PERM_ADMIN_
$g4p_module_export['export_gedcom_db.php']['permission']=_PERM_ADMIN_;
$g4p_module_export['export_gedcom_db.php']['formulaire']='Encodage : <input type="radio" name="g4p_encodage" value="utf8" />UTF-8 <input type="radio" name="g4p_encodage" value="ansi" checked="checked" />ANSI <input type="radio" name="g4p_encodage" value="ansel" />ANSEL<br />
Intégrer les notes : <input type="radio" name="g4p_notes" value="1" checked="checked" />Oui <input type="radio" name="g4p_notes" value="0" />non<br />
Intégrer les sources : <input type="radio" name="g4p_sources" value="1" checked="checked" />Oui <input type="radio" name="g4p_sources" value="0" />non<br />
Intégrer les médias : <input type="radio" name="g4p_medias" value="1" checked="checked" />Oui <input type="radio" name="g4p_medias" value="0" />non<br />
Ajouter les paramètres d\'export dans la description : <input type="radio" name="g4p_parm_desc" value="1" checked="checked" />Oui <input type="radio" name="g4p_parm_desc" value="0" />non<br />
';
//Pascal : ajout profil des lieux
$g4p_module_export['export_gedcom_db.php']['formulaire'].='<label for="lieu">'.$g4p_langue['profil_lieu'].'</label><br />';
for($i=0; $i<count($g4p_config['subdivision']); $i++)
{
  $g4p_module_export['export_gedcom_db.php']['formulaire'].='<select name="place['.$i.']">';
  $g4p_module_export['export_gedcom_db.php']['formulaire'].='<option value="-1"';
  $g4p_module_export['export_gedcom_db.php']['formulaire'].='>'.$g4p_langue['profil_place_aucun'].'</option>';
  foreach($g4p_config['subdivision'] as $val)
    $g4p_module_export['export_gedcom_db.php']['formulaire'].='<option value="'.$val.'">'.$g4p_langue[$val].'</option>';
  $g4p_module_export['export_gedcom_db.php']['formulaire'].='</select>,';
}
 //fin gestion des lieux    


/**** PDF ****/

$g4p_module_export['export_pdf_db.php']['titre']='Création d\'un fichier PDF de la base de donnée';
$g4p_module_export['export_pdf_db.php']['file']='export_pdf_db.php';
//modifier ici la permission necessaire du module
//_PERM_SUPER_ADMIN_ ou _PERM_ADMIN_
$g4p_module_export['export_pdf_db.php']['permission']=_PERM_ADMIN_;
$g4p_module_export['export_pdf_db.php']['formulaire']='
Intégrer les notes : <input type="radio" name="g4p_notes" value="oui" checked="checked" />Oui <input type="radio" name="g4p_notes" value="non" />non<br />
Intégrer les sources : <input type="radio" name="g4p_sources" value="oui" checked="checked" />Oui <input type="radio" name="g4p_sources" value="non" />non<br />
Intégrer les médias : <input type="radio" name="g4p_medias" value="oui" checked="checked" />Oui <input type="radio" name="g4p_medias" value="non" />non<br />
Ajouter les paramètres d\'export dans la description : <input type="radio" name="g4p_parm_desc" value="oui" checked="checked" />Oui <input type="radio" name="g4p_parm_desc" value="non" />non<br />
';

/**** LATEX ****/
/*
$g4p_module_export['export_latex_db.php']['titre']='Création d\'un fichier Latex de la base de donnée';
$g4p_module_export['export_latex_db.php']['file']='export_latex_db.php';
//modifier ici la permission necessaire du module
//_PERM_SUPER_ADMIN_ ou _PERM_ADMIN_
$g4p_module_export['export_latex_db.php']['permission']=_PERM_SUPER_ADMIN_;
$g4p_module_export['export_latex_db.php']['formulaire']='<b>Cet export n\'est pas encore fonctionnel</b>
Intégrer les notes : <input type="radio" name="g4p_notes" value="oui" checked="checked" />Oui <input type="radio" name="g4p_notes" value="non" />non<br />
Intégrer les sources : <input type="radio" name="g4p_sources" value="oui" checked="checked" />Oui <input type="radio" name="g4p_sources" value="non" />non<br />
Intégrer les médias : <input type="radio" name="g4p_medias" value="oui" checked="checked" />Oui <input type="radio" name="g4p_medias" value="non" />non<br />
Ajouter les paramètres d\'export dans la description : <input type="radio" name="g4p_parm_desc" value="oui" checked="checked" />Oui <input type="radio" name="g4p_parm_desc" value="non" />non<br />
';*/
/**** FICHIER PERSONNALISE ****/
$g4p_module_export['upload_file.php']['titre']='Upload d\'un fichier';
$g4p_module_export['upload_file.php']['file']='upload_file.php';
//modifier ici la permission necessaire du module
//_PERM_SUPER_ADMIN_ ou _PERM_ADMIN_
$g4p_module_export['upload_file.php']['permission']=_PERM_ADMIN_;
$g4p_module_export['upload_file.php']['formulaire']='
Fichier à mettre en téléchargement : <input name="g4p_file" id="g4p_file" type="file" /><br />
';


?>
