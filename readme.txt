=== OmniForm ===
Contributors: jrtashjian
Tags: contact form, block editor, form builder, forms, email
Requires at least: 6.3
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.2.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily create and manage custom forms with the block editor, customizable fields, and form submission management for your website.

== Description ==

OmniForm is a powerful WordPress plugin that allows you to create and manage forms within your WordPress site. With OmniForm, you can easily create custom forms for your website, collect user data, and manage form submissions.

= 🌟 Features =

* Complete and steadfast support for the block editor and block themes.
* Choose from 20+ form fields blocks, including text fields, checkboxes, radio buttons, and more
* Choose from pre-built form templates or create your own from scratch
* Manage form submissions within the admin dashboard
* Email notifications for new form submissions
* Customize form styling to match your website's design
* Spam protection with Cloudflare Turnstile, hCaptcha, and Google reCAPTCHA.

= 🚀 Getting Started =

To get started with OmniForm, simply install and activate the plugin on your WordPress site. Once activated, you can create new forms by navigating to the OmniForm menu in your WordPress dashboard.

From there, you can choose to create a new form from scratch or use one of the pre-built form templates. Once you've created your form, you can customize it to your liking using the block editor interface.

= 🌈 What's Coming Next =

OmniForm is designed to be your all-in-one form solution, and it's about to get even more powerful. On the immediate horizon:

* **User Login & Registration Forms**: Enhance user experience with integrated login and registration capabilities.
* **Site Search Forms**: Elevate website usability by implementing customizable site search features.

These additions will lay the groundwork for an upcoming premium offering featuring advanced form types, including but not limited to:

* Surveys
* Polls
* Quizzes
* Conversational Forms

Stay tuned — OmniForm aims to continually adapt and expand its functionalities to meet evolving user needs.

= 📣 Share Your Ideas and Enhancements =

OmniForm thrives on user engagement. If you have feature requests, ideas for improvements, or even want to contribute code, your input is more than welcome.

* **GitHub Repository**: Join the discussion, report bugs, or contribute directly to the codebase through the [OmniForm GitHub Repository](https://github.com/jrtashjian/omniform).

By sharing your ideas, you're actively participating in the future direction of OmniForm.

= 🙏 A Special Thanks from OmniForm =

Whether you're a first-time user or someone who's considering giving OmniForm a try, thank you! Your interest is what fuels ongoing improvements and innovations.

Feel free to explore, provide feedback, or even contribute to the plugin's development. Every bit of support counts and is highly appreciated.

== Installation ==

= Automatic installation =

To do an automatic install of OmniForm, log in to your WordPress dashboard, navigate to the Plugins menu, and click “Add New.”

In the search field type “OmniForm,” then click “Search Plugins.” Once you’ve found us, you can click “Install Now” and WordPress will take it from there.

= Manual installation =

Manual installation method requires downloading the OmniForm plugin and uploading it to your web server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://wordpress.org/support/article/managing-plugins/#manual-plugin-installation).

== Screenshots ==

1. OmniForm enhances Block Editor with a specialized form-only editing option.
2. Save Time and Effort: Use OmniForm's Ready-to-Customize Form Templates.
3. Create Forms Directly in WordPress Posts or Pages with OmniForm.
4. Get Insights on Form Performance: Responses, Impressions, and Conversion Rate.
5. OmniForm Keeps You Informed: Track Submissions and Get Email Notifications within WordPress.

== Changelog ==

= 1.2.1 / 2024-04-10 =

  * Fixed improper check for success/error message blocks
  * Resolved array to string conversion warning

= 1.2.0 / 2024-04-09 =

  * Fixed Cloudflare Turnstile validation in Captcha block
  * Added ability to customize email notification [#25](https://github.com/jrtashjian/omniform/pull/25)
  * Introduced success and error message blocks [#23](https://github.com/jrtashjian/omniform/pull/23)
  * Improved block splitting, replacing, removing, and merging of form blocks [#22](https://github.com/jrtashjian/omniform/pull/22)
  * Introduced Hidden inputs with custom callbacks [#21](https://github.com/jrtashjian/omniform/pull/21)
  * Fixed display of form blocks in the Style Book
  * Allowed form to be rendered while previewing

= 1.1.0 / 2024-04-01 =

  * Improve validation for grouped fields [#18](https://github.com/jrtashjian/omniform/pull/18)
  * Require WordPress version 6.3
  * Upgrade blocks to API version 3
  * Properly restrict nested blocks within the form block
  * Use global content_width for preview viewportWidth, if available
  * Add proper password protection for form rendering
