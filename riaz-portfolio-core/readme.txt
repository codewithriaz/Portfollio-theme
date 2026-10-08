=== Riaz Portfolio Core ===
Contributors: riazahmed
Requires at least: 6.6
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Companion plugin for the Riaz Portfolio theme. Keeps your content in a plugin so it survives a change of theme.

== Description ==

* Content types: Services, Portfolio (with categories), Case Studies, Testimonials (written or YouTube video), Team, FAQs and Pricing, each with its own fields.
* "Theme Section" block for the block editor, plus four page patterns.
* "Theme Section" widget for Elementor.
* Shortcodes for Divi, WPBakery and the classic editor: [riaz_section name="services"], [riaz_contact_form], [riaz_social], [riaz_button], [riaz_icon].
* Contact form protected by a nonce, a honeypot, a minimum fill time and a rate limit. Messages are emailed; nothing is stored.
* SEO: meta description, Open Graph, Twitter cards, canonical links on archives and a JSON-LD graph (WebSite, Person/Organization, WebPage, BreadcrumbList, BlogPosting, CreativeWork, Article, Service, FAQPage). Switches off automatically when Yoast SEO, Rank Math, SEOPress, All in One SEO, Slim SEO or The SEO Framework is active.
* Optional Google Analytics 4 (editors are not tracked).
* Starter content importer that recreates the demo homepage.

Section, social, button and icon output require the Riaz Portfolio theme. Content types, the contact form and SEO work with any theme.

== Installation ==

The easiest way: activate the Riaz Portfolio theme and click "Install & activate Riaz Portfolio Core" in Appearance → Riaz Portfolio. Or upload riaz-portfolio-core.zip in Plugins → Add New → Upload Plugin.

== Hooks ==

* riaz_core_post_type_args — change any content type's register_post_type() arguments.
* riaz_core_seo_enabled — turn the SEO output on or off.
* riaz_core_schema_graph — edit the JSON-LD graph.
* riaz_core_same_as, riaz_core_entity_image, riaz_core_default_share_image, riaz_core_faq_schema — data supplied by the theme.
* riaz_core_contact_recipient — who receives contact form messages.
* riaz_core_contact_submitted — action after a message is processed (e.g. log to a CRM).

== Changelog ==

= 1.0.0 =
* Initial release.

== Copyright ==

Riaz Portfolio Core, Copyright 2026 Riaz Ahmed. Distributed under the terms of the GNU GPL v2 or later.
