<?PHP namespace Slj123Tongcheng\model; defined('IN_IA') || die('Access Denied');

class Store
{
    public function __construct()
    {
    }
    public static function getById($ID)
    {
        return SQL::one("
SELECT *
FROM `@#__M_stores`
WHERE `id` = :id", array( ':id' => intval($ID)));
    }
}
