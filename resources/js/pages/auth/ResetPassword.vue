<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

interface Props {
    token: string;
    email: string;
}

const props = defineProps<Props>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <AuthLayout title="Réinitialiser le mot de passe" description="Veuillez entrer votre nouveau mot de passe ci-dessous.">
        <Head title="Réinitialiser le mot de passe" />

        <form @submit.prevent="submit">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email" class="text-gray-200">Adresse e-mail</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="form.email"
                        class="block w-full bg-[#100c1c] text-gray-400 border border-[#3E3E3A] focus:border-indigo-500 cursor-not-allowed"
                        readonly
                    />
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <div class="grid gap-2">
                    <Label for="password" class="text-gray-200">Nouveau mot de passe</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        v-model="form.password"
                        class="block w-full bg-[#100c1c] text-white border border-[#3E3E3A] focus:border-indigo-500 placeholder:text-gray-500"
                        autofocus
                        placeholder="Nouveau mot de passe"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation" class="text-gray-200">Confirmer le mot de passe</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        class="block w-full bg-[#100c1c] text-white border border-[#3E3E3A] focus:border-indigo-500 placeholder:text-gray-500"
                        placeholder="Confirmer le mot de passe"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                    Réinitialiser le mot de passe
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>
