<?PHP namespace Slj123Tongcheng\model; defined('IN_IA') || die('Access Denied');

class Order
{
    public function __construct()
    {
    }
    public static function submit()
    {
        $getActiveItemsInCartForOrderCreate = Cart::getActiveItemsInCartForOrderCreate();
        $getActiveMarketID = Member::getActiveMarketID();

        $TotalPrice = $TotalCount = 0;

        foreach($getActiveItemsInCartForOrderCreate as $key => $value) {
            $TotalPrice += $value['TotalPrice'];
            $TotalCount += $value['total'];
        }

        $MarketOrderSN = 'MK'
            . str_pad($GLOBALS['_W']['uniacid'], 2, '0', STR_PAD_LEFT) . 'M'
            . str_pad($getActiveMarketID, 4, '0', STR_PAD_LEFT)
            . str_pad($TotalPrice * 100, 6, '0', STR_PAD_LEFT)
            . date('YmdHis') . str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);


        SQL::insertOrUpdate('order', array(
            'weid' => $GLOBALS['_W']['uniacid'],
            'market_id' => $getActiveMarketID,
            'from_user' => $GLOBALS['_W']['openid'],
            'ordersn' => $MarketOrderSN,
            'totalnum' => $TotalCount,
            'totalprice' => $TotalPrice,
            'meal_time' => trim($GLOBALS['_GPC']['meal_time']),
            'goodsprice' => $TotalPrice,
            'dateline' => TIMESTAMP,
        ));

        $MarketOrderID = SQL::insertid();
        $StoreOrderIDs = array();

        foreach($getActiveItemsInCartForOrderCreate as $key => $value) {
            $StoreOrderSN = 'MK'
                . str_pad($GLOBALS['_W']['uniacid'], 2, '0', STR_PAD_LEFT) . 'S'
                . str_pad($value['storeid'], 4, '0', STR_PAD_LEFT)
                . str_pad($value['TotalPrice'] * 100, 6, '0', STR_PAD_LEFT)
                . date('YmdHis') . str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);

            SQL::insertOrUpdate('order', array(
                'weid' => $GLOBALS['_W']['uniacid'],
                'parent_order_id' => $MarketOrderID,
                'market_id' => $getActiveMarketID,
                'storeid' => $value['storeid'],
                'from_user' => $GLOBALS['_W']['openid'],
                'ordersn' => $StoreOrderSN,
                'totalnum' => $value['total'],
                'totalprice' => $value['TotalPrice'],
                'meal_time' => trim($GLOBALS['_GPC']['meal_time']),
                'goodsprice' => $value['TotalPrice'],
                'dateline' => TIMESTAMP,
            ));
            $StoreOrderID = SQL::insertid();
            $StoreOrderIDs[] = $StoreOrderID;
            SQL::insertOrUpdate('order_goods', array(
                'weid' => $GLOBALS['_W']['uniacid'],
                'storeid' => $value['storeid'],
                'orderid' => $StoreOrderID,
                'goodsid' => $value['goodsid'],
                'price' => $value['Price'],
                'total' => $value['total'],
                'dateline' => TIMESTAMP,
            ));
        }
        Cart::deleteActiveItemsInCartForOrderAlreadyCreated();
        return array(
            '$getActiveItemsInCartForOrderCreate' => $getActiveItemsInCartForOrderCreate,
            '$getActiveMarketID' => $getActiveMarketID,
            '$TotalPrice' => $TotalPrice,
            '$TotalCount' => $TotalCount,
            '$MarketOrderSN' => $MarketOrderSN,
            '$GLOBALS[\'_GPC\'][\'remark\']' => $GLOBALS['_GPC']['remark'],
            '$GLOBALS[\'_GPC\'][\'meal_time\']' => $GLOBALS['_GPC']['meal_time'],
            '$GLOBALS[\'_W\'][\'openid\']' => $GLOBALS['_W']['openid'],
            '$MarketOrderID' => $MarketOrderID,
            '$StoreOrderIDs' => $StoreOrderIDs,
        );
    }
}
