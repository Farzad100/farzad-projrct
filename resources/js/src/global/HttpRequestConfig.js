import httpClient from '../../axios';

// const END_POINT_PREFIX = '/api';

const get = (url, params = {}, configs = {}) => {
  let request;

  request = httpClient.get(url, {
    params: params,
    headers: { ...configs }
  });

  return request;
};

const post = (url, body = {}, configs = {}) => {
  let request;

  request = httpClient.post(url, body, {
    headers: { ...configs }
  });

  return request;
};

const put = (url, body = {}, configs = {}) => {
  let request;

  request = httpClient.put(url, body, {
    headers: { ...configs }
  });

  return request;
};

const del = (url, body = {}, configs = {}) => {
  let request;

  request = httpClient.delete(url, body, {
    headers: { ...configs }
  });

  return request;
};

export {
  get,
  post,
  put,
  del
};
