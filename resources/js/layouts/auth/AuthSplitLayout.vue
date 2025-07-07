<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const name = page.props.name;
const quote = page.props.quote;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0
                bg-gradient-to-br from-[#0a0a0a] via-[#100c1c] to-[#0a0a0a] text-[#EDEDEC] overflow-hidden">

        <div class="absolute inset-0 z-0 opacity-10" style="background-image: radial-gradient(#202020 1px, transparent 1px); background-size: 40px 40px;"></div>

        <div class="relative hidden h-full flex-col p-10 lg:flex
                    bg-gradient-to-br from-[#161615] to-[#100c1c] shadow-lg border-r border-[#3E3E3A]">
            <div class="absolute inset-0 bg-black opacity-30 z-0"></div> <Link :href="route('home')" class="relative z-20 flex items-center text-xl font-semibold text-white">
            <AppLogoIcon class="mr-3 size-9 fill-current text-indigo-400" />
            <span style="font-family: 'Orbitron', sans-serif;">{{ name || 'Investerra' }}</span>
        </Link>

            <div v-if="quote" class="relative z-20 mt-auto mb-10">
                <blockquote class="space-y-4">
                    <p class="text-3xl font-light leading-relaxed text-gray-200 italic">&ldquo;{{ quote.message }}&rdquo;</p>
                    <footer class="text-lg font-medium text-indigo-300">&mdash; {{ quote.author }}</footer>
                </blockquote>
            </div>

            <div class="absolute bottom-0 left-0 w-full h-24 bg-gradient-to-t from-black to-transparent opacity-20 z-10"></div>
        </div>

        <div class="lg:p-8 relative z-10 flex items-center justify-center h-full">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-3/5
                        p-8 rounded-lg shadow-xl border border-[#3E3E3A]" style="background-image: radial-gradient(#202020 1px, transparent 1px); background-size: 40px 40px;">
                <div class="flex flex-col space-y-3 text-center">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-200 dark:text-white" v-if="title">{{ title }}</h1>
                    <p class="text-lg text-gray-200 dark:text-gray-400" v-if="description">{{ description }}</p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>

<style>
/* Ensure Orbitron font is loaded for consistency */
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap');
</style>
