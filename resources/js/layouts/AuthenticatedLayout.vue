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


// Use props to pass the title to a Head component
const pageTitle = computed(() => props.title || 'LandAnalysis');
</script>

<template>
    <Head :title="pageTitle">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    </Head>

    <!-- Authenticated Layout with Sidebar -->
    <div class="min-h-screen bg-background text-foreground">
        <!-- Dot pattern background -->
        <div class="absolute inset-0 z-0 opacity-10" :style="{ 'background-image': `radial-gradient(var(--border) 1px, transparent 1px)`, 'background-size': '40px 40px' }"></div>

        <!-- Main Layout with Sidebar -->
        <AppSidebarLayout :breadcrumbs="breadcrumbs">
            <slot />
        </AppSidebarLayout>
    </div>

    <ToastNotification />
</template>
