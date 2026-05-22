<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4">Создание клиента</h1>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="title" class="required">Название</Label>
                            <Input
                                id="title"
                                v-model="form.title"
                                :error="errors.title"
                            />
                            <InputError :message="errors.title" />
                        </div>

                        <div>
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                :error="errors.email"
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <div>
                            <Label for="phone">Телефон</Label>
                            <Input
                                id="phone"
                                v-model="form.phone"
                                :error="errors.phone"
                            />
                            <InputError :message="errors.phone" />
                        </div>

                        <div>
                            <Label for="address">Адрес</Label>
                            <textarea
                                id="address"
                                v-model="form.address"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :class="{ 'border-red-300': errors.address }"
                            />
                            <InputError :message="errors.address" />
                        </div>

                        <div class="flex justify-end space-x-2">
                            <TextLink
                                :href="index().url"
                                variant="escape"
                                :showIcon="true"
                            >
                                Отмена
                            </TextLink>
                            <Button
                                type="submit"
                                variant="store"
                                size="lg"
                                :disabled="processing"
                            >
                                {{ processing ? 'Сохранение...' : 'Создать' }}
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
import { store } from '@/actions/App/Http/Controllers/ClientController'
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { index } from '@/routes/clients'

const form = reactive({
    title: '',
    email: '',
    phone: '',
    address: '',
})

const errors = ref({})
const processing = ref(false)

const submit = () => {
    processing.value = true
    const { url, method } = store()

    router[method](url, form, {
        preserveState: true,  // ← важно! сохраняет введенные значения
        onFinish: () => {
            processing.value = false
        },
        onSuccess: () => {
            router.visit(index().url)
        },
        onError: (err) => {
            errors.value = err
        },
    })
}
</script>

<style scoped>
.required::after {
    content: ' *';
    color: #ef4444;
}
</style>
