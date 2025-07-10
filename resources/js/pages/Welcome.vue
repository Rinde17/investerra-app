<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';

const showHeroContent = ref(false);
const showInvesterraSection = ref(false); // New ref for the Investerra section

onMounted(() => {
    // Reveal hero content after a short delay
    setTimeout(() => {
        showHeroContent.value = true;
    }, 100);

    // Reveal an Investerra section after a slightly longer delay or bagsed on scroll (for this example, time-based)
    setTimeout(() => {
        showInvesterraSection.value = true;
    }, 500); // Adjust delay as needed
});
</script>

<template>
    <GuestLayout title="Home">
        <div class="grid grid-cols-6 gap-4 justify-items-stretch">
            <div class="col-span-4 col-start-2">
                <section
                    :class="['text-center mb-20 mt-20 transition-all duration-1000 ease-out', showHeroContent ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <h1 class="text-6xl md:text-8xl font-extrabold text-gray-900 leading-tight mb-4 tracking-tight dark:text-gray-300" style="font-family: 'Orbitron', sans-serif;">
                        Élevez votre Stratégie <span class="text-indigo-500">Financière</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-500 dark:text-gray-400 max-w-3xl mx-auto mb-8">
                        Des outils d'analyse avancés pour des décisions d'investissement éclairées. Prenez le contrôle de votre avenir financier.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <Link
                            :href="route('pricing.index')"
                            class="inline-block px-8 py-4 bg-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:bg-indigo-700 transform hover:scale-105 transition-all duration-300"
                        >
                            Voir nos Plans
                        </Link>
                        <Link
                            :href="route('dashboard')"
                            v-if="$page.props.auth.user"
                            class="inline-block px-8 py-4 border border-gray-600 text-gray-500 dark:text-gray-200 font-semibold rounded-lg shadow-lg hover:bg-gray-800 hover:text-gray-200 transform hover:scale-105 transition-all duration-300"
                        >
                            Accéder au Tableau de Bord
                        </Link>
                        <Link
                            :href="route('login')"
                            v-else
                            class="inline-block px-8 py-4 border border-gray-600 text-gray-300 font-semibold rounded-lg shadow-lg hover:bg-gray-800 transform hover:scale-105 transition-all duration-300"
                        >
                            Commencer Gratuitement
                        </Link>
                    </div>
                </section>

                <section
                    :class="['w-full py-12 transition-all duration-1000 delay-200 ease-out', showHeroContent ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="dark:bg-[#161615] bg-gray-200 p-8 rounded-lg shadow-xl border border-gray-400 dark:border-gray-600 hover:border-indigo-500 transition-colors duration-300">
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">Analyse Approfondie</h3>
                            <p class="text-gray-500 dark:text-gray-400">Plongez dans les données financières avec des outils d'analyse puissants pour une compréhension complète.</p>
                        </div>
                        <div class="dark:bg-[#161615] bg-gray-200 p-8 rounded-lg shadow-xl border border-gray-400 dark:border-gray-600 hover:border-indigo-500 transition-colors duration-300">
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">Alertes Personnalisées</h3>
                            <p class="text-gray-500 dark:text-gray-400">Recevez des notifications en temps réel sur les mouvements du marché et les opportunités qui vous intéressent.</p>
                        </div>
                        <div class="dark:bg-[#161615] bg-gray-200 p-8 rounded-lg shadow-xl border border-gray-400 dark:border-gray-600 hover:border-indigo-500 transition-colors duration-300">
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">Portail de Facturation Sécurisé</h3>
                            <p class="text-gray-500 dark:text-gray-400">Gérez facilement vos abonnements et factures via un portail sécurisé et intuitif.</p>
                        </div>
                    </div>
                </section>

                <section
                    id="investerra-presentation"
                    :class="['w-full py-20 transition-all duration-1000 ease-out', showInvesterraSection ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div class="lg:pr-12 text-center lg:text-left">
                            <h2 class="text-6xl md:text-7xl font-extrabold text-gray-900 dark:text-gray-300 leading-tight mb-6" style="font-family: 'Orbitron', sans-serif;">
                                Découvrez <span class="text-purple-500">Investerra</span>
                            </h2>
                            <p class="text-xl text-gray-800 dark:text-gray-300 mb-8 max-w-lg mx-auto lg:mx-0">
                                Votre plateforme de référence pour des analyses financières précises et des outils décisionnels de pointe.
                                Optimisez vos investissements comme jamais auparavant.
                            </p>
                            <ul class="text-lg text-gray-800 dark:text-gray-200 space-y-4 max-w-md mx-auto lg:mx-0 text-left">
                                <li class="flex items-start">
                                    <svg class="h-6 w-6 text-indigo-400 mr-3 flex-shrink-0 mt-1" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Accès instantané à des données de marché fiables et à jour.
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-6 w-6 text-indigo-400 mr-3 flex-shrink-0 mt-1" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Des algorithmes d'analyse prédictive pour anticiper les tendances.
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-6 w-6 text-indigo-400 mr-3 flex-shrink-0 mt-1" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Une interface utilisateur intuitive, conçue pour les investisseurs de tous niveaux.
                                </li>
                            </ul>
                            <div class="mt-10 flex justify-center lg:justify-start">
                                <Link
                                    :href="route('pricing.index')"
                                    class="inline-block px-8 py-4 bg-purple-600 text-white font-semibold rounded-lg shadow-lg hover:bg-purple-700 transform hover:scale-105 transition-all duration-300"
                                >
                                    Explorer les Fonctionnalités
                                </Link>
                            </div>
                        </div>

                        <div class="relative bg-[#1a0f2b] rounded-lg shadow-2xl overflow-hidden p-6 aspect-video flex items-center justify-center border border-purple-800">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-900 to-indigo-900 opacity-20 z-0"></div>
                            <div class="relative z-10 text-center text-gray-300 text-xl font-mono p-4 rounded-md bg-black bg-opacity-40 border border-gray-700">
                                <p class="mb-2">// Interface Investerra</p>
                                <p class="text-sm">Votre tableau de bord personnalisé ici</p>
                                <img src="https://via.placeholder.com/600x350?text=Mockup+Interface+Investerra" alt="Investerra Interface Mockup" class="mt-4 rounded-md shadow-lg w-full h-auto object-cover" />
                                <p class="mt-4 text-xs italic">"Des décisions claires, des résultats concrets."</p>
                            </div>
                            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-purple-700 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse"></div>
                            <div class="absolute -top-10 -left-10 w-40 h-40 bg-indigo-700 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse animation-delay-2000"></div>
                        </div>
                    </div>
                </section>

                <section
                    :class="['text-center w-full py-20 transition-all duration-1000 delay-300 ease-out', showHeroContent ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-gray-300 mb-6">Prêt à transformer votre approche ?</h2>
                    <p class="text-lg text-gray-800 dark:text-gray-200 mb-10">
                        Rejoignez des d'investisseurs qui utilisent Investerra pour optimiser leurs stratégies.
                    </p>
                    <Link
                        :href="route('register')"
                        class="inline-block px-10 py-5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-xl font-bold rounded-full shadow-2xl hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-500"
                    >
                        Démarrer Maintenant
                    </Link>
                </section>
            </div>
        </div>
    </GuestLayout>
</template>

<style>

/* Add some animation delay if needed */
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

</style>
