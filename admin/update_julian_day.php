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
    echo "Not enough right to do this";
    require_once($g4p_chemin.'pied_de_page.php');
    exit;
}

$sql='SELECT events_details_id, events_details_gedcom_date FROM genea_events_details';
$g4p_infos_req=$g4p_mysqli->g4p_query($sql);
$g4p_liste_geddates=$g4p_mysqli->g4p_result($g4p_infos_req);

foreach($g4p_liste_geddates as $g4p_geddate)
{
    $g4p_date=new g4p_date(Null);
    $g4p_date->set_gedcomdate($g4p_geddate['events_details_gedcom_date']);
    $g4p_date->g4p_gedom2date();
    if(!empty($g4p_date->date1->calendar) and !empty($g4p_date->date1->jd_count) and !empty($g4p_date->date1->jd_precision))
        $g4p_julians[]=array("id"=>$g4p_geddate['events_details_id'], "jd_count"=>$g4p_date->date1->jd_count, "jd_precision"=>$g4p_date->date1->jd_precision, "jd_calendar"=>$g4p_date->date1->calendar);
}

$sql="CREATE TEMPORARY TABLE temp_table AS 
          SELECT events_details_id, jd_count, jd_precision, jd_calendar
          FROM genea_events_details
          WHERE 1=0";
$g4p_mysqli->g4p_query($sql);

$sql=[];
foreach($g4p_julians as	$value)
{
    $sql[]='('.$value['id'].','.$value['jd_count'].','.$value['jd_precision'].',"'.$value['jd_calendar'].'")';
}
$result=$g4p_mysqli->g4p_query("INSERT INTO temp_table VALUES ".implode($sql, ','));

$sql="UPDATE genea_events_details, temp_table SET genea_events_details.jd_count=temp_table.jd_count,
      genea_events_details.jd_precision=temp_table.jd_precision,
      genea_events_details.jd_calendar=temp_table.jd_calendar
      WHERE genea_events_details.events_details_id=temp_table.events_details_id";
$g4p_mysqli->g4p_query($sql);

echo '<H2>Update done</h2>';

require_once($g4p_chemin.'pied_de_page.php');

?>

