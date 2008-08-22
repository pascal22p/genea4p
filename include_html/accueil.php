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
 *                        Page d'accueil                                   *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


echo '<div class="cadre">';
echo '<p>'.$g4p_langue['accueil_renseignements'].'</p>
      <h2>'.$g4p_langue['accueil_titre'].'</h2>';


$g4p_query=$g4p_mysqli->g4p_query("SELECT id, nom, descriptif FROM genea_infos ORDER BY nom");
if($g4p_result=$g4p_mysqli->g4p_result($g4p_query))
{
  foreach($g4p_result as $g4p_a_result)
  {
    echo '<a href="'.g4p_make_url('','index.php','genea_db_id='.$g4p_a_result['id'].'&g4p_action=liste_patronyme','patronymes-'.$g4p_a_result['id']).'">'.$g4p_a_result['nom'].'</a><br />';
    echo '<i>'.$g4p_a_result['descriptif'].'</i><br /><br />';
  }
}

echo '</div>';

?>
