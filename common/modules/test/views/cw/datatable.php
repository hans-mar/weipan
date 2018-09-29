<?php common\assets\DataTableAsset::register($this) ?>

<table id="table">
    
</table>

<script type="text/javascript">
$(function() {
    var data = [
        {name:'张三', age:'啊', salary: '2000'},
        {name:'李四', age:'不', salary: '3000'},
        {name:'王五1', age:'餐1', salary: '14000'},
        {name:'王五2', age:'餐2', salary: '24000'},
        {name:'王五3', age:'餐3', salary: '34000'},
        {name:'王五4', age:'餐4', salary: '44000'},
        {name:'王五5', age:'餐5', salary: '54000'},
        {name:'王五6', age:'餐6', salary: '64000'},
    ];

    // var data = [
    //     ['张三','啊','2000'],
    //     ['李三','啊','2000'],
    //     ['红三','啊','2000'],
    // ];

    // var columns = [
    //     {
    //         header:'aa',
    //         // sort:true
    //     },
    //     {
    //         header:'aa3',
    //     },
    //     {
    //         header:'aa4',
    //     }
    // ];

    var columns = {
        age: '年龄',
        name: { 
            header: '姓 名',
            //自定义td属性
            options: {
                // class: 'text-c',
                style: 'color:red'
            },
            //排序
            sort: true,
            //搜索
            search: false,
            //自定义td内容
            value: function (value, row) {
                return '<span><input type="checkbox" name="username"/>采购-' + value + '</span>';
            }
        },
        salary: {
            header: '薪水',
            key: 'salary',
            search: true,
            value: function (value) {
                return '$' + value;
            }
        },
        '': {
            header: '操 作',
            // value: '<a href="">编辑-function</a> | <a href="">删除</a>'
            value: function (row) {            
                return '<a href="/abc.php?id=">编辑-' + row['name'] + '</a> | <a href="">删除</a>';
            }
        }
    };
    $('#table').getTable(data, columns, {
        paging: 5,
        rowOptions: function (row, index) {
            return {
                'a': row['name'],
                'b': row['age']
            };
        }
    });
});
</script>