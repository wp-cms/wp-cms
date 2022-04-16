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

## STEP NUMBER 2 - "Core Plugins"

Add some necessary Core Plugins that any CMS should have. Also, come up with a clean folder and files structure that will serve as a reference for WP Plugin Development.

* ~~Avatars: allow users to upload a custom avatar to their profile~~ ✅
* ~~Clone Posts: allow users to clone posts for a potential faster workflow.~~ ✅
* Custom Post Types: allow users to create custom post types from the admin area. Something simple that goes straight to the point, without complications. ✍️

Once those basic and useful plugins are provided and well organized, separate (not remove) things that are merged within or entangledly spread all over the core and keep them as Core Plugins:

* REST API
* Comments System

## STEP NUMBER 3 - "The cleanup"

After getting this done, the WP Core will be way lighter, and I'd say to reorganize it, removing old PHP polyfills and back-compat stuff.

Also, I would like to cleanup the database. Specially the `wp_posts` table, which has some crazy columns. Remove non GMT date and probably move post content into it's own table. Some posts don't use the post content field and some would actually need two or three different post content instances.

## STEP NUMBER 4 - "The media & editor upgrade"

Both the Media Library and the Editor need to be refreshed. Instead of retweaking the currently tewaked tweaks, why not? --> ADD a modern editor and then instead of fixing all the jquery plugins to make them work with OLD oldness, create what needs to be created from scratch or implement existing open source alternatives that fit the case.

## STEP NUMBER 5 - "Constant optimization"

Clean and optimize the core as much as possible, iterate over it with that goal in mind.