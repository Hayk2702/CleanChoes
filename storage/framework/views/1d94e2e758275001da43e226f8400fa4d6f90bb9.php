<!DOCTYPE html>
<html>
<head>
    <title>New Contact Request</title>
</head>
<body>
<h2>New Contact Submission</h2>

<p><strong>Name:</strong> <?php echo e($data['name']); ?></p>

<?php if(!empty($data['email'])): ?>
    <p><strong>Email:</strong> <?php echo e($data['email']); ?></p>
<?php endif; ?>

<p><strong>Phone:</strong> <?php echo e($data['phone']); ?></p>

<?php if(!empty($data['comment'])): ?>
    <p><strong>Comment:</strong> <?php echo e($data['comment']); ?></p>
<?php endif; ?>

</body>
</html>
<?php /**PATH F:\Projects\CleanChoes\resources\views/emails/contact.blade.php ENDPATH**/ ?>