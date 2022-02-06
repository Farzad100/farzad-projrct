import http from './config';

const url = '/organ';

const account = {

  register: (payload) => http.post(`${url}/register`, { ...payload }),

  get: {
    installments: (queries) => http.get(`${url}/ghests?${ queries ? queries : null }`),
    installmentsCounts: () => http.get(`${url}/ghests/counts`),
    info: () => http.get(`${url}/info`),
    docs: () => http.get(`${url}/docs`),
    orderPending: () => http.get(`${url}/orders/pending`),
    orders: (queries) => http.get(`${url}/orders?${ queries ? queries : '' }`),
  }

};

export default account;
