// Scroll-triggered entrance animations: elements with `.reveal-up` fade/slide
// in (via the `animate-fade-in-up` keyframe in app.css) once scrolled into view.
const revealObserver = new IntersectionObserver((entries, obs) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-fade-in-up');
            obs.unobserve(entry.target);
        }
    });
}, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

function observeRevealItems(root = document) {
    root.querySelectorAll('.reveal-up:not(.animate-fade-in-up)').forEach((el) => revealObserver.observe(el));
}

document.addEventListener('DOMContentLoaded', () => observeRevealItems());
document.addEventListener('livewire:navigated', () => observeRevealItems());

// Livewire re-renders (filtering, pagination, etc.) can add new `.reveal-up`
// elements after the initial load — pick those up too.
new MutationObserver((mutations) => {
    for (const mutation of mutations) {
        mutation.addedNodes.forEach((node) => {
            if (node.nodeType !== 1) return;
            if (node.matches?.('.reveal-up')) revealObserver.observe(node);
            observeRevealItems(node);
        });
    }
}).observe(document.documentElement, { childList: true, subtree: true });
