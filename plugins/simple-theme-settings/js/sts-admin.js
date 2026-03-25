document.addEventListener('DOMContentLoaded', function() {
    
    document.addEventListener('click', function(e) {
        
        // --- ADD ROW ---
        if (e.target && e.target.classList.contains('sts-add-row')) {
            e.preventDefault();
            
            const container = e.target.closest('.sts-repeater-container');
            const rowsWrapper = container.querySelector('.sts-repeater-rows');
            const templateElement = container.querySelector('.sts-repeater-template');
            
            if (!templateElement) return;

            // 1. Get the template string
            let html = templateElement.innerHTML;
            
            // 2. Replace the placeholder index 999 with a timestamp
            const newIndex = Date.now();
            html = html.replace(/999/g, newIndex);
            
            // 3. Convert string to DOM nodes
            const temp = document.createElement('div');
            temp.innerHTML = html.trim();
            const newRow = temp.firstElementChild;
            
            // 4. Append to the page
            rowsWrapper.appendChild(newRow);
        }

        // --- REMOVE ROW ---
        if (e.target && e.target.classList.contains('sts-remove-row')) {
            e.preventDefault();
            
            if (confirm('Er du sikker på du vil fjerne denne række?')) {
                const row = e.target.closest('.sts-repeater-row');
                if (row) row.remove();
            }
        }
    });
});