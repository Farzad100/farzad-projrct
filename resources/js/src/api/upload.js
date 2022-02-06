import http from './config';

const upload = {
  chunkConcat: (url, payload) => http.post(url, { ...payload }),
};

export default upload;
