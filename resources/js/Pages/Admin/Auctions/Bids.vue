<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue'
import Bid from './Partials/Bid.vue'
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';

defineProps(['bids', 'lot_title']);
</script>

<template>
    <AuthenticatedLayout :headerText="true">
        <template v-slot:headerText>
            Bids for
        </template>
        <template v-slot:gradientText>
            {{ lot_title }}
        </template>
        
        <div class="flex flex-col space-y-4 md:space-y-16">
            <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4">
                <Bid v-for="bid in bids.data" :key="bid.id" :bid="bid" class="my-animation-in-up" />
            </div>

            <Pagination :items="bids" />
        </div>
    </AuthenticatedLayout>
</template>