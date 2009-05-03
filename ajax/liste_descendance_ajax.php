<?php

$g4p_chemin='../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'p_conf/script_start.php');

$tmp_indi=g4p_load_indi_infos((int)$_REQUEST['root']);


if(!empty($tmp_indi->familles))
{
$leaf=array();
echo '[';
    foreach($tmp_indi->familles as $a_famille)
    {
        if(!empty($a_famille->enfants))
        {
            foreach($a_famille->enfants as $a_enfant)
            {
                $tmp_indi2=g4p_load_indi_infos($a_enfant['indi']->indi_id);
                $exist_desc=false;
                if(!empty($tmp_indi2->familles))
                {
                    foreach($tmp_indi2->familles as $a_famille2)
                    {
                        if(!empty($a_famille2->enfants))
                        {
                            $exist_desc=true;
                            break;
                        }
                    }
                }
                if($exist_desc)
                {
                    $tmp='<img src="images/'.$tmp_indi2->sexe.'.png" alt="'.
                        $tmp_indi2->sexe.'" class="icone_sexe" /> '.g4p_link_nom($a_enfant['indi']);
                            
                    if(isset($a_famille->husb->indi_id)  and $a_famille->husb->indi_id!=$tmp_indi2->indi_id)
                        $tmp.=' <img src="images/mariage.png" alt="mariage" class="icone_mar" /> '.g4p_link_nom($a_famille->husb);
                    elseif(isset($a_famille->wife->indi_id)  and $a_famille->wife->indi_id!=$tmp_indi2->indi_id)
                        $tmp.=' <img src="images/mariage.png" alt="mariage" class="icone_mar" /> '.g4p_link_nom($a_famille->wife);
                    $leaf[]='{'.
                    "'text':'".addslashes($tmp)."',".
                    "'id': '".$a_enfant['indi']->indi_id."',".
                    "'hasChildren': true".
                    "}";
                }
                else
                {
                    $tmp='<img src="images/'.$tmp_indi2->sexe.'.png" alt="'.
                        $tmp_indi2->sexe.'" class="icone_sexe" /> '.g4p_link_nom($a_enfant['indi']);
                    $leaf[]='{'.
                        "'text':'".addslashes($tmp)."'".
                        "}";
                }
            }
        }
    }
    echo implode(',',$leaf);
    echo ']';
}


?>