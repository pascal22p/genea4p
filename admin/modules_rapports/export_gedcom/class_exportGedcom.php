<?php
 /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                                                          *
 *  Copyright (C) 2006 DEQUIDT Davy                                         *
 *                                                                          *
 *  This program is free software; you can redistribute it and/or modify    *
 *  it under the terms of the GNU General Public License as published by    *
 *  the Free Software Foundation; either version 2 of the License, or       *
 *  (at your option) any later version.                                     *
 *                                                                          *
 *  This program is distribuited in the hope that it will be useful,        *
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
 *           Export de la BD vers fichiers Gedcom 5.5 standard             *
 *                                                                         *
 * dernière mise à jour : 21/06/2006                                       *
 * En cas de problème : http://genea4p.espace.fr.to                        *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

require_once($g4p_chemin.'include_sys/sys_functions.php');

class exportGedcom
{
	var $chemin;
	var $filename;
	var $fd;
	var $base;
	var $err;
	
	var $exportMedia = true;
	var $exportMedia = true;
	var $exportSource = true;
	

	//Encodage des caractères
	var $charset = 'ANSEL';

	//Ordre d'affichage des éléments d'un lieu
	var $placePosition = '';
	
	//Variable de traitement de coupure d'execution (en s)
	var $margeTps = 5;
	var $dateStart;
	var $maxBlocs = 100;
	var $taille_ligne = 80;
	
	//Table de conversion ansel -> UTF8
	var $char_utf8 = array ('/é/', '/è/', '/ê/', '/ë/', '/ó/', '/ò/', '/ô/', '/ö/', '/á/', '/à/', '/â/', '/ä/', '/ú/', '/ù/', '/û/', '/ü/', '/í/', '/ì/', '/î/', '/ï/', '/ý/', '/ÿ/', '/ç/', '/ñ/', '/É/', '/È/', '/Ê/', '/Ë/', '/Ó/', '/Ò/', '/Ô/', '/Ö/', '/Á/', '/À/', '/Â/', '/Ä/', '/Ú/', '/Ù/', '/Û/', '/Ü/', '/Í/', '/Ì/', '/Î/', '/Ï/', '/Ý/', '//', '/Ç/', '/Ñ/');
	var $char_ansel = array ('âe', 'áe', 'ãe', 'èe', 'âo', 'áo', 'ão', 'èo', 'âa', 'áa', 'ãa', 'èa', 'âu', 'áu', 'ãu', 'èu', 'âi', 'ái', 'ãi', 'èi', 'ây', 'èy', 'ðc', '~n', 'âE', 'áE', 'ãE', 'èE', 'âO', 'áO', 'ãO', 'èO', 'âA', 'áA', 'ãA', 'èA', 'âU', 'áU', 'ãU', 'èU', 'âI', 'áI', 'ãI', 'èI', 'âY', 'èY', 'ðC', '~N');
	var $date_mois_num = array('/01/', '/02/', '/03/', '/04/', '/05/', '/06/', '/07/', '/08/', '/09/', '/10/', '/11/', '/12/');
    var $date_mois_text = array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC');
    
	var $individu_id_init = array();
	var $individu_id = array();
	var $individu = array();
	
    var $repo_id = array();
    var $repo = array();
    
    var $source_id = array();
    var $source = array();
    
    var $famille_id = array();
    var $famille = array();
    
    var $note_id = array();
    var $note = array();
    
    var $media_id = array();
    var $media = array();
    
    var $subm_id = array();
    var $subm = array();
    
	/**
	 * Constructeur
	 * @return l'objet ou une chaine de caractère décrivant erreur
	 */
	function exportGedcom($chemin, $filename, $base, $charset, $placePosition, $exportMedia, $exportNote, $exportSource, $indi_id=null)
	{
		$this->base = $base;

		switch($charset)
		{
			case 'ansel' :
			case 'ansi' :
			case 'utf8' :
				$this->charset = $charset;
				break;
			default :
				$this->err = 'type charset inconnnu.';
		}
		
		ksort($placePosition);
		$this->placePosition = $placePosition;
		if(count($placePosition)>0)
			$this->placePositionSQL = ", CONCAT_WS(',',".implode(",",$placePosition).") as place";
		else
			$this->placePositionSQL = ", '' as place";
		
		$this->exportMedia = $exportMedia;
		$this->exportNote = $exportNote;
		$this->exportSource = $exportSource;
		$this->chemin = $chemin;
		$this->filename = $filename;
		$this->fd = fopen($chemin.$filename, "w");
		if (!$this->fd) {
			$this->err = 'Impossible d\'ouvrir le fichier';
		}
		if(!is_array($indi_id))
		{
			$res = g4p_db_query("SELECT indi_id FROM `genea_individuals` WHERE `base`=".$base);
			while($ligne=mysql_fetch_assoc($res))
			{
				$this->individu_id[] = $ligne['indi_id'];
			}
		}
		else
			$this->individu_id = $indi_id;
		$this->individu_id_init = $this->individu_id;
	}
	
	/**
	 * genere la liste entre () du tableau fournit
	 */
	function listTab($tab)
	{
		$list = '( ';
		foreach($tab as $id)
			$list.= $id.',';
		return substr($list,0,-1).')';
	}
	
	function formatText($text,$conc)
	{
		$text = trim($text);
		if(strlen($text)>$this->taille_ligne)
			return substr(chunk_split($text,$this->taille_ligne,$conc),0,-strlen($conc));
		return $text;
	}
	
	function specialChar($v)
	{
		if(is_array($v))
		{
			$res = array();
			foreach($v as $key=>$val)
			{
				$res[$key] = $this->specialChar($val);
			}
			return $res;
		}
		return str_replace('@', '@@', $v);
	}
	
	function write($val)
	{
		if($this->charset=='ansel')
			$val =  preg_replace($this->char_utf8,$this->char_ansel,$val);
		if($this->charset!='utf8')
			$val = utf8_decode($val);
		fwrite($this->fd,$val);
		echo nl2br($val);
	}
	
	/**
	 * genere le fichier gedcom
	 */
	function genereGedcom($vers, $descr)
	{
		$this->write("0 HEAD\r\n".
				"1 SOUR Genea4p\r\n" .
					"2 VERS ".$vers."\r\n".
					"2 NAME Genea4p\r\n".
					"2 CORP Genea4p\r\n".
						"3 ADDR website : http://genea4p.espace.fr.to\r\n".
				"1 DATE ".strtoupper(date('d M Y'))."\r\n".
					"2 TIME ".date('H:i:s')."\r\n".
				"1 FILE ".$this->filename."\r\n".
				"1 CHAR ".$this->charset."\r\n".
				"1 GEDC\r\n".
					"2 VERS 5.5\r\n".
					"2 FORM Lineage-Linked\r\n");
		if(count($this->placePosition)>0)
		{
			$form = "1 PLAC\r\n".
					"2 FORM ";
			foreach($this->placePosition as $place)
			{
				switch($place)
				{
					case 'place_ville' : $form .= 'city'; break;
					case 'place_pays' : $form .= 'country'; break;
					case 'place_stae' : $form .= 'state'; break;
					case 'place_cp' : $form .= 'postalCode'; break;
					default : $form .= str_replace('place_','',$place); break;
				}
				$form .= ',';
			}
			$this->write(substr($form,0,-1)."\r\n");
		}
		
		if($descr)
   		{
   			$bloc = '';
			$conc = "\r\n2 CONC ";
   			$lignes = split("(\r\n|\r|\n)",$descr);
   			$i=0;
   			foreach($lignes as $ligne)
   			{
   				if($i++==0)
   					$bloc.= '1 NOTE '.$this->formatText($ligne,$conc)."\r\n";
   				else
   					$bloc.= '2 CONT '.$this->formatText($ligne,$conc)."\r\n";
   			}
   			$this->write($bloc);
   		}

		//Individus
		while(count($this->individu_id)>0)
		{
			$this->media_id = array();
			$this->event_id = array();
			
			$this->loadIndividu();
			$this->specialChar($this->individu);
			$this->loadMedia();
			$this->specialChar($this->media);
				
			foreach($this->individu as $indi)
				$this->write($this->genereIndividu($indi));
			$this->individu = array();
		}
		
		//Familles
		while(count($this->famille_id)>0)
		{
			$this->media_id = array();
			$this->event_id = array();
			
			$this->loadFamille();
			$this->specialChar($this->famille);
			$this->loadMedia();
			$this->specialChar($this->media);
			
			foreach($this->famille as $famille)
				$this->write($this->genereFamille($famille));
		}
		
		//Notes
		while(count($this->note_id)>0)
		{
			$this->loadNote();
			$this->specialChar($this->note);
			
			foreach($this->note as $note)
				$this->write($this->genereNote($note));
		}
		
		//Sources
		while(count($this->source_id)>0)
		{
			$this->media_id = array();
			$this->event_id = array();
			$this->loadSource();
			$this->specialChar($this->source);
			$this->loadMedia();
			$this->specialChar($this->media);
			
			foreach($this->source as $source)
				$this->write($this->genereSource($source));
		}
		
		//Repository
		while(count($this->repo_id)>0)
		{
			$this->loadRepository();
			$this->specialChar($this->repo);
			
			foreach($this->repo as $repo)
				$this->write($this->genereRepository($repo));
		}

		//Submitter
		while(count($this->subm_id)>0)
		{
			$this->loadSubmitter();
			$this->specialChar($this->subm);
			
			foreach($this->subm as $subm)
				$this->write($this->genereSubmitter($subm));
		}
		
		$this->write("0 TRLR\r\n");
		exit;
	}
	
	/**
	 * Charge les informations, les evenements et les liens
	 * des individus
	 */
	function loadIndividu()
	{
		//Liste des individus à charger
		if(count($this->individu_id)==0)
			return;
		$list_id = '(';
		for($i=0; $i<$this->maxBlocs; $i++)
		{
			$id = array_pop($this->individu_id);
			if($id==null) break;
			$list_id .= $id.',';
		}
		$list_id = substr($list_id,0,-1).')';
		
		//Informations individus
		$sql ="SELECT indi_nom , indi_prenom , indi_sexe, indi_id, indi_chan, npfx, givn, nick," .
				" spfx, surn, nsfx, resn FROM genea_individuals WHERE base=".$this->base.
				" AND indi_id IN ".$list_id;
		$res = g4p_db_query($sql);
		$this->individu = g4p_db_result($res, 'indi_id');
		
		//Evenements/attribus individuels
		$sql ="SELECT indi_id, id, type, attestation, date_event, age, description, cause".$this->placePositionSQL.
				", addr, city, stae, post, ctry, phon1, phon2, phon3 ".
				"FROM rel_indi_event R, genea_events E " .
				"LEFT JOIN genea_place USING (place_id) " .
				"LEFT JOIN genea_events_address A ON A.event_id=E.id ".
				"WHERE R.events_id=E.id AND E.base=".$this->base." AND indi_id IN ".$list_id;
		$res = g4p_db_query($sql);
		if($events = g4p_db_result($res))
			foreach($events as $event)
			{
				switch($event['type'])
				{
					case 'BIRT' :
					case 'CHR' :
					case 'DEAT' :
					case 'BURI' :
					case 'CREM' :
					case 'ADOP' :
					case 'BAPM' :
					case 'BARM' :
					case 'BASM' :
					case 'BLES' :
					case 'CHRA' :
					case 'CONF' :
					case 'FCOM' :
					case 'ORDN' :
					case 'NATU' :
					case 'EMIG' :
					case 'IMMI' :
					case 'CENS' :
					case 'PROB' :
					case 'WILL' :
					case 'GRAD' :
					case 'RETI' :
					case 'EVEN' :
						$this->individu[$event['indi_id']]['event'][$event['id']] = $event;
						break;
	
					case 'CAST' :
					case 'DSCR' :
					case 'EDUC' :
					case 'IDNO' :
					case 'NATI' :
					case 'NCHI' :
					case 'NMR' :
					case 'OCCU' :
					case 'PROP' :
					case 'RELI' :
					case 'RESI' :
					case 'SSN' :
					case 'TITL' :
						$this->individu[$event['indi_id']]['attr'][$event['id']] = $event;
						break;
					
					default :
						$event['description'] = $event['type'];
						$event['type'] = 'EVEN';
						$this->individu[$event['indi_id']]['event'][$event['id']] = $event;
						break;
				}
				$this->event_id[] = $event['id'];
			}
				
		//event source
		if($this->exportSource)
		{
			$sql = "SELECT indi_id, E.events_id, sources_id FROM rel_events_sources M, rel_indi_event E" .
					" WHERE E.base=".$this->base." AND M.base=".$this->base." AND M.events_id=E.events_id AND E.events_id" .
					" IN ".$this->listTab($this->event_id);
			$res = g4p_db_query($sql);
			if($sources = g4p_db_result($res))
				foreach($sources as $source)
				{
					if(isset($this->individu[$source['indi_id']]['event'][$source['events_id']]))
						$this->individu[$source['indi_id']]['event'][$source['events_id']]['source'][] = $source['sources_id'];
					if(isset($this->individu[$source['indi_id']]['attr'][$source['events_id']]))
						$this->individu[$source['indi_id']]['attr'][$source['events_id']]['source'][] = $source['sources_id'];
					$this->source_id[] = $source['sources_id'];
				}
		}
		
		//event media
		if($this->exportMedia)
		{
			$sql = "SELECT indi_id, E.events_id, multimedia_id FROM rel_events_multimedia M, rel_indi_event E" .
					" WHERE E.base=".$this->base." AND M.base=".$this->base." AND M.events_id=E.events_id AND E.events_id" .
					" IN ".$this->listTab($this->event_id);
			$res = g4p_db_query($sql);
			if($medias = g4p_db_result($res))
				foreach($medias as $media)
				{
					if(isset($this->individu[$media['indi_id']]['event'][$media['events_id']]))
						$this->individu[$media['indi_id']]['event'][$media['events_id']]['media'][] = $media['multimedia_id'];
					if(isset($this->individu[$media['indi_id']]['attr'][$media['events_id']]))
						$this->individu[$media['indi_id']]['attr'][$media['events_id']]['media'][] = $media['multimedia_id'];
					$this->media_id[] = $media['multimedia_id'];
				}
		}
				
		//event note
		if($this->exportNote)
		{
			$sql = "SELECT indi_id, E.events_id, notes_id FROM rel_events_notes M, rel_indi_event E" .
					" WHERE E.base=".$this->base." AND M.base=".$this->base." AND M.events_id=E.events_id AND E.events_id" .
					" IN ".$this->listTab($this->event_id);
			$res = g4p_db_query($sql);
			if($notes = g4p_db_result($res))
				foreach($notes as $note)
				{
					if(isset($this->individu[$note['indi_id']]['event'][$note['events_id']]))
						$this->individu[$note['indi_id']]['event'][$note['events_id']]['note'][] = $note['notes_id'];
					if(isset($this->individu[$note['indi_id']]['attr'][$note['events_id']]))
						$this->individu[$note['indi_id']]['attr'][$note['events_id']]['note'][] = $note['notes_id'];
					$this->note_id[] = $note['notes_id'];
				}
		}
		
		
		//Chargement des liens
		//famille parent
		$sql = "SELECT familles_id, familles_wife, familles_husb FROM genea_familles" .
				" WHERE base=".$this->base." AND familles_wife IN ".$list_id.
				" OR familles_husb IN ".$list_id;
		$res = g4p_db_query($sql);
		if($spouses = g4p_db_result($res))
			foreach($spouses as $spouse)
			{
				if(isset($this->individu[$spouse['familles_wife']]))
					$this->individu[$spouse['familles_wife']]['fams'][] = $spouse['familles_id'];
				if(isset($this->individu[$spouse['familles_husb']]))
					$this->individu[$spouse['familles_husb']]['fams'][] = $spouse['familles_id'];
				$this->famille_id[] = $spouse['familles_id'];
			}
		
		//famille enfant
		$sql = "SELECT indi_id, familles_id, rela_type FROM rel_familles_indi" .
				" WHERE base=".$this->base." AND indi_id IN ".$list_id;
		$res = g4p_db_query($sql);
		if($enfants = g4p_db_result($res))
			foreach($enfants as $enfant)
				$this->individu[$enfant['indi_id']]['famc'][] = 
						array('familles_id' => $enfant['familles_id'],
								'rela_type' => $enfant['rela_type']);
		
		
		//alias
		$sql = "SELECT alias1, alias2 FROM genea_alias" .
				" WHERE base=".$this->base." AND alias1 IN ".$list_id;
		$res = g4p_db_query($sql);
		if($alias = g4p_db_result($res))
			foreach($alias as $alia)
				$this->individu[$alia['alias1']]['alias'][] = $alia['alias2'];
				
		//asso
		$sql = "SELECT indi_id1, indi_id2, description FROM rel_asso_indi" .
				" WHERE base=".$this->base." AND indi_id1 IN ".$list_id;
		$res = g4p_db_query($sql);
		if($assos = g4p_db_result($res))
			foreach($assos as $asso)
			{
				//FIXME changer les champs de rel_asso_indi (desc par type et rela)
				$res = explode(":",$asso['description']);
				if(!isset($res[0])) $res[0]="";
				if(!isset($res[1])) $res[1]="";
				$this->individu[$asso['indi_id1']]['asso'][] =
						array('indi_id2'=>$asso['indi_id2'],
								'type' => $res[0],
								'rela' => $res[1]);
			}
		
		//medias
		if($this->exportMedia)
		{
			$sql = "SELECT indi_id, multimedia_id FROM rel_indi_multimedia" .
					" WHERE base=".$this->base." AND indi_id IN ".$list_id;
			$res = g4p_db_query($sql);
			if($medias = g4p_db_result($res))
				foreach($medias as $media)
				{
					$this->individu[$media['indi_id']]['media'][] = $media['multimedia_id'];
					$this->media_id[] = $media['multimedia_id'];
				}
		}

		//notes
		if($this->exportNote)
		{
			$sql = "SELECT indi_id, notes_id FROM rel_indi_notes" .
					" WHERE base=".$this->base." AND indi_id IN ".$list_id;
			$res = g4p_db_query($sql);
			if($notes = g4p_db_result($res))
				foreach($notes as $note)
				{
					$this->individu[$note['indi_id']]['note'][] = $note['notes_id'];
					$this->note_id[] = $note['notes_id'];
				}
		}

		//sources
		if($this->exportSource)
		{
			$sql = "SELECT indi_id, sources_id FROM rel_indi_sources" .
					" WHERE base=".$this->base." AND indi_id IN ".$list_id;
			$res=g4p_db_query($sql);
			if($notes = g4p_db_result($res))
				foreach($notes as $note)
				{
					$this->individu[$note['indi_id']]['source'][] = $note['sources_id'];
					$this->source_id[] = $note['sources_id'];
				}
		}
		
	}//loadIndividu
	
	/**
	 * Charge les informations, les evenements et les liens
	 * des familles
	 */
	function loadFamille()
	{
		//Liste des familles à charger
		$this->famille_id = array_unique($this->famille_id);
		if(count($this->famille_id)==0)
			return;
		$list_id = '(';
		for($i=0; $i<$this->maxBlocs; $i++)
		{
			$id = array_pop($this->famille_id);
			if($id==null) break;
			$list_id .= $id.',';
		}
		$list_id = substr($list_id,0,-1).')';
		
		//Informations famille
		$sql ="SELECT familles_id, familles_husb, familles_wife, familles_chan" .
				" FROM genea_familles WHERE base=".$this->base.
				" AND familles_id  IN ".$list_id;
		$res = g4p_db_query($sql);
		if($familles = g4p_db_result($res))
			foreach($familles as $famille)
			{
				$this->famille[$famille['familles_id']] = 
					array( 'id' => $famille['familles_id'],
							'husb' => $famille['familles_husb'],
							'wife' => $famille['familles_wife'],
							'chan' => $famille['familles_chan']);
			}
		
		//Evenements familliaux
		$sql ="SELECT familles_id, id, type, attestation, date_event, age, description, cause".$this->placePositionSQL.
				", addr, city, stae, post, ctry, phon1, phon2, phon3 ".
				"FROM rel_familles_event R, genea_events E " .
				"LEFT JOIN genea_place USING (place_id) " .
				"LEFT JOIN genea_events_address A ON A.event_id=E.id ".
				"WHERE R.events_id=E.id AND E.base=".$this->base." AND familles_id  IN ".$list_id;
		$res = g4p_db_query($sql);
		if($events = g4p_db_result($res))
			foreach($events as $event)
			{
				switch($event['type'])
				{
					case 'ANUL' :
					case 'CENS' :
					case 'DIV' :
					case 'DIVF' :
					case 'ENGA' :
					case 'MARR' :
					case 'MARB' :
					case 'MARC' :
					case 'MARL' :
					case 'MARS' :
					case 'EVEN' :
						break;
						
					default :
						$event['description'] = $event['type'];
						$event['type'] = 'EVEN';
						break;
				}
				$this->famille[$event['familles_id']]['event'][$event['id']] = $event;
				$this->event_id[] = $event['id'];
			}
		
		//event source
		if($this->exportSource)
		{
			$sql = "SELECT familles_id, E.events_id, sources_id FROM rel_events_sources M, rel_familles_event E" .
					" WHERE E.base=".$this->base." AND M.base=".$this->base." AND M.events_id=E.events_id AND E.events_id" .
					" IN ".$this->listTab($this->event_id);
			$res = g4p_db_query($sql);
			if($sources = g4p_db_result($res))
				foreach($sources as $source)
				{
					$this->famille[$source['familles_id']]['event'][$source['events_id']]['source'][] = $source['sources_id'];
					$this->source_id[] = $source['sources_id'];
				}
		}
		
		//event media
		if($this->exportMedia)
		{
			$sql = "SELECT familles_id, E.events_id, multimedia_id FROM rel_events_multimedia M, rel_familles_event E" .
					" WHERE E.base=".$this->base." AND M.base=".$this->base." AND M.events_id=E.events_id AND E.events_id" .
					" IN ".$this->listTab($this->event_id);
			$res = g4p_db_query($sql);
			if($medias = g4p_db_result($res))
				foreach($medias as $media)
				{
					$this->famille[$media['familles_id']]['event'][$media['events_id']]['media'][] = $media['multimedia_id'];
					$this->media_id[] = $media['multimedia_id'];
				}
		}//event media
				
		//event note
		if($this->exportNote)
		{
			$sql = "SELECT familles_id, E.events_id, notes_id FROM rel_events_notes M, rel_familles_event E" .
					" WHERE E.base=".$this->base." AND M.base=".$this->base." AND M.events_id=E.events_id AND E.events_id" .
					" IN ".$this->listTab($this->event_id);
			$res = g4p_db_query($sql);
			if($notes = g4p_db_result($res))
				foreach($notes as $note)
				{
					$this->individu[$note['familles_id']]['event'][$note['events_id']]['note'][] = $note['notes_id'];
					$this->note_id[] = $note['notes_id'];
				}
		}
		
		//seulement les enfants présents ds la liste
		$sql = "SELECT indi_id, familles_id FROM rel_familles_indi" .
				" WHERE base=".$this->base." AND familles_id  IN ".$list_id.
				" AND indi_id IN ".$this->listTab($this->individu_id_init);
		$res = g4p_db_query($sql);
		if($enfants = g4p_db_result($res))
			foreach($enfants as $enfant)
				$this->famille[$enfant['familles_id']]['enfant'][] = $enfant['indi_id'];
		
		//Chargement des liens
		
		//asso
		$sql = "SELECT indi_id, familles_id, description FROM rel_asso_familles" .
				" WHERE base=".$this->base." AND familles_id IN ".$list_id;
		$res = g4p_db_query($sql);
		if($assos = g4p_db_result($res))
			foreach($assos as $asso)
			{
				//FIXME changer les champs de rel_asso_indi (desc par type et rela)
				$res = explode(":",$asso['description']);
				if(!isset($res[0])) $res[0]="";
				if(!isset($res[1])) $res[1]="";
				$this->famille[$asso['familles_id']]['asso'][] =
						array('indi_id'=>$asso['indi_id'],
								'type' => $res[0],
								'rela' => $res[1]);
			}
			
		//medias
		if($this->exportMedia)
		{
			$sql = "SELECT familles_id, multimedia_id FROM rel_familles_multimedia" .
					" WHERE base=".$this->base." AND familles_id  IN ".$list_id;
			$res = g4p_db_query($sql);
			if($medias = g4p_db_result($res))
				foreach($medias as $media)
				{
					$this->famille[$media['familles_id']]['media'][] = $media['multimedia_id'];
					$this->media_id[] = $media['multimedia_id'];
				}
		}

		//notes
		if($this->exportNote)
		{
			$sql = "SELECT familles_id, notes_id FROM rel_familles_notes" .
					" WHERE base=".$this->base." AND familles_id  IN ".$list_id;
			$res = g4p_db_query($sql);
			if($notes = g4p_db_result($res))
				foreach($notes as $note)
				{
					$this->famille[$note['familles_id']]['note'][] = $note['notes_id'];
					$this->note_id[] = $note['notes_id'];
				}
		}

		//sources
		if($this->exportSource)
		{
			$sql = "SELECT familles_id, sources_id FROM rel_familles_sources" .
					" WHERE base=".$this->base." AND familles_id  IN ".$list_id;
			$res=g4p_db_query($sql);
			if($notes = g4p_db_result($res))
				foreach($notes as $note)
				{
					$this->famille[$note['familles_id']]['source'][] = $note['sources_id'];
					$this->source_id[] = $note['sources_id'];
				}
		}
	}//loadFamille
	
	
	/**

	
	
	/**
	 * Charge les informations des dépots
	 */
	function loadRepository()
	{
		//Liste des repo à charger
		$this->repo_id = array_unique($this->repo_id);
		if(count($this->repo_id)==0)
			return;
		$list_id = '(';
		for($i=0; $i<$this->maxBlocs; $i++)
		{
			$id = array_pop($this->repo_id);
			if($id==null) break;
			$list_id .= $id.',';
		}
		$list_id = substr($list_id,0,-1).')';
		
		//Informations Notes
		$sql ="SELECT repo_id, repo_name, repo_addr, repo_city, repo_stae," .
				" repo_post, repo_ctry, repo_phon1, repo_phon2, repo_phon3, repo_chan" .
				" FROM genea_repository WHERE base=".$this->base.
				" AND repo_id IN ".$list_id;
		$res = g4p_db_query($sql);
		if($repos = g4p_db_result($res))
			foreach($repos as $repo)
				$this->repo[$repo['repo_id']] = 
					array( 'id' => $repo['repo_id'],
							'name' => $repo['repo_name'],
							'addr' => $repo['repo_addr'],
							'city' => $repo['repo_city'],
							'post' => $repo['repo_post'],
							'stae' => $repo['repo_stae'],
							'ctry' => $repo['repo_ctry'],
							'phon1' => $repo['repo_phon1'],
							'phon2' => $repo['repo_phon2'],
							'phon3' => $repo['repo_phon3'],
							'chan' => $repo['repo_chan']);
		
	}//loadRepository
	
	/**
	 * Charge les informations des sources
	 */
	function loadSource()
	{
		//Liste des Sources à charger
		$this->source_id = array_unique($this->source_id);
		if(count($this->source_id)==0)
			return;
		$list_id = '(';
		for($i=0; $i<$this->maxBlocs; $i++)
		{
			$id = array_pop($this->source_id);
			if($id==null) break;
			$list_id .= $id.',';
		}
		$list_id = substr($list_id,0,-1).')';
		
		//Informations Sources
		$sql ="SELECT sources_id, repo_id, sources_title, sources_publ, sources_text," .
				" sources_auth, sources_caln, sources_page, sources_medi, sources_chan" .
				" FROM genea_sources WHERE base=".$this->base.
				" AND sources_id IN ".$list_id;
		$res = g4p_db_query($sql);
		if($sources = g4p_db_result($res))
			foreach($sources as $source)
			{
				$this->source[$source['sources_id']] = 
					array( 'id' => $source['sources_id'],
							'repo_id' => $source['repo_id'],
							'title' => $source['sources_title'],
							'publ' => $source['sources_publ'],
							'text' => $source['sources_text'],
							'auth' => $source['sources_auth'],
							'caln' => $source['sources_caln'],
							'page' => $source['sources_page'],
							'medi' => $source['sources_medi'],
							'chan' => $source['sources_chan']);
				if($source['repo_id'])
					$this->repo_id[] = $source['repo_id'];
			}
		
		//medias
		if($this->exportMedia)
		{
			$sql = "SELECT sources_id, multimedia_id FROM rel_sources_multimedia" .
					" WHERE base=".$this->base." AND sources_id  IN ".$list_id;
			$res = g4p_db_query($sql);
			if($medias = g4p_db_result($res))
				foreach($medias as $media)
				{
					$this->source[$media['sources_id']]['media'][] = $media['multimedia_id'];
					$this->media_id[] = $media['multimedia_id'];
				}
		}
	}//loadSource
	
	
	/**
	 * Charge les informations des notes
	 */
	function loadNote()
	{
		//Liste des Notes à charger
		$this->note_id = array_unique($this->note_id);
		if(count($this->note_id)==0)
			return;
		$list_id = '(';
		for($i=0; $i<$this->maxBlocs; $i++)
		{
			$id = array_pop($this->note_id);
			if($id==null) break;
			$list_id .= $id.',';
		}
		$list_id = substr($list_id,0,-1).')';
		
		//Informations Notes
		$sql ="SELECT notes_id, notes_text, notes_chan" .
				" FROM genea_notes WHERE base=".$this->base.
				" AND notes_id IN ".$list_id;
		$res = g4p_db_query($sql);
		if($notes = g4p_db_result($res))
			foreach($notes as $note)
				$this->note[$note['notes_id']] = 
					array( 'id' => $note['notes_id'],
							'text' => $note['notes_text'],
							'chan' => $note['notes_chan']);
							
				
	}//loadNote
	
	
	/**
	 * Charge les informations des éléments multimedia
	 */
	function loadMedia()
	{
		//Liste des Media à charger
		$this->media_id = array_unique($this->media_id);
		
		if(count($this->media_id)==0)
			return;
		$list_id = '(';
		for($i=0; $i<$this->maxBlocs; $i++)
		{
			$id = array_pop($this->media_id);
			if($id==null) break;
			$list_id .= $id.',';
		}
		$list_id = substr($list_id,0,-1).')';
		
		//Informations Media
		$sql ="SELECT id, title, format, file, chan" .
				" FROM genea_multimedia WHERE base=".$this->base.
				" AND id IN ".$list_id;
		$res = g4p_db_query($sql);
		if($medias = g4p_db_result($res))
			foreach($medias as $media)
				$this->media[$media['id']] = 
					array( 'id' => $media['id'],
							'title' => $media['title'],
							'format' => $media['format'],
							'file' => $media['file'],
							'chan' => $media['chan']);
				
	}//loadMedia
	
	
	/**
	 * Charge les informations des auteurs
	 */
	function loadSubmitter()
	{
		//Liste des auteurs à charger
		$this->subm_id = array_unique($this->subm_id);
		
		if(count($this->subm_id)==0)
			return;
		$list_id = '(';
		for($i=0; $i<$this->maxBlocs; $i++)
		{
			$id = array_pop($this->subm_id);
			if($id==null) break;
			$list_id .= $id.',';
		}
		$list_id = substr($list_id,0,-1).')';
		
		//Informations Submitter
		$sql ="SELECT sub_id, sub_name, sub_chan, sub_addr, sub_city," .
				" sub_stae, sub_post, sub_ctry, sub_phon1, sub_phon2, sub_phon3, sub_lang" .
				" FROM genea_submitters WHERE base=".$this->base.
				" AND sub_id IN ".$list_id;
		$res = g4p_db_query($sql);
		if($subms = g4p_db_result($res))
			foreach($subms as $subm)
				$this->$subm[$subm['sub_id']] = 
					array( 'id' => $subm['sub_id'],
							'name' => $subm['sub_name'],
							'addr' => $subm['sub_addr'],
							'city' => $subm['sub_city'],
							'post' => $subm['sub_post'],
							'stae' => $subm['sub_stae'],
							'ctry' => $subm['sub_ctry'],
							'phon1' => $subm['sub_phon1'],
							'phon2' => $subm['sub_phon2'],
							'phon3' => $subm['sub_phon3'],
							'lang' => $subm['sub_lang'],
							'chan' => $subm['sub_chan']);
				
	}//loadSubmitter
	
	
	function genereIndividu($indi)
	{
		$bloc = "0 @I".$indi['indi_id']."@ INDI\r\n";
		
		if($indi['resn'])
			$bloc.= "1 RESN ".$indi['resn']."\r\n";
		
		//PERSONAL_NAME_STRUCTURE
		$bloc.= "1 NAME ".$indi['indi_prenom'].' /'.$indi['indi_nom']."/\r\n";
		if($indi['npfx'])
			$bloc.= "2 NPFX ".$indi['npfx']."\r\n";
		if($indi['givn'])
			$bloc.= "2 GIVN ".$indi['givn']."\r\n";
		if($indi['nick'])
			$bloc.= "2 NICK ".$indi['nick']."\r\n";
		if($indi['spfx'])
			$bloc.= "2 SPFX ".$indi['spfx']."\r\n";
		if($indi['surn'])
			$bloc.= "2 SURN ".$indi['surn']."\r\n";
		if($indi['nsfx'])
			$bloc.= "2 NSFX ".$indi['nsfx']."\r\n";
		
		//SEX
		if($indi['indi_sexe']=='M' || $indi['indi_sexe']=='F')
			$bloc.= "1 SEX ".$indi['indi_sexe']."\r\n";
		
		//INDIVIDUAL_EVENT_STRUCTURE
		foreach($indi['event'] as $event)
		{
			$bloc.= "1 ".$event['type'];
			if($event['type']!="EVEN" && $event['attestation']=='Y')
				$bloc.= " Y";
			$bloc.= "\r\n";
			
			$bloc.=$this->genereEventDetail(2,$event);
		}
		
		//INDIVIDUAL_ATTRIBUTE_STRUCTURE
		foreach($indi['attr'] as $attr)
		{
			$bloc.= "1 ".$attr['type']." ".$attr['description']."\r\n";
			
			$attr['description'] = $attr['type'];
			$bloc.=$this->genereEventDetail(2,$attr);
		}
		
		//SPOUSE_TO_FAMILY_LINK
		foreach($indi['fams'] as $fams)
			$bloc.= "1 FAMS @F".$fams."@\r\n";
		
		//CHILD_TO_FAMILY_LINK
		foreach($indi['famc'] as $famc)
		{
			$bloc.= "1 FAMC @F".$famc['familles_id']."@\r\n";
			if($famc['rela_type']!='BIRTH')
				$bloc.= "2 PEDI ".$famc['rela_type']."\r\n";
		}
		
		//ASSOCIATION_STRUCTURE
		foreach($indi['asso'] as $asso)
			$bloc.= $this->genereAssociationStructure(1,$asso);
		
		//ALIAS
		foreach($indi['alias'] as $alias)
			$bloc.= "1 ALIA @I".$alias."@\r\n";
		
		//SOURCE_CITATION
		foreach($indi['source'] as $source)
			$bloc.= "1 SOUR @S".$source."@\r\n";
		
		//NOTE_STRUCTURE
		foreach($indi['note'] as $note)
			$bloc.= "1 NOTE @N".$note."@\r\n";
		
		//MULTIMEDIA_LINK
		foreach($indi['media'] as $media)
			$bloc.= $this->genereMultimediaLink(1,$media);
		
		//CHANGE_DATE
		$bloc.= $this->genereChangeDate(1,$indi['indi_chan']);
		
		return $bloc;
	}//genereIndividu
	
	
	function genereFamille($famille)
	{

		$bloc = "0 @F".$famille['id']."@ FAM\r\n";
		
		//FAMILY_EVENT_STRUCTURE
		foreach($famille['event'] as $event)
		{
			$bloc.= "1 ".$event['type'];
			if($event['type']!="EVEN" && $event['attestation']=='Y')
				$bloc.= " Y";
			$bloc.= "\r\n";
			
			$bloc.=$this->genereEventDetail(2,$event);
		}
		
		//HUSB
		if($famille['husb'])
			$bloc.= "1 HUSB @I".$famille['husb']."@\r\n";
		
		
		//WIFE
		if($famille['wife'])
			$bloc.= "1 WIFE @I".$famille['wife']."@\r\n";
		
		//CHILD
		foreach($famille['enfant'] as $enfant)
			$bloc.= "1 CHIL @I".$enfant."@\r\n";
		
		//ASSOCIATION_STRUCTURE
		foreach($famille['asso'] as $asso)
			$bloc.= $this->genereAssociationStructure(1,$asso);
		
		//SOURCE_CITATION
		foreach($famille['source'] as $source)
			$bloc.= "1 SOUR @S".$source."@\r\n";
		
		//MULTIMEDIA_LINK
		foreach($famille['media'] as $media)
			$bloc.= $this->genereMultimediaLink(1,$media);
		
		//NOTE_STRUCTURE
		foreach($famille['note'] as $note)
			$bloc.= "1 NOTE @N".$note."@\r\n";
		
		//CHANGE_DATE
		$bloc.= $this->genereChangeDate(1,$famille['chan']);
		
		return $bloc;
	}//genereFamille
	
	
	function genereNote($note)
	{
		$bloc = '';
		$conc = "\r\n1 CONC ";
   		$lignes = split("(\r\n|\r|\n)",$note['text']);
   		$i=0;
   		foreach($lignes as $ligne)
   		{
   			if($i++==0)
	   			$bloc.= "0 @N".$note['id']."@ NOTE ".$this->formatText($ligne,$conc)."\r\n";
	   		else
	   			$bloc.= '1 CONT '.$this->formatText($ligne,$conc)."\r\n";
   		}
   		
		//CHANGE_DATE
		$bloc.= $this->genereChangeDate(1,$note['chan']);
		
		return $bloc;
	}//genereNote
	
	
	function genereRepository($repo)
	{
		$bloc = "0 @R".$repo['id']."@ REPO\r\n";
		
		//NAME
		if($repo['name'])
			$bloc.= '1 NAME '.$repo['name']."\r\n";
		
		//ADDRESS_STRUCTURE
		$bloc.= $this->genereAddressStructure(1,$repo);
   		
		//CHANGE_DATE
		$bloc.= $this->genereChangeDate(1,$repo['chan']);
		
		return $bloc;
	}//genereRepository
	
	
	
	function genereSubmitter($sub)
	{
		$bloc = "0 @A".$sub['id']."@ SUBM\r\n";
		
		$bloc.= "1 NAME ".$sub['name']."\r\n";
		
		//ADDRESS_STRUCTURE
		$bloc.= $this->genereAddressStructure(1,$sub);
			
		if($sub['lang'])
			$bloc.= '1 LANG '.$sub['lang']."\r\n";
   		
		//CHANGE_DATE
		$bloc.= $this->genereChangeDate(1,$sub['chan']);
		
		return $bloc;
	}//genereRepository
	
	
	function genereSource($source)
	{
		$bloc = "0 @S".$source['id']."@ SOUR\r\n";
	
		//AUTH
   		if($source['auth'])
   		{
			$conc = "\r\n2 CONC ";
   			$lignes = split("(\r\n|\r|\n)",$source['auth']);
   			$i=0;
   			foreach($lignes as $ligne)
   			{
   				if($i++==0)
   					$bloc.= '1 AUTH '.$this->formatText($ligne,$conc)."\r\n";
   				else
   					$bloc.= '2 CONT '.$this->formatText($ligne,$conc)."\r\n";
   			}
   		}
   		
   		//TITL
   		if($source['title'])
   		{
			$conc = "\r\n2 CONC ";
   			$lignes = split("(\r\n|\r|\n)",$source['title']);
   			$i=0;
   			foreach($lignes as $ligne)
   			{
   				if($i++==0)
   					$bloc.= '1 TITL '.$this->formatText($ligne,$conc)."\r\n";
   				else
   					$bloc.= '2 CONT '.$this->formatText($ligne,$conc)."\r\n";
   			}
   		}
   		
   		//PUBL
   		if($source['publ'])
   		{
			$conc = "\r\n2 CONC ";
   			$lignes = split("(\r\n|\r|\n)",$source['publ']);
   			$i=0;
   			foreach($lignes as $ligne)
   			{
   				if($i++==0)
   					$bloc.= '1 PUBL '.$this->formatText($ligne,$conc)."\r\n";
   				else
   					$bloc.= '2 CONT '.$this->formatText($ligne,$conc)."\r\n";
   			}
   		}
   		
   		//TEXT
   		if($source['text'])
   		{
			$conc = "\r\n2 CONC ";
   			$lignes = split("(\r\n|\r|\n)",$source['text']);
   			$i=0;
   			foreach($lignes as $ligne)
   			{
   				if($i++==0)
   					$bloc.= '1 TEXT '.$this->formatText($ligne,$conc)."\r\n";
   				else
   					$bloc.= '2 CONT '.$this->formatText($ligne,$conc)."\r\n";
   			}
   		}
   		
   		//SOURCE_REPOSITORY_CITATION
   		if($source['repo_id'])
   		{
   			$bloc.= '1 REPO @R'.$source['repo_id']."@\r\n";
   			if($source['caln'])
   				$bloc.= '2 CALN '.$source['auth']."\r\n";
   			if($source['medi'])
   				$bloc.= '3 MEDI '.$source['medi']."\r\n";
   		}

		//MULTIMEDIA_LINK
		foreach($source['media'] as $media)
			$bloc.= $this->genereMultimediaLink(1,$media);
		
		//CHANGE_DATE
		$bloc.= $this->genereChangeDate(1,$source['chan']);
		
		return $bloc;
	}//genereSource
	
	/*
	function genereMedia($media)
	{
		$bloc = "0 @O".$media['id']."@ OBJE\r\n";
		
		if($media['form'])
   			$bloc.= '1 FORM '.$media['form']."\r\n";
		if($media['form'])
   			$bloc.= '1 TITL '.$media['titl']."\r\n";
		if($media['form'])
   			$bloc.= '1 FORM '.$media['form']."\r\n";
   		
		//CHANGE_DATE
		$bloc.= $this->genereChangeDate(1,$media['chan']);
		
		return $bloc;
	}//genereMedia
	*/
	
	function genereMultimediaLink($n, $id)
	{
		if(!isset($this->media[$id])) return '';
		
		$bloc = $n." OBJE\r\n";
		if($this->media[$id]['format'])
			$bloc.= ($n+1)." FORM ".$this->media[$id]['format']."\r\n";
		if($this->media[$id]['title'])
			$bloc.= ($n+1)." TITL ".$this->media[$id]['title']."\r\n";
		if($this->media[$id]['file'])
			$bloc.= ($n+1)." FILE ".$this->media[$id]['file']."\r\n";
		//CHANGE_DATE
		$bloc.= $this->genereChangeDate($n+1,$this->media[$id]['chan']);
		
		return $bloc;
	}
	
	function genereEventDetail($n, $event)
	{
		$bloc = "";
		if($event['description'])
			$bloc.= $n." TYPE ".$event['description']."\r\n";
		if($event['date_event'])
			$bloc.= $n." DATE ".$event['date_event']."\r\n";
		if($event['place'])
			$bloc.= $n." PLAC ".$event['place']."\r\n";
		
		//ADDRESS_STRUCTURE
			$bloc.= $this->genereAddressStructure($n,$event);
		
		if($event['age'])
			$bloc.= $n." AGE ".$event['age']."\r\n";
		if($event['cause'])
			$bloc.= $n." CAUS ".$event['cause']."\r\n";
		
		//SOURCE_CITATION
		foreach($event['source'] as $source)
			$bloc.= $n." SOUR @S".$source."@\r\n";
			
		//MULTIMEDIA_LINK
		foreach($event['media'] as $media)
		{
			$bloc.= $this->genereMultimediaLink($n,$media);
		}
		
		//NOTE_STRUCTURE
		foreach($event['note'] as $note)
			$bloc.= $n." NOTE @N".$note."@\r\n";
		
		return $bloc;
	}//genereEventDetail
	
	
	function genereAssociationStructure($n, $asso)
	{
		$bloc = "";	
		$bloc.= $n." ASSO @I".$asso['indi_id2']."@\r\n";
		if($asso['type'])
			$bloc.= ($n+1)." TYPE ".$asso['type']."\r\n";
		if($asso['rela'])
			$bloc.= ($n+1)." RELA ".$asso['rela']."\r\n";
		return $bloc;
	}
	
	function genereChangeDate($n, $chan)
	{
		$bloc = "";
		if($chan && $chan!="0000-00-00 00:00:00")
		{
			$date = explode(' ',$chan);
			//$conv = str_replace('-',' ',$date[0]);
			//$conv = preg_replace(array_keys($this->date_mois),array_values($this->date_mois),$conv);
			ereg("([^-]*)-([^-]*)-([^-]*)",$date[0],$ereg);
			$conv = $ereg[3]." ".preg_replace($this->date_mois_num,$this->date_mois_text,$ereg[2])." ".$ereg[1];
			$bloc.= $n." CHAN\r\n";
			$bloc.= ($n+1)." DATE ".$conv."\r\n";
			if(isset($date[1]))
				$bloc.= ($n+2)." TIME ".$date[1]."\r\n";
		}
		return $bloc;
	}
	
	function genereAddressStructure($n, $addr)
	{
		$bloc = "";
		if($addr['addr'] || $addr['city'] || $addr['post'] || $addr['ctry'])
		{
			$conc = "\r\n".($n+1)." CONC ";
   			$lignes = split("(\r\n|\r|\n)",$addr['addr']);
   			$i=0;
   			foreach($lignes as $ligne)
   			{
   				if($i++==0)
   					$bloc.= $n.' ADDR '.$this->formatText($ligne,$conc)."\r\n";
   				else
   					$bloc.= ($n+1).' CONT '.$this->formatText($ligne,$conc)."\r\n";
   			}
   			
			if($addr['city'])
				$bloc.= ($n+1).' CITY '.$addr['city']."\r\n";
			if($addr['stae'])
				$bloc.= ($n+1).' STAE '.$addr['stae']."\r\n";
			if($addr['post'])
				$bloc.= ($n+1).' POST '.$addr['post']."\r\n";
			if($addr['ctry'])
				$bloc.= ($n+1).' CTRY '.$addr['ctry']."\r\n";
   		}
		if($addr['phon1'])
			$bloc.= $n.' PHON '.$addr['phon1']."\r\n";
		if($addr['phon2'])
			$bloc.= $n.' PHON '.$addr['phon2']."\r\n";
		if($addr['phon3'])
			$bloc.= $n.' PHON '.$addr['phon3']."\r\n";
			
		return $bloc;
	}
}



?>
