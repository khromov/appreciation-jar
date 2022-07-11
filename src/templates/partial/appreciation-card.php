<?php 
$escaper = new Laminas\Escaper\Escaper('utf-8');
?>
<div class="mx-auto mt-4 border-2 rounded-lg bg-white pb-4">
    <img style="max-width: 200px" class="mx-auto mt-8" src="<?php echo $baseFolder; ?>/images/arabica-452.png" alt="three hearts" />

    <h1 class="text-4xl font-bold text-center mt-4 px-6">
        <?php echo $escaper->escapeHtml($appreciation['text']); ?>
    </h1>
    <p class="mt-4 text-center text-lg px-6">
        <?php echo $escaper->escapeHtml($appreciation['author']); ?>
    </p>
    <p class="mt-4 mb-6 text-center px-6 italic">
        <?php echo $escaper->escapeHtml($appreciation['timeFormatted']); ?>
    </p>
</div>