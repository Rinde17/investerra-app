<script setup lang="ts">
import { onMounted, ref } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { useForm } from '@inertiajs/vue3';

// Références pour contrôler l'apparition progressive du contenu
const showHeroContent = ref(false);
const showContactDetails = ref(false);
const showForm = ref(false);
const showMapSection = ref(false);

onMounted(() => {
    // Révéler le contenu de la section héro après un court délai
    setTimeout(() => {
        showHeroContent.value = true;
    }, 100);

    // Révéler les détails de contact
    setTimeout(() => {
        showContactDetails.value = true;
    }, 300);

    // Révéler le formulaire
    setTimeout(() => {
        showForm.value = true;
    }, 600);

    // Révéler la section carte
    setTimeout(() => {
        showMapSection.value = true;
    }, 900);
});

// Logique pour la soumission du formulaire (côté frontend, à adapter avec votre backend Laravel)
const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
});

const submitForm = () => {
    form.post(route('contact.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <GuestLayout title="Contactez-nous">
        <div class="grid grid-cols-6 gap-4 justify-items-stretch">
            <div class="col-span-4 col-start-2">

                <section
                    :class="['text-center mb-20 mt-20 transition-all duration-1000 ease-out', showHeroContent ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <h1 class="text-6xl md:text-8xl font-extrabold text-foreground leading-tight mb-4 tracking-tight" style="font-family: 'Orbitron', sans-serif;">
                        Entrons en <span class="text-primary">Contact</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-muted-foreground max-w-3xl mx-auto mb-8">
                        Une question, une suggestion ou besoin d'assistance ? Notre équipe est là pour vous.
                    </p>
                </section>

                <section
                    :class="['w-full py-12 transition-all duration-1000 delay-200 ease-out', showContactDetails ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <div class="grid md:grid-cols-2 gap-8 text-center">
                        <div class="bg-card p-8 rounded-lg shadow-xl border border-border hover:border-primary transition-colors duration-300">
                            <div class="mb-4">
                                <svg class="h-12 w-12 text-primary mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-foreground mb-4">Email</h3>
                            <p class="text-muted-foreground">Envoyez-nous un message à tout moment !</p>
                            <a href="mailto:contact@landanalysis.com" class="text-primary hover:underline font-semibold">contact@landanalysis.com</a>
                        </div>
                        <div class="bg-card p-8 rounded-lg shadow-xl border border-border hover:border-primary transition-colors duration-300">
                            <div class="mb-4">
                                <svg class="h-12 w-12 text-primary mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-foreground mb-4">Téléphone</h3>
                            <p class="text-muted-foreground">Appelez-nous pendant nos heures d'ouverture :</p>
                            <a href="tel:+33123456789" class="text-primary hover:underline font-semibold">+33 1 23 45 67 89</a><br>
                            <span class="text-sm text-muted-foreground">(Du Lundi au Vendredi, 9h-17h CET)</span>
                        </div>
                    </div>
                </section>

                <section
                    :class="['w-full py-20 transition-all duration-1000 delay-300 ease-out', showForm ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <h2 class="text-4xl md:text-5xl font-extrabold text-foreground mb-12 text-center">Envoyez-nous un message</h2>
                    <form @submit.prevent="submitForm" class="max-w-3xl mx-auto bg-card p-8 rounded-lg shadow-xl border border-border">
                        <div class="mb-6">
                            <label for="name" class="block text-foreground text-lg font-semibold mb-2">Votre Nom</label>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                required
                                class="w-full px-5 py-3 bg-input rounded-lg border border-border focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-foreground placeholder-muted-foreground"
                                placeholder="Nom complet"
                            >
                        </div>
                        <div class="mb-6">
                            <label for="email" class="block text-foreground text-lg font-semibold mb-2">Votre Email</label>
                            <input
                                type="email"
                                id="email"
                                v-model="form.email"
                                required
                                class="w-full px-5 py-3 bg-input rounded-lg border border-border focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-foreground placeholder-muted-foreground"
                                placeholder="votre.email@exemple.com"
                            >
                        </div>
                        <div class="mb-6">
                            <label for="subject" class="block text-foreground text-lg font-semibold mb-2">Sujet</label>
                            <input
                                type="text"
                                id="subject"
                                v-model="form.subject"
                                required
                                class="w-full px-5 py-3 bg-input rounded-lg border border-border focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-foreground placeholder-muted-foreground"
                                placeholder="Ex: Question sur l'abonnement"
                            >
                        </div>
                        <div class="mb-8">
                            <label for="message" class="block text-foreground text-lg font-semibold mb-2">Votre Message</label>
                            <textarea
                                id="message"
                                v-model="form.message"
                                rows="6"
                                required
                                class="w-full px-5 py-3 bg-input rounded-lg border border-border focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-foreground placeholder-muted-foreground resize-y"
                                placeholder="Décrivez votre demande en détail..."
                            ></textarea>
                        </div>
                        <div class="text-center">
                            <button
                                type="submit"
                                class="cursor-pointer inline-block px-10 py-5 bg-gradient-to-r from-primary to-secondary text-primary-foreground text-xl font-bold rounded-full shadow-2xl hover:from-primary/90 hover:to-secondary/90 transform hover:scale-105 transition-all duration-500"
                            >
                                Envoyer le Message
                            </button>
                        </div>
                    </form>
                </section>

                <section
                    :class="['w-full py-20 transition-all duration-1000 delay-400 ease-out', showMapSection ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']"
                >
                    <h2 class="text-4xl md:text-5xl font-extrabold text-foreground mb-12 text-center">Où nous trouver</h2>
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div class="relative bg-card rounded-lg shadow-2xl overflow-hidden aspect-video border border-border">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.9916250326463!2d2.292292615676059!3d48.85837007928746!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e2964e34e2d%3A0x8ddb210f84381d6c!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1678912345678!5m2!1sfr!2sfr"
                                width="100%"
                                height="100%"
                                style="border:0;"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                class="absolute inset-0"
                            ></iframe>
                        </div>
                        <div class="text-center lg:text-left bg-card p-8 rounded-lg shadow-xl border border-border">
                            <h3 class="text-2xl font-bold text-foreground mb-4">Notre Bureau Principal</h3>
                            <p class="text-lg text-muted-foreground mb-2">
                                LandAnalysis Headquarters<br>
                                123 Rue de la Finance<br>
                                75000 Paris, France
                            </p>
                            <p class="text-lg text-muted-foreground mb-4">
                                <strong>Heures d'ouverture :</strong><br>
                                Lundi - Vendredi : 9h00 - 17h00<br>
                                Samedi - Dimanche : Fermé
                            </p>
                            <p class="text-muted-foreground text-sm italic">
                                Nous vous recommandons de prendre rendez-vous pour une visite.
                            </p>
                        </div>
                    </div>
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
        transform: scale(1.05);
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}
</style>
