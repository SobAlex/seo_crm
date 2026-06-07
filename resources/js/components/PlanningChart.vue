<template>
    <div class="bg-white rounded shadow p-4 mt-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Динамика выполнения</h3>
            <div class="flex space-x-2">
                <button
                    @click="mode = 'cumulative'"
                    :class="mode === 'cumulative' ? 'bg-indigo-600 text-white' : 'bg-gray-200'"
                    class="px-3 py-1 rounded text-sm"
                >
                    Накопленный
                </button>
                <button
                    @click="mode = 'weekly'"
                    :class="mode === 'weekly' ? 'bg-indigo-600 text-white' : 'bg-gray-200'"
                    class="px-3 py-1 rounded text-sm"
                >
                    По неделям
                </button>
            </div>
        </div>
        <canvas ref="chartCanvas"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const props = defineProps({
    weeks: Array, // массив объектов с полями: week_number, target, actual, manual_value, cumulative_target, cumulative_actual, cumulative_forecast
});

const mode = ref('cumulative');
const chartCanvas = ref(null);
let chartInstance = null;

const renderChart = () => {
    if (!chartCanvas.value) return;
    if (chartInstance) chartInstance.destroy();

    const labels = props.weeks.map(w => `Неделя ${w.week_number}`);
    let datasets = [];

    if (mode.value === 'cumulative') {
        datasets = [
            {
                label: 'План (накопленный)',
                data: props.weeks.map(w => w.cumulative_target),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
            },
            {
                label: 'Факт (накопленный)',
                data: props.weeks.map(w => w.cumulative_actual),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
            },
            {
                label: 'Прогноз (накопленный)',
                data: props.weeks.map(w => w.cumulative_forecast),
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.05)',
                borderWidth: 2,
                borderDash: [5, 5],
                fill: false,
                tension: 0.4,
            },
        ];
    } else {
        datasets = [
            {
                label: 'План (неделя)',
                data: props.weeks.map(w => w.target),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                type: 'line',
            },
            {
                label: 'Факт (неделя)',
                data: props.weeks.map(w => w.actual !== null ? w.actual : null),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                borderWidth: 2,
                fill: true,
                type: 'line',
                spanGaps: false,
            },
            {
                label: 'Ручной ввод',
                data: props.weeks.map(w => w.manual_value !== null ? w.manual_value : null),
                borderColor: '#ef4444',
                backgroundColor: 'transparent',
                borderWidth: 2,
                type: 'scatter',
                pointRadius: 4,
                pointHoverRadius: 6,
                showLine: false,
            },
        ];
    }

    chartInstance = new Chart(chartCanvas.value, {
        type: 'line',
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                tooltip: { mode: 'index', intersect: false },
                legend: { position: 'bottom' },
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Значение' } },
                x: { title: { display: true, text: 'Недели' } },
            },
        },
    });
};

watch(mode, () => renderChart());
onMounted(() => renderChart());
</script>
