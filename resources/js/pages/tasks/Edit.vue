<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4">Редактирование задачи</h1>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="title" class="required">Название</Label>
                            <Input id="title" v-model="form.title" :error="errors.title" />
                            <InputError :message="errors.title" />
                        </div>

                        <div>
                            <Label for="track_id" class="required">Трек</Label>
                            <select id="track_id" v-model="form.track_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Выберите трек</option>
                                <option v-for="track in tracks" :key="track.id" :value="track.id">
                                    {{ track.title }}
                                </option>
                            </select>
                            <InputError :message="errors.track_id" />
                        </div>

                        <div>
                            <Label for="status_id" class="required">Статус</Label>
                            <select id="status_id" v-model="form.status_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Выберите статус</option>
                                <option v-for="status in statuses" :key="status.id" :value="status.id">
                                    {{ status.title }}
                                </option>
                            </select>
                            <InputError :message="errors.status_id" />
                        </div>

                        <div>
                            <Label for="description">Описание</Label>
                            <textarea id="description" v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <Label for="priority">Приоритет</Label>
                                <select id="priority" v-model="form.priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="low">Низкий</option>
                                    <option value="medium">Средний</option>
                                    <option value="high">Высокий</option>
                                </select>
                            </div>

                            <div>
                                <Label for="deadline">Дедлайн</Label>
                                <Input id="deadline" v-model="form.deadline" type="date" />
                            </div>

                            <div>
                                <Label for="assignee_user_id">Исполнитель (сотрудник)</Label>
                                <select id="assignee_user_id" v-model="form.assignee_user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Не назначен</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <Label for="assignee_contractor_id">Исполнитель (подрядчик)</Label>
                                <select id="assignee_contractor_id" v-model="form.assignee_contractor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Не назначен</option>
                                    <option v-for="contractor in contractors" :key="contractor.id" :value="contractor.id">
                                        {{ contractor.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <Label>Теги</Label>
                            <div class="flex flex-wrap gap-2 mt-1">
                                <label v-for="tag in tags" :key="tag.id" class="flex items-center space-x-1">
                                    <input type="checkbox" :value="tag.id" v-model="form.tag_ids" />
                                    <span class="px-2 py-1 rounded-full text-xs" :style="{ backgroundColor: tag.color + '20', color: tag.color }">
                                        {{ tag.title }}
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <Label>Ключевые слова</Label>
                            <div class="flex flex-wrap gap-2 mt-1">
                                <label v-for="keyword in keywords" :key="keyword.id" class="flex items-center space-x-1">
                                    <input type="checkbox" :value="keyword.id" v-model="form.keyword_ids" />
                                    <span>{{ keyword.keyword }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <TextLink :href="index({ track_id: task.track_id }).url" variant="escape" :showIcon="true">Отмена</TextLink>
                            <Button type="submit" variant="update" size="lg" :showIcon="true" :disabled="processing">
                                {{ processing ? 'Сохранение...' : 'Обновить' }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'
import { update } from '@/actions/App/Http/Controllers/TaskController'
import { index } from '@/routes/tasks'
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const props = defineProps<{
    task: any
    tracks: any[]
    statuses: any[]
    users: any[]
    contractors: any[]
    tags: any[]
    keywords: any[]
}>()

const form = reactive({
    title: props.task.title,
    description: props.task.description,
    track_id: props.task.track_id,
    status_id: props.task.status_id,
    priority: props.task.priority,
    deadline: props.task.deadline,
    assignee_user_id: props.task.assignee_user_id,
    assignee_contractor_id: props.task.assignee_contractor_id,
    tag_ids: props.task.tags?.map((t: any) => t.id) || [],
    keyword_ids: props.task.keywords?.map((k: any) => k.id) || [],
})

const errors = ref({})
const processing = ref(false)

const submit = () => {
    processing.value = true
    const { url, method } = update({ task: props.task.id })

    router[method](url, form, {
        preserveState: false,
        onFinish: () => (processing.value = false),
        onSuccess: () => router.visit(`/tasks/${props.task.id}`),
        onError: (err) => (errors.value = err),
    })
}
</script>

<style scoped>
.required::after {
    content: ' *';
    color: #ef4444;
}
</style>
