<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4">Редактирование клиента</h1>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="title">Название *</Label>
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
                            />
                            <InputError :message="errors.address" />
                        </div>

                        <div class="flex justify-end space-x-2">
                            <TextLink
                                :href="show(client.id).url"
                                variant="escape"
                                :showIcon="true"
                            >
                                Отмена
                            </TextLink>
                            <Button
                                type="submit"
                                variant="default"
                                :disabled="processing"
                            >
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
import { update } from '@/actions/App/Http/Controllers/ClientController'
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { index, show } from '@/routes/clients'

const props = defineProps({
    client: Object
})

const form = reactive({
    title: props.client.title,
    email: props.client.email,
    phone: props.client.phone,
    address: props.client.address,
})

const errors = ref({})
const processing = ref(false)

const submit = () => {
    processing.value = true
    const { url, method } = update(props.client.id)

    router[method](url, form, {
        preserveState: false,
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
