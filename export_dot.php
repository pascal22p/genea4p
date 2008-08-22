<?php
/** Export de la famille d'une personne vers un format
 * de fichier DOT (graphviz.org). Ce format permet avec le
 * programme DOT de générer des graphes.
 * @date 5 december 2004
 * @author Valentin Valls
 * @version 0.40
 */
$g4p_chemin='';

require_once($g4p_chemin . 'p_conf/g4p_config.php');
require_once($g4p_chemin . 'p_conf/script_start.php');
require_once($g4p_chemin . 'include_sys/sys_functions.php');

//------------------- EXPORT SETUP
//voir fichier de config

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

// ---------------------- GLOBAL MEMORY STRUCTURE

$dotConfig = array(
    'maxDepth'     => 99999,
    'stateFamily'  => 'shape=ellipse,style=filled,fillcolor=gray90,label="",fontsize=8,fontname=courier',
    'stateF'       => 'shape=box,style=filled,fillcolor=lightpink1,fontsize=8,fontname=courier',
    'stateM'       => 'shape=box,style=filled,fillcolor=lightblue1,fontsize=8,fontname=courier',
    'edgeChild'    => 'arrowtail=none,arrowhead=dot,color=black,tailport=s,headport=n,constraint=true,style=filled',
    'edgeParent'   => 'arrowtail=none,arrowhead=odot,color=black,tailport=n,headport=s,constraint=true,style=filled',
    'edgeUnion'    => 'arrowtail=none,arrowhead=none,color=gray50,tailport=w,headport=e,constraint=false,style=dotted',
    'displayAsc'   => false,
    'displayDes'   => false,
    'displayExtra' => false,
    'download'     => true,
    'location'     => false,
    'date'         => false,
    'address'      => false,
);

$dotMemory = array(
    // need to do some size optimisation
    'bufferEdgeChild'  => '',
    'bufferEdgeParent' => '',
    'bufferEdgeUnion'  => '',
    'bufferStateM'     => '',
    'bufferStateF'     => '',
    'bufferFamily'     => '',
);

// ---------------------- MEMORY CACHE

/** sauve les info des personnes chargées */
$dotInfo = array();
$dotFamilyInfo = array();
$link_list=array();

/** retour les infos a propos d'un id */
function getPersonInfoWithoutCache($id) {
    global $dotInfo;
    if (array_key_exists($id, $dotInfo)) {
        return $dotInfo[$id];
    }
    return g4p_load_indi_infos($id);
}

/** retour les infos a propos d'un id */
function getPersonInfo($id) {
    global $dotInfo;
    if (!array_key_exists($id, $dotInfo)) {
        $dotInfo[$id] = g4p_load_indi_infos($id);
    }
    return $dotInfo[$id];
}

function isPersonLoaded($id) {
    global $dotInfo;
    return array_key_exists($id, $dotInfo);
}

/** retourne la famille constituée des deux conjoint passées en paramettre */
// modif pascal : Ne sert à rien ?!?!
/*
function findFamily($id1, $id2) {
    global $dotFamilyInfo;
    $info1 = getPersonInfoWithoutCache($id1);
    foreach($info1->familles as $familyId => $family) {

        // si ya pas de conjoint on continu
        // ### A CORRIGER famille composée d'une seul personne
        if (!isset($family['conjoint'])) continue;
        
        $conjointId = $family['conjoint']->indi_id;
        if ($conjointId == $id2) {
            return $family;
        }
    }
    return null;
} */

/** place la famille en memoire 
 * ### Truc de merde vue qu'on l'a deja :D en param
 */
function getFamily($family) {
    global $dotFamilyInfo;
    $dotFamilyInfo[$family->id] = $family;
    return $dotFamilyInfo[$family->id];
}

function isFamilyLoadedById($familyId) {
    global $dotFamilyInfo;
    return array_key_exists($familyId, $dotFamilyInfo);
}

function isFamilyLoaded($family) {
    global $dotFamilyInfo;
    return array_key_exists($family->id, $dotFamilyInfo);
}

function isLinkLoaded($id)
{
  global $link_list;
  return in_array($id,$link_list);
}

// ---------------------- DOT GENERATOR

function generateEvent($event) {
    global $dotConfig;
    $text = '';
    if ($dotConfig['date'] && $event->date != '') {
        $text .= ' ' . g4p_date($event->date, 'date2');
    }
    if ($dotConfig['location'] && !empty($event->lieu)) {
        $text .= ' à ' . $event->lieu->toCompactString();
    }
    return $text;
}

function generateFamily($family, $depth) {
    global $dotConfig, $dotMemory;
    $extra = '';
    $extra_marriage = '';
    if (isset($family->evenements)) {
        foreach($family->evenements as $event) {
            switch($event->type) {
            case 'MARR':
                $extra_marriage = generateEvent(&$event);
                break;
            }
        }
    }
    if ($extra_marriage != '') {
        $extra .= 'x' . $extra_marriage;
    }
    $dotMemory['bufferFamily'] .= "\tf" . $family->id
    . ' [label="' . $extra . '"'
    . ',group=' . $depth . "];\n";
}

function generateIndividu($info, $depth) {
    global $dotConfig, $dotMemory;

    // extrait quelques infos sur la vie de l'individue
    $extra = '';
    $extra_birth = '';
    $extra_death = '';
    $extra_address = '';
    
    if(isset($info->evenements))
    {
      foreach($info->evenements as $event) {
          switch($event->type) {
          case 'ADDR':
              if (!empty($event->lieu)) $extra_address = ' de ' . $event->lieu->toCompactString();
              break;
          case 'BIRT':
          case 'BAPM':
              $extra_birth = generateEvent(&$event);
              break;
          case 'DEAT':
          case 'BURI':
              $extra_death = generateEvent(&$event);
              break;
          }
      }
    }
    
    if ($extra_address != '') {
        $extra .= ' \n' . $extra_address;
    }
    if ($extra_birth != '') {
        $extra .= ' \no' . $extra_birth;
    }
    if ($extra_death != '') {
        $extra .= ' \n+' . $extra_death;
    }
  
    // formate la représentation des infos
    $description = "\t" . $info->indi_id
    . ' [label="'   . $info->nom . ' ' . $info->prenom . $extra . '"'
    . ',group=' . $depth . "];\n";
    
    switch ($info->sexe) {
    case 'M':
        $dotMemory['bufferStateM'] .= $description;
        break;
    // case 'F':
    default:
        $dotMemory['bufferStateF'] .= $description;
        break;
    }
}

define('CIBLE'      ,0);
define('DESCENDANT' ,1);
define('ASCENDANT'  ,2);
define('EXTRA'      ,4);
define('JUSTINFO'   ,8);

// modif pascal : Ca commence ici! J'ai tout réécris, je n'arrivais pas à faire ce que je voulais
function getRecDotText($rootId, $familyId, $depth, $flag) {
    global $dotConfig, $dotMemory, $link_list;

    // si la personne a déjà été traité !
    if (isPersonLoaded($rootId)) return;
    $info = getPersonInfo($rootId);

    // génère les information sur l'individu
    generateIndividu(&$info, $depth);


    //traite le conjoint d'une seule famille pour l'affichage de l'ascendance,
    //1ère condit°=affichage de l'ascendance + descendance ($depth>0 => partie ascendante)
    //2ème condition : ascendance pure
    if(($depth>0 and !$dotConfig['displayExtra'] and $dotConfig['displayAsc'] and $dotConfig['displayDes']) or (!$dotConfig['displayExtra'] and $dotConfig['displayAsc'] and !$dotConfig['displayDes']))
    {
        // traite les mariages et la descendance de la personne
        if (isset($info->familles[$familyId])) {
            $isFamilyLoaded = isFamilyLoadedById($familyId);

            // genere la famille
            if (!$isFamilyLoaded) {
                getFamily($info->familles[$familyId]); // désolé
                generateFamily(&$info->familles[$familyId], $depth);

                // genere et lie le conjoint a la famille
                if (isset($info->familles[$familyId]->conjoint)) {

                    $id = $info->familles[$familyId]->conjoint->indi_id;
                    // $hasLoaded = isPersonLoaded($id);
                    //modif pascal, j'ai trop compris les anciennes lignes, je prefere ca:
                    $newflag=$flag;
                    if(!$hasLoaded=isPersonLoaded($id))
                      getRecDotText($id, $familyId, $depth, $newflag);

                    // cree un lien avec la famille si ce n'est pas fait
                    if (!isLinkLoaded($id.'-f'.$familyId)) {
                        $link = "\t" . $id . ' -> f' . $familyId . ";\n";
                        $dotMemory['bufferEdgeParent'] .= $link;
                        $link_list[]=$id.'-f'.$familyId;
                    }

                    
                    // cree une contrainte entre deux personne d'un union
                    if (!isLinkLoaded($rootId.'-'.$id)) {
                        $link = '';
                        switch ($info->sexe) {
                        case 'M': $link = "\t" . $rootId . ' -> ' . $id . ";\n"; break;
                        //default:  $link = "\t" . $id . ' -> ' . $rootId . ";\n"; break;
                        // en faisant M+F, on double les traits...
                        }
                        $dotMemory['bufferEdgeUnion'] .= $link;
                        $link_list[]=$rootId.'-'.$id;
                    }
               }
           } else {
                // cree une contrainte entre deux personne d'un union
                if (isset($info->familles[$familyId]->conjoint)) {
                    $id = $info->familles[$familyId]->conjoint->indi_id;
                    if (!isLinkLoaded($rootId.'-'.$id)) {
                        $link = '';
                        switch ($info->sexe) {
                        case 'M': $link = "\t" . $rootId . ' -> ' . $id . ";\n"; break;
                        //default:  $link = "\t" . $id . ' -> ' . $rootId . ";\n"; break;
                        }
                        $dotMemory['bufferEdgeUnion'] .= $link;
                        $link_list[]=$rootId.'-'.$id;
                    }
                }
            }
        }
    }
    else
    {
      // traite les mariages et/ou la descendance de la personne
      if (isset($info->familles)) {
        // modif pascal
        if(!isset($_POST['usedesdepth']) and floatval('-'.($_POST['desdepth']))>=$depth)
           return;

            foreach ($info->familles as $familyId => $family) {

                $isFamilyLoaded = isFamilyLoadedById($familyId);

                // genere la famille
                if (!$isFamilyLoaded) {
                    getFamily($family); // désolé
                    generateFamily(&$family, $depth);

                    // genere et lie le conjoint a la famille
                    if (isset($family->conjoint)) {

                        $newflag=$flag;

                        $id = $family->conjoint->indi_id;
                        if(!$hasLoaded=isPersonLoaded($id))
                          getRecDotText($id, $familyId, $depth, $newflag);

                        // cree une contrainte entre deux personne d'un union
                        if (!isLinkLoaded($rootId.'-'.$id)) {
                            $link = '';
                            switch ($info->sexe) {
                            case 'M': $link = "\t" . $rootId . ' -> ' . $id . ";\n"; break;
                            //default:  $link = "\t" . $id . ' -> ' . $rootId . ";\n"; break;
                            }
                            $dotMemory['bufferEdgeUnion'] .= $link;
                            $link_list[]=$rootId.'-'.$id;
                        }
                    }
                } else {
                    // cree une contrainte entre deux personne d'un union
                    if (isset($family->conjoint)) {
                        $id = $family->conjoint->indi_id;
                        if (!isLinkLoaded($rootId.'-'.$id)) {
                            $link = '';
                            switch ($info->sexe) {
                            case 'M': $link = "\t" . $rootId . ' -> ' . $id . ";\n"; break;
                            //default:  $link = "\t" . $id . ' -> ' . $rootId . ";\n"; break;
                            }
                            $dotMemory['bufferEdgeUnion'] .= $link;
                            $link_list[]=$rootId.'-'.$id;
                        }
                    }

                    // genere et lie les enfants
                    if (isset($family->enfants) and $dotConfig['displayDes'] and floatval('-'.($_POST['desdepth']))<$depth) {

                        $newflag=$flag;

                        foreach ($family->enfants as $id => $individu) {
                            if(!isPersonLoaded($id))
                                getRecDotText($id, $familyId, $depth - 1, $newflag);
                            if (!isLinkLoaded('f'.$familyId.'-'.$id)) {
                                $link = "\tf" . $familyId . ' -> ' . $id . ";\n";
                                $dotMemory['bufferEdgeChild'] .= $link;
                                $link_list[]='f'.$familyId.'-'.$id;
                            }
                        }
                    }
                }
                if (!isLinkLoaded($rootId.'-f'.$familyId)) {
                    $link = "\t" . $rootId . ' -> f' . $familyId . ";\n";
                    $dotMemory['bufferEdgeParent'] .= $link;
                    $link_list[]=$rootId.'-f'.$familyId;
                }
            }
        }
    }

    // filtre
    if ($flag == JUSTINFO) return;

    // si il y a des parents (recurssif)
    //($dotConfig['displayAsc'] or ($depth<0 and !$dotConfig['displayExtra'] and $dotConfig['displayAsc'] and $dotConfig['displayDes']) or ($dotConfig['displayExtra'] and $dotConfig['displayAsc'] and $dotConfig['displayDes'])) = $dotConfig['displayAsc'] ;) ou comment se compliqué la vie!
    if (isset($info->parents) and $dotConfig['displayAsc']) {
        foreach($info->parents as $newFamilyId=>$parent) {
            // modif pascal
            if(!isset($_POST['useascdepth']) and ($_POST['ascdepth']-2)<$depth)
              $flag = JUSTINFO;

            if ($parent['rela_type']=='BIRTH') {
                $fatherId = 'unkown';
                $motherId = 'unkown';

                $newflag=$flag;

                if (!isLinkLoaded('f'.$newFamilyId.'-'.$rootId)) {
                    $link = "\tf" . $newFamilyId . ' -> ' . $rootId . ";\n";
                    $dotMemory['bufferEdgeChild'] .= $link;
                    $link_list[]='f'.$newFamilyId.'-'.$rootId;
                }

                if (isset($parent['pere']) and $parent['pere'] != 0) {
                    $fatherId = $parent['pere']->indi_id;
                    getRecDotText($fatherId, $newFamilyId, $depth + 1, $newflag);
                    if (!isLinkLoaded($fatherId.'-f'.$newFamilyId) ) {
                        $link = "\t" . $fatherId . ' -> f' . $newFamilyId . ";\n";
                        $dotMemory['bufferEdgeParent'] .= $link;
                        $link_list[]=$fatherId.'-f'.$newFamilyId;
                    }
                }

                if (isset($parent['mere']) and $parent['mere'] != 0) {
                    $motherId = $parent['mere']->indi_id;
                    getRecDotText($motherId, $newFamilyId, $depth + 1, $newflag);
                    if (!isLinkLoaded($motherId.'-f'.$newFamilyId)) {
                        $link = "\t" . $motherId . ' -> f' . $newFamilyId . ";\n";
                        $dotMemory['bufferEdgeParent'] .= $link;
                        $link_list[]=$motherId.'-f'.$newFamilyId;
                    }
                }
            }
        }
    }

}

/** Retourne la syntaxe dot de l'arbre ascendant commencant par $rootId
 * @param $rootId l'identifieur de la racine de l'arbre ascendant
 */
function getDotText($rootId) {
    global $dotConfig, $dotMemory, $link_list;
    if ($rootId == 0) return "/* id error */";
    
    $dotMemory['bufferFamily']  = '';
    $dotMemory['bufferStateM'] = '';
    $dotMemory['bufferStateF'] = '';
    $dotMemory['bufferEdgeParent']  = '';
    $dotMemory['bufferEdgeChild']  = '';

    getRecDotText($rootId, 0, 0, CIBLE);

    $result = 'digraph G {' . "\n".'concentrate=false'."\n";

    $result .= "\tnode [" . $dotConfig['stateFamily'] . "];\n";
    $result .= $dotMemory['bufferFamily'];
    $result .= "\n";

    $result .= "\tnode [" . $dotConfig['stateM'] . "];\n";
    $result .= $dotMemory['bufferStateM'];
    $result .= "\n";

    $result .= "\tnode [" . $dotConfig['stateF'] . "];\n";
    $result .= $dotMemory['bufferStateF'];
    $result .= "\n";

    $result .= "\tedge [" . $dotConfig['edgeUnion'] . "];\n";
    $result .= $dotMemory['bufferEdgeUnion'];
    $result .= "\n";

    $result .= "\tedge [" . $dotConfig['edgeChild'] . "];\n";
    $result .= $dotMemory['bufferEdgeChild'];
    $result .= "\n";

    $result .= "\tedge [" . $dotConfig['edgeParent'] . "];\n";
    $result .= $dotMemory['bufferEdgeParent'];

    $result .= "}";
    return $result;
}

// ---------------------- INTERFACE

/** Genere un fichier dans un format media vers l'entree 
 * du navigateur
 * <br/>
 * Cette fonction necessite la presence de l'utilitaire dot
 * sur la machine serveur.
 */
function generateMediaFile($rootId, $extension) {
    global $dotConfig, $link_list, $g4p_langue, $g4p_config;

    // permet de creer un fichier sans que celui-ci soit
    // ecrasé par qq un d'autre avec la meme fonction.
    // ATTENTION c'est une fonction BLOQUANTE
    // ### sem_acquire(SEMAPHORE_ID);

    // genere le fichier dot
    $dot = getDotText($rootId);
    $fp = fopen('family.dot', 'wt');
    fwrite($fp, $dot);
    fclose($fp);

    $image = 'family.' . $extension;
    $cmd = "\"" . $g4p_config['DOT_EXEC'] ."\"".  ' -T' . $extension . ' family.dot -o ' . $image;
    $result = shell_exec($cmd);

    if (file_exists($image)) {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
    
        switch($extension) {
        case 'jpg' : header("Content-type: image/jpeg"); break;
        default:     header("Content-type: image/" . $extension); break;
        }

        if ($dotConfig['download']) {
            header("Content-Disposition: attachment; filename=family." . $extension);
        }
    
        //readfile('family.' . $extension);
        $fp = fopen($image, 'rb');
        echo fread ($fp, filesize ($image));
        fclose ($fp);
    
        //supprime le média généré
        unlink($image);
    } else {
        $message = $g4p_langue['dot_dot_non_present'];
        $message .= '<br/><br/><strong>'.$g4p_langue['dot_ligne'].'</strong><br/>' . $cmd;
        displayMessage($message);
    }

    //supprime le fichier dot
    unlink('family.dot');

    // ### sem_release(SEMAPHORE_ID);
}


/** Genere un fichier de sortie au format DOT */
function generateDotFile($rootId) {
    global $dotConfig;

    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    
    if (!$dotConfig['download']) {
        // ici pour aide au debug
        echo '<pre>';
    }
    
    $dot = getDotText($rootId);

    if ($dotConfig['download']) {
        header("Content-type: text/dot");
        header("Content-Disposition: attachment; filename=family.dot");
        echo $dot;
    } else {
        //header("Content-type: text/txt");
        echo $dot;
        echo '</pre>';
    }

/*    if (EXPORT_DOT_DEBUG) {
        $fp = fopen('dot/family.dot', 'wt');
        fwrite($fp, $dot);
        fclose($fp);
    }*/
}

function updateDotConfig() {
    global $dotConfig, $g4p_langue, $g4p_config;
    
    if (isset($_POST['type'])) {
        $type = $_POST['type'];
        switch($type) {
        case 'asc':
            $dotConfig['displayAsc']   = true;
            $dotConfig['displayDes']   = false;
            $dotConfig['displayExtra'] = false;
            break;
        case 'des':
            $dotConfig['displayAsc']   = false;
            $dotConfig['displayDes']   = true;
            $dotConfig['displayExtra'] = false;
            break;
        case 'both':
            $dotConfig['displayAsc']   = true;
            $dotConfig['displayDes']   = true;
            $dotConfig['displayExtra'] = false;
            break;
        case 'nolimit':
            $dotConfig['displayAsc']   = true;
            $dotConfig['displayDes']   = true;
            $dotConfig['displayExtra'] = true;
            break;
        }
    }
    if (isset($_POST['view'])) {
        $view = $_POST['view'];
        $dotConfig['download'] = $view == 'false';
    }
    if (isset($_POST['showdate']) && $_POST['showdate'] == 'true') {
        $dotConfig['date'] = true;
    }
    if (isset($_POST['showlocation']) && $_POST['showlocation'] == 'true') {
        $dotConfig['location'] = true;
    }
    if (isset($_POST['showaddress']) && $_POST['showaddress'] == 'true') {
        $dotConfig['address'] = true;
    }
    
    if(!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    {
      if($g4p_langue['dot_max_asc']+$g4p_langue['dot_max_desc']>$g4p_config['dot_limit_gen'])
        $g4p_langue['dot_max_asc']=$g4p_langue['dot_max_desc']=ceil($g4p_config['dot_limit_gen']/2);
      if(isset($_POST['useascdepth']))
        unset($_POST['useascdepth']);
      if(isset($_POST['usedesdepth']))
        unset($_POST['usedesdepth']);
    }
}

function displayIntoGenetiqueUI($rootId) {
    global $g4p_chemin, $time_start, $sql_count, $g4p_config, $DOT_FORMAT_EXPORT, $g4p_langue;
    require_once($g4p_chemin . 'entete.php');

    echo '<div class="cadre" style="text-align:left;">';
    echo '<h2>',$g4p_langue['dot_titre'],'</h2>';
    echo "<h3>",$g4p_langue['dot_sstitre_info'],"</h3>";

    $info = getPersonInfoWithoutCache($rootId);
    
    echo '<form action="',g4p_make_url('','export_dot.php','',0),'" method="post">';
    if ($info->indi_id != 0) {
        echo '<a href="'.g4p_make_url('','index.php','g4p_action=fiche_indi&id_pers='
        . $rootId,'fiche-'.g4p_prepare_varurl($info->nom).'-'.g4p_prepare_varurl($info->prenom).'-'.$rootId) . '">' . $info->nom . ' ' . $info->prenom . '</a> ';
    } else {
        echo $g4p_langue['id_inconnu'];
    }
    echo '<input type="text" name="g4p_id" value="' . $rootId . '" size="4" />';
    echo ' <input type="submit" value="',$g4p_langue['dot_submit_id'],'" />';
    echo '</form>';
    echo '<a href="',g4p_make_url('','recherche.php','type=indi_0&g4p_referer='.rawurlencode('|export_dot.php|'.$_SERVER['QUERY_STRING']).'&g4p_champ=g4p_id',0).'">Rechercher Identifieur</a>';


    echo "<h3>",$g4p_langue['dot_sstitre_config'],"</h3>";
    if(!isset($_REQUEST['g4p_id']))
      $_REQUEST['g4p_id']="";
    ?>
    <form action="<?=g4p_make_url('','export_dot.php','',0)?>" method="post">
    <input type="hidden" name="g4p_id" value="<?php echo $_REQUEST['g4p_id']; ?>" />

        <strong>Type d'extraction :</strong>
        <div style="position:relative;float:left;padding-right:5em;">
        <input type="radio" class="coche" name="type" value="asc" checked="checked" /><?=$g4p_langue['dot_arbre_asc']?>
        <br/><input type="radio" class="coche" name="type" value="des" /><?=$g4p_langue['dot_arbre_desc']?>
        <br/><input type="radio" class="coche" name="type" value="both" /><?=$g4p_langue['dot_arbre_asc_desc']?>
        <br/><input type="radio" class="coche" name="type" value="nolimit" /><?=$g4p_langue['dot_arbre_complet']?>
        </div>
        <div style="position:relative;float:left">
        <input type="checkbox" class="coche" name="showdate" value="true" checked="checked" /><?=$g4p_langue['dot_date_event']?>
        <br/>
        <input type="checkbox" class="coche" name="showlocation" value="true" /><?=$g4p_langue['dot_lieu_event']?>
        <br/>
        <input type="checkbox" class="coche" name="showaddress" value="true" /><?=$g4p_langue['dot_adresse']?>
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
         echo '<input type="checkbox" class="coche" name="useascdepth" />',$g4p_langue['dot_nolimit'],'';
?>         
        <br/>
        <strong><?=$g4p_langue['dot_max_desc']?></strong>
        <br/><input type="text" name="desdepth" value="7" size="4" />
        &nbsp;&nbsp;
<?php 
if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
         echo '<input type="checkbox" class="coche" name="usedesdepth" />',$g4p_langue['dot_nolimit'],'';
?>         
        <br/>
        <br/>
    
<?php if ($g4p_config['USE_DOT'] == true) {
        echo '<strong>',$g4p_langue['dot_format'],'</strong>';
        $checked = ' checked="checked"';
        foreach($DOT_FORMAT_EXPORT as $extention => $name) {
            echo '<input class="coche" type="radio" name="gen" value="'
            . $extention . '"' . $checked . ' />' . $name . "\n";
            $checked = '';
        }
        echo '<br/>';
    } else { ?>
        <input type="hidden" name="gen" value="dot" />
<?php } ?>
        <strong><?=$g4p_langue['dot_divers']?></strong>
        <input type="radio" class="coche" name="view" value="true" checked="checked" /><?=$g4p_langue['dot_visualiser']?>
        <input type="radio" class="coche" name="view" value="false" /><?=$g4p_langue['dot_telecharger']?>
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

function displayMessage($message) {
    global $g4p_chemin, $time_start, $sql_count, $g4p_config;
    require_once($g4p_chemin . 'entete.php');
    echo '<div class="cadre" style="text-align:left;">';
    echo $message;
    echo '</div>';
    require_once($g4p_chemin . 'copyright.php');
    require_once($g4p_chemin . 'pied_de_page.php');
}

// ---------------------- EXECUTION

if(!$_SESSION['permission']->permission[_PERM_DOT_])
{
    require_once($g4p_chemin . 'entete.php');
    echo '<div class="cadre" style="text-align:left;">';
    echo $g4p_langue['acces_non_autorise'];
    echo '</div>';
    require_once($g4p_chemin . 'copyright.php');
    require_once($g4p_chemin . 'pied_de_page.php');
    exit;
}

//configure les option de dot en fonction de la requette
updateDotConfig();

// filtre les entrees
$id = 0;
$gen = '';
if (isset($_REQUEST['g4p_id'])) {
    $id = $_REQUEST['g4p_id'];
}
if (isset($_POST['gen'])) {
    $gen = $_POST['gen'];
}

// protection
if ($g4p_config['USE_DOT'] != true && $gen != 'dot') {
    $gen == '';
}
if ($id == 0) {
    $gen == '';
}
if ($gen != '' && !array_key_exists($gen, $DOT_FORMAT_EXPORT)) {
    $gen == '';
}

if($id==0 and !empty($g4p_indi->indi_id))
  $id=$g4p_indi->indi_id;

if (is_numeric($id) && $id >= 0) {
    switch($gen) {
    case '':
        displayIntoGenetiqueUI($id);
        break;
    case 'dot':
        generateDotFile($id);
        break;
    default:
        generateMediaFile($id, $gen);
        break;
    }
} else {
    displayMessage($g4p_langue['dot_erreur_id']);
}

?>
