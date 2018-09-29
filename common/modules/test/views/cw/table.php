<?= $html ?>
<?= $html2 ?>

<?php 
// echo \common\widgets\Table::widget([
//     // 'count' => self::$totalCount,
//     // 'data' => self::paginate($query),
//     'query' => $query,
//     'searchColumns' => [
//         'id',
//         'state' => 'radio',
//         'supplyTrade.currency' => ['type' => 'checkbox', 'header' => '币种', 'items' => 'getTpMap'],
//         'create_method' => ['type' => 'select'],
//         'time' => 'dateRange'
//     ],
//     'columns' => [
//         ['type' => 'checkbox'],
//         'id' => ['sort' => true],
//         'order_price',
//         'created_by',
//         'state',
//         ['header' => '订单数量', 'value' => function($value) {return $value['order_quantity'];}],
//         'supplyUser.admin.realname' => '卖方交易员',
//         'demandUser.admin.realname' => [new \common\models\AdminUser, 'testRealname'],
//         'created' => function($value) {return date('Y-m-d', strtotime($value->created));},
//         'supplyUser.company_name' => ['header' => '买方公司名称', 'sort' => false],
//         'demandUser.company_name' => ['header' => '卖方公司名称', 'type' => 'text', 'search' => true],
//         'supplyTrade.trade_type' => ['search' => 'checkbox', 'type' => 'select'],
//         ['type' => ['edit' => 'edit-one', 'view', 'reset'], 'value' => [new \common\models\AdminUser, 'testBtn']]
//     ]
// ]) ;
?>
<script>
$(function() {
    // $(".table").paginate().sortTable();
    // $("#t1").paginate(8);
    // $(".table").paginate();
    'created-datetimepicker'.config({
        dateFormat: 'yy-mm'
    });

})
</script>