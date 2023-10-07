<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                                                          *
 *  Copyright (C) 2004-2005  PAROIS Pascal                                  *
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
 *              FIchier de configuration de genea4p                        *
 *                                                                         *
 * dernière mise à jour : 29/06/2006                                       *
 * En cas de probl�me : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

//PERMISSIONS
require_once($g4p_chemin.'p_conf/g4p_permissions.php');
//CALENDAR
require_once($g4p_chemin.'p_conf/g4p_calendar.php');
//GLOBAL CONFIG
require_once($g4p_chemin.'p_conf/g4p_config_global.php');
//LOCAL CONFIG (écrase les données de config_global)
require_once($g4p_chemin.'p_conf/g4p_config_local.php');
//Data
require_once($g4p_chemin.'p_conf/g4p_data.php');

?>
