<?php

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'p_conf/script_start.php');

if (empty($_REQUEST['root']) or $_REQUEST['root'] == "source")
{

    $g4p_indi=g4p_load_indi_infos(74);

}
else
{
        $tmp_indi=g4p_load_indi_infos($_REQUEST['root']);
}

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
                        //var_dump($a_enfant);
                        //verf si descendance existe
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
                            $leaf[]='{'.
				"'text':'".addslashes(g4p_link_nom($a_enfant['indi']))."',".
				"'id': '".$a_enfant['indi']->indi_id."',".
				"'hasChildren': true".
				"}";
                        }
                        else
                        {
                            $leaf[]='{'.
				"'text':'".addslashes(g4p_link_nom($a_enfant['indi']))."'".
				"}";
                        }
                    }
                }
            }
		echo implode(',',$leaf);
		echo ']';
		
        }



/*
echo '[';
foreach($_POST as $key=>$val)
{
	if(!is_array($val))
	{
		echo '{';
		echo "'text':'".$key.' - '.$val."'";
		echo '},';
	}
	else
	{
		echo '{';
		echo "'text':'Array'";
		echo '},';
	}
	

}
echo ']';


/*

[
	{
		"text": "1. Review of existing structures",
		"expanded": true,
		"children":
		[
			{
				"text": "<?php addslashes(var_dump($_POST)); ?>"
			},
		 	{
				"text": "1.2 metaplugins"
			}
		]
	},
	{
		"text": "2. Wrapper plugins"
	},
	{
		"text": "3. Summary"
	},
	{
		"text": "4. Questions and answers"
	}
	
]
*/


?>