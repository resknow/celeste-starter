(()=>{document.addEventListener("alpine:init",()=>{Alpine.data("backToTop",()=>({isIntersecting:!1,init(){this.intersectionObserver=new IntersectionObserver(e=>this.processIntersectionEntries(e)),this.intersectionObserver.observe(this.$el)},processIntersectionEntries(e){e.forEach(t=>{this.isIntersecting=t.isIntersecting})}}))});})();
