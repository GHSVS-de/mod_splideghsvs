# This is only a very rudimentarily implemented extension.
- All module layouts were programmed for individual websites. Maybe there is one that works for you without any problems. Maybe!
- Your job to fiddle around with custom overrides.

# mod_splideghsvs
- Joomla slider based on Splidejs / splide JS library (https://github.com/Splidejs/splide).
  - [Splide options](https://splidejs.com/guides/options/)
- The module layouts are based on Bootstrap 5? That has not yet been decided.

# Hint
If you use Cassiopeia and you insert modules with loadmodule plugin, e.g. in articles, you need the additional CSS rule (or similar)

```css
.com-content-article ul.splide__list
{
	overflow: unset !important;
}
```

-----------------------------------------------------

# My personal build procedure (WSL 1 or 2, Debian, Win 10)

**@since v2022.06.11_4.0.7: Build procedure uses local repo fork of https://github.com/GHSVS-de/buildKramGhsvs**

- Prepare/adapt `./package.json`.
- `cd /mnt/z/git-kram/mod_splideghsvs`

## node/npm installation/updates
- `npm install` (if not done yet)
### Update
- `npm run updateCheck` or (faster) `npm outdated`
- `npm run update` (if needed) or (faster) `npm update --save-dev`

## Build installable ZIP package
- `node build.js`
- New, installable ZIP is in `./dist` afterwards.
- All packed files for this ZIP can be seen in `./package`. **But only if you disable deletion of this folder at the end of `build.js`**.s

### For Joomla update and changelog server
- Create new release with new tag.
  - See release description in `dist/release.txt`.
- Extracts(!) of the update and changelog XML for update and changelog servers are in `./dist` as well. Copy/paste and necessary additions.
