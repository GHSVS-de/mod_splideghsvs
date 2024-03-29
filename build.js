#!/usr/bin/env node
const path = require('path');

/* Configure START */
const pathBuildKram = path.resolve("../buildKramGhsvs");
const updateXml = `${pathBuildKram}/build/update_no-changelog.xml`;
// const changelogXml = `${pathBuildKram}/build/changelog.xml`;
const releaseTxt = `${pathBuildKram}/build/release_no-changelog.txt`;
/* Configure END */

const replaceXml = require(`${pathBuildKram}/build/replaceXml.js`);
const helper = require(`${pathBuildKram}/build/helper.js`);
const unminify = require(`${pathBuildKram}/build/unminify.js`);

const pc = require(`${pathBuildKram}/node_modules/picocolors`);
const fse = require(`${pathBuildKram}/node_modules/fs-extra`);

const {
	name,
	version,
} = require("./package.json");

const manifestFileName = `${name}.xml`;
const Manifest = `${__dirname}/package/${manifestFileName}`;
const source = `./node_modules/@splidejs/splide`;
const target = `./package/media`;
let versionSub = '';

let replaceXmlOptions = {
	"xmlFile": '',
	"zipFilename": '',
	"checksum": '',
	"dirname": __dirname,
	"jsonString": '',
	"versionSub": ''
};
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

	await helper.mkdir('./dist');

	from = path.resolve(source);
	versionSub = await helper.findVersionSubSimple (
		path.join(from, `package.json`),
		'@splidejs/splide');

	console.log(pc.magenta(pc.bold(`versionSub identified as: "${versionSub}"`)));
	replaceXmlOptions.versionSub = versionSub;

	// Das sind zumeist leere Ordner.
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

	// ### SCSS. START. Auch, wenn da src/css steht, sind da nur scss-Dateien drin.
	from = `${source}/src/css`;
	to = `${target}/scss/splide`;
	await helper.copy(from, to)
	// ### SCSS. END

	from = `${source}/LICENSE`;
	to = `${target}/LICENSE_splide.txt`;
	await helper.copy(from, to)

	await helper.gzip([`${target}/js`, `${target}/css`]);

	from = `./src`;
	to = `./package`;
	await helper.copy(from, to)

	// ### CREATE A joomla.asset.json - START
	// Add asset entries for all css files.
	// Makes loading via WAM easier.
	to = 'joomla.asset.json';
	console.log(pc.green(pc.bold(`Start build of ${to}.`)));

	// Is already existing package/media/joomla.asset.json.
	to = path.resolve(`${target}/${to}`);

	// load it.
	jsonObj = require(to);

	from = path.resolve(`${target}/css/splide`);

	// Collect css files. We don't need min.css in joomla.asset.json because WAM loads what it wants.
	const regex = '\.css$';
	const exclude = '\.min\.css$';
	const collector = await helper.getFilesRecursive(
		from,
		regex,
		from + '/',
		exclude
	);

	for (const file of collector)
	{
		// Strip extension. Then replace / with .
		let assetName = file.substring(0, file.lastIndexOf('.')) || file;
		assetName = `{{name}}.${assetName.replace(new RegExp(/\//, 'g'), '.')}`;

		const registryItem = {
			name: assetName,
			version: '{{versionSub}}',
			type: "style",
			uri: path.join('{{name}}', 'splide', file)
		};

		await jsonObj.assets.push(registryItem);
	}

	await fse.writeFile(
		to,
		JSON.stringify(jsonObj, null, '\t'),
		{encoding:"utf8"}
	).then(
		answer => console.log(pc.green(pc.bold(`${to} written`)))
	);

	replaceXmlOptions.xmlFile = to;
	await replaceXml.main(replaceXmlOptions);

	await helper.copy(to, `./dist/joomla.asset.json`)
	// ### CREATE A joomla.asset.json - END

	const zipFilename = `${name}-${version}_${versionSub}.zip`;

	replaceXmlOptions = Object.assign(
		replaceXmlOptions,
		{
			"xmlFile": Manifest,
			"zipFilename": zipFilename
		}
	);
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
	for (const file of [updateXml, releaseTxt])
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
