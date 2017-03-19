(function( $ ) {

	'use strict';

	$( document ).ready(function() {

		var i,
			invoiceIdFormat = $( 'input[name="invoice-id-format"]' ),
			advanceInvoiceIdGeneratorBlock = $( '#advance-invoice-id-generator-block' ),
			invoiceIdPrefix = $( '#invoice-id-prefix' ),
			example = $( '#example' );

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

	});

})( jQuery );
