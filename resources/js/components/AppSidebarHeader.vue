<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { Moon, Sun } from 'lucide-vue-next';
import { useAppearance } from '@/composables/useAppearance';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const { appearance, updateAppearance } = useAppearance();

function toggleTheme() {
    const next = appearance.value === 'dark' ? 'light' : 'dark'
    updateAppearance(next)
}
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />

                <!-- Dark/Light Mode Toggle Button -->
                <button
                    @click="toggleTheme"
                    :class="[
                        'flex items-center justify-center rounded-md p-2 transition-colors',
                        'hover:bg-muted/60', /* Using muted for hover background */
                        'text-muted-foreground' /* Using muted-foreground for text color */
                    ]"
                >
                    <component :is="appearance === 'dark' ? Sun : Moon" class="h-4 w-4" />
                </button>
            </template>
        </div>
    </header>
</template>
