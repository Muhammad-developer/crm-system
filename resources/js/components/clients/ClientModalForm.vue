<script setup lang="ts">
import Modal from '@/components/ui/Modal.vue'
import ClientForm from './ClientForm.vue'

interface Props {
    modelValue: {
        name: string
        company: string
        email: string
        phone: string
    }
    visible: boolean
    title: string
    errors?: Record<string, string[]>
    submitting: boolean
}
import { computed } from 'vue'

const form = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
})

const props = defineProps<Props>()
const emit = defineEmits(['update:modelValue', 'update:visible', 'submit'])

const close = () => emit('update:visible', false)
</script>

<template>
    <Modal :show="visible" @close="close">
        <h2 class="text-lg font-bold mb-4">{{ title }}</h2>
        <ClientForm
            v-model="form"
            :errors="errors"
            :submitting="submitting"
            @submit="emit('submit')"
        >
            <button type="button" class="text-gray-500" @click="close">Отмена</button>
            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                :disabled="submitting"
            >
                {{ submitting ? 'Сохранение...' : 'Сохранить' }}
            </button>
        </ClientForm>
    </Modal>
</template>
