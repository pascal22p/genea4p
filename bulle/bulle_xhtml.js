document.onmousemove = mouseMove;
function mouseMove(e)
 {
  bouge_bulle(e);
 }

//Couleur d'arri�re-plan principale
//G�n�ralement une couleur claire (blanc, jaune,etc)
  if (typeof fcolor == 'undefined') { var fcolor = "#CCCCFF";}
  
//Couleur du bord et du titre (caption)
//G�n�ralement une couleur fonc�e (noir, bleu marine,etc)
  if (typeof backcolor == 'undefined') { var backcolor = "#333399";}
  
//Couleur du texte de l'infobulle
//G�n�ralement une couleur fonc�e
  if (typeof textcolor == 'undefined') { var textcolor = "#000000";}

//Epaisseur du bord en pixels 
//G�n�ralement entre 1 et 3 
  if (typeof border == 'undefined') { var border = "1px";}
  
//Retrait horizontal en pixels de l'infobulle par rapport au curseur
//G�n�ralement entre 3 et 12 
  if (typeof offsetx == 'undefined') { var offsetx = 10;}
  
//Retrait vertical en pixels de l'infobulle par rapport au curseur
//G�n�ralement entre 3 et 12
  if (typeof offsety == 'undefined') { var offsety = 10;}

 var ns6 = document.getElementById && !document.all;
 var ie4 = document.all;
 var bulle;
     
 var range = document.createRange();
 var bal;

  function bouge_bulle(e)
  {
    if(ie4)
    {
     bulle = document.all["bulle_div"];
     x=event.clientX+offsetx+document.body.scrollLeft;
     y=event.clientY+offsety+document.body.scrollTop;
    }
    else if(ns6)
    {
     bulle = document.getElementById("bulle_div");
     x=e.pageX+offsetx;
     y=e.pageY+offsety;
    }
   bulle.style.left=x+'px';
   bulle.style.top=y+'px';
  }

  function AffBulle(text)
  {
	txt= '<span xmlns="http://www.w3.org/1999/xhtml">' + text + '</span>'
    racine = new DOMParser().parseFromString(txt, "application/xhtml+xml").documentElement;
    bal=document.createElement('div');
    bal.style.backgroundColor="#FFBE8D";
    bal.style.color="black";
    bal.style.border="2px ridge Gray";
    bal.style.fontSize="x-small";
    bal.style.textAlign="left";
    bal.style.lineHeight="1em";
    bal.appendChild(racine);
    bulle.appendChild(bal);
    bulle.style.visibility = "visible"
  }

  function HideBulle()
  {
   bulle.style.visibility="hidden";
   bulle.removeChild(bal);
  }
