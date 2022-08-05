<?php 
$escaper = new Laminas\Escaper\Escaper('utf-8');
echo $this->fetch('./partial/header.php'); 
?>
<div style="width: 650px;" class="max-w-full mb-auto mx-auto px-4 shrink-0">
    <?php 
        foreach($appreciations as $appreciation) {
            echo $this->fetch('./partial/appreciation-card.php', ['appreciation' => $appreciation, 'baseFolder' => $baseFolder]);
        }
    ?>
    <div class="text-center pt-2 pb-2">
        <a class="text-white" href="<?php echo $baseFolder ?>/latest">✨ View latest</a>
    </div>
</div>
<?php echo $this->fetch('./partial/footer.php'); ?>