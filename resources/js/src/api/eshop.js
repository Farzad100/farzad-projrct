import http from './config';

const url = '/eshop';

const account = { 

  agree: () => http.post(`${url}/agree`), 

  get: {
    info: (just_mode = '') => http.get(`${url}/info/${just_mode}`),
    credits: (queries) => http.get(`${url}/orders?${ queries ? queries : '' }`),
  },

  orders: {
    all: (queries) => http.get(`${url}/orders?${ queries ? queries : '' }`),
    single: (role, oid) => http.get(`${role}/orders/${oid}`),
    docs: (oid, role) => http.get(`${role}/orders/${oid}/docs`),
    reject: (oid, payload) => http.post(`${url}/orders/${oid}/change/rejected`, { ...payload }),
    edit: (oid, payload) => http.post(`${url}/orders/${oid}/edit`, { ...payload }),
    changeStatus: (oid, status) => http.post(`${url}/orders/${oid}/change/${status}/silent`),
    sendMessage: (id, payload) => http.post(`${url}/orders/${id}/send-message`, { ...payload }),
  },

  notes: { 
    all: (oid, route) => http.get(`${url}/${route}/${oid}/notes`), 
    create: (payload) => http.post(`${url}/notes/create`, { ...payload }),
    edit: (id, payload) => http.post(`${url}/notes/${id}/edit`, { ...payload }),
    delete: (id) => http.post(`${url}/notes/${id}/delete`),
  },

  verifyDomain: () => http.post(`${url}/verify-domain`),
  verifyEmail: (payload) => http.post(`${url}/verify-email`, { ...payload }),
  sendEmailAgain: () => http.post(`${url}/send-email-again`),

};

export default account;
