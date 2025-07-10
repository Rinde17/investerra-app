<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useAppearance } from '@/composables/useAppearance';
import { Moon, Sun } from 'lucide-vue-next';

const page = usePage();
const name = page.props.name;
const quote = page.props.quote;

defineProps<{
    title?: string;
    description?: string;
}>();

const { appearance, updateAppearance } = useAppearance();

function toggleTheme() {
    const next = appearance.value === 'dark' ? 'light' : 'dark'
    updateAppearance(next)
}
</script>

<template>
    <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0
                transition-colors duration-500 ease-in-out
                bg-gradient-to-br from-gray-50 via-white to-gray-50 text-gray-800
                dark:from-zinc-950 dark:via-gray-950 dark:to-zinc-950 dark:text-zinc-100 overflow-hidden">

        <div class="absolute inset-0 z-0 opacity-15 bg-pattern-light dark:bg-pattern-dark"></div>

        <div class="relative hidden h-full flex-col p-10 lg:flex
                    transition-colors duration-500 ease-in-out
                    bg-gradient-to-br from-white to-gray-100 shadow-xl border-r border-gray-100
                    dark:from-gray-900 dark:to-black dark:shadow-2xl dark:border-r dark:border-gray-800">
            <div class="absolute inset-0 bg-black opacity-0 dark:opacity-40 z-0 transition-opacity duration-500"></div>

            <div class="flex flex-row items-center">
                <Link :href="route('home')" class="relative z-20 flex items-center text-2xl font-semibold text-gray-800 dark:text-white mr-4">
                    <AppLogoIcon class="mr-3 size-10 fill-current text-indigo-600 dark:text-indigo-400" />
                    <span style="font-family: 'Orbitron', sans-serif;">{{ name || 'Investerra' }}</span>
                </Link>

                <button
                    @click="toggleTheme"
                    class="cursor-pointer flex items-center justify-center z-20 rounded-full p-2 transition-colors duration-300
                           bg-gray-100 hover:bg-gray-200 text-gray-600
                           dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-400"
                >
                    <component :is="appearance === 'dark' ? Sun : Moon" class="h-5 w-5" />
                </button>
            </div>

            <div v-if="quote" class="relative z-20 mt-auto mb-10">
                <blockquote class="space-y-4">
                    <p class="text-3xl font-light leading-relaxed text-gray-600 dark:text-gray-300 italic">&ldquo;{{ quote.message }}&rdquo;</p>
                    <footer class="text-lg font-medium text-indigo-500 dark:text-indigo-300">&mdash; {{ quote.author }}</footer>
                </blockquote>
            </div>

            <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-gray-100 to-transparent opacity-70 z-10
                        dark:from-gray-900 dark:to-transparent dark:opacity-40 transition-opacity duration-500"></div>
        </div>

        <div class="lg:p-8 relative z-10 flex items-center justify-center h-full">
            <div class="mx-auto flex w-full flex-col justify-center space-y-8 sm:w-3/5
                        p-10 rounded-xl shadow-2xl border border-gray-200
                        bg-white dark:border-gray-800 dark:bg-gray-900
                        transition-colors duration-500 ease-in-out card-pattern-light dark:card-pattern-dark">
                <div class="flex flex-col space-y-4 text-center">
                    <h1 class="text-3xl font-extrabold tracking-tight text-gray-800 dark:text-white" v-if="title">{{ title }}</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400" v-if="description">{{ description }}</p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>

<style>
/* Ensure Orbitron font is loaded for consistency */
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap');

/* Custom patterns for background and card */
.bg-pattern-light {
    background-image: radial-gradient(rgba(229, 231, 235, 0.7) 0.5px, transparent 0.5px); /* Very light gray dots, semi-transparent */
    background-size: 30px 30px; /* Smaller dots, more dense */
}

.dark .bg-pattern-dark {
    background-image: radial-gradient(rgba(30, 30, 30, 0.7) 0.5px, transparent 0.5px); /* Darker gray dots, semi-transparent */
    background-size: 30px 30px;
}

.card-pattern-light {
    background-image: radial-gradient(rgba(240, 240, 240, 0.8) 0.5px, transparent 0.5px); /* Slightly visible dots on card for light mode */
    background-size: 30px 30px;
}

.dark .card-pattern-dark {
    background-image: radial-gradient(rgba(40, 40, 40, 0.8) 0.5px, transparent 0.5px); /* Dots for dark mode card */
    background-size: 30px 30px;
}
</style>
