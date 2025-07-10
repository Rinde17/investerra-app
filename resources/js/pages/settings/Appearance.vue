<script setup lang="ts">
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import SettingsLayout from '@/layouts/settings/Layout.vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Appearance settings',
        href: '/app/settings/appearance',
    },
];
</script>

<template>
    <AuthenticatedLayout title="Appearance settings" :breadcrumbs="breadcrumbItems">
        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Appearance settings" description="Update your account's appearance settings" />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </AuthenticatedLayout>
</template>
