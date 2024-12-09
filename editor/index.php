<!DOCTYPE html>
<html lang="en">
	<head>
		<title>three.js editor</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<link rel="apple-touch-icon" href="images/icon.png">
		<link rel="manifest" href="manifest.json">
		<link rel="shortcut icon" href="../files/favicon_white.ico" media="(prefers-color-scheme: dark)"/>
		<link rel="shortcut icon" href="../files/favicon.ico" media="(prefers-color-scheme: light)" />
	</head>
	<body>
		<link rel="stylesheet" href="css/main.css">

		<script type="importmap">
			{
				"imports": {
					"three": "../build/three.module.js",
					"three/addons/": "../examples/jsm/",

					"three/examples/": "../examples/",
					"three-gpu-pathtracer": "https://cdn.jsdelivr.net/npm/three-gpu-pathtracer@0.0.23/build/index.module.js",
					"three-mesh-bvh": "https://cdn.jsdelivr.net/npm/three-mesh-bvh@0.7.4/build/index.module.js"
				}
			}
		</script>

		<script src="../examples/jsm/libs/draco/draco_encoder.js"></script>

		<link rel="stylesheet" href="js/libs/codemirror/codemirror.css">
		<link rel="stylesheet" href="js/libs/codemirror/theme/monokai.css">
		<script src="js/libs/codemirror/codemirror.js"></script>
		<script src="js/libs/codemirror/mode/javascript.js"></script>
		<script src="js/libs/codemirror/mode/glsl.js"></script>

		<script src="js/libs/esprima.js"></script>
		<script src="js/libs/jsonlint.js"></script>

		<script src="https://cdn.jsdelivr.net/npm/@ffmpeg/ffmpeg@0.11.6/dist/ffmpeg.min.js"></script>

		<link rel="stylesheet" href="js/libs/codemirror/addon/dialog.css">
		<link rel="stylesheet" href="js/libs/codemirror/addon/show-hint.css">
		<link rel="stylesheet" href="js/libs/codemirror/addon/tern.css">

		<script src="js/libs/codemirror/addon/dialog.js"></script>
		<script src="js/libs/codemirror/addon/show-hint.js"></script>
		<script src="js/libs/codemirror/addon/tern.js"></script>
		<script src="js/libs/acorn/acorn.js"></script>
		<script src="js/libs/acorn/acorn_loose.js"></script>
		<script src="js/libs/acorn/walk.js"></script>
		<script src="js/libs/ternjs/polyfill.js"></script>
		<script src="js/libs/ternjs/signal.js"></script>
		<script src="js/libs/ternjs/tern.js"></script>
		<script src="js/libs/ternjs/def.js"></script>
		<script src="js/libs/ternjs/comment.js"></script>
		<script src="js/libs/ternjs/infer.js"></script>
		<script src="js/libs/ternjs/doc_comment.js"></script>
		<script src="js/libs/tern-threejs/threejs.js"></script>
		<script src="js/libs/signals.min.js"></script>

		<script type="module">
            import './ext/ws.js';
            import './ext/ai_interface.js';

			import * as THREE from 'three';

			import { Editor } from './js/Editor.js';
			import { Viewport } from './js/Viewport.js';
			import { Toolbar } from './js/Toolbar.js';
			import { Script } from './js/Script.js';
			import { Player } from './js/Player.js';
			import { Sidebar } from './js/Sidebar.js';
			import { Menubar } from './js/Menubar.js';
			import { Resizer } from './js/Resizer.js';

			window.URL = window.URL || window.webkitURL;
			window.BlobBuilder = window.BlobBuilder || window.WebKitBlobBuilder || window.MozBlobBuilder;

			//

			const editor = new Editor();

			window.editor = editor; // Expose editor to Console
			window.THREE = THREE; // Expose THREE to APP Scripts and Console

			const viewport = new Viewport( editor );
			document.body.appendChild( viewport.dom );

			const toolbar = new Toolbar( editor );
			document.body.appendChild( toolbar.dom );

			const script = new Script( editor );
			document.body.appendChild( script.dom );

			const player = new Player( editor );
			document.body.appendChild( player.dom );

			const sidebar = new Sidebar( editor );
			document.body.appendChild( sidebar.dom );

			const menubar = new Menubar( editor );
			document.body.appendChild( menubar.dom );

			const resizer = new Resizer( editor );
			document.body.appendChild( resizer.dom );

			//

			editor.storage.init( function () {

				editor.storage.get( async function ( state ) {

					if ( isLoadingFromHash ) return;

					if ( state !== undefined ) {

						await editor.fromJSON( state );

					}

					const selected = editor.config.getKey( 'selected' );

					if ( selected !== undefined ) {

						editor.selectByUuid( selected );

					}

				} );

				//

				let timeout;

				function saveState() {

					if ( editor.config.getKey( 'autosave' ) === false ) {

						return;

					}

					clearTimeout( timeout );

					timeout = setTimeout( function () {

						editor.signals.savingStarted.dispatch();

						timeout = setTimeout( function () {

							editor.storage.set( editor.toJSON() );

							editor.signals.savingFinished.dispatch();

						}, 100 );

					}, 1000 );

				}

				const signals = editor.signals;

				signals.geometryChanged.add( saveState );
				signals.objectAdded.add( saveState );
				signals.objectChanged.add( saveState );
				signals.objectRemoved.add( saveState );
				signals.materialChanged.add( saveState );
				signals.sceneBackgroundChanged.add( saveState );
				signals.sceneEnvironmentChanged.add( saveState );
				signals.sceneFogChanged.add( saveState );
				signals.sceneGraphChanged.add( saveState );
				signals.scriptChanged.add( saveState );
				signals.historyChanged.add( saveState );

			} );

			//

			document.addEventListener( 'dragover', function ( event ) {

				event.preventDefault();
				event.dataTransfer.dropEffect = 'copy';

			} );

			document.addEventListener( 'drop', function ( event ) {

				event.preventDefault();

				if ( event.dataTransfer.types[ 0 ] === 'text/plain' ) return; // Outliner drop

				if ( event.dataTransfer.items ) {

					// DataTransferItemList supports folders

					editor.loader.loadItemList( event.dataTransfer.items );

				} else {

					editor.loader.loadFiles( event.dataTransfer.files );

				}

			} );

			function onWindowResize() {

				editor.signals.windowResize.dispatch();

			}

			window.addEventListener( 'resize', onWindowResize );

			onWindowResize();

			//

			let isLoadingFromHash = false;
			const hash = window.location.hash;

			if ( hash.slice( 1, 6 ) === 'file=' ) {

				const file = hash.slice( 6 );

				if ( confirm( editor.strings.getKey( 'prompt/file/open' ) ) ) {

					const loader = new THREE.FileLoader();
					loader.crossOrigin = '';
					loader.load( file, function ( text ) {

						editor.clear();
						editor.fromJSON( JSON.parse( text ) );

					} );

					isLoadingFromHash = true;

				}

			}

			// ServiceWorker. Not needed.
		</script>
	</body>
</html>
