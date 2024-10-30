=== CommentsWP ===
Contributors: slaFFik
Tags: comments, comments dashboard, comments analytics, comments statistics, comments insights, commenters
Requires at least: 5.5
Tested up to: 6.7
Stable tag: 1.1.0
Requires PHP: 7.2
License: GNU General Public License v2.0 or later

A beautifully helpful dashboard for all your WordPress comments: analyze & aggregate comments, see comments insights, learn about your commenters.

== Description ==

CommentsWP aims to provide more tools to moderators for easier comments analysis and insights gathering. The plugin aggregates a lot of information that is already stored in your database - and showcases everything in a much more readable and actionable way.

The Comments component inside WordPress will turn from being neglected to a very usable and actionable way to monitor and improve comments engagement. Your site can do much more than just display the "leave a comment" form, collect the text, display it in a list in the admin area, and allow you to edit them.

= Comments Card Widgets =

As the data is already stored in the database - you just need to access it easily.

And that's where CommentsWP shines by providing you on its Dashboard page various small card widgets:

* Section with 4 default comment statuses: Approved, Pending, Spam, and Trashed
* Average Time To First Comment (with a precision up to a second; example: 3m 2d 8h 35min 18s)
* Fastest Time To First Comment (with a precision up to a second, example: 4d 20h 38min 53s)
* Number of posts with and without comments (both total and percentage)
* Number of comments left by logged-in and logged-out users (both total and percentage)
* Number of top-level comments and those in a thread, replies (both total and percentage)
* Number of pingbacks and trackbacks (both total and percentage)

= Comments Table Widgets =

But not any useful information can fit the small card with a number or two. Sometimes tables are much more useful.

Even wondered how many comments were left by your most prolific commenters? There is a table card for that, called "Total by User". And you can see the number of comments for each user who can be grouped by either email or IP addresses. You literally can find users who leave comments on your site under the same email address - but different names, or use the same email - but different IP addresses.

And there is also this awesome "Anomalies" table card that allows identifying offenders and legit comment "spammers" (who leave a ton of comments manually, perhaps being too invasive and trying to hide their location by using different IP addresses or identity by using different email addresses). You better check the card on a regular basis.

Last but not least: the "Total by Time Period" table card will allow you to see the number of comments left throughout the whole history of your site, grouped by years, months, and weeks. That's an easy way to see global commenting trends on your site.

== Frequently Asked Questions ==

= Is the data cached? =

Yes, the default cache duration is 1 minute for now (`MINUTE_IN_SECONDS`).

= How does the plugin affect the front-end and site visitors? =

CommentsWP plugin does not do its calculations and data aggregation when regular visitors are working with your site.

Please note that the data is displayed and calculated only when you are on the Comments > Dashboard page.

= Who has access to the CommentsWP Dashboard? =

Only those logged-in users who have access to the `wp-admin` area of your site and with the `moderate_comments` capability. By default, that's everyone with Administrator and Editor roles.

= Is CommentsWP translation ready? =

Yes, CommentsWP can be fully translated into any language via the `commentswp` textdomain. The plugin is compatible with Loco Translate and WPML as well.

== Screenshots ==

1. CommentsWP Dashboard.

== Changelog ==

= 1.1.0 =
- IMPORTANT: This version requires PHP 7.2 or higher and WordPress 5.5 or higher.
- Added: Each Dashboard Card now has its own link to a dedicated documentation page with more information about the card.
- Added: Display the actual dates next to week numbers in the "Total by Time Period" card for improved readability.
- Added: All Comments Date range support: the "Total by Time Period" card when grouped by month or week now has links that filter comments on the "All Comments" page accordingly.
- Fixed: In certain screen sizes, the double cards labels were not fitting in the block and were overlapping with each other. Now ellipsis is displayed when the label is too long.

= 1.0.0 =
- Initial release containing 13 cards, 2 dashboard-wide filters, and a bunch of cards specific filters.
