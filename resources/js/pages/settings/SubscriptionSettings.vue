<script setup lang="ts">
import { Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import type { BreadcrumbItem } from '@/types';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Button } from '@/components/ui/button';
import Modal from '@/components/Modal.vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Subscription settings',
        href: '/app/settings/subscription',
    },
];

interface PlanProps {
    id: number;
    name: string;
    stripe_price_id: string;
    price_monthly: number;
    description: string;
    analyses_per_week: number;
    pdf_pro: boolean;
    comparator: boolean;
    pdf_expert: boolean;
    fiscal_analysis: boolean;
    custom_alerts: boolean;
    priority_support: boolean;
    dedicated_account_manager: boolean;
}

interface Subscription {
    id: number;
    stripe_status: string;
    on_grace_period: boolean;
    ends_at: string | null;
    plan: PlanProps;
}

interface InvoiceProps {
    id: string; // L'ID Stripe de la facture est une chaîne
    date: string;
    total: string; // Le total est souvent une chaîne formatée
    status: string;
}

// Define props for the component
const props = defineProps<{
    subscription: Subscription | null;
    invoices: InvoiceProps[];
    plans: PlanProps[];
}>();

const showCancelConfirmationModal = ref(false);
const showSwapPlanModal = ref(false);
const processingResume = ref(false);
const processingCancel = ref(false);
const processingSwap = ref(false);

const cancelForm = useForm({}); // Formulaire vide pour l'annulation
const swapForm = useForm({
    new_plan_id: null as number | null, // ID du nouveau plan sélectionné
});

const isInvoiceDownloadable = (invoice: InvoiceProps) => {
    return invoice.status === 'paid';
};

const cancelSubscription = () => {
    processingCancel.value = true;
    cancelForm.post(route('subscriptions.cancel'), {
        preserveScroll: true,
        only: ['flash', 'subscription', 'invoices'],
        onSuccess: () => {
            showCancelConfirmationModal.value = false;
        },
        onError: () => {
            showCancelConfirmationModal.value = false;
        },
        onFinish: () => {
            processingCancel.value = false;
        },
    });
};

const swapPlan = () => {
    processingSwap.value = true;
    swapForm.post(route('subscriptions.swap'), {
        preserveScroll: true,
        only: ['flash', 'subscription', 'invoices'],
        onSuccess: () => {
            showSwapPlanModal.value = false;
        },
        onError: () => {
            showSwapPlanModal.value = false;
        },
        onFinish: () => {
            processingSwap.value = false;
        },
    });
};

const openSwapPlanModal = () => {
    showSwapPlanModal.value = true;
    // Réinitialise le plan sélectionné au plan actuel par défaut
    swapForm.new_plan_id = props.subscription ? props.subscription.plan.id : null;
};

router.on('start', (event) => {
    // Active le loader si la visite est pour 'subscriptions.resume'
    if (event.detail.visit.url.href.includes(route('subscriptions.resume'))) {
        processingResume.value = true;
    }
});

router.on('finish', (event) => {
    // Désactive le loader après toute visite, ou plus spécifiquement
    if (event.detail.visit.url.href.includes(route('subscriptions.resume'))) {
        processingResume.value = false;
    }
});

const formatPrice = (price: number | null) => {
    return price !== null ? `${price.toFixed(2)} € / mois` : 'N/A';
};

const featureIcon = (hasFeature: boolean) => {
    return hasFeature
        ? '<span class="text-primary mr-2">&#10003;</span>' // Checkmark
        : '<span class="text-destructive mr-2">&#10008;</span>'; // Cross
};
</script>

<template>
    <AuthenticatedLayout title="Subscription settings" :breadcrumbs="breadcrumbItems">
        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Subscription settings" description="Update your subscription" />

                <div class="bg-card overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8">
                    <h3 class="text-lg font-medium text-foreground mb-4">
                        Statut de l'abonnement
                    </h3>

                    <div v-if="subscription">
                        <p class="text-muted-foreground mb-2">
                            Votre abonnement actuel : <span class="font-bold">{{ subscription.plan.name }}</span>
                        </p>
                        <p class="text-muted-foreground mb-2">
                            Prix mensuel : <span class="font-bold">{{ formatPrice(Number(subscription.plan.price_monthly)) }}</span>
                        </p>
                        <p class="text-muted-foreground mb-2">
                            Statut : <span class="font-bold">{{ subscription.stripe_status }}</span>
                        </p>

                        <div class="mt-4">
                            <h4 class="text-md font-medium text-foreground mb-2">Fonctionnalités incluses :</h4>
                            <ul class="text-muted-foreground space-y-1">
                                <li v-html="featureIcon(true) + 'Analyses par semaine\u00A0: ' + (subscription.plan.analyses_per_week === 0 ? 'Illimité' : subscription.plan.analyses_per_week)"></li>
                                <li v-html="featureIcon(subscription.plan.pdf_pro) + 'PDF Pro'"></li>
                                <li v-html="featureIcon(subscription.plan.comparator) + 'Comparateur'"></li>
                                <li v-html="featureIcon(subscription.plan.pdf_expert) + 'PDF Expert'"></li>
                                <li v-html="featureIcon(subscription.plan.fiscal_analysis) + 'Analyse fiscale'"></li>
                                <li v-html="featureIcon(subscription.plan.custom_alerts) + 'Alertes personnalisées'"></li>
                                <li v-html="featureIcon(subscription.plan.priority_support) + 'Support prioritaire'"></li>
                                <li v-html="featureIcon(subscription.plan.dedicated_account_manager) + 'Gestionnaire de compte dédié'"></li>
                            </ul>
                        </div>


                        <div v-if="subscription.on_grace_period" class="mt-4 p-3 bg-accent/80 text-accent-foreground rounded-md">
                            Votre abonnement est actuellement annulé mais reste actif jusqu'au {{ subscription.ends_at }}.
                        </div>
                        <div v-else-if="subscription.stripe_status === 'canceled'" class="mt-4 p-3 bg-destructive/80 text-destructive-foreground rounded-md">
                            Votre abonnement est actuellement inactif.
                        </div>
                        <div v-else-if="subscription.stripe_status === 'active'" class="mt-4 p-3 bg-primary/80 text-primary-foreground rounded-md">
                            Votre abonnement est actif.
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                            <Link
                                v-if="subscription.on_grace_period || subscription.stripe_status === 'canceled'"
                                :href="route('subscriptions.resume')"
                                method="post"
                                :class="{'opacity-25': processingResume}"
                                :disabled="processingResume"
                                class="cursor-pointer inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-primary-foreground uppercase tracking-widest hover:bg-primary/90 focus:bg-primary/90 active:bg-primary/80 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <span v-if="!processingResume">Reprendre mon abonnement</span>
                                <span v-else>Chargement...</span>
                            </Link>

                            <Button @click="openSwapPlanModal" :disabled="!subscription || processingSwap" variant="default">
                                Changer d'abonnement
                            </Button>

                            <a :href="route('billing-portal')" target="_top"
                               class="inline-flex items-center px-4 py-2 bg-secondary border border-transparent rounded-md font-semibold text-xs text-secondary-foreground uppercase tracking-widest hover:bg-secondary/90 focus:bg-secondary/90 active:bg-secondary/80 focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition ease-in-out duration-150">
                                Accéder au portail de facturation
                            </a>

                            <Button
                                v-if="subscription.stripe_status === 'active' && !subscription.on_grace_period"
                                @click="showCancelConfirmationModal = true"
                                :disabled="processingCancel"
                                variant="destructive"
                            >
                                Annuler l'abonnement
                            </Button>
                        </div>
                    </div>
                    <div v-else>
                        <p class="text-muted-foreground">
                            Vous n'avez pas d'abonnement actif pour le moment.
                        </p>
                        <Link :href="route('pricing.index')"
                              class="mt-4 inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-primary-foreground uppercase tracking-widest hover:bg-primary/90 focus:bg-primary/90 active:bg-primary/80 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150">
                            Voir nos offres
                        </Link>
                    </div>
                </div>

                <div class="bg-card overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8 mt-8">
                    <h3 class="text-lg font-medium text-foreground mb-4">
                        Mes factures
                    </h3>

                    <div v-if="invoices.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-border">
                            <thead class="bg-muted">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                    Total
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                    Statut
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-card divide-y divide-border">
                            <tr v-for="invoice in invoices" :key="invoice.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-foreground">{{ invoice.date }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-foreground">{{ invoice.total }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                            invoice.status === 'paid' ? 'bg-primary text-primary-foreground' : '',
                                            invoice.status === 'open' ? 'bg-accent text-accent-foreground' : '',
                                            invoice.status === 'void' ? 'bg-destructive text-destructive-foreground' : '',
                                        ]">
                                            {{ invoice.status }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a v-if="isInvoiceDownloadable(invoice)" :href="route('invoices.download', invoice.id)" target="_blank" class="text-primary hover:text-primary/90">
                                        Télécharger
                                    </a>
                                    <span v-else class="text-muted-foreground">Non disponible</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else>
                        <p class="text-muted-foreground">
                            Aucune facture trouvée pour le moment.
                        </p>
                    </div>
                </div>
            </div>
        </SettingsLayout>
    </AuthenticatedLayout>

    <Modal :show="showCancelConfirmationModal" @close="showCancelConfirmationModal = false">
        <div class="p-6 bg-card rounded-lg">
            <h3 class="text-lg font-medium text-foreground">Confirmer l'annulation de l'abonnement</h3>
            <p class="mt-4 text-sm text-muted-foreground">
                Êtes-vous sûr de vouloir annuler votre abonnement ? Votre abonnement restera actif jusqu'à la fin de la période de facturation actuelle (le <span class="font-bold">{{ subscription?.ends_at }}</span>) et ne sera pas renouvelé automatiquement. Vous pouvez le reprendre à tout moment avant cette date.
            </p>
            <div class="mt-6 flex justify-end">
                <Button @click="showCancelConfirmationModal = false" variant="outline">Annuler</Button>
                <Button @click="cancelSubscription" :class="{ 'opacity-25': processingCancel }" :disabled="processingCancel" variant="destructive" class="ms-3">
                    <span v-if="!processingCancel">Confirmer l'annulation</span>
                    <span v-else>Annulation en cours...</span>
                </Button>
            </div>
        </div>
    </Modal>

    <Modal :show="showSwapPlanModal" @close="showSwapPlanModal = false" :closeable="true" maxWidth="4xl">
        <div class="p-6 bg-card rounded-lg">
            <h3 class="text-lg font-medium text-foreground">Changer de plan</h3>
            <p class="mt-4 text-sm text-muted-foreground">
                Sélectionnez le nouveau plan auquel vous souhaitez souscrire.
            </p>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="plan in plans" :key="plan.id"
                     :class="[
                         'border rounded-lg p-4 cursor-pointer flex flex-col bg-background', /* Added bg-background for plan card */
                         {'border-primary ring-2 ring-primary': swapForm.new_plan_id === plan.id}, /* Active plan border/ring */
                         {'border-border': swapForm.new_plan_id !== plan.id}, /* Inactive plan border */
                         'hover:shadow-lg transition-shadow duration-200'
                     ]"
                     @click="swapForm.new_plan_id = plan.id"
                >
                    <div class="flex items-center mb-3">
                        <input type="radio" :id="'plan-' + plan.id" :value="plan.id" v-model="swapForm.new_plan_id" class="form-radio text-primary h-4 w-4">
                        <label :for="'plan-' + plan.id" class="ml-3 text-lg font-semibold text-foreground">{{ plan.name }}</label>
                    </div>
                    <p class="text-muted-foreground text-base font-medium mt-1">{{ formatPrice(Number(plan.price_monthly)) }}</p>
                    <p class="text-muted-foreground text-sm mt-2 flex-grow">{{ plan.description }}</p>
                    <ul class="text-muted-foreground text-sm mt-3 space-y-1">
                        <li v-html="featureIcon(true) + 'Analyses par semaine\u00A0: ' + (plan.analyses_per_week === 0 ? 'Illimité' : plan.analyses_per_week)"></li>
                        <li v-html="featureIcon(plan.pdf_pro) + 'PDF Pro'"></li>
                        <li v-html="featureIcon(plan.comparator) + 'Comparateur'"></li>
                        <li v-html="featureIcon(plan.pdf_expert) + 'PDF Expert'"></li>
                        <li v-html="featureIcon(plan.fiscal_analysis) + 'Analyse fiscale'"></li>
                        <li v-html="featureIcon(plan.custom_alerts) + 'Alertes personnalisées'"></li>
                        <li v-html="featureIcon(plan.priority_support) + 'Support prioritaire'"></li>
                        <li v-html="featureIcon(plan.dedicated_account_manager) + 'Gestionnaire de compte dédié'"></li>
                    </ul>
                </div>
            </div>

            <div v-if="swapForm.errors.new_plan_id" class="mt-4 text-sm text-destructive">
                {{ swapForm.errors.new_plan_id }}
            </div>

            <div class="mt-6 flex justify-end">
                <Button @click="showSwapPlanModal = false" variant="outline">Annuler</Button>
                <Button @click="swapPlan" :class="{ 'opacity-25': processingSwap }" :disabled="processingSwap || swapForm.new_plan_id === subscription?.plan.id" variant="default" class="ms-3">
                    <span v-if="!processingSwap">Changer de plan</span>
                    <span v-else>Changement en cours...</span>
                </Button>
            </div>
        </div>
    </Modal>
</template>
