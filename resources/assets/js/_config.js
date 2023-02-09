/**
	* @todo Add social interface
	*/
let configElement = document.querySelector('script#config');
let config = undefined;
if (configElement === undefined || configElement === null) {
	console.warn('No configuration found');
	config = undefined;
}
else {
	config = JSON.parse(configElement.innerHTML);
}
export { config };
