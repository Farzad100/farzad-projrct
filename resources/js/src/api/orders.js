import http from './config';

const url = '/orders';

const orders = {
  check: {
    limit: () => http.get(`${url}/limits`),
    shopLimit: mobile => http.get(`/shop${url}/limits/${mobile ? mobile : ''}`),
    organLimit: code => http.get(`/organ${url}/limits/${code ? code : ''}`),
    preview: oid => http.get(`${url}/${oid}/cheques-preview`),
    code: payload => http.post(`${url}/check-organ/${payload}`),
    cardRecived: payload => http.post('cards/card-received', { ...payload }),
    discountCode: (oid, payload) =>
      http.post(`${url}/${oid}/payment/check-coupon`, { ...payload }),
    global: () => http.get('/globals')
  },

  bankList: {
    get: oid => http.get(`${url}/${oid}/accounts`),
    post: (oid, payload) => http.post(`${url}/${oid}/accounts`, { ...payload })
  },

  get: {
    all: queries => http.get(`${url}?${queries ? queries : ''}`),
    single: oid => http.get(`${url}/${oid}`),
    docs: oid => http.get(`${url}/${oid}/docs`),
    latest: () => http.get(`${url}/last`),
    recentGhests: () => http.get(`${url}/recent/ghests`),
    ghests: oid => http.get(`${url}/${oid}/ghests`),
    chequesPreview: oid => http.get(`${url}/${oid}/cheques-preview`),
    contract: oid => http.get(`${url}/${oid}/contract`),
    cardStick: (oid, a) => http.get(`${url}/${oid}/card-stick/${a}`),
    counts: () => http.get(`/admin${url}/counts`)
  },

  regEssentials: (payload, oid) =>
    http.post(`${url}/${oid}/user-info/essentials`, { ...payload }),

  add: (payload, role = '') => http.post(role + `${url}/create`, payload),
  addByShop: payload => http.post(`shop${url}/create`, payload),

  pay: (oid, payload) => http.post(`${url}/${oid}/payment/prepayment`, payload),

  payInquiryInvoice: (oid, payload) =>
    http.post(`${url}/${oid}/payment/inquiry`, payload),

  invoiceInquiryDiscountCode: (oid, payload) =>
    http.post(`${url}/${oid}/payment/check-coupon/inquiry`, { ...payload }),

  payExtra: (oid, payload) => http.post(`${url}/${oid}/payment/extra`, payload),

  cancel: (oid, payload) => http.post(`${url}/${oid}/cancel`, { ...payload }),

  submitDocs: (oid, endpoint) => http.post(`${url}/${oid}/${endpoint}`),

  submitCheques: (oid, payload) =>
    http.post(`${url}/${oid}/cheques`, { ...payload }),

  submitCheque: (oid, item, payload) =>
    http.post(`${url}/${oid}/cheques/${item}`, { ...payload })
};

export default orders;
