window.likeQueue = new Queue(10, Infinity);

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
            
            // Increment the like count so that we maintain consistency on the current page load
            window.initialAppreciationLikeCount++;
        } catch(e) {
            console.error(e);
        }
    }

    if(likeHeartElement || likeCountElement) {
        window.jsConfettiInstance = new window.JSConfetti();

        likeHeartElement.addEventListener('click', () => likeQueue.add(addLike));
        likeCountElement.addEventListener('click', () => likeQueue.add(addLike));
    }
})();

// Function for SSE polling
window.startSSEReloadPolling = function () {
    // SSE worker
    var eventSource = new ReconnectingEventSource("/events");

    // Event when receiving a message from the server
    eventSource.onmessage = function(event) {
        try {  
            const pendingLikeEvents = window.likeQueue.getPendingLength();

            const latest = JSON.parse(event?.data)?.latest; 
            const likes = JSON.parse(event?.data)?.likes;

            // If the appreciation id is higher than currently displayed, or the like count is higher than displayed and we don't have any outstanding like promises, reload
            if(latest > window.initialAppreciationId || (pendingLikeEvents === 0 && likes > window.initialAppreciationLikeCount)) {
                location.reload();
            } else {
                console.log('Latest appreciation is still', window.initialAppreciationId, '=', latest, ', not reloading.');
                console.log('Current likes are', window.initialAppreciationLikeCount, '=', likes, ', ', pendingLikeEvents, 'messages in queue, not reloading.');
            }
        } catch(e) {
            console.error(e);
        }
    };
}