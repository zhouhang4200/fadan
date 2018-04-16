<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>左右两侧固定列，中间内容可以横向拖动</title>
    <link rel="stylesheet" href="/frontend/css/fixed-table.css">
    <script src="http://www.jq22.com/jquery/jquery-1.10.2.js"></script>
    <script src="/frontend/js/fixed-table.js"></script>
    <style>
        .fixed-table-box{
            width: 1000px;
            margin: 50px auto;
        }
        .fixed-table-box>.fixed-table_body-wraper{/*内容了表格主体内容有纵向滚动条*/
            max-height: 260px;
        }

        .fixed-table_fixed>.fixed-table_body-wraper{/*为了让两侧固定列能够同步表格主体内容滚动*/
            max-height: 240px;
        }

        .w-150{
            width: 150px;
        }
        .w-120{
            width: 120px;
        }
        .w-300{
            width: 300px;
        }
        .w-100{
            width: 100px;
        }
        .btns{
            text-align: center;
        }
        .btns button{
            padding: 10px 20px;
        }
    </style>
</head>
<body>

<div class="fixed-table-box row-col-fixed">
    <!-- 表头 start -->
    <div class="fixed-table_header-wraper">
        <table class="fixed-table_header" cellspacing="0" cellpadding="0" border="0">
            <thead>
            <tr>
                <td  data-fixed="true"><div class="table-cell w-150">订单号</div></td>
                <td><div class="table-cell w-150">号主旺旺</div></td>
                <td><div class="table-cell w-150">客服备注</div></td>
                <td><div class="table-cell w-150">代练标题</div></td>
                <td><div class="table-cell w-150">游戏/区/服</div></td>
                <td><div class="table-cell w-150">账号/密码</div></td>
                <td><div class="table-cell w-150">角色名称</div></td>
                <td><div class="table-cell w-150">订单状态</div></td>
                <td><div class="table-cell w-150">代练价格</div></td>
                <td><div class="table-cell w-150">账号/密码</div></td>
                <td><div class="table-cell w-150">角色名称</div></td>
                <td><div class="table-cell w-150">订单状态</div></td>
                <td><div class="table-cell w-150">代练价格</div></td>
                <td><div class="table-cell w-150">角色名称</div></td>
                <td><div class="table-cell w-150">订单状态</div></td>
                <td><div class="table-cell w-150">代练价格</div></td>
                <td><div class="table-cell w-150">效率保证金</div></td>
                <td><div class="table-cell w-150">安全保证金</div></td>
                <td><div class="table-cell w-150">发单时间</div></td>
                <td><div class="table-cell w-150">接单时间</div></td>
                <td><div class="table-cell w-150">代练时间</div></td>
                <td><div class="table-cell w-150">剩余时间</div></td>
                <td><div class="table-cell w-150">打手呢称</div></td>
                <td><div class="table-cell w-150">打手电话</div></td>
                <td><div class="table-cell w-150">号主电话</div></td>
                <td><div class="table-cell w-150">来源价格</div></td>
                <td><div class="table-cell w-150">支付金额</div></td>
                <td><div class="table-cell w-150">获得金额</div></td>
                <td><div class="table-cell w-150">手续费</div></td>
                <td><div class="table-cell w-150">利润</div></td>
                <td><div class="table-cell w-150">发单客服</div></td>
                <td class="w-100" data-fixed="true" data-direction="right"><div class="table-cell w-150">操作</div></td>
            </tr>
            </thead>
        </table>
    </div>
    <!-- 表头 end -->
    <!-- 表格内容 start -->
    <div class="fixed-table_body-wraper">
        <table class="fixed-table_body" cellspacing="0" cellpadding="0" border="0">
            <tbody>
            @forelse($orders as $item)
            <tr>
                <td><div class="table-cell w-150"> 2016-05-03</div></td>
                <td><div class="table-cell w-150">王小虎</div></td>
                <td><div class="table-cell w-150">上海</div></td>
                <td><div class="table-cell w-150">普陀区</div></td>
                <td><div class="table-cell w-150">上海市普陀区金沙江路 1518 路</div></td>
                <td><div class="table-cell w-150">200333</div></td>
                <td><div class="table-cell w-150">角色名称</div></td>
                <td><div class="table-cell w-150">订单状态</div></td>
                <td><div class="table-cell w-150">代练价格</div></td>
                <td><div class="table-cell w-150">200333</div></td>
                <td><div class="table-cell w-150">角色名称</div></td>
                <td><div class="table-cell w-150">订单状态</div></td>
                <td><div class="table-cell w-150">代练价格</div></td>
                <td><div class="table-cell w-150">角色名称</div></td>
                <td><div class="table-cell w-150">订单状态</div></td>
                <td><div class="table-cell w-150">代练价格</div></td>
                <td><div class="table-cell w-150">效率保证金</div></td>
                <td><div class="table-cell w-150">安全保证金</div></td>
                <td><div class="table-cell w-150">发单时间</div></td>
                <td><div class="table-cell w-150">接单时间</div></td>
                <td><div class="table-cell w-150">代练时间</div></td>
                <td><div class="table-cell w-150">剩余时间</div></td>
                <td><div class="table-cell w-150">打手呢称</div></td>
                <td><div class="table-cell w-150">打手电话</div></td>
                <td><div class="table-cell w-150">号主电话</div></td>
                <td><div class="table-cell w-150">来源价格</div></td>
                <td><div class="table-cell w-150">支付金额</div></td>
                <td><div class="table-cell w-150">获得金额</div></td>
                <td><div class="table-cell w-150">手续费</div></td>
                <td><div class="table-cell w-150">利润</div></td>
                <td><div class="table-cell w-150">发单客服</div></td>
                <td>
                    <div class="table-cell w-150">
                        <a href="###">查看</a>
                        <a href="###">编辑</a>
                    </div>
                </td>
            </tr>
            @empty
            @endforelse
            </tbody>
        </table>
    </div>
    <!-- 表格内容 end -->

    <!-- 固定列 start -->
    <div class="fixed-table_fixed fixed-table_fixed-left">
        <div class="fixed-table_header-wraper">
            <table class="fixed-table_header" cellspacing="0" cellpadding="0" border="0">
                <thead>
                <tr>
                    <th class="w-150"><div class="table-cell w-150">订单号</div></th>
                </tr>
                </thead>
            </table>
        </div>

        <div class="fixed-table_body-wraper">
            <table class="fixed-table_body" cellspacing="0" cellpadding="0" border="0">
                <tbody>
                @forelse($orders as $item)
                <tr>
                    <td class="w-150"><div class="table-cell w-150"> 2016-05-03</div></td>
                </tr>
                @empty
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="fixed-table_fixed fixed-table_fixed-right">
        <div class="fixed-table_header-wraper">
            <table class="fixed-table_header" cellspacing="0" cellpadding="0" border="0">
                <thead>
                <tr>
                    <th class="w-100"><div class="table-cell w-150">操作</div></th>
                </tr>
                </thead>
            </table>
        </div>

        <div class="fixed-table_body-wraper">
            <table class="fixed-table_body" cellspacing="0" cellpadding="0" border="0">
                <tbody>
                @forelse($orders as $item)
                <tr>
                    <td class="w-100">
                        <div class="table-cell w-150">
                            <a href="###">查看</a>
                            <a href="###">编辑</a>
                        </div>
                    </td>
                </tr>
                @empty
                @endforelse

                </tbody>
            </table>
        </div>
    </div>
    <!-- 固定列 end -->
</div>



<script>
    //初始化FixedTable
    $(".fixed-table-box").fixedTable();

    //清空表格
    $("#empty_data").on("click", function (){
        $(".fixed-table-box").emptyTable();
    });
    //添加数据
    $("#add_data").on("click", function (){
        $(".fixed-table-box").addRow(function (){
            var html = '';
            for(var i = 0; i < 5; i ++){
                html += '<tr>';
                html += '    <td class="w-150"><div class="table-cell w-150"> 2016-05-03</div></td>';
                html += '    <td><div class="table-cell w-150">王小虎</div></td>';
                html += '    <td><div class="table-cell w-150">上海</div></td>';
                html += '    <td><div class="table-cell w-150">普陀区</div></td>';
                html += '    <td class="w-300"><div class="table-cell w-150">上海市普陀区金沙江路 1518 路</div></td>';
                html += '    <td><div class="table-cell w-150">200333</div></td>';
                html += '    <td class="w-100">';
                html += '        <div class="table-cell w-150">';
                html += '            <a href="###">查看</a>';
                html += '            <a href="###">编辑</a>';
                html += '        </div>';
                html += '    </td>';
                html += '</tr>';
            }
            return html;
        });
    });

    //删除指定行
    $("#del_row").on("click", function (){
        $(".fixed-table-box").deleteRow($(".fixed-table-box").children('.fixed-table_fixed-left').children('.fixed-table_body-wraper').find('tr').eq(0));
    });
</script>
</body>
</html>