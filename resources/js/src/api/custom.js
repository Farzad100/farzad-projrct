import http from './config';


const custom = {

  get   : (url) => http.get( url ),
  post  : (url, payload, options) => http.post( url, { ...payload }, options ),

  custom: {
    get   : (url) => http.get( url ),
    post  : (url, payload, options) => http.post( url, payload, options ),
  }
  
};

export default custom;
