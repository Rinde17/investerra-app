<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
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
    projectId: number;
    projectName: string;
    terrainId: number;
    terrainTitle: string;
}>();

const confirmInput = ref<HTMLInputElement | null>(null);
const dialogOpen = ref(false); // State to control dialog visibility

const form = useForm({
    confirmation: '', // Field to confirm by typing the terrain title
});

const removeTerrain = () => {
    // Client-side validation: check if the typed confirmation matches the terrain title
    if (form.confirmation !== props.terrainTitle) {
        form.setError('confirmation', 'The terrain title you entered does not match.');
        confirmInput.value?.focus();
        return;
    }

    form.delete(
        route('projects.terrains.destroy', {
            project: props.projectId,
            terrain: props.terrainId,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                // Perform the specific reload action from the original snippet
                router.reload({ only: ['terrains', 'totalInvestment', 'totalProfit', 'averageScore'] });
                // Optionally, add a global success notification here
            },
            onError: () => {
                // Focus on the confirmation input if there's a server-side error
                confirmInput.value?.focus();
            },
            onFinish: () => {
                // Always reset the form after the request
                form.reset();
            },
        },
    );
};

const closeModal = () => {
    dialogOpen.value = false; // Close the dialog
    form.clearErrors(); // Clear any validation errors
    form.reset(); // Reset the form fields
};
</script>

<template>
    <Dialog :open="dialogOpen" @update:open="dialogOpen = $event">
        <DialogTrigger as-child>
            <Button
                type="button"
                variant="destructive"
                class="bg-red-600 text-white hover:bg-red-700 active:bg-red-800 dark:bg-red-700 dark:hover:bg-red-600 dark:active:bg-red-800"
            >
                Remove
            </Button>
        </DialogTrigger>
        <DialogContent>
            <form class="space-y-6" @submit.prevent="removeTerrain">
                <DialogHeader class="space-y-3">
                    <DialogTitle>Are you sure you want to remove this terrain from the project?</DialogTitle>
                    <DialogDescription>
                        This action will remove the terrain
                        <span class="font-semibold text-gray-900 dark:text-white">"{{ terrainTitle }}"</span>
                        from the project
                        <span class="font-semibold text-gray-900 dark:text-white">"{{ projectName }}"</span>.
                        <br />
                        <span class="font-bold text-red-600 dark:text-red-400">This will NOT delete the terrain itself.</span>
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
                        @keyup.enter="removeTerrain"
                    />
                    <InputError :message="form.errors.confirmation" />
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button type="button" variant="secondary" @click="closeModal"> Cancel </Button>
                    </DialogClose>

                    <Button type="submit" variant="destructive" :disabled="form.processing || form.confirmation !== terrainTitle">
                        <span v-if="form.processing">Removing...</span>
                        <span v-else>Remove</span>
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
