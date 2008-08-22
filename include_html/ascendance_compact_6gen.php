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
 *                 Matrice de l'arbre compact                              *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
?>

<div class="arbre" style="text-align:center">
<table class="arbre">
 <tr style="height:30px">
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MMMMMM']))?($g4p_resultat['MMMMMM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MMMMFM']))?($g4p_resultat['MMMMFM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MMFMMM']))?($g4p_resultat['MMFMMM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MMFMFM']))?($g4p_resultat['MMFMFM']):('&nbsp;')?></td>
 </tr>
 <tr style="height:15px">
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
 </tr>
 <tr style="height:15px">
  <td class="cellule_M" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MMMMM']))?($g4p_resultat['MMMMM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MMMM']))?($g4p_resultat['MMMM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MMMMF']))?($g4p_resultat['MMMMF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" rowspan="2">&nbsp;</td>
  <td width="63px" rowspan="2">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MMFMM']))?($g4p_resultat['MMFMM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MMFM']))?($g4p_resultat['MMFM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MMFMF']))?($g4p_resultat['MMFMF']):('&nbsp;')?></td>
 </tr>
 <tr style="height:15px">
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px">&nbsp;</td>
 <td width="17px">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 </tr>
 <tr style="height:15px">
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
 </tr>
 <tr style="height:30px">
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MMMMMF']))?($g4p_resultat['MMMMMF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MMMMFF']))?($g4p_resultat['MMMMFF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MMFMMF']))?($g4p_resultat['MMFMMF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MMFMFF']))?($g4p_resultat['MMFMFF']):('&nbsp;')?></td>
 </tr>
 <tr style="height:15px">
  <td colspan="2" width="126px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MMM']))?($g4p_resultat['MMM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td colspan="2" width="126px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MM']))?($g4p_resultat['MM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td colspan="2" width="126px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MMF']))?($g4p_resultat['MMF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td rowspan="2" colspan="2" width="126px">&nbsp;</td>
 </tr>
 <tr style="height:15px">
 <td colspan="2" width="126px">&nbsp;</td>
 <td width="17px">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td colspan="2" width="126px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td colspan="2" width="126px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 </tr>
 <!--2ème motif -->
 <tr style="height:30px">
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MMMFMM']))?($g4p_resultat['MMMFMM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MMMFFM']))?($g4p_resultat['MMMFFM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MMFFMM']))?($g4p_resultat['MMFFMM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MMFFFM']))?($g4p_resultat['MMFFFM']):('&nbsp;')?></td>
 </tr>
 <tr style="height:15px">
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
 </tr>
  <tr style="height:15px">
  <td class="cellule_M" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MMMFM']))?($g4p_resultat['MMMFM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MMMF']))?($g4p_resultat['MMMF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MMMFF']))?($g4p_resultat['MMMFF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" rowspan="2" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px" rowspan="2">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MMFFM']))?($g4p_resultat['MMFFM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MMFF']))?($g4p_resultat['MMFF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F"  rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MMFFF']))?($g4p_resultat['MMFFF']):('&nbsp;')?></td>
 </tr>
 <tr style="height:15px">
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px">&nbsp;</td>
 <td width="17px">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 </tr>
 <tr style="height:15px">
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
 </tr>
 <tr style="height:30px">
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MMMFMF']))?($g4p_resultat['MMMFMF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MMMFFF']))?($g4p_resultat['MMMFFF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MMFFMF']))?($g4p_resultat['MMFFMF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MMFFFF']))?($g4p_resultat['MMFFFF']):('&nbsp;')?></td>
 </tr>
<!-- 2eme grand motif --> 
<!-- ligne du milieu -->
 <tr style="height:15px">
  <td colspan="2" width="126px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td rowspan="2" colspan="2" width="126px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td colspan="2" width="126px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_I" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['M']))?($g4p_resultat['M']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td colspan="2" width="126px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td rowspan="2" colspan="2" width="126px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td rowspan="2" colspan="2" width="126px">&nbsp;</td>
 </tr>
 <tr style="height:15px">
 <td colspan="2" width="126px">&nbsp;</td>
 <td width="17px">&nbsp;</td>
 <td width="17px">&nbsp;</td>
 <td colspan="2">&nbsp;</td>
 <td width="17px">&nbsp;</td>
 <td width="17px">&nbsp;</td>
 <td colspan="2" width="126px">&nbsp;</td>
 <td width="17px">&nbsp;</td>
 </tr>
<!-- fin ligne milieu  -->
 <tr style="height:30px">
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MFMMMM']))?($g4p_resultat['MFMMMM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MFMMFM']))?($g4p_resultat['MFMMFM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MFFMMM']))?($g4p_resultat['MFFMMM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MFFMFM']))?($g4p_resultat['MFFMFM']):('&nbsp;')?></td>
 </tr>
 <tr style="height:15px">
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
 </tr>
 <tr style="height:15px">
  <td class="cellule_M" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MFMMM']))?($g4p_resultat['MFMMM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MFMM']))?($g4p_resultat['MFMM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MFMMF']))?($g4p_resultat['MFMMF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" rowspan="2" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px" rowspan="2">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MFFMM']))?($g4p_resultat['MFFMM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MFFM']))?($g4p_resultat['MFFM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F"  rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MFFMF']))?($g4p_resultat['MFFMF']):('&nbsp;')?></td>
 </tr>
 <tr style="height:15px">
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px">&nbsp;</td>
 <td width="17px">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 </tr>
 <tr style="height:15px">
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
 </tr>
 <tr style="height:30px">
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MFMMMF']))?($g4p_resultat['MFMMMF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MFMMFF']))?($g4p_resultat['MFMMFF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MFFMMF']))?($g4p_resultat['MFFMMF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MFFMFF']))?($g4p_resultat['MFFMFF']):('&nbsp;')?></td>
 </tr>
 <tr style="height:15px">
  <td colspan="2" width="126px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MFM']))?($g4p_resultat['MFM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td colspan="2" width="126px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MF']))?($g4p_resultat['MF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td colspan="2" width="126px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MFF']))?($g4p_resultat['MFF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td rowspan="2" colspan="2" width="126px">&nbsp;</td>
 </tr>
 <tr style="height:15px">
 <td colspan="2" width="126px">&nbsp;</td>
 <td width="17px">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td colspan="2" width="126px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td colspan="2" width="126px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 </tr>
 <!--2ème motif -->
 <tr style="height:30px">
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MFMFMM']))?($g4p_resultat['MFMFMM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MFMFFM']))?($g4p_resultat['MFMFFM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MFFFMM']))?($g4p_resultat['MFFFMM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" colspan="2" width="126px"><?=(isset($g4p_resultat['MFFFFM']))?($g4p_resultat['MFFFFM']):('&nbsp;')?></td>
 </tr>
 <tr style="height:15px">
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
 </tr>
  <tr style="height:15px">
  <td class="cellule_M" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MFMFM']))?($g4p_resultat['MFMFM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MFMF']))?($g4p_resultat['MFMF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MFMFF']))?($g4p_resultat['MFMFF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px" rowspan="2">&nbsp;</td>
  <td width="63px" rowspan="2">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_M" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MFFFM']))?($g4p_resultat['MFFFM']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MFFF']))?($g4p_resultat['MFFF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" rowspan="2" colspan="2" width="126px"><?=(isset($g4p_resultat['MFFFF']))?($g4p_resultat['MFFFF']):('&nbsp;')?></td>
 </tr>
 <tr style="height:15px">
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px">&nbsp;</td>
 <td width="17px">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 <td width="17px" style="border-top:solid 1px black;">&nbsp;</td>
 </tr>
 <tr style="height:15px">
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td width="63px" style="border-right:solid 1px black;">&nbsp;</td>
  <td width="63px">&nbsp;</td>
 </tr>
 <tr style="height:30px">
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MFMFMF']))?($g4p_resultat['MFMFMF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MFMFFF']))?($g4p_resultat['MFMFFF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MFFFMF']))?($g4p_resultat['MFFFMF']):('&nbsp;')?></td>
  <td width="17px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="63px">&nbsp;</td>
  <td width="17px">&nbsp;</td>
  <td class="cellule_F" colspan="2" width="126px"><?=(isset($g4p_resultat['MFFFFF']))?($g4p_resultat['MFFFFF']):('&nbsp;')?></td>
 </tr>
</table>
</div>
