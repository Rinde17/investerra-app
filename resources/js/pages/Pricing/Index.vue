<script setup lang="ts">
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3'; // Keep Link for internal POST requests
import { computed } from 'vue';
// import AuthLayout from '@/layouts/AuthLayout.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
// import AppLayout from '@/layouts/AppLayout.vue';

// Define props for the component
const props = defineProps<{
    plans: Array<{
        id: number;
        name: string;
        price_monthly: number;
        features: Array<string>;
        stripe_price_id: string;
        description: string | null;
        analyses_per_week: number;
        pdf_pro: boolean;
        comparator: boolean;
        pdf_expert: boolean;
        fiscal_analysis: boolean;
        custom_alerts: boolean;
    }>;
    subscription: {
        stripe_status: string;
        ends_at: string | null;
        stripe_price_id: string;
    } | null;
    intent: {
        client_secret: string;
    };
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

const isOnGracePeriod = computed(() =>
    props.subscription !== null &&
    (props.subscription.stripe_status === 'active' || props.subscription.stripe_status === 'canceled') &&
    props.subscription.ends_at !== null &&
    new Date(props.subscription.ends_at).getTime() > Date.now()
);

const formattedEndDate = computed(() => {
    if (props.subscription?.ends_at) {
        return new Date(props.subscription.ends_at).toLocaleDateString();
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
        return { text: 'Souscrire', disabled: false, classes: 'bg-primary text-white hover:bg-primary-dark focus:ring-primary', action: 'checkout' };
    }

    // If the user IS subscribed
    if (isCurrentPlan(planStripePriceId)) {
        // If it's the user's current plan
        if (isOnGracePeriod.value) {
            // Text for the main button for current plan on a grace period
            return { text: 'Plan Actuel', disabled: true, classes: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-200 cursor-not-allowed', action: 'current' };
        }
        return { text: 'Plan Actuel', disabled: true, classes: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 cursor-not-allowed', action: 'current' };
    }

    // If the user is subscribed to ANOTHER plan (for changing/upgrading/downgrading)
    return { text: 'Gérer mon abonnement', disabled: false, classes: 'bg-primary text-white hover:bg-primary-dark focus:ring-primary', action: 'manage' };
};
</script>

<template>
    <Head title="Pricing" />

    <GuestLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl p-4">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Choose Your Plan</h1>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                    Select the plan that best fits your investment needs
                </p>
            </div>

            <div v-if="isSubscribed" class="mb-8 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800 dark:text-green-200">Active Subscription</h3>
                        <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                            <p v-if="isOnGracePeriod">
                                Your subscription is currently active but will end on {{ formattedEndDate }}.
                                <a
                                    :href="route('settings.subscription')"
                                    class="font-medium text-green-700 underline hover:text-green-600 dark:text-green-200 dark:hover:text-green-100"
                                >
                                    Resume Subscription
                                </a>
                            </p>
                            <p v-else>
                                You currently have an active subscription. You can manage your subscription in your
                                <a :href="route('settings.subscription')" target="_top" class="font-medium text-green-700 underline hover:text-green-600 dark:text-green-200 dark:hover:text-green-100">
                                    account settings
                                </a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    class="flex flex-col overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm transition-all hover:shadow-md dark:border-sidebar-border dark:bg-sidebar-bg"
                >
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ plan.name }}</h2>
                        <p class="mt-4 text-gray-500 dark:text-gray-400">{{ plan.description }}</p>
                        <p class="mt-6">
                            <span class="text-4xl font-bold text-gray-900 dark:text-white">{{ formatPrice(plan.price_monthly) }}</span>
                            <span class="text-base font-medium text-gray-500 dark:text-gray-400">/month</span>
                        </p>

                        <ul class="mt-6 space-y-4">
                            <li v-for="(feature, index) in plan.features" :key="index" class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-500" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-3 text-base text-gray-700 dark:text-gray-300">{{ feature }}</p>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-auto flex border-t border-sidebar-border/70 p-6 dark:border-sidebar-border">
                        <a
                            v-if="getButtonProps(plan.stripe_price_id).action === 'checkout'"
                            :href="route('settings.subscription')"
                            :class="['flex-grow flex items-center justify-center rounded-md px-4 py-2 text-center text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2', getButtonProps(plan.stripe_price_id).classes]"
                        >
                            {{ getButtonProps(plan.stripe_price_id).text }}
                        </a>
                        <a
                            v-else-if="getButtonProps(plan.stripe_price_id).action === 'manage'"
                            :href="route('settings.subscription')"
                            target="_top"
                            :class="['flex-grow flex items-center justify-center rounded-md px-4 py-2 text-center text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2', getButtonProps(plan.stripe_price_id).classes]"
                        >
                            {{ getButtonProps(plan.stripe_price_id).text }}
                        </a>
                        <span
                            v-else-if="getButtonProps(plan.stripe_price_id).action === 'current'"
                            :class="['flex-grow flex items-center justify-center rounded-md px-4 py-2 text-center text-sm font-medium', getButtonProps(plan.stripe_price_id).classes]"
                        >
                            {{ getButtonProps(plan.stripe_price_id).text }}
                        </span>

                        <a
                            v-if="isOnGracePeriod && isCurrentPlan(plan.stripe_price_id)"
                            :href="route('settings.subscription')"
                            class="ml-4 flex-shrink-0 rounded-md px-4 py-2 text-sm font-medium underline focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="{
                                'text-yellow-800 hover:text-yellow-600 dark:text-yellow-200 dark:hover:text-yellow-100': getButtonProps(plan.stripe_price_id).classes.includes('bg-yellow'),
                                'text-green-800 hover:text-green-600 dark:text-green-200 dark:hover:text-green-100': !getButtonProps(plan.stripe_price_id).classes.includes('bg-yellow')
                            }"
                        >
                            Reprendre
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Frequently Asked Questions</h2>
                <dl class="mt-6 space-y-6 divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
                    <div class="pt-6">
                        <dt class="text-lg font-medium text-gray-900 dark:text-white">How do the subscription plans work?</dt>
                        <dd class="mt-2 text-base text-gray-500 dark:text-gray-400">
                            Our subscription plans are billed monthly. You can upgrade, downgrade, or cancel your subscription at any time.
                        </dd>
                    </div>
                    <div class="pt-6">
                        <dt class="text-lg font-medium text-gray-900 dark:text-white">Can I cancel my subscription?</dt>
                        <dd class="mt-2 text-base text-gray-500 dark:text-gray-400">
                            Yes, you can cancel your subscription at any time. Your subscription will remain active until the end of your current billing period.
                        </dd>
                    </div>
                    <div class="pt-6">
                        <dt class="text-lg font-medium text-gray-900 dark:text-white">What payment methods do you accept?</dt>
                        <dd class="mt-2 text-base text-gray-500 dark:text-gray-400">
                            We accept all major credit cards, including Visa, Mastercard, and American Express.
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </GuestLayout>
</template>
