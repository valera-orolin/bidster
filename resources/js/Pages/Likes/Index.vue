<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Auction from '@/Pages/Lots/Partials/Auction.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps(['auctions']);
</script>

<template>
    <AuthenticatedLayout :headerText="true">
        <template v-slot:headerText>
            Liked
        </template>
        <template v-slot:gradientText>
            Auctions
        </template>
        
        <div class="flex flex-col space-y-4 md:space-y-16">

            <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-4">
                <Auction v-for="auction in auctions.data" :key="auction.id" :auction="auction" class="my-animation-in-up" />
            </div>

            <Pagination :items="auctions" />
        </div>
    </AuthenticatedLayout>
</template>