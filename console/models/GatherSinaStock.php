<?php

namespace console\models;

use Yii;
use common\models\Order;
use common\models\Product;
use common\helpers\StringHelper;

class GatherSinaStock extends Gather
{
    public $urlPrefix = 'http://hq.sinajs.cn/list=';
    
    public function init()
    {
        parent::init();

        $this->productList = config('stocks', []);
    }

    protected function gatherStock($html, $name)
    {
        $arr = explode(',', $html);

        $data = [
            'price' => round($arr[3], 2),
            'open' => $arr[1],
            'high' => $arr[4],
            'low' => $arr[5],
            'close' => $arr[2],
            'diff' => $arr[3] - $arr[2],
            'diff_rate' => round(($arr[3] - $arr[2]) / $arr[2] * 100, 2) . '%',
            'time' => $arr[30] . ' ' . $arr[31]
        ];

        $this->insert($name, $data);
    }

    public function run()
    {
        $this->switchMap = option('risk_product') ?: [];
        $params = $d = '';
        if (!($products = cache('gatherProductList'))) {
            $products = Product::find()->andWhere(['product.state' => Product::STATE_VALID])->asArray()->map('id', 'table_name');
            cache('gatherProductList', $products, 3600 * 10);
        }
        foreach ($this->productList as $name => $url) {
            if (!in_array($name, $products)) {
                continue;
            }
            $params .= $d . $url;
            $d = ',';
        }
        $html = $this->getHtml($this->urlPrefix . $params);
        $pieces = StringHelper::explode("\n", $html);
        foreach ($pieces as $value) {
            preg_match('/var hq_str_(.*)="(.*)";/U', $value, $match);
            $name = $match[1];
            $html = $match[2];
            $this->gatherStock($html, $name);
        }

        // 监听是否有人应该平仓
        $this->listen();
    }
}
