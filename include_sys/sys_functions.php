<?php

function g4p_permission()
{   
  if(!isset($_SESSION['g4p_permission']))
  {
    unset($_SESSION);
    @session_destroy();
    exit('ERREUR!!');
  }
  if(isset($_SESSION['g4p_permission'][$_SESSION['genea_db_id']]))
    return $_SESSION['g4p_permission'][$_SESSION['genea_db_id']];
  else
    return $_SESSION['g4p_permission']['*'];
}


function g4p_utf8_decode($var)
{
  if(is_array($var))
  {
    foreach($var as $key=>$value)
      $var_iso[$key]=g4p_utf8_decode($value);
    return $var_iso;
  }
  else
    return utf8_decode($var);
}

function g4p_clean_xhtml_arrays ( $string )
{
  if (is_array($string))
  {
    return array_map('g4p_clean_xhtml_arrays', $string);;
  }
  return preg_replace('/&(?!(#*x*[0-9]{1,4}|[a-zA-Z]{1,7});)/','&amp;',$string);
  //str_replace("&", "&amp;", $string);
}

function g4p_db_numrows($g4p_result)//OK
{
  global $g4p_langue;
  if (empty($g4p_result))
    return false;
  else
    return mysql_num_rows($g4p_result);
}

function g4p_date($g4p_date, $date='date1', $cache=0)
{
    global $g4p_date_modif,$g4p_langue, $date1;
    static $date1keys, $date1values;
    //date1 affichage long (12 janvier 2005...)
    //date2 conversion jj/mm/aaaa
    //date3 conversion 12 JAN 2005
    //date4 comme date2 plus suppression du texte
    $date4=$date2=array('` JAN `'=>'/01/', '` FEB `'=>'/02/', '` MAR `'=>'/03/', '` APR `'=>'/04/', '` MAY `'=>'/05/', '` JUN `'=>'/06/', '` JUL `'=>'/07/', '` AUG `'=>'/08/', '` SEP `'=>'/09/', '` OCT `'=>'/10/', '` NOV `'=>'/11/', '` DEC `'=>'/12/');
    $date3=array('`/01/`'=>' JAN ', '`/02/`'=>' FEB ', '`/03/`'=>' MAR ', '`/04/`'=>' APR ', '`/05/`'=>' MAY ', '`/06/`'=>' JUN ', '`/07/`'=>' JUL ', '`/08/`'=>' AUG ',
        '`/09/`'=>' SEP ', '`/10/`'=>' OCT ', '`/11/`'=>' NOV ', '`/12/`'=>' DEC ');

    $g4p_date=preg_replace ('/(@#DHEBREW@|@#DROMAN@|@#DFRENCH R@|@#DGREGORIAN@|@#DJULIAN@|@#DUNKNOWN@)/','',$g4p_date);

    if(empty($date1keys))
        $date1keys=array_keys($$date);
    if(empty($date1values))
        $date1values=array_values($$date);
    $g4p_date=preg_replace ($date1keys,$date1values,$g4p_date);
    if($date=='date4')
        $g4p_date=preg_replace(array_keys($g4p_date_modif),'',$g4p_date);
    elseif($date!='date3')
        $g4p_date=preg_replace(array_keys($g4p_date_modif),array_values($g4p_date_modif), $g4p_date);

    //$g4p_date=mb_ereg_replace (' ','&nbsp;',$g4p_date);
    $g4p_date=''.$g4p_date.'';
    //supression des données récentes
    if($_SESSION['permission']->permission[_PERM_AFF_DATE_]==0 and $cache==0 and preg_match_all("/[0-9]{4}/",$g4p_date, $match))
    {
        foreach($match[0] as $g4p_a_year)
        {
            if(intval($g4p_a_year)>date('Y')-100)
                $g4p_date=$g4p_langue['date_cache'];
        }
    }
    return(trim($g4p_date));
}

function g4p_affiche_notes($notes,$lien,$type)
{
    global $g4p_chemin, $g4p_langue;
    if($_SESSION['permission']->permission[_PERM_NOTE_] and is_array($notes))
    {
        //echo '<div class="notes">';
        foreach($notes as $g4p_a_note)
        {
            if(isset($_SESSION['permission']) and ($_SESSION['permission']->permission[_PERM_EDIT_FILES_]))
            {
                echo '<div class="menu_interne"><a href="'.g4p_make_url('admin','index.php','g4p_opt=mod_note&amp;g4p_id='.$g4p_a_note->id.'&amp;g4p_lien='.$lien,0),'" class="admin">',$g4p_langue['menu_mod_note'],'</a>';
                if(isset($_SESSION['permission']) and ($_SESSION['permission']->permission[_PERM_SUPPR_FILES_]))
                    echo ' - <a href="'.g4p_make_url('admin','exec.php','g4p_opt=suppr_note&amp;g4p_id='.$g4p_a_note->id.'&amp;g4p_lien='.$lien.'&amp;g4p_type='.$type,0),'"  onclick=" return confirme(this, \'',$g4p_langue['menu_sppr_confirme'],'\')" class="admin">',$g4p_langue['Supprimer'],'</a>';
                echo '</div>';
            }
            echo '<div class="box">';
            echo '<div class="box_title">Note</div>';

            if($g4p_a_note->timestamp!='0000-00-00 00:00:00')
                $g4p_chan=g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_a_note->timestamp));
            else
                $g4p_chan=$g4p_langue['class_chan'];

            echo '<span class="petit">',$g4p_langue['sys_function_note_chan'],$g4p_chan,'</span><br />';
            echo '<p>',nl2br($g4p_a_note->text),'</p></div>';
        }
        //echo '</div>';
    }
}

function g4p_affiche_sources($sources, $lien, $type)
{
    global $g4p_chemin, $g4p_langue;
    if($_SESSION['permission']->permission[_PERM_SOURCE_] and is_array($sources))
    {
        foreach($sources as $g4p_a_source)
        {
            if (isset($_SESSION['permission']) and $_SESSION['permission']->permission[_PERM_EDIT_FILES_])
            {
                echo '<div class="menu_interne"><a href="'.g4p_make_url('admin','index.php','g4p_opt=mod_source&amp;g4p_id='.$g4p_a_source->id,0).'" class="admin">',$g4p_langue['menu_mod_source'],'</a>';
                if(isset($_SESSION['permission']) and ($_SESSION['permission']->permission[_PERM_SUPPR_FILES_]))
                    echo ' - <a href="'.g4p_make_url('admin','exec.php','g4p_opt=suppr_source&amp;g4p_id='.$g4p_a_source->id.'&amp;g4p_lien='.$lien.'&amp;g4p_type='.$type,0),'" onclick=" return confirme(this, \'',$g4p_langue['menu_sppr_confirme'],'\')" class="admin">',$g4p_langue['menu_suppr_source'],'</a>';
                echo '</div>';
            }
            echo '<div class="box">';
            echo '<div class="box_title">Source : '.$g4p_a_source->record->title.'</div>';

            if($g4p_a_source->timestamp!='0000-00-00 00:00:00')
                $g4p_chan=g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_a_source->timestamp));
            else
                $g4p_chan=$g4p_langue['class_chan'];
            echo '<span class="petit">',$g4p_langue['sys_function_sour_chan'],$g4p_chan,'</span><br />';
            
            echo '<dl class="collapsed">';
            if(!empty($g4p_a_source->record->auth))
            {
                echo '<dt>'.$g4p_langue['sys_function_sour_auth'].'</dt> ';
                echo '<dd>'.$g4p_a_source->record->auth,'</dd>';
            }
            if(!empty($g4p_a_source->page))
            {
                echo '<dt>'.$g4p_langue['sys_function_sour_page'].'</dt> ';
                echo '<dd>'.$g4p_a_source->page.'</dd>';
            }
            if(!empty($g4p_a_source->record->repo_caln))
            {
                echo '<dt>'.$g4p_langue['sys_function_sour_ref'].'</dt> ';
                echo '<dd>'.$g4p_a_source->record->repo_caln,'</dd>';
            }
            if(!empty($g4p_a_source->record->repo_medi))
            {
                echo '<dt>'.$g4p_langue['sys_function_sour_type'].'</dt> ';
                echo '<dd>'.$g4p_a_source->record->repo_medi.'</dd>';
            }
            if(!empty($g4p_a_source->record->publ))
            {
                echo '<dt>'.$g4p_langue['sys_function_sour_publ'].'</dt> ';
                echo '<dd>'.nl2br($g4p_a_source->record->publ),'</dd>';
            }
            if(!empty($g4p_a_source->record->text))
            {
                echo '<dt>'.$g4p_langue['sys_function_sour_texte'].'</dt>';
                echo '<dd>'.nl2br($g4p_a_source->record->text),'</dd>';
            }
            echo '</dl>';

            if(!empty($g4p_a_source->record->repo))
            {
                $a_repo=$g4p_a_source->record->repo;
                echo '<dl class="collapsed">';
                if(!empty($a_repo->name))
                    echo '<dt>',$g4p_langue['sys_function_sour_depot'],'</dt><dd>',$a_repo->name,'</dd>';
                if(!empty($a_repo->addr->addr))               
                    echo '<dt>',$g4p_langue['sys_function_sour_depot_addr'],'</dt><dd>',$a_repo->addr->addr,'</dd>';
                if(!empty($a_repo->addr->city))                                   
                    echo '<dt>',$g4p_langue['sys_function_sour_depot_ville'],'</dt><dd>',$a_repo->addr->city,'</dd>';
                if(!empty($a_repo->addr->post))               
                    echo '<dt>',$g4p_langue['sys_function_sour_depot_cp'],'</dt><dd>',$a_repo->addr->post,'</dd>';
                if(!empty($a_repo->addr->stae))               
                    echo '<dt>stae</dt><dd>',$a_repo->addr->stae,'</dd>';
                if(!empty($a_repo->addr->ctry))               
                    echo '<dt>',$g4p_langue['sys_function_sour_depot_pays'],'</dt><dd>',$a_repo->addr->ctry,'</dd>';
                if(!empty($a_repo->phon1))               
                    echo '<dt>',$g4p_langue['sys_function_sour_depot_tel'],' 1 :</dt><dd>',$a_repo->phon1,'</dd>';
                if(!empty($a_repo->phon2))               
                    echo '<dt>',$g4p_langue['sys_function_sour_depot_tel'],' 2 :</dt><dd>',$a_repo->phon2,'</dd>';
                if(!empty($a_repo->phon3))               
                    echo '<dt>',$g4p_langue['sys_function_sour_depot_tel'],' 3 :</dt><dd>',$a_repo->phon3,'</dd>';
                if(!empty($a_repo->email1))               
                    echo '<dt>email 1 :</dt><dd>',$a_repo->email1,'</dd>';
                if(!empty($a_repo->email2))               
                    echo '<dt>email 2 :</dt><dd>',$a_repo->email2,'</dd>';
                if(!empty($a_repo->email3))               
                    echo '<dt>email 3 :</dt><dd>',$a_repo->email3,'</dd>';
                if(!empty($a_repo->fax1))               
                    echo '<dt>fax 1 :</dt><dd>',$a_repo->fax1,'</dd>';
                if(!empty($a_repo->fax2))               
                    echo '<dt>fax 2 :</dt><dd>',$a_repo->fax2,'</dd>';
                if(!empty($a_repo->fax3))               
                    echo '<dt>fax 3 :</dt><dd>',$a_repo->fax3,'</dd>';
                if(!empty($a_repo->www1))               
                    echo '<dt>www 1 :</dt><dd>',$a_repo->www1,'</dd>';
                if(!empty($a_repo->www2))               
                    echo '<dt>www 2 :</dt><dd>',$a_repo->www2,'</dd>';
                if(!empty($a_repo->www3))               
                    echo '<dt>www 3 :</dt><dd>',$a_repo->www3,'</dd>';
                echo '</dl>';
                
                g4p_affiche_multimedia(@$g4p_a_source->medias, $g4p_a_source->id, 'sources');
            }
            echo '</div>';
        }
    }
}

function g4p_affiche_asso($asso,$lien,$type)
{
  global $g4p_chemin, $g4p_langue;
  if(is_array($asso))
  {
    echo '<div class="assos"><em>',$g4p_langue['sys_function_asso_titre'],'</em><br />';
    foreach($asso as $g4p_a_note)
    {
      echo '<a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&amp;id_pers='.$g4p_a_note['indi']->indi_id.'&amp;genea_db_id='.$g4p_a_note['indi']->base,'fiche-'.$g4p_a_note['indi']->base.'-'.g4p_prepare_varurl($g4p_a_note['indi']->nom).'-'.g4p_prepare_varurl($g4p_a_note['indi']->prenom).'-'.$g4p_a_note['indi']->indi_id),'">',$g4p_a_note['indi']->nom,' ',$g4p_a_note['indi']->prenom,'</a> : ',$g4p_a_note['description'];
      if(isset($_SESSION['permission']) and ($_SESSION['permission']->permission[_PERM_EDIT_FILES_]))
      {
        echo ' <a href="'.g4p_make_url('admin','index.php','g4p_opt=mod_asso&amp;g4p_id='.$g4p_a_note['indi']->indi_id.'&amp;g4p_lien='.$lien,0),'" class="admin">',$g4p_langue['sys_function_asso_mod'],'</a>';
        if(isset($_SESSION['permission']) and ($_SESSION['permission']->permission[_PERM_SUPPR_FILES_]))
          echo ' - <a href="'.g4p_make_url('admin','exec.php','g4p_opt=suppr_asso&amp;g4p_id='.$g4p_a_note['indi']->indi_id.'&amp;g4p_lien='.$lien.'&amp;g4p_type='.$type,0),'"  onclick=" return confirme(this, \'',$g4p_langue['menu_sppr_confirme'],'\')" class="admin">',$g4p_langue['sys_function_asso_suppr'],'</a>';
      }
      echo '<br />';
    }
    echo '</div>';
  }
}

function g4p_affiche_event_temoins($asso)
{
  global $g4p_chemin, $g4p_langue;
  if(is_array($asso))
  {
    echo '<div class="assos"><em>',$g4p_langue['sys_function_asso_titre'],'</em><br />';
    foreach($asso as $g4p_a_note)
    {
      echo $g4p_a_note['events_id'],' ',$g4p_a_note['type'],' ',$g4p_a_note->lieu->toCompactString(),' : ',$g4p_a_note['description'];
      echo '<br />';
    }
    echo '</div>';
  }
}

function g4p_affiche_mariage()
{
    global $g4p_chemin, $g4p_tag_def, $g4p_config, $g4p_langue, $g4p_indi;
    if(isset($g4p_indi->familles))
    {
        foreach($g4p_indi->familles as $g4p_a_famille)//affiche tous les mariages
        {
            if ($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
            {
                echo '<div class="menu_interne">
                    <a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_note&amp;g4p_id='.$g4p_a_famille->id.'&amp;g4p_type=familles',0),'" class="admin">',$g4p_langue['menu_ajout_note'],'</a> 
                    <a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_source&amp;g4p_id='.$g4p_a_famille->id.'&amp;g4p_type=familles',0),'" class="admin">',$g4p_langue['menu_ajout_source'],'</a> 
                    <a href="',g4p_make_url('admin','index.php','g4p_opt=ajout_media&amp;g4p_id='.$g4p_a_famille->id.'&amp;g4p_type=familles',0),'" class="admin">',$g4p_langue['menu_ajout_media'],'</a> 
                    <a href="',g4p_make_url('admin','index.php','g4p_opt=mod_fams&amp;g4p_id='.$g4p_a_famille->id,0),'" class="admin">',$g4p_langue['menu_mod_famille'],'</a>';
                if(isset($_SESSION['permission']) and ($_SESSION['permission']->permission[_PERM_SUPPR_FILES_]))
                    echo ' <a href="',g4p_make_url('admin','exec.php','g4p_opt=suppr_fams&amp;g4p_id='.$g4p_a_famille->id,0),'" class="admin" onclick=" return confirme(this, \'',$g4p_langue['menu_sppr_confirme'],'\')">',$g4p_langue['menu_sppr_fam'],'</a>';
                echo '</div>';
            }
            echo '<div class="box">';
            
            if(!empty($g4p_a_famille->husb->nom) and !empty($g4p_a_famille->wife->nom))
                $tmp='Famille '.$g4p_a_famille->husb->nom.' &mdash; '.$g4p_a_famille->wife->nom;
            elseif(!empty($g4p_a_famille->husb->nom))
                $tmp='Famille '.$g4p_a_famille->husb->nom;
            elseif(!empty($g4p_a_famille->wife->nom))
                $tmp='Famille '.$g4p_a_famille->wife->nom;
            else
                $tmp='Famille';
            
            echo '<div class="box_title"><h3>'.$tmp.'</h3></div>';
            if(!empty($g4p_a_famille->timestamp))
                echo '<span class="petit">'.sprintf($g4p_langue['sys_function_mariage_chan'],g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_a_famille->timestamp))),'</span><br />';
                
            if(!empty($g4p_a_famille->husb->indi_id) and $g4p_indi->indi_id!=$g4p_a_famille->husb->indi_id)
                $conjoint='husb';
            elseif(!empty($g4p_a_famille->wife) and $g4p_indi->indi_id!=$g4p_a_famille->wife->indi_id)
                $conjoint='wife';
            else
                $conjoint=false;
      
            if($conjoint!==false)
            {
                echo '<dl class="collapsed"><dt>',$g4p_langue['sys_function_conjoint'],'</dt><dd>';
                echo g4p_link_nom($g4p_a_famille->$conjoint);
                echo '</dd></dl>';
            }
            else
                echo '<dl class="collapsed"><dt>',$g4p_langue['sys_function_conjoint'],'</dt><dd>',
                    $g4p_langue['sys_function_conjoint_inc'],'</dd></dl>';

            if(isset($g4p_a_famille->events))
            {
                //echo '<pre>'; print_r($g4p_a_famille->events);
                echo '<div class="box">';
                echo '<div class="box_title"><h3>Évènements</h3></div>';
                foreach($g4p_a_famille->events as $g4p_a_event)
                {
                    echo '<dl class="evenements">';
                    echo '<dt><em>',$g4p_tag_def[$g4p_a_event->tag],' :</em> ';
                    echo '<span class="date">',g4p_date($g4p_a_event->gedcom_date),'</span>';
                    echo (isset($g4p_a_event->sources))?('  <span style="color:blue; font-size:x-small;">-S-</span>  '):('');
                    echo (isset($g4p_a_event->notes))?('  <span style="color:blue; font-size:x-small;">-N-</span>  '):('');
                    echo (isset($g4p_a_event->medias))?('  <span style="color:blue; font-size:x-small;">-M-</span>  '):('');
                    echo (isset($g4p_a_event->assos))?('  <span style="color:blue; font-size:x-small;">-T-</span>  '):('');
                    echo ' <a href="',g4p_make_url('','detail_eventf.php','id='.$g4p_a_event->id.'&id_famille='.$g4p_a_famille->id.'&id_parent='.$g4p_indi->indi_id,0),'" class="noprint">',$g4p_langue['detail'],'</a>';
                    echo '</dt>';
                    
                    if($g4p_a_event->place->g4p_formated_place()!='')                    
                        echo '<dd><em>Lieu : </em>',$g4p_a_event->place->g4p_formated_place(),'</dd>';
                    echo '</dl>';
                }
                echo '</div>';
            }

            //les enfants des mariages
            if(isset($g4p_a_famille->enfants))
            {
                echo '<div class="box">';
                echo '<div class="box_title"><h3>Enfants issus de l\'union</h3></div>';
                echo '<ul style="list-style-type:none;padding:0;">';
                foreach($g4p_a_famille->enfants as $g4p_a_enfant)
                {
                    echo '<li>'.$g4p_a_enfant['rela_type'].' '.g4p_link_nom($g4p_a_enfant['indi']).'</li>';
                }
                echo '</ul>';
                echo "</div>";
            }

            if ($_SESSION['permission']->permission[_PERM_NOTE_])
                g4p_affiche_notes(@$g4p_a_famille->notes, $g4p_a_famille->id,'familles');

            if ($_SESSION['permission']->permission[_PERM_SOURCE_])
                g4p_affiche_sources(@$g4p_a_famille->sources, $g4p_a_famille->id,'familles');

            if ($_SESSION['permission']->permission[_PERM_MULTIMEDIA_])
                g4p_affiche_multimedia(@$g4p_a_famille->medias, $g4p_a_famille->id,'familles');

            echo '</div>';
        }
    }
}

function g4p_destroy_cache($fichier='session')
{
	// fichier, permet de choisir le cache à detruire
	global $g4p_chemin, $g4p_langue;
	if($fichier=='session')
	{
		$id=$g4p_indi->indi_id;
		unset($g4p_indi);
	}
	else
		$id=$fichier;

	if($g4p_langue['entete_charset']=='UTF-8')
		$g4p_tmp=utf8_decode($_SESSION['genea_db_nom']);
	else
		$g4p_tmp=$_SESSION['genea_db_nom'];

  // fiches pdf
  /*
  if(file_exists($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_000_'.$id.'.pdf'))
    unlink($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_000_'.$id.'.pdf');
  if(file_exists($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_001_'.$id.'.pdf'))
    unlink($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_001_'.$id.'.pdf');
  if(file_exists($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_010_'.$id.'.pdf'))
    unlink($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_010_'.$id.'.pdf');
  if(file_exists($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_100_'.$id.'.pdf'))
    unlink($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_100_'.$id.'.pdf');
  if(file_exists($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_011_'.$id.'.pdf'))
    unlink($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_011_'.$id.'.pdf');
  if(file_exists($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_011_'.$id.'.pdf'))
    unlink($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_110_'.$id.'.pdf');
  if(file_exists($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_111_'.$id.'.pdf'))
    unlink($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_111_'.$id.'.pdf');
  if(file_exists($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_101_'.$id.'.pdf'))
    unlink($g4p_chemin.'cache/'.$g4p_tmp.'/pdf/indi_101_'.$id.'.pdf');
*/

	//cache des données
	if(file_exists($g4p_chemin.'cache/'.$g4p_tmp.'/indi_'.$id.'.txt'))
	{
		if(!unlink($g4p_chemin.'cache/'.$g4p_tmp.'/indi_'.$id.'.txt'))
			$_SESSION['message'].=$g4p_langue['message_imp_suppr_cache'];
	}
	return g4p_load_indi_infos($id);
}

function g4p_load_indi_infos($id, $debug=false)
{
	global $g4p_chemin;
	static $cache_count=0;
    $cache_count++;
    /*
    if($cache_count>_MAX_LOAD_INDI_)
    {
        $indi=new g4p_individu(0);
        $indi->nom='limit exceeded';
        $indi->prenom='';
        return $indi;
    }*/
	$g4p_mon_indi=new g4p_individu($id);
	//$g4p_mon_indi->ignore_cache($debug);
	$g4p_mon_indi->g4p_load();
	
    if(empty($_SESSION['permission']->permission[_PERM_MASK_INDI_]) and ($g4p_mon_indi->resn=='privacy'))
    {
        $indi=new g4p_individu($g4p_mon_indi->indi_id);
        $indi->nom='Accès non autorisé';
        $indi->prenom='';
        $indi->base=$g4p_mon_indi->base;
        $indi->timestamp='';
        $indi->resn=$g4p_mon_indi->resn;
        $indi->sexe=$g4p_mon_indi->sexe;
        return $indi;
    }
    else  
        return $g4p_mon_indi;
	
}

function g4p_affiche_liste_nom($lettre, $cpt)
{
    global $g4p_langue,$g4p_mysqli ;
    static $cpt_fn=1;
    if($cpt_fn>30)
        die('fonction récursive en boucle n>30');
    $g4p_affiche=0;
    $cpt_fn++;

    if(empty($lettre) or empty($_SESSION['patronyme']))
    {
        $sql="SELECT lettre, nombre, longueur FROM agregats_noms WHERE base=".$_SESSION['genea_db_id']." AND longueur=1 ORDER BY lettre";
        $g4p_result=$g4p_mysqli->g4p_query($sql);
        if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
        {
            $g4p_total_nom=0;
            foreach($g4p_result as $a_g4p_result)
                $g4p_total_nom+=$a_g4p_result['nombre'];

            $_SESSION['patronyme'][strlen($lettre)]=$g4p_result;
            
            if(empty($lettre))
                $lettre=$g4p_result[0]['lettre'];
            $g4p_affiche=1;
/*
            if($g4p_total_nom<_AFF_NBRE_NOM_)
            {
                $lettre='';
                $g4p_affiche=1;
            }
            else  // recherche du nbre optimum à afficher
                g4p_affiche_liste_nom($g4p_result[0]['lettre'],$g4p_result[0]['nombre']);*/
        }
    }
    
    if(!empty($lettre) and $cpt>_AFF_NBRE_NOM_)
    {

        $longueur_lettre=strlen($lettre);
        $sql="SELECT lettre, nombre, longueur FROM agregats_noms WHERE base=".$_SESSION['genea_db_id']." AND longueur=".($longueur_lettre+1)." AND lettre LIKE '".mysql_escape_string($lettre)."%' ORDER BY lettre";

        $g4p_result=$g4p_mysqli->g4p_query($sql);
        if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
        {
            $g4p_total_nom=0;
            foreach($g4p_result as $a_g4p_result)
                $g4p_total_nom+=$a_g4p_result['nombre'];

            $_SESSION['patronyme'][$longueur_lettre+1]=$g4p_result;

            if($g4p_total_nom>_AFF_NBRE_NOM_)     //on affiche tout
            {
                $g4p_affiche=0;   // recherche du nbre optimum à afficher
                g4p_affiche_liste_nom($g4p_result[0]['lettre'],$g4p_result[0]['nombre']);
            }
            else
                $g4p_affiche=1;
        }
    }
    else
        $g4p_affiche=1;

    if($g4p_affiche)
    {
        $longueur_lettre=strlen($lettre);

        echo '<ul class="lettre">';
        foreach($_SESSION['patronyme'] as $a_niveau)
        {
		echo '<li>';
		foreach ($a_niveau as $une_lettre)
		{
			$une_lettre['lettre_aff']=$une_lettre['lettre'];
			echo '-<a href="',g4p_make_url('','liste_patronymes.php','g4p_cpt='.$une_lettre['nombre'].'&amp;patronyme='.urlencode($une_lettre['lettre']),'patronymes-'.$_SESSION['genea_db_id'].'-'.urlencode($une_lettre['lettre']).'-'.$une_lettre['nombre']),'">',$une_lettre['lettre_aff'],'</a>';
		}
		echo '-';
		echo '</li>';
        }
	echo '</ul>';
        
        if(!empty($lettre))
            $sql="SELECT indi_nom, count(*) as cpt FROM genea_individuals
                WHERE base=".$_SESSION['genea_db_id']." AND indi_nom LIKE'".mysql_escape_string($lettre)."%' GROUP BY indi_nom ORDER BY indi_nom";
        else
            $sql="SELECT indi_nom, count(*) as cpt FROM genea_individuals
                WHERE base=".$_SESSION['genea_db_id']." GROUP BY indi_nom ORDER BY indi_nom";
        $g4p_result=$g4p_mysqli->g4p_query($sql);
        if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
        {
            echo '
                <ul class="colonne">';
            $g4p_count_result=count($g4p_result);
            $dimension=ceil($g4p_count_result/2);
            $nom_courant=$g4p_result[0]['indi_nom'];
            $i=0;
            while($i<$dimension)
            {
                $g4p_result[$i]['indi_nom_aff']=$g4p_result[$i]['indi_nom'];

                if ($nom_courant!=$g4p_result[$i]['indi_nom'])
                    $nom_courant=$g4p_result[$i]['indi_nom'];
                echo '<li><a
                    href="',g4p_make_url('','liste_prenoms.php','g4p_nbre='.$g4p_result[$i]['cpt'].'&amp;nom='.urlencode($g4p_result[$i]['indi_nom']),'prenoms-'.$_SESSION['genea_db_id'].'-'.urlencode($g4p_result[$i]['indi_nom']).'-'.$g4p_result[$i]['cpt']),'">',$g4p_result[$i]['indi_nom_aff'],'
                    <span style="font-size:smaller;direction:ltr;unicode-bidi:embed;">(',$g4p_result[$i]['cpt'],')</span></a></li>';
                $i++;
            }
            //echo '</ul>';
            if(isset($g4p_result[$i]))
            {
                //echo '<ul class="colonne">';
                $nom_courant=$g4p_result[$i]['indi_nom'];
                while($i<$g4p_count_result)
                {
                    $g4p_result[$i]['indi_nom_aff']=$g4p_result[$i]['indi_nom'];

                    if ($nom_courant!=$g4p_result[$i]['indi_nom'])
                        $nom_courant=$g4p_result[$i]['indi_nom'];
                    echo '<li><a
                        href="',g4p_make_url('','liste_prenoms.php','g4p_nbre='.$g4p_result[$i]['cpt'].'&amp;nom='.urlencode($g4p_result[$i]['indi_nom']),'prenoms-'.$_SESSION['genea_db_id'].'-'.urlencode($g4p_result[$i]['indi_nom']).'-'.$g4p_result[$i]['cpt']),'">',$g4p_result[$i]['indi_nom_aff'],'
                        <span style="font-size:smaller;direction:ltr;unicode-bidi:embed;">(',$g4p_result[$i]['cpt'],')</span></a></li>';
                    $i++;
                }
                echo '</ul>';
            }
            //echo '<div class="spacer">&nbsp;</div>';
        }
    }
}

function g4p_agregat_noms()
{
  global $cpt_fn, $g4p_table_agregats, $g4p_langue, $g4p_mysqli;
  $i=0;
  function g4p_divise_resultat($lettre)
  {
    global $cpt_fn, $g4p_table_agregats,$i, $g4p_mysqli ;

    $longueur_lettre=strlen(stripslashes($lettre))+1;
    $sql="SELECT SUBSTRING(indi_nom,1,".$longueur_lettre.") AS lettre, COUNT(DISTINCT indi_nom) as cpt FROM genea_individuals
      WHERE indi_nom LIKE '".$lettre."%' AND base=".$_SESSION['genea_db_id']." GROUP BY lettre";
    $g4p_result=$g4p_mysqli->g4p_query($sql);
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    {
      foreach($g4p_result as $a_g4p_result)
      {
        if(strlen($a_g4p_result['lettre'])!=$longueur_lettre)//hack, resultat mysql subit un trim en sortie
          $a_g4p_result['lettre'].=' ';
        if($a_g4p_result['cpt']<_AFF_NBRE_NOM_)
          $g4p_table_agregats[]=array('lettre'=>$a_g4p_result['lettre'],'cpt'=>$a_g4p_result['cpt']);
        else
        {
          $g4p_table_agregats[]=array('lettre'=>$a_g4p_result['lettre'],'cpt'=>$a_g4p_result['cpt']);
          g4p_divise_resultat(addslashes($a_g4p_result['lettre']));
        }
      }
    }
  }

  $sql="SELECT SUBSTRING(indi_nom,1,1) AS lettre, COUNT(DISTINCT indi_nom) as cpt FROM genea_individuals WHERE base=".$_SESSION['genea_db_id']." GROUP BY lettre";
  $g4p_result=$g4p_mysqli->g4p_query($sql);
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
  {
    foreach($g4p_result as $a_g4p_result)
    {
      if($a_g4p_result['cpt']<_AFF_NBRE_NOM_)
      {
        $g4p_table_agregats[]=array('lettre'=>$a_g4p_result['lettre'],'cpt'=>$a_g4p_result['cpt']);
      }
      else
      {
        $g4p_table_agregats[]=array('lettre'=>$a_g4p_result['lettre'],'cpt'=>$a_g4p_result['cpt']);
        g4p_divise_resultat(addslashes($a_g4p_result['lettre']));
      }
    }
  }

  //print_r($g4p_table_agregats);
  if(!empty($g4p_table_agregats))
  {
    $sql="DELETE FROM agregats_noms WHERE base=".$_SESSION['genea_db_id'];
    $g4p_mysqli->g4p_query($sql);
    $sql="INSERT INTO agregats_noms (lettre , nombre, longueur, base ) VALUES ";
    foreach($g4p_table_agregats as $g4p_une_valeur)
    {
      $sql.="('".addslashes($g4p_une_valeur['lettre'])."',".$g4p_une_valeur['cpt'].','.strlen($g4p_une_valeur['lettre']).','.$_SESSION['genea_db_id'].'), ';
      if(strlen($sql)>400)
      {
        //echo $sql.'<br />';
        $g4p_mysqli->g4p_query(substr($sql,0,-2));
        $sql="INSERT INTO agregats_noms (lettre , nombre, longueur, base ) VALUES ";
      }
    }
    $g4p_result=$g4p_mysqli->g4p_query(substr($sql,0,-2));
  }
}

function g4p_update_agregat_noms($nom)
{
  global $cpt_fn, $g4p_table_agregats, $g4p_langue, $g4p_mysqli;
  $i=0;

  $tmp='';
  for($i=0;$i<strlen($nom);$i++)
  {
    $tmp.=$nom[$i];
    $cond[]="lettre='".mysql_escape_string($tmp)."'";
  }
  $cond=implode(' or ',$cond);

  $sql="SELECT id FROM agregats_noms WHERE base=".$_SESSION['genea_db_id']." AND (".$cond.")";
  $g4p_result=$g4p_mysqli->g4p_query($sql);
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result,'id'))
  {
    $cond=implode(',',array_keys($g4p_result));
    $sql="UPDATE agregats_noms SET nombre=nombre+1 WHERE id IN(".$cond.")";
    $g4p_result=$g4p_mysqli->g4p_query($sql);
  }
  else
  {
    $sql="INSERT INTO agregats_noms (lettre, nombre, longueur, base) VALUES ('".$nom[0]."',1,1,".$_SESSION['genea_db_id'].")";
    $g4p_result=$g4p_mysqli->g4p_query($sql);
  }
}

function g4p_affiche_multimedia($multimedia, $lien, $type)
{
    global $g4p_chemin, $g4p_langue;

    if($_SESSION['permission']->permission[_PERM_MULTIMEDIA_] and is_array($multimedia))
    {
        echo '<div class="multimedias">';
        foreach($multimedia as $g4p_a_multimedia)
        {
            if (isset($_SESSION['permission']) and $_SESSION['permission']->permission[_PERM_EDIT_FILES_])
            {
                echo '<div class="menu_interne">
                    <a href="',g4p_make_url('admin','index.php','g4p_opt=mod_multimedia&amp;g4p_id='.$g4p_a_multimedia->id.'&amp;type='.$type,0),'" class="admin">',$g4p_langue['a_index_affiche_media_mod'],'</a>';
                if ($_SESSION['permission']->permission[_PERM_SUPPR_FILES_])
                    echo ' - <a href="',g4p_make_url('admin','exec.php','g4p_opt=suppr_multimedia&amp;g4p_id='.$g4p_a_multimedia->id.'&amp;g4p_type='.$type.'&amp;g4p_lien='.$lien,0),'" onclick=" return confirme(this, \'',$g4p_langue['menu_sppr_confirme'],'\')" class="admin">',$g4p_langue['a_index_affiche_media_suppr'],'</a>';
                echo '</div>';
            }

        echo '<div class="box">';
        echo '<div class="box_title">',$g4p_a_multimedia->title,'</div>';

        if($g4p_a_multimedia->timestamp!='0000-00-00 00:00:00')
            $g4p_chan=g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_a_multimedia->timestamp));
        else
            $g4p_chan=$g4p_langue['class_chan'];

        echo '<span class="petit">',sprintf($g4p_langue['sys_function_media_chan'],$g4p_chan),'</span><br />
            <dl class="collapsed"><dt>',$g4p_langue['sys_function_media_format'],'</dt><dd>',$g4p_a_multimedia->format,'</dd>';

        if(($g4p_a_multimedia->format)=='URL')
            echo '<dt>',$g4p_langue['sys_function_media_lien'],'</dt><dd><a href="',$g4p_a_multimedia->file,'">',str_replace('mailto:','',$g4p_a_multimedia->title),'</a></dd>';
        else
            echo '<dt>',$g4p_langue['sys_function_media_lien'],'</dt><dd><a href="',$g4p_chemin.'cache/'.$_SESSION['genea_db_nom'].'/objets/',$g4p_a_multimedia->file,'" target="_blank">',$g4p_a_multimedia->title,'</a></dd>';
        echo '</dl>';
        echo '</div>';
    }
    echo '</div>';
  }
}

function g4p_getmicrotime()
{
  list($usec, $sec) = explode(" ",microtime());
  return ((float)$usec + (float)$sec);
}

// extrait la date GEDCOM
function g4p_jd_extract($gedcom_date='')
{
  global $g4p_cal_ged_php, $g4p_liste_calendrier, $g4p_cal_mrev, $g4p_cal_mhebreux, $g4p_cal_gregorien, $g4p_date_modif;

  //on supprime le commentaire de la date si il existe
  if(strpos($gedcom_date,'INT')!==false)
  {
    $gedcom_date=preg_replace('/INT(.*)\(.*\)/u','$1',$gedcom_date);
  }
  // Si il s'agit d'une plage, on prends que la première
  if(preg_match ('/(BET|FROM) (.*) (AND|TO) (.*)/u',$gedcom_date,$reg))
  {
    $gedcom_date=$reg[2];
  }

  $gedcom_date=preg_replace(array_keys($g4p_date_modif),'',$gedcom_date);

  // on cherche une date precise
  if(preg_match('/([0-9]{1,2}) ([A-Z]{3,4}) ([0-9]{1,7})/',$gedcom_date,$g4p_resultat))
  {
    //recherche du calendrier
    if(preg_match('/@.*@/',$gedcom_date,$g4p_calendrier))
    {
      $g4p_calendrier=$g4p_calendrier[0];
      $gedcom_date=str_replace($g4p_calendrier,'',$gedcom_date);
    }
    else
      $g4p_calendrier=g4p_jd_calendrier($g4p_resultat[2]);

    //on calcule le jd_count
    if(isset($g4p_cal_ged_php[$g4p_calendrier]))
    {
      //on verifie si il y a une date négative, BC n'est pas une valeur acceptable mais peut être rencontré
      if(preg_match('/(B\.C\.|BC)/',$gedcom_date))
        $g4p_annee='-';
      else
        $g4p_annee='';

      $chiffre_mois=array_merge(array_flip($g4p_cal_mrev),array_flip($g4p_cal_mhebreux),array_flip($g4p_cal_gregorien));
      //echo $g4p_cal_ged_php[$g4p_calendrier], '*', $chiffre_mois[$g4p_resultat[2]],'*', $g4p_resultat[1],'*', intval($g4p_annee.$g4p_resultat[3]),'<br />';

      $jd_count=@cal_to_jd($g4p_cal_ged_php[$g4p_calendrier], $chiffre_mois[$g4p_resultat[2]],$g4p_resultat[1],intval($g4p_annee.$g4p_resultat[3]));
    }
    else
      $jd_count='NULL';
    //var_dump($g4p_resultat);
    $date_jd['calendrier']=$g4p_calendrier;
    $date_jd['jd_count']=$jd_count;
    //3=jour/mois/année, 2=mois/année, 1=année
    $date_jd['precision']=3;
  }
  elseif(preg_match('/([A-Z]{3,4}) ([0-9]{1,7})/',$gedcom_date,$g4p_resultat))
  {
    //recherche du calendrier
    if(preg_match('/@.*@/',$gedcom_date,$g4p_calendrier))
    {
      $g4p_calendrier=$g4p_calendrier[0];
      $gedcom_date=str_replace($g4p_calendrier,'',$gedcom_date);
    }
    else
      $g4p_calendrier=g4p_jd_calendrier($g4p_resultat[1]);

    //on calcule le jd_count
    if(isset($g4p_cal_ged_php[$g4p_calendrier]))
    {
      //on verifie si il y a une date négative, BC n'est pas une valeur acceptable mais peut être rencontré
      if(preg_match('/(B\.C\.|BC)/',$gedcom_date))
        $g4p_annee='-';
      else
        $g4p_annee='';

      $chiffre_mois=array_merge(array_flip($g4p_cal_mrev),array_flip($g4p_cal_mhebreux),array_flip($g4p_cal_gregorien));
      $jd_count=@cal_to_jd($g4p_cal_ged_php[$g4p_calendrier], $chiffre_mois[$g4p_resultat[1]],1,intval($g4p_annee.$g4p_resultat[2]));
    }
    else
      $jd_count='NULL';
    //var_dump($g4p_resultat);
    $date_jd['calendrier']=$g4p_calendrier;
    $date_jd['jd_count']=$jd_count;
    //3=jour/mois/année, 2=mois/année, 1=année
    $date_jd['precision']=2;
  }
  elseif(preg_match('/([0-9]{1,7})/',$gedcom_date,$g4p_resultat))
  {
    //recherche du calendrier
    if(preg_match('/@.*@/',$gedcom_date,$g4p_calendrier))
    {
      $g4p_calendrier=$g4p_calendrier[0];
      $gedcom_date=str_replace($g4p_calendrier,'',$gedcom_date);
    }
    else
      $g4p_calendrier='@#DGREGORIAN@';

    //on calcule le jd_count
    if(isset($g4p_cal_ged_php[$g4p_calendrier]))
    {
      //on verifie si il y a une date négative, BC n'est pas une valeur acceptable mais peut être rencontré
      if(preg_match('/(B\.C\.|BC)/',$gedcom_date))
        $g4p_annee='-';
      else
        $g4p_annee='';
      $jd_count=cal_to_jd($g4p_cal_ged_php[$g4p_calendrier], 1,1,intval($g4p_annee.$g4p_resultat[1]));
    }
    else
      $jd_count='NULL';
    //var_dump($g4p_resultat);
    $date_jd['calendrier']=$g4p_calendrier;
    $date_jd['jd_count']=$jd_count;
    //3=jour/mois/année, 2=mois/année, 1=année
    $date_jd['precision']=1;
  }
  else
    $date_jd=false;

  return $date_jd;
}

function g4p_jd_calendrier($g4p_mois)
{
  global $g4p_liste_calendrier, $g4p_cal_mrev, $g4p_cal_mhebreux, $g4p_cal_gregorien;
  /*  [ @#DHEBREW@ | @#DROMAN@ | @#DFRENCH R@ | @#DGREGORIAN@ |
      @#DJULIAN@ | @#DUNKNOWN@ ]*/

  if(in_array($g4p_mois,$g4p_cal_mrev))
    return '@#DFRENCH R@';
  elseif(in_array($g4p_mois,$g4p_cal_mhebreux))
    return '@#DHEBREW@';
  elseif(in_array($g4p_mois,$g4p_cal_gregorien))
    return '@#DGREGORIAN@';
  else
    return '@#DGREGORIAN@';
  //remarques
  //Il n'est pas possible de différencier le calendrier julien du gregorien
  // par defaut la norme GEDCOM utilise le calendrier gregorien
}

/*
   tri multidimensionnel
   Fonction modifié par mes soins pour trier mes tableaux d'objets suivant le type d'évènement
 */

/**
 * array_column_sort
 *
 * function to sort an "arrow of rows" by its columns
 * exracts the columns to be sorted and then
 * uses eval to flexibly apply the standard
 * array_multisort function
 *
 * uses a temporary copy of the array whith "_" prefixed to  the keys
 * this makes sure that array_multisort is working with an associative
 * array with string type keys, which in turn ensures that the keys
 * will be preserved.
 *
 * TODO: find a way of modifying the keys of $array directly, without using
 * a copy of the array.
 *
 * flexible syntax:
 * $new_array = array_column_sort($array [, 'col1' [, SORT_FLAG [, SORT_FLAG]]]...);
 *
 * original code credited to Ichier (www.ichier.de) here:
 * http://uk.php.net/manual/en/function.array-multisort.php
 *
 * prefixing array indeces with "_" idea credit to steve at mg-rover dot org, also here:
 * http://uk.php.net/manual/en/function.array-multisort.php
 *
 */
function array_column_sort()
{
    global $g4p_tag_def;

    $args = func_get_args();
    $array = array_shift($args);
    // make a temporary copy of array for which will fix the
    // keys to be strings, so that array_multisort() doesn't
    // destroy them
    $array_mod = array();
    foreach ($array as $key => $value)
        $array_mod['_' . $key] = $value;

    $i = 0;
    $multi_sort_line = "return array_multisort( ";
    foreach ($args as $arg)
    {
        $i++;
        if ( is_string($arg) )
            foreach ($array_mod as $row_key => $row)
                $sort_array[$i][] = $row->$arg;
        else
            $sort_array[$i] = $arg;
        $multi_sort_line .= "\$sort_array[" . $i . "], ";
    }
    $multi_sort_line .= "\$array_mod );";
    eval($multi_sort_line);
    // now copy $array_mod back into $array, stripping off the "_"
    // that we added earlier.
    $array = array();
    foreach ($array_mod as $key => $value)
        $array[ substr($key, 1) ] = $value;
    return $array;
}

// recherches les dependances pour gerer les fichiers de cache
function g4p_cherche_dependances($g4p_id,$g4p_type)
{
  switch($g4p_type)
  {
    case 'notes':
      $sql="SELECT events_details_id FROM rel_events_notes WHERE notes_id=$g4p_id";
      break;

    case 'sources':
      $sql="SELECT events_details_id FROM rel_events_sources WHERE sources_id=$g4p_id";
      break;

    case 'medias':
      $sql="SELECT events_details_id FROM rel_events_multimedia WHERE multimedia_id=$g4p_id";
      break;
  }

  $g4p_result=$g4p_mysqli->g4p_query($sql);
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
  {
    $g4p_events='';
    foreach($g4p_result as $g4p_a_event)
      $g4p_events.=$g4p_a_event['events_id'].',';
    $g4p_events=substr($g4p_events,0,-1);

    $sql="SELECT familles_husb, familles_wife FROM rel_familles_event
      LEFT JOIN genea_familles USING (familles_id) WHERE events_id IN ($g4p_events)";
    $g4p_result=$g4p_mysqli->g4p_query($sql);
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    {
      foreach($g4p_result as $g4p_a_famille)
      {
        if($g4p_a_famille['familles_husb'])
          $g4p_liste_indi[]=$g4p_a_famille['familles_husb'];
        if($g4p_a_famille['familles_wife'])
          $g4p_liste_indi[]=$g4p_a_famille['familles_wife'];
      }
    }
    $sql="SELECT indi_id FROM rel_indi_event WHERE events_id IN ($g4p_events)";
    $g4p_result=$g4p_mysqli->g4p_query($sql);
    if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
      foreach($g4p_result as $g4p_a_indi)
        $g4p_liste_indi[]=$g4p_a_indi['indi_id'];
  }

  switch($g4p_type)
  {
    case 'notes':
      $sql="SELECT familles_husb, familles_wife FROM rel_familles_notes
        LEFT JOIN genea_familles USING (familles_id) WHERE rel_familles_notes.notes_id=$g4p_id";
      break;

    case 'sources':
      $sql="SELECT familles_husb, familles_wife FROM rel_familles_sources
        LEFT JOIN genea_familles USING (familles_id) WHERE rel_familles_sources.sources_id=$g4p_id";
      break;

    case 'medias':
      $sql="SELECT familles_husb, familles_wife FROM rel_familles_multimedia
        LEFT JOIN genea_familles USING (familles_id) WHERE rel_familles_multimedia.multimedia_id=$g4p_id";
      break;
  }
  $g4p_result=$g4p_mysqli->g4p_query($sql);
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
  {
    foreach($g4p_result as $g4p_a_famille)
    {
      if($g4p_a_famille['familles_husb'])
        $g4p_liste_indi[]=$g4p_a_famille['familles_husb'];
      if($g4p_a_famille['familles_wife'])
        $g4p_liste_indi[]=$g4p_a_famille['familles_wife'];
    }
  }
  switch($g4p_type)
  {
    case 'notes':
      $sql="SELECT indi_id FROM rel_indi_notes WHERE notes_id=$g4p_id";
      break;

    case 'sources':
      $sql="SELECT indi_id FROM rel_indi_sources WHERE sources_id=$g4p_id";
      break;

    case 'medias':
      $sql="SELECT indi_id FROM rel_indi_multimedia WHERE multimedia_id=$g4p_id";
      break;
  }
  $g4p_result=$g4p_mysqli->g4p_query($sql);
  if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
    foreach($g4p_result as $g4p_a_indi)
      $g4p_liste_indi[]=$g4p_a_indi['indi_id'];
  return array_unique($g4p_liste_indi);
}

// effectue quelques remplacements pour eviter des problèmes dans l'url
function g4p_prepare_varurl($var)
{
  //$search  = array ('/\&/', '/\-/', '/\?/', '/"/', '/ /');
  //$replace = array (htmlentities('&',ENT_COMPAT,'UTF-8'), htmlentities('-',ENT_COMPAT,'UTF-8'), htmlentities('?',ENT_COMPAT,'UTF-8'), htmlentities('"',ENT_COMPAT,'UTF-8'), ' ');
  return preg_replace("[ |\&|\-|\?|\"]", "_",$var);
  //return preg_replace($search, $replace,$var);

}

//construit l'url
function g4p_make_url($repertoire,$fichier,$arguments,$mod_rewrite=0,$amp_replace=1)
{
  global $g4p_config, $g4p_chemin;
  if($amp_replace)
  {
  	$arguments=str_replace('&amp;','&',$arguments);
  	$arguments=str_replace('&','&amp;',$arguments);
  	$arguments=str_replace(' ','%20',$arguments);
  }

  switch($g4p_config['g4p_type_install'])
  {
    case 'seule':
      if($arguments!='')
        $arguments='?'.$arguments;
      if($repertoire!='')
        $repertoire.='/';
      return $g4p_chemin.$repertoire.$fichier.$arguments;

    case 'seule-mod_rewrite':
      if(empty($mod_rewrite))
      {
        if($arguments!='')
        $arguments='?'.$arguments;
        if($repertoire!='')
          $repertoire.='/';
        return $g4p_chemin.$repertoire.$fichier.$arguments;
      }
      else
        return $g4p_chemin.$mod_rewrite;

    case 'module-npds':
      if($arguments!='')
      {
        $arguments='&'.$arguments;
      }
      if($repertoire!='')
        $repertoire='/'.$repertoire;
      return 'modules.php?ModPath=genea4p'.$repertoire.'&ModStart='.substr($fichier,0,-4).$arguments;

    case 'module-npds-mod_rewrite':
      $arguments='&'.$arguments;
      return 'modules.php?ModPath=genea4p'.$repertoire.'&ModStart='.substr($fichier,0,-4).$arguments;
  }
}

function g4p_strftime($masque,$date,$format='timestamp')
{
  global $g4p_jours_gregorien, $g4p_mois_gregorien, $g4p_mois_revolutionnaire, $g4p_jours_revolutionnaire, $g4p_jours_juif, $g4p_mois_juif, $g4p_cal_gregorien;

    if($date==NULL)
        return NULL;

  switch($format)
  {
    case 'timestamp':
      $g4p_tmp=array(
          '/%A/'=>$g4p_jours_gregorien[strftime('%w',$date)],
          '/%d/'=>strftime('%d',$date),
          '/%b/'=>$g4p_cal_gregorien[intval(strftime('%m',$date))],
          '/%B/'=>$g4p_mois_gregorien[intval(strftime('%m',$date))],
          '/%Y/'=>strftime('%Y',$date),
          '/%H/'=>strftime('%H',$date),
          '/%M/'=>strftime('%S',$date),
          '/%S/'=>strftime('%S',$date),
          );
      return trim(preg_replace(array_keys($g4p_tmp),array_values($g4p_tmp),$masque));
      break;

    case 'jd_cal_julien':
    case 'jd_cal_gregorien':
      $date=explode('-',$date);
      $g4p_tmp=array(
          '/%A/'=>$g4p_jours_gregorien[$date[0]],
          '/%d/'=>$date[1],
          '/%B/'=>$g4p_mois_gregorien[$date[2]],
          '/%Y/'=>$date[3],
          '/%H/'=>'',
          '/%M/'=>'',
          '/%S/'=>'',
          );
      return trim(preg_replace(array_keys($g4p_tmp),array_values($g4p_tmp),$masque));
      break;

    case 'jd_cal_revolutionnaire':
      $date=explode('-',$date);
      $g4p_tmp=array(
          '/%A/'=>$g4p_jours_revolutionnaire[$date[0]],
          '/%d/'=>$date[1],
          '/%B/'=>$g4p_mois_revolutionnaire[$date[2]],
          '/%Y/'=>$date[3],
          '/%H/'=>'',
          '/%M/'=>'',
          '/%S/'=>'',
          );
      return trim(preg_replace(array_keys($g4p_tmp),array_values($g4p_tmp),$masque));
      break;

    case 'jd_cal_juif':
      $date=explode('-',$date);
      $g4p_tmp=array(
          '/%A/'=>$g4p_jours_juif[$date[0]],
          '/%d/'=>$date[1],
          '/%B/'=>$g4p_mois_juif[$date[2]],
          '/%Y/'=>$date[3],
          '/%H/'=>'',
          '/%M/'=>'',
          '/%S/'=>'',
          );
      return trim(preg_replace(array_keys($g4p_tmp),array_values($g4p_tmp),$masque));
      break;
  }
}

function g4p_liste_membres()
{
  global $g4p_config;

  if($g4p_config['g4p_type_install']=='module-npds' or $g4p_config['g4p_type_install']=='module-npds-mod_rewrite')
    echo $sql="SELECT uid as id, CONCAT(uname,' (', email,')') as email FROM users ORDER BY uname";
  else
    $sql="SELECT id, email FROM genea_membres";
  $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
  return $g4p_mysqli->g4p_result($g4p_infos_req);
}

// affichage détaillé des relations (frères et soeurs, tantes et oncles, cousin(e)s...
// je n'affiche uniquement les relation de sang, pas de demi-frères et soeur par ex.
function g4p_relations_avancees($id)
{
  global $g4p_chemin, $g4p_langue, $g4p_config;

  $mon_indi=g4p_load_indi_infos($id);

  $tatas=array();
  $cousins=array();
  $freres=array();
  $parents_liste=array();

  // je récupère la famille des parents
  if(isset($mon_indi->parents))
  {
    foreach($mon_indi->parents as $famille=>$parents)
    {
      if(strtoupper($parents->rela_type)=='BIRTH')
      {
        if(isset($parents->pere))
          $pere=$parents->pere->indi_id;
        else
          $pere=0;
        $famille_pere=$famille;
        if(isset($parents->mere))
          $mere=$parents->mere->indi_id;
        else
          $mere=0;

        $pere=g4p_load_indi_infos($pere);
        $mere=g4p_load_indi_infos($mere);

        // je récupère la famille des grands parents
        if(isset($pere->parents))
        {
          foreach($pere->parents as $famille=>$gparents)
          {
            if(strtoupper($gparents->rela_type)=='BIRTH')
            {
              if(isset($gparents->pere->indi_id))
              {
                $gpere=$gparents->pere->indi_id;
                $famille_gpere=$famille;
                $gpere=g4p_load_indi_infos($gpere);

                //liste des tantes et oncles
                if(isset($gpere->familles[$famille_gpere]->enfants))
                {
                  foreach($gpere->familles[$famille_gpere]->enfants as $a_enfant)
                  {
                    if($a_enfant->indi_id!=$pere->indi_id )
                      $tatas[$a_enfant->indi_id]=$a_enfant;
                    else
                      $parents_liste[$a_enfant->indi_id]=$a_enfant;
                  }
                }
              }
            }
          }
        }

        if(isset($mere->parents))
        {
          foreach($mere->parents as $famille=>$gparents)
          {
            if(strtoupper($gparents->rela_type)=='BIRTH')
            {
              if(isset($gparents->pere->indi_id))
              {
                $gpere=$gparents->pere->indi_id;
                $famille_gpere=$famille;
                $gpere=g4p_load_indi_infos($gpere);

                //liste des tantes et oncles
                if(isset($gpere->familles[$famille_gpere]->enfants))
                {
                  foreach($gpere->familles[$famille_gpere]->enfants as $a_enfant)
                  {
                    if($a_enfant->indi_id!=$mere->indi_id )
                      $tatas[$a_enfant->indi_id]=$a_enfant;
                    else
                      $parents_liste[$a_enfant->indi_id]=$a_enfant;
                  }
                }
              }
            }
          }
        }

        //liste des tantes et oncles
        if(isset($pere->familles[$famille_pere]->enfants))
        {
          foreach($pere->familles[$famille_pere]->enfants as $a_enfant)
          {
            if($a_enfant->indi_id!=$mon_indi->indi_id )
              $freres[$a_enfant->indi_id]=$a_enfant;
          }
        }
      }

      //liste des cousins et des frères et soeurs
      foreach($tatas as $a_tata)
      {
        $g4p_tmp=g4p_load_indi_infos($a_tata->indi_id);
        if(isset($g4p_tmp->familles))
        {
          foreach($g4p_tmp->familles as $cle_famille=>$une_famille)
          {
            if(isset($une_famille->enfants))
            {
              foreach($une_famille->enfants as $un_enfant)
              {
                if($cle_famille!=$famille_pere)
                  $cousins[$un_enfant->indi_id]=$un_enfant;
              }
            }
          }
        }
      }

    }
  }

  if(count($freres)>0)
  {
    echo '<div class="freres">';
    echo '<em>',$g4p_langue['sys_function_show_rela_ext_freres'],'</em>';
    foreach($freres as $frere)
    {
      if(!$_SESSION['permission']->permission[_PERM_MASK_INDI_] or $g4p_indi->resn!='privacy')
        echo '<br /><a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&amp;id_pers='.$frere->indi_id.'&amp;genea_db_id='.$frere->base,'fiche-'.$frere->base.'-'.g4p_prepare_varurl($frere->nom).'-'.g4p_prepare_varurl($frere->prenom).'-'.$frere->indi_id),'">',$frere->nom,' ',$frere->prenom,' ',g4p_date($frere->date_rapide()),'</a>';
    }
    echo '</div>';
  }

  if(count($tatas)>0)
  {
    echo '<div class="oncles">';
    echo '<em>',$g4p_langue['sys_function_show_rela_ext_oncles'],'</em>';
    foreach($tatas as $tata)
    {
      if(!$_SESSION['permission']->permission[_PERM_MASK_INDI_] or $g4p_indi->resn!='privacy')
        echo '<br /><a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&amp;id_pers='.$tata->indi_id.'&amp;genea_db_id='.$tata->base,'fiche-'.$tata->base.'-'.g4p_prepare_varurl($tata->nom).'-'.g4p_prepare_varurl($tata->prenom).'-'.$tata->indi_id),'">',$tata->nom,' ',$tata->prenom,' ',g4p_date($tata->date_rapide()),'</a>';
    }
    echo '</div>';
  }

 if(count($cousins)>0)
  {
    echo '<div class="cousins">';
    echo '<em>',$g4p_langue['sys_function_show_rela_ext_cousins'],'</em>';
    foreach($cousins as $cousin)
    {
      if(!$_SESSION['permission']->permission[_PERM_MASK_INDI_] or $g4p_indi->resn!='privacy')
        echo '<br /><a href="',g4p_make_url('','index.php','g4p_action=fiche_indi&amp;id_pers='.$cousin->indi_id.'&amp;genea_db_id='.$cousin->base,'fiche-'.$cousin->base.'-'.g4p_prepare_varurl($cousin->nom).'-'.g4p_prepare_varurl($cousin->prenom).'-'.$cousin->indi_id),'">',$cousin->nom,' ',$cousin->prenom,' ',g4p_date($cousin->date_rapide()),'</a>';
    }
    echo '</div>';
  }
}

function vide_repertoire($base)
{
  global $g4p_chemin;

  if($g4p_rep=opendir($g4p_chemin.'cache/'.$base))
  {
    while($g4p_fichier=readdir($g4p_rep))
    {
      if(is_file($g4p_chemin.'cache/'.$base.'/'.$g4p_fichier) and $g4p_fichier!='index.html' and $g4p_fichier!='.htaccess' and $g4p_fichier!='.' and $g4p_fichier!='..')
        unlink($g4p_chemin.'cache/'.$base.'/'.$g4p_fichier);
      elseif(is_dir($g4p_chemin.'cache/'.$base.'/'.$g4p_fichier) and $g4p_fichier!='.' and $g4p_fichier!='..')
        vide_repertoire($base.'/'.$g4p_fichier);
    }
  }

  return 1;
}

function & strip_slashes(&$str)
{
  global $g4p_langue;
  if(is_array($str))
  {
    while(list($key, $val) = each($str))
      $str[$key] = strip_slashes($val);
  }
  else
  {
    if($g4p_langue['entete_charset']!='UTF-8')
      $str = stripslashes(utf8_encode($str));
    else
      $str = stripslashes($str);
  }
  return $str;
}

/*
function g4p_parse_lieu($lieu,$all=0)
{
  global $g4p_config;
  global $g4p_langue;

  if($all==0)
  {
    $texte=array();
    foreach($_SESSION['place'] as $val)
      if($val!=-1 and !empty($g4p_config['subdivision'][$val]) and !empty($lieu[$g4p_config['subdivision'][$val]]))
        $texte[]=$lieu[$g4p_config['subdivision'][$val]];
    return implode($g4p_config['lieu_separateur'],$texte);
  }
  elseif($all==1)
  {
    $texte='';
    foreach($g4p_config['subdivision'] as $val)
      if($val!=-1 and !empty($lieu[$val]))
        $texte.='<br/>'.$g4p_langue[$val].' : '.$lieu[$val];
    return $texte;
  }
  else
  {
    return $lieu['place_ville'];
  }
}
*/

function g4p_add_intohistoric($id,$type='indi')
{
	global $g4p_config, $g4p_indi;

	if(!isset($_SESSION['historic'][$type]) or !is_array($_SESSION['historic'][$type]))
		$_SESSION['historic'][$type]=array();

	switch($type)
	{
		case 'indi':
		if(!empty($g4p_indi->indi_id) and $g4p_indi->indi_id==$id)
			$mon_indi=$g4p_indi;
		else
			$mon_indi=g4p_load_indi_infos($id);

		//On enlève l'individu
		//$id = array_keys($_SESSION['historic'][$type],$mon_indi->indi_id.'||'.$mon_indi->nom.' '.$mon_indi->prenom.' '.substr(g4p_date($mon_indi->date_rapide()),21,-7));
		//if($id)
		foreach($_SESSION['historic']['indi'] as $key=>$value)
		{
			if($value['id']==$mon_indi->indi_id)
				unset($_SESSION['historic']['indi'][$key]);
		}
    
		//on l'ajoute à la fin de la liste
		$_SESSION['historic']['indi'][] = array('id'=>$mon_indi->indi_id, 
			'text'=>$mon_indi->nom.' '.$mon_indi->prenom);

		break;
	}
  
	if(count($_SESSION['historic'][$type])>$g4p_config['historic_count'])
		unset($_SESSION['historic'][$type][0]);
  
	//on ré-indice le tableau
	$_SESSION['historic'][$type] = array_values($_SESSION['historic'][$type]);
}

function g4p_show_alias($alias,$admin=0)
{
    global $g4p_langue;

    if(!empty($alias))
    {
        echo '<div class="box">';
        echo '<div class="box_title">Alias</div>';  
        echo '<ul>';
        foreach($alias as $a_alias)
        {
            $g4p_texte='';

            echo '<li>',g4p_link_nom($a_alias);
            if($admin and $_SESSION['permission']->permission[_PERM_EDIT_FILES_])
                echo ' <a href="',g4p_make_url('admin','exec.php','g4p_opt=suppr_alia&amp;g4p_id1='.$g4p_indi->indi_id.'&amp;g4p_id2='.$a_alias->indi_id,0),'" class="admin" onclick=" return confirme(this, \'',$g4p_langue['a_index_modfams_confirme_suppr_js'],'\')">',$g4p_langue['Supprimer'],'</a>';
            echo '</li>';
        }
        echo '</div>';
    }
}

function g4p_anniversaire()
{
  $anniversaire=array();
  $sql="SELECT genea_individuals.indi_id, genea_individuals.base AS base, resn, indi_nom, indi_prenom, date_event FROM genea_events
    LEFT JOIN rel_indi_event ON (id=events_id)
    LEFT JOIN genea_individuals USING (indi_id)
    WHERE date_event LIKE '%".date('d M')."%' AND (type='BIRT' OR type='BAPM') AND jd_count>2415021
    ORDER BY indi_nom, indi_prenom ASC";
  if($g4p_infos_req=$g4p_mysqli->g4p_query($sql))
  {
    if($req1=$g4p_mysqli->g4p_result($g4p_infos_req,'indi_id'))
    {
      $liste_indi=implode(',',array_keys($req1));

      $sql="SELECT genea_individuals.indi_id AS indi_id, date_event AS deces
          FROM `genea_individuals`
          JOIN rel_indi_event AS a ON ( genea_individuals.indi_id=a.indi_id )
          JOIN genea_events AS n ON ( a.events_id = n.id)
          WHERE genea_individuals.indi_id IN (".$liste_indi.") AND (n.type = 'DEAT' OR n.type = 'BURI' )";
      if($g4p_result_req=$g4p_mysqli->g4p_query($sql))
        $g4p_deces=$g4p_mysqli->g4p_result($g4p_result_req,'indi_id',false);
      else
        $g4p_deces=array();

      foreach($req1 as $a_pers)
      {
        $anniversaire[$a_pers['indi_id']]['resn']=$a_pers['resn'];
        $anniversaire[$a_pers['indi_id']]['base']=$a_pers['base'];
        $anniversaire[$a_pers['indi_id']]['nom_prenom']=$a_pers['indi_nom'].' '.$a_pers['indi_prenom'];
        $anniversaire[$a_pers['indi_id']]['naissance']=$a_pers['date_event'];
        if(!empty($g4p_deces[$a_pers['indi_id']]['deces']))
          $anniversaire[$a_pers['indi_id']]['deces']=$g4p_deces[$a_pers['indi_id']]['deces'];
      }
    }
  }
  return $anniversaire;
}


//fonction non utilisable en dehors de son contexte
//A changer si je suis motiver
function g4p_formulaire_date_event($g4p_date,$g4p_ajout='mod_event')
  {
    global $g4p_date_range,$g4p_langue,$liste_mois_gregorien,$liste_mois_francais,$liste_mois_hebreux,$g4p_liste_calendrier,$g4p_date_modif1,$g4p_events;

    if(isset($_GET['g4p_type_event']))
      $g4p_type_event_url='&amp;g4p_type_event='.$_GET['g4p_type_event'];
    else
      $g4p_type_event_url='';

    echo '<hr /><em>';
    echo $g4p_langue['a_index_mod_event_date'],'</em><br/>';

    if(!isset($_GET['g4p_type_date']))
    {
      if(!empty($g4p_date) and !empty($g4p_date->date['exact']))
        $g4p_select='exacte';
      elseif(!empty($g4p_date) and !empty($g4p_date->date['range']))
        $g4p_select='range';
      else
        $g4p_select='exacte';
    }
    else
      $g4p_select=$_GET['g4p_type_date'];

    echo '<ul class="tabnav">';
    if($g4p_select=='range')
    {
      echo '<input type="hidden" name="g4p_date_type" value="range" />';
      echo '<li ><a href="',g4p_make_url('admin','index.php','g4p_opt='.$g4p_ajout.$g4p_type_event_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_date=exacte',0),'" >Date</a></li>';
      echo '<li class="active"><a href="',g4p_make_url('admin','index.php','g4p_opt='.$g4p_ajout.$g4p_type_event_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_date=range',0),'" >Période</a></li>';
      echo '<li ><a href="',g4p_make_url('admin','index.php','g4p_opt='.$g4p_ajout.$g4p_type_event_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_date=gedcom',0),'" >Format GEDCOM</a></li>';
      echo '</ul>';

      echo '<p class="boite_tabulation">';
      echo '<select name="date_period_deb_modif" style="width:auto">';
      echo '<option value="" ></option>';
      foreach($g4p_date_range as $a_modif_value)
      {
        $keys=array_keys($a_modif_value);
        if(!empty($g4p_date->date['range']['de']['type']) and $keys[0]==$g4p_date->date['range']['de']['type'])
          $selected='selected="selected"';
        else
          $selected='';
        echo '<option value="',$keys[0],'" ',$selected,'>',$a_modif_value[$keys[0]],'</option>';
      }
      echo '</select> ';

      echo '<select name="date_periode_deb_calendrier" style="width:auto">';
      echo '<option value="" ></option>';
      foreach($g4p_liste_calendrier as $a_modif_key=>$a_modif_value)
      {
        if(!empty($g4p_date->date['range']['de']['calendar']) and $a_modif_key==$g4p_date->date['range']['de']['calendar'])
          $selected='selected="selected"';
        else
          $selected='';
        echo '<option value="',$a_modif_key,'" ',$selected,'>',$a_modif_value,'</option>';
      }
      echo '</select> ';

      echo '<input type="text" name="date_periode_deb_jour" value="'.@$g4p_date->date['range']['de']['day'].'" style="width:4em" /> ';

      $tmp=array_merge($liste_mois_gregorien,$liste_mois_francais,$liste_mois_hebreux);
      echo '<select name="date_periode_deb_mois" id="date_exate_mois" style="width:auto">';
      echo '<option value="" ></option>';
      foreach($tmp as $a_modif_key=>$a_modif_value)
      {
        if(!empty($g4p_date->date['range']['de']['month']) and $a_modif_key==$g4p_date->date['range']['de']['month'])
          $selected='selected="selected"';
        else
          $selected='';
        echo '<option value="',$a_modif_key,'" ',$selected,'>',$a_modif_value,'</option>';
      }
      echo '</select>';

      echo ' <input type="text" name="date_periode_deb_annee" value="'.@$g4p_date->date['range']['de']['year'].'" style="width:4em" /><br /> ';

      echo '<select name="date_period_fin_modif" style="width:auto">';
      echo '<option value="" ></option>';
      foreach($g4p_date_range as $a_modif_key=>$a_modif_value)
      {
        $keys=array_keys($a_modif_value);
        if(!empty($g4p_date->date['range']['a']['type']) and $keys[1]==$g4p_date->date['range']['a']['type'])
          $selected='selected="selected"';
        else
          $selected='';
        echo '<option value="',$keys[1],'" ',$selected,'>',$a_modif_value[$keys[1]],'</option>';
      }
      echo '</select> ';

      echo '<select name="date_periode_fin_calendrier" style="width:auto">';
      echo '<option value="" ></option>';
      foreach($g4p_liste_calendrier as $a_modif_key=>$a_modif_value)
      {
        if(!empty($g4p_date->date['range']['a']['calendar']) and $a_modif_key==$g4p_date->date['range']['a']['calendar'])
          $selected='selected="selected"';
        else
          $selected='';
        echo '<option value="',$a_modif_key,'" ',$selected,'>',$a_modif_value,'</option>';
      }
      echo '</select> ';

      echo '<input type="text" name="date_periode_fin_jour" value="'.@$g4p_date->date['range']['a']['day'].'" style="width:4em" /> ';

      $tmp=array_merge($liste_mois_gregorien,$liste_mois_francais,$liste_mois_hebreux);
      echo '<select name="date_periode_fin_mois" id="date_periode_fin_mois" style="width:auto">';
      echo '<option value="" ></option>';
      foreach($tmp as $a_modif_key=>$a_modif_value)
      {
        if(!empty($g4p_date->date['range']['a']['month']) and $a_modif_key==$g4p_date->date['range']['a']['month'])
          $selected='selected="selected"';
        else
          $selected='';
        echo '<option value="',$a_modif_key,'" ',$selected,'>',$a_modif_value,'</option>';
      }
      echo '</select>';

      echo ' <input type="text" name="date_periode_fin_annee" value="'.@$g4p_date->date['range']['a']['year'].'" style="width:4em" /> ';

      echo '</p>';

    }
    elseif($g4p_select=='gedcom')
    {
      echo '<input type="hidden" name="g4p_date_type" value="gedcom" />';
      echo '<li ><a href="',g4p_make_url('admin','index.php','g4p_opt='.$g4p_ajout.$g4p_type_event_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_date=exacte',0),'" >Date</a></li>';
      echo '<li ><a href="',g4p_make_url('admin','index.php','g4p_opt='.$g4p_ajout.$g4p_type_event_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_date=range',0),'" >Période</a></li>';
      echo '<li class="active"><a href="',g4p_make_url('admin','index.php','g4p_opt='.$g4p_ajout.$g4p_type_event_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_date=gedcom',0),'" >Format GEDCOM</a></li>';
      echo '</ul>';

      //date gedcom
      echo '<p class="boite_tabulation">';
      echo ' <input type="text" name="date_gedcom" value="'.@$g4p_date->phrase.'" style="width:40em" /></p>';

    }
    else
    {
      echo '<input type="hidden" name="g4p_date_type" value="date_exacte" />';
      echo '<li class="active"><a href="',g4p_make_url('admin','index.php','g4p_opt='.$g4p_ajout.$g4p_type_event_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_date=exacte',0),'" >Date</a></li>';
      echo '<li ><a href="',g4p_make_url('admin','index.php','g4p_opt='.$g4p_ajout.$g4p_type_event_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_date=range',0),'" >Période</a></li>';
      echo '<li ><a href="',g4p_make_url('admin','index.php','g4p_opt='.$g4p_ajout.$g4p_type_event_url.'&amp;g4p_id='.$_GET['g4p_id'].'&amp;g4p_type_date=gedcom',0),'" >Format GEDCOM</a></li>';
      echo '</ul>';

      echo '<p class="boite_tabulation">';
      echo '<select name="date_exacte_modif" style="width:auto">';
      echo '<option value="" ></option>';
      if(@empty($g4p_date->date['exact']['type']))
        echo '<option value="date_exacte" selected="selected">Date exacte</option>';
      else
        echo '<option value="date_exacte" >Date exacte</option>';
      foreach($g4p_date_modif1 as $a_modif_key=>$a_modif_value)
      {
        if(!@empty($g4p_date->date['exact']['type']) and $a_modif_key==$g4p_date->date['exact']['type'])
          $selected='selected="selected"';
        else
          $selected='';
        echo '<option value="',$a_modif_key,'" ',$selected,'>',$a_modif_value,'</option>';
      }
      echo '</select> ';

      echo '<select name="date_exacte_calendrier" style="width:auto">';
      echo '<option value="" ></option>';
      foreach($g4p_liste_calendrier as $a_modif_key=>$a_modif_value)
      {
        if(!@empty($g4p_date->date['exact']['calendar']) and $a_modif_key==$g4p_date->date['exact']['calendar'])
          $selected='selected="selected"';
        else
          $selected='';
        echo '<option value="',$a_modif_key,'" ',$selected,'>',$a_modif_value,'</option>';
      }
      echo '</select> ';

      echo '<input type="text" name="date_exacte_jour" value="'.@$g4p_date->date['exact']['day'].'" style="width:4em" /> ';

      $tmp=array_merge($liste_mois_gregorien,$liste_mois_francais,$liste_mois_hebreux);
      echo '<select name="date_exacte_mois" id="date_exate_mois" style="width:auto">';
      echo '<option value="" ></option>';
      foreach($tmp as $a_modif_key=>$a_modif_value)
      {
        if(!@empty($g4p_date->date['exact']['month']) and $a_modif_key==$g4p_date->date['exact']['month'])
          $selected='selected="selected"';
        else
          $selected='';
        echo '<option value="',$a_modif_key,'" ',$selected,'>',$a_modif_value,'</option>';
      }
      echo '</select>';

      echo ' <input type="text" name="date_exacte_annee" value="'.@$g4p_date->date['exact']['year'].'" style="width:4em" />';

	  echo '<br /><br /></p>';
      //echo '<br /><br />Description : <input type="text" value="'.$g4p_events['description'].'" name="g4p_date_description" /></p>';
    }


    //fin date
  }
  
function g4p_error($a)
{
	echo $a;
	exit;
}

function g4p_show_events($events)
{
	echo '<div class="evenements">';
	//$g4p_indi->events=array_column_sort($g4p_indi->events,'jd_count');
	foreach($events as $g4p_a_ievents)
	{
		if($g4p_a_ievents->details_descriptor)
			$g4p_tmp=' ('.$g4p_a_ievents->details_descriptor.')';
		else
			$g4p_tmp='';
		echo '<em>',$g4p_tag_def[$g4p_a_ievents->tag],$g4p_tmp,'&nbsp;:
			</em><span class="date">',g4p_date($g4p_a_ievents->gedcom_date),'</span>', ($g4p_a_ievents->place->g4p_formated_place($_SESSION['place']))?(' ('.$g4p_langue['index_lieu'].$g4p_a_ievents->place->g4p_formated_place($_SESSION['place']).') '):('');
		echo (isset($g4p_a_ievents->sources))?(' <span style="color:blue; font-size:x-small;">-S-</span> '):('');
		echo (isset($g4p_a_ievents->notes))?(' <span style="color:blue; font-size:x-small;">-N-</span> '):('');
		echo (isset($g4p_a_ievents->medias))?(' <span style="color:blue; font-size:x-small;">-M-</span> '):('');
		echo (isset($g4p_a_ievents->asso))?(' <span style="color:blue; font-size:x-small;">-T-</span> '):('');
		echo (isset($g4p_a_ievents->id))?(' <a href="'.g4p_make_url('','detail_event.php','parent=FAM&amp;id_parent='.$g4p_a_ievents->id,0).'" class="noprint">'.$g4p_langue['detail'].'</a><br />'):('<br />');
	}
	echo '</div>';
}

function g4p_http_not_modifed()
{
    global $g4p_chemin;
    
    // On économise le serveur, pas la peine de renvoyer la page si celle en cache dans le navigateur est la bonne
    header('Date: ' . gmdate("D, d M Y H:i:s") . ' GMT');
    header('Cache-Control: Public, must-revalidate');
    header('Pragma: public');
    header("ETag: ".md5($_SESSION['g4p_id_membre'].$_SESSION['langue'].$_SESSION['theme'].serialize($_SESSION['place'])));

    if(file_exists($g4p_chemin.'cache/'.$_SESSION['genea_db_nom'].'/indi_'.$_GET['id_pers'].'.txt'))
    {
        // date de dernière modif
        $modif = gmdate('D, d M Y H:i:s', filemtime($g4p_chemin.'cache/'.$_SESSION['genea_db_nom'].'/indi_'.$_GET['id_pers'].'.txt')) ;
        header("Last-Modified: $modif GMT");
        // on vérifie si le contenu a changé
        //Je compare la date de génération du cache et de la page
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) and (strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'],0,29)))>=gmdate('U', filemtime($g4p_chemin.'cache/'.$_SESSION['genea_db_nom'].'/indi_'.$_GET['id_pers'].'.txt')))
        {		
            //Je compare un hash du membre et du etag de la page, 
            if(isset($_SERVER['HTTP_IF_NONE_MATCH']) and 
                $_SERVER['HTTP_IF_NONE_MATCH']==md5($_SESSION['g4p_id_membre'].$_SESSION['langue'].$_SESSION['theme'].serialize($_SESSION['place'])))
            {
                //Rien à chnagé, un petit 304, si la config est ok...
                if(!empty($g4p_config['g4p_httpcache']))
                {
                    header('Not Modified', TRUE, 304);
                    exit;
                }
            }
        }
    }

    // on enregistre la date de la page pour pouvoir vérifier ultérieurement si les données sont à jour
    $modif = gmdate('D, d M Y H:i:s', @filemtime($g4p_chemin.'cache/'.$_SESSION['genea_db_nom'].'/indi_'.$g4p_indi->indi_id.'.txt')) ;
    header("Last-Modified: $modif GMT");

}

function g4p_link_nom($indi)
{
    //echo '<pre>'; print_r($indi);
    //var_dump($indi);
    
    if(!is_object($indi))
	$indi=g4p_load_indi_infos($indi);
       
    $return='<a class="link_nom" href="'.g4p_make_url('','fiche_individuelle.php','id_pers='.$indi->indi_id,'fiche-'.$indi->base.'-'.g4p_prepare_varurl($indi->nom).'-'
        .g4p_prepare_varurl($indi->prenom).'-'.$indi->indi_id).'">'
        .$indi->nom.' '
        .$indi->prenom.'</a>';
    $tmp=$indi->date_rapide();
    if(!empty($tmp)) $return.=' <span class="petit">'.$tmp.'</span>';
    return $return;
}

// http://uk.php.net/manual/en/function.array-search.php
// search haystack for needle and return an array of the key path,
// FALSE otherwise.
// if NeedleKey is given, return only for this key
// mixed ArraySearchRecursive(mixed Needle,array Haystack
//                            [,NeedleKey[,bool Strict[,array Path]]])
function ArraySearchRecursive($Needle,$Haystack,$NeedleKey="", $Strict=false,$Path=array()) 
{
    if(!is_array($Haystack))
        return false;
    foreach($Haystack as $Key => $Val) 
    {
        if(is_array($Val) && $SubPath=ArraySearchRecursive($Needle,$Val,$NeedleKey, $Strict,$Path)) 
        {
            $Path=array_merge($Path,Array($Key),$SubPath);
            return $Path;
        }
        elseif((!$Strict&&$Val==$Needle && $Key==(strlen($NeedleKey)>0?$NeedleKey:$Key)) ||
            ($Strict&&$Val===$Needle && $Key==(strlen($NeedleKey)>0?$NeedleKey:$Key))) 
        {
            $Path[]=$Key;
            return $Path;
        }
    }
    return false;
}


// http://codeigniter.com/forums/viewthread/51788/P30/
function word_limiter($str, $limit = 100, $end_char = '&#8230;') {
    
    // Don't bother about empty strings.
    // Get rid of them here because the regex below would match them too.
    if (trim($str) == '')
        return $str;
    
    // Added the initial \s* in order to make the regex work in case $str starts with whitespace.
    // Without it a string like " test" would be counted for two words instead of one.

    // This HIGHLY OPTIMIZED regexp pattern says:
    // anchor the pattern to the beginning of the string "^",
    // then look for any number of space chars "\s*"
    // (but, b/c they are outside the parens, don't
    // include them in the repetition count of the next sub-pattern)
    // then use a non-capturing sub-pattern "(?:)"
    // ( "?:" tells preg not to make a $matches[1] even though there is a parentheses)
    // and look for at least one non-space char followed by 0 or 1 space char "\S+\s*"
    // there must be at least 1 and not more than $limit
    // repetitions of this non-capturing sub-pattern "{1,x}"
    preg_match('/^\s*+(?:\S++\s*+){1,'. (int) $limit .'}/', $str, $matches); 
    
    // Only add end character if the string got chopped off.
    if (strlen($matches[0]) == strlen($str))
        $end_char = '';
    
    // Chop off trailing whitespace and add the end character.
    return rtrim($matches[0]) . $end_char;
} 

function g4p_birth_death($timestamp)
{
    global $g4p_langue;
    
    if(!empty($timestamp))
        return '<span class="petit">'.sprintf($g4p_langue['index_chan'],g4p_strftime($g4p_langue['date_complete'], strtotime($timestamp))).'</span>';
}

function g4p_affiche_adresse($adresse)
{
    global $g4p_chemin, $g4p_langue;
    if(!empty($adresse))
    {
        if($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
        {
            echo '<div class="menu_interne"><a href="'.
                g4p_make_url('admin','index.php','g4p_opt=mod_note&amp;g4p_id='.''.'&amp;g4p_lien='.
                '',0),'" class="admin">Modifier l\'adresse</a>';
            if(isset($_SESSION['permission']) and ($_SESSION['permission']->permission[_PERM_SUPPR_FILES_]))
                echo ' - <a href="'.g4p_make_url('admin','exec.php','g4p_opt=suppr_note&amp;g4p_id='.
                    ''.'&amp;g4p_lien='.''.'&amp;g4p_type='.'',0),'"  onclick=" return confirme(this, \'',$g4p_langue['menu_sppr_confirme'],'\')" class="admin">',
                    $g4p_langue['Supprimer'],'</a>';
            echo '</div>';
        }
        echo '<div class="box">'."\n";
        echo '<div class="box_title">Adresse</div>'."\n";
        echo '<dl class="collapsed">'."\n";

        if(!empty($adresse->addr))
            echo '<dt>',$g4p_langue['sys_function_sour_depot_addr'],'</dt><dd>',$adresse->addr,'</dd>';
        if(!empty($adresse->city))
            echo '<dt>',$g4p_langue['sys_function_sour_depot_ville'],'</dt><dd>',$adresse->city,'</dd>';
        if(!empty($adresse->post))
            echo '<dt>',$g4p_langue['sys_function_sour_depot_cp'],'</dt><dd>',$adresse->post,'</dd>';
        if(!empty($adresse->ctry))
            echo '<dt>',$g4p_langue['sys_function_sour_depot_pays'],'</dt><dd>',$adresse->ctry,'</dd>';
        if(!empty($adresse->phon1))
            echo '<dt>',$g4p_langue['sys_function_sour_depot_tel'],' 1 : ','</dt><dd>',$adresse->phon1,'</dd>';
        if(!empty($adresse->phon2))
            echo '<dt>',$g4p_langue['sys_function_sour_depot_tel'],' 2 : ','</dt><dd>',$adresse->phon2,'</dd>';
        if(!empty($adresse->phon3))
            echo '<dt>',$g4p_langue['sys_function_sour_depot_tel'],' 3 : ','</dt><dd>',$adresse->phon3,'</dd>';
        echo '</dl></div>';
    }
}

function g4p_forbidden_access($g4p_indi)
{
    global $g4p_chemin, $g4p_langue, $g4p_config, $time_start, $g4p_mysqli;

    if(!$_SESSION['permission']->permission[_PERM_MASK_INDI_] and ($g4p_indi->resn=='privacy' or $g4p_indi->resn=='confidential'))
    {
        require_once($g4p_chemin.'entete.php');
        echo '<div class="box_title"><h2>'.$g4p_indi->prenom,' ',$g4p_indi->nom.'</h2></div>'."\n";
        echo '<strong>Vous n\'êtes pas autorisé à visualiser cette fiche</strong>';
        require_once($g4p_chemin.'pied_de_page.php');
        exit;
    }
}

function g4p_base_namefolder($base)
{
    $base = iconv("utf-8", "us-ascii//TRANSLIT", $base); // TRANSLIT does the whole job
    $base = strtolower($base);
    $base = preg_replace('~[^-a-z0-9_]+~', '', $base); // keep only letters, numbers, '_' and separator
    return $base;
}

//http://us2.php.net/manual/en/function.rmdir.php
    function deleteDirectory($dir) {
        //if (!file_exists($dir)) return true;
        if (!is_dir($dir)) 
        {
            if(is_file($dir))
                return unlink($dir);
            else
                return false;
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!deleteDirectory($dir.DIRECTORY_SEPARATOR.$item)) return false;
        }
        return rmdir($dir);
    }

#http://nashruddin.com/Remove_Directories_Recursively_with_PHP
//~ function deleteDirectory($dir) {
    //~ if(!is_dir($dir))
        //~ return false;
    //~ $files = scandir($dir);
    //~ array_shift($files);    // remove '.' from array
    //~ array_shift($files);    // remove '..' from array

    //~ foreach ($files as $file) {
        //~ $file = $dir . '/' . $file;
        //~ if (is_dir($file)) {
            //~ rmdir_recursive($file);
            //~ rmdir($file);
        //~ } else {
            //~ unlink($file);
        //~ }
    //~ }
    //~ if(rmdir($dir))
        //~ return true;
    //~ else
        //~ return false;
//~ }
?>