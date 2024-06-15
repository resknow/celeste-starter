
document.addEventListener( 'alpine:init', () => {
    Alpine.data('backToTop', () => ({
        isIntersecting: false,
        init() {
            this.intersectionObserver = new IntersectionObserver(entries => this.processIntersectionEntries(entries))
            this.intersectionObserver.observe(this.$el);
        },
        processIntersectionEntries(entries) {
            entries.forEach(entry => {
                this.isIntersecting = entry.isIntersecting
            });
        }
    }))
} );