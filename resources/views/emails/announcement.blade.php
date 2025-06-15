<h2>{{ $announcement->title }}</h2>

<p>{!! nl2br(e($announcement->content)) !!}</p>

<p style="margin-top: 20px;">
    <small>Sent by: {{ $announcement->sent_by }}</small>
</p>
