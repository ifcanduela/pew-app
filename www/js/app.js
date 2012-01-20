/**
 * App.js
 *
 * Use this skeleton to create a JavaScript library for your app. The App
 * object will be available to your views after including app.js.
 *
 * @author ifcanduela <ifcanduela@gmail.com>
 */
var App = (function () {

    /* Private methods and properties */
    
    var note = {},
        folder = {};
    
    var EDITOR_TEXTAREA_ID = '#source-code';
    var MARKDOWN_CONTENT_ID = '.markdown-content';
    var NOTE_TITLE_ID = '#note-title-header';
    
    var get_id_from_hash = function() {
        if (window.location.hash && window.location.hash) {
            return window.location.hash.substring(1);
        } else {
            return -1;
        }
    };
    
    var update_ui()
    {
        $(EDITOR_TEXTAREA_ID).val(note.source);
        $(MARKDOWN_CONTENT_ID).html(note.content);
        $(NOTE_TITLE_ID).text(note.title);
    }
    
    /* Public methods and properties */
    
    return {
        init: function() {
            id = get_id_from_hash
        },
        
        add: function(folder_id) {
            $.getJSON('', function() {
                
            });
        },
        
        find: function(note_id) {
            
        },
        
        save: function() {
            $.getJSON('', function() {
                
            });
        },
    };
})();