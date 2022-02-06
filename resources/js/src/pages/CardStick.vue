<template>
  <div class="page">
    <div>
      <span class="p-4">
        <span class="d-block mb-2">
          <h6>آدرس گیرنده:</h6>
          <strong>
            {{ data.name }}
          </strong>
          <small class="opa-8"> • کد ملی: {{ data.nid }} </small>
        </span>
        <small class="d-block mb-2 text-justify">
          آدرس: {{ data.address }}
        </small>
        <div class="d-flex">
          <small class="pl-4 estedad-font d-flex aic">
            <span class="opa-8">کدپستی:</span>
            {{ data.post }}
          </small>
          <small class="estedad-font d-flex aic">
            <span class="opa-8">موبایل:</span>
            {{ data.mobile }}
          </small>
        </div>
      </span>
    </div>
    <div class="pt-5">
      <span class="pt-5"> رمز کارت: {{ $route.params.password }} </span>
    </div>
  </div>
</template>

<script>
import Orders from '@/api/orders';

export default {
  name: 'CardStick',

  metaInfo: {
    title: 'چسب کارت'
  },

  data() {
    return {
      data: {}
    };
  },

  created() {
    this.loadData();
  },

  methods: {
    loadData() {
      Orders.get
        .cardStick(this.$route.params.id, this.$route.params.address)
        .then(r => {
          this.data = r.data.result;

          setTimeout(() => {
            window.print();
          }, 1000);
        });
    }
  }
};
</script>

<style scoped>
body {
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
}
.page {
  background: white;
  display: block;
  margin: 0 auto;
  margin-bottom: 0.5cm;
  width: 210mm;
  min-height: 297mm;
  max-height: 297mm;
  /* border: 1px solid red; */
  overflow: hidden;
  position: relative;
}
.page > div {
  width: 100%;
  height: calc(270mm / 3);
  /* border-bottom: 1px solid red; */
  display: flex;
  align-items: center;
}
.page > div > span {
  width: calc(105mm - 15mm);
  height: calc(99mm - 25mm);
  /* border: 1px solid blue; */
  margin: 0 calc(15mm / 2);
  display: block;
}
.page > div:first-child span {
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
}
.page > div:last-child {
  justify-content: flex-end;
}
.page > div:last-child span {
  height: 60mm;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}
@page {
  size: A4;
  margin: 0;
}
@media print {
  html,
  body {
    width: 210mm;
    height: 297mm;
    overflow: hidden;
  }
  .page {
    margin: 0;
    overflow: hidden;
    border: initial;
    border-radius: initial;
    width: initial;
    min-height: initial;
    max-height: 297mm;
    box-shadow: initial;
    background: initial;
    page-break-after: always;
  }
}
</style>
