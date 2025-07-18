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

    // Reveal an Investerra section after a slightly longer delay or based on scroll (for this example, time-based)
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
                    <h1 class="text-6xl md:text-8xl font-extrabold text-foreground leading-tight mb-4 tracking-tight" style="font-family: 'Orbitron', sans-serif;">
                        Élevez votre Stratégie <span class="text-primary">Financière</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-muted-foreground max-w-3xl mx-auto mb-8">
                        Des outils d'analyse avancés pour des décisions d'investissement éclairées. Prenez le contrôle de votre avenir financier.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <Link
                            :href="route('pricing.index')"
                            class="inline-block px-8 py-4 bg-primary text-primary-foreground font-semibold rounded-lg shadow-lg hover:bg-primary/90 transform hover:scale-105 transition-all duration-300"
                        >
                            Voir nos Plans
                        </Link>
                        <Link
                            :href="route('dashboard')"
                            v-if="$page.props.auth.user"
                            class="inline-block px-8 py-4 border border-border text-muted-foreground font-semibold rounded-lg shadow-lg hover:bg-accent hover:text-accent-foreground transform hover:scale-105 transition-all duration-300"
                        >
                            Accéder au Tableau de Bord
                        </Link>
                        <Link
                            :href="route('login')"
                            v-else
                            class="inline-block px-8 py-4 border border-border text-muted-foreground font-semibold rounded-lg shadow-lg hover:bg-accent hover:text-accent-foreground transform hover:scale-105 transition-all duration-300"
                        >
                            Commencer Gratuitement
                        </Link>
                    </div>
                </section>

                <section
                    :class="['w-full py-12 transition-all duration-1000 delay-200 ease-out', showHeroContent ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="bg-card p-8 rounded-lg shadow-xl border border-border hover:border-primary transition-colors duration-300">
                            <h3 class="text-2xl font-bold text-foreground mb-4">Analyse Approfondie</h3>
                            <p class="text-muted-foreground">Plongez dans les données financières avec des outils d'analyse puissants pour une compréhension complète.</p>
                        </div>
                        <div class="bg-card p-8 rounded-lg shadow-xl border border-border hover:border-primary transition-colors duration-300">
                            <h3 class="text-2xl font-bold text-foreground mb-4">Alertes Personnalisées</h3>
                            <p class="text-muted-foreground">Recevez des notifications en temps réel sur les mouvements du marché et les opportunités qui vous intéressent.</p>
                        </div>
                        <div class="bg-card p-8 rounded-lg shadow-xl border border-border hover:border-primary transition-colors duration-300">
                            <h3 class="text-2xl font-bold text-foreground mb-4">Portail de Facturation Sécurisé</h3>
                            <p class="text-muted-foreground">Gérez facilement vos abonnements et factures via un portail sécurisé et intuitif.</p>
                        </div>
                    </div>
                </section>

                <section
                    id="investerra-presentation"
                    :class="['w-full py-20 transition-all duration-1000 ease-out', showInvesterraSection ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div class="lg:pr-12 text-center lg:text-left">
                            <h2 class="text-6xl md:text-7xl font-extrabold text-foreground leading-tight mb-6" style="font-family: 'Orbitron', sans-serif;">
                                Découvrez <span class="text-accent">LandAnalysis</span>
                            </h2>
                            <p class="text-xl text-foreground mb-8 max-w-lg mx-auto lg:mx-0">
                                Votre plateforme de référence pour des analyses financières précises et des outils décisionnels de pointe.
                                Optimisez vos investissements comme jamais auparavant.
                            </p>
                            <ul class="text-lg text-foreground space-y-4 max-w-md mx-auto lg:mx-0 text-left">
                                <li class="flex items-start">
                                    <svg class="h-6 w-6 text-primary mr-3 flex-shrink-0 mt-1" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Accès instantané à des données de marché fiables et à jour.
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-6 w-6 text-primary mr-3 flex-shrink-0 mt-1" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Des algorithmes d'analyse prédictive pour anticiper les tendances.
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-6 w-6 text-primary mr-3 flex-shrink-0 mt-1" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Une interface utilisateur intuitive, conçue pour les investisseurs de tous niveaux.
                                </li>
                            </ul>
                            <div class="mt-10 flex justify-center lg:justify-start">
                                <Link
                                    :href="route('pricing.index')"
                                    class="inline-block px-8 py-4 bg-accent text-accent-foreground font-semibold rounded-lg shadow-lg hover:bg-accent/90 transform hover:scale-105 transition-all duration-300"
                                >
                                    Explorer les Fonctionnalités
                                </Link>
                            </div>
                        </div>

                        <div class="relative bg-card rounded-lg shadow-2xl overflow-hidden p-6 aspect-video flex items-center justify-center border border-border">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary to-secondary opacity-20 z-0"></div>
                            <div class="relative z-10 text-center text-foreground text-xl font-mono p-4 rounded-md bg-background/40 border border-border">
                                <p class="mb-2">// Interface LandAnalysis</p>
                                <p class="text-sm">Votre tableau de bord personnalisé ici</p>
                                <img src="https://via.placeholder.com/600x350?text=Mockup+Interface+Investerra" alt="LandAnalysis Interface Mockup" class="mt-4 rounded-md shadow-lg w-full h-auto object-cover" />
                                <p class="mt-4 text-xs italic">"Des décisions claires, des résultats concrets."</p>
                            </div>
                            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-primary rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse"></div>
                            <div class="absolute -top-10 -left-10 w-40 h-40 bg-secondary rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse animation-delay-2000"></div>
                        </div>
                    </div>
                </section>

                <section
                    :class="['text-center w-full py-20 transition-all duration-1000 delay-300 ease-out', showHeroContent ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <h2 class="text-4xl md:text-5xl font-extrabold text-foreground mb-6">Prêt à transformer votre approche ?</h2>
                    <p class="text-lg text-foreground mb-10">
                        Rejoignez des d'investisseurs qui utilisent LandAnalysis pour optimiser leurs stratégies.
                    </p>
                    <Link
                        :href="route('register')"
                        class="inline-block px-10 py-5 bg-gradient-to-r from-primary to-secondary text-primary-foreground text-xl font-bold rounded-full shadow-2xl hover:from-primary/90 hover:to-secondary/90 transform hover:scale-105 transition-all duration-500"
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
