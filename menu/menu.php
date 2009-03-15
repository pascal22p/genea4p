<?php
echo '<div id="navigation">';

echo '
<ul class="menu">
    <li class="menu">',$g4p_langue['menu_sommaire'],'
    <ul class="ssmenu">
        <li class="ssmenu"><a href="',g4p_make_url('','index.php','base',0),'">',$g4p_langue['menu_liste_base'],'</a></li>
        <li class="ssmenu"><a href="',g4p_make_url('','liste_patronymes.php','test='.urlencode('a&b?c'),0),'">',$g4p_langue['menu_patronyme'],'</a></li>';
      		
if(!isset($_SESSION['g4p_id_membre']) or $_SESSION['g4p_id_membre']==1)
    echo '
        <li class="ssmenu"><a href="',g4p_make_url('','connect.php','',0),'">',$g4p_langue['menu_connexion'],'</a></li>';
else
    echo '  
        <li class="ssmenu"><a href="',g4p_make_url('','disconnect.php','',0),'">',$g4p_langue['menu_deconnexion'],'</a></li>';
	//echo '  <li class="ssmenu"><a href="',g4p_make_url('','tags.php','',0),'">',$g4p_langue['menu_tags'],'</a></li>';

echo '      
        <li class="ssmenu"><a href="',g4p_make_url('','statistiques.php','',0),'">',$g4p_langue['menu_stats'],'</a></li>
        <li class="ssmenu"><a href="',g4p_make_url('','recherche.php','',0),'">',$g4p_langue['menu_rechercher'],'</a></li>';

echo '
        <li class="ssmenu"><a href="',g4p_make_url('','profil.php','',0),'">',$g4p_langue['menu_profil'],'</a></li>';
     
echo '  
    </ul>
    </li>';

if ($_SESSION['permission']->permission[_PERM_EDIT_FILES_])
{
    echo '
    <li class="menu">',$g4p_langue['menu_creer'],'
    <ul class="ssmenu">
        <li class="ssmenu"><a href="',g4p_make_url('','new_indi.php','',0),'">',$g4p_langue['menu_creer_indi'],'</a></li>
        <li class="ssmenu"><a href="',g4p_make_url('','new_family.php','',0),'">',$g4p_langue['menu_creer_famille'],'</a></li>
        <li class="ssmenu"><a href="',g4p_make_url('','new_repo.php','',0),'">',$g4p_langue['menu_creer_depot'],'</a></li>
        <li class="ssmenu"><a href="',g4p_make_url('admin','creer.php','g4p_opt=ajout_place',0),'">',$g4p_langue['menu_creer_lieu'],'</a></li>
    </ul>
    </li>';
}

echo '  
    <li class="menu">',$g4p_langue['menu_voir'],'
    <ul class="ssmenu">';
		
if (isset($g4p_indi->indi_id))
{
  echo '  
        <li class="ssmenu"><a href="',g4p_make_url('','arbre_ascendant.php','genea_db_id='.$_SESSION['genea_db_id'].'&amp;g4p_id='.$g4p_indi->indi_id,'arbre_ascendant-'.$_SESSION['genea_db_id'].'-'.g4p_prepare_varurl($g4p_indi->nom).'-'.g4p_prepare_varurl($g4p_indi->prenom).'-'.$g4p_indi->indi_id),'">',$g4p_langue['menu_voir_arbre_asc'],'</a></li>
        <li class="ssmenu"><a href="',g4p_make_url('','liste_descendance.php','id_pers='.$g4p_indi->indi_id,'liste_descendance-'.$_SESSION['genea_db_id'].'-'.g4p_prepare_varurl($g4p_indi->nom).'-'.g4p_prepare_varurl($g4p_indi->prenom).'-'.$g4p_indi->indi_id),'">',$g4p_langue['menu_voir_liste_desc'],'</a></li>
        <li class="ssmenu"><a href="',g4p_make_url('','liste_ascendance.php','id_pers='.$g4p_indi->indi_id,'liste_ascendance-'.$_SESSION['genea_db_id'].'-'.g4p_prepare_varurl($g4p_indi->nom).'-'.g4p_prepare_varurl($g4p_indi->prenom).'-'.$g4p_indi->indi_id),'">',$g4p_langue['menu_voir_liste_asc'],'</a></li>
        <li class="ssmenu"><a href="',g4p_make_url('','arbre.php','id_pers='.$g4p_indi->indi_id,0),'">',$g4p_langue['menu_famille_proche'],'</a></li>
        <li class="ssmenu"><a href="',g4p_make_url('','index.php','g4p_action=fiche_indi',0),'">',$g4p_langue['menu_fiche'],'</a></li>';
}

echo '    
        <li class="ssmenu"><a href="',g4p_make_url('modules','cartographie.php','',0),'">',$g4p_langue['menu_voir_cartographie'],'</a></li>';
echo '  
    </ul>
    </li>';
  
if (isset($_SESSION['historic']))
{
	echo '
		<li class="menu">',$g4p_langue['menu_historic'],'
		<ul class="ssmenu">';
    
	if(isset($_SESSION['historic']['indi']))
	{
		foreach($_SESSION['historic']['indi'] as $value)
		{		
			echo '<li class="ssmenu"><a href="',
				g4p_make_url('','fiche_individuelle.php','id_pers='.$value['id']),
				'">',$value['text'],'</a></li>';
		}
	}
	echo '
		</ul>
		</li>';
}
  
echo '   
    <li class="menu">',$g4p_langue['menu_telecharger'],'
    <ul class="ssmenu">
        <li class="ssmenu"><a href="',g4p_make_url('','download.php','',0),'">',$g4p_langue['menu_telecharger_rapports'],'</a></li>';

if (isset($g4p_indi->indi_id) and $_SESSION['permission']->permission[_PERM_PDF_])
    echo '   
        <li class="ssmenu"><a href="',g4p_make_url('tcpdf','fiche_individuelle.php','g4p_id='.$g4p_indi->indi_id,0),'">',$g4p_langue['menu_telecharger_pdfi'],'</a></li>';
if (isset($g4p_indi->indi_id) and $_SESSION['permission']->permission[_PERM_PDF_])
    echo '   
        <li class="ssmenu"><a href="',g4p_make_url('','arbre_ascendant_pdf.php','g4p_id='.$g4p_indi->indi_id,0),'">',$g4p_langue['menu_telecharger_arbre_asc_pdf'],'</a></li>';
/*if ($_SESSION['permission']->permission[_PERM_DOT_])
  echo '<li class="ssmenu"><a href="',g4p_make_url('','export_dot.php','',0),'">',$g4p_langue['menu_telecharger_dot'],'</a></li>';*/

echo '  
    </ul>
    </li>';

//echo '  <li class="menu"><a href="',g4p_make_url('','index.php','g4p_action=liste_patronyme&amp;patronyme=all',0),'">',$g4p_langue['menu_patronyme'],'</a></li>';

if($_SESSION['permission']->permission[_PERM_ADMIN_] or $_SESSION['permission']->permission[_PERM_EDIT_FILES_] or $_SESSION['permission']->permission[_PERM_SUPPR_FILES_] or $_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
{
    echo '
    <li class="menu">',$g4p_langue['menu_admin'],'<ul class="ssmenu">';
    echo '
    <li class="ssmenu"><a href="',g4p_make_url('admin','panel.php','',0),'">',$g4p_langue['menu_admin_panel'],'</a></li>';
}
if ($_SESSION['permission']->permission[_PERM_ADMIN_])
{
    echo '
    <li class="ssmenu"><a href="',g4p_make_url('import_gedcom','import_gedcom.php','',0),'">',$g4p_langue['menu_admin_import_ged'],'</a></li>
    <li class="ssmenu"><a href="',g4p_make_url('admin','exec.php','g4p_opt=agregats',0),'">',$g4p_langue['menu_admin_index'],'</a></li>
    <li class="ssmenu"><a href="',g4p_make_url('admin','index.php','g4p_opt=gerer_download',0),'">',$g4p_langue['menu_admin_gere_telechargement'],'</a></li>
    <li class="ssmenu"><a href="',g4p_make_url('admin','index.php','g4p_opt=vide_base',0),'">',$g4p_langue['menu_admin_vide_base'],'</a></li>';
  
    if(isset($_SESSION['genea_db_id']))
        echo '
    <li class="ssmenu"><a href="'.g4p_make_url('admin','exec.php','g4p_opt=vide_cache&amp;g4p_id='.$_SESSION['genea_db_nom'],0).'" onclick=" return confirme(this, \''.$g4p_langue['menu_sppr_confirme'].'\')">'.$g4p_langue['menu_admin_vide_cache'].'</a></li>';
}
if ($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
{
    echo '
    <li class="ssmenu"><hr /></li><li class="ssmenu"><a href="',g4p_make_url('admin','index.php','g4p_opt=new_base',0),'">',$g4p_langue['menu_admin_ajout_base'],'</a></li>
    <li class="ssmenu"><a href="',g4p_make_url('admin','index.php','g4p_opt=gerer_permissions',0),'">',$g4p_langue['menu_admin_gere_perm'],'</a></li>
    <li class="ssmenu"><a href="',g4p_make_url('admin','index.php','g4p_opt=suppr_base',0),'">',$g4p_langue['menu_admin_suppr_base'],'</a></li>
    ';
}            
if ($_SESSION['permission']->permission[_PERM_ADMIN_] or $_SESSION['permission']->permission[_PERM_EDIT_FILES_] or $_SESSION['permission']->permission[_PERM_SUPPR_FILES_] or $_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
{
    echo '
    </ul>
    </li>';
}

if($g4p_config['g4p_type_install']=='seule' or $g4p_config['g4p_type_install']=='seule-mod_rewrite')
{
    echo '
    <li class="menu" style="text-align:center">';
    foreach($g4p_config['langues'] as $cle=>$valeur)
        echo '<a
            href="',g4p_make_url('','profil.php','langue='.$cle,0),'"><img
            src="',$g4p_chemin,'languages/',$cle,'.png" alt="'.$valeur.'" /></a>';
    echo '
    </li>';
}

echo '
    <li><img src="'.$g4p_chemin.'styles/'.$_SESSION['theme'].'/images/logo.png" alt="genea4p" /></li>';
echo '
</ul>';

echo '</div>';

echo '<!-- Fin menu-->';
?>
