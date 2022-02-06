<template>
  <div class="mt-4 form-builder">
    <ValidationObserver ref="observer">
      <div
        v-for="(section, sectionIndex) in formData"
        :key="sectionIndex"
        class="row _row"
      >
        <div class="col-12 col-lg-3">
          <h4 class="special-font text-brand opa-7 _section-title">
            {{ section.section }}
          </h4>
        </div>

        <div class="col-12 col-lg-9">
          <div class="row">
            <div
              v-for="(item, itemIndex) in section.fields"
              :key="itemIndex"
              :class="['col-12 mb-4', `col-md-${item.width}`]"
            >
              <ValidationProvider
                v-slot="{ errors }"
                :name="item.label"
                :rules="`${item.validation_rules ? item.validation_rules : ''}`"
              >
                <!-- Default -->
                <template v-if="item.type === 'text' && item.mode !== 'percent'">
                  <label :class="{'n-opt': item.required}">
                    {{ item.label }}
                  </label>
                  <small
                    v-if="item.description"
                    class="font-weight-light opa-5 d-block mb-3"
                  >
                    {{ item.description }}
                  </small>
                  <input
                    v-model="models[item.v_model]"
                    :class="[
                      'form-control form-control-lg',
                      item.mode === 'price' ? 'ltr estedad-font' : null
                    ]"
                    :disabled="item.disabled"
                    :minlength="item.minlength"
                    :maxlength="item.maxlength"
                  >
                  <small
                    v-if="item.mode === 'price'"
                    class="font-weight-light opa-5"
                  >
                    {{ models[item.v_model] | numToPersian }} تومان
                  </small>
                </template>




                <!-- Input with percent gp -->
                <template v-if="item.type === 'text' && item.mode === 'percent'">
                  <label :class="{'n-opt': item.required}">
                    {{ item.label }}
                  </label>
                  <small
                    v-if="item.description"
                    class="font-weight-light opa-5 d-block mb-3"
                  >
                    {{ item.description }}
                  </small>
                  <div class="input-group input-group-lg ltr normal-rounded">
                    <span
                      class="input-group-text"
                    >
                      %
                    </span>
                    <input
                      v-model="models[item.v_model]"
                      type="number"
                      min="0"
                      max="100"
                      class="form-control estedad-font"
                      placeholder="0.00"
                    >
                  </div>
                </template>




                <!-- Switch -->
                <template v-if="item.type === 'switch'">
                  <label
                    class="d-block"
                    :class="{'n-opt': item.required}"
                  >
                    {{ item.label }}
                  </label>
                  <small
                    v-if="item.description"
                    class="font-weight-light opa-5 d-block mb-3"
                  >
                    {{ item.description }}
                  </small>
                  <div class="btn-group w-100">
                    <input
                      id="inactive"
                      v-model="models[item.v_model]"
                      :value="0"
                      type="radio"
                      class="btn-check"
                      name="activity"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="inactive"
                    >
                      غیر فعال
                    </label>

                    <input
                      id="active"
                      v-model="models[item.v_model]"
                      :value="1"
                      type="radio"
                      class="btn-check"
                      name="activity"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="active"
                    >
                      فعال
                    </label>
                  </div>
                </template>




                <!-- Date -->
                <template v-if="item.type === 'date'">
                  <label :class="{'n-opt': item.required}">{{ item.label }}</label>
                  <small
                    v-if="item.description"
                    class="font-weight-light opa-5 d-block mb-3"
                  >
                    {{ item.description }}
                  </small>
                  <g-date-picker
                    v-model="models[item.v_model]"
                    empty
                    no-suggest
                    size="lg"
                  />
                </template>




                <!-- Textarea -->
                <template v-if="item.type === 'textarea'">
                  <label :class="{'n-opt': item.required}">
                    {{ item.label }}
                  </label>
                  <small
                    v-if="item.description"
                    class="font-weight-light opa-5 d-block mb-3"
                  >
                    {{ item.description }}
                  </small>
                  <textarea
                    v-model="models[item.v_model]"
                    class="form-control form-control-lg"
                    rows="5"
                  />
                </template>




                <!-- Select -->
                <template v-if="item.type === 'select'">
                  <label :class="{'n-opt': item.required}">
                    {{ item.label }}
                  </label>
                  <small
                    v-if="item.description"
                    class="font-weight-light opa-5 d-block mb-3"
                  >
                    {{ item.description }}
                  </small>
                  <select
                    v-model="models[item.v_model]"
                    class="form-select form-select-lg"
                    :disabled="item.disabled"
                  >
                    <option disabled>
                      انتخاب کنید
                    </option>
                    <option
                      v-for="(opt, index) in item.options"
                      :key="index"
                      :value="opt.value"
                    >
                      {{ opt.label }}
                    </option>
                  </select>
                </template>




                <!-- Radio -->
                <template v-if="item.type === 'radio'">
                  <label :class="{'n-opt': item.required}">
                    {{ item.label }}
                  </label>
                  <small
                    v-if="item.description"
                    class="font-weight-light opa-5 d-block mb-3"
                  >
                    {{ item.description }}
                  </small>
                  <div class="_form-builder-checks">
                    <div
                      v-for="(opt, index) in item.options"
                      :key="index"
                    >
                      <input
                        :id="`${item.v_model}-${index}`"
                        v-model="models[item.v_model]"
                        :value="opt.value"
                        type="radio"
                        :name="item.v_model"
                      >
                      <label
                        class="_check-itself _radio"
                        :for="`${item.v_model}-${index}`"
                      >
                        {{ opt.label }}
                      </label>
                    </div>
                  </div>
                </template>




                <!-- Checkbox -->
                <template v-if="item.type === 'checkbox'">
                  <label :class="{'n-opt': item.required}">
                    {{ item.label }}
                  </label>
                  <small
                    v-if="item.description"
                    class="font-weight-light opa-5 d-block mb-3"
                  >
                    {{ item.description }}
                  </small>

                  <div class="_form-builder-checks">
                    <div
                      v-for="(opt, index) in item.options"
                      :key="index"
                    >
                      <input
                        :id="`${item.v_model}-${index}`"
                        v-model="models[item.v_model]"
                        :value="opt.value"
                        type="checkbox"
                        :name="item.v_model"
                      >
                      <label
                        class="_check-itself _box"
                        :for="`${item.v_model}-${index}`"
                      >
                        {{ opt.label }}
                      </label>
                    </div>
                  </div>
                </template>




                <!-- Range -->
                <template v-if="item.type === 'range'">
                  <div class="d-flex aic mb-2">
                    <label
                      class="m-0"
                      :class="{'n-opt': item.required}"
                    >
                      {{ item.label }}
                    </label>
                    <h5
                      v-if="models[item.v_model]"
                      class="price-style text-green mr-2 font-weight-bold"
                    >
                      {{ models[item.v_model] | moneySeperate }}
                      <small class="opa-7">
                        {{ item.value_name }}
                      </small>
                    </h5>
                  </div>
                  <small
                    v-if="item.description"
                    class="font-weight-light opa-5 d-block mb-3"
                  >
                    {{ item.description }}
                  </small>
                  <input
                    v-model="models[item.v_model]"
                    type="range"
                    class="form-range"
                    :min="item.min"
                    :max="item.max"
                    :step="item.step"
                  >
                  <div class="_range-min-max">
                    <span class="price-style">
                      {{ item.min | moneySeperate }}
                      <small class="opa-5">
                        {{ item.value_name }}
                      </small>
                    </span>
                    <span class="price-style">
                      {{ item.max | moneySeperate }}
                      <small class="opa-5">
                        {{ item.value_name }}
                      </small>
                    </span>
                  </div>
                </template>


                <small class="text-danger d-block mt-1 font-weight-light">
                  {{ errors[0] }}
                </small>
              </ValidationProvider>
            </div>
          </div>
        </div>
      </div>
    </ValidationObserver>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>