<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Bid from './Partials/Bid.vue'
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
        
        <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4">
            <Bid v-for="bid in bids.data" :key="bid.id" :bid="bid" class="my-animation-in-up" />
        </div>
    </AuthenticatedLayout>
</template>