document.addEventListener('DOMContentLoaded', () => {
    const placeholder = document.getElementById('shared-footer');
    if (!placeholder) {
        return;
    }

    fetch('footer.php')
        .then((response) => {
            if (!response.ok) {
                throw new Error('Failed to load footer');
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
