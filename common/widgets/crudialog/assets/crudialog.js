/**
 * jqCRUDialog widget javascript.
 *
 * Allows to create/update/delete model entry from JUI Dialog.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */

jQuery(function($) {

	/**
	 * Update contents of CRUD dialog.
	 *
	 * @param url mixed url of the page to display, or false when submitting form.
	 * @param id dialog object id.
	 * @param act mixed name of clicked button value [important for delete]
	 */
	jQuery.CRUDialog = function(url, id, act) {
		// Set action
		var action = '';

		// Get form contained inside update dialog
		var form = $(id + ' div.crudialog-content form');

		// Set CSRF token if CSRF validation is enabled.
		var csrfToken = '';
		if(jQuery.cookie) {
			csrfToken = '&YII_CSRF_TOKEN=' + $.cookie('YII_CSRF_TOKEN');
		}

		// When submitting form set variables to ajax call
		if(url === false)
		{
			action = '&action=' + act;
			url = form.attr('action');
		}


		// Make ajax call
		$.ajax({
			'url': url,
			'data': form.serialize() + action + csrfToken,
			'type': 'post',
			'dataType': 'json',
			'success': function(data) {
				if(data.status == 'failure')
				{
					$(id + ' div.crudialog-content').html(data.content);
					$(id + ' div.crudialog-content form input[type=submit]')
					.die() // Stop from re-binding event handlers
					.live('click', function(e) { // Send clicked button value
						e.preventDefault();
						jQuery.CRUDialog(false, id, $(this).attr('name'));
					});
				}
				else if(data.status == 'success')
				{
					$(id + ' div.crudialog-content').html(data.content);

					$(id).dialog('close').children(':eq(0)').empty();

					// Update all grid views on success
					$('div.grid-view').each(function(){
						$.fn.yiiGridView.update($(this).attr('id'));
					});
					$('.ui-jqgrid-btable').trigger('reloadGrid');
				}
			},
			'error': function() {
				alert('ajax error');
			},
			'cache': false
		});
	}

	/**
	 * Open update dialog.
	 * @param button object the originator object.
	 * @param dialogTitle string the title of the dialog.
	 */
	jQuery.CRUDialogAction = function(button, dialogTitle) {
	}

	/**
	 * Open dialog for update action.
	 * @param e Event
	 */
	jQuery.CRUDialogUpdate = function(id, url, dialogTitle) {
		id = '#' + id;

		// Clean the contents, just in case there is something left
		$(id).children(':eq(0)').empty();

		// Add content to update dialog
		jQuery.CRUDialog(url, id);

		// Open the dialog
		$(id)
			.dialog({ title: dialogTitle })
			.dialog('open');
	}

	/**
	 * Open dialog for create action.
	 * @param e Event
	 */
	jQuery.CRUDialogCreate = function(e) {
		e.preventDefault();

		// Get the dialog params
		var id = '#' + $(this).data('dialog');
		var url = $(this).data('action');
		var dialogTitle = $(this).data('title');

		// Clean the contents, just in case there is something left
		$(id).children(':eq(0)').empty();

		// Add content to update dialog
		jQuery.CRUDialog(url, id);

		// Open the dialog
		$(id)
			.dialog({ title: dialogTitle })
			.dialog('open');
	}

	/**
	 * INSTALL !
	 */
	$('.crudialog-create').bind('click', jQuery.CRUDialogCreate);
});