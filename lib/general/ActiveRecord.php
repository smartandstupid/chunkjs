<?php
    //
    // class ActiveRecord v2 Alfa
    //

    class ActiveRecord
    {
        private $db;
        private $result;

        public $tableName;

        var $resObj;
        var $isertId;

        function __construct($tableName)
        {
            if(phpversion() < 5)
            {
                $this->db = mysql_connect('localhost', 'proorder', '');
                mysql_select_db('proorder_pawnshop', $this->db);

                mysql_query("SET NAMES 'utf8");
                mysql_query("SET CHARACTER SET 'utf8'");
            }
            else
            {
                $this->db = new mysqli('localhost', 'proorder', 'W25o07w19a56', 'proorder_pawnshop');
                if($this->db->connect_errno)
                {
                    echo 'alert("Ошибка соединения с базой данных: '.$this->db->connect_error.'")';
                    return false;
                }
                $this->db->query("SET NAMES 'utf8");
                $this->db->query("SET CHARACTER SET 'utf8'");
            }
            $this->tableName = $tableName;
        }

        function getDB()
        {
            return $this->db;
        }

        function save($arr)
        {
            $query = 'INSERT INTO `'.$this->tableName.'` (';
            foreach ($arr as $key => $val)
            {
                if($val === reset($arr))
                {
                    $query .= '`'.$key.'`';
                }
                else
                {
                    $query .= ', `'.$key.'`';
                }
            }
            $query .= ') VALUES(';
            foreach ($arr as $val)
            {
                $q = $this->db->real_escape_string($val) ? $this->db->real_escape_string($val) : mysqli_real_escape_string($this->db, $val);
                if($val === reset($arr)) {
                    $query .= '"'.$q.'"';
                } else {
                    $query .= ', "'.$q.'"';
                }
            }
            $query .= ')';

            if(phpversion() < 5) {
                $this->result = mysql_query($query);
                $this->isertId = mysqli_insert_id($this->db);
            } else {
                if(!$this->result = $this->db->query($query))
                {
                    echo 'alert("Ошибка выполнения запроса к базе: '.$this->db->error.'")';
                    return false;
                }
                $this->isertId = $this->db->insert_id;
                return true;
            }
        }

        function query($arr, $params, $isArr = false, $order = '')
        {
            $query = 'SELECT ';
            if(is_array($arr))
            {
                foreach ($arr as $val)
                {
                    $q = $this->db->real_escape_string($val) ? $this->db->real_escape_string($val) : mysqli_real_escape_string($this->db, $val);
                    if($val === reset($arr))
                    {
                        $query .= '`'.$q.'`';
                    }
                    else
                    {
                        $query .= ', `'.$q.'`';
                    }
                }
            }
            else
                $query .= '*';
            $query .= ' FROM `'.$this->tableName.'`';

            if(!empty($params))
            {
                $query .= ' WHERE ';

                foreach ($params as $key => $val)
                {
                    $q = $this->db->real_escape_string($val) ? $this->db->real_escape_string($val) : mysqli_real_escape_string($this->db, $val);
                    if($val === reset($params))
                    {
                        $query .= '`'.$key.'` = "'.$q.'"';
                    }
                    else
                    {
                        $query .= ' AND `'.$key.'` = "'.$q.'"';
                    }
                }
            }

            if($order != '')
            {
                $query .= ' ORDER BY `'.$order.'`';
            }

            if(phpversion() < 5) {
                $this->result = mysql_query($query);
            } else {
                if(!$this->result = $this->db->query($query))
                {
                    echo 'alert("Ошибка выполнения запроса к базе: '.$this->db->error.'")';
                    return false;
                }
            }
            if(phpversion() < 5)
            {
                return $this->resObj = mysql_fetch_assoc($this->result);
            } else {
                if($isArr) {
                    while($resObj = $this->result->fetch_assoc())
                    {
                        $this->resObj[] = $resObj;
                    }
                    return $this->resObj;
                } else {
                    return $this->resObj = $this->result->fetch_assoc();
                }
            }
        }
        //
        // $dbObj = new ActiveRecord('table_name');
        // $resObj = $dbObj->query(array('column_name'), array('where who' => 'where value'));
        // echo $resObj['column_name'];
        //

        function update($arr, $params)
        {
            $query = 'UPDATE `'.$this->tableName.'` SET ';

            foreach ($arr as $key => $val) {
                $q = $this->db->real_escape_string($val) ? $this->db->real_escape_string($val) : mysqli_real_escape_string($this->db, $val);
                if($val === reset($arr))
                {
                    $query .= '`'.$key.'`="'.$q.'"';
                }
                else
                {
                    $query .= ', `'.$key.'`="'.$q.'"';
                }
            }

            $query .= ' WHERE ';
            foreach ($params as $key => $val) {
                $q = $this->db->real_escape_string($val) ? $this->db->real_escape_string($val) : mysqli_real_escape_string($this->db, $val);
                if($val === reset($params))
                {
                    $query .= '`'.$key.'` = "'.$q.'"';
                }
                else
                {
                    $query .= ' AND `'.$key.'` = "'.$q.'"';
                }
            }

            if(phpversion() < 5) {
                $this->result = mysql_query($query);
            } else {
                if(!$this->result = $this->db->query($query))
                {
                    echo 'alert("Ошибка выполнения запроса к базе: '.$this->db->error.'")';
                    return false;
                }
            }
            return true;
        }

        function delete()
        {

        }
    }

?>
