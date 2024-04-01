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

let updateAuction = () => {
    let formData = new FormData();
    formData.append('lot', props.request.lot.id);
    formData.append('old_lot', props.request.old_lot.id);
    formData.append('_method', 'PUT');

    axios.post(route('admin.requests.update-auction', props.request.id), formData)
    .then((response) => {
        window.location.href = '/admin/requests';
    }).catch(error => {
        console.error(error);
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
        <div class="text-my-gray4 text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-6 my-animation-in-up animation-lg">
            Old Version
        </div>
        <Auction :lot="request.old_lot" :user="request.user" />

        <div class="text-my-gray4 text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-6 mt-24 my-animation-in-up animation-lg">
            New Version
        </div>
        <Auction :lot="request.lot" :user="request.user" />

        <div class="flex justify-center">
            <div class="border-2 border-transparent rounded-2xl my-gradient-bord p-4 lg:p-12 text-my-gray4 mt-12 lg:my-12 w-full lg:w-260 my-animation-in-up animation-lg">
                <div class="text-my-gray4 text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-6">
                    Manage Request
                </div>

                <form class="text-my-gray3 text-base md:space-x-6 flex flex-col md:flex-row">
                    <form @submit.prevent="updateAuction"><ButtonWhite class="mt-10 w-full" text="Accept new version" /></form>
                    <form @submit.prevent="declineAuction"><ButtonLila class="mt-10 w-full" text="Decline" /></form>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>