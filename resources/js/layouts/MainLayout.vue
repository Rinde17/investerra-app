<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    title?: string;
    description?: string;
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

// Get page props from Inertia
const page = usePage();
const auth = computed(() => page.props.auth);
const user = computed(() => auth.value?.user);
const subscription = computed(() => auth.value?.subscription);
const hasActiveSubscription = computed(() => subscription.value?.stripe_status === 'active');

// Check if user is authenticated AND has an active subscription
const isAuthenticatedAndSubscribed = computed(() => {
    return user.value && hasActiveSubscription.value;
});
</script>

<template>
    <!-- Conditionally render the appropriate layout based on authentication and subscription status -->
    <AuthenticatedLayout
        v-if="isAuthenticatedAndSubscribed"
        :title="props.title"
        :description="props.description"
        :breadcrumbs="props.breadcrumbs"
    >
        <slot />
    </AuthenticatedLayout>
    <GuestLayout
        v-else
        :title="props.title"
        :description="props.description"
        :breadcrumbs="props.breadcrumbs"
    >
        <slot />
    </GuestLayout>
</template>
