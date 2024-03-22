<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue'
import Request from './Partials/Request.vue'
import Pagination from '@/Components/Pagination.vue';
const props = defineProps(['requests']);
</script>

<template>
    <AuthenticatedLayout :requestsPage="true">
        <div class="flex flex-col space-y-4 md:space-y-16">
            <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4">
                <Request v-for="request in requests.data" :key="request.id" :request="request" class="my-animation-in-up" />
            </div>

            <Pagination :items="requests" />
        </div>
    </AuthenticatedLayout>
</template>