

<?php $__env->startSection('content'); ?>
    <main-component
            :locale="'<?php echo e(app()->getLocale()); ?>'"
            :authUser="<?php echo e(Auth::user()); ?>"
    />
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\Projects\CleanChoes\resources\views/home.blade.php ENDPATH**/ ?>