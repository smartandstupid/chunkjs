<!doctype html>
<html lang="<?=LANG ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?=$l['Заголовок'] ?></title>
    <link rel="stylesheet/less" type="text/css" href="<?=DIR?>styles.less" />
    <link rel="stylesheet/less" type="text/css" href="<?=DIR?>index.less" />
    <link rel="stylesheet/less" type="text/css" href="<?=DIR?>profile.less" />
    <link rel="stylesheet/less" type="text/css" href="<?=DIR?>pawnshop.less" />
    <link rel="stylesheet/less" type="text/css" href="<?=DIR?>shop.less" />
    <link rel="stylesheet/less" type="text/css" href="<?=DIR?>howitwork.less" />
    <link rel="stylesheet/less" type="text/css" href="<?=DIR?>bonus.less" />
    <link rel="stylesheet/less" type="text/css" href="<?=DIR?>faq.less" />
    <link rel="stylesheet/less" type="text/css" href="<?=DIR?>feedback.less" />
    <link rel="stylesheet/less" type="text/css" href="<?=DIR?>head.less" />
    <script src="<?=DIR?>less.min.js" type="text/javascript"></script>
    <?php
        require('lib/general/Auth.php');
        $auth = new Auth();
        if($auth->type == 'admin') {
            echo '<script type="text/javascript" src="'.DIR.'lib/contentEdit/content-tools.min.js"></script>';
            echo '<link rel="stylesheet" type="text/css" href="'.DIR.'lib/contentEdit/content-tools.min.css">';
        }
    ?>
</head>
<body>
    <?php //require('preloader.php') ?>
    <div class="scroll"><div id="backHead"></div>

        <div id="language">
            <a href="" id="lang-ru" class="<?php if(LANG == 'ru') echo 'active' ?>" onclick="chooseLang(event)">ru</a>
            <a href="" id="lang-eng" class="<?php if(LANG == 'en') echo 'active' ?>" onclick="chooseLang(event)">eng</a>
            <div id="lang-back" class="lis"></div>
        </div>
        <?php
        $steam = array(
            'key'=>'9BCC9FDEC634CF05CBDB7C4415CE1B63',
            'redirect'=>'http://'.$_SERVER['HTTP_HOST'].'/'.LANG.'/steam-auth/');
        $steam_url = "https://steamcommunity.com/openid/login?openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0&openid.mode=checkid_setup&openid.return_to=".urldecode($steam["redirect"])."%3Fstate=steam&openid.realm=".urldecode($steam["redirect"])."&openid.ns.sreg=http%3A%2F%2Fopenid.net%2Fextensions%2Fsreg%2F1.1&openid.claimed_id=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.identity=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select";
        ?>
        <?php
        if($auth->auth) {
            echo '<div id="steamauth" onclick="openPage(event, \'profile\')"><a href="http://pawnshop-market.com/'.LANG.'/profile">'.$auth->name.'</a><div style="background-image:url(\''.$auth->avatar.'\'); background-size: 100% auto;"><div><span id="balanceEl">'.$auth->balance.'</span>&#8381;</div></div></div>';
        } else {
            echo '<div id="steamauth" onclick="window.location.href=\''.$steam_url.'\'"><a href="'.$steam_url.'">'.$l['Войти'].'</a><div></div></div>';
        }
        ?>

        <div id="menu">
            <div id="aboveMenu">
                <div id="menu-howitwork" class="menu <?php if(PAGE=='how-it-work') echo 'active'?>" onclick="openPage(event, 'how-it-work')">
                    <div class="icons40 icon-howitwork"></div>
                    <a href="http://pawnshop-market.com/<?=LANG?>/how-it-work"><?=$l['Как это работает'] ?></a>
                </div>
                <div id="menu-bonus" class="menu <?php if(PAGE=='bonus') echo 'active'?>" onclick="openPage(event, 'bonus')">
                    <div class="icons40 icon-bonus"></div>
                    <a href="http://pawnshop-market.com/<?=LANG?>/bonus"><?=$l['Бонусы'] ?></a>
                </div>
                <a href="http://pawnshop-market.com/<?=LANG?>" onclick="openPage(event, '')"><img src="<?=DIR?>images/logo.png" id="logo" /></a>
                <div id="menu-faq" class="menu <?php if(PAGE=='faq') echo 'active'?>" onclick="openPage(event, 'faq')">
                    <div class="icons40 icon-faq"></div>
                    <a href="http://pawnshop-market.com/<?=LANG?>/faq">F.A.Q.</a>
                </div>
                <div id="menu-feedback" class="menu <?php if(PAGE=='feedback') echo 'active'?>" onclick="openPage(event, 'feedback')">
                    <div class="icons40 icon-feedback"></div>
                    <a href="http://pawnshop-market.com/<?=LANG?>/feedback"><?=$l['Обратная связь'] ?></a>
                </div>
            </div>
            <div id="underMenu">
                <div id="menu-shop" class="menu <?php if(PAGE=='sell-skins') echo 'active'?>" onclick="openPage(event, 'sell-skins')">
                    <div class="icons40 icon-csgo"></div>
                    <a href="http://pawnshop-market.com/<?=LANG?>/sell-skins" class="under-shop"><?=$l['Продать скины'] ?> <b>CS GO</b></a>
                </div>
                <div id="menu-shop" class="menu <?php if(PAGE=='shop') echo 'active'?>" onclick="openPage(event, 'shop')">
                    <div class="icons40 icon-shop"></div>
                    <a href="http://pawnshop-market.com/<?=LANG?>/shop"><?=$l['Магазин'] ?></a>
                </div>
                <div id="menu-faq" class="menu <?php if(PAGE=='pawnshop') echo 'active'?>" onclick="openPage(event, 'pawnshop')">
                    <div class="icons40 icon-pawnshop"></div>
                    <a href="http://pawnshop-market.com/<?=LANG?>/pawnshop" class="under-shop"><?=$l['Ломбард'] ?></a>
                </div>
                <div id="menu-feedback" class="menu <?php if(PAGE=='sell-things') echo 'active'?>" onclick="openPage(event, 'sell-things')">
                    <div class="icons40 icon-dota"></div>
                    <a href="http://pawnshop-market.com/<?=LANG?>/sell-things" class="under-shop"><?=$l['Продать вещи'] ?> <b>Dota 2</b></a>
                </div>
            </div>
        </div>