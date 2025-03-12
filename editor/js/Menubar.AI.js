import { UIPanel } from './libs/ui.js';

let ai_window;
function start_ai_window()
{
    const url = '../editor/ext/ai/this_ai.php'; // URL of the web app
    ai_window = window.open(
        url,
        '_blank',
        'width=1280,height=800,left=100,top=100'
    );

    if (ai_window) 
    {
        window.ai_window = ai_window;
        // Optional: Set up a function to be called when the new window is ready
        ai_window.onload = function() {
            // This code will run once the new window has fully loaded
            // Example: You can directly access the new window's DOM or JavaScript functions
        };
    } 
    else 
    {
        alert('Popup blocked. Please allow popups for this site.');
    }
}

function MenubarAI( editor ) {

	const container = new UIPanel();
	container.setClass( 'menu' );

	const title = new UIPanel();
	title.setClass( 'title' );
	title.setTextContent( 'AI' );
	title.onClick( function () 
        {
            start_ai_window();
            console.log('Start AI');
        } 
    );
	container.add( title );

	return container;

}

export { MenubarAI };
