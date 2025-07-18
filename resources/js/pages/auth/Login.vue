<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase title="Connectez-vous à votre compte" description="Entrez votre adresse e-mail et votre mot de passe ci-dessous pour vous connecter.">
        <Head title="Connexion" />

        <div v-if="status" class="mb-6 text-center text-sm font-medium text-primary">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="email" class="text-foreground">Adresse e-mail</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                        class="bg-input border-border text-foreground
                               focus:border-primary focus:ring-primary
                               placeholder:text-muted-foreground"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password" class="text-foreground">Mot de passe</Label>
                        <TextLink v-if="canResetPassword" :href="route('password.request')"
                                  class="text-primary hover:text-primary/90" :tabindex="5">
                            Mot de passe oublié ?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="Mot de passe"
                        class="bg-input border-border text-foreground
                               focus:border-primary focus:ring-primary
                               placeholder:text-muted-foreground"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3 text-foreground">
                        <Checkbox id="remember" v-model="form.remember" :tabindex="3"
                                  class="data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground
                                         border-border
                                         focus-visible:ring-primary focus-visible:ring-offset-background" />
                        <span>Se souvenir de moi</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-2 w-full bg-primary hover:bg-primary/90 text-primary-foreground
                                          active:bg-primary/80"
                        :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                    Se connecter
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Vous n'avez pas de compte ?
                <TextLink :href="route('register')"
                          class="text-primary hover:text-primary/90" :tabindex="5">Inscrivez-vous</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
