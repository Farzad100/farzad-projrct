import Vue from 'vue';

/**
 * Mask Inputs
 */
import VueTheMask from 'vue-the-mask';
Vue.use(VueTheMask);

/**
 * Validator
 */
import { ValidationProvider, ValidationObserver } from 'vee-validate';
Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);

/**
 * ApexChart
 */
import VueApexCharts from 'vue-apexcharts';
Vue.use(VueApexCharts);
Vue.component('apexchart', VueApexCharts);

/**
 * VueMeta
 */
import VueMeta from 'vue-meta';
Vue.use(VueMeta, {
  refreshOnceOnNavigation: true
});


import SmoothPicker from 'vue-smooth-picker';
Vue.use(SmoothPicker);





/**
 * Internal global
 * components
 */
Vue.component(
  'Register',
  require('@/pages/auth/Register').default
);

Vue.component(
  'Empty',
  require('@/components/overall/Empty/Index').default
);

Vue.component(
  'SelectableInputGroup',
  require('@/components/overall/SelectableInputGroup/Index').default
);

Vue.component(
  'SelectableInput',
  require('@/components/overall/SelectableInput/Index').default
);

Vue.component(
  'Uploader',
  require('@/components/overall/Uploader/Index').default
);

Vue.component(
  'AlertMessages',
  require('@/components/overall/AlertMessages/Index').default
);

Vue.component(
  'AmountRanger',
  require('@/components/dashboard/AmountRanger/Index').default
);

Vue.component(
  'Modal',
  require('@/components/overall/Modal/Index').default
);

Vue.component(
  'GButton',
  require('@/components/overall/TheButton/Index').default
);

Vue.component(
  'GLoading',
  require('@/components/overall/Loading/Index').default
);

Vue.component(
  'GMultiSelect',
  require('@/components/overall/MultiSelect/Index').default
);

Vue.component(
  'GNotes',
  require('@/components/dashboard/Notes/Index').default
);

Vue.component(
  'TableList',
  require('@/components/dashboard/TableList/Index').default
);

Vue.component(
  'FormBuilder',
  require('@/components/overall/FormBuilder/Index').default
);

Vue.component(
  'LoaderSm',
  require('@/components/overall/LoaderSm/Index.vue').default
);

Vue.component(
  'GChart',
  require('@/components/overall/Chart/Index.vue').default
);

Vue.component(
  'GDatePicker',
  require('@/components/overall/Datepicker/Index.vue').default
);

Vue.component(
  'GBankList',
  require('@/components/overall/BankList/Index.vue').default
);

Vue.component(
  'GDocs',
  require('@/components/dashboard/Docs').default
);
