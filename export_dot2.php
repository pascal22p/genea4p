<?php
/** Export de la famille d'une personne vers un format
 * de fichier DOT (graphviz.org). Ce format permet avec le
 * programme DOT de générer des graphes.
 * @date septembre 2005
 * @author Valentin Valls
 * @réécrit par Pascal Parois
 * @version 0.50
 */
$g4p_chemin='';

require_once($g4p_chemin . 'p_conf/g4p_config.php');
require_once($g4p_chemin . 'p_conf/script_start.php');
require_once($g4p_chemin . 'include_sys/sys_functions.php');

$DOT_FORMAT_EXPORT = array(
    // format dot
    'dot'  => 'Dot',
    // format d'image pixmap
    'png'  => 'Png',
    'jpg'  => 'Jpeg',
    'gif'  => 'Gif',
    // format d'image vectoriel
    'ps'   => 'Ps',
    'svg'  => 'Svg',
    'svgz' => 'Svgz',
    'fig'  => 'Fig',
    'vrml' => 'Vrml',
);

if(empty($_POST['gen']))
{
  require_once($g4p_chemin . 'entete.php');
  
  echo '<div class="cadre" style="text-align:left;">';
  echo '<h2>',$g4p_langue['dot_titre'],'</h2>';
  echo "<h3>",$g4p_langue['dot_sstitre_info'],"</h3>";
  
  if(!empty($_REQUEST['g4p_id']))
    $info = g4p_load_indi_infos($_REQUEST['g4p_id']);
  elseif(!empty($g4p_indi->indi_id))
    $info = g4p_load_indi_infos($g4p_indi->indi_id);
  else
    $info=false;
  
  echo '<form action="',g4p_make_url('','export_dot2.php','',0),'" method="POST">';
  echo '<label for="g4p_id">';
  if (!empty($info) and !empty($info->indi_id)) 
  {
    echo '<a href="'.g4p_make_url('','index.php','g4p_action=fiche_indi&id_pers=' . $info->indi_id,'fiche-'.g4p_prepare_varurl($info->nom).'-'.g4p_prepare_varurl($info->prenom).'-'.$info->indi_id) . '">' . $info->nom . ' ' . $info->prenom . '</a> ';
  } 
  else 
  {
    echo $g4p_langue['id_inconnu'];
  }
  echo '</label>';
  echo '<input type="text" name="g4p_id" value="' . @$info->indi_id . '" size="4" />';
  echo ' <input type="submit" value="',$g4p_langue['dot_submit_id'],'" />';
  echo '</form>';
  echo '<a href="',g4p_make_url('','recherche.php','type=indi_0&g4p_referer='.rawurlencode('|export_dot2.php|'.$_SERVER['QUERY_STRING']).'&g4p_champ=g4p_id',0).'">Rechercher Identifieur</a>';
  
  
  echo "<h3>",$g4p_langue['dot_sstitre_config'],"</h3>";
  ?>
  <form action="<?=g4p_make_url('','export_dot2.php','',0)?>" method="POST">
  <input type="hidden" name="g4p_id" value="<?=$info->indi_id?>" />
  
      <strong>Type d'extraction :</strong>
      <div style="position:relative;float:left;padding-right:5em;">
      <input type="radio" class="coche" name="type" value="asc" checked ><?=$g4p_langue['dot_arbre_asc']?></input>
      <br/><input type="radio" class="coche" name="type" value="des"><?=$g4p_langue['dot_arbre_desc']?></input>
      <br/><input type="radio" class="coche" name="type" value="both"><?=$g4p_langue['dot_arbre_asc_desc']?></input>
      <br/><input type="radio" class="coche" name="type" value="nolimit"><?=$g4p_langue['dot_arbre_complet']?></input>
      </div>
      <div style="position:relative;float:left">
      <input type="checkbox" class="coche" name="showdate" value="true" checked><?=$g4p_langue['dot_date_event']?></input>
      <br/>
      <input type="checkbox" class="coche" name="showlocation" value="true"><?=$g4p_langue['dot_lieu_event']?></input>
      <br/>
      <input type="checkbox" class="coche" name="showaddress" value="true"><?=$g4p_langue['dot_adresse']?></input>
      </div>
      <hr style="border:none;clear:both;margin:0.5em" />
  
      <strong>       
  <br />
  <?php 
  if(!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
  {
  printf($g4p_langue['dot_limit_gen'],$g4p_config['dot_limit_gen']);
  echo '<br />';
  }
  ?>
      </strong>
      <strong><?=$g4p_langue['dot_max_asc']?></strong>
      <br/><input type="text" name="ascdepth" value="7" size="4" />
      &nbsp;&nbsp;
      
  <?php 
  if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
       echo '<input type="checkbox" class="coche" name="useascdepth" >',$g4p_langue['dot_nolimit'],'</input>';
  ?>         
      <br/>
      <strong><?=$g4p_langue['dot_max_desc']?></strong>
      <br/><input type="text" name="desdepth" value="7" size="4" />
      &nbsp;&nbsp;
  <?php 
  if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
       echo '<input type="checkbox" class="coche" name="usedesdepth" >',$g4p_langue['dot_nolimit'],'</input>';
  ?>         
      <br/>
      <br/>
  
  <?php if ($g4p_config['USE_DOT'] == true) {
      echo '<strong>',$g4p_langue['dot_format'],'</strong>';
      $checked = ' checked';
      foreach($DOT_FORMAT_EXPORT as $extention => $name) {
          echo '<input class="coche" type="radio" name="gen" value="'
          . $extention . '"' . $checked . '>' . $name . '</input>' . "\n";
          $checked = '';
      }
      echo '<br/>';
  } else { ?>
      <input type="hidden" name="gen" value="dot" />
  <?php } ?>
      <strong><?=$g4p_langue['dot_divers']?></strong>
      <input type="radio" class="coche" name="view" value="true" checked><?=$g4p_langue['dot_visualiser']?></input>
      <input type="radio" class="coche" name="view" value="false"><?=$g4p_langue['dot_telecharger']?></input>
      <br/>
  
  <br/><input type="submit" value="<?=$g4p_langue['dot_submit_executer']?>" />
  </form>
  <?php
  
  /*    if (EXPORT_DOT_DEBUG) {
      echo '<pre>';
      getDotText($rootId);
      echo '</pre>';
  }*/
  
  echo '</div>';
  require_once($g4p_chemin . 'pied_de_page.php');
}
else
{
  function g4p_charge_indi($indi_id)
  {
    global $indi_labels, $generations, $generation_cpt  ;

    $info=g4p_load_indi_infos($indi_id);

    if(!isset($indi_labels['i'.$info->indi_id]))
    {
      $g4p_naissance=$g4p_deces='';
      if(isset($g4p_mon_indi->evenements))
      {
        foreach($g4p_mon_indi->evenements as $g4p_a_event)
        {
          if($g4p_a_event->type=='BIRT' or $g4p_a_event->type=='BAPM')
          {
            $g4p_naissance=g4p_date($g4p_a_event->date,'date2');
            $g4p_naissance=preg_replace("'([a-z])*'", '', $g4p_naissance);
          }
          if($g4p_a_event->type=='DEAT' or $g4p_a_event->type=='BURI')
          {
            $g4p_deces=g4p_date($g4p_a_event->date,'date2');
            $g4p_deces='+'.preg_replace("'([a-z])*'", '', $g4p_deces);
          }
        }
      }
      $tmp=$g4p_naissance.' '.$g4p_deces;
      $indi_labels['i'.$info->indi_id]=$info->prenom.' '.$info->nom.'\n'.$tmp;
      $generations['i'.$generation_cpt][]='i'.$info->indi_id;
    }
    return $info;
  }  

  function g4p_recursive_load($indi_id,$generation_cpt=0)
  {
    global $indi_labels, $generations, $generationsf, $links1, $links2, $familles_labels, $limit, $generation_cpt;
  
    ++$limit;
    if($limit>500)
      die('pouf');
    $info=g4p_charge_indi($indi_id);

//    if($generation_cpt<-10 or $generation_cpt>10)
//      return;

    
    if((($_POST['type']=='des' or $_POST['type']=='both') and $generation_cpt>=0) or $_POST['type']=='nolimit')
    {
      if(isset($info->familles))
      {
        foreach($info->familles as $g4p_a_famille)
        {
          $tmp='';
          if(isset($g4p_a_famille->evenements))
            foreach($g4p_a_famille->evenements as $g4p_a_event)
              if($g4p_a_event->type=='MARR')
                $tmp='x '.g4p_date($g4p_a_event->date);

          $links1[]=array('couple'=>'i'.$info->indi_id, 'famille'=>'f'.$g4p_a_famille->id);
          $familles_labels['f'.$g4p_a_famille->id]=$tmp;

          $generationsf['f'.$generation_cpt][]='f'.$g4p_a_famille->id;
          if(!empty($g4p_a_famille->conjoint) and $g4p_a_famille->conjoint->indi_id!=0)
          {
            $links1[]=array('couple'=>'i'.$g4p_a_famille->conjoint->indi_id, 'famille'=>'f'.$g4p_a_famille->id); 
            g4p_charge_indi($g4p_a_famille->conjoint->indi_id);
            if(isset($g4p_a_famille->enfants))
            {
              ++$generation_cpt;
              foreach($g4p_a_famille->enfants as $g4p_a_enfant)
              {
                $links2[]=array('couple'=>'i'.$g4p_a_enfant->indi_id, 'famille'=>'f'.$g4p_a_famille->id);
                g4p_charge_indi($g4p_a_enfant->indi_id,$generation_cpt);
                g4p_recursive_load($g4p_a_enfant->indi_id,$generation_cpt);
              }
            }
          }
        }
      }
    }
    
    // les parents
    if(isset($info->parents) and ((($_POST['type']=='asc' or $_POST['type']=='both') and $generation_cpt<=0) or $_POST['type']=='nolimit'))
    {
      --$generation_cpt;
      foreach($g4p_indi->parents as $key=>$g4p_a_parent)
      {
        if($g4p_a_parent['rela_type']=='BIRTH')
        {
          if(!empty($g4p_a_parent['pere']))
          {
            g4p_charge_indi($g4p_a_parent['pere']->indi_id);
            $links2[]=array('couple'=>'i'.$g4p_a_parent['pere']->indi_id, 'famille'=>'f'.$key);
            
            /*
            $tmp='';
            if(!empty($info->familles[$key]->evenements))
            {
              foreach($info->familles[$key]->evenements as $g4p_a_event)
                if($g4p_a_event->type=='MARR')
                  $tmp='x '.g4p_date($g4p_a_event->date);
              
                
              if(!empty($info->familles[$key]->conjoint) and $info->familles[$key]->conjoint->indi_id!=0)
              {
                $links2[]=array('couple'=>'i'.$info->familles[$key]->conjoint->indi_id, 'famille'=>'f'.$info->familles[$key]->id);
                g4p_charge_indi($info->familles[$key]->conjoint->indi_id);
                g4p_recursive_load($info->familles[$key]->conjoint->indi_id,$generation_cpt);
              }
            }*/
            $familles_labels['f'.$info->familles[$key]->id]=$tmp;
            $generationsf[$generation_cpt][]='f'.$info->familles[$key]->id;
echo $info->indi_id,'-',$g4p_a_parent['pere']->indi_id,';';
            g4p_recursive_load($g4p_a_parent['pere']->indi_id,$generation_cpt);
          }
        }
      }
    }
  }

$links1=array();
$links2=array();
$limit=0;
g4p_recursive_load($_POST['g4p_id']);

$dot=fopen('arbre.dot','w');
fwrite($dot,"digraph structs {\nranksep=.75; concentrate=true;\n");
/*
echo '<pre>';
print_r($generations);
*/
fwrite($dot,"node [shape=record];\n");
foreach($generations as $a_generation)
{
  fwrite($dot,"{ rank = same; \"".implode('"; "',$a_generation).'"};'."\n");
}

fwrite($dot,"node [shape=triangle];\n");
foreach($generationsf as $a_generation)
  fwrite($dot,"{ rank = same; \"".implode('"; "',$a_generation).'"};'."\n");

foreach($indi_labels as $key=>$a_label)
  fwrite($dot,$key.' [label="'.$a_label.'"];'."\n");

foreach($familles_labels as $key=>$a_label)
  fwrite($dot,$key.' [label="'.$a_label.'"];'."\n");
   
fwrite($dot,'edge[arrowhead=none, arrowtail=none];'."\n");
foreach($links2 as $val)
  fwrite($dot,$val['famille'].'->'.$val['couple'].";\n");
foreach($links1 as $val)
  fwrite($dot,$val['couple'].'->'.$val['famille'].";\n");
    
fwrite($dot,'}');
fclose($dot);

$cmd = "dot -T png arbre.dot -o test";
$result = shell_exec($cmd);

if (file_exists('test')) 
{
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-cache, must-revalidate");
  header("Pragma: no-cache");

  //header("Content-type: image/png");
  /*
echo '<pre>';
  readfile('arbre.dot');//*/

  $fp = fopen('test', 'rb');
  echo fread ($fp, filesize ('test'));
  fclose ($fp);//*/
}
          
  
  
  
  
  
  
  
  
  


}

?>