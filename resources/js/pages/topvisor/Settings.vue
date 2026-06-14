<template>
    <AppLayout title="Настройки Topvisor">
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h1 class="text-2xl font-bold mb-4">Настройки Topvisor</h1>
                        <p class="text-gray-600 mb-6">
                            Укажите данные вашего аккаунта Topvisor для отслеживания позиций сайтов.
                            Эти настройки будут использоваться для всех сотрудников компании.
                        </p>

                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <Label for="topvisor_user_id" class="required">Topvisor User ID</Label>
                                <Input
                                    id="topvisor_user_id"
                                    v-model="form.topvisor_user_id"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :error="errors.topvisor_user_id"
                                />
                                <InputError :message="errors.topvisor_user_id" />
                                <p class="text-sm text-gray-500 mt-1">
                                    Ваш ID пользователя Topvisor. <a href="https://topvisor.com/account/" target="_blank" class="text-indigo-600 hover:underline">Найти в настройках аккаунта</a>
                                </p>
                            </div>

                            <div class="mb-4">
                                <Label for="topvisor_api_key" class="required">API Key</Label>
                                <Input
                                    id="topvisor_api_key"
                                    v-model="form.topvisor_api_key"
                                    type="password"
                                    class="mt-1 block w-full"
                                    :error="errors.topvisor_api_key"
                                />
                                <InputError :message="errors.topvisor_api_key" />
                                <p class="text-sm text-gray-500 mt-1">
                                    Секретный ключ API. <a href="https://topvisor.com/account/api/" target="_blank" class="text-indigo-600 hover:underline">Создать и скопировать</a>
                                </p>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <Button type="submit" variant="store" size="lg" :disabled="processing">
                                    {{ processing ? 'Сохранение...' : 'Сохранить' }}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';

const props = defineProps({
    topvisorUserId: String,
    topvisorApiKey: String,
});

const form = useForm({
    topvisor_user_id: props.topvisorUserId || '',
    topvisor_api_key: '',
});

const errors = form.errors;
const processing = form.processing;

const submit = () => {
    form.put('/settings/topvisor', {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>
