<?php 
$escaper = new Laminas\Escaper\Escaper('utf-8');
echo $this->fetch('./partial/header.php'); 
?>
<div style="width: 650px;" class="max-w-full mx-auto px-4 grow">
    <div class="flex flex-col justify-center h-full"> <!-- style="width: 650px;"  -->
        <div>
            <?php echo $this->fetch('./partial/appreciation-card.php', ['appreciation' => $appreciation, 'baseFolder' => $baseFolder, 'latest' => $latest]); ?>
            <div class="text-center pt-2">
                <a class="text-white" href="<?php echo $baseFolder ?>/archive">🗓️ View all published</a>
                <a class="text-white" href="<?php echo $baseFolder ? $baseFolder : '/' ?>">🌱 Create new</a>
            </div>
        </div>
    </div>
</div>
<script>
    window.initialAppreciationId = <?php echo json_encode(intval($appreciation['id'])); ?>;
    window.initialAppreciationLikeCount = <?php echo json_encode(intval($appreciation['count'])); ?>;
    window.addEventListener('load', (event) => {
        window.startSSEReloadPolling();
    });
</script>
<?php echo $this->fetch('./partial/footer.php'); ?>