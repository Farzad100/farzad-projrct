<?php (config('app.env') == 'production' ? $dev=0 : $dev=1); ?>
<!DOCTYPE html>
<html dir="rtl" lang="fa">

<head>
  <?php if($dev == 0): ?>
  <?php echo $__env->make('addons.googletag_script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php endif; ?>
  <meta charset="utf-8">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <meta name="_token" content="<?php echo e(csrf_token()); ?>">
  <meta name="app_version" content="<?php echo e($version); ?>">
  <meta name="robots" content="noindex, nofollow">
  <meta name="theme-color" content="#00579F">

  <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon">
  <link rel="icon" href="/images/favicon.ico" type="image/x-icon">

  <title>قسطا</title>

  <link rel="stylesheet" href="/libs/bootstrap.min.css"> 

</head>

<body>
  <?php if($dev == 0): ?> 
  <?php echo $__env->make('addons.googletag_noscript', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php endif; ?>
  <div id="sbhAlert" class="col-12 col-md-8 col-lg-6 col-xl-4 d-none align-items-center mx-auto text-center" style="height: 100vh">
    <div>
      <h2> مرورگر شما قدیمی و غیراستاندارد است و به دلیل مشکلات امنیتی شما قادر به استفاده از قسطا در این مرورگر نیستید</h2>
      <h5 class="mt-5">لطفا یکی از مرورگرهای زیر را دانلود و استفاده کنید</h5>
      <div class="d-flex justify-content-center mt-4"> <a href="https://www.google.com/chrome/" class="d-flex flex-column justify-content-center align-items-center" style="width: 100px"> <img height="40" src="/images/browsers/gc.svg" alt="Chrome"> Chrome </a> <a href="https://www.mozilla.org/en-US/firefox/new/" class="d-flex flex-column justify-content-center align-items-center" style="width: 100px"> <img height="40" src="/images/browsers/ff.svg" alt="Firefox"> Firefox </a> <a href="https://brave.com/" class="d-flex flex-column justify-content-center align-items-center" style="width: 100px"> <img height="40" src="/images/browsers/b.svg" alt="Brave"> Brave </a> <a href="https://www.opera.com/" class="d-flex flex-column justify-content-center align-items-center" style="width: 100px"> <img height="40" src="/images/browsers/o.svg" alt="Opera"> Opera </a></div>
    </div>
  </div>

  <div id="app">
    <router-view />
  </div>

  <script src="/libs/popper.min.js"></script>
  <script src="/libs/bootstrap.min.js"></script>
  <script src="/js/app.min.js?ver=<?php echo e($version); ?>"></script>
  <script src="/libs/brDetector.js"></script>
</body>

</html><?php /**PATH G:\ghesta\ghesta-git\q3til2\resources\views/spa.blade.php ENDPATH**/ ?>