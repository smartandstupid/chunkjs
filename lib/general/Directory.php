<?php

    $index = 2;

    $direction = explode('/', $_SERVER['REQUEST_URI']);

    if($direction[count($direction)-1] == '') array_pop($direction);

    if(!in_array($direction[$index-1], array('ru', 'en'))) header('Location: http://'.$_SERVER['HTTP_HOST'].'/ru'.$_SERVER['REQUEST_URI']);

    $direction = explode('/', $_SERVER['REQUEST_URI']);
    $dir;

    for($i = sizeof($direction); $i > $index; $i--)
    {
        $dir .= '../';
    }
    if($direction[$index] == '')
        $dir .= '../';

    define("DIR", $dir);
    define("LANG", $direction[$index-1]);
    define("PAGE", $direction[$index]);

?>