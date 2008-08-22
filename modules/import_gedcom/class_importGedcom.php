<?php
 /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                                                          *
 *  Copyright (C) 2006 DEQUIDT Davy, FEDERBE Arnaud                         *
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
 *            Analyse grammaire gedcom 5.5 / import ds la BD               *
 *                                                                         *
 * dernière mise à jour : 13/04/2006                                       *
 * En cas de problème : http://genea4p.espace.fr.to                        *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
 
 
require_once ("data.php");
require_once($g4p_chemin.'include_sys/sys_functions.php');

class importGedcom {
	var $filename;
	var $prefix;
	var $fd;
	var $length = 0;
	var $ligne;
	var $noLigne;
	var $base;

	/**
	 * Variable de traitement d'erreur
	 */
	var $ignoreErreur = true;
	var $erreur = false;
	var $warning = false;
	var $debug = false;
	var $log;
	var $log_filename = null;
	var $nolog = false;

	//Encodage des caractères
	var $charset = 'ANSEL';

	//Ordre d'affichage des éléments d'un lieu
	var $placePosition;
	
	//Variable utilise par pre_place
	var $demoPlace;
	var $nbDemoPlace = 15;

	//Variable de traitement de coupure d'execution (em s)
	var $margeTps = 1;
	var $dateStart;
	
	//Nombre d'insertions max dans le buffer
	var $maxReq = 50;
	var $nbReq = array();
	var $bufsql = array();

	//Table de hachage des lieux (md5)
	var $hashPlace;
	
	var $head = false;
	var $subn = false;

	//Table de conversion ansel -> UTF8
	var $ansel_map = array ('/âe/', '/áe/', '/ãe/', '/èe/', '/âo/', '/áo/', '/ão/', '/èo/', '/âa/', '/áa/', '/ãa/', '/èa/', '/âu/', '/áu/', '/ãu/', '/èu/', '/âi/', '/ái/', '/ãi/', '/èi/', '/ây/', '/èy/', '/ðc/', '/~n/', '/âE/', '/áE/', '/ãE/', '/èE/', '/âO/', '/áO/', '/ãO/', '/èO/', '/âA/', '/áA/', '/ãA/', '/èA/', '/âU/', '/áU/', '/ãU/', '/èU/', '/âI/', '/áI/', '/ãI/', '/èI/', '/âY/', '/èY/', '/ðC/', '/~N/');
	var $ansel_replacement = array ('é', 'è', 'ê', 'ë', 'ó', 'ò', 'ô', 'ö', 'á', 'à', 'â', 'ä', 'ú', 'ù', 'û', 'ü', 'í', 'ì', 'î', 'ï', 'ý', 'ÿ', 'ç', 'ñ', 'É', 'È', 'Ê', 'Ë', 'Ó', 'Ò', 'Ô', 'Ö', 'Á', 'À', 'Â', 'Ä', 'Ú', 'Ù', 'Û', 'Ü', 'Í', 'Ì', 'Î', 'Ï', 'Ý', '', 'Ç', 'Ñ');

	/**
	 * Correspondance entre références Gedcom et id BD
	 */
	var $xref = array ('FAM' => array (), 'INDI' => array (), 'NOTE' => array (), 'OBJE' => array (), 'REPO' => array (), 'SOUR' => array (), 'SOUR_CIT' => array (), 'SUBM' => array (), 'SUBN' => array ());
	var $xref_id = array ('FAM' => 1, 'INDI' => 1, 'NOTE' => 1, 'OBJE' => 1, 'REPO' => 1, 'SOUR' => 1, 'SOUR_CIT' => 1, 'SUBM' => 1, 'SUBN' => 1, 'PLAC' => 1, 'EVEN' => 1);
	var $xref_id_init;
	var $nb_xref_id = array ('FAM' => 0, 'INDI' => 0, 'NOTE' => 0, 'OBJE' => 0, 'REPO' => 0, 'SOUR' => 0, 'SOUR_CIT' => 0, 'SUBM' => 0, 'SUBN' => 0, 'PLAC' => 0, 'EVEN' => 0);
	
	/**
	 * traitement des tables de liens
	 */
	var $fdlien = array ('alia' => null, 'asso_indi' => null, 'indi_notes' => null, 'asso_events' => null, 'indi_attributes' => null, 'indi_events' => null, 'events_notes' => null, 'indi_multimedia' => null, 'events_multimedia' => null, 'familles_indi' => null, 'asso_familles' => null, 'familles_notes' => null, 'familles_sources' => null, 'familles_events' => null, 'familles_multimedia' => null, 'events_sources' => null, 'indi_sources' => null, 'sour_citations_multimedia' => null, 'multimedia_notes' => null, 'repo_notes' => null, 'sour_citations_notes' => null);
	var $fdFamilles;
	var $fdSourcesRepo;
	var $fdSourcesCitRec;
	var $lien_courant = 0;
	var $nb_err_lien = 0;
	
	/**
	 * Constructeur
	 * Charge le fichier gedcom filename
	 */
	function importGedcom($filename, $base, $logType) {
		global $g4p_chemin;
		$this->base = $base;
		$this->filename = $filename;
		$this->prefix = uniqid('importged_');
		$this->log_filename = $g4p_chemin.'temp/'.$this->prefix.'.log';
		$this->fd = fopen($filename, "r");
		if (!$this->fd) {
			echo 'Impossible d\'ouvrir le fichier';
			exit;
		}
		switch($logType) {
			case 'debug' :
				$this->debug = true;
			case 'warn' :
				$this->warning = true;
			case 'err' :
				$this->erreur = true;
		}
	}
	
	/**
	 * traite les erreurs
	 */
	function erreur($err, $bypass = -1) {
		if ($err && $this->erreur && !$this->nolog) {
			//ouverture fichier log
			if(!$this->log) {
				$this->log = fopen($this->log_filename,"a+");
				if(!$this->log) {
					echo 'Impossible d\'ouvrir le fichier de log en écriture';
					exit;
				}
			}
			fwrite($this->log, '[Erreur        - Ligne '.$this->noLigne.'] '.$err."\n");
			if ($bypass >= 0)
				$this->ligne = $this->lireLigne($bypass);
			if (!$this->ignoreErreur) {
				$this->stop();
			}
		}
	}

	/**
	 * termine le script
	 */
	function stop() {
		if($this->fd)
			$this->length = ftell($this->fd);
		$this->annulation();
		exit;
	}

	/**
	 * efface les insertions déjà effectué ds la base
	 */
	function annulation() {
		if(!is_array($this->xref_id_init))
			return;
		
		g4p_db_query("DELETE FROM `genea_familles` WHERE familles_id>=".$this->xref_id_init['FAM']." AND familles_id<".($this->xref_id_init['FAM']+$this->nb_xref_id['FAM']),true);
  		
		g4p_db_query("DELETE FROM `genea_individuals` WHERE indi_id>=".$this->xref_id_init['INDI']." AND indi_id<".($this->xref_id_init['INDI']+$this->nb_xref_id['INDI']),true);
  		
		g4p_db_query("DELETE FROM `genea_notes` WHERE notes_id>=".$this->xref_id_init['NOTE']." AND notes_id<".($this->xref_id_init['NOTE']+$this->nb_xref_id['NOTE']),true);
  		
		g4p_db_query("DELETE FROM `genea_multimedia` WHERE media_id>=".$this->xref_id_init['OBJE']." AND media_id<".($this->xref_id_init['OBJE']+$this->nb_xref_id['OBJE']),true);
  		
		g4p_db_query("DELETE FROM `genea_repository` WHERE repo_id>=".$this->xref_id_init['REPO']." AND repo_id<".($this->xref_id_init['REPO']+$this->nb_xref_id['REPO']),true);
  		
		g4p_db_query("DELETE FROM `genea_sour_records` WHERE sour_records_id>=".$this->xref_id_init['SOUR']." AND sour_records_id<".($this->xref_id_init['SOUR']+$this->nb_xref_id['SOUR']),true);
		g4p_db_query("DELETE FROM `genea_sour_citations` WHERE sour_citations_id>=".$this->xref_id_init['SOUR_CIT']." AND sour_citations_id<".($this->xref_id_init['SOUR_CIT']+$this->nb_xref_id['SOUR_CIT']),true);
				
		g4p_db_query("DELETE FROM `genea_submitters` WHERE sub_id>=".$this->xref_id_init['SUBM']." AND sub_id<".($this->xref_id_init['SUBM']+$this->nb_xref_id['SUBM']),true);
		
		//g4p_db_query("DELETE FROM `genea_infos` WHERE id>=".$this->xref_id_init['SUBN']." AND id<".($this->xref_id_init['SUBN']+$this->nb_xref_id['SUBN']),true);
  		
  		g4p_db_query("DELETE FROM `genea_place` WHERE place_id>=".$this->xref_id_init['PLAC']." AND place_id<".($this->xref_id_init['PLAC']+$this->nb_xref_id['PLAC']),true);
  		
		g4p_db_query("DELETE FROM `genea_events_details` WHERE events_details_id>=".$this->xref_id_init['EVEN']." AND events_details_id<".($this->xref_id_init['EVEN']+$this->nb_xref_id['EVEN']),true);
		g4p_db_query("DELETE FROM `genea_events_address` WHERE events_details_id>=".$this->xref_id_init['EVEN']." AND events_details_id<".($this->xref_id_init['EVEN']+$this->nb_xref_id['EVEN']),true);
	}

	/**
	 * traite les avertissements
	 */
	function warning($warn) {
		static $error;
		$error++;
		
		if ($warn && $this->warning && !$this->nolog) {
			//ouverture fichier log
			if(!$this->log) {
				$this->log = fopen($this->log_filename,"a+");
				if(!$this->log) {
					echo 'Impossible d\'ouvrir le fichier de log en écriture';
					exit;
				}
			}
			fwrite($this->log, '[Avertissement - Ligne '.$this->noLigne.'] '.$warn."\n");
			if($error>100000)
			{
				fclose($this->log);
				exit('100k erreurs, trop c\'est trop...');
			}
			//$this->ligne = $this->lireLigne();
		}
	}

	/**
	 * traite les messages de debuggage
	 */
	function debug($debug) {
		if ($debug && $this->debug && !$this->nolog) {
			//ouverture fichier log
			if(!$this->log) {
				$this->log = fopen($this->log_filename,"a+");
				if(!$this->log) {
					echo 'Impossible d\'ouvrir le fichier de log en écriture';
					exit;
				}
			}
			fwrite($this->log, '[Debug         - Ligne '.$this->noLigne.'] '.$debug."\n");
		}
	}

	/**
	 * Convertit une chaine en UTF8
	 */
	function convert($data) {
		switch ($this->charset) {
			case 'ANSEL' :
				$data = utf8_encode(trim($data));
				$data = preg_replace($this->ansel_map, $this->ansel_replacement, $data);
				break;

			case 'UTF8' :
			case 'UTF-8' :
				$data = trim($data);
				break;

			default :
				$data = utf8_encode(trim($data));
				break;
		}
		return addslashes($data);
	}

	/**
	 * Extrait les infos d'une ligne
	 */
	function lireLigne($n = -1) {
		if ($n >= 0) { //ignore toute les lignes de niveau supérieur à $n
			//recupère la prochaine ligne avec des informations de niveau $n
			$this->ligne = $this->lireLigneSimple();
			while (!$this->ligne || $this->ligne['no'] > $n) {
				$this->warning('Ligne ignorée');
				$this->ligne = $this->lireLigneSimple();
			}
		} else {
			do {
				//recupère la prochaine ligne avec des informations
				$this->ligne = $this->lireLigneSimple();
			} while (!$this->ligne);
		}
		//print_r($this->ligne);
		return $this->ligne;
	}

	function lireLigneSimple() {
		global $tag;
		if (feof($this->fd)) {
			$this->erreur('Fin de fichier prématurée');
			$this->stop();
		}
		$s = fgets($this->fd, 4096);
		$data = explode(" ", $this->convert($s), 3);
		$this->noLigne++;
		fwrite($this->log, '[Lecture :  '.$this->noLigne.'] '.$s."\n");
		
		// Pas de tag présent
		if (count($data) < 2)
			return null;
		
		$this->ligne = array();

		if (!isset ($tag[$data[1]])) {
			// Tag non reconnu
			// si on est en début de bloc
			// il est possible que la référence soit placée avant le tag
			if ($data[0] == 0 && !typeCheck('XREF', $data[1])) {
				$reste = explode(' ',$data[2],2);
				
				if(isset($tag[$reste[0]])) {
					if (!$tag[$reste[0]]) {
						// Tag connue mais pas géré
						$this->warning('Tag non pris en compte : '.$reste[0]);
						return $this->lireLigne($data[0]);
					}
					$this->ligne['no'] = $data[0];
					$this->ligne['xref'] = $data[1];
					$this->ligne['tag'] = $reste[0];
					$this->ligne['desc'] = (isset ($reste[1])) ? $reste[1] : '';
					return $this->ligne;
				}
			}

			// Tag inconnu
			$this->warning('Tag inconnu (non standard) : '.(isset($data[1])?$data[1]:'').'('.(isset($data[0])?'niveau:'.$data[0]:'').','.(isset($data[2])?'desc:'.$data[2]:'').')');
			return $this->lireLigne($data[0]);
		}
		if (!$tag[$data[1]]) {
			// Tag connue mais pas géré
			$this->warning('Tag non pris en compte : '.$data[1]);
			return $this->lireLigne($data[0]);
		}

		$this->ligne['no'] = $data[0];
		$this->ligne['tag'] = $data[1];
		$this->ligne['desc'] = isset($data[2])?$data[2]:'';
		return $this->ligne;
	}

	/**
	 * Défini l'ordre d'apparition des informations des lieux
	 * Utilisé par le pré-traitement pre_place.php
	 */
	function setPlacePosition($position,$restart=false) {
		global $g4p_config;
		$this->debug('Pré-Traitement - Choix de l\'ordre des info de lieu');
		if(!$restart)
		{
			$this->dateStart = time();
			$this->placePosition = $position;
		
			if (!$this->fd) {
				$this->fd = fopen($this->filename, "r");
				if (!$this->fd) {
					echo 'Impossible d\'ouvrir le fichier';
					exit;
				}
			} else
				rewind($this->fd);
			$this->noLigne = 0;
		}
		$this->nolog = true;
		
		$this->initBufSql();
		$this->ligne = $this->lireLigneSimple();
		while ($this->ligne['tag']!='TRLR') {
			if($this->ligne['tag']=='PLAC') {
				if($this->hashPlace[ md5($this->ligne['desc']) ]==-1) {
					$place = array();
					$place['id'] = $this->getID_xref('PLAC');
					$this->hashPlace[ md5($this->ligne['desc']) ] = $place['id'];
					$lieu = explode(',', $this->ligne['desc']);

					foreach ($g4p_config['subdivision'] as $sub)
						if(isset($this->placePosition[$sub]))
							if(isset($lieu[$this->placePosition[$sub]]))
								$place[$sub] = trim($lieu[$this->placePosition[$sub]]);
					$this->ajoutDonnee('PLAC',$place);
				}
			}
			if($this->timeCut())
				return 'cut';
			$this->ligne = $this->lireLigneSimple();
		}
		$this->nolog = false;
		$this->flushBufSql();
	}

	/**
	 * Pré-Lecture pour recupérer le nombre de bloc (id) de chaque type
	 */
	function loadNbBlocs($restart=false) {
		$this->debug('Pré-Traitement - Comptage des differents types de blocs');
 		if(!$restart) {
			$this->dateStart = time();
			$this->ouvertureFichierLiens("w");
			if (!$this->fd) {
				$this->fd = fopen($this->filename, "r");
				if (!$this->fd) {
					echo 'Impossible d\'ouvrir le fichier';
					exit;
				}
			} else
				rewind($this->fd);
			
			$this->noLigne = 0;
			$this->nbplace = 0;
		}
		$this->nolog = true;
			
		$this->ligne = $this->lireLigneSimple();
		while ($this->ligne['tag']!='TRLR') {
			if($this->ligne['no']==0 && isset($this->nb_xref_id[$this->ligne['tag']]))
				$this->nb_xref_id[$this->ligne['tag']]++;
			else
				switch($this->ligne['tag']) {
					case 'PLAC' :
						//regroupement par lieux identiques avec une table de hachage
						if( !isset( $this->hashPlace[ md5($this->ligne['desc']) ] ) ) {
							$this->hashPlace[ md5($this->ligne['desc']) ] = -1;
							$this->nb_xref_id['PLAC']++;
							if( $this->nbplace < $this->nbDemoPlace )
								$this->demoPlace[$this->nbplace++] = $this->ligne['desc'];
						}
						break;
						
					case 'NOTE' :
						if(typeCheck('XREF', $this->ligne['desc']) )
						//Si on trouve une note intégrée alors on crée un nouvel id
						$this->nb_xref_id['NOTE']++;
						break;
					
					//indi attribute
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
					//indi event
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
					//fam event
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
						$this->nb_xref_id['EVEN']++;
						break;

					case 'SOUR' : //SOURCE_CITATION
						if(typeCheck('XREF', $this->ligne['desc']))
						{	
							$this->nb_xref_id['SOUR_CIT']++;
						}
						break;
				}
			if($this->timeCut())
				return 'cut';
			$this->ligne = $this->lireLigneSimple();
		}
		$this->nolog = false;
		
		$this->loadAutoIncrement();
		
		return 'end';
	}
	
	
	/**
	 * Pré-Traitement
	 * Appel la fonction de pré-lecture loadNbBlocs
	 * Recupère les auto incréments de la base
	 * les met dans $xref_id
	 * Reserve la place dans la base de données
	 */
	function loadAutoIncrement()
	{
		$this->debug('Pré-Traitement - Chargement des auto-increment');
		$sql = "LOCK TABLES genea_familles WRITE, genea_individuals WRITE, genea_notes WRITE," .
				" genea_multimedia WRITE, genea_repository WRITE, genea_sour_records WRITE," .
				" genea_sour_citations WRITE, genea_submitters WRITE, genea_infos WRITE," .
				" genea_place WRITE, genea_events_details WRITE;";
  		$query=g4p_db_query($sql,true);
		$sql = "SHOW TABLE STATUS LIKE 'genea_%';";
  		$query=g4p_db_query($sql,true);
  		$result=g4p_db_result($query,'Name');
  		
  		if(!empty($result['genea_familles']['Auto_increment']))
			$this->xref_id['FAM'] = $result['genea_familles']['Auto_increment'];
  		g4p_db_query("INSERT INTO `genea_familles` (familles_id, base) VALUES (".($this->xref_id['FAM']+$this->nb_xref_id['FAM']).",".$_SESSION['genea_db_id'].");",true);
  		g4p_db_query("DELETE FROM `genea_familles` WHERE familles_id=".($this->xref_id['FAM']+$this->nb_xref_id['FAM']),true);
  		
  		if(!empty($result['genea_individuals']['Auto_increment']))
			$this->xref_id['INDI'] = $result['genea_individuals']['Auto_increment'];
  		g4p_db_query("INSERT INTO `genea_individuals` (indi_id, base) VALUES (".($this->xref_id['INDI']+$this->nb_xref_id['INDI']).",".$_SESSION['genea_db_id'].");",true);
  		g4p_db_query("DELETE FROM `genea_individuals` WHERE indi_id=".($this->xref_id['INDI']+$this->nb_xref_id['INDI']),true);
  		
  		if(!empty($result['genea_notes']['Auto_increment']))
			$this->xref_id['NOTE'] = $result['genea_notes']['Auto_increment'];
  		g4p_db_query("INSERT INTO `genea_notes` (notes_id, base) VALUES (".($this->xref_id['NOTE']+$this->nb_xref_id['NOTE']).",".$_SESSION['genea_db_id'].");",true);
  		g4p_db_query("DELETE FROM `genea_notes` WHERE notes_id=".($this->xref_id['NOTE']+$this->nb_xref_id['NOTE']),true);
  		
  		if(!empty($result['genea_multimedia']['Auto_increment']))
			$this->xref_id['OBJE'] = $result['genea_multimedia']['Auto_increment'];
  		g4p_db_query("INSERT INTO `genea_multimedia` (media_id, base) VALUES (".($this->xref_id['OBJE']+$this->nb_xref_id['OBJE']).",".$_SESSION['genea_db_id'].");",true);
  		g4p_db_query("DELETE FROM `genea_multimedia` WHERE media_id=".($this->xref_id['OBJE']+$this->nb_xref_id['OBJE']),true);
  		
  		if(!empty($result['genea_repository']['Auto_increment']))
			$this->xref_id['REPO'] = $result['genea_repository']['Auto_increment'];
  		g4p_db_query("INSERT INTO `genea_repository` (repo_id, base) VALUES (".($this->xref_id['REPO']+$this->nb_xref_id['REPO']).",".$_SESSION['genea_db_id'].");",true);
  		g4p_db_query("DELETE FROM `genea_repository` WHERE repo_id=".($this->xref_id['REPO']+$this->nb_xref_id['REPO']),true);
  		
  		if(!empty($result['genea_sour_records']['Auto_increment']))
			$this->xref_id['SOUR'] = $result['genea_sour_records']['Auto_increment'];
  		g4p_db_query("INSERT INTO `genea_sour_records` (sour_records_id, base) VALUES (".($this->xref_id['SOUR']+$this->nb_xref_id['SOUR']).",".$_SESSION['genea_db_id'].");",true);
  		g4p_db_query("DELETE FROM `genea_sour_records` WHERE sour_records_id=".($this->xref_id['SOUR']+$this->nb_xref_id['SOUR']).';',true);
  		
  		if(!empty($result['genea_sour_citations']['Auto_increment']))
			$this->xref_id['SOUR_CIT'] = $result['genea_sour_citations']['Auto_increment'];
  		g4p_db_query("INSERT INTO `genea_sour_citations` (sour_citations_id, base) VALUES (".($this->xref_id['SOUR_CIT']+$this->nb_xref_id['SOUR_CIT']).",".$_SESSION['genea_db_id'].");",true);
  		g4p_db_query("DELETE FROM `genea_sour_citations` WHERE sour_citations_id=".($this->xref_id['SOUR_CIT']+$this->nb_xref_id['SOUR_CIT']).';',true);
  		
  		if(!empty($result['genea_submitters']['Auto_increment']))
			$this->xref_id['SUBM'] = $result['genea_submitters']['Auto_increment'];
  		g4p_db_query("INSERT INTO `genea_submitters` (sub_id) VALUES (".($this->xref_id['SUBM']+$this->nb_xref_id['SUBM']).");",true);
  		g4p_db_query("DELETE FROM `genea_submitters` WHERE sub_id=".($this->xref_id['SUBM']+$this->nb_xref_id['SUBM']),true);
  		
		//FIXME table sql non compatible
  		/*
  		 * genea_infos correspond à une base (1 généalogie) et non pas un gedcom
  		if(!empty($result['genea_infos']['Auto_increment']))
			$this->xref_id['SUBN'] = $result['genea_infos']['Auto_increment'];
  		g4p_db_query("INSERT INTO `genea_infos` (indi_id, base) VALUES (".($this->xref_id['SUBN']+$this->nb_xref_id['SUBN']).",".$_SESSION['genea_db_id'].");",true);
  		g4p_db_query("DELETE FROM `genea_infos` WHERE indi_id=".($this->xref_id['SUBN']+$this->nb_xref_id['SUBN']),true);
  		*/
  		
  		if(!empty($result['genea_place']['Auto_increment']))
			$this->xref_id['PLAC'] = $result['genea_place']['Auto_increment'];
  		g4p_db_query("INSERT INTO `genea_place` (place_id, base) VALUES (".($this->xref_id['PLAC']+$this->nb_xref_id['PLAC']).",".$_SESSION['genea_db_id'].");",true);
  		g4p_db_query("DELETE FROM `genea_place` WHERE place_id=".($this->xref_id['PLAC']+$this->nb_xref_id['PLAC']),true);
  		
  		if(!empty($result['genea_events_details']['Auto_increment']))
			$this->xref_id['EVEN'] = $result['genea_events_details']['Auto_increment'];
  		g4p_db_query("INSERT INTO `genea_events_details` (events_details_id, base) VALUES (".($this->xref_id['EVEN']+$this->nb_xref_id['EVEN']).",".$_SESSION['genea_db_id'].");",true);
  		g4p_db_query("DELETE FROM `genea_events_details` WHERE events_details_id=".($this->xref_id['EVEN']+$this->nb_xref_id['EVEN']),true);
  		
		g4p_db_query("UNLOCK TABLES;",true);

		$this->xref_id_init = $this->xref_id;
	}
	
	/**
	 * Renvoie l'id correspondant au xref
	 * crée la correspondance si aucune n'existe pour cet xref. 
	 */
	function getID_xref($typeXREF, $xref=false) {
		if($xref===false) {
			 return $this->xref_id[$typeXREF]++;
		}
		if (!isset ($this->xref[$typeXREF][$xref])) {
			$this->xref[$typeXREF][$xref] = $this->xref_id[$typeXREF]++;
		}
		return $this->xref[$typeXREF][$xref];
	}


	/**
	 * Initialise les buffer SQL
	 */
	function initBufSql()
	{
		$this->nbReq['INDI'] = 0;
		$this->bufsql['INDI'] = "INSERT INTO `genea_individuals` ( `indi_id` , `base` , `indi_nom` , `indi_prenom` , `indi_sexe` , `indi_timestamp` , `indi_npfx` , `indi_givn` , `indi_nick` , `indi_spfx` , `indi_surn` , `indi_nsfx` , `indi_resn` ) VALUES";
	/* la table famille est traitée comme une table de lien
	 * 	$this->nbReq['FAM'] = 0;
	 *	$this->bufsql['FAM'] = "INSERT INTO `genea_familles` ( `familles_id` , `base` , `familles_wife` , `familles_husb` , `familles_timestamp` ) VALUES";
	 */
		$this->nbReq['NOTE'] = 0;
		$this->bufsql['NOTE'] = "INSERT INTO `genea_notes` ( `notes_id` , `base` , `notes_text` , `notes_timestamp` ) VALUES";
		$this->nbReq['OBJE'] = 0;
		$this->bufsql['OBJE'] = "INSERT INTO `genea_multimedia` ( `media_id` , `base` , `media_title` , `media_format` , `media_file` , `media_timestamp` ) VALUES";
		$this->nbReq['REPO'] = 0;
		$this->bufsql['REPO'] = "INSERT INTO `genea_repository` ( `repo_id` , `base` , `repo_name` , `repo_addr` , `repo_city` , `repo_post` , `repo_stae` , `repo_ctry` , `repo_phon1` , `repo_phon2` , `repo_phon3` , `repo_timestamp` ) VALUES";
		$this->nbReq['SOUR'] = 0;
		$this->bufsql['SOUR'] = "INSERT INTO `genea_sour_records` ( `sour_records_id`, `sour_records_auth`, `sour_records_title`, `sour_records_abbr`, `sour_records_publ`, `sour_records_data_even`, `sour_records_data_date`, `sour_records_data_place`, `sour_records_agnc`, `repo_id`, `repo_caln`, `repo_medi`, `sour_records_timestamp`, `base` ) VALUES";
		$this->nbReq['SOUR_CIT'] = 0;
		$this->bufsql['SOUR_CIT'] = "INSERT INTO `genea_sour_citations` ( `sour_citations_id`, `sour_records_id`, `sour_citations_page`, `sour_citations_even`, `sour_citations_even_role`, `sour_citations_data_dates`, `sour_citations_data_text`, `sour_citations_quay`, `sour_citations_subm`, `sour_citations_timestamp`, `base` ) VALUES";
		$this->nbReq['SUBM'] = 0;
		$this->bufsql['SUBM'] = "INSERT INTO `genea_submitters` ( `sub_id` , `sub_name` , `sub_timestamp` , `sub_addr` , `sub_city` , `sub_stae` , `sub_post` , `sub_ctry` , `sub_phon1` , `sub_phon2` , `sub_phon3` , `sub_lang` ) VALUES";
		//FIXME table sql non compatible
		$this->nbReq['SUBN'] = 0;
		$this->bufsql['SUBN'] = "";
		$this->nbReq['PLAC'] = 0;
		$this->bufsql['PLAC'] = "INSERT INTO `genea_place` ( `place_id` , `place_lieudit` , `place_ville` , `place_cp` , `place_insee` , `place_departement` , `place_region` , `place_pays` , `place_longitude` , `place_latitude` , `base` ) VALUES";
		$this->nbReq['EVEN'] = 0;
		$this->bufsql['EVEN'] = "INSERT INTO `genea_events_details` ( `events_details_id` , `place_id` , `events_details_descriptor` , `events_details_gedcom_date` , `events_details_age` , `events_details_cause` , `jd_count` , `jd_precision` , `jd_calendar` , `base` , `events_details_timestamp` ) VALUES";
		$this->nbReq['EVEN_ADDR'] = 0;
		$this->bufsql['EVEN_ADDR'] = "INSERT INTO `genea_events_address` ( `events_details_id` , `base` , `address_addr` , `address_city` , `address_stae` , `address_post` , `address_ctry` , `address_phon1` , `address_phon2` , `address_phon3` ) VALUES";
	}


	/**
	 * Formatte les données pour l'insertion SQL
	 * Ajoute le résultat dans le buffer sql bufsql[$type]
	 * Si le nb de requêtes max est atteint vide les buffers
	 */
	function ajoutDonnee($type, $data) {
		$this->nbReq[$type]++;
		
		foreach($data as $key=>$val)
		{
			$data[$key] = str_replace('@@','@',$data[$key]);
		}
		
		switch($type) { 
			case 'INDI' :
				$data['indi_id'] = (empty($data['indi_id']))?'':$data['indi_id'];
				$data['indi_nom'] = (empty($data['indi_nom']))?'':$data['indi_nom'];
				$data['indi_prenom'] = (empty($data['indi_prenom']))?'':$data['indi_prenom'];
				if(!isset($data['sex']))
					$data['sex'] = '';
				if($data['sex']=='M' || $data['sex']=='F')
					$data['indi_sexe'] = $data['sex'];
				else
					$data['indi_sexe'] = '';
				
				$data['indi_chan'] = (empty($data['indi_chan']))?'NULL':"'".$data['indi_chan']."'";
				$data['npfx'] = (empty($data['npfx']))?'':$data['npfx'];
				$data['givn'] = (empty($data['givn']))?'':$data['givn'];
				$data['nick'] = (empty($data['nick']))?'':$data['nick'];
				$data['spfx'] = (empty($data['spfx']))?'':$data['spfx'];
				$data['surn'] = (empty($data['surn']))?'':$data['surn'];
				$data['nsfx'] = (empty($data['nsfx']))?'':$data['nsfx'];
				$data['resn'] = (empty($data['resn']))?'NULL':"'".$data['resn']."'";

				$this->bufsql['INDI'] .= " ('".$data['indi_id']."', '".$this->base."', '".$data['indi_nom']."', '".$data['indi_prenom']."', '".$data['indi_sexe']."', ".$data['indi_chan'].", '".$data['npfx']."', '".$data['givn']."', '".$data['nick']."', '".$data['spfx']."', '".$data['surn']."', '".$data['nsfx']."', ".$data['resn']."),";
				break;
		/* 	la table famille est traitée comme une table de lien
		 *	case 'FAM' :
		 *	 	$data['familles_id'] = (empty($data['id']))?'':$data['id'];
		 *	 	$data['familles_wife'] = (empty($data['wife']))?'NULL':"'".$data['wife']."'";
		 *	 	$data['familles_husb'] = (empty($data['husb']))?'NULL':"'".$data['husb']."'";
		 *	 	$data['familles_chan'] = (empty($data['chan']))?'NULL':"'".$data['chan']."'";
		 *	 	
		 *		$this->bufsql['FAM'] .= " ('".$data['familles_id']."', '".$this->base."', ".$data['familles_wife']." , ".$data['familles_husb']." , '".$data['familles_chan']."'),";
		 *		break;
		 */	
			case 'NOTE' :
				$data['notes_id'] = (empty($data['id']))?'':$data['id'];
				$data['notes_text'] = (empty($data['notes_text']))?'':$data['notes_text'];
				$data['notes_chan'] = (empty($data['notes_chan']))?'NULL':"'".$data['notes_chan']."'";

				$this->bufsql['NOTE'] .= " ('".$data['notes_id']."', '".$this->base."', '".$data['notes_text']."', ".$data['notes_chan']."),";
				break;
			
			case 'OBJE' :
			 	$data['id'] = (empty($data['id']))?'':$data['id'];
			 	$data['title'] = (empty($data['titl']))?'':$data['titl'];
			 	$data['format'] = (empty($data['form']))?'':$data['form'];
			 	$data['file'] = (empty($data['file']))?'':$data['file'];
			 	$data['chan'] = (empty($data['chan']))?'NULL':"'".$data['chan']."'";
			 
				$this->bufsql['OBJE'] .= " ('".$data['id']."', '".$this->base."', '".$data['title']."', '".$data['format']."', '".$data['file']."', '".$data['chan']."'),";
				break;
			
			case 'REPO' :
			 	$data['repo_id'] = (empty($data['id']))?'':$data['id'];
			 	$data['repo_name'] = (empty($data['name']))?'':$data['name'];
			 	$data['repo_addr'] = (empty($data['adress_line']))?'':$data['adress_line'];
			 	$data['repo_city'] = (empty($data['city']))?'':$data['city'];
			 	$data['repo_post'] = (empty($data['post']))?'':$data['post'];
			 	$data['repo_stae'] = (empty($data['stae']))?'':$data['stae'];
			 	$data['repo_ctry'] = (empty($data['ctry']))?'':$data['ctry'];
			 	$data['repo_phon1'] = (empty($data['phon1']))?'':$data['phon1'];
			 	$data['repo_phon2'] = (empty($data['phon2']))?'':$data['phon2'];
			 	$data['repo_phon3'] = (empty($data['phon3']))?'':$data['phon3'];
			 	$data['repo_chan'] = (empty($data['chan']))?'NULL':"'".$data['chan']."'";
			 	
				$this->bufsql['REPO'] .= " ('".$data['repo_id']."', '".$this->base."', '".$data['repo_name']."', '".$data['repo_addr']."', '".$data['repo_city']."', '".$data['repo_post']."', '".$data['repo_stae']."', '".$data['repo_ctry']."', '".$data['repo_phon1']."', '".$data['repo_phon2']."', '".$data['repo_phon3']."', ".$data['repo_chan']."),";
				break;

			case 'SOUR' :
			 	$data['sour_records_id'] = (empty($data['id']))?'':$data['id'];
			 	$data['sour_records_auth'] = (empty($data['auth']))?'':$data['auth'];
			 	$data['sour_records_title'] = (empty($data['titl']))?'':$data['titl'];
			 	$data['sour_records_abbr'] = (empty($data['abbr']))?'':$data['abbr'];
			 	$data['sour_records_publ'] = (empty($data['publ']))?'':$data['publ'];
			 	$data['sour_records_data_even'] = '';
			 	$data['sour_records_data_date'] = '';
			 	$data['sour_records_data_place'] = '';
//FIXME champs à mettre dans la BD	 	$data['sources_text'] = (empty($data['text']))?'':$data['text'];
			 	$data['sour_records_agnc'] = (empty($data['agnc']))?'':$data['agnc'];
			 	$data['repo_id'] = 'NULL'; //mis à jour avec les liens
			 	if($data['repo_id']>=0)
			 		fputs($this->fdSourcesRepo, $data['sour_records_id']."\t".$data['repo_id']."\n");
			 	$data['repo_caln'] = (empty($data['repo_caln']))?'':$data['repo_caln'];
			 	$data['repo_medi'] = (empty($data['repo_medi']))?'':$data['repo_medi'];
			 	$data['sour_records_timestamp'] = (empty($data['chan']))?'NULL':"'".$data['chan']."'";
			 	
				$this->bufsql['SOUR'] .= " ('".$data['sour_records_id']."', '".$data['sour_records_auth']."', '".$data['sour_records_title']."', '".$data['sour_records_abbr']."', '".$data['sour_records_publ']."', '".$data['sour_records_data_even']."', '".$data['sour_records_data_date']."', '".$data['sour_records_data_place']."', '".$data['sour_records_agnc']."', '".$data['repo_id']."', '".$data['repo_caln']."', '".$data['repo_medi']."', ".$data['sour_records_timestamp'].", '".$this->base."'),";
				break;
		
			case 'SOUR_CIT' :
			 	$data['sour_citations_id'] = (empty($data['sour_citations_id']))?'':$data['sour_citations_id'];
			 	$data['sour_records_id'] = 'NULL'; //mis à jour avec les liens
			 	if($data['sour_records_id']>=0)
			 		fputs($this->fdSourcesCitRec, $data['sour_citations_id']."\t".$data['sour_records_id']."\n");
			 	$data['sour_citations_page'] = (empty($data['page']))?'':$data['page'];
			 	$data['sour_citations_even'] = (empty($data['even']))?'':$data['even'];
			 	$data['sour_citations_even_role'] = (empty($data['role']))?'':$data['role'];
			 	$data['sour_citations_data_dates'] = (empty($data['data_date']))?'':$data['data_date'];
			 	$data['sour_citations_data_text'] = (empty($data['data_text']))?'':$data['data_text'];
			 	$data['sour_citations_subm'] = ''; //FIXME
			 	$data['sour_citations_quay'] = (empty($data['quay']))?'':$data['quay'];
			 	$data['sour_citations_timestamp'] = 'NULL';
			 	
				$this->bufsql['SOUR_CIT'] .= " ('".$data['sour_citations_id']."', ".$data['sour_records_id']." , '".$data['sour_citations_page']."', '".$data['sour_citations_even']."', '".$data['sour_citations_even_role']."', '".$data['sour_citations_data_dates']."', '".$data['sour_citations_data_text']."', '".$data['sour_citations_quay']."', '".$data['sour_citations_subm']."', ".$data['sour_citations_timestamp'].", '".$this->base."'),";
				fwrite($this->log, '[Sour        - Ligne '.$this->noLigne.'] '." ('".$data['sour_citations_id']."', ".$data['sour_records_id']." , '".$data['sour_citations_page']."', '".$data['sour_citations_even']."', '".$data['sour_citations_even_role']."', '".$data['sour_citations_data_dates']."', '".$data['sour_citations_data_text']."', '".$data['sour_citations_quay']."', '".$data['sour_citations_subm']."', ".$data['sour_citations_timestamp'].", '".$this->base."\n");
				break;
			
			case 'SUBM' :
			 	$data['sub_id'] = (empty($data['id']))?'':$data['id'];
			 	$data['sub_name'] = (empty($data['name']))?'':$data['name'];
			 	$data['sub_chan'] = (empty($data['chan']))?'NULL':"'".$data['chan']."'";
			 	$data['sub_addr'] = (empty($data['adress_line']))?'':$data['adress_line'];
			 	$data['sub_city'] = (empty($data['city']))?'':$data['city'];
			 	$data['sub_post'] = (empty($data['post']))?'':$data['post'];
			 	$data['sub_stae'] = (empty($data['stae']))?'':$data['stae'];
			 	$data['sub_ctry'] = (empty($data['ctry']))?'':$data['ctry'];
			 	$data['sub_phon1'] = (empty($data['phon1']))?'':$data['phon1'];
			 	$data['sub_phon2'] = (empty($data['phon2']))?'':$data['phon2'];
			 	$data['sub_phon3'] = (empty($data['phon3']))?'':$data['phon3'];
			 	$data['sub_lang'] = (empty($data['lang']))?'':$data['lang'];
			 	
				$this->bufsql['SUBM'] .= " ('".$data['sub_id']."', '".$data['sub_name']."', ".$data['sub_chan'].", '".$data['sub_addr']."', '".$data['sub_city']."', '".$data['sub_stae']."', '".$data['sub_post']."', '".$data['sub_ctry']."', '".$data['sub_phon1']."', '".$data['sub_phon2']."', '".$data['sub_phon3']."', '".$data['sub_lang']."'),";
				break;
			
			case 'SUBN' :
				//FIXME table sql non compatible
				$this->bufsql['SUBN'] .= " (),";
				break;
			
			case 'PLAC' :
				$data['place_id'] = (empty($data['id']))?'':$data['id'];
				$data['place_lieudit'] = (empty($data['place_lieudit']))?'NULL':"'".$data['place_lieudit']."'";
				$data['place_ville'] = (empty($data['place_ville']))?'NULL':"'".$data['place_ville']."'";
				$data['place_cp'] = (empty($data['place_cp']))?'NULL':"'".$data['place_cp']."'";
				$data['place_insee'] = (empty($data['place_insee']))?'NULL':"'".$data['place_insee']."'";
				$data['place_departement'] = (empty($data['place_departement']))?'NULL':"'".$data['place_departement']."'";
				$data['place_region'] = (empty($data['place_region']))?'NULL':"'".$data['place_region']."'";
				$data['place_pays'] = (empty($data['place_pays']))?'NULL':"'".$data['place_pays']."'";
				$data['place_longitude'] = (empty($data['place_longitude']))?'NULL':"'".$data['place_longitude']."'";
				$data['place_latitude'] = (empty($data['place_latitude']))?'NULL':"'".$data['place_latitude']."'";
				
				$this->bufsql['PLAC'] .= " ('".$data['place_id']."', ".$data['place_lieudit'].", ".$data['place_ville'].", ".$data['place_cp'].", ".$data['place_cp'].", ".$data['place_departement'].", ".$data['place_region'].", ".$data['place_pays'].", ".$data['place_longitude'].", ".$data['place_latitude'].", '".$this->base."'),";
				break;

			case 'EVEN' :
				$data['id'] = (empty($data['id']))?'':$data['id'];
				$data['place_id'] = (empty($data['place_id']))?'NULL':"'".$data['place_id']."'";
				$data['type'] = (empty($data['type']))?'':$data['type'];
				$data['date_event'] = (empty($data['date_event']))?'':$data['date_event'];
				$data['age'] = (empty($data['age']))?'':$data['age'];
				$data['cause'] = (empty($data['cause']))?'':$data['cause'];
				
				$jd = g4p_jd_extract($data['date_event']);
				$data['jd_count'] = (empty($jd['jd_count']))?'NULL':"'".$jd['jd_count']."'";
				$data['jd_precision'] = (empty($jd['precision']))?'NULL':"'".$jd['precision']."'";
				$data['jd_calendar'] = (empty($jd['calendrier']))?'NULL':"'".$jd['calendrier']."'";
				$data['attestation'] = (empty($data['attestation']))?'':$data['attestation'];
				
			 	$data['chan'] = 'NULL';

				$this->bufsql['EVEN'] .= " ('".$data['id']."', ".$data['place_id']." , '".$data['type']."', '".$data['date_event']."', '".$data['age']."', '".$data['cause']."', ".$data['jd_count']." , ".$data['jd_precision']." , ".$data['jd_calendar']." , '".$this->base."', ".$data['chan']."),";
				
				//events_address
			 	$data['addr'] = (empty($data['adress_line']))?'':$data['adress_line'];
			 	$data['city'] = (empty($data['city']))?'':$data['city'];
			 	$data['post'] = (empty($data['post']))?'':$data['post'];
			 	$data['stae'] = (empty($data['stae']))?'':$data['stae'];
			 	$data['ctry'] = (empty($data['ctry']))?'':$data['ctry'];
			 	$data['phon1'] = (empty($data['phon1']))?'':$data['phon1'];
			 	$data['phon2'] = (empty($data['phon2']))?'':$data['phon2'];
			 	$data['phon3'] = (empty($data['phon3']))?'':$data['phon3'];
			 	
			 	if( $data['addr']!='' || $data['city']!='' || $data['post']!='' 
			 		|| $data['stae']!='' || $data['ctry']!='' || $data['phon1']!='' 
			 		|| $data['phon2']!='' || $data['phon2']!='')
			 	{
			 		$this->bufsql['EVEN_ADDR'] .= " ('".$data['id']."', '".$this->base."', '".$data['addr']."' , '".$data['city']."', '".$data['stae']."', '".$data['post']."', '".$data['ctry']."', '".$data['phon1']."', '".$data['phon2']."' , '".$data['phon3']."'),";
			 		$this->nbReq['EVEN_ADDR']++;
			 	}
				break;
			default :
				$this->debug('Type de donnéee SQL invalide : '.$type);
		}
		
		//envoi des requetes si le nb max est atteint
		if($this->nbReq[$type] > $this->maxReq) {
			$this->flushBufSql();
		}
	}
	
	/**
	 * Effectue les requêtes contenues dans le buffer
	 * Vide le buffer
	 */
	function flushBufSql() {
		foreach($this->bufsql as $type => $sql) {
			if($this->nbReq[$type]>0) {
				$sql = substr($sql, 0, -1);
				$this->debug('Requête  SQL ('.$type.') : '.$sql);
				g4p_db_query($sql);
			}
		}
		$this->initBufSql();
	}

	/**
	 * Analyse le gedcom chargé
	 */
	function analyse() {
		global $g4p_chemin;
		$this->dateStart = time();
		
		if (!$this->fd) {
			$this->fd = fopen($this->filename, "r");
			if (!$this->fd) {
				echo 'Impossible d\'ouvrir le fichier';
				exit;
			}
		} else
			rewind($this->fd);
		
		// Réouverture des fichiers de liens
		$this->ouvertureFichierLiens('w');
				
		$this->noLigne = 0;
		
		$this->initBufSql();
		
		if($this->lectureGedcom()=='cut')
			return 'cut';

		$this->length = ftell($this->fd);
		$this->flushBufSql();
	}
	
	/**
	 * Découpage de l'execution en plusieurs parties
	 */
	function timeCut() {
		if (time() - $this->dateStart + $this->margeTps >= get_cfg_var('max_execution_time')) {
			//Le script a dépassé la limite d'execution
			//Coupure
			if($this->fd)
				$this->length = ftell($this->fd);
			$this->flushBufSql();
			$this->nolog = false;
			$this->debug('Arrêt de l\'analyse');
			return true;
		}
		return false;
	}

	/**
	 * Reprise de l'execution après une coupure
	 */
	function restartCut($type='') {
		global $g4p_chemin;
		$this->dateStart = time();
		/*
		 * Réouverture du fichier gedcom et positionnement
		 */
		$this->fd = fopen($this->filename, "r");
		if (!$this->fd) {
			echo 'Impossible d\'ouvrir le fichier';
			exit;
		}
		fseek($this->fd, $this->length, SEEK_SET);
		

		$this->initBufSql();
		
		if($type=='nbBlocs') {
			$this->debug('Reprise de la pré-analyse');
			return $this->loadNbBlocs(true);
		} else if($type=='placePosition') {
			$this->debug('Reprise de la pré-analyse (enregistrement des lieux)');
			return $this->setPlacePosition(null,true);
		}
		
		// Réouverture des fichiers de liens
		$this->ouvertureFichierLiens('a');
		
		$this->debug('Reprise de l\'analyse');
		
		if($this->lectureGedcom(true)=='cut')
			return 'cut';
			
		$this->length = ftell($this->fd);
		$this->flushBufSql();
	}
	
	
	/**
	 * Ouverture des fichiers de liens
	 * si un problème d'ouverture apparait
	 * on arrete le script immédiatement
	 * @param restart true si relance du script après coupure
	 */
	 function ouvertureFichierLiens($mode){
	 	global $g4p_chemin;
		
		//Ouverture fichier genea_familles
		$file = $g4p_chemin.'temp/'.$this->prefix.'_genea_familles.txt';
		$this->debug('Ouverture du fichier de lien '.$file.' (mode : '.$mode.')');
		$this->fdFamilles = @fopen($file,$mode);
		if(!$this->fdFamilles){
			$this->erreur('Impossible d\'ouvrir le fichier "'.$file.'" (mode : '.$mode.')');
			$this->stop();
		}

		//Ouverture fichier sour_records.repo_id
		$file = $g4p_chemin.'temp/'.$this->prefix.'_update_genea_sour_records.txt';
		$this->debug('Ouverture du fichier de lien '.$file.' (mode : '.$mode.')');
		$this->fdSourcesRepo = @fopen($file,$mode);
		if(!$this->fdSourcesRepo){
			$this->erreur('Impossible d\'ouvrir le fichier "'.$file.'" (mode : '.$mode.')');
			$this->stop();
		}

		//Ouverture fichier sour_citations.sour_records_id
		$file = $g4p_chemin.'temp/'.$this->prefix.'_update_genea_sour_citations.txt';
		$this->debug('Ouverture du fichier de lien '.$file.' (mode : '.$mode.')');
		$this->fdSourcesCitRec = @fopen($file,$mode);
		if(!$this->fdSourcesCitRec){
			$this->erreur('Impossible d\'ouvrir le fichier "'.$file.'" (mode : '.$mode.')');
			$this->stop();
		}

		//Ouverture fichier de liens (rel_*)
		foreach($this->fdlien as $lien => $fdlien)
		{
			$file = $g4p_chemin.'temp/'.$this->prefix.'_lien_'.$lien.'.txt';
			$this->debug('Ouverture du fichier de lien '.$file.' (mode : '.$mode.')');
			$this->fdlien[$lien] = @fopen($file,$mode);
			if(!$this->fdlien[$lien]){
				$this->erreur('Impossible d\'ouvrir le fichier "'.$file.'" (mode : '.$mode.')');
				$this->stop();
			}
		}
}

	/**
	 * Ajoute le lien dans le fichier correspondant
	 */
	function ajoutLien($table, $id1, $id2, $desc='', $desc2='') {
		if(!isset($this->fdlien[$table]))
		{
			$this->warning('Table de lien  "'.$table.'" non définie (lien '.$id1.' - '.$id2.' - '.$desc.' - '.$desc2.')');
			return;
		}
		fputs($this->fdlien[$table], $id1."\t".$id2."\t".$desc."\t".$desc2."\n");
		$this->debug('Lien ajouté dans '.$table.' : '.$id1.' - '.$id2." - ".$desc." - ".$desc2);
	}
	
	/**
	 * Insertion de lien dans la BD
	 * Fonction générique
	 */
	function insertionLienUnique($table, $f, $id1, $id2, $desc = '', $desc2='') {
		$this->debug('Post-traitement - Insertion des liens : '.$table);
		while(!(feof($f))) {
			$nb = 0;
			$sql = 'INSERT INTO `'.$table.'` (`'.$id1.'`, `'.$id2.'`';
			if(!empty($desc))
				$sql.= ',`'.$desc.'`';
			if(!empty($desc2))
				$sql.= ',`'.$desc2.'`';
			$sql.= ') VALUES';
			while(!feof($f) && $nb<$this->maxReq) {
				$s = fgets($f, 4096);
				if(!$s) continue;
				$nb++;
				$data = explode("\t", trim($s), 4);
				$sql.= " (".$data[0].",".$data[1];
				if(!empty($desc))
					$sql.= ",'".(isset($data[2])?$data[2]:'')."'";
				if(!empty($desc2))
					$sql.= ",'".(isset($data[3])?$data[3]:'')."'";
				$sql.= "),";
			}
			if($nb>0) {
				$sql = substr($sql, 0, -1);
				
				if(!g4p_db_query($sql,true))
				{
					$this->nb_err_lien++;
					$this->debug('Echec Requête  SQL ('.$table.') : '.$sql);
				} else
					$this->debug('Requête  SQL ('.$table.') : '.$sql);
			}
		}
	}
	
	/**
	 *  Insertion des tables de liens dans la BD
	 */
	function insertionLien() {
		global $g4p_chemin;
		$this->dateStart = time();
		$this->ouvertureFichierLiens('r');
		foreach($this->fdlien as $f)
			rewind($f);
			
		$this->maxReq = 1;
		
		switch($this->lien_courant) {
			case 0 :
				//Insertion des familles
				if($this->timeCut()) return 'cut';
				$this->debug('Post-traitement - Insertion des familles');
				$this->lien_courant++;
				while(!(feof($this->fdFamilles))) {
					$nb = 0;
					$sql = 'INSERT INTO `genea_familles` (`base`, `familles_id`, `familles_husb`, `familles_wife`, `familles_timestamp`) VALUES';
					while(!feof($this->fdFamilles) && $nb<$this->maxReq) {
						$s = fgets($this->fdFamilles, 4096);
						if(!$s) continue;
						$nb++;
						$data = explode("\t", trim($s), 4);
						for($i=0;$i<3;$i++)
							if(empty($data[$i]))
								$data[$i] = 'NULL';
						if(empty($data[3]))
							$data[3] = '0000-00-00 00:00:00';
						$sql.= " (".$this->base.",".$data[0].",".$data[1].",".$data[2].",'".$data[3]."'),";
					}
					if($nb>0) {
						$sql = substr($sql, 0, -1);
						if(!g4p_db_query($sql,true))
						{
							$this->nb_err_lien++;
							$this->debug('Echec Requête  SQL (genea_familles) : '.$sql);
						} else
							$this->debug('Requête  SQL (genea_familles) : '.$sql);
					}
				}
					
				//Insertion des relations
			case 1 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_alias', $this->fdlien['alia'], 'alias1', 'alias2');
			case 2 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_asso_events', $this->fdlien['asso_events'], 'indi_id', 'events_details_id', 'description');
			case 3 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_asso_familles', $this->fdlien['asso_familles'], 'indi_id', 'familles_id', 'description');
			case 4 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_asso_indi', $this->fdlien['asso_indi'], 'indi_id1', 'indi_id2', 'description');
			case 5 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_events_multimedia', $this->fdlien['events_multimedia'], 'events_details_id', 'media_id');
			case 6 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_events_notes', $this->fdlien['events_notes'], 'events_details_id', 'notes_id');
			case 7 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_events_sources', $this->fdlien['events_sources'], 'events_details_id', 'sour_citations_id');
			case 8 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_familles_events', $this->fdlien['familles_events'], 'familles_id', 'events_details_id', 'events_tag', 'events_attestation');
			case 9 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_familles_indi', $this->fdlien['familles_indi'], 'familles_id', 'indi_id', 'rela_type');
			case 10 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_familles_multimedia', $this->fdlien['familles_multimedia'], 'familles_id', 'media_id');
			case 11 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_familles_notes', $this->fdlien['familles_notes'], 'familles_id', 'notes_id');
			case 12 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_familles_sources', $this->fdlien['familles_sources'], 'familles_id', 'sour_citations_id');
			case 13 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_indi_attributes', $this->fdlien['indi_attributes'], 'indi_id', 'events_details_id', 'events_tag', 'events_descr');
			case 14 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_indi_events', $this->fdlien['indi_events'], 'indi_id', 'events_details_id', 'events_tag', 'events_attestation');
			case 15 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_indi_multimedia', $this->fdlien['indi_multimedia'], 'indi_id', 'media_id');
			case 16 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_indi_notes', $this->fdlien['indi_notes'], 'indi_id', 'notes_id');
			case 17 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_indi_sources', $this->fdlien['indi_sources'], 'indi_id', 'sour_citations_id');
			case 18 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_multimedia_notes', $this->fdlien['multimedia_notes'], 'media_id', 'notes_id');
			case 19 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_repo_notes', $this->fdlien['repo_notes'], 'repo_id', 'notes_id');
			case 20 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_sour_citations_multimedia', $this->fdlien['sour_citations_multimedia'], 'sour_citations_id', 'media_id');
			case 21 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				$this->insertionLienUnique('rel_sour_citations_notes', $this->fdlien['sour_citations_notes'], 'sour_citations_id', 'notes_id');
			case 22 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				//Maj des liens genea_sources.repo_id
				$this->debug('Post-traitement - Mise à jour des sources records (liens repository)');
				while(!feof($this->fdSourcesRepo)) {
					$s = fgets($this->fdSourcesRepo, 4096);
					if(!$s) continue;
					$data = explode("\t", trim($s), 2);
					$sql = "UPDATE `genea_sour_records` SET `repo_id`=".$data[1];
					$sql.= " WHERE `base`=".$this->base." AND `sour_records_id`=".$data[0];
					$this->debug('Requête  SQL (genea_sources) : '.$sql);
					if(!g4p_db_query($sql,true))
						$this->nb_err_lien++;
				}
			case 23 :
				if($this->timeCut()) return 'cut';
				$this->lien_courant++;
				//Maj des liens genea_sources.repo_id
				$this->debug('Post-traitement - Mise à jour des sources citations (liens sources records)');
				while(!feof($this->fdSourcesCitRec)) {
					$s = fgets($this->fdSourcesCitRec, 4096);
					if(!$s) continue;
					$data = explode("\t", trim($s), 2);
					$sql = "UPDATE `genea_sour_citations` SET `sour_records_id`=".$data[1];
					$sql.= " WHERE `base`=".$this->base." AND `sour_citations_id`=".$data[0];
					$this->debug('Requête  SQL (genea_sources) : '.$sql);
					if(!g4p_db_query($sql,true))
						$this->nb_err_lien++;
				}
		}

		//Suppression des fichiers temporaires de liens
		unlink($g4p_chemin.'temp/'.$this->prefix.'_genea_familles.txt');
		unlink($g4p_chemin.'temp/'.$this->prefix.'_update_genea_sour_records.txt');
		unlink($g4p_chemin.'temp/'.$this->prefix.'_update_genea_sour_citations.txt');
		foreach($this->fdlien as $lien=>$f)
			unlink($g4p_chemin.'temp/'.$this->prefix.'_lien_'.$lien.'.txt');
		
	}


	/**
	 * 
	 * 
	 * BLOC STRUCTURE
	 * 
	 * 
	 * 
	 */

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */

	function lectureGedcom($restart = false) {
		$this->debug('lectureGedcom()');

		if (!$restart)
			$this->lireLigne();

		while (true) {
			if ($this->timeCut())
				return 'cut';
			if ($this->ligne['no'] != 0) {
				$this->warning('Tag de niveau 0 attendu', 0);
				continue;
			}
			switch ($this->ligne['tag']) {
				//HEADER
				case 'HEAD' :
					if ($this->head) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', 0);
					break;
					}
					$this->head = true;
					$this->lectureHEAD($this->ligne['no']);
					break;

					//RECORD
				case 'FAM' :
					$this->lectureFAM_RECORD(0);
					break;
				case 'INDI' :
					$this->lectureINDIVIDUAL_RECORD(0);
					break;
				case 'OBJE' :
					$this->lectureMULTIMEDIA_RECORD(0);
					break;
				case 'NOTE' :
					$this->lectureNOTE_RECORD(0);
					break;
				case 'REPO' :
					$this->lectureREPOSITORY_RECORD(0);
					break;
				case 'SOUR' :
					$this->lectureSOURCE_RECORD(0);
					break;
				case 'SUBM' :
					$this->lectureSUBMITTER_RECORD(0);
					break;

					//SUBMISSION_RECORD
				case 'SUBN' :
					if ($this->subn) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', 0);
					break;
					}
					$subn = true;
					$this->lectureSUBMISSION_RECORD(0);
					break;

				case 'TRLR' :
					if (!$this->head) {
						$this->warning('Tag HEAD attendu');
					}
					return;
				default :
					$this->warning('-------Tag '.$this->ligne['tag'].' inattendu', 0);
					break;
			}
		} // while 0
	}

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */

	function lectureHEAD($n) {
		$this->debug('lectureHEAD('.$n.')');

		$sour = false;
		$sourvers = false;
		$name = false;
		$corp = false;
		$data = false;
		$date3 = false;
		$copr3 = false;
		$dest = false;
		$date1 = false;
		$time = false;
		$subm = false;
		$subn = false;
		$file = false;
		$copr1 = false;
		$gedc = false;
		$gedcvers = false;
		$gedcform = false;
		$char = false;
		$charvers = false;
		$lang = false;
		$plac = false;
		$note = false;

		$this->lireLigne();

		while (true) {
			if ($this->ligne['no'] == $n) {
				if (!$sour || !$subm || !$gedc || !$gedcvers || !$gedcform || !$char) {
					$this->warning('Tag [SOUR|SUBM|GEDC|GEDC.VERS|GEDC.FORM|CHAR] attendu');
				}
				return;
			}
			switch ($this->ligne['tag']) {
				case 'SOUR' :
					if ($sour) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$sour = true;
					$this->warning(typeCheck('APPROVED_SYSTEM_ID', $this->ligne['desc']));
					$this->lireLigne();
					while ($this->ligne['no'] > $n +1) {
						switch ($this->ligne['tag']) {
							case 'VERS' :
								if ($sourvers) {
									$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +2);
									continue 2;
								}
								$sourvers = true;
								$this->warning(typeCheck('VERSION_NUMBER', $this->ligne['desc']));
								break;

							case 'NAME' :
								if ($name) {
									$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +2);
									continue 2;
								}
								$name = true;
								$this->warning(typeCheck('NAME_OF_PRODUCT', $this->ligne['desc']));
								break;

							case 'CORP' :
								if ($corp) {
									$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +2);
									continue 2;
								}
								$corp = true;
								$this->warning(typeCheck('NAME_OF_BUSINESS', $this->ligne['desc']));
								$this->lireLigne();
								if ($this->ligne['no'] == $n +3) {
									$this->lectureADDRESS_STRUCTURE($n +3);
								}
								continue 2; //retour sur le while

							case 'DATA' :
								if ($data) {
									$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé');
									$this->lireLigne($n +2);
									continue 2;
								}
								$data = true;
								$this->warning(typeCheck('NAME_OF_SOURCE_DATA', $this->ligne['desc']));
								$this->lireLigne();
								while ($this->ligne['no'] > $n +2) {
									switch ($this->ligne['tag']) {
										case 'DATE' :
											if ($date3) {
												$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +3);
												continue 2;
											}
											$date3 = true;
											$this->warning(typeCheck('PUBLICATION_DATE', $this->ligne['desc']));
											break;
										case 'COPR' :
											if ($copr3) {
												$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +3);
												continue 2;
											}
											$copr3 = true;
											$this->warning(typeCheck('COPYRIGHT_SOURCE_DATA', $this->ligne['desc']));
											break;
										default :
											$this->warning('*******Tag '.$this->ligne['tag'].' inattendu', $n +3);
											continue 2;
									}
									$this->lireLigne();
								} //while +3
								continue 2;
								// fin case DATA

							default :
								$this->warning('+-+-+-Tag '.$this->ligne['tag'].' inattendu', $n +2);
								continue 2;
						}
						$this->lireLigne();
					} //while +2
					continue 2;
					//fin case SOUR

				case 'DEST' :
					if ($dest) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$dest = true;
					$this->warning(typeCheck('RECEIVING_SYSTEM_NAME', $this->ligne['desc']));
					break;

				case 'DATE' :
					if ($date1) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$date1 = true;
					$this->warning(typeCheck('TRANSMISSION_DATE', $this->ligne['desc']));

					$this->lireLigne();
					if ($this->ligne['no'] == $n +2) {
						if ($this->ligne['tag'] != 'TIME') {
							$this->warning('Tag TIME attendu', $n +1);
							continue 2;
						}
						if ($time) {
							$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
							continue 2;
						}
						$time = true;
						$this->warning(typeCheck('TIME_VALUE', $this->ligne['desc']));
						$this->lireLigne($n +1);
					}
					continue 2;

				case 'SUBM' :
					if ($subm) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$subm = true;
					$this->warning(typeCheck('XREF', $this->ligne['desc']));
					$id = $this->getID_xref('SUBM', $this->ligne['desc']);
					break;

				case 'SUBN' :
					if ($subn) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$subn = true;
					$this->warning(typeCheck('XREF', $this->ligne['desc']));
					break;

				case 'FILE' :
					if ($file) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$file = true;
					$this->warning(typeCheck('FILE_NAME', $this->ligne['desc']));
					break;

				case 'COPR' :
					if ($copr1) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$copr1 = true;
					$this->warning(typeCheck('COPYRIGHT_GEDCOM_FILE', $this->ligne['desc']));
					break;

				case 'GEDC' :
					if ($gedc) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$gedc = true;

					$this->lireLigne();
					while ($this->ligne['no'] > $n +1) {
						switch ($this->ligne['tag']) {
							case 'VERS' :
								if ($gedcvers) {
									$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +2);
									continue 2;
								}
								$gedcvers = true;
								$this->warning(typeCheck('VERSION_NUMBER', $this->ligne['desc']));
								break;
							case 'FORM' :
								if ($gedcform) {
									$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +2);
									continue 2;
								}
								$gedcform = true;
								$this->warning(typeCheck('GEDCOM_FORM', $this->ligne['desc']));
								break;
							default :
								$this->warning('+*+*+*Tag '.$this->ligne['tag'].' inattendu', $n +2);
								continue 2;
						}
						$this->lireLigne();
					} //while +2
					continue 2;

				case 'CHAR' :
					if ($char) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$char = true;
					$this->warning(typeCheck('CHARACTER_SET', $this->ligne['desc']));
					
					$this->charset = $this->ligne['desc'];

					$this->lireLigne();
					if ($this->ligne['no'] == $n +2) {
						if ($this->ligne['tag'] != 'VERS') {
							$this->warning('Tag VERSION_NUMBER attendu', $n +1);
							continue 2;
						}
						if ($charvers) {
							$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
							continue 2;
						}
						$charvers = true;
						$this->warning(typeCheck('VERSION_NUMBER', $this->ligne['desc']));
						$this->lireLigne($n +1);
					}
					continue 2;

				case 'LANG' :
					if ($lang) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$lang = true;
					$this->warning(typeCheck('LANGUAGE_OF_TEXT', $this->ligne['desc']));
					break;

				case 'PLAC' :
					if ($plac) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$plac = true;

					$this->lireLigne();
					if ($this->ligne['no'] != $n +2 || $this->ligne['tag'] != 'FORM') {
						$this->warning('Tag FORM de niveau 2 attendu', $n +1);
						continue 2;
					}
					$this->warning(typeCheck('PLACE_HIERARCHY', $this->ligne['desc']));
					break;

				case 'NOTE' :
					if ($note) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$note = true;
					$this->warning(typeCheck('GEDCOM_CONTENT_DESCRIPTION', $this->ligne['desc']));

					$this->lireLigne();
					while ($this->ligne['no'] > $n +1) {
						switch ($this->ligne['tag']) {
							case 'CONT' :
								$this->warning(typeCheck('GEDCOM_CONTENT_DESCRIPTION', $this->ligne['desc']));
								break;
							case 'CONC' :
								$this->warning(typeCheck('GEDCOM_CONTENT_DESCRIPTION', $this->ligne['desc']));
								break;
							default :
								$this->warning('*-*-*-*-Tag '.$this->ligne['tag'].' inattendu', $n +2);
								continue 2;
						}
						$this->lireLigne();
					} //while +2
					continue 2;

				default :
					$this->warning('/*/*/*/*Tag '.$this->ligne['tag'].' inattendu', $n +1);
					continue 2;
			}
			$this->lireLigne();
		} // while +1
	} // function lectureHEAD

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureFAM_RECORD($n) {
		$this->debug('lectureFAM_RECORD('.$n.')');

		$husb2 = false;
		$wife2 = false;
		$husb1 = false;
		$wife1 = false;
		$nchi = false;
		$chan = false;
		$rin = false;

		if ($this->ligne['tag'] != 'FAM') {
			$this->warning('Tag FAM attendu', $n);
			return;
		}
		if ($err = typeCheck('XREF', $this->ligne['xref'])) {
			$this->erreur($err, $n);
			return;
		}
		$fam['id'] = $this->getID_xref('FAM', $this->ligne['xref']);
		$fam['husb'] = '';
		$fam['wife'] = '';
		$fam['chan'] = '';
		

		$this->lireLigne();
		while ($this->ligne['no'] > $n) {
			switch ($this->ligne['tag']) {

				//FAMILY_EVENT_STRUCTURE
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
					$husb2 = false;
					$wife2 = false;
					$id = $this->lectureFAMILY_EVENT_STRUCTURE($n +1,$fam['id']);
					//if($id>=0)
					//	$this->ajoutLien('familles_events',$fam['id'],$id);

					while ($this->ligne['no'] == $n +2) { // Non supporté
						switch ($this->ligne['tag']) {
							case 'HUSB' :
								if ($husb2) {
									$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
									continue 2;
								}
								$husb2 = true;
								$this->lireLigne();
								if ($this->ligne['no'] == $n +3) {
									if ($this->ligne['tag'] != 'AGE') {
										$this->warning('Tag AGE attendu', $n +2);
										continue 2;
									}
									$this->warning(typeCheck('AGE_AT_EVENT', $this->ligne['desc']));
									break;
								}
								$this->warning('Tag AGE attendu', $n +2);
								continue 2;

							case 'WIFE' :
								if ($wife2) {
									$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
									continue 2;
								}
								$wife2 = true;
								$this->lireLigne();
								if ($this->ligne['no'] == $n +3) {
									if ($this->ligne['tag'] != 'AGE') {
										$this->warning('Tag AGE attendu', $n +2);
										continue 2;
									}
									$this->warning(typeCheck('AGE_AT_EVENT', $this->ligne['desc']));
									break;
								}
								$this->warning('Tag AGE attendu', $n +2);
								continue 2;

							default :
								$this->warning('/-/-/-/-/-Tag '.$this->ligne['tag'].' inattendu', $n +2);
								continue 2;
						}
						$this->lireLigne();
					}
					continue 2;

				case 'HUSB' :
					if ($husb1) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$husb1 = true;
					$this->warning(typeCheck('XREF', $this->ligne['desc']));
					$fam['husb'] = $this->getID_xref('INDI', $this->ligne['desc']);
					break;

				case 'WIFE' :
					if ($wife1) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$wife1 = true;
					$this->warning(typeCheck('XREF', $this->ligne['desc']));
					$fam['wife'] = $this->getID_xref('INDI', $this->ligne['desc']);
					break;

				case 'CHIL' :
					$this->warning(typeCheck('XREF', $this->ligne['desc']));
					//Non utilisé
					break;

				case 'NCHI' :
					if ($nchi) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$nchi = true;
					$this->warning(typeCheck('COUNT_OF_CHILDREN', $this->ligne['desc']));
					//Non supporté
					break;

				case 'SUBM' :
					$this->warning(typeCheck('XREF', $this->ligne['desc']));
					//Non supporté
					break;

				case 'SLGS' :
					$this->lectureLDS_SPOUSE_SEALING($n +1);
					//Non supporté
					continue 2;

				case 'SOUR' :
					$id = $this->lectureSOURCE_CITATION($n +1);
					if($id>=0)
						$this->ajoutLien('familles_sources',$fam['id'],$id);
					continue 2;

				case 'OBJE' :
					$id = $this->lectureMULTIMEDIA_LINK($n +1);
					if($id>=0)
						$this->ajoutLien('familles_multimedia',$fam['id'],$id);
					continue 2;

				case 'NOTE' :
					$id = $this->lectureNOTE_STRUCTURE($n +1);
					if($id>=0)
						$this->ajoutLien('familles_notes',$fam['id'],$id);
					continue 2;

				case 'REFN' :
					$this->warning(typeCheck('USER_REFERENCE_NUMBER', $this->ligne['desc']));
					//Non supporté
					$this->lireLigne();
					if ($this->ligne['no'] == $n +2 && $this->ligne['tag'] == 'TYPE') {
						$this->warning(typeCheck('USER_REFERENCE_TYPE', $this->ligne['desc']));
						$this->lireLigne($n +1);
						continue 2;
					}
					$this->warning('Tag TYPE attendu', $n +2);
					continue 2;

				case 'RIN' :
					if ($rin) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$rin = true;
					$this->warning(typeCheck('AUTOMATED_RECORD_ID', $this->ligne['desc']));
					//Non supporté
					break;

				case 'CHAN' :
					if ($chan) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$chan = true;
					$result = $this->lectureCHANGE_DATE($n +1);
					$fam['chan'] = $result['date'];
					continue 2;

				default :
					$this->warning('/////////Tag '.$this->ligne['tag'].' inattendu', $n +1);
					continue 2;
			}
			$this->lireLigne();
		} //while n
		
		//$this->ajoutDonnee('FAM',$fam);
		
		if(!$this->fdFamilles)
		{
			$this->warning('Fichier de lien genea_familles non disponible');
			return;
		}
		fputs($this->fdFamilles, $fam['id']."\t".$fam['husb']."\t".$fam['wife']."\t".$fam['chan']."\n");
		$this->debug('Familles ajoutée : '.$fam['id']." - ".$fam['husb']." - ".$fam['wife']." - ".$fam['chan']);
		
	} //function lectureFAM_RECORD

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureINDIVIDUAL_RECORD($n) {
		$this->debug('lectureINDIVIDUAL_RECORD('.$n.')');

		$name = false;
		$resn = false;
		$sex = false;
		$rfn = false;
		$afn = false;
		$rin = false;
		$chan = false;

		if ($this->ligne['tag'] != 'INDI') {
			$this->warning('Tag INDI attendu', $n);
			return;
		}
		if ($err = typeCheck('XREF', $this->ligne['xref'])) {
			$this->erreur($err, $n);
			return;
		}
		$indi['indi_id'] = $this->getID_xref('INDI', $this->ligne['xref']);

		$this->lireLigne();
		while ($this->ligne['no'] > $n) {
			switch ($this->ligne['tag']) {
				case 'RESN' :
					if ($resn) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$resn = true;
					$this->warning(typeCheck('RESTRICTION_NOTICE', $this->ligne['desc']));
					$indi['resn'] = $this->ligne['desc'];
					break;

				case 'NAME' :
					if ($name) {
						$this->erreur('Un seul tag NAME supporté', $n +1);
						continue 2;
					}
					$name = true;
					$res = $this->lecturePERSONAL_NAME_STRUCTURE($n +1);
					$indi = array_merge($indi,$res);
					continue 2;

				case 'SEX' :
					if ($sex) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$sex = true;
					$this->warning(typeCheck('SEX_VALUE', $this->ligne['desc']));
					$indi['sex'] = $this->ligne['desc'];
					break;

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
					$event_id = $this->lectureINDIVIDUAL_EVENT_STRUCTURE($n +1, $indi['indi_id']);
					continue 2;

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
					$event_id = $this->lectureINDIVIDUAL_ATTRIBUTE_STRUCTURE($n +1, $indi['indi_id']);
					continue 2;

				case 'BAPL' :
				case 'CONL' :
				case 'ENDL' :
				case 'SLGC' :
					$this->lectureLDS_INDIVIDUAL_ORDINANCE($n +1);
					//Non supporté
					continue 2;

				case 'FAMC' :
					$child = $this->lectureCHILD_TO_FAMILY_LINK($n +1);
					if(empty($child['rela_type']))
						$child['rela_type'] = 'BIRTH';
					$this->ajoutLien('familles_indi',$child['id'], $indi['indi_id'], $child['rela_type'] );
					continue 2;

				case 'FAMS' :
					$this->lectureSPOUSE_TO_FAMILY_LINK($n +1);
					//Non utilisé
					continue 2;

				case 'SUBM' :
					$this->warning(typeCheck('XREF', $this->ligne['desc']));
					//Non supporté
					break;

				case 'ASSO' :
					$result = $this->lectureASSOCIATION_STRUCTURE($n +1);
					$desc = (empty($result['type'])?'':$result['type']).':'.(empty($result['rela'])?'':$result['rela']);
					$this->ajoutLien('asso_indi',$indi['indi_id'],$result['id'],$desc);
					continue 2;

				case 'ALIA' :
					$this->warning(typeCheck('XREF', $this->ligne['desc']));
					$result = $this->getID_xref('INDI', $this->ligne['desc']);
					if($result >=0)
						$this->ajoutLien('alia',$indi['indi_id'],$result);
					break;

				case 'ANCI' :
					$this->warning(typeCheck('XREF', $this->ligne['desc']));
					//Non supporté
					break;

				case 'DESI' :
					$this->warning(typeCheck('XREF', $this->ligne['desc']));
					//Non supporté
					break;

				case 'SOUR' :
					$result = $this->lectureSOURCE_CITATION($n +1);
					if($result >=0)
						$this->ajoutLien('indi_sources',$indi['indi_id'],$result);
					continue 2;

				case 'OBJE' :
					$result = $this->lectureMULTIMEDIA_LINK($n +1);
					if($result >=0)
						$this->ajoutLien('indi_multimedia',$indi['indi_id'],$result);
					continue 2;

				case 'NOTE' :
					$result = $this->lectureNOTE_STRUCTURE($n +1);
					if($result >=0)
						$this->ajoutLien('indi_notes',$indi['indi_id'],$result);
					continue 2;

				case 'RFN' :
					if ($rfn) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$rfn = true;
					$this->warning(typeCheck('PERMANENT_RECORD_FILE_NUMBER', $this->ligne['desc']));
					//Non supporté
					break;

				case 'AFN' :
					if ($afn) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$afn = true;
					$this->warning(typeCheck('ANCESTRAL_FILE_NUMBER', $this->ligne['desc']));
					//Non supporté
					break;

				case 'REFN' :
					$this->warning(typeCheck('USER_REFERENCE_NUMBER', $this->ligne['desc']));
					//Non supporté
					$this->lireLigne();
					if ($this->ligne['no'] == $n +2) {
						if ($this->ligne['tag'] == 'TYPE') {
							$this->warning(typeCheck('USER_REFERENCE_TYPE', $this->ligne['desc']));
							$this->lireLigne($n +1);
							continue 2;
						}
						$this->warning('Tag TYPE attendu', $n +1);
					}
					continue 2;

				case 'RIN' :
					if ($rin) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$rin = true;
					$this->warning(typeCheck('AUTOMATED_RECORD_ID', $this->ligne['desc']));
					//Non supporté
					break;

				case 'CHAN' :
					if ($chan) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$chan = true;
					$result = $this->lectureCHANGE_DATE($n +1);
					$indi['indi_chan'] = $result['date'];
					continue 2;

				default :
					$this->warning('/+/+/+/+/+Tag '.$this->ligne['tag'].' inattendu', $n +1);
					continue 2;
			}
			$this->lireLigne();
		} //while n+1
		$this->ajoutDonnee('INDI', $indi);
	} //function lectureINDIVIDUAL_RECORD

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureMULTIMEDIA_RECORD($n) {
		$this->debug('lectureMULTIMEDIA_RECORD('.$n.')');

		$form = false;
		$titl = false;
		$blob = false;
		$obje = false;
		$rin = false;
		$chan = false;

		if ($this->ligne['tag'] != 'OBJE') {
			$this->warning('Tag OBJE attendu', $n);
			return;
		}
		if ($err = typeCheck('XREF', $this->ligne['xref'])) {
			$this->erreur($err, $n);
			return;
		}

		$multimedia['id'] = $this->getID_xref('OBJE', $this->ligne['xref']);

		$this->lireLigne();
		while ($this->ligne['no'] > $n) {
			switch ($this->ligne['tag']) {
				case 'FORM' :
					if ($form) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$form = true;
					$this->warning(typeCheck('MULTIMEDIA_FORMAT', $this->ligne['desc']));
					$multimedia['form'] = $this->ligne['desc'];
					break;
					
				case 'TITL' :
					if ($titl) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$titl = true;
					$this->warning(typeCheck('DESCRIPTIVE_TITLE', $this->ligne['desc']));
					$multimedia['titl'] = $this->ligne['desc'];
					break;

				case 'NOTE' :
					$id = $this->lectureNOTE_STRUCTURE($n +1);
					if($id >=0)
						$this->ajoutLien('multimedia_notes',$multimedia['id'],$id);
					continue 2;
					
				case 'BLOB' : //Non supporté
					if ($blob) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$blob = true;
					while ($this->ligne['no'] == $n +2) {
						switch ($this->ligne['tag']) {
							case 'CONT' :
								$this->warning(typeCheck('ENCODED_MULTIMEDIA_LINE', $this->ligne['desc']));
								break;
								
							default :
								$this->warning('/8/8/8/Tag '.$this->ligne['tag'].' inattendu', $n +2);
								continue 2;
						}
						$this->lireLigne();
					}
					continue 2;

				case 'OBJE' : //Non supporté
					if ($obje) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$obje = true;
					$this->warning(typeCheck('XREF', $this->ligne['desc']));
					break;
					
				case 'REFN' :
					$this->warning(typeCheck('USER_REFERENCE_NUMBER', $this->ligne['desc']));
					//Non supporté

					$this->lireLigne();
					if ($this->ligne['no'] == $n +2) {
						if ($this->ligne['tag'] == 'TYPE') {
							$this->warning(typeCheck('USER_REFERENCE_TYPE', $this->ligne['desc']));
							$this->lireLigne($n +1);
							continue 2;
						}
						$this->warning('Tag TYPE attendu', $n +1);
						//Non supporté
					}
					continue 2;

				case 'RIN' :
					if ($rin) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$rin = true;
					$this->warning(typeCheck('AUTOMATED_RECORD_ID', $this->ligne['desc']));
					//Non supporté
					break;

				case 'CHAN' :
					if ($chan) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$chan = true;
					$result = $this->lectureCHANGE_DATE($n +1);
					$multimedia['notes_chan'] = $result['date'];
					continue 2;

				default :
					$this->warning('9+9+9+9+9+Tag '.$this->ligne['tag'].' inattendu', $n +1);
					continue 2;
			}
			$this->lireLigne();
		} //while n+1
		
		if (!$form)
			$this->warning('Tag FORM attendu');
		if (!$blob)
			$this->warning('Tag BLOB attendu');
			
		$this->ajoutDonnee('OBJE', $multimedia);
		
	} //function lectureMULTIMEDIA_RECORD

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureNOTE_RECORD($n) {
		$this->debug('lectureNOTE_RECORD('.$n.')');

		$rin = false;
		$chan = false;

		if ($this->ligne['tag'] != 'NOTE') {
			$this->warning('Tag NOTE attendu', $n);
			return;
		}
		if ($err = typeCheck('XREF', $this->ligne['xref'])) {
			$this->erreur($err, $n);
			return;
		}

		$note['id'] = $this->getID_xref('NOTE', $this->ligne['xref']);

		if ($err = typeCheck('SUBMITTER_TEXT', $this->ligne['desc'])) {
			$this->erreur($err, $n);
			return;
		}

		$note['notes_text'] = $this->ligne['desc'];

		$this->lireLigne();
		while ($this->ligne['no'] > $n) {
			$fin_ligne = '';
			switch ($this->ligne['tag']) {
				case 'CONT' :
					$fin_ligne = '\n';
				case 'CONC' :
					$this->warning(typeCheck('SUBMITTER_TEXT', $this->ligne['desc']));
					$note['notes_text'] .= $fin_ligne.$this->ligne['desc'];
					break;

				case 'SOUR' :
					$id = $this->lectureSOURCE_CITATION($n +1);
					//Non supporté
					continue 2;

				case 'REFN' :
					$this->warning(typeCheck('USER_REFERENCE_NUMBER', $this->ligne['desc']));
					//Non supporté

					$this->lireLigne();
					if ($this->ligne['no'] == $n +2) {
						if ($this->ligne['tag'] == 'TYPE') {
							$this->warning(typeCheck('USER_REFERENCE_TYPE', $this->ligne['desc']));
							$this->lireLigne($n +1);
							continue 2;
						}
						$this->warning('Tag TYPE attendu', $n +1);
						//Non supporté
					}
					continue 2;

				case 'RIN' :
					if ($rin) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$rin = true;
					$this->warning(typeCheck('AUTOMATED_RECORD_ID', $this->ligne['desc']));
					//Non supporté
					break;

				case 'CHAN' :
					if ($chan) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$chan = true;
					$result = $this->lectureCHANGE_DATE($n +1);
					$note['notes_chan'] = $result['date'];
					continue 2;

				default :
					$this->warning('6+6+6+6+6+Tag '.$this->ligne['tag'].' inattendu', $n +1);
					continue 2;
			}
			$this->lireLigne();
		} //while n+1
		
		$this->ajoutDonnee('NOTE', $note);
		
	} //function lectureNOTE_RECORD

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureREPOSITORY_RECORD($n) {
		$this->debug('lectureREPOSITORY_RECORD('.$n.')');

		$name = false;
		$addr = false;
		$rin = false;
		$chan = false;
		
		if ($this->ligne['tag'] != 'REPO') {
			$this->warning('Tag REPO attendu', $n);
			return;
		}
		if ($err = typeCheck('XREF', $this->ligne['xref'])) {
			$this->erreur($err, $n);
			return;
		}
		$repo['id'] = $this->getID_xref('REPO', $this->ligne['xref']);

		$this->lireLigne();
		while ($this->ligne['no'] > $n) {
			switch ($this->ligne['tag']) {
				case 'NAME' :
					if ($name) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$name = true;
					$this->warning(typeCheck('NAME_OF_REPOSITORY', $this->ligne['desc']));
					$repo['name'] = $this->ligne['desc'];
					break;

				case 'ADDR' :
					if ($addr) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$addr = true;
					$result = $this->lectureADDRESS_STRUCTURE($n +1);
					$repo = array_merge($repo,$result);
					continue 2;

				case 'NOTE' :
					$id = $this->lectureNOTE_STRUCTURE($n +1);
					if( $id >= 0)
						$this->ajoutLien('repo_notes',$repo['id'],$id);
					continue 2;

				case 'REFN' :
					$this->warning(typeCheck('USER_REFERENCE_NUMBER', $this->ligne['desc']));
					//Non supporté

					$this->lireLigne();
					if ($this->ligne['no'] == $n +2) {
						if ($this->ligne['tag'] == 'TYPE') {
							$this->warning(typeCheck('USER_REFERENCE_TYPE', $this->ligne['desc']));
							$this->lireLigne($n +1);
							continue 2;
						}
						$this->warning('Tag TYPE attendu', $n +1);
					}
					continue 2;

				case 'RIN' :
					if ($rin) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$rin = true;
					$this->warning(typeCheck('AUTOMATED_RECORD_ID', $this->ligne['desc']));
					//Non supporté

					break;

				case 'CHAN' :
					if ($chan) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$chan = true;
					$result = $this->lectureCHANGE_DATE($n +1);
					$repo['chan'] = $result['date'];
					continue 2;

				default :
					$this->warning('3+3+3+3+3+3+Tag '.$this->ligne['tag'].' inattendu', $n +1);
					continue 2;
			}
			$this->lireLigne();
		} //while n+1
		$this->ajoutDonnee('REPO', $repo);
	} //function lectureREPOSITORY_RECORD

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureSOURCE_RECORD($n) {
		$this->debug('lectureSOURCE_RECORD('.$n.')');

		$data = false;
		$auth = false;
		$titl = false;
		$abbr = false;
		$publ = false;
		$text = false;
		$repo = false;
		$rin = false;
		$chan = false;
		if ($this->ligne['tag'] != 'SOUR') {
			$this->warning('Tag SOUR attendu', $n);
			return;
		}
		if ($err = typeCheck('XREF', $this->ligne['xref'])) {
			$this->erreur($err, $n);
			return;
		}
		$sour['id'] = $this->getID_xref('SOUR', $this->ligne['xref']);

		$this->lireLigne();
		while ($this->ligne['no'] > $n) {
			switch ($this->ligne['tag']) {

				case 'DATA' :
					if ($data) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$data = true;

					$agnc = false;

					$this->lireLigne();
					while ($this->ligne['no'] == $n +2) {
						switch ($this->ligne['tag']) {
							case 'EVEN' :
								$this->warning(typeCheck('EVENT_RECORDED', $this->ligne['desc']));
								$even = array();
								$even['id'] = $this->getID_xref('EVEN');
								$date = false;
								$plac = false;

								$this->lireLigne();
								while ($this->ligne['no'] == $n +3) {
									switch ($this->ligne['tag']) {
										case 'DATE' :
											if ($date) {
												$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +3);
												continue 2;
											}
											$date = true;
											$this->warning(typeCheck('DATE_PERIOD', $this->ligne['desc']));
											$even['date_event'] = $this->ligne['desc'];
											break;
										case 'PLAC' :
											if ($plac) {
												$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +3);
												continue 2;
											}
											$plac = true;
											$this->warning(typeCheck('SOURCE_JURISDICTION_PLACE', $this->ligne['desc']));
											$even['place_id'] = $this->hashPlace[md5($this->ligne['desc'])];
											break;
										default :
											$this->warning('5+5+5+5+Tag '.$this->ligne['tag'].' inattendu', $n +3);
											continue 2;
									}
									$this->lireLigne($n +3);
								} // while n+3
//								$this->ajoutDonnee('EVEN',$even);
//								$this->ajoutLien('events_sources',$even['id'],$sour['id']);
								continue 2;

							case 'AGNC' :
								if ($agnc) {
									$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
									continue 2;
								}
								$agnc = true;
								$this->warning(typeCheck('RESPONSIBLE_AGENCY', $this->ligne['desc']));
								$sour['agnc'] = $this->ligne['desc'];
								break;

							case 'NOTE' :
								$this->lectureNOTE_STRUCTURE($n +2);
								continue 2;

							default :
								$this->warning('4+4+4+4+Tag '.$this->ligne['tag'].' inattendu', $n +2);
								//Non supporté
								continue 2;
						}
						$this->lireLigne();
					} //while n+2
					continue 2;

				case 'AUTH' :
					if ($auth) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$auth = true;
					$this->warning(typeCheck('SOURCE_ORIGINATOR', $this->ligne['desc']));
					$sour['auth'] = $this->ligne['desc'];
					$this->lireLigne();
					while ($this->ligne['no'] == $n +2) {
						$fin_ligne = "";
						switch ($this->ligne['tag']) {
							case 'CONT' :
								$fin_ligne = "\n";
							case 'CONC' :
								$this->warning(typeCheck('SOURCE_ORIGINATOR', $this->ligne['desc']));
								$sour['auth'] .= $fin_ligne.$this->ligne['desc'];
								break;
							default :
								$this->warning('2+2+2+2+2+2+Tag '.$this->ligne['tag'].' inattendu', $n +2);
								continue 2;
						}
						$this->lireLigne($n +2);
					}
					continue 2;

				case 'TITL' :
					if ($titl) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$titl = true;
					$this->warning(typeCheck('SOURCE_DESCRIPTIVE_TITLE', $this->ligne['desc']));
					$sour['titl'] = $this->ligne['desc'];
					$this->lireLigne();
					while ($this->ligne['no'] == $n +2) {
						$fin_ligne = "";
						switch ($this->ligne['tag']) {
							case 'CONT' :
								$fin_ligne = "\n";
							case 'CONC' :
								$this->warning(typeCheck('SOURCE_DESCRIPTIVE_TITLE', $this->ligne['desc']));
								$sour['titl'] .= $fin_ligne.$this->ligne['desc'];
								break;
							default :
								$this->warning('1+1+1+1+Tag '.$this->ligne['tag'].' inattendu', $n +2);
								continue 2;

						}
						$this->lireLigne($n +2);
					}
					continue 2;

				case 'ABBR' :
					if ($abbr) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$abbr = true;
					$this->warning(typeCheck('SOURCE_FILED_BY_ENTRY', $this->ligne['desc']));
					$sour['abbr'] = $this->ligne['desc'];
					break;

				case 'PUBL' :
					if ($publ) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$publ = true;
					$this->warning(typeCheck('SOURCE_PUBLICATION_FACTS', $this->ligne['desc']));
					$this->lireLigne();
					$sour['publ'] = $this->ligne['desc'];
					while ($this->ligne['no'] == $n +2) {
						$fin_ligne = "";
						switch ($this->ligne['tag']) {
							case 'CONT' :
								$fin_ligne = "\n";
							case 'CONC' :
								$this->warning(typeCheck('SOURCE_PUBLICATION_FACTS', $this->ligne['desc']));
								$sour['publ'] .= $fin_ligne.$this->ligne['desc'];
								break;
							default :
								$this->warning('0++00+0++0+Tag '.$this->ligne['tag'].' inattendu', $n +2);
								continue 2;
						}
						$this->lireLigne($n +2);
					}
					continue 2;

				case 'TEXT' :
					if ($text) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$text = true;
					$this->warning(typeCheck('TEXT_FROM_SOURCE', $this->ligne['desc']));
					$sour['text'] = $this->ligne['desc'];
					$this->lireLigne();
					while ($this->ligne['no'] == $n +2) {
						$fin_ligne = "";
						switch ($this->ligne['tag']) {
							case 'CONT' :
								$fin_ligne = "\n";
							case 'CONC' :
								$this->warning(typeCheck('TEXT_FROM_SOURCE', $this->ligne['desc']));
								$sour['text'] .= $fin_ligne.$this->ligne['desc'];
								break;
							default :
								$this->warning('++++Tag '.$this->ligne['tag'].' inattendu', $n +2);
								continue 2;
						}
						$this->lireLigne($n +2);
					}
					continue 2;

				case 'REPO' :
					if ($repo) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$repo = true;
					$result = $this->lectureSOURCE_REPOSITORY_CITATION($n +1);
					$sour = array_merge($sour, $result);
					continue 2;
				
				case 'OBJE' :
					$result = $this->lectureMULTIMEDIA_LINK($n +1);
				//Non supporté
				/*
					if($result>=0)
						$this->ajoutLien('sources_multimedia', $sour['id'], $result);
				*/
					continue 2;

				case 'NOTE' :
					$this->lectureNOTE_STRUCTURE($n +1);
					//Non supporté
					continue 2;

				case 'REFN' :
					$this->warning(typeCheck('USER_REFERENCE_NUMBER', $this->ligne['desc']));
					//Non supporté
					$this->lireLigne();
					if ($this->ligne['no'] == $n +2 && $this->ligne['tag'] == 'TYPE') {
						$this->warning(typeCheck('USER_REFERENCE_TYPE', $this->ligne['desc']));
						$this->lireLigne($n +1);
						continue 2;
					}
					$this->warning('Tag TYPE attendu', $n +2);
					continue 2;

				case 'RIN' :
					if ($rin) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$rin = true;
					$this->warning(typeCheck('AUTOMATED_RECORD_ID', $this->ligne['desc']));
					//Non supporté
					break;

				case 'CHAN' :
					if ($chan) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$chan = true;
					$result = $this->lectureCHANGE_DATE($n +1);
					$sour['chan'] = $result['date'];
					continue 2;

				default :
					$this->warning('t-t-t--t-tt-Tag '.$this->ligne['tag'].' inattendu', $n +1);
					continue 2;
			}
			$this->lireLigne();
		} //while n+1
		$this->ajoutDonnee('SOUR',$sour);
	} // function lectureSOURCE_RECORD

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureSUBMISSION_RECORD($n) {
		$this->debug('lectureSUBMISSION_RECORD('.$n.')');

		$subm = false;
		$famf = false;
		$temp = false;
		$ance = false;
		$desc = false;
		$ordi = false;
		$rin = false;

		if ($this->ligne['tag'] != 'SUBN') {
			$this->warning('Tag SUBN attendu', $n);
			return;
		}
		if ($err = typeCheck('XREF', $this->ligne['xref'])) {
			$this->erreur($err, $n);
			return;
		}
		
		$sub['id'] = $this->getID_xref('SUBN', $this->ligne['xref']);

		$this->lireLigne();
		while ($this->ligne['no'] > $n) {
			switch ($this->ligne['tag']) {
				case 'SUBM' :
					if ($subm) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$subm = true;
					$this->warning(typeCheck('XREF', $this->ligne['desc']));
					//Non supporté
					break;

				case 'FAMF' :
					if ($famf) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$famf = true;
					$this->warning(typeCheck('NAME_OF_FAMILY_FILE', $this->ligne['desc']));
					$sub['famf'] = $this->ligne['desc'];
					break;

				case 'TEMP' :
					if ($temp) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$temp = true;
					$this->warning(typeCheck('TEMPLE_CODE', $this->ligne['desc']));
					$sub['temp'] = $this->ligne['desc'];
					break;

				case 'ANCE' :
					if ($ance) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$ance = true;
					$this->warning(typeCheck('GENERATIONS_OF_ANCESTORS', $this->ligne['desc']));
					$sub['ance'] = $this->ligne['desc'];
					break;

				case 'DESC' :
					if ($desc) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$desc = true;
					$this->warning(typeCheck('GENERATIONS_OF_DESCENDANTS', $this->ligne['desc']));
					$sub['desc'] = $this->ligne['desc'];
					break;

				case 'ORDI' :
					if ($ordi) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$ordi = true;
					$this->warning(typeCheck('ORDINANCE_PROCESS_FLAG', $this->ligne['desc']));
					$sub['ordi'] = $this->ligne['desc'];
					break;

				case 'RIN' :
					if ($rin) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$rin = true;
					$this->warning(typeCheck('AUTOMATED_RECORD_ID', $this->ligne['desc']));
					$sub['rin'] = $this->ligne['desc'];
					break;

				default :
					$this->warning('r-r-r--r-r-r-Tag '.$this->ligne['tag'].' inattendu', $n +1);
					continue 2;
			}
			$this->lireLigne();
		} //while n+1
		return $sub;
	} //function lectureSUBMISSION_RECORD

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureSUBMITTER_RECORD($n) {
		$this->debug('lectureSUBMITTER_RECORD('.$n.')');

		$name = false;
		$addr = false;
		$lang = 0;
		$rfn = false;
		$rin = false;
		$chan = false;

		if ($this->ligne['tag'] != 'SUBM') {
			$this->warning('Tag SUBM attendu', $n);
			return;
		}
		if ($err = typeCheck('XREF', $this->ligne['xref'])) {
			$this->erreur($err, $n);
			return;
		}
		$sub['id'] = $this->getID_xref('SUBM', $this->ligne['xref']);

		$this->lireLigne();
		while ($this->ligne['no'] > $n) {
			switch ($this->ligne['tag']) {
				case 'NAME' :
					if ($name) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$name = true;
					$this->warning(typeCheck('SUBMITTER_NAME', $this->ligne['desc']));
					$sub['name'] = $this->ligne['desc'];
					break;

				case 'ADDR' :
					if ($addr) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$addr = true;
					$result = $this->lectureADDRESS_STRUCTURE($n +1);
					$sub = array_merge($sub, $result);
					continue 2;

				case 'OBJE' :
					$result = $this->lectureMULTIMEDIA_LINK($n +1);
					//Non supporté
					continue 2;

				case 'LANG' :
					if ($lang > 3) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé 3 fois', $n +1);
						continue 2;
					}
					$lang ++;
					$this->warning(typeCheck('LANGUAGE_PREFERENCE', $this->ligne['desc']));
					$sub['lang'] = $this->ligne['desc'];
					break;

				case 'RFN' :
					if ($rfn) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$rfn = true;
					$this->warning(typeCheck('SUBMITTER_REGISTERED_RFN', $this->ligne['desc']));
					$sub['rfn'] = $this->ligne['desc'];
					break;

				case 'RIN' :
					if ($rin) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$rin = true;
					$this->warning(typeCheck('AUTOMATED_RECORD_ID', $this->ligne['desc']));
					$sub['rin'] = $this->ligne['desc'];
					break;

				case 'CHAN' :
					if ($chan) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$chan = true;
					$result = $this->lectureCHANGE_DATE($n +1);
					$sub['chan'] = $result['date'];
					continue 2;

				default :
					$this->warning('g--g-g-g-g-g-Tag '.$this->ligne['tag'].' inattendu', $n +1);
					continue 2;
			}
			$this->lireLigne();
		} //while n+1
		if (!$name)
			$this->warning('Tag NAME attendu');
		$this->ajoutDonnee('SUBM',$sub);
	} //function lectureSUBMITTER_RECORD

	/**
	 * 
	 * 
	 * BLOC SUB-STRUCTURE
	 * 
	 * 
	 * 
	 */

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureADDRESS_STRUCTURE($n) {
		$this->debug('lectureADDRESS_STRUCTURE('.$n.')');

		$addr = false;
		$phon = 0;
		$adr1 = false;
		$adr2 = false;
		$city = false;
		$stae = false;
		$post = false;
		$ctry = false;

		$address = array();

		while ($this->ligne['no'] >= $n) {
			switch ($this->ligne['tag']) {
				case 'ADDR' :
					if ($addr) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n);
						continue 2;
					}
					$addr = true;
					$this->warning(typeCheck('ADDRESS_LINE', $this->ligne['desc']));
					$address['adress_line'] = $this->ligne['desc'];
					$this->lireLigne();
					while ($this->ligne['no'] > $n) {
						switch ($this->ligne['tag']) {
							case 'CONT' :
								$this->warning(typeCheck('ADDRESS_LINE', $this->ligne['desc']));
								$address['adress_line'] .= "\n".$this->ligne['desc'];
								break;
							case 'ADR1' :
								if ($adr1) {
									$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
									continue 2;
								}
								$adr1 = true;
								$this->warning(typeCheck('ADDRESS_LINE1', $this->ligne['desc']));
								$address['adress_line'] .= "\n".$this->ligne['desc'];
								break;
							case 'ADR2' :
								if ($adr2) {
									$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
									continue 2;
								}
								$adr2 = true;
								$this->warning(typeCheck('ADDRESS_LINE2', $this->ligne['desc']));
								$address['adress_line'] .= "\n".$this->ligne['desc'];
								break;
							case 'CITY' :
								if ($city) {
									$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
									continue 2;
								}
								$city = true;
								$this->warning(typeCheck('ADDRESS_CITY', $this->ligne['desc']));
								$address['city'] = $this->ligne['desc'];
								break;
							case 'STAE' :
								if ($stae) {
									$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
									continue 2;
								}
								$stae = true;
								$this->warning(typeCheck('ADDRESS_STATE', $this->ligne['desc']));
								$address['stae'] = $this->ligne['desc'];
								break;
							case 'POST' :
								if ($post) {
									$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
									continue 2;
								}
								$post = true;
								$this->warning(typeCheck('ADDRESS_POSTAL_CODE', $this->ligne['desc']));
								$address['post'] = $this->ligne['desc'];
								break;
							case 'CTRY' :
								if ($ctry) {
									$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
									continue 2;
								}
								$ctry = true;
								$this->warning(typeCheck('ADDRESS_COUNTRY', $this->ligne['desc']));
								$address['ctry'] = $this->ligne['desc'];
								break;
							default :
								$this->warning('n-n-n-n-n-n-Tag '.$this->ligne['tag'].' inattendu', $n +1);
								continue 2;
						}
						$this->lireLigne();
					} //while +1
					continue 2;

				case 'PHON' :
					if ($phon >= 3) {
						$this->warning('Tag '.$this->ligne['tag'].' limté à 3 occurences', $n);
						continue 2;
					}
					$phon++;
					$this->warning(typeCheck('PHONE_NUMBER', $this->ligne['desc']));
					$address['phon'.$phon] = $this->ligne['desc'];
					break;
				default :
					return $address;
			}
			$this->lireLigne();
		} //while n
		return $address;
	} // function lectureADDRESS_STRUCTURE

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureASSOCIATION_STRUCTURE($n) {
		$this->debug('lectureASSOCIATION_STRUCTURE('.$n.')');

		$type = false;
		$rela = false;

		if ($this->ligne['tag'] != 'ASSO') {
			$this->warning('Tag ASSO attendu', $n);
			return null;
		}

		$this->warning(typeCheck('XREF', $this->ligne['desc']));
		$asso['id'] = $this->getID_xref('INDI', $this->ligne['desc']);

		$this->lireLigne();
		while ($this->ligne['no'] > $n) {
			switch ($this->ligne['tag']) {
				case 'TYPE' :
					if ($type) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$type = true;
					$this->warning(typeCheck('RECORD_TYPE', $this->ligne['desc']));
					$asso['type'] = $this->ligne['desc'];
					break;

				case 'RELA' :
					if ($rela) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$rela = true;
					$this->warning(typeCheck('RELATION_IS_DESCRIPTOR', $this->ligne['desc']));
					$asso['rela'] = $this->ligne['desc'];
					break;

				case 'NOTE' :
					$this->lectureNOTE_STRUCTURE($n +1);
					//Non supporté
					continue 2;

				case 'SOUR' :
					$this->lectureSOURCE_CITATION($n +1);
					//Non supporté
					continue 2;

				default :
					$this->warning('k-k-k-k-k-k-Tag '.$this->ligne['tag'].' inattendu', $n +1);
					break;
			}
			$this->lireLigne();
		} //while 
		if (!$type || !$rela) {
			$this->warning('Tag TYPE et tag RELA attendu');
		}
		return $asso;
	} // function lectureASSOCIATION_STRUCTURE

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureCHANGE_DATE($n) {
		$this->debug('lectureCHANGE_DATE('.$n.')');

		$date = false;

		if ($this->ligne['tag'] != 'CHAN') {
			$this->warning('Tag CHAN attendu', $n);
			return;
		}

		$this->lireLigne();

		while ($this->ligne['no'] > $n) {
			switch ($this->ligne['tag']) {
				case 'DATE' :
					if ($date) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$date = true;
					$this->warning(typeCheck('CHANGE_DATE', $this->ligne['desc']));
					$chan['date'] = $this->ligne['desc'];

					$mois = array('JAN'=>'01', 'FEB'=>'02', 'MAR'=>'03', 'APR'=>'04', 'MAY'=>'05', 'JUN'=>'06', 'JUL'=>'07', 'AUG'=>'08', 'SEP'=>'09', 'OCT'=>'10', 'NOV'=>'11', 'DEC'=>'12');
 
					if(!ereg("([0-3]{0,1}[0-9]) ([a-zA-Z]{3}) ([0-9]{3,4})",$chan['date'],$reg)) {
						$this->erreur('Format de date de modification invalide : '.$chan['date'],$n);
						$chan['date'] = '';
						return $chan;
					}
					
					if(!isset($mois[strtoupper($reg[2])])) {
						$this->erreur('Format de date de modification invalide : '.$chan['date'],$n);
						$chan['date'] = '';
						return $chan;
					}
					
					$chan['date'] = ' '.$reg[3].'-'.$mois[strtoupper($reg[2])].'-'.$reg[1];
					
					$this->lireLigne();
					if ($this->ligne['no'] == $n +2) {
						if ($this->ligne['tag'] != 'TIME') {
							$this->warning('d-d--d-d--Tag '.$this->ligne['tag'].' inattendu', $n +1);
							continue 2;
						}
						$this->warning(typeCheck('TIME_VALUE', $this->ligne['desc']));
						$chan['date'] .= ' '.$this->ligne['desc'];
						$this->lireLigne($n +1);
					}
					continue 2;

				case 'NOTE' :
					$this->lectureNOTE_STRUCTURE($n +1);
					//Non supporté
					continue 2;
				default :
					$this->warning('l-l-l-l-l-l-Tag '.$this->ligne['tag'].' inattendu', $n +1);
					continue 2;
			}
			$this->lireLigne();
		} //while n
		if (!$date)
			$this->warning('Tag DATE attendu');
		return $chan;
	} // function lectureCHANGE_DATE

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureCHILD_TO_FAMILY_LINK($n) {
		$this->debug('lectureCHILD_TO_FAMILY_LINK('.$n.')');

		if ($this->ligne['tag'] != 'FAMC') {
			$this->warning('Tag FAMC attendu', $n);
			return;
		}

		$this->warning(typeCheck('XREF', $this->ligne['desc']));
		$child['id'] = $this->getID_xref('FAM', $this->ligne['desc']);
		$child['rela_type'] = '';

		$this->lireLigne();
		while ($this->ligne['no'] > $n) {
			switch ($this->ligne['tag']) {
				case 'PEDI' :
					$this->warning(typeCheck('PEDIGREE_LINKAGE_TYPE', $this->ligne['desc']));
					$child['rela_type'] = $this->ligne['desc'];
					break;

				case 'NOTE' :
					$this->lectureNOTE_STRUCTURE($n +1);
					//Non supporté
					continue 2;

				default :
					$this->warning('o-o-o-o-o-Tag '.$this->ligne['tag'].' inattendu', $n +1);
					break;
			}
			$this->lireLigne();
		} //while 

		return $child;
		
	} // function lectureCHILD_TO_FAMILY_LINK

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureEVENT_DETAIL($n, $event_id) {
		$this->debug('lectureEVENT_DETAIL('.$n.')');

		$detail = array();
	
		$type = false;
		$date = false;
		$plac = false;
		$addr = false;
		$age = false;
		$agnc = false;
		$caus = false;

		while ($this->ligne['no'] == $n) {
			switch ($this->ligne['tag']) {
				case 'TYPE' :
					if ($type) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$type = true;
					$this->warning(typeCheck('EVENT_DESCRIPTOR', $this->ligne['desc']));
					if($this->ligne['desc']!='')
						$detail['type'] = $this->ligne['desc'];
					break;
				case 'DATE' :
					if ($date) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$date = true;
					$this->warning(typeCheck('DATE_VALUE', $this->ligne['desc']));
					$detail['date_event'] = $this->ligne['desc'];
					break;
				case 'PLAC' :
					if ($plac) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$plac = true;
					$id = $this->lecturePLACE_STRUCTURE($n);
					$detail['place_id'] = $id;
					continue 2;
				case 'ADDR' :
					if ($addr) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$addr = true;
					$res = $this->lectureADDRESS_STRUCTURE($n);
					$detail = array_merge($detail, $res);
					continue 2;
				case 'AGE' :
					if ($age) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$age = true;
					$this->warning(typeCheck('AGE_AT_EVENT', $this->ligne['desc']));
					$detail['age'] = $this->ligne['desc'];
					break;
				case 'AGNC' :
					if ($agnc) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$agnc = true;
					$this->warning(typeCheck('RESPONSIBLE_AGENCY', $this->ligne['desc']));
					$detail['agnc'] = $this->ligne['desc'];
					break;
				case 'CAUS' :
					if ($caus) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$caus = true;
					$this->warning(typeCheck('CAUSE_OF_EVENT', $this->ligne['desc']));
					$detail['cause'] = $this->ligne['desc'];
					break;
				case 'SOUR' :
					$id = $this->lectureSOURCE_CITATION($n);
					//supporté en partie
					/*
					 * Récupère seulement le lien de la source et pas les infos supplémentaire
					 * données dans Source citation
					 * 1 solution : crée une nvlle table qui complète la table de lien rel_events_sources
					 */
					if ( $id >= 0 )
						$this->ajoutLien('events_sources', $event_id, $id);
					continue 2;
				case 'OBJE' :
					$id = $this->lectureMULTIMEDIA_LINK($n);
					if($id>=0)
						$this->ajoutLien('events_multimedia',$event_id,$id);
					continue 2;
				case 'NOTE' :
					$id = $this->lectureNOTE_STRUCTURE($n);
					$this->ajoutLien('events_notes', $event_id, $id);
					continue 2;
				default :
					$this->warning('p-p-p-p-p-Tag '.$this->ligne['tag'].' inattendu', $n );
					continue 2;
			}
			$this->lireLigne();
		} //while +1
		return $detail;
	} // function lectureEVENT_DETAIL

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureFAMILY_EVENT_STRUCTURE($n,$fam_id) {
		$this->debug('lectureFAMILY_EVENT_STRUCTURE('.$n.')');

		$event['id'] = $this->getID_xref('EVEN');
		$event_tag = $this->ligne['tag'];

		if($this->ligne['desc']=="Y")
			$event_attestation = "Y";
		else
			$event_attestation = "";
		$this->lireLigne();
		if ($this->ligne['no'] == $n +1) {
			$detail = $this->lectureEVENT_DETAIL($n +1, $event['id']);
			$event = array_merge($event, $detail);
		}

		$this->ajoutDonnee('EVEN',$event);
		$this->ajoutLien('familles_events', $fam_id, $event['id'], $event_tag, $event_attestation);
		
		return $event['id'];
	} // function lectureFAMILY_EVENT_STRUCTURE

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureINDIVIDUAL_EVENT_STRUCTURE($n, $indi_id) {
		$this->debug('lectureINDIVIDUAL_EVENT_STRUCTURE('.$n.')');

		$famc = false;
		
		$event['id'] = $this->getID_xref('EVEN');
		$event_tag = $this->ligne['tag'];
		if($this->ligne['desc']=="Y")
			$event_attestation = "Y";
		else
			$event_attestation = "";
		switch ($this->ligne['tag']) {
			case 'BIRT' :
			case 'CHR' :
				$this->lireLigne();
				while ($this->ligne['no'] == $n +1) {
					switch ($this->ligne['tag']) {
						case 'TYPE' :
						case 'DATE' :
						case 'PLAC' :
						case 'ADDR' :
						case 'AGE' :
						case 'AGNC' :
						case 'CAUS' :
						case 'SOUR' :
						case 'OBJE' :
						case 'NOTE' :
							$detail = $this->lectureEVENT_DETAIL($n +1, $event['id']);
							$event = array_merge($event, $detail);
							continue 2;
						case 'FAMC' :
							if ($famc) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$famc = true;
							$this->warning(typeCheck('XREF', $this->ligne['desc']));
							$id = $this->getID_xref('FAM', $this->ligne['desc']);
							//Non utilisé
							break;
						default :
							$this->warning('m-m-m-m-m-m-Tag '.$this->ligne['tag'].' inattendu', $n +1);
							break;
					}
					$this->lireLigne();
				} //while +2
				break;

			case 'ADOP' :
				$this->lireLigne();
				while ($this->ligne['no'] == $n +1) {
					switch ($this->ligne['tag']) {
						case 'TYPE' :
						case 'DATE' :
						case 'PLAC' :
						case 'ADDR' :
						case 'AGE' :
						case 'AGNC' :
						case 'CAUS' :
						case 'SOUR' :
						case 'OBJE' :
						case 'NOTE' :
							$detail = $this->lectureEVENT_DETAIL($n +1, $event['id']);
							$event = array_merge($event, $detail);
							continue 2;
						case 'FAMC' :
							if ($famc) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$famc = true;
							$this->warning(typeCheck('XREF', $this->ligne['desc']));
							$id = $this->getID_xref('FAM', $this->ligne['desc']);							
							$this->lireLigne();
							if ($this->ligne['no'] == $n +1) {
								if ($this->ligne['tag'] != 'ADOP') {
									$this->warning('Tag ADOP attendu', $n +1);
									continue 2;
								}
								$this->warning(typeCheck('ADOPTED_BY_WHICH_PARENT', $this->ligne['desc']));
								//Non supporté
								$this->lireLigne($n +1);
								continue 2;
							}
							$this->ajoutLien('familles_indi',$id, $indi_id, 'adopted');
							break;
						default :
							$this->warning('s-s-s-s-s-Tag '.$this->ligne['tag'].' inattendu', $n +1);
							break;
					}
					$this->lireLigne();
				}
				break;

			case 'DEAT' :
			case 'BURI' :
			case 'CREM' :
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
				$this->lireLigne();
				if ($this->ligne['no'] == $n +1) {
					$detail = $this->lectureEVENT_DETAIL($n +1, $event['id']);
					$event = array_merge($event, $detail);
				}
				break;

			default :
				$this->warning('z-z-z-z-z-Tag '.$this->ligne['tag'].' inattendu', $n +1);
				return -1;
		}

		$this->ajoutDonnee('EVEN',$event);
		$this->ajoutLien('indi_events', $indi_id, $event['id'], $event_tag, $event_attestation);

		return $event['id'];
	} // function lectureINDIVIDUAL_EVENT_STRUCTURE

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureINDIVIDUAL_ATTRIBUTE_STRUCTURE($n, $indi_id) {
		$this->debug('lectureINDIVIDUAL_ATTRIBUTE_STRUCTURE('.$n.')');

		switch ($this->ligne['tag']) {
			case 'CAST' :
				$this->warning(typeCheck('CASTE_NAME', $this->ligne['desc']));
				break;
			case 'DSCR' :
				$this->warning(typeCheck('PHYSICAL_DESCRIPTION', $this->ligne['desc']));
				break;
			case 'EDUC' :
				$this->warning(typeCheck('SCHOLASTIC_ACHIEVEMENT', $this->ligne['desc']));
				break;
			case 'IDNO' :
				$this->warning(typeCheck('NATIONAL_ID_NUMBER', $this->ligne['desc']));
				break;
			case 'NATI' :
				$this->warning(typeCheck('NATIONAL_OR_TRIBAL_ORIGIN', $this->ligne['desc']));
				break;
			case 'NCHI' :
				$this->warning(typeCheck('COUNT_OF_CHILDREN', $this->ligne['desc']));
				break;
			case 'NMR' :
				$this->warning(typeCheck('COUNT_OF_MARRIAGES', $this->ligne['desc']));
				break;
			case 'OCCU' :
				$this->warning(typeCheck('OCCUPATION', $this->ligne['desc']));
				break;
			case 'PROP' :
				$this->warning(typeCheck('POSSESSIONS', $this->ligne['desc']));
				break;
			case 'RELI' :
				$this->warning(typeCheck('RELIGIOUS_AFFILIATION', $this->ligne['desc']));
				break;
			case 'RESI' :
				break;
			case 'SSN' :
				$this->warning(typeCheck('SOCIAL_SECURITY_NUMBER', $this->ligne['desc']));
				break;
			case 'TITL' :
				$this->warning(typeCheck('NOBILITY_TYPE_TITLE', $this->ligne['desc']));
				break;

			default :
				$this->warning('q-q-q-q-q-Tag '.$this->ligne['tag'].' inattendu', $n);
				return -1;
		}
		$event['id'] = $this->getID_xref('EVEN');
		$event_tag = $this->ligne['tag'];
		$event_description = $this->ligne['desc'];
		
		$this->lireLigne();
		if ($this->ligne['no'] == $n +1) {
			$result = $this->lectureEVENT_DETAIL($n +1, $event['id']);
			$event = array_merge($event, $result);
		}
		/*else
			$this->lireLigne();
		*/
		$this->ajoutDonnee('EVEN', $event);
		$this->ajoutLien('indi_attributes', $indi_id, $event['id'], $event_tag, $event_description);
		
		return $event['id'];
	} // function lectureINDIVIDUAL_ATTRIBUTE_STRUCTURE

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureLDS_INDIVIDUAL_ORDINANCE($n) {
		$this->debug('lectureLDS_INDIVIDUAL_ORDINANCE('.$n.')');
		//Non supporté
		
		$stat = false;
		$date = false;
		$temp = false;
		$plac = false;
		$famc = false;

		switch ($this->ligne['tag']) {
			case 'BAPL' :
			case 'CONL' :
				$this->lireLigne();
				while ($this->ligne['no'] > $n) {
					switch ($this->ligne['tag']) {
						case 'STAT' :
							if ($stat) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$stat = true;
							$this->warning(typeCheck('LDS_BAPTISM_DATE_STATUS', $this->ligne['desc']));
							break;
						case 'DATE' :
							if ($date) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$date = true;
							$this->warning(typeCheck('DATE_LDS_ORD', $this->ligne['desc']));
							break;
						case 'TEMP' :
							if ($temp) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$temp = true;
							$this->warning(typeCheck('TEMPLE_CODE', $this->ligne['desc']));
							break;
						case 'PLAC' :
							if ($plac) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$plac = true;
							$this->warning(typeCheck('PLACE_LIVING_ORDINANCE', $this->ligne['desc']));
							break;
						case 'SOUR' :
							$this->lectureSOURCE_CITATION($n +1);
							continue 2;
						case 'NOTE' :
							$this->lectureNOTE_STRUCTURE($n +1);
							continue 2;
						default :
							$this->warning('d-d-d-Tag '.$this->ligne['tag'].' inattendu', $n +1);
							continue 2;
					}
					$this->lireLigne();
				} //while +1
				break;
			case 'ENDL' :
				$this->lireLigne();
				while ($this->ligne['no'] > $n) {
					switch ($this->ligne['tag']) {
						case 'STAT' :
							if ($stat) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$stat = true;
							$this->warning(typeCheck('LDS_ENDOWMENT_DATE_STATUS', $this->ligne['desc']));
							break;
						case 'DATE' :
							if ($date) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$date = true;
							$this->warning(typeCheck('DATE_LDS_ORD', $this->ligne['desc']));
							break;
						case 'TEMP' :
							if ($temp) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$temp = true;
							$this->warning(typeCheck('TEMPLE_CODE', $this->ligne['desc']));
							break;
						case 'PLAC' :
							if ($plac) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$plac = true;
							$this->warning(typeCheck('PLACE_LIVING_ORDINANCE', $this->ligne['desc']));
							break;
						case 'SOUR' :
							$this->lectureSOURCE_CITATION($n +1);
							continue 2;
						case 'NOTE' :
							$this->lectureNOTE_STRUCTURE($n +1);
							continue 2;
						default :
							$this->warning('e-e-e-Tag '.$this->ligne['tag'].' inattendu', $n +1);
							continue 2;
					}
					$this->lireLigne();
				} //while +1
				break;

			case 'SLGC' :
				$this->lireLigne();
				while ($this->ligne['no'] > $n) {
					switch ($this->ligne['tag']) {
						case 'STAT' :
							if ($stat) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$stat = true;
							$this->warning(typeCheck('LDS_ENDOWMENT_DATE_STATUS', $this->ligne['desc']));
							break;
						case 'DATE' :
							if ($date) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$date = true;
							$this->warning(typeCheck('DATE_LDS_ORD', $this->ligne['desc']));
							break;
						case 'TEMP' :
							if ($temp) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$temp = true;
							$this->warning(typeCheck('TEMPLE_CODE', $this->ligne['desc']));
							break;
						case 'PLAC' :
							if ($plac) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$plac = true;
							$this->warning(typeCheck('PLACE_LIVING_ORDINANCE', $this->ligne['desc']));
							break;
						case 'FAMC' :
							if ($famc) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$famc = true;
							$this->warning(typeCheck('XREF', $this->ligne['desc']));
							$id = $this->getID_xref('FAM', $this->ligne['desc']);
							break;
						case 'SOUR' :
							$this->lectureSOURCE_CITATION($n +1);
							continue 2;
						case 'NOTE' :
							$this->lectureNOTE_STRUCTURE($n +1);
							continue 2;
						default :
							$this->warning('g-g-g-Tag '.$this->ligne['tag'].' inattendu', $n +1);
							continue 2;
					}
					$this->lireLigne();
				} //while +1
				break;

			default :
				$this->warning('Tag [BAPL|CONL|ENDL|SLGC] attendu', $n);
				break;
		}
	} // function lectureLDS_INDIVIDUAL_ORDINANCE

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureLDS_SPOUSE_SEALING($n) {
		$this->debug('lectureLDS_SPOUSE_SEALING('.$n.')');
		//Non supporté
		
		$stat = false;
		$date = false;
		$temp = false;
		$plac = false;

		if ($this->ligne['tag'] != 'SLGS') {
			$this->warning('Tag SLGS attendu', $n);
			return;
		}

		$this->lireLigne();
		while ($this->ligne['no'] > $n) {
			switch ($this->ligne['tag']) {
				case 'STAT' :
					if ($stat) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$stat = true;
					$this->warning(typeCheck('LDS_SPOUSE_SEALING_DATE_STATUS', $this->ligne['desc']));
					break;
				case 'DATE' :
					if ($date) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$date = true;
					$this->warning(typeCheck('DATE_LDS_ORD', $this->ligne['desc']));
					break;
				case 'TEMP' :
					if ($temp) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$temp = true;
					$this->warning(typeCheck('TEMPLE_CODE', $this->ligne['desc']));
					break;
				case 'PLAC' :
					if ($plac) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$plac = true;
					$this->warning(typeCheck('PLACE_LIVING_ORDINANCE', $this->ligne['desc']));
					break;
				case 'SOUR' :
					$this->lectureSOURCE_CITATION($n +1);
					continue 2;
				case 'NOTE' :
					$this->lectureNOTE_STRUCTURE($n +1);
					continue 2;
				default :
					$this->warning('h-h-h-h-Tag '.$this->ligne['tag'].' inattendu', $n +1);
					continue 2;
			}
			$this->lireLigne();
		} //while +1
	} // function lectureLDS_SPOUSE_SEALING

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureMULTIMEDIA_LINK($n) {
		$this->debug('lectureMULTIMEDIA_LINK('.$n.')');
		
		$form = false;
		$titl = false;
		$file = false;
		
		if (!typeCheck('XREF', $this->ligne['desc'])) {
			$id = $this->getID_xref('OBJE', $this->ligne['desc']);
			$this->lireLigne($n);
			
			return $id;
		} //OBJE XREF
		else {
			if ($this->ligne['desc'] == '') {
				$multimedia['id'] = $this->getID_xref('OBJE');
					
				$this->lireLigne();
				while ($this->ligne['no'] > $n) {
					switch ($this->ligne['tag']) {
						case 'FORM' :
							if ($form) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$form = true;
							$this->warning(typeCheck('MULTIMEDIA_FORMAT', $this->ligne['desc']));
							$multimedia['form'] = $this->ligne['desc'];
							break;
							
						case 'TITL' :
							if ($titl) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$titl = true;
							$this->warning(typeCheck('DESCRIPTIVE_TITLE', $this->ligne['desc']));
							$multimedia['titl'] = $this->ligne['desc'];
							break;
							
						case 'FILE' :
							if ($file) {
								$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
								continue 2;
							}
							$file = true;
							$this->warning(typeCheck('MULTIMEDIA_FILE_REFERENCE', $this->ligne['desc']));
							$multimedia['file'] = $this->ligne['desc'];
							break;
		
						case 'NOTE' :
							$id = $this->lectureNOTE_STRUCTURE($n +1);
							if($id >=0)
								$this->ajoutLien('multimedia_notes',$multimedia['id'],$id);
							continue 2;
							
						default :
							$this->warning('t-t-t-t-t-Tag '.$this->ligne['tag'].' inattendu', $n +1);
							continue 2;
					}
					$this->lireLigne();
				} //while n+1
				
				if (!$form)
					$this->warning('Tag FORM attendu');
					
				$this->ajoutDonnee('OBJE', $multimedia);

				return $multimedia['id'];
			} // OBJE
			else {
				$this->warning('Tag OBJE : format inattendu', $n);
				return -1;
			}
		}
		
	} // function lectureMULTIMEDIA_LINK

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureNOTE_STRUCTURE($n) {
		$this->debug('lectureNOTE_STRUCTURE('.$n.')');

		if (!typeCheck('XREF', $this->ligne['desc'])) {
			$id = $this->getID_xref('NOTE', $this->ligne['desc']);
			$this->lireLigne();
			while ($this->ligne['no'] > $n) {
				if ($this->ligne['no'] != $n +1) {
					$this->warning('Tag de niveau '. ($n +1).' attendu', $n +1);
					continue;
				}

				if ($this->ligne['tag'] != 'SOUR') {
					$this->warning('Tag SOUR attendu', $n +1);
					continue;
				}
				$this->lectureSOURCE_CITATION($n +1);
				//Non supporté
			} //while n+1
			
			return $id;
		} //NOTE XREF
		else {
			if (!typeCheck('SUBMITTER_TEXT', $this->ligne['desc']) || $this->ligne['desc'] == '') {
				
				$note['id'] = $this->getID_xref('NOTE');
				
				$note['notes_text'] = $this->ligne['desc'];
					
				$this->lireLigne();
				while ($this->ligne['no'] > $n) {
					$fin_ligne = '';
					if ($this->ligne['no'] > $n +1) {
						$this->warning('Tag de niveau '. ($n +1).' attendu', $n +1);
						continue;
					}
					switch ($this->ligne['tag']) {
						case 'CONT' :
							$fin_ligne = '\n';
						case 'CONC' :
							$this->warning(typeCheck('SUBMITTER_TEXT', $this->ligne['desc']));
							$note['notes_text'] .= $fin_ligne.$this->ligne['desc'];
							break;

						case 'SOUR' :
							$this->lectureSOURCE_CITATION($n +1);
							//Non supporté
							continue 2;
						default :
							$this->warning('y-y-y-y-y-y-Tag '.$this->ligne['tag'].' inattendu', $n +1);
							continue 2;
					}
					$this->lireLigne();
				} //while n+1

				$this->ajoutDonnee('NOTE',$note);

				return $note['id'];
			} // NOTE SUBMITTER_TEXT
			else {
				$this->warning('Tag NOTE : format inattendu', $n);
				return -1;
			}
		}

	} // function lectureNOTE_STRUCTURE

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lecturePERSONAL_NAME_STRUCTURE($n) {
		$this->debug('lecturePERSONAL_NAME_STRUCTURE('.$n.')');

		$npfx = false;
		$givn = false;
		$nick = false;
		$spfx = false;
		$surn = false;
		$nsfx = false;

		if ($this->ligne['tag'] != 'NAME') {
			$this->warning('Tag NAME attendu', $n);
			return;
		}

		$this->warning(typeCheck('NAME_PERSONAL', $this->ligne['desc']));

		$name = array();
		
		if(strpos($this->ligne['desc'],'/')===FALSE)
			$name['indi_nom'] = $this->ligne['desc'];
		else
		if(ereg("^/([^/]*)/$", $this->ligne['desc'], $reg) )
		{
			$name['indi_nom'] = '?';
			$name['indi_prenom'] = trim($reg[1]);
		}else
		if(ereg("^([^/]*)/([^/]*)/$", $this->ligne['desc'], $reg) )
		{
			$name['indi_nom'] = trim($reg[2]);
			$name['indi_prenom'] = trim($reg[1]);
		}else
		if(ereg("^/([^/]*)/([^/]*)$", $this->ligne['desc'], $reg) )
		{
			$name['indi_nom'] = trim($reg[1]);
			$name['indi_prenom'] = trim($reg[2]);
		}else
		if(ereg("^([^/]*)/([^/]*)/(.*)$", $this->ligne['desc'], $reg) )
		{
			$name['indi_nom'] = trim($reg[2]);
			$name['indi_prenom'] = trim($reg[1]);
			$name['nsfx'] = trim($reg[3]);
		}else
		{
			$this->warning('Format de nom invalide : '.$this->ligne['desc']);
			$name['indi_nom'] = $this->ligne['desc'];
		}
				
		$this->lireLigne();
		while ($this->ligne['no'] > $n) {
			switch ($this->ligne['tag']) {
				case 'NPFX' :
					if ($npfx) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$npfx = true;
					$this->warning(typeCheck('NAME_PIECE_PREFIX', $this->ligne['desc']));
					$name['npfx'] = $this->ligne['desc'];
					break;
				case 'GIVN' :
					if ($givn) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$givn = true;
					$this->warning(typeCheck('NAME_PIECE_GIVEN', $this->ligne['desc']));
					$name['givn'] = $this->ligne['desc'];
					break;
				case 'NICK' :
					if ($nick) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$nick = true;
					$this->warning(typeCheck('NAME_PIECE_NICKNAME', $this->ligne['desc']));
					$name['nick'] = $this->ligne['desc'];
					break;
				case 'SPFX' :
					if ($spfx) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$spfx = true;
					$this->warning(typeCheck('NAME_PIECE_SURNAME_PREFIX', $this->ligne['desc']));
					$name['spfx'] = $this->ligne['desc'];
					break;
				case 'SURN' :
					if ($surn) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$surn = true;
					$this->warning(typeCheck('NAME_PIECE_SURNAME', $this->ligne['desc']));
					$name['surn'] = $this->ligne['desc'];
					break;
				case 'NSFX' :
					if ($nsfx) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$nsfx = true;
					$this->warning(typeCheck('NAME_PIECE_SUFFIX', $this->ligne['desc']));
					$name['nsfx'] = $this->ligne['desc'];
					break;
				case 'SOUR' :
					$this->lectureSOURCE_CITATION($n +1);
					//Non supporté
					continue 2;
				case 'NOTE' :
					$this->lectureNOTE_STRUCTURE($n +1);
					//Non supporté
					continue 2;
			}
			$this->lireLigne();
		} //while +1
		return $name;
	} // function lecturePERSONAL_NAME_STRUCTURE

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lecturePLACE_STRUCTURE($n) {
		$this->debug('lecturePLACE_STRUCTURE('.$n.')');

		$form = false;

		if ($this->ligne['tag'] != 'PLAC') {
			$this->warning('Tag PLAC attendu', $n);
			return;
		}


		$this->warning(typeCheck('PLACE_VALUE', $this->ligne['desc']));
		
		$place['id'] = $this->hashPlace[ md5($this->ligne['desc']) ];
			
		$this->lireLigne();
		while ($this->ligne['no'] > $n) {
			switch ($this->ligne['tag']) {
				case 'FORM' :
					if ($form) {
						$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
						continue 2;
					}
					$form = true;
					$this->warning(typeCheck('PLACE_HIERARCHY', $this->ligne['desc']));
					break;
				case 'SOUR' :
					$this->lectureSOURCE_CITATION($n +1);
					continue 2;
				case 'NOTE' :
						$this->lectureNOTE_STRUCTURE($n +1);
					continue 2;

				default :
					$this->warning('u-u-u-u-u-u-u-u-Tag '.$this->ligne['tag'].' inattendu', $n +1);
					continue 2;
			}
			$this->lireLigne();
		} //while +1

		//$this->ajoutDonnee('PLAC',$place);
	
		return $place['id'];
	} // function lecturePLACE_STRUCTURERE

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureSOURCE_CITATION($n) {
		$this->debug('lectureSOURCE_CITATION('.$n.')');

		$page = false;
		$even = false;
		$data = false;
		$date = false;

		if (!typeCheck('XREF', $this->ligne['desc'])) {
			$source['sour_citations_id'] = $this->getID_xref('SOUR_CIT', false);
			$source['sour_records_id'] = $this->getID_xref('SOUR', $this->ligne['desc']);
			$this->lireLigne();

			while ($this->ligne['no'] > $n) {
				switch ($this->ligne['tag']) {
					case 'PAGE' :
						if ($page) {
							$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
							continue 2;
						}
						$page = true;
						$this->warning(typeCheck('WHERE_WITHIN_SOURCE', $this->ligne['desc']));
						$source['page'] = $this->ligne['desc'];
						break;

					case 'EVEN' :
						if ($even) {
							$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
							continue 2;
						}
						$even = true;
						$this->warning(typeCheck('EVENT_TYPE_CITED_FROM', $this->ligne['desc']));
						$source['even'] = $this->ligne['desc'];
						$this->lireLigne();
						if ($this->ligne['no'] == $n +2) {
							if ($this->ligne['tag'] != 'ROLE') {
								$this->warning('Tag ROLE attendu', $n +1);
								continue 2;
							}
							$this->warning(typeCheck('ROLE_IN_EVENT', $this->ligne['desc']));
							$source['role'] = $this->ligne['desc'];
							$this->lireLigne($n +1);
						}
						continue 2;

					case 'DATA' :
						if ($data) {
							$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
							continue 2;
						}
						$data = true;
						$this->lireLigne();
						while ($this->ligne['no'] > $n +1) {
							switch ($this->ligne['tag']) {
								case 'DATE' :
									if ($date) {
										$this->warning('Tag '.$this->ligne['tag'].' déjà utilisé', $n +1);
										continue 2;
									}
									$date = true;
									$this->warning(typeCheck('ENTRY_RECORDING_DATE', $this->ligne['desc']));
									$source['data_date'] = $this->ligne['desc'];
									break;

								case 'TEXT' :
									$this->warning(typeCheck('TEXT_FROM_SOURCE', $this->ligne['desc']));
									$source['data_text'] = $this->ligne['desc'];
									$this->lireLigne();
									while ($this->ligne['no'] > $n +2) {
										$fin_ligne = '';
										if ($this->ligne['no'] > $n +3) {
											$this->warning('Tag de niveau '. ($n +2).' attendu', $n +2);
											continue;
										}
										switch ($this->ligne['tag']) {
											case 'CONC' :
												$fin_ligne = '\n';
											case 'CONT' :
												$this->warning(typeCheck('TEXT_FROM_SOURCE', $this->ligne['desc']));
												$source['data_text'] .= $fin_ligne.$this->ligne['desc'];
												break;
										}
										$this->lireLigne();
									} //while n+2
									continue 2;
								default :
									$this->warning('i-i-i-i-i-Tag '.$this->ligne['tag'].' inattendu', $n +2);
									continue 2;
							}
							$this->lireLigne();
						} //while n+1
						continue 2;

					case 'QUAY' :
						$this->warning(typeCheck('CERTAINTY_ASSESSMENT', $this->ligne['desc']));
						$source['quay'] = $this->ligne['desc'];
						break;

					case 'OBJE' :
						$id = $this->lectureMULTIMEDIA_LINK($n +1);
						if($id>=0)
							$this->ajoutLien('sour_citations_multimedia',$source['sour_citations_id'],$id);
						
						continue 2;

					case 'NOTE' :
						$id = $this->lectureNOTE_STRUCTURE($n +1);
						if($id>=0)
							$this->ajoutLien('sour_citations_notes',$source['sour_citations_id'],$id);
						continue 2;

					default :
						$this->warning('k-k-k-k-Tag '.$this->ligne['tag'].' inattendu', $n +1);
						continue 2;
				}
				$this->lireLigne();
			} //while n+1
			$this->ajoutDonnee('SOUR_CIT',$source);
			return $source['sour_citations_id'];
		} //SOUR XREF

		else {
			if (!typeCheck('SOURCE_DESCRIPTION', $this->ligne['desc'])) {
				$text = 0;
				
				$source['id'] = $this->getID_xref('SOUR');
				$source['text'] = '';
				$source['publ'] = '';
				$this->lireLigne();
				while ($this->ligne['no'] > $n) {
					$fin_ligne = '';
					switch ($this->ligne['tag']) {
						case 'CONT' :
							$fin_ligne = '\n';
						case 'CONC' :
							$this->warning(typeCheck('SOURCE_DESCRIPTION', $this->ligne['desc']));
							$source['publ'] .= $fin_ligne.$this->ligne['desc'];
							break;

						case 'TEXT' :
							$text++;
							$this->warning(typeCheck('TEXT_FROM_SOURCE', $this->ligne['desc']));
							if($text>1)
								$source['text'] .= '\n\n';
							$source['text'] .= $this->ligne['desc'];
							$this->lireLigne();
							while ($this->ligne['no'] > $n +2) {
								$fin_ligne = '';
								if ($this->ligne['no'] > $n +3) {
									$this->warning('Tag de niveau '. ($n +2).' attendu', $n +2);
									continue;
								}
								switch ($this->ligne['tag']) {
									case 'CONT' :
										$fin_ligne = '\n';
									case 'CONC' :
										$this->warning(typeCheck('TEXT_FROM_SOURCE', $this->ligne['desc']));
										$source['text'] .= $fin_ligne.$this->ligne['desc'];
										break;
									default :
										$this->warning('b-b-b-b-Tag '.$this->ligne['tag'].' inattendu', $n +2);
										continue 2;
								}
								$this->lireLigne();
							} //while n+2
							continue 2;

						case 'NOTE' :
							$this->lectureNOTE_STRUCTURE($n +1);
							//Non supporté
							continue 2;

						default :
							$this->warning(',-,-,-,-Tag '.$this->ligne['tag'].' inattendu', $n +1);
							continue 2;
					}
					$this->lireLigne();
				} //while n+1
				$this->ajoutDonnee('SOUR',$source);
			} // SOUR SOURCE_DESCRIPTION
			else {
				$this->warning('Tag SOUR : format inattendu', $n);
				return -1;
			}
		}
		return $source['id'];
	} // function lectureSOURCE_CITATION

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureSPOUSE_TO_FAMILY_LINK($n) {
		$this->debug('lectureSPOUSE_TO_FAMILY_LINK('.$n.')');

		if ($this->ligne['tag'] != 'FAMS') {
			$this->warning('Tag FAMS attendu', $n);
			return;
		}

		$this->warning(typeCheck('XREF', $this->ligne['desc']));
		$id = $this->getID_xref('FAM', $this->ligne['desc']);
		//Non utilisé
		
		$this->lireLigne();
		while ($this->ligne['no'] > $n) {
			switch ($this->ligne['tag']) {
				case 'NOTE' :
					$this->lectureNOTE_STRUCTURE($n +1);
					//Non supporté
					continue 2;

				default :
					$this->warning(';-;-;-;-Tag '.$this->ligne['tag'].' inattendu', $n +1);
					break;
			}
		} //while 

	} // function lectureSPOUSE_TO_FAMILY_LINK

	/**
	 * 
	 * Fonction d'analyse
	 * 
	 */
	function lectureSOURCE_REPOSITORY_CITATION($n) {
		$this->debug('lectureSOURCE_REPOSITORY_CITATION('.$n.')');
		/*
		 * Complément de données d'un repository
		 */

		if ($this->ligne['tag'] != 'REPO') {
			$this->warning('Tag REPO attendu', $n);
			return null;
		}
		$this->warning(typeCheck('XREF', $this->ligne['desc']));
		$repo['repo_id'] = $this->getID_xref('REPO', $this->ligne['desc']);
		
		$this->lireLigne();
		while ($this->ligne['no'] > $n) {
			switch ($this->ligne['tag']) {
				case 'NOTE' :
					$note_id = $this->lectureNOTE_STRUCTURE($n +1);
					//Non supporté
					//$this->ajoutLien('',$repo['id'], $note_id);
					continue 2;

				case 'CALN' :
					$this->warning(typeCheck('SOURCE_CALL_NUMBER', $this->ligne['desc']));
					$repo['repo_caln'] = $this->ligne['desc'];
					$this->lireLigne();
					if ($this->ligne['no'] == $n +2) {
						if ($this->ligne['tag'] != 'MEDI') {
							$this->warning('Tag MEDI attendu', $n +1);
							continue 2;
						}
						$this->warning(typeCheck('SOURCE_MEDIA_TYPE', $this->ligne['desc']));
						$repo['repo_medi'] = $this->ligne['desc'];
						$this->lireLigne($n +1);
					}
					continue 2;

				default :
					$this->erreur(':-:-:-:-Tag '.$this->ligne['tag'].' inattendu', $n +1);
					break;
			}
		} //while
		return $repo;
	} // function lectureSOURCE_REPOSITORY_CITATION
}
?>
