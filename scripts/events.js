document.addEventListener('DOMContentLoaded', function () {
    const registerButtons = document.querySelectorAll('.register-btn');

    registerButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Find the associated paragraph with the registered count
            const sideContainer = this.closest('.carousel-side') || this.closest('.card-body');
            if (sideContainer) {
                const regP = sideContainer.querySelector('p:last-of-type') || sideContainer.querySelector('p');

                // If there's multiple paragraphs, we want the one with "Registered:" text
                const allPs = sideContainer.querySelectorAll('p');
                let targetP = null;
                for (let p of allPs) {
                    if (p.textContent.includes('Registered:')) {
                        targetP = p;
                        break;
                    }
                }

                if (targetP) {
                    const currentText = targetP.textContent;
                    const match = currentText.match(/Registered:\s*(\d+)/);
                    if (match) {
                        let count = parseInt(match[1], 10);
                        count++;
                        targetP.textContent = `Registered: ${count} people`;

                        // Update button state visually
                        this.textContent = 'Registered';
                        this.style.backgroundColor = 'var(--burgundy)';
                        this.disabled = true;
                        this.style.cursor = 'default';
                        this.style.transform = 'none';
                        this.style.boxShadow = 'none';
                    }
                }
            }
        });
    });
});
