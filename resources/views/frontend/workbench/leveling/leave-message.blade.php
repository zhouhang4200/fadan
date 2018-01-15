@foreach ($dataList as $data)
    @if (!isset($data->uid))
        <p style="text-align:center;">{{ $data->created_on }}</p>
        <p>{{ $data->userNickname }}：</p>
        <p>{{ $data->mess }}</p>
        @continue
    @endif

    <div class="{{ !isset($data->uid) || $data->uid == $show91Uid ? 'kf_message' : 'customer_message' }}">
        <div class="message">
            <div class="message_time">
                {{ $data->created_on }}
            </div>
            <div class="portrait">
                <img src="/frontend/images/{{ $data->uid == $show91Uid ? 'service_avatar.jpg' : 'customer_avatar.jpg' }}" alt="">
            </div>
            <div class="content">{{ $data->mess }}</div>
        </div>
    </div>
@endforeach
