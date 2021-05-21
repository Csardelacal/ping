
import delegate from 'delegate';
import Dialog from 'm3w-dialog';
import _SCSS  from 'm3w-_scss/dist/_scss';

/**
 * Share button functionality. Whenever a user clicks share, the application will attempt
 * to confirm their intent and check whether they actually meant to share it.
 * 
 * @todo This should be moved to the ping component as a dropdown, so it confirms the action
 * within it's context.
 */
try {
	
	var dialog = new Dialog(document.getElementById('share-dialog'))
				
	delegate(document.body, '.for-shares', 'click', function (e) {
		document.getElementById('share-confirm-link').href = this.href;
		dialog.show();
		e.preventDefault();
	});
	
	document.getElementById('share-confirm-link').addEventListener('click', function (e) {
		document.getElementById('share-processing').style.display = 'block';
		document.getElementById('share-confirm-link').style.display = 'none';
		
		
		fetch(this.href).then(function () { 
			dialog.hide(); 
			document.getElementById('share-processing').style.display = 'none';
			document.getElementById('share-confirm-link').style.display = 'block';
		})
		e.preventDefault();
	});
}
catch (e) {
	console.error(e);
}