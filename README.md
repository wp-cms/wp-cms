WP is a PHP based CMS, forked from ClassicPress, a WordPress fork.

The goal of this project is to remove irrelevant stuff and achieve a clean, lean and optimized core which you can use and extend in any way you like to create awesome web projects.

I am currently facing this on my own but anybody is welcome to join. This is my proposed roadmap:

## STEP NUMBER 1 - "The removal"

Remove irrelevant/outdated stuff:

* ~~XML-RPC: old stuff, not needed~~ ✅
* ~~Pingbacks: old stuff, not needed~~ ✅
* ~~Gravatar: external host for profile pics, replace with self hosted~~ ✅
* ~~Link Manager: disabled links gallery plugin hidden inside the core~~ ✅
* Deprecated: remove deprecated functions, methods, classes and files ✍️
* Themes directory: WordPress themes are incompatible with WP CMS
* Plugins directory: WordPress plugins are incompatible with WP CMS

Note that removing the theme and plugin directories is just for the sake of cutting bonds with WordPress. It wouldn't make sense in any other way. You can find thousands of plugins out there and adapt them easily to the WP CMS, because the fundamental core will remain the same. Just run it, read the errors, and see what you can do about it. It's really that plain simple.

In the future, a new directory can be created. But this is out of discussion until it makes sense, if it ever does.

## STEP NUMBER 2 - "The modularization"

Separate (not remove) things that are merged within or entangledly spread all over the core and keep them as feature plugins. This isn't easy... so we should make a list of stuff that falls into this category and order it by complexity. Then start with the easiest to get motivated and go on with more difficult stuff like the comments system.

## STEP NUMBER 3 - "The cleanup"

After getting this done, the WP CORE will be way lighter, and I'd say to reorganize it, removing old PHP polyfills and back-compat stuff.

## STEP NUMBER 4 - "The media & editor upgrade"

Both the Media Library and the Editor need to be refreshed. Instead of retweaking the currently tewaked tweaks, why not? --> ADD a modern editor and then instead of fixing all the jquery plugins to make them work with OLD oldness, create what needs to be created from scratch or implement existing open source alternatives that fit the case.

## STEP NUMBER 5 - "Constant optimization"

Clean and optimize the core as much as possible, iterate over it with that goal in mind.