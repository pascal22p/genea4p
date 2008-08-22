<?php

/**
 * le tableau tag contient tout les TAGs définis dans le standard
 * un TAG à la valeur
 * - true (1)  si l'information qu'il est importée dans la base
 * - true (-1) si elle est traitée mais pas supportée par la BD
 * - false (0) sinon (la ligne et ses niveaux supérieurs ne seront pas analysés)
 */

$tag['ABBR'] = 1;
$tag['ADDR'] = 1;
$tag['ADR1'] = 1;
$tag['ADR2'] = 1;
$tag['ADOP'] = 1;
$tag['AFN']  = -1;
$tag['AGE']  = 1;
$tag['AGNC'] = -1;
$tag['ALIA'] = 1;
$tag['ANCE'] = 1;
$tag['ANCI'] = -1;
$tag['ANUL'] = 1;
$tag['ASSO'] = 1;
$tag['AUTH'] = 1;
$tag['BAPL'] = -1;
$tag['BAPM'] = 1;
$tag['BARM'] = 1;
$tag['BASM'] = 1;
$tag['BIRT'] = 1;
$tag['BLES'] = 1;
$tag['BLOB'] = -1;
$tag['BURI'] = 1;
$tag['CALN'] = -1;
$tag['CAST'] = 1;
$tag['CAUS'] = 1;
$tag['CENS'] = 1;
$tag['CHAN'] = 1;
$tag['CHAR'] = 1;
$tag['CHIL'] = 1;
$tag['CHR']  = 1;
$tag['CHRA'] = 1;
$tag['CITY'] = 1;
$tag['CONC'] = 1;
$tag['CONF'] = 1;
$tag['CONL'] = 1;
$tag['CONT'] = 1;
$tag['COPR'] = 1;
$tag['CORP'] = 1;
$tag['CREM'] = 1;
$tag['CTRY'] = 1;
$tag['DATA'] = 1;
$tag['DATE'] = 1;
$tag['DEAT'] = 1;
$tag['DESC'] = 1;
$tag['DESI'] = -1;
$tag['DEST'] = 1;
$tag['DIV']  = 1;
$tag['DIVF'] = 1;
$tag['DSCR'] = 1;
$tag['EDUC'] = 1;
$tag['EMIG'] = 1;
$tag['ENDL'] = -1;
$tag['ENGA'] = 1;
$tag['EVEN'] = 1;
$tag['FAM']  = 1;
$tag['FAMC'] = 1;
$tag['FAMF'] = 1;
$tag['FAMS'] = 1;
$tag['FCOM'] = 1;
$tag['FILE'] = 1;
$tag['FORM'] = 1;
$tag['GEDC'] = 1;
$tag['GIVN'] = 1;
$tag['GRAD'] = 1;
$tag['HEAD'] = 1;
$tag['HUSB'] = 1;
$tag['IDNO'] = 1;
$tag['IMMI'] = 1;
$tag['INDI'] = 1;
$tag['LANG'] = 1;
$tag['LEGA'] = 1;
$tag['MARB'] = 1;
$tag['MARC'] = 1;
$tag['MARL'] = 1;
$tag['MARR'] = 1;
$tag['MARS'] = 1;
$tag['MEDI'] = 1;
$tag['NAME'] = 1;
$tag['NATI'] = 1;
$tag['NATU'] = 1;
$tag['NCHI'] = 1;
$tag['NICK'] = 1;
$tag['NMR']  = 1;
$tag['NOTE'] = 1;
$tag['NPFX'] = 1;
$tag['NSFX'] = 1;
$tag['OBJE'] = 1;
$tag['OCCU'] = 1;
$tag['ORDI'] = 1;
$tag['ORDN'] = 1;
$tag['PAGE'] = 1;
$tag['PEDI'] = 1;
$tag['PHON'] = 1;
$tag['PLAC'] = 1;
$tag['POST'] = 1;
$tag['PROB'] = 1;
$tag['PROP'] = 1;
$tag['PUBL'] = 1;
$tag['QUAY'] = 1;
$tag['REFN'] = -1;
$tag['RELA'] = 1;
$tag['RELI'] = 1;
$tag['REPO'] = 1;
$tag['RESI'] = 1;
$tag['RESN'] = 1;
$tag['RETI'] = 1;
$tag['RFN']  = -1;
$tag['RIN']  = -1;
$tag['ROLE'] = 1;
$tag['SEX']  = 1;
$tag['SLGC'] = -1;
$tag['SLGS'] = -1;
$tag['SOUR'] = 1;
$tag['SPFX'] = 1;
$tag['SSN']  = 1;
$tag['STAE'] = 1;
$tag['STAT'] = -1;
$tag['SUBM'] = 1;
$tag['SUBN'] = 1;
$tag['SURN'] = 1;
$tag['TEMP'] = -1;
$tag['TEXT'] = 1;
$tag['TIME'] = 1;
$tag['TITL'] = 1;
$tag['TRLR'] = 1;
$tag['TYPE'] = 1;
$tag['VERS'] = 1;
$tag['WIFE'] = 1;
$tag['WILL'] = 1;

/*
	$gedType['']['min'] = 0; //taille minimum
	$gedType['']['max'] = 0; //taille maximum
	$gedType['']['choix'] = array( ); //choix fixe
	$gedType['']['lien'][] = array( ); //references vers un autre type
	$gedType['']['separateur'][] = array( ); //separateur entre les references (lien)
	$gedType['']['terminal'][] = true; //type devant être verifié par une fct autre
	/**
   * si lien est un array alors chaque élément est séparé par separateur aura le type
   *    correspondant à chaque élément du tableau
   * si lien est une chaine et que separateur existe alors chaque élément séparé
   *    par le separateur aura le type défini par la chaine
   * si le lien est une chaine et qu'il n'y a pas de separateur, il n'y a qu'un seul élément
   *    ayant le type défini par la chaine

*/

$gedType['ADDRESS_CITY']['min'] = 1;
$gedType['ADDRESS_CITY']['max'] = 60;

$gedType['ADDRESS_COUNTRY']['min'] = 1;
$gedType['ADDRESS_COUNTRY']['max'] = 60;

$gedType['ADDRESS_LINE']['min'] = 1;
$gedType['ADDRESS_LINE']['max'] = 60;

$gedType['ADDRESS_LINE1']['min'] = 1;
$gedType['ADDRESS_LINE1']['max'] = 60;

$gedType['ADDRESS_LINE2']['min'] = 1;
$gedType['ADDRESS_LINE2']['max'] = 60;

$gedType['ADDRESS_POSTAL_CODE']['min'] = 1;
$gedType['ADDRESS_POSTAL_CODE']['max'] = 10;

$gedType['ADDRESS_STATE']['min'] = 1;
$gedType['ADDRESS_STATE']['max'] = 60;

$gedType['ADOPTED_BY_WICH_PARENT']['min'] = 1;
$gedType['ADOPTED_BY_WICH_PARENT']['max'] = 4;
$gedType['ADOPTED_BY_WICH_PARENT']['choix'] = array ('HUSB', 'WIFE', 'BOTH');

$gedType['AGE_AT_EVENT']['min'] = 1;
$gedType['AGE_AT_EVENT']['max'] = 12;
$gedType['AGE_AT_EVENT']['terminal'] = true;

$gedType['ANCESTRAL_FILE_NUMBER']['min'] = 1;
$gedType['ANCESTRAL_FILE_NUMBER']['max'] = 12;

$gedType['APPROVED_SYSTEM_ID']['min'] = 1;
$gedType['APPROVED_SYSTEM_ID']['max'] = 20;

$gedType['ATTRIBUTE_TYPE']['min'] = 1;
$gedType['ATTRIBUTE_TYPE']['max'] = 4;
$gedType['ATTRIBUTE_TYPE']['choix'] = array ('CAST', 'EDUC', 'NATI', 'OCCU', 'PROP', 'RELI', 'RESI', 'TITL');

$gedType['AUTOMATED_RECORD_ID']['min'] = 1;
$gedType['AUTOMATED_RECORD_ID']['max'] = 12;

$gedType['CASTE_NAME']['min'] = 1;
$gedType['CASTE_NAME']['max'] = 90;

$gedType['CAUSE_OF_EVENT']['min'] = 1;
$gedType['CAUSE_OF_EVENT']['max'] = 90;

$gedType['CERTAINTY_ASSESSMENT']['min'] = 1;
$gedType['CERTAINTY_ASSESSMENT']['max'] = 1;
$gedType['CERTAINTY_ASSESSMENT']['choix'] = array (0, 1, 2, 3);

$gedType['CHANGE_DATE']['min'] = 10;
$gedType['CHANGE_DATE']['max'] = 11;
$gedType['CHANGE_DATE']['lien'][] = 'DATE_EXACT';

$gedType['CHARACTER_SET']['min'] = 1;
$gedType['CHARACTER_SET']['max'] = 8;
$gedType['CHARACTER_SET']['choix'] = array ('ANSEL', 'UNICODE', 'ASCII');

$gedType['COPYRIGHT_GEDCOM_FILE']['min'] = 1;
$gedType['COPYRIGHT_GEDCOM_FILE']['max'] = 90;

$gedType['COPYRIGHT_SOURCE_DATA']['min'] = 1;
$gedType['COPYRIGHT_SOURCE_DATA']['max'] = 90;

$gedType['COUNT_OF_CHILDREN']['min'] = 1;
$gedType['COUNT_OF_CHILDREN']['max'] = 3;

$gedType['COUNT_OF_MARRIAGES']['min'] = 1;
$gedType['COUNT_OF_MARRIAGES']['max'] = 3;


$gedType['DATE']['min'] = 4;
$gedType['DATE']['max'] = 35;
/*$gedType['DATE']['lien'][0] = array ('DATE_CALENDAR_ESCAPE', 'DATE_CALENDAR');
$gedType['DATE']['separateur'][0] = ' ';
$gedType['DATE']['lien'][1] = 'DATE_CALENDAR';
*/
$gedType['DATE']['terminal'] = true;


$gedType['DATE_APPROXIMATED_ESCAPE']['min'] = 3;
$gedType['DATE_APPROXIMATED_ESCAPE']['max'] = 3;
$gedType['DATE_APPROXIMATED_ESCAPE']['choix'] = array ('ABT', 'CAL', 'EST');

$gedType['DATE_APPROXIMATED']['min'] = 4;
$gedType['DATE_APPROXIMATED']['max'] = 35;
$gedType['DATE_APPROXIMATED']['lien'][] = array ('DATE_APPROXIMATED_ESCAPE', 'DATE');
$gedType['DATE_APPROXIMATED']['separateur'][] = ' ';

$gedType['DATE_CALENDAR']['min'] = 4;
$gedType['DATE_CALENDAR']['max'] = 35;
$gedType['DATE_CALENDAR']['lien'][] = 'DATE_GREG';
$gedType['DATE_CALENDAR']['lien'][] = 'DATE_JULN';
$gedType['DATE_CALENDAR']['lien'][] = 'DATE_HEBR';
$gedType['DATE_CALENDAR']['lien'][] = 'DATE_FREN';
//$gedType['DATE_CALENDAR']['lien'][] = 'DATE_FUTURE';

$gedType['DATE_CALENDAR_ESCAPE']['min'] = 4;
$gedType['DATE_CALENDAR_ESCAPE']['max'] = 15;
$gedType['DATE_CALENDAR_ESCAPE']['choix'] = array ('@#DHEBREW@', '@#DROMAN@', '@#DGREGORIAN@', '@#DJULIAN@', '@#DUNKNOW@');

$gedType['DATE_EXACT']['min'] = 10;
$gedType['DATE_EXACT']['max'] = 11;
$gedType['DATE_EXACT']['lien'][] = array ('DAY', 'MONTH', 'YEAR_GREG');
$gedType['DATE_EXACT']['separateur'][] = ' ';

$gedType['DATE_FREN']['min'] = 4;
$gedType['DATE_FREN']['max'] = 35;
$gedType['DATE_FREN']['lien'][] = 'YEAR';
$gedType['DATE_FREN']['lien'][] = array ('MONTH_FREN', 'YEAR');
$gedType['DATE_FREN']['separateur'][] = ' ';
$gedType['DATE_FREN']['lien'][] = array ('DAY', 'MONTH_FREN', 'YEAR');
$gedType['DATE_FREN']['separateur'][] = ' ';

$gedType['DATE_GREG']['min'] = 4;
$gedType['DATE_GREG']['max'] = 35;
$gedType['DATE_GREG']['lien'][] = 'YEAR_GREG';
$gedType['DATE_GREG']['lien'][] = array ('MONTH', 'YEAR_GREG');
$gedType['DATE_GREG']['separateur'][] = ' ';
$gedType['DATE_GREG']['lien'][] = array ('DAY', 'MONTH', 'YEAR_GREG');
$gedType['DATE_GREG']['separateur'][] = ' ';

$gedType['DATE_HEBR']['min'] = 4;
$gedType['DATE_HEBR']['max'] = 35;
$gedType['DATE_HEBR']['lien'][] = 'YEAR';
$gedType['DATE_HEBR']['lien'][] = array ('MONTH_HEBR', 'YEAR');
$gedType['DATE_HEBR']['separateur'][] = ' ';
$gedType['DATE_HEBR']['lien'][] = array ('DAY', 'MONTH_HEBR', 'YEAR');
$gedType['DATE_HEBR']['separateur'][] = ' ';

$gedType['DATE_JULN']['min'] = 4;
$gedType['DATE_JULN']['max'] = 35;
$gedType['DATE_JULN']['lien'][] = 'YEAR';
$gedType['DATE_JULN']['lien'][] = array ('MONTH', 'YEAR');
$gedType['DATE_JULN']['separateur'][] = ' ';
$gedType['DATE_JULN']['lien'][] = array ('DAY', 'MONTH', 'YEAR');
$gedType['DATE_JULN']['separateur'][] = ' ';

$gedType['DATE_LDS_ORD']['min'] = 4;
$gedType['DATE_LDS_ORD']['max'] = 35;
$gedType['DATE_LDS_ORD']['lien'][] = 'DATE_VALUE';

$gedType['DATE_PERIOD_FROM']['min'] = 4;
$gedType['DATE_PERIOD_FROM']['max'] = 4;
$gedType['DATE_PERIOD_FROM']['choix'] = array ('FROM');

$gedType['DATE_PERIOD_TO']['min'] = 2;
$gedType['DATE_PERIOD_TO']['max'] = 2;
$gedType['DATE_PERIOD_TO']['choix'] = array ('TO');


$gedType['DATE_PERIOD']['min'] = 7;
$gedType['DATE_PERIOD']['max'] = 35;
/*
$gedType['DATE_PERIOD']['lien'][] = array ('DATE_PERIOD_FROM', 'DATE');
$gedType['DATE_PERIOD']['separateur'][] = ' ';
$gedType['DATE_PERIOD']['lien'][] = array ('DATE_PERIOD_TO', 'DATE');
$gedType['DATE_PERIOD']['separateur'][] = ' ';
$gedType['DATE_PERIOD']['lien'][] = array ('DATE_PERIOD_FROM', 'DATE', 'DATE_PERIOD_TO', 'DATE');
$gedType['DATE_PERIOD']['separateur'][] = ' ';
*/
$gedType['DATE_PERIOD']['terminal'] = true;

$gedType['DATE_PHRASE']['min'] = 1;
$gedType['DATE_PHRASE']['max'] = 35;
$gedType['DATE_PHRASE']['lien'] = 'TEXT';

$gedType['DATE_RANGE_BEF']['min'] = 3;
$gedType['DATE_RANGE_BEF']['max'] = 3;
$gedType['DATE_RANGE_BEF']['choix'] = array ('BEF');

$gedType['DATE_RANGE_AFT']['min'] = 3;
$gedType['DATE_RANGE_AFT']['max'] = 3;
$gedType['DATE_RANGE_AFT']['choix'] = array ('AFT');

$gedType['DATE_RANGE_BET']['min'] = 3;
$gedType['DATE_RANGE_BET']['max'] = 3;
$gedType['DATE_RANGE_BET']['choix'] = array ('BET');

$gedType['DATE_RANGE_AND']['min'] = 3;
$gedType['DATE_RANGE_AND']['max'] = 3;
$gedType['DATE_RANGE_AND']['choix'] = array ('AND');

$gedType['DATE_RANGE']['min'] = 8;
$gedType['DATE_RANGE']['max'] = 35;
$gedType['DATE_RANGE']['lien'][] = array ('DATE_RANGE_BEF', 'DATE');
$gedType['DATE_RANGE']['separateur'][] = ' ';
$gedType['DATE_RANGE']['lien'][] = array ('DATE_RANGE_AFT', 'DATE');
$gedType['DATE_RANGE']['separateur'][] = ' ';
$gedType['DATE_RANGE']['lien'][] = array ('DATE_RANGE_BET', 'DATE', 'DATE_RANGE_AND', 'DATE');
$gedType['DATE_RANGE']['separateur'][] = ' ';

$gedType['DAY']['min'] = 1;
$gedType['DAY']['max'] = 2;
$gedType['DAY']['terminal'] = true;

$gedType['DATE_VALUE']['min'] = 1;
$gedType['DATE_VALUE']['max'] = 35;
$gedType['DATE_VALUE']['terminal'] = true;

$gedType['DESCRIPTIVE_TITLE']['min'] = 1;
$gedType['DESCRIPTIVE_TITLE']['max'] = 248;

$gedType['DIGIT']['min'] = 1;
$gedType['DIGIT']['max'] = 1;
$gedType['DIGIT']['choix'] = array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

$gedType['ENCODED_MULTIMEDIA_LINE']['min'] = 1;
$gedType['ENCODED_MULTIMEDIA_LINE']['max'] = 87;

$gedType['ENTRY_RECORDING_DATE']['min'] = 1;
$gedType['ENTRY_RECORDING_DATE']['max'] = 90;
$gedType['ENTRY_RECORDING_DATE']['lien'][] = 'DATE_VALUE';

$gedType['EVENT_ATTRIBUTE_TYPE']['min'] = 1;
$gedType['EVENT_ATTRIBUTE_TYPE']['max'] = 15;
$gedType['EVENT_ATTRIBUTE_TYPE']['lien'][] = 'EVENT_TYPE_INDIVIDUAL';
$gedType['EVENT_ATTRIBUTE_TYPE']['lien'][] = 'EVENT_TYPE_FAMILY';
$gedType['EVENT_ATTRIBUTE_TYPE']['lien'][] = 'ATTRIBUTE_TYPE';

$gedType['EVENT_DESCRIPTOR']['min'] = 0;
$gedType['EVENT_DESCRIPTOR']['max'] = 90;

$gedType['EVENT_TYPE_CITED_FROM']['min'] = 1;
$gedType['EVENT_TYPE_CITED_FROM']['max'] = 15;
$gedType['EVENT_TYPE_CITED_FROM']['lien'][] = 'EVENT_ATTRIBUTE_TYPE';

$gedType['EVENT_TYPE_FAMILY']['min'] = 3;
$gedType['EVENT_TYPE_FAMILY']['max'] = 4;
$gedType['EVENT_TYPE_FAMILY']['choix'] = array ('ANUL', 'CENS', 'DIV', 'DIVF', 'ENGA', 'MARR', 'MARB', 'MARC', 'MARL', 'MARS', 'EVEN');

$gedType['EVENT_TYPE_INDIVIDUAL']['min'] = 3;
$gedType['EVENT_TYPE_INDIVIDUAL']['max'] = 4;
$gedType['EVENT_TYPE_INDIVIDUAL']['choix'] = array ('ADOP', 'BIRT', 'BAPM', 'BARM', 'BASM', 'BLES', 'BURI', 'CENS', 'CHR', 'CHRA', 'CONF', 'CREM', 'DEAT', 'EMIG', 'FCOM', 'GRAD', 'IMMI', 'NATU', 'ORDN', 'RETI', 'PROB', 'WILL', 'EVEN');

$gedType['EVENT_RECORDED']['min'] = 1;
$gedType['EVENT_RECORDED']['max'] = 90;
$gedType['EVENT_RECORDED']['lien'][] = 'EVENT_ATTRIBUTE_TYPE';
$gedType['EVENT_RECORDED']['separateur'][] = ',';

$gedType['FILE_NAME']['min'] = 1;
$gedType['FILE_NAME']['max'] = 90;

$gedType['GEDCOM_CONTENT_DESCRIPTION']['min'] = 0;
$gedType['GEDCOM_CONTENT_DESCRIPTION']['max'] = 248;

$gedType['GEDCOM_FORM']['min'] = 14;
$gedType['GEDCOM_FORM']['max'] = 20;
$gedType['GEDCOM_FORM']['choix'] = array( 'LINEAGE-LINKED' );

$gedType['GENERATIONS_OF_ANCESTORS']['min'] = 1;
$gedType['GENERATIONS_OF_ANCESTORS']['max'] = 4;

$gedType['GENERATIONS_OF_DESCENDANTS']['min'] = 1;
$gedType['GENERATIONS_OF_DESCENDANTS']['max'] = 4;

$gedType['LANGUAGE_ID']['min'] = 1;
$gedType['LANGUAGE_ID']['max'] = 15;
$gedType['LANGUAGE_ID']['choix'] = array( 'ENGLISH', 'FRENCH' );
// TODO , ajout des langues un regal !!

$gedType['LANGUAGE_OF_TEXT']['min'] = 1;
$gedType['LANGUAGE_OF_TEXT']['max'] = 15;
$gedType['LANGUAGE_OF_TEXT']['lien'][] = 'LANGUAGE_ID';

$gedType['LANGUAGE_PREFERENCE']['min'] = 1;
$gedType['LANGUAGE_PREFERENCE']['max'] = 90;
$gedType['LANGUAGE_PREFERENCE']['lien'][] = 'LANGUAGE_ID';
$gedType['LANGUAGE_PREFERENCE']['separateur'][] = ' ';

$gedType['LDS_BAPTISM_DATE_STATUS']['min'] = 5;
$gedType['LDS_BAPTISM_DATE_STATUS']['max'] = 10;
$gedType['LDS_BAPTISM_DATE_STATUS']['choix'] = array ('CHILD', 'CLEARED', 'COMPLETED', 'INFANT', 'PRE-1970', 'QUALIFIED', 'STILLBORN', 'SUBMITTED', 'UNCLEARED');

$gedType['LDS_CHILD_SEALING_DATE_STATUS']['min'] = 5;
$gedType['LDS_CHILD_SEALING_DATE_STATUS']['max'] = 10;
$gedType['LDS_CHILD_SEALING_DATE_STATUS']['choix'] = array ('BIC', 'CLEARED', 'COMPLETED', 'DNS', 'PRE-1970', 'QUALIFIED', 'STILLBORN', 'SUBMITTED', 'UNCLEARED');

$gedType['LDS_SPOUSE_SEALING_DATE_STATUS']['min'] = 3;
$gedType['LDS_SPOUSE_SEALING_DATE_STATUS']['max'] = 10;
$gedType['LDS_SPOUSE_SEALING_DATE_STATUS']['choix'] = array ('CANCELED', 'CLEARED', 'COMPLETED', 'DNS', 'DNS/CAN', 'PRE-1970', 'QUALIFIED', 'SUBMITTED', 'UNCLEARED');

$gedType['MONTH']['min'] = 3;
$gedType['MONTH']['max'] = 3;
$gedType['MONTH']['choix'] = array ('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC');

$gedType['MONTH_FREN']['min'] = 4;
$gedType['MONTH_FREN']['max'] = 4;
$gedType['MONTH_FREN']['choix'] = array ('VEND', 'BRUM', 'FRIM', 'NIVO', 'PLUV', 'VENT', 'GERM', 'FLOR', 'PRAI', 'MESS', 'THER', 'FRUC', 'COMP');

$gedType['MONTH_HERB']['min'] = 3;
$gedType['MONTH_HERB']['max'] = 3;
$gedType['MONTH_HERB']['choix'] = array ('TSH', 'CSH', 'KSL', 'TVT', 'SHV', 'ADR', 'ADS', 'NSN', 'IYR', 'SVN', 'TMZ', 'AAV', 'ELL');

$gedType['MULTIMEDIA_FILE_REFERENCE']['min'] = 1;
$gedType['MULTIMEDIA_FILE_REFERENCE']['max'] = 30;

$gedType['MULTIMEDIA_FORMAT']['min'] = 3;
$gedType['MULTIMEDIA_FORMAT']['max'] = 4;
$gedType['MULTIMEDIA_FORMAT']['choix'] = array ('BMP', 'GIF', 'JPEG', 'OLE', 'TIFF', 'WAV');

$gedType['NAME_OF_BUSINESS']['min'] = 1;
$gedType['NAME_OF_BUSINESS']['max'] = 90;

$gedType['NAME_OF_FAMILY_FILE']['min'] = 1;
$gedType['NAME_OF_FAMILY_FILE']['max'] = 120;

$gedType['NAME_OF_PRODUCT']['min'] = 1;
$gedType['NAME_OF_PRODUCT']['max'] = 90;

$gedType['NAME_OF_REPOSITORY']['min'] = 1;
$gedType['NAME_OF_REPOSITORY']['max'] = 90;

$gedType['NAME_OF_SOURCE_DATA']['min'] = 1;
$gedType['NAME_OF_SOURCE_DATA']['max'] = 90;

$gedType['NAME_PERSONAL']['min'] = 1;
$gedType['NAME_PERSONAL']['max'] = 120;
$gedType['NAME_PERSONAL']['terminal'] = true;

$gedType['NAME_PIECE']['min'] = 1;
$gedType['NAME_PIECE']['max'] = 90;

$gedType['NAME_PIECE_GIVEN']['min'] = 1;
$gedType['NAME_PIECE_GIVEN']['max'] = 120;
$gedType['NAME_PIECE_GIVEN']['lien'][] = 'NAME_PIECE';
$gedType['NAME_PIECE_GIVEN']['separateur'][] = ',';

$gedType['NAME_PIECE_NICKNAME']['min'] = 1;
$gedType['NAME_PIECE_NICKNAME']['max'] = 30;
$gedType['NAME_PIECE_NICKNAME']['lien'][] = 'NAME_PIECE';
$gedType['NAME_PIECE_NICKNAME']['separateur'][] = ',';

$gedType['NAME_PIECE_PREFIX']['min'] = 1;
$gedType['NAME_PIECE_PREFIX']['max'] = 30;
$gedType['NAME_PIECE_PREFIX']['lien'][] = 'NAME_PIECE';
$gedType['NAME_PIECE_PREFIX']['separateur'][] = ',';

$gedType['NAME_PIECE_SUFFIX']['min'] = 1;
$gedType['NAME_PIECE_SUFFIX']['max'] = 30;
$gedType['NAME_PIECE_SUFFIX']['lien'][] = 'NAME_PIECE';
$gedType['NAME_PIECE_SUFFIX']['separateur'][] = ',';

$gedType['NAME_PIECE_SURNAME']['min'] = 1;
$gedType['NAME_PIECE_SURNAME']['max'] = 120;
$gedType['NAME_PIECE_SURNAME']['lien'][] = 'NAME_PIECE';
$gedType['NAME_PIECE_SURNAME']['separateur'][] = ',';

$gedType['NAME_PIECE_SURNAME_PREFIX']['min'] = 1;
$gedType['NAME_PIECE_SURNAME_PREFIX']['max'] = 30;
$gedType['NAME_PIECE_SURNAME_PREFIX']['lien'][] = 'NAME_PIECE';
$gedType['NAME_PIECE_SURNAME_PREFIX']['separateur'][] = ',';

$gedType['NATIONAL_ID_NUMBER']['min'] = 1;
$gedType['NATIONAL_ID_NUMBER']['max'] = 30;

$gedType['NATIONAL_OR_TRIBAL_ORIGIN']['min'] = 1;
$gedType['NATIONAL_OR_TRIBAL_ORIGIN']['max'] = 120;

$gedType['NEW_TAG']['min'] = 1;
$gedType['NEW_TAG']['max'] = 15;

$gedType['NOBILITY_TYPE_TITLE']['min'] = 1;
$gedType['NOBILITY_TYPE_TITLE']['max'] = 120;

$gedType['NUMBER']['min'] = 0;
$gedType['NUMBER']['max'] = 9;
$gedType['NUMBER']['terminal'] = true;

$gedType['OCCUPATION']['min'] = 1;
$gedType['OCCUPATION']['max'] = 90;

$gedType['ORDINANCE_PROCESS_FLAG']['min'] = 2;
$gedType['ORDINANCE_PROCESS_FLAG']['max'] = 3;
$gedType['ORDINANCE_PROCESS_FLAG']['choix'] = array ('YES', 'NO');

$gedType['PEDIGREE_LINKAGE_TYPE']['min'] = 5;
$gedType['PEDIGREE_LINKAGE_TYPE']['max'] = 7;
$gedType['PEDIGREE_LINKAGE_TYPE']['choix'] = array ('ADOPTED', 'BIRTH', 'FOSTER', 'SEALING');

$gedType['PERMANENT_RECORD_FILE_NUMBER']['min'] = 1;
$gedType['PERMANENT_RECORD_FILE_NUMBER']['max'] = 90;
$gedType['PERMANENT_RECORD_FILE_NUMBER']['lien'][] = array ('REGISTERED_RESSOURCE_IDENTIFIER', 'RECORD_IDENTIFIER');
$gedType['PERMANENT_RECORD_FILE_NUMBER']['separateur'][] = ':';

$gedType['PHONE_NUMBER']['min'] = 1;
$gedType['PHONE_NUMBER']['max'] = 25;

$gedType['PHYSICAL_DESCRIPTION']['min'] = 1;
$gedType['PHYSICAL_DESCRIPTION']['max'] = 248;

$gedType['PLACE_HIERARCHY']['min'] = 1;
$gedType['PLACE_HIERARCHY']['max'] = 120;

$gedType['PLACE_LIVING_ORDINANCE']['min'] = 1;
$gedType['PLACE_LIVING_ORDINANCE']['max'] = 120;
$gedType['PLACE_LIVING_ORDINANCE']['lien'][] = 'PLACE_VALUE';

$gedType['PLACE_VALUE']['min'] = 1;
$gedType['PLACE_VALUE']['max'] = 120;

$gedType['POSSESSIONS']['min'] = 1;
$gedType['POSSESSIONS']['max'] = 248;

$gedType['PUBLICATION_DATE']['min'] = 10;
$gedType['PUBLICATION_DATE']['max'] = 11;
$gedType['PUBLICATION_DATE']['lien'][] = 'DATE_EXACT';

$gedType['RECEIVING_SYSTEM_NAME']['min'] = 1;
$gedType['RECEIVING_SYSTEM_NAME']['max'] = 20;

$gedType['RECORD_TYPE']['min'] = 3;
$gedType['RECORD_TYPE']['max'] = 4;
$gedType['RECORD_TYPE']['choix'] = array ('FAM', 'INDI', 'NOTE', 'OBJE', 'REPO', 'SOUR', 'SUBM', 'SUBN');

$gedType['REGISTERED_RESSOURCE_IDENTIFIER']['min'] = 1;
$gedType['REGISTERED_RESSOURCE_IDENTIFIER']['max'] = 25;

$gedType['RELATION_IS_DESCRIPTOR']['min'] = 1;
$gedType['RELATION_IS_DESCRIPTOR']['max'] = 25;

$gedType['RELIGIOUS_AFFILIATION']['min'] = 1;
$gedType['RELIGIOUS_AFFILIATION']['max'] = 90;

$gedType['RESPONSIBLE_AGENCY']['min'] = 1;
$gedType['RESPONSIBLE_AGENCY']['max'] = 120;

$gedType['RESTRICTION_NOTICE']['min'] = 6;
$gedType['RESTRICTION_NOTICE']['max'] = 7;
$gedType['RESTRICTION_NOTICE']['choix'] = array ('LOCKED', 'PRIVACY');

$gedType['ROLE_DESCRIPTOR']['min'] = 1;
$gedType['ROLE_DESCRIPTOR']['max'] = 25;

$gedType['ROLE_IN_EVENT']['min'] = 1;
$gedType['ROLE_IN_EVENT']['max'] = 25;
$gedType['ROLE_IN_EVENT']['choix'] = array ('CHIL', 'HUSB', 'WIFE', 'MOTH', 'FATH', 'SPOU');
$gedType['ROLE_IN_EVENT']['lien'][] = 'TEXT';

$gedType['SCHOLASTIC_ACHIEVEMENT']['min'] = 1;
$gedType['SCHOLASTIC_ACHIEVEMENT']['max'] = 248;

$gedType['SEX_VALUE']['min'] = 1;
$gedType['SEX_VALUE']['max'] = 7;
$gedType['SEX_VALUE']['choix'] = array ('M', 'F');

$gedType['SOCIAL_SECURITY_NUMBER']['min'] = 9;
$gedType['SOCIAL_SECURITY_NUMBER']['max'] = 11;

$gedType['SOURCE_CALL_NUMBER']['min'] = 1;
$gedType['SOURCE_CALL_NUMBER']['max'] = 120;

$gedType['SOURCE_DESCRIPTION']['min'] = 1;
$gedType['SOURCE_DESCRIPTION']['max'] = 248;

$gedType['SOURCE_DESCRIPTIVE_TITLE']['min'] = 1;
$gedType['SOURCE_DESCRIPTIVE_TITLE']['max'] = 248;

$gedType['SOURCE_FILED_BY_ENTRY']['min'] = 1;
$gedType['SOURCE_FILED_BY_ENTRY']['max'] = 60;

$gedType['SOURCE_JURISDICTION_PLACE']['min'] = 1;
$gedType['SOURCE_JURISDICTION_PLACE']['max'] = 120;
$gedType['SOURCE_JURISDICTION_PLACE']['lien'][] = 'PLACE_VALUE';

$gedType['SOURCE_MEDIA_TYPE']['min'] = 1;
$gedType['SOURCE_MEDIA_TYPE']['max'] = 15;
$gedType['SOURCE_MEDIA_TYPE']['choix'] = array ('AUDIO', 'BOOK', 'CARD', 'ELECTRONIC', 'FICHE', 'FILM', 'MAGAZINE', 'MANUSCRIPT', 'MAP', 'NEWSPAPER', 'PHOTO', 'TOMBSTONE', 'VIDEO');

$gedType['SOURCE_ORIGINATOR']['min'] = 1;
$gedType['SOURCE_ORIGINATOR']['max'] = 248;

$gedType['SOURCE_PUBLICATION_FACTS']['min'] = 1;
$gedType['SOURCE_PUBLICATION_FACTS']['max'] = 248;

$gedType['SUBMITTER_NAME']['min'] = 1;
$gedType['SUBMITTER_NAME']['max'] = 60;

$gedType['SUBMITTER_REGISTERED_RFN']['min'] = 1;
$gedType['SUBMITTER_REGISTERED_RFN']['max'] = 30;

$gedType['SUBMITTER_TEXT']['min'] = 0;
$gedType['SUBMITTER_TEXT']['max'] = 248;

$gedType['TEMPLE_CODE']['min'] = 4;
$gedType['TEMPLE_CODE']['max'] = 5;

$gedType['TEXT']['min'] = 1;
$gedType['TEXT']['max'] = 248;

$gedType['TEXT_FROM_SOURCE']['min'] = 1;
$gedType['TEXT_FROM_SOURCE']['max'] = 248;
$gedType['TEXT_FROM_SOURCE']['lien'][] = 'TEXT';

$gedType['TIME_VALUE']['min'] = 1;
$gedType['TIME_VALUE']['max'] = 12;
$gedType['TIME_VALUE']['terminal'] = true;

$gedType['TRANSMISSION_DATE']['min'] = 10;
$gedType['TRANSMISSION_DATE']['max'] = 11;
$gedType['TRANSMISSION_DATE']['lien'][] = 'DATE_EXACT';

$gedType['USER_REFERENCE_NUMBER']['min'] = 1;
$gedType['USER_REFERENCE_NUMBER']['max'] = 21;

$gedType['USER_REFERENCE_TYPE']['min'] = 1;
$gedType['USER_REFERENCE_TYPE']['max'] = 40;

$gedType['VERSION_NUMBER']['min'] = 1;
$gedType['VERSION_NUMBER']['max'] = 15;

$gedType['WHERE_WITHIN_SOURCE']['min'] = 1;
$gedType['WHERE_WITHIN_SOURCE']['max'] = 248;

$gedType['XREF']['min'] = 1;
$gedType['XREF']['max'] = 22;
$gedType['XREF']['terminal'] = true;

/*
$gedType['XREF:FAM']['min'] = 1;
$gedType['XREF:FAM']['max'] = 22;

$gedType['XREF:INDI']['min'] = 1;
$gedType['XREF:INDI']['max'] = 22;

$gedType['XREF:NOTE']['min'] = 1;
$gedType['XREF:NOTE']['max'] = 22;

$gedType['XREF:OBJE']['min'] = 1;
$gedType['XREF:OBJE']['max'] = 22;

$gedType['XREF:SOUR']['min'] = 1;
$gedType['XREF:SOUR']['max'] = 22;

$gedType['XREF:SUBM']['min'] = 1;
$gedType['XREF:SUBM']['max'] = 22;

$gedType['XREF:SUBN']['min'] = 1;
$gedType['XREF:SUBN']['max'] = 22;
*/

$gedType['YEAR']['min'] = 3;
$gedType['YEAR']['max'] = 4;
$gedType['YEAR']['terminal'] = true;

$gedType['YEAR_GREG']['min'] = 3;
$gedType['YEAR_GREG']['max'] = 7;
$gedType['YEAR_GREG']['terminal'] = true;

/**
 * si le format est valide retourne false
 * sinon retourne la description de l'erreur
 */
function typeCheck($format, $desc, $init = true) {
	global $gedType;

	$err = '';

	if (!isset($gedType[$format]) || !is_array($gedType[$format]))
		return 'Format non d&eacute;fini : '.$format;

	$lg = strlen($desc);
	if ($init && ($lg < $gedType[$format]['min'] || $lg > $gedType[$format]['max']))
		return 'Taille non conforme : '.$format;

	if (isset($gedType[$format]['choix']) && is_array($gedType[$format]['choix']))
		foreach ($gedType[$format]['choix'] as $choix){
			if (strtoupper($desc) == $choix)
				return false;
		}

	if (isset($gedType[$format]['lien']) && is_array($gedType[$format]['lien'])) {
		for ($i = 0; $i < count($gedType[$format]['lien']); $i ++) {
			if (is_string($gedType[$format]['lien'][$i])) {
				if (isset ($gedType[$format]['separateur'][$i])) {
					// elts séparés de format identique
					$elts = explode($gedType[$format]['separateur'][$i], $desc);
					foreach ($elts as $elt)
						if ($err = typeCheck($gedType[$format]['lien'][$i], $elt, false))
							break;
					if (!$err)
						return false;
				} else { // elt unique (lien direct)
					if (!typeCheck($gedType[$format]['lien'][$i], $desc, false))
						return false;
				}
			} else
				if (isset($gedType[$format]['lien'][$i]) && is_array($gedType[$format]['lien'][$i])) {
					// liens séparés de format différent
					$elts = explode($gedType[$format]['separateur'][$i], $desc);
					if (count($elts) != count($gedType[$format]['lien'][$i]))
						$err = "Nombre d'elements incompatible format ".$format;
					for ($j = 0; $j < count($elts); $j++)
					{
						if ($err = typeCheck($gedType[$format]['lien'][$i][$j], $elts[$j], false))
							break;
					}
					if (!$err)
						return false;
				}
		}
	}

	if (!empty ($gedType[$format]['terminal'])) {
		switch ($format) {
			case 'AGE_AT_EVENT' :
				$ereg = "/^[<>]{0,1}[ ]*([0-9]{1,2}y [0-9]{1,2}m [0-9]{1,3}d|[0-9]{1,2}y|[0-9]{1,2}m|[0-9]{1,3}d|[0-9]{1,2}y [0-9]{1,2}m|[0-9]{1,2}y [0-9]{1,3}d|[0-9]{1,2}m [0-9]{1,3}d|CHILD|INFANT|STILLBORN)$/";
				if (preg_match($ereg, $desc) > 0)
					return false;
				break;
			case 'DATE' :
			case 'DATE_PERIOD' :
			case 'DATE_VALUE' :
				//TODO faire le test si besoin ;)
				return false;
				//break;
			case 'DAY' :
				$ereg = "/^[0-9]{1,2}$/";
				if (preg_match($ereg, $desc) > 0)
					return false;
				break;
			case 'NAME_PERSONAL' :
				$ereg = "/^(.+|\/[^\/]+\/|[^\/]+\/[^\/]+\/|\/[^\/]+\/.+|[^\/]+\/[^\/]+\/.+)$/";
				if (preg_match($ereg, $desc) > 0)
					return false;
				break;
			case 'TIME_VALUE' :
				$ereg = "/^([0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}.[0-9]{1,2}|[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}|[0-9]{1,2}:[0-9]{1,2})$/";
				if (preg_match($ereg, $desc) > 0)
					return false;
				break;
			case 'XREF' :
				$ereg = "/^@[^#].*@$/";
				if (preg_match($ereg, $desc) > 0)
					return false;
				break;
			case 'NUMBER' :

			case 'YEAR' :
				if (is_int($desc))
					return false;
				break;
			case 'YEAR_GREG' :
				$ereg = "/^([0-9]+|[0-9]+\/[0-9]{2})$/";
				if (preg_match($ereg, $desc) > 0)
					return false;
				break;

			default :
				$err = 'Format terminal non connue';
		}
		if (!$err)
			$err = 'Format terminal non conforme';
	}
	if (!$err && (isset ($gedType[$format]['lien']) || isset ($gedType[$format]['choix'])))
		$err = 'Format non conforme';
	return $err;
}
?>