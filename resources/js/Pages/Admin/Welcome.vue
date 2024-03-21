<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue'
</script>

<template>
    <AuthenticatedLayout :headerText="true">
        <template v-slot:headerText>
            Welcome, 
        </template>
        <template v-slot:gradientText>
            Director
        </template>
    </AuthenticatedLayout>
</template>