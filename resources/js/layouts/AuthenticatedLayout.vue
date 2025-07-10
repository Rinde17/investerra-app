<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import ToastNotification from '@/components/ToastNotification.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    title?: string;
    description?: string;
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});


// Use props to pass title to a Head component
const pageTitle = computed(() => props.title || 'Investerra');
</script>

<template>
    <Head :title="pageTitle">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    </Head>

    <!-- Authenticated Layout with Sidebar -->
    <div class="min-h-screen bg-gradient-to-br from-[#f8f8f8] via-[#f0f0f0] to-[#f8f8f8] dark:from-[#0a0a0a] dark:via-[#100c1c] dark:to-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
        <!-- Dot pattern background -->
        <div class="absolute inset-0 z-0 opacity-10" style="background-image: radial-gradient(#202020 1px, transparent 1px); background-size: 40px 40px;"></div>

        <!-- Main Layout with Sidebar -->
        <AppSidebarLayout :breadcrumbs="breadcrumbs">
            <slot />
        </AppSidebarLayout>
    </div>

    <ToastNotification />
</template>
