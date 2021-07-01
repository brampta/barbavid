<?php

$text[0] = 'Hébergement vidéo H.264';
//$text[1000] = 'Mettre&#160;en&#160;ligne';
$text[1000] = 'ajouter&#160;une&#160;vidéo';
$text[1001] = 'Faire&#160;du&#160;$$$!';


//upload
$text[1] = 'Fichier';
$text[2] = 'Titre';
$text[3] = 'Description';
//$text[4] = 'Mettre en ligne';
$text[4] = 'Ajouter une vidéo';
$text[5] = 'Suspension';
$text[6] = 'Enregistrer';
$text[7] = 'Éffacer le vidéo associé à cette mise en ligne';
$text[8] = 'Je suis certain';
$text[9] = 'Maximum '.$maxmb.' Méga-octets';
$text[10] = 'Maximum 100 caractères';
$text[11] = 'Maximum 3000 caractères';
$text[12] = 'Mise en ligne en cours. Ne fermez pas cette page.';
$text[13] = 'Êtes vous sur que vous voulez quitter cette page? Un vidéo est en train d\'être mis en ligne. Il sera perdu si vous quittez la page avant qu\'il soit terminé.';
$text[14] = 'Mettez un vidéo en ligne:';
$text[15] = 'Adresse de votre popup';
$text[16] = 'ie http://domaine.com/pageweb. Maximum 1024 caractères';
$text[17] = 'Si vous spécifiez une Adresse de popup, la page que vous spécifiez sera ouverte dans une nouvelle fenêtre lorsque quelqu\'un regardera votre vidéo, si vous mettez des annonces sur cette page, vous pouvez faire de l\'argent avec votre vidéo. Si vous ne voulez pas avoir de popup sur votre vidéo, laissez ce champ vide. S\'il-vous-plaît ne mettez pas de mauvaises choses dans vos popups. Pas de pornographie, pas de virus ou malware pas de popups excessifs. Les violations de cette règle resulteront en retrait de vos popups ou suspension du compte.';


//uploads log
$text[201] = 'pas de titre';
$text[202] = 'Vos dernières mise en ligne';

function explain_expiration($days)
{ return 'Note, un vidéo expirera s\'il n\'est pas regardé pendant ' . $days . ' jours.'; }

//video
$text[101] = 'Ce vidéo n\'a pas encore fini d\'être encodé, réessayez plus tard.';
$text[102] = 'Désolé, ce vidéo a été suspendu.';
$text[103] = 'raison de la suspension: ';
$text[104] = 'Le vidéo a été supprimé.';
$text[105] = 'Il est coûteux d\'héberger du vidéo. S\'il-vous-plaît cliquez le bouton çi-dessous pour prouver que vous êtes en vie.';
$text[106] = 'Je suis en vie!';
$text[107] = 'Position dans la file d\'attente de l\'encodage: ';

function explain_encode_progress($step, $totaltime, $currenttime)
{
    if ($step == 'startup')
    { $rezu = 'Le processus de l\'encodage est en train de s\'initializer.'; } else if ($step == 'pass1')
    {
        $prct = ($currenttime / $totaltime) * 100;
        $prct = floor($prct * 100) / 100;
        $rezu = 'Le processus de l\'encodage est en train d\'encoder la passe 1 de 2. ' . $currenttime . ' secondes sur ' . $totaltime . ' on été encodées (' . $prct . '%).';
    } else if ($step == 'pass2')
    {
        $prct = ($currenttime / $totaltime) * 100;
        $prct = floor($prct * 100) / 100;
        $rezu = 'Le processus de l\'encodage est en train d\'encoder la passe 2 de 2. ' . $currenttime . ' secondes sur ' . $totaltime . ' on été encodées (' . $prct . '%).';
    } else if ($step == 'split')
    { $rezu = 'Le partie principale du processus de l\'encodage est terminé. Le système va maintenant diviser le fichier au besoin et créer des onglets. Le vidéo devrait être prêt à regarder dans quelques secondes.'; }
    return $rezu;
}

function showplayingchunk($chunknum, $totchunks)
{
    $rezu = 'vous regardez le morçeau ' . $chunknum . ' de ' . $totchunks;
    return $rezu;
}

$text[108] = 'regarder le morçeau précédent';
$text[109] = 'regarder le morçeau suivant';
$text[110] = 'retourner à la liste des morçeaux';
$text[112] = array('1', '2', '3', '4', '5');
$text[111] = 'fermer cette annonce';

$text[113] = 'Regarder';
$text[114] = 'Enregistrer';

$text[115] = 'L\'encodage du vidéo n\'a pas réussi. Le vidéo a été mis de côté temporairement et l\'encodage échoué sera enquêté aussi-tôt que possible. Nous sommes désolé pour les inconvients que çela pourrait vous poser.';

$text[116]='L\'hébergement de vidéo est coûteux,<br />
vous devez prouver que vous êtes humain<br />
en cliquant sur le bouton çi-dessous.';
$text[117]='Vérification de votre humanité';
$text[118]='Procéder vers le vidéo';
function showpleasewaitxseconds($seconds)
{
    $rezu = 'S\'il vous plaît attendez <span id="waittimezz">' . $seconds . '</span> secondes';
    return $rezu;
}
$text[119]='Utilisez les boutons numérotés qui sont dans le bas du lecteur lorsqu\'il est arrêté pour naviguer entre les parties.';



$text[120]='tag &#60;video> mp4:';
$text[121]='Votre naviagateur ne supporte pas le tag &#60;video>.';
$text[122]='téléchargement direct mp4:';
$text[123]='télécharger';
$text[124]='retourner à la selection des morceaux.';
$text[125]='cliquez ici pour la version flash';
$text[126]='cliquez ici pour la version mobile';



//new HTML5/Flash player texts
$text[150]='télécharger';
$text[151]='intégrer';
$text[152]='dupliquer';
$text[153]='utilisez ce code pour intégrer ce video à votre page';
$text[154]='selectionné';



//maketimus
$text[300] = "jan";
$text[301] = "fév";
$text[302] = "mar";
$text[303] = "avr";
$text[304] = "mai";
$text[305] = "juin";
$text[306] = "juillet";
$text[307] = "aoû";
$text[308] = "sep";
$text[309] = "oct";
$text[310] = "nov";
$text[311] = "déc";


//info page
$text[2000] = 'information';
$text[2001] = 'Barbavid Hébergement de Vidéo H.264';
$text[2002] = 'Système révolutionnaire, Barbavid utilise le nouveau codec vidéo H.264 dans Flash pour servir du vidéo de haute qualité utilisant peu de bande passante.';
$text[2003] = 'Simple, anonyme, gratuit, illimité. Conquiers le vidéomonde avec Barbavid.';
$text[2004] = 'Programmes nécessaires:';
//$text[2005] = 'Dernière version de Flash nécéssaire.';
$text[2005] = 'se procurer Flash:';
$text[2006] = 'obtenez flash';
$text[2007] = 'Contact:';
$text[2008] = 'anonymat';
$text[2009] = 'À propos de Barbavid';
$text[2010] = 'Anonymat à Barbavid';
$text[2011] = 'Barbavid n\'enregistre pas les addresses IP des utilisateurs qui mettent des vidéos en ligne ou les téléchargent. Barbavid n\'enregistre aucune information personelle.';

function make_writeto_string($email_link)
{ return $email_link; }

//duplicate upload
$text[3000] = 'Créez une copie d\'un vidéo';
$text[3001] = 'sans titre';
$text[3002] = 'La copie a été créé.';
$text[3003] = 'original';
$text[3004] = 'copie';

function explain_expiration2($days)
{ return 'Note, un vidéo (autant original que copie) expirera s\'il n\'est pas regardé pendant ' . $days . ' jours.'; }

function explain_badvid($upload, $nameforlink)
{ return 'Il n\'est pas possible de créer une copie du vidéo <a href="/video/' . $upload . '" target="_blank">' . $nameforlink . '</a> parcequ\'il est soit effacé ou suspendu.'; }

$text[3005] = 'Erreur, le titre ne peut pas être vide.';
$text[3006] = 'Erreur, le nombre que vous avez tapé ne correspondait pas au nombre qui était montré, réessayez de nouveau.';

function explain_dupvid($upload, $nameforlink)
{ return 'Créez une copie de <a href="/video/' . $upload . '" target="_blank">' . $nameforlink . '</a>.'; }

$text[3007] = 'nouveau titre';
$text[3008] = 'nouvelle description';
$text[3011] = 'nouveau popup';
$text[3009] = 'retapez çe nombre';
$text[3010] = 'créer la copie';


//make money page
$text[4000] = 'Faites de l\'argent avec vos vidéos!';
$text[4001] = 'Barbavid vous offre une façon de faire de l\'argent en ligne en mettant des vidéos populaires en ligne et en montrant vos propres popups a nos visiteurs qui regardent votre vidéo!';
$text[4002] = 'Pour faire cela, simplement placer l\'adresse d\'une page sous votre contrôle dans le champ "Adresse de votre popup" du formulaire de mise en ligne de vidéo.
Vous pouvez ensuite mettre des annonces et des compteurs sur cette page pour gagner de l\'argent avec le trafic que vos vidéos reçoivent.';

//mobile first page
$text[5002] = 'pro-mobile'; //link to this page
$text[5000] = 'Fait pour les appareils mobiles dabord!';
$text[5001] = 'Depuis déjà plusieurs années, chez Barbavid nous avons été l\'un des rares hébergeurs vidéo à s\'assurer que notre contenu fonctionne bien sur votre ordinateur de bureau aussi bien que sur vos appareils mobiles.
Mais depuis 2014 nous avons fait notre priorité de servir du contenu qui fonctionne bien sur les appareils mobiles, tout en améliorant son aspect sur les ordinateurs de bureaux.
Notre redesign de 2014 visait à s\'assurer que toutes nos pages apparaissent bien sur les appareils mobiles, qu\'elles s\'adaptent et tirent pleinement avantage de tous les different formats de navigateurs.';










?>
