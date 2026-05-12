<script setup lang="ts">
  import { ChevronLeft, ChevronRight, X, Loader } from 'lucide-vue-next';
  import { computed, ref } from 'vue';
  import { filterDates } from '@/routes';

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
    doctors?: DoctorOption[];
  }

  interface DoctorOption {
    id: number;
    name: string;
  }

  const props = withDefaults(defineProps<Props>(), {
    initialDates: () => [],
    doctors: () => [],
  });

  const emit = defineEmits<{
    'results-found': [dates: ScheduledDate[], error: string]
    cleared: []
  }>();

  const selectedDate = ref<string | null>(null);
  const doctorSearch = ref<string>('');
  const isLoading = ref(false);

  const normalizeSearch = (value: string): string => value.trim().toLocaleLowerCase('ca-ES');

  const selectedDoctorId = computed<number | null>(() => {
    const search = doctorSearch.value.trim();

    if (!search) {
      return null;
    }

    const doctorByName = props.doctors.find((doctor) => normalizeSearch(doctor.name) === normalizeSearch(search));

    return doctorByName?.id ?? null;
  });

  const hasInvalidDoctorSearch = computed(() => doctorSearch.value.trim() !== '' && selectedDoctorId.value === null);

  const getDateKey = (date: Date): string => {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
  };

  const fetchFilteredDates = async () => {
    if (hasInvalidDoctorSearch.value) {
      emit('results-found', [], 'Selecciona un doctor de la llista');

      return;
    }

    isLoading.value = true;

    const query: Record<string, string | number> = {};

    if (selectedDate.value) {
      query.date = selectedDate.value;
    }

    if (selectedDoctorId.value !== null) {
      query.doctor_id = selectedDoctorId.value;
    }

    try {
      const response = await fetch(filterDates.url({ query }));

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();

      if (data.status === 'success') {
        emit('results-found', data.data, '');
      } else {
        const errorMessage = data.message || 'Error al realitzar la cerca';

        emit('results-found', [], errorMessage);
      }
    } catch (error) {
      console.error('Error fetching filtered dates:', error);
      emit('results-found', [], 'Error al realitzar la cerca');
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
    selectedDate.value = input.value || null;
  };

  const clearFilters = () => {
    selectedDate.value = null;  
    doctorSearch.value = '';
    emit('cleared');
  };
</script>

<template>
  <div class="rounded-lg p-2 border border-gray-200 mb-3 w-full flex flex-col gap-3">
    <!--clear filters button-->
    <button
      v-if="selectedDate !== null || doctorSearch.trim()"
      type="button"
      @click="clearFilters"
      class="flex items-center gap-1 pt-1 px-1 text-sm font-medium text-pmf-primary cursor-pointer rounded-lg transition hover:bg-gray-100">
      <X class="h-4 w-4" />
      Esborrar filtres
    </button>

    <!--date filter-->
    <div class="flex items-center justify-between gap-2">
      <button
        type="button"
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
        type="button"
        @click="nextDay"
        class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition bg-pmf-green hover:bg-pmf-primary text-white cursor-pointer">
        Següent
        <ChevronRight class="h-4 w-4" />
      </button>
    </div>

    <!--doctor filter-->
    <div class="flex flex-col">
      <label for="doctor-id" class="text-xs text-gray-600 mb-1">Filtra per doctor/tècnic</label>
      <input
        id="doctor-id"
        v-model="doctorSearch"
        type="text"
        list="doctor-options"
        placeholder="Escriu o selecciona un doctor..."
        class="rounded-lg px-3 py-2 text-sm border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pmf-primary bg-white" />
      <datalist id="doctor-options">
        <option
          v-for="doctor in doctors"
          :key="doctor.id"
          :value="doctor.name">
          {{ doctor.name }}
        </option>
      </datalist>
      <p v-if="hasInvalidDoctorSearch" class="mt-1 text-sm text-red-600">
        Selecciona un doctor de la llista.
      </p>
    </div>

    <!--search button-->
    <button
      type="button"
      @click="fetchFilteredDates"
      :disabled="isLoading"
      class="w-full flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition bg-pmf-primary hover:bg-pmf-green text-white cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
      <Loader v-if="isLoading" class="h-4 w-4 animate-spin" />
      <span v-else>Cercar</span>
    </button>
  </div>
</template>
