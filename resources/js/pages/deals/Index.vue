<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { ref, onMounted } from 'vue';
import api from '@/lib/axios';
import DealForm from '@/components/deals/DealForm.vue';
import { useToast } from 'vue-toastification';
import { Dialog, DialogTrigger, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';

const showCreateModal = ref(false);
const newDeal = ref({
    title: '',
    client_id: null,
    amount: 0,
    status: 'new',
    due_date: '',
});
const submitting = ref(false);
const errors = ref<Record<string, string[]>>({});

const toast = useToast();

const submit = async () => {
    submitting.value = true;
    errors.value = {};

    try {
        const { data } = await api.post('/deals', newDeal.value);
        deals.value.unshift(data.data);
        toast.success('Сделка добавлена');
        showCreateModal.value = false;
        newDeal.value = { title: '', client_id: null, amount: 0, status: 'new', due_date: '' };
    } catch (e: any) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors;
        } else {
            toast.error('Ошибка при добавлении сделки');
        }
    } finally {
        submitting.value = false;
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Сделки', href: '/deals' },
];

interface Deal {
    id: number;
    title: string;
    amount: number;
    status: string;
    due_date: string;
    client: {
        id: number;
        name: string;
    };
}

const deals = ref<Deal[]>([]);
const loading = ref(true);
const clientsList = ref<{ id: number; name: string }[]>([]);

const showEditModal = ref(false);
const editingDeal = ref<Deal | null>(null);
const editForm = ref({ ...newDeal.value }); // начальное значение

const startEditing = (deal: Deal) => {
    editingDeal.value = deal;
    editForm.value = {
        title: deal.title,
        client_id: deal.client.id,
        amount: deal.amount,
        status: deal.status,
        due_date: deal.due_date?.slice(0, 10) || '',
    };
    showEditModal.value = true;
};

const updating = ref(false);

const updateDeal = async () => {
    if (!editingDeal.value) return;
    updating.value = true;

    try {
        const { data } = await api.put(`/deals/${editingDeal.value.id}`, editForm.value);

        // обновляем в списке
        const index = deals.value.findIndex((d) => d.id === editingDeal.value?.id);
        if (index !== -1) deals.value[index] = data.data;

        toast.success('Сделка обновлена');
        showEditModal.value = false;
        editingDeal.value = null;
    } catch (e) {
        toast.error('Ошибка при обновлении сделки');
        console.log(e);
    } finally {
        updating.value = false;
    }
};

const deleteDeal = async (id: number) => {
    if (!confirm('Удалить эту сделку?')) return

    try {
        await api.delete(`/deals/${id}`)
        deals.value = deals.value.filter(d => d.id !== id)
        toast.success('Сделка удалена')
    } catch (e) {
        toast.error('Ошибка при удалении сделки')
        console.error(e)
    }
}

onMounted(async () => {
    const [dealsRes, clientsRes] = await Promise.all([api.get('/deals'), api.get('/clients')]);
    deals.value = dealsRes.data.data;
    clientsList.value = clientsRes.data.data;
    loading.value = false;
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex justify-end mb-3 mt-3">
            <button
                @click="showCreateModal = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
            >
                + Добавить сделку
            </button>
        </div>
        <div class="p-6">
            <h1 class="mb-4 text-2xl font-bold">Список сделок</h1>
            <div v-if="loading">Загрузка...</div>
            <table v-else class="min-w-full border text-sm">
                <thead class="bg-gray-100 text-left dark:bg-gray-800">
                    <tr>
                        <th class="border px-4 py-2">Название</th>
                        <th class="border px-4 py-2">Клиент</th>
                        <th class="border px-4 py-2">Сумма</th>
                        <th class="border px-4 py-2">Статус</th>
                        <th class="border px-4 py-2">Дедлайн</th>
                        <th class="border px-4 py-2">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="deal in deals" :key="deal.id">
                        <td class="border px-4 py-2">{{ deal.title }}</td>
                        <td class="border px-4 py-2">{{ deal.client.name }}</td>
                        <td class="border px-4 py-2">{{ deal.amount }} сом</td>
                        <td class="border px-4 py-2 capitalize">
                            <span
                                class="rounded px-2 py-1 text-xs font-semibold"
                                :class="{
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': deal.status === 'new',
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': deal.status === 'in_progress',
                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': deal.status === 'won',
                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': deal.status === 'lost',
                                }"
                            >
                                {{ deal.status.replace('_', ' ') }}
                            </span>
                        </td>
                        <td class="border px-4 py-2">
                            {{ new Date(deal.due_date).toLocaleDateString() }}
                        </td>
                        <td class="border px-4 py-2 flex gap-2">
                            <button
                                @click="startEditing(deal)"
                                class="text-blue-600 hover:underline text-sm"
                            >
                                Редактировать
                            </button>

                            <button
                                @click="deleteDeal(deal.id)"
                                class="text-red-600 hover:underline text-sm"
                            >
                                Удалить
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Dialog :open="showCreateModal" @update:open="(val: boolean) => (showCreateModal = val)">
            <DialogContent class="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>Новая сделка</DialogTitle>
                </DialogHeader>

                <DealForm v-model="newDeal" :submitting="submitting" :errors="errors" :clients="clientsList" @submit="submit">
                    <DialogFooter>
                        <button type="submit" class="rounded bg-green-600 px-4 py-2 text-white" :disabled="submitting">Сохранить</button>
                    </DialogFooter>
                </DealForm>
            </DialogContent>
        </Dialog>
        <Dialog :open="showEditModal" @update:open="(val: boolean) => showEditModal = val">
            <DialogContent class="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>Редактировать сделку</DialogTitle>
                </DialogHeader>

                <DealForm
                    v-model="editForm"
                    :submitting="updating"
                    :clients="clientsList"
                    @submit="updateDeal"
                >
                    <DialogFooter>
                        <button
                            type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded"
                            :disabled="updating"
                        >
                            Сохранить
                        </button>
                    </DialogFooter>
                </DealForm>
            </DialogContent>
        </Dialog>

    </AppLayout>
</template>
