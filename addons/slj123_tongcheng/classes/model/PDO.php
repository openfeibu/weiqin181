<?PHP namespace Slj123Tongcheng\model; defined('IN_IA') || die('Access Denied');

class PDO {
    public static function db() {
        static $db;
        if(empty($db)) {
            if($GLOBALS['_W']['config']['db']['slave_status'] == true && !empty($GLOBALS['_W']['config']['db']['slave'])) {
                load()->classs('slave.db');
                $db = new SlaveDb('master');
            } else {
                load()->classs('db');
                if(empty($GLOBALS['_W']['config']['db']['master'])) {
                    $GLOBALS['_W']['config']['db']['master'] = $GLOBALS['_W']['config']['db'];
                    $db = new _DB($GLOBALS['_W']['config']['db']);
                } else {
                    $db = new _DB('master');
                }
            }
        }
        return $db;
    }

    public static function query($sql, $params = array()) {
        return self::db()->query($sql, $params);
    }


    public static function fetchcolumn($sql, $params = array(), $column = 0) {
        return self::db()->fetchcolumn($sql, $params, $column);
    }

    public static function fetch($sql, $params = array()) {
        return self::db()->fetch($sql, $params);
    }

    public static function fetchall($sql, $params = array(), $keyfield = '') {
        return self::db()->fetchall($sql, $params, $keyfield);
    }


    public static function get($tablename, $condition = array(), $fields = array()) {
        return self::db()->get($tablename, $condition, $fields);
    }

    public static function getall($tablename, $condition = array(), $fields = array(), $keyfield = '', $orderby = array(), $limit = array()) {
        return self::db()->getall($tablename, $condition, $fields, $keyfield, $orderby, $limit);
    }

    public static function getslice($tablename, $condition = array(), $limit = array(), &$total = null, $fields = array(), $keyfield = '', $orderby = array()) {
        return self::db()->getslice($tablename, $condition, $limit, $total, $fields, $keyfield, $orderby);
    }

    public static function getcolumn($tablename, $condition = array(), $field) {
        return self::db()->getcolumn($tablename, $condition, $field);
    }


    public static function update($table, $data = array(), $params = array(), $glue = 'AND') {
        return self::db()->update($table, $data, $params, $glue);
    }


    public static function insert($table, $data = array(), $replace = FALSE) {
        return self::db()->insert($table, $data, $replace);
    }


    public static function delete($table, $params = array(), $glue = 'AND') {
        return self::db()->delete($table, $params, $glue);
    }


    public static function insertid() {
        return self::db()->insertid();
    }


    public static function begin() {
        self::db()->begin();
    }


    public static function commit() {
        self::db()->commit();
    }


    public static function rollback() {
        self::db()->rollBack();
    }


    public static function debug($output = true, $append = array()) {
        return self::db()->debug($output, $append);
    }

    public static function run($sql) {
        return self::db()->run($sql);
    }


    public static function fieldexists($tablename, $fieldname = '') {
        return self::db()->fieldexists($tablename, $fieldname);
    }

    public static function fieldmatch($tablename, $fieldname, $datatype = '', $length = '') {
        return self::db()->fieldmatch($tablename, $fieldname, $datatype, $length);
    }

    public static function indexexists($tablename, $indexname = '') {
        return self::db()->indexexists($tablename, $indexname);
    }


    public static function fetchallfields($tablename){
        $fields = self::db()->fetchall("DESCRIBE {$tablename}", array(), 'Field');
        $fields = array_keys($fields);
        return $fields;
    }


    public static function tableexists($tablename){
        return self::db()->tableexists($tablename);
    }

}