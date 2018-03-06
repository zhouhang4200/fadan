@php
    $currentMessageCount = 0;
@endphp
@foreach ($messageArr['list'] as $data)
    {{--@if ($data['type'] == 4))--}}
        {{--<p style="text-align:center;">{{ $data['createtime'] }}</p>--}}
        {{--<p>{{ $data['nickname'] }}：</p>--}}
        {{--<p>{{ $data['content'] }}</p>--}}
        {{--@continue--}}
    {{--@endif--}}

    <div class="{{ $data['userid'] == $dailianUid ? 'kf_message' : 'customer_message' }}">
        <div class="message">
            <div class="message_time">
                {{ $data['createtime'] }}
            </div>
            <div class="portrait">
                <img src="{{ $data['touaddr'] }}" alt="">
            </div>
            <div class="content">{{ $data['content'] }}</div>
        </div>
    </div>

    @php $currentMessageCount++;  @endphp
@endforeach
@if($currentMessageCount == 10)
    <div style="text-align: center" id="loadMoreMessage" data="{{ $data['list'][9] }}">查看更多留言</div>
@endif
