<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1 style="margin-left:50px">Mails</h1>
    <h2>Your order status has been changed!</h2>
            <ul style="list-style-type:none;">
           <li class="list-group-item disabled"><b>Order no:</b> <?php echo e($order_no); ?></li>
          <li class="list-group-item"><b>Status:</b> <?php echo e($status); ?></li>
      </ul>
</body>
</html><?php /**PATH F:\xampp7\htdocs\ERP\resources\views/emails/statusmail.blade.php ENDPATH**/ ?>