<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue'

defineProps(['user']);
</script>

<template>
    <AuthenticatedLayout :headerText="true">
        <template v-slot:headerText>
            Welcome, 
        </template>
        <template v-slot:gradientText>
            {{ user.role }}
        </template>
    </AuthenticatedLayout>
</template>