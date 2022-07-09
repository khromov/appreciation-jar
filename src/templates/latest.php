<?php 
$escaper = new Laminas\Escaper\Escaper('utf-8');
echo $this->fetch('./partial/header.php'); 
?>
<div class="flex flex-col h-screen justify-between">
    <div style="width: 650px;" class="max-w-full mb-auto mx-auto px-4 shrink-0">
        <div class="mx-auto mt-4 border-2 rounded-lg bg-white pb-4">
            <img style="max-width: 200px" class="mx-auto mt-8" src="<?php echo $baseFolder; ?>/images/arabica-452.png" alt="three hearts" />

            <h1 class="text-2xl font-bold text-center mt-4 px-6">
                <?php echo $escaper->escapeHtml($appreciation['text']); ?>
            </h1>
            <p class="mt-4 text-center px-6">
                <?php echo $escaper->escapeHtml($appreciation['author']); ?>
            </p>
            <p class="mt-4 mb-6 text-center px-6 italic">
                <?php echo $escaper->escapeHtml($appreciation['timeFormatted']); ?>
            </p>
        </div>
    </div>
    <div class="mt-2 h-10 text-white text-center color-white pb-2 w-full">
        Illustration by <a href="https://icons8.com/illustrations/author/zD2oqC8lLBBA">Icons 8</a> from <a href="https://icons8.com/illustrations">Ouch!</a>
    </div>
</div>
<?php echo $this->fetch('./partial/footer.php'); ?>