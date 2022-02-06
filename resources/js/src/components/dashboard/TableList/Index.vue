<template>
  <div>
    <!-- Filters -->
    <div
      v-if="$slots.filter"
      class="d-flex aic justify-content-between mt-4 mt-md-0"
    >
      <slot name="filter" />
    </div>

    <div
      :class="[
        'long-list shadow-sm',
        { minimal: minimal },
        !minimal ? 'mt-4' : null
      ]"
    >
      <!-- Titles -->
      <div
        v-if="!minimal && listName"
        class="d-flex justify-content-between align-items-center p-sm-4"
      >
        <div class="d-flex align-items-center">
          <h4 class="special-font text-brand m-0">
            {{ listName }}
          </h4>
          <span
            v-if="pagination"
            class="pr-2 text-brand estedad-font opa-7"
          >
            (
            {{ pagination.total }}
            )
          </span>
        </div>

        <div class="d-flex align-items-center">
          <router-link
            v-if="route && tableList.length"
            class="btn btn-light btn-sm rounded-pill"
            :to="route"
          >
            مشاهده همه
            {{ listName }}
          </router-link>

          <button
            v-if="addButton"
            class="btn btn-primary btn-sm rounded-pill"
            @click="add"
          >
            <i class="far fa-plus ml-2" />
            {{ addButton }}
            جدید
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div
        v-if="loading"
        class="page-loading"
      >
        <div class="spinner-border" />
      </div>

      <!-- Content -->
      <template v-else>
        <table
          v-if="tableList.length"
          class="table long-list-table"
        >
          <!-- Table Header -->
          <thead>
            <tr>
              <th
                v-for="(th, index) in renderedData.headList"
                :key="index"
                scope="col"
                :class="{nopointer : th.nosort}"                 
              >
                <button
                  class="d-flex align-items-center"
                  :class="{activeHead: selectedHead === th.title }"
                  @click="theadClicked(index, $event) , selectedHeadtitle(th.title)"
                >
                  {{ th.title }}
                  <div
                    v-if="th.title && !th.nosort"
                    class="sort-arrows"
                  >
                    <i
                      v-show="renderedData.headList[index].sort === 'asc'"
                      :class="renderedData.headList[index].sort === 'asc' ? 'info' : 'noinfo'"
                      class="fal fa-caret-up mr-2"
                    />
                    <i
                      v-show="renderedData.headList[index].sort=== 'desc'"
                      :class="renderedData.headList[index].sort === 'desc' ? 'info' : 'noinfo'"
                      class="fal fa-caret-down mr-2"
                    />
                  </div>
                </button>
              </th>
            </tr>
          </thead>

          <!-- Table Data -->
          <tbody>
            <!--
            - Loop throgh tableList array and
            - define appearance of td based
            - addition value
            -->
            <tr
              v-for="tr in tableList"
              :key="tr.id"
            >
              <td
                v-for="(td, i) in tr.td"
                :key="i"
                :data-label="td.title"
                :class="[
                  role !== 'admin' && tr.seen ? 'opa-5' : null,
                  !td.addition && !td.value && !td.title && !td.type ? 'empty' : null
                ]"
              >
                <!-- Installments (Not in use !) -->
                <template v-if="td.type === 'instStatus'">
                  <inst-status :status="td.value" />
                </template>

                <!-- Action Button -->
                <template v-else-if="td.type === 'button'">
                  <button
                    :class="[
                      'd-inline-flex btn btn-sm rounded-pill mx-1',
                      td.buttons[0].btn_color
                        ? `btn-${td.buttons[0].btn_color}`
                        : 'btn-light',
                      td.buttons[0].css_classes
                    ]"
                    @click="handleClick(td.buttons[0], $event)"
                  >
                    <div
                      class="spinner-border"
                      role="status"
                    >
                      <span class="sr-only">در حال بررسی</span>
                    </div>

                    <span class="btn-text d-flex aic w-100 jcb">
                      <i
                        v-if="td.buttons[0].icon"
                        :class="[
                          `far fa-${td.buttons[0].icon}`,
                          td.buttons[0].value ? 'pl-2' : 'py-1'
                        ]"
                      />
                      {{ td.buttons[0].value }}
                    </span>
                  </button>

                  <button
                    v-if="td.buttons.length === 2"
                    :class="[
                      'd-inline-flex btn btn-sm rounded-pill mx-1',
                      td.buttons[1].btn_color
                        ? `btn-${td.buttons[1].btn_color}`
                        : 'btn-light',
                      td.buttons[1].css_classes
                    ]"
                    @click="handleClick(td.buttons[1], $event)"
                  >
                    <div
                      class="spinner-border"
                      role="status"
                    >
                      <span class="sr-only">در حال بررسی</span>
                    </div>

                    <span class="btn-text d-flex aic w-100 jcb">
                      <i
                        v-if="td.buttons[1].icon"
                        :class="[
                          `far fa-${td.buttons[1].icon}`,
                          td.buttons[1].value ? 'pl-2' : 'py-1'
                        ]"
                      />
                      {{ td.buttons[1].value }}
                    </span>
                  </button>

                  <div
                    v-if="td.buttons.length >= 3"
                    class="btn-group"
                  >
                    <button
                      type="button"
                      class="btn btn-sm btn-light rounded-pill py-2 dropdown-toggle"
                      data-bs-toggle="dropdown"
                      data-display="static"
                      aria-expanded="false"
                    >
                      <i class="fas fa-ellipsis-v" />
                    </button>
                    <ul
                      class="dropdown-menu dropdown-menu-dark dropdown-menu-left"
                    >
                      <li
                        v-for="(btn, btnIndex) in td.buttons.slice(1)"
                        :key="btnIndex"
                      >
                        <button
                          class="text-right dropdown-item"
                          :class="btn.css_classes"
                          type="button"
                          @click="handleClick(btn, $event)"
                        >
                          <small>
                            <i
                              v-if="btn.icon"
                              :class="[
                                `far fa-${btn.icon}`,
                                btn.value ? 'pl-2' : null
                              ]"
                            />
                            {{ btn.value }}
                          </small>
                        </button>
                      </li>
                    </ul>
                  </div>
                </template>

                <!-- Date -->
                <template v-else-if="td.type === 'date'">
                  {{ td.value | jTime }}
                  <small class="d-block opa-5">
                    {{ td.value | jDate }}
                  </small>
                </template>

                <!-- Status -->
                <template v-else-if="td.type === 'status'">
                  <div>
                    <span
                      class="badge rounded-pill py-2 px-3"
                      :class="td.css_classes"
                    >
                      {{ td.value }}
                    </span>
                  </div>
                </template>

                <!-- Toman -->
                <template v-else-if="td.type === 'toman'">
                  <span class="price-style">
                    {{ Number(td.value) | moneySeperate }}
                    <small class="opa-5">تومان</small>
                  </span>
                </template>

                <!-- Countable -->
                <template v-else-if="td.type === 'countable'">
                  <span>
                    {{ Number(td.value) | moneySeperate }}
                  </span>
                </template>

                <!-- Other -->
                <template v-else>
                  <span
                    :data-bs-toggle="td.tooltip ? 'tooltip' : null"
                    data-placement="top"
                    :title="td.tooltip"
                    :class="['d-block', td.css_classes]"
                  >
                    <a
                      v-if="td.link"
                      :href="td.link"
                      class="d-block text-primary"
                      :class="td.tooltip ? 'has-tooltip' : null"
                      :target="td.target ? td.target : '_self'"
                    >
                      <i
                        v-if="td.icon"
                        :class="[`far fa-${td.icon}`, 'pl-2']"
                      />
                      {{ td.value }}
                      <i
                        v-if="td.is_verified"
                        class="fas fa-badge-check text-primary"
                      />
                      <i
                        v-if="td.badge"
                        :class="['fas', 'fa-badge', 'user-badge', td.badge]"
                      />
                    </a>

                    <span
                      v-else
                      :class="td.tooltip ? 'has-tooltip' : null"
                    >
                      <i
                        v-if="td.icon"
                        :class="[`far fa-${td.icon}`, 'pl-2']"
                      />
                      {{ td.value }}
                      <i
                        v-if="td.is_verified"
                        class="fas fa-badge-check text-primary"
                      />
                      <i
                        v-if="td.badge"
                        :class="['fas', 'fa-badge', 'user-badge', td.badge]"
                      />
                    </span>
                  </span>
                </template>

                <small
                  v-if="td.addition"
                  class="d-block font-weight-light opa-7 d-block mt-sm-1 pr-2 pr-sm-0"
                >
                  {{ td.addition }}
                </small>
              </td>
            </tr>
          </tbody>
        </table>

        <empty
          v-else
          class="mb-4"
          message="در حال حاضر اطلاعاتی برای این لیست وجود ندارد"
        />
      </template>

      <pagination
        v-if="pagination"
        :data="pagination"
        @onPageChange="handlePageChange"
      />
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
