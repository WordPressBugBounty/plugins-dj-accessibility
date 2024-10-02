
(function ($) {

	"use strict";

	var CMB2_Conditionals = function () {

		this.fields = $('[data-conditional]');

		//init
		this.init();
	};

	CMB2_Conditionals.prototype = {
		constructor: CMB2_Conditionals,

		init: function (refresh = false) {
			const $this = this;

			this.fields.each(function() {

				let item = $(this);

				//console.log(item);

				if(! refresh) {
					item.closest('.cmb-row').hide(); //hide on the start
				}
		
				const dataJSON = $(item).attr('data-conditional');
				const data = JSON.parse(dataJSON);
				let operator;

				if( "operator" in data ) {
					operator = data.operator;
					delete data.operator;
				} else {
					operator = '&&';
				}

				$this.checkConditions(item, data, operator);

			});

		},


		checkConditions: function (target, conditions, operator) {
			const $this = this;

			let cond = false;
			const condNumber = Object.keys(conditions).length;

			const passedCond = [];

			$.each(conditions, function(cond_field, expected_value) {

				const field = $('#' + cond_field);

				if( field.length ) {
					const cond_value = $this.getFieldValue(field);
					const negative = ( expected_value.substring(0,1) === '!' ) ? expected_value.substring(1) : false;

					if( ! negative && ( cond_value == expected_value ) ) { //when expected value
						passedCond.push(cond_field);
					} else if( negative && ( cond_value != negative ) ) { // when ! and not expected value
						passedCond.push(cond_field);
					} else if( expected_value === '!' && cond_value.length ) { //when ! and not empty
						passedCond.push(cond_field);
					}

					$this.saveRelated(field);
				}

			});

			//console.log(passedCond);

			const passed = passedCond.length;

			if( passed ) {
				if( '&&' == operator ) { // && AND
					if( passed == condNumber ) cond = true; //all conditions must match
				} else { // || OR
					cond = true; //because one of conditions matched
				}
			}

			this.displayField(target, cond);

		},

		saveRelated( field ) {
			if( ! field.attr('data-conditional-related') ) {
				field.attr('data-conditional-related', 'true');
			}
		},

		getFieldValue( field ) {

			let value = field.val();

			const checked = field.prop('checked') ? true : false;
			const type = field.attr('type');
			
			if( type == 'radio' || type == 'checkbox' ) {
				if( !checked ) {
					value = false;
				}
			}

			return value;
		},

		displayField( target, cond ) {
			if(target.length) {

				const row = target.closest('.cmb-row');
				const displayed = row.is(':visible');

				if(cond == true && !displayed) {
					row.slideDown(400);
				} else if(cond == false && displayed) {
					row.slideUp(400);
				}
			}
		}
	}

	$( document ).ready(function() {
		let cond = new CMB2_Conditionals();

		$(document).on('change','[data-conditional-related]', function() {
			cond.init(true);
		});

		//media buttons support
		$(document).on('cmb_media_modal_select', function() {
			cond.init(true);
		});
		$(document).on('click','.cmb2-upload-file', function() {
			cond.init(true);
		});
	});

})(jQuery);


