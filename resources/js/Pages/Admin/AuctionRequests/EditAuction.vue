<script setup>
import ButtonLila from '@/Components/ButtonLila.vue';
import ButtonWhite from '@/Components/ButtonWhite.vue';
import Auction from './Partials/Auction.vue';
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue'
import InputError from '@/Components/InputError.vue'
import DangerButton from '@/Components/ButtonLila.vue';
import SecondaryButton from '@/Components/ButtonWhite.vue';
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';

const props = defineProps({
    request: {
    type: Object,
    required: true
  }
});

const errorMessage = ref('');

let updateAuction = () => {
    closeModalUpdate();

    let formData = new FormData();
    formData.append('lot', props.request.lot.id);
    formData.append('old_lot', props.request.old_lot.id);
    formData.append('_method', 'PUT');

    axios.post(route('admin.requests.update-auction', props.request.id), formData)
    .then((response) => {
        window.location.href = '/admin/requests';
    }).catch(error => {
        errorMessage.value = error.response.data;
    });
};

let declineAuction = () => {
    closeModalDecline();

    let formData = new FormData();
    formData.append('lot', props.request.lot.id);

    axios.post(route('admin.requests.decline-auction', props.request.id), formData)
    .then((response) => {
        window.location.href = '/admin/requests';
    });
};

const confirmingSubmissionUpdate = ref(false);
const confirmSubmissionUpdate = () => {
    confirmingSubmissionUpdate.value = true;
};
const closeModalUpdate = () => {
    confirmingSubmissionUpdate.value = false;
};

const confirmingSubmissionDecline = ref(false);
const confirmSubmissionDecline = () => {
    confirmingSubmissionDecline.value = true;
};
const closeModalDecline = () => {
    confirmingSubmissionDecline.value = false;
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
                    <form @submit.prevent="confirmSubmissionUpdate"><ButtonWhite class="mt-10 w-full" text="Accept new version" /></form>
                    <form @submit.prevent="confirmSubmissionDecline"><ButtonLila class="mt-10 w-full" text="Decline" /></form>
                </form>
                <InputError class="mt-2" :message=errorMessage />
            </div>
        </div>

        <Modal :show="confirmingSubmissionUpdate" @close="closeModalUpdate">
            <div class="p-6">
                <h2 class="text-lg font-medium text-my-gray3">
                    Are you sure you want to update this auction?
                </h2>

                <p class="mt-1 text-sm text-my-gray4">
                    A new lot will be attached to this auction. The old lot will be deleted permanently.
                </p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModalUpdate" text="Cancel" />
                    <DangerButton class="ms-3" @click="updateAuction" text="Submit" />
                </div>
            </div>
        </Modal>

        <Modal :show="confirmingSubmissionDecline" @close="closeModalDecline">
            <div class="p-6">
                <h2 class="text-lg font-medium text-my-gray3">
                    Are you sure you want to decline this auction?
                </h2>

                <p class="mt-1 text-sm text-my-gray4">
                    The old lot will remain attached to this auction. The new lot will be deleted permanently.
                </p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModalDecline" text="Cancel" />
                    <DangerButton class="ms-3" @click="declineAuction" text="Submit" />
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>