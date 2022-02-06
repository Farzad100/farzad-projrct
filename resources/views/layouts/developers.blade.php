@php(config('app.env') == 'production' ? $dev=0 : $dev=1)
<!doctype html>
<html lang="fa" dir="rtl">

<head>
  @if($dev == 0)
  @if(isset($tag_alt) && $tag_alt)
  @include('addons.googletag_script_landing')
  @else
  @include('addons.googletag_script')
  @endif
  @endif
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#00579F">
  <meta name="app_version" content="{{$version}}">
  <link rel="icon" type="image/png" href="/images/favicon.ico">

  @stack('header')

  <!-- CSS only -->
  @stack('style')
  <link rel="stylesheet" href="/libs/bootstrap.min.css">
  <link rel="stylesheet" href="/css/developers.css?ver={{$version}}">
  <link rel="stylesheet"
  href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.2/styles/default.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.2/styles/stackoverflow-dark.min.css" integrity="sha512-9F4w40pQJloG92QgszKGVFODKAkZ70xtcDe2IPcVELXMmkxkojfL6jcP6XSf1NTf4yVc3K0T9h/0gPp8bVIn6w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="/libs/feathericons.js"></script>
</head>

<body>
  @if($dev == 0)
  @if(isset($tag_alt) && $tag_alt)
  @include('addons.googletag_noscript_landing')
  @else
  @include('addons.googletag_noscript')
  @endif
  @endif
  <div id="app">
    <nav class="navbar navbar-expand-lg navbar-dark bg-black">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
          <img height="45" src="/images/logo-w.svg" alt="brand">
          <h4 class="special-font border-right mr-4 pr-4 mb-0">
            توسعه‌دهندگان
          </h4>
        </a>
  
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
  
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link" href="/">
                صفحه اصلی
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/plugins">
                پلاگین‌ها
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/developers">
                مستندات
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  
    <div class="developers-wrapper">
      <div class="container py-5">
        <div class="row">
          <div class="col-12 col-lg-3">
            <div class="p-3 bg-white border shadow-sm rounded">
              <ul class="list-unstyled mb-0 pr-0">
                <li>
                  <a
                    href="#"
                    class="btn btn-toggle align-items-center justify-content-start w-100"
                  >
                    شروع
                  </a>
                </li>
                <li>
                  <a
                    href="#"
                    class="btn btn-toggle align-items-center justify-content-start w-100"
                  >
                    نصب
                  </a>
                </li>
                <li class="mb-1">
                  <button
                    class="
                    btn btn-toggle align-items-center
                    justify-content-between rounded collapsed w-100
                    "
                    data-bs-toggle="collapse"
                    data-bs-target="#dashboard-collapse1"
                    aria-expanded="false"
                  >
                    نحوه کار با API
                    <i class="far fa-chevron-down"></i>
                  </button>
                  <div class="collapse" id="dashboard-collapse1">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 pr-4 small">
                      <li><a href="#" class="link-dark rounded w-100">آدرس‌ها</a></li>
                      <li><a href="#" class="link-dark rounded w-100">کار با propsها</a></li>
                      <li><a href="#" class="link-dark rounded w-100">مدیریت خطاها</a></li>
                    </ul>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <div class="col-12 col-lg-9">
            @yield('content')
          </div>
        </div>
      </div>
    </div>
  
    <footer class="developers">
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
  </div>

  <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.2/highlight.min.js"></script>
  <script src="/libs/popper.min.js"></script>
  <script src="/libs/bootstrap.min.js"></script>
  <script src="/js/app.min.js?ver={{$version}}"></script>
  <script>
    feather.replace();
    hljs.highlightAll();
  </script>
</body>

</html>
