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
 *          Page à tout faire                                              *
 *                                                                         *
 * dernière mise à jour : novembre 2004                                    *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

if(!file_exists($g4p_chemin.'p_conf/g4p_config.php'))
{
  header('location:'.$g4p_chemin.'install.php');
  exit;
}

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
if(!isset($_GET['g4p_action']))
  $_GET['g4p_action']='accueil';

switch($_GET['g4p_action'])
{
//////////////////////////////////////
// Fiche individuelle
////////////////////////////////////////
  case 'fiche_indi' :
  // On économise le serveur, pas la peine de renvoyer la page si celle en cache dans le navigateur est la bonne
  if(isset($_GET['id_pers']))
  {
    header('Date: ' . gmdate("D, d M Y H:i:s") . ' GMT');
    header('Cache-Control: Public, must-revalidate');
    header('Pragma: public');
    header("ETag: ".md5($_SESSION['g4p_id_membre'].$_SESSION['langue'].$_SESSION['theme'].serialize($_SESSION['place']).$g4p_langue['entete_charset']));
    if(file_exists($g4p_chemin.'cache/'.$_SESSION['genea_db_nom'].'/indi_'.$_GET['id_pers'].'.txt'))
    {
      // date de dernière modif
      $modif = gmdate('D, d M Y H:i:s', filemtime($g4p_chemin.'cache/'.$_SESSION['genea_db_nom'].'/indi_'.$_GET['id_pers'].'.txt')) ;
      header("Last-Modified: $modif GMT");
      // on vérifie si le contenu a changé
      if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) and (strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'],0,29)))>=gmdate('U', filemtime($g4p_chemin.'cache/'.$_SESSION['genea_db_nom'].'/indi_'.$_GET['id_pers'].'.txt')))
      {
        if(isset($_SERVER['HTTP_IF_NONE_MATCH']) and $_SERVER['HTTP_IF_NONE_MATCH']==md5($_SESSION['g4p_id_membre'].$_SESSION['langue'].$_SESSION['theme'].serialize($_SESSION['place']).$g4p_langue['entete_charset']))
        {
          $g4p_indi=g4p_load_indi_infos((int)$_GET['id_pers']);
          //header('Not Modified', TRUE, 304);
          //exit;
        }
      }
    }
  }

$g4p_indi=g4p_load_indi_infos((int)$_GET['id_pers']);

    if(isset($_GET['id_pers']) and isset($g4p_indi) and $_GET['id_pers']!=$g4p_indi->indi_id)
      $g4p_indi=g4p_load_indi_infos((int)$_GET['id_pers']);
    elseif(isset($_GET['id_pers']) and !isset($g4p_indi))
      $g4p_indi=g4p_load_indi_infos((int)$_GET['id_pers']);
    elseif(!isset($g4p_indi))
    {
      require_once($g4p_chemin.'entete.php');
      echo '<div class="cadre">',$g4p_langue['index_person_inconnu'],'</div>';
      require_once($g4p_chemin.'pied_de_page.php');
      exit;
    }
    // on enregistre la date de la page pour pouvoir vérifier ultérieurement si les données sont à jour
    $modif = gmdate('D, d M Y H:i:s', @filemtime($g4p_chemin.'cache/'.$_SESSION['genea_db_nom'].'/indi_'.$g4p_indi->indi_id.'.txt')) ;
    header("Last-Modified: $modif GMT");

    if($g4p_indi->indi_id==0)
    {
      require_once($g4p_chemin.'entete.php');
      echo '<div class="cadre">',$g4p_langue['index_person_inconnu'],'</div>';
      require_once($g4p_chemin.'pied_de_page.php');
      exit;
    }
    else
    {
      if($_SESSION['permission']->permission[_PERM_MASK_INDI_] and $g4p_indi->resn=='privacy')
      {
        require_once($g4p_chemin.'entete.php');
        echo '<div class="cadre">',$g4p_langue['acces_non_autorise'],'</div>';
        require_once($g4p_chemin.'pied_de_page.php');
        exit;
      }

      g4p_add_intohistoric($g4p_indi->indi_id,'indi');

      $g4p_titre_page=$g4p_indi->nom.' '.$g4p_indi->prenom;

      require_once($g4p_chemin.'entete.php');
      echo '<div class="cadre">';
      if ($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
      {
        echo '<div class="menu_interne">
          <a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_note&amp;g4p_id='.$g4p_indi->indi_id.'&amp;g4p_type=indi',0),'" class="admin">',$g4p_langue['menu_ajout_note'],'</a> -
          <a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_source&amp;g4p_id='.$g4p_indi->indi_id.'&amp;g4p_type=indi',0),'" class="admin">',$g4p_langue['menu_ajout_source'],'</a> -
          <a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_media&amp;g4p_id='.$g4p_indi->indi_id.'&amp;g4p_type=indi',0),'" class="admin">',$g4p_langue['menu_ajout_media'],'</a> -
          <a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_asso&amp;g4p_lien='.$g4p_indi->indi_id.'&amp;g4p_type=indi',0),'" class="admin">',$g4p_langue['menu_ajout_relation'],'</a> - ';
      }
      if($_SESSION['permission']->permission[_PERM_SUPPR_FILES_])
        echo '<a href="',g4p_make_url('admin','exec.php','g4p_opt=suppr_fiche',0),'" class="admin" onclick=" return confirme(this, \'',$g4p_langue['menu_sppr_confirme'],'\')">',$g4p_langue['menu_sppr_indi'],'</a> - ';
      if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
      {
        echo '<a href="',g4p_make_url('admin','index.php','g4p_opt=mod_fich',0),'" class="admin">',$g4p_langue['menu_modif_fiche'],'</a> -';
        if($g4p_indi->resn!='privacy')
          echo ' <a href="',g4p_make_url('admin','exec.php','g4p_opt=mask_fich&amp;g4p_id='.$g4p_indi->indi_id,0),'" class="admin">',$g4p_langue['menu_masque_fiche'],'</a> -';
        else
          echo ' <a href="',g4p_make_url('admin','exec.php','g4p_opt=demask_fich&amp;g4p_id='.$g4p_indi->indi_id,0),'" class="admin">',$g4p_langue['menu_demasque_fiche'],'</a> -';
        echo ' <a href="',g4p_make_url('admin','exec.php','g4p_opt=del_cache',0),'" class="admin">',$g4p_langue['menu_recreer_cache'],'</a> <a href="g4p_object.php?id='.$g4p_indi->indi_id.'" target="_blank">G4p_object</a></div>';
      }

      if($g4p_indi->resn=='privacy')
        echo'<span class="petit">',$g4p_langue['index_masquer'],'</span><br />';

      if($g4p_indi->timestamp!='0000-00-00 00:00:00')
        echo '<span class="petit">',sprintf($g4p_langue['index_chan'],g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_indi->timestamp))),'</span><br />';
                
      echo '<div class="etat_civil">';
      echo '<span><em>Id : </em>',number_format($g4p_indi->indi_id, 0, ',', ' '),'<br />';
      echo '<em>',$g4p_langue['index_nom'],'</em>',$g4p_indi->nom,'<br />';
      echo '<em>',$g4p_langue['index_prenom'],'</em>',$g4p_indi->prenom,'</span><br />';
      echo '<em>',$g4p_langue['index_sexe'],'</em>',$g4p_langue['index_sexe_valeur'][$g4p_indi->sexe],'<br />';
      if($g4p_indi->npfx)
        echo '<em>',$g4p_langue['index_npfx'],'</em>',$g4p_indi->npfx,'<br />';
      if($g4p_indi->givn)
        echo '<em>',$g4p_langue['index_givn'],'</em>',$g4p_indi->givn,'<br />';
      if($g4p_indi->nick)
        echo '<em>',$g4p_langue['index_nick'],'</em>',$g4p_indi->nick,'<br />';
      if($g4p_indi->spfx)
        echo '<em>',$g4p_langue['index_spfx'],'</em>',$g4p_indi->spfx,'<br />';
      if($g4p_indi->nsfx)
        echo '<em>',$g4p_langue['index_nsfx'],'</em>',$g4p_indi->nsfx,'<br />';
      echo '</div>';
 
      if(!empty($g4p_indi->alias))
        g4p_show_alias($g4p_indi->alias);

      //evenements individuels
      if(isset($g4p_indi->events) and count($g4p_indi->events)>0)
      {
        echo '<div class="evenements">';
        //$g4p_indi->events=array_column_sort($g4p_indi->events,'jd_count');
        foreach($g4p_indi->events as $g4p_a_ievents)
        {
          if($g4p_a_ievents->details_descriptor)
            $g4p_tmp=' ('.$g4p_a_ievents->details_descriptor.')';
                else
                $g4p_tmp='';
                echo
                '<em>',$g4p_tag_def[$g4p_a_ievents->tag],$g4p_tmp,' :
                </em><span class="date">',g4p_date($g4p_a_ievents->gedcom_date),'</span>', ($g4p_a_ievents->place->g4p_formated_place($_SESSION['place']))?(' ('.$g4p_langue['index_lieu'].$g4p_a_ievents->place->g4p_formated_place($_SESSION['place']).') '):('');
                echo (isset($g4p_a_ievents->sources))?(' <span style="color:blue; font-size:x-small;">-S-</span> '):('');
                echo (isset($g4p_a_ievents->notes))?(' <span style="color:blue; font-size:x-small;">-N-</span> '):('');
                echo (isset($g4p_a_ievents->medias))?(' <span style="color:blue; font-size:x-small;">-M-</span> '):('');
                echo (isset($g4p_a_ievents->asso))?(' <span style="color:blue; font-size:x-small;">-T-</span> '):('');
                echo (isset($g4p_a_ievents->id))?(' <a href="'.g4p_make_url('','detail_event.php','g4p_id=i|'.$g4p_indi->indi_id.'|'.$g4p_a_ievents->id,0).'" class="noprint">'.$g4p_langue['detail'].'</a><br />'):('<br />');
                }
                echo '</div>';
                }

                g4p_affiche_mariage();

                // les parents
                if(isset($g4p_indi->parents))
                {
                echo '<div class="parents">';
                foreach($g4p_indi->parents as $g4p_a_parent)
                {
                echo '<em>',$g4p_langue['index_ype_parent'],'</em>',str_replace(array_keys($g4p_lien_def),array_values($g4p_lien_def),$g4p_a_parent->rela_type);
                if(isset($g4p_a_parent->pere))
                {
                  echo '<br /><a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&amp;id_pers='.$g4p_a_parent->pere->indi_id.'&genea_db_id='.$g4p_a_parent->pere->base,'fiche-'.$g4p_a_parent->pere->base.'-'.g4p_prepare_varurl($g4p_a_parent->pere->nom).'-'.g4p_prepare_varurl($g4p_a_parent->pere->prenom).'-'.$g4p_a_parent->pere->indi_id),'">',
                       $g4p_a_parent->pere->nom,'
                       ',$g4p_a_parent->pere->prenom,'</a> <span class="petit">',g4p_date($g4p_a_parent->pere->date_rapide()),'</span>';
                }
                else
                  echo '<br />',$g4p_langue['index_parent_inconnu'];

                if(isset($g4p_a_parent->mere))
                {
                  echo '<br /><a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&amp;id_pers='.$g4p_a_parent->mere->indi_id.'&amp;genea_db_id='.$g4p_a_parent->mere->base,'fiche-'.$g4p_a_parent->mere->base.'-'.g4p_prepare_varurl($g4p_a_parent->mere->nom).'-'.g4p_prepare_varurl($g4p_a_parent->mere->prenom).'-'.$g4p_a_parent->mere->indi_id),'">',
                       $g4p_a_parent->mere->nom,' ',$g4p_a_parent->mere->prenom,'</a> <span class="petit">',g4p_date($g4p_a_parent->mere->date_rapide()),'</span>';
                }
                else
                  echo '<br />',$g4p_langue['index_parent_inconnu'];
                echo '<br />';
                }
            echo '</div>';
                }

          if($g4p_config['show_ext_rela'])
            g4p_relations_avancees($g4p_indi->indi_id);

          g4p_affiche_asso(@$g4p_indi->asso, $g4p_indi->indi_id,'indi');
          g4p_affiche_event_temoins(@$g4p_indi->temoins['events']);
          
          if ($_SESSION['permission']->permission[_PERM_NOTE_])
            g4p_affiche_notes(@$g4p_indi->notes, $g4p_indi->indi_id,'indi');

          if ($_SESSION['permission']->permission[_PERM_SOURCE_])
            g4p_affiche_sources(@$g4p_indi->sources, $g4p_indi->indi_id,'indi');

          if ($_SESSION['permission']->permission[_PERM_MULTIMEDIA_])
            g4p_affiche_multimedia(@$g4p_indi->multimedia, $g4p_indi->indi_id,'indi');

          echo '</div>';
    }
    break;

    //////////////////////////////////////
    // les patronymes
    ////////////////////////////////////////
  case 'liste_patronyme':
    require_once($g4p_chemin.'entete.php');
    if($_SESSION['permission']->permission[_PERM_MASK_DATABASE_])
    {
      echo $g4p_langue['liste_patro_acces_interdis'];
      require_once($g4p_chemin.'pied_de_page.php');
      exit;
    }
    
    echo '<div class="cadre" style="text-align:center;">';
    if(!isset($_GET['patronyme']) or $_GET['patronyme']=='all')
    {
      if(isset($_SESSION['patronyme']))
        unset($_SESSION['patronyme']);
      g4p_affiche_liste_nom(0,'');
    }
    else
    {
      //parsing du nom
      $_GET['patronyme']=str_replace('!1!','?',$_GET['patronyme']);
      $_GET['patronyme']=str_replace('!2!','-',$_GET['patronyme']);
      $_GET['patronyme']=str_replace('!3!','&',$_GET['patronyme']);
    
      $g4p_count_patro=count($_SESSION['patronyme']);
      if(strlen($_GET['patronyme'])<$g4p_count_patro)
      {
        $i=strlen($_GET['patronyme'])+1;
        while(isset($_SESSION['patronyme'][$i]))
        {
          unset($_SESSION['patronyme'][$i]);
          $i++;
        }
      }
      g4p_affiche_liste_nom($_GET['patronyme'],$_GET['g4p_cpt']);
    }
    echo '</div>';
    echo '<center><a style="font-size:xx-small;padding-top:0:margin-top:0;" href="'.g4p_make_url('','liste_patronymes.php','',0).'">Liste complète de tous les patronymes, toutes bases confondues</a></center>';
    break;

    //////////////////////////////////////
    // les prenoms
    ////////////////////////////////////////
    /**
     * Modifié par SYL: S. Coutable
     * Remodifié par Pascal 28/05/2005
     **/
    case 'liste_prenom':
    if(!isset($_GET['g4p_page']))
      $_GET['g4p_page']=1;   
    
    if(!isset($_GET['nom']))
    	$_GET['nom'] = $_GET['id_nom'];
      
    $_GET['nom']=str_replace('!1!','?',$_GET['nom']);
    $_GET['nom']=str_replace('!2!','-',$_GET['nom']);
    $_GET['nom']=str_replace('!3!','&',$_GET['nom']);
      

    $sql="SELECT indi_id, indi_nom, indi_prenom, indi_sexe, indi_resn FROM `genea_individuals`";
    $sql.=" WHERE base =".$_SESSION['genea_db_id']." AND indi_nom='".mysql_escape_string($_GET['nom'])."'";
    if($_SESSION['permission']->permission[_PERM_MASK_INDI_])
      $sql.=" AND (indi_resn IS NULL OR indi_resn<>'privacy')";
    $sql.=" ORDER BY indi_nom, indi_prenom LIMIT ".($_GET['g4p_page']-1)*_AFF_NBRE_PRENOM_.","._AFF_NBRE_PRENOM_;
    //echo $sql;
    $g4p_result=$g4p_mysqli->g4p_query($sql);
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    {
      foreach($g4p_result as $g4p_a_result)
      {
        $liste_indi[]=$g4p_a_result['indi_id'];
        if($g4p_a_result['indi_sexe']=='M')
          $homme[]=$g4p_a_result['indi_id'];
        else
          $femme[]=$g4p_a_result['indi_id'];      
      }
    
      if(!empty($homme))
        $homme=implode(',',$homme);
      else
        $homme='';
      if(!empty($femme))
        $femme=implode(',',$femme);
      else
        $femme='';
      if(!empty($liste_indi))
        $liste_indi=implode(',',$liste_indi);
      else
        $liste_indi='';
      
      if($homme!='')
      {
        $sql="SELECT CONCAT('i',familles_husb) as familles_husb, indi_id, indi_nom, indi_prenom, indi_resn FROM genea_familles LEFT JOIN genea_individuals ON (familles_wife=indi_id) WHERE familles_husb IN (".$homme.") AND indi_id IS NOT NULL";
        $g4p_result_req=$g4p_mysqli->g4p_query($sql);
        $g4p_femme=$g4p_mysqli->g4p_result($g4p_result_req,'familles_husb',false);
      }
      else
        $g4p_femme=array();
  
      if($femme!='')
      {
        $sql="SELECT CONCAT('i',familles_wife) as familles_wife, indi_id, indi_nom, indi_prenom, indi_resn FROM genea_familles LEFT JOIN genea_individuals ON (familles_husb=indi_id) WHERE familles_wife IN (".$femme.") AND indi_id IS NOT NULL";
        $g4p_result_req=$g4p_mysqli->g4p_query($sql);
        $g4p_mari=$g4p_mysqli->g4p_result($g4p_result_req,'familles_wife',false);
      }
      else
        $g4p_mari=array();
  
      if(is_array($g4p_femme) and is_array($g4p_mari))
        $g4p_conjoints=array_merge($g4p_femme,$g4p_mari);
      elseif(is_array($g4p_femme))
        $g4p_conjoints=$g4p_femme;
      elseif(is_array($g4p_mari))
        $g4p_conjoints=$g4p_mari;
      else
        $g4p_conjoints=array();
      
      //echo '<pre>';
      //print_r($g4p_conjoints);
      
      if(!empty($g4p_conjoints))
      {
        foreach($g4p_conjoints as $a_indi)
          $liste_indi2[]=$a_indi[0]['indi_id'];
        $liste_indi2=implode(',',$liste_indi2).','.$liste_indi;
      }
      else
      {
        $liste_indi2=$liste_indi;
      }

      // evenement naissance

      $sql="SELECT genea_individuals.indi_id AS indi_id, events_details_gedcom_date AS naissance
        FROM `genea_individuals`
        JOIN rel_indi_events AS a ON ( genea_individuals.indi_id=a.indi_id )
        JOIN genea_events_details AS n ON ( a.events_details_id = n.events_details_id)
        WHERE genea_individuals.indi_id IN (".$liste_indi2.") AND (a.events_tag = 'BIRT' OR a.events_tag = 'BAPM' )";
      $g4p_result_req=$g4p_mysqli->g4p_query($sql);
      $g4p_naissance=$g4p_mysqli->g4p_result($g4p_result_req,'indi_id',false);
      //echo $sql.':nb='.count($g4p_naissance).':t='.intval($time_query*1000).' ms<br/>';
  
      // evenement deces
      $sql="SELECT genea_individuals.indi_id AS indi_id, events_details_gedcom_date AS deces
        FROM `genea_individuals`
        JOIN rel_indi_events AS a ON ( genea_individuals.indi_id=a.indi_id )
        JOIN genea_events_details AS n ON ( a.events_details_id = n.events_details_id)
        WHERE genea_individuals.indi_id IN (".$liste_indi2.") AND (a.events_tag = 'DEAT' OR a.events_tag = 'BURI' )";
      $g4p_result_req=$g4p_mysqli->g4p_query($sql);
      $g4p_deces=$g4p_mysqli->g4p_result($g4p_result_req,'indi_id',false);
  
  
      $g4p_titre_page='Patronyme : '.$g4p_result[0]['indi_nom'];
      require_once($g4p_chemin.'entete.php');
      
      $g4p_page=ceil($_GET['g4p_nbre']/_AFF_NBRE_PRENOM_); 
      echo '<div class="page_prenom">';
      echo "<a href='javascript:history.back()' class=\"retour\">",$g4p_langue['retour'],"</a>&nbsp;";
      echo $_GET['g4p_nbre'],'&nbsp;',$g4p_langue['personnes_trouvees'];
    
      if($_GET['g4p_nbre']>_AFF_NBRE_PRENOM_)	
      {
        echo ', page n°',$_GET['g4p_page'],'&nbsp;';$g4p_langue['index_prenoms_page'];
        echo ' (',_AFF_NBRE_PRENOM_,'&nbsp;',$g4p_langue['personnes_page'],')';
        echo '<br />- ';
  
        for($i=1;$i<=$g4p_page;$i++)
        {
          if($_GET['g4p_page']==$i)
            echo $i,' - ';
          else
            echo '<a href="',g4p_make_url('','index.php','g4p_action=liste_prenom&genea_db_id='.$_SESSION['genea_db_id'].'&g4p_nbre='.$_GET['g4p_nbre'].'&g4p_page='.$i.'&nom='.$_GET['nom'],'prenoms-'.$_SESSION['genea_db_id'].'-'.$_GET['nom'].'-'.$_GET['g4p_nbre'].'-'.$i),'"> ',$i,' </a>-';
        }
      }
 
      //echo $sql.':nb='.count($g4p_deces).':t='.intval($time_query*1000).' ms<br/>';
      echo '</div>';
      echo '<div class="liste_prenoms">';
      
      $tit_conjoints=$tit_conjoint=' <img src="'.$g4p_chemin.'images/mariage.png" alt="mariage" class="icone_mar" /> '; //$g4p_langue['conjoint'];
      $tit_celibataire=$g4p_langue['celibataire'];
  
      foreach($g4p_result as $a_g4p_result)
      {
        $tmp_naissance=$tmp_deces='';
        if(!$_SESSION['permission']->permission[_PERM_MASK_INDI_] or $a_g4p_result['indi_resn']!='privacy')
        {
          if(!empty($g4p_naissance[$a_g4p_result['indi_id']]))
          {
            foreach($g4p_naissance[$a_g4p_result['indi_id']] as $tmp)
            {
              if($tmp['naissance']!='');
              {
                $tmp_naissance=$tmp['naissance'];
                break;
              }
            }            
          }
          if(!empty($tmp_naissance))
            $g4p_naissance_tmp='(°'.g4p_date($tmp_naissance);
          else
            $g4p_naissance_tmp='(';
            
          if(!empty($g4p_deces[$a_g4p_result['indi_id']]))
          {
            foreach($g4p_deces[$a_g4p_result['indi_id']] as $tmp)
            {
              if($tmp['deces']!='');
              {
                $tmp_deces=$tmp['deces'];
                break;
              }
            }            
          }
          if(!empty($tmp_deces))
            $g4p_deces_tmp='+'.g4p_date($tmp_deces).')';
          else
            $g4p_deces_tmp=')';
  
          if($g4p_naissance_tmp=='(' and $g4p_deces_tmp==')')
            $g4p_texte='';
          elseif($g4p_naissance_tmp!='(' and $g4p_deces_tmp!=')')
            $g4p_texte=' <span class="petit">'.$g4p_naissance_tmp.' - '.$g4p_deces_tmp.'</span>';
          else
            $g4p_texte=' <span class="petit">'.$g4p_naissance_tmp.$g4p_deces_tmp.'</span>';
  
          echo '<img src="'.$g4p_chemin.'images/'.$a_g4p_result['indi_sexe'].'.png" alt="'
            .$a_g4p_result['indi_sexe'].'" class="icone_sexe" /> <a class="liste" href="'
            ,g4p_make_url('','fiche_individuelle.php','id_pers='.$a_g4p_result['indi_id'],'fiche-'.$_SESSION['genea_db_id'].'-'.g4p_prepare_varurl($a_g4p_result['indi_nom']).'-'.g4p_prepare_varurl($a_g4p_result['indi_prenom']).'-'.$a_g4p_result['indi_id']),'">',$a_g4p_result['indi_nom'],' ',$a_g4p_result['indi_prenom'],'</a>',$g4p_texte;
                //echo ' t=',intval($time_query*1000),' tt=',intval($t_q*1000);
                
          if(!empty($g4p_conjoints['i'.$a_g4p_result['indi_id']]))
          {
            $nb=count($g4p_conjoints['i'.$a_g4p_result['indi_id']]);
            if($nb>1) 
              echo $tit_conjoints;
            else
              echo $tit_conjoint;
            $cnt=1;
            foreach($g4p_conjoints['i'.$a_g4p_result['indi_id']] as $a_conjoint)
            {
              echo $a_conjoint['indi_nom'].' '.$a_conjoint['indi_prenom'];

              $tmp_naissance=$tmp_deces='';
              if(!$_SESSION['permission']->permission[_PERM_MASK_INDI_] or $a_conjoint['indi_resn']!='privacy')
              {
                if(!empty($g4p_naissance[$a_conjoint['indi_id']]))
                {
                  foreach($g4p_naissance[$a_conjoint['indi_id']] as $tmp)
                  {
                    if($tmp['naissance']!='');
                    {
                      $tmp_naissance=$tmp['naissance'];
                      break;
                    }
                  }            
                }
                if(!empty($tmp_naissance))
                  $g4p_naissance_tmp='(°'.g4p_date($tmp_naissance);
                else
                  $g4p_naissance_tmp='(';
                  
                if(!empty($g4p_deces[$a_conjoint['indi_id']]))
                {
                  foreach($g4p_deces[$a_conjoint['indi_id']] as $tmp)
                  {
                    if($tmp['deces']!='');
                    {
                      $tmp_deces=$tmp['deces'];
                      break;
                    }
                  }            
                }
                if(!empty($tmp_deces))
                  $g4p_deces_tmp='+'.g4p_date($tmp_deces).')';
                else
                  $g4p_deces_tmp=')';
        
                if($g4p_naissance_tmp=='(' and $g4p_deces_tmp==')')
                  $g4p_texte='';
                elseif($g4p_naissance_tmp!='(' and $g4p_deces_tmp!=')')
                  $g4p_texte=' <span class="petit">'.$g4p_naissance_tmp.' - '.$g4p_deces_tmp.'</span>';
                else
                  $g4p_texte=' <span class="petit">'.$g4p_naissance_tmp.$g4p_deces_tmp.'</span>';
  
                echo $g4p_texte;
              }
              if($cnt++<$nb)
                echo ' - ';		
            }
          }
          echo '<br />';
        }
      }
    }
    else
    {
      require_once($g4p_chemin.'entete.php');
      echo '<h2>Aucune personnes trouvée</h2>';
    }
    //$t = g4p_getmicrotime()-$t_start;
    //echo 'total affichage(foreach)=',intval($t*1000),' ms<br/>';
    echo '</div>';

    echo '<div class="page_prenom">';
    echo "<a href='javascript:history.back()' class=\"retour\">",$g4p_langue['retour'],"</a>&nbsp;";
    echo $_GET['g4p_nbre'],'&nbsp;',$g4p_langue['personnes_trouvees'];
    if($_GET['g4p_nbre']>_AFF_NBRE_PRENOM_)	
    {
      echo ', page n°',$_GET['g4p_page'],'&nbsp;';$g4p_langue['index_prenoms_page'];
      echo ' (',_AFF_NBRE_PRENOM_,'&nbsp;',$g4p_langue['personnes_page'],')';
      echo '<br />- ';
  
      for($i=1;$i<=$g4p_page;$i++)
      {
        if($_GET['g4p_page']==$i)
          echo $i,' - ';
        else
          echo '<a href="',g4p_make_url('','index.php','g4p_action=liste_prenom&genea_db_id='.$_SESSION['genea_db_id'].'&g4p_nbre='.$_GET['g4p_nbre'].'&g4p_page='.$i.'&nom='.$_GET['nom'],'prenoms-'.$_SESSION['genea_db_id'].'-'.$_GET['nom'].'-'.$_GET['g4p_nbre'].'-'.$i),'"> ',$i,' </a>-';
      }
    }

    echo '</div>';
    break;

    //////////////////////////////////////
    // Accueil
    ////////////////////////////////////////
    default :
    require_once($g4p_chemin.'entete.php');
    require_once('include_html/accueil.php');
    break;

}

require_once($g4p_chemin.'pied_de_page.php');
?>
