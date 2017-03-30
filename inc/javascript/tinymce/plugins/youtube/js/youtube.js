tinyMCEPopup.requireLangPack();

var YoutubeDialog = {
    init: function () {
        var f = document.forms[0];

        // Get the selected contents as text and place it in the input
        f.youtubeURL.value = tinyMCEPopup.editor.selection.getContent({ format: 'text' });
    },

    insert: function () {
        // Insert the contents from the input into the document
        var url = document.forms[0].youtubeURL.value;
        if (url === null) { tinyMCEPopup.close(); return; }

        var code, regexRes;
        regexRes = url.match("[\\?&]v=([^&#]*)");
        code = (regexRes === null) ? url : regexRes[1];
        if (code === "") { tinyMCEPopup.close(); return; }

        tinyMCEPopup.editor.execCommand('mceInsertContent', false, ''
			+'<object height="295" width="480">'
			+'<param name="allowFullScreen" value="true" />'
			+'<param name="allowscriptaccess" value="always" />'
			+'<param name="src" value="http://www.youtube.com/v/'+ code +'" />'
			+'<param name="allowfullscreen" value="true" />'
			+'<embed height="295" width="480" src="http://www.youtube.com/v/'+ code +'" allowscriptaccess="always" '
			+'allowfullscreen="true" type="application/x-shockwave-flash"></embed>'
			+'</object>');

        tinyMCEPopup.close();
    }
};

tinyMCEPopup.onInit.add(YoutubeDialog.init, YoutubeDialog);
