<?php
 /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                                                          *
 *  Copyright (C) 2017  PAROIS Pascal                                       *
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
 *        Update julian day from gedcom date                               *
 *                                                                         *
 * dernière mise à jour : 08/2017                                          *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='../';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'entete.php');

if(!$_SESSION['permission']->permission[_PERM_SUPER_ADMIN_])
{
    echo "Not enouig right to do this";
    require_once($g4p_chemin.'pied_de_page.php');
    exit;
}

$sql='SELECT events_details_id, events_details_gedcom_date FROM genea_events_details';
$g4p_infos_req=$g4p_mysqli->g4p_query($sql);
$g4p_liste_geddates=$g4p_mysqli->g4p_result($g4p_infos_req);

echo "<textarea>";
foreach($g4p_liste_geddates as $g4p_geddate)
{
    $g4p_date=new g4p_date();
    $g4p_date->set_gedcomdate($g4p_geddate['events_details_gedcom_date']);
    $g4p_date->g4p_gedom2date();
    var_dump($g4p_date);
    //$g4p_julians[]=array("id"=>, "jd_count"=>, "jd_precision"=>, "jd_calendar"=>);
}


require_once($g4p_chemin.'pied_de_page.php');

?>

