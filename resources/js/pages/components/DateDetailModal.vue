<!-- DateDetailModal.vue -->
<script setup lang="ts">
import { Calendar, Stethoscope, User, FileText, Clock, X } from 'lucide-vue-next'
import { patientDetail } from '@/routes';
import { Link } from '@inertiajs/vue3'

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

const props = defineProps<{
  isOpen: boolean
  date: ScheduledDate | null
}>()

const emit = defineEmits<{
  'update:isOpen': [value: boolean]
  close: []
}>()

const getUrgencyColor = (urgency: string) => {
  const colors: Record<string, string> = {
    'urgent': 'bg-red-100 text-red-800',
    'preferent': 'bg-yellow-100 text-yellow-800',
    'no urgent': 'bg-green-100 text-green-800',
  };
  return colors[urgency] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status: string) => {
  const statusMap: Record<string, string> = {
    'scheduled': 'Programada',
    'completed': 'Completada',
    'cancelled': 'Cancel·lada',
    'pending': 'Pendent'
  };
  return statusMap[status] || status;
};

const closeModal = () => {
  emit('update:isOpen', false)
  emit('close')
}

const handleBackdropClick = (e: MouseEvent) => {
  if (e.target === e.currentTarget) {
    closeModal()
  }
}
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen && date"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 transition-opacity"
      @click="handleBackdropClick">
      
      <div class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl transform transition-all">
        <!--header -->
        <div class="sticky top-0 bg-[#f0f7f6] border-b border-[#c5d8d5] px-6 py-4 rounded-t-lg flex justify-between items-center">
          <div>
            <h2 class="text-xl font-bold text-pmf-primary">Detalls de la cita</h2>
          </div>
          <button
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600 transition-colors"
            aria-label="Tancar modal">
            <X class="h-6 w-6" />
          </button>
        </div>

        <!--content -->
        <div class="p-6 space-y-6">
          <!--test and urgency -->
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-lg font-semibold text-gray-900">Prova: {{ date.test?.name }}</h3>
            </div>
            <span :class="`inline-block px-3 py-1 rounded-lg text-sm font-medium ${getUrgencyColor(date.urgencia)}`">
              {{ date.urgencia }}
            </span>
          </div>

          <!--description section -->
          <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-start gap-2 mb-2">
              <FileText class="h-5 w-5 text-pmf-primary shrink-0 mt-0.5" />
              <h4 class="font-semibold text-gray-900">Descripció completa</h4>
            </div>
            <p class="text-gray-700 m-5">
              {{ date.description || 'No hi ha descripció disponible' }}
            </p>
          </div>

          <!-- patient information -->
          <div class="border-t border-gray-200 pt-4">
            <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
              <User class="h-5 w-5 text-pmf-primary" />
              Informació del pacient
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 ml-7">
              <div>
                <p class="text-xs text-gray-500">Nom complet</p>
                <Link :href="patientDetail(date.patient.id)" @click.stop class="font-medium text-pmf-primary hover:underline">
                  {{ date.patient?.name }}
                </Link>
              </div>
              <div>
                <p class="text-xs text-gray-500">NTS</p>
                <p class="font-medium text-gray-900">{{ date.patient?.nts }}</p>
              </div>
            </div>
          </div>

          <!--professional information -->
          <div class="border-t border-gray-200 pt-4">
            <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
              <Stethoscope class="h-5 w-5 text-pmf-primary" />
              Informació del professional
            </h4>
            <div class="ml-7">
              <p class="text-xs text-gray-500">Doctor/Tècnic</p>
              <p class="font-medium text-gray-900">{{ date.worker?.user?.name || 'No assignat' }}</p>
            </div>
          </div>

          <!-- date and time information -->
          <div class="border-t border-gray-200 pt-4">
            <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
              <Calendar class="h-5 w-5 text-pmf-primary" />
              Data i hora
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 ml-7">
              <div>
                <p class="text-xs text-gray-500">Data</p>
                <p class="font-medium text-gray-900">{{ new Date(date.date_time).toLocaleDateString('ca-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500">Hora</p>
                <p class="font-medium text-gray-900">{{ new Date(date.date_time).toLocaleString('ca-ES', { hour: '2-digit', minute: '2-digit' }) }}</p>
              </div>
            </div>
          </div>

          <!--duration -->
          <div class="border-t border-gray-200 pt-4">
            <h4 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
              <Clock class="h-5 w-5 text-pmf-primary" />
              Durada
            </h4>
            <div class="ml-7">
              <p class="font-medium text-gray-900">{{ date.time }} minuts</p>
            </div>
          </div>
        </div>

        <!--footer -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 rounded-b-lg flex justify-end">
          <button
            @click="closeModal"
            class="px-4 py-2 bg-pmf-primary text-white rounded-lg hover:bg-pmf-green transition-colors">
            Tancar
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>