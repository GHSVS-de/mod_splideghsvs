#!/usr/bin/env node

'use strict'

const fse = require('fs-extra');
const pc = require('picocolors');
const recursive = require("recursive-readdir");
const unminifyCss = require('cssunminifier').unminify;

// Unminify recursive. All *.min.css to *.css
module.exports.Css = async (folder) =>
{
	await recursive(folder).then(
		function(files) {
			const thisRegex = new RegExp('\.min\.css$');

			files.forEach((file) => {
				file = `./${file}`;

				if (thisRegex.test(file) && fse.existsSync(file)
					&& fse.lstatSync(file).isFile())
				{
					console.log(pc.magenta(pc.bold(`File to unminify: ${file}`)));
					let unminifiedFile = file.replace('.min.css', '.css');
					let code = fse.readFileSync(`${file}`).toString();
					code = unminifyCss(code);
					fse.writeFileSync(unminifiedFile, code, {encoding: "utf8"});
					console.log(pc.green(pc.bold(
						`Unminified file written: ${unminifiedFile}`))
					);
				}
			});
		},
		function(error) {
			console.error("something exploded", error);
		}
	);
}
