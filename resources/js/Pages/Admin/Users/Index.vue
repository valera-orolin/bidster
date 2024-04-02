<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue'
import User from './Partials/User.vue'
import Pagination from '@/Components/Pagination.vue';

defineProps(['users']);
</script>

<template>
    <AuthenticatedLayout :usersPage="true">
        <div class="flex flex-col space-y-4 md:space-y-16">
            <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4">
                <User v-for="user in users.data" :key="user.id" :user="user" class="my-animation-in-up" />
            </div>

            <Pagination :items="users" />
        </div>
    </AuthenticatedLayout>
</template>