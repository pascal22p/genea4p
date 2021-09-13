<?php
$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');

function checkdeps($name, $base, $membre)
{
    global $g4p_permissions;
    $result=array();
    if(isset($g4p_permissions[$name]['dependance']))
    {
        foreach($g4p_permissions[$name]['dependance'] as $key=>$a_perm)
            $result=array_merge($result, array($base.$membre.$key=>array('base'=>$base, 'membre'=>$membre, 'name'=>$key, 'value'=>$a_perm)));
            $dep=checkdeps($key, $base, $membre);
            if($dep)
                $result=array_merge($result, $dep);
        return $result;
    }
    else
        return false;
}

if(!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
    die('Droits insuffisants');
    
if(!empty($_POST['email_add']) and !empty($_POST['g4p_mdp']))
{
    $hash=password_hash($_POST['g4p_mdp'], PASSWORD_DEFAULT);
    
    $sql="INSERT INTO genea_membres (email, saltpass) VALUES 
        ('".$g4p_mysqli->escape_string($_POST['email_add'])."', '".
        $g4p_mysqli->escape_string($hash)."')";
    if($result=$g4p_mysqli->g4p_query($sql))
    {
        if($result!=='Error:1062')
            $message='<div class="success">'.sprintf($g4p_langue['message_email_ajout_succes'],$_POST['email_add']).'</div>';
        else
            $message='<div class="error">Impossible d\'ajouter l\'utilisateur : '.$_POST['email_add'].'. 
                Cet utilisateur existe déjà.</div>';
    }
    else
        $message='<div class="error">'.$g4p_langue['message_email_ajout_erreur'].'</div>';
}
elseif(!empty($_POST['del_membre_id']))
{
    $sql="DELETE FROM genea_membres WHERE id=".(int)$_POST['del_membre_id'];
    if($result=$g4p_mysqli->g4p_query($sql))
    {
        $message='<div class="success">Utilisateur supprimé avec succès</div>';
    }
    else
        $message='<div class="error">Erreur lors de la suppression de l\'utilisateur</div>';

}
elseif(!empty($_POST['membre_id_perm']))
{
    if(!empty($_POST['delete']))
    {
        $tmp=array();
        foreach($_POST['delete'] as $key=>$delete)
            $tmp[]=(int)$key;
        if(!empty($tmp))
        {
            $sql="DELETE FROM genea_permissions WHERE permission_id IN (".implode(',',$tmp).")";
            if($result=$g4p_mysqli->g4p_query($sql))
            {
                if($g4p_mysqli->affected_rows!=0)
                    $message='<div class="success">'.$g4p_mysqli->affected_rows.' Permission(s) supprimée(s) avec succès</div>';
                else
                    $message='<div class="error">Zéro permission supprimée, bug ?</div>';
            }
            else
                $message='<div class="error">Erreur lors de la suppression</div>';
        }
    }
    else
    {
        if(!empty($_POST['perm_id']))
        {
            $values=array();
            $success=0;
            foreach($_POST['perm_id'] as $perm)
            {
                $perm=(int)$perm;
                if(isset($_POST['valeur'][$perm]))
                {
                    $sql="UPDATE genea_permissions SET permission_value=".(int)$_POST['valeur'][$perm]." WHERE permission_id=".$perm;
                    if($g4p_mysqli->g4p_query($sql))
                        $success++;
                }
            }
            if($success)
                $message='<div class="success">'.$success.' permission(s) mise(s) à jour</div>';
            else
                $message='<div class="error">Erreur lors de la modification des permissions</div>';
        }
    }
}
elseif(!empty($_POST['new_perm_membre_id']))
{
    $cpt=count($_POST['new_perm_base']);
    $permissions=array();
    //verif des perms + recherche dépendances
    for($i=0; $i<$cpt; $i++)
    {
        if(isset($_POST['new_perm_base'][$i]) and 
            isset($_POST['new_perm_name'][$i]) and 
            isset($_POST['new_perm_value'][$i]))
        {
            //base, membre, name est utilisé comme clé. Permet d'éviter les doublons
            $permissions[(int)$_POST['new_perm_base'][$i].(int)$_POST['new_perm_membre_id'].(int)$_POST['new_perm_name'][$i]]=array(
                'base'=>(int)$_POST['new_perm_base'][$i], 'membre'=>(int)$_POST['new_perm_membre_id'], 
                'name'=>(int)$_POST['new_perm_name'][$i], 'value'=>(int)$_POST['new_perm_value'][$i]);
            $deps=checkdeps((int)$_POST['new_perm_name'][$i], (int)$_POST['new_perm_base'][$i], (int)$_POST['new_perm_membre_id']);            
        }
    }
    
    if($deps)
        $permissions=array_merge($permissions, $deps);

    //construction de la requète
    $values=array();
    foreach($permissions as $a_perm)
        $values[]='('.$a_perm['membre'].','.$a_perm['name'].','.$a_perm['value'].','.$a_perm['base'].')';
        
    if(!empty($values))
    {
        $sql="INSERT INTO genea_permissions (membre_id, permission_type, permission_value, base) VALUES ".
            implode(',',$values);
        if($result=$g4p_mysqli->g4p_query($sql))
        {
            if($g4p_mysqli->affected_rows>0)
                $message='<div class="success">'.$g4p_mysqli->affected_rows.' Permission(s) ajoutée(s) avec succès</div>';
            elseif($result=='Error:1062')
                $message='<div class="error">Impossible d\'ahouter cette permission, elle est déjà présente</div>';
            else
                $message='<div class="error">Zéro permission ajoutée, bug ?</div>';
        }
        else
            $message='<div class="error">Erreur lors de la requète sql</div>';
    }
}

//////////////////////////////////////////////////
// Formulaires
/////////////////////////////////////////////////

$g4p_javascript='<script src="javascript/jquery/jquery-3.6.0.min.js" type="text/javascript"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.bgiframe.min.js"></script>
  <script type="text/javascript" src="javascript/jquery/lib/jquery.dimensions.js"></script>
  <script type="text/javascript" src="javascript/jquery/jquery.autocomplete.min.js"></script>
  <script type="text/javascript">';
  
$sql="SELECT membre_id, permission_id, base, nom as nom_base, permission_type, permission_value FROM genea_permissions LEFT JOIN genea_infos ON base=genea_infos.id ORDER BY membre_id, base, permission_type";
$g4p_infos_req=$g4p_mysqli->g4p_query($sql);
if($g4p_result=$g4p_mysqli->g4p_result($g4p_infos_req,'membre_id', false))
{
    $g4p_javascript.="var listeperms = [];\n";
    foreach($g4p_result as $key=>$g4p_a_result)
    {
        $g4p_javascript.="listeperms[".$key."] = new Array(); \n";
        foreach($g4p_a_result as $key=>$a_perm)
        {
            $ouinon='<select name="valeur['.$a_perm['permission_id'].']" style="width:auto">';
            if($a_perm['permission_value']==1)
            {
                $ouinon.='<option value="1" selected="selected">Oui</option>';
                $ouinon.='<option value="0">Non</option>';
            }
            else
            {
                $ouinon.='<option value="1">Oui</option>';
                $ouinon.='<option value="0" selected="selected">Non</option>';
            }            
            $ouinon.='</select>';
            $g4p_javascript.="listeperms[".$a_perm['membre_id']."][".$key."] = new Array(); \n";
            $g4p_javascript.="listeperms[".$a_perm['membre_id']."][".$key."][0]='<input type=\"hidden\" name=\"perm_id[]\" value=\"".$a_perm['permission_id']."\" />".$a_perm['nom_base']."';\n";
            $g4p_javascript.="listeperms[".$a_perm['membre_id']."][".$key."][1]='".addslashes($g4p_langue[$a_perm['permission_type']])."';\n";
            $g4p_javascript.="listeperms[".$a_perm['membre_id']."][".$key."][2]='".addslashes($ouinon)."';\n";
            $g4p_javascript.="listeperms[".$a_perm['membre_id']."][".$key."][3]='<input type=\"checkbox\" name=\"delete[".$a_perm['permission_id']."]\">';\n";
        }
    }
}
  
$g4p_javascript.='$(function(){
      $("select#membre_id_perm").change(function(){
        $("table#permission_table").empty();
        $("table#permission_table").append("<thead><tr><td>Nom de la base</td><td>'.$g4p_langue['a_index_gerer_perm_type_perm'].'</td><td>'.$g4p_langue['a_index_gerer_perm_valeur'].'</td><td>Supprimer</td></tr></thead>");
        updateperms(listeperms[$(this).val()]);
      })
    })
    
    function updateperms(val) {
        var textToInsert = [];
        textToInsert[0] = \'<tbody>\';
        var i = 1;
        var length = val.length;
        for (var a = 0; a <length; a += 1) {
            textToInsert[i++]  = \'<tr>\';
            textToInsert[i++]  = \'<td>\'+val[a][0]+\'</td>\';
            textToInsert[i++]  = \'<td>\'+val[a][1]+\'</td>\';
            textToInsert[i++]  = \'<td>\'+val[a][2]+\'</td>\';
            textToInsert[i++]  = \'<td>\'+val[a][3]+\'</td>\';
            textToInsert[i++] = \'</tr>\' ;
        }
        textToInsert[i++] = \'</tbody>\';
        $("table#permission_table").append(textToInsert.join(""));
    }            
    
    function AddRow()
    {
        var clonedRow = $("table#new_perm tbody tr:last").clone();
        $("table#new_perm tbody").append(clonedRow);
        return true;
    }

    </script>
';

require_once($g4p_chemin.'entete.php');


if($_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
{
    echo '<div class="box_title"><h2>'.$g4p_langue['a_index_gerer_perm_titre'].'</h2></div>';

    if(!empty($message))
        echo $message;

    // NOUVEL UTILISATEUR
    echo '<div class="box">';
    echo '<div class="box_title"><h3>Ajout d\'un nouvel utilisateur</h3></div>';
    
    echo '<form class="formulaire" method="post" action="'.$_SERVER['PHP_SELF'].'" name="new_member">';
    echo '<dl class="horizontal">';
    echo '<dt style="width:7em;line-height:1.5em;vertical-align:text-bottom;">Adresse email</dt>
        <dd style="line-height:1.5em;vertical-align:text-bottom;"><input name="email_add" type="text" value="" size="40"  /></dd>';
    echo '<dt style="width:7em;line-height:1.5em;vertical-align:text-bottom;">Mot de passe</dt>
        <dd style="line-height:1.5em;vertical-align:text-bottom;"><input name="g4p_mdp" type="text" value="" size="40" /></dd></dl>';
    echo '<input type="submit" value="',$g4p_langue['submit_enregistrer'],'" /></form></div>';
    
    //SUPPRESSION UTILISATEUR
    echo '<div class="box">';
    echo '<div class="box_title"><h3>Suppression d\'un utilisateur</h3></div>';
    echo '<form class="formulaire" method="post" action="'.$_SERVER['PHP_SELF'].'" name="del_member">';
    echo '<select name="del_membre_id" style="width:auto">';
    $sql="SELECT id, email FROM genea_membres WHERE id<>1";
    $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
    $g4p_liste_membre=$g4p_mysqli->g4p_result($g4p_infos_req);
    foreach($g4p_liste_membre as $g4p_a_result)
        if($_SESSION['g4p_id_membre']!=$g4p_a_result['id'])
            echo '<option value="',$g4p_a_result['id'],'">',$g4p_a_result['email'],'</option>';
    echo '</select>';
    echo '<input type="submit" value="',$g4p_langue['Supprimer'],'" /></form></div>';
    
    //MODIFICATION PERMISSIONS UTILISATEUR
    echo '<div class="box">';
    echo '<div class="box_title"><h3>Modifier des permissions existantes</h3></div>';
    echo '<form class="formulaire" method="post" action="'.$_SERVER['PHP_SELF'].'" name="load_perm">';
    echo 'Editer les permissions de : <select name="membre_id_perm" id="membre_id_perm" style="width:auto"><option value="0" selected="selected"> </option>';
    // creation de la liste des membres
    $sql="SELECT id, email FROM genea_membres";
    $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
    $g4p_liste_membre=$g4p_mysqli->g4p_result($g4p_infos_req);
    foreach($g4p_liste_membre as $g4p_a_result)
        echo '<option value="',$g4p_a_result['id'],'">',$g4p_a_result['email'],'</option>';
    echo '</select>';

    echo '<table id="permission_table">';
    echo '</table>';
    echo '<input type="submit" value="Modifier les permissions" /></form></div>';

    //AJOUTER PERMISSIONS UTILISATEUR
    echo '<div class="box">';
    echo '<div class="box_title"><h3>Ajouter de nouvelles permissions</h3></div>';
    //liste des bases
    $sql="SELECT * from genea_infos";
    $g4p_infos_req=$g4p_mysqli->g4p_query($sql);
    $g4p_liste_base=$g4p_mysqli->g4p_result($g4p_infos_req); 
    echo '<form class="formulaire" method="post" action="'.$_SERVER['PHP_SELF'].'" name="new_perm">';
    echo 'Nouvelles permissions pour : <select name="new_perm_membre_id" id="new_perm_membre_id" style="width:auto"><option value="0" selected="selected"> </option>';
    foreach($g4p_liste_membre as $g4p_a_result)
        echo '<option value="',$g4p_a_result['id'],'">',$g4p_a_result['email'],'</option>';
    echo '</select>';
    echo '<table id="new_perm">';
    echo '<thead><tr><td>Nom de la base</td><td>'.$g4p_langue['a_index_gerer_perm_type_perm'].'</td><td>'.
    $g4p_langue['a_index_gerer_perm_valeur'].'</td></tr>';
    echo '</thead>';
    echo '<tbody><tr><td><select name="new_perm_base[]">';
    echo '<option value=""></option>';
    foreach($g4p_liste_base as $a_base)
        echo '<option value="'.$a_base['id'].'">'.$a_base['nom'].'</option>';
    echo '</select>';
    echo '</td><td><select name="new_perm_name[]">';
    echo '<option value=""></option>';
    foreach($g4p_permissions as $a_perm)
        echo '<option value="'.$a_perm['id'].'">'.$g4p_langue[$a_perm['id']].'</option>';
    echo '</select></td><td><select name="new_perm_value[]">';
    echo '<option value="vide"></option>';    
    echo '<option value="0">Non</option>';
    echo '<option value="1">Oui</option>';
    echo '</select></td></tr>';
    echo '</tbody><tfoot><tr><td colspan="4"><button type="button" onclick="JavaScript:AddRow()">Ajouter une ligne</button></td></tr></tfoot></table>';
    echo '<button type="submit" >Ajouter Les nouvelles permissions</button></form>';
    echo '</div>';

    echo '<div class="box">';
    echo '<div class="box_title"><h3>Dupliquer les permissions</h3></div>';
    echo 'A faire'; 
    echo '</div>';
}

require_once($g4p_chemin.'pied_de_page.php');

?>

