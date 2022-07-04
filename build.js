#!/usr/bin/env node
const path = require('path');

/* Configure START */
const pathBuildKram = path.resolve("../buildKramGhsvs/build");
const updateXml = `${pathBuildKram}/update.xml`;
const changelogXml = `${pathBuildKram}/changelog.xml`;
const releaseTxt = `${pathBuildKram}/release.txt`;
/* Configure END */

const replaceXml = require(`${pathBuildKram}/replaceXml.js`);
const helper = require(`${pathBuildKram}/helper.js`);
const unminify = require(`${pathBuildKram}/unminify.js`);

const fse = require('fs-extra');
const pc = require('picocolors');

const {
	name,
	version,
} = require("./package.json");

const manifestFileName = `${name}.xml`;
const Manifest = `${__dirname}/package/${manifestFileName}`;
const source = `./node_modules/@splidejs/splide`;
const target = `./package/media`;
let versionSub = '';

let replaceXmlOptions = {};
let zipOptions = {};
let from = "";
let to = "";

(async function exec()
{
	let cleanOuts = [
		`./package`,
		`./dist`
	];
	await helper.cleanOut(cleanOuts);

	from = path.resolve(source);
	versionSub = await helper.findVersionSubSimple (
		path.join(from, `package.json`),
		'@splidejs/splide');

	console.log(pc.magenta(pc.bold(`versionSub identified as: "${versionSub}"`)));

	from = './media';
	to = target;
	await helper.copy(from, to)

	// ### Prepare /media/css. START
	from = `${source}/dist/css`;
	to = `${target}/css/splide`;
	await helper.copy(from, to)

	await unminify.Css(path.resolve(to));
	// ### Prepare /media/css. END

	// ### JS. START
	from = `${source}/dist/js`;
	to = `${target}/js/splide`;
	await helper.copy(from, to)
	// ### JS. END

	// ### SCSS. START
	from = `${source}/src/css`;
	to = `${target}/scss/splide`;
	await helper.copy(from, to)
	// ### SCSS. END

	from = `${source}/LICENSE`;
	to = `${target}/LICENSE_splide.txt`;
	await helper.copy(from, to)

	from = `./src`;
	to = `./package`;
	await helper.copy(from, to)

	to = path.join(target, 'mediaVersion.txt');
	await fse.writeFile(to, `v${versionSub}`, { encoding: "utf8" }
	).then(
		answer => console.log(pc.green(pc.bold(`${to} written`)))
	);

	await helper.mkdir('./dist');

	const zipFilename = `${name}-${version}_${versionSub}.zip`;

	replaceXmlOptions = {
		"xmlFile": Manifest,
		"zipFilename": zipFilename,
		"checksum": "",
		"dirname": __dirname
	};

	await replaceXml.main(replaceXmlOptions);
	from = Manifest;
	to = `./dist/${manifestFileName}`;
	await helper.copy(from, to)

	// Create zip file and detect checksum then.
	const zipFilePath = path.resolve(`./dist/${zipFilename}`);

	zipOptions = {
		"source": path.resolve("package"),
		"target": zipFilePath
	};
	await helper.zip(zipOptions)

	replaceXmlOptions.checksum = await helper._getChecksum(zipFilePath);

	// Bei diesen werden zuerst Vorlagen nach dist/ kopiert und dort erst "replaced".
	for (const file of [updateXml, changelogXml, releaseTxt])
	{
		from = file;
		to = `./dist/${path.win32.basename(file)}`;
		await helper.copy(from, to)

		replaceXmlOptions.xmlFile = path.resolve(to);
		await replaceXml.main(replaceXmlOptions);
	}

	cleanOuts = [
		`./package`
	];
	await helper.cleanOut(cleanOuts).then(
		answer => console.log(
			pc.cyan(pc.bold(pc.bgRed(`Finished. Good bye!`)))
		)
	);
})();
