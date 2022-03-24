<?php
    $adverbs = ['how', 'when', 'that'];
?>
<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com?plugins=forms,aspect-ratio,line-clamp"></script>
</head>
<style>
    body {
        background-attachment: fixed;
    }
</style>
<body class="bg-gradient-to-b from-pink-500 via-red-500 to-yellow-500">
    <div class="flex flex-col h-screen justify-between">
        <div style="width: 650px;" class="max-w-full mb-auto mx-auto px-4 shrink-0">
            <div class="mx-auto mt-4 border-2 rounded-lg bg-white pb-4">
                <?php if($saved): ?>
                    <img style="max-width: 200px" class="mx-auto mt-8" src="images/arabica-452.png" alt="three hearts" />
                <?php else: ?>
                    <img style="max-width: 200px" class="mx-auto mt-8" src="images/bermuda-111.png" alt="broken heart" />
                <?php endif; ?>
                <h1 class="text-2xl font-bold text-center mt-4 px-6">
                    <?php echo $saved ? 'Your appreciation has<br/> been saved!' : 'Oh no, something<br/> went wrong!'; ?>
                </h1>
                <p class="mt-4 mb-6 text-center px-6">
                    <?php echo $saved ? 'It\'s on its way through cyberspace into your partners heart.' : 'Please reload and try again.'; ?>
                </p>
                <p class="text-center mb-4">
                    <a href="<?php echo $baseFolder; ?>" class="form-input border px-4 py-2 rounded-lg border-gray-400"><?php echo $saved ? 'Send another' : 'Try again'; ?></a>
                </p>
            </div>
        </div>
        <div class="mt-2 h-10 text-white text-center color-white pb-2 w-full">
            Illustration by <a href="https://icons8.com/illustrations/author/zD2oqC8lLBBA">Icons 8</a> from <a href="https://icons8.com/illustrations">Ouch!</a>
        </div>
    </div>
</body>
</html>