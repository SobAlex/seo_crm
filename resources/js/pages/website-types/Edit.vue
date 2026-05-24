<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4">Редактирование типа сайта</h1>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="title" class="required">Название</Label>
                            <Input id="title" v-model="form.title" :error="errors.title" />
                            <InputError :message="errors.title" />
                        </div>

                        <div>
                            <Label for="description">Описание</Label>
                            <textarea id="description" v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                        </div>

                        <div>
                            <Label>Метрики</Label>
                            <div class="flex flex-wrap gap-4 mt-1">
                                <label v-for="metric in metricOptions" :key="metric.value" class="flex items-center gap-1">
                                    <input type="checkbox" :value="metric.value" v-model="form.default_metrics" />
                                    {{ metric.label }}
                                </label>
                            </div>
                        </div>

                        <div>
                            <Label for="icon">Иконка</Label>
                            <Input id="icon" v-model="form.icon" />
                        </div>

                        <div>
                            <Label for="sort_order">Сортировка</Label>
                            <Input id="sort_order" v-model.number="form.sort_order" type="number" />
                        </div>

                        <div class="flex justify-end space-x-2">
                            <TextLink :href="show(websiteType.id).url" variant="escape" :showIcon="true">Отмена</TextLink>
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
import { update } from '@/actions/App/Http/Controllers/WebsiteTypeController'
import { show } from '@/routes/website-types'
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const props = defineProps<{
    websiteType: any
}>()

const metricOptions = [
    { value: 'visits', label: 'Визиты' },
    { value: 'depth', label: 'Глубина' },
    { value: 'time_on_site', label: 'Время на сайте' },
    { value: 'bounce_rate', label: 'Отказы' },
    { value: 'conversion', label: 'Конверсия' },
]

const form = reactive({
    title: props.websiteType.title,
    description: props.websiteType.description,
    default_metrics: props.websiteType.default_metrics || [],
    icon: props.websiteType.icon || '',
    sort_order: props.websiteType.sort_order,
})

const errors = ref({})
const processing = ref(false)

const submit = () => {
    processing.value = true
    const { url, method } = update(props.websiteType.id)

    router[method](url, form, {
        preserveState: false,
        onFinish: () => (processing.value = false),
        onSuccess: () => router.visit(`/website-types/${props.websiteType.id}`),
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
