<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Auction from './Partials/Auction.vue'

defineProps(['auctions']);
</script>

<template>
    <AuthenticatedLayout :headerText="true">
        <template v-slot:headerText>
            Your
        </template>
        <template v-slot:gradientText>
            Auctions
        </template>
        
        <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4">
            <Auction v-for="auction in auctions.data" :key="auction.id" :auction="auction" class="my-animation-in-up" />
        </div>
    </AuthenticatedLayout>
</template>