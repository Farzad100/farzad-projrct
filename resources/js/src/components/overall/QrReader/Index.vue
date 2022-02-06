<template>
  <div class="QrScanner">
    <button
      class="close"
      @click="close"
    >
      <i class="fas fa-times" />
    </button>
    
    <div class="scanner-wrapper">
      <qrcode-stream
        :track="paintOutline"
        @decode="onDecode"
        @init="onInit"
      >
        <div
          v-if="error"
          class="error-wrapper"
        >
          <div class="msg special-font font-weight-bold">
            {{ error }}
          </div>
          <img
            src="/images/tv-noise-min.gif"
            alt="tv-noise"
          >
        </div>
        <div
          v-if="loading"
          class="loading-indicator"
        >
          <div class="spinner-border text-light" />
        </div>
      </qrcode-stream>
    </div>

    
    <svg
      class="camera-frame d-none"
      viewBox="0 0 254 254"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        d="M77 252H32.5C22.3333 251.333 2 244.3 2 221.5C2 198.7 2 181 2 175"
        stroke="red"
        stroke-width="1"
      />
      <path
        d="M177 252H221.5C231.667 251.333 252 244.3 252 221.5C252 198.7 252 181 252 175"
        stroke="red"
        stroke-width="1"
      />
      <path
        d="M77 2H32.5C22.3333 2.66667 2 9.7 2 32.5C2 55.3 2 73 2 79"
        stroke="red"
        stroke-width="1"
      />
      <path
        d="M177 2H221.5C231.667 2.66667 252 9.7 252 32.5C252 55.3 252 73 252 79"
        stroke="red"
        stroke-width="1"
      />
    </svg>
  </div>
</template>


<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
