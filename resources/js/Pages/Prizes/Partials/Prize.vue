<script setup>
import { usePage } from '@inertiajs/vue3'
import ButtonWhite from '@/Components/ButtonWhite.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/ButtonLila.vue';
import SecondaryButton from '@/Components/ButtonWhite.vue';
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import InputLabel from '@/Components/InputLabel.vue'
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    prize: {
    type: Object,
    required: true
  }
});

const form_contract = useForm({
    address: '',
});

const user = usePage().props.auth.user;

const submitFormReceive = () => {
    closeModalReceive();
    let formData = new FormData();
    formData.append('address', form_contract.address);

    axios.post(route('prizes.receive', props.prize.id), formData)
    .then(() => {
        window.location.href = '/prizes';
    })
}

const closeModalReceive = () => {
    confirmingReceiving.value = false;
};

const confirmingReceiving = ref(false);
const confirmReceiving = () => {
    confirmingReceiving.value = true;
};
</script>

<template>
    <div class="p-4 text-my-gray4 rounded-2xl border-0.5 border-my-gray2 hover:border-my-lila transition duration-500 cursor-pointer lg:hover:-translate-y-1 flex items-center justify-between md:space-x-10">
        <div class="w-full">
            <h2 class="text-base md:text-xl font-bold">{{ prize.auction.lot.title }}</h2>
            <div class="flex justify-between items-center mt-4 space-x-2">
                <p class="text-sm font-light text-my-gray3">Seller: {{ prize.auction.seller.name }}</p>
            </div>
            <div class="flex justify-between items-center mt-4 space-x-2">
                <p class="text-sm font-light text-my-gray3">Purchaser: {{ prize.bid.user.name }}</p>
            </div>
            <div class="flex justify-between items-center mt-4 space-x-2">
                <p class="text-sm font-light text-my-gray3 mb-4">
                    Is received: 
                    <span v-if="prize.is_received" class="text-green-400">yes</span>
                    <span v-else class="text-red-400">no</span>
                </p>
            </div>
            <div v-if="!prize.is_received && prize.bid.user.id == user.id" class="text-my-gray3">
                <ButtonWhite text="Confirm receiving" @click="confirmReceiving" />
            </div>
        </div>
        <img v-if="prize.auction.lot.images[0]" :src="prize.auction.lot.images[0].image_path" alt="Lot image" class="w-32 h-32 md:min-w-48 md:min-h-48 object-cover ml-4 rounded-2xl">
        <img v-else src="/images/icon.svg" alt="Lot image" class="w-32 h-32 md:w-48 md:h-48 ml-4 rounded-2xl">
    </div>

    <Modal :show="confirmingReceiving" @close="closeModalReceive">
        <div class="p-6">
            <h2 class="text-lg font-medium text-my-gray3">
                Are you sure you want to confirm receiving your prize?
            </h2>

            <p class="mt-1 text-sm text-my-gray4">
                Your bid will be transferred to the seller. You cannot undo this action.
            </p>

            <div class="mt-6">
                    <InputLabel for="account_address" value="Account address" class="sr-only" />

                    <TextInput
                        id="account_address"
                        ref="accountAddress"
                        v-model="form_contract.address"
                        type="text"
                        class="mt-1 block w-3/4 text-my-gray4"
                        placeholder="Account address"
                        required
                        :colorsInversed="true"
                        @keyup.enter="submitFormReceive"
                    />

                    <InputError :message="form_contract.errors.address" class="mt-2" />
                </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeModalReceive" text="Cancel" />
                <DangerButton class="ms-3" @click="submitFormReceive" text="Submit" />
            </div>
        </div>
    </Modal>
</template>