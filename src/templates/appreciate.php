<?php echo $this->fetch('./partial/header.php'); ?>
<div class="flex flex-col h-screen justify-between">
    <div style="width: 650px;" class="max-w-full mb-auto mx-auto px-4 shrink-0">
        <div class="mx-auto mt-4 border-2 rounded-lg bg-white pb-4">
            <?php if($saved): ?>
                <img style="max-width: 200px" class="mx-auto mt-8" src="<?php echo $baseFolder; ?>images/arabica-452.png" alt="three hearts" />
            <?php else: ?>
                <img style="max-width: 200px" class="mx-auto mt-8" src="<?php echo $baseFolder; ?>images/bermuda-111.png" alt="broken heart" />
            <?php endif; ?>
            <h1 class="text-2xl font-bold text-center mt-4 px-6">
                <?php echo $saved ? 'Your appreciation has<br/> been saved!' : 'Oh no, something<br/> went wrong!'; ?>
            </h1>
            <p class="mt-4 mb-6 text-center px-6">
                <?php echo $saved ? 'Its unique, non-fungible number is <strong>'. $id .'</strong> and it is on its way through cyberspace into your partners heart.' : 'Please try again. Remember that you must use an allowed name and that appreciations can\'t be empty!'; ?>
            </p>
            <p class="text-center mb-4">
                <?php if($saved): ?>
                    <a href="<?php echo $baseFolder; ?>" class="form-input border px-4 py-2 rounded-lg border-gray-400">Send another</a>
                <?php else: ?>
                    <a href="#" onclick="window.history.go(-1); return false;" class="form-input border px-4 py-2 rounded-lg border-gray-400">Try again</a>
                <?php endif; ?>
            </p>
        </div>
    </div>
    <div class="mt-2 h-10 text-white text-center color-white pb-2 w-full">
        Illustration by <a href="https://icons8.com/illustrations/author/zD2oqC8lLBBA">Icons 8</a> from <a href="https://icons8.com/illustrations">Ouch!</a>
    </div>
</div>
<?php echo $this->fetch('./partial/footer.php'); ?>