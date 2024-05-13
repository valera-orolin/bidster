<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import { onMounted, ref } from 'vue';
import TextInput from '@/Components/TextInput.vue'
import { Chart, BarController, LinearScale, CategoryScale, BarElement, LineController, LineElement, PointElement, DoughnutController, ArcElement } from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';

const props = defineProps(['keys', 'finished_auctions', 'placed_bids', 'bid_amount', 'winning_bid_amount', 'completed_auctions_count']);

Chart.register(LineController, LinearScale, CategoryScale, PointElement, LineElement, BarController, BarElement, DoughnutController, ArcElement, ChartDataLabels);
const chartContainer1 = ref(null);
const chartContainer2 = ref(null);
const chartContainer3 = ref(null);
const chartContainer4 = ref(null);
const chartContainer5 = ref(null);

onMounted(() => {
    
    const ctx1 = chartContainer1.value.getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: props.keys,
            datasets: [{
                label: 'Bid size',
                data: props.finished_auctions,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                datalabels: {
                    display: false,
                }
            }
        }
    });

    const ctx2 = chartContainer2.value.getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: props.keys,
            datasets: [{
                label: 'Bid size',
                data: props.winning_bid_amount,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
        }
    });

    const ctx3 = chartContainer3.value.getContext('2d');
    new Chart(ctx3, {
        type: 'line',
        data: {
            labels: props.keys,
            datasets: [{
                label: 'Bid size',
                data: props.placed_bids,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                datalabels: {
                    display: false,
                }
            }
        }
    });

    const ctx4 = chartContainer4.value.getContext('2d');
    new Chart(ctx4, {
        type: 'bar',
        data: {
            labels: props.keys,
            datasets: [{
                label: 'Bid size',
                data: props.bid_amount,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const ctx5 = chartContainer5.value.getContext('2d');
    new Chart(ctx5, {
        type: 'doughnut',
        data: {
            labels: props.completed_auctions_count.map(item => item.name),
            datasets: [{
                label: 'Bid size',
                data: props.completed_auctions_count.map(item => item.value),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                datalabels: {
                    color: '#fff',
                    display: true,
                    formatter: (value, context) => {
                        return context.chart.data.labels[context.dataIndex] + ', ' + value;
                    },
                }
            }
        }
    });
});

const urlParams = new URLSearchParams(window.location.search);
const selected = ref(urlParams.get('tab') || 'today');
const from = ref(urlParams.get('from') || null);
const to = ref(urlParams.get('to') || null);

const updateParams = (option) => {
    selected.value = option;
    let url = `/admin/analytics?tab=${option}`;
    if (option === 'personal' && from.value && to.value) {
        url += `&from=${from.value}&to=${to.value}`;
    }
    window.location.href = url;
};
</script>

<template>
    <AuthenticatedLayout :headerText="true">
        <template v-slot:gradientText>
            Analytics
        </template>

        <div class="flex flex-col">

            <div class="flex flex-col sm:flex-row justify-center space-y-8 sm:space-y-0 sm:space-x-8">
                <button :class="selected === 'today' ? 'bg-my-lila border-my-lila text-my-black' : 'bg-my-gray2 text-my-gray3 border-my-gray3'" class="border hover:bg-my-lila hover:text-my-gray2 py-4 px-12 rounded-2xl transition duration-500" @click="updateParams('today')">Today</button>
                <button :class="selected === 'yesterday' ? 'bg-my-lila border-my-lila text-my-black' : 'bg-my-gray2 text-my-gray3 border-my-gray3'" class="border hover:bg-my-lila hover:text-my-gray2 py-4 px-12 rounded-2xl transition duration-500" @click="updateParams('yesterday')">Yesterday</button>
                <button :class="selected === 'week' ? 'bg-my-lila border-my-lila text-my-black' : 'bg-my-gray2 text-my-gray3 border-my-gray3'" class="border hover:bg-my-lila hover:text-my-gray2 py-4 px-12 rounded-2xl transition duration-500" @click="updateParams('week')">Week</button>
                <button :class="selected === 'month' ? 'bg-my-lila border-my-lila text-my-black' : 'bg-my-gray2 text-my-gray3 border-my-gray3'" class="border hover:bg-my-lila hover:text-my-gray2 py-4 px-12 rounded-2xl transition duration-500" @click="updateParams('month')">Month</button>
                <button :class="selected === 'personal' ? 'bg-my-lila border-my-lila text-my-black' : 'bg-my-gray2 text-my-gray3 border-my-gray3'" class="border hover:bg-my-lila hover:text-my-gray2 py-4 px-12 rounded-2xl transition duration-500" @click="updateParams('personal')">Personal</button>
            </div>
            <div class="flex flex-col sm:flex-row justify-center space-y-8 sm:space-y-0 sm:space-x-8 mt-8 text-my-gray3">
                <div>
                    <InputLabel for="from" value="From" />
                    <TextInput
                        id="from"
                        type="date"
                        class="mt-3 block w-64"
                        v-model="from"
                        :defaultValue="from"
                    />
                </div>
                <div>
                    <InputLabel for="to" value="To" />
                    <TextInput
                        id="to"
                        type="date"
                        class="mt-3 block w-64"
                        v-model="to"
                        :defaultValue="to"
                    />
                </div>
            </div>
            
            <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-10 mt-16">
                <div class="w-full">
                    <div class="text-2xl font-bold text-my-gray3 mb-4">Finished auctions</div>
                    <canvas id="myChart1" ref="chartContainer1"></canvas>
                </div>

                <div class="w-full">
                    <div class="text-2xl font-bold text-my-gray3 mb-4">Winning bid amount</div>
                    <canvas id="myChart2" ref="chartContainer2"></canvas>
                </div>

                <div class="w-full">
                    <div class="text-2xl font-bold text-my-gray3 mb-4">Placed bids</div>
                    <canvas id="myChart3" ref="chartContainer3"></canvas>
                </div>

                <div class="w-full">
                    <div class="text-2xl font-bold text-my-gray3 mb-4">Bid amount</div>
                    <canvas id="myChart4" ref="chartContainer4"></canvas>
                </div>

                <div class="w-full">
                    <div class="text-2xl font-bold text-my-gray3 mb-4">Completed auctions</div>
                    <div v-if="completed_auctions_count[0]['value'] !== 0 || completed_auctions_count[1]['value'] !== 0" class="flex justify-center">
                        <div class="w-40 md:w-80">
                            <canvas id="myChart5" ref="chartContainer5"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>