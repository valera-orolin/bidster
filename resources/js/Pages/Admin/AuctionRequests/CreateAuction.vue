<script setup>
import ButtonLila from '@/Components/ButtonLila.vue';
import ButtonWhite from '@/Components/ButtonWhite.vue';
import Auction from './Partials/Auction.vue';
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue'

const props = defineProps({
    request: {
    type: Object,
    required: true
  }
});

let createAuction = () => {
    let formData = new FormData();
    formData.append('lot', props.request.lot.id);
    formData.append('user', props.request.user.id);

    axios.post(route('admin.requests.store-auction', props.request.id), formData)
    .then((response) => {
        window.location.href = '/admin/requests';
    });
};

let declineAuction = () => {
    let formData = new FormData();
    formData.append('lot', props.request.lot.id);

    axios.post(route('admin.requests.decline-auction', props.request.id), formData)
    .then((response) => {
        window.location.href = '/admin/requests';
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Auction :request="request" />

        <div class="flex justify-center">
            <div class="border-2 border-transparent rounded-2xl my-gradient-bord p-4 lg:p-12 text-my-gray4 mt-12 lg:my-12 w-full lg:w-260 my-animation-in-up animation-lg">
                <div class="text-my-gray4 text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-6">
                    Manage Request
                </div>

                <div class="text-my-gray3 text-base md:space-x-6 flex flex-col md:flex-row">
                    <form @submit.prevent="createAuction"><ButtonWhite class="mt-10 w-full" :text="'Publish auction'" /></form>
                    <form @submit.prevent="declineAuction"><ButtonLila class="mt-10 w-full" :text="'Decline'" /></form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>