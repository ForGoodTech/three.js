<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Texture Viewer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.11/themes/default/style.min.css" />
    <style>
        body {
            display: flex;
            flex-direction: column;
            height: 100vh;
            margin: 0;
        }

        #main {
            display: flex;
            flex-grow: 1;
            height: calc(100vh - 60px);
        }

        #sidebar {
            width: 400px;
            overflow-y: auto;
            border-right: 1px solid #ccc;
        }

        #content {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow-y: auto;
            padding: 20px;
        }

        #imageContainer {
            text-align: center;
        }

        #imageViewer {
            max-width: 100%;
            max-height: 80vh;
            display: none;
        }

        #textView {
            max-height: 80vh;
            width: 80%;
            overflow-y: auto;
            background: #f4f4f4;
            padding: 10px;
            display: none;
        }

        #downloadLink {
            display: none;
        }

        #copyPathBtn {
            margin-top: 10px;
        }

        #copyRow {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 5px;
        }

        #copyConfirm {
            margin-left: 10px;
            color: green;
            visibility: hidden;
            min-width: 150px;
        }

        .hidden {
            display: none;
        }
        
        footer {
            background: #f0f0f0;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div id="main">
        <div id="sidebar">
            <div id="fileSystemTree"></div>
        </div>
        <div id="content">
            <div id="imageContainer">
                <img id="imageViewer" alt="Image Preview">
                <div id="copyRow">
                    <button id="copyPathBtn" class="hidden">Copy Path</button>
                    <span id="copyConfirm">Path copied to clipboard!</span>
                </div>
                <div id="textView"></div>
                <a id="downloadLink">Download File</a>
            </div>
        </div>
    </div>
    <footer>
        The texture is part of the <a href="https://github.com/mrdoob/three.js/" target="_blank">Three.js project</a> under the MIT License.
        Some textures have their own licenses. In that case, see the text files under the respective folders.
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.11/jstree.min.js"></script>
    <script src="texture_viewer.js"></script>
</body>
</html>
