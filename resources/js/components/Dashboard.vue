<template>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div
            class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4"
        >
            <div>
                <h2
                    class="text-3xl font-extrabold text-gray-900 tracking-tight"
                >
                    My Invoices
                </h2>
                <p class="text-gray-500 mt-1">
                    Manage your payments and track your dues history.
                </p>
            </div>
            <!-- Summary Stats -->
            <div
                v-if="!loading && invoices.length > 0"
                class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10"
            >
                <div
                    class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm"
                >
                    <p
                        class="text-sm font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Total Pending
                    </p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ totalPending }}
                        <span class="text-lg text-gray-400">TRY</span>
                    </p>
                </div>
                <div
                    class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm"
                >
                    <p
                        class="text-sm font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Pending Invoices
                    </p>
                    <p class="mt-2 text-3xl font-bold text-indigo-600">
                        {{ pendingInvoicesCount }}
                    </p>
                </div>
                <div
                    class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm"
                >
                    <p
                        class="text-sm font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Paid Invoices
                    </p>
                    <p class="mt-2 text-3xl font-bold text-green-600">
                        {{ paidInvoicesCount }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div
            v-if="loading"
            class="flex flex-col items-center justify-center py-20 text-gray-400 animate-pulse"
        >
            <svg
                class="w-12 h-12 mb-3 text-indigo-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                />
            </svg>
            <p class="font-medium">Loading invoices...</p>
        </div>

        <!-- Invoices List -->
        <div v-else class="space-y-6">
            <div
                v-for="invoice in invoices"
                :key="invoice.id"
                class="group bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 transition-all duration-300 overflow-hidden"
            >
                <div
                    class="p-6 md:p-8 flex flex-col md:flex-row gap-6 md:items-center justify-between"
                >
                    <!-- Left: Invoice Info -->
                    <div class="flex-1 space-y-3">
                        <div class="flex items-center gap-3">
                            <span
                                class="bg-indigo-50 text-indigo-700 text-xs font-bold uppercase px-2.5 py-1 rounded-md tracking-wide"
                            >
                                Invoice
                            </span>
                            <span class="text-sm text-gray-500 font-medium">
                                #{{ invoice.reference_code }}
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="item in invoice.items"
                                :key="item.id"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800"
                            >
                                {{ item.description }}
                            </span>
                        </div>

                        <div
                            class="flex items-center text-sm text-gray-500 gap-2"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                ></path>
                            </svg>
                            Due:
                            <span class="font-semibold text-gray-700">{{
                                invoice.due_date
                            }}</span>
                        </div>
                    </div>

                    <!-- Right: Actions & Amount -->
                    <div
                        class="flex flex-col md:items-end gap-4 md:pl-6 md:border-l border-gray-100 min-w-[200px]"
                    >
                        <div class="text-right">
                            <span
                                class="block text-3xl font-bold text-gray-900"
                            >
                                {{ invoice.total_amount }}
                                <span
                                    class="text-lg text-gray-500 font-medium"
                                    >{{ invoice.currency }}</span
                                >
                            </span>
                        </div>

                        <!-- Paid Status -->
                        <div
                            v-if="invoice.status === 'paid'"
                            class="flex items-center gap-2 text-green-600 bg-green-50 px-4 py-2 rounded-lg font-bold border border-green-100"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                ></path>
                            </svg>
                            PAID
                        </div>

                        <!-- Payment Controls -->
                        <div v-else class="w-full space-y-3">
                            <div class="relative">
                                <select
                                    v-model="selectedGateway"
                                    class="w-full appearance-none bg-gray-50 border border-gray-200 text-gray-700 py-2.5 px-4 pr-8 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow cursor-pointer font-medium"
                                >
                                    <option value="stripe">
                                        Pay via Stripe (Global)
                                    </option>
                                    <option value="iyzico">
                                        Pay via Iyzico (Turkey)
                                    </option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        ></path>
                                    </svg>
                                </div>
                            </div>

                            <button
                                @click="initiatePayment(invoice.id)"
                                :disabled="processingId === invoice.id"
                                class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed text-sm"
                            >
                                <svg
                                    v-if="processingId === invoice.id"
                                    class="animate-spin h-4 w-4 text-white"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                <span v-else>Pay Now</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="invoices.length === 0"
                class="text-center py-16 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200"
            >
                <div class="text-gray-400 mb-4">
                    <svg
                        class="mx-auto h-12 w-12"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">
                    No invoices
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    You're all caught up! No pending dues.
                </p>
            </div>
        </div>

        <!-- Iyzico Test Card Modal -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <div
                v-if="showCardModal"
                class="fixed inset-0 z-50 overflow-y-auto"
                aria-labelledby="modal-title"
                role="dialog"
                aria-modal="true"
            >
                <!-- Backdrop -->
                <div
                    class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
                    @click="closeModal"
                ></div>

                <div
                    class="flex min-h-full items-center justify-center p-4 text-center sm:p-0"
                >
                    <div
                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl"
                    >
                        <!-- Modal Header -->
                        <div
                            class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-between items-center border-b border-gray-100"
                        >
                            <h3
                                class="text-base font-semibold leading-6 text-gray-900"
                                id="modal-title"
                            >
                                Select Test Card (Iyzico)
                            </h3>
                            <button
                                @click="closeModal"
                                class="text-gray-400 hover:text-gray-500 transition-colors"
                            >
                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div
                            class="px-4 py-4 sm:p-6 max-h-[60vh] overflow-y-auto"
                        >
                            <div
                                v-if="loadingCards"
                                class="flex justify-center py-8"
                            >
                                <svg
                                    class="animate-spin h-8 w-8 text-indigo-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                            </div>

                            <div v-else class="space-y-3">
                                <div
                                    v-for="card in testCards"
                                    :key="card.id"
                                    @click="payWithIyzico(card.card_number)"
                                    class="group relative flex items-center justify-between p-4 border rounded-xl cursor-pointer transition-all duration-200"
                                    :class="
                                        card.should_succeed
                                            ? 'border-green-200 bg-green-50/30 hover:bg-green-50 hover:border-green-300'
                                            : 'border-red-100 bg-red-50/30 hover:bg-red-50 hover:border-red-200'
                                    "
                                >
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="font-bold text-gray-900"
                                                >{{ card.bank_name }}</span
                                            >
                                            <span
                                                class="text-xs px-2 py-0.5 rounded bg-white border border-gray-200 text-gray-600"
                                                >{{ card.scheme }}</span
                                            >
                                            <span class="text-xs text-gray-500"
                                                >({{ card.type }})</span
                                            >
                                        </div>
                                        <div
                                            class="font-mono text-gray-600 mt-1 tracking-wider text-sm"
                                        >
                                            {{ card.card_number }}
                                        </div>
                                        <div
                                            v-if="!card.should_succeed"
                                            class="flex items-center gap-1 mt-1.5 text-xs text-red-600 font-medium"
                                        >
                                            <svg
                                                class="w-3 h-3"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                                />
                                            </svg>
                                            Expects: {{ card.error_message }}
                                        </div>
                                    </div>
                                    <div
                                        class="opacity-0 group-hover:opacity-100 transition-opacity text-indigo-600"
                                    >
                                        <svg
                                            class="w-6 h-6"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 7l5 5m0 0l-5 5m5-5H6"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div
                            class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
                        >
                            <button
                                @click="closeModal"
                                type="button"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script>
import axios from "axios";
import Swal from "sweetalert2";

export default {
    props: ["token"],
    data() {
        return {
            invoices: [],
            loading: true,
            processingId: null,
            selectedGateway: "stripe",

            // Iyzico Modal State
            showCardModal: false,
            testCards: [],
            loadingCards: false,
            activeInvoiceId: null,
        };
    },
    computed: {
        totalPending() {
            return this.invoices
                .filter((inv) => inv.status === "pending")
                .reduce((sum, inv) => sum + parseFloat(inv.total_amount), 0)
                .toFixed(2);
        },
        paidInvoicesCount() {
            return this.invoices.filter((inv) => inv.status === "paid").length;
        },
        pendingInvoicesCount() {
            return this.invoices.filter((inv) => inv.status === "pending")
                .length;
        },
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
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Failed to load invoices. Please try again later.",
                });
            } finally {
                this.loading = false;
            }
        },

        initiatePayment(invoiceId) {
            if (this.selectedGateway === "stripe") {
                Swal.fire({
                    title: "Confirm Payment",
                    text: "You are about to pay with Stripe using your default card.",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#4f46e5", // Indigo-600
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, pay now!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.payWithStripe(invoiceId);
                    }
                });
            } else if (this.selectedGateway === "iyzico") {
                this.openCardModal(invoiceId);
            }
        },

        async payWithStripe(id) {
            this.processingId = id;
            Swal.fire({
                title: "Processing...",
                text: "Please wait while we process your payment.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            try {
                await axios.post(
                    `/api/invoices/${id}/pay`,
                    { gateway: "stripe" },
                    { headers: { Authorization: `Bearer ${this.token}` } },
                );

                await Swal.fire({
                    icon: "success",
                    title: "Payment Successful!",
                    text: "Your payment was processed successfully via Stripe.",
                    confirmButtonColor: "#4f46e5",
                    timer: 3000,
                    timerProgressBar: true,
                });

                this.fetchInvoices();
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Payment Failed",
                    text:
                        err.response?.data?.message ||
                        "An error occurred during payment.",
                });
            } finally {
                this.processingId = null;
            }
        },

        async openCardModal(invoiceId) {
            this.activeInvoiceId = invoiceId;
            this.showCardModal = true;

            if (this.testCards.length === 0) {
                this.loadingCards = true;
                try {
                    const response = await axios.get("/api/test-cards", {
                        headers: { Authorization: `Bearer ${this.token}` },
                    });
                    this.testCards = response.data.data;
                } catch (err) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to load test cards configuration.",
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                    });
                    this.closeModal();
                } finally {
                    this.loadingCards = false;
                }
            }
        },

        async payWithIyzico(cardNumber) {
            Swal.fire({
                title: "Confirm Card Selection",
                text: `Use card ending in ${cardNumber.slice(-4)}?`,
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#4f46e5",
                cancelButtonColor: "#6b7280",
                confirmButtonText: "Yes, proceed",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    this.closeModal();
                    this.processingId = invoiceId;

                    Swal.fire({
                        title: "Processing Payment",
                        text: "Connecting to Iyzico gateways...",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    try {
                        await axios.post(
                            `/api/invoices/${invoiceId}/pay`,
                            {
                                gateway: "iyzico",
                                card_number: cardNumber,
                            },
                            {
                                headers: {
                                    Authorization: `Bearer ${this.token}`,
                                },
                            },
                        );

                        await Swal.fire({
                            icon: "success",
                            title: "Thank you!",
                            text: "Payment Successful via Iyzico!",
                            confirmButtonColor: "#4f46e5",
                        });

                        this.fetchInvoices();
                    } catch (err) {
                        const msg =
                            err.response?.data?.message || "Payment Failed";
                        Swal.fire({
                            icon: "error",
                            title: "Payment Failed",
                            text: msg,
                            confirmButtonColor: "#d33",
                        });
                    } finally {
                        this.processingId = null;
                        this.activeInvoiceId = null;
                    }
                }
            });
        },

        closeModal() {
            this.showCardModal = false;
            this.activeInvoiceId = null;
        },
    },
};
</script>
