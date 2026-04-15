import { store } from '@wordpress/interactivity';

const { state } = store('eduCraftExample', {
	state: {
		isOpen: false,
		message: 'Additional details are hidden.',
	},
	actions: {
		toggleMessage() {
			console.log("hello")
			const nextState = !state.isOpen;
			state.isOpen = nextState;
			state.message = nextState
				? 'Additional details are now visible for learners.'
				: 'Additional details are hidden.';
		},
	},
});

console.log("test");