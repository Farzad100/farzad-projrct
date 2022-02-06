<template>
  <div
    v-if="data.print_docs_note"
    class="my-5 pb-3"
  >
    <small class="font-weight-light opa-7">
      {{ data.print_docs_note.description }}
    </small>

    <div class="row text-center mt-4">
      <div
        v-for="(item, itemIndex) in data.print_docs_note.items"
        :key="itemIndex"
        class="col-6"
      >
        <div class="border rounded">
          <div class="p-3 bg-gray-light">
            <i :class="['fad', `fa-${item.icon}`, 'fa-2x']" />
            <strong class="d-block">
              <span class="opa-7">
                به صورت
              </span>
              {{ item.title }}
            </strong>
            <small class="font-weight-light opa-7">
              {{ item.description }}
            </small>
          </div>
          <div class="border-top d-flex justify-content-between">
            <span class="border-left p-2 w-50">
              <strong class="price-style">
                {{ item.cost }}
                <small class="opa-7">
                  تومان
                </small>
              </strong>
            </span>
            <span class="p-2 w-50">
              <strong class="price-style">
                {{ item.duration }}
                <small>
                  <span class="opa-7">
                    {{ item.type }}
                  </span>
                  <span class="opa-3">
                    —
                  </span>
                  <span class="text-green">
                    {{ item.state }}
                  </span>
                </small>
              </strong>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>