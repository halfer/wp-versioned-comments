wp-versioned-comments plugin
============================

Introduction
------------

wp-versioned-comments is a Wordpress plugin that will capture previous versions of comments as they are edited in the admin interface, and thus will find most use in blog moderation. If a user contribution is off-topic, offensive (or otherwise contravenes your commenting policy) then simply amend or clear the text in the Edit screen, safe in the knowledge that the previous version(s) will be kept (these are visible in the Edit screen).

Installation
------------

Just add the plugin to your plugins folder, and activate it in the usual way from the Plugins screen.

License
-------

The license terms for wp-versioned-comments is the same as Wordpress. That is, this code is licensed under GPLv2 (or later).

Future development
------------------

There's plenty of things I want to do with this:

* trash creates version
* moderation messages (set at the same time as editing a comment)
  * publicly visible
  * privately visible
* side-by-side text comparison
  * Colour-based and strike-out to show what has changed
* pagination, useful where a large number of versions are recorded

Presently previous version data is stored as serialised PHP arrays, which aren't very searchable. Thus, I might do these:

* switch to separate meta-fields for each comment for searchability
* search previous versions screen

Version history
---------------

Note that although everything works for me, I currently regard this as beta software. Thus, for the time being, I'll add new features into master unless I come to a backwards-incompatible change, at which point I'll branch. So, if you use this in production, drop me a line! I'd love to hear where/how it is being used in any case.

0.1 (beta) - current master.