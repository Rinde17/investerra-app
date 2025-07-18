<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';

const showContent = ref(false);

onMounted(() => {
    // Reveal content after a short delay for a smooth entrance
    setTimeout(() => {
        showContent.value = true;
    }, 100);
});
</script>

<template>
    <GuestLayout title="À Propos de LandAnalysis">
        <div class="grid grid-cols-6 gap-4 justify-items-stretch">
            <div class="col-span-4 col-start-2">

                <section
                    :class="['text-center mb-20 mt-20 transition-all duration-1000 ease-out', showContent ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <h1 class="text-6xl md:text-8xl font-extrabold text-foreground leading-tight mb-4 tracking-tight" style="font-family: 'Orbitron', sans-serif;">
                        Notre Histoire, Notre <span class="text-primary">Mission</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-muted-foreground max-w-3xl mx-auto mb-8">
                        Découvrez qui nous sommes, ce qui nous motive et comment LandAnalysis est né de la passion pour l'investissement éclairé.
                    </p>
                </section>

                <section
                    :class="['w-full py-12 transition-all duration-1000 delay-200 ease-out', showContent ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div class="lg:pr-12 text-center lg:text-left">
                            <h2 class="text-5xl md:text-6xl font-extrabold text-foreground leading-tight mb-6" style="font-family: 'Orbitron', sans-serif;">
                                Les débuts de <span class="text-accent">LandAnalysis</span>
                            </h2>
                            <p class="text-lg text-foreground mb-6">
                                En [Année de fondation], nous avons constaté un manque d'outils d'analyse financière accessibles et puissants pour les investisseurs individuels et professionnels. L'idée de LandAnalysis est née de cette lacune : créer une plateforme qui démocratise l'accès aux données complexes et aux algorithmes prédictifs, rendant l'investissement plus transparent et plus performant.
                            </p>
                            <p class="text-lg text-foreground mb-8">
                                Depuis, nous n'avons cessé d'innover, en nous appuyant sur les dernières technologies et une équipe d'experts en finance et en développement pour vous offrir une expérience sans égale.
                            </p>
                        </div>
                        <div class="relative bg-card rounded-lg shadow-2xl overflow-hidden p-6 aspect-video flex items-center justify-center border border-border">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary to-secondary opacity-20 z-0"></div>
                            <div class="relative z-10 text-center text-foreground text-xl font-mono p-4 rounded-md bg-background/40 border border-border">
                                <p class="mb-2">// Notre Vision</p>
                                <img src="https://via.placeholder.com/600x350?text=Vision+LandAnalysis" alt="Vision LandAnalysis" class="mt-4 rounded-md shadow-lg w-full h-auto object-cover" />
                                <p class="mt-4 text-xs italic">"Bâtir un avenir financier où chaque décision est éclairée."</p>
                            </div>
                            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-primary rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse"></div>
                            <div class="absolute -top-10 -left-10 w-40 h-40 bg-secondary rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse animation-delay-2000"></div>
                        </div>
                    </div>
                </section>

                <section
                    :class="['w-full py-20 transition-all duration-1000 delay-300 ease-out', showContent ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <h2 class="text-4xl md:text-5xl font-extrabold text-foreground mb-12 text-center">Nos Valeurs Fondamentales</h2>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-card p-8 rounded-lg shadow-xl border border-border hover:border-primary transition-colors duration-300 text-center">
                            <div class="mb-4">
                                <svg class="h-12 w-12 text-primary mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-foreground mb-4">Innovation Constante</h3>
                            <p class="text-muted-foreground">Nous recherchons toujours les dernières technologies et approches pour vous offrir les meilleurs outils.</p>
                        </div>
                        <div class="bg-card p-8 rounded-lg shadow-xl border border-border hover:border-primary transition-colors duration-300 text-center">
                            <div class="mb-4">
                                <svg class="h-12 w-12 text-primary mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-foreground mb-4">Transparence & Confiance</h3>
                            <p class="text-muted-foreground">La clarté des données et la fiabilité de nos analyses sont au cœur de notre engagement envers vous.</p>
                        </div>
                        <div class="bg-card p-8 rounded-lg shadow-xl border border-border hover:border-primary transition-colors duration-300 text-center">
                            <div class="mb-4">
                                <svg class="h-12 w-12 text-primary mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16v4m-2-2h4M13 17v4m-2-2h4M16 3v4m-2-2h4m-5 9H9V7h4v14z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-foreground mb-4">Soutien Client Dédié</h3>
                            <p class="text-muted-foreground">Votre succès est notre priorité. Notre équipe est là pour vous accompagner à chaque étape.</p>
                        </div>
                    </div>
                </section>

                <section
                    :class="['w-full py-20 transition-all duration-1000 delay-400 ease-out', showContent ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <h2 class="text-4xl md:text-5xl font-extrabold text-foreground mb-12 text-center">Rencontrez l'Équipe</h2>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-12 justify-items-center">
                        <div class="text-center">
                            <img src="https://via.placeholder.com/150/007bff/ffffff?text=Jane+Doe" alt="Jane Doe" class="rounded-full w-32 h-32 object-cover mx-auto mb-4 shadow-lg border-2 border-primary" />
                            <h3 class="text-xl font-bold text-foreground mb-1">Jane Doe</h3>
                            <p class="text-primary font-semibold mb-2">PDG & Fondatrice</p>
                            <p class="text-muted-foreground text-sm max-w-xs mx-auto">Visionnaire derrière LandAnalysis, experte en marchés financiers et innovation technologique.</p>
                        </div>
                        <div class="text-center">
                            <img src="https://via.placeholder.com/150/28a745/ffffff?text=John+Smith" alt="John Smith" class="rounded-full w-32 h-32 object-cover mx-auto mb-4 shadow-lg border-2 border-primary" />
                            <h3 class="text-xl font-bold text-foreground mb-1">John Smith</h3>
                            <p class="text-primary font-semibold mb-2">CTO & Architecte Logiciel</p>
                            <p class="text-muted-foreground text-sm max-w-xs mx-auto">Maître des algorithmes et du développement, il assure la robustesse de notre plateforme.</p>
                        </div>
                        <div class="text-center">
                            <img src="https://via.placeholder.com/150/ffc107/ffffff?text=Sarah+Lee" alt="Sarah Lee" class="rounded-full w-32 h-32 object-cover mx-auto mb-4 shadow-lg border-2 border-primary" />
                            <h3 class="text-xl font-bold text-foreground mb-1">Sarah Lee</h3>
                            <p class="text-primary font-semibold mb-2">Responsable Analyse Marchés</p>
                            <p class="text-muted-foreground text-sm max-w-xs mx-auto">Analyste chevronnée, elle veille à la pertinence et à l'exactitude de nos données financières.</p>
                        </div>
                    </div>
                </section>

                <section
                    :class="['text-center w-full py-20 transition-all duration-1000 delay-500 ease-out', showContent ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <h2 class="text-4xl md:text-5xl font-extrabold text-foreground mb-6">Prêt à nous rejoindre ?</h2>
                    <p class="text-lg text-foreground mb-10">
                        Découvrez comment LandAnalysis peut transformer votre approche de l'investissement.
                    </p>
                    <Link
                        :href="route('contact')"
                        class="inline-block px-10 py-5 bg-gradient-to-r from-primary to-secondary text-primary-foreground text-xl font-bold rounded-full shadow-2xl hover:from-primary/90 hover:to-secondary/90 transform hover:scale-105 transition-all duration-500"
                    >
                        Contactez-nous
                    </Link>
                </section>
            </div>
        </div>
    </GuestLayout>
</template>

<style>
/* Re-use pulse animation from Home page if it's in a global CSS or duplicated here for self-containment */
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05); /* Slightly less aggressive pulse for subtlety */
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}
</style>
