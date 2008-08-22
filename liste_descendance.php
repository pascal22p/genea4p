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
 *                         Liste de descendance                            *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/*limites imposées pour éviter d'exploser le serveur:
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
			url: "liste_ascendance_ajax.php",
			// add some additional, dynamic data and request with POST
			ajax: {
				type: "post"
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

function recursive_descendance($g4p_id, $generation=1, $couleur=0)
{
    global $g4p_couleur, $g4p_chemin, $g4p_limite_pers, $cache_count, $g4p_langue;
    if($_SESSION['permission']->permission[_PERM_MASK_INDI_] and $g4p_indi->resn=='privacy')
        return;

    $g4p_limite_pers++;

    if($couleur>=count($g4p_couleur))
        $couleur=0;
    $g4p_indi_info=g4p_load_indi_infos($g4p_id);

    if(isset($g4p_indi_info->familles))
    {
        foreach($g4p_indi_info->familles as $g4p_une_famille)
        {
		    echo '<li class="open"><span>'.g4p_link_nom($g4p_indi_info->indi_id);
            if(isset($g4p_une_famille->husb->indi_id)  and $g4p_une_famille->husb->indi_id!=$g4p_indi_info->indi_id)
                echo ' & '.g4p_link_nom($g4p_une_famille->husb).'</span>';
            elseif(isset($g4p_une_famille->wife->indi_id)  and $g4p_une_famille->wife->indi_id!=$g4p_indi_info->indi_id)
                echo ' & '.g4p_link_nom($g4p_une_famille->wife).'</span>';
	    else
		echo '</span>';
	    

            echo '<ul>';
            if(isset($g4p_une_famille->enfants))
            {
                foreach($g4p_une_famille->enfants as $g4p_un_enfant)
                {
                    if($generation>$_GET['g4p_generation'] or $generation>_NBRE_MAX_GENERATION_)
                    {
                        echo '<li id="'.$g4p_un_enfant['indi']->indi_id.'" class="hasChildren">';
			echo '<span>'.g4p_link_nom($g4p_un_enfant['indi']).'</span>
				<ul>
					<li><span class="placeholder">&nbsp;</span></li>
				</ul>';
                        break;
                    }
                    recursive_descendance($g4p_un_enfant['indi']->indi_id, $generation+1, $couleur+1);
                }
            }
            echo '</ul>';
        }
    }
    else
	echo '<li><span>'.g4p_link_nom($g4p_indi_info).'</span>';
    echo '</li>';
}

if(!isset($_GET['g4p_generation']))
    $_GET['g4p_generation']=5;
$lien='';

echo '<div class="box_title"><h2>',sprintf($g4p_langue['liste_desc_titre'],$g4p_indi->nom,$g4p_indi->prenom),'</h2></div>'."\n";

echo '<span class="noprint">',$g4p_langue['liste_desc_nb_gen'],'<br />';
for($i=1;$i<=_NBRE_MAX_GENERATION_;$i+=2)
    $lien.='<a href="'.g4p_make_url('','liste_descendance.php','id_pers='.$g4p_indi->indi_id.'&g4p_generation='.$i,'').'">'.$i.'</a> - ';
echo substr($lien,0,-2);
echo '</span>';

echo '<div id="treecontrol">
		<a title="Collapse the entire tree below" href="#"><img src="treeview/images/minus.gif" /> Collapse All</a>
		<a title="Expand the entire tree below" href="#"><img src="treeview/images/plus.gif" /> Expand All</a>
		<a title="Toggle the tree below, opening closed branches, closing open branches" href="#">Toggle All</a>
	</div>';


echo '<ul id="mixed">';
$g4p_limite_pers=0;
recursive_descendance($g4p_indi->indi_id);
echo '</ul>';


include($g4p_chemin.'pied_de_page.php');
?>
