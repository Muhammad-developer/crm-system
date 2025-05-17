<script setup lang="ts">
import { computed } from 'vue'

const localModel = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
})

interface Props {
    modelValue: {
        title: string
        client_id: number | null
        amount: number
        status: string
        due_date: string
    }
    submitting: boolean
    errors?: Record<string, string[]>
    clients: { id: number; name: string }[]
}

const emit = defineEmits(['update:modelValue', 'submit'])
const props = defineProps<Props>()

const update = (field: string, value: any) => {
    emit('update:modelValue', { ...props.modelValue, [field]: value })
}
</script>

<template>
    <form @submit.prevent="emit('submit')" class="grid gap-4">
        <div>
            <input
                v-model="localModel.title"
                placeholder="Название сделки"
                class="w-full border p-2 rounded"
            />
            <p class="text-red-500 text-sm" v-if="errors?.title">{{ errors.title[0] }}</p>
        </div>

        <div>
            <select
                class="w-full border p-2 rounded bg-background dark:bg-background"
                :value="modelValue.client_id"
                @change="update('client_id', +($event.target as HTMLSelectElement).value)"
            >
                <option value="" disabled>Выберите клиента</option>
                <option v-for="client in clients" :key="client.id" :value="client.id">
                    {{ client.name }}
                </option>
            </select>
            <p class="text-red-500 text-sm" v-if="errors?.client_id">{{ errors.client_id[0] }}</p>
        </div>

        <div>
            <input
                type="number"
                min="0"
                v-model="localModel.amount"
                placeholder="Сумма сделки"
                class="w-full border p-2 rounded"
            />
            <p class="text-red-500 text-sm" v-if="errors?.amount">{{ errors.amount[0] }}</p>
        </div>

        <div>
            <select
                class="w-full border p-2 rounded bg-background dark:bg-background"
                :value="modelValue.status"
                @change="update('status', ($event.target as HTMLSelectElement).value)"
            >
                <option value="new">Новая</option>
                <option value="in_progress">В работе</option>
                <option value="won">Выиграна</option>
                <option value="lost">Проиграна</option>
            </select>
            <p class="text-red-500 text-sm" v-if="errors?.status">{{ errors.status[0] }}</p>
        </div>

        <div>
            <input
                type="date"
                v-model="localModel.due_date"
                class="w-full border p-2 rounded"
            />
            <p class="text-red-500 text-sm" v-if="errors?.due_date">{{ errors.due_date[0] }}</p>
        </div>

        <div class="flex justify-end gap-2">
            <slot />
        </div>
    </form>
</template>
