<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';

// test($users);

echo $sort1->link('id') . ' | ' . $sort1->link('product_name');
echo '<br>';
// 第一个列表
foreach ($data1 as $model) {
    echo $model['id'] . '&nbsp;';
    echo $model['product_name'] . '<br>';
}
// 第一个列表的分页
echo self::linkPager();
echo '<br>';
echo $sort2->link('id') . ' | ' . $sort2->link('name');
echo '<br>';
// 第二个列表
foreach ($data2 as $model) {
    echo $model['id'] . '&nbsp;';
    echo $model['name'] . '<br>';
}
// 第二个列表的分页
echo self::linkPager();
// 第三个列表
echo \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
            'rowOptions' => ['a' => 3],
    'columns' => [
        ['class' => 'yii\grid\CheckboxColumn', 'header' => null],
        ['class' => 'yii\grid\SerialColumn'],
        // 数据提供者中所含数据所定义的简单的列
        // 使用的是模型的列的数据
        'id',
        'name',
        // 更复杂的列数据
        [
            'class' => 'yii\grid\DataColumn', //由于是默认类型，可以省略 
            'header' => 'combine',
            'value' => function ($data) {
                return $data->id . '->' . $data->name; // 如果是数组数据则为 $data['name'] ，例如，使用 SqlDataProvider 的情形。
            },
        ],
    ],
]);
?>

<div class="site-index">

    <div class="jumbotron" style="padding:10px">
        <a class="fancybox.ajax" href="<?= self::createUrl(['site/index'])?>">
        fancybox的Ajax弹窗&nbsp;&nbsp;|&nbsp;&nbsp;
        </a>

        <a class="fancybox.iframe" href="<?= self::createUrl(['site/index'])?>">
        fancybox的Iframe弹窗&nbsp;&nbsp;|&nbsp;&nbsp;
        </a>

        <a class="fancybox" href="/css/image/1.jpg">
            fancybox的图片查看
        </a>
        <br>
        <input type="text" class="datepicker" placeholder="datepicker">
        <input type="text" class="timepicker" placeholder="timepicker">
        <input type="text" class="datetimepicker" placeholder="datetimepicker" value="2015-01-03 12:12">
        <br>
        <input type="text" class="startdate" value="" placeholder="start date"> 
        <input type="text" class="enddate" value="" placeholder="end date">

        <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <p><a class="btn btn-default" href="http://www.yiiframework.com/doc/">Yii Documentation &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <p><a class="btn btn-default" href="http://www.yiiframework.com/forum/">Yii Forum &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <p><a class="btn btn-default" href="http://www.yiiframework.com/extensions/">Yii Extensions &raquo;</a></p>
            </div>
        </div>

    </div>
</div>

<script>
$(function() {
    $(".fancybox").fancybox($.config('fancybox', {

    }));
    'fancybox.ajax'.config({
        'maxHeight': 300,
        'height': 900
    });
    'fancybox.iframe'.config({
        'maxHeight': 900,
        'height': 900
    });

    'startdate'.config({
        dateFormat: 'yy mm dd', 
        minInterval: 1000 * 3600 * 24 * 3
    });
})
</script>