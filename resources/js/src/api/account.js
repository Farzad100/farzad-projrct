import http from './config';

const url = '/profile';

const account = {

  get: {
    info: () => http.get(`${url}/info`),
    addresses: () => http.get(`${url}/addresses`),
    formsStatus: () => http.get(`${url}/forms-status`),

  },

  save: {
    info: (payload) => http.post(`${url}/info/edit`, { ...payload }),
    regEssentials: (payload) => http.post(`${url}/info/essentials`, { ...payload }),
    addresses: (payload) => http.post(`${url}/addresses/edit`, { ...payload }),
  }

};

export default account;
