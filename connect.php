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
 *                        Page de connection                               *
 *                                                                         *
 * dernière mise à jour : 11/07/2004                                       *
 * En cas de problème : http://www.parois.net                              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$g4p_chemin='';
// en module npds, ce fichier est inutile

require_once($g4p_chemin.'p_conf/g4p_config.php');
require_once($g4p_chemin.'p_conf/script_start.php');

if(!isset($_POST['g4p_email']))
{
  require_once($g4p_chemin.'entete.php');
  echo '<h2>',$g4p_langue['connect_titre'],'</h2><div class="cadre">';
  echo '<form class="formulaire" method="post" action="',g4p_make_url('','connect.php','',0),'">';
  echo '<label for="g4p_email">',$g4p_langue['connect_mail'],'</label><input type="text" id="g4p_email" name="g4p_email" /><br />';
  echo '<label
  for="g4p_password">',$g4p_langue['connect_pass'],'</label><input
  type="password" id="g4p_password" name="g4p_password"/><br />';
  echo '<label
  for="g4p_cookie">',$g4p_langue['connect_cookie'],'</label><input
  type="checkbox" id="g4p_cookie" name="g4p_cookie" /><br />';
  echo '<input type="submit" value="',$g4p_langue['submit_connection'],'" /></form></div>';
  require_once($g4p_chemin.'pied_de_page.php');
}
else
{
    $sql="SELECT email,id,langue,theme,place FROM genea_membres WHERE email='".$g4p_mysqli->escape_string($_POST['g4p_email'])."' AND pass='".md5($_POST['g4p_password'])."'";
    if($g4p_result=$g4p_mysqli->g4p_query($sql))
    {
        if($g4p_result=$g4p_mysqli->g4p_result($g4p_result))
        {
            $g4p_result=$g4p_result[0];
            $_SESSION['g4p_id_membre']=$g4p_result['id'];
            $_SESSION['langue']=$g4p_result['langue'];
            $_SESSION['g4p_email_membre']=$g4p_result['email'];
            $_SESSION['theme']=$g4p_result['theme'];
            /*
            $place=explode(',',$g4p_result['place']);
            for($i=0;$i<count($g4p_config['subdivision'])&&$place[$i]!=-1;$i++)
            {
                $_SESSION['place'][$i]=$place[$i];
            }
            */

            if(isset($_POST['g4p_cookie']))
            {
                setcookie('genea4p[g4p_id_membre]',$g4p_result['id'],time()+$g4p_config['cookie_time_limit'],'/');
                setcookie('genea4p[g4p_membre]',md5($g4p_result['email']),time()+$g4p_config['cookie_time_limit'],'/');
                setcookie('genea4p[langue]',$g4p_result['langue'],time()+$g4p_config['cookie_time_limit'],'/');
                setcookie('genea4p[theme]',$g4p_result['theme'],time()+$g4p_config['cookie_time_limit'],'/');
                /*
                for($i=0;$i<count($g4p_config['subdivision'])&&isset($_SESSION['place_'.$i]);$i++)
                {
                    setcookie('genea4p[place]['.$i.']',$place[$i],time()+$g4p_config['cookie_time_limit'],'/');
                }
                */
            }
            else
            {
                setcookie('genea4p[langue]','',0,'/','',0);
                setcookie('genea4p[g4p_membre]','',0,'/','',0);
                setcookie('genea4p[g4p_id_membre]','',0,'/','',0);
                setcookie('genea4p[theme]','',0,'/','',0);
                //setcookie('genea4p[place]','',0,'/','',0);
                setcookie('genea4p');
                unset($_COOKIE['genea4p']);
            }
            unset($_SESSION['permission']);
            $_SESSION['message']=$g4p_langue['connect_bienvenue']." : ".$_POST['g4p_email']."/".$_SESSION['langue'];
            header('location:'.g4p_make_url('','index.php',SID,0));
        }
        else
        {
            $_SESSION['message']=$g4p_langue['acces_login_pass_incorrect']." : ".$_POST['g4p_email'];
            header('location:'.g4p_make_url('','connect.php',SID,0));
        }
    }
}
?>
