import o from"./add-style.js";const e="/wp-content/plugins/cookie-law-info/public/css/cookie-law-info-";Promise.all([o(`${e}public.css`),o(`${e}gdpr.css`),o(`${e}table.css`)]).then((()=>{document.documentElement.classList.add("cookie-law-info-loaded"),document.documentElement.classList.remove("cookie-law-info-hidden")}));