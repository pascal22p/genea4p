<?php

function g4p_latex_link_nom($indi,$format='full')
{
    if(!is_object($indi))
        $indi=g4p_load_indi_infos($indi);
       
    $return=g4p_latex_link('I'.$indi->indi_id,$indi->nom.' '.$indi->prenom);
    if($format=='full')
    {
        $tmp=$indi->date_rapide();
        if(!empty($tmp)) $return.=' \begin{footnotesize}\textit{'.$tmp.'}\end{footnotesize}';
    }
    return $return;
}

function g4p_latex_link_prenom($indi)
{
    if(!is_object($indi))
        $indi=g4p_load_indi_infos($indi);
       
    $return=g4p_latex_link('I'.$indi->indi_id,$indi->prenom);
    //$tmp=$indi->date_rapide();
    //if(!empty($tmp)) $return.=' \begin{footnotesize}\textit{'.$tmp.'}\end{footnotesize}';
    return $return;
}

function g4p_latex_link($target, $text)
{
    return '\hyperlink{'.$target.'}{'.$text.'}';
}

function g4p_latex_write_header()
{   
    return '\documentclass[a4paper,10pt]{article}
\usepackage{fontspec}
\usepackage{xltxtra} % charge aussi fontspec et xunicode, nécessaires... 
\usepackage{hyperref}
\usepackage{framed}
\usepackage{color}
\usepackage{titlesec}
\usepackage{underscore}
\usepackage{graphicx}
\usepackage{geometry}
\usepackage{makeidx}
\geometry{hmargin=2.5cm, top=2cm, bottom=2cm}
\usepackage{tikz}
\usetikzlibrary{trees,positioning,arrows}
\hypersetup{ % Modifiez la valeur des champs suivants
    pdfauthor   = {Pascal Parois},%
    pdftitle    = {},%
    pdfsubject  = {},%
    pdfkeywords = {},%
    pdfcreator  = {XeLaTeX},%
    pdfproducer = {XeLaTeX},
    bookmarks         = false,%     % Signets
    bookmarksnumbered = false,%     % Signets numerote
    pdfpagemode       = UseOutlines,%     % Signets/vignettes ferme a l\'ouverture
    bookmarksopen	= false,
    pdfstartview      = FitH,%     % La page prend toute la largeur
    pdfpagelayout     = continuous,% Vue par page
    colorlinks        = true,%     % Liens en couleur
    linkcolor         = blue,
    pdfborder         = {0 0 0}%   % Style de bordure : ici, pas de bordure
} 
\usepackage[francais]{babel}


\makeatletter
\newcommand{\\affichedate}[1]{#1}

\newcommand*{\limitbox}[3]{%
  \begingroup
    \setlength{\@tempdima}{#1}%
    \setlength{\@tempdimb}{#2}%
    \resizebox{%
      \ifdim\width>\@tempdima\@tempdima\else\width\fi
    }{!}{%
      \resizebox{!}{%
        \ifdim\height>\@tempdimb\@tempdimb\else\height\fi
      }{#3}%
    }%
  \endgroup
}
\definecolor{LightBlue}{rgb}{0.94,0.94,1}
\definecolor{LightGreen}{rgb}{0.9,1,0.9}

\newenvironment{boite}[1][LightGreen]{%
  \def\FrameCommand{\fboxsep=\FrameSep \colorbox{#1}}%
  \MakeFramed {\hsize0.9\textwidth\FrameRestore}}%
{\endMakeFramed}

\makeatother

\titleformat{\section}
{\vspace{3cm}\titlerule[2pt]
\vspace{.8ex}%
\huge\bfseries\filleft}
{\thesection.}{1em}{}

\titleformat{\subsection}
{\vspace{0.5cm}%
\Large\itshape}
{\thesection.}{1em}{}

\titleformat{\subsubsection}
{%
\large\bfseries}
{\thesection.}{0.5em}{}

\renewcommand{\paragraph}{\parskip = 0pt}

\makeindex

\begin{document}';
}

function g4p_latex_write_event($event)
{   
    global $latex, $g4p_tag_def;

    fwrite($latex, '\begin{description}'."\n");
    //echo '<pre>'; print_r($g4p_a_ievents);
    if(!empty($event->details_descriptor))
        $g4p_tmp=' ('.$event->details_descriptor.')';
    else
        $g4p_tmp='';
    if(empty($event->tag))
        var_dump($event);
    fwrite($latex, '\item['.$g4p_tag_def[$event->tag].'] ');
    fwrite($latex, '\\affichedate{'.g4p_date($event->gedcom_date).'} '."\n");

    if(!empty($event->age) or 
        !empty($event->place->id) or 
        !empty($event->address))
    {
        fwrite($latex, '\begin{footnotesize}'."\n");
        fwrite($latex, '\begin{description}'."\n");
        if(!empty($event->age))
            fwrite($latex, '\item[Age] '.$event->age."\n");

        //place
        if($event->place->g4p_formated_place()!='')
            fwrite($latex, '\item[Lieu] '.$event->place->g4p_formated_place()."\n");
        
        //adresse
        if(!empty($event->address))
            fwrite($latex, '\item[Adresse] '.$event->address->g4p_formated_addr()."\n");
        fwrite($latex, '\end{description}'."\n");
        fwrite($latex, '\end{footnotesize}'."\n");
    }
    fwrite($latex, '\end{description}'."\n");

    if(!empty($event->sources))
        foreach($event->sources as $a_source)
            g4p_latex_write_source($a_source);
    if(!empty($event->notes))
        foreach($event->notes as $a_note)
            g4p_latex_write_note($a_note);
}

function g4p_latex_write_attribut($event)
{   
    global $latex, $g4p_tag_def;

    fwrite($latex, '\begin{description}'."\n");
    if($event->details_descriptor)
        $g4p_tmp=' ('.$g4p_a_ievents->details_descriptor.')';
    else
        $g4p_tmp='';
    fwrite($latex, '\item['.$g4p_tag_def[$event->tag].'] ');
    fwrite($latex, '\\affichedate{'.g4p_date($event->gedcom_date).'} '."\n");
    //echo (isset($g4p_a_ievents->sources))?(' <span style="color:blue; font-size:x-small;">-S-</span> '):('');
    //echo (isset($g4p_a_ievents->notes))?(' <span style="color:blue; font-size:x-small;">-N-</span> '):('');
    //echo (isset($g4p_a_ievents->medias))?(' <span style="color:blue; font-size:x-small;">-M-</span> '):('');
    //echo (isset($g4p_a_ievents->asso))?(' <span style="color:blue; font-size:x-small;">-T-</span> '):('');
    //echo (isset($g4p_a_ievents->id))?(' <a href="'.g4p_make_url('','detail_event.php','parent=INDI&amp;id_parent='.$g4p_a_ievents->id,0).'" class="noprint">'.$g4p_langue['detail'].'</a><br />'):('<br />');
                        
    if(!empty($event->age) or 
        !empty($event->place->id) or 
        !empty($event->address))
    {           
        fwrite($latex, '\begin{footnotesize}'."\n");
        fwrite($latex, '\begin{description}'."\n");
        if(!empty($event->age))
            fwrite($latex, '\item[Age] '.$event->age."\n");

        //place
        if($g4p_a_ievents->place->g4p_formated_place()!='')
            fwrite($latex, '\item[Lieu] '.$event->place->g4p_formated_place()."\n");
        
        //adresse
        if(!empty($event->address))
            fwrite($latex, '\item[Adresse] '.$event->address->g4p_formated_addr()."\n");
        fwrite($latex, '\end{description}'."\n");
        fwrite($latex, '\end{footnotesize}'."\n");
    }
    fwrite($latex, '\end{description}'."\n");

    if(!empty($event->sources))
        foreach($event->sources as $a_source)
            g4p_latex_write_source($a_source);
    if(!empty($event->notes))
        foreach($event->notes as $a_note)
            g4p_latex_write_note($a_note);
}

function g4p_latex_write_source($a_source)
{
    global $latex, $g4p_tag_def;
    //var_dump($a_source);
    
    fwrite($latex, '\begin{boite}[LightBlue]%'."\n");
    fwrite($latex, '\begin{footnotesize}'."\n");
    fwrite($latex, '\noindent\textit{Source citation} '."\n");
    fwrite($latex, '\begin{description}'."\n");
    
    //fwrite($latex, '\item[Id] '.$a_source->id."\n");
    if(!empty($a_source->page))
        fwrite($latex, '\item[Page] '.$a_source->page."\n");
    if(!empty($a_source->even))
        fwrite($latex, '\item[Even] '.$a_source->even."\n");
    if(!empty($a_source->even_role))
        fwrite($latex, '\item[Even role] '.$a_source->even_role."\n");
    if(!empty($a_source->data_dates))
        fwrite($latex, '\item[Data dates] '.$a_source->data_dates."\n");
    if(!empty($a_source->data_text))
        fwrite($latex, '\item[Data text] '.$a_source->data_text."\n");
    if(!empty($a_source->quay))
        fwrite($latex, '\item[Quay] '.$a_source->quay."\n");
    if(!empty($a_source->subm))
        fwrite($latex, '\item[Subm] '.$a_source->Subm."\n");
    if(!empty($a_source->timestamp))
        fwrite($latex, '\item[Dernière modification] '.$a_source->timestamp."\n");
    if(!empty($a_source->record))
    {
        fwrite($latex, '\par\textit{Source record}'."\n");
        fwrite($latex, '\begin{description}'."\n");
        //fwrite($latex, '\item[Id] '.$a_source->record->id."\n"); 
        if(!empty($a_source->record->auth))
            fwrite($latex, '\item[Rapporteur] '.$a_source->record->auth."\n"); 
        if(!empty($a_source->record->title))
            fwrite($latex, '\item[Titre] '.$a_source->record->title."\n"); 
        if(!empty($a_source->record->abbr))
            fwrite($latex, '\item[abbr] '.$a_source->record->abbr."\n"); 
        if(!empty($a_source->record->publ))
            fwrite($latex, '\item[Publ] '.$a_source->record->publ."\n"); 
        if(!empty($a_source->record->agnc))
            fwrite($latex, '\item[Agnc] '.$a_source->record->agnc."\n"); 
        if(!empty($a_source->record->rin))
            fwrite($latex, '\item[rin] '.$a_source->record->rin."\n"); 
        if(!empty($a_source->record->repo_medi))
            fwrite($latex, '\item[Repo_medi] '.$a_source->record->repo_medi."\n"); 
        if(!empty($a_source->record->repo_caln))
            fwrite($latex, '\item[Repo_caln] '.$a_source->record->repo_caln."\n"); 
        if(!empty($a_source->record->timestamp))
            fwrite($latex, '\item[Dernière modification] '.$a_source->record->timestamp."\n"); 
        if(!empty($a_source->record->repo))
        {
            fwrite($latex, '\par\textit{Dépôt} '.$a_source->record->repo->name."\n");
            fwrite($latex, '\begin{description}'."\n");
            //fwrite($latex, '\item[Id] '.$a_source->record->id."\n"); 
            if(!empty($a_source->record->repo->addr))
            {
                if(!empty($a_source->record->repo->addr->addr))
                    fwrite($latex, '\item[Addresse] '.$a_source->record->repo->addr->addr."\n"); 
                if(!empty($a_source->record->repo->addr->city))
                    fwrite($latex, '\item[Ville] '.$a_source->record->repo->addr->city."\n"); 
                if(!empty($a_source->record->repo->addr->stae))
                    fwrite($latex, '\item[État] '.$a_source->record->repo->addr->stae."\n"); 
                if(!empty($a_source->record->repo->addr->post))
                    fwrite($latex, '\item[Code postal] '.$a_source->record->repo->addr->post."\n"); 
                if(!empty($a_source->record->repo->addr->ctry))
                    fwrite($latex, '\item[Pays] '.$a_source->record->repo->addr->ctry."\n"); 
            }
            fwrite($latex, '\end{description}'."\n");
        }
        fwrite($latex, '\end{description}'."\n");
    }
    fwrite($latex, '\end{description}'."\n");

    if(!empty($a_source->medias))
        foreach($a_source->medias as $a_media)
            g4p_latex_write_media($a_media);
    fwrite($latex, '\end{footnotesize}'."\n");
    fwrite($latex, "\end{boite}\n");

    //echo (isset($g4p_a_ievents->notes))?(' <span style="color:blue; font-size:x-small;">-N-</span> '):('');
    //echo (isset($g4p_a_ievents->medias))?(' <span style="color:blue; font-size:x-small;">-M-</span> '):('');
    //echo (isset($g4p_a_ievents->asso))?(' <span style="color:blue; font-size:x-small;">-T-</span> '):('');
    //echo (isset($g4p_a_ievents->id))?(' <a href="'.g4p_make_url('','detail_event.php','parent=INDI&amp;id_parent='.$g4p_a_ievents->id,0).'" class="noprint">'.$g4p_langue['detail'].'</a><br />'):('<br />');
}

function g4p_latex_write_note($a_note)
{
    global $latex, $g4p_tag_def;
    fwrite($latex, '\begin{boite}[LightBlue]%'."\n");
    fwrite($latex, '\begin{footnotesize}'."\n");
    fwrite($latex, '\noindent\textit{Note}'."\n");
    fwrite($latex, $a_note->text."\n");
    fwrite($latex, '\end{footnotesize}'."\n");
    fwrite($latex, "\end{boite}\n");

    //echo (isset($g4p_a_ievents->notes))?(' <span style="color:blue; font-size:x-small;">-N-</span> '):('');
    //echo (isset($g4p_a_ievents->medias))?(' <span style="color:blue; font-size:x-small;">-M-</span> '):('');
    //echo (isset($g4p_a_ievents->asso))?(' <span style="color:blue; font-size:x-small;">-T-</span> '):('');
    //echo (isset($g4p_a_ievents->id))?(' <a href="'.g4p_make_url('','detail_event.php','parent=INDI&amp;id_parent='.$g4p_a_ievents->id,0).'" class="noprint">'.$g4p_langue['detail'].'</a><br />'):('<br />');
}

function g4p_latex_write_media($a_media)
{
    global $latex, $g4p_config;

    fwrite($latex, "\n");
    fwrite($latex, '\begin{boite}[LightGreen]'."\n");
    fwrite($latex, '\begin{footnotesize}'."\n");
    fwrite($latex, '\noindent\textit{Média} ');
    fwrite($latex, $a_media->title."\\newline\n");
    if(!empty($a_media->file) and file_exists($g4p_config['g4p_path'].'/cache/'.$_SESSION['genea_db_nom'].'/objets/'.$a_media->file))
        fwrite($latex, '\begin{center}\limitbox{0.95\textwidth}{0.75\textheight}{\includegraphics{'.$g4p_config['g4p_path'].'/cache/'.$_SESSION['genea_db_nom'].'/objets/'.$a_media->file.'}}\end{center}'."\n");
    else
        fwrite($latex, '\begin{center}Fichier introuvable : <<\,'.$g4p_config['g4p_path'].'/cache/'.$_SESSION['genea_db_nom'].'/objets/'.$a_media->file.'\,>>\end{center}'."\n");
    fwrite($latex, '\end{footnotesize}'."\n");
    fwrite($latex, '\end{boite}'."\n");
}

function g4p_latex_write_indi($g4p_indi)
{
    global $latex, $g4p_langue, $g4p_lien_def; 
    
    fwrite($latex, "\n\hypertarget{I".$g4p_indi->indi_id."}{}\n");
    fwrite($latex, "\section*{".$g4p_indi->prenom.' '.$g4p_indi->nom."}\n");
    fwrite($latex, "\index{".$g4p_indi->nom."!".$g4p_indi->prenom."}\n");
    fwrite($latex, '\subsection*{Etat civil}'."\n");
    fwrite($latex, '\begin{description}'."\n");
    fwrite($latex, '\item[Id] '.number_format($g4p_indi->indi_id, 0, ',', ' ')."\n");
    fwrite($latex, '\item[Nom] '.$g4p_indi->nom."\n");
    fwrite($latex, '\item[Prénom] '.$g4p_indi->prenom."\n");
    if(!empty($g4p_indi->sexe))
        fwrite($latex, '\item[Sexe] '.$g4p_indi->sexe."\n");
    if(!empty($g4p_indi->npfx))
        fwrite($latex, '\item[Npfx] '.$g4p_indi->npfx."\n");
    if(!empty($g4p_indi->givn))
        fwrite($latex, '\item[Givn] '.$g4p_indi->givn."\n");
    if(!empty($g4p_indi->nick))
        fwrite($latex, '\item[Surnom] '.$g4p_indi->nick."\n");
    if(!empty($g4p_indi->spfx))
        fwrite($latex, '\item[Spfx] '.$g4p_indi->spfx."\n");
    if(!empty($g4p_indi->nsfx))
        fwrite($latex, '\item[Nsfx] '.$g4p_indi->nsfx."\n");
    fwrite($latex, '\end{description}'."\n");
    

    if(!empty($g4p_indi->events))
    {
        fwrite($latex, "\n");
        fwrite($latex, '\subsection*{Évènements}'."\n");
        //fwrite($latex, '\begin{description}'."\n");
        foreach($g4p_indi->events as $g4p_a_ievents)
            g4p_latex_write_event($g4p_a_ievents);
        //fwrite($latex, '\end{description}'."\n");
    }

    if(!empty($g4p_indi->attributs))
    {
        fwrite($latex, "\n");
        fwrite($latex, '\subsection*{Attributs}'."\n");
        //fwrite($latex, '\begin{description}'."\n");
        foreach($g4p_indi->attributs as $g4p_a_ievents)
            g4p_write_attributs($g4p_a_ievents);
        //fwrite($latex, '\end{description}'."\n");
    }

    if(isset($g4p_indi->familles))
    {
        foreach($g4p_indi->familles as $g4p_a_famille)//affiche tous les mariages
        {
            if(!empty($g4p_a_famille->husb->nom) and !empty($g4p_a_famille->wife->nom))
                $tmp='Famille '.$g4p_a_famille->husb->nom.' -- '.$g4p_a_famille->wife->nom;
            elseif(!empty($g4p_a_famille->husb->nom))
                $tmp='Famille '.$g4p_a_famille->husb->nom;
            elseif(!empty($g4p_a_famille->wife->nom))
                $tmp='Famille '.$g4p_a_famille->wife->nom;
            else
                $tmp='Famille';
            
            fwrite($latex, "\n");
            fwrite($latex, '\subsection*{'.$tmp.'}'."\n");
            //fwrite($latex, "\begin{leftbar}"."\n");
            if(!empty($g4p_a_famille->timestamp))
                fwrite($latex, '\chandate{'.sprintf($g4p_langue['sys_function_mariage_chan'],g4p_strftime($g4p_langue['date_complete'], strtotime($g4p_a_famille->timestamp))),'}'."\n");
                
            if(!empty($g4p_a_famille->husb->indi_id) and $g4p_indi->indi_id!=$g4p_a_famille->husb->indi_id)
                $conjoint='husb';
            elseif(!empty($g4p_a_famille->wife) and $g4p_indi->indi_id!=$g4p_a_famille->wife->indi_id)
                $conjoint='wife';
            else
                $conjoint=false;
      
            fwrite($latex, '\begin{description}'."\n");
            fwrite($latex, '\item[Conjoint] ');
            if($conjoint!==false)
                fwrite($latex, g4p_latex_link_nom($g4p_a_famille->$conjoint)."\n");
            else
                fwrite($latex, 'inconnu(e)'."\n");
            fwrite($latex, '\end{description}'."\n");

            if(!empty($g4p_a_famille->events))
            {
                fwrite($latex, "\n");
                fwrite($latex, '\subsubsection*{Évènements}'."\n");
                //fwrite($latex, '\begin{description}'."\n");
                foreach($g4p_a_famille->events as $g4p_a_ievents)
                    g4p_latex_write_event($g4p_a_ievents);
                //fwrite($latex, '\end{description}'."\n");
            }

            //les enfants des mariages
            if(isset($g4p_a_famille->enfants))
            {
                fwrite($latex, "\n");
                fwrite($latex, '\subsubsection*{Enfants issus de l\'union}'."\n");
                fwrite($latex, '\begin{itemize}'."\n");
                foreach($g4p_a_famille->enfants as $g4p_a_enfant)
                {
                    fwrite($latex, '\item '.$g4p_a_enfant['rela_type'].' '.g4p_latex_link_nom($g4p_a_enfant['indi'])."\n");
                }
                fwrite($latex, '\end{itemize}'."\n\n");
            }

            if(!empty($g4p_a_famille->sources))
                foreach($g4p_a_famille->sources as $a_source)
                    g4p_latex_write_source($a_source);
            if(!empty($g4p_a_famille->notes))
                foreach($g4p_a_famille->notes as $a_note)
                    g4p_latex_write_note($a_note);

            //if ($_SESSION['permission']->permission[_PERM_MULTIMEDIA_])
            //    g4p_affiche_multimedia(@$g4p_a_famille->medias, $g4p_a_famille->id,'familles');
            //fwrite($latex, '\end{leftbar}'."\n");
        }
    }

    // les parents
    //echo '<pre>'; print_r($g4p_indi->parents);
    if(!empty($g4p_indi->parents))
    {
        foreach($g4p_indi->parents as $g4p_a_parent)
        {
            if(empty($g4p_a_parent->rela_type))
                $g4p_a_parent->rela_type='BIRTH';
            fwrite($latex, "\n");
            fwrite($latex, '\subsection*{Parents '.str_replace(array_keys($g4p_lien_def),array_values($g4p_lien_def),$g4p_a_parent->rela_type).'}'."\n");
            fwrite($latex, '\begin{itemize}'."\n");
            if(isset($g4p_a_parent->pere))
                fwrite($latex, '\item '.g4p_latex_link_nom($g4p_a_parent->pere)."\n");
            else
                fwrite($latex, '\item '.$g4p_langue['index_parent_inconnu']."\n");

            if(isset($g4p_a_parent->mere))
                fwrite($latex, '\item '.g4p_latex_link_nom($g4p_a_parent->mere)."\n");
            else
                fwrite($latex, '\item '.$g4p_langue['index_parent_inconnu']."\n");
            fwrite($latex, '\end{itemize}'."\n");
        }
    }

    //if($g4p_config['show_ext_rela'])
    //    g4p_relations_avancees($g4p_indi->indi_id);

    //g4p_affiche_asso(@$g4p_indi->asso, $g4p_indi->indi_id,'indi');
    //g4p_affiche_event_temoins(@$g4p_indi->temoins['events']);

    if(!empty($g4p_indi->sources))
        foreach($g4p_indi->sources as $a_source)
            g4p_latex_write_source($a_source);
    if(!empty($g4p_indi->notes))
        foreach($g4p_indi->notes as $a_note)
            g4p_latex_write_note($a_note);

    //if ($_SESSION['permission']->permission[_PERM_MULTIMEDIA_])
    //    g4p_affiche_multimedia(@$g4p_indi->multimedia, $g4p_indi->indi_id,'indi');
    
    //dessin
    fwrite($latex, "\n\n".'\subsection*{Arbre}'."\n");
    fwrite($latex, '\begin{center}\fbox{\limitbox{15cm}{25cm}{\begin{tikzpicture}'."\n");
    fwrite($latex, '\node (moi) {'.$g4p_indi->prenom.' '.$g4p_indi->nom.'}'."\n");
    
    if(isset($g4p_indi->familles))
    {
        $test=false;
        foreach($g4p_indi->familles as $g4p_a_famille)//affiche tous les mariages
        {
            if(isset($g4p_a_famille->enfants))
            {
                foreach($g4p_a_famille->enfants as $g4p_a_enfant)
                {
                    if(!$test)
                        fwrite($latex, '[edge from parent fork down,sibling distance=2.5cm]'."\n");
                    $test=true;
                    fwrite($latex, 'child {node [text width=2.3cm,text centered] {\footnotesize '.g4p_latex_link_prenom($g4p_a_enfant['indi']).'}}'."\n");
                }
            }
        }
    }
    fwrite($latex, ';'."\n");
    
    if(!empty($g4p_indi->parents))
    {
        foreach($g4p_indi->parents as $g4p_a_parent)
        {
            if($g4p_a_parent->rela_type=='BIRTH' or $g4p_a_parent->rela_type=='')
            {
                if(isset($g4p_a_parent->pere))
                {
                    fwrite($latex, '\node (P) [above=of moi,xshift=3cm,text width=3cm,text centered] {'.g4p_latex_link_nom($g4p_a_parent->pere,'sansdate').'} edge [->] (moi);'."\n");
                    $gp=g4p_load_indi_infos($g4p_a_parent->pere->indi_id);
                    if(!empty($gp->parents))
                    {
                        foreach($gp->parents as $g4p_a_gparent)
                        {
                            if($g4p_a_gparent->rela_type=='BIRTH' or $g4p_a_gparent->rela_type=='')
                            {
                                if(isset($g4p_a_gparent->pere))
                                    fwrite($latex, '\node (GP-P) [above=of P,xshift=2cm,text width=3cm,text centered] {'.g4p_latex_link_nom($g4p_a_gparent->pere,'sansdate').'} edge [->] (P);'."\n");
                                if(isset($g4p_a_gparent->mere))
                                    fwrite($latex, '\node (GM-P) [above=of P,xshift=-2cm,text width=3cm,text centered] {'.g4p_latex_link_nom($g4p_a_gparent->mere,'sansdate').'} edge [->] (P);'."\n");
                            }
                        }
                    }
                }

                if(isset($g4p_a_parent->mere))
                {
                    fwrite($latex, '\node (M) [above=of moi,xshift=-3cm,text width=3cm,text centered] {'.g4p_latex_link_nom($g4p_a_parent->mere,'sansdate').'} edge [->] (moi);'."\n");
                    $gp=g4p_load_indi_infos($g4p_a_parent->mere->indi_id);
                    if(!empty($gp->parents))
                    {
                        foreach($gp->parents as $g4p_a_gparent)
                        {
                            if($g4p_a_gparent->rela_type=='BIRTH' or $g4p_a_gparent->rela_type=='')
                            {
                                if(isset($g4p_a_gparent->pere))
                                    fwrite($latex, '\node (GP-M) [above=of M,xshift=2cm,text width=3cm,text centered] {'.g4p_latex_link_nom($g4p_a_gparent->pere,'sansdate').'} edge [->] (M);'."\n");
                                if(isset($g4p_a_gparent->pere))
                                    fwrite($latex, '\node (GM-M) [above=of M,xshift=-2cm,text width=3cm,text centered] {'.g4p_latex_link_nom($g4p_a_gparent->mere,'sansdate').'} edge [->] (M);'."\n");
                            }
                        }
                    }
                }
            }
        }
    }
    /*
   %grand parents
   \node	 (GPP)	[above=of P,xshift=2cm]			{GP-P}  edge [->] (P);
   \node	 (GMP)	[above=of P,xshift=-2cm]			{GM-P}  edge [->] (P);
   \node	 (GPM)	[above=of M,xshift=2cm]			{GP-M}  edge [->] (M);
   \node	 (GMM)	[above=of M,xshift=-2cm]			{GM-M}  edge [->] (M);*/
    fwrite($latex,'\end{tikzpicture}}}\end{center}'."\n");
}

?>