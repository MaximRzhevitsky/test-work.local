export function enqueueScript(e,t){if(enqueueDependencies(e.dependencies,t),e.extra){const t=document.createElement("script");t.textContent=e.extra,t.id=e.handle+"-js-extra",document.head.appendChild(t)}if(e.before){const t=document.createElement("script");t.textContent=e.before,t.id=e.handle+"-js-before",document.head.appendChild(t)}if(e.src){const t=document.createElement("script");t.src=e.src,t.id=e.handle+"-js",e.after&&(t.onload=function(){const t=document.createElement("script");t.textContent=e.after,t.id=e.handle+"-js-after",document.head.appendChild(t)}),document.head.appendChild(t)}else{const t=document.createElement("script");t.textContent=e.after,t.id=e.handle+"-js-after",document.head.appendChild(t)}}export function isEnqueue(e){return document.querySelector("#"+e+"-js")}export function enqueueDependencies(e,t){for(const n of e)isEnqueue(n)||enqueueScript(t[n])}