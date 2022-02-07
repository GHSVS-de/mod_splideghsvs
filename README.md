# This is only a very rudimentarily implemented extension.
- Only 1 slider implemented for 1 single individual site.

# mod_splideghsvs
- Joomla slider based on Splidejs / splide JS library (https://github.com/Splidejs/splide).
- The module layouts are based on Bootstrap 5? That has not yet been decided.

-----------------------------------------------------

# My personal build procedure (WSL 1, Debian, Win 10)
- Prepare/adapt `./package.json`.
- `cd /mnt/z/git-kram/mod_splideghsvs`

## node/npm installation/updates
- `npm install` (if not done yet)
### Update
- `npm run g-npm-update-check` or (faster) `npm outdated`
- `npm run g-npm-update` (if needed) or (faster) `npm update --save-dev`

## Build installable ZIP package
- `node build.js`
- New, installable ZIP is in `./dist` afterwards.
- All packed files for this ZIP can be seen in `./package`. **But only if you disable deletion of this folder at the end of `build.js`**.s

### For Joomla update and changelog server
- Create new release with new tag.
- - See release description in `dist/release.txt`.
- Extracts(!) of the update and changelog XML for update and changelog servers are in `./dist` as well. Copy/paste and necessary additions.
