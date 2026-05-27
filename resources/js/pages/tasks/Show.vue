<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-2xl font-bold">{{ task.title }}</h1>
                        <div class="flex space-x-2">
                            <TextLink :href="index().url" variant="escape" :showIcon="true">
                                Назад
                            </TextLink>

                            <TextLink :href="edit({ task: task.id }).url" variant="edit" :showIcon="true">
                                Редактировать
                            </TextLink>

                            <Button variant="delete" :showIcon="true" size="lg" @click="destroyTask">
                                Удалить
                            </Button>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 mt-4 pt-4">
                        <dl class="divide-y divide-gray-100">
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">ID</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ task.id }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Название</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ task.title }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Описание</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ task.description || '—' }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Трек</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ task.track?.title }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Статус</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">
                                    <span class="px-2 py-1 rounded-full text-xs" :style="{ backgroundColor: task.status?.color + '20', color: task.status?.color }">
                                        {{ task.status?.title }}
                                    </span>
                                </dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Приоритет</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">
                                    <span :class="priorityClass(task.priority)" class="px-2 py-1 rounded-full text-xs">
                                        {{ priorityLabel(task.priority) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Дедлайн</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ formatDate(task.deadline) }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Исполнитель</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ task.assignee_user?.name || task.assignee_contractor?.name || '—' }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Создатель</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ task.created_by?.name }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Теги</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="tag in task.tags" :key="tag.id" class="px-2 py-1 rounded-full text-xs" :style="{ backgroundColor: tag.color + '20', color: tag.color }">
                                            {{ tag.title }}
                                        </span>
                                        <span v-if="!task.tags?.length">—</span>
                                    </div>
                                </dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Ключевые слова</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="keyword in task.keywords" :key="keyword.id" class="px-2 py-1 rounded-full text-xs bg-gray-100">
                                            {{ keyword.keyword }}
                                        </span>
                                        <span v-if="!task.keywords?.length">—</span>
                                    </div>
                                </dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Дата создания</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ formatDateTime(task.created_at) }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Дата обновления</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ formatDateTime(task.updated_at) }}</dd>
                            </div>
                            <div v-if="task.completed_at" class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Дата завершения</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ formatDateTime(task.completed_at) }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Комментарии (внутри основного контейнера) -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Комментарии</h3>

                        <!-- Список комментариев -->
                        <div class="space-y-4 mb-6">
                            <div v-for="comment in task.comments" :key="comment.id" class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium">{{ comment.user?.name || comment.contractor?.name }}</span>
                                        <span class="text-xs text-gray-500">{{ formatDateTime(comment.created_at) }}</span>
                                    </div>
                                    <div class="flex space-x-1">
                                        <span
                                            v-for="tag in comment.tags"
                                            :key="tag.id"
                                            class="px-2 py-0.5 text-xs rounded-full"
                                            :style="{ backgroundColor: tag.color + '20', color: tag.color }"
                                        >
                                            {{ tag.title }}
                                        </span>
                                    </div>
                                </div>
                                <p class="mt-2 text-gray-700">{{ comment.text }}</p>
                            </div>

                            <div v-if="!task.comments?.length" class="text-gray-500 text-sm">
                                Нет комментариев
                            </div>
                        </div>

                        <!-- Форма добавления комментария -->
                        <form @submit.prevent="addComment" class="mt-4 border-t pt-4">
                            <div>
                                <Label for="comment_text">Добавить комментарий</Label>
                                <textarea
                                    id="comment_text"
                                    v-model="newComment.text"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    placeholder="Напишите комментарий..."
                                ></textarea>
                            </div>

                            <div class="mt-2">
                                <Label>Метки комментария</Label>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <label v-for="tag in commentTags" :key="tag.id" class="flex items-center space-x-1">
                                        <input type="checkbox" :value="tag.id" v-model="newComment.tag_ids" />
                                        <span
                                            class="px-2 py-1 rounded-full text-xs"
                                            :style="{ backgroundColor: tag.color + '20', color: tag.color }"
                                        >
                                            {{ tag.title }}
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end mt-3">
                                <Button type="submit" variant="store" size="sm" :disabled="commentProcessing">
                                    {{ commentProcessing ? 'Отправка...' : 'Добавить комментарий' }}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { index, edit } from '@/routes/tasks'
import { destroy } from '@/actions/App/Http/Controllers/TaskController'

const props = defineProps<{
    task: any
}>()

const priorityLabel = (priority: string) => {
    const labels: Record<string, string> = { low: 'Низкий', medium: 'Средний', high: 'Высокий' }
    return labels[priority] || priority
}

const priorityClass = (priority: string) => {
    const classes: Record<string, string> = {
        low: 'bg-green-100 text-green-800',
        medium: 'bg-yellow-100 text-yellow-800',
        high: 'bg-red-100 text-red-800',
    }
    return classes[priority] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date: string | null) => {
    if (!date) return '—'
    return new Date(date).toLocaleDateString('ru-RU')
}

const formatDateTime = (date: string | null) => {
    if (!date) return '—'
    return new Date(date).toLocaleString('ru-RU')
}

const destroyTask = () => {
    if (confirm(`Удалить задачу "${props.task.title}"?`)) {
        const { url, method } = destroy({ task: props.task.id })
        router[method](url)
    }
}

const newComment = ref({
    text: '',
    tag_ids: [] as number[]
})
const commentProcessing = ref(false)
const commentTags = ref<any[]>([])

// Загрузка меток комментариев
const loadCommentTags = async () => {
    try {
        const response = await fetch('/api/comment-tags')
        commentTags.value = await response.json()
    } catch (error) {
        console.error('Ошибка загрузки меток:', error)
    }
}

const addComment = () => {
    if (!newComment.value.text.trim()) return

    commentProcessing.value = true

    router.post(`/tasks/${props.task.id}/comments`, newComment.value, {
        preserveState: true,
        onSuccess: () => {
            newComment.value = { text: '', tag_ids: [] }
            commentProcessing.value = false
        },
        onError: () => {
            commentProcessing.value = false
        }
    })
}

onMounted(() => {
    loadCommentTags()
})
</script>
