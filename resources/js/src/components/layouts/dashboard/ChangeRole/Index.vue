<template>
  <div class="change-role-drp">
    <router-link to="/dashboard/user">
      <button 
        v-if="roles.includes('user')"
        :disabled="role === 'user'"
        class="text-brand"
        :class="'w-piece-'+roles.length"
        @click="changeRole('user')"
      >
        <i class="fad fa-user" />
        پنل کاربری
      </button>
    </router-link>

    <router-link to="/dashboard/eshop">
      <button
        v-if="roles.includes('eshop')"
        :disabled="role === 'eshop'"
        class="text-brand"
        :class="'w-piece-'+roles.length"
        @click="changeRole('eshop')"
      >
        <i class="fad fa-globe" />
        پنل فروشگاهی
        <br>
        <small>(آنلاین)</small>
      </button>
    </router-link>

    <router-link to="/dashboard/shop">
      <button
        v-if="roles.includes('shop')"
        :disabled="role === 'shop'"
        class="text-brand"
        :class="'w-piece-'+roles.length"
        @click="changeRole('shop')"
      >
        <i class="fad fa-store" />
        پنل فروشگاهی
        <br>
        <small>(فیزیکی)</small>
      </button>
    </router-link>

    <router-link to="/dashboard/organ">
      <button
        v-if="roles.includes('organ')"
        :disabled="role === 'organ'"
        class="text-brand"
        :class="'w-piece-'+roles.length"
        @click="changeRole('organ')"
      >
        <i class="fad fa-user-tie" />
        پنل سازمانی
      </button>
    </router-link>
    
    <router-link to="/dashboard/admin">
      <button
        v-if="roles.includes('admin')"
        :disabled="role === 'admin'"
        class="text-brand"
        :class="'w-piece-'+roles.length"
        @click="changeRole('admin')"
      >
        <i class="fad fa-user-crown" />
        ادمین
      </button>
    </router-link>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
