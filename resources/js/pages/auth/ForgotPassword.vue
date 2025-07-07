<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout title="Mot de passe oublié ?" description="Entrez votre adresse e-mail pour recevoir un lien de réinitialisation de mot de passe.">
        <Head title="Mot de passe oublié" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-500 dark:text-green-400">
            {{ status }}
        </div>

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="email" class="text-gray-200">Adresse e-mail</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="off"
                        v-model="form.email"
                        autofocus
                        placeholder="email@example.com"
                        class="bg-[#100c1c] text-white border border-[#3E3E3A] focus:border-indigo-500 placeholder:text-gray-500"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                        Envoyer le lien de réinitialisation
                    </Button>
                </div>
            </form>

            <div class="space-x-1 text-center text-sm text-gray-400">
                <span>Ou, retourner à la</span>
                <TextLink :href="route('login')" class="text-indigo-400 hover:text-indigo-300">connexion</TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
