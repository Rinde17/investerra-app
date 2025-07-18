<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
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
    <AuthBase title="Réinitialiser le mot de passe" description="Veuillez entrer votre nouveau mot de passe ci-dessous.">
        <Head title="Réinitialiser le mot de passe" />

        <form @submit.prevent="submit">
            <div class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="email" class="text-foreground">Adresse e-mail</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="form.email"
                        class="block w-full
                               bg-input border-border text-muted-foreground cursor-not-allowed"
                        readonly
                    />
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <div class="grid gap-2">
                    <Label for="password" class="text-foreground">Nouveau mot de passe</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        v-model="form.password"
                        class="block w-full
                               bg-input border-border text-foreground
                               focus:border-primary focus:ring-primary
                               placeholder:text-muted-foreground"
                        autofocus
                        placeholder="Nouveau mot de passe"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation" class="text-foreground">Confirmer le mot de passe</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        class="block w-full
                               bg-input border-border text-foreground
                               focus:border-primary focus:ring-primary
                               placeholder:text-muted-foreground"
                        placeholder="Confirmer le nouveau mot de passe"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full bg-primary hover:bg-primary/90 text-primary-foreground
                                          active:bg-primary/80"
                        :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                    Réinitialiser le mot de passe
                </Button>
            </div>
        </form>
    </AuthBase>
</template>
