// CSRF Token Helper for AJAX Requests
(function() {
    'use strict';
    
    // Get CSRF token from meta tag
    function getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
    
    // Add CSRF token to fetch requests
    const originalFetch = window.fetch;
    window.fetch = function(url, options = {}) {
        // Add CSRF token to POST, PUT, DELETE requests
        if (options.method && ['POST', 'PUT', 'DELETE'].includes(options.method.toUpperCase())) {
            options.headers = options.headers || {};
            options.headers['X-CSRF-TOKEN'] = getCSRFToken();
        }
        
        return originalFetch(url, options);
    };
    
    // Add CSRF token to form submissions
    document.addEventListener('submit', function(e) {
        const form = e.target;
        
        // Check if form already has CSRF token
        if (form.querySelector('input[name="csrf_token"]')) {
            return;
        }
        
        // Add CSRF token to form
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = getCSRFToken();
        form.appendChild(csrfInput);
    });
    
})();
