<script setup lang="ts">
import { X } from 'lucide-vue-next'
import { computed, ref } from 'vue'
import { filterPatientDates } from '@/routes'

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

const emit = defineEmits<{
  'results-found': [dates: ScheduledDate[], error: string]
  cleared: []
}>()

const nts = ref('')
const loading = ref(false)
const error = ref('')

const isValidNts = computed(() => {
  const ntsRegex = /^[A-Z]{4}\d{10}$/

  return ntsRegex.test(nts.value)
})

const handleSearch = async () => {
  if (!isValidNts.value) {
    error.value = 'El NTS ha de tenir 4 lletres majúscules i 10 dígits, sense espais.'
    return
  }

  error.value = ''
  loading.value = true

  try {
    const response = await fetch(filterPatientDates.url({ query: { nts: nts.value } }))
    const data = await response.json()
    
    console.log('Response from server:', data) // Add this debug line

    if (data.status === 'success') {
      console.log('Dates found:', data.data) // Add this debug line
      emit('results-found', data.data, '')
    } else {
      const errorMsg = data.message || 'Error al cercar pacient'
      error.value = errorMsg
      emit('results-found', [], errorMsg)
    }
  } catch (err) {
    console.error('Fetch error:', err) // Add this debug line
    error.value = 'Error al realitzar la cerca'
    emit('results-found', [], error.value)
  } finally {
    loading.value = false
  }
}

const handleReset = () => {
  nts.value = ''
  error.value = ''
  emit('cleared')
}

</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-col gap-4">
      <h2 class="font-bold text-lg">Filtrar cites per pacient</h2>

      <div class="flex gap-2">
        <div class="flex-1">
          <input
            v-model="nts"
            type="text"
            placeholder="Ej: ABCD1234567890"
            @keydown.enter="handleSearch"
            class="w-full rounded-lg px-3 py-2 text-sm border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pmf-primary bg-white cursor-pointer"
            maxlength="14"/>
          <p v-if="nts && !isValidNts" class="mt-1 text-sm text-red-600">
            Format invàlid. Ha de ser: 4 lletres + 10 dígits.
          </p>
        </div>
        
        <button
          type="button"
          @click="handleSearch"
          :disabled="!isValidNts || loading"
          class="flex items-center gap-2 rounded-lg px-2 text-sm font-medium transition bg-pmf-green hover:bg-pmf-primary text-white cursor-pointer">
          {{ loading ? 'Buscant...' : 'Buscar' }}
        </button>
        
        <button
          v-if="nts"
          type="button"
          @click="handleReset"
          class="flex items-center gap-1 pt-1 px-1 text-sm font-medium text-pmf-primary cursor-pointer rounded-lg transition hover:bg-gray-100">
          <X class="h-4 w-4"/>
          Eliminar filtre
        </button>
      </div>
    </div>
  </div>
</template>