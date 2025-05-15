document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.querySelector('.loading-overlay');
    
    // Show loading screen
    function showLoading() {
        loadingOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent scrolling while loading
    }

    // Hide loading screen
    function hideLoading() {
        loadingOverlay.style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling
    }

    // Handle all navigation link clicks
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && !link.hasAttribute('download') && !link.getAttribute('href').startsWith('#')) {
            showLoading();
        }
    });

    // Handle form submissions
    document.addEventListener('submit', function(e) {
        if (e.target.tagName === 'FORM') {
            showLoading();
        }
    });

    // Hide loading screen when page is fully loaded
    window.addEventListener('load', hideLoading);

    // Hide loading screen when navigating back/forward
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            hideLoading();
        }
    });

    // Add loading screen for AJAX requests
    let activeRequests = 0;
    
    const originalXHR = window.XMLHttpRequest;
    function newXHR() {
        const xhr = new originalXHR();
        xhr.addEventListener('loadstart', function() {
            activeRequests++;
            showLoading();
        });
        xhr.addEventListener('loadend', function() {
            activeRequests--;
            if (activeRequests === 0) {
                hideLoading();
            }
        });
        return xhr;
    }
    window.XMLHttpRequest = newXHR;

    // Handle fetch requests
    const originalFetch = window.fetch;
    window.fetch = function() {
        showLoading();
        return originalFetch.apply(this, arguments)
            .then(function(response) {
                hideLoading();
                return response;
            })
            .catch(function(error) {
                hideLoading();
                throw error;
            });
    };
}); 