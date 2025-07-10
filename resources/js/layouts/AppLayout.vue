<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import ToastNotification from '@/components/ToastNotification.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    title?: string;
}

defineProps<Props>();

// For dark/light mode toggle
const isDarkMode = ref(document.documentElement.classList.contains('dark'));

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};
</script>

<template>
    <Head :title="title">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-[#f8f8f8] via-[#f0f0f0] to-[#f8f8f8] dark:from-[#0a0a0a] dark:via-[#100c1c] dark:to-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
        <!-- Dot pattern background -->
        <div class="absolute inset-0 z-0 opacity-10" style="background-image: radial-gradient(#202020 1px, transparent 1px); background-size: 40px 40px;"></div>

        <!-- Dark/Light Mode Toggle Button -->
        <button @click="toggleDarkMode" class="fixed top-4 right-4 z-50 p-2 rounded-full bg-white/80 dark:bg-gray-800/80 hover:bg-gray-200 dark:hover:bg-gray-700 shadow-md">
            <svg v-if="isDarkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-800 dark:text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-800 dark:text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>

        <!-- Main Layout with Sidebar -->
        <AppSidebarLayout :breadcrumbs="breadcrumbs">
            <slot />
        </AppSidebarLayout>
    </div>

    <ToastNotification />
</template>
