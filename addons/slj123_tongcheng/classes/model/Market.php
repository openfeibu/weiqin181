<?PHP namespace Slj123Tongcheng\model; defined('IN_IA') || die('Access Denied');

class Market
{
    public function __construct()
    {
    }
    public static function getById($ID)
    {
        return SQL::one("
SELECT *
FROM `@#__M_area`
WHERE `id` = :id", array( ':id' => intval($ID)));
    }
    public static function getByIds($IDs)
    {
        SQL::parseIntArray($IDs);
        SQL::parseIntArray($IDs);
        if(empty($IDs)){
            return null;
        }
        return SQL::all("
SELECT *
FROM `@#__M_area`
WHERE `id` IN({$IDs})", array(), 'id');
    }
}
