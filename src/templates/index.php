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
            <div class="mx-auto mt-4 border-2 rounded-lg bg-white">
                <img style="max-width: 200px" class="mx-auto mt-8" src="images/woolly-jam.png" alt="jam jar" />
                <h1 class="text-2xl font-bold text-center mt-4">The appreciation jar</h1>
                <p class="mt-4 mb-4 text-center px-6">
                    Write an appreciation about<br/> your partner below.
                </p>
                <form action="/appreciate" method="post">
                    <p class="text-center mb-4">
                        <textarea id="appreciation" name="appreciation" rows="4" placeholder="I really appreciate <?php echo $adverbs[rand(0, count($adverbs)-1)]; ?> you..." class="px-4 w-11/12 mx-auto form-input py-3 rounded-xl border-gray-400"></textarea>
                    </p>
                    <p class="text-center mb-4">
                        <input type="submit" value="Send appreciation" class="form-input border px-4 py-2 rounded-lg border-gray-400">
                    </p>
                </form>
            </div>
        </div>
        <div class="mt-2 h-10 text-white text-center color-white pb-2 w-full">
            Illustration by <a href="https://icons8.com/illustrations/author/zD2oqC8lLBBA">Icons 8</a> from <a href="https://icons8.com/illustrations">Ouch!</a>
        </div>
    </div>
</body>
</html>