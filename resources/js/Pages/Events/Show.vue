<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    event: Object,
    bookings: Object,
    availableSeats: Number,
    bookingProgress: Number,
    filters: Object,
    activities: Array,
});

const search = ref(props.filters?.search || '');
let searchTimeout = null;

const bookingForm = useForm({
    email_address: '',
    seats_booked: 1,
});

const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

function formatDate(dateStr) {
    const dt = new Date(dateStr);
    const d = dt.getDate();
    const m = dt.getMonth();
    const y = dt.getFullYear();
    const hm = `${String(dt.getHours()).padStart(2, '0')}:${String(dt.getMinutes()).padStart(2, '0')}`;
    return `${d} ${months[m]} ${y}, ${hm}`;
}

function progressBarClass(pct) {
    if (pct >= 90) return 'bar-red';
    if (pct >= 60) return 'bar-amber';
    return 'bar-green';
}

const eventEnded = new Date(props.event.end_date) < new Date();

function statusBadge(status) {
    if (status === 'confirmed') return 'bg-emerald-50 text-emerald-600';
    if (status === 'cancelled') return 'bg-red-50 text-red-600';
    return 'bg-amber-50 text-amber-600';
}

function debouncedSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(`/events/${props.event.id}`, { search: search.value }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
}

function submitBooking() {
    bookingForm.post(`/events/${props.event.id}/bookings`, {
        preserveScroll: true,
        onSuccess: () => bookingForm.reset(),
    });
}

function changeStatus(bookingId, newStatus) {
    router.patch(`/bookings/${bookingId}/status`, { status: newStatus }, {
        preserveScroll: true,
    });
}

function activityIcon(activity) {
    if (activity.description === 'created') return '✦';
    if (activity.description === 'updated') return '✎';
    if (activity.description === 'deleted') return '✕';
    return '•';
}

function activityColor(activity) {
    if (activity.description === 'created') return 'bg-emerald-500';
    if (activity.description === 'updated') return 'bg-amber-500';
    if (activity.description === 'deleted') return 'bg-red-500';
    return 'bg-gray-400';
}

function activityLabel(activity) {
    const type = activity.log_name.charAt(0).toUpperCase() + activity.log_name.slice(1);
    return `${type} ${activity.description}`;
}

function handleDelete() {
    Swal.fire({
        title: 'Delete Event?',
        text: 'This will permanently remove the event and all its bookings.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/events/${props.event.id}`);
        }
    });
}
</script>

<template>
    <Head :title="event.name" />
    <div class="max-w-6xl mx-auto px-6 py-10">
        <!-- Header -->
        <div class="flex items-start justify-between mb-8">
            <div>
                <Link href="/events" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-indigo-600 transition mb-3 no-underline">
                    ← Back to events
                </Link>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ event.name }}</h1>
                <div v-if="event.description" class="mt-2 text-gray-500 text-sm max-w-2xl prose prose-sm" v-html="event.description"></div>
            </div>
            <button
                @click="handleDelete"
                class="cursor-pointer shrink-0 text-xs font-medium text-red-400 hover:text-white hover:bg-red-500 border border-red-200 hover:border-red-500 rounded-xl px-3 py-2 transition-all"
            >
                Delete
            </button>
        </div>

        <!-- Stat cards -->
        <div class="grid gap-4 sm:grid-cols-3 mb-10">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mb-1">Schedule</p>
                <p class="text-sm font-semibold text-gray-900">{{ formatDate(event.start_date) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">to {{ formatDate(event.end_date) }}</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mb-1">Capacity</p>
                <p class="text-sm font-semibold text-gray-900">{{ event.booked_seats }} / {{ event.capacity }} booked</p>
                <div class="mt-2 w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div
                        class="h-2 rounded-full progress-bar"
                        :class="progressBarClass(bookingProgress)"
                        :style="{ width: bookingProgress + '%' }"
                    ></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3"
                     :class="availableSeats === 0 ? 'bg-red-50' : 'bg-emerald-50'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :class="availableSeats === 0 ? 'text-red-500' : 'text-emerald-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mb-1">Available</p>
                <p class="text-3xl font-extrabold tracking-tight" :class="availableSeats === 0 ? 'text-red-500' : 'text-emerald-500'">
                    {{ availableSeats }}
                </p>
                <p class="text-xs text-gray-400">seats remaining</p>
            </div>
        </div>

        <!-- Main content: bookings + form -->
        <div class="grid gap-8 lg:grid-cols-3">
            <!-- Bookings list -->
            <div class="lg:col-span-2">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900">Bookings</h2>
                    <input
                        v-model="search"
                        @input="debouncedSearch"
                        type="text"
                        placeholder="Search by email..."
                        class="search-input text-sm border border-gray-200 rounded-xl pl-3 pr-3 py-2 bg-gray-50 focus:border-indigo-500 focus:bg-white outline-none transition-all w-56"
                    />
                </div>

                <div v-if="bookings.data.length === 0" class="bg-white rounded-2xl border border-gray-100 p-10 text-center">
                    <p class="text-gray-400 text-sm">No bookings yet. Be the first!</p>
                </div>

                <div v-else class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
                    <div
                        v-for="booking in bookings.data"
                        :key="booking.id"
                        class="bg-white rounded-xl border border-gray-100 px-5 py-4 flex items-center justify-between hover:border-gray-200 transition"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                <span class="text-xs font-bold text-indigo-600">{{ booking.email_address.charAt(0).toUpperCase() }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ booking.email_address }}</p>
                                <p class="text-xs text-gray-400">{{ booking.seats_booked }} seat(s) · {{ formatDate(booking.created_at) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 shrink-0 ml-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                :class="statusBadge(booking.status)"
                            >
                                {{ booking.status }}
                            </span>
                            <select
                                :value="booking.status"
                                @change="changeStatus(booking.id, $event.target.value)"
                                class="text-xs font-medium border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 outline-none focus:border-indigo-500 transition-all cursor-pointer"
                            >
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Booking pagination -->
                <div v-if="bookings.last_page > 1" class="mt-6 flex justify-center gap-1.5">
                    <Link
                        v-for="page in bookings.last_page"
                        :key="page"
                        :href="`/events/${event.id}?page=${page}${search ? '&search=' + search : ''}`"
                        class="w-8 h-8 text-xs rounded-lg border font-medium flex items-center justify-center no-underline transition-all"
                        :class="page === bookings.current_page
                            ? 'page-btn-active text-white border-transparent shadow-sm'
                            : 'bg-white text-gray-500 border-gray-200 hover:border-indigo-300 hover:text-indigo-600'"
                    >
                        {{ page }}
                    </Link>
                </div>
            </div>

            <!-- Booking form sidebar -->
            <div>
                <div class="sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Reserve Seats</h2>
                    <form @submit.prevent="submitBooking" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="p-5 space-y-4">
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
                                <input
                                    id="email"
                                    v-model="bookingForm.email_address"
                                    type="email"
                                    class="form-input"
                                    placeholder="you@example.com"
                                />
                                <p v-if="bookingForm.errors.email_address" class="mt-1.5 text-xs text-red-500 font-medium">{{ bookingForm.errors.email_address }}</p>
                            </div>

                            <div>
                                <label for="seats" class="block text-sm font-semibold text-gray-700 mb-1.5">Number of seats</label>
                                <div class="relative">
                                    <input
                                        id="seats"
                                        v-model.number="bookingForm.seats_booked"
                                        type="number"
                                        min="1"
                                        :max="availableSeats"
                                        class="form-input pr-14"
                                    />
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">seats</span>
                                </div>
                                <p v-if="bookingForm.errors.seats_booked" class="mt-1.5 text-xs text-red-500 font-medium">{{ bookingForm.errors.seats_booked }}</p>
                            </div>
                        </div>

                        <div class="px-5 pb-5">
                            <button
                                type="submit"
                                :disabled="bookingForm.processing || availableSeats === 0 || eventEnded"
                                class="btn-submit w-full inline-flex items-center justify-center gap-2 rounded-xl py-2.5 text-sm font-semibold text-white disabled:opacity-50 disabled:cursor-not-allowed"
                                :class="{ 'btn-disabled': availableSeats === 0 || eventEnded }"
                            >
                                {{ bookingForm.processing ? 'Reserving...' : (eventEnded ? 'Event Ended' : (availableSeats === 0 ? 'Sold Out' : 'Reserve Now')) }}
                            </button>
                        </div>
                    </form>

                    <!-- Flash success -->
                    <div v-if="$page.props.flash?.success" class="mt-4 rounded-xl bg-emerald-50 border border-emerald-100 p-3 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-xs font-medium text-emerald-700">{{ $page.props.flash.success }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div v-if="activities && activities.length" class="mt-10">
            <h2 class="text-xl font-bold text-gray-900 mb-5">Activity Timeline</h2>
            <div class="relative">
                <div class="absolute left-4 top-0 bottom-0 w-px bg-gray-200"></div>
                <div class="space-y-4 max-h-[400px] overflow-y-auto pr-1">
                    <div
                        v-for="activity in activities"
                        :key="activity.id"
                        class="relative flex items-start gap-4 pl-10"
                    >
                        <div
                            class="absolute left-2.5 top-1.5 w-3 h-3 rounded-full ring-2 ring-white"
                            :class="activityColor(activity)"
                        ></div>
                        <div class="bg-white rounded-xl border border-gray-100 p-4 flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ activityIcon(activity) }} {{ activityLabel(activity) }}
                                </span>
                                <span class="text-[10px] text-gray-400 font-medium">
                                    {{ formatDate(activity.created_at) }}
                                </span>
                            </div>
                            <div v-if="activity.description === 'updated' && activity.properties?.new" class="mt-2 space-y-1">
                                <div
                                    v-for="(value, key) in activity.properties.new"
                                    :key="key"
                                    class="text-xs text-gray-500"
                                >
                                    <span class="font-medium text-gray-700">{{ key }}</span>
                                    changed to
                                    <span class="font-medium text-indigo-600">{{ value }}</span>
                                </div>
                            </div>
                            <div v-else-if="activity.description === 'created' && activity.log_name === 'booking'" class="mt-1">
                                <p class="text-xs text-gray-500">
                                    {{ activity.properties?.attributes?.email_address }} booked {{ activity.properties?.attributes?.seats_booked }} seat(s)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.progress-bar {
    transition: width 0.5s ease-out;
}

.bar-green { background: linear-gradient(90deg, #34d399, #10b981); }
.bar-amber { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
.bar-red { background: linear-gradient(90deg, #f87171, #ef4444); }

.form-input {
    width: 100%;
    border-radius: 0.75rem;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    padding: 0.625rem 1rem;
    font-size: 0.875rem;
    outline: none;
    transition: all 0.2s;
}

.form-input:focus {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
}

.search-input:focus {
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
}

.btn-submit {
    background: linear-gradient(135deg, #6366f1, #a855f7);
    transition: all 0.2s;
}

.btn-submit:hover:not(:disabled) {
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.btn-disabled {
    background: #d1d5db;
}

.page-btn-active {
    background: linear-gradient(135deg, #6366f1, #a855f7);
}
</style>
