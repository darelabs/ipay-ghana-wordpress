(function( $ ) {

	'use strict';

	var api = 'https://community.ipaygh.com/',
		ipayGhanaPaymentModal = $( '#ipay-ghana-payment-modal' ),
		ipayGhanaPaymentForm = $( '#ipay-ghana-payment-form' ),
		ipayGhanaPay = $( '#ipay-ghana-pay' ),
		ipayGhanaDismissModal = $( '#ipay-ghana-dismiss-modal' ),
		ipayGhanaCheckStatus = $( '#ipay-ghana-check-status' ),
		ipayGhanaPaymentSummary = $( '#ipay-ghana-payment-summary' ),
		phoneNumber = $( '#phone-number' ),
		ipayGhanaInvoiceId = $( document ).find( 'input[name="invoice_id"]' ).attr( 'value' ),
		ipayGhanaMerchantKey = $( document ).find( 'input[name="merchant_key"]' ).attr( 'value' ),
		extraMobileNo = $( 'input[name="extra_mobile_no"]' );

	ipayGhanaPay.show().append( 'Check out' );
	ipayGhanaDismissModal.show().append( 'Cancel' );
	ipayGhanaCheckStatus.hide().append( 'Check Payment Status' );

	phoneNumber.on( 'blur', function() {
		extraMobileNo.val( this.value );
	});

	ipayGhanaPaymentModal.on( 'show.bs.modal', function() {
		console.log( 'ipayGhanaPaymentModal show.' );
	});

	ipayGhanaPaymentModal.on( 'hide.bs.modal', function() {
		window.location.reload( true );
	});

})( jQuery );
