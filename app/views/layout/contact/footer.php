        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){

                    },
                    validateTextField: function(textfield_id)
                    {
                        //console.log(textfield_id);
                        $('[id^=message]').each(function(e) {
                            var thisid = $(this).attr('id');
                            //console.log("thisid: " + thisid);
                            $("#"+thisid).rules('remove');
                            $("#"+thisid).rules('add', {
                                required: true,
                                messages:{
                                    required: "Please type a message"
                                }
                            });
                        });
                        $(textfield_id).valid();
                    },
                    createCKEditors: function(){
                        //.ckeditorInstance.destroy()
                        var allTextAreas = document.querySelectorAll('textarea.ckeditor');
                        var currentCKEditors = document.querySelectorAll('.ck-editor__editable');
                        for( var j = 0; j < currentCKEditors.length; ++j)
                        {
                            currentCKEditors[j].ckeditorInstance.destroy();
                        }
                        for (var i = 0; i < allTextAreas.length; ++i)
                        {
                            var this_id = allTextAreas[i].id;
                            console.log("this_id: "+this_id);
                            ClassicEditor
                                .create( allTextAreas[i] , {
                                    toolbar: {
                                        items: [
                                            'bold',
                                            'italic',
                                            'strikethrough',
                                            'subscript',
                                            'superscript',
                                            'underline',
                                            'outdent',
                                            'indent',
                                            'alignment',
                                            '|',
                                            'undo',
                                            'redo'
                                        ]
                                    },
                                    language: 'en',
                                    image: {
                                        toolbar: [
                                            'imageTextAlternative',
                                            'imageStyle:full',
                                            'imageStyle:side'
                                        ]
                                    },
                                    table: {
                                        contentToolbar: [
                                            'tableColumn',
                                            'tableRow',
                                            'mergeTableCells'
                                        ]
                                    }
                                } )
                                .then( editor => {
                                    if(window.editor)
                                    {
                                        window.editor[this_id] = editor;
                                    }
                                    else
                                    {
                                        window.editor = {};
                                        window.editor[this_id] = editor;
                                    }
                                    editor.model.document.on( 'change', () => {
                                        //console.log( 'The Document has changed!' );
                                        editor.updateSourceElement();
                                        actions.common.validateTextField(editor.sourceElement);
                                    } );
                                } )
                                .catch( error => {
                                    console.error( 'Oops, something went wrong!' );
                                    console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
                                    console.warn( 'Build id: x86d9y47fxh6-q4s9v3hwa0g6' );
                                    console.error( error );
                                } );
                        }
                    }
                },
                'contact-us': {
                    init: function(){
                        actions.common.createCKEditors();
                    }
                }
            }

            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>