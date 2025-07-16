/**
 * Frontend script for MTDEV Privacy Video Block.
 *
 * Handles the click event on the placeholder to load the video.
 */
document.addEventListener('DOMContentLoaded', function () {
    // Find all video placeholders on the page.
    const placeholders = document.querySelectorAll('.mtdev-pvb-placeholder');

    // Add a click event listener to each placeholder.
    placeholders.forEach(function (placeholder) {
        placeholder.addEventListener('click', function (event) {
            // Prevent any default action.
            event.preventDefault();

            // Get the original video source from the data-attribute.
            const videoSrc = this.dataset.videoSrc;

            // If there's no src, do nothing.
            if (!videoSrc) {
                return;
            }

            // Create a new iframe element.
            const iframe = document.createElement('iframe');

            // Set the attributes for the new iframe.
            iframe.setAttribute('src', videoSrc);
            iframe.setAttribute('frameborder', '0');
            iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
            iframe.setAttribute('allowfullscreen', '');
            iframe.setAttribute('width', '100%'); // Optional: for responsive behavior

            // Replace the placeholder div with the new iframe.
            this.replaceWith(iframe);
        });
    });
});