<div class="calculator" v-if="canCalShow" id="cal">

  <div id="calcPosition">
    <div class="container">
      <div class="row">
        <div class="col-12 mb-4">
          <div class="sec-title text-center ">
            <h2 class="special-font mb-5 ">
              <span class="highlighted-text pb-3">محاسبه‌گر اقساط</span>
            </h2>

          </div>
        </div>
      </div>
    </div>

  </div>


  <div class="container infoCalc  ">


    <div class="row">
      <div class="col-12 col-md-12 col-lg-8 right-sec p-0">
        <div class="ranger">


          <div class="ranger-head p-2">
            <amount-ranger v-model="order.amount" landing-style min="1200000" :max="maxes.third" @mc="changeRefund" />
          </div>

          <div class="c">

            <div class="d-flex flex-column align-items-center justify-content-between">
              <div class="d-flex flex-column position-relative w-100">
                <span class=" alert-small" v-if="order.amount > maxes.first && order.amount <= maxes.second">
                  این مبلغ برای سفارش
                  <strong class="special-font rounded-pill">دوم</strong>
                  به بعد است
                </span>
                <span class=" alert-small" v-if="order.amount > maxes.second">
                  این مبلغ برای سفارش
                  <strong class="special-font rounded-pill">سوم</strong>
                  به بعد است
                </span>
              </div>

            </div>


          </div>

        </div>
      </div>

      <div class="col-12 col-md-12 col-lg-4 left-sec">

        <div class="infoCalc-tile d-flex align-items-center ">
          <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16.595 14.8486H27.2583" stroke="#8A90D2" stroke-width="2.66472" stroke-linecap="round"
              stroke-linejoin="round" />
            <path d="M16.595 21.9267H21.9266" stroke="#8A90D2" stroke-width="2.66472" stroke-linecap="round"
              stroke-linejoin="round" />
            <path d="M8.5979 7.70898H29.036C30.999 7.70898 32.5904 9.30036 32.5904 11.2634V29.0357" stroke="#8A90D2"
              stroke-width="2.66472" stroke-linecap="round" stroke-linejoin="round" />
            <path
              d="M13.9294 36.1445H34.3674C36.3305 36.1445 37.9219 34.5532 37.9219 32.5901V30.8129C37.9219 29.8313 37.1262 29.0357 36.1447 29.0357H18.3724C17.3909 29.0357 16.5952 29.8313 16.5952 30.8129V33.4787C16.5952 34.951 15.4017 36.1445 13.9294 36.1445V36.1445C12.4571 36.1445 11.2636 34.951 11.2636 33.4787V10.3748C11.2636 8.90252 10.07 7.70898 8.59772 7.70898V7.70898C7.12542 7.70898 5.93188 8.90252 5.93188 10.3748V14.8179C5.93188 15.7994 6.72757 16.5951 7.70911 16.5951H11.2636"
              stroke="#8A90D2" stroke-width="2.66472" stroke-linecap="round" stroke-linejoin="round" />
          </svg>

          <h3>نتیجه محاسبه</h3>
        </div>

        <div class="s-ca-parent first  mt-4 mb-2">
          <div class="s-ca d-flex">
            <span class="special-font ">کل بازپرداخت (تومان) </span>
            <h4 class="price-style">
              {{ ghestify(order, refund).total | moneySeperate }}
            </h4>

          </div>
        </div>

        <div class="s-ca-parent">
          <div class="s-ca d-flex">
            <span class="special-font">مجموع اقساط</span>
            <h4 class="price-style">
              {{ ghestify(order, refund).payback | moneySeperate }}
            </h4>

          </div>
        </div>

        <div class="s-ca-parent">
          <div class="s-ca d-flex">
            <span class="special-font">پیش‌پرداخت</span>
            <h4 class="price-style">
              {{ ghestify(order, refund).prepayment | moneySeperate }}
            </h4>

          </div>
        </div>

        <div class="s-ca-parent">
          <div class="s-ca d-flex">
            <span class="special-font">مبلغ هر
              {{ order.type != 'individual' ? 'قسط' : 'چک' }}
            </span>
            <h4 class="price-style">
              {{ ghestify(order, refund).ghest | moneySeperate }}
              
            </h4>

          </div>
          <small class="warning d-block text-center"
            v-if="ghestify(order, refund).ghest >= maxes.gFirst && ghestify(order, refund).ghest < maxes.gSecond">
            این مبلغ چک برای سفارش
            <strong class="special-font rounded-pill">دوم</strong>
            به بعد است
          </small>
          <small class=" warning d-block text-center" v-if="ghestify(order, refund).ghest >= maxes.gSecond">
            این مبلغ چک برای سفارش
            <strong class="special-font  rounded-pill">سوم</strong>
            به بعد است
          </small>
        </div>

        <div class="descript">
          <img src="/images/icons/Description-clac.svg" alt="#" title="#">
          <p class="text-justify">
            برای محاسبه مبلغ کل بازپرداخت، مبلغ پیش‌پرداخت را هم در نظر گرفته ایم و نیازی به جمع دوباره مقادیر نیست.
          </p>
        </div>

      </div>
    </div>






  </div>


</div>
<?php /**PATH G:\ghesta\ghesta-git\q3til2\resources\views/components/calculator.blade.php ENDPATH**/ ?>