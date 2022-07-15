(async function () {
    const likeHeartElement = document.getElementById('like-heart');
    const likeCountElement = document.getElementById('like-count');
    const appreciationId = parseInt(likeHeartElement.dataset.cardId);

    const addLike = async function() {
        let body = new FormData();
        body.append('appreciationId', appreciationId);

        try {
            // TODO: Needs support for baseFolder configs
            const response = await fetch('/api/like', {
                method: 'POST',
                mode: 'same-origin',
                cache: 'no-cache',
                body
            });
            const responseJson = await response.json();

            const newLikesCount = responseJson?.likes;

            likeCountElement.innerHTML = newLikesCount;

            window.jsConfettiInstance.addConfetti({
                emojis: ['❤️', '💙', '💖', '💘', '💗', '🌸'],
            });
            
        } catch(e) {
            console.error(e);
        }
    }

    if(likeHeartElement || likeCountElement) {
        window.jsConfettiInstance = new window.JSConfetti();

        likeHeartElement.addEventListener('click', addLike);
        likeCountElement.addEventListener('click', addLike);
    }
})();