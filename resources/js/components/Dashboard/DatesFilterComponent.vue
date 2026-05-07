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

  const emit = defineEmits<{
    resultsFound: [dates: ScheduledDate[], error: string]
    cleared: []
  }>();

  const selectedDate = ref<string | null>(null);
  const doctorName = ref<string>('');
  const isLoading = ref(false);

  const getDateKey = (date: Date): string => {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  };

  const fetchFilteredDates = async () => {
    isLoading.value = true;

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
      emit('resultsFound', data, '');
    } catch (error) {
      console.error('Error fetching filtered dates:', error);
      emit('resultsFound', [], 'Error al realitzar la cerca');
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
    emit('cleared');
  };
</script>

<template>
  <div class="rounded-lg p-2 border border-gray-200 mb-3 w-full flex flex-col gap-3">
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
</template>
