<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

interface Props {
  modelValue?: string
  rows?: number
  error?: string
  class?: HTMLAttributes['class']
}

defineProps<Props>()
defineEmits(['update:modelValue'])
</script>

<template>
  <textarea
    :value="modelValue"
    :rows="rows"
    @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
    :class="cn(
      'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
      error && 'border-red-500 focus:border-red-500 focus:ring-red-500',
      props.class
    )"
  />
</template>
