<template>
    <div class="py-12">
        <div class="mx-auto max-w-md">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-4">Пригласить пользователя</h1>

                <form @submit.prevent="submit">
                    <div class="mt-4">
                        <Label for="email">Email *</Label>
                        <Input id="email" v-model="form.email" type="email" />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="mt-4">
                        <Label for="role">Роль *</Label>
                        <select id="role" v-model="form.role" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="employee">Сотрудник</option>
                            <option value="contractor">Подрядчик</option>
                        </select>
                        <InputError :message="errors.role" />
                    </div>

                    <Button type="submit" class="mt-4 w-full" :disabled="processing">
                        {{ processing ? 'Отправка...' : 'Отправить приглашение' }}
                    </Button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'

const form = reactive({
    email: '',
    role: 'employee',
})

const errors = ref({})
const processing = ref(false)

const submit = () => {
    processing.value = true
    router.post('/invitations', form, {
        onFinish: () => (processing.value = false),
        onError: (err) => (errors.value = err),
    })
}
</script>
