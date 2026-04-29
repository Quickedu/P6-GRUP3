<script setup lang="ts">
  import { ref, computed } from 'vue';
  import { Calendar, Stethoscope, User, FileText, ChevronLeft, ChevronRight, X, Loader } from 'lucide-vue-next';

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

  interface Props {
    initialDates?: ScheduledDate[];
  }

  withDefaults(defineProps<Props>(), {
    initialDates: () => [],
  });

  const selectedDate = ref<string | null>(null);
  const doctorName = ref<string>('');
  const displayedDates = ref<ScheduledDate[]>([]);
  const isLoading = ref(false);
  const hasSearched = ref(false);

  const getUrgencyColor = (urgency: string) => {
    const colors: Record<string, string> = {
      'urgent': 'bg-red-100 text-red-800',
      'preferent': 'bg-yellow-100 text-yellow-800',
      'no urgent': 'bg-green-100 text-green-800',
    };
    return colors[urgency] || 'bg-gray-100 text-gray-800';
  };

  const getDateKey = (date: Date): string => {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  };

  const fetchFilteredDates = async () => {
    isLoading.value = true;
    hasSearched.value = true;

    const params = new URLSearchParams();
    if (selectedDate.value) {
      params.append('date', selectedDate.value);
    }
    if (doctorName.value.trim()) {
      params.append('doctorName', doctorName.value.trim());
    }

    try {
      const response = await fetch(`/filter-dates?${params.toString()}`);
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      displayedDates.value = data;
    } catch (error) {
      console.error('Error fetching filtered dates:', error);
      displayedDates.value = [];
    } finally {
      isLoading.value = false;
    }
  };

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

  const clearFilters = () => {
    selectedDate.value = null;
    doctorName.value = '';
    displayedDates.value = [];
    hasSearched.value = false;
  };
</script>

<template>
  <div class="w-full">
    <div class="mb-3">
      <h2 class="text-2xl font-bold mb-4 text-pmf-primary">Agenda</h2>
      
      <!--filter controls-->
      <div class="rounded-lg p-2 border border-gray-200">
        <div class="flex flex-col gap-3">
          <!--clear filters button-->
          <button
            v-if="selectedDate !== null || doctorName.trim()"
            @click="clearFilters"
            class="flex items-center gap-1 pt-1 px-1 text-sm font-medium text-pmf-primary cursor-pointer rounded-lg transition hover:bg-gray-100">
            <X class="h-4 w-4" />
            Esborrar filtres
          </button>

          <!--date filter-->
          <div class="flex items-center justify-between gap-2">
            <button
              @click="previousDay"
              class="flex items-center gap-2 rounded-lg px-2 py-2 text-sm font-medium transition bg-pmf-green hover:bg-pmf-primary text-white cursor-pointer">
              <ChevronLeft class="h-4 w-4"/>
              Anterior
            </button>

            <div class="flex flex-col items-center">
              <label for="selecciona-data" class="text-xs text-gray-600 mb-1">Selecciona data</label>
              <input
                id="selecciona-data"
                type="date"
                :value="selectedDate || ''"
                @change="setFilterDateFromInput"
                class="rounded-lg px-3 py-2 text-sm border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pmf-primary bg-white cursor-pointer" />
            </div>

            <button
              @click="nextDay"
              class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition bg-pmf-green hover:bg-pmf-primary text-white cursor-pointer">
              Següent
              <ChevronRight class="h-4 w-4" />
            </button>
          </div>

          <!--doctor name filter-->
          <div class="flex flex-col">
            <label for="doctor-name" class="text-xs text-gray-600 mb-1">Filtra per nom de doctor/tècnic</label>
            <input
              id="doctor-name"
              v-model="doctorName"
              type="text"
              placeholder="Escriu el nom del doctor o tècnic..."
              class="rounded-lg px-3 py-2 text-sm border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pmf-primary bg-white" />
          </div>

          <!--search button-->
          <button
            @click="fetchFilteredDates"
            :disabled="isLoading"
            class="w-full flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition bg-pmf-primary hover:bg-pmf-green text-white cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
            <Loader v-if="isLoading" class="h-4 w-4 animate-spin" />
            <span v-else>Cercar</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Results -->
    <div v-if="hasSearched && isLoading" class="flex justify-center items-center p-8">
      <Loader class="h-8 w-8 animate-spin text-pmf-primary" />
    </div>

    <div v-else-if="hasSearched && displayedDates.length > 0" class="space-y-4 px-5">
      <div v-for="date in displayedDates" :key="date.id" class="rounded-lg border border-gray-200 bg-white p-3 gap-2">
        <div class="flex items-start justify-between mb-3">
          <div class="flex-1">
            <div class="flex items-center">
              <span class="font-semibold mr-1">Test:</span> {{ date.test?.name }}
            </div>
          </div>
          <span :class="`inline-block px-2 py-1 rounded-lg text-xs font-medium ${getUrgencyColor(date.urgencia)}`">
            {{ date.urgencia }}
          </span>
        </div>

        <div v-if="date.description" class="flex items-start text-sm mb-4">
          <FileText class="mr-2 h-4 w-4 shrink-0 text-gray-500" />
          <p class="text-gray-600 line-clamp-2">{{ date.description }}</p>
        </div>

        <div class="font-medium text-gray-900 flex flex-inline gap-2">
          <p class="text-sm mb-3 flex">
            <User class="mr-2 h-7 w-7 text-pmf-primary bg-pmf-secondary p-1 rounded-md" />
            <span>{{ date.patient?.name }} ({{ date.patient?.nts }})</span>
          </p>

          <p class="text-sm mb-3 flex">
            <Stethoscope class="mr-2 h-7 w-7 text-pmf-primary bg-pmf-secondary p-1 rounded-md" />
            <span>{{ date.worker?.user?.name || 'N/A' }}</span>
          </p>
        </div>

        <div class="flex items-center text-sm text-gray-500">
          <Calendar class="mr-2 h-4 w-4" />
          {{ new Date(date.date_time).toLocaleDateString('ca-ES') }}
          {{ new Date(date.date_time).toLocaleString('ca-ES', { hour: '2-digit', minute: '2-digit' }) }}
        </div>
      </div>
    </div>

    <!-- Empty results alert -->
    <div v-else-if="hasSearched && displayedDates.length === 0" class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-center">
      <p class="text-gray-600">No hi han cites amb els filtres seleccionats</p>
    </div>

    <!-- No search performed yet -->
    <div v-else class="rounded-lg border border-gray-200 bg-gray-50 p-8 text-center">
      <p class="text-gray-600">Aplica els filtres per veure les cites</p>
    </div>
  </div>
</template>
