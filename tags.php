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
 *              Détails d'un évènement familial                            *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

include($g4p_chemin.'p_conf/g4p_config.php');
include($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
include($g4p_chemin.'entete.php');
?>

<h2>Les LABELS (TAGS)</h2>
<a name="top"></a>
<p style="text-align:center;">Les 129 TAGS GEDCOM :<br />
<a href="#A">A</a>
<a href="#B">B</a>
<a href="#C">C</a>

<a href="#D">D</a>
<a href="#E">E</a>
<a href="#F">F</a>
<a href="#G">G</a>
<a href="#H">H</a>
<a href="#I">I</a>
<a href="#L">L</a>
<a href="#M">M</a>
<a href="#N">N</a>

<a href="#O">O</a>
<a href="#P">P</a>
<a href="#Q">Q</a>
<a href="#R">R</a>
<a href="#S">S</a>
<a href="#U">U</a>
<a href="#V">V</a>
<a href="#W">W</a>
<br /><br /></p>

<table class="taggedcom">
<tbody><tr>
<td><b>TAG</b></td>
<td><b>TITRE</b></td>
<td><b>Description d'origine</b></td>
<td><b>Résumé</b></td>
</tr>
<tr><td><a name="A"></a><b>A</b></td>
<td colspan="3"><a href="#top">HAUT DE PAGE</a>
</td></tr>
<tr>

<td><a name="ABBR"></a>ABBR</td>
<td>{ABBREVIATION}</td>
<td>A short name of a title, description, or name.</td>
<td>Titre, description ou autre</td>
</tr>
<tr>
<td><a name="ADDR"></a>ADDR</td>
<td>{ADDRESS}</td>
<td>The contemporary place, usually required for
postal purposes, of an individual, a submitter ofinformation, a
repository, a business, a school, or a company.</td>
<td>Adresse postale</td>

</tr>
<tr>
<td><a name="ADR1"></a>ADR1</td>
<td>{ADDRESS1}</td>
<td>The first line of an address.</td>
<td>Première ligne d'une adresse </td>
</tr>
<tr>
<td><a name="ADR2"></a>ADR2</td>
<td>{ADDRESS2}</td>
<td>The second line of an address.</td>

<td>Deuxième ligne d'une adresse </td>
</tr>
<tr>
<td><a name="ADOP"></a>ADOP</td>
<td>{ADOPTION}</td>
<td>Pertaining to creation of a child-parent relationship that does not exist biologically.</td>
<td>Lien parent enfant hors lien biologique</td>
</tr>
<tr>
<td><a name="AFN"></a>AFN</td>
<td>{AFN}</td>

<td>A unique permanent record file number of an individual record stored in Ancestral File.</td>
<td>Numéro dans le fichier Ancestral File qui contient les informations relatives à l'individu </td>
</tr>
<tr>
<td><a name="AGE"></a>AGE</td>
<td>{AGE}</td>
<td>The age of the individual at the time an event occurred, or the age listed in the document.</td>
<td>Age de l'individu au moment de l'événement ou âge qui figure dans le document. </td>
</tr>
<tr>
<td><a name="AGNC"></a>AGNC</td>

<td>{AGENCY}</td>
<td>The institution or individual having authority and/or responsibility to manage or govern.</td>
<td>Entreprise ou institution </td>
</tr>
<tr>
<td><a name="ALIA"></a>ALIA</td>
<td>{ALIAS}</td>
<td>An indicator to link different record descriptions of a person who may be the same person.</td>
<td>Association d'informations différentes</td>
</tr>

<tr>
<td><a name="ANCE"></a>ANCE</td>
<td>{ANCESTORS}</td>
<td>Pertaining to forbearers of an individual.</td>
<td>Ancêtres</td>
</tr>
<tr>
<td><a name="ANCI"></a>ANCI</td>
<td>{ANCES_INTEREST}</td>
<td>Indicates an interest in additional research for ancestors of this individual. (See also DESI)</td>

<td>Intérêt à rechercher des informations sur les ancêtres</td>
</tr>
<tr>
<td><a name="ANUL"></a>ANUL</td>
<td>{ANNULMENT}</td>
<td>Declaring a marriage void from the beginning (never existed).</td>
<td>Déclaration de nullité d'un mariage</td>
</tr>
<tr>
<td><a name="ASSO"></a>ASSO</td>
<td>{ASSOCIATES}</td>

<td>An indicator to link friends, neighbors, relatives, or associates of an individual.</td>
<td>Relation entre enregistrements</td>
</tr>
<tr>
<td><a name="AUTH"></a>AUTH</td>
<td>{AUTHOR}</td>
<td>The name of the individual who created or compiled information.</td>
<td>Nom de la personne qui a relevé les informations ou qui a constitué le fichier </td>
</tr>
<tr><td align="center"><a name="B"></a><b>B</b></td>

<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>
</td></tr>
<tr>
<td><a name="BAPL"></a>BAPL</td>
<td>{BAPTISM-LDS}</td>
<td>The event of baptism performed at age eight or later by priesthood authority of the LDS Church.(See also BAPM, next)</td>
<td>Baptême Mormons</td>
</tr>
<tr>
<td><a name="BAPM"></a>BAPM</td>
<td>{BAPTISM}</td>

<td>The event of baptism (not LDS), performed in infancy or later. (See also BAPL, above, and CHR)</td>
<td>Baptême (non Mormon) </td>
</tr>
<tr>
<td><a name="BARM"></a>BARM</td>
<td>{BAR_MITZVAH}</td>
<td>The ceremonial event held when a Jewish boy reaches age 13.</td>
<td>Cérémonie juive qui a lieu pour les garçons à l'âge de 13 ans </td>
</tr>
<tr>
<td><a name="BASM"></a>BASM</td>

<td>{BAS_MITZVAH}</td>
<td>The ceremonial event held when a Jewish girl reaches age 13, also known as "Bat Mitzvah."</td>
<td>Cérémonie juive qui a lieu pour les filles à l'âge de 13 ans</td>
</tr>
<tr>
<td><a name="BIRT"></a>BIRT</td>
<td>{BIRTH}</td>
<td>The event of entering into life.</td>
<td>Naissance </td>
</tr>

<tr>
<td><a name="BLES"></a>BLES</td>
<td>{BLESSING}</td>
<td>A religious event of bestowing divine care or intercession. Sometimes given in connection with anaming ceremony.</td>
<td>Bénédiction</td>
</tr>
<tr>
<td><a name="BLOB"></a>BLOB</td>
<td>{BINARY_OBJECT}</td>
<td>A grouping of data used as input to a multimedia system that processes binary data to represent images, sound, and video.</td>

<td>Données représentant images, son vidéo, etc... </td>
</tr>
<tr>
<td><a name="BURI"></a>BURI</td>
<td>{BURIAL}</td>
<td>The event of the proper disposing of the mortal remains of a deceased person.</td>
<td>Sépulture </td>
</tr>
<tr><td align="center"><a name="C"></a><b>C</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>

</td></tr>
<tr>
<td><a name="CALN"></a>CALN</td>
<td>{CALL_NUMBER}</td>
<td>The number used by a repository to identify the specific items in its collections.</td>
<td>Numéro d'identification</td>
</tr>
<tr>
<td><a name="CAST"></a>CAST</td>
<td>{CASTE}</td>
<td>The name of an individual's rank or status in
society, basedon racial or religious differences, or differences in
wealth, inheritedrank, profession, occupation, etc.</td>

<td>Rang ou statut</td>
</tr>
<tr>
<td><a name="CAUS"></a>CAUS</td>
<td>{CAUSE}</td>
<td>A description of the cause of the associated event or fact, such as the cause of death.</td>
<td>Cause d'un événement</td>
</tr>
<tr>
<td><a name="CENS"></a>CENS</td>
<td>{CENSUS}</td>

<td>The event of the periodic count of the population for a designated locality, such as a national orstate Census.</td>
<td>Recensement</td>
</tr>
<tr>
<td><a name="CHAN"></a>CHAN</td>
<td>{CHANGE}</td>
<td>Indicates a change, correction, or modification.
Typically used in connection with a DATE to specify when a change in
information occurred.</td>
<td>Indicateur de modification</td>
</tr>
<tr>
<td><a name="CHAR"></a>CHAR</td>

<td>{CHARACTER}</td>
<td>An indicator of the character set used in writing this automated information.</td>
<td>Jeu de caractères utilisé dans le fichier.</td>
</tr>
<tr>
<td><a name="CHIL"></a>CHIL</td>
<td>{CHILD}</td>
<td>The natural, adopted, or sealed (LDS) child of a father and a mother.</td>
<td>Enfant naturel ou adopté </td>
</tr>

<tr>
<td><a name="CHR"></a>CHR</td>
<td>{CHRISTENING}</td>
<td>The religious event (not LDS) of baptizing and/or naming a child.</td>
<td>baptême religieux (enfant) (non Mormon)</td>
</tr>
<tr>
<td><a name="CHRA"></a>CHRA</td>
<td>{ADULT_CHRISTENING}</td>
<td>The religious event (not LDS) of baptizing and/or naming an adult person.</td>

<td>baptême religieux (adulte) (non Mormon)</td>
</tr>
<tr>
<td><a name="CITY"></a>CITY</td>
<td>{CITY}</td>
<td>A lower level jurisdictional unit. Normally an incorporated municipal unit.</td>
<td>Ville</td>
</tr>
<tr>
<td><a name="CONC"></a>CONC</td>
<td>{CONCATENATION}</td>

<td>An indicator that additional data belongs to the
superior value. The information from the CONCvalue is to be connected
to the value of the superior preceding line without a space and without
acarriage return and/or new line character. Values that are split for a
CONC tag must always besplit at a non-space. If the value is split on a
space the space will be lost when concatenation takesplace. This is
because of the treatment that spaces get as a GEDCOM delimiter, many
GEDCOMvalues are trimmed of trailing spaces and some systems look for
the first non-space starting afterthe tag to determine the beginning of
the value.</td>
<td>Continuation des informations qui précédent,
concaténées sans espace ni retour de ligne avec coupure au milieu d'un
champ et pas sur un espace </td>
</tr>
<tr>
<td><a name="CONF"></a>CONF</td>
<td>{CONFIRMATION}</td>
<td>The religious event (not LDS) of conferring the gift of the Holy Ghost and, among protestants, fullchurch membership.</td>
<td>Cérémonie religieuse (non Mormon)</td>
</tr>
<tr>
<td><a name="CONL"></a>CONL</td>

<td>{CONFIRMATION_L}</td>
<td>The religious event by which a person receives membership in the LDS Church.</td>
<td>cérémonie religieuse par laquelle un individu devient membre de l'Eglise des Mormons </td>
</tr>
<tr>
<td><a name="CONT"></a>CONT</td>
<td>{CONTINUED}</td>
<td>An indicator that additional data belongs to the
superior value. The information from the CONTvalue is to be connected
to the value of the superior preceding line with a carriage return
and/ornew line character. Leading spaces could be important to the
formatting of the resultant text.When importing values from CONT lines
the reader should assume only one delimiter characterfollowing the CONT
tag. Assume that the rest of the leading spaces are to be a part of the
value.</td>
<td>indicateur de continuation des informations qui précédent, avec retour ligne. </td>
</tr>

<tr>
<td><a name="COPR"></a>COPR</td>
<td>{COPYRIGHT}</td>
<td>A statement that accompanies data to protect it from unlawful duplication and distribution.</td>
<td>copyright</td>
</tr>
<tr>
<td><a name="CORP"></a>CORP</td>
<td>{CORPORATE}</td>
<td>A name of an institution, agency, corporation, or company.</td>

<td>Nom d'entreprise ou d'institution </td>
</tr>
<tr>
<td><a name="CREM"></a>CREM</td>
<td>{CREMATION}</td>
<td>Disposal of the remains of a person's body by fire.</td>
<td>Incinération </td>
</tr>
<tr>
<td><a name="CTRY"></a>CTRY</td>
<td>{COUNTRY}</td>

<td>The name or code of the country.</td>
<td>Nom ou code du pays </td>
</tr>
<tr><td align="center"><a name="D"></a><b>D</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>
</td></tr>
<tr>
<td><a name="DATA"></a>DATA</td>
<td>{DATA}</td>
<td>Pertaining to stored automated information.</td>

<td>Informations</td>
</tr>
<tr>
<td><a name="DATE"></a>DATE</td>
<td>{DATE}</td>
<td>The time of an event in a calendar format.</td>
<td>Date d'un événement</td>
</tr>
<tr>
<td><a name="DEAT"></a>DEAT</td>
<td>{DEATH}</td>

<td>The event when mortal life terminates.</td>
<td>Décès </td>
</tr>
<tr>
<td><a name="DESC"></a>DESC</td>
<td>{DESCENDANTS}</td>
<td>Pertaining to offspring of an individual.</td>
<td>Descendance</td>
</tr>
<tr>
<td><a name="DESI"></a>DESI</td>

<td>{DESCENDANT_INT}</td>
<td>Indicates an interest in research to identify additional descendants of this individual. (See also ANCI)</td>
<td>Intérêt à rechercher des informations sur les descendants</td>
</tr>
<tr>
<td><a name="DEST"></a>DEST</td>
<td>{DESTINATION}</td>
<td>A system receiving data.</td>
<td>Destination des données. </td>
</tr>

<tr>
<td><a name="DIV"></a>DIV</td>
<td>{DIVORCE}</td>
<td>An event of dissolving a marriage through civil action.</td>
<td>Divorce</td>
</tr>
<tr>
<td><a name="DIVF"></a>DIVF</td>
<td>{DIVORCE_FILED}</td>
<td>An event of filing for a divorce by a spouse.</td>

<td>Dossier de divorce</td>
</tr>
<tr>
<td><a name="DSCR"></a>DSCR</td>
<td>{PHY_DESCRIPTION}</td>
<td>The physical characteristics of a person, place, or thing.</td>
<td>Description physique</td>
</tr>
<tr><td align="center"><a name="E"></a><b>E</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>

</td></tr>
<tr>
<td><a name="EDUC"></a>EDUC</td>
<td>{EDUCATION}</td>
<td>Indicator of a level of education attained.</td>
<td>Niveau d'instruction </td>
</tr>
<tr>
<td><a name="EMIG"></a>EMIG</td>
<td>{EMIGRATION}</td>
<td>An event of leaving one's homeland with the intent of residing elsewhere.</td>

<td>Emigration </td>
</tr>
<tr>
<td><a name="ENDL"></a>ENDL</td>
<td>{ENDOWMENT}</td>
<td>A religious event where an endowment ordinance for an individual was performed by priesthoodauthority in an LDS temple.</td>
<td>Cérémonie religieuse (Mormons) </td>
</tr>
<tr>
<td><a name="ENGA"></a>ENGA</td>
<td>{ENGAGEMENT}</td>

<td>An event of recording or announcing an agreement between two people to become married.</td>
<td>Fiançailles </td>
</tr>
<tr>
<td><a name="EVEN"></a>EVEN</td>
<td>{EVENT}</td>
<td>A noteworthy happening related to an individual, a group, or an organization.</td>
<td>Evénement </td>
</tr>
<tr><td align="center"><a name="F"></a><b>F</b></td>

<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>
</td></tr>
<tr>
<td><a name="FAM"></a>FAM</td>
<td>{FAMILY}</td>
<td>Identifies a legal, common law, or other customary
relationship of man and woman and theirchildren, if any, or a family
created by virtue of the birth of a child to its biological father
andmother.</td>
<td>Famille</td>
</tr>
<tr>
<td><a name="FAMC"></a>FAMC</td>
<td>{FAMILY_CHILD}</td>

<td>Identifies the family in which an individual appears as a child.</td>
<td>Indique la famille à laquelle un enfant appartient </td>
</tr>
<tr>
<td><a name="FAMF"></a>FAMF</td>
<td>{FAMILY_FILE}</td>
<td>Pertaining to, or the name of, a family file.
Names stored in a file that are assigned to a family fordoing temple
ordinance work.</td>
<td>Fichier familles (Mormons)</td>
</tr>
<tr>
<td><a name="FAMS"></a>FAMS</td>

<td>{FAMILY_SPOUSE}</td>
<td>Identifies the family in which an individual appears as a spouse.</td>
<td>Indique la famille dans laquelle l'individu est conjoint </td>
</tr>
<tr>
<td><a name="FCOM"></a>FCOM</td>
<td>{FIRST_COMMUNION}</td>
<td>A religious rite, the first act of sharing in the Lord's supper as part of church worship.</td>
<td>Première communion </td>
</tr>

<tr>
<td><a name="FILE"></a>FILE</td>
<td>{FILE}</td>
<td>An information storage place that is ordered and arranged for preservation and reference.</td>
<td>Fichier</td>
</tr>
<tr>
<td><a name="FORM"></a>FORM</td>
<td>{FORMAT}</td>
<td>An assigned name given to a consistent format in which information can be conveyed.</td>

<td>Format de fichier ou de données</td>
</tr>
<tr><td align="center"><a name="G"></a><b>G</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>
</td></tr>
<tr>
<td><a name="GEDC"></a>GEDC</td>
<td>{GEDCOM}</td>
<td>Information about the use of GEDCOM in a transmission.</td>
<td>Norme GEDCOM</td>

</tr>
<tr>
<td><a name="GIVN"></a>GIVN</td>
<td>{GIVEN_NAME}</td>
<td>A given or earned name used for official identification of a person.</td>
<td>Prénom </td>
</tr>
<tr>
<td><a name="GRAD"></a>GRAD</td>
<td>{GRADUATION}</td>
<td>An event of awarding educational diplomas or degrees to individuals.</td>

<td>Diplôme</td>
</tr>
<tr><td align="center"><a name="H"></a><b>H</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>
</td></tr>
<tr>
<td><a name="HEAD"></a>HEAD</td>
<td>{HEADER}</td>
<td>Identifies information pertaining to an entire GEDCOM transmission.</td>
<td>Entête des informations dans un fichier GEDCOM </td>

</tr>
<tr>
<td><a name="HUSB"></a>HUSB</td>
<td>{HUSBAND}</td>
<td>An individual in the family role of a married man or father.</td>
<td>Epoux </td>
</tr>
<tr><td align="center"><a name="I"></a><b>I</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>
</td></tr>
<tr>

<td><a name="IDNO"></a>IDNO</td>
<td>{IDENT_NUMBER}</td>
<td>A number assigned to identify a person within some significant external system.</td>
<td>Numéro d'identification</td>
</tr>
<tr>
<td><a name="IMMI"></a>IMMI</td>
<td>{IMMIGRATION}</td>
<td>An event of entering into a new locality with the intent of residing there.</td>
<td>Immigration </td>

</tr>
<tr>
<td><a name="INDI"></a>INDI</td>
<td>{INDIVIDUAL}</td>
<td>A person.</td>
<td>Individu, personne </td>
</tr>
<tr><td align="center"><a name="L"></a><b>L</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>
</td></tr>
<tr>

<td><a name="LANG"></a>LANG</td>
<td>{LANGUAGE}</td>
<td>The name of the language used in a communication or transmission of information.</td>
<td>Langage utilisé</td>
</tr>
<tr>
<td><a name="LEGA"></a>LEGA</td>
<td>{LEGATEE}</td>
<td>A role of an individual acting as a person receiving a bequest or legal devise.</td>
<td>Légataire </td>

</tr>
<tr><td align="center"><a name="M"></a><b>M</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>
</td></tr>
<tr>
<td><a name="MARB"></a>MARB</td>
<td>{MARRIAGE_BANN}</td>
<td>An event of an official public notice given that two people intend to marry.</td>
<td>Publication des bans de mariage </td>
</tr>
<tr>

<td><a name="MARC"></a>MARC</td>
<td>{MARR_CONTRACT}</td>
<td>An event of recording a formal agreement of
marriage, including the prenuptial agreement inwhich marriage partners
reach agreement about the property rights of one or both,
securingproperty to their children.</td>
<td>Contrat de mariage </td>
</tr>
<tr>
<td><a name="MARL"></a>MARL</td>
<td>{MARR_LICENSE}</td>
<td>An event of obtaining a legal license to marry.</td>
<td>Autorisation de mariage </td>

</tr>
<tr>
<td><a name="MARR"></a>MARR</td>
<td>{MARRIAGE}</td>
<td>A legal, common-law, or customary event of creating a family unit of a man and a woman ashusband and wife.</td>
<td>Mariage </td>
</tr>
<tr>
<td><a name="MARS"></a>MARS</td>
<td>{MARR_SETTLEMENT}</td>
<td>An event of creating an agreement between two
people contemplating marriage, at which timethey agree to release or
modify property rights that would otherwise arise from the marriage.</td>

<td>Contrat avant mariage</td>
</tr>
<tr>
<td><a name="MEDI"></a>MEDI</td>
<td>{MEDIA}</td>
<td>Identifies information about the media or having to do with the medium in which information isstored.</td>
<td>Support de données</td>
</tr>
<tr><td align="center"><a name="N"></a><b>N</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>

</td></tr>
<tr>
<td><a name="NAME"></a>NAME</td>
<td>{NAME}</td>
<td>A word or combination of words used to help
identify an individual, title, or other item. More thanone NAME line
should be used for people who were known by multiple names.</td>
<td>Identification complète</td>
</tr>
<tr>
<td><a name="NATI"></a>NATI</td>
<td>{NATIONALITY}</td>
<td>The national heritage of an individual.</td>

<td>Nationalité</td>
</tr>
<tr>
<td><a name="NATU"></a>NATU</td>
<td>{NATURALIZATION}</td>
<td>The event of obtaining citizenship.</td>
<td>Naturalisation</td>
</tr>
<tr>
<td><a name="NCHI"></a>NCHI</td>
<td>{CHILDREN_COUNT}</td>

<td>The number of children that this person is known
to be the parent of (all marriages) whensubordinate to an individual,
or that belong to this family when subordinate to a FAM_RECORD.</td>
<td>Nombre d'enfants d'un individu ou d'une famille</td>
</tr>
<tr>
<td><a name="NICK"></a>NICK</td>
<td>{NICKNAME}</td>
<td>A descriptive or familiar that is used instead of, or in addition to, one's proper name.</td>
<td>Surnom </td>
</tr>
<tr>
<td><a name="NMR"></a>NMR</td>

<td>{MARRIAGE_COUNT}</td>
<td>The number of times this person has participated in a family as a spouse or parent.</td>
<td>Nombre de mariages</td>
</tr>
<tr>
<td><a name="NOTE"></a>NOTE</td>
<td>{NOTE}</td>
<td>Additional information provided by the submitter for understanding the enclosing data.</td>
<td>Informations complémentaires</td>
</tr>

<tr>
<td><a name="NPFX"></a>NPFX</td>
<td>{NAME_PREFIX}</td>
<td>Text which appears on a name line before the given
and surname parts of a name.i.e. (Lt. Cmndr.) Joseph /Allen/ jr.In this
example Lt. Cmndr. is considered as the name prefix portion.</td>
<td>Titre avant le nom (Ex: Duc, Sir, maître )</td>
</tr>
<tr>
<td><a name="NSFX"></a>NSFX</td>
<td>{NAME_SUFFIX}</td>
<td>Text which appears on a name line after or behind
the given and surname parts of a name.i.e. Lt. Cmndr. Joseph /Allen/
(jr.)In this example jr. is considered as the name suffix portion.</td>

<td>Texte après le nom (Ex: Jrs) </td>
</tr>
<tr><td align="center"><a name="O"></a><b>O</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>
</td></tr>
<tr>
<td><a name="OBJE"></a>OBJE</td>
<td>{OBJECT}</td>
<td>Pertaining to a grouping of attributes used in
describing something. Usually referring to the datarequired to
represent a multimedia object, such an audio recording, a photograph of
a person, or animage of a document.</td>
<td>Référence pour des données descriptives</td>

</tr>
<tr>
<td><a name="OCCU"></a>OCCU</td>
<td>{OCCUPATION}</td>
<td>The type of work or profession of an individual.</td>
<td>Profession </td>
</tr>
<tr>
<td><a name="ORDI"></a>ORDI</td>
<td>{ORDINANCE}</td>
<td>Pertaining to a religious ordinance in general.</td>

<td>Cérémonie religieuse spécifique Mormons </td>
</tr>
<tr>
<td><a name="ORDN"></a>ORDN</td>
<td>{ORDINATION}</td>
<td>A religious event of receiving authority to act in religious matters.</td>
<td>Ordination</td>
</tr>
<tr><td align="center"><a name="P"></a><b>P</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>

</td></tr>
<tr>
<td><a name="PAGE"></a>PAGE</td>
<td>{PAGE}</td>
<td>A number or description to identify where information can be found in a referenced work.</td>
<td>Endroit où une information se trouve</td>
</tr>
<tr>
<td><a name="PEDI"></a>PEDI</td>
<td>{PEDIGREE}</td>
<td>Information pertaining to an individual to parent lineage chart.</td>

<td>Information sur l'individu par rapport à l'ascendance. </td>
</tr>
<tr>
<td><a name="PHON"></a>PHON</td>
<td>{PHONE}</td>
<td>A unique number assigned to access a specific telephone.</td>
<td>Numéro de téléphone </td>
</tr>
<tr>
<td><a name="PLAC"></a>PLAC</td>
<td>{PLACE}</td>

<td>A jurisdictional name to identify the place or location of an event.</td>
<td>Lieu</td>
</tr>
<tr>
<td><a name="POST"></a>POST</td>
<td>{POSTAL_CODE}</td>
<td>A code used by a postal service to identify an area to facilitate mail handling.</td>
<td>Code postal </td>
</tr>
<tr>
<td><a name="PROB"></a>PROB</td>

<td>{PROBATE}</td>
<td>An event of judicial determination of the validity of a will. May indicate several related courtactivities over several dates.</td>
<td>Validation d'un testament </td>
</tr>
<tr>
<td><a name="PROP"></a>PROP</td>
<td>{PROPERTY}</td>
<td>Pertaining to possessions such as real estate or other property of interest.</td>
<td>Possessions </td>
</tr>

<tr>
<td><a name="PUBL"></a>PUBL</td>
<td>{PUBLICATION}</td>
<td>Refers to when and/or were a work was published or created.</td>
<td>Publication d'un ouvrage </td>
</tr>
<tr><td align="center"><a name="Q"></a><b>Q</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>
</td></tr>
<tr>
<td><a name="QUAY"></a>QUAY</td>

<td>{QUALITY_OF_DATA}</td>
<td>An assessment of the certainty of the evidence to support the conclusion drawn from evidence.</td>
<td>Degré de confiance à accorder</td>
</tr>
<tr><td align="center"><a name="R"></a><b>R</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>
</td></tr>
<tr>
<td><a name="REFN"></a>REFN</td>
<td>{REFERENCE}</td>

<td>A description or number used to identify an item for filing, storage, or other reference purposes.</td>
<td>Référencement</td>
</tr>
<tr>
<td><a name="RELA"></a>RELA</td>
<td>{RELATIONSHIP}</td>
<td>A relationship value between the indicated contexts.</td>
<td>Type de lien</td>
</tr>
<tr>
<td><a name="RELI"></a>RELI</td>

<td>{RELIGION}</td>
<td>A religious denomination to which a person is affiliated or for which a record applies.</td>
<td>Religion </td>
</tr>
<tr>
<td><a name="REPO"></a>REPO</td>
<td>{REPOSITORY}</td>
<td>An institution or person that has the specified item as part of their collection(s).</td>
<td>Lieu ou est archivée l'information</td>
</tr>

<tr>
<td><a name="RESI"></a>RESI</td>
<td>{RESIDENCE}</td>
<td>The act of dwelling at an address for a period of time.</td>
<td>Domicile </td>
</tr>
<tr>
<td><a name="RESN"></a>RESN</td>
<td>{RESTRICTION}</td>
<td>A processing indicator signifying access to information has been denied or otherwise restricted.</td>

<td>Indicateur d'accès restreint</td>
</tr>
<tr>
<td><a name="RETI"></a>RETI</td>
<td>{RETIREMENT}</td>
<td>An event of exiting an occupational relationship with an employer after a qualifying time period.</td>
<td>Retraite </td>
</tr>
<tr>
<td><a name="RFN"></a>RFN</td>
<td>{REC_FILE_NUMBER}</td>

<td>A permanent number assigned to a record that uniquely identifies it within a known file.</td>
<td>Numéro d'enregistrement permanent et unique</td>
</tr>
<tr>
<td><a name="RIN"></a>RIN</td>
<td>{REC_ID_NUMBER}</td>
<td>A number assigned to a record by an originating
automated system that can be used by a receivingsystem to report
results pertaining to that record.</td>
<td>Numéro affecté automatiquement à un enregistrement</td>
</tr>
<tr>
<td><a name="ROLE"></a>ROLE</td>

<td>{ROLE}</td>
<td>A name given to a role played by an individual in connection with an event.</td>
<td>Rôle d'un individu dans un événement </td>
</tr>
<tr><td align="center"><a name="S"></a><b>S</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>
</td></tr>
<tr>
<td><a name="SEX"></a>SEX</td>
<td>{SEX}</td>

<td>Indicates the sex of an individual--male or female.</td>
<td>Sexe </td>
</tr>
<tr>
<td><a name="SLGC"></a>SLGC</td>
<td>{SEALING_CHILD}</td>
<td>A religious event pertaining to the sealing of a child to his or her parents in an LDS templeceremony.</td>
<td>Cérémonie religieuse spécifique Mormons </td>
</tr>
<tr>
<td><a name="SLGS"></a>SLGS</td>

<td>{SEALING_SPOUSE}</td>
<td>A religious event pertaining to the sealing of a husband and wife in an LDS temple ceremony.</td>
<td>Cérémonie religieuse spécifique Mormons </td>
</tr>
<tr>
<td><a name="SOUR"></a>SOUR</td>
<td>{SOURCE}</td>
<td>The initial or original material from which information was obtained.</td>
<td>Origine de l'information </td>
</tr>

<tr>
<td><a name="SPFX"></a>SPFX</td>
<td>{SURN_PREFIX}</td>
<td>A name piece used as a non-indexing pre-part of a surname.</td>
<td>Partie d'un nom de famille</td>
</tr>
<tr>
<td><a name="SSN"></a>SSN</td>
<td>{SOC_SEC_NUMBER}</td>
<td>A number assigned by the United States Social Security Administration. Used for taxidentification purposes.</td>

<td>Numéro de sécurité sociale </td>
</tr>
<tr>
<td><a name="STAE"></a>STAE</td>
<td>{STATE}</td>
<td>A geographical division of a larger jurisdictional area, such as a State within the United States of America.</td>
<td>Etat (sens géographique) </td>
</tr>
<tr>
<td><a name="STAT"></a>STAT</td>
<td>{STATUS}</td>

<td>An assessment of the state or condition of something.</td>
<td>Etat (condition) </td>
</tr>
<tr>
<td><a name="SUBM"></a>SUBM</td>
<td>{SUBMITTER}</td>
<td>An individual or organization who contributes genealogical data to a file or transfers it to someoneelse.</td>
<td>Emetteur des données</td>
</tr>
<tr>
<td><a name="SUBN"></a>SUBN</td>

<td>{SUBMISSION}</td>
<td>Pertains to a collection of data issued for processing.</td>
<td>Ensemble des données</td>
</tr>
<tr>
<td><a name="SURN"></a>SURN</td>
<td>{SURNAME}</td>
<td>A family name passed on or used by members of a family.</td>
<td>Nom de famille </td>
</tr>

<tr><td align="center"><a name="T"></a><b>T</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>
</td></tr>
<tr>
<td><a name="TEMP"></a>TEMP</td>
<td>{TEMPLE}</td>
<td>The name or code that represents the name a temple of the LDS Church.</td>
<td>Identification d'un temple (Mormons)</td>
</tr>
<tr>
<td><a name="TEXT"></a>TEXT</td>

<td>{TEXT}</td>
<td>The exact wording found in an original source document.</td>
<td>Texte</td>
</tr>
<tr>
<td><a name="TIME"></a>TIME</td>
<td>{TIME}</td>
<td>A time value in a 24-hour clock format, including
hours, minutes, and optional seconds, separatedby a colon (:).
Fractions of seconds are shown in decimal notation.</td>
<td>heures, minutes, secondes et centièmes de secondes (secondes et centièmes optionnels)</td>
</tr>

<tr>
<td><a name="TITL"></a>TITL</td>
<td>{TITLE}</td>
<td>A description of a specific writing or other work,
such as the title of a book when used in a sourcecontext, or a formal
designation used by an individual in connection with positions of
royalty orother social status, such as Grand Duke.</td>
<td>Titre</td>
</tr>
<tr>
<td><a name="TRLR"></a>TRLR</td>
<td>{TRAILER}</td>
<td>At level 0, specifies the end of a GEDCOM transmission.</td>

<td>Fin du fichier GEDCOM </td>
</tr>
<tr>
<td><a name="TYPE"></a>TYPE</td>
<td>{TYPE}</td>
<td>A further qualification to the meaning of the
associated superior tag. The value does not have anycomputer processing
reliability. It is more in the form of a short one or two word note
that shouldbe displayed any time the associated data is displayed.</td>
<td>Type</td>
</tr>
<tr><td align="center"><a name="V"></a><b>V</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>

</td></tr>
<tr>
<td><a name="VERS"></a>VERS</td>
<td>{VERSION}</td>
<td>Indicates which version of a product, item, or publication is being used or referenced.</td>
<td>Version</td>
</tr>
<tr><td align="center"><a name="W"></a><b>W</b></td>
<td colspan="3" align="center"><a class="l2" href="#top">HAUT DE PAGE</a>
</td></tr>
<tr>

<td><a name="WIFE"></a>WIFE</td>
<td>{WIFE}</td>
<td>An individual in the role as a mother and/or married woman.</td>
<td>Epouse </td>
</tr>
<tr>
<td><a name="WILL"></a>WILL</td>
<td>{WILL}</td>
<td>A legal document treated as an event, by which a
person disposes of his or her estate, to take effectafter death. The
event date is the date the will was signed while the person was alive.
(See also PROBate)</td>
<td>Testament</td>

</tr>
</tbody></table>
<div style="font-size:0.9em; text-align:center; margin-top:0;"><a href="http://www.netnico.com/index.php">© Netnico 2004</a></div>

<?php
include($g4p_chemin.'pied_de_page.php');
?>
