<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue'
import User from './Partials/User.vue'
import { ref } from 'vue';

defineProps(['users']);
</script>

<template>
    <AuthenticatedLayout :usersPage="true">
        <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4">
            <User v-for="user in users.data" :key="user.id" :user="user" class="my-animation-in-up" />
        </div>
    </AuthenticatedLayout>
</template>