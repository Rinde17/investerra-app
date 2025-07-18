<script setup lang="ts">
import { Link, Head, usePage, router } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { computed } from 'vue';
import type { BreadcrumbItemType } from '@/types';
import { LogOut, Moon, Sun } from 'lucide-vue-next';
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

// Check if the user is authenticated AND has an active subscription
const isAuthenticatedAndSubscribed = computed(() => {
    return user.value && hasActiveSubscription.value;
});

// Use props to pass the title to a Head component
const pageTitle = computed(() => props.title || 'LandAnalysis');

const { appearance, updateAppearance } = useAppearance();

function toggleTheme() {
    const next = appearance.value === 'dark' ? 'light' : 'dark'
    updateAppearance(next)
}

const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <Head :title="pageTitle">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    </Head>

    <div class="min-h-screen flex flex-col bg-background text-foreground">
        <!-- Dot pattern background -->
        <div class="absolute inset-0 z-0 opacity-10" :style="{ 'background-image': `radial-gradient(var(--foreground) 1px, transparent 1px)`, 'background-size': '40px 40px' }"></div>

        <!-- Header -->
        <header class="relative z-10 w-full border-b border-border bg-background/80 backdrop-blur-sm">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link :href="route('home')" class="flex items-center">
                            <AppLogoIcon class="mr-3 size-9 fill-current text-primary" />
                            <span class="text-xl font-semibold" style="font-family: 'Orbitron', sans-serif;">
                                <span class="text-primary">Land</span>
                                <span class="text-accent">Analysis</span>
                            </span>
                        </Link>
                    </div>

                    <!-- Navigation -->
                    <nav class="hidden md:flex items-center space-x-8">
                        <Link :href="route('home')" class="text-foreground hover:text-primary font-medium">
                            Accueil
                        </Link>
                        <Link :href="route('pricing.index')" class="text-foreground hover:text-primary font-medium">
                            Tarifs
                        </Link>
                        <Link :href="route('contact')" class="text-foreground hover:text-primary font-medium">
                            Contact
                        </Link>
                        <Link :href="route('about')" class="text-foreground hover:text-primary font-medium">
                            À Propos
                        </Link>
                    </nav>

                    <!-- Auth Buttons -->
                    <div class="flex items-center space-x-4">
                        <!-- Dark/Light Mode Toggle Button -->
                        <button
                            @click="toggleTheme"
                            :class="[
                            'flex items-center justify-center rounded-md p-2 transition-colors',
                            'hover:bg-muted/60',
                            'text-muted-foreground'
                        ]"
                        >
                            <component :is="appearance === 'dark' ? Sun : Moon" class="h-4 w-4" />
                        </button>

                        <!-- User is authenticated but not subscribed -->
                        <template v-if="user && !isAuthenticatedAndSubscribed">
                            <Link :href="route('pricing.index')" class="inline-block rounded-md border border-primary px-5 py-2 text-sm leading-normal text-primary hover:bg-primary/10 transition-colors duration-300">
                                S'abonner
                            </Link>
                            <Link class="cursor-pointer inline-block rounded-md bg-primary px-5 py-2 text-sm leading-normal text-primary-foreground hover:bg-primary/90 transition-colors duration-300" method="post" :href="route('logout')" @click="handleLogout">
                                Déconnexion
                            </Link>
                        </template>
                        <!-- User is authenticated and subscribed -->
                        <template v-else-if="isAuthenticatedAndSubscribed">
                            <Link :href="route('dashboard')" class="inline-block rounded-md border border-border px-5 py-2 text-sm leading-normal hover:bg-muted transition-colors duration-300">
                                Dashboard
                            </Link>
                            <Link class="cursor-pointer inline-block rounded-md bg-primary px-5 py-2 text-sm leading-normal text-primary-foreground hover:bg-primary/90 transition-colors duration-300" method="post" :href="route('logout')" @click="handleLogout">
                                Déconnexion
                            </Link>
                        </template>
                        <!-- User is not authenticated -->
                        <template v-else>
                            <Link :href="route('login')" class="inline-block rounded-md border border-transparent px-5 py-2 text-sm leading-normal hover:bg-muted transition-colors duration-300">
                                Connexion
                            </Link>
                            <Link :href="route('register')" class="inline-block rounded-md bg-primary px-5 py-2 text-sm leading-normal text-primary-foreground hover:bg-primary/90 transition-colors duration-300">
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
        <footer class="relative z-10 w-full py-8 bg-background/80 backdrop-blur-sm border-t border-border">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-muted-foreground">&copy; {{ new Date().getFullYear() }} LandAnalysis. Tous droits réservés.</p>
            </div>
        </footer>
    </div>
</template>
