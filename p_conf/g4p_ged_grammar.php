<?php
////////////////////////////////////////////////////////////////////////////////
// Valid tags and their context - see Gedcom 5.5.1, pages 23-65
////////////////////////////////////////////////////////////////////////////////
$DAY                                  ='(0?[1-9]|[12][0-9]|30|31)';
$MONTH                                ='(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)';
$YEAR                                 ='([0-9]{3,4})';
$YEAR_GREG                            ='([0-9]{3,4}(\/[0-9][0-9])?)';
$MONTH_FREN                           ='(VEND|BRUM|FRIM|NIVO|PLUV|VENT|GERM|FLOR|PRAI|MESS|THER|FRUC|COMP)';
$MONTH_HEBR                           ='(TSH|CSH|KSL|TVT|SHV|ADR|ADS|NSN|IYR|SVN|TMZ|AAV|ELL)';
$DATE_FREN                            ="(@#DFRENCH R@ (($DAY )?$MONTH_FREN )?$YEAR)";
$DATE_HEBR                            ="(@#DHEBREW@ (($DAY )?$MONTH_HEBR )?$YEAR)";
$DATE_GREG                            ="((@#DGREGORIAN@ )?((($DAY )?$MONTH )?$YEAR_GREG|$YEAR(B\.C\.)))";
$DATE_JULN                            ="(@#DJULIAN@ ((($DAY ?)$MONTH ?)$YEAR|$YEAR(B\.C\.)))";
$DATE                                 ="($DATE_GREG|$DATE_JULN|$DATE_HEBR|$DATE_FREN)";
$DATE_EXACT                           ="($DAY $MONTH $YEAR_GREG)";
$ATTRIBUTE_TYPE                       ='(CAST|EDUC|NATI|OCCU|PROP|RELI|RESI|TITL|FACT)';
$EVENT_TYPE_FAMILY                    ='(ANUL|CENS|DIV|DIVF|ENGA|MARR|MARB|MARC|MARL|MARS|EVEN)';
$EVENT_TYPE_INDIVIDUAL                ='(ADOP|BIRT|BAPM|BARM|BASM|BLES|BURI|CENS|CHR|CHRA|CONF|CREM|DEAT|EMIG|FCOM|GRAD|IMMI|NATU|ORDN|RETI|PROB|WILL|EVEN)';
$LANGUAGE_ID                          ='(Afrikaans|Albanian|Anglo-Saxon|Catalan|Catalan_Spn|Czech|Danish|Dutch|English|Esperanto|Estonain|Faroese|Finnish|French|German|Hawaiian|Hungarian|Icelandic|Indonesian|Italian|Latvian|Lithuanian|Navaho|Norwegian|Polish|Portuguese|Romanian|Serbo_Croa|Slovak|Slovene|Spanish|Swedish|Turkish|Wendic|Amharic|Arabic|Armenian|Assamese|Belorussian|Bengali|Braj|Bulgarian|Burmese|Cantonese|Church-Slavic|Dogri|Georgian|Greek|Gujarati|Hebrew|Hindi|Japanese|Kannada|Khmer|Konkani|Korean|Lahnda|Lao|Macedonian|Maithili|Malayalam|Mandrin|Manipuri|Marathi|Mewari|Nepali|Oriya|Pahari|Pali|Panjabi|Persian|Prakrit|Pusto|Rajasthani|Russian|Sanskrit|Serb|Tagalog|Tamil|Tehugu|Thai|Tibetan|Ukranian|Urdu|Vietnamese|Yiddish)';
//$NAME_TEXT                            ='\p{L}{1,120}';
//$NAME_PIECE                           ='\p{L}{1,90}';
// There is no widespread PCRE support for \p
$NAME_TEXT                            ='.{1,120}';
$NAME_PIECE                           ='.{1,90}';
$RECORD_IDENTIFIER                    ='[^:]{1,18}';
$REGISTERED_RESOURCE_IDENTIFIER       ='[^:]{1,25}';
$TEXT                                 ='.{1,248}';
$PLACE_TEXT                           =$TEXT;
$TAG['DATE_APPROXIMATED']             ="(ABT|CAL|EST) $DATE";
$TAG['DATE_VALUE']                    ="($DATE_FREN|$DATE_HEBR|$DATE_JULN|$DATE_GREG)";
$TAG['ADDRESS_CITY']                  ='.{1,60}';
$TAG['ADDRESS_COUNTRY']               ='.{1,60}';
$TAG['ADDRESS_EMAIL']                 ='.{5,120}';
$TAG['ADDRESS_FAX']                   ='.{1,60}';
$TAG['ADDRESS_LINE']                  ='.{1,60}';
$TAG['ADDRESS_LINE1']                 ='.{1,60}';
$TAG['ADDRESS_LINE2']                 ='.{1,60}';
$TAG['ADDRESS_LINE3']                 ='.{1,60}';
$TAG['ADDRESS_POSTAL_CODE']           ='.{1,10}';
$TAG['ADDRESS_STATE']                 ='.{1,60}';
$TAG['ADDRESS_WEB_PAGE']              ='.{5,120}';
$TAG['ADOPTED_BY_WHICH_PARENT']       ='(HUSB|WIFE|BOTH)';
$TAG['AGE_AT_EVENT']                  ='([<>]?)(CHILD|INFANT|STILLBORN|([0-9]+y?)?\s*([0-9]+m)?\s*([0-9]+d)?)';
$TAG['ANCESTRAL_FILE_NUMBER']         ='.{1,12}';
$TAG['APPROVED_SYSTEM_ID']            ='.{1,20}';
$TAG['ATTRIBUTE_DESCRIPTOR']          ='.{1,90}';
$TAG['AUTOMATED_RECORD_ID']           ='.{1,12}';
$TAG['CASTE_NAME']                    ='.{1,90}';
$TAG['CAUSE_OF_EVENT']                ='.{1,90}';
$TAG['CERTAINTY_ASSESSMENT']          ='[0-3]';
$TAG['CHANGE_DATE']                   =$DATE_EXACT;
$TAG['CHARACTER_SET']                 ='(ANSEL|UTF-8|UNICODE|ASCII)';
$TAG['CHILD_LINKAGE_STATUS']          ='(challenged|disproven|proven)';
$TAG['COPYRIGHT_GEDCOM_FILE']         ='.{1,90}';
$TAG['COPYRIGHT_SOURCE_DATA']         ='.{1,90}';
$TAG['COUNT_OF_CHILDREN']             ='[0-9]{1,3}';
$TAG['COUNT_OF_MARRIAGES']            ='[0-9]{1,3}';
$TAG['DATE_LDS_ORD']                  =$TAG['DATE_VALUE'];
$TAG['DATE_PERIOD']                   ="FROM $DATE|TO $DATE|FROM $DATE TO $DATE";
$TAG['DATE_PHRASE']                   ='.{1,35}';
$TAG['DATE_RANGE']                    ="((BEF|AFT) $DATE|BET $DATE AND $DATE)";
$TAG['DATE_VALUE']                    ="($DATE|${TAG['DATE_PERIOD']}|${TAG['DATE_RANGE']}|${TAG['DATE_APPROXIMATED']}|INT $DATE \(${TAG['DATE_PHRASE']}\)|\(${TAG['DATE_PHRASE']}\))";
$TAG['DESCRIPTIVE_TITLE']             ='.{1,248}';
$TAG['EVENT_ATTRIBUTE_TYPE']          ="($EVENT_TYPE_INDIVIDUAL|$EVENT_TYPE_FAMILY|$ATTRIBUTE_TYPE)";
$TAG['EVENT_DESCRIPTOR']              ='.{1,90}';
$TAG['EVENT_OR_FACT_CLASSIFICATION']  ='.{1,90}';
$TAG['ENTRY_RECORDING_DATE']          =$TAG['DATE_VALUE'];
$TAG['EVENT_TYPE_CITED_FROM']         =$TAG['EVENT_ATTRIBUTE_TYPE'];
$TAG['EVENTS_RECORDED']               ="(${TAG['EVENT_ATTRIBUTE_TYPE']}(, ${TAG['EVENT_ATTRIBUTE_TYPE']})*)";
$TAG['FILE_NAME']                     ='.{1,90}';
$TAG['GEDCOM_FORM']                   ='(LINEAGE-LINKED)';
$TAG['GEDCOM_CONTENT_DESCRIPTION']    ='.{1,248}';
$TAG['GENERATIONS_OF_ANCESTORS']      ='([1-9][0-9]{0,3})?';
$TAG['GENERATIONS_OF_DESCENDANTS']    ='([1-9][0-9]{0,3})?';
$TAG['LANGUAGE_OF_TEXT']              =$LANGUAGE_ID;
$TAG['LANGUAGE_PREFERENCE']           =$LANGUAGE_ID;
$TAG['LDS_BAPTISM_DATE_STATUS']       ='(CHILD|COMPLETED|EXCLUDED|PRE-1970|STILLBORN|SUBMITTED|UNCLEARED)';
$TAG['LDS_CHILD_SEALING_DATE_STATUS'] ='(BIC|COMPLETED|EXCLUDED|DNS|PRE-1970|STILLBORN|SUBMITTED|UNCLEARED)';
$TAG['LDS_ENDOWMENT_DATE_STATUS']     ='(CHILD|COMPLETED|EXCLUDED|PRE-1970|STILLBORN|SUBMITTED|UNCLEARED)';
$TAG['LDS_SPOUSE_SEALING_DATE_STATUS']='(CANCELED|COMPLETED|EXCLUDED|DNS\/CAN|PRE-1970|SUBMITTED|UNCLEARED)';
$TAG['MULTIMEDIA_FILE_REFN']          ='.{1,30}';
$TAG['MULTIMEDIA_FORMAT']             ='(bmp|gif|jpeg|ole|pcx|tif|wav)';
$TAG['NAME_OF_BUSINESS']              ='.{1,90}';
$TAG['NAME_OF_FAMILY_FILE']           ='.{1,120}';
$TAG['NAME_OF_PRODUCT']               ='.{1,90}';
$TAG['NAME_OF_REPOSITORY']            ='.{1,90}';
$TAG['NAME_OF_SOURCE_DATA']           ='.{1,90}';
$TAG['NAME_PERSONAL']                 ="(($NAME_TEXT)|($NAME_TEXT )*\/$NAME_TEXT\/( $NAME_TEXT)*)";
$TAG['NAME_PHONETIC_VARIATION']       ='.{1,120}';
$TAG['NAME_PIECE_GIVEN']              ="($NAME_PIECE(, $NAME_PIECE)*)";
$TAG['NAME_PIECE_NICKNAME']           ="($NAME_PIECE(, $NAME_PIECE)*)";
$TAG['NAME_PIECE_PREFIX']             ="($NAME_PIECE(, $NAME_PIECE)*)";
$TAG['NAME_PIECE_SUFFIX']             ="($NAME_PIECE(, $NAME_PIECE)*)";
$TAG['NAME_PIECE_SURNAME']            ="($NAME_PIECE(, $NAME_PIECE)*)";
$TAG['NAME_PIECE_SURNAME_PREFIX']     ="($NAME_PIECE(, $NAME_PIECE)*)";
$TAG['NAME_ROMANIZED_VARIATION']      ='.{1,120}';
$TAG['NAME_TYPE']                     ='(aka|birth|immigrant|maiden|married|.{5,30})';
$TAG['NATIONAL_ID_NUMBER']            ='.{1,30}';
$TAG['NATIONAL_OR_TRIBAL_ORIGIN']     ='.{1,120}';
$TAG['NOBILITY_TYPE_TITLE']           ='.{1,120}';
$TAG['NULL']                          ='^$';
$TAG['OCCUPATION']                    ='.{1,90}';
$TAG['ORDINANCE_PROCESS_FLAG']        ='(no|yes)';
$TAG['PEDIGREE_LINKAGE_TYPE']         ='(adopted|birth|foster|sealing)';
$TAG['PERMANENT_RECORD_FILE_NUMBER']  ="($REGISTERED_RESOURCE_IDENTIFIER:$RECORD_IDENTIFIER)";
$TAG['PHONE_NUMBER']                  ='.{1,25}';
$TAG['PHONETIC_TYPE']                 ='(kana|hangul|.{5,30})';
$TAG['PHYSICAL_DESCRIPTION']          ='.{1,248}';
$TAG['PLACE_HIERARCHY']               ='.{1,120}';
$TAG['PLACE_LATITUDE']                ='([NS][0-9]+(\.[0-9]*)?)';
$TAG['PLACE_LONGITUDE']               ='([EW][0-9]+(\.[0-9]*)?)';
$TAG['PLACE_NAME']                    ="($PLACE_TEXT(, $PLACE_TEXT)*)";
$TAG['PLACE_LIVING_ORDINANCE']        =$TAG['PLACE_NAME'];
$TAG['PLACE_PHONETIC_VARIATION']      ='.{1,120}';
$TAG['PLACE_ROMANIZED_VARIATION']     ='.{1,120}';
$TAG['POSSESSIONS']                   ='.{1,248}';
$TAG['PUBLICATION_DATE']              =$DATE_EXACT;
$TAG['RECEIVING_SYSTEM_NAME']         ='(ANSTFILE|TempleReady|.{1,20})';
$TAG['RELATION_IS_DESCRIPTOR']        ='.{1,25}';
$TAG['RELIGIOUS_AFFILIATION']         ='.{1,90}';
$TAG['RESPONSIBLE_AGENCY']            ='.{1,120}';
$TAG['RESTRICTION_NOTICE']            ='(confidential|locked|privacy)';
$TAG['ROLE_DESCRIPTOR']               ='.{1,25}';
$TAG['ROLE_IN_EVENT']                 ="(CHIL|HUSB|WIFE|MOTH|FATH|SPOU|\(${TAG['ROLE_DESCRIPTOR']}\))";
$TAG['ROMANIZED_TYPE']                ='(pinyin|ramanji|wadegiles)';
$TAG['SCHOLASTIC_ACHIEVEMENT']        ='.{1,248}';
$TAG['SEX_VALUE']                     ='[MFU]';
$TAG['SOCIAL_SECURITY_NUMBER']        ='\d\d\d-\d\d-\d\d\d\d';
$TAG['SOURCE_CALL_NUMBER']            ='.{1,120}';
$TAG['SOURCE_DESCRIPTIVE_TITLE']      ='.{1,248}';
$TAG['SOURCE_FILED_BY_ENTRY']         ='.{1,60}';
$TAG['SOURCE_JURISDICTION_PLACE']     =$TAG['PLACE_NAME'];
$TAG['SOURCE_MEDIA_TYPE']             ='audio|book|card|electronic|fiche|film|magazine|manuscript|map|newspaper|photo|tombstone|video';
$TAG['SOURCE_ORIGINATOR']             ='.{1,248}';
$TAG['SOURCE_PUBLICATION_FACTS']      ='.{1,248}';
$TAG['SUBMITTER_NAME']                ='.{1,60}';
$TAG['SUBMITTER_REGISTERED_RFN']      ='.{1,30}';
$TAG['SUBMITTER_TEXT']                ='.{1,248}';
$TAG['TEMPLE_CODE']                   ='.{4,5}';
$TAG['TEXT']                          ='.{1,248}';
$TAG['TEXT_FROM_SOURCE']              =$TAG['TEXT'];
$TAG['TIME_VALUE']                    ='([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9](\.[0-9]+)?)?';
$TAG['TRANSMISSION_DATE']             =$DATE_EXACT;
$TAG['USER_REFERENCE_NUMBER']         ='.{1,20}';
$TAG['USER_REFERENCE_TYPE']           ='.{1,40}';
$TAG['VERSION_NUMBER']                ='.{1,15}';
$TAG['WHERE_WITHIN_SOURCE']           ='[^:,]+: ?[^:,]+(, ?[^:,]+: ?[^:,]+)*';
$TAG['XREF']                          ='@[A-Z0-9!:]{1,22}@';

////////////////////////////////////////////////////////////////////////////////
// Build an array with valid tags and their context
////////////////////////////////////////////////////////////////////////////////
$ged_grammar=array(); $ged_grammar_MIN=array(); $ged_grammar_MAX=array(); $ged_grammar_SUB=array();

function add_element($tag, $min, $max, $regex, $ignore=1)
{
	global $ged_grammar, $ged_grammar_MIN, $ged_grammar_MAX, $ged_grammar_SUB;
    if($ignore==-1)
        $ged_grammar[$tag]=-1;
    else
        $ged_grammar[$tag]=$regex;
}

function add_structure($prefix, $min, $max, $structure, $ignore=1)
{
	global $TAG, $ged_grammar;
    
    if($ignore==-1)
        return;
    
	switch ($structure) {
	case '<<ADDRESS_STRUCTURE>>':
		add_element  ($prefix.'ADDR',      $min, $max, $TAG['ADDRESS_LINE']);
		add_element  ($prefix.'ADDR:CONT', 0,    3,    $TAG['ADDRESS_LINE']);
		add_element  ($prefix.'ADDR:ADR1', 0,    1,    $TAG['ADDRESS_LINE1']);
		add_element  ($prefix.'ADDR:ADR2', 0,    1,    $TAG['ADDRESS_LINE2']);
		add_element  ($prefix.'ADDR:ADR3', 0,    1,    $TAG['ADDRESS_LINE3']);
		add_element  ($prefix.'ADDR:CITY', 0,    1,    $TAG['ADDRESS_CITY']);
		add_element  ($prefix.'ADDR:STAE', 0,    1,    $TAG['ADDRESS_STATE']);
		add_element  ($prefix.'ADDR:POST', 0,    1,    $TAG['ADDRESS_POSTAL_CODE']);
		add_element  ($prefix.'ADDR:CTRY', 0,    1,    $TAG['ADDRESS_COUNTRY']);
		add_element  ($prefix.'PHON',      0,    3,    $TAG['PHONE_NUMBER']);
		add_element  ($prefix.'EMAIL',     0,    3,    $TAG['ADDRESS_EMAIL']);
		add_element  ($prefix.'FAX',       0,    3,    $TAG['ADDRESS_FAX']);
		add_element  ($prefix.'WWW',       0,    3,    $TAG['ADDRESS_WEB_PAGE']);
		break;
	case '<<ASSOCIATION_STRUCTURE>>':
		add_element  ($prefix.'ASSO',      $min, $max, $TAG['XREF']);
		add_element  ($prefix.'ASSO:RELA', 1,    1,    $TAG['RELATION_IS_DESCRIPTOR']);
		add_structure($prefix.'ASSO:',     0,    9999, '<<SOURCE_CITATION>>',-1);
		add_structure($prefix.'ASSO:',     0,    9999, '<<NOTE_STRUCTURE>>',-1);
		break;
	case '<<CHANGE_DATE>>':
		add_element  ($prefix.'CHAN',           $min, $max, $TAG['NULL']);
		add_element  ($prefix.'CHAN:DATE',      1,    1,    $TAG['CHANGE_DATE']);
		add_element  ($prefix.'CHAN:DATE:TIME', 0,    1,    $TAG['TIME_VALUE']);
		add_structure($prefix.'CHAN:',          0,    9999, '<<NOTE_STRUCTURE>>',-1);
		break;
	case '<<CHILD_TO_FAMILY_LINK>>':
		add_element  ($prefix.'FAMC',      $min, $max, $TAG['XREF']);
		add_element  ($prefix.'FAMC:PEDI', 0,    1,    $TAG['PEDIGREE_LINKAGE_TYPE']);
		add_element  ($prefix.'FAMC:STAT', 0,    1,    $TAG['CHILD_LINKAGE_STATUS']);
		add_structure($prefix.'FAMC:',     0,    9999, '<<NOTE_STRUCTURE>>',-1);
		break;
	case '<<EVENT_DETAIL>>':
		add_element  ($prefix.'TYPE', $min, $max, $TAG['EVENT_OR_FACT_CLASSIFICATION']);
		add_element  ($prefix.'DATE', 0,    1,    $TAG['DATE_VALUE']);
		add_structure($prefix,        0,    1,    '<<PLACE_STRUCTURE>>');
		add_structure($prefix,        0,    1,    '<<ADDRESS_STRUCTURE>>');
		add_element  ($prefix.'AGNC', 0,    1,    $TAG['RESPONSIBLE_AGENCY']);
		add_element  ($prefix.'RELI', 0,    1,    $TAG['RELIGIOUS_AFFILIATION']);
		add_element  ($prefix.'CAUS', 0,    1,    $TAG['CAUSE_OF_EVENT']);
		add_element  ($prefix.'RESN', 0,    1,    $TAG['RESTRICTION_NOTICE']);
		add_structure($prefix,        0,    9999, '<<NOTE_STRUCTURE>>');
		add_structure($prefix,        0,    9999, '<<SOURCE_CITATION>>');
		add_structure($prefix,        0,    9999, '<<MULTIMEDIA_LINK>>');
        add_structure($prefix,          0,    1,    '<<CHANGE_DATE>>'); //Non gedcom!!!!
		break;
	case '<<FAMILY_EVENT_DETAIL>>':
		add_element  ($prefix.'HUSB',     $min, $max, $TAG['NULL'],-1);
		add_element  ($prefix.'HUSB:AGE', 1,    1,    $TAG['AGE_AT_EVENT'],-1);
		add_element  ($prefix.'WIFE',     0,    1,    $TAG['NULL'],-1);
		add_element  ($prefix.'WIFE:AGE', 1,    1,    $TAG['AGE_AT_EVENT'],-1);
		add_structure($prefix,            0,    1,    '<<EVENT_DETAIL>>');
		break;
	case '<<FAM_RECORD>>':
		add_element  ($prefix.'FAM',           $min, $max, $TAG['NULL']);
		add_element  ($prefix.'FAM:RESN',      0,    1,    $TAG['RESTRICTION_NOTICE']);
		add_structure($prefix.'FAM:',          0,    9999, '<<FAMILY_EVENT_STRUCTURE>>');
		add_element  ($prefix.'FAM:HUSB',      0,    1,    $TAG['XREF']);
		add_element  ($prefix.'FAM:WIFE',      0,    1,    $TAG['XREF']);
		add_element  ($prefix.'FAM:CHIL',      0,    9999, $TAG['XREF']);
		add_element  ($prefix.'FAM:NCHI',      0,    1,    $TAG['COUNT_OF_CHILDREN'],-1);
		add_element  ($prefix.'FAM:SUBM',      0,    9999, $TAG['XREF'],-1);
		add_structure($prefix.'FAM:',          0,    9999, '<<LDS_SPOUSE_SEALING>>',-1);
		add_element  ($prefix.'FAM:REFN',      0,    9999, $TAG['USER_REFERENCE_NUMBER']);
		add_element  ($prefix.'FAM:REFN:TYPE', 0,    1,    $TAG['USER_REFERENCE_TYPE']);
		add_element  ($prefix.'FAM:RIN',       0,    1,    $TAG['AUTOMATED_RECORD_ID'],-1);
		add_structure($prefix.'FAM:',          0,    1,    '<<CHANGE_DATE>>');
		add_structure($prefix.'FAM:',          0,    9999, '<<NOTE_STRUCTURE>>');
		add_structure($prefix.'FAM:',          0,    9999, '<<SOURCE_CITATION>>');
		add_structure($prefix.'FAM:',          0,    9999, '<<MULTIMEDIA_LINK>>');
		break;
	case '<<FAMILY_EVENT_STRUCTURE>>':
		foreach(array('ANUL','CENS','DIV','DIVF','ENGA','MARB','MARC','MARL','MARS','RESI') as $event) {
			add_element  ($prefix.$event,     $min, $max, $TAG['NULL']);
			add_structure($prefix.$event.':', 0,    1,    '<<FAMILY_EVENT_DETAIL>>');
		}
		add_element  ($prefix.'MARR',  $min, $max, 'Y?');
		add_structure($prefix.'MARR:', 0,    1,    '<<FAMILY_EVENT_DETAIL>>');
		add_element  ($prefix.'EVEN',  $min, $max, "(".$TAG['EVENT_DESCRIPTOR'].")?");
		add_structure($prefix.'EVEN:', 0,    1,    '<<FAMILY_EVENT_DETAIL>>');
		break;
	case '<<HEADER>>':
		add_element  ($prefix.'HEAD',                     $min, $max, $TAG['NULL']);
		add_element  ($prefix.'HEAD:SOUR',                1,    1,    $TAG['APPROVED_SYSTEM_ID'],-1);
		add_element  ($prefix.'HEAD:SOUR:VERS',           0,    1,    $TAG['VERSION_NUMBER'],-1);
		add_element  ($prefix.'HEAD:SOUR:NAME',           0,    1,    $TAG['NAME_OF_PRODUCT'],-1);
		add_element  ($prefix.'HEAD:SOUR:CORP',           0,    1,    $TAG['NAME_OF_BUSINESS'],-1);
		add_structure($prefix.'HEAD:SOUR:CORP:',          0,    1,    '<<ADDRESS_STRUCTURE>>',-1);
		add_element  ($prefix.'HEAD:SOUR:DATA',           0,    1,    $TAG['NAME_OF_SOURCE_DATA'],-1);
		add_element  ($prefix.'HEAD:SOUR:DATA:DATE',      0,    1,    $TAG['PUBLICATION_DATE'],-1);
		add_element  ($prefix.'HEAD:SOUR:DATA:COPR',      0,    1,    $TAG['COPYRIGHT_SOURCE_DATA'],-1);
		add_element  ($prefix.'HEAD:SOUR:DATA:COPR:CONC', 0,    9999, $TAG['COPYRIGHT_SOURCE_DATA'],-1);
		add_element  ($prefix.'HEAD:SOUR:DATA:COPR:CONT', 0,    9999, $TAG['COPYRIGHT_SOURCE_DATA'],-1);
		add_element  ($prefix.'HEAD:DEST',                0,    1,    $TAG['RECEIVING_SYSTEM_NAME'],-1);
		add_element  ($prefix.'HEAD:DATE',                0,    1,    $TAG['TRANSMISSION_DATE'],-1);
		add_element  ($prefix.'HEAD:DATE:TIME',           0,    1,    $TAG['TIME_VALUE'],-1);
		add_element  ($prefix.'HEAD:SUBM',                1,    1,    $TAG['XREF'],-1);
		add_element  ($prefix.'HEAD:SUBN',                0,    1,    $TAG['XREF'],-1);
		add_element  ($prefix.'HEAD:FILE',                0,    1,    $TAG['FILE_NAME'],-1);
		add_element  ($prefix.'HEAD:COPR',                0,    1,    $TAG['COPYRIGHT_GEDCOM_FILE'],-1);
		add_element  ($prefix.'HEAD:GEDC',                1,    1,    $TAG['NULL'],-1);
		add_element  ($prefix.'HEAD:GEDC:VERS',           1,    1,    $TAG['VERSION_NUMBER'],-1);
		add_element  ($prefix.'HEAD:GEDC:FORM',           1,    1,    $TAG['GEDCOM_FORM'],-1);
		add_element  ($prefix.'HEAD:CHAR',                1,    1,    $TAG['CHARACTER_SET']);
		add_element  ($prefix.'HEAD:CHAR:VERS',           0,    1,    $TAG['VERSION_NUMBER'],-1);
		add_element  ($prefix.'HEAD:LANG',                0,    1,    $TAG['LANGUAGE_OF_TEXT'],-1);
		add_element  ($prefix.'HEAD:PLAC',                0,    1,    $TAG['NULL'],-1);
		add_element  ($prefix.'HEAD:PLAC:FORM',           1,    1,    $TAG['PLACE_HIERARCHY'],-1);
		add_element  ($prefix.'HEAD:NOTE',                0,    1,    $TAG['GEDCOM_CONTENT_DESCRIPTION'],-1);
		add_element  ($prefix.'HEAD:NOTE:CONT',           0,    9999, $TAG['GEDCOM_CONTENT_DESCRIPTION'],1);
		add_element  ($prefix.'HEAD:NOTE:CONC',           0,    9999, $TAG['GEDCOM_CONTENT_DESCRIPTION'],1);
		break;
	case '<<INDIVIDUAL_ATTRIBUTE_STRUCTURE>>':
		add_element  ($prefix.'CAST',      $min, $max, $TAG['CASTE_NAME']);
		add_structure($prefix.'CAST:',     0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
		add_element  ($prefix.'DSCR',      $min, $max, $TAG['PHYSICAL_DESCRIPTION']);
		add_element  ($prefix.'DSCR:CONT', 0,    9999, $TAG['PHYSICAL_DESCRIPTION'],-1);
		add_element  ($prefix.'DSCR:CONC', 0,    9999, $TAG['PHYSICAL_DESCRIPTION'],-1);
		add_structure($prefix.'DSCR:',     0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
		add_element  ($prefix.'EDUC',      $min, $max, $TAG['SCHOLASTIC_ACHIEVEMENT']);
		add_structure($prefix.'EDUC:',     0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
		add_element  ($prefix.'IDNO',      $min, $max, $TAG['NATIONAL_ID_NUMBER']);
		add_structure($prefix.'IDNO:',     0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
		add_element  ($prefix.'NATI',      $min, $max, $TAG['NATIONAL_OR_TRIBAL_ORIGIN']);
		add_structure($prefix.'NATI:',     0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
		add_element  ($prefix.'NCHI',      $min, $max, $TAG['COUNT_OF_CHILDREN'],-1);
		add_structure($prefix.'NCHI:',     0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>',-1);
		add_element  ($prefix.'NMR',       $min, $max, $TAG['COUNT_OF_MARRIAGES'],-1);
		add_structure($prefix.'NMR:',      0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>',-1);
		add_element  ($prefix.'OCCU',      $min, $max, $TAG['OCCUPATION']);
		add_structure($prefix.'OCCU:',     0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
		add_element  ($prefix.'PROP',      $min, $max, $TAG['POSSESSIONS']);
		add_structure($prefix.'PROP:',     0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
		add_element  ($prefix.'RELI',      $min, $max, $TAG['RELIGIOUS_AFFILIATION']);
		add_structure($prefix.'RELI:',     0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
		add_element  ($prefix.'RESI',      $min, $max, $TAG['NULL']);
		add_structure($prefix.'RESI:',     0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
		add_element  ($prefix.'SSN',       $min, $max, $TAG['SOCIAL_SECURITY_NUMBER'],-1);
		add_structure($prefix.'SSN:',      0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>',-1);
		add_element  ($prefix.'TITL',      $min, $max, $TAG['NOBILITY_TYPE_TITLE']);
		add_structure($prefix.'TITL:',     0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
		add_element  ($prefix.'FACT',      $min, $max, $TAG['ATTRIBUTE_DESCRIPTOR']);
		add_structure($prefix.'FACT:',     0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
		break;
	case '<<INDIVIDUAL_EVENT_DETAIL>>':
		add_structure($prefix,       $min, $max, '<<EVENT_DETAIL>>');
		add_element  ($prefix.'AGE', 0,    1,    $TAG['AGE_AT_EVENT']);
		break;
	case '<<INDIVIDUAL_EVENT_STRUCTURE>>':
		foreach(array('BIRT','CHR') as $event) {
			add_element  ($prefix.$event,         $min, $max, 'Y?');
			add_structure($prefix.$event.':',     0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
			add_element  ($prefix.$event.':FAMC', 0,    1,    $TAG['XREF']);
		}
		foreach(array('DEAT') as $event) {
			add_element  ($prefix.$event,     $min, $max, 'Y?');
			add_structure($prefix.$event.':', 0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
		}
		foreach(array('BURI','CREM','BAPM','BARM','BASM','BLES','CHRA','CONF','FCOM','ORDN','NATU','EMIG','IMMI','CENS','PROB','WILL','GRAD','RETI') as $event) {
			add_element  ($prefix.$event,     $min, $max, $TAG['NULL']);
			add_structure($prefix.$event.':', 0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
		}
		add_element  ($prefix.'EVEN',           $min, $max, $TAG['NULL']);
		add_structure($prefix.'EVEN:',          0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
		add_element  ($prefix.'ADOP',           $min, $max, $TAG['NULL']);
		add_structure($prefix.'ADOP:',          0,    1,    '<<INDIVIDUAL_EVENT_DETAIL>>');
		add_element  ($prefix.'ADOP:FAMC',      0,    1,    $TAG['XREF']);
		add_element  ($prefix.'ADOP:FAMC:ADOP', 0,    1,    $TAG['ADOPTED_BY_WHICH_PARENT']);
		break;
	case '<<INDIVIDUAL_RECORD>>':
		add_element  ('INDI',           $min, $max, $TAG['NULL']);
		add_element  ('INDI:RESN',      0,    1,    $TAG['RESTRICTION_NOTICE']);
		add_structure('INDI:',          0,    9999, '<<PERSONAL_NAME_STRUCTURE>>');
		add_element  ('INDI:SEX',       0,    1,    $TAG['SEX_VALUE']);
		add_structure('INDI:',          0,    9999, '<<INDIVIDUAL_EVENT_STRUCTURE>>');
		add_structure('INDI:',          0,    9999, '<<INDIVIDUAL_ATTRIBUTE_STRUCTURE>>');
		add_structure('INDI:',          0,    9999, '<<LDS_INDIVIDUAL_ORDINANCE>>',-1);
		add_structure('INDI:',          0,    9999, '<<CHILD_TO_FAMILY_LINK>>');
		add_structure('INDI:',          0,    9999, '<<SPOUSE_TO_FAMILY_LINK>>');
		add_element  ('INDI:SUBM',      0,    9999, $TAG['XREF'],-1);
		add_structure('INDI:',          0,    9999, '<<ASSOCIATION_STRUCTURE>>',-1);
		add_element  ('INDI:ALIA',      0,    9999, $TAG['XREF'],-1);
		add_element  ('INDI:ANCI',      0,    9999, $TAG['XREF'],-1);
		add_element  ('INDI:DESI',      0,    9999, $TAG['XREF'],-1);
		add_element  ('INDI:RFN',       0,    1,    $TAG['PERMANENT_RECORD_FILE_NUMBER'],-1);
		add_element  ('INDI:AFN',       0,    1,    $TAG['ANCESTRAL_FILE_NUMBER'],-1);
		add_element  ('INDI:REFN',      0,    9999, $TAG['USER_REFERENCE_NUMBER']);
		add_element  ('INDI:REFN:TYPE', 0,    1,    $TAG['USER_REFERENCE_TYPE']);
		add_element  ('INDI:RIN',       0,    1,    $TAG['AUTOMATED_RECORD_ID'],-1);
		add_structure('INDI:',          0,    1,    '<<CHANGE_DATE>>');
		add_structure('INDI:',          0,    9999, '<<NOTE_STRUCTURE>>');
		add_structure('INDI:',          0,    9999, '<<SOURCE_CITATION>>');
		add_structure('INDI:',          0,    9999, '<<MULTIMEDIA_LINK>>');
		break;
	case '<<LDS_INDIVIDUAL_ORDINANCE>>':
		add_element  ($prefix.'BAPL',           $min, $max, $TAG['NULL']);
		add_element  ($prefix.'BAPL:DATE',      0,    1,    $TAG['DATE_LDS_ORD']);
		add_element  ($prefix.'BAPL:TEMP',      0,    1,    $TAG['TEMPLE_CODE']);
		add_element  ($prefix.'BAPL:PLAC',      0,    1,    $TAG['PLACE_LIVING_ORDINANCE']);
		add_element  ($prefix.'BAPL:STAT',      0,    1,    $TAG['LDS_BAPTISM_DATE_STATUS']);
		add_element  ($prefix.'BAPL:STAT:DATE', 1,    1,    $TAG['CHANGE_DATE']);
		add_structure($prefix.'BAPL:',          0,    9999, '<<NOTE_STRUCTURE>>');
		add_structure($prefix.'BAPL:',          0,    9999, '<<SOURCE_CITATION>>');
		add_element  ($prefix.'CONL',           $min, $max, $TAG['NULL']);
		add_element  ($prefix.'CONL:DATE',      0,    1,    $TAG['DATE_LDS_ORD']);
		add_element  ($prefix.'CONL:TEMP',      0,    1,    $TAG['TEMPLE_CODE']);
		add_element  ($prefix.'CONL:PLAC',      0,    1,    $TAG['PLACE_LIVING_ORDINANCE']);
		add_element  ($prefix.'CONL:STAT',      0,    1,    $TAG['LDS_BAPTISM_DATE_STATUS']);
		add_element  ($prefix.'CONL:STAT:DATE', 1,    1,    $TAG['CHANGE_DATE']);
		add_structure($prefix.'CONL:',          0,    9999, '<<NOTE_STRUCTURE>>');
		add_structure($prefix.'CONL:',          0,    9999, '<<SOURCE_CITATION>>');
		add_element  ($prefix.'ENDL',           $min, $max, $TAG['NULL']);
		add_element  ($prefix.'ENDL:DATE',      0,    1,    $TAG['DATE_LDS_ORD']);
		add_element  ($prefix.'ENDL:TEMP',      0,    1,    $TAG['TEMPLE_CODE']);
		add_element  ($prefix.'ENDL:PLAC',      0,    1,    $TAG['PLACE_LIVING_ORDINANCE']);
		add_element  ($prefix.'ENDL:STAT',      0,    1,    $TAG['LDS_ENDOWMENT_DATE_STATUS']);
		add_element  ($prefix.'ENDL:STAT:DATE', 1,    1,    $TAG['CHANGE_DATE']);
		add_structure($prefix.'ENDL:',          0,    9999, '<<NOTE_STRUCTURE>>');
		add_structure($prefix.'ENDL:',          0,    9999, '<<SOURCE_CITATION>>');
		add_element  ($prefix.'SLGC',           $min, $max, $TAG['NULL']);
		add_element  ($prefix.'SLGC:DATE',      0,    1,    $TAG['DATE_LDS_ORD']);
		add_element  ($prefix.'SLGC:TEMP',      0,    1,    $TAG['TEMPLE_CODE']);
		add_element  ($prefix.'SLGC:PLAC',      0,    1,    $TAG['PLACE_LIVING_ORDINANCE']);
		add_element  ($prefix.'SLGC:FAMC',      1,    1,    $TAG['XREF']);
		add_element  ($prefix.'SLGC:STAT',      0,    1,    $TAG['LDS_CHILD_SEALING_DATE_STATUS']);
		add_element  ($prefix.'SLGC:STAT:DATE', 1,    1,    $TAG['CHANGE_DATE']);
		add_structure($prefix.'SLGC:',          0,    9999, '<<NOTE_STRUCTURE>>');
		add_structure($prefix.'SLGC:',          0,    9999, '<<SOURCE_CITATION>>');
		break;
	case '<<LDS_SPOUSE_SEALING>>':
		add_element  ($prefix.'SLGS',           $min, $max, $TAG['NULL']);
		add_element  ($prefix.'SLGS:DATE',      0,    1,    $TAG['DATE_LDS_ORD']);
		add_element  ($prefix.'SLGS:TEMP',      0,    1,    $TAG['TEMPLE_CODE']);
		add_element  ($prefix.'SLGS:STAT',      0,    1,    $TAG['LDS_SPOUSE_SEALING_DATE_STATUS']);
		add_element  ($prefix.'SLGS:STAT:DATE', 1,    1,    $TAG['CHANGE_DATE']);
		add_structure($prefix.'SLGS:',          0,    9999, '<<NOTE_STRUCTURE>>');
		add_structure($prefix.'SLGS:',          0,    9999, '<<SOURCE_CITATION>>');
		break;
	case '<<MULTIMEDIA_LINK>>':
		add_element  ($prefix.'OBJE',           $min, $max, $TAG['XREF']);
// Old formats for objects not supported
//  add_element  ($prefix.'OBJE',           $min, $max, $TAG['NULL']);
//  add_element  ($prefix.'OBJE:FILE',      1,    9999, $TAG['MULTIMEDIA_FILE_REFN']);
//  add_element  ($prefix.'OBJE:FILE:MEDI', 0,    1,    $TAG['MULTIMEDIA_FORMAT']);
//  add_element  ($prefix.'OBJE:TITL',      0,    1,    $TAG['DESCRIPTIVE_TITLE']);
		break;
	case '<<MULTIMEDIA_RECORD>>':
		add_element  ($prefix.'OBJE',                $min, $max, $TAG['NULL']);
		add_element  ($prefix.'OBJE:FILE',           1,    9999, $TAG['MULTIMEDIA_FILE_REFN']);
		add_element  ($prefix.'OBJE:FILE:FORM',      1,    1,    $TAG['MULTIMEDIA_FORMAT']);
		add_element  ($prefix.'OBJE:FILE:FORM:TYPE', 0,    1,    $TAG['SOURCE_MEDIA_TYPE']);
		add_element  ($prefix.'OBJE:FILE:TITL',      0,    1,    $TAG['DESCRIPTIVE_TITLE']);
		add_element  ($prefix.'OBJE:REFN',           0,    9999, $TAG['USER_REFERENCE_NUMBER'],-1);
		add_element  ($prefix.'OBJE:REFN:TYPE',      0,    1,    $TAG['USER_REFERENCE_TYPE'],-1);
		add_element  ($prefix.'OBJE:RIN',            0,    1,    $TAG['AUTOMATED_RECORD_ID']);
		add_structure($prefix.'OBJE:',               0,    1,    '<<CHANGE_DATE>>');
		add_structure($prefix.'OBJE:',               0,    9999, '<<NOTE_STRUCTURE>>',-1);
		add_structure($prefix.'OBJE:',               0,    9999, '<<SOURCE_CITATION>>',-1);
		break;
	case '<<NOTE_RECORD>>':
		add_element  ($prefix.'NOTE',           $min, $max, $TAG['SUBMITTER_TEXT']);
		add_element  ($prefix.'NOTE:CONC',      0,    9999, $TAG['SUBMITTER_TEXT']);
		add_element  ($prefix.'NOTE:CONT',      0,    9999, $TAG['SUBMITTER_TEXT']);
		add_element  ($prefix.'NOTE:REFN',      0,    9999, $TAG['USER_REFERENCE_NUMBER'],-1);
		add_element  ($prefix.'NOTE:REFN:TYPE', 0,    1,    $TAG['USER_REFERENCE_TYPE'],-1);
		add_element  ($prefix.'NOTE:RIN',       0,    1,    $TAG['AUTOMATED_RECORD_ID']);
		add_structure($prefix.'NOTE:',          0,    1,    '<<CHANGE_DATE>>');
		add_structure($prefix.'NOTE:',          0,    9999, '<<SOURCE_CITATION>>',-1);
		break;
	case '<<NOTE_STRUCTURE>>':
		add_element  ($prefix.'NOTE',      $min, $max, $TAG['XREF'].'|'.$TAG['SUBMITTER_TEXT']);
		add_element  ($prefix.'NOTE:CONT', 0,    9999, $TAG['SUBMITTER_TEXT']);
		add_element  ($prefix.'NOTE:CONC', 0,    9999, $TAG['SUBMITTER_TEXT']);
        add_structure($prefix,          0,    1,    '<<CHANGE_DATE>>'); //Non gedcom!!!!
		break;
	case '<<PERSONAL_NAME_PIECES>>':
		add_element  ($prefix.'NPFX', $min, $max, $TAG['NAME_PIECE_PREFIX']);
		add_element  ($prefix.'GIVN', 0,    1,    $TAG['NAME_PIECE_GIVEN']);
		add_element  ($prefix.'NICK', 0,    1,    $TAG['NAME_PIECE_NICKNAME']);
		add_element  ($prefix.'SPFX', 0,    1,    $TAG['NAME_PIECE_SURNAME_PREFIX']);
		add_element  ($prefix.'SURN', 0,    1,    $TAG['NAME_PIECE_SURNAME']);
		add_element  ($prefix.'NSFX', 0,    1,    $TAG['NAME_PIECE_SUFFIX']);
		add_structure($prefix,        0,    9999, '<<NOTE_STRUCTURE>>',-1);
		add_structure($prefix,        0,    9999, '<<SOURCE_CITATION>>',-1);
		break;
	case '<<PERSONAL_NAME_STRUCTURE>>':
		add_element  ($prefix.'NAME',           $min, $max, $TAG['NAME_PERSONAL']);
		add_element  ($prefix.'NAME:TYPE',      0,    1,    $TAG['NAME_TYPE']);
		add_structure($prefix.'NAME:',          0,    1,    '<<PERSONAL_NAME_PIECES>>');
		add_element  ($prefix.'NAME:FONE',      0,    9999, $TAG['NAME_PHONETIC_VARIATION']);
		add_element  ($prefix.'NAME:FONE:TYPE', 1,    1,    $TAG['PHONETIC_TYPE']);
		add_structure($prefix.'NAME:FONE:',     0,    1,    '<<PERSONAL_NAME_PIECES>>');
		add_element  ($prefix.'NAME:ROMN',      0,    9999, $TAG['NAME_ROMANIZED_VARIATION']);
		add_element  ($prefix.'NAME:ROMN:TYPE', 1,    1,    $TAG['ROMANIZED_TYPE']);
		add_structure($prefix.'NAME:ROMN:',     0,    1,    '<<PERSONAL_NAME_PIECES>>');
		break;
	case '<<PLACE_STRUCTURE>>':
		add_element  ($prefix.'PLAC',           $min, $max, $TAG['PLACE_NAME']);
		add_element  ($prefix.'PLAC:FORM',      0,    1,    $TAG['PLACE_HIERARCHY'],-1);
		add_element  ($prefix.'PLAC:FONE',      0,    9999, $TAG['PLACE_PHONETIC_VARIATION'],-1);
		add_element  ($prefix.'PLAC:FONE:TYPE', 1,    1,    $TAG['PHONETIC_TYPE'],-1);
		add_element  ($prefix.'PLAC:ROMN',      0,    9999, $TAG['PLACE_ROMANIZED_VARIATION'],-1);
		add_element  ($prefix.'PLAC:ROMN:TYPE', 1,    1,    $TAG['PHONETIC_TYPE'],-1);
		add_element  ($prefix.'PLAC:MAP',       0,    1,    $TAG['NULL'],-1);
		add_element  ($prefix.'PLAC:MAP:LATI',  1,    1,    $TAG['PLACE_LATITUDE'],-1);
		add_element  ($prefix.'PLAC:MAP:LONG',  1,    1,    $TAG['PLACE_LONGITUDE'],-1);
		add_structure($prefix.'PLAC:',          0,    9999, '<<NOTE_STRUCTURE>>');
		break;
	case '<<REPOSITORY_RECORD>>':
		add_element  ($prefix.'REPO',           $min, $max, $TAG['NULL']);
		add_element  ($prefix.'REPO:NAME',      1,    1,    $TAG['NAME_OF_REPOSITORY']);
		add_structure($prefix.'REPO:',          0,    1,    '<<ADDRESS_STRUCTURE>>');
		add_structure($prefix.'REPO:',          0,    9999, '<<NOTE_STRUCTURE>>');
		add_element  ($prefix.'REPO:REFN',      0,    9999, $TAG['USER_REFERENCE_NUMBER'],-1);
		add_element  ($prefix.'REPO:REFN:TYPE', 0,    1,    $TAG['USER_REFERENCE_TYPE'],-1);
		add_element  ($prefix.'REPO:RIN',       0,    1,    $TAG['AUTOMATED_RECORD_ID']);
		add_structure($prefix.'REPO:',          0,    1,    '<<CHANGE_DATE>>');
		break;
	case '<<SOURCE_CITATION>>':
		add_element  ($prefix.'SOUR',                $min, $max, $TAG['XREF']);
		add_element  ($prefix.'SOUR:PAGE',           0,    1,    $TAG['WHERE_WITHIN_SOURCE']);
		add_element  ($prefix.'SOUR:EVEN',           0,    1,    $TAG['EVENT_TYPE_CITED_FROM']);
		add_element  ($prefix.'SOUR:EVEN:ROLE',      0,    1,    $TAG['ROLE_IN_EVENT']);
		add_element  ($prefix.'SOUR:DATA',           0,    1,    $TAG['NULL']);
		add_element  ($prefix.'SOUR:DATA:DATE',      0,    1,    $TAG['ENTRY_RECORDING_DATE']);
		add_element  ($prefix.'SOUR:DATA:TEXT',      0,    9999, $TAG['TEXT_FROM_SOURCE']);
		add_element  ($prefix.'SOUR:DATA:TEXT:CONC', 0,    9999, $TAG['TEXT_FROM_SOURCE']);
		add_element  ($prefix.'SOUR:DATA:TEXT:CONT', 0,    9999, $TAG['TEXT_FROM_SOURCE']);
		add_structure($prefix.'SOUR:',               0,    9999, '<<MULTIMEDIA_LINK>>');
		add_structure($prefix.'SOUR:',               0,    9999, '<<NOTE_STRUCTURE>>');
		add_element  ($prefix.'SOUR:QUAY',           0,    1,     $TAG['CERTAINTY_ASSESSMENT']);
		break;
	case '<<SOURCE_RECORD>>':
		add_element  ($prefix.'SOUR',                $min, $max, $TAG['NULL']);
		add_element  ($prefix.'SOUR:DATA',           0,    1,    $TAG['NULL'],-1);
		add_element  ($prefix.'SOUR:DATA:EVEN',      0,    9999, $TAG['EVENTS_RECORDED'],-1);
		add_element  ($prefix.'SOUR:DATA:EVEN:DATE', 0,    1,    $TAG['DATE_PERIOD'],-1);
		add_element  ($prefix.'SOUR:DATA:EVEN:PLAC', 0,    1,    $TAG['SOURCE_JURISDICTION_PLACE'],-1);
		add_element  ($prefix.'SOUR:DATA:AGNC',      0,    1,    $TAG['RESPONSIBLE_AGENCY'],-1);
		add_structure($prefix.'SOUR:DATA:',          0,    9999, '<<NOTE_STRUCTURE>>',-1);
		add_element  ($prefix.'SOUR:AUTH',           0,    1,    $TAG['SOURCE_ORIGINATOR']);
		add_element  ($prefix.'SOUR:AUTH:CONT',      0,    9999, $TAG['SOURCE_ORIGINATOR']);
		add_element  ($prefix.'SOUR:AUTH:CONC',      0,    9999, $TAG['SOURCE_ORIGINATOR']);
		add_element  ($prefix.'SOUR:TITL',           0,    1,    $TAG['SOURCE_DESCRIPTIVE_TITLE']);
		add_element  ($prefix.'SOUR:TITL:CONT',      0,    9999, $TAG['SOURCE_DESCRIPTIVE_TITLE']);
		add_element  ($prefix.'SOUR:TITL:CONC',      0,    9999, $TAG['SOURCE_DESCRIPTIVE_TITLE']);
		add_element  ($prefix.'SOUR:ABBR',           0,    1,    $TAG['SOURCE_FILED_BY_ENTRY']);
		add_element  ($prefix.'SOUR:PUBL',           0,    1,    $TAG['SOURCE_PUBLICATION_FACTS']);
		add_element  ($prefix.'SOUR:PUBL:CONT',      0,    9999, $TAG['SOURCE_PUBLICATION_FACTS']);
		add_element  ($prefix.'SOUR:PUBL:CONC',      0,    9999, $TAG['SOURCE_PUBLICATION_FACTS']);
		add_element  ($prefix.'SOUR:TEXT',           0,    1,    $TAG['TEXT_FROM_SOURCE']);
		add_element  ($prefix.'SOUR:TEXT:CONT',      0,    9999, $TAG['TEXT_FROM_SOURCE']);
		add_element  ($prefix.'SOUR:TEXT:CONC',      0,    9999, $TAG['TEXT_FROM_SOURCE']);
		add_structure($prefix.'SOUR:',               0,    9999, '<<SOURCE_REPOSITORY_CITATION>>');
		add_element  ($prefix.'SOUR:REFN',           0,    9999, $TAG['USER_REFERENCE_NUMBER'],-1);
		add_element  ($prefix.'SOUR:REFN:TYPE',      0,    1,    $TAG['USER_REFERENCE_TYPE'],-1);
		add_element  ($prefix.'SOUR:RIN',            0,    1,    $TAG['AUTOMATED_RECORD_ID']);
		add_structure($prefix.'SOUR:',               0,    1,    '<<CHANGE_DATE>>');
		add_structure($prefix.'SOUR:',               0,    9999, '<<NOTE_STRUCTURE>>',-1);
		add_structure($prefix.'SOUR:',               0,    9999, '<<MULTIMEDIA_LINK>>');
		break;
	case '<<SOURCE_REPOSITORY_CITATION>>':
		add_element  ($prefix.'REPO',           $min, $max, $TAG['XREF']);
		add_structure($prefix.'REPO:',          0,    9999, '<<NOTE_STRUCTURE>>',-1);
		add_element  ($prefix.'REPO:CALN',      0,    9999, $TAG['SOURCE_CALL_NUMBER']);
		add_element  ($prefix.'REPO:CALN:MEDI', 0,    1,    $TAG['SOURCE_MEDIA_TYPE']);
		break;
	case '<<SPOUSE_TO_FAMILY_LINK>>':
		add_element  ($prefix.'FAMS',  $min, $max, $TAG['XREF']);
		add_structure($prefix.'FAMS:', 0,    9999, '<<NOTE_STRUCTURE>>',-1);
		break;
	case '<<SUBMISSION_RECORD>>':
		add_element  ($prefix.'SUBN',      $min, $max, $TAG['NULL']);
		add_element  ($prefix.'SUBN:SUBM', 0,    1,    $TAG['XREF']);
		add_element  ($prefix.'SUBN:FAMF', 0,    1,    $TAG['NAME_OF_FAMILY_FILE']);
		add_element  ($prefix.'SUBN:TEMP', 0,    1,    $TAG['TEMPLE_CODE']);
		add_element  ($prefix.'SUBN:ANCE', 0,    1,    $TAG['GENERATIONS_OF_ANCESTORS']);
		add_element  ($prefix.'SUBN:DESC', 0,    1,    $TAG['GENERATIONS_OF_DESCENDANTS']);
		add_element  ($prefix.'SUBN:ORDI', 0,    1,    $TAG['ORDINANCE_PROCESS_FLAG']);
		add_element  ($prefix.'SUBN:RIN',  0,    1,    $TAG['AUTOMATED_RECORD_ID']);
		add_structure($prefix.'SUBN:',     0,    9999, '<<NOTE_STRUCTURE>>',-1);
		add_structure($prefix.'SUBN:',     0,    1,    '<<CHANGE_DATE>>');
		break;
	case '<<SUBMITTER_RECORD>>':
		add_element  ($prefix.'SUBM',      $min, $max, $TAG['NULL']);
		add_element  ($prefix.'SUBM:NAME', 1,    1,    $TAG['SUBMITTER_NAME']);
		add_structure($prefix.'SUBM:',     0,    9999, '<<ADDRESS_STRUCTURE>>');
		add_structure($prefix.'SUBM:',     0,    9999, '<<MULTIMEDIA_LINK>>');
		add_element  ($prefix.'SUBM:LANG', 0,    3,    $TAG['LANGUAGE_PREFERENCE']);
		add_element  ($prefix.'SUBM:RFN',  0,    1,    $TAG['SUBMITTER_REGISTERED_RFN']);
		add_element  ($prefix.'SUBM:RIN',  0,    1,    $TAG['AUTOMATED_RECORD_ID']);
		add_structure($prefix.'SUBM:',     0,    9999, '<<NOTE_STRUCTURE>>');
		add_structure($prefix.'SUBM:',     0,    1,    '<<CHANGE_DATE>>');
		break;
	}
}

// A GEDCOM file consists of the following level 0 elements
add_structure('',     1, 1,      '<<HEADER>>');
add_structure('',     0, 1,      '<<SUBMISSION_RECORD>>',-1);
add_structure('',     0, 999999, '<<FAM_RECORD>>');
add_structure('',     0, 999999, '<<INDIVIDUAL_RECORD>>');
add_structure('',     0, 999999, '<<MULTIMEDIA_RECORD>>');
add_structure('',     0, 999999, '<<NOTE_RECORD>>');
add_structure('',     0, 999999, '<<REPOSITORY_RECORD>>');
add_structure('',     0, 999999, '<<SOURCE_RECORD>>');
add_structure('',     0, 999999, '<<SUBMITTER_RECORD>>',-1);
add_element  ('TRLR', 1, 1,      $TAG['NULL']);

?>