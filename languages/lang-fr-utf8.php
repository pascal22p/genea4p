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
 * dernière mise à jour : 24/01/2005 - pascal                              *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

//traductions générales
$g4p_langue[_PERM_DOWNLOAD_]='Autoriser le téléchargement de rapports et GEDCOMS'; //modifié le 02/04/2005
$g4p_langue[_PERM_GEDCOM_]='Autoriser l\'export GEDCOM'; //ajouter le 02/04/2005
$g4p_langue[_PERM_PDF_]='Autoriser l\'export PDF'; //ajouter le 02/04/2005
$g4p_langue[_PERM_DOT_]='Autoriser l\'export DOT'; //ajouter le 02/04/2005
$g4p_langue[_PERM_NOTE_]='voir les notes';          
$g4p_langue[_PERM_SOURCE_]='voir les sources';      
$g4p_langue[_PERM_EDIT_FILES_]='modifier les fiches généalogiques';
$g4p_langue[_PERM_SUPPR_FILES_]='Supression';
$g4p_langue[_PERM_ADMIN_]='Permission d\'administrateur';
$g4p_langue[_PERM_SUPER_ADMIN_]='Permission de super-administrateur'; // ajouté le 27/02/2005
$g4p_langue[_PERM_AFF_DATE_]='Afficher les dates de moins de 100 ans';
$g4p_langue[_PERM_MASK_DATABASE_]='Masquer la base de donnée';
$g4p_langue[_PERM_MASK_INDI_]='Masquer la personne';
$g4p_langue[_PERM_MULTIMEDIA_]='Voir les documents et références externes';

$g4p_langue['acces_non_autorise']='Vous n\'êtes pas autorisé à visualiser ces informations';
$g4p_langue['acces_admin']='Permission d\'administrateur requise';
$g4p_langue['acces_login_pass_incorrect']='Login et/ou mot de passe incorrect';

$g4p_langue['db_select_base_impossible']='Impossible de sélectionner la base de données';
$g4p_langue['db_conection_impossible']='Impossible de se connecter au serveur de base de données';
$g4p_langue['db_erreur_requete']='Erreur pendant l\'éxécution de la requète';

$g4p_langue['submit_connection']=' Se connecter ';
$g4p_langue['submit_enregistrer']=' Enregistrer ';
$g4p_langue['submit_ajouter']=' Ajouter ';
$g4p_langue['submit_modifier']=' Modifier ';
$g4p_langue['submit_editer']=' Editer ';
$g4p_langue['submit_vider']=' Vider ';
$g4p_langue['submit_restaurer']=' Restaurer ';
$g4p_langue['submit_rechercher']=' Rechercher '; //ajouté le 15/01/2005

$g4p_langue['id_inconnu']='Aucun id n\'est défini';
$g4p_langue['retour']='[Retour]';
$g4p_langue['detail']='[détail]';
$g4p_langue['Supprimer']='Supprimer';
$g4p_langue['date_cache']='-Date cachée-';


//menus
$g4p_langue['menu_mod_event']='Modifier l\'événement';
$g4p_langue['menu_del_event']='Supprimer l\'événement';
$g4p_langue['menu_mod_note']='Modifier la note';
$g4p_langue['menu_mod_source']='Modifier la source';
$g4p_langue['menu_ajout_note']='Ajouter une note';
$g4p_langue['menu_ajout_source']='Ajouter une source';
$g4p_langue['menu_ajout_media']='Ajouter un média';
$g4p_langue['menu_ajout_relation']='Ajouter une relation';
$g4p_langue['menu_sppr_indi']='Supprimer l\'individu';
$g4p_langue['menu_sppr_fam']='Supprimer la famille';
$g4p_langue['menu_sppr_confirme']='Confirmer la suppression ?'; //Jscript
$g4p_langue['menu_suppr_source']='Supprimer la source'; //ajouté le 12/01/2005 19h03
$g4p_langue['menu_modif_fiche']='Modifier la Fiche';
$g4p_langue['menu_masque_fiche']='Masquer la Fiche';
$g4p_langue['menu_demasque_fiche']='Démasquer la Fiche';
$g4p_langue['menu_recreer_cache']='Recréer le cache';
$g4p_langue['menu_mod_famille']='Modifier la famille'; //nouvelle ligne 11/05/2005 19h20

//page menu.php
$g4p_langue['menu_sommaire']='Sommaire';
$g4p_langue['menu_liste_base']='Liste des bases';
$g4p_langue['menu_patronyme']='Patronymes';
$g4p_langue['menu_connexion']='Se connecter';
$g4p_langue['menu_deconnexion']='Se déconnecter';
$g4p_langue['menu_aide_saisie']='Aide à la saisie';
$g4p_langue['menu_tags']='Explicat° des tags';
$g4p_langue['menu_stats']='Statistiques';
$g4p_langue['menu_rechercher']='Rechercher';
$g4p_langue['menu_profil']='Mon profil';
$g4p_langue['menu_creer']='Créer';
$g4p_langue['menu_creer_indi']='Un individu';
$g4p_langue['menu_creer_famille']='Une famille';
$g4p_langue['menu_creer_depot']='Un dépot';
$g4p_langue['menu_creer_lieu']='Un lieu';//ajouté le 16/08/2005
$g4p_langue['menu_historic']='Historique';
$g4p_langue['menu_voir']='Voir';
$g4p_langue['menu_voir_arbre_asc']='Arbre ascendant';
$g4p_langue['menu_voir_arbre_ascg']='Arbre d\'ascendance graphique';//syl:arbre_ascendance //ajouté le 04/05/2005
$g4p_langue['menu_voir_liste_desc']='Liste de descendance';
$g4p_langue['menu_voir_liste_asc']='Liste d\'ascendance';
$g4p_langue['menu_voir_cartographie']='Cartographie';//ajouté le 16/08/2005
$g4p_langue['menu_telecharger']='Télécharger';
$g4p_langue['menu_telecharger_rapports']='Gedcoms et rapports'; //ajouté le 16/08/2005
$g4p_langue['menu_telecharger_arbre_asc_pdf']='Arbre ascendant (PDF)'; //ajouté le 16/08/2005
$g4p_langue['menu_telecharger_gedc']='GEDCOM complet';
$g4p_langue['menu_telecharger_pdfc']='Dossier PDF';
$g4p_langue['menu_telecharger_gedcom']='GEDCOM asc/desc'; //modifié le 13/03/2005
$g4p_langue['menu_telecharger_pdfi']='Fiche individuelle (PDF)';
$g4p_langue['menu_telecharger_dot']='Export Arbre';
$g4p_langue['menu_admin']='Administration';
$g4p_langue['menu_admin_panel']='Panneau d\'admin.';//ajouté le 16/08/2005
$g4p_langue['menu_admin_import_ged']='Import GEDCOM';
$g4p_langue['menu_admin_import_dorotree']='Import dorotree';
$g4p_langue['menu_admin_index']='Créer index des noms';
$g4p_langue['menu_admin_ajout_base']='Ajouter une base';
$g4p_langue['menu_admin_gere_telechargement']='GEDCOM et rapports';
$g4p_langue['menu_admin_gere_perm']='Gérer les permissions';
$g4p_langue['menu_admin_vide_base']='Vider une base';
$g4p_langue['menu_admin_vide_cache']='Vider le cache'; //ajouté le 27/02/2005
$g4p_langue['menu_admin_suppr_base']='Supprimer une base';
$g4p_langue['menu_admin_crash']='Récup. crash import';
$g4p_langue['menu_famille_proche']='Famille proche'; //ajouter le 11/10/2005 à 23h37
$g4p_langue['menu_fiche']='Fiche individuelle'; //ajouter le 11/10/2005 à 23h37

//traduction de la page d'accueil (accueil.php) ajouter le 11/10/2005
$g4p_langue['accueil_titre']='Liste des bases de données';
$g4p_langue['accueil_renseignements']='';

//pour les dates, chaine de strftime et localisation
$g4p_langue['date_setlocale']='fr_FR'; //tableau pour setlocale, ne pas supprimer, il sert dans d'autres pages.
//setlocale(LC_ALL, $g4p_langue['date_setlocale']);
$g4p_langue['entete_content_language']='fr';
$g4p_langue['entete_charset']='UTF-8';
// j'ai du faire une fonction propre pour strftime, c'est la même syntaxe mais tous les masques ne sont pas supportés
// Envoyer moi un mail pour rajouter un masque.
$g4p_langue['date_complete']="%A %d %B %Y à %H:%M";
$g4p_langue['date']="%A %d %B %Y";

//entetes (entete.php)
$g4p_langue['entete_titre']='genea4p';
//$g4p_langue['entete_content_language'] voir rubrique date et localisation
//$g4p_langue['entete_charset'] idem
$g4p_langue['entete_description']='';
$g4p_langue['entete_mots_cles']='genealogie, genealogy, genea4p, php';
$g4p_langue['entete_grand_titre']="Famille %s";

//page connect.php
$g4p_langue['connect_titre']='Veuillez vous identifier&nbsp;: ';
$g4p_langue['connect_mail']='Votre adresse e-mail&nbsp;: ';
$g4p_langue['connect_pass']='Votre mot de passe&nbsp;: ';
$g4p_langue['connect_bienvenue']='Bonjour, vous êtes connecté(e)';
$g4p_langue['connect_cookie']='Sauvegarder les infos dans un cookie ';

//page detail_f.php et detail_i.php
$g4p_langue['detail_titre_famille']="Famille %1\$s - %2\$s";
$g4p_langue['detail_titre_individu']="%1\$s %2\$s";
$g4p_langue['detail_type_event']='type d\'événement&nbsp;: ';
$g4p_langue['detail_description']='Description&nbsp;: ';
$g4p_langue['detail_date_event']='Date de l\'événement&nbsp;: ';
$g4p_langue['detail_lieu']='Lieu&nbsp;: ';
$g4p_langue['detail_cause']='Cause de l\'événement&nbsp;: ';
$g4p_langue['detail_cal_gregorien']='Calendrier grégorien&nbsp;: ';
$g4p_langue['detail_cal_revolutionnaire']='Calendrier révolutionnaire&nbsp;: ';
$g4p_langue['detail_cal_juif']='Calendrier juif&nbsp;: ';
$g4p_langue['detail_cal_julien']='Calendrier julien&nbsp;: ';
$g4p_langue['detail_age']='Age au moment de l\'événement&nbsp;:';

//page download.php
$g4p_langue['download_titre']='Liste des fichiers disponibles&nbsp;: ';
$g4p_langue['download_gedcom']='Fichiers GEDCOM&nbsp;: ';
$g4p_langue['download_link_ged']="<i>%1\$s.zip - généré le : %2\$s - (%3\$s ko)</i>";
$g4p_langue['download_link_pdf']="<i>%1\$s.pdf - généré le : %2\$s - (%3\$s ko)</i>";

//page export_dot.php
$g4p_langue['dot_dot_non_present']="Impossible de generer le format demandé. "
        . "L'utilitaire DOT n'est pas installé ou mal configuré. "
        . "Contactez votre administrateur";
$g4p_langue['dot_ligne']='Ligne de commande&nbsp;:';
$g4p_langue['dot_titre']='Génération au format DOT';
$g4p_langue['dot_sstitre_info']='Information';
$g4p_langue['dot_submit_id']=' Mettre a jour ';
$g4p_langue['dot_sstitre_config']='Configuration';
$g4p_langue['dot_type_extract']='Type d\'extraction&nbsp;: ';
$g4p_langue['dot_arbre_asc']='Arbre des ascendants';
$g4p_langue['dot_arbre_desc']='Arbre des descendants';
$g4p_langue['dot_arbre_asc_desc']='Arbre des ascendants et des descendants';
$g4p_langue['dot_arbre_complet']='Arbre complet de la famille elargie';
$g4p_langue['dot_date_event']='Affiche la date des evenements';
$g4p_langue['dot_lieu_event']='Affiche le lieu des evenements';
$g4p_langue['dot_adresse']='Affiche l\'adresse des individus';
$g4p_langue['dot_limit_gen']="La limite de génération est de : %s (ascendance + descendance)"; //ajouté le 30/01/2005
$g4p_langue['dot_max_asc']='Profondeur maximum de l\'extraction coté ascendance&nbsp;:';
$g4p_langue['dot_max_desc']='Profondeur maximum de l\'extraction coté descendance&nbsp;:';
$g4p_langue['dot_nolimit']='ne pas utiliser de limite&nbsp;?';
$g4p_langue['dot_format']='Format du fichier de sortie&nbsp;: ';
$g4p_langue['dot_divers']='Divers&nbsp;: ';
$g4p_langue['dot_visualiser']='Visualiser';
$g4p_langue['dot_telecharger']='Télécharger';
$g4p_langue['dot_submit_executer']=' Executer ';
$g4p_langue['dot_erreur_id']="Impossible de trouver une racine. " . "<br/>"
    . "Séléctionnez une personne, puis recommancez l'opération. ";

//page famille_proche.php
$g4p_langue['fproche_description']='description : ';
$g4p_langue['fproche_lieu']='lieu : ';
$g4p_langue['fproche_parent_inconnu']='Parent inconnu';
$g4p_langue['fproche_freres_soeurs']='Frères et soeurs : ';
$g4p_langue['fproche_conjoint_inc']='Conjoint inconnu(e)';
$g4p_langue['fproche_marie']='marié(e) à : ';

//page import_gedcom
$g4p_langue['import_ged_titre']='Importer les données d\'un fichier GEDCOM';
$g4p_langue['import_ged_explications']='<i>Si des données éxistent déjà, elles ne seront pas remplacées<br />
    Les fichiers GEDCOMS doivent être placés dans le répertoire gedcoms.<br /><br /></i>';
$g4p_langue['import_ged_submit']=' Charger le gedcom ';

$g4p_langue['dorotree_ged_titre']='Importer GEDCOM hébreu depuis dorotree';
$g4p_langue['import_ged_reussi']="Durée de l'importation : %1\$ss, nombre de requètes : %2\$s";
$g4p_langue['import_ged_lieu_explications']="Fa&icirc;tes correspondre
chaque colonne avec sa signification"; //ajouté le 27/05/2005
$g4p_langue['import_ged_lieu_submit']="Convertir le fichier"; //ajouté le 27/05/2005
$g4p_langue['lieu_ignore']='Ignorer';

//page import_gns
$g4p_langue['import_gns_titre']='Importer une base de lieux GNS';
$g4p_langue['import_gns_pays_undefined']='Le pays de la base à télécharger n\'est pas défini';
$g4p_langue['import_gns_fichier_non_valide']='Fichier non valide';
$g4p_langue['import_gns_import_succes']='Importation effectuée avec succès en';
$g4p_langue['import_gns_millisecondes']='ms';
$g4p_langue['import_gns_import_nb_lieux']='Nombre de lieux ajoutés :';
$g4p_langue['import_gns_ftp_impossible']='Vous ne pouvez pas télécharger le fichier automatiquement.';
$g4p_langue['import_gns_download_impossible']='Il est impossible de télécharger le fichier à partir du ftp du serveur de GEOnet Names Server.';
$g4p_langue['import_gns_erreur_ftpget']='Le fichier n\'a pas pu etre téléchargé';
$g4p_langue['import_gns_erreur_unzip']='Le fichier n\'a pas pu etre téléchargé';
$g4p_langue['import_gns_cc_unknown']='Le fichier n\'existe pas sur le serveur GEOnet Names Server.<br/>Si vous avez selectionner les Etats-Unis, ils ne sont pas encore géré par la recherche GNS';
$g4p_langue['import_gns_aff_dezip']='Décompression du fichier';
$g4p_langue['import_gns_aff_recup']='Recupération des données';
$g4p_langue['import_gns_aff_download']='Téléchargement du fichier';
$g4p_langue['import_gns_aff_effectue']='effectué avec succès.';
$g4p_langue['import_gns_aff_non_effectue']='non effectué';
$g4p_langue['import_gns_reessayer']='Réessayer';
$g4p_langue['import_gns_help_manuel'][0]='Pour récupérer manuellement le fichier correspondant au pays,<br/>télécharger le';
$g4p_langue['import_gns_base_help_manuel']='Pour récupérer manuellement le fichier des codes FIPS,<br/>télécharger le';
$g4p_langue['import_gns_help_manuel'][1]='par ftp';
$g4p_langue['import_gns_help_manuel'][2]='ou';
$g4p_langue['import_gns_help_manuel'][3]='par http';
$g4p_langue['import_gns_help_manuel'][4]='et mettez le fichier décompréssé dans le répertoire';
$g4p_langue['import_gns_poursuivre']='Poursuivre l\'importation';

//page import_base_gns
$g4p_langue['import_base_gns_titre']='Importer les codes FIPS pour GNS';
$g4p_langue['import_base_gns_download_impossible']='Il est impossible de télécharger le fichier à partir du ftp du serveur de GEOnet Names Server.';
$g4p_langue['import_gns_base_fichier_introuvable_serveur']='Le fichier n\'existe pas sur le serveur GEOnet Names Server.';
$g4p_langue['import_gns_base_fichier_erreur_open']='Impossible d\'ouvrir le fichier';
$g4p_langue['import_gns_base_aff_nb_fips']='Nombre de références FIPS ajoutés :';

//page index.php
$g4p_langue['index_person_inconnu']='Personne inconue dans cette base';
$g4p_langue['index_masquer']='Personne masquée aux annonymes';
$g4p_langue['index_chan']="Dernière modification&nbsp;: %s";
$g4p_langue['index_nom']='Nom&nbsp;: ';
$g4p_langue['index_prenom']='Prénom&nbsp;: ';
$g4p_langue['index_sexe']='Sexe&nbsp;: ';
$g4p_langue['index_sexe_valeur']['M']='Masculin';  //ajouté le 12/01/2005 à 19h29
$g4p_langue['index_sexe_valeur']['F']='Féminin';  //ajouté le 12/01/2005 à 19h29
$g4p_langue['index_sexe_valeur']['I']='Inconnu';  //ajouté le 28/04/2005 à 21h31
$g4p_langue['index_npfx']='Préfixe du prénom (NPFX)&nbsp;: ';
$g4p_langue['index_givn']='Nom usuel (GIVN)&nbsp;: ';
$g4p_langue['index_nick']='Surnom (NICK)&nbsp;: ';
$g4p_langue['index_spfx']='Préfixe du nom (SPFX)&nbsp;: ';
$g4p_langue['index_nsfx']='Suffixe (NSFX)&nbsp;: ';
$g4p_langue['index_description']='Description&nbsp;: ';
$g4p_langue['index_lieu']='Lieu&nbsp;: ';
$g4p_langue['index_ype_parent']='Type de parents&nbsp;: ';
$g4p_langue['index_parent_inconnu']='Parent inconnu';
$g4p_langue['index_indi_nconnu']='Personne inconnue ou masquée';

//liste prenoms
$g4p_langue['index_prenoms_page']='Pages&nbsp;: '; //ajouté le 15/01/2005
$g4p_langue['index_prenoms_non_trouve']='<h2>Aucune personnes trouvée</h2>'; //ajouté le 16/08/2005

//liste patronyme
$g4p_langue['liste_patro_acces_interdis']='Vous n\'avez pas accès à cette base de donnée'; //ajouté le 16/08/2005


//syl liste des conjoint(s) ou celibataire sur chaque prenom //ajouté le 04/05/2005
$g4p_langue['conjoint']='Conjoint : ';
$g4p_langue['conjoints']='Conjoints : ';
$g4p_langue['celibataire']='Cébataire ou conjoint non connu';
$g4p_langue['personnes_trouvees']='Personnes trouvées';
$g4p_langue['personnes_page']='Personnes par page';

//page liste_ascendance.php
$g4p_langue['liste_asc_max_pers']="Plus de %s personnes présentes!! L'ascendance présentée est incomplète";
$g4p_langue['liste_asc_max_cache']="Protection contre un surnombre de requètes activée, l'ascendancee présenté est incomplète";
$g4p_langue['liste_asc_max_gen']='Limite de génération atteinte';
$g4p_langue['liste_asc_titre']="Ascendance de&nbsp;: %1\$s %2\$s";
$g4p_langue['liste_asc_nb_gen']='Nombre de générations à développer&nbsp;: ';
$g4p_langue['liste_asc_recherche_implexe']='Rechercher les implexes&nbsp;: ';
$g4p_langue['liste_asc_nb_pers_implexe']="<b>Génération&nbsp;: %1\$s</b>, %2\$s personne(s) dont %3\$s unique(s)";
$g4p_langue['liste_asc_nb_pers']="<b>Génération&nbsp;: %1\$s</b>, %2\$s personne(s)";
$g4p_langue['liste_asc_info_sosa']='Le numéro de SOSA est affichée en gras';
$g4p_langue['liste_asc_implexe']='Implexe, SOSA équivalents&nbsp;: ';
$g4p_langue['liste_asc_nb_asc_total_implexe']="%1\$s Ascendants dont %2\$s uniques";
$g4p_langue['liste_asc_nb_asc_total']="%1\$s Ascendants";

//page liste_descendance
$g4p_langue['liste_desc_max_pers']="Plus de %s personnes présentes!!";
$g4p_langue['liste_desc_max_cache']='Protection contre un surnombre de requètes activée';
$g4p_langue['liste_desc_inconnu']='Inconnu(e)';
$g4p_langue['liste_desc_marie_avec']="- Marié(e) avec&nbsp;: %s";
$g4p_langue['liste_desc_max_gen']="Limite de génération atteinte, %sème génération";
$g4p_langue['liste_desc_titre']="Descendance de : %1\$s %2\$s";
$g4p_langue['liste_desc_nb_gen']='Nombre de générations à développer&nbsp;: ';
$g4p_langue['liste_desc_nb_desc_total']="%s Descendants";

//page recherche.php
$g4p_langue['recherche_titre']="Recherches dans la base : %s";
$g4p_langue['recherche_rechercher']='Rechercher&nbsp;: ';
$g4p_langue['recherche_indi']='un individu';
$g4p_langue['recherche_note']='une note';
$g4p_langue['recherche_source']='une source';
$g4p_langue['recherche_media']='un média';
$g4p_langue['recherche_event']='un événement';
$g4p_langue['recherche_avance']='Recherche avancée';
$g4p_langue['recherche_depot']='un dépot';
$g4p_langue['recherche_nom']='Nom&nbsp;: ';
$g4p_langue['recherche_prenom']='Prénom&nbsp;: ';
$g4p_langue['recherche_indi_result']="Nombre de résultats : %s (la recherche est limitée à 200)";
$g4p_langue['recherche_indi_select']='Sélectionner l\'individu';
$g4p_langue['recherche_note_text']='texte&nbsp;: ';
$g4p_langue['recherche_note_parent']='Elément parent de la note&nbsp;: ';
$g4p_langue['recherche_note_orphelines']='Orphelines';
$g4p_langue['recherche_note_tous']='Tous';
$g4p_langue['recherche_note_indi']='Individu';
$g4p_langue['recherche_note_event']='Evènement';
$g4p_langue['recherche_note_familel']='Famille';
$g4p_langue['recherche_note_rechercher']=' Rechercher ';
$g4p_langue['recherche_note_num_note']='Note numéro&nbsp;: ';
$g4p_langue['recherche_note_chan']='dernière modification&nbsp;: ';
$g4p_langue['recherche_note_select_note']='Sélectionner la note';
$g4p_langue['recherche_note_note_de']='Note de&nbsp;: ';
$g4p_langue['recherche_note_noteevent']="Note de l'événement&nbsp;: %1\$s, %2\$s, à %3\$s";
$g4p_langue['recherche_note_notefamille']='Note de la famille&nbsp;: ';

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
$g4p_langue['recherche_sour_soureven']="Source de l'événement&nbsp; %1\$s, le %2\$s, à %3\$s";
$g4p_langue['recherche_sour_sourfamille']='Source de la famille&nbsp;: ';

$g4p_langue['recherche_obje_titre']='Titre&nbsp;: ';
$g4p_langue['recherche_obje_parent']='Elément parent du média&nbsp;: ';
$g4p_langue['recherche_obje_orphelines']='Orphelins';
$g4p_langue['recherche_obje_tous']='Tous';
$g4p_langue['recherche_obje_indi']='Individu';
$g4p_langue['recherche_obje_event']='Evènement';
$g4p_langue['recherche_obje_famile']='Famille';
$g4p_langue['recherche_obje_rechercher']=' Rechercher ';
$g4p_langue['recherche_obje_num_obje']='Média numéro&nbsp;: ';
$g4p_langue['recherche_obje_chan']='dernière modification&nbsp;: ';
$g4p_langue['recherche_obje_select_obje']='Sélectionner le média';
$g4p_langue['recherche_obje_media_de']='Média de&nbsp;: ';
$g4p_langue['recherche_obje_mediaeven']="Média de l\'événement&nbsp;: %1\$s, le %2\$s, à %3\$s";
$g4p_langue['recherche_obje_mediafamille']='Média de la famille&nbsp; ';

$g4p_langue['recherche_even_type']='Type d\'événement&nbsp;: ';
$g4p_langue['recherche_even_typedescr']='Description du type&nbsp;: ';
$g4p_langue['recherche_even_descr']='Description&nbsp;: ';
$g4p_langue['recherche_even_year']='Année de l\'événement&nbsp;: ';
$g4p_langue['recherche_even_lieu']='Lieu&nbsp;: ';
$g4p_langue['recherche_even_cause']='Cause de l\'événement&nbsp;: ';
$g4p_langue['recherche_even_rechercher']=' Rechercher ';

//page recherche_gns
$g4p_langue['recherche_gns_titre']='Recherche d\'un lieu dans la base GNS';
$g4p_langue['recherche_gns_rc']='Région du monde&nbsp;: ';
$g4p_langue['recherche_gns_cc']='Pays&nbsp;: ';
$g4p_langue['recherche_gns_rg']='Région&nbsp;: ';
$g4p_langue['recherche_gns_ville']='Ville&nbsp;: ';
$g4p_langue['recherche_gns_fips_vide']='La base fips de GNS est vide&nbsp;: ';
$g4p_langue['recherche_gns_lieux_vide']='La base GNS ne contient pas les
données de ce pays, vous devez importer le ficher ';
$g4p_langue['recherche_gns_choix']='Choisissez';
$g4p_langue['recherche_gns_ne_sais_pas']='Ne sais pas';
$g4p_langue['recherche_gns_no_result']='Aucun résultat';
$g4p_langue['recherche_gns_results']='résultats';
$g4p_langue['recherche_gns_result_limit']='Protection contre un surnombre de requètes activée, la liste des villes présentée est incomplète';

//Correspondance traduction code de région du monde
$g4p_langue['region_code'][1]='Europe de l\'Ouest / Amériques';
$g4p_langue['region_code'][2]='Europe de l\'Est';
$g4p_langue['region_code'][3]='Afrique / Moyen-Orient';
$g4p_langue['region_code'][4]='Asie Centrale';
$g4p_langue['region_code'][5]='Asie / Pacifique';
$g4p_langue['region_code'][6]='VietNam';

//page statistiques.php
$g4p_langue['stats_titre']="La base de donnée %s en chiffres";
$g4p_langue['stats_nb_indi']='Nombre d\'individus enregistrés&nbsp;: ';
$g4p_langue['stats_nb_ievent']='Nombre d\'événements inidviduels&nbsp;: ';
$g4p_langue['stats_nb_fevent']='Nombre d\'événements familiaux&nbsp;: ';
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

$g4p_langue['sys_function_show_rela_ext_oncles']='Oncles et tantes&nbsp;: '; //ajouté le 31/01/2005
$g4p_langue['sys_function_show_rela_ext_cousins']='Cousins et cousines&nbsp;: '; //ajouté le 31/01/2005
$g4p_langue['sys_function_show_rela_ext_freres']='Frères et soeurs&nbsp;: '; //ajouté le 31/01/2005

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
$g4p_langue['message_suppr_event_echec']='Erreur lors de la suppression de l\'événement<br />';
$g4p_langue['message_suppr_famille_succes']='La famille a bien été supprimé<br />';
$g4p_langue['message_suppr_famille_echec']='La famille n\'a pas été supprimé<br />';
$g4p_langue['message_creer_base_succes']='Création de la nouvelle entrée réussie<br />';
$g4p_langue['message_perm_admin_requis']='Permission d\'administrateur requise<br />';
$g4p_langue['message_email_ajout_succes']="%s a été ajouté avec succès<br />";
$g4p_langue['message_email_ajout_erreur']='Erreur lors de l\'insertion<br />';
$g4p_langue['message_membre_supp_succes']='Le membre a été supprimé avec succès<br />';
$g4p_langue['message_membre_supp_erreur']='Erreur lors de la suppression<br />';
$g4p_langue['message_membre_supp_erreur_courant']='Vous ne pouvez pas supprimer le compte en cours d\'utilisation<br />';
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
$g4p_langue['message_lien_media_succes']='Lien avec le média éffectué<br />';
$g4p_langue['message_repo_enr']='Nouveau dépot enregistré<br />';
$g4p_langue['message_ajout_rela_succes']='Création de la nouvelle relation réussie<br />';
$g4p_langue['message_suppr_rela_succes']='Suppression de la relation réussie<br />';
$g4p_langue['message_masque_indi']='L\'individu est désormais masqué aux annonymes<br />';
$g4p_langue['message_demasque_indi']='L\'individu est désormais visible aux annonymes<br />';
$g4p_langue['message_gen_pdf']='Génération du fichier PDF en %s s<br />';// ajouté le 19/02/2005

// page admin/index.php
$g4p_langue['a_index_titre']='<h2>Modification de la fiche de : %s</h2>';
$g4p_langue['a_index_nom']='Nom&nbsp;: ';
$g4p_langue['a_index_prenom']='Prénom&nbsp;: ';
$g4p_langue['a_index_sexe']='Sexe&nbsp;: ';
$g4p_langue['a_index_npfx']='Préfixe du prénom (NPFX)&nbsp;: ';
$g4p_langue['a_index_givn']='Nom usuel (GIVN)&nbsp;: ';
$g4p_langue['a_index_nick']='Surnom (NICK)&nbsp;: ';
$g4p_langue['a_index_spfx']='Préfixe du nom (SPFX)&nbsp;: ';
$g4p_langue['a_index_nsfx']='Suffixe (NSFX)&nbsp;: ';

$g4p_langue['a_index_ajout_alias']='Ajouter un alias : ';
$g4p_langue['a_index_ajout_alias_ou']=' ou ';

$g4p_langue['a_index_ajout_ievent']='Ajouter un nouvel événement';

$g4p_langue['a_index_modfams_titre']="Famille %1\$s - %2\$s";
$g4p_langue['a_index_modfams_ou']=' ou ';
$g4p_langue['a_index_modfams_conjoint1']='1<sup>er</sup> conjoint (homme)&nbsp;: ';
$g4p_langue['a_index_modfams_conjoint2']='2<sup>ème</sup> conjoint (femme)&nbsp;: ';
$g4p_langue['a_index_modfams_conjointid']='ID du nouveau conjoint&nbsp;: ';
$g4p_langue['a_index_modfams_ajout_ou']=' ou ';//ajouté le 24/08/2005
$g4p_langue['a_index_modfams_conjoint_cherche']='Rechercher un conjoint';
$g4p_langue['a_index_ajout_fevent']='Ajouter un nouvel événement'; //ajouté le 23/01/2005

$g4p_langue['a_index_modfams_enfants']='Enfant(s) issu(s) du mariage&nbsp;: ';
$g4p_langue['a_index_modfams_confirme_suppr_js']='Confirmer la suppression ?\\n ATTENTION, seul le lien est supprimé, l\\\'individu existera toujours';
$g4p_langue['a_index_modfams_newchild']='Ajouter un nouvel enfant&nbsp;: ';
$g4p_langue['a_index_modfams_enfant_recherche']='Rechercher un enfant';
$g4p_langue['a_index_modfams_enfant_relation']='Relation avec l\'enfant&nbsp;: '; //ajouté le 27/01/2005
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
$g4p_langue['a_index_mod_note_liste_event']='Les événements&nbsp;: ';
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
$g4p_langue['a_index_mod_source_inclu']='Cette source est incluse dans les éléments suivants&nbsp;:'; //ajouté le 12/12/2005

$g4p_langue['a_index_ajout_media_titre']='Ajout d\'un document multimédia ou d\'une URL';
$g4p_langue['a_index_ajout_media_lier']='Lier un média déjà éxistant&nbsp;: ';
$g4p_langue['a_index_ajout_media_id']='Id du média&nbsp;: ';
$g4p_langue['a_index_ajout_media_recherche']='Rechercher le média';
$g4p_langue['a_index_ajout_media_nouveau_media']='Nouveau média&nbsp;: ';
$g4p_langue['a_index_ajout_media_titre2']='titre du média&nbsp;: ';
$g4p_langue['a_index_ajout_media_file']='Média&nbsp;: ';
$g4p_langue['a_index_ajout_media_url']='<strong>OU</strong> URL&nbsp;: ';

$g4p_langue['a_index_mod_event_titre']='Modification d\'un événement ou d\'un attribut';
$g4p_langue['a_index_mod_event_type']='type d\'événement&nbsp;: ';
$g4p_langue['a_index_mod_event_type_descr']='description du type&nbsp;: ';
$g4p_langue['a_index_mod_event_descr']='Description&nbsp;: ';
$g4p_langue['a_index_mod_event_date']='Date de l\'événement&nbsp;:';
$g4p_langue['a_index_mod_event_lieu']='Lieu&nbsp;: ';
$g4p_langue['a_index_mod_event_age']='Age au moment de l\'événement&nbsp;: ';
$g4p_langue['a_index_mod_event_cause']='Cause de l\'événement&nbsp;: ';

$g4p_langue['a_index_ajout_event_titre']='Ajout d\'un événement individuel ou familial';
$g4p_langue['a_index_ajout_event_type']='type d\'événement&nbsp;: ';
$g4p_langue['a_index_ajout_event_type_descr']='description du type&nbsp;: ';
$g4p_langue['a_index_ajout_event_descr']='Description&nbsp;: ';
$g4p_langue['a_index_ajout_event_date']='Date de l\'événement (format JJ/MM/AAAA ou GEDCOM)&nbsp;: ';
$g4p_langue['a_index_ajout_event_lieu']='Lieu&nbsp;: ';
$g4p_langue['a_index_ajout_event_age']="Age au moment de l'événement&nbsp;: %s (inutilisé pour un événement familial)";
$g4p_langue['a_index_ajout_event_cause']='Cause de l\'événement&nbsp;: ';

//$g4p_langue['a_index_ajout_fevent_titre']='Ajout d\'un événement familial';

$g4p_langue['a_index_new_base_titre']='Ajout d\'une nouvelle base de données';
$g4p_langue['a_index_new_base_nom']='Nom&nbsp;: ';
$g4p_langue['a_index_new_base_descr']='Description&nbsp;: ';

$g4p_langue['a_index_gerer_download_titre']='Gestion des téléchargements';
$g4p_langue['a_index_gerer_download_message']='<i>Utilisez la fonction de création des fichiers GEDCOMS et PDF uniquement en local si votre base contient de nombreuses personnes<br />
      La création de ces fichiers demandent beaucoup de ressources.<br />
      Copier les fichiers des répertoires caches/gedcoms/ et cache/pdf/ sur votre serveur pour mettre à jour les fichiers.
      </i>';
$g4p_langue['a_index_gerer_download_base']="Base : %s";
$g4p_langue['a_index_gerer_download_date_ged']="Fichier GEDCOM : <i>%s</i>";
$g4p_langue['a_index_gerer_download_recreer']='Recréer le fichier';
$g4p_langue['a_index_gerer_download_inconnu']='Fichier inexistant - ';
$g4p_langue['a_index_gerer_download_creer']='Créer le fichier';
$g4p_langue['a_index_gerer_download_date_pdf']="Fichier PDF : <i>%s</i>";

$g4p_langue['a_index_gerer_perm_titre']='Gestions des membres et des autorisations';
$g4p_langue['a_index_gerer_perm_ajout_membre']='Ajout d\'un nouveau membre&nbsp;: ';
$g4p_langue['a_index_gerer_perm_supp_membre']='Suppression d\'un membre&nbsp;: ';
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
$g4p_langue['a_index_vide_base_message']='Supprimer les fichiers situés
dans le répertoire cache et de toutes les donn&eacute;es de la base correspondante';
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
$g4p_langue['a_index_mod_multimedia_lien_event']='Les événements&nbsp;: ';
$g4p_langue['a_index_mod_multimedia_file_url']='Fichier ou url&nbsp;: ';
$g4p_langue['a_index_mod_multimedia_new_file']='Importer un nouveau fichier&nbsp;: ';
$g4p_langue['a_index_mod_multimedia_erreur']='Erreur! L\'enregistrement n\'existe pas, le cache a été regénéré';

$g4p_langue['a_index_ajout_depot_titre']='Ajout d\'un dépot d\'archives';
$g4p_langue['a_index_ajout_depot_nom']='Nom&nbsp;: ';
$g4p_langue['a_index_ajout_depot_adresse']='Adresse&nbsp;: ';
$g4p_langue['a_index_ajout_depot_ville']='Ville&nbsp;: ';
$g4p_langue['a_index_ajout_depot_cp']='Code postal&nbsp;: ';
$g4p_langue['a_index_ajout_depot_etat']='Etat&nbsp;: ';
$g4p_langue['a_index_ajout_depot_pays']='Pays&nbsp;: ';
$g4p_langue['a_index_ajout_depot_tel']='Téléphone ';

$g4p_langue['a_index_ajout_assoc_titre']='Ajout d\'une nouvelle relation';
$g4p_langue['a_index_ajout_assoc_id']='Id de l\'individu&nbsp;: ';
$g4p_langue['a_index_ajout_assoc_descr']='Description&nbsp;: ';
$g4p_langue['a_index_ajout_assoc_recherche']='Rechercher l\'individu';

//rapport
$g4p_langue['a_index_delrapport_confirme_suppr_js']='Supprimer le rapport ?';  //ajouté le 30/04/2005 à 19h43
$g4p_langue['a_index_delrapport_ok']='Suppression du rapport éffectuée avec succès';  //ajouté le 30/04/2005 à 19h43
$g4p_langue['a_index_delrapport_fichier_nonok']='Suppression du fichier impossible';  //ajouté le 30/04/2005 à 19h43

//fichier profil.php
$g4p_langue['profil_titre']='Modification des préférences';
$g4p_langue['profil_annonyme']='Vous n\'êtes pas un utilisateur enregistré';
$g4p_langue['profil_membre']="Vous êtes enregistré sous l'email %s";
$g4p_langue['profil_langue']='Choisissez votre langue&nbsp;: ';
$g4p_langue['profil_theme']='Choisissez votre thème&nbsp;: ';

//16/06/2005 ajout de pref des lieux
$g4p_langue['profil_lieu']='Choisissez l\'affichage des lieux&nbsp;: ';
$g4p_langue['profil_place_aucun']='Aucun';

//g4p_class.php
$g4p_langue['class_chan']='inconnue'; //ajouté le 12/01/2005 19h03

//fichier admin/creer.php
//voir $g4p_langue['index_*']
$g4p_langue['creer_indi_titre']='Créer un individu'; // ajouté le 29/01/2005

$g4p_langue['creer_fam_titre']='Créer une famille'; // ajouté le 27/01/2005
$g4p_langue['creer_fam_conjoint1']='1er conjoint (homme)&nbsp;: '; // ajouté le 27/01/2005
$g4p_langue['creer_fam_id_conjoint']='ID du nouveau conjoint&nbsp;: '; // ajouté le 27/01/2005
$g4p_langue['creer_fam_rechercher_conjoint']='Rechercher un conjoint'; // ajouté le 27/01/2005
$g4p_langue['creer_fam_conjoint2']='2ème conjoint (femme)&nbsp;: '; // ajouté le 27/01/2005


//page export_gedcom.php
//ajouté le 30/01/2005
$g4p_langue['expot_gedcom_titre']='Export au format GEDCOM';
$g4p_langue['expot_gedcom_sstitre_info']='Informations';
$g4p_langue['expot_gedcom_submit_id']=' Mettre a jour ';
$g4p_langue['expot_gedcom_sstitre_config']='Configuration';
$g4p_langue['expot_gedcom_arbre_asc']='Arbre des ascendants';
$g4p_langue['expot_gedcom_arbre_desc']='Arbre des descendants';
$g4p_langue['expot_gedcom_arbre_asc_desc']='Arbre des ascendants et des descendants';
$g4p_langue['expot_gedcom_arbre_complet']='Arbre complet de la famille elargie';
$g4p_langue['expot_gedcom_inserer_source']='Insérer les sources';
$g4p_langue['expot_gedcom_inserer_note']='Insérer les notes';
$g4p_langue['expot_gedcom_inserer_media']='Insérer les médias';
$g4p_langue['expot_gedcom_generer']='Générer le GEDCOM';

//fichier export_rapport.php
$g4p_langue['export_rapport_titre']='Création de rapports';

//erreur, nom de fichier inconnu
$g4p_langue['a_index_affiche_media_mod']='Modifier l\'objet';
$g4p_langue['a_index_affiche_media_suppr']='Supprimer l\'objet';
//

//syl: pied de page //ajouté le 04/05/2005
$g4p_langue['membre_connecte']='Membre connecté :';
$g4p_langue['acces_base_interdit']='Vous n\'avez pas accès à cette base de donnée';

//ce qui suit vient des noms de colonnes de la table genea_place
$g4p_langue['place_lieudit']='Lieu dit';
$g4p_langue['place_ville']='Commune';
$g4p_langue['place_cp']='Code postal';
$g4p_langue['place_insee']='Code INSEE';
$g4p_langue['place_departement']='Département';
$g4p_langue['place_region']='Région';
$g4p_langue['place_pays']='Pays';
$g4p_langue['place_latitude']='Latitude';
$g4p_langue['place_longitude']='Longitude';

//cartographie
$g4p_langue['cartographie']['cartes']['france']='France';
$g4p_langue['cartographie']['cartes']['europe']='Europe';
$g4p_langue['cartographie']['cartes']['russie']='Russie';

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
  'CAUS'=>'Cause d\'un événement',
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
  'RESI'=>'Résidence',//syl
  'TITL'=>'Titre',
  'EVEN'=>'Evènement',
  'BAPL'=>'Baptème mormons',
  'GRAD'=>'Diplôme'
);
array_multisort($g4p_tag_def);

$g4p_tag_ievents=array(
  'ADOP'=>'Adoption',
  'BAPM'=>'Baptème',
  'BIRT'=>'Naissance',
  'BURI'=>'Inhumation',
  'CHR'=>'Baptème religieux',
  'CHRA'=>'Baptème religieux (Adulte)',
  'CONF'=>'Confirmation',
  'FCOM'=>'Première communion',
  'CREM'=>'Incinération',
  'DEAT'=>'Décès',
  'ORDN'=>'Ordination',
  'NATU'=>'Naturalisation',
  'EMIG'=>'Emigration',
  'IMMI'=>'Immigration',
  'CENS'=>'Resencement',
  'PROB'=>'Validation d\'un testament',
  'WILL'=>'Testament',
  'BLES'=>'Bénédiction',
  'BARM'=>'BAR MITZVAH (garçon)',
  'BASM'=>'BAR MITZVAH (fille)',
  'RETI'=>'Retraite',
  'EVEN'=>'Evènement',
  'BAPL'=>'Baptème mormons',
  'GRAD'=>'Diplôme'
);
array_multisort($g4p_tag_ievents);

$g4p_tag_iattributs=array(
  'CAST'=>'Rang ou status',
  'DSCR'=>'Description',
  'EDUC'=>'Niveau d\'instruction',
  'IDNO'=>'Matricule/numéro',
  'NATI'=>'Nationalité',
  'PROP'=>'Possession',
  'OCCU'=>'Profession',
  'RELI'=>'Religion',
  'RESI'=>'Résidence',//syl
  'TITL'=>'Titre'
);
array_multisort($g4p_tag_iattributs);

$g4p_tag_fevents=array(
  'ANUL'=>'Déclaration de nullité d\'un mariage',
  'CENS'=>'Resencement',
  'DIV'=>'Divorce',
  'DIVF'=>'Dossier de divorce',
  'ENGA'=>'Fiançaille',
  'MARB'=>'Publication des bans de mariage',
  'MARC'=>'Contrat de mariage',
  'MARL'=>'Autorisation de mariage',
  'MARS'=>'Contrat avant mariage',
  'MARR'=>'Mariage',
  'EVEN'=>'Evènement'
);
array_multisort($g4p_tag_fevents);

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

$g4p_date_modif1=array('EST'=>'Estimée', 'ABT'=>'Environ','BEF'=>'Avant','AFT'=>'Après', 'CAL'=>'Calculée');

$g4p_date_range=array(array('BET'=>'Entre', 'AND'=>'et'),array('FROM'=>'Depuis','TO'=>'à'));

$g4p_lien_def=array(
  'BIRTH'=>'légitimes',
  'adopted'=>'Adoptifs'
);

$g4p_source_media_type=array(
  'audio'=>'Audio',
  'book'=>'Livre',
  'card'=>'Fiche',
  'electronic'=>'Electronique',
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
  
$liste_mois_gregorien=array(
  'JAN'=>'Janvier',
  'FEB'=>'Février',
  'MAR'=>'Mars',
  'APR'=>'Avril',
  'MAY'=>'Mai',
  'JUN'=>'Juin',
  'JUL'=>'Juillet',
  'AUG'=>'Août',
  'SEP'=>'Septembre',
  'OCT'=>'Octobre',
  'NOV'=>'Novembre',
  'DEC'=>'Décembre');
$liste_mois_francais=array(
  'VEND' => 'vendemiaire',
  'BRUM' =>'brumaire',
  'FRIM' => 'frimaire',
  'NIVO' => 'nivose',
  'PLUV' => 'pluviose',
  'VENT' => 'ventose',
  'GERM' => 'germinal',
  'FLOR' => 'floreal',
  'PRAI' => 'prairial',
  'MESS' => 'messidor',
  'THER' => 'thermidor',
  'FRUC' => 'fructidor',
  'COMP' => 'jours complémentaires');
$liste_mois_hebreux=array( 
  'TSH' => 'Tishri',
  'CSH' => 'Cheshvan',
  'KSL' => 'Kislev',
  'TVT' => 'Tevet',
  'SHV' => 'Shevat',
  'ADR' => 'Adar',
  'ADS' => 'Adar Sheni',
  'NSN' => 'Nisan',
  'IYR' => 'Iyar',
  'SVN' => 'Sivan',
  'TMZ' => 'Tammuz',
  'AAV' => 'Av',
  'ELL' => 'Elul');  

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
  0=>'Decadi');

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
  1=>'Sheni',
  2=>'Shlishi',
  3=>'Revii',
  4=>'Hamishi',
  5=>'Shishi',
  6=>'Shabat',
  0=>'Rishon');

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