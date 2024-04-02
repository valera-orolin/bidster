<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Bid from './Partials/Bid.vue'
import Pagination from '@/Components/Pagination.vue';

const props = defineProps(['bids']);
</script>

<template>
    <AuthenticatedLayout :headerText="true">
        <template v-slot:headerText>
            Your
        </template>
        <template v-slot:gradientText>
            Bids
        </template>
        
        <div class="flex flex-col space-y-4 md:space-y-16">

            <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4">
                <Bid v-for="bid in bids.data" :key="bid.id" :bid="bid" class="my-animation-in-up" />
            </div>

            <Pagination :items="bids" />
        </div>
    </AuthenticatedLayout>
</template>