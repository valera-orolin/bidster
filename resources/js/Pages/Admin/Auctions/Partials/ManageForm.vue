<script setup>
import ButtonGradient from '@/Components/ButtonGradient.vue';
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import DangerButton from '@/Components/ButtonLila.vue';
import SecondaryButton from '@/Components/ButtonWhite.vue';
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';

const props = defineProps(['auction']);

const submitForm = () => {
    closeModal();
    axios.post(route('admin.auctions.declare-failure', props.auction.id))
    .then((response) => {
        window.location.href = '/admin/auctions';
    })
};

const confirmingSubmission = ref(false);
const confirmSubmission = () => {
    confirmingSubmission.value = true;
};
const closeModal = () => {
    confirmingSubmission.value = false;
};
</script>

<template>
    <div class="text-my-gray3 text-base mt-10">
        <!---
        <InputLabel for="admin-comment" value="Administration comment"   />
        <TextArea
            id="admin-comment"
            type="date"
            class="mt-3 block w-full"
            maxlength="1000"
            rows="5"
            required
            :colorsInversed="true"
        />
        <InputError class="mt-2" :message="''" />-->

        <ButtonGradient v-if="auction.status == 'Active'" class="mt-10" text="Declare failure" @click="confirmSubmission" />
    </div>

    <Modal :show="confirmingSubmission" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-my-gray3">
                Are you sure you want to declrare failure of this auction?
            </h2>

            <p class="mt-1 text-sm text-my-gray4">
                The auction will be given the status 'Failed'. All bids will be refunded.
            </p>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeModal" text="Cancel" />
                <DangerButton class="ms-3" @click="submitForm" text="Submit" />
            </div>
        </div>
    </Modal>
</template>