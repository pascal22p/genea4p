<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                                                          *
 *  Copyright (C) 2004-2006  PAROIS Pascal                                  *
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
 *              Fichier de configuration - permissions                     *
 *                                                                         *
 * dernière mise à jour : 29/06/2006                                       *
 * En cas de problème : http://genea4p.espace.fr.to                        *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/*************
Pour ajouter une permission:
- créer la constante,
- ajouter la permission au tableau $g4p_permissions[]
- ajouter la traduction aux fichiers de langues 
*************/
define('_PERM_DOWNLOAD_',1);
define('_PERM_GEDCOM_',2);
define('_PERM_PDF_',3);
define('_PERM_DOT_',4);
define('_PERM_NOTE_',5);
define('_PERM_SOURCE_',6);
define('_PERM_EDIT_FILES_',7);
define('_PERM_SUPPR_FILES_',8);
define('_PERM_SUPER_ADMIN_',9);
define('_PERM_ADMIN_',10);
define('_PERM_AFF_DATE_',11);
define('_PERM_MASK_DATABASE_',12);
define('_PERM_MASK_INDI_',13);
define('_PERM_MULTIMEDIA_',14);

$g4p_permissions[_PERM_DOWNLOAD_]['id']=_PERM_DOWNLOAD_;
$g4p_permissions[_PERM_DOWNLOAD_]['type']='select';
$g4p_permissions[_PERM_DOWNLOAD_]['value']='oui-non';
$g4p_permissions[_PERM_DOWNLOAD_]['default']=0;
$g4p_permissions[_PERM_GEDCOM_]['id']=_PERM_GEDCOM_;
$g4p_permissions[_PERM_GEDCOM_]['type']='select';
$g4p_permissions[_PERM_GEDCOM_]['value']='oui-non';
$g4p_permissions[_PERM_GEDCOM_]['default']=0;
$g4p_permissions[_PERM_PDF_]['id']=_PERM_PDF_;
$g4p_permissions[_PERM_PDF_]['type']='select';
$g4p_permissions[_PERM_PDF_]['value']='oui-non';
$g4p_permissions[_PERM_PDF_]['default']=0;
$g4p_permissions[_PERM_DOT_]['id']=_PERM_DOT_;
$g4p_permissions[_PERM_DOT_]['type']='select';
$g4p_permissions[_PERM_DOT_]['value']='oui-non';
$g4p_permissions[_PERM_DOT_]['default']=0;
$g4p_permissions[_PERM_NOTE_]['id']=_PERM_NOTE_;
$g4p_permissions[_PERM_NOTE_]['type']='select';
$g4p_permissions[_PERM_NOTE_]['value']='oui-non';
$g4p_permissions[_PERM_NOTE_]['default']=0;
$g4p_permissions[_PERM_SOURCE_]['id']=_PERM_SOURCE_;
$g4p_permissions[_PERM_SOURCE_]['type']='select';
$g4p_permissions[_PERM_SOURCE_]['value']='oui-non';
$g4p_permissions[_PERM_SOURCE_]['default']=0;
$g4p_permissions[_PERM_EDIT_FILES_]['id']=_PERM_EDIT_FILES_;
$g4p_permissions[_PERM_EDIT_FILES_]['type']='select';
$g4p_permissions[_PERM_EDIT_FILES_]['value']='oui-non';
$g4p_permissions[_PERM_EDIT_FILES_]['default']=0;
$g4p_permissions[_PERM_SUPPR_FILES_]['id']=_PERM_SUPPR_FILES_;
$g4p_permissions[_PERM_SUPPR_FILES_]['type']='select';
$g4p_permissions[_PERM_SUPPR_FILES_]['value']='oui-non';
$g4p_permissions[_PERM_SUPPR_FILES_]['dependance']=array(_PERM_EDIT_FILES_=>1); //remarque, les dependances ne sont pas lues recursivement
$g4p_permissions[_PERM_SUPPR_FILES_]['default']=0;
$g4p_permissions[_PERM_SUPER_ADMIN_]['id']=_PERM_SUPER_ADMIN_;
$g4p_permissions[_PERM_SUPER_ADMIN_]['type']='select';
$g4p_permissions[_PERM_SUPER_ADMIN_]['value']='oui-non';
$g4p_permissions[_PERM_SUPER_ADMIN_]['dependance']=array(_PERM_DOWNLOAD_=>1, _PERM_GEDCOM_=>1, _PERM_PDF_=>1, _PERM_DOT_=>1, _PERM_NOTE_=>1, _PERM_SOURCE_=>1, _PERM_EDIT_FILES_=>1, _PERM_SUPPR_FILES_=>1, _PERM_ADMIN_=>1, _PERM_AFF_DATE_=>1, _PERM_MULTIMEDIA_=>1);
$g4p_permissions[_PERM_SUPER_ADMIN_]['default']=0;
$g4p_permissions[_PERM_ADMIN_]['id']=_PERM_ADMIN_;
$g4p_permissions[_PERM_ADMIN_]['type']='select';
$g4p_permissions[_PERM_ADMIN_]['value']='oui-non';
$g4p_permissions[_PERM_ADMIN_]['default']=0;
$g4p_permissions[_PERM_ADMIN_]['dependance']=array(_PERM_DOWNLOAD_=>1, _PERM_GEDCOM_=>1, _PERM_PDF_=>1, _PERM_DOT_=>1, _PERM_NOTE_=>1, _PERM_SOURCE_=>1, _PERM_EDIT_FILES_=>1, _PERM_SUPPR_FILES_=>1, _PERM_AFF_DATE_=>1, _PERM_MULTIMEDIA_=>1);
$g4p_permissions[_PERM_AFF_DATE_]['id']=_PERM_AFF_DATE_;
$g4p_permissions[_PERM_AFF_DATE_]['type']='select';
$g4p_permissions[_PERM_AFF_DATE_]['value']='oui-non';
$g4p_permissions[_PERM_AFF_DATE_]['default']=0;
$g4p_permissions[_PERM_MASK_DATABASE_]['id']=_PERM_MASK_DATABASE_;
$g4p_permissions[_PERM_MASK_DATABASE_]['type']='select';
$g4p_permissions[_PERM_MASK_DATABASE_]['value']='oui-non';
$g4p_permissions[_PERM_MASK_DATABASE_]['default']=0;
$g4p_permissions[_PERM_MASK_INDI_]['id']=_PERM_MASK_INDI_;
$g4p_permissions[_PERM_MASK_INDI_]['type']='select';
$g4p_permissions[_PERM_MASK_INDI_]['value']='oui-non';
$g4p_permissions[_PERM_MASK_INDI_]['default']=1;
$g4p_permissions[_PERM_MULTIMEDIA_]['id']=_PERM_MULTIMEDIA_;
$g4p_permissions[_PERM_MULTIMEDIA_]['type']='select';
$g4p_permissions[_PERM_MULTIMEDIA_]['value']='oui-non';
$g4p_permissions[_PERM_MULTIMEDIA_]['default']=0;
 
?>
