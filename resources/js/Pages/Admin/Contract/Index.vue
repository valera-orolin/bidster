<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import ButtonWhite from '@/Components/ButtonWhite.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps(['contract']);

const form = useForm({
    contract_address: props.contract.address,
});

const form_owner = useForm({
    address: '',
});

const errorMessage = ref('');

let submitForm = () => {
    let formData = new FormData();
    formData.append('address', form.contract_address);

    axios.post(route('admin.contract.update-address'), formData)
    .then((response) => {
        form.reset();
        form.clearErrors();
        window.location.href = '/admin/contract';
    }).catch(error => {
        errorMessage.value = error.response.data.message;
    });
};

let submitFormWithdraw = () => {
    let formData = new FormData();
    formData.append('address', form_owner.address);

    axios.post(route('admin.contract.withdraw-comission'), formData)
    .then((response) => {
        form_owner.reset();
        form_owner.clearErrors();
        window.location.href = '/admin/contract';
    }).catch(error => {
        console.log(error)
        form_owner.errors.address = error.response.data.message;
    });
};
</script>

<template>
    <AuthenticatedLayout :headerText="true">
        <template v-slot:gradientText>
            Contract
        </template>

        <div class="flex flex-col">

            <form @submit.prevent="submitForm">
                <div class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-12 md:items-end text-my-gray4">
                    <div>
                        <InputLabel for="contract_address" value="Contract address" />

                        <div class="flex items-center space-x-8">
                            <TextInput
                                id="contract_address"
                                type="text"
                                class="mt-3 block w-140"
                                v-model="form.contract_address"
                                required
                            />
                            <ButtonWhite class="h-fit" text="Save" />
                        </div>

                        <InputError class="mt-2" :message=errorMessage />
                    </div>
                </div>
            </form>

            <form @submit.prevent="submitFormWithdraw" class="mt-16">
                <div class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-12 md:items-end text-my-gray4">
                    <div>
                        <InputLabel for="bid-size" value="Сontract owner's address" />

                        <div class="flex items-center space-x-8">
                            <TextInput
                                id="bid-size"
                                type="text"
                                class="mt-3 block w-140"
                                v-model="form_owner.address"
                                required
                            />
                            <ButtonWhite class="h-fit" text="Withdraw comission" />
                        </div>

                        <InputError class="mt-2" :message="form_owner.errors.address" />
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>