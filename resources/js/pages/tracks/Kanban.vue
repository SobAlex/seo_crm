<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-2xl font-bold">Kanban — {{ track.title }}</h1>
                        <Link :href="`/tracks/${track.id}`" class="btn btn-secondary">
                            Назад к треку
                        </Link>
                    </div>

                    <div class="flex overflow-x-auto pb-4 space-x-4">
                        <div
                            v-for="status in statuses"
                            :key="status.id"
                            class="w-80 flex-shrink-0 bg-gray-50 rounded shadow-sm"
                            :data-status-id="status.id"
                        >
                            <div class="p-3 border-b border-gray-200 font-medium flex justify-between">
                                <span>{{ status.title }}</span>
                                <span class="text-sm text-gray-500">
                                    {{ tasksByStatus[status.id]?.length || 0 }}
                                </span>
                            </div>
                            <draggable
                                :list="tasksByStatus[status.id]"
                                group="tasks"
                                item-key="id"
                                @change="(evt) => onDragChange(status, evt)"
                                class="p-2 min-h-[500px] space-y-2"
                            >
                                <template #item="{ element: task }">
                                    <div class="bg-white rounded p-3 shadow cursor-pointer hover:shadow-md transition">
                                        <div class="flex justify-between items-start">
                                            <Link :href="`/tasks/${task.id}`" class="font-medium text-gray-900 text-sm hover:text-indigo-600">
                                                {{ task.title }}
                                            </Link>
                                            <span
                                                class="px-1.5 py-0.5 text-xs rounded-full"
                                                :class="{
                                                    'bg-red-100 text-red-800': task.priority === 'high',
                                                    'bg-yellow-100 text-yellow-800': task.priority === 'medium',
                                                    'bg-green-100 text-green-800': task.priority === 'low',
                                                }"
                                            >
                                                {{ { low: 'Низкий', medium: 'Средний', high: 'Высокий' }[task.priority] }}
                                            </span>
                                        </div>
                                        <p v-if="task.description" class="text-xs text-gray-500 mt-1 line-clamp-2">
                                            {{ task.description }}
                                        </p>
                                        <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                                            <span v-if="task.deadline">
                                                📅 {{ formatDate(task.deadline) }}
                                            </span>
                                            <span v-else>—</span>
                                            <div class="flex items-center gap-1">
                                                <span>{{ task.assignee_user?.name || task.assignee_contractor?.name || '—' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import draggable from 'vuedraggable';
import { Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

const props = defineProps({
    track: Object,
    statuses: Array,
    tasks: Array,
});

const tasksByStatus = reactive({});

props.statuses.forEach(status => {
    tasksByStatus[status.id] = props.tasks.filter(task => task.status_id === status.id);
});

const formatDate = (date) => date ? new Date(date).toLocaleDateString('ru-RU') : '';

const onDragChange = (status, event) => {
    if (event.added) {
        const task = event.added.element;
        const newStatusId = status.id;
        router.patch(`/tasks/${task.id}/status`, {
            status_id: newStatusId,
        }, {
            preserveState: true,
            onSuccess: () => {
                // Вместо полной перезагрузки – обновляем только задачи
                router.reload({ only: ['tasks'] });
            },
            onError: (error) => {
                console.error('Ошибка обновления статуса', error);
            },
        });
    }
};
</script>
