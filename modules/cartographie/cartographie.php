<?
//la projection utilisé la projection de type lambert conique conforme sécante

class carte
{
  function carte($file)
  {
    $this->file=$file;
    $this->limite_adm=0;
    $this->marges=5;

    $this->backgroundcolor_r=154;
    $this->backgroundcolor_v=211;
    $this->backgroundcolor_b=220;

    $this->bordercolor_r=200;
    $this->bordercolor_v=200;
    $this->bordercolor_b=200;

    $this->deptcolor_r=255;
    $this->deptcolor_v=255;
    $this->deptcolor_b=255;

    $this->citycolor_r=34;
    $this->citycolor_v=111;
    $this->citycolor_b=50;
    
    $this->select_region=0;
    $this->select_region_color=array();

    $this->citycdiam=7;
    
    $this->fontsize=8;
    $this->font='../font/VeraSe.ttf';
    
    $this->title='';
    $this->titlecolor_r=0;
    $this->titlecolor_v=0;
    $this->titlecolor_b=0;
    
    $this->nom_villes=1;
    
    $this->chemin_cache='cache/cartographie/';  
    
    $this->ignore_cache=0;
    
    //je récupère les variables de projection, ligne 4 du fichier mif
    if($f=fopen($this->file.'.mif','rb'))
    {
      $g4p_ligne_cpt=0;

      while(!feof($f))
      {
        $g4p_ligne_cpt++;
        $buf=trim(fgets($f));
        if($g4p_ligne_cpt==4)
        {
          $buf=substr($buf,27,strpos($buf,'Bounds')-27);
           $this->num=explode(',',$buf);
          break;
        }
      }
    }
    
    if(!isset($this->num) or count($this->num)<9)
      die('impossible de lire les informations de projections');
  }
  
  function g4p_set_ignore_cache($i)
  {
    $this->ignore_cache=$i;
  }

  function g4p_set_select_region_color($i)
  {
    $this->select_region_color=$i;
  }

  function g4p_set_select_region($i)
  {
    $this->select_region=$i;
  }

  function g4p_set_chemin_cache($i)
  {
    $this->chemin_cache=$i;
  }

  function g4p_set_nom_villes($i)
  {
    $this->nom_villes=$i;
  }

  function g4p_set_dim($i)
  {
    $this->dim=$i;
  }

  function g4p_set_title($i)
  {
    $this->title=$i;
  }

  function g4p_set_title_color($r,$v,$b)
  {
    $this->titlecolor_r=$r;
    $this->titlecolor_v=$v;
    $this->titlecolor_b=$b;
  }

  function g4p_set_limite_adm($i)
  {
    $this->limite_adm=$i;
  }
  
  function g4p_set_marges($i)
  {
    $this->marges=$i;
  }

  function g4p_set_backgroundcolor($r,$v,$b)
  {
    $this->backgroundcolor_r=$r;
    $this->backgroundcolor_v=$v;
    $this->backgroundcolor_b=$b;
  }

  function g4p_set_bordercolor($r,$v,$b)
  {
    $this->bordercolor_r=$r;
    $this->bordercolor_v=$v;
    $this->bordercolor_b=$b;
  }

  function g4p_set_deptcolor($r,$v,$b)
  {
    $this->deptcolor_r=$r;
    $this->deptcolor_v=$v;
    $this->deptcolor_b=$b;
  }

  function g4p_set_citycolor($r,$v,$b)
  {
    $this->citycolor_r=$r;
    $this->citycolor_v=$v;
    $this->citycolor_b=$b;
  }

  function g4p_set_citydiam($i)
  {
    $this->citycdiam=$i;
  }

  function g4p_set_fontsize($i)//en pixel
  {
    $this->fontsize=$i;
  }

  function g4p_set_font($i)//en pixel
  {
    $this->font=$i;
  }

  function g4p_read_map()
  {
    if(!$this->ignore_cache)
    {
      //verif répertoire
      if(!is_dir(substr($this->chemin_cache,0,-1)))
        if(!mkdir(substr($this->chemin_cache,0,-1)))
          die('Il manque le répertoire '.substr($this->chemin_cache,0,-1).', impossible de le créer automatiquement');
    
      if(file_exists($this->chemin_cache.'cache.php'))
        $liste_cache=file($this->chemin_cache.'cache.php');
      else
        $liste_cache=array();
    
      $this->cache_name=md5($this->dim.$this->file.$this->limite_adm.$this->marges.$this->backgroundcolor_r.$this->backgroundcolor_v. $this->backgroundcolor_b.$this->bordercolor_r.$this->bordercolor_v.$this->bordercolor_b.$this->deptcolor_r.$this->deptcolor_v.$this->deptcolor_b.$this->citycolor_r.$this->citycolor_v.$this->citycolor_b.serialize($this->select_region).serialize($this->select_region_color));
      
      foreach($liste_cache as $a_cache)
      {
        $a_cache=trim($a_cache);
        if($a_cache==$this->cache_name)
        {
          if(file_exists($this->chemin_cache.$a_cache.'.php'))
          {
            $tmp=unserialize(file_get_contents($this->chemin_cache.$a_cache.'.php'));
            $this->bounds=$tmp->bounds;
            $this->liste_region=$tmp->liste_region;
            $this->dim_y=$tmp->dim_y;
            $this->dim_x=$tmp->dim_x;
            $this->x_echelle=$tmp->x_echelle;
            $this->y_echelle=$tmp->y_echelle;
            $this->g4p_load_map($a_cache);
            $this->cache='cache';
            return;
          }
        }
      }
    }

    $g4p_ligne_cpt=0;
    // recherches des infos de la carte:
    if($f=@fopen($this->file.'.mif','rb'))
    {
      $donnes_f=@fopen($this->file.'.mid','rb');
      
      // je recherche les limites de la carte
      $this->bounds['x_max']=$this->bounds['y_max']=0;
      $this->bounds['x_min']=$this->bounds['y_min']=1000000000;
      while(!feof($f)){
      $g4p_ligne_cpt++;
      $buf=trim(fgets($f));
      
      // Si on a deux numériques séparés par un espace
      $num=explode(' ',$buf);
      if((count($num)==2)&&is_numeric($num[0])&&is_numeric($num[1]))
      {
         if($this->bounds['x_max']<$num[0])
           $this->bounds['x_max']=$num[0];
         if($this->bounds['y_max']<$num[1])
           $this->bounds['y_max']=$num[1];
         if($this->bounds['x_min']>$num[0])
           $this->bounds['x_min']=$num[0];
         if($this->bounds['y_min']>$num[1])
           $this->bounds['y_min']=$num[1];
           
         if(isset($region_id))
         {
           if($this->liste_region[$region_id]['x_max']<$num[0])
             $this->liste_region[$region_id]['x_max']=$num[0];
           if($this->liste_region[$region_id]['y_max']<$num[1])
             $this->liste_region[$region_id]['y_max']=$num[1];
           if($this->liste_region[$region_id]['x_min']>$num[0])
             $this->liste_region[$region_id]['x_min']=$num[0];
           if($this->liste_region[$region_id]['y_min']>$num[1])
             $this->liste_region[$region_id]['y_min']=$num[1];       
         }
       }
       elseif($num[0]=='Region')
       {
         $region_id=$g4p_ligne_cpt+1;      
         $this->liste_region[$region_id]['x_max']=$this->liste_region[$region_id]['y_max']=0;
         $this->liste_region[$region_id]['x_min']=$this->liste_region[$region_id]['y_min']=10000000000;
         $this->liste_region[$region_id]['ligne_deb']=$region_id; 
         $donnes=trim(@fgets($donnes_f));
         $this->liste_region[$region_id]['nb_ss_region']=$num[2];
         $this->liste_region[$region_id]['nom']=$donnes; 
       }
       elseif($num[0]=='Center')
       {
         $this->liste_region[$region_id]['center']=$num[1].'|'.$num[2];    
       }
       elseif(is_numeric($buf))
       {
         $this->liste_region[$region_id]['ss_region'][]=$buf+$g4p_ligne_cpt+1;
       }
     }
     fclose($f);
     @fclose($donnes_f);
    }
    else
      die('carte inconnue');
    $this->cache='nocache';
  }

  function g4p_calcul_echelle()
  {
    $dim=$this->dim;   
    if($this->select_region===0)
    {
      $this->select_region=array_keys($this->liste_region);
    }
    else
    {
      $this->bounds['x_min']=$this->bounds['y_min']=1000000000;
      $this->bounds['x_max']=$this->bounds['y_max']=0;
      
      foreach($this->select_region as $a_region)
      {
        if($this->bounds['x_min']>=$this->liste_region[$a_region]['x_min'])
          $this->bounds['x_min']=$this->liste_region[$a_region]['x_min'];
        if($this->bounds['y_min']>$this->liste_region[$a_region]['y_min'])
          $this->bounds['y_min']=$this->liste_region[$a_region]['y_min'];
  
        if($this->bounds['x_max']<$this->liste_region[$a_region]['x_max'])
          $this->bounds['x_max']=$this->liste_region[$a_region]['x_max'];
        if($this->bounds['y_max']<$this->liste_region[$a_region]['y_max'])
          $this->bounds['y_max']=$this->liste_region[$a_region]['y_max'];
      }
    }
    
    // Coefficient pour dessiner une carte de $dim en largeur ou longueur max
    $dim=$dim-2*$this->marges; //j'enleve l'offset que j'ajoute après
    if($this->bounds['x_max']-$this->bounds['x_min']>$this->bounds['y_max']-$this->bounds['y_min'])
    {
      $this->dim_x=$dim;
      $this->x_echelle = $this->y_echelle = ($this->bounds['x_max']-$this->bounds['x_min'])/$this->dim_x;
      $this->dim_y=round(($this->bounds['y_max']-$this->bounds['y_min'])/$this->y_echelle);
    }
    else
    {
      $this->dim_y=$dim;
      $this->x_echelle = $this->y_echelle = ($this->bounds['y_max']-$this->bounds['y_min'])/$this->dim_y;
      $this->dim_x=round(($this->bounds['x_max']-$this->bounds['x_min'])/$this->y_echelle);
    }
  }  

  function g4p_build_map()
  {
    //echo '<pre>';
    //print_r($this->liste_region);
    //exit;
   
    if($this->cache=='nocache')
    {
      $this->g4p_calcul_echelle();
      
      $this->image=imagecreatetruecolor($this->dim_x+2*$this->marges,$this->dim_y+2*$this->marges+10);
      $black=imagecolorallocate($this->image,$this->bordercolor_r,$this->bordercolor_v,$this->bordercolor_b);
      $ceau=imagecolorallocate($this->image,$this->backgroundcolor_r,$this->backgroundcolor_v,$this->backgroundcolor_b);
      
      imagefilledrectangle($this->image,1,1,$this->dim_x+(2*$this->marges-2),$this->dim_y+(2*$this->marges-2)+10,$ceau);
     
      if($f=fopen($this->file.'.mif','rb'))
      {
        $g4p_ligne_cpt=0;
        $tmp=array_keys($this->select_region_color);
        
        while(!feof($f))
        {
          $g4p_ligne_cpt++;
          $buf=trim(fgets($f));
          $num=explode(' ',$buf);
          $eau=0;
          if(in_array($g4p_ligne_cpt,$this->select_region))
          {             
          
            if(in_array($g4p_ligne_cpt,array_keys($this->select_region_color)))
            {             
              $tmp=md5(serialize($this->select_region_color[$g4p_ligne_cpt]));
              if(isset($color_tab[$tmp]))
                $color_img=$color_tab[$tmp];
              else
                $color_img=$color_tab[$tmp]=imagecolorallocate($this->image,$this->select_region_color[$g4p_ligne_cpt][0],$this->select_region_color[$g4p_ligne_cpt][1],$this->select_region_color[$g4p_ligne_cpt][2]);           
            }
            else
             $color_img=imagecolorallocate($this->image,$this->deptcolor_r,$this->deptcolor_v,$this->deptcolor_b);
             
            while(!feof($f) and $num[0]!='Region')
            {
              // Si on a deux numériques séparés par un espace
              if((count($num)==2)&&is_numeric($num[0])&&is_numeric($num[1]))
              {
                $x_lu = $num[0];
                $y_lu = $num[1];
                $polygone[] = ( $x_lu - $this->bounds['x_min'] ) / $this->x_echelle+$this->marges; //+$this->marges decale la carte du bord
                $polygone[] = $this->dim_y-( $y_lu - $this->bounds['y_min'] ) / $this->y_echelle+$this->marges+10; //la coord est ( $y_lu - $y_base ) / $y_echelle, on y applique une symétrie (-1) et une translation (dim_y) pour remettre la carte à l'endroit
              }
              elseif((count($num)==1)&&is_numeric($num[0]) and !empty($polygone))
              {
                imagefilledpolygon($this->image,$polygone,count($polygone)/2,$color_img);
                if($this->limite_adm==1)
                  imagepolygon($this->image,$polygone,count($polygone)/2,$black);
                $polygone=array();
                $eau=1;
              }
              $g4p_ligne_cpt++;
              $buf=trim(fgets($f));
              $num=explode(' ',$buf);
            }
            
            if($eau==0)
            {
              imagefilledpolygon($this->image,$polygone,count($polygone)/2,$color_img);
              if($this->limite_adm==1)
                imagepolygon($this->image,$polygone,count($polygone)/2,$black);
            }
            else
            {
              imagefilledpolygon($this->image,$polygone,count($polygone)/2,$ceau);
              if($this->limite_adm==1)
                imagepolygon($this->image,$polygone,count($polygone)/2,$black);
            }
              
            $polygone=array();
            $eau=0;
    
          }
        }
        fclose($f);
      }
      if(!$this->ignore_cache)
        $this->g4p_cache_map($this->cache_name);
    }
  }

  function g4p_cache_map($nom)
  {
    global $g4p_chemin,$liste_cache;
    //ajout de la carte en cache
    if(file_exists($this->chemin_cache.'cache.php'))
      $liste_cache=file($this->chemin_cache.'cache.php');
    else
      $liste_cache=array();
    
    $liste_cache[]=$nom;
    if($write_cache=fopen($this->chemin_cache.$nom.'.php','w'))
    {
      fwrite($write_cache,serialize($this));
      fclose($write_cache);
    }
    if($write_cache=fopen($this->chemin_cache.'cache.php','wb'))
    {
      $liste_cache=array_unique($liste_cache);
      foreach($liste_cache as $a_cache)
        if(trim($a_cache)!='')
          fwrite($write_cache,$a_cache."\n");
      fclose($write_cache);
    }

    imagepng($this->image,$this->chemin_cache.$nom.'.png'); 
  }
  
  function g4p_load_map($nom)
  {
    global $g4p_chemin;
    if(file_exists($this->chemin_cache.$nom.'.png'))
      $this->image=imagecreatefrompng($this->chemin_cache.$nom.'.png'); 
  }

  function g4p_place_ville($latitude,$longitude,$texte='')
  {
    static $cpt=1;


    if($latitude==0 and $longitude==0)
      return;

    //variables:
    $a=6378137; //demi grand axe de l'ellipsoide (m)
    $e=0.08181919106; //première excentricité de l'ellipsoide
    //je suppose que les coordonnées des ville sont dans le système WGS84

    $l0=$lc=deg2rad($this->num[3]);
    $phi0=deg2rad($this->num[4]); //latitude d'origine en radian
    $phi1=deg2rad($this->num[5]); //1er parallele automécoïque
    $phi2=deg2rad($this->num[6]); //2eme parallele automécoïque
    
    $x0=$this->num[7]; //coordonnées à l'origine
    $y0=$this->num[8]; //coordonnées à l'origine
    
    $phi=deg2rad($latitude);
    $l=deg2rad($longitude);
    
    //calcul des grandes normales
    $gN1=$a/sqrt(1-$e*$e*sin($phi1)*sin($phi1));
    $gN2=$a/sqrt(1-$e*$e*sin($phi2)*sin($phi2));
    
    //calculs des latitudes isométriques
    $gl1=log(tan(pi()/4+$phi1/2)*pow((1-$e*sin($phi1))/(1+$e*sin($phi1)),$e/2));
    $gl2=log(tan(pi()/4+$phi2/2)*pow((1-$e*sin($phi2))/(1+$e*sin($phi2)),$e/2));
    $gl0=log(tan(pi()/4+$phi0/2)*pow((1-$e*sin($phi0))/(1+$e*sin($phi0)),$e/2));
    $gl=log(tan(pi()/4+$phi/2)*pow((1-$e*sin($phi))/(1+$e*sin($phi)),$e/2));
    
    //calcul de l'exposant de la projection
    $n=(log(($gN2*cos($phi2))/($gN1*cos($phi1))))/($gl1-$gl2);//ok
    
    //calcul de la constante de projection
    $c=(($gN1*cos($phi1))/$n)*exp($n*$gl1);//ok
    
    //calcul des coordonnées
    //exp(-1*$n*$l)*sin($n*($l-$l0));
    $ys=$y0+$c*exp(-1*$n*$gl0);
    
    //echo $n*($l-$lc);
    $x93=$x0+$c*exp(-1*$n*$gl)*sin($n*($l-$lc));
    $y93=$ys-$c*exp(-1*$n*$gl)*cos($n*($l-$lc));
     
     $vert=imagecolorallocate($this->image,$this->citycolor_r,$this->citycolor_v,$this->citycolor_b);
     $black=imagecolorallocate($this->image,0,0,0);
     $x = ( $x93 - $this->bounds['x_min'] ) / $this->x_echelle + $this->marges; //+5 decale la carte du bord
     $y = $this->dim_y-( $y93 - $this->bounds['y_min'] ) / $this->y_echelle + $this->marges+10; //la coord est ( $y_lu - $y_base ) / $y_echelle, on y applique une     
     imagefilledellipse($this->image,$x,$y,$this->citycdiam,$this->citycdiam,$vert); 
     
     if($texte!='' and $this->nom_villes!=0)
     {
       $box=imagettfbbox ( $this->fontsize, 0,$this->font, $texte); 
       $this->libele['i'.$cpt++]=array('x_ville'=>$x,'y_ville'=>$y,'largeur'=>($box[2]-$box[0]), 'hauteur'=>($box[7]-$box[1]), 'texte'=>$texte);
     }
  }
  
  function g4p_place_ville_libele()
  {
/*echo '<e>';
print_r($this->libele);
exit;*/
    $black=imagecolorallocate($this->image,0,0,0);
    $red=imagecolorallocate($this->image,255,0,0);

    if(!$this->nom_villes or empty($this->libele))
      return;

/*
$this->libele=array(
'i0' => Array
        (
            'x_ville' => 503.50418449404,
            'y_ville' => 393.44735894786,
            'largeur' => 43,
            'hauteur' => -12,
            'texte' => 'Lyon(1)'
        ),

    'i1'=> Array
        (
            'x_ville' => 504.37950714442,
            'y_ville' => 396.83389433496,
            'largeur' => 74,
            'hauteur' => -12,
            'texte' => 'Saint-Fons(1)'
        ),
    'i2'=> Array
        (
            'x_ville' => 506.37950714442,
            'y_ville' => 397.83389433496,
            'largeur' => 74,
            'hauteur' => -12,
            'texte' => 'Saint-Fons(3)'
        ),
    'i3'=> Array
        (
            'x_ville' => 500.37950714442,
            'y_ville' => 393.83389433496,
            'largeur' => 74,
            'hauteur' => -12,
            'texte' => 'Saint-Fons(9)'
        )
);*/

    //recherche de chevauchement des points des villes
    foreach($this->libele as $a_key=>$a_tab)
    {
      $g4p_nb_place_free=array(1=>1,1,1,1,1,1,1,1);
      foreach($this->libele as $b_key=>$b_tab)
      {
        if($a_key!=$b_key)
        {
          for($j=1;$j<=8;$j++)
          {
            if($this->g4p_check_chevauchement($this->calc_pos($j,$a_key),$this->libele[$b_key]['x_ville'],$this->libele[$b_key]['y_ville']))
              $g4p_nb_place_free[$j]=0;
          }
        }
      }
      $this->libele[$a_key]['coord_libre']=$g4p_nb_place_free;
    }

    $ordre_tri=array_map('nbre_pos_libre',$this->libele);
    array_multisort($this->libele,SORT_ASC,$ordre_tri);
    $this->libele_place=$this->libele;

    $i=0;
    while(count($this->libele)>0)
    {
      if($i++>5000)
        break;

      $ordre_tri=array_map('nbre_pos_libre',$this->libele);
      if(array_sum($ordre_tri)==0)
      {
        $this->libele_impossible=1;
        break;
      }

      foreach($this->libele as $key_libele=>$a_libele)
      {
        foreach($a_libele['coord_libre'] as $key_pos=>$a_position)
        {
          if($a_position==1)
          {
            if(!$this->g4p_update_chevauchement($key_libele,$key_pos))
            {
              $this->libele_place[$key_libele]['coord_trouve']=$this->calc_pos($key_pos,$key_libele);
              $this->libele[$key_libele]['coord_libre'][$key_pos]=0;
              unset($this->libele[$key_libele]);
            }
            if(!empty($this->libele[$key_libele]))
              $this->libele[$key_libele]['coord_libre'][$key_pos]=0;
            $ordre_tri=array_map('nbre_pos_libre',$this->libele);
            array_multisort($this->libele,SORT_ASC,$ordre_tri);
            break(2);
          }
        }
      }
    }

/*//echo '<pre>';
print_r($this->libele_place);
exit;//*/
    $aucun_libele=imagecolorallocate($this->image,255,0,0);
    foreach($this->libele_place as $a_tab)
    {
      if(isset($a_tab['coord_trouve']))
        imagettftext($this->image, $this->fontsize, 0, $a_tab['coord_trouve'][1], $a_tab['coord_trouve'][2]-1, $black, $this->font,$a_tab['coord_trouve'][5]); 
      else
        imagefilledellipse($this->image,$a_tab['x_ville'],$a_tab['y_ville'],$this->citycdiam,$this->citycdiam,$aucun_libele);
      //imagerectangle ($this->image,$a_tab['coord_trouve'][1], $a_tab['coord_trouve'][2],$a_tab['coord_trouve'][3], $a_tab['coord_trouve'][4],$black);
      
      //imagefilledellipse($this->image,$a_tab['coord_trouve'][3], $a_tab['coord_trouve'][2],$this->citycdiam,$this->citycdiam,$red);
    }
  }
  
  function g4p_send_carte()
  {
    if($this->title!='')
    {
      $title_color=imagecolorallocate($this->image,$this->titlecolor_r,$this->titlecolor_v,$this->titlecolor_b);
      $box=imagettfbbox ( round($this->fontsize*1.5), 0,$this->font, $this->title);
      //round($this->dim_x-(($box[2]-$box[0])/2))
      imagettftext($this->image, round($this->fontsize*1.5), 0, round(($this->dim_x+2*$this->marges)/2-(($box[2]-$box[0])/2)), 20, $title_color, $this->font,$this->title);
    }      
  
    $black=imagecolorallocate($this->image,0,0,0);

    if(!empty($this->libele_impossible))
      $this->libele_impossible=' - Certains libélés n\'ont pu être placés';
    else
      $this->libele_impossible='';

    imagettftext($this->image, $this->fontsize*0.8, 0, 10, $this->dim_y+2*$this->marges+5, $black, $this->font,'Parois.net, '.date('d/m/Y').$this->libele_impossible);   
  

    if (!headers_sent())
    {
      header('Date: ' . gmdate("D, d M Y H:i:s") . ' GMT');
      header('Cache-Control: no-cache');
      header('Pragma: public');
      $modif = gmdate('D, d M Y H:i:s', time()) ;
      header("Last-Modified: $modif GMT");
  
      $modif = gmdate('D, d M Y H:i:s', time()-3600) ;
      header('Expires: '.$modif);
      header("Content-type: image/png");
      imagepng($this->image);  
    }
    else
      die('Erreur lors de la création de l\'image');
    
  }

  function g4p_img_map()
  {
    $texte='';
    foreach($this->liste_region as $a_region)
    {
      $center=explode('|',$a_region['center']);
      $center[0] = ( $center[0] - $this->bounds['x_min'] ) / $this->x_echelle+$this->marges; //+$this->marges decale la carte du bord
      $center[1] = $this->dim_y-( $center[1] - $this->bounds['y_min'] ) / $this->y_echelle+$this->marges+10; //la coord est ( $y_lu - $y_base ) 
        $texte.='<area href="'.g4p_make_url('modules','cartographie.php','type_carte='.$_REQUEST['type_carte'].'&amp;event='.$_REQUEST['event'].'&amp;nom_villes='.$_REQUEST['nom_villes'].'&amp;zoom='.$a_region['ligne_deb'],0).'" alt="'.$a_region['nom'].'" shape="circ" coords="'.round($center[0]).','.round($center[1]).',10" />';
    }
    return $texte;
  }
  
  function g4p_color_active_area()
  {
    $transparent=imagecolorallocatealpha($this->image,255,255,0,80);
    foreach($this->liste_region as $a_region)
    {
      $center=explode('|',$a_region['center']);
      $center[0] = ( $center[0] - $this->bounds['x_min'] ) / $this->x_echelle+$this->marges; //+$this->marges decale la carte du bord
      $center[1] = $this->dim_y-( $center[1] - $this->bounds['y_min'] ) / $this->y_echelle+$this->marges+10; //la coord est ( $y_lu - $y_base ) 
      imagefilledellipse($this->image,$center[0],$center[1],20,20,$transparent); 
    }  
  }

  function calc_pos($id,$key)
  {
    $espace_x=6;
    $espace_y=6;
    
    //$debug=' '.$id;
    $debug='';
    $a_libele=$this->libele[$key];
  
    switch($id)
    {
      case 1:
      default:
      return array($key, $a_libele['x_ville'],$a_libele['y_ville'],$a_libele['x_ville']+$a_libele['largeur'],$a_libele['y_ville']+$a_libele['hauteur'],$a_libele['texte'].$debug);
      break;
      
      case 2:
      return array($key, $a_libele['x_ville'],$a_libele['y_ville']-$a_libele['hauteur'],$a_libele['x_ville']+$a_libele['largeur'],$a_libele['y_ville'],$a_libele['texte'].$debug);
      break;
      
      case 3:
      return array($key, $a_libele['x_ville']-$a_libele['largeur'],$a_libele['y_ville']-$a_libele['hauteur'],$a_libele['x_ville'],$a_libele['y_ville'],$a_libele['texte'].$debug);
      break;
      
      case 4:
      return array($key, $a_libele['x_ville']-$a_libele['largeur'],$a_libele['y_ville'],$a_libele['x_ville'],$a_libele['y_ville']+$a_libele['hauteur'],$a_libele['texte'].$debug);
      break;
      
      case 5:
      return array($key, $a_libele['x_ville']-$a_libele['largeur'],$a_libele['y_ville']-$a_libele['hauteur']/2,$a_libele['x_ville'],$a_libele['y_ville']+$a_libele['hauteur']/2,$a_libele['texte'].$debug);
      break;
      
      case 6:
      return array($key, $a_libele['x_ville']-$a_libele['largeur']/2,$a_libele['y_ville'],$a_libele['x_ville']+$a_libele['largeur']/2,$a_libele['y_ville']+$a_libele['hauteur'],$a_libele['texte'].$debug);
      break;

      case 7:
      return array($key, $a_libele['x_ville'],$a_libele['y_ville']-$a_libele['hauteur']/2,$a_libele['x_ville']+$a_libele['largeur'],$a_libele['y_ville']+$a_libele['hauteur']/2,$a_libele['texte'].$debug);
      break;
              
      case 8:
      return array($key, $a_libele['x_ville']-$a_libele['largeur']/2,$a_libele['y_ville']-$a_libele['hauteur'],$a_libele['x_ville']+$a_libele['largeur']/2,$a_libele['y_ville'],$a_libele['texte'].$debug);
      break;
    }
  }

  function g4p_check_chevauchement($tab,$x_ville,$y_ville)
  {
/*
print_r($tab);
echo '<br>',$x_ville;
echo '<br>',$y_ville;
echo '--<br>';*/
    if($y_ville>$tab[4] and $y_ville<$tab[2] and $x_ville>$tab[1] and $x_ville<$tab[3])
      return 1;
    else
      return 0;
  }

  function g4p_update_chevauchement($key_libele,$key_pos)
  {
    $coord=$this->calc_pos($key_pos,$key_libele);
    foreach($this->libele_place as $key=>$a_libele)
    {
      if(!empty($a_libele['coord_trouve']))
      {
        if($key_libele!=$key)
        {
          if($this->g4p_check_chevauchement2($a_libele['coord_trouve'],$coord))
            return 1;
        }
      }
    }
    return 0;
  }

  function g4p_check_chevauchement2($a_tab,$b_tab)
  {
    if($a_tab[1] != $b_tab[1] and $a_tab[2] != $b_tab[2] and $a_tab[3] != $b_tab[3] and $a_tab[4] != $b_tab[5])
    {
      if($a_tab[4]>$b_tab[4] and $a_tab[4]<$b_tab[2] and $a_tab[1]>$b_tab[1] and $a_tab[1]<$b_tab[3])
        $a=1;
      elseif($a_tab[4]>$b_tab[4] and $a_tab[4]<$b_tab[2] and $a_tab[1]>$b_tab[1] and $a_tab[1]<$b_tab[3])
        $a=2;
      elseif($a_tab[4]>$b_tab[4] and $a_tab[4]<$b_tab[2] and $a_tab[3]>$b_tab[1] and $a_tab[3]<$b_tab[3])
        $a=3;
      elseif($a_tab[2]>$b_tab[4] and $a_tab[2]<$b_tab[2] and $a_tab[3]>$b_tab[1] and $a_tab[3]<$b_tab[3])
        $a=4;
      elseif($b_tab[2]>$a_tab[4] and $b_tab[2]<$a_tab[2] and $b_tab[1]>$a_tab[1] and $b_tab[1]<$a_tab[3])
        $a=5;
      elseif($b_tab[4]>$a_tab[4] and $b_tab[4]<$a_tab[2] and $b_tab[1]>$a_tab[1] and $b_tab[1]<$a_tab[3])
        $a=6;
      elseif($b_tab[4]>$a_tab[4] and $b_tab[4]<$a_tab[2] and $b_tab[3]>$a_tab[1] and $b_tab[3]<$a_tab[3])
        $a=7;
      elseif($b_tab[2]>$a_tab[4] and $b_tab[2]<$a_tab[2] and $b_tab[3]>$a_tab[1] and $b_tab[3]<$a_tab[3])
        $a=8;
      elseif($b_tab[2]>$a_tab[4] and $b_tab[2]<$a_tab[2] and $b_tab[1]>$a_tab[1] and $b_tab[1]<$a_tab[3])
        $a=9;
      else
        $a=0;

      if($a)
        return 1;
      else
        return 0;
    }
    else
      return 1;
  }

}

function nbre_pos_libre($a_libele)
  {
    return array_sum($a_libele['coord_libre']);
  }

//$g4p_dept_mif=array_flip(array('ain','aisne','allier','alpesdehauteprovence','alpesmaritimes','ardeche','ardennes','ariege','aube','aude','aveyron','basrhin','bouchesdurhone','calvados','cantal','charente','charentemaritime','cher','correze','corse','cotedor','cotesdarmor','creuse','deuxsevres','dordogne','doubs','drome','essonne','eure','eureetloir','finistere','gard','gers','gironde','guadeloupe','guyane','hautrhin','hautegaronne','hauteloire','hautemarne','hautesaone','hautesavoie','hautevienne','hautesalpes','hautespyrenees','hautsdeseine','herault','illeetvilaine','indre','indreetloire','isere','jura','landes','loiretcher','loire','loireatlantique','loiret','lot','lotetgaronne','lozere','maineetloire','manche','marne','martinique','mayenne','mayotte','meurtheetmoselle','meuse','monaco','morbihan','moselle','nievre','nord','nouvelle-caledonie','oise','orne','paris','pasdecalais','polynesiefrancaise','puydedome','pyreneesatlantiques','pyreneesorientales','reunion','rhone','saoneetloire','sarthe','savoie','seineetmarne','seinemaritime','seinesaintdenis','somme','stpierreetmiquelon','tarn','tarnetgaronne','territoiredebelfort','valdemarne','valdoise','var','vaucluse','vendee','vienne','vosges','wallisetfutuna','yonne','yvelines'));

  
  
?>
