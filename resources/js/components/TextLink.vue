<script setup lang="ts">
import type { LinkComponentBaseProps, Method } from '@inertiajs/core';
import { Link } from '@inertiajs/vue3';
import { cva } from "class-variance-authority";
import { cn } from "@/lib/utils";
import { Pencil, Eye, ArrowBigLeft, Plus } from 'lucide-vue-next';

type Props = {
    href: LinkComponentBaseProps['href'];
    tabindex?: number;
    method?: Method;
    as?: string;
    variant?: 'default' | 'button' | 'ghost' | 'outline' |  'show' | 'edit' | 'escape' | 'create';
    showIcon?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    variant: 'default',
    showIcon: true
});

const linkVariants = cva(
    'transition-colors duration-300 ease-out inline-flex items-center gap-1.5',
    {
        variants: {
            variant: {
                default: 'text-foreground underline decoration-neutral-300 underline-offset-4 hover:decoration-current! dark:decoration-neutral-500',
                button: 'px-4 py-2 text-indigo-600 hover:bg-indigo-50 rounded-md no-underline',
                ghost: 'px-4 py-2 text-indigo-600 hover:bg-indigo-50 rounded-md no-underline',
                outline: 'px-4 py-2 border border-indigo-600 text-indigo-600 rounded-md hover:bg-indigo-50 no-underline',
                create: 'px-4 py-2 text-indigo-600 hover:bg-indigo-50 rounded-md no-underline',
                show: 'px-4 py-2 text-indigo-600 hover:bg-indigo-50 rounded-md no-underline',
                edit: 'px-4 py-2 text-indigo-600 hover:bg-indigo-50 rounded-md no-underline',
                escape: 'px-4 py-2 text-indigo-600 hover:bg-indigo-50 rounded-md no-underline',
            }
        },
        defaultVariants: {
            variant: 'default'
        }
    }
);

const getIcon = () => {
    if (!props.showIcon) return null;
    if (props.variant === 'create') return Plus;
    if (props.variant === 'edit') return Pencil;
    if (props.variant === 'show') return Eye;
    if (props.variant === 'escape') return ArrowBigLeft;
    return null;
};

const IconComponent = getIcon();
</script>

<template>
    <Link
        :href="href"
        :tabindex="tabindex"
        :method="method"
        :as="as"
        :class="cn(linkVariants({ variant }))"
    >
        <component
            v-if="IconComponent"
            :is="IconComponent"
            class="w-4 h-4"
        />
        <slot />
    </Link>
</template>
