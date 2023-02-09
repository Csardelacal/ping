
/**
 * @todo Add social interface
 */

interface Configuration {
	readonly user: object,
}

let configElement = document.querySelector('script#config');
let config: Configuration|undefined = undefined;

if (configElement === undefined || configElement === null) {
	console.warn('No configuration found');
	config = undefined;
}
else {
	config = JSON.parse(configElement.innerHTML);
}

export {config};
