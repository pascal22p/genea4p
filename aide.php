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
 *                                  Page d'aide                            *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// $g4p_chemin='modules/genea4p/';

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');
require_once($g4p_chemin.'include_sys/sys_functions.php');
require_once($g4p_chemin.'entete.php');

?>
<h2>exemple de création d'un individu, de son conjoint et l'un de ces enfants</h2><br />

<div class="cadre">
D'abord, on clique sur creer un individu, on complète les rensigements demandés.<br />
<em>Pour le champ sexe, utilisez M ou F</em></div>

<div class="cadre">
Pour ajouter sa date de naissance ou tout autre évènements concernant sa vie, cliquer sur ajouter un évènement<br />
Compléter les champs nécessaires. Les champs sont facultatifs.<br />
<em>Utiliser le format JJ/MM/AAAA ou MM/AAAA ou AAAA, pour la date<br />
Vous pouver utiliser les modificateur suivant : ABT (environ), CAL (calculé), EST (estimé)<br />
exemple : ABT 1761, 15/11/1832, EST 05/1845</em>
</div>

<div class="cadre">
Ensuite vous tombez sur la fenêtre qui détaille l'évènement, vous pouvez ajouter une source (acte, extrait d'acte, souvenirs, livret de famille...) ou une note pour
préciser l'évènement.</div>

<div class="cadre">
Pour ajouter conjoint, on crée d'abord la personne avec la méthode précédente. Puis on clique sur créer une famille<br />
On sélectionne les conjoints puis on clique sur enregistrer.<br />
On tombe alors sur le détaille de la famille. On peut y ajouter des évènement familiaux (mariage), des enfants qu'il faut au préalable créer
par la méthode précédente. On peut aussi y ajouter une source ou une note.</div>

<div class="cadre">
Il faut toujours creer les personnes avant d'envisager de creer des liens<br />
On ne peut pas creer le père directement. Il faut creer l'individu, la famille puis lier l'enfant à cette famille</div>

<?php

require_once($g4p_chemin.'pied_de_page.php');
?>
