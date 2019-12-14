(function( $ ) {

	'use strict';

	$( document ).ready(function() {

		var i,
			invoiceIdFormat = $( 'input[name="invoice-id-format"]' ),
			advanceInvoiceIdGeneratorBlock = $( '#advance-invoice-id-generator-block' ),
			invoiceIdPrefix = $( '#invoice-id-prefix' ),
			example = $( '#example' ),
            logoHolder = $('#logoHolder');

		advanceInvoiceIdGeneratorBlock.hide();

		for ( i in invoiceIdFormat ) {
			if ( invoiceIdFormat.hasOwnProperty( i ) ) {
				if ( invoiceIdFormat[i].checked && ( invoiceIdFormat[i].value === 'advance' ) ) {
					console.log( invoiceIdFormat[i] );
					advanceInvoiceIdGeneratorBlock.slideDown( 500 );
				}
			}
		}

		invoiceIdFormat.on( 'change', function() {
			if ( this.checked && ( this.value === 'advance' ) ) {
				advanceInvoiceIdGeneratorBlock.slideDown( 500 );
			} else {
				advanceInvoiceIdGeneratorBlock.slideUp( 500 );
			}
		});

		invoiceIdPrefix.on( 'focus', function() {
			for ( i in invoiceIdFormat ) {
				if ( invoiceIdFormat.hasOwnProperty(i) ) {
					if ( ! invoiceIdFormat[i].checked && ( invoiceIdFormat[i].value === 'custom' ) ) {
						invoiceIdFormat[i].click();
					}
				}
			}
			advanceInvoiceIdGeneratorBlock.slideUp( 500 );
		});

		invoiceIdPrefix.on( 'blur', function() {
			console.log( invoiceIdPrefix.val() );
			if ( invoiceIdPrefix.val() !== '' ) {
				example.html( '' ).append( this.value );
			} else {
				invoiceIdPrefix.val( 'WP-' );
			}
		});


        //upload logo script start
        logoHolder.on('click', function () {
            // Accepts an optional object hash to override default values.
            const frame = new wp.media.view.MediaFrame.Select({
                // Modal title
                title: 'Select Logo',
                // Enable/disable multiple select
                multiple: false,
                // Library WordPress query arguments.
                library: {
                    order: 'ASC',
                    // [ 'name', 'author', 'date', 'title', 'modified', 'uploadedTo',
                    // 'id', 'post__in', 'menuOrder' ]
                    orderby: 'title',
                    // mime type. e.g. 'image', 'image/jpeg'
                    type: 'image',
                    // Searches the attachment title.
                    search: null,
                    // Attached to a specific post (ID).
                    uploadedTo: null
                },
                button: {
                    text: 'SELECT'
                }
            });
            // Fires when a user has selected attachment(s) and clicked the select button.
            // @see media.view.MediaFrame.Post.mainInsertToolbar()
            frame.on('select', function () {
                var selectionCollection = frame.state().get('selection').first().toJSON();

                //update the image and the link with new logo link
                logoHolder.attr('src', selectionCollection.url);
                $('#brand-logo-url').val(selectionCollection.url);

            });
            // Get an object representing the current state.
            frame.state();

            // Get an object representing the previous state.
            frame.lastState();

            // Open the modal.
            frame.open();
        });

	});



})( jQuery );
