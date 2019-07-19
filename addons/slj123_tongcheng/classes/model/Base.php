<?PHP namespace Slj123Tongcheng\model; defined('IN_IA') || die('Access Denied');

class Base
{
    public function __construct()
    {
    }
    protected static function ModuleName() {
        return ModuleName();
    }
    protected static function ClassName($Name) {
        return preg_replace_callback('/_([a-z])/', function ($matches) {
            return strtoupper($matches[1]);
        }, '_' . self::ModuleName() . '_' . strtolower($Name));
    }
}
