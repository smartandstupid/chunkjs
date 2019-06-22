<?php
    require_once('ActiveRecord.php');

    class Auth
    {
        var $auth, $name, $steamid, $avatar, $balance, $bonus_perc, $bonus_level;

        function __construct()
        {
            if(isset($_COOKIE['steamid']) || isset($_SESSION['steamid'])) {
                $this->steamid = $_SESSION['steamid'] ? $_SESSION['steamid'] : $_COOKIE['steamid'];
                $dbObj = new ActiveRecord('nomodx_users');
                $resObj = $dbObj->query(array('name', 'login', 'avatar', 'balance', 'bonuc_perc', 'bonus_level', 'type'), array('steamid' => $this->steamid));
                if (!$resObj) { exit(); }
                if ($resObj['login'] != '') {
                    $this->name = $resObj['name'] ? $resObj['name'] : $resObj['login'];
                    $this->avatar = $resObj['avatar'];
                    $this->type = $resObj['type'];
                    $this->balance = $resObj['balance'];
                    $this->bonus_perc = $resObj['bonus_perc'];
                    $this->bonus_level = $resObj['bonus_level'];

                    $this->auth = true;
                } else {
                    $this->auth = false;
                }
            } else {
                $this->auth = false;
            }
        }

        function update($params)
        {
            $dbObj = new ActiveRecord('nomodx_users');
            $resObj = $dbObj->update($params, array('steamid' => $this->steamid));
            if($resObj) { return true; }
            else { echo 'Error DataBase'; }
        }

        function getRefLink() {

        }

        function getReferrals() {

        }

        function getRefParent() {

        }

        function getBonusSumm() {

        }

        function getBonusFactor() {

        }

        function getPaid() {
            $db = (new ActiveRecord('tradeoffers_history'))->getDB();
            $nowDate = date('Y-m-d H:i:s');
            $resObj = $db->query("SELECT SUM(`summ`) AS 'paid' FROM `tradeoffers_history` WHERE `steamid` = '$this->steamid'");
            return $resObj['paid'];
        }

        function getPaidToday() {
            $db = (new ActiveRecord('tradeoffers_history'))->getDB();
            $nowDate = date('Y-m-d H:i:s');
            $resObj = $db->query("SELECT SUM(`summ`) AS 'paidToday' FROM `tradeoffers_history` WHERE `steamid` = '$this->steamid' AND `data` = '$nowDate'");
            return $resObj['paidToday'];
        }

        function getHistoryTrade() {
            $dbObj = new ActiveRecord('tradeoffers_history');
            $resObj = $dbObj->query([], array('steamid' => $this->steamid), true);
            return $resObj;
        }
    }
?>