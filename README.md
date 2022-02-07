# This is only a very rudimentarily implemented extension.
- Only 1 slider implemented for 1 single individual site.

# mod_splideghsvs
- Joomla slider based on Splidejs / splide JS library (https://github.com/Splidejs/splide).
- The module layouts are based on Bootstrap 5? That has not yet been decided.

 ## Changelogs
 - https://updates.ghsvs.de/changelog.php?file=mod_splideghsvs

## Bugs, Fragen/questions (Deutsch or English)
- https://ghsvs.de/kontakt
- https://github.com/GHSVS-de/mod_splideghsvs/issues

## Downloads, Releases
- https://github.com/GHSVS-de/mod_splideghsvs/releases

## Template overrides (CSS)

### The parameter "CSS Stylesheet" in the plugin configuration
- Will be loaded as first CSS file.
- The original CSS files are located in `media/mod_splideghsvs/css/vendor/splidejs/splide` and in subfolder `themes/`.
- Therefore, template overrides must be located in folder `templates/YOURTEMPALTENAME/css/mod_splideghsvs/vendor/splidejs/splide` or subfolder `themes/`, respectively.

### Additional CSS file via JSON configuration
- Will be loaded as second CSS file.
- You can define an additional CSS file inside the JSON configuration file.
- Example `"css": "custom/myCssFile.css":
- - Without template override this will try to load the file `media/mod_splideghsvs/css/custom/myCssFile.css`.
- - Otherwise: `templates/YOURTEMPALTENAME/css/mod_splideghsvs/custom/myCssFile.css`.

### The parameter "Custom CSS file" in the plugin configuration
- Will be loaded as last CSS file.
- That field expects the **full**, **relative** path of the CSS file.
- The system doesn't search for overrides!
- Example: `myFolderThere/myFolderHere/mycustomsplide.css`.
- I haven't tested yet if external files via `https:` url are respected also.

-----------------------------------------------------

# My personal build procedure (WSL 1, Debian, Win 10)
- Prepare/adapt `./package.json`.
- `cd /mnt/z/git-kram/mod_splideghsvs`

## node/npm updates/installation
- `npm run g-npm-update-check` or (faster) `ncu`
- `npm run g-ncu-override-json` (if needed) or (faster) `ncu -u`
- `npm install` (if needed)

## Build installable ZIP package
- `node build.js`
- New, installable ZIP is in `./dist` afterwards.
- All packed files for this ZIP can be seen in `./package`. **But only if you disable deletion of this folder at the end of `build.js`**.s

### For Joomla update and changelog server
- Create new release with new tag.
- - See release description in `dist/release.txt`.
- Extracts(!) of the update and changelog XML for update and changelog servers are in `./dist` as well. Copy/paste and necessary additions.
