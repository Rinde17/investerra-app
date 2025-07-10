<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';

const props = defineProps<{
    terrainId: number;
    terrainTitle: string;
}>();

const confirmInput = ref<HTMLInputElement | null>(null);
const dialogOpen = ref(false); // State to control dialog visibility

const form = useForm({
    confirmation: '', // Field to confirm by typing the terrain title
});

const deleteTerrain = () => {
    // Basic client-side validation
    if (form.confirmation !== props.terrainTitle) {
        form.setError('confirmation', 'The terrain title you entered does not match.');
        confirmInput.value?.focus();
        return;
    }

    form.delete(route('terrains.destroy', { terrain: props.terrainId }), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            // Optionally, add a global success notification here
        },
        onError: () => {
            confirmInput.value?.focus(); // Focus on the confirmation input if there's a server-side error
        },
        onFinish: () => {
            form.reset(); // Reset the form after a success or error
        },
    });
};

const closeModal = () => {
    dialogOpen.value = false; // Close the dialog
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <Dialog :open="dialogOpen" @update:open="dialogOpen = $event">
        <DialogTrigger as-child>
            <Button variant="destructive">Delete Terrain</Button>
        </DialogTrigger>
        <DialogContent>
            <form class="space-y-6" @submit.prevent="deleteTerrain">
                <DialogHeader class="space-y-3">
                    <DialogTitle>Are you sure you want to delete this terrain?</DialogTitle>
                    <DialogDescription>
                        This action cannot be undone. This will permanently delete the terrain
                        <span class="font-semibold text-gray-900 dark:text-white">"{{ terrainTitle }}"</span>
                        and all its associated data.
                        <br /><br />
                        Please type the terrain's title "<span class="font-semibold text-gray-900 dark:text-white">{{ terrainTitle }}</span>" to confirm.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-2">
                    <Label for="confirmation" class="sr-only">Confirm Terrain Title</Label>
                    <Input
                        id="confirmation"
                        type="text"
                        name="confirmation"
                        ref="confirmInput"
                        v-model="form.confirmation"
                        :placeholder="terrainTitle"
                        :class="{ 'border-red-500': form.errors.confirmation }"
                        @keyup.enter="deleteTerrain"
                    />
                    <InputError :message="form.errors.confirmation" />
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button type="button" variant="secondary" @click="closeModal"> Cancel </Button>
                    </DialogClose>

                    <Button type="submit" variant="destructive" :disabled="form.processing || form.confirmation !== terrainTitle">
                        <span v-if="form.processing">Deleting...</span>
                        <span v-else>Delete Terrain</span>
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
