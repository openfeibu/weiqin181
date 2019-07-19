<?PHP namespace Slj123Tongcheng\model; defined('IN_IA') || die('Access Denied');

class Goods
{
    public function __construct()
    {
    }
    public static function getById($ID)
    {
        return SQL::one("
SELECT *
FROM `@#__M_goods`
WHERE `id` = :id;
UPDATE `@#__M_goods`
SET
  `today_counts` = 0,
  `lasttime` = UNIX_TIMESTAMP()
WHERE `id` = :id
  && `lasttime` <= UNIX_TIMESTAMP(CURDATE());", array( ':id' => intval($ID) ));
    }
}
