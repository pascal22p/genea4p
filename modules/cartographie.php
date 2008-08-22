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
 * dernière mise à jour : juillet 2005                                     *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='../';
require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

if(!file_exists($g4p_chemin.'p_conf/g4p_config.php'))
{
  header('location:'.$g4p_chemin.'install.php');
  exit;
}
if(!is_dir($g4p_chemin.'modules/cartographie'))
{
  require_once($g4p_chemin.'entete.php');
  echo '<div class="cadre">Le module n\'est pas installé, télécharger le sur <a href="http://www.parois.net">http://www.parois.net</a></div>';
  require_once($g4p_chemin.'pied_de_page.php');
  exit;
}
if(!$g4p_config['module_carto'])
{
  require_once($g4p_chemin.'entete.php');
  echo '<div class="cadre">Le module est désactivé</div>';
  require_once($g4p_chemin.'pied_de_page.php');
  exit;
}
include($g4p_chemin.'modules/cartographie/cartographie.php');

if(!isset($_REQUEST['g4p_action']))
  $_REQUEST['g4p_action']='';

switch($_REQUEST['g4p_action'])
{
    //////////////////////////////////////
    // Accueil
    ////////////////////////////////////////
    default :
    require_once($g4p_chemin.'entete.php');
    echo '<div class="cadre"><form class="formulaire" method="post" action="',g4p_make_url('modules','cartographie.php','',0),'">
    <h2>Cartographie des évènements</h2>';
    echo '<em>Type de carte : </em>';
    echo '<select id="type_carte" name="type_carte">';
    foreach($g4p_config['cartographie']['cartes'] as $une_carte){
      echo '<option value="'.$une_carte.'"';
      if(!empty($_REQUEST['type_carte']))
      	if($_REQUEST['type_carte']==$une_carte)
      	  echo ' selected="selected"';
      echo '>',$g4p_langue['cartographie']['cartes'][$une_carte],'</option>';
    }
    echo '</select><br />';
    
    echo '<em>Type d\'évènement : </em>';
    echo '<select id="event" name="event">';
    echo '<option value="tous">Tous</option>';    
    foreach($g4p_tag_def as $key=>$val){
      echo '<option value="'.$key.'"';
      if(!empty($_REQUEST['event']))
      	if($_REQUEST['event']==$key)
      	  echo ' selected="selected"';
	  echo '>',$val,'</option>';
    }
    echo '</select><br />';
    
    echo '<em>Afficher le nom des villes : </em>';
    echo '<select id="nom_villes" name="nom_villes">';
    if(!isset($_REQUEST['nom_villes'])){
      echo '<option value="1">oui</option>';
      echo '<option value="0">non</option>';
    }else{
      echo '<option value="1" '.(($_REQUEST['nom_villes']==1)?'selected="selected"':'').'>oui</option>';
      echo '<option value="0" '.(($_REQUEST['nom_villes']==0)?'selected="selected"':'').'>non</option>';
    }
    echo '</select><br />';

    echo '<em>Taille du texte : </em>';
    echo '<input type="text" name="taille_texte" value="'.(empty($_REQUEST['taille_texte'])?'7':$_REQUEST['taille_texte']).'" /><br />';

    echo '<input type="submit" value=" Générer " />
      </form></div>';
      
      
      
    if(!empty($_REQUEST['type_carte']))
    {
      $map=new carte($g4p_chemin.'modules/cartographie/'.$_REQUEST['type_carte'].'/'.$_REQUEST['type_carte']);
      $map->g4p_set_chemin_cache($g4p_chemin.'cache/cartographie/');
      $map->g4p_set_font($g4p_chemin.'modules/cartographie/font/VeraSe.ttf');
      $map->g4p_set_limite_adm(1);//1 trace les contours, 0 non (en fait ils sont invisibles...
      $map->g4p_set_marges(20);
      $map->g4p_set_dim("480");
      $map->g4p_set_ignore_cache(0);
      $map->g4p_read_map(); 
  
      $map->g4p_calcul_echelle();
      echo '<map name="map">';
      echo $map->g4p_img_map();
      echo '</map>';
    
    if(!empty($_REQUEST['zoom']))
      $zoom='&amp;zoom='.$_REQUEST['zoom'];
    else
      $zoom='';
    
      echo '<br /><hr /><center>Cliquer sur une zone de la carte pour zoomer<br /><img usemap="map" src="'.g4p_make_url('modules','cartographie.php','g4p_action=exec&amp;type_carte='.$_REQUEST['type_carte'].'&amp;event='.$_REQUEST['event'].'&amp;taille_carte=480&amp;nom_villes=0&amp;usemap=1'.$zoom,0).'" />';
    echo '<br /><em>Voir la carte : </em>';
    foreach($g4p_config['cartographie']['taille'] as $une_carte)
      echo '<a target="_blank" href="'.g4p_make_url('modules','cartographie.php','g4p_action=exec&amp;type_carte='.$_REQUEST['type_carte'].'&amp;event='.$_REQUEST['event'].'&amp;taille_carte='.$une_carte.'&amp;nom_villes='.$_REQUEST['nom_villes'].'&amp;taille_texte='.$_REQUEST['taille_texte'].$zoom,0).'">',$une_carte,' pixels</a> ';
    echo '</center>';
    }

    break;
    
    case 'exec':
    
    $carte=new carte($g4p_chemin.'modules/cartographie/'.$_REQUEST['type_carte'].'/'.$_REQUEST['type_carte']);
    $carte->g4p_set_chemin_cache($g4p_chemin.'cache/cartographie/');
    $carte->g4p_set_font($g4p_chemin.'modules/cartographie/font/VeraSe.ttf');
    $carte->g4p_set_limite_adm(1);//1 trace les contours, 0 non (en fait ils sont invisibles...
    $carte->g4p_set_marges(20);
    $carte->g4p_set_dim($_REQUEST['taille_carte']);
    $carte->g4p_set_ignore_cache(0);
    if(!empty($_REQUEST['zoom']))
      $carte->g4p_set_select_region(array($_REQUEST['zoom']));
    //$carte->g4p_set_select_region_color(array(10=>array(255,0,0)));
      
    //Le cache utilise les paramètres de l'objet cache, définir d'autres paramètres tel que le titre déclenche  la création d'un cache alors que c'est inutile.
    $carte->g4p_read_map();    

    $carte->g4p_set_citydiam(5);
    if(!empty($_REQUEST['taille_texte']))
      $carte->g4p_set_fontsize($_REQUEST['taille_texte']);
    $carte->g4p_build_map();    
    
    if(!empty($_REQUEST['usemap']) and empty($_REQUEST['zoom']))
      $carte->g4p_color_active_area();
    /*
    echo '<pre>';
    print_r($carte->liste_region);
    exit;
    //*/
    
    $sql = "SELECT genea_place.place_id, CONCAT(IF(place_lieudit='',place_ville,place_lieudit),'(',count(genea_place.place_id),')') as place_nom, place_latitude, place_longitude FROM genea_events
       		LEFT JOIN genea_place USING (place_id) 
       		WHERE genea_events.base=".$_SESSION['genea_db_id'].' AND place_latitude!=0 AND place_longitude!=0';
       		
    if($_REQUEST['event']!='tous')
    {
      $carte->g4p_set_title('Répartition géographique de : '.$g4p_tag_def[$_REQUEST['event']]);
      $sql.=" AND genea_events.type='".$_REQUEST['event']."'";
    }
    else
      $carte->g4p_set_title('Répartition géographique de tous les évènements');
      
    $sql .= " GROUP BY genea_place.place_id";
    
    $carte->g4p_set_nom_villes($_REQUEST['nom_villes']);    
    if($g4p_result=g4p_db_query($sql))
    {
      if($g4p_result=g4p_db_result($g4p_result))
      {
        foreach($g4p_result as $result)
        {
          $carte->g4p_place_ville(floatval($result['place_latitude']),floatval($result['place_longitude']),$result['place_nom']);
        }
        $carte->g4p_place_ville_libele();
      }
    }
    
    //echo '<pre>';
    $carte->g4p_send_carte();
    exit;
    
    break;

}

require_once($g4p_chemin.'pied_de_page.php');
?>
