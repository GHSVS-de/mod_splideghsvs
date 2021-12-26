const fse = require('fs-extra');
const pc = require('picocolors');
const replaceXml = require('./build/replaceXml.js');
const path = require('path');
const helper = require('./build/helper.js');

const {
	name,
	version,
} = require("./package.json");

const manifestFileName = `${name}.xml`;
const Manifest = `${__dirname}/package/${manifestFileName}`;
const source = `${__dirname}/node_modules/@splidejs/splide`;
const target = `./package/media`;
let versionSub = '';

(async function exec()
{
	let cleanOuts = [
		`./package`,
		`./dist`
	];
	await helper.cleanOut(cleanOuts);

	versionSub = await helper.findVersionSubSimple (
		path.join(source, `package.json`),
		'@splidejs/splide');

	console.log(pc.magenta(pc.bold(`versionSub identified as: "${versionSub}"`)));

	await fse.copy("./media", target
	).then(
		answer => console.log(
			pc.yellow(pc.bold(`Copied "./media" to "${target}".`))
		)
	);

	// ### Prepare /media/css. START
	let from = `${source}/dist/css`;
	let to = `${target}/css/splide`;

	await fse.copy(from, to
	).then(
		answer => console.log(
			pc.yellow(pc.bold(`Copied "${from}" into "${to}".`))
		)
	);

	await helper.unminifyCss(to);
	// ### Prepare /media/css. END

	// ### JS. START
	from = `${source}/dist/js`;
	to = `${target}/js/splide`;

	await fse.copy(from, to
	).then(
		answer => console.log(
			pc.yellow(pc.bold(`Copied "${from}" into "${to}".`))
		)
	);
	// ### JS. END

	// ### SCSS. START
	from = `${source}/src/css`;
	to = `${target}/scss/splide`;

	await fse.copy(from, to
	).then(
		answer => console.log(
			pc.yellow(pc.bold(`Copied "${from}" into "${to}".`))
		)
	);
	// ### SCSS. END

	from = `${source}/LICENSE`;
	to = `${target}/LICENSE_splide.txt`;
	await fse.copy(from, to
	).then(
		answer => console.log(
			pc.yellow(pc.bold(`Copied "${from}" into "${to}".`))
		)
	);

	from = `./src`;
	to = `./package`;
	await fse.copy(from, to
	).then(
		answer => console.log(
			pc.yellow(pc.bold(`Copied "${from}" into "${to}".`))
		)
	);

	to = path.join(target, 'mediaVersion.txt');
	await fse.writeFile(to, `v${versionSub}`, { encoding: "utf8" }
	).then(
		answer => console.log(pc.green(pc.bold(`${to} written`)))
	);

	if (!(await fse.exists("./dist")))
	{
		await fse.mkdir("./dist"
		).then(
			answer => console.log(pc.yellow(pc.bold(`Created "./dist".`)))
		);
  }

	const zipFilename = `${name}-${version}_${versionSub}.zip`;

	await replaceXml.main(Manifest, zipFilename);
	await fse.copy(`${Manifest}`, `./dist/${manifestFileName}`).then(
		answer => console.log(pc.yellow(pc.bold(
			`Copied "${manifestFileName}" to "./dist".`)))
	);

	// Create zip file and detect checksum then.
	const zipFilePath = `./dist/${zipFilename}`;

	const zip = new (require('adm-zip'))();
	zip.addLocalFolder("package", false);
	await zip.writeZip(`${zipFilePath}`);
	console.log(pc.cyan(pc.bold(pc.bgRed(
		`./dist/${zipFilename} written.`))));

	const Digest = 'sha256'; //sha384, sha512
	const checksum = await helper.getChecksum(zipFilePath, Digest)
  .then(
		hash => {
			const tag = `<${Digest}>${hash}</${Digest}>`;
			console.log(pc.green(pc.bold(`Checksum tag is: ${tag}`)));
			return tag;
		}
	)
	.catch(error => {
		console.log(error);
		console.log(pc.red(pc.bold(
			`Error while checksum creation. I won't set one!`)));
		return '';
	});

	let xmlFile = 'update.xml';
	await fse.copy(`./${xmlFile}`, `./dist/${xmlFile}`).then(
		answer => console.log(pc.yellow(pc.bold(
			`Copied "${xmlFile}" to ./dist.`)))
	);
	await replaceXml.main(`${__dirname}/dist/${xmlFile}`, zipFilename, checksum);

	xmlFile = 'changelog.xml';
	await fse.copy(`./${xmlFile}`, `./dist/${xmlFile}`).then(
		answer => console.log(pc.yellow(pc.bold(
			`Copied "${xmlFile}" to ./dist.`)))
	);
	await replaceXml.main(`${__dirname}/dist/${xmlFile}`, zipFilename, checksum);

	xmlFile = 'release.txt';
	await fse.copy(`./${xmlFile}`, `./dist/${xmlFile}`).then(
		answer => console.log(pc.yellow(pc.bold(
			`Copied "${xmlFile}" to ./dist.`)))
	);
	await replaceXml.main(`${__dirname}/dist/${xmlFile}`, zipFilename, checksum);

	cleanOuts = [
		`./package`
	];
	await helper.cleanOut(cleanOuts).then(
		answer => console.log(
			pc.cyan(pc.bold(pc.bgRed(`Finished. Good bye!`)))
		)
	);
})();
