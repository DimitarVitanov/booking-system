<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    events: Object,
});

const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

function formatDate(dateStr) {
    const [date, time] = dateStr.includes('T') ? dateStr.split('T') : dateStr.split(' ');
    const [y, m, d] = date.split('-');
    const hm = time ? time.substring(0, 5) : '';
    return `${parseInt(d)} ${months[parseInt(m) - 1]} ${y}, ${hm}`;
}

function progressColor(pct) {
    if (pct >= 90) return 'text-red-500';
    if (pct >= 60) return 'text-amber-500';
    return 'text-emerald-500';
}

function progressBarClass(pct) {
    if (pct >= 90) return 'bar-red';
    if (pct >= 60) return 'bar-amber';
    return 'bar-green';
}

function isPast(event) {
    return new Date(event.end_date) < new Date();
}

function statusLabel(event) {
    if (isPast(event)) return { text: 'Ended', cls: 'bg-gray-100 text-gray-500' };
    if (event.available_seats === 0) return { text: 'Sold Out', cls: 'bg-red-50 text-red-600' };
    if (event.booking_progress >= 75) return { text: 'Filling Fast', cls: 'bg-amber-50 text-amber-600' };
    return { text: 'Available', cls: 'bg-emerald-50 text-emerald-600' };
}
</script>

<template>
    <Head title="Events" />
    <div class="max-w-6xl mx-auto px-6 py-10">
        <!-- Header -->
        <div class="flex items-start justify-between mb-10">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    Discover <span class="text-gradient">Events</span>
                </h1>
                <p class="mt-2 text-gray-500 text-sm">Browse upcoming events and reserve your seats.</p>
            </div>
            <Link href="/events/create" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl">
                + Create Event
            </Link>
        </div>

        <!-- Empty state -->
        <div v-if="events.data.length === 0" class="text-center py-20">
            <div class="w-20 h-20 mx-auto mb-5 rounded-2xl bg-indigo-50 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">No events yet</h3>
            <p class="text-gray-500 text-sm mb-6">Get started by creating your first event.</p>
            <Link href="/events/create" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl">
                + Create Event
            </Link>
        </div>

        <!-- Event cards -->
        <div v-else class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="event in events.data"
                :key="event.id"
                :href="`/events/${event.id}`"
                class="event-card flex flex-col bg-white rounded-2xl border border-gray-100 p-6 relative overflow-hidden"
            >
                <div class="card-accent"></div>

                <div class="flex items-start justify-between mb-2 gap-3">
                    <h3 class="card-title font-bold text-gray-900 text-sm truncate">{{ event.name }}</h3>
                    <span
                        class="shrink-0 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full"
                        :class="statusLabel(event).cls"
                    >
                        {{ statusLabel(event).text }}
                    </span>
                </div>

                <div v-if="event.description" class="text-sm text-gray-500 line-clamp-2" v-html="event.description"></div>
                <p v-else class="text-sm text-gray-400 italic">No description provided</p>

                <div class="mt-4 flex items-center gap-2 text-xs text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ formatDate(event.start_date) }}</span>
                </div>

                <div class="mt-auto pt-4 border-t border-gray-50">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="text-gray-500 font-medium">{{ event.booked_seats }} / {{ event.capacity }} seats</span>
                        <span class="font-bold" :class="progressColor(event.booking_progress)">
                            {{ event.booking_progress }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div
                            class="h-2 rounded-full progress-bar"
                            :class="progressBarClass(event.booking_progress)"
                            :style="{ width: event.booking_progress + '%' }"
                        ></div>
                    </div>
                </div>
            </Link>
        </div>

        <!-- Pagination -->
        <div v-if="events.last_page > 1" class="mt-10 flex justify-center gap-1.5">
            <Link
                v-for="page in events.last_page"
                :key="page"
                :href="`/events?page=${page}`"
                class="w-9 h-9 text-sm rounded-xl border font-medium flex items-center justify-center no-underline transition-all"
                :class="page === events.current_page
                    ? 'btn-primary text-white border-transparent shadow-sm'
                    : 'bg-white text-gray-500 border-gray-200 hover:border-indigo-300 hover:text-indigo-600'"
            >
                {{ page }}
            </Link>
        </div>
    </div>
</template>

<style scoped>
.text-gradient {
    background: linear-gradient(135deg, #6366f1, #a855f7);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.btn-primary {
    background: linear-gradient(135deg, #6366f1, #a855f7);
    transition: all 0.2s;
}

.btn-primary:hover {
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.event-card {
    transition: all 0.2s;
    text-decoration: none;
}

.event-card:hover {
    border-color: #e5e7eb;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    transform: translateY(-2px);
}

.event-card:hover .card-title {
    color: #6366f1;
}

.card-accent {
    position: absolute;
    inset: 0 0 auto 0;
    height: 3px;
    background: linear-gradient(90deg, #6366f1, #a855f7, #ec4899);
    opacity: 0;
    transition: opacity 0.2s;
}

.event-card:hover .card-accent {
    opacity: 1;
}

.progress-bar {
    transition: width 0.5s ease-out;
}

.bar-green { background: linear-gradient(90deg, #34d399, #10b981); }
.bar-amber { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
.bar-red { background: linear-gradient(90deg, #f87171, #ef4444); }
</style>
