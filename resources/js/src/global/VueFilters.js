import Vue from 'vue';
import m from 'moment-jalaali';
m.loadPersian({
  usePersianDigits: true,
  dialect: 'persian-modern'
});

import Num2persian from 'num2persian';

Vue.filter('numToPersian', (val) => {
  if (val !== 0 && !val) return '';
  return Num2persian(val);
});

Vue.filter('moneySeperate', (val) => {
  if (val !== 0 && !val) return '';
  return Number(val).toLocaleString();
});

Vue.filter('cardNumber', (val) => {
  if (val !== 0 && !val) return '';
  return val.replace(/(\d)(?=(\d{4})+(?!\d))/g, '$1-').replace(/(^\s+|\s+$)/, '');
  
});

Vue.filter('cardNumberDash', (val) => {
  if (val !== 0 && !val) return '';

  val.split('-').join(''); // Remove dash (-) if mistakenly entered.
  return val.match(/.{1,4}/g).join('—');
});

Vue.filter('jDate', (val) => {
  return m(val).format('LL');
});

Vue.filter('jTime', (val) => {
  return m(val).format('LT');
});

Vue.filter('jYear', (val) => {
  return m(val).format('jYYYY');
});