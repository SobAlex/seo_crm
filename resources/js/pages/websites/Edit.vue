<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4">Редактирование сайта</h1>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="url" class="required">URL</Label>
                            <Input id="url" v-model="form.url" placeholder="https://example.com" :error="errors.url" />
                            <InputError :message="errors.url" />
                        </div>

                        <div>
                            <Label for="project_id" class="required">Проект</Label>
                            <select id="project_id" v-model="form.project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Выберите проект</option>
                                <option v-for="project in projects" :key="project.id" :value="project.id">
                                    {{ project.title }}
                                </option>
                            </select>
                            <InputError :message="errors.project_id" />
                        </div>

                        <div>
                            <Label for="website_type_id" class="required">Тип сайта</Label>
                            <select id="website_type_id" v-model="form.website_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Выберите тип сайта</option>
                                <option v-for="type in websiteTypes" :key="type.id" :value="type.id">
                                    {{ type.title }}
                                </option>
                            </select>
                            <InputError :message="errors.website_type_id" />
                        </div>

                        <div>
                            <Label for="cms">CMS</Label>
                            <Input id="cms" v-model="form.cms" />
                        </div>

                        <div>
                            <Label for="region">Регион</Label>
                            <Input id="region" v-model="form.region" />
                        </div>

                        <!-- Topvisor проект -->
                        <div>
                            <Label for="topvisor_project_id">Проект в Topvisor</Label>
                            <select id="topvisor_project_id" v-model="form.topvisor_project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Не выбран</option>
                                <option v-for="proj in topvisorProjects" :key="proj.id" :value="proj.id">
                                    {{ proj.name }} (ID: {{ proj.id }})
                                </option>
                            </select>
                            <InputError :message="errors.topvisor_project_id" />
                            <p class="text-gray-500 text-xs mt-1">
                                Соответствие сайта проекту в Topvisor для автоматического сбора позиций.
                            </p>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <TextLink :href="index({ project_id: website.project_id }).url" variant="escape" :showIcon="true">Отмена</TextLink>
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
import { update } from '@/actions/App/Http/Controllers/WebsiteController'
import { index } from '@/routes/websites'
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const props = defineProps<{
    website: any
    projects: any[]
    websiteTypes: any[]
    topvisorProjects: any[]
}>()

const form = reactive({
    url: props.website.url,
    project_id: props.website.project_id,
    website_type_id: props.website.website_type_id,
    cms: props.website.cms,
    region: props.website.region,
    topvisor_project_id: props.website.topvisor_project_id || '',
})

const errors = ref({})
const processing = ref(false)

const submit = () => {
    processing.value = true
    const { url, method } = update({ website: props.website.id })

    router[method](url, form, {
        preserveState: false,
        onFinish: () => (processing.value = false),
        onSuccess: () => router.visit(`/websites/${props.website.id}`),
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
