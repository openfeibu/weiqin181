<?PHP namespace Slj123Tongcheng\model; defined('IN_IA') || die('Access Denied');

class SQL
{
    public function __construct()
    {
    }
    public static function all($SQL, $PARAMS = array(), $keyfield = '')
    {
        $SQL = static::transTableName($SQL);
        return PDO::fetchall($SQL, $PARAMS, $keyfield);
    }
    public static function one($SQL, $PARAMS = array())
    {
        $SQL = static::transTableName($SQL);
        return PDO::fetch($SQL, $PARAMS);
    }
    public static function column($SQL, $PARAMS = array(), $COLUMN = 0)
    {
        $SQL = static::transTableName($SQL);
        return PDO::fetchcolumn($SQL, $PARAMS, $COLUMN);
    }
    public static function query($SQL, $PARAMS = array())
    {
        $SQL = static::transTableName($SQL);
        return PDO::query($SQL, $PARAMS);
    }


    public static function update($table, $data = array(), $params = array(), $glue = 'AND') {
        return PDO::update(ModuleName() . '_' . $table, $data, $params, $glue);
    }


    public static function insert($table, $data = array(), $replace = FALSE) {
        return PDO::insert(ModuleName() . '_' . $table, $data, $replace);
    }


    public static function delete($table, $PARAMS = array(), $glue = 'AND') {
        return PDO::delete(ModuleName() . '_' . $table, $PARAMS, $glue);
    }


    public static function get($tablename, $condition = array(), $fields = array()) {
        return PDO::get(ModuleName() . '_' . $tablename, $condition, $fields);
    }

    public static function getall($tablename, $condition = array(), $fields = array(), $keyfield = '', $orderby = array(), $limit = array()) {
        return PDO::getall(ModuleName() . '_' . $tablename, $condition, $fields, $keyfield, $orderby, $limit);
    }

    public static function getslice($tablename, $condition = array(), $limit = array(), &$total = null, $fields = array(), $keyfield = '', $orderby = array()) {
        return PDO::getslice(ModuleName() . '_' . $tablename, $condition, $limit, $total, $fields, $keyfield, $orderby);
    }

    public static function getcolumn($tablename, $condition = array(), $field) {
        return PDO::getcolumn(ModuleName() . '_' . $tablename, $condition, $field);
    }


    public static function insertid() {
        return PDO::insertid();
    }
    private static function transTableName($SQL)
    {
        $SQL = preg_replace_callback('/`@#__M_(.+?)`/', function ($matches) {
            return tablename(ModuleName() . '_' . $matches[1]);
        }, $SQL);
        $SQL = preg_replace_callback('/`@#__U_(.+?)`/', function ($matches) {
            return tablename($matches[1]);
        }, $SQL);
        return "-- BORDER \n /* BORDER */\n" . $SQL;
    }


    ///////////////////////////
    public static function parseIntArray(&$Array)
    {
        if (!isset($Array)) return;
        if (is_array($Array)) {
            array_walk($Array, array('self', 'intvalArray'), 'fruit');
            $Array = implode(',', $Array);
        } else {
            $Array = explode(',', $Array);
        }
    }
    private static function intvalArray(&$item, $key, $param)
    {
        $item = intval($item);
    }
    ///////////////////////////


    ///////////////////////////
    public static function Condition($TableAlias, $Filter)
    {
        $Condition = '';
        foreach ($Filter as $key => $value) {
            $Condition .= is_numeric($key) ? ' AND (' . preg_replace('/`([^`]+)`/', $TableAlias . '.`\1`', $value) . ')'
                : " AND {$TableAlias}.`{$key}` = '{$value}'";
        }
        return $Condition;
    }
    public static function ColumnsValuesFromDataMatrix($Data)
    {
        $Culumns = '';
        $Values = array();
        foreach ($Data as $index => $row) {
            empty($Culumns) && ($Culumns = '`' . implode('`,`', array_keys($row)) . '`');
            $Values[] = '(\'' . implode('\',\'', $row) . '\')';
        }
        $Values = implode(',', $Values);
        return array('Columns' => $Culumns, 'Values' => $Values);
    }
    ///////////////////////////

    ///////////////////////////
    public static function insertOrUpdate($tableName, $data, $plus = array())
    {
        $fields = array();
        $params = array();
        $update = array();
        foreach($data as $key => $value) {
            $fields[] = "`{$key}` = :{$key}";
            $params[':' . $key] = $value;
            empty($plus) && ($update[] = "`{$key}` = VALUES(`{$key}`)");
        }
        if(!empty($plus)) {
            foreach ($plus as $key => $value) {
                $fields[] = "`{$key}` = :{$key}";
                $params[':' . $key] = $value;
                $update[] = "`{$key}` = :{$key} + VALUES(`{$key}`)";
            }
        }
        $fields = implode(',', $fields);
        $update = implode(',', $update);
        return self::query("
INSERT INTO `@#__M_{$tableName}`
SET {$fields}
ON DUPLICATE KEY UPDATE {$update}", $params);
    }
    ///////////////////////////
}
