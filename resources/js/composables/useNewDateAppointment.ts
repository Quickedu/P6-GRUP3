import { computed, ref } from 'vue';
import type { ComputedRef, Ref } from 'vue';
import { ajaxDoctor, ajaxPatient, ajaxTest } from '@/actions/App/Http/Controllers/Workers/Secretary/DatesController';

export type UseNewDateAppointmentReturn = {
    dataCita: Ref<string>;
    professionalId: Ref<string>;
    patientId: Ref<number | null>;
    selectedTestId: Ref<number | null>;
    isAvaible: Ref<boolean>;
    cip: Ref<string>;
    confirmedPatient: Ref<string>;
    testMinutes: Ref<number | null>;
    timeValidationMessage: Ref<string>;
    estimatedMinutes: Ref<number | null>;
    validatedClass: Ref<string>;
    slotsMessage: Ref<string>;
    startTimeOptions: Ref<string[]>;
    selectedStartTime: Ref<string>;
    extraTime: Ref<number>;
    selectedDateTime: ComputedRef<string>;
    validatePatient: () => Promise<void>;
    validateTimeTest: (testId: number) => Promise<void>;
    validateDoctorSlots: () => Promise<void>;
};

export const useNewDateAppointment = (): UseNewDateAppointmentReturn => {
    const dataCita = ref('');
    const professionalId = ref('');
    const patientId = ref<number | null>(null);
    const selectedTestId = ref<number | null>(null);
    const isAvaible = ref(false);
    const cip = ref('');
    const patientAvailable = ref(true);
    const confirmedPatient = ref('');
    const testMinutes = ref<number | null>(null);
    const timeValidationMessage = ref('');
    const estimatedMinutes = ref<number | null>(null);
    const validatedClass = ref(
        'border-gray-200 focus:border-gray-900 focus:ring-gray-900',
    );
    const availableSlots = ref<string[]>([]);
    const slotsLoading = ref(false);
    const slotsMessage = ref('');
    const startTimeOptions = ref<string[]>([]);
    const selectedStartTime = ref('');
    const requiredSlotMinutes = ref<number | null>(null);
    const extraTime = ref(0);

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

    const validatePatient = async (): Promise<void> => {
        const currentCip = cip.value.trim();

        if (!currentCip) {
            confirmedPatient.value = '';
            patientId.value = null;
            extraTime.value = 0;
            estimatedMinutes.value = testMinutes.value;
            resetSlotSelection();
            slotsMessage.value = '';
            validatedClass.value =
                'border-gray-200 focus:border-gray-900 focus:ring-gray-900';

            return;
        }

        const response = await fetch(ajaxPatient.url(currentCip));
        const data = await response.json();

        patientAvailable.value = Boolean(data.available);
        validatedClass.value = patientAvailable.value
            ? '!border-green-500 !focus:border-green-500 !focus:ring-green-500'
            : '!border-red-500 !focus:border-red-500 !focus:ring-red-500';

        patientId.value = patientAvailable.value
            ? (data?.data?.id ?? null) 
            : null;
        confirmedPatient.value = patientAvailable.value ? currentCip : '';
        extraTime.value = Number(data?.data?.number) || 0;
        estimatedMinutes.value =
            testMinutes.value !== null
                ? testMinutes.value + extraTime.value
                : null;
        resetSlotSelection();
        slotsMessage.value = '';
        isAvaible.value = true;
    };

    const validateTimeTest = async (testId: number): Promise<void> => {
        selectedTestId.value = testId;

        try {
            const response = await fetch(ajaxTest.url(testId));
            const data = await response.json();
            const status = data?.status;
            const message = data?.message;
            const timeTest = data?.data?.number;

            if (status !== 'success') {
                testMinutes.value = null;
                estimatedMinutes.value = null;
                resetSlotSelection();
                slotsMessage.value = '';
                timeValidationMessage.value =
                    message || "No s'ha pogut validar el temps de la prova.";

                return;
            }

            const testDuration = Number(timeTest) || 0;

            testMinutes.value = testDuration;
            estimatedMinutes.value = testDuration + extraTime.value;
            resetSlotSelection();
            slotsMessage.value = '';
            timeValidationMessage.value =
                message || `Temps estimat ${estimatedMinutes.value} min`;
        } catch {
            testMinutes.value = null;
            estimatedMinutes.value = null;
            resetSlotSelection();
            slotsMessage.value = '';
            timeValidationMessage.value =
                'Error de connexió validant el temps de la prova.';
        }
    };

    const validateDoctorSlots = async (): Promise<void> => {
        if (!professionalId.value || !dataCita.value || estimatedMinutes.value === null) {
            resetSlotSelection();
            slotsMessage.value =
                'Selecciona data i prova abans d\'escollir el doctor per veure franges.';

            return;
        }

        slotsLoading.value = true;
        resetSlotSelection();
        slotsMessage.value = '';

        try {
            const response = await fetch(ajaxDoctor.url(professionalId.value, {
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
            selectedStartTime.value = startTimeOptions.value[0] || '';

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
    };

    return {
        dataCita,
        professionalId,
        patientId,
        selectedTestId,
        isAvaible,
        cip,
        confirmedPatient,
        testMinutes,
        timeValidationMessage,
        estimatedMinutes,
        validatedClass,
        slotsMessage,
        startTimeOptions,
        selectedStartTime,
        extraTime,
        selectedDateTime,
        validatePatient,
        validateTimeTest,
        validateDoctorSlots,
    };
};
