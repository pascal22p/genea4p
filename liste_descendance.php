<?php
/*limites imposées pour éviter d'exploser le serveur, cf p_conf/g4p_config:
_NBRE_MAX_PERS_DESCENDANCE_ générations au maximum sont affichées ($generation)
_NBRE_MAX_PERS_DESCENDANCE_ personnes sont affichées au maximum
*/

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

$g4p_javascript='
	<link rel="stylesheet" href="treeview/jquery.treeview.css" />
       <link rel="stylesheet" href="treeview/red-treeview.css" />
	
	<script src="treeview/lib/jquery.js" type="text/javascript"></script>
	<script src="treeview/lib/jquery.cookie.js" type="text/javascript"></script>
	<script src="treeview/jquery.treeview.js" type="text/javascript"></script>
	<script src="treeview/jquery.treeview.edit.js" type="text/javascript"></script>
	<script src="treeview/jquery.treeview.async.js" type="text/javascript"></script>
	
	<script type="text/javascript">
	function initTrees() {
		
		$("#mixed").treeview({
			control: "#treecontrol",
			url: "ajax/liste_descendance_ajax.php",
			// add some additional, dynamic data and request with POST
			ajax: {
				type: "get"
			}
		});
	}
	$(document).ready(function(){
		initTrees();
		$("#refresh").click(function() {
			$("#mixed").empty();
			initTrees();
		});
	});
	</script>
';

include($g4p_chemin.'p_conf/g4p_config.php');
include($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

//Chargement des données de la personne
$g4p_indi=g4p_load_indi_infos((int)$_REQUEST['id_pers']);

$g4p_titre_page='Liste de descendance : '.$g4p_indi->nom.' '.$g4p_indi->prenom;
include($g4p_chemin.'entete.php');

/*echo '
<ul id="mixed">
		<li id="74" class="hasChildren">
			<span>Racine</span>
			<ul>
				<li><span class="placeholder">&nbsp;</span></li>
			</ul>
		</li>
	</ul>
';*/

function recursive_descendance($g4p_id, $generation=1)
{
    global $g4p_couleur, $g4p_chemin, $g4p_limite_pers, $cache_count, $g4p_langue;

    $g4p_limite_pers++;

    $g4p_indi_info=g4p_load_indi_infos($g4p_id);

    if(!$_SESSION['permission']->permission[_PERM_MASK_INDI_] and $g4p_indi_info->resn=='privacy')
        return;

    if(isset($g4p_indi_info->familles))
    {
        foreach($g4p_indi_info->familles as $g4p_une_famille)
        {
            //else
               // echo '</span>';
	    

            //echo '<ul>';
            if(isset($g4p_une_famille->enfants))
            {
                if($generation<$_GET['g4p_generation'] and $generation<_NBRE_MAX_GENERATION_)
                {
                    echo '<li class="open" class="liste_descendance"><img src="'.$g4p_chemin.'images/'.$g4p_indi_info->sexe.'.png" alt="'.
                        $g4p_indi_info->sexe.'" class="icone_sexe" /> '.g4p_link_nom($g4p_indi_info->indi_id);
                    if(isset($g4p_une_famille->husb->indi_id)  and $g4p_une_famille->husb->indi_id!=$g4p_indi_info->indi_id)
                        echo ' <img src="'.$g4p_chemin.'images/mariage.png" alt="mariage" class="icone_mar" /> '.g4p_link_nom($g4p_une_famille->husb);
                    elseif(isset($g4p_une_famille->wife->indi_id)  and $g4p_une_famille->wife->indi_id!=$g4p_indi_info->indi_id)
                        echo ' <img src="'.$g4p_chemin.'images/mariage.png" alt="mariage" class="icone_mar" /> '.g4p_link_nom($g4p_une_famille->wife);
                    echo '<ul>';
                    foreach($g4p_une_famille->enfants as $a_enfant)
                        recursive_descendance($a_enfant['indi']->indi_id, $generation+1);
                    echo '</ul>';
                }
                else
                {
                    echo '<li id="'.$g4p_indi_info->indi_id.'" class="hasChildren" class="liste_descendance"><img src="'.$g4p_chemin.'images/'.$g4p_indi_info->sexe.'.png" alt="'.
                    $g4p_indi_info->sexe.'" class="icone_sexe" /> '.g4p_link_nom($g4p_indi_info->indi_id);
                    if(isset($g4p_une_famille->husb->indi_id)  and $g4p_une_famille->husb->indi_id!=$g4p_indi_info->indi_id)
                        echo ' <img src="'.$g4p_chemin.'images/mariage.png" alt="mariage" class="icone_mar" /> '.g4p_link_nom($g4p_une_famille->husb);
                    elseif(isset($g4p_une_famille->wife->indi_id)  and $g4p_une_famille->wife->indi_id!=$g4p_indi_info->indi_id)
                        echo ' <img src="'.$g4p_chemin.'images/mariage.png" alt="mariage" class="icone_mar" /> '.g4p_link_nom($g4p_une_famille->wife);
                    echo '<ul>
                        <li><span class="placeholder">&nbsp;</span></li>
                        </ul>';
                }
            }
            else
            {
                echo '<li class="liste_descendance"><img src="'.$g4p_chemin.'images/'.$g4p_indi_info->sexe.'.png" alt="'.
                $g4p_indi_info->sexe.'" class="icone_sexe" /> '.g4p_link_nom($g4p_indi_info->indi_id);
                if(isset($g4p_une_famille->husb->indi_id)  and $g4p_une_famille->husb->indi_id!=$g4p_indi_info->indi_id)
                    echo ' <img src="'.$g4p_chemin.'images/mariage.png" alt="mariage" class="icone_mar" /> '.g4p_link_nom($g4p_une_famille->husb);
                elseif(isset($g4p_une_famille->wife->indi_id)  and $g4p_une_famille->wife->indi_id!=$g4p_indi_info->indi_id)
                    echo ' <img src="'.$g4p_chemin.'images/mariage.png" alt="mariage" class="icone_mar" /> '.g4p_link_nom($g4p_une_famille->wife);            
            }
            //echo '</ul>';
        }
    }
    else
        echo '<li><img src="'.$g4p_chemin.'images/'.$g4p_indi_info->sexe.'.png" alt="'.
            $g4p_indi_info->sexe.'" class="icone_sexe" /> '.g4p_link_nom($g4p_indi_info);
    echo '</li>';
}

if(!isset($_GET['g4p_generation']))
    $_GET['g4p_generation']=5;
$lien='';

echo '<div class="box_title"><h2>',sprintf($g4p_langue['liste_desc_titre'],$g4p_indi->nom,$g4p_indi->prenom),'</h2></div>'."\n";

echo '<ul><li>',$g4p_langue['liste_desc_nb_gen'],'</li><li>';
for($i=1;$i<=_NBRE_MAX_GENERATION_;$i+=2)
    $lien.='<a href="'.g4p_make_url('','liste_descendance.php','id_pers='.$g4p_indi->indi_id.'&g4p_generation='.$i,'').'">'.$i.'</a> - ';
echo substr($lien,0,-2);
echo '</li>';
echo '<li id="treecontrol">
		<a title="Collapse the entire tree below" href="#"><img style="border:none;" src="treeview/images/minus.gif" /> Tout réduire </a>
		<a title="Expand the entire tree below" href="#"><img style="border:none;" src="treeview/images/plus.gif" /> Tout développer </a>
		<a title="Toggle the tree below, opening closed branches, closing open branches" href="#"> Inverser </a></li>
	</li></ul>';


echo '<ul id="mixed" class="liste_descendance">';
$g4p_limite_pers=0;
recursive_descendance($g4p_indi->indi_id);
echo '</ul>';


include($g4p_chemin.'pied_de_page.php');
?>
