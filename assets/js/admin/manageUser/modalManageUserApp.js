'use strict';
const jQuery=require('jquery');
(function(window, $) {
    window.RepLogApp = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'submit',
			'.edit_user_form',
            this.handleNewFormSubmit.bind(this)
        );

        this.$wrapper.on(
        	'submit',
			'.edit_role_form',
			this.handleNewRoleSubmit.bind(this)
		);
    };

    $.extend(window.RepLogApp.prototype, {
       

        handleNewFormSubmit: function(e) {
            e.preventDefault();
			var $form= $(e.currentTarget);

			$.ajax({
				url: $form.attr('action'),
				method: 'POST',
				data: $form.serialize(),
				success: function(data){
					//$tbody.append(data);
					$('.user-updatedAt-'+data.id).text(data.updatedAt);

					if(data.name !== undefined){
						$('.user-name-'+data.id).text(data.name);
					}
					if(data.firstname !== undefined){
						$('.user-firstname-'+data.id).text(data.firstname);
					}
					if(data.birthday !== undefined){
						$('.user-birthday-'+data.id).text(data.birthday);
					}
					if(data.address !== undefined || data.city !== undefined || data.zip !== undefined){
						let address = '';
						if(data.address !==undefined){
							address = address.concat(data.address);
						}
						if(data.zip !== undefined){
							address = address.concat(data.zip);
						}
						if(data.city !== undefined){
							address = address.concat(data.city);
						}
						$('.user-address-'+data.id).text(data.address);
					}
					if(data.member-seniority !== undefined){
						$('.user-member-seniority-'+data.id).text(data.member-seniority);
					}

					$(".check-seniority-modal").fadeOut('fast');
					$('body').removeClass('modal-open');
					$('.modal-backdrop').remove();
				},
				error: function(jqXHR){
					alert('edit failed : '+jqXHR);
					$form.closest('.edit_user_form').html(jqXHR.responseText);
				}
			});
        }
    });
	$.extend(window.RepLogApp.prototype, {


		handleNewRoleSubmit: function(e) {
			e.preventDefault();
			var $form= $(e.currentTarget);

			$.ajax({
				url: $form.attr('action'),
				method: 'POST',
				data: $form.serialize(),
				success: function(data){
					//$tbody.append(data);
					$('.user-updatedAt-'+data.id).text(data.updatedAt);

					if(data.roles !== undefined){
						$('.user-roles-'+data.id).text(data.roles);
					}

					$(".edit-role-modal").fadeOut('fast');
					$('body').removeClass('modal-open');
					$('.modal-backdrop').remove();
				},
				error: function(jqXHR){
					alert('edit failed : '+jqXHR);
					$form.closest('.edit_user_form').html(jqXHR.responseText);
				}
			});
		}
	});
   

})(window, jQuery);
