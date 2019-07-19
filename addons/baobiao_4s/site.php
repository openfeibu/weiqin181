<?php
/**
 * 4s汽车城模块微站定义
 *
 * @author 爆表哥
 * @url
 */
defined('IN_IA') or exit('Access Denied');

require_once IA_ROOT . "/addons/baobiao_4s/wxapp.php";

class Baobiao_4sModuleSite extends WeModuleSite
{
    use BaseBaoBiaoGeModuleTrait;

    public function doWebSwiper()
    {
        global $_W;

        $ops = array('list', 'edit', 'save', 'delete', 'status');
        $op = $this->optionsFilter($ops);

        $id = $this->getIdParam();

        switch ($op) {
            case 'list':
                $data = $this->getDao('swiper')
                    ->orderBySort()
                    ->all();
                load()->func('tpl');
                include $this->template('swiper');
                break;
            case 'edit':
                if ($id) {
                    $data = $this->getDao('swiper')->find($id);
                }
                load()->func('tpl');
                include $this->template('swiper_edit');
                break;
            case 'save':
                $data = $this->getInputs(array(
                    'image',
                    'sort',
                    'status',
                    'type',
                    'meta',
                ));
                if ($id) {
                    $res = $this->getDao('swiper')->updateById($id, $data);
                } else {
                    $res = $this->getDao('swiper')->add($data);
                }
                $this->showActionMessage($res, 'swiper');
                break;
            case 'status':
                $data = $this->getInputs(array(
                    'status',
                ));
                $res = $this->getDao('swiper')->updateById($id, $data);
                $this->showActionMessageAjax($res);
                break;
            case 'delete':
                $res = $this->getDao('swiper')->deleteById($id);
                $this->showActionMessageAjax($res);
                break;
        }
    }

    public function doWebPhoto()
    {
        global $_W;

        $ops = array('list', 'edit', 'save', 'delete');
        $op = $this->optionsFilter($ops);

        $id = $this->getIdParam();

        switch ($op) {
            case 'list':
                $data = $this->getDao('photo')
                    ->setDefaultPagination()
                    ->all();

                $pager = $this->getPager('photo');

                foreach ($data as &$item1) {
                    $item1['images'] = json_decode($item1['images'], true);
                }
                load()->func('tpl');
                include $this->template('photo');
                break;
            case 'edit':
                if ($id) {
                    $data = $this->getDao('photo')->find($id);
                    $data['images'] = json_decode($data['images'], true);
                }
                load()->func('tpl');
                include $this->template('photo_edit');
                break;
            case 'save':
                $data = $this->getInputs(array(
                    'images',
                    'name',
                ));
                $data['images'] = json_encode($data['images']);
                if ($id) {
                    $res = $this->getDao('photo')->updateById($id, $data);
                } else {
                    $res = $this->getDao('photo')->add($data);
                }
                $this->showActionMessage($res, 'photo');
                break;
            case 'status':
                $data = $this->getInputs(array(
                    'status',
                ));
                $res = $this->getDao('photo')->updateById($id, $data);
                $this->showActionMessageAjax($res);
                break;
            case 'delete':
                $res = $this->getDao('photo')->deleteById($id);
                $this->showActionMessageAjax($res);
                break;
        }
    }

    public function doWebOrder()
    {
        global $_W;

        $ops = array('list', 'delete');
        $op = $this->optionsFilter($ops);

        $id = $this->getIdParam();

        switch ($op) {
            case 'list':
                $data = $this->getDao('order')
                    ->setDefaultPagination()
                    ->all();
                $pager = $this->getPager('order');
                load()->func('tpl');
                include $this->template('order');
                break;
            case 'delete':
                $res = $this->getDao('order')->deleteById($id);
                $this->showActionMessageAjax($res);
                break;
        }
    }

    public function doWebCar()
    {
        global $_W;

        $ops = array('list', 'edit', 'save', 'status', 'delete', 'copy');
        $op = $this->optionsFilter($ops);

        $id = $this->getIdParam();

        switch ($op) {
            case 'list':
                $data = $this->getDao('car')
                    ->setDefaultPagination()
                    ->setOrderby(array('sort desc', 'id desc'))
                    ->all();
                $pager = $this->getPager('car');
                $category = $this->getDao('category')
                    ->orderBySort()
                    ->all();
                $category = $this->arrayFlatten($category);
                load()->func('tpl');
                include $this->template('car');
                break;
            case 'edit':
                if ($id) {
                    $data = $this->getDao('car')->find($id);
                    $data['images'] = json_decode($data['images'], true);
                }
                $category = $this->getDao('category')
                    ->orderBySort()
                    ->all();
                $category = $this->arrayFlatten($category);
                load()->func('tpl');
                include $this->template('car_edit');
                break;
            case 'copy':
                if (!$id) {
                    message('参数错误，请重试', $this->createWebUrl('car'), 'error');
                }

                $data = $this->getDao('car')->find($id);
                unset($data['id']);
                $data['name'] = $data['name'] . ' 复制';
                $data['sort'] = $data['sort'] + 1;
                $res = $this->getDao('car')->add($data);

                message('复制车型成功，前往编辑车型', $this->createWebUrl('car'));

                break;
            case 'save':
                $data = $this->getInputs(array(
//                    'image',
                    'images',
                    'name',
                    'detail',
                    'price',
                    'guide_price',
                    'category_id',
                    'sort',
                    'parameter',
                    'model',
                    'output',
                    'seat',
                    'video',
//                    'status',
                ));

                $data['images'] = json_encode($data['images']);

                $url = $data['parameter'];
                if ($url) {
                    $spider = $this->getSpider();
                    if (!$spider->checkoutUrl($url)) {
                        message('汽车参数抓取地址非法，请按照要求输入', $this->createWebUrl('car'), 'error');
                    }
                    $parameter_data = $spider->getPcautoData($url);
                    $data['parameter_content'] = json_encode($parameter_data);
                } else {
                    $data['parameter_content'] = '';
                }

                if ($id) {
                    $res = $this->getDao('car')->updateById($id, $data);
                } else {
                    $res = $this->getDao('car')->add($data);
                }
                $this->showActionMessage($res, 'car');
                break;
            case 'status':
                $data = $this->getInputs(array(
                    'status',
                ));
                $res = $this->getDao('car')->updateById($id, $data);
                $this->showActionMessageAjax($res);
                break;
            case 'delete':
                $res = $this->getDao('car')->deleteById($id);
                $this->showActionMessageAjax($res);
                break;
        }
    }

    public function doWebCategory()
    {
        global $_W;

        $ops = array('list', 'edit', 'save', 'delete', 'status');
        $op = $this->optionsFilter($ops);

        $id = $this->getIdParam();

        switch ($op) {
            case 'list':
                $data = $this->getDao('category')
                    ->orderBySort()
                    ->all();
                load()->func('tpl');
                include $this->template('category');
                break;
            case 'edit':
                if ($id) {
                    $data = $this->getDao('category')->find($id);
                }
                load()->func('tpl');
                include $this->template('category_edit');
                break;
            case 'save':
                $data = $this->getInputs(array(
                    'image',
                    'name',
                    'sort',
                ));

                if ($id) {
                    $res = $this->getDao('category')->updateById($id, $data);
                } else {
                    $res = $this->getDao('category')->add($data);
                }
                $this->showActionMessage($res, 'category');
                break;
            case 'status':
                $data = $this->getInputs(array(
                    'status',
                ));
                $res = $this->getDao('category')->updateById($id, $data);
                $this->showActionMessageAjax($res);
                break;
            case 'delete':
                $res = $this->getDao('car')
                    ->setCondition(array('category_id' => $id))
                    ->find();
                if ($res) {
                    $this->errorMessageAjax('该分类下有内容，请先删除该分类下的内容再删除分类。');
                }
                $res = $this->getDao('category')->deleteById($id);
                $this->showActionMessageAjax($res);
                break;
        }
    }

    public function doWebSalesman()
    {
        global $_W;

        $ops = array('list', 'edit', 'save', 'delete');
        $op = $this->optionsFilter($ops);

        $id = $this->getIdParam();

        switch ($op) {
            case 'list':
                $data = $this->getDao('salesman')
                    ->orderBySort()
                    ->all();
                load()->func('tpl');
                include $this->template('salesman');
                break;
            case 'edit':
                if ($id) {
                    $data = $this->getDao('salesman')->find($id);
                }
                load()->func('tpl');
                include $this->template('salesman_edit');
                break;
            case 'save':
                $data = $this->getInputs(array(
                    'image',
                    'name',
                    'phone',
                    'sort',
                ));

                if ($id) {
                    $res = $this->getDao('salesman')->updateById($id, $data);
                } else {
                    $res = $this->getDao('salesman')->add($data);
                }
                $this->showActionMessage($res, 'salesman');
                break;
            case 'delete':
                $res = $this->getDao('salesman')->deleteById($id);
                $this->showActionMessageAjax($res);
                break;
        }
    }

    public function doWebSetting()
    {
        global $_W;

        if (checksubmit()) {
            $data = $this->getInputs(array(
                'name',
                'address',
                'longitude',
                'latitude',
                'about',
                'phone',
                'phone2',
                'register_price',
                'interest_rate',
                'tax_0',
                'tax_1',
                'tax_2',
                'tax_3',
                'tax_4',
                'tax_5',
                'tax_6',
//                'tech_support',
                'photo_section',
                'brand_section',
                'salesman_section',
                'location_section',
                'car_calculate',
                'hot_icon',
                'news_section',
                'banner',
                'logo',
                'photo_text',
                'brand_text',
                'recommend_text',
                'salesman_text',
                'company_text',
                'message_text',
                'detail_ad_text',
                'is_email_on',
                'email_from',
                'email_password',
                'email_to',
                'email_title',
                'shop_text',
                'contact_icon',
            ));

            $data = array(
                'info' => json_encode($data)
            );

            $setting = $this->getDao('setting')->find();
            if ($setting) {
                $res = $this->getDao('setting')->update($data);
            } else {
                $res = $this->getDao('setting')->add($data);
            }

            $this->showActionMessage($res, 'setting');
        }

        $data = $this->getDao('setting')->find();
        $data = json_decode($data['info'], true);
        load()->func('tpl');
        include $this->template('setting');

    }

    public function doWebSupport()
    {
        global $_W;

        if (checksubmit()) {
            $data = $this->getInputs(array('tech_support'));

            $setting = $this->getDao('setting')->find();
            if ($setting) {
                $res = $this->getDao('setting')->update($data);
            } else {
                $res = $this->getDao('setting')->add($data);
            }

            $this->showActionMessage($res, 'support');
        }

        $data = $this->getDao('setting')->find();
        load()->func('tpl');
        include $this->template('support');

    }

    public function doWebNode()
    {
        global $_W;

        $ops = array('list', 'edit', 'save', 'delete');
        $op = $this->optionsFilter($ops);

        $id = $this->getIdParam();

        switch ($op) {
            case 'list':
                $data = $this->getDao('node')
                    ->orderBySort()
                    ->all();
                load()->func('tpl');
                include $this->template('node');
                break;
            case 'edit':
                if ($id) {
                    $data = $this->getDao('node')->find($id);
                }
                load()->func('tpl');
                include $this->template('node_edit');
                break;
            case 'save':
                $data = $this->getInputs(array(
                    'name',
                    'sort',
                ));

                if ($id) {
                    $res = $this->getDao('node')->updateById($id, $data);
                } else {
                    $res = $this->getDao('node')->add($data);
                }
                $this->showActionMessage($res, 'node');
                break;
            case 'delete':
                $res = $this->getDao('news')
                    ->setCondition(array('node_id' => $id))
                    ->find();
                if ($res) {
                    $this->errorMessageAjax('该分类下有新闻，请先删除该分类下的新闻再删除分类。');
                }
                $res = $this->getDao('node')->deleteById($id);
                $this->showActionMessageAjax($res);
                break;
        }
    }

    public function doWebNews()
    {
        global $_W;

        $ops = array('list', 'edit', 'save', 'status', 'delete');
        $op = $this->optionsFilter($ops);

        $id = $this->getIdParam();

        switch ($op) {
            case 'list':
                $data = $this->getDao('news')
                    ->setDefaultPagination()
                    ->all();
                $pager = $this->getPager('news');
                $node = $this->getDao('node')
                    ->orderBySort()
                    ->all();
                $node = $this->arrayFlatten($node);
                load()->func('tpl');
                include $this->template('news');
                break;
            case 'edit':
                if ($id) {
                    $data = $this->getDao('news')->find($id);
                }
                $node = $this->getDao('node')
                    ->orderBySort()
                    ->all();
                $node = $this->arrayFlatten($node);
                load()->func('tpl');
                include $this->template('news_edit');
                break;
            case 'save':
                $data = $this->getInputs(array(
                    'image',
                    'title',
//                    'video',
                    'body',
                    'view',
                    'node_id',
                    'status',
                    'sort',
                ));

                if ($id) {
                    $res = $this->getDao('news')->updateById($id, $data);
                } else {
                    $res = $this->getDao('news')->add($data);
                }
                $this->showActionMessage($res, 'news');
                break;
            case 'status':
                $data = $this->getInputs(array(
                    'status',
                ));
                $res = $this->getDao('news')->updateById($id, $data);
                $this->showActionMessageAjax($res);
                break;
            case 'delete':
                $res = $this->getDao('news')->deleteById($id);
                $this->showActionMessageAjax($res);
                break;
        }
    }

}

