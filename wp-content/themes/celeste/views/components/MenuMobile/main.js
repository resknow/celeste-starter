
document.addEventListener('alpine:init', () => {
    Alpine.data('menuMobile', () => ({
        activeSubMenu: null,

        openSubMenu(key) {
            this.activeSubMenu = key;
        }
    }));
});