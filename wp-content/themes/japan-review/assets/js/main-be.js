document.addEventListener('DOMContentLoaded', function() {
    const searchForms = document.querySelectorAll('form.jr-searchbar');
    
    searchForms.forEach(function(searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="s"]');
            
            // Check if input is empty
            if (!searchInput || !searchInput.value.trim()) {
                e.preventDefault();
                
                // Check if error message already exists within this specific form
                let errorMessage = searchForm.querySelector('.search-error');
                
                if (!errorMessage) {
                    // Create and display error message only if it doesn't exist
                    errorMessage = document.createElement('span');
                    errorMessage.className = 'search-error';
                    errorMessage.textContent = 'Please enter a search keyword';
                    errorMessage.style.color = 'red';
                    errorMessage.style.fontSize = '17px';
                    errorMessage.style.display = 'block';
                    errorMessage.style.marginTop = '5px';
                    
                    searchForm.appendChild(errorMessage);
                }
                
                searchInput.focus();
            }
        });
    });
});