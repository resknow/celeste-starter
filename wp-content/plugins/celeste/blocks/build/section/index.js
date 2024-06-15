(()=>{var e,t={135:(e,t,a)=>{"use strict";const l=window.wp.blocks,o=window.wp.data,n=window.wp.element,r=window.wp.blockEditor,i=window.wp.components,c=window.wp.i18n;var s=a(184),p=a.n(s);const d=e=>p()("section",{"has-background-image":e.mediaId},{"has-collapsed-bottom-space":e.collapseBottomSpace},{"has-collapsed-top-space":e.collapseTopSpace}),m=JSON.parse('{"u2":"resknow/section"}');(0,l.registerBlockType)(m.u2,{edit:(0,o.withSelect)(((e,t)=>({media:t.attributes.mediaId?e("core").getMedia(t.attributes.mediaId):void 0})))((function(e){let{attributes:t,setAttributes:a,media:l}=e;const o=0!=t.mediaId,s={className:d(t)};o&&(s.style={backgroundImage:`url(${t.mediaUrl})`});const p=t.tagName,m=(0,r.useBlockProps)(s),u=(0,r.useInnerBlocksProps)({className:"section__content"}),h=e=>{a({mediaId:e.id,mediaUrl:e.url})},g={header:(0,c.__)("The <header> element should represent introductory content, typically a group of introductory or navigational aids."),main:(0,c.__)("The <main> element should be used for the primary content of your document only."),section:(0,c.__)("The <section> element should represent a standalone portion of the document that can't be better represented by another element."),article:(0,c.__)("The <article> element should represent a self-contained, syndicatable portion of the document."),aside:(0,c.__)("The <aside> element should represent a portion of a document whose content is only indirectly related to the document's main content."),footer:(0,c.__)("The <footer> element should represent a footer for its nearest sectioning element (e.g.: <section>, <article>, <main> etc.).")};return(0,n.createElement)(n.Fragment,null,(0,n.createElement)(r.InspectorControls,null,(0,n.createElement)(i.PanelBody,{title:"Behaviour",initialOpen:!1},(0,n.createElement)(i.ToggleControl,{label:"Collapse vertical space",checked:!0===t.collapseTopSpace&&!0===t.collapseBottomSpace,onChange:()=>{a({collapseTopSpace:!t.collapseTopSpace,collapseBottomSpace:!t.collapseBottomSpace})}}),(0,n.createElement)(i.ToggleControl,{label:"Collapse top space",checked:!0===t.collapseTopSpace,onChange:()=>{a({collapseTopSpace:!t.collapseTopSpace})}}),(0,n.createElement)(i.ToggleControl,{label:"Collapse bottom space",help:"If paired with another section, this will collapse the bottom space between the two sections. This is useful when you have 2 sections with the same background colour.",checked:!0===t.collapseBottomSpace,onChange:()=>{a({collapseBottomSpace:!t.collapseBottomSpace})}})),(0,n.createElement)(i.PanelBody,{title:"Background Image",initialOpen:!1},(0,n.createElement)("div",{className:"editor-post-featured-image"},(0,n.createElement)(r.MediaUploadCheck,null,(0,n.createElement)(r.MediaUpload,{onSelect:h,multiple:!1,render:e=>{let{open:a}=e;return(0,n.createElement)(i.Button,{className:0==t.mediaId?"editor-post-featured-image__toggle":"editor-post-featured-image__preview",onClick:a},0==t.mediaId&&"Select an image",null!=l&&(0,n.createElement)(i.ResponsiveWrapper,{naturalWidth:l.media_details.width,naturalHeight:l.media_details.height},(0,n.createElement)("img",{src:l.source_url})))}})),0!=t.mediaId&&(0,n.createElement)(n.Fragment,null,(0,n.createElement)(i.__experimentalSpacer,null),(0,n.createElement)(i.Flex,null,(0,n.createElement)(i.FlexItem,null,(0,n.createElement)(r.MediaUploadCheck,null,(0,n.createElement)(r.MediaUpload,{title:"Replace Image",value:t.mediaId,onSelect:h,allowedTypes:["image"],render:e=>{let{open:t}=e;return(0,n.createElement)(i.Button,{onClick:t,variant:"secondary",isLarge:!0},"Replace Image")}}))),(0,n.createElement)(i.FlexItem,null,(0,n.createElement)(r.MediaUploadCheck,null,(0,n.createElement)(i.Button,{onClick:()=>{a({mediaId:0,mediaUrl:""})},variant:"link",isDestructive:!0},"Remove Image")))))))),(0,n.createElement)(r.InspectorControls,{group:"advanced"},(0,n.createElement)(i.SelectControl,{__nextHasNoMarginBottom:!0,__next40pxDefaultSize:!0,label:(0,c.__)("HTML element"),options:[{label:(0,c.__)("Default (<div>)"),value:"div"},{label:"<header>",value:"header"},{label:"<main>",value:"main"},{label:"<section>",value:"section"},{label:"<article>",value:"article"},{label:"<aside>",value:"aside"},{label:"<footer>",value:"footer"}],value:t.tagName,onChange:e=>a({tagName:e}),help:g[t.tagName]})),(0,n.createElement)(p,m,(0,n.createElement)("div",u)))})),save:function(e){let{attributes:t}=e;const a=0!=t.mediaId,l={className:d(t)};a&&(l.style={backgroundImage:`url(${t.mediaUrl})`});const o=t.tagName,i=r.useBlockProps.save(l),c=r.useInnerBlocksProps.save({className:"section__content"});return(0,n.createElement)(o,i,(0,n.createElement)("div",c))},getEditWrapperProps:()=>({"data-align":"full"})})},184:(e,t)=>{var a;!function(){"use strict";var l={}.hasOwnProperty;function o(){for(var e=[],t=0;t<arguments.length;t++){var a=arguments[t];if(a){var n=typeof a;if("string"===n||"number"===n)e.push(a);else if(Array.isArray(a)){if(a.length){var r=o.apply(null,a);r&&e.push(r)}}else if("object"===n){if(a.toString!==Object.prototype.toString&&!a.toString.toString().includes("[native code]")){e.push(a.toString());continue}for(var i in a)l.call(a,i)&&a[i]&&e.push(i)}}}return e.join(" ")}e.exports?(o.default=o,e.exports=o):void 0===(a=function(){return o}.apply(t,[]))||(e.exports=a)}()}},a={};function l(e){var o=a[e];if(void 0!==o)return o.exports;var n=a[e]={exports:{}};return t[e](n,n.exports,l),n.exports}l.m=t,e=[],l.O=(t,a,o,n)=>{if(!a){var r=1/0;for(p=0;p<e.length;p++){a=e[p][0],o=e[p][1],n=e[p][2];for(var i=!0,c=0;c<a.length;c++)(!1&n||r>=n)&&Object.keys(l.O).every((e=>l.O[e](a[c])))?a.splice(c--,1):(i=!1,n<r&&(r=n));if(i){e.splice(p--,1);var s=o();void 0!==s&&(t=s)}}return t}n=n||0;for(var p=e.length;p>0&&e[p-1][2]>n;p--)e[p]=e[p-1];e[p]=[a,o,n]},l.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return l.d(t,{a:t}),t},l.d=(e,t)=>{for(var a in t)l.o(t,a)&&!l.o(e,a)&&Object.defineProperty(e,a,{enumerable:!0,get:t[a]})},l.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={581:0,41:0};l.O.j=t=>0===e[t];var t=(t,a)=>{var o,n,r=a[0],i=a[1],c=a[2],s=0;if(r.some((t=>0!==e[t]))){for(o in i)l.o(i,o)&&(l.m[o]=i[o]);if(c)var p=c(l)}for(t&&t(a);s<r.length;s++)n=r[s],l.o(e,n)&&e[n]&&e[n][0](),e[n]=0;return l.O(p)},a=self.webpackChunkresknow_blocks=self.webpackChunkresknow_blocks||[];a.forEach(t.bind(null,0)),a.push=t.bind(null,a.push.bind(a))})();var o=l.O(void 0,[41],(()=>l(135)));o=l.O(o)})();