<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const showHeroContent = ref(false);
const showInvesterraSection = ref(false); // New ref for the Investerra section

onMounted(() => {
    // Reveal hero content after a short delay
    setTimeout(() => {
        showHeroContent.value = true;
    }, 100);

    // Reveal Investerra section after a slightly longer delay or based on scroll (for this example, time-based)
    setTimeout(() => {
        showInvesterraSection.value = true;
    }, 500); // Adjust delay as needed
});
</script>

<template>
    <Head title="Welcome to Investerra">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    </Head>

    <div class="relative min-h-screen flex flex-col items-center justify-start text-[#EDEDEC] overflow-hidden bg-gradient-to-br from-[#0a0a0a] via-[#100c1c] to-[#0a0a0a]">

        <div class="absolute inset-0 z-0 opacity-10" style="background-image: radial-gradient(#202020 1px, transparent 1px); background-size: 40px 40px;"></div>

        <header class="z-10 mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl pt-6 px-6 lg:px-0">
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="inline-block rounded-md border border-[#3E3E3A] px-5 py-2 text-sm leading-normal text-[#EDEDEC] hover:border-[#62605b] transition-colors duration-300"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="inline-block rounded-md border border-transparent px-5 py-2 text-sm leading-normal text-[#EDEDEC] hover:border-[#3E3E3A] transition-colors duration-300"
                    >
                        Connexion
                    </Link>
                    <Link
                        :href="route('register')"
                        class="inline-block rounded-md bg-indigo-600 px-5 py-2 text-sm leading-normal text-white hover:bg-indigo-700 transition-colors duration-300"
                    >
                        S'inscrire
                    </Link>
                </template>
            </nav>
        </header>

        <main class="relative z-10 flex flex-col items-center justify-center lg:grow w-full px-6 lg:px-8">
            <section
                :class="['text-center max-w-5xl mb-20 transition-all duration-1000 ease-out', showHeroContent ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
            >
                <h1 class="text-6xl md:text-8xl font-extrabold text-white leading-tight mb-4 tracking-tight" style="font-family: 'Orbitron', sans-serif;">
                    Élevez votre Stratégie <span class="text-indigo-500">Financière</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-400 max-w-3xl mx-auto mb-8">
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
                        class="inline-block px-8 py-4 border border-gray-600 text-gray-300 font-semibold rounded-lg shadow-lg hover:bg-gray-800 transform hover:scale-105 transition-all duration-300"
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
                :class="['w-full max-w-6xl py-12 transition-all duration-1000 delay-200 ease-out', showHeroContent ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
            >
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-[#161615] p-8 rounded-lg shadow-xl border border-[#3E3E3A] hover:border-indigo-500 transition-colors duration-300">
                        <h3 class="text-2xl font-bold text-white mb-4">Analyse Approfondie</h3>
                        <p class="text-gray-400">Plongez dans les données financières avec des outils d'analyse puissants pour une compréhension complète.</p>
                    </div>
                    <div class="bg-[#161615] p-8 rounded-lg shadow-xl border border-[#3E3E3A] hover:border-indigo-500 transition-colors duration-300">
                        <h3 class="text-2xl font-bold text-white mb-4">Alertes Personnalisées</h3>
                        <p class="text-gray-400">Recevez des notifications en temps réel sur les mouvements du marché et les opportunités qui vous intéressent.</p>
                    </div>
                    <div class="bg-[#161615] p-8 rounded-lg shadow-xl border border-[#3E3E3A] hover:border-indigo-500 transition-colors duration-300">
                        <h3 class="text-2xl font-bold text-white mb-4">Portail de Facturation Sécurisé</h3>
                        <p class="text-gray-400">Gérez facilement vos abonnements et factures via un portail sécurisé et intuitif.</p>
                    </div>
                </div>
            </section>

            <section
                id="investerra-presentation"
                :class="['w-full max-w-6xl py-20 transition-all duration-1000 ease-out', showInvesterraSection ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
            >
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div class="lg:pr-12 text-center lg:text-left">
                        <h2 class="text-6xl md:text-7xl font-extrabold text-white leading-tight mb-6" style="font-family: 'Orbitron', sans-serif;">
                            Découvrez <span class="text-purple-500">Investerra</span>
                        </h2>
                        <p class="text-xl text-gray-400 mb-8 max-w-lg mx-auto lg:mx-0">
                            Votre plateforme de référence pour des analyses financières précises et des outils décisionnels de pointe.
                            Optimisez vos investissements comme jamais auparavant.
                        </p>
                        <ul class="text-lg text-gray-300 space-y-4 max-w-md mx-auto lg:mx-0 text-left">
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
                :class="['text-center w-full max-w-4xl py-20 transition-all duration-1000 delay-300 ease-out', showHeroContent ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
            >
                <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-6">Prêt à transformer votre approche ?</h2>
                <p class="text-lg text-gray-400 mb-10">
                    Rejoignez des milliers d'investisseurs qui utilisent Investerra pour optimiser leurs stratégies.
                </p>
                <Link
                    :href="route('register')"
                    class="inline-block px-10 py-5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-xl font-bold rounded-full shadow-2xl hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-500"
                >
                    Démarrer Maintenant
                </Link>
            </section>

        </main>

        <footer class="z-10 w-full text-center py-6 text-gray-500 text-sm">
            &copy; {{ new Date().getFullYear() }} Investerra. Tous droits réservés.
        </footer>
    </div>
</template>

<style>
/* Optional: Custom styles for the Orbitron font if not loaded globally */
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap');

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
