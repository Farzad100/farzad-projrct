<template>
  <div class="container-fluid">
    <div class="dashboard-topbar">
      <!-- Logo -->
      <router-link to="/dashboard">
        <img
          src="@/assets/img/logo-minimal.webp"
          alt="brand logo"
        >
      </router-link>

      <div class="d-flex aic">
        <router-link
          v-if="inbox >= 1"
          class="new-msg"
          :to="{ name: 'dash-user-inbox' }"
        >
          <i class="fad fa-bell ml-2" />
          <span class="d-none d-lg-block">
            شما
            {{ inbox | numToPersian }}
            پیام جدید دارید
          </span>
          <span class="d-lg-none">
            {{ inbox }}
          </span>
        </router-link>

        <!-- Role Controller -->
        <div
          v-if="roles.length > 1"
          class="change-role"
        >
          <g-button
            ref="changeRole"
            :text="
              role === 'user'
                ? 'پنل کاربری'
                : role === 'eshop'
                  ? 'پنل فروشگاهی آنلاین'
                  : role === 'shop'
                    ? 'پنل فروشگاهی فیزیکی'
                    : role === 'organ'
                      ? 'پنل سازمانی'
                      : 'ادمین'
            "
            :icon-left="roleDrp ? 'caret-up' : 'caret-down'"
            icon-left-space="pr-2"
            sm
            @click.native="toggleRoleDrp"
          />

          <!-- Role changer -->
          <change-role
            v-show="roleDrp"
            class="drp"
          />
        </div>

        <!-- Support Phone Number -->
        <div class="support">
          <span class="mr-2">
            (021)
            <strong>910</strong>
            <strong>700</strong>
            <strong>92</strong>
          </span>

          :پشتیبانی تلفنی
        </div>

        <!-- Logout Button -->
        <g-button
          text="خروج"
          icon-right="sign-out"
          icon-right-space="pl-2"
          class="text-danger"
          sm
          @click.native="handleLogout"
        />
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
