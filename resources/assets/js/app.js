
import delegate from 'delegate';
import Dialog from 'm3w-dialog';
//import _SCSS  from 'm3w-_scss/dist/_scss';

import { createApp } from "vue";
import { createStore } from "vuex";
import { config } from "../js/_config"

//Common components
import ContextualMenuComponent from '../components/ui/contextual/menu.vue'
import ContextualItemComponent from '../components/ui/contextual/item.vue'

//Navbar components
import UserMenu from '../components/ui/navigation/user-menu.vue';

const store = createStore({
	modules: {
	},
	state : {
		config
	}
});

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

/**
 * In order to operate the navigation bar consistently across
 * different pages, this code just establishes it as a second
 * application.
 *
 * @todo Consolidate into the main application
 */
var navbar = createApp({
	/**
	 * The spinner we use is a module
	 */
	compilerOptions: {
		isCustomElement: (e) =>
			e === 'spinner-material'
	},
	components: {
		'context-menu': ContextualMenuComponent,
		'context-item': ContextualItemComponent,
		UserMenu
	},
	data: function () {
		return {
			hidden: false,
			scroll: 0
		};
	},
	methods: {
		listener(e) {
			const prev = this.scroll;
			const curr = window.scrollY || window.scrollTop || document.getElementsByTagName("html")[0].scrollTop
			
			if (curr < 50 || prev > curr + 50) {
				this.hidden = false;
				this.scroll = curr;
			}
			else if (curr > prev + 50) {
				this.hidden = true;
				this.scroll = curr;
			}
		},
	},
	mounted() {
		window.addEventListener('scroll', this.listener);
	},
	unmounted() {
		window.removeEventListener('scroll', this.listener);
	}
});

navbar.use(store);
navbar.mount('nav');
console.log(navbar);
