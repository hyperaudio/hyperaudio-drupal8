!function(e){function t(o){if(r[o])return r[o].exports;var n=r[o]={i:o,l:!1,exports:{}};return e[o].call(n.exports,n,n.exports,t),n.l=!0,n.exports}var r={};t.m=e,t.c=r,t.d=function(e,r,o){t.o(e,r)||Object.defineProperty(e,r,{configurable:!1,enumerable:!0,get:o})},t.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(r,"a",r),r},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=3)}([function(e,t,r){"use strict";function o(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var n=function(){function e(e,t){var r=[],o=!0,n=!1,a=void 0;try{for(var i,u=e[Symbol.iterator]();!(o=(i=u.next()).done)&&(r.push(i.value),!t||r.length!==t);o=!0);}catch(e){n=!0,a=e}finally{try{!o&&u.return&&u.return()}finally{if(n)throw a}}return r}return function(t,r){if(Array.isArray(t))return t;if(Symbol.iterator in Object(t))return e(t,r);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}(),a=function(){function e(e,t){for(var r=0;r<t.length;r++){var o=t[r];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}return function(t,r,o){return r&&e(t.prototype,r),o&&e(t,o),t}}(),i=function(){function e(t){var r=this,n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:".hyperaudio-transcript, .hyperaudio-effect";o(this,e),this.itemSelector=n,this.root="string"==typeof t?document.querySelector(t)||document.createElement("section"):t,this.root.querySelector(this.itemSelector).parentNode.querySelectorAll(this.itemSelector).forEach(function(e){return r.setup(e)}),this.root.querySelector(this.itemSelector).parentNode.addEventListener("click",this.onClick.bind(this))}return a(e,[{key:"setup",value:function(e){var t=e.getAttribute("data-src"),r=e.getAttribute("data-type");t&&this.getMedia(t,r),e.querySelectorAll("*[data-m]").forEach(function(e){var t=e.getAttribute("data-m"),r=e.getAttribute("data-d"),o=[];t&&o.push(parseInt(t,10)/1e3),r&&o.push(parseInt(r,10)/1e3),e.setAttribute("data-t",o.join(",")),e.removeAttribute("data-m"),e.removeAttribute("data-d"),e.removeAttribute("class"),e.parentNode.removeAttribute("class")})}},{key:"onClick",value:function(e){var t=e.target.getAttribute("data-t");if(t){for(var r=e.target.parentNode;r&&!r.matches(this.itemSelector);)r=r.parentNode;if(r){var o=r.getAttribute("data-src");if(o){var a=this.getMedia(o);if(a){var i=t.split(","),u=n(i,2),c=u[0];u[1];a.currentTime=c,a.paused&&a.play()}}}}}},{key:"setHead",value:function(e,t){var r=this;this.currentSrc=t;for(var o=!1,a=this.root.querySelectorAll('.hyperaudio-transcript[data-src="'+t+'"]'),i=0;i<a.length;i++){(function(t){var i=a[t];i.querySelectorAll(".hyperaudio-past").forEach(function(e){return e.classList.remove("hyperaudio-past")}),i.querySelectorAll(".hyperaudio-active").forEach(function(e){return e.classList.remove("hyperaudio-active")});var u=i.querySelectorAll("*[data-t]"),c=u[0],s=u[u.length-1],d=c.getAttribute("data-t").split(","),l=n(d,1),f=l[0];if(e<parseFloat(f))return"continue";var p=s.getAttribute("data-t").split(","),h=n(p,2),y=h[0],v=h[1];if(e>parseFloat(y)+parseFloat(v))return"continue";o=!0,r.lastSegment=i;for(var b=0;b<u.length;b++){if("break"===function(t){var r=u[t].getAttribute("data-t"),o=r.split(","),a=n(o,2),i=a[0],c=a[1];if(i=parseFloat(i),c=parseFloat(c),i<e&&u[t].classList.add("hyperaudio-past"),i<=e&&e<i+c&&(u[t].classList.add("hyperaudio-active"),u[t].classList.contains("hyperaudio-duration")||setInterval(function(){u[t].classList.remove("hyperaudio-duration")},1e3*(c-(e-i))),u[t].classList.add("hyperaudio-duration")),i>e)return"break"}(b))break}})(i)}if(!o){this.getMedia(t).pause();for(var u=this.root.querySelectorAll(".hyperaudio-transcript"),c=0;c<u.length-1;c++)if(u[c]===this.lastSegment){var s=document.createEvent("HTMLEvents");s.initEvent("click",!0,!1),u[c+1].querySelector("*[data-t]").dispatchEvent(s);break}}}},{key:"onTimeUpdate",value:function(e){var t=e.target.currentTime,r=e.target.getAttribute("data-src");this.setHead(t,r)}},{key:"findMedia",value:function(e){var t=this.root.querySelector('video[src="'+e+'"], audio[src="'+e+'"]');if(!t){var r=this.root.querySelector('source[src="'+e+'"], source[src="'+e+'"]');r&&(t=r.parentNode)}return t}},{key:"hideOtherMediaThan",value:function(e){this.root.querySelectorAll('video:not([src="'+e+'"]), audio:not([src="'+e+'"])').forEach(function(e){e.pause(),e.style.display="none"})}},{key:"createMedia",value:function(e,t){var r=document.createElement("div");switch(t.split("/").splice(0,1).pop()){case"audio":r.innerHTML='<audio src="'+e+'" type="'+t+'" controls preload></audio>';break;default:r.innerHTML='<video src="'+e+'" type="'+t+'" controls preload playsinline></video>'}var o=r.querySelector("audio, video");return this.root.querySelector("header").appendChild(o),o}},{key:"getMedia",value:function(e,t){var r=this.findMedia(e)||this.createMedia(e,t);return r&&(this.hideOtherMediaThan(e),r.style.display=""),r&&!r.classList.contains("hyperaudio-enabled")&&(r.addEventListener("timeupdate",this.onTimeUpdate.bind(this)),r.setAttribute("data-src",e),r.classList.add("hyperaudio-enabled")),r}}]),e}();t.default=i},function(e,t,r){"use strict";function o(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function n(e,t){if(!e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!t||"object"!=typeof t&&"function"!=typeof t?e:t}function a(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t)}Object.defineProperty(t,"__esModule",{value:!0});var i=function(){function e(e,t){for(var r=0;r<t.length;r++){var o=t[r];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}return function(t,r,o){return r&&e(t.prototype,r),o&&e(t,o),t}}(),u=r(0),c=function(e){return e&&e.__esModule?e:{default:e}}(u),s=function(e){function t(e){var r=arguments.length>1&&void 0!==arguments[1]?arguments[1]:".hyperaudio-transcript, .hyperaudio-effect";o(this,t);var a=n(this,(t.__proto__||Object.getPrototypeOf(t)).call(this,e,r));document.addEventListener("selectionchange",a.onSelectionChange.bind(a));var i=a.root.querySelector(a.itemSelector).parentNode;return i&&i.addEventListener("mouseup",a.onMouseUp.bind(a)),a}return a(t,e),i(t,[{key:"onSelectionChange",value:function(){var e=window.getSelection();if(0!==e.rangeCount){var t=e.getRangeAt(0);if(t.startOffset!==t.endOffset){var r=t.commonAncestorContainer;if(3===r.nodeType)return void t.setStartBefore(r.parentNode);(r.matches("section[data-src]")||r.parentNode.matches("section[data-src]"))&&(this.root.querySelectorAll(".hyperaudio-selected").forEach(function(t){e.containsNode(t,!0)||e.containsNode(t.parentNode,!0)||(t.classList.remove("hyperaudio-selected"),t.removeAttribute("draggable"))}),r.querySelectorAll("*").forEach(function(t){e.containsNode(t,!0)&&"P"!==t.nodeName&&t.classList.add("hyperaudio-selected")}))}}}},{key:"onMouseUp",value:function(){var e=this,t=window.getSelection();this.root.querySelectorAll(".hyperaudio-selected").forEach(function(r){t.containsNode(r,!0)?(r.setAttribute("draggable","true"),r.addEventListener("dragstart",e.onDragStart.bind(e)),r.addEventListener("dragend",e.onDragEnd.bind(e))):(r.classList.remove("hyperaudio-selected"),r.removeAttribute("draggable"))})}},{key:"onDragStart",value:function(e){var t=void 0,r=void 0,o=void 0;this.root.querySelectorAll(".hyperaudio-selected").forEach(function(e){var n=e.cloneNode(!0);n.classList.remove("hyperaudio-selected"),n.classList.remove("hyperaudio-active"),n.classList.remove("hyperaudio-past"),n.removeAttribute("draggable"),t||(t=e.parentNode.parentNode.cloneNode(!1)),r||(r=e.parentNode.cloneNode(!1),t.appendChild(r)),o&&o.parentNode!==e.parentNode&&(r=e.parentNode.cloneNode(!1),t.appendChild(r)),r.appendChild(n),o=e}),e.dataTransfer.setData("text/html",t.outerHTML),e.dataTransfer.setData("text/plain",t.innerText),e.dataTransfer.effectAllowed="copy",e.dataTransfer.dropEffect="copy";var n=t.cloneNode(!0);this.root.appendChild(n),e.dataTransfer.setDragImage(n,0,0)}},{key:"onDragEnd",value:function(){this.root.querySelectorAll(".hyperaudio-selected").forEach(function(e){e.classList.remove("hyperaudio-selected"),e.removeAttribute("draggable")})}}]),t}(c.default);t.default=s},function(e,t,r){"use strict";function o(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function n(e,t){if(!e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!t||"object"!=typeof t&&"function"!=typeof t?e:t}function a(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t)}Object.defineProperty(t,"__esModule",{value:!0});var i=function(){function e(e,t){for(var r=0;r<t.length;r++){var o=t[r];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}return function(t,r,o){return r&&e(t.prototype,r),o&&e(t,o),t}}(),u=function e(t,r,o){null===t&&(t=Function.prototype);var n=Object.getOwnPropertyDescriptor(t,r);if(void 0===n){var a=Object.getPrototypeOf(t);return null===a?void 0:e(a,r,o)}if("value"in n)return n.value;var i=n.get;if(void 0!==i)return i.call(o)},c=r(0),s=function(e){return e&&e.__esModule?e:{default:e}}(c),d=function(e){function t(e){var r=arguments.length>1&&void 0!==arguments[1]?arguments[1]:".hyperaudio-transcript, .hyperaudio-effect";o(this,t);var a=n(this,(t.__proto__||Object.getPrototypeOf(t)).call(this,e,r)),i=a.root.querySelector(a.itemSelector).parentNode;return i&&(i.addEventListener("dragover",a.onDragOver.bind(a)),i.addEventListener("dragenter",a.onDragEnter.bind(a)),i.addEventListener("dragend",a.onDragEnd.bind(a)),i.addEventListener("drop",a.onDrop.bind(a))),a}return a(t,e),i(t,[{key:"setup",value:function(e){u(t.prototype.__proto__||Object.getPrototypeOf(t.prototype),"setup",this).call(this,e),e.setAttribute("draggable","true"),e.setAttribute("tabindex",0),e.addEventListener("dragstart",this.onDragStart.bind(this)),e.addEventListener("dragend",this.onDragEnd2.bind(this))}},{key:"onDragStart",value:function(e){e.dataTransfer.setData("text/html",e.target.outerHTML),e.dataTransfer.setData("text/plain",e.target.innerText),e.dataTransfer.effectAllowed="copy",e.dataTransfer.dropEffect="copy"}},{key:"onDragEnd2",value:function(e){e.target.remove()}},{key:"onDragOver",value:function(e){return e.preventDefault(),e.stopPropagation(),!1}},{key:"onDragEnter",value:function(e){e.preventDefault(),e.stopPropagation(),this.root.querySelectorAll(".hyperaudio-over:not(article)").forEach(function(e){return e.classList.remove("hyperaudio-over")});var t=e.target;if("function"==typeof t.matches){for(;t&&"function"==typeof t.matches&&!t.matches(this.itemSelector+"[draggable]");){if(!(t=t.parentNode))return;if("function"!=typeof t.matches)return}t.classList.add("hyperaudio-over"),this.root.querySelector("article").classList.add("hyperaudio-over")}}},{key:"onDragEnd",value:function(){this.root.querySelectorAll(".hyperaudio-over").forEach(function(e){return e.classList.remove("hyperaudio-over")})}},{key:"onDrop",value:function(e){e.preventDefault();var t=e.dataTransfer.getData("text/html"),r=e.target,o=document.createElement("div");o.innerHTML=t,o.querySelector("meta")&&o.querySelector("meta").remove();var n=o.children[0];if("DIV"===r.nodeName&&(r=r.parentNode),"ARTICLE"===r.nodeName)r.insertBefore(n,r.querySelector("div")),this.setup(n);else{for(;r&&"function"==typeof r.matches&&!r.matches(this.itemSelector+"[draggable]");)r=r.parentNode;r.parentNode.insertBefore(n,r),this.setup(n)}this.onDragEnd()}}]),t}(s.default);t.default=d},function(e,t,r){e.exports=r(4)},function(e,t,r){"use strict";function o(e){return e&&e.__esModule?e:{default:e}}var n=r(0),a=o(n),i=r(1),u=o(i),c=r(2),s=o(c),d=r(5),l=o(d);window.Player=a.default,window.Source=u.default,window.Sink=s.default,window.Hyperaudio=l.default,window.hyperaudio||(window.hyperaudio=new l.default)},function(e,t,r){"use strict";function o(e){return e&&e.__esModule?e:{default:e}}function n(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0}),t.Sink=t.Source=t.Player=void 0;var a=r(0),i=o(a),u=r(1),c=o(u),s=r(2),d=o(s);t.Player=i.default,t.Source=c.default,t.Sink=d.default;var l=function e(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:document;n(this,e),this.root="string"==typeof t?document.querySelector(t)||t:t,this.root.querySelectorAll(".hyperaudio-source").forEach(function(e){return new c.default(e)}),this.root.querySelectorAll(".hyperaudio-sink").forEach(function(e){return new d.default(e)}),this.root.querySelectorAll(".hyperaudio-player:not(.hyperaudio-sink):not(.hyperaudio-source)").forEach(function(e){return new i.default(e)})};t.default=l}]);
//# sourceMappingURL=hyperaudio.js.map