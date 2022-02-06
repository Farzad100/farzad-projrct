@php(config('app.env') == 'production' ? $dev=0 : $dev=1)
<!doctype html>
<html lang="fa" dir="rtl">

<head>
  @if($dev == 0)
  @include('addons.googletag_script')
  @endif
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#00579F" >
  <meta name="app_version" content="100">
  <link rel="icon" 
      type="image/png" 
      href="/images/favicon.ico">
  
  @stack('header')

  <!-- CSS only -->
  <link rel="stylesheet" href="/libs/bootstrap.min.css">
  <link rel="stylesheet" href="/libs/swiper.min.css">
  <link rel="stylesheet" href="/css/style.css?ver={{$version}}">
  <script src="/libs/feathericons.js"></script>

</head>

<body>
  @if($dev == 0)
  @include('addons.googletag_noscript')
  @endif
  <div class="landing-style" id="app">

    <!-- Content -->
    @yield('content')
    <!-- / Content -->

    <!-- Footer -->
    <footer>
      <div class="container py-5">
        <div class="row">
          <div class="col-12 col-md-4">
            <img width="170" src="/images/logo.svg" alt="قسطا">
          </div>
          <div class="col-12 col-md-8">
            <div class="links mr-auto">
              <a target="_blank" href="https://www.instagram.com/ghesta_ir/">
                <i data-feather="instagram"></i>
              </a>
              <a target="_blank" href="https://www.linkedin.com/company/ghesta/">
                <i data-feather="linkedin"></i>
              </a>
              <a target="_blank" href="https://twitter.com/ghesta_ir">
                <i data-feather="twitter"></i>
              </a>
              <a target="_blank" href="https://t.me/ghesta_ir">
                <i data-feather="send"></i>
              </a>
              <a href="tel:02191070092">(021) 910 700 92</a>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <!-- / Footer -->
  </div>

  <script src="/libs/popper.min.js"></script>
  <script src="/libs/bootstrap.min.js"></script>
  <script src="/libs/swiper.min.js"></script>
  <script src="/js/app.min.js?ver={{$version}}"></script>
  <script>
    new Swiper('.swiper-container', {
      slidesPerView: 'auto',
      grabCursor: true,
      spaceBetween: 0,
      navigation: {
        nextEl: '.swiperButton-next',
        prevEl: '.swiperButton-prev',
      },
    });

    feather.replace();
    var nav = document.querySelector('nav');
    window.onscroll = function() {
      if (window.pageYOffset > 100) {
        nav.classList.add('bg-active')
      } else {
        nav.classList.remove('bg-active')
      }
    };

    
  </script>
  @stack('scripts')

  @if($dev == 0)
  @include('addons.append_body')
  @endif
</body>

</html>