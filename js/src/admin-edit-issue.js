/* global $oneApp */
( function( $, window ) {

	/**
	 * Selector cache of the container holding all of the items for an issue.
	 *
	 * @type {*|HTMLElement}
	 */
	var $issue_posts = $( "#wsuwp-issue-posts-stage" );

	if ( window.wsuwp_issue.items instanceof Array ) {
		load_issue_posts( window.wsuwp_issue.items );
	}

	sortable_layout();

	/**
	 * Use jQuery UI Sortable to add sorting functionality to issue posts.
	 */
	function sortable_layout() {
		var post_parent;

		$( ".wsuwp-spine-builder-column" ).sortable( {
			connectWith: ".wsuwp-spine-builder-column",
			handle: ".ttfmake-sortable-handle",
			opacity: 0.6,
			placeholder: "wswp-issue-post-placeholder",
			start: function( event, ui ) {
				post_parent = $( ui.item ).parent();
			},
			stop: function( event, ui ) {
				var existing_post = ui.item.siblings( ".issue-post" ),
					builder_stage = ui.item.closest( "#ttfmake-stage" ),
					staging_area = ui.item.closest( "#wsuwp-issue-posts-stage" );

				if ( existing_post && builder_stage.length ) {
					$( existing_post ).appendTo( post_parent );
				}

				if ( staging_area.length ) {
					ui.item.find( ".handlediv" ).removeClass( "wsuwp-toggle-closed" );
					ui.item.find( ".wsuwp-issue-post-body" ).css( "display", "" );
				}

				process_sorted_data();
			}
		} );
	}

	/**
	 * Process an existing list of issue items and add them to the front end view of the issue build.
	 *
	 * @param raw_data
	 */
	function load_issue_posts( raw_data ) {
		var data = "";

		// Append the results to the existing build of items.
		$.each( raw_data, function( index, val ) {
			var featured_image = ( val.featured_image ) ? "<img src='" + val.featured_image + "'/>" : "",
				thumbnail_image = ( val.thumbnail_image ) ? "<img src='" + val.thumbnail_image + "'/>" : "";

			data += "<div id='issue-post-" + val.id + "' class='issue-post' " +
				"data-classes " +
				"data-header='" + val.title + "' " +
				"data-subheader " +
				"data-display-image " +
				"data-display-excerpt " +
				"data-background-image " +
				"data-background-position>" +
				"<div class='ttfmake-sortable-handle' title='Drag-and-drop this post into place'>" +
					"<a href='#' class='spine-builder-column-configure'><span>Configure this column</span></a>" +
					"<a href='#' class='wsuwp-column-toggle' title='Click to toggle'><div class='handlediv'></div></a>" +
					"<div class='wsuwp-builder-column-title'>" + val.title + "</div>" +
				"</div>" +
				"<article class='wsuwp-issue-post-body wsuwp-column-content'>" +
					"<header>" +
						"<h2>" + val.title + "</h2>" +
						"<p class='subheader'></p>" +
					"</header>" +
					"<figure class='featured-image'>" +
						featured_image +
					"</figure>" +
					"<figure class='thumbnail-image'>" +
						thumbnail_image +
					"</figure>" +
					"<div class='excerpt'>" +
						val.excerpt +
					"</div>" +
				"</article>" +
			"</div>";
		} );

		$issue_posts.html( data );

		sortable_layout();
	}

	/**
	 * As issue posts are sorted, process their associated information.
	 */
	function process_sorted_data() {
		var new_val = "";

		// Posts added to the Page Builder interface.
		var placed_posts = $( "#ttfmake-stage" ).find( ".wsuwp-spine-builder-column" );

		$.each( placed_posts, function() {
			var column  = $( this ),
				post = column.children( ".issue-post" );

			if ( post.length ) {
				var new_val = post[ 0 ].id.replace( "issue-post-", "" ),
					display_excerpt = ( "yes" === post.data( "display-excerpt" ) ) ? true : false,
					bg_img = post.data( "background-image" );

				// Always set Post ID and Headline values.
				column.children( ".wsuwp-column-post-id" ).val( new_val );
				column.find( ".spine-builder-column-classes" ).val( post.data( "classes" ) );
				column.find( ".spine-builder-column-header" ).val( post.data( "header" ) );
				column.find( ".spine-builder-column-subheader" ).val( post.data( "subheader" ) );
				column.find( ".spine-builder-column-display-image" ).val( post.data( "display-image" ) );
				column.find( ".spine-builder-column-display-excerpt" ).prop( "checked", display_excerpt );

				// Set the background value and update the HTML if needed.
				column.find( ".spine-builder-column-background-image" ).val( bg_img );

				if ( bg_img.length ) {
					column.find( ".spine-builder-column-set-background-image" ).html( "<img src='" + bg_img + "' />" ).
						next( ".spine-builder-column-remove-background-image" ).show();
				} else {
					column.find( ".spine-builder-column-set-background-image" ).html( "Set background image" ).
						next( ".spine-builder-column-remove-background-image" ).hide();
				}

				column.find( ".spine-builder-column-background-position" ).val( post.data( "background-position" ) );
			} else {
				column.find( ".wsuwp-issue-post-meta" ).val( "" );
				column.find( ".spine-builder-column-set-background-image" ).html( "Set background image" ).
					next( ".spine-builder-column-remove-background-image" ).hide();
			}
		} );

		// Posts in the staging area.
		var staged_posts = $issue_posts.sortable( "toArray" );

		$.each( staged_posts, function( index, val ) {
			new_val = val.replace( "issue-post-", "" );
			staged_posts[ index ] = new_val;
		} );

		$( "#issue-staged-posts" ).val( staged_posts );

	}

	// Load posts published between the selected dates into the staging area.
	$( "#load-issue-posts" ).on( "click", function( e ) {
		e.preventDefault();

		var start_date = $( "#post-start-date" ).val(),
			end_date = $( "#post-end-date" ).val();

		if ( "" === start_date || "" === end_date ) {
			window.alert( "Please select both a start and end date." );
			return false;
		}

		var data = {
			action: "set_issue_posts",
			nonce: window.wsuwp_issue.nonce,
			start_date: start_date,
			end_date: end_date
		};

		// Make the ajax call
		$.post( window.ajaxurl, data, function( response ) {
			var response_data = $.parseJSON( response );
			load_issue_posts( response_data );
			process_sorted_data();
		} );
	} );

	// Make sure newly-added Page Builder elements are made sortable.
	$( ".ttfmake-menu-list" ).on( "click", ".ttfmake-menu-list-item", function() {
		$oneApp.on( "afterSectionViewAdded", function() {
			sortable_layout();
		} );
	} );

	// Apply user-added information to its respective post.
	$( "#ttfmake-stage" ).on( "change", ".wsuwp-issue-post-meta", function() {
		var input = $( this ),
			post = input.closest( ".wsuwp-spine-builder-column" ).find( ".issue-post" ),
			value = input.val();

		if ( input.hasClass( "spine-builder-column-header" ) ) {
			post.data( "header", value ).
				find( "h2" ).html( value );
		}

		if ( input.hasClass( "spine-builder-column-subheader" ) ) {
			post.data( "subheader", value ).
				find( ".subheader" ).html( value );
		}

		// Set the `data-display-image` attribute in a couple ways for display purposes.
		if ( input.hasClass( "spine-builder-column-display-image" ) ) {
			post.data( "display-image", value ).attr( "data-display-image", value );
		}

		if ( input.hasClass( "spine-builder-column-display-excerpt" ) ) {
			if ( input.is( ":checked" ) ) {
				post.data( "display-excerpt", "yes" ).find( ".excerpt" ).show();
			} else {
				post.data( "display-excerpt", "" ).find( ".excerpt" ).hide();
			}
		}

		if ( input.hasClass( "spine-builder-column-background-position" ) ) {
			post.data( "background-position", value ).
				find( ".wsuwp-issue-post-body" ).
				css( "background-position", value.replace( /-/g, " " ) );
		}
	} );

}( jQuery, window ) );

/**
 * Handle Background Image media modal.
 */
( function( $ ) {

	"use strict";

	var media_modal;

	$( "#ttfmake-stage" ).on( "click", ".spine-builder-column-set-background-image", function( e ) {

		e.preventDefault();

		var set_image_link = $( this );

		media_modal = window.wp.media( {
			title: "Choose Image",
			button: {
				text: "Choose Image"
			},
			multiple: false
		} );

		media_modal.on( "select", function() {
			var attachment = media_modal.state().get( "selection" ).first().toJSON(),
				attachment_url = ( attachment.sizes.hasOwnProperty( "spine-large_size" ) ) ? attachment.sizes[ "spine-large_size" ].url : attachment.url;

			set_image_link.html( "<img src='" + attachment_url + "' />" ).
				prev( ".spine-builder-column-background-image" ).val( attachment_url ).trigger( "change" ).
				siblings( ".spine-builder-column-remove-background-image" ).show().
				closest( ".wsuwp-spine-builder-column" ).
					find( ".issue-post" ).
						data( "background-image", attachment_url ).
					find( ".wsuwp-issue-post-body" ).
						css( "background-image", "url( " + attachment_url + " )" );
		} );

		media_modal.open();
	} );

	$( "#ttfmake-stage" ).on( "click", ".spine-builder-column-remove-background-image", function( e ) {
		e.preventDefault();

		var column = $( this ).closest( ".wsuwp-spine-builder-column" );

		$( this ).hide()
			.prev( ".spine-builder-column-set-background-image" ).html( "Set background image" )
			.prev( ".spine-builder-column-background-id" ).val( "" );

		column.find( ".spine-builder-column-background-position" ).val( "" );
		column.find( ".issue-post" ).
			data( "background-id", "" ).
			data( "background-position", "" ).
			data( "background-image", "" ).
			find( ".wsuwp-issue-post-body" ).css( "background-image", "" );
	} );

}( jQuery ) );
