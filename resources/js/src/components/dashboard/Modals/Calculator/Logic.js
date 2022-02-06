import { api, wrapper } from '@/global/services';

export default {
  name: 'Inquiry',

  data() {
    return {
      modal: false,
      endpoint: '',

      dataBC: [],
      dataFaci: [],
    };
  },

  methods: {
    openModal(payload) {
      const { endpoint } = payload;
      this.endpoint = endpoint;

      this.modal = true;
    },

    async getBackChques(force) {
      this.$refs.bc.loading('start');
      
      const { data } = await wrapper(
        api.custom.get(`${this.endpoint}/back-cheques${force ? '/1' : ''}`),
      );
        
      this.$refs.bc.loading('end');
      if (data) {
        const { result } = data;
        this.dataBC = result;
      }
    },

    async getFacilities(force) {
      this.$refs.faci.loading('start');
      const { data } = await wrapper(
        api.custom.get(`${this.endpoint}/facilities${force ? '/1' : ''}`),
      );
      
      this.$refs.faci.loading('end');
      if (data) {
        const { result } = data;
        this.dataFaci = result;
      }
    },

    async getBank() {
      this.loadingBC = true;
      const { data } = await wrapper(
        api.custom.get(`${this.endpoint}/back-cheques`),
      );
      
      this.loadingBC = false;
      if (data) {
        const { result } = data;
        this.dataBC = result;
      }
    },

    async getBankIntegrated() {
      this.loadingBC = true;
      const { data } = await wrapper(
        api.custom.get(`${this.endpoint}/back-cheques`),
      );
      
      this.loadingBC = false;
      if (data) {
        const { result } = data;
        this.dataBC = result;
      }
    },

    async getNID() {
      this.loadingBC = true;
      const { data } = await wrapper(
        api.custom.get(`${this.endpoint}/back-cheques`),
      );
      
      this.loadingBC = false;
      if (data) {
        const { result } = data;
        this.dataBC = result;
      }
    },
  }
};
