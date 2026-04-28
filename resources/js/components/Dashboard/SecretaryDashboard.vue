<script setup lang="ts">
  import { Head, Link, usePage } from '@inertiajs/vue3'
  import { computed, ref } from 'vue';
  import { Calendar, Stethoscope, User, FileText, ChevronLeft, ChevronRight, X } from 'lucide-vue-next';

  interface ScheduledDate {
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

  const props = defineProps({
    dates: {
      type: Array<ScheduledDate>,
      default: () => [],
    },
  });

  const selectedDate = ref<string | null>(null);

  const getDateKey = (date: Date): string => {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  };

  const datesGroupedByDay = computed(() => {
    const groups: Record<string, ScheduledDate[]> = {};

    props.dates.forEach((date) => {
      const dateKey = getDateKey(new Date(date.date_time));
      if (!groups[dateKey]) {
        groups[dateKey] = [];
      }
      groups[dateKey].push(date);
    });
    return groups;
  });

  const displayedDates = computed(() => {
    if (selectedDate.value === null) {
      return props.dates;
    }
    return datesGroupedByDay.value[selectedDate.value] || [];
  });

  const previousDay = () => {
    const currentDate = selectedDate.value || getDateKey(new Date());
    const date = new Date(currentDate + 'T00:00:00');
    date.setDate(date.getDate() - 1);
    selectedDate.value = getDateKey(date);
  };

  const nextDay = () => {
    const currentDate = selectedDate.value || getDateKey(new Date());
    const date = new Date(currentDate + 'T00:00:00');
    date.setDate(date.getDate() + 1);
    selectedDate.value = getDateKey(date);
  };

  const setFilterDateFromInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.value) {
      selectedDate.value = input.value;
    }
  };

  const clearFilter = () => {
    selectedDate.value = null;
  };

  const getUrgencyColor = (urgency: string) => {
    const colors: Record<string, string> = {
      'urgent': 'bg-red-100 text-red-800',
      'preferent': 'bg-yellow-100 text-yellow-800',
      'no urgent': 'bg-green-100 text-green-800',
    };
    return colors[urgency] || 'bg-gray-100 text-gray-800';
  };
</script>

<template>
  <div class="w-full">
    <div class="mb-3">
      <h2 class="text-2xl font-bold mb-4">Agenda</h2>
      <!--filter controls -->
      <div class="rounded-lg border border-gray-200 bg-white p-2">
        <div class="flex flex-col">
          <!--delete filter-->
          <button
            v-if="selectedDate !== null"
            @click="clearFilter"
            class="flex items-center gap-1 pt-1 px-1 text-sm font-medium text-blue-700 cursor-pointer rounded transition">
            <X class="h-4 w-4"/>
            Esborrar filtre
          </button>
          <!--date input-->
          <div class="flex items-center justify-between gap-2">
            <button
              @click="previousDay"
              class="flex items-center gap-2 rounded px-2 text-sm font-medium transition hover:bg-gray-100 cursor-pointer">
              <ChevronLeft class="h-4 w-4"/>
              Anterior
            </button>

            <div class="flex flex-col items-center">
              <label for="selecciona data" class="text-xs text-gray-600 mb-1">Selecciona data</label>
              <input name="selecciona data" id="selecciona data"
                type="date"
                :value="selectedDate || ''"
                @change="setFilterDateFromInput"
                class="rounded px-3 py-2 text-sm border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"/>
            </div>

            <button
              @click="nextDay"
              class="flex items-center gap-2 rounded px-3 py-2 text-sm font-medium transition hover:bg-gray-100 cursor-pointer">
              Següent
              <ChevronRight class="h-4 w-4"/>
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <div v-if="dates.length > 0" class="space-y-4">
        <div v-for="date in displayedDates" :key="date.id" class="rounded-lg border border-gray-200 bg-white p-3 gap-2">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
              <div class="flex items-center">
                <span class="font-semibold mr-1">Test:</span> {{ date.test?.name }}
              </div>
            </div>
            <span :class="`inline-block px-2 py-1 rounded text-xs font-medium ${getUrgencyColor(date.urgencia)}`">
              {{ date.urgencia }}
            </span>
          </div>

          <div v-if="date.description" class="flex items-start text-sm mb-4">
            <FileText class="mr-2 h-4 w-4 shrink-0 text-gray-500" />
            <p class="text-gray-600 line-clamp-2">{{ date.description }}</p>
          </div>

          <div class="font-medium text-gray-900 flex flex-inline gap-2">
            <p class="text-sm mb-3 flex">
              <User class="mr-2 h-5 w-5 text-blue-500" />
              <span>{{ date.patient?.name }} ({{ date.patient?.nts }})</span>
            </p>

            <p class="text-sm mb-3 flex">
              <Stethoscope class="mr-2 h-5 w-5 text-blue-500" />
              <span>{{ date.worker?.user?.name || 'N/A' }}</span>
            </p>
          </div>
          
          <div class="flex items-center text-sm text-gray-500">
            <Calendar class="mr-2 h-4 w-4" />
            {{ new Date(date.date_time).toLocaleDateString('ca-ES') }} {{ new Date(date.date_time).toLocaleString('ca-ES', { hour: '2-digit', minute: '2-digit' }) }}
          </div>
        </div>

      <!--empty result alert -->
      <div v-if="displayedDates.length === 0 && selectedDate" class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-center">
        <p class="text-gray-600">No hi han cites per a {{selectedDate}}</p>
      </div>
    </div>

    <div v-else class="rounded-lg border border-gray-200 bg-gray-50 p-8 text-center">
      <p class="mt-4 text-gray-600">No hi han cites</p>
    </div>
  </div>
</template>