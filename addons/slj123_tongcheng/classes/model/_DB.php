<?PHP namespace Slj123Tongcheng\model; defined('IN_IA') || die('Access Denied');

class _DB extends \DB
{
    public function connect($name = 'master') {
        $this->cfg = pdo()->cfg;
        $name = 'master';
        $cfg = is_array($name) ? $name : $this->cfg[$name];
        empty($cfg) && die("The master database is not found, Please checking 'data/config.php'");
        $this->tablepre = $cfg['tablepre'];

        $options = array();
        if (class_exists('PDO')) {
            if (extension_loaded('pdo_mysql') && in_array('mysql', \PDO::getAvailableDrivers())) {
                $dbclass = '\PDO';
                $options = array(\PDO::ATTR_PERSISTENT => $cfg['pconnect']);
            } else {
                class_exists('_PDO') || (include IA_ROOT . '/framework/library/pdo/PDO.class.php');
                $dbclass = '_PDO';
            }
        } else {
            include IA_ROOT . '/framework/library/pdo/PDO.class.php';
            $dbclass = 'PDO';
        }
        $this->pdo = new $dbclass("mysql:dbname={$cfg['database']};host={$cfg['host']};port={$cfg['port']};charset={$cfg['charset']}", $cfg['username'], $cfg['password'], $options);
        //$this->pdo->setAttribute(pdo::ATTR_EMULATE_PREPARES, false);
        $this->pdo->exec("SET NAMES '{$cfg['charset']}';");
        $this->pdo->exec("SET sql_mode='';");
        is_string($name) && ($this->link[$name] = $this->pdo);


        //$this->logging($sql);
    }
}