<script setup lang="ts">
import { Link, Head, usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { computed } from 'vue';
import type { BreadcrumbItemType } from '@/types';
import { Moon, Sun } from 'lucide-vue-next';
import { useAppearance } from '@/composables/useAppearance';

// Define props for title, description, and breadcrumbs
interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    title?: string;
    description?: string;
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);

// Check if a user is authenticated to adapt navigation
const user = computed(() => auth.value?.user);
const subscription = computed(() => auth.value?.subscription);
const hasActiveSubscription = computed(() => subscription.value?.stripe_status === 'active');

// Check if user is authenticated AND has an active subscription
const isAuthenticatedAndSubscribed = computed(() => {
    return user.value && hasActiveSubscription.value;
});

// Use props to pass title to a Head component
const pageTitle = computed(() => props.title || 'Investerra');

const { appearance, updateAppearance } = useAppearance();

function toggleTheme() {
    const next = appearance.value === 'dark' ? 'light' : 'dark'
    updateAppearance(next)
}
</script>

<template>
    <Head :title="pageTitle">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    </Head>

    <div class="min-h-screen flex flex-col bg-gradient-to-br from-[#f8f8f8] via-[#f0f0f0] to-[#f8f8f8] dark:from-[#0a0a0a] dark:via-[#100c1c] dark:to-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
        <!-- Dot pattern background -->
        <div class="absolute inset-0 z-0 opacity-10" style="background-image: radial-gradient(#202020 1px, transparent 1px); background-size: 40px 40px;"></div>

        <!-- Header -->
        <header class="relative z-10 w-full border-b border-gray-200 dark:border-[#3E3E3A] bg-white/80 dark:bg-[#161615]/80 backdrop-blur-sm">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link :href="route('home')" class="flex items-center">
                            <AppLogoIcon class="mr-3 size-9 fill-current text-indigo-600 dark:text-indigo-400" />
                            <span class="text-xl font-semibold" style="font-family: 'Orbitron', sans-serif;">Investerra</span>
                        </Link>
                    </div>

                    <!-- Navigation -->
                    <nav class="hidden md:flex items-center space-x-8">
                        <Link :href="route('home')" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">
                            Accueil
                        </Link>
                        <Link :href="route('pricing.index')" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">
                            Tarifs
                        </Link>
                        <Link :href="route('example')" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">
                            Example
                        </Link>
                        <Link href="#" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">
                            Contact
                        </Link>
                        <Link href="#" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">
                            Infos
                        </Link>
                    </nav>

                    <!-- Auth Buttons -->
                    <div class="flex items-center space-x-4">
                        <!-- Dark/Light Mode Toggle Button -->
                        <button
                            @click="toggleTheme"
                            :class="[
                            'flex items-center justify-center rounded-md p-2 transition-colors',
                            'hover:bg-neutral-200/60 dark:hover:bg-neutral-700/60',
                            'text-neutral-500 dark:text-neutral-400'
                        ]"
                        >
                            <component :is="appearance === 'dark' ? Sun : Moon" class="h-4 w-4" />
                        </button>

                        <!-- User is authenticated but not subscribed -->
                        <template v-if="user && !isAuthenticatedAndSubscribed">
                            <Link :href="route('pricing.index')" class="inline-block rounded-md border border-indigo-500 dark:border-indigo-400 px-5 py-2 text-sm leading-normal text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors duration-300">
                                S'abonner
                            </Link>
                            <Link :href="route('dashboard')" class="inline-block rounded-md border border-gray-300 dark:border-[#3E3E3A] px-5 py-2 text-sm leading-normal hover:bg-gray-100 dark:hover:bg-[#252525] transition-colors duration-300">
                                Dashboard
                            </Link>
                        </template>
                        <!-- User is authenticated and subscribed -->
                        <template v-else-if="isAuthenticatedAndSubscribed">
                            <Link :href="route('dashboard')" class="inline-block rounded-md border border-gray-300 dark:border-[#3E3E3A] px-5 py-2 text-sm leading-normal hover:bg-gray-100 dark:hover:bg-[#252525] transition-colors duration-300">
                                Dashboard
                            </Link>
                        </template>
                        <!-- User is not authenticated -->
                        <template v-else>
                            <Link :href="route('login')" class="inline-block rounded-md border border-transparent px-5 py-2 text-sm leading-normal hover:bg-gray-100 dark:hover:bg-[#252525] transition-colors duration-300">
                                Connexion
                            </Link>
                            <Link :href="route('register')" class="inline-block rounded-md bg-indigo-600 px-5 py-2 text-sm leading-normal text-white hover:bg-indigo-700 transition-colors duration-300">
                                S'inscrire
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow relative z-10">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="relative z-10 w-full py-8 bg-white/80 dark:bg-[#161615]/80 backdrop-blur-sm border-t border-gray-200 dark:border-[#3E3E3A]">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-gray-600 dark:text-gray-400">&copy; {{ new Date().getFullYear() }} Investerra. Tous droits réservés.</p>
            </div>
        </footer>
    </div>
</template>
