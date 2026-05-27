<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $project['title'] }} - Отчёт {{ $period['start'] }} — {{ $period['end'] }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
        }

        .logo {
            max-height: 80px;
            margin-bottom: 15px;
        }

        h1 {
            color: #1f2937;
            margin: 0;
            font-size: 24px;
        }

        .period {
            color: #6b7280;
            font-size: 14px;
            margin-top: 5px;
        }

        .info-block {
            background: #f3f4f6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .info-grid {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .info-item {
            flex: 1;
            min-width: 200px;
        }

        .info-label {
            font-size: 12px;
            color: #6b7280;
        }

        .info-value {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }

        h3 {
            color: #3b82f6;
            margin-top: 25px;
            margin-bottom: 10px;
            font-size: 18px;
        }

        h4 {
            margin-left: 15px;
            color: #4b5563;
            font-size: 15px;
        }

        .comment-block {
            background: #f9fafb;
            padding: 12px;
            margin-bottom: 15px;
            margin-left: 30px;
            border-radius: 6px;
            border-left: 3px solid #3b82f6;
        }

        .comment-meta {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .comment-text {
            font-size: 13px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }

        .no-comments {
            color: #9ca3af;
            font-style: italic;
            margin-left: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        @if ($logo)
            <img src="{{ $logo }}" class="logo">
        @endif
        <h1>{{ $project['title'] }}</h1>
        <div class="period">Отчёт за период: {{ $period['start'] }} — {{ $period['end'] }}</div>
        <div class="period">Дата формирования: {{ $generated_at }}</div>
    </div>

    <div class="info-block">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Клиент</div>
                <div class="info-value">{{ $client['title'] }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Статус проекта</div>
                <div class="info-value">
                    @switch($project['status'])
                        @case('active')
                            Активный
                        @break

                        @case('paused')
                            Приостановлен
                        @break

                        @case('completed')
                            Завершён
                        @break

                        @default
                            {{ $project['status'] }}
                    @endswitch
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Период работ</div>
                <div class="info-value">{{ $project['start_date'] ?? '—' }} — {{ $project['end_date'] ?? 'н.в.' }}
                </div>
            </div>
        </div>
    </div>

    <h3>Комментарии за период</h3>

    @forelse($comments as $trackTitle => $tasks)
        <h3>{{ $trackTitle }}</h3>
        @foreach ($tasks as $taskTitle => $taskComments)
            <h4>{{ $taskTitle }}</h4>
            @foreach ($taskComments as $comment)
                <div class="comment-block">
                    <div class="comment-meta">{{ \Carbon\Carbon::parse($comment['date'])->format('d.m.Y H:i') }}</div>
                    <div class="comment-text">{{ $comment['text'] }}</div>
                </div>
            @endforeach
        @endforeach
    @empty
        <p class="no-comments">Нет комментариев с меткой "Для отчета" за указанный период.</p>
    @endforelse

    <div class="footer">
        Сформировано автоматически в системе CRM<br>
        {{ $generated_at }}
    </div>
</body>

</html>
