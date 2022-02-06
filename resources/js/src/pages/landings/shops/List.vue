<template>
  <div class="shops-list">
    <header class="header">
      <div class="search">
        <form @submit.prevent="">
          <input
            type="text"
            placeholder="جستوی نام یا لینک فروشگاه..."
          >
          <button type="submit">
            <i class="fal fa-search" />
          </button>
        </form>

        <div class="fast-tags">
          <span class="pl-3 opa-5">
            پربازدید:
          </span>
          <a href="">
            موبایل
          </a>
          <a href="">
            لپتاپ
          </a>
          <a href="">
            لوازم منزل
          </a>
          <a href="">
            یخچال
          </a>
        </div>
      </div>
    </header>

    <div class="container mt-5">
      <div class="row">
        <div :class="['col-12', `col-lg-${isFilterShow ? '3' : '1'}`]">
          <button
            class="toggleFilter"
            @click.prevent="isFilterShow = !isFilterShow"
          >
            <i
              v-if="isFilterShow"
              class="fal fa-times fa-lg"
            />
            <i
              v-else
              class="fas fa-filter"
            />
            فیلتر
          </button>

          <div :class="['filter-sidebar', { 'open': isFilterShow }]">
            <input
              v-model="tagSearch"
              type="text"
              class="tags-search"
              placeholder="جستوی تگ‌ها..."
            >

            <vuescroll>
              <div class="tags">
                <check-box-list
                  v-model="selectedTags"
                  :tags="searchedTags"
                />
              </div>
            </vuescroll>

            <button class="btn btn-success w-100 rounded-pill mt-3">
              اعمال فیلتر
            </button>
          </div>
        </div>

        <div :class="['col-12', `col-lg-${isFilterShow ? '9' : '10'}`]">
          <div class="section-padding-top">
            <div class="row">
              <div class="col-12 col-lg-4 mb-4">
                <a
                  href=""
                  class="co-card"
                  target="_blank"
                >
                  <div class="_img">
                    <img
                      src="https://static-mh.content.disney.io/matterhorn/assets/goc/disney_logo_dark@2x-45d70f7dd57b.png"
                      alt=""
                    >
                  </div>
                  <h4 class="font-weight-bold special-font m-0">
                    دیزنی‌لند پاریس
                  </h4>

                  <span>
                    thewaltdisneycompany.com
                  </span>

                  <div class="_tags">
                    <a href="">
                      موبایل
                    </a>
                    <a href="">
                      سرگرمی
                    </a>
                    <a href="">
                      لوازم بازی
                    </a>
                  </div>
                </a>
              </div>
              <div class="col-12 col-lg-4 mb-4">
                <a
                  href=""
                  class="co-card"
                  target="_blank"
                >
                  <div class="_img">
                    <img
                      src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/Boomerang_tv_logo.png/758px-Boomerang_tv_logo.png"
                      alt=""
                    >
                  </div>

                  <h4 class="font-weight-bold special-font m-0">
                    بومرنگ
                  </h4>

                  <span>
                    bomerang.com
                  </span>

                  <div class="_tags">
                    <a href="">
                      موبایل
                    </a>
                    <a href="">
                      سرگرمی
                    </a>
                    <a href="">
                      لوازم بازی
                    </a>
                    <a href="">
                      سرگرمی
                    </a>
                  </div>
                </a>
              </div>
              <div class="col-12 col-lg-4 mb-4">
                <a
                  href=""
                  class="co-card"
                  target="_blank"
                >
                  <div class="_img">
                    <img
                      src="https://about.gitlab.com/images/devops-tools/digitalai-logo.png"
                      alt=""
                    >
                  </div>
                  
                  <h4 class="font-weight-bold special-font m-0">
                    دیجی طلا
                  </h4>

                  <span>
                    digitala.com
                  </span>

                  <div class="_tags">
                    <a href="">
                      موبایل
                    </a>
                    <a href="">
                      سرگرمی
                    </a>
                    <a href="">
                      لوازم بازی
                    </a>
                    <a href="">
                      سرگرمی
                    </a>
                    <a href="">
                      سرگرمی
                    </a>
                    <a href="">
                      سرگرمی
                    </a>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import vuescroll from 'vuescroll';

export default {
  name: 'ShopsList',

  components: { vuescroll },

  data() {
    return {
      isFilterShow: false,
      tagSearch: '',

      tags: [
        {
          value: 'mobile',
          text: 'موبایل'
        },
        {
          value: 'fraiger',
          text: 'یخچال'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
        {
          value: 'kichen',
          text: 'آشپزخانه'
        },
      ],

      selectedTags: []
    };
  },

  computed: {
    searchedTags() {
      return this.tags.filter(item => {
        return item.text.toUpperCase().indexOf(this.tagSearch) > -1;
      });
    }
  }
};
</script>