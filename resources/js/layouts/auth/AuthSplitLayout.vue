<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useAppearance } from '@/composables/useAppearance';
import { Moon, Sun } from 'lucide-vue-next';

const page = usePage();
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
                bg-background text-foreground overflow-hidden">

        <!-- Dot pattern background -->
        <div class="absolute inset-0 z-0 opacity-15" :style="{ 'background-image': `radial-gradient(var(--border) 1px, transparent 1px)`, 'background-size': '40px 40px' }"></div>

        <div class="relative hidden h-full flex-col p-10 lg:flex
                    transition-colors duration-500 ease-in-out
                    bg-card shadow-xl border-r border-border">
            <div class="absolute inset-0 bg-foreground opacity-0 dark:opacity-40 z-0 transition-opacity duration-500"></div>

            <div class="flex flex-row items-center">
                <Link :href="route('home')" class="relative z-20 flex items-center text-2xl font-semibold text-foreground mr-4">
                    <AppLogoIcon class="mr-3 size-10 fill-current text-primary" />
                    <span style="font-family: 'Orbitron', sans-serif;">
                        <span class="text-primary">Land</span>
                        <span class="text-accent">Analysis</span>
                    </span>
                </Link>

                <button
                    @click="toggleTheme"
                    class="cursor-pointer flex items-center justify-center z-20 rounded-full p-2 transition-colors duration-300
                           bg-muted hover:bg-muted/80 text-muted-foreground"
                >
                    <component :is="appearance === 'dark' ? Sun : Moon" class="h-5 w-5" />
                </button>
            </div>

            <div v-if="quote" class="relative z-20 mt-auto mb-10">
                <blockquote class="space-y-4">
                    <p class="text-3xl font-light leading-relaxed text-muted-foreground italic">&ldquo;{{ quote.message }}&rdquo;</p>
                    <footer class="text-lg font-medium text-primary">&mdash; {{ quote.author }}</footer>
                </blockquote>
            </div>

            <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-background to-transparent opacity-70 z-10
                        transition-opacity duration-500"></div>
        </div>

        <div class="lg:p-8 relative z-10 flex items-center justify-center h-full">
            <div class="mx-auto flex w-full flex-col justify-center space-y-8 sm:w-3/5
                        p-10 rounded-xl shadow-2xl border border-border
                        bg-card transition-colors duration-500 ease-in-out"
                 :style="{ 'background-image': `radial-gradient(var(--border) 0.5px, transparent 0.5px)`, 'background-size': '30px 30px' }">
                <div class="flex flex-col space-y-4 text-center">
                    <h1 class="text-3xl font-extrabold tracking-tight text-foreground" v-if="title">{{ title }}</h1>
                    <p class="text-lg text-muted-foreground" v-if="description">{{ description }}</p>
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
