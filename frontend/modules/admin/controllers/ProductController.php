<?php

namespace admin\controllers;

use Yii;
use admin\models\User;
use admin\models\Product;
use admin\models\ProductPrice;
use admin\models\ProductParam;
use common\models\DataAll;
use common\helpers\Hui;
use common\helpers\Html;
use common\helpers\ArrayHelper;

class ProductController extends \admin\components\Controller
{
    /**
     * @authname 产品列表
     */
    public function actionList()
    {
        $query = (new Product)->listQuery()->orderBy('hot ASC');
        //var_dump($query->all());
        //exit;

        $html = $query->getTable([
            'hot' => ['type' => 'text', 'header' => '排序'],
            'name' => ['type' => 'text'],
            'fee' => ['type' => 'text'],
            'desc' => ['type' => 'text'],
            'unit' => ['type' => 'text'],
            'currency' => ['type' => 'select'],
            'unit_price' => ['type' => 'text'],
            'unit_price_na' => ['type' => 'text'],
            'maxrise' => ['type' => 'text'],
            'maxlost' => ['type' => 'text'],
            'force_sell' => ['type' => 'select'],
            'is_trade' => ['type' => 'select'],
            'on_sale' => ['type' => 'select'],
            ['type' => ['delete'], 'width' => '250px', 'value' => function ($row) {
                return 
                    implode(str_repeat('&nbsp;', 2), [
                        Hui::dangerBtn('更新信息', ['updateProduct', 'id' => $row->id], ['class' => 'deleteBtn']),
                        Hui::successBtn('设置交易时间', ['setTradeTime', 'id' => $row->id], ['class' => 'fancybox fancybox.iframe'])
                        //Hui::primaryBtn('更新信息', ['setTradePrice', 'id' => $row->id], ['class' => 'fancybox fancybox.iframe'])
                    ]);
            }]
        ], [
            'paging' => 20,
            // 'addBtn' => u()->isMe() ? ['addStock' => '添加产品'] : null,
            'addBtn' => u()->power <= 9999 ?[]:['addProduct' => '添加特殊产品'],
            'extraBtn' => ['ajaxAllUp' => '一键上架', 'ajaxAllDown' => '一键下架']
        ]);

        return $this->render('list', compact('html'));
    }

    /**
     * @authname 产品浮动列表
     */
    public function actionFloatList()
    {
        $query = Product::find()->joinWith(['productParam'])->where(['source' => Product::SOURCE_FALSE, 'product.state' => Product::STATE_VALID])->orderBy('hot ASC');
        $html = $query->getTable([
            'name',
            'hot' => ['header' => '排序'],
            'productParam.end_point' => ['header' => '波动点位'],
            ['header' => '操作', 'width' => '80px', 'value' => function ($row) {
                return Hui::primaryBtn('波动点位', ['editPoint', 'id' => $row->id], ['class' => 'editBtn']);
            }]
        ], [
            'paging' => 20,
        ]);

        return $this->render('floatList', compact('html'));
    }

    /**
     * @authname 修改产品浮动点位
     */
    public function actionEditPoint() 
    {
        $productParam = ProductParam::findOne(get('id'));
        $point = post('point');
        if ($point <= 0) {
           return error('波动点位不能设置为负数！'); 
        }
        $productParam->start_point = -$point;
        $productParam->end_point = $point;
        if ($productParam->validate()) {
            $product = Product::findOne($productParam->product_id);
            session('initDataParam' . $product->table_name, null);
            $productParam->update(false);
            return success();
        } else {
            return error($productParam);
        }
    }

    /**
     * @authname 添加特殊产品
     */
    public function actionAddProduct()
    {
        $model = new Product;
        
         
        if ($model->load()) {
            //$productParam->product_id = rand(100, 999);

            $time = [
                ['start' => '09:00', 'end' => '04:00'],
            ];
            $model->trade_time = serialize($time);
            $model->source = Product::SOURCE_FALSE;
            $model->one_profit = (1/$model->unit)*$model->unit_price;
            $model->deposit = 100;

            if ($model->save()) {


                $res = self::db("
                      CREATE TABLE IF NOT EXISTS `data_{$model->table_name}` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `price` varchar(30) DEFAULT NULL,
                      `time` datetime DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      UNIQUE KEY `time` (`time`)
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;")->execute();
                $dataAll = DataAll::findOne($model->table_name);

                if (!$dataAll) {//插入最新数据
                    $dataAll = new DataAll;
                    $dataAll->name = $model->table_name;
                    // $dataAll->price = $dataAll->open = rand($productParam->start_price, $productParam->end_price);
                    // $dataAll->close = rand($productParam->start_price, $productParam->end_price);
                    // $dataAll->high = rand($dataAll->close, $productParam->end_price);
                    // $dataAll->low = rand($productParam->start_price, $dataAll->high);
                    // $dataAll->diff = $dataAll->high - $dataAll->low;
                    // $dataAll->diff_rate = sprintf('%.4f', $dataAll->diff / $dataAll->close) * 100 . '%';
                    $dataAll->insert();
                }
                $params = require Yii::getAlias('@common/config/params-local.php');
                $stocks = ArrayHelper::getValue($params, 'stocks', []);
                $stocks[$model->table_name] = $model->table_name;
                $params['stocks'] = $stocks;
                file_put_contents(Yii::getAlias('@common/config/params-local.php'), '<?php' . "\nreturn " . var_export($params, true) . ';');
                return success();
            } else {
                return error($model);
            }
        }

        return $this->render('addProduct', compact('model'));
    }

    /**
     * @authname 添加股票
     */
    public function actionAddStock()
    {
        $model = new Product;
        $model->deposit = 100;
        $model->one_profit = 1;
        if ($model->load()) {
            if ($model->save()) {
                $res = self::db("
CREATE TABLE IF NOT EXISTS `data_{$model->table_name}` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `price` varchar(30) DEFAULT NULL,
    `time` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `time` (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;")->execute();
                $dataAll = DataAll::findOne($model->table_name);
                if (!$dataAll) {
                    $dataAll = new DataAll;
                    $dataAll->name = $model->table_name;
                    $dataAll->insert();
                }
                // 股票采集需要解放以下功能
                // $params = require Yii::getAlias('@common/config/params-local.php');
                // $stocks = ArrayHelper::getValue($params, 'stocks', []);
                // $stocks[$model->table_name] = $model->table_name;
                // $params['stocks'] = $stocks;
                // file_put_contents(Yii::getAlias('@common/config/params-local.php'), '<?php' . "\nreturn " . var_export($params, true) . ';');
                return success();
            } else {
                return error($model);
            }
        }

        return $this->render('addStock', compact('model'));
    }

    /**
     * @authname 一键上架产品
     */
    public function actionAjaxAllUp()
    {
        Product::updateAll(['on_sale' => Product::ON_SALE_YES], '1');

        return success();
    }

    /**
     * @authname 一键下架产品
     */
    public function actionAjaxAllDown()
    {
        Product::updateAll(['on_sale' => Product::ON_SALE_NO], '1');

        return success();
    }

    public function actionDeletePrice()
    {
        $model = ProductPrice::findOne(post('id'));
        if ($model->delete()) {
            return success();
        } else {
            return error($model);
        }
    }

    /**
     * @authname 设置交易价格
     */
    public function actionSetTradePrice($id)
    {
        $product = Product::findOne($id);
        $models = ProductPrice::find()->where(['product_id' => $id])->all();
        if (!$models) {
            $models[] = new ProductPrice;
        }

        if (req()->isPost) {
            for ($i = count($models); $i < count(post('ProductPrice')); $i++) {
                $models[] = new ProductPrice;
            }
            if (ProductPrice::loadMultiple($models, post()) && ProductPrice::validateMultiple($models)) {
                foreach ($models as $model) {
                    $model->save(false);
                }
                return success('设置成功');
            } else {
                $errors = [];
                foreach ($models as $key => $model) {
                    $errors = array_merge($errors, $model->errors);
                }
                return error($errors);
            }
        }

        return $this->render('setTradePrice', compact('models', 'product'));
    }    

     /**
     * @authname 更新一手盈亏单价
     */

    public function actionUpdateProduct()
    {
        $product = Product::find()->where(['id' => get('id')])->one();
        $product->one_profit = (1/$product->unit)*$product->unit_price;
        
                if ($product->save(false)) {
                    return success('更新成功！');
                } 
    }    

    /**
     * @authname 设置交易时间
     */
    public function actionSetTradeTime($id)
    {
        $model = Product::findOne($id);

        if ($model->load(post())) {
            $data = [];
            foreach ($model->trade_start_time as $key => $value) {
                if ($value && $model->trade_end_time[$key]) {
                    $item = [];
                    $item['start'] = $value;
                    $item['end'] = $model->trade_end_time[$key];
                    $data[] = $item;
                }
            }
            $model->trade_time = serialize($data);
            $model->update(false);
            return success('设置成功');
        }
        if ($model->trade_time) {
            $time = unserialize($model->trade_time);
        }
        if (empty($time)) {
            $time = [
                ['start' => '', 'end' => ''],
            ];
        }

        return $this->render('setTradeTime', compact('model', 'time'));
    }
}
