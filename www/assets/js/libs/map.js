import e from"./live-dom";import o from"./dom-ready";let i="not-init";export const libraries=[];function r(e,o){if(theme.googleMapsApiKey)if("not-init"===i){i="progress";const r=[`key=${theme.googleMapsApiKey}`];libraries.length&&r.push(`libraries=${libraries.join(",")}`);const t=document.createElement("script"),n="https://maps.googleapis.com/maps/api/js?"+r.join("&");t.setAttribute("src",n),t.async=!0,t.onload=function(){i="done",e()},t.onerror=function(){i="error",o()},document.body.appendChild(t)}else"progress"===i?setTimeout((()=>r(e,o)),500):"done"===i?e():o();else console.error("There is a map on the page with no API key configured."),o()}export function map(i,t){o((()=>{e(i).dependency(r).firstShow(t)}))}