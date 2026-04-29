<script setup lang="ts">
  import { Head, Link, usePage } from '@inertiajs/vue3'
  import DatesFilterComponent from './DatesFilterComponent.vue'

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

  defineProps({
    dates: {
      type: Array<ScheduledDate>,
      default: () => [],
    },
  });
</script>

<template>
  <div class="w-full">
    <DatesFilterComponent :initial-dates="dates" />
  </div>
</template>