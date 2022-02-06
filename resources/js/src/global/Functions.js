import Cookies from 'js-cookie';

export const addMsgId = (id) => {
  let msgIds = JSON.parse(Cookies.get('msg_ids')) | [];

  if (!msgIdExist(id)) {
    msgIds.push(id);
    Cookies.set('msg_ids', msgIds);
  }
};

export const msgIdExist = (id) => {
  let msgIds = JSON.parse(Cookies.get('msg_ids')) | [];
  return msgIds.find(msgId => msgId === id);
};


export const HasContain = (member, array) => {
  let length = array.length;
  for (let i = 0; i < length; i++) {
    if (array[i] === member) return true;
  }
  return false;
};

export const inArray = (member, array) => {
  let length = array.length;
  for (let i = 0; i < length; i++) {
    if (array[i] === member) return true;
  }
  return false;
};

export const toEnglishDigits = (value) => {
  var id = { '۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4', '۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9' };
  return value.replace(/[^0-9.]/g, function (w) {
    return id[w] || w;
  });
};

export const copyToClipboard = {
  textarea: '',
  setDataCondition: window.clipboardData && window.clipboardData.setData,
  execCommandCondition: document.queryCommandSupported && document.queryCommandSupported('copy'),

  createEl(value) {
    this.textarea = document.createElement('textarea');
    this.textarea.textContent = toEnglishDigits(value);
    this.textarea.style.position = 'fixed';
    document.body.appendChild(this.textarea);
    this.textarea.select();
  },

  setData(value) {
    return window.clipboardData.setData('Text', toEnglishDigits(value)); 
  },

  CopyWithExecCommand(value) {
    try {
      window.vm.$alerts.show({
        msg: `${value} کپی شد`,
        type: 'success',
        style: 'float'
      });
      return document.execCommand('copy'); 
    } catch (ex) {
      window.vm.$alerts.show({
        msg: `${value} کپی نشد`,
        type: 'danger',
        style: 'float'
      });
      return false;
    } finally {
      document.body.removeChild(this.textarea);
    }
  },

  copy(payload) {
    if (this.setDataCondition) {
      this.setData(payload);
    } else if (this.execCommandCondition) {
      this.createEl(payload);
      this.CopyWithExecCommand(payload);
    }
  }
};