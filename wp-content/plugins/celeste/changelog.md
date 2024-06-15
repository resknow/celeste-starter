## 2.0.0

-   [New] Celeste plugin! Turns out having all the logic in the theme isn't the best for updates and such, so all the things that make Celeste tick have been moved to a Celeste plugin. The theme directory is much tidier as a result and all the code you'll likely never touch is gone!
-   [New] <a href="https://resknow.notion.site/Query-Builder-62b978bcfd524cb0812f0fab14df0b5e">Query Builder</a> - easy to use, chainable API for querying your posts!
-   [New] <a href="https://resknow.notion.site/Components-87a7884132804640a29b47fabcd51b0f">Component Slots!</a> You can now specify custom slots in your components.
-   [Update] Bumped Node version to 20, be sure to run `nvm use` before `npm install`.
-   [Update] No longer removing WooCommerce styles by default
-   [Removed] `.editorconfig` - doesn't seem to actually do anything of value by itself.

## 1.4.0

-   [Enhancement] Add better error handling for components. They'll now display errors in place. In local environments, this includes error information and in production, shows an error message.

## 1.3.2

-   [Update] Add "Create main.js file?" option to `add component` command.
-   [New] Add framework support for components. You'll need to install the framework you want to use via NPM and you can then create a `main.{vue,react,svelte}.js` file in the component. Supported Frameworks are Vue, React and Svelte.

## 1.3.1

-   [Fix] Finally set the correct font family in the base stylesheet.
-   [Fix] Fixed an issue where blocks & components only used in a template were not parsed and so would not enqueue scripts and styles.

## 1.3.0

-   [Update] Switch to Full Site Editing 👀
-   [New] Introduced new `layout`, `header` and `footer` blocks so we can make use of the Site Editor without sacrifying full control.
-   [New] New `@if` and `@for` directives for components allow you to check conditions or loop over a component when calling it. e.g. `<PostCard @for="post in posts" :id="post" />`
-   [Fix] Fix HTML escaping for icon selects after recent ACF update.

## 1.2.1

-   [Fix] Fixed a bug where Block assets would be enqueued after the main assets, causing issues with Alpine.js
-   [Fix] Fixed a bug where nested blocks would not have their assets enqueued automatically.
-   [Update] Swap Yoast for SEO Framework because Yoast can get in the bin.
-   [Enhancement] General tidy up in the theme directory.

## 1.2.0

-   [Enhancement] Dropped the `src` directory to clean up the theme directory. Global styles and scripts are now managed from the `Layout` component.
-   [Enhancement] Moved the `assets` directory into the theme directory, since all we do is copy it anyway. This is a place to store un-processed files.

## 1.1.0

-   [New] `component` function so you can render a component from PHP. Useful for use in hooks (e.g. replacing the markup for the product gallery in WooCommerce)
-   [New] `wp celeste pull <command>` will now automatically try to install dependencies (e.g. the `goform-input` block requires `goform`).
-   [New] `wp celeste pull <command>` now allows you to rename a component if it already exists. So you could use an existing component as a boilerplate for a new one.
-   [New] Components & Blocks now ship with a `celeste_version` which signifies the minimum version of Celeste required to use it.
-   [New] `props` function - Components can now define props and include information like `type`, `required` and `default` to validate props.
-   [New] `attributes` function - When using the `props` functions, defined props will be split from the rest. Pass the rest to `attributes` to get back an HTML string of attributes.
-   [New] In local development, Components will show warnings if prop types are incorrect. This feature also introduces a new `celeste_warn` function that can also be used to display warnings from anywhere in your codebase.
-   [Update] The `Twig::render_component` method in Twilight now returns instead of echo, since Twig echos anyway.

### Note

Pulling a component or block that is not compatible with the version of Celeste you have will not prevent you yet. It's coming in the next version.

## 1.0.0

-   [New] `wp celeste pull component` and `wp celeste pull block` command to add a components and blocks from our library to your project.
-   [New] Auto Reload 🥳 Still a work in progress but seems to work well.
-   [New] Components & Blocks will now only enqueue their styles if they exist on the current page. Note though that in the Block Editor, all of them are enqueued because we have no way of knowing which blocks will be inserted.
-   [New] Added new `Assets::component()` and `Assets::block()` methods that lets you enqueue either main.js or style.css for a component or block by it's name. e.g. `Assets::component( 'Header', 'style' )` will enqueue the style.css file (if it exists) for the Header component. Used in combination with the new `present` filter, will only enqueue if the Component is found on the current page.
-   [New] Add filter: `twilight.component.$name.present` - Runs much earlier in the lifecycle and only if a component is found in the current template.
-   [Fix] Fixed HTML escaping issue with the `Menu` component.
-   [Update] TWML is now called Twilight. Just sounds nicer 😌
-   [Update] Removed Sass. 🫡
-   [Update] The Goform block and related components now use the new Web Component validation, which means less config and automatic ReCaptcha setup.
-   [Update] Moved Goform related markup and logic to Components. This makes the block much simpler and also gives you the ability to use the components wherever you like in your theme. Take a look at the `Goform` component to see how they work.
-   [Update] Gave the 404 page a bit of a facelift.
-   [Enhancement] Completely rewrote the Asset bundler and watcher
-   [Enhancement] Improved boilerplate for `component.php` files when using `wp celeste add component`.
-   [Enhancement] Included the current Celeste version in the Logo and as a comment at the top of the pages `<head>` so you always know which version you're on.
-   [Enhancement] Improved performance of `get_posts_with_fields` when loading multiple (20+) number of posts.
-   [Enhancement] Added types to functions and class methods where possible.
-   [Enhancement] Added `Icon::exists()` method and Twig function `icon_exists` to check if an icon exists before trying to render it.
-   [Enhancement] The Icon block now supports colour and padding controls!
-   [Enhancement] Better default Prose style for text blocks

### Known Issues

Compiled assets are not cleared between saves when running `npm run dev` so old assets may persist when deleted. For now, restart the command to solve this. A fix will be added soon.

## 0.7.0

-   [New] Add some common Block Patterns
-   [Enhancement] Added Padding controls to the Button block
-   [Enhancement] Add Block Patterns page to the Appearance menu
-   [Fix] Fixed orientation of Order buttons on the Grid Item block
-   [Fix] Fixed Block Gap controls on the Grid Item and Section blocks
-   [Fix] Fixed a CLI issue which caused choices added to a Select field to not be saved to block.json
-   [Fix] Provide ACF fields in `post` context for Twig templates

## 0.6.0

-   Minor bug fixes

## 0.5.0

-   Updated Resknow Blocks plugin to add missing responsive options in the Grid block and alignment options for Button blocks.
-   Added WP CLI integration with interactive commands for adding Blocks and Components
-   [Fix] Fixed dropdown menus on Desktop menu
-   [Fix] Fixed missing default size for `<Button>` component

## 0.4.0

## 0.3.0

## 0.2.0

## 0.1.0

-   Made the thing
