export default function n(n,e){b(n)||console.error("Invalid Selector",n);const o={},t={top:250,bottom:250},i=[];let s=null,r=!1,l=null,c="ready",u=[],d=[];const f={onceInit:null,onceShow:null,onceAlways:null,onceHide:null},a={firstInit:null,firstShow:null,firstAlways:null,firstHide:null},h={onceInit:null,onceShow:null,onceAlways:null,onceHide:null,init:null,firstShow:null,show:null,firstAlways:null,always:null,firstHide:null,hide:null},w={init:null,firstShow:null,show:null,firstAlways:null,always:null,firstHide:null,hide:null},y={doneFirst:{always:[],show:[],hide:[]},doneOnce:{init:!1,always:!1,show:!1,hide:!1}},p={onceInit:I,onceShow:S,onceAlways:A,onceHide:m,init:H,firstShow:v,show:E,firstHide:O,hide:g,firstAlways:$,always:x,dependency:M,setMargin:F};function b(n){try{document.createDocumentFragment().querySelector(n)}catch(n){return!1}return!0}if(e)for(const n in e)p[n](e[n]);function I(n,e=!0){h.onceInit=n,f.onceInit=e,N()}function S(n,e=!0){h.onceShow=n,f.onceShow=e,N()}function A(n,e=!0){h.onceAlways=n,f.onceAlways=e,N()}function m(n,e=!0){h.onceHide=n,f.onceHide=e,N()}function H(n,e=!1,o=!0){h.init=n,a.firstInit=e,!0===o&&N()}function v(n,e=!1,o=!0){h.firstShow=n,a.firstShow=e,!0===o&&N()}function E(n){h.show=n}function O(n,e=!1,o=!0){h.firstHide=n,a.firstHide=e,!1===o&&N()}function g(n){h.hide=n}function $(n,e=!1,o=!0){h.firstAlways=n,a.firstAlways=e,!1===o&&N()}function x(n){h.always=n}function M(n){l=n,c="not_ready"}function F([n=250,e=250]){t.top=n,t.bottom=e}function _(n){i.includes(n)||(i.push(n),L(n),!1===r&&("undefined"==typeof IntersectionObserver?q(n):function(n){let e=!1;o[n]=new IntersectionObserver((([o])=>{e||o.intersectionRatio>0?(e=!0,o.isIntersecting?j(n,"show"):!1===o.isIntersecting&&j(n,"hide"),j(n,"always")):e=!0}),{rootMargin:`${t.bottom}px 0px ${t.top}px 0px`,threshold:[.01]}),o[n].observe(n)}(n)))}function q(n){const e=()=>{c="ready",q(n)},t=e=>{o[n].unobserve(n),e&&console.error(e)};"ready"===c?Object.keys(h).forEach((e=>{w[e]&&h[e].bind(n)(),h[e]&&h[e].bind(n)()})):"not_ready"===c&&(c="process",l(e,t))}function L(n){if(h.init||w.init||h.onceInit){const e=()=>{c="ready",d.forEach((n=>L(n))),d=[]},t=e=>{o[n].unobserve(n),e&&console.error(e)};if("ready"===c){if(h.onceInit)return void(!1===y.doneOnce.init&&(y.doneOnce.init=!0,h.onceInit.bind(n)(),f.onceInit&&(r=!0,s.disconnect(),i.forEach((n=>{void 0!==o[n]&&o[n].unobserve(n)})))));w.init&&w.init.bind(n)(),h.init&&h.init.bind(n)()}else"not_ready"===c?(c="process",d.push(n),l(e,t)):"process"===c&&d.push(n)}}function j(n,e){if("ready"===c){const t=e.charAt(0).toUpperCase()+e.slice(1);y.doneFirst[e].includes(n)||(y.doneFirst[e].push(n),w[`first${t}`]&&w[`first${t}`].bind(n)(),h[`first${t}`]&&(h[`first${t}`].bind(n)(),a[`first${t}`]&&o[n].unobserve(n))),w[e]&&w[e].bind(n)(),h[e]&&h[e].bind(n)()}else if("not_ready"===c){const t=()=>{c="ready",u.forEach((({element:n,type:e})=>{j(n,e)})),u=[]},i=e=>{o[n].unobserve(n),e&&console.error(e)};c="process",u.push({type:e,element:n}),l(t,i)}else"process"===c&&u.push({type:e,element:n})}function N(){if(null===s&&b(n)){s=new MutationObserver((e=>{e.forEach((e=>{"childList"===e.type&&e.addedNodes.length&&e.addedNodes.forEach((e=>{e instanceof HTMLElement&&(e.matches(n)&&_(e),e.querySelectorAll(n).forEach((n=>{_(n)})))}))}))}));const e=()=>{document.body?(s.observe(document.body,{subtree:!0,childList:!0}),document.querySelectorAll(n).forEach((n=>_(n)))):window.setTimeout(e,50)};e()}}return Object.freeze({onceInit:I,onceShow:S,onceAlways:A,onceHide:m,init:H,firstShow:v,show:E,firstHide:O,hide:g,firstAlways:$,always:x,dependency:M,setMargin:F})}