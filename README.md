# What's this?

WP CMS is a PHP based CMS, forked from ClassicPress, a WordPress fork. **Focused towards developers**.

WP does not stand for WordPress, it stands for nothing and at the same time for anything you want. It could be WinniPress, WungaPunga, WebPower, ... you name it.

This is a personal experiment, with which I want to achieve a clean, lean and optimized core that developers can use and extend in any way they like to create awesome web projects.

# Who should use WP CMS?

Developers who know some traditional web development skills: PHP, HTML, CSS, Javascript...

- If you are looking for a tool like [Wix](https://www.wix.com/), but open source, go to [WordPress](https://wordpress.org/).
- If you are looking for the good old WordPress, without the blocks fuss and with a focus on "please keep things the way they were and don't mess with it", go to [ClassicPress](https://www.classicpress.net/).
- If you are an adventurous developer who loves the old WP Way of doing things, but hate bloat... stay here.

# Key points of the WP CMS philosophy

## KEY NUMBER 1 - "Remove Bloat"

Remove irrelevant/outdated stuff:

* ~~XML-RPC: old stuff, not needed~~ ✅
* ~~Pingbacks: old stuff, not needed~~ ✅
* ~~Gravatar: external host for profile and low quality monster pics~~ ✅
* ~~Link Manager: disabled links gallery plugin hidden inside the core~~ ✅
* ~~Deprecated: remove deprecated functions, methods, classes and files~~ ✅
* Emojis: useless bloat
* Quick draft dashboard widget: does anyone use that?
* Post via email: from the old blogging times

## KEY NUMBER 2 - "Independent Themes & Plugins Directory"

Most WordPress themes and plugins won't work, because there will be some/many breaking changes. So, a new directory has to exist for this purpose.

Since I really like ClassicPress, I will collaborate with plugins in the [CP Plugin Directory](https://directory.classicpress.net/plugins), and integrate that directory into WP CMS. Why? Because it's a fresh directory and there is potential for it to grow without all the really old stuff (and new fuss!) that causes problems.

ClassicPress directory is under construction. ✍️

## KEY NUMBER 3 - "The media & editor upgrade"

Both the Media Library and the Editor need to be refreshed. Instead of retweaking the currently tewaked tweaks, why not? --> ADD a modern editor and then instead of fixing all the jquery plugins to make them work with OLD oldness, create what needs to be created from scratch or implement existing open source alternatives that fit the case.

## KEY NUMBER 5 - "Constant optimization"

Clean and optimize the core as much as possible, iterate over it with PHP 8 as minimum supported PHP version in mind.

> “Simplicity is the ultimate sophistication. It takes a lot of hard
> work to make something simple, to truly understand the underlying
> challenges and come up with elegant solutions. […] It’s not just
> minimalism or the absence of clutter. It involves digging through the
> depth of complexity. To be truly simple, you have to go really deep.
> […] You have to deeply understand the essence of a product in order to
> be able to get rid of the parts that are not essential “ —  **Steve Jobs.**