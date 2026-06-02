<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Управление командой</h1>
                        <TextLink :href="create().url" variant="create">
                            Пригласить
                        </TextLink>
                    </div>

                    <!-- Сотрудники -->
                    <h2 class="text-xl font-semibold mb-4">Сотрудники</h2>
                    <table class="min-w-full divide-y divide-gray-200 mb-8">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Имя</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Роль</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in employees" :key="user.id">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ user.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ user.email }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <!-- Для владельца просто текст, для остальных – выпадающий список -->
                                    <span v-if="user.role === 'owner'">Владелец</span>
                                    <select
                                        v-else
                                        :value="user.role"
                                        @change="updateRole(user, $event.target.value)"
                                        class="rounded-md border-gray-300 text-sm"
                                    >
                                        <option value="admin">Администратор</option>
                                        <option value="employee">Сотрудник</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <!-- Кнопка удаления только для не-владельцев -->
                                    <button v-if="user.role !== 'owner'" @click="deleteUser(user)" class="text-red-600 hover:text-red-900">
                                        Удалить
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Подрядчики -->
                    <h2 class="text-xl font-semibold mb-4 mt-8">Подрядчики</h2>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Имя</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Телефон</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="contractor in contractors" :key="contractor.id">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ contractor.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ contractor.email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ contractor.phone || '—' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <button @click="deleteContractor(contractor)" class="text-red-600 hover:text-red-900">
                                        Удалить
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import TextLink from '@/components/TextLink.vue'
import { create } from '@/routes/invitations';

defineProps({
    employees: Array,
    contractors: Array
})

const updateRole = (user, newRole) => {
    router.put(`/admin/team/users/${user.id}/role`, { role: newRole }, {
        preserveState: true,
        onSuccess: () => {
            user.role = newRole
        }
    })
}

const deleteUser = (user) => {
    if (confirm(`Удалить пользователя "${user.name}"?`)) {
        router.delete(`/admin/team/users/${user.id}`, {
            preserveState: true
        })
    }
}

const deleteContractor = (contractor) => {
    if (confirm(`Удалить подрядчика "${contractor.name}"?`)) {
        router.delete(`/admin/team/contractors/${contractor.id}`, {
            preserveState: true
        })
    }
}
</script>
