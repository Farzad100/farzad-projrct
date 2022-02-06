<template>
  <div class="border rounded shadow-sm p-3">
    <h4 class="special-font text-brand">
      راهنما
    </h4>

    <div class="list-view">
      <div class="list-view-item">
        <a
          href="/faq"
          target="_blank"
          class=" d-flex aic jcb"
        >
          <div class="title">
            <i class="fal fa-question-circle fa-lg" />
            <span>سوالات متداول</span>
          </div>

          <i class="far fa-external-link fa-xs" />
        </a>
      </div>
      <div class="list-view-item">
        <a
          href="/help"
          target="_blank"
          class=" d-flex aic jcb"
        >
          <div class="title">
            <i class="fal fa-play fa-lg" />
            <span>آموزش ویدئویی</span>
          </div>

          <i class="far fa-external-link fa-xs" />
        </a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HelperBox'
};
</script>
