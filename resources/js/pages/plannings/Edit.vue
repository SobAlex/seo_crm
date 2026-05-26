<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4">Редактирование плана</h1>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="title" class="required">Название</Label>
                            <Input id="title" v-model="form.title" :error="errors.title" />
                            <InputError :message="errors.title" />
                        </div>

                        <div>
                            <Label for="website_id" class="required">Сайт</Label>
                            <select id="website_id" v-model="form.website_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Выберите сайт</option>
                                <option v-for="website in websites" :key="website.id" :value="website.id">
                                    {{ website.url }} ({{ website.project?.title }})
                                </option>
                            </select>
                            <InputError :message="errors.website_id" />
                        </div>

                        <div>
                            <Label for="track_id">Трек (опционально)</Label>
                            <select id="track_id" v-model="form.track_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Не привязан</option>
                                <option v-for="track in tracks" :key="track.id" :value="track.id">
                                    {{ track.title }}
                                </option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label for="period_start" class="required">Начало периода</Label>
                                <Input id="period_start" v-model="form.period_start" type="date" />
                                <InputError :message="errors.period_start" />
                            </div>

                            <div>
                                <Label for="period_end" class="required">Конец периода</Label>
                                <Input id="period_end" v-model="form.period_end" type="date" />
                                <InputError :message="errors.period_end" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label for="metric_type" class="required">Тип метрики</Label>
                                <select id="metric_type" v-model="form.metric_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="visits">Визиты</option>
                                    <option value="views">Просмотры</option>
                                    <option value="users">Пользователи</option>
                                    <option value="bounce_rate">Отказы</option>
                                    <option value="depth">Глубина</option>
                                    <option value="conversion">Конверсия</option>
                                </select>
                            </div>

                            <div>
                                <Label for="metric_label">Метка метрики</Label>
                                <Input id="metric_label" v-model="form.metric_label" placeholder="Например: Цель 1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label for="target_value" class="required">Целевое значение</Label>
                                <Input id="target_value" v-model.number="form.target_value" type="number" step="0.01" />
                                <InputError :message="errors.target_value" />
                            </div>

                            <div>
                                <Label for="alert_threshold">Порог оповещения (%)</Label>
                                <Input id="alert_threshold" v-model.number="form.alert_threshold" type="number" step="5" min="0" max="100" />
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <TextLink :href="index().url" variant="escape" :showIcon="true">Отмена</TextLink>
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
import { update } from '@/actions/App/Http/Controllers/PlanningController'
import { index } from '@/routes/plannings'
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const props = defineProps<{
    planning: any
    websites: any[]
    tracks: any[]
}>()

const form = reactive({
    title: props.planning.title,
    website_id: props.planning.website_id,
    track_id: props.planning.track_id,
    period_start: props.planning.period_start,
    period_end: props.planning.period_end,
    metric_type: props.planning.metric_type,
    metric_label: props.planning.metric_label,
    target_value: props.planning.target_value,
    alert_threshold: props.planning.alert_threshold,
})

const errors = ref({})
const processing = ref(false)

const submit = () => {
    processing.value = true
    const { url, method } = update({ planning: props.planning.id })

    router[method](url, form, {
        preserveState: false,
        onFinish: () => (processing.value = false),
        onSuccess: () => router.visit(`/plannings/${props.planning.id}`),
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
