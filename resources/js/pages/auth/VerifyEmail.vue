<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <AuthLayout title="Vérifiez votre adresse e-mail" description="Veuillez vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer.">
        <Head title="Vérification d'e-mail" />

        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-500 dark:text-green-400">
            Un nouveau lien de vérification a été envoyé à l'adresse e-mail que vous avez fournie lors de l'inscription.
        </div>

        <form @submit.prevent="submit" class="space-y-6 text-center">
            <Button type="submit" :disabled="form.processing"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                Renvoyer l'e-mail de vérification
            </Button>

            <TextLink :href="route('logout')" method="post" as="button" class="mx-auto block text-sm text-gray-400 hover:text-indigo-400">
                Se déconnecter
            </TextLink>
        </form>
    </AuthLayout>
</template>
