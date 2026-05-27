<x-mail::message>
    # Отчёт готов

    **Проект:** {{ $project->title }}
    **Период:** {{ $periodStart }} — {{ $periodEnd }}

    Во вложении находится отчёт за указанный период.

    <x-mail::button :url="route('reports.download', $report)">
        Скачать отчёт
    </x-mail::button>

    С уважением,<br>
    {{ config('app.name') }}
</x-mail::message>
