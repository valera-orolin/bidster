<script setup>
import ButtonWhite from '@/Components/ButtonWhite.vue';
import ButtonGradient from '@/Components/ButtonGradient.vue';
import { Link } from '@inertiajs/vue3';

defineProps(['user', 'currentUserRole']);
</script>

<template>
    <div class="text-base mt-10 space-x-6">

        <Link v-if="user.status == 'Active' && user.role != 'Director'" :href="route('admin.users.make-banned', user.id)" method="post" as="button">
            <ButtonWhite text="Ban user" />
        </Link>

        <Link v-else-if="user.role != 'Director'" :href="route('admin.users.make-active', user.id)" method="post" as="button">
            <ButtonWhite text="Unban user" />
        </Link>

        <Link v-if="currentUserRole == 'Director' && user.role == 'User'" :href="route('admin.users.make-manager', user.id)" method="post" as="button">
            <ButtonGradient text="Appoint as manager" />
        </Link>

        <Link v-if="currentUserRole == 'Director' && user.role == 'Manager'" :href="route('admin.users.make-user', user.id)" method="post" as="button">
            <ButtonGradient text="Demote to user" />
        </Link>
    </div>
</template>