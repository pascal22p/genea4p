<?php
function g4p_getmysql_ids()
{
    global $g4p_start_id, $g4p_mysql_tables, $g4p_mysqli;
    
    //table list:
    $g4p_mysql_tables=array();
    $g4p_mysql_tables[]='genea_download';
    $g4p_mysql_tables[]='genea_address'; 
    $g4p_mysql_tables[]='genea_events_details';
    $g4p_mysql_tables[]='genea_familles';
    $g4p_mysql_tables[]='genea_individuals';
    $g4p_mysql_tables[]='genea_infos';
    $g4p_mysql_tables[]='genea_membres';
    $g4p_mysql_tables[]='genea_multimedia';
    $g4p_mysql_tables[]='genea_notes';
    $g4p_mysql_tables[]='genea_permissions';
    $g4p_mysql_tables[]='genea_place';
    $g4p_mysql_tables[]='genea_repository';
    $g4p_mysql_tables[]='genea_sour_citations'; 	 	
    $g4p_mysql_tables[]='genea_sour_records';
    $g4p_mysql_tables[]='genea_submitters';
    $g4p_mysql_tables[]='genea_refn';

    $return=array();

    //recherche des ids
    $sql="SHOW TABLE STATUS ";//recuperation des prochain auto_increment
    $query=$g4p_mysqli->g4p_query($sql);
    $g4p_results=$g4p_mysqli->g4p_result($query,'Name');

    foreach($g4p_mysql_tables as $ma_table)
    {
        //lock en lecture-ecriture des tables pour les nouvelles valeurs uniquement
        if(isset($g4p_results[$ma_table]))
        {
            //recup 
            if(empty($g4p_results[$ma_table]['Auto_increment']))
                $g4p_start_id[$ma_table]=0;
            else
                $g4p_start_id[$ma_table]=$g4p_results[$ma_table]['Auto_increment'];
            //lock de la table pour éviter les mauvaises surprises (sur les nouvelles valeurs uniquement)
            $sql="SHOW COLUMNS FROM ".$ma_table." WHERE `Key`='PRI'";
            $query=$g4p_mysqli->g4p_query($sql);
            $g4p_results2=$g4p_mysqli->g4p_result($query);
            $return[]="SELECT ".$g4p_results2[0]['Field']." FROM `".$ma_table."` WHERE ".$g4p_results2[0]['Field'].">".$g4p_start_id[$ma_table]." LOCK IN SHARE MODE"; 
        }
    }

    //on relit les autocincréments au cas ou entre les 2 requètes il y ait eut des requètes
    $sql="SHOW TABLE STATUS ";//recuperation des prochain auto_increment
    $query=$g4p_mysqli->g4p_query($sql);
    $g4p_results=$g4p_mysqli->g4p_result($query,'Name');

    foreach($g4p_mysql_tables as $ma_table)
    {
        //lock en lecture-ecriture des tables pour les nouvelles valeurs uniquement
        if(isset($g4p_results[$ma_table]))
        {
            //recup 
            if(empty($g4p_results[$ma_table]['Auto_increment']))
                $g4p_start_id[$ma_table]=0;
            else
                $g4p_start_id[$ma_table]=$g4p_results[$ma_table]['Auto_increment'];
            //lock de la table pour éviter les mauvaises surprises (sur les nouvelles valeurs uniquement)
            $sql="SHOW COLUMNS FROM ".$ma_table." WHERE `Key`='PRI'";
            $query=$g4p_mysqli->g4p_query($sql);
            $g4p_results2=$g4p_mysqli->g4p_result($query);
            $return[]="SELECT @".$ma_table.":=IFNULL(max(".$g4p_results2[0]['Field']."),0) FROM `".$ma_table."` "; 
        }
    }
    return $return;
}

function g4p_gedcom_check_charset($g4p_gedcom)
{
    global $log, $g4p_charset;
    //search for charset
    while (!feof($g4p_gedcom))
    {
        $g4p_ligne=trim(fgets($g4p_gedcom));
        if(stripos($g4p_ligne,'1 CHAR')!==false)
        {
            if(stripos($g4p_ligne,'ANSEL')!==false)
            {
                $g4p_charset='ANSEL';
                $log[]='Warning: ANSEL charset detected';
                break;
            }
            elseif(stripos($g4p_ligne,'UTF8')!==false or stripos($g4p_ligne,'UTF-8')!==false)
            {
                $g4p_charset='UTF8';
                $log[]='Warning: UTF8 charset detected';
                break;
            }
            elseif(stripos($g4p_ligne,'ANSI')!==false or stripos($g4p_ligne,'ASCII')!==false)
            {
                $g4p_charset='ANSI';
                $log[]='Warning: ANSI charset detected';
                break;
            }
            else
            {
                $log[]='Warning: Unknown charset';
                $g4p_charset='ANSI';
                break;
            }
        }
    }
    rewind($g4p_gedcom); 

    if(empty($g4p_charset))
    {
        $log[]='Warning: charset not detected, use ANSI by default';
        $g4p_charset='ANSI';
    } 
        
    return $g4p_charset;
}

function g4p_gedcom_saveplace()
{
    global $g4p_base, $g4p_start_id, $g4p_requetes;

    //Je trie en fonction des colonnes à ignorer de celles à importer
    $ignore=array();
    $g4p_requetes['places']=array();
    
    foreach($_POST['g4p_entete_lieux'] as $key=>$alieu)
    {
        if(empty($alieu))
            $ignore[$key]=$key;
        else
            $include[$key]=$alieu;
    }
           
    $tmp=array_flip($include);
    if(count($tmp)!=count($include))
        die('Error: Un lieu ne peut être assigner 2 fois');       
        
    $colonnes='(`place_id`, `base`, `'.implode('`,`',g4p_protect_var($include)).'`)';
    
    $i=0;
    foreach($_SESSION['liste_place'] as $id=>$aplace)
    {
        $avalues=array();
        foreach($aplace as $key=>$aaplace)
        {
            if(!isset($ignore[$key]))
                $avalues[]=g4p_protect_var($aaplace);
        }
        $i++;
        $values[]='('.$id.','.$g4p_base.',"'.implode('","',$avalues).'")';
        //echo '<pre>'; print_r($avalues);
        if($i%250==0)
        {
            $values=implode(', ',$values);
            $g4p_requetes['places'][]='INSERT INTO genea_place '.$colonnes.' VALUES '.$values;
            $values=array();
        }
    }
    $values=implode(', ',$values);
    $g4p_requetes['places'][]='INSERT INTO genea_place '.$colonnes.' VALUES '.$values;
}

function g4p_gedcom2sql($file)
{
    global $g4p_requetes, $g4p_gedcom, $g4p_base, $g4p_ligne, $g4p_links;
    global $log, $g4p_chemin, $g4p_store_ids;

    $g4p_start_id=array();
    $g4p_links=array();
    $g4p_ligne_cpt=0;
    $g4p_charset=''; //charset of gedcom file
    $g4p_ligne=''; //current gedcom line
    $g4p_arbre=array(); //hierarchy of tags
    $g4p_requetes=array(); // liste de requetes, j'insere tout à la fin dans une belle transaction
    //g4p_db_connect();

    if(preg_match('/\.zip$/i',$file))
    {
        $zip = new ZipArchive;
        if ($zip->open($g4p_chemin.'gedcoms/'.$file) === TRUE) 
        {
            mkdir($g4p_chemin.'gedcoms/temp');
            $zip->extractTo($g4p_chemin.'gedcoms/temp/');
            $zip->close();
            
            $filename=glob("temp/*.ged");
            $file=$filename[0];
        }
        else
            die('Error while opening zip file');
    }

    if (!$g4p_gedcom=fopen($g4p_chemin.'gedcoms/'.$file, 'r'))
    {
        $log[]='Error: impossible to open gedcom file';
        print_r($log);
        exit;
    }

    $g4p_charset=g4p_gedcom_check_charset($g4p_gedcom);

    $g4p_ligne=g4p_fgets($g4p_gedcom);
    while ($g4p_ligne!==false) //read gedcom file
    {
        if(substr($g4p_ligne,0,1)=='0')
        {
            if(stripos($g4p_ligne,'HEAD'))
            {
                $bloc=g4p_loadmainbloc('HEAD');
                continue;
            }
            if(stripos($g4p_ligne,'TRLR'))
                break;
            
            if(preg_match( "/^0? ?@([^@]+)@? ?([_a-zA-Z]{1,31})/" , $g4p_ligne, $g4p_preg_result))
            {
                $bloc=g4p_loadmainbloc($g4p_preg_result[2]);
                //print_r($bloc);
                switch($g4p_preg_result[2])
                {
                    case 'INDI':
                     //echo '----------------';
                     //               print_r($bloc);
                    //                echo '----------------';
                    g4p_load_indi($bloc);
                    break;

                    case 'FAM':
                     //echo '----------------';
                     //               print_r($bloc);
                    //                echo '----------------';
                    g4p_load_fam($bloc);
                    break;
                    
                    case 'SOUR':
                     //echo '----------------';
                     //               print_r($bloc);
                    //                echo '----------------';
                    g4p_load_sour($bloc);
                    break;

                    case 'REPO':
                     //echo '----------------';
                     //               print_r($bloc);
                    //                echo '----------------';
                    g4p_load_repo($bloc);
                    break;

                    case 'OBJE':
                     //echo '----------------';
                     //               print_r($bloc);
                    //                echo '----------------';
                    g4p_load_obje($bloc);
                    break;

                    case 'NOTE':
                     //echo '----------------';
                     //               print_r($bloc);
                    //                echo '----------------';
                    g4p_load_note($bloc);
                    break;

                    default:
                    //echo '0 <br />';
                    $log[]='Bloc 0 inconnu : '.$g4p_preg_result[2];
                    //print_r($bloc);
                    //exit;
                    break;
                }
                continue;
            }
            else
            {
                $log[]='<b>Bloc 0 inconnu et non conforme</b>'.$g4p_ligne;
            }
        }
        else
        {
            $log[]='<b>ligne non attendue ici </b>'.$g4p_ligne;
        }
        $g4p_ligne=g4p_fgets($g4p_gedcom);
    } //fin lecture gedcom

    // Rajoutes une entrée dans la table famille_indi à partir des relations trouvé dans les évènements.
    if(!empty($g4p_links['rel_events_famc']))
    {
        foreach($g4p_links['rel_events_famc'] as $event_id=>$fam)
        {
            $indi_id=ArraySearchRecursive($event_id, $g4p_links['rel_indi_events']);
            if(!empty($fam[0]['adop']))
            {
                if(isset($g4p_links['rel_familles_indi'][$indi_id[0]]) and $indi_id2=ArraySearchRecursive($fam[0]['fam_id'], $g4p_links['rel_familles_indi'][$indi_id[0]]))
                    $g4p_links['rel_familles_indi'][$indi_id[0]][$indi_id2[0]]=array('id'=>$fam[0]['fam_id'], 'pedi'=>'adopted');
                else
                    $g4p_links['rel_familles_indi'][$indi_id[0]][]=array('id'=>$fam[0]['fam_id'], 'pedi'=>'adopted');
            }
        }
        unset($g4p_links['rel_events_famc']);
    }
    
    foreach($g4p_links as $key=>$value)
    {
        switch($key)
        {
            case 'rel_indi_events':
            $sql=array();
            $i=0;
            foreach($value as $indi_id=>$events)
            {
                foreach($events as $aevent)
                {
                    $i++;
                    $sql[]='('.$indi_id.','.$aevent['id'].',"'.$aevent['tag'].'")';
                    if($i%250==0)
                    {
                        $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_indi_events (indi_id, events_details_id, events_tag) VALUES '.implode(',',$sql);
                        $sql=array();
                    }
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_indi_events (indi_id, events_details_id, events_tag) VALUES '.implode(',',$sql);
            break;

            case 'rel_familles_indi':
            $sql=array();
            $i=0;
            foreach($value as $indi_id=>$famc)
            {
                foreach($famc as $afamc)
                {
                    $i++;
                    //verif si la famille et l'indi existe bien...
                    if(empty($g4p_store_ids['genea_familles'][$afamc['id']]) or empty($g4p_store_ids['genea_individuals'][$indi_id]))
                    {
                        $log[]='Warning : La relation (1) '.$afamc['id'].'-'.$indi_id.' est incorrecte, lien ignoré';
                    }
                    else
                    {
                        $sql[]='('.$indi_id.','.$afamc['id'].',"'.$afamc['pedi'].'")';
                        if($i%250==0)
                        {
                            $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_familles_indi (indi_id, familles_id,rela_type) VALUES '.implode(',',$sql);
                            $sql=array();
                        }                    
                    }
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_familles_indi (indi_id, familles_id,rela_type) VALUES '.implode(',',$sql);
            break;
            
            case 'rel_famille_events':
            $sql=array();
            $i=0;
            foreach($value as $fam_id=>$famc)
            {
                foreach($famc as $aevent)
                {
                    $i++;
                    $sql[]='('.$fam_id.','.$aevent['id'].',"'.$aevent['tag'].'")';
                    if($i%250==0)
                    {
                        $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_familles_events (familles_id, events_details_id, events_tag) VALUES '.implode(',',$sql);
                        $sql=array();
                    }                     
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_familles_events (familles_id, events_details_id, events_tag) VALUES '.implode(',',$sql);
            break;
            
            case 'rel_indi_attributes':
            $sql=array();
            $i=0;
            foreach($value as $indi_id=>$attributes)
            {
                foreach($attributes as $attribute)
                {
                    $i++;
                    $sql[]='('.$indi_id.','.$attribute['id'].',"'.$attribute['tag'].'","'.g4p_protect_var($attribute['descr']).'")';
                    if($i%250==0)
                    {
                        $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_indi_attributes (indi_id, events_details_id, events_tag, events_descr) VALUES '.implode(',',$sql);
                        $sql=array();
                    }                          
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_indi_attributes (indi_id, events_details_id, events_tag, events_descr) VALUES '.implode(',',$sql);
            break;

            case 'rel_indi_sources':
            $sql=array();
            $i=0;
            foreach($value as $indi_id=>$sources)
            {
                foreach($sources as $source)
                {
                    $i++;
                    if(!empty($source))
                        $sql[]='('.$indi_id.','.$source.')';
                    if($i%250==0)
                    {
                        $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_indi_sources (indi_id, sour_citations_id) VALUES '.implode(',',$sql);
                        $sql=array();
                    }                           
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_indi_sources (indi_id, sour_citations_id) VALUES '.implode(',',$sql);
            break;

            case 'rel_indi_refn':
            $sql=array();
            $i=0;
            foreach($value as $indi_id=>$refns)
            {
                foreach($refns as $refn)
                {
                    $i++;
                    if(!empty($refn))
                        $sql[]='('.$indi_id.','.$refn.')';
                    if($i%250==0)
                    {
                        $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_indi_refn (indi_id, refn_id) VALUES '.implode(',',$sql);
                        $sql=array();
                    }                           
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_indi_refn (indi_id, refn_id) VALUES '.implode(',',$sql);
            break;

            case 'rel_familles_sources':
            $sql=array();
            $i=0;
            foreach($value as $fam_id=>$sources)
            {
                foreach($sources as $source)
                {
                    $i++;
                    if(!empty($source))
                        $sql[]='('.$fam_id.','.$source.')';
                    if($i%250==0)
                    {
                        $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_familles_sources (familles_id, sour_citations_id) VALUES '.implode(',',$sql);
                        $sql=array();
                    }                                               
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_familles_sources (familles_id, sour_citations_id) VALUES '.implode(',',$sql);
            break;

            case 'rel_repo_notes':
            $sql=array();
            $i=0;
            foreach($value as $repo_id=>$notes)
            {
                foreach($notes as $anote)
                {
                    $i++;
					if(empty($g4p_store_ids['genea_repository'][$repo_id]) or empty($g4p_store_ids['genea_notes'][$anote]))                                        
                    {
                        $log[]='Warning : La relation (3) '.$repo_id.'-'.$anote.' est incorrecte, lien ignoré';
                    }
                    else
                    {                                        
                        $sql[]='('.$repo_id.','.$anote.')';
                        if($i%250==0)
                        {
                            $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_repo_notes (repo_id, notes_id) VALUES '.implode(',',$sql);
                            $sql=array();
                        }                               
                    }
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_repo_notes (repo_id, notes_id) VALUES '.implode(',',$sql);
            break;

            case 'rel_sour_citations_notes':
            $sql=array();
            $i=0;
            foreach($value as $sour_id=>$notes)
            {
                foreach($notes as $anote)
                {
                    $i++;
                    if(!empty($anote))
                        $sql[]='('.$sour_id.','.$anote.')';
                    if($i%250==0)
                    {
                        $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_sour_citations_notes (sour_citations_id, notes_id) VALUES '.implode(',',$sql);
                        $sql=array();
                    }                              
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_sour_citations_notes (sour_citations_id, notes_id) VALUES '.implode(',',$sql);
            break;

            case 'rel_indi_notes':
            $sql=array();
            $i=0;
            foreach($value as $indi_id=>$notes)
            {
                foreach($notes as $anote)
                {
                    $i++;
 					if(empty($g4p_store_ids['genea_individuals'][$indi_id]) or empty($g4p_store_ids['genea_notes'][$anote]))                                        
					{
                        $log[]='Warning : La relation (4) '.$indi_id.'-'.$anote.' est incorrecte, lien ignoré';
                    }
                    else
                    {                                        
                        $sql[]='('.$indi_id.','.$anote.')';
                        if($i%250==0)
                        {
                            $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_indi_notes (indi_id, notes_id) VALUES '.implode(',',$sql);
                            $sql=array();
                        }                                                      
                    }
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_indi_notes (indi_id, notes_id) VALUES '.implode(',',$sql);
            break;

            case 'rel_familles_notes':
            $sql=array();
            $i=0;
            foreach($value as $fam_id=>$notes)
            {
                foreach($notes as $anote)
                {
                    $i++;
 					if(empty($g4p_store_ids['genea_familles'][$fam_id]) or empty($g4p_store_ids['genea_notes'][$anote]))                                        
                    {
                        $log[]='Warning : La relation (5) '.$fam_id.'-'.$anote.' est incorrecte, lien ignoré';
                    }
                    else
                    {                                        
                        $sql[]='('.$fam_id.','.$anote.')';
                        if($i%250==0)
                        {
                            $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_familles_notes (familles_id, notes_id) VALUES '.implode(',',$sql);
                            $sql=array();
                        }                                                                              
                    }
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_familles_notes (familles_id, notes_id) VALUES '.implode(',',$sql);
            break;

            case 'rel_familles_multimedia':
            $sql=array();
            $i=0;
            foreach($value as $fam_id=>$medias)
            {
                foreach($medias as $amedia)
                {
                    $i++;
 					if(empty($g4p_store_ids['genea_multimedia'][$amedia]))                                        
                    {
                        $log[]='Warning : La relation (5 bis) '.$fam_id.'-'.$amedia.' est incorrecte, lien ignoré';
                    }
                    else
                    {                                        
                        $sql[]='('.$fam_id.','.$amedia.')';
                        if($i%250==0)
                        {
                            $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_familles_multimedia (familles_id, media_id) VALUES '.implode(',',$sql);
                            $sql=array();
                        }                                                                              
                    }
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_familles_multimedia (familles_id, media_id) VALUES '.implode(',',$sql);
            break;

            case 'rel_place_notes':
            $sql=array();
            $i=0;
            foreach($value as $place_id=>$notes)
            {
                foreach($notes as $anote)
                {
                    $i++;
 					if(empty($g4p_store_ids['genea_notes'][$anote]))                                        
                    {
                        $log[]='Warning : La relation (6) '.$place_id.'-'.$anote.' est incorrecte, lien ignoré';
                    }
                    else
                    {                                        
                        $sql[]='('.$place_id.','.$anote.')';
                        if($i%250==0)
                        {
                            $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_place_notes (place_id, notes_id) VALUES '.implode(',',$sql);
                            $sql=array();
                        }                                                                              
                    }
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_place_notes (place_id, notes_id) VALUES '.implode(',',$sql);
            break;

            case 'rel_events_notes':
            $sql=array();
            $i=0;
            foreach($value as $event_id=>$notes)
            {
                foreach($notes as $anote)
                {
                    $i++;
 					if(empty($g4p_store_ids['genea_notes'][$anote]))                                        
                    {
                        $log[]='Warning : La relation (7) '.$event_id.'-'.$anote.' est incorrecte, lien ignoré';
                    }
                    else
                    {                                        
                        $sql[]='('.$event_id.','.$anote.')';
                        if($i%250==0)
                        {
                            $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_events_notes (events_details_id, notes_id) VALUES '.implode(',',$sql);
                            $sql=array();
                        }                                                                              
                    }
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_events_notes (events_details_id, notes_id) VALUES '.implode(',',$sql);
            break;

            case 'rel_events_multimedia':
            $sql=array();
            $i=0;
            foreach($value as $event_id=>$medias)
            {
                foreach($medias as $amedia)
                {
                    $i++;
                    if(!empty($amedia))
                        $sql[]='('.$event_id.','.$amedia.')';
                    if($i%250==0)
                    {
                        $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_events_multimedia (events_details_id, media_id) VALUES '.implode(',',$sql);
                        $sql=array();
                    }                                                                              
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_events_multimedia (events_details_id, media_id) VALUES '.implode(',',$sql);
            break;

            case 'rel_events_sources':
            $sql=array();
            $i=0;
            foreach($value as $event_id=>$sources)
            {
                foreach($sources as $asource)
                {
                    $i++;
                    if(!empty($asource))
                        $sql[]='('.$event_id.','.$asource.')';
                    if($i%250==0)
                    {
                        $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_events_sources (events_details_id, sour_citations_id) VALUES '.implode(',',$sql);
                        $sql=array();
                    }                                                                              
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_events_sources (events_details_id, sour_citations_id) VALUES '.implode(',',$sql);
            break;

            case 'rel_indi_multimedia':
            $sql=array();
            $i=0;
            foreach($value as $indi_id=>$medias)
            {
                foreach($medias as $amedia)
                {
                    $i++;
 					if(empty($g4p_store_ids['genea_individuals'][$indi_id]) or empty($g4p_store_ids['genea_multimedia'][$amedia]))                                        
                    {
                        $log[]='Warning : La relation (8) '.$indi_id.'-'.$amedia.' est incorrecte, lien ignoré';
                    }
                    else
                    {                                        
                        $sql[]='('.$indi_id.','.$amedia.')';
                        if($i%250==0)
                        {
                            $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_indi_multimedia (indi_id, media_id) VALUES '.implode(',',$sql);
                            $sql=array();
                        }                                                                              
                    }
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_indi_multimedia (indi_id, media_id) VALUES '.implode(',',$sql);
            break;

            case 'rel_sour_citations_multimedia':
            $sql=array();
            $i=0;
            foreach($value as $sour_id=>$medias)
            {
                foreach($medias as $amedia)
                {
                    $i++;
 					if(empty($g4p_store_ids['genea_multimedia'][$amedia]))                                        
                    {
                        $log[]='Warning : La relation (9) '.$sour_id.'-'.$amedia.' est incorrecte, lien ignoré';
                    }
                    else
                    {
                        $sql[]='('.$sour_id.','.$amedia.')';
                        if($i%250==0)
                        {
                            $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_sour_citations_multimedia (sour_citations_id, media_id) VALUES '.implode(',',$sql);
                            $sql=array();
                        }                                                                              
                    }
                }
            }
            if(!empty($sql))
                $g4p_requetes['relations'][]='INSERT IGNORE INTO rel_sour_citations_multimedia (sour_citations_id, media_id) VALUES '.implode(',',$sql);
            break;

            default:
            echo '2 <br />';
            echo $key;
            print_r($value);
            break;
        }
    }
}

function g4p_check_import()
{
    global $log, $g4p_db_lieux, $g4p_mysqli, $g4p_base;

    echo '<h1>Le log</h1>';
    echo '<div style="width:100%; height:400px;overflow:auto; font-size:x-small;">';
    echo implode($log,"<br />");
    echo '</div>';

    echo '<h1>Organisation des lieux</h1>';
    //print_r($g4p_db_lieux);
    $cpt_lieux=0;
    foreach($g4p_db_lieux as $aplace)
    {
        $liste_place[$aplace['id']]=explode(',',$aplace['text']);
        if($cpt_lieux<count($liste_place[$aplace['id']]))
            $cpt_lieux=count($liste_place[$aplace['id']]);
    }
    foreach($liste_place as $key=>$aplace)
    {
        $tmp=count($aplace);
        if($tmp!=$cpt_lieux)
        while($tmp<$cpt_lieux)
        {
            $aplace[]='';
            $tmp++;
        }
        $liste_place[$key]=$aplace;
        $table_lieux[]='<tr><td>'.implode($aplace,'</td><td>')."</td></tr>";
    }

    $sql='SHOW COLUMNS FROM `genea_place` ';
    $result=$g4p_mysqli->g4p_query($sql);
    $g4p_results=$g4p_mysqli->g4p_result($result);
    $g4p_db_lieux='<option value=""> - </option>'."\n";
    foreach($g4p_results as $aresult)
    {
        if($aresult['Field']!='place_id' and $aresult['Field']!='base')
            $g4p_db_lieux.='<option value="'.$aresult['Field'].'">'.$aresult['Field']."</option>\n";
    }

    $g4p_db_lieux_str=array();
    for($i=0;$i<$cpt_lieux;$i++)
        $g4p_db_lieux_str[]='<td><select name="g4p_entete_lieux['.$i.']">'.$g4p_db_lieux."</select></td>";
    $g4p_db_lieux_str='<tr>'.implode($g4p_db_lieux_str,"\n").'</tr>';

    echo '<form class="formulaire" method="post" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'">';
    echo '<table>'; 
    echo $g4p_db_lieux_str;
    echo '</table>';
    echo '</div>';
    echo '<input type="hidden" name="g4p_base" value="'.$g4p_base.'" />';
    echo '<input type="submit" value="Valider et poursuivre l\'importation" /></form>';

    echo '<div style="width:100%; height:400px;overflow:auto; font-size:x-small;">';
    echo '<table>'; 
    echo implode($table_lieux,"\n");
            
    echo '</table>';
    echo '</div>';
    return $liste_place;
}

function g4p_fgets($g4p_gedcom)
{
    global $g4p_ligne_cpt, $g4p_charset;
    $g4p_ligne_cpt++;
    
    /*if($g4p_ligne_cpt==1)
    {
        echo '<br /><b>Lecture ligne '.$g4p_ligne_cpt.'</b>';
        ob_flush();
        flush();
    }
    elseif($g4p_ligne_cpt==10)
    {
        echo '<br /><b>Lecture ligne '.$g4p_ligne_cpt.'</b>';
        ob_flush();
        flush();
    }
    elseif($g4p_ligne_cpt==1000)
    {
        echo '<br /><b>Lecture ligne '.$g4p_ligne_cpt.'</b>';
        ob_flush();
        flush();
    }
    elseif($g4p_ligne_cpt==10000)
    {
        echo '<br /><b>Lecture ligne '.$g4p_ligne_cpt.'</b>';
        ob_flush();
        flush();
    }
    elseif($g4p_ligne_cpt%100000==0)
    {
        echo '<br /><b>Lecture ligne '.$g4p_ligne_cpt.'</b>';
        ob_flush();
        flush();
    }*/

    $ansel_to_ansi=array('âe'=>'é','áe'=>'è','ãe'=>'ê','èe'=>'ë','âo'=>'ó','áo'=>'ò','ão'=>'ô','èo'=>'ö','âa'=>'á','áa'=>'à','ãa'=>'â','èa'=>'ä','âu'=>'ú','áu'=>'ù','ãu'=>'û','èu'=>'ü','âi'=>'í','ái'=>'ì','ãi'=>'î','èi'=>'ï','ây'=>'ý','èy'=>'ÿ','ðc'=>'ç','~n'=>'ñ','âE'=>'É','áE'=>'È','ãE'=>'Ê','èE'=>'Ë','âO'=>'Ó','áO'=>'Ò','ãO'=>'Ô','èO'=>'Ö','âA'=>'Á','áA'=>'À','ãA'=>'Â','èA'=>'Ä','âU'=>'Ú','áU'=>'Ù','ãU'=>'Û','èU'=>'Ü','âI'=>'Í','áI'=>'Ì','ãI'=>'Î','èI'=>'Ï','âY'=>'Ý','èY'=>'Ÿ','ðC'=>'Ç','~N'=>'Ñ');
    $ansel_map=array_keys($ansel_to_ansi);
    foreach($ansel_map as $ansel_a_map_key=>$ansel_a_map_value)
      $ansel_map[$ansel_a_map_key]='/'.$ansel_a_map_value.'/';
    $ansel_replacement=array_values($ansel_to_ansi);

    if(!feof($g4p_gedcom))
    {
        $g4p_ligne=trim(fgets($g4p_gedcom));

        switch($g4p_charset) //convert line into utf8 if necessary
        {
            case 'ANSEL':
                $g4p_ligne = utf8_encode(trim($g4p_ligne));        
                $g4p_ligne= preg_replace($ansel_map,$ansel_replacement,$g4p_ligne);  
            break;

            case 'UTF8':
            case 'UTF-8':
            break;

            default:
                $g4p_ligne = utf8_encode($g4p_ligne);        
            break;
        }
         
        return $g4p_ligne;
    }
    else
    {
        return false;
    }
}

function g4p_new_id($type,$gedid=false)
{
    global $g4p_start_id;
    static $g4p_new_id=array();
    static $g4p_translates_id=array();
    
    if($type=='dummy')
        return NULL;
    else
    {
        if(!empty($gedid) and isset($g4p_translates_id[$type][$gedid]))
        {
            return $g4p_translates_id[$type][$gedid];
        }
        else
        {
            if(!isset($g4p_new_id[$type]))
                $g4p_new_id[$type]=0;
            $g4p_new_id[$type]++;
            if(!empty($gedid))
                $g4p_translates_id[$type][$gedid]='@'.$type.'+'.$g4p_new_id[$type];
            return '@'.$type.'+'.$g4p_new_id[$type];
        }
    }
}

function g4p_loadmainbloc($tag)
{
    global $g4p_ligne, $g4p_ligne_cpt, $g4p_gedcom,$log,$ged_grammar;
    $bloc=array();
    $g4p_array=array();
    $i=0;
    
    $bloc[0][0]=$g4p_ligne;
    $bloc[0]['niveau']=0;
    $bloc[0]['tag']=$tag;
    if(preg_match( "/@(.+)@/" , $g4p_ligne , $g4p_preg_result ))
        $bloc[0]['id']=$g4p_preg_result[1];
    else
        $bloc[0]['id']=NULL;
    $bloc[0]['text']='';
    $i++;
    $g4p_arbre[0]=$tag;
    //echo $g4p_ligne,'<br>';
    $g4p_ligne=g4p_fgets($g4p_gedcom);
    
    //print_r($ged_grammar);   exit;

    while ($g4p_ligne!==false) //read gedcom file
    {
        if(preg_match( "/^([1-9]) ?([_A-Z]{1,31}) ?(@.+@)? ?(.*)/" , $g4p_ligne , $g4p_preg_result ))
        {
            //est-ce que le  bloc courant est ignorée ?
            //print_r($g4p_arbre);
            $tmp=count($g4p_arbre)-1;
            while($g4p_arbre[$tmp]=='ignore' and $g4p_preg_result[1]>$tmp)
            {
                //echo $g4p_preg_result[1],' ',count($g4p_arbre)-1;
                $log[]='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; « <b>'.$g4p_ligne_cpt.'</b> : '.$g4p_ligne.' » ce bloc a été ignoré';
                $g4p_ligne=g4p_fgets($g4p_gedcom);
                continue 2; //jump to next line...
            }
            
            //verification que c'est bien du gedcom connue et supporté par le script
            //arbre
            if($g4p_preg_result[1]<$tmp)
            {
                for($j=$g4p_preg_result[1]; $j<=$tmp; $j++)
                    unset($g4p_arbre[$j]);
            }
            $g4p_arbre[$g4p_preg_result[1]]=$g4p_preg_result[2];        
    
            //verif grammaire
            //print_r($ged_grammar); exit;
            $implode=implode(':',$g4p_arbre);
            if(isset($ged_grammar[$implode]) and $ged_grammar[$implode]!=-1)
            {
                $bloc[$i][0]=$g4p_ligne;
                $bloc[$i]['niveau']=$g4p_preg_result[1];
                $bloc[$i]['tag']=$g4p_preg_result[2];
                $bloc[$i]['id']=$g4p_preg_result[3];
                $bloc[$i]['text']=$g4p_preg_result[4];
                //echo $i,' ',$bloc[$i]['tag'],"\n";
                //if($bloc[$i]['tag']=='SOUR')
                //{
                //    echo implode(':',$g4p_arbre);
                //    print_r($bloc);
                //}
                $i++;
            }
            else
            {
                $log[]='Warning : « <b>'.$g4p_ligne_cpt.'</b> : '.$g4p_ligne.' » <span style="color:red;">'.$implode.'</span> tag non supporté ou non conforme';
                //echo '$ged_grammar[\'',implode(':',$g4p_arbre),'\']=1;<br>';
                $g4p_arbre[$g4p_preg_result[1]]='ignore';
            }     
            $g4p_ligne=g4p_fgets($g4p_gedcom);
        }
        else
        {
            if(substr($g4p_ligne,0,1)=='0')
                return $bloc;
            else
            {
                echo 'Problème de structure dans le gecom<br />';
                echo $g4p_ligne_cpt.'-'.$g4p_ligne;
                exit;
            }
        }
    }
}

function g4p_load_indi($bloc)
{
    global $log, $g4p_base, $g4p_requetes, $g4p_links, $g4p_store_ids;
    static $sql_count1=0;
    static $sql_count2=0;
    
    /*
    @XREF:INDI@ INDI {1:1}
        +1 RESN <RESTRICTION_NOTICE> {0:1} p.60
        +1 <<PERSONAL_NAME_STRUCTURE>> {0:M} p.38
        +1 SEX <SEX_VALUE> {0:1} p.61
        +1 <<INDIVIDUAL_EVENT_STRUCTURE>> {0:M} p.34
        +1 <<INDIVIDUAL_ATTRIBUTE_STRUCTURE>> {0:M} p.33
        +1 <<LDS_INDIVIDUAL_ORDINANCE>> {0:M} p.35, 36
        +1 <<CHILD_TO_FAMILY_LINK>> {0:M} p.31
        +1 <<SPOUSE_TO_FAMILY_LINK>> {0:M} p.40
        +1 SUBM @<XREF:SUBM>@ {0:M} p.28
        +1 <<ASSOCIATION_STRUCTURE>> {0:M} p.31
        +1 ALIA @<XREF:INDI>@ {0:M} p.25
        +1 ANCI @<XREF:SUBM>@ {0:M} p.28
        +1 DESI @<XREF:SUBM>@ {0:M} p.28
        +1 RFN <PERMANENT_RECORD_FILE_NUMBER> {0:1} p.57
        +1 AFN <ANCESTRAL_FILE_NUMBER> {0:1} p.42
        +1 REFN <USER_REFERENCE_NUMBER> {0:M} p.63, 64
        +2 TYPE <USER_REFERENCE_TYPE> {0:1} p.64
        +1 RIN <AUTOMATED_RECORD_ID> {0:1} p.43
        +1 <<CHANGE_DATE>> {0:1} p.31
        +1 <<NOTE_STRUCTURE>> {0:M} p.37
        +1 <<SOURCE_CITATION>> {0:M} p.39
        +1 <<MULTIMEDIA_LINK>> {0:M} p.37, 26
    */
    
    //print_r($bloc);
    $indi_id=g4p_new_id('genea_individuals',$bloc[0]['id']);
    $g4p_store_ids['genea_individuals'][$indi_id]=1;
    $max=count($bloc)-1;
    $indi_events=array();
    $indi_nom=$indi_prenom=$indi_sexe='';
    $indi_npfx=$indi_givn=$indi_nick=$indi_spfx=$indi_nsfx='';
    $indi_name=array();
    $indi_resn=$indi_chan='NULL';
    
    //print_r($bloc);
    
    for($i=1;$i<=$max;$i++)
    {
        //echo $bloc[$i]['tag']."\n";
        switch(strtoupper(trim($bloc[$i]['tag'])))
        {
            case 'RESN':
            $indi_resn=g4p_restriction_notice($bloc[$i]['text']);
            break;
    
            case 'NAME':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $indi_name=g4p_personal_name_structure($sousbloc);
            break;
    
            case 'SEX':
            //M = Male
            //F = Female
            //U = Undetermined from available records and quite sure that it can’t be.
            //echo $bloc[$i]['text'], $bloc[$i]['tag'];
            $indi_sexe=strtoupper(trim($bloc[$i]['text']));
            if($indi_sexe!='F' and $indi_sexe!='M' and $indi_sexe!='U' )
            {
                $log[]='Warning: unespected value for tag SEX: '.$indi_sexe;
                $indi_sexe='';
            }
            break;
            
            //individual event
            case 'BIRT':
            case 'DEAT':
            case 'BAPM':
            case 'BURI':
            case 'ADOP':
            case 'CHR':
            case 'CHRA':
            case 'CONF':
            case 'FCOM':
            case 'CREM':
            case 'ORDN':
            case 'NATU':
            case 'EMIG':
            case 'IMMI':
            case 'PROB':
            case 'WILL':
            case 'BLES':
            case 'BARM':
            case 'BASM':
            case 'GRAD':
            case 'RETI':
            case 'CENS':
            case 'EVEN':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_indi_events'][$indi_id][]=g4p_individual_event_structure($sousbloc);
            break;
            
            case 'CAST':
            case 'DSCR':
            case 'EDUC':
            case 'IDNO':
            case 'NATI':
            case 'PROP':
            case 'RELI':
            case 'TITL':
            case 'OCCU':
            case 'RESI':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_indi_attributes'][$indi_id][]=g4p_individual_attribute_structure($sousbloc);
            break;
            
            case 'FAMC':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_familles_indi'][$indi_id][]=g4p_child_to_family_link($sousbloc);
            break;
            
            case 'FAMS':
            $sousbloc=g4p_load_bloc($bloc,$i);
            //$indi_famss[]=g4p_spouse_to_family_link($sousbloc);
            //Information récupérer depuis FAM:(WIFE|HUSB)
            break;

            case 'ASSO':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $indi_assos[]=g4p_association_structure($sousbloc);
            break;
            
            case 'ALIA':
            echo 'to be done';
            break;

            case 'RFN':
            $indi_refn=$bloc[$i]['text'];
            break;

            case 'REFN':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_indi_refn'][$indi_id][]=g4p_user_reference_number($sousbloc);
            break;

            case 'RIN':
            $indi_rin=$bloc[$i]['text'];
            break;

            case 'CHAN':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $indi_chan=g4p_chan($sousbloc);
            break;

            case 'NOTE':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_indi_notes'][$indi_id][]=g4p_note_structure($sousbloc);
            break;

            case 'SOUR':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_indi_sources'][$indi_id][]=g4p_sour_citation_structure($sousbloc);
            break;

            case 'OBJE':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_indi_multimedia'][$indi_id][]=g4p_multimedia_link($sousbloc);
            break;

            default:
            echo 'tag qui a passé à travers (load indi) ',$bloc[$i]['tag'],' ',$bloc[$i][0],' ',$bloc[$i-1][0],"\n";
            break;
            //ignore
            //+1 ANCI @<XREF:SUBM>@ {0:M} p.28
            //+1 DESI @<XREF:SUBM>@ {0:M} p.28
            //+1 AFN <ANCESTRAL_FILE_NUMBER> {0:1} p.42
            
        }
    }
    
    //construction de la requète
    //    $sql.='(indi_id, base, indi_nom, indi_prenom, indi_sexe, indi_npfx, indi_givn';
    //    $sql.='indi_nick, indi_spfx, indi_nsfx, indi_resn)';
    //print_r($indi_name);
    extract($indi_name);
    if($indi_resn!='NULL')
        $indi_resn='"'.$indi_resn.'"';
        
    if($sql_count1%250==0)
    {
        $sql_count1++;
        $sql_count2++;
        $g4p_requetes['indis'][$sql_count2]='INSERT INTO genea_individuals ';
        $g4p_requetes['indis'][$sql_count2].='(indi_id, base, indi_nom, indi_prenom, indi_sexe, indi_npfx, indi_givn, ';
        $g4p_requetes['indis'][$sql_count2].='indi_nick, indi_spfx, indi_nsfx, indi_resn, indi_timestamp) VALUES ';
        $g4p_requetes['indis'][$sql_count2].= '('.$indi_id.','.$g4p_base.',"'.g4p_protect_var($indi_nom).'","'
            .g4p_protect_var($indi_prenom).'","'.g4p_protect_var($indi_sexe).'","'
            .g4p_protect_var($indi_npfx).'","'.g4p_protect_var($indi_givn)
            .'","'.g4p_protect_var($indi_nick).'","'.g4p_protect_var($indi_spfx)
            .'","'.g4p_protect_var($indi_nsfx).'",'.$indi_resn.','.$indi_chan.')';
    }
    else
    {
        $sql_count1++;
        $g4p_requetes['indis'][$sql_count2].= ', ('.$indi_id.','.$g4p_base.',"'.g4p_protect_var($indi_nom).'","'
            .g4p_protect_var($indi_prenom).'","'.g4p_protect_var($indi_sexe).'","'
            .g4p_protect_var($indi_npfx).'","'.g4p_protect_var($indi_givn)
            .'","'.g4p_protect_var($indi_nick).'","'.g4p_protect_var($indi_spfx)
            .'","'.g4p_protect_var($indi_nsfx).'",'.$indi_resn.','.$indi_chan.')';
    }
    return 1;
}

function g4p_load_fam($bloc)
{
    global $log, $g4p_base, $g4p_requetes, $g4p_links, $g4p_store_ids;
    static $sql_count1=0;
    static $sql_count2=0;
    
    /*
            n @<XREF:FAM>@ FAM {1:1}
        +1 RESN <RESTRICTION_NOTICE> {0:1) p.60
        +1 <<FAMILY_EVENT_STRUCTURE>> {0:M} p.32
        +1 HUSB @<XREF:INDI>@ {0:1} p.25
        +1 WIFE @<XREF:INDI>@ {0:1} p.25
        +1 CHIL @<XREF:INDI>@ {0:M} p.25
        +1 NCHI <COUNT_OF_CHILDREN> {0:1} p.44
        +1 SUBM @<XREF:SUBM>@ {0:M} p.28
        +1 <<LDS_SPOUSE_SEALING>> {0:M} p.36
        +1 REFN <USER_REFERENCE_NUMBER> {0:M} p.63, 64
        25
        +2 TYPE <USER_REFERENCE_TYPE> {0:1} p.64
        +1 RIN <AUTOMATED_RECORD_ID> {0:1} p.43
        +1 <<CHANGE_DATE>> {0:1} p.31
        +1 <<NOTE_STRUCTURE>> {0:M} p.37
        +1 <<SOURCE_CITATION>> {0:M} p.39
        +1 <<MULTIMEDIA_LINK>> {0:M} p.37, 26
    */

    $fam_id=g4p_new_id('genea_familles',$bloc[0]['id']);
    $g4p_store_ids['genea_familles'][$fam_id]=1;
    $max=count($bloc)-1;
    $indi_events=array();
    $indi_sexe='';
    $indi_name=array();
    $fam_wife=$fam_husb="NULL";
    $fam_resn=$fam_chan="NULL";

    for($i=1;$i<=$max;$i++)
    {
        switch(strtoupper($bloc[$i]['tag']))
        {

            case 'RESN':
            $fam_resn=g4p_restriction_notice($bloc[$i]['text']);
            break;
            
            case 'MARR':
            case 'ENGA':
            case 'ANUL':
            case 'CENS':
            case 'DIV':
            case 'DIVF':     
            case 'MARB':
            case 'MARC':
            case 'MARL':
            case 'MARS':
            case 'RESI':
            case 'EVEN':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_famille_events'][$fam_id][]=g4p_family_event_structure($sousbloc);
            break;

            case 'HUSB':
            $fam_husb=g4p_new_id('genea_individuals',substr($bloc[$i]['id'],1,-1));
            break;
            
            case 'WIFE':
            $fam_wife=g4p_new_id('genea_individuals',substr($bloc[$i]['id'],1,-1));
            break;

            case 'REFN':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $fam_refns[]=g4p_user_reference_number($sousbloc);
            break;
            
            case 'RIN':
            $fam_rin=$bloc[$i]['text'];
            break;

            case 'CHIL':
            //$fam_chils[]=g4p_new_id('genea_individuals',substr($bloc[$i]['id'],1,-1));
            //Information récupéré depuis INDI:FAMC.
            break;

            case 'CHAN':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $fam_chan=g4p_chan($sousbloc);
            break;

            case 'NOTE':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_familles_notes'][$fam_id][]=g4p_note_structure($sousbloc);
            break;

            case 'SOUR':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_familles_sources'][$fam_id][]=g4p_sour_citation_structure($sousbloc);
            break;

            case 'OBJE':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_familles_multimedia'][$fam_id][]=g4p_multimedia_link($sousbloc);
            break;
            
            default:
            echo '3 <br />';
            print_r($bloc[$i]);
            break;
        }
    }
   
    if($fam_resn!='NULL')
        $fam_resn='"'.$fam_resn.'"';
    
    if($sql_count1%250==0)
    {
        $sql_count1++;
        $sql_count2++;
        
        $g4p_requetes['familles'][$sql_count2]='INSERT INTO genea_familles ';
        $g4p_requetes['familles'][$sql_count2].='(familles_id, base, familles_wife, familles_husb, familles_resn, familles_timestamp) VALUES ';
        $g4p_requetes['familles'][$sql_count2].= '('.$fam_id.','.$g4p_base.','.$fam_wife.','.$fam_husb.','.$fam_resn.','.$fam_chan.')';        
    }
    else
    {
        $sql_count1++;
        $g4p_requetes['familles'][$sql_count2].= ', ('.$fam_id.','.$g4p_base.','.$fam_wife.','.$fam_husb.','.$fam_resn.','.$fam_chan.')';        
    }
    
    return 1;
}


function g4p_personal_name_structure($bloc)
{
    /*
    PERSONAL_NAME_STRUCTURE:=
        n NAME <NAME_PERSONAL> {1:1} p.54
        +1 TYPE <NAME_TYPE> {0:1} p.56
        +1 <<PERSONAL_NAME_PIECES>> {0:1} p.37
        +1 FONE <NAME_PHONETIC_VARIATION> {0:M} p.55
        +2 TYPE <PHONETIC_TYPE> {1:1} p.57
        +2 <<PERSONAL_NAME_PIECES>> {0:1} p.37
        +1 ROMN <NAME_ROMANIZED_VARIATION> {0:M} p.56
        +2 TYPE <ROMANIZED_TYPE> {1:1} p.61
        +2 <<PERSONAL_NAME_PIECES>> {0:1} p.37
    */
    
        /*
                    n NPFX <NAME_PIECE_PREFIX> {0:1} p.55
            n GIVN <NAME_PIECE_GIVEN> {0:1} p.55
            n NICK <NAME_PIECE_NICKNAME> {0:1} p.55
            n SPFX <NAME_PIECE_SURNAME_PREFIX {0:1} p.56
            38
            n SURN <NAME_PIECE_SURNAME> {0:1} p.55
            n NSFX <NAME_PIECE_SUFFIX> {0:1} p.55
            n <<NOTE_STRUCTURE>> {0:M} p.37
            n <<SOURCE_CITATION>> {0:M} p.39
        */
        
    $indi_prenom='';
    $indi_nom='';
    $indi_npfx='';
    $indi_givn='';
    $indi_nick='';
    $indi_spfx='';
    $indi_nsfx='';

    $max=count($bloc)-1;

    for($i=0;$i<=$max;$i++)
    {
        switch(strtoupper($bloc[$i]['tag']))
        {
            case 'NAME':
            if(($g4p_debut=strpos($bloc[0]['text'],'/'))!==FALSE)
            {
                if(($g4p_fin=strpos($bloc[0]['text'],'/',$g4p_debut+1))!==FALSE)
                {
                    $g4p_lg=$g4p_fin-$g4p_debut-1;
                    if($g4p_lg>0)
                        $indi_nom=trim(strtoupper(substr($bloc[0]['text'],$g4p_debut+1,$g4p_lg)));
                    else
                        $indi_nom='';
      
                    $indi_prenom='';
                    if($g4p_debut>0)
                        $indi_prenom.=trim(substr($bloc[0]['text'],0,$g4p_debut));
                    if($g4p_fin<strlen($bloc[0]['text']))
                        $indi_prenom.=trim(substr($bloc[0]['text'],$g4p_fin+1));
                }
            }
            break;
            
            case 'NPFX':
            $indi_npfx=$bloc[$i]['text'];
            break;
            
            case 'GIVN':
            $indi_givn=$bloc[$i]['text'];
            break;

            case 'NICK':
            $indi_nick=$bloc[$i]['text'];
            break;

            case 'SPFX':
            $indi_spfx=$bloc[$i]['text'];
            break;

            case 'SURN':
            $indi_nom=$bloc[$i]['text'];
            break;
            
            case 'NSFX':
            $indi_nsfx=$bloc[$i]['text'];
            break;       

            default:
            echo 'tag '.$bloc[$i]['tag'].' inconnu, verifie la grammaire...';
            break;
        }
    }
    return array('indi_nom'=>$indi_nom, 'indi_prenom'=>$indi_prenom, 'indi_npfx'=>$indi_npfx, 'indi_givn'=>$indi_givn, 
    'indi_nick'=>$indi_nick, 'indi_spfx'=>$indi_spfx, 'indi_nsfx'=>$indi_nsfx);
}


function g4p_individual_event_structure($bloc)
{
   
    $event_tag=$bloc[0]['tag'];
    $event_text=$bloc[0]['text'];
    $max=count($bloc)-1;

    $event_id=g4p_event_details($bloc);
    
    return array('id'=>$event_id, 'tag'=>$event_tag);

}

function g4p_event_details($bloc)
{
    global $g4p_base, $g4p_requetes, $g4p_links;
    static $sql_count1=0;
    static $sql_count2=0;
    
        /*
    n TYPE <EVENT_OR_FACT_CLASSIFICATION> {0:1} p.49
    n DATE <DATE_VALUE> {0:1} p.47, 46
    n <<PLACE_STRUCTURE>> {0:1} p.38
    n <<ADDRESS_STRUCTURE>> {0:1} p.31
    n AGNC <RESPONSIBLE_AGENCY> {0:1} p.60
    n RELI <RELIGIOUS_AFFILIATION> {0:1} p.60
    n CAUS <CAUSE_OF_EVENT> {0:1} p.43
    n RESN <RESTRICTION_NOTICE> {0:1} p.60
    n <<NOTE_STRUCTURE>> {0:M} p.37
    n <<SOURCE_CITATION>> {0:M} p.39
    n <<MULTIMEDIA_LINK>> {0:M} p.37, 26
    
    +1 FAMC @<XREF:FAM>@ {0:1} p.24
    +2 ADOP <ADOPTED_BY_WHICH_PARENT> {0:1} p.42
    
    AGE
    */
    
    $event_type='';
    $event_caus='';
    $event_date='';
    $event_age='';
    $event_place=$event_addr="NULL";
    $addr_bloc=array();
    $famc=$adop="NULL";
    $event_chan="NULL";
    
   //print_r($bloc); 
    
    $max=count($bloc)-1;
    $events_details_id=g4p_new_id('genea_events_details');
    
    $event_tag=$bloc[0]['tag'];
    
    for($i=1;$i<=$max;$i++)
    {
        switch(strtoupper($bloc[$i]['tag']))
        {
            case 'TYPE':
            $event_type=$bloc[$i]['text'];
            break;
    
            case 'DATE':
            $event_date=$bloc[$i]['text'];
            break;
            
            case 'PLAC':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $event_place=g4p_place_structure($sousbloc);
            break;
            
            case 'ADDR':
            //cas spécial, il faut plusierus blocs....
            $addr_bloc=g4p_load_bloc($bloc,$i);
            break;
            
            case 'PHON':
            case 'FAX':
            case 'EMAIL':
            case 'WWW':
            $addr_bloc[]=$bloc[$i];
            break;

            case 'AGNC':
            $event_agnc=$bloc[$i]['text'];
            break;

            case 'RELI':
            $event_reli=$bloc[$i]['text'];
            break;

            case 'CAUS':
            $event_caus=$bloc[$i]['text'];
            break;
            
            case 'AGE':
            $event_age=$bloc[$i]['text'];
            break;

            case 'RESN':
            $indi_resn=g4p_restriction_notice($bloc[$i]['text']);
            break;

            case 'NOTE':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_events_notes'][$events_details_id][]=g4p_note_structure($sousbloc);
            break;
            
            case 'SOUR':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_events_sources'][$events_details_id][]=g4p_sour_citation_structure($sousbloc);
            break;
            
            case 'OBJE':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_events_multimedia'][$events_details_id][]=g4p_multimedia_link($sousbloc);
            break;
            
            case 'FAMC':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_events_famc'][$events_details_id][]=g4p_event_famc_link($sousbloc);
            $famc=$g4p_links['rel_events_famc'][$events_details_id][0]['fam_id'];
            if(!empty($g4p_links['rel_events_famc'][$events_details_id][0]['adop']))
                $adop='"'.g4p_protect_var($g4p_links['rel_events_famc'][$events_details_id][0]['adop']).'"';
            break;
            
            case 'CHAN':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $event_chan=g4p_chan($sousbloc);
            break;            

            default:
            echo '4 <br />';
            print_r($bloc[$i]);
            break;
            
        }
    }
    
    if(!empty($addr_bloc))
        $event_addr=g4p_address_structure($addr_bloc);
          
    if($sql_count1%250==0)
    {
        $sql_count1++;
        $sql_count2++;
        
        $g4p_requetes['events'][$sql_count2]='INSERT INTO  genea_events_details ';
        $g4p_requetes['events'][$sql_count2].='(events_details_id, place_id, events_details_descriptor, events_details_gedcom_date,';
        $g4p_requetes['events'][$sql_count2].='events_details_cause, jd_count, jd_precision, jd_calendar, base, addr_id, events_details_age,';
        $g4p_requetes['events'][$sql_count2].='events_details_famc,events_details_adop, events_details_timestamp) VALUES ';
        $g4p_requetes['events'][$sql_count2].='('.$events_details_id.','.$event_place.',"'.$event_type.'","'
            .g4p_protect_var($event_date).'","","'.g4p_protect_var($event_caus)
            .'","'.'","'.'",'.$g4p_base.','.$event_addr.',"'.$event_age.'",'.$famc.','.$adop.','.$event_chan.')';
    }
    else
    {
        $sql_count1++;
        $g4p_requetes['events'][$sql_count2].=', ('.$events_details_id.','.$event_place.',"'.$event_type.'","'
            .g4p_protect_var($event_date).'","","'.g4p_protect_var($event_caus)
            .'","'.'","'.'",'.$g4p_base.','.$event_addr.',"'.$event_age.'",'.$famc.','.$adop.','.$event_chan.')';
    }  
     
    return $events_details_id;
}


function g4p_individual_attribute_structure($bloc)
{
   
    $event_tag=$bloc[0]['tag'];
    $event_text=$bloc[0]['text'];
    $max=count($bloc)-1;

    $event_id=g4p_event_details($bloc);
    
    return array('id'=>$event_id, 'tag'=>$event_tag, 'descr'=>$event_text);
}

function g4p_load_bloc($bloc,&$i)
{
    $sousbloc=array();
    $start=$bloc[$i]['niveau'];
    do 
    {
        $sousbloc[]=$bloc[$i];
        $i++;
    } while (isset($bloc[$i]) and $bloc[$i]['niveau']>$start);
    //print_r($sousbloc);
    $i--;
    return $sousbloc;
}

function g4p_place_structure($bloc)
{
    /*
    n PLAC <PLACE_NAME> {1:1} p.58
    +1 FORM <PLACE_HIERARCHY> {0:1} p.58
    39
    +1 FONE <PLACE_PHONETIC_VARIATION> {0:M} p.59
    +2 TYPE <PHONETIC_TYPE> {1:1} p.57
    +1 ROMN <PLACE_ROMANIZED_VARIATION> {0:M} p.59
    +2 TYPE <ROMANIZED_TYPE> {1:1} p.61
    +1 MAP {0:1}
    +2 LATI <PLACE_LATITUDE> {1:1} p.58
    +2 LONG <PLACE_LONGITUDE> {1:1} p.58
    +1 <<NOTE_STRUCTURE>> {0:M} p.37
     */
     
    global $g4p_db_lieux, $g4p_links;
    static $count_lieu=0;
    
    $max=count($bloc)-1;
    
    for($i=0;$i<=$max;$i++)
    {
        switch(strtoupper($bloc[$i]['tag']))
        {
            case 'PLAC':
            if(isset($g4p_db_lieux[md5($bloc[$i]['text'])]))
                $return_id=$g4p_db_lieux[md5($bloc[$i]['text'])]['id'];
            else
            {
                $count_lieu++;
                $return_id='@genea_place+'.$count_lieu;
                $g4p_db_lieux[md5($bloc[$i]['text'])]['id']=$return_id;
                $g4p_db_lieux[md5($bloc[$i]['text'])]['text']=$bloc[$i]['text'];
            }
            break;
            
            case 'NOTE':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_place_notes'][$return_id][]=g4p_note_structure($sousbloc);
            break;

            default:
            echo '5 <br />';
            print_r($bloc[$i]);
            break;
        }
    }

     
    return $return_id;
}

function g4p_note_structure($bloc)
{
    global $log, $g4p_base, $g4p_requetes, $g4p_links, $g4p_store_ids;
    static $sql_count1=0;
    static $sql_count2=0;
    
    $chan='NULL';
    
    /*
    [
    n NOTE @<XREF:NOTE>@ {1:1} p.27
    |
    n NOTE [<SUBMITTER_TEXT> | <NULL>] {1:1} p.63
    +1 [CONC|CONT] <SUBMITTER_TEXT> {0:M}
    ]
    */
    
   //print_r($bloc);

    if(!empty($bloc[0]['id']))
    {
        $note_id=g4p_new_id('genea_notes',substr($bloc[0]['id'],1,-1));
    }
    else
    {
        $max=count($bloc)-1;
        $note_id=g4p_new_id('genea_notes');       
        $g4p_store_ids['genea_notes'][$note_id]=1;
        for($i=0;$i<=$max;$i++)
        {
            switch(strtoupper($bloc[$i]['tag']))
            {
                case 'NOTE':
                $note_text=$bloc[$i]['text'];
                break;
                
                case 'CONT':
                $note_text.=" ".$bloc[$i]['text'];
                break;

                case 'CONC':
                $note_text.=$bloc[$i]['text'];
                break;

                case 'CHAN':
                $sousbloc=g4p_load_bloc($bloc,$i);
                $chan=g4p_chan($sousbloc);
                break;
                
                default:
                echo 'g4p_note_structure <br />';
                print_r($bloc[$i]);
                break;
            }
        }            
            
        if($sql_count1%250==0)
        {
            $sql_count1++;
            $sql_count2++;
            
            $g4p_requetes['note']['n'.$sql_count2]='INSERT INTO genea_notes ';
            $g4p_requetes['note']['n'.$sql_count2].='(`notes_id`, `base`, `notes_text`, `notes_timestamp`) VALUES ';
            $g4p_requetes['note']['n'.$sql_count2].= '('.$note_id.','.$g4p_base.',"'.g4p_protect_var($note_text).'",'.$chan.')';
        }
        else
        {
            $sql_count1++;
            $g4p_requetes['note']['n'.$sql_count2].= ', ('.$note_id.','.$g4p_base.',"'.g4p_protect_var($note_text).'",'.$chan.')';
        }
    }
    //echo $note_id.'-'.$bloc[0]['id'].'<br>';
    return $note_id;
}

function g4p_sour_citation_structure($bloc)
{
    global $log, $g4p_base, $g4p_requetes, $g4p_links;
    static $sql_count1=0;
    static $sql_count2=0;
    
    /*
    SOURCE_CITATION:=
    [ /* pointer to source record (preferred)
    n SOUR @<XREF:SOUR>@ {1:1} p.27
    +1 PAGE <WHERE_WITHIN_SOURCE> {0:1} p.64
    +1 EVEN <EVENT_TYPE_CITED_FROM> {0:1} p.49
    +2 ROLE <ROLE_IN_EVENT> {0:1} p.61
    +1 DATA {0:1}
    +2 DATE <ENTRY_RECORDING_DATE> {0:1} p.48
    +2 TEXT <TEXT_FROM_SOURCE> {0:M} p.63
    +3 [CONC|CONT] <TEXT_FROM_SOURCE> {0:M}
    +1 <<MULTIMEDIA_LINK>> {0:M} p.37, 26
    +1 <<NOTE_STRUCTURE>> {0:M} p.37
    +1 QUAY <CERTAINTY_ASSESSMENT> {0:1} p.43
    | /* Systems not using source records 
    n SOUR <SOURCE_DESCRIPTION> {1:1} p.61
    +1 [CONC|CONT] <SOURCE_DESCRIPTION> {0:M}
    +1 TEXT <TEXT_FROM_SOURCE> {0:M} p.63
    +2 [CONC|CONT] <TEXT_FROM_SOURCE> {0:M}
    +1 <<MULTIMEDIA_LINK>> {0:M} p.37, 26
    +1 <<NOTE_STRUCTURE>> {0:M} p.37
    +1 QUAY <CERTAINTY_ASSESSMENT> {0:1} p.43
    ]
    The data provided in the <<SOURCE_CITATION>> structure is source-related information specific
    to the data being cited. (See GEDCOM examples starting on page 74.) Systems that do not use a
    (SOURCE_RECORD) must use the non-preferred second SOURce citation structure option. When
    systems that support the zero level source record format encounters a source citation that does not
    contain pointers to source records, then that system needs to create a SOURCE_RECORD format
    and store the source description information found in the non-structured source citation in the title
    area for the new source record.
    The information intended to be placed in the citation structure includes:
    ! The pointer to the SOURCE_RECORD, which contains a more general description of the source
    40
    used for the fact being cited.
    ! Information, such as a page number, to help the user find the cited data within the referenced
    source. This is stored in the “.SOUR.PAGE” tag context.
    ! Actual text from the source that was used in making assertions, for example a date phrase as
    actually recorded in the source, or significant notes written by the recorder, or an applicable
    sentence from a letter. This is stored in the “.SOUR.DATA.TEXT” tag context.
    ! Data that allows an assessment of the relative value of one source over another for making the
    recorded assertions (primary or secondary source, etc.). Data needed for this assessment is data
    that would help determine how much time from the date of the asserted fact and when the source
    was actually recorded, what type of event was cited, and what type of role did this person have in
    the cited source.
    - Date when the entry was recorded in source document is stored in the
    ".SOUR.DATA.DATE" tag context.
    - The type of event that initiated the recording is stored in the “SOUR.EVEN” tag context. The
    value used is the event code taken from the table of choices shown in the
    EVENT_TYPE_CITED_FROM primitive on page 49
    - The role of this person in the event is stored in the ".SOUR.EVEN.ROLE" context.
    */
    
    $max=count($bloc)-1;
    $quay='NULL';
    $even['even']=$even['role']='';
    $data['text']=$data['date']='';
    $page='';
    
    if(empty($bloc[0]['id']))
        return false;
    
    $sour_record_id=g4p_new_id('genea_sour_records',substr($bloc[0]['id'],1,-1));
    $sour_citation_id=g4p_new_id('genea_sour_citations');
    
    for($i=1;$i<=$max;$i++)
    {
        switch(strtoupper($bloc[$i]['tag']))
        {
            case 'PAGE':
            $page=$bloc[$i]['text'];
            break;
            
            case 'EVEN':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $even=g4p_sour_citation_even($sousbloc);
            break;

            case 'DATA':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $data=g4p_sour_citation_data($sousbloc);
            break;

            case 'QUAY':
            if(!empty($bloc[$i]['text']))
                $quay=$bloc[$i]['text'];
            break;
            
            case 'NOTE':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_sour_citations_notes'][$sour_citation_id][]=g4p_note_structure($sousbloc);
            break;

            case 'OBJE':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_sour_citations_multimedia'][$sour_citation_id][]=g4p_multimedia_link($sousbloc);
            break;

            default:
            echo 'sour_citation_structure <br />';
            print_r($bloc[$i]);
            break;
        }
    }    
    
    if($sql_count1%250==0)
    {
        $sql_count1++;
        $sql_count2++;
        
        $g4p_requetes['sour_cit'][$sql_count2]='INSERT INTO  genea_sour_citations ';
        $g4p_requetes['sour_cit'][$sql_count2].='(sour_citations_id, sour_records_id, sour_citations_page, sour_citations_even, ';
        $g4p_requetes['sour_cit'][$sql_count2].='sour_citations_even_role, sour_citations_data_dates, sour_citations_data_text, ';
        $g4p_requetes['sour_cit'][$sql_count2].='sour_citations_quay, base) VALUES ';
        $g4p_requetes['sour_cit'][$sql_count2].='('.$sour_citation_id.','.$sour_record_id.',"'.g4p_protect_var($page)
            .'","'.g4p_protect_var($even['even']).'","'.g4p_protect_var($even['role']).'","';
        $g4p_requetes['sour_cit'][$sql_count2].=g4p_protect_var($data['date']).'","'.g4p_protect_var($data['text'])
            .'",'.$quay.','.$g4p_base.')';
    }
    else
    {
        $sql_count1++;
        $g4p_requetes['sour_cit'][$sql_count2].=', ('.$sour_citation_id.','.$sour_record_id.',"'.g4p_protect_var($page)
            .'","'.g4p_protect_var($even['even']).'","'.g4p_protect_var($even['role']).'","';
        $g4p_requetes['sour_cit'][$sql_count2].=g4p_protect_var($data['date']).'","'.g4p_protect_var($data['text'])
            .'",'.$quay.','.$g4p_base.')';
    }     
 
    return $sour_citation_id;
}

function g4p_sour_citation_even($bloc)
{
     global $log, $g4p_base, $g4p_requetes, $g4p_links;
    $even=$role='';
    
    $max=count($bloc)-1;
    
    for($i=0;$i<=$max;$i++)
    {
        switch(strtoupper($bloc[$i]['tag']))
        {
            case 'EVEN':
            $even=$bloc[$i]['text'];
            break;
            
            case 'ROLE':
            $role=$bloc[$i]['text'];
            break;

            default:
            echo 'sour_citation_even <br />';
            print_r($bloc[$i]);
            break;
        }
    }
    return array('even'=>$even, 'role'=>$role);
}

function g4p_sour_citation_data($bloc)
{
     global $log, $g4p_base, $g4p_requetes, $g4p_links;
    $date=$texte='';
    
    $max=count($bloc)-1;
    
    for($i=1;$i<=$max;$i++)
    {
        switch(strtoupper($bloc[$i]['tag']))
        {
            case 'DATE':
            $date=$bloc[$i]['text'];
            break;
            
            case 'TEXT':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $text=g4p_bloc_cont($sousbloc);
            break;

            default:
            echo 'sour_citation_data <br />';
            print_r($bloc[$i]);
            break;
        }
    }
    return array('date'=>$date, 'text'=>$text);
}

function g4p_multimedia_link($bloc)
{
    global $log, $g4p_requetes, $g4p_base, $g4p_store_ids;
    static $sql_count1=0;
    static $sql_count2=0;
    
    /*
        n OBJE @<XREF:OBJE>@ {1:1} p.26
    |
    n OBJE
    +1 FILE <MULTIMEDIA_FILE_REFN> {1:M} p.54
    +2 FORM <MULTIMEDIA_FORMAT> {1:1} p.54
    +3 MEDI <SOURCE_MEDIA_TYPE> {0:1} p.62
    +1 TITL <DESCRIPTIVE_TITLE> {0:1} p.48
    Note: some systems may have output the following 5.5 structure. The new context above was
    introduced in order to allow a grouping of related multimedia files to a particular context.
    n OBJE
    +1 FILE
    +1 FORM <MULTIMEDIA_FORMAT>
    +2 MEDI <SOURCE_MEDIA_TYPE>
    */
    
    $titl=$form=$file='';
    $chan='NULL';
    
    if(!empty($bloc[0]['id']))
    {
        $media_id=g4p_new_id('genea_multimedia',substr($bloc[0]['id'],1,-1));
    }
    else
    {
        $max=count($bloc)-1;
        $media_id=g4p_new_id('genea_multimedia');  
        $g4p_store_ids['genea_multimedia'][$media_id]=1;
        for($i=1;$i<=$max;$i++)
        {
            //echo $bloc[$i]['tag']."\n";
            switch(strtoupper(trim($bloc[$i]['tag'])))
            {            
                case 'FILE':
                $sousbloc=g4p_load_bloc($bloc,$i);
                $file=g4p_multimedia_file_refn($sousbloc);
                break;
                
                case 'RIN':
                $rin=$bloc[$i]['text'];
                break;
                
                case 'CHAN':
                $sousbloc=g4p_load_bloc($bloc,$i);
                $chan=g4p_chan($sousbloc);
                break;

                default:
                echo 'tag qui a passé à travers (load media) ',$bloc[$i]['tag'],' ',$bloc[$i][0],' ',$bloc[$i-1][0],"\n";
                break;            
            }
        }

        if(!empty($file))
            extract($file);
        
        if($sql_count1%250==0)
        {
            $sql_count1++;
            $sql_count2++;
            
            $g4p_requetes['obje']['m'.$sql_count2]='INSERT INTO genea_multimedia ';
            $g4p_requetes['obje']['m'.$sql_count2].='(`media_id`, `base`, `media_title`, `media_format`, `media_file`, `media_timestamp`) VALUES ';
            $g4p_requetes['obje']['m'.$sql_count2].= '('.$media_id.','.$g4p_base.',"'.g4p_protect_var($titl)
                .'","'.g4p_protect_var($form).'","'.g4p_protect_var($file).'",'.$chan.')';
        }
        else
        {
            $sql_count1++;
            $g4p_requetes['obje']['m'.$sql_count2].= ', ('.$media_id.','.$g4p_base.',"'.g4p_protect_var($titl)
                .'","'.g4p_protect_var($form).'","'.g4p_protect_var($file).'",'.$chan.')';
        }     
    }
    
    return $media_id;
}

function g4p_child_to_family_link($bloc)
{
    /*  
     n FAMC @<XREF:FAM>@ {1:1} p.24
        +1 PEDI <PEDIGREE_LINKAGE_TYPE> {0:1} p.57
        +1 STAT <CHILD_LINKAGE_STATUS> {0:1} p.44
        +1 <<NOTE_STRUCTURE>> {0:M} p.37
     */
     global $log, $g4p_base, $g4p_requetes, $g4p_links;
    $fam_id=$fam_pedi=NULL;
    
    $max=count($bloc)-1;
    
    for($i=0;$i<=$max;$i++)
    {
        switch(strtoupper($bloc[$i]['tag']))
        {
            case 'FAMC':
            $fam_id=g4p_new_id('genea_familles',substr($bloc[$i]['id'],1,-1));
            break;
            
            case 'PEDI':
            $fam_pedi=$bloc[$i]['text'];
            break;
            
            default:
            echo '6 <br />';
            print_r($bloc[$i]);
            break;
        }
    }
     
    return array('id'=>$fam_id, 'pedi'=>$fam_pedi);
}

function g4p_spouse_to_family_link($bloc)
{
    /*
    SPOUSE_TO_FAMILY_LINK:=
        n FAMS @<XREF:FAM>@ {1:1} p.24
        +1 <<NOTE_STRUCTURE>> {0:M} p.37
    */
    return 'fams';
}

function g4p_event_famc_link($bloc)
{
    /*  
        n [ BIRT | CHR ] [Y|<NULL>] {1:1}
        +1 <<INDIVIDUAL_EVENT_DETAIL>> {0:1}* p.34
        +1 FAMC @<XREF:FAM>@ {0:1} p.24
        
        n ADOP {1:1}
        +1 <<INDIVIDUAL_EVENT_DETAIL>> {0:1}* p.34
        +1 FAMC @<XREF:FAM>@ {0:1} p.24
        +2 ADOP <ADOPTED_BY_WHICH_PARENT> {0:1} p.42
     */
     global $log, $g4p_base, $g4p_requetes, $g4p_links;
    $fam_id=$fam_adop=NULL;
    
    $max=count($bloc)-1;
    
    for($i=0;$i<=$max;$i++)
    {
        switch(strtoupper($bloc[$i]['tag']))
        {
            case 'FAMC':
            $fam_id=g4p_new_id('genea_familles',substr($bloc[$i]['id'],1,-1));
            break;
            
            case 'ADOP':
            $fam_adop=$bloc[$i]['text'];
            break;

            default:
            echo 'g4p_event_famc_link <br />';
            print_r($bloc[$i]);
            break;
        }
    }
     
    return array('fam_id'=>$fam_id, 'adop'=>$fam_adop);
}

function g4p_association_structure($bloc)
{
    /*
    n ASSO @<XREF:INDI>@ {1:1} p.25
        +1 RELA <RELATION_IS_DESCRIPTOR> {1:1} p.60
        +1 <<SOURCE_CITATION>> {0:M} p.39
        +1 <<NOTE_STRUCTURE>> {0:M} p.37
    */
    return 'asso';
}

function g4p_chan($bloc)
{
    /*
     n CHAN {1:1}
        +1 DATE <CHANGE_DATE> {1:1} p.44
        +2 TIME <TIME_VALUE> {0:1} p.63
        +1 <<NOTE_STRUCTURE>> {0:M} p.37
        */
    $date=$time='';
    
    $max=count($bloc)-1;
    for($i=1;$i<=$max;$i++)
    {
        //echo $bloc[$i]['tag']."\n";
        switch(strtoupper(trim($bloc[$i]['tag'])))
        {            
            case 'DATE':
            $date=$bloc[$i]['text'];
            break;

            case 'TIME':
            $time=$bloc[$i]['text'];
            break;

            default:
            echo 'tag qui a passé à travers (load chan) ',$bloc[$i]['tag'],' ',$bloc[$i][0],' ',$bloc[$i-1][0],"\n";
            break;
            //ignore
            //+1 ANCI @<XREF:SUBM>@ {0:M} p.28
            //+1 DESI @<XREF:SUBM>@ {0:M} p.28
            //+1 AFN <ANCESTRAL_FILE_NUMBER> {0:1} p.42
            
        }
    }
    //echo '"'.date('Y-m-d G:i:s', strtotime(trim($date.' '.$time))).'"<br />';
    return '"'.date('Y-m-d G:i:s', strtotime(trim($date.' '.$time))).'"';
}

function g4p_family_event_structure($bloc)
{
    $event_tag=$bloc[0]['tag'];
    $event_text=$bloc[0]['text'];
    $max=count($bloc)-1;

    $event_id=g4p_event_details($bloc);
    
    return array('id'=>$event_id, 'tag'=>$event_tag);    
}

function g4p_restriction_notice($resn)
{
    $resn=trim($resn);
    if($resn!='privacy' and $resn!='locked' and $resn!='confidential')
        $resn='NULL';
    return $resn;
}

function g4p_load_sour($bloc)
{
    global $log, $g4p_base, $g4p_requetes, $g4p_links, $g4p_store_ids;
    static $sql_count1=0;
    static $sql_count2=0;
    
    /*
    n @<XREF:SOUR>@ SOUR {1:1}
        +1 DATA {0:1}
        +2 EVEN <EVENTS_RECORDED> {0:M} p.50
        +3 DATE <DATE_PERIOD> {0:1} p.46
        +3 PLAC <SOURCE_JURISDICTION_PLACE> {0:1} p.62
        +2 AGNC <RESPONSIBLE_AGENCY> {0:1} p.60
        +2 <<NOTE_STRUCTURE>> {0:M} p.37
        +1 AUTH <SOURCE_ORIGINATOR> {0:1} p.62
        +2 [CONC|CONT] <SOURCE_ORIGINATOR> {0:M} p.62
        +1 TITL <SOURCE_DESCRIPTIVE_TITLE> {0:1} p.62
        +2 [CONC|CONT] <SOURCE_DESCRIPTIVE_TITLE> {0:M} p.62
        +1 ABBR <SOURCE_FILED_BY_ENTRY> {0:1} p.62
        +1 PUBL <SOURCE_PUBLICATION_FACTS> {0:1} p.62
        +2 [CONC|CONT] <SOURCE_PUBLICATION_FACTS> {0:M} p.62
        +1 TEXT <TEXT_FROM_SOURCE> {0:1} p.63
        +2 [CONC|CONT] <TEXT_FROM_SOURCE> {0:M} p.63
        +1 <<SOURCE_REPOSITORY_CITATION>> {0:M} p.40
        +1 REFN <USER_REFERENCE_NUMBER> {0:M} p.63, 64
        +2 TYPE <USER_REFERENCE_TYPE> {0:1} p.64
        +1 RIN <AUTOMATED_RECORD_ID> {0:1} p.43
        28
        +1 <<CHANGE_DATE>> {0:1} p.31
        +1 <<NOTE_STRUCTURE>> {0:M} p.37
        +1 <<MULTIMEDIA_LINK>> {0:M} p.37, 26
    */
    
    //print_r($bloc);
    //print_r($g4p_translates_id);
    $sour_id=g4p_new_id('genea_sour_records',$bloc[0]['id']);
    $g4p_store_ids['genea_sour_records'][$sour_id]=1;
    $max=count($bloc)-1;
    $repo_id=$chan='NULL';
    $auth=$titl=$abbr=$publ=$rin=$repo_caln=$repo_medi='';
    
    //print_r($bloc);
    
    for($i=1;$i<=$max;$i++)
    {
        //echo $bloc[$i]['tag']."\n";
        switch(strtoupper(trim($bloc[$i]['tag'])))
        {            
            case 'FAMC':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_familles_indi'][$indi_id][]=g4p_child_to_family_link($sousbloc);
            break;
            
            case 'DATA':
            $sousbloc=g4p_load_bloc($bloc,$i);
            //necessite une nouvelle table, ignorée pour le moment
            break;
            
            case 'AUTH':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $auth=g4p_bloc_cont($sousbloc);
            break;
            
            case 'TITL':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $titl=g4p_bloc_cont($sousbloc);
            break;

            case 'ABBR':
            $abbr=$bloc[$i]['text'];
            break;

            case 'RIN':
            $rin=$bloc[$i]['text'];
            break;

            case 'PUBL':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $publ=g4p_bloc_cont($sousbloc);
            break;

             case 'TEXT':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $text=g4p_bloc_cont($sousbloc);
            break;

            case 'REPO':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $repo=g4p_source_repository_citation($sousbloc);
            break;
            
            case 'NOTE':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_sour_records_notes'][$sour_id][]=g4p_note_structure($sousbloc);
            break;

            case 'CHAN':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $chan=g4p_chan($sousbloc);
            break;

            default:
            echo 'tag qui a passé à travers (load sour) ',$bloc[$i]['tag'],' ',$bloc[$i][0],' ',$bloc[$i-1][0],"\n";
            break;
            //ignore
            //+1 ANCI @<XREF:SUBM>@ {0:M} p.28
            //+1 DESI @<XREF:SUBM>@ {0:M} p.28
            //+1 AFN <ANCESTRAL_FILE_NUMBER> {0:1} p.42
            
        }
    }
    
    if(!empty($repo))
        extract($repo);
    
    if($sql_count1%250==0)
    {
        $sql_count1++;
        $sql_count2++;
        
        $g4p_requetes['sour_records'][$sql_count2]='INSERT INTO genea_sour_records ';
        $g4p_requetes['sour_records'][$sql_count2].='(sour_records_id, sour_records_auth, sour_records_title, sour_records_abbr,';
        $g4p_requetes['sour_records'][$sql_count2].='sour_records_publ, sour_records_rin, repo_id, repo_caln, repo_medi, base,';
        $g4p_requetes['sour_records'][$sql_count2].='sour_records_timestamp) VALUES ';
        $g4p_requetes['sour_records'][$sql_count2].= '('.$sour_id.',"'.g4p_protect_var($auth).'","'.g4p_protect_var($titl)
            .'","'.g4p_protect_var($abbr).'","'.g4p_protect_var($publ)
            .'","'.g4p_protect_var($rin).'",'.$repo_id.',"'.g4p_protect_var($repo_caln)
           .'","'.g4p_protect_var($repo_medi).'",'.$g4p_base.','.$chan.')';
    }
    else
    {
        $sql_count1++;
        $g4p_requetes['sour_records'][$sql_count2].= ', ('.$sour_id.',"'.g4p_protect_var($auth).'","'.g4p_protect_var($titl)
            .'","'.g4p_protect_var($abbr).'","'.g4p_protect_var($publ)
            .'","'.g4p_protect_var($rin).'",'.$repo_id.',"'.g4p_protect_var($repo_caln)
           .'","'.g4p_protect_var($repo_medi).'",'.$g4p_base.','.$chan.')';
    }         
            
    return 1;
}

function g4p_load_repo($bloc)
{
    global $log, $g4p_base, $g4p_requetes, $g4p_links, $g4p_store_ids;
    static $sql_count1=0;
    static $sql_count2=0;
    
    /*
    n @<XREF:REPO>@ REPO {1:1}
        +1 NAME <NAME_OF_REPOSITORY> {1:1} p.54
        +1 <<ADDRESS_STRUCTURE>> {0:1} p.31
        +1 <<NOTE_STRUCTURE>> {0:M} p.37
        +1 REFN <USER_REFERENCE_NUMBER> {0:M} p.63, 64
        +2 TYPE <USER_REFERENCE_TYPE> {0:1} p.64
        +1 RIN <AUTOMATED_RECORD_ID> {0:1} p.43
        +1 <<CHANGE_DATE>> {0:1} p.31
    */
    
    //print_r($bloc);
    //print_r($g4p_translates_id);
    $addr_bloc=array();
    $rin='';
    $chan='NULL';
    $repo_id=g4p_new_id('genea_repository',$bloc[0]['id']);
    $g4p_store_ids['genea_repository'][$repo_id]=1;
    $max=count($bloc)-1;
    
    //print_r($bloc);
    
    for($i=1;$i<=$max;$i++)
    {
        //echo $bloc[$i]['tag']."\n";
        switch(strtoupper(trim($bloc[$i]['tag'])))
        {            
            case 'NAME':
            $name=$bloc[$i]['text'];
            break;
            
            case 'RIN':
            $rin=$bloc[$i]['text'];
            break;

            case 'ADDR':
            //cas spécial, il faut plusierus blocs....
            $addr_bloc=g4p_load_bloc($bloc,$i);
            break;
            
            case 'PHON':
            case 'FAX':
            case 'EMAIL':
            case 'WWW':
            $addr_bloc[]=$bloc[$i];
            break;
            
            case 'NOTE':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $g4p_links['rel_repo_notes'][$repo_id][]=g4p_note_structure($sousbloc);
            break;

            case 'CHAN':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $chan=g4p_chan($sousbloc);
            break;
            
            default:
            echo 'tag qui a passé à travers (load repo) ',$bloc[$i]['tag'],' ',$bloc[$i][0],' ',$bloc[$i-1][0],"\n";
            break;
            //ignore
            //+1 ANCI @<XREF:SUBM>@ {0:M} p.28
            //+1 DESI @<XREF:SUBM>@ {0:M} p.28
            //+1 AFN <ANCESTRAL_FILE_NUMBER> {0:1} p.42
            
        }
    }

    if(!empty($addr_bloc))
        $addr_id=g4p_address_structure($addr_bloc);

    if($sql_count1%250==0)
    {
        $sql_count1++;
        $sql_count2++;
        
        $g4p_requetes['repo'][$sql_count2]='INSERT INTO genea_repository ';
        $g4p_requetes['repo'][$sql_count2].='(repo_id, repo_name, repo_rin, base, addr_id, repo_timestamp) VALUES ';
        $g4p_requetes['repo'][$sql_count2].= '('.$repo_id.',"'.g4p_protect_var($name).'","'.g4p_protect_var($rin)
            .'",'.$g4p_base.','.$addr_id.','.$chan.')';
    }
    else
    {
        $sql_count1++;
        $g4p_requetes['repo'][$sql_count2].= ', ('.$repo_id.',"'.g4p_protect_var($name).'","'.g4p_protect_var($rin)
            .'",'.$g4p_base.','.$addr_id.','.$chan.')';
    }        

    return 1;
}

function g4p_load_obje($bloc)
{
    global $log, $g4p_base, $g4p_requetes, $g4p_links, $g4p_store_ids;
    static $sql_count1=0;
    static $sql_count2=0;
    
    /*
    n @XREF:OBJE@ OBJE {1:1}
        +1 FILE <MULTIMEDIA_FILE_REFN> {1:M} p.54
        +2 FORM <MULTIMEDIA_FORMAT> {1:1} p.54
        +3 TYPE <SOURCE_MEDIA_TYPE> {0:1} p.62
        +2 TITL <DESCRIPTIVE_TITLE> {0:1} p.48
        +1 REFN <USER_REFERENCE_NUMBER> {0:M} p.63, 64
        +2 TYPE <USER_REFERENCE_TYPE> {0:1} p.64
        +1 RIN <AUTOMATED_RECORD_ID> {0:1} p.43
        +1 <<NOTE_STRUCTURE>> {0:M} p.37
        +1 <<SOURCE_CITATION>> {0:M} p.39
        +1 <<CHANGE_DATE>> {0:1} p.31
    */
    
    //print_r($bloc);
    //print_r($g4p_translates_id);
    $chan='NULL';
    $media_id=g4p_new_id('genea_multimedia',$bloc[0]['id']);
    $g4p_store_ids['genea_multimedia'][$media_id]=1;
    $max=count($bloc)-1;
    
    //print_r($bloc);
    
    for($i=1;$i<=$max;$i++)
    {
        //echo $bloc[$i]['tag']."\n";
        switch(strtoupper(trim($bloc[$i]['tag'])))
        {            
            case 'FILE':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $file=g4p_multimedia_file_refn($sousbloc);
            break;
            
            case 'RIN':
            $rin=$bloc[$i]['text'];
            break;
            
            case 'CHAN':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $chan=g4p_chan($sousbloc);
            break;
            
            default:
            echo 'tag qui a passé à travers (load obje) ',$bloc[$i]['tag'],' ',$bloc[$i][0],' ',$bloc[$i-1][0],"\n";
            break;            
        }
    }

    extract($file);
    
    if($sql_count1%250==0)
    {
        $sql_count1++;
        $sql_count2++;
        
        $g4p_requetes['obje'][$sql_count2]='INSERT INTO genea_multimedia ';
        $g4p_requetes['obje'][$sql_count2].='(`media_id`, `base`, `media_title`, `media_format`, `media_file`, `media_timestamp`) VALUES ';
        $g4p_requetes['obje'][$sql_count2].= '('.$media_id.','.$g4p_base.',"'.g4p_protect_var($titl)
            .'","'.g4p_protect_var($form).'","'.g4p_protect_var($file).'",'.$chan.')';
    }
    else
    {
        $sql_count1++;
        $g4p_requetes['obje'][$sql_count2].= ', ('.$media_id.','.$g4p_base.',"'.g4p_protect_var($titl)
            .'","'.g4p_protect_var($form).'","'.g4p_protect_var($file).'",'.$chan.')';
    }     
    
    return 1;
}

function g4p_load_note($bloc)
{
    global $log, $g4p_base, $g4p_requetes, $g4p_links, $g4p_store_ids;
    static $sql_count1=0;
    static $sql_count2=0;
    
    /*
    n @<XREF:NOTE>@ NOTE <SUBMITTER_TEXT> {1:1} p.63
        +1 [CONC|CONT] <SUBMITTER_TEXT> {0:M}
        +1 REFN <USER_REFERENCE_NUMBER> {0:M} p.63, 64
        +2 TYPE <USER_REFERENCE_TYPE> {0:1} p.64
        +1 RIN <AUTOMATED_RECORD_ID> {0:1} p.43
        +1 <<SOURCE_CITATION>> {0:M} p.39
        +1 <<CHANGE_DATE>> {0:1} p.31
    */
    
    //print_r($bloc);
    //print_r($g4p_translates_id);
    $note_id=g4p_new_id('genea_notes',$bloc[0]['id']);
    $g4p_store_ids['genea_notes'][$note_id]=1;
    $note_text=$bloc[0]['text'];
    $max=count($bloc)-1;
    $chan='NULL';
    
    //print_r($bloc);
    
    for($i=1;$i<=$max;$i++)
    {
        //echo $bloc[$i]['tag']."\n";
        switch(strtoupper(trim($bloc[$i]['tag'])))
        {            
            case 'CONT':
            $note_text.=" ".$bloc[$i]['text'];
            break;
            
            case 'CONC':
            $note_text.=$bloc[$i]['text'];
            break;

            case 'RIN':
            $rin=$bloc[$i]['text'];
            break;

            case 'CHAN':
            $sousbloc=g4p_load_bloc($bloc,$i);
            $chan=g4p_chan($sousbloc);
            break;
            
            default:
            echo 'tag qui a passé à travers (load note) ',$bloc[$i]['tag'],' ',$bloc[$i][0],' ',$bloc[$i-1][0],"\n";
            break;            
        }
    }

    if($sql_count1%250==0)
    {
        $sql_count1++;
        $sql_count2++;
        
        $g4p_requetes['note'][$sql_count2]='INSERT INTO genea_notes ';
        $g4p_requetes['note'][$sql_count2].='(`notes_id`, `base`, `notes_text`, `notes_timestamp`) VALUES ';
        $g4p_requetes['note'][$sql_count2].= '('.$note_id.','.$g4p_base.',"'.g4p_protect_var($note_text).'",'.$chan.')';
    }
    else
    {
        $sql_count1++;
        $g4p_requetes['note'][$sql_count2].= ', ('.$note_id.','.$g4p_base.',"'.g4p_protect_var($note_text).'",'.$chan.')';
    }
        
    return 1;
}

function g4p_bloc_cont($bloc)
{
    $max=count($bloc)-1;
    
    //print_r($bloc);
    
    $text=$bloc[0]['text'];
    
    for($i=1;$i<=$max;$i++)
    {
        //echo $bloc[$i]['tag']."\n";
        //echo $bloc[$i]['niveau'].$bloc[0]['niveau']."\n";
        if($bloc[$i]['niveau']>$bloc[0]['niveau'])
        {
            switch(strtoupper(trim($bloc[$i]['tag'])))
            {            
                case 'CONT':
                $text.=' '.trim($bloc[$i]['text']);
                break;
                
                case 'CONC':
                $text.=trim($bloc[$i]['text']);
                break;
                
                default:
                echo 'tag qui a passé à travers (bloc_cont) ',$bloc[$i]['tag'],' ',$bloc[$i][0],' ',$bloc[$i-1][0],"\n";
                break;
                //ignore
                //+1 ANCI @<XREF:SUBM>@ {0:M} p.28
                //+1 DESI @<XREF:SUBM>@ {0:M} p.28
                //+1 AFN <ANCESTRAL_FILE_NUMBER> {0:1} p.42
                
            }
        }
    }
    return $text;
}

function g4p_source_repository_citation($bloc)
{

    $max=count($bloc)-1;
    $caln='';
    //print_r($bloc);
    
    if(empty($bloc[0]['id']))
        return false;
    
    $repo_id=g4p_new_id('genea_repository',substr($bloc[0]['id'],1,-1));
    
    for($i=1;$i<=$max;$i++)
    {
        //echo $bloc[$i]['tag']."\n";
        switch(strtoupper(trim($bloc[$i]['tag'])))
        {            
            case 'CALN':
            $caln=' '.$bloc[$i]['text'];
            break;
            
            case 'MEDI':
            $medi=$bloc[$i]['text'];
            break;
            
            default:
            echo 'tag qui a passé à travers (bloc_cont) ',$bloc[$i]['tag'],' ',$bloc[$i][0],' ',$bloc[$i-1][0],"\n";
            break;
            //ignore
            //+1 ANCI @<XREF:SUBM>@ {0:M} p.28
            //+1 DESI @<XREF:SUBM>@ {0:M} p.28
            //+1 AFN <ANCESTRAL_FILE_NUMBER> {0:1} p.42
            
        }
    }
    return array('repo_id'=>$repo_id, 'repo_caln'=>$caln, 'repo_medi'=>$medi);
}

function g4p_address_structure($bloc)
{  
    /*
    ADDRESS_STRUCTURE:=
        n ADDR <ADDRESS_LINE> {1:1} p.41
        +1 CONT <ADDRESS_LINE> {0:3} p.41
        +1 ADR1 <ADDRESS_LINE1> {0:1} p.41
        +1 ADR2 <ADDRESS_LINE2> {0:1} p.41
        +1 ADR3 <ADDRESS_LINE3> {0:1} p.41
        +1 CITY <ADDRESS_CITY> {0:1} p.41
        +1 STAE <ADDRESS_STATE> {0:1} p.42
        +1 POST <ADDRESS_POSTAL_CODE> {0:1} p.41
        +1 CTRY <ADDRESS_COUNTRY> {0:1} p.41
        n PHON <PHONE_NUMBER> {0:3} p.57
        n EMAIL <ADDRESS_EMAIL> {0:3} p.41
        n FAX <ADDRESS_FAX> {0:3} p.41
        n WWW <ADDRESS_WEB_PAGE> {0:3} p.42
    */

    global $g4p_requetes, $g4p_base;
    static $sql_count1=0;
    static $sql_count2=0;

    $max=count($bloc)-1;
    $addr=$city=$stae=$post=$ctry=$phon=$email=$fax=$www='';
    //print_r($bloc);
        
    $addr_id=g4p_new_id('genea_address');
    
    for($i=0;$i<=$max;$i++)
    {
        //echo $bloc[$i]['tag']."\n";
        switch(strtoupper(trim($bloc[$i]['tag'])))
        {            
            case 'ADDR':
            case 'CONT':
            $addr.=$bloc[$i]['text']."\n";
            break;
            
            case 'ADDR1':
            case 'ADDR2':
            case 'ADDR3':
            $addr.=$bloc[$i]['text'];
            break;
            
            case 'CITY':
            $city=$bloc[$i]['text'];
            break;

            case 'STAE':
            $stae=$bloc[$i]['text'];
            break;

            case 'POST':
            $post=$bloc[$i]['text'];
            break;

            case 'CTRY':
            $ctry=$bloc[$i]['text'];
            break;

            case 'PHON':
            $phon[]=$bloc[$i]['text'];
            break;

            case 'FAX':
            $fax[]=$bloc[$i]['text'];
            break;

            case 'EMAIL':
            $email[]=$bloc[$i]['text'];
            break;

            case 'WWW':
            $www[]=$bloc[$i]['text'];
            break;

            default:
            echo 'tag qui a passé à travers (bloc_cont) ',$bloc[$i]['tag'],' ',$bloc[$i][0],' ',$bloc[$i-1][0],"\n";
            break;
            //ignore
            //+1 ANCI @<XREF:SUBM>@ {0:M} p.28
            //+1 DESI @<XREF:SUBM>@ {0:M} p.28
            //+1 AFN <ANCESTRAL_FILE_NUMBER> {0:1} p.42
            
        }
    }
   
    for($i=1;$i<=3;$i++)
    {
        if(!empty($phon[$i]))
            ${'phon'.$i}=$phon[$i];
        else
            ${'phon'.$i}='';
        if(!empty($email[$i]))
            ${'email'.$i}=$email[$i];
        else
            ${'email'.$i}='';
        if(!empty($fax[$i]))
            ${'fax'.$i}=$fax[$i];
        else
            ${'fax'.$i}='';
        if(!empty($www[$i]))
            ${'www'.$i}=$www[$i];
        else
            ${'www'.$i}='';
    }
    
    if($sql_count1%250==0)
    {
        $sql_count1++;
        $sql_count2++;
        
        $g4p_requetes['addr'][$sql_count2]='INSERT INTO genea_address ';
        $g4p_requetes['addr'][$sql_count2].='(addr_id, base, addr_addr, addr_city, addr_stae, addr_post, addr_ctry, ';
        $g4p_requetes['addr'][$sql_count2].='addr_phon1, addr_phon2, addr_phon3, ';
        $g4p_requetes['addr'][$sql_count2].='addr_email1, addr_email2, addr_email3, ';
        $g4p_requetes['addr'][$sql_count2].='addr_fax1, addr_fax2, addr_fax3, addr_www1, addr_www2, addr_www3 ) VALUES ';
        $g4p_requetes['addr'][$sql_count2].= '('.$addr_id.','.$g4p_base.',"'.g4p_protect_var($addr)
            .'","'.g4p_protect_var($city).'","'.g4p_protect_var($stae)
            .'","'.g4p_protect_var($post).'","';
        $g4p_requetes['addr'][$sql_count2].=g4p_protect_var($ctry).'","'.g4p_protect_var($phon1)
            .'","'.g4p_protect_var($phon2).'","'.g4p_protect_var($phon3)
            .'","'.g4p_protect_var($email1).'","'.g4p_protect_var($email2)
            .'","'.g4p_protect_var($email3).'","';
        $g4p_requetes['addr'][$sql_count2].=g4p_protect_var($fax1).'","'.g4p_protect_var($fax2)
            .'","'.g4p_protect_var($fax3).'","'.g4p_protect_var($www1)
            .'","'.g4p_protect_var($www2).'","'.g4p_protect_var($www3).'")';
    }
    else
    {
        $sql_count1++;
        $g4p_requetes['addr'][$sql_count2].= ', ('.$addr_id.','.$g4p_base.',"'.g4p_protect_var($addr)
            .'","'.g4p_protect_var($city).'","'.g4p_protect_var($stae)
            .'","'.g4p_protect_var($post).'","';
        $g4p_requetes['addr'][$sql_count2].=g4p_protect_var($ctry).'","'.g4p_protect_var($phon1)
            .'","'.g4p_protect_var($phon2).'","'.g4p_protect_var($phon3)
            .'","'.g4p_protect_var($email1).'","'.g4p_protect_var($email2)
            .'","'.g4p_protect_var($email3).'","';
        $g4p_requetes['addr'][$sql_count2].=g4p_protect_var($fax1).'","'.g4p_protect_var($fax2)
            .'","'.g4p_protect_var($fax3).'","'.g4p_protect_var($www1)
            .'","'.g4p_protect_var($www2).'","'.g4p_protect_var($www3).'")';
    }
    
    return $addr_id;
}

function g4p_multimedia_file_refn($bloc)
{
    /*
    +1 FILE <MULTIMEDIA_FILE_REFN> {1:M} p.54
        +2 FORM <MULTIMEDIA_FORMAT> {1:1} p.54
        +3 TYPE <SOURCE_MEDIA_TYPE> {0:1} p.62
        +2 TITL <DESCRIPTIVE_TITLE> {0:1} p.48
    */
    global $g4p_requetes, $g4p_base;

    $max=count($bloc)-1;
    $file=$form=$type=$titl='';
    //print_r($bloc);
           
    for($i=0;$i<=$max;$i++)
    {
        //echo $bloc[$i]['tag']."\n";
        switch(strtoupper(trim($bloc[$i]['tag'])))
        {            
            case 'FILE':
            $file=$bloc[$i]['text'];
            break;
            
            case 'FORM':
            $form=$bloc[$i]['text'];
            break;
            
            case 'TYPE':
            $type=$bloc[$i]['text'];
            break;

            case 'TITL':
            $titl=$bloc[$i]['text'];
            break;

            default:
            echo 'tag qui a passé à travers (bloc_cont) ',$bloc[$i]['tag'],' ',$bloc[$i][0],' ',$bloc[$i-1][0],"\n";
            break;
        }
    }

    return array('file'=>$file, 'form'=>$form, 'type'=>$type, 'titl'=>$titl);
}

function g4p_protect_var($var)
{   
    global $g4p_mysqli; 
    
    if(is_array($var))
    {
        foreach($var as $key=>$val)
            $var[$key]=g4p_protect_var($val);
        return $var;
    }
    else
        return $g4p_mysqli->real_escape_string(trim($var));
}

function g4p_user_reference_number($bloc)
{
    /*
    +1 REFN <USER_REFERENCE_NUMBER> {0:M} p.63, 64
        +2 TYPE <USER_REFERENCE_TYPE> {0:1} p.64
    */

    global $g4p_requetes, $g4p_base;
    static $sql_count1=0;
    static $sql_count2=0;

    $max=count($bloc)-1;
    $refn=$type='';
    //print_r($bloc);
        
    $refn_id=g4p_new_id('genea_refn');
    
    for($i=0;$i<=$max;$i++)
    {
        //echo $bloc[$i]['tag']."\n";
        switch(strtoupper(trim($bloc[$i]['tag'])))
        {            
            case 'REFN':
            $refn=$bloc[$i]['text'];
            break;
            
            case 'TYPE':
            $type=$bloc[$i]['text'];
            break;
            
            default:
            echo 'tag qui a passé à travers (g4p_user_reference_number) ',$bloc[$i]['tag'],' ',$bloc[$i][0],' ',$bloc[$i-1][0],"\n";
            break;
            //ignore
            //+1 ANCI @<XREF:SUBM>@ {0:M} p.28
            //+1 DESI @<XREF:SUBM>@ {0:M} p.28
            //+1 AFN <ANCESTRAL_FILE_NUMBER> {0:1} p.42
            
        }
    }
    
    if($sql_count1%250==0)
    {
        $sql_count1++;
        $sql_count2++;
        
        $g4p_requetes['refn'][$sql_count2]='INSERT INTO genea_refn ';
        $g4p_requetes['refn'][$sql_count2].='(refn_id, refn_num, refn_type) VALUES ';
        $g4p_requetes['refn'][$sql_count2].= '('.$refn_id.',"'
            .g4p_protect_var($refn).'","'.g4p_protect_var($type).'")';
    }
    else
    {
        $sql_count1++;
        $g4p_requetes['refn'][$sql_count2].= ', ('.$refn_id.',"'
            .g4p_protect_var($refn).'","'.g4p_protect_var($type).'")';
    }
    
    return $refn_id;
}

?>
