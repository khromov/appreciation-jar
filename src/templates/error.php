<?php
$escaper = new Laminas\Escaper\Escaper('utf-8');
echo $this->fetch('./partial/header.php'); 
?>
<div style="width: 650px;" class="max-w-full mb-auto mx-auto px-4 shrink-0">
    <div class="mx-auto mt-4 border-2 rounded-lg bg-white pb-4">
        <img style="max-width: 200px" class="mx-auto mt-8" src="<?php echo $baseFolder; ?>/images/flame-1698.png" alt="three hearts" />
        <h1 class="text-4xl font-bold text-center mt-4 px-6">Error</h1>
        <h1 class="text-2xl font-bold text-center mt-4 px-6">
            <?php echo $errorMessage ? $escaper->escapeHtml($errorMessage) : 'Unknown error'; ?>
        </h1>
    </div>
</div>
<?php echo $this->fetch('./partial/footer.php'); ?>