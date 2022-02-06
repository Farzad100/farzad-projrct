<template>
  <div
    :class="[
      'amount-ranger',
      landingStyle && !simpleStyle ? 'landingStyle' : null,
      checkLimit
    ]"
  >
    <div :class="['amount', landingStyle ? 'd-none' : null]">
      <span ref="amount">
        {{ range.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,') }}
        <small>تومان</small>
      </span>
    </div>

    <input
      id="distance"
      ref="range"
      v-model="range"
      value="0"
      class="range-slider"
      type="range"
      :min="min"
      :max="max"
      :step="step ? step : 100000"
      list="tickmarks"
    >

    
    <output
      v-if="!hide.seke"
      for="distance"
      :value="range"
      class="range-slider-tooltip"
    > 
      <svg
        width="64"
        height="64"
        viewBox="0 0 64 64"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <rect
          width="64"
          height="64"
          rx="12"
          fill="#F2F3F6"
        />
        <rect
          x="24.645"
          y="20.7744"
          width="14.3226"
          height="4.64516"
          fill="#FFE89D"
        />
        <rect
          x="23.0967"
          y="29.29"
          width="14.3226"
          height="4.64516"
          fill="#FFE89D"
        />
        <rect
          x="17.2905"
          y="36.645"
          width="14.3226"
          height="4.64516"
          fill="#FFE89D"
        />
        <rect
          x="20"
          y="42.8389"
          width="12.3871"
          height="4.64516"
          fill="#FFE89D"
        />
        <circle
          cx="37.032"
          cy="43.9998"
          r="5.80645"
          fill="#FFE89D"
        />
        <path
          d="M20 34V26H36V34"
          stroke="#00569F"
          stroke-width="3"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
        <path
          d="M36 26H38V18H22V26"
          stroke="#00569F"
          stroke-width="3"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
        <path
          d="M43.6568 36.3431C46.781 39.4673 46.781 44.5327 43.6568 47.6568C40.5326 50.781 35.4673 50.781 32.3431 47.6568C29.2189 44.5326 29.2189 39.4673 32.3431 36.3431C35.4673 33.2189 40.5327 33.2189 43.6568 36.3431"
          stroke="#00569F"
          stroke-width="3"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
        <path
          d="M20 42V50H38"
          stroke="#00569F"
          stroke-width="3"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
        <path
          d="M38 34H18V42H30"
          stroke="#00569F"
          stroke-width="3"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
      </svg>

    </output>

    <datalist id="tickmarks">
      <option value="1200000" />
      <option value="10000000" />
      <option value="20000000" />
    </datalist>

    <div
      v-if="!hide.input"
      :class="['numParent', compressed ? 'compressed' : '']"
    >
      <!-- <label for="spinner"> تومان </label> -->
      <!-- <input
        id="spinner"
        v-model="range"
        :min="min"
        :max="max"
        :step="step ? step : 100000"
        type="number"
        pattern="[0-9]*"
      > -->
      <img
        src="/images/icons/vahed.png"
        alt="#"
        class="vahed"
      >
      <h4> {{ range | moneySeperate }} </h4>
      <div class="controlRange">
        <input
          id="spinner"
          v-model="range"
          :min="min"
          :max="max"
          :step="step ? step : 100000"
          type="number"
          pattern="[0-9]*"
        >
        <div class="UpDown d-flex flex-column">
          <img
            src="/images/icons/upRange.png"
            alt="#"
          >
          <img
            src="/images/icons/downRange.png"
            alt="#"
          >
        </div>
      </div>
    </div>



    <!-- :change="dataChange" -->
   
    <div class="parentPicker">
      <smooth-picker
        v-if="!hide.monthPicker"
        ref="smoothPicker"
        :data="data"
        :change="dataChange"
      />
    </div>


    

    <template v-if="!landingStyle || !simpleStyle">
      <span class="hints left">
        <small>
          <span>
            {{ (min || 1200000) | moneySeperate }}
          </span>
          <span>
            تومان
          </span>
        </small>
      </span>
      <span
        v-if="!hide.hintCenter"
        class="hints center"
      >
        <small>
          <span>
            {{ (max / 2 || 10000000) | moneySeperate }}
          </span>
          <span>
            تومان
          </span>
        </small>
      </span>

      <span class="hints right">
        <small>
          <span>
            {{ (max || 20000000) | moneySeperate }}
          </span>
          <span>
            تومان
          </span>
        </small>
      </span>
    </template>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
