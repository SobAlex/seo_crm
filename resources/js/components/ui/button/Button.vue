<script setup lang="ts">
import type { PrimitiveProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import type { ButtonVariants } from "."
import { Primitive } from "reka-ui"
import { cn } from "@/lib/utils"
import { buttonVariants } from "."
import { Trash2, Plus } from 'lucide-vue-next'

interface Props extends PrimitiveProps {
  variant?: ButtonVariants["variant"]
  size?: ButtonVariants["size"]
  class?: HTMLAttributes["class"]
  showIcon?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  as: "button",
  showIcon: true
})

const getIcon = () => {
  if (!props.showIcon) return null
  if (props.variant === 'delete') return Trash2
  if (props.variant === 'store') return Plus
  return null
}

const IconComponent = getIcon()
</script>

<template>
  <Primitive
    data-slot="button"
    :as="as"
    :as-child="asChild"
    :class="cn(buttonVariants({ variant, size }), props.class)"
  >
    <component
      v-if="IconComponent"
      :is="IconComponent"
      class="w-4 h-4"
    />
    <slot />
  </Primitive>
</template>
