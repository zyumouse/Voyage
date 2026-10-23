document.addEventListener('DOMContentLoaded', () => {
    const placeholder = document.getElementById('shared-header');
    if (!placeholder) {
        return;
    }

    fetch('header.php', { credentials: 'include' })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Failed to load header');
            }
            return response.text();
        })
        .then((html) => {
            placeholder.innerHTML = html;
        })
        .catch((error) => {
            console.error(error);
        });
});
