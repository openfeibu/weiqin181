<?PHP namespace Slj123Tongcheng\model; defined('IN_IA') || die('Access Denied');

class Cart
{
    public function __construct()
    {
    }
    public static function update($MarketID, $StoreID, $GoodsID, $ChangeCount)
    {
        $MarketID = intval($MarketID);
        $StoreID = intval($StoreID);
        $GoodsID = intval($GoodsID);
        $ChangeCount = intval($ChangeCount);


        $store = Store::getById($StoreID);
        if ($store['is_rest'] != 1) {
            $result['msg'] = '商家休息中,暂不接单';
            message($result, '', 'ajax');
        }

        //查询商品是否存在
        $goods = Goods::getById($GoodsID);
        if (empty($goods)) {
            $result['msg'] = '没有相关商品';
            message($result, '', 'ajax');
        }
        if ($goods['counts'] == 0) {
            $result['msg'] = '该商品已售完!';
            message($result, '', 'ajax');
        }
        //查询购物车有没该商品
        $cart = self::getGoodsExists($GoodsID);

        if ($goods['counts'] > 0) {
            $count = $goods['counts'] - $goods['today_counts'];
            if ($count <= 0) {
                $result['msg'] = '该商品已售完!!';
                message($result, '', 'ajax');
            }
            if (!empty($cart)) {
                if ($cart['total'] < $ChangeCount) {
                    if ($ChangeCount > $count) {
                        $result['msg'] = '该商品已没库存!!';
                        message($result, '', 'ajax');
                    }
                }
            } else {
                if ($ChangeCount > $count) {
                    $result['msg'] = '该商品已没库存!!';
                    message($result, '', 'ajax');
                }
            }
        }


        //$iscard = $this->get_sys_card($from_user);
        //$price = $goods['marketprice'];
        //if ($iscard == 1 && !empty($goods['memberprice'])) {
        //    $price = $goods['memberprice'];
        //}


        empty($cart) && SQL::query("UPDATE `@#__M_goods` SET `subcount` = `subcount` + 1 WHERE `id` = :id", array( ':id' => $GoodsID));


        SQL::insertOrUpdate('cart', array(
            'weid' => $GLOBALS['_W']['uniacid'],
            'market_id' => $MarketID,
            'storeid' => $StoreID,
            'goodsid' => $GoodsID,
            'goodstype' => $goods['pcate'],
            //'price' => $goods['marketprice'],
            'packvalue' => $goods['packvalue'],
            'from_user' => $GLOBALS['_W']['fans']['openid'],
        ), array(
            'total' => $ChangeCount,
        ));

        $totalcount = 0;
        $totalprice = 0;

        $Count = SQL::column("
SELECT SUM(`total`)
FROM `@#__M_cart`
WHERE `from_user` = :openid", array(':openid' => $GLOBALS['_W']['fans']['openid']));
        //$cart = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " WHERE  storeid=:storeid AND from_user=:from_user AND weid=:weid", array(':storeid' => $storeid, ':from_user' => $from_user, ':weid' => $weid));
//
        //$cart_html = '<ul>';
        //foreach ($cart as $key => $value) {
        //    $goods_t = pdo_fetch("SELECT * FROM " . tablename($this->table_goods) . " WHERE id = :id LIMIT 1 ", array(':id' => $value['goodsid']));
        //    $cart[$key]['goodstitle'] = $goods_t['title'];
        //    $totalcount = $totalcount + $value['total'];
        //    $totalprice = $totalprice + $value['total'] * $value['price'];
//
        //    if ($value['total'] > 0) {
        //        $cart_html .= '<li dishid="'.$value['goodsid'].'">';
        //        $cart_html .= '<div class="cart-item-name">'.$goods_t['title'].'</div>';
        //        $cart_html .= '<div class="cart-item-price">¥<font>'.$value['price'].'</font></div>';
        //        $cart_html .= '<div class="cart-item-num">';
        //        $cart_html .= '<i class="cart-item-add"></i>';
        //        $cart_html .= '<span>'.$value['total'].'</span>';
        //        $cart_html .= '<i class="cart-item-jj"></i>';
        //        $cart_html .= '</div>';
        //        $cart_html .= '</li>';
        //    }
        //}
        //$cart_html .= '</ul>';
        //$result['totalprice'] = $totalprice;
        //$result['totalcount'] = $totalcount;
        //$result['cart'] = $cart_html;
        //$result['code'] = 0;
        message(array( 'status' => 1, 'totalcount' => $Count, 'msg' => '已成功添加该商品到购物车！！' ), '', 'ajax');
    }
    public static function getGoodsExists($GoodsID)
    {
        return SQL::one("
SELECT *
FROM `@#__M_cart`
WHERE `id` = :id
  && `from_user` = :openid", array(
            ':id' => intval($GoodsID),
            ':openid' => $GLOBALS['_W']['fans']['openid'],
        ));
    }
    public static function getAllExists()
    {
        return SQL::all("
SELECT `id`, `market_id`, `storeid`, `goodsid`, `total`, `checked`, IFNULL(NULLIF(`price`, 0), (
  SELECT `marketprice`
  FROM `@#__M_goods` AS `G`
  WHERE `G`.`id` = `C`.`goodsid`
  LIMIT 1
)) AS `Price`, (
  SELECT `title`
  FROM `@#__M_goods` AS `G`
  WHERE `G`.`id` = `C`.`goodsid`
  LIMIT 1
) AS `GoodsTitle`, (
  SELECT `thumb`
  FROM `@#__M_goods` AS `G`
  WHERE `G`.`id` = `C`.`goodsid`
  LIMIT 1
) AS `Thumb`, (
  SELECT `unitname`
  FROM `@#__M_goods` AS `G`
  WHERE `G`.`id` = `C`.`goodsid`
  LIMIT 1
) AS `Unit`, (
  SELECT `title`
  FROM `@#__M_stores` AS `S`
  WHERE `S`.`id` = `C`.`storeid`
  LIMIT 1
) AS `StoreName`, (IFNULL(NULLIF(`price`, 0), (
  SELECT `marketprice`
  FROM `@#__M_goods` AS `G`
  WHERE `G`.`id` = `C`.`goodsid`
  LIMIT 1
)) * `total`
) AS `TotalPrice`
FROM `@#__M_cart` AS `C`
WHERE `from_user` = :openid", array( ':openid' => $GLOBALS['_W']['openid'] ));
    }
    public static function getJoinedMarketIDFromAllExists()
    {
        return SQL::column("
SELECT GROUP_CONCAT(`market_id`)
FROM `@#__M_cart`
WHERE `from_user` = :openid", array( ':openid' => $GLOBALS['_W']['openid'] ));
    }
    public static function getActiveItemsInCartForOrderCreate()
    {
        return SQL::all("
SELECT `id`, `market_id`, `storeid`, `goodsid`, `total`, `checked`, IFNULL(NULLIF(`price`, 0), (
  SELECT `marketprice`
  FROM `@#__M_goods` AS `G`
  WHERE `G`.`id` = `C`.`goodsid`
  LIMIT 1
)) AS `Price`, (
  SELECT `title`
  FROM `@#__M_goods` AS `G`
  WHERE `G`.`id` = `C`.`goodsid`
  LIMIT 1
) AS `GoodsTitle`, (
  SELECT `thumb`
  FROM `@#__M_goods` AS `G`
  WHERE `G`.`id` = `C`.`goodsid`
  LIMIT 1
) AS `Thumb`, (
  SELECT `unitname`
  FROM `@#__M_goods` AS `G`
  WHERE `G`.`id` = `C`.`goodsid`
  LIMIT 1
) AS `Unit`, (
  SELECT `title`
  FROM `@#__M_stores` AS `S`
  WHERE `S`.`id` = `C`.`storeid`
  LIMIT 1
) AS `StoreName`, (IFNULL(NULLIF(`price`, 0), (
  SELECT `marketprice`
  FROM `@#__M_goods` AS `G`
  WHERE `G`.`id` = `C`.`goodsid`
  LIMIT 1
)) * `total`
) AS `TotalPrice`
FROM `@#__M_cart` AS `C`
WHERE `from_user` = :openid
  && `checked` = 1
  && `market_id` = (
    SELECT `checked_market_id`
    FROM `@#__M_fans` AS `F`
    WHERE `F`.`from_user` = `C`.`from_user`
    LIMIT 1
  )", array( ':openid' => $GLOBALS['_W']['openid'] ));
    }
    public static function deleteActiveItemsInCartForOrderAlreadyCreated()
    {
        return SQL::query("
DELETE FROM `@#__M_cart`
WHERE `from_user` = :openid
  && `checked` = 1
  && `market_id` = (
    SELECT `checked_market_id`
    FROM `@#__M_fans` AS `F`
    WHERE `F`.`from_user` = :openid
    LIMIT 1
  )", array( ':openid' => $GLOBALS['_W']['openid'] ));
    }
}
