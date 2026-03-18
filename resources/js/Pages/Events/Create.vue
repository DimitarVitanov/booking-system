<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import TiptapEditor from '@/Components/TiptapEditor.vue';

const form = useForm({
    name: '',
    description: '',
    start_date: '',
    end_date: '',
    capacity: null,
});

function submit() {
    form.transform((data) => ({
        ...data,
        start_date: data.start_date ? data.start_date.replace('T', ' ') + ':00' : '',
        end_date: data.end_date ? data.end_date.replace('T', ' ') + ':00' : '',
    })).post('/events');
}
</script>

<template>
    <Head title="Create Event" />
    <div class="max-w-2xl mx-auto px-6 py-10">
        <div class="mb-8">
            <Link href="/events" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-indigo-600 transition mb-3 no-underline">
                ← Back to events
            </Link>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Create <span class="text-gradient">Event</span>
            </h1>
            <p class="mt-1 text-gray-500 text-sm">Fill in the details below to publish a new event.</p>
        </div>

        <form @submit.prevent="submit" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Event Name</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="form-input"
                        placeholder="e.g. Laravel Meetup 2026"
                    />
                    <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-500 font-medium">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                    <TiptapEditor v-model="form.description" />
                    <p v-if="form.errors.description" class="mt-1.5 text-xs text-red-500 font-medium">{{ form.errors.description }}</p>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-3">Schedule</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-xs font-medium text-gray-500 mb-1.5">Starts</label>
                            <input
                                id="start_date"
                                v-model="form.start_date"
                                type="datetime-local"
                                class="form-input"
                            />
                            <p v-if="form.errors.start_date" class="mt-1.5 text-xs text-red-500 font-medium">{{ form.errors.start_date }}</p>
                        </div>
                        <div>
                            <label for="end_date" class="block text-xs font-medium text-gray-500 mb-1.5">Ends</label>
                            <input
                                id="end_date"
                                v-model="form.end_date"
                                type="datetime-local"
                                class="form-input"
                            />
                            <p v-if="form.errors.end_date" class="mt-1.5 text-xs text-red-500 font-medium">{{ form.errors.end_date }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="capacity" class="block text-sm font-semibold text-gray-700 mb-1.5">Total Capacity</label>
                    <div class="relative">
                        <input
                            id="capacity"
                            v-model.number="form.capacity"
                            type="number"
                            min="1"
                            class="form-input pr-14"
                            placeholder="100"
                        />
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">seats</span>
                    </div>
                    <p v-if="form.errors.capacity" class="mt-1.5 text-xs text-red-500 font-medium">{{ form.errors.capacity }}</p>
                </div>
            </div>

            <div class="flex items-center justify-between px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-100">
                <Link href="/events" class="text-sm font-medium text-gray-400 hover:text-gray-600 transition no-underline">
                    Cancel
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="btn-submit inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-semibold text-white disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ form.processing ? 'Publishing...' : 'Publish Event' }}
                </button>
            </div>
        </form>
    </div>
</template>

<style scoped>
.text-gradient {
    background: linear-gradient(135deg, #6366f1, #a855f7);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

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

.btn-submit {
    background: linear-gradient(135deg, #6366f1, #a855f7);
    transition: all 0.2s;
}

.btn-submit:hover:not(:disabled) {
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}
</style>
