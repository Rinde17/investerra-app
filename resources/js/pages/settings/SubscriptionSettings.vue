<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import type { BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Button } from '@/components/ui/button';
import Modal from '@/components/Modal.vue';


const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Subscription settings',
        href: '/settings/subscription',
    },
];

interface PlanProps {
    id: number;
    name: string;
    stripe_price_id: string;
    price_monthly: number;
    description: string;
    features: string[];
    analyses_per_week: number;
    pdf_pro: boolean;
    comparator: boolean;
    pdf_expert: boolean;
    fiscal_analysis: boolean;
    custom_alerts: boolean;
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
        ? '<span class="text-green-500 mr-2">&#10003;</span>' // Checkmark
        : '<span class="text-red-500 mr-2">&#10008;</span>'; // Cross
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Subscription settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Subscription settings" description="Update your subscription" />

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Statut de l'abonnement
                    </h3>

                    <div v-if="subscription">
                        <p class="text-gray-600 dark:text-gray-400 mb-2">
                            Votre abonnement actuel : <span class="font-bold">{{ subscription.plan.name }}</span>
                        </p>
                        <p class="text-gray-600 dark:text-gray-400 mb-2">
                            Prix mensuel : <span class="font-bold">{{ formatPrice(Number(subscription.plan.price_monthly)) }}</span>
                        </p>
                        <p class="text-gray-600 dark:text-gray-400 mb-2">
                            Statut : <span class="font-bold">{{ subscription.stripe_status }}</span>
                        </p>

                        <div v-if="subscription.plan.features && subscription.plan.features.length > 0" class="mt-4">
                            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">Fonctionnalités incluses :</h4>
                            <ul class="text-gray-600 dark:text-gray-400 space-y-1">
                                <li v-for="feature in subscription.plan.features" :key="feature" v-html="featureIcon(true) + feature"></li>
                                <li v-html="featureIcon(true) + 'Analyses par semaine\u00A0: ' + (subscription.plan.analyses_per_week === 0 ? 'Illimité' : subscription.plan.analyses_per_week)"></li>
                                <li v-html="featureIcon(subscription.plan.pdf_pro) + 'PDF Pro'"></li>
                                <li v-html="featureIcon(subscription.plan.comparator) + 'Comparateur'"></li>
                                <li v-html="featureIcon(subscription.plan.pdf_expert) + 'PDF Expert'"></li>
                                <li v-html="featureIcon(subscription.plan.fiscal_analysis) + 'Analyse fiscale'"></li>
                                <li v-html="featureIcon(subscription.plan.custom_alerts) + 'Alertes personnalisées'"></li>
                            </ul>
                        </div>


                        <div v-if="subscription.on_grace_period" class="mt-4 p-3 bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200 rounded-md">
                            Votre abonnement est actuellement annulé mais reste actif jusqu'au {{ subscription.ends_at }}.
                        </div>
                        <div v-else-if="subscription.stripe_status === 'canceled'" class="mt-4 p-3 bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200 rounded-md">
                            Votre abonnement est actuellement inactif.
                        </div>
                        <div v-else-if="subscription.stripe_status === 'active'" class="mt-4 p-3 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 rounded-md">
                            Votre abonnement est actif.
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                            <Link
                                v-if="subscription.on_grace_period || subscription.stripe_status === 'canceled'"
                                :href="route('subscriptions.resume')"
                                method="post"
                                :class="{'opacity-25': processingResume}"
                                :disabled="processingResume"
                                class="cursor-pointer inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <span v-if="!processingResume">Reprendre mon abonnement</span>
                                <span v-else>Chargement...</span>
                            </Link>

                            <Button @click="openSwapPlanModal" :disabled="!subscription || processingSwap" variant="default">
                                Changer d'abonnement
                            </Button>

                            <a :href="route('billing-portal')" target="_top"
                               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150">
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
                        <p class="text-gray-600 dark:text-gray-400">
                            Vous n'avez pas d'abonnement actif pour le moment.
                        </p>
                        <Link :href="route('pricing.index')"
                              class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Voir nos offres
                        </Link>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8 mt-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Mes factures
                    </h3>

                    <div v-if="invoices.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Total
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="invoice in invoices" :key="invoice.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ invoice.date }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ invoice.total }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                            invoice.status === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '',
                                            invoice.status === 'open' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '',
                                            invoice.status === 'void' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : '',
                                        ]">
                                            {{ invoice.status }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a v-if="isInvoiceDownloadable(invoice)" :href="route('invoices.download', invoice.id)" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">
                                        Télécharger
                                    </a>
                                    <span v-else class="text-gray-400 dark:text-gray-600">Non disponible</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else>
                        <p class="text-gray-600 dark:text-gray-400">
                            Aucune facture trouvée pour le moment.
                        </p>
                    </div>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>

    <Modal :show="showCancelConfirmationModal" @close="showCancelConfirmationModal = false">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Confirmer l'annulation de l'abonnement</h3>
            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
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
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Changer de plan</h3>
            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                Sélectionnez le nouveau plan auquel vous souhaitez souscrire.
            </p>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="plan in plans" :key="plan.id"
                                                                                         :class="[
                         'border rounded-lg p-4 cursor-pointer flex flex-col', /* Ajout de flex flex-col pour que le contenu s'empile verticalement dans la carte */
                         {'border-indigo-600 ring-2 ring-indigo-500': swapForm.new_plan_id === plan.id},
                         {'dark:border-gray-700': swapForm.new_plan_id !== plan.id},
                         'hover:shadow-lg transition-shadow duration-200' /* Effet sympa au survol */
                     ]"
                     @click="swapForm.new_plan_id = plan.id"
                >
                    <div class="flex items-center mb-3">
                        <input type="radio" :id="'plan-' + plan.id" :value="plan.id" v-model="swapForm.new_plan_id" class="form-radio text-indigo-600 h-4 w-4">
                        <label :for="'plan-' + plan.id" class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ plan.name }}</label>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-base font-medium mt-1">{{ formatPrice(Number(plan.price_monthly)) }}</p>
                    <p class="text-gray-500 dark:text-gray-300 text-sm mt-2 flex-grow">{{ plan.description }}</p>
                    <ul class="text-gray-600 dark:text-gray-400 text-sm mt-3 space-y-1">
                        <li v-for="feature in plan.features" :key="feature" v-html="featureIcon(true) + feature"></li>
                        <li v-html="featureIcon(true) + 'Analyses par semaine\u00A0: ' + (plan.analyses_per_week === 0 ? 'Illimité' : plan.analyses_per_week)"></li>
                        <li v-html="featureIcon(plan.pdf_pro) + 'PDF Pro'"></li>
                        <li v-html="featureIcon(plan.comparator) + 'Comparateur'"></li>
                        <li v-html="featureIcon(plan.pdf_expert) + 'PDF Expert'"></li>
                        <li v-html="featureIcon(plan.fiscal_analysis) + 'Analyse fiscale'"></li>
                        <li v-html="featureIcon(plan.custom_alerts) + 'Alertes personnalisées'"></li>
                    </ul>
                </div>
            </div>

            <div v-if="swapForm.errors.new_plan_id" class="mt-4 text-sm text-red-600 dark:text-red-400">
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
