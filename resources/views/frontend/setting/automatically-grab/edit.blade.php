<div id="goods-form" style="padding: 30px 60px 0 0px;">
    <form class="layui-form" action="" id="goods-edit-form">
        <input type="hidden" name="id" value="{{ $automaticallyGrabGoods->id }}">
        <div class="layui-form-item">
            <label class="layui-form-label">店铺</label>
            <div class="layui-input-block">
            <select name="seller_nick" lay-verify="required" lay-search>
                <option value=""></option>
                @forelse($shop as  $value)
                    <option value="{{ $value }}" @if($automaticallyGrabGoods->seller_nick == $value) selected @endif>{{ $value }}</option>
                @empty
                @endforelse
            </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">绑定游戏</label>
            <div class="layui-input-block">
            <select name="game_id" lay-verify="required" lay-search lay-filter="game">
                <option value=""></option>
                @forelse($game as $key => $value)
                    <option value="{{ $key }}" @if($automaticallyGrabGoods->game_id == $key) selected @endif>{{ $value }}</option>
                @empty
                @endforelse
            </select>
            </div>
        </div>
        @if($automaticallyGrabGoods->game_id == 86)
            <div class="layui-form-item dnf">
                <label class="layui-form-label">订单类型</label>
                <div class="layui-input-block">
                    <input type="radio" name="type" value="0" title="普通订单" @if($automaticallyGrabGoods->type == 0) checked  @endif>
                    <input type="radio" name="type" value="1" title="推荐号" @if($automaticallyGrabGoods->type == 1) checked  @endif>
                </div>
            </div>
        @endif
        <div class="layui-form-item">
            <label class="layui-form-label">淘宝链接</label>
            <div class="layui-input-block">
            <input type="text" name="foreign_goods_id" required lay-verify="required" placeholder="淘宝链接" autocomplete="off" class="layui-input" value="{{ $automaticallyGrabGoods->foreign_goods_id }}">
        </div>
        </div>

        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">备注信息</label>
            <div class="layui-input-block">
            <textarea name="remark" placeholder="备注信息" lay-verify="required" class="layui-textarea">{{ $automaticallyGrabGoods->remark }}</textarea>
            </div>
        </div>
        <div class="layui-form-item">

            <div class="layui-input-block">
            <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="goods-edit-save">确定</button>
            <button  type="button" class="layui-btn layui-btn-danger cancel">取消</button>
        </div></div>
    </form>
</div>