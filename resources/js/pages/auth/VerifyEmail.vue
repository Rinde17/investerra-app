<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthBase from '@/layouts/AuthLayout.vue';
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
    <AuthBase title="Vérifiez votre adresse e-mail" description="Veuillez vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer. Si vous n'avez pas reçu l'e-mail, nous vous en enverrons un autre avec plaisir.">
        <Head title="Vérification d'e-mail" />

        <div v-if="status === 'verification-link-sent'" class="mb-6 text-center text-sm font-medium text-green-600 dark:text-green-400">
            Un nouveau lien de vérification a été envoyé à l'adresse e-mail que vous avez fournie lors de l'inscription.
        </div>

        <form @submit.prevent="submit" class="space-y-6 text-center">
            <Button type="submit" :disabled="form.processing"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white
                           dark:bg-indigo-700 dark:hover:bg-indigo-600
                           active:bg-indigo-800 dark:active:bg-indigo-800">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                Renvoyer l'e-mail de vérification
            </Button>

            <TextLink :href="route('logout')" method="post" as="button"
                      class="mx-auto block text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                Se déconnecter
            </TextLink>
        </form>
    </AuthBase>
</template>
