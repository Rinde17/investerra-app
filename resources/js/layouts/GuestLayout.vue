<script setup lang="ts">
import { Link, Head } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { computed } from 'vue'; // Si tu as besoin de computed pour des liens conditionnels (login/register)
import { usePage } from '@inertiajs/vue3'; // Pour vérifier l'état d'authentification

// Optionnel: Définir des props si tu veux passer des titres, descriptions, etc.
defineProps<{
    breadcrumbs?: Array<{ title: string; href: string }>;
    title?: string;
}>();

const page = usePage();

// Vérifie si l'utilisateur est authentifié pour adapter la navigation
const user = computed(() => page.props.auth?.user); // Assumes 'auth.user' is shared via HandleInertiaRequests

</script>

<template>
    <Head :title="title" />

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('home')">
                                <AppLogoIcon class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                            </Link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <Link :href="route('home')" :class="route().current('home') ? 'border-indigo-400 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700'" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                Accueil
                            </Link>
                            <Link :href="route('pricing.index')" :class="route().current('pricing.index') ? 'border-indigo-400 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700'" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                Tarifs
                            </Link>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <div v-if="user" class="ms-3 relative">
                            <Link
                                :href="route('dashboard')"
                                class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                            >
                                Dashboard
                            </Link>
                        </div>
                        <div v-else class="flex items-center gap-4">
                            <Link
                                :href="route('login')"
                                class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                            >
                                Log in
                            </Link>
                            <Link
                                :href="route('register')"
                                class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                            >
                                Register
                            </Link>
                        </div>
                    </div>

                </div>
            </div>
        </nav>

        <header v-if="breadcrumbs && breadcrumbs.length > 0" class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol role="list" class="flex items-center space-x-4">
                        <li v-for="(item, index) in breadcrumbs" :key="item.href">
                            <div class="flex items-center">
                                <svg v-if="index > 0" class="h-5 w-5 flex-shrink-0 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M5.555 17.776l8-16 .894.447-8 16-.894-.447z" />
                                </svg>
                                <Link :href="item.href" :class="[index === breadcrumbs.length - 1 ? 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' : 'text-gray-900 dark:text-gray-100 hover:text-gray-700 dark:hover:text-gray-300', 'ml-4 text-sm font-medium']">
                                    {{ item.title }}
                                </Link>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </header>


        <main>
            <slot />
        </main>

        <footer class="bg-gray-800 dark:bg-gray-900 text-gray-400 dark:text-gray-500 py-8 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                &copy; {{ new Date().getFullYear() }} Mon Entreprise. Tous droits réservés.
            </div>
        </footer>
    </div>
</template>
