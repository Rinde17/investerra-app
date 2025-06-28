<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

// Define props for the component
const props = defineProps<{
  team: {
    id: number;
    name: string;
  };
}>();

const form = useForm({
  email: '',
  role: 'member',
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Teams',
    href: route('teams.index'),
  },
  {
    title: props.team.name,
    href: route('teams.show', { team: props.team.id }),
  },
  {
    title: 'Add Member',
    href: route('teams.members.add', { team: props.team.id }),
  },
];

const submit = () => {
  form.post(route('teams.members.store', { team: props.team.id }), {
    onSuccess: () => {
      form.reset();
    },
  });
};
</script>

<template>
  <Head title="Add Team Member" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto max-w-3xl p-4">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add Team Member</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Add a new member to your team "{{ props.team.name }}".
        </p>
      </div>

      <div class="overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
        <form @submit.prevent="submit" class="p-6">
          <div class="mb-6">
            <label for="email" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
              Email Address <span class="text-red-500">*</span>
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              class="block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
              required
              placeholder="Enter email address"
            />
            <div v-if="form.errors.email" class="mt-1 text-sm text-red-500">
              {{ form.errors.email }}
            </div>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
              The user must already be registered in the system.
            </p>
          </div>

          <div class="mb-6">
            <label for="role" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
              Role <span class="text-red-500">*</span>
            </label>
            <select
              id="role"
              v-model="form.role"
              class="block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
              required
            >
              <option value="member">Member</option>
              <option value="admin">Admin</option>
            </select>
            <div v-if="form.errors.role" class="mt-1 text-sm text-red-500">
              {{ form.errors.role }}
            </div>
            <div class="mt-4 space-y-3">
              <div class="flex items-start">
                <div class="flex h-5 items-center">
                  <input
                    id="role-member"
                    name="role-description"
                    type="radio"
                    :checked="form.role === 'member'"
                    @change="form.role = 'member'"
                    class="h-4 w-4 rounded border-sidebar-border/70 text-primary focus:ring-primary dark:border-sidebar-border"
                  />
                </div>
                <div class="ml-3">
                  <label for="role-member" class="text-sm font-medium text-gray-900 dark:text-white">Member</label>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    Can view and create projects, but cannot add or remove team members.
                  </p>
                </div>
              </div>
              <div class="flex items-start">
                <div class="flex h-5 items-center">
                  <input
                    id="role-admin"
                    name="role-description"
                    type="radio"
                    :checked="form.role === 'admin'"
                    @change="form.role = 'admin'"
                    class="h-4 w-4 rounded border-sidebar-border/70 text-primary focus:ring-primary dark:border-sidebar-border"
                  />
                </div>
                <div class="ml-3">
                  <label for="role-admin" class="text-sm font-medium text-gray-900 dark:text-white">Admin</label>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    Can add and remove team members, and manage team settings.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3">
            <Link
              :href="route('teams.show', { team: props.team.id })"
              class="rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:border-sidebar-border dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80"
            >
              Cancel
            </Link>
            <button
              type="submit"
              class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
              :disabled="form.processing"
            >
              <span v-if="form.processing">Adding...</span>
              <span v-else>Add Member</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
