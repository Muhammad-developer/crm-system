<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types';
import { ref, onMounted } from 'vue'
import api from '@/lib/axios'
import ClientForm from '@/components/clients/ClientForm.vue'
import ClientModalForm from '@/components/clients/ClientModalForm.vue';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogFooter,
    DialogTitle,
    DialogOverlay
} from '@/components/ui/dialog'


const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Клиенты', href: '/clients' },
]

const showEditModal = ref(false)
const showModal = ref(false)

interface Client {
    id: number
    name: string
    company: string | null
    email: string | null
    phone: string | null
}

const clients = ref<Client[]>([])
const loading = ref(true)

const editingClient = ref<Client | null>(null)
const editForm = ref({
    name: '',
    company: '',
    email: '',
    phone: '',
})
const updating = ref(false)

const startEditing = (client: Client) => {
    editingClient.value = client
    editForm.value = {
        name: client.name,
        company: client.company ?? '',
        email: client.email ?? '',
        phone: client.phone ?? '',
    }
    showEditModal.value = true
}

const updateClient = async () => {
    if (!editingClient.value) return
    updating.value = true

    try {
        const { data } = await api.put(`/clients/${editingClient.value.id}`, editForm.value)

        // Обновить клиента в списке
        const index = clients.value.findIndex(c => c.id === editingClient.value?.id)
        if (index !== -1) clients.value[index] = data.data

        editingClient.value = null // Закрыть форму
    } catch (e) {
        alert('Ошибка при обновлении клиента')
        console.log(e);
    } finally {
        showEditModal.value = false;
        updating.value = false;
    }
}

const deleteClient = async (id: number) => {
    if (!confirm('Удалить этого клиента?')) return

    try {
        await api.delete(`/clients/${id}`)
        clients.value = clients.value.filter((c) => c.id !== id)
    } catch (e) {
        alert('Ошибка при удалении клиента.');
        console.log(e);
    }
}

onMounted(async () => {
    const { data } = await api.get('/clients')
    clients.value = data.data
    loading.value = false
})
const newClient = ref({
    name: '',
    company: '',
    email: '',
    phone: '',
})

const errors = ref<Record<string, string[]>>({})
const submitting = ref(false)

const submit = async () => {
    errors.value = {}
    submitting.value = true

    try {
        const { data } = await api.post('/clients', newClient.value)
        clients.value.unshift(data.data) // добавим сразу в список
        newClient.value = { name: '', company: '', email: '', phone: '' }
    } catch (e: any) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors
        }
    } finally {
        showModal.value = false;
        submitting.value = false
    }
}

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex justify-end mb-3 mt-3">
            <button
                @click="showModal = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
            >
                + Добавить клиента
            </button>
        </div>

        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Список клиентов</h1>

            <div v-if="loading">Загрузка...</div>
            <table v-else class="min-w-full border text-sm">
                <thead class="bg-gray-100 text-left dark:bg-gray-800">
                <tr>
                    <th class="border px-4 py-2">Имя</th>
                    <th class="border px-4 py-2">Компания</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Телефон</th>
                    <th class="border px-4 py-2">Действия</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="client in clients" :key="client.id">
                    <td class="border px-4 py-2">{{ client.name }}</td>
                    <td class="border px-4 py-2">{{ client.company }}</td>
                    <td class="border px-4 py-2">{{ client.email }}</td>
                    <td class="border px-4 py-2">{{ client.phone }}</td>
                    <td class="border px-4 py-2">
                        <button
                            class="text-blue-600 hover:underline px-1.5"
                            @click="startEditing(client)"
                        >
                            Редактировать
                        </button>
                        <button
                            class="text-red-600 hover:underline px-1.5"
                            @click="deleteClient(client.id)"
                        >
                            Удалить
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <ClientModalForm
            v-model:visible="showEditModal"
            v-model="editForm"
            title="Редактировать клиента"
            :submitting="updating"
            @submit="updateClient"
        />

        <ClientModalForm
            v-model:visible="showModal"
            v-model="newClient"
            title="Добавить клиента"
            :errors="errors"
            :submitting="submitting"
            @submit="submit"
        />

        <Dialog :open="showModal" @update:open="(val: boolean) => showModal = val">
            <DialogOverlay />
            <DialogContent class="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle>Добавить клиента</DialogTitle>
                </DialogHeader>

                <ClientForm
                    v-model="newClient"
                    :errors="errors"
                    :submitting="submitting"
                    @submit="submit"
                >
                    <DialogFooter>
                        <button
                            type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded"
                            :disabled="submitting"
                        >
                            {{ submitting ? 'Добавление...' : 'Сохранить' }}
                        </button>
                    </DialogFooter>
                </ClientForm>
            </DialogContent>
        </Dialog>
        <Dialog :open="showEditModal" @update:open="(val: boolean) => showEditModal = val">
            <DialogContent class="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle>Редактировать клиента</DialogTitle>
                </DialogHeader>

                <ClientForm
                    v-model="editForm"
                    :submitting="updating"
                    @submit="updateClient"
                >
                    <DialogFooter>
                        <button
                            type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded"
                            :disabled="updating"
                        >
                            {{ updating ? 'Сохранение...' : 'Сохранить' }}
                        </button>
                    </DialogFooter>
                </ClientForm>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
