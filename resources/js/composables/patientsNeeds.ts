// import { computed, ref } from 'vue';
// import type { ComputedRef, Ref } from 'vue';
// import { ajaxDoctor, ajaxPatient, ajaxTest } from '@/actions/App/Http/Controllers/Workers/Secretary/DatesController';

// export type UseNewDateAppointmentReturn = {
//     dataCita: Ref<string>;
//     professionalId: Ref<string>;
//     patientId: Ref<number | null>;
//     selectedTestId: Ref<number | null>;
//     isAvaible: Ref<boolean>;
//     cip: Ref<string>;
//     confirmedPatient: Ref<string>;
//     testMinutes: Ref<number | null>;
//     timeValidationMessage: Ref<string>;
//     estimatedMinutes: Ref<number | null>;
//     validatedClass: Ref<string>;
//     slotsMessage: Ref<string>;
//     startTimeOptions: Ref<string[]>;
//     selectedStartTime: Ref<string>;
//     extraTime: Ref<number>;
//     selectedDateTime: ComputedRef<string>;
//     validatePatient: () => Promise<void>;
//     validateTimeTest: (testId: number) => Promise<void>;
//     validateDoctorSlots: () => Promise<void>;
// };

// export const useNewDateAppointment = (): UseNewDateAppointmentReturn => {
//     const dataCita = ref('');
//     const professionalId = ref('');
//     const patientId = ref<number | null>(null);
//     const selectedTestId = ref<number | null>(null);
//     const isAvaible = ref(false);
//     const cip = ref('');
//     const patientAvailable = ref(true);
//     const confirmedPatient = ref('');
//     const testMinutes = ref<number | null>(null);
//     const timeValidationMessage = ref('');
//     const estimatedMinutes = ref<number | null>(null);
//     const validatedClass = ref(
//         'border-gray-200 focus:border-gray-900 focus:ring-gray-900',
//     );
//     const availableSlots = ref<string[]>([]);
//     const slotsLoading = ref(false);
//     const slotsMessage = ref('');
//     const startTimeOptions = ref<string[]>([]);
//     const selectedStartTime = ref('');
//     const requiredSlotMinutes = ref<number | null>(null);
//     const extraTime = ref(0);

//     /**
//      * Load the needs assigned to the given patient from the API and
//      * populate `patientNeeds`.
//      *
//      * This composable variant mirrors the logic used in
//      * `resources/js/pages/components/PatientNeedsModal.vue`.
//      * Called when the modal opens or when the patient changes.
//      */
//     const loadPatientNeeds = async function loadPatientNeeds() {
//         if (!props.patient) return;

//         loadingNeeds.value = true;

//         try {
//             const response = await fetch(`/patients/${props.patient.id}/needs`);
//             const data = await response.json();

//             patientNeeds.value = data;

//         } catch {
//             console.error('Error carregant necessitats');
//         } finally {
//             loadingNeeds.value = false;
//         }
//     }

//     /**
//      * Assign a selected need to the current patient by POSTing to the
//      * `/patients/:id/needs` endpoint. On success the new need is pushed
//      * into `patientNeeds` and UI state is reset.
//      *
//      * Note: the same action exists in
//      * `resources/js/pages/components/PatientNeedsModal.vue` where it's
//      * bound to the "Assignar" button.
//      */
//     const assignNeed = async function assignNeed(): Promise<void> {
//         if (!selectedNeedId.value || !props.patient) return;

//         try {
//             const response = await fetch(`/patients/${props.patient.id}/needs`, {
//                 method: 'POST',
//                 body: JSON.stringify({
//                     need_id: selectedNeedId.value
//                 }),
//                 headers: {
//                     'Content-Type': 'application/json'
//                 }
//             });

//             const data = await response.json();

//             if (data.status !== 'success') {
//                 console.error(data.message);
//                 return;
//             }

//             patientNeeds.value.push(data.data);

//             selectedNeedId.value = null;
//             showAddForm.value = false;

//         } catch {
//             console.error('Error assignant necessitat');
//         }
//     }

//     /**
//      * Remove an assigned need from the patient by DELETEing
//      * `/patients/:id/needs/:needId`. On success the local list is updated
//      * to remove the need.
//      *
//      * Called from `PatientNeedsModal.vue` when the trash button is pressed
//      * for a specific need.
//      */
//     const removeNeed = async function removeNeed(needId: number): Promise<void> {
//         if (!props.patient) return;

//         try {
//             const response = await fetch(`/patients/${props.patient.id}/needs/${needId}`, {
//                 method: 'DELETE'
//             });

//             const data = await response.json();

//             if (data.status !== 'success') {
//                 console.error(data.message);
//                 return;
//             }

//             patientNeeds.value = patientNeeds.value.filter(n => n.id !== needId);

//         } catch {
//             console.error('Error eliminant necessitat');
//         }
//     }

// };
