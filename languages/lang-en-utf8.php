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
 *        Futur fichier pour l'édition des différentes langues             *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

//traductions générales
$g4p_langue[_PERM_DOWNLOAD_]='Allow reports and GEDCOMS download';
$g4p_langue[_PERM_GEDCOM_]='Allow GEDCOM export';
$g4p_langue[_PERM_PDF_]='Allow PDF export';
$g4p_langue[_PERM_DOT_]='Allow DOT export';
$g4p_langue[_PERM_NOTE_]='voir les notes';          
$g4p_langue[_PERM_SOURCE_]='voir les sources';      
$g4p_langue[_PERM_EDIT_FILES_]='Change individuals files';
$g4p_langue[_PERM_SUPPR_FILES_]='Delete';
$g4p_langue[_PERM_ADMIN_]='Administrator permissions';
$g4p_langue[_PERM_SUPER_ADMIN_]='Root Administrator permissions';
$g4p_langue[_PERM_AFF_DATE_]='Show dates less than 100 years';
$g4p_langue[_PERM_MASK_DATABASE_]='Hide database';
$g4p_langue[_PERM_MASK_INDI_]='Hide person';
$g4p_langue[_PERM_MULTIMEDIA_]='Show extern documents and references';

$g4p_langue['acces_non_autorise']='You are not allowed to see this information';
$g4p_langue['acces_admin']='Administrator rank required';
$g4p_langue['acces_login_pass_incorrect']='Wrong login and/or password';

$g4p_langue['db_select_base_impossible']='Impossible to select the database';
$g4p_langue['db_conection_impossible']='Impossible to connect to the database server';
$g4p_langue['db_erreur_requete']='Error during the request';

$g4p_langue['submit_connection']=' Connection ';
$g4p_langue['submit_enregistrer']=' Save ';
$g4p_langue['submit_ajouter']=' Add ';
$g4p_langue['submit_modifier']=' Modify ';
$g4p_langue['submit_editer']=' Modify ';
$g4p_langue['submit_vider']=' Vider ';
$g4p_langue['submit_restaurer']=' Restore ';
$g4p_langue['submit_rechercher']=' Search '; //ajouté le 15/01/2005

$g4p_langue['id_inconnu']='Aucun id n\'est défini';
$g4p_langue['retour']='[back]';
$g4p_langue['detail']='[detail]';
$g4p_langue['Supprimer']='Delete';
$g4p_langue['date_cache']='-hidden date-';


//menus
$g4p_langue['menu_mod_event']='Modify event';
$g4p_langue['menu_del_event']='Delete event';
$g4p_langue['menu_mod_note']='Modify note';
$g4p_langue['menu_mod_source']='Modifier source';
$g4p_langue['menu_ajout_note']='Add a note';
$g4p_langue['menu_ajout_source']='Add source';
$g4p_langue['menu_ajout_media']='Add média';
$g4p_langue['menu_ajout_relation']='Add a new link';
$g4p_langue['menu_sppr_indi']='Delete person';
$g4p_langue['menu_sppr_fam']='Delete family';
$g4p_langue['menu_sppr_confirme']='Confirm suppression ?'; //Jscript
$g4p_langue['menu_suppr_source']='Delete source'; //ajouté le 12/01/2005 19h03
$g4p_langue['menu_modif_fiche']='Modify person';
$g4p_langue['menu_masque_fiche']='Hide person';
$g4p_langue['menu_demasque_fiche']='Show person';
$g4p_langue['menu_recreer_cache']='rebuild cache';
$g4p_langue['menu_mod_famille']='Modify family'; //nouvelle ligne 11/05/2005 19h20



//page menu.php
$g4p_langue['menu_sommaire']='Sommaire';
$g4p_langue['menu_liste_base']='database list';
$g4p_langue['menu_patronyme']='Patronymes';
$g4p_langue['menu_connexion']='Connection';
$g4p_langue['menu_deconnexion']='Deconnection';
$g4p_langue['menu_aide_saisie']='Help';
$g4p_langue['menu_tags']='Explicat° of tags';
$g4p_langue['menu_stats']='Statistic';
$g4p_langue['menu_rechercher']='Find';
$g4p_langue['menu_profil']='My profile';
$g4p_langue['menu_creer']='Create';
$g4p_langue['menu_creer_indi']='A person';
$g4p_langue['menu_creer_famille']='A family';
$g4p_langue['menu_creer_depot']='Un dépot';
$g4p_langue['menu_voir']='Show';
$g4p_langue['menu_voir_arbre_asc']='Ascendant tree';
$g4p_langue['menu_voir_liste_desc']='Descendance list';
$g4p_langue['menu_voir_liste_asc']='Ascendance list';
$g4p_langue['menu_telecharger']='Download';
$g4p_langue['menu_telecharger_gedc']='GEDCOM complet';
$g4p_langue['menu_telecharger_pdfc']='Dossier PDF';
$g4p_langue['menu_telecharger_pdfi']='Individual file';
$g4p_langue['menu_telecharger_dot']='Tree export';
$g4p_langue['menu_admin']='Administration';
$g4p_langue['menu_admin_import_ged']='GEDCOM import';
$g4p_langue['menu_admin_import_dorotree']='Dorotree import';
$g4p_langue['menu_admin_index']='Create name index';
$g4p_langue['menu_admin_ajout_base']='Add a new database';
$g4p_langue['menu_admin_gere_telechargement']='manage downloads';
$g4p_langue['menu_admin_gere_perm']='Manage permissions';
$g4p_langue['menu_admin_vide_base']='Empty database';
$g4p_langue['menu_admin_suppr_base']='Delete a database';
$g4p_langue['menu_admin_crash']='Repair import crash';
$g4p_langue['menu_famille_proche']='Direct family'; //ajouter le 11/10/2005 à 23h37
$g4p_langue['menu_fiche']='Individual file'; //ajouter le 11/10/2005 à 23h37

//traduction de la page d'accueil (accueil.php) ajouter le 11/10/2005
$g4p_langue['accueil_titre']='Database list';
$g4p_langue['accueil_renseignements']='About remarks, informations, you could write to me :
<a href="mailto:genealogie@phpalbum.org">PAROIS Pascal</a>';

//pour les dates, chaine de strftime et localisation
$g4p_langue['date_setlocale']=''; //deprecated
//setlocale(LC_ALL, $g4p_langue['date_setlocale']);
$g4p_langue['entete_content_language']='en';
$g4p_langue['entete_charset']='UTF-8'; // Don't change
// j'ai du faire une fonction propre pour strftime, c'est la même syntaxe mais tous les masques ne sont pas supportés
// Envoyer moi un mail pour rajouter un masque.
$g4p_langue['date_complete']="%A %d %B %Y at %H:%M:%S";
$g4p_langue['date']="%A %d %B %Y";

//entetes (entete.php)
$g4p_langue['entete_titre']='genea4p';
//$g4p_langue['entete_content_language'] voir rubrique date et localisation
//$g4p_langue['entete_charset'] idem
$g4p_langue['entete_description']='';
$g4p_langue['entete_mots_cles']='genealogie, genealogy, genea4p, phpalbum, php';
$g4p_langue['entete_grand_titre']="%s family";

//page connect.php
$g4p_langue['connect_titre']='Please, identify you&nbsp;: ';
$g4p_langue['connect_mail']='Your e-mail&nbsp;: ';
$g4p_langue['connect_pass']='Your password&nbsp;: ';
$g4p_langue['connect_bienvenue']='Hi, you are connected';
$g4p_langue['connect_cookie']='Save informations into a cookie ';

//page detail_f.php et detail_i.php
$g4p_langue['detail_titre_famille']="Famille %1\$s - %2\$s family";
$g4p_langue['detail_titre_individu']="%1\$s %2\$s";
$g4p_langue['detail_type_event']='Event type&nbsp;: ';
$g4p_langue['detail_description']='Description&nbsp;: ';
$g4p_langue['detail_date_event']='Event date&nbsp;: ';
$g4p_langue['detail_lieu']='place&nbsp;: ';
$g4p_langue['detail_cause']='Event cause&nbsp;: ';
$g4p_langue['detail_cal_gregorien']='gregorian calendar&nbsp;: ';
$g4p_langue['detail_cal_revolutionnaire']='Calendrien révolutionnaire&nbsp;: ';
$g4p_langue['detail_cal_juif']='Jewish calendar&nbsp;: ';
$g4p_langue['detail_cal_julien']='Julian calendar&nbsp;: ';
$g4p_langue['detail_age']='Age au moment de l\'évènement&nbsp;:';

//page download.php
$g4p_langue['download_titre']='Files list&nbsp;: ';
$g4p_langue['download_gedcom']='GEDCOM files&nbsp;: ';
$g4p_langue['download_link_ged']="%1\$s.zip - created on : %2\$s - (%3\$s ko)";
$g4p_langue['download_link_pdf']="%1\$s.pdf - created on : %2\$s - (%3\$s ko)";

//page export_dot.php
$g4p_langue['dot_dot_non_present']="Impossible to create the requested file format. "
        . "DOT software maybe not installed ";
$g4p_langue['dot_ligne']='Command line&nbsp;:';
$g4p_langue['dot_titre']='DOT format creation';
$g4p_langue['dot_sstitre_info']='Information';
$g4p_langue['dot_submit_id']=' Update ';
$g4p_langue['dot_sstitre_config']='Configuration';
$g4p_langue['dot_type_extract']='Extracted type&nbsp;: ';
$g4p_langue['dot_arbre_asc']='Ascendant tree';
$g4p_langue['dot_arbre_desc']='Descendant tree';
$g4p_langue['dot_arbre_asc_desc']='Ascendant and descendant tree';
$g4p_langue['dot_arbre_complet']='Complete database';
$g4p_langue['dot_date_event']='Show event date';
$g4p_langue['dot_lieu_event']='Show event place';
$g4p_langue['dot_adresse']='Shows person address';
$g4p_langue['dot_max_asc']='Profondeur maximum de l\'extraction coté ascendance&nbsp;:';
$g4p_langue['dot_max_desc']='Profondeur maximum de l\'extraction coté descendance&nbsp;:';
$g4p_langue['dot_nolimit']='Don\'t use limit&nbsp;?';
$g4p_langue['dot_format']='File format requested&nbsp;: ';
$g4p_langue['dot_divers']='Others&nbsp;: ';
$g4p_langue['dot_visualiser']='Visualiser';
$g4p_langue['dot_telecharger']='Download';
$g4p_langue['dot_submit_executer']=' Execute ';
$g4p_langue['dot_erreur_id']="Root undefined. " . "<br/>"
    . "choose a person and reload. ";

//page famille_proche.php
$g4p_langue['fproche_description']='description&nbsp;: ';
$g4p_langue['fproche_lieu']='place&nbsp;: ';
$g4p_langue['fproche_parent_inconnu']='Unknown parent';
$g4p_langue['fproche_freres_soeurs']='Brothers and sisters&nbsp;: ';
$g4p_langue['fproche_conjoint_inc']='Unknown husband/wife';
$g4p_langue['fproche_marie']='maried to&nbsp;: ';

//page import_gedcom
$g4p_langue['import_ged_titre']='Import data from a GEDCOM file';
$g4p_langue['import_ged_explications']='<i>If data already exist, they won\'t be replaced<br />
    GEDCOM files must be placed in gedcoms directory.<br /><br /></i>';
$g4p_langue['import_ged_submit']=' Load GEDCOM ';

$g4p_langue['dorotree_ged_titre']='Load GEDCOM create by Dorotree';
$g4p_langue['import_ged_reussi']="Importation duration : %1\$ss, number of queries : %2\$s";
$g4p_langue['import_ged_lieu_explications']="Choose the significance of
these columns";//ajouté le 27/05/2005
$g4p_langue['import_ged_lieu_submit']="Convert the file";//ajouté le 27/05/2005

//page index.php
$g4p_langue['index_masquer']='Person mask to annonyms';
$g4p_langue['index_chan']="Last update&nbsp;: %s";
$g4p_langue['index_nom']='Surname&nbsp;: ';
$g4p_langue['index_prenom']='Name&nbsp;: ';
$g4p_langue['index_sexe']='Sex&nbsp;: ';
$g4p_langue['index_sexe_valeur']['M']='Male';  //ajouté le 12/01/2005 à 19h29
$g4p_langue['index_sexe_valeur']['F']='Female';  //ajouté le 12/01/2005 à 19h29
$g4p_langue['index_sexe_valeur']['I']='Unknown';
$g4p_langue['index_npfx']='Name prefix (NPFX)&nbsp;: ';
$g4p_langue['index_givn']='Usual name (GIVN)&nbsp;: ';
$g4p_langue['index_nick']='Nickname (NICK)&nbsp;: ';
$g4p_langue['index_spfx']='Surname prefix (SPFX)&nbsp;: ';
$g4p_langue['index_nsfx']='Suffix (NSFX)&nbsp;: ';
$g4p_langue['index_description']='Description&nbsp;: ';
$g4p_langue['index_lieu']='Place&nbsp;: ';
$g4p_langue['index_ype_parent']='Parents type&nbsp;: ';
$g4p_langue['index_parent_inconnu']='Unknown parent';
$g4p_langue['index_indi_nconnu']='Unknown or hide person';

//liste prenoms
$g4p_langue['index_prenoms_page']='Pages&nbsp;: '; //ajouté le 15/01/2005

//page liste_ascendance.php
$g4p_langue['liste_asc_max_pers']="more than %s persons!! The ascendance presented is not complete";
$g4p_langue['liste_asc_max_cache']="Query overloaded protection is activate, the ascendance presented is not complete";
$g4p_langue['liste_asc_max_gen']='Limite de génération atteinte';
$g4p_langue['liste_asc_titre']="Ascendance of&nbsp;: %1\$s %2\$s";
$g4p_langue['liste_asc_nb_gen']='Nombre de générations à développer&nbsp;: ';
$g4p_langue['liste_asc_recherche_implexe']='Rechercher les implexes&nbsp;: ';
$g4p_langue['liste_asc_nb_pers_implexe']="<b>Génération&nbsp;: %1\$s</b>, %2\$s personne(s) dont %3\$s unique(s)";
$g4p_langue['liste_asc_nb_pers']="<b>Génération&nbsp;: %1\$s</b>, %2\$s personne(s)";
$g4p_langue['liste_asc_info_sosa']='SOSA number is write in bold';
$g4p_langue['liste_asc_implexe']='Implexe, SOSA équivalents&nbsp;: ';
$g4p_langue['liste_asc_nb_asc_total_implexe']="%1\$s Ascendants dont %2\$s uniques";
$g4p_langue['liste_asc_nb_asc_total']="%1\$s Ascendants";

//page liste_descendance
$g4p_langue['liste_desc_max_pers']="More than %s persons presented!!";
$g4p_langue['liste_desc_max_cache']='Query overloaded protection is activate';
$g4p_langue['liste_desc_inconnu']='Unknown';
$g4p_langue['liste_desc_marie_avec']="- Maried to&nbsp;: %s";
$g4p_langue['liste_desc_max_gen']="Limite de génération atteinte, %sème génération";
$g4p_langue['liste_desc_titre']="Descendance de : %1\$s %2\$s";
$g4p_langue['liste_desc_nb_gen']='Nombre de générations à développer&nbsp;: ';
$g4p_langue['liste_desc_nb_desc_total']="%s Descendants";

//page recherche.php
$g4p_langue['recherche_titre']="Search in database : %s";
$g4p_langue['recherche_rechercher']='Search&nbsp;: ';
$g4p_langue['recherche_indi']='A person';
$g4p_langue['recherche_note']='a note';
$g4p_langue['recherche_source']='a source';
$g4p_langue['recherche_media']='a média';
$g4p_langue['recherche_event']='an event';
$g4p_langue['recherche_avance']='Advanced search';
$g4p_langue['recherche_nom']='Surname&nbsp;: ';
$g4p_langue['recherche_prenom']='name&nbsp;: ';
$g4p_langue['recherche_indi_result']="Number of results : %s (limited responses : 200)";
$g4p_langue['recherche_indi_select']='Select the person';
$g4p_langue['recherche_note_text']='text&nbsp;: ';
$g4p_langue['recherche_note_parent']='Elément parent de la note&nbsp;: ';
$g4p_langue['recherche_note_orphelines']='Orphelines';
$g4p_langue['recherche_note_tous']='All';
$g4p_langue['recherche_note_indi']='Person';
$g4p_langue['recherche_note_event']='Event';
$g4p_langue['recherche_note_familel']='Family';
$g4p_langue['recherche_note_rechercher']=' Search ';
$g4p_langue['recherche_note_num_note']='Note number&nbsp;: ';
$g4p_langue['recherche_note_chan']='Last modification&nbsp;: ';
$g4p_langue['recherche_note_select_note']='Select note';
$g4p_langue['recherche_note_note_de']='Note of&nbsp;: ';
$g4p_langue['recherche_note_noteevent']="Note of the event&nbsp;: %1\$s, le %2\$s, à %3\$s";
$g4p_langue['recherche_note_notefamille']='Note of the family&nbsp;: ';

$g4p_langue['recherche_sour_titre']='titre&nbsp;: ';
$g4p_langue['recherche_sour_texte']='texte&nbsp;: ';
$g4p_langue['recherche_sour_parent']='Elément parent de la source&nbsp;: ';
$g4p_langue['recherche_sour_orphelines']='Orphelines';
$g4p_langue['recherche_sour_tous']='Tous';
$g4p_langue['recherche_sour_indi']='Individu';
$g4p_langue['recherche_sour_event']='Evènement';
$g4p_langue['recherche_sour_famile']='Famille';
$g4p_langue['recherche_sour_rechercher']=' Rechercher ';
$g4p_langue['recherche_sour_num_sour']='Source numéro&nbsp;: ';
$g4p_langue['recherche_sour_auth']='Auteur&nbsp;: ';
$g4p_langue['recherche_sour_ref']='Référence&nbsp;: ';
$g4p_langue['recherche_sour_page']='Page&nbsp;: ';
$g4p_langue['recherche_sour_type']='Type de source&nbsp;: ';
$g4p_langue['recherche_sour_select_sour']='Sélectionner la source';
$g4p_langue['recherche_sour_chan']='dernière modification&nbsp;: ';
$g4p_langue['recherche_sour_sour_de']='Source de&nbsp;: ';
$g4p_langue['recherche_sour_soureven']="Source de l\'évènement&nbsp; %1\$s, le %2\$s, à %3\$s";
$g4p_langue['recherche_sour_sourfamille']='Source de la famille&nbsp;: ';

$g4p_langue['recherche_obje_titre']='Title&nbsp;: ';
$g4p_langue['recherche_obje_parent']='Elément parent du média&nbsp;: ';
$g4p_langue['recherche_obje_orphelines']='Orphelins';
$g4p_langue['recherche_obje_tous']='All';
$g4p_langue['recherche_obje_indi']='Person';
$g4p_langue['recherche_obje_event']='Event';
$g4p_langue['recherche_obje_famile']='Family';
$g4p_langue['recherche_obje_rechercher']=' Search ';
$g4p_langue['recherche_obje_num_obje']='Média numéro&nbsp;: ';
$g4p_langue['recherche_obje_chan']='Last modification&nbsp;: ';
$g4p_langue['recherche_obje_select_obje']='Select the media';
$g4p_langue['recherche_obje_media_de']='Média de&nbsp;: ';
$g4p_langue['recherche_obje_mediaeven']="Média de l\'évènement&nbsp;: %1\$s, le %2\$s, à %3\$s";
$g4p_langue['recherche_obje_mediafamille']='Média de la famille&nbsp; ';

$g4p_langue['recherche_even_type']='Event type&nbsp;: ';
$g4p_langue['recherche_even_typedescr']='Type description&nbsp;: ';
$g4p_langue['recherche_even_descr']='Description&nbsp;: ';
$g4p_langue['recherche_even_year']='Year of event&nbsp;: ';
$g4p_langue['recherche_even_lieu']='Place&nbsp;: ';
$g4p_langue['recherche_even_cause']='Event cause&nbsp;: ';
$g4p_langue['recherche_even_rechercher']=' Search ';

//page statistiques.php
$g4p_langue['stats_titre']="La base de donnée %s en chiffres";
$g4p_langue['stats_nb_indi']='Nombre d\'individus enregistrés&nbsp;: ';
$g4p_langue['stats_nb_ievent']='Nombre d\'évènements inidviduels&nbsp;: ';
$g4p_langue['stats_nb_fevent']='Nombre d\'évènements familiaux&nbsp;: ';
$g4p_langue['stats_nb_sour']='Nombre de sources&nbsp;: ';
$g4p_langue['stats_nb_note']='Nombre de notes&nbsp;: ';
$g4p_langue['stats_nb_media']='Nombre de mocuments multimédia&nbsp;: ';
$g4p_langue['stats_familles']='Les 10 familles les plus nombreuses&nbsp;: ';
$g4p_langue['stats_patronymes']='Les 10 patronymes les plus usités&nbsp;: ';
$g4p_langue['stats_nb_enfants']='Nombre moyen d\'enfant par couple&nbsp;: ';
$g4p_langue['stats_x_enfants']="%s enfant(s)"; //ajouté le 23/01/2005
$g4p_langue['stats_x_pers']="%s personne(s)";  //ajouté le 23/01/2005


//page sys_function.php
//affiche_note()
$g4p_langue['sys_function_note_chan']='Dernière modification&nbsp;: ';
$g4p_langue['sys_function_note']='Note(s)&nbsp;: ';

//affiche_source()
$g4p_langue['sys_function_sour_titre']='titre&nbsp;: ';
$g4p_langue['sys_function_sour_texte']='texte&nbsp;: ';
$g4p_langue['sys_function_sour_num_sour']='Source&nbsp;: ';
$g4p_langue['sys_function_sour_auth']='Auteur&nbsp;: ';
$g4p_langue['sys_function_sour_ref']='Référence&nbsp;: ';
$g4p_langue['sys_function_sour_page']='Page&nbsp;: ';
$g4p_langue['sys_function_sour_type']='Type de source&nbsp;: ';
$g4p_langue['sys_function_sour_chan']='dernière modification&nbsp;: ';
$g4p_langue['sys_function_sour_publ']='Publication&nbsp;: ';

$g4p_langue['sys_function_sour_depot']='Informations sur le dépot&nbsp;: ';
$g4p_langue['sys_function_sour_depot_addr']='Adresse&nbsp;: ';
$g4p_langue['sys_function_sour_depot_ville']='Ville&nbsp;: ';
$g4p_langue['sys_function_sour_depot_cp']='Code postal&nbsp;: ';
$g4p_langue['sys_function_sour_depot_pays']='Pays&nbsp;: ';
$g4p_langue['sys_function_sour_depot_tel']='Téléphone ';

$g4p_langue['sys_function_asso_titre']='Relation&nbsp;: ';
$g4p_langue['sys_function_asso_mod']='Modifier'; //ajouté le 15/05/2005
$g4p_langue['sys_function_asso_suppr']='Supprimer'; //ajouté le 15/05/2005

$g4p_langue['sys_function_mariage_chan']="Dernière modification : %s";
$g4p_langue['sys_function_conjoint']='Conjoint&nbsp;: ';
$g4p_langue['sys_function_conjoint_inc']='Conjoint&nbsp;: Inconnu(e)';
$g4p_langue['sys_function_enfants']='Enfants issus de l\'union&nbsp;: ';
$g4p_langue['sys_function_enfant_inc']='- Enfant inconnu -';
$g4p_langue['sys_function_media_chan']="Dernière modification : %s";
$g4p_langue['sys_function_media_titre']='Documents externes et références&nbsp;: ';
$g4p_langue['sys_function_media_titre2']='Titre&nbsp;: ';
$g4p_langue['sys_function_media_format']='Format&nbsp;: ';
$g4p_langue['sys_function_media_lien']='Lien&nbsp;: ';

//messages d'erreurs ou autres (exec.php pour la plupart)
$g4p_langue['message_imp_suppr_cache']='Impossible de Supprimer le cache de données<br />';
$g4p_langue['message_imp_creer_cache']='Impossible de creer le cache<br />';
$g4p_langue['message_imp_creer_fichier']="Impossible de créer le fichier : indi_%s.txt<br />";
$g4p_langue['message_req_succes']='Requète éffectuée avec succès<br />';
$g4p_langue['message_req_echec']='Erreur lors de la requète<br />';
$g4p_langue['message_note_enr']='Note enregistrée<br />';
$g4p_langue['message_lien_note_succes']='Lien avec la note éffectuée<br />';
$g4p_langue['message_suppr_lien_note_succes']='Le lien avec la note a été supprimée<br />';
$g4p_langue['message_suppr_lien_note_echec']='Le lien avec la note n\'a pas été supprimée<br />';
$g4p_langue['message_suppr_note_succes']='La note a été supprimée<br />';
$g4p_langue['message_suppr_note_echec']='la note n\'a pas été supprimée<br />';
$g4p_langue['message_source_enr']='Source enregistrée<br />';
$g4p_langue['message_lien_source_succes']='Lien avec la source éffectuée<br />';
$g4p_langue['message_suppr_source_succes']='La source a été supprimée<br />';
$g4p_langue['message_suppr_source_echec']='la source n\'a pas été supprimée<br />';
$g4p_langue['message_suppr_event_echec']='Erreur lors de la suppression de l\'évènement<br />';
$g4p_langue['message_suppr_famille_succes']='La famille a bien été supprimé<br />';
$g4p_langue['message_suppr_famille_echec']='La famille n\'a pas été supprimé<br />';
$g4p_langue['message_creer_base_succes']='Création de la nouvelle entrée réussie<br />';
$g4p_langue['message_perm_admin_requis']='Permission d\'administrateur requise<br />';
$g4p_langue['message_email_ajout_succes']="%s a été ajouté avec succès<br />";
$g4p_langue['message_email_ajout_erreur']='Erreur lors de l\'insertion<br />';
$g4p_langue['message_perm_modif_succes']='Modification enregistrée<br />';
$g4p_langue['message_perm_modif_echec']='Modification non enregistrée<br />';
$g4p_langue['message_email_suppr_succes']='Suppression enregistrée<br />';
$g4p_langue['message_email_suppr_echec']='Suppression non enregistrée<br />';
$g4p_langue['message_perm_ajout_succes']='Nouvelle permission ajoutée<br />';
$g4p_langue['message_perm_ajout_echec']='Echec de l\'ajout d\'une nouvelle parmission<br />';
$g4p_langue['message_vide_base_succes']='Base de donnée vidée<br />';
$g4p_langue['message_vide_base_echec']='Echec de vidage de la base de donnée<br />';
$g4p_langue['message_suppr_base_succes']='Base de donnée supprimée<br />Pensez à supprimer le répertoire de cache<br />';
$g4p_langue['message_suppr_base_echec']='Echec de suppression de la base de donnée<br />';
$g4p_langue['message_recup_base_succes']='Base de donnée réparée<br />';
$g4p_langue['message_recup_base_echec']='Echec de réparation de la base de donnée<br />';
$g4p_langue['message_suppr_lien_media_succes']='Le lien avec le média a été supprimé<br />';
$g4p_langue['message_suppr_lien_media_echec']='Le lien avec le média n\'a pas été supprimé<br />';
$g4p_langue['message_suppr_media_succes']='Le média a été supprimé<br />';
$g4p_langue['message_suppr_media_echec']='Le média n\'a pas été supprimé<br />';
$g4p_langue['message_type_fichier_refuse']='Type de fichier non autorisé<br />';
$g4p_langue['message_copie_imp']='Impossible de copier le fichier<br />';
$g4p_langue['message_fichier_non_trouve']='Fichier non trouvé (taille trop importante)<br />';
$g4p_langue['message_media_enr']='Média enregistré<br />';
$g4p_langue['message_lien_media_succès']='Lien avec le média éffectué<br />';
$g4p_langue['message_repo_enr']='Nouveau dépot enregistré<br />';
$g4p_langue['message_ajout_rela_succes']='Création de la nouvelle relation réussie<br />';
$g4p_langue['message_suppr_rela_succes']='Suppression de la relation réussie<br />';
$g4p_langue['message_masque_indi']='L\'individu est désormais masqué aux annonymes<br />';
$g4p_langue['message_demasque_indi']='L\'individu est désormais visible aux annonymes<br />';

// page admin/index.php
$g4p_langue['a_index_nom']='Nom&nbsp;: ';
$g4p_langue['a_index_prenom']='Prénom&nbsp;: ';
$g4p_langue['a_index_sexe']='Sexe&nbsp;: ';
$g4p_langue['a_index_npfx']='Préfixe du prénom (NPFX)&nbsp;: ';
$g4p_langue['a_index_givn']='Nom usuel (GIVN)&nbsp;: ';
$g4p_langue['a_index_nick']='Surnom (NICK)&nbsp;: ';
$g4p_langue['a_index_spfx']='Préfixe du nom (SPFX)&nbsp;: ';
$g4p_langue['a_index_nsfx']='Suffixe (NSFX)&nbsp;: ';
$g4p_langue['a_index_ajout_ievent']='Ajouter un nouvel évènement';

$g4p_langue['a_index_modfams_titre']="Famille %1\$s - %2\$s";
$g4p_langue['a_index_modfams_conjoint1']='1<sup>er</sup> conjoint (homme)&nbsp;: ';
$g4p_langue['a_index_modfams_conjoint2']='2<sup>ème</sup> conjoint (femme)&nbsp;: ';
$g4p_langue['a_index_modfams_conjointid']='ID du nouveau conjoint&nbsp;: ';
$g4p_langue['a_index_modfams_conjoint_cherche']='Rechercher un conjoint';
$g4p_langue['a_index_ajout_ievent']='Ajouter un nouvel évènement';

$g4p_langue['a_index_modfams_enfants']='Enfant(s) issu(s) du mariage&nbsp;: ';
$g4p_langue['a_index_modfams_confirme_suppr_js']='Confirmer la suppression ?\\n ATTENTION, seul le lien est supprimé, l\\\'individu existera toujours';
$g4p_langue['a_index_modfams_newchild']='Ajouter un nouvel enfant&nbsp;: ';
$g4p_langue['a_index_modfams_enfant_recherche']='Rechercher un enfant';
$g4p_langue['a_index_modfams_aucune_famille']='Aucune famille';

$g4p_langue['a_index_ajout_note_titre']='Ajout d\'une note';
$g4p_langue['a_index_ajout_note_lier']='Lier une note déjà éxistante&nbsp;: ';
$g4p_langue['a_index_ajout_note_id']='Id de la note&nbsp; ';
$g4p_langue['a_index_ajout_note_rechercher']='Rechercher la note&nbsp; ';
$g4p_langue['a_index_ajout_note_ajout']='Nouvelle note&nbsp;: ';

$g4p_langue['a_index_mod_note_titre']='Modification d\'une note';
$g4p_langue['a_index_mod_note_inclu']='Cette note est incluse dans les éléments suivant';
$g4p_langue['a_index_mod_note_liste_indi']='Les individus&nbsp;: ';
$g4p_langue['a_index_mod_note_liste_famille']='Les familles&nbsp;: ';
$g4p_langue['a_index_mod_note_liste_event']='Les évènements&nbsp;: ';
$g4p_langue['a_index_mod_note_erreur']='Erreur! L\'enregistrement n\'existe pas, le cache a été regénéré';

$g4p_langue['a_index_ajout_source_titre']='Ajout d\'une source';
$g4p_langue['a_index_ajout_source_lier']='Lier une source déjà éxistante&nbsp;: ';
$g4p_langue['a_index_ajout_source_id']='Id de la source&nbsp;: ';
$g4p_langue['a_index_ajout_source_recherche']='Rechercher la source';
$g4p_langue['a_index_ajout_source_ajout']='Nouvelle source&nbsp;: ';
$g4p_langue['a_index_ajout_source_titre2']='titre&nbsp;: ';
$g4p_langue['a_index_ajout_source_auteur']='Auteur&nbsp;: ';
$g4p_langue['a_index_ajout_source_page']='Page&nbsp;';
$g4p_langue['a_index_ajout_source_ref']='Référence&nbsp;: ';
$g4p_langue['a_index_ajout_source_type']='Type de source&nbsp;: ';
$g4p_langue['a_index_ajout_source_texte']='Texte&nbsp;: ';
$g4p_langue['a_index_ajout_source_publ']='Publication&nbsp;: ';
$g4p_langue['a_index_ajout_source_depot']='Dépot&nbsp;: ';
$g4p_langue['a_index_ajout_source_nouveau_depor']='Ajouter un nouveau dépot d\'archive';

$g4p_langue['a_index_mod_source_titre']='Modification d\'une source';

$g4p_langue['a_index_ajout_media_titre']='Ajout d\'un document multimédia ou d\'une URL';
$g4p_langue['a_index_ajout_media_lier']='Lier un média déjà éxistant&nbsp;: ';
$g4p_langue['a_index_ajout_media_id']='Id du média&nbsp;: ';
$g4p_langue['a_index_ajout_media_recherche']='Rechercher le média';
$g4p_langue['a_index_ajout_media_nouveau_media']='Nouveau média&nbsp;: ';
$g4p_langue['a_index_ajout_media_titre2']='titre du média&nbsp;: ';
$g4p_langue['a_index_ajout_media_file']='Média&nbsp;: ';
$g4p_langue['a_index_ajout_media_url']='<strong>OU</strong> URL&nbsp;: ';

$g4p_langue['a_index_mod_event_titre']='Modification d\'un évènement';
$g4p_langue['a_index_mod_event_type']='type d\'évènement&nbsp;: ';
$g4p_langue['a_index_mod_event_type_descr']='description du type&nbsp;: ';
$g4p_langue['a_index_mod_event_descr']='Description&nbsp;: ';
$g4p_langue['a_index_mod_event_date']='Date de l\'évènement&nbsp;:';
$g4p_langue['a_index_mod_event_lieu']='Lieu&nbsp;: ';
$g4p_langue['a_index_mod_event_age']='Age au moment de l\'évènement&nbsp;: ';
$g4p_langue['a_index_mod_event_cause']='Cause de l\'évènement&nbsp;: ';

$g4p_langue['a_index_ajout_event_titre']='Ajout d\'un évènement individuel ou familial';
$g4p_langue['a_index_ajout_event_type']='type d\'évènement&nbsp;: ';
$g4p_langue['a_index_ajout_event_type_descr']='description du type&nbsp;: ';
$g4p_langue['a_index_ajout_event_descr']='Description&nbsp;: ';
$g4p_langue['a_index_ajout_event_date']='Date de l\'évènement (format JJ/MM/AAAA ou GEDCOM)&nbsp;: ';
$g4p_langue['a_index_ajout_event_lieu']='Lieu&nbsp;: ';
$g4p_langue['a_index_ajout_event_age']="Age au moment de l'évènement&nbsp;: %s (inutilisé pour un évènement familial)";
$g4p_langue['a_index_ajout_event_cause']='Cause de l\'évènement&nbsp;: ';

//$g4p_langue['a_index_ajout_fevent_titre']='Ajout d\'un évènement familial';

$g4p_langue['a_index_new_base_titre']='Ajout d\'une nouvelle base de données';
$g4p_langue['a_index_new_base_nom']='Nom&nbsp;: ';
$g4p_langue['a_index_new_base_descr']='Description&nbsp;: ';

$g4p_langue['a_index_gerer_download_titre']='Gestion des téléchargements';
$g4p_langue['a_index_gerer_download_message']='<i>Utilisez la fonction de création des fichiers GEDCOMS et PDF uniquement en local si votre base contient de nombreuses personnes<br />
      La création de ces fichiers demandent beaucoup de ressources.<br />
      Copier les fichiers des répertoires caches/gedcoms/ et cache/pdf/ sur votre serveur pour mettre à jour les fichiers.
      </i>';
$g4p_langue['a_index_gerer_download_base']="Base : %s";
$g4p_langue['a_index_gerer_download_date_ged']="Fichier GEDCOM : %s";
$g4p_langue['a_index_gerer_download_recreer']='Recréer le fichier';
$g4p_langue['a_index_gerer_download_inconnu']='Fichier inexistant - ';
$g4p_langue['a_index_gerer_download_creer']='Créer le fichier';
$g4p_langue['a_index_gerer_download_date_pdf']="Fichier PDF : %s";

$g4p_langue['a_index_gerer_perm_titre']='Gestions des membres et des autorisations';
$g4p_langue['a_index_gerer_perm_ajout_membre']='Ajout d\'un nouveau membre&nbsp;: ';
$g4p_langue['a_index_gerer_perm_email']='Email&nbsp;: ';
$g4p_langue['a_index_gerer_perm_pass']='mot de passe&nbsp;: ';
$g4p_langue['a_index_gerer_perm_gest_perm']='Gestion des permissions&nbsp;: ';
$g4p_langue['a_index_gerer_perm_edit_perm']='Editer les permissions de&nbsp;: ';
$g4p_langue['a_index_gerer_perm_type_perm']='Type de permission&nbsp;: ';
$g4p_langue['a_index_gerer_perm_valeur']='Valeur';
$g4p_langue['a_index_gerer_perm_ajout_perm']='Nouvelle permission&nbsp;: ';
$g4p_langue['a_index_gerer_perm_nom_base']='Nom de la base&nbsp;: ';
$g4p_langue['a_index_gerer_perm_perm']="Permission&nbsp;: %s (typiquement 1 pour oui, 0 pour non ou l\'id de la personne pour la masquer)";

$g4p_langue['a_index_mod_perm_titre']='Modifier une permission';
$g4p_langue['a_index_mod_perm_nom_base']='nom de la base&nbsp;: ';
$g4p_langue['a_index_mod_perm_type_perm']='type de permission&nbsp;: ';
$g4p_langue['a_index_mod_perm_perm']="Permission&nbsp;: %s (typiquement 1 pour oui, 0 pour non ou l\'id de la personne pour la masquer)";

$g4p_langue['a_index_vide_base_titre']='Vider une base';
$g4p_langue['a_index_vide_base_message']='Supprimer les fichiers situés dans le répertoire cache de la base correspondante';
$g4p_langue['a_index_vide_base_nom_base']='nom de la base&nbsp;: ';

$g4p_langue['a_index_suppr_base']='Supprimer une base';
$g4p_langue['a_index_suppr_base_message']='Supprimer le répertoire cache/nom_de_la_base';
$g4p_langue['a_index_suppr_base_nom_base']='nom de la base&nbsp;: ';

$g4p_langue['a_index_recup_crash_titre']='Récupération en cas d\'erreurs lors d\'un import GEDCOM';
$g4p_langue['a_index_recup_crash_message']='Permet la suppression des éléments nouvellement insérés et les colonnes temporaires';

$g4p_langue['a_index_ajout_media_titre']='Ajout d\'un document multimédia ou d\'une URL';
$g4p_langue['a_index_ajout_media_lier']='Lier un média déjà éxistant&nbsp;: ';
$g4p_langue['a_index_ajout_media_id']='Id du média&nbsp;: ';
$g4p_langue['a_index_ajout_media_recherche']='Rechercher le média';
$g4p_langue['a_index_ajout_media_nouveau']='Nouveau média&nbsp;: ';
$g4p_langue['a_index_ajout_media_titre_media']='titre du média&nbsp;: ';
$g4p_langue['a_index_ajout_media_media']='Média&nbsp;: ';
$g4p_langue['a_index_ajout_media_url']='<strong>OU</strong> URL&nbsp;: ';

$g4p_langue['a_index_mod_multimedia_titre']='Modification d\'un média';
$g4p_langue['a_index_mod_multimedia_lien']='Ce média est inclus dans les éléments suivant&nbsp;: ';
$g4p_langue['a_index_mod_multimedia_lien_indi']='Les individus&nbsp;: ';
$g4p_langue['a_index_mod_multimedia_lien_famille']='Les familles&nbsp;: ';
$g4p_langue['a_index_mod_multimedia_lien_event']='Les évènements&nbsp;: ';
$g4p_langue['a_index_mod_multimedia_file_url']='Fichier ou url&nbsp;: ';
$g4p_langue['a_index_mod_multimedia_new_file']='Importer un nouveau fichier&nbsp;: ';
$g4p_langue['a_index_mod_multimedia_erreur']='Erreur! L\'enregistrement n\'existe pas, le cache a été regénéré';

$g4p_langue['a_index_ajout_depot_titre']='Ajout d\'un dépot d\'archives';
$g4p_langue['a_index_ajout_depot_nom']='Nom&nbsp;: ';
$g4p_langue['a_index_ajout_depot_adresse']='Adresse&nbsp;: ';
$g4p_langue['a_index_ajout_depot_ville']='Ville&nbsp;: ';
$g4p_langue['a_index_ajout_depot_cp']='Code postal&nbsp;: ';
$g4p_langue['a_index_ajout_depot_pays']='Pays&nbsp;: ';
$g4p_langue['a_index_ajout_depot_tel']='Téléphone ';

$g4p_langue['a_index_ajout_assoc_titre']='Ajout d\'une nouvelle relation';
$g4p_langue['a_index_ajout_assoc_id']='Id de l\'individu&nbsp;: ';
$g4p_langue['a_index_ajout_assoc_descr']='Description&nbsp;: ';
$g4p_langue['a_index_ajout_assoc_recherche']='Rechercher l\'individu';

//fichier profil.php
$g4p_langue['profil_titre']='Modification des préférences';
$g4p_langue['profil_annonyme']='Vous n\'êtes pas un utilisateur enregistré';
$g4p_langue['profil_membre']="Vous êtes enregistré sous l'email %s";
$g4p_langue['profil_langue']='Choisissez votre langue&nbsp;: ';

//g4p_class.php
$g4p_langue['class_chan']='inconnue'; //ajouté le 12/01/2005 19h03

//erreur, nom de fichier inconnu
$g4p_langue['a_index_affiche_media_mod']='Modifier l\'objet';
$g4p_langue['a_index_affiche_media_suppr']='Supprimer l\'objet';
//




//LISTE DES CORRESPONDANCE GEDCOM - langage courant
$g4p_tag_def=array(
  'ABBR'=>'Titre, decription...',
  'ADDR'=>'Adresse',
  'ADOP'=>'Adoption',
  'AGE'=>'Age',
  'AGNC'=>'Entreprise ou Institution',
  'ANUL'=>'Déclaration de nullité d\'un mariage',
  'ASSO'=>'Relation',
  'AUTH'=>'Auteur qui a relevés les informations',
  'BAPM'=>'Baptème',
  'BIRT'=>'Naissance',
  'BURI'=>'Inhumation',
  'CAST'=>'Rang ou status',
  'CAUS'=>'Cause d\'un évènement',
  'CHIL'=>'Enfant',
  'CHR'=>'Baptème religieux',
  'CHRA'=>'Baptème religieux (Adulte)',
  'CONF'=>'Confirmation',
  'FCOM'=>'Première communion',
  'CHIL'=>'Enfant',
  'CREM'=>'Incinération',
  'DEAT'=>'Décès',
  'DIV'=>'Divorce',
  'ENGA'=>'Fiançaille',
  'MARR'=>'Mariage',
  'OCCU'=>'Profession',
  'ORDN'=>'Ordination',
  'NATU'=>'Naturalisation',
  'EMIG'=>'Emigration',
  'IMMI'=>'Immigration',
  'CENS'=>'Resencement',
  'DIVF'=>'Dossier de divorce',
  'MARB'=>'Publication des bans de mariage',
  'MARC'=>'Contrat de mariage',
  'MARL'=>'Autorisation de mariage',
  'MARS'=>'Contrat avant mariage',
  'PROB'=>'Validation d\'un testament',
  'WILL'=>'Testament',
  'BLES'=>'Bénédiction',
  'BARM'=>'BAR MITZVAH (garçon)',
  'BASM'=>'BAR MITZVAH (fille)',
  'GRAD'=>'Diplôme',
  'RETI'=>'Retraite',
  'DSCR'=>'Description',
  'EDUC'=>'Niveau d\'instruction',
  'IDNO'=>'Matricule/numéro',
  'NATI'=>'Nationalité',
  'PROP'=>'Possession',
  'RELI'=>'Religion',
  'TITL'=>'Titre',
  'EVEN'=>'Evènement',
  'BAPL'=>'Baptème mormons'
);
array_multisort($g4p_tag_def);

$g4p_date_modif=array(
  '/EST /i'=>'estimée ',
  '/ABT /i'=>'environ ',
  '/BEF /i'=>'avant ',
  '/AFT /i'=>'après ',
  '/BET /i'=>'entre ',
  '/ AND /i'=>' et ',
  '/ TO /i'=>' à ',
  '/FROM /i'=>'depuis ',
  '/CAL /i'=>'calculée ',
  '/INT /i'=>''
);

$g4p_lien_def=array(
  'BIRTH'=>'légitimes',
  'adopted'=>'Adoptifs'
);

$g4p_source_media_type=array(
  'audio'=>'Audio',
  'book'=>'Livre',
  'card'=>'"Card"',
  'electronic'=>'Electronique',
  'fiche'=>'Fiche ?',
  'film'=>'Film',
  'magazine'=>'magasine',
  'manuscript'=>'Manuscrit',
  'map'=>'Carte',
  'newspaper'=>'Journal',
  'photo'=>'Photo',
  'tombstone'=>'Pierre tombale',
  'video'=>'Video',
  'microfilm'=>'Microfilm'// non standard, je crois
);
asort($g4p_source_media_type);

// traductions GEDCOMS
$date1=array(
  '/JAN/'=>'Janvier',
  '/FEB/'=>'Février',
  '/MAR/'=>'Mars',
  '/APR/'=>'Avril',
  '/MAY/'=>'Mai',
  '/JUN/'=>'Juin',
  '/JUL/'=>'Juillet',
  '/AUG/'=>'Août',
  '/SEP/'=>'Septembre',
  '/OCT/'=>'Octobre',
  '/NOV/'=>'Novembre',
  '/DEC/'=>'Décembre',
  '/VEND/' => 'vendemiaire',
  '/BRUM/' =>'brumaire',
  '/FRIM/' => 'frimaire',
  '/NIVO/' => 'nivose',
  '/PLUV/' => 'pluviose',
  '/VENT/' => 'ventose',
  '/GERM/' => 'germinal',
  '/FLOR/' => 'floreal',
  '/PRAI/' => 'prairial',
  '/MESS/' => 'messidor',
  '/THER/' => 'thermidor',
  '/FRUC/' => 'fructidor',
  '/COMP/' => 'jours complémentaires',
  '/TSH/' => 'Tishri',
  '/CSH/' => 'Cheshvan',
  '/KSL/' => 'Kislev',
  '/TVT/' => 'Tevet',
  '/SHV/' => 'Shevat',
  '/ADR/' => 'Adar',
  '/ADS/' => 'Adar Sheni',
  '/NSN/' => 'Nisan',
  '/IYR/' => 'Iyar',
  '/SVN/' => 'Sivan',
  '/TMZ/' => 'Tammuz',
  '/AAV/' => 'Av',
  '/ELL/' => 'Elul');

$g4p_liste_calendrier=array(
'@#DHEBREW@'=>'Hébreu',
'@#DROMAN@'=>'Roman',
'@#DFRENCH R@'=>'Révolutionnaire',
'@#DGREGORIAN@'=>'Grégorien',
'@#DJULIAN@'=>'Julien',
'@#DUNKNOWN@'=>'Inconnu'
);

$g4p_jours_gregorien=array(
  ''=>'',
  1=>'Lundi',
  2=>'Mardi',
  3=>'Mercredi',
  4=>'Jeudi',
  5=>'Vendredi',
  6=>'Samedi',
  0=>'Dimanche');

$g4p_mois_gregorien=array(
  ''=>'',
  1=>'Janvier',
  2=>'Février',
  3=>'Mars',
  4=>'Avril',
  5=>'Mai',
  6=>'Juin',
  7=>'Juillet',
  8=>'Août',
  9=>'Septembre',
  10=>'Octobre',
  11=>'Novembre',
  12=>'Décembre');

$g4p_jours_revolutionnaire=array(
  ''=>'',
  1=>'Primidi',
  2=>'Duodi',
  3=>'Tridi',
  4=>'Quartidi',
  5=>'Quintidi',
  6=>'Sextidi',
  7=>'Septidi',
  8=>'Octidi',
  9=>'Nonidi',
  10=>'Decadi');

$g4p_mois_revolutionnaire=array(
  ''=>'',
  1 => 'vendemiaire',
  2 =>'brumaire',
  3 => 'frimaire',
  4 => 'nivose',
  5 => 'pluviose',
  6 => 'ventose',
  7 => 'germinal',
  8 => 'floreal',
  9 => 'prairial',
  10 => 'messidor',
  11 => 'thermidor',
  12 => 'fructidor',
  13 => 'jours complémentaires');

$g4p_jours_juif=array(
  ''=>'',
  1=>'ב',
  2=>'ג',
  3=>'ד',
  4=>'ה',
  5=>'ו',
  6=>'שבת',
  0=>'א');

$g4p_mois_juif=array(
  ''=>'',
  1 => 'Tishri',
  2 => 'Cheshvan',
  3 => 'Kislev',
  4 => 'Tevet',
  5 => 'Shevat',
  6 => 'Adar',
  7 => 'Adar Sheni',
  8 => 'Nisan',
  9 => 'Iyar',
  10 => 'Sivan',
  11 => 'Tammuz',
  12 => 'Av',
  13 => 'Elul');
  

?>
