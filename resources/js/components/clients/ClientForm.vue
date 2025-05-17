<script setup lang="ts">
import { ref, watch } from 'vue'

interface Props {
    modelValue: {
        name: string
        company: string
        email: string
        phone: string
    }
    submitting: boolean
    errors?: Record<string, string[]>
}

const emit = defineEmits(['update:modelValue', 'submit'])

const props = defineProps<Props>()

const localForm = ref({ ...props.modelValue })

watch(
    () => props.modelValue,
    (newVal) => {
        localForm.value = { ...newVal }
    },
    { deep: true, immediate: true }
)

watch(
    localForm,
    (val) => {
        emit('update:modelValue', val)
    },
    { deep: true }
)

const onSubmit = () => emit('submit')
</script>

<template>
    <form @submit.prevent="onSubmit" class="grid gap-4">
        <div>
            <input v-model="localForm.name" placeholder="Имя" class="w-full border p-2 rounded" />
            <p class="text-red-500 text-sm" v-if="errors?.name">{{ errors.name[0] }}</p>
        </div>
        <div>
            <input v-model="localForm.company" placeholder="Компания" class="w-full border p-2 rounded" />
            <p class="text-red-500 text-sm" v-if="errors?.company">{{ errors.company[0] }}</p>
        </div>
        <div>
            <input v-model="localForm.email" placeholder="Email" class="w-full border p-2 rounded" />
            <p class="text-red-500 text-sm" v-if="errors?.email">{{ errors.email[0] }}</p>
        </div>
        <div>
            <input v-model="localForm.phone" placeholder="Телефон" class="w-full border p-2 rounded" />
            <p class="text-red-500 text-sm" v-if="errors?.phone">{{ errors.phone[0] }}</p>
        </div>

        <div class="flex justify-end gap-2">
            <slot />
        </div>
    </form>
</template>
