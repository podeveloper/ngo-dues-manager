<template>
    <div>
        <h2 class="text-xl font-semibold mb-4">My Invoices</h2>

        <div v-if="loading" class="text-center text-gray-500">Loading...</div>

        <div v-else class="grid gap-4">
            <div
                v-for="invoice in invoices"
                :key="invoice.id"
                class="bg-white p-6 rounded-lg shadow border border-gray-200 flex justify-between items-center"
            >
                <div>
                    <div class="font-bold text-lg text-gray-800">
                        {{ invoice.reference_code }}
                    </div>
                    <div class="text-gray-500 text-sm">
                        Due Date: {{ invoice.due_date }}
                    </div>
                    <div class="mt-2">
                        <span
                            v-for="item in invoice.items"
                            :key="item.id"
                            class="inline-block bg-gray-100 rounded-full px-3 py-1 text-xs font-semibold text-gray-700 mr-2 mb-2"
                        >
                            {{ item.description }}
                        </span>
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-2xl font-bold text-indigo-600">
                        {{ invoice.total_amount }} {{ invoice.currency }}
                    </div>

                    <div
                        v-if="invoice.status === 'paid'"
                        class="mt-2 text-green-600 font-bold border border-green-600 px-3 py-1 rounded"
                    >
                        PAID
                    </div>

                    <button
                        v-else
                        @click="payInvoice(invoice.id)"
                        :disabled="paying === invoice.id"
                        class="mt-2 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition duration-300 disabled:opacity-50"
                    >
                        {{
                            paying === invoice.id
                                ? "Processing..."
                                : "Pay with Card"
                        }}
                    </button>
                </div>
            </div>

            <div
                v-if="invoices.length === 0"
                class="text-center text-gray-500 py-8"
            >
                You have no invoices yet.
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    props: ["token"],
    data() {
        return { invoices: [], loading: true, paying: null };
    },
    mounted() {
        this.fetchInvoices();
    },
    methods: {
        async fetchInvoices() {
            try {
                const response = await axios.get("/api/invoices", {
                    headers: { Authorization: `Bearer ${this.token}` },
                });
                this.invoices = response.data.data;
            } catch (err) {
                console.error(err);
                alert("Failed to load invoices.");
            } finally {
                this.loading = false;
            }
        },
        async payInvoice(id) {
            if (!confirm("Your card will be charged. Do you confirm?")) return;

            this.paying = id;
            try {
                await axios.post(
                    `/api/invoices/${id}/pay`,
                    { gateway: "stripe" },
                    {
                        headers: { Authorization: `Bearer ${this.token}` },
                    },
                );

                alert("Payment Successful! 💳");
                this.fetchInvoices();
            } catch (err) {
                alert(err.response.data.message || "Payment Failed");
            } finally {
                this.paying = null;
            }
        },
    },
};
</script>
