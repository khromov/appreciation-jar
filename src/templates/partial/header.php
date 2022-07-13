<?php $config = \Khromov\AppreciationJar\Lib\Helpers::getConfig(); ?>
<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appreciation Jar</title>
  <?php if($config['development'] === 'true'): ?>
    <script src="https://cdn.tailwindcss.com?plugins=forms,aspect-ratio,line-clamp"></script>
    
    <!-- Uncompiled styles -->
    <link href="<?php echo $config['baseFolder']; ?>/styles.css" rel="stylesheet">
  <?php else: ?>
    <link href="<?php echo $config['baseFolder']; ?>/dist/output.css" rel="stylesheet">
  <?php endif; ?>
  <?php if($config['noindex'] === 'true'): ?>
    <meta name="robots" content="noindex">
  <?php endif; ?>
  <style>
    body {
        background-attachment: fixed;
    }
</style>
</head>
<body class="bg-gradient-to-b from-pink-500 via-red-500 to-yellow-500">
  <div class="flex flex-col h-screen justify-between">