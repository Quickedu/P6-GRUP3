<!-- DateDetailModal.vue -->
<script setup lang="ts">
import { CircleCheck, X } from 'lucide-vue-next'
import { computed, ref, watch } from 'vue';
import { ajaxDoctor, reSchedule } from '@/actions/App/Http/Controllers/Workers/Secretary/DatesController';

interface ScheduledDate {
  id?: number;
  date_time: string;
  time: number;
  worker?: {
    user?: {
      id: number;
      name: string;
    };
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

const dataCita = ref('');
const estimatedMinutes = ref<number | null>(null);
const availableSlots = ref<string[]>([]);
const slotsLoading = ref(false);
const slotsMessage = ref('');
const startTimeOptions = ref<string[]>([]);
const selectedStartTime = ref('');
const requiredSlotMinutes = ref<number | null>(null);
const saveMessage = ref('');
const saveStatus = ref<'success' | 'error' | ''>('');
const isSaving = ref(false);

const doctorUserId = computed(() => props.date?.worker?.user?.id ?? null);
const doctorName = computed(() => props.date?.worker?.user?.name || 'No assignat');

function extractDatePart(dateTime: string): string {
  if (!dateTime) {
    return '';
  }

  const separator = dateTime.includes('T') ? 'T' : ' ';
  const [datePart] = dateTime.split(separator);

  return datePart ?? '';
}

function extractTimePart(dateTime: string): string {
  if (!dateTime) {
    return '';
  }

  const separator = dateTime.includes('T') ? 'T' : ' ';
  const timePart = dateTime.split(separator)[1] ?? '';

  return timePart.slice(0, 5);
}

function parseDateTimeFromParts(dateValue: string, timeValue: string): Date | null {
  const [rawHours, rawMinutes] = timeValue.split(':');
  const hours = Number(rawHours);
  const minutes = Number(rawMinutes);

  if (!Number.isInteger(hours) || !Number.isInteger(minutes)) {
    return null;
  }

  if (hours < 0 || hours > 23 || minutes < 0 || minutes > 59) {
    return null;
  }

  const dateTime = new Date(`${dateValue}T00:00:00`);

  if (Number.isNaN(dateTime.getTime())) {
    return null;
  }

  dateTime.setHours(hours, minutes, 0, 0);

  return dateTime;
}

function formatDateTimeForSubmit(dateValue: Date): string {
  const year = dateValue.getFullYear();
  const month = String(dateValue.getMonth() + 1).padStart(2, '0');
  const day = String(dateValue.getDate()).padStart(2, '0');
  const hour = String(dateValue.getHours()).padStart(2, '0');
  const minute = String(dateValue.getMinutes()).padStart(2, '0');

  return `${year}-${month}-${day} ${hour}:${minute}:00`;
}

function formatTimeForDisplay(dateValue: Date): string {
  const hour = String(dateValue.getHours()).padStart(2, '0');
  const minute = String(dateValue.getMinutes()).padStart(2, '0');

  return `${hour}:${minute}`;
}

function roundUpToMinutes(dateValue: Date, interval: number): Date {
  const rounded = new Date(dateValue.getTime());
  const minutes = rounded.getMinutes();
  const remainder = minutes % interval;

  if (remainder === 0) {
    return rounded;
  }

  rounded.setMinutes(minutes + interval - remainder, 0, 0);

  return rounded;
}

function resetSlotSelection() {
  availableSlots.value = [];
  startTimeOptions.value = [];
  selectedStartTime.value = '';
  requiredSlotMinutes.value = null;
}

function buildStartTimeOptions(slotRanges: string[]): string[] {
  if (!dataCita.value || estimatedMinutes.value === null) {
    return [];
  }

  const requiredMinutes = requiredSlotMinutes.value ?? estimatedMinutes.value;
  const validStartTimes = new Set<string>();

  slotRanges.forEach((slotRange) => {
    const [rawStartTime, rawEndTime] = slotRange.split(/\s*-\s*/);

    if (!rawStartTime || !rawEndTime) {
      return;
    }

    const slotStart = parseDateTimeFromParts(dataCita.value, rawStartTime);
    const slotEnd = parseDateTimeFromParts(dataCita.value, rawEndTime);

    if (!slotStart || !slotEnd || slotStart >= slotEnd) {
      return;
    }

    const first = roundUpToMinutes(slotStart, 5);

    if (first.getTime() + requiredMinutes * 60_000 <= slotEnd.getTime()) {
      validStartTimes.add(formatTimeForDisplay(first));
    }

    let cursor = roundUpToMinutes(first, 15);

    if (cursor.getTime() === first.getTime()) {
      cursor = new Date(first.getTime());
      cursor.setMinutes(cursor.getMinutes() + 15);
    }

    while (cursor.getTime() + requiredMinutes * 60_000 <= slotEnd.getTime()) {
      validStartTimes.add(formatTimeForDisplay(cursor));
      cursor.setMinutes(cursor.getMinutes() + 15);
    }
  });

  return [...validStartTimes].sort((firstTime, secondTime) => {
    const firstDate = parseDateTimeFromParts(dataCita.value, firstTime);
    const secondDate = parseDateTimeFromParts(dataCita.value, secondTime);

    if (!firstDate || !secondDate) {
      return 0;
    }

    return firstDate.getTime() - secondDate.getTime();
  });
}

const selectedDateTime = computed(() => {
  if (!dataCita.value || !selectedStartTime.value) {
    return '';
  }

  const startDateTime = parseDateTimeFromParts(dataCita.value, selectedStartTime.value);

  return startDateTime ? formatDateTimeForSubmit(startDateTime) : '';
});

watch(() => props.date, (currentDate) => {
  if (!currentDate) {
    dataCita.value = '';
    selectedStartTime.value = '';
    estimatedMinutes.value = null;
    resetSlotSelection();
    slotsMessage.value = '';
    saveMessage.value = '';
    saveStatus.value = '';

    return;
  }

  dataCita.value = extractDatePart(currentDate.date_time);
  selectedStartTime.value = extractTimePart(currentDate.date_time);
  estimatedMinutes.value = typeof currentDate.time === 'number' ? currentDate.time : null;
  resetSlotSelection();
  slotsMessage.value = '';
  saveMessage.value = '';
  saveStatus.value = '';
}, { immediate: true });

watch(() => props.isOpen, (isOpen) => {
  if (!isOpen || !props.date) {
    return;
  }

  if (!dataCita.value) {
    dataCita.value = extractDatePart(props.date.date_time);
  }

  if (estimatedMinutes.value === null) {
    estimatedMinutes.value = typeof props.date.time === 'number' ? props.date.time : null;
  }

  checkAvailability();
}, { immediate: true });

const closeModal = () => {
  emit('update:isOpen', false)
  emit('close')
}

function getCsrfToken(): string {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

async function submitReschedule() {
  if (!props.date?.id || !selectedDateTime.value) {
    saveStatus.value = 'error';
    saveMessage.value = 'Selecciona una hora disponible abans de canviar la data.';

    return;
  }

  isSaving.value = true;
  saveMessage.value = '';
  saveStatus.value = '';

  try {
    const csrfToken = getCsrfToken();
    const response = await fetch(reSchedule.url({ date: props.date.id }), {
      method: 'PUT',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
      },
      body: JSON.stringify({
        date_time: selectedDateTime.value,
      }),
    });
    const payload = await response.json().catch(() => null);

    if (!response.ok || payload?.status === 'error') {
      saveStatus.value = 'error';
      saveMessage.value = payload?.message || 'No s\'ha pogut reprogramar la cita.';

      return;
    }

    saveStatus.value = 'success';
    saveMessage.value = payload?.message || 'Cita reprogramada correctament.';
    closeModal();
  } catch {
    saveStatus.value = 'error';
    saveMessage.value = 'Error de connexió reprogramant la cita.';
  } finally {
    isSaving.value = false;
  }
}

async function checkAvailability() {
  if (!doctorUserId.value || !dataCita.value || estimatedMinutes.value === null) {
    resetSlotSelection();
    slotsMessage.value = 'Selecciona data abans de veure franges disponibles.';
    saveMessage.value = '';
    saveStatus.value = '';

    return;
  }

  const preferredStartTime = selectedStartTime.value
    || (props.date ? extractTimePart(props.date.date_time) : '');

  slotsLoading.value = true;
  resetSlotSelection();
  slotsMessage.value = '';
  saveMessage.value = '';
  saveStatus.value = '';

  try {
    const routeParams = props.date?.id
      ? { id: doctorUserId.value, idDate: props.date.id }
      : { id: doctorUserId.value };
    const response = await fetch(ajaxDoctor.url(routeParams, {
      query: {
        date: dataCita.value,
        time: estimatedMinutes.value,
      },
    }));
    const data = await response.json();

    availableSlots.value = Array.isArray(data?.data?.slots) ? data.data.slots : [];
    requiredSlotMinutes.value = Number(data?.data?.required_minutes) || estimatedMinutes.value;
    const apiStartTimes = Array.isArray(data?.data?.start_times)
      ? data.data.start_times
      : [];

    startTimeOptions.value = apiStartTimes.length > 0
      ? apiStartTimes
      : buildStartTimeOptions(availableSlots.value);
    selectedStartTime.value = startTimeOptions.value.includes(preferredStartTime)
      ? preferredStartTime
      : (startTimeOptions.value[0] || '');

    slotsMessage.value = startTimeOptions.value.length > 0
      ? 'Selecciona una hora d\'inici disponible:'
      : availableSlots.value.length > 0
        ? 'No hi ha hores d\'inici possibles per la durada indicada.'
        : 'Aquest doctor no té franges disponibles amb aquest temps.';
  } catch {
    resetSlotSelection();
    slotsMessage.value = 'Error de connexió consultant les franges del doctor.';
  } finally {
    slotsLoading.value = false;
  }
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
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-lg flex justify-between items-center">
          <div>
            <h2 class="text-xl font-bold text-pmf-primary">Reagendar la cita</h2>
          </div>
          <button
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600 transition-colors">
            <X class="h-6 w-6" />
          </button>
        </div>

        <!--content -->
        <div class="p-6 space-y-6">
          <div class="border border-gray-200 rounded-lg p-4 bg-white">
            <h3 class="text-lg font-semibold text-gray-900">Reprogramar cita</h3>
            <p class="mt-1 text-sm text-gray-500">
              Selecciona un dia, comprova les franges disponibles i escull una hora.
            </p>
          </div>

          <div class="grid gap-4 md:grid-cols-3">
            <div class="w-full">
              <label
                for="reschedule-date"
                class="text-xs font-semibold tracking-widest text-gray-500"
              >
                DATA DE LA CITA
              </label>
              <input
                id="reschedule-date"
                v-model="dataCita"
                type="date"
                :min="new Date().toISOString().split('T')[0]"
                class="mt-3 h-9 w-full rounded-md border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-pmf-primary focus:outline-none focus:ring-2 focus:ring-pmf-primary/30"
                autocomplete="off"
                required
                aria-required="true"
              />
            </div>

            <div class="w-full">
              <label
                class="mb-3 text-xs font-semibold tracking-widest text-gray-500"
              >
                DOCTOR
              </label>
              <input
                type="text"
                :value="doctorName"
                class="h-9 w-full rounded-md border border-gray-300 bg-gray-50 px-3 text-sm text-gray-700"
                disabled
              />
            </div>

            <button
              class="mt-7 inline-flex h-9 shrink-0 cursor-pointer items-center justify-center rounded-md bg-pmf-primary px-5 py-3 text-sm text-pmf-secondary hover:bg-pmf-green focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-pmf-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
              type="button"
              @click="checkAvailability"
              :disabled="slotsLoading || !doctorUserId || !dataCita || estimatedMinutes === null"
            >
              <CircleCheck class="mr-3 size-5" aria-hidden="true" />
              {{ slotsLoading ? 'Carregant...' : 'Comprovar disponibilitat' }}
            </button>
          </div>

          <div class="rounded-md border border-pmf-primary/20 bg-white p-4">
            <p id="slots-heading" class="text-sm font-medium text-pmf-primary">
              Franges horaries del doctor
            </p>

            <p
              id="slots-message"
              class="mt-2 text-sm text-muted-foreground"
              role="status"
              aria-live="polite"
            >
              {{ slotsMessage }}
            </p>

            <div
              class="mt-3 flex flex-wrap gap-2"
              aria-labelledby="slots-heading"
              aria-describedby="slots-message"
            >
              <legend class="sr-only">
                Seleccio d'hora d'inici disponible
              </legend>
              <label
                v-for="startTime in startTimeOptions"
                :key="startTime"
                class="cursor-pointer"
              >
                <input
                  v-model="selectedStartTime"
                  type="radio"
                  name="start_time"
                  :value="startTime"
                  class="peer sr-only"
                  :aria-label="`Seleccionar hora ${startTime}`"
                />
                <span
                  class="inline-flex items-center rounded-md border border-pmf-primary/25 px-3 py-1.5 text-sm text-pmf-primary transition hover:bg-pmf-secondary/70 peer-checked:border-pmf-primary peer-checked:bg-pmf-primary peer-checked:text-pmf-secondary peer-focus-visible:ring-2 peer-focus-visible:ring-pmf-primary peer-focus-visible:ring-offset-2"
                >
                  {{ startTime }}
                </span>
              </label>
            </div>

            <p
              v-if="selectedDateTime"
              class="mt-3 text-sm font-medium text-pmf-primary"
              role="status"
              aria-live="polite"
            >
              Data i hora seleccionada: {{ selectedDateTime }}
            </p>
            <p
              v-if="saveMessage"
              class="mt-2 text-sm font-medium"
              :class="saveStatus === 'success' ? 'text-green-600' : 'text-red-600'"
              role="status"
              aria-live="polite"
            >
              {{ saveMessage }}
            </p>
          </div>
        </div>

        <!--footer -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 rounded-b-lg flex justify-end gap-3">
          <button
            @click="closeModal"
            class="px-4 py-2 bg-white text-pmf-primary rounded-lg border border-pmf-primary/40 hover:bg-pmf-secondary transition-colors">
            Tancar
          </button>
          <button
            @click="submitReschedule"
            :disabled="isSaving || !selectedDateTime"
            class="px-4 py-2 bg-pmf-primary text-white rounded-lg hover:bg-pmf-green transition-colors disabled:cursor-not-allowed disabled:opacity-60"
          >
            {{ isSaving ? 'Canviant...' : 'Canviar data' }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>