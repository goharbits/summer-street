<div class="copyright-area">
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <ul class="links-list">
          <?php $__currentLoopData = $pages->where('position', 'copyright_area'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><a href="javascript:void(0)" target="_blank"><?php echo e($page->title, false); ?></a></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <li><a href="javascript:void(0)"><?php echo app('translator')->getFromJson('theme.nav.merchant_dashboard'); ?></a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <p class="copyright-text">© <?php echo e(date('Y'), false); ?> <a href="<?php echo e(url('/'), false); ?>"><?php echo e(get_platform_title(), false); ?></a></p>
      </div>
    </div>
  </div>
</div><!-- /.copyright-area -->
<?php /**PATH H:\xampp\htdocs\summerstreet\public\themes\default/views/nav/copyright.blade.php ENDPATH**/ ?>