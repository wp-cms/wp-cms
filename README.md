WP is a PHP based CMS, forked from ClassicPress, a WordPress fork.

The goal of this project is to remove irrelevant stuff and achieve a clean, lean and optimized core which you can use and extend in any way you like to create awesome web projects.

I am currently facing this on my own but anybody is welcome to join. This is my proposed roadmap:

## STEP NUMBER 1 - "The removal"

Remove irrelevant/outdated stuff:

* ~~XML-RPC: old stuff, not needed~~ ✅
* ~~Pingbacks: old stuff, not needed~~ ✅
* ~~Gravatar: external host for profile pics~~ ✅
* ~~Link Manager: disabled links gallery plugin hidden inside the core~~ ✅
* ~~Deprecated: remove deprecated functions, methods, classes and files~~ ✅

## STEP NUMBER 2 - "Independent Themes & Plugins Directory"

WordPress themes and plugins won't work. So, a new directory has to exist for this purpose. Instead of using an external service for this, I am playing around with JSON files that contain the information about themes and plugins. I'm not sure a JSON files based directory is better than an external API, but this is just a quick solution to cut from the WordPress directory for the moment and start a fresh directory with a set of useful plugins.

So, I will be adding basic and useful plugins to this new directory and also come up with a clean folder and files structure that will serve as a reference for WP Plugin Development.

* ~~Add Plugin: Avatars: allow users to upload a custom avatar to their profile~~ ✅
* ~~Add Plugin: Clone Posts: allow users to clone posts for a potential faster workflow.~~ ✅
* Add Plugin: Custom Post Types: allow users to create custom post types from the admin area. Something simple that goes straight to the point, without complications. ✍️

## STEP NUMBER 3 - "Core Plugins"

Once those basic and useful plugins are provided and well organized, separate (not remove) things that are merged within or entangledly spread all over the core and keep them as Core Plugins:

* Comments System
* REST API

## STEP NUMBER 4 - "The cleanup"

After getting this done, the WP Core will be way lighter, and I'd say to reorganize it, removing old PHP polyfills and back-compat stuff.

Also, I would like to cleanup the database. Specially the `wp_posts` table, which has some crazy columns. Remove non GMT date and probably move post content into it's own table. Some posts don't use the post content field and some would actually need two or three different post content instances.

## STEP NUMBER 5 - "The media & editor upgrade"

Both the Media Library and the Editor need to be refreshed. Instead of retweaking the currently tewaked tweaks, why not? --> ADD a modern editor and then instead of fixing all the jquery plugins to make them work with OLD oldness, create what needs to be created from scratch or implement existing open source alternatives that fit the case.

## STEP NUMBER 6 - "Constant optimization"

Clean and optimize the core as much as possible, iterate over it with that goal in mind.