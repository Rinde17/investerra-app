<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: route('admin.dashboard'),
    },
];
</script>

<template>
    <AuthenticatedLayout title="Admin Dashboard" :breadcrumbs="breadcrumbs">
        <h1>
            Admin Dashboard
        </h1>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
