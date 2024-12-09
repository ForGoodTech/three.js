$(function() {
    // Initialize the jstree and set up the event handler for node selection
    $.getJSON('textures.json', function(data) {
        $('#fileSystemTree').jstree({
            'core': {
                'data': data
            }
        }).on("select_node.jstree", function(e, data) {
            // Reset visibility and content for all viewers
            $('#imageViewer, #textView, #downloadLink, #copyPathBtn').addClass('hidden').hide();
            $('#textView').empty();

            var node = data.node;
            var filePath = getNodeFilePath(node); // Construct the file's path

            if (node.icon == "jstree-file") {
                var fileExtension = filePath.split('.').pop().toLowerCase();

                // Updated to include webp in the image types
                if (['png', 'jpg', 'jpeg', 'gif', 'webp'].includes(fileExtension)) {
                    $('#imageViewer').attr('src', filePath).removeClass('hidden').show();
                    $('#copyPathBtn').data('path', filePath).removeClass('hidden').show(); // Prepare and show the copy path button
                } 
                else {
                    $('#downloadLink').attr('href', filePath).attr('download', node.text).text('Download File').removeClass('hidden').show();
                }
            }
        });
    });

    // Event listener for the "Copy Path" button to copy the file path
    $('#copyPathBtn').click(function() {
        var fullPath = $(this).data('path'); // Retrieve the full path stored with the button
        var pathParts = fullPath.split('/'); // Split the path into parts
        var modifiedPath = pathParts.slice(1).join('/'); // Exclude the first part (top-level directory)

        var tempInput = document.createElement('input');
        document.body.appendChild(tempInput);
        tempInput.value = modifiedPath; // Use the modified path for copying
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);

        // Change visibility style for a non-collapsing hide/show effect
        $('#copyConfirm').css('visibility', 'visible').text('Path copied to clipboard!');
        setTimeout(function() {
            $('#copyConfirm').css('visibility', 'hidden');
        }, 2000); // Hide confirmation message after a delay
    });



    // Function to recursively construct the file path from the selected node
    function getNodeFilePath(node) {
        var filePath = node.text;
        var parentId = node.parent;
        var parts = [];

        while(parentId !== "#") {
            var parentNode = $("#fileSystemTree").jstree("get_node", parentId);
            parts.unshift(parentNode.text); // Add to the beginning of the parts array
            parentId = parentNode.parent;
        }

        // Skip the first part if you want to exclude the top-level root name
        parts.push(filePath); // Add the file name as the last part
        return parts.join('/'); // Join parts into a path, excluding the top-level root if it was the first part
    }

});
