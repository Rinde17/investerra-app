<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { CircleCheck, CircleX } from 'lucide-vue-next';
import { computed } from 'vue';

// Define props for the component
const props = defineProps<{
    plans: Array<{
        id: number;
        name: string;
        price_monthly: number;
        stripe_price_id: string;
        description: string | null;
        analyses_per_week: number;
        pdf_pro: boolean;
        comparator: boolean;
        pdf_expert: boolean;
        fiscal_analysis: boolean;
        custom_alerts: boolean;
        priority_support: boolean;
        dedicated_account_manager: boolean;
    }>;
    subscription: {
        stripe_status: string;
        ends_at: string | null;
        stripe_price_id: string;
    } | null;
}>();

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pricing',
        href: route('pricing.index'),
    },
];

const isSubscribed = computed(() => props.subscription !== null && props.subscription.stripe_status === 'active');

const isOnGracePeriod = computed(
    () =>
        props.subscription !== null &&
        (props.subscription.stripe_status === 'active' || props.subscription.stripe_status === 'canceled') &&
        props.subscription.ends_at !== null &&
        new Date(props.subscription.ends_at).getTime() > Date.now(),
);

const formattedEndDate = computed(() => {
    if (props.subscription?.ends_at) {
        return new Date(props.subscription.ends_at).toLocaleDateString('fr-FR'); // Format for French locale
    }
    return '';
});

// Function to determine if the plan is the user's current plan
const isCurrentPlan = (planStripePriceId: string) => {
    return props.subscription?.stripe_price_id === planStripePriceId;
};

// Function to get the button properties (text, disabled state, and classes)
const getButtonProps = (planStripePriceId: string) => {
    if (!props.subscription) {
        // If the user is NOT subscribed
        return {
            text: 'Souscrire',
            disabled: false,
            classes: 'bg-primary text-primary-foreground hover:bg-primary/90 focus:ring-primary',
            action: 'checkout',
        };
    }

    // If the user IS subscribed
    if (isCurrentPlan(planStripePriceId)) {
        // If it's the user's current plan
        if (isOnGracePeriod.value) {
            // Text for the main button for the current plan on a grace period
            return {
                text: 'Plan Actuel',
                disabled: true,
                classes:
                    'bg-accent/20 text-accent-foreground border border-accent/50 cursor-not-allowed',
                action: 'current',
            };
        }
        return {
            text: 'Plan Actuel',
            disabled: true,
            classes: 'bg-muted text-muted-foreground border border-border cursor-not-allowed',
            action: 'current',
        };
    }

    // If the user is subscribed to ANOTHER plan (for changing/upgrading/downgrading)
    return {
        text: 'Gérer mon abonnement',
        disabled: false,
        classes: 'bg-primary text-primary-foreground hover:bg-primary/90 focus:ring-primary',
        action: 'manage',
    };
};

const getFeatureList = (plan: (typeof props.plans)[0]) => {
    return [
        {
            label: 'Analyses par semaine : ' + (plan.analyses_per_week === 0 ? 'Illimité' : plan.analyses_per_week),
            value: true,
        },
        { label: 'Export PDF Pro', value: plan.pdf_pro },
        { label: 'Comparateur de terrains', value: plan.comparator },
        { label: 'Export PDF Expert', value: plan.pdf_expert },
        { label: 'Analyse fiscale', value: plan.fiscal_analysis },
        { label: 'Alertes personnalisées', value: plan.custom_alerts },
        { label: 'Support prioritaire', value: plan.priority_support },
        { label: 'Gestionnaire de compte dédié', value: plan.dedicated_account_manager },
    ];
};
</script>

<template>
    <GuestLayout title="Pricing" :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <h1 class="text-4xl font-extrabold text-foreground sm:text-5xl lg:text-6xl">Choisissez votre plan</h1>
                <p class="mt-4 text-xl text-muted-foreground">
                    Sélectionnez le plan qui correspond le mieux à vos besoins d'investissement.
                </p>
            </div>

            <div v-if="isSubscribed" class="mb-10 rounded-lg bg-card p-6 shadow-md border border-border">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-primary" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-foreground">Abonnement Actif</h3>
                        <div class="mt-2 text-base text-muted-foreground">
                            <p v-if="isOnGracePeriod">
                                Votre abonnement est actuellement actif mais se terminera le **{{ formattedEndDate }}**.
                                <a
                                    :href="route('settings.subscription')"
                                    class="font-medium text-primary underline hover:text-primary/90"
                                >
                                    Reprendre l'abonnement
                                </a>
                            </p>
                            <p v-else>
                                Vous avez actuellement un abonnement actif. Vous pouvez gérer votre abonnement dans vos
                                <a
                                    :href="route('settings.subscription')"
                                    target="_top"
                                    class="font-medium text-primary underline hover:text-primary/90"
                                >
                                    paramètres de compte </a
                                >.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-8 md:grid-cols-3 lg:gap-10">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    class="flex flex-col rounded-xl border border-border bg-card p-8 shadow-lg transition-all duration-300 hover:shadow-xl"
                >
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-foreground">{{ plan.name }}</h2>
                        <p class="mt-3 text-base text-muted-foreground">{{ plan.description }}</p>
                        <p class="mt-6">
                            <span class="text-5xl font-extrabold text-foreground">{{ formatPrice(plan.price_monthly) }}</span>
                            <span class="text-lg font-medium text-muted-foreground">/mois</span>
                        </p>
                    </div>

                    <ul class="mt-8 space-y-4">
                        <li v-for="(feature, index) in getFeatureList(plan)" :key="index" class="flex items-center">
                            <div class="flex-shrink-0">
                                <CircleCheck v-if="feature.value" class="h-5 w-5 text-primary" />
                                <CircleX v-else class="h-5 w-5 text-destructive" />
                            </div>
                            <p class="ml-3 text-base text-foreground">{{ feature.label }}</p>
                        </li>
                    </ul>

                    <div class="mt-10 flex flex-col gap-4">
                        <a
                            v-if="getButtonProps(plan.stripe_price_id).action === 'checkout'"
                            :href="route('subscription.checkout', plan.stripe_price_id)"
                            :class="[
                                'flex w-full items-center justify-center rounded-md px-6 py-3 text-lg font-semibold transition-colors duration-300 focus:ring-2 focus:ring-offset-2 focus:outline-none',
                                getButtonProps(plan.stripe_price_id).classes,
                            ]"
                        >
                            {{ getButtonProps(plan.stripe_price_id).text }}
                        </a>
                        <a
                            v-else-if="getButtonProps(plan.stripe_price_id).action === 'manage'"
                            :href="route('settings.subscription')"
                            target="_top"
                            :class="[
                                'flex w-full items-center justify-center rounded-md px-6 py-3 text-lg font-semibold transition-colors duration-300 focus:ring-2 focus:ring-offset-2 focus:outline-none',
                                getButtonProps(plan.stripe_price_id).classes,
                            ]"
                        >
                            {{ getButtonProps(plan.stripe_price_id).text }}
                        </a>
                        <span
                            v-else-if="getButtonProps(plan.stripe_price_id).action === 'current'"
                            :class="[
                                'flex w-full cursor-not-allowed items-center justify-center rounded-md px-6 py-3 text-lg font-semibold',
                                getButtonProps(plan.stripe_price_id).classes,
                            ]"
                        >
                            {{ getButtonProps(plan.stripe_price_id).text }}
                        </span>

                        <a
                            v-if="isOnGracePeriod && isCurrentPlan(plan.stripe_price_id)"
                            :href="route('settings.subscription')"
                            class="text-center text-sm font-medium text-accent underline transition-colors duration-300 hover:text-accent/90"
                        >
                            Reprendre l'abonnement
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-20">
                <h2 class="text-center text-3xl font-extrabold text-foreground">Questions Fréquemment Posées</h2>
                <dl class="mt-8 space-y-8 divide-y divide-border">
                    <div class="pt-8">
                        <dt class="text-xl font-semibold text-foreground">Comment fonctionnent les plans d'abonnement ?</dt>
                        <dd class="mt-2 text-base text-muted-foreground">
                            Nos plans d'abonnement sont facturés mensuellement. Vous pouvez mettre à niveau, rétrograder ou annuler votre abonnement à
                            tout moment.
                        </dd>
                    </div>
                    <div class="pt-8">
                        <dt class="text-xl font-semibold text-foreground">Puis-je annuler mon abonnement ?</dt>
                        <dd class="mt-2 text-base text-muted-foreground">
                            Oui, vous pouvez annuler votre abonnement à tout moment. Votre abonnement restera actif jusqu'à la fin de votre période de
                            facturation actuelle.
                        </dd>
                    </div>
                    <div class="pt-8">
                        <dt class="text-xl font-semibold text-foreground">Quels modes de paiement acceptez-vous ?</dt>
                        <dd class="mt-2 text-base text-muted-foreground">
                            Nous acceptons toutes les principales cartes de crédit, y compris Visa, Mastercard et American Express.
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </GuestLayout>
</template>
