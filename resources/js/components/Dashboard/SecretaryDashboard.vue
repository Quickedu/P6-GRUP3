<script setup lang="ts">
  import { Head, Link, usePage } from '@inertiajs/vue3'
  import { computed } from 'vue';
  import { Calendar, Clock, AlertCircle, Stethoscope, User, FileText, Zap } from 'lucide-vue-next';

  interface Date {
    id: number;
    patient_id: number;
    worker_id: number;
    test_id: number;
    date_time: string;
    time: number;
    estat: string;
    urgencia: string;
    description: string;
    patient: {
      id: number;
      name: string;
      nts: string;
    };
    worker: {
      id: number;
      user: {
        id: number;
        name: string;
      };
    };
    test: {
      id: number;
      name: string;
    };
  }

  defineProps({
    dates: {
      type: Array<Date>,
      default: () => [],
    },
  });

  const getUrgencyColor = (urgency: string) => {
    const colors: Record<string, string> = {
      'urgent': 'bg-red-100 text-red-800',
      'preferent': 'bg-yellow-100 text-yellow-800',
      'no urgent': 'bg-green-100 text-green-800',
    };
    return colors[urgency] || 'bg-gray-100 text-gray-800';
  };

  //show only today dates, paginar
</script>

<template>
  <div class="w-full">
    <h2 class="mb-4 text-2xl font-bold">Agenda</h2>
    
    <div v-if="dates.length > 0" class="space-y-4">
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div v-for="date in dates" :key="date.id" class="rounded-lg border border-gray-200 bg-white p-4 shadow">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
              <div class="flex items-center">
                <span class="font-medium font-semibold mr-1">Test:</span> {{ date.test?.name }}
              </div>
            </div>
            <span :class="`inline-block px-2 py-1 rounded text-xs font-medium ${getUrgencyColor(date.urgencia)}`">
              {{ date.urgencia }}
            </span>
          </div>

          <div v-if="date.description" class="flex items-start text-sm mb-4">
            <FileText class="mr-2 h-4 w-4 flex-shrink-0 text-gray-500" />
            <p class="text-gray-600 line-clamp-2">{{ date.description }}</p>
          </div>

          <div class="flex flex-col font-medium text-gray-900">
            <p class="text-sm mb-3 flex inline-flex">
              <User class="mr-2 h-5 w-5 text-blue-500" />
              <span>{{ date.patient?.name }} ({{ date.patient?.nts }})</span>
            </p>

            <p class="text-sm mb-3 flex inline-flex">
              <Stethoscope class="mr-2 h-5 w-5 text-blue-500" />
              <span>{{ date.worker?.user?.name || 'N/A' }}</span>
            </p>
          </div>
          
          <div class="flex items-center text-sm text-gray-500">
            <Calendar class="mr-2 h-4 w-4" />
            {{ new Date(date.date_time).toLocaleDateString('ca-ES') }} {{ new Date(date.date_time).toLocaleString('ca-ES', { hour: '2-digit', minute: '2-digit' }) }}
          </div>
        </div>
      </div>
    </div>

    <div v-else class="rounded-lg border border-gray-200 bg-gray-50 p-8 text-center">
      <AlertCircle class="mx-auto h-12 w-12 text-gray-400" />
      <p class="mt-4 text-gray-600">No hi han cites</p>
    </div>
  </div>
</template>