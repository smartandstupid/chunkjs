<?php

    session_start();
    define('DIR', $_POST['dir']);
    define('LANG', $_POST['lang']);
    $pageName = $_POST['page'] == '' ? 'index' : $_POST['page'];

    if($pageName == 'steam-auth')
    {
    }
    else
    {
        $fileName = '../lang/'.LANG.'/'.$pageName.'.json';
        if(is_file($fileName)) {
            $fl = json_decode(file_get_contents($fileName), true);
        }
        require('../scripts/'.$pageName.'.js.php');
    }

    function issetClass($className) {
        if(!in_array($className, $_SESSION['classes'])) {
            $_SESSION['classes'][] = $className;
            require('../scripts/classes/'.$className.'.js.php');
        }
        echo " window.openClass = new $className(); ";
    }

    function getShopItems() {
        require_once('../general/ActiveRecord.php');
        $conn = (new ActiveRecord('market_items'))->getDB();
        $needParam = ['amount', 'value', 'name', 'color', 'assetid', 'classid', 'icon'];
        $query = 'SELECT `'.implode('`, `', $needParam).'` FROM `market_items_csgo` WHERE `lang` = "'.LANG.'"';

        $countFetch = $conn->query($query);
        $countRows = $countFetch->num_rows;

        $query .= ' ORDER BY `value` LIMIT 40';

        $fetchQuery = $conn->query($query);
        $fetchItems = [];
        while($resObj = $fetchQuery->fetch_assoc())
        {
            $fetchItems[] = $resObj;
        }
        $returnObj = ['amountPage' => ceil($countRows/40), 'marketItems' => $fetchItems];
        echo json_encode($returnObj);
    }

    function getHTML($addr) {
        $file = file_get_contents($addr);
        $file = preg_replace_callback(
            '/\(\`(.*)\`\)/',
            function ($matches) {
                global $fl;
                return $fl[$matches[1]];
            },
            $file
        );
        return addslashes(str_replace(array("\r\n", "\r", "\n"), '', $file));
    }

    function writePage($pageObj) {
        for($key = 0; $key < count($pageObj); $key++) {
            echo $pageObj[$key]['start'];
            if($pageObj[$key]['in'] == '') {
                echo $pageObj[$key][LANG];
            } else {
                echo writeIn($pageObj[$key]['in'], $pageObj[$key][LANG]);
            }
            echo $pageObj[$key]['end'];
        }
    }

    function writeIn($in, $textTo) {
        $writeStr = '';
        // расстоние до предыдущего значения
        $backDelta = 0;
        //сортировавка массивка
        usort($in, function($a, $b){
            return ($a['pos'] - $b['pos']);
        });
        for($key = 0; $key < count($in); $key++) {
            $writeStr .= substr($textTo, $backDelta, $in[$key]['pos']-$backDelta);
            $writeStr .= $in[$key]['start'];
            if($in[$key]['in'] == '') {
                $writeStr .= $in[$key][LANG];
            } else {
                $writeStr .= writeIn($in[$key]['in'], $in[$key][LANG]);
            }
            $writeStr .= $in[$key]['end'];
            $backDelta = $in[$key]['pos'];
        }
        return $writeStr;
    }

    function getMassDescriptions() {
        require_once('../general/ActiveRecord.php');
        $conn = new ActiveRecord('market_desc_csgo');
        $fetch = $conn->query(['category_name', 'name'], ['lang' => LANG], true);
        $csgoMass = [];
        foreach ($fetch as $item) {
            $csgoMass[$item['category_name']][] = $item['name'];
        }
        $conn = new ActiveRecord('market_desc_dota');
        $fetch = $conn->query(['category_name', 'name'], ['lang' => LANG], true);
        $dotaMass = [];
        foreach ($fetch as $item) {
            $dotaMass[$item['category_name']][] = $item['name'];
        }
        return json_encode(['csgo' => $csgoMass, 'dota' => $dotaMass]);
    }

    function getBasketString() {
        return json_encode(getBasket());
    }

    function getBasket() {
        if(isset($_COOKIE['basket'])) {
            require_once('../general/ActiveRecord.php');
            $conn = new ActiveRecord('market_items_dota');
            $cBasket = json_decode($_COOKIE['basket'], true);
            usort($cBasket, function($a, $b){
                return ($a['appid'] - $b['appid']);
            });
            $mssRtrn = [];
            foreach($cBasket as $item) {
                if('market_items_'.$item['appid'] != $conn->tableName) {
                    $conn->tableName = 'market_items_csgo';
                }
                $iInfo = $conn->query(['assetid', 'icon', 'name', 'value', 'amount'], ['assetid' => $item['assetid'], 'lang' => LANG]);
                $iInfo['appid'] = $item['appid'];
                $mssRtrn[] = $iInfo;
            }
            return $mssRtrn;
        } else {
            return [];
        }
    }

?>