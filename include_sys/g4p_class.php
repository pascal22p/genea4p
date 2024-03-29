<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                                                          *
 *Copyright (C) 2006                                          PAROIS Pascal *
 *                                                                          *
 *This program is free software; you can redistribute it and/or modify      *
 *it under the terms of the GNU General Public License as published by      *
 *the Free Software Foundation; either version 2 of the License, or         *
 *(at your option) any later version.                                       *
 *                                                                          *
 *This program is distributed in the hope that it will be useful,           *
 *but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the               *
 *GNU General Public License for more details.                              *
 *                                                                          *
 *You should have received a copy of the GNU General Public License         *
 *along with this program; if not, write to the Free Software               *
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA02111-1307 USA   *
 *                                                                          *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *Class individu                                                           *
 *                                                                         *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
// $Id: g4p_class.php 297 2007-02-17 20:32:20Z pascal $

/**
 * Contient les données temporaires extraits des tables sqls
 * @author Pascal Parois
 * @param indi_id   Id de la personne
 */
class g4p_sql_datas
{
    //public $liste_sources, $liste_familles, $liste_sour_records, $liste_medias, $liste_repo, $liste_notes, $liste_indi;
    private $g4p_infos_req, $g4p_infos, $sql, $g4p_result_req, $ligne;
    //public $g4p_infos_indi, $g4p_alias, $rel_indi_sources, $rel_indi_notes, $rel_indi_medias, $g4p_parents, $g4p_rel_indi_events, $g4p_rel_indi_attributes, $g4p_events_details, $g4p_adresses, $g4p_places, $rel_events_sources, $rel_events_notes, $rel_events_media, $g4p_sour_citations, $rel_sour_citations_notes, $g4p_sour_records, $g4p_repo, $g4p_medias, $rel_medias_notes, $g4p_notes, $g4p_infos_indi2;

    /**
     * Lecture des données en BD et remplissage de l'objet
     * @author Pascal Parois
     */
    function __construct($indi_id, $debug=false)
    {
        global $g4p_mysqli;

        $liste_sources=$liste_familles=$liste_sour_records=$liste_medias=array();
        $liste_repo=$liste_notes=$liste_indi=array();
        $liste_indi[]=$indi_id;

        if($debug===true)
            $this->debug=true;
        else
            $this->debug=false;

        if($this->debug)
            echo 'Charge indi : '.$indi_id.'<br />';

        $sql="SELECT base, nom AS basename, indi_nom , indi_prenom ,
            indi_sexe, indi_timestamp, indi_npfx, indi_givn, indi_nick,
            indi_spfx, indi_nsfx, indi_resn
            FROM genea_individuals
            LEFT JOIN genea_infos ON genea_individuals.base=genea_infos.id
            WHERE indi_id=".$indi_id;
        $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
        if($g4p_infos=$g4p_mysqli->g4p_result($g4p_infos_req))
        {
            $this->g4p_infos_indi=$g4p_infos[0];
            if($this->debug)
                echo 'indi trouve : '.$this->g4p_infos_indi['indi_nom'].' '.$this->g4p_infos_indi['indi_prenom'].'<br />';
        }
        else
        {
            //trigger_error("Personne introuvable", E_USER_ERROR);
            $this->g4p_infos_indi=-1;
        }

        // Récupération des alias de la personne
        $g4p_infos_req=$g4p_mysqli->g4p_query("SELECT alias1,alias2 FROM rel_alias
            WHERE alias1=".$indi_id." OR alias2=".$indi_id);
        if($this->g4p_alias=$g4p_mysqli->g4p_result($g4p_infos_req))
        {
            foreach($this->g4p_alias as $g4p_a_info)
            {
                if($g4p_a_info['alias1']==$indi_id)
                    $liste_indi[]=$g4p_a_info['alias2'];
                else
                    $liste_indi[]=$g4p_a_info['alias1'];
            }
        }

        //Récupération des ids des sources de l'individu
        $sql="SELECT sour_citations_id, indi_id
            FROM rel_indi_sources
            WHERE indi_id=".$indi_id;
        $g4p_result_req=$g4p_mysqli->g4p_query($sql);
        while($ligne=$g4p_result_req->fetch_assoc())
        {
            $liste_sources[]=$ligne['sour_citations_id'];
            $this->rel_indi_sources[$ligne['indi_id']][]=$ligne;
        }

        //Récupération des ids des notes de l'individu
        $sql="SELECT notes_id, indi_id
            FROM rel_indi_notes
            WHERE indi_id=".$indi_id;
        $g4p_result_req=$g4p_mysqli->g4p_query($sql);
        while($ligne=$g4p_result_req->fetch_assoc())
        {
            $liste_notes[]=$ligne['notes_id'];
            $this->rel_indi_notes[$ligne['indi_id']][]=$ligne;
        }

        //Récupération des ids des médias de l'individu
        $sql="SELECT media_id, indi_id " .
            " FROM rel_indi_multimedia" .
            " WHERE indi_id=".$indi_id;
        $g4p_result_req=$g4p_mysqli->g4p_query($sql);
        while($ligne=$g4p_result_req->fetch_assoc())
        {
            $liste_medias[]=$ligne['media_id'];
            $this->rel_indi_medias[$ligne['indi_id']][]=$ligne;
        }

        //récupérations de familles
        $sql="SELECT familles_id, familles_husb, familles_wife, familles_timestamp FROM genea_familles
            WHERE familles_husb=".$indi_id." OR familles_wife=".$indi_id;
        $g4p_result_req=$g4p_mysqli->g4p_query($sql);
        if($this->g4p_familles=$g4p_mysqli->g4p_result($g4p_result_req, 'familles_id'))
        {
            foreach($this->g4p_familles as $g4p_a_famille)
            {
                $liste_familles[]=$g4p_a_famille['familles_id'];
                $liste_indi[]=$g4p_a_famille['familles_husb'];
                $liste_indi[]=$g4p_a_famille['familles_wife'];
            }
        }

        $liste_familles=array_diff($liste_familles, array(''));
        if(count($liste_familles)>0)
        {
            //Récupération des ids des sources des familles
            $sql="SELECT sour_citations_id, familles_id
                FROM rel_familles_sources
                WHERE familles_id IN (".implode(',',$liste_familles).")";
            $g4p_result_req=$g4p_mysqli->g4p_query($sql);
            while($ligne=$g4p_result_req->fetch_assoc())
            {
                $liste_sources[]=$ligne['sour_citations_id'];
                $this->rel_familles_sour_details[$ligne['familles_id']][]=$ligne;
            }

            //Récupération des ids des notes des familles
            $sql="SELECT notes_id, familles_id
                FROM rel_familles_notes
                WHERE familles_id IN (".implode(',',$liste_familles).")";
            $g4p_result_req=$g4p_mysqli->g4p_query($sql);
            while($ligne=$g4p_result_req->fetch_assoc())
            {
                $liste_notes[]=$ligne['notes_id'];
                $this->rel_familles_notes[$ligne['familles_id']][]=$ligne;
            }

            //Récupération des ids des médias des familles
            $sql="SELECT media_id, familles_id " .
                " FROM rel_familles_multimedia" .
                " WHERE familles_id IN (".implode(',',$liste_familles).")";
            $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
            while($ligne=$g4p_result_req->fetch_assoc())
            {
                $liste_medias[]=$ligne['media_id'];
                $this->rel_familles_media[$ligne['familles_id']][]=$ligne;
            }

            //Récupération des évènements familiaux
            $sql="SELECT events_details_id,familles_id,events_tag,events_attestation ".
                " FROM rel_familles_events" .
                " WHERE familles_id IN (".implode(',',$liste_familles).")";
            $g4p_result_req=$g4p_mysqli->g4p_query($sql);
            while($ligne=$g4p_result_req->fetch_assoc())
            {
                $liste_events[]=$ligne['events_details_id'];
                $this->g4p_rel_familles_events[$ligne['familles_id']][]=$ligne;
            }

            //Récupération des attributs familiaux
            //ça existe pas.... grrrr....

            //récupération des enfants
            $sql = "SELECT DISTINCT f.familles_id, f.indi_id, rela_type, rela_stat FROM rel_familles_indi f".
                " LEFT JOIN rel_indi_events i ON f.indi_id=i.indi_id" .
                " LEFT JOIN genea_events_details e ON i.events_details_id=e.events_details_id".
                " AND (i.events_tag='BIRT' OR i.events_tag='BAPM')".
                " WHERE familles_id IN (".implode(',',$liste_familles).")".
                " ORDER BY jd_count";
            $g4p_result_req=$g4p_mysqli->g4p_query($sql);
            if($g4p_result_order=$g4p_mysqli->g4p_result($g4p_result_req))
            {
                foreach($g4p_result_order as $g4p_a_enfant)
                {
                    $this->g4p_familles_enfants[$g4p_a_enfant['familles_id']][]=$g4p_a_enfant;
                    $liste_indi[]=$g4p_a_enfant['indi_id'];
                }
            }
        }

        //Récupérations des parents
        $sql="SELECT genea_familles.familles_id as familles_id, indi_id,
            rela_type, familles_husb, familles_wife FROM rel_familles_indi
            LEFT JOIN genea_familles USING (familles_id)
            WHERE indi_id=".$indi_id;
        $g4p_result_req=$g4p_mysqli->g4p_query($sql);
        if($this->g4p_parents=$g4p_mysqli->g4p_result($g4p_result_req))
        {
            foreach($this->g4p_parents as $g4p_a_parent)
            {
                $liste_indi[]=$g4p_a_parent['familles_husb'];
                $liste_indi[]=$g4p_a_parent['familles_wife'];
            }
        }

        //récupérations des ids des évènements individuels
        $liste_indi=array_unique($liste_indi);
        $liste_indi=array_diff($liste_indi, array(''));
        $sql="SELECT events_details_id, indi_id, events_tag, events_attestation ".
            " FROM rel_indi_events" .
            " WHERE indi_id IN (".implode(',',$liste_indi).")";

        $g4p_result_req=$g4p_mysqli->g4p_query($sql);
        while($ligne=$g4p_result_req->fetch_assoc())
        {
            $liste_events[]=$ligne['events_details_id'];
            $this->g4p_rel_indi_events[$ligne['indi_id']][]=$ligne;
        }

        //récupérations des attributs individuels
        $sql="SELECT events_details_id,indi_id,events_tag,events_descr ".
            " FROM rel_indi_attributes" .
            " WHERE indi_id IN (".implode(',',$liste_indi).")";
        $g4p_result_req=$g4p_mysqli->g4p_query($sql);
        while($ligne=$g4p_result_req->fetch_assoc())
        {
            $liste_events[]=$ligne['events_details_id'];
            $this->g4p_rel_indi_attributes[$ligne['indi_id']][]=$ligne;
        }

        //Chargements des évènements
        if(!empty($liste_events))
        {
            $sql="SELECT events_details_id, place_id, events_details_descriptor, " .
                " events_details_gedcom_date, " .
                " events_details_age, events_details_cause, jd_count, jd_precision, jd_calendar, " .
                " events_details_timestamp, addr_id ".
                " FROM genea_events_details".
                " WHERE events_details_id IN (".implode(',',$liste_events).")";
            $g4p_result_req=$g4p_mysqli->g4p_query($sql);
            if($this->g4p_events_details=$g4p_mysqli->g4p_result($g4p_result_req, 'events_details_id'))
            {
                foreach($this->g4p_events_details as $g4p_a_place)
                {
                    if(!empty($g4p_a_place['place_id']))
                        $liste_place[]=$g4p_a_place['place_id'];
                    if(!empty($g4p_a_place['addr_id']))
                        $liste_addr[]=$g4p_a_place['addr_id'];
                }
            }

            //Chargements des lieux
            if(!empty($liste_place))
            {
                $liste_place=array_unique($liste_place);
                $sql="SELECT place_id,place_lieudit,place_ville,place_cp,place_insee,
                    place_departement,place_region,place_pays,place_longitude,place_latitude,base".
                    " FROM genea_place ".
                    " WHERE place_id IN (".implode(',',$liste_place).")";
                $g4p_result_req=$g4p_mysqli->g4p_query($sql);
                $this->g4p_places=$g4p_mysqli->g4p_result($g4p_result_req, 'place_id');
            }

            //Récupération des ids des sources des sévènements
            $sql="SELECT sour_citations_id, events_details_id
                FROM rel_events_sources
                WHERE events_details_id IN (".implode(',',$liste_events).")";
            $g4p_result_req=$g4p_mysqli->g4p_query($sql);
            while($ligne=$g4p_result_req->fetch_assoc())
            {
                $liste_sources[]=$ligne['sour_citations_id'];
                $this->rel_events_sources[$ligne['events_details_id']][]=$ligne;
            }

            //Récupération des ids des notes des évènements
            $sql="SELECT notes_id, events_details_id
                FROM rel_events_notes
                WHERE events_details_id IN (".implode(',',$liste_events).")";
            $g4p_result_req=$g4p_mysqli->g4p_query($sql);
            while($ligne=$g4p_result_req->fetch_assoc())
            {
                $liste_notes[]=$ligne['notes_id'];
                $this->rel_events_notes[$ligne['events_details_id']][]=$ligne;
            }

            //Récupération des ids des médias des évènements
            $sql="SELECT media_id, events_details_id" .
                " FROM rel_events_multimedia" .
                " WHERE events_details_id IN (".implode(',',$liste_events).")";
            $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
            while($ligne=$g4p_infos_req->fetch_assoc())
            {
                $liste_medias[]=$ligne['media_id'];
                $this->rel_events_medias[$ligne['events_details_id']][]=$ligne;
            }
        }

        //Chargement des sour_citations
        $liste_sources=array_diff($liste_sources, array(''));
        $liste_sources=array_unique($liste_sources);
        if(count($liste_sources)!=0)
        {
            $sql="SELECT sour_citations_id,sour_records_id,sour_citations_page,sour_citations_even,
                sour_citations_even_role,sour_citations_data_dates,sour_citations_data_text,
                sour_citations_quay,sour_citations_subm,sour_citations_timestamp" .
                " FROM genea_sour_citations" .
                " WHERE sour_citations_id in (".implode(',',$liste_sources).")";
            $g4p_result_req=$g4p_mysqli->g4p_query($sql);
            if($this->g4p_sour_citations=$g4p_mysqli->g4p_result($g4p_result_req,'sour_citations_id'))
                foreach($this->g4p_sour_citations as $g4p_a_source)
                    $liste_sour_records[]=$g4p_a_source['sour_records_id'];

            //Récupération des ids des notes des sour_citations
            $sql="SELECT notes_id, sour_citations_id
                FROM rel_sour_citations_notes
                WHERE sour_citations_id IN (".implode(',',$liste_sources).")";
            $g4p_result_req=$g4p_mysqli->g4p_query($sql);
            while($ligne=$g4p_result_req->fetch_assoc())
            {
                $liste_notes[]=$ligne['notes_id'];
                $this->rel_sour_citations_notes[$ligne['sour_citations_id']][]=$ligne;
            }

            //Récupération des ids des médias des sour_citations
            $sql="SELECT media_id, sour_citations_id
                FROM rel_sour_citations_multimedia
                WHERE sour_citations_id IN (".implode(',',$liste_sources).")";
            $g4p_result_req=$g4p_mysqli->g4p_query($sql);
            while($ligne=$g4p_result_req->fetch_assoc())
            {
                $liste_medias[]=$ligne['media_id'];
                $this->rel_sour_citations_medias[$ligne['sour_citations_id']][]=$ligne;
            }
        }

        $liste_sour_records=array_diff($liste_sour_records, array(''));
        if(!empty($liste_sour_records))
        {
            $liste_sour_records=array_unique($liste_sour_records);
            //Chargement des sour_records
            $sql="SELECT sour_records_id,sour_records_auth,sour_records_title,sour_records_abbr," .
                " sour_records_publ,sour_records_agnc,sour_records_rin,repo_id,repo_caln,repo_medi," .
                " sour_records_timestamp ".
                " FROM genea_sour_records" .
                " WHERE sour_records_id in (".implode(',',$liste_sour_records).")";
            $g4p_result_req=$g4p_mysqli->g4p_query($sql);
            if($this->g4p_sour_records=$g4p_mysqli->g4p_result($g4p_result_req, 'sour_records_id'))
                foreach($this->g4p_sour_records as $g4p_a_source)
                    if(!empty($g4p_a_source['repo_id']))
                        $liste_repo[]=$g4p_a_source['repo_id'];
        }

        $liste_repo=array_diff($liste_repo, array(''));
        $liste_repo=array_unique($liste_repo);
        if(!empty($liste_repo))
        {
            $liste_repo=array_unique($liste_repo);
            //chargement des dépots
            $sql="SELECT repo_id,base,repo_name,addr_id,repo_timestamp ".
                " FROM genea_repository" .
                " WHERE repo_id in (".implode(',',$liste_repo).")";
            $g4p_result_req=$g4p_mysqli->g4p_query($sql);
            if($this->g4p_repo=$g4p_mysqli->g4p_result($g4p_result_req, 'repo_id'))
                foreach($this->g4p_repo as $g4p_a_source)
                    if(!empty($g4p_a_source['addr_id']))
                        $liste_addr[]=$g4p_a_source['addr_id'];
        }

        //Chargements des adresses
        $liste_repo=array_diff($liste_repo, array(''));
        $liste_repo=array_unique($liste_repo);
        if(!empty($liste_addr))
        {
            $sql="SELECT * ".
                " FROM genea_address ".
                " WHERE addr_id IN (".implode(',',$liste_addr).")";
            $g4p_result_req=$g4p_mysqli->g4p_query($sql);
            $this->g4p_adresses=$g4p_mysqli->g4p_result($g4p_result_req, 'addr_id');
        }

        $liste_medias=array_diff($liste_medias, array(''));
        if(!empty($liste_medias))
        {
            //Chargement des médias
            $sql="SELECT media_id,base,nom AS basename,media_title,media_format,media_file,media_timestamp" .
                " FROM genea_multimedia" .
                " LEFT JOIN genea_infos ON genea_multimedia.base=genea_infos.id" .
                " WHERE media_id IN (".implode(',',$liste_medias).")";
            $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
            $this->g4p_medias=$g4p_mysqli->g4p_result($g4p_infos_req,'media_id');

            //Récupération des ids des notes des medias
            $sql="SELECT notes_id, media_id
                FROM rel_multimedia_notes
                WHERE media_id IN (".implode(',',$liste_medias).")";
            $g4p_result_req=$g4p_mysqli->g4p_query($sql);
            while($ligne=$g4p_result_req->fetch_assoc())
            {
                $liste_notes[]=$ligne['notes_id'];
                $this->rel_medias_notes[$ligne['media_id']][]=$ligne;
            }
        }

        $liste_notes=array_diff($liste_notes, array(''));
        if(!empty($liste_notes))
        {
            //chargement des notes
            $sql="SELECT notes_text, notes_id, notes_timestamp FROM genea_notes
                WHERE notes_id IN (".implode(',',$liste_notes).")";
            $g4p_result_req=$g4p_mysqli->g4p_query($sql);
            $this->g4p_notes=$g4p_mysqli->g4p_result($g4p_result_req,'notes_id');
        }

        //chargement des individus
        $g4p_infos_req=$g4p_mysqli->g4p_query("SELECT indi_id, base, nom AS basename, indi_nom , indi_prenom ,
            indi_sexe, indi_timestamp, indi_npfx, indi_givn, indi_nick, indi_spfx, indi_nsfx, indi_resn
            FROM genea_individuals
            LEFT JOIN genea_infos ON genea_individuals.base=genea_infos.id
            WHERE indi_id IN (".implode(',',$liste_indi).")");
        $this->g4p_infos_indi=$g4p_mysqli->g4p_result($g4p_infos_req, 'indi_id');
        $this->liste_indi=$liste_indi;
    }

}


/**
 * Contient toutes les informations relatives à la personnes y compris les filiations
 * @author Pascal Parois
 * @param indi_id   Id de la personne
 */
class g4p_individu
{
    //public $alias, $attributes, $familles, $parents, $notes, $sources, $medias, $base, $nom, $prenom, $npfx, $givn, $nick, $spfx, $surn, $nsfx, $sexe, $timestamp, $resn, $events;
    /**
     * Constructeur
     * @author Pascal Parois
     * @param indi_id   Id de la personne
     */
    function __construct($indi_id)
    {
        $this->indi_id=$indi_id;
        $this->debug=false;
        $this->filiation=true;
        $this->ignorecache=false;
    }

    function set_debug($debug)
    {
        if($debug===true)
        {
            $this->debug=true;
            echo 'debuggage load indi<br />';
        }
        else
            $this->debug=false;
    }

    function set_filiation($filiation)
    {
        if($filiation===true)
            $this->filiation=true;
        else
            $this->filiation=false;
    }

    function ignore_cache($cache)
    {
        if($cache===true)
            $this->ignorecache=true;
        else
            $this->ignorecache=false;
    }

    private function g4p_load_from_db()
    {
        $this->g4p_sql_datas=new g4p_sql_datas($this->indi_id, $this->debug);
        if($this->g4p_sql_datas->g4p_infos_indi!==-1)
        {
            //$this->g4p_etat_civil($this->g4p_sql_datas->g4p_infos_indi,$g4p_sql_datas);
        }
        else
        {
            $this->indi_id=-1;
        }
    }

    function g4p_write_cache(&$g4p_sql_datas)
    {
        global $g4p_chemin, $g4p_mysqli;

        //dépendances
        $deps=array();
        foreach($g4p_sql_datas->liste_indi as $a_indi)
            if($this->indi_id!=$a_indi)
                $deps[]='('.$this->indi_id.','.$a_indi.')';

        $g4p_mysqli->autocommit(false);
        $sql="DELETE FROM genea_cache_deps WHERE indi_id=".$this->indi_id;
        $g4p_mysqli->query($sql);
        if(!empty($deps))
        {
            $sql="INSERT INTO genea_cache_deps (indi_id, indi_dep) VALUES ".implode(',',$deps);
            if($g4p_mysqli->query($sql))
            {
                if(file_put_contents($g4p_chemin.'cache/indis/indi_'.$this->indi_id.'.txt',serialize($this),LOCK_EX))
                    $g4p_mysqli->commit();
                else
                {
                    die('impossible d\'écrire le fichier de cache');
                    $g4p_mysqli->rollback();
                }
            }
            else
            {
                die('impossible d\'insérer les deps de cache en base '.$g4p_mysqli->error.' <br> '.$sql);
                $g4p_mysqli->rollback();
            }
        }
        else
            $g4p_mysqli->commit();
    }

    function __SLEEP()
    {
        if(isset($this->g4p_sql_datas))
            unset($this->g4p_sql_datas);
        return( array_keys( get_object_vars( $this ) ) );
    }

    function g4p_load()
    {
        global $g4p_chemin;

        if($this->ignorecache)
        {
            $this->g4p_load_from_db();
            $this->g4p_write_object($this->g4p_sql_datas);
            $this->g4p_write_cache($this->g4p_sql_datas);
            return ;
        }
        else
        {
            if(file_exists($g4p_chemin.'cache/indis/indi_'.$this->indi_id.'.txt'))
            {
                $this->g4p_load_from_cache();
                //var_dump($this);
                return;
            }
            else
            {
                $this->g4p_load_from_db();
                $this->g4p_write_object($this->g4p_sql_datas);
                $this->g4p_write_cache($this->g4p_sql_datas);
                return ;
            }
        }
    }

    private function g4p_load_from_cache()
    {
        global $g4p_chemin;
        if(file_exists($g4p_chemin.'cache/indis/indi_'.$this->indi_id.'.txt'))
        {
            if($g4p_mon_indi=unserialize(file_get_contents($g4p_chemin.'cache/indis/indi_'.$this->indi_id.'.txt')))
            {
                //var_dump($g4p_mon_indi);
                //exit;
                foreach($g4p_mon_indi as $key=>$val)
                {
                    $this->$key=$val;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Rempli l'objet
     * @author Pascal Parois
     */
    function g4p_write_object(&$g4p_sql_datas)
    {
        $this->g4p_etat_civil($g4p_sql_datas);
        //var_dump($g4p_sql_datas->g4p_infos_indi[$this->indi_id]);

        //les alias
        if(!empty($g4p_sql_datas->g4p_alias))
        {
            foreach($g4p_sql_datas->g4p_alias as $g4p_a_info)
            {
                if($g4p_a_info['alias1']==$this->indi_id)
                {
                    $this->alias[$g4p_a_info['alias2']]=new g4p_individu($g4p_a_info['alias2']);
                    $this->alias[$g4p_a_info['alias2']]->set_filiation(false);
                    $this->alias[$g4p_a_info['alias2']]->set_debug($this->debug);
                    $this->alias[$g4p_a_info['alias2']]->g4p_etat_civil($g4p_sql_datas);
                }
                else
                {
                    $this->alias[$g4p_a_info['alias1']]=new g4p_individu($g4p_a_info['alias1']);
                    $this->alias[$g4p_a_info['alias1']]->set_filiation(false);
                    $this->alias[$g4p_a_info['alias1']]->set_debug($this->debug);
                    $this->alias[$g4p_a_info['alias1']]->g4p_etat_civil($g4p_sql_datas);
                }
            }
        }

        //les familles
        if(!empty($g4p_sql_datas->g4p_familles))
            foreach($g4p_sql_datas->g4p_familles as $g4p_a_famille)
                $this->familles[$g4p_a_famille['familles_id']]=new g4p_famille($g4p_a_famille,$g4p_sql_datas, $this->debug);
        if($this->debug)
            var_dump($this->familles);

        //les parents
        if(!empty($g4p_sql_datas->g4p_parents))
            foreach($g4p_sql_datas->g4p_parents as $g4p_a_parent)
                $this->parents[$g4p_a_parent['familles_id']]=new g4p_parents($g4p_a_parent,$g4p_sql_datas, $this->debug);

        //notes, sources, medias
        if(!empty($g4p_sql_datas->rel_indi_notes[$this->indi_id]))
            foreach($g4p_sql_datas->rel_indi_notes[$this->indi_id] as $g4p_a_note)
                $this->notes[$g4p_a_note['notes_id']]=new g4p_note($g4p_sql_datas->g4p_notes[$g4p_a_note['notes_id']],$g4p_sql_datas);
        if(!empty($g4p_sql_datas->rel_indi_sources[$this->indi_id]))
            foreach($g4p_sql_datas->rel_indi_sources[$this->indi_id] as $g4p_a_source)
                $this->sources[$g4p_a_source['sour_citations_id']]=new g4p_source_citation($g4p_sql_datas->g4p_sour_citations[$g4p_a_source['sour_citations_id']],$g4p_sql_datas);
        if(!empty($g4p_sql_datas->rel_indi_medias[$this->indi_id]))
            foreach($g4p_sql_datas->rel_indi_medias[$this->indi_id] as $g4p_a_media)
                $this->medias[$g4p_a_media['media_id']]=new g4p_media($g4p_sql_datas->g4p_medias[$g4p_a_media['media_id']],$g4p_sql_datas);
    }

    /**
     * Rempli l'état civil
     * @author Pascal Parois
     */
    function g4p_etat_civil(&$g4p_sql_datas)
    {
        //var_dump($g4p_sql_datas->g4p_infos_indi);
        //echo $this->indi_id;
        $this->base=$g4p_sql_datas->g4p_infos_indi[$this->indi_id]['base'];
        $this->basename=$g4p_sql_datas->g4p_infos_indi[$this->indi_id]['basename'];
        $this->nom=$g4p_sql_datas->g4p_infos_indi[$this->indi_id]['indi_nom'];
        $this->prenom=$g4p_sql_datas->g4p_infos_indi[$this->indi_id]['indi_prenom'];
        $this->npfx=$g4p_sql_datas->g4p_infos_indi[$this->indi_id]['indi_npfx'];
        $this->givn=$g4p_sql_datas->g4p_infos_indi[$this->indi_id]['indi_givn'];
        $this->nick=$g4p_sql_datas->g4p_infos_indi[$this->indi_id]['indi_nick'];
        $this->spfx=$g4p_sql_datas->g4p_infos_indi[$this->indi_id]['indi_spfx'];
        $this->nsfx=$g4p_sql_datas->g4p_infos_indi[$this->indi_id]['indi_nsfx'];

        if($g4p_sql_datas->g4p_infos_indi[$this->indi_id]['indi_sexe']=='M' or $g4p_sql_datas->g4p_infos_indi[$this->indi_id]['indi_sexe']=='F')
            $this->sexe=$g4p_sql_datas->g4p_infos_indi[$this->indi_id]['indi_sexe'];
        else
            $this->sexe='I';
        $this->timestamp=$g4p_sql_datas->g4p_infos_indi[$this->indi_id]['indi_timestamp'];
        $this->resn=$g4p_sql_datas->g4p_infos_indi[$this->indi_id]['indi_resn'];

        //évènements individuels
        if(!empty($g4p_sql_datas->g4p_rel_indi_events[$this->indi_id]))
            foreach($g4p_sql_datas->g4p_rel_indi_events[$this->indi_id] as $g4p_a_indi_events)
                $this->events[$g4p_a_indi_events['events_details_id']]=new g4p_event($g4p_a_indi_events,$g4p_sql_datas);

        //attributs inidviduels
        if(!empty($g4p_sql_datas->g4p_rel_indi_attributes[$this->indi_id]))
            foreach($g4p_sql_datas->g4p_rel_indi_attributes[$this->indi_id] as $g4p_a_indi_attribut)
            {
                $this->attributes[$g4p_a_indi_attribut['events_details_id']]=new g4p_attribute($g4p_a_indi_attribut,$g4p_sql_datas);
                /*echo '***';
                echo is_array($g4p_a_indi_attribut);
                echo '+++';*/
            }
    }

    /**
     * Retourne naissance-décès
     * @author Pascal Parois
     * @param format Format de la date
     * @return Date
     */
    function date_rapide($format='')
    {
        if($format=='short')
            $format2='date1short';
        else
            $format2='date1';

        if(!empty($this->events))
        {
            foreach($this->events as $a_event)
            {
                if($a_event->tag=='BIRT')
                    $date['BIRT']=g4p_date($a_event->gedcom_date, $format2);
                elseif($a_event->tag=='BAPM')
                    $date['BAPM']=g4p_date($a_event->gedcom_date, $format2);
                elseif($a_event->tag=='DEAT')
                    $date['DEAT']=g4p_date($a_event->gedcom_date, $format2);
                elseif($a_event->tag=='BURI')
                    $date['BURI']=g4p_date($a_event->gedcom_date, $format2);
            }

            if(!empty($date['BIRT']))
                if($format=='short')
                    $naissance=$date['BIRT'];
                else
                    $naissance='°'.$date['BIRT'];
            elseif(!empty($date['BAPM']))
                if($format=='short')
                    $naissance=$date['BAPM'];
                else
                    $naissance='°'.$date['BAPM'];
            else
                $naissance='';

            if(!empty($date['DEAT']))
                if($format=='short')
                    $deces=$date['DEAT'];
                else
                    $deces='†'.$date['DEAT'];
            elseif(!empty($date['BURI']))
                if($format=='short')
                    $deces=$date['BURI'];
                else
                    $deces='†'.$date['BURI'];
            else
                $deces='';

            if(empty($naissance) and empty($deces))
                return NULL;

            if(!empty($deces) and !empty($naissance))
                $sep=' &ndash; ';
            else
                $sep='';

            if($format=='array')
                return array('naissance'=>$naissance,'deces'=>$deces);
            else
                return '('.$naissance.$sep.$deces.')';
        }
        else
            return NULL;
    }

}

/**
 * Objet contenant une famille
 * @author Pascal Parois
 * @param famille Id de la famille
 */
class g4p_famille
{
    function __construct($famille,&$g4p_sql_datas, $debug)
    {
        if($debug===true)
            $this->debug=true;
        else
            $this->debug=false;

        $this->id=$famille['familles_id'];
        $this->timestamp=$famille['familles_timestamp'];

        if(!empty($famille['familles_husb']))
        {
            $this->husb=new g4p_individu($famille['familles_husb']);
            $this->husb->set_filiation(false);
            $this->husb->set_debug($this->debug);
            $this->husb->g4p_etat_civil($g4p_sql_datas);
        }
        if(!empty($famille['familles_wife']))
        {
            $this->wife=new g4p_individu($famille['familles_wife']);
            $this->wife->set_filiation(false);
            $this->wife->set_debug($this->debug);
            $this->wife->g4p_etat_civil($g4p_sql_datas);
        }
        if(!empty($g4p_sql_datas->g4p_familles_enfants[$this->id]))
        {
            foreach($g4p_sql_datas->g4p_familles_enfants[$this->id] as $a_enfant)
            {
                $this->enfants[$a_enfant['indi_id']]['indi']=new g4p_individu($a_enfant['indi_id']);
                $this->enfants[$a_enfant['indi_id']]['indi']->set_filiation(false);
                $this->enfants[$a_enfant['indi_id']]['indi']->set_debug($this->debug);
                $this->enfants[$a_enfant['indi_id']]['indi']->g4p_etat_civil($g4p_sql_datas);

                $this->enfants[$a_enfant['indi_id']]['rela_type']=$a_enfant['rela_type'];
                $this->enfants[$a_enfant['indi_id']]['rela_stat']=$a_enfant['rela_stat'];
            }
        }

        if(!empty($g4p_sql_datas->g4p_rel_familles_events[$this->id]))
            foreach($g4p_sql_datas->g4p_rel_familles_events[$this->id] as $a_fevent)
                $this->events[$a_fevent['events_details_id']]=new g4p_event($a_fevent,$g4p_sql_datas);

        if(!empty($g4p_sql_datas->rel_familles_notes[$this->id]))
            foreach($g4p_sql_datas->rel_familles_notes[$this->id] as $g4p_a_note)
                $this->notes[$g4p_a_note['notes_id']]=new g4p_note($g4p_sql_datas->g4p_notes[$g4p_a_note['notes_id']],$g4p_sql_datas);
        if(!empty($g4p_sql_datas->rel_familles_sour_details[$this->id]))
            foreach($g4p_sql_datas->rel_familles_sour_details[$this->id] as $g4p_a_source)
                $this->sources[$g4p_a_source['sour_citations_id']]=new g4p_source_citation($g4p_sql_datas->g4p_sour_citations[$g4p_a_source['sour_citations_id']],$g4p_sql_datas);
        if(!empty($g4p_sql_datas->rel_familles_media[$this->id]))
            foreach($g4p_sql_datas->rel_familles_media[$this->id] as $g4p_a_media)
                $this->medias[$g4p_a_media['media_id']]=new g4p_media($g4p_sql_datas->g4p_medias[$g4p_a_media['media_id']],$g4p_sql_datas);
    }
}

/**
 * Objet contenant un évènement
 * @author Pascal Parois
 * @param g4p_event Tableau contenant les détails de l'évènement
 */
class g4p_event
{
    //public $id, $tag, $attestation, $place, $details_descriptor, $gedcom_date, $date, $age, $cause, $address, $timestamp, $notes, $sources, $medias;

    /**
     * Constructeur de l'objet évènement
     * @author Pascal Parois
     * @param g4p_event Tableau contenant les détails de l'évènement
     */
    function __construct($g4p_event,&$g4p_sql_datas)
    {
        $g4p_events_details=$g4p_sql_datas->g4p_events_details[$g4p_event['events_details_id']];
        $this->id=$g4p_event['events_details_id'];
        $this->tag=$g4p_event['events_tag'];
        $this->attestation=$g4p_event['events_attestation'];
        $this->place=new g4p_place($g4p_events_details['place_id'],$g4p_sql_datas);
        $this->details_descriptor=$g4p_events_details['events_details_descriptor'];
        $this->gedcom_date=$g4p_events_details['events_details_gedcom_date'];
        $this->date=new g4p_date($g4p_events_details,$g4p_sql_datas);
        $this->date->g4p_gedom2date();
        $this->age=$g4p_events_details['events_details_age'];
        $this->cause=$g4p_events_details['events_details_cause'];
        if(!empty($g4p_sql_datas->g4p_adresses[$g4p_events_details['addr_id']]))
            $this->address=new g4p_address($g4p_sql_datas->g4p_adresses[$g4p_events_details['addr_id']],$g4p_sql_datas);
        $this->timestamp=$g4p_events_details['events_details_timestamp'];

        if(!empty($g4p_sql_datas->rel_events_notes[$this->id]))
            $this->g4p_event_notes($g4p_sql_datas->rel_events_notes[$this->id],$g4p_sql_datas);
        if(!empty($g4p_sql_datas->rel_events_sources[$this->id]))
            $this->g4p_event_sources($g4p_sql_datas->rel_events_sources[$this->id],$g4p_sql_datas);
        if(!empty($g4p_sql_datas->rel_events_medias[$this->id]))
            $this->g4p_event_medias($g4p_sql_datas->rel_events_medias[$this->id],$g4p_sql_datas);

    }

    private function g4p_event_notes($g4p_event_notes,&$g4p_sql_datas)
    {
        foreach ($g4p_event_notes as $g4p_a_note)
            $this->notes[$g4p_a_note['notes_id']]=new g4p_note($g4p_sql_datas->g4p_notes[$g4p_a_note['notes_id']],$g4p_sql_datas);
    }

    private function g4p_event_sources($g4p_event_sources,&$g4p_sql_datas)
    {
        foreach ($g4p_event_sources as $g4p_a_source)
            $this->sources[$g4p_a_source['sour_citations_id']]=new g4p_source_citation($g4p_sql_datas->g4p_sour_citations[$g4p_a_source['sour_citations_id']],$g4p_sql_datas);
    }

    private function g4p_event_medias($g4p_event_medias,&$g4p_sql_datas)
    {
        foreach ($g4p_event_medias as $g4p_a_media)
            $this->medias[$g4p_a_media['media_id']]=new g4p_media($g4p_sql_datas->g4p_medias[$g4p_a_media['media_id']],$g4p_sql_datas);
    }
}

/**
 * Objet contenant un attribut
 * @author Pascal Parois
 * @param g4p_attribute Tableau contenant les détails de l'évènement
 */
class g4p_attribute
{
    //public $id, $tag, $description, $place, $detail_escriptor, $gedcom_date, $age, $cause, $address, $timestamp, $g4p_event_notes, $g4p_event_sources, $g4p_event_medias;

    /**
     * Constructeur de l'objet attribut
     * @author Pascal Parois
     * @param g4p_attribute Tableau contenant les détails de l'évènement
     */
    function __construct($g4p_attribute,&$g4p_sql_datas)
    {
        //echo '<pre>';
        //print_r($g4p_sql_datas->g4p_events_details);
        //echo '<hr>';
        //print_r($g4p_sql_datas->g4p_rel_indi_attributes);

        $g4p_events_details=$g4p_sql_datas->g4p_events_details[$g4p_attribute['events_details_id']];
        $this->id=$g4p_attribute['events_details_id'];
        $this->tag=$g4p_attribute['events_tag'];
        $this->description=$g4p_attribute['events_descr'];
        $this->place=new g4p_place($g4p_events_details['place_id'],$g4p_sql_datas);
        $this->details_descriptor=$g4p_events_details['events_details_descriptor'];
        $this->gedcom_date=$g4p_events_details['events_details_gedcom_date'];
        $this->age=$g4p_events_details['events_details_age'];
        $this->cause=$g4p_events_details['events_details_cause'];
        if(!empty($g4p_sql_datas->g4p_adresses[$g4p_events_details['addr_id']]))
            $this->address=new g4p_address($g4p_sql_datas->g4p_adresses[$g4p_events_details['addr_id']],$g4p_sql_datas);
        $this->timestamp=$g4p_events_details['events_details_timestamp'];

        if(!empty($g4p_sql_datas->rel_events_notes[$this->id]))
            $this->g4p_event_notes($g4p_sql_datas->rel_events_notes[$this->id], $g4p_sql_datas);
        if(!empty($g4p_sql_datas->rel_events_sources[$this->id]))
            $this->g4p_event_sources($g4p_sql_datas->rel_events_sources[$this->id], $g4p_sql_datas);
        if(!empty($g4p_sql_datas->rel_events_medias[$this->id]))
            $this->g4p_event_medias($g4p_sql_datas->rel_events_medias[$this->id]);
    }

    private function g4p_event_notes($g4p_event_notes, &$g4p_sql_datas)
    {
        foreach ($g4p_event_notes as $g4p_a_note)
            $this->notes[$g4p_a_note['notes_id']]=new g4p_note($g4p_sql_datas->g4p_notes[$g4p_a_note['notes_id']], $g4p_sql_datas);
    }

    private function g4p_event_sources($g4p_event_sources, &$g4p_sql_datas)
    {
        foreach ($g4p_event_sources as $g4p_a_source)
            $this->sources[$g4p_a_source['sour_citations_id']]=new g4p_source_citation($g4p_sql_datas->g4p_sour_citations[$g4p_a_source['sour_citations_id']], $g4p_sql_datas);
    }

    private function g4p_event_medias($g4p_event_medias)
    {
        foreach ($g4p_event_medias as $g4p_a_media)
            $this->medias[$g4p_a_media['media_id']]=new g4p_media($g4p_sql_datas->g4p_medias[$g4p_a_media['media_id']]);
    }
}

/**
 * Objet source citation (acte civil ou religieux d'un registre)
 * @author Pascal Parois
 * @param g4p_source_citation Tableau contenant les détails de l'acte
 */
class g4p_source_citation //acte
{
    /**
     * constructeur de l'objet source citation (acte civil ou religieux d'un registre)
     * @author Pascal Parois
     * @param g4p_source_citation Tableau contenant les détails de l'acte
     */
    function __construct($g4p_source_citation,&$g4p_sql_datas)
    {
        $this->id=$g4p_source_citation['sour_citations_id'];
        if(!empty($g4p_sql_datas->g4p_sour_records[$g4p_source_citation['sour_records_id']]))
            $this->record=new g4p_source_record($g4p_sql_datas->g4p_sour_records[$g4p_source_citation['sour_records_id']],$g4p_sql_datas);
        $this->page=$g4p_source_citation['sour_citations_page'];
        $this->even=$g4p_source_citation['sour_citations_even'];
        $this->even_role=$g4p_source_citation['sour_citations_even_role'];
        $this->data_dates=$g4p_source_citation['sour_citations_data_dates'];
        $this->data_text=$g4p_source_citation['sour_citations_data_text'];
        $this->quay=$g4p_source_citation['sour_citations_quay'];
        $this->subm=$g4p_source_citation['sour_citations_subm'];
        $this->timestamp=$g4p_source_citation['sour_citations_timestamp'];

        if(!empty($g4p_sql_datas->rel_sour_citations_medias[$g4p_source_citation['sour_citations_id']]))
            foreach($g4p_sql_datas->rel_sour_citations_medias[$g4p_source_citation['sour_citations_id']] as $a_media)
                $this->medias[$a_media['media_id']]=new g4p_media($g4p_sql_datas->g4p_medias[$a_media['media_id']],$g4p_sql_datas);

        //if(!empty($g4p_sql_datas->g4p_sour_records[$g4p_source_citation['sour_records_id']]))
            //$this->record=new g4p_source_record($g4p_sql_datas->g4p_sour_records[$g4p_source_citation['sour_records_id']],$g4p_sql_datas);

    }
}

/**
 * Objet source record (registre)
 * @author Pascal Parois
 * @param g4p_source_record Tableau contenant les détails du registre
 */
class g4p_source_record//registre
{
    /**
     * Constructeur de l'objet source record (registre)
     * @author Pascal Parois
     * @param g4p_source_record Tableau contenant les détails du registre
     */
    function __construct($g4p_source_record,&$g4p_sql_datas)
    {
        $this->id=$g4p_source_record['sour_records_id'];
        $this->auth=$g4p_source_record['sour_records_auth'];
        $this->title=$g4p_source_record['sour_records_title'];
        $this->abbr=$g4p_source_record['sour_records_abbr'];
        $this->publ=$g4p_source_record['sour_records_publ'];
        //$this->data_even=$g4p_source_record['sour_records_data_even'];
        //$this->data_date=$g4p_source_record['sour_records_data_date'];
        //$this->data_place=$g4p_source_record['sour_records_data_place'];
        $this->agnc=$g4p_source_record['sour_records_agnc'];
        $this->rin=$g4p_source_record['sour_records_rin'];
        $this->repo_medi=$g4p_source_record['repo_medi'];
        $this->repo_caln=$g4p_source_record['repo_caln'];
        if(!empty($g4p_source_record['repo_id']))
            $this->repo=new g4p_repo($g4p_sql_datas->g4p_repo[$g4p_source_record['repo_id']], $g4p_sql_datas);
        $this->timestamp=$g4p_source_record['sour_records_timestamp'];
    }
}

/**
 * Objet repository (dépot, archives départementales)
 * @author Pascal Parois
 * @param g4p_repo Tableau contenant les détails du dépot
 */
class g4p_repo
{
    /**
     * Constructeur de l'objet repository (dépot, archives départementales)
     * @author Pascal Parois
     * @param g4p_repo Tableau contenant les détails du dépot
     */
    function __construct($g4p_repo,&$g4p_sql_datas)
    {
        $this->id=$g4p_repo['repo_id'];
        $this->name=$g4p_repo['repo_name'];
        if(!empty($g4p_repo['addr_id']))
            $this->addr=new g4p_address($g4p_sql_datas->g4p_adresses[$g4p_repo['addr_id']], $g4p_sql_datas);
        $this->timestamp=$g4p_repo['repo_timestamp'];
    }
}

/**
 * Objet note
 * @author Pascal Parois
 * @param g4p_note Tableau contenant les détails de la note
 */
class g4p_note
{
    /**
     * Constructeur de l'objet note
     * @author Pascal Parois
     * @param g4p_note Tableau contenant les détails de la note
     */
    function __construct($g4p_note,&$g4p_sql_datas)
    {
        $this->id=$g4p_note['notes_id'];
        $this->text=$g4p_note['notes_text'];
        $this->timestamp=$g4p_note['notes_timestamp'];
    }
}

/**
 * Objet média
 * @author Pascal Parois
 * @param g4p_media Tableau contenant les détails du média
 */
class g4p_media
{
    /**
     * Constructeur de l'objet média
     * @author Pascal Parois
     * @param g4p_media Tableau contenant les détails du média
     */
    function __construct($g4p_media,&$g4p_sql_datas)
    {
        //var_dump($g4p_media);
        $this->id=$g4p_media['media_id'];
        $this->basename=$g4p_media['basename'];
        $this->title=$g4p_media['media_title'];
        $this->format=$g4p_media['media_format'];
        $this->file=$g4p_media['media_file'];
        $this->timestamp=$g4p_media['media_timestamp'];
    }
}

/**
 * Objet lieu
 * @author Pascal Parois
 * @param g4p_place Id du lieu
 */
class g4p_place
{
    /**
     * Constructeur de l'objet lieu
     * @author Pascal Parois
     * @param g4p_place Id du lieu
     */
    function __construct($g4p_place,&$g4p_sql_datas)
    {
        if(!empty($g4p_sql_datas->g4p_places[$g4p_place]))
        {
            $this->id=$g4p_sql_datas->g4p_places[$g4p_place]['place_id'];
            $this->lieudit=$g4p_sql_datas->g4p_places[$g4p_place]['place_lieudit'];
            $this->ville=$g4p_sql_datas->g4p_places[$g4p_place]['place_ville'];
            $this->cp=$g4p_sql_datas->g4p_places[$g4p_place]['place_cp'];
            $this->insee=$g4p_sql_datas->g4p_places[$g4p_place]['place_insee'];
            $this->departement=$g4p_sql_datas->g4p_places[$g4p_place]['place_departement'];
            $this->region=$g4p_sql_datas->g4p_places[$g4p_place]['place_region'];
            $this->pays=$g4p_sql_datas->g4p_places[$g4p_place]['place_pays'];
            $this->longitude=$g4p_sql_datas->g4p_places[$g4p_place]['place_longitude'];
            $this->latitude=$g4p_sql_datas->g4p_places[$g4p_place]['place_latitude'];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Construit une chaine formatée du lieu
     * @author Pascal Parois
     * @param place_format Format à adopter
     * @return Chaine formatée du lieu
     */
    public function g4p_formated_place($place_format='')
    {
        $place=array();
        if(!empty($this))
        {
            if(!empty($place_format))
            {
                foreach($place_format as $a_place)
                {
                    if(!empty($this->$a_place))
                        $place[]=$this->$a_place;
                    elseif(!empty($this->{'place_'.$a_place}))
                        $place[]=$this->{'place_'.$a_place};
                }
                return implode(', ',$place);
            }
            else
            {
                if(!empty($this->lieudit))
                    $tmp=$this->lieudit;
                else
                {
                    if(!empty($this->ville))
                        $tmp=$this->ville;
                    else
                        $tmp='';
                }

                if(!empty($this->region))
                {
                    if(!empty($tmp))
                        $tmp=$tmp.', '.$this->region;
                    else
                        $tmp=$this->region;
                }

                if(!empty($this->pays))
                {
       	       	    if(!empty($tmp))
       	       	       	$tmp=$tmp.', '.$this->pays;
                    else
                        $tmp=$this->pays;
                }

                if(!empty($tmp))
                    return '<a href="https://www.google.com/maps/place/'.$tmp.'">'.$tmp.'</a>';
                else
                    return '';
            }
        }
    }
}

/**
 * Objet adresse
 * @author Pascal Parois
 * @param g4p_address Tableau contenant le détail de l'adresse
 */
class g4p_address
{
    /**
     * Constructeur de l'objet adresse
     * @author Pascal Parois
     * @param g4p_address Tableau contenant le détail de l'adresse
     */
    function __construct($g4p_address,&$g4p_sql_datas)
    {
        //"addr_id";"base";"addr_addr";"addr_city";"addr_stae";"addr_post";"addr_ctry";
        //"addr_phon1";"addr_phon2";"addr_phon3";"addr_email1";"addr_email2";"addr_email3";
        //"addr_fax1";"addr_fax2";"addr_fax3";"addr_www1";"addr_www2";"addr_www3"

        $this->id=$g4p_address['addr_id'];
        $this->addr=$g4p_address['addr_addr'];
        $this->city=$g4p_address['addr_city'];
        $this->stae=$g4p_address['addr_stae'];
        $this->post=$g4p_address['addr_post'];
        $this->ctry=$g4p_address['addr_ctry'];
        $this->phon1=$g4p_address['addr_phon1'];
        $this->phon2=$g4p_address['addr_phon2'];
        $this->phon3=$g4p_address['addr_phon3'];
        $this->email1=$g4p_address['addr_email1'];
        $this->email2=$g4p_address['addr_email2'];
        $this->email3=$g4p_address['addr_email3'];
        $this->fax1=$g4p_address['addr_fax1'];
        $this->fax2=$g4p_address['addr_fax2'];
        $this->fax3=$g4p_address['addr_fax3'];
        $this->www1=$g4p_address['addr_www1'];
        $this->www2=$g4p_address['addr_www2'];
        $this->www3=$g4p_address['addr_www3'];
    }

    /**
     * Construit une chaine formatée de l'adresse
     * @author Pascal Parois
     * @param place_format Format à adopter - non implémenté
     * @return Chaine formatée de l'adresse
     */
    public function g4p_formated_addr($place_format='')
    {
        $place=array();
        if(!empty($this))
        {
            if(!empty($place_format))
            {
            }
            else
            {
                $tmp='';
                foreach($this as $key=>$value)
                    if(!empty($value))
                        $tmp.=$key.' : '.$value.', ';
                return $tmp;
            }
        }
    }
}

/**
 * Objet parents
 * @author Pascal Parois
 * @param g4p_parents Tableau contenant le détail des parents
 */
class g4p_parents
{
    /**
     * Constructeur de l'objet parent
     * @author Pascal Parois
     * @param g4p_parents Tableau contenant le détail des parents
     */
    function __construct($g4p_parents, &$g4p_sql_datas, $debug)
    {
        if($debug===true)
            $this->debug=true;
        else
            $this->debug=false;

        $this->famille_id=$g4p_parents['familles_id'];
        $this->rela_type=$g4p_parents['rela_type'];
        if(!empty($g4p_parents['familles_husb']))
        {
            $this->pere=new g4p_individu($g4p_parents['familles_husb']);
            $this->pere->set_filiation(false);
            $this->pere->set_debug($this->debug);
            $this->pere->g4p_etat_civil($g4p_sql_datas);
        }
        if(!empty($g4p_parents['familles_wife']))
        {
            $this->mere=new g4p_individu($g4p_parents['familles_wife']);
            $this->mere->set_filiation(false);
            $this->mere->set_debug($this->debug);
            $this->mere->g4p_etat_civil($g4p_sql_datas);
        }
    }
}

/**
 * Objet date
 * Vraiment pas terrible cette class
 * @author Pascal Parois
 * @param g4p_date Tableau contenant le détail de la date
 */
class g4p_date
{
    /**
     * Constructeur de l'objet date
     * @author Pascal Parois
     * @param g4p_date Tableau contenant le détail de la date
     */
    function __construct($g4p_date,&$g4p_sql_datas=NULL)
    {
        if(!empty($g4p_date))
        {
            $this->jd_count=$g4p_date['jd_count'];
            $this->jd_precision=$g4p_date['jd_precision'];
            $this->jd_calendar=$g4p_date['jd_calendar'];

            $this->gedcom_date=$g4p_date['events_details_gedcom_date'];
            $this->g4p_gedom2date();
        } else {
          $this->jd_count=0;
          $this->jd_precision=0;
          $this->jd_calendar=CAL_GREGORIAN;

          $this->gedcom_date='';
          $this->g4p_gedom2date();
        }
    }

    function set_gedcomdate($g4p_date)
    {
        $this->gedcom_date=$g4p_date;
    }

    /**
     * Objet date
     * @author Pascal Parois
     * @return boolean
     */
    function g4p_gedom2date()
    {
        $gedcom=$this->gedcom_date;

        //texte dans la date
        if(preg_match ('/^\(.*\)$/u',trim($gedcom),$reg))
        {
            if(!empty($reg[1]))
            {
                $this->phrase=$reg[1];
                return true;
            }
        }

        if(preg_match ('/^INT (.*)\(([^)]+)\)/u',trim($gedcom),$reg))
        {
            if(!empty($reg[1]))
            {
                $this->phrase=$reg[2];
                $gedcom=$reg[1];
            }
        }

        if(preg_match ('/(BET|FROM) (.*) (AND|TO) (.*)/u',$this->gedcom_date,$reg))
        {
            if(empty($reg[2]) or empty($reg[4]))
                return false;
            if($reg[1]=='BET' and $reg [3]!='AND')
                return false;

            if(!empty($reg[2]))
            {
                $date1=new g4p_date_value();
                $date1->load_from_ged($reg[1].' '.$reg[2]);
            }
            else
                $date1='';

            if(!empty($reg[4]))
            {
                $date2=new g4p_date_value();
                $date2->load_from_ged($reg[3].' '.$reg[4]);
            }
            else
                $date2='';

            //$this->date_range=new g4p_date_range();
            //$this->date_range->set_date($date1,$date2);
            $this->date1=$date1;
            $this->date2=$date2;
        }
        elseif(preg_match ('/(ABT|CAL|EST) (.*)/u',$this->gedcom_date,$reg))
        {
            if(!empty($reg[2]))
            {
                $date=new g4p_date_value();
                $date->load_from_ged($reg[1].' '.$reg[2]);

                //$this->date_approximated=new g4p_date_approximated();
                //$this->date_approximated->set_date($date);
                $this->date1=$date;
            }
            else
                return false;
        }
        else
        {
                $date=new g4p_date_value();
                $date->load_from_ged($this->gedcom_date);
                //$this->date_exact=$date;
                $this->date1=$date;
        }

            return true;
    }

    /**
     * Crée une chaine gedcom à partir d'un tableau
     * A faire et définir le fameux tableau
     * @author Pascal Parois
     * @return chaine gedcom
     */
    function g4p_date2gedom($table)
    {

        if(!empty($this->date_approximated))
            $gedcom=$this->date_approximated->get_gedcom();
        elseif(!empty($this->date_range))
            $gedcom=$this->date_range->get_gedcom();
        elseif(!empty($this->date_exact))
            $gedcom=$this->date_exact->get_gedcom();

        if(!empty($this->date_phrase))
            $phrase=$this->date_phrase->get_gedcom();

        if(!empty($gedcom) and !empty($phrase))
            return 'INT '.$gedcom.' '.$phrase;
        elseif(!empty($gedcom))
            return $gedcom;
        elseif(!empty($phrase))
            return $phrase;
        else
            return false;
    }

}

/************
    DATE_VALUE: = {Size=1:35}
    [<DATE> | <DATE_PERIOD> | <DATE_RANGE> <DATE_APPROXIMATED> | INT <DATE> (<DATE_PHRASE>) | (<DATE_PHRASE>) ]
    DATE: = {Size=4:35}
    [ <DATE_CALENDAR_ESCAPE> | <NULL>] <DATE_CALENDAR>
    period et range sont fusionné dans range
    DATE_PERIOD: = {Size=7:35}
    [ FROM <DATE> | TO <DATE> | FROM <DATE> TO <DATE> ]
    DATE_RANGE: = {Size=8:35}
    [ BEF <DATE> | AFT <DATE> | BET <DATE> AND <DATE> ]
    DATE_APPROXIMATED: = {Size=4:35}
    [ ABT <DATE> | CAL <DATE> | EST <DATE> ]
    DATE_CALENDAR: = {Size=4:35}
    [ <DATE_GREG> | <DATE_JULN> | <DATE_HEBR> | <DATE_FREN> | <DATE_FUTURE> ]
    DATE_CALENDAR_ESCAPE: = {Size=4:15}
    [ @#DHEBREW@ | @#DROMAN@ | @#DFRENCH R@ | @#DGREGORIAN@ | @#DJULIAN@ | @#DUNKNOWN@ ]
    DATE_FREN: = {Size=4:35}
    [ <YEAR> | <MONTH_FREN> <YEAR> | <DAY> <MONTH_FREN> <YEAR> ]
    DATE_GREG: = {Size=4:35}
    [ <YEAR_GREG> | <MONTH> <YEAR_GREG> | <DAY> <MONTH> <YEAR_GREG> ]
    DATE_HEBR: = {Size=4:35}
    [ <YEAR> | <MONTH_HEBR> <YEAR> | <DAY> <MONTH_HEBR> <YEAR> ]
    DATE_JULN: = {Size=4:35}
    [ <YEAR> | <MONTH> <YEAR> | <DAY> <MONTH> <YEAR> ]
    DATE_PHRASE: = {Size=1:35}
    (<TEXT>)
*****************/

class g4p_date_value
{
    function __construct()
    {
    }

    function set_date($day, $month, $year, $calendar='@#DGREGORIAN@')
    {
        global $g4p_cal_mrev;
        $fren_cal=array_flip($g4p_cal_mrev);
        $greg_cal=array_flip($g4p_cal_gregorien);
        $hebr_cal=array_flip($g4p_cal_mrev);

        if(!empty($year) and (int)$year==$year)
        {
            $this->year=$year;
            $this->calendar=$calendar;
            if(!empty($month))
            {
                switch($calendar)
                {
                    case '@#DGREGORIAN@':
                    case '@#DJULIAN@':
                    if(!isset($greg_cal[$month]))
                        return false;
                    break;
                    case '@#DFRENCH R@':
                    if(!isset($fren_cal[$month]))
                        return false;
                    break;
                    case '@#DHEBREW@':
                    if(!isset($hebr_cal[$month]))
                        return false;
                    break;
                }
                $this->year=$month;
                if(!empty($day) and (int)$day==$day and $day>0 and $day<32)
                    $this->day=$day;
            }
        }
        else
            return false;

    }

    /**
     * Extrait une date exacte
     * @author Pascal Parois
     * @param date Date GEDCOM à extraire
     * @return bool
     */
    function load_from_ged($date_ged)
    {
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

        $liste_modificateur=array('EST','ABT','CAL','TO','AFT','FROM','BET','TO','AND');
        $g4p_cal_ged_php=array('@#DHEBREW@'=>CAL_JEWISH,'@#DFRENCH R@'=>CAL_FRENCH,'@#DGREGORIAN@'=>CAL_GREGORIAN,'@#DJULIAN@'=>CAL_JULIAN,'@#UNKOWN@'=>CAL_GREGORIAN);

        if(preg_match('/^('.implode('|',$liste_modificateur).')/',trim($date_ged),$reg))
        {
            $this->mod=$reg[1];
            $date_ged=trim(str_replace($reg[1],'',$date_ged));
        }

        if(preg_match('/(@.*@) (.*)/',$date_ged,$reg))
        {
            if(isset($g4p_cal_ged_php[$reg[1]]))
            {
                $this->calendar=$reg[1];
            }
            $date_ged=str_replace($reg[1],'',$date_ged);
        }

        //on verifie si il y a une date négative, BC n'est pas une valeur acceptable mais peut être rencontré
        if(preg_match('/(B\.C\.|BC)/',$date_ged))
            $g4p_sign=-1;
        else
            $g4p_sign=1;

        if(preg_match('/([0-9]{1,2}) ([A-Z]{3,4}) ([0-9]{1,7})/',strtoupper($date_ged),$reg))
        {

            $this->day=$reg[1];
            $this->month=$reg[2];
            $this->year=$g4p_sign*intval($reg[3]);
            $this->jd_precision=3;
        }
        elseif(preg_match('/([A-Z]{3,4}) ([0-9]{1,7})/',strtoupper($date_ged),$reg))
        {
            $this->month=$reg[1];
            $this->year=$g4p_sign*intval($reg[2]);
            $this->jd_precision=2;
        }
        elseif(preg_match('/([0-9]{1,7})/',strtoupper($date_ged),$reg))
        {
            $this->year=$g4p_sign*intval($reg[1]);
            $this->jd_precision=1;
        }
        else
            return false;

        // Guess calendar using months name
        if(!empty($this->month) and empty($this->calendar) and !empty($this->year))
        {
            if(in_array(strtoupper($this->month),$g4p_cal_gregorien))
            {
                //assuming gregorian as per gedcom recomandation
                $this->calendar='@#DGREGORIAN@';
            }
            elseif(in_array(strtoupper($this->month),$g4p_cal_mrev))
            {
                $this->calendar='@#DFRENCH R@';
            }
            elseif(in_array(strtoupper($this->month),$g4p_cal_mhebreux))
            {
                $this->calendar='@#DHEBREW@';
            }
        }

        if(empty($this->calendar)) // still not set...
        {
            // using the year if present
            if(!empty($this->year))
            {
                if($this->year<3000)
                    $this->calendar='@#DGREGORIAN@';
                else
                    $this->calendar='@#DHEBREW@';
            }
        }

        if(!empty($this->calendar))
        {
            $chiffre_mois=array_merge(array_flip($g4p_cal_mrev),array_flip($g4p_cal_mhebreux),array_flip($g4p_cal_gregorien));
            if(!empty($this->month) and !empty($this->day) and !empty($this->year))
                $this->jd_count=cal_to_jd($g4p_cal_ged_php[$this->calendar], $chiffre_mois[$this->month],$this->day,$this->year);
            elseif(!empty($this->month) and !empty($this->year))
                $this->jd_count=cal_to_jd($g4p_cal_ged_php[$this->calendar], $chiffre_mois[$this->month],1,$this->year);
            elseif(!empty($this->year))
                $this->jd_count=cal_to_jd($g4p_cal_ged_php[$this->calendar], 1,1,$this->year);
        }
        return true;
    }

    function get_gedcom()
    {
        $gedcom='';
        if(!empty($this->date_approximated->date->calendar))
            $gedcom.=$this->date_approximated->date->calendar;
        if(!empty($this->date_approximated->date->day))
            $gedcom.=' '.$this->date_approximated->date->day;
        if(!empty($this->date_approximated->date->month))
            $gedcom.=' '.$this->date_approximated->date->month;
        if(!empty($this->date_approximated->date->year))
            $gedcom.=' '.$this->date_approximated->date->year;

        return trim($gedcom);
    }

}


//////////////////////////////
//Permissions
//////////////////////////////

class g4p_permission
{
    function __construct()
    {
        global $g4p_permissions;

        foreach($g4p_permissions as $g4p_a_perm)
            $this->permission[$g4p_a_perm['id']]=$g4p_a_perm['default'];
    }

    function g4p_load_permission($g4p_id_membre)
    {
        global $g4p_mysqli;

        // Permissions générales
        $sql="SELECT permission_type, permission_value FROM genea_permissions WHERE membre_id=$g4p_id_membre AND base=0";
        if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
            if($g4p_permission=$g4p_mysqli->g4p_result($g4p_result_req))
                foreach($g4p_permission as $g4p_a_permission)
                {
                    if(is_null($g4p_a_permission['permission_value']))
                        $this->permission[$g4p_a_permission['permission_type']]='*';
                    else
                        $this->permission[$g4p_a_permission['permission_type']]=$g4p_a_permission['permission_value'];
                }
        if(isset($_SESSION['genea_db_id']))
        {
            $sql="SELECT permission_type, permission_value FROM genea_permissions WHERE membre_id=$g4p_id_membre AND base='".$_SESSION['genea_db_id']."'";
            if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
                if($g4p_permission=$g4p_mysqli->g4p_result($g4p_result_req))
                    foreach($g4p_permission as $g4p_a_permission)
                    {
                        if(is_null($g4p_a_permission['permission_value']))
                            $this->permission[$g4p_a_permission['permission_type']]='*';
                        else
                            $this->permission[$g4p_a_permission['permission_type']]=$g4p_a_permission['permission_value'];
                    }
        }

        //echo '<pre>';
        //print_r($this->permission);

    }
}


class g4p_mysqli extends mysqli
{
    function __construct()
    {
        global $g4p_config;
        $this->init();
        $this->nb_requetes=0;

        $this->real_connect($g4p_config['g4p_db_host'],$g4p_config['g4p_db_login'],$g4p_config['g4p_db_mdp'],$g4p_config['g4p_db_base']);

        if (mysqli_connect_errno())
        {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }

        /* change character set to utf8 */
        if (!$this->set_charset("utf8"))
            printf("Error loading character set utf8: %s\n", $this->error);
        //else
            //printf("Current character set: %s\n", $this->character_set_name());
    }


    function g4p_query($g4p_db_request)
    {
        global $g4p_config, $g4p_chemin, $g4p_db_connect,$sql_count, $g4p_langue;
        try {
          $result = parent::query($g4p_db_request);
        }
        catch (exception $e) {
	  error_log($e->getMessage().": ".$g4p_db_request);
          throw $e;
	}
        $this->nb_requetes++;
        $this->requetes[]=$g4p_db_request;

        if(mysqli_error($this))
        {
            if(mysqli_errno($this)==1062)
                return 'Error:1062';
            else
            {
                echo $g4p_db_request;
                throw new exception(mysqli_error($this), mysqli_errno($this));
            }
        }
        return $result;
    }

    function g4p_result($result, $cle=0, $uniq=true)
    {
        $table=array();

        while($ligne=$result->fetch_assoc())
        {
            if($cle===0)
            {
                $table[]=$ligne;
            }
            else
            {
                if($uniq==false) {
                    // cle non unique: on ajoute un niveau pour mettre la ou les valeurs de la meme cle
                    $table[$ligne[$cle]][]=$ligne;
                } else {
                    $table[$ligne[$cle]]=$ligne;
                }
            }
        }
        $result->free();

        return (count($table))?($table):(false);
    }

    function escape_string($string)
    {
        return parent::real_escape_string($string);
    }

}



?>
