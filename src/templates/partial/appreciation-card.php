<?php 
$escaper = new Laminas\Escaper\Escaper('utf-8');
$latest = $latest ?? false;
$count = $appreciation['count'] ?? 0;
?>
<div class="mx-auto mt-4 border-2 rounded-lg bg-white pb-4">
    <div class="like mx-auto flex flex-row mt-2" style="max-width: 160px;">
        <?php if($latest === true): ?>
        <div id="like-heart" data-card-id="<?php echo $escaper->escapeHtmlAttr($appreciation['id']); ?>" class="like-wrapper">
            <div class="animation-wrapper">
                <div class="animation">
                </div>
            </div>
            <div class="heart-wrapper">
                <div class="heart">
                    <img src="<?php echo $baseFolder; ?>/images/arabica-red-heart.png" alt="heart" />
                </div>
            </div>
        </div>
        <div id="like-count" data-card-id="<?php echo $escaper->escapeHtmlAttr($appreciation['id']); ?>" class="like-count text-5xl mt-5 w-full text-center">
          <?php echo $escaper->escapeHtml($count); ?>
        </div>
        <?php else: ?>
            <img style="width: 50px; height: 44px;" class="mx-auto mt-8" src="<?php echo $baseFolder; ?>/images/arabica-red-heart.png" alt="heart" />
            <div class="like-count text-5xl mt-7 ml-6 w-full text-center">
                <?php echo $escaper->escapeHtml($count); ?>
            </div>
        <?php endif; ?>
    </div>

    <h1 class="text-4xl font-bold text-center mt-2 px-6">
        <?php echo $escaper->escapeHtml($appreciation['text']); ?>
    </h1>
    <p class="mt-4 text-center text-2xl px-6">
        <?php echo $escaper->escapeHtml($appreciation['author']); ?>
    </p>
    <p class="mt-4 mb-2 text-center px-6 italic">
        <?php echo $escaper->escapeHtml($appreciation['timeFormatted']); ?>
    </p>
</div>