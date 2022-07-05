
WP is a PHP based CMS, forked from ClassicPress, a WordPress fork. **Focused towards developers**.

This is a personal experiment, with which I want to achieve a clean, lean and optimized core that developers can use and extend in any way they like to create awesome web projects.

- If you are looking for a tool like [Wix](https://www.wix.com/), but open source, go to [WordPress](https://wordpress.org/).
- If you are looking for the good old WordPress, without the blocks fuss and with a focus on "please keep things the way they were and don't mess with it", go to [ClassicPress](https://www.classicpress.net/).
- If you are an adventurous developer who loves the old WP Way of doing things, but hate bloat... stay here. Just be warned: I don't care about backwards compatibility with old plugins. I will move this thing forwards however I want and whatever it takes. I usually never use public plugins for my sites as they are, I copy and tweak GPL licensed stuff to make it behave and look like I want it to. If something stops working it doesn't mean you have to throw it all away, in most cases you probably just have to modify or remove a few lines of code. If you understand that, all cool.

I am currently facing this on my own but anybody is welcome to submit PRs. If I like the idea I will merge it. This is my proposed and dynamic roadmap (it changes whenever my creativity changes color, which happens often):

## STEP NUMBER 1 - "The removal"

Remove irrelevant/outdated stuff:

* ~~XML-RPC: old stuff, not needed~~ ✅
* ~~Pingbacks: old stuff, not needed~~ ✅
* ~~Gravatar: external host for profile and low quality monster pics~~ ✅
* ~~Link Manager: disabled links gallery plugin hidden inside the core~~ ✅
* ~~Deprecated: remove deprecated functions, methods, classes and files~~ ✅

## STEP NUMBER 2 - "Independent Themes & Plugins Directory"

WordPress themes and plugins won't work. So, a new directory has to exist for this purpose.

Since I really like ClassicPress, I will collaborate with plugins in the [CP Plugin Directory](https://directory.classicpress.net/plugins), and integrate that directory into WP CMS. Why? Because it's a fresh directory and there is potential for it to grow without all the really old stuff (and new fuss!) that causes problems.

Currently working on this integration. ✍️

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