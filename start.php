<?php

//$mStart = microtime(true);
session_start();
$_SESSION['classes'] = [];
require('lib/general/Directory.php');

if(PAGE == '' || PAGE == 'index' || PAGE == 'bonus' || PAGE == 'pawnshop' || PAGE == 'faq' ||
    PAGE == 'profile' || PAGE == 'shop' || PAGE == 'how-it-work' || PAGE == 'feedback' ||
    PAGE == 'sell-skins' || PAGE == 'sell-things')
{
    $pageName = PAGE == '' ? 'index' : PAGE;
    session_start();
    $_SESSION['lastPage'] = $_SERVER['REQUEST_URI'];
    $l = array_merge(
        json_decode(file_get_contents('lib/lang/'.LANG.'/'.strtolower($pageName).'.json'), true),
        json_decode(file_get_contents('lib/lang/'.LANG.'/head.json'), true)
    );
    require('lib/chunk/header.php');
    echo '<div id="content"></div>';
    require('lib/chunk/content-right.php');
    require('lib/scripts/index.php');
}
else if(PAGE == 'anytest' || PAGE == 'anytest2')
{
    require(PAGE.'.php');
}
else if(PAGE == 'steam-auth')
{
    require('pages/steam-auth.php');
}
else
{
    header("HTTP/1.0 404 Not Found");
    require('pages/404.php');
}

echo '</body></html>';
//$mEnd = microtime(true);
//file_put_contents('measurement.txt', ' Время выдачи результата без кл. pawnshop: '.($mEnd-$mStart), FILE_APPEND);

?>