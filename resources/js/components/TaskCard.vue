<template>
    <div class="bg-white rounded-lg shadow-sm p-3 cursor-pointer hover:shadow-md transition">
        <div class="flex justify-between items-start">
            <h4 class="font-medium text-gray-900 text-sm">{{ task.title }}</h4>
            <span
                class="px-1.5 py-0.5 text-xs rounded-full"
                :class="{
                    'bg-red-100 text-red-800': task.priority === 'high',
                    'bg-yellow-100 text-yellow-800': task.priority === 'medium',
                    'bg-green-100 text-green-800': task.priority === 'low',
                }"
            >
                {{ priorityText[task.priority] }}
            </span>
        </div>
        <p v-if="task.description" class="text-xs text-gray-500 mt-1 line-clamp-2">
            {{ task.description }}
        </p>
        <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
            <span v-if="task.deadline">📅 {{ formatDate(task.deadline) }}</span>
            <span v-else>—</span>
            <div class="flex items-center space-x-1">
                <span v-if="task.assignee_user?.name">{{ task.assignee_user.name }}</span>
                <span v-else-if="task.assignee_contractor?.name">{{ task.assignee_contractor.name }}</span>
                <span v-else>—</span>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({ task: Object });

const priorityText = { low: 'Низкий', medium: 'Средний', high: 'Высокий' };

const formatDate = (date) => date ? new Date(date).toLocaleDateString('ru-RU') : '';
</script>
