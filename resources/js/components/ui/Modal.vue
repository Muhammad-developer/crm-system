<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="$emit('close')"
    >
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-lg">
            <slot />
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{ show: boolean }>()
defineEmits(['close'])
</script>
