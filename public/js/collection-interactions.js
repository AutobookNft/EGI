/**
 * collection-interactions.js
 *
 * Script for handling collection-related interactions in the marketplace.
 * Implements like functionality, sorting, and UI enhancements.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize like buttons
    initLikeButtons();

    // Initialize filter form handling
    initFilterForm();

    // Initialize any masonry layout if needed
    initMasonryLayout();
});

/**
 * Initialize like button functionality for collections
 */
function initLikeButtons() {
    const likeButtons = document.querySelectorAll('.btn-like');

    likeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            // Get collection ID
            const collectionId = this.dataset.collectionId;

            // Check if user is authenticated
            if (!userIsAuthenticated()) {
                // Redirect to login or show login modal
                showLoginPrompt();
                return;
            }

            // Toggle visual state immediately for better UX
            this.classList.toggle('liked');

            // Get the current like count
            let likeCountElement = this.querySelector('.like-count');
            let currentCount = parseInt(likeCountElement.textContent);

            // Update count based on new state
            if (this.classList.contains('liked')) {
                likeCountElement.textContent = currentCount + 1;
            } else {
                likeCountElement.textContent = Math.max(0, currentCount - 1);
            }

            // Send the like/unlike request to the server
            fetch(`/collections/${collectionId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update count with actual value from server
                likeCountElement.textContent = data.likes_count;

                // Show success message if provided
                if (data.message) {
                    showNotification(data.message, 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);

                // Revert UI changes in case of error
                this.classList.toggle('liked');
                likeCountElement.textContent = currentCount;

                showNotification('Something went wrong. Please try again.', 'error');
            });
        });
    });
}
