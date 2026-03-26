// Theme Logic for Villas del Sol
console.log("Villas del Sol Theme Loaded");

document.addEventListener('DOMContentLoaded', () => {
    // Header Effect on Scroll
    const header = document.querySelector('header');
    if(header) {
        window.addEventListener('scroll', () => {
            if(window.scrollY > 50) {
                header.classList.add('py-2', 'shadow-xl');
                header.classList.remove('py-4', 'shadow-md');
            } else {
                header.classList.add('py-4', 'shadow-md');
                header.classList.remove('py-2', 'shadow-xl');
            }
        });
    }
});
