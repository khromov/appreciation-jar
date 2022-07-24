<?php 
$escaper = new Laminas\Escaper\Escaper('utf-8');
?>
<?php echo $this->fetch('./partial/header.php'); ?>
    <div style="width: 650px;" class="max-w-full mb-auto mx-auto px-4 shrink-0">
        <div class="mx-auto mt-4 border-2 rounded-lg bg-white pb-4">
            <h1 class="mt-4 text-3xl px-4">Appreciations</h1>
            <h5 class="mt-4 font-bold px-4">Currently published:  <?php echo $escaper->escapeHtml($currentlyPublished); ?></h5>
            <ul class="list-disc ml-4 px-4 mt-4 mb-2">
                <?php foreach($appreciations as $appreciation): ?>
                <li class="mb-2">
                    <strong><?php echo $escaper->escapeHtml($appreciation['id']); ?></strong> -
                    <?php if(intval($appreciation['id']) <= $currentlyPublished): ?>
                        <?php echo $escaper->escapeHtml($appreciation['text']); ?>
                    <?php else: ?>
                        <?php echo mb_substr($escaper->escapeHtml($appreciation['text']), 0, 10); ?>... 
                    <?php endif; ?>
                    <form action="<?php echo $baseFolder; ?>/admin/delete/<?php echo intval($appreciation['id']); ?>" method="post" class="inline">
                        <input type="hidden" name="secret" value="<?php echo $escaper->escapeHtmlAttr($secret); ?>" />
                        <input type="submit" value="Delete" class="form-input border px-1 py-1 rounded-lg border-gray-400" style="cursor: pointer;">
                    </form>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="pt-2 px-4 mt-4 mb-5">
                <form action="<?php echo $baseFolder; ?>/increment" method="post" class="inline">
                        <input type="submit" value="⏰ Trigger new appreciation" class="form-input border px-1 py-1 rounded-lg border-gray-400" style="cursor: pointer;">
                </form>
            </div>
            <div class="px-4">
                <a class="border px-2 py-2 rounded-lg border-gray-400" href="<?php echo $baseFolder ? $baseFolder : '/'; ?>">Back</a>
            </div>
        </div>
    </div>
<?php echo $this->fetch('./partial/footer.php'); ?>