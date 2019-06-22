<?php
    class Shop {
        var $params,
            $whereParams;

        function __construct($params) {

            require_once('../general/ActiveRecord.php');

            $this->params = json_decode($params, true);
            $this->conn = (new ActiveRecord('market_items'))->getDB();
            $needParam = ['amount', 'value', 'name', 'assetid', 'classid', 'icon'];
            $this->basicQuery = 'SELECT `'.implode('`, `', $needParam).'` FROM `market_items_'.$this->params[appid].'`';
            $this->basicQuery .= ' WHERE `lang` = "'.$this->params[lang].'" ';

            $this->whereParams = [];
            foreach ($this->params as $key => $param) {
                if($key != 'pageNumber' && $key != 'less' && $key != 'more' && $key != 'LIKE' && $key != 'amountPage' && $param != '' && $key != 'appid' && $key != 'lang' && $key != 'desc') {
                    $this->whereParams[$key] = $param;
                }
            }
        }

        private function getQuery() {
            $query = '';
            foreach ($this->whereParams as $key => $param) {
                $query .= ' AND `'.$this->conn->real_escape_string($key).'` = "'.$this->conn->real_escape_string($param).'"';
            }
            if($this->params['less'] != '') {
                foreach ($this->params['less'] as $key => $less) {
                    if (is_numeric($less)) {
                        $query .= ' AND `' . $this->conn->real_escape_string($key) . '` < "' . $this->conn->real_escape_string($less) . '"';
                    }
                }
            }
            if($this->params['more'] != '') {
                foreach ($this->params['more'] as $key => $more) {
                    if (is_numeric($more)) {
                        $query .= ' AND `' . $this->conn->real_escape_string($key) . '` > "' . $this->conn->real_escape_string($more) . '"';
                    }
                }
            }
            if($this->params['LIKE'] != '') {
                foreach ($this->params['LIKE'] as $key => $like) {
                    if ($like != '') {
                        $query .= ' AND `' . $this->conn->real_escape_string($key) . '` LIKE "%' . $this->conn->real_escape_string($like) . '%"';
                    }
                }
            }

            return $query;
        }

        private function getItems() {
            $query = $this->getQuery();
            $query .= ' ORDER BY `value`';
            if($this->params['desc'] != '') { $query .= ' DESC'; }
            $query .= ' LIMIT '.(($this->params['pageNumber']-1)*40).', '.($this->params['pageNumber']*40);
            $fetchQuery = $this->conn->query($this->basicQuery.$query);
            $fetchItems = [];
            while($resObj = $fetchQuery->fetch_assoc())
            {
                $fetchItems[] = $resObj;
            }

            return $fetchItems;
        }

        private function getAmountPage() {
            return ceil($this->conn->query($this->basicQuery.$this->getQuery())->num_rows/40);
        }

        function getObject() {
            return ['amountPage' => $this->getAmountPage(), 'marketItems' => $this->getItems()];
        }
    }

    if(!isset($_POST['params'])) exit('Fatal error.');
    $shop = new Shop($_POST['params']);
    echo json_encode($shop->getObject());