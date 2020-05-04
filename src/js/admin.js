/**!
 * SaleXpresso Admin Scripts
 *
 * @author SaleXpresso <support@salexpresso.com>
 * @package SaleXpresso
 * @version 1.0.0
 * @since 1.0.0
 */
import { tabs } from './components/_tabs.js';
// import $ from 'jquery';
// import { sprintf, _n } from '@wordpress/i18n';

( function( $, window, document, wp, pagenow, SaleXpresso ) {
	// const sxp_page = 0 === pagenow.indexOf( 'salexpresso_page_' ) ? pagenow.replace( 'salexpresso_page_', '' ) : false;
	$( window ).on( 'load', function() {
		const sxhWrapper = $( '.sxp-wrapper' );
		$( document ).on( 'change', '.selector', function( event ) {
			event.preventDefault();
		} );
		if ( sxhWrapper.hasClass( 'sxp-has-tabs' ) ) {
			tabs();
		}
	} );


	// date range picker
	$(function() {

		var start = moment().subtract(29, 'days');
		var end = moment();

		function cb(start, end) {
			$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
		}

		$('#reportrange').daterangepicker({
			startDate: start,
			endDate: end,
			ranges: {
				'Today': [moment(), moment()],
				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			}
		}, cb);

		cb(start, end);

	});

	// Accordion Table
	$(function(){
		$(".sxp-table tr.has-fold").on("click", function(){
			if($(this).hasClass("open")) {
				$(this).removeClass("open").next(".fold").removeClass("open");
			} else {
				$(".sxp-table tr.has-fold").removeClass("open").next(".fold").removeClass("open");
				$(this).addClass("open").next(".fold").addClass("open");
			}
		});
	});

	// responsive table
	/* Credits:
	This bit of code: Exis | exisweb.net/responsive-tables-in-wordpress
	Original idea: Dudley Storey | codepen.io/dudleystorey/pen/Geprd */

	var headertext = [];
	var headers = document.querySelectorAll("thead");
	var tablebody = document.querySelectorAll("tbody");

	for (var i = 0; i < headers.length; i++) {
		headertext[i]=[];
		for (var j = 0, headrow; headrow = headers[i].rows[0].cells[j]; j++) {
			var current = headrow;
			headertext[i].push(current.textContent);
		}
	}

	for (var h = 0, tbody; tbody = tablebody[h]; h++) {
		for (var i = 0, row; row = tbody.rows[i]; i++) {
			for (var j = 0, col; col = row.cells[j]; j++) {
				col.setAttribute("data-th", headertext[h][j]);
			}
		}
	}

}( jQuery, window, document, wp, pagenow, SaleXpresso ) );
