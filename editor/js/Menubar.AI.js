import { UIPanel } from './libs/ui.js';

function MenubarAI( editor ) {

	const container = new UIPanel();
	container.setClass( 'menu' );

	const title = new UIPanel();
	title.setClass( 'title' );
	title.setTextContent( 'AI' );
	title.onClick( function () 
        {
            console.log('Start AI');
        } 
    );
	container.add( title );

	return container;

}

export { MenubarAI };
