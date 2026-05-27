<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4">Сформировать отчёт</h1>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="project_id" class="required">Проект</Label>
                            <select id="project_id" v-model="form.project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Выберите проект</option>
                                <option v-for="project in projects" :key="project.id" :value="project.id">
                                    {{ project.title }} ({{ project.client?.title }})
                                </option>
                            </select>
                            <InputError :message="errors.project_id" />
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

                        <div class="flex justify-end space-x-2">
                            <TextLink :href="index().url" variant="escape" :showIcon="true">Отмена</TextLink>
                            <Button type="submit" variant="store" size="lg" :showIcon="true" :disabled="processing">
                                {{ processing ? 'Генерация...' : 'Сформировать' }}
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
import { store } from '@/actions/App/Http/Controllers/ReportController'
import { index } from '@/routes/reports'
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

defineProps<{
    projects: any[]
}>()

const form = reactive({
    project_id: '',
    period_start: '',
    period_end: '',
})

const errors = ref({})
const processing = ref(false)

const submit = () => {
    processing.value = true
    const { url, method } = store()

    router[method](url, form, {
        preserveState: true,
        onFinish: () => (processing.value = false),
        onSuccess: () => router.visit(index().url),
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
