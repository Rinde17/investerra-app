<script setup lang="ts">
import { computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps<{
    show: boolean;
    maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '4xl';
    closeable?: boolean;
}>();

const emit = defineEmits(['close']);

watch(
    () => props.show,
    () => {
        if (props.show) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    },
);

const close = () => {
    // CORRECTION 2 : S'assurer que `closeable` a une valeur par défaut de true si non défini
    // Ou, si tu veux qu'il soit par défaut non-closeable, laisse le `if` et assure-toi de passer `closeable=true` explicitement
    // quand tu veux qu'il soit fermable. Pour l'instant, je vais le rendre par défaut fermable dans le composant appelant.
    // Pour ce composant Modal, on part du principe que `closeable` est bien passé.
    if (props.closeable === undefined || props.closeable) { // rend closeable par défaut si non spécifié
        emit('close');
    }
};

const maxWidthClass = computed(() => {
    return {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
        '4xl': 'sm:max-w-4xl',
    }[props.maxWidth || '4xl'];
});

onMounted(() => document.addEventListener('keydown', handleEscape));
onUnmounted(() => document.removeEventListener('keydown', handleEscape));

const handleEscape = (e: KeyboardEvent) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};
</script>

<template>
    <teleport to="body">
        <Transition
            leave-active-class="duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-show="show"
                class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center transform transition-all"
            >
                <Transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div
                        v-show="show"
                        class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75 transition-opacity"
                        @click="close"
                    />
                </Transition>

                <Transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div
                        v-show="show"
                        class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full p-4 md:p-6 relative z-[51]" :class="maxWidthClass"
                    >
                        <slot />
                    </div>
                </Transition>
            </div>
        </Transition>
    </teleport>
</template>
