<?php
namespace Vendor;

/**
 * Created by PhpStorm.
 * User: YD-YF-20150908004-1
 * Date: 2018/10/17
 * Time: 13:52
 */
class DB
{
    /**
     * @var \PDOStatement
     */
    protected $pdoStatement;
    protected $strGroupBy = '';
    protected $strOrderBy = '';
    protected $paramWhere = [];
    protected $strWhere = '';

    /**
     * @var \PDO
     */
    public $pdoObject;

    private $config;

    public function __construct($config)
    {
        $this->config = $config;
        $result = $this->connect();
        if(!$result)
        {
            throw new \Exception("mysql connect error\n");
        }
    }

    public function connect()
    {
        $config = $this->config;
        $dsn = sprintf('mysql:dbname=%s;host=%s;port=%s;charset=%s', $config["database_name"],
            $config["ip"], $config["port"], $config["charset"]);
        $obj = new \PDO($dsn, $config["username"], $config["password"], [\PDO::ATTR_PERSISTENT => true]);
        $this->pdoObject = $obj;
        return $obj;
    }


    /**
     * Description:
     * @param $sql
     * @param array $params
     * @return bool
     * @throws \Exception
     */
    public function query($sql, array $params = [])
    {
        $this->pdoStatement = $this->pdoObject->prepare($sql);
        $this->paramWhere = [];
        $result = $this->pdoStatement->execute($params);
        //如果mysql线程长期不活跃，可能处于自动关闭状态，我们要对错误码进行判断
        $errcode = $this->pdoStatement->errorCode();
        if(!$result &&  ($errcode == 2006 || $errcode == 2013))
        {
            if(!$this->connect())
            {
                throw new \Exception("mysql connect error\n");
            }
            $this->query($sql,$params);
        }
        return $result;
    }

    public function fetchAll($sql, array $params = [])
    {
        $ret = $this->query($sql, $params);
        $this->paramWhere = [];
        return $ret ? $this->pdoStatement->fetchAll(\PDO::FETCH_ASSOC) : false;
    }

    public function fetchRow($sql, array $params = [])
    {
        $ret = $this->query($sql, $params);
        $this->paramWhere = [];
        return $ret ? $this->pdoStatement->fetch(\PDO::FETCH_ASSOC) : false;
    }

    public function fetchColumn($sql, array $params = [], $colIndex = 0)
    {
        $ret = $this->query($sql, $params);
        $this->paramWhere = [];
        return $ret ? $this->pdoStatement->fetchAll(\PDO::FETCH_COLUMN, $colIndex) : false;
    }

    public function fetchField($sql, array $params = [], $colIndex = 0)
    {
        $this->paramWhere = [];
        $ret = $this->query($sql, $params);
        if ($ret) {
            $row = $this->pdoStatement->fetch(\PDO::FETCH_BOTH);
            return isset($row[$colIndex]) ? $row[$colIndex] : $row[0];
        } else {
            return false;
        }
    }

    public function getLastSql()
    {
        return $this->pdoStatement ? $this->pdoStatement->queryString : false;
    }

    public function getLastError()
    {
        return $this->pdoStatement ? $this->pdoStatement->errorInfo()[2] : false;
    }

    protected function getAffectRows()
    {
        return $this->pdoStatement ? $this->pdoStatement->rowCount() : false;
    }

    public function getLastInsertId()
    {
        return $this->pdoObject->lastInsertId();
    }

    public function insert($tableName, array $datas)
    {
        $sqlKey = [];
        $sqlVal = [];
        foreach ($datas as $dataKey => $dataVal) {
            $sqlKey[] = $dataKey;
            $sqlVal[] = $dataVal;
        }
        if (empty($sqlKey)) {
            return false;
        }
        $sqlKeys = implode('`,`', $sqlKey);
        $sqlVals = implode("','", $sqlVal);
        $sql = "INSERT INTO `$tableName` (`$sqlKeys`) VALUES ('$sqlVals')";
        return $this->query($sql);
    }

    protected function update($tableName, array $datas)
    {
        $sqlDatas = [];
        foreach ($datas as $dataKey => $dataVal) {
            $sqlDatas[] = "`$dataKey` = '$dataVal'";
        }
        $sqlData = implode(',', $sqlDatas);
        $where = $this->strWhere ? " WHERE $this->strWhere" : '';
        $this->strWhere = '';
        $sql = "UPDATE `$tableName` SET $sqlData $where";
        return $this->query($sql);
    }

    protected function delete($tableName)
    {
        $where = $this->strWhere ? " WHERE $this->strWhere" : '';
        $this->strWhere = '';
        $sql = "DELETE FROM `$tableName` $where";
        return $this->query($sql,$this->paramWhere);
    }

    public function select($tableName, array $columns = [], $limit = '')
    {
        if ($columns) {
            $columns = array_map(function ($val) {
                return strpos($val, '(') !== false ? $val : "`$val`";
            }, $columns);
            $field = implode(',', $columns);
        } else {
            $field = '*';
        }
        if (!$limit) {
            $limit = '';
        } elseif (preg_match('#^\d+(,\d+)?$#', $limit)) {
            $limit = $limit ? " LIMIT $limit" : '';
        } else {
            throw new \Exception('limit not allowed');
        }
        $where = $this->strWhere ? " WHERE $this->strWhere" : '';
        $this->strWhere = '';
        $groupBy = $this->strGroupBy ? " GROUP BY $this->strGroupBy" : '';
        $this->strGroupBy = '';
        $orderBy = $this->strOrderBy ? " ORDER BY $this->strOrderBy" : '';
        $this->strOrderBy = '';
        $sql = "SELECT $field FROM `$tableName` {$where}{$groupBy}{$orderBy}{$limit}";
        return $this->fetchAll($sql,$this->paramWhere);
    }

    protected function selectAuto($tableName, array $columns = [], $limit = '')
    {
        $ret = $this->select($tableName, $columns, $limit);
        if (count($ret) == 1 and count($ret[0]) == 1) {
            return array_pop($ret[0]);
        } elseif (count($ret) == 1) {
            return $ret[0];
        } elseif (count($ret) > 0 and count($ret[0]) == 1) {
            $retArray = [];
            foreach ($ret as $item) {
                $retArray[] = array_pop($item);
            }
            return $retArray;
        } else {
            return $ret;
        }
    }

    public function find($tableName)
    {
        $where = $this->strWhere ? " WHERE $this->strWhere" : '';
        $this->strWhere = '';
        $sql = "SELECT * FROM `$tableName` $where";
        return $this->fetchRow($sql, $this->paramWhere);
    }

    public function where($key, $value, $operator = '=')
    {
        if (in_array(strtolower($operator), ['=', '>', '<', '>=', '<=', '!=', '<>', 'like'])) {
            $this->strWhere and $this->strWhere .= ' AND ';
            $this->strWhere .= "`$key` $operator ?";
            $this->paramWhere[] = $value;
        } elseif (strtolower($operator) == 'in') {
            $inStr = implode('","', (array)$value);
            $this->strWhere and $this->strWhere .= ' AND ';
            $this->strWhere .= "`$key` $operator (\"$inStr\")";
            $this->paramWhere[] = $value;
        } else {
            throw new \Exception('operator not allowed');
        }
        return $this;
    }

    protected function orwhere($key, $value, $operator = '=')
    {
        if (in_array(strtolower($operator), ['=', '>', '<', '>=', '<=', '!=', '<>', 'like'])) {
            $this->strWhere and $this->strWhere .= ' OR ';
            $this->strWhere .= "`$key` $operator '$value'";
        } elseif (strtolower($operator) == 'in') {
            $inStr = implode('","', (array)$value);
            $this->strWhere and $this->strWhere .= ' OR ';
            $this->strWhere .= "`$key` $operator (\"$inStr\")";
        } else {
            throw new \Exception('operator not allowed');
        }
        return $this;
    }

    protected function groupBy($key, $order = 'ASC')
    {
        if (!in_array(strtoupper($order), ['ASC', 'DESC'])) {
            throw new \Exception('order not allowed');
        }
        $this->strGroupBy and $this->strGroupBy .= ' , ';
        $this->strGroupBy .= "`$key` $order";
        return $this;
    }



    protected function orderBy($key, $order = 'ASC')
    {
        if (!in_array(strtoupper($order), ['ASC', 'DESC'])) {
            throw new \Exception('order not allowed');
        }
        $this->strOrderBy and $this->strOrderBy .= ' , ';
        $this->strOrderBy .= "`$key` $order";
        return $this;
    }

    protected function selectPage($key, $order = 'ASC')
    {
        if (!in_array(strtoupper($order), ['ASC', 'DESC'])) {
            throw new \Exception('order not allowed');
        }
        $this->strOrderBy and $this->strOrderBy .= ' , ';
        $this->strOrderBy.="`$key` $order";
        return $this;
    }


    protected function inTransaction()
    {
        return $this->pdoObject->inTransaction();
    }

    protected function transaction()
    {
        return $this->pdoObject->beginTransaction();
    }

    protected function rollback()
    {
        return $this->pdoObject->rollBack();
    }

    protected function commit()
    {
        return $this->pdoObject->commit();
    }

}

