<div id="goods-form" style="padding: 20px">
    <form class="layui-form" action="" id="goods-edit-form">
        <input type="hidden" name="id" value="{{ $automaticallyGrabGoods->id }}">
        <div class="layui-form-item">
            <select name="seller_nick" lay-verify="required">
                <option value=""></option>
                @forelse($shop as  $value)
                    <option value="{{ $value }}" @if($automaticallyGrabGoods->seller_nick == $value) selected @endif>{{ $value }}</option>
                @empty
                @endforelse
            </select>
        </div>
        <div class="layui-form-item">
            <select name="game_id" lay-verify="required">
                <option value=""></option>
                @forelse($game as $key => $value)
                    <option value="{{ $key }}" @if($automaticallyGrabGoods->game_id == $key) selected @endif>{{ $value }}</option>
                @empty
                @endforelse
            </select>
        </div>
        <div class="layui-form-item">
            <input type="text" name="foreign_goods_id" required lay-verify="required" placeholder="淘宝链接" autocomplete="off" class="layui-input" value="{{ $automaticallyGrabGoods->foreign_goods_id }}">
        </div>
        <div class="layui-form-item layui-form-text">
            <textarea name="remark" placeholder="备注信息" lay-verify="required" class="layui-textarea">{{ $automaticallyGrabGoods->remark }}</textarea>
        </div>
        <div class="layui-form-item">
            <button class="layui-btn layui-bg-blue col-lg-12" lay-submit="" lay-filter="goods-edit-save">确定</button>
            <button  type="button" class="layui-btn layui-btn-danger cancel">取消</button>
        </div>
    </form>
</div>