# Changelog

## 2.1.6 (2023-05-03)
* Recoded LiveDom.
* Stop sliders if the user has requested to minimize the movement of elements.
* Displaying menu navigation tooltips.
* Footer menu navigation.
* Content-visibility settings.
* Updated menu script.
* Added cache.
* Added node-proxy scripts.
* Added Tom Select.
* Styles are now connected before the section in the hbs.

## 2.1.5 (2023-04-18)
* Stylized Woo shop.
* Fixed select field on Woo pages.
* Added titles on Woo pages.
* Smooth-scroll fixed.
* Changed <dialog> styles.

## 2.1.4 (2023-04-09)
* Stylized Woo product.
* Updated local fonts.

## 2.1.3 (2023-04-01)
* Added level header parameter for accordion.
* Updated cart html code.
* Checkout updated.
* Blog text highlight fixed.

## 2.1.2 (2023-03-26)
* Fixed mobile menu hidden.
* Temporarily the title of the accordions was changed to H2, in the future we plan to add a parameter for it.
* Fixed multiple triggering of the menu close event.
* Adding an icon to a video is now done via JS.
* Adding cli-modal styles.
* Update default templates and styles.

## 2.1.1 (2023-03-20)
* Fixed is-hidden class in tabs.
* Fixed mobile-menu in mobile aside.

## 2.1.0 (2023-03-12)
* All jQuery scripts (not woo) to native JS.
  * Slick to Tiny Slider.
  * Magnific Popup to native HTML dialog.
* Refactoring styles.
* Refactoring scripts.
* FAQ search to module.
* Updated npm packages.
* Refactoring gulp tasks.
* Added Prettier.
* Added libs in modules.
* Added instant.page.

## 2.0.10 (2023-02-27)
* Added focus on element after smooth scroll.
* Breadcrumbs are now collected only in a separate file.
* Removed overflow-wrap: break-word; from sanitize.pcss.

## 2.0.10 (2023-02-13)
* Fixed accessibility modal.

## 2.0.9 (2023-02-12)
* Fixed gulp tasks, now specific folders for the task are copied, and not all folders as before.
* Updated forms styles.
* Updated GF load optimization js and styles.
* Fixed visibility animation page speed warning.

## 2.0.8 (2023-02-04)
* Added wrapper for breadcrumb link.
* Added active property.
* Updated forms styles and js. Added select2 for select fields
* Updated jQuery version (v3.6.3).
* Fixed hide sub menu after mouseover (accessibility menu).

## 2.0.7 (2022-11-18)
* Fixed error in old iPhones
* Fixed stylelint woocommerce fonts
* Added tools scripts in package.json

## 2.0.6 (2022-10-06)
* Fixed slider function, error call callback.
* Fixed theme url in webpack config.
* Changed all line-height to PX units.
* Hide .gform_required_legend.
* Update Woocommerce menu-cart JS.
* Now most of the JS in main.js is dynamically included.
* Fixed typo fag-search to faq-search.
* Changed the order and values for some LiveDom AUTO flags.

## 2.0.6 (2022-09-30)
* Added "Thank You" page.
* Fixed FAQ page.
* Updated footer BAM.
* Updated Woocommerce My Account.
* Updated Woocommerce Reset Password.

## 2.0.5 (2022-09-30)
* Change button mobile.
* Fix config.yml (fix start style).

## 2.0.4 (2022-09-29)
* Added isWoocommerce helper.
* Updated header and mobile menu to better fit the new design with Woocommerce.
* Removed the main header and mobile menu wrappers to the base, only the content remained in src.
* Added a class to the header for woocommerce .page-header_woocommerce.
* Fixed reloading helpers if woocommerce is disabled.
* Hide shopping cart in menu if woocommerce is disabled.

## 2.0.3 (2022-09-29)
* Update toggle focus.

## 2.0.2 (2022-09-28)
* Fixed watch gulp task.

## 2.0.1 (2022-09-19)
* Started translating the menu into BEM.
* Now styles, scripts and pictures are collected in the assets folder.
* Added map function For easier work with maps.
* Fixed sliders feature that always turned on autoplay.
* Changed stylelint settings that we found inconvenient for us.
* The menu cart for woocommerce has been moved to a separate file.
* Added modules folder.
* Added wp-images gulp task.

## 2.0.0 (2022-08-28)
* Updated folder structure:
  * Now development is carried out in the src folder, and the default resources are placed in separate base/defaults and base/woocommerce folders.
  * Renamed js folders to scripts, css to styles to be more appropriate.
  * Renamed css/partials to styles/parts. to unlink partials from Handlebars.
  * Styles now need to be written separately for each section/block, and files should be created in src/styles/sections.
* The process of abandoning jQuery has begun, now there is main.js in it, you need to write it without jQuery, it will be connected asynchronously and the jquery.js file needs to be written in it if jQuery is needed.
* Changed coding standards to use WordPress coding standards.
* Changed Gulp tasks and split into files and moved to gulp folder.
* Updated and revised dependencies.

## 1.2.0
* Merged all branches
* Updated Local Fonts

## 1.1.0
* Updated jQuery - fix scroll events
* Updated Local Fonts
* Removed schema.org JSON from layout

## 1.0.1
* Added accessibility menu.

## 1.0.0
* Added local fonts.
* Added the path from the settings to the WP Copy task so that the process supports multiple projects.
